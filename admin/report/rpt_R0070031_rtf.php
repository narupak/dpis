<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_code = "b.PL_CODE";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);		
		$line_title = " สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_code = "b.PN_CODE";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);		
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_code = "b.EP_CODE";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);		
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_code = "b.TP_CODE";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);		
		$line_title = " ชื่อตำแหน่ง";
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
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "POSNO", "NAME");

	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
				/*case "MINISTRY" :
						if($order_by) $order_by .= ", ";
						$order_by .= "e.ORG_ID_REF";
		
						$heading_name .= " $MINISTRY_TITLE";
					break;
				*/
			case "DEPARTMENT" : 
						if($order_by) $order_by .= ", ";
						$order_by .= "b.DEPARTMENT_ID";
		
						$heading_name .= " $DEPARTMENT_TITLE";
					break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= $line_code; 
				$heading_name .= $line_title;
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
			case "POSNO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "$position_no_name, IIf(IsNull($position_no), 0, CLng($position_no))";
				elseif($DPISDB=="oci8") $order_by .= "$position_no_name, TO_NUMBER($position_no)";
				elseif($DPISDB=="mysql") $order_by .= "$position_no_name, $position_no";
			
				$heading_name .= " $POS_NO_TITLE";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";

				$heading_name .= " $FULLNAME_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_NAME, a.PER_SURNAME";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if($list_person_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_person_type == "CONDITION"){
		if(trim($search_pos_no)){ 
			$arr_search_condition[] = "($position_no like '$search_pos_no%')";
		} // end if
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)) $arr_search_condition[] = "(a.LEVEL_NO >= '$search_min_level')";
		if(trim($search_max_level)) $arr_search_condition[] = "(a.LEVEL_NO <= '$search_max_level')";
	} // end if

	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		$A_YEAR_MIN =  $arr_temp[2];	// ปีงบประมาณ
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		$A_YEAR_MAX =  $arr_temp[2];	// ปีงบประมาณ
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

