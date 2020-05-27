<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");			

        $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
        
		
		
        $debug = 0; /*0=ปิด ,1=เปิด การแสดง SQL*/
        
        $cmdCycle = "select to_char(sysdate,'YYYY-MM-DD HH24:MI:SS') from dual";
        $db_dpis2->send_cmd($cmdCycle);
        $datasysdate = $db_dpis2->get_array_array();
        $cur_date =  $datasysdate[0];
        //echo $cur_date;
	
		
        /*function ESLockStart($db_dpis3,$TimeoutInSecond=0){
            $SQL="DECLARE nCount NUMBER; BEGIN SELECT COUNT(*) INTO nCount FROM dba_tables where table_name = 'ESDB_LOCKMAN'; IF(nCount <= 0) THEN execute immediate 'create table ESDB_LOCKMAN(lock_satatus int NOT NULL,CONSTRAINT ESDB_LockMan_pk PRIMARY KEY (lock_satatus))'; END IF;execute immediate 'LOCK TABLE ESDB_LOCKMAN in exclusive mode wait " & TimeoutInSecond & "';end;";
            
             $db_dpis3->send_cmd_fast($SQL);
             if(empty($db_dpis3->show_error())){
                return TRUE;
             }else{
                return FALSE;
             }
        }*/
       
       
        /*if(ESLockStart($db_dpis3,5000)){
            echo 'ESLockStart';
        }else{
            echo 'CAN NOT';
        }*/
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	//	กำหนดค่า default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
	
	set_default_timezone('Asia/Bangkok');	// ทำเวลาให้เป็นเวลาโซนที่กำหนด
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i A", time());
	$CANCEL_DATETIME = date("Y-m-d H:i A", time());
	$ORI_APPROVE_DATETIME = date("Y-m-d H:i A", time());
	$TODATE = date("Y-m-d");
	$temp_date = explode("-", $TODATE);
	$temp_todate = ($temp_date[0]) ."-". $temp_date[1] ."-". $temp_date[2];
	if(!$search_abs_startdate) {
		$search_abs_startdate = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
                
                /**/
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                $sql = "SELECT min(ABS_STARTDATE) AS ABS_STARTDATE FROM(
                        select a.ABS_STARTDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null ) and a.CANCEL_FLAG =0 
                           /* $condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
               // echo $sql;
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_STARTDATE]){
                            $ArrABS_STARTDATE = explode('-',trim($dataEnd[ABS_STARTDATE]));
                            $search_abs_startdate= $ArrABS_STARTDATE[2]."/". $ArrABS_STARTDATE[1] ."/". ($ArrABS_STARTDATE[0] + 543);
                        }
                    }   
                }
                /**/
	}
	if(!$search_abs_enddate) {		// จากเดือนปัจจุบันไปอีก 60 วัน
		if($temp_date[1]<11){
			$search_abs_endmonth = ($temp_date[1] + 3);
			if($search_abs_endmonth<10) $search_abs_endmonth = "0".($search_abs_endmonth);
			$search_abs_endyear = $temp_date[0];
		}else{	// เดือน 11 กับ 12 ต้องไปเริ่มปีหน้า
			$search_abs_endmonth = "0".($temp_date[1] - 10);
			$search_abs_endyear = ($temp_date[0] + 1);
		}
		$max_date = get_max_date($search_abs_endmonth, $search_abs_endyear);
                $search_abs_enddate = $max_date."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
                
                /*$sql="SELECT TO_CHAR(ADD_MONTHS(LAST_DAY(sysdate),4),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') AS MAXMONTHS FROM dual";
                $db_dpis->send_cmd($sql);
                $dataEnd = $db_dpis->get_array();
                $search_abs_enddate=$dataEnd[MAXMONTHS];*/
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
               //echo ">>>>>>>>>>>>>>>1";
               $sql = "SELECT max(ABS_ENDDATE) AS ABS_ENDDATE FROM(
                        select a.ABS_ENDDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            --and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null) 
                            /*$condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
//echo '<pre>'.$sql;
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_ENDDATE]){
                            $ArrABS_ENDDATE = explode('-',trim($dataEnd[ABS_ENDDATE]));
                            $search_abs_enddate= $ArrABS_ENDDATE[2]."/". $ArrABS_ENDDATE[1] ."/". ($ArrABS_ENDDATE[0] + 543);
                        }
                    }   
                }
            
            
	}
//	echo "-> $result_set  / $CREATE_DATE <br>";
	$alert_confirm_absent = "";
        
        
        /*========================================= 05/09/2561 ==============================================*/
        /*======================== คำนวณ เพิ่มวันลาสะสมอัตโนมัติ เมื่อถึงหรือ เกิน 6 เดือน =================================*/
        $tests=0; //0=ไม่ใช้งาน , 1=ใช้งาน
        $debug=0; //0=ไม่ใช้งาน , 1=ใช้งาน
        //if($tests==1 && ($SESS_PER_ID == 16435 || $SESS_PER_ID == 16501)){
        if($SESS_PER_ID){
            if($debug==1){echo  '<br>  บรรทัดที่ ->'.__LINE__.'<br>    - PER_ID: '.$SESS_PER_ID;}

            /* หา max_id PER_ABSENTSUM */
            $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $data = array_change_key_case($data, CASE_LOWER);
            $AS_ID = $data[max_id] + 1;


            /*============================== ข้อมูล PERSONAL ===================================*/
            $cmd = " select PER_ID, PER_STARTDATE, PER_CARDNO ,PER_TYPE
                       from PER_PERSONAL  where PER_STATUS = 1  and PER_ID = $SESS_PER_ID";

            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            if($debug==1){echo  '<br> บรรทัดที่ ->'.__LINE__.'<pre>'.$cmd;}
            //$db_dpis->show_error();

            $CHECK_DATE = date("Y-m-d");
            //echo $CHECK_DATE;
            $SQLDate="SELECT to_char(sysdate,'yyyy','NLS_CALENDAR=''THAI BUDDHA''') as yearnow , to_char(sysdate,'mmdd')  AS datenow   from dual";
            $db_dpis->send_cmd($SQLDate);
            if ($dataDate = $db_dpis->get_array()) {
                $dataDate = array_change_key_case($dataDate, CASE_LOWER);
                $year_now = trim($dataDate[yearnow]);
                $date_now = $year_now.trim($dataDate[datenow]);
            }else{
                $date_now=(date("Y") + 543).date("md"); 
                $year_now = date("Y")+543;
            }
            $VC_YEAR=$year_now; 
            if($date_now>=$year_now.'1001'){ 
              $VC_YEAR=$year_now+1; 
            }
            //echo $date_now;
            $CHECK_DATE_NEXT_YEAR = ($VC_YEAR-543)."-10-01"; 
            $CHECK_DATE_BEFORE_YEAR = ($VC_YEAR-1-543)."-10-01";
            $sep_CHECK_DATE = ($VC_YEAR-543)."-09-30";
            $A_CHECK_STARTDATE = ($VC_YEAR-543)."-04-01";
            $tmp_vc_year = $VC_YEAR - 1;

            $A_START_DATE = ($VC_YEAR-1-544)."-10-01";/*เดิม*/ //ปี งบ ก่อนหน้า
            $A_END_DATE = ($VC_YEAR-1-543)."-09-30";/*เดิม*/ //ปี งบ ก่อนหน้า

            $VC_START_DATE_1 = ($VC_YEAR -544) . "-10-01"; 
            $VC_END_DATE_1 = $VC_YEAR-543 . "-03-31";

            $VC_START_DATE_2 = ($VC_YEAR-543) . "-04-01"; 
            $VC_END_DATE_2 = $VC_YEAR-543 . "-09-30"; 
            $PER_CARDNOS = trim($data[PER_CARDNO]);
            $PER_STARTDATE_1 = trim($data[PER_STARTDATE]);
            $A_PER_TYPE = trim($data[PER_TYPE]);
            /*==================================== END ========================================*/

            if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
            
            $cmd_con = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
            $db_dpis2->send_cmd($cmd_con);
            if($debug==1){echo  '<br> บรรทัดที่ ->'.__LINE__.'<pre>'.$cmd_con."<br>";}
            $data_con = $db_dpis2->get_array();
            if($debug==2){echo  '<br> บรรทัดที่ ->'.__LINE__.'VC_DAY:'.$data_con[VC_DAY];}
            if($data_con[VC_DAY] < 1){
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.'== '.$A_CHECK_STARTDATE." >= ".$PER_STARTDATE_1."<br>";}
                if ($A_CHECK_STARTDATE >= $PER_STARTDATE_1) { /*บรรจุก่อนวันที่ 1 เมษายน*/

                    if($debug==2){echo '<br>บรรทัดที่ ->'.__LINE__.'CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE_1."<br>";}

                    $AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE_1, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
                    $arr_temp = explode(" ", $AGE_DIFF);
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[0]='.$arr_temp[0]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[2]='.$arr_temp[2]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[4]='.$arr_temp[4]."<br>";}
                    /*เพิ่มเติม*/
                    $AGE_DIFF_NEXT_YEAR = date_vacation($CHECK_DATE_NEXT_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_next_year = explode(" ", $AGE_DIFF_NEXT_YEAR);

                    $AGE_DIFF_BEFORE_YEAR = date_vacation($CHECK_DATE_BEFORE_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_before_year = explode(" ", $AGE_DIFF_BEFORE_YEAR);

                    $Governor_Age_Sep = 0;
                    $Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
                    $Governor_Age_next_year = ($arr_temp_next_year[0]*10000)+($arr_temp_next_year[2]*100)+(($arr_temp_next_year[4]*1));
                    $Governor_Age_before = ($arr_temp_before_year[0]*10000)+($arr_temp_before_year[2]*100)+(($arr_temp_before_year[4]*1));

                    $dd=$arr_temp[4];
                    if(strlen($dd)==1){$dd='0'.$dd;}

                    $Leave_Cumulative=0; // def วันลาสะสม
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'<h1>เช็คอายุราชการว่า ถึง 6 เดือน หรือ มากกว่า 6 เดือนแล้วหรือไม่ </h1><br>';}
                    if($debug==1){echo 'บรรทัดที่ ->'.__LINE__.'<h1>อายุราชการ'.$arr_temp[0].'ปี '.$arr_temp[2].'เดือน '.$arr_temp[4].'วัน</h1><br>';}

                    //================================================================================================
                    /*=========================== เช็คอายุราชการถึง 6 เดือน หรือ เกิน 6 เดือน หรือยัง ============================*/
                    if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //อายุราชการมากกว่าหรือเท่ากับ 6 เดือนหรือไม่ m dd
                        
						// เช็คว่า มีข้อมูลปีที่แล้วหรือยัง
						$tmpVC_YEAR=$VC_YEAR-1;
						$cmd = " select VC_DAY from PER_VACATION 
							where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
						$count_data = $db_dpis1->send_cmd($cmd);
						if($debug==1){echo __LINE__.':select = '.$cmd.'<br><br>';}
		
						if ($count_data){
							if($arr_temp[0]==0){ //หลักเดือน
								$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
													  WHERE AC_FLAG = 1 AND ($arr_temp[2]/12) < AC_GOV_AGE ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$AC_DAY = $data1[AC_DAY];
								$AC_COLLECT = $data1[AC_COLLECT];
								if($debug==1){echo __LINE__.': AC_COLLECT=>'.$AC_COLLECT.'==><pre>'.$cmd.'<br>';}
							}
							if($arr_temp[0]>=1 && $arr_temp[0]<=10){ //between 1-10
								 $AC_DAY = 10;
								 $AC_COLLECT = 20;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
							if($arr_temp[0]>10){ //10up
								 $AC_DAY = 10;
								 $AC_COLLECT = 30;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
						}else{
											
							if(strlen ($arr_temp[2])==1){$XarrM= '0'.$arr_temp[2];}else{$XarrM= $arr_temp[2];}
								$xYM = (int) $arr_temp[0].'.'.$XarrM;
								if($xYM < 0.06){ //หลักเดือน
									$AC_DAY = 0;
									$AC_COLLECT = 0;
									//echo '<br>น้อยกว่า 6 เดือน AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
								if($xYM >= 0.06){ // มากกว่า 6 เดือน น้อยกว่า 1 ปี
									$AC_DAY = 10;
									$AC_COLLECT = 10;
									//echo '<br>6 เดือนน้อยกว่า 1 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
		
								if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 ปี -10 ปี
									 $AC_DAY = 10;
									 $AC_COLLECT = 20;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br>1 ปีน้อยกว่า 10 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
								if($arr_temp[0]>=10){ //10up
									 $AC_DAY = 10;
									 $AC_COLLECT = 30;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br> 10 ปีขึ้นไป AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
							
							
						}	
							
                        $Leave_Cumulative=10; //A
                        /*กำหนดค่าเริ่มต้น*/
                        if($Governor_Age<600){ //น้อยกว่า 6 เดือน
                           $Leave_Cumulative=0;					
                        }
                        $Before_vc_day=0;//N

                        if($debug==1){echo __LINE__.':Governor_Age '.$Governor_Age.'<br>';}
                        if($Governor_Age>600){ //Y
                           //มี Record เก่าตั้งต้น?ปีที่แล้ว
                            $cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$tmp_vc_year'and PER_ID=$SESS_PER_ID ";
                            if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                            $cnt=$db_dpis1->send_cmd($cmd);
                            //$Before_vc_day=10;//N //Comment Release 5.2.1.19 20180403 
                            if($cnt){//Y
                                $data1 = $db_dpis1->get_array();
                                $Before_vc_day = $data1[VC_DAY]; 
                            }else{
                                // เก่าแก้โดย Kiitphat if($Governor_Age>=1600){ //between 1 to 10 =20 day
                                if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
                                    $Before_vc_day = 20; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }elseif($Governor_Age>=100000){ //10up= 30 day
                                    $Before_vc_day = 30; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }
                            }
                        }
                        //จำนวนลาพักผ่อนของปีงบก่อนหน้า
                        $sum_absday= get_sum_absday($db_dpis1,$SESS_PER_ID,$A_START_DATE,$A_END_DATE);
                        if($debug==1){echo __LINE__.':sum_absday==>'.$sum_absday.'<br>';}
                        //สรุปวันลาและวันลาพักผ่อนสะสมของปีงบก่อนหน้า
                        $cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
                                 where PER_ID=$SESS_PER_ID and START_DATE >= '$A_START_DATE' and END_DATE <= '$A_END_DATE' ";
                        $db_dpis1->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                        $data1 = $db_dpis1->get_array();
                        $data1 = array_change_key_case($data1, CASE_LOWER);
						
                        if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$Before_abs_day_vacation = 0; 
						}else{
							$Before_abs_day_vacation = $data1[abs_day]+0; 
						}
                        if($debug==1){echo __LINE__.':Before_abs_day_vacation==>'.$Before_abs_day_vacation.'<br>';}

                        $Before_vc_day=$Before_vc_day-($sum_absday+$Before_abs_day_vacation);

                        if($debug==1){echo __LINE__.':<font color=red>'.$Before_vc_day.'='.$Before_vc_day.'-('.$sum_absday.'+'.$Before_abs_day_vacation.')</font><br>';}

                        if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                        if($debug==1){echo __LINE__.':'.$CHECK_DATE_NEXT_YEAR.' อายุราชการปีที่คลิกประมวลผล ==>'.$Governor_Age.'<br>';}

                        if($Before_vc_day>0){
                            /**/
                            if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                            $Leave_Cumulative=$Leave_Cumulative+$Before_vc_day;


                            /**/

                        }
                         if($debug==1){echo __LINE__.':Leave_Cumulative==>'.$Leave_Cumulative.'<br>';}
                    }else{ // N
                        $Leave_Cumulative=0;
                    }
                    if($Governor_Age>=100000){ // 10up
                        $AC_COLLECT = 30;
						if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$AC_COLLECT = 10;
						 }
                    }
                    if($debug==1){echo __LINE__.':'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
                    if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //อายุราชการมากกว่าหรือเท่ากับ 10 ปีหรือไม่ yy mm dd
                        $Leave_Cumulative=$AC_COLLECT;// 30 สะสมสูงสุดของแต่ละเงื่อนไข
                    }
                    if($debug==2){
                        echo '<br>====================<br><h1>อายุราชการ: '.$Governor_Age.' ,วันลาสะสมที่ควรได้:'.$Leave_Cumulative.' สะสมสูงสุด ['.$AC_COLLECT.']</h1><br>====================<br>';
                    }
                    /*===================================================================================*/
                    if($debug==1){echo __LINE__.':$A_PER_TYPE = '.$A_PER_TYPE.'<br><br>';}
                    if($A_PER_TYPE==3){
                        if($employees_year_up==TRUE){
                            if($Leave_Cumulative>15){
                                $AC_DAY = 15;
                            }else{
                                $AC_DAY = $Leave_Cumulative;
                            }

                        }else{
                            $AC_DAY = $AC_DAY;
                        }

                        if($debug==1){echo __LINE__.':AC_DAY = '.$AC_DAY.'<br><br>';}
                        if($debug==1){echo __LINE__.':อายุพนักงานราชการGovernor_Age = '.$Governor_Age.'<br><br>';}

                    }else{
                        $AC_DAY=$Leave_Cumulative;
                    }
                    if($Governor_Age<600){ //น้อยกว่า 6 เดือน

                       $AC_DAY=0;

                    }
                    $cmd = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID AND VC_DAY>0   ";
                    $chk_count_data = $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':select = '.$cmd.'<br><br>';}
                    if (!$chk_count_data) {
						$cmd = " delete from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
						$db_dpis->send_cmd($cmd);
										
                        $cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
                                                            values ('$VC_YEAR', $SESS_PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
                        if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':insert = '.$cmd.'<br><br>';}
                        //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($VC_YEAR)." : ".$SESS_PER_ID."]");
						
						
                    }
                }
				
				// kittiphat comment 09/10/2561
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 1 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                        values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 1, '$VC_START_DATE_1', '$VC_END_DATE_1', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 2 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                    values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 2, '$VC_START_DATE_2', '$VC_END_DATE_2', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
				
            }
        }
        /*=================================================================================================*/
        /*============================== END การทำงาน คำนวณวันลาพักผ่อนสะสม  =================================*/
        function get_sum_absday($db_dpis1,$PER_ID,$TMP_START_DATE,$TMP_END_DATE){
            /*
            :PER_ID, 
            :FIRST_DATE='2015-10-01'		วันที่เริ่มต้นปีงบประมาณ
            :END_DATE='2016-09-30'		วันที่สิ้นสุดปีงบประมาณ
            */
                $cmd="
            WITH
            PakPonInFiscalYear AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                         cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                       cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19))
            ),
            SeparateBegin AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate < cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            ),
            SeparateEnd AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_enddate > cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            )

            select nvl(sum(abs_day),0) sum_abs  from (
              select 1 a,ABS_STARTDATE,abs_day 
                from PakPonInFiscalYear 
              union all
                select 2,WORK_DATE,case when WORK_DATE=trim(b.ABS_STARTDATE) then 
                        case when b.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(b.ABS_ENDDATE) then 
                              case when b.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateBegin b
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateBegin), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
              union all
                select 3,WORK_DATE,case when WORK_DATE=trim(e.ABS_STARTDATE) then 
                        case when e.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(e.ABS_ENDDATE) then 
                              case when e.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateEnd e
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateEnd), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
            )";
                $cmd=str_replace(":PER_ID",$PER_ID,$cmd);
                $cmd=str_replace(":FIRST_DATE","'".$TMP_START_DATE."'",$cmd);
                $cmd=str_replace(":END_DATE","'".$TMP_END_DATE."'",$cmd);
                $db_dpis1->send_cmd_fast($cmd);
                //echo  '<br>'.$cmd.'<br>';
                $data1 = $db_dpis1->get_array_array();
                $sum_absday = $data1[0]; //C
                return $sum_absday;
        }
        
	//#### ส่งอีเมล์การลาอัตโนมัติ ####//
	function absent_auto_sendmail($ABS_SENDER_MAIL, $ABS_SENDER_NAME, $ABS_RCV_MAIL, $ABS_RCV_NAME, $AB_NAME, $ABS_STARTDATE, $ABS_ENDDATE, $ABS_REASON, $ABS_APPROVE){
			//global $db_dpis;
			$result = 0;		$abs_reason = "";
			if($ABS_SENDER_MAIL)	$ABS_SENDER_MAIL_SHOW = "<".$ABS_SENDER_MAIL.">";
			if($ABS_RCV_MAIL)	$ABS_RCV_MAIL_SHOW = "<".$ABS_RCV_MAIL.">";
		
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
		
			// Additional headers
			$headers .= 'To: '.$ABS_RCV_NAME." ".$ABS_RCV_MAIL_SHOW.'\r\n';
			$headers .= 'From: '.$ABS_SENDER_NAME." ".$ABS_SENDER_MAIL_SHOW.'\r\n';
			//$headers .= 'Cc: ' . "\r\n";	$headers .= 'Bcc: ' . "\r\n";
			$headers .= 'X-Mailer: PHP/'. phpversion();

			if($ABS_STARTDATE){
				$arr_temp = explode("-", $ABS_STARTDATE);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
			}
			
			if($ABS_ENDDATE){
				$arr_temp = explode("-", $ABS_ENDDATE);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;
			}
			if($ABS_REASON)	$abs_reason = " เนื่องจาก".$ABS_REASON;
			if ($ABS_APPROVE==0){  //ผู้ลา ส่งถึง ผู้อนุญาต
				$subject = $ABS_SENDER_NAME." ขออนุญาตลาในวันที่ ".$ABS_STARTDATE_ce_era; 
				$message = "\n".$ABS_SENDER_NAME." ขออนุญาตลาวันที่ ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era." (".$AB_NAME.$abs_reason.")\n\n"; 
				$result_type = 1;
			}else{		// ผู้อนุญาต ส่งถึง ผู้ลา
				if ($ABS_APPROVE==1)	$ABS_APPROVE = "อนุญาตการลา";
				if ($ABS_APPROVE==2)	$ABS_APPROVE = "ไม่อนุญาตการลา";
				$subject = $ABS_SENDER_NAME." ".$ABS_APPROVE." ในวันที่ ".$ABS_STARTDATE_ce_era."\n\n"; 
				$message = "\n".$ABS_SENDER_NAME.$ABS_APPROVE."ของ".$ABS_RCV_NAME." วันที่ ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era." (".$AB_NAME.$abs_reason.")\n\n"; 
				$result_type = 2;
			}
			$footers = "\n\n".$ABS_SENDER_NAME."\n";
			$message = wordwrap(trim($message.$footers), 70);
			
			// Mail it
			$result = 0;
			if($ABS_RCV_MAIL)	$result = @mail($ABS_RCV_MAIL, $subject, $message, $headers);
			if($result){
				//echo "$result ส่งอีเมล์สำเร็จ";
			}else{
				//echo "$result ส่งอีเมล์ไม่สำเร็จ";
			}
			if($result_type && $result==1)	$result = $result_type;
			
			//echo "<br>=> $result <=  $ABS_RCV_MAIL, $subject, $message.$footers, $headers<br>";
						
	return $result;
	} // end function
	
	//#### แสดงข้อมูลวันลาสะสม รอบ และปีนั้น (ต้องอนุญาตแล้ว)####//
	function get_ABSENT_SUM($PER_ID,$ABS_STARTDATE){
		global $db_dpis, $UPDATE_DATE;
		global $AS_YEAR,
		$AS_CYCLE,
		$START_DATE_1,
		$END_DATE_1,
		$START_DATE_2,
		$END_DATE_2,
		$AB_CODE_01,
		$AB_COUNT_01,
		$ABS_DATE_01,
		$AB_CODE_03,
		$AB_COUNT_03,
		$ABS_DATE_03,
		$AB_CODE_02,
		$ABS_DATE_02,
		$AB_CODE_04,
		$ABS_DATE_04,
		$AB_COUNT_TOTAL_04,
		$AB_COUNT_04;

		if($ABS_STARTDATE){
			$CHECK_ENDDATE = save_date($ABS_STARTDATE);
			if (substr($ABS_STARTDATE,3,2) > "09" || substr($ABS_STARTDATE,3,2) < "04") $AS_CYCLE = 1;
			elseif (substr($ABS_STARTDATE,3,2) > "03" && substr($ABS_STARTDATE,3,2) < "10")	$AS_CYCLE = 2;
			$AS_YEAR = substr($ABS_STARTDATE, 6, 4);
			if($AS_CYCLE==1){	//ตรวจสอบรอบการลา
				if (substr($ABS_STARTDATE,3,2) > "09") $AS_YEAR += 1;
				$START_DATE = ($AS_YEAR - 1) . "-10-01";
				$END_DATE = $AS_YEAR . "-03-31";
			}else if($AS_CYCLE==2){
				$START_DATE = $AS_YEAR . "-04-01";
				$END_DATE = $AS_YEAR . "-09-30"; 
			}
		} 
	
		if(!$AS_YEAR){
			if(date("Y-m-d") < date("Y")."-10-01") $AS_YEAR = date("Y") + 543;
			else $AS_YEAR = (date("Y") + 543) + 1;
		}
		$START_DATE_1 = "01/10/". ($AS_YEAR - 1);
		$END_DATE_1 = "31/03/". $AS_YEAR;
		$START_DATE_2 = "01/04/". $AS_YEAR;
		$END_DATE_2 = "30/09/". $AS_YEAR;
		if (!$CHECK_ENDDATE) $CHECK_ENDDATE =  save_date($END_DATE_2);
	
		if (!$AS_CYCLE){
			if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $AS_CYCLE = 1;
			elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $AS_CYCLE = 2;
		}
		if($AS_CYCLE == 1){
			$START_DATE =  save_date($START_DATE_1);
			$END_DATE =  save_date($END_DATE_1);
		}else{
			$START_DATE =  save_date($START_DATE_2);
			$END_DATE =  save_date($END_DATE_2);
		} // end if
		//echo "--> $ABS_STARTDATE // $AS_YEAR :: $AS_CYCLE +".substr($ABS_STARTDATE,3,2)." $START_DATE --- $END_DATE";
		
		$AB_CODE_01 = $AB_COUNT_01 = $AB_CODE_02 = $AB_CODE_03 = $AB_COUNT_03 = $AB_CODE_04 = 0;
		if ($BKK_FLAG==1)
			$code = array(	"01", "02", "03", "04" );
		else
			$code = array(	"01", "02", "03", "04" );
		$code_0 = array(	"AB_CODE_01", "AB_CODE_02", "AB_CODE_03", "AB_CODE_04" );
		
		$cmd = " SELECT		AB_CODE_01, AB_COUNT_01, AB_CODE_02, AB_CODE_03, AB_COUNT_03, AB_CODE_04 
							FROM		PER_ABSENTSUM 
							WHERE	PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE ";
		$count_abs_sum = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
	//echo "-------------------------- $cmd<br>";
	
		if($count_abs_sum  > 0){
			$AB_CODE_01 = $data[AB_CODE_01];
			$AB_COUNT_01 = $data[AB_COUNT_01];
			$AB_CODE_02 = $data[AB_CODE_02];
			$AB_CODE_03 = $data[AB_CODE_03];
			$AB_COUNT_03 = $data[AB_COUNT_03];
			$AB_CODE_04 = $data[AB_CODE_04];
		} //end if($count_abs_sum  > 0)
		//###END SHOW PER_ABSENTSUM  ====================================
		
		for ( $i=0; $i<count($code); $i++ ) { 
			if ($code[$i]=="04") $CHECK_STARTDATE =  save_date($START_DATE_1);
			else $CHECK_STARTDATE =  $START_DATE;

			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$CHECK_STARTDATE' and ABS_ENDDATE <= '$CHECK_ENDDATE' ";
			$db_dpis->send_cmd($cmd);
			//echo "-------------------------- $cmd<br>";
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$$code_0[$i] += $data[abs_day]; 		
	
			if ($code[$i]=="01" || $code[$i]=="03") {
				$cmd = " select count(ABS_DAY) as abs_count from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_STARTDATE >= '$START_DATE' and ABS_ENDDATE <= '$CHECK_ENDDATE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				if ($code[$i]=="01") $AB_COUNT_01 += $data[abs_count];
				elseif ($code[$i]=="03") $AB_COUNT_03 += $data[abs_count];
			}
	
			${"ABS_DATE_".$code[$i]} = "";
			// หาวันที่ลาล่าสุด
			$cmd = " select ABS_STARTDATE, ABS_ENDDATE  
							from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='$code[$i]' and ABS_ENDDATE < '$CHECK_ENDDATE'
							order by ABS_STARTDATE DESC, ABS_ENDDATE DESC, ABS_ID DESC ";
			$count_data = $db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
//				echo "-------------------------- $cmd<br>";
			if ($count_data) {
				$arr_temp = explode("-", $data[ABS_STARTDATE]);
				$ABS_START_DAY = trim($arr_temp[2]);
				$ABS_START_MONTH = trim($arr_temp[1]);
				$ABS_START_YEAR = trim($arr_temp[0] + 543);
				$ABS_STARTDATE_ce_era =  $ABS_START_DAY."/".$ABS_START_MONTH."/".$ABS_START_YEAR ;
				
				$arr_temp = explode("-", $data[ABS_ENDDATE]);
				$ABS_END_DAY = trim($arr_temp[2]);
				$ABS_END_MONTH = trim($arr_temp[1]);
				$ABS_END_YEAR = trim($arr_temp[0] + 543);
				$ABS_ENDDATE_ce_era =  $ABS_END_DAY."/".$ABS_END_MONTH."/".$ABS_END_YEAR ;

//					echo "$cmd --> $code[$i] =>  ".$ABS_STARTDATE_ce_era." - ".$ABS_ENDDATE_ce_era."<br>";
				${"ABS_DATE_".$code[$i]} = $ABS_ENDDATE_ce_era;		// วันที่ลาล่าสุด
//					echo ${"ABS_DATE_".$code[$i]}."<br>";
			}
		} // end for
		
		if($AS_CYCLE==2){			// รอบ 2
			$TMP_START_DATE =  save_date($START_DATE_1);
			$TMP_END_DATE =  save_date($END_DATE_1);
			$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
							where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TMP_AB_CODE_04 = $data[abs_day]+0; 
	
			$cmd = " select AB_CODE_04 from PER_ABSENTSUM 
							where PER_ID=$PER_ID and START_DATE = '$TMP_START_DATE' and END_DATE = '$TMP_END_DATE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TMP_AB_CODE_04 += $data[AB_CODE_04]+0; 
		}
		// การลาพักผ่อน
		$cmd = " select VC_DAY from PER_VACATION 
						where VC_YEAR='$AS_YEAR'and PER_ID=$PER_ID ";
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_COUNT_TOTAL_04 = $data[VC_DAY]; 	// วันลาพักผ่อนที่ลาได้ทั้งหมดในปีงบประมาณ
		$AB_COUNT_04 = $AB_COUNT_TOTAL_04 - $AB_CODE_04;		// วันลาสะสมที่เหลือ
	} // end function
	
	if($SESS_PER_ID ){ 	// ผู้ล็อกอินเข้ามา
            if($SESS_PER_AUDIT_FLAG == 1 && $PER_ID){
                $PER_ID = $PER_ID;
            }else{
                $PER_ID = $SESS_PER_ID;
            }
		

		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, a.PER_EMAIL
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO , a.PER_EMAIL
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, a.PER_EMAIL
							 from 		PER_PERSONAL a
											inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
											left join PER_POSITION c on a.POS_ID = c.POS_ID
											left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
							where		a.PER_ID = $PER_ID ";
		} // end if	
                //echo $cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();

               
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];
                
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];
		
		$PER_EMAIL = trim($data[PER_EMAIL]);

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
	} // end if
       // echo '>>>'.$PER_ID.' && '.$SESS_PER_AUDIT_FLAG.' != 1';
	if($PER_ID ){
            
		get_ABSENT_SUM($PER_ID,$ABS_STARTDATE);		// ดึงวันลาสะสมของผู้ที่ล็อกอินมาลา
		
		if($ORG_ID){
			$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_ID = $data[ORG_ID_REF];
		}
		
		if($DEPARTMENT_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		}

		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
	} // end if

	//echo "-> $command <br>";
	if($command == "ADD" || $command == "UPDATE"){		//ไม่ได้ 	ADD ใน PER_ABSENTHIS
		if (!$AUDIT_PER_ID) $AUDIT_PER_ID = "NULL";
		if (!$REVIEW1_PER_ID) $REVIEW1_PER_ID = "NULL";
		if (!$REVIEW2_PER_ID) $REVIEW2_PER_ID = "NULL";
		if(trim($AB_CODE) == "10"){
			$ABS_STARTPERIOD = 3;
			$ABS_ENDDATE = $ABS_STARTDATE;
			$ABS_ENDPERIOD = $ABS_STARTPERIOD;
			$ABS_LETTER = 0;
			$ABS_DAY_OLD = $ABS_DAY;
		} // end if
		if(!$ABS_LETTER)	$ABS_LETTER = 0;		//default
		if($AB_CODE){
			$cmd = "select 	 AB_NAME,AB_TYPE	from	PER_ABSENTTYPE	where AB_CODE= $AB_CODE"; 	
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$AB_NAME = trim($data[AB_NAME]);
                        $AB_TYPE = trim($data[AB_TYPE]);
		}

		if(!$ABS_AUDIT_FLAG && $ABS_AUDIT_FLAG_HIDDEN)				$ABS_AUDIT_FLAG = $ABS_AUDIT_FLAG_HIDDEN;
		if(!$ABS_REVIEW1_FLAG && $ABS_REVIEW1_FLAG_HIDDEN)		$ABS_REVIEW1_FLAG = $ABS_REVIEW1_FLAG_HIDDEN;
		if(!$ABS_REVIEW2_FLAG && $ABS_REVIEW2_FLAG_HIDDEN)		$ABS_REVIEW2_FLAG = $ABS_REVIEW2_FLAG_HIDDEN;
		if(!$ABS_APPROVE_FLAG && $ABS_APPROVE_FLAG_HIDDEN)	$ABS_APPROVE_FLAG = $ABS_APPROVE_FLAG_HIDDEN;
		if(!$ABS_CANCEL_FLAG && $ABS_CANCEL_FLAG_HIDDEN)		$ABS_CANCEL_FLAG = $ABS_CANCEL_FLAG_HIDDEN;
		if(!$ABS_STARTPERIOD && $ABS_STARTPERIOD_HIDDEN) 			$ABS_STARTPERIOD = $ABS_STARTPERIOD_HIDDEN;
		if(!$ABS_ENDPERIOD && $ABS_ENDPERIOD_HIDDEN)					$ABS_ENDPERIOD	= $ABS_ENDPERIOD_HIDDEN;
		
		/*เดิม*/
                /*
                 *if (!$ABS_AUDIT_FLAG)			$ABS_AUDIT_FLAG=0;
		 *if (!$ABS_REVIEW1_FLAG)		$ABS_REVIEW1_FLAG=0;
		 *if (!$ABS_REVIEW2_FLAG)		$ABS_REVIEW2_FLAG=0;
                */
                
                
                if (!$ABS_AUDIT_FLAG || $AUDIT_PER_ID=="NULL") $ABS_AUDIT_FLAG=0;
		if (!$ABS_REVIEW1_FLAG || $REVIEW1_PER_ID=="NULL") $ABS_REVIEW1_FLAG=0;
		if (!$ABS_REVIEW2_FLAG || $REVIEW2_PER_ID=="NULL") $ABS_REVIEW2_FLAG=0;
                
		if (!$ABS_APPROVE_FLAG)	$ABS_APPROVE_FLAG=0;
		if (!$ABS_CANCEL_FLAG)		$ABS_CANCEL_FLAG=0;
		if(!$ABS_STARTPERIOD) 		$ABS_STARTPERIOD = 3;
		if(!$ABS_ENDPERIOD)			$ABS_ENDPERIOD	= 3;
		
                
                /**/
                
                /**/
                
                
                
		// (HIDDEN (จาก DB)เป็น ""/0/NULL (ยังไม่ลงความเห็น) แต่ พอ FLAG ตัวใหม่มา!0 คือเป็นการลงความเห็นใหม่วันนี้)
		/*เดิม*/
                /*if($AUDIT_PER_ID != "NULL" && $ABS_AUDIT_FLAG!=0  && 
                        ($ABS_AUDIT_FLAG_HIDDEN==0 || $ABS_AUDIT_FLAG_HIDDEN=="" || $ABS_AUDIT_FLAG_HIDDEN=="NULL" || $ABS_AUDIT_FLAG_HIDDEN=="null"))	{
			$AUDIT_DATE = $TODATE;   
		}*/
               
                 /*เดิม*/
		/*if($REVIEW1_PER_ID!="NULL" && $ABS_REVIEW1_FLAG!=0 && 
                        ($ABS_REVIEW1_FLAG_HIDDEN==0 || $ABS_REVIEW1_FLAG_HIDDEN=="" || $ABS_REVIEW1_FLAG_HIDDEN=="NULL" || $ABS_REVIEW1_FLAG_HIDDEN=="null"))	 {
			$REVIEW1_DATE = $TODATE;
		}*/
                /*เดิม*/
		/*if($REVIEW2_PER_ID !="NULL" && $ABS_REVIEW2_FLAG!=0 && 
                        ($ABS_REVIEW2_FLAG_HIDDEN==0 || $ABS_REVIEW2_FLAG_HIDDEN=="" || $ABS_REVIEW2_FLAG_HIDDEN=="NULL" || $ABS_REVIEW2_FLAG_HIDDEN=="null"))	 {
			$REVIEW2_DATE = $TODATE;
                    
		}*/
                 /*เดิม*/
		/*if($ABS_APPROVE_FLAG!=0 && 
                        ($ABS_APPROVE_FLAG_HIDDEN==0 || $ABS_APPROVE_FLAG_HIDDEN=="" || $ABS_APPROVE_FLAG_HIDDEN=="NULL" || $ABS_APPROVE_FLAG_HIDDEN=="null"))	{
			$APPROVE_DATE = $TODATE;
                   
		}*/
                /*Release 5.1.0.8 begin */
                 if($AUDIT_PER_ID != "NULL" && $ABS_AUDIT_FLAG!=0  && $AUDIT_DISPLAY_DATE){ 
                    $AUDIT_DATE =save_date($AUDIT_DISPLAY_DATE);
                }
                if($REVIEW1_PER_ID!="NULL" && $ABS_REVIEW1_FLAG!=0 && $REVIEW1_DISPLAY_DATE){
                    $REVIEW1_DATE =save_date($REVIEW1_DISPLAY_DATE);
                }
                if($REVIEW2_PER_ID !="NULL" && $ABS_REVIEW2_FLAG!=0 && $REVIEW2_DISPLAY_DATE){
                    $REVIEW2_DATE =save_date($REVIEW2_DISPLAY_DATE);
                }
                 if($ABS_APPROVE_FLAG!=0 && $APPROVE_DISPLAY_DATE){
                    $APPROVE_DATE =save_date($APPROVE_DISPLAY_DATE);
                }
		 /*Release 5.1.0.8 End */
		if (!$AUDIT_DATE) 	$AUDIT_DATE = "NULL";
		if (!$REVIEW1_DATE) 	$REVIEW1_DATE = "NULL";
		if (!$REVIEW2_DATE) 	$REVIEW2_DATE = "NULL";
		if (!$APPROVE_DATE) 	$APPROVE_DATE = "NULL";
		
                
        if($SESS_PER_ID){
			//$debug = 1;
			
            if($debug==1){echo  '<br>  บรรทัดที่ ->'.__LINE__.'<br>    - PER_ID: '.$SESS_PER_ID;}
            $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $data = array_change_key_case($data, CASE_LOWER);
            $AS_ID = $data[max_id] + 1;


            /*============================== ข้อมูล PERSONAL ===================================*/
            $cmd = " select PER_ID, PER_STARTDATE, PER_CARDNO ,PER_TYPE
                       from PER_PERSONAL  where PER_STATUS = 1  and PER_ID = $SESS_PER_ID";

            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            if($debug==1){echo  '<br> บรรทัดที่ ->'.__LINE__.'<pre>'.$cmd;}
            //$db_dpis->show_error();

            $CHECK_DATE = date("Y-m-d");
            //echo $CHECK_DATE;
            /*$SQLDate="SELECT to_char(sysdate,'yyyy','NLS_CALENDAR=''THAI BUDDHA''') as yearnow , to_char(sysdate,'mmdd')  AS datenow   from dual";
            $db_dpis->send_cmd($SQLDate);
            if ($dataDate = $db_dpis->get_array()) {
                $dataDate = array_change_key_case($dataDate, CASE_LOWER);
                $year_now = trim($dataDate[yearnow]);
                $date_now = $year_now.trim($dataDate[datenow]);
            }else{
                $date_now=(date("Y") + 543).date("md"); 
                $year_now = date("Y")+543;
            }*/
			//หยิปบันที่ลา
			$arr_abs_startdate = explode("/",$ABS_STARTDATE);
            $date_now=$arr_abs_startdate[2].$arr_abs_startdate[1].$arr_abs_startdate[0];
            $year_now = $arr_abs_startdate[2];
				
            $VC_YEAR=$year_now; 
            if($date_now>=$year_now.'1001'){ 
              $VC_YEAR=$year_now+1; 
            }
           // echo $date_now.">=".$year_now.'1001';
            $CHECK_DATE_NEXT_YEAR = ($VC_YEAR-543)."-10-01"; 
            $CHECK_DATE_BEFORE_YEAR = ($VC_YEAR-1-543)."-10-01";
            $sep_CHECK_DATE = ($VC_YEAR-543)."-09-30";
            $A_CHECK_STARTDATE = ($VC_YEAR-543)."-04-01";
            $tmp_vc_year = $VC_YEAR - 1;

            $A_START_DATE = ($VC_YEAR-1-544)."-10-01";/*เดิม*/ //ปี งบ ก่อนหน้า
            $A_END_DATE = ($VC_YEAR-1-543)."-09-30";/*เดิม*/ //ปี งบ ก่อนหน้า

            $VC_START_DATE_1 = ($VC_YEAR -544) . "-10-01"; 
            $VC_END_DATE_1 = $VC_YEAR-543 . "-03-31";

            $VC_START_DATE_2 = ($VC_YEAR-543) . "-04-01"; 
            $VC_END_DATE_2 = $VC_YEAR-543 . "-09-30"; 
            $PER_CARDNOS = trim($data[PER_CARDNO]);
            $PER_STARTDATE_1 = trim($data[PER_STARTDATE]);
            $A_PER_TYPE = trim($data[PER_TYPE]);
            /*==================================== END ========================================*/

            if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
            
            $cmd_con = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
            $db_dpis2->send_cmd($cmd_con);
            if($debug==1){echo  '<br> บรรทัดที่ ->'.__LINE__.'<pre>'.$cmd_con."<br>";}
            $data_con = $db_dpis2->get_array();
			//$debug = 2;
            if($debug==2){echo  '<br> บรรทัดที่ ->'.__LINE__.'VC_DAY:'.$data_con[VC_DAY];}
            if($data_con[VC_DAY] < 1){
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.'== '.$A_CHECK_STARTDATE." >= ".$PER_STARTDATE_1."<br>";}
                if ($A_CHECK_STARTDATE >= $PER_STARTDATE_1) { /*บรรจุก่อนวันที่ 1 เมษายน*/

                    if($debug==2){echo '<br>บรรทัดที่ ->'.__LINE__.'CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE_1."<br>";}

                    $AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE_1, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
                    $arr_temp = explode(" ", $AGE_DIFF);
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[0]='.$arr_temp[0]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[2]='.$arr_temp[2]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[4]='.$arr_temp[4]."<br>";}
                    /*เพิ่มเติม*/
                    $AGE_DIFF_NEXT_YEAR = date_vacation($CHECK_DATE_NEXT_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_next_year = explode(" ", $AGE_DIFF_NEXT_YEAR);

                    $AGE_DIFF_BEFORE_YEAR = date_vacation($CHECK_DATE_BEFORE_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_before_year = explode(" ", $AGE_DIFF_BEFORE_YEAR);

                    $Governor_Age_Sep = 0;
                    $Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
                    $Governor_Age_next_year = ($arr_temp_next_year[0]*10000)+($arr_temp_next_year[2]*100)+(($arr_temp_next_year[4]*1));
                    $Governor_Age_before = ($arr_temp_before_year[0]*10000)+($arr_temp_before_year[2]*100)+(($arr_temp_before_year[4]*1));

                    $dd=$arr_temp[4];
                    if(strlen($dd)==1){$dd='0'.$dd;}

                    $Leave_Cumulative=0; // def วันลาสะสม
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'<h1>เช็คอายุราชการว่า ถึง 6 เดือน หรือ มากกว่า 6 เดือนแล้วหรือไม่ </h1><br>';}
                    if($debug==1){echo 'บรรทัดที่ ->'.__LINE__.'<h1>อายุราชการ'.$arr_temp[0].'ปี '.$arr_temp[2].'เดือน '.$arr_temp[4].'วัน</h1><br>';}

                    //================================================================================================
                    /*=========================== เช็คอายุราชการถึง 6 เดือน หรือ เกิน 6 เดือน หรือยัง ============================*/
                    if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //อายุราชการมากกว่าหรือเท่ากับ 6 เดือนหรือไม่ m dd
                        
						// เช็คว่า มีข้อมูลปีที่แล้วหรือยัง
						$tmpVC_YEAR=$VC_YEAR-1;
						$cmd = " select VC_DAY from PER_VACATION 
							where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
						$count_data = $db_dpis1->send_cmd($cmd);
						if($debug==1){echo __LINE__.':select = '.$cmd.'<br><br>';}
		
						if ($count_data){
							if($arr_temp[0]==0){ //หลักเดือน
								$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
													  WHERE AC_FLAG = 1 AND ($arr_temp[2]/12) < AC_GOV_AGE ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$AC_DAY = $data1[AC_DAY];
								$AC_COLLECT = $data1[AC_COLLECT];
								if($debug==1){echo __LINE__.': AC_COLLECT=>'.$AC_COLLECT.'==><pre>'.$cmd.'<br>';}
							}
							if($arr_temp[0]>=1 && $arr_temp[0]<=10){ //between 1-10
								 $AC_DAY = 10;
								 $AC_COLLECT = 20;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
							if($arr_temp[0]>10){ //10up
								 $AC_DAY = 10;
								 $AC_COLLECT = 30;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
						}else{
											
							if(strlen ($arr_temp[2])==1){$XarrM= '0'.$arr_temp[2];}else{$XarrM= $arr_temp[2];}
								$xYM = (int) $arr_temp[0].'.'.$XarrM;
								if($xYM < 0.06){ //หลักเดือน
									$AC_DAY = 0;
									$AC_COLLECT = 0;
									//echo '<br>น้อยกว่า 6 เดือน AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
								if($xYM >= 0.06){ // มากกว่า 6 เดือน น้อยกว่า 1 ปี
									$AC_DAY = 10;
									$AC_COLLECT = 10;
									//echo '<br>6 เดือนน้อยกว่า 1 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
		
								if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 ปี -10 ปี
									 $AC_DAY = 10;
									 $AC_COLLECT = 20;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br>1 ปีน้อยกว่า 10 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
								if($arr_temp[0]>=10){ //10up
									 $AC_DAY = 10;
									 $AC_COLLECT = 30;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br> 10 ปีขึ้นไป AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
							
							
						}	
							
                        $Leave_Cumulative=10; //A
                        /*กำหนดค่าเริ่มต้น*/
                        if($Governor_Age<600){ //น้อยกว่า 6 เดือน
                           $Leave_Cumulative=0;					
                        }
                        $Before_vc_day=0;//N

                        if($debug==1){echo __LINE__.':Governor_Age '.$Governor_Age.'<br>';}
                        if($Governor_Age>600){ //Y
                           //มี Record เก่าตั้งต้น?ปีที่แล้ว
                            $cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$tmp_vc_year'and PER_ID=$SESS_PER_ID ";
                            if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                            $cnt=$db_dpis1->send_cmd($cmd);
                            //$Before_vc_day=10;//N //Comment Release 5.2.1.19 20180403 
                            if($cnt){//Y
                                $data1 = $db_dpis1->get_array();
                                $Before_vc_day = $data1[VC_DAY]; 
                            }else{
                                // เก่าแก้โดย Kiitphat if($Governor_Age>=1600){ //between 1 to 10 =20 day
                                if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
                                    $Before_vc_day = 20; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }elseif($Governor_Age>=100000){ //10up= 30 day
                                    $Before_vc_day = 30; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }
                            }
                        }
                        //จำนวนลาพักผ่อนของปีงบก่อนหน้า
                        $sum_absday= get_sum_absday($db_dpis1,$SESS_PER_ID,$A_START_DATE,$A_END_DATE);
                        if($debug==1){echo __LINE__.':sum_absday==>'.$sum_absday.'<br>';}
                        //สรุปวันลาและวันลาพักผ่อนสะสมของปีงบก่อนหน้า
                        $cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
                                 where PER_ID=$SESS_PER_ID and START_DATE >= '$A_START_DATE' and END_DATE <= '$A_END_DATE' ";
                        $db_dpis1->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                        $data1 = $db_dpis1->get_array();
                        $data1 = array_change_key_case($data1, CASE_LOWER);
						
                        if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$Before_abs_day_vacation = 0; 
						}else{
							$Before_abs_day_vacation = $data1[abs_day]+0; 
						}
                        if($debug==1){echo __LINE__.':Before_abs_day_vacation==>'.$Before_abs_day_vacation.'<br>';}

                        $Before_vc_day=$Before_vc_day-($sum_absday+$Before_abs_day_vacation);

                        if($debug==1){echo __LINE__.':<font color=red>'.$Before_vc_day.'='.$Before_vc_day.'-('.$sum_absday.'+'.$Before_abs_day_vacation.')</font><br>';}

                        if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                        if($debug==1){echo __LINE__.':'.$CHECK_DATE_NEXT_YEAR.' อายุราชการปีที่คลิกประมวลผล ==>'.$Governor_Age.'<br>';}

                        if($Before_vc_day>0){
                            /**/
                            if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                            $Leave_Cumulative=$Leave_Cumulative+$Before_vc_day;


                            /**/

                        }
                         if($debug==1){echo __LINE__.':Leave_Cumulative==>'.$Leave_Cumulative.'<br>';}
                    }else{ // N
                        $Leave_Cumulative=0;
                    }
                    if($Governor_Age>=100000){ // 10up
                        $AC_COLLECT = 30;
						if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$AC_COLLECT = 10;
						 }
                    }
                    if($debug==1){echo __LINE__.':'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
                    if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //อายุราชการมากกว่าหรือเท่ากับ 10 ปีหรือไม่ yy mm dd
                        $Leave_Cumulative=$AC_COLLECT;// 30 สะสมสูงสุดของแต่ละเงื่อนไข
                    }
                    if($debug==2){
                        echo '<br>====================<br><h1>อายุราชการ: '.$Governor_Age.' ,วันลาสะสมที่ควรได้:'.$Leave_Cumulative.' สะสมสูงสุด ['.$AC_COLLECT.']</h1><br>====================<br>';
                    }
                    /*===================================================================================*/
                    if($debug==1){echo __LINE__.':$A_PER_TYPE = '.$A_PER_TYPE.'<br><br>';}
                    if($A_PER_TYPE==3){
                        if($employees_year_up==TRUE){
                            if($Leave_Cumulative>15){
                                $AC_DAY = 15;
                            }else{
                                $AC_DAY = $Leave_Cumulative;
                            }

                        }else{
                            $AC_DAY = $AC_DAY;
                        }

                        if($debug==1){echo __LINE__.':AC_DAY = '.$AC_DAY.'<br><br>';}
                        if($debug==1){echo __LINE__.':อายุพนักงานราชการGovernor_Age = '.$Governor_Age.'<br><br>';}

                    }else{
                        $AC_DAY=$Leave_Cumulative;
                    }
                    if($Governor_Age<600){ //น้อยกว่า 6 เดือน

                       $AC_DAY=0;

                    }
                    $cmd = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID AND VC_DAY>0   ";
                    $chk_count_data = $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':select = '.$cmd.'<br><br>';}
                    if (!$chk_count_data) {
						$cmd = " delete from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
						$db_dpis->send_cmd($cmd);
										
                        $cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
                                                            values ('$VC_YEAR', $SESS_PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
                        if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':insert = '.$cmd.'<br><br>';}
                        //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($VC_YEAR)." : ".$SESS_PER_ID."]");
						
						
                    }
                }
				
				// kittiphat comment 09/10/2561
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 1 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                        values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 1, '$VC_START_DATE_1', '$VC_END_DATE_1', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 2 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                    values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 2, '$VC_START_DATE_2', '$VC_END_DATE_2', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
				
            }
			get_ABSENT_SUM($PER_ID,$ABS_STARTDATE);
        }
        /*=================================================================================================*/
        /*============================== END การทำงาน คำนวณวันลาพักผ่อนสะสม  =================================*/
           
	} // end if
        
	if( $command == "ADD" && trim(!$ABS_ID)){
           
            /*Release 5.1.0.7 Begin*/
            $DisableTimeAtt='OPEN';
            if($IS_OPEN_TIMEATT_ES=="OPEN"){
                //หาหน่วยงานตามมอบหมาย...
                   $cmdOrgAss = " SELECT ORG.ORG_ID,ORG.ORG_ID_REF FROM PER_PERSONAL PNL
                              LEFT JOIN PER_ORG_ASS ORG ON(ORG.ORG_ID=PNL.ORG_ID)
                                WHERE PNL.PER_ID=$PER_ID";
                    $db_dpis2->send_cmd($cmdOrgAss);
                    $dataOrgAss = $db_dpis2->get_array();   
                    $ORG_ID_ASS = $dataOrgAss[ORG_ID];
                    
                $ArrSTARTDATE = explode("/", trim($ABS_STARTDATE));
                $ArrENDDATE = explode("/", trim($ABS_ENDDATE));

                $ValSTARTDATE = $ArrSTARTDATE[2].$ArrSTARTDATE[1];
                $ValENDDATE = $ArrENDDATE[2].$ArrENDDATE[1];
                $cmdClose = " SELECT CLOSE_YEAR,CLOSE_MONTH 
                                FROM PER_WORK_TIME_CONTROL 
                                WHERE CLOSE_DATE IS NOT NULL AND DEPARTMENT_ID = ".$ORG_ID_ASS." 
                                    AND (CLOSE_YEAR||CASE WHEN length(CLOSE_MONTH)=1 THEN '0'||CLOSE_MONTH ELSE ''||CLOSE_MONTH END)
                                    BETWEEN $ValSTARTDATE AND $ValENDDATE ";
                $db_dpis2->send_cmd($cmdClose);
                $dataATT = $db_dpis2->get_array();
                if($dataATT){
                    $DisableTimeAtt='CLOSE';
                }
            }
			
            /*Release 5.1.0.7 End*/
		  // กรณี ยืนยัน/ปิดรอบข้อมูลการลงเวลาแล้วจะไม่ทำการลา kittiphat 14/12/2561
         //if($DisableTimeAtt=="OPEN" || $SESS_USERGROUP==1){
         if($DisableTimeAtt=="OPEN"){
                $DisableTimeAtt='OPEN';
                $cmd = " select max(ABS_ID) as max_id from PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ABS_ID = $data[max_id] + 1;

		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

                $userID = $SESS_USERID;
                if($SESS_PER_AUDIT_FLAG == 1){
                    $userID = $SESS_PER_ID;
                }
				
                if(empty($POS_STATUS)){$POS_STATUS='0';}
				
                 /* if($POS_STATUS!='0'){
                    if($POS_STATUS=="1"){$POS_APPROVE=$POS_APPROVE1;}
                    if($POS_STATUS=="2"){$POS_APPROVE=$POS_APPROVE2;}
                    if($POS_STATUS=="3"){$POS_APPROVE=$POS_APPROVE3;}
                    $POS_APPROVE_Save="'".htmlspecialchars($POS_APPROVE) ."'"; 
                }else{
					$POS_APPROVE_Save="NULL";
                }*/
				
				// kittiphat 07062561
				if(!empty($POS_APPROVE0)){
					$POS_APPROVE_Save="'".htmlspecialchars($POS_APPROVE0) ."'"; 
				}else{
					$POS_APPROVE_Save="NULL";
				}
				
		$cmd = " insert into PER_ABSENT (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
		ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, APPROVE_PER_ID, AUDIT_PER_ID, 
		REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG, REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG, 
                UPDATE_USER, UPDATE_DATE,SENDMAIL_FLAG,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE,
                POS_STATUS,POS_APPROVE) 
		VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
		'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_LETTER', '$ABS_REASON', '$ABS_ADDRESS', $APPROVE_PER_ID, 
		$AUDIT_PER_ID, $REVIEW1_PER_ID, $REVIEW2_PER_ID, $ABS_AUDIT_FLAG, $ABS_REVIEW1_FLAG, $ABS_REVIEW2_FLAG, $ABS_APPROVE_FLAG, 
                $ABS_CANCEL_FLAG, $userID,'$UPDATE_DATE',0,'$CREATE_DATE', '$AUDIT_DATE','$REVIEW1_DATE','$REVIEW2_DATE','$APPROVE_DATE',
                '$POS_STATUS',$POS_APPROVE_Save) ";
		
                $isChkDupAbsent=ChkDupAbsent(); /*Release 5.1.0.8*/
                if ($isChkDupAbsent==0){
                   // echo '<pre>'.$cmd;
                    $db_dpis->send_cmd($cmd);
                }
		// ส่งอีเมล์อัตโนมัติถึง ผู้อนุญาต
		if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID && $isChkDupAbsent==0){ /*Release 5.1.0.8*/
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
			$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);
			
			$result=0;
			$result = absent_auto_sendmail($PER_EMAIL,$PER_NAME,$PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,0);
			
			// อัพเดทการส่งอีเมล์
			$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
			$db_dpis->send_cmd($cmd);
			
		} //end if
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการลา [".trim($ABS_ID)." : ".$PER_ID." : ".$AB_CODE."]");
		
		$RPT_AB_CODE = $AB_CODE;
		$RPT_ABS_ID = $ABS_ID;
                
                
                /*หาวันที่ยังไม่ได้อนุญาต*/
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                 
               $sql = "SELECT max(ABS_ENDDATE) AS ABS_ENDDATE FROM(
                        select a.ABS_ENDDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            /*and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null)*/  
                            /* $condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_ENDDATE]){
                            $ArrABS_ENDDATE = explode('-',trim($dataEnd[ABS_ENDDATE]));
                            $search_abs_enddate= $ArrABS_ENDDATE[2]."/". $ArrABS_ENDDATE[1] ."/". ($ArrABS_ENDDATE[0] + 543);
                        }
                    }   
                }
                
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                $sql = "SELECT min(ABS_STARTDATE) AS ABS_STARTDATE FROM(
                        select a.ABS_STARTDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null) and a.CANCEL_FLAG =0 
                            /* $condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_STARTDATE]){
                            $ArrABS_STARTDATE = explode('-',trim($dataEnd[ABS_STARTDATE]));
                            $search_abs_startdate= $ArrABS_STARTDATE[2]."/". $ArrABS_STARTDATE[1] ."/". ($ArrABS_STARTDATE[0] + 543);
                        }
                    }   
                }
                
                /***********************/
                
               // die($userID.'='.$cmd);
                /*Release 5.1.0.8 Begin*/

                
                /*Release 5.1.0.8 End */
                // echo "count_duplicate_date=>".$count_duplicate_date;
