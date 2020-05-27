<?
	$time1 = time();

	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	function is_leap_year($year)
	{
		 if((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0))){
			return "29";
		 }
		 else {
			return "28";
		 }
	}
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$order_posno_name = "b.POS_NO_NAME";
		$order_posno = "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$order_posno_name = "b.POEM_NO_NAME";
		$order_posno = "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$order_posno_name = "b.POEMS_NO_NAME";
		$order_posno = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$order_posno_name = "b.POT_NO_NAME";
		$order_posno = "b.POT_NO";
	} // end if
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if(trim($search_month)) $show_month = $month_full[($search_month + 0)][TH];			$SHOW_MONTH = str_pad($search_month, 2, "0", STR_PAD_LEFT);		//ประจำเดือน  ตามที่เลือกมา
	if(trim($search_year)){
		$show_year = $search_year;
		$search_year -= 543;
	} // end if
	function chk_num_month(){
		global $show_month,$search_year;
		if(strstr($show_month,"คม")) $loop_DATE = "31";
			else if (strstr($show_month,"ยน")) $loop_DATE = "30";
				else $loop_DATE = is_leap_year($search_year);
			return $loop_DATE;
	}
	$loop_DATE = chk_num_month();
	include ("rpt_R007002_calendar_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	นับจำนวนวันทำการ โดยไม่รวมวันหยุดเสาร์ - อาทิตย์ และวันหยุดราชการ (table )
	$search_date_min = "01/".$SHOW_MONTH."/$show_year";
	$search_date_max = date("t", mktime(0, 0, 0, $search_month, 1, $search_year)) ."/". $SHOW_MONTH ."/$show_year";

	$arr_temp = explode("/", $search_date_min);
	$START_DAY = $arr_temp[0];
	$START_MONTH = $arr_temp[1];
	$START_YEAR = $arr_temp[2] - 543;
	$search_date_min = "$START_YEAR-$START_MONTH-$START_DAY";

	$arr_temp = explode("/", $search_date_max);
	$END_DAY = $arr_temp[0];
	$END_MONTH = $arr_temp[1];
	$END_YEAR = $arr_temp[2] - 543;
	$search_date_max = "$END_YEAR-$END_MONTH-$END_DAY";
	
	$WORK_DAY = 1;
	while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){
		$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));

		if($DPISDB=="odbc") 
			$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		elseif($DPISDB=="oci8") 
			$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		elseif($DPISDB=="mysql")
			$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
		$IS_HOLIDAY = $db_dpis->send_cmd($cmd);

		if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $WORK_DAY++;			//ไม่ใช่วันหยุด นับเป็นวันทำการ
		
		$STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
		$arr_temp = explode("-", $STARTDATE);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
	} // end while

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
		}
		if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				 //
				$list_type_text .= "$search_org_ass_name";
			} // end if
		}
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
    $company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติงานในเวลาราชการของ$PERSON_TYPE[$search_per_type]||ประจำเดือน $show_month พ.ศ.$show_year";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0702";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
    	$ws_head_line1 = (array) null;
    	$ws_head_line2 = (array) null;
    	$ws_head_line3 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[] = $buff[0];
			$ws_head_line2[] = $buff[1];
			$ws_head_line3[] = $buff[2];
		}
		if($loop_DATE == "31"){
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_width = array(5,25,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,15);
		}else if ($loop_DATE == "30"){
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_width = array(5,25,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,15);
		}else if ($loop_DATE == "29"){
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_width = array(5,25,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,15);
		}else if ($loop_DATE == "28"){
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_width = array(5,25,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,8,15);
		}
		
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LR","LR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line3 = array("LR","LR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3,  $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_headalign_line1, $ws_fontfmt_line1, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		
	
	function check_is_holiday($SELECT_YEAR,$SELECT_MONTH,$SELECT_DAY){
		global $DPISDB,$db_dpis3;
		
		if($DPISDB=="odbc") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="oci8") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="mysql")
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		$IS_HOLIDAY = $db_dpis3->send_cmd($cmd_is_holiday);
				
	return $IS_HOLIDAY;
	}
	
	function count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE){
		global $search_month,$SHOW_MONTH,$search_year, $BKK_FLAG;
		
		$arr_temp = explode("-", $ABS_STARTDATE);
		$START_DAY = $arr_temp[2]+0;
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		
		$arr_temp = explode("-", $ABS_ENDDATE);
		$END_DAY = $arr_temp[2]+0;
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[0];
		
		while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){		//<<<<<<<<<<<<<<<<<<<<
			if($AB_COUNT==2){	//ไม่นับ = เสาร์อาทิตย์ และวันหยุดราขการ		//Numeric representation of the day of the week (0=sunday and 6=sat day)
				$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
				$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,$START_DAY);
				if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
			}else if($AB_COUNT==1){	//นับ = เลือกหยุดเสาร์อาทิตย์ และวันหยุดราชการได้
				$ABS_DAY++;
			}
