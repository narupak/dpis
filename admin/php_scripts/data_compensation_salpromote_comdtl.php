<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis5 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        ini_set("max_execution_time", 0);
        $debug=0;/*0=close,1=open*/
        if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
        
        
	if (!$ORG_ID_DTL && $_POST[ORG_ID_DTL]) 	$ORG_ID_DTL=$_POST[ORG_ID_DTL];
	if (is_null($ORG_ID_DTL) || $ORG_ID_DTL=="NULL" || trim($ORG_ID_DTL)=="") $ORG_ID_DTL=0;
	if (is_null($ORG_ID) || $ORG_ID=="NULL"|| trim($ORG_ID)=="") $ORG_ID=0;
	if($ORG_ID_DTL){		
		$ORG_ID=$ORG_ID_DTL;		$search_org_id = $ORG_ID_DTL;
	}else{
		$search_org_id = $ORG_ID;
	}
	
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
//		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
//		$search_org_id = "0";
//		$search_org_name = "";
	}	

	if(!$current_page) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!$search_kf_year)	$search_kf_year = $KPI_BUDGET_YEAR;
	if(!trim($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$tmp_COM_DATE =  save_date($COM_DATE);
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 1;	
	$list_type = (trim($list_type))? $list_type : 4;	
	$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 6; 
	$COM_NOTE=trim($COM_NOTE);
	
	if (!$search_kf_cycle) $search_kf_cycle  = $KPI_CYCLE;
	if($search_kf_cycle == 1){
		$KF_START_DATE = ($search_kf_year-544) . "-10-01";
		$KF_END_DATE = ($search_kf_year-543) . "-03-31";
		$TMP_CMD_DATE = ($search_kf_year-543) . "-04-01";
	}elseif($search_kf_cycle == 2){
		$KF_START_DATE = ($search_kf_year-543) . "-04-01";
		$KF_END_DATE = ($search_kf_year-543) . "-09-30";
		$TMP_CMD_DATE = ($search_kf_year-543) . "-10-01";
	} // end if

	//$TMP_CMD_DATE =  save_date($TMP_CMD_DATE);
	$_select_level_no_str = implode("','",$_select_level_no);
	if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")	$position_join = "b.PAY_ID=c.POS_ID";
	else $position_join = "b.POS_ID=c.POS_ID";
	
	//== จาก form11 data_compensation_salpromote_comdtl.html
	if ($MINISTRY_ID) 		$search_ministry_id = $MINISTRY_ID;
	if ($MINISTRY_NAME) 	$search_ministry_name = $MINISTRY_NAME;
	if ($DEPARTMENT_ID) $search_department_id = $DEPARTMENT_ID;
	if ($DEPARTMENT_NAME) $search_department_name = $DEPARTMENT_NAME;
	
	if (!$search_department_id) $search_department_id = "NULL";

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;
	
		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE,
								COM_TYPE, COM_CONFIRM, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, COM_LEVEL_SALP, ORG_ID, COM_STATUS) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE,
						'$COM_TYPE', 0, $SESS_USERID, '$UPDATE_DATE', $search_department_id, $COM_LEVEL_SALP, $ORG_ID, '') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//echo "<br>$cmd<br>=======================<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		if ($RPT_N) {
			if ($COM_LEVEL_SALP == 1)					// 1=เงินเดือน
				$where_com_level_salp = " and b.PER_STATUS=1 and (CD_EXTRA_SALARY=0 or (CD_EXTRA_SALARY>0 and CD_SALARY>0)) and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินเพิ่มพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=ข้าราชการทั้งหมด
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
		} else {
			if ($COM_LEVEL_SALP == 1)					// 1=ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY <= '08' ";		
			elseif ($COM_LEVEL_SALP == 2)					// 2=ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY >= '09' ";
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินเพิ่มพิเศษ ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY <= '08' ";	
			elseif ($COM_LEVEL_SALP == 4)					// 4=เงินเพิ่มพิเศษ ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY >= '09' ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=ข้าราชการทั้งหมด
				$where_com_level_salp = "";		
			elseif ($COM_LEVEL_SALP == 7)					// 7=ปกติ
				$where_com_level_salp = " and b.PER_STATUS=1 ";
			elseif ($COM_LEVEL_SALP == 8)					// 8=เงินเพิ่มพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 ";
			elseif ($COM_LEVEL_SALP == 9)					// 9=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			elseif ($COM_LEVEL_SALP == 10)				// 10=ลูกจ้างประจำหรือพนักงานราชการทั้งหมด
				$where_com_level_salp = "";
		}		
		if($SELECTED_CP_ID){
			$cmd1 = " select  DISTINCT  a.CP_ID, a.PER_ID, b.PER_SALARY, a.AL_CODE, a.CD_SALARY, a.CD_PERCENT, a.CD_EXTRA_SALARY, a.CD_MIDPOINT, b.LEVEL_NO, 
											b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.PER_CARDNO, b.DEPARTMENT_ID, c.PL_CODE, c.PM_CODE, 
											d.OT_CODE, d.PV_CODE, d.CT_CODE 
							   from 	PER_COMPENSATION_TEST_DTL a, PER_PERSONAL b, PER_POSITION c , PER_ORG d, PER_COMPENSATION_TEST e
							   where 	a.CP_ID in ($SELECTED_CP_ID) and a.CP_ID=e.CP_ID and a.PER_ID=b.PER_ID and $position_join and e.ORG_ID = d.ORG_ID(+) $where_com_level_salp ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                        if($debug==1){echo __LINE__.'<pre>'.$cmd1.'<br>';}
			$count_temp = $db_dpis->send_cmd($cmd1);
	//			$db_dpis->show_error();
	//			echo "<br>$cmd1<br>";
		
		$cmd_seq = 0;
		  while ($data = $db_dpis->get_array()) {
			$TMP_OT_CODE = trim($data[OT_CODE]) ;
			$TMP_PV_CODE = trim($data[PV_CODE]) ;
			$TMP_CT_CODE = trim($data[CT_CODE]) ;
			$show_data = 0;
			if ($list_type==1) {
				if ($TMP_OT_CODE = "01") $show_data = 1;
			} elseif ($list_type==2) { 
				if ($TMP_PV_CODE = "$PV_CODE") $show_data = 1;		
			} elseif ($list_type==3) {
				if ($TMP_CT_CODE = "$CT_CODE") $show_data = 1;
			} else {
				 $show_data = 1;
			}
			if ($show_data==1) { 
				$cmd_seq++;
				$CP_ID = $data[CP_ID];		
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$TMP_AL_CODE = trim($data[AL_CODE]) ;
				$CMD_SALARY = $data[CD_SALARY] + $CMD_OLD_SALARY;
				$CMD_PERCENT = $data[CD_PERCENT] + 0;
				$CMD_SPSALARY = $data[CD_EXTRA_SALARY] + 0;
				if ($COM_LEVEL_SALP == 1) $CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = $data[CD_MIDPOINT] + 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;
				$CMD_PM_CODE = trim($data[PM_CODE]) ;
				if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") $POS_ID = trim($data[PAY_ID]); 
				else $POS_ID = trim($data[POS_ID]); 
				$PER_CARDNO = trim($data[PER_CARDNO]) ;
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];		

				$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

				$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
						 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2,LAYER_SALARY_TEMPUP
						 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();

                                /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                                // เดิม if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
				if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
					$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
					$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
					$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
					$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];

				} else {
					$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
					$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
					$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
					$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];

				}
                                $LAYER_SALARY_TEMPUP=$data2[LAYER_SALARY_TEMPUP];
                                if($LAYER_TYPE==2 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5")){
                                    $LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                                    $SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
                                    $SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
                                    $SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
                                }else{
                                    $LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
                                    $SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
                                    $SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
                                    $SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
                                }
                                
                                /*Test.... ตามมติ ครม. ใหม่*/
                                //if($LAYER_TYPE==2 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5")){
                                    //echo 'LAYER_TYPE:'.$LAYER_TYPE.', ประเภททำแหน่ง:'.$TMP_SALP_LEVEL.', เงินเดือนเดิม:'.$CMD_OLD_SALARY.'<br>';
                                    
                                //}else{
                                    $test_CMD_SPSALARY=$CMD_SPSALARY;
                                    /*if($CMD_SPSALARY){ //มีเงินพิเศษ
                                        //$test_SUM_SALARY=$CMD_SALARY+$CMD_SPSALARY+0;//เงินเดือน+เงืนที่เลือน+เงินพิเศษ
                                        $test_SUM_SALARY=$CMD_SALARY+0;//เงินเดือน+เงืนที่เลือน
                                    }else{ //ไม่มีเงินพิเศษ
                                        $test_SUM_SALARY=$CMD_SALARY; //เงินเดือน+เงืนที่เลือน
                                    }*/
                                    $test_SUM_SALARY=$CMD_SALARY; //เงินเดือน+เงืนที่เลือน

                                    $test_LAYER_SALARY_MAX=$LAYER_SALARY_MAX;
                                    //เชคว่าเกินช่วงไหม ถ้าเกินให้ ตั้งเพดานใหม่ ตาม ครม
                                    if($test_SUM_SALARY>$test_LAYER_SALARY_MAX){
                                        $test_LAYER_SALARY_MAX=$LAYER_SALARY_TEMPUP; //ขั้นตาม ครม.
                                    }
                                    
                                    
                                    
                                    if($SALARY_POINT_MID>$test_SUM_SALARY){
                                        $test_CMD_MIDPOINT = $SALARY_POINT_MID1; //บน
                                    }else{
                                        $test_CMD_MIDPOINT = $SALARY_POINT_MID2; //ล่าง
                                    }
                                    
                                    //ถ้าเงินเดือนสูงกว่า ขั้นใหม่ชั่วคราว
                                    //if($test_SUM_SALARY>$test_LAYER_SALARY_MAX){
                                        //echo '<font color=red>เงินเดือนสูงกว่าขั้นใหม่ชั่วคราว</font><br>';
                                    //    $test_SUM_SALARY=$test_LAYER_SALARY_MAX; //เงินเดือนกูจะได้เท่ากันขั้นสูงสุด
                                        //$test_CMD_SPSALARY =$test_SUM_SALARY-$test_LAYER_SALARY_MAX;
                                    //}
                                    //if($CMD_SPSALARY){ 
                                        $before_MOD_CMD_SALARY =$test_SUM_SALARY;
                                        $MOD_CMD_SALARY =  fmod($test_SUM_SALARY,10);
                                        if($MOD_CMD_SALARY!=0){
                                            $test_SUM_SALARY = ($test_SUM_SALARY-$MOD_CMD_SALARY)+10;
                                        }
                                        /*echo 'เงินเดือนเดิม='.$CMD_OLD_SALARY.' + <font color=red>เงินพิเศษ:'.$CMD_SPSALARY.'</font>  <br>';
                                        echo 'เงินที่เลื่อน='.$data[CD_SALARY].'<br>';
                                        echo '<b>เงินเดือนใหม่ก่อนปัด 10='.$before_MOD_CMD_SALARY.'</b><br>';
                                        echo '<b>เงินเดือนใหม่='.$test_SUM_SALARY.'</b><br>';
                                        if($test_CMD_SPSALARY){
                                            echo '<font color=red><b>เงินเดือนสูงกว่าขั้นสูง ได้ค่าเงินพิเศษ='.$test_CMD_SPSALARY.'</b></font><br>';
                                        }else{
                                            echo '<font color=red><b>ล้างค่าเงินพิเศษ='.$test_CMD_SPSALARY.'</b></font><br>';
                                        }
                                        
                                        echo '<font color=blue>**เงินเดือนขั้นสูงตามมติ ครม.='.$test_LAYER_SALARY_MAX.'</font><br>';
                                        echo '--------------<br><br>';*/
                                    //}
                                    
                                    //if ($COM_LEVEL_SALP == 3) $CMD_SALARY = $CMD_SALARY;
                                //} 
                                $CMD_SALARY=$test_SUM_SALARY;
                                $CMD_SPSALARY=$test_CMD_SPSALARY;
                                /*Test End................*/
                                
				if ($CMD_MIDPOINT==0) {
					if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
						$CMD_MIDPOINT = $SALARY_POINT_MID1;
					} else {
						$CMD_MIDPOINT = $SALARY_POINT_MID2;
					}
				}

				//if ($COM_LEVEL_SALP == 3) $CMD_SALARY = $LAYER_SALARY_MAX; /*เดิม*/


				$SALARY_REMARK1 = "";
				$cmd = " select SALARY_FLAG, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $TMP_PER_ID and KF_CYCLE = $search_kf_cycle and 
								KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$SALARY_FLAG = trim($data1[SALARY_FLAG]);
				$SALARY_REMARK1 = trim($data1[SALARY_REMARK1]);

                                
				//เดิม
                                /*$cmd = " select a.AM_CODE, AL_NAME, AM_NAME, AM_SHOW,a.AL_YEAR,a.AL_CYCLE  from PER_ASSESS_LEVEL a, PER_ASSESS_MAIN b 
					 where a.AL_YEAR = b.AM_YEAR and a.AL_CYCLE = b.AM_CYCLE and a.AM_CODE = b.AM_CODE and AL_CODE='$TMP_AL_CODE'  ";*/
                                
                                //ปรับใหม่ 
                                $cmd = " SELECT a.AM_CODE, AL_NAME, AM_NAME, AM_SHOW,a.AL_YEAR,a.AL_CYCLE  
                                         FROM PER_ASSESS_LEVEL a, PER_ASSESS_MAIN b 
					 WHERE a.AL_YEAR = b.AM_YEAR 
                                            AND a.AL_CYCLE = b.AM_CYCLE 
                                            AND a.AM_CODE = b.AM_CODE 
                                            AND AL_CODE='$TMP_AL_CODE' 
                                            AND a.AL_CYCLE = '$search_kf_cycle'  
                                            AND a.AL_YEAR = '$search_kf_year' ";
                                
				
                                
                               //echo $cmd."<br><br>";
                                $db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$AM_CODE = trim($data2[AM_CODE]);
				$AL_NAME = trim($data2[AL_NAME]);
				$AM_NAME = trim($data2[AM_NAME]);
				$AL_YEAR = trim($data2[AL_YEAR]);
				$AL_CYCLE = trim($data2[AL_CYCLE]);
				$AM_SHOW = $data2[AM_SHOW] + 0;
                                
                                
