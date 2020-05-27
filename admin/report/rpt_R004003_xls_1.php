<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", "1800");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 
/*
	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($search_per_type==1) $select_list .= "b.PL_CODE";
				elseif($search_per_type==2) $select_list .= "b.PN_CODE";

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";

				if($search_per_type==1) $heading_name .= " สายงาน";
				elseif($search_per_type==2) $heading_name .= " ชื่อตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "b.ORG_ID";
	if(!trim($select_list)) $select_list = "b.ORG_ID";
*/
	$search_level_no = trim($search_level_no);
	$search_condition = $search_condition1 = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
//	$arr_search_condition[] = "(trim(e.MOV_CODE) in ('10110', '10120', '10130', '10410', '10420', '10430', '10440', '10450', '10460', '10510', '10520') and rtrim(e.LEVEL_NO) = '$search_level_no')";
//	$arr_search_condition[] = "(rtrim(e.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(rtrim(a.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = "ทั้งส่วนราชการ";

//	include ("../report/rpt_condition3.php");
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

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
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

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
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

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
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
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		}elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
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
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
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

	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];
	
	if($DPISDB=="odbc"){  
			///$search_condition .= " and (rtrim(e.LEVEL_NO) <>'$search_level_no') "; 
			$search_condition1 .= $search_condition;
			$search_condition1 .= " and rtrim(e.LEVEL_NO)='$search_level_no'"; 		//สำหรับที่ไม่มีประวัติการดำรงตำแหน่งก่อนหน้า มาถึงก็เป็นระดับนี้เลย (1 record)
	if ($search_level_no=="O1")
		$search_condition .= " and e.LEVEL_NO in('04','03','02','01') ";
	elseif ($search_level_no=="O2")
		$search_condition .= " and e.LEVEL_NO in('06','05','04','O1') ";
	elseif ($search_level_no=="O3")
		$search_condition .= " and e.LEVEL_NO in('08','07','06','O2') ";
	elseif ($search_level_no=="O4")
		$search_condition .= " and e.LEVEL_NO in('09','08','O3') ";
	elseif ($search_level_no=="K1")
		$search_condition .= " and e.LEVEL_NO in('05','04','03') ";
	elseif ($search_level_no=="K2")
		$search_condition .= " and e.LEVEL_NO in('07','06','05') ";
	elseif ($search_level_no=="K3")
		$search_condition .= " and e.LEVEL_NO in('08','07','K2') ";
	elseif ($search_level_no=="K4")
		$search_condition .= " and e.LEVEL_NO in('09','08','K3') ";
	elseif ($search_level_no=="K5")
		$search_condition .= " and e.LEVEL_NO in('10','09','K4') ";
	elseif ($search_level_no=="D1")
		$search_condition .= " and e.LEVEL_NO in('08','07') ";
	elseif ($search_level_no=="D2")
		$search_condition .= " and e.LEVEL_NO in('09','08','D1') ";
	elseif ($search_level_no=="M1")
		$search_condition .= " and e.LEVEL_NO in('09','08') ";
	elseif ($search_level_no=="M2")
		$search_condition .= " and e.LEVEL_NO in('11','10','09','M1') ";	
	} else {
			///$search_condition .= " and (rtrim(e.LEVEL_NO) !='$search_level_no') "; 
			$search_condition1 .= $search_condition;
			$search_condition1 .= " and rtrim(e.LEVEL_NO)='$search_level_no'"; 		//สำหรับที่ไม่มีประวัติการดำรงตำแหน่งก่อนหน้า มาถึงก็เป็นระดับนี้เลย (1 record)
	if ($search_level_no=="O1")
		$search_condition .= " and e.LEVEL_NO in('04','03','02','01') ";
	elseif ($search_level_no=="O2")
		$search_condition .= " and e.LEVEL_NO in('06','05','04','O1') ";
	elseif ($search_level_no=="O3")
		$search_condition .= " and e.LEVEL_NO in('08','07','06','O2') ";
	elseif ($search_level_no=="O4")
		$search_condition .= " and e.LEVEL_NO in('09','08','O3') ";
	elseif ($search_level_no=="K1")
		$search_condition .= " and e.LEVEL_NO in('05','04','03') ";
	elseif ($search_level_no=="K2")
		$search_condition .= " and e.LEVEL_NO in('07','06','05','K1') ";
	elseif ($search_level_no=="K3")
		$search_condition .= " and e.LEVEL_NO in('08','07','K2') ";
	elseif ($search_level_no=="K4")
		$search_condition .= " and e.LEVEL_NO in('09','08','K3') ";
	elseif ($search_level_no=="K5")
		$search_condition .= " and e.LEVEL_NO in('10','09','K4') ";
	elseif ($search_level_no=="D1")
		$search_condition .= " and e.LEVEL_NO in('08','07') ";
	elseif ($search_level_no=="D2")
		$search_condition .= " and e.LEVEL_NO in('09','08','D1') ";
	elseif ($search_level_no=="M1")
		$search_condition .= " and e.LEVEL_NO in('09','08') ";
	elseif ($search_level_no=="M2")
		$search_condition .= " and e.LEVEL_NO in('11','10','09','M1') ";
	}
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||รายชื่อลูกจ้างที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||รายชื่อพนักงานราชการที่ดำรงตำแหน่งในระดับ ". level_no_format($level_name) ." เรียงตามลำดับอาวุโส";
	$report_code = "R0403";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 1, 35);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "วุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สาขา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "วันเข้าสู่ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "เครื่องราชฯ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));	
		$worksheet->write($xlsRow, 9, "วันบรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "วันเดือนปีเกิด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "วันเกษียณ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "วิชาเอก", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "(อายุราชการ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "(อายุตัว)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER				
					   from			(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and (e.LEVEL_NO < a.LEVEL_NO)
					    group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by  e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) 
						 ";
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select 	distinct e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 		
											a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER
								from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
								where a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID(+) and b.PL_CODE=f.PL_CODE(+) 
											and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
								$search_condition 											
								 group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 											 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
								 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								 			SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) 
								"; 
								//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
				 	   from			(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and e.LEVEL_NO < a.LEVEL_NO)
												) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition
				 	group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
					order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10)  ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER					 
					 	  from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in$arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) 				  
						";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
											c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER 
					   from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_POS_NAME f,
					   				PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
				 	   where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
									and a.PER_ID=e.PER_ID(+) and b.PN_CODE=f.PN_CODE(+)
									and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)  and e.LEVEL_NO in$arr_level_no[$search_level_no]
									$search_condition
 						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
						 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
						 			SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";	
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER			
					   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in $arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
		}
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
					   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								and e.LEVEL_NO in $arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) 							
						";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
											c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER 			 
						   from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_EMPSER_POS_NAME f,
					   				PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
				 	   where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
									and a.PER_ID=e.PER_ID(+) and b.EP_CODE=f.EP_CODE(+)
									and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)  and e.LEVEL_NO in$arr_level_no[$search_level_no]
									$search_condition
 						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
						 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
						 			 SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";		
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
											c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
											MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MAX(i.DC_ORDER) as DC_ORDER	
				 	   from			(
										(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j (a.LEVEL_NO=j.LEVEL_NO)
								 and e.LEVEL_NO in$arr_level_no[$search_level_no]
								$search_condition
						group by e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
 								 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),MAX(i.DC_ORDER) desc,a.PER_SALARY desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
		}
	} // end if
	//สร้าง query ใหม่ สำหรับข้อมูล record เดียว
	$cmd1=$cmd;
	$cmd1 = str_replace($search_condition,$search_condition1,$cmd1); 
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("a.ORG_ID_1", "b.ORG_ID_1", $cmd);
		$cmd = str_replace("a.ORG_ID_2", "b.ORG_ID_2", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$data_count = 0;
	$person_count = 0;
	//ประมวลผลข้าราชการที่ดำรงตำแหน่ง ตามiระดับตำแหน่งที่เลือกมา
	$count_data1 = $db_dpis->send_cmd($cmd1);
	while($data = $db_dpis->get_array()){
		$ARRALL_PERID_LEVELNO[] = $data[PER_ID];
	}	//end while
//==========================================
	
	$count_data = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){		
		if($PER_ID == $data[PER_ID]) continue;
		//$person_count++;		
		
		$PER_ID = $data[PER_ID];
		if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
			$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
		}
		$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
		$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
		$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
		$POS_NO[$PER_ID] = trim($data[POS_NO]);
		$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
		$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
		$LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
		$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
		$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
		$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
		$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
		$ORG_ID_1=trim($data[ORG_ID_1]);
		$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
		$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
		if(trim($POH_EFFECTIVEDATE)){
			//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.LEVEL_NO และ e.POH_EFFECTIVEDATE)
			$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;
			$arr_temp = explode("-", $POH_EFFECTIVEDATE);
			$POH_EFFECTIVEDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);	
		} // end if	
		$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		if(trim($PER_BIRTHDATE)){
			$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
		if(trim($PER_STARTDATE)){
			$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_STARTDATE);
			$PER_STARTDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		if(trim($PER_RETIREDATE)){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a 
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
					   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		} // end if
		$db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
		$PER_EDUCATE[$PER_ID] = "";
		while($data2 = $db_dpis2->get_array()){
			if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
			$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
			$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
		} // end while

		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a, PER_DECORATION b
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
					   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		} // end if
		$db_dpis2->send_cmd($cmd);
