<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");


	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$worksheet = &$workbook->addworksheet("T0302");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 10);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 23);
		
		$worksheet->write($xlsRow, 0, "หมายเลขเครื่อง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อเครื่องบันทึกเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "สถานที่ปฏิบัติราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "IP Address", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สถานะ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "Lock/Unlock", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "วัน-เวลาของสถานะล่าสุด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if


  if(trim($search_code)) $arr_search_condition[] = "(a.TA_CODE like '".trim($search_code)."%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.TA_NAME like '%".trim($search_name)."%')";
  	if(trim($search_wl_code)) $arr_search_condition[] = "(a.WL_CODE = '".trim($search_wl_code)."')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);



		$cmd = " select		a.TA_CODE, a.TA_NAME, a.WL_CODE, a.TA_ACTIVE, b.WL_NAME,
                                 			a.ALIVE_STATUS,a.ALIVE_IP_ADDR,a.LOCK_STATUS,
                                            TO_CHAR(a.SYNC_DATE,'yyyy-mm-dd') AS SYNC_DATE,
                                            TO_CHAR(a.SYNC_DATE,'HH24:MI:SS') AS SYNC_TIME
								from  		PER_TIME_ATT a, PER_WORK_LOCATION b
								where		a.WL_CODE = b.WL_CODE AND a.TA_ACTIVE=1
												$search_condition order by a.TA_CODE ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$DATA_TA_CODE = $data[TA_CODE];
			
			$DATA_TA_NAME = $data[TA_NAME]; 

			
			$DATA_WL_NAME = $data[WL_NAME];

			
			$ALIVE_IP_ADDR = $data[ALIVE_IP_ADDR];

			
			if($data[ALIVE_STATUS]==1){ 
      				$ALIVE_STATUS = "ON";
           }else if($data[ALIVE_STATUS]==0){ 
           			$ALIVE_STATUS = "OFF";
           			
      	   }else{ 
           			$ALIVE_STATUS = "";
           }
		   

		   
		   $DATA_SYNC_DATE="";
			if($data[SYNC_DATE]){
				$DATA_SYNC_DATE = show_date_format($data[SYNC_DATE], $DATE_DISPLAY)." ".$data[SYNC_TIME];
			}

			
			if($data[LOCK_STATUS]=="N"){  
      				$LOCK_STATUS="Unlock";
			}else if($data[LOCK_STATUS]=="Y"){ 
					$LOCK_STATUS="Lock";	
			}

			
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $DATA_TA_CODE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $DATA_TA_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $DATA_WL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $ALIVE_IP_ADDR, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $ALIVE_STATUS, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $LOCK_STATUS, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $DATA_SYNC_DATE, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"T0302.xls\"");
	header("Content-Disposition: inline; filename=\"T0302.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>