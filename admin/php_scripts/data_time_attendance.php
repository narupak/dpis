<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_d = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_p = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_u = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_d1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	// ==========================

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD"){
		
		
		

		$Save_save_org_id=$save_org_id;
		$Save_save_month=$save_month;
		$Save_save_year=$save_year;
		$cmd = " select CONTROL_ID,PROCESS_NUM from PER_WORK_TIME_CONTROL 
		                  where CLOSE_YEAR=$Save_save_year 
						  AND CLOSE_MONTH=$Save_save_month
						  AND DEPARTMENT_ID=$Save_save_org_id ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
			$data = $db_dpis->get_array();
			$CONTROL_ID = $data[CONTROL_ID];
			$PROCESS_NUM = $data[PROCESS_NUM]+1;
			
			$cmd = " update PER_WORK_TIME_CONTROL SET
						   PROCESS_DATE=SYSDATE,
						  CLOSE_DATE=NULL ,PROCESS_NUM=$PROCESS_NUM 
						  WHERE CONTROL_ID=$CONTROL_ID ";
			$db_dpis_u->send_cmd($cmd);
			
			//เคลียหมดเลย
			if($HIDCEARL=="CEARL"){
				$cmd = " delete from PER_WORK_TIME  where  CONTROL_ID=$CONTROL_ID ";
				$db_dpis_d1->send_cmd($cmd);
				
				// เคลียร์ OT เฉพาะที่ยังไม่ตรวจทาน kittiphat 12/11/2561
				$bgnOT= ($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-01";
				$last_date_find = strtotime(date("Y-m-d", strtotime($bgnOT)) . ", last day of this month");
				$endOT= date("Y-m-d",$last_date_find); // หาค่าวันสุดท้ายของเดือน
				$cmd = " select		ot.PER_ID,	ot.OT_DATE
												from  TA_PER_OT ot
												left join PER_PERSONAL a on(a.PER_ID=ot.PER_ID) 
							where   ot.AUDIT_FLAG = 0 AND a.ORG_ID=$Save_save_org_id AND (ot.OT_DATE BETWEEN  '$bgnOT' and '$endOT') 
							AND (ot.START_TIME is not null or ot.END_TIME is not null or ot.START_TIME_BFW is not null or ot.END_TIME_BFW is not null) ";
				//echo $cmd."<br>";
				$count_page_data = $db_dpis->send_cmd($cmd);
				if($count_page_data){
					while($data = $db_dpis->get_array()){
						
						$cmd = " update TA_PER_OT set 
										START_TIME=NULL,END_TIME = NULL,NUM_HRS=NULL,AMOUNT=NULL,START_TIME_BFW=NULL,END_TIME_BFW=NULL
										where  PER_ID=".$data[PER_ID]." AND OT_DATE=".$data[OT_DATE];
						$db_dpis_d1->send_cmd($cmd);
						
					}
				}
				///////////////////////////////////////////////////////////////////
				
				
				
			}else{
				$cmd = " delete from PER_WORK_TIME  where  CONTROL_ID=$CONTROL_ID  AND (APV_ENTTIME IS NULL AND  APV_EXITTIME IS NULL)  ";
				$db_dpis_d->send_cmd($cmd);
			}
			

			$mk_data=mktime(0, 0, 0, substr("0".$Save_save_month,-2), '01', ($Save_save_year-543));
			$xx = date("Y-m-d", $mk_data)." 00:00:00";

			$bgnbackMonth= ($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-01";
        	$yy=($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-".date("t",strtotime($bgnbackMonth))." 23:59:59";

			$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
			$db_dpis->send_cmd($cmd_con);
			$data_con = $db_dpis->get_array();
			$SCANTYPE = $data_con[CONFIG_VALUE];
			
			$cmd = file_get_contents('ProcessWorkTimeByOrgID.sql');	
			$cmd=str_ireplace(":CONTROL_ID",$CONTROL_ID,$cmd);
			$cmd=str_ireplace(":ORG_ID",$Save_save_org_id,$cmd);
			$cmd=str_ireplace(":BEGINDATEAT","'".$xx."'",$cmd);
			$cmd=str_ireplace(":TODATEAT","'".$yy."'",$cmd);
			$cmd=str_ireplace(":LAW_OR_ASS",2,$cmd);
			$cmd=str_ireplace(":PER_TYPE",0,$cmd);
			$cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
			$db_dpis_p->send_cmd($cmd);
			
			//echo "1<pre>".$cmd;
			
			//echo "CONTROL_ID=".$CONTROL_ID."|ORG_ID=".$Save_save_org_id."|BEGINDATEAT='".$xx."'|TODATEAT='".$yy."'|LAW_OR_ASS=2|PER_TYPE=0|SCANTYPE=".$SCANTYPE."<br><pre>".$cmd;
			
				

		}else{
			
			$cmd = " insert into PER_WORK_TIME_CONTROL (CONTROL_ID, CLOSE_YEAR,CLOSE_MONTH, DEPARTMENT_ID, PROCESS_DATE,PROCESS_NUM,PROCESS_USER)
								  values (PER_WORK_TIME_CONTROL_SEQ.NEXTVAL,$Save_save_year,$Save_save_month, $Save_save_org_id, SYSDATE,1,$SESS_USERID)   ";
			$db_dpis_n->send_cmd($cmd);
			
			$cmd = " SELECT PER_WORK_TIME_CONTROL_SEQ.currval AS CURID FROM dual ";
			$db_dpis_n->send_cmd($cmd);
			$data = $db_dpis_n->get_array();
			$CONTROL_ID = $data[CURID];
			
			
			$mk_data=mktime(0, 0, 0, substr("0".$Save_save_month,-2), '01', ($Save_save_year-543));
			$xx = date("Y-m-d", $mk_data)." 00:00:00";

			$bgnbackMonth= ($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-01";
        	$yy=($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-".date("t",strtotime($bgnbackMonth))." 23:59:59";


			$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
			$db_dpis->send_cmd($cmd_con);
			$data_con = $db_dpis->get_array();
			$SCANTYPE = $data_con[CONFIG_VALUE];
			
			$cmd = file_get_contents('ProcessWorkTimeByOrgID.sql');	
			$cmd=str_ireplace(":CONTROL_ID",$CONTROL_ID,$cmd);
			$cmd=str_ireplace(":ORG_ID",$Save_save_org_id,$cmd);
			$cmd=str_ireplace(":BEGINDATEAT","'".$xx."'",$cmd);
			$cmd=str_ireplace(":TODATEAT","'".$yy."'",$cmd);
			$cmd=str_ireplace(":LAW_OR_ASS",2,$cmd);
			$cmd=str_ireplace(":PER_TYPE",0,$cmd);
			$cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
			$db_dpis_p->send_cmd($cmd);
			//echo "2<pre>".$cmd;
				
			/*insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ประมวลผลการลงเวลาประจำเดือน [$CONTROL_ID : $Save_save_org_id : $Save_save_year : $Save_save_month]");*/
			
			$HIDCEARL=="";
		}
		
		
		$db_dpis_ot = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$mk_data_ot=mktime(0, 0, 0, substr("0".$Save_save_month,-2), '01', ($Save_save_year-543));
		$xx_ot = date("Y-m-d", $mk_data_ot);

		$bgnbackMonth_ot= ($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-01";
		$yy_ot=($Save_save_year-543)."-".substr("0".$Save_save_month,-2)."-".date("t",strtotime($bgnbackMonth_ot));

		$cmd = file_get_contents('UpdateOT.sql');	
		$cmd=str_ireplace(":BEGINDATEAT","'".$xx_ot."'",$cmd);
		$cmd=str_ireplace(":TODATEAT","'".$yy_ot."'",$cmd);
		$cmd=str_ireplace(":ORG_ID","'".$Save_save_org_id."'",$cmd);
		$cmd=str_ireplace(":LAW_OR_ASS",2,$cmd);
		$cmd=str_ireplace(":PER_TYPE",0,$cmd);
		//echo "<pre>".$cmd;
		$db_dpis_ot->send_cmd($cmd);
		
		$command == "";
		
		$search_month = $save_month;
		$search_year = $save_year;

	} // end if
	
	
	if ($command == "SETFLAG_ALLOW") {
			
			$db_dpis_uwc = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			$db_dpis_uw = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			$db_dpis_del = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			$db_dpis_up = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
			
			$cntchkClose= count($CONTROL_ID);
		
			if($cntchkClose>0){
				for($idx=0;$idx<=$cntchkClose;$idx++){
					if($CONTROL_ID[$idx]){
						$val =  explode("_",$CONTROL_ID[$idx]);
						$cmd = " update PER_WORK_TIME_CONTROL set 
										CLOSE_USER=$SESS_USERID,CLOSE_DATE = sysdate
										where  CLOSE_DATE IS NULL AND CONTROL_ID=".$val[0];
						$db_dpis->send_cmd($cmd);
						
						$cmd1 = " update PER_WORK_TIME set 
										APV_ENTTIME=SCAN_ENTTIME,APV_EXITTIME=SCAN_EXITTIME
										where (APV_ENTTIME IS NULL AND  APV_EXITTIME IS NULL) AND  CONTROL_ID=".$val[0];
						$db_dpis_uwc->send_cmd($cmd1);
						
					
						/*บันทึกประวัติการลา*/

						
						//////////////////////////////////////////////////
						$mk_data=mktime(0, 0, 0, substr("0".$val[3],-2), '01', ($val[2]-543));
						$xx = date("Y-m-d", $mk_data);
			
						$bgnbackMonth= ($val[2]-543)."-".substr("0".$val[3],-2)."-01";
						$yy=($val[2]-543)."-".substr("0".$val[3],-2)."-".date("t",strtotime($bgnbackMonth));
			
						//ลบข้อมูลก่อน
						$cmddel =" delete from PER_ABSENTHIS 
											where ABS_STARTDATE between '$xx' and '$yy'
											and (AB_CODE=10 OR AB_CODE=13)
											and PER_ID in (
																	select p.PER_ID
																	from per_personal p, PER_ORG_ASS o
																	where p.ORG_ID=o.ORG_ID(+)
																	and (o.org_id in (select org_id from PER_ORG_ASS start with ORG_ID 
																								in (select org_id from PER_ORG_ASS  where org_id = $val[1])
																								CONNECT BY PRIOR org_id = ORG_ID_REF
																							)  and o.org_active=1
																		  ) 
															)";
															//echo $cmddel."<br>";
						$db_dpis_del->send_cmd($cmddel);
						
						
						// อัพเดท ประวัติการลา
						$cmdup =" insert into  PER_ABSENTHIS (
											ABS_ID,
											PER_ID,
											AB_CODE,
											ABS_STARTDATE,
											ABS_STARTPERIOD,
											ABS_ENDDATE,
											ABS_ENDPERIOD,
											ABS_DAY,
											UPDATE_USER,
											UPDATE_DATE
										)
										select abs_id,per_id,AB_CODE,ABS_STARTDATE,ABS_STARTPERIOD,
											ABS_ENDDATE,ABS_ENDPERIOD,ABS_DAY,UPDATE_USER,UPDATE_DATE
										 from(	
												select (select max(abs_id) from PER_ABSENTHIS)+rownum 
												abs_id,per_id,decode(work_flag,1,10,2,13) AB_CODE
												,to_char(work_date,'yyyy-mm-dd') ABS_STARTDATE, 3 ABS_STARTPERIOD
												,to_char(work_date,'yyyy-mm-dd') ABS_ENDDATE, 3 ABS_ENDPERIOD
												,1 ABS_DAY,-1 UPDATE_USER,to_char(sysdate,'yyyy-mm-dd hh24:mi:ss')  UPDATE_DATE,
												work_date
												from per_work_time
												where work_date between to_date('$xx','yyyy-mm-dd') and to_date('$yy','yyyy-mm-dd')
												and ((work_flag=2 and remark is null) or (work_flag=1 and remark is null))  AND ABSENT_FLAG=0 AND HOLIDAY_FLAG=0
												and per_id in (
												   select p.PER_ID
												   from per_personal p, PER_ORG_ASS o
												   where p.ORG_ID=o.ORG_ID(+)
													  and (o.org_id in (select org_id from PER_ORG_ASS start with ORG_ID 
												in
																			 (select org_id from PER_ORG_ASS  where 
												org_id = $val[1])
																		 CONNECT BY PRIOR org_id = ORG_ID_REF)  and 
												o.org_active=1
														   )
												)
										  ) 
							where per_id not in(select abs.per_id from PER_ABSENTHIS abs
														where to_char(work_date,'yyyy-mm-dd') between 
																 substr(abs.ABS_STARTDATE,1,10) and substr(abs.ABS_ENDDATE,1,10)) ";
										//echo "<pre>".$cmdup."<br>";
						$db_dpis_up->send_cmd($cmdup);
						
						
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลยืนยันข้อมูลรอบ [".$val[0]." : $SESS_USERID]");
					
					
					
					}
				}
			}
			
			
			

	}
	
	if($command=="DELETE"){

				
				$cmd = " select DEPARTMENT_ID,CLOSE_YEAR,CLOSE_MONTH
								 from PER_WORK_TIME where CONTROL_ID=$HID_CONTROL_ID";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$del_CLOSE_YEAR = $data[CLOSE_YEAR];
				$del_CLOSE_MONTH = $data[CLOSE_MONTH];
				$del_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลประมวลผล [$HID_CONTROL_ID :$del_DEPARTMENT_ID : $del_CLOSE_YEAR : $del_CLOSE_MONTH  ]");

				$cmd = " delete from PER_WORK_TIME  where  CONTROL_ID=$HID_CONTROL_ID ";
				$db_dpis->send_cmd($cmd);
		$command="";
				/*echo "<script>window.location='../admin/data_time_attendance.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";*/

 	} // end if*/
	
?>