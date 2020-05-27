<?
	ereg("/([0-9a-z]{32})", $REQUEST_URI, $regs);
	$session_id = $regs[1];
	//echo "SESSSS :: $session_id <br>";
        //echo "CHK_PASS :: $CHK_PASS";
        //echo "<br>SESSSS :: $session_id <br>";
       
	if ($session_id) {
		$sess_file = ini_get("session.save_path")."/sess_".$session_id;
		$sess_duration = ini_get("session.gc_maxlifetime");			// in seconds 

		if(file_exists($sess_file)){
			$sess_modified = filemtime($sess_file);
			$diff_time = mktime() - $sess_modified;

			if($diff_time <= $sess_duration){ 
//				echo "diff_time=$diff_time, sess_duration=$sess_duration<br>";
				session_id($session_id);
				session_start();

				// update last_activate
				if($db_type=="mysql") $log_date = "NOW()";
				elseif($db_type=="mssql") $log_date = "GETDATE()";
				elseif($db_type=="oci8" || $db_type=="odbc") { $log_date = date("Y-m-d H:i:s"); $log_date = "'$log_date'"; }
			
				$cmd = " update user_last_access set last_active=$log_date where session_id='$session_id' ";
				$db->send_cmd($cmd);
//				$db->show_error();
			} // end if
		} // end if file_exists
                
                /*$sql="SELECT USER_ID,F_LOGOUT FROM  USER_LAST_ACCESS WHERE SESSION_ID='$session_id' AND LAST_LOGOUT IS NULL AND F_LOGOUT !='1' ";
                echo $sql;
                if ($db->send_cmd($sql)) {
                    $dataApi = $db->get_array();
                    echo $session_id;
                    die('xxxxxxxxxxxxx');
                }*/
                //die();
	} // end if

	if ($CHK_PASS != 1)  {
//		manage_login("1"); // ปิดรายการ user_last_access (รายการ logout)

			// เมื่อมีการ logout
			if($db_type=="mysql") $log_date = "NOW()";
			elseif($db_type=="mssql") $log_date = "GETDATE()";
			elseif($db_type=="oci8" || $db_type=="odbc") 	{ $log_date = date("Y-m-d H:i:s"); $log_date = "'$log_date'"; }
			
			$cmd = " update user_last_access set last_logout=$log_date, f_logout='1' where session_id='$session_id' ";
			$db->send_cmd($cmd);
//			$db->show_error();

		session_id($session_id);
		session_start();
		session_destroy();
//		echo "Location: http://".$_SERVER['HTTP_HOST']."/admin/index.html<br>";
		//header("Location: http://".$_SERVER['HTTP_HOST']."/admin/index.html");
                header("Location: /admin/index.html");
		exit;
	}
	$FILENAME = basename($PHP_SELF);
	
  	$main_category_id = 1;
	$ACCESS_PAGE = 1;
	if ($BKK_FLAG==1) $webpage_title = "โปรแกรมบริหารผลงานแบบ Portfolio (แฟ้มสะสมผลงาน)";
	else $webpage_title = "โปรแกรมสารสนเทศทรัพยากรบุคคล";

	include("../php_scripts/acquire_auth.php");
?>