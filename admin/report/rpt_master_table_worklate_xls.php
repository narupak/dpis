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
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet("T0102");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 30);
		
		$worksheet->write($xlsRow, 0, "วันที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "รอบการมาปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เวลาที่ไม่สาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "เวลาเพิ่มพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สถานที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	if(trim($search_wl_code)) $arr_search_condition[] = "(wla.WL_CODE = '$search_wl_code')";
  	if(trim($search_wc_code)) $arr_search_condition[] = "(wla.WC_CODE = '$search_wc_code')";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = "  wla.WORK_DATE BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_min'  ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_max' ";
    }else{
      	$arr_search_condition[] = " wla.WORK_DATE = (select max(WORK_DATE) from PER_WORK_LATE) ";
    }

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
  	if($order_by==1){	//สถานที่
    	$order_str = "wla.WORK_DATE ".$SortType[$order_by].",wlo.WL_NAME ".$SortType[$order_by].",wcy.WC_SEQ_NO ".$SortType[$order_by];
  	}elseif($order_by==2) {	//รอบ
		$order_str = "wcy.WC_SEQ_NO   ".$SortType[$order_by];
  	} elseif($order_by==3) {	//สถานที่
		$order_str = "wlo.WL_NAME ".$SortType[$order_by];
  	}
	
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="oci8"){
		$cmd = "	select 		wla.WL_CODE, wla.WC_CODE, wla.WORK_DATE, wla.LATE_TIME, wla.LATE_REMARK,
                                  				wlo.WL_NAME,wcy.WC_NAME  ,wcy.WC_START
								  from 		PER_WORK_LATE  wla
                                  left join PER_WORK_LOCATION  wlo on(wlo.WL_CODE=wla.WL_CODE)
                                  left join PER_WORK_CYCLE wcy on(wcy.WC_CODE=wla.WC_CODE)
								  $search_condition
									order by  WL_CODE asc, WC_CODE asc, WORK_DATE asc ";
	}

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;
			
			$WORK_DATE = show_date_format($data[WORK_DATE], $DATE_DISPLAY);
			$WC_NAME = $data[WC_NAME];
			
			$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[WC_START],0,2).':'.substr($data[WC_START],2,2).' + '.$TMP_P_EXTRATIME.' minute');
        	$DATA_P_EXTRATIME_SHOW =  date('H:i', $newtimestampBgn) ." น.";
			
			$LATE_TIME =substr($data[LATE_TIME],0,2).":".substr($data[LATE_TIME],2,2). " น.";
			$WL_NAME = $data[WL_NAME];
			$LATE_REMARK = $data[LATE_REMARK];

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $WORK_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $WC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_P_EXTRATIME_SHOW, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $LATE_TIME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $WL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $LATE_REMARK, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"T0102.xls\"");
	header("Content-Disposition: inline; filename=\"T0102.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>