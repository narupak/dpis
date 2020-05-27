<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
  	$sort_by = (isset($sort_by))?  $sort_by : 1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
  	if(!$order_by)	$order_by=1;
	if($order_by==1){	// (ค่าเริ่มต้น) ลำดับที่
		$order_str = "server_id ".$SortType[$order_by];
  	}elseif($order_by==2) {	// ชื่อ
		$order_str = "server_name ".$SortType[$order_by];
  	}elseif($order_by==3) {	// ftp_server
		$order_str = "ftp_server ".$SortType[$order_by];
  	}elseif($order_by==4) {	// http_server
		$order_str = "http_server ".$SortType[$order_by];
	}

	if ($command == "ADD" && $server_name != "") {
		$cmd = " select max(server_id) as max_id from OTH_SERVER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$max_id = $data[max_id] + 1;
		$cmd = "insert into OTH_SERVER (server_id, server_name, ftp_server, ftp_username, ftp_password, main_path, http_server) values ($max_id, '$server_name', '$ftp_server', '$ftp_username', '$ftp_password' , '$main_path', '$http_server')";
		$db_dpis->send_cmd($cmd);
//		$db->show_error();
		$CLEAR = 1;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่ม Server [$server_id : $server_name]");
	}

	if ($command == "UPDATE" && $server_id != "") {
		$update_code = "";
		$cmd = "update OTH_SERVER set
						SERVER_NAME = '$server_name',
						FTP_SERVER = '$ftp_server',
						FTP_USERNAME = '$ftp_username',
						FTP_PASSWORD = '$ftp_password',
						MAIN_PATH = '$main_path',
						HTTP_SERVER = '$http_server'
					where SERVER_ID = $server_id";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$CLEAR = 1;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขกลุ่มผู้ใช้งาน [$group_name_th]");
	}

	if ($command == "DELETE" && $server_id) {
		$cmd = " select SERVER_NAME from OTH_SERVER where SERVER_ID=$server_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$server_name = $data[server_name];
		
		$cmd = " delete from OTH_SERVER where SERVER_ID = $server_id ";
		$db_dpis->send_cmd($cmd);
		$CLEAR = 1;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบ ข้อมูล Server [$server_id : $server_name]");
	}

	$cmd = "select * from OTH_SERVER order by $order_str";
	$count_server = $db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd ($count_server)<br>";
	if($count_server) :
		$i = 0;
		while($data = $db_dpis->get_array()) :
			$data = array_change_key_case($data, CASE_LOWER);
			$server_rec[$i]["id"] = $data[server_id];
			$server_rec[$i]["name"] = $data[server_name];
			$server_rec[$i]["ftpserver"] = $data[ftp_server];
			$server_rec[$i]["ftpuser"] = $data[ftp_username];
			$server_rec[$i]["ftppwd"] = $data[ftp_password];
			$server_rec[$i]["mainpath"] = $data[main_path];
			$server_rec[$i]["httpserver"] = $data[http_server];
			$i++;
		endwhile;
	endif;

	if ($server_id) {
		$cmd = " select * from OTH_SERVER where SERVER_ID=$server_id ";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			$server_id = $data[server_id];
			$server_name = $data[server_name];
			$ftp_server = $data[ftp_server];
			$ftp_username = $data[ftp_username];
			$ftp_password = $data[ftp_password];
			$main_path = $data[main_path];
			$http_server = $data[http_server];
		} // while
	} // if

	if ( !$UPD && !$DEL) {
		$server_id = "";
		unset($server_id);
		unset($server_name);
		unset($ftp_server);
		unset($ftp_username);
		unset($ftp_password);
		unset($main_path);
		unset($http_server);
	}

?>