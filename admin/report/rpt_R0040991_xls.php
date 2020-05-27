<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type==1 || $search_per_type == 5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "g.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "g.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "g.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "g.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	

	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS=1)";

   	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[ORG_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[ORG_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";

	} // end if

	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
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
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
	
	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		$arr_search_condition[] = "(trim(d.TR_CODE)=trim('$search_tr_code'))";
	}
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	if(in_array("ALL", $list_type)){//ค้นหารายปีงบประมาณ
		if($DPISDB=="odbc") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="oci8") $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (SUBSTR(trim(d.TRN_ENDDATE), 1, 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="mysql") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01' or d.TRN_ENDDATE is null)";
	}
	if(in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ
		if($DPISDB=="odbc") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="oci8") $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (SUBSTR(trim(d.TRN_ENDDATE), 1, 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
		
		elseif($DPISDB=="mysql") $arr_search_condition[] = "LEFT(trim(d.TRN_STARTDATE), 10) >= '".(($search_budget_year2 - 543) - 1)."-10-01' and (LEFT(trim(d.TRN_ENDDATE), 10) < '".($search_budget_year2 - 543)."-10-01' or d.TRN_ENDDATE is null)";
	}

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	$DateOfTraining="";
	if ($date_of_training != "") $DateOfTraining=" เกณฑ์วันอบรม " . $date_of_training . " วัน";
	$company_name = "";
	if(in_array("TRAIN", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการเข้าร่วมการพัฒนารายหลักสูตร";
	if(in_array("SELECT", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานประวัติการพัฒนารายบุคคล";
	if(in_array("ALL", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการพัฒนาข้าราชการ ประจำปีงบประมาณ $search_budget_year";
	if(in_array("ALL2", $list_type)) $report_title = "$DEPARTMENT_NAME||รายงานการพัฒนาข้าราชการสรุปจำนวนวันอบรม ประจำปีงบประมาณ $search_budget_year2" . $DateOfTraining;
	$report_code = "R040991";
	if (in_array("ALL", $list_type)) $orientation='L';
	else $orientation='P';

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header1(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 17);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 15);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ค่าใช้จ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "วันเข้ารับการอบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "วันสิ้นสุดการอบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function	

	function print_header2(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 20);
		$worksheet->set_column(1, 1, 100);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "วันที่อบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "เรื่องที่อบรม/ รุ่นที่/ สถานที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function

	function print_header3(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 8);
		$worksheet->set_column(7, 7, 30);
		$worksheet->set_column(8,8, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "เรื่อง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "วันที่อบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ระยะเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "สถานที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "ค่าใช้จ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function

	function print_header4(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ระยะเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ผ่านเกณฑ์", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function
	
	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
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
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
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
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		}else{	// 2 || 3 || 4
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		} //end if
	}//จบการค้นหารายหลักสูตร
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(	
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_POS_NAME g on (b.PN_CODE=g.PN_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME f, PER_POS_NAME g, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.PN_CODE=g.PN_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(	
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_POS_NAME g on (b.PN_CODE=g.PN_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PN_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		} elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_EMPSER_POS_NAME g on (b.EP_CODE=g.EP_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10)  , LEFT(trim(a.PER_STARTDATE), 1, 10)  
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME f, PER_EMPSER_POS_NAME g, PER_LEVEL i,PER_POSITIONHIS j
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PN_CODE=f.PN_CODE(+) and b.EP_CODE=g.EP_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME , SUBSTR(trim(a.PER_STARTDATE), 1, 10)  , SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) 
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";

			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME as PL_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join PER_EMPSER_POS_NAME g on (b.EP_CODE=g.EP_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
													$search_condition
									 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.EP_NAME, a.LEVEL_NO, a.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10)  , LEFT(trim(a.PER_STARTDATE), 1, 10)  
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID ";
			}
		} // end if
	}//จบการค้นหารายบุคคล
	if(in_array("ALL", $list_type) || in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
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
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
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
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		}else{	// 2 || 3 || 4
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC,  a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
								 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
													and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
													and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE
								 from			(
														(
															(
																(
																	(	
																		(
																		PER_PERSONAL a 
																		left join $position_table b on ($position_join) 
																	) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
															) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
														) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
													) left join $line_table g on ($line_join)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
													$search_condition
								 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
			}
		} //end if
	}//จบการค้นหารายปีงบประมาณ

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$TRN_PRICE_SUM = 0;
	//echo $cmd . "<br>";
	if (in_array("TRAIN", $list_type)){ //ค้นหารายหลักสูตร
		while($data = $db_dpis->get_array()){
			$data_row++;
			if ($data_row==1){
				$TRN_ORG = $data[TRN_ORG];
				if ($data[TRN_TYPE]=="1") $TRN_TYPE1 = "ฝึกอบรม";
				elseif ($data[TRN_TYPE]=="2") $TRN_TYPE1 = "ดูงาน";
				else $TRN_TYPE1 = "สัมมนา";
				$TRN_TYPE2 = "";
				$TR_NAME = $data[TR_NAME];
				$TRN_DAY = $data[TRN_DAY] . " วัน";
				$TRN_PLACE = $data[TRN_PLACE];
			}
	
			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$PL_NAME = $data[PL_NAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$POSITION_LEVEL = $data[POSITION_LEVEL];
			$PT_CODE = trim($data[PT_CODE]);
			$ORG_SHORT = "";//$data[ORG_SHORT];
			$ORG_NAME = $data[ORG_NAME];
			$TRN_NO = $data[TRN_NO];
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
	
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
			if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content[$data_count][trn_enddate] = $TRN_ENDDATE;
			else $arr_content[$data_count][trn_enddate] = "-";
			$arr_content[$data_count][trn_day] = $TRN_DAY . " วัน";
			$arr_content[$data_count][trn_place] = $TRN_PLACE;
			$arr_content[$data_count][trn_price] = number_format(0,2,'.',' ');
			$TRN_PRICE_SUM+=$arr_content[$data_count][trn_price];

			$data_count++;
		} // end while
		
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($count_data){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "หน่วยงานผู้จัด : ", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $TRN_ORG, set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ประเภทการสมัคร : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, "-",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "",  set_format("xlsFmtTitle", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ลักษณะการพัฒนา : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $TRN_TYPE1,  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "ประเภทหลักสูตร : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, $TRN_TYPE2,  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "",  set_format("xlsFmtTitle", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ชื่อหลักสูตร : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $TR_NAME,  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "",  set_format("xlsFmtTitle", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "สถานที่ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $TRN_PLACE,  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "",  set_format("xlsFmtTitle", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "รวมระยะเวลา : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $TRN_DAY."  วัน", set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "",  set_format("xlsFmtTitle", "", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "หมายเหตุ : ",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 1, "",  set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "", "L", "", 0));

			print_header1();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][trn_price];
				$NAME_3 = $arr_content[$data_count][trn_startdate];
				$NAME_4 = $arr_content[$data_count][trn_enddate];

				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER ", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME_2", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$NAME_3", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$NAME_4", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));

			} // end for
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 1, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 2, (number_format($TRN_PRICE_SUM,2,'.',' ')), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
		}else{
			$xlsRow = 0;
			$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	}
	
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		$data_count2 = 0;
		while($data = $db_dpis->get_array()){
			$data_row++;
			$arr_content[$data_count][PER_ID] = $data[PER_ID];
			$arr_content[$data_count][PN_NAME] = $data[PN_NAME];
			$arr_content[$data_count][PER_NAME] = $data[PER_NAME];
			$arr_content[$data_count][PER_SURNAME] = $data[PER_SURNAME];
			$arr_content[$data_count][PL_NAME] = $data[PL_NAME];
			$arr_content[$data_count][LEVEL_NO] = $data[LEVEL_NO];
			$arr_content[$data_count][POSITION_LEVEL] = $data[POSITION_LEVEL];
			$arr_content[$data_count][PT_CODE] = trim($data[PT_CODE]);
			$arr_content[$data_count][ORG_NAME] = $data[ORG_NAME];

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$arr_content[$data_count][PER_BIRTHDATE] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$arr_content[$data_count][PER_STARTDATE] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$arr_content[$data_count][POH_EFFECTIVEDATE] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);

			$arr_content[$data_count][AGE] = date_difference(date("Y-m-d"), $data[PER_BIRTHDATE], "ymd");
			$arr_content[$data_count][WORK_DURATION] = date_difference(date("Y-m-d"), $data[PER_STARTDATE], "ymd");

			if($search_per_type==1){
				if($DPISDB=="odbc"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(
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
																		) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																	) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
																) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
															) left join $line_table g on ($line_join)
														) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}elseif($DPISDB=="oci8"){
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
									 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
														and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
														and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10),d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(
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
																		) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																	) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
																) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
															) left join $line_table g on ($line_join)
														) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
												) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID, LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}
			}else{	// 2 || 3 || 4
				if($DPISDB=="odbc"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from		(	
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}elseif($DPISDB=="oci8"){
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE,d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i,PER_POSITIONHIS j
									 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
														and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
														and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10),d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,SUBSTR(trim(d.TRN_STARTDATE), 1, 10), SUBSTR(trim(d.TRN_ENDDATE), 1, 10) ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name as PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
									from		(	
														(
															(
																(
																	(
																		(	
																			(
																			PER_PERSONAL a 
																			left join $position_table b on ($position_join) 
																		) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																	) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
																) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join $line_table g on ($line_join)
													) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
														$search_condition and  (a.PER_ID = $data[PER_ID])
										 group by a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_name, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 1, 10), LEFT(trim(d.TRN_ENDDATE), 1, 10), d.TRN_DAY,e.TR_NAME,d.TRN_PLACE,d.TRN_ORG, d.TRN_TYPE, LEFT(trim(a.PER_BIRTHDATE), 1, 10) , LEFT(trim(a.PER_STARTDATE), 1, 10)
									 order by		i.LEVEL_SEQ_NO DESC, a.PER_ID,LEFT(trim(d.TRN_STARTDATE), 10), LEFT(trim(d.TRN_ENDDATE), 10) ";
				}
			} //end if
			$count_data2 = $db_dpis2->send_cmd($cmd);
			//echo $cmd . "<br><br>";
			while($data2 = $db_dpis2->get_array()){
				$arr_content2[$data_count2][PER_ID] = $data[PER_ID];

				$TRN_ORG = $data2[TRN_ORG];
				if ($data2[TRN_TYPE]=="1") $TRN_TYPE = "ฝึกอบรม";
				elseif ($data2[TRN_TYPE]=="2") $TRN_TYPE = "ดูงาน";
				else $TRN_TYPE = "สัมมนา";
				
				$TR_NAME = $data2[TR_NAME];
				$TRN_PLACE = $data2[TRN_PLACE];
				$TRN_DAY = $data2[TRN_DAY];
				
				$TRN_NO = $data2[TRN_NO];
				$TRN_STARTDATE = trim($data2[TRN_STARTDATE]);
				$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

				$TRN_ENDDATE = trim($data2[TRN_ENDDATE]);
				$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
				
				$arr_content2[$data_count2][trn_traindate] = $TRN_STARTDATE;
				if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content2[$data_count2][trn_traindate] .= " - " . $TRN_ENDDATE;
				$arr_content2[$data_count2][type] = "CONTENT";
				$arr_content2[$data_count2][trn_training] = $TRN_TYPE . $TR_NAME;
				if ($TRN_NO != "") $arr_content2[$data_count2][trn_training] .= " รุ่นที่ " . $TRN_NO;
				if ($TRN_ORG != "") $arr_content2[$data_count2][trn_training] .= " / จัดโดย" . $TRN_ORG;
				if ($TRN_PLACE != "") $arr_content2[$data_count2][trn_training] .= " / ณ " . $TRN_PLACE;
				if ($TRN_DAY != "") $arr_content2[$data_count2][trn_training] .= " / จำนวน " . $TRN_DAY . " วัน";
				$data_count2++;
			}
			//echo count($arr_content2)."<br><br>";
			$data_count++;
		} // end while
		
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
		
		if($count_data){
			$data2_check = 0;
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				if ($data_count==0){
					$xlsRow++;
					$PER_ID_PREV=$arr_content[$data_count][PER_ID];
					$worksheet->write($xlsRow, 0, "ชื่อ : ", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME], set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "ตำแหน่ง : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "สังกัด : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][ORG_NAME],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "วัน เดือน ปีเกิด : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PER_BIRTHDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "อายุ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][AGE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "เริ่มรับราชการวันที่ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PER_STARTDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "อายุราชการ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][WORK_DURATION],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "วันดำรงตำแหน่ง : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][POH_EFFECTIVEDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "ประวัติการอบรม / สัมมนา / ประชุม / ดูงาน : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, "",  set_format("xlsFmtTitle", "", "L", "", 0));

					print_header2();
				}else{
					$xlsRow+=3;
					$worksheet->write($xlsRow, 0, "ชื่อ : ", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME], set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "ตำแหน่ง : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "สังกัด : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][ORG_NAME],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "วัน เดือน ปีเกิด : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PER_BIRTHDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "อายุ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][AGE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "เริ่มรับราชการวันที่ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][PER_STARTDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "อายุราชการ : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][WORK_DURATION],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "วันดำรงตำแหน่ง : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $arr_content[$data_count][POH_EFFECTIVEDATE],  set_format("xlsFmtTitle", "", "L", "", 0));

					$xlsRow++;
					$worksheet->write($xlsRow, 0, "ประวัติการอบรม / สัมมนา / ประชุม / ดูงาน : ",  set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, "",  set_format("xlsFmtTitle", "", "L", "", 0));

					print_header2();
				}

				$REPORT_ORDER = $arr_content[$data_count][type];
				$data2_check_PER_ID=0;
				//echo "count(arr_content2) = " . count($arr_content2) . "<br>";
				//echo "data2_check = " . $data2_check . "<br>";
				if ((count($arr_content2)>0) && (count($arr_content2)>= $data2_check)) {
					while ($arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]){
						//echo "in while <br><br><br><br>";
						$data2_check_PER_ID++;

						$NAME_1 = $arr_content2[$data2_check][trn_traindate];
						$NAME_2 = $arr_content2[$data2_check][trn_training];

						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 1, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						
						$data2_check++;
					}// end while $arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]
				}
				if ($data2_check_PER_ID==0){
					//echo "not while <br><br><br><br>";
					$NAME_1 = "-";
					$NAME_2 = "-";
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 1, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				}
			} // end for
		}else{
			$xlsRow = 0;
			$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	}
	
	if(in_array("ALL", $list_type)){//ค้นหารายปีงบประมาณ
		$TRN_PRICE_SUM=0;
		while($data = $db_dpis->get_array()){
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$PL_NAME = $data[PL_NAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$POSITION_LEVEL = $data[POSITION_LEVEL];
			$PT_CODE = trim($data[PT_CODE]);
			$ORG_SHORT = $data[ORG_SHORT];
			$ORG_NAME = $data[ORG_NAME];
			$TRN_NO = $data[TRN_NO];
			$TRN_STARTDATE = trim($data[TRN_STARTDATE]);
			$TRN_STARTDATE = show_date_format($TRN_STARTDATE,$DATE_DISPLAY);

			$TRN_ENDDATE = trim($data[TRN_ENDDATE]);
			$TRN_ENDDATE = show_date_format($TRN_ENDDATE,$DATE_DISPLAY);
			$TRN_DAY = $data[TRN_DAY];
			$TR_NAME = $data[TR_NAME];
			$TRN_PLACE = $data[TRN_PLACE];

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][tr_name] = $TR_NAME;
			$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
			if (($TRN_ENDDATE != "0  543") && ($TRN_STARTDATE != $TRN_ENDDATE) && ($TRN_ENDDATE != "")) $arr_content[$data_count][trn_startdate] .= " - " . $TRN_ENDDATE;
			$arr_content[$data_count][trn_day] = $TRN_DAY . " วัน";
			$arr_content[$data_count][trn_place] = $TRN_PLACE;
			$arr_content[$data_count][trn_price] = number_format(0,2,'.',' ');
			$TRN_PRICE_SUM+=$arr_content[$data_count][trn_price];

			$data_count++;
		} // end while
	
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
		
			print_header3();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][position];
				$NAME_3 = $arr_content[$data_count][org_name];
				$NAME_4 = $arr_content[$data_count][tr_name];
				$NAME_5 = $arr_content[$data_count][trn_startdate];
				$NAME_6 = $arr_content[$data_count][trn_day];
				$NAME_7 = $arr_content[$data_count][trn_place];
				$NAME_8 = $arr_content[$data_count][trn_price];
				
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER ", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$NAME_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$NAME_4", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$NAME_5", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$NAME_6", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$NAME_7 ", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$NAME_8", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end for
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 6, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 7, number_format($data_row) ."  รายการ  ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, number_format($TRN_PRICE_SUM,2,'.',' '), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
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
	}
	if(in_array("ALL2", $list_type)){//ค้นหารายปีงบประมาณ สรุปจำนวนวันอบรม
		$TRN_PRICE_SUM=0;
		while($data = $db_dpis->get_array()){
			if ($PER_ID_PREV != $data[PER_ID]){
				if ($PER_ID_PREV!=""){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][order] = "$data_row.";
					$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
					$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
					$arr_content[$data_count][trn_day] = $TRN_DAY;
					$data_count++;
				}
				$data_row++;
				
				$PER_ID_PREV = $data[PER_ID];
				$PN_NAME = $data[PN_NAME];
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];
				$PL_NAME = $data[PL_NAME];
				$LEVEL_NO = $data[LEVEL_NO];
				$POSITION_LEVEL = $data[POSITION_LEVEL];
				$ORG_SHORT = $data[ORG_SHORT];
				$ORG_NAME = $data[ORG_NAME];
				$PT_CODE = trim($data[PT_CODE]);
				$TRN_DAY = $data[TRN_DAY];
			}else{
				$TRN_DAY += $data[TRN_DAY];
			}
		} // end while
		if ($PER_ID_PREV!=""){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "$data_row.";
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL;
			$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
			$arr_content[$data_count][trn_day] = $TRN_DAY;
			$data_count++;
		}
	
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
			} // end if
		
			print_header4();
			
			for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][position];
				$NAME_3 = $arr_content[$data_count][org_name];
				$NAME_4 = $arr_content[$data_count][trn_day] . " วัน";
				if ($arr_content[$data_count][trn_day]>= $date_of_training) $NAME_5 = "ผ่าน";
				else $NAME_5 = "-";
				
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$ORDER ", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$NAME_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$NAME_4", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$NAME_5", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			} // end for
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
			$worksheet->write_string($xlsRow, 4, "รวม", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 1));
			$worksheet->write_string($xlsRow, 5, number_format($data_row) ."  รายการ  ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		}else{
			$xlsRow = 0;
			$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	}//จบรายงานรายปีงบประมาณ สรุปจำนวนวันอบรม

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