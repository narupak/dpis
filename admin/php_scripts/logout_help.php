<?
	include("php_scripts/function_manage_login.php");
	
	if ($command == "YES") {
		manage_login("1"); // ปิดรายการ user_last_access (รายการ logout)
		session_destroy();
		//header("Location: http://$HTTP_HOST/admin/index_help.html?command=LOGOUT");
                header("Location: /admin/index_help.html?command=LOGOUT");
	} else if ($command == "NO") header("Location: main_help.html");
	
?>	