//echo " $cmd [ $AM_SHOW ] => $TMP_PER_ID :: $TMP_AL_CODE ( $AM_NAME  )  !! $AL_YEAR //$AL_CYCLE <br>";				

				if ($BKK_FLAG==1) {
					$MOV_CODE = "044";
					if ($AM_CODE == 2 || $AM_NAME == "พอใช้") 		$MOV_CODE = "81";
					elseif ($AM_CODE == 3 || $AM_NAME == "ดี")		$MOV_CODE = "82";
					elseif ($AM_CODE == 4 || $AM_NAME == "ดีมาก") 	$MOV_CODE = "83";
					elseif ($AM_CODE == 5 || $AM_NAME == "ดีเด่น") 		$MOV_CODE = "84";

					if ($CMD_SPSALARY  > 0)		$MOV_CODE = "85";
//					if ($SALARY_FLAG=="N") 	$MOV_CODE = "85";
				} else {
					$MOV_CODE = "21305";
					if ($AM_CODE == 2 || $AM_NAME == "พอใช้") 		$MOV_CODE = "21315";
					elseif ($AM_CODE == 3 || $AM_NAME == "ดี")		$MOV_CODE = "21325";
					elseif ($AM_CODE == 4 || $AM_NAME == "ดีมาก") 	$MOV_CODE = "21335";
					elseif ($AM_CODE == 5 || $AM_NAME == "ดีเด่น") 		$MOV_CODE = "21345";

					if ($CMD_SPSALARY  >0) $MOV_CODE = "21415";
					if ($SALARY_FLAG=="N") 	$MOV_CODE = "21375";
				}
				
				$cmd = " select CP_CYCLE, CP_END_DATE from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CP_CYCLE = $data2[CP_CYCLE];
				$CP_END_DATE = trim($data2[CP_END_DATE]);

				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				if ($SALARY_FLAG=="N") $CMD_NOTE2 = $SALARY_REMARK1;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = ($POS_ID)? $POS_ID : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";

                                //เพิ่ม ใหม่ เคส http://dpis.ocsc.go.th/Service/node/2591 กรณีมีการติ๊กโครงสร้างตามมอบหมาย โปรแกรม หาไอดีหน่วยงานผิดที่ แก้ไขโดยให้หยิบ มอบหมายที่ตัวคน
                                $cmd_p = " select ORG_ID , ORG_ID_1,ORG_ID_2,ORG_ID_3,ORG_ID_4,ORG_ID_5 from PER_PERSONAL  where POS_ID=$POS_ID ";
                                $db_dpis5->send_cmd($cmd_p);
				$data_p = $db_dpis5->get_array();
                                
				$cmd = " select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
                //echo "<pre>".$cmd;
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POS_NO]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
		
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$CMD_POSITION = $PL_NAME;
				if ($PM_NAME)
					$CMD_POSITION .= "\|$PM_NAME";
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

                                
                                //เพิ่ม ใหม่ เคส http://dpis.ocsc.go.th/Service/node/2591 กรณีมีการติ๊กโครงสร้างตามมอบหมาย โปรแกรม หาไอดีหน่วยงานผิดที่  แก้ไขโดยให้หยิบ มอบหมายที่ตัวคน
				if($select_org_structure==1){
                                    $ORG_ID_1 = (trim($data_p[ORG_ID]))? trim($data_p[ORG_ID]) : 0;
                                    $ORG_ID_2 = (trim($data_p[ORG_ID_1]))? trim($data_p[ORG_ID_1]) : 0;
                                    $ORG_ID_3 = (trim($data_p[ORG_ID_2]))? trim($data_p[ORG_ID_2]) : 0;
                                    $ORG_ID_4 = (trim($data_p[ORG_ID_3]))? trim($data_p[ORG_ID_3]) : 0;
                                    $ORG_ID_5 = (trim($data_p[ORG_ID_4]))? trim($data_p[ORG_ID_4]) : 0;
                                    $ORG_ID_6 = (trim($data_p[ORG_ID_5]))? trim($data_p[ORG_ID_5]) : 0;
                                }else{
                                    $ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
                                    $ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
                                    $ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
                                    $ORG_ID_4 = (trim($data2[ORG_ID_3]))? trim($data2[ORG_ID_3]) : 0;
                                    $ORG_ID_5 = (trim($data2[ORG_ID_4]))? trim($data2[ORG_ID_4]) : 0;
                                    $ORG_ID_6 = (trim($data2[ORG_ID_5]))? trim($data2[ORG_ID_5]) : 0;
                                }
                                
				
                                
                                
				$CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = $CMD_ORG6 = $CMD_ORG7 = $CMD_ORG8 = "";
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($TMP_DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $ORG_ID_6) ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$temp_id = trim($data2[ORG_ID]);
					$CMD_ORG2 = ($temp_id == $TMP_DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
					$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
					$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
					$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					$CMD_ORG6 = ($temp_id == $ORG_ID_4)?  trim($data2[ORG_NAME]) : $CMD_ORG6;
					$CMD_ORG7 = ($temp_id == $ORG_ID_5)?  trim($data2[ORG_NAME]) : $CMD_ORG7;
					$CMD_ORG8 = ($temp_id == $ORG_ID_6)?  trim($data2[ORG_NAME]) : $CMD_ORG8;						
				}
					
				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_CODE = trim($data2[OT_CODE]);
		
				if ($OT_CODE == "03") 
					if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$search_department_name);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
                                if($debug==1){
                                    echo __LINE__.$TMP_PER_ID.'=>search_org_id='.$search_org_id.'<br>';
                                    echo __LINE__.$TMP_PER_ID.'=>ORG_ID_1='.$ORG_ID_1.'<br>';
                                    }
                                
				//if (!$search_org_id || $search_org_id == "NULL" || $search_org_id == $ORG_ID_1) { /*เดิม*/
                                
					if (($COM_LEVEL_SALP==1 && $CMD_SALARY > $CMD_OLD_SALARY) || ($COM_LEVEL_SALP==3 && $CMD_SPSALARY > 0) ||
						$COM_LEVEL_SALP==6) {                                           
                                            
						$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
                                                                CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
                                                                CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
                                                                PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, UPDATE_USER, UPDATE_DATE, 
                                                                LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, 
                                                                PL_CODE, PM_CODE, CMD_POS_NO_NAME, CMD_POS_NO, PER_CARDNO)
                                                        values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$TMP_CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
                                                                '$search_ministry_name', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6','$CMD_ORG7', '$CMD_ORG8', 
                                                                $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, $PL_CODE_ASSIGN, 
                                                                $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, $SESS_USERID, '$UPDATE_DATE', 
                                                                $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,'$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', $cmd_seq, 
                                                                '$CMD_PL_CODE', '$CMD_PM_CODE', '$CMD_POS_NO_NAME', '$CMD_POS_NO', '$PER_CARDNO') "; 
                                               
                                                //if($CMD_SPSALARY){ 
                                                //    echo '<pre>'.$cmd.'<br>++++++++++++++++++++++++++++++++<br>';
                                                //}
