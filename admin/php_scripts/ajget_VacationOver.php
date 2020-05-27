<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");
if($IS_OPEN_TIMEATT_ES=='OPEN'){
    $PER_ID = $_GET['PER_ID'];
    
   /* $cmdCycle = "select (case when WORKCYCLE_TYPE=1 then WC_START else ON_TIME end) MORNING, 
                    TIME_LEAVEEARLY AFTERNOON
                from PER_WORK_CYCLE
                where WC_CODE = 
                    NVL((select WC_CODE from PER_WORK_CYCLEHIS where PER_ID = $PER_ID 
                    and sysdate between to_date(START_DATE, 'YYYY-MM-DD hh24:mi:ss') 
                                and to_date(nvl(END_DATE,sysdate+1),'YYYY-MM-DD hh24:mi:ss')),'02')";*/
    $cmdCycle= "select (case when WORKCYCLE_TYPE=1 then WC_START else ON_TIME end) MORNING, 
                TIME_LEAVEEARLY AFTERNOON
        from PER_WORK_CYCLE
        where WC_CODE = 
            NVL((select WC_CODE from PER_WORK_CYCLEHIS where PER_ID = $PER_ID 
            and to_char(sysdate,'YYYY-MM-DD') between START_DATE
                        and nvl(END_DATE,to_char(sysdate+1,'YYYY-MM-DD hh24:mi:ss'))),(select WC_CODE from PER_WORK_CYCLE where WC_START='0830'))";
    $db_dpis->send_cmd($cmdCycle);
    $dataCycle = $db_dpis->get_array_array();
    if($dataCycle){ //ยึดเวลา ตามรอบ
        $defTimeMORNING = $dataCycle[0];//0730
        $defTimeAFTERNOON = $dataCycle[1];//1300
    }else{ //ยึดเวลาที่ 0830
        $defTimeMORNING = '0830';
        $defTimeAFTERNOON = '1300';
    }
    
    $cmdSysdate = "select to_char( sysdate,'YYYYmmdd') date_now,to_char( sysdate,'HH24MI') as time_now from dual ";
    $db_dpis->send_cmd($cmdSysdate);
    $dataSysdate = $db_dpis->get_array_array();
    $date_now = $dataSysdate[0];
    $time_now = $dataSysdate[1];
    
    

    $input_abs_startdateArr = explode('/', $_GET['abs_startdate']); /*15/09/2559*/
    $input_abs_startdate = (intval($input_abs_startdateArr[2])-543).$input_abs_startdateArr[1].$input_abs_startdateArr[0];
    $input_abs_startperiod = $_GET['abs_startperiod'];/*1 ครึ่งวันเช้า ,2 ครึ่งวันบ่าย,3 ทั้งวัน*/
    //echo $input_abs_startdate.'=='.$date_now;
    if($input_abs_startdate==$date_now){ //ถ้าเป็นวันนที่ทำรายการ
        //echo $time_now.'>='.$defTimeMORNING.','.$time_now.'< '.$defTimeAFTERNOON.'::'.$input_abs_startperiod.'=1 || '.$input_abs_startperiod.'=3';
        if($time_now>=$defTimeMORNING && $time_now< $defTimeAFTERNOON){ // เกินเวลา ต้องลาบ่ายได้เท่านั้น
            if($input_abs_startperiod==1 || $input_abs_startperiod==3){
                echo 'OVER';
            }
        }elseif($time_now>=$defTimeAFTERNOON){ // ถ้ามาทำรายการบ่าย ต้องไม่ได้
            if($input_abs_startperiod==1 ||$input_abs_startperiod==2 || $input_abs_startperiod==3){
                echo 'OVER';
            }
        }
    }
}else{ //กรมอื่นๆ ที่ไม่มีระบบลงเวลา
    $cmdSysdate = "select to_char( sysdate,'YYYYmmdd') date_now,to_char( sysdate,'HH24MI') as time_now from dual ";
    $db_dpis->send_cmd($cmdSysdate);
    $dataSysdate = $db_dpis->get_array_array();
    $date_now = $dataSysdate[0];
    $time_now = $dataSysdate[1];


    $input_abs_startdateArr = explode('/', $_GET['abs_startdate']); /*15/09/2559*/
    $input_abs_startdate = (intval($input_abs_startdateArr[2])-543).$input_abs_startdateArr[1].$input_abs_startdateArr[0];
    $input_abs_startperiod = $_GET['abs_startperiod'];/*1 ครึ่งวันเช้า ,2 ครึ่งวันบ่าย,3 ทั้งวัน*/

    if($input_abs_startdate==$date_now){ //ถ้าเป็นวันนที่ทำรายการ
        if($time_now>='0830' && $time_now< '1300'){ // เกินเวลา ต้องลาบ่ายได้เท่านั้น
            if($input_abs_startperiod==1 ||$input_abs_startperiod==3){
                echo 'OVER';
            }
        }elseif($time_now>='1300'){ // ถ้ามาทำรายการบ่าย ต้องไม่ได้
            if($input_abs_startperiod==1 ||$input_abs_startperiod==2 || $input_abs_startperiod==3){
                echo 'OVER';
            }
        }
    }
}



?>
