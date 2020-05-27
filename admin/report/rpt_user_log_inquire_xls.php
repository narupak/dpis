<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("รายงานข้อมูลสถาบันการศึกษา");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	if (!$PER_TYPE) $PER_TYPE = 1;
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "2:desc";
	$arrSort = explode(":",$sort_type);
	$SortType[$arrSort[0]]	= $arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1) $order_str = "LOG_DATE $SortType[$order_by], FULLNAME $SortType[$order_by]";
  	elseif($order_by==2) $order_str = "FULLNAME $SortType[$order_by], LOG_DATE $SortType[$order_by]";
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $ACTIVE_TITLE;

		$worksheet->set_column(0, 0, 5);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 95);
		$worksheet->set_column(4, 4, 45);
		
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "วันที่บันทึก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อผู้ใช้งาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "รายละเอียด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "รายละเอียดเพิ่มเติม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	$arr_search_condition = (array) null;
	if(trim($search_date_from)) {
		$temp_start = (substr($search_date_from, 6, 4) - 543) ."-". substr($search_date_from, 3, 2) ."-". substr($search_date_from, 0, 2);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(LOG_DATE), 10) >= '$temp_start') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(LOG_DATE), 1, 10) >= '$temp_start') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(SUBSTRING(trim(LOG_DATE), 1, 10) >= '$temp_start') ";
	}
	if(trim($search_date_to)){
		$temp_end = (substr($search_date_to, 6, 4) - 543) ."-". substr($search_date_to, 3, 2) ."-". substr($search_date_to, 0, 2);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(LOG_DATE), 10) >= '$temp_end') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(LOG_DATE), 1, 10) <= '$temp_end') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(SUBSTRING(trim(LOG_DATE), 1, 10) >= '$temp_end') ";
	}

  if($search_username) {  $arr_search_condition[] = "(UPPER(USERNAME) LIKE UPPER ('%$search_username%') or UPPER(FULLNAME) LIKE UPPER ('%$search_username%') or 
	                                                    LOWER(USERNAME) LIKE LOWER ('%$search_username%') or LOWER(FULLNAME) LIKE LOWER ('%$search_username%'))";	}
	if($search_log_detail) {  $arr_search_condition[] = "UPPER(LOG_DETAIL) LIKE UPPER ('%$search_log_detail%') or LOWER(LOG_DETAIL) LIKE LOWER ('%$search_log_detail%')";}

	$search_condition = "";         
	if ($arr_search_condition)		$search_condition = implode(" and ", $arr_search_condition);
		$cmd ="select 	LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE
									  from 		USER_LOG ".($search_condition ? "where ".$search_condition : "")." ";
		$count_data = $db_dpis->send_cmd($cmd);

	if($DPISDB=="odbc"){
			$cmd = "	
							select		PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE , c.LEVEL_SEQ_NO, a.DEPARTMENT_ID
										$search_field 
							from			PER_PERSONAL a, $search_from b , PER_LEVEL c
							where		a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO
										$search_condition
							order by 	$order_str ";
		}elseif($DPISDB=="oci8"){
			$cmd = "select * from (
							   select rownum rnum, q1.* from ( 
									  select 	LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE
									  from 		USER_LOG ".($search_condition ? "where ".$search_condition : "")."
									  order by 	$order_str  
							   )  q1
						) ";	
//									  where 	a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO(+)
//												$search_condition 
		}elseif($DPISDB=="mysql"){
			$cmd = "	select	PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE, a.DEPARTMENT_ID
										$search_field 
							from		PER_PERSONAL a, $search_from b
							where	a.PER_TYPE=$PER_TYPE and PER_STATUS=1
										$search_condition
							order by $order_str ";
		} // end if
		$count_page_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_page_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = $data_row = 1;
		while($data = $db_dpis->get_array()){
			$data_count++;
		    $data_num++;
			$TMP_LOG_ID = $data[LOG_ID];
			$current_list .= ((trim($current_list))?",":"") . $TMP_LOG_ID;
			$TMP_FULLNAME = trim($data[FULLNAME]);
			$TMP_LOG_DETAIL = trim($data[LOG_DETAIL]);
			include("../php_scripts/user_log_inquire_decode.php");
			$TMP_LOG_DATE = show_date_format($data[LOG_DATE], 1);

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_num, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $TMP_LOG_DATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $TMP_FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $TMP_LOG_DETAIL, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $detail_id_1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 1));
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