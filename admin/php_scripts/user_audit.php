<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE_SAVE = date("Y-m-d H:i:s");
		
	if(!$group_id){
		$cmd = " select ID from user_group where CODE='HRG' ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$group_id = $data[ID];
		$HID_HRG = 1;
		
	}
	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$group_id ";
	$db->send_cmd($cmd);
//	$db->show_error(); 
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$group_code = $data[code];
	$group_level = $data[group_level];
	
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
        
        
        if($command=="DELETEAUDIT"){
            $IDdECode = explode("_",base64_decode($idEncode));

            $valPER_CARDNO = $IDdECode[0];
            $valDEPARTMENT_ID = $IDdECode[1];
            $valORG_ASS_ID = $IDdECode[2];
            $valORG_LOWER1 = $IDdECode[3];
            if(empty($valORG_LOWER1)){
                $cmdDel = " DELETE FROM TA_PER_AUDIT WHERE PER_CARDNO='$valPER_CARDNO' AND DEPARTMENT_ID=$valDEPARTMENT_ID AND ORG_ASS_ID=$valORG_ASS_ID ";
            }else{
                $cmdDel = " DELETE FROM TA_PER_AUDIT 
                            WHERE PER_CARDNO='$valPER_CARDNO' 
                                AND DEPARTMENT_ID=$valDEPARTMENT_ID AND ORG_ASS_ID=$valORG_ASS_ID AND ORG_LOWER1=$valORG_LOWER1 ";
            }
            //echo "<pre>".$cmdDel."<br><br>";
            $db_dpis->send_cmd($cmdDel);
            
            $cmdChk = "SELECT COUNT(PER_CARDNO) AS CNTAUDIT FROM TA_PER_AUDIT WHERE PER_CARDNO='$valPER_CARDNO' ";
            //echo "<pre>".$cmdChk."<br><br>";
            $db_dpis->send_cmd($cmdChk);
            $dataChk = $db_dpis->get_array();
            $count_audit = $dataChk[CNTAUDIT];
            if($count_audit==0){
                $cmdUpdate = "UPDATE PER_PERSONAL SET PER_AUDIT_FLAG =0 WHERE PER_CARDNO='$valPER_CARDNO' ";
                //echo "<pre>".$cmdUpdate."<br><br>";
                $db_dpis->send_cmd($cmdUpdate);
            }
            $command="";
        }
        
        if($command=="ADDAUDIT"){
            //die($DEPARTMENT_ID.$DEPARTMENT_NAME);
            $valPER_CARDNO  = $link_username;
            $valDEPARTMENT_ID = $DEPARTMENT_ID;
            
			/*$cmdPer = "SELECT ORG_ID,ORG_ID_1 FROM PER_PERSONAL WHERE PER_CARDNO='$valPER_CARDNO' ";
            $cnt = $db_dpis->send_cmd($cmdPer);
            if($cnt>0){
                $dataPer = $db_dpis->get_array();
                $valORG_ASS_ID = $dataPer[ORG_ID];
                $valORG_LOWER1 = $dataPer[ORG_ID_1];
            }*/
            //if(!empty($valORG_ASS_ID)){
                /*if(empty($valORG_LOWER1)){
                    $valORG_LOWER1 = "NULL";
                }*/
			if ($Level==2){
					$valORG_ASS_ID = $hidorg0;
					if ($hidorg1){
						$valORG_LOWER1 = $hidorg1;
					}else{
						$valORG_LOWER1 = "NULL";
					}
			}else{
				$valORG_ASS_ID = $hidorg0;
				$valORG_LOWER1 = "NULL";
			}
			
			if ($Level==2 && empty($hidorg1)){
				echo "<script>alert('รายชื่อดังกล่าวไม่สามารถเป็นผู้ตรวจสอบการลา ในระดับต่ำกว่าสำนัก/กอง 1 ระดับได้\\nเนื่องจากไม่ได้สังกัดต่ำกว่าสำนัก/กอง 1 ระดับ กรุณาตรวจสอบรายชื่อดังกล่าวอีกครั้ง');</script>";
				
			}else{
				$cmdInt="INSERT INTO TA_PER_AUDIT (PER_CARDNO,DEPARTMENT_ID,ORG_ASS_ID,ORG_LOWER1,CREATE_USER,CREATE_DATE) 
				VALUES('$valPER_CARDNO',$valDEPARTMENT_ID,$valORG_ASS_ID,$valORG_LOWER1,$SESS_USERID,'$UPDATE_DATE_SAVE')";
				$db_dpis1->send_cmd($cmdInt);
				
				$cmd="UPDATE PER_PERSONAL SET PER_AUDIT_FLAG=1 WHERE PER_CARDNO='$valPER_CARDNO' ";
				$db_dpis1->send_cmd($cmd);
			}
            //}
            $command="";
        }
        
        
        
        
	
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
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
				$passwd = md5($ORG_CODE);
				$cmd = " insert into user_detail (id, group_id, username, password, fullname, address, create_date, create_by, update_date, update_by)
								  values ($max_id, $group_id, '$ORG_CODE', '$passwd', '$ORG_NAME', '$ORG_NAME', $update_date, $update_by, $update_date, 
								  $update_by) ";
				$db->send_cmd($cmd);
	//		$db->show_error();
			}
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจากโครงสร้างส่วนราชการจำนวน $count_all รายการ");
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

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if
	
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
			$ORG_CODE = trim($data[ORG_CODE])." (มอบหมายงาน)";
			$ORG_NAME = trim($data[ORG_NAME])." (มอบหมายงาน)";
			if ($DOH_FLAG == 1 && trim($data[ORG_SHORT])) $ORG_CODE = trim($data[ORG_SHORT])." (มอบหมายงาน)";
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

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจากโครงสร้างส่วนราชการ (มอบหมายงาน) จำนวน $count_all รายการ");
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
			$ORG_CODE = trim($data[ORG_CODE])." (มอบหมายงาน)";
			$ORG_NAME = trim($data[ORG_NAME])." (มอบหมายงาน)";

			$cmd = " delete from user_group where name_th = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		
			$cmd = " delete from user_detail where fullname = '$ORG_NAME' ";
			$db->send_cmd($cmd);
		} // end while

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if
	
	if( $command=="LOADDPIS" ){
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

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจาก DPIS จำนวน $count_all รายการ");
	} // end if
	
	if( $command=="DELETEALL" ){
		$cmd = " delete from user_detail where group_id = $group_id ";
		$db->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลทั้งหมด");
	} // end if
	
	if( $command=='ADD' && ( trim($new_username) || trim($link_username) )){
		if($user_type=="T")	$new_username = $link_username;
	
		$cmd = "select id from user_detail where username='$new_username'";
		if($db->send_cmd($cmd)){
			$error_signin = "Error :: Username ซ้ำ กรุณากรอกข้อมูลใหม่";
		}else{
			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			$passwd = md5($passwd);
			$cmd = " insert into user_detail (id, group_id, username, password, inherit_group, user_link_id, fullname, address, 
							district_id, amphur_id, province_id, email, tel, fax, titlename, create_date, create_by, update_date, update_by)
							 values ($max_id, $select_group_id, '$new_username', '$passwd', '$inherit_group', '$user_link_id', 
							'$user_name', '$user_address', $district_id, $amphur_id, $province_id, '$user_email', '$user_tel', 
							'$user_fax', '$user_titlename', $update_date, $update_by, $update_date, $update_by) ";
			$db->send_cmd($cmd);
//			$db->show_error();
			$CLEAR = 1;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มผู้ใช้งาน [$new_username -> $user_name]");
		}
	}

	if( $command=='UPDATE' && ( trim($new_username) || trim($link_username) )){
		$error_signin = "";
		$UPD = 1;
		if($user_type=="T")	$new_username = $link_username;

		if(trim($new_username) != $username){
			$cmd = "select id from user_detail where username='$new_username'";
			if($db->send_cmd($cmd)) $error_signin = "Error :: Duplicate Username";
		} // end if
		if(!trim($error_signin)){
			$UPD = "";
			if($passwd){
				$set_password = ", password = '".md5($passwd)."'";
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

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขผู้ใช้งาน [$username -> $user_name]");
		} // end if
	}

	if ($command == "DELETE" && $id) {
		$cmd = " select username, fullname from user_detail where id=$id ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$username = $data[username];
		$user_name = $data[fullname];
		
		$cmd = " delete from user_detail where id = $id ";
		$db->send_cmd($cmd);
		$CLEAR = 1;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบผู้ใช้งาน [$username -> $user_name]");
	}

	if($UPD){
		$cmd = " select 	username, group_id, inherit_group, user_link_id, fullname, address, district_id, amphur_id, province_id, email, tel, fax, titlename, update_by, update_date
				 		 from 		user_detail 
						 where 	id=$id ";
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
		unset($org0);
		unset($hidorg0);
		unset($org1);
		unset($hidorg1);
	}			
?>