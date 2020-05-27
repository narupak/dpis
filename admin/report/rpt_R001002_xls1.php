<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "CO_LEVEL", "PROVINCE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $select_list .= "e.ORG_SEQ_NO, d.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_SEQ_NO, a.ORG_ID";
				elseif($select_org_structure==1) $order_by .= "e.ORG_SEQ_NO, d.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "a.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " สายงาน";
				break;
			case "CO_LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.CL_NAME, c.LEVEL_NO_MIN";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "c.LEVEL_NO_MIN";

				$heading_name .= " ช่วงระดับตำแหน่ง";
				break;
			case "PROVINCE" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.PV_CODE";
				elseif($select_org_structure==1) $select_list .= "e.PV_CODE";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.PV_CODE";
				elseif($select_org_structure==1) $order_by .= "e.PV_CODE";

				$heading_name .= " จังหวัด";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $order_by = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, a.ORG_ID";
		elseif($select_org_structure==1) $select_list = "e.ORG_SEQ_NO, d.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='02')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='04')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.CT_CODE) = '$search_ct_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$search_pv_code')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "จำนวนตำแหน่งจำแนกตามลักษณะของตำแหน่ง";
	$report_code = "R0102";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 60);
		$worksheet->set_column(1, 1, 8);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 8);
		$worksheet->set_column(6, 6, 8);
		$worksheet->set_column(7, 7, 8);
		$worksheet->set_column(8, 8, 8);

		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ทั่วไป", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "วิชาชีพเฉพาะ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "บริหาร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function count_position($position_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;		
		global $select_org_structure, $DEPARTMENT_ID;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		// $position_type = 1 ==> ตำแหน่ง ทั่วไป
		// $position_type = 2 ==> ตำแหน่ง วิชาชีพเฉพาะ
		// $position_type = 3 ==> ตำแหน่ง บริหาร
		if($select_org_structure == 0){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.POS_ID) as count_position
						   from			(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)																												
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
						   where		d.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
										$search_condition
					   	";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_TYPE d
								 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.PT_CODE=d.PT_CODE
													and d.PT_GROUP=$position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.POS_ID) as count_position
						   from			(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)																												
										) inner join PER_TYPE d on (a.PT_CODE=d.PT_CODE)
						   where		d.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
										$search_condition
					   	";
			} // end if
		}elseif($select_org_structure == 1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			(
														(
															(
																(
																	PER_POSITION a
																	inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
															) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
														) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
													) inner join PER_TYPE f on (a.PT_CODE=f.PT_CODE)
								 where		f.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_TYPE f
								 where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID and a.PT_CODE=f.PT_CODE
													and f.PT_GROUP=$position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.POS_ID) as count_position
								 from			(
														(
															(
																(
																	PER_POSITION a
																	inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
															) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
														) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
													) inner join PER_TYPE f on (a.PT_CODE=f.PT_CODE)
								 where		f.PT_GROUP = $position_type and a.DEPARTMENT_ID=$DEPARTMENT_ID
													$search_condition
							   ";
			} // end if
		} // end if

		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data = $db_dpis2->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$count_position = $data[count_position] + 0;
		return $count_position;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0)
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					elseif($select_org_structure==1)
						if($ORG_ID) $arr_addition_condition[] = "(d.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(d.ORG_ID = 0 or d.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($select_org_structure==0)
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($select_org_structure==0)
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
				case "CO_LEVEL" :
					if($CL_NAME) $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME')";
					else $arr_addition_condition[] = "(trim(a.CL_NAME) = '$CL_NAME' or a.CL_NAME is null)";
				break;
				case "PROVINCE" :
					if($select_org_structure==0)
						if($PV_CODE) $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(b.PV_CODE) = '$PV_CODE' or b.PV_CODE is null)";
					elseif($select_org_structure==1)
						if($PV_CODE) $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE')";
						else $arr_addition_condition[] = "(trim(e.PV_CODE) = '$PV_CODE' or e.PV_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $CL_NAME, $PV_CODE;
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
				case "LINE" :
					$PL_CODE = -1;
				break;
				case "CO_LEVEL" :
					$CL_NAME = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($select_org_structure == 0){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on (d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_ORG d, PER_ORG g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and b.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												PER_POSITION a 
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
										) inner join PER_ORG d on (b.ORG_ID_REF=d.ORG_ID)
									) inner join PER_ORG g on d.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, d.ORG_SEQ_NO, d.ORG_ID, $order_by
					";
		}
	}elseif($select_org_structure == 1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a 
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			PER_POSITION a, PER_ORG b, PER_CO_LEVEL c, PER_PERSONAL d, PER_ORG_ASS e, PER_ORG_ASS f, PER_ORG_ASS g
					   where		a.ORG_ID=b.ORG_ID and a.CL_NAME=c.CL_NAME and a.POS_ID=d.POS_ID and d.ORG_ID=e.ORG_ID 
					   				and e.ORG_ID_REF=f.ORG_ID and f.ORG_ID_REF=g.ORG_ID
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct g.ORG_SEQ_NO, g.ORG_ID as MINISTRY_ID, f.ORG_SEQ_NO, f.ORG_ID as DEPARTMENT_ID, $select_list
					   from			(
										(
											(
												(
													(
														PER_POSITION a 
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_CO_LEVEL c on (a.CL_NAME=c.CL_NAME)
												) inner join PER_PERSONAL d on (a.POS_ID=d.POS_ID)
											) inner join PER_ORG_ASS e on (d.ORG_ID=e.ORG_ID)
										) inner join PER_ORG_ASS f on (e.ORG_ID_REF=f.ORG_ID)
									) inner join PER_ORG_ASS g on (f.ORG_ID_REF=g.ORG_ID)
									$search_condition
					   order by		g.ORG_SEQ_NO, g.ORG_ID, f.ORG_SEQ_NO, f.ORG_ID, $order_by
					";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	initialize_parameter(0);
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){	
			if($data_count > 1){
				$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
				$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
				$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
				
				$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID];
			} // end if
			
			$MINISTRY_ID = $data[MINISTRY_ID];
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];

			$DEPARTMENT_ID = $data[DEPARTMENT_ID];			
			if($select_org_structure==0)
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][id] = $DEPARTMENT_ID;
			$arr_content[$data_count][ref_name] = $MINISTRY_NAME;
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							elseif($select_org_structure==1)
								$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุงาน]";
						}else{
							if($select_org_structure==0)
								$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE == ""){
							$PL_NAME = "[ไม่ระบุสายงาน]";
						}else{
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "CO_LEVEL" :
					if($CL_NAME != trim($data[CL_NAME])){
						$CL_NAME = trim($data[CL_NAME]);

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "CO_LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (trim($CL_NAME)?"ระดับ $CL_NAME":"[ไม่ระบุช่วงระดับตำแหน่ง]");
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE == ""){
							$PV_NAME = "[ไม่ระบุจังหวัด]";
						}else{
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						$arr_content[$data_count][count_1] = count_position(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_position(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_position(3, $search_condition, $addition_condition);
						if($rpt_order_index == 0){
							$GRAND_TOTAL_1[$DEPARTMENT_ID] += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2[$DEPARTMENT_ID] += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3[$DEPARTMENT_ID] += $arr_content[$data_count][count_3];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL_1[$DEPARTMENT_ID] = count_position(1, $search_condition, "");
	$GRAND_TOTAL_2[$DEPARTMENT_ID] = count_position(2, $search_condition, "");
	$GRAND_TOTAL_3[$DEPARTMENT_ID] = count_position(3, $search_condition, "");
	
	$GRAND_TOTAL[$DEPARTMENT_ID] = $GRAND_TOTAL_1[$DEPARTMENT_ID] + $GRAND_TOTAL_2[$DEPARTMENT_ID] + $GRAND_TOTAL_3[$DEPARTMENT_ID];

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2 + $COUNT_3;
			
			$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_TOTAL = 0;
			if($COUNT_TOTAL){ 
				$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
				$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
			} // end if
			if($GRAND_TOTAL[$DEPARTMENT_ID]) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;

			if($REPORT_ORDER == "DEPARTMENT"){
				if($data_count > 0){
					$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = 0;
					if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
						$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
						$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
					} // end if
					$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, ($GRAND_TOTAL_1[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_1[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 2, ($PERCENT_TOTAL_1?number_format($PERCENT_TOTAL_1, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 3, ($GRAND_TOTAL_2[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_2[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 4, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 5, ($GRAND_TOTAL_3[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_3[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 6, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 7, ($GRAND_TOTAL[$DEPARTMENT_ID]?number_format($GRAND_TOTAL[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 8, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					
					$count_org_ref++;
					$xlsRow = 0;
				} // end if
				
				$ORG_ID_REF = $arr_content[$data_count][id];
				$REF_NAME = $arr_content[$data_count][ref_name];

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//

				$temp_report_title = "$REF_NAME||$NAME||$report_title";
				$arr_title = explode("||", $temp_report_title);
				for($i=0; $i<count($arr_title); $i++){
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} // end if
		
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$xlsRow++;
				} // end if
				
				print_header();
			}else{
				$border = "";
				if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, ($COUNT_1?number_format($COUNT_1):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 2, ($PERCENT_1?number_format($PERCENT_1, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 3, ($COUNT_2?number_format($COUNT_2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 4, ($PERCENT_2?number_format($PERCENT_2, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 5, ($COUNT_3?number_format($COUNT_3):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 6, ($PERCENT_3?number_format($PERCENT_3, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 8, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, ($COUNT_1?number_format($COUNT_1):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 2, ($PERCENT_1?number_format($PERCENT_1, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 3, ($COUNT_2?number_format($COUNT_2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 4, ($PERCENT_2?number_format($PERCENT_2, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 5, ($COUNT_3?number_format($COUNT_3):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 6, ($PERCENT_3?number_format($PERCENT_3, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 7, ($COUNT_TOTAL?number_format($COUNT_TOTAL):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write($xlsRow, 8, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end if
			} // end if
		} // end for
				
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = 0;
		if($GRAND_TOTAL[$DEPARTMENT_ID]){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL[$DEPARTMENT_ID] / $GRAND_TOTAL[$DEPARTMENT_ID]) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		$worksheet->write($xlsRow, 1, ($GRAND_TOTAL_1[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_1[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 2, ($PERCENT_TOTAL_1?number_format($PERCENT_TOTAL_1, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 3, ($GRAND_TOTAL_2[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_2[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 4, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 5, ($GRAND_TOTAL_3[$DEPARTMENT_ID]?number_format($GRAND_TOTAL_3[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 6, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 7, ($GRAND_TOTAL[$DEPARTMENT_ID]?number_format($GRAND_TOTAL[$DEPARTMENT_ID]):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 8, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));

		if(count($GRAND_TOTAL) > 1){
			$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = 0;
			if(array_sum($GRAND_TOTAL)){ 
				$PERCENT_TOTAL_1 = (array_sum($GRAND_TOTAL_1) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_2 = (array_sum($GRAND_TOTAL_2) / array_sum($GRAND_TOTAL)) * 100;
				$PERCENT_TOTAL_3 = (array_sum($GRAND_TOTAL_3) / array_sum($GRAND_TOTAL)) * 100;
			} // end if
			$PERCENT_TOTAL = (array_sum($GRAND_TOTAL) / array_sum($GRAND_TOTAL)) * 100;

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, (array_sum($GRAND_TOTAL_1)?number_format(array_sum($GRAND_TOTAL_1)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 2, ($PERCENT_TOTAL_1?number_format($PERCENT_TOTAL_1, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 3, (array_sum($GRAND_TOTAL_2)?number_format(array_sum($GRAND_TOTAL_2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 4, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 5, (array_sum($GRAND_TOTAL_3)?number_format(array_sum($GRAND_TOTAL_3)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 6, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 7, (array_sum($GRAND_TOTAL)?number_format(array_sum($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 8, ($PERCENT_TOTAL?number_format($PERCENT_TOTAL, 2):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		} // end if
	}else{
		$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
				
		//====================== SET FORMAT ======================//
		include "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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