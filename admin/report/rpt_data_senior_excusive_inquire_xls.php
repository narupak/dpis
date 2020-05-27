<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "สอบถามข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง";
	$report_code = "";
	
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

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 25);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 20);
		$worksheet->set_column(8, 8, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ตำแหน่งระหว่างอบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "กรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "กระทรวง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "ระยะเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "รุ่น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // function		
		
	$from = "PER_SENIOR_EXCUSIVE a, PER_LEVEL b";		
	if(trim($search_cardno)) $arr_search_condition[] = "(SE_CARDNO like '".trim($search_cardno)."%')";	
	if(trim($search_per_id)) $arr_search_condition[] = "(a.PER_ID = $search_per_id)";
	if(trim($search_name)) $arr_search_condition[] = "(SE_NAME like '$search_name%')";
	if(trim($search_surname)) $arr_search_condition[] = "(SE_SURNAME like '$search_surname%')";
	if($search_ministry_name){
		$arr_search_condition[] = "(SE_MINISTRY_NAME = '".trim($search_ministry_name)."')";	
	}
	if($search_department_name){
		$arr_search_condition[] = "(SE_DEPARTMENT_NAME = '".trim($search_department_name)."')";			
	}
	if($search_train_ministry_name){
		$arr_search_condition[] = "(SE_TRAIN_MINISTRY = '".trim($search_train_ministry_name)."')";	
	}
	if($search_train_department_name){
		$arr_search_condition[] = "(SE_TRAIN_DEPARTMENT = '".trim($search_train_department_name)."')";			
	}
	if(trim($search_org_name))  {
		$arr_search_condition[] = "(SE_ORG_NAME = '".trim($search_org_name)."')";	
	}
	if( trim($search_se_no) ) {
		$arr_search_condition[] = "(trim(SE_NO) = '".trim($search_se_no)."')";	
	}
	if(trim($search_se_startdate)) {
		$search_se_startdate_tmp =  save_date($search_se_startdate);
		$arr_search_condition[] = "(SE_STARTDATE <= '$search_se_startdate_tmp' and SE_ENDDATE >= '$search_se_startdate_tmp')";
	} 
	if ($search_se_enddate) {
		$search_se_enddate_tmp =  save_date($search_se_enddate);
		$arr_search_condition[] = "(SE_ENDDATE <= '$search_se_enddate_tmp')";
	} // end if 
	$search_condition = "";
	if(count($arr_search_condition)) 	$search_condition 	= " and " . implode(" and ", $arr_search_condition);	
		
	if($DPISDB=="odbc"){	
		$cmd = " select  distinct a.PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID   
						 from 		$from, PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where		a.LEVEL_NO=b.LEVEL_NO  and a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID 
											$search_condition
						$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select	 distinct a.PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID   
								from 			$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
	   							where		a.LEVEL_NO=b.LEVEL_NO(+) and a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+)
													$search_condition   
							$order_str";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID  
						 from 		$from, PER_ORG f, PER_POSITION e, PER_PERSONAL d     
						 where	a.LEVEL_NO=b.LEVEL_NO and a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
						$order_str";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "$cmd ($count_data)<br>";		exit;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_SE_ID = $data[SE_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_SE_ID;

		$TMP_PN_CODE = trim($data[PN_CODE]);
		$TMP_SE_NAME = $data[SE_NAME];
		$TMP_SE_SURNAME = $data[SE_SURNAME];
		$TMP_PER_NAME = $TMP_SE_NAME ." ". $TMP_SE_SURNAME;
		$TMP_SE_NO = trim($data[SE_NO]);
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_SE_TRAIN_POSITION = trim($data[SE_TRAIN_POSITION]);
		$TMP_SE_TRAIN_DEPARTMENT = trim($data[SE_TRAIN_DEPARTMENT]);
		$TMP_SE_TRAIN_MINISTRY = trim($data[SE_TRAIN_MINISTRY]);
		$TMP_PER_ID = trim($data[PER_ID]);
		//$dateDiff = calculate_sec(substr($data[SE_ENDDATE], 8, 2), substr($data[SE_ENDDATE], 5, 2), substr($data[SE_ENDDATE], 0, 4)) - calculate_sec(substr($data[SE_STARTDATE], 8, 2), substr($data[SE_STARTDATE], 5, 2), substr($data[SE_STARTDATE], 0, 4));
		//$TMP_RESTDATE = floor($dateDiff/60/60/24);						
		$TMP_RESTDATE = floor(date_difference($data[SE_ENDDATE], $data[SE_STARTDATE], "d"));
		$SE_STARTDATE = show_date_format($data[SE_STARTDATE], 1);
		$SE_ENDDATE = show_date_format($data[SE_ENDDATE], 1);

		$cmd = "select LEVEL_NAME from PER_LEVEL
				where LEVEL_NO = '$TMP_LEVEL_NO'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[SCH_NAME]);
		
		if ($TMP_PER_ID) {
			$cmd = "select POS_ID, POEM_ID, POEMS_ID,POT_ID, a.LEVEL_NO, b.LEVEL_NAME,b.POSITION_LEVEL, PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL a, PER_LEVEL b where PER_ID=$TMP_PER_ID and a.LEVEL_NO=b.LEVEL_NO";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_ID = trim($data2[POS_ID]);
			$POEM_ID = trim($data2[POEM_ID]);
			$POEMS_ID = trim($data2[POEMS_ID]);
			$POT_ID = trim($data2[POT_ID]);
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			$TMP_PN_CODE = trim($data2[PN_CODE]);
			$TMP_PER_NAME = trim($data2[PER_NAME]) ." ". trim($data2[PER_SURNAME]);

			if ($POS_ID) { 
				$cmd = " select b.PL_NAME, a.CL_NAME, c.ORG_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b, PER_ORG c 
						where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd;
				$data2 = $db_dpis2->get_array();
//				$TMP_POSITION = $data2[PL_NAME] . " " . $data2[CL_NAME];
				$TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
			} elseif ($POEM_ID) {
				$cmd = "	select PN_NAME, ORG_NAME from PER_POS_EMP a, PER_POS_NAME b, PER_ORG c  
						where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[PN_NAME];
			}elseif ($POEMS_ID) {
				$cmd = "	select EP_NAME, ORG_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c  
						where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[EP_NAME];
			}elseif ($POT_ID) {
				$cmd = "	select TP_NAME, ORG_NAME from PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c  
						where POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[TP_NAME];
			}
			$ORG_NAME = $data2[ORG_NAME];					
		}	// if ($TMP_PER_ID)
		
		if ($TMP_PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$TMP_PN_NAME$TMP_PER_NAME";
		$arr_content[$data_count][position] = $TMP_POSITION;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][train_position] = $TMP_SE_TRAIN_POSITION;
		$arr_content[$data_count][train_department] = $TMP_SE_TRAIN_DEPARTMENT;
		$arr_content[$data_count][train_ministry] = $TMP_SE_TRAIN_MINISTRY;
		$arr_content[$data_count][sten_date] = "$SE_STARTDATE - $SE_ENDDATE";
		$arr_content[$data_count][se_no] = $TMP_SE_NO;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$POSITION = $arr_content[$data_count][position];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$SCH_NAME = $arr_content[$data_count][train_position];
			$INS_NAME = $arr_content[$data_count][train_department];
			$CT_NAME = $arr_content[$data_count][train_ministry];
			$SE_STENDATE = $arr_content[$data_count][sten_date];
			$RESTDATE = $arr_content[$data_count][se_no];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$SCH_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$CT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$SE_STENDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$RESTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end for				
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>