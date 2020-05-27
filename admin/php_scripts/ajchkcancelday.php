<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
header("content-type: application/x-javascript; charset=TIS-620");
if($IS_OPEN_TIMEATT_ES=='OPEN'){
    $PER_ID = $_GET['PER_ID'];
    $TYPECANCEL = $_GET['TYPECANCEL'];
    $ABS_ID = $_GET['ABS_ID'];
   
    /*1072*/
    if($TYPECANCEL=='ALL'){
        $cmd = "SELECT ABS_STARTDATE,ABS_ENDDATE FROM PER_ABSENT WHERE ABS_ID=".$ABS_ID." AND PER_ID=".$PER_ID;
        $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array();
        $ABS_STARTDATE=$data[ABS_STARTDATE];
        $ABS_ENDDATE=$data[ABS_ENDDATE];
        /*---------------------------*/
        
        $cmd = 'SELECT COUNT(PER_ID) AS CNT_SET_EXCEPTPER
            FROM TA_SET_EXCEPTPER 
            WHERE PER_ID='.$PER_ID.' AND CANCEL_FLAG=1';
        $db_dpis->send_cmd($cmd);
        die($cmd.$ABS_STARTDATE.$ABS_ENDDATE);
        $dataChk = $db_dpis->get_array();
        $CNT_SET_EXCEPTPER = $dataChk[CNT_SET_EXCEPTPER];

        $ChkNextStep = false;
         
        if($CNT_SET_EXCEPTPER>0){
            die(__LINE__.':Y');
        }else{
            $ChkNextStep = true;
        }
        
        if($ChkNextStep==true){
            $cmdBgnDate = "
            WITH tbdate AS(
                SELECT ROWNUM,to_char( (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ROWNUM,'YYYY-MM-DD') AS SELECTDATE,
                CASE WHEN to_char(sysdate,'yyyymmdd') > to_char( to_date(:BEGINDATEAT,'yyyy-mm-dd'),'yyyymmdd')THEN 1 ELSE 0 END CHKDISABLE
                                FROM all_objects
                                WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ rownum <= TO_DATE(:TODATEAT, 'YYYY-MM-DD')
            )
            SELECT * FROM tbdate
            WHERE SELECTDATE NOT IN(
              SELECT to_char((TO_DATE(substr(hol_date,0,10), 'YYYY-MM-DD')),'YYYY-MM-DD') AS xx
              FROM per_holiday
            )";
            $cmdBgnDate=str_ireplace(":BEGINDATEAT","'".trim($ABS_STARTDATE)."'",$cmdBgnDate);
            $cmdBgnDate=str_ireplace(":TODATEAT","'".trim($ABS_ENDDATE)."'",$cmdBgnDate);
            $db_dpis->send_cmd($cmdBgnDate);
            $data = $db_dpis->get_array();
            $ChkNextStep=false;
            while ($data = $db_dpis->get_array()) {
                $SELECTDATE = $data[SELECTDATE];
                $day = explode('/',$data[SELECTDATE]);
                $jd=cal_to_jd(CAL_GREGORIAN,$day[1],$day[0],($day[2]-543)); 
                if(strtoupper(jddayofweek($jd,1)) != "SATURDAY" && strtoupper(jddayofweek($jd,1)) != "SUNDAY"){
                    $cmd = "SELECT COUNT(TIME_STAMP) AS  CNT_TIME_ATTENDANCE
                        FROM PER_TIME_ATTENDANCE 
                        WHERE PER_ID=".$PER_ID." AND TO_CHAR(TIME_STAMP,'YYYY-MM-DD')='".$SELECTDATE."' ";
                    $db_dpis->send_cmd($cmd);
                    $dataChk = $db_dpis->get_array();
                    $CNT_REQUEST_DATE = $dataChk[CNT_REQUEST_DATE];
                    if($CNT_REQUEST_DATE==0){
                        $ChkNextStep=true;
                        break;
                    }
                }
            }
            
            if($ChkNextStep==false){
                //echo 'Y';
                die(__LINE__.':Y');
            }
            /*วันที่ลงเวลา*/
        }
        if($ChkNextStep==true){
            $cmdBgnDate = "
            WITH tbdate AS(
                SELECT ROWNUM,to_char( (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ROWNUM,'YYYY-MM-DD') AS SELECTDATE,
                CASE WHEN to_char(sysdate,'yyyymmdd') > to_char( to_date(:BEGINDATEAT,'yyyy-mm-dd'),'yyyymmdd')THEN 1 ELSE 0 END CHKDISABLE
                                FROM all_objects
                                WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ rownum <= TO_DATE(:TODATEAT, 'YYYY-MM-DD')
            )
            SELECT * FROM tbdate
            WHERE SELECTDATE NOT IN(
              SELECT to_char((TO_DATE(substr(hol_date,0,10), 'YYYY-MM-DD')),'YYYY-MM-DD') AS xx
              FROM per_holiday
            )";
            $cmdBgnDate=str_ireplace(":BEGINDATEAT","'".trim($ABS_STARTDATE)."'",$cmdBgnDate);
            $cmdBgnDate=str_ireplace(":TODATEAT","'".trim($ABS_ENDDATE)."'",$cmdBgnDate);
            $db_dpis->send_cmd($cmdBgnDate);
            $data = $db_dpis->get_array();
            $ChkNextStep=false;
            
            while ($data = $db_dpis->get_array()) {
                $SELECTDATE = $data[SELECTDATE];
                $day = explode('/',$data[SELECTDATE]);
                $jd=cal_to_jd(CAL_GREGORIAN,$day[1],$day[0],($day[2]-543)); 
                if(strtoupper(jddayofweek($jd,1)) != "SATURDAY" && strtoupper(jddayofweek($jd,1)) != "SUNDAY"){
                    $cmd = "SELECT COUNT(REQUEST_DATE)
                            FROM TA_REQUESTTIME 
                            WHERE PER_ID=$PER_ID 
                              AND APPROVE_FLAG = 1 
                              AND CANCEL_FLAG = 0
                              AND REQUEST_DATE='".$SELECTDATE."' ";
                    //die($cmd);
                    $db_dpis->send_cmd($cmd);
                    $dataChk = $db_dpis->get_array();
                    $CNT_REQUEST_DATE = $dataChk[CNT_REQUEST_DATE];
                    if($CNT_REQUEST_DATE==0){
                        $ChkNextStep=true;
                        break;
                    }    
                }
            }
            if($ChkNextStep==false){
                //echo 'Y';
                die(__LINE__.':Y');
            }else{
                die(__LINE__.':N');
            }
        }
    } // end ALL;
    /*
     SELECT count(per_id) AS CNT
FROM TA_SET_EXCEPTPER 
WHERE per_id=14211 AND cancel_flag=1;

SELECT COUNT(time_stamp) AS  CNT_TIME_ATTENDANCE
FROM PER_TIME_ATTENDANCE WHERE per_id=14135 and to_char(time_stamp,'YYYY-MM-DD')='2016-05-06';

SELECT COUNT(REQUEST_DATE)
FROM TA_REQUESTTIME 
WHERE PER_ID=14135 
  AND APPROVE_FLAG = 1 
  AND  CANCEL_FLAG = 0
  and REQUEST_DATE IN('','','');     
     */
}else{
    echo 'Y'; //ยกเลิกได้ตลอด
}
?>
