<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 50;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID==$SESS_PER_ID){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	} // end if
	
	if($SUBPAGE==1){
		$TD_SEQ += 0;
		$TRAINING_DESC = trim($TRAINING_DESC);		

		if($command=="ADD" && $PG_ID && trim($TRAINING_DESC)){
			$cmd = " select TD_SEQ from PER_TRAINING_DTL where PG_ID=$PG_ID and trim(TRAINING_DESC)='$TRAINING_DESC' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " select max(TD_ID) as max_id from PER_TRAINING_DTL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$TD_ID = $data[max_id] + 1;
					
				if (!get_magic_quotes_gpc()) {
					$TRAINING_DESC = addslashes(str_replace('"', "&quot;", trim($TRAINING_DESC)));
				}else{
					$TRAINING_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($TRAINING_DESC))));
				} // end if

				$cmd = " 	insert into PER_TRAINING_DTL
									(TD_ID, PG_ID, TD_SEQ, TRAINING_DESC, UPDATE_USER, UPDATE_DATE)
									values
									($TD_ID, $PG_ID, $TD_SEQ, '$TRAINING_DESC', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มหลักสูตรอบรม สัมมนา [$TD_ID : $TD_SEQ : $TRAINING_DESC]");
			}else{
				$data = $db_dpis->get_array();
				$TD_SEQ = $data[TD_SEQ];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$TD_SEQ." - ".$TRAINING_DESC."]";
			} // end if
		} // end if
	
		if($command=="UPDATE" && $PG_ID && $TD_ID && trim($TRAINING_DESC)){
			$cmd = " select TD_SEQ from PER_TRAINING_DTL where PG_ID=$PG_ID and trim(TRAINING_DESC)='$TRAINING_DESC' and TD_ID<>$TD_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				if (!get_magic_quotes_gpc()) {
					$TRAINING_DESC = addslashes(str_replace('"', "&quot;", trim($TRAINING_DESC)));
				}else{
					$TRAINING_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($TRAINING_DESC))));
				} // end if

				$cmd = " UPDATE PER_TRAINING_DTL SET
									TD_SEQ=$TD_SEQ, 
									TRAINING_DESC='$TRAINING_DESC',
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								WHERE PG_ID=$PG_ID and TD_ID=$TD_ID";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขหลักสูตรอบรม สัมมนา [$TD_ID : $TD_SEQ : $TRAINING_DESC]");
			}else{
				$data = $db_dpis->get_array();
				$TD_SEQ = $data[TD_SEQ];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$TD_SEQ." - ".$TRAINING_DESC."]";
				$UPD = 1;
			} // end if
		} // end if
		
		if($command=="DELETE" && $PG_ID && $TD_ID){
			$cmd = " select TD_SEQ, TRAINING_DESC from PER_TRAINING_DTL where PG_ID=$PG_ID and TD_ID=$TD_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TD_SEQ = $data[TD_SEQ];
			$TRAINING_DESC = $data[TRAINING_DESC];
			
			$cmd = " delete from PER_TRAINING_DTL where PG_ID=$PG_ID and TD_ID=$TD_ID ";
			$db_dpis->send_cmd($cmd);
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบหลักสูตรอบรม สัมมนา [$TD_ID : $TD_SEQ : $TRAINING_DESC]");
		} // end if
	
		if(($UPD && $PG_ID && $TD_ID) || ($VIEW && $PG_ID && $TD_ID)){
			$cmd = "	SELECT 	TD_SEQ, TRAINING_DESC
								FROM		PER_TRAINING_DTL
								WHERE		PG_ID=$PG_ID and TD_ID=$TD_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TD_SEQ = $data[TD_SEQ];
			$TRAINING_DESC = $data[TRAINING_DESC];
		} // end if
		
		if( !$UPD && !$VIEW && !$err_text){
			$TD_ID = "";
			$TD_SEQ = "";
			$cmd = " select max(TD_SEQ) as max_seq from PER_TRAINING_DTL where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TD_SEQ = $data[max_seq] + 1;

			$TRAINING_DESC = "";
		} // end if
		
		$cmd = " select PG_END_DATE from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
		$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
	}elseif($SUBPAGE==2){
		if($command == "SAVE"){
			if (!get_magic_quotes_gpc()) {
				$IPIP_DESC = addslashes(str_replace('"', "&quot;", trim($IPIP_DESC)));
			}else{
				$IPIP_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($IPIP_DESC))));
			} // end if

			$cmd = " update PER_PERFORMANCE_GOODNESS set
								IPIP_DESC = '$IPIP_DESC',
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							 where PG_ID=$PG_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end if
		
		$cmd = " select IPIP_DESC from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$IPIP_DESC = $data[IPIP_DESC];
	} // end if
?>