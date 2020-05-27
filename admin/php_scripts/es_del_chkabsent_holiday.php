
<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_add = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){								
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
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
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case
	
		$UPDATE_DATE = date("Y-m-d H:i:s");
		
		
		$HOL_DATE_save=$HidHOL_DATE;
		$HOL_NAME_save=$HidHOL_NAME;
		
		$HOL_DATE_arr = explode("-", $HidHOL_DATE);
		$HOL_DATE_chk = $HOL_DATE_arr[0].$HOL_DATE_arr[1].$HOL_DATE_arr[2];
	
		//ยืนยันบันทึกปรับลดวันและบันทึกปฏิทิน
  		if($chk_confirm==1){

			// ข้อมูลการลา
			$cmd = " select 	ex.ABS_ID,ex.PER_ID,ex.ABS_STARTDATE ,ex.ABS_ENDDATE,ex.ABS_STARTPERIOD,ex.ABS_ENDPERIOD,ex.ABS_DAY,at.AB_COUNT
					from  PER_ABSENT ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
					WHERE '$HOL_DATE_save'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE AND ex.CANCEL_FLAG=0  ";

			$count_page_data = $db_dpis->send_cmd($cmd);
            if($count_page_data){
            	$data_count =0;
                while ($data = $db_dpis->get_array()) {
					
					$ArrABS_STARTDATE = explode('-',trim($data[ABS_STARTDATE]));
					$ABS_STARTDATE_chk = $ArrABS_STARTDATE[0].$ArrABS_STARTDATE[1].$ArrABS_STARTDATE[2];
					
					$ArrABS_ENDDATE = explode('-',trim($data[ABS_ENDDATE]));
					$ABS_ENDDATE_chk = $ArrABS_ENDDATE[0].$ArrABS_ENDDATE[1].$ArrABS_ENDDATE[2];

					if($HOL_DATE_chk==$ABS_ENDDATE_chk){
						if($data[ABS_ENDPERIOD]==3){
							$BlowABS_DAY = $data[ABS_DAY] + 1;
						}else{
							$BlowABS_DAY = $data[ABS_DAY] + 0.5;
						}
						
					}elseif($HOL_DATE_chk==$ABS_STARTDATE){
						if($data[ABS_STARTPERIOD]==3){
							$BlowABS_DAY = $data[ABS_DAY] + 1;
						}else{
							$BlowABS_DAY = $data[ABS_DAY] + 0.5;
						}
						
					}else{
						$BlowABS_DAY = $data[ABS_DAY] + 1;
					}
					
					if($data[AB_COUNT]==2){ // เป็นประเภทการลาที่ไม่นับวันหยุดจึงลดจำนวนวันให้
						$cmd = "UPDATE PER_ABSENT SET ABS_DAY=$BlowABS_DAY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' WHERE ABS_ID =".$data[ABS_ID];
						$db_dpis_add->send_cmd($cmd); 
					
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปฏิทินวันหยุด ปรับลดจำนวนวัน (ข้อมูลการลา) [$value : $ABS_ID : $HOL_DATE_save]");
					}
					
				}
			}
			
			// ข้อมูลประวัติการลา
			$cmd = " select 	ex.ABS_ID,ex.PER_ID,ex.ABS_STARTDATE ,ex.ABS_ENDDATE,ex.ABS_STARTPERIOD,ex.ABS_ENDPERIOD,ex.ABS_DAY,at.AB_COUNT
					from  PER_ABSENTHIS ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
					WHERE '$HOL_DATE_save'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE  ";

			$count_page_data = $db_dpis->send_cmd($cmd);
            if($count_page_data){
            	$data_count =0;
                while ($data = $db_dpis->get_array()) {
					
					$ArrABS_STARTDATE = explode('-',trim($data[ABS_STARTDATE]));
					$ABS_STARTDATE_chk = $ArrABS_STARTDATE[0].$ArrABS_STARTDATE[1].$ArrABS_STARTDATE[2];
					
					$ArrABS_ENDDATE = explode('-',trim($data[ABS_ENDDATE]));
					$ABS_ENDDATE_chk = $ArrABS_ENDDATE[0].$ArrABS_ENDDATE[1].$ArrABS_ENDDATE[2];
					
					if($HOL_DATE_chk==$ABS_ENDDATE_chk){
						if($data[ABS_ENDPERIOD]==3){
							$BlowABS_DAY = $data[ABS_DAY] + 1;
						}else{
							$BlowABS_DAY = $data[ABS_DAY] + 0.5;
						}
						
					}elseif($HOL_DATE_chk==$ABS_STARTDATE){
						if($data[ABS_STARTPERIOD]==3){
							$BlowABS_DAY = $data[ABS_DAY] + 1;
						}else{
							$BlowABS_DAY = $data[ABS_DAY] + 0.5;
						}
						
					}else{
						$BlowABS_DAY = $data[ABS_DAY] + 1;
					}
					
					if($data[AB_COUNT]==2){ // เป็นประเภทการลาที่ไม่นับวันหยุดจึงลดจำนวนวันให้
						$cmd = "UPDATE PER_ABSENTHIS SET ABS_DAY=$BlowABS_DAY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' WHERE ABS_ID =".$data[ABS_ID];
						$db_dpis_add->send_cmd($cmd); 
					
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปฏิทินวันหยุด ปรับลดจำนวนวัน (ข้อมูลประวัติการลา) [$value : $ABS_ID : $HOL_DATE_save]");
					}
					
				}
			}
				
            $cmd = "DELETE FROM PER_HOLIDAY where  HOL_DATE ='$HOL_DATE_save' ";
            $db_dpis_add->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$HOL_DATE_save : $HOL_NAME_save]");
			echo "<script type='text/javascript'>parent.refresh_opener('<::>');</script>";
 	} 
	
	//ไม่บันทึกปรับลดวัน บันทึกเฉพาะปฏิทิน
	if($chk_confirm==2){
            $cmd = "DELETE FROM PER_HOLIDAY where  HOL_DATE ='$HOL_DATE_save' ";
            $db_dpis_add->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$HOL_DATE_save : $HOL_NAME_save]");
				
			echo "<script type='text/javascript'>parent.refresh_opener('<::>');</script>";
 	} 
	

  
?>