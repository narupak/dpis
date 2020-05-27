 <?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	if($PER_ID){
		$ORGATH_ID_1 = $MINISTRY_ID;
		$ORG_NAME_1 = (trim($ORG_NAME_1))? $ORG_NAME_1 : $MINISTRY_NAME;
		$ORG_ID_2 = $DEPARTMENT_ID;
		$ORG_NAME_2 = (trim($ORG_NAME_2))? $ORG_NAME_2 :  $DEPARTMENT_NAME;

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
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
//echo "fdfdf".$PER_ID_DEPARTMENT_ID.":::::::::".$PER_ID."<br>";
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command=="ADD" || $command=="UPDATE") {
		$ACTH_EFFECTIVEDATE = save_date($ACTH_EFFECTIVEDATE); 
		$ACTH_ENDDATE = save_date($ACTH_ENDDATE); 
		$ACTH_DOCDATE = save_date($ACTH_DOCDATE); 
		$ACTH_ASSIGN = (trim($ACTH_ASSIGN))? $ACTH_ASSIGN : "-";		
		$ACTH_ASSIGN = str_replace("'", "&rsquo;", $ACTH_ASSIGN);	
		$ACTH_REMARK = (trim($ACTH_REMARK))? $ACTH_REMARK : "-";		
		$ACTH_REMARK = str_replace("'", "&rsquo;", $ACTH_REMARK);	
		
		$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
		$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
		$PM_CODE_ASSIGN = (trim($PM_CODE_ASSIGN))? "'$PM_CODE_ASSIGN'" : "NULL";
		$LEVEL_NO_ASSIGN = (trim($LEVEL_NO_ASSIGN))? "'$LEVEL_NO_ASSIGN'" : "NULL";
		$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";
		$ACTH_SEQ_NO = (trim($ACTH_SEQ_NO))? $ACTH_SEQ_NO : 1;		
		
		if ($PER_TYPE==1 || $PER_TYPE==5) {	
			$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PN_CODE = $EP_CODE = $TP_CODE = "NULL";
			$PL_CODE_ASSIGN = (trim($PL_CODE_ASSIGN))? "'$PL_CODE_ASSIGN'" : "NULL";	
		} elseif ($PER_TYPE==2) {
			$PN_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $EP_CODE = $TP_CODE = "NULL";
			$PL_CODE_ASSIGN = (trim($PL_CODE_ASSIGN))? "'$PL_CODE_ASSIGN'" : "NULL";	
		} elseif ($PER_TYPE==3) {
			$EP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = $TP_CODE = "NULL";
			$PL_CODE_ASSIGN = (trim($PL_CODE_ASSIGN))? "'$PL_CODE_ASSIGN'" : "NULL";	
		} elseif ($PER_TYPE==4) {
			$TP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = $EP_CODE = "NULL";
			$PL_CODE_ASSIGN = (trim($PL_CODE_ASSIGN))? "'$PL_CODE_ASSIGN'" : "NULL";	
		}
	}

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_ACTINGHIS set AUDIT_FLAG = 'N' where ACTH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_ACTINGHIS set AUDIT_FLAG = 'Y' where ACTH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(ACTH_ID) as max_id from PER_ACTINGHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ACTH_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_ACTINGHIS
			 	(ACTH_ID, PER_ID, PER_CARDNO, ACTH_EFFECTIVEDATE, MOV_CODE, ACTH_ENDDATE, 
				ACTH_DOCNO, ACTH_DOCDATE, ACTH_POS_NO_NAME, ACTH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, 
				ACTH_PM_NAME, ACTH_PL_NAME, ACTH_ORG1, ACTH_ORG2, 	ACTH_ORG3, ACTH_ORG4, 
				ACTH_ORG5, ACTH_POS_NO_NAME_ASSIGN, ACTH_POS_NO_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, PL_CODE_ASSIGN, 
				ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN, ACTH_ORG1_ASSIGN, ACTH_ORG2_ASSIGN, 
				ACTH_ORG3_ASSIGN, ACTH_ORG4_ASSIGN, ACTH_ORG5_ASSIGN, ACTH_ASSIGN, ACTH_REMARK, 
				ACTH_SEQ_NO, UPDATE_USER, UPDATE_DATE )
				values
			 	($ACTH_ID, $PER_ID, $PER_CARDNO, '$ACTH_EFFECTIVEDATE', '$MOV_CODE', '$ACTH_ENDDATE', 
				'$ACTH_DOCNO', '$ACTH_DOCDATE', '$ACTH_POS_NO_NAME', '$ACTH_POS_NO', $PM_CODE, $LEVEL_NO, $PL_CODE, 
				'$PM_NAME', '$PL_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', '$ORG_NAME_4', 
				'$ORG_NAME_5', '$ACTH_POS_NO_NAME_ASSIGN', '$ACTH_POS_NO_ASSIGN', $PM_CODE_ASSIGN, $LEVEL_NO_ASSIGN, $PL_CODE_ASSIGN, 
				'$PM_NAME_ASSIGN', '$PL_NAME_ASSIGN', '$ORG_NAME_1_ASSIGN', '$ORG_NAME_2_ASSIGN', 
				'$ORG_NAME_3_ASSIGN', '$ORG_NAME_4_ASSIGN', '$ORG_NAME_5_ASSIGN', '$ACTH_ASSIGN', 
				'$ACTH_REMARK', $ACTH_SEQ_NO, $SESS_USERID, '$UPDATE_DATE' )  ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง [$PER_ID : $ACTH_ID : $PL_CODE]");
		$ADD_NEXT = 1;
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $ACTH_ID){		
	
		$cmd = " UPDATE PER_ACTINGHIS SET
					ACTH_EFFECTIVEDATE='$ACTH_EFFECTIVEDATE', 
					MOV_CODE='$MOV_CODE', 
					ACTH_ENDDATE='$ACTH_ENDDATE', 
					ACTH_DOCNO='$ACTH_DOCNO', 
					ACTH_DOCDATE='$ACTH_DOCDATE', 
					ACTH_POS_NO_NAME='$ACTH_POS_NO_NAME', 
					ACTH_POS_NO='$ACTH_POS_NO', 
					PM_CODE=$PM_CODE, 
					LEVEL_NO=$LEVEL_NO, 
					PL_CODE=$PL_CODE, 
					ACTH_PM_NAME='$PM_NAME', 
					ACTH_PL_NAME='$PL_NAME', 
					ACTH_ORG1='$ORG_NAME_1', 
					ACTH_ORG2='$ORG_NAME_2', 
					ACTH_ORG3='$ORG_NAME_3', 
					ACTH_ORG4='$ORG_NAME_4', 
					ACTH_ORG5='$ORG_NAME_5', 
					ACTH_POS_NO_NAME_ASSIGN='$ACTH_POS_NO_NAME_ASSIGN', 
					ACTH_POS_NO_ASSIGN='$ACTH_POS_NO_ASSIGN', 
					PM_CODE_ASSIGN=$PM_CODE_ASSIGN, 
					LEVEL_NO_ASSIGN=$LEVEL_NO_ASSIGN, 
					PL_CODE_ASSIGN=$PL_CODE_ASSIGN, 
					ACTH_PM_NAME_ASSIGN='$PM_NAME_ASSIGN', 
					ACTH_PL_NAME_ASSIGN='$PL_NAME_ASSIGN', 
					ACTH_ORG1_ASSIGN='$ORG_NAME_1_ASSIGN', 
					ACTH_ORG2_ASSIGN='$ORG_NAME_2_ASSIGN', 
					ACTH_ORG3_ASSIGN='$ORG_NAME_3_ASSIGN', 
					ACTH_ORG4_ASSIGN='$ORG_NAME_4_ASSIGN', 
					ACTH_ORG5_ASSIGN='$ORG_NAME_5_ASSIGN', 
					ACTH_ASSIGN='$ACTH_ASSIGN', 
					ACTH_REMARK='$ACTH_REMARK', 
					PER_CARDNO=$PER_CARDNO, 
					ACTH_SEQ_NO=$ACTH_SEQ_NO, 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE ACTH_ID=$ACTH_ID";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";		
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง [$PER_ID : $ACTH_ID : $PL_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $ACTH_ID){
		$cmd = " select PL_CODE from PER_ACTINGHIS where ACTH_ID=$ACTH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		
		$cmd = " delete from PER_ACTINGHIS where ACTH_ID=$ACTH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรักษาราชการแทน/รักษาการในตำแหน่ง [$PER_ID : $ACTH_ID : $PL_CODE]");
	} // end if

	if(($UPD && $PER_ID && $ACTH_ID) || ($VIEW && $PER_ID && $ACTH_ID)){
		$cmd = "	SELECT 		ACTH_EFFECTIVEDATE, MOV_CODE, ACTH_ENDDATE, 
				ACTH_DOCNO, ACTH_DOCDATE, ACTH_POS_NO_NAME, ACTH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, 
				ACTH_PM_NAME, ACTH_PL_NAME, ACTH_ORG1, ACTH_ORG2, 	ACTH_ORG3, ACTH_ORG4, 
				ACTH_ORG5, ACTH_POS_NO_NAME_ASSIGN, ACTH_POS_NO_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, PL_CODE_ASSIGN, 
				ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN, ACTH_ORG1_ASSIGN, ACTH_ORG2_ASSIGN, 
				ACTH_ORG3_ASSIGN, ACTH_ORG4_ASSIGN, ACTH_ORG5_ASSIGN, ACTH_ASSIGN, ACTH_REMARK, 
				ACTH_SEQ_NO, UPDATE_USER, UPDATE_DATE  
				FROM		PER_ACTINGHIS 
				WHERE		ACTH_ID=$ACTH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();
	
		$PL_NAME = $PM_NAME = $PL_NAME_ASSIGN = $PM_NAME_ASSIGN = "";
		$ACTH_EFFECTIVEDATE = show_date_format(trim($data[ACTH_EFFECTIVEDATE]), 1);
		$ACTH_ENDDATE = show_date_format(trim($data[ACTH_ENDDATE]), 1);
		$ACTH_DOCDATE = show_date_format(trim($data[ACTH_DOCDATE]), 1);
		$ACTH_DOCNO = $data[ACTH_DOCNO];
		$ACTH_POS_NO_NAME = $data[ACTH_POS_NO_NAME];
		$ACTH_POS_NO = $data[ACTH_POS_NO];
		$LEVEL_NO = $data[LEVEL_NO];
		$ACTH_PM_NAME = trim($data[ACTH_PM_NAME]);
		$ACTH_PL_NAME = trim($data[ACTH_PL_NAME]);
		$ORG_NAME_1 = trim($data[ACTH_ORG1]);
		$ORG_NAME_2 = trim($data[ACTH_ORG2]);
		$ORG_NAME_3 = trim($data[ACTH_ORG3]);
		$ORG_NAME_4 = trim($data[ACTH_ORG4]);
		$ORG_NAME_5 = trim($data[ACTH_ORG5]);
		$ACTH_POS_NO_NAME_ASSIGN = $data[ACTH_POS_NO_NAME_ASSIGN];
		$ACTH_POS_NO_ASSIGN = $data[ACTH_POS_NO_ASSIGN];
		$LEVEL_NO_ASSIGN = $data[LEVEL_NO_ASSIGN];
		$ACTH_PM_NAME_ASSIGN = trim($data[ACTH_PM_NAME_ASSIGN]);
		$ACTH_PL_NAME_ASSIGN = trim($data[ACTH_PL_NAME_ASSIGN]);
		
		$ORG_NAME_1_ASSIGN = trim($data[ACTH_ORG1_ASSIGN]);
		$ORG_NAME_2_ASSIGN = trim($data[ACTH_ORG2_ASSIGN]);
		$ORG_NAME_3_ASSIGN = trim($data[ACTH_ORG3_ASSIGN]);
		$ORG_NAME_4_ASSIGN = trim($data[ACTH_ORG4_ASSIGN]);
		$ORG_NAME_5_ASSIGN = trim($data[ACTH_ORG5_ASSIGN]);
		
		$ACTH_ASSIGN = trim($data[ACTH_ASSIGN]);
		$ACTH_REMARK = trim($data[ACTH_REMARK]);
		$ACTH_SEQ_NO = $data[ACTH_SEQ_NO];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$PL_CODE = trim($data[PL_CODE]);
		if ($PL_CODE) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];
		}		
		if ($ACTH_PL_NAME) $PL_NAME = $ACTH_PL_NAME;

		$PM_CODE = trim($data[PM_CODE]);
		if ($PM_CODE) {
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];
		}		
		if ($ACTH_PM_NAME) $PM_NAME = $ACTH_PM_NAME;
		
		$PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
		if ($PL_CODE_ASSIGN) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME_ASSIGN = $data2[PL_NAME];
		}		
		if ($ACTH_PL_NAME_ASSIGN) $PL_NAME_ASSIGN = $ACTH_PL_NAME_ASSIGN;

		$PM_CODE_ASSIGN = trim($data[PM_CODE_ASSIGN]);
		if ($PM_CODE_ASSIGN) {
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME_ASSIGN = $data2[PM_NAME];
		}		
		if ($ACTH_PM_NAME_ASSIGN) $PM_NAME_ASSIGN = $ACTH_PM_NAME_ASSIGN;
		
		$MOV_CODE = $data[MOV_CODE];
		if ($MOV_CODE) {
			$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MOV_NAME = $data2[MOV_NAME];		
		}

		$cmd ="select COM_ID from PER_COMMAND where COM_NO = '$ACTH_DOCNO' ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$COM_ID = trim($data2[COM_ID]);

