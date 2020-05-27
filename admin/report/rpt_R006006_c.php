<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select 	PV_CODE
					 from 		PER_ORG
					 where	ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PV_CODE = $data[PV_CODE];
	if($PV_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_PROVINCE = $data[PV_NAME];
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status = 1;
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีสรุปจำนวนข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือนกรณีพิเศษ 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME $MINISTRY_NAME||ประจำปีงบประมาณ $search_budget_year||หน่วยงานผู้เบิก $DEPARTMENT_NAME  จังหวัด$DEPARTMENT_PROVINCE";
	$report_code = "R0606";
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
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "50";
	$heading_width[1] = "15";
	$heading_width[2] = "55";
	$heading_width[3] = "80";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));

		$pdf->Cell(array_sum($heading_width) ,7,"",'',1,'C',0);

		$pdf->Cell($heading_width[0] ,7,"กลุ่มข้าราชการ",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] + $heading_width[2]) ,7,"จำนวนข้าราชการ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ระดับ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"1 ต.ค.",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตัวอักษร",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',1,'C',1);
	} // function		

	$cmd = " select PER_ID from PER_SALPROMOTE where SALQ_YEAR='$search_budget_year' and SALP_YN=1 and SALQ_TYPE=2 ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<8; $i++){
		if($i < 4){
			$search_level_min = 1;
			$search_level_max = 8;
		}else{
			$search_level_min = 9;
			$search_level_max = 11;
		} // end if

		if($i == 0 || ($i % 4) == 0){
			$arr_content[$data_count][type] = "HEADER";
			$arr_content[$data_count][name] = "กลุ่ม $search_level_min - $search_level_max";
			
			$data_count++;
		}elseif(($i % 4) == 1){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "1 ขั้น";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
			if($i < 4) $arr_content[$data_count][remark] = $remark_1?"$remark_1":"(1-8)";
			else $arr_content[$data_count][remark] = $remark_2?"$remark_2":"(9-11)";
			
			$data_count++;
		}elseif(($i % 4) == 2){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "1.5 ขั้น";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
			$data_count++;
		}elseif(($i % 4) == 3){
			if($DPISDB=="odbc"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="oci8"){ 
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
			} // end if

			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ค่าตอบแทน 4 %";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			
			$data_count++;
		} // end if
	} // end for	

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_PERSON = $arr_content[$data_count][count_person];
			$COUNT_SPEECH = $arr_content[$data_count][count_speech];
			$REMARK = $arr_content[$data_count][remark];
			
			$border = "";
			if($REPORT_ORDER == "DETAIL") $pdf->SetFont($font,'',14);
			else $pdf->SetFont($font,'b','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$NAME", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[1], 7, "$COUNT_PERSON", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[2], 7, "$COUNT_SPEECH", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;

			if(($data_count % 4) == 1){
				$pdf->MultiCell($heading_width[3], 7, "$REMARK", $border, "L");
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
			} // end if

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

			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
		
		if($data_count > 0){
			$border = "LR";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "ได้ตรวจสอบถูกต้องแล้ว", $border, 1, 'C', 0);

			for($i=0; $i<3; $i++){
				$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);
			} // end if

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "( $confirm_name )", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "ตำแหน่ง  $confirm_position", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "วันที่                         ตุลาคม ". ($search_budget_year - 1), $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "กรมบัญชีกลางตรวจสอบแล้ว", $border, 1, 'C', 0);

			for($i=0; $i<3; $i++){
				$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);
			} // end if

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "( ............................................................ )", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "ตำแหน่ง ............................................................ ", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "วันที่ ............................................. ", $border, 1, 'C', 0);

			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);

			$border = "LBR";
			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[1], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, "", $border, 1, 'C', 0);
		} // end if
		
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>