//			echo "AB_COUNT=($AB_COUNT) , $START_YEAR-$START_MONTH-$START_DAY--->ABS_DAY=$ABS_DAY<br>";
			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_STARTDATE);
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} //end while
		if($AB_COUNT==2){	//ไม่นับ = เสาร์อาทิตย์ และวันหยุดราขการ		//Numeric representation of the day of the week (0=sunday and 6=sat day)
			$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
			$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,$START_DAY);
			if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
		}else if($AB_COUNT==1){	//นับ = เลือกหยุดเสาร์อาทิตย์ และวันหยุดราชการได้
			$ABS_DAY++;
		}
		
		return $ABS_DAY;
	}

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2;
		global $arr_content, $data_count;
		global $search_year,$search_date_min, $search_date_max, $SHOW_MONTH, $BKK_FLAG;
		global $search_month,$show_year;
		//global $search_condition;
		$search_condition="";
		unset($arr_search_condition);

		$ABS_CUT_PERIOD = 0;
/*
		if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
		} // end if
		if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) <= '$search_date_max')";
		} // end if
*/
		// เริ่มต้นค้นหาวันลาจากเดือน มกราคม ถึงเดือนปัจจุบันที่ค้นหาของปีนั้น
		//####(ABS_STARTDATE) วันที่เริ่มต้น	//####(ABS_ENDDATE)	วันที่สิ้นสุด
		if($DPISDB=="odbc" || $DPISDB=="mysql")		$column_startdate="LEFT(ABS_STARTDATE, 10)";				$column_enddate="LEFT(ABS_ENDDATE, 10)";
		if($DPISDB=="oci8")												$column_startdate="SUBSTR(ABS_STARTDATE, 1, 10)";		$column_enddate="SUBSTR(ABS_ENDDATE, 1, 10)";
		//สำหรับเดือน มกราคม เช็คให้ไม่เอาของปีที่แล้วมา
		$search_condition_january = "";
		$search_date_minjanuary = $search_year."-01-01";		$search_date_maxjanuary = $search_year."-01-31";
		/*if(trim($search_date_max)==trim($search_date_maxjanuary)){
			$arr_temp = explode("-", $search_date_max);
			$LASTYEAR_JANUARY_DAY = str_pad($arr_temp[2]+0, 2, "0", STR_PAD_LEFT);
			$LASTYEAR_JANUARY_MONTH = $arr_temp[1];
			$LASTYEAR_JANUARY_YEAR = $arr_temp[0]-1;	
			$search_date_lastyear_january = "$LASTYEAR_JANUARY_YEAR-$LASTYEAR_JANUARY_MONTH-$LASTYEAR_JANUARY_DAY";
			$search_condition_january = "$column_startdate > '$search_date_lastyear_january' AND ";
		}*/
		
		$arr_srch_cond = (array) null;
