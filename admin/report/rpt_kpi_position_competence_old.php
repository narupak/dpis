<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "K07";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
    $pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "20";
	$heading_width[1] = "85";
	$heading_width[2] = "80";
	$heading_width[3] = "80";
	
	//new format**************************************************
    $heading_text[0] = "เลขที่ตำแหน่ง|";
	$heading_text[1] = "ตำแหน่ง|";
	$heading_text[2] = "ช่วงระดับตำแหน่ง|";
	$heading_text[3] = "ผู้ครองตำแหน่ง|";
	
	$heading_align = array('C','C','C','C');

  if($search_org_id1){
		
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(b.ORG_ID=$search_org_id1)";
	else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(e.ORG_ID=$search_org_id1)";
	}elseif($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if
	if(trim($search_per_name)) $arr_search_condition[] = "(e.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $arr_search_condition[] = "(e.PER_SURNAME like '$search_per_surname%')";
  	if(trim($search_pos_no)) $arr_search_condition[] = "(b.POS_NO = '$search_pos_no')";
  	if(trim($search_pl_code)) $arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
	if(trim($search_level_no)) $arr_search_condition[] = "(b.LEVEL_NO = '$search_level_no')";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$cmd = " select		distinct 
										a.POS_ID, POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)) as POS_NO, c.PL_NAME, b.PT_CODE, d.PT_NAME, b.CL_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
						 from		(
											(
												(
													(
														PER_POSITION_COMPETENCE a
														inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) inner join PER_LINE c on (b.PL_CODE=c.PL_CODE)
												) inner join PER_TYPE d on (b.PT_CODE=d.PT_CODE)
											) left join PER_PERSONAL e on (b.POS_ID=e.POS_ID)
										) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
						 where	(e.PER_STATUS=1 or e.PER_STATUS IS NULL)
									 	$search_condition
									 	
						 order by POS_NO_NAME, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO)) ";
	}elseif($DPISDB=="oci8"){			   
		$rec_start = (($current_page-1) * $data_per_page) + 1;
		$rec_end = ($current_page > 1)? ($current_page * $data_per_page) : $data_per_page;

		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		distinct a.POS_ID, POS_NO_NAME, b.POS_NO, c.PL_NAME, b.PT_CODE, d.PT_NAME, b.CL_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
								 from			PER_POSITION_COMPETENCE a, PER_POSITION b, PER_LINE c, PER_TYPE d, PER_PERSONAL e, PER_PRENAME f
								 where		a.POS_ID=b.POS_ID and b.PL_CODE=c.PL_CODE and b.PT_CODE=d.PT_CODE and b.POS_ID=e.POS_ID(+) and e.PN_CODE=f.PN_CODE(+) and (e.PER_STATUS=1 or e.PER_STATUS IS NULL)
													$search_condition 
								  order by 	POS_NO_NAME, to_number(b.POS_NO) ";					   
	}elseif($DPISDB=="mysql"){
		$cmd = " select		distinct a.POS_ID, POS_NO_NAME, b.POS_NO as POS_NO, c.PL_NAME, b.PT_CODE, d.PT_NAME, b.CL_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
						 from		PER_POSITION_COMPETENCE as a
														inner join PER_POSITION as b on (a.POS_ID=b.POS_ID)
													 inner join PER_LINE as c on (b.PL_CODE=c.PL_CODE)
												inner join PER_TYPE as d on (b.PT_CODE=d.PT_CODE)
											left join PER_PERSONAL as e on (b.POS_ID=e.POS_ID)
										left join PER_PRENAME as f on (e.PN_CODE=f.PN_CODE)
						 where	e.PER_STATUS=1 or Isnull(e.PER_STATUS)
									 	$search_condition
						 order by POS_NO_NAME, b.POS_NO+0 ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	    if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
			$pdf->AutoPageBreak = false; 
		
             foreach($print_search_condition as $show_condition){
                  		$pdf->Cell(array_sum($heading_width), 7, $show_condition, "", 1, 'L', 0);
                   } // end foreach
		
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$temp_POS_ID = trim($data[POS_ID]);
			$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
			$PL_NAME = trim($data[PL_NAME]);
			$CL_NAME = trim($data[CL_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$LEVEL_NO = $data[LEVEL_NO];
			
			$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data2[LEVEL_NAME];
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			$POSITION = "$PL_NAME $CL_NAME";
			$POS_PERSON = "";
			if($PER_ID){ 
				if ($RPT_N)
					$POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
				else
					$POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
				$POS_PERSON = "$PN_NAME$PER_NAME $PER_SURNAME";
			} // end if
/*
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $POS_NO, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $POSITION, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $CL_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[3], 7, $POS_PERSON, $border, 0, 'L', 0);
			
			

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=3; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
			//		print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;

	} // end while
*/	
	//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] ="$POS_NO";
				$arr_data[] ="$POSITION";
				$arr_data[] ="$CL_NAME";
				$arr_data[] ="$POS_PERSON";
				$data_align = array("C", "L", "L", "L");
				
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
      			$pdf->add_data_tab("", 0, "TRHBL", $data_align, "cordia", "14", "", "000000", "");		// เส้นปิดบรรทัด		
		}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>