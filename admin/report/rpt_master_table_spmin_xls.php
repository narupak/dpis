<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================

	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	if ($table=="PER_GROUP_N") {
		$worksheet = &$workbook->addworksheet("ประเภทตำแหน่ง(ใหม่)");
	} else {
		if (strlen($report_title) >= 32) 
			$worksheet = &$workbook->addworksheet("Sheet1");
		else
			$worksheet = &$workbook->addworksheet("$report_title");
	}
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//	
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;		

		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 90);
		$worksheet->set_column(2, 2, 10);
                $worksheet->set_column(3, 3, 20);

		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 2, "จำนวน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // end if

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[1] like '%$search_name%')";
         if($table == "PER_SPECIAL_SKILLGRP"){
                
                if ($search_data==1){
                    $arr_search_condition[] = "($arr_fields[7] is null)";
                    }
                if ($search_ref_code){ 
                    $arr_search_condition[] = "(SS_CODE = '$search_ref_code' or REF_CODE = '$search_ref_code')";
                    }
            }
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2]
							from		$table
							$search_condition
							order by IIF(ISNULL($arr_fields[5]), 9999, $arr_fields[5]),$arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
       
                               
                 $cmd = " select                             spmin.SPMIN_CODE, spmin.SPMIN_TITLE,spmin.SPMIN_ACTIVE, 
                                                                (SELECT count(ss_code) from PER_MAPPING_SKILLMIN WHERE SPMIN_CODE = spmin.SPMIN_CODE ) as CNT 
                                                                from PER_SPECIAL_SKILLMIN spmin
							$search_condition
							order by TO_NUMBER(spmin.SPMIN_CODE) asc";
					   
                                         
                                       
        
     
		
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2]
							from		$table
							$search_condition
							order by $arr_fields[5],$arr_fields[0] 
					   ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;		
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$spmin_code    = $data[SPMIN_CODE];
			$spmin_title   = $data[SPMIN_TITLE];
                        $cnt           =$data[CNT];
			$spmin_active  = ($data[SPMIN_ACTIVE]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $spmin_code, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $spmin_title, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 2, $cnt, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 3, $spmin_active, 65, 4, 1, 0.8);			
		} // end while

	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

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