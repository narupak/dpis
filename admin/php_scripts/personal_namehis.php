<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PER.PN_CODE, PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PERSONAL.PN_CODE, PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PER.PN_CODE, PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE_OLD = $data[PN_CODE];
		$PN_NAME_OLD = $data[PN_NAME];
		$NH_NAME_OLD = $data[PER_NAME];
		$NH_SURNAME_OLD = $data[PER_SURNAME];
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_NAMEHIS set AUDIT_FLAG = 'N' where NH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_NAMEHIS set AUDIT_FLAG = 'Y' where NH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$NH_DATE = save_date($NH_DATE); 
		$NH_BOOK_DATE = save_date($NH_BOOK_DATE); 
		
		$cmd2 = " select   NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO from PER_NAMEHIS 
							where PER_ID=$PER_ID and NH_DATE='$NH_DATE' and PN_CODE='$PN_CODE' and  NH_NAME='$NH_NAME' and 
							NH_SURNAME='$NH_SURNAME' and NH_DOCNO='$NH_DOCNO' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ วันที่เปลี่ยนแปลง, คำนำหน้า, ชื่อเดิม, สกุลเดิม และหลักฐานการเปลี่ยนแปลงซ้ำ !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(NH_ID) as max_id from PER_NAMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$NH_ID = $data[max_id] + 1;

			$cmd = " insert into PER_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, 
								NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
								values ($NH_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
								$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', 
								'$NH_SURNAME_NEW', '$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			if ($NH_UPDATE == 1) {
				if ($PN_CODE_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PN_CODE = '$PN_CODE_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}

				if ($NH_NAME_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PER_NAME = '$NH_NAME_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}

				if ($NH_SURNAME_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PER_SURNAME = '$NH_SURNAME_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}
			}
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการเปลี่ยนแปลงชื่อ-สกุล [$PER_ID : $NH_ID : $PN_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} //end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $NH_ID){
		$NH_DATE = save_date($NH_DATE); 
		$NH_BOOK_DATE = save_date($NH_BOOK_DATE); 
	
		$cmd2 = " select   NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO from PER_NAMEHIS 
							where PER_ID=$PER_ID and NH_DATE='$NH_DATE' and PN_CODE='$PN_CODE' and  NH_NAME='$NH_NAME' and 
							NH_SURNAME='$NH_SURNAME' and NH_DOCNO='$NH_DOCNO' and  NH_ID <> $NH_ID ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ วันที่เปลี่ยนแปลง, คำนำหน้า, ชื่อเดิม, สกุลเดิม และหลักฐานการเปลี่ยนแปลงซ้ำ !!!");
				-->   </script>	<? 
		} else {		
			$cmd = " UPDATE PER_NAMEHIS SET
							NH_DATE='$NH_DATE', 
							PN_CODE='$PN_CODE', 
							NH_DOCNO='$NH_DOCNO',  
							NH_NAME='$NH_NAME', 
							NH_SURNAME='$NH_SURNAME', 
							NH_ORG='$NH_ORG', 
							PN_CODE_NEW='$PN_CODE_NEW', 
							NH_NAME_NEW='$NH_NAME_NEW', 
							NH_SURNAME_NEW='$NH_SURNAME_NEW', 
							NH_BOOK_NO='$NH_BOOK_NO',  
							NH_BOOK_DATE='$NH_BOOK_DATE',  
							NH_REMARK='$NH_REMARK',  
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							PER_CARDNO='$PER_CARDNO' 
						WHERE NH_ID=$NH_ID ";
			$db_dpis->send_cmd($cmd);
			
			if ($NH_UPDATE == 1) {
				if ($PN_CODE_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PN_CODE = '$PN_CODE_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}

				if ($NH_NAME_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PER_NAME = '$NH_NAME_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}

				if ($NH_SURNAME_NEW) {
					$cmd = " UPDATE PER_PERSONAL SET PER_SURNAME = '$NH_SURNAME_NEW' WHERE PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				}
			}
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการเปลี่ยนแปลงชื่อ-สกุล [$PER_ID : $NH_ID : $PN_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $NH_ID){
		$cmd = " select PN_CODE from PER_NAMEHIS where NH_ID=$NH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		
		$cmd = " delete from PER_NAMEHIS where NH_ID=$NH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการเปลี่ยนแปลงชื่อ-สกุล [$PER_ID : $NH_ID : $PN_CODE]");
	} // end if

	if(($UPD && $PER_ID && $NH_ID) || ($VIEW && $PER_ID && $NH_ID)){
		$cmd = " select		NH_DATE, pnh.PN_CODE, ppn.PN_NAME, NH_NAME, NH_SURNAME, NH_DOCNO, 
											pnh.UPDATE_USER, pnh.UPDATE_DATE, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
											NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK
						from			PER_NAMEHIS pnh, PER_PRENAME ppn
						where		NH_ID = $NH_ID and pnh.PN_CODE = ppn.PN_CODE ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NH_DATE = show_date_format(trim($data[NH_DATE]), 1);
		$PN_CODE = $data[PN_CODE];
		$PN_NAME = $data[PN_NAME];
	
		$NH_NAME = $data[NH_NAME];
		$NH_SURNAME = $data[NH_SURNAME];
		$NH_DOCNO = $data[NH_DOCNO];
		$NH_ORG = trim($data[NH_ORG]);
		$NH_NAME_NEW = trim($data[NH_NAME_NEW]);
		$NH_SURNAME_NEW = trim($data[NH_SURNAME_NEW]);
		$NH_BOOK_NO = trim($data[NH_BOOK_NO]);
		$NH_BOOK_DATE = show_date_format(trim($data[NH_BOOK_DATE]), 1);
		$NH_REMARK = trim($data[NH_REMARK]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
		if($PN_CODE_NEW){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE_NEW' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME_NEW = $data2[PN_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($NH_ID);
		unset($NH_DATE);
		unset($NH_DOCNO);		
		unset($NH_ORG);
		$PN_CODE = $PN_CODE_NEW = $PN_CODE_OLD;
		$PN_NAME = $PN_NAME_NEW = $PN_NAME_OLD;
		$NH_NAME = $NH_NAME_NEW = $NH_NAME_OLD;
		$NH_SURNAME = $NH_SURNAME_NEW = $NH_SURNAME_OLD;
		unset($NH_BOOK_NO);
		unset($NH_BOOK_DATE);
		unset($NH_REMARK);
	} // end if
?>