//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
		
		$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
		$db_dpis3->send_cmd($cmd2);
		$data_org=$db_dpis3->get_array();
		$ORG_ID_1_NAME[$PER_ID]=$data_org[ORG_NAME];
	} // end while
	
	//============================================================================
	//เปรียบเทียบ Array เพื่อคัดหา PER_ID ที่มีเพียง record เดียว เข้ามาก็เป็นตำแหน่งนี้เลย ไม่มีประวัติการดำรงตน.ก่อนหน้า
	$result = array_diff($ARRALL_PERID_LEVELNO, $ARRALL_HAVE_PERID_LEVELNO);
	$strresult=implode("','",$result);
	$cmd2 = $cmd1; 
	$cmd2 = str_replace($search_condition1,$search_condition1." and e.PER_ID in('$strresult') ",$cmd2); 
	$count_data2 = $db_dpis->send_cmd($cmd2);
	while($data = $db_dpis->get_array()){		
		$PER_ID = $data[PER_ID];
		$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
		$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
		$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
		$POS_NO[$PER_ID] = trim($data[POS_NO]);
		$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
		$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
		$LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
		$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
		$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
		$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
		$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
		$ORG_ID_1=trim($data[ORG_ID_1]);
		$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
		$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
		if(trim($POH_EFFECTIVEDATE)){
			//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.LEVEL_NO และ e.POH_EFFECTIVEDATE)
			$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;
			$arr_temp = explode("-", $POH_EFFECTIVEDATE);
			$POH_EFFECTIVEDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);	
		} // end if	
		$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		if(trim($PER_BIRTHDATE)){
			$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
		if(trim($PER_STARTDATE)){
			$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
			$arr_temp = explode("-", $PER_STARTDATE);
			$PER_STARTDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		if(trim($PER_RETIREDATE)){
			$arr_temp = explode("-", $PER_RETIREDATE);
			$PER_RETIREDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		} // end if	
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a 
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
									) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
					   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
					   from		
					   (
					   PER_EDUCATE a
								left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
								)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
					   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%||2||%' or a.EDU_TYPE like '%||3||%')
					   order by 	a.EDU_SEQ
					 ";
		} // end if
		$db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
		$PER_EDUCATE[$PER_ID] = "";
		while($data2 = $db_dpis2->get_array()){
			if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
			$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
			$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
		} // end while

		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a, PER_DECORATION b
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
					   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc
					";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
					   from		PER_DECORATEHIS a
								left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
					   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
					   order by 	LEFT(trim(a.DEH_DATE), 10) desc
					";
		} // end if
		$db_dpis2->send_cmd($cmd);
