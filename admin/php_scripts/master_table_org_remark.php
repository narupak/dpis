 <?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
//	echo "2.. command=$command :::  $AL_YEAR - $SEQ_NO :: (UPD=$UPD)<br>";

	if(!$search_al_year)	$search_al_year = $KPI_BUDGET_YEAR;		
	if(!$search_al_cycle)	$search_al_cycle = $KPI_CYCLE; 
	
	if($command=="SEARCH"){//ค้นหา เงื่อนไข
		if($_POST[search_al_year])	$search_al_year = $_POST[search_al_year];		
		if($_POST[search_al_cycle])	$search_al_cycle = $_POST[search_al_cycle]; 
		if($_POST[search_al_year]=="")	$search_al_year = "";		
		if(!$_POST[search_al_cycle] || $_POST[search_al_cycle]=="")	$search_al_cycle = ""; 
	}
	if($command=="SEARCH_ALL"){//ค้นหา แสดงทั้งหมด
		$search_al_year ="";	$search_al_cycle = "";
	}
	//echo " $command :::  $_POST[search_al_year] - $search_al_year  / $_POST[search_al_cycle] -  $search_al_cycle :: ";

//---เพิ่มใหม่
//	if (!$AL_YEAR) $AL_YEAR = $KPI_BUDGET_YEAR;
//	if (!$SEQ_NO) $SEQ_NO = $KPI_CYCLE;
	
//	if($_GET[AL_YEAR] || $_GET[AL_YEAR]=="")				$AL_YEAR = $_GET[AL_YEAR];
//	if($_GET[SEQ_NO] || $_GET[SEQ_NO]=="")		$SEQ_NO = $_GET[SEQ_NO];

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
		
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;

			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			$search_org_id2 = $ORG_ID;
			$search_org_name2 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			$search_org_id2 = $ORG_ID;
			$search_org_name2 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	$search_ministry_id = 2895;
	$search_ministry_id2 = 2895;
	$search_department_id = 2954;
	$search_department_id2 = 2954;
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_ORG_REMARK set OR_ACTIVE = 0 where AL_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_ORG_REMARK set OR_ACTIVE = 1 where AL_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($ORG_REMARK)){
		$cmd = " select ORG_REMARK from PER_ORG_REMARK where ORG_ID=$ORG_ID and SEQ_NO=$SEQ_NO ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_ORG_REMARK (ORG_ID , SEQ_NO, ORG_REMARK, OR_ACTIVE, UPDATE_USER, UPDATE_DATE) 
							values ($search_org_id , $SEQ_NO, '$ORG_REMARK', 1, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "ADD-$cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$ORG_ID." : ".$SEQ_NO." : ".$ORG_REMARK."]");
			$SEQ_NO = "";
			$ORG_REMARK = "";
			$command = "";
			unset($SHOW_UPDATE_USER);
			unset($SHOW_UPDATE_DATE);
			$err_text = "";
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$ORG_ID." ".$SEQ_NO." ".$ORG_REMARK."]";
		} // endif
	}

	if($command == "UPDATE" && trim($ORG_REMARK)){
		if (!$search_department_id) $search_department_id = "NULL";
		$cmd = " update PER_ORG_REMARK set 
									ORG_REMARK = '$ORG_REMARK', 
									UPDATE_USER = $SESS_USERID, 
									UPDATE_DATE = '$UPDATE_DATE'  
									where ORG_ID=$ORG_ID and SEQ_NO=$SEQ_NO ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd";
		//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$ORG_ID." : ".$SEQ_NO." : ".$ORG_REMARK."]");
			$SEQ_NO = "";
			$ORG_REMARK = "";
			$command = "";
			unset($SHOW_UPDATE_USER);
			unset($SHOW_UPDATE_DATE);
		//เคลียร์ค่าเพื่อเพิ่มใหม่
 	}
	
	if($command == "DELETE" && trim($AL_CODE)){
		//เพื่อให้อันที่ไม่มี ปีงบประมาณ และครั้งที่ สามารถลบทิ้งได้ ถ้าไม่มีลบทิ้งไม่ได้
		$cmd = " select ORG_REMARK from PER_ORG_REMARK where ORG_ID=$ORG_ID and SEQ_NO=$SEQ_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_REMARK = $data[ORG_REMARK];

		$cmd = " delete from PER_ORG_REMARK where ORG_ID=$ORG_ID and SEQ_NO=$SEQ_NO ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$ORG_ID." : ".$SEQ_NO." : ".$ORG_REMARK."]");
		$ORG_REMARK = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		$command = "";
		
	}
	
	if($UPD){
		$cmd = " select ORG_REMARK, OR_ACTIVE, UPDATE_USER, UPDATE_DATE
						from PER_ORG_REMARK 
						where ORG_ID=$ORG_ID and SEQ_NO=$SEQ_NO  ";
		$db_dpis->send_cmd($cmd);
//		echo "---> cmd=$cmd<br>";
		$data = $db_dpis->get_array();
		$ORG_REMARK = $data[ORG_REMARK];
		$search_org_id = $data[ORG_ID];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_org_id ";
		if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_org_name = $data[ORG_NAME];
	} // end if
	
	if( (!$UPD  && $UPD_Y_C && !$DEL && !$err_text) ){
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>