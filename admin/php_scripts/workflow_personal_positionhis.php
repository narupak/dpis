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
		$ORG_ID_1 = $MINISTRY_ID;
		$ORG_NAME_1 = (trim($ORG_NAME_1))? $ORG_NAME_1 : $MINISTRY_NAME;
		$ORG_ID_2 = $DEPARTMENT_ID;
		$ORG_NAME_2 = (trim($ORG_NAME_2))? $ORG_NAME_2 :  $DEPARTMENT_NAME;
		$CT_CODE = (trim($CT_CODE))? $CT_CODE : "140";
		$CT_NAME = (trim($CT_NAME))? $CT_NAME : "ไทย";		

		if($DPISDB=="odbc"){
			$cmd = "  select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
							from		PER_PERSONAL as PER
										left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "  select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
										PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 							from		PER_PERSONAL, PER_PRENAME
							where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
										PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = "  select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
							from		PER_PERSONAL as PER
										left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		///echo "fdfdf".$PER_ID_DEPARTMENT_ID.":::::::::".$PER_ID;
				
		if (!$POH_ID) {
			$cmd = "	select  POH_ID  from  PER_WORKFLOW_POSITIONHIS
							where  PER_ID=$PER_ID and POH_WF_STATUS!='04'
							order by POH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POH_ID = $data[POH_ID];
			if ($POH_ID) {
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
	
	if ($command=="ADD" || $command=="UPDATE") {
		$SAV_POH_EFFECTIVEDATE =  save_date($POH_EFFECTIVEDATE);
		$SAV_POH_ENDDATE =  save_date($POH_ENDDATE);
		$SAV_POH_DOCDATE =  save_date($POH_DOCDATE);
		$POH_ORGMGT = ($POH_ORGMGT == 1)?   $POH_ORGMGT : 2 ;
		$POH_SALARY = (str_replace(",", "", $POH_SALARY)) + 0;
		$POH_SALARY_POS = (str_replace(",", "", $POH_SALARY_POS)) + 0;
		$POH_REMARK = (trim($POH_REMARK))? $POH_REMARK : "-";		
		$POH_REMARK = str_replace("'", "&rsquo;", $POH_REMARK);	
		
		$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
		$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
		$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
		$CT_CODE_tmp = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
		$PV_CODE1 = (trim($PV_CODE1])? "'$PV_CODE1'" : "NULL";
		$AP_CODE = (trim($AP_CODE))? "'$AP_CODE'" : "NULL";
		$ORG_ID_1 = (trim($ORG_ID_1))? $ORG_ID_1 : 1;
		$ORG_ID_2 = (trim($ORG_ID_2))? $ORG_ID_2 : 1;
		$ORG_ID_3 = (trim($ORG_ID_3))? $ORG_ID_3 : 1;		
		$POH_SEQ_NO = (trim($POH_SEQ_NO))? $POH_SEQ_NO : 1;		
		
		if ($PER_TYPE==1) {	
			$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PN_CODE = $EP_CODE = "NULL";
		} elseif ($PER_TYPE==2) {
			$PN_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $EP_CODE = "NULL";
		} elseif ($PER_TYPE==3) {
			$EP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = "NULL";
		}
	}

	if($command=="ADD" && $PER_ID){
	
		$cmd = " select max(POH_ID) as max_id from PER_WORKFLOW_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$POH_ID = $data[max_id] + 1;	
		
		$cmd = "  insert into PER_WORKFLOW_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, 
							POH_ENDDATE, POH_DOCNO, POH_DOCDATE, POH_POS_NO, POH_ORGMGT, ORG_ID_1, ORG_ID_2, 
							ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, 
							POH_UNDER_ORG5, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, 
							POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, PL_CODE, PN_CODE, 
							EP_CODE, PER_CARDNO, PM_CODE, LEVEL_NO, PT_CODE, CT_CODE, PV_CODE, AP_CODE, 
							POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, 
							UPDATE_USER, UPDATE_DATE, POH_WF_STATUS )
							values ($POH_ID, $PER_ID, '$SAV_POH_EFFECTIVEDATE', '$MOV_CODE', '$SAV_POH_ENDDATE', 
							'$POH_DOCNO', '$SAV_POH_DOCDATE', '$POH_POS_NO', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, 
							$ORG_ID_3, '$POH_UNDER_ORG1_NAME', '$POH_UNDER_ORG2_NAME', '$POH_UNDER_ORG3_NAME', 
							'$POH_UNDER_ORG4_NAME', '$POH_UNDER_ORG5_NAME', '$POH_ASS_ORG_NAME', 
							'$POH_ASS_ORG1_NAME', '$POH_ASS_ORG2_NAME', '$POH_SALARY', '$POH_SALARY_POS', 
							'$POH_REMARK', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $PL_CODE, $PN_CODE, 
							$EP_CODE, '$PER_CARDNO', $PM_CODE, $LEVEL_NO, $PT_CODE, $CT_CODE_tmp, $PV_CODE1, 
							$AP_CODE, '$POH_ORG_TRANSFER', '$POH_ORG', '$PM_NAME', '$PL_NAME', $POH_SEQ_NO,  
							$SESS_USERID, '$UPDATE_DATE', '01' )  ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $POH_ID){		
	
		$cmd = " UPDATE PER_WORKFLOW_POSITIONHIS SET
							POH_EFFECTIVEDATE='$SAV_POH_EFFECTIVEDATE', 
							MOV_CODE='$MOV_CODE', 
							POH_ENDDATE='$SAV_POH_ENDDATE', 
							POH_DOCNO='$POH_DOCNO', 
							POH_DOCDATE='$SAV_POH_DOCDATE', 
							POH_POS_NO='$POH_POS_NO', 
							PM_CODE=$PM_CODE, 
							LEVEL_NO=$LEVEL_NO, 
							PL_CODE=$PL_CODE, 
							PN_CODE=$PN_CODE, 
							EP_CODE=$EP_CODE, 
							PT_CODE=$PT_CODE, 
							CT_CODE=$CT_CODE_tmp, 
							PV_CODE=$PV_CODE1, 
							AP_CODE=$AP_CODE, 
							POH_ORGMGT=$POH_ORGMGT, 
							ORG_ID_1=$ORG_ID_1, 
							ORG_ID_2=$ORG_ID_2, 
							ORG_ID_3=$ORG_ID_3, 
							POH_UNDER_ORG1='$POH_UNDER_ORG1_NAME', 
							POH_UNDER_ORG2='$POH_UNDER_ORG2_NAME', 
							POH_UNDER_ORG3='$POH_UNDER_ORG3_NAME', 
							POH_UNDER_ORG4='$POH_UNDER_ORG4_NAME', 
							POH_UNDER_ORG5='$POH_UNDER_ORG5_NAME', 
							POH_ASS_ORG='$POH_ASS_ORG_NAME', 
							POH_ASS_ORG1='$POH_ASS_ORG1_NAME', 
							POH_ASS_ORG2='$POH_ASS_ORG2_NAME', 
							POH_SALARY=$POH_SALARY, 
							POH_SALARY_POS=$POH_SALARY_POS, 
							POH_REMARK='$POH_REMARK', 
							PER_CARDNO='$PER_CARDNO', 
							POH_ORG1='$ORG_NAME_1', 
							POH_ORG2='$ORG_NAME_2', 
							POH_ORG3='$ORG_NAME_3', 
							POH_ORG_TRANSFER='$POH_ORG_TRANSFER', 
							POH_ORG='$POH_ORG', 
							POH_PM_NAME='$PM_NAME', 
							POH_PL_NAME='$PL_NAME', 
							POH_SEQ_NO=$POH_SEQ_NO, 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE POH_ID=$POH_ID";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";		
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $POH_ID){
		$cmd = " select PL_CODE from PER_WORKFLOW_POSITIONHIS where POH_ID=$POH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		
		$cmd = " delete from PER_WORKFLOW_POSITIONHIS where POH_ID=$POH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if

//  UPDATE POH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $POH_ID){
		$POH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_POSITIONHIS where PER_ID=$PER_ID and POH_ID=$POH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_POSITIONHIS set
								POH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and POH_ID=$POH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการดำรงตำแหน่ง [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($POH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE POH_WF_STATUS

	if($command=="COMMAND" && $PER_ID){
		$SAV_POH_EFFECTIVEDATE =  save_date($POH_EFFECTIVEDATE);
		$SAV_POH_ENDDATE =  save_date($POH_ENDDATE);
		$SAV_POH_DOCDATE =  save_date($POH_DOCDATE);
		$POH_ORGMGT = ($POH_ORGMGT == 1)?   $POH_ORGMGT : 2 ;
		$POH_SALARY = (str_replace(",", "", $POH_SALARY)) + 0;
		$POH_SALARY_POS = (str_replace(",", "", $POH_SALARY_POS)) + 0;
		$POH_REMARK = (trim($POH_REMARK))? $POH_REMARK : "-";		
		$POH_REMARK = str_replace("'", "&rsquo;", $POH_REMARK);	
		
		$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
		$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
		$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
		$CT_CODE_tmp = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
		$PV_CODE1 = (trim($PV_CODE1))? "'$PV_CODE1'" : "NULL";
		$AP_CODE = (trim($AP_CODE))? "'$AP_CODE'" : "NULL";
		$ORG_ID_1 = (trim($ORG_ID_1))? $ORG_ID_1 : 1;
		$ORG_ID_2 = (trim($ORG_ID_2))? $ORG_ID_2 : 1;
		$ORG_ID_3 = (trim($ORG_ID_3))? $ORG_ID_3 : 1;		
		$POH_SEQ_NO = (trim($POH_SEQ_NO))? $POH_SEQ_NO : 1;		
		
		if ($PER_TYPE==1) {	
			$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PN_CODE = $EP_CODE = "NULL";
		} elseif ($PER_TYPE==2) {
			$PN_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $EP_CODE = "NULL";
		} elseif ($PER_TYPE==3) {
			$EP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = "NULL";
		}

		$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$POH_ID = $data[max_id] + 1;
		
		$cmd = "  insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, 
							POH_DOCNO, POH_DOCDATE, POH_POS_NO, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, 
							POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							POH_ORG1, POH_ORG2, POH_ORG3, PL_CODE, PN_CODE, EP_CODE, PER_CARDNO, PM_CODE, 
							LEVEL_NO, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORG_TRANSFER, POH_ORG, 
							POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, UPDATE_USER, UPDATE_DATE )
							values ($POH_ID, $PER_ID, '$SAV_POH_EFFECTIVEDATE', '$MOV_CODE', '$SAV_POH_ENDDATE', 
							'$POH_DOCNO', '$SAV_POH_DOCDATE', '$POH_POS_NO', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, 
							$ORG_ID_3, '$POH_UNDER_ORG1_NAME', '$POH_UNDER_ORG2_NAME', '$POH_UNDER_ORG3_NAME', 
							'$POH_UNDER_ORG4_NAME', '$POH_UNDER_ORG5_NAME', '$POH_ASS_ORG_NAME', 
							'$POH_ASS_ORG1_NAME', '$POH_ASS_ORG2_NAME', '$POH_SALARY', '$POH_SALARY_POS', 
							'$POH_REMARK', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $PL_CODE, $PN_CODE, 
							$EP_CODE, '$PER_CARDNO', $PM_CODE, $LEVEL_NO, $PT_CODE, $CT_CODE_tmp, $PV_CODE1, 
							$AP_CODE, '$POH_ORG_TRANSFER', '$POH_ORG', '$PM_NAME', '$PL_NAME', $POH_SEQ_NO,  
							$SESS_USERID, '$UPDATE_DATE' )  ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/51';
				$MOVERENAME = "PER_ATTACHMENT51";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$POH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$POH_ID;
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
			//___echo "TEST : [ $POH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการดำรงตำแหน่ง [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if 

	if(($UPD && $PER_ID && $POH_ID) || ($VIEW && $PER_ID && $POH_ID)){
		$cmd = "	SELECT POH_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, 
							POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, PT_CODE, CT_CODE, PV_CODE, 
							AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, 
							POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, POH_ASS_ORG, POH_ASS_ORG1, 
							POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, 
							POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, UPDATE_USER, 
							UPDATE_DATE, POH_WF_STATUS
				FROM		PER_WORKFLOW_POSITIONHIS 
				WHERE		POH_ID=$POH_ID and POH_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();
	
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], 1);
		$POH_ENDDATE = show_date_format($data[POH_ENDDATE], 1);
		$POH_DOCDATE = show_date_format($data[POH_DOCDATE], 1);
		$POH_DOCNO = $data[POH_DOCNO];
		$POH_POS_NO = $data[POH_POS_NO];
		$LEVEL_NO = $data[LEVEL_NO];
		$POH_SALARY = number_format($data[POH_SALARY], 2, '.', ',');
		$POH_SALARY_POS = number_format($data[POH_SALARY_POS], 2, '.', ',');
		$POH_DOCNO = $data[POH_DOCNO];
		$POH_REMARK = trim($data[POH_REMARK]);
		$CHK_POH_ORGMGT = ($data[POH_ORGMGT] == 1)?  "checked" : "" ;
		$POH_UNDER_ORG1_NAME = $data[POH_UNDER_ORG1];
		$POH_UNDER_ORG2_NAME = $data[POH_UNDER_ORG2];
		$POH_UNDER_ORG3_NAME = $data[POH_UNDER_ORG3];
		$POH_UNDER_ORG4_NAME = $data[POH_UNDER_ORG4];
		$POH_UNDER_ORG5_NAME = $data[POH_UNDER_ORG5];
		$POH_ASS_ORG_NAME = $data[POH_ASS_ORG];		
		$POH_ASS_ORG1_NAME = $data[POH_ASS_ORG1];
		$POH_ASS_ORG2_NAME = $data[POH_ASS_ORG2];	
		$POH_ORG_TRANSFER = trim($data[POH_ORG_TRANSFER]);
		$POH_ORG = $data[POH_ORG];
		$POH_SEQ_NO = $data[POH_SEQ_NO];
		$POH_PM_NAME = trim($data[POH_PM_NAME]);
		$POH_PL_NAME = trim($data[POH_PL_NAME]);
		$UPDATE_USER = $data[UPDATE_USER];
		$POH_WF_STATUS = $data[POH_WF_STATUS];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		// === ชื่อตำแหน่ง
		if (trim($data[PL_CODE])) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='". trim($data[PL_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];
			$PL_CODE = $data[PL_CODE];
		}		
		if (trim($data[PN_CODE])) {
			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='". trim($data[PN_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];
			$PL_CODE = $data[PN_CODE];
		}		
		if (trim($data[EP_CODE])) {
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='". trim($data[EP_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];
			$PL_CODE = $data[EP_CODE];
		}		
		if ($POH_PL_NAME) $PL_NAME = $POH_PL_NAME;

		$ORG_ID_3 = $data[ORG_ID_3];
		if ($ORG_ID_3) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_3' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_3 = $data2[ORG_NAME];
		}
		$ORG_NAME_3 = trim($data[POH_ORG3]);

		$ORG_ID_2 = $data[ORG_ID_2];
		if ($ORG_ID_2) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data2[ORG_NAME];
		}
		$ORG_NAME_2 = trim($data[POH_ORG2]);

		$ORG_ID_1 = $data[ORG_ID_1];
		if ($ORG_ID_1) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];
		}
		$ORG_NAME_1 = trim($data[POH_ORG1]);

		$CT_CODE = $data[CT_CODE];
		if ($CT_CODE) {
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		}
		
		$PV_CODE1 = $data[PV_CODE];
		if ($PV_CODE1) {
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE1' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME1 = $data2[PV_NAME];
		}
		
		$AP_CODE = $data[AP_CODE];
		if ($AP_CODE) {
			$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$AP_NAME = $data2[AP_NAME];
		}

		$PT_CODE = $data[PT_CODE];
		if ($PT_CODE) {
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];
		}

		$PM_CODE = $data[PM_CODE];
		if ($PM_CODE) {
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];
		}		
		if ($POH_PM_NAME) $PM_NAME = $POH_PM_NAME;
		
		$MOV_CODE = $data[MOV_CODE];
		if ($MOV_CODE) {
			$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MOV_NAME = $data2[MOV_NAME];		
		}
	} // end if
	
	if (!$PER_ID) {
		unset($ORG_ID_1);
		unset($ORG_NAME_1);
		unset($ORG_ID_2);
		unset($ORG_NAME_2);
		unset($CT_CODE);
		unset($CT_NAME);	
	
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if			
	}
	
	if( !$UPD && !$VIEW ){
		unset($POH_ID);
		unset($POH_DOCNO);
		unset($POH_DOCDATE);
		unset($POH_REF_NO);
		unset($POH_TYPE);
		unset($POH_EFFECTIVEDATE);
		unset($POH_ENDDATE);
		unset($POH_ORGMGT);
		unset($POH_REMARK);
		unset($LEVEL_NO);
		unset($POH_SALARY);
		$POH_SALARY_POS = 0;
		unset($POH_POS_NO);
		
		unset($MOV_CODE);
		unset($MOV_NAME);
		unset($PL_CODE);
		unset($PL_NAME);
		unset($PV_CODE1);
		unset($PV_NAME1);
		unset($AP_CODE);
		unset($AP_NAME);
		unset($PT_CODE);		
		unset($PT_NAME);
		unset($PM_CODE);
		unset($PM_NAME);
		
		$ORG_ID_1 = $MINISTRY_ID;
		$ORG_NAME_1 = $MINISTRY_NAME;
		if($DEPARTMENT_ID){	$ORG_ID_2 = $DEPARTMENT_ID;	}
		$ORG_NAME_2 = $DEPARTMENT_NAME;		
		unset($ORG_ID_3);
		unset($ORG_NAME_3);
		
		unset($POH_UNDER_ORG1);
		unset($POH_UNDER_ORG1_NAME);
		unset($POH_UNDER_ORG2);
		unset($POH_UNDER_ORG2_NAME);
		unset($POH_UNDER_ORG3);
		unset($POH_UNDER_ORG3_NAME);
		unset($POH_UNDER_ORG4);
		unset($POH_UNDER_ORG4_NAME);
		unset($POH_UNDER_ORG5);
		unset($POH_UNDER_ORG5_NAME);
		unset($POH_ASS_ORG);
		unset($POH_ASS_ORG_NAME);
		unset($POH_ASS_ORG1);
		unset($POH_ASS_ORG1_NAME);
		unset($POH_ASS_ORG2);
		unset($POH_ASS_ORG2_NAME);			
		unset($POH_ORG_TRANSFER);
		unset($POH_ORG);
		unset($POH_PM_NAME);
		unset($POH_PL_NAME);
		unset($POH_SEQ_NO);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>