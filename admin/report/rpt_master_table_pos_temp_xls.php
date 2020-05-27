<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
		global $worksheet, $ORG_TITLE, $ORG_TITLE1;

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 10);
		
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if
	
  	 	if ($POSITION_NO_CHAR=="Y") {
		$POT_NO_NUM = "POT_NO";
	} else {
		if($DPISDB=="odbc") $POT_NO_NUM = "CLng(POT_NO)";
		elseif($DPISDB=="oci8") $POT_NO_NUM = "to_number(replace(POT_NO,'-',''))";
		elseif($DPISDB=="mysql") $POT_NO_NUM = "POT_NO+0";
	} // end if
  	if(trim($search_pot_no_min)){ 
		$arr_search_condition[] = "(".$POT_NO_NUM." >= $search_pot_no_min)";
	} // end if
  	if(trim($search_pot_no_max)){ 
		$arr_search_condition[] = "(".$POT_NO_NUM." <= $search_pot_no_max)";
	} // end if
	if(trim($search_tp_code)) $arr_search_condition[] = "(trim(a.TP_CODE) = '". trim($search_tp_code) ."')";

	if(trim($search_org_id)){ 
		if($SESS_ORG_STRUCTURE==1){	
			$arr_search_condition[] = "(c.ORG_ID = $search_org_id)";			
		}else{
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
	}elseif(trim($search_department_id)){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif(trim($search_ministry_id)){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	}elseif($PV_CODE){
		$cmd = " select 	a.ORG_ID as MINISTRY_ID, b.ORG_ID as DEPARTMENT_ID, c.ORG_ID
						 from   	PER_ORG a, PER_ORG b, PER_ORG c
						 where  	a.OL_CODE='01' and b.OL_CODE='02' and c.OL_CODE='03' and c.PV_CODE='$PV_CODE' 
						 				and a.ORG_ID=b.ORG_ID_REF and b.ORG_ID=c.ORG_ID_REF
						 order by a.DEPARTMENT_ID, a.ORG_ID, b.ORG_ID, c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID in (". implode(",", $arr_org) ."))";
	}

	if(trim($search_org_id_1)){
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		}
	}
	if(trim($search_org_id_2)){
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		}
  	}
        if(trim($search_org_id_3)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_3 = $search_org_id_3)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_3 = $search_org_id_3)";
		}
  	}
        if(trim($search_org_id_4)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_4 = $search_org_id_4)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_4 = $search_org_id_4)";
		}
  	}
        if(trim($search_org_id_5)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_5 = $search_org_id_5)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_5 = $search_org_id_5)";
		}
  	}
	if(trim($search_pot_salary_min)) $arr_search_condition[] = "(POT_MIN_SALARY >= $search_pot_salary_min)";
  	if(trim($search_pot_salary_max)) $arr_search_condition[] = "(POT_MAX_SALARY <= $search_pot_salary_max)";

	if(!isset($search_pos_status)) $search_pos_status = 1;
	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POT_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POT_STATUS = 2)";

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
	$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
											a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							from	(
											(
												PER_POS_TEMP a
												inner join PER_TEMP_POS_NAME b on (a.TP_CODE=b.TP_CODE)
												) left join PER_PERSONAL c on (a.POT_ID=c.POT_ID and c.PER_TYPE=4 and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POT_ID=d.POT_ID and d.PER_TYPE=4 and (d.PER_STATUS=0 or d.PER_STATUS=2))	
							where		a.TP_CODE=b.TP_CODE 
											$search_condition
							group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
											a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							order by a.DEPARTMENT_ID, iif(isnull(POT_NO),0,$POT_NO_NUM) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									from		PER_POS_TEMP a, PER_TEMP_POS_NAME b,
										(select POT_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=4 and PER_STATUS=1) c, 
										(select POT_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=4 and (PER_STATUS=0 or PER_STATUS=2)) d
									where		a.TP_CODE=b.TP_CODE and a.POT_ID=c.POT_ID(+) and a.POT_ID=d.POT_ID(+)
													$search_condition
									group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									order by a.DEPARTMENT_ID, $POT_NO_NUM ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							from	(
											(
												PER_POS_TEMP a
												inner join PER_TEMP_POS_NAME b on (a.TP_CODE=b.TP_CODE)
												) left join PER_PERSONAL c on (a.POT_ID=c.POT_ID and c.PER_TYPE=4 and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POT_ID=d.POT_ID and d.PER_TYPE=4 and (d.PER_STATUS=0 or d.PER_STATUS=2))	
									where		a.TP_CODE=b.TP_CODE 
													$search_condition
									group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									order by a.DEPARTMENT_ID, $POT_NO_NUM ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	
//	$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$temp_POT_ID = trim($data[POT_ID]);
		//$POT_NO = trim($data[POT_NO]);
		$POT_NO = trim($data[POT_NO_NAME]).trim($data[POT_NO]);
		$TP_CODE = trim($data[TP_CODE]);
		$TP_NAME = trim($data[TP_NAME]);
		$POT_MIN_SALARY = trim($data[POT_MIN_SALARY]);
		$POT_MAX_SALARY = trim($data[POT_MAX_SALARY]);
		$POT_SALARY = number_format($POT_MIN_SALARY) . (trim($POT_MAX_SALARY)?(" - ".number_format($POT_MAX_SALARY)):"");
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]);
		//$POT_STATUS = trim($data[POT_STATUS]);
		$POT_STATUS = (trim($data[POT_STATUS])==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";;

		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME = $data_dpis2[ORG_NAME];
		$ORG_ID_REF = $data_dpis2[ORG_ID_REF];
		
		$ORG_REF_NAME = "";
		if($ORG_ID_REF){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_REF_NAME = $data_dpis2[ORG_NAME];
		}
		$ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data_dpis2[ORG_NAME];
		}
		$ORG_NAME_2 = "";
		if($ORG_ID_2){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data_dpis2[ORG_NAME];
		}
		if($DPISDB=="odbc"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
							 from 		PER_PERSONAL a
							 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
							 where	a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
							 from 		PER_PERSONAL a, PER_PRENAME b
							 where	a.PN_CODE=b.PN_CODE(+) and a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
							 from 		PER_PERSONAL a
							 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
							 where	a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
		}
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
		if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" <span class=\"label_alert\">(รอบรรจุ)</span>":"");

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$POT_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$TP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$POS_PERSON", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 5, "$POT_STATUS", 35, 4, 1, 0.8);
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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