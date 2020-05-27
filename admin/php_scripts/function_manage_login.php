<?
	// เพื่อการสร้าง รายการ login และ logout
	function manage_login($upd) {
		global $db, $db_type, $session_id, $SESS_USERID, $SESS_USERNAME, $SESS_FIRSTNAME, $REMOTE_ADDR;
		
		if($db_type=="mysql") $log_date = "NOW()";
		elseif($db_type=="mssql") $log_date = "GETDATE()";
		elseif($db_type=="oci8" || $db_type=="odbc") 	{ $log_date = date("Y-m-d H:i:s"); $log_date = "'$log_date'"; }
		
		if ($upd != "1") {
// 			สร้าง login ใหม่
			// เก็บอันเก่าที่ค้างก่อน (f_logout != "1")
//			$cmd = "select * from user_last_access where user_id=$SESS_USERID and (f_logout!='1' or f_logout is null)";
			$cmd = "select log_id, last_active from user_last_access where (f_logout!='1' or f_logout is null)";
			$cnt = $db->send_cmd($cmd);
//			echo "$cmd ($cnt)<br>";
			$arr = (array) null;
			if ($cnt > 0) {
				$sess_duration = ini_get("session.gc_maxlifetime");			// in seconds 
				while ($data = $db->get_array()) {
					$data = array_change_key_case($data, CASE_LOWER);
					$last_act = $data[last_active];
					$last_active = strtotime($last_act);
					$diff_time = mktime() - $last_active;
//					echo $data[USERNAME]." -- diff_time ($diff_time) = ".mktime()." - $last_active (".$last_act.") -- $sess_duration<br>";
//					if($diff_time > $sess_duration || $SESS_USERID==$data[USER_ID])  $arr[] = $data[LOG_ID];
					if($diff_time > $sess_duration)  $arr[] = $data[log_id];
				}
				for($i = 0; $i < $cnt; $i++) {
					$cmd = " update user_last_access set last_logout=$log_date, f_logout='1' where log_id=".$arr[$i]." ";
					$db->send_cmd($cmd);
//					echo "update old-$cmd<br>";
				}
			}
			// หา log_id ใหม่
			$cmd = " select max(log_id) as max_id from user_last_access ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$log_id = $data[max_id] + 1;
			// สร้าง login record ใหม่
			$cmd = " insert into user_last_access (log_id, user_id, username, fullname, last_login, from_ip, session_id, last_active) values ($log_id, $SESS_USERID, '$SESS_USERNAME', '$SESS_FIRSTNAME', $log_date, '$REMOTE_ADDR', '$session_id', $log_date) ";
			$db->send_cmd($cmd);
//			$db->show_error();
//			echo "create new $cmd<br>";
		} else {
			// เมื่อมีการ logout
			$cmd = " update user_last_access set last_logout=$log_date, f_logout='1' where session_id='$session_id' ";
			$db->send_cmd($cmd);
//			$db->show_error();
		}
	}
?>