//                if($count_duplicate_date==0){ /*Release 5.1.0.8 Begin*/
//                   
//                    $db_dpis->send_cmd($cmd);
//                    // ส่งอีเมล์อัตโนมัติถึง ผู้อนุญาต
//                    if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
//                            $cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
//                                                            from PER_PERSONAL a, PER_PRENAME b 
//                                                            where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
//                            $db_dpis->send_cmd($cmd);
//                            $data = $db_dpis->get_array();
//                            $APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
//                            $PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);
//
//                            $result=0;
//                            $result = absent_auto_sendmail($PER_EMAIL,$PER_NAME,$PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,0);
//
//                            // อัพเดทการส่งอีเมล์
//                            $cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
//                            $db_dpis->send_cmd($cmd);
//
//                    } //end if
//                    insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการลา [".trim($ABS_ID)." : ".$PER_ID." : ".$AB_CODE."]");
//                }
                /*Release 5.1.0.8 End*/
                
/*เดิม*/                
                
            }else{echo "<script>alert('ระบบไม่อนุญาตให้ทำรายการย้อนหลัง\n เนื่องจากได้ยืนยัน/ปิดรอบข้อมูลไปแล้ว\n กรุณาติดต่อ Admin');</script>";}
            
            
            /*เดิม*/
		/*$cmd = " select max(ABS_ID) as max_id from PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ABS_ID = $data[max_id] + 1;

		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

		$cmd = " insert into PER_ABSENT (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
		ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, APPROVE_PER_ID, AUDIT_PER_ID, 
		REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG, REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG, UPDATE_USER, UPDATE_DATE,SENDMAIL_FLAG,CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE,APPROVE_DATE) 
		VALUES ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
		'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_LETTER', '$ABS_REASON', '$ABS_ADDRESS', $APPROVE_PER_ID, 
		$AUDIT_PER_ID, $REVIEW1_PER_ID, $REVIEW2_PER_ID, $ABS_AUDIT_FLAG, $ABS_REVIEW1_FLAG, $ABS_REVIEW2_FLAG, $ABS_APPROVE_FLAG, $ABS_CANCEL_FLAG, $SESS_USERID,'$UPDATE_DATE',0,'$CREATE_DATE', '$AUDIT_DATE','$REVIEW1_DATE','$REVIEW2_DATE','$APPROVE_DATE') ";
		$db_dpis->send_cmd($cmd);
		// ส่งอีเมล์อัตโนมัติถึง ผู้อนุญาต
	
		if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
			$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);
			
			$result=0;
			$result = absent_auto_sendmail($PER_EMAIL,$PER_NAME,$PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,0);
			
			// อัพเดทการส่งอีเมล์
			$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
			$db_dpis->send_cmd($cmd);
			
		} //end if
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการลา [".trim($ABS_ID)." : ".$PER_ID." : ".$AB_CODE."]");
		
		$RPT_AB_CODE = $AB_CODE;
		$RPT_ABS_ID = $ABS_ID;*/
	}
