<?
	if (!trim($SELECTED_PER_ID)) $SELECTED_PER_ID = $PER_ID;
	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	require("../../RTF/rtf_class.php");
	
	$a_set_of_colors = (array) null;
	$color_buff = explode("^",$xlsFmtTitle_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTitle_col_idx = 1;
	$color_buff = explode("^",$xlsFmtTitle_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTitle_bgcol_idx = 2;
	$color_buff = explode("^",$xlsFmtSubTitle_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtSubTitle_col_idx = 3;
	$color_buff = explode("^",$xlsFmtSubTitle_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtSubTitle_bgcol_idx = 4;
	$color_buff = explode("^",$xlsFmtTableHeader_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableHeader_col_idx = 5;
	$color_buff = explode("^",$xlsFmtTableHeader_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableHeader_bgcol_idx = 6;
	$color_buff = explode("^",$xlsFmtTableDetail_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableDetail_col_idx = 7;
	$color_buff = explode("^",$xlsFmtTableDetail_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableDetail_bgcol_idx = 8;

//	echo "xlsFmtTitle_color=$xlsFmtTitle_color, xlsFmtTitle_bgcolor=$xlsFmtTitle_bgcolor, xlsFmtSubTitle_color=$xlsFmtSubTitle_color, xlsFmtSubTitle_bgcolor=$xlsFmtSubTitle_bgcolor, xlsFmtTableHeader_color=$xlsFmtTableHeader_color, xlsFmtTableHeader_bgcolor=$xlsFmtTableHeader_bgcolor, xlsFmtTableDetail_color=$xlsFmtTableDetail_color, xlsFmtTableDetail_bgcolor=$xlsFmtTableDetail_bgcolor<br>";

	$set_of_colors = implode("^",$a_set_of_colors);

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
//	$BKK_FLAG = 1;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	// sort หัวข้อที่เลือก เพื่อจัดลำดับการแสดงก่อนหลัง ของ $arr_history_name ที่เลือกเข้ามา
	$arr_history_sort = array("DECORATEHIS", "ABSENTSUM", "ABILITY", "SERVICEHIS", "SPECIALSKILLHIS", "EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS", "KPI"); 
	// sort หัวข้อที่เลือก
	
	if (!$HISTORY_LIST) {
		$arr_history_name = $arr_history_sort;
	} else {
		$arr_history_name = explode("|", $HISTORY_LIST);
	}
	$search_condition = "";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if

	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			$arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
		
	if($list_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no))  {	
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO) = '$search_pos_no')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO) = '$search_pos_no')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO) = '$search_pos_no') or (trim(POEM_NO) = '$search_pos_no') or (trim(POEMS_NO) = '$search_pos_no') or (trim(POT_NO) = '$search_pos_no')) ";
		}
		if(trim($search_pos_no_name)){
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO_NAME) = '$search_pos_no_name')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO_NAME) = '$search_pos_no_name')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO_NAME) = '$search_pos_no_name') or (trim(POEM_NO_NAME) = '$search_pos_no_name') or (trim(POEMS_NO_NAME) = '$search_pos_no_name') or (trim(POT_NO_NAME) = '$search_pos_no_name')) ";
		}
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$fname= "rpt_R0040063_bkk_rtf.rtf";

//	echo "font=$font<br>";
	if (!$font) $font = "AngsanaUPC";
	
