<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        /*Release 5.2.1.3 Begin*/
        $cmd_max="SELECT MAX(CONFIG_ID) AS MAXOFCONFIG_ID FROM SYSTEM_CONFIG";
        $db->send_cmd($cmd_max);
        $data = $db->get_array();
        if($data){
            $MAXOFCONFIG_ID=$data['MAXOFCONFIG_ID'];
        }
        
        $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='IS_OPEN_TIMEATT_ES' ";
        $db->send_cmd($cmdChk);
        $data = $db->get_array();
        if($data){
            $cnt_chk=$data['CNT'];
            if($cnt_chk==0){
                $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
                $cmd_insert ="INSERT INTO SYSTEM_CONFIG(CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                          VALUES(".$MAXOFCONFIG_ID.",'IS_OPEN_TIMEATT_ES','CLOSE','กำหนดการเปิดใช้งานระบบลงเวลา OPEN=เปิดใช้งานระบบลงเวลา CLOSE=ปิดการใช้งานระบบลงเวลา')";
                $db->send_cmd($cmd_insert);
            }
        }
        $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='P_ESIGN_ATT' ";
        $db->send_cmd($cmdChk);
        $data = $db->get_array();
        if($data){
            $cnt_chk=$data['CNT'];
            if($cnt_chk==0){
                $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
                $cmd_insert ="INSERT INTO SYSTEM_CONFIG(CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                          VALUES(".$MAXOFCONFIG_ID.",'P_ESIGN_ATT','N','N = ไม่ใช้ Y= ใช้')";
                $db->send_cmd($cmd_insert);
            }
        }
        $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='P_ESIGN_OT' ";
        $db->send_cmd($cmdChk);
        $data = $db->get_array();
        if($data){
            $cnt_chk=$data['CNT'];
            if($cnt_chk==0){
                $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
                $cmd_insert ="INSERT INTO SYSTEM_CONFIG(CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                          VALUES(".$MAXOFCONFIG_ID.",'P_ESIGN_OT','N','N = ไม่ใช้ Y= ใช้')";
                $db->send_cmd($cmd_insert);
            }
        }
        $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='SHOW_GENERAL' ";
        $db->send_cmd($cmdChk);
        $data = $db->get_array();
        if($data){
            $cnt_chk=$data['CNT'];
            if($cnt_chk==0){
                $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
                $cmd_insert ="INSERT INTO SYSTEM_CONFIG(CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                          VALUES(".$MAXOFCONFIG_ID.",'SHOW_GENERAL','Y','แสดงข้อมูลทั่วไป(ใช้เฉพาะข้าราชการ) N =ไม่แสดง Y=แสดง')";
                $db->send_cmd($cmd_insert);
                $SHOW_GENERAL='Y';
            }
        }
        /*Release 5.2.1.3 End*/
		
        /*Release 5.2.1.9 Begin*/
        $cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='IS_ACCEPT_CONFIG' ";
        $db->send_cmd($cmdChk);
        $data = $db->get_array();
        if($data){
            $cnt_chk=$data['CNT'];
            if($cnt_chk==0){
                $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
                $cmd_insert ="INSERT INTO SYSTEM_CONFIG(CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                          VALUES(".$MAXOFCONFIG_ID.",'IS_ACCEPT_CONFIG','0','แสดงช่องยอมรับผลการประเมิน 0 =ไม่แสดง 1=แสดง')";
                $db->send_cmd($cmd_insert);
                $SHOW_GENERAL='Y';
            }
        }
        /*Release 5.2.1.9 End*/
        
        /*Release 5.2.1.10 Begin*/
        $cmdChk ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'ORG_SHORT_NAME'";
        $db_dpis->send_cmd($cmdChk);
        $dataChkN = $db_dpis->get_array();
        if($dataChkN[CNT]==0){
            $MAXOFCONFIG_ID = $MAXOFCONFIG_ID+1;
            $cmdA = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                            VALUES ($MAXOFCONFIG_ID,'ORG_SHORT_NAME','Y','แสดงชื่อย่อส่วนราชการในใบลา')";
            $db_dpis->send_cmd($cmdA);
        }
        
        /*Release 5.2.1.10 End*/
		
		/*Release 5.2.1.14 Begin*/
        $cmdChk ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'CH_SCORE_DAY'";
        $db_dpis->send_cmd($cmdChk);
        $dataChkN = $db_dpis->get_array();
        if($dataChkN[CNT]==0){
            $MAXOFCONFIG_ID = $MAXOFCONFIG_ID+1;
            $cmdA = "INSERT INTO SYSTEM_CONFIG (CONFIG_ID, CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
                            VALUES ($MAXOFCONFIG_ID,'CH_SCORE_DAY','Y','ไม่แสดงวันที่ในแบบสรุปผลการประเมินการปฏิบัติราชการ')";
            $db_dpis->send_cmd($cmdA);
        }
        /*Release 5.2.1.14 End*/
        
	// print_r($_POST);

	$cmd =" select config_value from system_config where config_name='COMPETENCY_METHOD' ";
	$db->send_cmd($cmd);
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$COMPETENCY_METHOD=trim($data['config_value']);
	$tmp_competency_method = explode("||", $COMPETENCY_METHOD);
	for ($i=1; $i<count($tmp_competency_method)-1; $i++) {
		if ($tmp_competency_method[$i]==1)		$chk_competency_method1 = 1;
		if ($tmp_competency_method[$i]==2)		$chk_competency_method2 = 2;
		if ($tmp_competency_method[$i]==3)		$chk_competency_method3 = 3;
		if ($tmp_competency_method[$i]==4)		$chk_competency_method4 = 4;
	}

	$cmd =" select config_value from system_config where config_name='E_SIGN' ";
	$db->send_cmd($cmd);
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$E_SIGN=trim($data['config_value']);
	$tmp_e_sign = explode("||", $E_SIGN);
	for ($i=1; $i<count($tmp_e_sign)-1; $i++) {
		if ($tmp_e_sign[$i]==1)		$chk_e_sign1 = 1;
		if ($tmp_e_sign[$i]==2)		$chk_e_sign2 = 2;
		if ($tmp_e_sign[$i]==3)		$chk_e_sign3 = 3;
		if ($tmp_e_sign[$i]==4)		$chk_e_sign4 = 4;
	}
	if($command == "CHANGEPERCENT" && trim($search_budget_year) && trim($search_kf_cycle)){
		if ($search_kf_cycle==1) {
			$KF_START_DATE = ($search_budget_year - 543 - 1) . "-10-01";
			$KF_END_DATE = ($search_budget_year - 543) . "-03-31";
		} elseif ($search_kf_cycle==2) {
			$KF_START_DATE = ($search_budget_year - 543) . "-04-01";
			$KF_END_DATE = ($search_budget_year - 543) . "-09-30";
		}
		$cmd = " select KF_ID, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, PER_TYPE, OT_CODE
						from PER_KPI_FORM a, PER_PERSONAL b 
						where a.PER_ID = b.PER_ID and KF_CYCLE = $search_kf_cycle and PER_TYPE = $search_per_type and KF_START_DATE = '$KF_START_DATE' and 
						KF_END_DATE = '$KF_END_DATE' ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		while($data1 = $db_dpis1->get_array()){
			$KF_ID = $data1[KF_ID];
			$PERFORMANCE_WEIGHT = $data1[PERFORMANCE_WEIGHT];
			$COMPETENCE_WEIGHT = $data1[COMPETENCE_WEIGHT];
			$OTHER_WEIGHT = $data1[OTHER_WEIGHT];
			$PER_TYPE = $data1[PER_TYPE];
			$OT_CODE = trim($data1[OT_CODE]);
			if ($PER_TYPE==1) {
				$PERFORMANCE_WEIGHT = ($CH_WEIGHT_KPI)?$CH_WEIGHT_KPI:"NULL"; 
				$COMPETENCE_WEIGHT = ($CH_WEIGHT_COMPETENCE)?$CH_WEIGHT_COMPETENCE:"NULL"; 
				$OTHER_WEIGHT = ($CH_WEIGHT_OTHER)?$CH_WEIGHT_OTHER:"NULL";
			} elseif ($PER_TYPE==3 && $OT_CODE=="08") {
				$PERFORMANCE_WEIGHT = ($CH_WEIGHT_KPI_E)?$CH_WEIGHT_KPI_E:"NULL"; 
				$COMPETENCE_WEIGHT = ($CH_WEIGHT_COMPETENCE_E)?$CH_WEIGHT_COMPETENCE_E:"NULL"; 
				$OTHER_WEIGHT = ($CH_WEIGHT_OTHER_E)?$CH_WEIGHT_OTHER_E:"NULL";
			} elseif ($PER_TYPE==3 && $OT_CODE=="09") {
				$PERFORMANCE_WEIGHT = ($CH_WEIGHT_KPI_S)?$CH_WEIGHT_KPI_S:"NULL"; 
				$COMPETENCE_WEIGHT = ($CH_WEIGHT_COMPETENCE_S)?$CH_WEIGHT_COMPETENCE_S:"NULL"; 
				$OTHER_WEIGHT = ($CH_WEIGHT_OTHER_S)?$CH_WEIGHT_OTHER_S:"NULL";
			}
			$cmd = " UPDATE PER_KPI_FORM SET
								PERFORMANCE_WEIGHT=$PERFORMANCE_WEIGHT, 
								COMPETENCE_WEIGHT=$COMPETENCE_WEIGHT, 
								OTHER_WEIGHT=$OTHER_WEIGHT
								WHERE KF_ID=$KF_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
		} // end while
		$command = "UPDATESYSTEMPARAMETER";
	}

	if($command == "UPDATESYSTEMPARAMETER"){
/*		if ($RPT_N)
			$cmd = " UPDATE PER_LEVEL SET LEVEL_ACTIVE = 0 WHERE LEVEL_NO in ('01','02','03','04','05','06','07','08','09','10','11') ";
		else
			$cmd = " UPDATE PER_LEVEL SET LEVEL_ACTIVE = 1 WHERE LEVEL_NO in ('01','02','03','04','05','06','07','08','09','10','11') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_LEVEL SET LEVEL_ACTIVE = 1 WHERE LEVEL_NO in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		else
			$cmd = " UPDATE PER_LEVEL SET LEVEL_ACTIVE = 0 WHERE LEVEL_NO in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_CO_LEVEL SET CL_ACTIVE = 0 WHERE LEVEL_NO_MIN in ('01','02','03','04','05','06','07','08','09','10','11') ";
		else
			$cmd = " UPDATE PER_CO_LEVEL SET CL_ACTIVE = 1 WHERE LEVEL_NO_MIN in ('01','02','03','04','05','06','07','08','09','10','11') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_CO_LEVEL SET CL_ACTIVE = 1 WHERE LEVEL_NO_MIN in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		else
			$cmd = " UPDATE PER_CO_LEVEL SET CL_ACTIVE = 0 WHERE LEVEL_NO_MIN in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_LINE SET PL_ACTIVE = 0 WHERE PL_CODE like '0%' ";
		else
			$cmd = " UPDATE PER_LINE SET PL_ACTIVE = 1 WHERE PL_CODE like '0%' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_LINE SET PL_ACTIVE = 1 WHERE PL_CODE like '5%' ";
		else
			$cmd = " UPDATE PER_LINE SET PL_ACTIVE = 0 WHERE PL_CODE like '5%' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_MGTSALARY SET MS_ACTIVE = 0 WHERE LEVEL_NO in ('01','02','03','04','05','06','07','08','09','10','11') ";
		else
			$cmd = " UPDATE PER_MGTSALARY SET MS_ACTIVE = 1 WHERE LEVEL_NO in ('01','02','03','04','05','06','07','08','09','10','11') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;

		if ($RPT_N)
			$cmd = " UPDATE PER_MGTSALARY SET MS_ACTIVE = 1 WHERE LEVEL_NO in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		else
			$cmd = " UPDATE PER_MGTSALARY SET MS_ACTIVE = 0 WHERE LEVEL_NO in ('O1','O2','O3','O4','K1','K2','K3','K4','K5','D1','D2','M1','M2') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error() ;
*/
		$cmd = " select CTRL_TYPE from PER_CONTROL ";
		$count_ctrl = $db_dpis->send_cmd($cmd);
		if($count_ctrl && $FIX_CONTROL){
			$cmd = " update PER_CONTROL set FIX_CONTROL = $FIX_CONTROL ";
			$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		} // end if
	} // end if

		
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_DAY' ";
		$count_data = $db_dpis->send_cmd($cmd);
		if (!$count_data){
		$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
		$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
				values ('$MAXOFCONFIG_ID', 'NUMBER_OF_DAY', '23', 'จำนวนวัน') ";
		$db_dpis->send_cmd($cmd);
		}

	
		
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_TIME' ";
		$count_data = $db_dpis->send_cmd($cmd);
		if(!$count_data){
			$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
			$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					 values ('$MAXOFCONFIG_ID', 'NUMBER_OF_TIME', '10', 'จำนวนครั้ง') ";
			$db_dpis->send_cmd($cmd);
	}

		
		$cmd = " select config_name from system_config where config_name = 'NUMBER_OF_LATE' ";
		$count_data = $db->send_cmd($cmd);
		if (!$count_data){
				$MAXOFCONFIG_ID =  $MAXOFCONFIG_ID + 1;
				$cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
					values ('$MAXOFCONFIG_ID', 'NUMBER_OF_LATE', '18', 'จำนวนครั้งที่สาย') ";
					$db_dpis->send_cmd($cmd);
		}
		



	$cmd = " select FIX_CONTROL from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$FIX_CONTROL = $data[FIX_CONTROL];
	
	
	$cmd = " select config_name from system_config where CONFIG_NAME = 'EXTRA_ABSEN_DAY'";
	$CNT_EXTRA_ABSEN_DAY = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	
	
	

?>