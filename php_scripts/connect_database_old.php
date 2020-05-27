<?
	error_reporting(1);

	$db_type			= 	"mysql";
	$db_host 			= 	"localhost";
	$db_name 		= 	"dpis35";
	$db_user 			= 	"root";
	$db_pwd			= 	"";
/*
	$db_type			= 	"oci8";
	$db_host 			= 	"localhost";
	$db_name 		= 	"ocsc";
	$db_user 			= 	"dopa";
	$db_pwd			= 	"dopa";
*/
	$SERVER_TYPE = explode("/", $_SERVER['SERVER_SOFTWARE']);
	if( substr_count($SERVER_TYPE[0], "Apache") ){
		$ROOTPATH = $_SERVER['DOCUMENT_ROOT'];
	}elseif( substr_count($SERVER_TYPE[0], "IIS") ){
		$ROOTPATH = $_SERVER['APPL_PHYSICAL_PATH'];
//		$virtual_site = "DREAM/";
	}

	include("$ROOTPATH/php_scripts/connect_".$db_type.".php");
	if($db_type=="mysql"){
		class connect_db extends connect_mysql { };
	}elseif($db_type=="mssql"){
		class connect_db extends connect_mssql { };
	}elseif($db_type=="oci8"){
		class connect_db extends connect_oci8 { };
	} // end if
	$db = new connect_db($db_host, $db_name, $db_user, $db_pwd);
