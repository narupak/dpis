<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="UPDATE"){
		
			$START_DATE_save = $HID_WORK_DATE." ".$APV_ENTTIME_HH.":".$APV_ENTTIME_II.":00";
			$START_DATE_del = $HID_WORK_DATE;

			$REMARK_save = trim($REMARK); 
			$WORK_FLAG_save = $WORK_FLAG; 
			if($NEXTDAY_ENDSCAN==1){
				$strStartDate = $HID_WORK_DATE;
				$END_DATE_save = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)))." ".$APV_EXITTIME_HH.":".$APV_EXITTIME_II.":00";
			}else{
				$END_DATE_save = $HID_WORK_DATE." ".$APV_EXITTIME_HH.":".$APV_EXITTIME_II.":00";
				
			}
			
					
			$cmd = " UPDATE PER_WORK_TIME SET	
						  APV_ENTTIME=to_date('$START_DATE_save','yyyy-mm-dd hh24:mi:ss'),
						   APV_EXITTIME=to_date('$END_DATE_save','yyyy-mm-dd hh24:mi:ss'),
						   WORK_FLAG=$WORK_FLAG_save,
						  REMARK='$REMARK_save',
						  UPDATE_USER=$SESS_USERID, 
						  UPDATE_DATE=to_char(sysdate,'yyyy-mm-dd hh24:mi:ss')
						  WHERE PER_ID=$HID_PER_ID 
						AND CONTROL_ID=$CONTROL_ID
						AND TO_CHAR(WORK_DATE,'yyyy-mm-dd') ='$HID_WORK_DATE' ";
			$db_dpis_n->send_cmd($cmd);
			
			if($HID_CLOSE_FLAG==1){
					if($HID_HOLIDAY_FLAG==0){	//ทำเฉพาะวันทำงาน
						if($WORK_FLAG !=$HID_WORK_FLAG){
							if($HID_WORK_FLAG==1 || $HID_WORK_FLAG==2){
								if($HID_WORK_FLAG==1){$del_WORK_FLAG=10;}else{$del_WORK_FLAG=13;}
								$db_dpis_del = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
								$cmddel =" delete from PER_ABSENTHIS 
														where ABS_STARTDATE = '$START_DATE_del' 
														and AB_CODE=$del_WORK_FLAG
														and PER_ID=$HID_PER_ID ";
																		//echo $cmddel."<br>";
								$db_dpis_del->send_cmd($cmddel);
							}
							
							if($WORK_FLAG==1 || $WORK_FLAG==2){
								// อัพเดท ประวัติการลา
								if($WORK_FLAG==1){$in_WORK_FLAG=10;}else{$in_WORK_FLAG=13;}
								$cmd = "select max(abs_id) + 1 AS CNTMAX from PER_ABSENTHIS ";
								$db_dpis->send_cmd($cmd);
								$data = $db_dpis->get_array();
								$CNTMAX = $data[CNTMAX];
								$db_dpis_up = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
								$cmdup =" insert into  PER_ABSENTHIS (
													ABS_ID,PER_ID,AB_CODE,ABS_STARTDATE,
													ABS_STARTPERIOD,ABS_ENDDATE,ABS_ENDPERIOD,
													ABS_DAY,UPDATE_USER,UPDATE_DATE)
												values($CNTMAX,$HID_PER_ID,$in_WORK_FLAG,
												'$START_DATE_del' , 3 ,
												'$START_DATE_del' , 3 ,1 ,-1 ,to_char(sysdate,'yyyy-mm-dd hh24:mi:ss') )";
												//echo $cmdup."<br>";
								$db_dpis_up->send_cmd($cmdup);
							}
							
						}
					}
			}
			
			
			
				
			//insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลรอบการลงเวลา [$CONTROL_ID : $HID_PER_ID : $HID_WORK_DATE]");
			$command="";
	}
	
	
	if($UPD){
		$cmd = "	  select 		
                                    TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
                                    TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
                                    TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,
                                    psn.PER_TYPE,wt.PER_ID,
                                    pn.PN_SHORTNAME||psn.PER_NAME||' '||psn.PER_SURNAME  AS FULLNAME_SHOW,
                                    c.ORG_ID,d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
                                    j.POT_ID AS POT_ORG_ID,
									psn.POS_ID,psn.POEM_ID,psn.POEMS_ID,psn.POT_ID,
                                    wc.WC_NAME,wt.REMARK,wt.WORK_FLAG,f.POSITION_LEVEL,
									TO_CHAR(wt.SCAN_ENTTIME,'hh24:mi') AS SCAN_ENTTIME,
                                    TO_CHAR(wt.SCAN_EXITTIME,'hh24:mi') AS SCAN_EXITTIME,
									col.CLOSE_DATE,wt.HOLIDAY_FLAG, wt.UPDATE_USER, wt.UPDATE_DATE
						from 		PER_WORK_TIME wt 
						left join PER_PERSONAL psn on(psn.PER_ID=wt.PER_ID)
						left join PER_PRENAME pn on(pn.PN_CODE=psn.PN_CODE) 
						left join PER_POSITION c on(c.POS_ID=psn.POS_ID) 
						left join PER_POS_EMP d on(d.POEM_ID=psn.POEM_ID) 
						left join PER_POS_EMPSER e on(e.POEMS_ID=psn.POEMS_ID) 
						left join PER_POS_TEMP j on (j.POT_ID=psn.POT_ID)
						left join PER_WORK_CYCLE wc on(wc.WC_CODE=wt.WC_CODE)
						left join PER_LEVEL f on (psn.LEVEL_NO=f.LEVEL_NO)
						left join PER_WORK_TIME_CONTROL col  on(col.CONTROL_ID=wt.CONTROL_ID)  
						  where 		wt.PER_ID=$HID_PER_ID 
						  		AND wt.CONTROL_ID=$CONTROL_ID
								AND TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') ='$HID_WORK_DATE'
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$HID_HOLIDAY_FLAG = $data[HOLIDAY_FLAG];
		 $WC_NAME_SHOW = "-";
         if( $data[WC_NAME]){
         	 $WC_NAME_SHOW = $data[WC_NAME];
         }
		 
		 $SCAN_ENTTIME_SHOW = "";
         if( $data[SCAN_ENTTIME]){
         	 $SCAN_ENTTIME_SHOW = $data[SCAN_ENTTIME]." น.";
         }
       
         $SCAN_EXITTIME_SHOW = "";
         if( $data[SCAN_EXITTIME]){
         	 $SCAN_EXITTIME_SHOW = $data[SCAN_EXITTIME]." น.";
         }
		 
		$WORK_DATE_SHOW = show_date_format($data[WORK_DATE], $DATE_DISPLAY);
		$HID_WORK_DATE = $data[WORK_DATE];
		
		
		$HID_PER_ID = $data[PER_ID];
        $DATA_PER_TYPE = $data[PER_TYPE];
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
        
        
         $APV_ENTTIME_HH = "00";
		 $APV_ENTTIME_II = "00";
         if( $data[APV_ENTTIME]){
         	 $APV_ENTTIME_HH = substr($data[APV_ENTTIME],0,2);
			 $APV_ENTTIME_II = substr($data[APV_ENTTIME],3,2);
         }else{
			 $APV_ENTTIME_HH = substr($data[SCAN_ENTTIME],0,2);
			 $APV_ENTTIME_II = substr($data[SCAN_ENTTIME],3,2); 
		}
		 
		 $APV_EXITTIME_HH = "00";
		 $APV_EXITTIME_II = "00";
       
         if( $data[APV_EXITTIME]){
         	 $APV_EXITTIME_HH = substr($data[APV_EXITTIME],0,2);
			 $APV_EXITTIME_II = substr($data[APV_EXITTIME],3,2);
         }else{
			 $APV_EXITTIME_HH = substr($data[SCAN_EXITTIME],0,2);
			 $APV_EXITTIME_II = substr($data[SCAN_EXITTIME],3,2);
		}
		 
		 $REMARK = $data[REMARK];
		 $WORK_FLAG = $data[WORK_FLAG];
		 $HID_WORK_FLAG = $data[WORK_FLAG];
		 $HID_CLOSE_FLAG = 0;
        if($data[CLOSE_DATE]){
        	$HID_CLOSE_FLAG= 1; //ยืนยันแล้ว
        }
		
		
		if($data[WORK_FLAG]==0){
			$TEXT_WORK_FLAG = "ปกติ";
        }else if($data[WORK_FLAG]==1){
        	$TEXT_WORK_FLAG = "สาย";
        }else if($data[WORK_FLAG]==2){
        	$TEXT_WORK_FLAG = "ขาดราชการ";
        }else if($data[WORK_FLAG]==3){
        	$TEXT_WORK_FLAG = "ออกก่อน";
        }else if($data[WORK_FLAG]==4){
        	$TEXT_WORK_FLAG = "ไม่ได้ลงเวลา";
        }
		 
		 
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db_dpis1->send_cmd($cmd);
		$data2 = $db_dpis1->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
         
         
         
         
		
	} // end if
	
	
	
?>