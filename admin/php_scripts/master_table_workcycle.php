<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update PER_WORK_CYCLE set WC_ACTIVE = 0 where WC_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_WORK_CYCLE set WC_ACTIVE = 1 where WC_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
			if($SEQ_NO=="") { 
				$cmd = " update PER_WORK_CYCLE set WC_SEQ_NO='' where WC_CODE='$CODE' "; 
				}else {	
				$cmd = " update PER_WORK_CYCLE set WC_SEQ_NO=$SEQ_NO where WC_CODE='$CODE' "; 
			}
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > จัดลำดับ [$CODE : $SEQ_NO]");
	} // end if
	

		$tmp_WC_CODE = trim($WC_CODE);
		$tmp_WC_NAME = trim($WC_NAME);
		$tmp_WC_START = $WC_START_HH.$WC_START_II;
		if(empty($WC_END_HH)){ $WC_END_HH="00";}
		if(empty($WC_END_II)){ $WC_END_II="00";}
		$tmp_WC_END = $WC_END_HH.$WC_END_II;
		$tmp_WC_ACTIVE = $WC_ACTIVE;
		$tmp_TIME_LEAVEEARLY = $TIME_LEAVEEARLY_HH.$TIME_LEAVEEARLY_II;
		$tmp_TIME_LEAVEAFTER = $TIME_LEAVEAFTER_HH.$TIME_LEAVEAFTER_II;
		$tmp_WORKCYCLE_TYPE = 1;
		
		if($NEXTDAY_EXIT==1){
			$Save_NEXTDAY_EXIT = 1;
		}else{
			$Save_NEXTDAY_EXIT = 0;
		}
		
		if($NEXTDAY_ONTIME==1){
			$Save_NEXTDAY_ONTIME = 1;
		}else{
			$Save_NEXTDAY_ONTIME = 0;
		}
		
		if($NEXTDAY_ENDLATETIME==1){
			$Save_NEXTDAY_ENDLATETIME = 1;
		}else{
			$Save_NEXTDAY_ENDLATETIME = 0;
		}
		
		if($TMP_P_EXTRATIME){
			$Save_MIN_ONTIME = $TMP_P_EXTRATIME;
		}else{
			$Save_MIN_ONTIME = 0;
		}
		
		if($TMP_P_TIMEOVERLATE){
			$Save_MIN_ENDLATETIME = $TMP_P_TIMEOVERLATE;
		}else{
			$Save_MIN_ENDLATETIME = 0;
		}
		
		$Save_ON_TIME = substr($hidP_EXTRATIME_SHOW,0,2).substr($hidP_EXTRATIME_SHOW,3,2);
		$Save_END_LATETIME = substr($hidP_TIMEOVERLATE_SHOW,0,2).substr($hidP_TIMEOVERLATE_SHOW,3,2);
		
		$Save_START_SCAN=$START_SCAN_HH.$START_SCAN_II;
		
		if($SAMEDAY_STARTSCAN==1){
			$Save_SAMEDAY_STARTSCAN = 1;
		}else{
			$Save_SAMEDAY_STARTSCAN = 0;
		}
		
		$Save_END_SCAN=$END_SCAN_HH.$END_SCAN_II;
		if($NEXTDAY_ENDSCAN==1){
			$Save_NEXTDAY_ENDSCAN = 1;
		}else{
			$Save_NEXTDAY_ENDSCAN = 0;
		}
		
		
		/**/
		
		 function diff2time($begin,$end){  
			return round((strtotime($end)-strtotime($begin))/(60),0);  
		}  

		if($SAMEDAY_STARTSCAN==1){
			$month=date("m"); //สร้างค่าเดือนปัจจุบัน
			$day=date("d")-1; //สร้างค่าย้อนหลังไป 5 วัน
			$year=date("Y"); //สร้างค่าปีปัจจุบัน
			$mk_data=mktime(22, 15, 10, $month, $day, $year); //กำหนดค่าโดย mktime
			$day_todayORyesterday= date("Y/m/d", $mk_data); //แสดงผลโดย Format M-d-Y
			$endSTARTSCAN = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
			$beginSTARTSCAN = $day_todayORyesterday." ".$START_SCAN_HH.":".$START_SCAN_II.":00"; 
		}else{
			$endSTARTSCAN = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
			$beginSTARTSCAN = date("Y/m/d")." ".$START_SCAN_HH.":".$START_SCAN_II.":00"; 
			
		}
		
		$Save_MIN_STARTSCAN=diff2time($beginSTARTSCAN,$endSTARTSCAN);
		
		if($NEXTDAY_ENDSCAN==1){
			$month=date("m"); //สร้างค่าเดือนปัจจุบัน
			$day=date("d")+1; //สร้างค่าย้อนหลังไป 5 วัน
			$year=date("Y"); //สร้างค่าปีปัจจุบัน
			$mk_data=mktime(22, 15, 10, $month, $day, $year); //กำหนดค่าโดย mktime
			$day_todayORtomorow= date("Y/m/d", $mk_data); //แสดงผลโดย Format M-d-Y
			$endENDSCAN = $day_todayORtomorow." ".$END_SCAN_HH.":".$END_SCAN_II.":00"; 
			$beginENDSCAN = date("Y/m/d")." ".$WC_END_HH.":".$WC_END_II.":00"; 
		}else{
			$endENDSCAN = date("Y/m/d")." ".$END_SCAN_HH.":".$END_SCAN_II.":00"; 
			$beginENDSCAN = date("Y/m/d")." ".$WC_END_HH.":".$WC_END_II.":00"; 
		}
		
		$Save_MIN_ENDSCAN=diff2time($beginENDSCAN,$endENDSCAN);
		
		//---------------------------
		if($NEXTDAY_LEAVEEARLY==1){
			$Save_NEXTDAY_LEAVEEARLY = 1;
		}else{
			$Save_NEXTDAY_LEAVEEARLY = 0;
		}
	
		if($NEXTDAY_LEAVEAFTER==1){
			$Save_NEXTDAY_LEAVEAFTER = 1;
		}else{
			$Save_NEXTDAY_LEAVEAFTER = 0;
		}
		
		
		if($NEXTDAY_LEAVEEARLY==1){
			$month=date("m"); //สร้างค่าเดือนปัจจุบัน
			$day=date("d")+1; //สร้างค่าย้อนหลังไป 5 วัน
			$year=date("Y"); //สร้างค่าปีปัจจุบัน
			$mk_data=mktime(22, 15, 10, $month, $day, $year); //กำหนดค่าโดย mktime
			$day_todayORtomorow= date("Y/m/d", $mk_data); //แสดงผลโดย Format M-d-Y
			$endLEAVEEARLY = $day_todayORtomorow." ".$TIME_LEAVEEARLY_HH.":".$TIME_LEAVEEARLY_II.":00"; 
			$beginLEAVEEARLY = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
		}else{
			$endLEAVEEARLY = date("Y/m/d")." ".$TIME_LEAVEEARLY_HH.":".$TIME_LEAVEEARLY_II.":00"; 
			$beginLEAVEEARLY = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
		}
		
		$Save_MIN_LEAVEEARLY=diff2time($beginLEAVEEARLY,$endLEAVEEARLY);
		
		if($NEXTDAY_LEAVEAFTER==1){
			$month=date("m"); //สร้างค่าเดือนปัจจุบัน
			$day=date("d")+1; //สร้างค่าย้อนหลังไป 5 วัน
			$year=date("Y"); //สร้างค่าปีปัจจุบัน
			$mk_data=mktime(22, 15, 10, $month, $day, $year); //กำหนดค่าโดย mktime
			$day_todayORtomorow= date("Y/m/d", $mk_data); //แสดงผลโดย Format M-d-Y
			$endLEAVEAFTER = $day_todayORtomorow." ".$TIME_LEAVEAFTER_HH.":".$TIME_LEAVEAFTER_II.":00"; 
			$beginLEAVEAFTER = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
		}else{
			$endLEAVEAFTER = date("Y/m/d")." ".$TIME_LEAVEAFTER_HH.":".$TIME_LEAVEAFTER_II.":00"; 
			$beginLEAVEAFTER = date("Y/m/d")." ".$WC_START_HH.":".$WC_START_II.":00"; 
		}
		
		$Save_MIN_LEAVEAFTER=diff2time($beginLEAVEAFTER,$endLEAVEAFTER);
	
	if($command == "ADD"){
		$cmd = " select WC_CODE, WC_NAME
				  from PER_WORK_CYCLE where WC_CODE='$tmp_WC_CODE' OR WC_NAME='". $tmp_WC_NAME ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_WORK_CYCLE
						 			(WC_CODE,WC_NAME,WC_START,WC_END,WC_ACTIVE,UPDATE_USER,UPDATE_DATE,
						 			TIME_LEAVEEARLY,TIME_LEAVEAFTER,WORKCYCLE_TYPE,NEXTDAY_EXIT,
									NEXTDAY_ONTIME,NEXTDAY_ENDLATETIME,MIN_ONTIME,MIN_ENDLATETIME,
									ON_TIME,END_LATETIME,START_SCAN,SAMEDAY_STARTSCAN,
									END_SCAN,NEXTDAY_ENDSCAN,MIN_STARTSCAN,MIN_ENDSCAN,
									NEXTDAY_LEAVEEARLY,NEXTDAY_LEAVEAFTER,
									MIN_LEAVEEARLY,MIN_LEAVEAFTER
									) 
						values ('$tmp_WC_CODE', '$tmp_WC_NAME', '$tmp_WC_START', '$tmp_WC_END',
						 			$tmp_WC_ACTIVE, $SESS_USERID, '$UPDATE_DATE',
						 			'$tmp_TIME_LEAVEEARLY','$tmp_TIME_LEAVEAFTER','$tmp_WORKCYCLE_TYPE',
									'$Save_NEXTDAY_EXIT','$Save_NEXTDAY_ONTIME','$Save_NEXTDAY_ENDLATETIME',
									$Save_MIN_ONTIME,$Save_MIN_ENDLATETIME,'$Save_ON_TIME','$Save_END_LATETIME',
									'$Save_START_SCAN','$Save_SAMEDAY_STARTSCAN','$Save_END_SCAN',
									'$Save_NEXTDAY_ENDSCAN',$Save_MIN_STARTSCAN,$Save_MIN_ENDSCAN,
									'$Save_NEXTDAY_LEAVEEARLY','$Save_NEXTDAY_LEAVEAFTER',
									$Save_MIN_LEAVEEARLY,$Save_MIN_LEAVEAFTER
									) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล [".$tmp_WC_CODE." : ".$tmp_WC_NAME." : ".$tmp_WC_START." : ".$tmp_WC_END." : ".$tmp_WC_ACTIVE." : ".$tmp_TIME_LEAVEEARLY." : ".$tmp_TIME_LEAVEAFTER." : ".$tmp_WORKCYCLE_TYPE."]");
		
			echo "<script>window.location='../admin/master_table_workcycle.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

		
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสหรือชื่อรอบซ้ำ [".$data[WC_CODE]." ".$data[WC_NAME]."]";
			$P_EXTRATIME_SHOW=$hidP_TIMEOVERLATE_SHOW;
			$P_TIMEOVERLATE_SHOW=$hidP_TIMEOVERLATE_SHOW;
			
		} // endif
	}

	if($command == "UPDATE"){
		$cmd = " select WC_CODE, WC_NAME
				  from PER_WORK_CYCLE where WC_NAME='$tmp_WC_NAME' AND WC_NAME!='$hidWC_NAME'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
				$cmd = " update PER_WORK_CYCLE set 
										WC_CODE='$tmp_WC_CODE',WC_NAME='$tmp_WC_NAME',
										WC_START='$tmp_WC_START',WC_END='$tmp_WC_END',WC_ACTIVE=$tmp_WC_ACTIVE,
										UPDATE_USER=$SESS_USERID,UPDATE_DATE='$UPDATE_DATE',
										TIME_LEAVEEARLY='$tmp_TIME_LEAVEEARLY',
										TIME_LEAVEAFTER='$tmp_TIME_LEAVEAFTER',WORKCYCLE_TYPE='$tmp_WORKCYCLE_TYPE',
										NEXTDAY_EXIT=$Save_NEXTDAY_EXIT,
										NEXTDAY_ONTIME='$Save_NEXTDAY_ONTIME',NEXTDAY_ENDLATETIME='$Save_NEXTDAY_ENDLATETIME',
										MIN_ONTIME=$Save_MIN_ONTIME,MIN_ENDLATETIME=$Save_MIN_ENDLATETIME,
										ON_TIME='$Save_ON_TIME',END_LATETIME='$Save_END_LATETIME',
										START_SCAN='$Save_START_SCAN',SAMEDAY_STARTSCAN='$Save_SAMEDAY_STARTSCAN',
										END_SCAN='$Save_END_SCAN',NEXTDAY_ENDSCAN='$Save_NEXTDAY_ENDSCAN',
										MIN_STARTSCAN=$Save_MIN_STARTSCAN,MIN_ENDSCAN=$Save_MIN_ENDSCAN,
										NEXTDAY_LEAVEEARLY='$Save_NEXTDAY_LEAVEEARLY',
										NEXTDAY_LEAVEAFTER='$Save_NEXTDAY_LEAVEAFTER',
										MIN_LEAVEEARLY=$Save_MIN_LEAVEEARLY,MIN_LEAVEAFTER=$Save_MIN_LEAVEAFTER
										where WC_CODE='".$WC_CODE."' ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล [".$tmp_WC_CODE." : ".$tmp_WC_NAME." : ".$tmp_WC_START." : ".$tmp_WC_END." : ".$tmp_WC_ACTIVE." : ".$tmp_TIME_LEAVEEARLY." : ".$tmp_TIME_LEAVEAFTER." : ".$tmp_WORKCYCLE_TYPE."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "ชื่อรอบซ้ำ [".$data[WC_CODE]." ".$data[WC_NAME]."]";
			$UPD=1;
			$P_EXTRATIME_SHOW=$hidP_TIMEOVERLATE_SHOW;
			$P_TIMEOVERLATE_SHOW=$hidP_TIMEOVERLATE_SHOW;
		} // endif
		$command ="";
	}
	
	if($command == "DELETE"){
		$cmd = " select WC_NAME, WC_START, WC_END, WC_ACTIVE,
                                TIME_LEAVEEARLY,TIME_LEAVEAFTER,WORKCYCLE_TYPE
								 from PER_WORK_CYCLE where WC_CODE='".$tmp_WC_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$del_WC_NAME = $data[WC_NAME];
		$del_WC_START = $data[WC_START];
		$del_WC_END = $data[WC_END];
		$del_TIME_LEAVEEARLY = $data[TIME_LEAVEEARLY];
		$del_TIME_LEAVEAFTER = $data[TIME_LEAVEAFTER];
		$del_WORKCYCLE_TYPE = $data[WORKCYCLE_TYPE];
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูล [".$tmp_WC_CODE." : ".$del_WC_NAME." : ".$del_WC_START." : ".$del_WC_END." : ".$del_WC_ACTIVE." : ".$del_TIME_LEAVEEARLY." : ".$del_TIME_LEAVEAFTER." : ".$del_WORKCYCLE_TYPE."]");
	
		
		$cmd = " delete from PER_WORK_CYCLE where WC_CODE='".$tmp_WC_CODE."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		echo "<script>window.location='../admin/master_table_workcycle.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
		
	}
	
	if($UPD){
		$cmd = " select WC_NAME,WC_START,WC_END,WC_ACTIVE,UPDATE_USER,UPDATE_DATE,
						 			TIME_LEAVEEARLY,TIME_LEAVEAFTER,WORKCYCLE_TYPE,NEXTDAY_EXIT,
									NEXTDAY_ONTIME,NEXTDAY_ENDLATETIME,MIN_ONTIME,MIN_ENDLATETIME,
									ON_TIME,END_LATETIME,START_SCAN,SAMEDAY_STARTSCAN,
									END_SCAN,NEXTDAY_ENDSCAN,NEXTDAY_LEAVEEARLY,NEXTDAY_LEAVEAFTER
				  from PER_WORK_CYCLE where WC_CODE='".$tmp_WC_CODE."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$WC_NAME = $data[WC_NAME];
		$WC_START_HH = substr($data[WC_START],0,2);
		$WC_START_II = substr($data[WC_START],2,2);
		$WC_END_HH = substr($data[WC_END],0,2);
		$WC_END_II = substr($data[WC_END],2,2);
		$WC_ACTIVE = $data[WC_ACTIVE];
		$NEXTDAY_EXIT = $data[NEXTDAY_EXIT];
		$NEXTDAY_ONTIME = $data[NEXTDAY_ONTIME];
		$NEXTDAY_ENDLATETIME = $data[NEXTDAY_ENDLATETIME];
		$MIN_ONTIME = $data[MIN_ONTIME];
		$MIN_ENDLATETIME = $data[MIN_ENDLATETIME];
		$NEXTDAY_LEAVEEARLY = $data[NEXTDAY_LEAVEEARLY];
		$NEXTDAY_LEAVEAFTER = $data[NEXTDAY_LEAVEAFTER];
		$newtimestampBgn = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$MIN_ONTIME.' minute'); /*Bgn*/
		$newtimestampEnd = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$MIN_ENDLATETIME.' minute'); /*Bgn*/
		$P_EXTRATIME_SHOW =  date('H:i', $newtimestampBgn);
		$P_TIMEOVERLATE_SHOW =  date('H:i', $newtimestampEnd);
		
		$TMP_P_EXTRATIME2 = $MIN_ONTIME + 1;
		$newtimestampBgn1 = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$TMP_P_EXTRATIME2.' minute'); /*Bgn*/
		$P_EXTRATIME_SHOW2 =  date('H:i', $newtimestampBgn1);
		
		$TIME_LEAVEEARLY_HH = substr($data[TIME_LEAVEEARLY],0,2);
		$TIME_LEAVEEARLY_II = substr($data[TIME_LEAVEEARLY],2,2);
		$TIME_LEAVEAFTER_HH = substr($data[TIME_LEAVEAFTER],0,2);
		$TIME_LEAVEAFTER_II = substr($data[TIME_LEAVEAFTER],2,2);
		
		$START_SCAN_HH = substr($data[START_SCAN],0,2);
		$START_SCAN_II = substr($data[START_SCAN],2,2);
		$SAMEDAY_STARTSCAN = $data[SAMEDAY_STARTSCAN];
		$END_SCAN_HH = substr($data[END_SCAN],0,2);
		$END_SCAN_II = substr($data[END_SCAN],2,2);
		$NEXTDAY_ENDSCAN = $data[NEXTDAY_ENDSCAN];
		
		$WORKCYCLE_TYPE = $data[WORKCYCLE_TYPE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$WC_CODE = "";
		$WC_NAME = "";
		$WC_START_HH = "00";
		$WC_START_II = "00";
		$WC_END_HH = "00";
		$WC_END_II = "00";
		$TIME_LEAVEEARLY_HH = "13";
		$TIME_LEAVEEARLY_II = "00";
		$TIME_LEAVEAFTER_HH = "12";
		$TIME_LEAVEAFTER_II = "00";
		$WC_ACTIVE = 1;
		$START_SCAN_HH = "00";
		$START_SCAN_II = "00";
		$SAMEDAY_STARTSCAN = 0;
		$END_SCAN_HH = "23";
		$END_SCAN_II = "59";
		$NEXTDAY_ENDSCAN =0;
		$NEXTDAY_ONTIME =0;
		$NEXTDAY_ENDLATETIME = 0;
		$NEXTDAY_LEAVEEARLY = 0;
		$NEXTDAY_LEAVEAFTER = 0;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>