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
		$cmd = " select ID from user_group where CODE='OT' ";
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
            $valPER_CARDNO = base64_decode($idEncode);
			$cmdUpdate = "UPDATE PER_PERSONAL SET PER_OT_FLAG =0 WHERE PER_CARDNO='$valPER_CARDNO' ";
			//echo "<pre>".$cmdUpdate."<br><br>";
			$db_dpis->send_cmd($cmdUpdate);
            $command="";
        }
        
        if($command=="ADDAUDIT"){
            //die($DEPARTMENT_ID.$DEPARTMENT_NAME);
			$valPER_CARDNO  = $link_username;
			
			/*$cmd = " select NVL(COUNT(PER_ID),0) AS CNTUSER from PER_PERSONAL where PER_CARDNO='$valPER_CARDNO' AND ORG_ID IS NOT NULL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if ($data[CNTUSER] > 0) {*/
			if ($Level==2){
					$Level_save = $hidorg1;
			}else{
				$Level_save = $hidorg0;
			}
			
			if ($Level==2 && empty($hidorg1)){
				echo "<script>alert('ไม่สามารถกำหนดค่าได้ เนื่องจากบุคคลนี้ไม่ได้สังกัดหน่วยงานที่ระดับต่ำกว่าสำนัก/กอง 1 ระดับ\\nกรุณาตรวจสอบข้อมูลอีกครั้ง');</script>";
				
			}else{
				$cmd="UPDATE PER_PERSONAL SET PER_OT_FLAG=$Level_save WHERE PER_CARDNO='$valPER_CARDNO' ";
				$db_dpis1->send_cmd($cmd);
			}
            $command="";
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