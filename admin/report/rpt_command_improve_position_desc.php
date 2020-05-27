<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		ORD_NO, ORD_TITLE, ORD_DATE
					 from		PER_ORDER 
					 where	ORD_ID=$ORD_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$ORD_NO = trim($data[ORD_NO]);
	$ORD_TITLE = trim($data[ORD_TITLE]);
	$ORD_DATE = $data[ORD_DATE];
	$ORD_DATE = 	show_date_format($ORD_DATE,$DATE_DISPLAY);

	if ($order_by==1) $order_str = "a.ORD_SEQ";
	else 
		if($DPISDB=="odbc") $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
		elseif($DPISDB=="oci8")  $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-',''))";
		elseif($DPISDB=="mysql")  $order_str = "b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO";
	$order_str = "a.ORD_SEQ";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการปรับปรุงการกำหนดตำแหน่ง||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $ORD_NO ลงวันที่ $ORD_DATE";
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
	
	$heading_width[0] = "8";
	$heading_width[1] = "12";
	$heading_width[2] = "48";
	$heading_width[3] = "36";
	$heading_width[4] = "15";
	$heading_width[5] = "20";
	$heading_width[6] = "48";
	$heading_width[7] = "36";
	$heading_width[8] = "15";
	$heading_width[9] = "20";
	$heading_width[10] = "15";
	$heading_width[11] = "15";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]) ,7,"ส่วนราชการและตำแหน่งที่กำหนดไว้เดิม",'LTRB',0,'C',1);
		$pdf->Cell(($heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9]) ,7,"ส่วนราชการและตำแหน่งที่ขอกำหนดใหม่",'LTRB',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"อัตรา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อตำแหน่งในการบริหารงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อตำแหน่งในสายงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภท",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ชื่อตำแหน่งในการบริหารงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ชื่อตำแหน่งในสายงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ประเภท",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',1,'C',1);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.ORD_SEQ, a.ORD_POS_NO, a.ORG_ID as ORD_ORG_ID, a.ORG_ID_1 as ORD_ORG_ID_1, 
											a.ORG_ID_2 as ORD_ORG_ID_2, a.ORG_ID_3 as ORD_ORG_ID_3, a.ORG_ID_4 as ORD_ORG_ID_4, 
											a.ORG_ID_5 as ORD_ORG_ID_5, a.PL_CODE as ORD_PL_CODE, a.PM_CODE as ORD_PM_CODE, 
											a.CL_NAME as ORD_CL_NAME, a.ORD_SALARY, a.LEVEL_NO as ORD_LEVEL_NO, a.ORD_REMARK,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3, b.ORG_ID_4, b.ORG_ID_5, b.PL_CODE, 
											b.PM_CODE, b.CL_NAME, b.POS_SALARY, b.LEVEL_NO, b.POS_REMARK
						 from			PER_ORDER_DTL a, PER_POSITION b
						 where		a.POS_ID_OLD=b.POS_ID and a.ORD_ID=$ORD_ID
						 order by 	$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$ORD_ORG_ID = $ORD_ORG_ID_1 = $ORD_ORG_ID_2 = -1;
	while($data = $db_dpis->get_array()){
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select OT_CODE, ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_CODE = trim($data2[OT_CODE]);
		$ORG_NAME = trim($data2[ORG_NAME]);

		$ORD_ORG_ID = trim($data[ORD_ORG_ID]);
		$cmd = " select OT_CODE, ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_OT_CODE = trim($data2[OT_CODE]);
		$ORD_ORG_NAME = trim($data2[ORG_NAME]);

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_1 = trim($data[ORD_ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_1' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_1 = trim($data2[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_2 = trim($data[ORD_ORG_ID_2]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_2' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_2 = trim($data2[ORG_NAME]);

		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_3' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_3 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_3 = trim($data[ORD_ORG_ID_3]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_3' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_3 = trim($data2[ORG_NAME]);

		$ORG_ID_4 = trim($data[ORG_ID_4]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_4' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_4 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_4 = trim($data[ORD_ORG_ID_4]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_4' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_4 = trim($data2[ORG_NAME]);

		$ORG_ID_5 = trim($data[ORG_ID_5]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_5' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_5 = trim($data2[ORG_NAME]);

		$ORD_ORG_ID_5 = trim($data[ORD_ORG_ID_5]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORD_ORG_ID_5' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_ORG_NAME_5 = trim($data2[ORG_NAME]);

		$data_row++;
		$POS_NO = trim($data[POS_NO]);
		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = trim($data2[PM_NAME]);
		
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);
		if (!$PM_NAME) $PM_NAME = $PL_NAME;
		
		$CL_NAME = trim($data[CL_NAME]);
		$POS_SALARY = trim($data[POS_SALARY]);
		$POS_REMARK = trim($data[POS_REMARK]);

		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
		
		$ORD_POS_NO = trim($data[ORD_POS_NO]);
		$ORD_PM_CODE = trim($data[ORD_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$ORD_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_PM_NAME = trim($data2[PM_NAME]);
		
		$ORD_PL_CODE = trim($data[ORD_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$ORD_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_PL_NAME = trim($data2[PL_NAME]);
		if (!$ORD_PM_NAME) $ORD_PM_NAME = $ORD_PL_NAME;
		
		$ORD_CL_NAME = trim($data[ORD_CL_NAME]);
		$ORD_SALARY = trim($data[ORD_SALARY]);
		$ORD_REMARK = trim($data[ORD_REMARK]);
		
		$ORD_LEVEL_NO = trim($data[ORD_LEVEL_NO]);
		$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$ORD_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$ORD_POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pm_name] = $PM_NAME;
//		$arr_content[$data_count][pl_name] = ($PL_NAME)?"$PL_NAME $CL_NAME":"";
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][pos_salary] = $POS_SALARY;
		$arr_content[$data_count][position_type] = $POSITION_TYPE;
		$arr_content[$data_count][position_level] = $POSITION_LEVEL;
		
		$arr_content[$data_count][ord_pos_no] = $ORD_POS_NO;
		$arr_content[$data_count][ord_pm_name] = $ORD_PM_NAME;
//		$arr_content[$data_count][ord_pl_name] = ($ORD_PL_NAME)?"$ORD_PL_NAME $ORD_CL_NAME":"";
		$arr_content[$data_count][ord_pl_name] = $ORD_PL_NAME;
		$arr_content[$data_count][ord_cl_name] = $ORD_CL_NAME;
		$arr_content[$data_count][ord_salary] = $ORD_SALARY;
		$arr_content[$data_count][ord_position_type] = $ORD_POSITION_TYPE;
		$arr_content[$data_count][ord_position_level] = $ORD_POSITION_LEVEL;
		$arr_content[$data_count][ord_remark] = $ORD_REMARK;
//		if ($order_by==1) {
			if ($ORG_NAME_5 || $ORD_ORG_NAME_5) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_5;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_5;
			}
			if ($ORG_NAME_4 || $ORD_ORG_NAME_4) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_4;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_4;
			}
			if ($ORG_NAME_3 || $ORD_ORG_NAME_3) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_3;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_3;
			}
			if ($ORG_NAME_2 || $ORD_ORG_NAME_2) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_2;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_2;
			}
			if ($ORG_NAME_1 || $ORD_ORG_NAME_1) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME_1;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME_1;
			}
			if ($ORG_NAME || $ORD_ORG_NAME) {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][pm_name] = $ORG_NAME;
				$arr_content[$data_count][ord_pm_name] = $ORD_ORG_NAME;
			}
			if ($OT_CODE=="01" || $ORD_OT_CODE=="01") {
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				if ($OT_CODE=="01") $arr_content[$data_count][pm_name] = $DEPARTMENT_NAME;
				if ($ORD_OT_CODE=="01") $arr_content[$data_count][ord_pm_name] = $DEPARTMENT_NAME;
			}
//		}
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$POS_SALARY = $arr_content[$data_count][pos_salary];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$POSITION_LEVEL = $arr_content[$data_count][position_level];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORD_POS_NO = $arr_content[$data_count][ord_pos_no];
			$ORD_PM_NAME = $arr_content[$data_count][ord_pm_name];
			$ORD_PL_NAME = $arr_content[$data_count][ord_pl_name];
			$ORD_CL_NAME = $arr_content[$data_count][ord_cl_name];
			$ORD_SALARY = $arr_content[$data_count][ord_salary];
			$ORD_ORG_NAME = $arr_content[$data_count][ord_org_name];			
			$ORD_POSITION_TYPE = $arr_content[$data_count][ord_position_type];
			$ORD_POSITION_LEVEL = $arr_content[$data_count][ord_position_level];
			$ORD_REMARK = $arr_content[$data_count][ord_remark];
		
			if($CONTENT_TYPE == "ORG"){
				$border = "";
				$pdf->SetFont($font,'b','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				$pdf->Cell($heading_width[0], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[1], 7, "", $border, 0, 'R', 0);
				$pdf->MultiCell(($heading_width[2] + $heading_width[3]), 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'C', 0);
				$pdf->MultiCell(($heading_width[6] + $heading_width[7]), 7, "$ORD_ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[8], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[9], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[10], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[11], 7, "", $border, 0, 'L', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=11; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					if($i!=2 && $i!=6) $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

//				if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 22){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			}elseif($CONTENT_TYPE == "CONTENT"){
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[1], 7, "$POS_NO", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[2], 7, "$PM_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$PL_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, "$POSITION_TYPE", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[5], 7, "$POSITION_LEVEL", $border, 0, 'C', 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[6], 7, "$ORD_PM_NAME", $border, 0, 'L', 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[7], 7, "$ORD_PL_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[8], 7, "$ORD_POSITION_TYPE", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[9], 7, "$ORD_POSITION_LEVEL", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[10], 7, ($ORD_SALARY?number_format($ORD_SALARY):""), $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[11], 7, "$ORD_REMARK", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=11; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

//				if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 22){ 
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				}else{
					if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end if
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