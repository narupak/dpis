<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

    //=============================================== ส่วนการทำงาน ลบข้อมูลการสแกนลงเวลา ============================================================
	$cmd = " select distinct to_char(sysdate,'YYYY/mm/dd') as date_now, to_char(sysdate,'HH24:MI:SS') as time_now ,to_char(sysdate,'yyyy') as yearnow , to_char(sysdate,'mmdd')  as datenow  from dual ";
	$db_dpis->send_cmd($cmd);
	if ($data_date = $db_dpis->get_array()) {
		$data_date = array_change_key_case($data_date, CASE_LOWER);
		$year_now = (trim($data_date[yearnow]));
		$date_nows = $year_now.trim($data_date[datenow]);
		if($date_nows>=$year_now.'1001'){ 
			$year = $year_now+1; 
			$old_year = $year-2;
		}else{
			$year = $year_now;
			$old_year = $year_now-2;
		}
	}
	$hidd_two_year_old = ($old_year+543)."1001";

	$cmd = " select substr(log_detail,-13,10) AS log_detail , substr(log_detail,1,17) as log_status, substr(log_date,1,11) as log_dates from user_log where log_detail  like '%ลบข้อมูลก่อนวันที่%' order by log_date desc ";
	$db_dpis->send_cmd($cmd);
	$datas = $db_dpis->get_array();
	$last_date = trim($datas[LOG_DETAIL]);
	$log_status = trim($datas[LOG_STATUS]);
	$arr_log_date = explode("-",trim($datas[LOG_DATES]));
	$last_log_date = $arr_log_date[2]."/".$arr_log_date[1]."/".$arr_log_date[0];

    //======================================== จบ ส่วนการทำงาน ลบข้อมูลการสแกนลงเวลา=============================================================
    if($command == ""){
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FULLTIME' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_FULLTIME = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_PARTTIME_AM' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_PARTTIME_AM = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_PARTTIME_PM' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_PARTTIME_PM = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_PARTTIME_PM' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_PARTTIME_PM = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_EXTRATIME' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_EXTRATIME = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_TIMEOVERLATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_TIMEOVERLATE = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_TIMEOVERLATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_TIMEOVERLATE = $data[CONFIG_VALUE];
			
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_NUMLATETIMES' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_NUMLATETIMES = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_SCANTYPE = $data[CONFIG_VALUE];

			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_NUMLATEPER' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_NUMLATEPER = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWORKDAY' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTWORKDAY = $data[CONFIG_VALUE];

			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTWEEKEND' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTWEEKEND = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATEWORKDAY' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTRATEWORKDAY = $data[CONFIG_VALUE];
    
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATEWEEKEND' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTRATEWEEKEND = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATETYPE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTRATETYPE = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTRATECAL' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTRATECAL = $data[CONFIG_VALUE];
    
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_FONT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_FONT = $data[CONFIG_VALUE];

			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SIZE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_SIZE = $data[CONFIG_VALUE];
    
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_NUMBERTYPE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_NUMBERTYPE = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISHEAD' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_DISHEAD = $data[CONFIG_VALUE];
    
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISFOOT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_DISFOOT = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISLEFT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_DISLEFT = $data[CONFIG_VALUE];
    
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_DISRIGHT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_DISRIGHT = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_ESIGN_ATT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_ESIGN_ATT = $data[CONFIG_VALUE];
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_ESIGN_OT' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_ESIGN_OT = $data[CONFIG_VALUE];
			
            $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='P_EXPIATE' ";
			$db_dpis->send_cmd($cmdChk);
			$data = $db_dpis->get_array();
			if($data){
				$cnt_chk=$data['CNT'];
				if($cnt_chk >0){        
					$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_EXPIATE' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_P_EXPIATE = $data[CONFIG_VALUE];
				}
			}
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'IS_OPEN_TIMEATT_ES' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_IS_OPEN_TIMEATT_ES = $data[CONFIG_VALUE];
			
			// kittiphat 16/11/2561
			$cmdChk ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'P_COL_OTSCAN'";
			$db_dpis->send_cmd($cmdChk);
			$dataOTSCAN = $db_dpis->get_array();
			
			if($dataOTSCAN[CNT]=="0"){
				$cmdM = "SELECT MAX(CONFIG_ID) AS MAXOFCONFIG_ID FROM SYSTEM_CONFIG";
				$db_dpis->send_cmd($cmdM);
				$dataMax = $db_dpis->get_array();
				$MAXOFCONFIG_ID =  $dataMax[MAXOFCONFIG_ID];
				
				$MAXOFCONFIG_ID = $MAXOFCONFIG_ID+1;
				$cmdA = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
								VALUES ($MAXOFCONFIG_ID,'P_COL_OTSCAN','T','T=แสดงจากเครื่องบันทึกเวลา, P=แสดงจากการ Process')";
				$db_dpis->send_cmd($cmdA);
		
			}
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_COL_OTSCAN' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_COL_OTSCAN = $data[CONFIG_VALUE];
			
			// kittiphat 27/11/2561
			$cmdChk ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'P_OTTYPE_ORGANIZE'";
			$db_dpis->send_cmd($cmdChk);
			$dataOTTYPEORGANIZE = $db_dpis->get_array();
			
			if($dataOTTYPEORGANIZE[CNT]=="0"){
				$cmdM = "SELECT MAX(CONFIG_ID) AS MAXOFCONFIG_ID FROM SYSTEM_CONFIG";
				$db_dpis->send_cmd($cmdM);
				$dataMax = $db_dpis->get_array();
				$MAXOFCONFIG_ID =  $dataMax[MAXOFCONFIG_ID];
				
				$MAXOFCONFIG_ID = $MAXOFCONFIG_ID+1;
				$cmdA = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
								VALUES ($MAXOFCONFIG_ID,'P_OTTYPE_ORGANIZE','1','1=โครงสร้างตามกฏหมาย, 2=โครงสร้างตามมอบหมาย')";
				$db_dpis->send_cmd($cmdA);
		
			}
			
			$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_OTTYPE_ORGANIZE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_P_OTTYPE_ORGANIZE = $data[CONFIG_VALUE];
			
			
			
	}

	
	
	if($command == "ES_UPDATESYSTEMPARAMETER"){
			$P_FULLTIME = $TMP_P_FULLTIME;
			$cmd = " update system_config set 
							config_value = '$P_FULLTIME'
						 where config_name = 'P_FULLTIME' ";
			$db->send_cmd($cmd);
			
			$P_PARTTIME_AM = $TMP_P_PARTTIME_AM;
			$cmd = " update system_config set 
							config_value = '$P_PARTTIME_AM'
						 where config_name = 'P_PARTTIME_AM' ";
			$db->send_cmd($cmd);
			
			$P_PARTTIME_PM = $TMP_P_PARTTIME_PM;
			$cmd = " update system_config set 
							config_value = '$P_PARTTIME_PM'
						 where config_name = 'P_PARTTIME_PM' ";
			$db->send_cmd($cmd);
			
			$P_EXTRATIME = $TMP_P_EXTRATIME;
			$cmd = " update system_config set 
							config_value = '$P_EXTRATIME'
						 where config_name = 'P_EXTRATIME' ";
			$db->send_cmd($cmd);
			
			$P_TIMEOVERLATE = $TMP_P_TIMEOVERLATE;
			$cmd = " update system_config set 
							config_value = '$P_TIMEOVERLATE'
						 where config_name = 'P_TIMEOVERLATE' ";
			$db->send_cmd($cmd);
			
			$P_SCANTYPE = $TMP_P_SCANTYPE;
			$cmd = " update system_config set 
							config_value = '$P_SCANTYPE'
						 where config_name = 'P_SCANTYPE' ";
			$db->send_cmd($cmd);
			
			$P_NUMLATETIMES = $TMP_P_NUMLATETIMES;
			$cmd = " update system_config set 
							config_value = '$P_NUMLATETIMES'
						 where config_name = 'P_NUMLATETIMES' ";
			$db->send_cmd($cmd);

                        $P_NUMLATEPER = $TMP_P_NUMLATEPER;
			$cmd = " update system_config set 
							config_value = '$P_NUMLATEPER'
						 where config_name = 'P_NUMLATEPER' ";
			$db->send_cmd($cmd);

			$cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='P_EXPIATE' ";
			$db_dpis->send_cmd($cmdChk);
			$data = $db_dpis->get_array();
			if($data){
				$cnt_chk=$data['CNT'];
				if($cnt_chk >0){  

					if($TMP_P_EXPIATE=='Y'){
						$P_EXPIATE = "Y";
					}else{
						$P_EXPIATE = "N";
					}
					$cmd = " update system_config set 
									config_value = '$P_EXPIATE'
								 where config_name = 'P_EXPIATE' ";
					$db->send_cmd($cmd);
				}
			}
			
			
			/*if($TMP_IS_OPEN_TIMEATT_ES=='OPEN'){
				$IS_OPEN_TIMEATT_ES = "OPEN";
			}else{
				$IS_OPEN_TIMEATT_ES = "CLOSE";
			}
			$cmd = " update system_config set 
							config_value = '$IS_OPEN_TIMEATT_ES'
						 where config_name = 'IS_OPEN_TIMEATT_ES' ";
			$db->send_cmd($cmd);*/
			
			$command ="";
			echo "<script>window.location='../admin/es_t0101_config_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	}
	
	if($command == "ES_UPDATESYSTEMPARAMETER1"){
			$P_OTWORKDAY = $TMP_P_OTWORKDAY;
			$cmd = " update system_config set 
							config_value = '$P_OTWORKDAY'
						 where config_name = 'P_OTWORKDAY' ";
			$db->send_cmd($cmd);

			$P_OTWEEKEND = $TMP_P_OTWEEKEND;
			$cmd = " update system_config set 
							config_value = '$P_OTWEEKEND'
						 where config_name = 'P_OTWEEKEND' ";
			$db->send_cmd($cmd);
			
			$P_OTRATEWORKDAY = $TMP_P_OTRATEWORKDAY;
			$cmd = " update system_config set 
							config_value = '$P_OTRATEWORKDAY' 
						 where config_name = 'P_OTRATEWORKDAY' ";
			$db->send_cmd($cmd);
    
   			$P_OTRATEWEEKEND = $TMP_P_OTRATEWEEKEND;
			$cmd = " update system_config set 
							config_value = '$P_OTRATEWEEKEND'
						 where config_name = 'P_OTRATEWEEKEND' ";
			$db->send_cmd($cmd);
			
			$P_OTRATETYPE = $TMP_P_OTRATETYPE;
			$cmd = " update system_config set 
							config_value = '$P_OTRATETYPE'
						 where config_name = 'P_OTRATETYPE' ";
			$db->send_cmd($cmd);
			
			$P_OTRATECAL = $TMP_P_OTRATECAL;
			$cmd = " update system_config set 
							config_value = '$P_OTRATECAL' 
						 where config_name = 'P_OTRATECAL' ";
			$db->send_cmd($cmd);
			
			$P_COL_OTSCAN = $TMP_P_COL_OTSCAN;
			$cmd = " update system_config set 
							config_value = '$P_COL_OTSCAN' 
						 where config_name = 'P_COL_OTSCAN' ";
			$db->send_cmd($cmd);
			
			$P_OTTYPE_ORGANIZE = $TMP_P_OTTYPE_ORGANIZE;
			$cmd = " update system_config set 
							config_value = '$P_OTTYPE_ORGANIZE' 
						 where config_name = 'P_OTTYPE_ORGANIZE' ";
			$db->send_cmd($cmd);
			
			$command ="";
			echo "<script>window.location='../admin/es_t0101_config_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	}
	
	if($command == "ES_UPDATESYSTEMPARAMETER2"){

			$P_FONT = $TMP_P_FONT;
			$cmd = " update system_config set 
							config_value = '$P_FONT'
						 where config_name = 'P_FONT' ";
			$db->send_cmd($cmd);

    		$P_SIZE = $TMP_P_SIZE;
			$cmd = " update system_config set 
							config_value = '$P_SIZE'
						 where config_name = 'P_SIZE' ";
			$db->send_cmd($cmd);
    
			$P_NUMBERTYPE = $TMP_P_NUMBERTYPE;
			$cmd = " update system_config set 
							config_value = '$P_NUMBERTYPE'
						 where config_name = 'P_NUMBERTYPE' ";
			$db->send_cmd($cmd);
			
			$P_DISHEAD = $TMP_P_DISHEAD;
			$cmd = " update system_config set 
							config_value = '$P_DISHEAD'
						 where config_name = 'P_DISHEAD' ";
			$db->send_cmd($cmd);
    
			$P_DISFOOT = $TMP_P_DISFOOT;
			$cmd = " update system_config set 
							config_value = '$P_DISFOOT'
						 where config_name = 'P_DISFOOT' ";
			$db->send_cmd($cmd);
			
			$P_DISLEFT = $TMP_P_DISLEFT;
			$cmd = " update system_config set 
							config_value = '$P_DISLEFT'
						 where config_name = 'P_DISLEFT' ";
			$db->send_cmd($cmd);
    
    		$P_DISRIGHT = $TMP_P_DISRIGHT;
			$cmd = " update system_config set 
							config_value = '$P_DISRIGHT'
						 where config_name = 'P_DISRIGHT' ";
			$db->send_cmd($cmd);
			

			if($TMP_P_ESIGN_ATT=='Y'){
				$P_ESIGN_ATT = "Y";
			}else{
				$P_ESIGN_ATT = "N";
			}
			$cmd = " update system_config set 
							config_value = '$P_ESIGN_ATT'
						 where config_name = 'P_ESIGN_ATT' ";
			$db->send_cmd($cmd);
			
			
			if($TMP_P_ESIGN_OT=='Y'){
				$P_ESIGN_OT = "Y";
			}else{
				$P_ESIGN_OT = "N";
			}
			$cmd = " update system_config set 
							config_value = '$P_ESIGN_OT'
						 where config_name = 'P_ESIGN_OT' ";
			$db->send_cmd($cmd);
			
			
			$command ="";
			echo "<script>window.location='../admin/es_t0101_config_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

	}
	//======================================== ส่วนการทำงาน ลบข้อมูลการสแกนลงเวลา ============================================================
	if($command == "ES_DELETEPICTURE"){
		$cmd = "delete from ta_unknown_pic where time_stamp < add_months(to_date(('$year')||'-10-01','yyyy-mm-dd') ,-24)";
		$db_dpis->send_cmd($cmd);
		$cmd = "delete from per_time_attendance where time_stamp < add_months(to_date(('$year')||'-10-01','yyyy-mm-dd') ,-24)";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลก่อนวันที่ [ ".$STARTDATE." ] ");
		echo '<script type="text/javascript">  setTimeout(function(){ document.getElementById(\'alet_text\').innerHTML = "<font color=\'blue\'>ลบข้อมูลก่อนวันที่ [ '.$STARTDATE.' ] เรียบร้อยแล้ว</font>" }, 500); </script>';
		$command ="";
		//echo "<script>window.location='../admin/es_t0101_config_frm.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";
		$STARTDATE = "";
	}
    //======================================== จบ ส่วนการทำงาน ลบข้อมูลการสแกนลงเวลา ==========================================================	
?>