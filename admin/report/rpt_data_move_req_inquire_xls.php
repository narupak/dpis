<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$ORG_ID_01 = $MINISTRY_ID;
			$ORG_NAME_01 = $MINISTRY_NAME;
			break;
		case 4 :
			$ORG_ID_01 = $MINISTRY_ID;
			$ORG_NAME_01 = $MINISTRY_NAME;
			$ORG_ID_02 = $DEPARTMENT_ID;
			$ORG_NAME_02 = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$ORG_ID_01 = $MINISTRY_ID;
			$ORG_NAME_01 = $MINISTRY_NAME;
			break;
		case 4 :
			$ORG_ID_01 = $MINISTRY_ID;
			$ORG_NAME_01 = $MINISTRY_NAME;
			$ORG_ID_02 = $DEPARTMENT_ID;
			$ORG_NAME_02 = $DEPARTMENT_NAME;
			break;
		case 5 :
			$ORG_ID_01 = $MINISTRY_ID;
			$ORG_NAME_01 = $MINISTRY_NAME;
			$ORG_ID_02 = $DEPARTMENT_ID;
			$ORG_NAME_02 = $DEPARTMENT_NAME;
			break;
	} // end switch case

	if(trim($PER_TYPE)) 		 	$arr_search_condition[] = "(b.PER_TYPE=$PER_TYPE)";
	if(trim($EN_CODE) || trim($EM_CODE) || trim($INS_CODE)) {
		$arr_search_from[] = "PER_EDUCATE c";
		if (trim($EN_CODE))		$arr_search_condition[] = "(EN_CODE='$EN_CODE')" ;
		if(trim($EM_CODE)) 		$arr_search_condition[] = "(EM_CODE='$EM_CODE')";	
		if(trim($INS_CODE)) 		$arr_search_condition[] = "(INS_CODE='$INS_CODE')";
	}

	if(trim($PL_PN_CODE_NOW) || trim($PL_PN_CODE_MV)) {
		if ($PER_TYPE == 1) {
			$arr_search_from[] = "PER_POSITION d";
			if ($PL_PN_CODE_NOW) 	$arr_search_condition[] = "( PL_CODE='$PL_PN_CODE_NOW' )";
			if ($PL_PN_CODE_MV)		$arr_search_condition[] = "( PL_CODE='$PL_PN_CODE_MV' )";			
		} elseif ($PER_TYPE == 2) {
			$arr_search_from[] = "PER_POS_EMP e";		
			if ($PL_PN_CODE_NOW)	$arr_search_condition[] = "( PN_CODE='$PL_PN_CODE_NOW' )";
			if ($PL_PN_CODE_MV)		$arr_search_condition[] = "( PN_CODE='$PL_PN_CODE_MV' )";
		}elseif ($PER_TYPE == 3) {
			$arr_search_from[] = "PER_POS_EMPSER f";		
			if ($PL_PN_CODE_NOW)	$arr_search_condition[] = "( EP_CODE='$PL_PN_CODE_NOW' )";
			if ($PL_PN_CODE_MV)		$arr_search_condition[] = "( EP_CODE='$PL_PN_CODE_MV' )";
			}
		elseif ($PER_TYPE == 4) {
			$arr_search_from[] = "PER_POS_TEMP g";		
			if ($PL_PN_CODE_NOW)	$arr_search_condition[] = "( TP_CODE='$PL_PN_CODE_NOW' )";
			if ($PL_PN_CODE_MV)		$arr_search_condition[] = "( TP_CODE='$PL_PN_CODE_MV' )";
			}
	}

	if(trim($LEVEL_START)) $arr_search_condition[] = "(b.LEVEL_NO >= '$LEVEL_START')";
	if(trim($LEVEL_END)) $arr_search_condition[] = "(b.LEVEL_NO <= '$LEVEL_END')";
			
	if(trim($MV_DATE_START)) {
		$temp_start =  save_date($MV_DATE_START);
		$arr_search_condition[] = "(MV_DATE >= '$temp_start')";
	}
	if(trim($MV_DATE_END)){
		$temp_end =  save_date($MV_DATE_END);
		$arr_search_condition[] = "(MV_DATE <= '$temp_end')";
	}

	if($ORG_ID_02)	$arr_search_condition[] = "(a.DEPARTMENT_ID=$ORG_ID_02)";
	if(trim($ORG_ID)) {				
		if ($PER_TYPE == 1) $arr_search_condition[] = "( d.ORG_ID=$ORG_ID )";
		elseif ($PER_TYPE == 2) $arr_search_condition[] = "( e.ORG_ID=$ORG_ID )";
		elseif ($PER_TYPE == 3) $arr_search_condition[] = "( f.ORG_ID=$ORG_ID )";
		elseif ($PER_TYPE == 4) $arr_search_condition[] = "( g.ORG_ID=$ORG_ID )";

	}elseif(trim($ORG_ID_03)) {		
		$arr_search_condition[] = "(ORG_ID_REF_1=$ORG_ID_03 or ORG_ID_REF_2=$ORG_ID_03 or ORG_ID_REF_3=$ORG_ID_03)";
	}elseif($ORG_ID_02){			
		$cmd = "	select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID_02 order by ORG_ID ";
								  if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID_REF_1 in (". implode(", ", $arr_org) .") or ORG_ID_REF_2 in (". implode(", ", $arr_org) .") or ORG_ID_REF_3 in (". implode(", ", $arr_org) ."))";
	}elseif($ORG_ID_01){			
		$cmd = " 	select 		a.ORG_ID, a.ORG_NAME, a.ORG_ID_REF, a.OL_CODE, b.ORG_NAME 
							from 		PER_ORG a, PER_ORG b  
							where 	a.ORG_ID_REF=b.ORG_ID and b.OL_CODE='02' and b.ORG_ID_REF=$ORG_ID_01 
							order by 	a.ORG_ID_REF ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID_REF_1 in (". implode(", ", $arr_org) .") or ORG_ID_REF_2 in (". implode(", ", $arr_org) .") or ORG_ID_REF_3 in (". implode(", ", $arr_org) ."))"; 			
	}elseif($PV_CODE){			// ค้นหาตามจังหวัด
		$cmd = " 	select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID_REF_1 in (". implode(", ", $arr_org) .") or ORG_ID_REF_2 in (". implode(", ", $arr_org) .") or ORG_ID_REF_3 in (". implode(", ", $arr_org) ."))";
	} // end if

	$search_condition = $search_from = "";
	if(count($arr_search_from))			$search_from			= ", " . implode(", ", $arr_search_from);
	if(count($arr_search_condition)) 	$search_condition 	= "and " . implode(" and ", $arr_search_condition);	

	$company_name = "";
