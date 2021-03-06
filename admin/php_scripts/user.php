<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	/*cdgs*/
	include("php_scripts/function_gen_user.php");
	/*cdgs*/
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$group_id ";
	$db->send_cmd($cmd);
//	$db->show_error(); 
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$group_code = $data[code];
	$group_level = $data[group_level];
	//$msg_chk=0;
	switch($group_level){
		case 2 :
			$PROVINCE_CODE = $data[pv_code];
			break;
		case 3 :
			$MINISTRY_ID = $data[org_id];
			break;
		case 4 :
			$DEPARTMENT_ID = $data[org_id];
			break;
		case 5 :
			$ORG_ID = $data[org_id];
			break;
		case 6 :
			$ORG_ID_1 = $data[org_id];
			break;
	} // end switch case

//	echo "$group_level :: $PROVINCE_CODE :: $MINISTRY_ID :: $DEPARTMENT_ID :: $ORG_ID";
	$multi_id_n = ""; 
	$num_ch="";	
	if($ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME_1 = $data[ORG_NAME];
		$ORG_ID = $data[ORG_ID_REF];	
	} // end if

	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROVINCE_NAME = $data[PV_NAME];
	} // end if

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$district_id += 0;		$amphur_id += 0;		$province_id += 0;
	if(!$select_group_id) $select_group_id = $group_id;
	$user_link_id += 0;

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
	
	$inherit_group = "";
	if(count($list_inherit_group)) $inherit_group = implode(",", $list_inherit_group);
	
	if( $command=="LOADPOS" ){
		ini_set("max_execution_time", $max_execution_time);

		if($BKK_FLAG==1) {
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02' or OL_CODE='03') and ORG_CODE is not NULL
								 order by		ORG_CODE ";		
		} elseif ($group_level==1) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02') and ORG_CODE is not NULL and length(trim(ORG_CODE))=5
								 order by		ORG_CODE ";		
		} elseif ($group_level==2) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02') and ORG_CODE is not NULL and length(trim(ORG_CODE))=5
								 order by		ORG_CODE ";		
		} elseif ($group_level==3) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and ORG_ACTIVE=1 and OL_CODE='02') and ORG_ACTIVE=1 and (OL_CODE='02' or OL_CODE='03') 
								 order by		ORG_CODE ";		
		} elseif ($group_level==4) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ACTIVE=1 and OL_CODE='03' 
								 order by		ORG_CODE ";		
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);
			if ($DOH_FLAG == 1 && trim($data[ORG_SHORT])) $ORG_CODE = trim($data[ORG_SHORT]);
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE=="01") $GROUP_LEVEL = 3;
			elseif ($OL_CODE=="02") $GROUP_LEVEL = 4;
			elseif ($OL_CODE=="03") $GROUP_LEVEL = 5;

			$cmd = " delete from user_detail where fullname = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		
			$cmd = " select id from user_group where name_th = '$ORG_NAME' ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data) {
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_id = $data[id];

				$cmd = " delete from user_privilege where group_id = $old_id ";
				$db->send_cmd($cmd);
		
				$cmd = " delete from user_group where id = $old_id ";
				$db->send_cmd($cmd);
			}
		
			$cmd = " select max(id) as max_id from user_group ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$group_id = $data[max_id] + 1;
			$cmd = " insert into user_group (id, code, name_th, name_en, access_list, group_level, org_id, group_per_type, group_org_structure, group_active, 
							  create_date, create_by, update_date, update_by) 
							  values ($group_id, '$ORG_CODE', '$ORG_NAME', '$ORG_NAME', ',1,', $GROUP_LEVEL, $ORG_ID, 0, 2, 1 , 
							  $update_date, $update_by, $update_date, $update_by)";
			$db->send_cmd($cmd);
