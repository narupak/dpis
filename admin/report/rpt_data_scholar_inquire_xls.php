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
	$report_title = "สอบถามข้อมูลข้าราชการขอลาศึกษา";
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
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 20);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 20);
		$worksheet->set_column(7, 7, 30);
		$worksheet->set_column(8, 8, 30);
		$worksheet->set_column(9, 9, 20);
		$worksheet->set_column(10, 10, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "สาขาวิชา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "ระดับการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "สถานศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "ประเทศ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ชื่อทุน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "ระยะเวลา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

	} // function		
		
	if(trim($search_cardno)) $arr_search_condition[] = "(SC_CARDNO like '$search_cardno%')";
	if(trim($search_name)) $arr_search_condition[] = "(SC_NAME||' '||SC_SURNAME like '%$search_name%')";

	$from = "PER_SCHOLAR a, PER_SCHOLARSHIP b";		
  	if( trim($search_typecode) ) {
		$arr_search_condition[] = "(b.ST_CODE = '$search_typecode')";
	}
	
	if(trim($ORG_ID))  {
		$arr_search_condition[] = "(f.ORG_ID = $ORG_ID)";
	}
	
	if(trim($INS_CODE_SEARCH))  {
		$arr_search_condition[] = "(a.INS_CODE = '$INS_CODE_SEARCH' or a.INS_CODE1 = '$INS_CODE_SEARCH' or a.INS_CODE2 = '$INS_CODE_SEARCH')";
	} elseif(trim($INS_NAME_SEARCH))  {
		$arr_search_condition[] = "(a.SC_INSTITUTE = '$INS_NAME_SEARCH' or a.SC_INSTITUTE1 = '$INS_NAME_SEARCH' or a.SC_INSTITUTE2 = '$INS_NAME_SEARCH')";
	}
	
	if(trim($CT_CODE_SEARCH))  {
		$arr_search_condition[] = "(trim(a.CT_CODE) = trim('$CT_CODE_SEARCH') or trim(a.CT_CODE1) = trim('$CT_CODE_SEARCH') or trim(a.CT_CODE2) = trim('$CT_CODE_SEARCH'))";
	}
	
	if(trim($EM_CODE_SEARCH))  {
		$arr_search_condition[] = "(a.EM_CODE = '$EM_CODE_SEARCH' or a.EM_CODE1 = '$EM_CODE_SEARCH' or a.EM_CODE2 = '$EM_CODE_SEARCH')";
	}
	
	if(trim($SCH_CODE_SEARCH))  {
		$arr_search_condition[] = "(a.SCH_CODE = '$SCH_CODE_SEARCH')";
	}
	
	if ($search_sc_startdate) {
		$temp_start =  save_date($search_sc_startdate);
		$arr_search_condition[] = "(a.SC_STARTDATE >= '$temp_start')";
	} // end if
	
	if ($search_sc_enddate) {
		$temp_end =  save_date($search_sc_enddate);
		$arr_search_condition[] = "(a.SC_ENDDATE <= '$temp_end')";
	} // end if 
	
  	if( trim($search_status) )  {
		$from = "PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARINC c";
		$arr_search_condition[] = "(a.SC_ID=c.SC_ID)";
		$arr_search_condition[] = "(c.SCI_BEGINDATE <= '$search_date_tmp' and c.SC_ENDDATE >= '$search_date_tmp')";
	}

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition))?  $search_condition." and "  : "";	
				
		
	if($DPISDB=="odbc"){	
		$cmd = " select  distinct a.PN_CODE, a.SC_ID, SC_NAME, SC_SURNAME, SC_STARTDATE, a.SC_ENDDATE, a.SCH_CODE, INS_CODE, a.PER_ID, a.EL_CODE, a.EM_CODE   
						 from 		$from, PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where		$search_condition
										a.SCH_CODE=b.SCH_CODE  and a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID 
						order by SC_STARTDATE desc, a.SC_ENDDATE desc, SC_NAME, SC_SURNAME";
	}elseif($DPISDB=="oci8"){
		$cmd = " select	 distinct a.PN_CODE, a.SC_ID, SC_NAME, SC_SURNAME, SC_STARTDATE, a.SC_ENDDATE, a.SCH_CODE, INS_CODE, a.PER_ID, a.EL_CODE, a.EM_CODE   
								from 			$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
	   							where		$search_condition   a.SCH_CODE=b.SCH_CODE(+) and a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+)
								order by 	SC_STARTDATE desc, SC_ENDDATE desc, SC_NAME, SC_SURNAME  ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct PN_CODE, a.SC_ID, SC_NAME, SC_SURNAME, SC_STARTDATE, a.SC_ENDDATE, a.SCH_CODE, INS_CODE,PER_ID, a.EL_CODE, a.EM_CODE  
						 from 		$from    
						 where	$search_condition
										a.SCH_CODE=b.SCH_CODE 
						order by SC_STARTDATE desc, a.SC_ENDDATE desc, SC_NAME, SC_SURNAME ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_SC_ID = $data[SC_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_SC_ID;

		$TMP_PN_CODE = trim($data[PN_CODE]);
		$TMP_SC_NAME = $data[SC_NAME];
		$TMP_SC_SURNAME = $data[SC_SURNAME];
		$TMP_PER_NAME = $TMP_SC_NAME ." ". $TMP_SC_SURNAME;
		$TMP_INS_CODE = trim($data[INS_CODE]);
		$TMP_SCH_CODE = trim($data[SCH_CODE]);
		$TMP_PER_ID = trim($data[PER_ID]);
		$TMP_EL_CODE = trim($data[EL_CODE]);
		$TMP_EM_CODE = trim($data[EM_CODE]);
		//$dateDiff = calculate_sec(substr($data[SC_ENDDATE], 8, 2), substr($data[SC_ENDDATE], 5, 2), substr($data[SC_ENDDATE], 0, 4)) - calculate_sec(substr($data[SC_STARTDATE], 8, 2), substr($data[SC_STARTDATE], 5, 2), substr($data[SC_STARTDATE], 0, 4));
		//$TMP_RESTDATE = floor($dateDiff/60/60/24);						
		$TMP_RESTDATE = floor(date_difference($data[SC_ENDDATE], $data[SC_STARTDATE], "d"));
		$SC_STARTDATE = show_date_format($data[SC_STARTDATE], 1);
		$SC_ENDDATE = show_date_format($data[SC_ENDDATE], 1);

		$cmd = "select INS_NAME, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
				where INS_CODE = '$TMP_INS_CODE' and a.CT_CODE=b.CT_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$INS_NAME = trim($data2[INS_NAME]);
		$CT_NAME = trim($data2[CT_NAME]);
		
		$cmd = "select SCH_NAME from PER_SCHOLARSHIP where SCH_CODE = '$TMP_SCH_CODE'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SCH_NAME = trim($data2[SCH_NAME]);
		
		$cmd = "select EL_NAME from PER_EDUCLEVEL where EL_CODE = '$TMP_EL_CODE'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EL_NAME = trim($data2[EL_NAME]);
		
		$cmd = "select EM_NAME from PER_EDUCMAJOR where EM_CODE = '$TMP_EM_CODE'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EM_NAME = trim($data2[EM_NAME]);
		
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

		//  แสดงผลในส่วนของผู้ที่มีการขยายระยะเวลาศึกษา
//		$cmd = "	select SCI_BEGINDATE, SC_ENDDATE from PER_SCHOLARINC  
//						where SC_ID=$TMP_SC_ID order by SCI_BEGINDATE ";
//		$db_dpis2->send_cmd($cmd);
//		while ( $data2 = $db_dpis2->get_array() )  {
//			$TMP_SCI_BEGINDATE = show_date_format($data2[SCI_BEGINDATE], 1);
//			$TMP_SC_ENDDATE = show_date_format($data2[SC_ENDDATE], 1);
//			$TMP_RESTDATEINC = floor(date_difference($data2[SC_ENDDATE], $data2[SCI_BEGINDATE], "d"));
//			echo "<tr class='$class' height='22' $onmouse_event><td></td><td></td><td></td>
//					<td></td><td></td><td></td>
//					<td class='label_alert'>&nbsp;$TMP_SCI_BEGINDATE - $TMP_SC_ENDDATE</td>
//					<td class='label_alert' align='right'>$TMP_RESTDATEINC&nbsp;</td></tr>";
//		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$TMP_PN_NAME$TMP_PER_NAME";
		$arr_content[$data_count][em_name] = $EM_NAME;
		$arr_content[$data_count][el_name] = $EL_NAME;
		$arr_content[$data_count][ins_name] = $INS_NAME;
		$arr_content[$data_count][ct_name] = $CT_NAME;
		$arr_content[$data_count][position] = $TMP_POSITION;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][sch_name] = $SCH_NAME;
		$arr_content[$data_count][sten_date] = "$SC_STARTDATE - $SC_ENDDATE";
		$arr_content[$data_count][rest_date] = $TMP_RESTDATE;
				
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			$EL_NAME = $arr_content[$data_count][el_name];
			$INS_NAME = $arr_content[$data_count][ins_name];
			$CT_NAME = $arr_content[$data_count][ct_name];
			$POSITION = $arr_content[$data_count][position];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$SCH_NAME = $arr_content[$data_count][sch_name];
			$SC_STENDATE = $arr_content[$data_count][sten_date];
			$RESTDATE = $arr_content[$data_count][rest_date];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$CT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$SCH_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$SC_STENDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, "$RESTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างขอลาศึกษา.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างขอลาศึกษา.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>