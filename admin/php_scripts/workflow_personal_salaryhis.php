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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
				
		if (!$SAH_ID) {
			$cmd = "	select  SAH_ID  from  PER_WORKFLOW_SALARYHIS
							where  PER_ID=$PER_ID and SAH_WF_STATUS!='04'
							order by SAH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SAH_ID = $data[SAH_ID];
			if ($SAH_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
	if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
	if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
	$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		

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
		$SAH_EFFECTIVEDATE =  save_date($SAH_EFFECTIVEDATE);
		$SAH_DOCDATE =  save_date($SAH_DOCDATE);
		$SAH_ENDDATE =  save_date($SAH_ENDDATE);

		$cmd = " select max(SAH_ID) as max_id from PER_WORKFLOW_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SAH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
						SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, 
						SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, 
						SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_WF_STATUS)
						values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', $MOV_CODE, $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
						'$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
						$SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', '$LEVEL_NO', '$SAH_POS_NO', 
						'$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_PAY_NO', '01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $SAH_ID){
		$SAH_EFFECTIVEDATE =  save_date($SAH_EFFECTIVEDATE);
		$SAH_DOCDATE =  save_date($SAH_DOCDATE);
		$SAH_ENDDATE =  save_date($SAH_ENDDATE);
		
		$cmd = " UPDATE PER_WORKFLOW_SALARYHIS SET SAH_EFFECTIVEDATE='$SAH_EFFECTIVEDATE', 
																					MOV_CODE='$MOV_CODE', 
																					SAH_SALARY=$SAH_SALARY, 
																					SAH_DOCNO='$SAH_DOCNO', 
																					SAH_DOCDATE='$SAH_DOCDATE', 
																					SAH_ENDDATE='$SAH_ENDDATE',   
																					PER_CARDNO='$PER_CARDNO', 
																					SAH_PERCENT_UP=$SAH_PERCENT_UP, 
																					SAH_SALARY_UP=$SAH_SALARY_UP, 
																					SAH_SALARY_EXTRA=$SAH_SALARY_EXTRA, 
																					SAH_SEQ_NO=$SAH_SEQ_NO, 
																					SAH_REMARK='$SAH_REMARK',   
																					LEVEL_NO='$LEVEL_NO',   
																					SAH_POS_NO='$SAH_POS_NO',   
																					SAH_POSITION='$SAH_POSITION',   
																					SAH_ORG='$SAH_ORG',   
																					EX_CODE='$EX_CODE',   
																					SAH_PAY_NO='$SAH_PAY_NO',   
																					UPDATE_USER=$SESS_USERID, 
																					UPDATE_DATE='$UPDATE_DATE'
				WHERE SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SAH_ID){
		$cmd = " select MOV_CODE from PER_WORKFLOW_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MOV_CODE = $data[MOV_CODE];
		
		$cmd = " delete from PER_WORKFLOW_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if

//  UPDATE POH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $SAH_ID){
		$SAH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_SALARYHIS where PER_ID=$PER_ID and SAH_ID=$SAH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_SALARYHIS set
								SAH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and SAH_ID=$SAH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเดือน [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($SAH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE POH_WF_STATUS

	if($command=="COMMAND" && $PER_ID){
		$SAH_EFFECTIVEDATE =  save_date($SAH_EFFECTIVEDATE);
		$SAH_DOCDATE =  save_date($SAH_DOCDATE);
		$SAH_ENDDATE =  save_date($SAH_ENDDATE);

		$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SAH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, 
							SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, 
							SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO )
						values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', $MOV_CODE, $SAH_SALARY, '$SAH_DOCNO', '$SAH_DOCDATE', 
							'$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', $SAH_PERCENT_UP, $SAH_SALARY_UP, 
							$SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', '$LEVEL_NO', '$SAH_POS_NO', 
							'$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_PAY_NO' ) ";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/53';
				$MOVERENAME = "PER_ATTACHMENT53";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$SAH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$SAH_ID;
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
			//___echo "TEST : [ $SAH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเดือน [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if 

	if(($UPD && $PER_ID && $SAH_ID) || ($VIEW && $PER_ID && $SAH_ID)){
		$cmd = "	SELECT 		SAH_ID, SAH_EFFECTIVEDATE, psh.MOV_CODE, pm.MOV_NAME, SAH_SALARY, SAH_DOCNO, SAH_DOCDATE, 
						SAH_ENDDATE, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, 
						SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, psh.UPDATE_USER, psh.UPDATE_DATE, SAH_WF_STATUS
				FROM		PER_WORKFLOW_SALARYHIS psh, PER_MOVMENT pm 
				WHERE		psh.SAH_ID=$SAH_ID and psh.MOV_CODE=pm.MOV_CODE and SAH_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID];
		$SAH_EFFECTIVEDATE = show_date_format($data[SAH_EFFECTIVEDATE], 1);
		$SAH_DOCDATE = show_date_format($data[SAH_DOCDATE], 1);
		$SAH_ENDDATE = show_date_format($data[SAH_ENDDATE], 1);

		$MOV_CODE = trim($data[MOV_CODE]);
		$MOV_NAME = trim($data[MOV_NAME]);		
		$SAH_SALARY = $data[SAH_SALARY];
		$SAH_DOCNO = trim($data[SAH_DOCNO]);
		$SAH_PERCENT_UP = $data[SAH_PERCENT_UP];
		$SAH_SALARY_UP = $data[SAH_SALARY_UP];
		$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA];
		$SAH_SEQ_NO = $data[SAH_SEQ_NO];
		$SAH_REMARK = trim($data[SAH_REMARK]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$SAH_POS_NO = trim($data[SAH_POS_NO]);
		$SAH_POSITION = trim($data[SAH_POSITION]);
		$SAH_ORG = trim($data[SAH_ORG]);
		$EX_CODE = trim($data[EX_CODE]);
		$SAH_PAY_NO = trim($data[SAH_PAY_NO]);
		$UPDATE_USER = $data[UPDATE_USER];
		$SAH_WF_STATUS = trim($data[SAH_WF_STATUS]);

		$cmd ="select EX_NAME from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$EX_NAME = trim($data2[EX_NAME]);

		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($SAH_ID);
		unset($SAH_EFFECTIVEDATE);
		unset($SAH_ENDDATE);
		unset($SAH_SALARY);
		unset($SAH_DOCNO);
		unset($SAH_DOCDATE);
		unset($SAH_PERCENT_UP);
		unset($SAH_SALARY_UP);
		unset($SAH_SALARY_EXTRA);
		unset($SAH_SEQ_NO);
		unset($SAH_REMARK);
		unset($LEVEL_NO);
		unset($SAH_POS_NO);
		unset($SAH_POSITION);
		unset($SAH_ORG);
		unset($SAH_PAY_NO);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	
		unset($MOV_CODE);
		unset($MOV_NAME);
		unset($EX_CODE);
		unset($EX_NAME);
	} // end if
?>