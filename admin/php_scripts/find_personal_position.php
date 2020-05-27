<?
//	include("../php_scripts/connect_database.php");
//	include("php_scripts/session_start.php");

	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);			

	//echo "POS_ID=$POS_ID || PER_TYPE=$PER_TYPE<br>";
	$PER_MGTSALARY = "0.00";

	if($PER_TYPE == 1){				// ข้าราชการ
		$cmd = " select 	ORG_ID, POS_NO, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, 
						CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
						POS_STATUS ,ORG_ID_3,ORG_ID_4,ORG_ID_5 
				from 	PER_POSITION where POS_ID=$POS_ID  ";	
                
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NO = trim($data[POS_NO]);

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";			
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PM_NAME = trim($data3[PM_NAME]);
		
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PL_NAME = trim($data3[PL_NAME]);
		
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PT_NAME = trim($data3[PT_NAME]);

		$CL_NAME = trim($data[CL_NAME]);
		$CL_CODE = $CL_NAME;
		$POS_SALARY = trim($data[POS_SALARY]);
		$PER_MGTSALARY = trim($data[POS_MGTSALARY]);
		if (!trim($PER_MGTSALARY)) {
			// ===== ถ้าไม่มี PER_MGTSALARY  ให้เลือกจาก table PER_MGTSALARY 
			$cmd = "	select 	MS_SALARY 			
						from	PER_MGTSALARY a, PER_TYPE b 
						where	LEVEL_NO=$LEVEL_NO and PT_CODE= '$PT_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			if (trim($data3[MS_SALARY]))
				$PER_MGTSALARY = number_format($data3[MS_SALARY], 2, '.', ',');
		}

		$SKILL_CODE = trim($data[SKILL_CODE]);
		$cmd = " select SKILL_NAME, SG_CODE from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$SKILL_NAME = trim($data3[SKILL_NAME]);
		
		$SG_CODE = trim($data3[SG_CODE]);
		$cmd = " select SG_NAME from PER_SKILL_GROUP where trim(SG_CODE)='$SG_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();			
		$SG_NAME = trim($data3[SG_NAME]);
				
		$POS_STATUS = trim($data[POS_STATUS]);
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME = trim($data_dpis3[ORG_NAME]);

		$AP_CODE_POS = trim($data_dpis3[AP_CODE]);
		$PV_CODE_POS = trim($data_dpis3[PV_CODE]);
		$CT_CODE_POS = trim($data_dpis3[CT_CODE]);
		$ORG_TYPE_CODE = trim($data_dpis3[OT_CODE]); 		
		$DEPARTMENT_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$AP_NAME_POS = trim($data_dpis3[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$PV_NAME_POS = trim($data_dpis3[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$CT_NAME_POS = trim($data_dpis3[CT_NAME]);

		// ===== สังกัด =====
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_TYPE_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_TYPE_NAME = trim($data_dpis3[OT_NAME]);	

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_1 = trim($data_dpis3[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_2 = trim($data_dpis3[ORG_NAME]);
                
                /*Release 5.1.0.5 BEgin เพิ่มเติม*/
                $ORG_ID_3 = trim($data[ORG_ID_3]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_3 = trim($data_dpis3[ORG_NAME]);
                
                $ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_4 = trim($data_dpis3[ORG_NAME]);
                
                
                $ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_5 = trim($data_dpis3[ORG_NAME]);
                /*Release 5.1.0.5 End*/
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$DEPARTMENT_NAME = trim($data_dpis3[ORG_NAME]);
		$MINISTRY_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$MINISTRY_NAME = trim($data_dpis3[ORG_NAME]);
	} // end if ข้าราชการ

	if ($PER_TYPE == 2) {			// ลุกจ้างประจำ
		$cmd = " select 	ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, 
								POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS ,ORG_ID_3,ORG_ID_4,ORG_ID_5 
				from 	PER_POS_EMP where POEM_ID=$POS_ID  ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NO = trim($data[POEM_NO]);
	
		$PN_CODE = trim($data[PN_CODE]);
		$PL_CODE = $PN_CODE;		//set hidden
		$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PL_NAME = trim($data3[PN_NAME]);
		
		$POS_STATUS = trim($data[POEM_STATUS]);
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME = trim($data_dpis3[ORG_NAME]);

		$AP_CODE_POS = trim($data_dpis3[AP_CODE]);
		$PV_CODE_POS = trim($data_dpis3[PV_CODE]);
		$CT_CODE_POS = trim($data_dpis3[CT_CODE]);
		$ORG_TYPE_CODE = trim($data_dpis3[OT_CODE]); 		
		$DEPARTMENT_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$AP_NAME_POS = trim($data_dpis3[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$PV_NAME_POS = trim($data_dpis3[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$CT_NAME_POS = trim($data_dpis3[CT_NAME]);

		// ===== สังกัด =====
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_TYPE_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_TYPE_NAME = trim($data_dpis3[OT_NAME]);	

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_1 = trim($data_dpis3[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_2 = trim($data_dpis3[ORG_NAME]);
                
                /*Release 5.1.0.5 BEgin เพิ่มเติม*/
                $ORG_ID_3 = trim($data[ORG_ID_3]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_3 = trim($data_dpis3[ORG_NAME]);
                
                $ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_4 = trim($data_dpis3[ORG_NAME]);
                
                
                $ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_5 = trim($data_dpis3[ORG_NAME]);
                /*Release 5.1.0.5 End*/
                
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$DEPARTMENT_NAME = trim($data_dpis3[ORG_NAME]);
		$MINISTRY_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$MINISTRY_NAME = trim($data_dpis3[ORG_NAME]);
	}	// end if ลูกจ้างประจำ

	if ($PER_TYPE == 3) {			// พนักงานราชการ
		$cmd = " select 	ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, 
								POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS ,ORG_ID_3,ORG_ID_4,ORG_ID_5 
				from 	PER_POS_EMPSER where POEMS_ID=$POS_ID  ";				
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NO = trim($data[POEMS_NO]);
	
		$EP_CODE = trim($data[EP_CODE]);
		$PL_CODE = $EP_CODE;		//set hidden
		$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PL_NAME = trim($data3[EP_NAME]);
		
		$POS_STATUS = trim($data[POEM_STATUS]);
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME = trim($data_dpis3[ORG_NAME]);

		$AP_CODE_POS = trim($data_dpis3[AP_CODE]);
		$PV_CODE_POS = trim($data_dpis3[PV_CODE]);
		$CT_CODE_POS = trim($data_dpis3[CT_CODE]);
		$ORG_TYPE_CODE = trim($data_dpis3[OT_CODE]); 		
		$DEPARTMENT_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$AP_NAME_POS = trim($data_dpis3[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$PV_NAME_POS = trim($data_dpis3[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$CT_NAME_POS = trim($data_dpis3[CT_NAME]);

		// ===== สังกัด =====
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_TYPE_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_TYPE_NAME = trim($data_dpis3[OT_NAME]);	

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_1 = trim($data_dpis3[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_2 = trim($data_dpis3[ORG_NAME]);
                
                /*Release 5.1.0.5 BEgin เพิ่มเติม*/
                $ORG_ID_3 = trim($data[ORG_ID_3]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_3 = trim($data_dpis3[ORG_NAME]);
                
                $ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_4 = trim($data_dpis3[ORG_NAME]);
                
                
                $ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_5 = trim($data_dpis3[ORG_NAME]);
                /*Release 5.1.0.5 End*/
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$DEPARTMENT_NAME = trim($data_dpis3[ORG_NAME]);
		$MINISTRY_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$MINISTRY_NAME = trim($data_dpis3[ORG_NAME]);
	}	// end if พนักงานราชการ

	if ($PER_TYPE == 4) {			// ลุกจ้างชั่วคราว
		$cmd = " select 	ORG_ID, POT_NO, ORG_ID_1, ORG_ID_2, TP_CODE, 
								POT_MIN_SALARY, POT_MAX_SALARY, POT_STATUS ,ORG_ID_3,ORG_ID_4,ORG_ID_5 
				from 	PER_POS_TEMP where POT_ID=$POS_ID  ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NO = trim($data[POT_NO]);
	
		$TP_CODE = trim($data[TP_CODE]);
		$PL_CODE = $TP_CODE;		//set hidden
		$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$PL_NAME = trim($data3[TP_NAME]);
		
		$POS_STATUS = trim($data[POT_STATUS]);
		
		$ORG_ID = trim($data[ORG_ID]);
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME = trim($data_dpis3[ORG_NAME]);

		$AP_CODE_POS = trim($data_dpis3[AP_CODE]);
		$PV_CODE_POS = trim($data_dpis3[PV_CODE]);
		$CT_CODE_POS = trim($data_dpis3[CT_CODE]);
		$ORG_TYPE_CODE = trim($data_dpis3[OT_CODE]); 		
		$DEPARTMENT_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$AP_NAME_POS = trim($data_dpis3[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$PV_NAME_POS = trim($data_dpis3[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE_POS' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$CT_NAME_POS = trim($data_dpis3[CT_NAME]);

		// ===== สังกัด =====
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_TYPE_CODE' ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_TYPE_NAME = trim($data_dpis3[OT_NAME]);	

		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_1 = trim($data_dpis3[ORG_NAME]);

		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_2 = trim($data_dpis3[ORG_NAME]);	
                
                 /*Release 5.1.0.5 BEgin เพิ่มเติม*/
                $ORG_ID_3 = trim($data[ORG_ID_3]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_3 = trim($data_dpis3[ORG_NAME]);
                
                $ORG_ID_4 = trim($data[ORG_ID_4]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_4 = trim($data_dpis3[ORG_NAME]);
                
                
                $ORG_ID_5 = trim($data[ORG_ID_5]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$ORG_NAME_5 = trim($data_dpis3[ORG_NAME]);
                /*Release 5.1.0.5 End*/
                
                
                
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$DEPARTMENT_NAME = trim($data_dpis3[ORG_NAME]);
		$MINISTRY_ID = $data_dpis3[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis3->send_cmd($cmd);
		$data_dpis3 = $db_dpis3->get_array();
		$MINISTRY_NAME = trim($data_dpis3[ORG_NAME]);
	}	// end if ลูกจ้างชั่วคราว

//<script>
//	$PER_SALARY = $POS_SALARY;	 /**/
//	$PER_MGTSALARY = $PER_MGTSALARY;
//	$PL_NAME = $PL_NAME;
//	$PM_NAME = $PM_NAME;	
//	$CL_NAME = $CL_NAME;
//	$PT_NAME = $PT_NAME;
//	$SG_NAME = $SG_NAME;
//	$SKILL_NAME = $SKILL_NAME;	
//	$MINISTRY_NAME = $MINISTRY_NAME;
//	$DEPARTMENT_NAME = $DEPARTMENT_NAME;
//	$ORG_NAME = $ORG_NAME;
//	$ORG_NAME_1 = $ORG_NAME_1;
//	$ORG_NAME_2 = $ORG_NAME_2;
//	$AP_NAME_POS = $AP_NAME_POS;
//	$PV_NAME_POS = $PV_NAME_POS;
//	$CT_NAME_POS = $CT_NAME_POS;
//	$ORG_TYPE_NAME = $ORG_TYPE_NAME;
// <            script>
?>
