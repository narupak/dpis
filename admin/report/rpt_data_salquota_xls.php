<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if ($SALQ_TYPE == 1) 		 {  $SALQ_TYPE1 = 1;		$SALQ_TYPE2 = 1;  }
	elseif ($SALQ_TYPE == 2)  {  $SALQ_TYPE1 = 1;		$SALQ_TYPE2 = 2;  }
	elseif ($SALQ_TYPE == 3)  {  $SALQ_TYPE1 = 2;		$SALQ_TYPE2 = 1;  }		
	elseif ($SALQ_TYPE == 4)  {  $SALQ_TYPE1 = 2;		$SALQ_TYPE2 = 2;  }
	elseif ($SALQ_TYPE == 5)  {  $SALQ_TYPE1 = 3;		$SALQ_TYPE2 = 1;  }
	elseif ($SALQ_TYPE == 6)  {  $SALQ_TYPE1 = 3;		$SALQ_TYPE2 = 2;  }

	$cmd = " select		SALQ_PERCENT, SALQ_DATE
					 from		PER_SALQUOTA
					 where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	
	$SALQ_PERCENT = $data[SALQ_PERCENT];
	$SALQ_DATE = substr(trim($data[SALQ_DATE]), 0, 10);
	if($SALQ_DATE){
		$SALQ_DATE = show_date_format($SALQ_DATE,$DATE_DISPLAY);
	} // end if

	$company_name = "";
	$report_title = "โควตาและหลักเกณฑ์การเลื่อนขั้นเงินเดือน";
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
		global $SALQ_TYPE2,$ORG_TITLE;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 60);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, ($SALQ_TYPE2==1?"จำนวนคนที่ได้ 1 ขั้น":($SALQ_TYPE2==2?"วงเงินที่เพิ่ม":"")), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, ($SALQ_TYPE2==1?"จำนวนคนที่จัดสรร":($SALQ_TYPE2==2?"วงเงินที่จัดสรร":"")), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	} // function		
		
	if($DPISDB=="odbc"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	}elseif($DPISDB=="oci8"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	}elseif($DPISDB=="mysql"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$TOTAL_SALQD_QTY1 = $TOTAL_SALQ_QTY2 = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = trim($data[ORG_NAME]);
		$SALQD_QTY1 = $data[SALQD_QTY1];
		$SALQD_QTY2 = $data[SALQD_QTY2];
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][salqd_qty1] = $SALQD_QTY1;
		$arr_content[$data_count][salqd_qty2] = $SALQD_QTY2;
		
		$TOTAL_SALQD_QTY1 += $SALQD_QTY1;
		$TOTAL_SALQD_QTY2 += $SALQD_QTY2;
		
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "$MINISTRY_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "ปีงบประมาณ $SALQ_YEAR", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, $PERSON_TYPE[$SALQ_TYPE1], set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "โควตาการเลื่อนขั้นเงินเดือนหนึ่งขั้นได้ไม่เกินร้อยละ $SALQ_PERCENT", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, ($SALQ_TYPE2==1?"เลื่อนครั้งที่ 1":($SALQ_TYPE2==2?"เลื่อนครั้งที่ 2":"")), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "ของจำนวน$PERSON_TYPE[$SALQ_TYPE1] ณ วันที่ $SALQ_DATE", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, ($SALQDTL_TYPE==1?"โครงสร้างตามกฎหมาย":($SALQDTL_TYPE==2?"โครงสร้างตามคำสั่งมอบหมายงาน":"")), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$SALQD_QTY1 = number_format($arr_content[$data_count][salqd_qty1], 2);
			$SALQD_QTY2 = number_format($arr_content[$data_count][salqd_qty2], 2);
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($SALQD_QTY1):$SALQD_QTY1), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3,(($NUMBER_DISPLAY==2)?convert2thaidigit($SALQD_QTY2):$SALQD_QTY2), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
		} // end for				
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
		$worksheet->write_string($xlsRow, 2, ($TOTAL_SALQD_QTY1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_SALQD_QTY1,2)):number_format($TOTAL_SALQD_QTY1, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 3, ($TOTAL_SALQD_QTY2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_SALQD_QTY2,2)):number_format($TOTAL_SALQD_QTY2, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "คงเหลือ", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write_string($xlsRow, 3, (($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2),2)):number_format(($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2), 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"โควตาและหลักเกณฑ์การเลื่อนขั้นเงินเดือน.xls\"");
	header("Content-Disposition: inline; filename=\"โควตาและหลักเกณฑ์การเลื่อนขั้นเงินเดือน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>