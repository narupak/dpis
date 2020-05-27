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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_BIRTHDATE
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID, PER_PERSONAL.PER_BIRTHDATE
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_BIRTHDATE
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		//echo '<pre>'.$cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$PER_BIRTHDATE = show_date_format(trim($data[PER_BIRTHDATE]), 1);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_BIRTHDATE_CHANGE set AUDIT_FLAG = 'N' where BC_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_BIRTHDATE_CHANGE set AUDIT_FLAG = 'Y' where BC_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" || $command=="UPDATE"){		
		if($PER_BIRTHDATE_NEW){
			$arr_temp = explode("/", $PER_BIRTHDATE_NEW);
			$RETIRE_YEAR = ($arr_temp[2] - 543) + 60;
			if($arr_temp[1] > 10 || ($arr_temp[1]==10 && $arr_temp[0] > "01"))    
				$RETIRE_YEAR += 1;
			$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
		} // end if
	} // end if

	if($command=="ADD" && $PER_ID){	
		$PER_BIRTHDATE = save_date($PER_BIRTHDATE); 
		$PER_BIRTHDATE_NEW = save_date($PER_BIRTHDATE_NEW); 
		$BC_BOOK_DATE = save_date($BC_BOOK_DATE); 
		
		$cmd2 = " select PER_BIRTHDATE from PER_BIRTHDATE_CHANGE 
							where PER_ID=$PER_ID and PER_BIRTHDATE='$PER_BIRTHDATE' and PER_BIRTHDATE_NEW='$PER_BIRTHDATE_NEW' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ วันเดือนปีเกิด ซ้ำ !!!");
				-->   </script>	<? }  
		else {			
			$cmd = " select max(BC_ID) as max_id from PER_BIRTHDATE_CHANGE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$BC_ID = $data[max_id] + 1;
		
			$cmd = " 	insert into PER_BIRTHDATE_CHANGE (BC_ID, PER_ID, PER_BIRTHDATE, PER_BIRTHDATE_NEW, PER_CARDNO, 
								BC_BOOK_NO, BC_BOOK_DATE, BC_REMARK, BC_APPROVE_FLAG, UPDATE_USER, UPDATE_DATE)
								values ($BC_ID, $PER_ID, '$PER_BIRTHDATE', '$PER_BIRTHDATE_NEW', '$PER_CARDNO', 
								'$BC_BOOK_NO', '$BC_BOOK_DATE', '$BC_REMARK', $BC_APPROVE_FLAG, $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//หาว่า insert เข้าไหม
			$cmd = " select BC_ID from PER_BIRTHDATE_CHANGE where BC_ID=$BC_ID ";
			$count_insert = $db_dpis->send_cmd($cmd);
			//echo "-> $count_insert <br>";			


			if ($PER_BIRTHDATE_NEW && $count_insert) {
				$cmd = " UPDATE PER_PERSONAL SET PER_BIRTHDATE = '$PER_BIRTHDATE_NEW', PER_RETIREDATE = '$PER_RETIREDATE' WHERE PER_ID = $PER_ID ";
				$db_dpis->send_cmd($cmd);
				//echo $cmd;
//				$db_dpis->show_error();
			}

			// เคลียร์ค่า input
			$PER_BIRTHDATE = show_date_format(trim($PER_BIRTHDATE), 1);
			$PER_BIRTHDATE_NEW = "";
			$BC_BOOK_DATE = "";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการแก้ไขวันเดือนปีเกิด [$PER_ID : $BC_ID : $PER_BIRTHDATE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $BC_ID){
		$PER_BIRTHDATE = save_date($PER_BIRTHDATE); 
		$PER_BIRTHDATE_NEW = save_date($PER_BIRTHDATE_NEW); 
		$BC_BOOK_DATE = save_date($BC_BOOK_DATE); 
		
		$cmd2 = " select   PER_BIRTHDATE from PER_BIRTHDATE_CHANGE 
							where PER_ID=$PER_ID and PER_BIRTHDATE='$PER_BIRTHDATE' and PER_BIRTHDATE_NEW='$PER_BIRTHDATE_NEW' and BC_ID<>$BC_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ วันเดือนปีเกิด ซ้ำ !!!");
				-->   </script>	<? }  
		else {			
	
			$cmd = " UPDATE PER_BIRTHDATE_CHANGE SET
						PER_BIRTHDATE='$PER_BIRTHDATE', 
						PER_BIRTHDATE_NEW='$PER_BIRTHDATE_NEW', 
						PER_CARDNO='$PER_CARDNO', 
						BC_BOOK_NO='$BC_BOOK_NO', 
						BC_BOOK_DATE='$BC_BOOK_DATE', 
						BC_REMARK='$BC_REMARK', 
						BC_APPROVE_FLAG=$BC_APPROVE_FLAG, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
					WHERE BC_ID=$BC_ID ";
			$db_dpis->send_cmd($cmd);
			
			$cmd = " UPDATE PER_PERSONAL SET PER_BIRTHDATE = '$PER_BIRTHDATE_NEW', PER_RETIREDATE = '$PER_RETIREDATE' WHERE PER_ID = $PER_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการแก้ไขวันเดือนปีเกิด [$PER_ID : $BC_ID : $PER_BIRTHDATE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $BC_ID){
		
		
		
		$cmd = " delete from PER_BIRTHDATE_CHANGE where BC_ID=$BC_ID ";
		$db_dpis->send_cmd($cmd);
                
                $cmd = " select PER_BIRTHDATE from PER_BIRTHDATE_CHANGE where BC_ID=$BC_ID ";
		$cnt = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
                
                if($cnt>0){
                    $PER_BIRTHDATE = $data[PER_BIRTHDATE];
                }else{
                        if($DPISDB=="odbc"){
                            $cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_BIRTHDATE
                                            from		PER_PERSONAL as PER
                                                            left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
                                            where	PER.PER_ID=$PER_ID ";
                    }elseif($DPISDB=="oci8"){
                            $cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
                                                            PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID, PER_PERSONAL.PER_BIRTHDATE
                                            from		PER_PERSONAL, PER_PRENAME
                                            where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
                    }elseif($DPISDB=="mysql"){
                            $cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_BIRTHDATE
                                            from		PER_PERSONAL as PER
                                                            left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
                                            where	PER.PER_ID=$PER_ID ";
                    } // end if
                    //echo '<pre>'.$cmd;
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
                    $PER_CARDNO = trim($data[PER_CARDNO]);
                    $PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
                    $PER_BIRTHDATE = show_date_format(trim($data[PER_BIRTHDATE]), 1);
                }
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการแก้ไขวันเดือนปีเกิด [$PER_ID : $BC_ID : $PER_BIRTHDATE]");
	} // end if

	if(($UPD && $PER_ID && $BC_ID) || ($VIEW && $PER_ID && $BC_ID)){
		$cmd = "	SELECT 	BC_ID, PER_BIRTHDATE, PER_BIRTHDATE_NEW, BC_BOOK_NO, BC_BOOK_DATE, 
												BC_REMARK, BC_APPROVE_FLAG, UPDATE_USER, UPDATE_DATE
							FROM		PER_BIRTHDATE_CHANGE
							WHERE	BC_ID=$BC_ID ";
                
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$BC_ID = $data[BC_ID];
		$PER_BIRTHDATE = show_date_format(trim($data[PER_BIRTHDATE]), 1);
		$PER_BIRTHDATE_NEW = show_date_format(trim($data[PER_BIRTHDATE_NEW]), 1);
		$BC_BOOK_NO = $data[BC_BOOK_NO];
		$BC_BOOK_DATE = show_date_format(trim($data[BC_BOOK_DATE]), 1);
		$BC_REMARK = $data[BC_REMARK];
		$BC_APPROVE_FLAG = $data[BC_APPROVE_FLAG];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($BC_ID);
		unset($PER_BIRTHDATE_NEW);
		unset($BC_BOOK_NO);
		unset($BC_BOOK_DATE);
		unset($BC_REMARK);
		$BC_APPROVE_FLAG = 1;
	} // end if
?>