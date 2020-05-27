<?
//	header('Content-Type: text/html; charset=windows-874');
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time); 
	
	$fname= "rpt_kpi_form_bkk1_rtf.rtf";

	if (!$font) $font = "AngsanaUPC";

	$RTF = new RTF("a4", 750, 500, 500, 500);

//	$RTF->set_default_font($font, 14);

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
		$count_pic_sign=$db_dpis->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data = $db_dpis->get_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
		if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}

	//หาชื่อหน่วยงาน
	if($PV_CODE){
		$select_org_name = "PV_ENG_NAME";
		$find_org_name = "PV_CODE='$PV_CODE'";
	}else{
		$select_org_name = "ORG_ENG_NAME";
		$find_org_name = "ORG_ID=$DEPARTMENT_ID";
	}
	$cmd = " select $select_org_name from PER_ORG where $find_org_name";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ORG_ENG_NAME = trim($data[$select_org_name]);
	//echo $cmd;

	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW1, PER_ID_REVIEW2,
						SCORE_KPI, SUM_KPI, SCORE_COMPETENCE, SUM_COMPETENCE, SCORE_OTHER, SUM_OTHER, RESULT_COMMENT, 
						COMPETENCE_COMMENT, SALARY_RESULT, SALARY_REMARK1, SALARY_REMARK2, AGREE_REVIEW1, DIFF_REVIEW1, 
						AGREE_REVIEW2, DIFF_REVIEW2, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT
			   from 		PER_KPI_FORM
			   where 	KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
