<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
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
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
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
	$report_title = "บัญชีสรุปจำนวนข้าราชการ||สำหรับการเลื่อนเงินเดือน 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME  $MINISTRY_NAME||ประจำปีงบประมาณ $search_budget_year||หน่วยงานผู้เบิก $DEPARTMENT_NAME  จังหวัด$DEPARTMENT_PROVINCE";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
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

	$heading_width[0] = "60";
	$heading_width[1] = "20";
	$heading_width[2] = "55";
	$heading_width[3] = "65";

//new format***********************************************************
    $heading_text[0] = "กลุ่มข้าราชการ|ระดับ";
	$heading_text[1] = "<**1**>จำนวนข้าราชการ|1 ต.ค";
	$heading_text[2] = "<**1**>จำนวนข้าราชการ|ตัวอักษร";
	$heading_text[3] = "หมายเหตุ|";

	$heading_align = array('C','C','C','C');

	$cmd = " select PER_ID from PER_SALARYHIS where SAH_KF_YEAR='$search_budget_year' ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	$search_level = array( "O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
	for ( $i=0; $i<count($search_level); $i++ ) { 
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$search_level[$i]' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$LEVEL_NAME = $data[LEVEL_NAME];
			$arr_content[$data_count][type] = "HEADER";
			$arr_content[$data_count][name] = $LEVEL_NAME;
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP < 2 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "น้อยกว่า 2%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$arr_content[$data_count][remark] = $remark[$search_level[$i]];
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP >= 2 and b.SAH_PERCENT_UP < 4 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ตั้งแต่ 2% แต่ไม่ถึง 4%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP >= 4 and b.SAH_PERCENT_UP < 6 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "ตั้งแต่ 4% แต่ไม่ถึง 6%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;

			$cmd = " select count(a.PER_ID) as PROMOTED_PERSON 
							  from PER_PERSONAL a, PER_SALARYHIS b 
							  where a.PER_ID=b.PER_ID and a.LEVEL_NO = '$search_level[$i]' and 
							  b.SAH_KF_YEAR='$search_budget_year' and b.SAH_PERCENT_UP = 6 ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$count_person = $data[PROMOTED_PERSON] + 0;

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "6%";
			$arr_content[$data_count][count_person] = $count_person?number_format($count_person):"-";
			$arr_content[$data_count][count_speech] = $count_person?number2speech($count_person):"ไม่มี";
			$data_count++;	
		} // end for	

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//new format****************************************************************
	    if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_PERSON = $arr_content[$data_count][count_person];
			$COUNT_SPEECH = $arr_content[$data_count][count_speech];
			$REMARK = $arr_content[$data_count][remark];
		
		/*
			$border = "";
			if($REPORT_ORDER == "DETAIL") $pdf->SetFont($font,'',14);
			else $pdf->SetFont($fontb,'',14);
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
		*/
		
			$arr_data = (array) null;
				$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME);
			    $arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($COUNT_PERSON):$COUNT_PERSON);
		    	$arr_data[] ="$COUNT_SPEECH";
				$arr_data[] ="";
			if(($data_count % 4) == 1){
		    	$arr_data[] = "$REMARK";
				}
				$data_align = array("L", "R", "R", "R");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "12", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
				$pdf->add_data_tab("", 0, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// เส้นปิดบรรทัด		
		
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
			} // end  for

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
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>