//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".3in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
	
	$page_ch = 98;
	
	$RTF->set_table_font($font, 14);
	$RTF->color("0");	// 0=BLACK
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$heading_text[NAME] = array("","","");
	$heading_width[NAME][0] = "32";
	$heading_width[NAME][1] = "34";
	$heading_width[NAME][2] = "32";
	$column_function[NAME][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[NAME][1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[NAME][2] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_align[NAME] = array('C', 'C', 'C');
	$heading_border[NAME] = array('TLBR', 'TLBR', 'TLBR');

	$heading_width[EDUCATE][0] = "20";		// "40";
	$heading_width[EDUCATE][1] = "10";		// "20";
	$heading_width[EDUCATE][2] = "17";		// "35";
	$heading_width[EDUCATE][3] = "17";		// "35";
	$heading_width[EDUCATE][4] = "12";		// "25";
	$heading_width[EDUCATE][5] = "22";		// "45";
		
	$heading_text[EDUCATE][0] = "สถานศึกษา";				$column_function[EDUCATE][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][1] = "ตั้งแต่ - ถึง";				$column_function[EDUCATE][1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[EDUCATE][2] = "วุฒิที่ได้รับ/สาขา";	$column_function[EDUCATE][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][3] = "สถานที่";					$column_function[EDUCATE][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][4] = "ตั้งแต่ - ถึง";				$column_function[EDUCATE][4] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[EDUCATE][5] = "หลักสูตร/รุ่นที่";		$column_function[EDUCATE][5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[EDUCATE] = array('C', 'C', 'C', 'C', 'C', 'C');
	$heading_border[EDUCATE] = array('TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR');

	// หัวของกรณี $BKK_FLAG==1
	$heading_bkk1_width[EDUCATE][0] = "40";
	$heading_bkk1_width[EDUCATE][1] = "24";
	$heading_bkk1_width[EDUCATE][2] = "34";
	$heading_bkk2_width[EDUCATE][0] = "34";
	$heading_bkk2_width[EDUCATE][1] = "24";
	$heading_bkk2_width[EDUCATE][2] = "40";
	
	$heading_bkk1_text[EDUCATE][0] = "สถานศึกษา";			$column_function_bkk1[EDUCATE][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_bkk1_text[EDUCATE][1] = "ตั้งแต่ - ถึง";				$column_function_bkk1[EDUCATE][1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_bkk1_text[EDUCATE][2] = "วุฒิที่ได้รับ/สาขา";	$column_function_bkk1[EDUCATE][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_bkk2_text[EDUCATE][0] = "สถานที่";					$column_function_bkk2[EDUCATE][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_bkk2_text[EDUCATE][1] = "ตั้งแต่ - ถึง";				$column_function_bkk2[EDUCATE][1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_bkk2_text[EDUCATE][2] = "หลักสูตร/รุ่นที่";		$column_function_bkk2[EDUCATE][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_bkk1_align[EDUCATE] = array('C', 'C', 'C');
	$heading_bkk2_align[EDUCATE] = array('C', 'C', 'C');
	$heading_bkk1_border[EDUCATE] = array('TLBR', 'TLBR', 'TLBR');
	$heading_bkk2_border[EDUCATE] = array('TLBR', 'TLBR', 'TLBR');
	// end หัวของกรณี $BKK_FLAG==1

	if ($BKK_FLAG==1) {
		$heading_width[POSITIONHIS][0] = "15";
		$heading_width[POSITIONHIS][1] = "44";      
		$heading_width[POSITIONHIS][2] = "8";
		$heading_width[POSITIONHIS][3] = "8";
		$heading_width[POSITIONHIS][4] = "8";
		$heading_width[POSITIONHIS][5] = "15";
		$date_format = 2;
	} else {
		$heading_width[POSITIONHIS][0] = "44";
		$heading_width[POSITIONHIS][1] = "15";      
		$heading_width[POSITIONHIS][2] = "8";
		$heading_width[POSITIONHIS][3] = "8";
		$heading_width[POSITIONHIS][4] = "8";
		$heading_width[POSITIONHIS][5] = "15";
		$date_format = 3;
	}

	$heading_text[POSITIONHIS][0] = "วัน เดือน ปี";											$column_function[POSITIONHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][1] = "ตำแหน่ง";												$column_function[POSITIONHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][2] = "เลขที่ตำแหน่ง";										$column_function[POSITIONHIS][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][3] = "ระดับ";													$column_function[POSITIONHIS][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][4] = "อัตราเงินเดือน";										$column_function[POSITIONHIS][4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[POSITIONHIS][5] = "เอกสารอ้างอิง $SESS_USERID";	$column_function[POSITIONHIS][5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			
	$heading_align[POSITIONHIS] = array('C', 'C', 'C', 'C', 'C', 'C');
	$heading_border[POSITIONHIS][0] = array('TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR');
	$heading_border[POSITIONHIS][1] = array('LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR');

	$heading_width[PUNISHMENT][0] = "22";
	$heading_width[PUNISHMENT][1] = "55";
	$heading_width[PUNISHMENT][2] = "21";

	$heading_text[PUNISHMENT][0] = "พ.ศ.";					$column_function[PUNISHMENT][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[PUNISHMENT][1] = "รายการ";				$column_function[PUNISHMENT][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[PUNISHMENT][2] = "เอกสารอ้างอิง";		$column_function[PUNISHMENT][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	$heading_align[PUNISHMENT] = array('C', 'C', 'C');
	$heading_border[PUNISHMENT] = array('TLBR', 'TLBR', 'TLBR');
	
	$heading_width[NOTPAID][0] = "22";
	$heading_width[NOTPAID][1] = "55";
	$heading_width[NOTPAID][2] = "21";
	
	$heading_text[NOTPAID][0] = "ตั้งแต่-ถึง";			$column_function[NOTPAID][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[NOTPAID][1] = "รายการ";			$column_function[NOTPAID][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[NOTPAID][2] = "เอกสารอ้างอิง";	$column_function[NOTPAID][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[NOTPAID] = array('C', 'C', 'C');
	$heading_border[NOTPAID] = array('TLBR', 'TLBR', 'TLBR');

	$heading_width[ABILITY][0] = "49";
	$heading_width[ABILITY][1] = "49";
	
	$heading_text[ABILITY][0] = "ด้านความสามารถพิเศษ";	$column_function[ABILITY][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[ABILITY][1] = "ความสามารถพิเศษ";			$column_function[ABILITY][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	$heading_align[ABILITY] = array('C', 'C');
	$heading_border[ABILITY] = array('TLBR', 'TLBR');
	
	$heading_width[SERVICEHIS][0] = "10";
	$heading_width[SERVICEHIS][1] = "39";
	$heading_width[SERVICEHIS][2] = "10";
	$heading_width[SERVICEHIS][3] = "39";

	$heading_text[SERVICEHIS][0] = "พ.ศ.";									$column_function[SERVICEHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[SERVICEHIS][1] = "รายการ";								$column_function[SERVICEHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[SERVICEHIS][2] = "พ.ศ.";									$column_function[SERVICEHIS][2] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[SERVICEHIS][3] = "รายการ";								$column_function[SERVICEHIS][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
//	$heading_text[SERVICEHIS][1] = "ประเภทราชการพิเศษ";
//	$heading_text[SERVICEHIS][2] = "หัวข้อราชการพิเศษ";
//	$heading_text[SERVICEHIS][3] = "หนังสือ";

	$heading_align[SERVICEHIS] = array('C', 'C', 'C', 'C');
	$heading_border[SERVICEHIS] = array('TLBR', 'TLBR', 'TLBR', 'TLBR');

	$heading_width[SPECIALSKILLHIS][0] = "40";
	$heading_width[SPECIALSKILLHIS][1] = "58";

	$heading_text[SPECIALSKILLHIS][0] = "ด้านความเชี่ยวชาญพิเศษ";			$column_function[SPECIALSKILLHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[SPECIALSKILLHIS][1] = "เน้นทาง";									$column_function[SPECIALSKILLHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[SPECIALSKILLHIS] = array('C', 'C');
	$heading_border[SPECIALSKILLHIS] = array('TLBR', 'TLBR');

	$heading_width[DECORATEHIS][0] = "24";
	$heading_width[DECORATEHIS][1] = "22";
	$heading_width[DECORATEHIS][2] = "15";
	$heading_width[DECORATEHIS][3] = "22";
	$heading_width[DECORATEHIS][4] = "15";

	$heading_text[DECORATEHIS][0] = "(1)|ตำแหน่ง (อดีต-ปัจจุบัน|เฉพาะปีที่ได้รับ|พระราชทานเครื่องราชฯ)";				$column_function[DECORATEHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[DECORATEHIS][1] = "(2)|ลำดับเครื่องราชฯ ที่ได้รับ|พระราชทานแล้ว|จากชั้นรองไปชั้นสูงตาม (1)";		$column_function[DECORATEHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[DECORATEHIS][2] = "(3)|ว.ด.ป.|ที่ได้รับ|พระราชทานตาม (2)";															$column_function[DECORATEHIS][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[DECORATEHIS][3] = "(4)|เครื่องราชฯ|ตาม (2)|รับมอบเมื่อ ";																$column_function[DECORATEHIS][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[DECORATEHIS][4] = "(5)|เครื่องราชฯ|ตาม (2)|ส่งคืนเมื่อ ";																	$column_function[DECORATEHIS][4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[DECORATEHIS] = array('C', 'C', 'C', 'C', 'C');
	$heading_border[DECORATEHIS][0] = array('TLR', 'TLR', 'TLR', 'TLR', 'TLR');
	$heading_border[DECORATEHIS][1] = array('LR', 'LR', 'LR', 'LR', 'LR');
	$heading_border[DECORATEHIS][2] = array('LR', 'LR', 'LR', 'LR', 'LR');
	$heading_border[DECORATEHIS][3] = array('LBR', 'LBR', 'LBR', 'LBR', 'LBR');

	$heading_width[ABSENTSUM][0] = "14";
	$heading_width[ABSENTSUM][1] = "6";
	$heading_width[ABSENTSUM][2] = "6";
	$heading_width[ABSENTSUM][3] = "6";
	$heading_width[ABSENTSUM][4] = "6";
	$heading_width[ABSENTSUM][5] = "6";
	$heading_width[ABSENTSUM][6] = "6";
	$heading_width[ABSENTSUM][7] = "6";
	$heading_width[ABSENTSUM][8] = "6";
	$heading_width[ABSENTSUM][9] = "6";
	$heading_width[ABSENTSUM][10] = "6";
	$heading_width[ABSENTSUM][11] = "6";
	$heading_width[ABSENTSUM][12] = "6";
	$heading_width[ABSENTSUM][13] = "6";
	$heading_width[ABSENTSUM][14] = "6";

	$heading_text[ABSENTSUM][0] = "พ.ศ.";								$column_function[ABSENTSUM][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[ABSENTSUM][1] = "ลาป่วย";							$column_function[ABSENTSUM][1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][2] = "ลากิจ";								$column_function[ABSENTSUM][2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][3] = "ลาพักผ่อน";						$column_function[ABSENTSUM][3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][4] = "สาย";								$column_function[ABSENTSUM][4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][5] = "ขาดราชการ";					$column_function[ABSENTSUM][5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][6] = "ลาศึกษาต่อ";						$column_function[ABSENTSUM][6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][7] = "ลาคลอดบุตร";					$column_function[ABSENTSUM][7] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$heading_text[ABSENTSUM][8] = "ลาอุปสมบท";					$column_function[ABSENTSUM][8] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][9] = "ลาเข้ารับการตรวจเลือก";	$column_function[ABSENTSUM][9] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][10] = "ลาไปปฏิบัติงาน";			$column_function[ABSENTSUM][10] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][11] = "ลาติดตามคู่สมรส";			$column_function[ABSENTSUM][11] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][12] = "ลากิจเลี้ยงดูบุตร";			$column_function[ABSENTSUM][12] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][13] = "ลาไปช่วยเหลือภริยา";		$column_function[ABSENTSUM][13] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	$heading_text[ABSENTSUM][14] = "ฟื้นฟูสมรรถภาพ";			$column_function[ABSENTSUM][14] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	
	$heading_align[ABSENTSUM] = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
	$heading_border[ABSENTSUM] = array('TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR', 'TLBR');
//	$heading_border[ABSENTSUM][0] = array('TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR', 'TLR');
//	$heading_border[ABSENTSUM][1] = array('LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR');
//	$heading_border[ABSENTSUM][2] = array('LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR');

	if ($BKK_FLAG==1) {
		$heading_width[KPI][0] = "14";
		$heading_width[KPI][1] = "14";
		$heading_width[KPI][2] = "14";
		$heading_width[KPI][3] = "14";
		$heading_width[KPI][4] = "14";
		$heading_width[KPI][5] = "14";
		$heading_width[KPI][6] = "14";

		$heading_text[KPI][0] = "<**0**>ปีงบประมาณ|<**0^**>";																								$column_function[KPI][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$heading_text[KPI][1] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ดีเด่น\par 90 - 100%";						$column_function[KPI][1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][2] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|เป็นที่ยอมรับได้\par 60 - 89%";		$column_function[KPI][2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][3] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ต้องปรับปรุง\par ต่ำกว่า 60%";			$column_function[KPI][3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][4] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ดีเด่น\par 90 - 100%";										$column_function[KPI][4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][5] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|เป็นที่ยอมรับได้\par 60 - 89%";							$column_function[KPI][5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][6] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ต้องปรับปรุง\par ต่ำกว่า 60%";							$column_function[KPI][6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	
		$heading_align[KPI] = array('C', 'C', 'C', 'C', 'C', 'C', 'C');
		$heading_border[KPI][0] = array('TLR', 'TLB', 'TB', 'TB', 'TLB', 'TB', 'TBR');
		$heading_border[KPI][1] = array('LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR');
		$heading_border[KPI][2] = array('LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR');
	} else {
		$heading_width[KPI][0] = "9";
		$heading_width[KPI][1] = "9";
		$heading_width[KPI][2] = "9";
		$heading_width[KPI][3] = "9";
		$heading_width[KPI][4] = "9";
		$heading_width[KPI][5] = "9";
		$heading_width[KPI][6] = "9";
		$heading_width[KPI][7] = "9";
		$heading_width[KPI][8] = "9";
		$heading_width[KPI][9] = "9";
		$heading_width[KPI][10] = "8";

		$heading_text[KPI][0] = "ปี|งบประมาณ|";																										$column_function[KPI][0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$heading_text[KPI][1] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ดีเด่น";						$column_function[KPI][1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][2] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ดีมาก";					$column_function[KPI][2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][3] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ดี";							$column_function[KPI][3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][4] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|พอใช้";					$column_function[KPI][4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][5] = "<**1**>ผลการประเมินครั้งที่ 1 (1 ต.ค. - 31 มี.ค. ของปีถัดไป)|ต้องปรับปรุง";			$column_function[KPI][5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][6] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ดีเด่น";										$column_function[KPI][6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][7] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ดีมาก";										$column_function[KPI][7] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][8] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ดี";											$column_function[KPI][8] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][9] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|พอใช้";										$column_function[KPI][9] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
		$heading_text[KPI][10] = "<**2**>ผลการประเมินครั้งที่ 2 (1 เม.ย. - 30 ก.ย.)|ต้องปรับปรุง";							$column_function[KPI][10] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
	
		$heading_align[KPI] = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
		$heading_border[KPI][0] = array('TLR', 'TLB', 'TB', 'TB', 'TB', 'TBR', 'TLB', 'TB', 'TB', 'TB', 'TBR');
		$heading_border[KPI][1] = array('LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR', 'LR');
		$heading_border[KPI][2] = array('LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR', 'LBR');
	}

	function run_col_format($HISTORY_NAME){
		global $RTF, $SESS_USERID, $COLUMN_FORMAT;
		global $heading_width, $heading_text, $heading_align, $heading_border, $column_function;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;

		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
		$buff = explode("|",$COLUMN_FORMAT);
		$arr_column_map = explode(",",$buff[0]);
		$arr_column_sel = explode(",",$buff[1]);
		$arr_column_width = explode(",",$buff[2]);
		$arr_column_align = explode(",",$buff[3]);
	} // function
	
	function print_header($HISTORY_NAME){
		global $RTF, $SESS_USERID;
		global $heading_width, $heading_text, $heading_align, $heading_border, $column_function;
//		global $arr_column_map, $arr_column_sel, $arr_column_align;

		$head_text = (array) null;
		$max_row = 0;
		for($col=0; $col < count($heading_width[$HISTORY_NAME]); $col++) {
			$buff = explode("|",$heading_text[$HISTORY_NAME][$col]);
			$cnt_row = count($buff);
			if ($cnt_row > $max_row) $max_row=$cnt_row;
			for($row=0; $row < count($buff); $row++) {
				$head_text[$row][$col] = $buff[$row];
			}
		}
		
		for($row=0; $row < $max_row; $row++) {
//			echo "*** text=".implode(",",$head_text[$row])." ***";
			$COLUMN_FORMAT = do_COLUMN_FORMAT($head_text[$row], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
			$buff = explode("|",$COLUMN_FORMAT);
			$arr_column_map = explode(",",$buff[0]);
			$arr_column_sel = explode(",",$buff[1]);
			$arr_column_width = explode(",",$buff[2]);
			$arr_column_align = explode(",",$buff[3]);

//			echo "head_text=".implode(",",$head_text[$row]).", COLUMN_FORMAT=$COLUMN_FORMAT";
			if ($max_row > 1)
				$a_border = $heading_border[$HISTORY_NAME][$row];
			else if ($max_row == 1)
				$a_border = $heading_border[$HISTORY_NAME];

			$RTF->open_line();
			$RTF->set_font($font, 14);
			$RTF->color("0");	// 0='BLACK'
			
			$pgrp="";

//			echo "HISTORY_NAME=$HISTORY_NAME, count Head=".count($heading_width[$HISTORY_NAME])."<br>";
			for($col=0; $col < count($heading_width[$HISTORY_NAME]); $col++) {
//				echo "colmap $i=".$arr_column_map[$col]."..select=".$arr_column_sel[$arr_column_map[$col]]."<br>";
				if ($arr_column_sel[$arr_column_map[$col]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
					$buff = explode("|",doo_merge_cell($head_text[$row][$arr_column_map[$col]], $a_border[$col], "0", $pgrp, ($col == count($heading_width[$HISTORY_NAME])-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo ".....................$ndata, $border<br>";
//					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$a = ($heading_align[$row][$arr_column_map[$col]]="C" ? "center" : ($heading_align[$row][$arr_column_map[$col]]="R" ? "right" : "left"));
					$RTF->cell($RTF->bold(1).$ndata.$RTF->bold(0), $heading_width[$HISTORY_NAME][$arr_column_map[$col]], $a, "25", $border);	
				}
			}
			$RTF->close_line();
		}
	} // function

	function print_footer($LEVEL_NAME) {
		global $RTF, $heading_width, $max_y, $FULLNAME, $FULL_LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME, $page_no;

/*		$prt_text_footer = "ชื่อ ".$FULLNAME."..".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME;
		$RTF->MyRTF .= "\sectd \linemod0 {\footnote \pard";
		$tab_align = "center";
		$RTF->open_line($tab_align, false, false);
		$RTF->set_font($font, 10);
		$RTF->color("$fmtTitle_col_idx");	// 0='BLACK'
		$RTF->cell($RTF->bold(1).$prt_text_footer.$RTF->bold(0), 90, "left", "$fmtTitle_bgcol_idx", "TBL");	
		$RTF->cell($RTF->bold(1)."หน้า [$page_no]".$RTF->bold(0), 8, "right", "$fmtTitle_bgcol_idx", "TRB");
		$RTF->close_line();
		$RTF->MyRTF .= "} \par\n";	
*/
		$name = "ชื่อ ".$FULLNAME."..".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME;
		$prt_text_footer = "$name^^10^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^l^||หน้า [#page_no#]^^10^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^r^";
		//	$RTF->add_footer($text, $bottomUp=720, $brdSurround=false)
		$RTF->add_footer($prt_text_footer, "1", false);

	} // function footer

	if ($BKK_FLAG==1) {
		if($DPISDB=="odbc"){
			$order_str = "order by		c.POS_NO_NAME, CLng(c.POS_NO), d.POEM_NO_NAME, CLng(d.POEM_NO), 
															e.POEMS_NO_NAME, CLng(e.POEMS_NO), g.POT_NO_NAME, CLng(g.POT_NO) ";
		}elseif($DPISDB=="oci8"){
			$order_str = "order by		c.POS_NO_NAME, to_number(replace(c.POS_NO,'-','')), d.POEM_NO_NAME, to_number(replace(d.POEM_NO,'-','')), 
															e.POEMS_NO_NAME, to_number(replace(e.POEMS_NO,'-','')), g.POT_NO_NAME, to_number(replace(g.POT_NO,'-','')) ";
		}elseif($DPISDB=="mysql"){
			$order_str = "order by		c.POS_NO_NAME, c.POS_NO+0, d.POEM_NO_NAME, d.POEM_NO+0, e.POEMS_NO_NAME, e.POEMS_NO+0, g.POT_NO_NAME, g.POT_NO+0 ";
		}
	} else {
		if($DPISDB=="oci8"){
			$order_str = "order by		NLSSORT(PER_NAME,'NLS_SORT=THAI_DICTIONARY')a.PER_NAME, NLSSORT(PER_NAME,'NLS_SORT=THAI_DICTIONARY')a.PER_SURNAME ";
		}else{
			$order_str = "order by		a.PER_NAME, a.PER_SURNAME ";
		}
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
				 		from		PER_PRENAME b inner join 
				 				(	
									(
										(
											( 	
												(
													PER_PERSONAL a 
													left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
												) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
										) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)	
									) on (trim(a.PN_CODE)=trim(b.PN_CODE))
						$search_condition
				 		$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.DEPARTMENT_ID,
											a.OT_CODE, a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE, a.PER_ADD1, a.PER_ADD2, 
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		$order_str ";
	}
	//echo "$cmd<\br>";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$ARRAY_POH_SAH = (array) null;
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PER_OFFNO = $data[PER_OFFNO];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO_NAME = trim($data[POS_NO_NAME]);
				if (substr($POS_NO_NAME,0,4)=="กปด.")
					$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
				else
					$POS_NO = $POS_NO_NAME.$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			}elseif($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[TEMP_POS_NO_NAME].$data[TEMP_POS_NO];
				$PL_CODE = trim($data[TEMP_PL_CODE]);
				$ORG_ID = $data[TEMP_ORG_ID];
				$ORG_ID_1 = $data[TEMP_ORG_ID_1];
				$ORG_ID_2 = $data[TEMP_ORG_ID_2];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 

			// ข้อมูลประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $TMP_DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
			$ORG_ID_REF = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			if ($ORG_NAME=="-") $ORG_NAME = "";
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);
			$PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

			$PER_ADD1 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
			$DT_CODE_ADR = trim($data2[DT_CODE]);
			$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$DT_NAME_ADR = trim($data3[DT_NAME]);
			if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
			
			$AP_CODE_ADR = trim($data2[AP_CODE]);
			$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$AP_NAME_ADR = trim($data3[AP_NAME]);
			
			$PV_CODE_ADR = trim($data2[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			if($data2[ADR_VILLAGE]) $PER_ADD1 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD1 .= "อาคาร".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD1 .= "เลขที่ ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD1 .= "ม.".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD1 .= "ซ.".str_replace("ซ.","",str_replace("ซอย","",$data2[ADR_SOI]))." ";
			if($data2[ADR_ROAD]) $PER_ADD1 .= "ถ.".str_replace("ถ.","",str_replace("ถนน","",$data2[ADR_ROAD]))." ";
			if($DT_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= "แขวง".$DT_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "ต.".$DT_NAME_ADR." ";
				}
			}
			if($AP_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= "เขต".$AP_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "อ.".$AP_NAME_ADR." ";
				}
			}
			if($PV_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD1 .= $PV_NAME_ADR." ";
				} else {
					$PER_ADD1 .= "จ.".$PV_NAME_ADR." ";
				}
			}
			if($data2[ADR_POSTCODE]) $PER_ADD1 .= $data2[ADR_POSTCODE]." ";
			if (!$PER_ADD1) $PER_ADD1 = trim($data[PER_ADD1]);

			$cmd = " select a.*, b.AP_NAME, c.PV_NAME 
							from PER_ADDRESS a left join PER_AMPHUR b on (a.AP_CODE=b.AP_CODE) left join PER_PROVINCE c on (a.PV_CODE=c.PV_CODE) 
							where PER_ID = $PER_ID and ADR_TYPE=2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();

			$PER_ADD2 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
			$DT_CODE_ADR = trim($data2[DT_CODE]);
			$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$DT_NAME_ADR = trim($data3[DT_NAME]);
			if (!$DT_NAME_ADR) $DT_NAME_ADR = $data2[ADR_DISTRICT];
			
			$AP_CODE_ADR = trim($data2[AP_CODE]);
			$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$AP_NAME_ADR = trim($data3[AP_NAME]);
			
			$PV_CODE_ADR = trim($data2[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$PV_NAME_ADR = trim($data3[PV_NAME]);
				
			if($data2[ADR_VILLAGE]) $PER_ADD2 .= "หมู่บ้าน".$data2[ADR_VILLAGE]." ";
			if($data2[ADR_BUILDING]) $PER_ADD2 .= "อาคาร".$data2[ADR_BUILDING]." ";
			if($data2[ADR_NO]) $PER_ADD2 .= "เลขที่ ".$data2[ADR_NO]." ";
			if($data2[ADR_MOO]) $PER_ADD2 .= "ม.".$data2[ADR_MOO]." ";
			if($data2[ADR_SOI]) $PER_ADD2 .= "ซ.".str_replace("ซ.","",str_replace("ซอย","",$data2[ADR_SOI]))." ";
			if($data2[ADR_ROAD]) $PER_ADD2 .= "ถ.".str_replace("ถ.","",str_replace("ถนน","",$data2[ADR_ROAD]))." ";
			if($DT_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= "แขวง".$DT_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "ต.".$DT_NAME_ADR." ";
				}
			}
			if($AP_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= "เขต".$AP_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "อ.".$AP_NAME_ADR." ";
				}
			}
			if($PV_NAME_ADR) {
				if ($PV_CODE_ADR=="1000") {
					$PER_ADD2 .= $PV_NAME_ADR." ";
				} else {
					$PER_ADD2 .= "จ.".$PV_NAME_ADR." ";
				}
			}
			if($data2[ADR_POSTCODE]) $PER_ADD2 .= $data2[ADR_POSTCODE]." ";
			if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$FULL_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];

			$arr_img = (array) null;
			$arr_imgsavedate = (array) null;
			$arr_imgshow = (array) null;
//			$img_file = "";
//			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";
//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID and (PIC_SIGN = 0 or PIC_SIGN is NULL) order by PER_PICSAVEDATE asc ";
//			echo "IMG:$cmd<br>";
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
					$PIC_SHOW = trim($data2[PIC_SHOW]);

					$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
					if ($PIC_SHOW == '1') {
						$arr_img[] = "../".$img_file;
//						echo "1..pic_show=$PIC_SHOW ==>".("../".$img_file)."<br>";
						$arr_imgshow[] = 1;
						$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
					} else {
						if(file_exists("../".$img_file))			$img_file = "../".$img_file;
						else		$img_file = $IMG_PATH."shadow.jpg";
						$arr_img[] = $img_file;
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
//						echo "2..pic_show=$PIC_SHOW ==>".("../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg")."<br>";
					}
				} // end while loop
			} else {
				//$img_file="";
				$img_file=$IMG_PATH."shadow.jpg";
			}

			if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
				$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
				if ($db_dpis2->send_cmd($cmd)) { 
					$data2 = $db_dpis2->get_array();
					$SERVER_NAME = trim($data2[SERVER_NAME]);
					$ftp_server = trim($data2[FTP_SERVER]);
					$ftp_username = trim($data2[FTP_USERNAME]);
					$ftp_password = trim($data2[FTP_PASSWORD]);
					$main_path = trim($data2[MAIN_PATH]);
					$http_server = trim($data2[HTTP_SERVER]);
					if ($http_server) {
						//echo "1.".$http_server."/".$img_file."<br>";
						$fp = @fopen($http_server."/".$img_file, "r");
						if ($fp !== false) $img_file = $http_server."/".$img_file;
						else $img_file=$IMG_PATH."shadow.jpg";
						fclose($fp);
					} else {
//						echo "2.".$img_file."<br>";
						$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					}
				} else{
				 	$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				}
			} else{ 
				//$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
				$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
//				echo "../".$img_file." @@@@ ".file_exists("../".$img_file);
			}
//echo "img_file=$img_file // $PIC_SERVER_ID<br>";

			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$CHECK_RETIREDATE = "2013-10-01";
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'y',60);
			if ($BKK_FLAG==1 && $PER_BIRTHDATE<$CHECK_RETIREDATE){
				$PER_RETIREDATE = $PER_RETIREDATE;
			}else{
				$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'d',-1);
			} // end if
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),3);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),3);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),3);
			} // end if

			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE = show_date_format(substr($PER_OCCUPYDATE, 0, 10),3);
			} // end if
		
			// =====  ส่วนราชการที่บรรจุ =====
			$where = "";
			if ($BKK_FLAG==1) $where = " and POH_ORG1 = 'กรุงเทพมหานคร' ";
			$cmd = " select 	POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1, POH_UNDER_ORG2  		
							from		PER_POSITIONHIS a, PER_MOVMENT b
							where	PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE in (1, 10, 11) $where	
							order by	POH_EFFECTIVEDATE, POH_SEQ_NO ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$POH_ORG1 = trim($data_dpis2[POH_ORG1]);
			$POH_ORG2 = trim($data_dpis2[POH_ORG2]);
			if ($POH_ORG2=="สำนักงานเขต") $POH_ORG2 = "";
			$POH_ORG3 = trim($data_dpis2[POH_ORG3]);
			$POH_UNDER_ORG1 = trim($data_dpis2[POH_UNDER_ORG1]);
			$POH_UNDER_ORG2 = trim($data_dpis2[POH_UNDER_ORG2]);
			if ($POH_UNDER_ORG1=="-") $POH_UNDER_ORG1 = "";
			if ($POH_UNDER_ORG2=="-") $POH_UNDER_ORG2 = "";

			// =====  ข้อมูลบิดา และมารดา =====
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=1 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_F = trim($data1[PN_CODE]);
			$PER_FATHERNAME = $data1[FML_NAME];
			$PER_FATHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_F=trim($data_dpis2[PN_NAME]);

			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=2 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_M = trim($data1[PN_CODE]);
			$PER_MOTHERNAME = $data1[FML_NAME];
			$PER_MOTHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_M = trim($data[PN_CODE_M]);	
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			}
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M=trim($data_dpis2[PN_NAME]);
					
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$SHOW_SPOUSE = "";
			$cmd = "	select 	PN_CODE, MAH_NAME, DV_CODE, MR_CODE 		
							from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	
							order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
			$MAH_NAME = trim($data_dpis2[MAH_NAME]);
			$DV_CODE = trim($data_dpis2[DV_CODE]);
			$MR_CODE = trim($data_dpis2[MR_CODE]);
			if (!$DV_CODE && $MR_CODE==2) {
				$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$PN_NAME=trim($data_dpis2[PN_NAME]);
					
				$SHOW_SPOUSE = $PN_NAME.$MAH_NAME;
			}

			if (!$SHOW_SPOUSE) {
				$cmd = "	select PN_NAME, FML_NAME, FML_SURNAME from PER_FAMILY	a, PER_PRENAME b
									where a.PN_CODE = b.PN_CODE(+) and PER_ID=$PER_ID and FML_TYPE = 3 and (MR_CODE is NULL or trim(MR_CODE) not in ('3','4')) ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[PN_NAME]).trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
			}
			
			// เครื่องราชฯ
			unset($arr_DEH_SHOW);
			if($DPISDB=="odbc"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								 order by		a.DEH_DATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			b.DC_NAME, b.DC_SHORTNAME, a.DEH_DATE, a.DEH_GAZETTE, a.DEH_RECEIVE_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								 order by		a.DEH_DATE ";	
			} // end if
//								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_TYPE != 3
			$count_decoratehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_decoratehis){
				$decoratehis_count = 0;
				$DEH_SHOW = "";
				$arr_DEH_SHOW = (array) null;
				while($data2 = $db_dpis2->get_array()){
					$decoratehis_count++;
					$DC_NAME = trim($data2[DC_NAME]);
					$DC_SHORTNAME = trim($data2[DC_SHORTNAME]);
					if ($DC_SHORTNAME) $DC_NAME .= " (".$DC_SHORTNAME.")";
					$DEH_DATE = trim($data2[DEH_DATE]);
					if($DEH_DATE){
						$DEH_DATE = substr($DEH_DATE, 0, 10);
						$arr_temp = explode("-", $DEH_DATE);
						$DEH_YEAR1 = ($arr_temp[0] + 543);
						$DEH_DATE1 = show_date_format($DEH_DATE,3);
					} // end if	
					$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);

					if($DEH_GAZETTE) 
						if (strpos($DEH_GAZETTE,"ราชกิจจานุเบกษา") === false)
							$DEH_GAZETTE = "ราชกิจจานุเบกษา $DEH_GAZETTE";
					$DEH_RECEIVE_DATE = trim($data2[DEH_RECEIVE_DATE]);
					if($DEH_RECEIVE_DATE) $DEH_RECEIVE_DATE = "ลงวันที่ ".show_date_format($DEH_RECEIVE_DATE,2);
					if ($DEH_SHOW) {
						$DEH_SHOW = "$DEH_SHOW,  ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DEH_SHOW1, $DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					} else {
						$DEH_SHOW = "ปี $DEH_YEAR1 $DC_NAME";
//						$DEH_SHOW1 = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 ";
					}
					if ($BKK_FLAG==1)
						$arr_DEH_SHOW[] = "$DEH_DATE1 $DC_NAME \n$DEH_GAZETTE $DEH_RECEIVE_DATE";
					else
						$arr_DEH_SHOW[] = "$DC_NAME รับพระราชทานเมื่อ $DEH_DATE1 \n$DEH_GAZETTE $DEH_RECEIVE_DATE";
				} // end while
			} // end if ($count_decoratehis)
			//------------------------------------------------------------------------------------------

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

			for($history_index=0; $history_index<count($arr_history_sort); $history_index++){
				if (in_array($arr_history_sort[$history_index],$arr_history_name)) {
					$HISTORY_NAME = $arr_history_sort[$history_index];
				} else {
					$HISTORY_NAME = "";
				}
				
				switch($HISTORY_NAME){
					case "EDUCATE" :
						$RTF->set_table_font($font, 14);
						$RTF->color($fmtTableDetail_col_idx);	// 0=BLACK
						
						$tab_align = "center";

						$RTF->new_page();
						$RTF->paragraph();
//						$RTF->new_line();
						
						$RTF->open_line($tab_align, false, false);
						$RTF->set_font($font,25);
						$RTF->color("$fmtTitle_col_idx");	// 0=BLACK
						$RTF->cell($RTF->bold(1)."11. การได้รับโทษทางวินัย ".$RTF->bold(0), "$page_ch", "center", "$fmtTitle_bgcol_idx", "TRBL");
						$RTF->close_line();
			
//						print_header("PUNISHMENT");
						$head_text1 = implode(",", $heading_text["PUNISHMENT"]);
						$head_width1 = implode(",", $heading_width["PUNISHMENT"]);
						$head_align1 = implode(",", $heading_align["PUNISHMENT"]);
						$col_function = implode(",", $column_function["PUNISHMENT"]);
						run_col_format("PUNISHMENT");
					//	echo "head_text:$head_text1<br>";
						$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$CR_NAME = "";
						$CRD_NAME = "";
						$PUN_STARTDATE = "";
						$PUN_ENDDATE = "";							
										
						if($DPISDB=="odbc"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE, PUN_NO, PUN_REF_NO
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and
																a.CRD_CODE=b.CRD_CODE and
																b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";		
						} // end if
						$count_punishmenthis = $db_dpis2->send_cmd($cmd);
			//			$db_dpis2->show_error();
						if ($count_punishmenthis) {
							$punishmenthis_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$punishmenthis_count++;
								$CR_NAME = trim($data2[CR_NAME]);
								$CRD_NAME = trim($data2[CRD_NAME]);
								$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],3);
								$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],3);
								$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
								if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";

								$PUN_NO = trim($data2[PUN_NO]);
								$PUN_REF_NO = trim($data2[PUN_REF_NO]);
								$PUN_REF = ($PUN_REF_NO ? $PUN_REF_NO : $PUN_NO);

//								$RTF->open_line();	
//								$RTF->set_font($font,14);
//								$RTF->color("0");	// 0=BLACK
//								$RTF->cell("$PUN_DURATION", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
//								$RTF->cell("$CRD_NAME", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
//								$RTF->cell("$PUN_REF", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
//								$RTF->close_line();
								$arr_data = (array) null;
								$arr_data[] = "$PUN_DURATION";	//, $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
								$arr_data[] = "$CRD_NAME";	//, $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
								$arr_data[] = "$PUN_REF";	//, $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
								$data_align = array("L","L","L");
								$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
								if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
							} // end while
						} else {
/*							$RTF->open_line();	
							$RTF->set_font($font,14);
							$RTF->color("0");	// 0=BLACK
							$RTF->cell("", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
							$RTF->cell("", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
							$RTF->cell("", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
							$RTF->close_line();
			
							$RTF->open_line();	
							$RTF->set_font($font,14);
							$RTF->color("0");	// 0=BLACK
							$RTF->cell("", $heading_width[PUNISHMENT][0], "left", "8", "TRBL");
							$RTF->cell("", $heading_width[PUNISHMENT][1], "left", "8", "TRBL");
							$RTF->cell("", $heading_width[PUNISHMENT][2], "left", "8", "TRBL");
							$RTF->close_line();*/
							$arr_data = array("","","");
							$data_align = array("L","L","L");
							$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
							if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
						}
						$RTF->close_tab();

						$RTF->open_line($tab_align, false, false);
						$RTF->set_font($font,14);
						$RTF->cell($RTF->color("0").$RTF->bold(1)."12. วันที่ไม่ได้รับเงินเดือน หรือวันที่ได้รับเงินเดือนไม่เต็ม หรือวันที่มิได้ประจำปฏิบัติหน้าที่อยู่ในเขตที่ได้มีประกาศใช้กฎอัยการศึก".$RTF->bold(0), "$page_ch", "center", "16", "TRBL");
						$RTF->close_line();		

//						print_header("NOTPAID");
						$head_text1 = implode(",", $heading_text["NOTPAID"]);
						$head_width1 = implode(",", $heading_width["NOTPAID"]);
						$head_align1 = implode(",", $heading_align["NOTPAID"]);
						$col_function = implode(",", $column_function["NOTPAID"]);
						run_col_format("NOTPAID");
					//	echo "head_text:$head_text1<br>";
						$tab_align = "center";
						$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = array("","","");
						$data_align = array("L","L","L");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
/*
						$RTF->open_line();
						$RTF->set_font($font,14);
						$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][0], "center", "8", "LTBR");
						$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][1], "center", "8", "LTBR");
						$RTF->cell($RTF->color("0").$RTF->bold(1).$SESS_USERID.$RTF->bold(0), $heading_width[NOTPAID][2], "center", "8", "TRBL");
						$RTF->close_line();
						$RTF->open_line();
						$RTF->set_font($font,14);
						$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][0], "center", "8", "LTBR");
						$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][1], "center", "8", "LTBR");
						$RTF->cell($RTF->color("0").$RTF->bold(1)."".$RTF->bold(0), $heading_width[NOTPAID][2], "center", "8", "LTBR");
						$RTF->close_line();
*/
						$RTF->close_tab();

						$head_text1 = implode(",", $heading_text["NAME"]);
						$head_width1 = implode(",", $heading_width["NAME"]);
						$head_align1 = implode(",", $heading_align["NAME"]);
						$col_function = implode(",", $column_function["NAME"]);
						run_col_format("NAME");
					//	echo "head_text:$head_text1<br>";
						$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
						if (!$result) echo "****** error ****** on open table for $table<br>";
/*						
						$RTF->open_line();
						$RTF->set_font($font,14);
						$RTF->cell($RTF->color("0").$RTF->bold(1).($MINISTRY_NAME?$MINISTRY_NAME:"").$RTF->bold(0), 32, "left", "8", "LTB");
						$RTF->cell($RTF->color("0").$RTF->bold(1).($DEPARTMENT_NAME?$DEPARTMENT_NAME:"").$RTF->bold(0), 34, "left", "8", "TB");
						$RTF->cell($RTF->color("0").$RTF->bold(1)."$KP7_TITLE".$RTF->bold(0), 32, "right", "8", "TBR");
						$RTF->close_line();
*/
						$arr_data = (array) null;
						$arr_data[] = ($MINISTRY_NAME?$MINISTRY_NAME:"");
						$arr_data[] = ($DEPARTMENT_NAME?$DEPARTMENT_NAME:"");
						$arr_data[] = "$KP7_TITLE";
						$data_align = array("L","L","R");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						// คำนวนเพื่อ fix ขนาดของภาพ
						$fix_width = 100;
						if ($img_file) {
							list($width, $height) = getimagesize($img_file); 
							$new_h = $height / $width * $fix_width;
							$new_ratio = floor(100 / $height * $fix_width); 
						}
						
						$arr_data = (array) null;
						$arr_data[] = "1. ชื่อ  ".$FULLNAME;
						$arr_data[] = "5. ชื่อบิดา  ".($FATHERNAME?$FATHERNAME:"-");
						$arr_data[] = "<&&rowone&&>".($img_file ? "<*img*".$img_file.",25*img*>" : "");
						$data_align = array("L","L","C");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx, true); // ตัวสุดท้าย คือ แสดงบรรทัดนี้ในหน้าเดียว
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$arr_data = (array) null;
						$arr_data[] = "2. วัน เดือน ปี เกิด  ".$PER_BIRTHDATE;
						$arr_data[] = "6. ชื่อมารดา  ".($MOTHERNAME?$MOTHERNAME:"-");
						$arr_data[] = "<&&row&&>";
						$data_align = array("L","L","C");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$arr_data = (array) null;
						$arr_data[] = "3. วันครบเกษียณอายุ ".$PER_RETIREDATE;
						$arr_data[] = "7. วันสั่งบรรจุ  ".$PER_STARTDATE;
						$arr_data[] = "<&&row&&>";
						$data_align = array("L","L","C");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$arr_data = (array) null;
						$arr_data[] = "4. ชื่อคู่สมรส ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-");
						$arr_data[] = "8. วันเริ่มปฏิบัติราชการ  ".$PER_STARTDATE;
						$arr_data[] = "<&&row&&>";
						$data_align = array("L","L","C");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$arr_data = (array) null;
						$arr_data[] = "    ภูมิลำเนา ".($PV_NAME?$PV_NAME:"-");
						$arr_data[] = "9. ประเภทข้าราชการ ".$OT_NAME;
						$arr_data[] = "<&&row&&>";
						$data_align = array("L","L","C");
						$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

						$RTF->close_tab();

						$RTF->open_line($tab_align, false, false);
						$RTF->set_font($font,14);
						$RTF->color($fmtTableHead_col_idx);	// 0=DARKGRAY
						$RTF->cell("เครื่องราชอิสริยาภรณ์", "66", "center", "$fmtTableHead_bgcol_idx", "TRBL");
						$RTF->cell("เลขประจำตัวประชาชน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit(card_no_format($PER_CARDNO,1)):card_no_format($PER_CARDNO,1)), "32", "left", "$fmtTableHead_bgcol_idx", "TRBL");
						$RTF->close_line();

						$RTF->open_line($tab_align, false, false);
						$RTF->set_font($font,14);
//						$RTF->color("1");	// 0=DARKGRAY
						$RTF->color($fmtTitle_col_idx);	// 0=DARKGRAY
						$RTF->cell($DEH_SHOW, "66", "center", "$fmtTableHead_bgcol_idx", "TRBL");
						$RTF->cell("เลขประจำตัวข้าราชการ : $PER_OFFNO", "32", "left", "$fmtTableHead_bgcol_idx", "TRBL");
						$RTF->close_line();

					// เฉพาะ EDUCATE และ TRAINING
						$RTF->open_line($tab_align, false, false);
						$RTF->set_font($font,14);
						$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
						$RTF->cell($RTF->bold(1)."10. ประวัติการศึกษา ฝึกอบรมและดูงาน".$RTF->bold(0), "$page_ch", "center", "$fmtTitle_bgcol_idx", "TRBL");
						$RTF->close_line();

						$EDU_PERIOD="";
						$EN_NAME = "";
						$EM_NAME = "";
						$INS_NAME = "";
						$CT_NAME = "";

						if($DPISDB=="odbc"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
										from			((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										 where		a.PER_ID=$PER_ID
									 order by		iif(isnull(a.EDU_SEQ),0,a.EDU_SEQ), a.EDU_STARTYEAR, a.EDU_ENDYEAR ";							   
						}elseif($DPISDB=="oci8"){
							$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
									from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
												a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
									order by		to_number(nvl(a.EDU_SEQ,0)), a.EDU_STARTYEAR, a.EDU_ENDYEAR ";							   
						}elseif($DPISDB=="mysql"){
							$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME, EDU_HONOR
									from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									 where		a.PER_ID=$PER_ID
									 order by		a.EDU_SEQ+0, a.EDU_STARTYEAR, a.EDU_ENDYEAR ";			
						} // end if
						$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$arr_educate = (array) null;
							$row_count = $edu_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
								$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
								if($EDU_STARTYEAR && $EDU_STARTYEAR != "-" && $EDU_ENDYEAR){
									$EDU_PERIOD = "$EDU_STARTYEAR - $EDU_ENDYEAR";
								}else{
									$EDU_PERIOD = "$EDU_ENDYEAR";
								}
								$arr_educate[$row_count][edu_period] = $EDU_PERIOD;

								$EN_NAME = trim($data2[EN_NAME]);
								$EM_NAME = trim($data2[EM_NAME]);
								if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
								$INS_NAME = trim($data2[INS_NAME]);
								if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
								$EDU_HONOR = trim($data2[EDU_HONOR]);
								if ($EDU_HONOR) {
									if (strpos($EDU_HONOR,"เกียรตินิยม") === false)
										$EN_NAME .= " เกียรตินิยม$EDU_HONOR";
								}
								$arr_educate[$row_count][en_name] = $EN_NAME;
								$arr_educate[$row_count][em_name] = $EM_NAME;
								$arr_educate[$row_count][ins_name] = $INS_NAME;
								$row_count++;
								$edu_count++;
							} // end while $data2 for EDUCATION
						
							if($DPISDB=="odbc"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, 
																	a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE , a. TRN_FUND, a.TRN_NO, 
																	a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
												 order by	a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, 
																	a.TRN_COURSE_NAME, a.TRN_DOCNO, a.TRN_DOCDATE
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE ";		
							} // end if
							$count_traininghis = $db_dpis2->send_cmd($cmd);
	//							$db_dpis2->show_error();
							$arr_training = (array) null;
							$row_count = $trn_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$TRN_DURATION = "";
								$TRN_STARTDATE = "";
								$TRN_ENDDATE = "";
								$TR_NAME = "";
								$TRN_PLACE = "";
							
								$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],2);
								$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],2);
								if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
									if ($BKK_FLAG==1) { // กทม
										$TRN_DURATION = $TRN_STARTDATE." - ".$TRN_ENDDATE;
									} else {
										$TRN_DURATION = $TRN_STARTDATE." - \n".$TRN_ENDDATE;
									}
									if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
								} elseif(trim($TRN_STARTDATE)) {
									$TRN_DURATION = $TRN_STARTDATE;
								} elseif(trim($TRN_ENDDATE)) {
									$TRN_DURATION = $TRN_ENDDATE;
								}
								$arr_training[$row_count][trn_duration]=$TRN_DURATION;
								$TRN_ORG = trim($data2[TRN_ORG]);
								$TR_NAME = trim($data2[TR_NAME]);				
								if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
								if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",'"',$TR_NAME);		}
								$TRN_PLACE = trim($data2[TRN_PLACE]);
								$TRN_FUND = trim($data2[TRN_FUND]);
								$TRN_NO = trim($data2[TRN_NO]);
								if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
								$TRN_DOCNO = trim($data2[TRN_DOCNO]);
								$TRN_DOCDATE = show_date_format($data2[TRN_DOCDATE],2);
								if ($TRN_DOCNO && $TRN_DOCDATE) {
									$TR_NAME .= "\nคำสั่ง ".$TRN_DOCNO." ลว. ".$TRN_DOCDATE;
								}
								if (trim($TRN_PLACE) || trim($TR_NAME)) {
									$arr_training[$row_count][tr_name] = $TR_NAME;
									$arr_training[$row_count][trn_org] = $TRN_ORG;
									$arr_training[$row_count][trn_place] = $TRN_PLACE;
									$arr_training[$row_count][trn_fund] = $TRN_FUND;
									$row_count++;
								}
								$trn_count++;
						} // end while data2 for TRAINING

			 			if ($BKK_FLAG==1) { // กทม
							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell("ประวัติการศึกษา", "$page_ch", "center", "$fmtTitle_bgcol_idx", "TRBL");
//							$RTF->cell("ประวัติการฝึกอบรมและดูงาน", "52", "center", "8", "TRBL");
							$RTF->close_line();						

							$head_text1 = implode(",", $heading_bkk1_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_bkk1_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_bkk1_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function_bkk1[$HISTORY_NAME]);
							$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_bkk1_text[$HISTORY_NAME], $heading_bkk1_width[$HISTORY_NAME], $heading_bkk1_align[$HISTORY_NAME]);
							$buff = explode("|",$COLUMN_FORMAT);
							$arr_column_map = explode(",",$buff[0]);
							$arr_column_sel = explode(",",$buff[1]);
							$arr_column_width = explode(",",$buff[2]);
							$arr_column_align = explode(",",$buff[3]);
						//	echo "head_text:$head_text1<br>";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";
							
							for($row_count=0; $row_count < $edu_count; $row_count++) {
									// พิมพ์ EDU
									$arr_data = (array) null;
									$arr_data[] = $arr_educate[$row_count][ins_name];
									$arr_data[] = $arr_educate[$row_count][edu_period];
									$arr_data[] = $arr_educate[$row_count][en_name]."  ".$arr_educate[$row_count][em_name];
									$data_align = array("L","C","L");
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
							} // end for loop $row_count

							$RTF->paragraph();

							$head_text1 = implode(",", $heading_bkk2_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_bkk2_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_bkk2_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function_bkk2[$HISTORY_NAME]);
							$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_bkk2_text[$HISTORY_NAME], $heading_bkk2_width[$HISTORY_NAME], $heading_bkk2_align[$HISTORY_NAME]);
							$buff = explode("|",$COLUMN_FORMAT);
							$arr_column_map = explode(",",$buff[0]);
							$arr_column_sel = explode(",",$buff[1]);
							$arr_column_width = explode(",",$buff[2]);
							$arr_column_align = explode(",",$buff[3]);
						//	echo "head_text:$head_text1<br>";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";

							for($row_count=0; $row_count < $trn_count; $row_count++) {
									// พิมพ์ TRN
									$arr_data = (array) null;
									$arr_data[] = $arr_training[$row_count][trn_place];
									$arr_data[] = $arr_training[$row_count][trn_duration];
									$arr_data[] = $arr_training[$row_count][tr_name];
									$data_align = array("L","C","L");
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
							} // end for loop $row_count
						} else {	// $BKK_FLAG != 1
							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	 //0=DARKGRAY
							$RTF->cell("ประวัติการศึกษา", "46", "center", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->cell("ประวัติการฝึกอบรมและดูงาน", "52", "center", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();
	
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function[$HISTORY_NAME]);
							run_col_format($HISTORY_NAME);
						//	echo "head_text:$head_text1<br>";
							$tab_align = "center";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";

//							print_header($HISTORY_NAME);

							for($row_count=0; $row_count < count($arr_content); $row_count++) {
								$arr_data = (array) null;
								$arr_data[] = $arr_educate[$row_count][ins_name];
								$arr_data[] = $arr_educate[$row_count][edu_period];
								$arr_data[] = $arr_educate[$row_count][en_name]."  ".$arr_educate[$row_count][em_name];
								// พิมพ์ TRN
								$arr_data[] = $arr_training[$row_count][trn_place];
								$arr_data[] = $arr_training[$row_count][trn_duration];
								$arr_data[] = $arr_training[$row_count][tr_name];
								$data_align = array("L","C","L","L","C","L");
								$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
								if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
							} // end for loop $row_count
						}	// end if ($BKK_FLAG==1)
						break;
					case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
							$page_no = 1;
							
							$border = "TRBL";
							
							$RTF->open_section(1); 
							print_footer();

//							$RTF->new_page();
//							$RTF->add_text("", "left");
							$RTF->paragraph();

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."13. ตำแหน่งและอัตราเงินเดือน".$RTF->bold(0), "$page_ch", "center", "$fmtTitle_bgcol_idx", $border);
							$RTF->close_line();

							$RTF->paragraph();
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function[$HISTORY_NAME]);
							run_col_format($HISTORY_NAME);
						//	echo "head_text:$head_text1<br>";
							$tab_align = "center";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";
//							print_header($HISTORY_NAME);
							//########################
							//ประวัติการดำรงตำแหน่งข้าราชการ
							//########################
							$ARR_POSITIONHIS = (array) null;
							if($DPISDB=="odbc"){
								$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_TYPE, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST,
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";							   
							}elseif($DPISDB=="oci8"){
							 	$cmd = "select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_TYPE, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST, 
																a.POH_DOCDATE,	a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												from		PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
												where	a.PER_ID=$PER_ID and a.PL_CODE=d.PL_CODE(+) and
																a.PT_CODE=e.PT_CODE(+) and a.PM_CODE=f.PM_CODE(+) and 
																a.LEVEL_NO=g.LEVEL_NO(+)
												order by	a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,	a.MOV_CODE, 
																d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_TYPE, g.POSITION_LEVEL, g.LEVEL_SHORTNAME, a.POH_POS_NO_NAME, a.POH_POS_NO, 
																a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, POH_SPECIALIST, 
																a.POH_DOCDATE, a.UPDATE_USER, a.POH_PL_NAME, a.POH_PM_NAME, a.POH_ORG, a.POH_SEQ_NO, a.POH_ENDDATE, 
																POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ
												  from	(
																(
																	(
																		PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																	) 	left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
															) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where	a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();

//							echo "ประวัติการดำรงตำแหน่ง.. PER_ID=$PER_ID, count=$count_positionhis<br>";
							if($count_positionhis){
								$last_org = "";
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
									$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
									$POH_ENDDATE = trim(substr($data2[POH_ENDDATE],0,10));
									$LEVEL_NO = trim($data2[LEVEL_NO]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									if ($BKK_FLAG==1)
										$POSITION_LEVEL = trim($data2[LEVEL_SHORTNAME]);
									else
										$POSITION_LEVEL = trim($data2[POSITION_LEVEL]);
									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
										$POSITION_LEVEL = "ชำนาญการ\nพิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									if ($BKK_FLAG==1 && ($POSITION_LEVEL=="ต้น" || $POSITION_LEVEL=="สูง")) 
										$POSITION_LEVEL = $POSITION_TYPE.$POSITION_LEVEL;
									$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
									$POH_ACTH_SEQ = trim($data2[POH_ACTH_SEQ]);
//									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO]))){
									$PL_NAME = trim($data2[PL_NAME]);
									$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
									if ($POSITION_LEVEL=="1" || $POSITION_LEVEL=="2" || $POSITION_LEVEL=="3" || $POSITION_LEVEL=="4" || $POSITION_LEVEL=="5" || 
										$POSITION_LEVEL=="6" || $POSITION_LEVEL=="7" || $POSITION_LEVEL=="8" || $POSITION_LEVEL=="9" || $POSITION_LEVEL=="10" ||	
										$POSITION_LEVEL=="11")
										$footer_level = trim($data2[LEVEL_NAME]);
									else
										$footer_level = trim($data2[POSITION_LEVEL]);
//									echo "1..footer_level=$footer_level<br>";
//										echo "PER_ID=$PER_ID,  POSITION_LEVEL=$POSITION_LEVEL,  LEVEL_NAME=$LEVEL_NAME,  footer_level=$footer_level<br>";
									$PT_CODE = trim($data2[PT_CODE]);
									$PT_NAME = trim($data2[PT_NAME]);
									$PM_CODE = trim($data2[PM_CODE]);
									$PM_NAME = trim($data2[PM_NAME]);
									$POH_PL_NAME = trim($data2[POH_PL_NAME]);
									$POH_SPECIALIST = trim($data2[POH_SPECIALIST]);
									$POH_DOCNO_EDIT = trim($data2[POH_DOCNO_EDIT]);
									$POH_DOCDATE_EDIT = trim($data2[POH_DOCDATE_EDIT]);
									$POH_REF_DOC = trim($data2[POH_REF_DOC]);
//										if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") $POH_PL_NAME = $POH_PL_NAME." ".level_no_format($LEVEL_NO);
									$arr_temp = "";
									if (strlen($POH_PL_NAME) > 50) {
										$arr_temp = explode(" ", $POH_PL_NAME);
										if ((strlen($arr_temp[0])+strlen($arr_temp[1])+strlen($arr_temp[2])) < 50) 
											$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]." ".$arr_temp[2]."\n".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
										elseif ((strlen($arr_temp[0])+strlen($arr_temp[1])) < 50) 
											$POH_PL_NAME = $arr_temp[0]." ".$arr_temp[1]."\n".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
										else
											$POH_PL_NAME = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5]." ".$arr_temp[6]." ".$arr_temp[7];
									}
									$POH_PM_NAME = trim($data2[POH_PM_NAME]);
									if ($POH_PM_NAME) {
										if ($BKK_FLAG==1 && $POH_EFFECTIVEDATE < $PT_DATE) 
											$POH_PL_NAME = $POH_PL_NAME."\n".$POH_PM_NAME;
										elseif (strpos($POH_PL_NAME,$POH_PM_NAME)!==false)
											$POH_PL_NAME = $POH_PL_NAME;
										else
											$POH_PL_NAME = $POH_PM_NAME."\n".$POH_PL_NAME;
									}
									if ($POH_SPECIALIST) $POH_PL_NAME .= " ($POH_SPECIALIST)";
									$POH_ORG = trim($data2[POH_ORG]);
									$MOV_CODE = trim($data2[MOV_CODE]);
									//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
									$cmd = " select MOV_NAME, MOV_SUB_TYPE from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$MOV_NAME = $data3[MOV_NAME];
									$MOV_SUB_TYPE = $data3[MOV_SUB_TYPE];
									if ($MOV_SUB_TYPE==9) {
										$POH_POS_NO = $POSITION_LEVEL = $POH_SALARY = "";
									}

									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,12) == "ทดลองปฏิบัติ" || substr($MOV_NAME,0,5) == "บรรจุ"))
										$POH_ORG .= " (ทดลองปฏิบัติหน้าที่ราชการ)"; 
									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,15) == "พ้นทดลองปฏิบัติ" || substr($MOV_NAME,0,14) == "พ้นจากการทดลอง")) {
										$POH_ORG .= " (พ้นจากการทดลองปฏิบัติหน้าที่ราชการ)"; 
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && substr($MOV_NAME,0,11) == "ไม่พ้นทดลอง") {
										$POH_ORG .= " (ไม่ผ่านการทดลองปฏิบัติหน้าที่ราชการ)"; 
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && (substr($MOV_NAME,0,8) == "รักษาการ" || substr($MOV_NAME,0,14) == "รักษาราชการแทน")) {
										$POH_PL_NAME = $MOV_NAME.$POH_PL_NAME; 
										if (substr($MOV_NAME,0,8) == "รักษาการ" && $POH_ACTH_SEQ) $POH_ORG .= " (ลำดับที่ $POH_ACTH_SEQ)";
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && ($MOV_NAME == "การช่วยราชการ" || $MOV_NAME == "ช่วยราชการ")) {
										$POH_ORG = $MOV_NAME.$POH_ORG; 
										if ($POH_ENDDATE) $POH_PL_NAME = "ถึง ".show_date_format($POH_ENDDATE,2);
										$MOV_NAME = "";
									}
									if ($BKK_FLAG==1 && (strpos($MOV_NAME,"แก้ไข")!==false || strpos($MOV_NAME,"ยกเลิก")!==false)) {
										if ($POH_DOCNO_EDIT) {
											if(trim($POH_DOCDATE_EDIT)){
												$POH_DOCDATE_EDIT = " ลว. ".show_date_format(substr($POH_DOCDATE_EDIT, 0, 10),2);
												$POH_PL_NAME = $MOV_NAME.' '.$POH_DOCNO_EDIT.$POH_DOCDATE_EDIT.' '.$POH_PL_NAME;
											} else {
												$POH_PL_NAME = $MOV_NAME.' '.$POH_DOCNO_EDIT.' '.$POH_PL_NAME;
											}
										} else {
											$POH_PL_NAME = $MOV_NAME.' '.$POH_PL_NAME;
										}
										$MOV_NAME = "";
									}
									$arr_temp = "";
									if (strlen($POH_ORG) > 50) {
										$arr_temp = explode(" ", $POH_ORG);
//										if (strpos($POH_ORG,"ลำดับที่")!==false) echo "1..POH_ORG=$POH_ORG<br>";
//										$POH_ORG = $arr_temp[0]."\n".$arr_temp[1]." ".$arr_temp[2]." ".$arr_temp[3]." ".$arr_temp[4]." ".$arr_temp[5];
										$POH_ORG = $arr_temp[0]."\n".$arr_temp[1];
										if (count($arr_temp) > 2)
											for($iii = 2; $iii < count($arr_temp); $iii++)	$POH_ORG .= " ".$arr_temp[$iii];
//										if (strpos($POH_ORG,"ลำดับที่")!==false) echo "2..POH_ORG=$POH_ORG<br>";
									}
