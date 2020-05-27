<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command == "UPDATE" && trim($POS_ID)){
		foreach($ARR_PC_TARGET_LEVEL as $CP_CODE => $PC_TARGET_LEVEL){
//			if(trim($PC_TARGET_LEVEL)){
				if (!$PC_TARGET_LEVEL) $PC_TARGET_LEVEL = 0;
				$cmd = "	select		PC_TARGET_LEVEL 
								from		PER_POSITION_COMPETENCE 
								where 	 POS_ID=$POS_ID and CP_CODE='$CP_CODE' and PER_TYPE=3 ";	
				$count_data = $db_dpis->send_cmd($cmd);
				//echo $cmd."<br>";
				//$db_dpis->show_error();echo $cmd."<br>";
				if ($count_data)
					$cmd = " update PER_POSITION_COMPETENCE set 
									PC_TARGET_LEVEL=$PC_TARGET_LEVEL,
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
								 where POS_ID=$POS_ID and trim(CP_CODE)='$CP_CODE' and PER_TYPE=3 ";
				else
					$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, UPDATE_DATE, 
									DEPARTMENT_ID, PER_TYPE) values ($POS_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE', 
									$DEPARTMENT_ID,  3) ";
				$db_dpis->send_cmd($cmd);
                                //echo $cmd.'<br>';
				//$db_dpis->show_error();		echo "<br>";		
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$POS_ID : $CP_CODE]");
//			} // end if
		} // end foreach
	}
?>