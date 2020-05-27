<? 
	include("../../php_scripts/connect_database.php");
	//include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	

	include ("rpt_data_jethro_inquire_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	
	
	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if

	if(trim($search_org_id)) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
	if(trim($search_pos_no)) $arr_search_condition[] = "(POS_NO like '$search_pos_no%')";

	if(trim($search_pl_code))  {
		$arr_search_condition[] = "(PL_CODE = '$search_pl_code')";
	}
	
	if(trim($search_pm_code))  {
		$arr_search_condition[] = "(PM_CODE = '$search_pm_code')";
	}
	
	if(trim($search_cl_name))  {
		$arr_search_condition[] = "(CL_NAME = '$search_cl_name')";
	}
	if(trim($search_pl_name)) $arr_search_condition[] = "(PL_NAME like '$search_pl_name%')";
	if(trim($search_pm_name)) $arr_search_condition[] = "(PM_NAME like '$search_pm_name%')";
	if(trim($search_org_name)) $arr_search_condition[] = "(ORG_NAME like '$search_org_name%')";
	
	if(trim($search_meeting_time))  $arr_search_condition[] = "(MEETING_TIME = '$search_meeting_time')";
	if(trim($search_meeting_year))  $arr_search_condition[] = "(MEETING_YEAR = '$search_meeting_year')";
	if(trim($search_committee) == 1) $arr_search_condition[] = "(COMMITTEE_RESULT = 1)";
	if(trim($search_committee) == 2) $arr_search_condition[] = "(COMMITTEE_RESULT = 2)";
	if(trim($search_committee) == 3) $arr_search_condition[] = "(COMMITTEE_RESULT = 3)";
	 if ($search_meeting_date_min) {
		$temp_start =  save_date($search_meeting_date_min);
		$arr_search_condition[] = "(MEETING_DATE >= '$temp_start')";
	} // end if
	
	if ($search_meeting_date_max) {
		$temp_end =  save_date($search_meeting_date_max);
		$arr_search_condition[] = "(MEETING_DATE <= '$temp_end')";
	} // end if 
	
	if(trim($search_okp_time))  $arr_search_condition[] = "(OKP_MEETING_TIME = '$search_okp_time')";
	if(trim($search_okp_year))  $arr_search_condition[] = "(OKP_MEETING_YEAR = '$search_okp_year')";
	if(trim($search_okp_committee) == 1) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 1)";
	if(trim($search_okp_committee) == 2) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 2)";
	if(trim($search_okp_committee) == 3) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 3)";
	 if ($search_okp_date_min) {
		$temp_start =  save_date($search_okp_date_min);
		$arr_search_condition[] = "(OKP_MEETING_DATE >= '$temp_start')";
	} // end if
	
	if ($search_okp_date_max) {
		$temp_end =  save_date($search_okp_date_max);
		$arr_search_condition[] = "(OKP_MEETING_DATE <= '$temp_end')";
	} // end if 
	
	$search_condition = "";
	if(count($arr_search_condition)) 	$search_condition 	= " where " . implode(" and ", $arr_search_condition);

	$search_condition = str_replace(" where ", " and ", $search_condition);
	$cmd =" select count(a.JETHRO_ID) as count_data 
					from 		PER_JETHRO a, PER_ORG b
					where		a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition  ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$count_data = $data[count_data];	
//echo "$count_data : $cmd";
	
