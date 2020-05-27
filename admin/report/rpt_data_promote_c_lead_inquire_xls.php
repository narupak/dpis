<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if (trim($PRO_YEAR))	 {
		$tmp_pro_year = $PRO_YEAR - 543;
		$tmp_pro_year_start = ($PRO_YEAR - 3) - 543;	
		$tmp_pro_year_end = ($PRO_YEAR - 2) - 543;
	}

	if ($DPISDB=="odbc") {
		$where_effectivedate = " LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and 	";
	} elseif ($DPISDB=="oci8") {
		$where_effectivedate = " SUBSTR(POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-10-01' and SUBSTR(POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-09-30' and ";
	}elseif($DPISDB=="mysql"){
		$where_effectivedate = " LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' and 	";
	}
	if(count($arr_search_condition)) 	$search_condition 	= implode(" and ", $arr_search_condition);	

	$company_name = "";
	$report_title = "รายชื่อข้าราชการได้เลื่อนระดับควบต้น ประจำปีงบประมาณ $PRO_YEAR";
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
		$worksheet->set_column(2, 2, 22);
		$worksheet->set_column(3, 3, 22);
		$worksheet->set_column(4, 4, 22);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 22);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ตำแหน่งเดิม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "วันที่เข้าสู่ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ขั้นเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ตำแหน่งใหม่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = "	select		b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, 
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE 
							from		PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c  
							where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
											((a.LEVEL_NO='01' and b.LEVEL_NO='01') or (a.LEVEL_NO='02' and b.LEVEL_NO='02') or (a.LEVEL_NO='03' and b.LEVEL_NO='03')) 
											and LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' 
											and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and  a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
							group by b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE 
							order by	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME	  "; 
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, 
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE 
						 from			PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c
						 where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
											((a.LEVEL_NO='01' and b.LEVEL_NO='01') or (a.LEVEL_NO='02' and b.LEVEL_NO='02') or (a.LEVEL_NO='03' and b.LEVEL_NO='03')) 
											and SUBSTR(POH_EFFECTIVEDATE,1,10) >= '$tmp_pro_year_start-10-01' 
											and SUBSTR(POH_EFFECTIVEDATE,1,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
						 group by 	b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE
						 order by  	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME
					  ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO as LEVEL_FILL, b.LEVEL_NO as LEVEL_NOW, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, 
											min(POH_EFFECTIVEDATE) as EFFECTIVEDATE, b.PER_CARDNO, c.PT_CODE 
							from		PER_POSITIONHIS a, PER_PERSONAL b, PER_POSITION c  
							where		a.MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520')  and 
											((a.LEVEL_NO='01' and b.LEVEL_NO='01') or (a.LEVEL_NO='02' and b.LEVEL_NO='02') or (a.LEVEL_NO='03' and b.LEVEL_NO='03')) 
											and LEFT(POH_EFFECTIVEDATE,10) >= '$tmp_pro_year_start-10-01' 
											and LEFT(POH_EFFECTIVEDATE,10) <= '$tmp_pro_year_end-09-30' 
											and PER_TYPE=1 and PER_STATUS=1 and  a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID and b.DEPARTMENT_ID=$DEPARTMENT_ID 
							group by b.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.LEVEL_NO, b.POS_ID, 
											c.PL_CODE, b.PER_SALARY, c.ORG_ID, c.ORG_ID_1, b.PER_CARDNO, c.PT_CODE 
							order by	a.LEVEL_NO, b.PER_SALARY, PER_NAME, PER_SURNAME	  "; 
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_NAME = trim($data[PER_NAME]) . " " . trim($data[PER_SURNAME]);
		$POS_DATE = trim($data[POS_DATE]);
		$PER_SALARY = $data[PER_SALARY];
		$PER_TYPE = trim($data[PER_TYPE]);
		$LEVEL_NOW = trim($data[LEVEL_NOW]);
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		$PER_CARDNO = trim($data[PER_CARDNO]);

		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = trim($data2[PN_NAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);
		
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);

		$OLD_POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NOW) . (($PT_NAME != "ทั่วไป" && $LEVEL_NOW >= 6)?"$PT_NAME":"")):"ระดับ $LEVEL_NOW";
		$NEW_POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NOW + 1) . (($PT_NAME != "ทั่วไป" && ($LEVEL_NOW + 1) >= 6)?"$PT_NAME":"")):"ระดับ ".($LEVEL_NOW + 1);

		$POS_ID = trim($data[POS_ID]);
		$cmd = " 	select ORG_ID, ORG_ID_1 from PER_POSITION where POS_ID=$POS_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_ID = trim($data2[ORG_ID]);
		$ORG_ID_1 = trim($data2[ORG_ID_1]);
		if ($ORG_ID || $ORG_ID_1) {
			$ORG_NAME = $ORG_NAME_1; 
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID, $ORG_ID_1) ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array() ) {
				$ORG_NAME 	= ($ORG_ID == trim($data2[ORG_ID]))?		trim($data2[ORG_NAME]) : $ORG_NAME;
				$ORG_NAME_1 	= ($ORG_ID_1 == trim($data2[ORG_ID]))?		trim($data2[ORG_NAME]) : $ORG_NAME_1;
			}
		}		
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$PN_NAME$TMP_PER_NAME";
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][org_name_1] = $ORG_NAME_1;
		$arr_content[$data_count][old_position] = $OLD_POSITION;
		$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][new_position] = $NEW_POSITION;
				
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
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME_1 = $arr_content[$data_count][org_name_1];
			$OLD_POSITION = $arr_content[$data_count][old_position];
			$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
			$PER_SALARY = number_format($arr_content[$data_count][per_salary]);
			$NEW_POSITION = $arr_content[$data_count][new_position];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$OLD_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$POH_EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$NEW_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"รายชื่อข้าราชการได้เลื่อนระดับควบต้น.xls\"");
	header("Content-Disposition: inline; filename=\"รายชื่อข้าราชการได้เลื่อนระดับควบต้น.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>