//	include ("../report/rpt_condition3.php");
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
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
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){ 
			if($select_org_structure==1){		//มอบหมายงาน
				//หา ORG ที่อยู่ภายใต้ DEPARTMENT_ID นี้
				$cmd = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.ORG_ID in (". implode($arr_org_ref, ",") ."))";
			}else{	//ตามกฎหมาย
				$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	include ("rpt_R0070031_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R0070031_rtf.rtf";
//	$fname= "rpt_R0070031_rtf.txt";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการลารายบุคคล" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	function get_day($STARTDATE, $STARTPERIOD, $ENDDATE, $ENDPERIOD, $AB_CODE) {
		global $db_dpis3, $DPISDB;
		
		//	นับจำนวนวันลา โดยไม่รวมวันหยุดเสาร์ - อาทิตย์ และวันหยุดราชการ (table) X
		//วันเริ่มต้น
		$arr_temp = explode("-", $STARTDATE);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		$STARTDATE_ce_era = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
		
		//ถึงวันที่
		$arr_temp = explode("-", $ENDDATE);
		$END_DAY = $arr_temp[2];
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[0];
		$ENDDATE = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;
		$ENDDATE_ce_era = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;	

		// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
		if($DPISDB=="odbc") 
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		elseif($DPISDB=="oci8") 
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		elseif($DPISDB=="mysql")
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		$cntAB = $db_dpis3->send_cmd($cmd);
		$data = $db_dpis3->get_array();
		$AB_COUNT = $data[AB_COUNT];
//		echo "get_day...$START_DATE, $STARTPERIOD, $END_DATE, $ENDPERIOD, $AB_CODE ($AB_COUNT) ($cmd)<br>";
		
//		echo "st ($STARTDATE_ce_era) - en ($ENDDATE_ce_era)<br>";
//		if(!$INVALID_STARTDATE && !$INVALID_ENDDATE){
			//$ABSENT_DAY = 1;
			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
	
			if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***		// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
				$DAY_START_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
				$DAY_END_NUM = date("w", mktime(0, 0, 0, $END_MONTH, $END_DAY, $END_YEAR));
				if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){			// เริ่มต้น / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
					$ABSENT_START_DAY = 0;
				}else if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM != 0 || $DAY_END_NUM != 6)){	// เริ่มต้น เป็นวันเสาร์ - อาทิตย์  / สิ้นสุด เป็นวันธรรมดา (หานอก loop)
					$ABSENT_START_DAY = 0;
				}else if(($DAY_START_NUM != 0 || $DAY_START_NUM != 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){	// เริ่มต้น  เป็นวันธรรมดา / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
					$ABSENT_START_DAY = 0;	
				}else if(($DAY_START_NUM != 0 && $DAY_START_NUM != 6)  && ($DAY_END_NUM != 0 && $DAY_END_NUM != 6)){	 // เริ่มต้น / สิ้นสุด ไม่เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
	//				$ABSENT_START_DAY = 1;	// ลองเอาออกก่อน ใน loop นับวันมันนับทุกวันอยู่แล้ว และ เช็ควันหยุดอยู่ใน loop อยู่แล้ว ไม่รู้ว่าส่วนนี้ทำทำไม
					$ABSENT_START_DAY = 0;
				}
				
				// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				$IS_HOLIDAY_START = $db_dpis3->send_cmd($cmd);
				
				// หาวันสิ้นสุดเป็นวันหยุดหรือไม่ ?
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				$IS_HOLIDAY_END = $db_dpis3->send_cmd($cmd);
				if($IS_HOLIDAY_START || $IS_HOLIDAY_END){		// ถ้าวันเริ่ม / สิ้นสุดเป็นวันหยุดราชการ  ก็ไม่นับตรงนี้
					$ABSENT_START_DAY = 0;
				}
	
				// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
				// คิดคำนวณครึ่งวัน กรณีนี้ไม่นับครึ่งวันเพราะวันเริ่มต้น / สิ้นสุดเป็นวันหยุดอยู่แล้ว ไม่เอามาคิด	
				// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
				$period_err = 0;
				$ABSENT_2_STARTPERIOD = 0;
				$ABSENT_2_ENDPERIOD = 0;
				if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
					if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
						$period_err = 1;	//	"วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขเป็น 'ทั้งวัน'";
					} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$period_err = 2;	//	"วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ โปรดแก้ไขแก้ไข";
					} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$ABSENT_2_STARTPERIOD = 0.5;
					}else{
						if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
							$period_err = 3; // "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
						if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
							$period_err = 4; // "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน โปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
					}
				}else{
					if($STARTPERIOD == 1){
						$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($STARTPERIOD == 2){		//  เริ่มวันแรกครึ่งบ่าย
						if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6) || $IS_HOLIDAY_START){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
							$ABSENT_2_STARTPERIOD = 0;
						}else{  // case ปกติ วันธรรมดา
							$ABSENT_2_STARTPERIOD = 0.5;
						}
					}
					if($ENDPERIOD == 2){
						$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($ENDPERIOD == 1){
						if(($DAY_END_NUM == 0 || $DAY_END_NUM == 6) || $IS_HOLIDAY_END){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
							$ABSENT_2_ENDPERIOD = 0;
						}else{  // case ปกติ วันธรรมดา
							$ABSENT_2_ENDPERIOD = 0.5;
						}
					}
				}
			}else if($AB_COUNT == 1){		// นับวันหยุดเท่านั้น
				$ABSENT_START_DAY = 1; 
				// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
				$period_err = "";
				$ABSENT_2_STARTPERIOD = 0;
				$ABSENT_2_ENDPERIOD = 0;
				if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
					if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
						$period_err = 1;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขเป็น 'ทั้งวัน'";
					} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$period_err = 2;	// "วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ\nโปรดแก้ไขให้ถูกต้อง";
					} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$ABSENT_2_STARTPERIOD = 0.5;
					}else{
						if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
							$period_err = 3;	// "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
						if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
							$period_err = 4;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน\nโปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
					}
				} else {	// วันไม่เท่ากัน
					if($STARTPERIOD == 1){		// เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ
						$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง\nโปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($ENDPERIOD == 2){		// สิ้นสุด เป็นครึ่งวันหลัง
						$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else {
						if($STARTPERIOD == 2){		// เริ่มต้น เป็นครึ่งวันหลัง
							$ABSENT_2_STARTPERIOD = 0.5;	
						}
						if($ENDPERIOD == 1){		// สิ้นสุด เป็นครึ่งวันแรก
							$ABSENT_2_ENDPERIOD = 0.5;
						}
					}            
				}
			}
			
			$ABSENT_START_DAY = 0;	// loop ต่อไปนี้นับใหม่หมดทุกวันแล้ว ไม่จำเป็นต้องใช้ ABSENT_START_DAY อีก
			$TMP_ENDDATE = date("Y-m-d", mktime(0, 0, 0, $END_MONTH, ($END_DAY + 1), $END_YEAR));
			$arr_temp = explode("-", $TMP_ENDDATE);
			$END_DAY1 = $arr_temp[2];
			$END_MONTH1 = $arr_temp[1];
			$END_YEAR1 = $arr_temp[0];
//			echo "bf loop day....$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
			while($START_YEAR!=$END_YEAR1 || $START_MONTH!=$END_MONTH1 || $START_DAY!=$END_DAY1){
				$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
	
				if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***
					if($DPISDB=="odbc") 
						$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					elseif($DPISDB=="oci8") 
						$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					elseif($DPISDB=="mysql")
						$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					$IS_HOLIDAY = $db_dpis3->send_cmd($cmd);
	
					if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $ABSENT_DAY++;
//					echo "loop cnt day.AB_COUNT=2...$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
				}elseif($AB_COUNT == 1){	//นับ
					$ABSENT_DAY++;
//					echo "loop cnt day.AB_COUNT=1...$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
				} // end if
	
				$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
				$arr_temp = explode("-", $TMP_STARTDATE);
				$START_DAY = $arr_temp[2];
				$START_MONTH = $arr_temp[1];
				$START_YEAR = $arr_temp[0];
			} // end while
	
			// สรุปรวมวันสุดท้าย //
			$ABSENT_DAY = ($ABSENT_DAY + $ABSENT_START_DAY);		
	
			// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน	//ถ้าไม่นับวันหยุด ให้ check ว่าวันที่เริ่มต้น / สิ้นสุดที่ลาครึ่งวัน(เช้า/บ่าย)นั้น เป็นวันหยุดราชการ/ เสาร์-อาทิตย์? ถ้าเป็นไม่ต้องเอามาคิด
			$ABSENT_DAY = ($ABSENT_DAY -  ($ABSENT_2_STARTPERIOD + $ABSENT_2_ENDPERIOD));
