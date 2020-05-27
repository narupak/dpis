<?
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POEM_ID, a.LEVEL_NO, CMD_SALARY, 
							CMD_SPSALARY, MOV_CODE, PN_CODE_ASSIGN, 
							CMD_NOTE1, PN_CODE, b.LEVEL_NAME 
					from		PER_COMDTL a, PER_LEVEL b 
					where	COM_ID=$COM_ID and a.LEVEL_NO = b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
//echo "$cmd<br>";		
//$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POEM_ID = (trim($data[POEM_ID]))? trim($data[POEM_ID]) : "NULL";
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NAME = trim($data[LEVEL_NAME]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]);
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_PN_CODE = trim($data[PN_CODE]);
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$LEVEL_NO = $PT_CODE = $POH_ASS_ORG = "";
			$PN_CODE = "NULL";
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ASS_ORG = $data2[ORG_NAME];	

			$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";
			$db_dpis2->send_cmd($cmd);		
			$data2 = $db_dpis2->get_array();
			$POH_POS_NO = trim($data2[POEM_NO]);
			$PN_CODE = "'".trim($data2[PN_CODE])."'";
			
			$ORG_ID_3 = trim($data2[ORG_ID]);	
			$ORG_ID_4 = trim($data2[ORG_ID_1]);
			$ORG_ID_5 = trim($data2[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				
			 			
			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = (trim($data2[CT_CODE]))? "'".trim($data2[CT_CODE])."'" : "NULL";
			$PV_CODE = (trim($data2[PV_CODE]))? "'".trim($data2[PV_CODE])."'" : "NULL";
			$AP_CODE = (trim($data2[AP_CODE]))? "'".trim($data2[AP_CODE])."'" : "NULL";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}
			
			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  POEM_ID=$tmp_POEM_ID, LEVEL_NO='$tmp_LEVEL_NO', MOV_CODE='$tmp_MOV_CODE', 
								PER_STATUS=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

			// update status of PER_POS_EMP 
			$cmd = " update PER_POS_EMP set PN_CODE='$tmp_PN_CODE_ASSIGN', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'								
						  where POEM_ID=$tmp_POEM_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();				
			//echo "<br>";
			
			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();				
			//echo "<br>";
			
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 			 
			$cmd = " insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
							POH_DOCDATE, POH_ENDDATE, 
						   	POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, PT_CODE, 
							CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							POH_UNDER_ORG1, POH_UNDER_ORG2, POH_ASS_ORG, 
						   	POH_SALARY, POH_SALARY_POS, POH_REMARK, 
							POH_ORG1, POH_ORG2, POH_ORG3, UPDATE_USER, UPDATE_DATE)
						  	values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '', 
						   	'$POH_POS_NO', NULL, '$tmp_LEVEL_NO', NULL, '$tmp_PN_CODE_ASSIGN', NULL, NULL, 
							$CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, 
							'$ORG_NAME_4', '$ORG_NAME_5', '$POH_ASS_ORG', 
						   	$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE1', 
							'$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			//echo "<br>============================<br>";

		}	// end while 		

		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=18190 where PN_CODE in 
		('1101', '1102', '1105', '1108', '1109', '1112', '1116', '1119', '1203', '1301', '1304', '1404', '1406', '1407', '1410', '1411', '1412', 
		 '1501', '1502', '1506', '1508', '2105', '2205', '2318', '2322', '2401', '2409', '2413', '2414', '2415', '2416', '2418', '2419', '2426', 
		 '2427', '2429','2510', '2512', '2516', '2518', '2601', '2608', '2702', '2806', '2904', '2908', '2912', '2915', '2916', '3103', '3106', 
		 '3304', '3313', '3401', '3403', '3407', '3411', '3607', '3608', '3705', '3712', '3718', '3720', '3722', '3902', '3904', '3906', '3911', '3912') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=15260 where PN_CODE in 
		('1103', '1111', '1113', '1114', '1115', '1201', '1202', '1204', '1205', '1206', '1207', '1303', '1402', '1408', '1503', '1510', '2503', '3905') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=22220 where PN_CODE in 
		('1104', '1110', '1117', '1118', '1401', '1405', '1409', '1507', '1511', '2403', '2422', '2423', '2509', '2604', '2701', '2704', '2705', 
		 '2708', '2709', '2805', '2902', '2907', '2909', '2911', '3316', '3324', '3326', '3335', '3406', '3412', '3413', '3706', '3714', '3717', 
		 '3724', '3801', '3909', '3910') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7100, POEM_MAX_SALARY=18190 where PN_CODE in ('1106','1107') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5840, POEM_MAX_SALARY=22220 where PN_CODE in 
		('1302', '1305', '1117', '1118', '1403', '1413', '1504', '1505', '1509', '2109', '2110', '2114'. '2201', '2203', '2207', '2301', '2302', 
		 '2303', '2305', '2306', '2307', '2310', '2311', '2312', '2313', '2314', '2316', '2317', '2321', '2323', '2326', '2327', '2402', '2408', 
		 '2410', '2411', '2412', '2417', '2420', '2421', '2424', '2425', '2428', '2430', '2431', '2506', '2508', '2513', '2515', '2517', '2519',
		 '2602', '2703', '2706', '2801', '2903', '2906', '2914', '3107', '3202', '3203', '3205', '3302', '3309', '3310', '3311', '3317', '3319', 
		 '3327', '3402', '3415', '3501', '3502', '3505', '3602', '3603', '3604', '3605', '3606', '3611', '3613', '3704', '3707', '3708', '3719', 
		 '3723', '3725', '3726', '3727', '3803', '3804', '3805', '3815', '3816', '3901', '3903', '3907') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5840, POEM_MAX_SALARY=33540 where PN_CODE in 
		('2101', '2102', '2106', '2108', '2206', '2905') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7940, POEM_MAX_SALARY=31420 where PN_CODE in 
		('2103','2104', '2208', '2315', '2320', '2432','2522', '2910', '3409', '3504', '3614', '3806', '3807', '3809', '3810', '3812', '3814') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5840, POEM_MAX_SALARY=29320 where PN_CODE in 
		('2107', '2112', '2202', '2209', '2210', '2309', '2324', '2325', '2433', '2504', '2507', '2523', '2524', '2802', '2804', '2913', '3102', 
		 '3104', '3204', '3320', '3404', '3612', '3701', '3713', '3715', '3721') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=29320 where PN_CODE in 
		('2111', '2113', '2404', '2707', '3307', '3308', '3328', '3329', '3330', '3332', '3333', '3408', '3410', '3609', '3703', '3711') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5840, POEM_MAX_SALARY=31420 where PN_CODE in 
		('2204','2405', '2406', '2407', '2511', '2605', '2607', '3101', '3201', '3206', '3321', '3323', '3325', '3331') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7100, POEM_MAX_SALARY=29320 where PN_CODE in 
		('2304','2501','2521', '2807', '2808', '2901', '3105', '3208', '3405', '3414', '3503', '3506', '3601', '3702', '3709', '3710', '3728', '3811', '3813', '3908') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7100, POEM_MAX_SALARY=31420 where PN_CODE in ('2308', '2514', '2803', '3207', '3716', '3802', '3808') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=6470, POEM_MAX_SALARY=29320 where PN_CODE in ('2319') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=10190, POEM_MAX_SALARY=31420 where PN_CODE in ('2434') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=36020 where PN_CODE in ('2502') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5840, POEM_MAX_SALARY=33540 where PN_CODE in ('2505', '3301', '3303', '3314', '3315', '3318') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7100, POEM_MAX_SALARY=25320 where PN_CODE in ('2520') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=5080, POEM_MAX_SALARY=31420 where PN_CODE in ('2603', '3305', '3306', '3322', '3610') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=7940, POEM_MAX_SALARY=29320 where PN_CODE in ('2606', '3312') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=11350, POEM_MAX_SALARY=36020 where PN_CODE in ('4101') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=9700, POEM_MAX_SALARY=31420 where PN_CODE in ('4201', '4301', '4304') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=13960, POEM_MAX_SALARY=36020 where PN_CODE in ('4302') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=19420, POEM_MAX_SALARY=55800 where PN_CODE in ('4303', '4306') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=13960, POEM_MAX_SALARY=60060 where PN_CODE in ('4305') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=26470, POEM_MAX_SALARY=64340 where PN_CODE in ('4307') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=28660, POEM_MAX_SALARY=64340 where PN_CODE in ('4308') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_EMP set POEM_MIN_SALARY=9700, POEM_MAX_SALARY=33540 where PN_CODE in ('3334') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		$cmd = " update PER_POS_GROUP set PG_ACTIVE=0 where PG_CODE in ('10','20','30','41','42','43','44','50') ";
		$db_dpis1->send_cmd($cmd);	
		//$db_dpis1->show_error();				
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งจัดระบบตำแหน่งลูกจ้างประจำ [$COM_ID : $PER_ID : $POH_ID]");
?>