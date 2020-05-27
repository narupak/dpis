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
		$line_join = "e.PL_CODE=f.PL_CODE";
		$pl_code = "e.PL_CODE";
		$line_code = "e.PL_CODE";
		$pl_name = "f.PL_NAME";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "e.PN_CODE=f.PN_CODE";
		$pl_code = "e.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "e.EP_CODE=f.EP_CODE";
		$pl_code = "e.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "e.TP_CODE=f.TP_CODE";
		$pl_code = "e.TP_CODE";
		$pl_name = "f.TP_NAME";
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "LINE";
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("LINE"); 

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
//	$arr_search_condition[] = "(e.MOV_CODE in ('10320', '10340', '10350', '10430', '10450'))";
	if ($BKK_FLAG==1)
		$arr_search_condition[] = "(e.MOV_CODE in ('3', '34'))";
	else
		$arr_search_condition[] = "(e.MOV_CODE in ('10320', '10340', '10350'))";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่ย้ายระหว่างสายงาน ในปีงบประมาณ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0304";

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
		
		$worksheet->set_column(0, 0, 40);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 5);
		$worksheet->set_column(3, 3, 5);
		$worksheet->set_column(4, 4, 5);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "สายงานเดิม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "สายงานใหม่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "จำนวน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ชาย", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "หญิง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from			(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on $line_join
											$search_condition
						 order by		$pl_code ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, $line_table f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=e.PER_ID(+) and $line_join(+)
											$search_condition
						 order by		$pl_code ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from			(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on $line_join
											$search_condition
						 order by		$pl_code ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = 0;
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$POH_EFFECTIVEDATE = substr($data[POH_EFFECTIVEDATE], 0, 10);
		$NEW_PL_CODE = trim($data[PL_CODE]);
		$NEW_PL_NAME = $data[PL_NAME];
		
		if($DPISDB=="odbc")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e
											 left join $line_table f on $line_join
							 where 	 e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="oci8")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e, $line_table f
							 where 	 $line_join(+) and e.PER_ID=$PER_ID and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="mysql")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e
											 left join $line_table f on $line_join
							 where 	 e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_PL_CODE = trim($data2[PL_CODE]);
		$OLD_PL_NAME = $data2[PL_NAME];
		
		$POH_MOV_CODE = trim($data[MOV_CODE]);
		if ($BKK_FLAG==1) { 
			if((($OLD_PL_NAME && $OLD_PL_NAME != $NEW_PL_NAME && in_array($POH_MOV_CODE, array('3', '34'))) || in_array($POH_MOV_CODE, array('34'))) && (($list_type=="PER_LINE" && $search_per_type==1 && $search_pl_code==$OLD_PL_CODE) || ($list_type=="PER_LINE" && $search_per_type==2 && $search_pn_code==$OLD_PL_CODE) || ($list_type=="PER_LINE" && $search_per_type==3 && $search_ep_code==$OLD_PL_CODE) || $list_type!="PER_LINE") || ($list_type=="PER_LINE" && $search_per_type==4 && $search_tp_code==$OLD_PL_CODE)){
				$key = "$OLD_PL_NAME:$NEW_PL_NAME";
				if(!array_key_exists($key, $arr_content)){ 
					$arr_content[$key][old_pl] = $OLD_PL_NAME;
					$arr_content[$key][new_pl] = $NEW_PL_NAME;
					$arr_content[$key][count_1] = 0;
					$arr_content[$key][count_2] = 0;
				} // end if

				if($PER_GENDER==1){ 
					$arr_content[$key][count_1]++;
					$GRAND_TOTAL_1++;
				}elseif($PER_GENDER==2){ 
					$arr_content[$key][count_2]++;
					$GRAND_TOTAL_2++;
				} // end if
			} // end if
		} else {
			if((($OLD_PL_NAME && $OLD_PL_NAME != $NEW_PL_NAME && in_array($POH_MOV_CODE, array('10350', '10360'))) || in_array($POH_MOV_CODE, array('10320', '10340'))) && (($list_type=="PER_LINE" && $search_per_type==1 && $search_pl_code==$OLD_PL_CODE) || ($list_type=="PER_LINE" && $search_per_type==2 && $search_pn_code==$OLD_PL_CODE) || ($list_type=="PER_LINE" && $search_per_type==3 && $search_ep_code==$OLD_PL_CODE) || $list_type!="PER_LINE") || ($list_type=="PER_LINE" && $search_per_type==4 && $search_tp_code==$OLD_PL_CODE)){
				$key = "$OLD_PL_NAME:$NEW_PL_NAME";
				if(!array_key_exists($key, $arr_content)){ 
					$arr_content[$key][old_pl] = $OLD_PL_NAME;
					$arr_content[$key][new_pl] = $NEW_PL_NAME;
					$arr_content[$key][count_1] = 0;
					$arr_content[$key][count_2] = 0;
				} // end if

				if($PER_GENDER==1){ 
					$arr_content[$key][count_1]++;
					$GRAND_TOTAL_1++;
				}elseif($PER_GENDER==2){ 
					$arr_content[$key][count_2]++;
					$GRAND_TOTAL_2++;
				} // end if
			} // end if
		}
	} // end while

	ksort($arr_content);
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = count($arr_content);
	
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();
		
//		for($data_count=0; $data_count<count($arr_content); $data_count++){
		$data_count = 0;
		foreach($arr_content as $key => $value){
			$NAME_1 = $arr_content[$key][old_pl];
			$NAME_2 = $arr_content[$key][new_pl];
			$COUNT_1 = $arr_content[$key][count_1];
			$COUNT_2 = $arr_content[$key][count_2];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_2;
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_1):$NAME_1), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_2):$NAME_2), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, ($COUNT_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_1)):number_format($COUNT_1)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, ($COUNT_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_2)):number_format($COUNT_2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			
			$data_count++;
		} // end for
				
		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
		$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
		$worksheet->write_string($xlsRow, 2, ($GRAND_TOTAL_1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_1)):number_format($GRAND_TOTAL_1)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write_string($xlsRow, 3, ($GRAND_TOTAL_2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL_2)):number_format($GRAND_TOTAL_2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write_string($xlsRow, 4, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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