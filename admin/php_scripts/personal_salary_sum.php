<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

        
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if( !$current_page ) $current_page = 1;
	
	if( !$current_page1 ) $current_page1 = 1;

	if( !$current_page2 ) $current_page2 = 1;
	
	if( !$current_page3 ) $current_page3 = 1;
	
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

	if ($command == "SETFLAG1") {
		$setflagaudit1 =  implode(",",$list_audit_id1);
		//echo "current : $current_list1 <br>";
		//echo "setflag : $setflagaudit1 <br>";

		$cmd = " update PER_SALARYHIS set AUDIT_FLAG = 'N' where SAH_ID in (".stripslashes($current_list1).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update PER_SALARYHIS set AUDIT_FLAG = 'Y' where SAH_ID in (".stripslashes($setflagaudit1).") ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูลเงินเดือน");
	}

	if ($command == "SETFLAG2") {
		$setflagaudit2 =  implode(",",$list_audit_id2);
		//echo "current : $current_list2 <br>";
		//echo "setflag : $setflagaudit2 <br>";

		$cmd = " update PER_POS_MGTSALARYHIS set AUDIT_FLAG = 'N' where PMH_ID in (".stripslashes($current_list2).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_POS_MGTSALARYHIS set AUDIT_FLAG = 'Y' where PMH_ID in (".stripslashes($setflagaudit2).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบเงินตามตำแหน่ง");
	}
	
	if ($command == "SETFLAG3") {
		$setflagaudit3 =  implode(",",$list_audit_id3);
		//echo "current : $current_list3 <br>";
		//echo "setflag : $setflagaudit3 <br>";

		$cmd = " update PER_EXTRAHIS set  AUDIT_FLAG = 'N' where EXH_ID in (".stripslashes($current_list3).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();

		$cmd = " update PER_EXTRAHIS set AUDIT_FLAG = 'Y' where EXH_ID in (".stripslashes($setflagaudit3).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูลข้อมูลเงินเพิ่มพิเศษ");
	}

	if($command=="DELETESAH" && $PER_ID && $SAH_ID){
		$cmd = " select MOV_CODE from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MOV_CODE = $data[MOV_CODE];
		
		$cmd = " delete from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if

	if($command=="DELETEEXH" && $PER_ID && $EXH_ID){
		$cmd = " select EX_CODE from PER_EXTRAHIS where EXH_ID=$EXH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = "delete from PER_EXTRAHIS where EXH_ID=$EXH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเพิ่มพิเศษ [$PER_ID : $EXH_ID : $EX_CODE]");
	} // end if

	if($command == "DELETEPMH" && $PER_ID && $PMH_ID){
		$cmd = " select EX_CODE from PER_POS_MGTSALARYHIS where PMH_ID=$PMH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = " delete from PER_POS_MGTSALARYHIS where PMH_ID=$PMH_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินประจำตำแหน่ง [$PER_ID : $PMH_ID : $EX_CODE]");
	}
?>