//echo $cmd;
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$KF_START_DATE = show_date_format($data[KF_START_DATE], 1);
	$KF_END_DATE = show_date_format($data[KF_END_DATE], 1);
	$KF_YEAR = substr($KF_END_DATE, 6, 4);

	$PER_ID = $data[PER_ID];
	$PER_CARDNO = trim($data[PER_CARDNO]);		
	$PER_ID_REVIEW = $data[PER_ID_REVIEW];
	$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
	$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
	
	$SCORE_KPI = $data[SCORE_KPI];
	$SUM_KPI = $data[SUM_KPI];
	$SCORE_COMPETENCE = $data[SCORE_COMPETENCE]; 
	$SUM_COMPETENCE = $data[SUM_COMPETENCE];
	$SCORE_OTHER = $data[SCORE_OTHER];
	$SUM_OTHER = $data[SUM_OTHER];
	$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
	$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
	$OTHER_WEIGHT = $data[OTHER_WEIGHT];
	
	$RESULT_COMMENT = $data[RESULT_COMMENT];
	$COMPETENCE_COMMENT = $data[COMPETENCE_COMMENT];
	$SALARY_RESULT = $data[SALARY_RESULT];
	$SALARY_REMARK1 = $data[SALARY_REMARK1];
	$SALARY_REMARK2 = $data[SALARY_REMARK2];
	$AGREE_REVIEW1 = $data[AGREE_REVIEW1];
	$DIFF_REVIEW1 = $data[DIFF_REVIEW1];
	$AGREE_REVIEW2 = $data[AGREE_REVIEW2];
	$DIFF_REVIEW2 = $data[DIFF_REVIEW2];

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);

	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
	
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
	
	$cmd = " select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_TYPE = trim($data[POSITION_TYPE]);
	$POSITION_LEVEL = trim($data[POSITION_LEVEL]);

	$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
					 from 		PER_POSITION a, PER_LINE b, PER_ORG c
					 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_NAME = trim($data[PL_NAME]);
	$ORG_NAME = trim($data[ORG_NAME]);
	$PT_CODE = trim($data[PT_CODE]);
	$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$PT_NAME = trim($data2[PT_NAME]);
	if($PER_ID_REVIEW){
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO , PER_CARDNO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE = trim($data[PN_CODE]);
		$REVIEW_PER_NAME = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
		$REVIEW_POS_ID = trim($data[POS_ID]);
		$REVIEW_POEM_ID = trim($data[POEM_ID]);
		$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
		$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_CARDNO_REVIEW = trim($data[PER_CARDNO]);
	
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$REVIEW_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_LEVEL_NAME = trim($data[LEVEL_NAME]);
		$REVIEW_POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
		$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REVIEW_PL_NAME = trim($data[PL_NAME]);
		$REVIEW_PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$REVIEW_PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$REVIEW_PT_NAME = trim($data2[PT_NAME]);
		$REVIEW_PL_NAME = trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME . $REVIEW_POSITION_LEVEL . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):$REVIEW_LEVEL_NAME;
		$REVIEW_PM_CODE = trim($data[PM_CODE]);
		if($REVIEW_PM_CODE){
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = $data[PM_NAME]." ($REVIEW_PL_NAME)"; 
		} // end if
	}
	
	$cmd = " select 	a.PG_ID, a.PG_SEQ, a.KPI_NAME, c.PFR_NAME, a.KPI_WEIGHT, a.PG_RESULT, a.PG_EVALUATE, a.KF_TYPE, 
						a.KPI_TARGET_LEVEL1, a.KPI_TARGET_LEVEL2, a.KPI_TARGET_LEVEL3, a.KPI_TARGET_LEVEL4, a.KPI_TARGET_LEVEL5,
						a.KPI_TARGET_LEVEL1_DESC, a.KPI_TARGET_LEVEL2_DESC, a.KPI_TARGET_LEVEL3_DESC, a.KPI_TARGET_LEVEL4_DESC, a.KPI_TARGET_LEVEL5_DESC
			   from 		PER_PERFORMANCE_GOALS a, PER_KPI b, PER_PERFORMANCE_REVIEW c
			   where 	a.KF_ID=$KF_ID and a.KPI_ID=b.KPI_ID(+) and b.PFR_ID=c.PFR_ID(+)
			   order by	a.KF_TYPE, a.PG_SEQ ";
	$db_dpis->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	while($data = $db_dpis->get_array()){
		$PG_ID++;
		$PG_SEQ = $data[PG_SEQ];
		$PFR_NAME = $data[PFR_NAME];
		$KPI_NAME = $data[KPI_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT];
		$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
		$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
		$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
		$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
		$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
		$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
		$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
		$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
		$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
		$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
		$PG_RESULT = $data[PG_RESULT];
		$PG_EVALUATE = $data[PG_EVALUATE];
		$KF_TYPE = $data[KF_TYPE];
		
		if ($KF_TYPE==1 && !$FLAG_1) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามแผนปฏิบัติราชการ";
			$FLAG_1 = 1;
			$PG_ID++;
		} elseif ($KF_TYPE==2 && !$FLAG_2) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก";
			$FLAG_2 = 1;
			$PG_ID++;
		} elseif ($KF_TYPE==3 && !$FLAG_3) {
			$ARR_KPI[$PG_ID] = "ตัวชี้วัดตามงานที่ได้รับมอบหมายพิเศษ";
			$FLAG_3 = 1;
			$PG_ID++;
		}
		$ARR_KPI[$PG_ID] = $KPI_NAME;
		$ARR_KPI_SEQ[$PG_ID] = $PG_SEQ;
		$ARR_KPI_PFR[$PG_ID] = $PFR_NAME;
		$ARR_KPI_WEIGHT[$PG_ID] = $KPI_WEIGHT;
		$ARR_KPI_TARGET1[$PG_ID] = $KPI_TARGET_LEVEL1;
		$ARR_KPI_TARGET2[$PG_ID] = $KPI_TARGET_LEVEL2;
		$ARR_KPI_TARGET3[$PG_ID] = $KPI_TARGET_LEVEL3;
		$ARR_KPI_TARGET4[$PG_ID] = $KPI_TARGET_LEVEL4;
		$ARR_KPI_TARGET5[$PG_ID] = $KPI_TARGET_LEVEL5;
		$ARR_KPI_TARGET1_DESC[$PG_ID] = $KPI_TARGET_LEVEL1_DESC;
		$ARR_KPI_TARGET2_DESC[$PG_ID] = $KPI_TARGET_LEVEL2_DESC;
		$ARR_KPI_TARGET3_DESC[$PG_ID] = $KPI_TARGET_LEVEL3_DESC;
		$ARR_KPI_TARGET4_DESC[$PG_ID] = $KPI_TARGET_LEVEL4_DESC;
		$ARR_KPI_TARGET5_DESC[$PG_ID] = $KPI_TARGET_LEVEL5_DESC;
		$ARR_KPI_RESULT[$PG_ID] = $PG_RESULT;
		$ARR_KPI_EVALUATE[$PG_ID] = $PG_EVALUATE;
		