//		$db->show_error();

			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			
			if (substr($ORG_CODE,2,3)=="000")
				$username = 'M'.substr($ORG_CODE,0,2).'01';
			else
				$username = 'M'.substr($ORG_CODE,0,2).'D'.substr($ORG_CODE,3,2).'01';
			$cmd = " select id, group_id from user_detail where username='$username' ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data) {
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_id = $data[id];
				$old_group_id = $data[group_id];
				$cmd = " update user_detail set group_id = $group_id where id = $old_id ";
				$db->send_cmd($cmd);
	//		$db->show_error();

				$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, 
								 can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, can_attach)
								 select $group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, 
								 can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, can_attach from user_privilege where group_id = $old_group_id ";
				$db->send_cmd($cmd);
	//		$db->show_error();
			} else {
				//$passwd = md5($ORG_CODE);
				/*cdgs */
				//$passwd = md5($ORG_CODE);
				$passwd=encryptPwd($ORG_CODE);
				/*cdgs */
				$cmd = " insert into user_detail (id, group_id, username, password, fullname, address, create_date, create_by, update_date, update_by)
								  values ($max_id, $group_id, '$ORG_CODE', '$passwd', '$ORG_NAME', '$ORG_NAME', $update_date, $update_by, $update_date, 
								  $update_by) ";
				$db->send_cmd($cmd);
	//		$db->show_error();
			}
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ����Ң����Ũҡ�ç���ҧ��ǹ�Ҫ��èӹǹ $count_all ��¡��");
	} // end if
	
	if( $command=="DELETEPOS" ){
		if ($group_level==1) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3
								 from			PER_ORG
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02')
								 order by		ORG_CODE ";		
		} elseif ($group_level==4) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ACTIVE=1 and OL_CODE='03' 
								 order by		ORG_CODE ";		
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE]);
			$ORG_NAME = trim($data[ORG_NAME]);

			$cmd = " delete from user_group where name_th = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		
			$cmd = " delete from user_detail where fullname = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����ŷ�����");
	} // end if
        
        if($command=="RENAMEACTIVE"){
            $strSQL = " UPDATE  USER_DETAIL a
                                SET FULLNAME = (SELECT b.PER_NAME||' '||b.PER_SURNAME AS FULL_NAME 
                                                FROM PER_PERSONAL b
                                                WHERE  a.USER_LINK_ID=b.PER_ID 
                                                AND b.PER_STATUS=1 )
                                WHERE a.USER_FLAG = 'Y' and  a.GROUP_ID = $group_id ";
            $db_dpis->send_cmd($strSQL);
            //echo "<pre>".$strSQL;
            //$db_dpis->show_error();
            if(1==1){
                //echo "�Ѿഷ��ª������º��������";
                $msg_chk = 1;
            }else{
                //echo "�Ѿഷ��ª�����������";
                $msg_chk = 2;
            }
        }
	
	if( $command=="LOADASS" ){
		ini_set("max_execution_time", $max_execution_time);

		if($BKK_FLAG==1) {
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG_ASS
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02' or OL_CODE='03') and ORG_CODE is not NULL
								 order by		ORG_CODE ";		
		} elseif ($group_level==1) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG_ASS
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02') and ORG_CODE is not NULL and length(trim(ORG_CODE))=5
								 order by		ORG_CODE ";		
		} elseif ($group_level==2) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG_ASS
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02') and ORG_CODE is not NULL and length(trim(ORG_CODE))=5
								 order by		ORG_CODE ";		
		} elseif ($group_level==3) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG_ASS
								 where		DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and ORG_ACTIVE=1 and OL_CODE='02') and ORG_ACTIVE=1 and (OL_CODE='02' or OL_CODE='03') 
								 order by		ORG_CODE ";		
		} elseif ($group_level==4) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG_ASS
								 where		DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ACTIVE=1 and OL_CODE='03' 
								 order by		ORG_CODE ";		
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE])." (�ͺ���§ҹ)";
			$ORG_NAME = trim($data[ORG_NAME])." (�ͺ���§ҹ)";
			if ($DOH_FLAG == 1 && trim($data[ORG_SHORT])) $ORG_CODE = trim($data[ORG_SHORT])." (�ͺ���§ҹ)";
			$OL_CODE = trim($data[OL_CODE]);
			if ($OL_CODE=="01") $GROUP_LEVEL = 3;
			elseif ($OL_CODE=="02") $GROUP_LEVEL = 4;
			elseif ($OL_CODE=="03") $GROUP_LEVEL = 5;

			$cmd = " delete from user_detail where fullname = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		
			$cmd = " select id from user_group where name_th = '$ORG_NAME' ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data) {
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_id = $data[id];

				$cmd = " delete from user_privilege where group_id = $old_id ";
				$db->send_cmd($cmd);
		
				$cmd = " delete from user_group where id = $old_id ";
				$db->send_cmd($cmd);
			}
		
			$cmd = " select max(id) as max_id from user_group ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$group_id = $data[max_id] + 1;
			$cmd = " insert into user_group (id, code, name_th, name_en, access_list, group_level, org_id, group_per_type, group_org_structure, group_active, 
							  create_date, create_by, update_date, update_by) 
							  values ($group_id, '$ORG_CODE', '$ORG_NAME', '$ORG_NAME', ',1,', $GROUP_LEVEL, $ORG_ID, 0, 1, 1 , 
							  $update_date, $update_by, $update_date, $update_by)";
			$db->send_cmd($cmd);
