<?
	include("../../php_scripts/connect_database.php");
	// include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$workbook = new writeexcel_workbook($fname);
	//echo $report_title;
	$worksheet = &$workbook->addworksheet("ระดับผลการประเมินย่อย");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $ORG_TITLE, $ORG_TITLE1;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 40);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 40);
		$worksheet->set_column(5, 5, 40);
		$worksheet->set_column(6, 6, 40);
        $worksheet->set_column(7, 7, 15);
        $worksheet->set_column(8, 8, 10);
        $worksheet->set_column(9, 9, 10);

		
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง / ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
        $worksheet->write($xlsRow, 5, "หน่วยงานที่จัดฝึกอบรม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
        $worksheet->write($xlsRow, 6, "สถานที่ติดต่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
        $worksheet->write($xlsRow, 7, "โทรศัพท์", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ผลคัดเลือก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "ผลการประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
       
		
	} // end if

	/* $cmd = " select * from $table "; 
	$db_dpis->send_cmd($cmd); */
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

	 if(trim($search_code)) $arr_search_condition[] = "(a.PER_ID = $search_code)";
  	if(trim($search_per_id)) $arr_search_condition[] = "(a.PER_ID = $search_per_id)";
  	if(trim($search_result)) {
		$search_result_chk = $search_result - 1;
		$arr_search_condition[] = "(COD_RESULT = $search_result_chk)";	
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition)? " and " : "") . $search_condition;

	if($DPISDB=="odbc"){
		$cmd = " select	 a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO 
						from 	PER_COURSEDTL a, PER_PERSONAL b
						where	CO_ID=$CO_ID and a.PER_ID=b.PER_ID
									$search_condition
								order by	b.PER_NAME, b.PER_SURNAME";			
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO ,b.PER_CARDNO 
							from 		PER_COURSEDTL a, PER_PERSONAL b
							where		CO_ID=$CO_ID and a.PER_ID=b.PER_ID
											$search_condition
											$limit_data		
							order by 		b.PER_NAME, b.PER_SURNAME 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select	 a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO 
						from 	PER_COURSEDTL a, PER_PERSONAL b
						where	CO_ID=$CO_ID and a.PER_ID=b.PER_ID
									$search_condition
								order by	b.PER_NAME, b.PER_SURNAME";	
	} // end if
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
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
        $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;
			$num++;

			$TMP_PER_ID = $data[PER_ID];
		$current_list .= ((trim($current_list))?",":"") . "$TMP_PER_ID";

		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$COD_RESULT = ($data[COD_RESULT]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
		$COD_PASS = ($data[COD_PASS]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);
		$LEVEL_NO = trim(level_no_format($data[LEVEL_NO]));
        $PER_CARDNO = $data[PER_CARDNO];
			
			$PN_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "";
		$cmd = "select PN_NAME from PER_PRENAME where PN_CODE= '". $PN_CODE . "'";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PN_NAME = $data_dpis2[PN_NAME];
		$POS_NAME = $POS_TYPE = "";
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";	
		$db_dpis1 ->send_cmd($cmd);
		$data_level = $db_dpis1->get_array();
		$LEVEL_NAME = $data_level[LEVEL_NAME];	
		$POSITION_LEVEL = $data_level[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		if ($POS_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, PL_CODE, PT_CODE
							from 	PER_POSITION 
							where 	POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_CODE = trim($data_dpis2[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' "; 
			$db_dpis1 ->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_NAME = $data1[PL_NAME];
			
			$PT_CODE = trim($data_dpis2[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' "; 
			$db_dpis1 ->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_TYPE = ($PT_CODE == "11")? "" : $data1[PT_NAME];
				
							
		} elseif ($POEM_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							from 	PER_POS_EMP
							where 	POEM_ID=$POEM_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			//$db_dpis2->show_error();
			//echo "<hr>$cmd";
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[PN_NAME];
							
		} elseif ($POEMS_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							from 	PER_POS_EMPSER 
							where 	POEMS_ID=$POEMS_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$EP_CODE = trim($data_dpis2[EP_CODE]);
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[EP_NAME];

		}	elseif ($POT_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, TP_CODE 
							from 	PER_POS_TEMP 
							where 	POT_ID=$POT_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$TP_CODE = trim($data_dpis2[TP_CODE]);
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[TP_NAME];

		}
            
            $cmd = " select CO_ORG, CO_PLACE from PER_COURSE where CO_ID=$CO_ID "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
                $CO_ORG  = ($data1[CO_ORG]?$data1[CO_ORG]=$data1[CO_ORG]:"-");
                $CO_PLACE  = ($data1[CO_PLACE]?$data1[CO_PLACE]=$data1[CO_PLACE]:"-");
           
            
            $cmd = " select TEL from USER_DETAIL where USERNAME ='$PER_CARDNO' and USER_LINK_ID = $TMP_PER_ID and USER_FLAG ='Y'"; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$TEL = ($data1[TEL]?$data1[TEL]=$data1[TEL]:"-");
            
        
		$POS_NAME = ($POSITION_LEVEL)? "$POS_NAME$POSITION_LEVEL" : "$POS_NAME";
		
		$ORG_ID = (trim($data_dpis2[ORG_ID]))? trim($data_dpis2[ORG_ID]) : 0;
		$ORG_ID_1 = (trim($data_dpis2[ORG_ID_1]))? trim($data_dpis2[ORG_ID_1]) : 0;
		$ORG_ID_2 = (trim($data_dpis2[ORG_ID_2]))? trim($data_dpis2[ORG_ID_2]) : 0;		
		$ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "-";
		$cmd = "	select 	ORG_ID, ORG_NAME
				from		PER_ORG 
				where	ORG_ID IN ( $ORG_ID, $ORG_ID_1, $ORG_ID_2 )";
		$db_dpis2->send_cmd($cmd);
		while ( $data_dpis2 = $db_dpis2->get_array() )  {
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID )				$ORG_NAME = trim( $data_dpis2[ORG_NAME] );
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID_1 )			$ORG_NAME_1 = trim( $data_dpis2[ORG_NAME] );
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID_2 )			$ORG_NAME_2 = trim( $data_dpis2[ORG_NAME] );										
		}
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0,  "$num", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PN_NAME$PER_NAME $PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 5, "$CO_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 6, "$CO_PLACE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 7, "$TEL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8,"", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 8, $COD_RESULT, 35, 4, 1, 0.8);
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 9, $COD_PASS, 35, 4, 1, 0.8);
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