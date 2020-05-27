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
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;
	$PER_TYPE = 3;

	if($command == "ADD" && trim($POS_ID)){
		$cmd = " select a.POS_ID, b.POEMS_NO  from PER_POSITION_COMPETENCE a, PER_POS_EMPSER b 
						where a.POS_ID=b.POEMS_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=$POS_ID and a.PER_TYPE = $PER_TYPE ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select a.EP_CODE, LEVEL_NO, ORG_ID  from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where a.EP_CODE=b.EP_CODE and POEMS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EP_CODE = trim($data[EP_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$TMP_ORG_ID = $data[ORG_ID];

			$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
							WHERE PER_TYPE = $PER_TYPE AND a.DEPARTMENT_ID = $DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  in (1,2) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$CP_CODE = trim($data1[CP_CODE]);
				$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' "; 
				$count_data = $db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
				if (!$count_data) echo '';//echo "$cmd<br>";
				else {
					$data2 = $db_dpis2->get_array();
					$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;

					$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
									  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
									  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
		
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$POS_ID, $CP_CODE, $PER_TYPE]");
				}
			} // end while						

			$cmd = " SELECT EP_CP_CODE FROM PER_EMPSER_POS_NAME WHERE EP_CODE = '$EP_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$EP_CP_CODE = trim($data1[EP_CP_CODE]);
			if ($EP_CP_CODE) {
				$arr_temp = explode("|", $EP_CP_CODE);
				for ($count=0; $count<count($arr_temp); $count++) {
					$CP_CODE = $arr_temp[$count];
					$cmd = " SELECT CP_CODE FROM PER_TYPE_COMPETENCE 
									WHERE PER_TYPE = $PER_TYPE AND DEPARTMENT_ID = $DEPARTMENT_ID AND CP_CODE = '$CP_CODE' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$CP_CODE = trim($data1[CP_CODE]);
					if ($CP_CODE) {
						$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' "; 
						$count_data = $db_dpis2->send_cmd($cmd);
		//					$db_dpis2->show_error();
						if (!$count_data) echo '';//echo "$cmd<br>";
						else {
							$data2 = $db_dpis2->get_array();
							$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;

							$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
											  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
											  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
							$db_dpis2->send_cmd($cmd);
			//				$db_dpis2->show_error();
						}
					}
				}
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$POS_ID, $CP_CODE, $PER_TYPE]");
			} // end if
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POEMS_NO]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($POS_ID)){
		$cmd = " select a.POS_ID, b.POEMS_NO  from PER_POSITION_COMPETENCE a, PER_POS_EMPSER b 
						where a.POS_ID=b.POEMS_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=$POS_ID and a.PER_TYPE = $PER_TYPE ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " 	update PER_POSITION_COMPETENCE set 
									POS_ID=$POS_ID, 
									PER_TYPE=$PER_TYPE, 
									UPDATE_USER=$SESS_USERID, 
									UPDATE_DATE='$UPDATE_DATE' 
								where POS_ID=$OLD_POS_ID and PER_TYPE = $PER_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$OLD_POS_ID -> $POS_ID, $PER_TYPE]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POS_NO]."]";
			$UPD = 1;
		} // end if
	}
	
	if($command == "DELETE" && trim($POS_ID)){
		$cmd = " delete from PER_POSITION_COMPETENCE where POS_ID=$POS_ID and PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$POS_ID]");
	}
	
	if($command == "GENDATA"){
		if($BKK_FLAG==1)
			$cmd = " delete from PER_POSITION_COMPETENCE where PER_TYPE = $PER_TYPE ";
		else
			$cmd = " delete from PER_POSITION_COMPETENCE 
							where PER_TYPE = $PER_TYPE and (DEPARTMENT_ID = $DEPARTMENT_ID or DEPARTMENT_ID = 0) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($BKK_FLAG==1)
			$cmd = " select	POEMS_ID, a.EP_CODE, b.LEVEL_NO, DEPARTMENT_ID, ORG_ID 
							from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where a.EP_CODE=b.EP_CODE and POEM_STATUS=1 
							order by POEMS_NO ";		
		else
			$cmd = " select	POEMS_ID, a.EP_CODE, b.LEVEL_NO, DEPARTMENT_ID, ORG_ID
							from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where a.EP_CODE=b.EP_CODE and DEPARTMENT_ID = $DEPARTMENT_ID and POEM_STATUS=1 
							order by POEMS_NO ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
                $iddata=0;
		while($data = $db_dpis->get_array()){
			$POS_ID = $data[POEMS_ID];
			$EP_CODE = trim($data[EP_CODE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			if (!$LEVEL_NO || $BKK_FLAG==1) {
				$cmd = " select 	LEVEL_NO from PER_PERSONAL where 	POS_ID=$POEMS_ID and PER_STATUS=1 and PER_TYPE = $PER_TYPE ";
				$count_data = $db_dpis2->send_cmd($cmd);
				if ($count_data) {
					$data2 = $db_dpis2->get_array();
					$LEVEL_NO = trim($data2[LEVEL_NO]);
				}
			}

			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$TMP_ORG_ID = $data[ORG_ID];

			$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
							WHERE PER_TYPE = $PER_TYPE AND a.DEPARTMENT_ID = $DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  in (1,2) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$CP_CODE = trim($data1[CP_CODE]);
				$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' "; 
				$count_data = $db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
				if (!$count_data) echo '1:รายการที่ไม่มีข้อมูล PER_STANDARD_COMPETENCE รหัสตำแน่งที่ไม่พบ '.$POS_ID.' <br>';//echo "$cmd<br>";
				else {
					$data2 = $db_dpis2->get_array();
					$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;

					$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
									  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
									  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $TMP_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > สร้างข้อมูลใหม่ [$POS_ID, $CP_CODE, $PER_TYPE]");
				}
			} // end while						
				
			$cmd = " SELECT EP_CP_CODE FROM PER_EMPSER_POS_NAME WHERE trim(EP_CODE) = '$EP_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$EP_CP_CODE = trim($data1[EP_CP_CODE]);
			if ($EP_CP_CODE) {
				$arr_temp = explode("|", $EP_CP_CODE);
				for ($count=0; $count<count($arr_temp); $count++) {
					$CP_CODE = $arr_temp[$count];
					$cmd = " SELECT CP_CODE FROM PER_TYPE_COMPETENCE 
									WHERE PER_TYPE = $PER_TYPE AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND CP_CODE = '$CP_CODE' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$CP_CODE = trim($data1[CP_CODE]);
					if ($CP_CODE) {
						$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' "; 
						$count_data = $db_dpis2->send_cmd($cmd);
		//					$db_dpis2->show_error();
						if (!$count_data) echo '2:รายการที่ไม่มีข้อมูล PER_STANDARD_COMPETENCE รหัสตำแน่งที่ไม่พบ '.$POS_ID.' <br>';//echo "$cmd<br>";
						else {
							$data2 = $db_dpis2->get_array();
							$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;

							$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
											  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
											  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $TMP_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
							$db_dpis2->send_cmd($cmd);
			//				$db_dpis2->show_error();
						}
					}else{
                                            echo '<font color=red>ไม่พบ กรุณาตรวจสอบ ที่ M1104 การประเมินสมรรถนะจำแนกตามประเภทบุคลากร และกรม	</font><br>';
                                        }
				}
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > สร้างข้อมูลใหม่ [$POS_ID, $CP_CODE, $PER_TYPE]");
			}else{
                            echo '<font color=red>ไม่พบ ชื่อตำแหน่งพนักงานราชการ ที่ต้องการสร้าง EP_CODE:'.$EP_CODE.' กรุณาตรวจสอบที่ M0310 ชื่อตำแหน่งพนักงานราชการ	 </font><br>';
                        } // end if
                        $iddata=1;
		} // end while
                if($iddata==0){
                    echo '<font color=red>ไม่พบข้อมูลที่ต้องการสร้าง '.$DEPARTMENT_ID.'</font>';
                }
	}

	if($UPD && !$err_text){
		$cmd = "  select 	distinct a.POS_ID, b.POEMS_NO, c.EP_NAME, a.DEPARTMENT_ID
				    from 		PER_POSITION_COMPETENCE a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c
				    where 	a.POS_ID=b.POEMS_ID and b.EP_CODE=c.EP_CODE and a.POS_ID=$POS_ID and PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];
		$POS_NO = $data[POEMS_NO];
		$EP_NAME = $data[EP_NAME];
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
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$POS_ID = "";
		$OLD_POS_ID = "";
		$POS_NO = "";
		$EP_NAME = "";
	} // end if
?>