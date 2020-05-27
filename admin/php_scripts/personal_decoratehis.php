<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
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

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$DEH_RECEIVE_FLAG) $DEH_RECEIVE_FLAG = "NULL";
	if (!$DEH_RETURN_FLAG) $DEH_RETURN_FLAG = "NULL";
	if (!$DEH_RETURN_TYPE) $DEH_RETURN_TYPE = "NULL";

		
		if($DEH_GAZETTE=="" && ($DEH_BOOK || $DEH_PART || $DEH_PAGE || $DEH_ORDER_DECOR)){
		if($DEH_ISSUE){
			if($DEH_ISSUE==1) $DEH_ISSUE_SHOW = "ฉบับทะเบียนฐานันดร";
			if($DEH_ISSUE==2) $DEH_ISSUE_SHOW = "ฉบับพิเศษ";
			$DEH_GAZETTE = "ราชกิจจานุเบกษา".$DEH_ISSUE_SHOW;
		}
		if($DEH_BOOK)	$DEH_GAZETTE .= " เล่ม".$DEH_BOOK;
		if($DEH_PART)		$DEH_GAZETTE .= " ตอนที่".$DEH_PART;
		if($DEH_PAGE)		$DEH_GAZETTE .= " หน้า".$DEH_PAGE;
		if($DEH_ORDER_DECOR)	$DEH_GAZETTE .= " ลำดับ".$DEH_ORDER_DECOR;
		if (!get_magic_quotes_gpc()) {
			$DEH_GAZETTE = addslashes(str_replace('"', "&quot;", trim($DEH_GAZETTE)));
			$DEH_REMARK = addslashes(str_replace('"', "&quot;", trim($DEH_REMARK)));
		}else{
			$DEH_GAZETTE = addslashes(str_replace('"', "&quot;", stripslashes(trim($DEH_GAZETTE))));
			$DEH_REMARK = addslashes(str_replace('"', "&quot;", stripslashes(trim($DEH_REMARK))));
		}
//		$DEH_GAZETTE = str_replace("'", "&rsquo;", $DEH_GAZETTE);
//		$DEH_REMARK = str_replace("'", "&rsquo;", $DEH_REMARK);
	}

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_DECORATEHIS set AUDIT_FLAG = 'N' where DEH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_DECORATEHIS set AUDIT_FLAG = 'Y' where DEH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$DEH_DATE = save_date($DEH_DATE); 
		$DEH_RETURN_DATE = save_date($DEH_RETURN_DATE); 
		$DEH_RECEIVE_DATE = save_date($DEH_RECEIVE_DATE); 
		$DEH_BOOK_DATE = save_date($DEH_BOOK_DATE); 
		
		if($DEH_RECEIVE_FLAG_1 === '0'){$DEH_RECEIVE_FLAG = 0;}
		if($DEH_RECEIVE_FLAG_2 === '1'){$DEH_RECEIVE_FLAG = 1;}
		
		if($DEH_RETURN_TYPE_1) $DEH_RETURN_TYPE = $DEH_RETURN_TYPE_1;	
		if($DEH_RETURN_TYPE_2) $DEH_RETURN_TYPE = $DEH_RETURN_TYPE_2;
		
		if($DEH_RETURN_FLAG_1) $DEH_RETURN_FLAG = $DEH_RETURN_FLAG_1;	
		if($DEH_RETURN_FLAG_2) $DEH_RETURN_FLAG = $DEH_RETURN_FLAG_2;
		
		$cmd2="select   DC_CODE, DEH_DATE from PER_DECORATEHIS where PER_ID=$PER_ID and DC_CODE='$DC_CODE' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count && $MFA_FLAG!=1) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ เครื่องราช/เหรียญตรา ซ้ำ !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(DEH_ID) as max_id from PER_DECORATEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$DEH_ID = $data[max_id] + 1;	
		
			$cmd = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
							DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG, DEH_ISSUE, 
							DEH_BOOK, DEH_PART, DEH_PAGE, DEH_ORDER_DECOR) 
							values	($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '".save_quote($DEH_GAZETTE)."', 
							$DEH_RECEIVE_FLAG, $DEH_RETURN_FLAG, '$DEH_RETURN_DATE', $DEH_RETURN_TYPE, 
							'$DEH_RECEIVE_DATE', '$DEH_BOOK_NO', '$DEH_BOOK_DATE', '".save_quote($DEH_REMARK)."', '$DEH_POSITION', '$DEH_ORG', '$DEH_ISSUE', 
							'$DEH_BOOK', '$DEH_PART', '$DEH_PAGE', '$DEH_ORDER_DECOR') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<pre>".$cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเครื่องราชฯ [$PER_ID : $DEH_ID : $DC_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $DEH_ID){
		$DEH_DATE = save_date($DEH_DATE); 
		$DEH_RETURN_DATE = save_date($DEH_RETURN_DATE); 
		$DEH_RECEIVE_DATE = save_date($DEH_RECEIVE_DATE); 
		$DEH_BOOK_DATE = save_date($DEH_BOOK_DATE);
		
		
		if($DEH_RECEIVE_FLAG_1 === '0'){$DEH_RECEIVE_FLAG = 0;}
		if($DEH_RECEIVE_FLAG_2 === '1'){$DEH_RECEIVE_FLAG = 1;}
		
		if($DEH_RETURN_TYPE_1) $DEH_RETURN_TYPE = $DEH_RETURN_TYPE_1;	
		if($DEH_RETURN_TYPE_2) $DEH_RETURN_TYPE = $DEH_RETURN_TYPE_2;
		
		if($DEH_RETURN_FLAG_1) $DEH_RETURN_FLAG = $DEH_RETURN_FLAG_1;	
		if($DEH_RETURN_FLAG_2) $DEH_RETURN_FLAG = $DEH_RETURN_FLAG_2;

		$cmd2="select   DC_CODE, DEH_DATE from PER_DECORATEHIS where PER_ID=$PER_ID and DC_CODE='$DC_CODE' and DEH_ID<>$DEH_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count && $MFA_FLAG!=1) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ เครื่องราช/เหรียญตรา ซ้ำ !!!");
				-->   </script>	<? 
		} else {		
			$cmd = "	update PER_DECORATEHIS set
							DC_CODE='$DC_CODE',  
							DEH_DATE='$DEH_DATE', 
							PER_CARDNO='$PER_CARDNO', 
							DEH_GAZETTE='$DEH_GAZETTE',
							DEH_RECEIVE_FLAG=$DEH_RECEIVE_FLAG,
							DEH_RETURN_FLAG=$DEH_RETURN_FLAG,
							DEH_RETURN_DATE='$DEH_RETURN_DATE',
							DEH_RETURN_TYPE=$DEH_RETURN_TYPE,
							DEH_RECEIVE_DATE='$DEH_RECEIVE_DATE',
							DEH_BOOK_NO='$DEH_BOOK_NO', 
							DEH_BOOK_DATE='$DEH_BOOK_DATE', 
							DEH_REMARK='".save_quote($DEH_REMARK)."', 
							DEH_POSITION='$DEH_POSITION', 
							DEH_ORG='$DEH_ORG', 
							DEH_ISSUE='$DEH_ISSUE', 
							DEH_BOOK='$DEH_BOOK', 
							DEH_PART='$DEH_PART', 
							DEH_PAGE='$DEH_PAGE', 
							DEH_ORDER_DECOR='$DEH_ORDER_DECOR', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
					where DEH_ID=$DEH_ID  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<pre>".$cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเครื่องราชฯ [$PER_ID : $DEH_ID : $DC_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $DEH_ID){
		$cmd = " select DC_CODE from PER_DECORATEHIS where DEH_ID=$DEH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DC_CODE = $data[DC_CODE];
		
		$cmd = " delete from PER_DECORATEHIS where DEH_ID=$DEH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเครื่องราชฯ [$PER_ID : $DEH_ID : $DC_CODE]");
	} // end if

	if(($UPD && $PER_ID && $DEH_ID) || ($VIEW && $PER_ID && $DEH_ID)){
		$cmd = 	" select 	DEH_ID, pdh.DC_CODE, pd.DC_NAME, DEH_DATE, DEH_GAZETTE, DEH_RECEIVE_FLAG, 
											DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, pdh.UPDATE_USER, pdh.UPDATE_DATE, 
											DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG, DEH_ISSUE, 
											DEH_BOOK, DEH_PART, DEH_PAGE, DEH_ORDER_DECOR   
							from		PER_DECORATEHIS pdh,  PER_DECORATION pd  
							where	DEH_ID=$DEH_ID and pdh.DC_CODE=pd.DC_CODE  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//var_dump($data);
		$DEH_ID = $data[DEH_ID];
		$DEH_DATE = show_date_format(trim($data[DEH_DATE]), 1);
		$DEH_GAZETTE = stripslashes(trim($data[DEH_GAZETTE]));
		$DEH_RECEIVE_FLAG = $data[DEH_RECEIVE_FLAG];
		$DEH_RETURN_FLAG = $data[DEH_RETURN_FLAG];
		$DEH_RETURN_DATE = show_date_format(trim($data[DEH_RETURN_DATE]), 1);
		$DEH_RETURN_TYPE = $data[DEH_RETURN_TYPE];
		$DEH_RECEIVE_DATE = show_date_format(trim($data[DEH_RECEIVE_DATE]), 1);
		$DC_CODE = $data[DC_CODE];
		$DC_NAME = $data[DC_NAME];		
		$DEH_BOOK_NO = trim($data[DEH_BOOK_NO]);
		$DEH_BOOK_DATE = show_date_format(trim($data[DEH_BOOK_DATE]), 1);
		$DEH_REMARK = stripslashes(trim($data[DEH_REMARK]));
		$DEH_POSITION = trim($data[DEH_POSITION]);
		$DEH_ORG = trim($data[DEH_ORG]);
		$DEH_ISSUE = trim($data[DEH_ISSUE]);
		$DEH_BOOK = trim($data[DEH_BOOK]);
		$DEH_PART = trim($data[DEH_PART]);
		$DEH_PAGE = trim($data[DEH_PAGE]);
		$DEH_ORDER_DECOR = trim($data[DEH_ORDER_DECOR]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($DEH_ID);
		unset($DEH_DATE);

		unset($DC_CODE);
		unset($DC_NAME);
		unset($DEH_GAZETTE);
		unset($DEH_RECEIVE_FLAG);
		unset($DEH_RETURN_FLAG);
		unset($DEH_RETURN_DATE);
		unset($DEH_RETURN_TYPE);
		unset($DEH_RECEIVE_DATE);
		unset($DEH_BOOK_NO);
		unset($DEH_BOOK_DATE);
		unset($DEH_REMARK);
		unset($DEH_POSITION);
		unset($DEH_ORG);
		$DEH_ISSUE = "1";
		unset($DEH_BOOK);
		unset($DEH_PART);
		unset($DEH_PAGE);
		unset($DEH_ORDER_DECOR);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		 
	} // end if
?>