//echo "$count_data : $cmd";
	

  	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;

	if($order_by==1){	//ชื่อ-สกุล
		$order_str = "ORDER BY MEETING_YEAR $SortType[$order_by], MEETING_TIME $SortType[$order_by], POS_NO $SortType[$order_by]";
  	}elseif($order_by==4) {	//ตำแหน่ง - ระดับ
		$order_str = "ORDER BY AB_NAME  ".$SortType[$order_by];
  	} elseif($order_by==3) {	
		$order_str = "ORDER BY f.ORG_NAME ".$SortType[$order_by];
	} elseif($order_by==2) {	//ชื่อทุน / หลักสูตร
		$order_str =  "ORDER BY a.SCH_CODE  ".$SortType[$order_by];
	}elseif($order_by==5) {	//สถานศึกาษา
		$order_str = "ORDER BY INS_CODE ".$SortType[$order_by];
  	} elseif($order_by==6) {	//วันที่เริ่มศึกษา
		$order_str = "ORDER BY SC_STARTDATE  ".$SortType[$order_by];
	}elseif($order_by==7) {	//วันที่สิ้นสุดระยะเวลาศึกษา
		$order_str = "ORDER BY SC_ENDDATE ".$SortType[$order_by];
	}

	$company_name = "";
	if(!$search_ministry_name) $search_ministry_name = "ระดับกระทรวง";
	if($search_department_name) $search_department_name ="||".$search_department_name;
	$report_title = "การพิจารณากำหนดตำแหน่งระดับสูงของ $search_ministry_name $search_department_name";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "jethro";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = (array) null;	$ws_head_line2 = (array) null; 	$ws_head_line3 = (array) null;
		$ws_colmerge_line1 = (array) null;	$ws_colmerge_line2 = (array) null; 	$ws_colmerge_line3 = (array) null;
		$ws_border_line1 = (array) null;	$ws_border_line2 = (array) null; 	$ws_border_line3 = (array) null;
		$ws_width = (array) null;
	for($i=0; $i < count($heading_text); $i++) {
		$buff = explode("|",$heading_text[$i]);
		$ws_head_line1[] = $buff[0];
		$ws_head_line2[] = $buff[1];
		$ws_head_line3[] = $buff[2];
		$ws_colmerge_line1[] = 0;
		$ws_colmerge_line2[] = 0;
		$ws_colmerge_line3[] = 0;
		$ws_border_line1[] = "TLR";
		$ws_border_line2[] = "LBR";
		$ws_border_line3[] = "LBR";
		$ws_fontfmt_line1[] = "B";
		$ws_headalign_line1[] = "C";
	}
	$ws_colmerge_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
	$ws_colmerge_line2 = array(0,0,1,1,1,1,1,1,1,1,1,0,0,0,0);
	$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_border_line1 = array("TL","T","T","T","T","T","T","T","T","T","T","T","TL","T","TR");
	$ws_border_line2 = array("TL","TL","TL","T","T","T","T","TL","T","T","T","TL","TL","TL","TLR");
	$ws_border_line3 = array("LB","LB","TLB","TLB","TLB","TLB","TLB","TLB","TLB","TLB","TLB","LB","LB","LB","LBR");
	$ws_width = array(15,17,15,40,20,15,15,40,20,15,15,15,15,17,15);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	function print_header(){
		global $worksheet, $xlsRow;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1;
		global $ws_headalign_line1, $ws_width;

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
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge, 1));	// 1 ตัวหลังสุดคือกำหนด $wrapText=1
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge, 1)); 	// 1 ตัวหลังสุดคือกำหนด $wrapText=1
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], 1));	// 1 ตัวหลังสุดคือกำหนด $wrapText=1
				$colseq++;
			}
		}
	}
	
	if($DPISDB=="odbc"){	
		$cmd = " select JETHRO_ID,a.ORG_ID,a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
					     COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME   
					from PER_JETHRO  a, PER_ORG b 
					where a.DEPARTMENT_ID=b.ORG_ID(+)
				    $search_condition
					$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "    select	JETHRO_ID,a.ORG_ID,a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
							COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME    
					from 		PER_JETHRO a, PER_ORG b
					where		a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition 
					$order_str ";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select JETHRO_ID,a.ORG_ID, a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
						COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME  
					from PER_JETHRO a, PER_ORG b 
					where a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition  
					$order_str ";
	} // end if

	$count_page_data = $db_dpis->send_cmd($cmd);