//echo '<pre>'.$cmd.'<br>++++++++++++++++++++++++++++++++<br>';
							/***$cmd = " insert into PER_COMDTL
												(	COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
													CMD_LEVEL, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
													POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
													PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
													CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
													CMD_PERCENT, UPDATE_USER, UPDATE_DATE,
													PL_NAME_WORK, ORG_NAME_WORK,CMD_LEVEL_POS, CMD_SEQ_NO, CMD_MIDPOINT,CMD_POS_NO_NAME, CMD_POS_NO)
											 values
												(	$COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$TMP_CMD_DATE', '$CMD_POSITION', 
													$CMD_LEVEL, '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
													$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
													$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
													'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
													$CMD_PERCENT, $SESS_USERID, '$UPDATE_DATE',
													'$PL_NAME_WORK', '$ORG_NAME_WORK','$CMD_LEVEL_POS', $cmd_seq, $CMD_MIDPOINT, '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";		***/
						$db_dpis1->send_cmd($cmd);				
                                                if($debug==1){echo __LINE__.'<pre>'.$cmd.'<br>';}
						//$db_dpis1->show_error();
						//echo "<br>$cmd<br>======<br>";
					//}//if (!$search_org_id || $search_org_id == "NULL" || $search_org_id == $ORG_ID_1) { /*เดิม*/
				}
			}
		  }	// end while
		}
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, COM_LEVEL_SALP, ORG_ID) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, $search_department_id, $SESS_USERID, '$UPDATE_DATE', $COM_LEVEL_SALP, $search_org_id) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<br>$cmd<br>=======================<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [$search_department_id : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		if ($RPT_N) {
			if ($search_org_id && $search_org_id != "NULL") $where .= " and c.ORG_ID = $search_org_id ";		 
			if ($list_type==5) $where .= " and f.ORG_ID = $search_org_id ";
			if ($list_type==6) $where .= " and f.ORG_ID != $search_org_id ";
		}		
		if ($RPT_N) {
			if ($COM_LEVEL_SALP == 1)					// 1=เงินเดือน
				$where_com_level_salp = " and b.PER_STATUS=1 and (CD_EXTRA_SALARY=0 or (CD_EXTRA_SALARY>0 and CD_SALARY>0)) and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินตอบแทนพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 and b.LEVEL_NO in ('".$_select_level_no_str."') ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=เงินเดือนและเงินตอบแทนพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO in ('".$_select_level_no_str."') ";		
		} else {
			if ($COM_LEVEL_SALP == 1)					// 1=ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY <= '08' ";		
			elseif ($COM_LEVEL_SALP == 2)					// 2=ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and b.LEVEL_NO_SALARY >= '09' ";
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินเพิ่มพิเศษ ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY <= '08' ";	
			elseif ($COM_LEVEL_SALP == 4)					// 4=เงินเพิ่มพิเศษ ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 and b.LEVEL_NO_SALARY >= '09' ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=ข้าราชการทั้งหมด
				$where_com_level_salp = "";		
			elseif ($COM_LEVEL_SALP == 7)					// 7=ปกติ
				$where_com_level_salp = " and b.PER_STATUS=1 ";
			elseif ($COM_LEVEL_SALP == 8)					// 8=เงินเพิ่มพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and CD_EXTRA_SALARY>0 ";
			elseif ($COM_LEVEL_SALP == 9)					// 9=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			elseif ($COM_LEVEL_SALP == 10)				// 10=ลูกจ้างประจำหรือพนักงานราชการทั้งหมด
				$where_com_level_salp = "";
		}		
		if ($list_type==5 || $list_type==6) 
			$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.PER_CARDNO, b.DEPARTMENT_ID, 
												c.PL_CODE, c.PM_CODE, d.OT_CODE, d.PV_CODE, d.CT_CODE
							   from 	PER_PERSONAL b, PER_POSITION c , PER_ORG d, PER_POSITION e, PER_ORG f
							   where 	b.PAY_ID=c.POS_ID and c.ORG_ID = d.ORG_ID and b.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID $where_com_level_salp $where ";
		else 
			$cmd1 = " select	b.PER_ID, b.PER_SALARY, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.PAY_ID, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.PER_CARDNO, b.DEPARTMENT_ID, 
												c.PL_CODE, c.PM_CODE
							   from 	PER_PERSONAL b, PER_POSITION c , PER_ORG d
							   where 	$position_join and c.ORG_ID = d.ORG_ID $where_com_level_salp $where ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);					   
		$count_temp = $db_dpis->send_cmd($cmd1);
		//$db_dpis->show_error();
		//echo "<br>$cmd1<br>";
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$TMP_OT_CODE = trim($data[OT_CODE]) ;
			$TMP_PV_CODE = trim($data[PV_CODE]) ;
			$TMP_CT_CODE = trim($data[CT_CODE]) ;
			$show_data = 0;
			if ($list_type==1) {
				if ($TMP_OT_CODE = "01") $show_data = 1;
			} elseif ($list_type==2) { 
				if ($TMP_PV_CODE = "$PV_CODE") $show_data = 1;		
			} elseif ($list_type==3) {
				if ($TMP_CT_CODE = "$CT_CODE") $show_data = 1;
			} else {
				 $show_data = 1;
			}
			if ($show_data==1) { 
				$cmd_seq++;
				$TMP_PER_ID = $data[PER_ID];		
				$CMD_OLD_SALARY = $data[PER_SALARY] + 0;
				$CMD_SALARY = 0;
				$CMD_PERCENT = 0;
				$CMD_SPSALARY = 0;
				$TMP_SALP_LEVEL = trim($data[LEVEL_NO]) ;
				$CMD_MIDPOINT = 0;
				$CMD_PL_CODE = trim($data[PL_CODE]) ;
				$CMD_PM_CODE = trim($data[PM_CODE]) ;
				if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") $POS_ID = trim($data[PAY_ID]); 
				else $POS_ID = trim($data[POS_ID]); 
				$PER_CARDNO = trim($data[PER_CARDNO]) ;
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];		

				if ($CMD_MIDPOINT==0) {
					$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

					$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
						 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
						 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
                                        
                                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                                        // เดิม if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
					if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
						$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
						$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
						$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
						$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
					} else {
						$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
						$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
						$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
						$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
					}
	
					if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
						$CMD_MIDPOINT = $SALARY_POINT_MID1;
					} else {
						$CMD_MIDPOINT = $SALARY_POINT_MID2;
					}
				}

				if(!$AM_SHOW)	$AM_SHOW = 0;
				$MOV_CODE = "21345";
				$CMD_NOTE1 = $AL_NAME;
				$CMD_NOTE2 = $AM_NAME;
				
				$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
				$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
				$POS_ID = ($POS_ID)? $POS_ID : "NULL";
				$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
				$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
				$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";
				
                                //เพิ่ม ใหม่ เคส http://dpis.ocsc.go.th/Service/node/2591 กรณีมีการติ๊กโครงสร้างตามมอบหมาย โปรแกรม หาไอดีหน่วยงานผิดที่  แก้ไขโดยให้หยิบ มอบหมายที่ตัวคน
                                $cmd_p = " select ORG_ID , ORG_ID_1,ORG_ID_2,ORG_ID_3,ORG_ID_4,ORG_ID_5 from PER_PERSONAL  where POS_ID=$POS_ID ";
                                $db_dpis5->send_cmd($cmd_p);
				$data_p = $db_dpis5->get_array();
                                
				$cmd = " select POS_NO_NAME, POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO 
								from PER_POSITION a, PER_LINE b 
								where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$CMD_LEVEL_POS = trim($data2[LEVEL_NO]);
				$CMD_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$CMD_POS_NO = trim($data2[POS_NO]);

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
	
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$CMD_POSITION = $PL_NAME;
				if ($PM_NAME)
					$CMD_POSITION .= "\|$PM_NAME";
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

                                //เพิ่ม ใหม่ เคส http://dpis.ocsc.go.th/Service/node/2591 กรณีมีการติ๊กโครงสร้างตามมอบหมาย โปรแกรม หาไอดีหน่วยงานผิดที่  แก้ไขโดยให้หยิบ มอบหมายที่ตัวคน
				if($select_org_structure==1){
                                    $ORG_ID_1 = (trim($data_p[ORG_ID]))? trim($data_p[ORG_ID]) : 0;
                                    $ORG_ID_2 = (trim($data_p[ORG_ID_1]))? trim($data_p[ORG_ID_1]) : 0;
                                    $ORG_ID_3 = (trim($data_p[ORG_ID_2]))? trim($data_p[ORG_ID_2]) : 0;
                                    $ORG_ID_4 = (trim($data_p[ORG_ID_3]))? trim($data_p[ORG_ID_3]) : 0;
                                    $ORG_ID_5 = (trim($data_p[ORG_ID_4]))? trim($data_p[ORG_ID_4]) : 0;
                                    $ORG_ID_6 = (trim($data_p[ORG_ID_5]))? trim($data_p[ORG_ID_5]) : 0;
                                }else{
                                    $ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
                                    $ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
                                    $ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
                                    $ORG_ID_4 = (trim($data2[ORG_ID_3]))? trim($data2[ORG_ID_3]) : 0;
                                    $ORG_ID_5 = (trim($data2[ORG_ID_4]))? trim($data2[ORG_ID_4]) : 0;
                                    $ORG_ID_6 = (trim($data2[ORG_ID_5]))? trim($data2[ORG_ID_5]) : 0;
                                }
                                    
                                
                                
				$CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = $CMD_ORG6 = $CMD_ORG7 = $CMD_ORG8 = "";
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($TMP_DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $ORG_ID_6) ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$temp_id = trim($data2[ORG_ID]);
					$CMD_ORG2 = ($temp_id == $TMP_DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
					$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
					$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
					$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
					$CMD_ORG6 = ($temp_id == $ORG_ID_4)?  trim($data2[ORG_NAME]) : $CMD_ORG6;
					$CMD_ORG7 = ($temp_id == $ORG_ID_5)?  trim($data2[ORG_NAME]) : $CMD_ORG7;
					$CMD_ORG8 = ($temp_id == $ORG_ID_6)?  trim($data2[ORG_NAME]) : $CMD_ORG8;						
				}
					
				$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$OT_CODE = trim($data2[OT_CODE]);
		
				if ($OT_CODE == "03") 
					if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="กรมการปกครอง") 
						$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else 
						$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$search_department_name);
				else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);
			 
				$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
								CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
								CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
								UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, CMD_PERCENT, AM_SHOW, CMD_MIDPOINT,
								PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_SEQ_NO, PL_CODE, PM_CODE, CMD_POS_NO_NAME, 
								CMD_POS_NO, PER_CARDNO)
								values	($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$TMP_CMD_DATE', '$CMD_POSITION', $CMD_LEVEL, 
								'$search_ministry_name', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', '$CMD_ORG6', 
								'$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 
								$CMD_SPSALARY, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', 
								'$MOV_CODE', 0, $SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, $CMD_PERCENT, $AM_SHOW, $CMD_MIDPOINT,
								'$PL_NAME_WORK', '$ORG_NAME_WORK', '$CMD_LEVEL_POS', $cmd_seq, '$CMD_PL_CODE', '$CMD_PM_CODE', 
								'$CMD_POS_NO_NAME', '$CMD_POS_NO', '$PER_CARDNO') ";
				$db_dpis1->send_cmd($cmd);				
