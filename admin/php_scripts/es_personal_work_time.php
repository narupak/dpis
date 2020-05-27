<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");  

	$db_dpis5 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($PER_ID){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
					PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
								PER_PERSONAL.PER_ID=$PER_ID ";

		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		
		
		
	/*หามาสาย*/
	/*หาจำนวนมีกี่วัน*/
	/* เก็บเอาไว้ เผื่อคิดทั้งรอบปี
	$YMD_TIME_START_L = (date('Y')-1).'-10-01';
	$YMD_TIME_END_L = date('Y').'-09-30';
    $date_L1 = $YMD_TIME_START_L.' 00:00:00';
    $date_L2 = $YMD_TIME_END_L.' 23:59:59';
    $ts_L1 = strtotime($date_L1);
    $ts_L2 = strtotime($date_L2);
    $seconds_diff_L = $ts_L2 - $ts_L1;
    $cntday_L=(floor($seconds_diff_L/(60*60*24))+1);
    
    $con_TIME_STAMP_L ="";
	 if($YMD_TIME_START_L != $YMD_TIME_END_L){ // ถ้ามากกว่า 1 วันค่อยมาบันทึกส่วนนี้
		$con_TIME_STAMP_L =" select '$YMD_TIME_START_L' as datex from dual union all ";
		$date_L = $YMD_TIME_START_L; 
		for($i=1;$i<$cntday_L;$i++){
			$date_L = strtotime($date_L);
			$date_L2 = strtotime("+1 day", $date_L);
			$date_L =date('Y-m-d', $date_L2);
			$union = " union all ";
			if(($cntday_L - 1)==$i){
				$union = " ";
			}
			$con_TIME_STAMP_L .=" select '$date_L' as datex from dual ".$union;
			
		}
	 }

		
		$cmd5 = "select * from ( 
                            	with tbdate as(                 
                                         $con_TIME_STAMP_L
                                      ),person_tb as (
                                            select a.PER_ID
                                            from per_personal a
                                            WHERE a.PER_ID=$PER_ID
                                                       )
                                      SELECT tbtmp.*,substr(wt.start_date,12,5) w_starttime, 
                                      substr(wt.end_date,12,5) AS w_endtime,
                                         ( SELECT TO_CHAR(min(TIME_STAMP),'HH24:MI') 
                                           FROM PER_TIME_ATTENDANCE
                                           WHERE per_id=tbtmp.per_id AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') =tbtmp.datex ) 
                                      AS att_starttime ,
                                         ( SELECT TO_CHAR(max(TIME_STAMP),'HH24:MI') 
                                           FROM PER_TIME_ATTENDANCE
                                           WHERE per_id=tbtmp.per_id AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') =tbtmp.datex ) 
                                      as att_endtime,wc.WC_NAME,wt.ABSENT_FLAG,wc.WC_START,wc.WC_END,
                                      cyh.WC_CODE
                                      FROM(
                                         SELECT * FROM tbdate,person_tb 
                                      ) tbtmp
                                      left join PER_WORK_TIME wt on(substr(wt.start_date,1,10)=tbtmp.datex AND wt.per_id=tbtmp.per_id)
                                      left join PER_WORK_CYCLEHIS cyh on(cyh.PER_ID=tbtmp.PER_ID AND  
                                                tbtmp.datex between cyh.START_DATE AND 
                                                case when cyh.END_DATE is not null then cyh.END_DATE 
                                                else 
                                                tbtmp.datex end ) 
                                      left join PER_WORK_CYCLE  wc on(wc.WC_CODE=cyh.WC_CODE) 
                            
                              )   ";	
	
			$count_page_data5 = $db_dpis5->send_cmd($cmd5);
			$HIDLATE = 0;
			if ($count_page_data5) {
				while ($data5 = $db_dpis5->get_array()) {
					
					//เช็ควันหยุด
					$timestamp = strtotime($data5[DATEX]); //y-m-d
					$day = date('D', $timestamp);
					if(strtoupper($day) == 'SAT' || strtoupper($day) =='SUN'){
							if ($data5[W_STARTTIME] || $data5[ATT_STARTTIME] ) {  //มาทำงานวันหยุด
								$showstatus= "<font color='green'>มา</font>";
							 }else{
								$showstatus= "<font color='#E817CD'>วันหยุด</font>";
							 }
					}else{
							
							 //หาวันตามปฏิทินวันหยุด
							$cmd = " select  HOL_DATE from PER_HOLIDAY 
									 where HOL_DATE='$data5[DATEX]'";
							$count_duplicate = $db_dpis1->send_cmd($cmd);
							if($count_duplicate > 0){
								 if ($data5[W_STARTTIME] || $data5[ATT_STARTTIME] ) { //มาทำงานวันหยุด
									$showstatus= "<font color='green'>มา</font>";
								 }else{
									$showstatus= "<font color='#E817CD'>หยุดนักขัตฤกษ์</font>";
								 }
								
							}else{
							
								 
								 //ดูค่า ABSENT_FLAG 
								// 1 : วันหยุด
								//2 : ลา
								//3 : สาย
								//4 : ปฏิบัติราชการนอกสถานที่
								//5 : ขาดราชการ (ไม่มาเลย)
								//6 : ขาดราชการ (สแกน แต่เลยเวลาที่
								//7 : ลาแต่มาทำงาน
								//9 : ไม่สแกน (แต่ส่งใบคำร้องรับรองให้ กจ.)
								//0 : มาปกติ 
								
								if (!empty($data5[ABSENT_FLAG])) { // กรณีโอนไปแล้ว
									if ($data5[ABSENT_FLAG]==1) { 
										$showstatus= "<font color='#E817CD'>วันหยุด</font>";
									}elseif ($data5[ABSENT_FLAG]==2) { 
										$showstatus= "<font color='green'>ลา</font>";
									}elseif ($data5[ABSENT_FLAG]==3) { 
										$showstatus= "<font color='red'>สาย</font>";
										$HIDLATE++;
									}elseif ($data5[ABSENT_FLAG]==5) { 
										$showstatus= "<font color='red'>ขาด</font>";
									}elseif ($data5[ABSENT_FLAG]==6) { 
										$showstatus= "<font color='red'>ขาด</font>";
									}elseif ($data5[ABSENT_FLAG]==7) { 
										$showstatus= "<font color='green'>ลาแต่มา</font>";
									}elseif ($data5[ABSENT_FLAG]==9) { 
										$showstatus= "<font color='green'>มา</font>";
									}elseif ($data5[ABSENT_FLAG]==0) { 
										$showstatus= "<font color='green'>มา</font>";
									}
								
								}else{ // กรณียังไม่ได้โอน
								
									if (empty($data5[ATT_STARTTIME])) {  // ถ้าไม่มีในสแกน ต้องหาที่ ยื่นคำร้อง
										$cmd = " select  PER_ID from TA_REQUESTTIME 
													where PER_ID=$PER_ID AND REQUEST_DATE='$data5[ATT_STARTTIME]'";
										$count_duplicate1 = $db_dpis1->send_cmd($cmd);
										if($count_duplicate1 > 0){
											$showstatus= "<font color='green'>มา</font>";
										}else{ // หาดูว่า มีลาหรือไม่
												
												$cmd = " SELECT ABS_STARTPERIOD
													FROM PER_ABSENTHIS
													where PER_ID =$PER_ID
													AND ( '$data5[DATEX]' BETWEEN substr(ABS_STARTDATE,1,10) and substr(ABS_ENDDATE,1,10)) ";
												$db_dpis1->send_cmd($cmd);
												$data_abs = $db_dpis1->get_array();
												 if($data_abs > 0){
														if($data_abs[ABS_STARTPERIOD]==1){
																$showstatus= "<font color='green'>ลา (เช้า)</font>";
														}else if($data_abs[ABS_STARTPERIOD]==2){
																$showstatus= "<font color='green'>ลา (บ่าย)</font>";
														}else{
																$showstatus= "<font color='green'>ลา</font>";
														}
														
												 }else{
														$showstatus= "<font color='red'>ขาด</font>";
												 }
										
											
										}
										
									}else{ //สแกนและยังไม่ได้โอน
										
										 
										 // มาหาที่ ปรับเวลาพิเศษก่อน
												 $cmd = " SELECT LATE_TIME FROM PER_WORK_LATE  where WORK_DATE = '$data5[DATEX]' AND WC_CODE='".$data5[WC_CODE]."'";
												$db_dpis1->send_cmd($cmd);
												$data2 = $db_dpis1->get_array();
												if($data2[LATE_TIME]){
													$TMP_TIME_HH= substr($data2[LATE_TIME],0,2);
													$TMP_TIME_II= substr($data2[LATE_TIME],2,2);
												}else{
													$TMP_TIME_HH= substr($data5[WC_START],0,2);
													$TMP_TIME_II= substr($data5[WC_START],2,2);
												}
												
												//เวลาเพิ่มพิเศษ
												$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_EXTRATIME' ";
												$db_dpis1->send_cmd($cmd);
												$data_E = $db_dpis1->get_array();
												$TMP_P_EXTRATIME = $data_E[CONFIG_VALUE];
												
												$TMP_P_EXTRATIME2 = $TMP_P_EXTRATIME + 1;
												$newtimestampBgn1 = strtotime($data5[DATEX].' '.$TMP_TIME_HH.':'.$TMP_TIME_II.' + '.$TMP_P_EXTRATIME2.' minute'); 
												$P_EXTRATIME_START =  date('Hi', $newtimestampBgn1);
												
												//เวลาเพิ่มพิเศษ
												$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_TIMEOVERLATE' ";
												$db_dpis1->send_cmd($cmd);
												$data2 = $db_dpis1->get_array();
												$TMP_P_TIMEOVERLATE = $data2[CONFIG_VALUE];
												
												$newtimestampBgn = strtotime($data5[DATEX].' '.$TMP_TIME_HH.':'.$TMP_TIME_II.' + '.$TMP_P_TIMEOVERLATE.' minute'); 
												$P_EXTRATIME_END =  date('Hi', $newtimestampBgn);
												
												
												$CHKATT_STARTTIME=substr($data5[ATT_STARTTIME],0,2).substr($data5[ATT_STARTTIME],3,2);
												//หาค่าเวลาสาย
												if($CHKATT_STARTTIME< $P_EXTRATIME_START){
														$showstatus= "<font color='green'>มา</font>";
												}else if(($CHKATT_STARTTIME>=$P_EXTRATIME_START) && ($CHKATT_STARTTIME<=$P_EXTRATIME_END) ){
														$showstatus= "<font color='red'>สาย</font>";
														$HIDLATE++;
												}else{
														$showstatus= "<font color='red'>ขาด</font>";
												}

									}
									
								}
								
							}
							
					}
					
				} 
				
				
			}*/
		
		
		
	} // end if

?>