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
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		 $line_search_code=trim($search_pl_code);
		 $line_search_name=trim($search_pl_name);
		 $line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
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
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	if(!trim($RPTORD_LIST)){ 
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
				if($select_org_structure==0) $select_list .= "e.POH_ORG3 as ORG_ID_3";
				else if($select_org_structure==1)  $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_ORG3";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG1";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "e.POH_UNDER_ORG2";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "e.POH_UNDER_ORG2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";

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
	if(!trim($order_by)){
		if($select_org_structure==0) $order_by = "e.POH_ORG3";
		else if($select_org_structure==1) $order_by = "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure==0)  $select_list = "e.POH_ORG3";
		else if($select_org_structure==1)   $select_list = "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);		 
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);		 
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);		 
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			 $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);		 
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} 
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
			}
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} 
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			}
		}
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
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
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	if ($search_mov_code[0]==1) { 
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '35', '208', '13', '27', '28', '29', '59', '20101', '20102', '20103', '20104', '20105', '20106', '20107', '20108', '20109', '20110', '20112', '203', '204', '205', '209' ";
		else
			$arr_mov_code[] = " '118', '11810', '11820', '11830', '120', '12010', '12020', '12030', '121', '12110', '122', '12210' ";
		$mov_name .= "ลาออก ";
	} 
	if ($search_mov_code[0]==2 || $search_mov_code[1]==2) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '5', '202' ";
		else
			$arr_mov_code[] = " '106','10610','10620' ";
		$mov_name .= "ให้โอน ";
	} 
	if ($search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '17','201' ";
		else
			$arr_mov_code[] = " '119','11910' ";
		$mov_name .= "เกษียณอายุ ";
	}
	if ($search_mov_code[0]==4 || $search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
		if ($BKK_FLAG==1)
			$arr_mov_code[] = " '30', '206' ";
		else
			$arr_mov_code[] = " '123','12310' ";
		$mov_name .= "เสียชีวิต ";
	}

	if ($mov_name=="เกษียณอายุ ") {
		if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
		elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
		elseif($DPISDB=="mysql") $search_condition .= "and (LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE)";
	} elseif (strpos($mov_name,"เกษียณอายุ") !== false) {
		if($DPISDB=="odbc") $search_condition .= " and ((LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
		elseif($DPISDB=="oci8") $search_condition .= " and ((SUBSTR(trim(a.PER_RETIREDATE), 1, 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01'))";
		elseif($DPISDB=="mysql") $search_condition .= " and ((LEFT(trim(a.PER_RETIREDATE), 10) = '".($search_budget_year - 543)."-10-01' 
			and a.PER_POSDATE = a.PER_RETIREDATE) or (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
	} else {
		if($DPISDB=="odbc") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="oci8") $search_condition .= " and (SUBSTR(trim(a.PER_POSDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and SUBSTR(trim(a.PER_POSDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="mysql") $search_condition .= " and (LEFT(trim(a.PER_POSDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' 
			and LEFT(trim(a.PER_POSDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	}
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type] ที่$mov_name ในปีงบประมาณ $show_budget_year";
	$report_code = "R0311";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name, $search_mov_code;
		
		if ($search_mov_code[0]==1 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3 || $search_mov_code[0]==4 || 
			$search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
			$worksheet->set_column(0, 0, 5);
			$worksheet->set_column(1, 1, 8);
			$worksheet->set_column(2, 2, 30);
			$worksheet->set_column(3, 3, 40);
			$worksheet->set_column(4, 4, 20);
			$worksheet->set_column(5, 5, 25);
			$worksheet->set_column(6, 6, 25);
			$worksheet->set_column(7, 7, 12);
			$worksheet->set_column(8, 8, 12);
			$worksheet->set_column(9, 9, 12);
			$worksheet->set_column(10, 10, 40);

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 1, "คำนำ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 2, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 4, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 5, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 6, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 7, "วัน/เดือน/ปี", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 8, "วัน/เดือน/ปี", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 9, "วัน/เดือน/ปี", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 10, "เหตุผลใน", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 1, "หน้านาม", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 6, "สูงสุด", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 7, "เกิด", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 8, "ที่บรรจุ", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 9, "ที่ลาออก", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 10, "การลาออก", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
		} else {
			$worksheet->set_column(0, 0, 5);
			$worksheet->set_column(1, 1, 8);
			$worksheet->set_column(2, 2, 30);
			$worksheet->set_column(3, 3, 40);
			$worksheet->set_column(4, 4, 20);
			$worksheet->set_column(5, 5, 45);
			$worksheet->set_column(6, 6, 45);
			$worksheet->set_column(7, 7, 12);
			$worksheet->set_column(8, 8, 12);
			$worksheet->set_column(9, 9, 40);
			$worksheet->set_column(10, 10, 40);

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 1, "คำนำ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 2, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 4, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 5, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 6, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 7, "วัน/เดือน/ปี", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 8, "วัน/เดือน/ปี", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 9, "ส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$worksheet->write($xlsRow, 10, "ลักษณะการโอน", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 1, "หน้านาม", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 5, "ที่ให้โอน", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 6, "สูงสุด", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 7, "เกิด", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 8, "ที่ให้โอน", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 9, "ที่รับโอน", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
			$worksheet->write($xlsRow, 10, "(ปกติ/สอบ)", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
	}
	} // function		

function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$RPT_N,$select_org_structure;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $arr_mov_code, $position_table, $position_join, $line_code, $DATE_DISPLAY;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as 
											PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE, 
											LEFT(trim(a.PER_POSDATE), 10) as PER_POSDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
											$search_condition ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as 
											PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE, 
											SUBSTR(trim(a.PER_POSDATE), 1, 10) as PER_POSDATE, 
											SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE
							 from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+) and a.PN_CODE=d.PN_CODE(+)
											$search_condition ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as 
											PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, $line_code as PL_CODE, a.LEVEL_NO, a.MOV_CODE, 
											LEFT(trim(a.PER_POSDATE), 10) as PER_POSDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE
							 from	(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
											$search_condition ";
		} // end if
		if($select_org_structure==1){
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);

			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
			$PER_POSDATE = substr(trim($data[PER_POSDATE]), 0, 10);
			$PER_RETIREDATE = substr(trim($data[PER_RETIREDATE]), 0, 10);
			if (!$PER_POSDATE) $PER_POSDATE = $PER_RETIREDATE;

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
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_POSDATE' ";
				$cmd = " select 	POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
												PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from		PER_POSITIONHIS 
								where		PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			}elseif($DPISDB=="oci8"){	
				$BEFORE_MOVDATE = " SUBSTR(POH_EFFECTIVEDATE,1,10)	= '$PER_POSDATE' ";
				$cmd = "select 		POH_ID, SUBSTR(POH_EFFECTIVEDATE,1,10),SUBSTR(POH_ENDDATE,1,10), POH_ORG_TRANSFER, 
												LEVEL_NO, PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from 		PER_POSITIONHIS
								where 	PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by 	SUBSTR(POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(POH_ENDDATE,1,10) desc ";				 					 
			}elseif($DPISDB=="mysql"){
				$BEFORE_MOVDATE = " LEFT(POH_EFFECTIVEDATE,10) = '$PER_POSDATE' ";
				$cmd = " select 	POH_ID, LEFT(POH_EFFECTIVEDATE,10), LEFT(POH_ENDDATE,10), POH_ORG_TRANSFER, LEVEL_NO, 
												PL_CODE, PT_CODE, PN_CODE, EP_CODE, MOV_CODE
								from		PER_POSITIONHIS 
								where		PER_ID=$PER_ID and $BEFORE_MOVDATE $mov_code
								order by	LEFT(POH_EFFECTIVEDATE,10) desc, LEFT(POH_ENDDATE,10) desc ";
			} // end if
			$count_poh = $db_dpis3->send_cmd($cmd);
//				$db_dpis3->show_error();
//				echo "<br>$cmd<br>";
			$data2 = $db_dpis3->get_array();
			$OLD_ORG = trim($data2[POH_ORG_TRANSFER]);		
			$LEVEL_NO = trim($data2[LEVEL_NO]);		
			$PL_CODE = trim($data2[PL_CODE]);		
			$PT_CODE = trim($data2[PT_CODE]);
			$PN_CODE = trim($data2[PN_CODE]);		
			$EP_CODE = trim($data2[EP_CODE]);		
			$MOV_CODE = trim($data2[MOV_CODE]);

			if (!$LEVEL_NO) $LEVEL_NO = trim($data[LEVEL_NO]);		
			if (!$PL_CODE) $PL_CODE = trim($data[PL_CODE]);		
			if (!$MOV_CODE) $MOV_CODE = trim($data[MOV_CODE]);		

			$cmd = " select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$POSITION_TYPE = trim($data2[POSITION_TYPE]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			if($search_per_type==1 || $search_per_type==5) {
				$cmd = "select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[PL_NAME]);
	
				$cmd = "select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PT_NAME = trim($data[PT_NAME]);

				if ($RPT_N)
					$PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
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
			} elseif($search_per_type==4) {
				$cmd = "select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				$PL_NAME =  trim($data2[TP_NAME]);
			}

			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data2 = $db_dpis3->get_array();
			$MOV_NAME = $data2[MOV_NAME];
			
			if ($count_poh || ($PER_POSDATE == $PER_RETIREDATE)) {
				$PER_POSDATE = show_date_format($PER_POSDATE, $DATE_DISPLAY);

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
				$arr_content[$data_count][startdate]	= $PER_STARTDATE;
				$arr_content[$data_count][posdate] =  $PER_POSDATE;
				$arr_content[$data_count][old_org] = $OLD_ORG;	 //ส่วนราชการเดิมก่อนโอน
				$arr_content[$data_count][reason] = $MOV_NAME;
			}
			
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_NAME, $ORG_NAME_1, $ORG_NAME_2;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_NAME){
						if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_ORG3 = '$ORG_NAME')";
						}else  if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						}
					}
				break;
				case "ORG_1" :
				  if($ORG_NAME_1){
					if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_UNDER_ORG1 = '$ORG_NAME_1')";
					}else if($select_org_structure==1){
						$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
					}
				}	
				break;
				case "ORG_2" :
					if($ORG_NAME_2){
						if($select_org_structure==0){
							$arr_addition_condition[] = "(e.POH_UNDER_ORG2 = '$ORG_NAME_2')";
						}else  if($select_org_structure==1){
							$arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_2)";
						}
					}
				break;
			}
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
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e
						 where			$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from			(
												(
													PER_PERSONAL a 
													left join $position_table b on ($position_join) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1){
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
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
					if($select_org_structure==0) {
						$ORG_NAME = $ORG_ID;
					} elseif($select_org_structure==1) {
						$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
						$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
						$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_NAME = $data2[ORG_NAME];
					}
				} // end if

				$addition_condition = generate_condition($rpt_order_index);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;

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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();
		
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
			$STARTDATE = $arr_content[$data_count][startdate];
			$POSDATE = $arr_content[$data_count][posdate]; 
			$OLD_ORG = $arr_content[$data_count][old_org];
			$MOV_NAME = $arr_content[$data_count][reason];
			$REPORT_ORDER = $arr_content[$data_count][type];
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PRE_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$POSITION", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$EDU_USE", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$EDU_MAX", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE):$BIRTHDATE), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				if ($search_mov_code[0]==1 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3 || 
					$search_mov_code[0]==4 || $search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
					$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSDATE):$POSDATE), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				} else {
					$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSDATE):$POSDATE), set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 9, "$OLD_ORG", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				}
				$worksheet->write_string($xlsRow, 10, "$MOV_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PRE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$POSITION", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$EDU_USE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$EDU_MAX", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE):$BIRTHDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				if ($search_mov_code[0]==1 || $search_mov_code[0]==3 || $search_mov_code[1]==3 || $search_mov_code[2]==3 || 
					$search_mov_code[0]==4 || $search_mov_code[1]==4 || $search_mov_code[2]==4 || $search_mov_code[3]==4) {
					$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSTDATE):$POSDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				} else {
					$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSTDATE):$POSDATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 9, "$OLD_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				}
				$worksheet->write_string($xlsRow, 10, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} // end if
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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