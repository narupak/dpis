<?	
	include("php_scripts/session_start.php");
//	echo "bf load....PROVINCE_NAME=$PROVINCE_NAME, MINISTRY_NAME=$MINISTRY_NAME, DEPARTMENT_NAME=$DEPARTMENT_NAME, ORG_NAME=$ORG_NAME, ORG_NAME_1=$ORG_NAME_1<br>";
	$PROVINCE_NAME_TMP=$PROVINCE_NAME;
	$MINISTRY_NAME_TMP=$MINISTRY_NAME;
	$DEPARTMENT_NAME_TMP=$DEPARTMENT_NAME;
	$ORG_NAME_TMP=$ORG_NAME;
	$ORG_NAME_1_TMP=$ORG_NAME_1;
	include("php_scripts/load_per_control.php");
	$PROVINCE_NAME=$PROVINCE_NAME_TMP;
	$MINISTRY_NAME=$MINISTRY_NAME_TMP;
	$DEPARTMENT_NAME=$DEPARTMENT_NAME_TMP;
	$ORG_NAME=$ORG_NAME_TMP;
	$ORG_NAME_1=$ORG_NAME_1_TMP;
//	echo "af load....PROVINCE_NAME=$PROVINCE_NAME, MINISTRY_NAME=$MINISTRY_NAME, DEPARTMENT_NAME=$DEPARTMENT_NAME, ORG_NAME=$ORG_NAME, ORG_NAME_1=$ORG_NAME_1<br>";
	include("php_scripts/function_share.php");

	if($command == "UPDATE"){
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
		switch($group_level){
			case 1 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = 0;
				break;
			case 2 :
				$CH_ORG_ID = 0;
				break;
			case 3 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_MINISTRY_ID;
				break;
			case 4 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_DEPARTMENT_ID;
				break;
			case 5 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_ORG_ID;
				break;
			case 6 :
				$CH_PROVINCE_CODE = "";
				$CH_ORG_ID = $CH_ORG_ID_1;
				break;
		} // end switch case
  
//		$_select_level_no_str = implode("','",$_select_level_no);
		if ($_select_level_no[0] == "") $_select_level_no_str  = "";
		else $_select_level_no_str = implode(",",$_select_level_no);
		if(trim($group_id)){
				$cmd = " update user_group set 
									group_level = $group_level,
									pv_code = '$CH_PROVINCE_CODE',
									org_id = $CH_ORG_ID,
									update_by = $update_by,
									update_date = $update_date,
									group_per_type=$group_per_type,
									group_org_structure=$group_org_structure,
									level_no_list = '$_select_level_no_str'
								 where id=$group_id ";
				$db->send_cmd($cmd);
 //				$db->show_error();
				//echo "cmd=".$cmd."<br>";
		}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$group_id : $PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID ; $ORG_ID => $CH_ORG_ID]");
		$f_upd="1";
	}	//end if($command == "UPDATE")	
	
	// =================================== PER_CONTROL ==================================// [ตัวกลาง]
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;
	
	switch($CTRL_TYPE){
		case 2 :
			$CTRL_PROVINCE_CODE = $data[PV_CODE];
			break;
		case 3 :
			$CTRL_MINISTRY_ID = $data[ORG_ID];
			break;
		case 4 :
			$CTRL_DEPARTMENT_ID = $data[ORG_ID];
			break;
		case 5 :
			$CTRL_ORG_ID = $data[ORG_ID];
			break;
		case 6 :
			$CTRL_ORG_ID_1 = $data[ORG_ID];
			break;
	} // end switch case

	if($CTRL_ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_ORG_ID_1 ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_ORG_NAME_1 = $data[ORG_NAME];
		$CTRL_ORG_ID = $data[ORG_ID_REF];	
	} // end if
	
	if($CTRL_ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_ORG_ID ";
		if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_ORG_NAME = $data[ORG_NAME];
		$CTRL_DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($CTRL_DEPARTMENT_ID){	//***DEPARTMENT ไมใช้ตาราง PER_ORG_ASS
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$CTRL_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_DEPARTMENT_NAME = $data[ORG_NAME];
		$CTRL_MINISTRY_ID = $data[ORG_ID_REF];
	} // end if

	if($CTRL_MINISTRY_ID){	//***MINISTRY ไมใช้ตาราง PER_ORG_ASS
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CTRL_MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($CTRL_PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$CTRL_PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CTRL_PROVINCE_NAME = $data[PV_NAME];
	} // end if
	// =================================== PER_CONTROL ==================================//	

//	echo "$CTRL_TYPE :: $ctrl_type<br>";
	if(!$command){
		switch($CTRL_TYPE){
			case 2 :
				$CTRL_PROVINCE_CODE = $CTRL_PROVINCE_CODE;
				$CTRL_PROVINCE_NAME = $CTRL_PROVINCE_NAME;
				break;
			case 3 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				break;
			case 4 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				break;
			case 5 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				$CTRL_ORG_ID = $CTRL_ORG_ID;
				$CTRL_ORG_NAME = $CTRL_ORG_NAME;
				break;
			case 6 :
				$CTRL_MINISTRY_ID = $CTRL_MINISTRY_ID;
				$CTRL_MINISTRY_NAME = $CTRL_MINISTRY_NAME;
				$CTRL_DEPARTMENT_ID = $CTRL_DEPARTMENT_ID;
				$CTRL_DEPARTMENT_NAME = $CTRL_DEPARTMENT_NAME;
				$CTRL_ORG_ID = $CTRL_ORG_ID;
				$CTRL_ORG_NAME = $CTRL_ORG_NAME;
				$CTRL_ORG_ID_1 = $CTRL_ORG_ID_1;
				$CTRL_ORG_NAME_1 = $CTRL_ORG_NAME_1;
				break;
		} // end switch case
	} // end if

if ($SESS_USERGROUP==trim($group_id)) {
	if ($group_level) {
		if($SESS_USERGROUP_LEVEL != $group_level){
			session_unregister("SESS_USERGROUP_LEVEL");
			$SESS_USERGROUP_LEVEL = $group_level;
			session_register("SESS_USERGROUP_LEVEL");
		} // end if
	}
	if ($CH_PROVINCE_CODE) {
		if($SESS_PROVINCE_CODE != $CH_PROVINCE_CODE){
			session_unregister("SESS_PROVINCE_CODE");
			session_unregister("SESS_PROVINCE_NAME");
			if($group_level==2){ 
				$SESS_PROVINCE_CODE = $CH_PROVINCE_CODE;	
				$SESS_PROVINCE_NAME = $PROVINCE_NAME;	
			}else{ 
				$SESS_PROVINCE_CODE = "";
				$SESS_PROVINCE_NAME = "";
			} // end if
			session_register("SESS_PROVINCE_CODE");
			session_register("SESS_PROVINCE_NAME");
//			echo "SESS_PROVINCE_NAME=$SESS_PROVINCE_NAME<br>";
		} // end if
	}
	if ($CH_MINISTRY_ID) {
		if($SESS_MINISTRY_ID != $CH_MINISTRY_ID){
			session_unregister("SESS_MINISTRY_ID");
			session_unregister("SESS_MINISTRY_NAME");
			if($group_level==3 || $group_level==4 || $group_level==5){ 
				$SESS_MINISTRY_ID = $CH_MINISTRY_ID;
				$SESS_MINISTRY_NAME = $MINISTRY_NAME;
			}else{ 
				$SESS_MINISTRY_ID = "";
				$SESS_MINISTRY_NAME = "";
			} // end if
			session_register("SESS_MINISTRY_ID");
			session_register("SESS_MINISTRY_NAME");
//			echo "SESS_MINISTRY_NAME=$SESS_MINISTRY_NAME<br>";
		} // end if
	}
	if ($CH_DEPARTMENT_ID) {	
		if($SESS_DEPARTMENT_ID != $CH_DEPARTMENT_ID){
			session_unregister("SESS_DEPARTMENT_ID");
			session_unregister("SESS_DEPARTMENT_NAME");
			if($group_level==4 || $group_level==5){
				$SESS_DEPARTMENT_ID = $CH_DEPARTMENT_ID;
				$SESS_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			}else{
				$SESS_DEPARTMENT_ID = "";
				$SESS_DEPARTMENT_NAME = "";
			} // end if
			session_register("SESS_DEPARTMENT_ID");
			session_register("SESS_DEPARTMENT_NAME");
//			echo "SESS_DEPARTMENT_NAME=$SESS_DEPARTMENT_NAME<br>";
		} // end if
	}
	if ($CH_ORG_ID) {		
		if($SESS_ORG_ID != $CH_ORG_ID){
			session_unregister("SESS_ORG_ID");
			session_unregister("SESS_ORG_NAME");
			if($group_level==5){
				$SESS_ORG_ID = $CH_ORG_ID;
				$SESS_ORG_NAME = $ORG_NAME;
			}else{
				$SESS_ORG_ID = "";
				$SESS_ORG_NAME = "";
			} // end if
			session_register("SESS_ORG_ID");
			session_register("SESS_ORG_NAME");
//			echo "SESS_ORG_NAME=$SESS_ORG_NAME, group_level=$group_level<br>";
		} // end if
	}
	if ($CH_ORG_ID_1) {		
		if($SESS_ORG_ID_1 != $CH_ORG_ID_1){
				session_unregister("SESS_ORG_ID_1");
				session_unregister("SESS_ORG_NAME_1");
				if($group_level==6){
					$SESS_ORG_ID_1 = $CH_ORG_ID_1;
					$SESS_ORG_NAME_1 = $ORG_NAME_1;
				}else{
					$SESS_ORG_ID_1 = "";
					$SESS_ORG_NAME_1 = "";
				} // end if
				session_register("SESS_ORG_ID1");
				session_register("SESS_ORG_NAME1");
		} // end if
	}
}

	if(trim($group_id) && $command != "change_per_type"){//เพื่อแสดงข้อมุล *********แต่ค่าตัวแปรบางตัวที่ไม่มี แต่ระดับกลุ่มไม่ตรงกัน มันไปดึงจาก /load_per_control.php (include) ด้วย *********
		//เคลียร์ค่าจากตัวกลาง หากมีของแต่ละกลุ่มอยู่ เพื่อไม่ให้ไปดึงจากตัวกลาง PER_CONTROL มา
		$PROVINCE_CODE = "";
		$CH_PROVINCE_CODE = "";			$CTRL_PROVINCE_NAME="";
		$PROVINCE_NAME = "";
		$MINISTRY_ID = "";
		$CH_MINISTRY_ID = "";					$CTRL_MINISTRY_NAME="";
		$MINISTRY_NAME = "";
		$DEPARTMENT_ID = "";
		$CH_DEPARTMENT_ID = "";			$CTRL_DEPARTMENT_NAME="";
		$DEPARTMENT_NAME = "";
		$ORG_ID = "";
		$CH_ORG_ID = "";							$CTRL_ORG_NAME="";
		$ORG_NAME = "";
		$ORG_ID_1 = "";
		$CH_ORG_ID_1 = "";						$CTRL_ORG_NAME_1="";
		$ORG_NAME_1 = "";
	
		$cmd = " select code, name_th, group_level, pv_code, org_id,group_per_type, group_org_structure, level_no_list, update_by, update_date from user_group where id=$group_id ";
		$db->send_cmd($cmd);
//		echo "select:".$cmd."<br>";
	//	$db->show_error();
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$group_code = $data[code];
		$group_name = $data[name_th];
		$group_level = $data[group_level];
		if(!$group_level) $group_level = 4;
		$group_per_type = $data[group_per_type];
		$group_org_structure = $data[group_org_structure];
		$group_pv_code = $data[pv_code];
		$group_org_id = $data[org_id];
		$level_no_list = $data[level_no_list];
		$update_user = $data[update_by];
		if (is_numeric($update_user)) 
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id = $update_user ";
		else
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where username = '$update_user' ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[update_date]), $DATE_DISPLAY);
//		echo "get from database-->group_per_type=$group_per_type , level_no_list=[$level_no_list]<br>";

		switch($group_level){
			case 2 :
				$PROVINCE_CODE = $group_pv_code;
				break;
			case 3 :
				$MINISTRY_ID = $group_org_id;
				break;
			case 4 :
				$DEPARTMENT_ID = $group_org_id;
				break;
			case 5 :
				$ORG_ID = $group_org_id;
				break;
			case 6 :
				$ORG_ID_1 = $group_org_id;
				break;
		} // end switch case
		$SESS_PER_TYPE=$group_per_type;
		session_register("SESS_PER_TYPE");
		//  echo "<br> $PROVINCE_CODE + $MINISTRY_ID + $DEPARTMENT_ID + $ORG_ID + $ORG_ID_1 >> $group_name [$group_id]  :: $group_level ->ID $group_org_id <<<br>";
		
		if($ORG_ID_1){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
			if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_NAME_1 = $data[ORG_NAME];
			$ORG_ID = $data[ORG_ID_REF];	
		} // end if
	
		if($ORG_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			if($group_org_structure==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);	}
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_NAME = $data[ORG_NAME];
			$DEPARTMENT_ID = $data[ORG_ID_REF];	
		} // end if
	
		if($DEPARTMENT_ID){	//***DEPARTMENT ไมใช้ตาราง PER_ORG_ASS
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];	
		} // end if
	
		if($MINISTRY_ID){	//***MINISTRY ไมใช้ตาราง PER_ORG_ASS
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
		// echo "++++ $group_id - ORG[$group_level] : $PROVINCE_CODE=>$PROVINCE_NAME - $MINISTRY_ID=>$MINISTRY_NAME - $DEPARTMENT_ID=>$DEPARTMENT_NAME - $ORG_ID=>$ORG_NAME - $ORG_ID_1=>$ORG_NAME_1 +++<br>";
		
		$_select_level_no = explode(",",$level_no_list);
		$SESS_LEVEL_NO_LIST = $_select_level_no;
		session_register("SESS_LEVEL_NO_LIST");
	}
/*
	echo "$group_id - [$group_org_id] - $group_org_structure <br> ";
	echo "$group_id - CTRL[$CTRL_TYPE] : $CTRL_PROVINCE_CODE=>$CTRL_PROVINCE_NAME - $CTRL_MINISTRY_ID=>$CTRL_MINISTRY_NAME - $CTRL_DEPARTMENT_ID=>$CTRL_DEPARTMENT_NAME - $CTRL_ORG_ID=>$CTRL_ORG_NAME - $CTRL_ORG_ID_1=>$CTRL_ORG_NAME_1<br>";
	echo "$group_id - ORG[$group_level] : $PROVINCE_CODE=>$PROVINCE_NAME - $MINISTRY_ID=>$MINISTRY_NAME - $DEPARTMENT_ID=>$DEPARTMENT_NAME - $ORG_ID=>$ORG_NAME - $ORG_ID_1=>$ORG_NAME_1<br>"; 
		print("<pre>");		print_r($SESS_LEVEL_NO_LIST);		print("</pre>"); */
?>