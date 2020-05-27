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
		$PF_CODE = trim($PF_CODE);

		if($command=="ADD" && $PG_ID && trim($PF_CODE)){
			$cmd = " select PF_CODE from PER_PERFORMANCE_DTL where PG_ID=$PG_ID and trim(PF_CODE)='$PF_CODE' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " select max(PD_ID) as max_id from PER_PERFORMANCE_DTL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PD_ID = $data[max_id] + 1;
					
				if (!get_magic_quotes_gpc()) {
					$PERFORMANCE_DESC = addslashes(str_replace('"', "&quot;", trim($PERFORMANCE_DESC)));
				}else{
					$PERFORMANCE_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($PERFORMANCE_DESC))));
				} // end if

				$cmd = " 	insert into PER_PERFORMANCE_DTL
									(PD_ID, PG_ID, PF_CODE, PERFORMANCE_DESC,	UPDATE_USER, UPDATE_DATE)
									values
									($PD_ID, $PG_ID, '$PF_CODE', '$PERFORMANCE_DESC', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มผลงานที่ได้ดำเนินการ [$PD_ID : $PF_CODE : $PF_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$PF_CODE = $data[PF_CODE];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$PF_CODE." - ".$PF_NAME."]";
			} // end if
		} // end if
	
		if($command=="UPDATE" && $PG_ID && $PD_ID && trim($PF_CODE)){
			$cmd = " select PF_CODE from PER_PERFORMANCE_DTL where PG_ID=$PG_ID and trim(PF_CODE)='$PF_CODE' and PD_ID<>$PD_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				if (!get_magic_quotes_gpc()) {
					$PERFORMANCE_DESC = addslashes(str_replace('"', "&quot;", trim($PERFORMANCE_DESC)));
				}else{
					$PERFORMANCE_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($PERFORMANCE_DESC))));
				} // end if

				$cmd = " UPDATE PER_PERFORMANCE_DTL SET
									PF_CODE='$PF_CODE', 
									PERFORMANCE_DESC='$PERFORMANCE_DESC',
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								WHERE PG_ID=$PG_ID and PD_ID=$PD_ID";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขผลงานที่ได้ดำเนินการ [$PD_ID : $PF_CODE : $PF_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$PF_CODE = $data[PF_CODE];
				
				$err_text = "รหัสข้อมูลซ้ำ [".$PF_CODE." - ".$PF_NAME."]";
				$UPD = 1;
			} // end if
		} // end if
		
		if($command=="DELETE" && $PG_ID && $PD_ID){
			$cmd = " select PF_CODE from PER_PERFORMANCE_DTL where PG_ID=$PG_ID and PD_ID=$PD_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PF_CODE = $data[PF_CODE];
			
			$cmd = " select PF_NAME from PER_PERFORMANCE where PF_CODE='$PF_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PF_NAME = $data[PF_NAME];
			
			$cmd = " delete from PER_PERFORMANCE_DTL where PG_ID=$PG_ID and PD_ID=$PD_ID ";
			$db_dpis->send_cmd($cmd);
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบผลงานที่ได้ดำเนินการ [$PD_ID : $PF_CODE : $PF_NAME]");
		} // end if
	
		if(($UPD && $PG_ID && $PD_ID) || ($VIEW && $PG_ID && $PD_ID)){
			$cmd = "	SELECT 	a.PF_CODE, b.PF_NAME, a.PERFORMANCE_DESC
								FROM		PER_PERFORMANCE_DTL a, PER_PERFORMANCE b
								WHERE		a.PG_ID=$PG_ID and a.PD_ID=$PD_ID and a.PF_CODE=b.PF_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PF_CODE = $data[PF_CODE];
			$PF_NAME = $data[PF_NAME];
			$PERFORMANCE_DESC = $data[PERFORMANCE_DESC];
		} // end if
		
		if( !$UPD && !$VIEW && !$err_text){
			$PD_ID = "";
			$PF_CODE = "";
			$PF_NAME = "";
			$PERFORMANCE_DESC = "";
		} // end if
		
		$cmd = " select PG_END_DATE from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
		$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
	}elseif($SUBPAGE==2){
		$GN_CODE = trim($GN_CODE);
		
		if($command=="ADD" && $PG_ID && trim($GN_CODE)){
			$cmd = " select GN_CODE from PER_GOODNESS_DTL where PG_ID=$PG_ID and trim(GN_CODE)='$GN_CODE' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " select max(GD_ID) as max_id from PER_GOODNESS_DTL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$GD_ID = $data[max_id] + 1;
				
				if (!get_magic_quotes_gpc()) {
					$GOODNESS_DESC = addslashes(str_replace('"', "&quot;", trim($GOODNESS_DESC)));
				}else{
					$GOODNESS_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($GOODNESS_DESC))));
				} // end if

				$cmd = " 	insert into PER_GOODNESS_DTL
									(PG_ID, GD_ID, GN_CODE, GOODNESS_DESC, UPDATE_USER, UPDATE_DATE)
									values
									($PG_ID, $GD_ID, '$GN_CODE', '$GOODNESS_DESC', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มพฤติกรรมที่เป็นคุณงามความดี [$GD_ID : $GN_CODE : $GN_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$GN_CODE = trim($data[GN_CODE]);
				
				$err_text = "รหัสข้อมูลซ้ำ [".$GN_CODE." ".$GN_NAME."]";
			} // end if
		} // end if

		if($command=="UPDATE" && $PG_ID && $GD_ID && trim($GN_CODE)){
			$cmd = " select GN_CODE from PER_GOODNESS_DTL where PG_ID=$PG_ID and trim(GN_CODE)='$GN_CODE' and GD_ID<>$GD_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				if (!get_magic_quotes_gpc()) {
					$GOODNESS_DESC = addslashes(str_replace('"', "&quot;", trim($GOODNESS_DESC)));
				}else{
					$GOODNESS_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($GOODNESS_DESC))));
				} // end if

				$cmd = " UPDATE PER_GOODNESS_DTL SET
									GN_CODE='$GN_CODE',
									GOODNESS_DESC='$GOODNESS_DESC',
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								WHERE PG_ID=$PG_ID and GD_ID=$GD_ID";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขพฤติกรรมที่เป็นคุณงามความดี [$GD_ID : $GN_CODE : $GN_NAME]");
			}else{
				$data = $db_dpis->get_array();
				$GN_CODE = trim($data[GN_CODE]);
				
				$err_text = "รหัสข้อมูลซ้ำ [".$GN_CODE." ".$GN_NAME."]";
				$UPD = 1;
			} // end if
		} // end if

		if($command=="DELETE" && $PG_ID && $GD_ID){
			$cmd = " select GN_CODE from PER_GOODNESS_DTL where PG_ID=$PG_ID and GD_ID=$GD_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$GN_CODE = trim($data[GN_CODE]);
			
			$cmd = " select GN_NAME from PER_GOODNESS where trim(GN_CODE)='$GN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$GN_NAME = $data[GN_NAME];
	
			$cmd = " delete from PER_GOODNESS_DTL where PG_ID=$PG_ID and GD_ID=$GD_ID ";
			$db_dpis->send_cmd($cmd);
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบพฤติกรรมที่เป็นคุณงามความดี [$GD_ID : $GN_CODE : $GN_NAME]");
		} // end if

		if(($UPD && $PG_ID && $GD_ID) || ($VIEW && $PG_ID && $GD_ID)){
			$cmd = "	SELECT 	a.GN_CODE, b.GN_NAME, a.GOODNESS_DESC
								FROM		PER_GOODNESS_DTL a, PER_GOODNESS b
								WHERE		a.PG_ID=$PG_ID and a.GD_ID=$GD_ID and a.GN_CODE=b.GN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$GN_CODE = $data[GN_CODE];
			$GN_NAME = $data[GN_NAME];
			$GOODNESS_DESC = $data[GOODNESS_DESC];
		} // end if
		
		$cmd = " select PER_TYPE, POS_ID, POEM_ID, POEMS_ID from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $POS_ID = $data[POS_ID];
		elseif($PER_TYPE==2) $POS_ID = $data[POEM_ID];
		elseif($PER_TYPE==3) $POS_ID = $data[POEMS_ID];
		
		if( !$UPD && !$VIEW && !$err_text){
			$GD_ID = "";
			$GN_CODE = "";
			$GN_NAME = "";		
			$GOODNESS_DESC = "";
		} // end if
	} // end if
?>