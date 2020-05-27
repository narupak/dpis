<?  
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $report_type, $search_per_cooperative, $search_per_member, $search_tr_code, $search_per_status, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 18);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 5);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 8);
		$worksheet->set_column(10, 10, 15);
		$worksheet->set_column(11, 11, 8);
		$worksheet->set_column(12, 12, 25);
		$worksheet->set_column(13, 13, 25);
		$worksheet->set_column(14, 14, 25);
		$worksheet->set_column(15, 15, 30);
		$worksheet->set_column(16, 16, 25);
		$worksheet->set_column(17, 17, 10);
		$worksheet->set_column(18, 18, 25);
		$worksheet->set_column(19, 19, 25);
		$worksheet->set_column(20, 20, 25);
		$worksheet->set_column(21, 21, 22);
		$worksheet->set_column(22, 22, 30);
		$worksheet->set_column(23, 23, 30);
		$worksheet->set_column(24, 24, 30);
		$worksheet->set_column(25, 25, 10);
		$worksheet->set_column(26, 26, 15);
		$worksheet->set_column(27, 27, 35);
		$worksheet->set_column(28, 28, 10);

		if ($report_type==3) {	
			$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "ส่วน/อำเภอ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 4, "สำนัก/กอง/จังหวัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 5, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 6, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "เลขเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "จังหวัดเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "ช่วยราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 10, "รายชื่อผู้ครองเลขถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 11, "เลขตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}else if($report_type==4){
			$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "ระดับตำแหน่ง (บช.1)", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "ฝ่าย/กง.", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 4, "ส่วน/อำเภอ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 5, "สำนัก/กอง/จังหวัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 6, "ชื่อ - นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "นอ.รุ่น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "ระดับบุคคล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "เลขถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 10, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "ระดับถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 12, "ต่ำกว่าสำนัก/กอง 2 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 13, "ต่ำกว่าสำนัก/กอง 1 ระดับ ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "เทียบเท่าสำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}else {
			$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "ชื่อ - นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "รหัสข้าราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 4, "เพศ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 5, "วัน/เดือน/ปีเกิด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 6, "วันที่บรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "วันที่เข้าส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "วันที่เลื่อนระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "ปีเกษียณ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 10, "ระดับบุคคล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 11, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "ระดับการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 13, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "สาขาวิชา/วิชาเอก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "สถาบันการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 16, "หมายเหตุวุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 17, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 18, "ตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 19, "ตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 20, "ช่วงระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 21, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));		
			$worksheet->write($xlsRow, 22, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 23, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 24, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 25, "รหัสจังหวัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 26, "วันที่ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 27, "ช่วยราชการ/ปฏิบัติหน้าที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 28, "เลขถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			if ($report_type==1) {
				$worksheet->set_column(29, 29, 12);
				$worksheet->set_column(30, 30, 12);
				$worksheet->set_column(31, 31, 12);
				$worksheet->set_column(32, 32, 12);
				$worksheet->set_column(33, 33, 12);
				$worksheet->set_column(34, 34, 12);
				$worksheet->set_column(35, 35, 12);
				$worksheet->set_column(36, 36, 12);
				$worksheet->set_column(37, 37, 12);
				$worksheet->set_column(38, 38, 12);
				$worksheet->set_column(39, 39, 12);
				$worksheet->set_column(40, 40, 12);
				$worksheet->set_column(41, 41, 12);
				$worksheet->set_column(42, 42, 12);
				$worksheet->set_column(43, 43, 12);
				$worksheet->set_column(44, 44, 12);
				$worksheet->set_column(45, 45, 12);
				$worksheet->set_column(46, 46, 12);
				$worksheet->set_column(47, 47, 12);
				$worksheet->set_column(48, 48, 20);
				$worksheet->set_column(49, 49, 20);
				$worksheet->set_column(50, 50, 20);
				$worksheet->set_column(51, 51, 8);
				if (in_array(2, $search_per_status)) { 
					$worksheet->set_column(52, 52, 30);
					$worksheet->set_column(53, 53, 12);
					$worksheet->set_column(54, 54, 25);
					$worksheet->set_column(55, 55, 30);
				}
		
				$worksheet->write($xlsRow, 29, "วันที่บรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 30, "ระดับ 2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 31, "ระดับ 3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 32, "ระดับ 4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 33, "ระดับ 5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 34, "ระดับ 6", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 35, "ระดับ 7", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 36, "ระดับ 8", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 37, "ระดับ 9", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 38, "ระดับ 10", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 39, "ชำนาญงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 40, "อาวุโส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 41, "ชำนาญการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 42, "ชำนาญการพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 43, "เชี่ยวชาญ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 44, "อำนวยการต้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 45, "อำนวยการสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 46, "บริหารต้น", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 47, "บริหารสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 48, "หมายเหตุตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 49, "เลขทะเบียนสมาชิกสหกรณ์", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 50, "วันที่เป็นสมาชิก กบข./กสจ.", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 51, "รุ่นที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				if (in_array(2, $search_per_status)) { 
					$worksheet->write($xlsRow, 52, "เหตุที่พ้นจากส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
					$worksheet->write($xlsRow, 53, "วันที่มีผล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
					$worksheet->write($xlsRow, 54, "สาเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
					$worksheet->write($xlsRow, 55, "ส่วนราชการที่โอนไป", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				}
			} else {
				$worksheet->set_column(29, 29, 25);
				$worksheet->set_column(30, 30, 25);
				$worksheet->set_column(31, 31, 25);
				$worksheet->set_column(32, 32, 22);
				$worksheet->set_column(33, 33, 30);
				$worksheet->set_column(34, 34, 30);
				$worksheet->set_column(35, 35, 30);
				$worksheet->set_column(36, 36, 10);
				$worksheet->set_column(37, 37, 15);
				
				$worksheet->write($xlsRow, 29, "ตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 30, "ตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 31, "ช่วงระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 32, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 33, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 34, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 35, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 36, "รหัสจังหวัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 37, "วันที่ครองเลขถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			}
		}
	} // end function

	if(!isset($search_per_type)) $search_per_type = 1;
	if(!isset($search_per_status) && $command != "SEARCH") $search_per_status = array(1);
	if(!isset($search_per_gender) && $command != "SEARCH") $search_per_gender = array(1, 2);
	if(!isset($search_per_member) && $command != "SEARCH") $search_per_member = array(0, 1);
	if(!isset($search_per_cooperative) && $command != "SEARCH") $search_per_cooperative = array(0, 1);
	if(!isset($search_last_position) && $command != "SEARCH") $search_last_position = array('Y');
	if(!isset($search_last_salary) && $command != "SEARCH") $search_last_salary = array('Y');
	if(!isset($EDU_TYPE) && $command != "SEARCH") $EDU_TYPE = array(1, 2, 3, 4, 5);
	if(!isset($EDU_SHOW) && $command != "SEARCH") $EDU_SHOW = 2;
	if(!isset($POS_ORGMGT) && $command != "SEARCH") $POS_ORGMGT = 1;
	if(!isset($TRN_TYPE) && $command != "SEARCH") $TRN_TYPE = array(1, 2, 3);
	
  	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif($search_ministry_id){
		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	
	/* ================= 	ข้อมูลทั่วไป    ===================== */
	if(trim($search_per_type)) $arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	if(trim($search_pv_code)) $arr_search_condition[] = "(a.PV_CODE='$search_pv_code')";
	if(trim($search_es_code)) $arr_search_condition[] = "(a.ES_CODE='$search_es_code')";
	if(trim($search_per_startyear_min)){
		$search_per_startyear = $search_per_startyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) >= '$search_per_startyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 4) >= '$search_per_startyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) >= '$search_per_startyear')";
	} // end if
	if(trim($search_per_startyear_max)){
		$search_per_startyear = $search_per_startyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) <= '$search_per_startyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 4) <= '$search_per_startyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_STARTDATE, 4) <= '$search_per_startyear')";
	} // end if
	if(trim($search_per_occupyyear_min)){
		$search_per_occupyyear = $search_per_occupyyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) >= '$search_per_occupyyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 4) >= '$search_per_occupyyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) >= '$search_per_occupyyear')";
	} // end if
	if(trim($search_per_occupyyear_max)){
		$search_per_occupyyear = $search_per_occupyyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) <= '$search_per_occupyyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_OCCUPYDATE, 1, 4) <= '$search_per_occupyyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_OCCUPYDATE, 4) <= '$search_per_occupyyear')";
	} // end if
	if(trim($search_per_retireyear_min)){
		$search_per_retireyear = $search_per_retireyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) >= '$search_per_retireyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_RETIREDATE, 1, 4) >= '$search_per_retireyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) >= '$search_per_retireyear')";
	} // end if
	if(trim($search_per_retireyear_max)){
		$search_per_retireyear = $search_per_retireyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) <= '$search_per_retireyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_RETIREDATE, 1, 4) <= '$search_per_retireyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_RETIREDATE, 4) <= '$search_per_retireyear')";
	} // end if
	if(trim($search_per_posyear_min)){
		$search_per_posyear = $search_per_posyear_min - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) >= '$search_per_posyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_POSDATE, 1, 4) >= '$search_per_posyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) >= '$search_per_posyear')";
	} // end if
	if(trim($search_per_posyear_max)){
		$search_per_posyear = $search_per_posyear_max - 543;
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) <= '$search_per_posyear')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(a.PER_POSDATE, 1, 4) <= '$search_per_posyear')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(a.PER_POSDATE, 4) <= '$search_per_posyear')";
	} // end if
	
	$arr_search_condition[] = "(a.PER_GENDER in (". implode(",", $search_per_gender) ."))";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	$arr_search_condition[] = "(a.PER_MEMBER in (". implode(",", $search_per_member) ."))";
	$arr_search_condition[] = "(a.PER_COOPERATIVE in (". implode(",", $search_per_cooperative) ."))";
	if ($POS_ORGMGT==2) 
		if (trim($search_pos_orgmgt))
			$arr_search_condition[] = "(a.PER_POS_ORGMGT like '%$search_pos_orgmgt%')";
		else
			$arr_search_condition[] = "(a.PER_POS_ORGMGT is NULL)";

	/* ================= 	ตำแหน่งปัจจุบัน    ===================== */
  	if(trim($search_pos_no))  {	
		if ($search_per_type == 1)
			$arr_search_condition[] = "(b.POS_NO like '$search_pos_no%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(c.POEM_NO like '$search_pos_no%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(d.POEMS_NO like '$search_pos_no%')";			
	}
	if(trim($search_pl_code)){
		if($search_per_type == 1)
			$arr_search_condition[] = "(b.PL_CODE = '$search_pl_code')";
		elseif($search_per_type == 2)
			$arr_search_condition[] = "(c.PN_CODE = '$search_pl_code')";
		elseif($search_per_type == 3)
			$arr_search_condition[] = "(d.EP_CODE = '$search_pl_code')";
	} // end if
	if(trim($search_pm_code)) $arr_search_condition[] = "(b.PM_CODE = '$search_pm_code')";
	if(trim($search_pt_code)) $arr_search_condition[] = "(b.PT_CODE = '$search_pt_code')";
	if(trim($search_level_no_min)) $arr_search_condition[] = "(trim(a.LEVEL_NO) >= trim('$search_level_no_min'))";
	if(trim($search_level_no_max)) $arr_search_condition[] = "(trim(a.LEVEL_NO) <= trim('$search_level_no_max'))";
	if(trim($search_per_salary_min)) $arr_search_condition[] = "(a.PER_SALARY >= $search_per_salary_min)";
	if(trim($search_per_salary_max)) $arr_search_condition[] = "(a.PER_SALARY <= $search_per_salary_max)";

