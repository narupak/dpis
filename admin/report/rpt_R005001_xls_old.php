<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=h.PL_CODE";
		$line_name = "h.PL_NAME";
		$line_code= "b.PL_CODE";
		$position_no= "b.POS_NO";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
		 $mgt_code ="b.PM_CODE";
		 $select_mgt_code =",b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=h.PN_CODE";
		$line_name = "h.PN_NAME";
		$line_code= "b.PN_CODE";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=h.EP_CODE";
		$line_name = "h.EP_NAME";
		$line_code= "b.EP_CODE";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=h.TP_CODE";
		$line_name = "h.TP_NAME";
		$line_code= "b.TP_CODE";
		$position_no= "b.POT_NO";
	} // end if		
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.DE_YEAR) = '$search_year')";
	if(trim($search_dc_type==1)){ 
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "�������о��";
	}elseif(trim($search_dc_type==2)){
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "��鹵�ӡ�������о��";
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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�ѭ��$PERSON_TYPE[$search_per_type] $DEPARTMENT_NAME �ʹ͢;���Ҫ�ҹ����ͧ�Ҫ��������ó�$search_dc_name||��Шӻ� $search_year";
	$report_code = "R0501";

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
		
		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 8);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "���� - ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�繢���Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "����ͧ�Ҫ��������ó�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "�ѹ/��͹/��", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "�����ش����", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "�ѹ/��͹/��", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "�ͤ��駹��", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
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
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, a.PER_SALARY, 
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,
											e.DC_CODE, g.DC_SHORTNAME $select_type_code  $select_mgt_code
						 from			(
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
											) left join $line_table h on ($line_join)
										) left join PER_LEVEL k on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition
						 order by		a.LEVEL_NO, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, a.PER_SALARY, 
											SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE,
											e.DC_CODE, g.DC_SHORTNAME $select_type_code  $select_mgt_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
											PER_DECORDTL e, PER_DECOR f, PER_DECORATION g, $line_table h, PER_LEVEL k
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE and $line_join(+) and a.LEVEL_NO=k.LEVEL_NO
											$search_condition
						 order by		a.LEVEL_NO, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, k.POSITION_LEVEL, a.PER_SALARY, 
											LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,
											e.DC_CODE, g.DC_SHORTNAME $select_type_code  $select_mgt_code
						 from			(
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
											) left join $line_table h on ($line_join)
										) left join PER_LEVEL on (a.LEVEL_NO=k.LEVEL_NO)
											$search_condition
						 order by		a.LEVEL_NO, LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) ";
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
		if(trim($mgt_code)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE=".$PM_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		if(trim($type_code)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
		$PER_SALARY = $data[PER_SALARY];
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], $DATE_DISPLAY);
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_RETIREDATE = "";
		$PER_RETIREYEAR = "";
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);	

			$PER_RETIREDATE = ($arr_temp[0] + 60) ."-10-01";
			if($PER_BIRTHDATE >= ($arr_temp[0] ."-10-01")) $PER_RETIREDATE = ($arr_temp[0] + 60 + 1) ."-10-01";

			//$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		} // end if
		if($PER_RETIREDATE){
			$arr_temp = explode("-", $PER_RETIREDATE);
			//$PER_RETIREDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			$PER_RETIREDATE = show_date_format($PER_RETIREDATE,$DATE_DISPLAY);
			$PER_RETIREYEAR = $arr_temp[0] + 543;
		} // end if
		$DC_CODE = trim($data[DC_CODE]);
		$DC_NAME = trim($data[DC_SHORTNAME]);
				
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, SUBSTR(trim(a.DEH_DATE), 1, 10) as DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.DC_CODE=b.DC_CODE(+) and a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID and b.DC_ORDER < 99
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$LAST_DC_NAME = trim($data2[DC_SHORTNAME]);
		$LAST_DEH_DATE = show_date_format($data2[DEH_DATE], $DATE_DISPLAY);

		if($DPISDB=="odbc"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE2
							 from 		PER_POSITIONHIS 
							 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='". str_pad(($LEVEL_NO - 1), 2, "0", STR_PAD_LEFT) ."' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE2 = show_date_format($data2[POH_EFFECTIVEDATE2], $DATE_DISPLAY);
		
		$REMARK = "";
		if($PER_RETIREYEAR == $search_year) $REMARK = "���³���ػ� $PER_RETIREYEAR";
		elseif($POH_EFFECTIVEDATE2) $REMARK = "�������дѺ ". ($LEVEL_NO - 1) ." ����� $POH_EFFECTIVEDATE2";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row.". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
		$arr_content[$data_count][startdate] = "$PER_STARTDATE";
		$arr_content[$data_count][salary] = "$PER_SALARY";
		$arr_content[$data_count][last_dc_name] = "$LAST_DC_NAME";
		$arr_content[$data_count][last_deh_date] = "$LAST_DEH_DATE";
		$arr_content[$data_count][dc_name] = "$DC_NAME";
		$arr_content[$data_count][remark] = "$REMARK";
		if($PM_CODE){
			$arr_content[$data_count][position] = $PM_NAME;
			
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
		}else{
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "�����" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		} // end if
		
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
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$NAME = $arr_content[$data_count][name];
			$LEVEL_NO = $arr_content[$data_count][level];
			$STARTDATE = $arr_content[$data_count][startdate];
			$SALARY = $arr_content[$data_count][salary];
			$POSITION = $arr_content[$data_count][position];
			$LAST_DC_NAME = $arr_content[$data_count][last_dc_name];
			$LAST_DEH_DATE = $arr_content[$data_count][last_deh_date];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$REMARK = $arr_content[$data_count][remark];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, level_no_format($LEVEL_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, ($SALARY?number_format($SALARY):""), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$LAST_DC_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$LAST_DEH_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$DC_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
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