<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="REORDER"){
		foreach($ARR_CHILD_ORDER as $CHILD_ID => $CHILD_SEQ){
			$cmd = " update PER_CHILD set CHILD_SEQ='$CHILD_SEQ' where CHILD_ID=$CHILD_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับบุตร [$PER_ID]");
	} // end if

	$PER_GENDER += 0;
	$PER_ALIVE += 0;
	$CHILD_TYPE += 0;
	$DOC_TYPE += 0;
	$MR_DOC_TYPE += 0;
	$PER_INCOMPETENT += 0;
	$IN_DOC_TYPE += 0;

	$PER_BIRTHDATE =  save_date($PER_BIRTHDATE);
	$DOC_DATE =  save_date($DOC_DATE);
	$MR_DOC_DATE =  save_date($MR_DOC_DATE);
	$IN_DOC_DATE =  save_date($IN_DOC_DATE);

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(CHILD_ID) as max_id from PER_CHILD ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CHILD_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_CHILD
							(CHILD_ID, PER_ID, CHILD_SEQ, PN_CODE, PER_NAME, PER_SURNAME, PER_CARDNO, PER_GENDER, PER_BIRTHDATE,
							 PER_ALIVE, RE_CODE, OC_CODE, OC_OTHER, CHILD_TYPE, CHILD_TYPE_OTHER, DOC_TYPE, DOC_NO, DOC_DATE,
							 MR_CODE, MR_DOC_TYPE, MR_DOC_NO, MR_DOC_DATE, PV_CODE, POST_CODE,
							 PER_INCOMPETENT, IN_DOC_TYPE, IN_DOC_NO, IN_DOC_DATE, UPDATE_USER, UPDATE_DATE)
						 values
						 	($CHILD_ID, $PER_ID, '$CHILD_SEQ', '$PN_CODE', '$PER_NAME', '$PER_SURNAME',  '$PER_CARDNO', $PER_GENDER, '$PER_BIRTHDATE', 
							 $PER_ALIVE, '$RE_CODE', '$OC_CODE', '$OC_OTHER', $CHILD_TYPE, '$CHILD_TYPE_OTHER', $DOC_TYPE, '$DOC_NO', '$DOC_DATE',
							 '$MR_CODE', $MR_DOC_TYPE, '$MR_DOC_NO', '$MR_DOC_DATE', '$PV_CODE', '$POST_CODE',
							 $PER_INCOMPETENT, $IN_DOC_TYPE, '$IN_DOC_NO', '$IN_DOC_DATE', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบุตร [$PER_ID : $CHILD_ID : $CHILD_SEQ : $PER_NAME $PER_SURNAME]");
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $CHILD_ID){
		$cmd = " update PER_CHILD set
							CHILD_SEQ = '$CHILD_SEQ', 
							PN_CODE = '$PN_CODE',
							PER_NAME = '$PER_NAME',
							PER_SURNAME = '$PER_SURNAME',
							PER_CARDNO = '$PER_CARDNO',
							PER_GENDER = $PER_GENDER,
							PER_BIRTHDATE = '$PER_BIRTHDATE',
							PER_ALIVE = $PER_ALIVE,
							RE_CODE = '$RE_CODE',
							OC_CODE = '$OC_CODE',
							OC_OTHER = '$OC_OTHER',
							CHILD_TYPE = $CHILD_TYPE,
							CHILD_TYPE_OTHER = '$CHILD_TYPE_OTHER',
							DOC_TYPE = $DOC_TYPE,
							DOC_NO = '$DOC_NO',
							DOC_DATE = '$DOC_DATE',
							MR_CODE = '$MR_CODE',
							MR_DOC_TYPE = $MR_DOC_TYPE,
							MR_DOC_NO = '$MR_DOC_NO',
							MR_DOC_DATE = '$MR_DOC_DATE',
							PV_CODE = '$PV_CODE',
							POST_CODE = '$POST_CODE',
							PER_INCOMPETENT = $PER_INCOMPETENT,
							IN_DOC_TYPE = $IN_DOC_TYPE,
							IN_DOC_NO = '$IN_DOC_NO',
							IN_DOC_DATE = '$IN_DOC_DATE',
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						where PER_ID=$PER_ID and CHILD_ID=$CHILD_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบุตร [$PER_ID : $CHILD_SEQ : $PER_NAME $PER_SURNAME]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $CHILD_ID){		
		$cmd = " select CHILD_SEQ, PER_NAME, PER_SURNAME from PER_CHILD where PER_ID=$PER_ID and CHILD_ID=$CHILD_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CHILD_SEQ = $data[CHILD_SEQ];
		$CHILD_NAME = $data[PER_NAME] ." ". $data[PER_SURNAME];
		
		$cmd = " delete from PER_CHILD where PER_ID=$PER_ID and CHILD_ID=$CHILD_ID ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบุตร [$PER_ID : $CHILD_ID : $CHILD_SEQ : $CHILD_NAME]");
	} // end if

	if(($UPD && $PER_ID && $CHILD_ID) || ($VIEW && $PER_ID && $CHILD_ID)){
		$cmd = "	select		CHILD_SEQ, PN_CODE, PER_NAME, PER_SURNAME, PER_CARDNO, PER_GENDER, PER_BIRTHDATE, PER_ALIVE,
											RE_CODE, OC_CODE, OC_OTHER, CHILD_TYPE, CHILD_TYPE_OTHER, DOC_TYPE, DOC_NO, DOC_DATE,
											MR_CODE, MR_DOC_TYPE, MR_DOC_NO, MR_DOC_DATE, PV_CODE, POST_CODE,
											PER_INCOMPETENT, IN_DOC_TYPE, IN_DOC_NO, IN_DOC_DATE
							from		PER_CHILD
							where		PER_ID=$PER_ID and CHILD_ID=$CHILD_ID ";	
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();		
		$CHILD_SEQ = $data[CHILD_SEQ];
		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_GENDER = $data[PER_GENDER];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);

		$PER_ALIVE = $data[PER_ALIVE];
		$RE_CODE = $data[RE_CODE];
		$OC_CODE = $data[OC_CODE];
		$OC_OTHER = $data[OC_OTHER];
		$CHILD_TYPE = $data[CHILD_TYPE];
		if($CHILD_TYPE==4) $CHILD_TYPE_OTHER = $data[CHILD_TYPE_OTHER];
		$DOC_TYPE = $data[DOC_TYPE];
		$DOC_NO = $data[DOC_NO];
		$DOC_DATE = show_date_format($data[DOC_DATE], 1);

		$MR_CODE = $data[MR_CODE];
		$MR_DOC_TYPE = $data[MR_DOC_TYPE];
		$MR_DOC_NO = $data[MR_DOC_NO];
		$MR_DOC_DATE = show_date_format($data[MR_DOC_DATE], 1);

		$PV_CODE = trim($data[PV_CODE]);
		$POST_CODE= $data[POST_CODE];
		$PER_INCOMPETENT = $data[PER_INCOMPETENT];
		$IN_DOC_TYPE = $data[IN_DOC_TYPE];
		$IN_DOC_NO = $data[IN_DOC_NO];
		$IN_DOC_DATE = show_date_format($data[IN_DOC_DATE], 1);

		if($PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
		} // end if

		if($OC_CODE){
			$cmd = " select OC_NAME from PER_OCCUPATION where trim(OC_CODE)='$OC_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$OC_NAME = $data[OC_NAME];
		} // end if

		if($PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PV_NAME = $data[PV_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($CHILD_ID);
		unset($CHILD_SEQ);
		unset($PN_CODE);
		unset($PN_NAME);
		unset($PER_NAME);
		unset($PER_SURNAME);
		unset($PER_CARDNO);
		unset($PER_GENDER);
		unset($PER_BIRTHDATE);
		unset($PER_ALIVE);
		unset($RE_CODE);
		unset($OC_CODE);
		unset($OC_NAME);
		unset($OC_OTHER);
		unset($CHILD_TYPE);
		unset($CHILD_TYPE_OTHER);
		unset($DOC_TYPE);
		unset($DOC_NO);
		unset($DOC_DATE);
		unset($MR_CODE);
		unset($MR_DOC_TYPE);
		unset($MR_DOC_NO);
		unset($MR_DOC_DATE);
		unset($PV_CODE);
		unset($PV_NAME);
		unset($POST_CODE);
		unset($PER_INCOMPETENT);
		unset($IN_DOC_TYPE);
		unset($IN_DOC_NO);
		unset($IN_DOC_DATE);
	} // end if
?>