//										if ($footer_level) $footer_level .= " ".$POH_ORG;
//										echo "footer_level=$footer_level<br>";
									$TMP_PL_NAME = $POH_PL_NAME."\n".$POH_ORG;
									//if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
//									}
									if ($POH_ORG) $last_org = $POH_ORG;
//									echo "PER_ID=$PER_ID, POH_ORG=$POH_ORG, last_org=$last_org<br>";
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO_NAME = trim($data2[POH_POS_NO_NAME]);
									if (substr($POH_POS_NO_NAME,0,4)=="กปด.")
										$POH_POS_NO = $POH_POS_NO_NAME." ".trim($data2[POH_POS_NO]);
									else
										$POH_POS_NO = $POH_POS_NO_NAME.trim($data2[POH_POS_NO]);	
									$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									$POH_SALARY = $data2[POH_SALARY];
//									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$POH_DOCNO = trim($data2[POH_DOCNO]);
									
									$POH_DOCDATE = "";
									if(trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-"){
										if(trim($data2[POH_DOCDATE])){
											$POH_DOCDATE = "ลว. ".show_date_format(substr($data2[POH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname]; 
										}
										if ($BKK_FLAG==1) 
											$POH_DOCNO = $data2[POH_DOCNO]." ".$POH_DOCDATE." ".$POH_REF_DOC;
										else
											if ($PRINT_KP7_USER=="Y") 
												$POH_DOCNO = $data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
											else
												$POH_DOCNO = $data2[POH_DOCNO]."\n".$POH_DOCDATE;
										if (trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-" && strpos($data2[POH_DOCNO],"คำสั่ง") === false && 
											strpos($data2[POH_DOCNO],"บันทึก") === false && strpos($data2[POH_DOCNO],"หนังสือ") === false && $MOV_CODE != "12" && $MOV_CODE != "21" && $MOV_CODE != "027")
											$POH_DOCNO = "คำสั่ง ".$POH_DOCNO;
									}

//									if ($MOV_CODE == "10420") {
									if ($MOV_CODE == "xxxx") {
										$POH_EFFECTIVEDATE = $POH_EFFECTIVEDATE."[2date]\nถึง\n".show_date_format($POH_ENDDATE,2);	//$POH_ENDDATE;
									}
									
									//เก็บลง array ของ POSTION HIS
									if ($BKK_FLAG==1) {
										if ($MOV_NAME) $DOC_NO = $MOV_NAME."\n".$POH_DOCNO;
										else $DOC_NO = $POH_DOCNO;
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level,
																						'SALARY'=>$POH_SALARY,
																						'SALARY1'=>$POH_SALARY,
																						'ORG'=>$POH_ORG,
																						'DOC_NO'=>$DOC_NO);
									} else {
										$ARR_POSITIONHIS[$PER_ID][] = array(
																						'DATE'=>$POH_EFFECTIVEDATE,
																						'SEQ'=>$POH_SEQ_NO,
																						'MOVE'=>$MOV_NAME,
																						'POS_NAME'=>$TMP_PL_NAME,
																						'POS_NO'=>$POH_POS_NO,
																						'LEVEL'=>$POSITION_LEVEL,
																						'FOOTERLEVEL'=>$footer_level." ".$last_org,
																						'SALARY'=>$POH_SALARY,
																						'SALARY1'=>$POH_SALARY,
																						'ORG'=>$POH_ORG,
																						'DOC_NO'=>$POH_DOCNO);
									}
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $data2[POH_DOCDATE];
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
								} // end while