/*******************************************************************************/
		$PATH_MAIN = "../attachments";	

		$file_namedb = "PER_COMMAND_".$COM_ID;
		$FIRST_SUBDIR = $PATH_MAIN;
		$SECOND_SUBDIR = $FIRST_SUBDIR.'/'."PER_COMMAND";
		$FINAL_PATH = $SECOND_SUBDIR.'/'.$COM_ID;

/*			$pos = strrpos($filename_name, ".");	
			$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
			$file_ext = substr($filename_name, ($pos+1));
			$filename_encode = $file_namedb."_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;
*/
		$FILE_PATH = $FINAL_PATH;
		$real_filename = "";
		$cmd ="select * from EDITOR_ATTACHMENT where REAL_FILENAME like '$file_namedb%' ";
		$count_atta = $db->send_cmd($cmd);
		if ($count_atta) {
			$data2 = $db->get_array();
			$ATTAFILE_ID = trim($data2[ID]);
			$ATTAFILE_REAL_NAME = trim($data2[REAL_FILENAME]);
			$real_filename = $ATTAFILE_REAL_NAME;
			$ATTAFILE_SHOW_NAME = trim($data2[SHOW_FILENAME]);
			$ATTAFILE_DESCRIPTION = trim($data2[DESCRIPTION]);
			$ATTAFILE_FILE_SIZE = trim($data2[FILE_SIZE]);
			$ATTAFILE_USER_ID = trim($data2[USER_ID]);
		}