//	if(trim($search_pos_ot_code)) $arr_search_condition[] = "(b.OT_CODE='$search_pos_ot_code')";
	/* ================= 	ประวัติการดำรงตำแหน่ง    ===================== */
	if(trim($search_poh_effectivedate)){
		$search_poh_effectivedate =  save_date($search_poh_effectivedate);
		if($DPISDB=="odbc") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate')";
		elseif($DPISDB=="oci8") $arr_search_positionhis_condition[] = "(SUBSTR(POH_EFFECTIVEDATE, 1, 10) <= '$search_poh_effectivedate')";
		elseif($DPISDB=="mysql") $arr_search_positionhis_condition[] = "(LEFT(POH_EFFECTIVEDATE, 10) <= '$search_poh_effectivedate')";
		$search_poh_effectivedate = show_date_format($search_poh_effectivedate, 1);
	} // end if
	if(trim($search_poh_es_code)) $arr_search_positionhis_condition[] = "(a.ES_CODE = '$search_poh_es_code')";
	if(trim($search_poh_position_type)=="ทั่วไป") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'O%')";
	elseif(trim($search_poh_position_type)=="วิชาการ") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'K%')";
	elseif(trim($search_poh_position_type)=="อำนวยการ") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'D%')";
	elseif(trim($search_poh_position_type)=="บริหาร") $arr_search_positionhis_condition[] = "(a.LEVEL_NO like 'M%')";
	if(trim($search_poh_pm_code)) $arr_search_positionhis_condition[] = "(b.PM_CODE = '$search_poh_pm_code')";
	if(trim($search_poh_pl_code)){
		if($search_per_type == 1) $arr_search_positionhis_condition[] = "(b.PL_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 2) $arr_search_positionhis_condition[] = "(PN_CODE = '$search_poh_pl_code')";
		elseif($search_per_type == 3) $arr_search_positionhis_condition[] = "(EP_CODE = '$search_poh_pl_code')";
	} // end if
	if(trim($search_poh_cl_name)) $arr_search_positionhis_condition[] = "(b.CL_NAME = '$search_poh_cl_name')";
	if(trim($search_poh_level_no)) $arr_search_positionhis_condition[] = "(a.LEVEL_NO = '$search_poh_level_no')";
	if(trim($search_poh_org_id)) $arr_search_positionhis_condition[] = "(b.ORG_ID = $search_poh_org_id)";
	if(trim($search_per_level_no)) $arr_search_positionhis_condition[] = "(a.POH_LEVEL_NO = '$search_per_level_no')";
	if(trim($search_poh_ot_code)) $arr_search_positionhis_condition[] = "(c.OT_CODE = '$search_poh_ot_code')";
	if(trim($search_pos_org)) $arr_search_positionhis_condition[] = "(a.POH_ORG like '%$search_pos_org%')";
	
	if(count($arr_search_positionhis_condition)){
		if($search_per_type == 1) {
			$table = "PER_POSITION";
			$field = "b.POS_NO";
		} elseif($search_per_type == 2) {
			$table = "PER_POS_EMP";
			$field = "b.POEM_NO";
		} elseif($search_per_type == 3) {
			$table = "PER_POS_EMPSER";
			$field = "b.POEMS_NO";
		}
		$search_position = "";
		for ($i=0;$i<count($search_last_position);$i++) {
			if($search_position) { $search_position.= ' or '; }
			$search_position.= "a.POH_LAST_POSITION = '$search_last_position[$i]' "; 
		}
		if ($search_position) $arr_search_positionhis_condition[] = "(".$search_position.")";
		$cmd = " select distinct PER_ID from PER_POSITIONHIS a, $table b, PER_ORG c where a.POH_POS_NO = $field and b.ORG_ID = c.ORG_ID and ". 
						  implode(" and ", $arr_search_positionhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_positionhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_positionhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_positionhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_positionhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_positionhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_positionhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_positionhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_positionhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_positionhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_positionhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_positionhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_positionhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_positionhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_positionhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_positionhis15[] = $data[PER_ID];
			else $arr_positionhis16[] = $data[PER_ID];
		}
		
		if (count($arr_positionhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")) or (a.PER_ID in (". implode(",", $arr_positionhis16) .")))";
		elseif (count($arr_positionhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis15) .")))";
		elseif (count($arr_positionhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")) or (a.PER_ID in (". implode(",", $arr_positionhis14) .")))";
		elseif (count($arr_positionhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis13) .")))";
		elseif (count($arr_positionhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")) or (a.PER_ID in (". implode(",", $arr_positionhis12) .")))";
		elseif (count($arr_positionhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis11) .")))";
		elseif (count($arr_positionhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")) or (a.PER_ID in (". implode(",", $arr_positionhis10) .")))";
		elseif (count($arr_positionhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis9) .")))";
		elseif (count($arr_positionhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")) or (a.PER_ID in (". implode(",", $arr_positionhis8) .")))";
		elseif (count($arr_positionhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_positionhis7) .")))";
		elseif (count($arr_positionhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")) or (a.PER_ID in (". implode(",", $arr_positionhis6) .")))";
		elseif (count($arr_positionhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis5) .")))";
		elseif (count($arr_positionhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")) or (a.PER_ID in (". implode(",", $arr_positionhis4) .")))";
		elseif (count($arr_positionhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_positionhis3) .")))";
		elseif (count($arr_positionhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_positionhis) .")) or (a.PER_ID in (". implode(",", $arr_positionhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_positionhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการรับเงินเดือน    ===================== */
	if(trim($search_sah_effectivedate)){
		$search_sah_effectivedate =  save_date($search_sah_effectivedate);
		if($DPISDB=="odbc") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate')";
		elseif($DPISDB=="oci8") $arr_search_salaryhis_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) <= '$search_sah_effectivedate')";
		elseif($DPISDB=="mysql") $arr_search_salaryhis_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_sah_effectivedate')";
		$search_sah_effectivedate = show_date_format($search_sah_effectivedate, 1);
	} // end if
	if(trim($search_sah_position_type)=="ทั่วไป") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'O%')";
	elseif(trim($search_sah_position_type)=="วิชาการ") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'K%')";
	elseif(trim($search_sah_position_type)=="อำนวยการ") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'D%')";
	elseif(trim($search_sah_position_type)=="บริหาร") $arr_search_salaryhis_condition[] = "(b.LEVEL_NO like 'M%')";
	if(trim($search_sah_pm_code)) $arr_search_salaryhis_condition[] = "(b.PM_CODE = '$search_sah_pm_code')";
	if(trim($search_sah_pl_code)){
		if($search_per_type == 1) $arr_search_salaryhis_condition[] = "(b.PL_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 2) $arr_search_salaryhis_condition[] = "(PN_CODE = '$search_sah_pl_code')";
		elseif($search_per_type == 3) $arr_search_salaryhis_condition[] = "(EP_CODE = '$search_sah_pl_code')";
	} // end if
	if(trim($search_sah_cl_name)) $arr_search_positionhis_condition[] = "(b.CL_NAME = '$search_sah_cl_name')";
	if(trim($search_sah_org_id)) $arr_search_salaryhis_condition[] = "(b.ORG_ID = $search_sah_org_id)";
	if(trim($search_sah_level_no)) $arr_search_salaryhis_condition[] = "(b.LEVEL_NO = '$search_sah_level_no')";
	if(trim($search_sah_salary_min)) $arr_search_salaryhis_condition[] = "(SAH_SALARY >= $search_sah_salary_min)";
	if(trim($search_sah_salary_max)) $arr_search_salaryhis_condition[] = "(SAH_SALARY <= $search_sah_salary_max)";
	if(trim($search_sah_ot_code)) $arr_search_positionhis_condition[] = "(c.OT_CODE = '$search_sah_ot_code')";
	
	if(count($arr_search_salaryhis_condition)){
		if($search_per_type == 1) {
			$table = "PER_POSITION";
			$field = "b.POS_NO";
		} elseif($search_per_type == 2) {
			$table = "PER_POS_EMP";
			$field = "b.POEM_NO";
		} elseif($search_per_type == 3) {
			$table = "PER_POS_EMPSER";
			$field = "b.POEMS_NO";
		}
		$search_salary = "";
		for ($i=0;$i<count($search_last_salary);$i++) {
			if($search_salary) { $search_salary.= ' or '; }
			$search_salary.= "a.SAH_LAST_SALARY = '$search_last_salary[$i]' "; 
		} 
		if ($search_salary) $arr_search_salaryhis_condition[] = "(".$search_salary.")";
		$cmd = " select distinct PER_ID from PER_SALARYHIS a, $table b, PER_ORG c where a.SAH_PAY_NO = $field and b.ORG_ID = c.ORG_ID and ". 
						  implode(" and ", $arr_search_salaryhis_condition) ." order by PER_ID";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_salaryhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_salaryhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_salaryhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_salaryhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_salaryhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_salaryhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_salaryhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_salaryhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_salaryhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_salaryhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_salaryhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_salaryhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_salaryhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_salaryhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_salaryhis15[] = $data[PER_ID];
			elseif ($count < 16000) $arr_salaryhis16[] = $data[PER_ID];
			elseif ($count < 17000) $arr_salaryhis17[] = $data[PER_ID];
			elseif ($count < 18000) $arr_salaryhis18[] = $data[PER_ID];
			elseif ($count < 19000) $arr_salaryhis19[] = $data[PER_ID];
			else $arr_salaryhis20[] = $data[PER_ID];
		}

		if (count($arr_salaryhis20)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis20) .")))";
		elseif (count($arr_salaryhis19)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis19) .")))";
		elseif (count($arr_salaryhis18)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis18) .")))";
		elseif (count($arr_salaryhis17)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis17) .")))";
		elseif (count($arr_salaryhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis16) .")))";
		elseif (count($arr_salaryhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis15) .")))";
		elseif (count($arr_salaryhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis14) .")))";
		elseif (count($arr_salaryhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis13) .")))";
		elseif (count($arr_salaryhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis12) .")))";
		elseif (count($arr_salaryhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis11) .")))";
		elseif (count($arr_salaryhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis10) .")))";
		elseif (count($arr_salaryhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis9) .")))";
		elseif (count($arr_salaryhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis8) .")))";
		elseif (count($arr_salaryhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_salaryhis7) .")))";
		elseif (count($arr_salaryhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis6) .")))";
		elseif (count($arr_salaryhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis5) .")))";
		elseif (count($arr_salaryhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis4) .")))";
		elseif (count($arr_salaryhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_salaryhis3) .")))";
		elseif (count($arr_salaryhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_salaryhis) .")) or (a.PER_ID in (". implode(",", $arr_salaryhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_salaryhis) ."))";
	} // end if
	
	/* ================= 	ประวัติการศึกษา    ===================== */
	$search_edu = "";
	if(trim($search_el_code)) $arr_search_educatehis_condition[] = "(trim(a.EL_CODE) = trim('$search_el_code'))";
	if(trim($search_en_code)) $arr_search_educatehis_condition[] = "(a.EN_CODE = '$search_en_code')";
	if(trim($search_em_code)) $arr_search_educatehis_condition[] = "(a.EM_CODE = '$search_em_code')";
	if(trim($search_ins_code)) $arr_search_educatehis_condition[] = "(a.INS_CODE = '$search_ins_code')";
	if(trim($search_ins_ct_code)) $arr_search_educatehis_condition[] = "(b.CT_CODE = '$search_ins_ct_code')";

	if(count($arr_search_educatehis_condition)){
		for ($i=0;$i<count($EDU_TYPE);$i++) {
			if($search_edu) { $search_edu.= ' or '; }
			$search_edu.= "a.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
		} 
		if ($search_edu) $arr_search_educatehis_condition[] = "(".$search_edu.")";
		$cmd = " select 	distinct a.PER_ID 
						 from 		PER_EDUCATE a, PER_INSTITUTE b
						 where 	a.INS_CODE=b.INS_CODE(+)
						 				and ". implode(" and ", $arr_search_educatehis_condition) ." 
						 order by a.PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_educatehis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_educatehis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_educatehis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_educatehis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_educatehis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_educatehis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_educatehis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_educatehis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_educatehis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_educatehis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_educatehis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_educatehis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_educatehis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_educatehis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_educatehis15[] = $data[PER_ID];
			else $arr_educatehis16[] = $data[PER_ID];
		}
		
		if (count($arr_educatehis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")) or (a.PER_ID in (". implode(",", $arr_educatehis16) .")))";
		elseif (count($arr_educatehis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis15) .")))";
		elseif (count($arr_educatehis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")) or (a.PER_ID in (". implode(",", $arr_educatehis14) .")))";
		elseif (count($arr_educatehis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis13) .")))";
		elseif (count($arr_educatehis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")) or (a.PER_ID in (". implode(",", $arr_educatehis12) .")))";
		elseif (count($arr_educatehis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis11) .")))";
		elseif (count($arr_educatehis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")) or (a.PER_ID in (". implode(",", $arr_educatehis10) .")))";
		elseif (count($arr_educatehis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis9) .")))";
		elseif (count($arr_educatehis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")) or (a.PER_ID in (". implode(",", $arr_educatehis8) .")))";
		elseif (count($arr_educatehis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_educatehis7) .")))";
		elseif (count($arr_educatehis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")) or (a.PER_ID in (". implode(",", $arr_educatehis6) .")))";
		elseif (count($arr_educatehis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis5) .")))";
		elseif (count($arr_educatehis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")) or (a.PER_ID in (". implode(",", $arr_educatehis4) .")))";
		elseif (count($arr_educatehis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_educatehis3) .")))";
		elseif (count($arr_educatehis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_educatehis) .")) or (a.PER_ID in (". implode(",", $arr_educatehis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_educatehis) ."))";
	} // end if

	/* ================= 	ประวัติการอบรม/ดูงาน    ===================== */
	$search_trn = "";
	if(trim($search_tr_code)) $arr_search_trainhis_condition[] = "(TR_CODE = '$search_tr_code')";
	if(trim($search_trn_no)) $arr_search_trainhis_condition[] = "(TRN_NO = '$search_trn_no')";
	if(trim($search_trn_org)) $arr_search_trainhis_condition[] = "(TRN_ORG like '$search_trn_org%')";
	if(trim($search_tr_ct_code)) $arr_search_trainhis_condition[] = "(CT_CODE = '$search_tr_ct_code')";

	if(count($arr_search_trainhis_condition)){
		for ($i=0;$i<count($TRN_TYPE);$i++) {
			if($search_trn) { $search_trn.= ' or '; }
			$search_trn.= "TRN_TYPE like '%$TRN_TYPE[$i]%' "; 
		} 
		if ($search_trn) $arr_search_trainhis_condition[] = "(".$search_trn.")";
		$cmd = " select distinct PER_ID from PER_TRAINING where ". implode(" and ", $arr_search_trainhis_condition) ." order by PER_ID";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$count = 0;
		while($data = $db_dpis->get_array()) { 
			$count++;
			if ($count < 1000) $arr_trainhis[] = $data[PER_ID];
			elseif ($count < 2000) $arr_trainhis2[] = $data[PER_ID];
			elseif ($count < 3000) $arr_trainhis3[] = $data[PER_ID];
			elseif ($count < 4000) $arr_trainhis4[] = $data[PER_ID];
			elseif ($count < 5000) $arr_trainhis5[] = $data[PER_ID];
			elseif ($count < 6000) $arr_trainhis6[] = $data[PER_ID];
			elseif ($count < 7000) $arr_trainhis7[] = $data[PER_ID];
			elseif ($count < 8000) $arr_trainhis8[] = $data[PER_ID];
			elseif ($count < 9000) $arr_trainhis9[] = $data[PER_ID];
			elseif ($count < 10000) $arr_trainhis10[] = $data[PER_ID];
			elseif ($count < 11000) $arr_trainhis11[] = $data[PER_ID];
			elseif ($count < 12000) $arr_trainhis12[] = $data[PER_ID];
			elseif ($count < 13000) $arr_trainhis13[] = $data[PER_ID];
			elseif ($count < 14000) $arr_trainhis14[] = $data[PER_ID];
			elseif ($count < 15000) $arr_trainhis15[] = $data[PER_ID];
			else $arr_trainhis16[] = $data[PER_ID];
		}
		
		if (count($arr_trainhis16)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")) or (a.PER_ID in (". implode(",", $arr_trainhis16) .")))";
		elseif (count($arr_trainhis15)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis15) .")))";
		elseif (count($arr_trainhis14)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")) or (a.PER_ID in (". implode(",", $arr_trainhis14) .")))";
		elseif (count($arr_trainhis13)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis13) .")))";
		elseif (count($arr_trainhis12)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")) or (a.PER_ID in (". implode(",", $arr_trainhis12) .")))";
		elseif (count($arr_trainhis11)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis11) .")))";
		elseif (count($arr_trainhis10)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")) or (a.PER_ID in (". implode(",", $arr_trainhis10) .")))";
		elseif (count($arr_trainhis9)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis9) .")))";
		elseif (count($arr_trainhis8)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")) or (a.PER_ID in (". implode(",", $arr_trainhis8) .")))";
		elseif (count($arr_trainhis7)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")) or 
																													(a.PER_ID in (". implode(",", $arr_trainhis7) .")))";
		elseif (count($arr_trainhis6)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")) or (a.PER_ID in (". implode(",", $arr_trainhis6) .")))";
		elseif (count($arr_trainhis5)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis5) .")))";
		elseif (count($arr_trainhis4)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")) or (a.PER_ID in (". implode(",", $arr_trainhis4) .")))";
		elseif (count($arr_trainhis3)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")) or 
																															(a.PER_ID in (". implode(",", $arr_trainhis3) .")))";
		elseif (count($arr_trainhis2)) $arr_search_condition[] = "((a.PER_ID in (". implode(",", $arr_trainhis) .")) or (a.PER_ID in (". implode(",", $arr_trainhis2) .")))";
		else $arr_search_condition[] = "(a.PER_ID in (". implode(",", $arr_trainhis) ."))";
	} // end if

	/* ======================================================== */
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
	if($DPISDB=="oci8") $search_condition = str_replace(" where ", " and ", $search_condition);

	if($DPISDB=="odbc"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO as PER_LEVEL_NO, a.PER_TYPE, 
											a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, 
											a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID, b.LEVEL_NO, b.CL_NAME,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	
							 						PER_PERSONAL a
													left join PER_POSITION b on (a.POS_ID=b.POS_ID)	) 
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
											a.POEM_ID as POS_ID, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, 
											c.ORG_ID_1, c.ORG_ID_2, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, 
											a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	PER_PERSONAL a
												left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	)
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
											a.POEMS_ID as POS_ID, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, 
											d.ORG_ID_1, d.ORG_ID_2,  a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE,
											a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	PER_PERSONAL a
												left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	)
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd);  }
		} // end if
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		if($search_per_type==1){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO as PER_LEVEL_NO, a.PER_TYPE, 
							  					a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
												a.PER_SALARY, a.PER_MGTSALARY, a.PER_RETIREDATE, a.PER_BIRTHDATE, 
												a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID, b.LEVEL_NO, b.CL_NAME,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 			PER_PERSONAL a, PER_POSITION b
							 where 		a.POS_ID=b.POS_ID(+)
												$search_condition
							 order by 	PER_NAME, PER_SURNAME ";
			 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==2){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
												a.POEM_ID as POS_ID, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, 
												c.ORG_ID_1, c.ORG_ID_2, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, 
												a.PER_RETIREDATE,a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 			PER_PERSONAL a, PER_POS_EMP c 
							 where 		a.POEM_ID=c.POEM_ID(+)
												$search_condition
							 order by 	PER_NAME, PER_SURNAME ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==3){
			$cmd = " select 		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
												a.POEMS_ID as POS_ID, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, 
												d.ORG_ID_1, d.ORG_ID_2, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, 
												a.PER_RETIREDATE,a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 			PER_PERSONAL a, PER_POS_EMPSER d 
							 where 		a.POEMS_ID=d.POEMS_ID(+) 
												$search_condition
							 order by 	PER_NAME, PER_SURNAME ";
			 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd);  }
		} // end if
	}elseif($DPISDB=="mysql"){	
		if($search_per_type==1){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO as PER_LEVEL_NO, a.PER_TYPE, 
											a.POS_ID, b.POS_NO, b.PL_CODE, b.PM_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.PT_CODE, a.PER_CARDNO,
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, a.PER_RETIREDATE, 
												a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID, b.LEVEL_NO, b.CL_NAME,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	PER_PERSONAL a
												left join PER_POSITION b on (a.POS_ID=b.POS_ID)	)
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==2){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
											a.POEM_ID as POS_ID, c.POEM_NO as POS_NO, c.PN_CODE as PL_CODE, c.ORG_ID, 
											c.ORG_ID_1, c.ORG_ID_2, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, 
											a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	PER_PERSONAL a
												left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)	)
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);  }
		}elseif($search_per_type==3){
			$cmd = " select 	a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_TYPE, 
											a.POEMS_ID as POS_ID, d.POEMS_NO as POS_NO, d.EP_CODE as PL_CODE, d.ORG_ID, 
											d.ORG_ID_1, d.ORG_ID_2, a.PER_CARDNO,a.PER_SALARY, a.PER_MGTSALARY, a.PER_BIRTHDATE, 
											a.PER_RETIREDATE, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.PER_OFFNO, a.PER_GENDER, a.PAY_ID,
											a.PER_COOPERATIVE_NO, a.PER_MEMBERDATE, a.MOV_CODE, a.PER_EFFECTIVEDATE, a.PER_POS_REASON, a.PER_POS_ORG, a.PER_POS_ORGMGT
							 from 		(	PER_PERSONAL a
												left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)	)
							$search_condition
							 order by PER_NAME, PER_SURNAME  ";
				 if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd);  }
		} // end if
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo $cmd;

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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($report_type==1) {
			$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if (in_array(2, $search_per_status)) { 
				$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		}

		print_header(1);
		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PER_ID = trim($data[PER_ID]);
			$PER_LEVEL_NO = trim($data[PER_LEVEL_NO]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			if (!$PER_LEVEL_NO) $PER_LEVEL_NO = $LEVEL_NO;
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$PER_SALARY = trim($data[PER_SALARY]);
			$PER_MGTSALARY = trim($data[PER_MGTSALARY]);
			$CARDNO = $data[PER_CARDNO];
			$OFFNO = $data[PER_OFFNO];
			$GENDER = $data[PER_GENDER];
			if ($GENDER==1) $GENDER = "ชาย";
			else $GENDER = "หญิง";
			$PAY_ID = $data[PAY_ID];
			$PER_TYPE = $data[PER_TYPE];
			$CL_NAME = trim($data[CL_NAME]);
			$COOPERATIVE_NO = trim($data[PER_COOPERATIVE_NO]);
			$POS_REASON = trim($data[PER_POS_REASON]);
			$POS_ORG = trim($data[PER_POS_ORG]);
			$POS_ORGMGT = trim($data[PER_POS_ORGMGT]);
			if (trim($data[PER_RETIREDATE])) {	
				$tmp_date = explode("-", substr(trim($data[PER_RETIREDATE]), 0, 10));
				$RETIREYEAR = ($tmp_date[0] + 543);
			}
			$BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			$OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);
			$MEMBERDATE = show_date_format($data[PER_MEMBERDATE],$DATE_DISPLAY);
			$EFFECTIVEDATE = show_date_format($data[PER_EFFECTIVEDATE],$DATE_DISPLAY);
			
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data2=$db_dpis2->get_array();
			$LEVEL_NAME=trim($data2[LEVEL_NAME]);
			if(strstr($LEVEL_NAME, 'ประเภท') == TRUE) {
				$LEVEL_NAME = str_replace("ประเภท", "", $LEVEL_NAME);
			}
			
			$cmd = "select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$PER_LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data2=$db_dpis2->get_array();
			$PER_LEVEL_NAME=trim($data2[POSITION_LEVEL]);
			
			if($DPISDB=="odbc") {
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
								  from	(
												(
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.en_code=b.en_code)	
													)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
												)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
											)left join PER_EDUCMAJOR f on (a.EM_CODE=f.EM_CODE)
									where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%$EDU_SHOW%'	"	;
				$ed =	$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME = trim($data4[EN_NAME]);
				$INS_NAME = trim($data4[INS_NAME]);
				$EL_NAME = trim($data4[EL_NAME]);
				$EM_NAME = trim($data4[EM_NAME]);
				if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
				$EDU_REMARK = trim($data4[EDU_REMARK]);
		
				if($ed<=0) {
					$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
						  from	(
										(
											(
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.en_code=b.en_code)	
											)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
										)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
									)left join PER_EDUCMAJOR f on (a.EM_CODE=f.EM_CODE)
							where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'	";
								
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
	//				echo $cmd."<hr>";
					$data4 = $db_dpis2->get_array();
					$EN_NAME = trim($data4[EN_NAME]);
					$INS_NAME = trim($data4[INS_NAME]);
					$EL_NAME = trim($data4[EL_NAME]);		
					$EM_NAME = trim($data4[EM_NAME]);
					if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
					$EDU_REMARK = trim($data4[EDU_REMARK]);
				} 
			}elseif($DPISDB=="oci8"){
		
				$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
								from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR f
								where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%$EDU_SHOW%' and a.en_code=b.en_code(+) and 
									a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=f.EM_CODE(+) ";
								
				$ed =	$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME = trim($data4[EN_NAME]);
				$INS_NAME = trim($data4[INS_NAME]);
				$EL_NAME = trim($data4[EL_NAME]);
				$EM_NAME = trim($data4[EM_NAME]);
				if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
				$EDU_REMARK = trim($data4[EDU_REMARK]);
		
				if($ed<=0) {
					$cmd = "select   b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
								from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_INSTITUTE d, PER_EDUCMAJOR f
								where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%' and a.en_code=b.en_code(+) and 
									a.ins_code=d.ins_code(+) and b.EL_CODE=c.EL_CODE(+) and a.EM_CODE=f.EM_CODE(+) ";
								
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
	//				echo $cmd."<hr>";
					$data4 = $db_dpis2->get_array();
					$EN_NAME = trim($data4[EN_NAME]);
					$INS_NAME = trim($data4[INS_NAME]);
					$EL_NAME = trim($data4[EL_NAME]);		
					$EM_NAME = trim($data4[EM_NAME]);
					if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
					$EDU_REMARK = trim($data4[EDU_REMARK]);
				} 
		
			}elseif($DPISDB=="mysql"){
		
				$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
						  from	(
										(
											(
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.en_code=b.en_code)	
											)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
										)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
									)left join PER_EDUCMAJOR f on (a.EM_CODE=f.EM_CODE)
							where  a.PER_ID=$PER_ID and a.EDU_TYPE like '%$EDU_SHOW%'	"	;
								
				$ed =	$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo $cmd."<hr>";
				$data4 = $db_dpis2->get_array();
				$EN_NAME = trim($data4[EN_NAME]);
				$INS_NAME = trim($data4[INS_NAME]);
				$EL_NAME = trim($data4[EL_NAME]);
				$EM_NAME = trim($data4[EM_NAME]);
				if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
				$EDU_REMARK = trim($data4[EDU_REMARK]);
		
				if($ed<=0) {
					$cmd = "select b.EN_NAME, d.INS_NAME, c.EL_NAME, f.EM_NAME, EDU_INSTITUTE, EDU_REMARK
						  from	(
										(
											(
												PER_EDUCATE a
												left join PER_EDUCNAME b on (a.en_code=b.en_code)	
											)left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
										)left join PER_INSTITUTE d on (a.ins_code=d.ins_code)
									)left join PER_EDUCMAJOR f on (a.EM_CODE=f.EM_CODE)
							where  a.PER_ID='$PER_ID' and a.EDU_TYPE like '%2%'	"	;
								
					$db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
	//				echo $cmd."<hr>";
					$data4 = $db_dpis2->get_array();
					$EN_NAME = trim($data4[EN_NAME]);
					$INS_NAME = trim($data4[INS_NAME]);
					$EL_NAME = trim($data4[EL_NAME]);		
					$EM_NAME = trim($data4[EM_NAME]);
					if (!$INS_NAME) $INS_NAME = trim($data4[EDU_INSTITUTE]);
					$EDU_REMARK = trim($data4[EDU_REMARK]);
				} 		
			}
		
			$PN_CODE = trim($data[PN_CODE]);
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}
					
			$MOV_CODE = trim($data[MOV_CODE]);
			if ($MOV_CODE) {
				$cmd = "	select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MOV_NAME = $data2[MOV_NAME];
			}
					
			if ($PER_TYPE == 1) {
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = $data[PL_CODE];
				$PM_CODE = $data[PM_CODE];
				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				
				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = $data2[PM_NAME];
				if (!$PM_NAME) $PM_NAME = $PL_NAME;
				
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);

				//$PL_NAME = (trim($PL_NAME))? ("$PL_NAME ". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"")) : "".$LEVEL_NAME;
				$PL_NAME = (trim($PL_NAME))? ("$PL_NAME ". (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"")) : "";
				
			} elseif ($PER_TYPE == 2) {
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = $data[PL_CODE];
				$cmd = " select PN_NAME as PL_NAME from PER_POS_NAME where PN_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME " : "";
				
			} elseif ($PER_TYPE == 3) {
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = $data[PL_CODE];
				$cmd = " select EP_NAME as PL_NAME from PER_EMPSER_POS_NAME where EP_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
				$PL_NAME = (trim($PL_NAME))? "$PL_NAME ": "";
				
			}		

			$ORG_ID = $data[ORG_ID];
			$cmd = " select ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
			$DOPA_CODE = $data2[ORG_DOPA_CODE];
			
			$ORG_NAME_1 = $ORG_NAME_2 = "";
			$ORG_ID_1 = $data[ORG_ID_1];
			if ($ORG_ID_1) {
				$cmd = " select ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data2[ORG_NAME];
				$DOPA_CODE = $data2[ORG_DOPA_CODE];
			}
			
			$ORG_ID_2 = $data[ORG_ID_2];
			if ($ORG_ID_2) {
				$cmd = " select ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID=$ORG_ID_2 ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_2 = $data2[ORG_NAME];
				$DOPA_CODE = $data2[ORG_DOPA_CODE];
			}
				
			$cmd = " select POH_ORG
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and POH_LAST_POSITION='Y' and (ES_CODE='05' or ES_CODE='06') ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG = trim($data2[POH_ORG]);

			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=3
							order by POH_EFFECTIVEDATE DESC ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_UP_DATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);

			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and POH_POS_NO='$POS_NO'
							order by POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_DATE = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);
			if ($PER_TYPE=="1") {
				if ($PAY_ID) {			
					$cmd = " select 	POS_NO 
							from 	PER_POSITION where POS_ID=$PAY_ID  ";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$PAY_NO = trim($data_dpis2[POS_NO]);
				}
	
				if($DPISDB=="odbc"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a, PER_PERSONAL b
										where	a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);

				$cmd = " select SAH_PAY_NO, SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID_PAY
								order by SAH_EFFECTIVEDATE DESC ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					$SAH_PAY_NO = trim($data2[SAH_PAY_NO]);
					if ($SAH_PAY_NO==$PAY_NO) {
						$PAY_DATE = show_date_format($data2[SAH_EFFECTIVEDATE],$DATE_DISPLAY);
					} else break;
				}
	
				$cmd = " select PM_CODE, PL_CODE, CL_NAME, LEVEL_NO, ORG_ID, ORG_ID_1, ORG_ID_2
								from   PER_POSITION
								where POS_NO='$PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
	
				$data2 = $db_dpis2->get_array();
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
				$PAY_CL_NAME = trim($data2[CL_NAME]);
				$PAY_LEVEL_NO = trim($data2[LEVEL_NO]);
				$ORG_ID = $data2[ORG_ID];
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];

				$cmd = " select OT_CODE, ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID = $ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PAY_OT_CODE = trim($data2[OT_CODE]);
				$PAY_ORG_NAME = trim($data2[ORG_NAME]);
				$PAY_DOPA_CODE = trim($data2[ORG_DOPA_CODE]);
			
				$PAY_ORG_NAME_1 = $PAY_ORG_NAME_2 = "";
				if ($ORG_ID_1) {
					$cmd = " select ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID = $ORG_ID_1 ";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PAY_ORG_NAME_1 = trim($data2[ORG_NAME]);
					$PAY_DOPA_CODE = trim($data2[ORG_DOPA_CODE]);
				}

				if ($ORG_ID_2) {
					$cmd = " select ORG_NAME, ORG_DOPA_CODE from PER_ORG where ORG_ID = $ORG_ID_2 ";
					if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PAY_ORG_NAME_2 = trim($data2[ORG_NAME]);
					$PAY_DOPA_CODE = trim($data2[ORG_DOPA_CODE]);
				}

//				$PAY_ORG_NAME = trim($PAY_ORG_NAME_2." ".$PAY_ORG_NAME_1." ".$PAY_ORG_NAME);
//				if ($PAY_OT_CODE == "01" && $DEPARTMENT_NAME=="กรมการปกครอง") $PAY_ORG_NAME = trim($PAY_ORG_NAME." ".$DEPARTMENT_NAME);

				$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_PM_NAME = trim($data2[PM_NAME]);
				if (!$PAY_PM_NAME) $PAY_PM_NAME = $PAY_PL_NAME;

				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$PAY_LEVEL_NO' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_LEVEL_NAME = trim($data2[LEVEL_NAME]);
				if(strstr($PAY_LEVEL_NAME, 'ประเภท') == TRUE) {
					$PAY_LEVEL_NAME = str_replace("ประเภท", "", $PAY_LEVEL_NAME);
				}
			} // endif ($PER_TYPE=="1") 

			// === หาวันที่มีผล
			$level = array("02", "03", "04", "05", "06", "07", "08", "09", "10", "O2", "O3", "K2", "K3", "K4", "D1", "D2", "M1", "M2");
			for ( $i=0; $i<count($level); $i++ ) { 
				$LEVEL_DATE[$level[$i]] = "";
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and LEVEL_NO='$level[$i]'
								order by POH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LEVEL_DATE[$level[$i]] = show_date_format($data2[POH_EFFECTIVEDATE],$DATE_DISPLAY);
			} // end for
						
			$cmd = "select TRN_NO from PER_TRAINING where PER_ID=$PER_ID and TR_CODE = '$search_tr_code' ";
			$db_dpis2->send_cmd($cmd);
			$data2=$db_dpis2->get_array();
			$TRN_NO=trim($data2[TRN_NO]);
			
			$xlsRow = $data_count;
			if ($report_type==3) {
				$worksheet->write_string($xlsRow, 0, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2,"$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$PER_LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$PAY_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$POH_ORG", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "-------", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "--------", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "---------", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} else if($report_type==4){
				$worksheet->write_string($xlsRow, 0, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "-----------", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$TRN_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$PAY_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10,"$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11,"-----------", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12,"$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13,"$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14,"$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15,"$POS_REASON", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} else{
				$worksheet->write_string($xlsRow, 0, $data_count-1, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2,card_no_format($CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$OFFNO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$GENDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 7, "$OCCUPYDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 8, "$POS_UP_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 9, "$RETIREYEAR", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 10, "$PER_LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 11, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 12, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 13, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 14, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 15, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 16, "$EDU_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 17, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 18, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 19, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 20, "$CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 21, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 22, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 23, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 24, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 25, "$DOPA_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 26, "$POH_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 27, "$POH_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 28, "$PAY_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				if ($report_type==1) {
					$worksheet->write_string($xlsRow, 29, "$STARTDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 30, "$LEVEL_DATE[02]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 31, "$LEVEL_DATE[03]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 32, "$LEVEL_DATE[04]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 33, "$LEVEL_DATE[05]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 34, "$LEVEL_DATE[06]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 35, "$LEVEL_DATE[07]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 36, "$LEVEL_DATE[08]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 37, "$LEVEL_DATE[09]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 38, "$LEVEL_DATE[10]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 39, "$LEVEL_DATE[O2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 40, "$LEVEL_DATE[O3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 41, "$LEVEL_DATE[K2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 42, "$LEVEL_DATE[K3]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 43, "$LEVEL_DATE[K4]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 44, "$LEVEL_DATE[D1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 45, "$LEVEL_DATE[D2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 46, "$LEVEL_DATE[M1]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 47, "$LEVEL_DATE[M2]", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 48, "$POS_ORGMGT", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 49, "$COOPERATIVE_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 50, "$MEMBERDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 51, "$TRN_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					if (in_array(2, $search_per_status)) { 
						$worksheet->write_string($xlsRow, 52, "$MOV_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 53, "$EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 54, "$POS_REASON", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 55, "$POS_ORG", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}
				} else {
					$worksheet->write_string($xlsRow, 29, "$PAY_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 30, "$PAY_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 31, "$PAY_CL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 32, "$PAY_LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 33, "$PAY_ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 34, "$PAY_ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 35, "$PAY_ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 36, "$PAY_DOPA_CODE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 37, "$PAY_DATE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				}  
			} //end else
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 31, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 32, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 33, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 34, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 35, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 36, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 37, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($report_type==1) {
			$worksheet->write($xlsRow, 38, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 39, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 40, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 41, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 42, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 43, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 44, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 45, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 46, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 47, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 48, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 49, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 50, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 51, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if (in_array(2, $search_per_status)) { 
				$worksheet->write($xlsRow, 52, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 53, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 54, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 55, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} // end if
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