//$db_dpis1->show_error();
//echo "<br>$cmd<br>=======================<br>";     
			}
		}	// end while 
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD_ALL" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
//echo "search_kf_year :".$search_kf_year;
            if(empty($search_kf_year)){$search_kf_year ="NULL";}else{$search_kf_year=$search_kf_year-543;} //pitak
		$cmd = " UPDATE PER_COMMAND SET
                            COM_NO='$COM_NO', 
                            COM_NAME='$COM_NAME', 
                            COM_DATE='$tmp_COM_DATE', 
                            COM_NOTE='$COM_NOTE',
                            COM_PER_TYPE=$COM_PER_TYPE, 
                            COM_TYPE='$COM_TYPE',
                            UPDATE_USER=$SESS_USERID, 
                            UPDATE_DATE='$UPDATE_DATE' ,
                            COM_YEAR = $search_kf_year 
                            where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_OLD_SALARY, 
							CMD_SALARY, CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, LEVEL_NO_SALARY, CMD_PERCENT, 
							CMD_MIDPOINT, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ_NO, CMD_SEQ 
							from		PER_COMDTL 
							where	COM_ID=$COM_ID";
               if($debug==1){echo '<pre>'.$cmd.'<br>';} 
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID] + 0;
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = $data[POS_ID] + 0;
			$tmp_POEM_ID = $data[POEM_ID] + 0;
			$tmp_POEMS_ID = $data[POEMS_ID] + 0;
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);
			$tmp_CMD_OLD_SALARY = $data[CMD_OLD_SALARY] + 0;
			$tmp_CMD_SALARY = $data[CMD_SALARY] + 0;
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY] + 0;
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_CMD_PERCENT = $data[CMD_PERCENT] + 0;
			$tmp_CMD_DIFF = $tmp_CMD_SALARY - $tmp_CMD_OLD_SALARY;
			$tmp_CMD_MIDPOINT = $data[CMD_MIDPOINT] + 0;
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$tmp_CMD_SEQ_NO = $data[CMD_SEQ_NO] + 0;
			$tmp_CMD_SEQ = $data[CMD_SEQ] + 0;
			if ($tmp_CMD_SEQ_NO==0) $tmp_CMD_SEQ_NO = $tmp_CMD_SEQ;
                        
                        //echo $tmp_CMD_SALARY.'<<<< <br>';
                        
                        
						
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			$cmd = " select POS_NO,PL_CODE,LEVEL_NO from PER_POSITION where POS_ID=$tmp_POS_ID  ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$SAH_POS_NO = trim($data1[POS_NO]);
                        $CMD_PL_CODE = trim($data1[PL_CODE]);
                        $TMP_SALP_LEVEL = trim($data1[LEVEL_NO]);
			$SAH_PAY_NO = $SAH_POS_NO;

                        
                        /*-----------------------------------------------------------*/
                        $comment1='';
                        $comment2='';
                        if($chk_sum_sp && $TMP_SALP_LEVEL !="M2"){ //นำเงินตอบแทนพิเศษรวมเป็นเงินเดือน     
                            if ($tmp_CMD_SPSALARY  > 0){
                                $tmp_MOV_CODE="21520";/*ปรับเงินเดือนตามกฏหมาย*/
                                $comment1=' เงินตอบแทนพิเศษ '.$tmp_CMD_SPSALARY;
                                $comment2=' [ระบุผลการเลื่อนเงินเดือน] และให้ได้รับเงินเดือนตามมติ ครม. วันที่ 18 ต.ค. 59';
                            }
                            
                            $cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $LAYER_TYPE = $data2[LAYER_TYPE] + 0;

                            $cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
                                             LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2,LAYER_SALARY_TEMPUP
                                             from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
                            $db_dpis2->send_cmd($cmd);
                            if($debug==1){echo'XXX :<pre>'.$cmd.'<br>';} 
                            $data2 = $db_dpis2->get_array();

                            /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                            // เดิม if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
                            if ($LAYER_TYPE==1 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
                                    $LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
                                    $SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
                                    $SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
                                    $SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];

                            } else {
                                    $LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                                    $SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
                                    $SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
                                    $SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];

                            }
                            $LAYER_SALARY_TEMPUP=$data2[LAYER_SALARY_TEMPUP];
                            if($LAYER_TYPE==2 && ($TMP_SALP_LEVEL == "O3" || $TMP_SALP_LEVEL == "K5")){
                                $LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                                $SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
                                $SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
                                $SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
                            }else{
                                $LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
                                $SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
                                $SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
                                $SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
                            }
                            $test_CMD_SPSALARY=$tmp_CMD_SPSALARY;
                            $test_LAYER_SALARY_MAX=$LAYER_SALARY_MAX;
                            //ถ้าเงินเดือนสูงกว่า ขั้นใหม่ชั่วคราว
                            //echo $LAYER_SALARY_TEMPUP.'<<<< <br>';
                            if($tmp_CMD_SALARY>$LAYER_SALARY_TEMPUP){
                                //echo '<font color=red>เงินเดือนสูงกว่าขั้นใหม่ชั่วคราว</font><br>';
                                $tmp_CMD_SALARY=$LAYER_SALARY_TEMPUP; //เงินเดือนกูจะได้เท่ากันขั้นสูงสุด
                                $tmp_CMD_SPSALARY =$tmp_CMD_SALARY-$LAYER_SALARY_TEMPUP;
                            }else{
                                $tmp_CMD_SALARY=$tmp_CMD_SALARY+$test_CMD_SPSALARY;
                                $tmp_CMD_SPSALARY=0;
                            }
                            $before_MOD_CMD_SALARY =$tmp_CMD_SALARY;
                            $MOD_CMD_SALARY =  fmod($tmp_CMD_SALARY,10);
                            if($MOD_CMD_SALARY!=0){
                                $tmp_CMD_SALARY = ($tmp_CMD_SALARY-$MOD_CMD_SALARY)+10;
                            }
                            //echo $tmp_CMD_SALARY.'<<<< <br>';
                        }else{
							 $cmd = " select LAYER_SALARY_MAX
                                             from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$TMP_SALP_LEVEL' and LAYER_NO = 0 ";
                            $db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
							
                            if($tmp_CMD_SPSALARY && ($tmp_CMD_SALARY>$LAYER_SALARY_MAX)){
                                $tmp_CMD_SALARY=$LAYER_SALARY_MAX;
                                $tmp_CMD_SPSALARY=$tmp_CMD_SPSALARY;
                            }else{
                                $tmp_CMD_SALARY=$tmp_CMD_SALARY;
                            }
                        }
                                                
                        
                        
			// update status of PER_PERSONAL 
			$cmd = " update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY, 
							PER_DOCNO='$COM_NO', PER_DOCDATE='$tmp_COM_DATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			if($debug==1){echo'2:<pre>'.$cmd.'<br>';} 

			// update into PER_POSITION 
			if ($tmp_POS_ID) {
				$cmd = "	update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
									where POS_ID=$tmp_POS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();	
				if($debug==1){echo'3:<pre>'.$cmd.'<br>';} 			
			}
				
			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID 
							order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			if (substr($before_cmd_date,0,10) < substr($tmp_SAH_EFFECTIVEDATE,0,10)) $before_cmd_date = $tmp_SAH_EFFECTIVEDATE;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
							where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();							
			if($debug==1){echo'3:<pre>'.$cmd.'<br>';} 

			$SAH_REMARK = "";
			$cmd = " select TOTAL_SCORE, SALARY_FLAG, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $tmp_PER_ID and KF_CYCLE = $search_kf_cycle and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_TOTAL_SCORE = $data1[TOTAL_SCORE];
			if (!$tmp_TOTAL_SCORE) $tmp_TOTAL_SCORE = "NULL";
			$SALARY_FLAG = trim($data1[SALARY_FLAG]);
			if ($SALARY_FLAG=="N") $SAH_REMARK = trim($data1[SALARY_REMARK1]);

			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$SAH_ID = $data1[max_id] + 1; 			 

			$SAH_SEQ_NO = 1;
			if ($tmp_MOV_CODE=="21415") $EX_CODE = "197";
			else $EX_CODE = "024";
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, SAH_SALARY_UP, 
							SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, 
							EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE,
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_REMARK1, SAH_REMARK2) 
							values	($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$tmp_COM_DATE', '', $SESS_USERID, '$UPDATE_DATE', $tmp_CMD_PERCENT, $tmp_CMD_DIFF, 
							$tmp_CMD_SPSALARY, $SAH_SEQ_NO, '$SAH_REMARK', '$tmp_LEVEL_NO', '$SAH_POS_NO', 
							'$PL_NAME_WORK', '$ORG_NAME_WORK', '$EX_CODE', '$SAH_PAY_NO', $tmp_CMD_MIDPOINT, 
							$search_kf_year, $search_kf_cycle, $tmp_TOTAL_SCORE, 'Y', '$SM_CODE', $tmp_CMD_SEQ_NO, 
							'$SAH_ORG_DOPA_CODE', $tmp_CMD_OLD_SALARY, '$tmp_CMD_NOTE1 $comment1', '$tmp_CMD_NOTE2 $comment2') ";
			$db_dpis1->send_cmd($cmd);
                        if($debug==1){echo'5:<pre>'.$cmd.'<br>';} 
