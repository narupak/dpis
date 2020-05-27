<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");		

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//======================================================================
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
	//======================================================================

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

//======================================================================
//---Add new invest2
	if( $command == "ADD" && trim(!$INV_ID) ){
		$cmd = " select max(INV_ID) as max_id from PER_INVEST2 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$INV_ID = $data[max_id] + 1;
		$INV_APP_RESULT = str_replace("'", "&rsquo;", $INV_APP_RESULT);
		$INV_ID_REF = (trim($INV_ID_REF))? $INV_ID_REF : 'NULL';
		$INV_DATE =  save_date($INV_DATE);
		$INV_APP_DATE =  save_date($INV_APP_DATE);

		$cmd = " insert into PER_INVEST2 
				(INV_ID, INV_NO, INV_DATE, INV_ID_REF, INV_APPEAL, INV_APP_DATE, INV_APP_RESULT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				($INV_ID, '$INV_NO', '$INV_DATE', $INV_ID_REF, $INV_APPEAL, '$INV_APP_DATE', '$INV_APP_RESULT', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "add-$cmd<br>";
		//-------------------
		$cmd = " select  INV_ID  from PER_INVEST2 where INV_ID=$INV_ID";
		$count_result = $db_dpis->send_cmd($cmd);
		if($count_result==0){
			$INV_ID =""; 
		}
		//-------------------

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
		$cmd = " select * from PER_INVEST2DTL where INV_ID=$INV_ID ";
		$count_invdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		echo "parent.refresh_opener('2<::>$INV_ID<::><::>$count_invdtl<::>?UPD=1')";
		echo "</script>";
	}

	if( $command == "UPDATE" && trim($INV_ID) ) {
		$INV_APP_RESULT = str_replace("'", "&rsquo;", $_POST["INV_APP_RESULT"]);
		$INV_ID_REF = (trim($INV_ID_REF))? $INV_ID_REF : 'NULL';
		$temp_date = trim($INV_DATE);
		$INV_DATE =  save_date($INV_DATE);
		$INV_APP_DATE =  save_date($INV_APP_DATE);

		$cmd = " update PER_INVEST2 set  
					INV_DATE='$INV_DATE', INV_ID_REF=$INV_ID_REF, INV_APPEAL=$INV_APPEAL, 
					INV_APP_DATE='$INV_APP_DATE', INV_APP_RESULT='$INV_APP_RESULT',
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where INV_ID=$INV_ID  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
	}

	if($command == "DELETE_COMMAND" && trim($INV_ID) ){
		$cmd = " delete from PER_INVEST2DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST2 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");
		#############
		$INV_ID = "";
		#############
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::>')";
		echo "</script>";
	}
//======================================================================
	if($command == "ADD" && trim(INV_ID) && trim($PER_ID)){
		$INV_ID = trim($INV_ID);
		$cmd = " select INV_ID, PER_ID  from PER_INVEST2DTL  where INV_ID=$INV_ID and PER_ID=$PER_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$PEN_CODE = (trim($PEN_CODE))? "'$PEN_CODE'" : "NULL";
			$cmd = " insert into PER_INVEST2DTL (INV_ID, PER_ID, CRD_CODE, PEN_CODE, UPDATE_USER, UPDATE_DATE) values ($INV_ID, $PER_ID, '$CRD_CODE', $PEN_CODE, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ถูกกล่าวหาการสอบทางวินัย [".$INV_ID." : ".$PER_ID."]");

			$cmd = " select * from PER_INVEST2DTL where INV_ID = $INV_ID ";
			$count_invdtl = $db_dpis->send_cmd($cmd);
			// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
			echo "<script>";
			echo "parent.refresh_opener('3<::>!<::>$PER_ID<::>$count_invdtl<::>')";
			echo "</script>";
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$INV_ID." : ".$PER_ID."]";
		} // endif
	}
	
	if($command == "UPDATE" && trim(INV_ID) && trim($PER_ID)){
		$cmd = " 	update PER_INVEST2DTL set 
				CRD_CODE='$CRD_CODE', PEN_CODE='$PEN_CODE', UPDATE_USER=$SESS_USERID, 
				UPDATE_DATE='$UPDATE_DATE'
				where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้ถูกกล่าวหาการสอบทางวินัย [".$INV_ID." : ".$PER_ID."]");
	}	

	if($command == "DELETE" && trim($PER_ID)){
		$cmd = " delete from PER_INVEST2DTL where INV_ID=$INV_ID and PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ถูกกล่าวหาการสอบทางวินัย [".trim($INV_ID)." : ".$PER_ID."]");

		$cmd = " select * from PER_INVEST2DTL where INV_ID = $INV_ID ";
		$count_invdtl = $db_dpis->send_cmd($cmd);
	}

	if (trim($INV_ID)) {
		$cmd = " 	select 	INV_ID, INV_NO, INV_DATE, INV_ID_REF, INV_APPEAL, INV_APP_DATE, INV_APP_RESULT, DEPARTMENT_ID
				from 	PER_INVEST2 
				where 	trim(INV_ID)=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$INV_NO = trim($data[INV_NO]);
		$INV_ID_REF = trim($data[INV_ID_REF]);
		$INV_APPEAL = trim($data[INV_APPEAL]);
		$INV_APP_RESULT = trim($data[INV_APP_RESULT]);
		
		$INV_DATE = show_date_format($data[INV_DATE], 1);
		$INV_APP_DATE = show_date_format($data[INV_APP_DATE], 1);
		$INV_REF_NO = "";
		if($INV_ID_REF){
			$cmd = " select INV_NO, INV_DATE from PER_INVEST1 where INV_ID=$INV_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$INV_REF_NO = $data2[INV_NO];
		}
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	}	

	if(!$INV_ID){
		$INV_ID = "";
		$INV_NO = "";
		$INV_DATE = "";
		$INV_ID_REF = "";
		$INV_REF_NO = "";
		$INV_APPEAL = "";
		$INV_APP_DATE = "";
		$INV_APP_RESULT = "";

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
	} // end if

	if (($UPD || $VIEW) && $PER_ID){
		$cmd = "	select  	a.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, CRD_CODE, PEN_CODE 
				from 	PER_INVEST2DTL a, PER_PERSONAL b 
				where 	INV_ID=$INV_ID and a.PER_ID=$PER_ID and 
						a.PER_ID=b.PER_ID  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		if (trim($PN_CODE)) {
			$cmd = "	select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME = $data_dpis2[PN_NAME];
		}
		$PER_NAME = trim($data[PN_NAME]) . trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
		$CRD_CODE = trim($data[CRD_CODE]);
		if (trim($CRD_CODE)) {
			$cmd = "	select CRD_NAME, CR_NAME from PER_CRIME_DTL a, PER_CRIME b where a.CRD_CODE='$CRD_CODE' and a.CR_CODE=b.CR_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$CRD_NAME = $data_dpis2[CRD_NAME];		
			$CR_NAME = $data_dpis2[CR_NAME];
		}		
		$PEN_CODE = trim($data[PEN_CODE]);	
		if (trim($PEN_CODE)) {
			$cmd = "	select PEN_NAME from PER_PENALTY where PEN_CODE='$PEN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PEN_NAME = $data_dpis2[PEN_NAME];		
		}	
	}

	if (!$UPD && !$VIEW) {
		$PER_ID = "";
		$PER_NAME = "";
		$CRD_CODE = "";
		$CRD_NAME = "";
		$CR_NAME = "";
		$PEN_CODE = "";
		$PEN_NAME = "";	
	}
?>