//		$TOTAL_KPI_EVALUATE += $PG_EVALUATE;
		$TOTAL_KPI_EVALUATE += ($PG_EVALUATE * $KPI_WEIGHT);
		$TOTAL_KPI_WEIGHT += $KPI_WEIGHT;
	} // end while
	
	$COUNT_KPI = count($ARR_KPI);
	
	//สมรรถนะ
	$cmd = " select 	a.KC_ID, a.CP_CODE, b.CP_NAME, b.CP_MODEL, a.KC_EVALUATE, a.KC_WEIGHT, a.PC_TARGET_LEVEL
			   from 		PER_KPI_COMPETENCE a, PER_COMPETENCE b
			   where 	a.KF_ID=$KF_ID and a.CP_CODE=b.CP_CODE
			   order by 	a.CP_CODE ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$KC_ID = $data[KC_ID];
		$CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
		$CP_MODEL = $data[CP_MODEL];
		$KC_EVALUATE = $data[KC_EVALUATE];
		$KC_WEIGHT = $data[KC_WEIGHT];
		$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		
		$ARR_COMPETENCE[$KC_ID] = $CP_NAME.(($CP_MODEL==2)?" *":"");
		$ARR_COMPETENCE_TARGET[$KC_ID] = $PC_TARGET_LEVEL;
		$ARR_COMPETENCE_EVALUATE[$KC_ID] = $KC_EVALUATE;
		$ARR_COMPETENCE_WEIGHT[$KC_ID]=$KC_WEIGHT;
		//เพื่อแสดงคะแนนในส่วน แบบประเมินการสรุปสมรรถนะ
		$PC_SCORE[$KC_ID] = "";
 		if ($COMPETENCY_SCALE==1) {			//COMPETENCY_SCALE ??????????
			if($KC_EVALUATE > 0){	
				if($KC_EVALUATE >= $PC_TARGET_LEVEL) $PC_SCORE[$KC_ID] = 3;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $PC_SCORE[$KC_ID] = 2;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $PC_SCORE[$KC_ID] = 1;
				elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $PC_SCORE[$KC_ID] = 0;
				else $PC_SCORE[$KC_ID] = 0;
			} else $KC_EVALUATE = "";
 		} elseif ($COMPETENCY_SCALE==2) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE * $KC_WEIGHT / 100;
 		} elseif ($COMPETENCY_SCALE==3) {		
			$PC_SCORE[$KC_ID] = $KC_EVALUATE;
		}
		$TOTAL_PC_SCORE += $PC_SCORE[$KC_ID];
		//----------------------------------------------------
		
		if($KC_EVALUATE != ""){
			if($KC_EVALUATE >= $PC_TARGET_LEVEL) $ARR_COMPETENCE_COUNT[GE] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 1) $ARR_COMPETENCE_COUNT[L1] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 2) $ARR_COMPETENCE_COUNT[L2] += 1;
			elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) == 3) $ARR_COMPETENCE_COUNT[L3] += 1;
			else $ARR_COMPETENCE_COUNT[L3] += 1;
		} // end if
	} // end while
	
	$COUNT_COMPETENCE = count($ARR_COMPETENCE);

	$ARR_COMPETENCE_SCORE[GE] = $ARR_COMPETENCE_COUNT[GE] * 3;
	$ARR_COMPETENCE_SCORE[L1] = $ARR_COMPETENCE_COUNT[L1] * 2;
	$ARR_COMPETENCE_SCORE[L2] = $ARR_COMPETENCE_COUNT[L2] * 1;
	$ARR_COMPETENCE_SCORE[L3] = $ARR_COMPETENCE_COUNT[L3] * 0;
	
	$TOTAL_COMPETENCE_SCORE = "";
	if($ARR_COMPETENCE_COUNT[GE] || $ARR_COMPETENCE_COUNT[L1] || $ARR_COMPETENCE_COUNT[L2] || $ARR_COMPETENCE_COUNT[L3]) 
		$TOTAL_COMPETENCE_SCORE = array_sum($ARR_COMPETENCE_SCORE);
		
//	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($COUNT_KPI * 5)), 3), 2);
	$SHOW_SCORE_KPI = round(round(($SCORE_KPI / ($TOTAL_KPI_WEIGHT * 5)), 3), 2);