//			$db_dpis1->show_error();	
//			echo "$cmd<br>";
                        /********************/
                        $cmd="UPDATE PER_COMDTL SET 
                        CMD_NOTE1=CMD_NOTE1||'".$comment1."' ,
                        CMD_NOTE2=CMD_NOTE2||'".$comment2."' ,
                        MOV_CODE= '$tmp_MOV_CODE' 
                     WHERE COM_ID=".$COM_ID." AND CMD_SEQ=".$tmp_CMD_SEQ." AND PER_ID=".$tmp_PER_ID;
                        $db_dpis1->send_cmd($cmd);
                        if($debug==1){echo'6:<pre>'.$cmd.'<br>';} 
                        /********************/
		}	// 		while ($data = $db_dpis->get_array())		
		
                
		$cmd = " update PER_COMMAND set 
                            COM_CONFIRM=1, 
                            UPDATE_USER=$SESS_USERID, 
                            UPDATE_DATE='$UPDATE_DATE' ,
                            COM_NOTE=COM_NOTE||'".$comment2."' 
                        where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo'7:<pre>'.$cmd.'<br>';} 

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
		
		$COM_ID = "";	// เคลียร์ค่าว่าง
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID)){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO."]");
		$COM_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
                            a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID, a.ORG_ID, a.COM_LEVEL_SALP ,
                            a.COM_YEAR 
                        from PER_COMMAND a, PER_COMTYPE b
			where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$cnt = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd ($cnt)<br>";
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
                $CMD_YEAR_DB = $data[CMD_YEAR];
	
		$ORG_ID_DTL = $data[ORG_ID];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$ORG_NAME_DTL = $data1[ORG_NAME];
