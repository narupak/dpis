<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select CP_CODE, CP_NAME from PER_COMPETENCE where CP_ACTIVE=1 order by CP_MODEL, CP_CODE ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_CP_CODE[] = $data[CP_CODE];		
		$ARR_CP_NAME[] = $data[CP_NAME];
	}
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;
		global $ARR_CP_CODE,$ARR_CP_NAME;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 55);
		$worksheet->set_column(2, 2, 45);
		$worksheet->set_column(3, 3, 45);
		$worksheet->set_column(4, 40, 30);
		
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ช่วงระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		for($i=3; $i<count($ARR_CP_NAME)+3; $i++){ 
			$tmp_cp_name = $ARR_CP_NAME[$i-3];
			$worksheet->write($xlsRow,$i+1 , "$tmp_cp_name", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}
		
		
	} // end if
	
  	 if($search_org_id1){
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(b.ORG_ID=$search_org_id1)";
		else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(e.ORG_ID=$search_org_id1)";
	}elseif($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if
	if(trim($search_per_name)) $arr_search_condition[] = "(e.PER_NAME like '$search_per_name%')";
	if(trim($search_per_surname)) $arr_search_condition[] = "(e.PER_SURNAME like '$search_per_surname%')";
  	if(trim($search_pos_no)) $arr_search_condition[] = "(b.POEMS_NO = '$search_pos_no')";
  	if(trim($search_ep_code)) $arr_search_condition[] = "(b.EP_CODE = '$search_ep_code')";
	$arr_search_condition[] = "(a.PER_TYPE = 3)";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
	
	if($DPISDB=="odbc"){
		$cmd = " select		distinct 
										a.POS_ID, POEMS_NO_NAME, IIf(IsNull(b.POEMS_NO), 0, CLng(b.POEMS_NO)) as POEMS_NO, c.EP_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
						 from		(
											(
													(
														PER_POSITION_COMPETENCE a
														inner join PER_POS_EMPSER b on (a.POS_ID=b.POEMS_ID)
													) inner join PER_EMPSER_POS_NAME c on (b.EP_CODE=c.EP_CODE)
											) left join PER_PERSONAL e on (b.POEMS_ID=e.POEMS_ID)
										) left join PER_PRENAME f on (e.PN_CODE=f.PN_CODE)
						 where	(e.PER_STATUS=1 or e.PER_STATUS IS NULL)
									 	$search_condition
									 	
						 order by POEMS_NO_NAME, IIf(IsNull(b.POEMS_NO), 0, CLng(b.POEMS_NO)) ";
	}elseif($DPISDB=="oci8"){			   
		$rec_start = (($current_page-1) * $data_per_page) + 1;
		$rec_end = ($current_page > 1)? ($current_page * $data_per_page) : $data_per_page;

		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		distinct a.POS_ID, POEMS_NO_NAME, b.POEMS_NO, c.EP_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
								 from			PER_POSITION_COMPETENCE a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c, PER_PERSONAL e, PER_PRENAME f
								 where		a.POS_ID=b.POEMS_ID and b.EP_CODE=c.EP_CODE and b.POEMS_ID=e.POEMS_ID(+) and e.PN_CODE=f.PN_CODE(+) and (e.PER_STATUS=1 or e.PER_STATUS IS NULL)
													$search_condition 
								  order by 	POEMS_NO_NAME, to_number(replace(b.POEMS_NO,'-','')) ";					   
	}elseif($DPISDB=="mysql"){
		$cmd = " select		distinct a.POS_ID, POEMS_NO_NAME, b.POEMS_NO as POEMS_NO, c.EP_NAME, e.PER_ID, f.PN_NAME, e.PER_NAME, e.PER_SURNAME, e.LEVEL_NO
						 from		PER_POSITION_COMPETENCE as a
														inner join PER_POS_EMPSER as b on (a.POS_ID=b.POEMS_ID)
													 inner join PER_EMPSER_POS_NAME as c on (b.EP_CODE=c.EP_CODE)
											left join PER_PERSONAL as e on (b.POEMS_ID=e.POEMS_ID)
										left join PER_PRENAME as f on (e.PN_CODE=f.PN_CODE)
						 where	e.PER_STATUS=1 or Isnull(e.PER_STATUS)
									 	$search_condition
						 order by POEMS_NO_NAME, b.POEMS_NO+0 ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($j=1; $j<=count($ARR_CP_CODE); $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$temp_POS_ID = trim($data[POS_ID]);
			$POS_NO = trim($data[POEMS_NO]);
			$EP_NAME = trim($data[EP_NAME]);

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$LEVEL_NO = $data[LEVEL_NO];
			
			$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data2[LEVEL_NAME];
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			$POSITION = "$EP_NAME";
			$POS_PERSON = "";
			if($PER_ID){ 
				$POS_PERSON = "$PN_NAME$PER_NAME $PER_SURNAME";
			} // end if

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, " $POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POSITION_LEVEL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$POS_PERSON", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			for($i=0; $i<count($ARR_CP_CODE); $i++){ 
				$tmp_cp_code = $ARR_CP_CODE[$i];
				$cmd = "	select		PC_TARGET_LEVEL 
									from		PER_POSITION_COMPETENCE 
									where 	POS_ID=$temp_POS_ID and CP_CODE='$tmp_cp_code' and PER_TYPE=3	";	
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$TMP_PC_TARGET_LEVEL = trim($data2[PC_TARGET_LEVEL]);

				$worksheet->write($xlsRow, ($i+4), ($TMP_PC_TARGET_LEVEL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TMP_PC_TARGET_LEVEL)):number_format($TMP_PC_TARGET_LEVEL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			} // end for
			
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		
		
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