//								for($ii=0; $ii < count($ARR_POSITIONHIS[$PER_ID]); $ii++) {
//									echo "loop1..".$ARR_POSITIONHIS[$PER_ID][$ii]['DATE']."=>ORG=>".$ARR_POSITIONHIS[$PER_ID][$ii]['ORG']."<br>";
//								}
							} //end if 
	
							//########################
							//ประวัติการเลื่อนขั้นเงินเดือน
							//########################
							$ARR_SALARYHIS = (array) null;
							if($DPISDB=="odbc"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		 SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												from			PER_SALARYHIS b, PER_MOVMENT c
												where		b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE and b.MOV_CODE!='1901' 
												order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, 
																	b.SAH_DOCNO, b.SAH_DOCDATE, b.UPDATE_USER, b.SAH_SEQ_NO, b.LEVEL_NO, b.SAH_POSITION, 
																	b.SAH_ORG, b.SM_CODE, b.SAH_PAY_NO, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_PERCENT_UP, 
																	b.SAH_SALARY_EXTRA, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC
												 from			PER_SALARYHIS b  inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE
												 where			b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							//echo "<br>$cmd<br>";
							
//							echo "ประวัติการเลื่อนขั้นเงินเดือน.. PER_ID=$PER_ID, count=$count_salaryhis<br>";
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
									$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
									$MOV_NAME = trim($data2[MOV_NAME]);		
									$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"");
									$SAH_SALARY = $data2[SAH_SALARY];
									$SAH_SALARY1 = (float)$data2[SAH_SALARY];
									$SAH_SEQ_NO = $data2[SAH_SEQ_NO];
									$LEVEL_NO = $data2[LEVEL_NO];

									$cmd = " select POSITION_TYPE, POSITION_LEVEL, LEVEL_SHORTNAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									if ($BKK_FLAG==1)
										$POSITION_LEVEL = $data3[LEVEL_SHORTNAME];
									else
										$POSITION_LEVEL = $data3[POSITION_LEVEL];
									$POSITION_TYPE = $data3[POSITION_TYPE];
									if ($POSITION_LEVEL=="ชำนาญการพิเศษ") {
										$POSITION_LEVEL = "ชำนาญการ\nพิเศษ";
//										$testpos = str_replace("\n","<BR>",$POSITION_LEVEL);
//										echo "POSITION_LEVEL=$testpos<BR>";
									}
									if ($BKK_FLAG==1 && ($POSITION_LEVEL=="ต้น" || $POSITION_LEVEL=="สูง")) 
										$POSITION_LEVEL = $POSITION_TYPE.$POSITION_LEVEL;
									
									$SAH_POSITION = $data2[SAH_POSITION];
									if ($LEVEL_NO >= "01" && $LEVEL_NO <= "11" && strpos($SAH_POSITION,"0") === false && strpos($SAH_POSITION,"1") === false && 
										strpos($SAH_POSITION,"2") === false && strpos($SAH_POSITION,"3") === false && strpos($SAH_POSITION,"4") === false && strpos($SAH_POSITION,"5") === false && 
										strpos($SAH_POSITION,"6") === false && strpos($SAH_POSITION,"7") === false && strpos($SAH_POSITION,"8") === false && strpos($SAH_POSITION,"9") === false) 
										$SAH_POSITION .= $POSITION_LEVEL;
									$SAH_ORG = $data2[SAH_ORG];
									$SM_CODE = trim($data2[SM_CODE]);
									$SAH_SPECIALIST = trim($data2[SAH_SPECIALIST]);
									$SAH_DOCNO_EDIT = trim($data2[SAH_DOCNO_EDIT]);
									$SAH_DOCDATE_EDIT = trim($data2[SAH_DOCDATE_EDIT]);
									$SAH_REF_DOC = trim($data2[SAH_REF_DOC]);
									if ($SAH_SPECIALIST) $SAH_POSITION .= " ($SAH_SPECIALIST)";
									if ($BKK_FLAG==1 && (strpos($MOV_NAME,"แก้ไข")!==false || strpos($MOV_NAME,"ยกเลิก")!==false)) {
										if ($SAH_DOCNO_EDIT) {
											if(trim($SAH_DOCDATE_EDIT)){
												$SAH_DOCDATE_EDIT = " ลว. ".show_date_format(substr($SAH_DOCDATE_EDIT, 0, 10),2);
												$MOV_NAME .= ' '.$SAH_DOCNO_EDIT.$SAH_DOCDATE_EDIT;
											} else {
												$MOV_NAME .= ' '.$SAH_DOCNO_EDIT;
											}
										} 
//										if ($SAH_EFFECTIVEDATE < $PT_DATE) $SAH_POSITION = $MOV_NAME.' '.$SAH_POSITION;
									} 
									if ($BKK_FLAG==1 && $SAH_EFFECTIVEDATE >= $PT_DATE) {
										$SAH_ORG = "";
										if (strpos($MOV_NAME,"แก้ไข")!==false || strpos($MOV_NAME,"ยกเลิก")!==false) {
											$SAH_POSITION = $MOV_NAME;
											$MOV_NAME = "";
										} elseif (substr($MOV_NAME,0,13)=="ปรับเงินเดือน" || $MOV_NAME=="ปรับตามบัญชีเงินเดือนใหม่" || substr($MOV_NAME,0,18)=="ปรับอัตราเงินเดือน") {
											$SAH_POSITION = "ปรับอัตราเงินเดือน";
										} elseif ($MOV_NAME=="เลื่อนขั้นเงินเดือน" || $MOV_NAME=="เลื่อนเงินเดือน" || $MOV_NAME=="เลื่อนขั้น 1 ขั้น" || $MOV_NAME=="เลื่อนขั้น 0.5 ขั้น" || 
											$MOV_NAME=="เลื่อนขั้น 1.5 ขั้น" || $MOV_NAME=="เลื่อนขั้น 1 ขั้น" || $MOV_NAME=="เลื่อนขั้นเงินเดือนข้าราชการ") {
											$SAH_POSITION = "เลื่อนเงินเดือน";
											$MOV_NAME = "";
										} elseif ($MOV_NAME=="ลดขั้น 1 ขั้น" || $MOV_NAME=="ลดขั้นมากกว่า 1 ขั้น") {
											$SAH_POSITION = "ลดขั้นเงินเดือน";
										} elseif ($MOV_NAME=="ได้รับเงินตอบแทนพิเศษ" || $MOV_NAME=="เงินตอบแทนพิเศษ 2%" || $MOV_NAME=="เงินตอบแทนพิเศษ 4%") {
											$SAH_POSITION = "เต็มขั้น";
										} elseif ($MOV_NAME=="ไม่ได้เลื่อนขั้น") {
											$SAH_POSITION = "ไม่ได้เลื่อนเงินเดือน";
										} else {
											$SAH_POSITION = $MOV_NAME;
											$MOV_NAME = "";
										}
									}
									$SAH_PAY_NO = $data2[SAH_PAY_NO];
									if (!$SAH_PAY_NO) $SAH_PAY_NO = $data2[SAH_POS_NO];
									$SAH_POS_NO_NAME = trim($data2[SAH_POS_NO_NAME]);
									if (substr($SAH_POS_NO_NAME,0,4)=="กปด.")
										$SAH_PAY_NO = $SAH_POS_NO_NAME." ".$SAH_PAY_NO;
									else
										$SAH_PAY_NO = $SAH_POS_NO_NAME.$SAH_PAY_NO;
									$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];		
									$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA];		
									if ($SAH_PERCENT_UP) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_PERCENT_UP, 2) . " %";		
									if (strpos($MOV_NAME,"เต็มขั้น") !== false && $SAH_SALARY_EXTRA > 0) $MOV_NAME = $MOV_NAME . " " . number_format($SAH_SALARY_EXTRA, 2);	
