<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/session_start.php");
	include("../../php_scripts/function_share.php");
	include("../../php_scripts/load_per_control.php");			

    $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	 
	$CONTROL_ID_x = $_GET['CONTROL_ID'];
	
	$antabs = 0;
	$antabshis = 0;
	$antreq = 0;
	$antatt = 0;
	$cmd = " select 	col.CLOSE_YEAR,col.CLOSE_MONTH,
                        TO_CHAR(col.PROCESS_DATE,'yyyy-mm-dd hh24:mi:ss') AS PROCESS_DATE,
                        col.DEPARTMENT_ID
						from  PER_WORK_TIME_CONTROL col  
                    	where CONTROL_ID in($CONTROL_ID_x)
                     ";
                     //echo "<pre>".$cmd."<br>";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			
			 /*ใบลา*/
			$Stextyear = ($data[CLOSE_YEAR]-543)."-".substr("0".$data[CLOSE_MONTH],-2);
			$tmpdate = " AND '$Stextyear' between substr(abs.ABS_STARTDATE,1,7) AND substr(abs.ABS_ENDDATE,1,7) ";
			$cmdabs = " select count(abs.ABS_ID) as ANTABS  
							from PER_ABSENT abs 
							left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)    	
							where 		a.ORG_ID=".$data[DEPARTMENT_ID]."
									AND ((abs.APPROVE_FLAG = 0 or abs.APPROVE_FLAG is null) AND  abs.CANCEL_FLAG=0 )
									$tmpdate 
									AND abs.PER_ID not in(select PER_ID from TA_SET_EXCEPTPER 
																		where END_DATE IS NULL AND CANCEL_FLAG=1)"; 
			$db_dpis2->send_cmd($cmdabs);
			$dataabs = $db_dpis2->get_array();
			if($dataabs[ANTABS] > 0){
				$antabs ++;
			}
			
			/*ประวัติการลา*/
			$cmdabshis = " select count(abs.ABS_ID) as ANTABSHIS 
							from PER_ABSENTHIS abs 
							left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)    	
							where 		a.ORG_ID=".$data[DEPARTMENT_ID]."
									AND '".$data[PROCESS_DATE]."' < abs.UPDATE_DATE
									$tmpdate 
									"; 
			$db_dpis2->send_cmd($cmdabshis);
			$dataabshis = $db_dpis2->get_array();
			if($dataabshis[ANTABSHIS] > 0){
				$antabshis ++;
			}
			
			/*คำร้อง*/
			$cmdreq = " select count(abs.REC_ID) as ANTREG
							from TA_REQUESTTIME abs 
							left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)    	
							where 		a.ORG_ID=".$data[DEPARTMENT_ID]."
									AND abs.APPROVE_FLAG=0
									AND substr(abs.REQUEST_DATE,1,7)='$Stextyear'
									"; 
			$db_dpis2->send_cmd($cmdreq);
			$datareq = $db_dpis2->get_array();
			if($datareq[ANTREG] > 0){
				$antreq ++;
			}
			
			
			/*PER_TIME_ATTENDANCE*/
			$tmpdateatt = " AND '$Stextyear' between TO_CHAR(abs.TIME_STAMP,'yyyy-mm') AND TO_CHAR(abs.TIME_STAMP,'yyyy-mm') ";
			$cmdatt = " select count(abs.PER_ID) as ANTATT 
							from PER_TIME_ATTENDANCE abs 
							left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)    	
							where 		a.ORG_ID=".$data[DEPARTMENT_ID]."
									AND '".$data[PROCESS_DATE]."' < TO_CHAR(abs.ADJUST_DATE,'yyyy-mm-dd hh24:mi:ss')
									$tmpdateatt 
									"; 
			$db_dpis2->send_cmd($cmdatt);
			$dataatt = $db_dpis2->get_array();
			if($dataatt[ANTATT] > 0){
				$antatt ++;
			}

			
		}
	
	
	
	
	echo $antabs."<::>".$antabshis."<::>".$antreq."<::>".$antatt;
	 

?>