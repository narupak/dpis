<?
	include("../php_scripts/connect_database.php");
	if ($DPISDB) 	include("php_scripts/load_per_control.php");
	include("php_scripts/function_share.php");

	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_per_type SINGLE NULL ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_per_type NUMBER(1) NULL ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_per_type SMALLINT(1) NULL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_org_structure SINGLE NULL ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_org_structure NUMBER(1) NULL ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_org_structure SMALLINT(1) NULL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_active SINGLE NULL ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_active NUMBER(1) NULL ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_active SMALLINT(1) NULL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($db_type=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_seq_no INTEGER2 ";
	elseif($db_type=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_seq_no NUMBER(5) ";
	elseif($db_type=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_seq_no SMALLINT(5) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_active SINGLE NULL ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_active NUMBER(1) NULL ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_active SMALLINT(1) NULL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($db_type=="odbc") 
		$cmd = " ALTER TABLE user_detail ADD password_last_update varchar(19) ";
	elseif($db_type=="oci8") 
		$cmd = " ALTER TABLE user_detail ADD password_last_update varchar2(19) ";
	elseif($db_type=="mysql")
		$cmd = " ALTER TABLE user_detail ADD password_last_update varchar(19) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	$cmd = " ALTER TABLE user_privilege ADD can_confirm char(1) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	$cmd = " ALTER TABLE user_detail ADD titlename varchar(50) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	$UPDATE_DATE = date("Y-m-d H:i:s");
// พงษ์ศักดิ์ เพิ่มเพื่อ check อายุ password
$PASSWORD_UPDATE_DATE = date("Y-m-d");

if($command1=='UPDATE' ){
	$old_password = md5($old_passwd);
		
	$cmd =  " select id from user_detail where username = '$username' and password = '$old_password' ";
	$count_id = $db->send_cmd($cmd);
	if($count_id > 0){ 
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$USER_ID = $data[id];
		$allow_update = 1;
		if($passwd) $set_password = "password = '".md5($passwd)."'";
		$cmd = "update user_detail set 
							$set_password,
							update_date = '$UPDATE_DATE', 
							update_by = $USER_ID,
							password_last_update = '$PASSWORD_UPDATE_DATE'
						where id=$USER_ID";
		$db->send_cmd($cmd);
//		echo "$cmd<br>";
		$ERR = 9; // แก้ไขรหัสผ่านแล้ว
		insert_log("LOGON PAGE > รหัสผ่านหมดอายุ แก้ไขรหัสผ่านใหม่ [$username -> $user_name]");			
	}else{ 
		$ERR=3; // เพื่อแสดงข้อความ ว่า แก้ไขไม่ผ่าน
		$err_text = "ไม่สามารถแก้ไขรหัสผ่านใหม่ได้!!!";
	}
	
} else { //else command1 != UPDATE
// จบ ส่วนเพิ่ม พงษ์ศักดิ์ เพิ่มเพื่อ check อายุ password

	if($db_type=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_per_type single ";
	elseif($db_type=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_per_type number(1) ";
	elseif($db_type=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_per_type smallint(1) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	if($db_type=="odbc") 
		$cmd = " ALTER TABLE user_group ADD group_org_structure single ";
	elseif($db_type=="oci8") 
		$cmd = " ALTER TABLE user_group ADD group_org_structure number(1) ";
	elseif($db_type=="mysql")
		$cmd = " ALTER TABLE user_group ADD group_org_structure smallint(1) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	if($DPISDB=="odbc") 
		$cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL single ";
	elseif($DPISDB=="oci8") 
		$cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL number(1) ";
	elseif($DPISDB=="mysql")
		$cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL smallint(1) ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	$cmd = " SELECT COUNT(SITE_ID) AS COUNT_DATA FROM SITE_INFO ";
	$count_data = $db->send_cmd($cmd);
//	$db->show_error();
	if (!$count_data) {
		if($db_type=="odbc") 
			$cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR(10) NOT NULL,
			ORG_ID VARCHAR(10) NULL,
			DPISDB VARCHAR(50) NULL,		
			DPISDB_HOST VARCHAR(50) NULL,		
			DPISDB_NAME VARCHAR(50) NULL,		
			DPISDB_USER VARCHAR(50) NULL,		
			DPISDB_PWD VARCHAR(50) NULL,		
			SITE_NAME VARCHAR(100) NULL,		
			SITE_BG_LEFT VARCHAR(100) NULL,		
			SITE_BG_LEFT_X INTEGER2 NULL,
			SITE_BG_LEFT_Y INTEGER2 NULL,
			SITE_BG_LEFT_W INTEGER2 NULL,
			SITE_BG_LEFT_H INTEGER2 NULL,
			SITE_BG_LEFT_ALPHA NUMBER NULL,
			SITE_BG VARCHAR(100) NULL,		
			SITE_BG_X INTEGER2 NULL,
			SITE_BG_Y INTEGER2 NULL,
			SITE_BG_W INTEGER2 NULL,
			SITE_BG_H INTEGER2 NULL,
			SITE_BG_ALPHA NUMBER NULL,
			SITE_BG_RIGHT VARCHAR(100) NULL,		
			SITE_BG_RIGHT_X INTEGER2 NULL,
			SITE_BG_RIGHT_Y INTEGER2 NULL,
			SITE_BG_RIGHT_W INTEGER2 NULL,
			SITE_BG_RIGHT_H INTEGER2 NULL,
			SITE_BG_RIGHT_ALPHA NUMBER NULL,
			HEAD_FONT_NAME VARCHAR(100) NULL,		
			HEAD_FONT_SIZE INTEGER2 NULL,
			CSS_NAME VARCHAR(100) NULL,		
			SITE_LEVEL SINGLE NULL,	
			PV_CODE VARCHAR(10) NULL,		
			HEAD_HEIGHT INTEGER2 NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
		elseif($db_type=="oci8") 
			$cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR2(10) NOT NULL,
			ORG_ID VARCHAR2(10) NULL,
			DPISDB VARCHAR2(50) NULL,		
			DPISDB_HOST VARCHAR2(50) NULL,		
			DPISDB_NAME VARCHAR2(50) NULL,		
			DPISDB_USER VARCHAR2(50) NULL,		
			DPISDB_PWD VARCHAR2(50) NULL,		
			SITE_NAME VARCHAR2(100) NULL,		
			SITE_BG_LEFT VARCHAR2(100) NULL,		
			SITE_BG_LEFT_X NUMBER(5) NULL,
			SITE_BG_LEFT_Y NUMBER(5) NULL,
			SITE_BG_LEFT_W NUMBER(5) NULL,
			SITE_BG_LEFT_H NUMBER(5) NULL,
			SITE_BG_LEFT_ALPHA NUMBER(4,2) NULL,
			SITE_BG VARCHAR2(100) NULL,		
			SITE_BG_X NUMBER(5) NULL,
			SITE_BG_Y NUMBER(5) NULL,
			SITE_BG_W NUMBER(5) NULL,
			SITE_BG_H NUMBER(5) NULL,
			SITE_BG_ALPHA NUMBER(4,2) NULL,
			SITE_BG_RIGHT VARCHAR2(100) NULL,		
			SITE_BG_RIGHT_X NUMBER(5) NULL,
			SITE_BG_RIGHT_Y NUMBER(5) NULL,
			SITE_BG_RIGHT_W NUMBER(5) NULL,
			SITE_BG_RIGHT_H NUMBER(5) NULL,
			SITE_BG_RIGHT_ALPHA NUMBER(4,2) NULL,
			HEAD_FONT_NAME VARCHAR2(100) NULL,		
			HEAD_FONT_SIZE NUMBER(5) NULL,
			CSS_NAME VARCHAR2(100) NULL,		
			SITE_LEVEL NUMBER(1) NULL,	
			PV_CODE VARCHAR2(10) NULL,		
			HEAD_HEIGHT NUMBER(5) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
		elseif($db_type=="mysql")
			$cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR(10) NOT NULL,
			ORG_ID VARCHAR(10) NULL,
			DPISDB VARCHAR(50) NULL,		
			DPISDB_HOST VARCHAR(50) NULL,		
			DPISDB_NAME VARCHAR(50) NULL,		
			DPISDB_USER VARCHAR(50) NULL,		
			DPISDB_PWD VARCHAR(50) NULL,		
			SITE_NAME VARCHAR(100) NULL,		
			SITE_BG_LEFT VARCHAR(100) NULL,		
			SITE_BG_LEFT_X SMALLINT(5) NULL,
			SITE_BG_LEFT_Y SMALLINT(5) NULL,
			SITE_BG_LEFT_W SMALLINT(5) NULL,
			SITE_BG_LEFT_H SMALLINT(5) NULL,
			SITE_BG_LEFT_ALPHA DECIMAL(4,2) NULL,
			SITE_BG VARCHAR(100) NULL,		
			SITE_BG_X SMALLINT(5) NULL,
			SITE_BG_Y SMALLINT(5) NULL,
			SITE_BG_W SMALLINT(5) NULL,
			SITE_BG_H SMALLINT(5) NULL,
			SITE_BG_ALPHA DECIMAL(4,2) NULL,
			SITE_BG_RIGHT VARCHAR(100) NULL,		
			SITE_BG_RIGHT_X SMALLINT(5) NULL,
			SITE_BG_RIGHT_Y SMALLINT(5) NULL,
			SITE_BG_RIGHT_W SMALLINT(5) NULL,
			SITE_BG_RIGHT_H SMALLINT(5) NULL,
			SITE_BG_RIGHT_ALPHA DECIMAL(4,2) NULL,
			HEAD_FONT_NAME VARCHAR(100) NULL,		
			HEAD_FONT_SIZE SMALLINT(5) NULL,
			CSS_NAME VARCHAR(100) NULL,		
			SITE_LEVEL SMALLINT(1) NULL,	
			PV_CODE VARCHAR(10) NULL,		
			HEAD_HEIGHT SMALLINT(5) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
		$db->send_cmd($cmd);
//		$db->show_error();

		$cmd = " select ORG_ID, CTRL_TYPE, PV_CODE from PER_CONTROL ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID = $data[ORG_ID];
		$CTRL_TYPE = $data[CTRL_TYPE];
		$PV_CODE = $data[PV_CODE];
		if (!$ORG_ID) $ORG_ID = "NULL";
		$UPDATE_USER = 1;

		$CSS_NAME = "style.css";
		if ($CTRL_TYPE==1) {
			$SITE_BG_LEFT = "images/top_left_ses.jpg";
			$SITE_BG = "images/top_bg_ses.jpg";
			$SITE_BG_RIGHT = "images/top_right_ses.jpg";
		} elseif ($CTRL_TYPE==2) { 
			$SITE_BG_LEFT = "images/top_left_prov.jpg";
			$SITE_BG = "images/top_bg_prov.jpg";
			$SITE_BG_RIGHT = "images/top_right_prov.jpg";
		} elseif (($CTRL_TYPE==3 && $TEMP_ORG_NAME=="กระทรวงเกษตรและสหกรณ์") || $TEMP_ORG_NAME=="สำนักงานปลัดกระทรวงเกษตรและสหกรณ์") {
			$SITE_BG_LEFT = "images/top_left_moac.jpg";
			$SITE_BG = "images/top_bg_moac.jpg";
			$SITE_BG_RIGHT = "images/top_right_moac.jpg";
			$SITE_NAME = "โปรแกรมระบบฐานข้อมูลสารสนเทศการบริหารทรัพยากรบุคคล";
		} elseif (($CTRL_TYPE==3 && $TEMP_ORG_NAME=="กระทรวงอุตสาหกรรม") || $TEMP_ORG_NAME=="สำนักงานปลัดกระทรวงอุตสาหกรรม") {
			$SITE_BG_LEFT = "images/top_left_moi.jpg";
			$SITE_BG = "images/top_bg_moi.jpg";
			$SITE_BG_RIGHT = "images/top_right_moi.jpg";
			$SITE_NAME = "โปรแกรมระบบฐานข้อมูลสารสนเทศการบริหารทรัพยากรบุคคล";
		} elseif ($TEMP_ORG_NAME=="กรมการปกครอง") {
			$SITE_BG_LEFT = "images/top_left_dopa.swf";
			$SITE_BG = "images/top_bg_dopa.jpg";
			$SITE_BG_RIGHT = "images/top_right_dopa.swf";
			$SITE_NAME = "ระบบฐานข้อมูลสารสนเทศการบริหารงานทรัพยากรบุคคล";
		} elseif ($TEMP_ORG_NAME=="กรมอุตสาหกรรมพื้นฐานและการเหมืองแร่") { 
			$SITE_BG_LEFT = "images/top_left_dpim.jpg";
			$SITE_BG = "images/top_bg_dpim.jpg";
			$SITE_BG_RIGHT = "images/top_right_dpim.jpg";
			$SITE_NAME = "โปรแกรมระบบฐานข้อมูลสารสนเทศการบริหารทรัพยากรบุคคล";
		} elseif ($TEMP_ORG_NAME=="สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม") {
			$SITE_BG_LEFT = "images/top_left_tisi.jpg";
			$SITE_BG = "images/top_bg_tisi.jpg";
			$SITE_BG_RIGHT = "images/top_right_tisi.jpg";
			$SITE_NAME = "โปรแกรมระบบฐานข้อมูลสารสนเทศการบริหารทรัพยากรบุคคล";
		} elseif ($TEMP_ORG_NAME=="สถาบันบัณฑิตพัฒนศิลป์") {
			$SITE_BG_LEFT = "images/top_left_bpi.jpg";
			$SITE_BG = "images/top_bg_bpi.jpg";
			$SITE_BG_RIGHT = "images/top_right_bpi.jpg";
			$SITE_NAME = "โปรแกรมระบบฐานข้อมูลสารสนเทศการบริหารทรัพยากรบุคคล";
		} elseif ($TEMP_ORG_NAME=="สำนักงาน ก.พ.xxxxxxxxxx") {
			$SITE_BG_LEFT = "images/top_left_51.swf";
			$SITE_BG = "images/top_bg_51.jpg";
			$SITE_BG_RIGHT = "images/top_right_51.swf";
		} else {
			$SITE_BG_LEFT = "images/top_left.jpg";
			$SITE_BG = "images/top_bg.jpg";
			$SITE_BG_RIGHT = "images/top_right.jpg";
		}
		$cmd = " INSERT INTO SITE_INFO (SITE_ID, ORG_ID, SITE_NAME, SITE_BG_LEFT, SITE_BG, SITE_BG_RIGHT, 
						  CSS_NAME, SITE_LEVEL, PV_CODE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, $ORG_ID, '$SITE_NAME', '$SITE_BG_LEFT', '$SITE_BG', '$SITE_BG_RIGHT', 
						  '$CSS_NAME', $CTRL_TYPE, '$PV_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
		$db->send_cmd($cmd);
		//$db->show_error() ;
	} // end if

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
	
	$sql = " select * from user_privilege ";
	$count_data = $db->send_cmd($sql);

	if(!$count_data){
		$db_insert = new connect_db($db_host, $db_name, $db_user, $db_pwd);

		$sql = " 	select 		a.menu_id as menu_id_lv0, b.menu_id as menu_id_lv1
						from		backoffice_menu_bar_lv0 a, backoffice_menu_bar_lv1 b
						where		a.menu_id=b.parent_id and a.langcode='TH' and b.langcode='TH'
						order by	a.menu_id, b.menu_id ";
		$db->send_cmd($sql);
		while($data = $db->get_array()){
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[menu_id_lv0] != $menu_id){
				$sql = 	"	insert into user_privilege
									(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, create_date, create_by, update_date, update_by)
									values
									(1, 1, $data[menu_id], 0, 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', $update_date, $update_by, $update_date, $update_by) ";
				$db_insert->send_cmd($sql);
				$menu_id = $data[menu_id_lv0];
			} // end if

			$sql = 	"	insert into user_privilege
								(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, create_date, create_by, update_date, update_by)
								values
								(1, 1, $data[menu_id_lv0], $data[menu_id_lv1], 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', $update_date, $update_by, $update_date, $update_by) ";
			$db_insert->send_cmd($sql);
		} // end while
	} // end if
	
	$username = trim($username);
	$password = trim($password);
	if ($command == "LOGIN" && $username != "" && $password != "") {
		$encrypt_password = md5($password);
		$ERR = 1;
		///////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////////////////////////////
		$cmd =  "select	create_by from user_detail where username = '$username'";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		if($data[0] == 'LDAP') {
			/// authenication by ldap 
			$cmd = "select config_name,config_value from ldap_config";
			$db->send_cmd($cmd);
			while($data = $db->get_array()) {
				$data = array_change_key_case($data, CASE_LOWER);
				$xx = $data[0];
				$ldap_cfg[$xx] = $data[1];
			}

			//$user = "CN=17 Temp,OU=Class_17,OU=Student,DC=nist,DC=ac,DC=th";
			//$pass = "";
			// connect to active directory
			$ad = ldap_connect($ldap_cfg['server_address'],$ldap_cfg['server_port']);
			if(!$ad) {
				die("Connect not connect to ".$ldap_cfg['server_address']);
			} else {
				$b = @ldap_bind($ad,"uid=".$username.",ou=People,dc=doeb,dc=go,dc=th",$password);
				if($b) {
					$cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' ";
				} else {
					/* $cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' ";  */

					$cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update
												from	user_detail a, user_group b
												where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' ";
				}
			}
		} else {
			$cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' ";			
		}
		///////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////////////////////////////
		
		/*
		$cmd =  "	select	a.id, a.fullname, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' ";
		$db->send_cmd($cmd);
		*/
//		echo $cmd;
		$result = $db->send_cmd($cmd);
//		$db->show_error();
		if ($result) {
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			// พงษ์ศักดิ์ เพิ่มเพื่อ check อายุ password
			$today = date("Y-m-d");
			$pwd_lastupd = $data[password_last_update];

			$cmd1 =" select config_value from system_config where config_name='password_age' ";
			$db->send_cmd($cmd1);
			$data1 = $db->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$pwd_age = trim($data1['config_value']);

			if ($pwd_age != "0") { // ถ้ากำหนดที่ system_config ที่ตัวแปร password_age ให้ = 0 จะไม่มีการ check อายุ password
				if (strpos(strtolower($pwd_age),"d") !== false) {
					$pwd_age_type = "d";
					$pwd_diff = substr($pwd_age,0,strlen($pwd_age)-1);
				} else if (strpos(strtolower($pwd_age),"y") !== false) {
					$pwd_age_type = "y";
					$pwd_diff = substr($pwd_age,0,strlen($pwd_age)-1);
				} else if (strpos(strtolower($pwd_age),"m") !== false) {
					$pwd_age_type = "m";
					$pwd_diff = substr($pwd_age,0,strlen($pwd_age)-1);
				} else {
					$pwd_age_type = "m";
					$pwd_diff = $pwd_age;	
				}
				$diff = date_difference($pwd_lastupd, $today, $pwd_age_type);
//				echo "$pwd_age::$diff::$pwd_lastupd::$today<br>";
			} // end if ($pwd_age!="0") {
				
			if ($pwd_age != "0" && (real)$diff > (real)$pwd_diff) {
//				echo "password expire...";
				$ERR = 2;
			} else {
			// จบ ส่วนเพิ่ม พงษ์ศักดิ์ เพิ่มเพื่อ check อายุ password
			$SESS_USERNAME = $username;
			$SESS_USERADDR = $data[address];
			$SESS_USERID = $data[id];
			$user_link_id = $data[user_link_id];
			if(!trim($user_link_id)){ 
				$SESS_PER_ID = "";
				$SESS_FIRSTNAME = $data[fullname];
			}else{
				//หาข้อมูลของผู้ล็อกอิน
				$SESS_PER_ID = $user_link_id;
				if($DPISDB=="odbc")
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
									 from 		PER_PERSONAL a
									 				left join PER_PRENAME b on a.PN_CODE=b.PN_CODE
									 where 	a.PER_ID=". $SESS_PER_ID;
				elseif($DPISDB=="oci8")
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
									 from 		PER_PERSONAL a, PER_PRENAME b 
									 where 	a.PN_CODE=b.PN_CODE(+) and a.PER_ID=". $SESS_PER_ID;
				elseif($DPISDB=="mysql")
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
									 from 		PER_PERSONAL a
									 				left join PER_PRENAME b on a.PN_CODE=b.PN_CODE
									 where 	a.PER_ID=". $SESS_PER_ID;
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data_dpis = $db_dpis->get_array();
				$SESS_FIRSTNAME = $data_dpis[PN_NAME] . (trim($data[PN_NAME])?" ":"") . $data_dpis[PER_NAME] ." ". $data_dpis[PER_SURNAME];
			} // end if
			$SESS_PER_TYPE=$data[group_per_type];
			$SESS_ORG_STRUCTURE=$data[group_org_structure];
			//ตรวจสอบการล็อกอินว่าเข้ามาเป็นประเภทอะไร ?
			$ALL_DEPARTMENT_ID="";
			$SESS_USERGROUP = $data[group_id];
			$SESS_USERGROUP_LEVEL = $data[group_level];

			switch($SESS_USERGROUP_LEVEL){
				case 2 :
					$SESS_PROVINCE_CODE = $data[pv_code];
					break;
				case 3 :
					$SESS_MINISTRY_ID = $data[org_id];
					//หากรมทั้งหมดในกระทรวงนี้ เพื่อตรวจสอบว่ามีกรมไหนบ้างที่อนุญาติให้ใช้ PKG ได้
					$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$SESS_MINISTRY_ID ";
					if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
					$db_dpis->send_cmd($cmd);
			//		$db_dpis->show_error();
					while($data2 = $db_dpis->get_array()){
						//กำหนดค่าเริ่มต้น สำหรับ PKG
						$SPKG1[$data2[ORG_ID]]="Y";		//$SPKG1[$data2[ORG_ID]]="N";	
						$SPKG2[$data2[ORG_ID]]="Y";		//$SPKG2[$data2[ORG_ID]]="N";	
						$SCOMPETENCY[$data2[ORG_ID]]="N";
						$ALL_DEPARTMENT_ID .= $data2[ORG_ID].",";
					}
					break;
				case 4 :
					$SESS_DEPARTMENT_ID = $data[org_id];
					//กำหนดค่าเริ่มต้น สำหรับ PKG
					$SPKG1[$SESS_DEPARTMENT_ID]="Y";		//$SPKG1[$SESS_DEPARTMENT_ID]="N";	  
					$SPKG2[$SESS_DEPARTMENT_ID]="Y";		//$SPKG2[$SESS_DEPARTMENT_ID]="N";	
					$SCOMPETENCY[$SESS_DEPARTMENT_ID]="N";	
					$ALL_DEPARTMENT_ID = $SESS_DEPARTMENT_ID.",";
					break;
				case 5 :
					$SESS_ORG_ID = $data[org_id];
					//หาสำนัก/กองทั้งหมดในกรมนี้ เพื่อตรวจสอบว่ามีสำนัก/กองไหนบ้างที่อนุญาติให้ใช้ PKG ได้
					$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
					if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
					$db_dpis->send_cmd($cmd);
			//		$db_dpis->show_error();
					while($data2 = $db_dpis->get_array()){
						//กำหนดค่าเริ่มต้น สำหรับ PKG
						$SPKG1[$data2[ORG_ID_REF]]="Y";		//$SPKG1[$data2[ORG_ID_REF]]="N";	
						$SPKG2[$data2[ORG_ID_REF]]="Y";		//$SPKG2[$data2[ORG_ID_REF]]="N";	
						$SCOMPETENCY[$data2[ORG_ID_REF]]="N";
						$ALL_DEPARTMENT_ID .= $data2[ORG_ID_REF].",";
					}
					break;
					case 6 :
						$SESS_ORG_ID_1 = $data[org_id];
					break;
			} // end switch case

			if($SESS_ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID_1 ";
				if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_ORG_NAME_1 = $data[ORG_NAME];
				$SESS_ORG_ID = $data[ORG_ID_REF];	
			} // end if

			if($SESS_ORG_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
				if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_ORG_NAME = $data[ORG_NAME];
				$SESS_DEPARTMENT_ID = $data[ORG_ID_REF];	
			} // end if

			if($SESS_DEPARTMENT_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_DEPARTMENT_ID ";
				if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_DEPARTMENT_NAME = $data[ORG_NAME];
				$SESS_MINISTRY_ID = $data[ORG_ID_REF];	
			} // end if
		
			if($SESS_MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$SESS_MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_MINISTRY_NAME = $data[ORG_NAME];
			} // end if
		
			if($SESS_PROVINCE_CODE){
				$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$SESS_PROVINCE_CODE' ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_PROVINCE_NAME = $data[PV_NAME];
			} // end if

			$SESS_INHERITGROUP = $data[inherit_group];
			$ACCESSIBLE_GROUP = $SESS_USERGROUP.(trim($SESS_INHERITGROUP)?",$SESS_INHERITGROUP":"");

			if($db_type=="mysql"){ 
				$cmd = " insert into user_last_access (user_id, username, fullname, last_login, from_ip) values ($SESS_USERID, '$SESS_USERNAME', '$SESS_FIRSTNAME', NOW(), '$REMOTE_ADDR') ";
			}elseif($db_type == "mssql"){ 
				$cmd = " insert into user_last_access (user_id, username, fullname, last_login, from_ip) values ($SESS_USERID, '$SESS_USERNAME', '$SESS_FIRSTNAME', GETDATE(), '$REMOTE_ADDR') ";
			}elseif($db_type == "oci8"){ 
				$cmd = " insert into user_last_access (user_id, username, fullname, last_login, from_ip) values ($SESS_USERID, '$SESS_USERNAME', '$SESS_FIRSTNAME', TO_DATE('".date("Y-m-d h:i:s")."', 'YYYY-MM-DD HH24:MI:SS'), '$REMOTE_ADDR') ";
			}
			$db->send_cmd($cmd);
//			$db->show_error();

			//รายชื่อกรมทั้งหมดในกระทรวง ถ้าล็อกอินเป็นกระทรวง
			if(trim($ALL_DEPARTMENT_ID)){	
				$ALL_DEPARTMENT_ID=substr($ALL_DEPARTMENT_ID,0,-1);		//ตัด comma ตัวท้ายทิ้ง
				$ARR_ALL_DEPARTMENT_ID= explode(",",$ALL_DEPARTMENT_ID);	
			}
			
			//เซตระดับของสำนักกอง
			$SESS_ORG_SETLEVEL = (trim($SESS_ORG_SETLEVEL))?  $SESS_ORG_SETLEVEL : 2;	 //ถ้ายังไม่มีการ set ให้เป็นต่ำกว่า 2 ระดับ
			$cmd =" select config_value from system_config where config_name='ORG_SETLEVEL' ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SESS_ORG_SETLEVEL=trim($data['config_value']);
			
			//วิธีการประเมินสมรรถนะ
			$cmd =" select config_value from system_config where config_name='COMPETENCY_METHOD' ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SESS_COMPETENCY_METHOD=str_replace("|","",trim($data['config_value']));
			
			$cmd =" select config_value from system_config where config_name='PKG1' ";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$ARR_PKG1EXIST=explode(",",$data['config_value']);
				if(is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_PKG1EXIST)){		//ทั้งกระทรวง/กรม
					for($i=0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++){	
						if(in_array(md5("PKG1".$ARR_ALL_DEPARTMENT_ID[$i]),$ARR_PKG1EXIST)){
							$SPKG1[$ARR_ALL_DEPARTMENT_ID[$i]]="Y";
							$PAGE_AUTH_GRAPH="Y";
						}
					}
				}
			} //end count
		
			$cmd =" select config_value from system_config where config_name='PKG2' ";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$ARR_PKG2EXIST=explode(",",$data['config_value']);
				if(is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_PKG2EXIST)){		//ทั้งกระทรวง/กรม
					for($i=0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++){	
						if(in_array(md5("PKG2".$ARR_ALL_DEPARTMENT_ID[$i]),$ARR_PKG2EXIST)){
							$SPKG2[$ARR_ALL_DEPARTMENT_ID[$i]]="Y";
						}
					}
				}
			} //end count

			$cmd =" select config_value from system_config where config_name='COMPETENCY' ";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$ARR_COMPETENCYEXIST=explode(",",$data['config_value']);
				if(is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_COMPETENCYEXIST)){		//ทั้งกระทรวง/กรม
					for($i=0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++){	
						if(in_array(md5("COMPETENCY".$ARR_ALL_DEPARTMENT_ID[$i]),$ARR_COMPETENCYEXIST)){
							$SCOMPETENCY[$ARR_ALL_DEPARTMENT_ID[$i]]="Y";
						}
					}
				}
			} //end count

			$MSG_HEADER = 'สรุปการปรับปรุงระบบ DPIS 4.0.0.38';
			$MSG_DETAIL = '
P0101 ข้อมูลบุคคล แสดงจำนวนไม่ถูกต้อง เมื่อตรวจสอบกับเมนู A03 จำนวนไม่เท่ากัน
P0311 โปรแกรมจัดคนลงตามส่วนราชการใหม่ เมื่อยืนยันแล้ว ระบบไม่ update ข้อมูลให้
R0406 รายงานประวัติข้าราชการ วันเกษียณอายุราชการมีทั้งวันที่ 1 ต.ค. และ 30 ก.ย. ที่ถูกต้อง ต้องเป็นวันที่ 1 ต.ค.
P0307 หลังจากยืนยันคำสั่งแล้ว ในประวัติตรงส่วนหน่วยงานไม่บันทึกให้ / ข้อมูลทั่วไป ระดับตำแหน่งไม่แสดง
R0201 จำนวนข้าราชการแยกตามระดับตำแหน่ง เมื่อออกรายงานเป็นระดับสำนักฯ และจำแนกข้อมูลตามต่ำกว่าสำนัก / กอง 1 ระดับ บรรทัดสุดท้ายแสดงข้อมูลชื่อฝ่ายซ้ำกัน
R0310 รายชื่อข้าราชการที่บรรจุในปีงบประมาณ ในส่วนของการจำแนกข้อมูล ให้สามารถเลือกเงื่อนไขได้
R0407 รายชื่อข้าราชการ ในส่วนของลูกจ้างประจำ,พนักงานราชการ แสดงผลไม่ถูกต้อง
การเลื่อนเงินเดือนลูกจ้าง เมื่อทำบัญชีแล้ว ยืนยัน ในประวัติรายการล่าสุดที่ยืนไม่ใส่ให้ว่าเป็นรายการล่าสุด
S0201 ตำแหน่งข้าราชการ ปุ่มแสดงทั้งหมด ใช้ไม่ได้ 
S0201 ตำแหน่งข้าราชการ ส่งออกเป็น Excel หัวตารางไปทับข้อมูล คนที่ 1
S0203 แสดงข้อมูลซ้ำ เฉพาะตำแหน่งที่มีคนเคยครอง แต่พ้นไปแล้ว
A06 ทำบัญชีเลื่อนเงินเดือนข้าราชการ กรณีเพิ่มข้อมูลเอง ระบบ นำข้อมูลคนที่พ้นจากส่วนราชการแล้ว  มาเข้าสู่บัญชีด้วย
			';
			$MSG_SOURCE = 'สำนักงาน ก.พ.';
			$MSG_POST_DATE = '2011-10-01';
			$MSG_START_DATE = '2011-10-01'; 
			$MSG_FINISH_DATE = '2012-10-01'; 
			$USER_ID = 1; 
			$MSG_TYPE = 0;
			$MSG_DOCUMENT = ''; 
			$MSG_ORG_NAME = '';
			$MSG_SHOW = 1;
			$UPDATE_USER = 1;

			$cmd = " select MSG_ID from PER_MESSAGE where MSG_HEADER = 'สรุปการปรับปรุงระบบ DPIS 4.0.0.38' ";
			$count_data = $db_dpis->send_cmd($cmd);
			if (!$count_data) {
				$cmd = " SELECT MAX(MSG_ID) as MSG_ID FROM PER_MESSAGE ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd<br>";
				$data_dpis = $db_dpis->get_array();
				$MSG_ID = $data_dpis[MSG_ID] + 1;

				$cmd = " INSERT INTO PER_MESSAGE(MSG_ID, MSG_HEADER, MSG_DETAIL, MSG_SOURCE, MSG_POST_DATE, MSG_START_DATE, 
								MSG_FINISH_DATE, USER_ID, MSG_TYPE, MSG_DOCUMENT, MSG_ORG_NAME, MSG_SHOW, UPDATE_USER, UPDATE_DATE)
								VALUES ($MSG_ID, '$MSG_HEADER', '$MSG_DETAIL', '$MSG_SOURCE', '$MSG_POST_DATE', '$MSG_START_DATE', 
								'$MSG_FINISH_DATE', $USER_ID, $MSG_TYPE, '$MSG_DOCUMENT', '$MSG_ORG_NAME', $MSG_SHOW, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);

				$cmd = " INSERT INTO PER_MESSAGE_USER(MSG_ID, USER_ID, MSG_STATUS, MSG_READ, UPDATE_USER, UPDATE_DATE)
									VALUES ($MSG_ID, 1, 0, NULL, $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
		}
			
			srand((double)microtime()*1000000);
			$session_id = md5(uniqid(rand()));
			session_id($session_id);
			session_start();
//			setcookie(session_name(), session_id($session_id), time()+28800, "/");
			$CHK_PASS = 1;
			session_register("CHK_PASS");
			session_register("SESS_USERNAME");
			session_register("SESS_USERADDR");
			session_register("SESS_USERID");
			session_register("SESS_PER_ID");
			session_register("SESS_FIRSTNAME");
			session_register("SESS_LASTNAME");
			session_register("SESS_USERGROUP");
			session_register("SESS_USERGROUP_LEVEL");
			session_register("SESS_PROVINCE_CODE");
			session_register("SESS_PROVINCE_NAME");
			session_register("SESS_MINISTRY_ID");
			session_register("SESS_MINISTRY_NAME");
			session_register("SESS_DEPARTMENT_ID");
			session_register("SESS_DEPARTMENT_NAME");
			session_register("SESS_ORG_ID");
			session_register("SESS_ORG_NAME");
			session_register("SESS_ORG_ID_1");
			session_register("SESS_ORG_NAME_1");		
			session_register("SESS_INHERITGROUP");
			session_register("ACCESSIBLE_GROUP");
			session_register("SPKG1");
			session_register("SPKG2");
			session_register("SCOMPETENCY");
			session_register("SESS_PER_TYPE");
			session_register("SESS_ORG_STRUCTURE");
			session_register("PAGE_AUTH_GRAPH");
			session_register("SESS_ORG_SETLEVEL");
			session_register("SESS_COMPETENCY_METHOD");

//			echo "Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main.html<br>";
			if ($MENU_TYPE==2) 
				//header("Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main_1.html");
                            header("Location: /$session_id/".$virtual_site."admin/main_1.html");
			else
				//header("Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main.html");
                            header("Location: /$session_id/".$virtual_site."admin/main.html");
			} // end if check password last updatedate diff
		} // end if read user_detail pass == if ($result)
	}elseif($command == "LOGIN" && $username != "" && $password == ""){
		$ERR = 1;
	} // end if
	
	//echo "$SESS_PER_ID | $SESS_USERID :: $SESS_MINISTRY_NAME :: $SESS_DEPARTMENT_NAME :: $SESS_ORG_NAME :: $SESS_ORG_NAME_1 :: $SESS_PROVINCE_NAME :: $SESS_ORG_SETLEVEL :: $SESS_COMPETENCY_METHOD :: $SESS_PER_TYPE :: $SESS_ORG_SETLEVEL <br>";

} //end command1 = UPDATE
?>
