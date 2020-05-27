<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");			

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

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
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i A", time());
	$CANCEL_DATETIME = date("Y-m-d H:i A", time());
	$ORI_APPROVE_DATETIME = date("Y-m-d H:i A", time());
	$TODATE = date("Y-m-d");
	$temp_date = explode("-", $TODATE);
	$temp_todate = ($temp_date[0]) ."-". $temp_date[1] ."-". $temp_date[2];
	if(!$search_abs_startdate) {
		$search_abs_startdate = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
	}
	if(!$search_abs_enddate) {		// จากเดือนปัจจุบันไปอีก 60 วัน
		if($temp_date[1]<11){
			$search_abs_endmonth = ($temp_date[1] + 2);
			if($search_abs_endmonth<10) $search_abs_endmonth = "0".($search_abs_endmonth);
			$search_abs_endyear = $temp_date[0];
		}else{	// เดือน 11 กับ 12 ต้องไปเริ่มปีหน้า
			$search_abs_endmonth = "0".($temp_date[1] - 10);
			$search_abs_endyear = ($temp_date[0] + 1);
		}
		$max_date = get_max_date($search_abs_endmonth, $search_abs_endyear);
		$search_abs_enddate = $max_date."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
	}
//	echo "-> $result_set  / $CREATE_DATE <br>";
	
	$alert_confirm_absent = "";
	
	//#### ส่งอีเมล์การลาอัตโนมัติ ####//
	function absent_auto_sendmail($ABS_SENDER_MAIL, $ABS_SENDER_NAME, $ABS_RCV_MAIL, $ABS_RCV_NAME, $AB_NAME, $ABS_STARTDATE, $ABS_ENDDATE, $ABS_REASON, $ABS_APPROVE){
			//global $db_dpis;
			$result = 0;		$abs_reason = "";
			if($ABS_SENDER_MAIL)	$ABS_SENDER_MAIL_SHOW = "<".$ABS_SENDER_MAIL.">";
			if($ABS_RCV_MAIL)	$ABS_RCV_MAIL_SHOW = "<".$ABS_RCV_MAIL.">";
		
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
			// Additional headers
			$headers .= 'To: '.$ABS_RCV_NAME." ".$ABS_RCV_MAIL_SHOW.'\r\n';
			$headers .= 'From: '.$ABS_SENDER_NAME." ".$ABS_SENDER_MAIL_SHOW.'\r\n';
			//$headers .= 'Cc: ' . "\r\n";	$headers .= 'Bcc: ' . "\r\n";
			$headers .= 'X-Mailer: PHP/'. phpversion();

			if($ABS_STARTDATE){
				$arr_temp = explode("-", $ABS_STARTDATE);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
			}
			
			if($ABS_ENDDATE){
				$arr_temp = explode("-", $ABS_ENDDATE);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;
			}
			if($ABS_REASON)	$abs_reason = " เนื่องจาก".$ABS_REASON;
			if ($ABS_APPROVE==0){  //ผู้ลา ส่งถึง ผู้อนุญาต
				$subject = $ABS_SENDER_NAME." ขออนุญาตลาในวันที่ ".$ABS_STARTDATE_ce_era; 
				$message = "\n".$ABS_SENDER_NAME." ขออนุญาตลาวันที่ ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era." (".$AB_NAME.$abs_reason.")\n\n"; 
				$result_type = 1;
			}else{		// ผู้อนุญาต ส่งถึง ผู้ลา
				if ($ABS_APPROVE==1)	$ABS_APPROVE = "อนุญาตการลา";
				if ($ABS_APPROVE==2)	$ABS_APPROVE = "ไม่อนุญาตการลา";
				$subject = $ABS_SENDER_NAME." ".$ABS_APPROVE." ในวันที่ ".$ABS_STARTDATE_ce_era."\n\n"; 
				$message = "\n".$ABS_SENDER_NAME.$ABS_APPROVE."ของ".$ABS_RCV_NAME." วันที่ ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era." (".$AB_NAME.$abs_reason.")\n\n"; 
				$result_type = 2;
			}
			$footers = "\n\n".$ABS_SENDER_NAME."\n";
			$message = wordwrap(trim($message.$footers), 70);
			
			// Mail it
			$result = 0;
			if($ABS_RCV_MAIL)	$result = @mail($ABS_RCV_MAIL, $subject, $message, $headers);
			if($result){
				//echo "$result ส่งอีเมล์สำเร็จ";
			}else{
				//echo "$result ส่งอีเมล์ไม่สำเร็จ";
			}
			if($result_type && $result==1)	$result = $result_type;
			
			//echo "<br>=> $result <=  $ABS_RCV_MAIL, $subject, $message.$footers, $headers<br>";
						
	return $result;
	} // end function
	
	//#### แสดงข้อมูลวันลาสะสม รอบ และปีนั้น (ต้องอนุญาตแล้ว)####//
	function get_ABSENT_SUM($PER_ID,$ABS_STARTDATE){
		global $db_dpis, $UPDATE_DATE;
		global $AS_YEAR,
		$AS_CYCLE,
		$START_DATE_1,
		$END_DATE_1,
		$START_DATE_2,
		$END_DATE_2,
		$AB_CODE_01,
		$AB_COUNT_01,
		$ABS_DATE_01,
		$AB_CODE_03,
		$AB_COUNT_03,
		$ABS_DATE_03,
		$AB_CODE_02,
		$ABS_DATE_02,
		$AB_CODE_04,
		$ABS_DATE_04,
		$AB_COUNT_TOTAL_04,
		$AB_COUNT_04;

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
			if(date("Y-m-d") <= date("Y")."-10-01") $AS_YEAR = date("Y") + 543;
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
			if ($code[$i]=="04") $CHECK_STARTDATE =  save_date($START_DATE_1);
			else $CHECK_STARTDATE =  $START_DATE;

			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$CHECK_STARTDATE' and ABS_ENDDATE < '$CHECK_ENDDATE' ";
			$db_dpis->send_cmd($cmd);
			//echo "-------------------------- $cmd<br>";
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$$code_0[$i] += $data[abs_day]; 		
	
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
//				echo "-------------------------- $cmd<br>";
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

//					echo "$cmd --> $code[$i] =>  ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era."<br>";
				${"ABS_DATE_".$code[$i]} = $ABS_ENDDATE_ce_era;		// วันที่ลาล่าสุด
//					echo ${"ABS_DATE_".$code[$i]}."<br>";
			}
		} // end for
		
		if($AS_CYCLE==2){			// รอบ 2
			$TMP_START_DATE =  save_date($START_DATE_1);
			$TMP_END_DATE =  save_date($END_DATE_1);
			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TMP_AB_CODE_04 = $data[abs_day]+0; 
	
			$cmd = " select AB_CODE_04 from PER_ABSENTSUM 
							where PER_ID=$PER_ID and START_DATE = '$TMP_START_DATE' and END_DATE = '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_AB_CODE_04 += $data[AB_CODE_04]+0; 
		}
		// การลาพักผ่อน
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$AS_YEAR'and PER_ID=$PER_ID ";
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_COUNT_TOTAL_04 = $data[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
		$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $AB_CODE_04;		// วันลาสะสมที่เหลือ
	} // end function
	
	if($SESS_PER_ID){ 	// ผู้ล็อกอินเข้ามา
		$PER_ID = $SESS_PER_ID;

		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, a.PER_EMAIL
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO , a.PER_EMAIL
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, a.PER_EMAIL
							 from 		PER_PERSONAL a
											inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
											left join PER_POSITION c on a.POS_ID = c.POS_ID
											left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
							where		a.PER_ID = $PER_ID ";
		} // end if	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();

		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];
		
		$PER_EMAIL = trim($data[PER_EMAIL]);

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
	} // end if

	if($PER_ID){
		get_ABSENT_SUM($PER_ID,"");		// ดึงวันลาสะสมของผู้ที่ล็อกอินมาลา
		
		if($ORG_ID){
			$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_ID = $data[ORG_ID_REF];
		}
		
		if($DEPARTMENT_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		}

		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
	} // end if

	//echo "-> $command <br>";
	if($command == "ADD" || $command == "UPDATE"){		//ไม่ได้ 	ADD ใน PER_ABSENTHIS
		if (!$AUDIT_PER_ID) $AUDIT_PER_ID = "NULL";
		if (!$REVIEW1_PER_ID) $REVIEW1_PER_ID = "NULL";
		if (!$REVIEW2_PER_ID) $REVIEW2_PER_ID = "NULL";
		if(trim($AB_CODE) == "10"){
			$ABS_STARTPERIOD = 3;
			$ABS_ENDDATE = $ABS_STARTDATE;
			$ABS_ENDPERIOD = $ABS_STARTPERIOD;
			$ABS_LETTER = 0;
		} // end if
		if(!$ABS_LETTER)	$ABS_LETTER = 0;		//default
		if($AB_CODE){
			$cmd = "select 	 AB_NAME	from	PER_ABSENTTYPE	where AB_CODE= $AB_CODE"; 	
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$AB_NAME = trim($data[AB_NAME]);
		}

		if(!$ABS_AUDIT_FLAG && $ABS_AUDIT_FLAG_HIDDEN)				$ABS_AUDIT_FLAG = $ABS_AUDIT_FLAG_HIDDEN;
		if(!$ABS_REVIEW1_FLAG && $ABS_REVIEW1_FLAG_HIDDEN)		$ABS_REVIEW1_FLAG = $ABS_REVIEW1_FLAG_HIDDEN;
		if(!$ABS_REVIEW2_FLAG && $ABS_REVIEW2_FLAG_HIDDEN)		$ABS_REVIEW2_FLAG = $ABS_REVIEW2_FLAG_HIDDEN;
		if(!$ABS_APPROVE_FLAG && $ABS_APPROVE_FLAG_HIDDEN)	$ABS_APPROVE_FLAG = $ABS_APPROVE_FLAG_HIDDEN;
		if(!$ABS_CANCEL_FLAG && $ABS_CANCEL_FLAG_HIDDEN)		$ABS_CANCEL_FLAG = $ABS_CANCEL_FLAG_HIDDEN;
		if(!$ABS_STARTPERIOD && $ABS_STARTPERIOD_HIDDEN) 			$ABS_STARTPERIOD = $ABS_STARTPERIOD_HIDDEN;
		if(!$ABS_ENDPERIOD && $ABS_ENDPERIOD_HIDDEN)					$ABS_ENDPERIOD	= $ABS_ENDPERIOD_HIDDEN;

		if (!$ABS_AUDIT_FLAG)			$ABS_AUDIT_FLAG=0;
		if (!$ABS_REVIEW1_FLAG)		$ABS_REVIEW1_FLAG=0;
		if (!$ABS_REVIEW2_FLAG)		$ABS_REVIEW2_FLAG=0;
		if (!$ABS_APPROVE_FLAG)	$ABS_APPROVE_FLAG=0;
		if (!$ABS_CANCEL_FLAG)		$ABS_CANCEL_FLAG=0;
		if(!$ABS_STARTPERIOD) 		$ABS_STARTPERIOD = 3;
		if(!$ABS_ENDPERIOD)			$ABS_ENDPERIOD	= 3;
		
		// (HIDDEN (จาก DB)เป็น ""/0/NULL (ยังไม่ลงความเห็น) แต่ พอ FLAG ตัวใหม่มา!0 คือเป็นการลงความเห็นใหม่วันนี้)
		if($ABS_AUDIT_FLAG!=0  && ($ABS_AUDIT_FLAG_HIDDEN==0 || $ABS_AUDIT_FLAG_HIDDEN=="" || $ABS_AUDIT_FLAG_HIDDEN=="NULL" || $ABS_AUDIT_FLAG_HIDDEN=="null"))	{
			$AUDIT_DATE = $TODATE;
		}
		if($ABS_REVIEW1_FLAG!=0 && ($ABS_REVIEW1_FLAG_HIDDEN==0 || $ABS_REVIEW1_FLAG_HIDDEN=="" || $ABS_REVIEW1_FLAG_HIDDEN=="NULL" || $ABS_REVIEW1_FLAG_HIDDEN=="null"))	 {
			$REVIEW1_DATE = $TODATE;
		}
		if($ABS_REVIEW2_FLAG!=0 && ($ABS_REVIEW2_FLAG_HIDDEN==0 || $ABS_REVIEW2_FLAG_HIDDEN=="" || $ABS_REVIEW2_FLAG_HIDDEN=="NULL" || $ABS_REVIEW2_FLAG_HIDDEN=="null"))	 {
			$REVIEW2_DATE = $TODATE;
		}
		if($ABS_APPROVE_FLAG!=0 && ($ABS_APPROVE_FLAG_HIDDEN==0 || $ABS_APPROVE_FLAG_HIDDEN=="" || $ABS_APPROVE_FLAG_HIDDEN=="NULL" || $ABS_APPROVE_FLAG_HIDDEN=="null"))	{
			$APPROVE_DATE = $TODATE;
		}
		
		if (!$AUDIT_DATE) 		$AUDIT_DATE = "NULL";
		if (!$REVIEW1_DATE) 	$REVIEW1_DATE = "NULL";
		if (!$REVIEW2_DATE) 	$REVIEW2_DATE = "NULL";
		if (!$APPROVE_DATE) 	$APPROVE_DATE = "NULL";
	} // end if

	if( $command == "ADD" && trim(!$ABS_ID)){
            
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
            
            if($DisableTimeAtt=="OPEN" || $SESS_USERGROUP==1){
                $DisableTimeAtt='OPEN';
                $cmd = " select max(ABS_ID) as max_id from PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ABS_ID = $data[max_id] + 1;

		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

		$cmd = " insert into PER_ABSENT (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
		ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, APPROVE_PER_ID, AUDIT_PER_ID, 
		REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG, REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG, UPDATE_USER, UPDATE_DATE,SENDMAIL_FLAG,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE) 
		VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
		'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_LETTER', '$ABS_REASON', '$ABS_ADDRESS', $APPROVE_PER_ID, 
		$AUDIT_PER_ID, $REVIEW1_PER_ID, $REVIEW2_PER_ID, $ABS_AUDIT_FLAG, $ABS_REVIEW1_FLAG, $ABS_REVIEW2_FLAG, $ABS_APPROVE_FLAG, $ABS_CANCEL_FLAG, $SESS_USERID,'$UPDATE_DATE',0,'$CREATE_DATE', '$AUDIT_DATE','$REVIEW1_DATE','$REVIEW2_DATE','$APPROVE_DATE') ";
		$db_dpis->send_cmd($cmd);
		// ส่งอีเมล์อัตโนมัติถึง ผู้อนุญาต
	
		if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
			$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);
			
			$result=0;
			$result = absent_auto_sendmail($PER_EMAIL,$PER_NAME,$PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,0);
			
			// อัพเดทการส่งอีเมล์
			$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
			$db_dpis->send_cmd($cmd);
			
		} //end if
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการลา [".trim($ABS_ID)." : ".$PER_ID." : ".$AB_CODE."]");
		
		$RPT_AB_CODE = $AB_CODE;
		$RPT_ABS_ID = $ABS_ID;
            }else{echo "<script>alert('ระบบไม่อนุญาตให้ทำรายการย้อนหลัง\n เนื่องจากได้ปิดรอบข้อมูลไปแล้ว\n กรุณาติดต่อ Admin');</script>";}
            
            
            /*เดิม*/
		/*$cmd = " select max(ABS_ID) as max_id from PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ABS_ID = $data[max_id] + 1;

		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

		$cmd = " insert into PER_ABSENT (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
		ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, APPROVE_PER_ID, AUDIT_PER_ID, 
		REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG, REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG, UPDATE_USER, UPDATE_DATE,SENDMAIL_FLAG,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE) 
		VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
		'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_LETTER', '$ABS_REASON', '$ABS_ADDRESS', $APPROVE_PER_ID, 
		$AUDIT_PER_ID, $REVIEW1_PER_ID, $REVIEW2_PER_ID, $ABS_AUDIT_FLAG, $ABS_REVIEW1_FLAG, $ABS_REVIEW2_FLAG, $ABS_APPROVE_FLAG, $ABS_CANCEL_FLAG, $SESS_USERID,'$UPDATE_DATE',0,'$CREATE_DATE', '$AUDIT_DATE','$REVIEW1_DATE','$REVIEW2_DATE','$APPROVE_DATE') ";
		$db_dpis->send_cmd($cmd);
		// ส่งอีเมล์อัตโนมัติถึง ผู้อนุญาต
	
		if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
			$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);
			
			$result=0;
			$result = absent_auto_sendmail($PER_EMAIL,$PER_NAME,$PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,0);
			
			// อัพเดทการส่งอีเมล์
			$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
			$db_dpis->send_cmd($cmd);
			
		} //end if
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการลา [".trim($ABS_ID)." : ".$PER_ID." : ".$AB_CODE."]");
		
		$RPT_AB_CODE = $AB_CODE;
		$RPT_ABS_ID = $ABS_ID;*/
	}

	if( $command == "UPDATE" && trim($ABS_ID) ) {
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

                /*คัดลอกค่าไปเก็บไว้ก่อน นะจ๊ะ...*/
                $cmd = " UPDATE PER_ABSENT 
                    SET OABS_STARTDATE=ABS_STARTDATE,
                        OABS_STARTPERIOD=ABS_STARTPERIOD,
                        OABS_ENDDATE=ABS_ENDDATE,
                        OABS_ENDPERIOD=ABS_ENDPERIOD,
                        OAPPROVE_PER_ID=APPROVE_PER_ID,
                        OAPPROVE_DATE=APPROVE_DATE,
                        OAUDIT_PER_ID=AUDIT_PER_ID,
                        OAUDIT_DATE=AUDIT_DATE,
                        OREVIEW1_PER_ID=REVIEW1_PER_ID,
                        OREVIEW1_DATE=REVIEW1_DATE,
                        OREVIEW2_PER_ID=REVIEW2_PER_ID,
                        OREVIEW2_DATE=REVIEW2_DATE
                    WHERE ABS_ID =$ABS_ID ";
            $db_dpis->send_cmd($cmd); 
                /**/
                
                
                
                
                
                
		$cmd = "	update PER_ABSENT set  
							AB_CODE='$AB_CODE', 
							ABS_STARTDATE='$ABS_STARTDATE', 
							ABS_STARTPERIOD='$ABS_STARTPERIOD', 
							ABS_ENDDATE='$ABS_ENDDATE', 
							ABS_ENDPERIOD='$ABS_ENDPERIOD', 
							ABS_DAY='$ABS_DAY', 
							ABS_LETTER='$ABS_LETTER', 
							ABS_REASON='$ABS_REASON', 
							ABS_ADDRESS='$ABS_ADDRESS', 
							APPROVE_PER_ID=$APPROVE_PER_ID,
							AUDIT_PER_ID=$AUDIT_PER_ID,
							REVIEW1_PER_ID=$REVIEW1_PER_ID,
							REVIEW2_PER_ID=$REVIEW2_PER_ID,
							AUDIT_FLAG=$ABS_AUDIT_FLAG, 
							REVIEW1_FLAG = $ABS_REVIEW1_FLAG, 
							REVIEW2_FLAG = $ABS_REVIEW2_FLAG,
							APPROVE_FLAG=$ABS_APPROVE_FLAG, 
							CANCEL_FLAG=$ABS_CANCEL_FLAG,
							AUDIT_DATE='$AUDIT_DATE',
							REVIEW1_DATE='$REVIEW1_DATE',
							REVIEW2_DATE='$REVIEW2_DATE',
							APPROVE_DATE='$APPROVE_DATE',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						where ABS_ID=$ABS_ID  ";
		//die('<pre>'.$cmd);
                $db_dpis->send_cmd($cmd);
                
                
                
                

//$db_dpis->show_error();
//echo "1 : $cmd<br>";
		// ดึงขัอมูลมาเพื่อหาเงื่อนไข
		if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1){
			$cmd = " 	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, SENDMAIL_FLAG
					from			PER_ABSENT
					where 	ABS_ID=$ABS_ID  "; 
			$db_dpis->send_cmd($cmd);
                        //echo "2:".$cmd."<br><br>";
			$data = $db_dpis->get_array();
			$ABS_PER_ID = trim($data[PER_ID]);
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
			$ABS_DAY = trim($data[ABS_DAY]);
			$ABS_REASON = trim($data[ABS_REASON]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
			$ABS_SENDMAIL_FLAG = trim($data[SENDMAIL_FLAG]);	
		}
		if ($ABS_APPROVE_FLAG==1) {		//	อนุญาต insert PER_ABSENTHIS PER_ABSENTSUM		
		    /*Release 5.1.0.6 Begin*/
                    $cmd = "SELECT ABS_ID 
                            FROM PER_ABSENTHIS 
                            WHERE PER_ID=$ABS_PER_ID 
                                AND ABS_STARTDATE='".save_date($OLD_ABS_STARTDATE)."' 
                                AND ABS_STARTPERIOD='$OLD_ABS_STARTPERIOD' 
                                AND ABS_ENDDATE='".save_date($OLD_ABS_ENDDATE)."' 
                                AND ABS_ENDPERIOD='$OLD_ABS_ENDPERIOD' "; /*HID_ABS_ENDPERIOD*/
                    //echo "0:".$cmd."<br><br>";
                    //die('++++++');
                    /*้เดิม*/
                    /*$cmd = " select ABS_ID from PER_ABSENTHIS 
                                where PER_ID=$ABS_PER_ID 
                     * and ABS_STARTDATE='$ABS_STARTDATE' 
                     * and ABS_STARTPERIOD='$ABS_STARTPERIOD' 
                     * and ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";*/
                    $count_data = $db_dpis2->send_cmd($cmd);
                    $data = $db_dpis2->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    if(!empty($count_data)){ //UPDATE
                        $cmd = "UPDATE PER_ABSENTHIS 
                                    SET AB_CODE = '$AB_CODE' , 
                                        ABS_STARTDATE = '$ABS_STARTDATE', 
                                        ABS_STARTPERIOD = '$ABS_STARTPERIOD', 
                                        ABS_ENDDATE = '$ABS_ENDDATE', 
                                        ABS_ENDPERIOD = '$ABS_ENDPERIOD', 
                                        ABS_DAY = '$ABS_DAY', 
                                        ABS_REMARK = '$ABS_REASON', 
                                        PER_CARDNO = '$PER_CARDNO', 
                                        UPDATE_USER = $SESS_USERID, 
                                        UPDATE_DATE = '$UPDATE_DATE' 
                                WHERE ABS_ID =".$data[abs_id]." AND PER_ID=$ABS_PER_ID ";
                        $db_dpis2->send_cmd($cmd);
                        
                        $cmd="SELECT AS_ID 
                              FROM PER_ABSENTSUM 
                              WHERE PER_ID=$ABS_PER_ID 
                                  AND AS_YEAR = '$BDH_YEAR' 
                                  AND AS_CYCLE = $AS_CYCLE ";
                        $count=$db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        $data = array_change_key_case($data, CASE_LOWER);
                        if(!empty($count)){ //UPDATE
                            $cmd = "UPDATE PER_ABSENTSUM 
                                    SET START_DATE = '$START_DATE',
                                        END_DATE = '$END_DATE', 
                                        PER_CARDNO = '$PER_CARDNO' , 
                                        UPDATE_USER = $SESS_USERID, 
                                        UPDATE_DATE = '$UPDATE_DATE'
                                    WHERE AS_ID = ".$data[as_id];
                                $db_dpis->send_cmd($cmd);
                        }
                        //echo "UPDATE";
                    }else{ //INSERT
                        // echo "INSERT";
                        $cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        $data = array_change_key_case($data, CASE_LOWER);
                        $ABS_ID_MAX = $data[max_id] + 1; 

                        $cmd = "insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
                                ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
                                VALUES ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
                                '$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
                        $db_dpis2->send_cmd($cmd);
                        //echo $cmd."<br>";
                        $AS_CYCLE1 = $AS_CYCLE2 = "";
                        if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
                        elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
                        if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
                        elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
                        if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
                            $AS_CYCLE = 1;
                            $TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
                            if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
                            $START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
                            $END_DATE = $TMP_AS_YEAR . "-03-31";
                            $BDH_YEAR = $TMP_AS_YEAR + 543;

                            $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
                            $count=$db_dpis->send_cmd($cmd);
                            if(!$count) { 
                                $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
                                $db_dpis->send_cmd($cmd);
                                $data = $db_dpis->get_array();
                                $data = array_change_key_case($data, CASE_LOWER);
                                $AS_ID = $data[max_id] + 1;

                                $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                                values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
                                $db_dpis->send_cmd($cmd);
                            }
                        }
                        if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
                                $AS_CYCLE = 2;
                                $TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
                                $START_DATE = $TMP_AS_YEAR . "-04-01";
                                $END_DATE = $TMP_AS_YEAR . "-09-30"; 
                                $BDH_YEAR = $TMP_AS_YEAR + 543;

                                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
                                $count=$db_dpis->send_cmd($cmd);
                                if(!$count) { 
                                    $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
                                    $db_dpis->send_cmd($cmd);
                                    $data = $db_dpis->get_array();
                                    $data = array_change_key_case($data, CASE_LOWER);
                                    $AS_ID = $data[max_id] + 1;

                                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                                    values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
                                    $db_dpis->send_cmd($cmd);
                                }
                        }
                    }
                    /*Release 5.1.0.6 End*/
                    
                    
                    
                    
                    /*เดิม Begin*/
                    /*$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID and ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD' and 
							ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {	
				$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$ABS_ID_MAX = $data[max_id] + 1; 
			
				$cmd = "	insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
								values ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
								'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis2->send_cmd($cmd);

				$AS_CYCLE1 = $AS_CYCLE2 = "";
				if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
				elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
				if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
				elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
				if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
					$AS_CYCLE = 1;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
					}
				}
				if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
					$AS_CYCLE = 2;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
					}
				}
			}*/
			/*เดิม End*/
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > อนุญาตให้เพิ่มประวัติการลาและวันลาสะสม [".$APPROVE_FLAG." : ".trim($ABS_ID)." : ".$ABS_PER_ID." - ".$PER_NAME." BY ".$APPROVE_PER_ID." - ".$APPROVE_PER_NAME."]");
			
		}
		if ($ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1) {		//	ไม่อนุญาต/ผู้ลายกเลิก delete PER_ABSENTHIS
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";		//and AB_CODE='$AB_CODE'
			$count_abshis_data = $db_dpis2->send_cmd($cmd);
//			echo "=> $count_abshis_data : $cmd <br>";
			$data = $db_dpis2->get_array();
			$ABSHIS_ID = $data[ABS_ID];
			if ($ABSHIS_ID) {	
				// delete PER_ABSENTHIS     
				$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABSHIS_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
			
				/***
				$cmd="select AS_ID from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE ";
				$count_abssum_data=$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ABSAS_ID = $data[AS_ID];
				if($ABSAS_ID) { 
					// delete PER_ABSENTSUM
					$cmd = " delete from PER_ABSENTSUM where AS_ID=$ABSAS_ID ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					//echo "<br><hr>delete PER_ABSENTSUM : $cmd<br>";
				}
				****/
			}
			
			// ส่งอีเมล์อัตโนมัติถึง ผู้ลา (เฉพาะผู้อนุญาต)
			if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2){
				if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
					if($ABS_PER_ID){
						$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
										from PER_PERSONAL a, PER_PRENAME b 
										where a.PER_ID=$ABS_PER_ID and a.PN_CODE=b.PN_CODE ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$ABS_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
						$ABS_PER_EMAIL = $data[PER_EMAIL];
					}
			
					$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
									from PER_PERSONAL a, PER_PRENAME b 
									where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
					$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);

					if($ABS_SENDMAIL_FLAG==0 || $ABS_SENDMAIL_FLAG==1){		// ดึง sendmail_flag จาก DB check ผู้อนุญาตส่งอีเมล์ไปหรือยัง (ถ้า 0 = ผู้ลา/ผู้อนุญาตยังไม่ส่ง/ส่งเมล์ไม่สำเร็จ / 1  = ผู้ลาส่งเมล์สำเร็จแล้ว / 2 =  ผู้อนุญาตส่งเมล์สำเร็จแล้ว)
						$result=0;
						$result = absent_auto_sendmail($PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$ABS_PER_EMAIL,$ABS_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,$ABS_APPROVE_FLAG);
						
						// อัพเดทการส่งอีเมล์
						$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
						$db_dpis->send_cmd($cmd);
					} //end if
				} //end if
			} //end if

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ไม่อนุญาต/ยกเลิกการลาให้ลบประวัติการลา [".$APPROVE_FLAG." : ".trim($ABS_ID)." : ".$ABS_PER_ID." - ".$PER_NAME." BY ".$APPROVE_PER_ID." - ".$APPROVE_PER_NAME."]");
			
		}

                
                
                
                /*กำหนดค่าให้ว่าทำการยกเลิกบางช่วง นะจ๊ะ....*/
                $cmd = " UPDATE PER_ABSENT 
                    SET CANCEL_FLAG = 8 ,
                    REVIEW1_FLAG = NULL ,REVIEW1_DATE = NULL ,
                    REVIEW2_FLAG = NULL  , REVIEW2_DATE = NULL ,
                    AUDIT_FLAG = NULL ,AUDIT_DATE = NULL ,
                    APPROVE_FLAG = NULL ,APPROVE_DATE = NULL             
                    WHERE ABS_ID =$ABS_ID";
            
            $db_dpis->send_cmd($cmd);
                /**/
                
                
                
                
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");	
               echo "<script type='text/javascript'>parent.refresh_opener('1<::>1');</script>";
                $refreshOpener='1';
	}
	
	if($command == "DELETE" && trim($ABS_ID) ){
		// หาข้อมูลเพื่อนำไปใส่เงื่อนไขในการลบ PER_ABSENTHIS
		$cmd = " 	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, APPROVE_FLAG
				from			PER_ABSENT
				where 	ABS_ID=$ABS_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABS_PER_ID = trim($data[PER_ID]);
		$PER_CARDNO = trim($data[PER_CARDNO]);	
		$AB_CODE = trim($data[AB_CODE]);
		$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);
		$ABS_APPROVE_FLAG =trim($data[APPROVE_FLAG]);

		$cmd = " delete from PER_ABSENT where ABS_ID=$ABS_ID ";
		$count_delete = $db_dpis->send_cmd($cmd);
	
		$cmd = " 	select 	PER_ID
		from			PER_ABSENT
		where 	ABS_ID=$ABS_ID  "; 
		$count_delete = $db_dpis->send_cmd($cmd);

		if(!$count_delete){
			// delete PER_ABSENTHIS     
			$cmd = " delete from PER_ABSENTHIS where PER_ID=$ABS_PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD' and 
							 ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";
			$db_dpis2->send_cmd($cmd);

//			$db_dpis2->show_error();
//			echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
			
			/***
			// delete PER_ABSENTSUM
			if ($ABS_APPROVE_FLAG==1) {
				$cmd = " delete from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE and START_DATE='$START_DATE' and 
								 END_DATE='$END_DATE' and PER_CARDNO='$PER_CARDNO' ";
				$db_dpis->send_cmd($cmd);
			}
			***/
		}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");		
	}
	if ($command == "CANCELALL") {
            if(!empty($ABS_APPROVE_FLAG_HIDDEN)){
                $cmd = " UPDATE PER_ABSENT 
                    SET OABS_STARTDATE=ABS_STARTDATE,
                        OABS_STARTPERIOD=ABS_STARTPERIOD,
                        OABS_ENDDATE=ABS_ENDDATE,
                        OABS_ENDPERIOD=ABS_ENDPERIOD,
						OABS_DAY=ABS_DAY,
                        OAPPROVE_PER_ID=APPROVE_PER_ID,
                        OAPPROVE_DATE=APPROVE_DATE,
                        OAUDIT_PER_ID=AUDIT_PER_ID,
                        OAUDIT_DATE=AUDIT_DATE,
                        OREVIEW1_PER_ID=REVIEW1_PER_ID,
                        OREVIEW1_DATE=REVIEW1_DATE,
                        OREVIEW2_PER_ID=REVIEW2_PER_ID,
                        OREVIEW2_DATE=REVIEW2_DATE
                        WHERE ABS_ID IN (".$ABS_ID.") ";
                $db_dpis->send_cmd($cmd);
                $db_dpis->send_cmd("COMMIT");  
            }
            
            
            
             $cmd = " UPDATE PER_ABSENT 
                    SET CANCEL_FLAG = 9 ,
                    REVIEW1_FLAG = NULL ,REVIEW1_DATE = NULL ,
                    REVIEW2_FLAG = NULL  , REVIEW2_DATE = NULL ,
                    AUDIT_FLAG = NULL ,AUDIT_DATE = NULL ,
                    APPROVE_FLAG = NULL ,APPROVE_DATE = NULL,
					CANCEL_DATE='$CANCEL_DATETIME',
				    CANCEL_BY=$SESS_USERID,
					UPDATE_DATE = '$UPDATE_DATE'   
                    WHERE ABS_ID IN (".$ABS_ID.") ";
            $db_dpis->send_cmd($cmd);
            $db_dpis->send_cmd("COMMIT");  
           
            echo "<script type='text/javascript'>parent.refresh_opener('1<::>1');</script>";
        }
		
		
        if($command =="CANCELSOMONE"){
            
			$ABS_STARTDATE =  save_date($ABS_STARTDATE);
			$ABS_ENDDATE =  save_date($ABS_ENDDATE);
                
                /*คัดลอกค่าไปเก็บไว้ก่อน นะจ๊ะ...*/
                if(!empty($ABS_APPROVE_FLAG_HIDDEN)){
                    $cmd = " UPDATE PER_ABSENT 
                    SET OABS_STARTDATE=ABS_STARTDATE,
                        OABS_STARTPERIOD=ABS_STARTPERIOD,
                        OABS_ENDDATE=ABS_ENDDATE,
                        OABS_ENDPERIOD=ABS_ENDPERIOD,
                        OAPPROVE_PER_ID=APPROVE_PER_ID,
                        OAPPROVE_DATE=APPROVE_DATE,
                        OAUDIT_PER_ID=AUDIT_PER_ID,
                        OAUDIT_DATE=AUDIT_DATE,
                        OREVIEW1_PER_ID=REVIEW1_PER_ID,
                        OREVIEW1_DATE=REVIEW1_DATE,
                        OREVIEW2_PER_ID=REVIEW2_PER_ID,
                        OREVIEW2_DATE=REVIEW2_DATE,
                        OABS_DAY=ABS_DAY,
						CANCEL_DATE='$CANCEL_DATETIME',
						CANCEL_BY=$SESS_USERID 
                    WHERE ABS_ID =$ABS_ID ";
                    $db_dpis->send_cmd($cmd); 
                }
            
                /* เพิ่มใหม่ 14/11/2017 http://dpis.ocsc.go.th/Service/node/1644*/
                
                 /*$cmd = " UPDATE PER_ABSENT 
                    SET ORI_ABS_DAY = ABS_DAY        
                    WHERE ABS_ID =$ABS_ID";
                //echo $cmd."<br><br>";
                $db_dpis->send_cmd($cmd);*/
                
                /*END เพิ่มใหม่ 14/11/2017 */
                
                /**/
                if(empty($AUDIT_PER_ID)){$AUDIT_PER_ID='NULL';}
                if(empty($REVIEW1_PER_ID)){$REVIEW1_PER_ID='NULL';}
                if(empty($REVIEW2_PER_ID)){$REVIEW2_PER_ID='NULL';}
                if(empty($ABS_AUDIT_FLAG)){$ABS_AUDIT_FLAG='NULL';}
                if(empty($ABS_REVIEW1_FLAG)){$ABS_REVIEW1_FLAG='NULL';}
                if(empty($ABS_REVIEW2_FLAG)){$ABS_REVIEW2_FLAG='NULL';}
                if(empty($ABS_APPROVE_FLAG)){$ABS_APPROVE_FLAG='NULL';}
                if(empty($ABS_CANCEL_FLAG)){$ABS_CANCEL_FLAG='NULL';}
                
		$cmd = " update PER_ABSENT set  
                                AB_CODE='$AB_CODE', 
                                ABS_STARTDATE='$ABS_STARTDATE', 
                                ABS_STARTPERIOD='$HID_ABS_STARTPERIOD',
                                ABS_ENDDATE='$ABS_ENDDATE', 
                                ABS_ENDPERIOD='$ABS_ENDPERIOD_HIDDEN', 
                                ABS_DAY='$ABS_DAY', 
                                ABS_LETTER='$ABS_LETTER', 
                                ABS_REASON='$ABS_REASON', 
                                ABS_ADDRESS='$ABS_ADDRESS', 
                                APPROVE_PER_ID=$APPROVE_PER_ID,
                                    
                                AUDIT_PER_ID=$AUDIT_PER_ID,
                                REVIEW1_PER_ID=$REVIEW1_PER_ID,
                                REVIEW2_PER_ID=$REVIEW2_PER_ID,
                                AUDIT_FLAG=$ABS_AUDIT_FLAG, 
                                REVIEW1_FLAG = $ABS_REVIEW1_FLAG, 
                                REVIEW2_FLAG = $ABS_REVIEW2_FLAG,
                                APPROVE_FLAG=$ABS_APPROVE_FLAG, 
                                CANCEL_FLAG=$ABS_CANCEL_FLAG,
                                    
                                AUDIT_DATE='$AUDIT_DATE',
                                REVIEW1_DATE='$REVIEW1_DATE',
                                REVIEW2_DATE='$REVIEW2_DATE',
                                APPROVE_DATE='$APPROVE_DATE',
                                UPDATE_USER=$SESS_USERID, 
                                UPDATE_DATE='$UPDATE_DATE' 
                        where ABS_ID=$ABS_ID  ";
		//echo $cmd."<br><br>";
                $db_dpis->send_cmd($cmd);
                
                
                
                

//$db_dpis->show_error();
//echo "1 : $cmd<br>";
		// ดึงขัอมูลมาเพื่อหาเงื่อนไข
		//if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1){}
		//if ($ABS_APPROVE_FLAG==1) {}
		//if ($ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1) {}

                /*กำหนดค่าให้ว่าทำการยกเลิกบางช่วง นะจ๊ะ....*/
                
                $cmd = " UPDATE PER_ABSENT 
                    SET CANCEL_FLAG = 8 ,
                    REVIEW1_FLAG = NULL ,REVIEW1_DATE = NULL ,
                    REVIEW2_FLAG = NULL  , REVIEW2_DATE = NULL ,
                    AUDIT_FLAG = NULL ,AUDIT_DATE = NULL ,
                    APPROVE_FLAG = NULL ,APPROVE_DATE = NULL             
                    WHERE ABS_ID =$ABS_ID";
            //echo $cmd."<br><br>";
                $db_dpis->send_cmd($cmd);
                /**/
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ขอยกเลิกการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");	
              echo "<script type='text/javascript'>parent.refresh_opener('1<::>1');</script>";

        }
	if ($command == "SETFLAG_CANCEL") {		// ของตัวเอง
		$setflagshow =  implode(",",$list_cancel_id);
		$setflagshow_tmp =  implode(",",$list_cancel_flag);		
//		$cmd = " update PER_ABSENT set CANCEL_FLAG = 0 where ABS_ID in (".stripslashes($current_list).") ";
//		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_ABSENT set CANCEL_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($setflagshow_tmp){			// คงตัวเดิมที่เคยยกเลิกไปแล้ว
			$cmd = " update PER_ABSENT set CANCEL_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow_tmp).") ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
		}

		// delete PER_ABSENTHIS  ตัวที่ CANCEL
		for($i=0; $i < count($list_cancel_id); $i++){
			$ABS_ID_CANCEL = $list_cancel_id[$i];
			//echo "-> $ABS_ID_CANCEL <br>";
			$cmd = " 	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO
					from			PER_ABSENT
					where 	 ABS_ID = $ABS_ID_CANCEL "; 
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ABS_PER_ID = trim($data[PER_ID]);
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
			$ABS_DAY = trim($data[ABS_DAY]);
			$ABS_REASON = trim($data[ABS_REASON]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
			
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD')
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
			$count_abshis_data = $db_dpis2->send_cmd($cmd);
			if($count_abshis_data){
				//echo "=> $count_abshis_data : $cmd <br>";
				$data = $db_dpis2->get_array();
				$ABSHIS_ID = $data[ABS_ID];
				if ($ABSHIS_ID) {	
					// delete PER_ABSENTHIS     
					$cmd = " delete from PER_ABSENTHIS where ABS_ID = $ABSHIS_ID";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
				}
			}
		} //end for
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลยกเลิกการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_AUDIT") {
		$setflagshow =  implode(",",$list_audit_id);
		$cmd = " update PER_ABSENT set AUDIT_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลตรวจสอบการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_REVIEW1") {
		$setflagshow =  implode(",",$list_review1_id);
		$cmd = " update PER_ABSENT set REVIEW1_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลความเห็นผู้บังคับบัญชาชั้นต้นการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_REVIEW2") {
		$setflagshow =  implode(",",$list_review2_id);
		$cmd = " update PER_ABSENT set REVIEW2_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลความเห็นผู้บังคับบัญชาชั้นต้นเหนือขึ้นไปการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_APPROVE") {
//		print("<pre>1 เคยอนุญาตไปแล้ว (เฉพาะ value=1) : ");	print_r($list_approve_allabs);	print("</pre><hr>");
//		print("<br><pre>2.1 มาอนุญาตใหม่ (ยังไม่รวมตัวเก่า): ");	print_r($list_approve_id);	print("</pre>");
		
		//เพิ่มตัวที่ ผู้อนุญาตการลาให้ความเห็นไปแล้ว (แสดงรูปภาพ) ยังคงความเห็นของผู้อนุญาตไว้อยู่เหมือนเดิม
		if(is_array($list_approve_allabs)){	
			foreach($list_approve_allabs as $key=>$value){ //APPROVEการลาไปแล้ว
				$ABSAP_APPROVE_ID = trim($key);
				$ABSAP_APPROVE_FLAG = trim($value);
				if(!$ABSAP_APPROVE_FLAG)		$ABSAP_APPROVE_FLAG=0;
				if($ABSAP_APPROVE_FLAG==1 || $ABSAP_APPROVE_FLAG==2){
					if($ABSAP_APPROVE_FLAG==1){		// เฉพาะที่อนุญาตไปแล้ว
						$list_approve_id[] = $ABSAP_APPROVE_ID;	// เพิ่ม ABS_ID ตัวเก่าที่เคยอนุญาตไปแล้วเพื่อไม่ให้ไปลบประวัติการลาออก
					}
					$cmd = " update PER_ABSENT set APPROVE_FLAG = ".$ABSAP_APPROVE_FLAG." where ABS_ID =".$ABSAP_APPROVE_ID;
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
			} //end foreach
		} // end is_array
		
//		print("<br><pre>2.2 มาอนุญาตใหม่ (รวมตัวเก่าที่อนุญาตแล้ว): ");	print_r($list_approve_id);	print("</pre>");		
		
		$setflagshow =  implode(",",$list_approve_id);
		$cmd = " update PER_ABSENT set APPROVE_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
/****
		if($list_approve_all==1){		//อนุญาตการลาทั้งหมด
			$cmd = " update PER_ABSENT set APPROVE_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
		}else{		//บางอัน
			$cmd = " update PER_ABSENT set APPROVE_FLAG = 0 where ABS_ID in (".stripslashes($current_list).") ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
	
			$cmd = " update PER_ABSENT set APPROVE_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
		}
***/
		
		// delete PER_ABSENTHIS ( UNCHECKED BOX)
		$list_current_list = explode(",",stripslashes($current_list));
		for($i=0; $i < count($list_current_list); $i++){
				$ABS_ID_LIST = $list_current_list[$i];		// ดึง ABS_ID ทั้งหมดมา
				if(!in_array($ABS_ID_LIST,$list_approve_id)){ // เปรียบเทียบกับที่ผู้อนุญาต อนุญาตการลาจาก checkbox / hidden ตัวเดิมที่เคยอนุญาตการลาไปแล้ว ถ้าไม่มีในอนุญาตการลา (uncheck) ให้ไปลบประวัติการลาออก
					$cmd = "	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
									ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, CANCEL_FLAG
									from			PER_ABSENT
									where 	 ABS_ID = $ABS_ID_LIST"; 
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ABS_PER_ID = trim($data[PER_ID]);
					$AB_CODE = trim($data[AB_CODE]);
					$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
					$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
					$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
					$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
					$ABS_DAY = trim($data[ABS_DAY]);
					$ABS_REASON = trim($data[ABS_REASON]);
					$PER_CARDNO = trim($data[PER_CARDNO]);
					$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
					if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";			
					
					$cmd = " select ABS_ID from PER_ABSENTHIS 
									where PER_ID=$ABS_PER_ID 
									and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
									and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
					$count_abshis_data = $db_dpis2->send_cmd($cmd);
			//		echo "=> $count_abshis_data : $cmd <br>";
					$data = $db_dpis2->get_array();
					$ABSHIS_ID = $data[ABS_ID];
					if ($ABSHIS_ID) {	
						// delete PER_ABSENTHIS     
						$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABSHIS_ID ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						//echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
					}
				} //end in_array
		} // end for($i=0; $i < count($list_current_list); $i++)
// END delete PER_ABSENTHIS ==============
// insert PER_ABSENTHIS ( CHECKED BOX) เฉพาะตัวที่ APPROVE
// ให้คงความเห็นของผู้อนุญาตไว้ แม้จะยกเลิก/ไม่ยกเลิกการลา
foreach($list_cancel_allabs as $key=>$value){ //CANCELการลา
	$ABSCC_CANCEL_ID = trim($key);			
	$ABSCC_CANCEL_FLAG = trim($value);
	if(!$ABSCC_CANCEL_FLAG)		$ABSCC_CANCEL_FLAG=0;
	$cmd = " update PER_ABSENT set CANCEL_FLAG = ".$ABSCC_CANCEL_FLAG." where ABS_ID =".$ABSCC_CANCEL_ID;
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
} //end foreach
for($i=0; $i < count($list_approve_id); $i++){
	$ABS_ID_APPROVE = $list_approve_id[$i];
	$cmd = "	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, APPROVE_PER_ID,CANCEL_FLAG, APPROVE_FLAG, SENDMAIL_FLAG
					from			PER_ABSENT
					where 	 ABS_ID = $ABS_ID_APPROVE"; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABS_PER_ID = trim($data[PER_ID]);
		$AB_CODE = trim($data[AB_CODE]);
		$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
		$ABS_DAY = trim($data[ABS_DAY]);
		$ABS_REASON = trim($data[ABS_REASON]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);
		$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
		$ABS_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
		if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
		$ABS_SENDMAIL_FLAG = trim($data[SENDMAIL_FLAG]);	
		
		// ส่งอีเมล์อัตโนมัติถึง ผู้ลา (เฉพาะผู้อนุญาต)
		if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2){
			if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
				if($ABS_PER_ID){
					$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
									from PER_PERSONAL a, PER_PRENAME b 
									where a.PER_ID=$ABS_PER_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ABS_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
					$ABS_PER_EMAIL = $data[PER_EMAIL];
				}
			
				$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
								from PER_PERSONAL a, PER_PRENAME b 
								where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
				$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);

				if($ABS_SENDMAIL_FLAG==0 || $ABS_SENDMAIL_FLAG==1){		// ดึง sendmail_flag จาก DB check ผู้อนุญาตส่งอีเมล์ไปหรือยัง (ถ้า 0 = ผู้ลา/ผู้อนุญาตยังไม่ส่ง/ส่งเมล์ไม่สำเร็จ / 1  = ผู้ลาส่งเมล์สำเร็จแล้ว / 2 =  ผู้อนุญาตส่งเมล์สำเร็จแล้ว)
					$result=0;
					$result = absent_auto_sendmail($PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$ABS_PER_EMAIL,$ABS_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,$ABS_APPROVE_FLAG);
					
					// อัพเดทการส่งอีเมล์
					$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = ".$ABS_ID_APPROVE;
					$db_dpis->send_cmd($cmd);
				} //end if
			} //end if
		} //end if
		
		if($ABS_CANCEL_FLAG!=1){		// ไม่เพิ่มกรณีที่ผู้ลายกเลิกการลานั้นแล้ว
			$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABS_ID_MAX = $data[max_id] + 1; 
			
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {	
				$cmd = "	insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
								values ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
								'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				//echo "<br><hr>$cmd<br>";
			
				$AS_CYCLE1 = $AS_CYCLE2 = "";
				if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
				elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
				if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
				elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
				if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
					$AS_CYCLE = 1;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error(); echo "<hr>$cmd<br>";
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
				}
				if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
					$AS_CYCLE = 2;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error(); echo "<hr>$cmd<br>";
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
				}
			}
		} // end ABS_CANCEL_FLAG==1
} // end for($i=0; $i < count($list_approve_id); $i++)
// END insert PER_ABSENTHIS PER_ABSENTSUM ==============

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลอนุญาตการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");
	}

	if($UPD || $VIEW){
            
            
            
            
            
		$cmd = " 	select 	a.PER_ID, a.AB_CODE, AB_NAME, AB_COUNT, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, 
							APPROVE_PER_ID, AUDIT_PER_ID, REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG , REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG,
							CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE, APPROVE_DATE,
                                                        a.ORI_ABS_STARTDATE,a.ORI_ABS_STARTPERIOD,a.ORI_ABS_ENDDATE,a.ORI_ABS_ENDPERIOD
				from	PER_ABSENT a, PER_ABSENTTYPE b
				where 	ABS_ID=$ABS_ID and a.AB_CODE=b.AB_CODE  "; 	
		//echo $cmd;
                $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = trim($data[PER_ID]);
		$AB_CODE = trim($data[AB_CODE]);
		$AB_NAME = trim($data[AB_NAME]);
		$AB_COUNT = trim($data[AB_COUNT]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
		$ABS_DAY = trim($data[ABS_DAY]);
		
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
	   $db_dpis2->send_cmd($cmd_chkhid);
	  // echo '<pre>'.$cmd_chkhid;
	   $HID_ABS_DAY = 0;
	   while ($data_chkhid = $db_dpis2->get_array_array()) {
		   $HID_ABS_DAY = round($HID_ABS_DAY,2)+ round($data_chkhid[NDAY],2);
	   }
		
		///////////////////////////////////////////////
		
		
		$ABS_LETTER = trim($data[ABS_LETTER]);
		$ABS_REASON = trim($data[ABS_REASON]);
		$ABS_ADDRESS = trim($data[ABS_ADDRESS]);
		$APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);
		$AUDIT_PER_ID = trim($data[AUDIT_PER_ID]);
		$REVIEW1_PER_ID = trim($data[REVIEW1_PER_ID]);
		$REVIEW2_PER_ID = trim($data[REVIEW2_PER_ID]);
		
		$ABS_AUDIT_FLAG = trim($data[AUDIT_FLAG]);
		$ABS_REVIEW1_FLAG = trim($data[REVIEW1_FLAG]);
		$ABS_REVIEW2_FLAG = trim($data[REVIEW2_FLAG]);
		$ABS_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
		$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);

		$ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], 1);
		$ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], 1);
                
                /**/
                $ORI_ABS_STARTDATE = show_date_format($data[ORI_ABS_STARTDATE], 1);
		$ORI_ABS_ENDDATE = show_date_format($data[ORI_ABS_ENDDATE], 1);
                $ORI_ABS_STARTPERIOD = trim($data[ORI_ABS_STARTPERIOD]);
		$ORI_ABS_ENDPERIOD = trim($data[ORI_ABS_ENDPERIOD]);
                /**/

		$ABS_CREATE_DATE = substr(trim($data[CREATE_DATE]),0,10);
		if($ABS_CREATE_DATE){
			if(substr($data[CREATE_DATE],12,strlen($data[CREATE_DATE]))){
				$ABS_CREATE_TIME  = substr($data[CREATE_DATE],12,strlen($data[CREATE_DATE]));
			}
			$temp_date = explode("-", trim($ABS_CREATE_DATE));
			$ABS_CREATE_DATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
			if($ABS_CREATE_TIME)	$ABS_CREATE_DATE = 	$ABS_CREATE_DATE." (".$ABS_CREATE_TIME.")";
		}
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
		
		// สำหรับ HIDDEN //
		$AUDIT_DATE = ($data[AUDIT_DATE]!=""&&$data[AUDIT_DATE]!="NULL"&&$data[AUDIT_DATE]!="null")?substr(trim($data[AUDIT_DATE]),0,10):"";
		$REVIEW1_DATE = ($data[REVIEW1_DATE]!=""&&$data[REVIEW1_DATE]!="NULL"&&$data[REVIEW1_DATE]!="null")?substr(trim($data[REVIEW1_DATE]),0,10):"";
		$REVIEW2_DATE = ($data[REVIEW2_DATE]!=""&&$data[REVIEW2_DATE]!="NULL"&&$data[REVIEW2_DATE]!="null")?substr(trim($data[REVIEW2_DATE]),0,10):"";
		$APPROVE_DATE = ($data[APPROVE_DATE]!=""&&$data[APPROVE_DATE]!="NULL"&&$data[APPROVE_DATE]!="null")?substr(trim($data[APPROVE_DATE]),0,10):"";
		
		$AUDIT_DISPLAY_DATE = $REVIEW1_DISPLAY_DATE = $REVIEW2_DISPLAY_DATE = $APPROVE_DISPLAY_DATE = "";
		if($AUDIT_DATE){
			if(is_numeric(strrpos($AUDIT_DATE, "/"))){	// ตรวจสอบรูปแบบวันที่ ที่ format ผิดจากการ กรอกข้อมูลแต่แรก
				$AUDIT_DATE =	"";		//$TODATE;	//กำหนด hidden ให้มันไปอัพเดทค่าให้ถูกต้อง
			}else{
				$AUDIT_DISPLAY_DATE = show_date_format($AUDIT_DATE, 1);
			}
		}
		if($REVIEW1_DATE){
			if(is_numeric(strrpos($REVIEW1_DATE, "/"))){
				$REVIEW1_DATE =	"";		 //$TODATE;	
			}else{
				$REVIEW1_DISPLAY_DATE = show_date_format($REVIEW1_DATE, 1);
			}
		}
		if($REVIEW2_DATE){
			if(is_numeric(strrpos($REVIEW2_DATE, "/"))){
				$REVIEW2_DATE =	"";		//$TODATE;
			}else{
				$REVIEW2_DISPLAY_DATE = show_date_format($REVIEW2_DATE, 1);
			}
		}
		if($APPROVE_DATE){
			if(is_numeric(strrpos($APPROVE_DATE, "/"))){
				$APPROVE_DATE =	"";		//$TODATE;	
			}else{
				$APPROVE_DISPLAY_DATE = show_date_format($APPROVE_DATE, 1);
			}
		}
		//echo "$AUDIT_DATE / $REVIEW1_DATE / $REVIEW2_DATE / $APPROVE_DATE ";
		
		$temp_date = explode("-", trim($data[ABS_STARTDATE]));
		$ABS_STARTDATE_CHECK = ($temp_date[0])."-".$temp_date[1]."-".substr($temp_date[2],0,2) ;

		if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
		
		get_ABSENT_SUM($PER_ID,trim($ABS_STARTDATE));	//ดึงวันลาสะสมของคนที่เลือกมาแก้ไขขึ้นมา

		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO 
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PERSONAL a
											inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
											left join PER_POSITION c on a.POS_ID = c.POS_ID
											left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
							where		a.PER_ID = $PER_ID ";
		} // end if	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();

		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];					

		if($ORG_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
		}

		if (!$DEPARTMENT_NAME) {
			$DEPARTMENT_ID = $data2[ORG_ID_REF];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];

			$MINISTRY_ID = $data2[ORG_ID_REF];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];
		}

		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
						from PER_PERSONAL a, PER_PRENAME b 
						where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];

		$AUDIT_PER_NAME = $REVIEW1_PER_NAME = $REVIEW2_PER_NAME = "";
		if ($AUDIT_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$AUDIT_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$AUDIT_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}

		if ($REVIEW1_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$REVIEW1_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW1_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}

		if ($REVIEW2_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$REVIEW2_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW2_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}
		
		$count_check_flag = 0; 		$CAN_EDIT = "N";
		if($REVIEW1_PER_ID && ($ABS_REVIEW1_FLAG==1 || $ABS_REVIEW1_FLAG==2))				$count_check_flag++;
		if($REVIEW2_PER_ID && ($ABS_REVIEW2_FLAG==1 || $ABS_REVIEW2_FLAG==2))					$count_check_flag++;
		if($AUDIT_PER_ID && ($ABS_AUDIT_FLAG==1 || $ABS_AUDIT_FLAG==2))							$count_check_flag++;
		if($APPROVE_PER_ID && ($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2))				$count_check_flag++;
		if($PER_ID && ($ABS_CANCEL_FLAG==1))				$count_check_flag++;
		if($count_check_flag==0)	$CAN_EDIT = "Y";
		//echo "$count_check_flag / $CAN_EDIT";
	} // end if command
	
	if ($command == "CANCEL" && !$ABS_ID && !$SESS_PER_ID) {
		$PER_ID = "";
		$PER_NAME = "";
		$ORG_ID = "";
		$ORG_NAME = "";
		
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if		
	}

	if( !$UPD && !$DEL && !$VIEW ){
		$ABS_ID = "";
		$AB_CODE = "";
		$AB_NAME = "";
		$AB_COUNT = "";
		$ABS_STARTDATE = "";
		$ABS_ENDDATE = "";
		$ABS_DAY = "";
		$ABS_STARTPERIOD = "";
		$ABS_ENDPERIOD = "";
		$ABS_LETTER = "";
		$ABS_REASON = "";
		$ABS_ADDRESS = "";
		$APPROVE_PER_ID = "";
		$APPROVE_PER_NAME = "";
		$AUDIT_PER_ID = "";
		$AUDIT_PER_NAME = "";
		$REVIEW1_PER_ID = "";
		$REVIEW1_PER_NAME = "";
		$REVIEW2_PER_ID = "";
		$REVIEW2_PER_NAME = "";
		$ABS_REVIEW1_FLAG = "";
		$ABS_REVIEW2_FLAG = "";
		$ABS_AUDIT_FLAG = "";
		$ABS_APPROVE_FLAG = "";
		$ABS_CANCEL_FLAG = "";
		$AUDIT_DATE="";
		$REVIEW1_DATE="";
		$REVIEW2_DATE="";
		$APPROVE_DATE="";
	} // end
?>