//	echo "Test Connect : $db : $db->link_con : $PHP_SELF ";
	if(!$db->link_con) die("Cannot connect to ".strtoupper($db_type)." database");
	
	if($command == "UPDATEDPISDB"){
		$cmd = " update system_config set config_value='$CH_DPISDB' where config_name='DPISDB' ";
		$db->send_cmd($cmd);
		
		$cmd = " update system_config set config_value='$ch_dpisdb_host' where config_name='dpisdb_host' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_name' where config_name='dpisdb_name' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_user' where config_name='dpisdb_user' ";
		$db->send_cmd($cmd);

		$cmd = " update system_config set config_value='$ch_dpisdb_pwd' where config_name='dpisdb_pwd' ";
		$db->send_cmd($cmd);
	
		if(!$CH_ATTDB) $CH_ATTDB = $CH_DPISDB;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', 'ประเภทฐานข้อมูลเครื่องบันทึกเวลา') ";
		$db->send_cmd($cmd);

		if(!$ch_attdb_host) $ch_attdb_host = $ch_dpisdb_host;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (16, 'attdb_host', '$ch_attdb_host', 'Time DB Server IP') ";
		$db->send_cmd($cmd);

		if(!$ch_attdb_name) $ch_attdb_name = $ch_dpisdb_name;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (17, 'attdb_name', '$ch_attdb_name', 'Time DB Name') ";
		$db->send_cmd($cmd);

		if(!$ch_attdb_user) $ch_attdb_user = $ch_dpisdb_user;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (18, 'attdb_user', '$ch_attdb_user', 'Time DB User') ";
		$db->send_cmd($cmd);

		if(!$ch_attdb_pwd) $ch_attdb_pwd = $ch_dpisdb_pwd;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (19, 'attdb_pwd', '$ch_attdb_pwd', 'Time DB Password') ";
		$db->send_cmd($cmd);
	
		if(!$CH_DPIS35DB) $CH_DPIS35DB = $CH_DPISDB;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', 'ประเภทฐานข้อมูลที่ต้องการถ่ายโอน') ";
		$db->send_cmd($cmd);

		if(!$ch_dpis35db_host) $ch_dpis35db_host = $ch_dpisdb_host;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (22, 'dpis35db_host', '$ch_dpis35db_host', 'Convert DB Server IP') ";
		$db->send_cmd($cmd);

		if(!$ch_dpis35db_name) $ch_dpis35db_name = $ch_dpisdb_name;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (23, 'dpis35db_name', '$ch_dpis35db_name', 'Convert DB Name') ";
		$db->send_cmd($cmd);

		if(!$ch_dpis35db_user) $ch_dpis35db_user = $ch_dpisdb_user;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (24, 'dpis35db_user', '$ch_dpis35db_user', 'Convert DB User') ";
		$db->send_cmd($cmd);

		if(!$ch_dpis35db_pwd) $ch_dpis35db_pwd = $ch_dpisdb_pwd;
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (25, 'dpis35db_pwd', '$ch_dpis35db_pwd', 'Convert DB Password') ";
		$db->send_cmd($cmd);
	} // end if
	
	if($command == "UPDATEATTDB"){
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', 'ประเภทฐานข้อมูลเครื่องบันทึกเวลา') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (16, 'attdb_host', '$ch_attdb_host', 'Time DB Server IP') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (17, 'attdb_name', '$ch_attdb_name', 'Time DB Name') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (18, 'attdb_user', '$ch_attdb_user', 'Time DB User') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (19, 'attdb_pwd', '$ch_attdb_pwd', 'Time DB Password') ";
		$db->send_cmd($cmd);
	} // end if
	
	if($command == "UPDATEDPIS35DB"){
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', 'ประเภทฐานข้อมูลที่ต้องการถ่ายโอน') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (22, 'dpis35db_host', '$ch_dpis35db_host', 'Convert DB Server IP') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (23, 'dpis35db_name', '$ch_dpis35db_name', 'Convert DB Name') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (24, 'dpis35db_user', '$ch_dpis35db_user', 'Convert DB User') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (25, 'dpis35db_pwd', '$ch_dpis35db_pwd', 'Convert DB Password') ";
		$db->send_cmd($cmd);
	} // end if
	
	if($command == "UPDATESYSTEMPARAMETER"){
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (10, 'data_per_page', '$ch_data_per_page', 'จำนวนเรคอร์ดต่อหน้า') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (11, 'WEIGHT_KPI', '$CH_WEIGHT_KPI', '% ผลการประเมินของข้าราชการ - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (12, 'WEIGHT_COMPETENCE', '$CH_WEIGHT_COMPETENCE', '% ผลการประเมินของข้าราชการ - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (13, 'WEIGHT_OTHER', '$CH_WEIGHT_OTHER', '% ผลการประเมินของข้าราชการ - อื่น ๆ') ";
		$db->send_cmd($cmd);
/*
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (13, 'BACKUP_PATH', '$CH_BACKUP_PATH', 'สำเนาฐานข้อมูล / เรียกคืนข้อมูล') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (14, 'TRANSFER_PATH', '$CH_TRANSFER_PATH', 'ถ่ายโอน / รับโอน ข้อมูลรายบุคคล') ";
		$db->send_cmd($cmd);
*/		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (20, 'RPT_N', '$CH_RPT_N', 'พระราชบัญญัติระเบียบข้าราชการพลเรือน พ.ศ. 2551') ";
		$db->send_cmd($cmd);
		
//		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
//						values (26, 'SA_SU_ABSENT', '$CH_SA_SU_ABSENT', 'การเพิ่ม/แก้ไข และนับวันลาเสาร์ อาทิตย์') ";
//		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (14, 'COMPETENCY_SCALE', '$CH_COMPETENCY_SCALE', 'มาตรวัดการประเมินสมรรถนะ') ";
		$db->send_cmd($cmd);
		
		//รูปแบบเลขประจำตัวประชาชน
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (29, 'CARD_NO_DISPLAY', '$CH_CARD_NO_DISPLAY', 'การแสดงผลเลขประจำตัวประชาชน') ";
		$db->send_cmd($cmd);
		
		//รูปแบบปุ่มกด
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (30, 'BUTTON_DISPLAY', '$CH_BUTTON_DISPLAY', 'การแสดงผลปุ่มกด') ";
		$db->send_cmd($cmd);
		
		//รูปแบบการเรียงข้อมูลบุคคล
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (31, 'PER_ORDER_BY', '$CH_ORDER_BY', 'การเรียงข้อมูลบุคคล') ";
		$db->send_cmd($cmd);
		
		//ถือจ่าย
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (32, 'PAYMENT_FLAG', '$CH_PAYMENT_FLAG', 'ถือจ่าย') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (33, 'WEIGHT_KPI_E', '$CH_WEIGHT_KPI_E', '% ผลการประเมินของพนักงานราชการทั่วไป - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (34, 'WEIGHT_COMPETENCE_E', '$CH_WEIGHT_COMPETENCE_E', '% ผลการประเมินของพนักงานราชการทั่วไป - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (35, 'WEIGHT_KPI_S', '$CH_WEIGHT_KPI_S', '% ผลการประเมินของพนักงานราชการพิเศษ - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (36, 'WEIGHT_COMPETENCE_S', '$CH_WEIGHT_COMPETENCE_S', '% ผลการประเมินของพนักงานราชการพิเศษ - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (37, 'PRINT_FONT', '$CH_PRINT_FONT', 'รูปแบบอักษรในการพิมพ์รายงาน') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (38, 'ATTACH_FILE', '$CH_ATTACH_FILE', 'การจัดเก็บไฟล์แนบ') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (39, 'EMPSER_SCORE_METHOD', '$CH_EMPSER_SCORE_METHOD', 'การคำนวณคะแนนผลการปฏิบัติราชการ') ";
		$db->send_cmd($cmd);
		
	} // end if

	include("$ROOTPATH/php_scripts/system_config.php");	
	
//	echo "command=$command && $username && $password<br>";
	// ส่วนนี้เพิ่มสำหรับ user login ที่ กำหนดการเข้าถึง ฐานข้อมูลจำเพาะเอาไว้
	$username = trim($username);
	$password = trim($password);
	$condition = "";
	if ($command == "LOGIN" && $username != "" && $password != "") {
		$encrypt_password = md5($password);
		$condition = "and a.username = '$username' and a.password = '$encrypt_password' ";
	} else if ($command != "FIRST" && $command != "LOGIN" && $command != "LOGOUT") {
		include("$ROOTPATH/admin/php_scripts/session_start.php");
		$condition = "and a.id = '$SESS_USERID' ";
	} else if ($command == "FIRST") {
		$command="";
	}

	if ($condition) {
		$cmd_u = " select a.group_id, b.dpisdb, b.dpisdb_host, b.dpisdb_name, b.dpisdb_user, b.dpisdb_pwd, 
									b.group_level, b. org_id
							from	user_detail a, user_group b
							where a.group_id=b.id $condition ";
//		$db->send_cmd($cmd_u);
//		$db->show_error();
//		echo "$cmd_u<br>";
		if ($db->send_cmd($cmd_u)) {
			$data_u = $db->get_array();
			$data_u = array_change_key_case($data_u, CASE_LOWER);
			$f_center = "Y"; // use in load_per_control.php
			if(trim($data_u[dpisdb])) {
//				$DPISDB = $data_u[dpisdb];
//				echo "DPIS=".$data_u[dpisdb]."<br>";
				$a = $data_u[dpisdb];
				if ($a=="1") $DPISDB=="odbc";
				elseif ($a=="2") $DPISDB=="oci8";
				else $DPISDB=="mysql";
				$dpisdb_host = $data_u[dpisdb_host];
				$dpisdb_name = $data_u[dpisdb_name];
				$dpisdb_user = $data_u[dpisdb_user];
				$dpisdb_pwd = $data_u[dpisdb_pwd];
				$dpisdb_group_level = $data_u[group_level];
				$dpisdb_org_id = $data_u[org_id];
//				echo "user connect->$DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd<br>";
				$f_center = "N"; // use in load_per_control.php
			} // end if(trim($data_u[dpisdb]))
		} // end if if ($db->send_cmd($cmd_u))
	} // end if ($condition)
	// จบส่วนสำหรับ user login ที่ กำหนดการเข้าถึง ฐานข้อมูลจำเพาะเอาไว้
	
//	echo "center=$f_center user connect->$DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd<br>";

	if($DPISDB=="mysql"){
		class connect_dpis extends connect_mysql { };
	}else{
		include("$ROOTPATH/php_scripts/connect_".$DPISDB.".php");
		if($DPISDB=="mssql"){
			class connect_dpis extends connect_mssql { };
		}elseif($DPISDB=="oci8"){
			class connect_dpis extends connect_oci8 { };
		}elseif($DPISDB=="oracle"){
			class connect_dpis extends connect_oracle { };
		}elseif($DPISDB=="odbc"){
			class connect_dpis extends connect_odbc { };
		} // end if
	} // end if

	if ($DPIS35DB) {
		if($DPISDB!=$DPIS35DB) include("$ROOTPATH/php_scripts/connect_".$DPIS35DB.".php");
		if($DPIS35DB=="mssql"){
			class connect_dpis35 extends connect_mssql { };
		}elseif($DPIS35DB=="oci8"){
			class connect_dpis35 extends connect_oci8 { };
		}elseif($DPIS35DB=="oracle"){
			class connect_dpis35 extends connect_oracle { };
		}elseif($DPIS35DB=="odbc"){
			class connect_dpis35 extends connect_odbc { };
		}elseif($DPIS35DB=="mysql"){
			class connect_dpis35 extends connect_mysql { };
		} // end if
	} // end if

	if($DPISDB=="odbc"){
		class connect_att extends connect_odbc { };
	}else{
		if($DPIS35DB!="odbc") include("$ROOTPATH/php_scripts/connect_odbc.php");
		class connect_att extends connect_odbc { };
	} // end if

	$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if ($DPIS35DB) $db_dpis35 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	if ($ATTDB) $db_att = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);

	ini_set ("session.gc_maxlifetime", "7200");
	$CATE_FID = 1;
	$SYSLANGCODE = array("TH" , "EN");
	$FontLangCode =  substr(dirname($PHP_SELF),-2);	
	$ARR_GROUP_CODE = array ( 1 => "ADMIN", 2 => "USER", 3 => "BUREAU");

//	echo "f_center=$f_center && grp_level=$dpisdb_group_level && org_id=$dpisdb_org_id<br>";
	if ($f_center == "N" && $dpisdb_group_level && $dpisdb_org_id) { // กรณีกำหนดการ connect ไว้ที่ user_group
		$CTRL_TYPE = $dpisdb_group_level; // ให้ระดับเริ่มต้นเป็น level ที่กำหนดที่ user_group
		$TEMP_ORG_ID = $dpisdb_org_id;
	} else {
		$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_TYPE = $data[CTRL_TYPE];
		if(!$CTRL_TYPE) $CTRL_TYPE = 4;
		$TEMP_ORG_ID = $data[ORG_ID];
	}
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TEMP_ORG_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$TEMP_ORG_NAME = $data[ORG_NAME];

	if ($TEMP_ORG_NAME=="สถาบันบัณฑิตพัฒนศิลป์") 
		$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"อาจารย์",5=>"ครู"); //ประเภทบุคคล
	else
		$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ"); //ประเภทบุคคล

	$MINISTRY_TITLE = "กระทรวง";
	$DEPARTMENT_TITLE = "กรม";
	$ORG_TITLE = "เทียบเท่าสำนัก/กอง";
//	$ORG_TITLE = "สำนัก/กอง";
	$ORG_TITLE1 = "ต่ำกว่าสำนัก/กอง 1 ระดับ";
	$ORG_TITLE2 = "ต่ำกว่าสำนัก/กอง 2 ระดับ";
	$ORG_TITLE3 = "ต่ำกว่าสำนัก/กอง 3 ระดับ";
	$ORG_TITLE4 = "ต่ำกว่าสำนัก/กอง 4 ระดับ";
	$ORG_TITLE5 = "ต่ำกว่าสำนัก/กอง 5 ระดับ";
	$PL_TITLE = "ตำแหน่งในสายงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$CT_TITLE = "ประเทศ";
	$PV_TITLE = "จังหวัด";
	$AP_TITLE = "อำเภอ";
	$POS_NO_TITLE = "เลขที่ตำแหน่ง";
	$PAY_NO_TITLE = "เลขถือจ่าย";
	$SEQ_NO_TITLE = "ลำดับที่";
	$PT_TITLE = "ประเภทตำแหน่ง";
	$LEVEL_TITLE = "ระดับตำแหน่ง";
	$PER_LEVEL_TITLE = "ระดับบุคคล";
	$MOV_TITLE = "ประเภทการเคลื่อนไหว";
	$REMARK_TITLE = "หมายเหตุ";
	$SALARY_TITLE = "อัตราเงินเดือน";
	$MGTSALARY_TITLE = "เงินประจำตำแหน่ง";
	$DOCNO_TITLE = "เลขที่คำสั่ง";
	$DOCDATE_TITLE = "ลงวันที่";
	$UPDATE_USER_TITLE = "แก้ไขโดย";
	$UPDATE_DATE_TITLE = "วันที่แก้ไข";
	$POH_EFFECTIVEDATE_TITLE = "วันที่มีผล";
	$POH_ENDDATE_TITLE = "วันที่สิ้นสุด";
	$SAH_EFFECTIVEDATE_TITLE = "วันที่มีผล";
	$SAH_ENDDATE_TITLE = "วันที่สิ้นสุด";
	$TRN_STARTDATE_TITLE = "วันที่เริ่ม";
	$TRN_ENDDATE_TITLE = "วันที่สิ้นสุด";
	$TRN_PROJECT_NAME_TITLE = "โครงการฝึกอบรม";
	$COM_NO_TITLE = "คำสั่งเลขที่";
	$COM_DATE_TITLE = "ลงวันที่";
	$COM_NAME_TITLE = "เรื่อง";
	$COM_TYPE_TITLE = "ประเภทคำสั่ง";
	$COM_NOTE_TITLE = "หมายเหตุท้ายคำสั่ง";
	$COM_DETAIL_TITLE = "รายละเอียดของคำสั่ง";
	$COM_ORDER_TITLE = "บัญชีแนบท้าย เรียงตาม";
	$COM_ORDER2_TITLE = "สังกัด, เลขที่ตำแหน่ง";
	$COM_DEL_TITLE = "ลบบัญชี";
	$COM_ADD_TITLE = "เพิ่มบัญชี";
	$COM_EDIT_TITLE = "แก้ไขบัญชี";
	$COM_CONFIRM_TITLE = "ยืนยันคำสั่ง";
	$COM_SEND_TITLE = "ส่งคำสั่ง";
	$COM_SEARCH_TITLE = "ค้นหาบัญชีแนบท้ายคำสั่ง";
	$ADD_PERSON_TITLE = "เพิ่มรายการบุคคล";
	$SELECT_PERSON_TITLE = "เลือกรายการบุคคล";
	$FULLNAME_TITLE = "ชื่อ-สกุล";
	$PER_TYPE_TITLE = "ประเภทบุคลากร";
	$NAME_TITLE = "ชื่อ";
	$SURNAME_TITLE = "นามสกุล";
	$CARDNO_TITLE = "เลขประจำตัวประชาชน";
	$OFFNO_TITLE = "เลขประจำตัวข้าราชการ";
	$SEX_TITLE = "เพศ";
	$DEH_RECEIVE_DATE_TITLE = "วันที่ราชกิจจานุเบกษา";
	$DEH_DATE_TITLE = "วันเดือนปี ที่รับ";

	$PL_NAME_WORK_TITLE = "ตำแหน่ง (ก.พ.7)";
	$ORG_NAME_WORK_TITLE = "ส่วนราชการ (ก.พ.7)";
	$BIRTHDATE_TITLE = "วันเดือนปีเกิด";
	$CMD_EDUCATE_TITLE = "วุฒิที่ใช้ในตำแหน่ง";
	$CMD_DATE_TITLE = "วันที่แต่งตั้ง";
	$OLD_POSITION_TITLE = "ตำแหน่งเดิม";
	$CMD_POSITION_TITLE = "ตำแหน่ง";
	$NEW_POSITION_TITLE = "ตำแหน่งที่แต่งตั้ง";
	$PL_ASSIGN_TITLE = "มอบหมายให้ปฏิบัติงาน";
	$FROM_DATE_TITLE = "ตั้งแต่วันที่";
	$TO_DATE_TITLE = "ถึงวันที่";
	$SPSALARY_TITLE = "เงินตอบแทนพิเศษ";
	$ES_TITLE = "สถานะการดำรงตำแหน่ง";
	$EL_TITLE = "ระดับการศึกษา";
	$EN_TITLE = "วุฒิการศึกษา";
	$EM_TITLE = "สาขาวิชาเอก";
	$INS_TITLE = "สถานศึกษา";
	$PER_STATUS_TITLE = "สถานภาพ";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$PM_TITLE = "ตำแหน่งในการบริหารงาน";
	$CLOSE_WINDOW_TITLE = "ปิดหน้าต่าง";
	$SHOW_ALL_TITLE = "แสดงทั้งหมด";
	$EXCEL_TITLE = "ส่งออกไฟล์ Excel";
	$PDF_TITLE = "ดูรายงานรูปแบบ PDF";
	$CLEAR_TITLE = "เคลียร์";
	$SELECT_TITLE = "เลือก";
	$CANCEL_TITLE = "ยกเลิก";
	$SEARCH_TITLE = "ค้นหาข้อมูล";
	$ADD_TITLE = "เพิ่มข้อมูล";
	$EDIT_TITLE = "แก้ไข";
	$DEL_TITLE = "ลบ";
	$INQ_TITLE = "เรียกดู";
	$PRINT_TITLE = "พิมพ์";
	$CONFIRM_TITLE = "ยืนยัน";
	$LOAD_TITLE = "แนบไฟล์";
	$ALT_LOAD_TITLE = "แนบไฟล์ข้อมูล";
	$DETAIL_TITLE = "รายละเอียด";
	$INQUIRE_TITLE = "สอบถาม";
	$SAVE_SEARCH_TITLE = "บันทึกผลการค้นหา";
	$REORDER_TITLE = "จัดลำดับ";
	$SETFLAG_TITLE = "ตั้งค่า";

	if ($PRINT_FONT==2) {
		$font = 'cordia';
		$fontb = 'cordiab';
	} else { 
		$font = 'angsa';
		$fontb = 'angsab';
	}
	
	if($MENU_ID_LV0){
		$cmd = " select menu_label from backoffice_menu_bar_lv0 where langcode='TH' and menu_id=$MENU_ID_LV0 ";
		$db->send_cmd($cmd);
		$data = $db->get_object();
		$MENU_TITLE_LV0 = $data->menu_label;
	} //endif

	if($MENU_ID_LV1){
		$cmd = " select menu_label from backoffice_menu_bar_lv1 where langcode='TH' and menu_id=$MENU_ID_LV1 ";
		$db->send_cmd($cmd);
		$data = $db->get_object();
		$MENU_TITLE_LV1 = $data->menu_label;
	} // endif

	if($MENU_ID_LV2){
		$cmd = " select menu_label from backoffice_menu_bar_lv2 where langcode='TH' and menu_id=$MENU_ID_LV2 ";
		$db->send_cmd($cmd);
		$data = $db->get_object();
		$MENU_TITLE_LV2 = $data->menu_label;
	} // endif

	if($MENU_ID_LV3){
		$cmd = " select menu_label from backoffice_menu_bar_lv3 where langcode='TH' and menu_id=$MENU_ID_LV3 ";
		$db->send_cmd($cmd);
		$data = $db->get_object();
		$MENU_TITLE_LV3 = $data->menu_label;
	} // endif
//	$TEMP_ORG_NAME = "กรมการปกครอง";
?>