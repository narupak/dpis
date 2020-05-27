<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
//	echo "FML_TYPE=$FML_TYPE<br>";
	if ($FML_TYPE==1) {	// บิดา
		$FML_BY = $FML_BY1;
		$FML_DOCNO = $FML_DOCNO1;
		$FML_DOCTYPE = $FML_DOCTYPE1;
		$FML_DOCDATE = $FML_DOCDATE1;
	} else if ($FML_TYPE==2) {	// มารดา
		$FML_BY = $FML_BY2;
		$FML_DOCNO = $FML_DOCNO2;
		$FML_DOCTYPE = $FML_DOCTYPE2;
		$FML_DOCDATE = $FML_DOCDATE2;
	} else if ($FML_TYPE==3) {	//  คู่ สมรส
		$FML_BY = $FML_BY3;
		$FML_DOCNO = $FML_DOCNO3;
		$FML_DOCTYPE = $FML_DOCTYPE3;
		$FML_DOCDATE = $FML_DOCDATE3;
	} else {	// บุตร
		$FML_BY = $FML_BY4;
		$FML_DOCNO = $FML_DOCNO4;
		$FML_DOCTYPE = $FML_DOCTYPE4;
		$FML_DOCDATE = $FML_DOCDATE4;
	}
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO])?trim($data[PER_CARDNO]):"NULL";				//$PER_CARDNO = (trim($data[PER_CARDNO]))? "'".trim($data[PER_CARDNO])."'" : "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	if($command=="REORDER"){
			foreach($ARR_FAMILY_ORDER as $FML_ID => $FML_SEQ){
			$cmd = " update PER_FAMILY set FML_SEQ='$FML_SEQ' where FML_ID=$FML_ID ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับครอบครัว [$FML_ID]");
	} // end if

	$FML_SEQ += 0;
	$FML_GENDER += 0;
	$FML_ALIVE += 0;
	$FML_BY += 0;
	$FML_DOCTYPE += 0;
	$MR_DOCTYPE += 0;
	$FML_INCOMPETENT += 0;
	$IN_DOCTYPE += 0;

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_FAMILY set AUDIT_FLAG = 'N' where FML_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_FAMILY set AUDIT_FLAG = 'Y' where FML_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$FML_BIRTHDATE = save_date($FML_BIRTHDATE); 
		$FML_DOCDATE = save_date($FML_DOCDATE); 
		$MR_DOCDATE = save_date($MR_DOCDATE); 
		$IN_DOCDATE = save_date($IN_DOCDATE); 

		$cmd = " select max(FML_ID) as max_id from PER_FAMILY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$FML_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_SEQ, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, 
						  FML_CARDNO, FML_GENDER, FML_BIRTHDATE, FML_ALIVE, RE_CODE, OC_CODE, OC_OTHER, FML_BY, 
						  FML_BY_OTHER, FML_DOCTYPE, FML_DOCNO, FML_DOCDATE, MR_CODE, MR_DOCTYPE, MR_DOCNO, 
						  MR_DOCDATE, MR_DOC_PV_CODE, PV_CODE, POST_CODE, FML_INCOMPETENT, IN_DOCTYPE, IN_DOCNO, 
						  IN_DOCDATE, UPDATE_USER, UPDATE_DATE, FML_OLD_SURNAME)
						  values	($FML_ID, $PER_ID, $FML_SEQ, $FML_TYPE, '$PN_CODE', '$FML_NAME', '$FML_SURNAME',  
						  '$FML_CARDNO', $FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$OC_CODE', '$OC_OTHER', 
						  $FML_BY, '$FML_BY_OTHER', $FML_DOCTYPE, '$FML_DOCNO', '$FML_DOCDATE', '$MR_CODE', 
						  $MR_DOCTYPE, '$MR_DOCNO', '$MR_DOCDATE', '$MR_DOC_PV_CODE', '$PV_CODE', '$POST_CODE',
						  $FML_INCOMPETENT, $IN_DOCTYPE, '$IN_DOCNO', '$IN_DOCDATE', $SESS_USERID, '$UPDATE_DATE', '$FML_OLD_SURNAME') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลครอบครัว [$PER_ID : $FML_ID : $FML_SEQ : $FML_NAME $FML_SURNAME]");
		$ADD_NEXT = 1;
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $FML_ID){
		$FML_BIRTHDATE = save_date($FML_BIRTHDATE); 
		$FML_DOCDATE = save_date($FML_DOCDATE); 
		$MR_DOCDATE = save_date($MR_DOCDATE); 
		$IN_DOCDATE = save_date($IN_DOCDATE); 

		$cmd = " update PER_FAMILY set
							FML_SEQ = $FML_SEQ, 
							FML_TYPE = $FML_TYPE, 
							PN_CODE = '$PN_CODE',
							FML_NAME = '$FML_NAME',
							FML_SURNAME = '$FML_SURNAME',
							FML_CARDNO = '$FML_CARDNO',
							FML_GENDER = $FML_GENDER,
							FML_BIRTHDATE = '$FML_BIRTHDATE',
							FML_ALIVE = $FML_ALIVE,
							RE_CODE = '$RE_CODE',
							OC_CODE = '$OC_CODE',
							OC_OTHER = '$OC_OTHER',
							FML_BY = $FML_BY,
							FML_BY_OTHER = '$FML_BY_OTHER',
							FML_DOCTYPE = $FML_DOCTYPE,
							FML_DOCNO = '$FML_DOCNO',
							FML_DOCDATE = '$FML_DOCDATE',
							MR_CODE = '$MR_CODE',
							MR_DOCTYPE = $MR_DOCTYPE,
							MR_DOCNO = '$MR_DOCNO',
							MR_DOCDATE = '$MR_DOCDATE',
							MR_DOC_PV_CODE = '$MR_DOC_PV_CODE',
							PV_CODE = '$PV_CODE',
							POST_CODE = '$POST_CODE',
							FML_INCOMPETENT = $FML_INCOMPETENT,
							IN_DOCTYPE = $IN_DOCTYPE,
							IN_DOCNO = '$IN_DOCNO',
							IN_DOCDATE = '$IN_DOCDATE',
							FML_OLD_SURNAME = '$FML_OLD_SURNAME',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลครอบครัว [$PER_ID : $FML_SEQ : $FML_NAME $FML_SURNAME]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $FML_ID){		
		$cmd = " select FML_SEQ, FML_NAME, FML_SURNAME from PER_FAMILY where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$FML_SEQ = $data[FML_SEQ];
		$FAMILY_NAME = $data[FML_NAME] ." ". $data[FML_SURNAME];
		
		$cmd = " delete from PER_FAMILY where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลครอบครัว [$PER_ID : $FML_ID : $FML_SEQ : $FAMILY_NAME]");
	} // end if

	if($UPD || $VIEW){
		$cmd = "	select	FML_SEQ, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, FML_GENDER, FML_BIRTHDATE, 
											FML_ALIVE, RE_CODE, OC_CODE, OC_OTHER, FML_BY, FML_BY_OTHER, FML_DOCTYPE, FML_DOCNO, 
											FML_DOCDATE, MR_CODE, MR_DOCTYPE, MR_DOCNO, MR_DOCDATE, MR_DOC_PV_CODE, PV_CODE, 
											POST_CODE, FML_INCOMPETENT, IN_DOCTYPE, IN_DOCNO, IN_DOCDATE, FML_OLD_SURNAME, UPDATE_USER, UPDATE_DATE
							from		PER_FAMILY
							where	FML_ID=$FML_ID ";	
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();		
		$FML_SEQ = $data[FML_SEQ];
		$FML_TYPE = $data[FML_TYPE];
		$PN_CODE = trim($data[PN_CODE]);
		$FML_NAME = $data[FML_NAME];
		$FML_SURNAME = $data[FML_SURNAME];
		$FML_CARDNO = $data[FML_CARDNO];
		$FML_GENDER = $data[FML_GENDER];
		$FML_BIRTHDATE = show_date_format(trim($data[FML_BIRTHDATE]), 1);
		$FML_ALIVE = $data[FML_ALIVE];
		$RE_CODE = $data[RE_CODE];
		$OC_CODE = $data[OC_CODE];
		$OC_OTHER = $data[OC_OTHER];
		$FML_BY = $data[FML_BY];
		if($FML_BY==4) $FML_BY_OTHER = $data[FML_BY_OTHER];
		$FML_DOCTYPE = $data[FML_DOCTYPE];
		$FML_DOCNO = $data[FML_DOCNO];
		$FML_DOCDATE = show_date_format(trim($data[FML_DOCDATE]), 1);
		$MR_CODE = $data[MR_CODE];
		$MR_DOCTYPE = $data[MR_DOCTYPE];
		$MR_DOCNO = $data[MR_DOCNO];
		$MR_DOCDATE = show_date_format(trim($data[MR_DOCDATE]), 1);
		$MR_DOC_PV_CODE = trim($data[MR_DOC_PV_CODE]);
		$PV_CODE = trim($data[PV_CODE]);
		$POST_CODE= $data[POST_CODE];
		$FML_INCOMPETENT = $data[FML_INCOMPETENT];
		$IN_DOCTYPE = $data[IN_DOCTYPE];
		$IN_DOCNO = $data[IN_DOCNO];
		$IN_DOCDATE = show_date_format(trim($data[IN_DOCDATE]), 1);
		$FML_OLD_SURNAME = $data[FML_OLD_SURNAME];

		$FML_BY1 = "";
		$FML_DOCNO1 = "";
		$FML_DOCTYPE1 = "";
		$FML_DOCDATE1 = "";
		$FML_BY2 = "";
		$FML_DOCNO2 = "";
		$FML_DOCTYPE2 = "";
		$FML_DOCDATE2 = "";
		$FML_BY3 = "";
		$FML_DOCNO3 = "";
		$FML_DOCTYPE3 = "";
		$FML_DOCDATE3 = "";
		$FML_BY4 = "";
		$FML_DOCNO4 = "";
		$FML_DOCTYPE4 = "";
		$FML_DOCDATE4 = "";
		if ($FML_TYPE==1) {	// บิดา
			$FML_BY1 = $FML_BY;
			$FML_DOCNO1 = $FML_DOCNO;
			$FML_DOCTYPE1 = $FML_DOCTYPE;
			$FML_DOCDATE1 = $FML_DOCDATE;
		} else if ($FML_TYPE==2) {	// มารดา
			$FML_BY2 = $FML_BY;
			$FML_DOCNO2 = $FML_DOCNO;
			$FML_DOCTYPE2 = $FML_DOCTYPE;
			$FML_DOCDATE2 = $FML_DOCDATE;
		} else if ($FML_TYPE==3) {	//  คู่ สมรส
			$FML_BY3 = $FML_BY;
			$FML_DOCNO3 = $FML_DOCNO;
			$FML_DOCTYPE3 = $FML_DOCTYPE;
			$FML_DOCDATE3 = $FML_DOCDATE;
		} else {	// บุตร
			$FML_BY4 = $FML_BY;
			$FML_DOCNO4 = $FML_DOCNO;
			$FML_DOCTYPE4 = $FML_DOCTYPE;
			$FML_DOCDATE4 = $FML_DOCDATE;
		}

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		if($PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
		} // end if

		if($OC_CODE){
			$cmd = " select OC_NAME from PER_OCCUPATION where trim(OC_CODE)='$OC_CODE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$OC_NAME = $data[OC_NAME];
		} // end if

		if($PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); echo "<hr>";
			$data = $db_dpis->get_array();
			$PV_NAME = $data[PV_NAME];
			$PV_NAME2 = $data[PV_NAME];
			$PV_CODE2 = $PV_CODE;
		} // end if

		if($MR_DOC_PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$MR_DOC_PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo $cmd;
			$data = $db_dpis->get_array();
			$MR_DOC_PV_NAME = $data[PV_NAME];
			//echo "<hr>PV = $data[PV_NAME]";
						
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($FML_ID);
		unset($FML_SEQ);
		unset($PN_CODE);
		unset($PN_NAME);
		unset($FML_NAME);
		unset($FML_SURNAME);
		unset($FML_CARDNO);
		unset($FML_GENDER);
		unset($FML_BIRTHDATE);
		unset($FML_ALIVE);
		unset($RE_CODE);
		unset($OC_CODE);
		unset($OC_NAME);
		unset($OC_OTHER);
		unset($FML_BY);
		unset($FML_BY_OTHER);
		unset($FML_DOCTYPE);
		unset($FML_DOCNO);
		unset($FML_DOCDATE);
		unset($MR_CODE);
		unset($MR_DOCTYPE);
		unset($MR_DOCNO);
		unset($MR_DOCDATE);
		unset($MR_DOC_PV_CODE);
		unset($MR_DOC_PV_NAME);
		unset($PV_CODE);
		unset($PV_NAME);
		unset($POST_CODE);
		unset($FML_INCOMPETENT);
		unset($IN_DOCTYPE);
		unset($IN_DOCNO);
		unset($IN_DOCDATE);
		unset($FML_OLD_SURNAME);
	} // end if
?>