<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "DELETE" && $TR_ID){
		$cmd = " select TR_CARDNO from PER_TRANSFER_REQ where TR_ID=$TR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_CARDNO = $data[TR_CARDNO];
		
		$cmd = " delete from PER_TRANSFER_REQ where TR_ID=$TR_ID ";
		$db_dpis->send_cmd($cmd);
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลข้าราชการ/ลูกจ้างขอโอน [$TR_ID : $TR_CARDNO : $TR_NAME]");
		$TR_ID="";
	} // end if
	
	if($command == "CANCEL"){	//&& !$TR_ID
		//--- เคลียร์ค่าใน input สำหรับเพิ่มคนถัดไป
		$PN_NAME="";
		$PN_CODE="";
		$TR_BIRTH_DATE="";
		$TR_ADDRESS="";
		$TR_TEL="";
		$EM_NAME="";
		$EM_CODE="";
		$TR_STARTDATE="";
		$TR_STARTLEVEL=0;
		$TR_STARTORG2="";
		$TR_POSITION="";
		$TR_SALARY="";
		$TR_ORG3="";
		$TR_ORG1="";
		$TR_JOB="";
		$PL_PN_NAME_1="";
		$PL_PN_CODE_1="";
		$ORG_NAME_1="";
		$ORG_ID1="";
		$PL_PN_NAME_2="";
		$PL_PN_CODE_2="";
		$PL_PN_NAME_3="";
		$PL_PN_CODE_3="";
		$ORG_NAME_3="";
		$ORG_ID3="";
		$TR_REASON="";
		$TR_DATE="";
		$TR_CARDNO="";
		$TR_NAME="";
		$TR_GENDER=1;
		$EN_NAME="";
		$EN_CODE="";
		$INS_NAME="";
		$INS_CODE="";
		$TR_STARTPOS="";
		$TR_STARTORG3="";
		$TR_STARTORG1="";
		$TR_LEVEL=0;
		$TR_ORG_TEL="";
		$TR_ORG2="";
		$LEVEL_NO_1=0;
		$LEVEL_NO_2=0;
		$LEVEL_NO_3=0;
		$TR_BEGINDATE="";
	}
?>