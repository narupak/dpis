
<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($HIDPER_ID){
		$cmd = "	  select 		g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
										ot.OT_DATE,ot.UPDATE_USER,ot.UPDATE_DATE,
										ot.PER_CARDNO,ot.PER_ID,ot.HOLYDAY_FLAG,
										ot.START_TIME,ot.END_TIME,ot.PER_ID,a.POS_ID,a.POEM_ID,a.POEMS_ID,a.POT_ID,
										f.POSITION_LEVEL
						  from 		TA_PER_OT ot
						  left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID)
						  left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
						  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						  where 		ot.PER_ID=$HIDPER_ID 
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID]; 
		$POEMS_ID =$data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$LEVEL_NAME = $data[POSITION_LEVEL];
		
		
		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_POSITION =====  PER_TYPE=1
		if ($POS_ID) {			
			$cmd = " select 	ORG_ID, PL_CODE
					from 	PER_POSITION where POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			$PL_CODE = trim($data_dpis2[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PL_NAME]);
			
		}
		
		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMP =====  PER_TYPE=2
		if ($POEM_ID) {
			$cmd = " select 	ORG_ID,  PN_CODE
					from 	PER_POS_EMP where POEM_ID=$POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			$PER_POS_CODE = trim($data_dpis2[PN_CODE]);
			$cmd = " select PN_NAME, PG_CODE from PER_POS_NAME where trim(PN_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PN_NAME]);
		}
		
		
		//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMPSER =====  PER_TYPE=3
		if ($POEMS_ID) {
			$cmd = " select 	ORG_ID, EP_CODE
					from 	PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			//  table  PER_POS_EMP = ตำแหน่งพนักงานราชการ
			$PER_POS_CODE = trim($data_dpis2[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[EP_NAME]);	
		}
		
		//  ===== ถ้าเป็นลูกจ้างชั่วคราว SELECT ข้อมูลตำแหน่งจาก table PER_POS_TEMP =====  PER_TYPE=4
		if ($POT_ID) {
			$cmd = " select 	ORG_ID, TP_CODE
					from 	PER_POS_TEMP where POT_ID=$POT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			//  table  PER_POS_TEMP = ตำแหน่งลูกจ้างชั่วคราว
			$PER_POS_CODE = trim($data_dpis2[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[TP_NAME]);
		}
		
		if($P_OTTYPE_ORGANIZE==2){	
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$TMP_ORG_ID ";
		}else{
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		}
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);
		
		
		
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$TOs_DATE = date("Y-m-d ");
	if(empty($TIME_START)){$TIME_START=$TIME_END;}
	if(empty($TIME_END)){$TIME_END=$TIME_START;}
	$YMD_TIME_START = (substr($TIME_START,6,4)-543)."-".substr($TIME_START,3,2)."-".substr($TIME_START,0,2);
	$YMD_TIME_END = (substr($TIME_END,6,4)-543)."-".substr($TIME_END,3,2)."-".substr($TIME_END,0,2);
	
	/*หาจำนวนมีกี่วัน*/
	$date1 = $YMD_TIME_START.' 00:00:00';
	$date2 = $YMD_TIME_END.' 23:59:59';
	$ts1 = strtotime($date1);
	$ts2 = strtotime($date2);
	$seconds_diff = $ts2 - $ts1;
	$cntday=(floor($seconds_diff/(60*60*24))+1);
		
		
				
  		
	
	if($command=="UPDATE"){
		
		if(empty($OT_Status)) {$OT_Status=3;}
		
		if(($START_TIME_HH."".$START_TIME_II =="0000") && ($END_TIME_HH."".$END_TIME_HH =="0000") && ($START_BFW_HH."".$START_BFW_II =="0000") && ($END_BFW_HH."".$END_BFW_II =="0000") && ($START_TIME_HOLIDAY_HH."".$START_TIME_HOLIDAY_II =="0000") && ($END_TIME_HOLIDAY_HH."".$END_TIME_HOLIDAY_II =="0000")){
			$START_BFW_save = NULL;
			$END_BFW_save = NULL;
			$START_TIME_save = NULL;
			$END_TIME_save = NULL;
			$NUM_HRS_save= "NULL";
			$AMOUNT_save = "NULL";
		}else{
			
			if($HIDHOLYDAY_FLAG==0){
				//เช้า , เช้าเย็น
				if($OT_Status==1 || $OT_Status==3){
					$START_BFW_save = $START_BFW_HH."".$START_BFW_II;
					$END_BFW_save = $END_BFW_HH."".$END_BFW_II;
					
					/* คำนวณชั่วโมง*/
					$START_BFW_tmp = $START_BFW_HH.":".$START_BFW_II;
					$END_BFW_tmp = $END_BFW_HH.":".$END_BFW_II;
					
					if($OT_Status==1){
						$START_TIME_save = NULL;
						$END_TIME_save = NULL;
						
						$START_TIME_tmp = NULL;
						$END_TIME_tmp = NULL;
					}
				} //if($HIDDivDAY==1 || $HIDDivDAY==3){
				
				//เย็น, เช้าเย็น
				if($OT_Status==2 || $OT_Status==3){
					if($OT_Status==2){
						$START_BFW_save = NULL;
						$END_BFW_save = NULL;
						
						$START_BFW_tmp = NULL;
						$END_BFW_tmp = NULL;
					}
					$START_TIME_save = $START_TIME_HH."".$START_TIME_II;
					$END_TIME_save = $END_TIME_HH."".$END_TIME_II;
					
					/* คำนวณชั่วโมง*/
					$START_TIME_tmp = $START_TIME_HH.":".$START_TIME_II;
					$END_TIME_tmp = $END_TIME_HH.":".$END_TIME_II;
				} //if($HIDDivDAY==2 || $HIDDivDAY==3){
				
				
				
				
		}else{
			
			$START_BFW_save = NULL;
			$END_BFW_save = NULL;
			
			$START_BFW_tmp = NULL;
			$END_BFW_tmp = NULL;
			
			$START_TIME_save = $START_TIME_HOLIDAY_HH."".$START_TIME_HOLIDAY_II;
			$END_TIME_save = $END_TIME_HOLIDAY_HH."".$END_TIME_HOLIDAY_II;
			
			/* คำนวณชั่วโมง*/
			$START_TIME_tmp = $START_TIME_HOLIDAY_HH.":".$START_TIME_HOLIDAY_II;
			$END_TIME_tmp = $END_TIME_HOLIDAY_HH.":".$END_TIME_HOLIDAY_II;
			
		}// if(($START_TIME_HH."".$START_TIME_II =="0000") && ($END_TIME_HH."".$END_TIME_HH =="0000") && ($START_BFW_HH."".$START_BFW_II =="0000") && ($END_BFW_HH."".$END_BFW_II =="0000") && ($START_TIME_HOLIDAY_HH."".$START_TIME_HOLIDAY_II =="0000") && ($END_TIME_HOLIDAY_HH."".$END_TIME_HOLIDAY_II =="0000")){
			
			
			
			
			
		/*วันทำงาน*/
		if($HIDHOLYDAY_FLAG==0){
			
			if($OT_Status==1 || $OT_Status==3){
				// จำนวนชั่วโมงวันทำงาน
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_FULLTIME = $data[CONFIG_VALUE];
				
				$tmp_NUM_HRS_chk = floor((strtotime($END_BFW_tmp) - strtotime($START_BFW_tmp))/  ( 60 * 60 ));
				 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME)/2){
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
					
					$tmp_NUM_HRS_B_BFW = $TMP_P_OTWEEKEND;
					$tmp_NUM_HRS_E_BFW = 0;
					//echo 'a0|'.$tmp_NUM_HRS_E_BFW;
				 }else{
					if($START_BFW_HH.$START_BFW_II<='1159'){
					
						if($END_BFW_HH.$END_BFW_II<='1159'){
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = floor((strtotime($END_BFW_tmp) - strtotime($START_BFW_tmp))/  ( 60 * 60 ));
							//echo 'a1|'.$tmp_NUM_HRS_E_BFW;
						}elseif($END_BFW_HH.$END_BFW_II>='1200' && $END_BFW_HH.$END_BFW_II<='1259'){
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = floor((strtotime('12:00') - strtotime($START_BFW_tmp))/  ( 60 * 60 ));
							//echo 'a2|'.$tmp_NUM_HRS_E_BFW;
						}else{
							$tmp_NUM_HRS_B_BFW = floor((strtotime('12:00') - strtotime($START_BFW_tmp))/  ( 60 * 60 ));
							$tmp_NUM_HRS_E_BFW = floor((strtotime($END_BFW_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
							//echo 'a3|'.$tmp_NUM_HRS_E_BFW;
						}
						
					}elseif($START_BFW_HH.$START_BFW_II>='1200' && $START_BFW_HH.$START_BFW_II<='1259'){
						
						if($END_BFW_HH.$END_BFW_II<='1259'){
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = 0;
							//echo 'a4|'.$tmp_NUM_HRS_E_BFW;
						}else{
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = floor((strtotime($END_BFW_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
							//echo 'a5|'.$tmp_NUM_HRS_E_BFW;
						}
						
					}else{
						
						$tmp_NUM_HRS_B_BFW = floor((strtotime($END_BFW_tmp) - strtotime($START_BFW_tmp))/  ( 60 * 60 ));
						$tmp_NUM_HRS_E_BFW = 0;
						//echo 'a6|'.$tmp_NUM_HRS_E_BFW;
					}
				 } // if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
					
					if($OT_Status==1){
						$tmp_NUM_HRS_B = 0;
						$tmp_NUM_HRS_E = 0;
						//echo 'a7|'.$tmp_NUM_HRS_E_BFW;
					}
					
				} //if($HIDDivDAY==1 || $HIDDivDAY==3){
					
				if($OT_Status==2 || $OT_Status==3){
					// จำนวนชั่วโมงวันทำงาน
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_FULLTIME = $data[CONFIG_VALUE];
					
					$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
					 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME)/2){
						$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
						
						$tmp_NUM_HRS_B = $TMP_P_OTWEEKEND;
						$tmp_NUM_HRS_E = 0;
						//echo 'b0|'.$tmp_NUM_HRS_E;
					 }else{
						if($START_TIME_HH.$START_TIME_II<='1159'){
						
							if($END_TIME_HH.$END_TIME_II<='1159'){
								$tmp_NUM_HRS_B = 0;
								$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
								//echo 'b1|'.$tmp_NUM_HRS_E;
							}elseif($END_TIME_HH.$END_TIME_II>='1200' && $END_TIME_HH.$END_TIME_II<='1259'){
								$tmp_NUM_HRS_B = 0;
								$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
								//echo 'b2|'.$tmp_NUM_HRS_E;
							}else{
								$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
								$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
								//echo 'b3|'.$tmp_NUM_HRS_E;
							}
							
						}elseif($START_TIME_HH.$START_TIME_II>='1200' && $START_TIME_HH.$START_TIME_II<='1259'){
							
							if($END_TIME_HH.$END_TIME_II<='1259'){
								$tmp_NUM_HRS_B = 0;
								$tmp_NUM_HRS_E = 0;
								//echo 'b4|'.$tmp_NUM_HRS_E;
							}else{
								$tmp_NUM_HRS_B = 0;
								$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
								//echo 'b5|'.$tmp_NUM_HRS_E;
							}
							
						}else{
							
							$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							$tmp_NUM_HRS_E = 0;
							//echo 'b6|'.$tmp_NUM_HRS_E;
						}
					 } //if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
						
						if($OT_Status==2){
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = 0;
							//echo 'b7|'.$tmp_NUM_HRS_E;
						}
						
				} //if($HIDDivDAY==2 || $HIDDivDAY==3){
			
			
				
		}else{ // if($HIDHOLYDAY_FLAG==0){
				// จำนวนชั่วโมงวันทำงาน
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_FULLTIME = $data[CONFIG_VALUE];
				
				//echo $END_TIME_tmp."&&".$START_TIME_tmp;
				
				$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
				 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
					
					$tmp_NUM_HRS_B = $TMP_P_OTWORKDAY;
					$tmp_NUM_HRS_E = 0;
					//echo 'x0|'.$tmp_NUM_HRS_E;
				 }else{
					if($START_TIME_HOLIDAY_HH.$START_TIME_HOLIDAY_II<='1159'){
					
						if($END_TIME_HOLIDAY_HH.$END_TIME_HOLIDAY_II<='1159'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							//echo 'x1|'.$tmp_NUM_HRS_E;
						}elseif($END_TIME_HOLIDAY_HH.$END_TIME_HOLIDAY_II>='1200' && $END_TIME_HOLIDAY_HH.$END_TIME_HOLIDAY_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							//echo 'x2|'.$tmp_NUM_HRS_E;
						}else{
							//$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							//$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
							$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ))-1;
							$tmp_NUM_HRS_E = 0;
							//echo 'x3|'.$tmp_NUM_HRS_E;
						}
						
					}elseif($START_TIME_HOLIDAY_HH.$START_TIME_HOLIDAY_II>='1200' && $START_TIME_HOLIDAY_HH.$START_TIME_HOLIDAY_II<='1259'){
						
						if($END_TIME_HOLIDAY_HH.$END_TIME_HOLIDAY_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = 0;
							//echo 'x4|'.$tmp_NUM_HRS_E;
						}else{
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
							//echo 'x5|'.$tmp_NUM_HRS_E;
						}
						
					}else{
						
						$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						$tmp_NUM_HRS_E = 0;
						//echo 'x6|'.$tmp_NUM_HRS_E;
					}
				 } //if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME / 2)){
				
				
			} // if($HIDHOLYDAY_FLAG==0){
				
			
			
			
			$tmp_NUM_HRS=$tmp_NUM_HRS_B + $tmp_NUM_HRS_E + $tmp_NUM_HRS_B_BFW + $tmp_NUM_HRS_E_BFW;
			
			/*วันทำงาน*/
			if($HIDHOLYDAY_FLAG==0){
				/* จำนวนชั่วโมงวันทำงาน*/
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
				/* จำนวนเงินวันทำงาน*/
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATEWORKDAY' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_OTRATEWORKDAY = $data[CONFIG_VALUE];
				
				if($tmp_NUM_HRS>$TMP_P_OTWORKDAY){
					$NUM_HRS_save= $TMP_P_OTWORKDAY;
					$AMOUNT_save = ($TMP_P_OTWORKDAY * $TMP_P_OTRATEWORKDAY);
				}else{
					$NUM_HRS_save= $tmp_NUM_HRS;
					$AMOUNT_save = ($tmp_NUM_HRS * $TMP_P_OTRATEWORKDAY);
				}
				
			}else{
				/* จำนวนชั่วโมงวันหยุด*/
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
				/* จำนวนเงินวันหยุด*/
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATEWEEKEND' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_OTRATEWEEKEND = $data[CONFIG_VALUE];
				
				if($tmp_NUM_HRS>$TMP_P_OTWEEKEND){
					$NUM_HRS_save= $TMP_P_OTWEEKEND;
					$AMOUNT_save = ($TMP_P_OTWEEKEND * $TMP_P_OTRATEWEEKEND);
				}else{
					$NUM_HRS_save= $tmp_NUM_HRS;
					$AMOUNT_save = ($tmp_NUM_HRS * $TMP_P_OTRATEWEEKEND);
				}
				
			} //if($HIDHOLYDAY_FLAG==0){
			
		}
		
		if($NUM_HRS_save<=0){
			$NUM_HRS_save = "NULL";
		}
		
		if($AMOUNT_save<=0){
			$AMOUNT_save = "NULL";
		}
		
		if($START_TIME_save=='0000'){
			$START_TIME_save="";
		}
		
		if($END_TIME_save=='0000'){
			$END_TIME_save="";
		}
		
		if($START_BFW_save=='0000'){
			$START_BFW_save="";
		}
		
		if($END_BFW_save=='0000'){
			$END_BFW_save="";
		}
		

		 $cmd = " UPDATE TA_PER_OT	SET
								   START_TIME_BFW='$START_BFW_save',
								   END_TIME_BFW='$END_BFW_save',
								   START_TIME='$START_TIME_save',
								   END_TIME='$END_TIME_save',
								   NUM_HRS=$NUM_HRS_save,
								   AMOUNT=$AMOUNT_save,
								   OT_STATUS=$OT_Status,
								   UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							WHERE  PER_ID=$HIDPER_ID AND  OT_DATE='$HIDOT_DATE' ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลกำหนดบุคคลที่ทำ OT [$HIDPER_ID : $HIDOT_DATE : $HIDPER_CARDNO]");
			
			$command = "";			
		
	}
	
	
	if($command=="DELETE"){
		
		if(count($list_allow_id)>0){
				for($i=0;$i<=count($list_allow_id);$i++){
					if(!empty($list_allow_id[$i])){
						$val =  explode("_",$list_allow_id[$i]);
						$cmd = " DELETE FROM  TA_PER_OT	
						      WHERE  PER_ID= ".$val[0]." 
							      AND  OT_DATE='".$val[1]."'";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > ลบข้อมูลกำหนดบุคคลที่ทำ OT [".$val[0]." : ".$val[1]." : ".$val[2]."]");
					}
					
				}
			}
		
			$command = "";			
		
	}
	
	
	if($UPD){
		$cmd = "	  select 		
										ot.OT_DATE,ot.UPDATE_USER,ot.UPDATE_DATE,
										ot.PER_CARDNO,ot.PER_ID,ot.HOLYDAY_FLAG,
										ot.START_TIME,ot.END_TIME,ot.PER_ID,
										ot.ORG_ID AS  OT_ORG_ID,ot.DEPARTMENT_ID AS OT_DEPARTMENT_ID,
										ot.ORG_LOWER1 AS OT_ORG_LOWER1,ot.ORG_LOWER2 AS OT_ORG_LOWER2,
										ot.ORG_LOWER3 AS OT_ORG_LOWER3,ot.OT_STATUS,
										ot.START_TIME_BFW,ot.END_TIME_BFW,ot.OT_STATUS
						  from 		TA_PER_OT ot
						  where 		ot.PER_ID=$HIDPER_ID AND  ot.OT_DATE='$HIDOT_DATE'
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$OT_DATE = show_date_format(trim($data[OT_DATE]), $DATE_DISPLAY);
		$HIDPER_ID = $data[PER_ID];
		$HIDOT_DATE = $data[OT_DATE];
		$HIDHOLYDAY_FLAG = $data[HOLYDAY_FLAG];
		$HIDPER_CARDNO = $data[PER_CARDNO];
		
		// 1 เช้า 2 เย็น 3 เช้าเย็น
		$START_TIME_HH = "";
		$START_TIME_II = "";
		$END_TIME_HH = "";
		$END_TIME_II = "";
		if($data[HOLYDAY_FLAG]==0){
			if($data[OT_STATUS]==2 || $data[OT_STATUS]==3){
				if($data[START_TIME] != $data[END_TIME]){
					$START_TIME_HH = substr($data[START_TIME],0,2);
					$START_TIME_II = substr($data[START_TIME],2,2);
					$END_TIME_HH = substr($data[END_TIME],0,2);
					$END_TIME_II = substr($data[END_TIME],2,2);
				}
			}
		}
		$START_BFW_HH = "";
		$START_BFW_II = "";
		$END_BFW_HH = "";
		$END_BFW_II = "";
		if($data[HOLYDAY_FLAG]==0){
			if($data[OT_STATUS]==1 || $data[OT_STATUS]==3){
				if($data[START_TIME_BFW] != $data[END_TIME_BFW]){
					$START_BFW_HH = substr($data[START_TIME_BFW],0,2);
					$START_BFW_II = substr($data[START_TIME_BFW],2,2);
					$END_BFW_HH = substr($data[END_TIME_BFW],0,2);
					$END_BFW_II = substr($data[END_TIME_BFW],2,2);
				}
			}
		}
		
		$START_TIME_HOLIDAY_HH = "";
		$START_TIME_HOLIDAY_II = "";
		$END_TIME_HOLIDAY_HH = "";
		$END_TIME_HOLIDAY_II = "";
		if($data[HOLYDAY_FLAG]==1){
			if($data[START_TIME] != $data[END_TIME]){
				$START_TIME_HOLIDAY_HH = substr($data[START_TIME],0,2);
				$START_TIME_HOLIDAY_II = substr($data[START_TIME],2,2);
				$END_TIME_HOLIDAY_HH = substr($data[END_TIME],0,2);
				$END_TIME_HOLIDAY_II = substr($data[END_TIME],2,2);
			}
		}
		
		if($data[HOLYDAY_FLAG]==1){
			$HIDDivDAY = 4;
		}else{
			if($data[OT_STATUS]==1){
				$HIDDivDAY = 1;
			}elseif($data[OT_STATUS]==2){
				$HIDDivDAY = 2;
			}else{
				$HIDDivDAY = 3;
			}
			
		}
		
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		/*เจ้าของเรื่อง*/
		$OT_ORG_ID = $data[OT_ORG_ID];
		$OT_DEPARTMENT_ID = $data[OT_DEPARTMENT_ID];
		$OT_ORG_LOWER1 = $data[OT_ORG_LOWER1];
		$OT_ORG_LOWER2 = $data[OT_ORG_LOWER2];
		$OT_ORG_LOWER3 = $data[OT_ORG_LOWER3];
		$OT_ORG=0;
		if($OT_ORG_LOWER3 && $OT_ORG_LOWER3 != -1){
			$OT_ORG=$OT_ORG_LOWER3;
		}elseif($OT_ORG_LOWER2 && $OT_ORG_LOWER2 != -1){
			$OT_ORG=$OT_ORG_LOWER2;
		}elseif($OT_ORG_LOWER1 && $OT_ORG_LOWER1 != -1){
			$OT_ORG=$OT_ORG_LOWER1;
		}elseif($OT_DEPARTMENT_ID && $OT_DEPARTMENT_ID != -1){
			$OT_ORG=$OT_DEPARTMENT_ID;
		}elseif($OT_ORG_ID){
			$OT_ORG=$OT_ORG_ID;
		}
		
		if($P_OTTYPE_ORGANIZE==2){	
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$OT_ORG ";
		}else{
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OT_ORG ";
		}
		
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$OT_ORG_NAME = trim($data_dpis2[ORG_NAME]);
		/*----------------------------------------------------------------------*/
		
		
	} // end if
	
	
	
	
  
?>