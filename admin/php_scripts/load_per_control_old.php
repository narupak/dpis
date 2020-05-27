<?
	// =================================== PER_CONTROL ==================================//
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;
	$TEMP_ORG_ID = $data[ORG_ID];
    
	switch($CTRL_TYPE){
		case 2 :
			$PROVINCE_CODE = $data[PV_CODE];
			break;
		case 3 :
			$MINISTRY_ID = $data[ORG_ID];
			break;
		case 4 :
			$DEPARTMENT_ID = $data[ORG_ID];
			break;
		case 5 :
			$ORG_ID = $data[ORG_ID];
			break;
		case 6 :
			$ORG_ID_1 = $data[ORG_ID];
			break;
	} // end switch case
	
//	echo "USERGROUP_LEVEL=$SESS_USERGROUP_LEVEL > CTRL_TYPE=$CTRL_TYPE<br>";
	if($SESS_USERGROUP_LEVEL >= $CTRL_TYPE){
		switch($SESS_USERGROUP_LEVEL){
			case 2 :
				$PROVINCE_CODE = $SESS_PROVINCE_CODE;
				break;
			case 3 :
				$MINISTRY_ID = $SESS_MINISTRY_ID;
				break;
			case 4 :
				$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;
				break;
			case 5 :
				$ORG_ID = $SESS_ORG_ID;
				break;
			case 6 :
				$ORG_ID_1 = $SESS_ORG_ID_1;
				break;
		} // end switch case
	} // end if
//echo "1..$SESS_PER_ID | $SESS_USERGROUP_LEVEL VS $CTRL_TYPE :2: $PROVINCE_CODE => $PROVINCE_NAME ,3: $MINISTRY_ID => $MINISTRY_NAME ,4: $DEPARTMENT_ID => $DEPARTMENT_NAME ,5: $ORG_ID => $ORG_NAME ,6: $ORG_ID_1 => $ORG_NAME_1<br>";

	if($isLock){
		if($LOCK_MINISTRY_ID) $MINISTRY_ID = $LOCK_MINISTRY_ID;
		if($LOCK_DEPARTMENT_ID) $DEPARTMENT_ID = $LOCK_DEPARTMENT_ID;
		if($LOCK_ORG_ID) $ORG_ID = $LOCK_ORG_ID;
		if($LOCK_ORG_ID_1) $ORG_ID_1 = $LOCK_ORG_ID_1;
	} // end if

	if($ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME_1 = $data[ORG_NAME];
		$ORG_ID = $data[ORG_ID_REF];
	}

	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];
	}

	if($PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROVINCE_NAME = $data[PV_NAME];
		//หา $DEPARTMENT_ID กรณี login เป็นคน
		if($SESS_PER_ID){
			$cmd = "select DEPARTMENT_ID from PER_PERSONAL where PER_ID=$SESS_PER_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];	
		}
	} // end if
	$PV_CODE = $PROVINCE_CODE;
	$PV_NAME = $PROVINCE_NAME;
	
	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];	

		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='$DEPARTMENT_NAME' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if(trim($data[ORG_ID])){	
			$DEPARTMENT_ID_ASS = $data[ORG_ID];
		}else{
			$DEPARTMENT_ID_ASS = -1;
		}
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if

    // เพิ่มเพื่อให้แสดง header จากข้อมูล session สำหรับกรณี logon ฐานข้อมูลตาม user
	if ($SESS_USERGROUP_LEVEL >= $CTRL_TYPE) {
		$CTRL_TYPE = $SESS_USERGROUP_LEVEL;
//		if($SESS_ORG_ID_1) $TEMP_ORG_ID = $SESS_ORG_ID_1;
//		elseif($SESS_ORG_ID) $TEMP_ORG_ID = $SESS_ORG_ID;
//		elseif($SESS_DEPARTMENT_ID) $TEMP_ORG_ID = $SESS_DEPARTMENT_ID;
		if($SESS_DEPARTMENT_ID) $TEMP_ORG_ID = $SESS_DEPARTMENT_ID;
		elseif($SESS_MINISTRY_ID) $TEMP_ORG_ID = $SESS_MINISTRY_ID;
		elseif($SESS_PROVINCE_CODE) $TEMP_ORG_ID = $SESS_PROVINCE_CODE;
    }

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TEMP_ORG_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$TEMP_ORG_NAME = $data[ORG_NAME];

	$cssfileselected = "stylesheets/style$RPT_N.css";
/*	if ($CTRL_TYPE!=1 && $CTRL_TYPE!=2 && 
		!(($CTRL_TYPE==3 && $TEMP_ORG_NAME=="กระทรวงเกษตรและสหกรณ์") || 
			$TEMP_ORG_NAME=="สำนักงานปลัดกระทรวงเกษตรและสหกรณ์") && 
		!(($CTRL_TYPE==3 && $TEMP_ORG_NAME=="กระทรวงอุตสาหกรรม") || 
			$TEMP_ORG_NAME=="สำนักงานปลัดกระทรวงอุตสาหกรรม") && 
		$TEMP_ORG_NAME!="กรมการปกครอง" &&
		$TEMP_ORG_NAME!="กรมอุตสาหกรรมพื้นฐานและการเหมืองแร่" &&
		$TEMP_ORG_NAME!="สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม" &&
		$TEMP_ORG_NAME!="สถาบันบัณฑิตพัฒนศิลป์") {
			$cssfileselected = "stylesheets/style".$RPT_N."_51.css";
	}*/
	if ($CTRL_TYPE!=1 && $CTRL_TYPE!=2 && $CTRL_TYPE!=3 && $TEMP_ORG_NAME=="สำนักงาน ก.พ.xxxxxxxxxx") {
		$cssfileselected = "stylesheets/style".$RPT_N."_51.css";
	}

//	if($SESS_USERGROUP_LEVEL) echo "2..$SESS_PER_ID | $SESS_USERGROUP_LEVEL VS $CTRL_TYPE :2: $PROVINCE_CODE => $PROVINCE_NAME ,3: $MINISTRY_ID => $MINISTRY_NAME ,4: $DEPARTMENT_ID => $DEPARTMENT_NAME ,5: $ORG_ID => $ORG_NAME ,6: $ORG_ID_1 => $ORG_NAME_1<br>";
	// =================================== PER_CONTROL ==================================//	
?>