<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

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

	if($search_year_min && $search_year_max) $show_year = "||ตั้งแต่ปี $search_year_min - $search_year_max";
	elseif($search_year_min) $show_year = "||ตั้งแต่ปี $search_year_min";
	elseif($search_year_max) $show_year = "||ถึงปี $search_year_max";

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type]ที่ลาไปศึกษา/ฝึกอบรม ณ ต่างประเทศ จำแนกตาม$ORG_TITLE". $show_year;
	$report_code = "R0415";

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
		
		$worksheet->set_column(0, 0, 45);
		$worksheet->set_column(1, 11, 6);
		$worksheet->set_column(12, 12, 10);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, " ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "จำนวน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 1));
		$worksheet->write($xlsRow, 12, "รวม", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 13, "ขรก.", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 14, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ทั้งหมด", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $search_year_min, $search_year_max, $select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		if($level_no){
			$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);
			if($DPISDB=="odbc"){ 
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) <> '140')";
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="oci8"){
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) != '140')";
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(SUBSTR(trim(d.SC_STARTDATE), 1, 4) <= '".($search_year_max - 543)."')";
			}elseif($DPISDB=="mysql"){
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.CT_CODE) <> '140')";
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(a.LEVEL_NO) = '$level_no')";
				if($search_year_min) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) >= '".($search_year_min - 543)."')";
				if($search_year_max) $search_condition .= (trim($search_condition)?" and ":" where ") . "(LEFT(trim(d.SC_STARTDATE), 4) <= '".($search_year_max - 543)."')";
			} // end if
		} // end if

		// นับการลาศึกษาจาก PER_SCHOLAR
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
								$search_condition
								group by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
								$search_condition
								group by		a.PER_ID ";
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
								$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
								$search_condition
								 group by 	a.PER_ID ";
			} // end if
		} elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=d.PER_ID(+) and d.INS_CODE=e.INS_CODE(+)
													$search_condition
								 group by 	a.PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
													$search_condition
								 group by 	a.PER_ID ";
			} // end if
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if
		
		if($level_no){
			// นับการลาฝึกอบรมจาก PER_TRAIN
			$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
			$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
			$search_condition .= (trim($search_condition)?" and ":" where ") . "(e.TR_TYPE in (1))";
			
			if($search_per_type==1){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
									$search_condition
									group by		a.PER_ID ";
				}elseif($DPISDB=="oci8"){				
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_TRAINING d, PER_TRAIN e
									 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and a.PER_ID=d.PER_ID(+) and d.TR_CODE=e.TR_CODE(+)
														$search_condition
									 group by 	a.PER_ID ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
									$search_condition
									group by		a.PER_ID ";
				} // end if
			}elseif($search_per_type==2){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
									$search_condition
									 group by 	a.PER_ID ";
				}elseif($DPISDB=="oci8"){	
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_TRAINING d, PER_TRAIN e
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and a.PER_ID=d.PER_ID(+) and d.TR_CODE=e.TR_CODE(+)
														$search_condition
									 group by 	a.PER_ID ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			distinct $select_list
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID)
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														$search_condition
									 order by		$order_by ";
				}
			} elseif($search_per_type==3){
				if($DPISDB=="odbc"){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
									$search_condition
									 group by 	a.PER_ID ";
				}elseif($DPISDB=="oci8"){	
					$search_condition = str_replace(" where ", " and ", $search_condition);
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_TRAINING d, PER_TRAIN e
									 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and a.PER_ID=d.PER_ID(+) and d.TR_CODE=e.TR_CODE(+)
														$search_condition
									 group by 	a.PER_ID ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
									$search_condition
									 group by 	a.PER_ID ";
				} // end if
			} // end if
			if($select_org_structure==1) { 
				$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
				$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			}
			$count_person_2 = $db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			if($count_person_2==1){
				$data = $db_dpis2->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if($data[count_person] == 0) $count_person += 0;
			}else{
				$count_person += $count_person_2;
			} // end if
		} // end if

		return $count_person;
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
	
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	} elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													PER_PERSONAL a 
													left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												$search_condition
							 order by		$order_by ";
		}
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($GRAND_LEVEL);
	$GRAND_ALL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
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
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=1; $i<=11; $i++){
							$arr_content[$data_count]["count_".$i] = count_person($i, $search_condition, $addition_condition);
							$GRAND_LEVEL[$i] += $arr_content[$data_count]["count_".$i];
						} // end for
						$arr_content[$data_count][count_all] = count_person(0, $search_condition, $addition_condition);
						$GRAND_ALL += $arr_content[$data_count][count_all];

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=1; $i<=11; $i++){ 
				${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
				$COUNT_TOTAL += ${"COUNT_".$i};
			} // end for
			$COUNT_ALL = $arr_content[$data_count][count_all];
			$PERCENT_TOTAL = "";
			if($COUNT_ALL) $PERCENT_TOTAL = ($COUNT_TOTAL / $COUNT_ALL) * 100;
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, (${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"COUNT_".$i})):number_format(${"COUNT_".$i})):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 12, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 13, ($COUNT_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_ALL)):number_format($COUNT_ALL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 14, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
		} // end for				

		$GRAND_TOTAL = array_sum($GRAND_LEVEL);
		$PERCENT_TOTAL = "";
		if(GRAND_ALL) $PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_ALL) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, ($GRAND_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_LEVEL[$i])):number_format($GRAND_LEVEL[$i])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 12, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, ($GRAND_ALL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_ALL)):number_format($GRAND_ALL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 14, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
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
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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