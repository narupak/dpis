<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$TRN_DAY) $TRN_DAY = "NULL";
		
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
		$cmd = " update PER_TRAINING set AUDIT_FLAG = 'N' where TRN_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_TRAINING set AUDIT_FLAG = 'Y' where TRN_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$TRN_STARTDATE = save_date($TRN_STARTDATE); 
		$TRN_ENDDATE = save_date($TRN_ENDDATE); 
		$TRN_DOCDATE = save_date($TRN_DOCDATE); 
		$TRN_BOOK_DATE = save_date($TRN_BOOK_DATE); 
		$TRN_BACKDATE = save_date($TRN_BACKDATE); 
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'" : "NULL";
		$CT_CODE_FUND = (trim($CT_CODE_FUND))? "'" . trim($CT_CODE_FUND) . "'" : "NULL";
		if (!$TRN_PRINT) $TRN_PRINT = "NULL";
		if (!$TRN_COST) $TRN_COST = "NULL";
		
		if($DPISDB=="oci8")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID=$PER_ID and  LEFT(TRN_STARTDATE,10)='$TRN_STARTDATE' and 
								LEFT(TRN_ENDDATE,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(TRN_ID) as max_id from PER_TRAINING ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TRN_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_TRAINING	(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND, CT_CODE_FUND, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							TRN_DAY, TRN_REMARK, TRN_PASS, TRN_DOCNO, TRN_DOCDATE, TRN_BOOK_NO, TRN_BOOK_DATE, 
							TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE, TRN_PRINT, 
							TRN_COST, TRN_BACKDATE, TRN_FLAG, TRN_SHORTNAME)
							values ($TRN_ID, $PER_ID, '$TRN_TYPE', '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', $CT_CODE, '$TRN_FUND', $CT_CODE_FUND, '$PER_CARDNO', $SESS_USERID, 
							'$UPDATE_DATE', $TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_DOCNO', '$TRN_DOCDATE', '$TRN_BOOK_NO', 
							'$TRN_BOOK_DATE', '$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', '$TRN_DEGREE_RECEIVE', '$TRN_POINT', 
							'$TRN_OBJECTIVE', $TRN_PRINT, $TRN_COST, '$TRN_BACKDATE', '$TRN_FLAG', '$TRN_SHORTNAME') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $TRN_ID){
		$TRN_STARTDATE = save_date($TRN_STARTDATE); 
		$TRN_ENDDATE = save_date($TRN_ENDDATE); 
		$TRN_DOCDATE = save_date($TRN_DOCDATE); 
		$TRN_BOOK_DATE = save_date($TRN_BOOK_DATE); 
		$TRN_BACKDATE = save_date($TRN_BACKDATE); 
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'" : "NULL";
		$CT_CODE_FUND = (trim($CT_CODE_FUND))? "'" . trim($CT_CODE_FUND) . "'" : "NULL";	
		if (!$TRN_PRINT) $TRN_PRINT = "NULL";
		if (!$TRN_COST) $TRN_COST = "NULL";
		
		if($DPISDB=="oci8")    
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID=$PER_ID and  LEFT(TRN_STARTDATE,10)='$TRN_STARTDATE' and 
								LEFT(TRN_ENDDATE,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_TRAINING SET
							TRN_TYPE='$TRN_TYPE', 
							TR_CODE='$TR_CODE', 
							TRN_NO='$TRN_NO', 
							TRN_STARTDATE='$TRN_STARTDATE', 
							TRN_ENDDATE='$TRN_ENDDATE', 
							TRN_ORG='$TRN_ORG', 
							TRN_PLACE='$TRN_PLACE', 
							CT_CODE=$CT_CODE, 
							TRN_FUND='$TRN_FUND', 
							CT_CODE_FUND=$CT_CODE_FUND, 
							PER_CARDNO='$PER_CARDNO', 
							TRN_DAY=$TRN_DAY, 
							TRN_REMARK='$TRN_REMARK', 
							TRN_PASS=$TRN_PASS, 
							TRN_DOCNO='$TRN_DOCNO', 
							TRN_DOCDATE='$TRN_DOCDATE', 
							TRN_BOOK_NO='$TRN_BOOK_NO', 
							TRN_BOOK_DATE='$TRN_BOOK_DATE', 
							TRN_PROJECT_NAME='$TRN_PROJECT_NAME', 
							TRN_COURSE_NAME='$TRN_COURSE_NAME', 
							TRN_DEGREE_RECEIVE='$TRN_DEGREE_RECEIVE', 
							TRN_POINT='$TRN_POINT', 
							TRN_OBJECTIVE='$TRN_OBJECTIVE', 
							TRN_PRINT=$TRN_PRINT, 
							TRN_COST=$TRN_COST, 
							TRN_BACKDATE='$TRN_BACKDATE', 
							TRN_FLAG='$TRN_FLAG', 
							TRN_SHORTNAME='$TRN_SHORTNAME', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE TRN_ID=$TRN_ID";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $TRN_ID){
		$cmd = " select TR_CODE from PER_TRAINING where TRN_ID=$TRN_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_CODE = $data[TR_CODE];
		
		$cmd = " delete from PER_TRAINING where TRN_ID=$TRN_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
	} // end if

	if(($UPD && $PER_ID && $TRN_ID) || ($VIEW && $PER_ID && $TRN_ID)){
		$cmd = " select	TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
										CT_CODE, TRN_FUND, CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, TRN_DAY, TRN_REMARK, 
										TRN_PASS, TRN_DOCNO, TRN_DOCDATE, TRN_BOOK_NO, TRN_BOOK_DATE, TRN_PROJECT_NAME, TRN_COURSE_NAME, 
										TRN_DEGREE_RECEIVE, TRN_POINT, TRN_OBJECTIVE, TRN_PRINT, TRN_COST, TRN_BACKDATE, TRN_FLAG, TRN_SHORTNAME
						from		PER_TRAINING
						where	TRN_ID=$TRN_ID  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TRN_TYPE = $data[TRN_TYPE];
		$TRN_NO = $data[TRN_NO];
		$TRN_STARTDATE = show_date_format(trim($data[TRN_STARTDATE]), 1);
		$TRN_ENDDATE = show_date_format(trim($data[TRN_ENDDATE]), 1);
		$TRN_ORG = $data[TRN_ORG];
		$TRN_PLACE = $data[TRN_PLACE];
		$TRN_FUND = $data[TRN_FUND];
		$TRN_DAY = $data[TRN_DAY];
		$TRN_REMARK = $data[TRN_REMARK];
		$TRN_PASS = $data[TRN_PASS];
		$TRN_DOCNO = $data[TRN_DOCNO];
		$TRN_DOCDATE = show_date_format(trim($data[TRN_DOCDATE]), 1);
		$TRN_BOOK_NO = $data[TRN_BOOK_NO];
		$TRN_BOOK_DATE = show_date_format(trim($data[TRN_BOOK_DATE]), 1);
		$TRN_PROJECT_NAME = $data[TRN_PROJECT_NAME];
		$TRN_COURSE_NAME = $data[TRN_COURSE_NAME];
		$TRN_DEGREE_RECEIVE = $data[TRN_DEGREE_RECEIVE];
		$TRN_POINT = $data[TRN_POINT];
		$TRN_OBJECTIVE = $data[TRN_OBJECTIVE];
		$TRN_PRINT = $data[TRN_PRINT];
		$TRN_COST = $data[TRN_COST];
		$TRN_BACKDATE = show_date_format(trim($data[TRN_BACKDATE]), 1);
		$TRN_FLAG = $data[TRN_FLAG];
		$TRN_SHORTNAME = $data[TRN_SHORTNAME];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$TR_CODE = $data[TR_CODE];
		if($TR_CODE){
			$cmd = " select TR_NAME from PER_TRAIN where TR_CODE='$TR_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TR_NAME = $data2[TR_NAME];
		} // end if

		$CT_NAME = $CT_NAME_FUND = "";
		$CT_CODE = $data[CT_CODE];
		if($CT_CODE){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		} // end if

		$CT_CODE_FUND = $data[CT_CODE_FUND];
		if($CT_CODE_FUND){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_FUND' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME_FUND = $data2[CT_NAME];
		} // end if
//		echo "$CT_CODE / $CT_NAME - $CT_CODE_FUND / $CT_NAME_FUND <br>";
		
		$cmd ="select COM_ID from PER_COMMAND where COM_NO = '$TRN_DOCNO' ";
		$db->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		$data2 = $db->get_array();
		$COM_ID = trim($data2[COM_ID]);

/*******************************************************************************/
		$PATH_MAIN = "../attachments";	

		$file_namedb = "PER_COMMAND_".$COM_ID;
		$FIRST_SUBDIR = $PATH_MAIN;
		$SECOND_SUBDIR = $FIRST_SUBDIR.'/'."PER_COMMAND";
		$FINAL_PATH = $SECOND_SUBDIR.'/'.$COM_ID;

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
//		echo "FILE_PATH=$FILE_PATH , real_filename=$real_filename<br>";
/*******************************************************************************/

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($TRN_ID);
		unset($TRN_TYPE);
		unset($TRN_NO);		
		unset($TRN_STARTDATE);
		unset($TRN_ENDDATE);
		unset($TRN_ORG);
		unset($TRN_PLACE);
		unset($TRN_FUND);
		unset($TRN_DAY);
		unset($TRN_REMARK);
		unset($TRN_PASS);
		unset($TRN_DOCNO);
		unset($TRN_DOCDATE);
		unset($TRN_BOOK_NO);
		unset($TRN_BOOK_DATE);
		unset($TRN_PROJECT_NAME);
		unset($TRN_COURSE_NAME);
		unset($TRN_DEGREE_RECEIVE);
		unset($TRN_POINT);
		unset($TRN_OBJECTIVE);
		$TRN_PRINT = 1;
		unset($TRN_COST);
		unset($TRN_BACKDATE);
		unset($TRN_FLAG);
		unset($TRN_SHORTNAME);

		$CT_CODE = "140";
		$CT_NAME = "ไทย";
		unset($CT_CODE_FUND);
		unset($CT_NAME_FUND);
		unset($TR_CODE);
		unset($TR_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>