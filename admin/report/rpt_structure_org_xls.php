<?	
	if ($BYASS=="Y") $ORGTAB = "PER_ORG_ASS"; else $ORGTAB = "PER_ORG";

	include("../../php_scripts/connect_database.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$header_width[0] = 100; $header_text[0] = "หน่วยงาน"; 				//	ORG_NAME;

	require_once("excel_headpart_subrtn.php");

	$cnt = 0;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";

	$report_code = "xls_STRUCTURE_".($BYASS=="Y"?"BY_ASS":"BY_LAW");

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

	$head = "โครงสร้างตาม".($BYASS=="Y"?"มอบหมายงาน":"กฏหมาย");

//	echo "$ORG_ID:$search_ol_code<br>";
	if (!$ORG_ID1)  {
		if ($SESS_ORG_ID) { $ORG_ID1=$SESS_ORG_ID; $search_ol_code="03"; }
		elseif ($SESS_DEPARTMENT_ID) { $ORG_ID1=$SESS_DEPARTMENT_ID; $search_ol_code="02"; }
		elseif ($SESS_MINISTRY_ID) { $ORG_ID1=$SESS_MINISTRY_ID; $search_ol_code="01"; }
		else { $ORG_ID1=0; $search_ol_code="01"; }
//		echo "not ORG_ID1:$ORG_ID1:$search_ol_code<br>";
	}
// 	copy จาก structure_org_edit.php
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;

  	if($CTRL_TYPE==1 || $CTRL_TYPE==2) { 
		unset($AUTH_CHILD_ORG);
	
		if($CTRL_TYPE==2){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==3 || $SESS_USERGROUP_LEVEL==3){
			$AUTH_CHILD_ORG[] = $SESS_MINISTRY_ID;
			list_child_org($SESS_MINISTRY_ID);
		}elseif($CTRL_TYPE==4 || $SESS_USERGROUP_LEVEL==4){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==5 || $SESS_USERGROUP_LEVEL==5){
			$AUTH_CHILD_ORG[] = $SESS_ORG_ID;
			list_child_org($SESS_ORG_ID);
		} // end if
	
		switch($SESS_USERGROUP_LEVEL){
			case 1 :
				$START_ORG_ID = 1;
				break;
			case 2 :
				$START_ORG_ID = 1;
				break;
			case 3 :
				$START_ORG_ID = $SESS_MINISTRY_ID;
				break;
			case 4 :
				$START_ORG_ID = $SESS_DEPARTMENT_ID;
				break;
			case 5 :
				$START_ORG_ID = $SESS_ORG_ID;
				break;
		} // end switch case
	} else {
		$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=$ORG_ID1 order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	}
	
	if(!$ORG_ID1 && !$ORG_ID_REF){
		$ORG_ID1 = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
	} // end if

	if(!$ORG_ID1 && !$ORG_ID_REF){
		$ORG_ID1 = 1;
		$ORG_ID_REF = 1;
	} // end if
//	echo "ORG_ID1=$ORG_ID1, ORG_ID_REF=$ORG_ID_REF<br>";
//	จบ copy จาก structure_org_edit.php
//	$UPDATE_DATE = date("Y-m-d H:i:s");

	$arr_data = (array) null;
	$data_count = 0;
	$count_data = get_org($ORG_ID1, 0);
	if ($count_data) {
		$cmd = " select ORG_NAME from $ORGTAB where ORG_ID=$ORG_ID1 order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "cmd=$cmd (".$data['ORG_NAME'].")<br>";

		$data_count = 0;
		call_header($data_count, $head);

		$xlsRow++;
//		$worksheet->write($xlsRow, 0, $ORG_ID1."-".$data[ORG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
		$worksheet->write($xlsRow, 0, $data[ORG_NAME], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));

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

//			$worksheet->write($xlsRow, 0, $pre_fix.$arr_data[$data_count][org_parent].":".$arr_data[$data_count][org_id]."-".$arr_data[$data_count][org_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
			$worksheet->write($xlsRow, 0, $pre_fix."-".$arr_data[$data_count][org_name], set_format("xlsFmtTitle", "B", "L", "TLBR", 0));
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
		global $arr_data, $data_count, $ORGTAB;

		$count_all = 0;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID, ORG_NAME from $ORGTAB where ORG_ID_REF=$org_parent order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$arr_data[$data_count][org_id] = $data[ORG_ID];
				$arr_data[$data_count][org_parent] = $org_parent;
				$arr_data[$data_count][org_name] = $data[ORG_NAME];
				$arr_data[$data_count][level] = $level;
//				echo "count_all:$count_all [$org_parent:".$data[ORG_ID].":".$data[ORG_NAME]."] level=$level<br>";
				$data_count++;
				$count_all += get_org($data[ORG_ID], $level+1)+1;
			} // end while
		} // end if
		
		return $count_all;
	} // function count_all

	function list_child_org ($org_parent) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $AUTH_CHILD_ORG, $ORGTAB;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=$org_parent order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$AUTH_CHILD_ORG[] = $data[ORG_ID];
				list_child_org($data[ORG_ID]);
			} // end while
		} // end if
	} // function
?>