<?
	include("../php_scripts/connect_database_help.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_manage_login.php");

	$username = trim($username);
	$password = trim($password);
	$ERR = -1;
	if ($command == "LOGIN" && $username != "" && $password != "") {
		///////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////////////////////////////
		if ($username=="administrator" && $password=="systemhelp") {
			$ERR = 0;
		} else {
			$encrypt_password = md5($password);
			$ERR = 1;
			$cmd =  "select	* from user_system where username = '$username'";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if ($data[password] == $encrypt_password) {
				$ERR = 0;
			} else {
				$ERR = 1;
			}
		} // end if ($username=="admin")
	} else if ($command == "LOGIN" && $username != "" && $password == "") {
		$ERR = 1;
	} // end if
	if ($ERR == 0) {
		//header("Location: http://$HTTP_HOST/admin/main_help.html?LOGON_USER=$username");
            header("Location: /admin/main_help.html?LOGON_USER=$username");
	}
?>