//	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / ($COUNT_COMPETENCE * 3)), 3), 2);
	$SHOW_SCORE_COMPETENCE = round(round(($SCORE_COMPETENCE / 5), 3), 2);
	$SHOW_SCORE_OTHER = $SCORE_OTHER;

	$SUM_TOTAL = $SUM_KPI + $SUM_COMPETENCE + $SUM_OTHER;
	
	// =============================== START GEN PDF ========================//

	$heading_width1[0] = "120";
	$heading_width1[1] = "30";
	$heading_width1[2] = "14";
	
	$heading_width2[0] = "120";
	$heading_width2[1] = "30";
	$heading_width2[2] = "30";
	$heading_width2[3] = "40";

	function print_header($header_select){
		global $RTF, $heading_width1, $heading_width2;
		global $font;
		
		if($header_select == 1){
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell("ตัวชี้วัดผลสัมฤทธิ์ของงาน", $heading_width1[0], "center", "25", "TRL");
			$RTF->cell("น้ำหนัก", $heading_width1[1], "center", "25", "TRL");
			$RTF->cell("เป้าหมาย", ($heading_width1[2]*5), "center", "25", "TRL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("", $heading_width1[0], "center", "25", "RBL");
			$RTF->cell("(ระดับความสำคัญ)", $heading_width1[1], "center", "25", "RBL");
			for($i=1; $i<=5; $i++){ 
				$RTF->cell(convert2thaidigit($i), $heading_width1[2], "center", "25", "TRBL");
			} // end for
			$RTF->close_line();
		}elseif($header_select == 2){	//แบบสรุปการประเมินผลสัมฤทธิ์ของงาน 
			$RTF->open_line();			
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			$RTF->cell("สมรรถนะหลัก", $heading_width2[0], "center", "25", "TRL");
			$RTF->cell("น้ำหนัก", $heading_width2[1], "center", "25", "TRL");
			$RTF->cell("ระดับที่ต้องการ", $heading_width2[2], "center", "25", "TRL");
			$RTF->cell("หมายเหตุ", $heading_width2[3], "center", "25", "TRL");
			$RTF->close_line();
			$RTF->open_line();
			$RTF->cell("", $heading_width2[0], "center", "25", "RBL");
			$RTF->cell("(ระดับความสำคัญ)", $heading_width2[1], "center", "25", "RBL");
			$RTF->cell("", $heading_width2[2], "center", "25", "RBL");
			$RTF->cell("", $heading_width2[3], "center", "25", "RBL");
			$RTF->close_line();
		}	// end if
	} // function		

	$RTF->set_table_font($font, 14);
	$RTF->color("0");	// 0=BLACK
	
	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."แบบบันทึกข้อตกลงการปฏิบัติราชการรายบุคคลของข้าราชการกรุงเทพมหานครสามัญ", "220", "center", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ประจำปีงบประมาณ พ.ศ. ".convert2thaidigit("$KF_YEAR"), "220", "center", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ระยะการประเมิน", "120", "right", "0");
	if($KF_CYCLE==1) $RTF->cellImage("../images/checkbox_check.jpg", 100, 1, "right", 0);
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 1, "right", 0);
	$RTF->cell(convert2thaidigit("ระยะที่ ๑ ตั้งแต่ ๑ ตุลาคม ".($KF_YEAR - 1)." ถึง ๓๑ มีนาคม ".$KF_YEAR), "100", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell("", "120", "right", "0");
	if($KF_CYCLE==2) $RTF->cellImage("../images/checkbox_check.jpg", 100, 1, "right", 0);
	else $RTF->cellImage("../images/checkbox_blank.jpg", 100, 1, "right", 0);
	$RTF->cell($RTF->color("0").convert2thaidigit("ระยะที่ ๒ ตั้งแต่ ๑ เมษายน ".$KF_YEAR." ถึง ๓๐ กันยายน ".$KF_YEAR), "100", "left", "0");
	$RTF->close_line();

	$RTF->ln();			

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล (ผู้ทำข้อตกลง)  $PER_NAME  ", "110", "left", "0");
	$RTF->cell($RTF->color("0")."ตำแหน่ง $PL_NAME  ", "110", "left", "0");
	$RTF->close_line();

	$RTF->open_line();
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ประเภทตำแหน่ง  $POSITION_TYPE  ระดับตำแหน่ง  $POSITION_LEVEL", "110", "left", "0");
	$RTF->cell($RTF->color("0")."สังกัด  ".convert2thaidigit("$ORG_NAME  ").convert2thaidigit("$DEPARTMENT_NAME"), "110", "left", "0");
	$RTF->close_line();

	$RTF->open_line();			
	$RTF->set_font($font,16);
	$RTF->cell($RTF->color("0")."ชื่อ - นามสกุล (ผู้รับข้อตกลง)  $REVIEW_PER_NAME", "110", "left", "0");
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME", "110", "left", "0");
	$RTF->close_line();

	$RTF->ln();

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell($RTF->color("0")." ๑. ผลสัมฤทธิ์ของงาน", "220", "left", "16", "TRBL");
	$RTF->close_line();
	print_header(1);
	
	$data_count = 0;
	foreach($ARR_KPI as $PG_ID => $KPI_NAME){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("0");	// 0=BLACK
		$RTF->cell(convert2thaidigit($KPI_NAME), $heading_width1[0], "left", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_KPI_WEIGHT[$PG_ID]), $heading_width1[1], "center", "0", "TRBL");
		for($i=1; $i<=5; $i++) { 
			$RTF->cell(convert2thaidigit(${"ARR_KPI_TARGET".$i."_DESC"}[$PG_ID]), $heading_width1[2], "center", "0", "TRBL");
		}
		$RTF->close_line();

	} // end foreach
