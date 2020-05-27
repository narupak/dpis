<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R003002_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type)  && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
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
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO, i.LEVEL_NAME";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO, i.LEVEL_NAME";

				$heading_name .= " $LEVEL_TITLE";
				break; 
			case "SEX" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_GENDER";

				$heading_name .= " $SEX_TITLE";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PV_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PV_CODE";

				$heading_name .= " $PV_TITLE";
				break;
			case "EDUCLEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EL_CODE";

				$heading_name .= " $EL_TITLE";
				break;
			case "EDUCMAJOR" :
				if($select_list) $select_list .= ", ";
				$select_list .= "d.EM_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "d.EM_CODE";

				$heading_name .= " $EM_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by = "e.POH_ORG3";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0) $select_list = "e.POH_ORG3";
		else if($select_org_structure==1)  $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

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
				 $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}elseif($MINISTRY_ID){
				 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			}
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
				 $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}elseif($MINISTRY_ID){
				 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			}
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
				 $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}elseif($MINISTRY_ID){
				 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			}
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
				 $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}elseif($MINISTRY_ID){
				 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
	
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			}
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
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(e.POH_UNDER_ORG2 = '$search_org_name_2')";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			}
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
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
				 $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
			}elseif($MINISTRY_ID){
				 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
	
				$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
				$list_type_text .= " - $MINISTRY_NAME";
			}
		}
	}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
