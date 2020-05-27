<?
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

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

	if ($command=="ADD" && trim($username) && trim($passwd)) {	
		$error_signin = "";
		$cmd = "select * from user_system where username='$username'";
		if($db->send_cmd($cmd)) $error_signin = "Error :: Duplicate Username";
		if(!trim($error_signin)){
			$passwd = md5($passwd);
			$cmd = " insert into user_system (username, password, f_level)
						  values ('$username', '$passwd', '$f_level') ";
			$db->send_cmd($cmd);
			echo "add-$cmd<br>";
			$UPD = 1;
		}
	} // end if

	if( $command=='UPDATE' && ( trim($username) || trim($passwd) )){
		$UPD = 1;
		$error_signin = "";
		$cmd = "select * from user_system where username='$username'";
		if(!($db->send_cmd($cmd))) $error_signin = "Error :: Can not Update (Not found Username='$username') ";
		if(!trim($error_signin)){
			$UPD = "";
			if($passwd){
				$set_password = ", password = '".md5($passwd)."'";
			}
			$cmd = " update user_system set 
								$set_password, f_level='$f_level' 
							where username='$username' ";
			$db->send_cmd($cmd);
	//		$db->show_error();
			$CLEAR = 1;
			echo "upd-$cmd<br>";
		} // end if
	}

	if( $command=="DELETE" ){
		$error_signin = "";
		$cmd = "select * from user_system where username='$username'";
		if(!($db->send_cmd($cmd))) $error_signin = "Error :: Cannot Delete (not founded Username='$username')";
		if(!trim($error_signin)){
			$cmd = " delete from user_system where username = '$username' ";
			$db->send_cmd($cmd);
		}
	} // end if

	if ($command=="UPD") {
		$UPD=1;
	}
	
	$passwd = "";
	$confirm_passwd = "";
	$f_level = "";
	if ($UPD) {
		$cmd = " select * from user_system where username='$username' ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$username = $data[username];
		$f_level = $data[f_level];
	}

	if( (!$UPD && !$DEL && $command=="CANCEL" && !$error_signin) ){
			unset($username);
			unset($passwd);
			unset($confirm_passwd);
			unset($f_level);
	}			
?>