/*******************************************************************************/

	} // end if
	
	if (!$PER_ID) {
		unset($ORG_NAME_1);
		unset($ORG_NAME_2);
	
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
		unset($ACTH_ID);
		unset($ACTH_DOCNO);
		unset($ACTH_DOCDATE);
		if (!($form1command == "personal" && ($ACTH_EFFECTIVEDATE || $ACTH_ENDDATE || $MOV_CODE || $MOV_NAME))) {
			unset($ACTH_EFFECTIVEDATE);
			unset($ACTH_ENDDATE);
			unset($MOV_CODE);
			unset($MOV_NAME);
		}
		unset($ACTH_ASSIGN);
		unset($ACTH_REMARK);
		unset($LEVEL_NO);
		unset($LEVEL_NO_ASSIGN);
		unset($ACTH_POS_NO);
		unset($ACTH_POS_NO_ASSIGN);
		
		unset($PL_CODE);
		unset($PL_NAME);
		unset($PM_CODE);
		unset($PM_NAME);
		unset($PL_CODE_ASSIGN);
		unset($PL_NAME_ASSIGN);
		unset($PM_CODE_ASSIGN);
		unset($PM_NAME_ASSIGN);
		
		$ORG_NAME_1 = $MINISTRY_NAME;
		$ORG_NAME_2 = $DEPARTMENT_NAME;		
		unset($ORG_NAME_3);
		unset($ORG_NAME_4);
		unset($ORG_NAME_5);
		
		$ORG_ID_1_ASSIGN = $MINISTRY_ID;
		$ORG_ID_2_ASSIGN =  $DEPARTMENT_ID;
		$ORG_NAME_1_ASSIGN = $MINISTRY_NAME;
		$ORG_NAME_2_ASSIGN = $DEPARTMENT_NAME;
		unset($ORG_NAME_3_ASSIGN);
		unset($ORG_NAME_4_ASSIGN);
		unset($ORG_NAME_5_ASSIGN);
		
		unset($ACTH_PM_NAME);
		unset($ACTH_PL_NAME);
		unset($ACTH_PM_NAME_ASSIGN);
		unset($ACTH_PL_NAME_ASSIGN);
		unset($ACTH_SEQ_NO);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
//echo $PER_ID."--".$MINISTRY_NAME."--".$DEPARTMENT_NAME;
?>