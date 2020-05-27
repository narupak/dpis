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
		$cmd = " update PER_POSTING set AUDIT_FLAG = 'N' where POST_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_POSTING set AUDIT_FLAG = 'Y' where POST_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$POST_STARTDATE = save_date($POST_STARTDATE); 
		$POST_ENDDATE = save_date($POST_ENDDATE); 
		$POST_DOCDATE = save_date($POST_DOCDATE); 
		
		if($DPISDB=="oci8")
			$cmd2 = " select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID='$PER_ID' and  substr(POST_STARTDATE,0,10)='$POST_STARTDATE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID=$PER_ID and  LEFT(POST_STARTDATE,10)='$POST_STARTDATE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID='$PER_ID' and  substr(POST_STARTDATE,0,10)='$POST_STARTDATE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่ออกประจำการ ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(POST_ID) as max_id from PER_POSTING ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POST_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_POSTING (POST_ID, PER_ID, PER_CARDNO, POST_STARTDATE, POST_ENDDATE, POST_ENDTIME, 
							POST_POSITION, POST_ORG_NAME, POST_TEL, POST_DOCNO, POST_DOCDATE, POST_REMARK, UPDATE_USER, UPDATE_DATE)
							values ($POST_ID, $PER_ID, '$PER_CARDNO', '$POST_STARTDATE', '$POST_ENDDATE', '$POST_ENDTIME', 
							'$POST_POSITION', '$POST_ORG_NAME', '$POST_TEL', '$POST_DOCNO', '$POST_DOCDATE', '$POST_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการออกประจำการ [$PER_ID : $POST_ID : $POST_ORG_NAME]");
			$ADD_NEXT = 1;
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $POST_ID){
		$POST_STARTDATE = save_date($POST_STARTDATE); 
		$POST_ENDDATE = save_date($POST_ENDDATE); 
		$POST_DOCDATE = save_date($POST_DOCDATE); 
		
		if($DPISDB=="oci8")    
			$cmd2 = " select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID='$PER_ID' and  substr(POST_STARTDATE,0,10)='$POST_STARTDATE' and POST_ID<>$POST_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID=$PER_ID and  LEFT(POST_STARTDATE,10)='$POST_STARTDATE' and POST_ID<>$POST_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select POST_STARTDATE,POST_ENDDATE from PER_POSTING  
								where PER_ID='$PER_ID' and  substr(POST_STARTDATE,0,10)='$POST_STARTDATE' and POST_ID<>$POST_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่ออกประจำการ ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_POSTING SET
							PER_CARDNO='$PER_CARDNO', 
							POST_STARTDATE='$POST_STARTDATE', 
							POST_ENDDATE='$POST_ENDDATE', 
							POST_ENDTIME='$POST_ENDTIME', 
							POST_POSITION='$POST_POSITION', 
							POST_ORG_NAME='$POST_ORG_NAME', 
							POST_TEL='$POST_TEL', 
							POST_DOCNO='$POST_DOCNO', 
							POST_DOCDATE='$POST_DOCDATE', 
							POST_REMARK='$POST_REMARK', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE POST_ID=$POST_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการออกประจำการ [$PER_ID : $POST_ID : $POST_ORG_NAME]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $POST_ID){
		$cmd = " select POST_ORG_NAME from PER_POSTING where POST_ID=$POST_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POST_ORG_NAME = $data[POST_ORG_NAME];
		
		$cmd = " delete from PER_POSTING where POST_ID=$POST_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการออกประจำการ [$PER_ID : $POST_ID : $POST_ORG_NAME]");
	} // end if

	if(($UPD && $PER_ID && $POST_ID) || ($VIEW && $PER_ID && $POST_ID)){
		$cmd = " select	POST_STARTDATE, POST_ENDDATE, POST_ENDTIME, POST_POSITION, POST_ORG_NAME, 
										POST_TEL, POST_DOCNO, POST_DOCDATE, POST_REMARK, UPDATE_USER, UPDATE_DATE
						from		PER_POSTING
						where	POST_ID=$POST_ID  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POST_STARTDATE = show_date_format(trim($data[POST_STARTDATE]), 1);
		$POST_ENDDATE = show_date_format(trim($data[POST_ENDDATE]), 1);
		$POST_ENDTIME = $data[POST_ENDTIME];
		$POST_POSITION = $data[POST_POSITION];
		$POST_ORG_NAME = $data[POST_ORG_NAME];
		$POST_TEL = $data[POST_TEL];
		$POST_DOCNO = $data[POST_DOCNO];
		$POST_DOCDATE = show_date_format(trim($data[POST_DOCDATE]), 1);
		$POST_REMARK = $data[POST_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($POST_ID);
		unset($POST_STARTDATE);
		unset($POST_ENDDATE);
		unset($POST_ENDTIME);
		unset($POST_POSITION);
		unset($POST_ORG_NAME);
		unset($POST_TEL);
		unset($POST_DOCNO);
		unset($POST_DOCDATE);
		unset($POST_REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>