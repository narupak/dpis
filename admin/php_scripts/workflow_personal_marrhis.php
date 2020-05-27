<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID && $command != "CANCEL"){
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
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		if (!$MAH_ID) {
			$cmd = "	select  MAH_ID  from  PER_WORKFLOW_MARRHIS
							where  PER_ID=$PER_ID and MAH_WF_STATUS!='04'
							order by MAH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MAH_ID = $data[MAH_ID];
			if ($MAH_ID) {
				$VIEW = 1;
			}
		}
	} // end if


	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	if($command=="REORDER"){
	
		foreach($ARR_MAH_ORDER as $MAH_ID => $MAH_SEQ){
			$cmd = " update PER_WORKFLOW_MARRHIS set MAH_SEQ='$MAH_SEQ' where MAH_ID=$MAH_ID ";
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับประวัติการสมรส [$PER_ID : $PER_NAME]");
	} // end if

	if($command=="ADD" && $PER_ID){
		$MAH_MARRY_DATE =  save_date($MAH_MARRY_DATE);
		$MAH_DIVORCE_DATE =  save_date($MAH_DIVORCE_DATE);
		$MAH_BOOK_DATE =  save_date($MAH_BOOK_DATE);
		
		$cmd2="select  MAH_SEQ from PER_WORKFLOW_MARRHIS where PER_ID=$PER_ID and MAH_SEQ=$MAH_SEQ ";
		$count=$db_dpis->send_cmd($cmd2); 
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) {
		?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ ลำดับที่ ซ้ำ !!!");
				-->   </script>	<? 
		}
	
		$cmd = " select max(MAH_ID) as max_id from PER_WORKFLOW_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, 
						UPDATE_USER, UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, 
						MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK, MAH_WF_STATUS)
						 values ($MAH_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
						$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$PN_CODE', '$MAH_MARRY_NO', '$MAH_MARRY_ORG', '$PV_CODE', 
						'$MR_CODE', '$MAH_BOOK_NO', '$MAH_BOOK_DATE', '$MAH_REMARK', '01') ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการสมรส [$PER_ID : $MAH_ID : $MAH_SEQ : $MAH_NAME]");
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $MAH_ID){
		$MAH_MARRY_DATE =  save_date($MAH_MARRY_DATE);
		$MAH_DIVORCE_DATE =  save_date($MAH_DIVORCE_DATE);
		$MAH_BOOK_DATE =  save_date($MAH_BOOK_DATE);
		
		$cmd2="select  MAH_SEQ from PER_WORKFLOW_MARRHIS where PER_ID=$PER_ID and MAH_SEQ=$MAH_SEQ and MAH_ID <> $MAH_ID ";
		$count=$db_dpis->send_cmd($cmd2); 
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) {
		?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ ลำดับที่ ซ้ำ !!!");
				-->   </script>	<? 
		}
	
		$cmd = " update PER_WORKFLOW_MARRHIS set
							MAH_SEQ=$MAH_SEQ, 
							MAH_NAME='$MAH_NAME', 
							MAH_MARRY_DATE='$MAH_MARRY_DATE', 
							DV_CODE='$DV_CODE', 
							MAH_DIVORCE_DATE='$MAH_DIVORCE_DATE', 
							PN_CODE='$PN_CODE', 
							MAH_MARRY_NO='$MAH_MARRY_NO', 
							MAH_MARRY_ORG='$MAH_MARRY_ORG', 
							PV_CODE='$PV_CODE', 
							MR_CODE='$MR_CODE', 
							MAH_BOOK_NO='$MAH_BOOK_NO', 
							MAH_BOOK_DATE='$MAH_BOOK_DATE', 
							MAH_REMARK='$MAH_REMARK', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							PER_CARDNO='$PER_CARDNO' 
						where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการสมรส [$PER_ID : $PER_NAME : $MAH_SEQ : $MAH_NAME]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $MAH_ID){
		/*
		$cmd = " select DV_CODE from PER_WORKFLOW_MARRHIS where PER_ID=$PER_ID and MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DV_CODE = trim($data[DV_CODE]);
		*/
		
		$cmd = " delete from PER_WORKFLOW_MARRHIS where MAH_ID=$MAH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการสมรส [$PER_ID : $MAH_ID : $MAH_SEQ : $MAH_NAME]");
	} // end if

