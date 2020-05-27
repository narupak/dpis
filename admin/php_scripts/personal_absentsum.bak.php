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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_STARTDATE
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID, PER_PERSONAL.PER_STARTDATE
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID, PER.PER_STARTDATE
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$PER_STARTDATE = trim($data[PER_STARTDATE]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	$AGE_DIFF = date_difference($UPDATE_DATE, $PER_STARTDATE, "y");
//	if ($AGE_DIFF < 0.5) $INCREASE_AB_CODE_04 = 0;
//	elseif ($AGE_DIFF > 10) $INCREASE_AB_CODE_04 = 30;
//	else $INCREASE_AB_CODE_04 = 20; 
	if(!$AS_YEAR){
		if(date("Y-m-d") <= date("Y")."-10-01") $AS_YEAR = date("Y") + 543;
		else $AS_YEAR = (date("Y") + 543) + 1;
	}
	$START_DATE_1 = "01/10/". ($AS_YEAR - 1);
	$END_DATE_1 = "31/03/". $AS_YEAR;
	$START_DATE_2 = "01/04/". $AS_YEAR;
	$END_DATE_2 = "30/09/". $AS_YEAR;

	if (!$AS_CYCLE) 
		if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $AS_CYCLE = 1;
		elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $AS_CYCLE = 2;
		
	if($AS_CYCLE == 1){
		$START_DATE =  save_date($START_DATE_1);
		$END_DATE =  save_date($END_DATE_1);
	}else{
		$START_DATE =  save_date($START_DATE_2);
		$END_DATE =  save_date($END_DATE_2);
	} // end if

	if($command=="ADD" || $command=="UPDATE"){
		if (!$AB_CODE_01) $AB_CODE_01 = "NULL";
		if (!$AB_COUNT_01) $AB_COUNT_01 = "NULL";
		if (!$AB_CODE_02) $AB_CODE_02 = "NULL";
		if (!$AB_CODE_03) $AB_CODE_03 = "NULL";
		if (!$AB_COUNT_03) $AB_COUNT_03 = "NULL";
		if (!$AB_CODE_04) $AB_CODE_04 = "NULL";
		if (!$AB_CODE_05) $AB_CODE_05 = "NULL";
		if (!$AB_CODE_06) $AB_CODE_06 = "NULL";
		if (!$AB_CODE_07) $AB_CODE_07 = "NULL";
		if (!$AB_CODE_08) $AB_CODE_08 = "NULL";
		if (!$AB_CODE_09) $AB_CODE_09 = "NULL";
		if (!$AB_CODE_10) $AB_CODE_10 = "NULL";
		if (!$AB_CODE_11) $AB_CODE_11 = "NULL";
		if (!$AB_CODE_12) $AB_CODE_12 = "NULL";
		if (!$AB_CODE_13) $AB_CODE_13 = "NULL";
		if (!$AB_CODE_14) $AB_CODE_14 = "NULL";
		if (!$AB_CODE_15) $AB_CODE_15 = "NULL";
		if (!$TOTAL_LEAVE) $TOTAL_LEAVE = "NULL";
		if (!$TOTAL_COUNT) $TOTAL_COUNT = "NULL";
	} // end if

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_ABSENTSUM set AUDIT_FLAG = 'N' where AS_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_ABSENTSUM set AUDIT_FLAG = 'Y' where AS_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID && $AS_YEAR && $AS_CYCLE){
		$cmd2="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ ปีงบประมาณ และรอบการลา ซ้ำ !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$AS_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, AB_CODE_01, 
							AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04, AB_CODE_05, AB_CODE_06, AB_CODE_07, 
							AB_CODE_08, AB_CODE_09, AB_CODE_10, AB_CODE_11, AB_CODE_12, AB_CODE_13, AB_CODE_14, AB_CODE_15, 
							TOTAL_LEAVE, TOTAL_COUNT, PER_CARDNO, UPDATE_USER, UPDATE_DATE, AS_REMARK)
							values ($AS_ID, $PER_ID, '$AS_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', $AB_CODE_01, $AB_COUNT_01, 
							$AB_CODE_02, $AB_CODE_03, $AB_COUNT_03, $AB_CODE_04, $AB_CODE_05, $AB_CODE_06, $AB_CODE_07, 
							$AB_CODE_08, $AB_CODE_09, $AB_CODE_10, $AB_CODE_11, $AB_CODE_12, $AB_CODE_13, $AB_CODE_14, 
							$AB_CODE_15, $TOTAL_LEAVE, $TOTAL_COUNT, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$AS_REMARK') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มสรุปวันลาสะสม [$PER_ID : $AS_ID : $AS_YEAR : $AS_CYCLE]");
			$ADD_NEXT = 1;
		} // end if
	} // end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $AS_ID){
		$cmd = " UPDATE PER_ABSENTSUM SET
						AB_CODE_01=$AB_CODE_01, 
						AB_COUNT_01=$AB_COUNT_01, 
						AB_CODE_02=$AB_CODE_02, 
						AB_CODE_03=$AB_CODE_03, 
						AB_COUNT_03=$AB_COUNT_03, 
						AB_CODE_04=$AB_CODE_04, 
						AB_CODE_05=$AB_CODE_05, 
						AB_CODE_06=$AB_CODE_06, 
						AB_CODE_07=$AB_CODE_07, 
						AB_CODE_08=$AB_CODE_08, 
						AB_CODE_09=$AB_CODE_09, 
						AB_CODE_10=$AB_CODE_10, 
						AB_CODE_11=$AB_CODE_11, 
						AB_CODE_12=$AB_CODE_12, 
						AB_CODE_13=$AB_CODE_13, 
						AB_CODE_14=$AB_CODE_14, 
						AB_CODE_15=$AB_CODE_15, 
						TOTAL_LEAVE=$TOTAL_LEAVE, 
						TOTAL_COUNT=$TOTAL_COUNT, 
						PER_CARDNO='$PER_CARDNO', 
						AS_REMARK='$AS_REMARK', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
					WHERE AS_ID=$AS_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขสรุปวันลาสะสม [$PER_ID : $AS_ID : $AS_YEAR]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $AS_ID){
		$cmd = " select AS_YEAR from PER_ABSENTSUM where AS_ID=$AS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AS_YEAR = $data[AS_YEAR];
		
		$cmd = " delete from PER_ABSENTSUM where AS_ID=$AS_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบสรุปวันลาสะสม [$PER_ID : $AS_ID : $AS_YEAR]");
	} // end if

	if(($UPD && $PER_ID && $AS_ID) || ($VIEW && $PER_ID && $AS_ID)){
		$cmd = " SELECT		AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, AB_CODE_01, AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04, 
							AB_CODE_05, AB_CODE_06, AB_CODE_07, AB_CODE_08, AB_CODE_09, AB_CODE_10, AB_CODE_11, 
							AB_CODE_12, AB_CODE_13, AB_CODE_14, AB_CODE_15, TOTAL_LEAVE, 
							TOTAL_COUNT, PER_CARDNO, AS_REMARK, UPDATE_USER, UPDATE_DATE 
							FROM		PER_ABSENTSUM 
							WHERE	AS_ID=$AS_ID ";
		//echo '<pre>'.$cmd;
                $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AS_YEAR = trim($data[AS_YEAR]);
		$AS_CYCLE = $data[AS_CYCLE];
		if($AS_CYCLE==1){	//ตรวจสอบรอบการลา
			$START_DATE_1 = show_date_format($data[START_DATE], 1);
			$END_DATE_1 = show_date_format($data[END_DATE], 1);
			$START_DATE_2 = "01/04/". $AS_YEAR;
			$END_DATE_2 = "30/09/". $AS_YEAR;
		}else if($AS_CYCLE==2){
			$START_DATE_1 = "01/10/". ($AS_YEAR - 1);
			$END_DATE_1 = "31/03/". $AS_YEAR;
			$START_DATE_2 = show_date_format($data[START_DATE], 1);
			$END_DATE_2 = show_date_format($data[END_DATE], 1);
		}
		$START_DATE = trim($data[START_DATE]);
		$END_DATE = trim($data[END_DATE]);

                /*Release 5.1.0.7 Begin*/
                $DisableTimeAtt='OPEN';
                $CntLate = 0;
                $CntNotWork = 0;
                if($IS_OPEN_TIMEATT_ES=="OPEN"){
                    //หาหน่วยงานตามมอบหมาย...
                    $cmdOrgAss = " SELECT ORG.ORG_ID,ORG.ORG_ID_REF FROM PER_PERSONAL PNL
                              LEFT JOIN PER_ORG_ASS ORG ON(ORG.ORG_ID=PNL.ORG_ID)
                                WHERE PNL.PER_ID=$PER_ID";
                    $db_dpis2->send_cmd($cmdOrgAss);
                    $dataOrgAss = $db_dpis2->get_array();   
                    $ORG_ID_ASS = $dataOrgAss[ORG_ID];
                
                    $ArrSTARTDATE = explode("-", trim($START_DATE));
                    $ArrENDDATE = explode("-", trim($END_DATE));
                    
                    $ValSTARTDATE = ($ArrSTARTDATE[0]+543).$ArrSTARTDATE[1];
                    $ValENDDATE = ($ArrENDDATE[0]+543).$ArrENDDATE[1];
                    $cmdClose = " SELECT CLOSE_YEAR,CLOSE_MONTH 
                                    FROM PER_WORK_TIME_CONTROL 
                                    WHERE CLOSE_DATE IS NOT NULL AND DEPARTMENT_ID = ".$ORG_ID_ASS."
                                        AND (CLOSE_YEAR||CASE WHEN length(CLOSE_MONTH)=1 THEN '0'||CLOSE_MONTH ELSE ''||CLOSE_MONTH END)
                                        BETWEEN $ValSTARTDATE AND $ValENDDATE ";
                    $db_dpis2->send_cmd($cmdClose);
                    $dataATT = $db_dpis2->get_array();
                    if($dataATT){
                        $DisableTimeAtt='CLOSE';
                    }
                    
                    //นับสรุปวัน สาย และ ขาดราชการ
                    if($DisableTimeAtt=='CLOSE'){}
                    $ValSTARTDATE = ($ArrSTARTDATE[0]).$ArrSTARTDATE[1].$ArrSTARTDATE[2];
                    $ValENDDATE = ($ArrENDDATE[0]).$ArrENDDATE[1].$ArrENDDATE[2];
                    $cmdSum ="SELECT SUM(CASE WHEN WORK_FLAG=1 THEN 1 ELSE 0 END) AS SUM_CODE1, 
                            SUM(CASE WHEN WORK_FLAG=2 THEN 1 ELSE 0 END) AS SUM_CODE2
                          FROM PER_WORK_TIME 
                          WHERE PER_ID=$PER_ID AND HOLIDAY_FLAG=0 AND TO_CHAR(WORK_DATE,'YYYYMMDD') BETWEEN '$ValSTARTDATE' AND '$ValENDDATE'";
                    //echo $cmdSum;
                    $db_dpis2->send_cmd($cmdSum);
                    $dataSum = $db_dpis2->get_array();
                    $CntLate = $dataSum[SUM_CODE1]+0;
                    $CntNotWork = $dataSum[SUM_CODE2]+0;
                }
                /*Release 5.1.0.7 End*/
                
                
                
		$TOTAL_LEAVE = $TOTAL_LEAVE_1 = $TOTAL_LEAVE_2 = 0;
		$TOTAL_COUNT = $TOTAL_COUNT_1 = $TOTAL_COUNT_2 = 0;
		$AB_CODE_01 = $data[AB_CODE_01];
		$AB_COUNT_01 = $data[AB_COUNT_01];
		$AB_CODE_02 = $data[AB_CODE_02];
		$AB_CODE_03 = $data[AB_CODE_03];
		$AB_COUNT_03 = $data[AB_COUNT_03];
		$AB_CODE_04 = $data[AB_CODE_04];
		$AB_CODE_05 = $data[AB_CODE_05];
		$AB_CODE_06 = $data[AB_CODE_06];
		$AB_CODE_07 = $data[AB_CODE_07];
		$AB_CODE_08 = $data[AB_CODE_08];
		$AB_CODE_09 = $data[AB_CODE_09];
		$AB_CODE_10 = $data[AB_CODE_10];
		$AB_CODE_11 = $data[AB_CODE_11];
		$AB_CODE_12 = $data[AB_CODE_12];
		$AB_CODE_13 = $data[AB_CODE_13];
		$AB_CODE_14 = $data[AB_CODE_14];
		$AB_CODE_15 = $data[AB_CODE_15];
		$TOTAL_LEAVE = $AB_CODE_01 + $AB_CODE_03;
		$TOTAL_COUNT = $AB_COUNT_01 + $AB_COUNT_03;
		$AS_REMARK = trim($data[AS_REMARK]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$code = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15" );
		$code_0 = array("AB_CODE_01", "AB_CODE_02", "AB_CODE_03", "AB_CODE_04", "AB_CODE_05", "AB_CODE_06", "AB_CODE_07", 
                                "AB_CODE_08", "AB_CODE_09", "AB_CODE_10", "AB_CODE_11", "AB_CODE_12", "AB_CODE_13", "AB_CODE_14", "AB_CODE_15" );
		$code_1 = array("AB_CODE_01_1", "AB_CODE_02_1", "AB_CODE_03_1", "AB_CODE_04_1", "AB_CODE_05_1", "AB_CODE_06_1", "AB_CODE_07_1", 
				"AB_CODE_08_1", "AB_CODE_09_1", "AB_CODE_10_1", "AB_CODE_11_1", "AB_CODE_12_1", "AB_CODE_13_1", "AB_CODE_14_1", "AB_CODE_15_1" );
		$code_2 = array("AB_CODE_01_2", "AB_CODE_02_2", "AB_CODE_03_2", "AB_CODE_04_2", "AB_CODE_05_2", "AB_CODE_06_2", "AB_CODE_07_2", 
				"AB_CODE_08_2", "AB_CODE_09_2", "AB_CODE_10_2", "AB_CODE_11_2", "AB_CODE_12_2", "AB_CODE_13_2", "AB_CODE_14_2", "AB_CODE_15_2" );
//		$code_3 = array("AB_CODE_01_3", "AB_CODE_02_3", "AB_CODE_03_3", "AB_CODE_04_3", "AB_CODE_05_3", "AB_CODE_06_3", "AB_CODE_07_3", 
//				"AB_CODE_08_3", "AB_CODE_09_3", "AB_CODE_10_3", "AB_CODE_11_3", "AB_CODE_12_3", "AB_CODE_13_3", "AB_CODE_14_3", "AB_CODE_15_3" );
		for ( $i=0; $i<count($code); $i++ ) { 
			$cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa from PER_ABSENTHIS a, PER_ABSENTTYPE b
				where PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE and AB_TYPE='$code[$i]' and ABS_STARTDATE >= '$START_DATE' and ABS_ENDDATE <= '$END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if ($code[$i]=="04")$AB_CODE_04_3 = $data[abs_day_mfa] + 0; 
			$$code_1[$i] = $data[abs_day]; 
			if ($code[$i]=="01" || $code[$i]=="03") $TOTAL_LEAVE_1 += $data[abs_day];

			if ($code[$i]=="01" || $code[$i]=="03") {
				$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$START_DATE' and ABS_ENDDATE <= '$END_DATE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if ($code[$i]=="01") $AB_COUNT_01_1 = $data[abs_count];
				elseif ($code[$i]=="03") $AB_COUNT_03_1 = $data[abs_count];
				
				if ($code[$i]=="01" || $code[$i]=="03") $TOTAL_COUNT_1 += $data[abs_count];
			}

			$$code_2[$i] = $$code_0[$i] + $$code_1[$i];
			if ($code[$i]=="01") $AB_COUNT_01_2 = $AB_COUNT_01 + $AB_COUNT_01_1;
			elseif ($code[$i]=="03") $AB_COUNT_03_2 = $AB_COUNT_03 + $AB_COUNT_03_1;
/*
			if($AS_CYCLE==2){	
				$TMP_START_DATE =  save_date($START_DATE_1);
				$TMP_END_DATE =  save_date($END_DATE_1);
				$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$$code_3[$i] = $data[abs_day]; 
				if ($code[$i]=="01" || $code[$i]=="03") $TOTAL_LEAVE_2 += $data[abs_day];

				if ($code[$i]=="01" || $code[$i]=="03") {
					$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
									where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					if ($code[$i]=="01") $AB_COUNT_01_3 = $data[abs_count];
					elseif ($code[$i]=="03") $AB_COUNT_03_3 = $data[abs_count];
					
					if ($code[$i]=="01" || $code[$i]=="03") $TOTAL_COUNT_1 += $data[abs_count];
				}
				$$code_2[$i] = $$code_2[$i] + $$code_3[$i];
				if ($code[$i]=="01") $AB_COUNT_01_2 += $AB_COUNT_01_3;
				elseif ($code[$i]=="03") $AB_COUNT_03_2 += $AB_COUNT_03_3;
			} */
		}
		/*if($AS_CYCLE==2){	
			$TMP_START_DATE =  save_date($START_DATE_1);
			$TMP_END_DATE =  save_date($END_DATE_1);
			$cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TMP_AB_CODE_04_3 = $data[abs_day_mfa] + 0; 
			$TMP_AB_CODE_04 = $data[abs_day] + 0; 

			$cmd = " select AB_CODE_04 from PER_ABSENTSUM 
							where PER_ID=$PER_ID and START_DATE = '$TMP_START_DATE' and END_DATE = '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_AB_CODE_04 += $data[AB_CODE_04]+0; 
		}*/
                
                /*กำหนดการดึงวันที่แสดงใหม่ กรณีที่มีการลาแบบคร่อมรอบ*/
                //echo "$PER_ID,$AS_YEAR ,$AS_CYCLE ";
                $cmd = "WITH
                        AllWorkDay As
                        (
                          select * from (
                            select x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
                              case when exists (select null from PER_HOLIDAY where HOL_DATE = x.LISTDATE) then 1 else 0 end end) HOL
                            from (
                              select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
                                SELECT (TO_DATE((select min(ABS_STARTDATE) from PER_ABSENTHIS
                                    where (ABS_STARTDATE < (select START_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)) 
                                       and ABS_ENDDATE > (select START_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)) 
                                       and ABS_ENDDATE <= (select END_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)))
                                ), 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
                                 WHERE (TO_DATE((select min(ABS_STARTDATE) from PER_ABSENTHIS
                                    where (ABS_STARTDATE < (select START_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)) 
                                       and ABS_ENDDATE > (select START_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)) 
                                       and ABS_ENDDATE <= (select END_DATE from PER_ABSENTSUM where PER_ID=$PER_ID and START_DATE=(select min(START_DATE) from PER_ABSENTSUM where PER_ID=$PER_ID)))
                                ), 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE((select max(ABS_ENDDATE) from PER_ABSENTHIS where PER_ID=$PER_ID), 'YYYY-MM-DD')
                              )
                            ) x 
                          )
                        )
                        select AS_YEAR,AS_CYCLE,START_DATE,END_DATE,
                          sum(ABS_DAY_MFA) ABS_DAY_MFA,
                          sum(case when AB_TYPE='01' then ABS_DAY else 0 end) as SICK_ABS,
                          sum(case when AB_TYPE='03' then ABS_DAY else 0 end) as KIT_ABS,
                          sum(case when AB_TYPE='04' then ABS_DAY else 0 end) as PAKPON_ABS,
                          sum(case when AB_TYPE='07' then ABS_DAY else 0 end) as LASUKSA_ABS,
                          sum(case when AB_TYPE='10' then ABS_DAY else 0 end) as SAY_ABS,
                          sum(case when AB_TYPE='13' then ABS_DAY else 0 end) as KHAD_ABS

                        from (
                           select AS_YEAR,AS_CYCLE,START_DATE,END_DATE,AB_TYPE,sum(ABS_DAY) ABS_DAY,sum(ABS_DAY_MFA) ABS_DAY_MFA
                           from ( 

                            select s.AS_YEAR,s.AS_CYCLE,s.START_DATE,s.END_DATE,t.AB_TYPE,h.ABS_DAY,ABS_DAY_MFA
                            from PER_ABSENTSUM s
                            left join PER_ABSENTHIS h on (h.PER_ID=s.PER_ID
                              and (h.ABS_STARTDATE >= s.START_DATE and h.ABS_ENDDATE <= s.END_DATE))
                            left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE)
                            where s.PER_ID=$PER_ID

                            union all

                            select  AS_YEAR,AS_CYCLE,START_DATE,END_DATE,AB_TYPE,ABS_DAY ABS_DAY,ABS_DAY_MFA
                            from (
                              select t.AB_TYPE,case when LISTDATE=trim(ABS_STARTDATE) then 
                                                  case when ABS_STARTPERIOD=3 then 1 else 0.5 end
                                                else case when LISTDATE=trim(ABS_ENDDATE) then 
                                                        case when ABS_ENDPERIOD=3 then 1 else 0.5 end
                                                      else 
                                                        1
                                                     end
                                               end abs_day,ABS_DAY_MFA,s.START_DATE,s.END_DATE,s.AS_CYCLE,s.AS_YEAR
                              from PER_ABSENTSUM s
                              left join PER_ABSENTHIS h on (h.PER_ID=s.PER_ID
                                and ((h.ABS_STARTDATE < s.START_DATE and h.ABS_ENDDATE between s.START_DATE and s.END_DATE) or
                                     (h.ABS_ENDDATE > s.END_DATE and h.ABS_STARTDATE between s.START_DATE and s.END_DATE)) or 
                                     (h.ABS_STARTDATE < s.START_DATE and h.ABS_ENDDATE > s.END_DATE)
                                     )
                              left join PER_ABSENTTYPE t on (t.AB_CODE=h.AB_CODE and h.PER_ID=s.PER_ID)
                              left join AllWorkDay on (h.PER_ID=s.PER_ID and ((t.AB_COUNT=2 and HOL=0) or (t.AB_COUNT=1 and (HOL=0 or HOL=1)) ) 
                                    and LISTDATE between trim(h.ABS_STARTDATE) and h.ABS_ENDDATE and LISTDATE between trim(s.START_DATE) and s.END_DATE)
                              where s.PER_ID= $PER_ID
                            )
                          )
                          group by AS_YEAR,AS_CYCLE,START_DATE,END_DATE,AB_TYPE
                        ) 
                        WHERE as_year=$AS_YEAR AND as_cycle=$AS_CYCLE
                        group by AS_YEAR,AS_CYCLE,START_DATE,END_DATE";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
                $AB_CODE_01_1 = $data[SICK_ABS]; 
                $AB_CODE_03_1 = $data[KIT_ABS]; 
                $AB_CODE_10_1 = $data[SAY_ABS]; 
                $AB_CODE_13_1 = $data[KHAD_ABS]; 
                $AB_CODE_07_1 = $data[LASUKSA_ABS]; 
                $AB_CODE_04_1 = $data[PAKPON_ABS]; 
                /**/
                
                
                
                
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$AS_YEAR'and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$VC_DAY = $data[VC_DAY]; 
                //echo $AB_CODE_04_2.' + '.$AB_CODE_04_3.' - '.$TMP_AB_CODE_04.' + '.$TMP_AB_CODE_04_3;
		//$REMAIN_VC_DAY = $VC_DAY - $AB_CODE_04_2 + $AB_CODE_04_3 - $TMP_AB_CODE_04 + $TMP_AB_CODE_04_3;/*เดืิม*/
                
                
                $REMAIN_VC_DAY = $VC_DAY - $AB_CODE_04_1 + $AB_CODE_04_3;/**/
                if($REMAIN_VC_DAY<0){$REMAIN_VC_DAY=0;}

		$TOTAL_LEAVE_2 = $TOTAL_LEAVE + $TOTAL_LEAVE_1;
		$TOTAL_COUNT_2 = $TOTAL_COUNT + $TOTAL_COUNT_1;
	} // end if

	if( !$UPD && !$VIEW ){
		unset($AS_ID);
		unset($AB_CODE_01);		
		unset($AB_CODE_01_1);		
		unset($AB_CODE_01_2);		
		unset($AB_COUNT_01);		
		unset($AB_COUNT_01_1);		
		unset($AB_COUNT_01_2);		
		unset($AB_CODE_02);
		unset($AB_CODE_02_1);		
		unset($AB_CODE_02_2);		
		unset($AB_CODE_03);
		unset($AB_CODE_03_1);		
		unset($AB_CODE_03_2);		
		unset($AB_COUNT_03);		
		unset($AB_COUNT_03_1);		
		unset($AB_COUNT_03_2);		
		unset($AB_CODE_04);
		unset($AB_CODE_04_1);		
		unset($AB_CODE_04_2);		
		unset($AB_CODE_04_3);		
		unset($AB_CODE_05);
		unset($AB_CODE_05_1);		
		unset($AB_CODE_05_2);		
		unset($AB_CODE_06);
		unset($AB_CODE_06_1);		
		unset($AB_CODE_06_2);		
		unset($AB_CODE_07);
		unset($AB_CODE_07_1);		
		unset($AB_CODE_07_2);		
		unset($AB_CODE_08);
		unset($AB_CODE_08_1);		
		unset($AB_CODE_08_2);		
		unset($AB_CODE_09);		
		unset($AB_CODE_09_1);		
		unset($AB_CODE_09_2);		
		unset($AB_CODE_10);
		unset($AB_CODE_10_1);		
		unset($AB_CODE_10_2);		
		unset($AB_CODE_11);
		unset($AB_CODE_11_1);		
		unset($AB_CODE_11_2);		
		unset($AB_CODE_12);
		unset($AB_CODE_12_1);		
		unset($AB_CODE_12_2);		
		unset($AB_CODE_13);
		unset($AB_CODE_13_1);		
		unset($AB_CODE_13_2);		
		unset($AB_CODE_14);
		unset($AB_CODE_14_1);		
		unset($AB_CODE_14_2);		
		unset($AB_CODE_15);
		unset($AB_CODE_15_1);		
		unset($AB_CODE_15_2);		
		unset($TOTAL_LEAVE);
		unset($TOTAL_LEAVE_1);
		unset($TOTAL_LEAVE_2);
		unset($TOTAL_COUNT);
		unset($TOTAL_COUNT_1);
		unset($TOTAL_COUNT_2);
		unset($AS_REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		unset($VC_DAY);
		unset($REMAIN_VC_DAY);
	} // end if
?>