//	$report_title = "สอบถามข้อมูลข้าราชการ/ลูกจ้างขอย้าย";
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
		$worksheet->set_column(2, 2, 28);
		$worksheet->set_column(3, 3, 28);
		$worksheet->set_column(4, 4, 28);
		$worksheet->set_column(5, 5, 28);
		$worksheet->set_column(6, 6, 28);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 18);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สังกัดที่ขอย้ายตามลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "วันที่ขอย้าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "หนึ่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "สอง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "สาม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){	
		$cmd = " select		distinct a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, b.PER_SALARY, b.PN_CODE, 
												PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3, 
												b.ORG_ID_1 as PER_ORG_ID_1, b.ORG_ID_2 as PER_ORG_ID_2, b.ORG_ID_3 as PER_ORG_ID_3 
							from  			PER_MOVE_REQ a, PER_PERSONAL b, PER_LEVEL e $search_from
							where			a.PER_ID=b.PER_ID and b.LEVEL_NO=e.LEVEL_NO $search_condition 
												$limit_data
							group by		a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, b.PER_SALARY, b.PN_CODE, 
									  			PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3 , b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3
						order by 	a.PER_ID 	";		
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		distinct a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, b.PER_SALARY, b.PN_CODE, 
												PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3,
												b.ORG_ID_1 as PER_ORG_ID_1, b.ORG_ID_2 as PER_ORG_ID_2, b.ORG_ID_3 as PER_ORG_ID_3
								  from 		PER_MOVE_REQ a, PER_PERSONAL b, PER_LEVEL e $search_from
								  where 		a.PER_ID=b.PER_ID and b.LEVEL_NO=e.LEVEL_NO $search_condition 
								  group by	a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.PER_SALARY, b.PN_CODE, 
												PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3
								  order by 	a.PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, b.PER_SALARY, b.PN_CODE, 
												PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3, 
												b.ORG_ID_1 as PER_ORG_ID_1, b.ORG_ID_2 as PER_ORG_ID_2, b.ORG_ID_3 as PER_ORG_ID_3
							from  			PER_MOVE_REQ a, PER_PERSONAL b, PER_LEVEL e $search_from
							where			a.PER_ID=b.PER_ID  and b.LEVEL_NO=e.LEVEL_NO $search_condition 
							group by		a.PER_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, b.PER_SALARY, b.PN_CODE, 
									  			PER_NAME, PER_SURNAME, b.LEVEL_NO, e.LEVEL_NAME, e.POSITION_LEVEL, MV_DATE, MV_REMARK, 
												a.PL_CODE_1, a.PL_CODE_2, a.PL_CODE_3, a.PN_CODE_1, a.PN_CODE_2, a.PN_CODE_3, 
												a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, ORG_ID_REF_1, ORG_ID_REF_2, ORG_ID_REF_3, b.ORG_ID_1, b.ORG_ID_2, b.ORG_ID_3 
							order by 		a.PER_ID 	";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_MV_ID = trim($data[MV_ID]);
		$TMP_PER_ID = trim($data[PER_ID]);
		$TMP_POS_ID = trim($data[POS_ID]);	
		$TEMP_POEM_ID = trim($data[POEM_ID]);
		$TMP_POEMS_ID = trim($data[POEMS_ID]);
		$TMP_POT_ID = trim($data[POT_ID]);
		$TMP_SALARY = number_format(trim($data[PER_SALARY]), 2, '.', ',');
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		$TMP_MV_REMARK = (trim($data[MV_REMARK]))? trim($data[MV_REMARK]) : "-" ;
		
		$TMP_MV_DATE = show_date_format($data[MV_DATE],$DATE_DISPLAY);
		
		$PN_CODE = trim($data[PN_CODE]);
		if ($PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);
		} 
		$TMP_PER_NAME = $PN_NAME . trim($data[PER_NAME]) . " " . trim($data[PER_SURNAME]);

		$PL_CODE_1 = (trim($data[PL_CODE_1]))? trim($data[PL_CODE_1]) : "" ;
		$PL_CODE_2 = (trim($data[PL_CODE_2]))? trim($data[PL_CODE_2]) : "" ;
		$PL_CODE_3 = (trim($data[PL_CODE_3]))? trim($data[PL_CODE_3]) : "" ;
		$PL_NAME_1 = $PL_NAME_2 = $PL_NAME_3 = "";
		$cmd = "	select PL_CODE, PL_NAME from PER_LINE where PL_CODE in ('$PL_CODE_1', '$PL_CODE_2', '$PL_CODE_3')";
		$db_dpis2->send_cmd($cmd);
		while ($data2 = $db_dpis2->get_array() ) {
			$temp_id = trim($data2[PL_CODE]);
			$PL_NAME_1 = ($PL_CODE_1 == $temp_id)? trim($data2[PL_NAME]) : "$PL_NAME_1";
			$PL_NAME_2 = ($PL_CODE_2 == $temp_id)? trim($data2[PL_NAME]) : "$PL_NAME_2";	
			$PL_NAME_3 = ($PL_CODE_3 == $temp_id)? trim($data2[PL_NAME]) : "$PL_NAME_3";	
		}				
		
		$PN_CODE_1 = (trim($data[PN_CODE_1]))? trim($data[PN_CODE_1]) : "" ;
		$PN_CODE_2 = (trim($data[PN_CODE_2]))? trim($data[PN_CODE_2]) : "" ;
		$PN_CODE_3 = (trim($data[PN_CODE_3]))? trim($data[PN_CODE_3]) : "" ;
		$PN_NAME_1 = $PN_NAME_2 = $PN_NAME_3 = "";
		$cmd = "	select PN_CODE, PN_NAME from PER_POS_NAME where PN_CODE in ('$PN_CODE_1', '$PN_CODE_2', '$PN_CODE_3')";
		$db_dpis2->send_cmd($cmd);
		while ($data2 = $db_dpis2->get_array() ) {
			$temp_id = trim($data2[PL_CODE]);
			$PN_NAME_1 = ($PN_CODE_1 == $temp_id)? trim($data2[PN_NAME]) : "$PN_NAME_1";
			$PN_NAME_2 = ($PN_CODE_2 == $temp_id)? trim($data2[PN_NAME]) : "$PN_NAME_2";	
			$PN_NAME_3 = ($PN_CODE_3 == $temp_id)? trim($data2[PN_NAME]) : "$PN_NAME_3";	
		}				
		
		$ORG_ID_REF_1 = (trim($data[ORG_ID_REF_1]))? trim($data[ORG_ID_REF_1]) : 0 ;
		$ORG_ID_REF_2 = (trim($data[ORG_ID_REF_2]))? trim($data[ORG_ID_REF_2]) : 0 ;
		$ORG_ID_REF_3 = (trim($data[ORG_ID_REF_3]))? trim($data[ORG_ID_REF_3]) : 0 ;
		$ORG_NAME_REF_1 = $ORG_NAME_REF_2 = $ORG_NAME_REF_3 = "-";
		$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_REF_1, $ORG_ID_REF_2, $ORG_ID_REF_3)";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		while ($data2 = $db_dpis2->get_array() ) {
			$temp_id = trim($data2[ORG_ID]);
			$ORG_NAME_REF_1 = ($ORG_ID_REF_1 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_REF_1";
			$ORG_NAME_REF_2 = ($ORG_ID_REF_2 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_REF_2";	
			$ORG_NAME_REF_3 = ($ORG_ID_REF_3 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_REF_3";	
		}				

		$ORG_ID_1 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0 ;
		$ORG_ID_2 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0 ;
		$ORG_ID_3 = (trim($data[ORG_ID_3]))? trim($data[ORG_ID_3]) : 0 ;
		$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
		$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3)";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		while ($data2 = $db_dpis2->get_array() ) {
			$temp_id = trim($data2[ORG_ID]);
			$ORG_NAME_1 = ($ORG_ID_1 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_1";
			$ORG_NAME_2 = ($ORG_ID_2 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_2";	
			$ORG_NAME_3 = ($ORG_ID_3 == $temp_id)? trim($data2[ORG_NAME]) : "$ORG_NAME_3";	
		}				
		
		unset($temp_ORG_ID, $temp_ORG_ID_1, $TMP_ORG_NAME, $TMP_ORG_NAME_1);		
		if($TMP_POS_ID){
			$cmd = " 	select ORG_ID, ORG_ID_1, PL_CODE from PER_POSITION  
							where POS_ID=$TMP_POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_CODE = trim($data2[PL_CODE]);	
			$temp_ORG_ID = $data2[ORG_ID] + 0;
			$temp_ORG_ID_1 = $data2[ORG_ID_1] + 0;
			$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($temp_ORG_ID, $temp_ORG_ID_1)";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$TMP_ORG_NAME = ($temp_ORG_ID == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME";
				$TMP_ORG_NAME_1 = ($temp_ORG_ID_1 == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME_1";				
			}				
			if (trim($PL_CODE)) {
				$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = (trim($data2[PL_NAME]))? "$data2[PL_NAME]" . $POSITION_LEVEL : ""; 
			}
		} // end if	
		if($TMP_POEM_ID){
			unset($arr_ORG_ID, $TMP_ORG_NAME, $TMP_ORG_NAME_1);
			$cmd = " 	select ORG_ID, ORG_ID_1, PN_CODE from PER_POS_EMP   
							where POEM_ID=$TMP_POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_CODE = trim($data2[PN_CODE]);			
			$temp_ORG_ID = $data2[ORG_ID] + 0;
			$temp_ORG_ID_1 = $data2[ORG_ID_1] + 0;
			$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($temp_ORG_ID, $temp_ORG_ID_1)";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$TMP_ORG_NAME = ($temp_ORG_ID == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME";
				$TMP_ORG_NAME_1 = ($temp_ORG_ID_1 == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME_1";	
			}
			if (trim($PN_CODE)) {
				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE = '$PN_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = (trim($data2[PN_NAME]))? "$data2[PN_NAME] " . $LEVEL_NAME : ""; 
			}			
		} // end if
		if($TMP_POEMS_ID){
			unset($arr_ORG_ID, $TMP_ORG_NAME, $TMP_ORG_NAME_1);
			$cmd = " 	select ORG_ID, ORG_ID_1, EP_CODE from PER_POS_EMPSER    
							where POEMS_ID=$TMP_POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EP_CODE = trim($data2[EP_CODE]);			
			$temp_ORG_ID = $data2[ORG_ID] + 0;
			$temp_ORG_ID_1 = $data2[ORG_ID_1] + 0;
			$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($temp_ORG_ID, $temp_ORG_ID_1)";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$TMP_ORG_NAME = ($temp_ORG_ID == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME";
				$TMP_ORG_NAME_1 = ($temp_ORG_ID_1 == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME_1";	
			}
			if (trim($EP_CODE)) {
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE = '$EP_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = (trim($data2[EP_NAME]))? "$data2[EP_NAME] " . $LEVEL_NAME : ""; 
			}			
		} // end if
		if($TMP_POT_ID){
			unset($arr_ORG_ID, $TMP_ORG_NAME, $TMP_ORG_NAME_1);
			$cmd = " 	select ORG_ID, ORG_ID_1, TP_CODE from PER_POS_TEMP    
							where POT_ID=$TMP_POT_ID ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TP_CODE = trim($data2[TP_CODE]);			
			$temp_ORG_ID = $data2[ORG_ID] + 0;
			$temp_ORG_ID_1 = $data2[ORG_ID_1] + 0;
			$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($temp_ORG_ID, $temp_ORG_ID_1)";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$TMP_ORG_NAME = ($temp_ORG_ID == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME";
				$TMP_ORG_NAME_1 = ($temp_ORG_ID_1 == $temp_id)? trim($data2[ORG_NAME]) : "$TMP_ORG_NAME_1";	
			}
			if (trim($TP_CODE)) {
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE = '$TP_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = (trim($data2[TP_NAME]))? "$data2[TP_NAME] " . $LEVEL_NAME : ""; 
			}			
		} // end if
		$TMP_ORG_NAME = (trim($TMP_ORG_NAME))?  $TMP_ORG_NAME : "-";
		$TMP_ORG_NAME_1 = (trim($TMP_ORG_NAME_1))?  $TMP_ORG_NAME_1 : "-";			
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][position] = $TMP_POSITION;
		$arr_content[$data_count][org_name] = $TMP_ORG_NAME;
		$arr_content[$data_count][move_1] = $ORG_NAME_REF_1;
		$arr_content[$data_count][move_2] = $ORG_NAME_REF_2;
		$arr_content[$data_count][move_3] = $ORG_NAME_REF_3;
		$arr_content[$data_count][move_date] = $TMP_MV_DATE;
		$arr_content[$data_count][move_remark] = $TMP_MV_REMARK;
		$data_count++;

		if (trim($ORG_NAME_1) || trim($ORG_NAME_2) || trim($ORG_NAME_3)) {
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][move_1] = $ORG_NAME_1;
			$arr_content[$data_count][move_2] = $ORG_NAME_2;
			$arr_content[$data_count][move_3] = $ORG_NAME_3;
			$data_count++;
		}

		if (trim($PL_NAME_1) || trim($PL_NAME_2) || trim($PL_NAME_3)) {
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][move_1] = $PL_NAME_1;
			$arr_content[$data_count][move_2] = $PL_NAME_2;
			$arr_content[$data_count][move_3] = $PL_NAME_3;
			$data_count++;
		}

		if (trim($PN_NAME_1) || trim($PN_NAME_2) || trim($PN_NAME_3)) {
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][move_1] = $PN_NAME_1;
			$arr_content[$data_count][move_2] = $PN_NAME_2;
			$arr_content[$data_count][move_3] = $PN_NAME_3;
			$data_count++;
		}
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
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$MOVE_1 = $arr_content[$data_count][move_1];
			$MOVE_2 = $arr_content[$data_count][move_2];
			$MOVE_3 = $arr_content[$data_count][move_3];
			$MOVE_DATE = $arr_content[$data_count][move_date];
			$MOVE_REMARK = $arr_content[$data_count][move_remark];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$MOVE_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$MOVE_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$MOVE_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($MOVE_DATE):$MOVE_DATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$MOVE_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างขอย้าย.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลข้าราชการ/ลูกจ้างขอย้าย.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>