//echo "cmd=$cmd ($count_page_data)<br>";
//$db_dpis->show_error();

	if($count_page_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}
		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
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

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$wsdata_fontfmt_1 = array("","","","BUI","","","","BUI","","","","","","","");
		//$wsdata_fontfmt_1 = (array) null;
		$wsdata_align_1 = (array) null;
		$wsdata_border_1 = (array) null;
		$wsdata_colmerge_1 = (array) null;
		$wsdata_fontfmt_2 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$wsdata_fontfmt_1[] = "B";
			$wsdata_align_1[] = "L";
			$wsdata_border_1[] = "TLRB";
			$wsdata_colmerge_1[] = 0;
			$wsdata_fontfmt_2[] = "";
		}
		$wsdata_align_1 = array("C","C","C","L","L","C","C","L","L","C","C","C","C","C","C");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
	
	
		$data_count = 0;
		$buff_org_id = 0;
        while ($data = $db_dpis->get_array()) {
			$data_count++;
			//$temp_JETHRO_ID = $data[JETHRO_ID];
			$ORG_ID = $data[ORG_ID];
			$TMP_ORG_NAME = trim($data[ORG_NAME]);
			$NEW_ORG_NAME = trim($data[NEW_ORG_NAME]);
			//$current_list .= ((trim($current_list))?", ":"") . "'" . $temp_JETHRO_ID ."'";
			$TMP_MEETING_TIME = $data[MEETING_TIME];
			$OKP_MEETING_TIME = $data[OKP_MEETING_TIME];
			$TMP_MEETING_YEAR = $data[MEETING_YEAR];
			$OKP_MEETING_YEAR = $data[OKP_MEETING_YEAR];			
			$TMP_POS_NO = $data[POS_NO];
			$TMP_MEETING_DATE = show_date_format($data[MEETING_DATE], 1);
			$OKP_MEETING_DATE = show_date_format($data[OKP_MEETING_DATE], 1);
			$OKP_COMMITTEE_RESULT = trim($data[OKP_COMMITTEE_RESULT]);
			if ($OKP_COMMITTEE_RESULT==1) $OKP_COMMITTEE_RESULT = "อนุมัติ";
			elseif ($OKP_COMMITTEE_RESULT==2) $OKP_COMMITTEE_RESULT = "ไม่อนุมัติ";
			elseif ($OKP_COMMITTEE_RESULT==3) $OKP_COMMITTEE_RESULT = "ยุบเลิก";
			$TMP_PL_NAME = trim($data[PL_NAME]);
			$NEW_PL_NAME = trim($data[NEW_PL_NAME]);
			$TMP_PM_NAME = trim($data[PM_NAME]);
			$NEW_PM_NAME = trim($data[NEW_PM_NAME]);
			$TMP_CL_NAME = trim($data[CL_NAME]);
			$NEW_CL_NAME = trim($data[NEW_CL_NAME]);
			$TMP_COMMITTEE_RESULT = trim($data[COMMITTEE_RESULT]);
			if ($TMP_COMMITTEE_RESULT==1) $TMP_COMMITTEE_RESULT = "ผ่าน";
			elseif ($TMP_COMMITTEE_RESULT==2) $TMP_COMMITTEE_RESULT = "ไม่ผ่าน";
			elseif ($TMP_COMMITTEE_RESULT==3) $TMP_COMMITTEE_RESULT = "ยุบเลิก";
			$TMP_COMMITTEE_REMARK = trim($data[COMMITTEE_REMARK]);
			$TMP_DEPARTMENT_NAME = $data[DEPARTMENT_NAME];

			if ($buff_org_id != $ORG_ID) {
				$buff_org_id = $ORG_ID;
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $TMP_ORG_NAME; 
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $NEW_ORG_NAME;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			}
				$arr_data = (array) null;
				$arr_data[] = $TMP_MEETING_TIME."/".$TMP_MEETING_YEAR;
				$arr_data[] = $TMP_MEETING_DATE;
				$arr_data[] = $TMP_POS_NO;
				if(!$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
				$arr_data[] = $TMP_PM_NAME;
				$arr_data[] = $TMP_PL_NAME;
				////////เช็คประเภทตำแหน่ง//////// {
				$cmd = "select	CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE
							from PER_CO_LEVEL
							where CL_ACTIVE=1 and CL_NAME = '$TMP_CL_NAME'";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LEVEL_NO_MIN =  trim($data1[LEVEL_NO_MIN]);
				$LEVEL_TYPE = substr(trim(strtoupper($LEVEL_NO_MIN)),0,1); 
				if($LEVEL_TYPE == "D") $LEVEL_TYPE = "อำนวยการ";
				else if($LEVEL_TYPE == "M") $LEVEL_TYPE = "บริหาร";
				else if($LEVEL_TYPE == "O") $LEVEL_TYPE = "ทั่วไป";
				else if($LEVEL_TYPE == "K") $LEVEL_TYPE = "วิชาการ";
				////////เช็คประเภทตำแหน่ง//////// }
				$arr_data[] = $LEVEL_TYPE;
				$arr_data[] = $TMP_CL_NAME;
				if(!$NEW_PM_NAME) $NEW_PM_NAME = $NEW_PL_NAME;
				$arr_data[] = $NEW_PM_NAME;
				$arr_data[] = $NEW_PL_NAME;
				////////เช็คประเภทตำแหน่ง//////// {
				$cmd = "select	CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE
							from PER_CO_LEVEL
							where CL_ACTIVE=1 and CL_NAME = '$NEW_CL_NAME'";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$NEW_LEVEL_NO_MIN =  trim($data1[LEVEL_NO_MIN]);
				$NEW_LEVEL_TYPE = substr(trim(strtoupper($NEW_LEVEL_NO_MIN)),0,1); 
				if($NEW_LEVEL_TYPE == "D") $NEW_LEVEL_TYPE = "อำนวยการ";
				else if($NEW_LEVEL_TYPE == "M") $NEW_LEVEL_TYPE = "บริหาร";
				else if($NEW_LEVEL_TYPE == "O") $NEW_LEVEL_TYPE = "ทั่วไป";
				else if($NEW_LEVEL_TYPE == "K") $NEW_LEVEL_TYPE = "วิชาการ";
				////////เช็คประเภทตำแหน่ง//////// }
				$arr_data[] = $NEW_LEVEL_TYPE;
				$arr_data[] = $NEW_CL_NAME;
				$arr_data[] = $TMP_COMMITTEE_RESULT;
				$arr_data[] = $OKP_MEETING_TIME."/".$OKP_MEETING_YEAR;
				$arr_data[] = $OKP_MEETING_DATE;
				$arr_data[] = $OKP_COMMITTEE_RESULT;
				
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
			 
		}
		  
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>	
	


