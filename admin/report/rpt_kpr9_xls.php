<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_rpt_order = array("POEMNO"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POEMNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "a.POEM_NO_NAME, IIf(IsNull(a.POEM_NO), 0 , CLng(a.POEM_NO)) as POEM_NO";				
					$order_by .= "a.POEM_NO_NAME, IIf(IsNull(a.POEM_NO), 0 , CLng(a.POEM_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POEM_NO_NAME, a.POEM_NO";
					$order_by .= "a.POEM_NO_NAME, to_number(replace(a.POEM_NO,'-',''))";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POEM_NO_NAME, a.POEM_NO";				
					$order_by .= "a.POEM_NO_NAME, a.POEM_NO";
				}

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POEM_NO_NAME, a.POEM_NO";
	if(!trim($select_list)) $select_list = "a.POEM_NO_NAME, a.POEM_NO";

	$search_condition = "";
	$arr_search_condition[] = "(a.POEM_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$todate = date("Y-m-d");
	$show_date = show_date_format($todate, 3);
	
	$company_name = "แบบ คปร.9 - พลเรือน ส่วนราชการ : $list_type_text";
	$report_title = "แบบสำรวจข้อมูลลูกจ้างประจำที่จ้างด้วยเงินงบประมาณ งบบุคลากร ณ วันที่ ". ($show_date?(($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date):"-");
	$report_code = "คปร.9";

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
		global $heading_name,$ORG_TITLE;
		
		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 27);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 5);
		$worksheet->set_column(6, 6, 4);
		$worksheet->set_column(7, 7, 4);
		$worksheet->set_column(8, 8, 4);
		$worksheet->set_column(9, 9, 4);
		$worksheet->set_column(10, 10, 4);
		$worksheet->set_column(11, 11, 4);
		$worksheet->set_column(12, 12, 18);
		$worksheet->set_column(13, 13, 25);
		$worksheet->set_column(14, 14, 5);
		$worksheet->set_column(15, 15, 8);
		$worksheet->set_column(16, 16, 28);
		$worksheet->set_column(17, 17, 15);
		$worksheet->set_column(18, 18, 20);
		$worksheet->set_column(19, 19, 12);
		$worksheet->set_column(20, 20, 8);
		$worksheet->set_column(21, 21, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "(1)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "(2)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "(3)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "(4)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "(5)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "(6)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 9, "(7)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 12, "(8)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "(9)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "(10)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 15, "(11)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 16, "(12)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 17,"(13)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 18, "(14)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 19, "(15)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 20, "(16)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 21, "(17)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "เลข", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "",0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "L", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, "พื้นที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, "วุฒิการศึกษา/", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "ประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "เพศ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "เกิด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 9, "บรรจุ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 12, "หมวด", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, "ชั้น", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 15, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 16, "ส่วนกลาง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 17, "ปฏิบัติงาน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 18, "ประกาศนียบัตร", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 19, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 20, "ปีที่", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));		
		$worksheet->write($xlsRow, 21, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));		

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 1, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "เขต/แขวง/ศูนย์", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 4, "ประชาชน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ว", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "ด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "ป", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "ว", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "ด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "ป", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 15, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 16, "/ภูมิภาค", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 17, "(จังหวัด)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 18, "เฉพาะทาง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 19, "ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 20, "เกษียณ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POEM_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :	
					if($POEM_NO) $arr_addition_condition[] = "(trim(a.POEM_NO) = '$POEM_NO')";
					else $arr_addition_condition[] = "(trim(a.POEM_NO) = '$POEM_NO' or a.POEM_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POEM_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :	
					$POEM_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	//สำหรับลูกจ้างประจำ
	if($DPISDB=="odbc"){
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
															PER_POS_EMP a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_POS_NAME c on (a.PN_CODE=c.PN_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			 PER_POS_EMP a, PER_ORG b, PER_POS_NAME c,PER_PROVINCE f, PER_ORG_TYPE g
						 where		a.ORG_ID=b.ORG_ID and a.PN_CODE=c.PN_CODE(+)
						 					and b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)
											$search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct 
											b.ORG_ID_REF, $select_list, a.POEM_ID, b.ORG_NAME, c.PN_NAME, c.PG_CODE,  f.PV_NAME, g.OT_NAME
						 from			(
												(
														(
																 PER_POS_EMP a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_POS_NAME c on (a.PN_CODE=c.PN_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		b.ORG_ID_REF, $order_by ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POEMNO" :
					if($POEM_NO != trim($data[POEM_NO_NAME]).trim($data[POEM_NO])){
						$POEM_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);

						$addition_condition = generate_condition($rpt_order_index);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POEM_ID = $data[POEM_ID];

							$ORG_NAME = trim($data[ORG_NAME]);
							$ORG_NAME2 = "";	$LEVEL_PNNAME="";

							$arr_pn_name = explode("ชั้น",trim($data[PN_NAME]));
							$PN_NAME = trim($arr_pn_name[0]);
							if(trim($arr_pn_name[1])){ $LEVEL_PNNAME = trim($arr_pn_name[1]); }

							$OT_NAME = trim($data[OT_NAME]);
							$PV_NAME = trim($data[PV_NAME]);
							$PG_CODE = trim($data[PG_CODE]);
						
							$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='".$PG_CODE."' ";
							$db_dpis2->send_cmd($cmd);
							$data_dpis2 = $db_dpis2->get_array();
							$PG_NAME = $data_dpis2[PG_NAME];

							//หาข้อมูลส่วนตัว
							$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_RETIREDATE,PER_STARTDATE, LEVEL_NO, PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
											 from		PER_PERSONAL
											 where	POEM_ID=$POEM_ID and PER_TYPE=2 and PER_STATUS=1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_NAME = $data2[PER_NAME];
							$PER_SURNAME = $data2[PER_SURNAME];
							$PER_CARDNO = $data2[PER_CARDNO];
							$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = trim($arr_temp[2]);
								$BIRTHDATE_M = trim($arr_temp[1]);
								$BIRTHDATE_Y = substr(($arr_temp[0] + 543), -2);
							} // end if
							$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
							$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
							if($PER_STARTDATE){
								$arr_temp = explode("-", $PER_STARTDATE);
								$STARTDATE_D = trim($arr_temp[2]);
								$STARTDATE_M = trim($arr_temp[1]);
								$STARTDATE_Y = substr(($arr_temp[0] + 543), -2);
							} // end if
							$PER_RETIREDATE = substr(trim($data2[PER_RETIREDATE]), 0, 10);
							if($PER_RETIREDATE){
								$arr_temp = explode("-", $PER_RETIREDATE);
								$RETIREDATE_Y = ($arr_temp[0] + 543);
							}
							$LEVEL_NO = trim($data2[LEVEL_NO]);
							$PER_SALARY = $data2[PER_SALARY];
							$PER_MGTSALARY = $data2[PER_MGTSALARY];
							
							$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis2->send_cmd($cmd);
							$data_level = $db_dpis2->get_array();
							$LEVEL_NAME=$data_level[LEVEL_NAME];
							
							$EDU_TYPE="";		$EL_NAME = "";			$EM_NAME = "";		$EN_SHORTNAME="";		$EN_NAME="";
							//หาข้อมูลการศึกษาเลือก วุฒิสูงสุด ถ้าไม่มีเอาวุฒิในตน.ปัจจุบันมา
							if($PER_ID){
								if($DPISDB=="odbc"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
											 from ( 	
											 			(
															PER_EDUCATE a
															left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
													) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
											 where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%') ";									
								}elseif($DPISDB=="oci8"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													 from 		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
													 where	a.PER_ID=$PER_ID and  (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%')  and a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=d.EM_CODE(+) ";						
								}elseif($DPISDB=="mysql"){
									$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
											 from ( 	
											 			(
																PER_EDUCATE a
																left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
															) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
														) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
											 where	a.PER_ID=$PER_ID and  (a.EDU_TYPE like '%4%' or a.EDU_TYPE like '%2%') ";	
								} // end if
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								
								$EDU_TYPE = trim($data2[EDU_TYPE]);
								$EL_NAME = trim($data2[EL_NAME]);
								$EM_NAME = trim($data2[EM_NAME]);		//สาขา
								$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
								$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
							} // end if
							if(trim($EL_NAME)){
								$ED_NAME = $EL_NAME;
							}
							/* if(trim($EM_NAME)){
								if(trim($ED_NAME)){
									$ED_NAME .= " / ";
								}
								$ED_NAME .= $EM_NAME;
							} */
							if(trim($EN_NAME)){		//$EN_SHORTNAME
								if(trim($ED_NAME)){
									$ED_NAME .= " / ";
								}
								$ED_NAME .= $EN_NAME;		//$EN_SHORTNAME;
							}
							
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POEM_NO;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name2] = $ORG_NAME2;
							$arr_content[$data_count][pg_name] = $PG_NAME;
							$arr_content[$data_count][pn_name] = "$PN_NAME";
							$arr_content[$data_count][level_pnname] = $LEVEL_PNNAME?$LEVEL_PNNAME:"-";
							$arr_content[$data_count][ot_name] = $OT_NAME;
							$arr_content[$data_count][pv_name] = $PV_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_cardno] = $PER_CARDNO;
							$arr_content[$data_count][per_gender] = ($PER_GENDER==1?"ชาย":($PER_GENDER==2?"หญิง":"ว่าง"));
							$arr_content[$data_count][birthdate_d] = $BIRTHDATE_D;
							$arr_content[$data_count][birthdate_m] = $BIRTHDATE_M;
							$arr_content[$data_count][birthdate_y] = $BIRTHDATE_Y;
							$arr_content[$data_count][startdate_d] = $STARTDATE_D;
							$arr_content[$data_count][startdate_m] = $STARTDATE_M;
							$arr_content[$data_count][startdate_y] = $STARTDATE_Y;
							$arr_content[$data_count][retiredate_y] = $RETIREDATE_Y;
							$arr_content[$data_count][level_no] = $LEVEL_NAME;
//							$arr_content[$data_count][per_salary] = ($PER_SALARY?number_format($PER_SALARY):"");
//							$arr_content[$data_count][per_mgtsalary] = ($PER_MGTSALARY?number_format($PER_MGTSALARY):"");
							$arr_content[$data_count][per_salary] = ($PER_SALARY?number_format($PER_SALARY):"");
							$arr_content[$data_count][per_mgtsalary] = ($PER_MGTSALARY?number_format($PER_MGTSALARY):"");
							$arr_content[$data_count][ed_name] = $ED_NAME;

							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POEM_NO = $arr_content[$data_count][pos_no];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME2 = $arr_content[$data_count][org_name2];
			$PG_NAME = $arr_content[$data_count][pg_name];
			$PN_NAME = $arr_content[$data_count][pn_name];
			$LEVEL_PNNAME = $arr_content[$data_count][level_pnname];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$BIRTHDATE_D = $arr_content[$data_count][birthdate_d];
			$BIRTHDATE_M = $arr_content[$data_count][birthdate_m];
			$BIRTHDATE_Y = $arr_content[$data_count][birthdate_y];
			$STARTDATE_D = $arr_content[$data_count][startdate_d];
			$STARTDATE_M = $arr_content[$data_count][startdate_m];
			$STARTDATE_Y = $arr_content[$data_count][startdate_y];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$ED_NAME = $arr_content[$data_count][ed_name];
			
			if($REPORT_ORDER == "ORG_REF"){
				if($data_count > 0) $count_org_ref++;

				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//
				
				//$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
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
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				} // end for

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
					$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if

				print_header();
			//	$xlsRow++;
			}else{	//else if($REPORT_ORDER=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$ORG_NAME2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PER_NAME   $PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_CARDNO):$PER_CARDNO), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PER_GENDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_D):$BIRTHDATE_D), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_M):$BIRTHDATE_M), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_Y):$BIRTHDATE_Y), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_D):$STARTDATE_D), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_M):$STARTDATE_M), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11,(($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_Y):$STARTDATE_Y), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$PG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$LEVEL_PNNAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15,(($NUMBER_DISPLAY==2)?convert2thaidigit($POEM_NO):$POEM_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$OT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, $PV_NAME?$PV_NAME:"-", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18,$ED_NAME?$ED_NAME:"-", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20,(($NUMBER_DISPLAY==2)?convert2thaidigit($RETIREDATE_Y):$RETIREDATE_Y), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} // end if
			
		} // end for
				$xlsRow++;
				$worksheet->write_string($xlsRow, 2, "ผู้ให้ข้อมูล", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				$xlsRow++;
				$worksheet->write_string($xlsRow, 2, "โทร", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
	}else{
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"แบบ $report_code.xls\"");
	header("Content-Disposition: inline; filename=\"แบบ $report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>