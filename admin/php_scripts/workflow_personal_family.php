<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
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

		if (!$FML_ID) {
			$cmd = "	select  FML_ID  from  PER_WORKFLOW_FAMILY
							where  PER_ID=$PER_ID and FML_WF_STATUS!='04'
							order by FML_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$FML_ID = $data[FML_ID];
			if ($FML_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	if($command=="REORDER"){
		foreach($ARR_FAMILY_ORDER as $FML_ID => $FML_SEQ){
			$cmd = " update PER_WORKFLOW_FAMILY set FML_SEQ='$FML_SEQ' where FML_ID=$FML_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
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

	$FML_BIRTHDATE =  save_date($FML_BIRTHDATE);
	$FML_DOCDATE =  save_date($FML_DOCDATE);
	$MR_DOCDATE =  save_date($MR_DOCDATE);
	$IN_DOCDATE =  save_date($IN_DOCDATE);

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(FML_ID) as max_id from PER_WORKFLOW_FAMILY ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$FML_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_FAMILY (FML_ID, PER_ID, FML_SEQ, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, 
						  FML_CARDNO, FML_GENDER, FML_BIRTHDATE, FML_ALIVE, RE_CODE, OC_CODE, OC_OTHER, FML_BY, 
						  FML_BY_OTHER, FML_DOCTYPE, FML_DOCNO, FML_DOCDATE, MR_CODE, MR_DOCTYPE, MR_DOCNO, 
						  MR_DOCDATE, MR_DOC_PV_CODE, PV_CODE, POST_CODE, FML_INCOMPETENT, IN_DOCTYPE, IN_DOCNO, 
						  IN_DOCDATE, UPDATE_USER, UPDATE_DATE, FML_WF_STATUS)
						  values	($FML_ID, $PER_ID, $FML_SEQ, $FML_TYPE, '$PN_CODE', '$FML_NAME', '$FML_SURNAME',  
						  '$FML_CARDNO', $FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$OC_CODE', '$OC_OTHER', 
						  $FML_BY, '$FML_BY_OTHER', $FML_DOCTYPE, '$FML_DOCNO', '$FML_DOCDATE', '$MR_CODE', 
						  $MR_DOCTYPE, '$MR_DOCNO', '$MR_DOCDATE', '$MR_DOC_PV_CODE', '$PV_CODE', '$POST_CODE',
						  $FML_INCOMPETENT, $IN_DOCTYPE, '$IN_DOCNO', '$IN_DOCDATE', $SESS_USERID, '$UPDATE_DATE', '01') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลครอบครัว [$PER_ID : $FML_ID : $FML_SEQ : $FML_NAME $FML_SURNAME]");
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $FML_ID){
		$cmd = " update PER_WORKFLOW_FAMILY set
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
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลครอบครัว [$PER_ID : $FML_SEQ : $FML_NAME $FML_SURNAME]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $FML_ID){		
		$cmd = " select FML_SEQ, FML_NAME, FML_SURNAME from PER_WORKFLOW_FAMILY where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$FML_SEQ = $data[FML_SEQ];
		$FAMILY_NAME = $data[FML_NAME] ." ". $data[FML_SURNAME];
		
		$cmd = " delete from PER_WORKFLOW_FAMILY where FML_ID=$FML_ID ";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลครอบครัว [$PER_ID : $FML_ID : $FML_SEQ : $FAMILY_NAME]");
	} // end if

//  UPDATE FML_WF_STATUS	//อนุมัติแล้ว
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $FML_ID){
		$FML_WF_STATUS = $updtype[1];
		
		$cmd2 = " select * from PER_WORKFLOW_FAMILY  
						 where PER_ID='$PER_ID' and  FML_ID=$FML_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_FAMILY set
								FML_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and FML_ID=$FML_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติฝึกอบรม [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($FML_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE FML_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$cmd2 = " select FML_NAME, FML_SURNAME from PER_FAMILY 
							where PER_ID='$PER_ID and FML_NAME='$FML_NAME' and FML_SURNAME='$FML_SURNAME' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก ชื่อ-นามสกุล ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(FML_ID) as max_id from PER_FAMILY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$FML_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_FAMILY (FML_ID, PER_ID, FML_SEQ, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, 
							  FML_CARDNO, FML_GENDER, FML_BIRTHDATE, FML_ALIVE, RE_CODE, OC_CODE, OC_OTHER, FML_BY, 
							  FML_BY_OTHER, FML_DOCTYPE, FML_DOCNO, FML_DOCDATE, MR_CODE, MR_DOCTYPE, MR_DOCNO, 
							  MR_DOCDATE, MR_DOC_PV_CODE, PV_CODE, POST_CODE, FML_INCOMPETENT, IN_DOCTYPE, IN_DOCNO, 
							  IN_DOCDATE, UPDATE_USER, UPDATE_DATE)
							  values	($FML_ID, $PER_ID, $FML_SEQ, $FML_TYPE, '$PN_CODE', '$FML_NAME', '$FML_SURNAME',  
							  '$FML_CARDNO', $FML_GENDER, '$FML_BIRTHDATE', $FML_ALIVE, '$RE_CODE', '$OC_CODE', '$OC_OTHER', 
							  $FML_BY, '$FML_BY_OTHER', $FML_DOCTYPE, '$FML_DOCNO', '$FML_DOCDATE', '$MR_CODE', 
							  $MR_DOCTYPE, '$MR_DOCNO', '$MR_DOCDATE', '$MR_DOC_PV_CODE', '$PV_CODE', '$POST_CODE',
							  $FML_INCOMPETENT, $IN_DOCTYPE, '$IN_DOCNO', '$IN_DOCDATE', $SESS_USERID, '$UPDATE_DATE') ";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/41';
				$MOVERENAME = "PER_ATTACHMENT41";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$FML_ID;
				$MOVERENAME = $MOVE_CATEGORY.$FML_ID;
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
			//___echo "TEST : [ $FML_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติครอบครัว [$PER_ID : $FML_ID : $TR_CODE]");
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($UPD || $VIEW){
		$cmd = "	select	FML_SEQ, FML_TYPE, PN_CODE, FML_NAME, FML_SURNAME, FML_CARDNO, FML_GENDER, FML_BIRTHDATE, 
											FML_ALIVE, RE_CODE, OC_CODE, OC_OTHER, FML_BY, FML_BY_OTHER, FML_DOCTYPE, FML_DOCNO, 
											FML_DOCDATE, MR_CODE, MR_DOCTYPE, MR_DOCNO, MR_DOCDATE, MR_DOC_PV_CODE, PV_CODE, 
											POST_CODE, FML_INCOMPETENT, IN_DOCTYPE, IN_DOCNO, IN_DOCDATE, UPDATE_USER, UPDATE_DATE, FML_WF_STATUS
							from		PER_WORKFLOW_FAMILY
							where	FML_ID=$FML_ID and FML_WF_STATUS!='04' ";	
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
		$FML_BIRTHDATE = show_date_format($data[FML_BIRTHDATE], 1);

		$FML_ALIVE = $data[FML_ALIVE];
		$RE_CODE = $data[RE_CODE];
		$OC_CODE = $data[OC_CODE];
		$OC_OTHER = $data[OC_OTHER];
		$FML_BY = $data[FML_BY];
		if($FML_BY==4) $FML_BY_OTHER = $data[FML_BY_OTHER];
		$FML_DOCTYPE = $data[FML_DOCTYPE];
		$FML_DOCNO = $data[FML_DOCNO];
		$FML_DOCDATE = show_date_format($data[FML_DOCDATE], 1);

		$MR_CODE = $data[MR_CODE];
		$MR_DOCTYPE = $data[MR_DOCTYPE];
		$MR_DOCNO = $data[MR_DOCNO];
		$MR_DOCDATE = show_date_format($data[MR_DOCDATE], 1);

		$MR_DOC_PV_CODE = $data[MR_DOC_PV_CODE];
		$PV_CODE = trim($data[PV_CODE]);
		$POST_CODE= $data[POST_CODE];
		$FML_INCOMPETENT = $data[FML_INCOMPETENT];
		$IN_DOCTYPE = $data[IN_DOCTYPE];
		$IN_DOCNO = $data[IN_DOCNO];
		$IN_DOCDATE = show_date_format($data[IN_DOCDATE], 1);

		$FML_WF_STATUS = $data[FML_WF_STATUS];
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
			$data = $db_dpis->get_array();
			$OC_NAME = $data[OC_NAME];
		} // end if

		if($PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PV_NAME = $data[PV_NAME];
		} // end if

		if($MR_DOC_PV_CODE){
			$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$MR_DOC_PV_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MR_DOC_PV_NAME = $data[PV_NAME];
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
	} // end if
?>