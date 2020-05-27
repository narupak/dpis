<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.LEVEL_NO, PER.OT_CODE, 
											PER.PER_PROBATION_FLAG
								from		PER_PERSONAL as PER
												left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
								where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, 
											PER_PERSONAL.DEPARTMENT_ID, PER_PERSONAL.LEVEL_NO, PER_PERSONAL.OT_CODE, PER_PERSONAL.PER_PROBATION_FLAG
 								from		PER_PERSONAL, PER_PRENAME
								where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.LEVEL_NO, PER.OT_CODE, 
											PER.PER_PROBATION_FLAG
								from		PER_PERSONAL as PER
												left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
								where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$OT_CODE = trim($data[OT_CODE]);
		$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	$KF_START_DATE_1 = ($KF_YEAR - 544) . "-10-01";
	$KF_END_DATE_1 = ($KF_YEAR - 543) . "-03-31";
	$KF_START_DATE_2 = ($KF_YEAR - 543) . "-04-01";
	$KF_END_DATE_2 = ($KF_YEAR - 543) . "-09-30";
	if(!$WEIGHT_OTHER) $WEIGHT_OTHER = "NULL";

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_KPI_FORM set AUDIT_FLAG = 'N' where KF_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_KPI_FORM set AUDIT_FLAG = 'Y' where KF_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID && $KF_YEAR){	
		if($TOTAL_SCORE1){
			$cmd2 = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	KF_END_DATE = '$KF_END_DATE_1' and  KF_CYCLE=1 and PER_ID = $PER_ID ";
		} // end if
		$count1=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($TOTAL_SCORE2){
			$cmd2 = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	KF_END_DATE = '$KF_END_DATE_2' and  KF_CYCLE=2 and PER_ID = $PER_ID ";
		} // end if
		$count2=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count1 || $count2) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ ข้อมูลซ้ำ !!! \n กรุณาตรวจสอบ ปีงบประมาณและรอบการประเมิน ของคะแนนดังกล่าวที่เมนู K08");
                        
				-->   </script>	<? }  
		else {			
			if ($PER_PROBATION_FLAG==1){
				$PERFORMANCE_WEIGHT = $WEIGHT_PROBATION;
				$COMPETENCE_WEIGHT = $WEIGHT_PROBATION;
				$OTHER_WEIGHT = "NULL";
			}elseif ($OT_CODE=="08"){
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI_E;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_E;
				$OTHER_WEIGHT = "NULL";
			}elseif ($OT_CODE=="09"){
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI_S;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_S;
				$OTHER_WEIGHT = "NULL";
			}else{
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE;
				$OTHER_WEIGHT = $WEIGHT_OTHER;
			}
			$cmd = " select max(KF_ID) as max_id from PER_KPI_FORM ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$KF_ID = $data[max_id] + 1;
		
			if ($TOTAL_SCORE1) {
				$cmd = " 	insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
									TOTAL_SCORE, DEPARTMENT_ID, LEVEL_NO, UPDATE_USER, UPDATE_DATE, PERFORMANCE_WEIGHT, 
									COMPETENCE_WEIGHT, OTHER_WEIGHT ,KF_SCORE_STATUS)
									values ($KF_ID, $PER_ID, '$PER_CARDNO', 1, '$KF_START_DATE_1', '$KF_END_DATE_1', 
									$TOTAL_SCORE1, $PER_ID_DEPARTMENT_ID, '$LEVEL_NO', $SESS_USERID, '$UPDATE_DATE', $PERFORMANCE_WEIGHT, 
									$COMPETENCE_WEIGHT, $OTHER_WEIGHT,1)   ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				$KF_ID++;
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่ม KPI รายบุคคล [$PER_ID : $KF_ID : $KF_START_DATE_1]");
				
				
			}

			if ($TOTAL_SCORE2) {
				$cmd = " 	insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
									TOTAL_SCORE, DEPARTMENT_ID, LEVEL_NO, UPDATE_USER, UPDATE_DATE, PERFORMANCE_WEIGHT, 
									COMPETENCE_WEIGHT, OTHER_WEIGHT ,KF_SCORE_STATUS)
									values ($KF_ID, $PER_ID, '$PER_CARDNO', 2, '$KF_START_DATE_2', '$KF_END_DATE_2', 
									$TOTAL_SCORE2, $PER_ID_DEPARTMENT_ID, '$LEVEL_NO', $SESS_USERID, '$UPDATE_DATE', $PERFORMANCE_WEIGHT, 
									$COMPETENCE_WEIGHT, $OTHER_WEIGHT,1)   ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่ม KPI รายบุคคล [$PER_ID : $KF_ID : $KF_START_DATE_2]");
			}
			$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $KF_ID){
		if ($TOTAL_SCORE1) $TOTAL_SCORE = $TOTAL_SCORE1;
		elseif ($TOTAL_SCORE2) $TOTAL_SCORE = $TOTAL_SCORE2;
		$cmd = " UPDATE PER_KPI_FORM SET
					TOTAL_SCORE=$TOTAL_SCORE, 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไข KPI รายบุคคล [$PER_ID : $KF_ID : $TOTAL_SCORE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $KF_ID){
		$cmd = " select KF_START_DATE from PER_KPI_FORM where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_START_DATE = $data[KF_START_DATE];
		
		$cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบ KPI รายบุคคล [$PER_ID : $KF_ID : $KF_START_DATE]");
	} // end if

	if(($UPD && $PER_ID && $KF_ID) || ($VIEW && $PER_ID && $KF_ID)){
		$cmd = "	SELECT 	KF_ID, KF_END_DATE, KF_CYCLE, TOTAL_SCORE, UPDATE_USER, UPDATE_DATE, KF_SCORE_STATUS
							FROM		PER_KPI_FORM
							WHERE	KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[KF_ID];
		$KF_END_DATE = $data[KF_END_DATE];
		$KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
		$KF_CYCLE = $data[KF_CYCLE];
		$TOTAL_SCORE1 = $TOTAL_SCORE2 = "";
		$KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
		if ($KF_SCORE_STATUS==1 || $KPI_SCORE_CONFIRM!=1 || $SESS_USERGROUP == 1 || $PER_ID_REVIEW==$SESS_PER_ID || 
			$PER_ID_REVIEW0==$SESS_PER_ID || $PER_ID_REVIEW1==$SESS_PER_ID || $PER_ID_REVIEW2==$SESS_PER_ID) {
			if ($KF_CYCLE==1) $TOTAL_SCORE1 = $data[TOTAL_SCORE];
			elseif ($KF_CYCLE==2) $TOTAL_SCORE2 = $data[TOTAL_SCORE];
		}
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($KF_ID);
		unset($KF_YEAR);
		unset($TOTAL_SCORE1);
		unset($TOTAL_SCORE2);
	} // end if
?>