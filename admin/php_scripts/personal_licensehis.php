<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
		
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
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_LICENSEHIS set AUDIT_FLAG = 'N' where LH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_LICENSEHIS set AUDIT_FLAG = 'Y' where LH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$LH_LICENSE_DATE = save_date($LH_LICENSE_DATE); 
		$LH_EXPIRE_DATE = save_date($LH_EXPIRE_DATE); 
		
		if($DPISDB=="oci8")
			$cmd2 = " select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID='$PER_ID' and  substr(LH_LICENSE_DATE,0,10)='$LH_LICENSE_DATE' and 
								substr(LH_EXPIRE_DATE,0,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID=$PER_ID and  LEFT(LH_LICENSE_DATE,10)='$LH_LICENSE_DATE' and 
								LEFT(LH_EXPIRE_DATE,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID='$PER_ID' and  substr(LH_LICENSE_DATE,0,10)='$LH_LICENSE_DATE' and 
								substr(LH_EXPIRE_DATE,0,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่ออกใบอนุญาต -วันที่หมดอายุ และใบอนุญาต ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(LH_ID) as max_id from PER_LICENSEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$LH_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_LICENSEHIS	(LH_ID, PER_ID, PER_CARDNO, LT_CODE, LH_SUB_TYPE, LH_LICENSE_NO, 
							LH_LICENSE_DATE, LH_EXPIRE_DATE, LH_SEQ_NO, LH_REMARK, LH_MAJOR, UPDATE_USER, UPDATE_DATE)
							values ($LH_ID, $PER_ID, '$PER_CARDNO', '$LT_CODE', '$LH_SUB_TYPE', '$LH_LICENSE_NO', '$LH_LICENSE_DATE', 
							'$LH_EXPIRE_DATE', '$LH_SEQ_NO', '$LH_REMARK', '$LH_MAJOR', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติใบอนุญาตประกอบวิชาชีพ [$PER_ID : $LH_ID : $LT_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $LH_ID){
		$LH_LICENSE_DATE = save_date($LH_LICENSE_DATE); 
		$LH_EXPIRE_DATE = save_date($LH_EXPIRE_DATE); 
		
		if($DPISDB=="oci8")    
			$cmd2 = " select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID='$PER_ID' and  substr(LH_LICENSE_DATE,0,10)='$LH_LICENSE_DATE' and 
								substr(LH_EXPIRE_DATE,0,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' and LH_ID<>$LH_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID=$PER_ID and  LEFT(LH_LICENSE_DATE,10)='$LH_LICENSE_DATE' and 
								LEFT(LH_EXPIRE_DATE,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' and LH_ID<>$LH_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select LH_LICENSE_DATE,LH_EXPIRE_DATE from PER_LICENSEHIS  
								where PER_ID='$PER_ID' and  substr(LH_LICENSE_DATE,0,10)='$LH_LICENSE_DATE' and 
								substr(LH_EXPIRE_DATE,0,10)='$LH_EXPIRE_DATE'  and LT_CODE='$LT_CODE' and LH_ID<>$LH_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่ออกใบอนุญาต -วันที่หมดอายุ และใบอนุญาต ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_LICENSEHIS SET
							PER_CARDNO='$PER_CARDNO', 
							LT_CODE='$LT_CODE', 
							LH_SUB_TYPE='$LH_SUB_TYPE', 
							LH_LICENSE_NO='$LH_LICENSE_NO', 
							LH_LICENSE_DATE='$LH_LICENSE_DATE', 
							LH_EXPIRE_DATE='$LH_EXPIRE_DATE', 
							LH_SEQ_NO='$LH_SEQ_NO', 
							LH_REMARK='$LH_REMARK', 
							LH_MAJOR='$LH_MAJOR', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE LH_ID=$LH_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติใบอนุญาตประกอบวิชาชีพ [$PER_ID : $LH_ID : $LT_CODE]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $LH_ID){
		$cmd = " select LT_CODE from PER_LICENSEHIS where LH_ID=$LH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LT_CODE = $data[LT_CODE];
		
		$cmd = " delete from PER_LICENSEHIS where LH_ID=$LH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติใบอนุญาตประกอบวิชาชีพ [$PER_ID : $LH_ID : $LT_CODE]");
	} // end if

	if(($UPD && $PER_ID && $LH_ID) || ($VIEW && $PER_ID && $LH_ID)){
		$cmd = " select	LT_CODE, LH_SUB_TYPE, LH_LICENSE_NO, LH_LICENSE_DATE, LH_EXPIRE_DATE, 
										LH_SEQ_NO, LH_REMARK, LH_MAJOR, UPDATE_USER, UPDATE_DATE 
						from		PER_LICENSEHIS
						where	LH_ID=$LH_ID  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LH_SUB_TYPE = $data[LH_SUB_TYPE];
		$LH_LICENSE_DATE = show_date_format(trim($data[LH_LICENSE_DATE]), 1);
		$LH_EXPIRE_DATE = show_date_format(trim($data[LH_EXPIRE_DATE]), 1);
		$LH_LICENSE_NO = $data[LH_LICENSE_NO];
		$LH_SEQ_NO = $data[LH_SEQ_NO];
		$LH_REMARK = $data[LH_REMARK];
		$LH_MAJOR = $data[LH_MAJOR];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$LT_CODE = $data[LT_CODE];
		if($LT_CODE){
			$cmd = " select LT_NAME from PER_LICENSE_TYPE where LT_CODE='$LT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LT_NAME = $data2[LT_NAME];
		} // end if

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($LH_ID);
		unset($LH_SEQ_NO);		
		unset($LH_LICENSE_DATE);
		unset($LH_EXPIRE_DATE);
		unset($LH_LICENSE_NO);
		unset($LH_REMARK);
		unset($LH_MAJOR);
		unset($LH_SUB_TYPE);

		unset($LT_CODE);
		unset($LT_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>