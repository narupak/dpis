<?php
	$phpversion = phpversion();
	if ($phpversion >= '5'){
			@date_default_timezone_set("Asia/Bangkok");
	}

	putenv("NLS_LANG=AMERICAN_AMERICA.TH8TISASCII");
	
	error_reporting(1);
        	
	$SERVER_TYPE = explode("/", $_SERVER['SERVER_SOFTWARE']);
	if( substr_count($SERVER_TYPE[0], "Apache") ){
		$ROOTPATH = $_SERVER['DOCUMENT_ROOT'];
	}elseif( substr_count($SERVER_TYPE[0], "IIS") ){
		$ROOTPATH = $_SERVER['APPL_PHYSICAL_PATH'];
//		$virtual_site = "DREAM/";
	}
	if (!$ch_dpisdb_port) $ch_dpisdb_port = "1521";
	if (!$ch_attdb_port) $ch_attdb_port = "1521";
	if (!$ch_dpis35db_port) $ch_dpis35db_port = "1521";
        

	include("$ROOTPATH/php_scripts/db_connect_var.php");

	include("$ROOTPATH/php_scripts/connect_".$db_type.".php");
	if($db_type=="mysql"){
		class connect_db extends connect_mysql { };
	}elseif($db_type=="mssql"){
		class connect_db extends connect_mssql { };
	}elseif($db_type=="oci8"){
		class connect_db extends connect_oci8 { };
	}elseif($db_type=="odbc"){
		class connect_db extends connect_odbc { };
	} // end if
	$db = new connect_db($db_host, $db_name, $db_user, $db_pwd);
        
	//	echo "Test Connect : $db : $db->link_con : $PHP_SELF ";
        if(!$db->link_con) {
            $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $myfile = fopen("../php_scripts/cannot_connect.log", "r") ;
            $myfileData=fread($myfile,filesize("../php_scripts/cannot_connect.log"));
            fclose($myfile);
            if(empty($myfileData)){
                $myfileData;
            }else{
                $myfileData."\n";
            }
            $myfile = fopen("../php_scripts/cannot_connect.log", "w");
            $txt = $myfileData."Datetime : " . date("Y-m-d")." ".date("h:i:sa"). ",IP:".$_SERVER['REMOTE_ADDR'].",link:".$actual_link."\n";
            fwrite($myfile, $txt);
            fclose($myfile);
        }
        
	if(!$db->link_con) die("Cannot connect to ".strtoupper($db_type)." database");
	
        
        //echo '>>>'.$command;
        
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
	
		$cmd = " select config_name from system_config where config_id = 73 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'dpisdb_port',
									config_value = '$ch_dpisdb_port', 
									config_remark = 'DB Server Port'
								 where config_id = 73 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (73, 'dpisdb_port', '$ch_dpisdb_port', 'DB Server Port') ";
		$db->send_cmd($cmd);
	} // end if
	
	if($command == "UPDATEATTDB"){
		if ($db_type=="oci8") {
			$cmd = " select config_name from system_config where config_id = 15 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'ATTDB',
										config_value = '$CH_ATTDB', 
										config_remark = 'ประเภทฐานข้อมูลเครื่องบันทึกเวลา'
									 where config_id = 15 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', 'ประเภทฐานข้อมูลเครื่องบันทึกเวลา') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 16 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'attdb_host',
										config_value = '$ch_attdb_host', 
										config_remark = 'Time DB Server IP'
									 where config_id = 16 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (16, 'attdb_host', '$ch_attdb_host', 'Time DB Server IP') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 17 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'attdb_name',
										config_value = '$ch_attdb_name', 
										config_remark = 'Time DB Name'
									 where config_id = 17 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (17, 'attdb_name', '$ch_attdb_name', 'Time DB Name') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 18 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'attdb_user',
										config_value = '$ch_attdb_user', 
										config_remark = 'Time DB User'
									 where config_id = 18 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (18, 'attdb_user', '$ch_attdb_user', 'Time DB User') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 19 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'attdb_pwd',
										config_value = '$ch_attdb_pwd', 
										config_remark = 'Time DB Password'
									 where config_id = 19 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (19, 'attdb_pwd', '$ch_attdb_pwd', 'Time DB Password') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 75 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'attdb_port',
										config_value = '$ch_attdb_port', 
										config_remark = 'Time DB Server Port'
									 where config_id = 75 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (75, 'attdb_host', '$ch_attdb_host', 'Time DB Server Port') ";
			$db->send_cmd($cmd);
		} else {
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

			$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (75, 'attdb_port', '$ch_attdb_port', 'Time DB Server Port') ";
			$db->send_cmd($cmd);
		} // end if
	} // end if

	if($command == "UPDATEDPIS35DB"){
		if ($db_type=="oci8") {
			$cmd = " select config_name from system_config where config_id = 21 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'DPIS35DB',
										config_value = '$CH_DPIS35DB', 
										config_remark = 'ประเภทฐานข้อมูลที่ต้องการถ่ายโอน'
									 where config_id = 21 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', 'ประเภทฐานข้อมูลที่ต้องการถ่ายโอน') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 22 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'dpis35db_host',
										config_value = '$ch_dpis35db_host', 
										config_remark = 'Convert DB Server IP'
									 where config_id = 22 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (22, 'dpis35db_host', '$ch_dpis35db_host', 'Convert DB Server IP') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 23 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'dpis35db_name',
										config_value = '$ch_dpis35db_name', 
										config_remark = 'Convert DB Name'
									 where config_id = 23 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (23, 'dpis35db_name', '$ch_dpis35db_name', 'Convert DB Name') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 24 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'dpis35db_user',
										config_value = '$ch_dpis35db_user', 
										config_remark = 'Convert DB User'
									 where config_id = 24 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (24, 'dpis35db_user', '$ch_dpis35db_user', 'Convert DB User') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 25 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'dpis35db_pwd',
										config_value = '$ch_dpis35db_pwd', 
										config_remark = 'Convert DB Password'
									 where config_id = 25 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (25, 'dpis35db_pwd', '$ch_dpis35db_pwd', 'Convert DB Password') ";
			$db->send_cmd($cmd);

			$cmd = " select config_name from system_config where config_id = 74 ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data)
				$cmd = " update system_config set 
										config_name = 'dpis35db_port',
										config_value = '$ch_dpis35db_port', 
										config_remark = 'Convert DB Server Port'
									 where config_id = 74 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (74, 'dpis35db_port', '$ch_dpis35db_port', 'Convert DB Server Port') ";
			$db->send_cmd($cmd);
		} else {
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

			$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (74, 'dpis35db_port', '$ch_dpis35db_port', 'Convert DB Server Port') ";
			$db->send_cmd($cmd);
		} // end if
	} // end if
	
	if($command == "UPDATESYSTEMPARAMETER"){
		for ($i=0; $i<count($CH_COMPETENCY_METHOD); $i++) {
			$COMPETENCY_METHOD_TXT.= "||$CH_COMPETENCY_METHOD[$i]";
		}
		$COMPETENCY_METHOD_TXT = (trim($COMPETENCY_METHOD_TXT))? $COMPETENCY_METHOD_TXT."||" : "";

		for ($i=0; $i<count($CH_E_SIGN); $i++) {
			$E_SIGN_TXT.= "||$CH_E_SIGN[$i]";
		}
		$E_SIGN_TXT = (trim($E_SIGN_TXT))? $E_SIGN_TXT."||" : "";

		if (!$ch_password_age) $ch_password_age = "0";
		$cmd = " select config_name from system_config where config_id = 10 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'data_per_page',
									config_value = '$ch_data_per_page', 
									config_remark = 'จำนวนเรคอร์ดต่อหน้า'
								 where config_id = 10 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (10, 'data_per_page', '$ch_data_per_page', 'จำนวนเรคอร์ดต่อหน้า') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 11 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI',
									config_value = '$CH_WEIGHT_KPI', 
									config_remark = '% ผลการประเมินของข้าราชการ - ผลสำเร็จของงาน'
								 where config_id = 11 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (11, 'WEIGHT_KPI', '$CH_WEIGHT_KPI', '% ผลการประเมินของข้าราชการ - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 12 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE',
									config_value = '$CH_WEIGHT_COMPETENCE', 
									config_remark = '% ผลการประเมินของข้าราชการ - สมรรถนะ'
								 where config_id = 12 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (12, 'WEIGHT_COMPETENCE', '$CH_WEIGHT_COMPETENCE', '% ผลการประเมินของข้าราชการ - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 13 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_OTHER',
									config_value = '$CH_WEIGHT_OTHER', 
									config_remark = '% ผลการประเมินของข้าราชการ - อื่น ๆ'
								 where config_id = 13 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (13, 'WEIGHT_OTHER', '$CH_WEIGHT_OTHER', '% ผลการประเมินของข้าราชการ - อื่น ๆ') ";
		$db->send_cmd($cmd);
/*			
		$cmd = " select config_name from system_config where config_id = 20 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'RPT_N',
									config_value = '$CH_RPT_N', 
									config_remark = 'พระราชบัญญัติระเบียบข้าราชการพลเรือน พ.ศ. 2551'
								 where config_id = 20 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (20, 'RPT_N', '$CH_RPT_N', 'พระราชบัญญัติระเบียบข้าราชการพลเรือน พ.ศ. 2551') ";
		$db->send_cmd($cmd);
*/			
		$cmd = " select config_name from system_config where config_id = 14 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'COMPETENCY_SCALE',
									config_value = '$CH_COMPETENCY_SCALE', 
									config_remark = 'มาตรวัดการประเมินสมรรถนะ'
								 where config_id = 14 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (14, 'COMPETENCY_SCALE', '$CH_COMPETENCY_SCALE', 'มาตรวัดการประเมินสมรรถนะ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 29 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CARD_NO_DISPLAY',
									config_value = '$CH_CARD_NO_DISPLAY', 
									config_remark = 'การแสดงผลเลขประจำตัวประชาชน'
								 where config_id = 29 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (29, 'CARD_NO_DISPLAY', '$CH_CARD_NO_DISPLAY', 'การแสดงผลเลขประจำตัวประชาชน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 30 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'BUTTON_DISPLAY',
									config_value = '$CH_BUTTON_DISPLAY', 
									config_remark = 'การแสดงผลปุ่มกด'
								 where config_id = 30 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (30, 'BUTTON_DISPLAY', '$CH_BUTTON_DISPLAY', 'การแสดงผลปุ่มกด') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 31 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PER_ORDER_BY',
									config_value = '$CH_ORDER_BY', 
									config_remark = 'การเรียงข้อมูลบุคคล'
								 where config_id = 31 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (31, 'PER_ORDER_BY', '$CH_ORDER_BY', 'การเรียงข้อมูลบุคคล') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 32 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PAYMENT_FLAG',
									config_value = '$CH_PAYMENT_FLAG', 
									config_remark = 'ถือจ่าย'
								 where config_id = 32 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (32, 'PAYMENT_FLAG', '$CH_PAYMENT_FLAG', 'ถือจ่าย') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 33 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI_E',
									config_value = '$CH_WEIGHT_KPI_E', 
									config_remark = '% ผลการประเมินของพนักงานราชการทั่วไป - ผลสำเร็จของงาน'
								 where config_id = 33 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (33, 'WEIGHT_KPI_E', '$CH_WEIGHT_KPI_E', '% ผลการประเมินของพนักงานราชการทั่วไป - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 34 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE_E',
									config_value = '$CH_WEIGHT_COMPETENCE_E', 
									config_remark = '% ผลการประเมินของพนักงานราชการทั่วไป - สมรรถนะ'
								 where config_id = 34 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (34, 'WEIGHT_COMPETENCE_E', '$CH_WEIGHT_COMPETENCE_E', '% ผลการประเมินของพนักงานราชการทั่วไป - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 35 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI_S',
									config_value = '$CH_WEIGHT_KPI_S', 
									config_remark = '% ผลการประเมินของพนักงานราชการพิเศษ - ผลสำเร็จของงาน'
								 where config_id = 35 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (35, 'WEIGHT_KPI_S', '$CH_WEIGHT_KPI_S', '% ผลการประเมินของพนักงานราชการพิเศษ - ผลสำเร็จของงาน') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 36 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE_S',
									config_value = '$CH_WEIGHT_COMPETENCE_S', 
									config_remark = '% ผลการประเมินของพนักงานราชการพิเศษ - สมรรถนะ'
								 where config_id = 36 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (36, 'WEIGHT_COMPETENCE_S', '$CH_WEIGHT_COMPETENCE_S', '% ผลการประเมินของพนักงานราชการพิเศษ - สมรรถนะ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 37 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_FONT',
									config_value = '$CH_PRINT_FONT', 
									config_remark = 'รูปแบบอักษรในการพิมพ์รายงาน'
								 where config_id = 37 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (37, 'PRINT_FONT', '$CH_PRINT_FONT', 'รูปแบบอักษรในการพิมพ์รายงาน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 38 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'ATTACH_FILE',
									config_value = '$CH_ATTACH_FILE', 
									config_remark = 'การจัดเก็บไฟล์แนบ'
								 where config_id = 38 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (38, 'ATTACH_FILE', '$CH_ATTACH_FILE', 'การจัดเก็บไฟล์แนบ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 39 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'EMPSER_SCORE_METHOD',
									config_value = '$CH_EMPSER_SCORE_METHOD', 
									config_remark = 'การคำนวณคะแนนผลการปฏิบัติราชการ'
								 where config_id = 39 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (39, 'EMPSER_SCORE_METHOD', '$CH_EMPSER_SCORE_METHOD', 'การคำนวณคะแนนผลการปฏิบัติราชการ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 40 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'ORG_SETLEVEL',
									config_value = '$CH_ORG_SETLEVEL', 
									config_remark = 'ต่ำกว่าสำนักกอง'
								 where config_id = 40 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (40, 'ORG_SETLEVEL', '$CH_ORG_SETLEVEL', 'ต่ำกว่าสำนักกอง') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 41 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'COMPETENCY_METHOD',
									config_value = '$COMPETENCY_METHOD_TXT', 
									config_remark = 'วิธีการประเมิน'
								 where config_id = 41 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (41, 'COMPETENCY_METHOD', '$COMPETENCY_METHOD_TXT', 'วิธีการประเมิน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 42 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'MENU_TYPE',
									config_value = '$CH_MENU_TYPE', 
									config_remark = 'รูปแบบเมนู'
								 where config_id = 42 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (42, 'MENU_TYPE', '$CH_MENU_TYPE', 'รูปแบบเมนู') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 43 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'DATE_DISPLAY',
									config_value = '$CH_DATE_DISPLAY', 
									config_remark = 'การแสดงผลวันที่'
								 where config_id = 43 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (43, 'DATE_DISPLAY', '$CH_DATE_DISPLAY', 'การแสดงผลวันที่') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 44 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'POSITION_NO_CHAR',
									config_value = '$CH_POSITION_NO_CHAR', 
									config_remark = 'เลขที่ตำแหน่งมีตัวอักษร'
								 where config_id = 44 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (44, 'POSITION_NO_CHAR', '$CH_POSITION_NO_CHAR', 'เลขที่ตำแหน่งมีตัวอักษร') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 45 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'NUMBER_DISPLAY',
									config_value = '$CH_NUMBER_DISPLAY', 
									config_remark = 'การแสดงผลตัวเลข'
								 where config_id = 45 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (45, 'NUMBER_DISPLAY', '$CH_NUMBER_DISPLAY', 'การแสดงผลตัวเลข') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 46 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'password_age',
									config_value = '$ch_password_age', 
									config_remark = 'อายุรหัสผ่าน 0= ไม่จำกัดอายุ'
								 where config_id = 46 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (46, 'password_age', '$ch_password_age', 'อายุรหัสผ่าน 0= ไม่จำกัดอายุ') ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from system_config where config_id = 47 ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 48 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'maxsize_up_file',
									config_value = '$maxsize_up_file', 
									config_remark = 'ขนาดไฟล์สูงสุดที่อัพโหลดได้'
								 where config_id = 48 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (48, 'maxsize_up_file', '$maxsize_up_file', 'ขนาดไฟล์สูงสุดที่อัพโหลดได้') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 49 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'E_SIGN',
									config_value = '$E_SIGN_TXT', 
									config_remark = 'ลายเซ็นอิเล็คทรอนิกส์'
								 where config_id = 49 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (49, 'E_SIGN', '$E_SIGN_TXT', 'ลายเซ็นอิเล็คทรอนิกส์') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 50 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_BUDGET_YEAR',
									config_value = '$CH_KPI_BUDGET_YEAR', 
									config_remark = 'ปีงบประมาณสำหรับประเมินผล'
								 where config_id = 50 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (50, 'KPI_BUDGET_YEAR', '$CH_KPI_BUDGET_YEAR', 'ปีงบประมาณสำหรับประเมินผล') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 51 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_CYCLE',
									config_value = '$CH_KPI_CYCLE', 
									config_remark = 'รอบการประเมินผล'
								 where config_id = 51 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (51, 'KPI_CYCLE', '$CH_KPI_CYCLE', 'รอบการประเมินผล') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 52 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_PER_REVIEW',
									config_value = '$CH_PER_REVIEW', 
									config_remark = 'พิมพ์ผู้ให้ข้อมูล'
								 where config_id = 52 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (52, 'KPI_PER_REVIEW', '$CH_PER_REVIEW', 'พิมพ์ผู้ให้ข้อมูล') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 53 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CARD_NO_FILL',
									config_value = '$CH_CARD_NO_FILL', 
									config_remark = 'ต้องป้อนเลขประจำตัวประชาชน'
								 where config_id = 53 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (53, 'CARD_NO_FILL', '$CH_CARD_NO_FILL', 'ต้องป้อนเลขประจำตัวประชาชน') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 54 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'start_age',
									config_value = '$ch_start_age', 
									config_remark = 'อายุที่ใช้บรรจุไม่เกิน'
								 where config_id = 54 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (54, 'start_age', '$ch_start_age', 'อายุที่ใช้บรรจุไม่เกิน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 55 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'NOT_LEVEL_NO_O4',
									config_value = '$CH_NOT_LEVEL_NO_O4', 
									config_remark = 'ไม่มีตำแหน่งระดับทักษะพิเศษ'
								 where config_id = 55 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (55, 'NOT_LEVEL_NO_O4', '$CH_NOT_LEVEL_NO_O4', 'ไม่มีตำแหน่งระดับทักษะพิเศษ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 56 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
                    /*เดิม*/
			/*$cmd = " update system_config set 
									config_name = 'KPI_SCORE_CONFIRM',
									config_value = '$CH_SCORE_CONFIRM', 
									config_remark = 'ตรวจสอบการให้คะแนนผลการประเมิน'
								 where config_id = 56 ";*/
                    /*http://dpis.ocsc.go.th/Service/node/1152*/
                    $cmd = " update system_config set 
									config_name = 'KPI_SCORE_CONFIRM',
									config_value = 1, 
									config_remark = 'ตรวจสอบการให้คะแนนผลการประเมิน'
								 where config_id = 56 ";
                    /**/
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (56, 'KPI_SCORE_CONFIRM', '$CH_SCORE_CONFIRM', 'ตรวจสอบการให้คะแนนผลการประเมิน') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 57 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SAME_ASSESS_LEVEL',
									config_value = '$CH_SAME_ASSESS_LEVEL', 
									config_remark = 'ใช้ระดับผลการประเมินย่อยเหมือนกัน'
								 where config_id = 57 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (57, 'SAME_ASSESS_LEVEL', '$CH_SAME_ASSESS_LEVEL', 'ใช้ระดับผลการประเมินย่อยเหมือนกัน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 58 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_DISPLAY',
									config_value = '$CH_SLIP_DISPLAY', 
									config_remark = 'รูปแบบสลิปเงินเดือน'
								 where config_id = 58 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (58, 'SLIP_DISPLAY', '$CH_SLIP_DISPLAY', 'รูปแบบสลิปเงินเดือน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 59 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'FILE_PATH_DISPLAY',
									config_value = '$CH_FILE_PATH', 
									config_remark = 'พาธเครื่องที่เก็บไฟล์'
								 where config_id = 59 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (59, 'FILE_PATH_DISPLAY', '$CH_FILE_PATH', 'พาธเครื่องที่เก็บไฟล์') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 60 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'AUTOMAIL_ABSENT_FLAG',
									config_value = '$CH_AUTOMAIL_ABSENT', 
									config_remark = 'ส่งอีเมล์ในระบบการลา'
								 where config_id = 60 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (60, 'AUTOMAIL_ABSENT_FLAG', '$CH_AUTOMAIL_ABSENT', 'ส่งอีเมล์ในระบบการลา') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 61 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'IMG_PATH_DISPLAY',
									config_value = '$CH_IMG_PATH', 
									config_remark = 'พาธเครื่องที่เก็บรูปภาพ'
								 where config_id = 61 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (61, 'IMG_PATH_DISPLAY', '$CH_IMG_PATH', 'พาธเครื่องที่เก็บรูปภาพ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 62 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTitle_color',
									config_value = '$xlsFmtTitle_color', 
									config_remark = 'การแสดงสีอักษรใน excel แบบ Title 1'
								 where config_id = 62 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (62, 'xlsFmtTitle_color', '$xlsFmtTitle_color', 'การแสดงสีอักษรใน excel แบบ Title 1') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 63 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTitle_bgcolor',
									config_value = '$xlsFmtTitle_bgcolor', 
									config_remark = 'การแสดงสีพี้นใน excel แบบ Title 1'
								 where config_id = 63 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (63, 'xlsFmtTitle_bgcolor', '$xlsFmtTitle_bgcolor', 'การแสดงสีพี้นใน excel แบบ Title 1') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 64 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtSubTitle_color',
									config_value = '$xlsFmtSubTitle_color', 
									config_remark = 'การแสดงสีตัวอักษรใน excel แบบ Title 2'
								 where config_id = 64 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (64, 'xlsFmtSubTitle_color', '$xlsFmtSubTitle_color', 'การแสดงสีตัวอักษรใน excel แบบ Title 2') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 65 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtSubTitle_bgcolor',
									config_value = '$xlsFmtSubTitle_bgcolor', 
									config_remark = 'การแสดงสีพี้นใน excel แบบ Title 2'
								 where config_id = 65 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (65, 'xlsFmtSubTitle_bgcolor', '$xlsFmtSubTitle_bgcolor', 'การแสดงสีพี้นใน excel แบบ Title 2') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 66 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableHeader_color',
									config_value = '$xlsFmtTableHeader_color', 
									config_remark = 'การแสดงสีตัวอักษรหัวตาราง excel'
								 where config_id = 66 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (66, 'xlsFmtTableHeader_color', '$xlsFmtTableHeader_color', 'การแสดงสีตัวอักษรหัวตาราง excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 67 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableHeader_bgcolor',
									config_value = '$xlsFmtTableHeader_bgcolor', 
									config_remark = 'การแสดงสีพี้นหัวตาราง excel'
								 where config_id = 67 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (67, 'xlsFmtTableHeader_bgcolor', '$xlsFmtTableHeader_bgcolor', 'การแสดงสีพี้นหัวตาราง excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 68 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableDetail_color',
									config_value = '$xlsFmtTableDetail_color', 
									config_remark = 'การแสดงสีตัวอักษรในตาราง excel'
								 where config_id = 68 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (68, 'xlsFmtTableDetail_color', '$xlsFmtTableDetail_color', 'การแสดงสีตัวอักษรในตาราง excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 69 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableDetail_bgcolor',
									config_value = '$xlsFmtTableDetail_bgcolor', 
									config_remark = 'การแสดงสีพี้นในตาราง excel'
								 where config_id = 69 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (69, 'xlsFmtTableDetail_bgcolor', '$xlsFmtTableDetail_bgcolor', 'การแสดงสีพี้นในตาราง excel') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 70 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_KP7',
									config_value = '$CH_PRINT_KP7', 
									config_remark = 'พิมพ์ $KP7_TITLE ทั้งฉบับในหน้าจอข้อมูลบุคคล'
								 where config_id = 70 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (70, 'PRINT_KP7', '$CH_PRINT_KP7', 'พิมพ์ $KP7_TITLE ทั้งฉบับในหน้าจอข้อมูลบุคคล') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 71 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_SCORE_DECIMAL',
									config_value = '$CH_SCORE_DECIMAL', 
									config_remark = 'คะแนนผลการประเมินการปฏิบัติราชการมีทศนิยม'
								 where config_id = 71 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (71, 'KPI_SCORE_DECIMAL', '$CH_SCORE_DECIMAL', 'คะแนนผลการประเมินการปฏิบัติราชการมีทศนิยม') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 72 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'USE_KPI_TYPE',
									config_value = '$CH_USE_KPI_TYPE', 
									config_remark = 'ใช้ประเภทตัวชี้วัด'
								 where config_id = 72 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (72, 'USE_KPI_TYPE', '$CH_USE_KPI_TYPE', 'ใช้ประเภทตัวชี้วัด') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 73 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_KP7_USER',
									config_value = '$CH_PRINT_KP7_USER', 
									config_remark = 'พิมพ์ผู้บันทึกข้อมูลในช่องเอกสารอ้างอิงของ $KP7_TITLE ทั้งฉบับ'
								 where config_id = 72 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (73, 'PRINT_KP7_USER', 'Y', 'พิมพ์ผู้บันทึกข้อมูลในช่องเอกสารอ้างอิงของ $KP7_TITLE ทั้งฉบับ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 74 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SHOW_TIME_P0101',
									config_value = '$CH_SHOW_TIME_P0101', 
									config_remark = 'แสดงเวลาทวีคูณในข้อมูลบุคคลหน้าแรก'
								 where config_id = 74 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (74, 'SHOW_TIME_P0101', '$CH_SHOW_TIME_P0101', 'แสดงเวลาทวีคูณในข้อมูลบุคคลหน้าแรก') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 75 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SHOW_POSDATE_P0101',
									config_value = '$CH_SHOW_POSDATE_P0101', 
									config_remark = 'แสดงวันที่เข้าสู่ระดับก่อนปัจจุบันในข้อมูลบุคคลหน้าแรก'
								 where config_id = 75 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (75, 'SHOW_POSDATE_P0101', '$CH_SHOW_POSDATE_P0101', 'แสดงวันที่เข้าสู่ระดับก่อนปัจจุบันในข้อมูลบุคคลหน้าแรก') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 76 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SALARYHIS_DISPLAY',
									config_value = '$CH_SALARYHIS_DISPLAY', 
									config_remark = 'รูปแบบหนังสือแจ้งผลการเลื่อนเงินเดือน'
								 where config_id = 76 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (76, 'SALARYHIS_DISPLAY', '$CH_SALARYHIS_DISPLAY', 'รูปแบบหนังสือแจ้งผลการเลื่อนเงินเดือน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 77 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_SELF_EVALUATE',
									config_value = '$CH_SELF_EVALUATE', 
									config_remark = 'การประเมินตนเอง (สมรรถนะ)'
								 where config_id = 77 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (77, 'KPI_SELF_EVALUATE', '$CH_SELF_EVALUATE', 'การประเมินตนเอง (สมรรถนะ)') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 78";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'EDIT_ABSENT_DAY',
									config_value = '$CH_EDIT_ABSENT_DAY', 
									config_remark = 'แก้ไขจำนวนวันลา'
								 where config_id = 78 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (78, 'EDIT_ABSENT_DAY', '$CH_EDIT_ABSENT_DAY', 'แก้ไขจำนวนวันลา') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 79 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CMD_DATE_DISPLAY',
									config_value = '$CH_CMD_DATE_DISPLAY', 
									config_remark = 'การแสดงผลวันที่ (บัญชีแนบท้ายคำสั่ง)'
								 where config_id = 79 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (79, 'CMD_DATE_DISPLAY', '$CH_CMD_DATE_DISPLAY', 'การแสดงผลวันที่ (บัญชีแนบท้ายคำสั่ง)') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 80 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_FORMAT',
									config_value = '$CH_SLIP_FORMAT', 
									config_remark = 'สลิปเงินเดือน'
								 where config_id = 80 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (80, 'SLIP_FORMAT', '$CH_SLIP_FORMAT', 'สลิปเงินเดือน') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 81 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_LOGO',
									config_value = '$CH_SLIP_LOGO', 
									config_remark = 'ตำแหน่งโลโก้บนสลิปเงินเดือน (PDF)'
								 where config_id = 81 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (81, 'SLIP_LOGO', '$CH_SLIP_LOGO', 'ตำแหน่งโลโก้บนสลิปเงินเดือน (PDF)') ";
		$db->send_cmd($cmd);
                
                 /*Release 5.2.1.10 Begin*/
                
                $cmdM = "SELECT MAX(CONFIG_ID) AS MAXOFCONFIG_ID FROM SYSTEM_CONFIG";
                $db->send_cmd($cmdM);
                $dataMax = $db->get_array();
                $MAXOFCONFIG_ID =  $dataMax[MAXOFCONFIG_ID];
                
                if(!$CH_ORG_SHORT_NAME){$CH_ORG_SHORT_NAME='N';}
                $cmd = " select config_name from system_config where config_name = 'ORG_SHORT_NAME' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
			$cmd = " update system_config set 
									config_name = 'ORG_SHORT_NAME',
									config_value = '$CH_ORG_SHORT_NAME', 
									config_remark = 'แสดงชื่อย่อส่วนราชการในใบลา'
								 where config_name = 'ORG_SHORT_NAME' ";
                }else{
                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values ('$MAXOFCONFIG_ID', 'ORG_SHORT_NAME', '$CH_ORG_SHORT_NAME', 'แสดงชื่อย่อส่วนราชการในใบลา') ";
                }
		$db->send_cmd($cmd);
                
                /*Release 5.2.1.10 End*/
                
                /*Release 5.2.1.3 Begin*/
                if(!$SHOW_GENERAL){$SHOW_GENERAL='N';}
                $cmd = " UPDATE system_config SET 
                                config_value = '$SHOW_GENERAL'
                         WHERE UPPER(CONFIG_NAME)='SHOW_GENERAL' ";
                $db->send_cmd($cmd);
		/*Release 5.2.1.3 End*/
                
                /*Release 5.2.1.9 Begin*/
                if(empty($IS_ACCEPT_CONFIG)){
                    $IS_ACCEPT_CONFIG="0";
                }
                $cmd = " update system_config set config_value = '$IS_ACCEPT_CONFIG' 
                         where upper(config_name)='IS_ACCEPT_CONFIG' ";
                
		$db->send_cmd($cmd);
		
                /*Release 5.2.1.14 End*/
                if(!$CH_SCORE_DAY){$CH_SCORE_DAY='N';}
                $cmd = " select config_name from system_config where config_name = 'CH_SCORE_DAY' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
			$cmd = " update system_config set 
									config_name = 'CH_SCORE_DAY',
									config_value = '$CH_SCORE_DAY', 
									config_remark = 'ไม่แสดงวันที่ในแบบสรุปผลการประเมินการปฏิบัติราชการ'
								 where config_name = 'CH_SCORE_DAY' ";
                }else{
                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values ('$MAXOFCONFIG_ID', 'CH_SCORE_DAY', '$CH_SCORE_DAY', 'ไม่แสดงวันที่ในแบบสรุปผลการประเมินการปฏิบัติราชการ') ";
                }
		$db->send_cmd($cmd);
		if(!$ADD_PAPER_BUREAU){$ADD_PAPER_BUREAU='N';}
		$cmd = " select config_name from system_config where config_name = 'ADD_PAPER_BUREAU' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'ADD_PAPER_BUREAU',
							config_value = '$ADD_PAPER_BUREAU', 
							config_remark = 'อนุญาตให้กลุ่ม Bureau เพิ่มรายการประกาศ ที่หน้าแรก'
						 where config_name = 'ADD_PAPER_BUREAU' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'ADD_PAPER_BUREAU', '$ADD_PAPER_BUREAU', 'อนุญาตให้กลุ่ม Bureau เพิ่มรายการประกาศ ที่หน้าแรก') ";
		}
		
		$db->send_cmd($cmd);
		
		if($NUMBER_OF_DAY==""){$NUMBER_OF_DAY='23';}
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_DAY' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'NUMBER_OF_DAY',
							config_value = '$NUMBER_OF_DAY', 
							config_remark = 'จำนวนวัน'
						 where config_name = 'NUMBER_OF_DAY' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_DAY', '23', 'จำนวนวัน') ";
		}
		$db->send_cmd($cmd);
		
                
		if($NUMBER_OF_TIME==""){$NUMBER_OF_TIME='10';}
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_TIME' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'NUMBER_OF_TIME',
							config_value = '$NUMBER_OF_TIME', 
							config_remark = 'จำนวนครั้ง'
						 where config_name = 'NUMBER_OF_TIME' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_TIME', '10', 'จำนวนครั้ง') ";
		}
		$db->send_cmd($cmd);
		//echo $cmd;
		if($NUMBER_OF_LATE==""){$NUMBER_OF_LATE='18';}
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_LATE' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'NUMBER_OF_LATE',
							config_value = '$NUMBER_OF_LATE', 
							config_remark = 'จำนวนครั้งที่สาย'
						 where config_name = 'NUMBER_OF_LATE' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_LATE', '18', 'จำนวนครั้งที่สาย') ";
		}
		$db->send_cmd($cmd);
		
		
		if(!$EXTRA_ABSEN_DAY){$EXTRA_ABSEN_DAY='N';}
		$cmd = " select config_name from system_config where config_name = 'EXTRA_ABSEN_DAY' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'EXTRA_ABSEN_DAY',
							config_value = '$EXTRA_ABSEN_DAY', 
							config_remark = 'ได้รับสิทธิพิเศษวันลาสะสม จังหวัดชายแดนใต้'
						 where config_name = 'EXTRA_ABSEN_DAY' ";
		}
		$db->send_cmd($cmd);

                //เปิดแสดงวันที่ลงนามในประวัติการดำงตำแหน่ง
                if(!$CH_SHOW_POH_DATE){$CH_SHOW_POH_DATE='N';}
		$cmd = " select config_name from system_config where config_name = 'CH_SHOW_POH_DATE' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'CH_SHOW_POH_DATE',
							config_value = '$CH_SHOW_POH_DATE', 
							config_remark = 'แสดงวันที่ลงนามในประวัติการดำรงตำแหน่ง'
						 where config_name = 'CH_SHOW_POH_DATE' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'CH_SHOW_POH_DATE', '$CH_SHOW_POH_DATE', 'แสดงวันที่ลงนามในประวัติการดำรงตำแหน่ง') ";
		}
		$db->send_cmd($cmd);
                
                if($GMIS_STATUS_PROCESS){
                    $cmd = " select config_name from system_config where config_name = 'GMIS_STATUS_PROCESS' ";
                    $count_data = $db->send_cmd($cmd);
                    if ($count_data){

                    $cmd = " UPDATE SYSTEM_CONFIG SET 
                                                            CONFIG_NAME = 'GMIS_STATUS_PROCESS',
                                                            CONFIG_VALUE = '$GMIS_STATUS_PROCESS'
                                                     WHERE CONFIG_NAME = 'GMIS_STATUS_PROCESS' ";
                    }
                    $db->send_cmd($cmd);
                }    
                
                
                /*Release 5.2.1.14 End*/ 
		
//		   /*Release 5.2.1.21 End*/
//        if(!$CHANGE_PASSWORD){$CHANGE_PASSWORD='N';}
//                $cmd = " select config_name from system_config where config_name = 'CHANGE_PASSWORD' ";
//		$count_data = $db->send_cmd($cmd);
//		if ($count_data){
//			$cmd = " update system_config set 
//									config_name = 'CHANGE_PASSWORD',
//									config_value = '$CHANGE_PASSWORD', 
//									config_remark = 'เรียกใช้ฟังก์ชันลืมรหัสผ่าน'
//								 where config_name = 'CHANGE_PASSWORD' ";
//                }else{
//                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
//			$cmd = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
//									VALUES ($MAXOFCONFIG_ID,'CHANGE_PASSWORD','N','เรียกใช้ฟังก์ชันลืมรหัสผ่าน')";	
//                }
//		$db->send_cmd($cmd);
//                
//                /*Release 5.2.1.21 End*/     
//          
                
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
        
        
        
        //die();
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
			$f_center = "Y"; 
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
				$f_center = "N"; 
			} // end if(trim($data_u[dpisdb]))
		} // end if if ($db->send_cmd($cmd_u))
	} // end if ($condition)
	// จบส่วนสำหรับ user login ที่ กำหนดการเข้าถึง ฐานข้อมูลจำเพาะเอาไว้

//	echo "center=$f_center user connect->$DPISDB, $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd<br>";

//	echo "DPISDB:$DPISDB==db_type:$db_type<br>";
	if ($DPISDB) {
		if($DPISDB!=$db_type)  include("$ROOTPATH/php_scripts/connect_".$DPISDB.".php");
		if($DPISDB=="mysql"){
			class connect_dpis extends connect_mysql { };
		}elseif($DPISDB=="mssql"){
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

	if ($DPISDB) {
		if($DPISDB=="odbc"){
			class connect_att extends connect_odbc { };
		}else{
			if($DPIS35DB!="odbc") include("$ROOTPATH/php_scripts/connect_odbc.php");
			class connect_att extends connect_odbc { };
		} // end if

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
                
                /*ตัวนีืหากทำการ config ผิด จะทำให้ช้ามาก*/
		/*if ($DPIS35DB) 
				$db_dpis35 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);*/
		
                if ($ATTDB) $db_att = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);

		ini_set ("session.gc_maxlifetime", "7200");
		$CATE_FID = 1;
		$SYSLANGCODE = array("TH" , "EN");
		$FontLangCode =  substr(dirname($PHP_SELF),-2);	
		//$ARR_GROUP_CODE = array ( 1 => "ADMIN", 2 => "USER", 3 => "BUREAU");
         //$ARR_GROUP_CODE = array ( 1 => "ADMIN", 2 => "USER", 3 => "BUREAU",4=>"HRD",5=>"HRG",6=>"REGISTER");
        $ARR_GROUP_CODE = array ( 1 => "ADMIN", 2 => "USER", 3 => "BUREAU",4=>"HRD",5=>"HRG",6=>"REGISTER",7=>"OT");
//		echo "f_center=$f_center && grp_level=$dpisdb_group_level && org_id=$dpisdb_org_id<br>";
		if ($f_center == "N" && $dpisdb_group_level && $dpisdb_org_id) { // กรณีกำหนดการ connect ไว้ที่ user_group
			$CTRL_TYPE = $dpisdb_group_level; // ให้ระดับเริ่มต้นเป็น level ที่กำหนดที่ user_group
			$TEMP_ORG_ID = $dpisdb_org_id;
		} else {
			$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
			$db_dpis->send_cmd($cmd);
		//	$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$CTRL_TYPE = $data[CTRL_TYPE];
			if(!$CTRL_TYPE) $CTRL_TYPE = 4;
			$TEMP_ORG_ID = $data[ORG_ID];
		}
		$TEMP_ORG_NAME = "";
		if($TEMP_ORG_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TEMP_ORG_ID ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$TEMP_ORG_NAME = $data[ORG_NAME];
		}

		if(!$SESS_PER_TYPE)	$SESS_PER_TYPE = 0;
		$SAL_PER_TYPE = 2;
		if ($TEMP_ORG_NAME=="สถาบันบัณฑิตพัฒนศิลป์"){
			if($SESS_PER_TYPE==0){		//ทั้งหมด	
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"อาจารย์",5=>"ครู"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ",3=>"พนักงานราชการ"); 
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"ข้าราชการ"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ"); 
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"ลูกจ้างประจำ"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==3){
				$PERSON_TYPE=array(3=>"พนักงานราชการ"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(3=>"พนักงานราชการ"); 
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"อาจารย์"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==5){
				$PERSON_TYPE=array(5=>"ครู"); //ประเภทบุคคล
			}else{ //ค่าเริ่มต้น
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"อาจารย์",5=>"ครู"); //ประเภทบุคคล			
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ",3=>"พนักงานราชการ"); 
			}
			$PERSON_TYPE_USERGROUP=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"อาจารย์",5=>"ครู"); //ประเภทบุคคล			
		}elseif ($TEMP_ORG_NAME=="กรุงเทพมหานคร" || strstr($TEMP_ORG_NAME,"กรุงเทพมหานคร")) { 
			if($SESS_PER_TYPE==0){		//ทั้งหมด	
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"ข้าราชการ"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"ลูกจ้างประจำ"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
			}else{ //ค่าเริ่มต้น
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
			}
			$PERSON_TYPE_USERGROUP=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
			$PERSON_TYPE=array(1=>"ข้าราชการ"); //ประเภทบุคคล
			$PERSON_TYPE_USERGROUP=array(1=>"ข้าราชการ"); //ประเภทบุคคล
			$PERSON_TYPE_KPI=array(1=>"ข้าราชการ"); 
			$SAL_PER_TYPE = 1;
//		}elseif ($CTRL_TYPE==1) { // ISCS เอาออก
//			$PERSON_TYPE=array(1=>"ข้าราชการ"); //ประเภทบุคคล
//			$PERSON_TYPE_USERGROUP=array(1=>"ข้าราชการ"); //ประเภทบุคคล
		}else{
			if ($TEMP_ORG_NAME=="เทศบาลนครนนทบุรี") $EMPSER_NAME = "พนักงานจ้าง";
			else $EMPSER_NAME = "พนักงานราชการ";
			if($SESS_PER_TYPE==0){		//ทั้งหมด
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"$EMPSER_NAME",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
				//	$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"$EMPSER_NAME",4=>"ลูกจ้างชั่วคราว",5=>"ข้าราชการครู",6=>"ข้าราชการในสถาบันอุดมศึกษา"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ",3=>"$EMPSER_NAME"); 
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"ข้าราชการ"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ"); 
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"ลูกจ้างประจำ"); //ประเภทบุคคล
			}elseif($SESS_PER_TYPE==3){
				$PERSON_TYPE=array(3=>"$EMPSER_NAME"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(3=>"$EMPSER_NAME"); 
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
			}else{ //ค่าเริ่มต้น
				$PERSON_TYPE=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"$EMPSER_NAME",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
				$PERSON_TYPE_KPI=array(1=>"ข้าราชการ",3=>"$EMPSER_NAME"); 
			}
			$PERSON_TYPE_USERGROUP=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"$EMPSER_NAME",4=>"ลูกจ้างชั่วคราว"); //ประเภทบุคคล
		}

		if ($TEMP_ORG_NAME=="กรุงเทพมหานคร" || strstr($TEMP_ORG_NAME,"กรุงเทพมหานคร")) {
			$MINISTRY_TITLE = "กรุงเทพมหานคร";
			$MINISTRY_ALERT = "กรุณาระบุกรุงเทพมหานคร";
			$MINISTRY_SELECT = "เลือกกรุงเทพมหานคร";
			$DEPARTMENT_TITLE = "หน่วยงาน";
			$DEPARTMENT_ALERT = "กรุณาระบุหน่วยงาน";
			$DEPARTMENT_SELECT = "เลือกหน่วยงาน";
			$ORG_TITLE = "ส่วนราชการ";
			$ORG_ALERT = "กรุณาระบุส่วนราชการ";
			$ORG_SELECT = "เลือกส่วนราชการ";
			$ORG_TITLE1 = "ฝ่าย/กลุ่มงาน/กลุ่ม";
			$ORG1_ALERT = "กรุณาระบุฝ่าย/กลุ่มงาน/กลุ่ม";
			$ORG1_SELECT = "เลือกฝ่าย/กลุ่มงาน/กลุ่ม";
			$ORG_TITLE2 = "งาน";
			$ORG2_ALERT = "กรุณาระบุงาน";
			$ORG2_SELECT = "เลือกงาน";
			$ALL_REPORT_TITLE = "ทั้งหมด";
			$ORG_CODE_TITLE = "รหัสหน่วยงาน/ส่วนราชการ";
			$ORG_NAME_TITLE = "ชื่อหน่วยงาน/ส่วนราชการ";
			$ORG_SHORT_TITLE = "ชื่อย่อหน่วยงาน/ส่วนราชการ";
			$OL_TITLE = "ฐานะของหน่วยงาน/ส่วนราชการ";
			$ORG_ACTIVE_TITLE = "สถานภาพของหน่วยงาน/ส่วนราชการ";
			$PL_TITLE = "ชื่อตำแหน่งในสายงาน";
			$PM_TITLE = "ชื่อตำแหน่งทางบริหาร";
			$PT_TITLE1 = "ตามพรบ. พ.ศ. 2528";
			$PT_TITLE2 = "ตามพรบ. พ.ศ. 2554";
			$POS_DATE_TITLE = "วันที่ ก.ก. กำหนดตำแหน่ง";
			$OFF_TYPE_TITLE = "ประเภทข้าราชการ";
			$OCCUPYDATE_TITLE = "วันที่เข้าสู่หน่วยงาน/ส่วนราชการ";
			$POH_EFFECTIVEDATE_TITLE = "วันที่เข้าดำรงตำแหน่ง";
			$POH_ENDDATE_TITLE = "วันที่สิ้นสุดการดำรงตำแหน่ง";
			$SAH_EFFECTIVEDATE_TITLE = "วันที่มีผลบังคับใช้";
			$SAH_ENDDATE_TITLE = "ถึงวันที่";
//			$POH_EFFECTIVEDATE_TITLE = "วันที่เข้าดำรงตำแหน่ง";
			$LOCAL_TITLE = "ส่วนราชการ";
			$RETIRE_TITLE = "พ้นจากราชการ";
			$LAYER_TYPE_TITLE = "พรบ.2554";
			$PFR_NAME_TITLE = "กลยุทธ์";
			$AP_TITLE = "เขต";
			$DT_TITLE = "แขวง";
			$KP7_TITLE = "ก.ก.1";
			$PT_DATE = "2011-08-15";
			$KPI_CYCLE_TITLE = "ระยะการประเมิน";
			$KPI_CYCLE1_TITLE = "ระยะที่ 1";
			$KPI_CYCLE2_TITLE = "ระยะที่ 2";
			$COM_HEAD_01 = "<**1**>ตำแหน่งและหน่วยงาน";
			$COM_HEAD_02 = "<**2**>ตำแหน่งและหน่วยงาน";
			$COM_HEAD_03 = "<**3**>ตำแหน่งและหน่วยงาน";
			$FULLNAME_HEAD = "ชื่อ - นามสกุล";
			$EL_CODE_BACHELOR = "40";
			$EL_NAME_BACHELOR = "ปริญญาตรีหรือเทียบเท่า";
			$EL_CODE_MASTER = "60";
			$EL_NAME_MASTER = "ปริญญาโทหรือเทียบเท่า";
			$EL_CODE_DOCTOR = "80";
			$EL_NAME_DOCTOR = "ปริญญาเอกหรือเทียบเท่า";
			$SM_CODE_00 = "1";
			$SM_CODE_05 = "2";
			$SM_CODE_10 = "3";
			$SM_CODE_15 = "4";
			$SM_CODE_20 = "5";
		} else {
			if ($TEMP_ORG_NAME=="เทศบาลนครนนทบุรี") {
				$MINISTRY_TITLE = "ราชการบริหารส่วนท้องถิ่น";
				$MINISTRY_ALERT = "กรุณาระบุราชการบริหารส่วนท้องถิ่น";
				$MINISTRY_SELECT = "เลือกราชการบริหารส่วนท้องถิ่น";
				$DEPARTMENT_TITLE = "เทศบาล";
				$DEPARTMENT_ALERT = "กรุณาระบุเทศบาล";
				$DEPARTMENT_SELECT = "เลือกเทศบาล";
				$ORG_TITLE = "กอง/สำนัก";
				$ORG_ALERT = "กรุณาระบุกอง/สำนัก";
				$ORG_SELECT = "เลือกกอง/สำนัก";
				$ORG_TITLE1 = "ส่วน";
				$ORG1_ALERT = "กรุณาระบุส่วน";
				$ORG1_SELECT = "เลือกส่วน";
				$ORG_TITLE2 = "ฝ่าย";
				$ORG2_ALERT = "กรุณาระบุฝ่าย";
				$ORG2_SELECT = "เลือกฝ่าย";
				$ORG_TITLE3 = "กลุ่มงาน";
				$ORG3_ALERT = "กรุณาระบุกลุ่มงาน";
				$ORG3_SELECT = "เลือกกลุ่มงาน";
				$ORG_TITLE4 = "งาน";
				$ORG4_ALERT = "กรุณาระบุงาน";
				$ORG4_SELECT = "เลือกงาน";
				$ORG_TITLE5 = "ต่ำกว่ากอง/สำนัก 5 ระดับ";
				$ORG5_ALERT = "กรุณาระบุต่ำกว่ากอง/สำนัก 5 ระดับ";
				$ORG5_SELECT = "เลือกต่ำกว่ากอง/สำนัก 5 ระดับ";
				$RPT_N = "";
			} else {
				$MINISTRY_TITLE = "กระทรวง";
				$MINISTRY_ALERT = "กรุณาระบุกระทรวง";
				$MINISTRY_SELECT = "เลือกกระทรวง";
				$DEPARTMENT_TITLE = "กรม";
				$DEPARTMENT_ALERT = "กรุณาระบุกรม";
				$DEPARTMENT_SELECT = "เลือกกรม";
				if ($TEMP_ORG_NAME=="กรมการปกครอง") 
					$ORG_TITLE = "เทียบเท่าสำนัก/กอง";
				else
					$ORG_TITLE = "สำนัก/กอง";
				$ORG_ALERT = "กรุณาระบุสำนัก/กอง";
				$ORG_SELECT = "เลือกสำนัก/กอง";
				$ORG_TITLE1 = "ต่ำกว่าสำนัก/กอง 1 ระดับ";
				$ORG1_ALERT = "กรุณาระบุต่ำกว่าสำนัก/กอง 1 ระดับ";
				$ORG1_SELECT = "เลือกต่ำกว่าสำนัก/กอง 1 ระดับ";
				$ORG_TITLE2 = "ต่ำกว่าสำนัก/กอง 2 ระดับ";
				$ORG2_ALERT = "กรุณาระบุต่ำกว่าสำนัก/กอง 2 ระดับ";
				$ORG2_SELECT = "เลือกต่ำกว่าสำนัก/กอง 2 ระดับ";
				$ORG_TITLE3 = "ต่ำกว่าสำนัก/กอง 3 ระดับ";
				$ORG3_ALERT = "กรุณาระบุต่ำกว่าสำนัก/กอง 3 ระดับ";
				$ORG3_SELECT = "เลือกต่ำกว่าสำนัก/กอง 3 ระดับ";
				$ORG_TITLE4 = "ต่ำกว่าสำนัก/กอง 4 ระดับ";
				$ORG4_ALERT = "กรุณาระบุต่ำกว่าสำนัก/กอง 4 ระดับ";
				$ORG4_SELECT = "เลือกต่ำกว่าสำนัก/กอง 4 ระดับ";
				$ORG_TITLE5 = "ต่ำกว่าสำนัก/กอง 5 ระดับ";
				$ORG5_ALERT = "กรุณาระบุต่ำกว่าสำนัก/กอง 5 ระดับ";
				$ORG5_SELECT = "เลือกต่ำกว่าสำนัก/กอง 5 ระดับ";
			}
			$ALL_REPORT_TITLE = "ทั้งส่วนราชการ";
			$ORG_CODE_TITLE = "รหัสส่วนราชการ";
			$ORG_NAME_TITLE = "ชื่อส่วนราชการ";
			$ORG_SHORT_TITLE = "ชื่อย่อส่วนราชการ";
			$OL_TITLE = "ฐานะของส่วนราชการ";
			$ORG_ACTIVE_TITLE = "สถานภาพของส่วนราชการ";
			$PL_TITLE = "ตำแหน่งในสายงาน";
			$PM_TITLE = "ตำแหน่งในการบริหารงาน";
			$PT_TITLE1 = "ตามพรบ. พ.ศ. 2535";
			$PT_TITLE2 = "ตามพรบ. พ.ศ. 2551";
			$POS_DATE_TITLE = "วันที่ อ.ก.พ. กำหนดตำแหน่ง";
			$OFF_TYPE_TITLE = "สังกัดบุคลากร";
			$OCCUPYDATE_TITLE = "วันที่เข้าส่วนราชการ";
			$POH_EFFECTIVEDATE_TITLE = "วันที่มีผล";
			$POH_ENDDATE_TITLE = "วันที่สิ้นสุด";
			$SAH_EFFECTIVEDATE_TITLE = "วันที่มีผล";
			$SAH_ENDDATE_TITLE = "วันที่สิ้นสุด";
			if ($MFA_FLAG==1) $LOCAL_TITLE = "สำนัก/กอง";
			else $LOCAL_TITLE = "ภูมิภาค";
			$RETIRE_TITLE = "พ้นจากส่วนราชการ";
			$LAYER_TYPE_TITLE = "พรบ.2551/พนักงานราชการ";
			$PFR_NAME_TITLE = "การประเมินผลการปฏิบัติราชการ";
			$AP_TITLE = "อำเภอ";
			$DT_TITLE = "ตำบล";
			$KP7_TITLE = "ก.พ.7";
			$PT_DATE = "2008-12-11";
			$KPI_CYCLE_TITLE = "รอบการประเมิน";
			$KPI_CYCLE1_TITLE = "ครั้งที่ 1";
			$KPI_CYCLE2_TITLE = "ครั้งที่ 2";
			$COM_HEAD_01 = "<**1**>ตำแหน่งและส่วนราชการ";
			$COM_HEAD_02 = "<**2**>ตำแหน่งและส่วนราชการ";
			$COM_HEAD_03 = "<**3**>ตำแหน่งและส่วนราชการ";
			$FULLNAME_HEAD = "ชื่อ-นามสกุล";
			$EL_CODE_BACHELOR = "40";
			$EL_NAME_BACHELOR = "ปริญญาตรีหรือเทียบเท่า";
			$EL_CODE_MASTER = "60";
			$EL_NAME_MASTER = "ปริญญาโทหรือเทียบเท่า";
			$EL_CODE_DOCTOR = "80";
			$EL_NAME_DOCTOR = "ปริญญาเอกหรือเทียบเท่า";
			$SM_CODE_00 = "10";
			$SM_CODE_05 = "1";
			$SM_CODE_10 = "2";
			$SM_CODE_15 = "3";
			$SM_CODE_20 = "4";
		}

		$ORG_BKK_TITLE  = "ผู้บริหาร";
		if ($ISCS_FLAG==1) $LAYER_TYPE_TITLE = "พรบ.2551";
		$OT_TITLE = "สังกัด";
		$ORG_ADDR1_TITLE = "สถานที่ตั้ง";
		$ORG_JOB_TITLE = "หน้าที่ความรับผิดชอบ";
		$POS_SALARY_TITLE = "อัตราเงินเดือนถือจ่าย";
		$POS_SITUATION_TITLE = "สถานภาพของตำแหน่ง";
		$POS_GET_DATE_TITLE = "วันที่ตำแหน่งมีเงิน";
		$POS_CHANGE_DATE_TITLE = "วันที่ตำแหน่งเปลี่ยนสถานภาพ";
		$POS_VACANT_DATE_TITLE = "วันที่ตำแหน่งว่าง";
		$POS_STATUS_TITLE = "สถานะของตำแหน่ง";
		$STARTDATE_TITLE = "วันที่เข้ารับราชการ";
		$BL_TITLE = "หมู่โลหิต";
		$PV_PER_TITLE = "ภูมิลำเนาเดิม";
		$CT_TITLE = "ประเทศ";
		$PV_TITLE = "จังหวัด";
		$POS_NO_TITLE = "เลขที่ตำแหน่ง";
		$PAY_NO_TITLE = "เลขถือจ่าย";
		$SEQ_NO_TITLE = "ลำดับที่";
		$PT_TITLE = "ประเภทตำแหน่ง";
		$LEVEL_TITLE = "ระดับตำแหน่ง";
		$PER_LEVEL_TITLE = "ระดับของผู้ดำรงตำแหน่ง";
		$MOV_TITLE = "ประเภทการเคลื่อนไหว";
		$REMARK_TITLE = "หมายเหตุ";
		$SALARY_TITLE = "อัตราเงินเดือน";
		$MGTSALARY_TITLE = "เงินประจำตำแหน่ง";
		$DOCNO_TITLE = "เลขที่คำสั่ง";
		$DOCDATE_TITLE = "ลงวันที่";
		$UPDATE_USER_TITLE = "แก้ไขโดย";
		$UPDATE_DATE_TITLE = "วันที่แก้ไข";
		$TRN_STARTDATE_TITLE = "วันที่เริ่มต้น";
		$TRN_ENDDATE_TITLE = "วันที่สิ้นสุด";
		$TRN_PROJECT_NAME_TITLE = "โครงการฝึกอบรม";
		$COM_NO_TITLE = "คำสั่งเลขที่";
		$COM_DATE_TITLE = "ลงวันที่";
		$COM_NAME_TITLE = "เรื่อง";
		$COM_TYPE_TITLE = "ประเภทคำสั่ง";
		$COM_NOTE_TITLE = "หมายเหตุท้ายคำสั่ง";
		$COM_DETAIL_TITLE = "รายละเอียดของคำสั่ง";
//		$COM_ORDER_TITLE = "บัญชีแนบท้าย เรียงตาม";
		$COM_ORDER_TITLE = "เรียงตาม";
		$COM_ORDER2_TITLE = "สังกัด, เลขที่ตำแหน่ง";
		$COM_DEL_TITLE = "ลบบัญชี";
		$COM_ADD_TITLE = "เพิ่มบัญชี";
		$COM_EDIT_TITLE = "แก้ไขบัญชี";
		$COM_CONFIRM_TITLE = "ยืนยันคำสั่ง";
		$COM_SEND_TITLE = "ส่งคำสั่ง";
		$COM_SEARCH_TITLE = "ค้นหาบัญชี";
		$DEL_PERSON_TITLE = "ลบรายการบุคคล";
		$EDIT_PERSON_TITLE = "แก้ไขรายการบุคคล";
		$ADD_PERSON_TITLE = "เพิ่มรายการบุคคล";
		$SELECT_PERSON_TITLE = "เลือกรายการบุคคล";
		$FULLNAME_TITLE = "ชื่อ-สกุล";
		$PER_TYPE_TITLE = "ประเภทบุคลากร";
		$PRENAME_TITLE = "คำนำหน้าชื่อ";
		$NAME_TITLE = "ชื่อ";
		$SHORTNAME_TITLE = "ชื่อย่อ";
		$OTHERNAME_TITLE = "ชื่ออื่นๆ รูปแบบ ||xx||yy||";
		$SURNAME_TITLE = "นามสกุล";
		$NICKNAME_TITLE = "ชื่อเล่น";
		$CARDNO_TITLE = "เลขประจำตัวประชาชน";
		$OFFNO_TITLE = "เลขประจำตัวข้าราชการ";
		$TAXNO_TITLE = "เลขประจำตัวผู้เสียภาษีอากร";
		$SEX_TITLE = "เพศ";
		$RE_TITLE = "ศาสนา";
		$MR_TITLE = "สถานภาพสมรส";
		$FATHER_TITLE = "บิดา";
		$MOTHER_TITLE = "มารดา";
		$EMAIL_TITLE = "อีเมล์";
		$BANK_ACCOUNT_TITLE = "เลขที่บัญชีธนาคาร";
		$CERT_OCC_TITLE = "เลขที่ใบประกอบวิชาชีพ";
		$DEH_RECEIVE_DATE_TITLE = "วันที่ราชกิจจานุเบกษา";
		$DEH_DATE_TITLE = "วันที่ได้รับเครื่องราชฯ";
		$DEH_RETURN_DATE_TITLE = "วันที่ส่งคืนเครื่องราชฯ";

		$PL_NAME_WORK_TITLE = "ตำแหน่ง ($KP7_TITLE)";
		$ORG_NAME_WORK_TITLE = "ส่วนราชการ ($KP7_TITLE)";
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
		$INS_TITLE = "สถาบันการศึกษา";
		$CT_EDU_TITLE = "ประเทศที่สำเร็จการศึกษา";
		$PER_STATUS_TITLE = "สถานภาพ";
		$CL_TITLE = "ช่วงระดับตำแหน่ง";
		$SG_TITLE = "ด้านความเชี่ยวชาญ";
		$SKILL_TITLE = "สาขาความเชี่ยวชาญ";
		$PC_TITLE = "เงื่อนไขตำแหน่ง";
		$FILE_NO_TITLE = "เลขที่แฟ้ม";
		$COOPERATIVE_NO_TITLE = "เลขทะเบียนสมาชิกสหกรณ์";
		$REPORT_CONDITION = "เงื่อนไขการออกรายงาน";
		$REPORT_FORMAT = "รูปแบบการออกรายงาน";
		$REPORT_ORDER = "จำแนกข้อมูลตาม";
		$LAW_STRUCTURE_TITLE = "โครงสร้างตามกฎหมาย";
		$ASSIGN_STRUCTURE_TITLE = "โครงสร้างตามมอบหมายงาน";
		$ORG_TYPE1_TITLE = "ส่วนกลาง";
		$ORG_TYPE2_TITLE = "ส่วนกลางในภูมิภาค";
		$ORG_TYPE3_TITLE = "ส่วนภูมิภาค";
		$ORG_TYPE4_TITLE = "ต่างประเทศ";
		$POSITION_TITLE = "ตำแหน่งข้าราชการ";
		$POS_EMP_TITLE = "ตำแหน่งลูกจ้างประจำ";
		$POS_EMPSER_TITLE = "ตำแหน่งพนักงานราชการ";
		$POS_TEMP_TITLE = "ตำแหน่งลูกจ้างชั่วคราว";
		$PERSON_TITLE = "บุคลากร";
		$BOOK_NO_TITLE = "เลขที่หนังสือนำส่ง";
		$PER_JOB_TITLE = "หน้าที่ความรับผิดชอบ";
		$EXPORT_TITLE = "ถ่ายโอนข้อมูล";
		$IMPORT_TITLE = "นำเข้าข้อมูล";
		$ACTIVE_TITLE = "ใช้งาน/ยกเลิก";
		$CGD_TITLE = "รหัสกรมบัญชีกลาง";
		$COMPETENCE_TITLE = "สมรรถนะ";
		$KPI_TYPE_TITLE = "ประเภทตัวชี้วัด";
		$KPI_SUCCESS_TITLE = "ระดับผลสำเร็จตัวชี้วัด";
		$KPI_LEVEL_TITLE = "ระดับตัวชี้วัด";
		$KPI_DEFINE_TITLE = "คำนิยามและวิธีการคำนวณ";
		$PJ_TYPE_TITLE = "ประเภทโครงการ";
		$PJ_CLASS_TITLE = "ชนิดของโครงการ";
		$PJ_STATUS_TITLE = "สถานภาพของโครงการ";
		$PJ_IMPORTANCE_TITLE = "ระดับความสำคัญ";
		$PJ_OBJECTIVE_TITLE = "วัตถุประสงค์ของโครงการ";
		$PJ_TARGET_TITLE = "เป้าหมายของโครงการ";
		$START_DATE_TITLE = "วันที่เริ่มต้น";
		$END_DATE_TITLE = "วันที่สิ้นสุด";
		$COM_TITLE = "บัญชีแนบท้ายคำสั่ง";
		$PJ_NAME_TITLE = "ชื่อโครงการ";
		$KPI_NAME_TITLE = "ชื่อตัวชี้วัด";
		$REPORT_FORMAT_TITLE = "รูปแบบรายงาน";
		$YEAR_TITLE = "ปีงบประมาณ";
		$TENA_TITLE = "นปร.";
		$CA_TEST_DATE_TITLE = "วันที่เข้ารับการประเมิน";
		$CA_APPROVE_DATE_TITLE = "วันที่ขึ้นบัญชี";
		$COMTYPE1_1_TITLE = "บรรจุผู้สอบแข่งขันได้";
		$COMTYPE2_1_TITLE = "ย้ายข้าราชการ";
		$COMTYPE2_6_TITLE = "จัดคนลง";
		$COMTYPE3_5_TITLE = "ให้โอนข้าราชการ";
		$COMTYPE4_1_TITLE = "เลื่อนข้าราชการ";
		$COMTYPE6_TITLE = "ให้ข้าราชการได้รับเงินเดือนตามคุณวุฒิ";
		$COMTYPE9_TITLE = "ให้ข้าราชการรักษาราชการแทน";
		$COMTYPE7_TITLE = "เลื่อนเงินเดือน";
		$COMTYPE7_1_TITLE = "เลื่อนเงินเดือนข้าราชการ";
		$COMTYPE12_TITLE = "แก้ไขคำสั่งที่ผิดพลาด";
		$COMTYPE13_TITLE = "ยกเลิกคำสั่งเดิม";
		$COMTYPE21_1_TITLE = "มอบหมายให้ปฏิบัติราชการ";
		$COMTYPE22_TITLE = "ช่วยราชการ";
		$COMTYPE23_TITLE = "ตัดโอนอัตราเงินเดือน";
		$COMTYPE24_TITLE = "ปรับอัตราเงินเดือน";
		$COMTYPE241_TITLE = "ให้ข้าราชการได้รับเงินเดือนตามบัญชีเงินเดือนขั้นต่ำขั้นสูงที่ได้รับการปรับใหม่";
		$COMTYPE25_TITLE = "จ่ายเงินรางวัลประจำปี";
		$COMTYPE28_TITLE = "ตัดโอนตำแหน่งและอัตราเงินเดือน";
		$SM_TITLE = "จำนวนขั้นเงินเดือน";
		$EX_TITLE = "ประเภทเงินเพิ่มพิเศษ";
		if ($BKK_FLAG==1) { 
			$SORT_TITLE = '&nbsp;&nbsp;<font color="#FF0000"><B>!</B></font> <font color="#000000">สามารถเรียงลำดับได้</font>, &nbsp;<img src="images/b_arrow_down.png" border="0">&nbsp;เรียงจากมากไปน้อย , &nbsp;<img src="images/b_arrow_up.png" border="0">&nbsp;เรียงจากน้อยไปมาก';
			$SORT_CUR="<font SIZE='3' color='#FF0000'><b>!</b></font>";
			$SORT_ASC="<img src='images/b_arrow_up.png' border='0'>";
			$SORT_DESC="<img src='images/b_arrow_down.png' border='0'>";
		} else {
			$SORT_TITLE = '&nbsp;&nbsp;<font color="#FF0000"><B>*</B></font> <font color="#000000">สามารถเรียงลำดับได้</font>, &nbsp;<img src="images/b_arrow_down.png" border="0">&nbsp;เรียงจากมากไปน้อย , &nbsp;<img src="images/b_arrow_up.png" border="0">&nbsp;เรียงจากน้อยไปมาก';
			$SORT_CUR="<font SIZE='3' color='#FF0000'><b>*</b></font>";
			$SORT_ASC="<img src='images/b_arrow_up.png' border='0'>";
			$SORT_DESC="<img src='images/b_arrow_down.png' border='0'>";
		}
		$SHOW_GRAPH_TITLE = "แสดงกราฟ";
		$CLOSE_WINDOW_TITLE = "ปิดหน้าต่าง";
		$SHOW_ALL_TITLE = "แสดงทั้งหมด";
		$EXCEL_TITLE = "ส่งออกไฟล์ Excel";
		$PDF_TITLE = "ดูรายงานรูปแบบ PDF";
		$RTF_TITLE = "ดูรายงานรูปแบบ RTF";
//		if ($BKK_FLAG==1)
//			$CLEAR_TITLE = "เคลียร์";
//		else
			$CLEAR_TITLE = "ล้างหน้าจอ";
		$SELECT_TITLE = "เลือก";
		$CANCEL_TITLE = "ยกเลิก";
		$SEARCH_TITLE = "ค้นหาข้อมูล";
		$ADD_TITLE = "เพิ่มข้อมูล";
		$ADDTAB_TITLE = "คลิกเพื่อเพิ่ม";
		$EDIT_TITLE = "แก้ไข";/*เดิม*/
                $BNT_EDIT_TITLE = "บันทึก";/*Release 5.1.0.8 */
		$DEL_TITLE = "ลบ";
		$INQ_TITLE = "เรียกดู";
		$PRINT_TITLE = "พิมพ์";
		$CONFIRM_TITLE = "ยืนยัน";
		$LOAD_TITLE = "แนบไฟล์";
		$ALT_LOAD_TITLE = "แนบไฟล์ข้อมูล";
		$AUDIT_TITLE = "ตรวจสอบ";
		$AUDITED_TITLE = "ตรวจแล้ว";
		$DETAIL_TITLE = "รายละเอียด";
		$INQUIRE_TITLE = "สอบถาม";
		$SAVE_SEARCH_TITLE = "บันทึกผลการค้นหา";
		$REORDER_TITLE = "จัดลำดับ";
		$SETFLAG_TITLE = "บันทึก";
		$SAVE_TITLE = "บันทึก";
		$CODE_TITLE = "รหัส";
		$CARDNO_PRINT_TITLE = "เลขที่บัตรอยู่ด้านล่างชื่อ";
		$WEIGHT_PROBATION = 50;
		if ($MUNICIPALITY_FLAG==1) 
			$LIST_LEVEL_NO="('01','02','03','04','05','06','07','08','09','10','11')";
		elseif ($NOT_LEVEL_NO_O4=="Y") 
			$LIST_LEVEL_NO="('O1','O2','O3','K1','K2','K3','K4','K5','D1','D2','M1','M2')";
		else
			$LIST_LEVEL_NO="('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2')";
		if ($ISCS_FLAG==1) $LIST_LEVEL_NO="('O4','K4','K5','D1','D2','M1','M2')";
		$ARR_OCCUPATION_GROUP=array(1=>"กลุ่มอาชีพบริหาร อำนวยการ ธุรการ งานสถิติ งานนิติการ งานการฑูตและต่างประเทศ",2=>"กลุ่มอาชีพการคลัง การเศรษฐกิจ การพาณิชย์และอุตสาหกรรม",3=>"กลุ่มอาชีพคมนาคม ขนส่ง และติดต่อสื่อสาร",4=>"กลุ่มอาชีพเกษตรกรรม",5=>"กลุ่มอาชีพวิทยาศาสตร์",6=>"กลุ่มอาชีพแพทย์ พยาบาล และสาธารณสุข",7=>"กลุ่มอาชีพวิศวกรรม สถาปัตยกรรม และช่างเทคนิคต่างๆ",8=>"กลุ่มอาชีพการศึกษา ศิลป สังคม และการพัฒนาชุมชน"); //กลุ่มอาชีพ
		$max_execution_time = 30000;
		$maxsize_up_file_label = "<span class='label_alert'>ขนาดของไฟล์แนบไม่เกิน ".$maxsize_up_file." MB</span>";
		$ARR_PFR_TYPE=array(0=>"", 1=>"ยุทธศาสตร์",2=>"ประเด็นยุทธศาสตร์",3=>"กลยุทธ์"); //ประเภทการประเมินผล
		$ARR_KPI_FORM_TYPE=array(1=>"ตัวชี้วัดตามแผนปฏิบัติราชการประจำปี",2=>"ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก",3=>"ตัวชี้วัดตามงานที่ได้รับมอบหมายพิเศษ"); //ประเภทตัวชี้วัด
		$ARR_KPI_TYPE=array(1=>"ตัวชี้วัดเชิงยุทธศาสตร์ตามแผนปฏิบัติราชการของหน่วยงาน",2=>"ตัวชี้วัดงานประจำตามแผนปฏิบัติราชการของหน่วยงาน"); //ประเภทตัวชี้วัด
		$ARR_KPI_SUCCESS_LEVEL=array(1=>"ตัวชี้วัดระดับผลผลิต (Output)",2=>"ตัวชี้วัดระดับผลลัพธ์ (Outcome)"); //ระดับผลสำเร็จตัวชี้วัด
		$ARR_KPI_LEVEL=array(1=>"ตัวชี้วัดระดับประเด็นยุทธศาสตร์",2=>"ตัวชี้วัดระดับกลยุทธ์"); //ระดับตัวชี้วัด
		$ARR_PROJECT_IMPORTANCE=array(1=>"โครงการตามแผนปฏิบัติราชการ",2=>"โครงการ/กิจกรรมที่ได้รับมอบหมายเพิ่มเติม",3=>"โครงการที่ผู้บริหารสนใจเป็นพิเศษ",4=>"โครงการตามนโยบายผู้บริหาร กทม.",5=>"โครงการตามนโยบายของ ก.มหาดไทย",6=>"โครงการตามนโยบายรัฐบาล"); //ระดับความสำคัญของโครงการ
		$ARR_PROJECT_CLASS=array(1=>"โครงการ/กิจกรรมปีเดียว",2=>"โครงการต่อเนื่องปีงบประมาณ"); //ประเภทโครงการ
		$ARR_PROJECT_TYPE=array(1=>"โครงการตามแผน",2=>"โครงการนอกแผน"); //ชนิดของโครงการ
		$ARR_PROJECT_STATUS=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"ลูกจ้างชั่วคราว"); //สถานภาพของโครงการ
		$ARR_PROJECT_TYPE_USERGROUP=array(1=>"ข้าราชการ",2=>"ลูกจ้างประจำ",3=>"พนักงานราชการ",4=>"ลูกจ้างชั่วคราว"); //ระดับความสำคัญของโครงการ
		$ARR_PRINT_FONT=array(1=>"Angsana",2=>"Cordia",3=>"TH Sarabun",4=>"Browallia"); //รูปแบบอักษรในการพิมพ์รายงาน

		if (isset($CH_PRINT_FONT))		$PRINT_FONT = $CH_PRINT_FONT;

		if ($PRINT_FONT==1) {
			$font = 'angsa';
			$fontb = 'angsab';
		} else if ($PRINT_FONT==2) {
			$font = 'cordia';
			$fontb = 'cordiab';
		} else if ($PRINT_FONT==4) {
			$font = 'browallia';
			$fontb = 'browalliab';
		} else { 
			$font = 'thsarabun';
			$fontb = 'thsarabunb';
		}

		if($MENU_ID_LV0){
			$cmd = " select menu_label from backoffice_menu_bar_lv0 where langcode='TH' and menu_id=$MENU_ID_LV0 ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$MENU_TITLE_LV0 = $data[menu_label];
		} //endif

		if($MENU_ID_LV1){
			$cmd = " select menu_label from backoffice_menu_bar_lv1 where langcode='TH' and menu_id=$MENU_ID_LV1 ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$MENU_TITLE_LV1 = $data[menu_label];
		} // endif

		if($MENU_ID_LV2){
			$cmd = " select menu_label from backoffice_menu_bar_lv2 where langcode='TH' and menu_id=$MENU_ID_LV2 ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$MENU_TITLE_LV2 = $data[menu_label];
		} // endif

		if($MENU_ID_LV3){
			$cmd = " select menu_label from backoffice_menu_bar_lv3 where langcode='TH' and menu_id=$MENU_ID_LV3 ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$MENU_TITLE_LV3 = $data[menu_label];
		} // endif
//		$TEMP_ORG_NAME = "กรมการปกครอง";
	} // end if ($DPISDB)

/*http://dpis.ocsc.go.th/Service/node/1152*/
$cmd = " select config_name from system_config where config_id = 56 and config_value =1 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
$cmd = " update system_config set 
        config_name = 'KPI_SCORE_CONFIRM',
        config_value = 1, 
        config_remark = 'ตรวจสอบการให้คะแนนผลการประเมิน'
        where config_id = 56 ";
        $db->send_cmd($cmd);
/**/        
    $count_data=0;  
      
      
$show_btn_add=FALSE;
if($PAGE_AUTH["add"]=='Y' && $SESS_USERGROUP==3){$show_btn_add=TRUE;}
if($SESS_USERGROUP!=3){$show_btn_add=TRUE;}      
      


/*if(isset($SESS_PASSWORD_KEY)){
    $pos= strpos($_SERVER['REQUEST_URI'],'user_profile.html');
    if( ($SESS_PASSWORD_KEY==$SESS_PASSWORD_DB || $SESS_USERNAME==$SESS_PASSWORD_KEY) && empty($pos) ){
        if($SESS_USERNAME==$SESS_PASSWORD_KEY){
            echo"<script>alert('รหัสผ่านของท่านคาดเดาได้ง่าย เพื่อความปลอดภัยกรุณาเปลี่ยนรหัสผ่านใหม่ \\n !!!  Username และ Password ไม่ควรเป็นค่าเดียวกัน !!!');</script>";
        }else{
            echo"<script>alert('รหัสผ่านของท่านคาดเดาได้ง่าย เพื่อความปลอดภัยกรุณาเปลี่ยนรหัสผ่านใหม่ \\n !!! ไม่ควรเป็นวันเดือนปีเกิด !!!');</script>";
        }
        echo"<script>window.location='user_profile.html?MENU_ID_LV0=12&MENU_ID_LV1=0';</script>";
    }
}*/
?>