//									if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") $SAH_POSITION = $SAH_POSITION." ".level_no_format($LEVEL_NO);

									$cmd = " select SM_NAME, SM_FACTOR from PER_SALARY_MOVMENT where SM_CODE='$SM_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$SM_NAME = trim($data3[SM_NAME]);
									$SM_FACTOR = $data3[SM_FACTOR] + 0;
//									if ($SM_NAME && $SM_FACTOR != 0) $MOV_NAME .= " ($SM_NAME)";
									if ($BKK_FLAG==1) {
										if ($SM_NAME=="เลื่อนขั้น 1 ขั้น") $SAH_SALARY .= "\nหนึ่งขั้น";
										elseif ($SM_NAME=="เลื่อนขั้น 1.5 ขั้น") $SAH_SALARY .= "\nหนึ่งขั้นครึ่ง";
										elseif ($SM_NAME=="เลื่อนขั้น 2 ขั้น") $SAH_SALARY .= "\nสองขั้น";
//										echo "1..SALARY=$SAH_SALARY<br>";
									} else {
										if ($SM_NAME) $MOV_NAME .= " ($SM_NAME)";
//										echo "2..SALARY=$SAH_SALARY<br>";
									}
									$ARR_POSCHECK[$PER_ID][DOC_NO][] = $data2[POH_DOCNO];
									$ARR_POSCHECK[$PER_ID][DOC_DATE][] = $POH_DOCDATE;
									$ARR_POSCHECK[$PER_ID][MOVE_CODE][] = $MOV_CODE;
									$SAH_DOCNO = "";
									if (trim($data2[SAH_DOCNO]) == "-") {
										if(trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-"){
											$SAH_DOCNO = "ลว. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),2);
										}
										$SAH_DOCNO = "";
									}elseif(trim($data2[SAH_DOCNO])){
										if(trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-"){
											$SAH_DOCDATE = "ลว. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),2);
										}
										$USRNAME = "";
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//	$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname];
										}
										if ($BKK_FLAG==1) 
											$SAH_DOCNO = $data2[SAH_DOCNO]." ".$SAH_DOCDATE;
										else
											if ($PRINT_KP7_USER=="Y") 
												$SAH_DOCNO = $data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
											else
												$SAH_DOCNO = $data2[SAH_DOCNO]."\n".$SAH_DOCDATE;
										if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO]) != "-" && strpos($data2[SAH_DOCNO],"คำสั่ง") === false && 
											strpos($data2[SAH_DOCNO],"บันทึก") === false && strpos($data2[SAH_DOCNO],"หนังสือ") === false)
											$SAH_DOCNO = "คำสั่ง ".$SAH_DOCNO;
									}

									$TMP_PL_NAME = trim($MOVE_NAME);
									
									if ($POSITION_LEVEL=="1" || $POSITION_LEVEL=="2" || $POSITION_LEVEL=="3" || $POSITION_LEVEL=="4" || $POSITION_LEVEL=="5" || 
										$POSITION_LEVEL=="6" || $POSITION_LEVEL=="7" || $POSITION_LEVEL=="8" || $POSITION_LEVEL=="9" || $POSITION_LEVEL=="10" ||	
										$POSITION_LEVEL=="11")
										$footer_level = $data3[LEVEL_NAME];
									else
										$footer_level = $POSITION_LEVEL;
//									echo "2..footer_level=$footer_level<br>";

									$flag_dup = false;
//									echo "(".trim($data2[SAH_DOCNO])." && ".trim($data2[SAH_DOCDATE]).")<br>";
									if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCNO])!="-" && trim($data2[SAH_DOCDATE]) && trim($data2[SAH_DOCDATE])!="-") {
										$key = array_search($data2[SAH_DOCNO], $ARR_POSCHECK[$PER_ID][DOC_NO]); 
										if ($key !== false) { // ถ้ามี DOC_NO เหมือนกัน
											if ($ARR_POSCHECK[$PER_ID][DOC_DATE][$key] == $data2[SAH_DOCDATE]) { // และถ้ามี doc_date เหมือนกัน
	//											if ($ARR_POSCHECK[$PER_ID][MOVE_CODE][$key] == "") { // และถ้ามี move_code=
													$flag_dup = true;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SEQ]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SEQ] = $SAH_SEQ_NO;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][MOVE]) 
														$ARR_POSITIONHIS[$PER_ID][$key][MOVE] = $MOV_NAME;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME]) 
														$ARR_POSITIONHIS[$PER_ID][$key][POS_NAME] = $SAH_POSITION;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][POS_NO]) 
														$ARR_POSITIONHIS[$PER_ID][$key][POS_NO] = $SAH_PAY_NO;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]) 
														$ARR_POSITIONHIS[$PER_ID][$key][LEVEL] = $POSITION_LEVEL;
	//												echo "PER_ID=$PER_ID,  key=$key,  POSITION_LEVEL=$POSITION_LEVEL,  footer_level=$footer_level, FOOTER เดิม=".$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]."  LEVEL เดิม=".$ARR_POSITIONHIS[$PER_ID][$key][LEVEL]."<br>";
													if (!$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL]) 
														$ARR_POSITIONHIS[$PER_ID][$key][FOOTERLEVEL] = $footer_level;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SALARY] = $SAH_SALARY;
													if (!$ARR_POSITIONHIS[$PER_ID][$key][SALARY1]) 
														$ARR_POSITIONHIS[$PER_ID][$key][SALARY1] = $SAH_SALARY1;
	//											} // end if check movecode
											} // end if check doc_date
										} // end if check doc_no
									} // end if (trim($data2[SAH_DOCNO]) && trim($data2[SAH_DOCDATE]))
									if (!$flag_dup) { // ถ้าไม่ซ้ำ
										//เก็บลง array ของ SALARYHIS
										if ($BKK_FLAG==1) {
											if ($MOV_NAME) $DOC_NO = $MOV_NAME."\n".$SAH_DOCNO;
											else $DOC_NO = $SAH_DOCNO;
											$ARR_SALARYHIS[$PER_ID][] = array(
																							'DATE'=>$SAH_EFFECTIVEDATE,
																							'SEQ'=>$SAH_SEQ_NO,
																							'MOVE'=>$SAH_POSITION."\n".$SAH_ORG,
																							'POS_NAME'=>$TMP_PL_NAME,
																							'POS_NO'=>$SAH_PAY_NO,
																							'LEVEL'=>$POSITION_LEVEL,
																							'FOOTERLEVEL'=>$footer_level,
																							'SALARY'=>$SAH_SALARY,
																							'SALARY1'=>$SAH_SALARY1,
																							'ORG'=>$SAH_ORG,
																							'DOC_NO'=>$DOC_NO);																			
										} else {
											$ARR_SALARYHIS[$PER_ID][] = array(
																							'DATE'=>$SAH_EFFECTIVEDATE,
																							'SEQ'=>$SAH_SEQ_NO,
																							'MOVE'=>$MOV_NAME,
																							'POS_NAME'=>$TMP_PL_NAME,
																							'POS_NO'=>$SAH_PAY_NO,
																							'LEVEL'=>$POSITION_LEVEL,
																							'FOOTERLEVEL'=>$footer_level,
																							'SALARY'=>$SAH_SALARY,
																							'SALARY1'=>$SAH_SALARY1,
																							'ORG'=>$SAH_ORG,
																							'DOC_NO'=>$SAH_DOCNO);
										}
									} // end if !$flag_dup 
								} // end while
