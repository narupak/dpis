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

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "GENDATA"){
			$cmd = " DELETE FROM PER_POS_MGTSALARY ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();

			if ($BKK_FLAG==1)
				$cmd = " SELECT POS_ID, LEVEL_NO, PT_CODE FROM PER_POSITION WHERE LEVEL_NO IN ('O4', 'K2', 'K3', 'K4', 'K5', 'D1', 'D2', 'M1', 'M2') AND PT_CODE > '1' ";
			else
				$cmd = " SELECT POS_ID, LEVEL_NO, PT_CODE FROM PER_POSITION WHERE LEVEL_NO IN ('O4', 'K2', 'K3', 'K4', 'K5', 'D1', 'D2', 'M1', 'M2') AND PT_CODE IN ('12', '21', '22', '31', '32') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$PT_CODE = trim($data[PT_CODE]);
				$EX_CODE = ""; 
				if ($LEVEL_NO=="O4") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="K2" && $PT_CODE=="21") $EX_CODE = "020"; 
				elseif ($LEVEL_NO=="K3" && $PT_CODE=="21") $EX_CODE = "200"; 
				elseif ($LEVEL_NO=="K4") $EX_CODE = "011"; 
				elseif ($LEVEL_NO=="K5") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="D1") $EX_CODE = "010"; 
				elseif ($LEVEL_NO=="D2") $EX_CODE = "017"; 
				elseif ($LEVEL_NO=="M1") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="M2") $EX_CODE = "023"; 

				$cmd = " SELECT POS_ID FROM PER_POS_MGTSALARY 	WHERE POS_ID = $POS_ID AND trim(EX_CODE) = '$EX_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO  PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE)
									  VALUES ($POS_ID, '$EX_CODE', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end if

				$EX_CODE = ""; 
				if ($LEVEL_NO=="K3" && $PT_CODE=="21") $EX_CODE = "201"; 
				elseif ($LEVEL_NO=="K4") $EX_CODE = "196"; 
				elseif ($LEVEL_NO=="D1") $EX_CODE = "195"; 
				elseif ($LEVEL_NO=="D2") $EX_CODE = "194"; 
				elseif ($LEVEL_NO=="M1") $EX_CODE = ""; 
				elseif ($LEVEL_NO=="M2") $EX_CODE = "193"; 

				$cmd = " SELECT POS_ID FROM PER_POS_MGTSALARY 	WHERE POS_ID = $POS_ID AND trim(EX_CODE) = '$EX_CODE' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				if (!$count_data) {
					$cmd = " INSERT INTO  PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE)
									  VALUES ($POS_ID, '$EX_CODE', NULL, 1, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end if

			} // end while
	} // end if 

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);

		if($DPISDB=="odbc"){    $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 2 where (POS_ID&'||')&EX_CODE in (".stripslashes($current_list).") ";  }
		elseif($DPISDB=="oci8"){     $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 2 where concat(concat(POS_ID, '||'), EX_CODE) in (".stripslashes($current_list).") ";    }
		elseif($DPISDB=="mysql"){    $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 2 where (POS_ID&'||')&EX_CODE in (".stripslashes($current_list).") ";         }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		if($DPISDB=="odbc"){    $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 1 where  (POS_ID&'||')&EX_CODE in (".stripslashes($setflagshow).") "; }
		elseif($DPISDB=="oci8"){    $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 1 where concat(concat(POS_ID, '||'), EX_CODE) in (".stripslashes($setflagshow).") ";   }
		elseif($DPISDB=="mysql"){    $cmd = " update PER_POS_MGTSALARY set POS_STATUS = 1 where  (POS_ID&'||')&EX_CODE in (".stripslashes($setflagshow).") ";      }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}

	if($command == "ADD" && trim($POS_ID) && trim($EX_CODE)){
			$cmd = "select POS_ID, EX_CODE from PER_POS_MGTSALARY 
							where POS_ID=$POS_ID and EX_CODE= '$EX_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_POS_MGTSALARY (POS_ID, EX_CODE, POS_STARTDATE, POS_STATUS, UPDATE_USER, UPDATE_DATE) 
							  values ($POS_ID, '$EX_CODE', '$POS_STARTDATE', $POS_STATUS, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [trim($POS_ID) : $EX_CODE]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[POS_ID] $data[EX_CODE]]";
		} // endif
	}

	if($command == "UPDATE" && trim($POS_ID) && trim($EX_CODE)){
		$cmd = " 	select POS_ID, EX_CODE from PER_POS_MGTSALARY 
							where POS_ID=$POS_ID and EX_CODE='$EX_CODE' ";
					$count_duplicate = $db_dpis->send_cmd($cmd);
					 
		if(!$count_duplicate){
			$cmd = " 	update PER_POS_MGTSALARY set 
								POS_ID=$POS_ID, 
								EX_CODE='$EX_CODE', 
								POS_STARTDATE='$POS_STARTDATE', 
								POS_STATUS=$POS_STATUS, 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							where POS_ID=$upd_pos_id and EX_CODE='$upd_ex_code' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [จาก->trim($POS_ID) : $EX_CODE เป็น->$upd_pos_id : $upd_ex_code]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[POS_ID] $data[EX_CODE]]";
			$UPD = 1;
		} // endif
	}
	
	if($command == "DELETE" && trim($POS_ID) && trim($EX_CODE)){
		$cmd = " select EX_CODE from PER_POS_MGTSALARY where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EX_CODE = $data[EX_CODE];
		
		$cmd = " delete from PER_POS_MGTSALARY where POS_ID=$POS_ID and EX_CODE='$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [trim($POS_ID) : $EX_CODE]");
	}

	if($UPD){
		$cmd = " select 	a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, b.POS_NO, b.PL_CODE, b.PM_CODE, EX_NAME, a.UPDATE_USER, a.UPDATE_DATE 
						from 		PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c 
						where 	a.POS_ID = $POS_ID and trim(a.EX_CODE) = trim('$EX_CODE') and 
					  					a.POS_ID=b.POS_ID and trim(a.EX_CODE)=trim(c.EX_CODE)
					  order by 	a.POS_ID, a.EX_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];		
		$EX_CODE = $data[EX_CODE];
		$POS_STATUS = $data[POS_STATUS];
		$POS_NO = $data[POS_NO];
		$PL_CODE = $data[PL_CODE];
		$PM_CODE = $data[PM_CODE];
		$EX_NAME = $data[EX_NAME];
		$upd_pos_id = $data[POS_ID];
		$upd_ex_code = $data[EX_CODE];		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";			
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = $data2[PL_NAME];

		$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = trim($data2[PM_NAME]);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$POS_ID = "";
		$EX_CODE = "";
		$EX_NAME = "";
		$POS_STARTDATE = "";
		$POS_STATUS = 1;
		
		$POS_NO = "";
		$PL_CODE = "";
		$PL_NAME = "";
		$PM_CODE = "";
		$PM_NAME = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if 
?>