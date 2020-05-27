<?
	include("php_scripts/function_manage_login.php");
	include("php_scripts/session_start.php");
	
	if ($command == "YES") {
		manage_login("1"); // ปิดรายการ user_last_access (รายการ logout)
		session_destroy();
		//header("Location: http://$HTTP_HOST/".$virtual_site."admin/index.html?command=LOGOUT");
                header("Location: /".$virtual_site."admin/index.html?command=LOGOUT");
	} else if ($command == "NO" && $MENU_TYPE==2) header("Location: main_1.html");
	else if ($command == "NO") header("Location: main.html");
	
?>	