//								for($ii=0; $ii < count($ARR_SALARYHIS[$PER_ID]); $ii++) {
//									echo "loop2..".$ARR_SALARYHIS[$PER_ID][$ii]['DATE']."=>ORG=>".$ARR_SALARYHIS[$PER_ID][$ii]['ORG']."<br>";
//								}
							}// end if

							//######################################
							//รวมประวัติการดำรงตำแหน่ง + การเลื่อนขั้นเงินเดือน
							//######################################
							//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
							$cnt_pos=count($ARR_POSITIONHIS[$PER_ID]);
							$cnt_sal=count($ARR_SALARYHIS[$PER_ID]);
							if ($cnt_pos > 0 && $cnt_sal > 0)
								$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
							else if ($cnt_pos > 0)
								$ARRAY_POH_SAH[$PER_ID] = $ARR_POSITIONHIS[$PER_ID];
							else if ($cnt_sal > 0)
								$ARRAY_POH_SAH[$PER_ID] = $ARR_SALARYHIS[$PER_ID];
							else
								$ARRAY_POH_SAH[$PER_ID] = (array) null;
							// dum
//							echo "PER_ID=$PER_ID cnt-position-his=".count($ARR_POSITIONHIS[$PER_ID])." ,  cnt-salary-his=".count($ARR_SALARYHIS[$PER_ID]).",   cnt-merge=".count($ARRAY_POH_SAH[$PER_ID])."<br>";
							// dum
							unset($ARR_POSITIONHIS);
							unset($ARR_SALARYHIS);

							$DATE_HIS = (array) null;
							$POH_SAH_HIS = (array) null;

							// เรียงข้อมูล ตามวันที่ / เงินเดือน น้อยไปมาก
							for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]); $in++){
								//เก็บค่าวันที่
								$DATE_HIS[] = array('DATE'=>trim($ARRAY_POH_SAH[$PER_ID][$in]['DATE']),
															'SEQ'=>$ARRAY_POH_SAH[$PER_ID][$in]['SEQ'],
															'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
															'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
															'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
															'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
															'FOOTERLEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['FOOTERLEVEL'],
															'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
															'SALARY1'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY1'],
															'ORG'=>$ARRAY_POH_SAH[$PER_ID][$in]['ORG'],
															'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']);
							} // end for
							unset($ARRAY_POH_SAH);
							$DATE = (array) null;
							$SEQ = (array) null;
							$LEVEL = (array) null;
							$FOOTERLEVEL = (array) null;
							$SALARY1 = (array) null;
							foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
								$DATE[$key]  = $value['DATE'];
								$SEQ[$key]  = $value['SEQ'];
//								$MOVE[$key]  = $value['MOVE'];
//								$POS_NAME[$key] = $value['POS_NAME'];
//								$POS_NO[$key]  = $value['POS_NO'];
								$LEVEL[$key]  = $value['LEVEL'];
								$FOOTERLEVEL[$key]  = $value['FOOTERLEVEL'];
//								$SALARY[$key] = $value['SALARY'];
								$SALARY1[$key] = $value['SALARY1'];
//								$DOC_NO[$key]  = $value['DOC_NO'];
//								echo "bf.....$PER_ID-->$key-->DATE=".$DATE[$key]." | SALARY1=".$SALARY1[$key]." | SEQ=".$SEQ[$key]." | LEVEL=".$LEVEL[$key]." | FOOTERLEVEL=".$FOOTERLEVEL[$key]."<br>";
							} // end foreach
//							$msort_result = array_multisort($DATE, SORT_ASC, $SALARY1, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
							$msort_result = array_multisort($DATE, SORT_ASC, $SEQ, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
//							$msort_result = array_multisort($DATE, SORT_ASC, $DATE_HIS);
//							echo "************************* msort_result=$msort_result  (count(DATE_HIS)=".count($DATE_HIS).")<br>";
							foreach ($DATE_HIS as $key => $value) {		// loop นี้เพื่อ echo ค่า เพื่อตรวจสอบหลัง sort แล้ว
								$DATE[$key]  = $value['DATE'];
								$SEQ[$key]  = $value['SEQ'];
//								$MOVE[$key]  = $value['MOVE'];
//								$POS_NAME[$key] = $value['POS_NAME'];
//								$POS_NO[$key]  = $value['POS_NO'];
								$LEVEL[$key]  = $value['LEVEL'];
								$FOOTERLEVEL[$key]  = $value['FOOTERLEVEL'];
								$SALARY[$key] = $value['SALARY'];
//								$SALARY1[$key] = $value['SALARY'];
//								$DOC_NO[$key]  = $value['DOC_NO'];
//								echo "af.....$PER_ID-->$key-->DATE=".$DATE[$key]." | SALARY=".$SALARY[$key]." | SEQ=".$SEQ[$key]." | LEVEL=".$LEVEL[$key]." | FOOTERLEVEL=".$FOOTERLEVEL[$key]."<br>";
							} // end foreach
							$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
							unset($DATE_HIS);

							//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
							if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
//							echo "count merge array=$count_positionhis<br>";
							$positionhis_count=$first_line-1;
							//ถ้าไม่ได้ใส่ line มาคือแสดงทั้งหมด 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
//							$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
							$linecnt = 0;
							 // ส่วนนับจำนวนหน้าทั้งหมด
							$total_page = 0;
//							$linenum += 3;	// (จำนวนบรรทัดตาราง + บรรทัดคำนำหัวตาราง)
							for($in=0; $in < $count_positionhis; $in++){
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										if (strpos($POH_SAH_HIS[$PER_ID][$in]['DATE'],"[2date]")) {
											$abuff = explode("[2date]", $POH_SAH_HIS[$PER_ID][$in]['DATE']); 
											$DATE_POH_SAH = show_date_format($abuff[0],$date_format).$abuff[1];
//											echo "DATE-".$POH_SAH_HIS[$PER_ID][$in]['DATE']."<br>";
										} else {
											$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format);
										}
//										echo "1...DATE_POH_SAH=$DATE_POH_SAH<br>";
										$dtlen = strlen($DATE_POH_SAH);
//										$temptext = $pdf->thaiCutLinePDF($DATE_POH_SAH, $heading_width[POSITIONHIS][0], "\n");
//										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format)." (".($in+1).")";	// ($in+1) เพิ่มเพื่อให้เห็น record number;
									} else {
										$temptext = "";
									}
									$sub_date = explode("\n", $temptext);

									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME1 = trim($data2[LEVEL_NAME]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									$arr_temp = explode(" ", $LEVEL_NAME1);
									//หาชื่อระดับตำแหน่ง 
									$LEVEL_NAME1 ="";
									if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
										$LEVEL_NAME1 =  str_replace("ระดับ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME1 =  $arr_temp[1];
									}
									
									//กำหนดชื่อตำแหน่ง -----------------------
									if(trim($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'])){		//สำหรับการเคลื่อนไหวของ ตน.
//										$f_pos_color = "blue";
										$posname = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME1;	
									}else if(trim($POH_SAH_HIS[$PER_ID][$in]['MOVE'])){		//สำหรับการเลื่อนขั้นเงินเดือน
//										$f_pos_color = "black";
										$posname =  $POH_SAH_HIS[$PER_ID][$in]['MOVE'];
									}else  	$posname = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'];

//									if ($BKK_FLAG==1) {
										$docno = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['DOC_NO']):$POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
										$dlen = strlen($docno);
//										$temptext = $pdf->thaiCutLinePDF($docno, $heading_width[POSITIONHIS][5], "\n");
										$sub_doc = explode("\n", $temptext);
//									} else {
//										$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
//									}
//									echo "PER_ID=$PER_ID, level=".$POH_SAH_HIS[$PER_ID][$in]['LEVEL'].",  last level=".$POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']."<br>";
									$prt_doc_line = count($sub_doc);
									$prt_doc_line = $prt_doc_line - (!trim($sub_doc[$prt_doc_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
										$posname = (($NUMBER_DISPLAY==2)?convert2thaidigit($posname):$posname);
										$dlen = strlen($posname);
//										$temptext = $pdf->thaiCutLinePDF($posname, $heading_width[POSITIONHIS][1], "\n");
										$sub_pos = explode("\n", $temptext);	//	echo "POS---".implode("|",$sub_pos)."  count_line=".count($sub_pos)."  width=".$heading_width[POSITIONHIS][1]."<br>";
									$level = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['LEVEL']):$POH_SAH_HIS[$PER_ID][$in]['LEVEL']);
//									$buff_level = $pdf->thaiCutLinePDF($level, $heading_width[POSITIONHIS][3], "\n");
									$sub_level = explode("\n", $buff_level);	//	echo "LEVEL---".$buff_level."  count_line=".count($sub_level)."<br>";
//									$salary = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['SALARY']):$POH_SAH_HIS[$PER_ID][$in]['SALARY']);
//									echo "POH_SAH_HIS=".$POH_SAH_HIS[$PER_ID][$in]['SALARY']."<br>";
									$salary = $POH_SAH_HIS[$PER_ID][$in]['SALARY'];
//									$buff_salary = $pdf->thaiCutLinePDF($salary, $heading_width[POSITIONHIS][4], "\n");
									$sub_salary = explode("\n", $salary);		// echo "SALARY---".$salary." sub salary=".implode(",",$sub_salary)." count_line=".count($sub_salary)."<br>";

									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_salary_line = count($sub_salary);
									$prt_date_line = count($sub_date);
//									echo "1..prt_date_line=$prt_date_line  sub_date=".implode("&",$sub_date)."<br>";
//									echo "prt_salary_line=$prt_salary_line<br>";
//									echo "0..prt_doc_line=$prt_doc_line, prt_level_line=$prt_level_line, prt_pos_line=$prt_pos_line, prt_salary_line=$prt_salary_line<br>";
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line);
									$prt_max_line = ($prt_salary_line > $prt_max_line ? $prt_salary_line : $prt_max_line);
									$prt_max_line = ($prt_date_line > $prt_max_line ? $prt_date_line : $prt_max_line);
//									echo "1..prt_doc_line=$prt_doc_line, prt_level_line=$prt_level_line, prt_pos_line=$prt_pos_line, prt_salary_line=$prt_salary_line<br>";
//									if (((($linenum + $prt_max_line) * 7) + 14) > ($pdf->h - $at_end_up)) {
//										$total_page++;
//										$linenum = 3;	// จำนวนบรรทัดตาราง
//										echo "*********************************** footer page $total_page<br>";
//									}
//									echo "$PER_ID-->".$DATE_POH_SAH."-".$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']."-->linenum=$linenum , prt_max_line=$prt_max_line , pdf->h=".($pdf->h - $at_end_up)." total_page=$total_page<br>";
									$linenum += $prt_max_line;
							} // จบส่วนนับจำนวนหน้าทั้งหมด
							if ($linenum > 3) $total_page++;	// ถ้ามีการพิมพ์หัวแสดงว่ามีอีก 1 หน้า
							$positionhis_count=$first_line-1;
							//ถ้าไม่ได้ใส่ line มาคือแสดงทั้งหมด 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
//							$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
							$linecnt = 0;
							$last_org = "";
							for($in=0; $in < $count_positionhis; $in++){
									$positionhis_count++;
									$linecnt++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										if (strpos($POH_SAH_HIS[$PER_ID][$in]['DATE'],"[2date]")) {
											$abuff = explode("[2date]", $POH_SAH_HIS[$PER_ID][$in]['DATE']); 
											$DATE_POH_SAH = show_date_format($abuff[0],$date_format).$abuff[1];
//											echo "DATE-".$POH_SAH_HIS[$PER_ID][$in]['DATE']."<br>";
										} else {
											$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format);
										}
//										echo "2...DATE_POH_SAH=$DATE_POH_SAH<br>";
										$dtlen = strlen($DATE_POH_SAH);
//										$temptext = $pdf->thaiCutLinePDF($DATE_POH_SAH, $heading_width[POSITIONHIS][0], "\n");
//										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$date_format)." (".($in+1).")";	// ($in+1) เพิ่มเพื่อให้เห็น record number;
									} else {
										$temptext = "";
									}
									$sub_date = explode("\n", $temptext);

									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME1 = trim($data2[LEVEL_NAME]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									$arr_temp = explode(" ", $LEVEL_NAME1);
									//หาชื่อระดับตำแหน่ง 
									$LEVEL_NAME1 ="";
									if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
										$LEVEL_NAME1 =  str_replace("ระดับ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME1 =  $arr_temp[1];
									}

									//กำหนดชื่อตำแหน่ง -----------------------
									if(trim($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'])){		//สำหรับการเคลื่อนไหวของ ตน.
										$f_pos_color = "blue";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME1;	
									}else if(trim($POH_SAH_HIS[$PER_ID][$in]['MOVE'])){		//สำหรับการเลื่อนขั้นเงินเดือน
										$f_pos_color = "black";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] =  $POH_SAH_HIS[$PER_ID][$in]['MOVE'];
									}

//									if ($BKK_FLAG==1) {
										$docno = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['DOC_NO']):$POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
										$dlen = strlen($docno);
//										$temptext = $pdf->thaiCutLinePDF($docno, $heading_width[POSITIONHIS][5], "\n");
										$sub_doc = explode("\n", $temptext);
//									} else {
//										$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
//									}
//									echo "PER_ID=$PER_ID, level=".$POH_SAH_HIS[$PER_ID][$in]['LEVEL'].",  last level=".$POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']."<br>";
									$prt_doc_line = count($sub_doc);
									$prt_doc_line = $prt_doc_line - (!trim($sub_doc[$prt_doc_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
										$posname = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']):$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']);
										$dlen = strlen($posname);
//										$temptext = $pdf->thaiCutLinePDF($posname, $heading_width[POSITIONHIS][1], "\n");
										$sub_pos = explode("\n", $temptext);	//	echo "POS---".implode("|",$sub_pos)."  count_line=".count($sub_pos)."  width=".$heading_width[POSITIONHIS][1]."<br>";
									$level = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['LEVEL']):$POH_SAH_HIS[$PER_ID][$in]['LEVEL']);
//									$buff_level = $pdf->thaiCutLinePDF($level, $heading_width[POSITIONHIS][3], "\n");
									$sub_level = explode("\n", $buff_level);	//	echo "LEVEL---".$buff_level."  count_line=".count($sub_level)."<br>";
//									$salary = (($NUMBER_DISPLAY==2)?convert2thaidigit($POH_SAH_HIS[$PER_ID][$in]['SALARY']):$POH_SAH_HIS[$PER_ID][$in]['SALARY']);
									$salary = $POH_SAH_HIS[$PER_ID][$in]['SALARY'];
//									$buff_salary = $pdf->thaiCutLinePDF($salary, $heading_width[POSITIONHIS][4], "\n");
									$sub_salary = explode("\n", $salary);	//	echo "SALARY---".$buff_salary."  count_line=".count($sub_salary)."<br>";

									$prt_level_line = count($sub_level);
									$prt_pos_line = 0;
									$prt_pos_line = count($sub_pos);
									$prt_salary_line = count($sub_salary);
//									echo "sub_salary=".implode(",",$sub_salary)."<br>";
									$prt_date_line = count($sub_date);
