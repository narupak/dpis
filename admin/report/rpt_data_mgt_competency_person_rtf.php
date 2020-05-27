<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$fname= "rpt_data_mgt_competency_person_RTF.rtf";

	$RTF = new RTF();
	$RTF = new RTF("a4", 1800, 1100, 600, 200);
//	$RTF = new RTF("a4", 1200, 1100, 600, 200);
	$RTF->set_default_font($font, 16);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$heading_width[0] = "20";
	$heading_width[1] = "80";
	$heading_width[2] = "100";

	$heading_text[0] = "รหัส";
	$heading_text[1] = "ชื่อ - สกุล";
	$heading_text[2] = "หมายเหตุ";
	
	$heading_align = array('C','C','C');
	$heading_bgcolor = array(LIGHTBLUE1,LIGHTBLUE1,LIGHTBLUE1,LIGHTBLUE1);

	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	$cmd = " select CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE, CA_APPROVE_DATE, 
							PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2, CA_SCORE_3, 
							CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10, CA_SCORE_11, 
							CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,CA_ORG_NAME2, 
							CA_LINE, LEVEL_NO, CA_MGT, CA_POSITION, CA_POS_NO, CA_DOC_DATE, CA_DOC_NO, CA_NEW_SCORE_1, CA_NEW_SCORE_2, 
							CA_NEW_SCORE_3, CA_NEW_SCORE_4, CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, 
							CA_NEW_SCORE_9, CA_NEW_SCORE_10, CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT 
							where CA_TEST_DATE>='$temp_start' and CA_TEST_DATE<='$temp_end'
							order by CA_CODE ";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$CA_CODE = trim($data[CA_CODE]);
		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$CA_NAME = trim($data[CA_NAME]);
		$CA_SURNAME = trim($data[CA_SURNAME]);
		if (trim($data[CA_MINISTRY_NAME])) $CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
		if (trim($data[CA_POSITION])) $CA_POSITION = trim($data[CA_POSITION]);
		if (trim($data[CA_POS_NO])) $CA_POS_NO = trim($data[CA_POS_NO]);
/*		
		if ($PER_ID) {
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_CODE = $data2[PN_CODE];
			$CA_NAME = trim($data2[PER_NAME]);
			$CA_SURNAME = trim($data2[PER_SURNAME]);
		}
*/
		$PN_NAME = "";
		if ($PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][code] = $CA_CODE;
		$arr_content[$data_count][ca_name] = "$PN_NAME$CA_NAME $CA_SURNAME";
		$arr_content[$data_count][remark] = "";
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$HEAD_01 = "รหัสบุคคล $CA_MINISTRY_NAME";
		$HEAD_02 = "ตำแหน่ง $CA_POSITION";
		$HEAD_03 = "ตำแหน่งเลขที่ $CA_POS_NO";

			// เริ่มตาราง
		$RTF->ln();			
		$RTF->set_table_font($font, 20);

		$RTF->open_line();			
		$RTF->cell($RTF->bold(1) . $RTF->underline(1) . ($NUMBER_DISPLAY==2)?convert2thaidigit($HEAD_01):$HEAD_01 . $RTF->bold(0) . $RTF->underline(0), "200", "center", "0");
		$RTF->close_line();

		$RTF->open_line();	
		$RTF->cell($RTF->bold(1) . ($NUMBER_DISPLAY==2)?convert2thaidigit($HEAD_02):$HEAD_02 . $RTF->bold(0), "200", "center", "0");
		$RTF->close_line();

		$RTF->open_line();	
		$RTF->cell($RTF->bold(1) . ($NUMBER_DISPLAY==2)?convert2thaidigit($HEAD_03):$HEAD_03 . $RTF->bold(0), "200", "center", "0");
		$RTF->close_line();

		// พิมพ์ head
		$RTF->ln();			
		$RTF->set_table_font($font, 20);

		$RTF->open_line();
		for($i = 0; $i < count($heading_text); $i++) {
			$style_in = "";
			$style_out = "";
			if (strpos(strtolower($heading_style[$i]),"b")!==false) {
				$style_in .= $RTF->bold(1);
				$style_out .= $RTF->bold(0);
			}
			if (strpos(strtolower($heading_style[$i]),"i")!==false) {
				$style_in .= $RTF->italic(1);
				$style_out .= $RTF->italic(0);
			}
			if (strpos(strtolower($heading_style[$i]),"u")!==false) {
				$style_in .= $RTF->underline(1);
				$style_out .= $RTF->underline(0);
			}
			$align = (strtolower($heading_align[$i])=="c" ? "center" : (strtolower($heading_align[$i])=="l" ? "left" : "right"));
			$border = (!$heading_border[$i] ? "" : $heading_border[$i]);
			$bgcolor = (!$heading_bgcolor[$i] ? 0 : $heading_bgcolor[$i]);
			$RTF->cell($style_in . ($NUMBER_DISPLAY==2)?convert2thaidigit($heading_text[$i]):$heading_text[$i] . $style_o, $heading_width[$i], $align, $bgcolor, $border);
		}
		$RTF->close_line();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CODE = $arr_content[$data_count][code];
			$CA_NAME = $arr_content[$data_count][ca_name];
			$REMARK = $arr_content[$data_count][remark];
		
			$arr_data = (array) null;
			$arr_data[] = $CODE;
			$arr_data[] = $CA_NAME;
			$arr_data[] = $REMARK;

//			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
//			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			
			$RTF->open_line();			
			for($i = 0; $i < count($heading_width); $i++) {
				$style_in = "";
				$style_out = "";
				if (strpos(strtolower($data_style[$i]),"b")!==false) {
					$style_in .= $RTF->bold(1);
					$style_out .= $RTF->bold(0);
				}
				if (strpos(strtolower($data_style[$i]),"i")!==false) {
					$style_in .= $RTF->italic(1);
					$style_out .= $RTF->italic(0);
				}
				if (strpos(strtolower($data_style[$i]),"u")!==false) {
					$style_in .= $RTF->underline(1);
					$style_out .= $RTF->underline(0);
				}
				$align = (strtolower($data_align[$i])=="c" ? "center" : (strtolower($data_align[$i])=="l" ? "left" : "right"));
				$border = (!$data_border[$i] ? "" : $data_border[$i]);
				$bgcolor = (!$data_bgcolor[$i] ? 0 : $data_bgcolor[$i]);
				$RTF->cell($style_in . ($NUMBER_DISPLAY==2)?convert2thaidigit($arr_data[$i]):$arr_data[$i] . $style_o, $heading_width[$i], $align, $bgcolor, $border);
			}
			$RTF->close_line();
		} // end for				
	}else{
		$RTF->ln();			
		$RTF->set_table_font($font, 20);
		
		$RTF->open_line();	
		$RTF->cell($RTF->bold(1) . "********** ไม่มีข้อมูล **********" . $RTF->bold(0), "200", "center", "0");
		$RTF->close_line();
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>