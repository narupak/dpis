<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
		$UPDATE_DATE = date("Y-m-d H:i:s");
		$START_DATE_save = save_date($TIME_START)." ".$WC_START_HH.":".$WC_START_II.":00"; 
		if($TIME_END){
			$END_DATE_save = save_date($TIME_END)." ".$WC_END_HH.":".$WC_END_II.":00";
		}else{
			$END_DATE_save ="";
		}
		
		
  		if($command=="ADD"){
			$HideID_save= explode(",",$HideID);
			foreach ($HideID_save as $value) {
				
				
				/* Update ข้อมูลเก่าก่อน*/
				$cmd2 = " select NVL(max(WH_ID),0) as max_id from PER_WORK_CYCLEHIS WHERE PER_ID=$value";
				$db_dpis2->send_cmd($cmd2);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$WH_ID_UP = $data2[max_id];
				if($WH_ID_UP > 0){
					$newtimestampBgn = strtotime(save_date($TIME_START).' '.$WC_START_HH.':'.$WC_START_II.' - 1 minute'); 
	    			$START_DATE_save_Up =date('Y-m-d H:i', $newtimestampBgn).":00"; 
					$cmd = " UPDATE PER_WORK_CYCLEHIS SET	
								  END_DATE='$START_DATE_save_Up',
								  UPDATE_USER=$SESS_USERID, 
								  UPDATE_DATE='$UPDATE_DATE'
								  WHERE WH_ID=$WH_ID_UP ";
					$db_dpis->send_cmd($cmd);
				}
				
				
				$cmd = " insert into PER_WORK_CYCLEHIS	(WH_ID, PER_ID, WC_CODE, START_DATE, END_DATE, REMARK, UPDATE_USER, UPDATE_DATE)
								  values (PER_WORK_CYCLEHIS_SEQ.NEXTVAL, $value, '$WC_CODE', '$START_DATE_save', '$END_DATE_save', '$REMARK', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis->send_cmd($cmd);
		//		$db_dpis->show_error();
		
				$cmd = " SELECT PER_WORK_CYCLEHIS_SEQ.currval AS CURID FROM dual ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$WH_ID = $data[CURID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกำหนดรอบการมาปฏิบัติราชการ [$value : $WH_ID : $WC_CODE]");
				
			} // end for
			echo "<script>window.location='../admin/es_t0203_workcyclehis_person_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";


 	} // end if*/
	
	
	if($command=="UPDATE"){
					$cmd = " select WC_CODE,PER_ID
								 from PER_WORK_CYCLEHIS where WH_ID=$HIDWH_ID";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$del_WC_CODE = $data[WC_CODE];
					$del_PER_ID = $data[PER_ID];
					/*อัพอันก่อน*/
					$newtimestampBgn = strtotime(save_date($TIME_START).' '.$WC_START_HH.':'.$WC_START_II.' - 1 minute'); 
	    			$START_DATE_save_Up =date('Y-m-d H:i', $newtimestampBgn).":00"; 
					$db_dpis_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
					$cmd = " UPDATE PER_WORK_CYCLEHIS SET	
								  END_DATE='$START_DATE_save_Up',
								  UPDATE_USER=$SESS_USERID, 
								  UPDATE_DATE='$UPDATE_DATE'
								   WHERE PER_ID=$HIDPER_ID AND substr(END_DATE,1,10)='".$HID_END_DATE."'";
					$db_dpis_insert->send_cmd($cmd);
					
					$cmd = " UPDATE PER_WORK_CYCLEHIS SET	
								  START_DATE='$START_DATE_save',
								  END_DATE='$END_DATE_save',
								  WC_CODE='$WC_CODE',
								  UPDATE_USER=$SESS_USERID, 
								  UPDATE_DATE='$UPDATE_DATE'
								   WHERE WH_ID=$HIDWH_ID ";
					$db_dpis->send_cmd($cmd);
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลกำหนดรอบการมาปฏิบัติราชการ [$del_PER_ID : $HIDWH_ID : $del_WC_CODE]");
					$command="";
	}
	
	
	
	if($command=="DELETE"){

				
				$cmd = " select WC_CODE,PER_ID
								 from PER_WORK_CYCLEHIS where WH_ID=$HIDWH_ID";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$del_WC_CODE = $data[WC_CODE];
				$del_PER_ID = $data[PER_ID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกำหนดรอบการมาปฏิบัติราชการ [$del_PER_ID :$HIDWH_ID : $del_WC_CODE ]");

				$cmd = " delete from PER_WORK_CYCLEHIS	 where WH_ID= $HIDWH_ID ";
				$db_dpis->send_cmd($cmd);

				echo "<script>window.location='../admin/es_t0203_workcyclehis_person_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

 	} // end if*/
	
	if($UPD){
		$cmd = "	  select 		g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
										wch.START_DATE,wch.END_DATE,wch.UPDATE_USER,wch.UPDATE_DATE,
										wc.WC_NAME,wch.WC_CODE,a.PER_CARDNO,wch.PER_ID,
										a.POS_ID,a.POEM_ID,a.POEMS_ID,a.POT_ID,f.POSITION_LEVEL
						  from 		PER_WORK_CYCLEHIS wch
						  left join PER_PERSONAL a on(a.PER_ID=wch.PER_ID)
                          left join PER_WORK_CYCLE wc on(wc.WC_CODE=wch.WC_CODE) 
						  left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
						  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						  where 		wch.WH_ID='$HIDWH_ID'
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$PER_CARDNO = $data[PER_CARDNO];
		$WC_NAME = $data[WC_NAME];
		$WC_CODE = $data[WC_CODE];
		$TIME_START = show_date_format(substr($data[START_DATE],0,10), 1);
		$WC_START_HH = substr($data[START_DATE],11,2);
		$WC_START_II = substr($data[START_DATE],14,2);
		$HID_END_DATE = substr($data[START_DATE],0,10);
		
		$TIME_END = "";
		$WC_END_HH = "00";
		$WC_END_II = "00";
		if($data[END_DATE]){
			$TIME_END = show_date_format(substr($data[END_DATE],0,10), 1);
			$WC_END_HH = substr($data[END_DATE],11,2);
			$WC_END_II = substr($data[END_DATE],14,2);
		}
		
		$HIDPER_ID = $data[PER_ID];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		
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
		
	} // end if
	
	if( (!$UPD && !$DEL) ){
		$TIME_START = "";
		$TIME_END = "";
		$WC_NAME = "";
		$WC_CODE = "";
		$HIDWH_ID = "";	
		$HIDPER_ID = "";	
		$HideID = "";	
		$WC_START_HH = "";	
		$WC_START_II = "";	
		$WC_END_HH = "";	
		$WC_END_II = "";	
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
	
  
?>