function ChkDupAbsent(){
    global $db_dpis,$ABS_STARTDATE,$ABS_ENDDATE,$PER_ID,$ABS_ID,$HID_ABS_STARTPERIOD,$ABS_ENDPERIOD_HIDDEN;
    //วันเริ่มต้น
    $STARTDATE_ce_era = $ABS_STARTDATE;
    //ถึงวันที่
    $ENDDATE_ce_era = $ABS_ENDDATE;	

    
    $arr_temp = explode("-", $STARTDATE_ce_era);
    $START_DAY = $arr_temp[2];
    $START_MONTH = $arr_temp[1];
    $START_YEAR = $arr_temp[0];
    $tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR);
    $tmp_date = date("Y-m-d", $tmp);
    
    $arr_temp = explode("-", $ENDDATE_ce_era);
    $START_DAY = $arr_temp[2];
    $START_MONTH = $arr_temp[1];
    $START_YEAR = $arr_temp[0];
    $tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR);
    $ENDDATE_ce_era = date("Y-m-d", $tmp);

    

    $count_duplicate_date = 0;
    if ($ABS_ID) {
        $absid_condition = " and (ABS_ID!=$ABS_ID) ";
    }
    if ($HID_ABS_STARTPERIOD == 1) {
        $startperiod_condition1 = " (ABS_STARTPERIOD in (1,3)) ";
        $startperiod_condition2 = " (ABS_ENDPERIOD in (1,3)) ";
    } elseif ($HID_ABS_STARTPERIOD == 2) {
        $startperiod_condition1 = " (ABS_STARTPERIOD in (2, 3)) ";
        $startperiod_condition2 = " (ABS_ENDPERIOD in (2, 3)) ";
    } elseif ($HID_ABS_STARTPERIOD == 3) { 
        $startperiod_condition1 = " (ABS_STARTPERIOD<=3) ";
        $startperiod_condition2 = " (ABS_ENDPERIOD<=3) ";
    }
    if ($ABS_ENDPERIOD_HIDDEN == 1) {
        $endperiod_condition1 = " (ABS_STARTPERIOD in (1, 3)) ";
        $endperiod_condition2 = " (ABS_ENDPERIOD in (1, 3)) ";
    } elseif ($ABS_ENDPERIOD_HIDDEN == 2) {
        $endperiod_condition1 = " (ABS_STARTPERIOD in (2, 3)) ";
        $endperiod_condition2 = " (ABS_ENDPERIOD in (2, 3)) ";
    } elseif ($ABS_ENDPERIOD_HIDDEN == 3) {	
        $endperiod_condition1 = " (ABS_STARTPERIOD<=3) ";
        $endperiod_condition2 = " (ABS_ENDPERIOD<=3) ";
    }
    $search_approve ="";
    