/*********	
	if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
 	$search_condition .= " and (a.PER_STATUS=2 or a.PER_STATUS=3) ";		//iรอบรรจุ (สำหรับโอนออก) หรือ พ้นจากส่วนราชการ (สำหรับเกษียณ/ลาออก)     
********/
	//ตาม rpt_R003011.php

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่ออกจากราชการในปีงบประมาณ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0302";

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
		$ws_head_line1 = array("$heading_name","เกษียณ","เกษียณ","ลาออก","ตาย","ให้โอน","ออกด้วย","รวม");
		$ws_head_line2 = array("","ปกติ","ก่อนกำหนด","","","","เหตุวินัย","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_width = array(50,8,10,8,8,8,8,8);
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

		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line1[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function count_person($movement_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $search_budget_year, $BKK_FLAG;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		switch($movement_type){	//***แก้ MOV_CODE ตาม R003011***//
			case 1 :	//เกษียณปกติ
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE = 92)";
				if ($BKK_FLAG==1) {
					if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
					elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
					elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
				} else {
					if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
					elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
					elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
				}
			break;
			case 2 :	//เกษียณก่อนกำหนด
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE = 93)";
				if ($BKK_FLAG==1) {
					if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
					elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
					elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE <= a.PER_RETIREDATE)";
				} else {
					if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
					elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
					elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
						and a.PER_POSDATE = a.PER_RETIREDATE)";
				}
			break;
			case 3 :	//ลาออก
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE= 90)";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 4 :	//ตาย
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE = 94)";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 5 :	//ให้โอน
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE = 91)";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
			case 6 :	//ออกด้วยเหตุวินัย
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(f.MOV_SUB_TYPE = 95)";
				if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
				elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
					and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
			break;
		} // end switch case

		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select		count(a.PER_ID) as count_person
								 from		(
													(
								 						(
															PER_PERSONAL a 
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
								$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from		(
													(
														PER_PERSONAL a 
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
								$search_condition
								 group by	a.PER_ID ";
			} // end if
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e, PER_MOVMENT f
								 where		a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
								 					and a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+)
													$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f
								 where		a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+)
													$search_condition
								 group by	a.PER_ID ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select		count(a.PER_ID) as count_person
								 from		(
													(
								 						(
															PER_PERSONAL a 
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
								$search_condition
								 group by	a.PER_ID ";
			}else{
				$cmd = " select		count(a.PER_ID) as count_person
								 from		(
													(
														PER_PERSONAL a 
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
								$search_condition
								 group by	a.PER_ID ";
			} // end if
		} // end if
		$count_person = $db_dpis2->send_cmd($cmd);

//echo "[person] $count_person - $cmd<hr>";
		//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure,$line_code;
		global $MINISTRY_ID,$DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2,$MINISTRY_NAME, $DEPARTMENT_NAME,
					$ORG_NAME, $ORG_NAME_1, $ORG_NAME_2, $PL_CODE, $PN_CODE,$LEVEL_NO,$LEVEL_NAME, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE, $EP_CODE, $TP_CODE, $ORG_BKK_TITLE;
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
							$arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
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
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim($line_code) = '$PL_CODE' or $line_code is null)";
				break;
                                case "LEVEL" :
					if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO')";
					else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '$LEVEL_NO' or a.LEVEL_NO is null)";
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(a.PV_CODE) = '$PV_CODE' or a.PV_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EL_CODE>0) $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE')";
					//else $arr_addition_condition[] = "(trim(d.EL_CODE) = '$EL_CODE' or d.EL_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID,$DEPARTMENT_ID,$ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE,$LEVEL_NO,$LEVEL_NAME, $PER_GENDER, $PV_CODE, $EM_CODE, $EL_CODE;
		
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
				case "LINE" :
					$PL_CODE = -1;
				break;
                                case "LEVEL" :
					$LEVEL_NO = -1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
					$PN_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EL_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//แสดงทั้งหมด
	$cond_where = "and (f.MOV_SUB_TYPE in (9, 90, 91, 92, 93, 94, 95))";
	if($DPISDB=="odbc") $cond_where .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $cond_where .= " and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) <= '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $cond_where .= " and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) <= '".($search_budget_year - 543)."-10-01')";
 	$cond_where .= " and (a.PER_STATUS=2 or a.PER_STATUS=3) ";		//iรอบรรจุ (สำหรับโอนออก) หรือ พ้นจากส่วนราชการ (สำหรับเกษียณ/ลาออก)     
	
	if($DPISDB=="odbc"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from		(
                                                                                            (        (
                                                                                                            (
                                                                                                                    PER_PERSONAL a 
                                                                                                            ) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
                                                                                                    ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                            ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                        ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												$search_condition	$cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from		(
                                                                                            (        (
                                                                                                            PER_PERSONAL a 
                                                                                                    ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                            ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                        ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from			PER_PERSONAL a, PER_EDUCATE d, PER_POSITIONHIS e, PER_MOVMENT f ,PER_LEVEL i
							 where		a.PER_ID=d.PER_ID(+)  and d.EDU_TYPE like '%2%'
							 					and a.PER_ID=e.PER_ID(+) and a.LEVEL_NO=i.LEVEL_NO(+) and e.MOV_CODE=f.MOV_CODE(+)
												$search_condition $cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from			PER_PERSONAL a, PER_POSITIONHIS e, PER_MOVMENT f ,PER_LEVEL i
							 where		a.PER_ID=e.PER_ID(+) and a.LEVEL_NO=i.LEVEL_NO(+) and e.MOV_CODE=f.MOV_CODE(+)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}elseif($DPISDB=="mysql"){
		if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
			$cmd = " select		distinct $select_list
							 from		(
                                                                                            (        (
                                                                                                            (
                                                                                                                    PER_PERSONAL a 
                                                                                                            ) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
                                                                                                    ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                            ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                        ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												$search_condition $cond_where
							 order by		$order_by ";
		}else{
			$cmd = " select		distinct $select_list
							 from		(
                                                                                            (        (
                                                                                                            PER_PERSONAL a 
                                                                                                    ) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
                                                                                            ) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
                                                                                        ) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												$search_condition $cond_where
							 order by		$order_by ";
		} // end if
	}
	$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->num_rows (); 
//		echo "$count_data = $cmd";	exit;
//		$db_dpis->show_error();	exit;
 
	$data_count = 0;
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = $GRAND_TOTAL_5 = 0;
	initialize_parameter(0);
	$first_order = 1;	// order ที  0 = COUNTRY ยังไม่ได้คำนวณ เริ่ม order ที่ 1 (MINISTRY) ก่อน
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" : 
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){	
							$MINISTRY_ID = trim($data[MINISTRY_ID]);
							if($MINISTRY_ID != "" || $MINISTRY_ID!=0 || $MINISTRY_ID!=-1){
									/*$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$MINISTRY_NAME = $data2[ORG_NAME];
									$MINISTRY_SHORT = $data2[ORG_SHORT];*/ 
									$MINISTRY_NAME = $MINISTRY_ID;				//ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME
							
							if ($f_all) {
									$addition_condition = generate_condition($rpt_order_index);
							
									$arr_content[$data_count][type] = "MINISTRY";
									$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
									$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
									$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
									$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
							
									if($rpt_order_index==$first_order){ 
										$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
										$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
										$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
										$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
										$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
										$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
									} // end if
							
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
								$data_count++;
							} // end if ($f_all)
						} // end if($MINISTRY_ID != "" || $MINISTRY_ID!=0 || $MINISTRY_ID!=-1)
				} // end if
				break;
				
				case "DEPARTMENT" : 
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
							$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
							if($DEPARTMENT_ID != "" || $DEPARTMENT_ID != 0 || $DEPARTMENT_ID != -1){
									/*$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
									if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$DEPARTMENT_NAME = $data2[ORG_NAME];
									$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT]; */
									$DEPARTMENT_NAME = $DEPARTMENT_ID;				//ใน db ฟิล์ดนี้เก็บเป็นตัวอักษร NAME

									$addition_condition = generate_condition($rpt_order_index);
			
									$arr_content[$data_count][type] = "DEPARTMENT";
									$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
									$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_SHORT;
									$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
									$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
									$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
									$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
									$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
									$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
			
									if($rpt_order_index==$first_order){ 
										$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
										$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
										$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
										$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
										$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
										$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
									} // end if
			
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
						} // end if($DEPARTMENT_ID != "" || $DEPARTMENT_ID != 0 || $DEPARTMENT_ID != -1)
					} // end if
				break;
				
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
							$ORG_ID = trim($data[ORG_ID]);
							if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1){
								if($select_org_structure==0){ 
									$ORG_NAME = $ORG_ID;
								}elseif($select_org_structure==1) {
									$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
									$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$ORG_NAME = $data2[ORG_NAME];
									$ORG_SHORT = $data2[ORG_SHORT];
								}
								if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;

								if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-")){
										$addition_condition = generate_condition($rpt_order_index);
									
										$arr_content[$data_count][type] = "ORG";
										$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
										$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT;
										$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
										$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
										$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
										$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
										$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
										$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
									
										if($rpt_order_index==$first_order){ 
											$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
											$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
											$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
											$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
											$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
											$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
										} // end if
									
										if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
										$data_count++;
								} // end if(($ORG_NAME !="" && $ORG_NAME !="-") || ($BKK_FLAG==1 && $ORG_NAME !="" && $ORG_NAME !="-"))
						} // end if($ORG_ID != "" || $ORG_ID != 0 || $ORG_ID != -1)
				}	
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
							$ORG_ID_1 = trim($data[ORG_ID_1]);
							$ORG_NAME_1 = $ORG_SHORT_1 = "ไม่ระบุ";
							if($ORG_ID_1 != "" || $ORG_ID_1 != 0 || $ORG_ID_1 != -1){
								$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
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
								$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
								$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT_1;
								$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
								$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
								$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
								$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
								$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
								$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
		
								if($rpt_order_index==$first_order){ 
									$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
									$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
									$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
									$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
									$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
									$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								} // end if
					
								if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
								$data_count++;
							} // end if(($ORG_NAME_1 !="" && $ORG_NAME_1 !="-") || ($BKK_FLAG==1 && $ORG_NAME_1 !="" && $ORG_NAME_1 !="-"))
					}
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						$ORG_NAME_2 = $ORG_SHORT_2 = "ไม่ระบุ";
						if($ORG_ID_2!= "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
							$ORG_SHORT_2 = $data2[ORG_SHORT];
							if($ORG_NAME_2=="-")	$ORG_NAME_2 = $ORG_BKK_TITLE;
						} // end if($ORG_ID_2!= "" || $ORG_ID_2 != 0 || $ORG_ID_2 != -1)
						
						if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-")){
							$addition_condition = generate_condition($rpt_order_index);
							
							$arr_content[$data_count][type] = "ORG_2";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
							$arr_content[$data_count][short_name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_SHORT_2;
							$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
							$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);
							
							if($rpt_order_index==$first_order){ 
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
							} // end if
							
					if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
					$data_count++;
					} // end if(($ORG_NAME_2 !="" && $ORG_NAME_2 !="-") || ($BKK_FLAG==1 && $ORG_NAME_2 !="" && $ORG_NAME_2 !="-"))
				}
				break;
		
				case "LINE" :
					if(trim($data[PL_CODE]) && $PL_CODE != trim($data[PL_CODE])){	
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							if($search_per_type==1){
								$cmd = " select $line_name as PL_NAME, $line_short_name from $line_table e where trim($line_code)='$PL_CODE' ";
							}else{
								$cmd = " select $line_name as PL_NAME from $line_table e where trim($line_code)='$PL_CODE' ";
							}
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_NAME]);
							if($search_per_type==1){
								$PL_NAME = trim($data2[$line_short_name])?$data2[$line_short_name]:$PL_NAME;
							}
						} // end if
                                                if(($PL_NAME !="" && $PL_NAME !="-") || ($BKK_FLAG==1 && $PL_NAME !="" && $PL_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

						if($rpt_order_index==$first_order){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;
                                case "LEVEL" :
                                                if($LEVEL_NO != trim($data[LEVEL_NO])){
                                                        $LEVEL_NO = trim($data[LEVEL_NO]);
                                                        $LEVEL_NAME = trim($data[LEVEL_NAME]);
                                                if(($LEVEL_NAME !="" && $LEVEL_NAME !="-") || ($BKK_FLAG==1 && $LEVEL_NAME !="" && $LEVEL_NAME !="-")){
                                                        $addition_condition = generate_condition($rpt_order_index);

                                                        $arr_content[$data_count][type] = "LEVEL";
                                                        $arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($LEVEL_NAME)?"". $LEVEL_NAME :"[ไม่ระบุระดับตำแหน่ง]");
                                                        $arr_content[$data_count][short_name] = "";
                                                        $arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
                                                        $arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

                                                        if($rpt_order_index == $first_order){ 
                                                            $GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
                                                            $GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
                                                            $GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
                                                            $GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
                                                            $GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
                                                            $GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
                                                        } // end if

                                                        if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
                                                        $data_count++;
                                                } // end if
                                                }
                                        break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]) + 0;

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER==1)?"ชาย":(($PER_GENDER==2)?"หญิง":""));
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

						if($rpt_order_index==$first_order){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if(trim($data[PV_CODE]) && $PV_CODE != trim($data[PV_CODE])){	
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE != ""){
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if
                    if(($PV_NAME !="" && $PV_NAME !="-") || ($BKK_FLAG==1 && $PV_NAME !="" && $PV_NAME !="-")){
						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

						if($rpt_order_index==$first_order){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
					}
				break;

				case "EDUCLEVEL" :
					if(trim($data[EL_CODE]) && $EL_CODE != trim($data[EL_CODE])){	
						$EL_CODE = trim($data[EL_CODE]);
						if($EL_CODE !=0||$EL_CODE != ""){
							$cmd = " select EL_NAME from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
//							$EL_NAME= trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EL_NAME];
							$EL_NAME= $data2[EL_NAME];
						}else{
							$EL_NAME= "[ไม่ระบุระดับการศึกษา]";
						}

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EL_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

						if($rpt_order_index==$first_order){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if(trim($data[EM_CODE]) && $EM_CODE != trim($data[EM_CODE])){	
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE !=0||$EM_CODE != ""){
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						}else{
							$EM_NAME = "[ไม่ระบุสาขาวิชาเอก]";
						}

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						$arr_content[$data_count][short_name] = "";
						$arr_content[$data_count][count_1] = count_person(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person(3, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person(4, $search_condition, $addition_condition);
						$arr_content[$data_count][count_5] = count_person(5, $search_condition, $addition_condition);
						$arr_content[$data_count][count_6] = count_person(6, $search_condition, $addition_condition);

						if($rpt_order_index==$first_order){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
							$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
		}
	} // end while

	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
		$GRAND_TOTAL_4 = count_person(4, $search_condition, "");
		$GRAND_TOTAL_5 = count_person(5, $search_condition, "");
		$GRAND_TOTAL_6 = count_person(6, $search_condition, "");
	} // end if
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0){
		$GRAND_TOTAL_1 = count_person(1, $search_condition, "");
		$GRAND_TOTAL_2 = count_person(2, $search_condition, "");
		$GRAND_TOTAL_3 = count_person(3, $search_condition, "");
		$GRAND_TOTAL_4 = count_person(4, $search_condition, "");
		$GRAND_TOTAL_5 = count_person(5, $search_condition, "");
		$GRAND_TOTAL_6 = count_person(6, $search_condition, "");
	} // end if

	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4 + $GRAND_TOTAL_5 + $GRAND_TOTAL_6;

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","R","R","R","R","R","R","R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_5 = $arr_content[$data_count][count_5];
			$COUNT_6 = $arr_content[$data_count][count_6];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4 + $COUNT_5 + $COUNT_6;
			
			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $COUNT_1;
			$arr_data[] = $COUNT_2;
			$arr_data[] = $COUNT_3;
			$arr_data[] = $COUNT_4;
			$arr_data[] = $COUNT_5;
			$arr_data[] = $COUNT_6;
			$arr_data[] = $COUNT_TOTAL;

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

		$arr_data = (array) null;
		$arr_data[] = "รวม";
		$arr_data[] = $GRAND_TOTAL_1;
		$arr_data[] = $GRAND_TOTAL_2;
		$arr_data[] = $GRAND_TOTAL_3;
		$arr_data[] = $GRAND_TOTAL_4;
		$arr_data[] = $GRAND_TOTAL_5;
		$arr_data[] = $GRAND_TOTAL_6;
		$arr_data[] = $GRAND_TOTAL;

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
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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