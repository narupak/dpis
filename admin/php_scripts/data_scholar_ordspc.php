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
		$SC_ENDDATE = show_date_format($data[SC_ENDDATE], 1);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="ADD" && $SC_ID && $SO_STARTDATE){
		$SO_STARTDATE =  save_date($SO_STARTDATE);
		$SO_ENDDATE =  save_date($SO_ENDDATE);
		$CT_CODE = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
	
		$cmd = " insert into PER_SCHOLAR_ORDSPC (SC_ID, SO_TYPE, SO_STARTDATE, SO_ENDDATE, SO_MAJOR_DESC, 
						SO_PLACE, CT_CODE, SO_FUND, SO_REMARK, UPDATE_USER, UPDATE_DATE)
						values ($SC_ID, '$SO_TYPE', '$SO_STARTDATE', '$SO_ENDDATE', '$SO_MAJOR_DESC', '$SO_PLACE',
						$CT_CODE, '$SO_FUND', '$SO_REMARK', $SESS_USERID, '$UPDATE_DATE')   ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลคำขอพิเศษ [$PER_ID : $SC_ID : $SO_TYPE : $SO_STARTDATE]");
	} // end if

	if($command=="UPDATE" && $SC_ID && $SO_STARTDATE){
		$SO_STARTDATE =  save_date($SO_STARTDATE);
		$SO_ENDDATE =  save_date($SO_ENDDATE);
		$CT_CODE = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
			
		$cmd = " UPDATE PER_SCHOLAR_ORDSPC SET
					SO_STARTDATE='$SO_STARTDATE', 
					SO_ENDDATE='$SO_ENDDATE', 
					SO_MAJOR_DESC='$SO_MAJOR_DESC', 
					SO_PLACE='$SO_PLACE', 
					CT_CODE=$CT_CODE, 
					SO_FUND='$SO_FUND', 
					SO_REMARK='$SO_REMARK', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE SC_ID=$SC_ID and SO_TYPE = '$SO_TYPE' and SO_STARTDATE = '$SO_STARTDATE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลคำขอพิเศษ [$PER_ID : $SC_ID : $SO_TYPE : $SO_STARTDATE]");
	} // end if
	
	if($command=="DELETE" && $SC_ID){
		$SO_STARTDATE =  save_date($SO_STARTDATE);

		$cmd = " delete from PER_SCHOLAR_ORDSPC where SC_ID=$SC_ID and SO_TYPE = '$SO_TYPE' and SO_STARTDATE='$SO_STARTDATE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลคำขอพิเศษ [$PER_ID : $SC_ID : $SO_TYPE : $SO_STARTDATE]");
	} // end if

	if(($UPD && $SC_ID && $SO_STARTDATE) || ($VIEW && $SC_ID && SO_STARTDATE)){
		$cmd = "	SELECT 		SO_STARTDATE, SO_ENDDATE, SO_MAJOR_DESC, SO_PLACE, CT_CODE, SO_FUND, SO_REMARK 
				FROM		PER_SCHOLAR_ORDSPC 
				WHERE		SC_ID=$SC_ID and SO_TYPE = '$SO_TYPE' and SO_STARTDATE='$SO_STARTDATE' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SO_STARTDATE = show_date_format($data[SO_STARTDATE], 1);
		$SO_ENDDATE = show_date_format($data[SO_ENDDATE], 1);
		$SO_MAJOR_DESC = trim($data[SO_MAJOR_DESC]);
		$SO_PLACE = trim($data[SO_PLACE]);
		$SO_FUND = trim($data[SO_FUND]);
		$SO_REMARK = trim($data[SO_REMARK]);

		$CT_CODE = trim($data[CT_CODE]);
		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = trim($data2[CT_NAME]);		

	} // end if
	
	if (!$UPD && !$VIEW ) {
		$SO_TYPE = 1;
		unset($SO_STARTDATE);
		unset($SO_ENDDATE);
		unset($SO_MAJOR_DESC);
		unset($SO_PLACE);
		unset($CT_CODE);
		unset($CT_NAME);
		unset($SO_FUND);
		unset($SO_REMARK);

		unset($SC_STARTDATE);
		unset($SC_ENDDATE);
		unset($SCH_NAME);
	} // end if
?>