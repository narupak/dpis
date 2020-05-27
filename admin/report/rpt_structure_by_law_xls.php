<?	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 50; $header_text[0] = "ชื่อส่วนราชการ"; 	//	ORG_NAME;
	$header_width[1] = 25; $header_text[1] = "รหัสส่วนราชการ"; 
	$header_width[2] = 25; $header_text[2] = "ฐานะของหน่วยงาน";
	$header_width[3] = 25; $header_text[3] = "สังกัด"; 	
	$header_width[4] = 25; $header_text[4] = "สถานภาพของหน่วยงาน"; 	
	$header_width[5] = 25; $header_text[5] = "ประเทศ";
	$header_width[6] = 25; $header_text[6] = "จังหวัด";			

	require_once("excel_headpart_subrtn.php");

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_STRUCTURE_BY_LAW";

//	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/$report_code";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnum_text="";

	$arr_file = (array) null;
	$f_new = false;

	$workbook = new writeexcel_workbook($fname1);

//	echo "$data_count>>fname=$fname1<br>";

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$head = "โครงสร้างตามกฏหมาย";

//	echo "$ORG_ID:$search_ol_code<br>";
	if (!$ORG_ID1)  {
		if ($SESS_ORG_ID) { $ORG_ID1=$SESS_ORG_ID; $search_ol_code="03"; }
		elseif ($SESS_DEPARTMENT_ID) { $ORG_ID1=$SESS_DEPARTMENT_ID; $search_ol_code="02"; }
		elseif ($SESS_MINISTRY_ID) { $ORG_ID1=$SESS_MINISTRY_ID; $search_ol_code="01"; }
		else { $ORG_ID1=0; $search_ol_code="01"; }
//		echo "not ORG_ID1:$ORG_ID1:$search_ol_code<br>";
	}

//	$UPDATE_DATE = date("Y-m-d H:i:s");

	$arr_data = (array) null;
	$data_count = 0;
	$count_data = get_org($ORG_ID1, 0);
	if ($count_data) {
		$cmd = " select ORG_CODE, ORG_NAME, OL_CODE, OT_CODE, OS_CODE, CT_CODE, PV_CODE from PER_ORG where ORG_ID=$ORG_ID1 order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$data_count = 0;
		call_header($data_count, $head);
		
	$OL_CODE = $data[OL_CODE];
	$OL_NAME = "";
	if($OL_CODE){
		$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE='$OL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OL_NAME = $data2[OL_NAME];
	} // end if

	$OT_CODE = $data[OT_CODE];
	$OT_NAME = "";
	if($OT_CODE){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];
	} // end if

	$OS_CODE = $data[OS_CODE];
	$OS_NAME = "";
	if($OS_CODE){
		$cmd = " select OS_NAME from PER_ORG_STAT where OS_CODE='$OS_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OS_NAME = $data2[OS_NAME];
	} // end if

	$CT_CODE = $data[CT_CODE];
	$CT_NAME = "";
	if($CT_CODE){
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
	} // end if

	$PV_CODE1 = $data[PV_CODE];
	$PV_NAME1 = "";
	if($PV_CODE1){
		$cmd = " select PV_NAME from PER_PROVINCE where CT_CODE='$CT_CODE' and PV_CODE='$PV_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME1 = $data2[PV_NAME];
	} // end if
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, $data[ORG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 1, $data[ORG_CODE], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 2, $OL_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 3, $OT_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 4, $OS_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 5, $CT_NAME, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 6, $PV_NAME1, set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
//		$worksheet->write($xlsRow, 0, $data[ORG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

		for($data_count=0; $data_count < count($arr_data); $data_count++) {
			// เช็คจบแต่ละ file ตาม $file_limit
//			echo "data_count:$data_count<br>";
			if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//				echo "***** close>>fname=$fname1<br>";
				$workbook->close();
				$arr_file[] = $fname1;

				$fnum++; $fnum_text="_$fnum";
				$fname1=$fname.$fnum_text.".xls";
//				echo "$data_count>>fname=$fname1<br>";
				$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
				require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

				$f_new = true;
			};
				
			// เช็คจบที่ข้อมูลแต่ละ sheet ตาม $data_limit
			if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if ($f_new) {
					$sheet_no=0; $sheet_no_text="";
					$f_new = false;
				} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
					$sheet_no++;
					$sheet_no_text = "_$sheet_no";
				}

