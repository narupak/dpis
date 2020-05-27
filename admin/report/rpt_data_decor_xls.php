<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$company_name = "";
	if($DC_TYPE == 1) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นสายสะพาย";
	elseif($DC_TYPE == 2) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นต่ำกว่าสายสะพาย";
	elseif($DC_TYPE == 3) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นเหรียญตรา";
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
		$worksheet->set_column(3, 3, 10);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 35);
		$worksheet->set_column(6, 6, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "เครื่องราชฯ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "เพศ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "เครื่องราชฯ ล่าสุดที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	$tmp_DE_DATE =  save_date($DE_DATE);

	if($DPISDB=="odbc"){
		$cmd = " select			a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE,  c.LEVEL_NO, e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE ,DC_NAME  
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c, PER_DECORATION d, PER_LEVEL e
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and d.DC_TYPE=$DC_TYPE and 
											a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE  and c.LEVEL_NO=e.LEVEL_NO
											$search_condition 
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE, c.LEVEL_NO, e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE, DC_NAME
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c, PER_DECORATION d , PER_LEVEL e
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and 
											d.DC_TYPE=$DC_TYPE and a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE and c.LEVEL_NO=e.LEVEL_NO 
											$search_condition
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE,  c.LEVEL_NO, e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE,  DC_NAME 
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c , PER_DECORATION d, PER_LEVEL e
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and d.DC_TYPE=$DC_TYPE and 
											a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE  and c.LEVEL_NO=e.LEVEL_NO
											$search_condition 
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_GENDER = (trim($data[PER_GENDER])==1)?  "ชาย" : "หญิง";
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_LEVEL_NAME = trim($data[POSITION_LEVEL]);
		
		$TMP_DE_ID = trim($data[DE_ID]);
		$TMP_DC_NAME = trim($data[DC_NAME]);
		$TMP_DC_CODE = trim($data[DC_CODE]);
		
		$TMP_PN_CODE = trim($data[PN_CODE]);
		if ($TMP_PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = trim($data2[PN_NAME]);
		}
		$TMP_PER_NAME = $TMP_PN_NAME . $data[PER_NAME] ." ". $data[PER_SURNAME];		

		$TMP_POS_ID = $data[POS_ID];
		if($TMP_POS_ID){
			$cmd = " select a.ORG_ID, ORG_NAME, a.PL_CODE, PL_NAME, a.PT_CODE
						  from PER_POSITION a, PER_ORG b, PER_LINE c
						  where POS_ID=$TMP_POS_ID and a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE ";	
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];			
			$PL_NAME = $data2[PL_NAME];			
			$PT_CODE = $data2[PT_CODE];			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];			
			$TMP_POSITION = trim($PL_NAME)?($PL_NAME . $TMP_LEVEL_NAME . ((trim($PT_NAME) != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?trim($PT_NAME):"")):" ".$TMP_LEVEL_NAME;
		} // end if
		
		$TMP_POEM_ID = $data[POEM_ID];
		if($TMP_POEM_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.PN_CODE, PN_NAME
						  from PER_POS_NAME a, PER_ORG b, PER_POS_EMP c  
						  where POEM_ID=$TMP_POEM_ID and b.ORG_ID=c.ORG_ID and a.PN_CODE=c.PN_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
//$db_dpis2->show_error();			
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[PN_NAME];
		} // end if

		$TMP_POEMS_ID = $data[POEMS_ID];
		if($TMP_POEMS_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.EP_CODE, EP_NAME
						  from PER_EMPSER_POS_NAME a, PER_ORG b, PER_POS_EMPSER c  
						  where POEMS_ID=$TMP_POEMS_ID and b.ORG_ID=c.ORG_ID and a.EP_CODE=c.EP_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[EP_NAME];			
		} // end if
		
		$TMP_POT_ID = $data[POT_ID];
		if($TMP_POT_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.TP_CODE, TP_NAME
						  from PER_TEMP_POS_NAME a, PER_ORG b, PER_POS_TEMP  c  
						  where POT_ID=$TMP_POT_ID and b.ORG_ID=c.ORG_ID and a.TP_CODE=c.TP_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[TP_NAME];			
		} // end if

		// หาเครื่องราชที่ได้รับล่าสุด
		if($DPISDB=="odbc"){  
			$cmd= " select top 1 pdh.DC_CODE,DC_NAME from PER_DECORATEHIS pdh, PER_DECORATION pd
							where pdh.PER_ID=$TMP_PER_ID and pdh.DC_CODE=pd.DC_CODE
							order by DEH_DATE  desc";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
		}else {		
			$cmd = " 	select 	b.DC_CODE, DC_NAME from PER_DECOR a, PER_DECORDTL b, PER_DECORATION c 
				  		where 	b.PER_ID=$TMP_PER_ID and DE_DATE < '$tmp_DE_DATE' and 
									a.DE_ID=b.DE_ID and b.DC_CODE=c.DC_CODE
						order by DE_DATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
		}
		$TMP_DC_CODE_OLD = $data2[DC_CODE];		
		$TMP_DC_NAME_OLD = (trim($data2[DC_NAME]))? $data2[DC_NAME] : "-";
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][dc_name] = $TMP_DC_NAME;
		$arr_content[$data_count][org_name] = $TMP_ORG_NAME;
		$arr_content[$data_count][per_gender] = $TMP_PER_GENDER;
		$arr_content[$data_count][per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][per_position] = $TMP_POSITION;
		$arr_content[$data_count][dc_name_old] = $TMP_DC_NAME_OLD;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
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
	} // end if

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "$MINISTRY_TITLE $MINISTRY_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "$DEPARTMENT_TITLE $DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "ปี พ.ศ. $DE_YEAR", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "วันที่ได้รับ $DE_DATE", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

	if($count_data){
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_POSITION = $arr_content[$data_count][per_position];
			$DC_NAME_OLD = $arr_content[$data_count][dc_name_old];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$PER_GENDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$PER_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$DC_NAME_OLD", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"รายงานข้าราชการได้รับเครื่องราชฯ .xls\"");
	header("Content-Disposition: inline; filename=\"รายงานข้าราชการได้รับเครื่องราชฯ .xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>