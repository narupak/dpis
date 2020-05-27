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
		$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=$ORG_ID1 ";
		$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	}

//	echo "command=$command,ORG_ID_REF=$ORG_ID_REF<br>";

	if (!$NEW_CT_CODE) { 
		$NEW_CT_CODE = "140";
		$NEW_CT_NAME = "ไทย";
	}
	if (!$NEW_PV_CODE) { 
		if ($PV_CODE) {
			$NEW_PV_CODE = $PV_CODE;
			$NEW_PV_NAME = $PV_NAME;
		} else {
			$NEW_PV_CODE = "1000";
			$NEW_PV_NAME = "กรุงเทพมหานคร";
		}
	}
	if (!$NEW_OT_CODE) { 
		if($CTRL_TYPE==2){
			$NEW_OT_CODE = "03";
			$NEW_OT_NAME = "ส่วนภูมิภาค";
		} else {
			$NEW_OT_CODE = "01";
			$NEW_OT_NAME = "ส่วนกลาง";
		}
	}
	if($command=="ADD" && $ORG_ID1 && trim($NEW_ORG_NAME)){
		$cmd = " select ORG_CODE, ORG_NAME from $ORGTAB where ORG_NAME='".trim($NEW_ORG_NAME)."' and OL_CODE='".trim($NEW_OL_CODE)."' and 
						ORG_ID_REF = $ORG_ID1 and ORG_ACTIVE=1 ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
			$err_text = "รหัสข้อมูลซ้ำ [".trim($NEW_OL_CODE)." ".trim($NEW_ORG_NAME)."]";
			echo "<script type=\"text/javascript\">alert('".$err_text."');</script>";
		}else{
			$NEW_ORG_DATE =  save_date($NEW_ORG_DATE);

			$ORG_ID_REF = $ORG_ID1;
			$NEW_ORG_JOB = str_replace("'", "&prime;", trim($NEW_ORG_JOB));
			if(trim($NEW_ORG_SEQ_NO)=="") $NEW_ORG_SEQ_NO = "NULL";
			
			if(trim($NEW_OL_CODE)) $NEW_OL_CODE = "'$NEW_OL_CODE'";
			else $NEW_OL_CODE = "NULL";
			if(trim($NEW_OT_CODE)) $NEW_OT_CODE = "'$NEW_OT_CODE'";
			else $NEW_OT_CODE = "NULL";
			if(trim($NEW_DT_CODE)) $NEW_DT_CODE = "'$NEW_DT_CODE'";
			else $NEW_DT_CODE = "NULL";
			if(trim($NEW_AP_CODE)) $NEW_AP_CODE = "'$NEW_AP_CODE'";
			else $NEW_AP_CODE = "NULL";
			if(trim($NEW_PV_CODE)) $NEW_PV_CODE = "'$NEW_PV_CODE'";
			else $NEW_PV_CODE = "NULL";
			if(trim($NEW_CT_CODE)) $NEW_CT_CODE = "'$NEW_CT_CODE'";
			else $NEW_CT_CODE = "NULL";
			if(trim($NEW_MG_CODE)) $NEW_MG_CODE = "'$NEW_MG_CODE'";
			else $NEW_MG_CODE = "NULL";
			if(trim($NEW_PG_CODE)) $NEW_PG_CODE = "'$NEW_PG_CODE'";
			else $NEW_PG_CODE = "NULL";
			if (!$NEW_POS_LAT) $NEW_POS_LAT = "NULL";
			if (!$NEW_POS_LONG) $NEW_POS_LONG = "NULL";
			if (!$NEW_ORG_ID_ASS) $NEW_ORG_ID_ASS = "NULL";
			
			if ($SESS_DEPARTMENT_ID) {
				$TMP_DEPARTMENT_ID = $SESS_DEPARTMENT_ID;
			} else {
				$cmd = " select OL_CODE, ORG_ID, DEPARTMENT_ID from PER_ORG where ORG_ID=$ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_OL_CODE = trim($data2[OL_CODE]);
				if ($TMP_OL_CODE=="02")
					$TMP_DEPARTMENT_ID = $data2[ORG_ID];
				else
					$TMP_DEPARTMENT_ID = $data2[DEPARTMENT_ID];
			}
			if (!$TMP_DEPARTMENT_ID) $TMP_DEPARTMENT_ID = "NULL";

			$cmd = " select max(ORG_ID) as max_id from $ORGTAB ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ORG_ID1 = $data[max_id] + 1;
			
			$cmd = " insert into $ORGTAB (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE,
							  ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, CT_CODE, PV_CODE, AP_CODE, DT_CODE, ORG_DATE, ORG_JOB,
							  ORG_ID_REF, ORG_ACTIVE, ORG_SEQ_NO, ORG_WEBSITE, UPDATE_USER, UPDATE_DATE, 
							  ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, DEPARTMENT_ID, MG_CODE, PG_CODE, ORG_ZONE, ORG_ID_ASS)
							  values ($ORG_ID1, '$NEW_ORG_CODE', '$NEW_ORG_NAME', '$NEW_ORG_SHORT', $NEW_OL_CODE, 
							  $NEW_OT_CODE, '$NEW_ORG_ADDR1', '$NEW_ORG_ADDR2', '$NEW_ORG_ADDR3', $NEW_CT_CODE, 
							  $NEW_PV_CODE, $NEW_AP_CODE, $NEW_DT_CODE, '$NEW_ORG_DATE', '$NEW_ORG_JOB', $ORG_ID_REF, 
							  $NEW_ORG_ACTIVE, $NEW_ORG_SEQ_NO, '$NEW_ORG_WEBSITE', $SESS_USERID, '$UPDATE_DATE', 
							  '$NEW_ORG_ENG_NAME', $NEW_POS_LAT, $NEW_POS_LONG, '$NEW_ORG_DOPA_CODE', 
							  $TMP_DEPARTMENT_ID, $NEW_MG_CODE, $NEW_PG_CODE, '$NEW_ORG_ZONE', $NEW_ORG_ID_ASS) ";
			$db_dpis->send_cmd($cmd);
	//		echo "<pre>$cmd<br>";
	//		$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มโครงสร้างลูก [$ORG_REF_ID : $ORG_ID1 : $ORG_NAME1]");
			//echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>\");</script>";
			
		}
	} // end if
	
	if($command=="UPDATE" && $ORG_ID_REF && $ORG_ID1 && trim($ORG_NAME1)){
//		echo "UPDATE ORG_ACTIVE=$ORG_ACTIVE<br>";
		$ORG_DATE =  save_date($ORG_DATE);

		$ORG_JOB = str_replace("'", "&prime;", trim($ORG_JOB));
		if(trim($ORG_SEQ_NO)=="") $ORG_SEQ_NO = "NULL";

		if(trim($OL_CODE)) $OL_CODE = "'$OL_CODE'";
		else $OL_CODE = "NULL";
		if(trim($OT_CODE)) $OT_CODE = "'$OT_CODE'";
		else $OT_CODE = "NULL";
		if(trim($DT_CODE)) $DT_CODE = "'$DT_CODE'";
		else $DT_CODE = "NULL";
		if(trim($AP_CODE)) $AP_CODE = "'$AP_CODE'";
		else $AP_CODE = "NULL";
		if(trim($PV_CODE1)) $PV_CODE1 = "'$PV_CODE1'";
		else $PV_CODE1 = "NULL";
		if(trim($CT_CODE)) $CT_CODE = "'$CT_CODE'";
		else $CT_CODE = "NULL";
		if(trim($MG_CODE)) $MG_CODE = "'$MG_CODE'";
		else $MG_CODE = "NULL";
		if(trim($PG_CODE)) $PG_CODE = "'$PG_CODE'";
		else $PG_CODE = "NULL";
		if (!$POS_LAT) $POS_LAT = "NULL";
		if (!$POS_LONG) $POS_LONG = "NULL";
		if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
		$cmd = " update $ORGTAB set
							ORG_CODE='$ORG_CODE', 
							ORG_NAME='$ORG_NAME1', 
							ORG_SHORT='$ORG_SHORT',
							OL_CODE=$OL_CODE, 
							OT_CODE=$OT_CODE, 
							ORG_ADDR1='$ORG_ADDR1', 
							ORG_ADDR2='$ORG_ADDR2', 
							ORG_ADDR3='$ORG_ADDR3', 
							ORG_JOB='$ORG_JOB',
							CT_CODE=$CT_CODE, 
							PV_CODE=$PV_CODE1, 
							AP_CODE=$AP_CODE, 
							DT_CODE=$DT_CODE, 
							MG_CODE=$MG_CODE, 
							PG_CODE=$PG_CODE, 
							ORG_ZONE='$ORG_ZONE', 
							ORG_DATE='$ORG_DATE', 
							ORG_ACTIVE='$ORG_ACTIVE', 
							ORG_SEQ_NO=$ORG_SEQ_NO, 
							ORG_WEBSITE='$ORG_WEBSITE',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							ORG_ENG_NAME='$ORG_ENG_NAME', 
							POS_LAT=$POS_LAT, 
							POS_LONG=$POS_LONG,
							ORG_ID_ASS=$ORG_ID_ASS,
							ORG_DOPA_CODE='$ORG_DOPA_CODE' 
						 where ORG_ID=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$msg = "$cmd";
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงสร้าง [$ORG_REF_ID : $ORG_ID1 : $ORG_NAME1]");
		echo "<script type=\"text/javascript\">parent.refresh_opener(\"1<::>\");</script>";
	} // end if
	
	if($command=="CHANGESTRUCTUREPARENT" && $NEW_ORG_ID_REF){
// ขาด per_scholar per_positionhis per_kpi *************************************************************
		$cmd = " select	OL_CODE from	$ORGTAB	where ORG_ID=$NEW_ORG_ID_REF ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OL_CODE = trim($data[OL_CODE]);
		if ($OL_CODE=="01") $NEW_OL_CODE = "02";
		elseif ($OL_CODE=="02") $NEW_OL_CODE = "03";
		elseif ($OL_CODE=="03") $NEW_OL_CODE = "04";
		elseif ($OL_CODE=="04") $NEW_OL_CODE = "05";
		elseif ($OL_CODE=="05") $NEW_OL_CODE = "06";
		elseif ($OL_CODE=="06") $NEW_OL_CODE = "07";
		elseif ($OL_CODE=="07") $NEW_OL_CODE = "08";

		$cmd = " update $ORGTAB set ORG_ID_REF=$NEW_ORG_ID_REF, OL_CODE='$NEW_OL_CODE' where ORG_ID=$ORG_ID1 and ORG_ID_REF=$ORG_ID_REF ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if ($OL_CODE=="01") {
			$cmd = " SELECT POS_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POSITION WHERE ORG_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $ORG_ID1, ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3, 
									ORG_ID_3=$ORG_ID_4, ORG_ID_4=$ORG_ID_5, ORG_ID_5=NULL WHERE POS_ID = $POS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $ORG_ID1 WHERE POS_ID = $POS_ID AND PER_TYPE = 1 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POEM_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_EMP WHERE ORG_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POEM_ID = $data[POEM_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_EMP SET DEPARTMENT_ID = $ORG_ID1, ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3, 
									ORG_ID_3=$ORG_ID_4, ORG_ID_4=$ORG_ID_5, ORG_ID_5=NULL WHERE POEM_ID = $POEM_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $ORG_ID1 WHERE POEM_ID = $POEM_ID AND PER_TYPE = 2 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POEMS_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_EMPSER WHERE ORG_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POEMS_ID = $data[POEMS_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_EMPSER SET DEPARTMENT_ID = $ORG_ID1, ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3, 
									ORG_ID_3=$ORG_ID_4, ORG_ID_4=$ORG_ID_5, ORG_ID_5=NULL WHERE POEMS_ID = $POEMS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $ORG_ID1 WHERE POEMS_ID = $POEMS_ID AND PER_TYPE = 3 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POT_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_TEMP WHERE ORG_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POT_ID = $data[POT_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_TEMP SET DEPARTMENT_ID = $ORG_ID1, ORG_ID=$ORG_ID_1, ORG_ID_1=$ORG_ID_2, ORG_ID_2=$ORG_ID_3, 
									ORG_ID_3=$ORG_ID_4, ORG_ID_4=$ORG_ID_5, ORG_ID_5=NULL WHERE POT_ID = $POT_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $ORG_ID1 WHERE POT_ID = $POT_ID AND PER_TYPE = 4 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}
		} elseif ($OL_CODE=="02") {
			$cmd = " SELECT POS_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POSITION WHERE DEPARTMENT_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POS_ID = $data[POS_ID];
					$ORG_ID = $data[ORG_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID) $ORG_ID = "NULL";
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POSITION SET DEPARTMENT_ID = $NEW_ORG_ID_REF, ORG_ID=$ORG_ID1, ORG_ID_1=$ORG_ID, ORG_ID_2=$ORG_ID_1, 
									ORG_ID_3=$ORG_ID_2, ORG_ID_4=$ORG_ID_3, ORG_ID_5=ORG_ID_4 WHERE POS_ID = $POS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID AND PER_TYPE = 1 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POEM_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_EMP WHERE DEPARTMENT_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POEM_ID = $data[POEM_ID];
					$ORG_ID = $data[ORG_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID) $ORG_ID = "NULL";
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_EMP SET DEPARTMENT_ID = $NEW_ORG_ID_REF, ORG_ID=$ORG_ID1, ORG_ID_1=$ORG_ID, ORG_ID_2=$ORG_ID_1, 
									ORG_ID_3=$ORG_ID_2, ORG_ID_4=$ORG_ID_3, ORG_ID_5=ORG_ID_4 WHERE POEM_ID = $POEM_ID AND PER_TYPE = 2 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $NEW_ORG_ID_REF WHERE POEM_ID = $POEM_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POEMS_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_EMPSER WHERE DEPARTMENT_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POEMS_ID = $data[POEMS_ID];
					$ORG_ID = $data[ORG_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID) $ORG_ID = "NULL";
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_EMPSER SET DEPARTMENT_ID = $NEW_ORG_ID_REF, ORG_ID=$ORG_ID1, ORG_ID_1=$ORG_ID, ORG_ID_2=$ORG_ID_1, 
									ORG_ID_3=$ORG_ID_2, ORG_ID_4=$ORG_ID_3, ORG_ID_5=ORG_ID_4 WHERE POEMS_ID = $POEMS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $NEW_ORG_ID_REF WHERE POEMS_ID = $POEMS_ID AND PER_TYPE = 3 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}

			$cmd = " SELECT POT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5 FROM PER_POS_TEMP WHERE DEPARTMENT_ID=$ORG_ID1 ";
			$count_data = $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			if ($count_data) {
				while($data = $db_dpis->get_array()){
					$POT_ID = $data[POT_ID];
					$ORG_ID = $data[ORG_ID];
					$ORG_ID_1 = $data[ORG_ID_1];
					$ORG_ID_2 = $data[ORG_ID_2];
					$ORG_ID_3 = $data[ORG_ID_3];
					$ORG_ID_4 = $data[ORG_ID_4];
					$ORG_ID_5 = $data[ORG_ID_5];
					if (!$ORG_ID) $ORG_ID = "NULL";
					if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
					if (!$ORG_ID_2) $ORG_ID_2 = "NULL";
					if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
					if (!$ORG_ID_4) $ORG_ID_4 = "NULL";
					if (!$ORG_ID_5) $ORG_ID_5 = "NULL";

					$cmd = " UPDATE PER_POS_TEMP SET DEPARTMENT_ID = $NEW_ORG_ID_REF, ORG_ID=$ORG_ID1, ORG_ID_1=$ORG_ID, ORG_ID_2=$ORG_ID_1, 
									ORG_ID_3=$ORG_ID_2, ORG_ID_4=$ORG_ID_3, ORG_ID_5=ORG_ID_4 WHERE POT_ID = $POT_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " UPDATE PER_PERSONAL SET DEPARTMENT_ID = $NEW_ORG_ID_REF WHERE POT_ID = $POT_ID AND PER_TYPE = 4 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			}
		}
		/*
		$cmd = " SELECT POS_ID FROM PER_POSITION WHERE ORG_ID_5=$ORG_ID1 ";
		$count_data5 = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if ($count_data5) {
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];

				$cmd = " UPDATE PER_POSITION SET ORG_ID_4 = $NEW_ORG_ID_REF WHERE POS_ID = $POS_ID ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();

				$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $NEW_ORG_ID_REF ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$ORG_ID_REF = $data2[ORG_ID_REF];

				if ($ORG_ID_REF) {
					$cmd = " UPDATE PER_POSITION SET ORG_ID_3 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();

					$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$ORG_ID_REF = $data2[ORG_ID_REF];

					if ($ORG_ID_REF) {
						$cmd = " UPDATE PER_POSITION SET ORG_ID_2 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();

						$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
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

					$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$ORG_ID_REF = $data2[ORG_ID_REF];

					if ($ORG_ID_REF) {
						$cmd = " UPDATE PER_POSITION SET ORG_ID_2 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();

						$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
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

						$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$ORG_ID_REF = $data2[ORG_ID_REF];

						if ($ORG_ID_REF) {
							$cmd = " UPDATE PER_POSITION SET ORG_ID_1 = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();

							$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
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

							$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_ID_REF = $data2[ORG_ID_REF];

							if ($ORG_ID_REF) {
								$cmd = " UPDATE PER_POSITION SET ORG_ID = $ORG_ID_REF WHERE POS_ID = $POS_ID ";
								$db_dpis2->send_cmd($cmd);
								//$db_dpis2->show_error();

								$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
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

								$cmd = " SELECT ORG_ID_REF FROM $ORGTAB WHERE ORG_ID = $ORG_ID_REF ";
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
*/
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

//echo $command." && ".$ORG_ID_REF." && ".$ORG_ID1." && ".$ORGTAB;
	if($command=="COPY" && $ORG_ID_REF && $ORG_ID1 && $ORGTAB=="PER_ORG"){		// คัดลอกข้อมูลจาก PER_ORG ไป PER_ORG_ASS 
		$cmd = " delete from PER_BONUSQUOTADTL2 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_SALQUOTADTL2 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " update PER_PERSONAL set ORG_ID=NULL, ORG_ID_1=NULL, ORG_ID_2=NULL, ORG_ID_3=NULL, ORG_ID_4=NULL, ORG_ID_5=NULL, PER_SET_ASS=NULL ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " update PER_ORG_ASS set ORG_ID_REF=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();
		$cmd = " delete from PER_ORG_ASS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, $ORG_ID1, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS
						from 	PER_ORG 
						where 	ORG_ID=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);				
		//echo $cmd."<br>";
		//$db_dpis->show_error();
		$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG 
						where ORG_ID_REF=$ORG_ID1 and ORG_ACTIVE=1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if ($DPISDB=="odbc") {	
			$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID <> $ORG_ID1 ) and ORG_ACTIVE=1 ";
		} elseif ($DPISDB=="oci8") {
			$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
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
				$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
					($search_condition) and ORG_ACTIVE=1 ";
			}
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if ($DPISDB=="odbc") {	
			$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
					(select ORG_ID from PER_ORG_ASS where ORG_ID <> $ORG_ID1 and OL_CODE='04' ) and ORG_ACTIVE=1 ";
		} elseif ($DPISDB=="oci8") {
			$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
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
				$cmd = " insert into PER_ORG_ASS (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
						MG_CODE, PG_CODE, ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, 
						CT_CODE, ORG_DATE, ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, 
						ORG_WEBSITE, ORG_SEQ_NO, DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, 
						ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS)
						select ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, MG_CODE, PG_CODE, 
						ORG_ADDR1, ORG_ADDR2, ORG_ADDR3, DT_CODE, AP_CODE, PV_CODE, CT_CODE, ORG_DATE, 
						ORG_JOB, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, ORG_WEBSITE, ORG_SEQ_NO, 
						DEPARTMENT_ID, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ZONE, ORG_ID_ASS 
						from PER_ORG where ORG_ID_REF in 
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
			$cmd = " select top 1 ORG_ID_REF from $ORGTAB where ORG_ID=$ORG_ID1 ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select ORG_ID_REF from $ORGTAB where ORG_ID=$ORG_ID1 ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select top 1 ORG_ID_REF from $ORGTAB where ORG_ID=$ORG_ID1 ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID_REF = $data[ORG_ID_REF];
	} // end if

	$cmd = " select		ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, ORG_JOB, ORG_ADDR1, ORG_ADDR2, 
										ORG_ADDR3, CT_CODE, PV_CODE, AP_CODE, DT_CODE, MG_CODE, PG_CODE, ORG_ZONE, ORG_DATE, ORG_ACTIVE,
										ORG_SEQ_NO, ORG_WEBSITE, ORG_ENG_NAME, POS_LAT, POS_LONG, ORG_DOPA_CODE, ORG_ID_ASS, UPDATE_USER, UPDATE_DATE
						from		$ORGTAB
						where	ORG_ID=$ORG_ID1 ";
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
	$ORG_ZONE = $data[ORG_ZONE];
	$UPDATE_USER = $data[UPDATE_USER];
	$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
	$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

	$OL_CODE = $data[OL_CODE];
	$OL_NAME = "";
	if($OL_CODE){
		$cmd = " select OL_NAME from PER_ORG_LEVEL where OL_CODE='$OL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OL_NAME = $data2[OL_NAME];
	} // end if

	$OT_CODE = $data[OT_CODE];
	$OT_NAME = "";
	if($OT_CODE){
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];
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
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE1' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME1 = $data2[PV_NAME];
	} // end if

	$AP_CODE = $data[AP_CODE];
	$AP_NAME = "";
	if($AP_CODE){
		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];
	} // end if
	
	$DT_CODE = $data[DT_CODE];
	$DT_NAME = "";
	if($DT_CODE){
		$cmd = " select DT_NAME from PER_DISTRICT where DT_CODE='$DT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DT_NAME = $data2[DT_NAME];
	} // end if
	
	$MG_CODE = $data[MG_CODE];
	$MG_NAME = "";
	if($MG_CODE){
		$cmd = " select MG_NAME from PER_MINISTRY_GROUP where MG_CODE='$MG_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MG_NAME = $data2[MG_NAME];
	} // end if

	$PG_CODE = $data[PG_CODE];
	$PG_NAME = "";
	if($PG_CODE){
		$cmd = " select PG_NAME from PER_PROVINCE_GROUP where PG_CODE='$PG_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PG_NAME = $data2[PG_NAME];
	} // end if

	$ORG_ID_ASS = $data[ORG_ID_ASS];
	$ORG_NAME_ASS = "";
	if($ORG_ID_ASS){
		if ($ORGTAB=="PER_ORG") $ASSTAB = "PER_ORG_ASS";
		else $ASSTAB = "PER_ORG";
		$cmd = " select ORG_NAME from $ASSTAB where ORG_ID=$ORG_ID_ASS ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_ASS = $data2[ORG_NAME];
	} // end if

	$ORG_DATE = show_date_format($data[ORG_DATE], 1);
	$NEW_CT_CODE = "140";
	$NEW_CT_NAME = "ไทย";

	function list_child_org ($org_parent) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $AUTH_CHILD_ORG, $ORGTAB;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=$org_parent ";
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
		global $LIST_OPENED_ORG, $START_ORG_ID, $ORGTAB;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_ID_REF from $ORGTAB where ORG_ID_REF=$ORG_ID1 and ORG_ID<>$START_ORG_ID "; // ทดสอบ ORG_ID_REF=21057 ORG_ID<>2987
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
//		echo "<script type=\"text/javascript\">alert(\"$cmd\");</script>";

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_org($data[ORG_ID], $data[ORG_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from $ORGTAB where ORG_ID=$ORG_ID1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_ORG = str_replace(",$ORG_ID1,", ",", $LIST_OPENED_ORG);
		return;
	} // function
?>