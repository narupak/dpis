<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
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

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
/*
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);

		if($DPISDB=="odbc"){    $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 2 where (POS_ID&'||')&EX_CODE in ($current_list) ";  }
		elseif($DPISDB=="oci8"){     $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 2 where concat(concat(POS_ID, '||'), EX_CODE) in ($current_list) ";    }
		elseif($DPISDB=="mysql"){    $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 2 where (POS_ID&'||')&EX_CODE in ($current_list) ";         }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		if($DPISDB=="odbc"){    $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 1 where  (POS_ID&'||')&EX_CODE in ($setflagshow) "; }
elseif($DPISDB=="oci8"){    $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 1 where concat(concat(POS_ID, '||'), EX_CODE) in ($setflagshow) ";   }
elseif($DPISDB=="mysql"){    $cmd = " update PER_POSITION_MGTSALARY set POS_STATUS = 1 where  (POS_ID&'||')&EX_CODE in ($setflagshow) ";      }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($POS_ID)){
		$cmd = " 	select POS_ID, EX_CODE from PER_POSITION_MGTSALARY 
							where POS_ID=$POS_ID and EX_CODE= '$EX_CODE' ;
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_POSITION_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE) 
							  values ($POS_ID, '$EX_CODE', '$POS_STARTDATE', $POS_STATUS, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($POS_ID)." : ".$EX_CODE."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[POS_ID]." ".$data[EX_CODE]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($POS_ID)){
		$cmd = " 	select POS_ID, EX_CODE from PER_POSITION_MGTSALARY 
							where POS_ID=$POS_ID and EX_CODE='$EX_CODE' ;
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " 	update PER_POSITION_MGTSALARY set 
								POS_ID=$POS_ID, 
								EX_CODE='$EX_CODE', 
								POS_STARTDATE='$POS_STARTDATE', 
								POS_STATUS=$POS_STATUS, 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							where POS_ID=$upd_pos_id and EX_CODE='$upd_ex_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [จาก->".trim($POS_ID)." : ".$EX_CODE." เป็น->$upd_pos_id : $upd_ex_code]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[POS_ID]." ".$data[EX_CODE]."]";
			$UPD = 1;
		} // endif
	}
	
	if($command == "DELETE" && trim($POS_ID)){
		$cmd = " select EX_CODE from PER_POSITION_MGTSALARY where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = " delete from PER_POSITION_MGTSALARY where POS_ID=$POS_ID and EX_CODE='$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($POS_ID)." : ".$EX_CODE."]");
	}
	
	if($UPD){
		$cmd = " select 	a.POS_ID, a.EX_CODE, POS_STARTDATE, POS_STATUS, b.POS_NO, b.PL_CODE, EX_NAME 
						from 		PER_POSITION_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c 
						where 	a.POS_ID = '$POS_ID' and a.EX_CODE = '$EX_CODE' and 
					  					a.POS_ID=b.POS_ID and a.EX_CODE=c.EX_CODE
					  order by 	a.POS_ID, a.EX_CODE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];		
		$EX_CODE = $data[EX_CODE];
		$POS_STATUS = $data[POS_STATUS];
		$POS_NO = $data[POS_NO];
		$PL_CODE = $data[PL_CODE];
		$EX_NAME = $data[EX_NAME];
		$upd_pos_id = $data[POS_ID];
		$upd_ex_code = $data[EX_CODE];		
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$POS_ID = "";
		$EX_CODE = "";
		$POS_STARTDATE = "";
		$POS_STATUS = 1;
		
		$POS_NO = "";
		$PL_CODE = "";
		$PL_NAME = "";
		$EX_NAME = "";
	} // end if */
?>