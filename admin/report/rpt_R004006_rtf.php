<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";	
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";

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

//	if(!trim($select_org_structure)){
		if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2)";
//	}
		
	if(in_array("SELECT", $list_type)){
		if($SELECTED_PER_ID){		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";	}
	}
	if(in_array("CONDITION", $list_type)){
		//ทั้งข้าราชการ/ลูกจ้าง/พนง.ราชการ
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no'  or trim(e.POEMS_NO)='$search_pos_no') ";
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
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R004006_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$report_title = "ประวัติ $show_persontype";
	$report_code = "R0406";
	$orientation='P';
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	
	//กำหนดค่าประเภทบุคคล
	//$PERSON_TYPE จาก connect_database
	$show_persontype="";
	for($i=0; $i < count($search_per_type); $i++){
		$show_persontype .= $PERSON_TYPE[$search_per_type[$i]]."/";
	}
	$show_persontype = substr($show_persontype,0,-1);
	
	$heading_width[POSITIONHIS][0] = "32";         
	$heading_width[POSITIONHIS][1] = "31";
	$heading_width[POSITIONHIS][2] = "26";          
	$heading_width[POSITIONHIS][3] = "19";
	$heading_width[POSITIONHIS][4] = "30";      
	$heading_width[POSITIONHIS][5] = "17";
	$heading_width[POSITIONHIS][6] = "19";        
 	$heading_width[POSITIONHIS][7] = "26";

	$heading_width[SALARYHIS][0] = "25";
	$heading_width[SALARYHIS][1] = "25";
	$heading_width[SALARYHIS][2] = "150";

	$heading_width[EXTRA_INCOMEHIS][0] = "25";
	$heading_width[EXTRA_INCOMEHIS][1] = "25";
	$heading_width[EXTRA_INCOMEHIS][2] = "125";
	$heading_width[EXTRA_INCOMEHIS][3] = "25";

	$heading_width[EDUCATE][0] = "60";
	$heading_width[EDUCATE][1] = "50";
	$heading_width[EDUCATE][2] = "50";
	$heading_width[EDUCATE][3] = "40";

	$heading_width[TRAINING][0] = "35";
	$heading_width[TRAINING][1] = "88";
	$heading_width[TRAINING][2] = "40";
	$heading_width[TRAINING][3] = "37";

	$heading_width[ABILITY][0] = "100";
	$heading_width[ABILITY][1] = "100";

	$heading_width[HEIR][0] = "100";
	$heading_width[HEIR][1] = "100";

	$heading_width[ABSENTHIS][0] = "55";
	$heading_width[ABSENTHIS][1] = "20";
	$heading_width[ABSENTHIS][2] = "125";

	$heading_width[PUNISHMENT][0] = "70";
	$heading_width[PUNISHMENT][1] = "75";
	$heading_width[PUNISHMENT][2] = "55";

	$heading_width[TIMEHIS][0] = "150";
	$heading_width[TIMEHIS][1] = "25";
	$heading_width[TIMEHIS][2] = "25";

	$heading_width[REWARDHIS][0] = "60";
	$heading_width[REWARDHIS][1] = "140";

	$heading_width[MARRHIS][0] = "70";
	$heading_width[MARRHIS][1] = "50";
	$heading_width[MARRHIS][2] = "40";
	$heading_width[MARRHIS][3] = "40";

	$heading_width[NAMEHIS][0] = "40";
	$heading_width[NAMEHIS][1] = "80";
	$heading_width[NAMEHIS][2] = "80";

	$heading_width[DECORATEHIS][0] = "80";
	$heading_width[DECORATEHIS][1] = "60";
	$heading_width[DECORATEHIS][2] = "60";

	$heading_width[SERVICEHIS][0] = "30";
	$heading_width[SERVICEHIS][1] = "40";
	$heading_width[SERVICEHIS][2] = "50";
	$heading_width[SERVICEHIS][3] = "30";
	$heading_width[SERVICEHIS][4] = "30";
	$heading_width[SERVICEHIS][5] = "20";

	$heading_width[SPECIALSKILLHIS][0] = "80";
	$heading_width[SPECIALSKILLHIS][1] = "120";

	$heading_width[EXTRAHIS][0] = "25";
	$heading_width[EXTRAHIS][1] = "25";
	$heading_width[EXTRAHIS][2] = "125";
	$heading_width[EXTRAHIS][3] = "25";
	
	$heading_width[ACTINGHIS][0] = "45";
	$heading_width[ACTINGHIS][1] = "16";      
	$heading_width[ACTINGHIS][2] = "18";
	$heading_width[ACTINGHIS][3] = "18";
	$heading_width[ACTINGHIS][4] = "12";
	$heading_width[ACTINGHIS][5] = "34";
	$heading_width[ACTINGHIS][6] = "32";
	$heading_width[ACTINGHIS][7] = "32";

	function open_tab_header($HISTORY_NAME){
		global $RTF, $heading_width, $report_title, $DEPARTMENT_TITLE, $ORG_TITLE;
		
		switch($HISTORY_NAME){
			case "POSITIONHIS" :
				$heading_text[$HISTORY_NAME][0] = "$DEPARTMENT_TITLE|";
				$heading_text[$HISTORY_NAME][1] = "$ORG_TITLE|ตามกฎหมาย";
				$heading_text[$HISTORY_NAME][2] = "$ORG_TITLE|ตามมอบหมายงาน";
				$heading_text[$HISTORY_NAME][3] = "วันที่|";
				$heading_text[$HISTORY_NAME][4] = "ตำแหน่ง|";
				$heading_text[$HISTORY_NAME][5] = "เลขที่|ตำแหน่ง";
				$heading_text[$HISTORY_NAME][6] = "อัตรา|เงินเดือน";
				$heading_text[$HISTORY_NAME][7] = "ประเภท|การเคลื่อนไหว";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[7] = (($NUMBER_DISPLAY==2)?"SUM-1-2-3-4-5-6|TNUM-z":"SUM-1-2-3-4-5-6|ENUM-z");
		
				$heading_align = array("C","C","C","C","C","C","C","C");	
		
				$data_align = array("L","C","C","C","C","C","C","C");
				break;			
			case "SALARYHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่";
				$heading_text[$HISTORY_NAME][1] = "เงินเดือน";
				$heading_text[$HISTORY_NAME][2] = "ประเภทการเคลื่อนไหว";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");	
		
				$data_align = array("L","C","C");
				break;			
			case "EXTRA_INCOMEHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่";
				$heading_text[$HISTORY_NAME][1] = "ถึงวันที่";
				$heading_text[$HISTORY_NAME][2] = "ประเภทเงินเพิ่มพิเศษ";
				$heading_text[$HISTORY_NAME][3] = "เงินเพิ่มพิเศษ";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C");	
		
				$data_align = array("C","C","C","C");
				break;			
			case "EDUCATE" :
				$heading_text[$HISTORY_NAME][0] = "วุฒิการศึกษา";
				$heading_text[$HISTORY_NAME][1] = "สาขาวิชาเอก";
				$heading_text[$HISTORY_NAME][2] = "สถานศึกษา";
				$heading_text[$HISTORY_NAME][3] = "ประเทศ";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C");	
		
				$data_align = array("L","L","L","L");
				break;			
			case "TRAINING" :
				$heading_text[$HISTORY_NAME][0] = "ช่วงระยะเวลา";
				$heading_text[$HISTORY_NAME][1] = "หลักสูตร";
				$heading_text[$HISTORY_NAME][2] = "หน่วยงานที่จัด";
				$heading_text[$HISTORY_NAME][3] = "สถานที่";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C");	
		
				$data_align = array("C","L","L","L");
				break;			
			case "ABILITY" :
				$heading_text[$HISTORY_NAME][0] = "ด้านความสามารถพิเศษ";
				$heading_text[$HISTORY_NAME][1] = "ความสามารถพิเศษ";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C");	
		
				$data_align = array("L","L");
				break;			
			case "HEIR" :
				$heading_text[$HISTORY_NAME][0] = "ความสัมพันธ์";
				$heading_text[$HISTORY_NAME][1] = "ชื่อ - สกุล";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C");	
		
				$data_align = array("L","L");
				break;			
			case "ABSENTHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่ลา";
				$heading_text[$HISTORY_NAME][1] = "จำนวนวัน";
				$heading_text[$HISTORY_NAME][2] = "ประเภทการลา";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");
		
				$data_align = array("L","L","L");
				break;			
			case "PUNISHMENT" :
				$heading_text[$HISTORY_NAME][0] = "ฐานความผิด";
				$heading_text[$HISTORY_NAME][1] = "กรณีความผิด";
				$heading_text[$HISTORY_NAME][2] = "วันที่รับโทษ";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");
		
				$data_align = array("L","L","C");
				break;			
			case "TIMEHIS" :
				$heading_text[$HISTORY_NAME][0] = "เวลาทวีคูณ";
				$heading_text[$HISTORY_NAME][1] = "จำนวนวัน";
				$heading_text[$HISTORY_NAME][2] = "จำนวนวันที่ไม่นับ";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");
		
				$data_align = array("C","C","C");
				break;
			case "REWARDHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่ได้รับความดีความชอบ";
				$heading_text[$HISTORY_NAME][1] = "ความดีความชอบ";
				
				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");
		
				$data_align = array("L","L","L");
				break;			
			case "MARRHIS" :
				$heading_text[$HISTORY_NAME][0] = "ชื่อคู่สมรส";
				$heading_text[$HISTORY_NAME][1] = "วันที่สมรส";
				$heading_text[$HISTORY_NAME][2] = "เหตุที่ขาดจากสมรส";
				$heading_text[$HISTORY_NAME][3] = "วันที่ขาดจากสมรส";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C");	
		
				$data_align = array("L","C","L","C");
				break;			
			case "NAMEHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่เปลี่ยนแปลง";
				$heading_text[$HISTORY_NAME][1] = "คำนำหน้า - ชื่อ - สกุล (เดิม)";
				$heading_text[$HISTORY_NAME][2] = "คำนำหน้า - ชื่อ - สกุล (ใหม่)";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");
		
				$data_align = array("C","L","L");
				break;			
			case "DECORATEHIS" :
				$heading_text[$HISTORY_NAME][0] = "ชื่อเครื่องราชฯ ที่ได้รับ";
				$heading_text[$HISTORY_NAME][1] = "วันที่ได้รับ";
				$heading_text[$HISTORY_NAME][2] = "ราชกิจจานุเบกษา";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C");	
		
				$data_align = array("L","C","L");
				break;			
			case "SERVICEHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่";
				$heading_text[$HISTORY_NAME][1] = "ราชการพิเศษ";
				$heading_text[$HISTORY_NAME][2] = "หัวข้อ / โครงการ";
				$heading_text[$HISTORY_NAME][3] = "สถานที่/หน่วยงาน";
				$heading_text[$HISTORY_NAME][4] = "เลขที่คำสั่ง";
				$heading_text[$HISTORY_NAME][5] = "หมายเหตุ";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C","C","C");	
		
				$data_align = array("C","L","L","L","L","L");
				break;			
			case "SPECIALSKILLHIS" :
				$heading_text[$HISTORY_NAME][0] = "ด้านความเชี่ยวชาญพิเศษ";
				$heading_text[$HISTORY_NAME][1] = "เน้นทาง";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C");	
		
				$data_align = array("L","L");
				break;			
			case "EXTRAHIS" :
				$heading_text[$HISTORY_NAME][0] = "วันที่";
				$heading_text[$HISTORY_NAME][1] = "ถึงวันที่";
				$heading_text[$HISTORY_NAME][2] = "ประเภทเงินพิเศษ";
				$heading_text[$HISTORY_NAME][3] = "เงินพิเศษ";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C");	
		
				$data_align = array("C","C","C","C");
				break;			
			case "ACTINGHIS" :
				$heading_text[$HISTORY_NAME][0] = "ประเภท|การเคลื่อนไหว";
				$heading_text[$HISTORY_NAME][1] = "เลขที่คำสั่ง|";
				$heading_text[$HISTORY_NAME][2] = "ตั้งแต่วันที่|";
				$heading_text[$HISTORY_NAME][3] = "ถึงวันนี้|";
				$heading_text[$HISTORY_NAME][4] = "เลขที่|ตำแหน่ง";
				$heading_text[$HISTORY_NAME][5] = "ตำแหน่ง|ในสายงาน";
				$heading_text[$HISTORY_NAME][6] = "$DEPARTMENT_TITLE|";
				$heading_text[$HISTORY_NAME][7] = "$ORG_TITLE|";

				$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
				$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");

				$heading_align = array("C","C","C","C","C","C","C","C");	
		
				$data_align = array("L","C","C","C","C","L","L","L");
			break;	
		} // end switch case
	
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
		
		if (!$COLUMN_FORMAT) {	// ต้องกำหนดเป็น element ให้อยู่ใน form1 ด้วย  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
			$arr_column_map = (array) null;
			$arr_column_sel = (array) null;
			for($i=0; $i < count($heading_text); $i++) {
				$arr_column_map[] = $i;		// link index ของ head 
				$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
			}
			$arr_column_width = $heading_width;	// ความกว้าง
			$arr_column_align = $data_align;		// align
			$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		} else {
			$arrbuff = explode("|",$COLUMN_FORMAT);
			$arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
			$arr_column_sel = explode(",",$arrbuff[1]);	// 1=แสดง	0=ไม่แสดง
			$arr_column_width = explode(",",$arrbuff[2]);	// ความกว้าง
			$arr_column_align = explode(",",$arrbuff[3]);		// align
		}
		
		$total_show_width = 0;
		for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];


		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		if (!$result) echo "****** error ****** on open table for $table<br>";
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
								a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
								c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.SKILL_CODE,
								d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
								d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
								e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
								e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
											c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.SKILL_CODE,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
						 from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f
						 where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						 order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
								a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE, a.PER_TYPE, a.PER_CARDNO,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CERT_OCC,
								c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, c.SKILL_CODE,
								d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
								d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
								e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
								e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$show_persontype = $PERSON_TYPE[$PER_TYPE];
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
				$SKILL_CODE = $data[SKILL_CODE];

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
				$POS_NO = $data[EMP_POS_NO];
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
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} 

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);
			
			$cmd = " select SKILL_NAME, SG_CODE from PER_SKILL where SKILL_CODE = '$SKILL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$SKILL_NAME = trim($data2[SKILL_NAME]);
			$SG_CODE = trim($data2[SG_CODE]);
			
			$cmd = " select SG_NAME from PER_SKILL_GROUP where SG_CODE = '$SG_CODE' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$SG_NAME = trim($data2[SG_NAME]);
			

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[POSITION_LEVEL]);
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];			
			$PER_CERT_OCC = trim($data[PER_CERT_OCC]);

