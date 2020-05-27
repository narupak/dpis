
<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
		
		if(($START_TIME_HH."".$START_TIME_II =="0000") && ($END_TIME_HH."".$END_TIME_HH =="0000") ){
			$START_TIME_save = NULL;
			$END_TIME_save = NULL;
			$NUM_HRS_save= "NULL";
			$AMOUNT_save = "NULL";
		}else{
			$START_TIME_save = $START_TIME_HH."".$START_TIME_II;
			$END_TIME_save = $END_TIME_HH."".$END_TIME_II;
			/* คำนวณชั่วโมง*/
			$START_TIME_tmp = $START_TIME_HH.":".$START_TIME_II;
			$END_TIME_tmp = $END_TIME_HH.":".$END_TIME_II;
			
			/*วันทำงาน*/
			if($HIDHOLYDAY_FLAG==0){
				/*หารอบปัจจุบัน*/
				/*$cmd = " SELECT cl.WC_END,cl.WC_CODE FROM PER_WORK_CYCLEHIS clh
							left join PER_WORK_CYCLE cl on(cl.WC_CODE=clh.WC_CODE)  
							where clh.PER_ID =$HIDPER_ID
							AND  '$TOs_DATE' between clh.START_DATE AND 
							case when clh.END_DATE is not null then clh.END_DATE else '$TOs_DATE' end ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if($data[WC_END]){
					if($data[WC_CODE] != -1){
						if($START_TIME_save < $data[WC_END]){
							$WC_END = substr($data[WC_END],0,2).":".substr($data[WC_END],0,2);
						}else{
							$WC_END = $START_TIME_tmp;
						}
					}else{ //แบบนับชั่วโมง
						// จำนวนชั่วโมงวันทำงาน
						$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$TMP_P_FULLTIME = $data[CONFIG_VALUE];
						
						$TIME_MIN_H = substr($HID_CHK_TIME_MIN,0,2);
						$TIME_MIN_I = substr($HID_CHK_TIME_MIN,3,2);
						
						$WC_END = date("H:i", mktime(date("H",$TIME_MIN_H)+$TMP_P_FULLTIME, date("i",0)+$TIME_MIN_I, date("s")+0, date("m")+0  , date("d")+0, date("Y")+0))."";
						
					}
				}else{
					$WC_END = "16:30";
				}
				
				$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($WC_END))/  ( 60 * 60 ));
				$tmp_NUM_HRS_E = 0;
				*/

				// จำนวนชั่วโมงวันทำงาน
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_FULLTIME = $data[CONFIG_VALUE];
				
				$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
				 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME / 2){
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
					
					$tmp_NUM_HRS_B = $TMP_P_OTWORKDAY;
					$tmp_NUM_HRS_E = 0;
				 }else{
					if($START_TIME_HH.$START_TIME_II<='1159'){
					
						if($END_TIME_HH.$END_TIME_II<='1159'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						}elseif($END_TIME_HH.$END_TIME_II>='1200' && $END_TIME_HH.$END_TIME_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						}else{
							$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
						}
						
					}elseif($START_TIME_HH.$START_TIME_II>='1200' && $START_TIME_HH.$START_TIME_II<='1259'){
						
						if($END_TIME_HH.$END_TIME_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = 0;
						}else{
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
						}
						
					}else{
						
						$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						$tmp_NUM_HRS_E = 0;
					}
				 }
					
			}else{
				// จำนวนชั่วโมงวันทำงาน
				$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TMP_P_FULLTIME = $data[CONFIG_VALUE];
				
				$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
				 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
					
					$tmp_NUM_HRS_B = $TMP_P_OTWEEKEND;
					$tmp_NUM_HRS_E = 0;
				 }else{
					if($START_TIME_HH.$START_TIME_II<='1159'){
					
						if($END_TIME_HH.$END_TIME_II<='1159'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						}elseif($END_TIME_HH.$END_TIME_II>='1200' && $END_TIME_HH.$END_TIME_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						}else{
							$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
						}
						
					}elseif($START_TIME_HH.$START_TIME_II>='1200' && $START_TIME_HH.$START_TIME_II<='1259'){
						
						if($END_TIME_HH.$END_TIME_II<='1259'){
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = 0;
						}else{
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
						}
						
					}else{
						
						$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
						$tmp_NUM_HRS_E = 0;
					}
				}
				
			}
			
			
			
			$tmp_NUM_HRS=$tmp_NUM_HRS_B + $tmp_NUM_HRS_E;
			
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
				
			}
			
		}

		$cmd = " UPDATE TA_PER_OT	SET
								   START_TIME='$START_TIME_save',
								   END_TIME='$END_TIME_save',
								   NUM_HRS=$NUM_HRS_save,
								   AMOUNT=$AMOUNT_save,
								   UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							WHERE  PER_ID=$HIDPER_ID AND  OT_DATE='$HIDOT_DATE' ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลกำหนดบุคคลที่ทำ OT [$HIDPER_ID : $HIDOT_DATE : $HIDPER_CARDNO]");
			
			$command = "";			
		
	}
	
	
	if($command=="DELETE"){
		
		$cmd = " DELETE FROM  TA_PER_OT	WHERE  PER_ID=$HIDPER_ID AND  OT_DATE='$HIDOT_DATE' ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกำหนดบุคคลที่ทำ OT [$HIDPER_ID : $HIDOT_DATE : $HIDPER_CARDNO]");
			
			$command = "SEARCH";			
		
	}
	
	
	if($UPD){
		$cmd = "	  select 		g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
										ot.OT_DATE,ot.UPDATE_USER,ot.UPDATE_DATE,
										ot.PER_CARDNO,ot.PER_ID,ot.HOLYDAY_FLAG,
										ot.START_TIME,ot.END_TIME,ot.PER_ID,a.POS_ID,a.POEM_ID,a.POEMS_ID,a.POT_ID,
										f.POSITION_LEVEL,
										ot.ORG_ID AS  OT_ORG_ID,ot.DEPARTMENT_ID AS OT_DEPARTMENT_ID,
										ot.ORG_LOWER1 AS OT_ORG_LOWER1,ot.ORG_LOWER2 AS OT_ORG_LOWER2,
										ot.ORG_LOWER3 AS OT_ORG_LOWER3
						  from 		TA_PER_OT ot
						  left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID)
						  left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
						  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						  where 		ot.PER_ID=$HIDPER_ID AND  ot.OT_DATE='$HIDOT_DATE'
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$PER_CARDNO = $data[PER_CARDNO];
		$OT_DATE = show_date_format(trim($data[OT_DATE]), $DATE_DISPLAY);
		$HIDPER_ID = $data[PER_ID];
		$HIDOT_DATE = $data[OT_DATE];
		$HIDHOLYDAY_FLAG = $data[HOLYDAY_FLAG];
		$HIDPER_CARDNO = $data[PER_CARDNO];
		$START_TIME_HH = "";
		$START_TIME_II = "";
		$END_TIME_HH = "";
		$END_TIME_II = "";
		if($data[START_TIME] != $data[END_TIME]){
			$START_TIME_HH = substr($data[START_TIME],0,2);
			$START_TIME_II = substr($data[START_TIME],2,2);
			$END_TIME_HH = substr($data[END_TIME],0,2);
			$END_TIME_II = substr($data[END_TIME],2,2);
		}
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
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);
		
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
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OT_ORG ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$OT_ORG_NAME = trim($data_dpis2[ORG_NAME]);
		/*----------------------------------------------------------------------*/
		
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);
		
	} // end if
	
	
	if ($command == "SETFLAG_AUDIT") {
		if($list_audit_all=="1"){ /* บันทึกทั้งหมด*/
		
					 if( $SESS_USERGROUP ==1 || $NAME_GROUP_HRD =='HRD'){ 
						if($select_org_head == 1){  // หน่วยงานที่สังกัด 
							 if($select_org_structure == 0){ //ตามกฏหมาย
								if($search_org_id_1){
									$arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
								}elseif($search_org_id){
								  //echo "1";
								   $arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
								}else{
									//echo "2";
								   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
								}
							}else{ //ตามมอบหมาย
								if($search_org_id_1){
									$arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
								}elseif($search_org_id){
									//echo "3";
								   $arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
								}else{
									//echo "4";
								   $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
								}
							
							}
							
						 }else{ // หน่วยงานเจ้าของเรื่อง (OT)
							if($search_org_id_1){
								$arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
							}elseif($search_org_id){
								//echo "5";
								$arr_search_condition[] = "(ot.DEPARTMENT_ID=$search_org_id AND ot.ORG_LOWER1=-1)";
							}else{
								//echo "6";
								$arr_search_condition[] = "(ot.ORG_ID=$search_department_id AND ot.DEPARTMENT_ID=-1  )";
							}
						 }
					}else{ // กจ. สำนัก
						//echo "7";
						if($search_org_id_1){
							$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=$search_org_id_1)";
						}else{
							$arr_search_condition[] = "(ot.DEPARTMENT_ID= $search_org_id AND ot.ORG_LOWER1=-1)";
						}
					}
				   
					if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
					 if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";
				  
					if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
					if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
					
					if(trim($search_pos_no))  {	
						if ($search_per_type == 1 || $search_per_type==5)
							$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
						elseif ($search_per_type == 2) 
							$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
						elseif ($search_per_type == 3) 	
							$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
						elseif ($search_per_type == 4) 	
							$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
						else if ($search_per_type==0)		//ทั้งหมด
							$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
					}
					
					if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
					 if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";
				  
					if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
					if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
					
					if(trim($search_pos_no))  {	
						if ($search_per_type == 1 || $search_per_type==5)
							$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
						elseif ($search_per_type == 2) 
							$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
						elseif ($search_per_type == 3) 	
							$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
						elseif ($search_per_type == 4) 	
							$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
						else if ($search_per_type==0)		//ทั้งหมด
							$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
					}
					
					if(trim($search_pos_no_name)){
						if ($search_per_type == 1 || $search_per_type==5)
							$arr_search_condition[] = "(trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%')";
						elseif ($search_per_type == 2) 
							$arr_search_condition[] = "(trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%')";		
						elseif ($search_per_type == 3) 	
							$arr_search_condition[] = "(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%')";
						elseif ($search_per_type == 4) 	
							$arr_search_condition[] = "(trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')";
						else if ($search_per_type==0)		//ทั้งหมด
							$arr_search_condition[] = "((trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%') or 
							(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')) ";
					}
					
					if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
					if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
					if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
					if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
					if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
					
					if($search_date_min && $search_date_max){ 
						 $tmpsearch_date_min =  save_date($search_date_min);
						 $tmpsearch_date_max =  save_date($search_date_max);
						 $arr_search_condition[] = " ( ot.OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
					}else if($search_date_min && empty($search_date_max)){ 
						 $tmpsearch_date_min =  save_date($search_date_min);
						 $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_min') ";
					}else if(empty($search_date_min) && $search_date_max){ 
						 $tmpsearch_date_max =  save_date($search_date_max);
						 $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_max') ";
					}

			
				
				
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " select 	
                                 			ot.PER_ID,ot.OT_DATE
                                            from  TA_PER_OT ot
                                            left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID) 
                                            left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
                                            left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
                                            left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
                                            left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
                                            left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
                                            left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                                            left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
                                            left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
                                            left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                                 where 		(ot.NUM_HRS IS NOT NULL AND ot.NUM_HRS !=0) AND ot.AUDIT_FLAG=0
                                                $search_condition  ";

					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " update TA_PER_OT set AUDIT_FLAG = 1,
										AUDIT_USER=$SESS_USERID,AUDIT_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where (NUM_HRS IS NOT NULL AND NUM_HRS !=0) AND AUDIT_FLAG = 0 AND OT_DATE='".$data[OT_DATE]."' AND PER_ID=".$data[PER_ID];
							$db_insert->send_cmd($cmd);
							
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลตรวจทานการทำ OT [".$data[PER_ID]." : ".$data[OT_DATE]."]");
							
						}
				 	}
		
			
		}else{  /*กรณีที่เลือกบางรายการ*/
		
			if(count($list_audit_id)>0){
				for($i=0;$i<=count($list_audit_id);$i++){
					if(!empty($list_audit_id[$i])){
						$val =  explode("_",$list_audit_id[$i]);
						$cmd = " update TA_PER_OT set AUDIT_FLAG = 1,
										AUDIT_USER=$SESS_USERID,AUDIT_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where OT_DATE='".$val[1]."' AND PER_ID=".$val[0];
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลตรวจทานการทำ OT [".$val[0]." : ".$val[1]."]");
					}
					
				}
			}
		
		}
	
		
		
		
	}
	
	if($command=="REAUDIT"){
		
		$cmd = " update TA_PER_OT set AUDIT_FLAG = 0,AUDIT_USER=NULL,AUDIT_DATE = NULL,
						UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'	WHERE  PER_ID=$HIDPER_ID AND  OT_DATE='$HIDOT_DATE' ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ยกเลิกการตรวจทานข้อมูลกำหนดบุคคลที่ทำ OT [$HIDPER_ID : $HIDOT_DATE : $HIDPER_CARDNO]");
			
			$command = "SEARCH";			
		
	}
	
  
?>