<?php 
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$search_date_min=$_POST['PER_STARTDATE'];
$search_date_max=$_POST['PER_ENDDATE'];


	function CheckPublicHoliday($YYYY_MM_DD){
			global $DPISDB,$db_dpis;
			if($DPISDB=="odbc"){
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$YYYY_MM_DD' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
			}
			$IS_HOLIDAY = $db_dpis->send_cmd($cmd);
			if(!$IS_HOLIDAY){
				return false;
			}else{
				return true;
			}
		}

		if(trim($search_date_min)){
			$arr_temp = explode("/", $search_date_min);
			$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
			$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		} // end if

		if(trim($search_date_max)){
			$arr_temp = explode("/", $search_date_max);
			$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
			$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
		} // end if

if(trim($search_date_min) && trim($search_date_max)){
			$strStartDate = $search_date_min;//"2011-08-01";
			$strEndDate = $search_date_max;//"2011-08-15";
			//echo $strStartDate.'==='.$strStartDate;
			$intWorkDay = 0;
			$intHoliday = 0;
			$intPublicHoliday = 0;
			$intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate))/ ( 60 * 60 * 24 )) + 1;

			while (strtotime($strStartDate) <= strtotime($strEndDate)) {
				$DayOfWeek = date("w", strtotime($strStartDate));
				if($DayOfWeek == 0 or $DayOfWeek ==6){ // 0 = Sunday, 6 = Saturday;
					$intHoliday++;
					//echo "$strStartDate = <font color=red>Holiday</font><br>";
				}elseif(CheckPublicHoliday($strStartDate)){
					$intPublicHoliday++;
					//echo "$strStartDate = <font color=orange>Public Holiday</font><br>";
				}else{
					$intWorkDay++;
					//echo "$strStartDate = <b>Work Day</b><br>";
				}
				//$DayOfWeek = date("l", strtotime($strStartDate)); // return Sunday, Monday,Tuesday....
				$strStartDate = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)));
			}
		}else{
		$intWorkDay=0;
		$intPublicHoliday=0;
		}
	
		$data=$intPublicHoliday."|".$intWorkDay;
		
		echo $data;
  ?>              
				
