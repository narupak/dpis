<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";
	$orientation='P';

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
	$heading_width[1] = "160";
	$heading_width[2] = "20";
		
	function print_header(){
		global $pdf, $heading_width, $ACTIVE_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"หน้าที่ความรับผิดชอบหลัก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"$ACTIVE_TITLE",'LTBR',1,'C',1);
	} // function		
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  if(trim($search_PL_CODE)) $arr_search_condition[] = "(a.PL_CODE like '$search_PL_CODE%')";
  	if(trim($search_ORG_ID)) $arr_search_condition[] = "(a.ORG_ID like '$search_ORG_ID%')";
  	if(trim($search_mjt_code)) $arr_search_condition[] = "(a.MJT_CODE like '%$search_mjt_code%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)){
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select	a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, PL_NAME, MJT_NAME
									from	PER_LINE_COMPETENCE a, PER_LINE b, PER_MAIN_JOB_TYPE c 
									where 	a.PL_CODE=b.PL_CODE and 
												a.MJT_CODE=c.MJT_CODE and
												a.PL_CODE=$PL_CODE and a.ORG_ID = $ORG_ID
											$search_condition 
									order by a.PL_CODE, a.ORG_ID, a.MJT_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, PL_NAME, MJT_NAME 
								  from 		PER_LINE_COMPETENCE a, PER_LINE b, PER_MAIN_JOB_TYPE c 
								  where 	trim(a.PL_CODE)=trim(b.PL_CODE) and 
												a.MJT_CODE=c.MJT_CODE and
												trim(a.PL_CODE)=$PL_CODE and a.ORG_ID = $ORG_ID
											$search_condition 
								  order by 	a.PL_CODE, a.ORG_ID, a.MJT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, PL_NAME, MJT_NAME 
					from 		PER_LINE_COMPETENCE a, PER_LINE b, PER_MAIN_JOB_TYPE c 
					where 		a.PL_CODE=b.PL_CODE and 
									a.MJT_CODE=c.MJT_CODE and
									a.PL_CODE=$PL_CODE and a.ORG_ID = $ORG_ID
								$search_condition 
					order by 	a.PL_CODE, a.ORG_ID, a.MJT_CODE ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;		
			$data_num++;	

		$temp_mjt_code = $data[MJT_CODE];		
		$temp_org_id = $data[ORG_ID];
		$MJT_NAME = $data[MJT_NAME];		
		$LC_ACTIVE = $data[LC_ACTIVE];
		$LC_ACTIVE = ($data[LC_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $data_num, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $MJT_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->Image($LC_ACTIVE,($pdf->x + ($heading_width[2] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=2; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');		
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>