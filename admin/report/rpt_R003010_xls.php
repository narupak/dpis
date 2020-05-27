<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	
	include ("rpt_R003010_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
$DEPARTMENT_ID_ORI = $DEPARTMENT_ID;

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$line_table = "PER_LINE";
		$line_code = "e.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$line_table = "PER_POS_NAME";
		$line_code = "e.PN_CODE";
		$line_name = "e.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "e.EP_CODE";
		$line_name = "e.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "e.TP_CODE";
		$line_name = "e.TP_NAME";	
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	if(in_array("ALL", $list_type))  {//if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  { เงื่อนไขเดิมไม่แสดงกระทรวงหากมีการเลือกกรม ทำให้การนับข้อมูลไม่ถูกต้อง
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
		case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.POH_ORG1 as MINISTRY_ID";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "e.POH_ORG1";
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.POH_ORG2 as DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.POH_ORG2";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_ORG3 as ORG_ID";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_ORG3";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG1 as ORG_ID_1";		
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_1";
				
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG1";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG2 as ORG_ID_2";	
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG2";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;							
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";
					
				if($order_by) $order_by .= ", ";
				$order_by .= "$line_code"; 
				
				$heading_name .=  $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) {
		if($select_org_structure==0) $order_by = "e.POH_ORG3";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0) $select_list = "e.POH_ORG3";
		else if($select_org_structure==1)  $select_list = "a.ORG_ID";
	}
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";			//search_per_status  เงื่อนไขที่ถูกส่งมา
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
        $budget_year = $search_budget_year - 543; 
	$budget_year_from = $budget_year - 1; 
	$budget_year_from = $budget_year_from.'-10-01'; 
	$budget_year_to = $budget_year.'-09-30';

	//$arr_search_condition[] = "(PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to')";		//PER_OCCUPYDATE จาก PER_PERSONAL ค่ามันเท่ากันหมด เวลา select มาค่าเท่ากัน ก็ count มาผิดเพราะดึงมาทั้งหมดเลย????
	$arr_search_condition[] = "POH_EFFECTIVEDATE >= '$budget_year_from' and POH_EFFECTIVEDATE <= '$budget_year_to'";	
	$arr_search_condition[] = "PER_OCCUPYDATE >= '$budget_year_from' and PER_OCCUPYDATE <= '$budget_year_to'";	

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($select_org_structure==0) {
			if($DEPARTMENT_ID){
				$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$arr_search_condition[] = "(e.POH_ORG1 = '".$data[ORG_NAME]."')";
			}
		}else if($select_org_structure==1) {
			if($DEPARTMENT_ID){
				//$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			} // end if
		}
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($select_org_structure==0) {
			if($DEPARTMENT_ID){
				$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$arr_search_condition[] = "(e.POH_ORG1 = '".$data[ORG_NAME]."')";
			}
		}else if($select_org_structure==1) {
			if($DEPARTMENT_ID){
				//$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			} // end if
		}
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($select_org_structure==0) {
			if($DEPARTMENT_ID){
				$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$arr_search_condition[] = "(e.POH_ORG1 = '".$data[ORG_NAME]."')";
			}
		}else if($select_org_structure==1) {
			if($DEPARTMENT_ID){
				//$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			} // end if
		}
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($select_org_structure==0) {
			if($DEPARTMENT_ID){
				$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$arr_search_condition[] = "(e.POH_ORG1 = '".$data[ORG_NAME]."')";
			}
		}else if($select_org_structure==1) {
			if($DEPARTMENT_ID){
				//$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			} // end if
		}
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(c.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(e.POH_ORG3 = '$search_org_name')";
				$list_type_text .= "$search_org_name";
			}	
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(e.POH_UNDER_ORG1 = '$search_org_name_1')";
				$list_type_text .= " - $search_org_name_1";
			}
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(e.POH_UNDER_ORG2 = '$search_org_name_2')";
				$list_type_text .= " - $search_org_name_2";
			} 
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			} 
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "($line_code='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($select_org_structure==0) {
			if($DEPARTMENT_ID){
				$arr_search_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
				$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$arr_search_condition[] = "(e.POH_ORG1 = '".$data[ORG_NAME]."')";
				$list_type_text .= " - $MINISTRY_NAME";
			}
		}else if($select_org_structure==1) {
			if($DEPARTMENT_ID){
				//$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";
				$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
			}elseif($MINISTRY_ID){
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
				$list_type_text .= " - $MINISTRY_NAME";
			}
		} // end if
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	if ($search_mov_code[0]==1) { 
		$arr_mov_code[] = 1;
		$mov_name .= "บรรจุใหม่ ";
	} 
	if ($search_mov_code[0]==2 || $search_mov_code[1]==2) {
		$arr_mov_code[] = 10;
		$mov_name .= "รับโอน ";
	} 
	if ($search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
		$arr_mov_code[] = 11;
		$mov_name .= "บรรจุกลับ ";
	}
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่$mov_name ในปีงบประมาณ $show_budget_year";
	$report_code = "R0310";

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
	if ($search_mov_code[0]==2 || $search_mov_code[1]==2 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
		$ws_head_line1 = array("ลำดับ","คำนำ","ชื่อ-สกุล","เลขที่ตำแหน่ง","ชื่อตำแหน่ง", "ประเภท", "วุฒิการศึกษา","วุฒิการศึกษา","วัน/เดือน/ปี","วัน/เดือน/ปี","ส่วนราชการ","ลักษณะการโอน");
		$ws_head_line2 = array("ที่","หน้านาม","","","ในสายงาน", "ตำแหน่ง", "ที่รับโอน","สูงสุด","เกิด","ที่รับโอน","เดิมก่อนโอน","(ปกติ/สอบ)");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5,8,40,15,40,12,30,25,12,12,40,20);
	} else {
		$ws_head_line1 = array("ลำดับ","คำนำ","ชื่อ-สกุล","เลขที่ตำแหน่ง","ชื่อตำแหน่ง", "ประเภท", "วุฒิการศึกษา","วุฒิการศึกษา","วัน/เดือน/ปี","วัน/เดือน/ปี");
		$ws_head_line2 = array("ที่","หน้านาม","","","ในสายงาน", "ตำแหน่ง", "ที่บรรจุ","สูงสุด","เกิด","ที่บรรจุ");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5,8,40,15,40,15,30,25,15,15);
	}
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
		global $heading_name, $search_mov_code;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		$colshow_cnt = $colseq;

		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N, $position_table, $position_join;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $arr_mov_code, $DATE_DISPLAY;
		global $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		//ระบุวันก่อนหน้าที่ได้รับโอน หาจากวันสิ้นสุดการดำรงตำแหน่ง
		if(count($arr_mov_code)) $mov_code = " and trim(g.MOV_SUB_TYPE) in (". implode(" , ", $arr_mov_code) . ")";

		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from		(
												(	
							 						(		
							 							(
															PER_PERSONAL a 
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
							$mov_code
							$search_condition
							group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			/*$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
												PER_BIRTHDATE, SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE													
							 from			PER_PERSONAL a, PER_PRENAME d , PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
							 where		a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and 
												a.DEPARTMENT_ID=f.ORG_ID(+) and e.MOV_CODE=g.MOV_CODE(+)
												$mov_code
												$search_condition 
							 group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";*//*เดิม*/
                        $cmd = " select	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
                                    PER_BIRTHDATE, SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE													
                                from PER_PERSONAL a, PER_PRENAME d , PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g
                                where a.PN_CODE=d.PN_CODE(+) and a.PER_ID=e.PER_ID(+) and 
                                    a.DEPARTMENT_ID=f.ORG_ID(+) and e.MOV_CODE=g.MOV_CODE(+) 
                                    $mov_code 
                                    $search_condition 
                                group by a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from		(
												(	
							 						(			
							 							(
															PER_PERSONAL a 
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
							$mov_code
							$search_condition 
							group by	a.PER_ID , d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE , a.PER_OCCUPYDATE ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		//echo "<br><pre>$cmd<br>";
                //die();
		while($data = $db_dpis2->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
			$PER_OCCUPYDATE = substr(trim($data[PER_OCCUPYDATE]), 0, 10);

			//หาวุฒิการศึกษาบรรจุ	
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_USE = $data2[EN_NAME];
			
			//หาวุฒิการศึกษาสูงสุด
			$cmd = " select 	a.EN_CODE, b.EN_NAME
							 from 		PER_EDUCATE a, PER_EDUCNAME b
							 where	a.EN_CODE=b.EN_CODE and a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%'
							 order by a.EDU_SEQ desc,a.EN_CODE ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$EDU_MAX = $data2[EN_NAME];

			//หาส่วนราชการเดิม
			if($DPISDB=="odbc"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
								from				PER_POSITIONHIS e, PER_MOVMENT g
								where			PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			}elseif($DPISDB=="oci8"){	
				/*$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_OCCUPYDATE' ";
				$cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
								from 		PER_POSITIONHIS e, PER_MOVMENT g
								where 	PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";*/
                            $cmd = "select POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
                                        LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE ,
                                        e.POH_POS_NO
                                    from PER_POSITIONHIS e, PER_MOVMENT g
                                    where PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE $mov_code
                                    order by SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";
                            
			}elseif($DPISDB=="mysql"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE, TP_CODE , e.MOV_CODE
								from				PER_POSITIONHIS  e, PER_MOVMENT g
								where			PER_ID=$PER_ID and e.MOV_CODE=g.MOV_CODE and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			} // end if
			$count_poh = $db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
//			echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$OLD_ORG = $data2[POH_ORG_TRANSFER];		
			$LEVEL_NO = $data2[LEVEL_NO];		
			$PL_CODE = $data2[PL_CODE];
                        $POH_POS_NO = $data2[POH_POS_NO];
			$PT_CODE = trim($data[PT_CODE]);
			$PN_CODE = $data2[PN_CODE];		
			$EP_CODE = $data2[EP_CODE];		
			$TP_CODE = $data2[TP_CODE];		
			$MOV_CODE = $data2[MOV_CODE];

			$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			if($search_per_type==1) {
				$cmd = "select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PL_NAME]);
	
				$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
				else
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");

			} elseif($search_per_type==2) {
				$cmd = "select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PN_NAME]);
			} elseif($search_per_type==3) {
				$cmd = "select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[EP_NAME]);
			}elseif($search_per_type==4) {
				$cmd = "select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[TP_NAME]);
			}

			$PER_OCCUPYDATE = show_date_format($PER_OCCUPYDATE, $DATE_DISPLAY);

			if ($count_poh) {
				$data_count++;
				$person_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = $person_count .". ";
				$arr_content[$data_count][prename] = $PN_NAME;
				$arr_content[$data_count][name] = $PER_NAME ." ". $PER_SURNAME;
                                $arr_content[$data_count][pos_no] = $POH_POS_NO;
				$arr_content[$data_count][position] = $PL_NAME;
				$arr_content[$data_count][position_type] = $POSITION_TYPE; 
				$arr_content[$data_count][educate_use] = $EDU_USE;
				$arr_content[$data_count][educate_max] =  $EDU_MAX;
				$arr_content[$data_count][birthdate]	= $PER_BIRTHDATE;
				$arr_content[$data_count][posdate] = $PER_OCCUPYDATE;
				$arr_content[$data_count][old_org] = $OLD_ORG;	 //ส่วนราชการเดิมก่อนโอน
				$arr_content[$data_count][reason] = ""; // $MOV_NAME;
			}

		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $db_dpis, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2,$MINISTRY_NAME, $DEPARTMENT_NAME,
					$ORG_NAME, $ORG_NAME_1, $ORG_NAME_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE, $ORG_BKK_TITLE;
		global $line_code,$DEPARTMENT_ID_ORI;
		if($ORG_NAME==$ORG_BKK_TITLE)		$ORG_NAME_SEARCH = "-";			// กำหนดให้คำว่า ผู้บริหาร เป็น - ตาม DB ไม่งั้นจะค้นหาไม่ได้
		else $ORG_NAME_SEARCH = $ORG_NAME;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1){ 
						$arr_addition_condition[] = "(e.POH_ORG1 = '$MINISTRY_NAME')";
					}
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1){ 
						if($select_org_structure==0){	
							$arr_addition_condition[] = "(e.POH_ORG2 = '$DEPARTMENT_NAME')";
						}else if($select_org_structure==1){
							//$arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
							$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID_ORI)";	
						}
					}else{
						if($select_org_structure==0){	
					 		$arr_addition_condition[] = "(e.POH_ORG2 = '' or e.POH_ORG2 is null)";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.DEPARTMENT_ID = 0 or a.DEPARTMENT_ID is null)";
						}
					}
				break;
				case "ORG" :	
					if($ORG_ID && $ORG_ID!=-1){
						if($select_org_structure==0){	
							$arr_addition_condition[] = "(e.POH_ORG3 = '$ORG_NAME_SEARCH')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						}
					}else{
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_ORG3 = '' or e.POH_ORG3 is null)";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
						}
					}
				break;
				case "ORG_1" :
					if($ORG_ID_1 && $ORG_ID_1!=-1){
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '$ORG_NAME_1')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						}
					}else{
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '' or e.POH_UNDER_ORG1 is null)";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
						}
					}
				break;
				case "ORG_2" :
					if($ORG_ID_2 && $ORG_ID_2!=-1){
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '$ORG_NAME_2')";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						}
					}else{
						if($select_org_structure==0){	 
							$arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '' or e.POH_UNDER_ORG2 is null)";
						}else if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
						}
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order, $arr_mov_code;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" :
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//แสดงรายชื่อหน่วยงาน
	if(count($arr_mov_code)) $mov_code = " and trim(g.MOV_SUB_TYPE) in (". implode(" , ", $arr_mov_code) . ")";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list , a.PER_ID 
						 from	(
										(	
						 					(
												PER_PERSONAL a 
											) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
										) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
									) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition $mov_code
								 group by 	$order_by , a.PER_ID
								 order by		$order_by , a.PER_ID";
	}elseif($DPISDB=="oci8"){	
		$search_condition = str_replace(" where ", " and ", $search_condition);
                
		/*$cmd = " select			distinct $select_list , a.PER_ID 
								 from			PER_PERSONAL a, PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g	
								 where		a.PER_ID=e.PER_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+) and 
													e.MOV_CODE=g.MOV_CODE(+) and SUBSTR(a.PER_OCCUPYDATE,1,10) = SUBSTR(e.POH_EFFECTIVEDATE,1,10)
													$search_condition $mov_code
								 group by 	$order_by , a.PER_ID
								 order by		$order_by , a.PER_ID";*/
                
                $cmd = " select distinct $select_list , a.PER_ID 
                    from PER_PERSONAL a, PER_POSITIONHIS e, PER_ORG f, PER_MOVMENT g	
                    where a.PER_ID=e.PER_ID(+) and a.DEPARTMENT_ID=f.ORG_ID(+) 
                    and e.MOV_CODE=g.MOV_CODE(+) 
                        $search_condition $mov_code 
                    group by $order_by , a.PER_ID 
                    order by $order_by , a.PER_ID";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list , a.PER_ID 
						 from	(
										(	
						 					(
												PER_PERSONAL a 
											) left join PER_ORG f on (a.DEPARTMENT_ID=f.ORG_ID)
										) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
									) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition $mov_code
								 group by 	$order_by , a.PER_ID
								 order by		$order_by , a.PER_ID";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "<br> <pre> $count_data :: $cmd<br>";	exit;
        //die();
//	$db_dpis->show_error();	exit;
//	print_r($arr_rpt_order);
	$data_count = 0;
	$person_count = 0;
	$start_rpt_order = ($f_all ? 0 : 1);
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
			//echo "<br>$rpt_order_index // MINISTRY : $data[ORG_ID] // PER_ID : $data[PER_ID]<br>";
			for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
				$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
				switch($REPORT_ORDER){
				case "MINISTRY" :
						if($MINISTRY_ID != trim($data[MINISTRY_ID])){
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" || $MINISTRY_ID!=0 || $MINISTRY_ID!=-1){
								/*$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$MINISTRY_NAME = $data2[ORG_NAME];*/
									$MINISTRY_NAME = $MINISTRY_ID;				//ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

								if ($f_all) {
									$addition_condition = generate_condition($rpt_order_index);
									
									$arr_content[$data_count][type] = "MINISTRY";
									$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $MINISTRY_NAME;

		//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
									if($rpt_order_index == (count($arr_rpt_order) - 1)) {
										initialize_parameter($rpt_order_index + 1);
		//								echo "MINISTRY--list_person - ($search_condition, $addition_condition)<br>";
										list_person($search_condition, $addition_condition);
									}
									$data_count++;
								} // end if ($f_all)
							} // end if($MINISTRY_ID != "" || $MINISTRY_ID!=0 || $MINISTRY_ID!=-1)
						} // end if
					break;

				case "DEPARTMENT" :
						if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" || $DEPARTMENT_ID!=0 || $DEPARTMENT_ID!=-1){
								/*$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$DEPARTMENT_NAME = $data2[ORG_NAME];*/
									$DEPARTMENT_NAME = $DEPARTMENT_ID;				//ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

								$addition_condition = generate_condition($rpt_order_index);
								
								$arr_content[$data_count][type] = "DEPARTMENT";
								$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $DEPARTMENT_NAME;
								
	//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								if($rpt_order_index == (count($arr_rpt_order) - 1)) {
									initialize_parameter($rpt_order_index + 1);			
	//								echo "DEPARTMENT--list_person - ($search_condition, $addition_condition)<br>";
									list_person($search_condition, $addition_condition);
								}
								$data_count++;
							} // end if($DEPARTMENT_ID != "" || $DEPARTMENT_ID!=0 || $DEPARTMENT_ID!=-1)
						} // end if
					break;

				case "ORG" :
						if($ORG_ID != $data[ORG_ID]){
							$ORG_ID = $data[ORG_ID];
							if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1){
								if($select_org_structure==0){ 
									$ORG_NAME = $ORG_ID;
								}elseif($select_org_structure==1) {
									$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
									$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$ORG_NAME = $data2[ORG_NAME];
									$ORG_SHORT = $data2[ORG_SHORT];
								}
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

								if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
									//echo "<br>$rpt_order_index // $ORG_ID : $ORG_NAME  // PER_ID : $data[PER_ID]<br>";
										$addition_condition = generate_condition($rpt_order_index);
				
										$arr_content[$data_count][type] = "ORG";
										$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . $ORG_NAME;
				
	//									if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
										if($rpt_order_index == (count($arr_rpt_order) - 1)) { 
											initialize_parameter($rpt_order_index + 1);			
	//										echo "ORG--list_person - ($search_condition, $addition_condition)<br>";
											list_person($search_condition, $addition_condition);
										}
										$data_count++;
								} // end if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
							} // end if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1)
						}
					break;

			case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
						if($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1){
							$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
							$ORG_SHORT_1 = $data2[ORG_SHORT];
							if($ORG_NAME_1=="-")	$ORG_NAME_1 = $ORG_BKK_TITLE;
						} //end if($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1)

					 if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-")){
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "ORG_1";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . ($ORG_NAME_1=="ไม่ระบุ" ? "" : $ORG_NAME_1);
	
