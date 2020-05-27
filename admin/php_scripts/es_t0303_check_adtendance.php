<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE"){
		
		if($chk_today==3){
			$strStartDate = $HIDTIME_STAMP;
			$Save_TIME_ADJUST = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)))." ".$WC_START_HH.":".$WC_START_II.":00";
		}elseif($chk_today==2){
			$strStartDate = $HIDTIME_STAMP;
			$Save_TIME_ADJUST = date ("Y-m-d", strtotime("-1 day", strtotime($strStartDate)))." ".$WC_START_HH.":".$WC_START_II.":00";
		}else{
			$Save_TIME_ADJUST = $HIDTIME_STAMP." ".$WC_START_HH.":".$WC_START_II.":00";
			
		}
			
		$cmd = " UPDATE PER_TIME_ATTENDANCE	SET
					    TIME_ADJUST=to_date('".$Save_TIME_ADJUST."','yyyy-mm-dd hh24:mi:ss'),
						ADJUST_USER=$SESS_USERID, ADJUST_DATE=sysdate
						WHERE  PER_ID=$HIDPER_ID AND  TIME_STAMP=to_date('".$HIDTIME_STAMP." ".$HIDTIME."','yyyy-mm-dd hh24:mi:ss')";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลเวลาการมาปฏิบัติราชการ [$HIDPER_ID : ".$HIDTIME_STAMP." ".$HIDTIME."]");
			
			$command = "";	
		
	}
	

	if($UPD){
		$cmd = "	  select 		g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
										a.POS_ID,a.POEM_ID,a.POEMS_ID,a.POT_ID,
										f.POSITION_LEVEL,a.PER_CARDNO,
										TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP,
										TO_CHAR(att.TIME_ADJUST,'yyyy-mm-dd') AS DATE_ADJUST,
										TO_CHAR(att.TIME_ADJUST,'HH24:MI') AS TIME_ADJUST,
										TO_CHAR(att.TIME_STAMP,'yyyymmdd') AS CHKTIME_STAMP,
										TO_CHAR(att.TIME_ADJUST,'yyyymmdd') AS CHKDATE_ADJUST,
										cl.WC_NAME,
										att.ADJUST_USER, TO_CHAR(att.ADJUST_DATE,'yyyy-mm-dd') AS ADJUST_DATE,
										TO_CHAR(att.ADJUST_DATE,'HH24:MI') AS ADJUST_TIME
						  from 		PER_TIME_ATTENDANCE att
						  left join per_personal a on(a.PER_ID=att.PER_ID) 
						  left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
						  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						  left join PER_WORK_CYCLEHIS cyh on(cyh.PER_ID=att.PER_ID   
								AND (att.TIME_STAMP
								between to_date(cyh.START_DATE,'yyyy-mm-dd hh24:mi:ss')  
								  AND case when cyh.END_DATE is not null then to_date(cyh.END_DATE,'yyyy-mm-dd hh24:mi:ss') 
								else sysdate end ))
                    		left join PER_WORK_CYCLE cl on(cl.WC_CODE=cyh.WC_CODE) 
						  where 		a.PER_ID=$HIDPER_ID
						       AND att.TIME_STAMP=to_date('".$HIDTIME_STAMP." ".$HIDTIME."','yyyy-mm-dd hh24:mi:ss')  
						  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$PER_CARDNO = $data[PER_CARDNO];
		$TIME_STAMP_SHOW = show_date_format(trim($HIDTIME_STAMP), $DATE_DISPLAY)." ".$HIDTIME;
		$WC_START_HH = substr($data[TIME_ADJUST],0,2);
		$WC_START_II = substr($data[TIME_ADJUST],3,2);
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID]; 
		$POEMS_ID =$data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$LEVEL_NAME = $data[POSITION_LEVEL];
		
		if($data[WC_NAME]){
			$WC_NAME=$data[WC_NAME];
		}else{
			$WC_NAME="<font color='red'>ยังไม่ได้กำหนด</font>";
		}
		
		
		if($data[CHKDATE_ADJUST]>$data[CHKTIME_STAMP]){
			$chk_today= 3;
		}else if(($data[CHKDATE_ADJUST]<$data[CHKTIME_STAMP]) && ($data[CHKDATE_ADJUST]!="")){
			$chk_today= 2;
		}else{
			$chk_today= 1;
		}
		
		
		
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
		if($OT_ORG_LOWER3){
			$OT_ORG=$OT_ORG_LOWER3;
		}elseif($OT_ORG_LOWER2){
			$OT_ORG=$OT_ORG_LOWER2;
		}elseif($OT_ORG_LOWER1){
			$OT_ORG=$OT_ORG_LOWER1;
		}elseif($OT_DEPARTMENT_ID){
			$OT_ORG=$OT_DEPARTMENT_ID;
		}elseif($OT_ORG_ID){
			$OT_ORG=$OT_ORG_ID;
		}
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OT_ORG ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$OT_ORG_NAME = trim($data_dpis2[ORG_NAME]);
		/*----------------------------------------------------------------------*/
		
		
		$UPDATE_USER = $data[ADJUST_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[ADJUST_DATE]), $DATE_DISPLAY)." ".$data[ADJUST_TIME];
		
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);
		
	} // end if
	
	
if($command=="DELETE"){
		$cmd = " UPDATE PER_TIME_ATTENDANCE	SET
					    TIME_ADJUST=NULL
						WHERE  PER_ID=$HIDPER_ID AND  TIME_STAMP=to_date('".$HIDTIME_STAMP." ".$HIDTIME."','yyyy-mm-dd hh24:mi:ss')";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลเวลาการมาปฏิบัติราชการ [$HIDPER_ID : ".$HIDTIME_STAMP." ".$HIDTIME."]");
			
			/*echo "<script>window.location='../admin/es_t0303_check_adtendance.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";*/
		$command = "";	
	}
	
	
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$HIDPER_ID = "";
		$HIDTIME_STAMP = "";
		$WC_START_HH = "00";
		$WC_START_II = "00";	
	} // end if
?>