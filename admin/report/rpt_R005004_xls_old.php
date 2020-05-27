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
		$line_join = "a.PL_CODE=b.PL_CODE";
		$pl_code = "a.PL_CODE";
		$pl_name = "b.PL_NAME";
		$ipl_name = "i.PL_NAME";
		$type_code ="a.PT_CODE";
		$select_type_code =",a.PT_CODE";
		$mgt_code ="a.PM_CODE";
		$select_mgt_code =",a.PM_CODE";
		$type_code_b ="b.PT_CODE";
		$select_type_code_b =",b.PT_CODE";
		$mgt_code_b ="b.PM_CODE";
		$select_mgt_code_b =",b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "a.PN_CODE=b.PN_CODE";
		$pl_code = "a.PN_CODE";
		$pl_name = "b.PN_NAME";
		$ipl_name = "i.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "a.EP_CODE=b.EP_CODE";
		$pl_code = "a.EP_CODE";
		$pl_name = "b.EP_NAME";
		$ipl_name = "i.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "a.TP_CODE=b.TP_CODE";
		$pl_code = "a.TP_CODE";
		$pl_name = "b.TP_NAME";
		$ipl_name = "i.TP_NAME";
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
//	$arr_rpt_order = array("ORG"); 
	$search_per_type = 1;

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.DE_YEAR) = '$search_year')";
	if(trim($search_dc_type==1)){ 
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นสายสะพาย";
	}elseif(trim($search_dc_type==2)){
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นต่ำกว่าสายสะพาย";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

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
	if(trim($search_org_id)){
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1) {
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
	}
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "แบบ ขร 4/" . substr($search_year, -2);
	$report_title = "บัญชีแสดงคุณสมบัติของข้าราชการ ซึ่งขอพระราชทานเครื่องราชอิสริยาภรณ์ $search_dc_name ปี พ.ศ.$search_year||$MINISTRY_NAME||$DEPARTMENT_NAME" . ($search_org_id?"||$search_org_name":"");
	$report_code = "R0504";

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
		global $heading_name;
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 8);
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 10);
		$worksheet->set_column(9, 9, 25);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "(1)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "(2)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "(3)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "(4)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "(5)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 9, "(6)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "เป็นข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "เครื่องราชอิสริยาภรณ์", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 9, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อตัว - ชื่อสกุล", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "ชั้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ตั้งแต่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ปัจจุบัน และอดีตเฉพาะปีที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ขอครั้งนี้", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "(ปัจจุบัน)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "(ปัจจุบัน)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "พระราชทานเครื่องราชอิสริยาภรณ์", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "(จากชั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "(5 ธ.ค. ...)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ไปชั้นรอง)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function list_decoratehis($PER_ID, $LEVEL_NO){
		global $DPISDB, $db_dpis2, $db_dpis3;
		global $search_per_type, $arr_content, $data_count, $DATE_DISPLAY;
		global $pl_code,$pl_name,$line_table,$line_join;
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, SUBSTR(trim(a.DEH_DATE), 1, 10) as DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){
			$GET_DC_NAME = trim($data2[DC_SHORTNAME]);
			$GET_DEH_DATE = trim($data2[DEH_DATE]);
			if($GET_DEH_DATE){
				$SHOW_DEH_DATE = show_date_format($GET_DEH_DATE,$DATE_DISPLAY);
			} // end if

			if($DPISDB=="odbc"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from			PER_POSITIONHIS a, $line_table b, PER_LEVEL e
								 where		$line_join(+)
													and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$GET_DEH_DATE' and a.LEVEL_NO=e.LEVEL_NO(+)
								 order by		SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) desc ";
			}elseif($DPISDB=="mysql"){
			$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$PL_CODE = trim($data3[PL_CODE]);
			$PL_NAME = trim($data3[PL_NAME]);
			$LEVEL_NO = trim($data3[LEVEL_NO]);
			$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
			$PM_CODE = trim($data3[PM_CODE]);	$PT_CODE = trim($data3[PT_CODE]);
		
			if(trim($PM_CODE)){
				$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PM_NAME = trim($data3[PM_NAME]);
			}
			if(trim($PT_CODE)){
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PT_NAME = trim($data3[PT_NAME]);
			}

			if($DPISDB=="odbc"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select		MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			} // end if
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$POH_EFFECTIVEDATE =  show_date_format($data3[POH_EFFECTIVEDATE],$DATE_DISPLAY);
			$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

			$data_count++;			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = $GET_DC_NAME;
			$arr_content[$data_count][last_deh_date] = $SHOW_DEH_DATE;
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = $REMARK;

			if($PM_CODE){
				$arr_content[$data_count][position] = $PM_NAME;
				
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = "";
				$arr_content[$data_count][name] = "";
				$arr_content[$data_count][level] = "";
				$arr_content[$data_count][effectivedate] = "";
				$arr_content[$data_count][salary] = "";
				$arr_content[$data_count][last_dc_name] = "";
				$arr_content[$data_count][last_deh_date] = "";
				$arr_content[$data_count][dc_name] = "";
				$arr_content[$data_count][remark] = "";
				$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
			}else{
				$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			} // end if
		} // end while		
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $ipl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
															(
																(
																	(
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
														) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
													) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $ipl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $ipl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
											PER_DECORDTL e, PER_DECOR f, PER_DECORATION g, PER_POSITIONHIS h, $line_table i, PER_LEVEL m
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
											and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=m.LEVEL_NO(+)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $ipl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $ipl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
															(
																(
																	(
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join)
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
															) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
														) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
													) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $ipl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);
		$LEVEL_NO = $data[LEVEL_NO];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PER_SALARY = $data[PER_SALARY];
		$DC_CODE = trim($data[DC_CODE]);
		$DC_NAME = trim($data[DC_SHORTNAME]);
		if(trim($type_code_b)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		if(trim($mgt_code_b)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);
		$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
		$arr_content[$data_count][effectivedate] = "$POH_EFFECTIVEDATE";
		$arr_content[$data_count][salary] = "$PER_SALARY";
		$arr_content[$data_count][last_dc_name] = "-";
		$arr_content[$data_count][last_deh_date] = "";
		$arr_content[$data_count][dc_name] = "$DC_NAME";
		$arr_content[$data_count][remark] = "$REMARK";
		if($PM_CODE){
			$arr_content[$data_count][position] = $PM_NAME;
			
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = "";
			$arr_content[$data_count][last_deh_date] = "";
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = "";
			$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
		}else{
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		} // end if
		
		list_decoratehis($PER_ID, $LEVEL_NO);
		
		$data_count++;
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
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$LEVEL_NO = $arr_content[$data_count][level];
			$EFFECTIVEDATE = $arr_content[$data_count][effectivedate];
			$SALARY = $arr_content[$data_count][salary];
			$LAST_DC_NAME = $arr_content[$data_count][last_dc_name];
			$LAST_DEH_DATE = $arr_content[$data_count][last_deh_date];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$REMARK = $arr_content[$data_count][remark];
			$POSITION = $arr_content[$data_count][position];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, level_no_format($LEVEL_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, ($SALARY?number_format($SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$LAST_DC_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$LAST_DEH_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$DC_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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