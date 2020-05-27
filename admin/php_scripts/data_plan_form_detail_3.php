<?	
	//include("php_scripts/session_start.php");
	//include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID==$SESS_PER_ID && !$RESULT_COMMENT && !$COMPETENCE_COMMENT && !$SALARY_RESULT && !$COUNT_IPIP && !$AGREE_REVIEW1 && !$DIFF_REVIEW2 && !$AGREE_REVIEW2 && !$DIFF_REVIEW2){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	} // end if

	if($command=="UPDATE" && $PROJ_ID && $TR_CODE && trim($TR_CLASS)){
		// CLEAR OLD RECORD
		$cmd = "delete from PER_TRAIN_PROJECT_PERSONAL WHERE PROJ_ID=$PROJ_ID and TR_CODE = '$TR_CODE' and TR_CLASS=$TR_CLASS ";
		$db_dpis->send_cmd($cmd);
		$PER_ID_ARR = explode(',',$SELECTED_LIST);
		foreach($PER_ID_ARR as $key => $PER_ID ) {
			$cmd = " 	insert into PER_TRAIN_PROJECT_PERSONAL
								(PROJ_ID, TR_CODE, TR_CLASS,PER_ID,UPDATE_USER, UPDATE_DATE)
								values
								($PROJ_ID, '$TR_CODE', $TR_CLASS, $PER_ID,$SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo $cmd . "<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มผลสำเร็จของงานที่คาดหวัง [$TR_CODE : $PER_ID : $TR_CLASS]");
		}
	} // end if
	

		$cmd = "	SELECT PER_ID	FROM PER_TRAIN_PROJECT_PERSONAL
							WHERE	PROJ_ID=$PROJ_ID and TR_CODE=$TR_CODE and TR_CLASS=$TR_CLASS";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$temp_id[] = $data[PER_ID];
		}
		$SELECTED_LIST = implode(',',$temp_id);