//		$ORG_ID_DTL = $data1[ORG_ID];
//		echo "cmd=$cmd (ORG_NAME_DTL=$ORG_NAME_DTL)<br>";

		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$DEPARTMENT_NAME = $data1[ORG_NAME];
//		echo "cmd=$cmd (DEPARTMENT_NAME=$DEPARTMENT_NAME)<br>";
                
                //pitak
                // หาค่าปีงบประมาณ และรอบการประเมิน
                $cmd = " SELECT CMD_DATE FROM PER_COMDTL WHERE COM_ID=$COM_ID AND rownum=1";
		$db_dpis1->send_cmd($cmd);
		$data2 = $db_dpis1->get_array();
                
                
                $CMD_YEAR="";
                $KfCycle = "";
                if(count($data2) > 0){
                    $tmpCmdDate = explode("-", $data2[CMD_DATE]) ; /*2016-10-01*/
                    $tmpYear =$tmpCmdDate[0];
                    $tmpCmdDateDB = $tmpCmdDate[0].$tmpCmdDate[1].$tmpCmdDate[2];
                    $dateCycle1 = $tmpYear. "0401";
                    $dateCycle2 = $tmpYear. "1001";
                    if(!empty($CMD_YEAR_DB)){
                        $CMD_YEAR = $CMD_YEAR_DB+543;
                    }else{
                        $CMD_YEAR = $tmpYear+543;
                    }
                    if($tmpCmdDateDB >=$dateCycle1 && $tmpCmdDateDB <$dateCycle2){ //รอบ 1
                        $KfCycle = "1";
                    }else{ //รอบ 2
                        $KfCycle = "2";
                    }
                    
                }// end pitak
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		$SELECTED_CP_ID = "";		

		if ($RPT_N) {
			$COM_TYPE = "5071";	//$COM_TYPE = "5142";  //สป.ศึกษาธิการ
			$COM_TYPE_NAME = "คำสั่งเลื่อนเงินเดือนข้าราชการ";
		} else {
			$COM_TYPE = "1005";
			$COM_TYPE_NAME = "คำสั่งเลื่อนเงินเดือน";
		}
		$search_per_name = "";
		$search_per_surname = "";		

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$search_ministry_id = "";
			$search_ministry_name = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$search_department_id = "";
			$search_department_name = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){ 
			$search_org_id = "";
			$search_org_name = "";
		} // end if
		$_select_level_no = array("O1","O2","O3","O4","K1","K2","K3","K4","K5","D1","D2","M1","M2");
	} // end if		
?>