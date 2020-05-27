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
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (15, 'ATTDB', '$CH_ATTDB', '�������ҹ����������ͧ�ѹ�֡����') ";
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
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) values (21, 'DPIS35DB', '$CH_DPIS35DB', '�������ҹ�����ŷ���ͧ��ö����͹') ";
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
	} // end if
	
	if($command == "UPDATEDPIS35DB"){
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
	} // end if
	
	if($command == "UPDATESYSTEMPARAMETER"){
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (10, 'data_per_page', '$ch_data_per_page', '�ӹǹ�ä��촵��˹��') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (11, 'WEIGHT_KPI', '$CH_WEIGHT_KPI', '% �š�û����Թ�ͧ����Ҫ��� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (12, 'WEIGHT_COMPETENCE', '$CH_WEIGHT_COMPETENCE', '% �š�û����Թ�ͧ����Ҫ��� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (13, 'WEIGHT_OTHER', '$CH_WEIGHT_OTHER', '% �š�û����Թ�ͧ����Ҫ��� - ��� �') ";
		$db->send_cmd($cmd);
/*
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (13, 'BACKUP_PATH', '$CH_BACKUP_PATH', '���Ұҹ������ / ���¡�׹������') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (14, 'TRANSFER_PATH', '$CH_TRANSFER_PATH', '�����͹ / �Ѻ�͹ ��������ºؤ��') ";
		$db->send_cmd($cmd);
*/		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (20, 'RPT_N', '$CH_RPT_N', '����Ҫ�ѭ�ѵ�����º����Ҫ��þ����͹ �.�. 2551') ";
		$db->send_cmd($cmd);
		
//		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
//						values (26, 'SA_SU_ABSENT', '$CH_SA_SU_ABSENT', '�������/��� ��йѺ�ѹ������� �ҷԵ��') ";
//		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (14, 'COMPETENCY_SCALE', '$CH_COMPETENCY_SCALE', '�ҵ��Ѵ��û����Թ���ö��') ";
		$db->send_cmd($cmd);
		
		//�ٻẺ�Ţ��Шӵ�ǻ�ЪҪ�
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (29, 'CARD_NO_DISPLAY', '$CH_CARD_NO_DISPLAY', '����ʴ����Ţ��Шӵ�ǻ�ЪҪ�') ";
		$db->send_cmd($cmd);
		
		//�ٻẺ������
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (30, 'BUTTON_DISPLAY', '$CH_BUTTON_DISPLAY', '����ʴ��Ż�����') ";
		$db->send_cmd($cmd);
		
		//�ٻẺ������§�����źؤ��
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (31, 'PER_ORDER_BY', '$CH_ORDER_BY', '������§�����źؤ��') ";
		$db->send_cmd($cmd);
		
		//��ͨ���
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (32, 'PAYMENT_FLAG', '$CH_PAYMENT_FLAG', '��ͨ���') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (33, 'WEIGHT_KPI_E', '$CH_WEIGHT_KPI_E', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (34, 'WEIGHT_COMPETENCE_E', '$CH_WEIGHT_COMPETENCE_E', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��÷���� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (35, 'WEIGHT_KPI_S', '$CH_WEIGHT_KPI_S', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ������稢ͧ�ҹ') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (36, 'WEIGHT_COMPETENCE_S', '$CH_WEIGHT_COMPETENCE_S', '% �š�û����Թ�ͧ��ѡ�ҹ�Ҫ��þ���� - ���ö��') ";
		$db->send_cmd($cmd);

		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (37, 'PRINT_FONT', '$CH_PRINT_FONT', '�ٻẺ�ѡ��㹡�þ������§ҹ') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (38, 'ATTACH_FILE', '$CH_ATTACH_FILE', '��èѴ�����Ṻ') ";
		$db->send_cmd($cmd);
		
		$cmd = " replace into system_config (config_id, config_name, config_value, config_remark) 
						values (39, 'EMPSER_SCORE_METHOD', '$CH_EMPSER_SCORE_METHOD', '��äӹǳ��ṹ�š�û�Ժѵ��Ҫ���') ";
		$db->send_cmd($cmd);
		
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
	// ����ǹ����Ѻ user login ��� ��˹������Ҷ֧ �ҹ�����Ũ����������
	
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
	if ($f_center == "N" && $dpisdb_group_level && $dpisdb_org_id) { // �óա�˹���� connect ����� user_group
		$CTRL_TYPE = $dpisdb_group_level; // ����дѺ��������� level ����˹���� user_group
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

	if ($TEMP_ORG_NAME=="ʶҺѹ�ѳ�Ե�Ѳ���Ż�") 
		$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���",4=>"�Ҩ����",5=>"���"); //�������ؤ��
	else
		$PERSON_TYPE=array(1=>"����Ҫ���",2=>"�١��ҧ��Ш�",3=>"��ѡ�ҹ�Ҫ���"); //�������ؤ��

	$MINISTRY_TITLE = "��з�ǧ";
	$DEPARTMENT_TITLE = "���";
	$ORG_TITLE = "��º����ӹѡ/�ͧ";
//	$ORG_TITLE = "�ӹѡ/�ͧ";
	$ORG_TITLE1 = "��ӡ����ӹѡ/�ͧ 1 �дѺ";
	$ORG_TITLE2 = "��ӡ����ӹѡ/�ͧ 2 �дѺ";
	$ORG_TITLE3 = "��ӡ����ӹѡ/�ͧ 3 �дѺ";
	$ORG_TITLE4 = "��ӡ����ӹѡ/�ͧ 4 �дѺ";
	$ORG_TITLE5 = "��ӡ����ӹѡ/�ͧ 5 �дѺ";
	$PL_TITLE = "���˹����§ҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$CT_TITLE = "�����";
	$PV_TITLE = "�ѧ��Ѵ";
	$AP_TITLE = "�����";
	$POS_NO_TITLE = "�Ţ�����˹�";
	$PAY_NO_TITLE = "�Ţ��ͨ���";
	$SEQ_NO_TITLE = "�ӴѺ���";
	$PT_TITLE = "���������˹�";
	$LEVEL_TITLE = "�дѺ���˹�";
	$PER_LEVEL_TITLE = "�дѺ�ؤ��";
	$MOV_TITLE = "�������������͹���";
	$REMARK_TITLE = "�����˵�";
	$SALARY_TITLE = "�ѵ���Թ��͹";
	$MGTSALARY_TITLE = "�Թ��Шӵ��˹�";
	$DOCNO_TITLE = "�Ţ�������";
	$DOCDATE_TITLE = "ŧ�ѹ���";
	$UPDATE_USER_TITLE = "�����";
	$UPDATE_DATE_TITLE = "�ѹ������";
	$POH_EFFECTIVEDATE_TITLE = "�ѹ����ռ�";
	$POH_ENDDATE_TITLE = "�ѹ�������ش";
	$SAH_EFFECTIVEDATE_TITLE = "�ѹ����ռ�";
	$SAH_ENDDATE_TITLE = "�ѹ�������ش";
	$TRN_STARTDATE_TITLE = "�ѹ��������";
	$TRN_ENDDATE_TITLE = "�ѹ�������ش";
	$TRN_PROJECT_NAME_TITLE = "�ç��ý֡ͺ��";
	$COM_NO_TITLE = "������Ţ���";
	$COM_DATE_TITLE = "ŧ�ѹ���";
	$COM_NAME_TITLE = "����ͧ";
	$COM_TYPE_TITLE = "�����������";
	$COM_NOTE_TITLE = "�����˵ط��¤����";
	$COM_DETAIL_TITLE = "��������´�ͧ�����";
	$COM_ORDER_TITLE = "�ѭ��Ṻ���� ���§���";
	$COM_ORDER2_TITLE = "�ѧ�Ѵ, �Ţ�����˹�";
	$COM_DEL_TITLE = "ź�ѭ��";
	$COM_ADD_TITLE = "�����ѭ��";
	$COM_EDIT_TITLE = "��䢺ѭ��";
	$COM_CONFIRM_TITLE = "�׹�ѹ�����";
	$COM_SEND_TITLE = "�觤����";
	$COM_SEARCH_TITLE = "���Һѭ��Ṻ���¤����";
	$ADD_PERSON_TITLE = "������¡�úؤ��";
	$SELECT_PERSON_TITLE = "���͡��¡�úؤ��";
	$FULLNAME_TITLE = "����-ʡ��";
	$PER_TYPE_TITLE = "�������ؤ�ҡ�";
	$NAME_TITLE = "����";
	$SURNAME_TITLE = "���ʡ��";
	$CARDNO_TITLE = "�Ţ��Шӵ�ǻ�ЪҪ�";
	$OFFNO_TITLE = "�Ţ��Шӵ�Ǣ���Ҫ���";
	$SEX_TITLE = "��";
	$DEH_RECEIVE_DATE_TITLE = "�ѹ����Ҫ�Ԩ�ҹ�ມ��";
	$DEH_DATE_TITLE = "�ѹ��͹�� ����Ѻ";

	$PL_NAME_WORK_TITLE = "���˹� (�.�.7)";
	$ORG_NAME_WORK_TITLE = "��ǹ�Ҫ��� (�.�.7)";
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
	$INS_TITLE = "ʶҹ�֡��";
	$PER_STATUS_TITLE = "ʶҹ�Ҿ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$PM_TITLE = "���˹�㹡�ú����çҹ";
	$CLOSE_WINDOW_TITLE = "�Դ˹�ҵ�ҧ";
	$SHOW_ALL_TITLE = "�ʴ�������";
	$EXCEL_TITLE = "���͡��� Excel";
	$PDF_TITLE = "����§ҹ�ٻẺ PDF";
	$CLEAR_TITLE = "������";
	$SELECT_TITLE = "���͡";
	$CANCEL_TITLE = "¡��ԡ";
	$SEARCH_TITLE = "���Ң�����";
	$ADD_TITLE = "����������";
	$EDIT_TITLE = "���";
	$DEL_TITLE = "ź";
	$INQ_TITLE = "���¡��";
	$PRINT_TITLE = "�����";
	$CONFIRM_TITLE = "�׹�ѹ";
	$LOAD_TITLE = "Ṻ���";
	$ALT_LOAD_TITLE = "Ṻ��������";
	$DETAIL_TITLE = "��������´";
	$INQUIRE_TITLE = "�ͺ���";
	$SAVE_SEARCH_TITLE = "�ѹ�֡�š�ä���";
	$REORDER_TITLE = "�Ѵ�ӴѺ";
	$SETFLAG_TITLE = "��駤��";

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
//	$TEMP_ORG_NAME = "�����û���ͧ";
?>