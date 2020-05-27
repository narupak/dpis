<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if (!$LIST_OPENED_ORG) {
		$LIST_OPENED_ORG = ",$SESS_MINISTRY_ID.0.1,$SESS_DEPARTMENT_ID.0.$SESS_MINISTRY_ID,";
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_ORG_ID ? "$SESS_ORG_ID.0.$SESS_DEPARTMENT_ID," : "");
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_PROVINCE_CODE ? "$SESS_PROVINCE_CODE.0.$SESS_ORG_ID," : "");
	}

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;

  	if($CTRL_TYPE==1 || $CTRL_TYPE==2) { 
		unset($AUTH_CHILD_ORG);
	
		if($CTRL_TYPE==2){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==3 || $SESS_USERGROUP_LEVEL==3){
			$AUTH_CHILD_ORG[] = $SESS_MINISTRY_ID;
			list_child_org($SESS_MINISTRY_ID);
		}elseif($CTRL_TYPE==4 || $SESS_USERGROUP_LEVEL==4){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==5 || $SESS_USERGROUP_LEVEL==5){
			$AUTH_CHILD_ORG[] = $SESS_ORG_ID;
			list_child_org($SESS_ORG_ID);
		} // end if
	
		switch($SESS_USERGROUP_LEVEL){
			case 1 :
				$START_ORG_ID = 1;
				break;
			case 2 :
				$START_ORG_ID = 1;
				break;
			case 3 :
				$START_ORG_ID = $SESS_MINISTRY_ID;
				break;
			case 4 :
				$START_ORG_ID = $SESS_DEPARTMENT_ID;
				break;
			case 5 :
				$START_ORG_ID = $SESS_ORG_ID;
				break;
		} // end switch case
	} else {
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$ORG_ID1 ";
		$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	}

