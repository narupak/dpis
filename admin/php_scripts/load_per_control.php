<? 

	// =================================== PER_CONTROL ==================================//
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID, FIX_CONTROL from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
        //echo "CTRL_TYPE :".$CTRL_TYPE;
	if(empty($CTRL_TYPE) || $CTRL_TYPE==1) $CTRL_TYPE = 4;
	$TEMP_ORG_ID = $data[ORG_ID];
	$FIX_CONTROL = $data[FIX_CONTROL];
    
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

//echo "$FIX_CONTROL ::: USERGROUP_LEVEL=$SESS_USERGROUP_LEVEL > CTRL_TYPE=$CTRL_TYPE<br>";
//echo "0..$SESS_PER_ID | $SESS_USERGROUP_LEVEL VS $CTRL_TYPE :2: $PROVINCE_CODE => $PROVINCE_NAME ,3: $MINISTRY_ID => $MINISTRY_NAME ,4: $DEPARTMENT_ID => $DEPARTMENT_NAME ,5: $ORG_ID => $ORG_NAME ,6: $ORG_ID_1 => $ORG_NAME_1<hr>";
//	if ($FIX_CONTROL != 1)	 { // ถ้าไม่ fix ข้อมูลสำหรับ center แล้ว
		
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
//	} // if ($FIX_CONTROL!=1)

//echo "1..$SESS_PER_ID | $SESS_USERGROUP_LEVEL VS $CTRL_TYPE :2: $PROVINCE_CODE => $PROVINCE_NAME ,3: $MINISTRY_ID => $MINISTRY_NAME ,4: $DEPARTMENT_ID => $DEPARTMENT_NAME ,5: $ORG_ID => $ORG_NAME ,6: $ORG_ID_1 => $ORG_NAME_1<hr>";

	if($isLock){
		if($LOCK_MINISTRY_ID) $MINISTRY_ID = $LOCK_MINISTRY_ID;
		if($LOCK_DEPARTMENT_ID) $DEPARTMENT_ID = $LOCK_DEPARTMENT_ID;
		if($LOCK_ORG_ID) $ORG_ID = $LOCK_ORG_ID;
		if($LOCK_ORG_ID_1) $ORG_ID_1 = $LOCK_ORG_ID_1;
	} // end if
	//echo "Lock : $LOCK_MINISTRY_ID -  $LOCK_DEPARTMENT_ID -  $LOCK_ORG_ID - $LOCK_ORG_ID_1 <br>";

	if($ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME_1 = $data[ORG_NAME];
		$ORG1_SHORT = $data[ORG_SHORT];
		$ORG_ID = $data[ORG_ID_REF];
	}

	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$ORG_SHORT = $data[ORG_SHORT];
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
			
			$cmd = " select ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$DEPARTMENT_SHORT = $data[ORG_SHORT];
		}
	} // end if
	$PV_CODE = $PROVINCE_CODE;
	$PV_NAME = $PROVINCE_NAME;
	
	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$DEPARTMENT_SHORT = $data[ORG_SHORT];
		$MINISTRY_ID = $data[ORG_ID_REF];	

		$cmd = " select ORG_ID,ORG_SHORT from PER_ORG_ASS where ORG_NAME='$DEPARTMENT_NAME' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if(trim($data[ORG_ID])){	
			$DEPARTMENT_ID_ASS = $data[ORG_ID];
			$DEPARTMENT_ID_ASS_SHORT = $data[ORG_SHORT];
		}else{
			$DEPARTMENT_ID_ASS = -1;
		}
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
		$MINISTRY_SHORT = $data[ORG_SHORT];
	} // end if

//	if ($FIX_CONTROL != 1)	 { // ถ้าไม่ fix ข้อมูลสำหรับ center แล้ว
	// เพิ่มเพื่อให้แสดกฆheader กฆกข้อมูกฆsession สำหรับกรณี logon ฐานข้อมูลตาม user
	if ($SESS_USERGROUP_LEVEL >= $CTRL_TYPE) {
			if ($FIX_CONTROL != 1)	 { // ถ้าไม่ fix ข้อมูลสำหรับ center แล้ว
				$CTRL_TYPE = $SESS_USERGROUP_LEVEL;
//				echo "CTRL_TYPE=$CTRL_TYPE<br>";
			}
			if($SESS_ORG_ID_1) $TEMP_ORG_ID = $SESS_ORG_ID_1;
			elseif($SESS_ORG_ID) $TEMP_ORG_ID = $SESS_ORG_ID;
			elseif($SESS_DEPARTMENT_ID) $TEMP_ORG_ID = $SESS_DEPARTMENT_ID;
			elseif($SESS_MINISTRY_ID) $TEMP_ORG_ID = $SESS_MINISTRY_ID;
			elseif($SESS_PROVINCE_CODE) $TEMP_ORG_ID = $SESS_PROVINCE_CODE;
  	}	//end if ($SESS_USERGROUP_LEVEL >= $CTRL_TYPE)
 // 	} // end if ($FIX_CONTROL != 1)