//		$db->show_error();

			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			
			if (substr($ORG_CODE,2,3)=="000")
				$username = 'M'.substr($ORG_CODE,0,2).'02';
			else
				$username = 'M'.substr($ORG_CODE,0,2).'D'.substr($ORG_CODE,3,2).'02';
			$cmd = " select id, group_id from user_detail where username='$username' ";
			$count_data = $db->send_cmd($cmd);
			if ($count_data) {
				$data = $db->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_id = $data[id];
				$old_group_id = $data[group_id];
				$cmd = " update user_detail set group_id = $group_id where id = $old_id ";
				$db->send_cmd($cmd);
	//		$db->show_error();

				$cmd = " insert into user_privilege (group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, 
								 can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, can_attach)
								 select $group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, 
								 can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, can_attach from user_privilege where group_id = $old_group_id ";
				$db->send_cmd($cmd);
	//		$db->show_error();
			} else {
				$passwd = md5($ORG_CODE);
				$cmd = " insert into user_detail (id, group_id, username, password, fullname, address, create_date, create_by, update_date, update_by)
								  values ($max_id, $group_id, '$ORG_CODE', '$passwd', '$ORG_NAME', '$ORG_NAME', $update_date, $update_by, $update_date, 
								  $update_by) ";
				$db->send_cmd($cmd);
	//		$db->show_error();
			}
		} // end while
