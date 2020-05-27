<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(peh.EX_CODE in('012', '013', '014'))";	
	//ค้นหา ณ วันที
	if(trim($search_end_date)){
		$arr_temp = explode("/", $search_end_date);
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[2] + 0);	//ณ วันที่
	}
	if(trim($search_date)){	}
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$select_position = "c.POS_ID, c.POS_NO_NAME, c.POS_NO, c.PT_CODE, c.PL_CODE, c.PM_CODE";
		$column_count=8;
	}else if($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$select_position = "c.POEM_ID, c.POEM_NO_NAME, c.POEM_NO, c.PN_CODE";
		$column_count=8;
	}else if($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$select_position = "c.POEMS_ID, c.POEMS_NO_NAME, c.POEMS_NO, c.EP_CODE";
		$column_count=8;
	}else if($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$select_position = "c.POT_ID, c.POT_NO_NAME, c.POT_NO, c.TP_CODE";
		$column_count=8;
	} // end if

	include ("rpt_R006017_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006017_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "";
	
	//เซตค่า
	$COM_DATE = $show_date;

	$report_title = "บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว$PERSON_TYPE[$search_per_type] ||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ ". (($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NO):$COM_NO) ."ลงวันที่ ".  (($NUMBER_DISPLAY==2)?convert2thaidigit($COM_DATE):$COM_DATE);
	$report_code = "R6017";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										b.PER_SALARY,b.PER_MGTSALARY, c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID,$select_position 
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						where 	b.PER_ID=peh.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										 b.PER_SALARY,b.PER_MGTSALARY,c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						where 	b.PER_ID=peh.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";	
	}elseif($DPISDB=="mysql"){
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										b.PER_SALARY,b.PER_MGTSALARY,c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID,$select_position 
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						 where 	b.PER_ID=peh.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PREN_CODE = trim($data[PREN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PREN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];	
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$PER_SALARY = $data[PER_SALARY];
		$PER_MGTSALARY = $data[PER_MGTSALARY];	//เงินประจำตำแหน่ง

		$EXH_EFFECTIVEDATE = show_date_format($data[EXH_EFFECTIVEDATE], $DATE_DISPLAY);
		$EX_NAME = trim($data[EX_NAME]);
		$EXH_AMT = trim($data[EXH_AMT]);
		$EXH_ENDDATE = show_date_format($data[EXH_ENDDATE], $DATE_DISPLAY);

		//นำเงินเพิ่มค่าครองชีพ (เช่น เงินช่วยเหลือค่าครองชีพ, เงินตอบแทนพิเศษรายเดือน) มารวมกับเงินประจำตำแหน่ง (ถ้ามี)
		$MGTS_NAME = "";
		if($PER_MGTSALARY > 0){
			$EXH_AMT = ($EXH_AMT+$PER_MGTSALARY);
			$MGTS_NAME = ",เงินประจำตำแหน่ง";
		}
		//ให้ได้รับ = รวมเงินเดือนปัจจุบัน กับ เงินที่ได้รับ	
		$SUM_RCV = ($PER_SALARY + $EXH_AMT);

		//หาข้อมูลตำแหน่ง
		if($PER_TYPE==1){		
			$POS_ID = $data[POS_ID];
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			if (substr($POS_NO_NAME,0,4)=="กปด.")
				$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
			else
				$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
		
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];		
			
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];		
			
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];		
	
			if ($RPT_N)
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
			else
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
		}elseif($PER_TYPE==2){		
			$POEM_ID = $data[POEM_ID];
			$POS_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);
			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select	 PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		}elseif($PER_TYPE==3){		
			$POEMS_ID = $data[POEMS_ID];
			$POS_NO = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]);
			
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select	 EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		} // end if

		if($CMD_ORG3 != trim($data[ORG_ID])){
			$CMD_ORG3 = trim($data[ORG_ID]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CMD_ORG3 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
		
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$data_count++;
		} // end if
		
		/***
		if($CMD_ORG4 != trim($data[ORG_ID_1])){
			$CMD_ORG4 = trim($data[ORG_ID_1]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CMD_ORG4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
		
			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$data_count++;
		} // end if
		***/

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO, $CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_pos_no] = $POS_NO;
		$arr_content[$data_count][cmd_level] = "ท." . level_no_format($LEVEL_NO);
		$arr_content[$data_count][cmd_salary] =($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY)):"-");
		$arr_content[$data_count][cmd_extra] =($EXH_AMT?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXH_AMT)):number_format($EXH_AMT)):"-");
		$arr_content[$data_count][cmd_sumrcv] =($SUM_RCV?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($SUM_RCV)):number_format($SUM_RCV)):"-");
		$arr_content[$data_count][cmd_note1] = ""; //$EX_NAME.$MGTS_NAME;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CARDNO = $arr_content[$data_count][cardno];
			$NAME = $arr_content[$data_count][name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_EXTRA = $arr_content[$data_count][cmd_extra];
			$CMD_SUMRCV = $arr_content[$data_count][cmd_sumrcv];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
				
				$border = "";

				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $ORG_NAME;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";

				$data_align = array("L","L","L","L","L","L","L","L","L");

				if($CONTENT_TYPE == "ORG")
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
/*
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
				
				$border = "";
				if($CONTENT_TYPE == "ORG") $pdf->SetFont($fontb,'',14);
				else $pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
				$pdf->MultiCell($heading_width[3], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[6], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[7], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[8], 7, "", $border, 0, 'L', 0);
				
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=$column_count; $i++){
					if($i < 3){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					}elseif($i==3){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[3] + $heading_width[4]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[3] + $heading_width[4]);
					}elseif($i > 4){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					} // end if

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y) < 21){ 
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
*/
			} else if($CONTENT_TYPE=="CONTENT") {
				$border = "";

				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $CARDNO;
				$arr_data[] = $NAME;
				$arr_data[] = $CMD_POSITION;
				$arr_data[] = $CMD_POS_NO;
				$arr_data[] = $CMD_SALARY;
				$arr_data[] = $CMD_EXTRA;
				$arr_data[] = $CMD_SUMRCV;
				$arr_data[] = $CMD_NOTE1;

				$data_align = array("C","L","L","L","C","R","C","R","R");

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($CMD_NOTE2){
					$result = $RTF->add_text_line("หมายเหตุ : $CMD_NOTE2", 7, "LR", "L", "", "14", "b", 0, 0);
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
				} // end if
/*
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell($heading_width[0], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($CARDNO):$CARDNO), $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$CMD_POSITION", $border,"L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_POS_NO):$CMD_POS_NO), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_SALARY", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[6], 7, "$CMD_EXTRA", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$CMD_SUMRCV", $border, 0, 'R', 0);
				$pdf->Cell($heading_width[8], 7, "$CMD_NOTE1", $border, 0, 'R', 0);

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=$column_count; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "หมายเหตุ : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
	
	//			if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 21){ 
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
*/
			} // end if
		} // end for				

		if($COM_NOTE){
			$border = "";

			$arr_data = (array) null;
			$arr_data[] = "หมายเหตุ : ";
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;
			$arr_data[] = "<**1**>".$COM_NOTE;

			$data_align = array("R","L","L","L","L","L","L","L","L");

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
/*
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "หมายเหตุ : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
*/
		} // end if
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>