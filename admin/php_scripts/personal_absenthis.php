<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        /*Release 5.2.1.3 สร้าง index สำหรับงานแสดงวันที่ลาในรอบนั้นๆ*/
        $cmdChk =" SELECT COUNT(INDEX_NAME) AS CNT
                   FROM USER_INDEXES
                   WHERE  TABLE_NAME = 'PER_ABSENTSUM' AND UPPER(INDEX_NAME) = 'IDX3_PER_ABSENTSUM'";
          $db_dpis2->send_cmd($cmdChk);
          //echo $cmdChk;
          $dataChk = $db_dpis2->get_array();
          if($dataChk[CNT]=="0"){
              $cmdA = "CREATE  INDEX IDX3_PER_ABSENTSUM ON PER_ABSENTSUM(PER_ID, START_DATE,END_DATE)";
              $db_dpis2->send_cmd($cmdA);
              $cmdA = "COMMIT";
              $db_dpis2->send_cmd($cmdA);
          }
        /**/
        
        
        
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$TODAY = show_date_format(date("Y-m-d"),1);
	//	กำหนดค่า default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
	
	set_default_timezone('Asia/Bangkok');	// ทำเวลาให้เป็นเวลาโซนที่กำหนด
/*	
	if($MFA_FLAG==1){
		$cmd = " select	ORG_ZONE from	PER_POSITIONHIS a, PER_ORG b 
							where PER_ID=$PER_ID and a.ORG_ID=b.ORG_ID and POH_EFFECTIVEDATE < = '$START_DATE' ";
		$count_abs_sum = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
	//echo "-------------------------- $cmd<br>";
		$ORG_ZONE = $data[ORG_ZONE];
	}
*/	
	//#### แสดงข้อมูลวันลาสะสม รอบ และปีนั้น (ต้องอนุญาตแล้ว)####//
	function get_ABSENT_SUM($PER_ID,$ABS_STARTDATE){
		global $db_dpis, $UPDATE_DATE, $MFA_FLAG;
		global $AS_YEAR,
		$AS_CYCLE,
		$START_DATE_1,
		$END_DATE_1,
		$START_DATE_2,
		$END_DATE_2,
		$AB_CODE_01,
		$SUM_CODE_01,
		$AB_COUNT_01,
		$SUM_COUNT_01,
		$ABS_DATE_01,
		$LAST_DATE_01,
		$AB_CODE_03,
		$SUM_CODE_03,
		$AB_COUNT_03,
		$SUM_COUNT_03,
		$ABS_DATE_03,
		$LAST_DATE_03,
		$AB_CODE_02,
		$SUM_CODE_02,
		$ABS_DATE_02,
		$LAST_DATE_02,
		$AB_CODE_04,
		$SUM_CODE_04,
		$ABS_DATE_04,
		$LAST_DATE_04,
		$AB_COUNT_TOTAL_04,
		$AB_COUNT_04;
//echo ">>>".$ABS_STARTDATE."<br>";
		if($ABS_STARTDATE){
			$CHECK_ENDDATE = save_date($ABS_STARTDATE);
                        
			if (substr($ABS_STARTDATE,3,2) > "09" || substr($ABS_STARTDATE,3,2) < "04") $AS_CYCLE = 1;
			elseif (substr($ABS_STARTDATE,3,2) > "03" && substr($ABS_STARTDATE,3,2) < "10")	$AS_CYCLE = 2;
                        
                        
			$AS_YEAR = substr($ABS_STARTDATE, 6, 4);
			if($AS_CYCLE==1){	//ตรวจสอบรอบการลา
				if (substr($ABS_STARTDATE,3,2) > "09") $AS_YEAR += 1;
				$START_DATE = ($AS_YEAR - 1) . "-10-01";
				$END_DATE = $AS_YEAR . "-03-31";
			}else if($AS_CYCLE==2){
				$START_DATE = $AS_YEAR . "-04-01";
				$END_DATE = $AS_YEAR . "-09-30"; 
			}
		}
		if(!$AS_YEAR){
			if(date("Y-m-d") < date("Y")."-10-01") $AS_YEAR = date("Y") + 543;
			else $AS_YEAR = (date("Y") + 543) + 1;
		}
		$START_DATE_1 = "01/10/". ($AS_YEAR - 1);
		$END_DATE_1 = "31/03/". $AS_YEAR;
		$START_DATE_2 = "01/04/". $AS_YEAR;
		$END_DATE_2 = "30/09/". $AS_YEAR;
		if (!$CHECK_ENDDATE) $CHECK_ENDDATE =  save_date($END_DATE_2);
	
		if (!$AS_CYCLE){
			if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $AS_CYCLE = 1;
			elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $AS_CYCLE = 2;
		}
		if($AS_CYCLE == 1){
			$START_DATE =  save_date($START_DATE_1);
			$END_DATE =  save_date($END_DATE_1);
		}else{
			$START_DATE =  save_date($START_DATE_2);
			$END_DATE =  save_date($END_DATE_2);
		} // end if
		//echo "--> $ABS_STARTDATE // $AS_YEAR :: $AS_CYCLE +".substr($ABS_STARTDATE,3,2)." $START_DATE --- $END_DATE";
		
		$AB_CODE_01 = $AB_COUNT_01 = $AB_CODE_02 = $AB_CODE_03 = $AB_COUNT_03 = $AB_CODE_04 = 0;
		$SUM_CODE_01 = $SUM_COUNT_01 = $SUM_CODE_02 = $SUM_CODE_03 = $SUM_COUNT_03 = $SUM_CODE_04 = 0;
		if ($BKK_FLAG==1)
			$code = array(	"01", "02", "03", "04" );
		else
			$code = array(	"01", "02", "03", "04" );
		$code_0 = array(	"AB_CODE_01", "AB_CODE_02", "AB_CODE_03", "AB_CODE_04" );
		
		$cmd = " SELECT		AB_CODE_01, AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04 
							FROM		PER_ABSENTSUM 
							WHERE	PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE ";
		$count_abs_sum = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
	//echo "-------------------------- $cmd<br>";
	
		if($count_abs_sum  > 0){
			$AB_CODE_01 = $data[AB_CODE_01];
			$AB_COUNT_01 = $data[AB_COUNT_01];
			$AB_CODE_02 = $data[AB_CODE_02];
			$AB_CODE_03 = $data[AB_CODE_03];
			$AB_COUNT_03 = $data[AB_COUNT_03];
			$AB_CODE_04 = $data[AB_CODE_04];
		} //end if($count_abs_sum  > 0)
		//###END SHOW PER_ABSENTSUM  ====================================
		
		for ( $i=0; $i<count($code); $i++ ) { 
			$CHECK_STARTDATE =  $START_DATE;
			if ($MFA_FLAG==1) $CHECK_ENDDATE =  "2099-12-31";

			$cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa from PER_ABSENTHIS 
				where PER_ID=$PER_ID and AB_CODE='$code[$i]' 
                                    and ABS_STARTDATE >= '$CHECK_STARTDATE' and ABS_ENDDATE < '$CHECK_ENDDATE' ";
			$db_dpis->send_cmd($cmd);
			//echo "-------------------------- $cmd<br>";
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$$code_0[$i] += $data[abs_day]; 		
			$ABS_DAY_MFA += $data[abs_day_mfa]; 		
	
			if ($code[$i]=="01" || $code[$i]=="03") {
				$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$START_DATE' and ABS_ENDDATE < '$CHECK_ENDDATE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if ($code[$i]=="01") $AB_COUNT_01 += $data[abs_count];
				elseif ($code[$i]=="03") $AB_COUNT_03 += $data[abs_count];
			}
	
			${"ABS_DATE_".$code[$i]} = "";
			// หาวันที่ลาล่าสุด
			$cmd = " select ABS_STARTDATE, ABS_ENDDATE  
							from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_ENDDATE < '$CHECK_ENDDATE'
							order by ABS_STARTDATE DESC, ABS_ENDDATE DESC, ABS_ID DESC ";
			$count_data = $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if ($count_data) {
				$arr_temp = explode("-", $data[ABS_STARTDATE]);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
				
				$arr_temp = explode("-", $data[ABS_ENDDATE]);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;

	//			echo "$cmd --> $code[$i] =>  ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era."<br>";
				${"ABS_DATE_".$code[$i]} = $ABS_ENDDATE_ce_era;		// วันที่ลาล่าสุด
			}

			${"LAST_DATE_".$code[$i]} = "";
			// หาวันที่ลาล่าสุด (ล่วงหน้า)
			$cmd = " select ABS_STARTDATE, ABS_ENDDATE  
							from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' 
							order by ABS_STARTDATE DESC, ABS_ENDDATE DESC, ABS_ID DESC ";
			$count_data = $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			if ($count_data) {
				$arr_temp = explode("-", $data[ABS_STARTDATE]);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
				
				$arr_temp = explode("-", $data[ABS_ENDDATE]);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;

	//			echo "$cmd --> $code[$i] =>  ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era."<br>";
				${"LAST_DATE_".$code[$i]} = $ABS_ENDDATE_ce_era;		// วันที่ลาล่าสุด
			}
		} // end for
		$SUM_CODE_04 = $AB_CODE_04;

		if($AS_CYCLE==2){			// รอบ 2
			$TMP_START_DATE =  save_date($START_DATE_2);/*$START_DATE_1 เดิม*/
			$TMP_END_DATE =  save_date($END_DATE_2);/*$END_DATE_1*/
			$cmd = " select AB_CODE_01, AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04 from PER_ABSENTSUM 
							where PER_ID=$PER_ID and START_DATE = '$TMP_START_DATE' and END_DATE = '$TMP_END_DATE' ";
			
                        $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SUM_CODE_01 = $AB_CODE_01 + $data[AB_CODE_01]+0;
			$SUM_COUNT_01 = $AB_COUNT_01 + $data[AB_COUNT_01]+0;
			$SUM_CODE_02 = $AB_CODE_02 + $data[AB_CODE_02]+0;
			$SUM_CODE_03 = $AB_CODE_03 + $data[AB_CODE_03]+0;
			$SUM_COUNT_03 = $AB_COUNT_03 + $data[AB_COUNT_03]+0;
			$SUM_CODE_04 = $AB_CODE_04 + $data[AB_CODE_04]+0; 

			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='01' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_CODE_01 += $data[abs_day]+0; 
	
			$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='01' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE < '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_COUNT_01 += $data[abs_count];

			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='02' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_CODE_02 += $data[abs_day]+0; 
	
			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='03' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_CODE_03 += $data[abs_day]+0; 
	
			$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='03' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE < '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_COUNT_03 += $data[abs_count];

			 $cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa 
                            from PER_ABSENTHIS 
                            where PER_ID=$PER_ID and AB_CODE='04' 
                                and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
                        
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SUM_CODE_04 += $data[abs_day]+0; 
			$ABS_DAY_MFA += $data[abs_day_mfa]+0; 
		}
                
               //ส่วนที่ทำการปรับแก้ จำนวนวันลาพักผ่อนคงเหลือ :
                    $TMP_START_DATE =  save_date($START_DATE_1);
                    $TMP_END_DATE =  save_date($END_DATE_2);                
                    
                    $cmd = " select sum(ABS_DAY) as abs_day, sum(ABS_DAY_MFA) as abs_day_mfa 
                        from PER_ABSENTHIS 
                        where PER_ID=$PER_ID and AB_CODE='04' 
                        and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
                    //echo $cmd;
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    $SUM_CODE_04 = $data[abs_day]+0; 
                    $ABS_DAY_MFA = $data[abs_day_mfa]+0;  
               // End...



		// การลาพักผ่อน
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$AS_YEAR'and PER_ID=$PER_ID ";
               // echo $cmd;
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_COUNT_TOTAL_04 = $data[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
		$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $SUM_CODE_04 + $ABS_DAY_MFA;		// วันลาสะสมที่เหลือ
                
               // echo $AB_COUNT_04 ."=". $AB_COUNT_TOTAL_04 ."-". $SUM_CODE_04 ."+". $ABS_DAY_MFA;
	} // end function
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID,PER_GENDER
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID,PER_GENDER
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID,PER_GENDER
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$PER_GENDER = trim($data[PER_GENDER]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_ABSENTHIS set AUDIT_FLAG = 'N' where ABS_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_ABSENTHIS set AUDIT_FLAG = 'Y' where ABS_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if (!$ABS_DAY_MFA) $ABS_DAY_MFA = "NULL";
	if($command=="ADD" && $PER_ID){
            /*Release 5.1.0.7 Begin*/
            $DisableTimeAtt='OPEN';
            if($IS_OPEN_TIMEATT_ES=="OPEN"){
                //หาหน่วยงานตามมอบหมาย...
                $cmdOrgAss = " SELECT ORG.ORG_ID,ORG.ORG_ID_REF FROM PER_PERSONAL PNL
                          LEFT JOIN PER_ORG_ASS ORG ON(ORG.ORG_ID=PNL.ORG_ID)
                            WHERE PNL.PER_ID=$PER_ID";
                $db_dpis2->send_cmd($cmdOrgAss);
                $dataOrgAss = $db_dpis2->get_array();   
                $ORG_ID_ASS = $dataOrgAss[ORG_ID];

                $ArrSTARTDATE = explode("/", trim($ABS_STARTDATE));
                $ArrENDDATE = explode("/", trim($ABS_ENDDATE));

                $ValSTARTDATE = $ArrSTARTDATE[2].$ArrSTARTDATE[1];
                $ValENDDATE = $ArrENDDATE[2].$ArrENDDATE[1];
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
            }
            /*Release 5.1.0.7 End*/

            if($DisableTimeAtt=="OPEN"){
                $ABS_STARTDATE = save_date($ABS_STARTDATE); 
		$ABS_ENDDATE = save_date($ABS_ENDDATE); 
		
		$cmd2 = " select  AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
							from PER_ABSENTHIS  
							where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  
								ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and 
								ABS_ENDPERIOD='$ABS_ENDPERIOD' and ABS_DAY='$ABS_DAY' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุ ตั้งแต่วันที่ ถึงวันที่ ประเภทการลา จำนวนวัน ซ้ำ !!!");
				-->   </script>	<? }  
		else {	  	
		
			$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABS_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_ABSENTHIS	(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, ABS_DAY_MFA, UPDATE_USER, UPDATE_DATE)
							values ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
							'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REMARK', '$PER_CARDNO', $ABS_DAY_MFA, $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>$cmd<br>";

			$AS_CYCLE1 = $AS_CYCLE2 = "";
 			if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
			elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
			if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
			elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
			if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
				$AS_CYCLE = 1;
				$AS_YEAR = substr($ABS_STARTDATE, 0, 4);
				if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $AS_YEAR += 1;
				$START_DATE = ($AS_YEAR - 1) . "-10-01";
				$END_DATE = $AS_YEAR . "-03-31";
				$BDH_YEAR = $AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count=$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$AS_ID = $data[max_id] + 1;
					
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
			if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
				$AS_CYCLE = 2;
				$AS_YEAR = substr($ABS_STARTDATE, 0, 4);
				$START_DATE = $AS_YEAR . "-04-01";
				$END_DATE = $AS_YEAR . "-09-30"; 
				$BDH_YEAR = $AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count=$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$AS_ID = $data[max_id] + 1;
					
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติการลา [$PER_ID : $ABS_ID : $AB_CODE]");
			$ADD_NEXT = 1;
		} //end if เช็คข้อมูลซ้ำ
            }
            
		
	} // end if

	if($command=="UPDATE" && $PER_ID && $ABS_ID){
		$ABS_STARTDATE = save_date($ABS_STARTDATE); 
		$ABS_ENDDATE = save_date($ABS_ENDDATE); 
		/*เเดิม*/
		/*$cmd2 = " select AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
						from 	PER_WORKFLOW_ABSENTHIS  
						where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  
							ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and 
							ABS_ENDPERIOD='$ABS_ENDPERIOD' 
                                                            and ABS_DAY='$ABS_DAY' 
                                                                and ABS_ID != $ABS_ID ";*/
		/*Release 5.1.0.8 begin*/
                $cmd2 = " select AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
						from 	PER_ABSENTHIS  
						where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  
							ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and 
							ABS_ENDPERIOD='$ABS_ENDPERIOD' 
                                                            and ABS_DAY='$ABS_DAY' 
                                                                and ABS_ID != $ABS_ID ";
                /*Release 5.1.0.8 End*/
                $count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุ ตั้งแต่วันที่ ถึงวันที่ ประเภทการลา จำนวนวัน ซ้ำ !!!");
		-->   </script>	<? }  
		else {	  	
	
			$cmd = " UPDATE PER_ABSENTHIS SET
							AB_CODE='$AB_CODE', 
							ABS_STARTDATE='$ABS_STARTDATE', 
							ABS_STARTPERIOD='$ABS_STARTPERIOD', 
							ABS_ENDDATE='$ABS_ENDDATE', 
							ABS_ENDPERIOD='$ABS_ENDPERIOD', 
							ABS_DAY='$ABS_DAY',  
							ABS_REMARK='$ABS_REMARK',  
							PER_CARDNO='$PER_CARDNO', 
							ABS_DAY_MFA=$ABS_DAY_MFA,  
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE ABS_ID=$ABS_ID ";
			$db_dpis->send_cmd($cmd);
			
			$AS_CYCLE1 = $AS_CYCLE2 = "";
 			if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
			elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
			if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
			elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
			if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
				$AS_CYCLE = 1;
				$AS_YEAR = substr($ABS_STARTDATE, 0, 4);
				if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $AS_YEAR += 1;
				$START_DATE = ($AS_YEAR - 1) . "-10-01";
				$END_DATE = $AS_YEAR . "-03-31";
				$BDH_YEAR = $AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count=$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$AS_ID = $data[max_id] + 1;
					
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}
			if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
				$AS_CYCLE = 2;
				$AS_YEAR = substr($ABS_STARTDATE, 0, 4);
				$START_DATE = $AS_YEAR . "-04-01";
				$END_DATE = $AS_YEAR . "-09-30"; 
				$BDH_YEAR = $AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count=$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$AS_ID = $data[max_id] + 1;
					
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			}

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติการลา [$PER_ID : $ABS_ID : $AB_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $ABS_ID){
		$cmd = " select AB_CODE from PER_ABSENTHIS where ABS_ID=$ABS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_CODE = $data[AB_CODE];
		
		$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABS_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติการลา [$PER_ID : $ABS_ID : $AB_CODE]");
	} // end if

	if(($UPD && $PER_ID && $ABS_ID) || ($VIEW && $PER_ID && $ABS_ID)){
		$cmd = "	SELECT 	ABS_ID, pah.AB_CODE, pat.AB_NAME,pat.AB_COUNT, ABS_STARTDATE, ABS_STARTPERIOD, 
												ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, ABS_DAY_MFA, pah.UPDATE_USER, pah.UPDATE_DATE   
							FROM		PER_ABSENTHIS pah, PER_ABSENTTYPE pat  
							WHERE	ABS_ID=$ABS_ID and pah.AB_CODE=pat.AB_CODE  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABS_STARTDATE = show_date_format(trim($data[ABS_STARTDATE]), 1);
		$ABS_ENDDATE = show_date_format(trim($data[ABS_ENDDATE]), 1);
		$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];		
		$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];		
		$ABS_DAY = trim(round($data[ABS_DAY],2));
		
		// kittiphat 05/11/2561
		// หาค่าวันลาทำการ
		
		$abs_startdate_chkhid =trim($data[ABS_STARTDATE]);
		$startperiod_chkhid =$ABS_STARTPERIOD;
		$abs_enddate_chkhid =trim($data[ABS_ENDDATE]);
		$endperiod_chkhid =$ABS_ENDPERIOD;
		$ab_code_chkhid =$AB_CODE;
		$per_id_chkhid =$PER_ID;
		
		if($abs_startdate_chkhid==$abs_enddate_chkhid){
			$def_val_start_chkhid=2;
			$def_val_end_chkhid=1;
			if($startperiod_chkhid==1 && $endperiod_chkhid==1){
				$def_val_start_chkhid=1;
				$def_val_end_chkhid=2;
			}
		}else{
			$def_val_start_chkhid=2;
			$def_val_end_chkhid=1;
		}
		
		
		$cmd_chkhid = "WITH
				AllWorkDay As
				(
				  select /*+ MATERIALIZE */ * from (
					select /*+ MATERIALIZE */ x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
					  case when exists (select null from PER_HOLIDAY where HOL_DATE = x.LISTDATE) then 1 else 0 end end) HOL
					  , (case when LISTDATE='$abs_startdate_chkhid' then (case when $startperiod_chkhid<>$def_val_start_chkhid then 1 else 0.5 end) 
						else (case when LISTDATE='$abs_enddate_chkhid' then (case when $endperiod_chkhid<>$def_val_end_chkhid then 1 else 0.5 end) else 1 end )end) CNT
					from (
					  select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
						SELECT (TO_DATE('$abs_startdate_chkhid', 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
						 WHERE (TO_DATE('$abs_startdate_chkhid', 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE('$abs_enddate_chkhid', 'YYYY-MM-DD')
					  )
					) x
				  )
				  where (select ab_count from PER_ABSENTTYPE where ab_code='$ab_code_chkhid')=1 or HOL=0

				)

				select /*+ MATERIALIZE */ s.as_id,s.as_year,s.as_cycle,
                                    to_char((TO_DATE(min(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_start,
                                    to_char((TO_DATE(max(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_end,
                                    sum(a.CNT) nday ,min(LISTDATE) abs_startorder
                                    from AllWorkDay a
				left join PER_ABSENTSUM s on (s.per_id=$per_id_chkhid and a.LISTDATE between s.start_date and s.end_date)
				group by s.as_id,s.as_year,s.as_cycle
				ORDER by abs_startorder";
	   $db_dpis->send_cmd($cmd_chkhid);
	  // echo '<pre>'.$cmd_chkhid;
	   $HID_ABS_DAY = 0;
	   while ($data_chkhid = $db_dpis->get_array_array()) {
		   $HID_ABS_DAY = round($HID_ABS_DAY,2)+ round($data_chkhid[NDAY],2);
	   }
		
		///////////////////////////////////////////////
		
		
		
		$ABS_REMARK = $data[ABS_REMARK];
		$ABS_DAY_MFA = $data[ABS_DAY_MFA];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$AB_CODE = $data[AB_CODE];
		$AB_NAME = $data[AB_NAME];
		$AB_COUNT = $data[AB_COUNT];

		get_ABSENT_SUM($PER_ID,trim($ABS_STARTDATE));	//ดึงวันลาสะสมของคนที่เลือกมาแก้ไขขึ้นมา

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($ABS_ID);
		unset($ABS_STARTDATE);
		unset($ABS_STARTPERIOD);		
		unset($ABS_ENDDATE);
		unset($ABS_ENDPERIOD);
		unset($ABS_DAY);		
		unset($ABS_REMARK);		
		unset($ABS_DAY_MFA);		

		unset($AB_CODE);
		unset($AB_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		unset($AB_CODE_1);
		unset($AB_CODE_2);
		unset($AB_CODE_3);
		unset($AB_CODE_4);
		unset($SUM_CODE_1);
		unset($SUM_CODE_2);
		unset($SUM_CODE_3);
		unset($SUM_CODE_4);
		unset($AB_COUNT_01);
		unset($AB_COUNT_03);
		unset($AB_COUNT_04);
		unset($SUM_COUNT_01);
		unset($SUM_COUNT_03);
		unset($ABS_DATE_01);
		unset($ABS_DATE_02);
		unset($ABS_DATE_03);
		unset($ABS_DATE_04);
		unset($LAST_DATE_01);
		unset($LAST_DATE_02);
		unset($LAST_DATE_03);
		unset($LAST_DATE_04);
		unset($AB_COUNT_TOTAL_04);
		
		$ABS_STARTPERIOD = 3;
		$ABS_ENDPERIOD = 3;
		$ABS_LETTER = 3;
	} // end if
?>