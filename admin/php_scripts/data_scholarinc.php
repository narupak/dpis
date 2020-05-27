<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($SC_ID){
		//if($DPISDB=="odbc"){
			$cmd = " select 	SCH_NAME, SC_STARTDATE, SC_ENDDATE
					from		PER_SCHOLAR a, PER_SCHOLARSHIP b
					where 	SC_ID=$SC_ID and a.SCH_CODE=b.SCH_CODE   ";
		//}elseif($DPISDB=="oci8"){
		//} // end if
		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$SCH_NAME = $data[SCH_NAME];
		$SC_STARTDATE = show_date_format($data[SC_STARTDATE], 1);
		$SC_ENDDATE_OLD = show_date_format($data[SC_ENDDATE], 1);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="ADD" && $SC_ID && $SCI_BEGINDATE){
		$SCI_BEGINDATE =  save_date($SCI_BEGINDATE);
		$SC_ENDDATE =  save_date($SC_ENDDATE);
	
		$cmd = " insert into PER_SCHOLARINC
					(SC_ID, SCI_BEGINDATE, SC_ENDDATE, UPDATE_USER, UPDATE_DATE)
					values
					($SC_ID, '$SCI_BEGINDATE', '$SC_ENDDATE', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการขยายระยะเวลาศึกษา [$PER_ID : $SC_ID : $SCI_BEGINDATE]");
	} // end if

	if($command=="UPDATE" && $SC_ID && $SCI_BEGINDATE){
		$SCI_BEGINDATE =  save_date($SCI_BEGINDATE);
		$SC_ENDDATE =  save_date($SC_ENDDATE);
			
		$cmd = " UPDATE PER_SCHOLARINC SET
					SCI_BEGINDATE='$SCI_BEGINDATE', SC_ENDDATE='$SC_ENDDATE', 
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
				WHERE SC_ID=$SC_ID and SCI_BEGINDATE like '$SCI_BEGINDATE%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการขยายระยะเวลาศึกษา [$PER_ID : $SC_ID : $SCI_BEGINDATE]");
	} // end if
	
	if($command=="DELETE" && $SC_ID){
		$SCI_BEGINDATE =  save_date($SCI_BEGINDATE);

		$cmd = " delete from PER_SCHOLARINC where SC_ID=$SC_ID and SCI_BEGINDATE='$SCI_BEGINDATE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการขยายระยะเวลาศึกษา [$PER_ID : $SC_ID : $SCI_BEGINDATE]");
	} // end if

	if(($UPD && $SC_ID && $SCI_BEGINDATE) || ($VIEW && $SC_ID && SCI_BEGINDATE)){
		$cmd = "	SELECT 		SCI_BEGINDATE, SC_ENDDATE 
				FROM		PER_SCHOLARINC 
				WHERE		SC_ID=$SC_ID and SCI_BEGINDATE='$SCI_BEGINDATE' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SCI_BEGINDATE = show_date_format($data[SCI_BEGINDATE], 1);
		$SC_ENDDATE = show_date_format($data[SC_ENDDATE], 1);

	} // end if
	
	if (!$UPD && !$VIEW ) {
		unset($SCI_BEGINDATE);
		unset($SC_ENDDATE);
		unset($SC_STARTDATE);
		unset($SC_ENDDATE_OLD);
		unset($SCH_NAME);
	} // end if
?>