//		$arr_search_condition[] =  "(($column_startdate < '$search_date_min' OR $column_startdate >= '$search_date_min') AND $column_startdate <= '$search_date_max' AND $column_enddate >= '$search_date_min')";
		$arr_srch_cond[] =  "($column_startdate <= '$search_date_min' AND $column_enddate >= '$search_date_max')";	// กรณีวันในฐานข้อมูลค่อมวันที่เลือก
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันเริ่มและวันถึงอยู่ในช่วงที่เลือก 
		$arr_srch_cond[] =  "($column_enddate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันถึงอยู่ในช่วง
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_startdate <= '$search_date_max')";	// กรณีวันเริ่มอยู่ในช่วงที่เลือก 
		$arr_search_condition[] = "(".implode(" OR ",$arr_srch_cond).")";

		//  "($search_condition_date OR ($column_startdate >= '$search_date_min' AND $column_enddate <= '$search_date_max'))";	
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		if ($BKK_FLAG==1) 
			$ab_type = "'1','3','12','13'";			
		else
			$ab_type = "'01','03','10','13'";
		$arr_ab_type = explode(",",$ab_type);
		
		
	for($i=0; $i < count($arr_ab_type); $i++){
		$cmd = " select		a.AB_CODE,b.AB_TYPE ,ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD,b.AB_COUNT 
						 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b 
						 where	PER_ID=$PER_ID and b.AB_TYPE =$arr_ab_type[$i] and a.AB_CODE=b.AB_CODE
						 				$search_condition
						 order by ABS_STARTDATE ";	

	$cnt = $db_dpis2->send_cmd($cmd);
//		echo "normal abs :  $cmd<hr><hr>";
//		$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){		//	$data2 = $db_dpis2->get_array();
			$ABS_ID = trim($data2[ABS_ID]);
			$AB_CODE = trim($data2[AB_TYPE]);
			if ($BKK_FLAG==1) {
				if ($AB_CODE=="1") $AB_CODE = "01";
				elseif ($AB_CODE=="3") $AB_CODE = "03";
				elseif ($AB_CODE=="12") $AB_CODE = "10";
				elseif ($AB_CODE=="13") $AB_CODE = "13";
//				elseif ($AB_CODE=="5") $AB_CODE = "11";
			}
			$AB_COUNT = trim($data2[AB_COUNT]);
			$ABS_DAY = $data2[ABS_DAY];
			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
			
			$arr_temp = explode("-", $ABS_STARTDATE);
			$START_DATE = $arr_temp[2]+0;
			$START_MONTH = $arr_temp[1]+0;
			$START_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_STARTPERIOD==1) $START_DATE .= "/";			//ครึ่งวัน เช้า
			elseif($ABS_STARTPERIOD==2) $START_DATE .= "/";	//ครึ่งวัน บ่าย
			
			$arr_temp = explode("-", $ABS_ENDDATE);
			$END_DATE = $arr_temp[2]+0;
			$END_MONTH = $arr_temp[1]+0;
			$END_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_ENDPERIOD==1) $END_DATE .= "/";				//ครึ่งวัน เช้า
			elseif($ABS_ENDPERIOD==2) $END_DATE .= "/";		//ครึ่งวัน เช้า
			
			if($ABS_STARTDATE == $ABS_ENDDATE){
				$ABS_DURATION[$AB_CODE] = $START_DATE;
			}else if($START_MONTH == $END_MONTH) {
				$num_date = "";
				$START_DATE_1 = "";
				if(strstr($START_DATE,"/")) {$START_DATE = str_replace("/","",$START_DATE);  $num_date[$START_DATE] = $START_DATE;}
				if(strstr($END_DATE,"/")) {$END_DATE = str_replace("/","",$END_DATE);  $num_date[$END_DATE] = $END_DATE;}
				for($i1=$START_DATE; $i1 <= $END_DATE; $i1++){
					if($i1 == $END_DATE && $num_date[$i1] != $i1) $START_DATE_1 .= $i1;
					else if($num_date[$i1] == $i1 && $i1 != $END_DATE) $START_DATE_1 .= $i1."/,";
					else if($num_date[$i1] == $i1 && $i1 == $END_DATE) $START_DATE_1 .= $i1."/";
					else $START_DATE_1 .= $i1.",";
				}
				$ABS_DURATION[$AB_CODE] = $START_DATE_1;
			}else{
				$num_date = "";
				$ABS_DURATION_1 = "";
				if(($search_month == $START_MONTH) || ($search_month == $END_MONTH)) { 
					if($search_month == $START_MONTH){
						$END_DATE = chk_num_month();
					}else{
						$START_DATE = "1";
					}
			    }else {
					$START_DATE = "1";
					$END_DATE = chk_num_month();
				} 
				if(strstr($START_DATE,"/")) {$START_DATE = str_replace("/","",$START_DATE);  $num_date[$START_DATE] = $START_DATE;}
					if(strstr($END_DATE,"/")) {$END_DATE = str_replace("/","",$END_DATE);  $num_date[$END_DATE] = $END_DATE;}
						for($i1=$START_DATE; $i1 <= $END_DATE; $i1++){
						 	if($i1 == $END_DATE && $num_date[$i1] != $i1) $ABS_DURATION_1 .= $i1;
							else if($num_date[$i1] == $i1 && $i1 != $END_DATE) $ABS_DURATION_1 .= $i1."/,";
							else if($num_date[$i1] == $i1 && $i1 == $END_DATE) $ABS_DURATION_1 .= $i1."/";
							else $ABS_DURATION_1 .= $i1.","; 
						}
				$ABS_DURATION[$AB_CODE] = $ABS_DURATION_1;
			}

			$AB_CODE_EXIST .= $AB_CODE.",";
			$ABS_ID_EXIST .= $ABS_ID.",";
			
			$ABS_DURATION[$AB_CODE] = $ABS_DURATION[$AB_CODE]?$ABS_DURATION[$AB_CODE]:"";
			$ABS_DURATION_EXIST[$AB_CODE] .= $ABS_DURATION[$AB_CODE].","; 
			
			//$ABS_DAY = $ABS_DAY?$ABS_DAY:"-";
			//$ABS_DAY_EXIST .= $ABS_DAY.","; 
			
			//${"NUM_".$AB_CODE} +=  1;
			//${"NUM_".$AB_CODE."_EXIST"} .= ${"NUM_".$AB_CODE}.",";
			
			$arr_content[$data_count][AB_CODE] = $AB_CODE_EXIST;
			$arr_content[$data_count][("ABS_ID_".$AB_CODE)] = substr($ABS_ID_EXIST,0,-1);
			$arr_content[$data_count][("DATE_".$AB_CODE)] = substr($ABS_DURATION_EXIST[$AB_CODE],0,-1);			//.= (($arr_content[$data_count][("DATE_".$AB_CODE)])?",":"") . $ABS_DURATION;
			//--------------------------------------------------------------------------------------------------------------
			$START_DATE_CNT = ($search_date_min > $ABS_STARTDATE ? $search_date_min : $ABS_STARTDATE);
			$END_DATE_CNT = ($search_date_max < $ABS_ENDDATE ? $search_date_max : $ABS_ENDDATE);
			//--------------------------------------------------------------------------------------------------------------
			$ABS_DAY_EXIST[$AB_CODE] += count_abs_days($AB_COUNT,$START_DATE_CNT,$END_DATE_CNT);		//ตรวจสอบการลานับวันหยุด เสาร์อาทิตย์
