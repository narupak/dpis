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
	$report_title = "จัดทำบัญชีคำขอปรับปรุง/เกลี่ยตำแหน่ง||(ส่งพร้อมหนังสือสำนักนายกรัฐมนตรี||ที่". str_repeat(".", 40) ." ลงวันที่". str_repeat(".", 40) .")";
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
	$heading_width[2] = "45";
	$heading_width[3] = "45";
	$heading_width[4] = "15";
	$heading_width[5] = "15";
	$heading_width[6] = "12";
	$heading_width[7] = "45";
	$heading_width[8] = "45";
	$heading_width[9] = "15";
	$heading_width[10] = "15";
	$heading_width[11] = "15";
	
	function print_header(){
		global $pdf, $heading_width, $select_org_structure;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]) ,7,"ส่วนราชการและตำแหน่งเดิม",'LTRB',0,'C',1);
		$pdf->Cell(($heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10]) ,7,"ส่วนราชการและตำแหน่งใหม่",'LTRB',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"หมาย",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ชื่อตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ชื่อตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"เหตุ",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ในการบริหาร",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ในสายงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ในการบริหาร",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ในสายงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ประเภท",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',1,'C',1);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.PT_CODE as REQ_PT_CODE,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PL_CODE, b.PM_CODE, b.CL_NAME, b.PT_CODE
						 from			PER_REQ3_DTL a, PER_POSITION b
						 where		a.POS_ID=b.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.PT_CODE as REQ_PT_CODE,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PL_CODE, b.PM_CODE, b.CL_NAME, b.PT_CODE
						 from			PER_REQ3_DTL a, PER_POSITION b
						 where		a.POS_ID=b.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, to_number(replace(b.POS_NO,'-',''))  
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.REQ_SEQ, a.REQ_POS_NO, a.ORG_ID as REQ_ORG_ID, a.ORG_ID_1 as REQ_ORG_ID_1, a.ORG_ID_2 as REQ_ORG_ID_2,
											a.PL_CODE as REQ_PL_CODE, a.PM_CODE as REQ_PM_CODE, a.CL_NAME as REQ_CL_NAME, a.PT_CODE as REQ_PT_CODE,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PL_CODE, b.PM_CODE, b.CL_NAME, b.PT_CODE
						 from			PER_REQ3_DTL a, PER_POSITION b
						 where		a.POS_ID=b.POS_ID and a.REQ_ID=$REQ_ID
						 order by 	b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_NO
					   ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = 1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID != $data[ORG_ID]){		
			$ORG_ID = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);

			$REQ_ORG_ID = trim($data[REQ_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME = trim($data2[ORG_NAME]);

			if($ORG_NAME || $REQ_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $ORG_NAME;
				$arr_content[$data_count][req_org_name] = $REQ_ORG_NAME;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID] != $data[REQ_ORG_ID]){
			$REQ_ORG_ID = trim($data[REQ_ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME = trim($data2[ORG_NAME]);

			if($ORG_NAME || $REQ_ORG_NAME){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $ORG_NAME;
				$arr_content[$data_count][req_org_name] = $REQ_ORG_NAME;
				
				$data_count++;
			} // end if
		} // end if

		if($ORG_ID_1 != $data[ORG_ID_1]){
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);

			$REQ_ORG_ID_1 = trim($data[REQ_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($ORG_NAME_1 || $REQ_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 5) . $ORG_NAME_1;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 5) . $REQ_ORG_NAME_1;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID_1] != $data[REQ_ORG_ID_1]){
			$REQ_ORG_ID_1 = trim($data[REQ_ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_1' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_1 = trim($data2[ORG_NAME]);

			if($ORG_NAME_1 || $REQ_ORG_NAME_1){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 5) . $ORG_NAME_1;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 5) . $REQ_ORG_NAME_1;
				
				$data_count++;
			} // end if
		} // end if

		if($ORG_ID_2 != $data[ORG_ID_2]){
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

			$REQ_ORG_ID_2 = trim($data[REQ_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($ORG_NAME_2 || $REQ_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 10) . $ORG_NAME_2;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 10) . $REQ_ORG_NAME_2;
				
				$data_count++;
			} // end if
		}elseif($data[ORG_ID_2] != $data[REQ_ORG_ID_2]){
			$REQ_ORG_ID_2 = trim($data[REQ_ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$REQ_ORG_ID_2' ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REQ_ORG_NAME_2 = trim($data2[ORG_NAME]);

			if($ORG_NAME_2 || $REQ_ORG_NAME_2){
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = str_repeat(" ", 10) . $ORG_NAME_2;
				$arr_content[$data_count][req_org_name] = str_repeat(" ", 10) . $REQ_ORG_NAME_2;
				
				$data_count++;
			} // end if
		} // end if

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
		
		$CL_NAME = trim($data[CL_NAME]);

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$REQ_POS_NO = trim($data[REQ_POS_NO]);
		$REQ_PM_CODE = trim($data[REQ_PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REQ_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PM_NAME = trim($data2[PM_NAME]);
		
		$REQ_PL_CODE = trim($data[REQ_PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$REQ_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PL_NAME = trim($data2[PL_NAME]);
		
		$REQ_CL_NAME = trim($data[REQ_CL_NAME]);

		$REQ_PT_CODE = trim($data[REQ_PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$REQ_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REQ_PT_NAME = trim($data2[PT_NAME]);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pl_name] = ($PL_NAME)?"$PL_NAME $CL_NAME":"";
		$arr_content[$data_count][cl_name] = $CL_NAME;
		$arr_content[$data_count][pt_name] = $PT_NAME;
		
		$arr_content[$data_count][req_pos_no] = $REQ_POS_NO;
		$arr_content[$data_count][req_pm_name] = $REQ_PM_NAME;
		$arr_content[$data_count][req_pl_name] = ($REQ_PL_NAME)?"$REQ_PL_NAME $REQ_CL_NAME":"";
		$arr_content[$data_count][req_cl_name] = $REQ_CL_NAME;
		$arr_content[$data_count][req_pt_name] = $REQ_PT_NAME;
		
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
			$PT_NAME = $arr_content[$data_count][pt_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$REQ_POS_NO = $arr_content[$data_count][req_pos_no];
			$REQ_PM_NAME = $arr_content[$data_count][req_pm_name];
			$REQ_PL_NAME = $arr_content[$data_count][req_pl_name];
			$REQ_CL_NAME = $arr_content[$data_count][req_cl_name];
			$REQ_PT_NAME = $arr_content[$data_count][req_pt_name];
			$REQ_ORG_NAME = $arr_content[$data_count][req_org_name];			
		
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
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, "", $border, 0, 'R', 0);
				$pdf->MultiCell(($heading_width[7] + $heading_width[8]), 7, "$REQ_ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;
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
					if($i!=2 && $i!=7) $pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
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
				$pdf->Cell($heading_width[1], 7, "$POS_NO", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[2], 7, "$PM_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$PL_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, "$CL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[5], 7, "$PT_NAME", $border, "R");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[6], 7, "$REQ_POS_NO", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[7], 7, "$REQ_PM_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[8], 7, "$REQ_PL_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[9], 7, "$REQ_CL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[10], 7, "$REQ_PT_NAME", $border, "R");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[11], 7, "", $border, "L");
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