//  UPDATE MAH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $MAH_ID){
		$MAH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_MARRHIS where PER_ID=$PER_ID and MAH_ID=$MAH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_MARRHIS set
								MAH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and MAH_ID=$MAH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการสมรส [$PER_ID : $PER_NAME : $MAH_NAME]");
			
			if ($MAH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE MAH_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$MAH_MARRY_DATE =  save_date($MAH_MARRY_DATE);
		$MAH_DIVORCE_DATE =  save_date($MAH_DIVORCE_DATE);
		$MAH_BOOK_DATE =  save_date($MAH_BOOK_DATE);
		
		$cmd = " select max(MAH_ID) as max_id from PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAH_ID = $data[max_id] + 1;
		
		$cmd = " select max(MAH_SEQ) as max_id from PER_MARRHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MAH_SEQ = $data[max_id] + 1;
		
		$cmd = " insert into PER_MARRHIS (MAH_ID, PER_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, 
						UPDATE_USER, UPDATE_DATE, PER_CARDNO, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, MR_CODE, 
						MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK)
						 values ($MAH_ID, $PER_ID, $MAH_SEQ, '$MAH_NAME', '$MAH_MARRY_DATE', '$MAH_DIVORCE_DATE', '$DV_CODE', 
						$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$PN_CODE', '$MAH_MARRY_NO', '$MAH_MARRY_ORG', '$PV_CODE', 
						'$MR_CODE', '$MAH_BOOK_NO', '$MAH_BOOK_DATE', '$MAH_REMARK') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
			//################################################
			//---เมื่ออนุมัติแล้ว ทำการย้ายโฟล์เดอร์ทั้งหมดไปเก็บไว้อีกโฟลเดอร์นึง
			//if($ATTACH_FILE==1){
			$PATH_MAIN = "../attachments";		$mode = 0755;		$MOVERENAME = "";
			$MOVE_CATEGORY = str_replace("_WORKFLOW","",$CATEGORY);	// ตัดคำ _WORKFLOW หาชื่อ category ที่จะย้ายไป
			$FILE_PATH = $PATH_MAIN."/".$PER_CARDNO."/".$CATEGORY."/".$LAST_SUBDIR;		//ชื่อโฟล์เดอร์ของ workflow ที่มีไฟล์เก็บอยู่
			//$FILE_PATH_HOST = "$PATH_MAIN\$PER_CARDNO\$CATEGORY\$LAST_SUBDIR";		//ชื่อโฟล์เดอร์ของ workflow ที่มีไฟล์เก็บอยู่
			
			//ตรวจสอบโฟลเดอร์	(ชื่อโฟลเดอร์ที่จะย้ายไฟล์ไปเก็บไว้)
			$MOVEFIRST_SUBDIR = $PATH_MAIN.'/'.$PER_CARDNO;
			if($ATTACH_FILE==1){
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/PER_ATTACHMENT';
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/33';
				$MOVERENAME = "PER_ATTACHMENT33";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$MAH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$MAH_ID;
			}
			echo "$MOVEFINAL_PATH with $MOVERENAME";
				//==================================================
				//---หาว่ามีชื่อโฟลเดอร์นี้อยู่แล้วหรือไม่ ?
				//---สร้างโฟลเดอร์ใหม่ ถ้ายังไม่มีอยู่
				if (!is_dir($MOVEFINAL_PATH)) {		//---1 : ไม่มีโฟลเดอร์นี้อยู่ สร้างขึ้นมาใหม่
					$msgresult1 = $msgresult2 = $msgresult3 = "";

					//โฟล์เดอร์แรก
					if (!is_dir($MOVEFIRST_SUBDIR)) {
						$result1= mkdir($MOVEFIRST_SUBDIR,$mode);
					}
					//โฟล์เดอร์ที่สอง
					if (!is_dir($MOVESECOND_SUBDIR)) {
						if($result1 || is_dir($MOVEFIRST_SUBDIR)){
							$result2 = mkdir($MOVESECOND_SUBDIR,$mode);
						}else{
							$msgresult1 = " <br><span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVEFIRST_SUBDIR ไม่ได้ !!!</span><br>";
						}
					}					
					//โฟล์เดอร์สุดท้าย
					if($result2 || is_dir($MOVESECOND_SUBDIR)){
						$result3= mkdir($MOVEFINAL_PATH,$mode);
					}else{
						$msgresult1="";
						$msgresult2 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVESECOND_SUBDIR ไม่ได้ !!!</span><br>";
					}
					//umask(0);		echo umask();  //test
					if(!$result3){
						$msgresult2="";
						$msgresult3 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVEFINAL_PATH ไม่ได้ !!!</span><br>";
					}
				if($msgresult3){	echo $msgresult3;	}
				if($result3 || is_dir($MOVEFINAL_PATH)){		//--->สร้างโฟลเดอร์ได้แล้ว ก็นำไฟล์ย้ายมาเก็บ//rename
					//---วน loop อ่านไฟล์ทั้งหมดที่เก็บไว้ เพื่อย้ายไปโฟล์เดอร์ใหม่
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//ตัดคำทิ้ง ให้ชื่อไฟล์ใหม่
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "สร้าง/ย้ายสำเร็จ<br>";		unlink("$FINAL_PATH/$file");		//---เมื่อย้ายไปเก็บไว้แล้ว ก็ลบไฟล์นั้นทิ้ง
									//--- อัพเดทชื่อใหม่ใน db เพื่อให้มาเชื่อมโยงแสดงรายการได้
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ลบโฟล์เดอร์นั้นทิ้ง
					} // end if is_dir
				} // end if result
			}else{				//---2 : โฟล์เดอร์นี้ถูกสร้างขึ้นแล้ว ย้ายไฟล์ไปเก็บไว้ได้เลย
					//---วน loop อ่านไฟล์ทั้งหมดที่เก็บไว้ เพื่อย้ายไปโฟล์เดอร์ใหม่
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//ตัดคำทิ้ง ให้ชื่อไฟล์ใหม่
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "ย้ายสำเร็จ<br>";		unlink("$FINAL_PATH/$file");		//---เมื่อย้ายไปเก็บไว้แล้ว ก็ลบไฟล์นั้นทิ้ง
									//--- อัพเดทชื่อใหม่ใน db เพื่อให้มาเชื่อมโยงแสดงรายการได้
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ลบโฟล์เดอร์นั้นทิ้ง
					} // end if is_dir
			}
			//___echo "TEST : [ $MAH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการสมรส [$PER_ID : $MAH_ID : $MAH_SEQ : $MAH_NAME]");
	} // end if
	
	if(($UPD && $PER_ID && $MAH_ID) || ($VIEW && $PER_ID && $MAH_ID)){
		$cmd = " select		MAH_ID, MAH_SEQ, MAH_NAME, MAH_MARRY_DATE, MAH_DIVORCE_DATE, DV_CODE, 
											UPDATE_USER, UPDATE_DATE, PN_CODE, MAH_MARRY_NO, MAH_MARRY_ORG, PV_CODE, 
											MR_CODE, MAH_BOOK_NO, MAH_BOOK_DATE, MAH_REMARK, MAH_WF_STATUS
						from			PER_WORKFLOW_MARRHIS
						where		MAH_ID=$MAH_ID and MAH_WF_STATUS!='04' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();		
		$MAH_SEQ = $data[MAH_SEQ];
		$MAH_NAME = $data[MAH_NAME];
		$MAH_MARRY_DATE = show_date_format($data[MAH_MARRY_DATE], 1);
		$MAH_DIVORCE_DATE = show_date_format($data[MAH_DIVORCE_DATE], 1);
		$MAH_BOOK_DATE = show_date_format($data[MAH_BOOK_DATE], 1);
		$MAH_MARRY_NO = trim($data[MAH_MARRY_NO]);
		$MAH_MARRY_ORG = trim($data[MAH_MARRY_ORG]);
		$MR_CODE = trim($data[MR_CODE]);
		$MAH_BOOK_NO = trim($data[MAH_BOOK_NO]);
		$MAH_REMARK = trim($data[MAH_REMARK]);
		$MAH_WF_STATUS = trim($data[MAH_WF_STATUS]);

		$DV_CODE = trim($data[DV_CODE]);
		if($DV_CODE){
			$cmd = " select DV_NAME from PER_DIVORCE where DV_CODE='$DV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DV_NAME = $data2[DV_NAME];
		} // end if

		$PN_CODE = trim($data[PN_CODE]);
		if($PN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		} // end if

		$PV_CODE = trim($data[PV_CODE]);
		if($PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME = $data2[PV_NAME];
		} // end if

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($MAH_ID);
		unset($MAH_SEQ);
		unset($MAH_NAME);
		unset($MAH_MARRY_DATE);
		unset($MAH_DIVORCE_DATE);
		unset($MAH_MARRY_NO);
		unset($MAH_MARRY_ORG);
		unset($MR_CODE);
		unset($MAH_BOOK_NO);
		unset($MAH_BOOK_DATE);
		unset($MAH_REMARK);

		unset($DV_CODE);
		unset($DV_NAME);
		unset($PN_CODE);
		unset($PN_NAME);
		unset($PV_CODE);
		unset($PV_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>