explode("|",'xxx|xvvvv|111232');
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ����Ң����Ũҡ�ç���ҧ��ǹ�Ҫ��� (�ͺ���§ҹ) �ӹǹ $count_all ��¡��");
	} // end if
	
	if( $command=="DELETEASS" ){
		if ($group_level==1) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3
								 from			PER_ORG
								 where		ORG_ACTIVE=1 and (OL_CODE='01' or OL_CODE='02')
								 order by		ORG_CODE ";		
		} elseif ($group_level==4) { 
			$cmd = " select			ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, OL_CODE
								 from			PER_ORG
								 where		DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ACTIVE=1 and OL_CODE='03' 
								 order by		ORG_CODE ";		
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$ORG_ID = $data[ORG_ID];
			$ORG_CODE = trim($data[ORG_CODE])." (�ͺ���§ҹ)";
			$ORG_NAME = trim($data[ORG_NAME])." (�ͺ���§ҹ)";

			$cmd = " delete from user_group where name_th = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		
			$cmd = " delete from user_detail where fullname = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����ŷ�����");
	} // end if
	
	/*if( $command=="LOADDPIS" ){
		ini_set("max_execution_time", $max_execution_time);

		$cmd = " delete from user_detail where group_id = $group_id ";
		$db->send_cmd($cmd);
		
		switch($group_level){
			case 1 :
			case 2 :
				$search_condition = "";
				break;
			case 3 :
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_search_org[] = $data[ORG_ID];
				$search_condition = " and (a.DEPARTMENT_ID in (". implode(",", $arr_search_org) .")) ";
				break;
			case 4 :
//				$search_condition = " and (a.DEPARTMENT_ID=$DEPARTMENT_ID) ";
				break;
			case 5 :
				$search_condition = " and (b.ORG_ID=$ORG_ID or c.ORG_ID=$ORG_ID or d.ORG_ID=$ORG_ID) ";
				break;
		} // end switch case

		if ($BKK_FLAG==1) {
			$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
							 from			PER_PERSONAL a, PER_POSITION b
							 where		a.POS_ID=b.POS_ID and a.PER_CARDNO is not null and a.PER_TYPE=1 and a.PER_STATUS=1 
												$search_condition
							 order by		a.PER_ID ";		
		} else {
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_ID as EMP_ORG_ID, d.ORG_ID as EMPSER_ORG_ID,
													a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
								 from			(
													(
														PER_PERSONAL a
														left join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)
								 where		trim(a.PER_CARDNO)<>'' and a.PER_CARDNO is not null and a.PER_STATUS=1
													$search_condition
								 order by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_ID as EMP_ORG_ID,
													a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
								 from			PER_PERSONAL a, PER_POSITION b, PER_POS_EMP c, PER_POS_EMPSER d
								 where		a.POS_ID=b.POS_ID(+) and a.POEM_ID=c.POEM_ID(+) and a.POEMS_ID=d.POEMS_ID(+)
													and a.PER_CARDNO is not null and a.PER_STATUS=1 
													$search_condition
								 order by		a.PER_ID ";		
			} // end if
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
			
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$PER_BIRTHDATE = $arr_temp[2] . $arr_temp[1] . ($arr_temp[0] + 543);
			} // end if

			$PER_TYPE = $data[PER_TYPE];
			if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
			elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
			elseif($PER_TYPE==3) $ORG_ID = $data[EMPSER_ORG_ID];
			elseif($PER_TYPE==4) $ORG_ID = $data[TEMP_ORG_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			
			$username = $PER_CARDNO;
			$passwd = $PER_BIRTHDATE?md5($PER_BIRTHDATE):md5($PER_CARDNO);
			$user_link_id = $PER_ID;
			$user_name = $FULLNAME;
			$address = $ORG_NAME;
			$titlename = $PN_NAME;
			
			$cmd = " insert into user_detail (id, group_id, username, password, user_link_id, fullname, 
							address, create_date, create_by, update_date, update_by, titlename)
							 values ($max_id, $group_id, '$username', '$passwd', $user_link_id, '$user_name', 
							'$address', $update_date, $update_by, $update_date, $update_by, '$titlename') ";
			$db->send_cmd($cmd);
//			$db->show_error();
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ����Ң����Ũҡ DPIS �ӹǹ $count_all ��¡��");
	} // end if*/
	
	if( $command=="DELETEALL" ){
		//$cmd = " delete from user_detail where group_id = $group_id "; //���
                $cmd = " update user_detail set user_flag='N' where group_id = $group_id "; //Release 5.2.1.21
		$db->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����ŷ�����");
	} // end if
	
	if( $command=='ADD' && ( trim($new_username) || trim($link_username) )){
		if($user_type=="T")	$new_username = $link_username;
	
		$cmd = "select id from user_detail where username='$new_username' and user_flag='Y' ";
		if($db->send_cmd($cmd)){
			$error_signin = "Error :: Username ��� ��سҡ�͡����������";
		}else{
			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			//$passwd = md5($passwd);
			/* cdgs */
			//$passwd = md5($passwd);
			$passwd = encryptPwd($passwd);
			/*cdgs*/
			$cmd = " insert into user_detail (id, group_id, username, password, inherit_group, user_link_id, fullname, address, 
							district_id, amphur_id, province_id, email, tel, fax, titlename, create_date, create_by, update_date, update_by)
							 values ($max_id, $select_group_id, '$new_username', '$passwd', '$inherit_group', '$user_link_id', 
							'$user_name', '$user_address', $district_id, $amphur_id, $province_id, '$user_email', '$user_tel', 
							'$user_fax', '$user_titlename', $update_date, $update_by, $update_date, $update_by) ";
			$db->send_cmd($cmd);
//			$db->show_error();
			$CLEAR = 1;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������ҹ [$new_username -> $user_name]");
		}
	}

	if( $command=='UPDATE' && ( trim($new_username) || trim($link_username) )){
		$error_signin = "";
		$UPD = 1;
		if($user_type=="T")	$new_username = $link_username;

		if(trim($new_username) != $username){
			$cmd = "select id from user_detail where username='$new_username' and user_flag='Y' ";
			if($db->send_cmd($cmd)) $error_signin = "Error :: Duplicate Username";
		} // end if
		if(!trim($error_signin)){
			$UPD = "";
			if($passwd){
				// $set_password = ", password = '".md5($passwd)."'";
				/* cdgs */
				//$set_password = ", password = '".md5($passwd)."'";
				$set_password = ", password = '".encryptPwd($passwd)."'";
				/* cdgs */
			}
			$cmd = " update user_detail set 
								username = '$new_username' 
								$set_password, 
								group_id = '$select_group_id',
								inherit_group = '$inherit_group', 
								user_link_id = '$user_link_id',
								fullname = '$user_name', 
								address = '$user_address', 
								district_id = $district_id, 
								amphur_id = $amphur_id, 
								province_id = $province_id, 
								email = '$user_email', 
								tel = '$user_tel', 
								fax = '$user_fax',
								titlename = '$user_titlename',
								update_date = $update_date, 
								update_by = $update_by
							where id=$id ";
			$db->send_cmd($cmd);
	//		$db->show_error();
			$CLEAR = 1;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢼����ҹ [$username -> $user_name]");
		} // end if
	}

	if ($command == "DELETE" && $id) {
		$cmd = " select username, fullname from user_detail where id=$id and user_flag='Y' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$username = $data[username];
		$user_name = $data[fullname];
		
		//$cmd = " delete from user_detail where id = $id "; //���
                $cmd = " update user_detail set user_flag='N' where id = $id "; //Release 5.2.1.21
		$db->send_cmd($cmd);
		$CLEAR = 1;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����ҹ [$username -> $user_name]");
	}

	if($UPD){
		$cmd = " select 	username, group_id, inherit_group, user_link_id, fullname, address, district_id, amphur_id, province_id, email, tel, fax, titlename, update_by, update_date
				 		 from 		user_detail 
						 where 	id=$id and user_flag='Y' ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$username = $data[username]; 
		$select_group_id = $data[group_id];
		$inherit_group = trim($data[inherit_group]);
		$user_link_id = $data[user_link_id];
		$update_user = $data[update_by];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $update_user ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[update_date]), $DATE_DISPLAY);
		if($user_link_id){
			if($DPISDB=="odbc")
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, d.ORG_NAME, f.ORG_NAME as EMP_ORG_NAME , h.ORG_NAME as EMPSER_ORG_NAME , j.ORG_NAME as TEMP_ORG_NAME
								 from 	(	
								 			  (
								 				(
													(
														(
															(
														(
															(
																PER_PERSONAL a
																left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
															) left join PER_POSITION c on (a.POS_ID=c.POS_ID)
														) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
													) left join PER_POS_EMP e on (a.POEM_ID=e.POEM_ID)
												) left join PER_ORG f on (e.ORG_ID=f.ORG_ID)
											) left join PER_POS_EMPSER g on (a.POEMS_ID=g.POEMS_ID)
										) left join PER_ORG h on (g.ORG_ID=h.ORG_ID)
									) left join PER_POS_TEMP i on (a.POT_ID=i.POT_ID)
								) left join PER_ORG j on (i.ORG_ID=j.ORG_ID)
								 where 	a.PER_ID=". $user_link_id;
			elseif($DPISDB=="oci8")
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, d.ORG_NAME, f.ORG_NAME as EMP_ORG_NAME , h.ORG_NAME as EMPSER_ORG_NAME, j.ORG_NAME as TEMP_ORG_NAME
								 from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_ORG d, PER_POS_EMP e, PER_ORG f , PER_POS_EMPSER g , PER_ORG h , PER_POS_TEMP i , PER_ORG j
								 where 	a.PN_CODE=b.PN_CODE(+) and a.POS_ID=c.POS_ID(+) and c.ORG_ID=d.ORG_ID(+) 
								 				and a.POEM_ID=e.POEM_ID(+) and e.ORG_ID=f.ORG_ID(+) and a.POEMS_ID=g.POEMS_ID(+) and g.ORG_ID=h.ORG_ID(+) and a.POT_ID=i.POT_ID(+) and i.ORG_ID=j.ORG_ID(+)
								 				and a.PER_ID=". $user_link_id;
			$db_dpis->send_cmd($cmd);
//			echo $cmd;
//			$db_dpis->show_error();
			$data_dpis = $db_dpis->get_array();
			$user_titlename = $data_dpis[PN_NAME];
			$user_name = $data_dpis[PER_NAME] ." ". $data_dpis[PER_SURNAME];
			if($data_dpis[PER_TYPE]==1) $user_address = $data_dpis[ORG_NAME];
			elseif($data_dpis[PER_TYPE]==2) $user_address = $data_dpis[EMP_ORG_NAME];
			elseif($data_dpis[PER_TYPE]==3) $user_address = $data_dpis[EMPSER_ORG_NAME];
			elseif($data_dpis[PER_TYPE]==4) $user_address = $data_dpis[TEMP_ORG_NAME];
		}else{
			$user_name = $data[fullname];
			$user_address = $data[address];
			$user_titlename = $data[titlename];
		} // end if
		$district_id = $data[district_id]; $amphur_id = $data[amphur_id]; $province_id = $data[province_id]; 
		$user_email = $data[email];
		$user_tel = $data[tel]; $user_fax = $data[fax]; 
		
		if($inherit_group) $arr_inherit_group = explode(",", $inherit_group);
	}
