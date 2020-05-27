<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($ORG_ID){
		$arr_search_condition[] = "(c.ORG_ID = $ORG_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $ORG_NAME";
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(d.ORG_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$arr_search_condition[] = "(e.ORG_ID = $MINISTRY_ID)";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(c.PV_CODE = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if	
	if($search_pl_code){
		$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
	} // end if
	if($search_lv_code){
		$arr_search_condition[] = "(b.LEVEL_NO = '$search_lv_code')";
	} // end if
	$search_min_month = str_pad($search_min_month, 2, "0", STR_PAD_LEFT);
	$search_max_month = str_pad($search_max_month, 2, "0", STR_PAD_LEFT);

	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ตารางแสดงบัญชีการเปลี่ยนแปลงของค่างานรายตำแหน่งและของตำแหน่งทั้งหมดของส่วนราชการ";
	$report_title .= "||สำหรับ $DEPARTMENT_NAME $MINISTRY_NAME";
	$report_title .= "||ระหว่าง ".$month_abbr[$search_min_month][TH]." ".$search_min_year;
	$report_title .= "ถึง ".$month_abbr[$search_max_month][TH]." ".$search_max_year;
	$report_code = "รายงานผลการประเมินค่างาน";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$worksheet->set_column(0, 0, 8);
	$worksheet->set_column(1, 1, 8);
	$worksheet->set_column(2, 2, 20);
	$worksheet->set_column(3, 3, 20);
	$worksheet->set_column(4, 4, 20);
	$worksheet->set_column(5, 5, 15);
	$worksheet->set_column(6, 6, 20);
	$worksheet->set_column(7, 7, 20);
	$worksheet->set_column(8, 8, 15);
	$worksheet->set_column(9, 9, 12);
	$worksheet->set_column(10, 10, 25);

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
//		$worksheet->set_row($xlsRow, 20);
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "ส่วนราชการและตำแหน่งที่กำหนดเดิม", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 2, $xlsRow, 5);
		$worksheet->write($xlsRow, 6, "ส่วนราชการและตำแหน่งที่กำหนดใหม่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 6, $xlsRow, 8);
		$worksheet->write($xlsRow, 9, "ชื่อผู้ทดสอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "หมายเหตุ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "ส่วนราชการที่สังกัด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในการบริหารงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "ชื่อตำแหน่งในสายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "ประเภท/ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "ชื่อตำแหน่งในการบริหารงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "ชื่อตำแหน่งในสายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "ประเภท/ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "(เหตุผลในการอนุมัติครั้งสุดท้าย)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "($ORG_TITLE)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 5, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 6, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 8, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " 	select		c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME, b.POS_NO, a.TESTER_ID, a.TEST_TIME
							from		(
												(
													(
														JOB_EVALUATION a
														inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
							where		a.ISPASSED='Y'
											$search_condition
							order by	c.ORG_SEQ_NO, c.ORG_ID, cLng(b.POS_NO) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME, b.POS_NO, a.TESTER_ID, a.TEST_TIME
							from		JOB_EVALUATION a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e
							where		a.ISPASSED='Y' and a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID 
											and c.ORG_ID_REF=d.ORG_ID_REF and d.ORG_ID_REF=e.ORG_ID_REF
											$search_condition
							order by	c.ORG_SEQ_NO, c.ORG_ID, to_number(replace(b.POS_NO,'-','')) ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME, b.POS_NO, a.TESTER_ID, a.TEST_TIME, b.PM_CODE, b.PL_CODE, b.LEVEL_NO
							from		(
												(
													(
														JOB_EVALUATION a
														inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
							where		a.ISPASSED='Y'
											$search_condition
							order by	c.ORG_SEQ_NO, c.ORG_ID, b.POS_NO ";
	}
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;

	while($data = $db_dpis->get_array()){
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = $data[ORG_NAME];
		$POS_NO = $data[POS_NO_INT];
		$TESTER_ID = $data[TESTER_ID];
		$TEST_TIME = substr($data[TEST_TIME], 0, 10);
		$arr_temp = explode("-", $TEST_TIME);
		$TEST_TIME = ($arr_temp[0]+543).$arr_temp[1];
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$PL_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME = $data_dpis2[PL_NAME];

		$cmd = " select PM_NAME from PER_MGT where PM_CODE='".$PM_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME = $data_dpis2[PM_NAME];
	
		if($LEVEL_NO == "NOT SPECIFY" || !$LEVEL_NO){ 
			$LEVEL_NAME = "ยังไม่มีการกำหนดตำแหน่งงานใหม่";
		}elseif($LEVEL_NO){
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
		} // end if

//		echo ($search_min_year.$search_min_month)." :: $TEST_TIME :: ".($search_max_year.$search_max_month)."<br>";
		if( $TEST_TIME >= ($search_min_year.$search_min_month) && $TEST_TIME <= ($search_max_year.$search_max_month) ){
			$cmd = " select FULLNAME from USER_DETAIL where ID=$TESTER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TESTER_NAME = $data2[FULLNAME];
			
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][tester_name] = $TESTER_NAME;
	
			$data_count++;
		} // end if
	} // end while
	
	$count_data = count($arr_content);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 10);
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format_new("xlsFmtSubTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 10);
			$xlsRow++;
		} // end if

		print_header();
		
		$data_row = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_row++;
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$TESTER_NAME = $arr_content[$data_count][tester_name];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$data_row", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->write_string($xlsRow, 2, "$ORG_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->write_string($xlsRow, 3, "$PM_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 4, "$PL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 5, "$LEVEL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 6, "$PM_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 7, "$PL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 8, "$LEVEL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 9, "$TESTER_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
			$worksheet->write_string($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 10);
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>