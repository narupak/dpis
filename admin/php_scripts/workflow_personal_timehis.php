<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID && $command != "CANCEL"){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID 
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";		
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		if (!$TIMEH_ID) {
			$cmd = "	select  TIMEH_ID  from  PER_WORKFLOW_TIMEHIS
							where  PER_ID=$PER_ID and TIMEH_WF_STATUS!='04'
							order by TIMEH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TIMEH_ID = $data[TIMEH_ID];
			if ($TIMEH_ID) {
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
	
	if($command=="ADD" && $PER_ID){	
		$TIMEH_BOOK_DATE =  save_date($TIMEH_BOOK_DATE);
		
		$cmd2 = " select TIME_CODE, TIMEH_MINUS from PER_WORKFLOW_TIMEHIS 
							where PER_ID=$PER_ID and TIME_CODE='$TIME_CODE' and TIMEH_MINUS=$TIMEH_MINUS ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ เวลาทวีคูณ และ จำนวนวันที่ไม่นับเวลาทวีคูณ ซ้ำ !!!");
		-->   </script>	<? }  
		else {			
			$cmd = " select max(TIMEH_ID) as max_id from PER_WORKFLOW_TIMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TIMEH_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_WORKFLOW_TIMEHIS (TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, 
							PER_CARDNO, UPDATE_USER, UPDATE_DATE, TIMEH_BOOK_NO, TIMEH_BOOK_DATE, TIMEH_WF_STATUS)
							values ($TIMEH_ID, $PER_ID, '$TIME_CODE', '$TIMEH_MINUS', '$TIMEH_REMARK', '$PER_CARDNO', 
							$SESS_USERID, '$UPDATE_DATE', '$TIMEH_BOOK_NO', '$TIMEH_BOOK_DATE', '01')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $TIMEH_ID){
		$TIMEH_BOOK_DATE =  save_date($TIMEH_BOOK_DATE);
		
		$cmd2 = " select   TIME_CODE, TIMEH_MINUS from PER_WORKFLOW_TIMEHIS 
							where PER_ID=$PER_ID and TIME_CODE='$TIME_CODE' and TIMEH_MINUS=$TIMEH_MINUS and TIMEH_ID<>$TIMEH_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
				if($count) { ?>  <script> <!--  
		alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ เวลาทวีคูณ และ จำนวนวันที่ไม่นับเวลาทวีคูณ ซ้ำ !!!");
				-->   </script>	<? }  
				else {			
	
		$cmd = " UPDATE PER_WORKFLOW_TIMEHIS SET
					TIME_CODE='$TIME_CODE', 
					TIMEH_MINUS='$TIMEH_MINUS', 
					PER_CARDNO='$PER_CARDNO', 
					TIMEH_REMARK='$TIMEH_REMARK', 
					TIMEH_BOOK_NO='$TIMEH_BOOK_NO', 
					TIMEH_BOOK_DATE='$TIMEH_BOOK_DATE', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
	} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $TIMEH_ID){
		$cmd = " select TIME_CODE from PER_WORKFLOW_TIMEHIS where TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TIME_CODE = $data[TIME_CODE];
		
		$cmd = " delete from PER_WORKFLOW_TIMEHIS where TIMEH_ID=$TIMEH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
	} // end if

//  UPDATE TIMEH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $TIMEH_ID){
		$TIMEH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_TIMEHIS where PER_ID=$PER_ID and TIMEH_ID=$TIMEH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_TIMEHIS set
								TIMEH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and TIMEH_ID=$TIMEH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($TIMEH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE TIMEH_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$cmd2="select   TIME_CODE, TIMEH_MINUS from PER_TIMEHIS where PER_ID=$PER_ID and TIME_CODE='$TIME_CODE' and TIMEH_MINUS=$TIMEH_MINUS ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ เวลาทวีคูณ และ จำนวนวันที่ไม่นับเวลาทวีคูณ ซ้ำ !!!");
		-->   </script>	<? }  
		else {			
			$cmd = " select max(TIMEH_ID) as max_id from PER_TIMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TIMEH_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_TIMEHIS	(TIMEH_ID, PER_ID, TIME_CODE, TIMEH_MINUS, TIMEH_REMARK, 
							PER_CARDNO, UPDATE_USER, UPDATE_DATE, TIMEH_BOOK_NO, TIMEH_BOOK_DATE)
							values ($TIMEH_ID, $PER_ID, '$TIME_CODE', '$TIMEH_MINUS', '$TIMEH_REMARK', 
							'$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$TIMEH_BOOK_NO', '$TIMEH_BOOK_DATE')   ";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/61';
				$MOVERENAME = "PER_ATTACHMENT61";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$TIMEH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$TIMEH_ID;
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
			//___echo "TEST : [ $TIMEH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติเวลาทวีคูณ [$PER_ID : $TIMEH_ID : $TIME_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if(($UPD && $PER_ID && $TIMEH_ID) || ($VIEW && $PER_ID && $TIMEH_ID)){
		$cmd = "	SELECT 	TIMEH_ID, pth.TIME_CODE, pt.TIME_NAME, TIMEH_MINUS, TIMEH_REMARK, TIMEH_BOOK_NO, TIMEH_BOOK_DATE, TIMEH_WF_STATUS
							FROM		PER_WORKFLOW_TIMEHIS pth, PER_TIME pt
							WHERE	pth.TIMEH_ID=$TIMEH_ID and pth.TIME_CODE=pt.TIME_CODE and TIMEH_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TIMEH_ID = $data[TIMEH_ID];
		$TIME_CODE = $data[TIME_CODE];
		$TIME_NAME = $data[TIME_NAME];
		$TIMEH_MINUS = $data[TIMEH_MINUS];
		$TIMEH_REMARK = $data[TIMEH_REMARK];
		$TIMEH_BOOK_NO = $data[TIMEH_BOOK_NO];
		$TIMEH_BOOK_DATE = show_date_format($data[TIMEH_BOOK_DATE], 1);
		$TIMEH_WF_STATUS = $data[TIMEH_WF_STATUS];
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($TIMEH_ID);
		unset($TIMEH_MINUS);
		unset($TIMEH_REMARK);
		unset($TIMEH_BOOK_NO);
		unset($TIMEH_BOOK_DATE);
	
		unset($TIME_CODE);
		unset($TIME_NAME);
	} // end if
?>