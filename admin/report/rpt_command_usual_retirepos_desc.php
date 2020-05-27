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
	
	$cmd = " select 	a.ORG_ID, b.ORG_NAME, b.ORG_ID_REF
					 from 		PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID
				   ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "จัดทำบัญชีคำขอยุบเลิก/ขอจัดสรร ตำแหน่งเกษียณ (ปกติ)||สำนักนายกรัฐมนตรี||(ส่งพร้อมหนังสือสำนักนายกรัฐมนตรี||ที่". str_repeat(".", 40) ." ลงวันที่". str_repeat(".", 40) .")";
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
	$heading_width[2] = "85";
	$heading_width[3] = "15";
	$heading_width[4] = "12";
	$heading_width[5] = "85";
	$heading_width[6] = "15";
	$heading_width[7] = "20";
	$heading_width[8] = "20";
	$heading_width[9] = "15";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3]) ,7,"หน่วยงาน/ตำแหน่งที่ว่างจากการเกษียณอายุ",'LTRB',0,'C',1);
		$pdf->Cell(($heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8]) ,7,"หน่วยงาน/ตำแหน่งว่างที่ยุบเลิกแทนตำแหน่งเกษียณอายุ",'LTRB',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"อัตรา",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"วัน เดือน ปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"หมายเหตุ",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเดือน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ที่ยุบเลิก",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',1,'C',1);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, LEFT(trim(a.REQ_EFF_DATE), 10) as REQ_EFF_DATE
						 from			(
							 					PER_REQ2_DTL a
							 					inner join PER_POSITION b on (a.POS_ID_RETIRE=b.POS_ID)
											) left join PER_POSITION c on (a.POS_ID_DROP=c.POS_ID)
						 where		a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, SUBSTR(trim(a.REQ_EFF_DATE), 1, 10) as REQ_EFF_DATE
						 from			PER_REQ2_DTL a, PER_POSITION b, PER_POSITION c
						 where		a.POS_ID_RETIRE=b.POS_ID and a.POS_ID_DROP=c.POS_ID(+) and a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-',''))  
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			b.POS_NO as RETIRE_POS_NO, b.ORG_ID as RETIRE_ORG_ID, b.ORG_ID_1 as RETIRE_ORG_ID_1, 
											b.ORG_ID_2 as RETIRE_ORG_ID_2, 
											b.PL_CODE as RETIRE_PL_CODE, b.PM_CODE as RETIRE_PM_CODE, b.CL_NAME as RETIRE_CL_NAME,
											c.POS_NO as DROP_POS_NO, c.ORG_ID as DROP_ORG_ID, c.ORG_ID_1 as DROP_ORG_ID_1, 
											c.ORG_ID_2 as DROP_ORG_ID_2, c.PL_CODE as DROP_PL_CODE, c.PM_CODE as DROP_PM_CODE, 
											c.CL_NAME as DROP_CL_NAME, c.POS_SALARY as DROP_SALARY, 
											a.REQ_RESULT, LEFT(trim(a.REQ_EFF_DATE), 10) as REQ_EFF_DATE
						 from			(
							 					PER_REQ2_DTL a
							 					inner join PER_POSITION b on (a.POS_ID_RETIRE=b.POS_ID)
											) left join PER_POSITION c on (a.POS_ID_DROP=c.POS_ID)
						 where		a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO
					   ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$RETIRE_ORG_ID = $RETIRE_ORG_ID_1 = $RETIRE_ORG_ID_2 = 1;
	while($data = $db_dpis->get_array()){
		if($RETIRE_ORG_ID != $data[RETIRE_ORG_ID]){		
			$RETIRE_ORG_ID = trim($data[RETIRE_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME = trim($data2[ORG_NAME]);

			$DROP_ORG_ID = trim($data[DROP_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME || $DROP_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = $RETIRE_ORG_NAME;
				$arr_content[$data_count][drop_org_name] = $DROP_ORG_NAME;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID] != $data[DROP_ORG_ID]){
			$DROP_ORG_ID = trim($data[DROP_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME || $DROP_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = $RETIRE_ORG_NAME;
				$arr_content[$data_count][drop_org_name] = $DROP_ORG_NAME;
				
				$data_count++;
			} // end if
		} // end if

		if($RETIRE_ORG_ID_1 != $data[RETIRE_ORG_ID_1]){
			$RETIRE_ORG_ID_1 = trim($data[RETIRE_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME_1 = trim($data2[ORG_NAME]);

			$DROP_ORG_ID_1 = trim($data[DROP_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_1 || $DROP_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 5) . $RETIRE_ORG_NAME_1;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 5) . $DROP_ORG_NAME_1;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID_1] != $data[DROP_ORG_ID_1]){
			$DROP_ORG_ID_1 = trim($data[DROP_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_1 || $DROP_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 5) . $RETIRE_ORG_NAME_1;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 5) . $DROP_ORG_NAME_1;
				
				$data_count++;
			} // end if
		} // end if

		if($RETIRE_ORG_ID_2 != $data[RETIRE_ORG_ID_2]){
			$RETIRE_ORG_ID_2 = trim($data[RETIRE_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$RETIRE_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$RETIRE_ORG_NAME_2 = trim($data2[ORG_NAME]);

			$DROP_ORG_ID_2 = trim($data[DROP_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_2 || $DROP_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 10) . $RETIRE_ORG_NAME_2;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 10) . $DROP_ORG_NAME_2;
				
				$data_count++;
			} // end if
		}elseif($data[RETIRE_ORG_ID_2] != $data[DROP_ORG_ID_2]){
			$DROP_ORG_ID_2 = trim($data[DROP_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$DROP_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DROP_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($RETIRE_ORG_NAME_2 || $DROP_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][retire_org_name] = str_repeat(" ", 10) . $RETIRE_ORG_NAME_2;
				$arr_content[$data_count][drop_org_name] = str_repeat(" ", 10) . $DROP_ORG_NAME_2;
				
				$data_count++;
			} // end if
		} // end if

		$data_row++;
		$RETIRE_POS_NO = trim($data[RETIRE_POS_NO]);
		$RETIRE_PM_CODE = trim($data[RETIRE_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$RETIRE_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PM_NAME = trim($data2[PM_NAME]);
		
		$RETIRE_PL_CODE = trim($data[RETIRE_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$RETIRE_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$RETIRE_PL_NAME = trim($data2[PL_NAME]);
		
		$RETIRE_CL_NAME = trim($data[RETIRE_CL_NAME]);

		$DROP_POS_NO = trim($data[DROP_POS_NO]);
		$DROP_PM_CODE = trim($data[DROP_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$DROP_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PM_NAME = trim($data2[PM_NAME]);
		
		$DROP_PL_CODE = trim($data[DROP_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$DROP_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DROP_PL_NAME = trim($data2[PL_NAME]);
		
		$DROP_CL_NAME = trim($data[DROP_CL_NAME]);
		$DROP_SALARY = trim($data[DROP_SALARY]);

		$REQ_RESULT = trim($data[REQ_RESULT]);
		$REQ_EFF_DATE = trim($data[REQ_EFF_DATE]);
		if($REQ_EFF_DATE && $REQ_RESULT!=""){
			$REQ_EFF_DATE = show_date_format($REQ_EFF_DATE,$DATE_DISPLAY);
		}else{
			$REQ_EFF_DATE = "";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][retire_pos_no] = $RETIRE_POS_NO;
		$arr_content[$data_count][retire_position] = ($RETIRE_PM_NAME)?($RETIRE_PM_NAME.(($RETIRE_PL_NAME)?" ($RETIRE_PL_NAME $RETIRE_CL_NAME)":"")):(($RETIRE_PL_NAME)?"$RETIRE_PL_NAME $RETIRE_CL_NAME":"");
		$arr_content[$data_count][retire_cl_name] = $RETIRE_CL_NAME;
		
		$arr_content[$data_count][drop_pos_no] = $DROP_POS_NO;
		$arr_content[$data_count][drop_position] = ($DROP_PM_NAME)?($DROP_PM_NAME.(($DROP_PL_NAME)?" ($DROP_PL_NAME $DROP_CL_NAME)":"")):(($DROP_PL_NAME)?"$DROP_PL_NAME $DROP_CL_NAME":"");
		$arr_content[$data_count][drop_cl_name] = $DROP_CL_NAME;
		$arr_content[$data_count][drop_salary] = $DROP_SALARY;
		$arr_content[$data_count][req_eff_date] = $REQ_EFF_DATE;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$RETIRE_POS_NO = $arr_content[$data_count][retire_pos_no];
			$RETIRE_POSITION = $arr_content[$data_count][retire_position];
			$RETIRE_CL_NAME = $arr_content[$data_count][retire_cl_name];
			$RETIRE_ORG_NAME = $arr_content[$data_count][retire_org_name];
			$DROP_POS_NO = $arr_content[$data_count][drop_pos_no];
			$DROP_POSITION = $arr_content[$data_count][drop_position];
			$DROP_CL_NAME = $arr_content[$data_count][drop_cl_name];
			$DROP_SALARY = $arr_content[$data_count][drop_salary];
			$DROP_ORG_NAME = $arr_content[$data_count][drop_org_name];
			$REQ_EFF_DATE = $arr_content[$data_count][req_eff_date];
		
			if($CONTENT_TYPE == "ORG"){
				$border = "";
				$pdf->SetFont($font,'b','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				$pdf->Cell($heading_width[0], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[1], 7, "", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[2], 7, "$RETIRE_ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[5], 7, "$DROP_ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[6], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[9], 7, "", $border, 0, 'L', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=9; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
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
			
				$pdf->Cell($heading_width[0], 7, "$ORDER.", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[1], 7, "$RETIRE_POS_NO", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[2], 7, "$RETIRE_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$RETIRE_CL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$DROP_POS_NO", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[5], 7, "$DROP_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[6], 7, "$DROP_CL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[7], 7, ($DROP_SALARY?number_format($DROP_SALARY):""), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, "$REQ_EFF_DATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[9], 7, "", $border, 0, 'L', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=9; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 15){ 
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