//	echo "command=$command,ORG_ID_REF=$ORG_ID_REF<br>";

	if($command=="ADD" && $ORG_ID_REF && trim($NEW_ORG_CODE)){
		if(trim($NEW_ORG_DATE)){
			$arr_temp = explode("/", $NEW_ORG_DATE);
			$NEW_ORG_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		} // end if

		$ORG_ID_REF = $ORG_ID1;
		$NEW_ORG_JOB = str_replace("'", "&prime;", trim($NEW_ORG_JOB));
		if(trim($NEW_ORG_SEQ_NO)=="") $NEW_ORG_SEQ_NO = "NULL";
		
		if(trim($NEW_OL_CODE)) $NEW_OL_CODE = "'$NEW_OL_CODE'";
		else $NEW_OL_CODE = "NULL";
		if(trim($NEW_OT_CODE)) $NEW_OT_CODE = "'$NEW_OT_CODE'";
		else $NEW_OT_CODE = "NULL";
		if(trim($NEW_OP_CODE)) $NEW_OP_CODE = "'$NEW_OP_CODE'";
		else $NEW_OP_CODE = "NULL";
		if(trim($NEW_OS_CODE)) $NEW_OS_CODE = "'$NEW_OS_CODE'";
		else $NEW_OS_CODE = "NULL";
		if(trim($NEW_AP_CODE)) $NEW_AP_CODE = "'$NEW_AP_CODE'";
		else $NEW_AP_CODE = "NULL";
		if(trim($NEW_PV_CODE)) $NEW_PV_CODE = "'$NEW_PV_CODE'";
		else $NEW_PV_CODE = "NULL";
		if(trim($NEW_CT_CODE)) $NEW_CT_CODE = "'$NEW_CT_CODE'";
		else $NEW_CT_CODE = "NULL";
		if (!$NEW_POS_LAT) $NEW_POS_LAT = "NULL";
		if (!$NEW_POS_LONG) $NEW_POS_LONG = "NULL";
		
		$cmd = " select max(ORG_ID) as max_id from PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ORG_ID1 = $data[max_id] + 1;
		
		$cmd = " insert into PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OP_CODE, OT_CODE, OS_CODE,
						  ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, CT_CODE, PV_CODE, AP_CODE, ORG_DATE, ORG_JOB,
						  ORG_ID_REF, ORG_ACTIVE, ORG_SEQ_NO, ORG_WEBSITE, UPDATE_USER, UPDATE_DATE, 
						  ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE)
						  values ($ORG_ID1, '$NEW_ORG_CODE', '$NEW_ORG_NAME', '$NEW_ORG_SHORT', $NEW_OL_CODE, $NEW_OP_CODE, 
						  $NEW_OT_CODE, $NEW_OS_CODE, '$NEW_ORG_ADDR1', '$NEW_ORG_ADDR2', '$NEW_ORG_ADDR3', $NEW_CT_CODE, 
						  $NEW_PV_CODE, $NEW_AP_CODE, '$NEW_ORG_DATE', '$NEW_ORG_JOB', $ORG_ID_REF, $NEW_ORG_ACTIVE, 
						  $NEW_ORG_SEQ_NO, '$NEW_ORG_WEBSITE', $SESS_USERID, '$UPDATE_DATE', 
						  '$NEW_ORG_ENG_NAME', $NEW_POS_LAT, $NEW_POS_LONG, '$NEW_ORG_DOPA_CODE') ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มโครงสร้างลูก [$ORG_REF_ID : $ORG_ID1 : $ORG_NAME1]");
		echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>\");</script>";
	} // end if
	
	if($command=="UPDATE" && $ORG_ID_REF && $ORG_ID1 && trim($ORG_CODE)){
		if(trim($ORG_DATE)){
			$arr_temp = explode("/", $ORG_DATE);
			$ORG_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		} // end if

		$ORG_JOB = str_replace("'", "&prime;", trim($ORG_JOB));
		if(trim($ORG_SEQ_NO)=="") $ORG_SEQ_NO = "NULL";

		if(trim($OL_CODE)) $OL_CODE = "'$OL_CODE'";
		else $OL_CODE = "NULL";
		if(trim($OT_CODE)) $OT_CODE = "'$OT_CODE'";
		else $OT_CODE = "NULL";
		if(trim($OP_CODE)) $OP_CODE = "'$OP_CODE'";
		else $OP_CODE = "NULL";
		if(trim($OS_CODE)) $OS_CODE = "'$OS_CODE'";
		else $OS_CODE = "NULL";
		if(trim($AP_CODE)) $AP_CODE = "'$AP_CODE'";
		else $AP_CODE = "NULL";
		if(trim($PV_CODE1)) $PV_CODE1 = "'$PV_CODE1'";
		else $PV_CODE1 = "NULL";
		if(trim($CT_CODE)) $CT_CODE = "'$CT_CODE'";
		else $CT_CODE = "NULL";
		if (!$POS_LAT) $POS_LAT = "NULL";
		if (!$POS_LONG) $POS_LONG = "NULL";
		$cmd = " update PER_ORG set
							ORG_CODE='$ORG_CODE', 
							ORG_NAME='$ORG_NAME1', 
							ORG_SHORT='$ORG_SHORT',
							OL_CODE=$OL_CODE, 
							OP_CODE=$OP_CODE, 
							OT_CODE=$OT_CODE, 
							OS_CODE=$OS_CODE,
							ORG_ADDR1='$ORG_ADDR1', 
							ORG_ADDR2='$ORG_ADDR2', 
							ORG_ADDR3='$ORG_ADDR3', 
							ORG_JOB='$ORG_JOB',
							CT_CODE=$CT_CODE, 
							PV_CODE=$PV_CODE1, 
							AP_CODE=$AP_CODE, 
							ORG_DATE='$ORG_DATE', 
							ORG_ACTIVE='$ORG_ACTIVE', 
							ORG_SEQ_NO=$ORG_SEQ_NO, 
							ORG_WEBSITE='$ORG_WEBSITE',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							ORG_ENG_NAME='$ORG_ENG_NAME', 
							POS_LAT=$POS_LAT, 
							POS_LONG=$POS_LONG,
							ORG_DOPA_CODE='$ORG_DOPA_CODE' 
						 where ORG_ID=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงสร้าง [$ORG_REF_ID : $ORG_ID1 : $ORG_NAME1]");
		echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>\");</script>";
	} // end if
	
	if($command=="CHANGESTRUCTUREPARENT" && $NEW_ORG_ID_REF){
		$cmd = " update PER_ORG set ORG_ID_REF=$NEW_ORG_ID_REF where ORG_ID=$ORG_ID1 and ORG_ID_REF=$ORG_ID_REF ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_5=$ORG_ID1 ";
		$count_data5 = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if ($count_data5) {
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];

				$cmd = " UPDATE PER_POSITION SET ORG_ID_4 = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $NEW_ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[ORG_ID_REF];

				if ($ORG_ID_REF) {
					$cmd = " UPDATE PER_POSITION SET ORG_ID_3 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$ORG_ID_REF = $data2[ORG_ID_REF];

					if ($ORG_ID_REF) {
						$cmd = " UPDATE PER_POSITION SET ORG_ID_2 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();

						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_ID_REF = $data2[ORG_ID_REF];

								if ($ORG_ID_REF) {
									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								}
							}
						}
					}
				}
			} // end while						
		} else { // if ($count_data5)
			$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_4=$ORG_ID1 ";
			$count_data4 = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data4) {
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];

					$cmd = " UPDATE PER_POSITION SET ORG_ID_3 = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$ORG_ID_REF = $data2[ORG_ID_REF];

					if ($ORG_ID_REF) {
						$cmd = " UPDATE PER_POSITION SET ORG_ID_2 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();

						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_ID_REF = $data2[ORG_ID_REF];

								if ($ORG_ID_REF) {
									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								}
							}
						}
					}
				} // end while						
			} else { // if ($count_data4)
				$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_3=$ORG_ID1 ";
				$count_data3 = $db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				if ($count_data3) {
					while($data = $db_dpis->get_array()){
						$POS_ID = $data[POS_ID];

						$cmd = " UPDATE PER_POSITION SET ORG_ID_2 = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();

						$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_ID_REF = $data2[ORG_ID_REF];

								if ($ORG_ID_REF) {
									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								}
							}
						}
					} // end while						
				} else { // if ($count_data3)
					$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_2=$ORG_ID1 ";
					$count_data2 = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					if ($count_data2) {
						while($data = $db_dpis->get_array()){
							$POS_ID = $data[POS_ID];

							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_ID_REF = $data2[ORG_ID_REF];

								if ($ORG_ID_REF) {
									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								}
							}
						} // end while						
					} else { // if ($count_data2)
						$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_1=$ORG_ID1 ";
						$count_data1 = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						if ($count_data1) {
							while($data = $db_dpis->get_array()){
								$POS_ID = $data[POS_ID];

								$cmd = " UPDATE PER_POSITION SET ORG_ID = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM PER_ORG WHERE ORG_ID = $ORG_ID_REF ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$ORG_ID_REF = $data2[ORG_ID_REF];

								if ($ORG_ID_REF) {
									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								}
							} // end while						
						} else { // if ($count_data1)
							$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID=$ORG_ID1 ";
							$count_data = $db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							if ($count_data) {
								while($data = $db_dpis->get_array()){
									$POS_ID = $data[POS_ID];

									$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
									$db_dpis2->send_cmd($cmd);
									//$db_dpis2->show_error();
								} // end while						
							}
						}
					}
				}
			}
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับโครงสร้าง [$ORG_ID1 : $ORG_NAME1 | $ORG_ID_REF => $NEW_ORG_ID_REF]");	

		$ORG_ID_REF = $NEW_ORG_ID_REF;

		echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>$ORG_ID_REF\");</script>";
	} // end if

	if($command=="DELETE" && $ORG_ID_REF && $ORG_ID1){
		delete_org($ORG_ID1, $ORG_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบโครงสร้าง [$ORG_REF_ID : $ORG_ID1 : $ORG_NAME1]");

		if($ORG_ID1==$START_ORG_ID) unset($ORG_ID1);
		else $ORG_ID1 = $ORG_ID_REF;
		unset($ORG_ID_REF);
        
		echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>\");</script>";
	} // end if

	if($command=="COPY" && $ORG_ID_REF && $ORG_ID){		// คัดลอกข้อมูลจาก PER_ORG ไป PER_ORG_ASS 
		$cmd = " delete from PER_BONUSQUOTADTL2 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_SALQUOTADTL2 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " update PER_PERSONAL set ORG_ID=NULL ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " update PER_ORG_ASS set ORG_ID_REF=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_ORG_ASS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		// สำนัก/กอง ตัวแม่
		$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						OP_CODE, OS_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, 
						ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, OP_CODE, OS_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, $ORG_ID1, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, 
						ORG_SEQ_NO, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE
				from 	PER_ORG 
				where 	ORG_ID=$ORG_ID1";
		$db_dpis->send_cmd($cmd);				
		//$db_dpis->show_error();
		// สำนัก/กอง		
		$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF=$ORG_ID1 and ORG_ACTIVE=1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		// ต่ำกว่าสำนัก/กอง 1 ระดับ
		if ($DPISDB=="odbc") {	
			$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID <> $ORG_ID1 ) and ORG_ACTIVE=1 ";
		} elseif ($DPISDB=="oci8") {
			$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID != $ORG_ID1 ) and ORG_ACTIVE=1 ";		
		}elseif($DPISDB=="mysql"){
			$search_condition = "";
			$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID != $ORG_ID1";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$search_condition.=$data[ORG_ID].",";
			}
			if(trim($search_condition)){ 
				$search_condition=substr($search_condition,0,strlen($search_condition)-1);
				$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					($search_condition) and ORG_ACTIVE=1 ";
			}
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		// ต่ำกว่าสำนัก/กอง 2 ระดับ
		if ($DPISDB=="odbc") {	
			$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID <> $ORG_ID1 and OL_CODE='04' ) and ORG_ACTIVE=1 ";
		} elseif ($DPISDB=="oci8") {
			$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID != $ORG_ID1 and OL_CODE='04' ) and ORG_ACTIVE=1 ";
		}elseif($DPISDB=="mysql"){
			$search_condition = "";
			$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID != $ORG_ID1 and OL_CODE='04'";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$search_condition.=$data[ORG_ID].",";
			}
			if(trim($search_condition)){ 
				$search_condition=substr($search_condition,0,strlen($search_condition)-1); 
				$cmd = " insert into PER_ORG_ASS select * from PER_ORG where ORG_ID_REF in 
					($search_condition) and ORG_ACTIVE=1 ";
			}
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	} // end if 
	
	if(!$ORG_ID1 && !$ORG_ID_REF){
		$ORG_ID1 = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
	} // end if

	if(!$ORG_ID1 && !$ORG_ID_REF){
		$ORG_ID1 = 1;
		$ORG_ID_REF = 1;
	} // end if

	if($ORG_ID1 && !$ORG_ID_REF)	{
		if($DPISDB=="odbc"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID1 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID1 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID1 ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID_REF = $data[ORG_ID_REF];
	} // end if

	$cmd = " select		ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OP_CODE, OT_CODE, OS_CODE, ORG_JOB,
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, CT_CODE, PV_CODE, AP_CODE, ORG_DATE, ORG_ACTIVE,
						ORG_SEQ_NO, ORG_WEBSITE, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE
					from		PER_ORG
					 where		ORG_ID=$ORG_ID1 ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
//	echo "Data :: <pre>"; print_r($data); echo "</pre>";
	$ORG_CODE = $data[ORG_CODE];
	$ORG_NAME1 = $data[ORG_NAME];
	$ORG_SHORT = $data[ORG_SHORT];
	$ORG_ENG_NAME = $data[ORG_ENG_NAME];
	$ORG_JOB = str_replace("\r", "", str_replace("\n", "", $data[ORG_JOB]));
	$ORG_ADDR1 = $data[ORG_ADDR1];
	$ORG_ADDR2 = $data[ORG_ADDR2];
	$ORG_ADDR3 = $data[ORG_ADDR3];
	$ORG_ACTIVE = $data[ORG_ACTIVE];
	$ORG_SEQ_NO = $data[ORG_SEQ_NO];
	$ORG_WEBSITE = $data[ORG_WEBSITE];
	$POS_LAT = $data[POS_LAT];
	$POS_LONG = $data[POS_LONG];
	$ORG_DOPA_CODE = $data[ORG_DOPA_CODE];

	$OL_CODE = $data[OL_CODE];
	$OL_NAME = "";
	if($OL_CODE){
		$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE='$OL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OL_NAME = $data2[OL_NAME];
	} // end if

	$OP_CODE = $data[OP_CODE];
	$OP_NAME = "";
	if($OP_CODE){
		$cmd = " select OP_NAME from PER_ORG_PROVINCE where ST_CODE='$OP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OP_NAME = $data2[OP_NAME];
	} // end if

	$OT_CODE = $data[OT_CODE];
	$OT_NAME = "";
	if($OT_CODE){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];
	} // end if

	$OS_CODE = $data[OS_CODE];
	$OS_NAME = "";
	if($OS_CODE){
		$cmd = " select OS_NAME from PER_ORG_STAT where OS_CODE='$OS_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OS_NAME = $data2[OS_NAME];
	} // end if

	$CT_CODE = $data[CT_CODE];
	$CT_NAME = "";
	if($CT_CODE){
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
	} // end if

	$PV_CODE1 = $data[PV_CODE];
	$PV_NAME1 = "";
	if($PV_CODE1){
		$cmd = " select PV_NAME from PER_PROVINCE where CT_CODE='$CT_CODE' and PV_CODE='$PV_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME1 = $data2[PV_NAME];
	} // end if

	$AP_CODE = $data[AP_CODE];
	$AP_NAME = "";
	if($AP_CODE){
		$cmd = " select AP_NAME from PER_AMPHUR where PV_CODE='$PV_CODE1' and AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];
	} // end if
	
	$ORG_DATE = trim($data[ORG_DATE]);
	if($ORG_DATE){
		$arr_temp = explode("-", $ORG_DATE);
		$ORG_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
	} // end if
	$NEW_CT_CODE = "140";
	$NEW_CT_NAME = "ไทย";

	function list_child_org ($org_parent) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $AUTH_CHILD_ORG;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$org_parent ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$AUTH_CHILD_ORG[] = $data[ORG_ID];
				list_child_org($data[ORG_ID]);
			} // end while
		} // end if
	} // function

	function delete_org($ORG_ID1, $ORG_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG, $START_ORG_ID;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_ID_REF from PER_ORG where ORG_ID_REF=$ORG_ID1 and ORG_ID<>$START_ORG_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_org($data[ORG_ID], $data[ORG_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_ORG where ORG_ID=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_ORG = str_replace(",$ORG_ID1,", ",", $LIST_OPENED_ORG);
		
		return;
	} // function
?>