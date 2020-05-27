<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "การประเมินสมรรถนะหลักทางการบริหาร";
	$report_code = "";
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
	
	$heading_width[0] = "10";
	$heading_width[1] = "40";
	$heading_width[2] = "35";
	$heading_width[3] = "40";
	$heading_width[4] = "40";
	$heading_width[5] = "45";
	$heading_width[6] = "35";

	$heading_text[0] = "ลำดับที่";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ตำแหน่ง/ระดับ";
	$heading_text[3] = "สังกัด";
	$heading_text[4] ="วันที่ประเมิน";
	$heading_text[5] = "วันที่ขึ้นทะเบียน";
	$heading_text[6] = "Mean";
	
	$heading_align = array('C','C','C','C','C','C','C','C','C');

	$from = "PER_MGT_COMPETENCY_ASSESSMENT a";		
	//echo $from."+".$search_condition."+".$order_str; exit;
		
	if($DPISDB=="odbc"){	
		$cmd = " select 	a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID    
						 from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where	a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
										$limit_data
						$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select		a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID   
								from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
	   							where 		a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+)
												$search_condition
												$limit_data
								$order_str 
					 ";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID    
						 from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where	a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
										$limit_data
						$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "$cmd ($count_data)<br>";		exit;
	$data_count = $data_row = 0;
     while ($data = $db_dpis->get_array()) {
            $data_row++;

            $temp_CA_ID = $data[CA_ID];
            $current_list .= ((trim($current_list))?", ":"") . "'" . $temp_CA_ID ."'";
    
            $CA_CODE = $data[CA_CODE];
            $PN_CODE = trim($data[PN_CODE]);
            $CA_NAME = $data[CA_NAME];		
            $CA_SURNAME = $data[CA_SURNAME];
            $CA_TEST_DATE = show_date_format($data[CA_TEST_DATE], 1);
            $CA_APPROVE_DATE = show_date_format($data[CA_APPROVE_DATE], 1);
            $TMP_CA_MEAN = trim($data[CA_MEAN]);
    
            $PER_ID = trim($data[PER_ID]);
//			$PER_ID = trim($data[CA_ID]);
			$TMP_POSITION = "";
            if ($PER_ID) {
                $cmd = " select POS_ID, a.LEVEL_NO, b.LEVEL_NAME,b.POSITION_LEVEL, PN_CODE, PER_NAME, PER_SURNAME 
							from PER_PERSONAL a, PER_LEVEL b 
							where PER_ID=$PER_ID and a.LEVEL_NO=b.LEVEL_NO ";
                $db_dpis2->send_cmd($cmd);
                if ($data2 = $db_dpis2->get_array()) {
                    $POS_ID = trim($data2[POS_ID]);
                    $LEVEL_NO = trim($data2[LEVEL_NO]);
                    $LEVEL_NAME = trim($data2[POSITION_LEVEL]);
                    $PN_CODE = trim($data2[PN_CODE]);
                    $CA_NAME = trim($data2[PER_NAME]);
                    $CA_SURNAME = trim($data2[PER_SURNAME]);
        
                    if ($POS_ID) { 
                        $cmd = " select b.PL_NAME, a.CL_NAME, c.ORG_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b, PER_ORG c 
                                where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
                        $db_dpis2->send_cmd($cmd);
                        //echo $cmd;
                        $data2 = $db_dpis2->get_array();
        //				$TMP_POSITION = $data2[PL_NAME] . " " . $data2[CL_NAME];
                        $TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
                    }
                    $ORG_NAME = $data2[ORG_NAME];
                } else { // ถ้าอ่านไม่ได้
                    $POS_ID = "";
                    $LEVEL_NO = trim($data[LEVEL_NO]);
                    $LEVEL_NAME = "";
                    $ORG_NAME = trim($data[CA_ORG_NAME]).trim($data[CA_DEPARTMENT_NAME]);
                    $TMP_POSITION = trim($data[CA_LINE]);
                }	// if ($data2 = $db_dpis2->get_array())
            }	// if ($PER_ID)
            
			$PN_NAME = "";
            if ($PN_CODE) {
                $cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $PN_NAME = $data2[PN_NAME];
            }

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $data_row;
			$arr_content[$data_count][per_name] = "$PN_NAME$CA_NAME $CA_SURNAME";
			$arr_content[$data_count][position] = $TMP_POSITION;
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$arr_content[$data_count][ca_test_date] =  $CA_TEST_DATE;
			$arr_content[$data_count][ca_approve_date] =  $CA_APPROVE_DATE;
			$arr_content[$data_count][ca_mean] =  $TMP_CA_MEAN;
					
	$data_count++;
	} // end while
	
		if($count_data){
			$pdf->AutoPageBreak = false;
			$head_text1 = implode(",", $heading_text);
			$head_width1 = implode(",", $heading_width);
			$head_align1 = implode(",", $heading_align);
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
	
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$ORDER = $arr_content[$data_count][order];
				$PER_NAME = $arr_content[$data_count][per_name];
				$POSITION = $arr_content[$data_count][position];
				$ORG_NAME = $arr_content[$data_count][org_name];
				 $CA_TEST_DATE = $arr_content[$data_count][ca_test_date];
				$CA_APPROVE_DATE = $arr_content[$data_count][ca_approve_date];
				$CA_MEAN = $arr_content[$data_count][ca_mean];
			
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$PER_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, "$CA_TEST_DATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[5], 7, "$CA_APPROVE_DATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[6], 7, "$CA_MEAN", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=6; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if(($pdf->h - $max_y - 10) < 15){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
	//					print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end for				
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>