//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
		
		$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
		$db_dpis3->send_cmd($cmd2);
		$data_org=$db_dpis3->get_array();
		$ORG_ID_1_NAME[$PER_ID]=$data_org[ORG_NAME];
	} // end while
	
	//รวม num_results ของ 2 query 
	$count_data = ($count_data+$count_data2);			//+count($result);	

/**
print("<pre>");
print($count_data." : <br>");	
print_r($ARRALL_PERID_LEVELNO);	
print("<br>=======================<br>");	
print_r($ARRALL_HAVE_PERID_LEVELNO);		
print("<br>=======================<br>");	
print_r($result);
print("</pre>");
**/
	
	//แสดงข้อมูลตาม Array ที่เรียงมา และใส่ค่าลงไปแต่ละตัว
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O2','O1');
	$L["O3"]=array('08','07','06','O3','O2');
	$L["O4"]=array('09','08','O4','O3');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K2','K1');
	$L["K3"]=array('08','07','K3','K2');
	$L["K4"]=array('09','08','K4','K3');
	$L["K5"]=array('10','09','K5','K4');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D2','D1');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M2','M1');
	
for($i=0; $i < count($L[$search_level_no]); $i++){	
	$index=$L[$search_level_no][$i];		//$index=level no
	asort($PER_EFFECTIVEDATE[$index]);		//เรียงลำดับตาม ระดับตน. และวันที่เข้าสู่ระดับ
	if(is_array($PER_EFFECTIVEDATE[$index]) && !empty($PER_EFFECTIVEDATE[$index])){
		foreach($PER_EFFECTIVEDATE[$index] as $key=>$value){	//key=perid, value=min_effectivedate
			$person_count++;		
			$PER_ID=$key;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
			$arr_content[$data_count][position] = $PL_NAME[$PER_ID]. " ". level_no_format($LEVEL_NAME[$PER_ID]) . (($PT_CODE[$PER_ID] != "11" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
			$arr_content[$data_count][posno] = $POS_NO[$PER_ID];
			$arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
			$arr_content[$data_count][em_name] = $EM_NAME[$PER_ID];
			$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	//$value
			//หาระดับตำแหน่งนั้น--------------------
			$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$index' ";
			$db_dpis3->send_cmd($cmd2);
			$levelname=$db_dpis3->get_array();
			//---------------------------------------------
			$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$index?level_no_format($index):"";
			$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
			$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
			$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID];
			$arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID];	
			$arr_content[$data_count][per_retiredate]=$PER_RETIREDATE2[$PER_ID];
			$data_count++;
	
			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][org_name] = (($ORG_ID_1_NAME[$PER_ID])?"$ORG_ID_1_NAME[$PER_ID]  ":"") . $ORG_NAME[$PER_ID];
			$arr_content[$data_count][per_work] = " (".$PER_WORK[$PER_ID]." ปี)";
			$arr_content[$data_count][per_age] = " (".$PER_AGE[$PER_ID]." ปี)";
			$data_count++;
		}
	}
} //end for	
	
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_2_1 = $arr_content[$data_count][org_name];
			$NAME_3 = $arr_content[$data_count][posno];
			$NAME_4 = $arr_content[$data_count][educate];
			$NAME_5 = $arr_content[$data_count][poh_effectivedate];
			$NAME_11 = $arr_content[$data_count][per_sortlevel];
			$NAME_6 = $arr_content[$data_count][per_salary];
			$NAME_7 = $arr_content[$data_count][decorate];
			$NAME_8 = $arr_content[$data_count][per_startdate];
			$NAME_8_1 = $arr_content[$data_count][per_work];
			$NAME_9 = $arr_content[$data_count][per_birthdate];
			$NAME_9_1 = $arr_content[$data_count][per_age];
			$NAME_10=$arr_content[$data_count][per_retiredate];
			$NAME_12 = $arr_content[$data_count][em_name];
						
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$NAME_3", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$NAME_4", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$NAME_12", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$NAME_5", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$NAME_11", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$NAME_6", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$NAME_7", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$NAME_8", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$NAME_9", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$NAME_10", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			if($REPORT_ORDER == "DETAIL"){
				$worksheet->write_string($xlsRow, 1, "$NAME_2_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9,($NAME_8_1?$NAME_8_1:"-"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, ($NAME_9_1?$NAME_9_1:"-"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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