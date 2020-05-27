<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
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
	
	$heading_width[0] = "15";
	$heading_width[1] = "45";
	$heading_width[2] = "62";
	$heading_width[3] = "30";
	$heading_width[4] = "15";
	$heading_width[5] = "20";
	$heading_width[6] = "25";
	$heading_width[7] = "35";
	$heading_width[8] = "35";
		
	function print_header(){
		global $pdf, $heading_width;
		global $COM_PER_TYPE, $SHOW_PRO_DATE, $POS_POEM_NO, $POS_POEM_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME;
		
		$pdf->SetFont($font,'b','',14);
		$pdf->Cell(($heading_width[0] + $heading_width[1]) ,7,"ประเภทบุคลากร : ".$PERSON_TYPE[$COM_PER_TYPE],'',0,'L',0);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"วันที่ประมวลผล : ".($SHOW_PRO_DATE?$SHOW_PRO_DATE:""),'',1,'L',0);
		$pdf->Cell(($heading_width[0] + $heading_width[1]) ,7,"$MINISTRY_TITLE : ".($MINISTRY_NAME?$MINISTRY_NAME:""),'',0,'L',0);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"$DEPARTMENT_TITLE : ".($DEPARTMENT_NAME?$DEPARTMENT_NAME:""),'',1,'L',0);
		$pdf->Cell(($heading_width[0] + $heading_width[1]) ,7,"เลขที่ตำแหน่ง : ".($POS_POEM_NO?$POS_POEM_NO:""),'',0,'L',0);
		$pdf->Cell(($heading_width[2] + $heading_width[3]) ,7,"ตำแหน่ง : ".($POS_POEM_NAME?$POS_POEM_NAME:""),'',1,'L',0);
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"อัตราเงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"วันเข้าสู่ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ระยะเวลาที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"อายุราชการ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ดำรงตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"(ปี/เดือน/วัน)",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"(ปี/เดือน/วัน)",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
	} // function		
		
	if ($COM_PER_TYPE == 1) {
		$search_main_from = "PER_PROMOTE_P";
		$search_pos = "POS_ID"; 
		$search_from = ", PER_POSITION d";		
		$search_field = ", d.POS_CHANGE_DATE, d.PM_CODE, d.PL_CODE, b.LEVEL_NO, e.POSITION_LEVEL, d.PT_CODE, d.POS_NO";
	} elseif ($COM_PER_TYPE == 2)	{
		$search_main_from = "PER_PROMOTE_E";
		$search_pos = "POEM_ID"; 
		$search_from = ", PER_POS_EMP d";				
		$search_field = ", d.PN_CODE, d.POEM_NO";
	} elseif ($COM_PER_TYPE == 3) {
		$search_main_from = "";	
		$search_pos = "POEMS_ID"; 
		$search_from = ", PER_POS_EMPSER d";
		$search_field = ", d.POEMS_NO, d.EP_CODE";
	}elseif ($COM_PER_TYPE == 4) {
		$search_main_from = "";	
		$search_pos = "POT_ID"; 
		$search_from = ", PER_POS_TEMP d";
		$search_field = ", d.POT_NO, d.TP_CODE";
	}

	if (trim($PRO_DATE)) {	
		$PRO_DATE =  save_date($PRO_DATE);
		$arr_search_condition[] = "(PRO_DATE like '$PRO_DATE%')";
		$SHOW_PRO_DATE = show_date_format($data[PRO_DATE], 1);
	} 
	if (trim($POS_POEM_ID)) {
		$arr_search_condition[] = "(a.$search_pos = $POS_POEM_ID)";	
	}	
	if (trim($search_from))		// if table ที่เก็บชื่อตำแหน่ง
		$arr_search_condition[] = "(b.$search_pos = d.$search_pos)";

	if($DEPARTMENT_ID){ 
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(a.DEPARTMENT_ID=$DEPARTMENT_ID)";
		elseif($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(b.DEPARTMENT_ID=$DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data=$db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if

	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = " select		a.$search_pos $search_field as POS_NO, 
										a.PER_ID, c.PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE
						 from 		$search_main_from a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e
										$search_from 
						 where 	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE and b.LEVEL_NO=e.LEVEL_NO
										$search_condition
						 order by PRO_DATE desc, a.$search_pos, a.PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select 		a.$search_pos $search_field as POS_NO, 
										a.PER_ID, PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE, e.LEVEL_NAME
						  from 			$search_main_from a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e
								  			$search_from 
						  where 		a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE(+) and b.LEVEL_NO=e.LEVEL_NO(+)
								  			$search_condition
						 order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.$search_pos $search_field as POS_NO, 
										a.PER_ID, c.PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE
						 from 		$search_main_from a, PER_PERSONAL b, PER_PRENAME c, PER_LEVEL e
										$search_from 
						 where 	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE and b.LEVEL_NO=e.LEVEL_NO
										$search_condition
						 order by PRO_DATE desc, a.$search_pos, a.PER_ID ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("d.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$PER_ID = $data[PER_ID];
			$PER_NAME = trim($data[PN_NAME]) . trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = number_format(trim($data[PER_SALARY]), 2, '.', ',');
			
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], 1);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE], 1);
			$PER_STARTDATE = date_difference(date("Y-m-d"), trim($data[PER_STARTDATE]), "full");
			$POS_STARTDATE = date_difference(date("Y-m-d"), trim($data[POS_CHANGE_DATE]), "full");
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[POSITION_LEVEL]);

			$POH_EFFECTIVEDATE = "";
			$cmd = " select			POH_EFFECTIVEDATE
								 from			PER_POSITIONHIS
								 where		PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO' 
								 order by		POH_EFFECTIVEDATE ";
			
			$db_dpis1->send_cmd($cmd);
		//		$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$POH_EFFECTIVEDATE = substr(trim($data1[POH_EFFECTIVEDATE]), 0, 10);
			//$POS_STARTDATE = "";
			//if($POH_EFFECTIVEDATE) $POS_STARTDATE = date_difference(date("Y-m-d"), $POH_EFFECTIVEDATE, "full");

			$PL_NAME = $PN_NAME = $PM_NAME = $ORG_NAME = "";
	
			if($COM_PER_TYPE==1){
				$PL_CODE = trim($data[PL_CODE]);
				if($PL_CODE){
					$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[PL_NAME]);
				} // end if		

				$PT_CODE = trim($data[PT_CODE]);
				if($PT_CODE){
					$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PT_NAME = $data2[PT_NAME];
				} // end if
		
				$PM_CODE = trim($data[PM_CODE]);
				if($PM_CODE){
					$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PM_NAME = $data2[PM_NAME];
				} // end if
			}elseif($COM_PER_TYPE==2){
				$PL_CODE = trim($data[PN_CODE]);
				if($PL_CODE){
					$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[PN_NAME]);
				} // end if		
			}elseif($COM_PER_TYPE==3){
				$PL_CODE = trim($data[EP_CODE]);
				if($PL_CODE){
					$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[EP_NAME]);
				} // end if		
			} elseif($COM_PER_TYPE==4){
				$PL_CODE = trim($data[TP_CODE]);
				if($PL_CODE){
					$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[TP_NAME]);
				} // end if		
			} // end if
	
//			$POSITION = "($PL_NAME ".level_no_format($LEVEL_NO).")";
			$POSITION = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");

			$ORG_ID = trim($data[ORG_ID]);
			if($ORG_ID){
				$cmd = " select ORG_SHORT from PER_ORG where ORG_ID='$ORG_ID' ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = $data2[ORG_SHORT];
			} // end if

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $data_row, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $PER_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $POSITION, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $ORG_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[4], 7, $POS_NO, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[5], 7, $PER_SALARY, $border, 0, 'R', 0);
			$pdf->Cell($heading_width[6], 7, $POS_CHANGE_DATE, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[7], 7, $POS_STARTDATE, $border, 0, 'L', 0);
			$pdf->Cell($heading_width[8], 7, $PER_STARTDATE, $border, 0, 'L', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=8; $i++){
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
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>