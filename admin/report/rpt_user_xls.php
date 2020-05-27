<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	
	$report_code = "rpt_user";

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
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 40);
                $worksheet->set_column(4, 4, 30);
                $worksheet->set_column(5, 5, 25);
                $worksheet->set_column(6, 6, 30);
                $worksheet->set_column(7, 7, 25);
                $worksheet->set_column(8, 8, 30);
		
                $worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 1, "ชื่อผู้ใช้งาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "สำนัก/กอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "สร้างโดย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "วันที่สร้าง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 6, "แก้ไขโดย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 7, "วันที่แก้ไข", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 8, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if
        
        
        $sort_type2 = (isset($sort_type2))?  $sort_type2 : "1:asc";
	$arrSort=explode(":",$sort_type2);
	$SortType[$arrSort[0]]	= $arrSort[1];
	
        if (trim($search_code))	$arr_search_condition[] = "username like '%$search_code%'";
	if (trim($search_name))	$arr_search_condition[] = "fullname like '%$search_name%'";	
//	if (trim($search_name))	$arr_search_condition[] = "fullname like '$search_name%'";	
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition); 
        
  	if(!$order_str2)	$order_str2=1;
	if($order_str2==1){	//(ค่าเริ่มต้น) ลำดับที่
		$order_str = "USERNAME $SortType[$order_str2]";
  	}elseif($order_str2==2) {	//รหัส
		$order_str = "FULLNAME ".$SortType[$order_str2];
  	}elseif($order_str2==3) {	//ชื่อ
		$order_str = "ADDRESS ".$SortType[$order_str2];
  	}
        //รหัสผู้ใช้ตรงกับรหัสผ่าน
        $conditionUserPW='';
        $val_user_flag=" user_flag='Y' AND ";
        if($SESS_USERGROUP==1){
            //echo 'รหัสผู้ใช้ตรงกับรหัสผ่าน :'.$chhuserpw;
            $userpw_arr=array();
            if($chhuserpw=='Y'){
                $sql =" select username from user_detail where $val_user_flag group_id=$group_id $search_condition ";
                $db->send_cmd($sql);
                while ($data9 = $db->get_array()) {
                    $data9 = array_change_key_case($data9, CASE_LOWER);
                    $tmp_username_md5 = md5($data9[username]);
                    $userpw_arr[]= " password='".$tmp_username_md5."'";
                }
            }
            $inuserpw=implode(" or ",$userpw_arr);
            if(count($userpw_arr)){
                $conditionUserPW=' and ('.$inuserpw.')';
            }
            if($selectFlag=='0'){
                $val_user_flag="";
            }else{
                if($selectFlag){
                    $val_user_flag=" user_flag='".$selectFlag."' AND ";
                }else{
                    $val_user_flag="";
                }
            }
        }
        $cmd =  "select  ID,USERNAME, GROUP_ID ,FULLNAME,ADDRESS,CREATE_BY,CREATE_DATE,UPDATE_BY,UPDATE_DATE,PASSWORD ,USER_FLAG 
        from user_detail 
        where $val_user_flag group_id=$group_id $search_condition $conditionUserPW order by $order_str";
	//$cmd = "select id,code,name_th,name_en,access_list, group_org_structure, group_active, group_seq_no from user_group order by $order_str";
	$count_data = $db->send_cmd($cmd);
        //echo "<pre>";
        //die($cmd);
//	echo "$cmd ($count_data)<br>";
	$user = (array) null;
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

		print_header(1);
		$data_count = 1;
		$i=0;
                $idx=1;
		while($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
                        $user[$i]["idx"] = $idx;
			$user[$i]["id"] = $data[id];
                        $user[$i]["username"] = $data[username];
                        $user[$i]["password"] = $data[password];
                        $user[$i]["user_flag"] = $data[user_flag];
                        
                        
			$user[$i]["group_id"] = $data[group_id];
			$user[$i]["fullname"] = $data[fullname];
			$user[$i]["address"] = $data[address];
			$user[$i]["create_by"] = $data[create_by];
			$user[$i]["create_date"] = trim($data[create_date]);
                        $user[$i]["update_by"] = $data[update_by];
			$user[$i]["update_date"] = trim($data[update_date]);
                        
                        $id_user_create = $data[create_by];
                        $id_user_update = $data[update_by];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $id_user_create ";
			$db_dpis2->send_cmd($cmd);
			if ($data2=$db_dpis2->get_array()) {
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$user[$i]["create_by_text"]= trim($data2[titlename]) . trim($data2[fullname]);
			} else {
                                $user[$i]["create_by_text"]= '';
                        }
                        
                        $cmd2 ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $id_user_update ";
			$db_dpis3->send_cmd($cmd2);
			if ($data3=$db_dpis3->get_array()) {
				$data3 = array_change_key_case($data3, CASE_LOWER);
				$user[$i]["update_by_text"]= trim($data3[titlename]) . trim($data3[fullname]);
			} else {
                                $user[$i]["update_by_text"]= '';
                        }
			$i++;
                        $idx++;
		} // end while
                
               
		for($i = 0; $i < $count_data; $i++){
			$data_count++;
                        
                        $user_number = $user[$i]["idx"];
			$user_id = $user[$i]["id"];
                        $username = $user[$i]["username"];
                        $password = $user[$i]["password"];
                        $user_flag = $user[$i]["user_flag"];
                        
                        
			$user_group_id = $user[$i]["group_id"];
			$user_fullname = $user[$i]["fullname"];
			$user_address = $user[$i]["address"];
			$user_create_by = $user[$i]["create_by"];
                        $user_create_by_text = $user[$i]["create_by_text"];
			$user_create_date = $user[$i]["create_date"];
			$user_update_by_text = $user[$i]["update_by_text"];
                        $user_update_by = $user[$i]["update_by"];
			$user_update_date = $user[$i]["update_date"];
			
                        $md5_username=md5($username);
                        $iconchk='';
                        $txt_flag='';
                        if($SESS_USERGROUP==1){
                            if($user_flag=='N'){
                                $txt_flag=' [รหัสถูกยกเลิก]';
                            }
                        }
                        if($md5_username==$password && $SESS_USERGROUP==1){
                            
                            $iconchk='รหัสผู้ใช้ตรงกับรหัสผ่าน';
                        }
			

			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $user_number, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write_string($xlsRow, 1, $username, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 2, $user_fullname, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $user_address, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $user_create_by_text, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $user_create_date, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 6, $user_update_by_text, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 7, $user_update_date, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 8, $iconchk.$txt_flag, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
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