//    echo $tmp_date.'<='.$ENDDATE_ce_era.$ABS_ID.$HID_ABS_STARTPERIOD.$ABS_ENDPERIOD_HIDDEN;
   
    while ($tmp_date <= $ENDDATE_ce_era && !trim($count_duplicate_date)) {
        if ($tmp_date == $STARTDATE_ce_era) {
            // ===== เช็ควันลาเริ่มต้น
            $cmd = "select PER_ID 
                    from PER_ABSENT 
                    where PER_ID=$PER_ID $absid_condition $search_approve 
                        and ( '$tmp_date' = ABS_STARTDATE ) 
                        and $startperiod_condition1 
                        AND CANCEL_FLAG = 0 ";
            //echo "1:$cmd <br><br>";
            $count_duplicate_date = $db_dpis->send_cmd($cmd); //echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
            //$chkxx = $linkfrom;
            if (!trim($count_duplicate_date)) {
            //$chkxx = 1;
                $cmd = "select PER_ID 
                        from PER_ABSENT 
                        where PER_ID=$PER_ID $absid_condition $search_approve 
                            and ( '$tmp_date' = ABS_ENDDATE ) 
                            and $startperiod_condition2  
                            AND CANCEL_FLAG = 0";	
                //echo "2:$cmd <br><br>";
                $count_duplicate_date = $db_dpis->send_cmd($cmd);//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
            }
        } elseif ($tmp_date == $ENDDATE_ce_era) {
        //$chkxx = 2;
            // ===== เช็ควันลาสิ้นสุด
            $cmd = "select PER_ID from PER_ABSENT where PER_ID=$PER_ID $absid_condition $search_approve and ( '$tmp_date' = ABS_STARTDATE ) and $endperiod_condition1  AND CANCEL_FLAG = 0";	
            //echo "3:$cmd <br><br>";
            $count_duplicate_date = $db_dpis->send_cmd($cmd);//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
            if (!trim($count_duplicate_date)) {
            //$chkxx = 3;
                $cmd = "select PER_ID 
                        from PER_ABSENT 
                        where PER_ID=$PER_ID $absid_condition $search_approve 
                            and ( '$tmp_date' = ABS_ENDDATE ) 
                            and $endperiod_condition2  
                            AND CANCEL_FLAG = 0";	
                //echo "4:$cmd <br><br>";
                $count_duplicate_date = $db_dpis->send_cmd($cmd);//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";
            }				
        } else {	
        //$chkxx = 4;
            // ===== เช็ควันลาว่าอยู่ในระหว่างวันลาเริ่มต้นและวันลาสิ้นสุดใน db		
            $cmd = "select PER_ID 
                    from PER_ABSENT 
                    where PER_ID=$PER_ID $absid_condition $search_approve 
                        and ABS_STARTDATE <= '$tmp_date' and ABS_ENDDATE >= '$tmp_date'  
                        AND CANCEL_FLAG = 0 ";
            //echo "5:$cmd <br><br>";
            $count_duplicate_date = $db_dpis->send_cmd($cmd);//echo "$cmd<br />count_duplicate_date=$count_duplicate_date<br />";				
        }	

        //echo ++$num . " วัน || $tmp_date != $ENDDATE_ce_era<br>";				
        $num_day++;
        $tmp = mktime(0, 0, 0, $START_MONTH, $START_DAY+$num_day, $START_YEAR);	
        $tmp_date = date("Y-m-d", $tmp);			
    }	// end while 
    return $count_duplicate_date;
}




	if( $command == "UPDATE" && trim($ABS_ID) ) {
                
                $userID = $SESS_USERID;
                if($SESS_PER_AUDIT_FLAG == 1){
                    $userID = $SESS_PER_ID;
                }
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);

               /**/
                $cmd = " select PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
                    ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, APPROVE_PER_ID,
                    CANCEL_FLAG, APPROVE_FLAG, SENDMAIL_FLAG,
                    OABS_STARTDATE,OABS_STARTPERIOD,OABS_ENDDATE,OABS_ENDPERIOD,
                    OAPPROVE_PER_ID,OAPPROVE_DATE,OAUDIT_PER_ID,OAUDIT_DATE,
                    OREVIEW1_PER_ID,OREVIEW1_DATE,OREVIEW2_PER_ID,OREVIEW2_DATE,
                    ORI_ABS_STARTDATE,ORI_ABS_STARTPERIOD,ORI_ABS_ENDDATE,ORI_ABS_ENDPERIOD,REVIEW1_NOTE,REVIEW2_NOTE,AUDIT_NOTE,APPROVE_NOTE 
                 from PER_ABSENT
					where 	 ABS_ID = $ABS_ID"; 
                $db_dpis->send_cmd($cmd);
                if($debug==1){echo 'Step1:<pre>'.$cmd.'<br><br>';}/**/
                
		$dataChk = $db_dpis->get_array();
		$ABS_PER_ID = trim($dataChk[PER_ID]);
//		$AB_CODE = trim($dataChk[AB_CODE]);
		$dbABS_STARTDATE = trim($dataChk[ABS_STARTDATE]);
		$dbABS_STARTPERIOD = trim($dataChk[ABS_STARTPERIOD]);
		$dbABS_ENDDATE = trim($dataChk[ABS_ENDDATE]);
		$dbABS_ENDPERIOD = trim($dataChk[ABS_ENDPERIOD]);		
//		$ABS_DAY = trim($dataChk[ABS_DAY]);
//		$ABS_REASON = trim($dataChk[ABS_REASON]);
//		$PER_CARDNO = trim($dataChk[PER_CARDNO]);
//		$APPROVE_PER_ID = trim($dataChk[APPROVE_PER_ID]);
		$CHECK_ABS_CANCEL_FLAG = trim($dataChk[CANCEL_FLAG]);
		$dbABS_APPROVE_FLAG = trim($dataChk[APPROVE_FLAG]);
//                
                $OABS_STARTDATE = trim($dataChk[OABS_STARTDATE]);
                $OABS_STARTPERIOD = trim($dataChk[OABS_STARTPERIOD]);
                $OABS_ENDDATE = trim($dataChk[OABS_ENDDATE]);
                $OABS_ENDPERIOD = trim($dataChk[OABS_ENDPERIOD]);
               
                /**/
                $dbOABS_STARTDATE = trim($dataChk[OABS_STARTDATE]);
                $dbOABS_STARTPERIOD = trim($dataChk[OABS_STARTPERIOD]);
                $dbOABS_ENDDATE = trim($dataChk[OABS_ENDDATE]);
                $dbOABS_ENDPERIOD = trim($dataChk[OABS_ENDPERIOD]);
                /**/
                
                $CHK_REVIEW1_NOTE = trim($dataChk[REVIEW1_NOTE]);
                $CHK_REVIEW2_NOTE = trim($dataChk[REVIEW2_NOTE]);
                $CHK_AUDIT_NOTE = trim($dataChk[AUDIT_NOTE]);
				$CHK_APPROVE_NOTE = trim($dataChk[APPROVE_NOTE]);
				
				$SEARCH_REVIEW1_NOTE = trim($dataChk[REVIEW1_NOTE]);
				$SEARCH_REVIEW2_NOTE = trim($dataChk[REVIEW2_NOTE]);
				$SEARCH_AUDIT_NOTE = trim($dataChk[AUDIT_NOTE]);
				$SEARCH_APPROVE_NOTE = trim($dataChk[APPROVE_NOTE]);
				

//                
//                $VAL_ABS_STARTDATE = trim($dataChk[ABS_STARTDATE]);
//                $VAL_ABS_STARTPERIOD = trim($dataChk[ABS_STARTPERIOD]);
//                $VAL_ABS_ENDDATE = trim($dataChk[ABS_ENDDATE]);
//                $VAL_ABS_ENDPERIOD = trim($dataChk[ABS_ENDPERIOD]);
//                
                $ORI_ABS_STARTDATE = trim($dataChk[ORI_ABS_STARTDATE]);
                $ORI_ABS_STARTPERIOD = trim($dataChk[ORI_ABS_STARTPERIOD]);
                $ORI_ABS_ENDDATE = trim($dataChk[ORI_ABS_ENDDATE]);
                $ORI_ABS_ENDPERIOD = trim($dataChk[ORI_ABS_ENDPERIOD]);
                
                if($debug==1){
                    echo 'dbABS_APPROVE_FLAG :'.$dbABS_APPROVE_FLAG.'<br>';
                    echo $ORI_ABS_STARTDATE.'=='.$dbABS_STARTDATE.'<br>';
                    echo $ORI_ABS_STARTPERIOD.'=='.$dbABS_STARTPERIOD.'<br>';
                    echo $ORI_ABS_ENDDATE.'=='.$dbABS_ENDDATE.'<br>';
                    echo $ORI_ABS_ENDPERIOD.'=='.$dbABS_ENDPERIOD.'<br>';
                }
                
                //$debug=1;
                $ABS_CANCEL_FLAG=0;
                if( ($CHECK_ABS_CANCEL_FLAG==9 && $ABS_APPROVE_FLAG==1) ||
                    ($dbABS_APPROVE_FLAG==2 && $ORI_ABS_STARTDATE == $dbABS_STARTDATE && $ORI_ABS_STARTPERIOD == $dbABS_STARTPERIOD 
                        && $ORI_ABS_ENDDATE == $dbABS_ENDDATE && $ORI_ABS_ENDPERIOD == $dbABS_ENDPERIOD ) ){
                    $ABS_CANCEL_FLAG = 1;
					
					if($debug==1){
						echo "x1";
					}
					
                }
                $chkCancleAll = false;
                //echo 'step1==>'.$CHECK_ABS_CANCEL_FLAG.'<br>';
                $ABS_STARTPERIOD = $HID_ABS_STARTPERIOD;
                $ABS_ENDPERIOD = $ABS_ENDPERIOD_HIDDEN;
                if($CHECK_ABS_CANCEL_FLAG==8){
                    /*if(($OABS_STARTDATE == $ABS_STARTDATE && $OABS_STARTPERIOD == $ABS_STARTPERIOD 
                        && $OABS_ENDDATE == $ABS_ENDDATE && $OABS_ENDPERIOD == $ABS_ENDPERIOD) ||
                         ($ORI_ABS_STARTDATE == $ABS_STARTDATE && $ORI_ABS_STARTPERIOD == $ABS_STARTPERIOD 
                        && $ORI_ABS_ENDDATE == $ABS_ENDDATE && $ORI_ABS_ENDPERIOD == $ABS_ENDPERIOD)){
                        $chkCancleAll = true;
                        $ABS_CANCEL_FLAG = 1;
                    }*/
					if($debug==1){
						echo "x2";
					}
                    if($ABS_APPROVE_FLAG==2){
                        $ABS_CANCEL_FLAG=0;
						if($debug==1){
							echo "x3";
						}
                    }
                }
				
				
                /**/
				if(empty($POS_STATUS)){$POS_STATUS='0';}
                
               /* if($POS_STATUS!='0'){
                    if($POS_STATUS=="1"){$POS_APPROVE=$POS_APPROVE1;}
                    if($POS_STATUS=="2"){$POS_APPROVE=$POS_APPROVE2;}
                    if($POS_STATUS=="3"){$POS_APPROVE=$POS_APPROVE3;}
                    $POS_APPROVE_Save="'".htmlspecialchars($POS_APPROVE) ."'"; 
                }else{
					$POS_APPROVE_Save="NULL";
                }*/
				
				// kittiphat 07062561
				if(!empty($POS_APPROVE0)){
					$POS_APPROVE_Save="'".htmlspecialchars($POS_APPROVE0) ."'"; 
				}else{
					$POS_APPROVE_Save="NULL";
				}
            
                if(!$REVIEW1_NOTE){
                    $REVIEW1_NOTE = $CHK_REVIEW1_NOTE;
                }else{
                    $REVIEW1_NOTE = $REVIEW1_NOTE;
                }
                
                if(!$REVIEW2_NOTE){
                    $REVIEW2_NOTE = $CHK_REVIEW2_NOTE;
                }else{
                    $REVIEW2_NOTE = $REVIEW2_NOTE;
                }
                
                if(!$AUDIT_NOTE){
                    $AUDIT_NOTE = $CHK_AUDIT_NOTE;
                }else{
                    $AUDIT_NOTE = $AUDIT_NOTE;
                }
                
                if(!$APPROVE_NOTE){
                    $APPROVE_NOTE = $CHK_APPROVE_NOTE;
                }else{
                    $APPROVE_NOTE = $APPROVE_NOTE;
                }
				
				//กรณีที่ยกเลิกใบลาแล้ว เมื่อเจ้าตัวทำรายการ  CANCEL_FLAG จะต้องได้ตัวเดิม และค่าอื่นต้องว่าง by kittiphat
				if($HIDDEN_ABS_APPROVE_FLAG=="" && empty($ABS_APPROVE_FLAG)){
					$ABS_CANCEL_FLAG = $CHECK_ABS_CANCEL_FLAG;
					$ABS_APPROVE_FLAG="NULL";
					$ABS_AUDIT_FLAG="NULL";
					$ABS_REVIEW1_FLAG="NULL";
					$ABS_REVIEW2_FLAG="NULL";
					if($debug==1){
						echo "x4";
					}

				}
				
				if($HIDDEN_ABS_APPROVE_FLAG=="2"){
					 if($ORI_ABS_STARTDATE == $dbABS_STARTDATE && $ORI_ABS_STARTPERIOD == $dbABS_STARTPERIOD 
                        && $ORI_ABS_ENDDATE == $dbABS_ENDDATE && $ORI_ABS_ENDPERIOD == $dbABS_ENDPERIOD){
							$ABS_CANCEL_FLAG =1;
							if($debug==1){
								echo "x5";
							}

						}else{
							$ABS_CANCEL_FLAG = $CHECK_ABS_CANCEL_FLAG;
							if($debug==1){
								echo "x6";
							}

					    }
					
				}
				
				if(empty($ORI_ABS_STARTDATE)){
					if($CHECK_ABS_CANCEL_FLAG==1){
						$ABS_APPROVE_FLAG=0;
						$ABS_CANCEL_FLAG=1;
						$APPROVE_DATE='';
						if($debug==1){
							echo "x7";
						}

					}
				}
				if(trim($dataChk[CANCEL_FLAG])==1){$ABS_CANCEL_FLAG=1; if($debug==1){echo "x8";} }
                
		$cmd = "update PER_ABSENT set  
                            AB_CODE='$AB_CODE', 
                            ABS_STARTDATE='$ABS_STARTDATE', 
                            ABS_STARTPERIOD='$HID_ABS_STARTPERIOD', 
                            ABS_ENDDATE='$ABS_ENDDATE', 
                            ABS_ENDPERIOD='$ABS_ENDPERIOD_HIDDEN', 
                            ABS_DAY='$ABS_DAY', 
                            ABS_LETTER='$ABS_LETTER', 
                            ABS_REASON='$ABS_REASON', 
                            ABS_ADDRESS='$ABS_ADDRESS', 
                            APPROVE_PER_ID=$APPROVE_PER_ID,
                            AUDIT_PER_ID=$AUDIT_PER_ID,
                            REVIEW1_PER_ID=$REVIEW1_PER_ID,
                            REVIEW2_PER_ID=$REVIEW2_PER_ID,
                            AUDIT_FLAG=$ABS_AUDIT_FLAG, 
                            REVIEW1_FLAG = $ABS_REVIEW1_FLAG, 
                            REVIEW2_FLAG = $ABS_REVIEW2_FLAG,
                            APPROVE_FLAG=$ABS_APPROVE_FLAG, 
                            CANCEL_FLAG=$ABS_CANCEL_FLAG,
                            AUDIT_DATE='$AUDIT_DATE',
                            REVIEW1_DATE='$REVIEW1_DATE',
                            REVIEW2_DATE='$REVIEW2_DATE',
                            APPROVE_DATE='$APPROVE_DATE',
							UPDATE_USER = $userID, 
                            UPDATE_DATE='$UPDATE_DATE' ,
                            POS_STATUS=$POS_STATUS,
                            POS_APPROVE=$POS_APPROVE_Save,
                            REVIEW1_NOTE='$REVIEW1_NOTE',
                            REVIEW2_NOTE='$REVIEW2_NOTE',
                            AUDIT_NOTE='$AUDIT_NOTE',
                            APPROVE_NOTE='$APPROVE_NOTE'
                        where ABS_ID=$ABS_ID  ";/*UPDATE_USER=$userID, */
		      $db_dpis->send_cmd($cmd);
          //      echo "upd = <pre>".$cmd;
                if($debug==1){echo 'Step2:<pre>'.$cmd.'<br><br>';}/**/
                if($CHECK_ABS_CANCEL_FLAG==8 && $ABS_APPROVE_FLAG==2){ //กรณีไม่อนุญาติ
                    $Reset_ABS_STARTDATE =$ABS_STARTDATE;
                    $Reset_HID_ABS_STARTPERIOD = $HID_ABS_STARTPERIOD;
                    $Reset_ABS_ENDDATE = $ABS_ENDDATE;
                    $Reset_ABS_ENDPERIOD_HIDDEN = $ABS_ENDPERIOD_HIDDEN;
                    $Reset_APPROVE_PER_ID = $APPROVE_PER_ID;
                    $Reset_APPROVE_DATE = $APPROVE_DATE;
                }
                //echo 'step3==>'.$cmd.'<br>';
