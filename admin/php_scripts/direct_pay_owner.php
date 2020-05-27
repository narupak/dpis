<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	$cmd = " select 	a.ORG_ID, b.ORG_NAME
					 from 	PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID
				   ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	
	if(!$PER_GENDER) $PER_GENDER = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE" && $PER_ID){
		if($PER_BIRTHDATE){
			$arr_temp = explode("/", $PER_BIRTHDATE);
			$PER_BIRTHDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($PER_STARTDATE){
			$arr_temp = explode("/", $PER_STARTDATE);
			$PER_STARTDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if

		$cmd = " update PER_PERSONAL  set
								PN_CODE = trim('$PN_CODE'), 
								PER_NAME = trim('$PER_NAME'), 
								PER_SURNAME = trim('$PER_SURNAME'), 
								PER_GENDER = $PER_GENDER, 
								MR_CODE = trim('$MR_CODE'), 
								PER_CARDNO = trim('$PER_CARDNO'), 
								RE_CODE = trim('$RE_CODE'), 
								PER_BIRTHDATE = '$PER_BIRTHDATE', 
								PER_STARTDATE = '$PER_STARTDATE', 
								PER_ADD2 = trim('$PER_ADD2'), 
								PER_MEMBER = $PER_MEMBER, 
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID=$PER_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ/ลูกจ้างประจำ [ $PER_ID : $PER_NAME $PER_SURNAME ]");
	} // end if
	
	if($PER_ID){
		$cmd = " select		PER_ID, PER_TYPE, PN_CODE, PER_NAME, PER_SURNAME, 
										PER_GENDER, MR_CODE, PER_CARDNO, RE_CODE, PER_BIRTHDATE, 
										PER_STARTDATE, PER_ADD2, PER_MEMBER 
						 from		PER_PERSONAL 
						 where	PER_ID=$PER_ID
					   ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_TYPE = trim($data[PER_TYPE]);

		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);		
		$PER_GENDER = trim($data[PER_GENDER]);
		$MR_CODE = trim($data[MR_CODE]);		
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$RE_CODE = trim($data[RE_CODE]);

		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
			$PER_BIRTHDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		
		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		if($PER_STARTDATE){
			$arr_temp = explode("-", substr($PER_STARTDATE, 0, 10));
			$PER_STARTDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$PER_ADD2 = trim($data[PER_ADD2]);		
		$PER_MEMBER = trim($data[PER_MEMBER]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
		
		$cmd = " select CHILD_ID from PER_CHILD where PER_ID=$PER_ID ";
		$COUNT_CHILD = $db_dpis->send_cmd($cmd);
		if(!$COUNT_CHILD){
			$cmd = " select HEIR_ID, HEIR_NAME, HEIR_BIRTHDAY from PER_HEIR where PER_ID=$PER_ID and HR_CODE='01' order by HEIR_BIRTHDAY ";
			$COUNT_CHILD = $db_dpis->send_cmd($cmd);
			$child_count = 0;
			while($data = $db_dpis->get_array()){
				$child_count++;
				list($HEIR_NAME, $HEIR_SURNAME) = split(" ", $data[HEIR_NAME], 2);
				$HEIR_BIRTHDAY = substr($data[HEIR_BIRTHDAY], 0, 10);
				
				$cmd = " select max(CHILD_ID) as max_id from PER_CHILD ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$CHILD_ID = $data2[max_id] + 1;
				$CHILD_SEQ = str_pad($child_count, 2, "0", STR_PAD_LEFT);				
				
				$cmd = " insert into PER_CHILD 
									(CHILD_ID, PER_ID, CHILD_SEQ, PER_NAME, PER_SURNAME, PER_BIRTHDATE, UPDATE_USER, UPDATE_DATE)
								 values
								 	($CHILD_ID, $PER_ID, '$CHILD_SEQ', '$HEIR_NAME', '$HEIR_SURNAME', '$HEIR_BIRTHDAY', $SESS_USERID, '$UPDATE_DATE')
							   ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end while
		} // end if
	} 	// 	if($PER_ID){
	
?>