/*
	if($command=="UPDATE" && $PROJ_ID && $TR_CODE && trim($TR_CLASS)){
		$TR_CLASS = trim($TR_CLASS);
		$cmd = " select TR_CODE, TR_CLASS from PER_TRAIN_PROJECT_DTL where PROJ_ID=$PROJ_ID and TR_CODE = '$TR_CODE' and TR_CLASS=$TR_CLASS ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate == 1){
			if(!$TR_CLASS) $TR_CLASS = "NULL";
			if(!$TRAIN_PLACE) $TRAIN_PLACE = "NULL";
			if(!$TARGET_POSITION) $TARGET_POSITION = "NULL";
			
			if(!$MAX_DAY) $MAX_DAY = "0";
			if(!$BUDGET) $BUDGET = "0";
			if(!$LOCAL_TAX) $LOCAL_TAX = "0";
			if(!$PER_DEVELOP_FUND) $PER_DEVELOP_FUND = "0";
			if(!$OTHER_BUDGET) $OTHER_BUDGET = "0";
			if(!$BUDGET_USED) $BUDGET_USED = "0";
			if(!$LOCAL_TAX_USED) $LOCAL_TAX_USED = "0";
			if(!$PER_DEVELOP_FUND_USED) $PER_DEVELOP_FUND_USED = "0";
			if(!$OTHER_BUDGET_USED) $OTHER_BUDGET_USED = "0";

			$temp_date = explode('/',$START_DATE);
			$START_DATE = ($temp_date[2] - 543) . "-" . $temp_date[1]. "-" . $temp_date[0];
			$temp_date = explode('/',$END_DATE);
			$END_DATE = ($temp_date[2] - 543) . "-" . $temp_date[1]. "-" . $temp_date[0];
			
			$cmd = " UPDATE PER_TRAIN_PROJECT_DTL SET
								TRAIN_PLACE='$TRAIN_PLACE', 
								TARGET_POSITION='$TARGET_POSITION',
								MAX_DAY=$MAX_DAY,
								LEVEL_NO_START = '$LEVEL_NO_START',
								LEVEL_NO_END = '$LEVEL_NO_END',
								START_DATE = '$START_DATE',
								END_DATE = '$END_DATE',
								BUDGET=$BUDGET,
								LOCAL_TAX=$LOCAL_TAX,
								PER_DEVELOP_FUND=$PER_DEVELOP_FUND,
								OTHER_BUDGET=$OTHER_BUDGET,
								BUDGET_USED=$BUDGET_USED,
								LOCAL_TAX_USED=$LOCAL_TAX_USED,
								PER_DEVELOP_FUND_USED=$PER_DEVELOP_FUND_USED,
								OTHER_BUDGET_USED=$OTHER_BUDGET_USED,
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							WHERE PROJ_ID=$PROJ_ID and TR_CODE=$TR_CODE AND TR_CLASS = $TR_CLASS";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			echo $cmd;	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขผลสำเร็จของงานที่คาดหวัง [$TR_CODE : $PG_SEQ : $PFR_NAME : $TR_CLASS]");
		}else{
			$data = $db_dpis->get_array();
			$PG_SEQ = $data[PG_SEQ];
			$TR_CLASS = $data[TR_CLASS];
			
			$err_text = "รหัสข้อมูลซ้ำ [".$PG_SEQ." - ".$TR_CLASS."]";
			$UPD = 1;
		} // end if
	} // end if
	
	if($command=="DELETE" && $PROJ_ID && $TR_CODE){
		$cmd = " select TR_CLASS from PER_TRAIN_PROJECT_DTL where PROJ_ID=$PROJ_ID and TR_CODE=$TR_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_CLASS = $data[TR_CLASS];
		
		$cmd = " delete from PER_TRAIN_PROJECT_DTL where PROJ_ID=$PROJ_ID and TR_CODE=$TR_CODE ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบผลสำเร็จของงานที่คาดหวัง [$TR_CODE : $PG_SEQ : $TR_CLASS]");
	} // end if

	if(($UPD && $PROJ_ID && $TR_CODE) || ($VIEW && $PROJ_ID && $TR_CODE)){
		$cmd = "	SELECT 	TR_CLASS,MAX_DAY,TRAIN_PLACE,TARGET_POSITION,LEVEL_NO_START,LEVEL_NO_END,START_DATE,END_DATE,
							BUDGET,BUDGET_USED,LOCAL_TAX,LOCAL_TAX_USED,PER_DEVELOP_FUND,PER_DEVELOP_FUND_USED,OTHER_BUDGET,OTHER_BUDGET_USED
							FROM		PER_TRAIN_PROJECT_DTL
							WHERE		PROJ_ID=$PROJ_ID and TR_CODE=$TR_CODE and TR_CLASS=$TR_CLASS";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//print_r($data);
	
		$TR_CLASS = $data[TR_CLASS];
		$MAX_DAY = $data[MAX_DAY];
		$TRAIN_PLACE = $data[TRAIN_PLACE];
		$TARGET_POSITION = $data[TARGET_POSITION];
		$LEVEL_NO_START = $data[LEVEL_NO_START];
		$LEVEL_NO_END = $data[LEVEL_NO_END];

		$START_DATE = show_date_format($data[START_DATE], 1);
		$END_DATE = show_date_format($data[END_DATE], 1);
		$BUDGET = $data[BUDGET];
		$LOCAL_TAX = $data[LOCAL_TAX];
		$PER_DEVELOP_FUND = $data[PER_DEVELOP_FUND];
		$OTHER_BUDGET = $data[OTHER_BUDGET];

		$BUDGET_USED = $data[BUDGET_USED];
		$LOCAL_TAX_USED = $data[LOCAL_TAX_USED];
		$PER_DEVELOP_FUND_USED = $data[PER_DEVELOP_FUND_USED];
		$OTHER_BUDGET_USED = $data[OTHER_BUDGET_USED];
		echo "$LEVEL_NO_START $LEVEL_NO_END";
	} // end if
	
	if( !$UPD && !$VIEW && !$err_text){
		//$TR_CODE = "";
		$PG_SEQ = "";
		$cmd = " select max(TR_CLASS) as max_seq from PER_TRAIN_PROJECT_DTL where PROJ_ID=$PROJ_ID AND TR_CODE = '$TR_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$TR_CLASS = $data[max_seq] + 1;
		
		//$TR_CLASS = "";
		$TRAIN_PLACE = "";
		$TARGET_POSITION = "";
		$LEVEL_NO_START = "";
		$LEVEL_NO_END = "";
		$START_DATE = "";
		$END_DATE = "";
			
		$MAX_DAY = "0";
		$BUDGET = "0";
		$LOCAL_TAX = "0";
		$PER_DEVELOP_FUND = "0";
		$OTHER_BUDGET = "0";
		$BUDGET_USED = "0";
		$LOCAL_TAX_USED = "0";
		$PER_DEVELOP_FUND_USED = "0";
		$OTHER_BUDGET_USED = "0";
	} // end if
	*/
	//echo "$START_DATE $END_DATE";
?>