//									echo "2..prt_date_line=$prt_date_line  sub_date=".implode("&",$sub_date)."<br>";
//									echo "2..prt_doc_line=$prt_doc_line, prt_level_line=$prt_level_line, prt_pos_line=$prt_pos_line, prt_salary_line=$prt_salary_line<br>";
									$prt_pos_line = $prt_pos_line - (!trim($sub_pos[$prt_pos_line-1]) ? 1 -  (!trim($sub_doc[$prt_doc_line-2]) ? 1 : 0) : 0);
									$prt_max_line = ($prt_doc_line > $prt_pos_line ? $prt_doc_line : $prt_pos_line);
									$prt_max_line = ($prt_level_line > $prt_max_line ? $prt_level_line : $prt_max_line);
									$prt_max_line = ($prt_salary_line > $prt_max_line ? $prt_salary_line : $prt_max_line);
									$prt_max_line = ($prt_date_line > $prt_max_line ? $prt_date_line : $prt_max_line);
//									echo "3..prt_doc_line=$prt_doc_line, prt_level_line=$prt_level_line, prt_pos_line=$prt_pos_line, prt_salary_line=$prt_salary_line<br>";
//									if (((($linenum + $prt_max_line) * 7) + 14) > ($pdf->h - $at_end_up)) {
									// $linenum = จำนวนบรรทัดที่พิมพ์ไปแล้ว + $prt_doc_line = จำนวนบรรทัดที่จะพิมพ์สำหรับรายการนี้ แล้วคูณด้วย 7 คือความสูงของบรรทัด
									//  แล้วบวกด้วย 7 คือเว้นเผื่อขอบล่าง 1 บรรทัด (ถ้าจะเว้น 2 บรรทัดก็ บวกด้วย 14)
									
//										echo "PER_ID=$PER_ID, print last level=$last_footer_level<br>";
//										print_footer($last_footer_level);
//										$RTF->new_page();
//									}

									$last_footer_level = $POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL'];

//									echo "$DATE_POH_SAH- prt_max_line=$prt_max_line (linenum=$linenum)<br>";
//									echo "PER_ID=$PER_ID, last level=".$POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']."<br>";
									if ($POH_SAH_HIS[$PER_ID][$in]['ORG'])	$last_org = $POH_SAH_HIS[$PER_ID][$in]['ORG'];
									$buff = $POH_SAH_HIS[$PER_ID][$in]['FOOTERLEVEL']." ".$last_org;
//									if (strpos($buff,"ลำดับที่")!==false)	echo "level=$buff<br>";
									if (strpos($buff,"ชำนาญการ\nพิเศษ")!==false) $buff = str_replace("\n","",$buff);
									if (strpos($buff,"ลำดับที่")===false && strpos($buff,"รักษาการ")===false)	$last_footer_level = $buff;
									
//									for($sub_line_i = 0; 	$sub_line_i < $prt_max_line; $sub_line_i++) {
										$linenum++;
										$border = (($sub_line_i == ($prt_max_line-1) && $sub_line_i == 0) ? "LTBR" : ($sub_line_i == 0 ? "LTR" : ($sub_line_i == ($prt_max_line-1) ? "LBR" : "LR")));

										$arr_data = (array) null;
										$arr_data[] = ($DATE_POH_SAH?$DATE_POH_SAH:"");
										$arr_data[] = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'];
										$arr_data[] = ($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:"");
										$arr_data[] = $POH_SAH_HIS[$PER_ID][$in]['LEVEL'];
										$buff = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['SALARY']);
										$salary_text = "";
										$cntsalary = count($buff);
										for($iii = 0; $iii < $cntsalary; $iii++) {
//											echo "buff [$iii]=".$buff[$iii]."<br>";
											if ($iii==0)
												$salary_text .= ($buff[$iii]?number_format($buff[$iii]):($cntsalary<=0?"-":""));
//												$salary_text .= $buff[$iii];
											else
												$salary_text .= $buff[$iii];
										}
										$arr_data[] = $salary_text;
										$arr_data[] = $POH_SAH_HIS[$PER_ID][$in]['DOC_NO'];
										$data_align = array("L","L","C","C","C","L");
										$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
										if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
/*
										$RTF->open_line($tab_align, false, false);
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
										$RTF->cell(($DATE_POH_SAH?$DATE_POH_SAH:""), $heading_width[$HISTORY_NAME][0], "left", "8", $border);
//										$RTF->cell(($prt_pos ? $prt_pos : ""), $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell(($POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""), $heading_width[$HISTORY_NAME][2], "center", "8", $border);
//										$RTF->cell(($prt_level ? $prt_level : ""), $heading_width[$HISTORY_NAME][3], "center", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['LEVEL'], $heading_width[$HISTORY_NAME][3], "center", "8", $border);
										$salary_text = ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?number_format($POH_SAH_HIS[$PER_ID][$in]['SALARY']):"-");
										$RTF->cell($salary_text, $heading_width[$HISTORY_NAME][4], "center", "8", $border);
//										$RTF->cell(($prt_doc ? $prt_doc : ""), $heading_width[$HISTORY_NAME][5], "left", "8", $border);
										$RTF->cell($POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], $heading_width[$HISTORY_NAME][5], "left", "8", $border);
										$RTF->close_line();
*/
//									} //end for $sub_line_i
							} // end for $in
							
							$RTF->close_tab();

							$RTF->close_section(); 

//							$RTF->open_section(0); 
							$RTF->clear_header();
							$RTF->clear_footer();	
//							$RTF->close_section(); 
							
							if($in<=0){	//}else{
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,16);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), "$page_ch", "center", "8", "LBR");
								$RTF->close_line();
//							} else {
//								$page_no++;
//								print_footer($last_footer_level);
//								$RTF->paragraph();
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
						break;
					case "ABILITY" :
							$RTF->paragraph();
							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ความสามารถพิเศษ ".$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select		b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by	a.ABI_ID ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);

									$border = "RL";

									$RTF->open_line($tab_align, false, false);
									$RTF->set_font($font,14);
									$RTF->color("$fmtTableDetail_col_idx");	// 0=DARKGRAY
									$RTF->cell("ด้าน:$AL_NAME ความสามารถพิเศษ:$ABI_DESC", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTableDetail_bgcol_idx", $border);
									$RTF->close_line();
								} // end while
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell("", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTitle_bgcol_idx", "RBL");
								$RTF->close_line();
							}else{
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell("********** ไม่มีข้อมูล **********", "$page_ch", "center", "$fmtTitle_bgcol_idx", "LBR");
								$RTF->close_line();
							} // end if
						break;
					case "SERVICEHIS" :
							$RTF->new_page();

//							$RTF->add_text("", "left");
							
							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  ประวัติการปฏิบัติราชการพิเศษ ".$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$RTF->paragraph();
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function[$HISTORY_NAME]);
							run_col_format($HISTORY_NAME);
						//	echo "head_text:$head_text1<br>";
							$tab_align = "center";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";
//							print_header($HISTORY_NAME);
							
							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select		a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME, SRH_ORG, SRH_SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID(+) and a.SRT_CODE=d.SRT_CODE(+)
											 order by	a.SRH_STARTDATE ";							   
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$count_servicehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									if ($servicehis_count % 2 == 0) {
										$SRH_STARTDATE2 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME2 = trim($data2[SV_NAME]);
										$SRH_DOCNO2 = trim($data2[SRH_DOCNO]);
										if ($SRH_DOCNO2) $SRH_DOCNO2 = "เอกสาร : $SRH_DOCNO2";
										$SRH_NOTE2 = trim($data2[SRH_NOTE]);
										$SRH_ORG2 = trim($data2[ORG_NAME]);
										if (!$SRH_ORG2) $SRH_ORG2 = trim($data2[SRH_ORG]);
										$SRT_NAME2 = trim($data2[SRT_NAME]);
										if (!$SRT_NAME2) $SRT_NAME2 = trim($data2[SRH_SRT_NAME]);
									} else {
										$SRH_STARTDATE1 = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
										$SV_NAME1 = trim($data2[SV_NAME]);
										$SRH_DOCNO1 = trim($data2[SRH_DOCNO]);
										if ($SRH_DOCNO1) $SRH_DOCNO1 = "เอกสาร : $SRH_DOCNO1";
										$SRH_NOTE1 = trim($data2[SRH_NOTE]);
										$SRH_ORG1 = trim($data2[ORG_NAME]);
										if (!$SRH_ORG1) $SRH_ORG1 = trim($data2[SRH_ORG]);
										$SRT_NAME1 = trim($data2[SRT_NAME]);
										if (!$SRT_NAME1) $SRT_NAME1 = trim($data2[SRH_SRT_NAME]);
									}

									if ($servicehis_count % 2 == 0) {
										$border = "TRBL";
/*
										$RTF->open_line($tab_align, false, false);
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
										$RTF->cell("$SRH_STARTDATE1", $heading_width[$HISTORY_NAME][0], "left", "8", $border);
										$RTF->cell("ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1", $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell("$SRH_STARTDATE2", $heading_width[$HISTORY_NAME][2], "left", "8", $border);
										$RTF->cell("ประเภท : $SV_NAME2   หัวข้อราชการพิเศษ : $SRT_NAME2  เอกสาร : $SRH_DOCNO2", $heading_width[$HISTORY_NAME][3], "left", "8", $border);
										$RTF->close_line();
*/
										$arr_data = (array) null;
										$arr_data[] = $SRH_STARTDATE1;
										$arr_data[] = "ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1";
										$arr_data[] = $SRH_STARTDATE2;
										$arr_data[] = "ประเภท : $SV_NAME2   หัวข้อราชการพิเศษ : $SRT_NAME2  เอกสาร : $SRH_DOCNO2";
										$data_align = array("L","L","L","L");
										$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
										if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
									} // end if ($servicehis_count % 2 == 0)
								} // end while
								if ($servicehis_count % 2 != 0) {
									$border = "TRBL";
/*
									$RTF->open_line($tab_align, false, false);
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
									$RTF->cell("$SRH_STARTDATE1", $heading_width[$HISTORY_NAME][0], "left", "8", $border);
									$RTF->cell("ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1", $heading_width[$HISTORY_NAME][1], "left", "8", $border);
									$RTF->cell("", $heading_width[$HISTORY_NAME][2], "left", "8", $border);
									$RTF->cell("", $heading_width[$HISTORY_NAME][3], "left", "8", $border);
									$RTF->close_line();
*/
									$arr_data = (array) null;
									$arr_data[] = $SRH_STARTDATE1;
									$arr_data[] = "ประเภท : $SV_NAME1   หัวข้อราชการพิเศษ : $SRT_NAME1  เอกสาร : $SRH_DOCNO1";
									$arr_data[] = "";
									$arr_data[] = "";
									$data_align = array("L","L","L","L");
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
								} // end if ($servicehis_count % 2 == 0)
//							} else {
//								$RTF->open_line($tab_align, false, false);
//								$RTF->set_font($font,14);
//								$RTF->color("1");	// 0=DARKGRAY
//								$RTF->cell("********** ไม่มีข้อมูล **********", "$page_ch", "center", "8", "LBR");
//								$RTF->close_line();
							}

							$cmd = " select		a.REH_YEAR, b.REW_NAME, a.REH_OTHER_PERFORMANCE
											 from			PER_REWARDHIS a, PER_REWARD b
											 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE(+)
											 order by	a.REH_YEAR ";							   
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									if ($rewardhis_count % 2 == 0) {
										$REH_YEAR2 = trim($data2[REH_YEAR]);
										$REW_NAME2 = trim($data2[REW_NAME]);
										if (!$REW_NAME2) $REW_NAME2 = trim($data2[REH_OTHER_PERFORMANCE]);
									} else {
										$REH_YEAR1 = trim($data2[REH_YEAR]);
										$REW_NAME1 = trim($data2[REW_NAME]);
										if (!$REW_NAME1) $REW_NAME1 = trim($data2[REH_OTHER_PERFORMANCE]);
									}

									if ($rewardhis_count % 2 == 0) {
										$border = "TRBL";
/*
										$RTF->open_line($tab_align, false, false);
										$RTF->set_font($font,14);
										$RTF->color("1");	// 0=DARKGRAY
										$RTF->cell("$REH_YEAR1", $heading_width[$HISTORY_NAME][0], "left", "8", $border);
										$RTF->cell("$REW_NAME1", $heading_width[$HISTORY_NAME][1], "left", "8", $border);
										$RTF->cell("$REH_YEAR2", $heading_width[$HISTORY_NAME][2], "left", "8", $border);
										$RTF->cell("$REW_NAME2", $heading_width[$HISTORY_NAME][3], "left", "8", $border);
										$RTF->close_line();
*/
										$arr_data = (array) null;
										$arr_data[] = $REH_YEAR1;
										$arr_data[] = $REW_NAME1;
										$arr_data[] = $REH_YEAR2;
										$arr_data[] = $REW_NAME2;
										$data_align = array("L","L","L","L");
										$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
										if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
									} // end if ($rewardhis_count % 2 == 0)
								} // end while
								if ($rewardhis_count % 2 != 0) {
									$border = "TRBL";
/*
									$RTF->open_line($tab_align, false, false);
									$RTF->set_font($font,14);
									$RTF->color("1");	// 0=DARKGRAY
									$RTF->cell("$REH_YEAR1", $heading_width[$HISTORY_NAME][0], "left", "8", $border);
									$RTF->cell("$REW_NAME1", $heading_width[$HISTORY_NAME][1], "left", "8", $border);
									$RTF->cell("", $heading_width[$HISTORY_NAME][2], "left", "8", $border);
									$RTF->cell("", $heading_width[$HISTORY_NAME][3], "left", "8", $border);
									$RTF->close_line();
*/
									$arr_data = (array) null;
									$arr_data[] = $REH_YEAR1;
									$arr_data[] = $REW_NAME1;
									$arr_data[] = "";
									$arr_data[] = "";
									$data_align = array("L","L","L","L");
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
								} // end if ($rewardhis_count % 2 == 0)
//							} else {
//								$RTF->open_line($tab_align, false, false);
//								$RTF->set_font($font,14);
//								$RTF->color("1");	// 0=DARKGRAY
//								$RTF->cell("********** ไม่มีข้อมูล **********", "$page_ch", "center", "8", "LBR");
//								$RTF->close_line();
							}
						break;
					case "SPECIALSKILLHIS" :
							$RTF->paragraph();
							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1).($history_index)."  รายการอื่น ๆ ที่จำเป็น ".$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select		b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by	a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$border = "RL";

									$RTF->open_line($tab_align, false, false);
									$RTF->set_font($font,14);
									$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
									$RTF->cell("ความสามารถพิเศษ : $SS_NAME, $SPS_EMPHASIZE", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTitle_bgcol_idx", $border);
									$RTF->close_line();

								} // end while
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell("", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTitle_bgcol_idx", "RBL");
								$RTF->close_line();
//							}else{
//								$RTF->open_line($tab_align, false, false);
//								$RTF->set_font($font,14);
//								$RTF->color("1");	// 0=DARKGRAY
//								$RTF->cell("********** ไม่มีข้อมูล **********", "$page_ch", "center", "8", "LBR");
//								$RTF->close_line();
							} // end if

							$cmd = " select		NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME, c.PN_NAME as PN_NAME_NEW, a.NH_NAME_NEW, a.NH_SURNAME_NEW 
											 from			PER_NAMEHIS a, PER_PRENAME b, PER_PRENAME c
											 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE(+) and a.PN_CODE_NEW=c.PN_CODE(+)
											 order by	NH_DATE ";							   
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = show_date_format($data2[NH_DATE],$DATE_DISPLAY);
									$OLD_NAME = trim($data2[PN_NAME]).trim($data2[NH_NAME])." ".trim($data2[NH_SURNAME]);
									$NEW_NAME = trim($data2[PN_NAME_NEW]).trim($data2[NH_NAME_NEW])." ".trim($data2[NH_SURNAME_NEW]);
				
									$border = "RL";

									$RTF->open_line($tab_align, false, false);
									$RTF->set_font($font,14);
									$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
									$RTF->cell("วันที่เปลี่ยน : $NH_DATE จาก $OLD_NAME เป็น $NEW_NAME", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTitle_bgcol_idx", $border);
									$RTF->close_line();
								} // end while
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell("", $heading_width[$HISTORY_NAME][0]+$heading_width[$HISTORY_NAME][1], "left", "$fmtTitle_bgcol_idx", "RBL");
								$RTF->close_line();
							}else{
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell("********** ไม่มีข้อมูล **********", "$page_ch", "center", "$fmtTitle_bgcol_idx", "LBR");
								$RTF->close_line();
							}
						break;
					case "DECORATEHIS" :
					
							$tab_align = "center";
							
							$RTF->paragraph();
							$RTF->new_page();

//							$RTF->add_text("", "left");

