<?php

include("../php_scripts/connect_database.php");
include("php_scripts/session_start.php");
include("php_scripts/function_share.php");
include("php_scripts/function_list.php");
include("php_scripts/load_per_control.php");

$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis_up = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis_del = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
// ==========================

$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

$UPDATE_DATE = date("Y-m-d H:i:s");
$CHK_DATE = date("Y-m-d");

if ($command == "UNLOCK") {
    $cmdUnlock =" UPDATE PER_WORK_TIME_CONTROL 
                    SET CLOSE_DATE = NULL,APPROVE_DATE = NULL 
                  WHERE CONTROL_ID=$CRL_ID";
    $db_dpis->send_cmd($cmdUnlock);
	
	$mk_data=mktime(0, 0, 0, substr("0".$HID_CLOSE_MONTH,-2), '01', ($HID_CLOSE_YEAR-543));
				$xx = date("Y-m-d", $mk_data);
	
				$bgnbackMonth= ($HID_CLOSE_YEAR-543)."-".substr("0".$HID_CLOSE_MONTH,-2)."-01";
				$yy=($HID_CLOSE_YEAR-543)."-".substr("0".$HID_CLOSE_MONTH,-2)."-".date("t",strtotime($bgnbackMonth));
	//ลบข้อมูลก่อน
	$cmddel =" delete from PER_ABSENTHIS 
						where ABS_STARTDATE between '$xx' and '$yy'
						and (AB_CODE=10 OR AB_CODE=13)
						and PER_ID in (
												select p.PER_ID
												from per_personal p, PER_ORG_ASS o
												where p.ORG_ID=o.ORG_ID(+)
												and (o.org_id in (select org_id from PER_ORG_ASS start with ORG_ID 
																			in (select org_id from PER_ORG_ASS  where org_id = $HID_DEPARTMENT_ID)
																			CONNECT BY PRIOR org_id = ORG_ID_REF
																		)  and o.org_active=1
													  ) 
										)";
										//echo $cmddel."<br>";
	$db_dpis_del->send_cmd($cmddel);
	
    $command='';
}

if ($command == "CLOSEJOB") {
    $cntchkClose= count($chkClose);
    if($cntchkClose>0){
        for($idx=0;$idx<=$cntchkClose;$idx++){
			if($chkClose[$idx]){
				$val =  explode("_",$chkClose[$idx]);
				$cmdClose =" UPDATE PER_WORK_TIME_CONTROL 
						SET APPROVE_DATE = SYSDATE , APPROVE_USER = $SESS_USERID  
					  WHERE CONTROL_ID=".$val[0];
    			$db_dpis->send_cmd($cmdClose);
				
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
										and ((work_flag=2 and remark is null) or (work_flag=1 and remark is null)) AND ABSENT_FLAG=0 AND HOLIDAY_FLAG=0
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
								//echo $cmdup."<br>";
				$db_dpis_up->send_cmd($cmdup);
				
				
			}
			
			
			
			
        }
    }
    $command='';
}

?>
