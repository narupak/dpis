<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "$line_code as PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code;

				$heading_name .= $line_title;
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
	 	if($select_org_structure==0) $order_by = "b.ORG_ID";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	 }
	if(!trim($select_list)){
	 	if($select_org_structure==0) $select_list = "b.ORG_ID";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	 }

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_OCCUPYDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_OCCUPYDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_OCCUPYDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_OCCUPYDATE), 10) < '".($search_budget_year - 543)."-10-01')";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
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
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)"; 
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		}
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
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
	
	$company_name = "";
	$report_title = "จ.18||ปีงบประมาณ $search_budget_year ณ  1 ต.ค. ".($search_budget_year -1)."||";
	$report_title .= $MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:"";
	$report_code = "R20010";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	$heading_width[PERSONAL][0] = "3";
	$heading_width[PERSONAL][1] = "50";      
	$heading_width[PERSONAL][2] = "60";
	$heading_width[PERSONAL][3] = "20";
	$heading_width[PERSONAL][4] = "60";
	$heading_width[PERSONAL][5] = "60";
	$heading_width[PERSONAL][6] = "12";
	$heading_width[PERSONAL][7] = "12";
	$heading_width[PERSONAL][8] = "12";
	
	$heading_width[SALARY][0] = "8";
	$heading_width[SALARY][1] = "15";      
	$heading_width[SALARY][2] = "15";
	$heading_width[SALARY][3] = "25";     
	$heading_width[SALARY][4] = "15";
	$heading_width[SALARY][5] = "45";
	$heading_width[SALARY][6] = "8";
	$heading_width[SALARY][7] = "8";
	$heading_width[SALARY][8] = "8";
	
	$heading_width[DEPART][0] = "3";
	$heading_width[DEPART][1] = "50";      
	$heading_width[DEPART][2] = "60";
	$heading_width[DEPART][3] = "20";
	$heading_width[DEPART][4] = "60";
	$heading_width[DEPART][5] = "60";
	$heading_width[DEPART][6] = "12";
	$heading_width[DEPART][7] = "12";
	$heading_width[DEPART][8] = "12";
	
	$heading_width[SUMLEVEL][0] = "15";
	$heading_width[SUMLEVEL][1] = "25";      
	$heading_width[SUMLEVEL][2] = "15";
	$heading_width[SUMLEVEL][3] = "25";     
	$heading_width[SUMLEVEL][4] = "15";
	$heading_width[SUMLEVEL][5] = "45";
	$heading_width[SUMLEVEL][6] = "10";
	$heading_width[SUMLEVEL][7] = "10";
	$heading_width[SUMLEVEL][8] = "12";
	
	function print_header($HISTORY_NAME){
		global $worksheet, $xlsRow;
		global $heading_width;
		
		$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
		$worksheet->set_column(1,1,$heading_width[$HISTORY_NAME][1]);
		$worksheet->set_column(2,2,$heading_width[$HISTORY_NAME][2]);
		$worksheet->set_column(3,3,$heading_width[$HISTORY_NAME][3]);
		$worksheet->set_column(4,4,$heading_width[$HISTORY_NAME][4]);
		$worksheet->set_column(5,5,$heading_width[$HISTORY_NAME][5]);
		$worksheet->set_column(6,6,$heading_width[$HISTORY_NAME][6]);
		$worksheet->set_column(7,7,$heading_width[$HISTORY_NAME][7]);
		$worksheet->set_column(8,8,$heading_width[$HISTORY_NAME][8]);
		
		switch($HISTORY_NAME){
			case "PERSONAL" :
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"เลขเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,6,"ถือจ่ายปีนี้", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,7,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,8,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,6,"ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,7,"ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,8,"อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				break;	
			case "SALARY" :
				$xlsRow++;	
				$worksheet->write($xlsRow,0,"เงินพิเศษ	", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,5,"	การเลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,6,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,7,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,8,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"เงินส่วนบุคคล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"เงินประจำตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,5,"วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));	
				$worksheet->write($xlsRow,6,"รูปแบบ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,7,"จำนวนเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,8,"ผลต่าง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				break;	
			case "DEPART" :
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ฝ่ายทะเบียนและบัตร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,6,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,7,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,8,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;	
			case "SUMLEVEL" :
				$xlsRow++;
				$worksheet->write($xlsRow,0," ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,5,"ผลรวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,6,"จำนวนขั้น", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow,7,"เงินล่าสุด", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow,8,"จำนวนผลต่าง", set_format("xlsFmtTableHeader", "B", "R", "TLRB", 0));
				break;	
		} // end switch case
	} // function		

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count,$arr_mov_code,$DATE_DISPLAY;
		global $select_org_structure,$position_table,$position_join;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from						(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							$search_condition ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
												PER_BIRTHDATE, SUBSTR(trim(a.PER_OCCUPYDATE), 1, 10) as PER_OCCUPYDATE													
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PN_CODE=d.PN_CODE(+)
												$search_condition ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												LEFT(trim(a.PER_OCCUPYDATE), 10) as PER_OCCUPYDATE
							 from						(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							$search_condition ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		while($data = $db_dpis2->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);

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
			//ระบุวันก่อนหน้าที่ได้รับโอน หาจากวันสิ้นสุดการดำรงตำแหน่ง
			if(count($arr_mov_code)) $mov_code = " and trim(MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
			if($DPISDB=="odbc"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from				PER_POSITIONHIS 
								where			PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			}elseif($DPISDB=="oci8"){	
				$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_OCCUPYDATE' ";
				$cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from 		PER_POSITIONHIS
								where 	PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";				 					 
			}elseif($DPISDB=="mysql"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_OCCUPYDATE' ";
				$cmd = " select 		POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
														PL_CODE, PT_CODE, PN_CODE, EP_CODE
								from				PER_POSITIONHIS 
								where			PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	 	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			} // end if
			$count_poh = $db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
//			echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$OLD_ORG = $data2[POH_ORG_TRANSFER];		
			$LEVEL_NO = $data2[LEVEL_NO];		
			$PL_CODE = $data2[PL_CODE];		
			$PT_CODE = trim($data[PT_CODE]);
			$PN_CODE = $data2[PN_CODE];		
			$EP_CODE = $data2[EP_CODE];		

			$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			if($search_per_type==1) {
				$cmd = "select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PL_NAME]);
	
				$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME $POSITION_TYPE" : "") . (trim($PM_NAME) ?")":"");
				else
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
			} else{ // 2 || 3 || 4
				$cmd = "select $line_name as PL_NAME from $line_table b where $line_code='$PN_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PN_NAME]);
			} //end if

			if ($count_poh) {
				$data_count++;
				$person_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = $person_count .". ";
				$arr_content[$data_count][prename] = $PN_NAME;
				$arr_content[$data_count][name] = $PER_NAME ." ". $PER_SURNAME;
				$arr_content[$data_count][position] = $PL_NAME;
				$arr_content[$data_count][levelname] = $LEVEL_NAME; 
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
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}
				break;
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
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
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
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
	if(count($arr_mov_code)) $mov_code = " and trim(e.MOV_CODE) in (". implode(" , ", $arr_mov_code) . ")";
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
											$search_condition $mov_code
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=e.PER_ID(+) and SUBSTR(a.PER_OCCUPYDATE,1,10) = SUBSTR(e.POH_EFFECTIVEDATE,1,10)
											$search_condition $mov_code
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and LEFT(a.PER_OCCUPYDATE,10) = LEFT(e.POH_EFFECTIVEDATE,10))
											$search_condition $mov_code
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 8)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);	
						list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
		} // end for
		
		if(count($arr_rpt_order) == 0) list_person($search_condition, $addition_condition);
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
		} // end if

		print_header("PERSONAL");			//---1
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PRE_NAME = $arr_content[$data_count][prename];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NAME = $arr_content[$data_count][levelname];
			$EDU_USE = $arr_content[$data_count][educate_use];
			$EDU_MAX = $arr_content[$data_count][educate_max];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$POSTDATE = $arr_content[$data_count][posdate]; 
			$OLD_ORG = $arr_content[$data_count][old_org];
			$MOV_NAME = $arr_content[$data_count][reason];
			
			//---ข้อมูลบุคคล
			print_header("DEPART");		//---2
			$border = "";
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "-", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$PRE_NAME.$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "-", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$POSITION.$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$OLD_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "-", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "-", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "-", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			//===============================================================
			//---เงินพิเศษ + การเลื่อนขั้น  <ดึงค่ามาใส่>
			print_header("SALARY");		//---3
			$border = "";
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write($xlsRow, 1, "1", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write($xlsRow, 3, "2",  set_format("xlsFmtTableDetail", "", "C", "L", 0));
			$worksheet->write_string($xlsRow, 4, "",   set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write($xlsRow, 5, "3", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "a", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "5", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "6", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			//===============================================================
			print_header("SUMLEVEL");	//---4
			//===============================================================
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