//$db_dpis->show_error();
//echo "1 : $cmd<br>";
		// ดึงขัอมูลมาเพื่อหาเงื่อนไข
		if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1){
			$cmd = "select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
                                    ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, SENDMAIL_FLAG,
                                    ORI_ABS_STARTDATE,ORI_ABS_STARTPERIOD,ORI_ABS_ENDDATE,ORI_ABS_ENDPERIOD,
                                    OABS_STARTDATE,OABS_STARTPERIOD,OABS_ENDDATE,OABS_ENDPERIOD,ORI_ABS_DAY,OABS_DAY
                                from PER_ABSENT
                                where ABS_ID=$ABS_ID  "; 
                        if($debug==1){echo 'Step3:<pre>'.$cmd.'<br><br>';}/**/
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ABS_PER_ID = trim($data[PER_ID]);
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
			$ABS_DAY = trim($data[ABS_DAY]);
			$ABS_REASON = trim($data[ABS_REASON]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
                        
			$ORI_ABS_STARTDATE = trim($data[ORI_ABS_STARTDATE]); /**/
			$ORI_ABS_STARTPERIOD = trim($data[ORI_ABS_STARTPERIOD]);
			$ORI_ABS_ENDDATE = trim($data[ORI_ABS_ENDDATE]);
			$ORI_ABS_ENDPERIOD = trim($data[ORI_ABS_ENDPERIOD]);
			
			// kittiphat 12/02/2561
			$UPORI_ABS_DAY = trim($data[ORI_ABS_DAY]);
			$UPOABS_DAY = trim($data[OABS_DAY]);
			//////////////////////////////
			
                        
			/*เอามาใช้กรณี ไม่อนุญาติ*/
			$OABS_STARTDATE = trim($data[OABS_STARTDATE]);
			$OABS_STARTPERIOD = trim($data[OABS_STARTPERIOD]);
			$OABS_ENDDATE = trim($data[OABS_ENDDATE]);
			$OABS_ENDPERIOD = trim($data[OABS_ENDPERIOD]);
                        /*++++++++++++++++++*/
                        
			if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
			$ABS_SENDMAIL_FLAG = trim($data[SENDMAIL_FLAG]);
                        
                        if(empty($ORI_ABS_STARTDATE)){
							if($CHECK_ABS_CANCEL_FLAG!=1){
								$cmd = "UPDATE PER_ABSENT 
									  SET ORI_ABS_STARTDATE=ABS_STARTDATE,
										  ORI_ABS_STARTPERIOD=ABS_STARTPERIOD,
										  ORI_ABS_ENDDATE=ABS_ENDDATE,
										  ORI_ABS_ENDPERIOD=ABS_ENDPERIOD,
										  ORI_ABS_DAY=ABS_DAY ,
										  ORI_APPROVE_DATE='$ORI_APPROVE_DATETIME',
						    			  ORI_APPROVE_PER_ID=$SESS_USERID 
									  WHERE ABS_ID =$ABS_ID ";
								  $db_dpis->send_cmd($cmd); 
								  $db_dpis->send_cmd('COMMIT'); 
								 if($debug==1){echo 'Step4:<pre>'.$cmd.'<br><br>';}/**/
							}
                             $ORI_ABS_STARTDATE = trim($ABS_STARTDATE);
                             $ORI_ABS_STARTPERIOD = trim($ABS_STARTPERIOD);
                             $ORI_ABS_ENDDATE = trim($ABS_ENDDATE);
                             $ORI_ABS_ENDPERIOD = trim($ABS_ENDPERIOD);
                          }
                            /**/
                          if($ABS_APPROVE_FLAG==2){ //ไม่อนุญาติประวัติต้องกลับไปเหมือนเดิม
                              
                              if(empty($OABS_STARTDATE) || $OABS_STARTDATE==''){ ////ลบประวัติรายการที่ยังไม่มีการ ปป. มาก่อน
                                  /* $cmd_delhis = "delete from PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                        and (ABS_STARTDATE='$ORI_ABS_STARTDATE' and ABS_STARTPERIOD='$ORI_ABS_STARTPERIOD' 
                                        and ABS_ENDDATE='$ORI_ABS_ENDDATE' and ABS_ENDPERIOD='$ORI_ABS_ENDPERIOD')";
                                  $db_dpis2->send_cmd($cmd_delhis); 
                                  if($debug==1){echo 'Step DEL HIS:<pre>'.$cmd_delhis.'<br><br>';}*//**/
                                  $cmd = "UPDATE PER_ABSENT 
                                  SET APPROVE_FLAG=2 WHERE ABS_ID =$ABS_ID "; 
                                  $db_dpis2->send_cmd($cmd);
                                  if($debug==1){echo 'Step444:<pre>'.$cmd.'<br><br>';}/**/
                              }

                           

                          }
                          /**/
		}
		
		
		if ($ABS_APPROVE_FLAG==1) {		//	อนุญาต insert PER_ABSENTHIS PER_ABSENTSUM		
		    /*Release 5.1.0.6 Begin*/
                    
                    $cmd = "SELECT ABS_ID 
                            FROM PER_ABSENTHIS 
                            WHERE PER_ID=$ABS_PER_ID 
                                AND ABS_STARTDATE='".save_date($OLD_ABS_STARTDATE)."' 
                                AND ABS_STARTPERIOD='$OLD_ABS_STARTPERIOD' 
                                AND ABS_ENDDATE='".save_date($OLD_ABS_ENDDATE)."' 
                                AND ABS_ENDPERIOD='$OLD_ABS_ENDPERIOD' "; /*HID_ABS_ENDPERIOD*/
                   if($debug==1){echo 'Step9:<pre>'.$cmd.'<br><br>';}/**/
                    ////die('++++++');
                    /*้เดิม*/
                    /*$cmd = " select ABS_ID from PER_ABSENTHIS 
                                where PER_ID=$ABS_PER_ID 
                     * and ABS_STARTDATE='$ABS_STARTDATE' 
                     * and ABS_STARTPERIOD='$ABS_STARTPERIOD' 
                     * and ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";*/
                    $count_data = $db_dpis2->send_cmd($cmd);
                    $data = $db_dpis2->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    if(!empty($count_data)){ //UPDATE
                        if($CHECK_ABS_CANCEL_FLAG==9 ||
                    		($dbABS_APPROVE_FLAG==2 && $ORI_ABS_STARTDATE == $dbABS_STARTDATE && $ORI_ABS_STARTPERIOD == $dbABS_STARTPERIOD 
                        && $ORI_ABS_ENDDATE == $dbABS_ENDDATE && $ORI_ABS_ENDPERIOD == $dbABS_ENDPERIOD ) ){ //ยกเลิกทั้งใบ
                            if(empty($dbOABS_STARTDATE)){
                                $dbOABS_STARTDATE = $ORI_ABS_STARTDATE;
                                $dbOABS_STARTPERIOD = $ORI_ABS_STARTPERIOD;
                                $dbOABS_ENDDATE  =$ORI_ABS_ENDDATE;
                                $dbOABS_ENDPERIOD = $ORI_ABS_ENDPERIOD;
                            }
                            $cmd_delhis = "delete from PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                    and (ABS_STARTDATE='$dbOABS_STARTDATE' and ABS_STARTPERIOD='$dbOABS_STARTPERIOD' 
                                         and ABS_ENDDATE='$dbOABS_ENDDATE' and ABS_ENDPERIOD='$dbOABS_ENDPERIOD')";
                            $db_dpis2->send_cmd($cmd_delhis);
                            if($debug==1){echo 'Step13 DEL HIS CANCLE ALL:<pre>'.$cmd_delhis.'<br><br>';}
                        }else{
                            $cmd = "UPDATE PER_ABSENTHIS 
                                    SET AB_CODE = '$AB_CODE' , 
                                            ABS_STARTDATE = '$ABS_STARTDATE', 
                                            ABS_STARTPERIOD = '$ABS_STARTPERIOD', 
                                            ABS_ENDDATE = '$ABS_ENDDATE', 
                                            ABS_ENDPERIOD = '$ABS_ENDPERIOD', 
                                            ABS_DAY = '$ABS_DAY', 
                                            ABS_REMARK = '$ABS_REASON', 
                                            PER_CARDNO = '$PER_CARDNO', 
                                            UPDATE_USER = $userID, 
                                            UPDATE_DATE = '$UPDATE_DATE' 
                                    WHERE ABS_ID =".$data[abs_id]." AND PER_ID=$ABS_PER_ID ";
                            $db_dpis2->send_cmd($cmd);
                            if($debug==1){echo 'Step10:<pre>'.$cmd.'<br><br>';}/**/
                            $cmd="SELECT AS_ID 
                                  FROM PER_ABSENTSUM 
                                  WHERE PER_ID=$ABS_PER_ID 
                                      AND AS_YEAR = '$BDH_YEAR' 
                                      AND AS_CYCLE = $AS_CYCLE ";
                            if($debug==1){echo 'Step11:<pre>'.$cmd.'<br><br>';}/**/
                            $count=$db_dpis->send_cmd($cmd);
                            $data = $db_dpis->get_array();
                            $data = array_change_key_case($data, CASE_LOWER);
                            if(!empty($count)){ //UPDATE
                                $cmd = "UPDATE PER_ABSENTSUM 
                                        SET START_DATE = '$START_DATE',
                                            END_DATE = '$END_DATE', 
                                            PER_CARDNO = '$PER_CARDNO' , 
                                            UPDATE_USER = $userID, 
                                            UPDATE_DATE = '$UPDATE_DATE'
                                        WHERE AS_ID = ".$data[as_id];
                                    $db_dpis->send_cmd($cmd);
                                    if($debug==1){echo 'Step12:<pre>'.$cmd.'<br><br>';}/**/
                            }
                        }
                        
                        
                        //echo "UPDATE";
                    }else{ //INSERT
                        if(empty($dbOABS_STARTDATE)){
                            $dbOABS_STARTDATE = $ORI_ABS_STARTDATE;
                            $dbOABS_STARTPERIOD = $ORI_ABS_STARTPERIOD;
                            $dbOABS_ENDDATE  =$ORI_ABS_ENDDATE;
                            $dbOABS_ENDPERIOD = $ORI_ABS_ENDPERIOD;
                        }
                        $cmd_delhis = "delete from PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                and (ABS_STARTDATE='$dbOABS_STARTDATE' and ABS_STARTPERIOD='$dbOABS_STARTPERIOD' 
                                     and ABS_ENDDATE='$dbOABS_ENDDATE' and ABS_ENDPERIOD='$dbOABS_ENDPERIOD')";
                        /*$cmd_delhis = " delete from 
                            PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                and (
                                        (ABS_STARTDATE='$ORI_ABS_STARTDATE' ) 
                                        or (ABS_ENDDATE='$ORI_ABS_ENDDATE' )
                                        or (ABS_STARTDATE >'$ORI_ABS_STARTDATE' and ABS_STARTDATE<'$ORI_ABS_ENDDATE' )
                                        or (ABS_ENDDATE >'$ORI_ABS_STARTDATE' and ABS_ENDDATE<'$ORI_ABS_ENDDATE' )
                                    ) 
                           ";*/
                        $db_dpis2->send_cmd($cmd_delhis);
                        if($debug==1){echo 'Step13:<pre>'.$cmd_delhis.'<br><br>';}
                        
                        // กรณีถ้าลา อนุญาต เปลี่ยนแปลง ขอยกเลิกทั้งหมด แล้ว อนุญาต จะต้อง script ข้ามไป by kittiphat 14/02/2561
                        if($ABS_APPROVE_FLAG==1 && $ABS_CANCEL_FLAG==1) {

                        }else{
                                // echo "INSERT";
                                $cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
                                $db_dpis->send_cmd($cmd);
                                $data = $db_dpis->get_array();
                                $data = array_change_key_case($data, CASE_LOWER);
                                $ABS_ID_MAX = $data[max_id] + 1; 

                                $cmd = "insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
                                                ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
                                                VALUES ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
                                                '$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $userID, '$UPDATE_DATE')   ";
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){echo 'Step14:<pre>'.$cmd.'<br><br>';}
                        }
                        $AS_CYCLE1 = $AS_CYCLE2 = "";
                        if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
                        elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
                        if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
                        elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
                        if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
                            $AS_CYCLE = 1;
                            $TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
                            if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
                            $START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
                            $END_DATE = $TMP_AS_YEAR . "-03-31";
                            $BDH_YEAR = $TMP_AS_YEAR + 543;

                            $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
                            $count=$db_dpis->send_cmd($cmd);
                            if(!$count) { 
                                $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
                                $db_dpis->send_cmd($cmd);
                                $data = $db_dpis->get_array();
                                $data = array_change_key_case($data, CASE_LOWER);
                                $AS_ID = $data[max_id] + 1;

                                $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                                values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
                                $db_dpis->send_cmd($cmd);
                                if($debug==1){echo 'Step15:<pre>'.$cmd.'<br><br>';}/**/
                            }
                        }
                        if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
                                $AS_CYCLE = 2;
                                $TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
                                $START_DATE = $TMP_AS_YEAR . "-04-01";
                                $END_DATE = $TMP_AS_YEAR . "-09-30"; 
                                $BDH_YEAR = $TMP_AS_YEAR + 543;

                                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
                                $count=$db_dpis->send_cmd($cmd);
                                if(!$count) { 
                                    $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
                                    $db_dpis->send_cmd($cmd);
                                    $data = $db_dpis->get_array();
                                    $data = array_change_key_case($data, CASE_LOWER);
                                    $AS_ID = $data[max_id] + 1;

                                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                                    values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
                                    $db_dpis->send_cmd($cmd);
                                    if($debug==1){echo 'Step16:<pre>'.$cmd.'<br><br>';}/**/
                                }
                        }
                    }
                    /*Release 5.1.0.6 End*/
                    
                    
                    
                    
                    /*เดิม Begin*/
                    /*$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID and ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD' and 
							ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {	
				$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$ABS_ID_MAX = $data[max_id] + 1; 
			
				$cmd = "	insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
								values ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
								'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis2->send_cmd($cmd);

				$AS_CYCLE1 = $AS_CYCLE2 = "";
				if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
				elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
				if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
				elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
				if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
					$AS_CYCLE = 1;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
					}
				}
				if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
					$AS_CYCLE = 2;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
					}
				}
			}*/
			/*เดิม End*/
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > อนุญาตให้เพิ่มประวัติการลาและวันลาสะสม [".$ABS_APPROVE_FLAG." : ".trim($ABS_ID)." : ".$ABS_PER_ID." - ".$PER_NAME." BY ".$APPROVE_PER_ID." - ".$APPROVE_PER_NAME."]");
			
		}
		//echo "ค่าสุดท้าย=".$ABS_APPROVE_FLAG."|<br>";
		
		if ($ABS_APPROVE_FLAG==2 || $ABS_CANCEL_FLAG==1) {		//	ไม่อนุญาต/ผู้ลายกเลิก delete PER_ABSENTHIS
		     // ใช้เช็คกรณี ลา > อนุญาต > เปลี่ยนแปลง > อนุมัติ แล้วเปลี่ยนใจ ไม่อนุมัติ kittiphat 12/02/2561
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";		//and AB_CODE='$AB_CODE'
			$count_abshis_data = $db_dpis2->send_cmd($cmd);
			$data = $db_dpis2->get_array();
			$ABSHIS_ID = $data[ABS_ID];
			
			if ($ABSHIS_ID) { // ต้องคืนค่าเก่าก่อนหน้า มาอัพเดท
			            if($OABS_STARTDATE){ // ถ้า old ไม่ว่าง
							$cmd = "UPDATE PER_ABSENTHIS SET 
										ABS_STARTDATE = '$OABS_STARTDATE', 
										ABS_STARTPERIOD = '$OABS_STARTPERIOD', 
										ABS_ENDDATE = '$OABS_ENDDATE', 
										ABS_ENDPERIOD = '$OABS_ENDPERIOD', 
										ABS_DAY = '$UPOABS_DAY', 
										ABS_REMARK = '$ABS_REASON', 
										UPDATE_USER = $userID, 
										UPDATE_DATE = '$UPDATE_DATE' 
								WHERE ABS_ID =".$ABSHIS_ID." AND PER_ID=$ABS_PER_ID ";
							$db_dpis2->send_cmd($cmd);
							if($debug==1){echo 'Step16.1 OABS:<pre>'.$cmd.'<br><br>';}
						} /*else{ // ถ้า old ว่าง
							$cmd = "UPDATE PER_ABSENTHIS SET 
										ABS_STARTDATE = '$ORI_ABS_STARTDATE', 
										ABS_STARTPERIOD = '$ORI_ABS_STARTPERIOD', 
										ABS_ENDDATE = '$ORI_ABS_ENDDATE', 
										ABS_ENDPERIOD = '$ORI_ABS_ENDPERIOD', 
										ABS_DAY = '$UPORI_ABS_DAY', 
										ABS_REMARK = '$ABS_REASON', 
										UPDATE_USER = $userID, 
										UPDATE_DATE = '$UPDATE_DATE' 
								WHERE ABS_ID =".$ABSHIS_ID." AND PER_ID=$ABS_PER_ID ";
							$db_dpis2->send_cmd($cmd);
							if($debug==1){echo 'Step16.1 ORI:<pre>'.$cmd.'<br><br>';}
							
						}*/
			
			}
			
			//if ($ABSHIS_ID) {	
                                //if($ABS_APPROVE_FLAG==1){
                                    //$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABSHIS_ID ";
                                    //$db_dpis2->send_cmd($cmd);
                                    // if($debug==1){echo 'Step17:<pre>'.$cmd.'<br><br>';}/**/
                                //}
			//}
                        
                        if(empty($dbOABS_STARTDATE)){
                            $dbOABS_STARTDATE = $ORI_ABS_STARTDATE;
                            $dbOABS_STARTPERIOD = $ORI_ABS_STARTPERIOD;
                            $dbOABS_ENDDATE  =$ORI_ABS_ENDDATE;
                            $dbOABS_ENDPERIOD = $ORI_ABS_ENDPERIOD;
                            if($debug==1){echo 'Step17xx :<pre>xxxxxxx<br><br>';}
                            $cmd_delhis = "delete from PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                and (ABS_STARTDATE='$dbOABS_STARTDATE' and ABS_STARTPERIOD='$dbOABS_STARTPERIOD' 
                                     and ABS_ENDDATE='$dbOABS_ENDDATE' and ABS_ENDPERIOD='$dbOABS_ENDPERIOD')";
                            $db_dpis2->send_cmd($cmd_delhis);
                            if($debug==1){echo 'Step17:<pre>'.$cmd_delhis.'<br><br>';}/**/
                        }
                        
                        
                        
                        
                        
                        
                        
			
			// ส่งอีเมล์อัตโนมัติถึง ผู้ลา (เฉพาะผู้อนุญาต)
			if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2){
				if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
					if($ABS_PER_ID){
						$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
										from PER_PERSONAL a, PER_PRENAME b 
										where a.PER_ID=$ABS_PER_ID and a.PN_CODE=b.PN_CODE ";
						$db_dpis->send_cmd($cmd);
                                                //echo '13:<pre>'.$cmd.'<br>';
						$data = $db_dpis->get_array();
						$ABS_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
						$ABS_PER_EMAIL = $data[PER_EMAIL];
					}
			
					$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
									from PER_PERSONAL a, PER_PRENAME b 
									where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
                                        //echo '14:<pre>'.$cmd.'<br>';
					$data = $db_dpis->get_array();
					$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
					$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);

					if($ABS_SENDMAIL_FLAG==0 || $ABS_SENDMAIL_FLAG==1){		// ดึง sendmail_flag จาก DB check ผู้อนุญาตส่งอีเมล์ไปหรือยัง (ถ้า 0 = ผู้ลา/ผู้อนุญาตยังไม่ส่ง/ส่งเมล์ไม่สำเร็จ / 1  = ผู้ลาส่งเมล์สำเร็จแล้ว / 2 =  ผู้อนุญาตส่งเมล์สำเร็จแล้ว)
						$result=0;
						$result = absent_auto_sendmail($PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$ABS_PER_EMAIL,$ABS_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,$ABS_APPROVE_FLAG);
						
						// อัพเดทการส่งอีเมล์
						$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = $ABS_ID";
                                                //echo '15:<pre>'.$cmd.'<br>';
						$db_dpis->send_cmd($cmd);
					} //end if
				} //end if
			} //end if

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ไม่อนุญาต/ยกเลิกการลาให้ลบประวัติการลา [".$ABS_APPROVE_FLAG." : ".trim($ABS_ID)." : ".$ABS_PER_ID." - ".$PER_NAME." BY ".$APPROVE_PER_ID." - ".$APPROVE_PER_NAME."]");
			
		}
                insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");	
                
                /*==============================================================เพิ่ม insert log   =====================================================*/
                /**================================================ http://dpis.ocsc.go.th/Service/node/2748 =======================================**/
                
                if($SESS_PER_ID==$REVIEW1_PER_ID){
                    if($ABS_REVIEW1_FLAG==1){$TXT_REVIEW1 = "[เห็นควรอนุญาต]";}
                    else {$TXT_REVIEW1 = "[ไม่เห็นควรอนุญาต]";}
                    
                    if($ABS_REVIEW1_FLAG != $ABS_REVIEW1_FLAG_HIDDEN && $ABS_REVIEW1_FLAG){
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ความเห็นผู้บังคับบัญชาชั้นต้น ".$TXT_REVIEW1."[".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE." : BY ".$SESS_PER_ID."]");
                    }
                }
                if($SESS_PER_ID==$REVIEW2_PER_ID){
                    if($ABS_REVIEW2_FLAG==1){$TXT_REVIEW2 = "[เห็นควรอนุญาต]";}
                    else {$TXT_REVIEW2 = "[ไม่เห็นควรอนุญาต]";}
                    
                    if($ABS_REVIEW2_FLAG != $ABS_REVIEW2_FLAG_HIDDEN && $ABS_REVIEW2_FLAG){
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ความเห็นผู้บังคับบัญชาชั้นต้นเหนือขึ้นไป ".$TXT_REVIEW2."[".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE." : BY ".$SESS_PER_ID."]");
                    }    
                }
                if($SESS_PER_ID==$APPROVE_PER_ID){
                    if($ABS_APPROVE_FLAG==1){$TXT_APPROVE = "[อนุญาต]";}
                    else {$TXT_APPROVE = "[ไม่อนุญาต]";}
                    
                    if($ABS_APPROVE_FLAG != $ABS_APPROVE_FLAG_HIDDEN && $ABS_APPROVE_FLAG){
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ความเห็นผู้อนุญาตการลา ".$TXT_APPROVE."[".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE." : BY ".$SESS_PER_ID."]");
                    }    
                }
                if($SESS_PER_ID==$AUDIT_PER_ID){
                    if($ABS_AUDIT_FLAG==1){$TXT_AUDIT = "[ตรวจสอบแล้ว]";}
                    else {$TXT_AUDIT = "[ยังไม่ตรวจสอบ]";}
                    
                    if($ABS_AUDIT_FLAG != $ABS_AUDIT_FLAG_HIDDEN && $ABS_AUDIT_FLAG){
                        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ความเห็นผู้ตรวจสอบการลา ".$TXT_AUDIT."[".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE." : BY ".$SESS_PER_ID."]");
                    }    
                }
                
		/*============================================================== End insert log  =====================================================*/
                
                /*หาวันที่ยังไม่ได้อนุญาต*/
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
               
               $sql = "SELECT max(ABS_ENDDATE) AS ABS_ENDDATE FROM(
                        select a.ABS_ENDDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                //echo '16:<pre>'.$cmd.'<br>';
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_ENDDATE]){
                            $ArrABS_ENDDATE = explode('-',trim($dataEnd[ABS_ENDDATE]));
                            $search_abs_enddate= $ArrABS_ENDDATE[2]."/". $ArrABS_ENDDATE[1] ."/". ($ArrABS_ENDDATE[0] + 543);
                        }
                    }   
                }
                
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                $sql = "SELECT min(ABS_STARTDATE) AS ABS_STARTDATE FROM(
                        select a.ABS_STARTDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                             and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null) and a.CANCEL_FLAG =0 
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                //echo '17:<pre>'.$sql.'<br>';
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_STARTDATE]){
                            $ArrABS_STARTDATE = explode('-',trim($dataEnd[ABS_STARTDATE]));
                            $search_abs_startdate= $ArrABS_STARTDATE[2]."/". $ArrABS_STARTDATE[1] ."/". ($ArrABS_STARTDATE[0] + 543);
                        }
                    }   
                }
                
                /***********************/
                $POS_STATUS=0;
                $POS_APPROVE='';
				$POS_APPROVE_Save="";
	}
     
	if($command == "DELETE" && trim($ABS_ID) ){
		// หาข้อมูลเพื่อนำไปใส่เงื่อนไขในการลบ PER_ABSENTHIS
		$cmd = " 	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, APPROVE_FLAG
				from			PER_ABSENT
				where 	ABS_ID=$ABS_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABS_PER_ID = trim($data[PER_ID]);
		$PER_CARDNO = trim($data[PER_CARDNO]);	
		$AB_CODE = trim($data[AB_CODE]);
		$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);
		$ABS_APPROVE_FLAG =trim($data[APPROVE_FLAG]);

		$cmd = " delete from PER_ABSENT where ABS_ID=$ABS_ID ";
		$count_delete = $db_dpis->send_cmd($cmd);
	
		$cmd = " 	select 	PER_ID
		from			PER_ABSENT
		where 	ABS_ID=$ABS_ID  "; 
		$count_delete = $db_dpis->send_cmd($cmd);

		if(!$count_delete){
			// delete PER_ABSENTHIS     
			$cmd = " delete from PER_ABSENTHIS where PER_ID=$ABS_PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD' and 
							 ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' ";
			$db_dpis2->send_cmd($cmd);

//			$db_dpis2->show_error();
//			echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
			
			/***
			// delete PER_ABSENTSUM
			if ($ABS_APPROVE_FLAG==1) {
				$cmd = " delete from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$AS_YEAR' and AS_CYCLE = $AS_CYCLE and START_DATE='$START_DATE' and 
								 END_DATE='$END_DATE' and PER_CARDNO='$PER_CARDNO' ";
				$db_dpis->send_cmd($cmd);
			}
			***/
		}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");	
                
                /*หาวันที่ยังไม่ได้อนุญาต*/
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                
               $sql = "SELECT max(ABS_ENDDATE) AS ABS_ENDDATE FROM(
                        select a.ABS_ENDDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            /*and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null)*/  
                            /* $condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_ENDDATE]){
                            $ArrABS_ENDDATE = explode('-',trim($dataEnd[ABS_ENDDATE]));
                            $search_abs_enddate= $ArrABS_ENDDATE[2]."/". $ArrABS_ENDDATE[1] ."/". ($ArrABS_ENDDATE[0] + 543);
                        }
                    }   
                }
                
                $condition='';
                if($SESS_PER_AUDIT_FLAG == 1){
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                    }                   
                }else{
                    if(!empty($SESS_PER_ID)){
                        $condition = " AND (a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                    }                    
                }
                $sql = "SELECT min(ABS_STARTDATE) AS ABS_STARTDATE FROM(
                        select a.ABS_STARTDATE
                        from PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c, PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g ,
                        PER_PERSONAL ap
                        where a.AB_CODE=c.AB_CODE and a.PER_ID=b.PER_ID and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and 
                            b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) and a.approve_per_id=ap.PER_ID(+) 
                            and (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null)  and a.CANCEL_FLAG =0 
                           /* $condition */
                            and (b.DEPARTMENT_ID = $search_department_id) 
                        group by ABS_ID, b.PER_TYPE,b.PER_NAME,b.PER_SURNAME, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
                            a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
                            a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO,a.ABS_STARTPERIOD ,ap.per_name
                        )";
                $count = $db_dpis->send_cmd($sql);
                if($count>0){
                    $dataEnd = $db_dpis->get_array(); //2016-06-27
                    if($dataEnd){
                        if($dataEnd[ABS_STARTDATE]){
                            $ArrABS_STARTDATE = explode('-',trim($dataEnd[ABS_STARTDATE]));
                            $search_abs_startdate= $ArrABS_STARTDATE[2]."/". $ArrABS_STARTDATE[1] ."/". ($ArrABS_STARTDATE[0] + 543);
                        }
                    }   
                }
                
                /***********************/
	}
	
	if ($command == "SETFLAG_CANCEL") {		// ของตัวเอง
		$setflagshow =  implode(",",$list_cancel_id);
		$setflagshow_tmp =  implode(",",$list_cancel_flag);		
//		$cmd = " update PER_ABSENT set CANCEL_FLAG = 0 where ABS_ID in (".stripslashes($current_list).") ";
//		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " update PER_ABSENT set CANCEL_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($setflagshow_tmp){			// คงตัวเดิมที่เคยยกเลิกไปแล้ว
			$cmd = " update PER_ABSENT set CANCEL_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow_tmp).") ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
		}

		// delete PER_ABSENTHIS  ตัวที่ CANCEL
		for($i=0; $i < count($list_cancel_id); $i++){
			$ABS_ID_CANCEL = $list_cancel_id[$i];
			//echo "-> $ABS_ID_CANCEL <br>";
			$cmd = " 	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO
					from			PER_ABSENT
					where 	 ABS_ID = $ABS_ID_CANCEL "; 
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ABS_PER_ID = trim($data[PER_ID]);
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
			$ABS_DAY = trim($data[ABS_DAY]);
			$ABS_REASON = trim($data[ABS_REASON]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
			
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD')
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
			$count_abshis_data = $db_dpis2->send_cmd($cmd);
			if($count_abshis_data){
				//echo "=> $count_abshis_data : $cmd <br>";
				$data = $db_dpis2->get_array();
				$ABSHIS_ID = $data[ABS_ID];
				if ($ABSHIS_ID) {	
					// delete PER_ABSENTHIS     
					$cmd = " delete from PER_ABSENTHIS where ABS_ID = $ABSHIS_ID";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "<br><hr>delete PER_ABSENTHIS : $cmd<br>";
				}
			}
		} //end for
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลยกเลิกการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_AUDIT") {
		$setflagshow =  implode(",",$list_audit_id);
		//$cmd = " update PER_ABSENT set AUDIT_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") ";/*เดิม*/
                $cmd = " update PER_ABSENT set AUDIT_FLAG = 1,AUDIT_DATE=to_char( sysdate,'yyyy-mm-dd') ,UPDATE_USER = $SESS_USERID,UPDATE_DATE = '$UPDATE_DATE' 
                    where ABS_ID in (".stripslashes($setflagshow).") AND AUDIT_PER_ID IS NOT NULL ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลตรวจสอบการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_REVIEW1") {
		$setflagshow =  implode(",",$list_review1_id);
		//$cmd = " update PER_ABSENT set REVIEW1_FLAG = 1 ,  where ABS_ID in (".stripslashes($setflagshow).") ";/*เดิม*/
                $cmd = " update PER_ABSENT set REVIEW1_FLAG = 1 ,REVIEW1_DATE=to_char( sysdate,'yyyy-mm-dd')   ,UPDATE_USER = $SESS_USERID,UPDATE_DATE = '$UPDATE_DATE' 
                    where ABS_ID in (".stripslashes($setflagshow).") AND REVIEW1_PER_ID IS NOT NULL";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลความเห็นผู้บังคับบัญชาชั้นต้นการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if ($command == "SETFLAG_REVIEW2") {
		$setflagshow =  implode(",",$list_review2_id);
                //$cmd = " update PER_ABSENT set REVIEW2_FLAG = 1 where ABS_ID in (".stripslashes($setflagshow).") "; /*เดิม*/
		$cmd = " update PER_ABSENT set REVIEW2_FLAG = 1 , REVIEW2_DATE=to_char( sysdate,'yyyy-mm-dd')  ,UPDATE_USER = $SESS_USERID,UPDATE_DATE = '$UPDATE_DATE' 
                    where ABS_ID in (".stripslashes($setflagshow).") AND REVIEW2_PER_ID IS NOT NULL ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลความเห็นผู้บังคับบัญชาชั้นต้นเหนือขึ้นไปการลา [ABS_ID : ".trim(stripslashes($setflagshow))."]");
	}
	if ($command == "CANCELALL") {
		     // กรณีนี้ไม่น่าจะเกิด by kittiphat 09/02/2561
            /*if($hidden_APPROVE_FLAG==1){
                $cmd = " UPDATE PER_ABSENT 
                    SET OABS_STARTDATE=ABS_STARTDATE,
                        OABS_STARTPERIOD=ABS_STARTPERIOD,
                        OABS_ENDDATE=ABS_ENDDATE,
                        OABS_ENDPERIOD=ABS_ENDPERIOD,
                        OAPPROVE_PER_ID=APPROVE_PER_ID,
                        OAPPROVE_DATE=APPROVE_DATE,
                        OAUDIT_PER_ID=AUDIT_PER_ID,
                        OAUDIT_DATE=AUDIT_DATE,
                        OREVIEW1_PER_ID=REVIEW1_PER_ID,
                        OREVIEW1_DATE=REVIEW1_DATE,
                        OREVIEW2_PER_ID=REVIEW2_PER_ID,
                        OREVIEW2_DATE=REVIEW2_DATE,
						CANCEL_DATE='$CANCEL_DATE',
					    CANCEL_BY=$SESS_USERID 
                        WHERE ABS_ID IN (".$ABS_ID.") ";

                $db_dpis->send_cmd($cmd);  
                $db_dpis->send_cmd("COMMIT");  
            }*/
            
			$cmd = " 	select 	APPROVE_FLAG
					       from			PER_ABSENT
					       where 	 ABS_ID = $ABS_ID "; 
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_FLAG = trim($data[APPROVE_FLAG]);
			if(!empty($APPROVE_FLAG)){ // กรณี ยกเลิก ตรวจเช็คดู ได้รับอนุญาตแล้ว
					 $cmd = " UPDATE PER_ABSENT SET 
							OABS_STARTDATE=ABS_STARTDATE,
							OABS_STARTPERIOD=ABS_STARTPERIOD,
							OABS_ENDDATE=ABS_ENDDATE,
							OABS_ENDPERIOD=ABS_ENDPERIOD,
							OABS_DAY=ABS_DAY,
							OAPPROVE_PER_ID=APPROVE_PER_ID,
							OAPPROVE_DATE=APPROVE_DATE,
							OAUDIT_PER_ID=AUDIT_PER_ID,
							OAUDIT_DATE=AUDIT_DATE,
							OREVIEW1_PER_ID=REVIEW1_PER_ID,
							OREVIEW1_DATE=REVIEW1_DATE,
							OREVIEW2_PER_ID=REVIEW2_PER_ID,
							OREVIEW2_DATE=REVIEW2_DATE
							WHERE ABS_ID IN (".$ABS_ID.") ";
						$db_dpis->send_cmd($cmd);
            			$db_dpis->send_cmd("COMMIT"); 
						
				    $cmd = " UPDATE PER_ABSENT 
						SET CANCEL_FLAG = 9 ,
						REVIEW1_FLAG = NULL ,REVIEW1_DATE = NULL ,
						REVIEW2_FLAG = NULL  , REVIEW2_DATE = NULL ,
						AUDIT_FLAG = NULL ,AUDIT_DATE = NULL ,
						APPROVE_FLAG = NULL ,APPROVE_DATE = NULL,
						CANCEL_DATE='$CANCEL_DATETIME',
					    CANCEL_BY=$SESS_USERID, 
						UPDATE_DATE = '$UPDATE_DATE'             
						WHERE ABS_ID IN (".$ABS_ID.") ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->send_cmd("COMMIT"); 
						 
			}else{ // กรณี ยกเลิก ยังไม่ได้อนุญาต
            			$cmd = " UPDATE PER_ABSENT SET 
							CANCEL_FLAG = 1,
							CANCEL_DATE='$CANCEL_DATETIME',
							CANCEL_BY=$SESS_USERID  ,
							UPDATE_DATE = '$UPDATE_DATE'         
							WHERE ABS_ID IN (".$ABS_ID.") ";
						$db_dpis->send_cmd($cmd);
            			$db_dpis->send_cmd("COMMIT");  
			}
            
    }
	
	
	if ($command == "SETFLAG_APPROVE" ) {
//		print("<pre>1 เคยอนุญาตไปแล้ว (เฉพาะ value=1) : ");	print_r($list_approve_allabs);	print("</pre><hr>");
//		print("<br><pre>2.1 มาอนุญาตใหม่ (ยังไม่รวมตัวเก่า): ");	print_r($list_approve_id);	print("</pre>");
		
		//เพิ่มตัวที่ ผู้อนุญาตการลาให้ความเห็นไปแล้ว (แสดงรูปภาพ) ยังคงความเห็นของผู้อนุญาตไว้อยู่เหมือนเดิม
		if(is_array($list_approve_allabs)){	
			foreach($list_approve_allabs as $key=>$value){ //APPROVEการลาไปแล้ว
				$ABSAP_APPROVE_ID = trim($key);
				$ABSAP_APPROVE_FLAG = trim($value);
				if(!$ABSAP_APPROVE_FLAG)		$ABSAP_APPROVE_FLAG=0;
				if($ABSAP_APPROVE_FLAG==1 || $ABSAP_APPROVE_FLAG==2){
					if($ABSAP_APPROVE_FLAG==1){		// เฉพาะที่อนุญาตไปแล้ว
						$list_approve_id[] = $ABSAP_APPROVE_ID;	// เพิ่ม ABS_ID ตัวเก่าที่เคยอนุญาตไปแล้วเพื่อไม่ให้ไปลบประวัติการลาออก
					}
					//$cmd = " update PER_ABSENT set APPROVE_FLAG = ".$ABSAP_APPROVE_FLAG." where ABS_ID =".$ABSAP_APPROVE_ID;/*เดิม*/
                                        $cmd = " update PER_ABSENT set APPROVE_FLAG = ".$ABSAP_APPROVE_FLAG." , APPROVE_DATE=to_char( sysdate,'yyyy-mm-dd') 
                                            where ABS_ID =".$ABSAP_APPROVE_ID. " AND APPROVE_PER_ID IS NOT NULL AND CANCEL_FLAG !=1 ";
					$db_dpis->send_cmd($cmd);
                                        if($debug==1){echo 'chkbox1:<pre>'.$cmd.'<br><br>';}
					//$db_dpis->show_error();
				}
			} //end foreach
		} // end is_array
		
//		print("<br><pre>2.2 มาอนุญาตใหม่ (รวมตัวเก่าที่อนุญาตแล้ว): ");	print_r($list_approve_id);	print("</pre>");		
		
		$setflagshow =  implode(",",$list_approve_id);
		//$cmd = " update PER_ABSENT set APPROVE_FLAG = 1  where ABS_ID in (".stripslashes($setflagshow).") ";/*เดิม*/
                $cmd = " update PER_ABSENT set APPROVE_FLAG = 1 , APPROVE_DATE=to_char( sysdate,'yyyy-mm-dd') ,UPDATE_USER = $SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
                    where ABS_ID in (".stripslashes($setflagshow).") AND APPROVE_PER_ID IS NOT NULL AND CANCEL_FLAG !=1  ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo 'chkbox2:<pre>'.$cmd.'<br><br>';}
		
		//ปิดเนื่องจากการ ไปลบข้อมูลประวัติการลา ที่อนุมัติไปแล้ว by kittiphat 13/02/61
		// delete PER_ABSENTHIS ( UNCHECKED BOX)
		/*$list_current_list = explode(",",stripslashes($current_list));
		for($i=0; $i < count($list_current_list); $i++){
				$ABS_ID_LIST = $list_current_list[$i];		// ดึง ABS_ID ทั้งหมดมา
				if(!in_array($ABS_ID_LIST,$list_approve_id)){ // เปรียบเทียบกับที่ผู้อนุญาต อนุญาตการลาจาก checkbox / hidden ตัวเดิมที่เคยอนุญาตการลาไปแล้ว ถ้าไม่มีในอนุญาตการลา (uncheck) ให้ไปลบประวัติการลาออก
					$cmd = "	select 	PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
                                                    ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, CANCEL_FLAG,
                                                    OABS_STARTDATE,OABS_STARTPERIOD,OABS_ENDDATE,OABS_ENDPERIOD,
                                                    OAPPROVE_PER_ID,OAPPROVE_DATE,OAUDIT_PER_ID,OAUDIT_DATE,
                                                    OREVIEW1_PER_ID,OREVIEW1_DATE,OREVIEW2_PER_ID,OREVIEW2_DATE,APPROVE_FLAG
                                                    from PER_ABSENT
                                                    where 	 ABS_ID = $ABS_ID_LIST"; 
					if($debug==1){echo 'chkbox3:<pre>'.$cmd.'<br><br>';}
                                        $db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ABS_PER_ID = trim($data[PER_ID]);
					$AB_CODE = trim($data[AB_CODE]);
					$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
					$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
					$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
					$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
					$ABS_DAY = trim($data[ABS_DAY]);
					$ABS_REASON = trim($data[ABS_REASON]);
					$PER_CARDNO = trim($data[PER_CARDNO]);
					$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
                                        $dbAPPROVE_FLAG = trim($data[APPROVE_FLAG]);
                                        
                                        
                                           $OABS_STARTDATE = trim($data[OABS_STARTDATE]);
                                           $OABS_STARTPERIOD = trim($data[OABS_STARTPERIOD]);
                                           $OABS_ENDDATE = trim($data[OABS_ENDDATE]);
                                           $OABS_ENDPERIOD = trim($data[OABS_ENDPERIOD]);
                                           
                                        //////////////////////////////////////
                                        //อนุญาติการของยกเลิกใบลา Begin
                                        if($ABS_CANCEL_FLAG=='8'){ //8=ค่าที่ได้มาจาการขอยกเลิกใบลาแบบลดวันลา
                                            $ABS_STARTDATE = $OABS_STARTDATE;
                                            $ABS_STARTPERIOD = $OABS_STARTPERIOD;
                                            $ABS_ENDDATE = $OABS_ENDDATE;
                                            $ABS_ENDPERIOD = $OABS_ENDPERIOD;
                                        }
                                        //////////////////////////////////////  
					if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";			
					
					$cmd = " select ABS_ID from PER_ABSENTHIS 
									where AB_CODE='$AB_CODE' AND PER_ID=$ABS_PER_ID 
									and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
									and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
					$count_abshis_data = $db_dpis2->send_cmd($cmd);
					if($debug==1){echo 'chkbox4:<pre>'.$cmd.'<br><br>';}
					$data = $db_dpis2->get_array();
					$ABSHIS_ID = $data[ABS_ID];
					if (!empty($ABSHIS_ID)) {
						// delete PER_ABSENTHIS     
						$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABSHIS_ID ";
						$db_dpis2->send_cmd($cmd);
						if($debug==1){echo 'chkbox5:<pre>'.$cmd.'<br><br>';}
					}
				} //end in_array
		} // end for($i=0; $i < count($list_current_list); $i++)
		*/