//			echo "ABSENT_DAY=$ABSENT_DAY<br>";
//		} // end if
		return $ABSENT_DAY;
	} // end function get_day()
	
	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2, $db_dpis3;
		global $arr_content, $data_count;
		global $search_date_min, $search_date_max, $select_org_structure, $DATE_DISPLAY, $BKK_FLAG;
		
		$search_condition = "";
		unset($arr_search_condition);
		if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
		} // end if
		if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
		} // end if
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		
		$cmd = " select		AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD
						 from 		PER_ABSENTHIS
						 where	PER_ID=$PER_ID
						 				$search_condition
						 order by SUBSTR(ABS_STARTDATE, 1, 4), AB_CODE ";
		$count_absent = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "cmd=$cmd<br>";
		$data_row = 0;
		while($data2 = $db_dpis2->get_array()){
			$data_row++;

			$AB_CODE = trim($data2[AB_CODE]);
/*			if ($BKK_FLAG==1) {
				if ($AB_CODE=="1") $AB_CODE = "01";
				elseif ($AB_CODE=="2") $AB_CODE = "12";
				elseif ($AB_CODE=="3") $AB_CODE = "03";
				elseif ($AB_CODE=="4") $AB_CODE = "02";
				elseif ($AB_CODE=="5") $AB_CODE = "11";
				elseif ($AB_CODE=="6") $AB_CODE = "04";
				elseif ($AB_CODE=="7") $AB_CODE = "05";
				elseif ($AB_CODE=="8") $AB_CODE = "07";
				elseif ($AB_CODE=="9") $AB_CODE = "09";
				elseif ($AB_CODE=="10") $AB_CODE = "06";
				elseif ($AB_CODE=="11") $AB_CODE = "13";
				elseif ($AB_CODE=="12") $AB_CODE = "10";
				elseif ($AB_CODE=="13") $AB_CODE = "05";
				elseif ($AB_CODE=="14") $AB_CODE = "06";
				elseif ($AB_CODE=="15") $AB_CODE = "15";
			}*/
			$ABS_DAY = $data2[ABS_DAY];
			$T_ABS_DAY += $ABS_DAY;

			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];

			// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
			$cmd3 = " select  AB_NAME, AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
			$db_dpis3->send_cmd($cmd3);
			$data3 = $db_dpis3->get_array();
			$AB_NAME = (strpos($data3[AB_NAME],"ลา")!==false?"":"ลา").$data3[AB_NAME];
			$AB_COUNT = $data3[AB_COUNT];	//	$AB_COUNT == 1	คือ นับวันหยุด
			if ($AB_COUNT==1) $arr_day = dateDifference($ABS_STARTDATE, $ABS_ENDDATE);
			else $arr_day = (array) null;
			$long_days = ($arr_day[0] ? $arr_day[0]." ปี " : "").($arr_day[1] ? $arr_day[1]." เดือน " : "").($arr_day[2] ? $arr_day[2]." วัน" : "");
