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
										config_remark = '�������ҹ����������ͧ�ѹ�֡����'
									 where config_id = 15 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', '�������ҹ����������ͧ�ѹ�֡����') ";
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
			$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', '�������ҹ����������ͧ�ѹ�֡����') ";
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
										config_remark = '�������ҹ�����ŷ���ͧ��ö����͹'
									 where config_id = 21 ";
			else
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', '�������ҹ�����ŷ���ͧ��ö����͹') ";
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
			$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', '�������ҹ�����ŷ���ͧ��ö����͹') ";
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
									config_remark = '�ӹǹ�ä��촵��˹��'
								 where config_id = 10 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (10, 'data_per_page', '$ch_data_per_page', '�ӹǹ�ä��촵��˹��') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 11 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI',
									config_value = '$CH_WEIGHT_KPI', 
									config_remark = '% �š�û����Թ�ͧ����Ҫ��� - ������稢ͧ�ҹ'
								 where config_id = 11 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (11, 'WEIGHT_KPI', '$CH_WEIGHT_KPI', '% �š�û����Թ�ͧ����Ҫ��� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 12 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE',
									config_value = '$CH_WEIGHT_COMPETENCE', 
									config_remark = '% �š�û����Թ�ͧ����Ҫ��� - ���ö��'
								 where config_id = 12 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (12, 'WEIGHT_COMPETENCE', '$CH_WEIGHT_COMPETENCE', '% �š�û����Թ�ͧ����Ҫ��� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 13 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_OTHER',
									config_value = '$CH_WEIGHT_OTHER', 
									config_remark = '% �š�û����Թ�ͧ����Ҫ��� - ��� �'
								 where config_id = 13 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (13, 'WEIGHT_OTHER', '$CH_WEIGHT_OTHER', '% �š�û����Թ�ͧ����Ҫ��� - ��� �') ";
		$db->send_cmd($cmd);
/*			
		$cmd = " select config_name from system_config where config_id = 20 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'RPT_N',
									config_value = '$CH_RPT_N', 
									config_remark = '����Ҫ�ѭ�ѵ�����º����Ҫ��þ����͹ �.�. 2551'
								 where config_id = 20 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (20, 'RPT_N', '$CH_RPT_N', '����Ҫ�ѭ�ѵ�����º����Ҫ��þ����͹ �.�. 2551') ";
		$db->send_cmd($cmd);
*/			
		$cmd = " select config_name from system_config where config_id = 14 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'COMPETENCY_SCALE',
									config_value = '$CH_COMPETENCY_SCALE', 
									config_remark = '�ҵ��Ѵ��û����Թ���ö��'
								 where config_id = 14 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (14, 'COMPETENCY_SCALE', '$CH_COMPETENCY_SCALE', '�ҵ��Ѵ��û����Թ���ö��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 29 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CARD_NO_DISPLAY',
									config_value = '$CH_CARD_NO_DISPLAY', 
									config_remark = '����ʴ����Ţ��Шӵ�ǻ�ЪҪ�'
								 where config_id = 29 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (29, 'CARD_NO_DISPLAY', '$CH_CARD_NO_DISPLAY', '����ʴ����Ţ��Шӵ�ǻ�ЪҪ�') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 30 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'BUTTON_DISPLAY',
									config_value = '$CH_BUTTON_DISPLAY', 
									config_remark = '����ʴ��Ż�����'
								 where config_id = 30 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (30, 'BUTTON_DISPLAY', '$CH_BUTTON_DISPLAY', '����ʴ��Ż�����') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 31 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PER_ORDER_BY',
									config_value = '$CH_ORDER_BY', 
									config_remark = '������§�����źؤ��'
								 where config_id = 31 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (31, 'PER_ORDER_BY', '$CH_ORDER_BY', '������§�����źؤ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 32 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PAYMENT_FLAG',
									config_value = '$CH_PAYMENT_FLAG', 
									config_remark = '��ͨ���'
								 where config_id = 32 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (32, 'PAYMENT_FLAG', '$CH_PAYMENT_FLAG', '��ͨ���') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 33 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI_E',
									config_value = '$CH_WEIGHT_KPI_E', 
									config_remark = '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ������稢ͧ�ҹ'
								 where config_id = 33 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (33, 'WEIGHT_KPI_E', '$CH_WEIGHT_KPI_E', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 34 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE_E',
									config_value = '$CH_WEIGHT_COMPETENCE_E', 
									config_remark = '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ���ö��'
								 where config_id = 34 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (34, 'WEIGHT_COMPETENCE_E', '$CH_WEIGHT_COMPETENCE_E', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 35 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_KPI_S',
									config_value = '$CH_WEIGHT_KPI_S', 
									config_remark = '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ������稢ͧ�ҹ'
								 where config_id = 35 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (35, 'WEIGHT_KPI_S', '$CH_WEIGHT_KPI_S', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 36 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'WEIGHT_COMPETENCE_S',
									config_value = '$CH_WEIGHT_COMPETENCE_S', 
									config_remark = '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ���ö��'
								 where config_id = 36 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (36, 'WEIGHT_COMPETENCE_S', '$CH_WEIGHT_COMPETENCE_S', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 37 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_FONT',
									config_value = '$CH_PRINT_FONT', 
									config_remark = '�ٻẺ�ѡ��㹡�þ������§ҹ'
								 where config_id = 37 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (37, 'PRINT_FONT', '$CH_PRINT_FONT', '�ٻẺ�ѡ��㹡�þ������§ҹ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 38 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'ATTACH_FILE',
									config_value = '$CH_ATTACH_FILE', 
									config_remark = '��èѴ�����Ṻ'
								 where config_id = 38 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (38, 'ATTACH_FILE', '$CH_ATTACH_FILE', '��èѴ�����Ṻ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 39 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'EMPSER_SCORE_METHOD',
									config_value = '$CH_EMPSER_SCORE_METHOD', 
									config_remark = '��äӹǳ��ṹ�š�û�Ժѵ��Ҫ���'
								 where config_id = 39 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (39, 'EMPSER_SCORE_METHOD', '$CH_EMPSER_SCORE_METHOD', '��äӹǳ��ṹ�š�û�Ժѵ��Ҫ���') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 40 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'ORG_SETLEVEL',
									config_value = '$CH_ORG_SETLEVEL', 
									config_remark = '��ӡ����ӹѡ�ͧ'
								 where config_id = 40 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (40, 'ORG_SETLEVEL', '$CH_ORG_SETLEVEL', '��ӡ����ӹѡ�ͧ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 41 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'COMPETENCY_METHOD',
									config_value = '$COMPETENCY_METHOD_TXT', 
									config_remark = '�Ըա�û����Թ'
								 where config_id = 41 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (41, 'COMPETENCY_METHOD', '$COMPETENCY_METHOD_TXT', '�Ըա�û����Թ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 42 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'MENU_TYPE',
									config_value = '$CH_MENU_TYPE', 
									config_remark = '�ٻẺ����'
								 where config_id = 42 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (42, 'MENU_TYPE', '$CH_MENU_TYPE', '�ٻẺ����') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 43 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'DATE_DISPLAY',
									config_value = '$CH_DATE_DISPLAY', 
									config_remark = '����ʴ����ѹ���'
								 where config_id = 43 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (43, 'DATE_DISPLAY', '$CH_DATE_DISPLAY', '����ʴ����ѹ���') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 44 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'POSITION_NO_CHAR',
									config_value = '$CH_POSITION_NO_CHAR', 
									config_remark = '�Ţ�����˹��յ���ѡ��'
								 where config_id = 44 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (44, 'POSITION_NO_CHAR', '$CH_POSITION_NO_CHAR', '�Ţ�����˹��յ���ѡ��') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 45 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'NUMBER_DISPLAY',
									config_value = '$CH_NUMBER_DISPLAY', 
									config_remark = '����ʴ��ŵ���Ţ'
								 where config_id = 45 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (45, 'NUMBER_DISPLAY', '$CH_NUMBER_DISPLAY', '����ʴ��ŵ���Ţ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 46 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'password_age',
									config_value = '$ch_password_age', 
									config_remark = '�������ʼ�ҹ 0= ���ӡѴ����'
								 where config_id = 46 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (46, 'password_age', '$ch_password_age', '�������ʼ�ҹ 0= ���ӡѴ����') ";
		$db->send_cmd($cmd);
		
		$cmd = " delete from system_config where config_id = 47 ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 48 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'maxsize_up_file',
									config_value = '$maxsize_up_file', 
									config_remark = '��Ҵ����٧�ش����Ѿ��Ŵ��'
								 where config_id = 48 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (48, 'maxsize_up_file', '$maxsize_up_file', '��Ҵ����٧�ش����Ѿ��Ŵ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 49 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'E_SIGN',
									config_value = '$E_SIGN_TXT', 
									config_remark = '���������礷�͹ԡ��'
								 where config_id = 49 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (49, 'E_SIGN', '$E_SIGN_TXT', '���������礷�͹ԡ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 50 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_BUDGET_YEAR',
									config_value = '$CH_KPI_BUDGET_YEAR', 
									config_remark = '�է�����ҳ����Ѻ�����Թ��'
								 where config_id = 50 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (50, 'KPI_BUDGET_YEAR', '$CH_KPI_BUDGET_YEAR', '�է�����ҳ����Ѻ�����Թ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 51 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_CYCLE',
									config_value = '$CH_KPI_CYCLE', 
									config_remark = '�ͺ��û����Թ��'
								 where config_id = 51 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (51, 'KPI_CYCLE', '$CH_KPI_CYCLE', '�ͺ��û����Թ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 52 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_PER_REVIEW',
									config_value = '$CH_PER_REVIEW', 
									config_remark = '���������������'
								 where config_id = 52 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (52, 'KPI_PER_REVIEW', '$CH_PER_REVIEW', '���������������') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 53 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CARD_NO_FILL',
									config_value = '$CH_CARD_NO_FILL', 
									config_remark = '��ͧ��͹�Ţ��Шӵ�ǻ�ЪҪ�'
								 where config_id = 53 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (53, 'CARD_NO_FILL', '$CH_CARD_NO_FILL', '��ͧ��͹�Ţ��Шӵ�ǻ�ЪҪ�') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 54 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'start_age',
									config_value = '$ch_start_age', 
									config_remark = '���ط�����è�����Թ'
								 where config_id = 54 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (54, 'start_age', '$ch_start_age', '���ط�����è�����Թ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 55 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'NOT_LEVEL_NO_O4',
									config_value = '$CH_NOT_LEVEL_NO_O4', 
									config_remark = '����յ��˹��дѺ�ѡ�о����'
								 where config_id = 55 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (55, 'NOT_LEVEL_NO_O4', '$CH_NOT_LEVEL_NO_O4', '����յ��˹��дѺ�ѡ�о����') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 56 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
                    /*���*/
			/*$cmd = " update system_config set 
									config_name = 'KPI_SCORE_CONFIRM',
									config_value = '$CH_SCORE_CONFIRM', 
									config_remark = '��Ǩ�ͺ�������ṹ�š�û����Թ'
								 where config_id = 56 ";*/
                    /*http://dpis.ocsc.go.th/Service/node/1152*/
                    $cmd = " update system_config set 
									config_name = 'KPI_SCORE_CONFIRM',
									config_value = 1, 
									config_remark = '��Ǩ�ͺ�������ṹ�š�û����Թ'
								 where config_id = 56 ";
                    /**/
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (56, 'KPI_SCORE_CONFIRM', '$CH_SCORE_CONFIRM', '��Ǩ�ͺ�������ṹ�š�û����Թ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 57 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SAME_ASSESS_LEVEL',
									config_value = '$CH_SAME_ASSESS_LEVEL', 
									config_remark = '���дѺ�š�û����Թ��������͹�ѹ'
								 where config_id = 57 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (57, 'SAME_ASSESS_LEVEL', '$CH_SAME_ASSESS_LEVEL', '���дѺ�š�û����Թ��������͹�ѹ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 58 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_DISPLAY',
									config_value = '$CH_SLIP_DISPLAY', 
									config_remark = '�ٻẺ��Ի�Թ��͹'
								 where config_id = 58 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (58, 'SLIP_DISPLAY', '$CH_SLIP_DISPLAY', '�ٻẺ��Ի�Թ��͹') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 59 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'FILE_PATH_DISPLAY',
									config_value = '$CH_FILE_PATH', 
									config_remark = '�Ҹ����ͧ��������'
								 where config_id = 59 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (59, 'FILE_PATH_DISPLAY', '$CH_FILE_PATH', '�Ҹ����ͧ��������') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 60 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'AUTOMAIL_ABSENT_FLAG',
									config_value = '$CH_AUTOMAIL_ABSENT', 
									config_remark = '����������к������'
								 where config_id = 60 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (60, 'AUTOMAIL_ABSENT_FLAG', '$CH_AUTOMAIL_ABSENT', '����������к������') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 61 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'IMG_PATH_DISPLAY',
									config_value = '$CH_IMG_PATH', 
									config_remark = '�Ҹ����ͧ������ٻ�Ҿ'
								 where config_id = 61 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (61, 'IMG_PATH_DISPLAY', '$CH_IMG_PATH', '�Ҹ����ͧ������ٻ�Ҿ') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 62 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTitle_color',
									config_value = '$xlsFmtTitle_color', 
									config_remark = '����ʴ����ѡ��� excel Ẻ Title 1'
								 where config_id = 62 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (62, 'xlsFmtTitle_color', '$xlsFmtTitle_color', '����ʴ����ѡ��� excel Ẻ Title 1') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 63 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTitle_bgcolor',
									config_value = '$xlsFmtTitle_bgcolor', 
									config_remark = '����ʴ��վ��� excel Ẻ Title 1'
								 where config_id = 63 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (63, 'xlsFmtTitle_bgcolor', '$xlsFmtTitle_bgcolor', '����ʴ��վ��� excel Ẻ Title 1') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 64 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtSubTitle_color',
									config_value = '$xlsFmtSubTitle_color', 
									config_remark = '����ʴ��յ���ѡ��� excel Ẻ Title 2'
								 where config_id = 64 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (64, 'xlsFmtSubTitle_color', '$xlsFmtSubTitle_color', '����ʴ��յ���ѡ��� excel Ẻ Title 2') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 65 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtSubTitle_bgcolor',
									config_value = '$xlsFmtSubTitle_bgcolor', 
									config_remark = '����ʴ��վ��� excel Ẻ Title 2'
								 where config_id = 65 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (65, 'xlsFmtSubTitle_bgcolor', '$xlsFmtSubTitle_bgcolor', '����ʴ��վ��� excel Ẻ Title 2') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 66 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableHeader_color',
									config_value = '$xlsFmtTableHeader_color', 
									config_remark = '����ʴ��յ���ѡ����ǵ��ҧ excel'
								 where config_id = 66 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (66, 'xlsFmtTableHeader_color', '$xlsFmtTableHeader_color', '����ʴ��յ���ѡ����ǵ��ҧ excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 67 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableHeader_bgcolor',
									config_value = '$xlsFmtTableHeader_bgcolor', 
									config_remark = '����ʴ��վ����ǵ��ҧ excel'
								 where config_id = 67 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (67, 'xlsFmtTableHeader_bgcolor', '$xlsFmtTableHeader_bgcolor', '����ʴ��վ����ǵ��ҧ excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 68 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableDetail_color',
									config_value = '$xlsFmtTableDetail_color', 
									config_remark = '����ʴ��յ���ѡ��㹵��ҧ excel'
								 where config_id = 68 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (68, 'xlsFmtTableDetail_color', '$xlsFmtTableDetail_color', '����ʴ��յ���ѡ��㹵��ҧ excel') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 69 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'xlsFmtTableDetail_bgcolor',
									config_value = '$xlsFmtTableDetail_bgcolor', 
									config_remark = '����ʴ��վ��㹵��ҧ excel'
								 where config_id = 69 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (69, 'xlsFmtTableDetail_bgcolor', '$xlsFmtTableDetail_bgcolor', '����ʴ��վ��㹵��ҧ excel') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 70 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_KP7',
									config_value = '$CH_PRINT_KP7', 
									config_remark = '����� $KP7_TITLE ��駩�Ѻ�˹�Ҩ͢����źؤ��'
								 where config_id = 70 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (70, 'PRINT_KP7', '$CH_PRINT_KP7', '����� $KP7_TITLE ��駩�Ѻ�˹�Ҩ͢����źؤ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 71 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_SCORE_DECIMAL',
									config_value = '$CH_SCORE_DECIMAL', 
									config_remark = '��ṹ�š�û����Թ��û�Ժѵ��Ҫ����շȹ���'
								 where config_id = 71 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (71, 'KPI_SCORE_DECIMAL', '$CH_SCORE_DECIMAL', '��ṹ�š�û����Թ��û�Ժѵ��Ҫ����շȹ���') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 72 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'USE_KPI_TYPE',
									config_value = '$CH_USE_KPI_TYPE', 
									config_remark = '���������Ǫ���Ѵ'
								 where config_id = 72 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (72, 'USE_KPI_TYPE', '$CH_USE_KPI_TYPE', '���������Ǫ���Ѵ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 73 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'PRINT_KP7_USER',
									config_value = '$CH_PRINT_KP7_USER', 
									config_remark = '�������ѹ�֡������㹪�ͧ�͡�����ҧ�ԧ�ͧ $KP7_TITLE ��駩�Ѻ'
								 where config_id = 72 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (73, 'PRINT_KP7_USER', 'Y', '�������ѹ�֡������㹪�ͧ�͡�����ҧ�ԧ�ͧ $KP7_TITLE ��駩�Ѻ') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 74 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SHOW_TIME_P0101',
									config_value = '$CH_SHOW_TIME_P0101', 
									config_remark = '�ʴ����ҷ�դٳ㹢����źؤ��˹���á'
								 where config_id = 74 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (74, 'SHOW_TIME_P0101', '$CH_SHOW_TIME_P0101', '�ʴ����ҷ�դٳ㹢����źؤ��˹���á') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 75 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SHOW_POSDATE_P0101',
									config_value = '$CH_SHOW_POSDATE_P0101', 
									config_remark = '�ʴ��ѹ����������дѺ��͹�Ѩ�غѹ㹢����źؤ��˹���á'
								 where config_id = 75 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (75, 'SHOW_POSDATE_P0101', '$CH_SHOW_POSDATE_P0101', '�ʴ��ѹ����������дѺ��͹�Ѩ�غѹ㹢����źؤ��˹���á') ";
		$db->send_cmd($cmd);

		$cmd = " select config_name from system_config where config_id = 76 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SALARYHIS_DISPLAY',
									config_value = '$CH_SALARYHIS_DISPLAY', 
									config_remark = '�ٻẺ˹ѧ����駼š������͹�Թ��͹'
								 where config_id = 76 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (76, 'SALARYHIS_DISPLAY', '$CH_SALARYHIS_DISPLAY', '�ٻẺ˹ѧ����駼š������͹�Թ��͹') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 77 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'KPI_SELF_EVALUATE',
									config_value = '$CH_SELF_EVALUATE', 
									config_remark = '��û����Թ���ͧ (���ö��)'
								 where config_id = 77 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (77, 'KPI_SELF_EVALUATE', '$CH_SELF_EVALUATE', '��û����Թ���ͧ (���ö��)') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 78";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'EDIT_ABSENT_DAY',
									config_value = '$CH_EDIT_ABSENT_DAY', 
									config_remark = '��䢨ӹǹ�ѹ��'
								 where config_id = 78 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (78, 'EDIT_ABSENT_DAY', '$CH_EDIT_ABSENT_DAY', '��䢨ӹǹ�ѹ��') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 79 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'CMD_DATE_DISPLAY',
									config_value = '$CH_CMD_DATE_DISPLAY', 
									config_remark = '����ʴ����ѹ��� (�ѭ��Ṻ���¤����)'
								 where config_id = 79 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (79, 'CMD_DATE_DISPLAY', '$CH_CMD_DATE_DISPLAY', '����ʴ����ѹ��� (�ѭ��Ṻ���¤����)') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 80 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_FORMAT',
									config_value = '$CH_SLIP_FORMAT', 
									config_remark = '��Ի�Թ��͹'
								 where config_id = 80 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (80, 'SLIP_FORMAT', '$CH_SLIP_FORMAT', '��Ի�Թ��͹') ";
		$db->send_cmd($cmd);
		
		$cmd = " select config_name from system_config where config_id = 81 ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data)
			$cmd = " update system_config set 
									config_name = 'SLIP_LOGO',
									config_value = '$CH_SLIP_LOGO', 
									config_remark = '���˹����麹��Ի�Թ��͹ (PDF)'
								 where config_id = 81 ";
		else
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values (81, 'SLIP_LOGO', '$CH_SLIP_LOGO', '���˹����麹��Ի�Թ��͹ (PDF)') ";
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
									config_remark = '�ʴ����������ǹ�Ҫ�������'
								 where config_name = 'ORG_SHORT_NAME' ";
                }else{
                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values ('$MAXOFCONFIG_ID', 'ORG_SHORT_NAME', '$CH_ORG_SHORT_NAME', '�ʴ����������ǹ�Ҫ�������') ";
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
									config_remark = '����ʴ��ѹ����Ẻ��ػ�š�û����Թ��û�Ժѵ��Ҫ���'
								 where config_name = 'CH_SCORE_DAY' ";
                }else{
                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
							values ('$MAXOFCONFIG_ID', 'CH_SCORE_DAY', '$CH_SCORE_DAY', '����ʴ��ѹ����Ẻ��ػ�š�û����Թ��û�Ժѵ��Ҫ���') ";
                }
		$db->send_cmd($cmd);
		if(!$ADD_PAPER_BUREAU){$ADD_PAPER_BUREAU='N';}
		$cmd = " select config_name from system_config where config_name = 'ADD_PAPER_BUREAU' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'ADD_PAPER_BUREAU',
							config_value = '$ADD_PAPER_BUREAU', 
							config_remark = '͹حҵ������� Bureau ������¡�û�С�� ���˹���á'
						 where config_name = 'ADD_PAPER_BUREAU' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'ADD_PAPER_BUREAU', '$ADD_PAPER_BUREAU', '͹حҵ������� Bureau ������¡�û�С�� ���˹���á') ";
		}
		
		$db->send_cmd($cmd);
		
		if($NUMBER_OF_DAY==""){$NUMBER_OF_DAY='23';}
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_DAY' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'NUMBER_OF_DAY',
							config_value = '$NUMBER_OF_DAY', 
							config_remark = '�ӹǹ�ѹ'
						 where config_name = 'NUMBER_OF_DAY' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_DAY', '23', '�ӹǹ�ѹ') ";
		}
		$db->send_cmd($cmd);
		
                
		if($NUMBER_OF_TIME==""){$NUMBER_OF_TIME='10';}
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_TIME' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'NUMBER_OF_TIME',
							config_value = '$NUMBER_OF_TIME', 
							config_remark = '�ӹǹ����'
						 where config_name = 'NUMBER_OF_TIME' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_TIME', '10', '�ӹǹ����') ";
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
							config_remark = '�ӹǹ���駷�����'
						 where config_name = 'NUMBER_OF_LATE' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_LATE', '18', '�ӹǹ���駷�����') ";
		}
		$db->send_cmd($cmd);
		
		
		if(!$EXTRA_ABSEN_DAY){$EXTRA_ABSEN_DAY='N';}
		$cmd = " select config_name from system_config where config_name = 'EXTRA_ABSEN_DAY' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'EXTRA_ABSEN_DAY',
							config_value = '$EXTRA_ABSEN_DAY', 
							config_remark = '���Ѻ�Է�Ծ�����ѹ������ �ѧ��Ѵ���ᴹ��'
						 where config_name = 'EXTRA_ABSEN_DAY' ";
		}
		$db->send_cmd($cmd);

                //�Դ�ʴ��ѹ���ŧ���㹻���ѵԡ�ôӧ���˹�
                if(!$CH_SHOW_POH_DATE){$CH_SHOW_POH_DATE='N';}
		$cmd = " select config_name from system_config where config_name = 'CH_SHOW_POH_DATE' ";
		$count_data = $db->send_cmd($cmd);
		if ($count_data){
		$cmd = " update system_config set 
							config_name = 'CH_SHOW_POH_DATE',
							config_value = '$CH_SHOW_POH_DATE', 
							config_remark = '�ʴ��ѹ���ŧ���㹻���ѵԡ�ô�ç���˹�'
						 where config_name = 'CH_SHOW_POH_DATE' ";
		}else{
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'CH_SHOW_POH_DATE', '$CH_SHOW_POH_DATE', '�ʴ��ѹ���ŧ���㹻���ѵԡ�ô�ç���˹�') ";
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
//									config_remark = '���¡��ѧ��ѹ������ʼ�ҹ'
//								 where config_name = 'CHANGE_PASSWORD' ";
//                }else{
//                        $MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
//			$cmd = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
//									VALUES ($MAXOFCONFIG_ID,'CHANGE_PASSWORD','N','���¡��ѧ��ѹ������ʼ�ҹ')";	
//                }
//		$db->send_cmd($cmd);
//                
//                /*Release 5.2.1.21 End*/     
//          
                
	} // end if

	include("$ROOTPATH/php_scripts/system_config.php");	

//	echo "command=$command && $username && $password<br>";
	// ��ǹ�����������Ѻ user login ��� ��˹������Ҷ֧ �ҹ�����Ũ����������
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
	// ����ǹ����Ѻ user login ��� ��˹������Ҷ֧ �ҹ�����Ũ����������

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
                
                /*��ǹ���ҡ�ӡ�� config �Դ �з�������ҡ*/
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
		if ($f_center == "N" && $dpisdb_group_level && $dpisdb_org_id) { // �óա�˹���� connect ����� user_group
			$CTRL_TYPE = $dpisdb_group_level; // ����дѺ��������� level ����˹���� user_group
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
		if ($TEMP_ORG_NAME=="ʶҺѹ�ѳ�Ե�Ѳ���Ż�"){
			if($SESS_PER_TYPE==0){		//������	
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�Ҩ����",5=>"���"); //�������ؤ��
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���",3=>"��ѡ�ҹ�Ҫ���"); 
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"����Ҫ���"); //�������ؤ��
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���"); 
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"�١��ҧ��Ш�"); //�������ؤ��
			}elseif($SESS_PER_TYPE==3){
				$PERSON_TYPE=array(3=>"��ѡ�ҹ�Ҫ���"); //�������ؤ��
				$PERSON_TYPE_KPI=array(3=>"��ѡ�ҹ�Ҫ���"); 
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"�Ҩ����"); //�������ؤ��
			}elseif($SESS_PER_TYPE==5){
				$PERSON_TYPE=array(5=>"���"); //�������ؤ��
			}else{ //����������
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�Ҩ����",5=>"���"); //�������ؤ��			
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���",3=>"��ѡ�ҹ�Ҫ���"); 
			}
			$PERSON_TYPE_USERGROUP=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�Ҩ����",5=>"���"); //�������ؤ��			
		}elseif ($TEMP_ORG_NAME=="��ا෾��ҹ��" || strstr($TEMP_ORG_NAME,"��ا෾��ҹ��")) { 
			if($SESS_PER_TYPE==0){		//������	
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"����Ҫ���"); //�������ؤ��
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"�١��ҧ��Ш�"); //�������ؤ��
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"�١��ҧ���Ǥ���"); //�������ؤ��
			}else{ //����������
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
			}
			$PERSON_TYPE_USERGROUP=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
			$PERSON_TYPE=array(1=>"����Ҫ���"); //�������ؤ��
			$PERSON_TYPE_USERGROUP=array(1=>"����Ҫ���"); //�������ؤ��
			$PERSON_TYPE_KPI=array(1=>"����Ҫ���"); 
			$SAL_PER_TYPE = 1;
//		}elseif ($CTRL_TYPE==1) { // ISCS ����͡
//			$PERSON_TYPE=array(1=>"����Ҫ���"); //�������ؤ��
//			$PERSON_TYPE_USERGROUP=array(1=>"����Ҫ���"); //�������ؤ��
		}else{
			if ($TEMP_ORG_NAME=="�Ⱥ�Ź�ù������") $EMPSER_NAME = "��ѡ�ҹ��ҧ";
			else $EMPSER_NAME = "��ѡ�ҹ�Ҫ���";
			if($SESS_PER_TYPE==0){		//������
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"$EMPSER_NAME",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
				//	$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"$EMPSER_NAME",4=>"�١��ҧ���Ǥ���",5=>"����Ҫ��ä��",6=>"����Ҫ����ʶҺѹ�ش��֡��"); //�������ؤ��
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���",3=>"$EMPSER_NAME"); 
			}elseif($SESS_PER_TYPE==1){
				$PERSON_TYPE=array(1=>"����Ҫ���"); //�������ؤ��
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���"); 
			}elseif($SESS_PER_TYPE==2){
				$PERSON_TYPE=array(2=>"�١��ҧ��Ш�"); //�������ؤ��
			}elseif($SESS_PER_TYPE==3){
				$PERSON_TYPE=array(3=>"$EMPSER_NAME"); //�������ؤ��
				$PERSON_TYPE_KPI=array(3=>"$EMPSER_NAME"); 
			}elseif($SESS_PER_TYPE==4){
				$PERSON_TYPE=array(4=>"�١��ҧ���Ǥ���"); //�������ؤ��
			}else{ //����������
				$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"$EMPSER_NAME",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
				$PERSON_TYPE_KPI=array(1=>"����Ҫ���",3=>"$EMPSER_NAME"); 
			}
			$PERSON_TYPE_USERGROUP=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"$EMPSER_NAME",4=>"�١��ҧ���Ǥ���"); //�������ؤ��
		}

		if ($TEMP_ORG_NAME=="��ا෾��ҹ��" || strstr($TEMP_ORG_NAME,"��ا෾��ҹ��")) {
			$MINISTRY_TITLE = "��ا෾��ҹ��";
			$MINISTRY_ALERT = "��س��кء�ا෾��ҹ��";
			$MINISTRY_SELECT = "���͡��ا෾��ҹ��";
			$DEPARTMENT_TITLE = "˹��§ҹ";
			$DEPARTMENT_ALERT = "��س��к�˹��§ҹ";
			$DEPARTMENT_SELECT = "���͡˹��§ҹ";
			$ORG_TITLE = "��ǹ�Ҫ���";
			$ORG_ALERT = "��س��к���ǹ�Ҫ���";
			$ORG_SELECT = "���͡��ǹ�Ҫ���";
			$ORG_TITLE1 = "����/������ҹ/�����";
			$ORG1_ALERT = "��س��кؽ���/������ҹ/�����";
			$ORG1_SELECT = "���͡����/������ҹ/�����";
			$ORG_TITLE2 = "�ҹ";
			$ORG2_ALERT = "��س��кاҹ";
			$ORG2_SELECT = "���͡�ҹ";
			$ALL_REPORT_TITLE = "������";
			$ORG_CODE_TITLE = "����˹��§ҹ/��ǹ�Ҫ���";
			$ORG_NAME_TITLE = "����˹��§ҹ/��ǹ�Ҫ���";
			$ORG_SHORT_TITLE = "�������˹��§ҹ/��ǹ�Ҫ���";
			$OL_TITLE = "�ҹТͧ˹��§ҹ/��ǹ�Ҫ���";
			$ORG_ACTIVE_TITLE = "ʶҹ�Ҿ�ͧ˹��§ҹ/��ǹ�Ҫ���";
			$PL_TITLE = "���͵��˹����§ҹ";
			$PM_TITLE = "���͵��˹觷ҧ������";
			$PT_TITLE1 = "����ú. �.�. 2528";
			$PT_TITLE2 = "����ú. �.�. 2554";
			$POS_DATE_TITLE = "�ѹ��� �.�. ��˹����˹�";
			$OFF_TYPE_TITLE = "����������Ҫ���";
			$OCCUPYDATE_TITLE = "�ѹ���������˹��§ҹ/��ǹ�Ҫ���";
			$POH_EFFECTIVEDATE_TITLE = "�ѹ�����Ҵ�ç���˹�";
			$POH_ENDDATE_TITLE = "�ѹ�������ش��ô�ç���˹�";
			$SAH_EFFECTIVEDATE_TITLE = "�ѹ����ռźѧ�Ѻ��";
			$SAH_ENDDATE_TITLE = "�֧�ѹ���";
//			$POH_EFFECTIVEDATE_TITLE = "�ѹ�����Ҵ�ç���˹�";
			$LOCAL_TITLE = "��ǹ�Ҫ���";
			$RETIRE_TITLE = "�鹨ҡ�Ҫ���";
			$LAYER_TYPE_TITLE = "�ú.2554";
			$PFR_NAME_TITLE = "���ط��";
			$AP_TITLE = "ࢵ";
			$DT_TITLE = "�ǧ";
			$KP7_TITLE = "�.�.1";
			$PT_DATE = "2011-08-15";
			$KPI_CYCLE_TITLE = "���С�û����Թ";
			$KPI_CYCLE1_TITLE = "���з�� 1";
			$KPI_CYCLE2_TITLE = "���з�� 2";
			$COM_HEAD_01 = "<**1**>���˹����˹��§ҹ";
			$COM_HEAD_02 = "<**2**>���˹����˹��§ҹ";
			$COM_HEAD_03 = "<**3**>���˹����˹��§ҹ";
			$FULLNAME_HEAD = "���� - ���ʡ��";
			$EL_CODE_BACHELOR = "40";
			$EL_NAME_BACHELOR = "��ԭ�ҵ��������º���";
			$EL_CODE_MASTER = "60";
			$EL_NAME_MASTER = "��ԭ���������º���";
			$EL_CODE_DOCTOR = "80";
			$EL_NAME_DOCTOR = "��ԭ���͡������º���";
			$SM_CODE_00 = "1";
			$SM_CODE_05 = "2";
			$SM_CODE_10 = "3";
			$SM_CODE_15 = "4";
			$SM_CODE_20 = "5";
		} else {
			if ($TEMP_ORG_NAME=="�Ⱥ�Ź�ù������") {
				$MINISTRY_TITLE = "�Ҫ��ú�������ǹ��ͧ���";
				$MINISTRY_ALERT = "��س��к��Ҫ��ú�������ǹ��ͧ���";
				$MINISTRY_SELECT = "���͡�Ҫ��ú�������ǹ��ͧ���";
				$DEPARTMENT_TITLE = "�Ⱥ��";
				$DEPARTMENT_ALERT = "��س��к��Ⱥ��";
				$DEPARTMENT_SELECT = "���͡�Ⱥ��";
				$ORG_TITLE = "�ͧ/�ӹѡ";
				$ORG_ALERT = "��س��кءͧ/�ӹѡ";
				$ORG_SELECT = "���͡�ͧ/�ӹѡ";
				$ORG_TITLE1 = "��ǹ";
				$ORG1_ALERT = "��س��к���ǹ";
				$ORG1_SELECT = "���͡��ǹ";
				$ORG_TITLE2 = "����";
				$ORG2_ALERT = "��س��кؽ���";
				$ORG2_SELECT = "���͡����";
				$ORG_TITLE3 = "������ҹ";
				$ORG3_ALERT = "��س��кء�����ҹ";
				$ORG3_SELECT = "���͡������ҹ";
				$ORG_TITLE4 = "�ҹ";
				$ORG4_ALERT = "��س��кاҹ";
				$ORG4_SELECT = "���͡�ҹ";
				$ORG_TITLE5 = "��ӡ��ҡͧ/�ӹѡ 5 �дѺ";
				$ORG5_ALERT = "��س��кص�ӡ��ҡͧ/�ӹѡ 5 �дѺ";
				$ORG5_SELECT = "���͡��ӡ��ҡͧ/�ӹѡ 5 �дѺ";
				$RPT_N = "";
			} else {
				$MINISTRY_TITLE = "��з�ǧ";
				$MINISTRY_ALERT = "��س��кء�з�ǧ";
				$MINISTRY_SELECT = "���͡��з�ǧ";
				$DEPARTMENT_TITLE = "���";
				$DEPARTMENT_ALERT = "��س��кء��";
				$DEPARTMENT_SELECT = "���͡���";
				if ($TEMP_ORG_NAME=="�����û���ͧ") 
					$ORG_TITLE = "��º����ӹѡ/�ͧ";
				else
					$ORG_TITLE = "�ӹѡ/�ͧ";
				$ORG_ALERT = "��س��к��ӹѡ/�ͧ";
				$ORG_SELECT = "���͡�ӹѡ/�ͧ";
				$ORG_TITLE1 = "��ӡ����ӹѡ/�ͧ 1 �дѺ";
				$ORG1_ALERT = "��س��кص�ӡ����ӹѡ/�ͧ 1 �дѺ";
				$ORG1_SELECT = "���͡��ӡ����ӹѡ/�ͧ 1 �дѺ";
				$ORG_TITLE2 = "��ӡ����ӹѡ/�ͧ 2 �дѺ";
				$ORG2_ALERT = "��س��кص�ӡ����ӹѡ/�ͧ 2 �дѺ";
				$ORG2_SELECT = "���͡��ӡ����ӹѡ/�ͧ 2 �дѺ";
				$ORG_TITLE3 = "��ӡ����ӹѡ/�ͧ 3 �дѺ";
				$ORG3_ALERT = "��س��кص�ӡ����ӹѡ/�ͧ 3 �дѺ";
				$ORG3_SELECT = "���͡��ӡ����ӹѡ/�ͧ 3 �дѺ";
				$ORG_TITLE4 = "��ӡ����ӹѡ/�ͧ 4 �дѺ";
				$ORG4_ALERT = "��س��кص�ӡ����ӹѡ/�ͧ 4 �дѺ";
				$ORG4_SELECT = "���͡��ӡ����ӹѡ/�ͧ 4 �дѺ";
				$ORG_TITLE5 = "��ӡ����ӹѡ/�ͧ 5 �дѺ";
				$ORG5_ALERT = "��س��кص�ӡ����ӹѡ/�ͧ 5 �дѺ";
				$ORG5_SELECT = "���͡��ӡ����ӹѡ/�ͧ 5 �дѺ";
			}
			$ALL_REPORT_TITLE = "�����ǹ�Ҫ���";
			$ORG_CODE_TITLE = "������ǹ�Ҫ���";
			$ORG_NAME_TITLE = "������ǹ�Ҫ���";
			$ORG_SHORT_TITLE = "���������ǹ�Ҫ���";
			$OL_TITLE = "�ҹТͧ��ǹ�Ҫ���";
			$ORG_ACTIVE_TITLE = "ʶҹ�Ҿ�ͧ��ǹ�Ҫ���";
			$PL_TITLE = "���˹����§ҹ";
			$PM_TITLE = "���˹�㹡�ú����çҹ";
			$PT_TITLE1 = "����ú. �.�. 2535";
			$PT_TITLE2 = "����ú. �.�. 2551";
			$POS_DATE_TITLE = "�ѹ��� �.�.�. ��˹����˹�";
			$OFF_TYPE_TITLE = "�ѧ�Ѵ�ؤ�ҡ�";
			$OCCUPYDATE_TITLE = "�ѹ��������ǹ�Ҫ���";
			$POH_EFFECTIVEDATE_TITLE = "�ѹ����ռ�";
			$POH_ENDDATE_TITLE = "�ѹ�������ش";
			$SAH_EFFECTIVEDATE_TITLE = "�ѹ����ռ�";
			$SAH_ENDDATE_TITLE = "�ѹ�������ش";
			if ($MFA_FLAG==1) $LOCAL_TITLE = "�ӹѡ/�ͧ";
			else $LOCAL_TITLE = "�����Ҥ";
			$RETIRE_TITLE = "�鹨ҡ��ǹ�Ҫ���";
			$LAYER_TYPE_TITLE = "�ú.2551/��ѡ�ҹ�Ҫ���";
			$PFR_NAME_TITLE = "��û����Թ�š�û�Ժѵ��Ҫ���";
			$AP_TITLE = "�����";
			$DT_TITLE = "�Ӻ�";
			$KP7_TITLE = "�.�.7";
			$PT_DATE = "2008-12-11";
			$KPI_CYCLE_TITLE = "�ͺ��û����Թ";
			$KPI_CYCLE1_TITLE = "���駷�� 1";
			$KPI_CYCLE2_TITLE = "���駷�� 2";
			$COM_HEAD_01 = "<**1**>���˹������ǹ�Ҫ���";
			$COM_HEAD_02 = "<**2**>���˹������ǹ�Ҫ���";
			$COM_HEAD_03 = "<**3**>���˹������ǹ�Ҫ���";
			$FULLNAME_HEAD = "����-���ʡ��";
			$EL_CODE_BACHELOR = "40";
			$EL_NAME_BACHELOR = "��ԭ�ҵ��������º���";
			$EL_CODE_MASTER = "60";
			$EL_NAME_MASTER = "��ԭ���������º���";
			$EL_CODE_DOCTOR = "80";
			$EL_NAME_DOCTOR = "��ԭ���͡������º���";
			$SM_CODE_00 = "10";
			$SM_CODE_05 = "1";
			$SM_CODE_10 = "2";
			$SM_CODE_15 = "3";
			$SM_CODE_20 = "4";
		}

		$ORG_BKK_TITLE  = "��������";
		if ($ISCS_FLAG==1) $LAYER_TYPE_TITLE = "�ú.2551";
		$OT_TITLE = "�ѧ�Ѵ";
		$ORG_ADDR1_TITLE = "ʶҹ�����";
		$ORG_JOB_TITLE = "˹�ҷ������Ѻ�Դ�ͺ";
		$POS_SALARY_TITLE = "�ѵ���Թ��͹��ͨ���";
		$POS_SITUATION_TITLE = "ʶҹ�Ҿ�ͧ���˹�";
		$POS_GET_DATE_TITLE = "�ѹ�����˹����Թ";
		$POS_CHANGE_DATE_TITLE = "�ѹ�����˹�����¹ʶҹ�Ҿ";
		$POS_VACANT_DATE_TITLE = "�ѹ�����˹���ҧ";
		$POS_STATUS_TITLE = "ʶҹТͧ���˹�";
		$STARTDATE_TITLE = "�ѹ�������Ѻ�Ҫ���";
		$BL_TITLE = "�������Ե";
		$PV_PER_TITLE = "�����������";
		$CT_TITLE = "�����";
		$PV_TITLE = "�ѧ��Ѵ";
		$POS_NO_TITLE = "�Ţ�����˹�";
		$PAY_NO_TITLE = "�Ţ��ͨ���";
		$SEQ_NO_TITLE = "�ӴѺ���";
		$PT_TITLE = "���������˹�";
		$LEVEL_TITLE = "�дѺ���˹�";
		$PER_LEVEL_TITLE = "�дѺ�ͧ����ç���˹�";
		$MOV_TITLE = "�������������͹���";
		$REMARK_TITLE = "�����˵�";
		$SALARY_TITLE = "�ѵ���Թ��͹";
		$MGTSALARY_TITLE = "�Թ��Шӵ��˹�";
		$DOCNO_TITLE = "�Ţ�������";
		$DOCDATE_TITLE = "ŧ�ѹ���";
		$UPDATE_USER_TITLE = "�����";
		$UPDATE_DATE_TITLE = "�ѹ������";
		$TRN_STARTDATE_TITLE = "�ѹ����������";
		$TRN_ENDDATE_TITLE = "�ѹ�������ش";
		$TRN_PROJECT_NAME_TITLE = "�ç��ý֡ͺ��";
		$COM_NO_TITLE = "������Ţ���";
		$COM_DATE_TITLE = "ŧ�ѹ���";
		$COM_NAME_TITLE = "����ͧ";
		$COM_TYPE_TITLE = "�����������";
		$COM_NOTE_TITLE = "�����˵ط��¤����";
		$COM_DETAIL_TITLE = "��������´�ͧ�����";
//		$COM_ORDER_TITLE = "�ѭ��Ṻ���� ���§���";
		$COM_ORDER_TITLE = "���§���";
		$COM_ORDER2_TITLE = "�ѧ�Ѵ, �Ţ�����˹�";
		$COM_DEL_TITLE = "ź�ѭ��";
		$COM_ADD_TITLE = "�����ѭ��";
		$COM_EDIT_TITLE = "��䢺ѭ��";
		$COM_CONFIRM_TITLE = "�׹�ѹ�����";
		$COM_SEND_TITLE = "�觤����";
		$COM_SEARCH_TITLE = "���Һѭ��";
		$DEL_PERSON_TITLE = "ź��¡�úؤ��";
		$EDIT_PERSON_TITLE = "�����¡�úؤ��";
		$ADD_PERSON_TITLE = "������¡�úؤ��";
		$SELECT_PERSON_TITLE = "���͡��¡�úؤ��";
		$FULLNAME_TITLE = "����-ʡ��";
		$PER_TYPE_TITLE = "�������ؤ�ҡ�";
		$PRENAME_TITLE = "�ӹ�˹�Ҫ���";
		$NAME_TITLE = "����";
		$SHORTNAME_TITLE = "�������";
		$OTHERNAME_TITLE = "�������� �ٻẺ ||xx||yy||";
		$SURNAME_TITLE = "���ʡ��";
		$NICKNAME_TITLE = "�������";
		$CARDNO_TITLE = "�Ţ��Шӵ�ǻ�ЪҪ�";
		$OFFNO_TITLE = "�Ţ��Шӵ�Ǣ���Ҫ���";
		$TAXNO_TITLE = "�Ţ��Шӵ�Ǽ�����������ҡ�";
		$SEX_TITLE = "��";
		$RE_TITLE = "��ʹ�";
		$MR_TITLE = "ʶҹ�Ҿ����";
		$FATHER_TITLE = "�Դ�";
		$MOTHER_TITLE = "��ô�";
		$EMAIL_TITLE = "������";
		$BANK_ACCOUNT_TITLE = "�Ţ���ѭ�ո�Ҥ��";
		$CERT_OCC_TITLE = "�Ţ���㺻�Сͺ�ԪҪվ";
		$DEH_RECEIVE_DATE_TITLE = "�ѹ����Ҫ�Ԩ�ҹ�ມ��";
		$DEH_DATE_TITLE = "�ѹ������Ѻ����ͧ�Ҫ�";
		$DEH_RETURN_DATE_TITLE = "�ѹ����觤׹����ͧ�Ҫ�";

		$PL_NAME_WORK_TITLE = "���˹� ($KP7_TITLE)";
		$ORG_NAME_WORK_TITLE = "��ǹ�Ҫ��� ($KP7_TITLE)";
		$BIRTHDATE_TITLE = "�ѹ��͹���Դ";
		$CMD_EDUCATE_TITLE = "�زԷ����㹵��˹�";
		$CMD_DATE_TITLE = "�ѹ����觵��";
		$OLD_POSITION_TITLE = "���˹����";
		$CMD_POSITION_TITLE = "���˹�";
		$NEW_POSITION_TITLE = "���˹觷���觵��";
		$PL_ASSIGN_TITLE = "�ͺ������黯Ժѵԧҹ";
		$FROM_DATE_TITLE = "������ѹ���";
		$TO_DATE_TITLE = "�֧�ѹ���";
		$SPSALARY_TITLE = "�Թ�ͺ᷹�����";
		$ES_TITLE = "ʶҹС�ô�ç���˹�";
		$EL_TITLE = "�дѺ����֡��";
		$EN_TITLE = "�زԡ���֡��";
		$EM_TITLE = "�Ң��Ԫ��͡";
		$INS_TITLE = "ʶҺѹ����֡��";
		$CT_EDU_TITLE = "����ȷ������稡���֡��";
		$PER_STATUS_TITLE = "ʶҹ�Ҿ";
		$CL_TITLE = "��ǧ�дѺ���˹�";
		$SG_TITLE = "��ҹ��������Ǫҭ";
		$SKILL_TITLE = "�ҢҤ�������Ǫҭ";
		$PC_TITLE = "���͹䢵��˹�";
		$FILE_NO_TITLE = "�Ţ������";
		$COOPERATIVE_NO_TITLE = "�Ţ����¹��Ҫԡ�ˡó�";
		$REPORT_CONDITION = "���͹䢡���͡��§ҹ";
		$REPORT_FORMAT = "�ٻẺ����͡��§ҹ";
		$REPORT_ORDER = "��ṡ�����ŵ��";
		$LAW_STRUCTURE_TITLE = "�ç���ҧ���������";
		$ASSIGN_STRUCTURE_TITLE = "�ç���ҧ����ͺ���§ҹ";
		$ORG_TYPE1_TITLE = "��ǹ��ҧ";
		$ORG_TYPE2_TITLE = "��ǹ��ҧ������Ҥ";
		$ORG_TYPE3_TITLE = "��ǹ�����Ҥ";
		$ORG_TYPE4_TITLE = "��ҧ�����";
		$POSITION_TITLE = "���˹觢���Ҫ���";
		$POS_EMP_TITLE = "���˹��١��ҧ��Ш�";
		$POS_EMPSER_TITLE = "���˹觾�ѡ�ҹ�Ҫ���";
		$POS_TEMP_TITLE = "���˹��١��ҧ���Ǥ���";
		$PERSON_TITLE = "�ؤ�ҡ�";
		$BOOK_NO_TITLE = "�Ţ���˹ѧ��͹���";
		$PER_JOB_TITLE = "˹�ҷ������Ѻ�Դ�ͺ";
		$EXPORT_TITLE = "�����͹������";
		$IMPORT_TITLE = "����Ң�����";
		$ACTIVE_TITLE = "��ҹ/¡��ԡ";
		$CGD_TITLE = "���ʡ���ѭ�ա�ҧ";
		$COMPETENCE_TITLE = "���ö��";
		$KPI_TYPE_TITLE = "��������Ǫ���Ѵ";
		$KPI_SUCCESS_TITLE = "�дѺ������稵�Ǫ���Ѵ";
		$KPI_LEVEL_TITLE = "�дѺ��Ǫ���Ѵ";
		$KPI_DEFINE_TITLE = "�ӹ��������Ըա�äӹǳ";
		$PJ_TYPE_TITLE = "�������ç���";
		$PJ_CLASS_TITLE = "��Դ�ͧ�ç���";
		$PJ_STATUS_TITLE = "ʶҹ�Ҿ�ͧ�ç���";
		$PJ_IMPORTANCE_TITLE = "�дѺ�����Ӥѭ";
		$PJ_OBJECTIVE_TITLE = "�ѵ�ػ��ʧ��ͧ�ç���";
		$PJ_TARGET_TITLE = "������¢ͧ�ç���";
		$START_DATE_TITLE = "�ѹ����������";
		$END_DATE_TITLE = "�ѹ�������ش";
		$COM_TITLE = "�ѭ��Ṻ���¤����";
		$PJ_NAME_TITLE = "�����ç���";
		$KPI_NAME_TITLE = "���͵�Ǫ���Ѵ";
		$REPORT_FORMAT_TITLE = "�ٻẺ��§ҹ";
		$YEAR_TITLE = "�է�����ҳ";
		$TENA_TITLE = "���.";
		$CA_TEST_DATE_TITLE = "�ѹ�������Ѻ��û����Թ";
		$CA_APPROVE_DATE_TITLE = "�ѹ����鹺ѭ��";
		$COMTYPE1_1_TITLE = "��èؼ���ͺ�觢ѹ��";
		$COMTYPE2_1_TITLE = "���¢���Ҫ���";
		$COMTYPE2_6_TITLE = "�Ѵ��ŧ";
		$COMTYPE3_5_TITLE = "����͹����Ҫ���";
		$COMTYPE4_1_TITLE = "����͹����Ҫ���";
		$COMTYPE6_TITLE = "������Ҫ������Ѻ�Թ��͹����س�ز�";
		$COMTYPE9_TITLE = "������Ҫ����ѡ���Ҫ���᷹";
		$COMTYPE7_TITLE = "����͹�Թ��͹";
		$COMTYPE7_1_TITLE = "����͹�Թ��͹����Ҫ���";
		$COMTYPE12_TITLE = "��䢤���觷��Դ��Ҵ";
		$COMTYPE13_TITLE = "¡��ԡ��������";
		$COMTYPE21_1_TITLE = "�ͺ������黯Ժѵ��Ҫ���";
		$COMTYPE22_TITLE = "�����Ҫ���";
		$COMTYPE23_TITLE = "�Ѵ�͹�ѵ���Թ��͹";
		$COMTYPE24_TITLE = "��Ѻ�ѵ���Թ��͹";
		$COMTYPE241_TITLE = "������Ҫ������Ѻ�Թ��͹����ѭ���Թ��͹��鹵�Ӣ���٧������Ѻ��û�Ѻ����";
		$COMTYPE25_TITLE = "�����Թ�ҧ��Ż�Шӻ�";
		$COMTYPE28_TITLE = "�Ѵ�͹���˹�����ѵ���Թ��͹";
		$SM_TITLE = "�ӹǹ����Թ��͹";
		$EX_TITLE = "�������Թ���������";
		if ($BKK_FLAG==1) { 
			$SORT_TITLE = '&nbsp;&nbsp;<font color="#FF0000"><B>!</B></font> <font color="#000000">����ö���§�ӴѺ��</font>, &nbsp;<img src="images/b_arrow_down.png" border="0">&nbsp;���§�ҡ�ҡ仹��� , &nbsp;<img src="images/b_arrow_up.png" border="0">&nbsp;���§�ҡ������ҡ';
			$SORT_CUR="<font SIZE='3' color='#FF0000'><b>!</b></font>";
			$SORT_ASC="<img src='images/b_arrow_up.png' border='0'>";
			$SORT_DESC="<img src='images/b_arrow_down.png' border='0'>";
		} else {
			$SORT_TITLE = '&nbsp;&nbsp;<font color="#FF0000"><B>*</B></font> <font color="#000000">����ö���§�ӴѺ��</font>, &nbsp;<img src="images/b_arrow_down.png" border="0">&nbsp;���§�ҡ�ҡ仹��� , &nbsp;<img src="images/b_arrow_up.png" border="0">&nbsp;���§�ҡ������ҡ';
			$SORT_CUR="<font SIZE='3' color='#FF0000'><b>*</b></font>";
			$SORT_ASC="<img src='images/b_arrow_up.png' border='0'>";
			$SORT_DESC="<img src='images/b_arrow_down.png' border='0'>";
		}
		$SHOW_GRAPH_TITLE = "�ʴ���ҿ";
		$CLOSE_WINDOW_TITLE = "�Դ˹�ҵ�ҧ";
		$SHOW_ALL_TITLE = "�ʴ�������";
		$EXCEL_TITLE = "���͡��� Excel";
		$PDF_TITLE = "����§ҹ�ٻẺ PDF";
		$RTF_TITLE = "����§ҹ�ٻẺ RTF";
//		if ($BKK_FLAG==1)
//			$CLEAR_TITLE = "������";
//		else
			$CLEAR_TITLE = "��ҧ˹�Ҩ�";
		$SELECT_TITLE = "���͡";
		$CANCEL_TITLE = "¡��ԡ";
		$SEARCH_TITLE = "���Ң�����";
		$ADD_TITLE = "����������";
		$ADDTAB_TITLE = "��ԡ��������";
		$EDIT_TITLE = "���";/*���*/
                $BNT_EDIT_TITLE = "�ѹ�֡";/*Release 5.1.0.8 */
		$DEL_TITLE = "ź";
		$INQ_TITLE = "���¡��";
		$PRINT_TITLE = "�����";
		$CONFIRM_TITLE = "�׹�ѹ";
		$LOAD_TITLE = "Ṻ���";
		$ALT_LOAD_TITLE = "Ṻ��������";
		$AUDIT_TITLE = "��Ǩ�ͺ";
		$AUDITED_TITLE = "��Ǩ����";
		$DETAIL_TITLE = "��������´";
		$INQUIRE_TITLE = "�ͺ���";
		$SAVE_SEARCH_TITLE = "�ѹ�֡�š�ä���";
		$REORDER_TITLE = "�Ѵ�ӴѺ";
		$SETFLAG_TITLE = "�ѹ�֡";
		$SAVE_TITLE = "�ѹ�֡";
		$CODE_TITLE = "����";
		$CARDNO_PRINT_TITLE = "�Ţ���ѵ������ҹ��ҧ����";
		$WEIGHT_PROBATION = 50;
		if ($MUNICIPALITY_FLAG==1) 
			$LIST_LEVEL_NO="('01','02','03','04','05','06','07','08','09','10','11')";
		elseif ($NOT_LEVEL_NO_O4=="Y") 
			$LIST_LEVEL_NO="('O1','O2','O3','K1','K2','K3','K4','K5','D1','D2','M1','M2')";
		else
			$LIST_LEVEL_NO="('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2')";
		if ($ISCS_FLAG==1) $LIST_LEVEL_NO="('O4','K4','K5','D1','D2','M1','M2')";
		$ARR_OCCUPATION_GROUP=array(1=>"������Ҫվ������ �ӹ�¡�� ��á�� �ҹʶԵ� �ҹ�Եԡ�� �ҹ��ñٵ��е�ҧ�����",2=>"������Ҫվ��ä�ѧ ������ɰ�Ԩ ��þҳԪ������ص��ˡ���",3=>"������Ҫվ���Ҥ� ���� ��еԴ����������",4=>"������Ҫվ�ɵá���",5=>"������Ҫվ�Է����ʵ��",6=>"������Ҫվᾷ�� ��Һ�� ����Ҹ�ó�آ",7=>"������Ҫվ���ǡ��� ʶһѵ¡��� ��Ъ�ҧ෤�Ԥ��ҧ�",8=>"������Ҫվ����֡�� ��Ż �ѧ�� ��С�þѲ�Ҫ����"); //������Ҫվ
		$max_execution_time = 30000;
		$maxsize_up_file_label = "<span class='label_alert'>��Ҵ�ͧ���Ṻ����Թ ".$maxsize_up_file." MB</span>";
		$ARR_PFR_TYPE=array(0=>"", 1=>"�ط���ʵ��",2=>"������ط���ʵ��",3=>"���ط��"); //��������û����Թ��
		$ARR_KPI_FORM_TYPE=array(1=>"��Ǫ���Ѵ���Ἱ��Ժѵ��Ҫ��û�Шӻ�",2=>"��Ǫ���Ѵ���˹�ҷ������Ѻ�Դ�ͺ��ѡ",3=>"��Ǫ���Ѵ����ҹ������Ѻ�ͺ���¾����"); //��������Ǫ���Ѵ
		$ARR_KPI_TYPE=array(1=>"��Ǫ���Ѵ�ԧ�ط���ʵ����Ἱ��Ժѵ��Ҫ��âͧ˹��§ҹ",2=>"��Ǫ���Ѵ�ҹ��Шӵ��Ἱ��Ժѵ��Ҫ��âͧ˹��§ҹ"); //��������Ǫ���Ѵ
		$ARR_KPI_SUCCESS_LEVEL=array(1=>"��Ǫ���Ѵ�дѺ�ż�Ե (Output)",2=>"��Ǫ���Ѵ�дѺ���Ѿ�� (Outcome)"); //�дѺ������稵�Ǫ���Ѵ
		$ARR_KPI_LEVEL=array(1=>"��Ǫ���Ѵ�дѺ������ط���ʵ��",2=>"��Ǫ���Ѵ�дѺ���ط��"); //�дѺ��Ǫ���Ѵ
		$ARR_PROJECT_IMPORTANCE=array(1=>"�ç��õ��Ἱ��Ժѵ��Ҫ���",2=>"�ç���/�Ԩ����������Ѻ�ͺ�����������",3=>"�ç��÷���������ʹ��繾����",4=>"�ç��õ����º�¼������� ���.",5=>"�ç��õ����º�¢ͧ �.��Ҵ��",6=>"�ç��õ����º���Ѱ���"); //�дѺ�����Ӥѭ�ͧ�ç���
		$ARR_PROJECT_CLASS=array(1=>"�ç���/�Ԩ����������",2=>"�ç��õ�����ͧ�է�����ҳ"); //�������ç���
		$ARR_PROJECT_TYPE=array(1=>"�ç��õ��Ἱ",2=>"�ç��ù͡Ἱ"); //��Դ�ͧ�ç���
		$ARR_PROJECT_STATUS=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�١��ҧ���Ǥ���"); //ʶҹ�Ҿ�ͧ�ç���
		$ARR_PROJECT_TYPE_USERGROUP=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�١��ҧ���Ǥ���"); //�дѺ�����Ӥѭ�ͧ�ç���
		$ARR_PRINT_FONT=array(1=>"Angsana",2=>"Cordia",3=>"TH Sarabun",4=>"Browallia"); //�ٻẺ�ѡ��㹡�þ������§ҹ

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
//		$TEMP_ORG_NAME = "�����û���ͧ";
	} // end if ($DPISDB)

/*http://dpis.ocsc.go.th/Service/node/1152*/
$cmd = " select config_name from system_config where config_id = 56 and config_value =1 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
$cmd = " update system_config set 
        config_name = 'KPI_SCORE_CONFIRM',
        config_value = 1, 
        config_remark = '��Ǩ�ͺ�������ṹ�š�û����Թ'
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
            echo"<script>alert('���ʼ�ҹ�ͧ��ҹ�Ҵ������� ���ͤ�����ʹ��¡�س�����¹���ʼ�ҹ���� \\n !!!  Username ��� Password ������繤�����ǡѹ !!!');</script>";
        }else{
            echo"<script>alert('���ʼ�ҹ�ͧ��ҹ�Ҵ������� ���ͤ�����ʹ��¡�س�����¹���ʼ�ҹ���� \\n !!! ��������ѹ��͹���Դ !!!');</script>";
        }
        echo"<script>window.location='user_profile.html?MENU_ID_LV0=12&MENU_ID_LV1=0';</script>";
    }
}*/
?>