//				echo "$data_count>>sheet name=$report_code"."$fnum_text"."$sheet_no_text<br>";

				$worksheet = &$workbook->addworksheet("$report_code".$fnum_text.$sheet_no_text);

				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
			
				call_header($data_count, $head);
			}
				
//			$AUTH_CHILD_ORG[] = $data[ORG_ID];
				
//			if ($arr_data[$data_count][level] > 0)  $pre_fix=str_repeat(" ", $arr_data[$data_count][level]*3)."^--"; else $pre_fix="";
			$pre_fix=str_repeat(" ", ($arr_data[$data_count][level]+1)*5);
			
			$xlsRow++;

			//$worksheet->write($xlsRow, 0, $pre_fix.$arr_data[$data_count][org_parent].":".$arr_data[$data_count][org_id]."-".$arr_data[$data_count][org_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 0, $pre_fix.$arr_data[$data_count][org_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 1, $pre_fix.$arr_data[$data_count][org_code], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 2, $pre_fix.$arr_data[$data_count][ol_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 3, $pre_fix.$arr_data[$data_count][ot_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 4, $pre_fix.$arr_data[$data_count][os_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 5, $pre_fix.$arr_data[$data_count][ct_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 6, $pre_fix.$arr_data[$data_count][pv_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
//			$worksheet->write($xlsRow, 0, $pre_fix."-".$arr_data[$data_count][org_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
//			echo "$data_count>(".$arr_data[$data_count][level].")".$arr_data[$data_count][org_parent].":".$arr_data[$data_count][ord_id]."-".$arr_data[$data_count][org_name]."<br>";
		} // end for
	} else {
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "***** ไม่พบข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 0));
	}

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "จำนวนข้อมูล $count_data รายการ", set_format("xlsFmtTitle", "B", "L", "", 0));
//	$xlsRow++;
//	$worksheet->write($xlsRow, 0, "Search Key", set_format("xlsFmtTitle", "", "L", "", 0));
//	$search_key = implode(", ",$search_arr_cond);
//	if ($search_key)
//		$skey = $search_key;
//	else
//		$skey = "";
//	$worksheet->write($xlsRow, 1, $skey, set_format("xlsFmtTitle", "", "L", "", 0));

	$workbook->close();

	$arr_file[] = $fname1;

	require_once("excel_tailpart_subrtn.php");

	function get_org ($org_parent, $level) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $arr_data, $data_count;

		$count_all = 0;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID, ORG_CODE, ORG_NAME, OL_CODE, OT_CODE, OS_CODE, CT_CODE, PV_CODE from PER_ORG where ORG_ID_REF=$org_parent  order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
			
				$OL_CODE = $data[OL_CODE];
	$OL_NAME = "";
	if($OL_CODE){
		$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE='$OL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OL_NAME = $data2[OL_NAME];
	} // end if

	$OT_CODE = $data[OT_CODE];
	$OT_NAME = "";
	if($OT_CODE){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];
	} // end if

	$OS_CODE = $data[OS_CODE];
	$OS_NAME = "";
	if($OS_CODE){
		$cmd = " select OS_NAME from PER_ORG_STAT where OS_CODE='$OS_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OS_NAME = $data2[OS_NAME];
	} // end if

	$CT_CODE = $data[CT_CODE];
	$CT_NAME = "";
	if($CT_CODE){
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
	} // end if

	$PV_CODE1 = $data[PV_CODE];
	$PV_NAME1 = "";
	if($PV_CODE1){
		$cmd = " select PV_NAME from PER_PROVINCE where CT_CODE='$CT_CODE' and PV_CODE='$PV_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME1 = $data2[PV_NAME];
	} // end if
			
				$arr_data[$data_count][org_id] = $data[ORG_ID];
				$arr_data[$data_count][org_parent] = $org_parent;
				$arr_data[$data_count][org_name] = $data[ORG_NAME];
				$arr_data[$data_count][org_code] = $data[ORG_CODE];
				$arr_data[$data_count][ol_name] = $OL_NAME;
				$arr_data[$data_count][ot_name] = $OT_NAME;
				$arr_data[$data_count][os_name] = $OS_NAME;
				$arr_data[$data_count][ct_name] = $CT_NAME;
				$arr_data[$data_count][pv_name] = $PV_NAME1;
				$arr_data[$data_count][level] = $level;
				
//				echo "count_all:$count_all [$org_parent:".$data[ORG_ID].":".$data[ORG_NAME]."]<br>";
				$data_count++;
				$count_all += get_org($data[ORG_ID], $level+1)+1;
			} // end while
		} // end if
		
		return $count_all;
	} // function count_all
?>