<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");


	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	


	if($command == "UPDATE"){
		
		$tmp_WC_NAME = trim($WC_NAME);
		$tmp_WC_START = $WC_START_HH.$WC_START_II;
		$tmp_WC_ACTIVE = $WC_ACTIVE;
		
		
		$tmp_ON_TIME = $ON_TIME_HH.$ON_TIME_II;
		$tmp_END_LATETIME = $END_LATETIME_HH.$END_LATETIME_II;
		$Save_WC_SEQ_NO= 99;

		function diff2time($begin,$end){  
			return round((strtotime($end)-strtotime($begin))/(60),0);  
		}  
		$end = date("Y/m/d")." ".$END_LATETIME_HH.":".$END_LATETIME_II.":00"; 
		$begin = date("Y/m/d")." ".$ON_TIME_HH.":".$ON_TIME_II.":00"; 
		$Save_MIN_ENDLATETIME=diff2time($begin,$end);
		
		$tmp_TIME_LEAVEEARLY = $TIME_LEAVEEARLY_HH.$TIME_LEAVEEARLY_II;
		$tmp_TIME_LEAVEAFTER = $TIME_LEAVEAFTER_HH.$TIME_LEAVEAFTER_II;
		
		
		if($CHKWC_END==1){
			$tmp_WC_END = "";
			$Save_END_SCAN = "";
			$Save_MIN_ENDSCAN= "NULL";
		}else{
			$tmp_WC_END = $WC_END_HH.$WC_END_II;
			$Save_END_SCAN=$END_SCAN_HH.$END_SCAN_II;
			
			$endENDSCAN = date("Y/m/d")." ".$END_SCAN_HH.":".$END_SCAN_II.":00"; 
			$beginENDSCAN = date("Y/m/d")." ".$WC_END_HH.":".$WC_END_II.":00"; 
			
			$Save_MIN_ENDSCAN=diff2time($beginENDSCAN,$endENDSCAN);
		}
		
		$Save_MIN_LEAVEEARLY=0;
		if($TIME_LEAVEEARLY_HH.$TIME_LEAVEEARLY_II !="0000"){
			$endLEAVEEARLY = date("Y/m/d")." ".$TIME_LEAVEEARLY_HH.":".$TIME_LEAVEEARLY_II.":00"; 
			$beginLEAVEEARLY = date("Y/m/d")." ".$ON_TIME_HH.":".$ON_TIME_II.":00"; 
			$Save_MIN_LEAVEEARLY=diff2time($beginLEAVEEARLY,$endLEAVEEARLY);
			
		}
		$Save_MIN_LEAVEAFTER=0;
		if($TIME_LEAVEAFTER_HH.$TIME_LEAVEAFTER_II !="0000"){
			$endLEAVEAFTER = date("Y/m/d")." ".$TIME_LEAVEAFTER_HH.":".$TIME_LEAVEAFTER_II.":00"; 
			$beginLEAVEAFTER = date("Y/m/d")." ".$ON_TIME_HH.":".$ON_TIME_II.":00"; 
			$Save_MIN_LEAVEAFTER=diff2time($beginLEAVEAFTER,$endLEAVEAFTER);
		}
		
		

		$cmd = " update PER_WORK_CYCLE set 
								WC_NAME='$tmp_WC_NAME',
								WORKCYCLE_TYPE=2,
								WC_START='$tmp_WC_START',
								WC_END='$tmp_WC_END',
								ON_TIME='$tmp_ON_TIME',
								END_LATETIME='$tmp_END_LATETIME',
								MIN_ONTIME=0,
								MIN_ENDLATETIME=$Save_MIN_ENDLATETIME,
								WC_SEQ_NO=$Save_WC_SEQ_NO,
								TIME_LEAVEEARLY='$tmp_TIME_LEAVEEARLY',
								TIME_LEAVEAFTER='$tmp_TIME_LEAVEAFTER',
								END_SCAN='$Save_END_SCAN',
								MIN_ENDSCAN=$Save_MIN_ENDSCAN,
								MIN_LEAVEEARLY=$Save_MIN_LEAVEEARLY,MIN_LEAVEAFTER=$Save_MIN_LEAVEAFTER,
								WC_ACTIVE=$tmp_WC_ACTIVE,
								UPDATE_USER=$SESS_USERID,UPDATE_DATE='$UPDATE_DATE'
								where WC_CODE='-1' ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล [".$tmp_WC_CODE." : ".$tmp_WC_NAME." : ".$tmp_WC_START." : ".$tmp_WC_END." : ".$tmp_WC_ACTIVE." : ".$tmp_TIME_LEAVEEARLY." : ".$tmp_TIME_LEAVEAFTER." : ".$tmp_WORKCYCLE_TYPE."]");
		echo "<script>window.location='../admin/es_master_table_workcycle.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

		$command ="";
	}
	
	

		$cmd = " select WC_NAME,WC_START,WC_END,WC_ACTIVE,UPDATE_USER,UPDATE_DATE,
						 			TIME_LEAVEEARLY,TIME_LEAVEAFTER,WORKCYCLE_TYPE,NEXTDAY_EXIT,
									NEXTDAY_ONTIME,NEXTDAY_ENDLATETIME,MIN_ONTIME,MIN_ENDLATETIME,
									ON_TIME,END_LATETIME,WC_SEQ_NO,END_SCAN
				  from PER_WORK_CYCLE where WC_CODE='-1' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$WC_CODE= "-1";
		$WC_NAME = $data[WC_NAME];
		$WC_START_HH = substr($data[WC_START],0,2);
		$WC_START_II = substr($data[WC_START],2,2);
		if($data[WC_END]){
			$CHKWC_END = 0;
		}else{
			$CHKWC_END = 1;
		}
		
		$WC_END_HH = substr($data[WC_END],0,2);
		$WC_END_II = substr($data[WC_END],2,2);
		$WC_ACTIVE = $data[WC_ACTIVE];
		$NEXTDAY_EXIT = $data[NEXTDAY_EXIT];
		$NEXTDAY_ONTIME = $data[NEXTDAY_ONTIME];
		$NEXTDAY_ENDLATETIME = $data[NEXTDAY_ENDLATETIME];
		$MIN_ONTIME = $data[MIN_ONTIME];
		$MIN_ENDLATETIME = $data[MIN_ENDLATETIME];
		$ON_TIME_HH = substr($data[ON_TIME],0,2);
		$ON_TIME_II = substr($data[ON_TIME],2,2);
		$END_LATETIME_HH = substr($data[END_LATETIME],0,2);
		$END_LATETIME_II = substr($data[END_LATETIME],2,2);
		$WC_SEQ_NO= $data[WC_SEQ_NO];
		$END_SCAN_HH = substr($data[END_SCAN],0,2);
		$END_SCAN_II = substr($data[END_SCAN],2,2);
		$newtimestampBgn = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$TMP_P_EXTRATIME.' minute'); /*Bgn*/
		$newtimestampEnd = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$TMP_P_TIMEOVERLATE.' minute'); /*Bgn*/
		$P_EXTRATIME_SHOW =  date('H:i', $newtimestampBgn);
		$P_TIMEOVERLATE_SHOW =  date('H:i', $newtimestampEnd);
		
		$TMP_P_EXTRATIME2 = $TMP_P_EXTRATIME + 1;
		$newtimestampBgn1 = strtotime(date('Y-m-d').' '.$WC_START_HH.':'.$WC_START_II.' + '.$TMP_P_EXTRATIME2.' minute'); /*Bgn*/
		$P_EXTRATIME_SHOW2 =  date('H:i', $newtimestampBgn1);
		
		$TIME_LEAVEEARLY_HH = substr($data[TIME_LEAVEEARLY],0,2);
		$TIME_LEAVEEARLY_II = substr($data[TIME_LEAVEEARLY],2,2);
		$TIME_LEAVEAFTER_HH = substr($data[TIME_LEAVEAFTER],0,2);
		$TIME_LEAVEAFTER_II = substr($data[TIME_LEAVEAFTER],2,2);
		$WORKCYCLE_TYPE = $data[WORKCYCLE_TYPE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	
?>