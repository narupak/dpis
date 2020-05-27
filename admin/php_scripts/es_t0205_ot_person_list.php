
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
		
		
				
  		if($command=="ADD"){
			//$HideID_save= explode(",",$HideID);
                        $HideID_save= explode(",",$SELECTED_PER_ID);
			
			/*หาว่ามีข้อมูลในฐานแล้วหรือไม่*/
				$chkPerId = 0;
			foreach ($HideID_save as $value) {
				
				$cmd = " select  OT_DATE from TA_PER_OT 
								WHERE OT_DATE='$YMD_TIME_START'  AND PER_ID=$value  ";
				$count_duplicate = $db_dpis->send_cmd($cmd);
				if($count_duplicate > 0){
					$chkPerId++;
				}
				
				/*ทำวันอื่นๆที่เหลือ ตามช่วงวันที่กำหนด*/
				if($YMD_TIME_START != $YMD_TIME_END){ /* ถ้ามากกว่า 1 วันค่อยมาบันทึกส่วนนี้*/
					$date = $YMD_TIME_START; /*mdy*/
					for($i=1;$i<$cntday;$i++){
						$date = strtotime($date);
						$date2 = strtotime("+1 day", $date);
						$date =date('Y-m-d', $date2);
						$cmd = " select  OT_DATE from TA_PER_OT 
								WHERE OT_DATE='$date'  AND PER_ID=$value  ";
						$count_duplicate1 = $db_dpis->send_cmd($cmd);
						if($count_duplicate1 > 0){
							$chkPerId++;
						}
				
					}
				}
				
			}
			
			$OpenDialog=0; 
			if($chkPerId>0){
				//$Dup_ID=$HideID;
                $Dup_ID=$SELECTED_PER_ID;
				$Dup_TIME_START=$YMD_TIME_START;
				$Dup_TIME_END=$YMD_TIME_END;
				$Dup_OT_Status=$OT_Status;
			 	$OpenDialog=1;  
				
			}else{

			
				
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
					$cmd2 = " select ORG_ID,POS_ID,POEM_ID,POEMS_ID,POT_ID,PER_OT_FLAG from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
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
						if($OT_Status == "1"  || $OT_Status == "3"){

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
							
							if($OT_Status == "1"){
								$START_TIME_CHK = "0000";
								$END_TIME_CHK = "0000";
								
							}
						}
						
						// เวลาเริ่มต้นทำหลังทำงาน
						if($OT_Status == "2"  || $OT_Status == "3"){
							
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
							
							if($OT_Status == "2"){
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
							 //echo "OK<br>";
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
				 //echo "1PER_ID=".$value."||".$NUM_HRS_save ."||".$AMOUNT_save."</br>";
				
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
					$OT_Status_save = $OT_Status;
				}else{
					$OT_Status_save = 3;
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
								$OT_Status_save,'$START_TIME_BFW_Save','$END_TIME_BFW_Save',
								$SESS_USERID, '$UPDATE_DATE',$SESS_USERID, '$UPDATE_DATE')   ";
					$db_dpis->send_cmd($cmd);
					//echo $cmd." =xxx<br>";
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกำหนดบุคคลที่ทำ OT [$value : $YMD_TIME_START : $PER_CARDNO]");
					
				}
				
				
				
				//-------------------------------------------------------------------------------
				//ทำวันอื่นๆที่เหลือ ตามช่วงวันที่กำหนด
				if($YMD_TIME_START != $YMD_TIME_END){ // ถ้ามากกว่า 1 วันค่อยมาบันทึกส่วนนี้
					$date = $YMD_TIME_START; //mdy
					for($i=1;$i<$cntday;$i++){
						
						$date = strtotime($date);
						$date2 = strtotime("+1 day", $date);
						$date =date('Y-m-d', $date2);

						
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
								$cmd2 = " select ORG_ID,POS_ID,POEM_ID,POEMS_ID,POT_ID,PER_OT_FLAG from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
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
								if($OT_Status == "1"  || $OT_Status == "3"){
		
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
									
									if($OT_Status == "1"){
										$START_TIME_CHK = "0000";
										$END_TIME_CHK = "0000";
										
									}
								}
								
								// เวลาเริ่มต้นทำหลังทำงาน
								if($OT_Status == "2"  || $OT_Status == "3"){
									
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
									
									if($OT_Status == "2"){
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
							$OT_Status_save = $OT_Status;
						}else{
							$OT_Status_save = 3;
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
										$search_department_id,$save_org_id,$save_ORG_LOWER1,$NUM_HRS_save,$AMOUNT_save,'$START_TIME_CHK_Save','$END_TIME_CHK_Save',
										$OT_Status_save,'$START_TIME_BFW_Save','$END_TIME_BFW_Save',
										$SESS_USERID, '$UPDATE_DATE',$SESS_USERID, '$UPDATE_DATE')   ";
							//echo $cmd." =zzz<br>";
							$db_dpis->send_cmd($cmd);
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลกำหนดบุคคลที่ทำ OT [$value : $YMD_TIME_START : $PER_CARDNO]");
							
						}
						
					} // end for($i=1;$i<$cntday;$i++){
				
				} // end if($YMD_START_DATE != $YMD_END_DATE){
				
				
				
			} // end for
			$SELECTED_PER_ID ="";
			$HideID ="";
			$command="";
			/*echo "<script>window.location='../admin/es_t0205_ot_person_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";*/
		
		
				} //if($chkPerId>0){

 	} // end if($command=="ADD"){ 
	
	
	if($command=="DELETE"){
		
		if($list_allow_all=="1"){ /* บันทึกทั้งหมด*/
				$cmd2 = " select POS_ID,ORG_ID,POEM_ID,POEMS_ID,POT_ID,ORG_ID_1,PER_OT_FLAG  from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
				$db_dpis2->send_cmd($cmd2);
				$data2 = $db_dpis2->get_array();
				$PER_POS_ID = $data2[POS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
				$PER_POEM_ID = $data2[POEM_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
				$PER_POEMS_ID = $data2[POEMS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
				$PER_POT_ID = $data2[POT_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
				$PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
				$PER_ORG_ID_1 = $data2[ORG_ID_1]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน ต่ำกว่าสำนัก 1 ระดับ*/
				$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*รหัสเจ้าของ OT ตามกฏหมาย*/
			
				
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
				
				
				if(empty($select_org_structure_ot)){$select_org_structure_ot=0;}
		
				 if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
	
					if($P_OTTYPE_ORGANIZE ==2){ // มอบหมาย
							$cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_ORG_ID"; 
							$db_dpis2->send_cmd($cmd3);
							$data3 = $db_dpis2->get_array();
							$search_org_name = $data3[ORG_NAME];
							$search_org_id = $PER_ORG_ID;
							
							if($PER_OT_FLAG!=$PER_ORG_ID){
								$cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_OT_FLAG"; 
								$db_dpis2->send_cmd($cmd3);
								$data3 = $db_dpis2->get_array();
								$search_org_name_1 = $data3[ORG_NAME];
								$search_org_id_1 = $PER_OT_FLAG;
							}
					 }else{ // กฏหมาย
					 
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
							
							$cmd3 = " select g.ORG_NAME ,g.ORG_ID,g1.ORG_NAME as ORG_NAME_1 ,g1.ORG_ID as ORG_ID_1
							   from $fromTable c 
							   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
							   LEFT JOIN PER_ORG  g1 on (g1.ORG_ID=c.ORG_ID_1)
							   where $whereTable "; 
							$db_dpis2->send_cmd($cmd3);
							$data3 = $db_dpis2->get_array();
							$search_org_name = $data3[ORG_NAME];
							$search_org_id = $data3[ORG_ID];
							if($PER_OT_FLAG==$data3[ORG_ID_1]){
								$search_org_name_1 = $data3[ORG_NAME_1];
								$search_org_id_1 = $data3[ORG_ID_1];
							}
					 }
					
					if(!$search_date_min) { // หาวันที่เริ่มต้นหลังจากปิดงวดโอที เดือนล่าสุด
				
						$cmd2 = " select max(CONTROL_ID) as CONTROL_ID  from TA_PER_OT where DEPARTMENT_ID=$search_org_id and CONTROL_ID IS NOT NULL "; 
						$db_dpis2->send_cmd($cmd2);
						$data2 = $db_dpis2->get_array();
						
						if (!empty($data2[CONTROL_ID])){
							$cmd2 = " select END_DATE  from TA_PER_OT_CONTROL where CONTROL_ID=".$data2[CONTROL_ID]; 
							$db_dpis2->send_cmd($cmd2);
							$data3 = $db_dpis2->get_array();
			
							if (!empty($data3[END_DATE])){
								$dateEnd_date = substr($data3[END_DATE],8,2)."-".substr($data3[END_DATE],5,2)."-".substr($data3[END_DATE],0,4);
								$datetomorow = date('d-m-Y', strtotime("+1 day", strtotime("$dateEnd_date")));
								 $search_date_min = substr($datetomorow,0,2)."/". substr($datetomorow,3,2) ."/". (substr($datetomorow,6,4) + 543);
							}else{
								$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
							}
						}else{
							$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
						}
						
					}
				}else{
					 if(!$search_date_min) {
						$search_date_min = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
					}
				
				}

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
			  
				
				
				if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
				if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
				if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
				if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
				if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
				
				$condition_date = "";
				if($search_date_min && $search_date_max){ 
					 $tmpsearch_date_min =  save_date($search_date_min);
					 $tmpsearch_date_max =  save_date($search_date_max);
					 $arr_search_condition[] = " ( ot.OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
					 $condition_date = " AND  ( OT_DATE BETWEEN  '$tmpsearch_date_min' and '$tmpsearch_date_max') ";
				}else if($search_date_min && empty($search_date_max)){ 
					 $tmpsearch_date_min =  save_date($search_date_min);
					 $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_min') ";
					 $condition_date = " AND (OT_DATE='$tmpsearch_date_min') ";
				}else if(empty($search_date_min) && $search_date_max){ 
					 $tmpsearch_date_max =  save_date($search_date_max);
					 $arr_search_condition[] = " (ot.OT_DATE='$tmpsearch_date_max') ";
					 $condition_date = " AND (OT_DATE='$tmpsearch_date_max') ";
				}
				
				/*เช็คการคำนวน Ot จากเจ้าของเรื่อง*/
				$condition_OT = "";
				if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
					if($select_org_structure_ot=="0"){ // กฏหมาย
						if($search_org_id_1){
							$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
						}else{
							$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id;
						}
					}else{ // มอบหมาย
						if($search_org_id_1){
							$condition_OT = " AND DEPARTMENT_ID=" .$PER_ORG_ID." AND ORG_LOWER1=".$search_org_id_1;
						}else{
							$condition_OT = " AND DEPARTMENT_ID=" .$PER_ORG_ID;
						}
					}
				}else{ 
					if($select_org_head=="0"){
						if($search_org_id_1){
							$condition_OT = " AND DEPARTMENT_ID=" .$search_org_id." AND ORG_LOWER1=".$search_org_id_1;
						}elseif($search_org_id){
							$condition_OT = " AND DEPARTMENT_ID=$search_org_id";
						}else{
							$condition_OT = " AND ORG_ID=$search_department_id AND DEPARTMENT_ID=-1";
						}
						
					}
					
				}
			
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " select 	distinct
									ot.PER_ID,
									(select min(OT_DATE) from TA_PER_OT 
									   where  PER_ID=ot.PER_ID $condition_date $condition_OT) AS OT_MIN,
									(select max(OT_DATE) from TA_PER_OT 
									   where PER_ID=ot.PER_ID $condition_date $condition_OT) AS OT_MAX,
									a.PER_CARDNO
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
						 where 		1=1
										$search_condition   ";

					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " DELETE FROM  TA_PER_OT	WHERE AUDIT_FLAG!=1 AND  PER_ID=".$data[PER_ID]." AND  (OT_DATE BETWEEN  '".$data[OT_MIN]."' and '".$data[OT_MAX]."') ";
							$db_insert->send_cmd($cmd);
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกำหนดบุคคลที่ทำ OT [".$data[PER_ID]." : ".$data[OT_MIN]." : ".$data[OT_MAX]." : ".$data[PER_CARDNO]."]");
			
							
						}
				 	}
		
			
		}else{  /*กรณีที่เลือกบางรายการ*/
		
			if(count($list_allow_id)>0){
				for($i=0;$i<=count($list_allow_id);$i++){
					if(!empty($list_allow_id[$i])){
						$val =  explode("_",$list_allow_id[$i]);
						$cmd = " DELETE FROM  TA_PER_OT	WHERE AUDIT_FLAG!=1 AND  PER_ID=".$val[0]." AND  (OT_DATE BETWEEN  '".$val[1]."' and '".$val[2]."') ";
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลกำหนดบุคคลที่ทำ OT [".$val[0]." : ".$val[1]." : ".$val[2]." : ".$val[3]."]");
			
					}
					
				}
			}
		
		}
		
		
		$command = "SEARCH";			
		
	}
	
  
?>