//			$img_file = "";
//			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			
//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
//echo "IMG:$cmd<br>";
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

					if ($PIC_SHOW == '1') {
						$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgshow[] = 1;
						$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
					} else {
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
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

			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);

			if($DPISDB=="odbc"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		((PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
									) left join PER_INSTITUTE d on (trim(a.INS_CODE)=trim(d.INS_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and
									a.EN_CODE=b.EN_CODE(+) and 
									a.EM_CODE=c.EM_CODE(+) and
									a.INS_CODE=d.INS_CODE(+)
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME
						 from		((PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
									) left join PER_INSTITUTE d on (trim(a.INS_CODE)=trim(d.INS_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";	
			} // end if
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EN_NAME = trim($data2[EN_NAME]);
			$EM_NAME = trim($data2[EM_NAME]);
			$INS_NAME = trim($data2[INS_NAME]);
			
//			$pdf->SetFont($font,'',14);
//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//			$pdf->Cell(200,7,"เลขที่ตำแหน่ง  ".($POS_NO?$POS_NO:"-"),0,1,"L");

			$RTF->new_page();
//			$RTF->new_line();
//			$RTF->set_font($font, 14);
//			$RTF->color("0");	// 0=BLACK
//			$RTF->add_text($RTF->bold(1) . "เลขที่ตำแหน่ง  ".($POS_NO?$POS_NO:"-") . $RTF->bold(0) , "center");
//			$RTF->new_line();
//			$RTF->paragraph();
			
			$RTF->open_line();	
			$RTF->cell("", "25", "left", "0");
			$RTF->cell("ลงชื่อ", "10", "right", "0");
			$RTF->cellImage($PIC_SIGN_PER, 40, 35, "center", 0);
			$RTF->cell("ผู้มีหน้าที่จ่ายเงิน", "20", "right", "0");
			$RTF->close_line();

			if($img_file && file_exists($img_file)){
				$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 7);		$image_w = 30;		$image_h = 40;
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} else {
				$img_file="../".$PIC_PATH."shadow.jpg";
				if (file_exists($img_file)){
					$image_x = ($pdf->x + 170);		$image_y = ($pdf->y - 7);		$image_w = 30;		$image_h = 40;
					$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
				}
			} // end if

			$pdf->Cell(95,7,$pdf->Text_print_optimize("ตำแหน่ง  ".(trim($PL_NAME)?($PL_NAME . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME), 100),0,0,"L");
			$pdf->Cell(95,7,"",0,1,"L");
			$pdf->Cell(200,7,$pdf->Text_print_optimize("สังกัด  ".($ORG_NAME?$ORG_NAME:"-"), 100),0,0,"L");
			$pdf->Cell(200,7,"",0,1,"L");
			
			if($ORG_ID_1){
				$pdf->Cell(200,7,$pdf->Text_print_optimize($ORG_NAME_1, 100),0,0,"L");
				$pdf->Cell(200,7,"",0,1,"L");
			} // end if
			if($ORG_ID_2){
				$pdf->Cell(200,7,$pdf->Text_print_optimize($ORG_NAME_2, 100),0,0,"L");
				$pdf->Cell(200,7,"",0,1,"L");
			} // end if
			
			$pdf->Cell(100,7,"ด้านความเชี่ยวชาญ  $SG_NAME",0,0,"L");
			$pdf->Cell(100,7,"สาขาความเชี่ยวชาญ   $SKILL_NAME",0,1,"L");
			
			$pdf->Cell(100,7,"ชื่อ  $FULLNAME ",0,0,"L");
			$pdf->Cell(100,7,"เลขประจำตัวประชาชน   ".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY),0,1,"L");

			$pdf->Cell(100,7,"วันเกิด  ".($PER_BIRTHDATE?$PER_BIRTHDATE:"-"),0,0,"L");
			$pdf->Cell(100,7,"เลขที่ใบประกอบวิชาชีพ   $PER_CERT_OCC",0,1,"L");

			$pdf->Cell(100,7,"เงินเดือน  ".($PER_SALARY?number_format($PER_SALARY):"-"),0,0,"L");
			$pdf->Cell(100,7,"เงินประจำตำแหน่ง  ".($PER_MGTSALARY?number_format($PER_MGTSALARY):"-"),0,1,"L");

			$pdf->Cell(100,7,$pdf->Text_print_optimize("วุฒิ  ".($EN_NAME?$EN_NAME:"-"), 100),0,0,"L");			
			$pdf->Cell(100,7,$pdf->Text_print_optimize("สาขาวิชาเอก  ".($EM_NAME?$EM_NAME:"-"), 100),0,1,"L");

			$pdf->Cell(200,7,"สถานศึกษา  ".($INS_NAME?$INS_NAME:"-"),0,1,"L");

			$pdf->Cell(100,7,"วันเข้ารับราชการ  ".($PER_STARTDATE?$PER_STARTDATE:"-"),0,0,"L");
			$pdf->Cell(100,7,"วันเกษียณอายุราชการ  ".($PER_RETIREDATE?$PER_RETIREDATE:"-"),0,1,"L");
			
			$pdf->AutoPageBreak = false;

			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
				$HISTORY_NAME = $arr_history_name[$history_index];
				switch($HISTORY_NAME){
					case "POSITIONHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับราชการ ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$ORG_NAME_1 = "";
							$ORG_NAME_2 = "";
							$POH_EFFECTIVEDATE = "";
							$PL_NAME = "";
							$LEVEL_NO = "";
							$POH_POS_NO = "";
							$POH_SALARY = "";
/*				
							if($DPISDB=="odbc"){
								$cmd = " select			b.ORG_NAME as ORG_NAME_1, c.ORG_NAME as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
														a.LEVEL_NO, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
										   from			(
															(
																(
																	( 
																		PER_POSITIONHIS a
																		inner join PER_ORG b on (a.ORG_ID_2=b.ORG_ID)
																	) inner join PER_ORG c on (a.ORG_ID_3=c.ORG_ID)
																) left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
															) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
														) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
										   where		a.PER_ID=$PER_ID
										   order by		a.POH_EFFECTIVEDATE
									   	";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			b.ORG_NAME ORG_NAME_1, c.ORG_NAME ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, 
														a.LEVEL_NO, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
										   from			PER_POSITIONHIS a, PER_ORG b, PER_ORG c, PER_LINE d, PER_TYPE e, PER_MGT f
										   where		a.PER_ID=$PER_ID and
														a.ORG_ID_2=b.ORG_ID and 
														a.ORG_ID_3=c.ORG_ID and 
														a.PL_CODE=d.PL_CODE(+) and
														a.PT_CODE=e.PT_CODE(+) and
														a.PM_CODE=f.PM_CODE(+)
										   order by		a.POH_EFFECTIVEDATE
									   	";							   
							}elseif($DPISDB=="mysql"){
							} // end if
*/
							if($DPISDB=="odbc"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, i.PN_NAME , a.POH_ASS_ORG,a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from (
															   (
																  (
																     (
																	   (
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												  			)  left join PER_MOVMENT h on (a.MOV_CODE=h.MOV_CODE)
														)  left join PER_POS_NAME i on (a.PN_CODE=i.PN_CODE)
												   where		a.PER_ID=$PER_ID
												   order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, LEFT(a.POH_ENDDATE,10) desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.POH_ORG2 ORG_NAME_1, a.POH_ORG3 ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, i.PN_NAME , a.POH_ASS_ORG, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from			PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g, PER_MOVMENT h, PER_POS_NAME i
												   where		a.PER_ID=$PER_ID and
																	a.PL_CODE=d.PL_CODE(+) and
																	a.PT_CODE=e.PT_CODE(+) and
																	a.PM_CODE=f.PM_CODE(+) and 
																	a.LEVEL_NO=g.LEVEL_NO(+) and 
																	a.MOV_CODE=h.MOV_CODE(+) and 
																	a.PN_CODE=i.PN_CODE(+)
												   order by	SUBSTR(a.POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(a.POH_ENDDATE,1,10) desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE, d.PL_NAME, a.POH_ASS_ORG,
																	a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL, a.POH_POS_NO, a.POH_SALARY, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, h.MOV_NAME
												   from			(
																		(
																			(
																				(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												 			)  left join PER_MOVMENT h on (a.MOV_CODE=h.MOV_CODE)
												  where		a.PER_ID=$PER_ID
												 order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, LEFT(a.POH_ENDDATE,10) desc ";		
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									if($ORG_NAME_1 != trim($data2[ORG_NAME_1])){
										$ORG_NAME_1 = trim($data2[ORG_NAME_1]);
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$ORG_NAME_1", $border, "L");
									}else{
										$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "", $border, "L");
									} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;
				
									if($ORG_NAME_2 != trim($data2[ORG_NAME_2])){
										$ORG_NAME_2 = trim($data2[ORG_NAME_2]);
										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$ORG_NAME_2", $border, "L");
									}else{
										$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "", $border, "L");
									} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$POH_ASS_ORG = trim($data2[POH_ASS_ORG]);
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$POH_ASS_ORG", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;	
									
									
									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"$POH_EFFECTIVEDATE",$border,0,"C");
				
								//	if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".trim($data2[LEVEL_NO])." ".trim($data2[POH_POS_NO])) ){
										$PL_NAME = trim($data2[PL_NAME]);
										$PN_NAME = trim($data2[PN_NAME]);
										$LEVEL_NO = trim($data2[LEVEL_NO]);
										$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										
						if($PL_NAME) {	$TMP_PL_NAME = (trim($PL_NAME)?($PL_NAME . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME);
										if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME."\n($TMP_PL_NAME)"; }
							elseif($PN_NAME) {	$TMP_PL_NAME = (trim($PN_NAME)?($PN_NAME . $LEVEL_NAME):$LEVEL_NAME);}
										$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, $TMP_PL_NAME, $border, "L");
								//	}else{
						//				$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, "", $border, "L");
							//		} // end if
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;
				
									$POH_POS_NO = trim($data2[POH_POS_NO]);
									$POH_SALARY = $data2[POH_SALARY];
									$MOV_NAME = trim($data2[MOV_NAME]);
				
									$pdf->Cell($heading_width[$HISTORY_NAME][5] ,7,($POH_POS_NO?$POH_POS_NO:"-"),$border,0,"C");
									$pdf->Cell($heading_width[$HISTORY_NAME][6] ,7,number_format($POH_SALARY),$border,0,'R');
									$pdf->MultiCell($heading_width[$HISTORY_NAME][7], 7, ($MOV_NAME?$MOV_NAME:"-"), $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5] + $heading_width[$HISTORY_NAME][6]  + $heading_width[$HISTORY_NAME][7];
									$pdf->y = $start_y;			
										
									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=7; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 15){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($positionhis_count < $count_positionhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($positionhis_count == $count_positionhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SALARYHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการเลื่อนขั้นเงินเดือน ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$SAH_EFFECTIVEDATE = "";
							$SAH_SALARY = "";
							$MOV_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		LEFT(a.SAH_EFFECTIVEDATE,10) desc, a.SAH_SALARY desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		SUBSTR(a.SAH_EFFECTIVEDATE,1,10) desc, a.SAH_SALARY desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.SAH_EFFECTIVEDATE, a.SAH_SALARY, b.MOV_NAME
												 from			PER_SALARYHIS a, PER_MOVMENT b
												 where		a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE 
												 order by		LEFT(a.SAH_EFFECTIVEDATE,10) desc, a.SAH_SALARY desc ";		
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									 $SAH_EFFECTIVEDATE= show_date_format($data2[SAH_EFFECTIVEDATE],$DATE_DISPLAY);
									$SAH_SALARY = $data2[SAH_SALARY];
									$MOV_NAME = trim($data2[MOV_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$SAH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,number_format($SAH_SALARY),$border,0,'R');
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$MOV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($salaryhis_count < $count_salaryhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($salaryhis_count == $count_salaryhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "EXTRAHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเงินเพิ่มพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$EXH_EFFECTIVEDATE = "";
							$EXH_ENDDATE = "";
							$EX_NAME = "";
							$EXH_AMT = "";
							
							$cmd = " select			a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
											 from			PER_EXTRAHIS a, PER_EXTRATYPE b
											 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
											 order by		a.EXH_EFFECTIVEDATE ";							   
							$count_extrahis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_extrahis){
								$extrahis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$extrahis_count++;
									$EXH_EFFECTIVEDATE = show_date_format($data2[EXH_EFFECTIVEDATE],$DATE_DISPLAY);
									$EXH_ENDDATE = show_date_format($data2[EXH_ENDDATE],$DATE_DISPLAY);
									$EX_NAME = trim($data2[EX_NAME]);
									$EXH_AMT = $data2[EXH_AMT];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$EXH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"$EXH_ENDDATE",$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$EX_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,number_format($EXH_AMT),$border,0,'R');

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($extrahis_count < $count_exincomehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($extrahis_count == $count_extrahis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "EDUCATE" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการศึกษา ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$EN_NAME = "";
							$EM_NAME = "";
							$INS_NAME = "";
							$CT_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
										where		a.PER_ID=$PER_ID and
													a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and 
													a.INS_CODE=d.INS_CODE(+) and 
													a.CT_CODE_EDU=e.CT_CODE(+)
										order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";			
							} // end if
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									$INS_NAME = trim($data2[INS_NAME]);
									$CT_NAME = trim($data2[CT_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$EN_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$EM_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$INS_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$CT_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($educatehis_count < $count_educatehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($educatehis_count == $count_educatehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TRAINING" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการฝึกอบรม สัมมนา และดูงาน ",0,1,"L");
							print_header($HISTORY_NAME);

							$TRN_STARTDATE = "";
							$TRN_ENDDATE = "";
							$TR_NAME = "";
							$TRN_PLACE = "";
							
							$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, b.TR_NAME, a.TRN_PLACE, a.TRN_ORG
											 from			PER_TRAINING a, PER_TRAIN b
											 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
											 order by		a.TRN_STARTDATE ";							   
							$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_traininghis){
								$traininghis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$traininghis_count++;
									$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
									$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],$DATE_DISPLAY);
									$TRN_DURATION = "$TRN_STARTDATE - $TRN_ENDDATE";
									if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
									$TR_NAME = trim($data2[TR_NAME]);
									$TRN_PLACE = trim($data2[TRN_PLACE]);
									$TRN_ORG = trim($data2[TRN_ORG]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$TRN_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$TR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$TRN_ORG", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1]  + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$TRN_PLACE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($traininghis_count < $count_traininghis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($traininghis_count == $count_traininghis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABILITY" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ความสามารถพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select			b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by		a.ABI_ID ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$AL_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$ABI_DESC", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($abilityhis_count < $count_abilityhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($abilityhis_count == $count_abilityhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "HEIR" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ข้อมูลทายาท ",0,1,"L");
							print_header($HISTORY_NAME);

							$HR_NAME = "";
							$HEIR_NAME = "";

							$cmd = " select			b.HR_NAME, a.HEIR_NAME
											 from			PER_HEIR a, PER_HEIRTYPE b
											 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
											 order by		a.HEIR_ID ";							   
							$count_heirhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_heirhis){
								$heirhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$heirhis_count++;
									$HR_NAME = trim($data2[HR_NAME]);
									$HEIR_NAME = trim($data2[HEIR_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$HR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$HEIR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($heirhis_count < $count_heirhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($heirhis_count == $count_heirhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "ABSENTHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการลา สาย ขาด ",0,1,"L");
							print_header($HISTORY_NAME);
							
							$ABS_STARTDATE = "";
							$ABS_ENDDATE = "";
							$ABS_DAY = "";
							$AB_NAME = "";

							$cmd = " select			a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
											 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
											 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
											 order by		a.ABS_STARTDATE ";							   
							$count_absenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_absenthis){
								$absenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absenthis_count++;
									$ABS_STARTDATE = show_date_format($data2[ABS_STARTDATE],$DATE_DISPLAY);
									$ABS_ENDDATE = show_date_format($data2[ABS_ENDDATE],$DATE_DISPLAY);
									$ABS_DURATION = "$ABS_STARTDATE - $ABS_ENDDATE";
									if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = "$ABS_STARTDATE";
									$ABS_DAY = $data2[ABS_DAY];
									$AB_NAME = trim($data2[AB_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$ABS_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($ABS_DAY?$ABS_DAY:""),$border,0,"R");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$AB_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($absenthis_count < $count_absenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($absenthis_count == $count_absenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "PUNISHMENT" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติทางวินัย ",0,1,"L");
							print_header($HISTORY_NAME);

							$CR_NAME = "";
							$CRD_NAME = "";
							$PUN_STARTDATE = "";
							$PUN_ENDDATE = "";							
							
							$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
											 order by		a.PUN_STARTDATE ";							   
							$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_punishmenthis){
								$punishmenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$punishmenthis_count++;
									$CR_NAME = trim($data2[CR_NAME]);
									$CRD_NAME = trim($data2[CRD_NAME]);
									$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],$DATE_DISPLAY);
									$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],$DATE_DISPLAY);
									$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
									if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$CR_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$CRD_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$PUN_DURATION", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($punishmenthis_count < $count_punishmenthis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($punishmenthis_count == $count_punishmenthis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "TIMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติเวลาทวีคูณ ",0,1,"L");
							print_header($HISTORY_NAME);

							$TIME_NAME = "";
							$TIME_DAY = "";
							$TIMEH_MINUS = "";
							
							$cmd = " select			b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
											 from			PER_TIMEHIS a, PER_TIME b
											 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
											 order by		a.TIMEH_ID ";							   
							$count_timehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_timehis){
								$timehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$timehis_count++;
									$TIME_NAME = trim($data2[TIME_NAME]);
									$TIME_DAY = $data2[TIME_DAY];
									$TIMEH_MINUS = $data2[TIMEH_MINUS];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$TIME_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,($TIME_DAY?$TIME_DAY:""),$border,0,"R");
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,($TIMEH_MINUS?$TIMEH_MINUS:""),$border,0,"R");

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($timehis_count < $count_timehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($timehis_count == $count_timehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "REWARDHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับความดีความชอบ ",0,1,"L");
							print_header($HISTORY_NAME);

							$REH_DATE = "";
							$REW_NAME = "";
							
							$cmd = " select			a.REH_DATE, b.REW_NAME
											 from			PER_REWARDHIS a, PER_REWARD b
											 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
											 order by		a.REH_DATE ";							   
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									$REH_DATE = show_date_format($data2[REH_DATE],$DATE_DISPLAY);
									$REW_NAME = trim($data2[REW_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$REH_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$REW_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($rewardhis_count < $count_rewardhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($rewardhis_count == $count_rewardhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "MARRHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการสมรส ",0,1,"L");
							print_header($HISTORY_NAME);

							$MAH_NAME = "";
							$MAH_MARRY_DATE = "";
							
							$cmd = " select			MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE
											 from			PER_MARRHIS
											 where		PER_ID=$PER_ID
											 order by		MAH_SEQ ";						   
							$count_marryhis = $db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
				//		echo $cmd;
							if($count_marryhis){
								$marryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$marryhis_count++;
									$MAH_NAME = trim($data2[MAH_NAME]);
									$MAH_DIVORCE_DATE = show_date_format($data2[MAH_DIVORCE_DATE],$DATE_DISPLAY);
									$MAH_MARRY_DATE = show_date_format($data2[MAH_MARRY_DATE],$DATE_DISPLAY);
									$DV_CODE = trim($data2[DV_CODE]);
									
									$cmd = "select DV_NAME from PER_DIVORCE where DV_CODE = '$DV_CODE'";
									$db_dpis3->send_cmd($cmd);
									$data3 = $db_dpis3->get_array();
									$DV_NAME = $data3[DV_NAME];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$MAH_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$MAH_MARRY_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$DV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$MAH_DIVORCE_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2]  + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($marryhis_count < $count_marryhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($marryhis_count == $count_marryhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "NAMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการเปลี่ยนแปลงชื่อ-สกุล ",0,1,"L");
							print_header($HISTORY_NAME);

							$NH_DATE = "";
							$PN_NAME = "";
							$NH_NAME = "";
							$NH_SURNAME = "";
							
							if($DPISDB=="odbc"){
								$cmd = " select  	NH_ID, NH_DATE, ppn.PN_CODE, ppn.PN_NAME, NH_NAME, NH_SURNAME, NH_DOCNO, 
									PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, NH_REMARK
				 from			PER_NAMEHIS pnh, PER_PRENAME ppn
				 where			PER_ID = $PER_ID and pnh.PN_CODE = ppn.PN_CODE  
								order by		pnh.NH_DATE  desc ";;							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
												 from			PER_NAMEHIS a, PER_PRENAME b
												 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
												 order by		a.NH_DATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
												 from			PER_NAMEHIS a, PER_PRENAME b
												 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
												 order by		a.NH_DATE ";		
							} // end if
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = show_date_format($data2[NH_DATE],$DATE_DISPLAY);
									$PN_NAME = trim($data2[PN_NAME]);
									$NH_NAME = trim($data2[NH_NAME]);
									$NH_SURNAME = trim($data2[NH_SURNAME]);
									$FULLNAME = ($PN_NAME?$PN_NAME:"")."$NH_NAME $NH_SURNAME";
									
									$PN_CODE_NEW = trim($data2[PN_CODE_NEW]);
									if($PN_CODE_NEW){
										$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE_NEW' ";
										$db_dpis3->send_cmd($cmd);
										$data3 = $db_dpis3->get_array();
										$PN_NAME_NEW = $data3[PN_NAME];
										} // end if
									$NH_NAME_NEW = trim($data2[NH_NAME_NEW]);
									$NH_SURNAME_NEW = trim($data2[NH_SURNAME_NEW]);
									$FULLNAME_NEW = ($PN_NAME_NEW?$PN_NAME_NEW:"")."$NH_NAME_NEW $NH_SURNAME_NEW";
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$NH_DATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$FULLNAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$FULLNAME_NEW", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($namehis_count < $count_namehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($namehis_count == $count_namehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "DECORATEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเครื่องราชฯ ",0,1,"L");
							print_header($HISTORY_NAME);

							$DC_NAME = "";
							$DEH_DATE = "";
							$DEH_GAZETTE = "";
							
							$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
											 from			PER_DECORATEHIS a, PER_DECORATION b
											 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
											 order by		a.DEH_DATE ";							   
							$count_decoratehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_decoratehis){
								$decoratehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$decoratehis_count++;
									$DC_NAME = trim($data2[DC_NAME]);
									$DEH_DATE = show_date_format($data2[DEH_DATE],$DATE_DISPLAY);
									$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$DC_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$DEH_DATE", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$DEH_GAZETTE", $border, "C");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=2; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($decoratehis_count < $count_decoratehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($decoratehis_count == $count_decoratehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SERVICEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติราชการพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select			a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME, SRH_ORG, SRH_SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID(+)  and a.SRT_CODE=d.SRT_CODE(+)
											 order by		a.SRH_STARTDATE ";					
							$count_servicehis = $db_dpis2->send_cmd($cmd);
							//echo $cmd."<hr>";
							//$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									$SRH_STARTDATE = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
									$SV_NAME = trim($data2[SV_NAME]);
									$SRH_DOCNO = trim($data2[SRH_DOCNO]);
									$SRH_NOTE = trim($data2[SRH_NOTE]);
									$SRH_ORG = trim($data2[ORG_NAME]);
									$SRT_NAME = trim($data2[SRT_NAME]);
									if (!$SRH_ORG) $SRH_ORG = trim($data2[SRH_ORG]);
									if (!$SRT_NAME) $SRT_NAME = trim($data2[SRH_SRT_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$SRH_STARTDATE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$SV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;
									
									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$SRT_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, "$SRH_ORG", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, "$SRH_DOCNO", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, "$SRH_NOTE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2] + $heading_width[$HISTORY_NAME][3] + $heading_width[$HISTORY_NAME][4] + $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=5; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($servicehis_count < $count_servicehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($servicehis_count == $count_servicehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					case "SPECIALSKILLHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ความเชี่ยวชาญพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select			b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by		a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$SS_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, "$SPS_EMPHASIZE", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=1; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($specialskillhis_count < $count_specialskillhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($specialskillhis_count == $count_specialskillhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;
					
			case "EXTRA_INCOMEHIS" :
							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
							$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรับเงินพิเศษ ",0,1,"L");
							print_header($HISTORY_NAME);

							$EXINH_EFFECTIVEDATE = "";
							$EXINH_ENDDATE = "";
							$EXIN_NAME = "";
							$EXINH_AMT = "";
							
							$cmd = " select			a.EXINH_EFFECTIVEDATE, a.EXINH_ENDDATE, b.EXIN_NAME, a.EXINH_AMT
											 from			PER_EXTRA_INCOMEHIS a, PER_EXTRA_INCOME_TYPE b
											 where		a.PER_ID=$PER_ID and a.EXIN_CODE=b.EXIN_CODE 
											 order by		a.EXINH_EFFECTIVEDATE ";							   
							$count_exincomehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_exincomehis){
								$exincomehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$exincomehis_count++;
									$EXINH_EFFECTIVEDATE = show_date_format($data2[EXINH_EFFECTIVEDATE],$DATE_DISPLAY);
									$EXINH_ENDDATE = show_date_format($data2[EXINH_ENDDATE],$DATE_DISPLAY);
									$EXIN_NAME = trim($data2[EXIN_NAME]);
									$EXINH_AMT = $data2[EXINH_AMT];
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"$EXINH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"$EXINH_ENDDATE",$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, "$EXIN_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0] + $heading_width[$HISTORY_NAME][1] + $heading_width[$HISTORY_NAME][2];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,number_format($EXINH_AMT),$border,0,'R');

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=3; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($exincomehis_count < $count_exincomehis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($exincomehis_count == $count_exincomehis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if
						break;			
				case "ACTINGHIS" :
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
						if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
						$pdf->Cell(200,7,($history_index + 1)."  ประวัติการรักษาราชการ/มอบหมายงาน ",0,1,"L");
						print_header($HISTORY_NAME);
							
					if($DPISDB=="odbc") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10), LEFT(ACTH_ENDDATE,10), ACTH_SEQ_NO";
					elseif($DPISDB=="oci8") $order_by = "SUBSTR(ACTH_EFFECTIVEDATE,1,10), SUBSTR(ACTH_ENDDATE,1,10), ACTH_SEQ_NO";			 
					elseif($DPISDB=="mysql") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10), LEFT(ACTH_ENDDATE,10), ACTH_SEQ_NO"; 
					if($DPISDB=="odbc"){
						$cmd = " SELECT 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
											PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
											ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN    
								FROM		PER_ACTINGHIS 
								WHERE		PER_ID=$PER_ID 
								ORDER BY	 $order_by ";
					}elseif($DPISDB=="oci8"){			 
						$cmd = "select 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN,
															PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
															ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
												  from 		PER_ACTINGHIS 
												  where 		PER_ID=$PER_ID
												  order by 	$order_by ";				 					 
					}elseif($DPISDB=="mysql"){
						$cmd = " SELECT 	ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
									PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, ACTH_DOCNO, 
									ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
									FROM		PER_ACTINGHIS 
									WHERE PER_ID=$PER_ID 
									ORDER BY 	$order_by ";
					} // end if
					$count_actinghis = $db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					if($count_actinghis){
								$actinghis_count = 0;
								while($data = $db_dpis2->get_array()){
									$actinghis_count++;
									$TMP_ACTH_ID = $data[ACTH_ID];
									$TMP_ACTH_EFFECTIVEDATE = show_date_format($data[ACTH_EFFECTIVEDATE],$DATE_DISPLAY);
									$TMP_ACTH_ENDDATE = show_date_format($data[ACTH_ENDDATE],$DATE_DISPLAY);
									$TMP_LEVEL_NO = trim($data[LEVEL_NO_ASSIGN]);
									$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_LEVEL_NAME = $data2[POSITION_LEVEL];
									
									$TMP_ACTH_POS_NO = (trim($data[ACTH_POS_NO_ASSIGN]))?   $data[ACTH_POS_NO_ASSIGN] : "-";
									
									$TMP_ORG_NAME_2 = $data[ACTH_ORG2_ASSIGN];
									$TMP_ORG_NAME_3 = $data[ACTH_ORG3_ASSIGN];
									
									$TMP_PL_CODE = trim($data[PL_CODE_ASSIGN]);
									$TMP_PM_CODE = trim($data[PM_CODE_ASSIGN]);
									$TMP_MOV_CODE = $data[MOV_CODE];
									$TMP_ACTH_DOCNO = $data[ACTH_DOCNO];
									
									$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$TMP_MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_MOV_NAME = $data2[MOV_NAME];		
									
									if($PER_TYPE==1){
										$TMP_PL_NAME = "";
										$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";			
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PL_NAME];
									
										$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PM_NAME = trim($data2[PM_NAME]);
									
										$cmd = " 	select PT_NAME from PER_TYPE	where PT_CODE='$TMP_PT_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PT_NAME = trim($data2[PT_NAME]);
									
										if ($RPT_N)
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)? "$TMP_PL_NAME$TMP_LEVEL_NAME" : "") . (trim($TMP_PM_NAME) ?")":"");
										else
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". level_no_format($TMP_LEVEL_NO) . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
									} elseif($PER_TYPE==2){
										$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PN_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PN_NAME];	
									} elseif($PER_TYPE==3){
										$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_EP_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[EP_NAME];		
									} // end if
									if (!$TMP_PL_NAME) $TMP_PL_NAME = trim($data[ACTH_PL_NAME]);
				
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
								
									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, "$actinghis_count.) $TMP_MOV_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0];
									$pdf->y = $start_y;

									$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"$TMP_ACTH_DOCNO",$border,0,"R");
									$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"$TMP_ACTH_EFFECTIVEDATE",$border,0,"L");
									$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"$TMP_ACTH_ENDDATE",$border,0,"L");

									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, "$TMP_ACTH_POS_NO", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, "$TMP_PL_NAME", $border, "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][6], 7, "$TMP_ORG_NAME_2", $border, "L");
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5]+ $heading_width[$HISTORY_NAME][6];
									$pdf->y = $start_y;
									$pdf->MultiCell($heading_width[$HISTORY_NAME][7], 7, "$TMP_ORG_NAME_3", $border, "L");
									$pdf->x = $start_x + $heading_width[$HISTORY_NAME][0]+ $heading_width[$HISTORY_NAME][1]+ $heading_width[$HISTORY_NAME][2]+ $heading_width[$HISTORY_NAME][3]+ $heading_width[$HISTORY_NAME][4]+ $heading_width[$HISTORY_NAME][5]+ $heading_width[$HISTORY_NAME][6]+ $heading_width[$HISTORY_NAME][7];
									$pdf->y = $start_y;

									//================= Draw Border Line ====================
									$line_start_y = $start_y;		$line_start_x = $start_x;
									$line_end_y = $max_y;		$line_end_x = $start_x;
									$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
									for($i=0; $i<=7; $i++){
										$line_start_y = $start_y;		$line_start_x += $heading_width[$HISTORY_NAME][$i];
										$line_end_y = $max_y;		$line_end_x += $heading_width[$HISTORY_NAME][$i];
										$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
									} // end for
									//====================================================
				
									if(($pdf->h - $max_y - 10) < 10){ 
										$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
										if($specialskillhis_count < $count_specialskillhis){
											$pdf->AddPage();
											print_header($HISTORY_NAME);
											$max_y = $pdf->y;
										} // end if
									}else{
										if($specialskillhis_count == $count_specialskillhis) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
									} // end if
									$pdf->x = $start_x;			$pdf->y = $max_y;
								} // end while
							}else{
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if		
				break;	
				} // end switch
			} // end for
			
			if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>