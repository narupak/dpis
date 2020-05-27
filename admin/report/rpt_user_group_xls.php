<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);
	
	$report_code = "rpt_user_group";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 45);
		$worksheet->set_column(2, 2, 70);
		$worksheet->set_column(3, 3, 10);
		
		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อกลุ่ม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ระดับกลุ่ม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ฐานข้อมูล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

  	if(!$order_by)	$order_by=1;
	if($order_by==1){	//(ค่าเริ่มต้น) ลำดับที่
		$order_str = "group_seq_no $SortType[$order_by], code $SortType[$order_by]";
  	}elseif($order_by==2) {	//รหัส
		$order_str = "code ".$SortType[$order_by];
  	}elseif($order_by==3) {	//ชื่อ
		$order_str = "name_th ".$SortType[$order_by];
  	}elseif($order_by==4) {	//โครงสร้าง
		$order_str = "group_org_structure ".$SortType[$order_by];
	}

	$cmd = "select id,code,name_th,name_en,access_list, group_org_structure, group_active, group_seq_no from user_group order by $order_str";
	$count_data = $db->send_cmd($cmd);
//	echo "$cmd ($count_data)<br>";
	$user_group = (array) null;
	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(1);
		$data_count = 1;
		
		while($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$user_group[$i]["id"] = $data[id];
			$user_group[$i]["code"] = $data[code];
//			echo "$i-".$user_group[$i]["code"]."<br>";
			$user_group[$i]["name_th"] = $data[name_th];
			$user_group[$i]["name_en"] = $data[name_en];
			$user_group[$i]["access_list"] = $data[access_list];
			$user_group[$i]["group_org_structure"] = $data[group_org_structure];
			$user_group[$i]["group_active"] = $data[group_active];
			$user_group[$i]["group_seq_no"] = $data[group_seq_no];
			$i++;
		} // end while

		for($i = 0; $i < $count_data; $i++){
			$data_count++;

			$user_group_id = $user_group[$i]["id"];
			$user_group_code = $user_group[$i]["code"];
			$user_group_name_th = $user_group[$i]["name_th"];
			$user_group_name_en = $user_group[$i]["name_en"];
			$user_group_name = ($user_group_name_th ? $user_group_name_th.($user_group_name_en && $user_group_name_en != $user_group_name_th ? "($user_group_name_en)" : "") : $user_group_name_en);
			$user_group_access_list = $user_group[$i]["access_list"];
			$user_group_group_org_structure = $user_group[$i]["group_org_structure"];
			$user_group_group_active = $user_group[$i]["group_active"];
			$user_group_group_seq_no = $user_group[$i]["group_seq_no"];
			
			$str_user_section = substr( $user_group_access_list , 1 , -1 );
//			echo $str_user_section;
			$arr_user_section = explode( "," , $str_user_section );

			$id = $user_group_id;
			$this_dpisdb_user = "";
			$cmd = " select dpisdb, dpisdb_name, dpisdb_user from user_group where id=$id ";
			$db->send_cmd($cmd);
			if ($data=$db->get_array()) {
				$data = array_change_key_case($data, CASE_LOWER);
				if ($data[dpisdb]==1) $this_dpisdb_user = $data[dpisdb_name];
				elseif ($data[dpisdb]==2) $this_dpisdb_user = $data[dpisdb_user];
			}
			if (!$this_dpisdb_user) $this_dpisdb_user = "-";

		 	if($user_group_group_org_structure==0){ 
				$this_org_structure = "โครงสร้างตามกฏหมาย";
			 }elseif($user_group_group_org_structure==1){ 
			 	$this_org_structure = "โครงสร้างตามมอบหมายงาน";
			 }elseif($user_group_group_org_structure==2){ 
				$this_org_structure = "ตามกฏหมายและมอบหมายงาน";
			}

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $user_group_code, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $user_group_name, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, $this_org_structure, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $this_dpisdb_user, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

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