// END delete PER_ABSENTHIS ==============
// insert PER_ABSENTHIS ( CHECKED BOX) เฉพาะตัวที่ APPROVE
// ให้คงความเห็นของผู้อนุญาตไว้ แม้จะยกเลิก/ไม่ยกเลิกการลา
foreach($list_cancel_allabs as $key=>$value){ //CANCELการลา
	$ABSCC_CANCEL_ID = trim($key);			
	$ABSCC_CANCEL_FLAG = trim($value);
	if(!$ABSCC_CANCEL_FLAG)		$ABSCC_CANCEL_FLAG=0;
	$cmd = " update PER_ABSENT set CANCEL_FLAG = ".$ABSCC_CANCEL_FLAG." where ABS_ID =".$ABSCC_CANCEL_ID." AND CANCEL_FLAG !=1";
	$db_dpis->send_cmd($cmd);
	if($debug==1){echo 'chkbox6:<pre>'.$cmd.'<br><br>';}
} //end foreach


for($i=0; $i < count($list_approve_id); $i++){
	$ABS_ID_APPROVE = $list_approve_id[$i];
	$cmd = " select PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, 
                    ABS_ENDPERIOD, ABS_DAY, ABS_REASON, PER_CARDNO, APPROVE_PER_ID,
                    CANCEL_FLAG, APPROVE_FLAG, SENDMAIL_FLAG,
                    OABS_STARTDATE,OABS_STARTPERIOD,OABS_ENDDATE,OABS_ENDPERIOD,
                    OAPPROVE_PER_ID,OAPPROVE_DATE,OAUDIT_PER_ID,OAUDIT_DATE,
                    OREVIEW1_PER_ID,OREVIEW1_DATE,OREVIEW2_PER_ID,OREVIEW2_DATE,
                    ORI_ABS_STARTDATE,ORI_ABS_STARTPERIOD,ORI_ABS_ENDDATE,ORI_ABS_ENDPERIOD 
                 from PER_ABSENT
					where 	 ABS_ID = $ABS_ID_APPROVE"; 
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo 'chkbox7:<pre>'.$cmd.'<br><br>';}
		$data = $db_dpis->get_array();
		$ABS_PER_ID = trim($data[PER_ID]);
		$AB_CODE = trim($data[AB_CODE]);
		$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);
		$ABS_DAY = trim($data[ABS_DAY]);
		$ABS_REASON = trim($data[ABS_REASON]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);
		$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
		$ABS_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
                
                /**/
                $OABS_STARTDATE = trim($data[OABS_STARTDATE]);
                $OABS_STARTPERIOD = trim($data[OABS_STARTPERIOD]);
                $OABS_ENDDATE = trim($data[OABS_ENDDATE]);
                $OABS_ENDPERIOD = trim($data[OABS_ENDPERIOD]);
                /**/
                $VAL_ABS_STARTDATE = trim($data[ABS_STARTDATE]);
                $VAL_ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
                $VAL_ABS_ENDDATE = trim($data[ABS_ENDDATE]);
                $VAL_ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);
                $VAL_ABS_DAY = trim($data[ABS_DAY]);
               
                
                $ORI_ABS_STARTDATE = trim($data[ORI_ABS_STARTDATE]);
                $ORI_ABS_STARTPERIOD = trim($data[ORI_ABS_STARTPERIOD]);
                $ORI_ABS_ENDDATE = trim($data[ORI_ABS_ENDDATE]);
                $ORI_ABS_ENDPERIOD = trim($data[ORI_ABS_ENDPERIOD]);
                
                /**/
                
                     
                
                
		if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
		$ABS_SENDMAIL_FLAG = trim($data[SENDMAIL_FLAG]);	
		
		// ส่งอีเมล์อัตโนมัติถึง ผู้ลา (เฉพาะผู้อนุญาต)
		if($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2){
			if($AUTOMAIL_ABSENT_FLAG=="Y" && $APPROVE_PER_ID){
				if($ABS_PER_ID){
					$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
									from PER_PERSONAL a, PER_PRENAME b 
									where a.PER_ID=$ABS_PER_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ABS_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
					$ABS_PER_EMAIL = $data[PER_EMAIL];
				}
			
				$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME , a.PER_EMAIL
								from PER_PERSONAL a, PER_PRENAME b 
								where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
				$PER_APPROVE_EMAIL = trim($data[PER_EMAIL]);

				if($ABS_SENDMAIL_FLAG==0 || $ABS_SENDMAIL_FLAG==1){		// ดึง sendmail_flag จาก DB check ผู้อนุญาตส่งอีเมล์ไปหรือยัง (ถ้า 0 = ผู้ลา/ผู้อนุญาตยังไม่ส่ง/ส่งเมล์ไม่สำเร็จ / 1  = ผู้ลาส่งเมล์สำเร็จแล้ว / 2 =  ผู้อนุญาตส่งเมล์สำเร็จแล้ว)
					$result=0;
					$result = absent_auto_sendmail($PER_APPROVE_EMAIL,$APPROVE_PER_NAME,$ABS_PER_EMAIL,$ABS_PER_NAME,$AB_NAME,$ABS_STARTDATE,$ABS_ENDDATE,$ABS_REASON,$ABS_APPROVE_FLAG);
					
					// อัพเดทการส่งอีเมล์
					$cmd = " update PER_ABSENT set SENDMAIL_FLAG = $result where ABS_ID = ".$ABS_ID_APPROVE;
					$db_dpis->send_cmd($cmd);
				} //end if
			} //end if
		} //end if
		if(empty($ORI_ABS_STARTDATE)){
			 if(trim($data[CANCEL_FLAG]) !=1){
                  $cmd = "UPDATE PER_ABSENT 
                        SET ORI_ABS_STARTDATE='$VAL_ABS_STARTDATE',
                            ORI_ABS_STARTPERIOD='$VAL_ABS_STARTPERIOD',
                            ORI_ABS_ENDDATE='$VAL_ABS_ENDDATE',
                            ORI_ABS_ENDPERIOD='$VAL_ABS_ENDPERIOD',
                            ORI_ABS_DAY='$VAL_ABS_DAY' ,
							ORI_APPROVE_DATE='$ORI_APPROVE_DATETIME',
						    ORI_APPROVE_PER_ID=$SESS_USERID 
                        WHERE ABS_ID =$ABS_ID_APPROVE ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo 'chkbox8:<pre>'.$cmd.'<br><br>';}
		     }
         }
                
                
                
                //////////////////////////////////////
                /*อนุญาติการของยกเลิกใบลา Begin*/
                if($ABS_CANCEL_FLAG=='8'){ //8=ค่าที่ได้มาจาการขอยกเลิกใบลาแบบลดวันลา
                   /*$ABS_STARTDATE = $ORI_ABS_STARTDATE;
                    $ABS_STARTPERIOD = $ORI_ABS_STARTPERIOD;
                    $ABS_ENDDATE = $ORI_ABS_ENDDATE;
                    $ABS_ENDPERIOD = $ORI_ABS_ENDPERIOD;*/
                    /*$cmd_delhis = " delete from PER_ABSENTHIS where PER_ID=$ABS_PER_ID 
                                and (
                                        (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
                                        or (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD')
                                        or (ABS_STARTDATE >'$ABS_STARTDATE' and ABS_STARTDATE<'$ABS_ENDDATE' )
                                        or (ABS_ENDDATE >'$ABS_STARTDATE' and ABS_ENDDATE<'$ABS_ENDDATE' )
                                    ) 
                           ";*/
                    if(empty($OABS_STARTDATE)){
                        $OABS_STARTDATE = $ORI_ABS_STARTDATE;
                        $OABS_STARTPERIOD = $ORI_ABS_STARTPERIOD;
                        $OABS_ENDDATE  =$ORI_ABS_ENDDATE;
                        $OABS_ENDPERIOD = $ORI_ABS_ENDPERIOD;
                    }
                        $cmd_delhis = "delete from PER_ABSENTHIS where AB_CODE='$AB_CODE' and PER_ID=$ABS_PER_ID 
                                and (ABS_STARTDATE='$OABS_STARTDATE' and ABS_STARTPERIOD='$OABS_STARTPERIOD' 
                                     and ABS_ENDDATE='$OABS_ENDDATE' and ABS_ENDPERIOD='$OABS_ENDPERIOD')";
                    $db_dpis2->send_cmd($cmd_delhis);
                    if($debug==1){echo 'chkbox9:<pre>'.$cmd_delhis.'<br><br>';}
                    //echo $cmd_delhis;
                }
                
                //////////////////////////////////////    
		if($ABS_CANCEL_FLAG!=1){		// ไม่เพิ่มกรณีที่ผู้ลายกเลิกการลานั้นแล้ว
			$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABS_ID_MAX = $data[max_id] + 1; 
			
			$cmd = " select ABS_ID from PER_ABSENTHIS 
							where PER_ID=$ABS_PER_ID 
							and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
							and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) {	
                            $chkCancleAll = false;
                            if($ABS_CANCEL_FLAG=='8'){
                                if( $OABS_STARTDATE == $VAL_ABS_STARTDATE && $OABS_STARTPERIOD == $VAL_ABS_STARTPERIOD 
                                    && $OABS_ENDDATE == $VAL_ABS_ENDDATE && $OABS_ENDPERIOD == $VAL_ABS_ENDPERIOD){
                                    $chkCancleAll = true;
                                }
                                $ABS_STARTDATE = $VAL_ABS_STARTDATE;
                                $ABS_STARTPERIOD = $VAL_ABS_STARTPERIOD;
                                $ABS_ENDDATE = $VAL_ABS_ENDDATE;
                                $ABS_ENDPERIOD = $VAL_ABS_ENDPERIOD;
                            }

				$cmd = " insert into PER_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
                                    ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE) 
                                    values ($ABS_ID_MAX, $ABS_PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
                                    '$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REASON', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
                                
			    if($chkCancleAll == false){
                                $db_dpis2->send_cmd($cmd);
                                if($debug==1){echo 'chkbox10:<pre>'.$cmd.'<br><br>';}
                            }   
				$AS_CYCLE1 = $AS_CYCLE2 = "";
				if (substr($ABS_STARTDATE,5,2) > "09" || substr($ABS_STARTDATE,5,2) < "04") $AS_CYCLE1 = 1;
				elseif (substr($ABS_STARTDATE,5,2) > "03" && substr($ABS_STARTDATE,5,2) < "10")	$AS_CYCLE1 = 2;
				if (substr($ABS_ENDDATE,5,2) > "09" || substr($ABS_ENDDATE,5,2) < "04") $AS_CYCLE2 = 1;
				elseif (substr($ABS_ENDDATE,5,2) > "03" && substr($ABS_ENDDATE,5,2) < "10")	$AS_CYCLE2 = 2;
				if ($AS_CYCLE1==1 || $AS_CYCLE2==1) {
					$AS_CYCLE = 1;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					if (($AS_CYCLE1==1 && substr($ABS_STARTDATE,5,2) > "09") || ($AS_CYCLE2==1 && substr($ABS_ENDDATE,5,2) > "09")) $TMP_AS_YEAR += 1;
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						if($chkCancleAll == false){
                                                    $db_dpis->send_cmd($cmd);
                                                    if($debug==1){echo 'chkbox11:<pre>'.$cmd.'<br><br>';}
                                                }
					}
				}
				if ($AS_CYCLE1==2 || $AS_CYCLE2==2) {
					$AS_CYCLE = 2;
					$TMP_AS_YEAR = substr($ABS_STARTDATE, 0, 4);
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
					$BDH_YEAR = $TMP_AS_YEAR + 543;

					$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$ABS_PER_ID and AS_YEAR = '$BDH_YEAR' and AS_CYCLE = $AS_CYCLE ";
					$count=$db_dpis->send_cmd($cmd);
					if(!$count) { 
						$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						$AS_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
										values ($AS_ID, $ABS_PER_ID, '$BDH_YEAR', $AS_CYCLE, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
						if($chkCancleAll == false){
                                                    $db_dpis->send_cmd($cmd);
                                                    if($debug==1){echo 'chkbox12:<pre>'.$cmd.'<br><br>';}
                                                }
					}
				}
			}
                        //////////////////////////////////////
                        /*อนุญาติการของยกเลิกใบลา Begin*/
                        $cmd = "SELECT CANCEL_FLAG FROM PER_ABSENT  WHERE ABS_ID =$ABS_ID_APPROVE ";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();   
                        $dbCancelFlag = $data[CANCEL_FLAG];
                        if($dbCancelFlag=='9'){ // 9=ค่าที่ได้มาจาการขอยกเลิกใบลาทั้งใบ
                            $cmd = "UPDATE PER_ABSENT SET CANCEL_FLAG=1 WHERE ABS_ID =$ABS_ID_APPROVE ";
                            $db_dpis->send_cmd($cmd);
                            if($debug==1){echo 'chkbox13:<pre>'.$cmd.'<br><br>';}
                            $cmd = " select ABS_ID from PER_ABSENTHIS 
                                    where PER_ID=$ABS_PER_ID 
                                    and (ABS_STARTDATE='$ABS_STARTDATE' and ABS_STARTPERIOD='$ABS_STARTPERIOD') 
                                    and (ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD') ";
                            $count_abshis_data = $db_dpis2->send_cmd($cmd);
                            if($debug==1){echo 'chkbox14:<pre>'.$cmd.'<br><br>';}
                            $data = $db_dpis2->get_array();
                            $ABSHIS_ID = $data[ABS_ID];
                            if ($ABSHIS_ID) {	
                                    $cmd = " delete from PER_ABSENTHIS where ABS_ID=$ABSHIS_ID ";
                                    $db_dpis2->send_cmd($cmd);
                                    if($debug==1){echo 'chkbox15:<pre>'.$cmd.'<br><br>';}
                            }
							
							 //ลา > อนุญาต > เปลี่ยนแปลง > ยกเลิกทั้งหมด > อนุมัติ by kittiphat 12/02/2561
							 //กรณีเปลี่ยนครั้งเดียวแล้วยกเลิกทั้งหมด OLD ก็เจอ
							$cmd = " select ABS_ID from PER_ABSENTHIS 
									where PER_ID=$ABS_PER_ID and AB_CODE=$AB_CODE 
									and (ABS_STARTDATE='$OABS_STARTDATE' and ABS_STARTPERIOD='$OABS_STARTPERIOD') 
									and (ABS_ENDDATE='$OABS_ENDDATE' and ABS_ENDPERIOD='$OABS_ENDPERIOD') ";
							$db_dpis2->send_cmd($cmd);
							$data = $db_dpis2->get_array();
							$OABSHIS_ID = $data[ABS_ID];
							if($OABSHIS_ID){
								$cmd = " delete from PER_ABSENTHIS where ABS_ID=$OABSHIS_ID ";
								$db_dpis2->send_cmd($cmd);
									if($debug==1){echo 'chkbox15.1 OABSHIS:<pre>'.$cmd.'<br><br>';}
							}
							// กรณีเปลี่ยนแล้วเปลี่ยนอีกมากกว่า 1 ครั้ง ต้องดูที่ ORI
							$cmd = " select ABS_ID from PER_ABSENTHIS 
									where PER_ID=$ABS_PER_ID and AB_CODE=$AB_CODE 
									and (ABS_STARTDATE='$ORI_ABS_STARTDATE' and ABS_STARTPERIOD='$ORI_ABS_STARTPERIOD') 
									and (ABS_ENDDATE='$ORI_ABS_ENDDATE' and ABS_ENDPERIOD='$ORI_ABS_ENDPERIOD') ";
							$db_dpis2->send_cmd($cmd);
							$data = $db_dpis2->get_array();
							$ORIABSHIS_ID = $data[ABS_ID];
							if($ORIABSHIS_ID){
								$cmd = " delete from PER_ABSENTHIS where ABS_ID=$ORIABSHIS_ID ";
								$db_dpis2->send_cmd($cmd);
									if($debug==1){echo 'chkbox15.1 ORIABSHIS:<pre>'.$cmd.'<br><br>';}
							}
							
							
							/////////////////////////////////////////////////////////
								
							
                        }
                        if($dbCancelFlag=='8'){ //8=ค่าที่ได้มาจาการขอยกเลิกใบลาแบบลดวันลา
                            if( $chkCancleAll == true){
                                $valCANCEL_FLAG = 1;
                            }else{
                                $valCANCEL_FLAG = 0;
                            }
                            $cmd = "UPDATE PER_ABSENT SET CANCEL_FLAG=$valCANCEL_FLAG WHERE ABS_ID =$ABS_ID_APPROVE ";
                            $db_dpis->send_cmd($cmd);
                            if($debug==1){echo 'chkbox16:<pre>'.$cmd.'<br><br>';}
                        }
                        /*อนุญาติการของยกเลิกใบลา End*/
                        /////////////////////////////////////
		} // end ABS_CANCEL_FLAG==1
                insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลอนุญาตการลา [".trim($ABS_ID)." : ".$ABS_PER_ID." : ".$AB_CODE."]");
} // end for($i=0; $i < count($list_approve_id); $i++)
// END insert PER_ABSENTHIS PER_ABSENTSUM ==============
//die();
		
	}// end SETFLAG_APPROVE
        
        
        
        
        

	if($UPD || $VIEW){
		$cmd = " 	select 	a.PER_ID, a.AB_CODE, AB_NAME, AB_COUNT, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, ABS_REASON, ABS_ADDRESS, 
							APPROVE_PER_ID, AUDIT_PER_ID, REVIEW1_PER_ID, REVIEW2_PER_ID, AUDIT_FLAG , REVIEW1_FLAG, REVIEW2_FLAG, APPROVE_FLAG, CANCEL_FLAG,
							CREATE_DATE, AUDIT_DATE,REVIEW1_DATE,REVIEW2_DATE, APPROVE_DATE,
                                                        OABS_STARTDATE,OABS_STARTPERIOD,OABS_ENDDATE,OABS_ENDPERIOD,
                                                        ORI_ABS_STARTDATE,ORI_ABS_STARTPERIOD,ORI_ABS_ENDDATE,ORI_ABS_ENDPERIOD,
                                                        a.POS_STATUS,a.POS_APPROVE,a.REVIEW1_NOTE,a.REVIEW2_NOTE,a.AUDIT_NOTE,a.APPROVE_NOTE,a.CANCEL_DATE,
                                                        b.AB_TYPE,a.CANCEL_BY,a.UPDATE_USER,a.UPDATE_DATE,a.ORI_APPROVE_PER_ID,a.ORI_APPROVE_DATE,
														a.OAPPROVE_PER_ID,a.ORI_ABS_DAY,a.OABS_DAY
				from	PER_ABSENT a, PER_ABSENTTYPE b
				where 	ABS_ID=$ABS_ID and a.AB_CODE=b.AB_CODE  "; 	
		//echo "<>".$cmd;
                $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = trim($data[PER_ID]);
		$AB_CODE = trim($data[AB_CODE]);
        $AB_TYPE = trim($data[AB_TYPE]);
		$AB_NAME = trim($data[AB_NAME]);
		$AB_COUNT = trim($data[AB_COUNT]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
		$ABS_DAY =round(trim($data[ABS_DAY]),2);
		$NEW_OAPPROVE_PER_ID = trim($data[OAPPROVE_PER_ID]);	
		$NEW_ORI_ABS_DAY = trim($data[ORI_ABS_DAY]);	
		$NEW_OABS_DAY = trim($data[OABS_DAY]);	
		$NEW_ABS_DAY = trim($data[ABS_DAY]);
		
		
		
		// kittiphat 05/11/2561
		// หาค่าวันลาทำการ
		
		$abs_startdate_chkhid =trim($data[ABS_STARTDATE]);
		$startperiod_chkhid =$ABS_STARTPERIOD;
		$abs_enddate_chkhid =trim($data[ABS_ENDDATE]);
		$endperiod_chkhid =$ABS_ENDPERIOD;
		$ab_code_chkhid =$AB_CODE;
		$per_id_chkhid =$PER_ID;
		
		if($abs_startdate_chkhid==$abs_enddate_chkhid){
			$def_val_start_chkhid=2;
			$def_val_end_chkhid=1;
			if($startperiod_chkhid==1 && $endperiod_chkhid==1){
				$def_val_start_chkhid=1;
				$def_val_end_chkhid=2;
			}
		}else{
			$def_val_start_chkhid=2;
			$def_val_end_chkhid=1;
		}
		
		$CANCEL_BY =trim($data[CANCEL_BY]);
		
		
		$cmd_chkhid = "WITH
				AllWorkDay As
				(
				  select /*+ MATERIALIZE */ * from (
					select /*+ MATERIALIZE */ x.*,(case when TO_CHAR(TO_DATE(x.LISTDATE,'YYYY-MM-DD'), 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') IN ('SAT', 'SUN') then 1 else
					  case when exists (select null from PER_HOLIDAY where HOL_DATE = x.LISTDATE) then 1 else 0 end end) HOL
					  , (case when LISTDATE='$abs_startdate_chkhid' then (case when $startperiod_chkhid<>$def_val_start_chkhid then 1 else 0.5 end) 
						else (case when LISTDATE='$abs_enddate_chkhid' then (case when $endperiod_chkhid<>$def_val_end_chkhid then 1 else 0.5 end) else 1 end )end) CNT
					from (
					  select to_char(LISTDATE,'YYYY-MM-DD') LISTDATE from (
						SELECT (TO_DATE('$abs_startdate_chkhid', 'YYYY-MM-DD'))-1+rownum AS LISTDATE FROM all_objects
						 WHERE (TO_DATE('$abs_startdate_chkhid', 'YYYY-MM-DD HH24:MI:SS'))-1+ rownum <= TO_DATE('$abs_enddate_chkhid', 'YYYY-MM-DD')
					  )
					) x
				  )
				  where (select ab_count from PER_ABSENTTYPE where ab_code='$ab_code_chkhid')=1 or HOL=0

				)

				select /*+ MATERIALIZE */ s.as_id,s.as_year,s.as_cycle,
                                    to_char((TO_DATE(min(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_start,
                                    to_char((TO_DATE(max(LISTDATE), 'YYYY-MM-DD')),'DD/MM/YYYY','NLS_CALENDAR=''THAI BUDDHA''') abs_end,
                                    sum(a.CNT) nday ,min(LISTDATE) abs_startorder
                                    from AllWorkDay a
				left join PER_ABSENTSUM s on (s.per_id=$per_id_chkhid and a.LISTDATE between s.start_date and s.end_date)
				group by s.as_id,s.as_year,s.as_cycle
				ORDER by abs_startorder";
	   $db_dpis1->send_cmd($cmd_chkhid);
	  // echo '<pre>'.$cmd_chkhid;
	   $HID_ABS_DAY = 0;
	   while ($data_chkhid = $db_dpis1->get_array_array()) {
		   $HID_ABS_DAY = round($HID_ABS_DAY,2)+ round($data_chkhid[NDAY],2);
	   }
		
		///////////////////////////////////////////////
		
		
		
		
		
		$ABS_LETTER = trim($data[ABS_LETTER]);
		$ABS_REASON = trim($data[ABS_REASON]);
		$ABS_ADDRESS = trim($data[ABS_ADDRESS]);
		$APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);
		$AUDIT_PER_ID = trim($data[AUDIT_PER_ID]);
		$REVIEW1_PER_ID = trim($data[REVIEW1_PER_ID]);
		$REVIEW2_PER_ID = trim($data[REVIEW2_PER_ID]);
		
		$ABS_AUDIT_FLAG = trim($data[AUDIT_FLAG]);
		$ABS_REVIEW1_FLAG = trim($data[REVIEW1_FLAG]);
		$ABS_REVIEW2_FLAG = trim($data[REVIEW2_FLAG]);
		$ABS_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
		$ABS_CANCEL_FLAG = trim($data[CANCEL_FLAG]);
                
                $POS_STATUS = trim($data[POS_STATUS]);
                $POS_APPROVE = trim($data[POS_APPROVE]);
                
                $REVIEW1_NOTE = trim($data[REVIEW1_NOTE]);
                $REVIEW2_NOTE = trim($data[REVIEW2_NOTE]);
                $AUDIT_NOTE = trim($data[AUDIT_NOTE]);
                $APPROVE_NOTE = trim($data[APPROVE_NOTE]);
                
                
                
                /*เพิ่มสำหรับแสดงผลว่าขอยกเลิกใบลาไปเมื่อช่วงวันที่เท่าไร ถึงเท่าไร*/
                
                $ORI_ABS_STARTDATE = trim($data[ORI_ABS_STARTDATE]);
                if(!empty($ORI_ABS_STARTDATE)){
                    $ORI_ABS_STARTDATE = show_date_format($ORI_ABS_STARTDATE,1);
                    $ORI_ABS_STARTPERIOD = trim($data[ORI_ABS_STARTPERIOD]);
                    if($ORI_ABS_STARTPERIOD==1){$ORI_ABS_STARTPERIOD='ครึ่งวันเช้า';}
                    if($ORI_ABS_STARTPERIOD==2){$ORI_ABS_STARTPERIOD='ครึ่งวันบ่าย';}
                    if($ORI_ABS_STARTPERIOD==3){$ORI_ABS_STARTPERIOD='ทั้งวัน';}
                    $ORI_ABS_ENDDATE = show_date_format(trim($data[ORI_ABS_ENDDATE]),1);
                    $ORI_ABS_ENDPERIOD = trim($data[ORI_ABS_ENDPERIOD]);
                    if($ORI_ABS_ENDPERIOD==1){$ORI_ABS_ENDPERIOD='ครึ่งวันเช้า';}
                    if($ORI_ABS_ENDPERIOD==2){$ORI_ABS_ENDPERIOD='ครึ่งวันบ่าย';}
                    if($ORI_ABS_ENDPERIOD==3){$ORI_ABS_ENDPERIOD='ทั้งวัน';}
                }
                $OABS_STARTDATE = trim($data[OABS_STARTDATE]);
                if(!empty($OABS_STARTDATE)){
                    $OABS_STARTDATE = show_date_format($OABS_STARTDATE,1);
                    $OABS_STARTPERIOD = trim($data[OABS_STARTPERIOD]);
                    if($OABS_STARTPERIOD==1){$OABS_STARTPERIOD='ครึ่งวันเช้า';}
                    if($OABS_STARTPERIOD==2){$OABS_STARTPERIOD='ครึ่งวันบ่าย';}
                    if($OABS_STARTPERIOD==3){$OABS_STARTPERIOD='ทั้งวัน';}
                    $OABS_ENDDATE = show_date_format(trim($data[OABS_ENDDATE]),1);
                    $OABS_ENDPERIOD = trim($data[OABS_ENDPERIOD]);
                    if($OABS_ENDPERIOD==1){$OABS_ENDPERIOD='ครึ่งวันเช้า';}
                    if($OABS_ENDPERIOD==2){$OABS_ENDPERIOD='ครึ่งวันบ่าย';}
                    if($OABS_ENDPERIOD==3){$OABS_ENDPERIOD='ทั้งวัน';}
                }
                
                
                /**/

		$ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], 1);
		$ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], 1);

		$ABS_CREATE_DATE = substr(trim($data[CREATE_DATE]),0,10);
		if($ABS_CREATE_DATE){
			if(substr($data[CREATE_DATE],12,strlen($data[CREATE_DATE]))){
				$ABS_CREATE_TIME  = substr($data[CREATE_DATE],12,strlen($data[CREATE_DATE]));
			}
			$temp_date = explode("-", trim($ABS_CREATE_DATE));
			$ABS_CREATE_DATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
			if($ABS_CREATE_TIME)	$ABS_CREATE_DATE = 	$ABS_CREATE_DATE." (".$ABS_CREATE_TIME.")";
		}
		
		
		// kittiphat 14/03/2562 
			//วันเวลาที่ยกเลิก
			$SHOWCANCEL_DATE = "";
			if($data[CANCEL_DATE]){
				$CANCEL_DATE = substr(trim($data[CANCEL_DATE]),0,10);
				$temp_date = explode("-", trim($CANCEL_DATE));
				$SHOWCANCEL_DATE = 	substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543)." ".substr($data[CANCEL_DATE],11,5)."";
			}
			
			$SHOWCANCEL_BY = "";
			if ($CANCEL_BY) {
				
				$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $CANCEL_BY ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SHOWCANCEL_BY = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
				
			}
			
			
			// ผู้ทำรายการล่าสุด
			$SHOWUPDATE_DATE = "";
			if($data[UPDATE_DATE]){
				$UPDATE_DATE = substr(trim($data[UPDATE_DATE]),0,10);
				$temp_date = explode("-", trim($UPDATE_DATE));
				$SHOWUPDATE_DATE = 	substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543)." ".substr($data[UPDATE_DATE],11,5)."";
			}
			
			$SHOWUPDATE_USER = "";
			if (trim($data[UPDATE_USER])) {
				

					$cmd = " select PER_AUDIT_FLAG
							from PER_PERSONAL
							where PER_ID=".trim($data[UPDATE_USER]);
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					if($data1[PER_AUDIT_FLAG]==1){
						$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where USER_LINK_ID=".trim($data[UPDATE_USER])." and id=(select max(id) as id from USER_DETAIL where  USER_LINK_ID=".trim($data[UPDATE_USER]).")";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SHOWUPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
					}else{
						$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =".trim($data[UPDATE_USER]);
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$SHOWUPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
					}
			
			}
			
			//ผู้อนุญาต
			$SHOWORI_APPROVE_DATE = "";
			if($data[ORI_APPROVE_DATE]){
				$ORI_APPROVE_DATE = substr(trim($data[ORI_APPROVE_DATE]),0,10);
				$temp_date = explode("-", trim($ORI_APPROVE_DATE));
				$SHOWORI_APPROVE_DATE = 	substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543)." ".substr($data[ORI_APPROVE_DATE],11,5)."";
			}
			
			$SHOWORI_APPROVE_PER = "";
			if (trim($data[ORI_APPROVE_PER_ID])) {
				
				$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =".trim($data[ORI_APPROVE_PER_ID]);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SHOWORI_APPROVE_PER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
				
			}
			
			

		
		// end 
		
		
		
		
		// สำหรับ HIDDEN //
		$AUDIT_DATE = ($data[AUDIT_DATE]!=""&&$data[AUDIT_DATE]!="NULL"&&$data[AUDIT_DATE]!="null")?substr(trim($data[AUDIT_DATE]),0,10):"";
		$REVIEW1_DATE = ($data[REVIEW1_DATE]!=""&&$data[REVIEW1_DATE]!="NULL"&&$data[REVIEW1_DATE]!="null")?substr(trim($data[REVIEW1_DATE]),0,10):"";
		$REVIEW2_DATE = ($data[REVIEW2_DATE]!=""&&$data[REVIEW2_DATE]!="NULL"&&$data[REVIEW2_DATE]!="null")?substr(trim($data[REVIEW2_DATE]),0,10):"";
		$APPROVE_DATE = ($data[APPROVE_DATE]!=""&&$data[APPROVE_DATE]!="NULL"&&$data[APPROVE_DATE]!="null")?substr(trim($data[APPROVE_DATE]),0,10):"";
		
		$AUDIT_DISPLAY_DATE = $REVIEW1_DISPLAY_DATE = $REVIEW2_DISPLAY_DATE = $APPROVE_DISPLAY_DATE = "";
		if($AUDIT_DATE){
			if(is_numeric(strrpos($AUDIT_DATE, "/"))){	// ตรวจสอบรูปแบบวันที่ ที่ format ผิดจากการ กรอกข้อมูลแต่แรก
				$AUDIT_DATE =	"";		//$TODATE;	//กำหนด hidden ให้มันไปอัพเดทค่าให้ถูกต้อง
			}else{
				$AUDIT_DISPLAY_DATE = show_date_format($AUDIT_DATE, 1);
			}
		}
		if($REVIEW1_DATE){
			if(is_numeric(strrpos($REVIEW1_DATE, "/"))){
				$REVIEW1_DATE =	"";		 //$TODATE;	
			}else{
				$REVIEW1_DISPLAY_DATE = show_date_format($REVIEW1_DATE, 1);
			}
		}
		if($REVIEW2_DATE){
			if(is_numeric(strrpos($REVIEW2_DATE, "/"))){
				$REVIEW2_DATE =	"";		//$TODATE;
			}else{
				$REVIEW2_DISPLAY_DATE = show_date_format($REVIEW2_DATE, 1);
			}
		}
		if($APPROVE_DATE){
			if(is_numeric(strrpos($APPROVE_DATE, "/"))){
				$APPROVE_DATE =	"";		//$TODATE;	
			}else{
				$APPROVE_DISPLAY_DATE = show_date_format($APPROVE_DATE, 1);
			}
		}
		//echo "$AUDIT_DATE / $REVIEW1_DATE / $REVIEW2_DATE / $APPROVE_DATE ";
		
		$temp_date = explode("-", trim($data[ABS_STARTDATE]));
		$ABS_STARTDATE_CHECK = ($temp_date[0])."-".$temp_date[1]."-".substr($temp_date[2],0,2) ;
                
                $temp_date = explode("-", trim($data[ABS_ENDDATE]));
		$ABS_ENDDATE_CHECK = ($temp_date[0])."-".$temp_date[1]."-".substr($temp_date[2],0,2) ;

		if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";	
		
		get_ABSENT_SUM($PER_ID,trim($ABS_STARTDATE));	//ดึงวันลาสะสมของคนที่เลือกมาแก้ไขขึ้นมา

		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO 
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PERSONAL a
											inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
											left join PER_POSITION c on a.POS_ID = c.POS_ID
											left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
							where		a.PER_ID = $PER_ID ";
		} // end if	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();

		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];					

		if($ORG_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
		}

		if (!$DEPARTMENT_NAME) {
			$DEPARTMENT_ID = $data2[ORG_ID_REF];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];

			$MINISTRY_ID = $data2[ORG_ID_REF];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];
		}

		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
						from PER_PERSONAL a, PER_PRENAME b 
						where a.PER_ID=$APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];

		$AUDIT_PER_NAME = $REVIEW1_PER_NAME = $REVIEW2_PER_NAME = "";
		if ($AUDIT_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$AUDIT_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$AUDIT_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}

		if ($REVIEW1_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$REVIEW1_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW1_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}

		if ($REVIEW2_PER_ID) {
			$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_PRENAME b 
							where a.PER_ID=$REVIEW2_PER_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REVIEW2_PER_NAME = $data[PN_NAME].$data[PER_NAME]." ".$data[PER_SURNAME];
		}
		
		
		
		$count_check_flag = 0; 		$CAN_EDIT = "N";
		if($REVIEW1_PER_ID && ($ABS_REVIEW1_FLAG==1 || $ABS_REVIEW1_FLAG==2))				$count_check_flag++;
		if($REVIEW2_PER_ID && ($ABS_REVIEW2_FLAG==1 || $ABS_REVIEW2_FLAG==2))					$count_check_flag++;
		if($AUDIT_PER_ID && ($ABS_AUDIT_FLAG==1 || $ABS_AUDIT_FLAG==2))							$count_check_flag++;
		if($APPROVE_PER_ID && ($ABS_APPROVE_FLAG==1 || $ABS_APPROVE_FLAG==2))				$count_check_flag++;
		if($PER_ID && ($ABS_CANCEL_FLAG==1))				$count_check_flag++;
		if($count_check_flag==0)	$CAN_EDIT = "Y";
		//echo "$count_check_flag / $CAN_EDIT";
	} // end if command
	
	if ($command == "CANCEL" && !$ABS_ID && !$SESS_PER_ID) {
		$PER_ID = "";
		$PER_NAME = "";
		$ORG_ID = "";
		$ORG_NAME = "";
		
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if		
	}

	if( !$UPD && !$DEL && !$VIEW ){
		$ABS_ID = "";
		$AB_CODE = "";
		$AB_NAME = "";
		$AB_COUNT = "";
		$ABS_STARTDATE = "";
		$ABS_ENDDATE = "";
		$ABS_DAY = "";
		$ABS_STARTPERIOD = "";
		$ABS_ENDPERIOD = "";
		$ABS_LETTER = "";
		$ABS_REASON = "";
		$ABS_ADDRESS = "";
		$APPROVE_PER_ID = "";
		$APPROVE_PER_NAME = "";
		$AUDIT_PER_ID = "";
		$AUDIT_PER_NAME = "";
		$REVIEW1_PER_ID = "";
		$REVIEW1_PER_NAME = "";
		$REVIEW2_PER_ID = "";
		$REVIEW2_PER_NAME = "";
		$ABS_REVIEW1_FLAG = "";
		$ABS_REVIEW2_FLAG = "";
		$ABS_AUDIT_FLAG = "";
		$ABS_APPROVE_FLAG = "";
		$ABS_CANCEL_FLAG = "";
		$AUDIT_DATE="";
		$REVIEW1_DATE="";
		$REVIEW2_DATE="";
		$APPROVE_DATE="";
	} // end
?>