//			$ABS_DAY_EXIST[$AB_CODE] += count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE);		//ตรวจสอบการลานับวันหยุด เสาร์อาทิตย์
			// เอาครึ่งวันไปหัก
			if(($ABS_STARTPERIOD==1 || $ABS_STARTPERIOD==2) || ($ABS_ENDPERIOD==1 || $ABS_ENDPERIOD==2)){//สำหรับลา ครึงวันเช้าครึ่งวันบ่าย
				$ABS_DAY_EXIST[$AB_CODE] = ($ABS_DAY_EXIST[$AB_CODE] - 0.5);
			}
			//--------------------------------------------------------------------------------------------------------------
			$arr_content[$data_count][("DAY_".$AB_CODE)] = $ABS_DAY_EXIST[$AB_CODE];		//+= $ABS_DAY;		= $ABS_DAY?$ABS_DAY:"-";
			$arr_content[$data_count][("NUM_".$AB_CODE)] += 1; //${"NUM_".$AB_CODE."_EXIST"};
		} // end while
	}  //end for

			$search_DATE = chk_num_month();
			if($search_month >= 10) {$TMP_YEAR_s = $search_year;}
			else {$TMP_YEAR_s = $search_year-1; $search_month_e = "0".$search_month;}
			
			$TMP_YEAR_e = $search_year;
			$TMP_START_DATE = $TMP_YEAR_s."-10-01";
			$TMP_END_DATE = $TMP_YEAR_e."-".$search_month_e."-".$search_DATE;
			
			$cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa from PER_ABSENTHIS a, PER_ABSENTTYPE b 
							where PER_ID=$PER_ID and b.AB_TYPE='04' and a.AB_CODE=b.AB_CODE and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE'
							order by ABS_STARTDATE";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$data2 = array_change_key_case($data2, CASE_LOWER);
			$SUM_CODE_04 += $data2[abs_day]+0; 
			$ABS_DAY_MFA += $data2[abs_day_mfa]+0; 
			$cmd = " select VC_DAY from PER_VACATION 
					where VC_YEAR='$show_year'and PER_ID=$PER_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$AB_COUNT_TOTAL = $data2[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
			$AB_COUNT_NUM = $AB_COUNT_TOTAL - $SUM_CODE_04 + $ABS_DAY_MFA;// วันลาสะสมที่เหลือ
			$AB_COUNT_USE = $AB_COUNT_TOTAL - $AB_COUNT_NUM;
			$arr_content[$data_count][AB_COUNT_TOTAL] = $AB_COUNT_TOTAL;
			$arr_content[$data_count][AB_COUNT_USE] = $AB_COUNT_USE;
			$arr_content[$data_count][AB_COUNT_NUM] = $AB_COUNT_NUM;

	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, iif(isnull($order_posno),0,$order_posno), a.PER_NAME , a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e
						 where		$position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, to_number(replace($order_posno,'-','')), a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME,$order_posno,$order_posno_name
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, $order_posno+0, a.PER_NAME, a.PER_SURNAME ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	} 
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd ($count_data)<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	$sheet_limit = 10;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_R007002_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];

		if($search_per_type==1) { $POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]); }
		if($search_per_type==2) { $POS_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]); }
		if($search_per_type==3) { $POS_NO  = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]); }
		if($search_per_type==4) { $POS_NO  = trim($data[POT_NO_NAME]).trim($data[POT_NO]); }

		$arr_content[$data_count][type] = "CONTENT";
		//$arr_content[$data_count][pos_no] = $POS_NO;
		//$arr_content[$data_count][per_id] = $PER_ID;
		$arr_content[$data_count][data_row] = "$data_row";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		count_absent($PER_ID);
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
//		$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type] $NAME||ประจำเดือน $show_month พ.ศ. ".(($NUMBER_DISPLAY==2)?convert2thaidigit($show_year):$show_year)." ||รวมวันมาปฏิบัติราชการ".(($NUMBER_DISPLAY==2)?convert2thaidigit($WORK_DAY):$WORK_DAY)." วันทำการ";