//							$RTF->open_line($tab_align, false, false);
//							$RTF->set_font($font,14);
//							$RTF->color("1");	// 0=DARKGRAY
//							$RTF->cell($RTF->bold(1).($history_index)."  รายการอื่น ๆ ที่จำเป็น ".$RTF->bold(0), "$page_ch", "left", "8", "TRBL");
//							$RTF->close_line();
//							$RTF->new_line();

							$imgcnt = count($arr_img);
							if ($imgcnt > 0) {
								$start_pic = 0;
								if  ($imgcnt > 7) $start_pic = $imgcnt - 7;	// แถวแสดงได้สูงสุด 7 ภาพ เริ่มนับจากภาพล่าสุดลงมา 7 ภาพ
								else $imgcnt = 7;
								$fix_width = 98; 
								$RTF->open_line($tab_align, false, false);
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$image_x = ($imgx + (($image_w+2) * $j) + 1);
										list($width, $height) = getimagesize($arr_img[$i]); 
										$new_h = $height / $width * $fix_width;
										$new_ratio = floor(100 / $height * $fix_width); 
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
//										$RTF->add_Image($arr_img[$i], "$new_ratio", "center");
//										echo "new_ratio=$new_ratio, w_chr=$w_chr<br>";
										$RTF->cellImage($arr_img[$i], "$new_ratio", "$w_chr", "center", 0, "TRL");
										$j++;
									} else {
										$RTF->cell("", "$w_chr", "center", "0", "TRL");
									} // end if	
								}							
								$RTF->close_line();
								$j = 0;
								$RTF->open_line($tab_align, false, false);
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$RTF->set_font($font,12);
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
//										echo "w_chr=$w_chr, RTF->_font_size()=$fsnum<br>";
										if ($arr_imgshow[$i]==1){  
											$RTF->color("$fmtSubTitle_col_idx");	 
											$RTF->cell($RTF->bold(1)."รูปที่ ".($i+1).$RTF->bold(0), "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RL");
										} else {  
											$RTF->color("$fmtSubTitle_col_idx");  
											$RTF->cell("รูปที่ ".($i+1), "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RL");
										}
										$j++;
									} else {
										$RTF->cell("", "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RL");
									} // end if			
								}
								$RTF->close_line();
								$j = 0;
								$RTF->open_line($tab_align, false, false);
								for($i=$start_pic; $i  < $imgcnt; $i++) {
									if($arr_img[$i]){
										$RTF->set_font($font,12);
										$fsnum = substr($RTF->_font_size(),3);
										$w_chr = floor($fix_width / $fsnum);
										$w_chr = 14;
										if ($arr_imgshow[$i]==1){  
											$RTF->color("$fmtSubTitle_col_idx");	 
											$RTF->cell($RTF->bold(1)."พ.ศ. ".substr($arr_imgsavedate[$i],0,4).$RTF->bold(0), "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RBL");
										} else {  
											$RTF->color("$fmtSubTitle_col_idx");  
											$RTF->cell("พ.ศ. ".substr($arr_imgsavedate[$i],0,4), "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RBL");
										}
										$j++;
									} else {
										$RTF->cell("", "$w_chr", "center", "$fmtSubTitle_bgcol_idx", "RBL");
									} // end if			
								}
								$RTF->close_line();
							}	// if ($imgcnt > 0)
							// จบประวัติรูป

							$RTF->new_page();

//							$RTF->add_text("", "left");

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell("1. ชื่อ  ".$FULLNAME."   วัน เดือน ปี เกิด  ".$PER_BIRTHDATE, "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell("2. ที่อยู่ปัจจุบัน ".($PER_ADD1 ? $PER_ADD1 : $PER_ADD2), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."3. เครื่องราชอิสริยาภรณ์ วันที่ได้รับ วันส่งคืน รวมทั้งเอกสารอ้างอิง".$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							for($i  = 0; $i < count($arr_DEH_SHOW); $i+=2) {
								if ($i==0 && $i >= count($arr_DEH_SHOW)-2) {
									$border = "TRBL";
								} else if ($i==0) {
									$border = "TRL";
								} else if ($i >= count($arr_DEH_SHOW)-2) {
									$border = "RBL";
								} else {
									$border = "RL";
								}
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,14);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell($arr_DEH_SHOW[$i], "49", "left", "$fmtTitle_bgcol_idx", $border);
								$RTF->cell($arr_DEH_SHOW[$i+1], "49", "left", "$fmtTitle_bgcol_idx", $border);
								$RTF->close_line();
							} // end for loop
							break;
					case "ABSENTSUM" :
							$RTF->paragraph();
//							$RTF->new_page();
//							$RTF->add_text("", "left");
							
							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."4. จำนวนวันลาหยุด ขาดราชการ มาสาย ".$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();
							$RTF->paragraph();
							
							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function[$HISTORY_NAME]);
							run_col_format($HISTORY_NAME);
						//	echo "head_text:$head_text1<br>";
							$tab_align = "center";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";

//							print_header($HISTORY_NAME);
							
							$cmd = " select		AS_YEAR, AB_CODE_01, AB_CODE_02, AB_CODE_03, AB_CODE_04, AB_CODE_05, AB_CODE_06, AB_CODE_07, AB_CODE_08, 
																AB_CODE_09, AB_CODE_10, AB_CODE_11, AB_CODE_12, AB_CODE_13, AB_CODE_14, AB_CODE_15
											 from			PER_ABSENTSUM 
											 where		PER_ID=$PER_ID
											 order by	AS_YEAR ";							   
							$count_absentsum = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_AB_NAME = (array) null;
							if($count_absentsum){
								$absentsum_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absentsum_count++;
									$AS_YEAR = trim($data2[AS_YEAR]);
//									echo "AS_YEAR=$AS_YEAR<br>";
									$AB_CODE_01 = $data2[AB_CODE_01];
									$AB_CODE_02 = $data2[AB_CODE_02];
									$AB_CODE_03 = $data2[AB_CODE_03];
									$AB_CODE_04 = $data2[AB_CODE_04];
									$AB_CODE_05 = $data2[AB_CODE_05];
									$AB_CODE_06 = $data2[AB_CODE_06];
									$AB_CODE_07 = $data2[AB_CODE_07];
									$AB_CODE_08 = $data2[AB_CODE_08];
									$AB_CODE_09 = $data2[AB_CODE_09];
									$AB_CODE_10 = $data2[AB_CODE_10];
									$AB_CODE_11 = $data2[AB_CODE_11];
									$AB_CODE_12 = $data2[AB_CODE_12];
									$AB_CODE_13 = $data2[AB_CODE_13];
									$AB_CODE_14 = $data2[AB_CODE_14];
									$AB_CODE_15 = $data2[AB_CODE_15];
									if (!$arr_AB_NAME[$AS_YEAR][$ABIDX]) $arr_AB_NAME[$ABS_YEAR][$ABIDX]=0;
									$arr_AB_NAME[$AS_YEAR][0] += $AB_CODE_01 + $AB_CODE_12;
									$arr_AB_NAME[$AS_YEAR][1] += $AB_CODE_03; 
									$arr_AB_NAME[$AS_YEAR][2] += $AB_CODE_04; 
									$arr_AB_NAME[$AS_YEAR][3] += $AB_CODE_10;
									$arr_AB_NAME[$AS_YEAR][4] += $AB_CODE_13;
									$arr_AB_NAME[$AS_YEAR][5] += $AB_CODE_07;
									$arr_AB_NAME[$AS_YEAR][6] += $AB_CODE_02;
									$arr_AB_NAME[$AS_YEAR][7] += $AB_CODE_05;
									$arr_AB_NAME[$AS_YEAR][8] += $AB_CODE_06;
									$arr_AB_NAME[$AS_YEAR][9] += $AB_CODE_08;
									$arr_AB_NAME[$AS_YEAR][10] += $AB_CODE_09;
									$arr_AB_NAME[$AS_YEAR][11] += $AB_CODE_11;
									$arr_AB_NAME[$AS_YEAR][12] += $AB_CODE_14;
									$arr_AB_NAME[$AS_YEAR][13] += $AB_CODE_15;
								}

								foreach($arr_AB_NAME as $year => $sub_arr) {
//									echo "year=$year<br>";
									if ($year)
										$arr_year[] = $year;
								}
//								$j = 1; $arr_data = (array) null; $data_align = (array) null;
								$numline = count($arr_year);	// จำนวนบรรทัด สำหรับแสดง
								for($yidx = 0; $yidx < $numline; $yidx++) {
//									$arr_data = (array) null; $data_align = (array) null;
//									$arr_data[] = $arr_year[$yidx];
//									$data_align[] = "C";
									
//									$RTF->open_line($tab_align, false, false);
//									$RTF->set_font($font,14);
//									$RTF->color("1");	// 0=DARKGRAY

//									$RTF->cell($arr_year[$yidx], $heading_width[$HISTORY_NAME][0], "center", "8", "TRBL");

									$arr_data = (array) null;
									$data_align = (array) null;
									$arr_data[] = $arr_year[$yidx];
									$data_align[] = "L";
									for($abidx = 0; $abidx < 14; $abidx++) {
										if ($arr_AB_NAME[$arr_year[$yidx]][$abidx]==0) $arr_data[] = "-";
										else $arr_data[] = $arr_AB_NAME[$arr_year[$yidx]][$abidx];
										$data_align[] = "C";
//										$RTF->cell($a_data, $heading_width[$HISTORY_NAME][$abidx+1], "center", "8", "TRBL");
									}
//									$RTF->close_line();
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
								}
							}else{
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,16);
								$RTF->color("1");	// 0=DARKGRAY
								$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), "$page_ch", "center", "8", "RBL");
								$RTF->close_line();
							} // end if

						break;
					case "KPI" :
							$RTF->set_table_font($font, 14);
							$RTF->color($fmtTableDetail_col_idx);	// 0=BLACK
							
							$RTF->paragraph();
//							$RTF->new_page();
//							$RTF->paragraph();

//							$RTF->add_text("", "left");
							
							$RTF->open_line($tab_align, false, false);
							$RTF->set_font($font,14);
							$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
							$RTF->cell($RTF->bold(1)."สรุปผลการประเมินประสิทธิภาพและประสิทธิผลการปฏิบัติงานของข้าราชการของ ".(($NUMBER_DISPLAY==2)?convert2thaidigit($FULLNAME):$FULLNAME).$RTF->bold(0), "$page_ch", "left", "$fmtTitle_bgcol_idx", "TRBL");
							$RTF->close_line();

							$head_text1 = implode(",", $heading_text[$HISTORY_NAME]);
							$head_width1 = implode(",", $heading_width[$HISTORY_NAME]);
							$head_align1 = implode(",", $heading_align[$HISTORY_NAME]);
							$col_function = implode(",", $column_function[$HISTORY_NAME]);
							run_col_format($HISTORY_NAME);
						//	echo "head_text:$head_text1<br>";
							$tab_align = "center";
							$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRBL", $head_align1,"","14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
							if (!$result) echo "****** error ****** on open table for $table<br>";
//							print_header($HISTORY_NAME);

							$where = "";
							if ($KPI_SCORE_CONFIRM==1) $where = "and KF_SCORE_STATUS=1";
							$cmd = " select		KF_END_DATE, KF_CYCLE, TOTAL_SCORE, SALARY_REMARK1, SALARY_REMARK2
											 from			PER_KPI_FORM 
											 where		PER_ID=$PER_ID $where
											 order by	KF_END_DATE ";							   
							$count_kpi_form = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_year = (array) null;
							$arr_rem = (array) null;
							$arr_AB_NAME = (array) null;
							if($count_kpi_form){
								$kpi_form_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$kpi_form_count++;
									$TMP_KF_END_DATE = substr($data2[KF_END_DATE], 0, 10);
									$TMP_KF_YEAR = substr($TMP_KF_END_DATE, 0, 4) + 543;
//									echo "TMP_KF_YEAR=$TMP_KF_YEAR<br>";
									$TMP_KF_CYCLE = $data2[KF_CYCLE] + 0;
									$TMP_TOTAL_SCORE = $data2[TOTAL_SCORE];
									$TMP_REMARK = trim($data2[SALARY_REMARK1])." ".trim($data2[SALARY_REMARK2]);
									if (array_search($TMP_KF_YEAR, $arr_year) === false) 	{
										$arr_year[] = $TMP_KF_YEAR;
										$arr_rem[] = $TMP_REMARK;
									}
									if ($TMP_KF_CYCLE==1) {
										if ($BKK_FLAG==1) {
											if ($TMP_TOTAL_SCORE >= 90) 
												$arr_AB_NAME[$TMP_KF_YEAR][0] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][1] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE < 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][2] = $TMP_TOTAL_SCORE;
										} else {
											if ($TMP_TOTAL_SCORE >= 90) 
												$arr_AB_NAME[$TMP_KF_YEAR][0] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 80) 
												$arr_AB_NAME[$TMP_KF_YEAR][1] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 70) 
												$arr_AB_NAME[$TMP_KF_YEAR][2] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][3] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE < 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][4] = $TMP_TOTAL_SCORE;
										}
									} elseif ($TMP_KF_CYCLE==2) {
										if ($BKK_FLAG==1) {
											if ($TMP_TOTAL_SCORE >= 90) 
												$arr_AB_NAME[$TMP_KF_YEAR][3] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][4] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE < 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][5] = $TMP_TOTAL_SCORE;
										} else {
											if ($TMP_TOTAL_SCORE >= 90) 
												$arr_AB_NAME[$TMP_KF_YEAR][5] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 80) 
												$arr_AB_NAME[$TMP_KF_YEAR][6] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 70) 
												$arr_AB_NAME[$TMP_KF_YEAR][7] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE >= 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][8] = $TMP_TOTAL_SCORE;
											elseif ($TMP_TOTAL_SCORE < 60) 
												$arr_AB_NAME[$TMP_KF_YEAR][9] = $TMP_TOTAL_SCORE;
										}
									}
//									echo "TMP_KF_YEAR=$TMP_KF_YEAR  รอบ=$TMP_KF_CYCLE  คะแนน=$TMP_TOTAL_SCORE<br>";
								}

								$arr_data = (array) null;
								$data_align = (array) null;
								
								$numline = count($arr_year);	// จำนวนบรรทัด สำหรับแสดง
								for($yidx = 0; $yidx < $numline; $yidx++) {	
//									$RTF->open_line($tab_align, false, false);
//									$RTF->set_font($font,14);
//									$RTF->color("1");	// 0=DARKGRAY
									$arr_data[] = $arr_year[$yidx];
//									$RTF->cell($arr_year[$yidx], $heading_width[$HISTORY_NAME][0], "center", "8", "TRBL");
									$data_align[] = "C";
//									echo "arr_year [$yidx]--".$arr_year[$yidx]."<br>";
									if ($BKK_FLAG==1) $maxidx = 6; else $maxidx = 10; 
									for($abidx = 0; $abidx < $maxidx; $abidx++) {
//										echo "yidx=$yidx  year=".$arr_year[$yidx]."  รอบ=$abidx  คะแนน=".$arr_AB_NAME[$arr_year[$yidx]][$abidx]."  remark=".$arr_rem[$yidx]."<br>";
										if ($arr_AB_NAME[$arr_year[$yidx]][$abidx]==0) $arr_data[] = "-";
										else $arr_data[] = $arr_AB_NAME[$arr_year[$yidx]][$abidx];
//										$RTF->cell($dd, $heading_width[$HISTORY_NAME][$abidx+1], "center", "8", "TRBL");
										$data_align[] = "C";
									}
									$arr_data[] = $arr_rem[$yidx];
									$data_align[] = "C";
//									$RTF->cell($arr_rem[$yidx], $heading_width[$HISTORY_NAME][$abidx+1], "center", "8", "TRBL");
//									$arr_data[] = $arr_rem[$yidx];
//									$RTF->close_line();
									$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
								}
							}else{
								$RTF->open_line($tab_align, false, false);
								$RTF->set_font($font,16);
								$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
								$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), "$page_ch", "center", "$fmtTitle_bgcol_idx", "RBL");
								$RTF->close_line();
							} // end if
						break;
				} // end switch
			} // end for
			
			if($data_count < $count_data) 	 $RTF->new_page();
		} // end while
	}else{
		$RTF->set_table_font($font, 14);
		
		$RTF->open_line($tab_align, false, false);
		$RTF->set_font($font,16);
		$RTF->color("$fmtTitle_col_idx");	// 0=DARKGRAY
		$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), "$page_ch", "center", "$fmtTitle_bgcol_idx", "RBL");
		$RTF->close_line();
	} // end if

	$RTF->display($fname);

	function 	do_COLUMN_FORMAT($heading_text, $heading_width, $data_align) {
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>