if($command == "SETFLAG"){
			   
			$cnt=0;
			foreach ($TXTCODE as $key => $val) {
				$all_txtx =  explode("_",$val);
				$arr[$cnt][flag] = $all_txtx[0];
				$arr[$cnt][id] = $all_txtx[1];
				$arr[$cnt][cardno] = $all_txtx[2];
				$arr[$cnt][old_flag] = $all_txtx[3];
				$arr[$cnt][per_id] = $all_txtx[4];
				
				$cnt++;
			}
			//sort($TXTCODE);
			
			$sort_arr[0]['name'] = "flag";
			$sort_arr[0]['sort'] = "ASC";
			$sort_arr[0]['case'] = FALSE;
			
			$sort_arr[1]['name'] = "id";
			$sort_arr[1]['sort'] = "DESC";
			$sort_arr[1]['case'] = FALSE;
			   
			array_sort($arr, $sort_arr);
			
			for($ix = 0; $ix<count($arr); $ix++){
				$old_flag  = $arr[$ix][old_flag];	

				if($arr[$ix][flag]=='N'){
					if($old_flag == 'Y'){
						$cnt_old_flag += count($old_flag);
					}
				}
			}	
			$num_ch = $cnt_old_flag;
			for($x = 0; $x<count($arr) ; $x++) {
				if($arr[$x][flag]=='N'){
					$card_no_n = $arr[$x][cardno];
					$id_n	  = $arr[$x][id];
					$per_id_n = $arr[$x][per_id];
					 $cmd="select ID, FULLNAME, USER_FLAG  from user_detail where user_flag = 'Y' and id = '$id_n'";
						$cnt_after_flag =  $db_dpis3->send_cmd($cmd);
						$datax = $db_dpis3->get_array();
						$cmd ="select per_posdate,pos_id  from per_personal where per_id='$per_id_n'";
						$db_dpis3->send_cmd($cmd);
						$dataN = $db_dpis3->get_array();
						$per_postdate = $dataN[PER_POSDATE];
						$pos_id = $dataN[POS_ID];						
						
						 if($cnt_after_flag == 1){//�ԡ�͡�ҡ���� 1 ��¡��
							 $multi_id_n .= $id_n.",";
							 if($num_ch == 1){
								$cmd ="update user_detail set user_flag = 'N' where id =  $id_n"; 
							    $db_dpis2->send_cmd($cmd); 
							 }
						 }else{
							 $cmd ="update user_detail set user_flag = 'N' where id =  $id_n"; 
							 $db_dpis2->send_cmd($cmd);
							 
						 }
						if($per_id_n && $per_postdate && ($pos_id == "")){
						$cmd ="update per_personal set 
											per_status = '2'             
											where per_id = $per_id_n "; 
						 $db_dpis2->send_cmd($cmd);
						}
				}else{//end flag_y
				   $card_no_y = $arr[$x][cardno];
				   $id_y	  = $arr[$x][id];
				   $per_id_y = $arr[$x][per_id];
				   
				   $cmd="select ID, FULLNAME, USER_FLAG from user_detail where user_flag = 'Y' and username = '$card_no_y'";
				   $cnt_user_y = $db_dpis->send_cmd($cmd);
				   $data = $db_dpis->get_array();
					$USER_FLAG = $data[USER_FLAG];
					$id_ch = $data[ID];

					$cmd ="select PER_BIRTHDATE  from per_personal where per_id='$per_id_y'";
					 $db_dpis3->send_cmd($cmd);
					 $data3 = $db_dpis3->get_array();
					 $PER_BIRTHDATE= trim($data3[PER_BIRTHDATE]); 
					 $new_pssw = explode('-',$PER_BIRTHDATE);
					 $pass_year = $new_pssw[0]+543;
					 $passw = $new_pssw[2].$new_pssw[1].$pass_year;
					 $passw = md5($passw);	 
						 
				   if($cnt_user_y == 0){
						 $cmd ="update user_detail set 
											user_flag = 'Y',
											password = '$passw'                
											where id =  $id_y "; 
						 $db_dpis2->send_cmd($cmd);
						 if($per_id_y){
						 $cmd ="update per_personal set 
											per_status = '1'             
											where per_id = $per_id_y "; 
						 $db_dpis2->send_cmd($cmd);
						 }
					}else{

						if($data[ID]!=$id_y){
						 $cmd ="update user_detail set user_flag = 'N' where id =  $id_y"; 
						 $db_dpis2->send_cmd($cmd);
						 
						  $fullname_ch .= "<tr><td>&nbsp;</td><td align=\"center\" ><font color=\"red\">�պѭ�ռ����ҹ ".$data[FULLNAME]." ���������<font></td></tr>"; 	
											  
						 }	
					}
					
				}
				
			}	
   $command="";  
}//end command
	if( (!$UPD && !$DEL && !$error_signin) ){
		// clear all variable
		$select_group_id = $group_id;
		unset($id);		
		unset($username);		
		unset($passwd);			
		unset($confirm_passwd);
		unset($user_link_id);	
		unset($link_username);
		unset($user_name);	
		unset($user_address); 
		unset($district_id); 		
		unset($amphur_id); 	
		unset($province_id); 	
		unset($user_email);
		unset($user_tel); 			
		unset($user_fax);
		unset($user_titlename);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	}			
?>