//	echo "$data_count - ".count($ARR_KPI);

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("รวม ", ($heading_width1[0]), "center", "0", "TRBL");
	$RTF->cell(convert2thaidigit($TOTAL_KPI_WEIGHT), $heading_width1[1], "center", "0", "TRBL");
		for($i=1; $i<=5; $i++) { 
			$RTF->cell("", $heading_width1[2], "center", "0", "TRBL");
		}
	$RTF->close_line();

	$RTF->ln();			

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell($RTF->color("0")." ๒. สมรรถนะหลัก", "220", "left", "16", "TRBL");
	$RTF->close_line();
	print_header(2);
	
	$data_count = 0;
	foreach($ARR_COMPETENCE as $KC_ID => $CP_NAME){
		$data_count++;

		$RTF->open_line();
		$RTF->set_font($font,14);
		$RTF->color("1");	// 1=DARKGRAY
		$RTF->cell(convert2thaidigit($CP_NAME), $heading_width2[0], "left", "0", "TRBL");
		if ($ARR_COMPETENCE_WEIGHT[$KC_ID])
			$RTF->cell(convert2thaidigit($ARR_COMPETENCE_WEIGHT[$KC_ID]), $heading_width2[1], "center", "0", "TRBL");
		else
			$RTF->cell("", $heading_width2[1], "center", "0", "TRBL");
		$RTF->cell(convert2thaidigit($ARR_COMPETENCE_TARGET[$KC_ID]), $heading_width2[2], "center", "0", "TRBL");
		$RTF->cell("", $heading_width2[3], "center", "0", "TRBL");
		$RTF->close_line();
	} // end foreach
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell("รวม ", $heading_width2[0], "center", "0", "TRBL");
	if (array_sum($ARR_COMPETENCE_WEIGHT))
		$RTF->cell(convert2thaidigit(array_sum($ARR_COMPETENCE_WEIGHT)), $heading_width2[1], "center", "0", "TRBL");
	else
		$RTF->cell("", $heading_width2[1], "center", "0", "TRBL");
	$RTF->cell("", $heading_width2[2], "center", "0", "TRBL");
	$RTF->cell("", $heading_width2[3], "center", "0", "TRBL");
	$RTF->close_line();

	$RTF->ln();			

	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->cell($RTF->color("0")." ๓. ลงชื่อรับทราบข้อตกลงการปฏิบัติราชการ", "220", "left", "16", "TRBL");
	$RTF->close_line();
	
	$RTF->open_line();
	$RTF->set_font($font,14);
	$RTF->color("1");	// 1=DARKGRAY
	$RTF->cell($RTF->color("0")."ลงชื่อ................................................................(ผู้ทำข้อตกลง)", "110", "left", "0", "TRL");
	$RTF->cell($RTF->color("0")."ลงชื่อ................................................................(ผู้รับข้อตกลง)", "110", "left", "0", "TRL");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."     ($PER_NAME)", "110", "left", "0", "RL");
	$RTF->cell($RTF->color("0")."     ($REVIEW_PER_NAME)", "110", "left", "0", "RL");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $PL_NAME", "110", "left", "0", "RL");
	$RTF->cell($RTF->color("0")."ตำแหน่ง  $REVIEW_PL_NAME", "110", "left", "0", "RL");
	$RTF->close_line();
	$RTF->open_line();
	$RTF->cell($RTF->color("0")."     วันที่......................................................", "110", "left", "0", "RBL");
	$RTF->cell($RTF->color("0")."     วันที่......................................................", "110", "left", "0", "RBL");
	$RTF->close_line();
	
	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>