//			if ($long_days) echo "$ABS_STARTDATE ถึง $ABS_ENDDATE เป็น ".$long_days."<br>";

			$A_YEAR = substr($ABS_STARTDATE,0,4);
			if ($ABS_STARTDATE > $A_YEAR ."-10-01") 	// ถ้าวันเริ่ม เริ่มหลัง 1 ตุลา ปีนั้น
				$A_YEAR += 1;	// ปีงบประมาณจะเป็นของปีถัดไป
			$B_YEAR = substr($ABS_ENDDATE,0,4);
			if ($ABS_ENDDATE > $B_YEAR ."-10-01") 	// ถ้าวันเริ่ม เริ่มหลัง 1 ตุลา ปีนั้น
				$B_YEAR += 1;	// ปีงบประมาณจะเป็นของปีถัดไป
			$T_YEAR = $B_YEAR - $A_YEAR + 1;

			for($yi = $A_YEAR; $yi <= $B_YEAR; $yi++) {
				if ($A_YEAR == $B_YEAR) $fromOneRow = "";	// รายการเดียวภายในปีงบประมาณเดียว
				else $fromOneRow = $long_days;	// กำหนดค่า จำนวนวันระยะห่างที่ได้
				if ($yi==$A_YEAR) {
					$T_STARTDATE = $ABS_STARTDATE;
					$T_STARTP = $ABS_STARTPERIOD;
				} else {
					$T_STARTDATE = ($yi-1)."-10-01";
					$T_STARTP = 3;	// ทั้งวัน
				}
				if ($yi==$B_YEAR) {
					$B1_YEAR = substr($ABS_ENDDATE,0,4);
					if ($ABS_ENDDATE > $B1_YEAR ."-10-01") { 	// ถ้าวันจบ จบหลัง 1 ตุลา ปีนั้น
						$B1_YEAR += 1;	// ปีงบประมาณจะเป็นของปีถัดไป
						$T_ENDDATE = $ABS_ENDDATE;
						$T_ENDP = $ABS_ENDPERIOD;
					} else {
						$T_ENDDATE = $ABS_ENDDATE;
						$T_ENDP = $ABS_ENDPERIOD;
//						$T_ENDDATE = $B1_YEAR."-09-30";
//						$T_ENDP = 3;	// ทั้งวัน
					}
				} else {
					$T_ENDDATE = $yi."-09-30";
					$T_ENDP = 3;	// ทั้งวัน
				}
				$T_DAY = get_day($T_STARTDATE,$T_STARTP, $T_ENDDATE,$T_ENDP, $AB_CODE);
				
//				echo "get_day...$START_DATE, $STARTPERIOD, $END_DATE, $ENDPERIOD, $AB_CODE ($AB_COUNT) ($cmd)<br>";

				$arr_content[$data_count][type] = "ABSENT";
				$arr_content[$data_count][YEAR] = ($yi+543);
				$arr_content[$data_count][AB_CODE] = $AB_CODE;	
				$arr_content[$data_count][AB_NAME] = $AB_NAME;	
				$arr_content[$data_count][STARTDATE] = $T_STARTDATE;
				$arr_content[$data_count][ENDDATE] = $T_ENDDATE;
				$arr_content[$data_count][DAY] = $T_DAY;
				$arr_content[$data_count][fromOneRow] = $fromOneRow;
//				echo "yi..$yi | $AB_CODE | $T_STARTDATE | $T_ENDDATE | $T_DAY<br>";

				$data_count++;
			} // end for $yi
		} // end while
	} // function
	
	function dateDifference($startDate, $endDate) {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
            if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
                return false;

            $years = date('Y', $endDate) - date('Y', $startDate);

            $endMonth = date('m', $endDate);
            $startMonth = date('m', $startDate);

            // Calculate months
            $months = $endMonth - $startMonth;
            if ($months <= 0)  {
                $months += 12;
                $years--;
            }
            if ($years < 0)
                return false;

            // Calculate the days
            $measure = ($months == 1) ? 'month' : 'months';
            $days = $endDate - strtotime('+' . $months . ' ' . $measure, $startDate);
            $days = date('z', $days);   

			return array($years, $months, $days);
	}
		
	if($DPISDB=="odbc"){
		$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
						 from			(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
						 $search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d
						 where		a.PER_STATUS=1 and $position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+)
											$search_condition
						 order by		$order_by ";
//			$cmd = "select * from (select rownum rnum, q1.* from ( ".$cmd." )  q1) where rnum between 0 and 100";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
						 from			(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
						 $search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo $DEPARTMENT_ID."===".$select_org_structure.":::".$list_type.":::".$cmd;
	//$db_dpis->show_error();
	$data_count = 0;
	while($data = $db_dpis->get_array()){		
		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$ORG_NAME = $data[ORG_NAME];
		$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);

		$arr_content[$data_count][type] = "PERSON";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$data_count++;
		
		count_absent($PER_ID);

	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
//	$msort_result = array_multisort($DATE, SORT_ASC, $SEQ, SORT_NUMERIC, SORT_ASC, $DATE_HIS);
	
	if($count_data){
//		$RTF->open_section(1); 
//		$RTF->set_font($font, 20);
//		$RTF->color("0");	// 0=BLACK
			
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
		
		$T_DAY = 0; $year_i_bak = 0;
		$sum_text = "";
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
//			$ORDER = $arr_content[$data_count][ORDER];
			$year_i = $arr_content[$data_count][YEAR];
			if (!$year_i_bak) {
				$year_i_bak=$year_i;
				$year_show=$year_i;
			}
			$AB_CODE = $arr_content[$data_count][AB_CODE];	
			$AB_NAME = $arr_content[$data_count][AB_NAME];
			if (!$AB_CODE_bak) {
				$AB_CODE_bak=$AB_CODE;
				$AB_NAME_bak=$AB_NAME;
			}
			$SESS_LANG="BDH";
			$STARTDATE = show_date($arr_content[$data_count][STARTDATE]);
			$ENDDATE = show_date($arr_content[$data_count][ENDDATE]);
			$A_DAY = $arr_content[$data_count][DAY];				
			$fromOneRow = $arr_content[$data_count][fromOneRow];
//			echo "$REPORT_ORDER | $NAME | $year_i | $AB_CODE | $STARTDATE | $ENDDATE | $A_DAY<br>";
			if($REPORT_ORDER == "PERSON"){
//				echo "ชื่อ $NAME (T_DAY=$T_DAY)<br>";
				if ($T_DAY) {
					$arr_data = (array) null;
					$arr_data[] = $year_show;
					$arr_data[] = $AB_NAME_bak;
					$arr_data[] = $a; if ($a) $a="";
//					$arr_data[] = $T_DAY;
					$arr_data[] = $sum_text;

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					
					$year_i_bak = 0;
					$AB_CODE_bak = "";
					$AB_NAME_bak = "";
					$T_DAY = 0;
//					$a = "";
					$sum_text = "";
				}
				if($arr_content[($data_count + 1)][type] != "PERSON" && ($data_count < (count($arr_content) - 1))){ 
					$data_align = array("L","L","L","L");

					if ($data_count >  0) $RTF->new_page();	
//					$pdf->print_tab_header();
					$arr_data = (array) null;
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					$RTF->print_tab_header();
				}else{			//กรณีคนที่ไม่มีวันลา แต่เลือกเงื่อนไขให้แสดงจาก เลือกรายบุคคลมากกว่า 1 คน
//					$pdf->SetFont($fontb,'',16);
//					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//					$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
					if ($data_count >  0) $RTF->new_page();	
//					$pdf->print_tab_header();
					$data_align = array("L","L","L","L");

					$arr_data = (array) null;
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
//					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
//					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					$RTF->print_tab_header();

					$arr_data = (array) null;
					$arr_data[] = "<**1**>********** ไม่มีข้อมูลการลา **********";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if
			} else if ($REPORT_ORDER == "ABSENT"){
//				echo ".............$year_i($year_i_bak) | $AB_CODE($AB_CODE_bak) | $A_DAY<br>";
				if ($year_i != $year_i_bak) {
//					echo "$year_i != $year_i_bak<br>";
					$arr_data = (array) null;
					$arr_data[] = $year_show;
					$arr_data[] = $AB_NAME_bak;
					$arr_data[] = $a;
					$arr_data[] = $sum_text;
					
//					echo "***** print  year_show=$year_show - AB_NAME_bak=$AB_NAME_bak - T_DAY=$T_DAY<br>";
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					
					$year_i_bak = $year_i;
					$year_show = $year_i;
					$T_DAY = $A_DAY;
//					echo "1..bf..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					if ($fromOneRow) 
						if ($a==$fromOneRow || $a==",,") $a = ",,";
						else $a = $fromOneRow;
					else $a = $T_DAY." วัน";
//					echo "1..af..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					$sum_text = "$STARTDATE-$ENDDATE ($A_DAY)";
				} else if ($AB_CODE != $AB_CODE_bak) {
//					echo "$AB_CODE != $AB_CODE_bak ($year_i == $year_i_bak)<br>";
					$arr_data = (array) null;
					$arr_data[] = $year_show;
					$arr_data[] = $AB_NAME_bak;
					$arr_data[] = $a;
//					$arr_data[] = $T_DAY;
					$arr_data[] = $sum_text;
					
//					echo "***** print  year_show=$year_show - AB_NAME_bak=$AB_NAME_bak - T_DAY=$T_DAY<br>";
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					
					if ($year_i == $year_i_bak) $year_show = "";
					
//					echo "$AB_CODE != $AB_CODE_bak<br>";
					$T_DAY = $A_DAY;
//					echo "2..bf..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					if ($fromOneRow) 
						if ($a==$fromOneRow || $a==",,") $a = ",,";
						else $a = $fromOneRow;
					else $a = $T_DAY." วัน";
//					echo "2..af..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					$sum_text = "$STARTDATE-$ENDDATE ($A_DAY)";
					$AB_CODE_bak = $AB_CODE;	
					$AB_NAME_bak = $AB_NAME;	
				} else {
					$T_DAY += $A_DAY;
//					echo "3..bf..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					if ($fromOneRow) 
						if ($a==$fromOneRow || $a==",,") $a = ",,";
						else $a = $fromOneRow;
					else $a = $T_DAY." วัน";
//					echo "3..af..T_DAY=$T_DAY  fromOneRow=$fromOneRow  a=$a<br>";
					$sum_text .= ($sum_text ? "     $STARTDATE-$ENDDATE ($A_DAY)" : "$STARTDATE-$ENDDATE ($A_DAY)");
				}
			} // end if			
		} // end for
		if ($T_DAY) {
//			echo "end for $year_show - $AB_NAME_bak - $T_DAY<br>";
			$arr_data = (array) null;
			$arr_data[] = $year_show;
			$arr_data[] = $AB_NAME_bak;
			$arr_data[] = $a;
//			$arr_data[] = $T_DAY;
			$arr_data[] = $sum_text;

//			echo "***** print  year_show=$year_show - AB_NAME_bak=$AB_NAME_bak - T_DAY=$T_DAY<br>";
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$year_i_bak = 0;
			$AB_CODE_bak = "";
			$AB_NAME_bak = "";
			$T_DAY = 0;
//			$a = "";
			$sum_text = "";
		}
	}else{

		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>