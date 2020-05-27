<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");			

    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_img = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
        

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

			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;

			break;
	} // end switch case

	$cmd = "select 		att.PICTUREDATA,att.TA_CODE,att.AUTHTYPE, tat.TA_NAME ,
						TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP1,
						TO_CHAR(att.TIME_STAMP,'HH24:MI:SS')  AS ATT_STARTTIME,
						TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd HH24:MI:SS') AS TIME_STAMP
						 from	 TA_UNKNOWN_PIC att
						 left join PER_TIME_ATT tat on(tat.TA_CODE=att.TA_CODE) 
						 where  TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$TIME_STAMP' and att.TA_CODE=$TA_CODE  and att.AUTHTYPE=$AUTHTYPE ";
	
	$db_dpis_img->send_cmd($cmd);
	$data_img = $db_dpis_img->get_array();
	if($data_img['PICTUREDATA']){ 
		$img= $data_img['PICTUREDATA']->load();
	}else { 
		$imgShow="<font color='red'><strong>ไม่พบข้อมูลรูปภาพ</strong></font>";
	}
	
	$DATA_TIME_STAMP = $data_img[TIME_STAMP];
	
	$TIME_STAMP_STR = "";
	if ($data_img[TIME_STAMP1]) {
		$TIME_STAMP_STR  = substr($data_img[TIME_STAMP1],8,2)."/".substr($data_img[TIME_STAMP1],5,2)."/".(substr($data_img[TIME_STAMP1],0,4)+543);
	}
	
	$TIME_STAMP1 = $data_img[TIME_STAMP1];
	
	$DATA_att_starttime = "";
	if ($data_img[ATT_STARTTIME]) { 
		$DATA_att_starttime = "(".$data_img[ATT_STARTTIME].")";
	}
	
	$DATA_TA_NAME = $data_img[TA_NAME];
	
	if($data_img[AUTHTYPE]=="0"){
		$DATA_AUTHTYPE = "นิ้ว";
	}elseif($data_img[AUTHTYPE]=="3"){
		$DATA_AUTHTYPE = "บัตร";
	}else{
		$DATA_AUTHTYPE = "หน้า";
	}
	

        
        /*----------------------------------------------------------Begin-----------------------------------------------------------*/

        if($command == "ADD"){
			
			$val_PER_ID = $PER_ID;
			$cmd = " select PER_ID from PER_TIME_ATTENDANCE 
							where PER_ID=".$val_PER_ID." and TO_CHAR(TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$HIDTIME_STAMP' and TA_CODE=$HIDTA_CODE  and AUTHTYPE=$HIDAUTHTYPE ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {	
						$cmd = "select PER_ID  from	TA_UNKNOWN_PIC
						 where  TO_CHAR(TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$HIDTIME_STAMP' and TA_CODE=$HIDTA_CODE  and AUTHTYPE=$HIDAUTHTYPE ";
	
						$db_dpis_img->send_cmd($cmd);
						$data_img = $db_dpis_img->get_array();
						$del_PER_ID=$data_img['PER_ID'];
						
						if($del_PER_ID){
							$cmd =" DELETE FROM PER_TIME_ATTENDANCE where PER_ID=$del_PER_ID and TO_CHAR(TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$HIDTIME_STAMP' and TA_CODE=$HIDTA_CODE  and AUTHTYPE=$HIDAUTHTYPE ";
							$db_dpis->send_cmd($cmd);
						}

						$cmd ="INSERT INTO PER_TIME_ATTENDANCE (PER_ID,TIME_STAMP,TA_CODE,AUTHTYPE,ADJUST_USER,ADJUST_DATE,PICTUREDATA,RECORD_BY)
							select  ".$val_PER_ID.",TIME_STAMP,TA_CODE,AUTHTYPE,".$SESS_USERID.",sysdate,PICTUREDATA,1
							from TA_UNKNOWN_PIC where TO_CHAR(TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$HIDTIME_STAMP' and TA_CODE=$HIDTA_CODE  and AUTHTYPE=$HIDAUTHTYPE ";
						$db_dpis->send_cmd($cmd);
						
						$cmd ="UPDATE  TA_UNKNOWN_PIC SET PER_ID=".$val_PER_ID.",UPDATE_USER=".$SESS_USERID.",UPDATE_DATE=sysdate where TO_CHAR(TIME_STAMP,'yyyy-mm-dd HH24:MI:SS')='$HIDTIME_STAMP' and TA_CODE=$HIDTA_CODE  and AUTHTYPE=$HIDAUTHTYPE ";
						$db_dpis->send_cmd($cmd);
						
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลการลงเวลาที่ไม่สำเร็จ [".$val_PER_ID." : ".$HIDTIME_STAMP." : ".$HIDTA_CODE." : ".$HIDAUTHTYPE."]");
						echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว'); parent.refresh_opener();</script>";
			
			}else{
				 echo "<script>window.location='../admin/es_t0304_unknown_picshow.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2;&TIME_STAMP=$HIDTIME_STAMP&TA_CODE=$HIDTA_CODE&AUTHTYPE=$HIDAUTHTYPE&err_text=1'</script>";
			}
			

			$command = "";

        }
		
	
		
		


?>