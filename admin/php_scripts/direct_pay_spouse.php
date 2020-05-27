<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	$cmd = " select 	a.ORG_ID, b.ORG_NAME
					 from 	PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE" && $PER_ID && $MAH_ID){
		$PER_BIRTHDATE =  save_date($PER_BIRTHDATE);
		$DOC_DATE =  save_date($DOC_DATE);

		$cmd = " update PER_MARRHIS  set
								MAH_NAME = trim('$MAH_NAME'), 
								MAH_MARRY_DATE = '$DOC_DATE'
						 where PER_ID=$PER_ID and MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		
		$PER_GENDER += 0;
		$PER_ALIVE += 0;
		$DOC_TYPE += 0;
		
		$cmd = " select PER_ID, MAH_ID from PER_MARRHIS_PLUS where PER_ID=$PER_ID and MAH_ID=$MAH_ID ";
		$COUNT_SPOUSE = $db_dpis->send_cmd($cmd);
		if($COUNT_SPOUSE){
			$cmd = " update PER_MARRHIS_PLUS  set
									PER_CARDNO = trim('$PER_CARDNO'), 
									PER_GENDER = $PER_GENDER,
									PER_BIRTHDATE = '$PER_BIRTHDATE',
									PER_ALIVE = $PER_ALIVE,
									RE_CODE = trim('$RE_CODE'), 
									OC_CODE = trim('$OC_CODE'), 
									OC_OTHER = trim('$OC_OTHER'), 
									MR_CODE = trim('$MR_CODE'), 
									DOC_TYPE = $DOC_TYPE,
									DOC_NO = trim('$DOC_NO'), 
									DOC_PV_CODE = trim('$DOC_PV_CODE'), 
									PV_CODE = trim('$PV_CODE'), 
									POST_CODE = trim('$POST_CODE'), 
								UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
							 where PER_ID=$PER_ID and MAH_ID=$MAH_ID
						  ";
		}else{
			$cmd = " insert into PER_MARRHIS_PLUS
								(PER_ID, MAH_ID, PER_CARDNO, PER_GENDER, PER_BIRTHDATE, PER_ALIVE, RE_CODE, OC_CODE, OC_OTHER,
								 MR_CODE, DOC_TYPE, DOC_NO, DOC_PV_CODE, PV_CODE, POST_CODE, UPDATE_USER, UPDATE_DATE)
							 values
							 	($PER_ID, $MAH_ID, '$PER_CARDNO', $PER_GENDER, '$PER_BIRTHDATE', $PER_ALIVE, '$RE_CODE', '$OC_CODE', '$OC_OTHER',
								 '$MR_CODE', $DOC_TYPE, '$DOC_NO', '$DOC_PV_CODE', '$PV_CODE', '$POST_CODE', $SESS_USERID, '$UPDATE_DATE')
						   ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลคู่สมรส [ $PER_ID : $MAH_NAME ]");
	} // end if
	
	if($PER_ID){
		$cmd = " select		PER_ID, PER_GENDER, MR_CODE
						 from		PER_PERSONAL 
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_GENDER = (($data[PER_GENDER]==1)?2:1);
		$MR_CODE = trim($data[MR_CODE]);
		
		$cmd = " select 	MAH_ID, PER_CARDNO, PER_BIRTHDATE, PER_ALIVE, RE_CODE, OC_CODE, OC_OTHER, 
										DOC_TYPE, DOC_NO, DOC_PV_CODE, PV_CODE, POST_CODE
						 from		PER_MARRHIS_PLUS
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MAH_ID = trim($data[MAH_ID]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
		$PER_ALIVE = trim($data[PER_ALIVE]);
		$RE_CODE = trim($data[RE_CODE]);
		$OC_CODE = trim($data[OC_CODE]);
		$OC_OTHER = trim($data[OC_OTHER]);
	
		$DOC_TYPE = trim($data[DOC_TYPE]);
		$DOC_NO = trim($data[DOC_NO]);
		$DOC_PV_CODE = trim($data[DOC_PV_CODE]);
		$PV_CODE = trim($data[PV_CODE]);
		$POST_CODE = trim($data[POST_CODE]);
			
		$cmd = " select MAH_NAME, MAH_MARRY_DATE from PER_MARRHIS where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAH_NAME = trim($data[MAH_NAME]);
		$DOC_DATE = show_date_format($data[MAH_MARRY_DATE], 1);

		$cmd = " select OC_NAME from PER_OCCUPATION where trim(OC_CODE)='$OC_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OC_NAME = $data[OC_NAME];
	
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$MR_DOC_PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DOC_PV_NAME = $data[PV_NAME];
	
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PV_NAME = $data[PV_NAME];
	} 	// 	if($PER_ID){
	
?>