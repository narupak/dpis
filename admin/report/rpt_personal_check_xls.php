<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $report_type, $search_per_cooperative, $search_per_member, $search_tr_code, $search_per_status, $KP7_TITLE;

		if ($report_type==1) {
			$worksheet->set_column(0, 0, 6);
			$worksheet->set_column(1, 1, 25);
			$worksheet->set_column(2, 2, 18);
			$worksheet->set_column(3, 3, 25);
			$worksheet->set_column(4, 4, 10);
			$worksheet->set_column(5, 5, 15);
			$worksheet->set_column(6, 6, 25);
			$worksheet->set_column(7, 7, 12);
			$worksheet->set_column(8, 8, 12);
			$worksheet->set_column(9, 9, 12);
			$worksheet->set_column(10, 10, 40);
			$worksheet->set_column(11, 11, 100);
			
			$worksheet->write($xlsRow, 0, "ÅÓ´Ñº·Õè", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "ª×èÍ - ¹ÒÁÊ¡ØÅ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "àÅ¢»ÃĞ¨ÓµÑÇ»ÃĞªÒª¹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "Ê¶Ò¹Ğ¡ÒÃ´ÓÃ§µÓáË¹è§", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 4, "àÅ¢·ÕèµÓáË¹è§", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 5, "ÃĞ´ÑººØ¤¤Å", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 6, "»ÃĞàÀ·¡ÒÃà¤Å×èÍ¹äËÇ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "àÅ¢·Õè¤ÓÊÑè§", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "Å§ÇÑ¹·Õè", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "ÇÑ¹·ÕèÁÕ¼Å", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 10, "µÓáË¹è§ ($KP7_TITLE)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 11, "ÊÑ§¡Ñ´ ($KP7_TITLE)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		} else {
			$worksheet->set_column(0, 0, 6);
			$worksheet->set_column(1, 1, 25);
			$worksheet->set_column(2, 2, 18);
			$worksheet->set_column(3, 3, 10);
			$worksheet->set_column(4, 4, 15);
			$worksheet->set_column(5, 5, 25);
			$worksheet->set_column(6, 6, 12);
			$worksheet->set_column(7, 7, 12);
			$worksheet->set_column(8, 8,12);
			$worksheet->set_column(9, 9, 40);
			$worksheet->set_column(10, 10, 100);
			$worksheet->set_column(11, 11, 10);
			$worksheet->set_column(12, 12, 14);
			$worksheet->set_column(13, 13, 14);
			$worksheet->set_column(14, 14, 14);
			$worksheet->set_column(15, 15, 14);
			$worksheet->set_column(16, 16, 14);
			$worksheet->set_column(17, 17, 14);
			
			$worksheet->write($xlsRow, 0, "ÅÓ´Ñº·Õè", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "ª×èÍ - ¹ÒÁÊ¡ØÅ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "àÅ¢»ÃĞ¨ÓµÑÇ»ÃĞªÒª¹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "àÅ¢¶×Í¨èÒÂ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 4, "ÃĞ´ÑººØ¤¤Å", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 5, "»ÃĞàÀ·¡ÒÃà¤Å×èÍ¹äËÇ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 6, "àÅ¢·Õè¤ÓÊÑè§", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "Å§ÇÑ¹·Õè", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "ÇÑ¹·ÕèÁÕ¼Å", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 9, "µÓáË¹è§ ($KP7_TITLE)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 10, "ÊÑ§¡Ñ´ ($KP7_TITLE)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "¢Ñé¹à§Ô¹à´×Í¹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "à§Ô¹à´×Í¹à´ÔÁ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 13, "à§Ô¹·Õèä´éàÅ×èÍ¹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "°Ò¹ã¹¡ÒÃ¤Ó¹Ç³", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "à§Ô¹µÍºá·¹¾ÔàÈÉ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 16, "à»ÍÃìà«ç¹µì", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 17, "ÍÑµÃÒà§Ô¹à´×Í¹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}
	} // end if

	if(!isset($POS_CHECK) && $command != "SEARCH") $POS_CHECK = 0;
	if(!isset($SAL_CHECK) && $command != "SEARCH") $SAL_CHECK = 0;
	if(!isset($SAL_DATE_CHECK) && $command != "SEARCH") $SAL_DATE_CHECK = 0;
	
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	$order_str = "PER_NAME, PER_SURNAME";
	$arr_search_condition[] = "(a.PER_TYPE = 1 and a.PER_STATUS = 1)";
	if ($POS_CHECK==1) 
		$arr_search_positionhis_condition[] = "(POH_POS_NO in (select POH_POS_NO from PER_POSITIONHIS a, PER_PERSONAL b 
			where a.PER_ID = b.PER_ID and b.PER_TYPE = 1 and b.PER_STATUS = 1 and a.POH_LAST_POSITION='Y' group by POH_POS_NO having count(*) > 1)) ";
	if ($POS_CHECK==2) 
		$arr_search_positionhis_condition[] = "(POH_POS_NO in (select POH_POS_NO from PER_POSITIONHIS a, PER_POSITION b 
			where a.POH_POS_NO = b.POS_NO and b.POS_STATUS = 2 and a.POH_LAST_POSITION='Y')) ";
	if ($POS_CHECK==3) 
		$arr_search_positionhis_condition[] = "(a.ES_CODE is NULL)";
	if ($POS_CHECK==4) 
		$arr_search_positionhis_condition[] = "(POH_POS_NO in (select POH_POS_NO from PER_POSITIONHIS a, PER_PERSONAL b 
			where a.PER_ID = b.PER_ID and b.PER_TYPE = 1 and b.PER_STATUS = 1 and a.POH_LAST_POSITION='Y' and a.POH_DOCNO not like '»¤%' and a.MOV_CODE = '')) ";
	if(trim($search_poh_docno)) $arr_search_positionhis_condition[] = "(a.POH_DOCNO = '$search_poh_docno')";
	if(trim($search_mov_code)) $arr_search_positionhis_condition[] = "(a.MOV_CODE = '$search_mov_code')";
	
	if(count($arr_search_positionhis_condition)){
		if($DPISDB=="odbc") $order_str = "iif(isnull(POS_NO),0,CLng(POS_NO))";
		elseif($DPISDB=="oci8") $order_str = "to_number(replace(POS_NO,'-',''))";
		elseif($DPISDB=="mysql") $order_str = "POS_NO+0";
		if ($POS_CHECK) $arr_search_positionhis_condition[] = "(a.POH_LAST_POSITION='Y')"; 
		$cmd = " select distinct PER_ID from PER_POSITIONHIS a where ". 
						  implode(" and ", $arr_search_positionhis_condition);
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_positionhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_positionhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_positionhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_positionhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_positionhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_positionhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_positionhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_positionhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_positionhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_positionhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_positionhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_positionhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_positionhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_positionhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_positionhis15[] = $data[PER_ID];
			else $arr_positionhis16[] = $data[PER_ID];
		}
		
		if (count($arr_positionhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")) or (a.PER_ID in (". implode(",", $arr_positionhis16) .")))";
		elseif (count($arr_positionhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")))";
		elseif (count($arr_positionhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")))";
		elseif (count($arr_positionhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")))";
		elseif (count($arr_positionhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")))";
		elseif (count($arr_positionhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")))";
		elseif (count($arr_positionhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")))";
		elseif (count($arr_positionhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")))";
		elseif (count($arr_positionhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")))";
		elseif (count($arr_positionhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")))";
		elseif (count($arr_positionhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")))";
		elseif (count($arr_positionhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")))";
		elseif (count($arr_positionhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")))";
		elseif (count($arr_positionhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")))";
		elseif (count($arr_positionhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_positionhis) ."))";
	} // end if
	
	/* ================= 	»ÃĞÇÑµÔ¡ÒÃÃÑºà§Ô¹à´×Í¹    ===================== */
	if ($SAL_CHECK==1) 
		$arr_search_salaryhis_condition[] = "(SAH_PAY_NO in (select SAH_PAY_NO from PER_SALARYHIS a, PER_PERSONAL b 
			where a.PER_ID = b.PER_ID and b.PER_TYPE = 1 and b.PER_STATUS = 1 and a.SAH_LAST_SALARY='Y' group by SAH_PAY_NO having count(*) > 1)) ";
	if ($SAL_CHECK==2) 
		$arr_search_salaryhis_condition[] = "(POH_POS_NO in (select POH_POS_NO from PER_POSITIONHIS a, PER_PERSONAL b 
			where a.PER_ID = b.PER_ID and b.PER_TYPE = 1 and b.PER_STATUS = 1 and a.POH_LAST_POSITION='Y' group by POH_POS_NO having count(*) > 1)) ";
	if(trim($search_sah_effectivedate)){
		$search_sah_effectivedate =  save_date($search_sah_effectivedate);
		if ($SAL_DATE_CHECK==1) $arr_search_salaryhis_condition[] = "(a.SAH_EFFECTIVEDATE = '$search_sah_effectivedate')";
		elseif ($SAL_DATE_CHECK==2) $arr_search_salaryhis_condition[] = "(a.PER_ID not in (select distinct a.PER_ID from PER_SALARYHIS a, PER_PERSONAL b 
			where a.PER_ID = b.PER_ID and b.PER_TYPE = 1 and b.PER_STATUS = 1 and a.SAH_EFFECTIVEDATE = '$search_sah_effectivedate'))";
		elseif ($SAL_DATE_CHECK==3) $arr_search_salaryhis_condition[] = "(a.PER_ID||a.SAH_SALARY_MIDPOINT in (select c.PER_ID||c.SAH_SALARY_MIDPOINT 
			from PER_SALARYHIS c, PER_PERSONAL d where c.PER_ID = d.PER_ID and d.PER_TYPE = 1 and d.PER_STATUS = 1 and c.SAH_EFFECTIVEDATE = '$search_sah_effectivedate'))";
		elseif ($SAL_DATE_CHECK==4) $arr_search_salaryhis_condition[] = "(a.SAH_OLD_SALARY + a.SAH_SALARY_UP != a.SAH_SALARY)";
		$search_sah_effectivedate = show_date_format($search_sah_effectivedate, 1);
	} // end if
	if ($SAL_DATE_CHECK==5) 
		$arr_search_salaryhis_condition[] = "(a.PER_ID||a.SAH_EFFECTIVEDATE in 
			(select c.PER_ID||max(c.SAH_EFFECTIVEDATE) as SAH_EFFECTIVEDATE from PER_SALARYHIS c, PER_PERSONAL d 
			where c.PER_ID = d.PER_ID and d.PER_TYPE = 1 and d.PER_STATUS = 1 group by c.PER_ID) and a.SAH_LAST_SALARY != 'Y')";
	if(trim($search_sah_docno)) $arr_search_salaryhis_condition[] = "(a.SAH_DOCNO = '$search_sah_docno')";
	
	if(count($arr_search_salaryhis_condition)){
		if($DPISDB=="odbc") $order_str = "";
		elseif($DPISDB=="oci8") $order_str = "a.PAY_ID";
		elseif($DPISDB=="mysql") $order_str = "";
		if ($SAL_CHECK && !trim($search_sah_effectivedate) && !trim($search_sah_docno)) $arr_search_salaryhis_condition[] = "(a.SAH_LAST_SALARY='Y')"; 
		if ($SAL_DATE_CHECK==5) 
			$cmd = " select a.PER_ID from PER_SALARYHIS a, PER_PERSONAL b where 
			a.PER_ID=b.PER_ID  and b.PER_TYPE = 1 and b.PER_STATUS = 1 and ". 
							  implode(" and ", $arr_search_salaryhis_condition);
		else
			$cmd = " select distinct PER_ID from PER_SALARYHIS a where ". 
							  implode(" and ", $arr_search_salaryhis_condition);
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_salaryhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_salaryhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_salaryhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_salaryhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_salaryhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_salaryhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_salaryhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_salaryhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_salaryhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_salaryhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_salaryhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_salaryhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_salaryhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_salaryhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_salaryhis15[] = $data[PER_ID];
			elseif ($count < 16000) $arr_salaryhis16[] = $data[PER_ID];
			elseif ($count < 17000) $arr_salaryhis17[] = $data[PER_ID];
			elseif ($count < 18000) $arr_salaryhis18[] = $data[PER_ID];
			elseif ($count < 19000) $arr_salaryhis19[] = $data[PER_ID];
			else $arr_salaryhis20[] = $data[PER_ID];
		}
		
		if (count($arr_salaryhis20)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis20) .")))";
		elseif (count($arr_salaryhis19)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")))";
		elseif (count($arr_salaryhis18)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")))";
		elseif (count($arr_salaryhis17)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")))";
		elseif (count($arr_salaryhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")))";
		elseif (count($arr_salaryhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")))";
		elseif (count($arr_salaryhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")))";
		elseif (count($arr_salaryhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")))";
		elseif (count($arr_salaryhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")))";
		elseif (count($arr_salaryhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")))";
		elseif (count($arr_salaryhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")))";
		elseif (count($arr_salaryhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")))";
		elseif (count($arr_salaryhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")))";
		elseif (count($arr_salaryhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")))";
		elseif (count($arr_salaryhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")))";
		elseif (count($arr_salaryhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")))";
		elseif (count($arr_salaryhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")))";
		elseif (count($arr_salaryhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")))";
		elseif (count($arr_salaryhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_salaryhis) ."))";
	} // end if
	
	/* ======================================================== */

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	if($DPISDB=="oci8") $search_condition = str_replace(" where ", " and ", $search_condition);

	if($DPISDB=="odbc"){	
		$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, 
							  					a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.PT_CODE, a.PER_CARDNO
							 from 		(	
							 						PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	) 
							$search_condition
							 order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, 
							  					a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.PT_CODE, a.PER_CARDNO
							 from 			PER_PERSONAL a, PER_POSITION b
							 where 		a.POS_ID=b.POS_ID(+)
												$search_condition
							 order by 	$order_str ";
	}elseif($DPISDB=="mysql"){	
		$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, 
							  					a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.PT_CODE, a.PER_CARDNO
							 from 		(	PER_PERSONAL a
												left join PER_POSITION b on (a.POS_ID=b.POS_ID)	)
							$search_condition
							 order by $order_str ";
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
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($report_type==2) {
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PER_ID = trim($data[PER_ID]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PN_CODE = trim($data[PN_CODE]);
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$CARDNO = $data[PER_CARDNO];
			
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}
					
			if ($report_type==1) {
				$cmd = " select ES_CODE, POH_POS_NO, POH_LEVEL_NO, MOV_CODE, POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE, POH_PL_NAME, POH_ORG
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and POH_LAST_POSITION='Y' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ES_CODE = trim($data2[ES_CODE]);
				$POH_POS_NO = trim($data2[POH_POS_NO]);
				$POH_LEVEL_NO = trim($data2[POH_LEVEL_NO]);
				$MOV_CODE = trim($data2[MOV_CODE]);
				$POH_DOCNO = trim($data2[POH_DOCNO]);
				$POH_DOCDATE = trim($data2[POH_DOCDATE]);
				$DOCDATE = show_date_format(substr($POH_DOCDATE, 0, 10),$DATE_DISPLAY);
				$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
				$EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
				$POH_PL_NAME = trim($data2[POH_PL_NAME]);
				$POH_ORG = trim($data2[POH_ORG]);

				$cmd = "select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO'";
				$db_dpis2->send_cmd($cmd);
				$data2=$db_dpis2->get_array();
				$LEVEL_NAME=trim($data2[POSITION_LEVEL]);
				
				$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$ES_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ES_NAME = $data2[ES_NAME];
				
				$cmd = "	select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MOV_NAME = $data2[MOV_NAME];
			} elseif ($report_type==2) {
				$cmd = " select SAH_PAY_NO, LEVEL_NO, MOV_CODE, SAH_DOCNO, SAH_DOCDATE, SAH_EFFECTIVEDATE, SAH_POSITION, SAH_ORG, 
												SM_CODE, SAH_OLD_SALARY, SAH_SALARY_UP, SAH_SALARY_MIDPOINT, SAH_SALARY_EXTRA, SAH_PERCENT_UP,
												SAH_SALARY
								from   PER_SALARYHIS
								where PER_ID=$PER_ID and SAH_LAST_SALARY='Y' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_PAY_NO = trim($data2[SAH_PAY_NO]);
				$LEVEL_NO = trim($data2[LEVEL_NO]);
				$MOV_CODE = trim($data2[MOV_CODE]);
				$SAH_DOCNO = trim($data2[SAH_DOCNO]);
				$SAH_DOCDATE = trim($data2[SAH_DOCDATE]);
				$DOCDATE = show_date_format(substr($SAH_DOCDATE, 0, 10),$DATE_DISPLAY);
				$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
				$EFFECTIVEDATE = show_date_format(substr($SAH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
				$SAH_POSITION = trim($data2[SAH_POSITION]);
				$SAH_ORG = trim($data2[SAH_ORG]);
				$SM_CODE = trim($data2[SM_CODE]);
				$SAH_OLD_SALARY = $data2[SAH_OLD_SALARY];
				$SAH_SALARY_UP = $data2[SAH_SALARY_UP];
				$SAH_SALARY_MIDPOINT = $data2[SAH_SALARY_MIDPOINT];
				$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA];
				$SAH_PERCENT_UP = $data2[SAH_PERCENT_UP];
				$SAH_SALARY = $data2[SAH_SALARY];

				$cmd = "select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
				$db_dpis2->send_cmd($cmd);
				$data2=$db_dpis2->get_array();
				$LEVEL_NAME=trim($data2[POSITION_LEVEL]);
				
				$cmd = "	select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MOV_NAME = $data2[MOV_NAME];
				
				$cmd = " select SM_NAME from PER_SALARY_MOVMENT where SM_CODE='$SM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SM_NAME = $data2[SM_NAME];
			}

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $data_count-1, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2,card_no_format($CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			if ($report_type==1) {
				$worksheet->write_string($xlsRow, 3, "$ES_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$POH_POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$POH_DOCNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$DOCDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$POH_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$POH_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} elseif ($report_type==2) {
				$worksheet->write_string($xlsRow, 3, "$SAH_PAY_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$SAH_DOCNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$DOCDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$SAH_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$SAH_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$SM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$SAH_OLD_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$SAH_SALARY_UP", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$SAH_SALARY_MIDPOINT", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$SAH_SALARY_EXTRA", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$SAH_PERCENT_UP", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$SAH_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			}
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** äÁèÁÕ¢éÍÁÙÅ *****", set_format("xlsFmtTitle", "B", "C", "", 1));
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
		if ($report_type==2) {
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
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