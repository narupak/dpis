<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/load_per_control.php");

	$cmd = " ALTER TABLE user_group ADD group_per_type smallint(1) ";
	$db->send_cmd($cmd);
	//$db->show_error();

	$sql = "select * from user_privilege";
	$count_data = $db->send_cmd($sql);
	if(!$count_data){
		$db_insert = new connect_db($db_host, $db_name, $db_user, $db_pwd);

		$sql = " 	select 		a.menu_id as menu_id_lv0, b.menu_id as menu_id_lv1
						from		backoffice_menu_bar_lv0 a, backoffice_menu_bar_lv1 b
						where		a.menu_id=b.parent_id and a.langcode='TH' and b.langcode='TH'
						order by	a.menu_id, b.menu_id ";
		$db->send_cmd($sql);
		while($data = $db->get_object()){
			$data = array_change_key_case($data, CASE_LOWER);
			if($data->menu_id_lv0 != $menu_id){
				$sql = 	"	insert into user_privilege
									(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, create_date, create_by, update_date, update_by)
									values
									(1, 1, $data->menu_id, 0, 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', NOW(), 'admin', NOW(), 'admin') ";
				$db_insert->send_cmd($sql);
				$menu_id = $data->menu_id_lv0;
			} // end if

			$sql = 	"	insert into user_privilege
								(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, create_date, create_by, update_date, update_by)
								values
								(1, 1, $data->menu_id_lv0, $data->menu_id_lv1, 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', NOW(), 'admin', NOW(), 'admin') ";
			$db_insert->send_cmd($sql);
		} // end while
	} // end if
	
	$username = trim($username);
	$password = trim($password);
	if ($command == "LOGIN" && $username != "" && $password != "") {
		$encrypt_password = md5($password);
		$ERR = 1;
		$cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id, b.group_per_type
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' ";
//		$db->send_cmd($cmd);
//		$db->show_error();
//		echo "$cmd<br>";
		if ($db->send_cmd($cmd)) {
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
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
					$db_dpis->send_cmd($cmd);
			//		$db_dpis->show_error();
					while($data2 = $db_dpis->get_array()){
						//กำหนดค่าเริ่มต้น สำหรับ PKG
						$SPKG1[$data2[ORG_ID]]="N";	
						$SPKG2[$data2[ORG_ID]]="N";	
						$SCOMPETENCY[$data2[ORG_ID]]="N";
						$ALL_DEPARTMENT_ID .= $data2[ORG_ID].",";
					}
					break;
				case 4 :
					$SESS_DEPARTMENT_ID = $data[org_id];
					//กำหนดค่าเริ่มต้น สำหรับ PKG
					$SPKG1[$SESS_DEPARTMENT_ID]="N";	  
					$SPKG2[$SESS_DEPARTMENT_ID]="N";	
					$SCOMPETENCY[$SESS_DEPARTMENT_ID]="N";	
					$ALL_DEPARTMENT_ID = $SESS_DEPARTMENT_ID.",";
					break;
				case 5 :
					$SESS_ORG_ID = $data[org_id];
					//หาสำนัก/กองทั้งหมดในกรมนี้ เพื่อตรวจสอบว่ามีสำนัก/กองไหนบ้างที่อนุญาติให้ใช้ PKG ได้
					$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
					$db_dpis->send_cmd($cmd);
			//		$db_dpis->show_error();
					while($data2 = $db_dpis->get_array()){
						//กำหนดค่าเริ่มต้น สำหรับ PKG
						$SPKG1[$data2[ORG_ID_REF]]="N";	
						$SPKG2[$data2[ORG_ID_REF]]="N";	
						$SCOMPETENCY[$data2[ORG_ID_REF]]="N";
						$ALL_DEPARTMENT_ID .= $data2[ORG_ID_REF].",";
					}
					break;
			} // end switch case

			if($SESS_ORG_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$SESS_ORG_NAME = $data[ORG_NAME];
				$SESS_DEPARTMENT_ID = $data[ORG_ID_REF];	
			} // end if

			if($SESS_DEPARTMENT_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_DEPARTMENT_ID ";
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
			
			$cmd ="select config_value from system_config where config_name='PKG1' order by config_id";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
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
		
			$cmd ="select config_value from system_config where config_name='PKG2' order by config_id";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
				$ARR_PKG2EXIST=explode(",",$data['config_value']);
				if(is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_PKG2EXIST)){		//ทั้งกระทรวง/กรม
					for($i=0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++){	
						if(in_array(md5("PKG2".$ARR_ALL_DEPARTMENT_ID[$i]),$ARR_PKG2EXIST)){
							$SPKG2[$ARR_ALL_DEPARTMENT_ID[$i]]="Y";
						}
					}
				}
			} //end count

			$cmd ="select config_value from system_config where config_name='COMPETENCY' order by config_id";
			$count = $db->send_cmd($cmd);
	//		$db->show_error();
			if($count > 0){
				$data = $db->get_array();
				$ARR_COMPETENCYEXIST=explode(",",$data['config_value']);
				if(is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_COMPETENCYEXIST)){		//ทั้งกระทรวง/กรม
					for($i=0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++){	
						if(in_array(md5("COMPETENCY".$ARR_ALL_DEPARTMENT_ID[$i]),$ARR_COMPETENCYEXIST)){
							$SCOMPETENCY[$ARR_ALL_DEPARTMENT_ID[$i]]="Y";
						}
					}
				}
			} //end count

			$MSG_HEADER = 'สรุปการปรับปรุงระบบ DPIS 4.0.0.22';
			$MSG_DETAIL = '
รายงาน Excel ที่ A06 บัญชีแนบท้ายคำสั่งเลื่อนเงินเดือน หัวบัญชียังใช้คำว่า เลื่อนขั้นเงินเดือน
รายงาน R0406 ประวัติ ข้าราชการ/ลูกจ้างประจำ/พนักงานราชการ ตรงประวัติการฝึกอบรม สัมนา และ ดูงาน กับ ประวัติ การสมรส การแสดงผลของตาราง ขาด ๆ หาย ไป
เมนู P0101 รายละเอียดข้อมูลข้าราชการ / ลูกจ้างประจำ ข้อมูลตรง ต่ำกว่าสำนัก/กอง 1 ระดับ ไมแสดงข้อมูล
เมนู P0101 รายละเอียดข้อมูลข้าราชการ / ลูกจ้างประจำ ในส่วนของข้อมูลบุคคลจะไม่แสดงข้อมูล กรณีที่เป็นข้อมูลของลูกจ้างประจำ
เมนู P0101 รายละเอียดข้อมูลข้าราชการ / ลูกจ้างประจำ ในส่วนของประวัติการดำรงตำแหน่ง ให้สามารถใส่ข้อมูลของ ต่ำกว่าสำนัก / กอง 1 ระดับ และ ต่ำกว่าสำนัก / กอง 2 ระดับ ได้ทั้งแบบ คีย์เอง และแบบเลือกจาก list box
P0101 ประวัติการรับเงินเดือน ให้สามารถใส่ข้อมูล เปอร์เซ็นต์ที่เลื่อน ทศนิยม 4 ตำแหน่งได้
เมนู S0402 เมื่อทำการแก้ไขข้อมูลรายละเอียดบัญชีขอปรับปรุง/ตัดโอนตำแหน่ง เมื่อทำการเปลี่ยนแปลงข้อมูล ต่ำกว่าสำนัก/กอง 1 ระดับแล้วเลือก อนุมัติ ข้อมูลจะต้องไปแก้ไขที่เมนู S0201 ด้วย
เมนู P0101 นำปุ่ม ถือจ่าย ออกไป (ใช้แต่กรณีกรมการปกครองเท่านั้น)
';
			$MSG_SOURCE = 'สำนักงาน ก.พ.';
			$MSG_POST_DATE = '2010-06-01';
			$MSG_START_DATE = '2010-06-01'; 
			$MSG_FINISH_DATE = '2011-06-01'; 
			$USER_ID = 1; 
			$MSG_TYPE = 0;
			$MSG_DOCUMENT = ''; 
			$MSG_ORG_NAME = '';
			$MSG_SHOW = 1;
			$UPDATE_USER = 1;
			$UPDATE_DATE = date("Y-m-d H:i:s");

			$cmd = " select MSG_ID from PER_MESSAGE where MSG_ID = 1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data)
				$cmd = " UPDATE PER_MESSAGE SET MSG_HEADER = '$MSG_HEADER', 
																								  MSG_DETAIL = '$MSG_DETAIL', 
																								  MSG_POST_DATE = '$MSG_POST_DATE', 
																								  MSG_START_DATE = '$MSG_START_DATE', 
																								  MSG_FINISH_DATE = '$MSG_FINISH_DATE', 
																								  UPDATE_USER = $UPDATE_USER, 
																								  UPDATE_DATE = '$UPDATE_DATE' 
								WHERE MSG_ID = 1 ";
			else
				$cmd = " INSERT INTO PER_MESSAGE(MSG_ID, MSG_HEADER, MSG_DETAIL, MSG_SOURCE, MSG_POST_DATE, MSG_START_DATE, 
								MSG_FINISH_DATE, USER_ID, MSG_TYPE, MSG_DOCUMENT, MSG_ORG_NAME, MSG_SHOW, UPDATE_USER, UPDATE_DATE)
								VALUES (1, '$MSG_HEADER', '$MSG_DETAIL', '$MSG_SOURCE', '$MSG_POST_DATE', '$MSG_START_DATE', 
								'$MSG_FINISH_DATE', $USER_ID, $MSG_TYPE, '$MSG_DOCUMENT', '$MSG_ORG_NAME', $MSG_SHOW, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select MSG_READ from PER_MESSAGE_USER where MSG_ID = 1 and USER_ID = 1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			if ($count_data)
				$cmd = " UPDATE PER_MESSAGE_USER SET MSG_STATUS = 0, 
																								  MSG_READ = NULL, 
																								  UPDATE_USER = $UPDATE_USER, 
																								  UPDATE_DATE = '$UPDATE_DATE' 
								WHERE MSG_ID = 1 and USER_ID = 1 ";
			else
				$cmd = " INSERT INTO PER_MESSAGE_USER(MSG_ID, USER_ID, MSG_STATUS, MSG_READ, UPDATE_USER, UPDATE_DATE)
									VALUES (1, 1, 0, NULL, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

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
			session_register("SESS_INHERITGROUP");
			session_register("ACCESSIBLE_GROUP");
			session_register("SPKG1");
			session_register("SPKG2");
			session_register("SCOMPETENCY");
			session_register("SESS_PER_TYPE");
			session_register("PAGE_AUTH_GRAPH");

//			echo "$SESS_PER_ID | $SESS_USERID :: $SESS_MINISTRY_NAME :: $SESS_DEPARTMENT_NAME :: $SESS_ORG_NAME :: $SESS_PROVINCE_NAME<br>";
//			echo "Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main.html<br>";
			//header("Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main.html");
                        header("Location: /$session_id/".$virtual_site."admin/main.html");
		} 
	}elseif($command == "LOGIN" && $username != "" && $password == ""){
		$ERR = 1;
	} // end if
?>
