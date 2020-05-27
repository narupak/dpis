<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;

		$worksheet->set_column(0, 0, 25);
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 6);
		$worksheet->set_column(3, 3, 6);
		$worksheet->set_column(4, 4, 6);
		$worksheet->set_column(5, 5, 6);
		$worksheet->set_column(6, 6, 6);
		$worksheet->set_column(7, 7, 6);
		$worksheet->set_column(8, 8, 6);
		$worksheet->set_column(9, 9, 6);
		$worksheet->set_column(10, 10, 6);
		$worksheet->set_column(11, 11, 6);
		$worksheet->set_column(12, 12, 6);
		$worksheet->set_column(13, 13, 6);
		$worksheet->set_column(14, 14, 6);
		$worksheet->set_column(15, 15, 6);
		$worksheet->set_column(16, 16, 6);
		$worksheet->set_column(17, 17, 6);
		$worksheet->set_column(18, 18, 6);
		$worksheet->set_column(19, 19, 6);
		$worksheet->set_column(20, 20, 6);
		$worksheet->set_column(21, 21, 6);
		$worksheet->set_column(22, 22, 6);
		$worksheet->set_column(23, 23, 6);
		$worksheet->set_column(24, 24, 6);
		$worksheet->set_column(25, 25, 6);
		$worksheet->set_column(26, 26, 6);
		$worksheet->set_column(27, 27, 6);
		$worksheet->set_column(28, 28, 6);
		$worksheet->set_column(29, 29, 6);
		$worksheet->set_column(30, 30, 6);
		
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 3, "ปี 2548", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));		
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 8, "ปี 2549", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 13, "ปี 2550", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));		
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 18, "ปี 2551", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 23, "ปี 2552", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 28, "ปี 2553", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTableHeader", "B", "C", "TBR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อ - นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 3, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 8, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 13, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 15, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 16, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 17, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 18, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 19, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 20, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 21, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 22, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 23, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 24, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 25, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 26, "เดิม", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 27, "เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));		
		$worksheet->write($xlsRow, 28, "พิเศษ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 29, "ใหม่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 30, "%", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // end if

  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
//		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
//		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	$arr_search_condition[] = "(a.PER_STATUS = 1 and b.POS_STATUS = 1)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	if($DPISDB=="oci8") $search_condition = str_replace(" where ", " and ", $search_condition);

	if($DPISDB=="odbc"){	
		$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
										a.POS_ID, b.POS_NO, b.PL_CODE, b.ORG_ID, b.ORG_ID_1, b.PT_CODE, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
						 from 		(	
						 						PER_PERSONAL a
												left join PER_POSITION b on (a.POS_ID=b.POS_ID)	) 
						$search_condition
						 order by PER_NAME, PER_SURNAME  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
						  					a.POS_ID, b.POS_NO, b.PL_CODE, b.ORG_ID, b.ORG_ID_1, b.PT_CODE, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_RETIREDATE, a.PER_BIRTHDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
						 from 			PER_PERSONAL a, PER_POSITION b
						 where 		a.POS_ID=b.POS_ID(+)
											$search_condition
						 order by 	PER_NAME, PER_SURNAME ";
	}elseif($DPISDB=="mysql"){	
		$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
										a.POS_ID, b.POS_NO, b.PL_CODE, b.ORG_ID, b.ORG_ID_1, b.PT_CODE, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE
						 from 		(	PER_PERSONAL a
											left join PER_POSITION b on (a.POS_ID=b.POS_ID)	)
						$search_condition
						 order by PER_NAME, PER_SURNAME  ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0,"$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
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
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header();
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PER_ID = trim($data[PER_ID]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_NAME = $data[PER_NAME];
			$PER_SALARY = trim($data[PER_SALARY]);
			
			$PER_MGTSALARY = trim($data[PER_MGTSALARY]);
			
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
			
			$PN_CODE = trim($data[PN_CODE]);
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}
					
			// === หาวันที่มีผล
			for ( $i=0; $i<=5; $i++ ) { 
				unset($OLD_SALARY[$i]);
				unset($SALARY_UP[$i]);
				unset($SALARY_EXTRA[$i]);
				unset($SALARY[$i]);
				unset($PERCENT[$i]);
						
				$EFFECTIVEDATE_1 = ($search_budget_year - $i - 543) . "-10-01";
				$EFFECTIVEDATE_2 = ($search_budget_year - $i - 543) . "-04-01";
				$cmd = " select SAH_SALARY, SAH_OLD_SALARY, SAH_SALARY_UP, SAH_SALARY_EXTRA
								from   PER_SALARYHIS
								where PER_ID=$PER_ID and (SAH_EFFECTIVEDATE = '$EFFECTIVEDATE_1' or SAH_EFFECTIVEDATE = '$EFFECTIVEDATE_2') and 
											MOV_CODE not in ('21360', '21370', '21375', '215', '21510', '21520', '21530') 
								order by SAH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					if (!$OLD_SALARY[$i]) $OLD_SALARY[$i] = $data2[SAH_OLD_SALARY] + 0;
					$SALARY_UP[$i] += $data2[SAH_SALARY_UP] + 0;
					$SALARY_EXTRA[$i] += $data2[SAH_SALARY_EXTRA] + 0;
					$SALARY[$i] = $data2[SAH_SALARY] + 0;
				} // end while
				$PERCENT[$i] = number_format($SALARY_UP[$i] * 100 / $OLD_SALARY[$i], 2);
			} // end for
						
			$xlsRow = $data_count;
			$worksheet->write($xlsRow,0, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$OLD_SALARY[5]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$SALARY_UP[5]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$SALARY_EXTRA[5]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$SALARY[5]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$PERCENT[5]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$OLD_SALARY[4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$SALARY_UP[4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$SALARY_EXTRA[4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$SALARY[4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, "$PERCENT[4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, "$OLD_SALARY[3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 12, "$SALARY_UP[3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 13, "$SALARY_EXTRA[3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 14, "$SALARY[3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 15, "$PERCENT[3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 16, "$OLD_SALARY[2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 17, "$SALARY_UP[2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 18, "$SALARY_EXTRA[2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 19, "$SALARY[2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 20, "$PERCENT[2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 21, "$OLD_SALARY[1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 22, "$SALARY_UP[1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 23, "$SALARY_EXTRA[1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 24, "$SALARY[1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 25, "$PERCENT[1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 26, "$OLD_SALARY[0]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 27, "$SALARY_UP[0]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 28, "$SALARY_EXTRA[0]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 29, "$SALARY[0]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 30, "$PERCENT[0]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end while
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
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>