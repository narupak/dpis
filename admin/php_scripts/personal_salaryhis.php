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
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
	if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
	if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
	if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
	if (!$SAH_OLD_SALARY) $SAH_OLD_SALARY = "NULL";
	$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		
	if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
	if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
	if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
	if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";

	if($command=="ADD" && $PER_ID){
		$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE); 
		if($SAH_DOCDATE) $SAH_DOCDATE = save_date($SAH_DOCDATE); 
		if($SAH_ENDDATE) $SAH_ENDDATE = save_date($SAH_ENDDATE); 
		if($SAH_DOCDATE_EDIT) $SAH_DOCDATE_EDIT = save_date($SAH_DOCDATE_EDIT); 

		if ($SAH_LAST_SALARY=="Y") {
			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
		}

		$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SAH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
						SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
						SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
						LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
						SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, 
						SAH_OLD_SALARY, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, SAH_REF_DOC)
						values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', $MOV_CODE, $SAH_SALARY, '$SAH_DOCNO', 
						'$SAH_DOCDATE', '$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', 
						$SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', 
						'$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_PAY_NO', 
						$SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, 
						'$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ, $SAH_OLD_SALARY, '$SAH_DOCNO_EDIT', 
						'$SAH_DOCDATE_EDIT', '$SAH_REF_DOC') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $SAH_ID){
		$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE); 
		if($SAH_DOCDATE) $SAH_DOCDATE = save_date($SAH_DOCDATE); 
		if($SAH_ENDDATE) $SAH_ENDDATE = save_date($SAH_ENDDATE); 
		if($SAH_DOCDATE_EDIT) $SAH_DOCDATE_EDIT = save_date($SAH_DOCDATE_EDIT); 
		
		if ($SAH_LAST_SALARY=="Y") {
			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
		}

		$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE='$SAH_EFFECTIVEDATE', 
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
																					SAH_SALARY_MIDPOINT=$SAH_SALARY_MIDPOINT, 
																					SAH_KF_YEAR='$SAH_KF_YEAR',   
																					SAH_KF_CYCLE=$SAH_KF_CYCLE, 
																					SAH_TOTAL_SCORE=$SAH_TOTAL_SCORE, 
																					SAH_LAST_SALARY='$SAH_LAST_SALARY',   
																					SM_CODE='$SM_CODE',   
																					SAH_CMD_SEQ=$SAH_CMD_SEQ, 
																					SAH_OLD_SALARY=$SAH_OLD_SALARY, 
																					SAH_DOCNO_EDIT='$SAH_DOCNO_EDIT',   
																					SAH_DOCDATE_EDIT='$SAH_DOCDATE_EDIT',   
																					SAH_REF_DOC='$SAH_REF_DOC',   
																					UPDATE_USER=$SESS_USERID, 
																					UPDATE_DATE='$UPDATE_DATE'
				WHERE SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SAH_ID){
		$cmd = " select MOV_CODE from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MOV_CODE = $data[MOV_CODE];
		
		$cmd = " delete from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if


	if(($UPD && $PER_ID && $SAH_ID) || ($VIEW && $PER_ID && $SAH_ID)){
		$cmd = "	SELECT SAH_ID, SAH_EFFECTIVEDATE, psh.MOV_CODE, pm.MOV_NAME, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, 	SAH_ENDDATE, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, 
							SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, 
							SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, 
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_OLD_SALARY, SAH_DOCNO_EDIT, 
							SAH_DOCDATE_EDIT, SAH_REF_DOC, psh.UPDATE_USER, psh.UPDATE_DATE
				FROM		PER_SALARYHIS psh, PER_MOVMENT pm 
				WHERE		psh.SAH_ID=$SAH_ID and psh.MOV_CODE=pm.MOV_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID];
		$SAH_EFFECTIVEDATE = show_date_format(trim($data[SAH_EFFECTIVEDATE]), 1);
		$SAH_DOCDATE = show_date_format(trim($data[SAH_DOCDATE]), 1);
		$SAH_ENDDATE = show_date_format(trim($data[SAH_ENDDATE]), 1);
		$MOV_CODE = trim($data[MOV_CODE]);
		$MOV_NAME = trim($data[MOV_NAME]);		
		$SAH_SALARY = $data[SAH_SALARY];
		$SAH_DOCNO = trim($data[SAH_DOCNO]);
		$SAH_PERCENT_UP = number_format($data[SAH_PERCENT_UP], 4, '.', ',');
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
		$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT];
		$SAH_KF_YEAR = trim($data[SAH_KF_YEAR]);
		$SAH_KF_CYCLE = $data[SAH_KF_CYCLE];
		$SAH_TOTAL_SCORE = $data[SAH_TOTAL_SCORE];
		$SAH_LAST_SALARY = trim($data[SAH_LAST_SALARY]);
		$SM_CODE = trim($data[SM_CODE]);
		$SAH_CMD_SEQ = $data[SAH_CMD_SEQ];
		$SAH_OLD_SALARY = $data[SAH_OLD_SALARY];
		$SAH_DOCNO_EDIT = $data[SAH_DOCNO_EDIT];
		$SAH_DOCDATE_EDIT = show_date_format(trim($data[SAH_DOCDATE_EDIT]), 1);
		$SAH_REF_DOC = $data[SAH_REF_DOC];
		$UPDATE_USER = $data[UPDATE_USER];

		$cmd ="select EX_NAME from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$EX_NAME = trim($data2[EX_NAME]);

		$cmd ="select SM_NAME from PER_SALARY_MOVMENT where SM_CODE = '$SM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$SM_NAME = trim($data2[SM_NAME]);

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
		unset($SAH_SALARY_MIDPOINT);
		unset($SAH_KF_YEAR);
		unset($SAH_KF_CYCLE);
		unset($SAH_TOTAL_SCORE);
		unset($SAH_LAST_SALARY);
		unset($SAH_CMD_SEQ);
		unset($SAH_OLD_SALARY);
		unset($SAH_DOCNO_EDIT);
		unset($SAH_DOCDATE_EDIT);
		unset($SAH_REF_DOC);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	
		unset($MOV_CODE);
		unset($MOV_NAME);
		unset($EX_CODE);
		unset($EX_NAME);
		unset($SM_CODE);
		unset($SM_NAME);
	} // end if
?>