//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
//							list_person($search_condition, $addition_condition);
							if($rpt_order_index == (count($arr_rpt_order) - 1)) {
								initialize_parameter($rpt_order_index + 1);
//								echo "ORG_1--list_person - ($search_condition, $addition_condition)<br>";
								list_person($search_condition, $addition_condition);
							}
							$data_count++;
					} // end if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
			}
			break;

			case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
						if($ORG_ID_2 != "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1){
							$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
						} // end if($ORG_ID_2!= "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1)

						 if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
							$addition_condition = generate_condition($rpt_order_index);
	
							$arr_content[$data_count][type] = "ORG_2";
							$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index - $start_rpt_order) * 5)) . ($ORG_NAME_2=="ไม่ระบุ" ? "" : $ORG_NAME_2);

//							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
//							list_person($search_condition, $addition_condition);
							if($rpt_order_index == (count($arr_rpt_order) - 1)) {
								initialize_parameter($rpt_order_index + 1);
//								echo "ORG_2--list_person - ($search_condition, $addition_condition)<br>";
								list_person($search_condition, $addition_condition);
							}
							$data_count++;
						} // end if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
				}	
				break;
				} //end switch
			} // end for
		
//	if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
		} // end if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1))
	} // end while
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";
	//die();
	if($count_data){
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","C","L","C","L","L","C","C","L","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
                        $POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$EDU_USE = $arr_content[$data_count][educate_use];
			$EDU_MAX = $arr_content[$data_count][educate_max];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$POSTDATE = $arr_content[$data_count][posdate]; 
			$OLD_ORG = $arr_content[$data_count][old_org];
			$MOV_NAME = $arr_content[$data_count][reason];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $PRE_NAME;
			$arr_data[] = $NAME;
                        $arr_data[] = $POS_NO;
			$arr_data[] = $POSITION;
			$arr_data[] = $POSITION_TYPE;
			$arr_data[] = $EDU_USE;
			$arr_data[] = $EDU_MAX;
			$arr_data[] = $BIRTHDATE;
			$arr_data[] = $POSTDATE;
			if ($search_mov_code[0]==2 || $search_mov_code[1]==2 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
				$arr_data[] = $OLD_ORG;
				$arr_data[] = $MOV_NAME;
			}
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for				
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
		if ($search_mov_code[0]==2 || $search_mov_code[1]==2 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
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