//	echo "TEMP_ORG_ID=$TEMP_ORG_ID<br>";

	$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$TEMP_ORG_ID ";
	if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$TEMP_ORG_NAME = $data[ORG_NAME];
	$TEMP_ORG_SHORT = $data[ORG_SHORT];

//	$cssfileselected = "stylesheets/style".$RPT_N.".css";
	$cssfileselected = "stylesheets/style.css";

	$R_CTRL_TYPE = $CTRL_TYPE;
    $f_end = false;
	while (!$f_end && $R_CTRL_TYPE > 0) {
		if(trim($PV_CODE)) $TEMP_PROVINCE_CODE = $PV_CODE;
		else $TEMP_PROVINCE_CODE = "NULL";
		if(!$TEMP_ORG_ID) $TEMP_ORG_ID = "NULL";

		if ($R_CTRL_TYPE == 1){
			$cond = " SITE_LEVEL = 1 ";
		}else if ($R_CTRL_TYPE == 2){
			if (is_null($TEMP_PROVINCE_CODE) || $TEMP_PROVINCE_CODE=="NULL" || $TEMP_PROVINCE_CODE=="'NULL'"){
				$cond = " SITE_LEVEL = 2 and PV_CODE = 'NULL' ";
			}else{
				$cond = " SITE_LEVEL = 2 and PV_CODE = '$TEMP_PROVINCE_CODE' ";
			}
		}else{
			$cond = " SITE_LEVEL = $R_CTRL_TYPE and ORG_ID = $TEMP_ORG_ID ";
		}

		$cmd = " select css_name from SITE_INFO where $cond ";
		$db->send_cmd($cmd);
//		$db->show_error();
//		echo "$R_CTRL_TYPE-$cmd<br />";
		if ($data = $db->get_array()) {
			$f_end = true;
        } else {
           	if ($R_CTRL_TYPE > 0) { 
       			$R_CTRL_TYPE = $R_CTRL_TYPE - 1;
				if($R_CTRL_TYPE==6) $TEMP_ORG_ID = $SESS_ORG_ID_1;
	            elseif($R_CTRL_TYPE==5) $TEMP_ORG_ID = $SESS_ORG_ID;
				elseif($R_CTRL_TYPE==4) $TEMP_ORG_ID = $SESS_DEPARTMENT_ID;
				elseif($R_CTRL_TYPE==3) $TEMP_ORG_ID = $SESS_MINISTRY_ID;
				elseif($R_CTRL_TYPE==2) $TEMP_ORG_ID = $SESS_PROVINCE_CODE;
				elseif($R_CTRL_TYPE==1) $TEMP_ORG_ID = "";
			} // if ($R_CTRL_TYPE > 0)
		} // if ($data = $db->get_array())
	} // end while loop

	if ($f_end) {
		$data = array_change_key_case($data, CASE_LOWER);
        $css_name = trim($data[css_name]);
//		$cssfileselected = "stylesheets/".substr($css_name,0,5).$RPT_N.substr($css_name,5);	// style+$RPT_N+ที่เหลือ
		$cssfileselected = "stylesheets/".$css_name;	// ไม่ใกฆ $RPT_N
    } else {
		$cmd = " select css_name from SITE_INFO where SITE_LEVEL = 1 ";
		$db->send_cmd($cmd);
//		$db->show_error();
//		echo "$cmd<br />";
		if ($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
	        $css_name = trim($data[css_name]);
//	        $cssfileselected = "stylesheets/".substr($css_name,0,5).$RPT_N.substr($css_name,5);	// style+$RPT_N+ที่เหลือ
	        $cssfileselected = "stylesheets/".$css_name;		// 	ไม่ใกฆ $RPT_N
		}  else {
//			$cssfileselected = "stylesheets/style".$RPT_N.".css";	//$cssfileselected = "stylesheets/style.css";	// style Default
			$cssfileselected = "stylesheets/style.css";	// style Default
		}
    }

	//echo "l=$top_left,bg=$top_bg,r=$top_right,css=$cssfileselected,head=$headtext_t<br />";
// echo "[CTRL_TYPE = $CTRL_TYPE]^[SESS_USERGROUP_LEVEL = $SESS_USERGROUP_LEVEL]^[TEMP_ORG_ID=$TEMP_ORG_ID]^[# $SESS_ORG_STRUCTURE] = 2..$SESS_PER_ID | $SESS_USERGROUP_LEVEL VS $CTRL_TYPE :2: $PROVINCE_CODE => $PROVINCE_NAME ,3 (MIN) : $MINISTRY_ID => $MINISTRY_NAME ,4 (DEF): $DEPARTMENT_ID => $DEPARTMENT_NAME ,5 (ORG) : $ORG_ID => $ORG_NAME ,6 (ORG1) : $ORG_ID_1 => $ORG_NAME_1<br>";
	// =================================== PER_CONTROL ==================================//	
?>