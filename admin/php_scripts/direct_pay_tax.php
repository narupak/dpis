<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		
/*
	$cmd = " select 	a.ORG_ID, b.ORG_NAME
					 from 	PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE" && $PER_ID){
			$cmd = " update PER_PERSONAL  set
									PN_CODE_F = trim('$PN_CODE'), 
									PER_FATHERNAME = trim('$PER_NAME'), 
									PER_FATHERSURNAME = trim('$PER_SURNAME')
							 where PER_ID=$PER_ID
						  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล".(($PER_GENDER==1)?"บิดา":"มารดา")." [ $PER_ID : $PER_GENDER $PER_NAME $PER_SURNAME ]");
	} // end if
	
	if($PER_ID){
		$cmd = " select		PER_ID, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME
						 from		PER_PERSONAL 
						 where	PER_ID=$PER_ID
					   ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($PER_GENDER==1){
			$PN_CODE = trim($data[PN_CODE_F]);
			$PER_NAME = trim($data[PER_FATHERNAME]);
			$PER_SURNAME = trim($data[PER_FATHERSURNAME]);		
		}elseif($PER_GENDER==2){
			$PN_CODE = trim($data[PN_CODE_M]);
			$PER_NAME = trim($data[PER_MOTHERNAME]);
			$PER_SURNAME = trim($data[PER_MOTHERSURNAME]);		
		} // end if

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
		
		$cmd = " select PER_ID, PER_GENDER from PER_PARENT where PER_ID=$PER_ID and PER_GENDER=$PER_GENDER ";
		$COUNT_PARENT = $db_dpis->send_cmd($cmd);
		if(!$COUNT_PARENT){
			$cmd = " insert into PER_PARENT (PER_ID, PER_GENDER, UPDATE_USER, UPDATE_DATE) values ($PER_ID, $PER_GENDER, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
		} // end if
		
		$cmd = " select 	PER_CARDNO, PER_BIRTHDATE, PER_ALIVE, RE_CODE, OC_CODE, OC_OTHER, 
										PARENT_TYPE, DOC_TYPE, DOC_NO, DOC_DATE,
										MR_CODE, MR_DOC_TYPE, MR_DOC_NO, MR_DOC_DATE, MR_DOC_PV_CODE,
										PV_CODE, POST_CODE
						 from		PER_PARENT
						 where	PER_ID=$PER_ID and PER_GENDER=$PER_GENDER
					   ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
			$PER_BIRTHDATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		$PER_ALIVE = trim($data[PER_ALIVE]);
		$RE_CODE = trim($data[RE_CODE]);
		$OC_CODE = trim($data[OC_CODE]);
		$OC_OTHER = trim($data[OC_OTHER]);

		$PARENT_TYPE = trim($data[PARENT_TYPE]);
		$DOC_TYPE = trim($data[DOC_TYPE]);
		$DOC_NO = trim($data[DOC_NO]);
		$DOC_DATE = trim($data[DOC_DATE]);
		if($DOC_DATE){
			$arr_temp = explode("-", substr($DOC_DATE, 0, 10));
			$DOC_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$MR_CODE = trim($data[MR_CODE]);
		$MR_DOC_TYPE = trim($data[MR_DOC_TYPE]);
		$MR_DOC_NO = trim($data[MR_DOC_NO]);
		$MR_DOC_DATE = trim($data[MR_DOC_DATE]);
		if($MR_DOC_DATE){
			$arr_temp = explode("-", substr($MR_DOC_DATE, 0, 10));
			$MR_DOC_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		$MR_DOC_PV_CODE = trim($data[MR_DOC_PV_CODE]);

		$PV_CODE = trim($data[PV_CODE]);
		$POST_CODE = trim($data[POST_CODE]);
		
		$cmd = " select OC_NAME from PER_OCCUPATION where trim(OC_CODE)='$OC_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OC_NAME = $data[OC_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$MR_DOC_PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MR_DOC_PV_NAME = $data[PV_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PV_NAME = $data[PV_NAME];
	} 	// 	if($PER_ID){
*/	
?>