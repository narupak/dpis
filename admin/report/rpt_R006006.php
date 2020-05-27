<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

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

	$company_name = "";
	$report_title = "บัญชีสรุปจำนวนข้าราชการ||สำหรับการเลื่อนเงินเดือน 1 ตุลาคม ". ($search_budget_year - 1) ."||$DEPARTMENT_NAME  $MINISTRY_NAME||ประจำปีงบประมาณ $search_budget_year||หน่วยงานผู้เบิก $DEPARTMENT_NAME  จังหวัด$DEPARTMENT_PROVINCE";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0606";
	include ("rpt_R006006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R006006_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='P';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
	
	$cmd = " select PER_ID from PER_SALARYHIS where SAH_KF_YEAR='$search_budget_year' ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	if ($ISCS_FLAG==1) 
		$search_level = array( "K4", "K5", "D1", "D2", "M1", "M2" );
	else
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
	
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function); 
		}
		// not print first head
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_PERSON = $arr_content[$data_count][count_person];
			$COUNT_SPEECH = $arr_content[$data_count][count_speech];
			$REMARK = $arr_content[$data_count][remark];
		
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $COUNT_PERSON;
			$arr_data[] =$COUNT_SPEECH;
			if(($data_count % 4) == 1){
		    	$arr_data[] = $REMARK;
			} else $arr_data[] ="";

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด		
		
		if($data_count > 0){
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "ได้ตรวจสอบถูกต้องแล้ว";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "( $confirm_name )";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "ตำแหน่ง  $confirm_position";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "วันที่                         ตุลาคม ". ($search_budget_year - 1);
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "กรมบัญชีกลางตรวจสอบแล้ว";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "( ............................................................ )";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "ตำแหน่ง ............................................................ ";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "";
			$arr_data[] = "วันที่ ............................................. ";
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "12", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";

			if ($FLAG_RTF)
				$result = $RTF->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("", 7, "", "L", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} // end if
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 

		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
	
		$pdf->close();
		$pdf->Output();	
	}
?>