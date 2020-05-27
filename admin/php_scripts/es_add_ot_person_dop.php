
<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis5 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	
		
		
		//หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
		$cmd4 = "	select	 b.CODE from	user_detail a, user_group b
						where a.group_id=b.id AND a.ID=".$SESS_USERID;
		$db_dpis2->send_cmd($cmd4);
		$data4 = $db_dpis2->get_array();
		if ($data4[CODE]) {
			$NAME_GROUP_HRD = $data4[CODE];
		}else{
			$NAME_GROUP_HRD = "";
		}
	
		$UPDATE_DATE = date("Y-m-d H:i:s");
		$TOs_DATE = date("Y-m-d ");
		
		$YMD_TIME_START = $TIME_START;
		$YMD_TIME_END = $TIME_END;
		
		/*หาจำนวนมีกี่วัน*/
		$date1 = $YMD_TIME_START.' 00:00:00';
		$date2 = $YMD_TIME_END.' 23:59:59';
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);
		$seconds_diff = $ts2 - $ts1;
		$cntday=(floor($seconds_diff/(60*60*24))+1);
		
		
				
	if($command=="SCRIPT"){
			$HideID_save= explode(",",$HideID);
			foreach ($HideID_save as $value) {
				
				//ข้อมูลบุคลากร
				$cmd = "	  select 		PER_CARDNO,PER_TYPE
						  from 		PER_PERSONAL
						  where 		PER_ID='$value'
						  ";

				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$PER_TYPE_save = $data[PER_TYPE];
				$PER_CARDNO_save = $data[PER_CARDNO];
				
				//--------------------------------------------
				

				$cmd = " select  OT_DATE from TA_PER_OT 
								WHERE OT_DATE='$YMD_TIME_START'  AND PER_ID=$value  ";
				$count_duplicate = $db_dpis->send_cmd($cmd);
				if($count_duplicate <= 0){
					//เช็ควันหยุด
					$timestamp = strtotime($YMD_TIME_START); //y-m-d
					$day = date('D', $timestamp);
					if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){
						 //หาวันตามปฏิทินวันหยุด ถ้าไม่มีค่อยบันทึก
						$cmd = " select  HOL_DATE from PER_HOLIDAY 
								 where trim(HOL_DATE)='$YMD_TIME_START'";
						$count_duplicate = $db_dpis->send_cmd($cmd);
						if($count_duplicate <= 0){
							$HOLYDAY_FLAG_save=0;
						}else{
							$HOLYDAY_FLAG_save=1;
						}
					}else{
						$HOLYDAY_FLAG_save=1;
					}
					// ORG_LOWER1,ORG_LOWER2,ORG_LOWER3, 
					
					if(($SESS_USERGROUP==1) || ($NAME_GROUP_HRD=='HRD') ){
						$save_org_id="-1";
						$save_department_id=$search_department_id;
						$save_ORG_LOWER1 = "-1";
					}else{
					
						// หา หน่วยงานตามหมอบหมายงาน สำนัก/กอง 
						/*$cmd2 = " select org.ORG_ID,org.ORG_ID_REF from PER_PERSONAL pnl
								LEFT JOIN PER_ORG_ASS org on(org.ORG_ID=pnl.ORG_ID)
								 where pnl.PER_ID=$SESS_PER_ID"; */
						 $cmd2 = " select POS_ID,POEM_ID,POEMS_ID,POT_ID,PER_OT_FLAG ,ORG_ID from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
						$db_dpis2->send_cmd($cmd2);
						$data2 = $db_dpis2->get_array();
						$PER_POS_ID = $data2[POS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
						$PER_POEM_ID = $data2[POEM_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
						$PER_POEMS_ID = $data2[POEMS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
						$PER_POT_ID = $data2[POT_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
						$PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
						$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*รหัสเจ้าของ OT ตามกฏหมาย*/
						if($PER_POS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
							$fromTable = "PER_POSITION";
							$whereTable = " c.POS_ID=$PER_POS_ID";
						}elseif($PER_POEM_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
							$fromTable = "PER_POS_EMP";
							$whereTable = " c.POEM_ID=$PER_POEM_ID";
						}elseif($PER_POEMS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
							$fromTable = "PER_POS_EMPSER";
							$whereTable = " c.POEMS_ID=$PER_POEMS_ID";
						}elseif($PER_POT_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
							$fromTable = "PER_POS_TEMP";
							$whereTable = " c.POT_ID=$PER_POT_ID";
						}

						if($P_OTTYPE_ORGANIZE==2){	
					
							$save_department_id=$search_department_id;
							if($PER_ORG_ID){
								$save_org_id= $PER_ORG_ID; // สำนัก/กอง
							}else{
								$save_org_id="-1";
							}
							
							if($PER_OT_FLAG==$PER_ORG_ID){
								$save_ORG_LOWER1 = "-1";
							}else{
								$save_ORG_LOWER1 = $PER_OT_FLAG;
							}
							
						}else{
						
							$cmd3 = " select g.ORG_ID,g.ORG_ID_REF
							   from $fromTable c 
							   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
							   where $whereTable "; 
							$db_dpis2->send_cmd($cmd3);
							$data2 = $db_dpis2->get_array();
							if($data2[ORG_ID]){
								$save_org_id= $data2[ORG_ID]; // สำนัก/กอง
								$save_department_id= $data2[ORG_ID_REF]; // กรม
							}else{
								$save_org_id="-1";
								$save_department_id=$search_department_id;
							}
							
							if($PER_OT_FLAG==$data2[ORG_ID]){
								$save_ORG_LOWER1 = "-1";
							}else{
								$save_ORG_LOWER1 = $PER_OT_FLAG;
							}
						}
					}
					
					/*เพิ่มคำนวณเวลา OT*/
				
					$cmd2 = "	  select 		w.PER_ID,TO_CHAR(w.APV_ENTTIME,'HH24MI') as APV_ENTTIME,
								  TO_CHAR(w.APV_EXITTIME,'HH24MI') as APV_EXITTIME,c.WC_END,c.WC_START,
								  TO_CHAR(w.SCAN_EXITTIME,'HH24MI') as SCAN_EXITTIME,
								  TO_CHAR(w.SCAN_ENTTIME,'HH24MI') as SCAN_ENTTIME,w.WC_CODE
							  from 		PER_WORK_TIME w
							  left join PER_WORK_CYCLE c on(c.WC_CODE=w.WC_CODE)
							  where 		w.PER_ID=$value and TO_CHAR(w.WORK_DATE,'yyyy-mm-dd')='$YMD_TIME_START'";
	
					$db_dpis2->send_cmd($cmd2);
					//$db_dpis->show_error();
					$data2 = $db_dpis2->get_array();
					if($data2[PER_ID]){
						$WC_CODE = $data2[WC_CODE];
						if($data2[APV_ENTTIME]){
							$APV_ENTTIME = $data2[APV_ENTTIME];
							$APV_EXITTIME = $data2[APV_EXITTIME];
						}else{
							$APV_ENTTIME = $data2[SCAN_ENTTIME];
							$APV_EXITTIME = $data2[SCAN_EXITTIME];
						}
						
						if($HOLYDAY_FLAG_save==0){ /*วันทำงาน*/
						
							// เวลาเริ่มต้นทำก่อนทำงาน
							if($OT_STATUS == "1"  || $OT_STATUS == "3"){
	
								if($APV_ENTTIME){
									$START_TIME_BFW = $APV_ENTTIME;
									
								}else{
										$START_TIME_BFW = "0000";
								}
								
								if( ($APV_ENTTIME && $data2[WC_START])  &&  ($APV_ENTTIME < $data2[WC_START]) ){
									$END_TIME_BFW = $data2[WC_START];
								}elseif( ($APV_ENTTIME && $data2[WC_START]=="") && ($APV_ENTTIME<='0830') ){
									$END_TIME_BFW = "0830";
								}else{
										$END_TIME_BFW = "0000";
									
								}
								
								if($OT_STATUS == "1"){
									$START_TIME_CHK = "0000";
									$END_TIME_CHK = "0000";
									
								}
							}
							
							// เวลาเริ่มต้นทำหลังทำงาน
							if($OT_STATUS == "2"  || $OT_STATUS == "3"){
								
								if( ($APV_ENTTIME && $data2[WC_END])  &&  ($APV_ENTTIME>$data2[WC_END]) ){
									$START_TIME_CHK = $APV_ENTTIME;
								}elseif( ($APV_ENTTIME && $data2[WC_END])  &&  ($APV_ENTTIME<=$data2[WC_END]) ){
									$START_TIME_CHK = $data2[WC_END];
								}elseif(  ($APV_ENTTIME && $data2[WC_END]=="")  &&  ($APV_ENTTIME>'1630') ){
									$START_TIME_CHK = $APV_ENTTIME;
								}elseif( ($APV_ENTTIME && $data2[WC_END]=="") && ($APV_ENTTIME<='1630') ){
									$START_TIME_CHK = "1630";
								}else{
									//ปรับปรุงโดย กิตติภัทร์ 3/01/62
									if($APV_ENTTIME =="" && $APV_EXITTIME){
										if($data2[WC_END]){
											$START_TIME_CHK = $data2[WC_END];
										}else{
											$START_TIME_CHK = "1630";
										}
									}else{
										$START_TIME_CHK = "0000";
									}
									
								}
								
								if($APV_EXITTIME){
									$END_TIME_CHK= $APV_EXITTIME;
								}else{
										$END_TIME_CHK = "0000";
								}
								
								if($OT_STATUS == "2"){
									$START_TIME_BFW = "0000";
									$END_TIME_BFW = "0000";
								}
								
							}
							
						}else{ /*วันหยุด*/
							$START_TIME_CHK = $APV_ENTTIME;
							$END_TIME_CHK = $APV_EXITTIME;
							$START_TIME_BFW = "0000";
							$END_TIME_BFW = "0000";
							
						}
						
						
					}else{
						$START_TIME_CHK = "0000";
						$END_TIME_CHK = "0000";
						$START_TIME_BFW = "0000";
						$END_TIME_BFW = "0000";
					}
					
					
					if(($START_TIME_CHK =="0000") && ($END_TIME_CHK =="0000") && ($START_TIME_BFW =="0000") && ($END_TIME_BFW =="0000") ){
						$NUM_HRS_save= "NULL";
						$AMOUNT_save = "NULL";
					}else{
						
						// คำนวณเวลาก่อนทำงาน
						if(($START_TIME_BFW !="0000") && ($END_TIME_BFW !="0000")){
							/* คำนวณชั่วโมง*/
							$START_TIME_tmp_AFW = $START_TIME_BFW;
							$END_TIME_tmp_AFW = $END_TIME_BFW;
							
							/*วันทำงาน*/
							if($HOLYDAY_FLAG_save==0){
				
								// จำนวนชั่วโมงวันทำงาน
								$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
								$db_dpis->send_cmd($cmd);
								$data = $db_dpis->get_array();
								$TMP_P_FULLTIME = $data[CONFIG_VALUE];
								
								$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
								 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME / 2)){
									$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
									$db_dpis->send_cmd($cmd);
									$data = $db_dpis->get_array();
									$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
									
									$tmp_NUM_HRS_B_BFW = $TMP_P_OTWORKDAY;
									$tmp_NUM_HRS_E_BFW = 0;
								 }else{
									if($START_TIME_tmp_AFW<='1159'){
								
										if($END_TIME_tmp_AFW<='1159'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										}elseif($END_TIME_tmp_AFW>='1200' && $END_TIME_tmp_AFW<='1259'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										}else{
											$tmp_NUM_HRS_B_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
											$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
										}
										
									}elseif($START_TIME_tmp_AFW>='1200' && $START_TIME_tmp_AFW<='1259'){
										
										if($END_TIME_tmp_AFW<='1259'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = 0;
										}else{
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
										}
										
									}else{
										
										$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										$tmp_NUM_HRS_E_BFW = 0;
									}
								 }
									
							}else{
								// จำนวนชั่วโมงวันทำงาน
								$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
								$db_dpis->send_cmd($cmd);
								$data = $db_dpis->get_array();
								$TMP_P_FULLTIME = $data[CONFIG_VALUE];
								
								$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
								 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
									$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
									$db_dpis->send_cmd($cmd);
									$data = $db_dpis->get_array();
									$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
									
									$tmp_NUM_HRS_B_BFW = $TMP_P_OTWEEKEND;
									$tmp_NUM_HRS_E_BFW = 0;
								 }else{
									if($APV_ENTTIME<='1159'){
									
										if($APV_EXITTIME<='1159'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										}elseif($APV_EXITTIME>='1200' && $APV_EXITTIME<='1259'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										}else{
											//$tmp_NUM_HRS_B_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
											//$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
											$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ))-1;
											$tmp_NUM_HRS_E_BFW = 0;
										}
										
									}elseif($APV_ENTTIME>='1200' && $APV_ENTTIME<='1259'){
										
										if($APV_EXITTIME<='1259'){
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = 0;
										}else{
											$tmp_NUM_HRS_B_BFW = 0;
											$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
										}
										
									}else{
										
										$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										$tmp_NUM_HRS_E_BFW = 0;
									}
								}
								
							}
						}else{
							$tmp_NUM_HRS_B_BFW = 0;
							$tmp_NUM_HRS_E_BFW = 0;
						} // end if(($START_TIME_BFW =="0000") && ($END_TIME_BFW =="0000")){
						
						// คำนวณเวลาหลังเลิกทำงาน
						if(($START_TIME_CHK !="0000") && ($END_TIME_CHK !="0000")){
							/* คำนวณชั่วโมง*/
							$START_TIME_tmp = $START_TIME_CHK;
							$END_TIME_tmp = $END_TIME_CHK;
							
							/*วันทำงาน*/
							if($HOLYDAY_FLAG_save==0){
				
								// จำนวนชั่วโมงวันทำงาน
								$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
								$db_dpis->send_cmd($cmd);
								$data = $db_dpis->get_array();
								$TMP_P_FULLTIME = $data[CONFIG_VALUE];
								
								$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
								 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME / 2)){
									$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
									$db_dpis->send_cmd($cmd);
									$data = $db_dpis->get_array();
									$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
									
									$tmp_NUM_HRS_B = $TMP_P_OTWORKDAY;
									$tmp_NUM_HRS_E = 0;
								 }else{
									if($START_TIME_tmp<='1159'){
								
										if($END_TIME_tmp<='1159'){
											//echo "x1=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
	
											$tmp_NUM_HRS_B = 0;
											$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
										}elseif($END_TIME_tmp>='1200' && $END_TIME_tmp<='1259'){
											//echo "x2=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
	
											$tmp_NUM_HRS_B = 0;
											$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
										}else{
											
	
											$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
											$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
											//echo "x3=".$END_TIME_tmp.'|'.$START_TIME_tmp."<>tmp_NUM_HRS_B=".$tmp_NUM_HRS_B."<>tmp_NUM_HRS_E=".$tmp_NUM_HRS_E."<br>";
										}
										
									}elseif($START_TIME_tmp>='1200' && $START_TIME_tmp<='1259'){
										
										if($END_TIME_tmp<='1259'){
											//echo "x4=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
	
											$tmp_NUM_HRS_B = 0;
											$tmp_NUM_HRS_E = 0;
										}else{
											//echo "x5=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
	
											$tmp_NUM_HRS_B = 0;
											$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
										}
										
									}else{
										//echo "x6=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
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
								
								//ปรับปรุงโดย กิตติภัทร์ 3/01/62
						    	if($END_TIME_tmp && $START_TIME_tmp){ 
										$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
										 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
											$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
											$db_dpis->send_cmd($cmd);
											$data = $db_dpis->get_array();
											$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
											
											$tmp_NUM_HRS_B = $TMP_P_OTWEEKEND;
											$tmp_NUM_HRS_E = 0;
										 }else{
											if($APV_ENTTIME<='1159'){
											
												if($APV_EXITTIME<='1159'){
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
												}elseif($APV_EXITTIME>='1200' && $APV_EXITTIME<='1259'){
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
												}else{
													//$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
													//$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
													$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ))-1;
													$tmp_NUM_HRS_E = 0;
												}
												
											}elseif($APV_ENTTIME>='1200' && $APV_ENTTIME<='1259'){
												
												if($APV_EXITTIME<='1259'){
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
										
								}else{  //ปรับปรุงโดย กิตติภัทร์ 3/01/62
									$tmp_NUM_HRS_B = 0;
									$tmp_NUM_HRS_E = 0;
								
								}
								
							}
						}else{
							$tmp_NUM_HRS_B = 0;
							$tmp_NUM_HRS_E = 0;
						} // end if(($START_TIME_CHK =="0000") && ($END_TIME_CHK =="0000")){
						
						
						$tmp_NUM_HRS=$tmp_NUM_HRS_B + $tmp_NUM_HRS_E + $tmp_NUM_HRS_B_BFW + $tmp_NUM_HRS_E_BFW;
						
						/*วันทำงาน*/
						if($HOLYDAY_FLAG_save==0){
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
					// echo "1PER_ID=".$value."||".$NUM_HRS_save ."||".$AMOUNT_save."</br>";
					
					/*--------------------------------------------------จบ OT*/
					if($START_TIME_CHK=='0000'){
						$START_TIME_CHK_Save="";
					}else{
						$START_TIME_CHK_Save=$START_TIME_CHK;
					}
					
					if($END_TIME_CHK=='0000'){
						$END_TIME_CHK_Save="";
					}else{
						$END_TIME_CHK_Save=$END_TIME_CHK;
					}
					
					if($START_TIME_CHK>=$END_TIME_CHK){
						$START_TIME_CHK_Save="";
						$END_TIME_CHK_Save="";
						
					}
					
					//-------------------------------------------------
					if($START_TIME_BFW=='0000'){
						$START_TIME_BFW_Save="";
					}else{
						$START_TIME_BFW_Save=$START_TIME_BFW;
					}
					
					if($END_TIME_BFW=='0000'){
						$END_TIME_BFW_Save="";
					}else{
						$END_TIME_BFW_Save=$END_TIME_BFW;
					}
					
					if($START_TIME_BFW>=$END_TIME_BFW){
						$START_TIME_BFW_Save="";
						$END_TIME_BFW_Save="";
						
					}
					
					//-------------------------------------------------
					
					if($NUM_HRS_save<=0){
						$NUM_HRS_save = "NULL";
					}
					
					if($AMOUNT_save<=0){
						$AMOUNT_save = "NULL";
					}	
					
					//วันทำงาน
					if($HOLYDAY_FLAG_save==0){
						$OT_STATUS_save = $OT_STATUS;
					}else{
						$OT_STATUS_save = 3;
					}
					
					if($WC_CODE == "-1"){
						$NUM_HRS_save = "NULL";
						$AMOUNT_save = "NULL";
						
						$START_TIME_CHK_Save="";
						$END_TIME_CHK_Save="";
						
						$START_TIME_BFW_Save="";
						$END_TIME_BFW_Save="";
						
					}
					
					if($value !=0){
						 $cmd = " insert into TA_PER_OT	
								   (OT_DATE, PER_TYPE, PER_ID, PER_CARDNO, HOLYDAY_FLAG, 
								   ORG_ID,DEPARTMENT_ID,ORG_LOWER1,NUM_HRS,AMOUNT,START_TIME,END_TIME,
								   OT_STATUS,START_TIME_BFW,END_TIME_BFW,
									CREATE_USER,CREATE_DATE,UPDATE_USER, UPDATE_DATE)
						values (
									'$YMD_TIME_START',$PER_TYPE_save,$value,'$PER_CARDNO_save',$HOLYDAY_FLAG_save,
									$save_department_id,$save_org_id,$save_ORG_LOWER1,$NUM_HRS_save,$AMOUNT_save,'$START_TIME_CHK_Save','$END_TIME_CHK_Save',
									$OT_STATUS_save,'$START_TIME_BFW_Save','$END_TIME_BFW_Save',
									$SESS_USERID, '$UPDATE_DATE',$SESS_USERID, '$UPDATE_DATE')   ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกำหนดบุคคลที่ทำ OT [$value : $YMD_TIME_START : $PER_CARDNO]");
					}
                }
				
				
				//-------------------------------------------------------------------------------
				//ทำวันอื่นๆที่เหลือ ตามช่วงวันที่กำหนด
				if($YMD_TIME_START != $YMD_TIME_END){ // ถ้ามากกว่า 1 วันค่อยมาบันทึกส่วนนี้
					$date = $YMD_TIME_START; //mdy
					for($i=1;$i<$cntday;$i++){
						
						$date = strtotime($date);
						$date2 = strtotime("+1 day", $date);
						$date =date('Y-m-d', $date2);
						
						$cmd = " select  OT_DATE from TA_PER_OT 
								WHERE OT_DATE='$date'  AND PER_ID=$value  ";
						$count_duplicate1 = $db_dpis->send_cmd($cmd);
						if($count_duplicate1 <= 0){
						
							$timestamp = strtotime($date); //y-m-d
							$day = date('D', $timestamp);
							if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){
							 //หาวันตามปฏิทินวันหยุด ถ้าไม่มีค่อยบันทึก
								$cmd = " select  HOL_DATE from PER_HOLIDAY 
										 where trim(HOL_DATE)='$date'";
								$count_duplicate = $db_dpis->send_cmd($cmd);
								if($count_duplicate <= 0){
									$HOLYDAY_FLAG_save=0;
								}else{
									$HOLYDAY_FLAG_save=1;
								}
							}else{
								$HOLYDAY_FLAG_save=1;
							}
							// ORG_LOWER1,ORG_LOWER2,ORG_LOWER3, 
							if(($SESS_USERGROUP==1) || ($NAME_GROUP_HRD=='HRD') ){
								$save_org_id="-1";
								$save_department_id=$search_department_id;
								$save_ORG_LOWER1 = "-1";
							}else{
							
								// หา หน่วยงานตามหมอบหมายงาน สำนัก/กอง 
								/*$cmd2 = " select org.ORG_ID,org.ORG_ID_REF from PER_PERSONAL pnl
										LEFT JOIN PER_ORG_ASS org on(org.ORG_ID=pnl.ORG_ID)
										 where pnl.PER_ID=$SESS_PER_ID"; */
								$cmd2 = " select POS_ID,POEM_ID,POEMS_ID,POT_ID,PER_OT_FLAG,ORG_ID from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
								echo "2".$cmd2;
								$db_dpis2->send_cmd($cmd2);
								$data2 = $db_dpis2->get_array();
								$PER_POS_ID = $data2[POS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
								$PER_POEM_ID = $data2[POEM_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
								$PER_POEMS_ID = $data2[POEMS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
								$PER_POT_ID = $data2[POT_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
								$PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
								$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*รหัสเจ้าของ OT ตามกฏหมาย*/
								if($PER_POS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
									$fromTable = "PER_POSITION";
									$whereTable = " c.POS_ID=$PER_POS_ID";
								}elseif($PER_POEM_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
									$fromTable = "PER_POS_EMP";
									$whereTable = " c.POEM_ID=$PER_POEM_ID";
								}elseif($PER_POEMS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
									$fromTable = "PER_POS_EMPSER";
									$whereTable = " c.POEMS_ID=$PER_POEMS_ID";
								}elseif($PER_POT_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
									$fromTable = "PER_POS_TEMP";
									$whereTable = " c.POT_ID=$PER_POT_ID";
								}

								if($P_OTTYPE_ORGANIZE==2){	
					
									$save_department_id=$search_department_id;
									if($PER_ORG_ID){
										$save_org_id= $PER_ORG_ID; // สำนัก/กอง
									}else{
										$save_org_id="-1";
									}
									
									if($PER_OT_FLAG==$PER_ORG_ID){
										$save_ORG_LOWER1 = "-1";
									}else{
										$save_ORG_LOWER1 = $PER_OT_FLAG;
									}
									
								}else{
								
									$cmd3 = " select g.ORG_ID,g.ORG_ID_REF
									   from $fromTable c 
									   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
									   where $whereTable "; 
									$db_dpis2->send_cmd($cmd3);
									$data2 = $db_dpis2->get_array();
									if($data2[ORG_ID]){
										$save_org_id= $data2[ORG_ID]; // สำนัก/กอง
										$save_department_id= $data2[ORG_ID_REF]; // กรม
									}else{
										$save_org_id="-1";
										$save_department_id=$search_department_id;
									}
									
									if($PER_OT_FLAG==$data2[ORG_ID]){
										$save_ORG_LOWER1 = "-1";
									}else{
										$save_ORG_LOWER1 = $PER_OT_FLAG;
									}
								}
							
							}
							
							/*เพิ่มคำนวณเวลา OT*/
				
							$cmd2 = "	  select 		w.PER_ID,TO_CHAR(w.APV_ENTTIME,'HH24MI') as APV_ENTTIME,
								  TO_CHAR(w.APV_EXITTIME,'HH24MI') as APV_EXITTIME,c.WC_END,c.WC_START,
								  TO_CHAR(w.SCAN_EXITTIME,'HH24MI') as SCAN_EXITTIME,
								  TO_CHAR(w.SCAN_ENTTIME,'HH24MI') as SCAN_ENTTIME,w.WC_CODE
									  from 		PER_WORK_TIME w
									  left join PER_WORK_CYCLE c on(c.WC_CODE=w.WC_CODE)
									  where 		w.PER_ID=$value and TO_CHAR(w.WORK_DATE,'yyyy-mm-dd')='$date'";
			
							$db_dpis2->send_cmd($cmd2);
							//$db_dpis->show_error();
							$data2 = $db_dpis2->get_array();
							if($data2[PER_ID]){
								$WC_CODE = $data2[WC_CODE];
								if($data2[APV_ENTTIME]){
									$APV_ENTTIME = $data2[APV_ENTTIME];
									$APV_EXITTIME = $data2[APV_EXITTIME];
								}else{
									$APV_ENTTIME = $data2[SCAN_ENTTIME];
									$APV_EXITTIME = $data2[SCAN_EXITTIME];
								}
								
								if($HOLYDAY_FLAG_save==0){ /*วันทำงาน*/
						
									// เวลาเริ่มต้นทำก่อนทำงาน
									if($OT_STATUS == "1"  || $OT_STATUS == "3"){
			
										if($APV_ENTTIME){
											$START_TIME_BFW = $APV_ENTTIME;
											
										}else{
												$START_TIME_BFW = "0000";
										}
										
										if( ($APV_ENTTIME && $data2[WC_START])  &&  ($APV_ENTTIME < $data2[WC_START]) ){
											$END_TIME_BFW = $data2[WC_START];
										}elseif( ($APV_ENTTIME && $data2[WC_START]=="") && ($APV_ENTTIME<='0830') ){
											$END_TIME_BFW = "0830";
										}else{
												$END_TIME_BFW = "0000";
											
										}
										
										if($OT_STATUS == "1"){
											$START_TIME_CHK = "0000";
											$END_TIME_CHK = "0000";
											
										}
									}
									
									// เวลาเริ่มต้นทำหลังทำงาน
									if($OT_STATUS == "2"  || $OT_STATUS == "3"){
										
										if( ($APV_ENTTIME && $data2[WC_END])  &&  ($APV_ENTTIME>$data2[WC_END]) ){
											$START_TIME_CHK = $APV_ENTTIME;
										}elseif( ($APV_ENTTIME && $data2[WC_END])  &&  ($APV_ENTTIME<=$data2[WC_END]) ){
											$START_TIME_CHK = $data2[WC_END];
										}elseif(  ($APV_ENTTIME && $data2[WC_END]=="")  &&  ($APV_ENTTIME>'1630') ){
											$START_TIME_CHK = $APV_ENTTIME;
										}elseif( ($APV_ENTTIME && $data2[WC_END]=="") && ($APV_ENTTIME<='1630') ){
											$START_TIME_CHK = "1630";
										}else{
											
											//ปรับปรุงโดย กิตติภัทร์ 3/01/62
											if($APV_ENTTIME =="" && $APV_EXITTIME){
												if($data2[WC_END]){
													$START_TIME_CHK = $data2[WC_END];
												}else{
													$START_TIME_CHK = "1630";
												}
											}else{
												$START_TIME_CHK = "0000";
											}
											
										}
										
										if($APV_EXITTIME){
											$END_TIME_CHK= $APV_EXITTIME;
										}else{
												$END_TIME_CHK = "0000";
										}
										
										if($OT_STATUS == "2"){
											$START_TIME_BFW = "0000";
											$END_TIME_BFW = "0000";
										}
										
									}
									
								}else{ /*วันหยุด*/
									$START_TIME_CHK = $APV_ENTTIME;
									$END_TIME_CHK = $APV_EXITTIME;
									$START_TIME_BFW = "0000";
									$END_TIME_BFW = "0000";
									
								}
								
								
							}else{
								$START_TIME_CHK = "0000";
								$END_TIME_CHK = "0000";
								$START_TIME_BFW = "0000";
								$END_TIME_BFW = "0000";
							}
							
							
							if(($START_TIME_CHK =="0000") && ($END_TIME_CHK =="0000") && ($START_TIME_BFW =="0000") && ($END_TIME_BFW =="0000") ){
								$NUM_HRS_save= "NULL";
								$AMOUNT_save = "NULL";
							}else{
								
								// คำนวณเวลาก่อนทำงาน
								if(($START_TIME_BFW !="0000") && ($END_TIME_BFW !="0000")){
									/* คำนวณชั่วโมง*/
									$START_TIME_tmp_AFW = $START_TIME_BFW;
									$END_TIME_tmp_AFW = $END_TIME_BFW;
									
									/*วันทำงาน*/
									if($HOLYDAY_FLAG_save==0){
						
										// จำนวนชั่วโมงวันทำงาน
										$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
										$db_dpis->send_cmd($cmd);
										$data = $db_dpis->get_array();
										$TMP_P_FULLTIME = $data[CONFIG_VALUE];
										
										$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME / 2)){
											$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
											$db_dpis->send_cmd($cmd);
											$data = $db_dpis->get_array();
											$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
											
											$tmp_NUM_HRS_B_BFW = $TMP_P_OTWORKDAY;
											$tmp_NUM_HRS_E_BFW = 0;
										 }else{
											if($START_TIME_tmp_AFW<='1159'){
										
												if($END_TIME_tmp_AFW<='1159'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												}elseif($END_TIME_tmp_AFW>='1200' && $END_TIME_tmp_AFW<='1259'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												}else{
													$tmp_NUM_HRS_B_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
													$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
												}
												
											}elseif($START_TIME_tmp_AFW>='1200' && $START_TIME_tmp_AFW<='1259'){
												
												if($END_TIME_tmp_AFW<='1259'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = 0;
												}else{
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
												}
												
											}else{
												
												$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												$tmp_NUM_HRS_E_BFW = 0;
											}
										 }
											
									}else{
										// จำนวนชั่วโมงวันทำงาน
										$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
										$db_dpis->send_cmd($cmd);
										$data = $db_dpis->get_array();
										$TMP_P_FULLTIME = $data[CONFIG_VALUE];
										
										$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
										 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
											$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
											$db_dpis->send_cmd($cmd);
											$data = $db_dpis->get_array();
											$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
											
											$tmp_NUM_HRS_B_BFW = $TMP_P_OTWEEKEND;
											$tmp_NUM_HRS_E_BFW = 0;
										 }else{
											if($APV_ENTTIME<='1159'){
											
												if($APV_EXITTIME<='1159'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												}elseif($APV_EXITTIME>='1200' && $APV_EXITTIME<='1259'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												}else{
													//$tmp_NUM_HRS_B_BFW = floor((strtotime('12:00') - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
													//$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
													$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ))-1;
													$tmp_NUM_HRS_E_BFW = 0;
												}
												
											}elseif($APV_ENTTIME>='1200' && $APV_ENTTIME<='1259'){
												
												if($APV_EXITTIME<='1259'){
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = 0;
												}else{
													$tmp_NUM_HRS_B_BFW = 0;
													$tmp_NUM_HRS_E_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime('13:00'))/  ( 60 * 60 ));
												}
												
											}else{
												
												$tmp_NUM_HRS_B_BFW = floor((strtotime($END_TIME_tmp_AFW) - strtotime($START_TIME_tmp_AFW))/  ( 60 * 60 ));
												$tmp_NUM_HRS_E_BFW = 0;
											}
										}
										
									}
								}else{
									$tmp_NUM_HRS_B_BFW = 0;
									$tmp_NUM_HRS_E_BFW = 0;
								} // end if(($START_TIME_BFW =="0000") && ($END_TIME_BFW =="0000")){
								
								// คำนวณเวลาหลังเลิกทำงาน
								if(($START_TIME_CHK !="0000") && ($END_TIME_CHK !="0000")){
									/* คำนวณชั่วโมง*/
									$START_TIME_tmp = $START_TIME_CHK;
									$END_TIME_tmp = $END_TIME_CHK;
									
									/*วันทำงาน*/
									if($HOLYDAY_FLAG_save==0){
						
										// จำนวนชั่วโมงวันทำงาน
										$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
										$db_dpis->send_cmd($cmd);
										$data = $db_dpis->get_array();
										$TMP_P_FULLTIME = $data[CONFIG_VALUE];
										
										$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
										 if($tmp_NUM_HRS_chk >= ($TMP_P_FULLTIME / 2)){
											$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
											$db_dpis->send_cmd($cmd);
											$data = $db_dpis->get_array();
											$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];
											
											$tmp_NUM_HRS_B = $TMP_P_OTWORKDAY;
											$tmp_NUM_HRS_E = 0;
										 }else{
											if($START_TIME_tmp<='1159'){
										
												if($END_TIME_tmp<='1159'){
													//echo "x1=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
			
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
												}elseif($END_TIME_tmp>='1200' && $END_TIME_tmp<='1259'){
													//echo "x2=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
			
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
												}else{
													
			
													$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
													$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
													//echo "x3=".$END_TIME_tmp.'|'.$START_TIME_tmp."<>tmp_NUM_HRS_B=".$tmp_NUM_HRS_B."<>tmp_NUM_HRS_E=".$tmp_NUM_HRS_E."<br>";
												}
												
											}elseif($START_TIME_tmp>='1200' && $START_TIME_tmp<='1259'){
												
												if($END_TIME_tmp<='1259'){
													//echo "x4=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
			
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = 0;
												}else{
													//echo "x5=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
			
													$tmp_NUM_HRS_B = 0;
													$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
												}
												
											}else{
												//echo "x6=".$END_TIME_tmp.'|'.$START_TIME_tmp."<br>";
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
										//ปรับปรุงโดย กิตติภัทร์ 3/01/62
						    			if($END_TIME_tmp && $START_TIME_tmp){ 
												$tmp_NUM_HRS_chk = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
												 if($tmp_NUM_HRS_chk >= $TMP_P_FULLTIME){
													$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
													$db_dpis->send_cmd($cmd);
													$data = $db_dpis->get_array();
													$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
													
													$tmp_NUM_HRS_B = $TMP_P_OTWEEKEND;
													$tmp_NUM_HRS_E = 0;
												 }else{
													if($APV_ENTTIME<='1159'){
												
														if($APV_EXITTIME<='1159'){
															$tmp_NUM_HRS_B = 0;
															$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
														}elseif($APV_EXITTIME>='1200' && $APV_EXITTIME<='1259'){
															$tmp_NUM_HRS_B = 0;
															$tmp_NUM_HRS_E = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
														}else{
															//$tmp_NUM_HRS_B = floor((strtotime('12:00') - strtotime($START_TIME_tmp))/  ( 60 * 60 ));
															//$tmp_NUM_HRS_E = floor((strtotime($END_TIME_tmp) - strtotime('13:00'))/  ( 60 * 60 ));
															$tmp_NUM_HRS_B = floor((strtotime($END_TIME_tmp) - strtotime($START_TIME_tmp))/  ( 60 * 60 ))-1;
															$tmp_NUM_HRS_E = 0;
														}
														
													}elseif($APV_ENTTIME>='1200' && $APV_ENTTIME<='1259'){
														
														if($APV_EXITTIME<='1259'){
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
										 }else{  //ปรับปรุงโดย กิตติภัทร์ 3/01/62
											$tmp_NUM_HRS_B = 0;
											$tmp_NUM_HRS_E = 0;
										
										}
										
									}
								}else{
									$tmp_NUM_HRS_B = 0;
									$tmp_NUM_HRS_E = 0;
								} // end if(($START_TIME_CHK =="0000") && ($END_TIME_CHK =="0000")){
								
								
								$tmp_NUM_HRS=$tmp_NUM_HRS_B + $tmp_NUM_HRS_E + $tmp_NUM_HRS_B_BFW + $tmp_NUM_HRS_E_BFW;
								
								/*วันทำงาน*/
								if($HOLYDAY_FLAG_save==0){
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
							// echo "1PER_ID=".$value."||".$NUM_HRS_save ."||".$AMOUNT_save."</br>";
							
							/*--------------------------------------------------จบ OT*/
							if($START_TIME_CHK=='0000'){
								$START_TIME_CHK_Save="";
							}else{
								$START_TIME_CHK_Save=$START_TIME_CHK;
							}
							
							if($END_TIME_CHK=='0000'){
								$END_TIME_CHK_Save="";
							}else{
								$END_TIME_CHK_Save=$END_TIME_CHK;
							}
							
							if($START_TIME_CHK>=$END_TIME_CHK){
								$START_TIME_CHK_Save="";
								$END_TIME_CHK_Save="";
								
							}
							
							//-------------------------------------------------
							if($START_TIME_BFW=='0000'){
								$START_TIME_BFW_Save="";
							}else{
								$START_TIME_BFW_Save=$START_TIME_BFW;
							}
							
							if($END_TIME_BFW=='0000'){
								$END_TIME_BFW_Save="";
							}else{
								$END_TIME_BFW_Save=$END_TIME_BFW;
							}
							
							if($START_TIME_BFW>=$END_TIME_BFW){
								$START_TIME_BFW_Save="";
								$END_TIME_BFW_Save="";
								
							}
							
							//-------------------------------------------------
							
							if($NUM_HRS_save<=0){
								$NUM_HRS_save = "NULL";
							}
							
							if($AMOUNT_save<=0){
								$AMOUNT_save = "NULL";
							}	
							
							//วันทำงาน
							if($HOLYDAY_FLAG_save==0){
								$OT_STATUS_save = $OT_STATUS;
							}else{
								$OT_STATUS_save = 3;
							}
							
							if($WC_CODE == "-1"){
								$NUM_HRS_save = "NULL";
								$AMOUNT_save = "NULL";
								
								$START_TIME_CHK_Save="";
								$END_TIME_CHK_Save="";
								
								$START_TIME_BFW_Save="";
								$END_TIME_BFW_Save="";
								
							}	
				
							if($value !=0){
								$cmd = " insert into TA_PER_OT	
										   (OT_DATE, PER_TYPE, PER_ID, PER_CARDNO, HOLYDAY_FLAG, 
										   ORG_ID,DEPARTMENT_ID,ORG_LOWER1,NUM_HRS,AMOUNT,START_TIME,END_TIME,
										   OT_STATUS,START_TIME_BFW,END_TIME_BFW,
											CREATE_USER,CREATE_DATE,UPDATE_USER, UPDATE_DATE)
								values (
											'$date',$PER_TYPE_save,$value,'$PER_CARDNO_save',$HOLYDAY_FLAG_save,
											$save_department_id,$save_org_id,$save_ORG_LOWER1,$NUM_HRS_save,$AMOUNT_save,'$START_TIME_CHK_Save','$END_TIME_CHK_Save',
											$OT_STATUS_save,'$START_TIME_BFW_Save','$END_TIME_BFW_Save',
											$SESS_USERID, '$UPDATE_DATE',$SESS_USERID, '$UPDATE_DATE')   ";
								$db_dpis->send_cmd($cmd);
								insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกำหนดบุคคลที่ทำ OT [$value : $YMD_TIME_START : $PER_CARDNO]");
							}
						}
							
					} // end for($i=1;$i<$cntday;$i++){
				
				} // end if($YMD_START_DATE != $YMD_END_DATE){	
				
			} // end foreach ($HideID_save as $value) {
				
				
			/*echo "<script>window.location='../admin/es_t0205_ot_person_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";*/
		
		echo "<script type='text/javascript'>parent.document.form1.SELECTED_PER_ID.value = '';parent.refresh_opener('1<::>1');</script>";

 	} // end if($command=="SCRIPT"){ 
	
	
	
	
	
  
?>