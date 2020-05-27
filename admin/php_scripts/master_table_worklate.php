<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$save_LATE_REMARK = trim($LATE_REMARK);
	

	if($command == "ADD"){
		
		if(empty($START_DATE)){$START_DATE=$END_DATE;}
		if(empty($END_DATE)){$END_DATE=$START_DATE;}
		$YMD_START_DATE = (substr($START_DATE,6,4)-543)."-".substr($START_DATE,3,2)."-".substr($START_DATE,0,2);
		$YMD_END_DATE = (substr($END_DATE,6,4)-543)."-".substr($END_DATE,3,2)."-".substr($END_DATE,0,2);
		
		/*หาจำนวนมีกี่วัน*/
		$date1 = $YMD_START_DATE.' 00:00:00';
		$date2 = $YMD_END_DATE.' 23:59:59';
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);
		$seconds_diff = $ts2 - $ts1;
		$cntday=(floor($seconds_diff/(60*60*24))+1);
				
		foreach($WL_ID as $WL_CODE => $WL_NO){  /*สถานที่ปฏิบัติราชการ*/
		
			foreach($WC_ID as $WC_CODE => $WC_NO){ /*รอบ*/
				
				/*เอาเฉพาะวันแรกที่เลือก เพื่อเอาไป Loop*/
				$date = $YMD_START_DATE; /*mdy*/
				
				/*เช็ควันหยุด ถ้าไม่ตรงวันหยุดถึงจะบันทึก*/
				$timestamp = strtotime($YMD_START_DATE); /*y-m-d*/
				$day = date('D', $timestamp);
				if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){
					
					/*หาวันตามปฏิทินวันหยุด ถ้าไม่มีค่อยบันทึก*/
					$cmd = " select  HOL_DATE from PER_HOLIDAY 
							 where HOL_DATE='$YMD_START_DATE'";
					$count_duplicate = $db_dpis->send_cmd($cmd);
					if($count_duplicate <= 0){
						
						$cmd = " select WL_CODE  from PER_WORK_LATE 
								 where WL_CODE='$WL_NO' and WC_CODE='$WC_NO' and WORK_DATE='$YMD_START_DATE' ";
						$count_duplicate1 = $db_dpis->send_cmd($cmd);
						if($count_duplicate1 <= 0){
							
							/*เอารอบมา เพื่อหา บวกเวลาเพิ่ม*/
							$cmd = "	  select 	WC_START,MIN_ONTIME,ON_TIME from PER_WORK_CYCLE where 	WC_CODE='$WC_NO' ";
							$db_dpis->send_cmd($cmd);
							$data = $db_dpis->get_array();
							if($WC_NO=="-1"){
								$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[ON_TIME],0,2).':'.substr($data[ON_TIME],2,2).' + '.$LATE_TIME.' minute'); /*Bgn*/
								$save_LATE_TIME =  date('Hi', $newtimestampBgn);
							}else{
								$TLATE_TIME=$data[MIN_ONTIME] + $LATE_TIME;
								$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[WC_START],0,2).':'.substr($data[WC_START],2,2).' + '.$TLATE_TIME.' minute'); /*Bgn*/
								$save_LATE_TIME =  date('Hi', $newtimestampBgn);
							}
							
							
							$cmd = " insert into PER_WORK_LATE (WL_CODE, WC_CODE, WORK_DATE, LATE_TIME, LATE_REMARK, UPDATE_USER, UPDATE_DATE) 
									 values ('$WL_NO', '$WC_NO', '$YMD_START_DATE', '$save_LATE_TIME', '$save_LATE_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
							$db_dpis->send_cmd($cmd);
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล [$WL_NO : $WC_NO : $YMD_START_DATE]");
						}/* end if($count_duplicate1 <= 0){*/
						
					}/* end if($count_duplicate <= 0){*/

				}/* end if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){*/
				
				
				/*เอาวันต่อๆ มา---------------------------------------------*/
				if($YMD_START_DATE != $YMD_END_DATE){ /* ถ้ามากกว่า 1 วันค่อยมาบันทึกส่วนนี้*/
						for($i=1;$i<$cntday;$i++){
				
								$date = strtotime($date);
								$date2 = strtotime("+1 day", $date);
								$date =date('Y-m-d', $date2);
								
								/*เช็ควันหยุด ถ้าไม่ตรงวันหยุดถึงจะบันทึก*/
								$timestamp = strtotime($date); /*y-m-d*/
								$day = date('D', $timestamp);
								if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){
								/*หาวันตามปฏิทินวันหยุด ถ้าไม่มีค่อยบันทึก*/
								$cmd = " select  HOL_DATE from PER_HOLIDAY where HOL_DATE='$date'";
								$count_duplicate = $db_dpis->send_cmd($cmd);
								if($count_duplicate <= 0){
									
									$cmd = " select WL_CODE  from PER_WORK_LATE 
											 where WL_CODE='$WL_NO' and WC_CODE='$WC_NO' and WORK_DATE='$date' ";
									$count_duplicate1 = $db_dpis->send_cmd($cmd);
									if($count_duplicate1 <= 0){
										
										/*เอารอบมา เพื่อหา บวกเวลาเพิ่ม*/
										$cmd = "	  select 	WC_START,MIN_ONTIME,ON_TIME from PER_WORK_CYCLE where 	WC_CODE='$WC_NO' ";
										$db_dpis->send_cmd($cmd);
										$data = $db_dpis->get_array();
										
										if($WC_NO=="-1"){
											$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[ON_TIME],0,2).':'.substr($data[ON_TIME],2,2).' + '.$LATE_TIME.' minute'); /*Bgn*/
											$save_LATE_TIME =  date('Hi', $newtimestampBgn);
										}else{
											$TLATE_TIME=$data[MIN_ONTIME] + $LATE_TIME;
											$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[WC_START],0,2).':'.substr($data[WC_START],2,2).' + '.$TLATE_TIME.' minute'); /*Bgn*/
											$save_LATE_TIME =  date('Hi', $newtimestampBgn);
										}
										
										
										$cmd = " insert into PER_WORK_LATE (WL_CODE, WC_CODE, WORK_DATE, LATE_TIME, LATE_REMARK, UPDATE_USER, UPDATE_DATE) 
												 values ('$WL_NO', '$WC_NO', '$date', '$save_LATE_TIME', '$save_LATE_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
										$db_dpis->send_cmd($cmd);
										insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล [$WL_NO : $WC_NO : $YMD_START_DATE]");
									
									}/* end if($count_duplicate1 <= 0){*/
			
								}/* end if($count_duplicate <= 0){*/
								
							}/* end if(strtoupper($day) != 'SAT' && strtoupper($day) !='SUN'){*/
								
						}	/* end for($i=1;$i<$cntday;$i++){*/
				}	/* end if($YMD_START_DATE != $YMD_END_DATE){*/
				
				/*----------------------------------------------------------------*/
				
			}/* end foreach($WC_ID as $WC_CODE => $WC_NO){*/	
			
		}/* end foreach($WL_ID as $WL_CODE => $WL_NO){*/
		
		echo "<script>window.location='../admin/master_table_worklate.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	}

	if($command == "UPDATE"){
							
				/*เอารอบมา เพื่อหา บวกเวลาเพิ่ม*/
				$cmd = "	  select 	WC_START,MIN_ONTIME,ON_TIME from PER_WORK_CYCLE where 	WC_CODE='$HIDWC_CODE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if($HIDWC_CODE=="-1"){
					$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[ON_TIME],0,2).':'.substr($data[ON_TIME],2,2).' + '.$LATE_TIME.' minute'); /*Bgn*/
					$save_LATE_TIME =  date('Hi', $newtimestampBgn);
				}else{
					$TLATE_TIME=$data[MIN_ONTIME] + $LATE_TIME;
					$newtimestampBgn = strtotime(date('Y-m-d').' '.substr($data[WC_START],0,2).':'.substr($data[WC_START],2,2).' + '.$TLATE_TIME.' minute'); /*Bgn*/
					$save_LATE_TIME =  date('Hi', $newtimestampBgn);
				}
				

				$cmd = " 	update PER_WORK_LATE set LATE_TIME='$save_LATE_TIME', LATE_REMARK='$save_LATE_REMARK', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
									where WL_CODE='$HIDWL_CODE' and WC_CODE='$HIDWC_CODE' and WORK_DATE='$HIDSTART_DATE' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล [$HIDWL_CODE : $HIDWC_CODE : $HIDSTART_DATE]");
				
				$command="";

	}
	
	if($command == "DELETE"){
		if($CONTROL_ID_ALL=="1"){ /* บันทึกทั้งหมด*/
		
					
				if(trim($search_wl_code)) $arr_search_condition[] = "(wla.WL_CODE = '$search_wl_code')";
				if(trim($search_wc_code)) $arr_search_condition[] = "(wla.WC_CODE = '$search_wc_code')";
				
				if($search_date_min && $search_date_max){ 
					 $tmpsearch_date_min =  save_date($search_date_min);
					 $tmpsearch_date_max =  save_date($search_date_max);
					 $arr_search_condition[] = "  wla.WORK_DATE BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ";
				}else if($search_date_min && empty($search_date_max)){ 
					 $tmpsearch_date_min =  save_date($search_date_min);
					 $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_min'  ";
				}else if(empty($search_date_min) && $search_date_max){ 
					 $tmpsearch_date_max =  save_date($search_date_max);
					 $arr_search_condition[] = " wla.WORK_DATE = '$tmpsearch_date_max' ";
				}else{
					$arr_search_condition[] = " wla.WORK_DATE = (select max(WORK_DATE) from PER_WORK_LATE) ";
				}
			
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " select 		wla.WL_CODE, wla.WC_CODE, wla.WORK_DATE
								  from 		PER_WORK_LATE  wla
                                  left join PER_WORK_LOCATION  wlo on(wlo.WL_CODE=wla.WL_CODE)
                                  left join PER_WORK_CYCLE wcy on(wcy.WC_CODE=wla.WC_CODE)
								  $search_condition   ";
					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " delete from PER_WORK_LATE	 where WL_CODE='".$data[WL_CODE]."' and WC_CODE='".$data[WC_CODE]."' and WORK_DATE='".$data[WORK_DATE]."'";
							$db_insert->send_cmd($cmd);
							
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูล [".$data[WL_CODE]." : ".$data[WC_CODE]." : ".$data[OT_DATE]."]");
							
						}
				 	}
		
			
		}else{  /*กรณีที่เลือกบางรายการ*/
		
			if(count($CONTROL_ID)>0){
				for($i=0;$i<=count($CONTROL_ID);$i++){
					if(!empty($CONTROL_ID[$i])){
						$val =  explode("_",$CONTROL_ID[$i]);
						$cmd = " delete from PER_WORK_LATE	
										where WL_CODE='".$val[0]."' 
										and WC_CODE='".$val[1]."' 
										and WORK_DATE='".$val[2]."' ";

						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลตรวจทานการทำ OT [".$val[0]." : ".$val[1]." : ".$val[2]."]");
					}
					
				}
			}
		
		}
		
	
		echo "<script>window.location='../admin/master_table_worklate.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	
	}
	
	if($UPD){
		$cmd = "	  select 		WL_CODE, WC_CODE, WORK_DATE, LATE_TIME, LATE_REMARK ,UPDATE_USER,UPDATE_DATE
						  from 		PER_WORK_LATE 
						  where 		WL_CODE='$HIDWL_CODE' and WC_CODE='$HIDWC_CODE' and WORK_DATE='$HIDSTART_DATE'
						  order by 	WL_CODE, WC_CODE, WORK_DATE  ";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$START_DATE = show_date_format(trim($data[WORK_DATE]), $DATE_DISPLAY);
		$HIDSTART_DATE = trim($data[WORK_DATE]);
		
		/*เอารอบมา เพื่อหา ลบเวลาเพิ่ม*/
		
		$cmd = "	  select 	WC_START,MIN_ONTIME,ON_TIME from PER_WORK_CYCLE where 	WC_CODE='$HIDWC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		
		function getminture($day1,$day2) 
		{ 
		  return round((strtotime($day2)-strtotime($day1))/(60),0); 
		} 
		$end = "2011/01/11 ".substr($data[LATE_TIME],0,2).":".substr($data[LATE_TIME],2,2).":00"; 
		if($HIDWC_CODE != "-1"){
			$begin = "2011/01/11 ".substr($data2[WC_START],0,2).":".substr($data2[WC_START],2,2).":00"; 
		}else{
			$begin = "2011/01/11 ".substr($data2[ON_TIME],0,2).":".substr($data2[ON_TIME],2,2).":00"; 
		}
		$LATE_TIME=getminture($begin,$end) - $data2[MIN_ONTIME];
		
		
		
		$LATE_REMARK = $data[LATE_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$cmd = "	select WL_NAME from PER_WORK_LOCATION where WL_CODE='$HIDWL_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$WL_NAME = $data2[WL_NAME];

		$cmd = "	select WC_NAME from PER_WORK_CYCLE where WC_CODE='$HIDWC_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$WC_NAME = $data2[WC_NAME]." ลงเวลาได้ถึง ".substr($data[LATE_TIME],0,2).":".substr($data[LATE_TIME],2,2). " น. ไม่ถือว่ามาสาย";
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$WL_CODE = "";
		$WC_CODE = "";
		$WORK_DATE = "";
		$LATE_REMARK = "";	
		$HIDWL_CODE = "";	
		$HIDWC_CODE = "";	
		$LATE_TIME = "";	
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>