//		print_header();

		$sheet_name = "sheet";
		$data_i = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$DATA_ROW = $arr_content[$data_count][data_row];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			//$POS_NO = $arr_content[$data_count][pos_no];
			//$PER_ID = $arr_content[$data_count][per_id];
			$NAME = $arr_content[$data_count][name];

			$DATE_01 = $arr_content[$data_count][DATE_01];
			$DAY_01 = $arr_content[$data_count][DAY_01];
			//$NUM_01 = $arr_content[$data_count][NUM_01];
			$DATE_03 = $arr_content[$data_count][DATE_03];
			$DAY_03 = $arr_content[$data_count][DAY_03];
			//$NUM_03 = $arr_content[$data_count][NUM_03];
			$DATE_10 = $arr_content[$data_count][DATE_10];
			$DAY_10 = $arr_content[$data_count][DAY_10];
			$DATE_13 = $arr_content[$data_count][DATE_13];
			$DAY_13 = $arr_content[$data_count][DAY_13];
			//$DATE_11 = $arr_content[$data_count][DATE_11];
			//$DAY_11 = $arr_content[$data_count][DAY_11];
			//$DATE_04 = $arr_content[$data_count][DATE_04];
			//$DAY_04 = $arr_content[$data_count][DAY_04];
			$AB_COUNT_TOTAL = $arr_content[$data_count][AB_COUNT_TOTAL];
			$AB_COUNT_USE = $arr_content[$data_count][AB_COUNT_USE];
			$AB_COUNT_NUM = $arr_content[$data_count][AB_COUNT_NUM];

			// เช็คจบที่ข้อมูล $data_limit
			if ($data_count > 0 && ($data_i == $file_limit)) {
//				echo "$data_count>>$xls_fname>>$fname1<br>";
				$workbook->close();
				$arr_file[] = $fname1;
	
				$fnum++;
				$fname1=$fname."_$fnum.xls";
				$workbook = new writeexcel_workbook($fname1);
	
				//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
				//====================== SET FORMAT ======================//
	
				$f_new = true;
			};
			// เช็คจบที่ข้อมูล $data_limit
			if($REPORT_ORDER == "ORG" || ($data_count > 0 && ($data_i  % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_name = "sheet";
					$sheet_no=0; $sheet_no_text="";
					if($data_count > 0) $count_org_ref++;
					$data_i = 0;
					$f_new = false;
				} else if (($data_count > 0 && ($data_i % $data_limit) == 0) || $REPORT_ORDER == "ORG") {
					$sheet_no++;
					if ($sheet_no > $sheet_limit) {
						$workbook->close();
						$arr_file[] = $fname1;
			
						$fnum++;
						$fname1=$fname."_$fnum.xls";
						$workbook = new writeexcel_workbook($fname1);
			
						//====================== SET FORMAT ======================//
						require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
						//====================== SET FORMAT ======================//

						$sheet_name = "sheet";
						$sheet_no=0; $sheet_no_text="";
						if($data_count > 0) $count_org_ref++;
						$data_i = 0;
					} else {
						$sheet_no_text = "_$sheet_no";
					}
				}

//				echo "data_i=$data_i-f_new=$f_new-REPORT_ORDER=$REPORT_ORDER-$sheet_name".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."";
				
				$worksheet = &$workbook->addworksheet("$sheet_name".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);

				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //for($i=0; $i<count($arr_title); $i++){
		
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //if($company_name){
				
				print_header();

			} // end if

			if($REPORT_ORDER == "ORG"){
				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //for($i=0; $i<count($arr_title); $i++){
		
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //if($company_name){
				
				print_header();		

				$arr_data = (array) null;
				$arr_data[] = "<**1**>$NAME";
				//$arr_data[] = "<**1**>$NAME";
				$arr_data[] = "<**1**>$NAME";

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], "L", $border, $merge));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $DATA_ROW;
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				 $arr_data[] = $NAME;
				 $DATE_P = explode(",",$DATE_01); $DATE_P_1 = "";
				 $DATE_K = explode(",",$DATE_03); $DATE_K_1 = "";
				 $DATE_KAD = explode(",",$DATE_13); $DATE_KAD_1 = "";
				 $DATE_S = explode(",",$DATE_10);  $DATE_S_1 = "";
				 for($i=0; $i < count($DATE_P); $i++){
					if($DATE_P[$i]) { 
						if(strstr($DATE_P[$i],"/")) {
							$DATE_P[$i] = str_replace("/","",$DATE_P[$i]);$DATE_P_1[$DATE_P[$i]] = "ป/";
						}else {
							$DATE_P_1[$DATE_P[$i]] = "ป";
						}
					}
				} 
				for($i=0; $i < count($DATE_K); $i++){
					if($DATE_K[$i]) { 
						if(strstr($DATE_K[$i],"/")) {
							$DATE_K[$i] = str_replace("/","",$DATE_K[$i]);$DATE_K_1[$DATE_K[$i]] = "ก/";
						} else {
							$DATE_K_1[$DATE_K[$i]] = "ก";
						}
					}
				}
				for($i=0; $i < count($DATE_KAD); $i++){
					if($DATE_KAD[$i]) { 
						if(strstr($DATE_KAD[$i],"/")) {
							$DATE_KAD[$i] = str_replace("/","",$DATE_KAD[$i]); $DATE_KAD_1[$DATE_KAD[$i]] = "ข/";
						} else {
							$DATE_KAD_1[$DATE_KAD[$i]] = "ข";
						}
					}
				}
				for($i=0; $i < count($DATE_S); $i++){
					if($DATE_S[$i])  {
						if(strstr($DATE_S[$i],"/")) {
							$DATE_S[$i] = str_replace("/","",$DATE_S[$i]);$DATE_S_1[$DATE_S[$i]] = "ส/";
						} else {
							$DATE_S_1[$DATE_S[$i]] = "ส";
						}
					}
				}
			 	for($i=1; $i <= $loop_DATE; $i++){
					/* if($DATE_P_1[$i]) $arr_data[] = $DATE_P_1[$i];
					if($DATE_K_1[$i]) $arr_data[] = $DATE_K_1[$i];
					if($DATE_KAD_1[$i]) $arr_data[] = $DATE_KAD_1[$i];
					if($DATE_S_1[$i]) $arr_data[] = $DATE_S_1[$i]; */
					if(!$DATE_P_1[$i] && !$DATE_K_1[$i] && !$DATE_KAD_1[$i] && !$DATE_S_1[$i]) $arr_data[] = "";
					else $arr_data[] = $DATE_P_1[$i].$DATE_K_1[$i].$DATE_KAD_1[$i].$DATE_S_1[$i];
				}  
				$arr_data[] = $DAY_01;
				$arr_data[] = $DAY_03;
				$arr_data[] = $DAY_13;
				$arr_data[] = $DAY_10;
				$arr_data[] = $AB_COUNT_TOTAL;
				$arr_data[] = $AB_COUNT_USE;
				$arr_data[] = $AB_COUNT_NUM;
				$arr_data[] = "";

//				echo "...........DATA_ROW=$DATA_ROW<br>";
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			}
			$data_i++;
		} // end for				
	}else{
		$xlsRow = 0;
		$arr_data = (array) null;
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";
		$arr_data[] = "<**1**>***** ไม่มีข้อมูล *****";

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;

	ini_set("max_execution_time", 30);
	
	include("../current_location.html");

	if (count($arr_file) > 0) {
		echo "<BR>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "แฟ้ม Excel ที่สร้างมี<BR><br>";
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			echo "---->".($i_file+1).":<a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a><br>";
		}
	}
	echo "<BR>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "เริ่ม:".date("d-m-Y h:i:s",$time1)." จบ:".date("d-m-Y h:i:s",$time2)." ใช้เวลา $show_lap [$tdiff]<br>";
	echo "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "จบการทำงาน<br>";
?>