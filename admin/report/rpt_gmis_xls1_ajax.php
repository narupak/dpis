<?php
    /*ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/
    include("../../php_scripts/class.json_forphp4.inc.php");
    if( !function_exists('json_encode') ) {
        function json_encode($data) {
            $json = new Services_JSON();
            return( $json->encode($data) );
        }
    }
    // Future-friendly json_decode
    if( !function_exists('json_decode') ) {
        function json_decode($data) {
            $json = new Services_JSON();
            return( $json->decode($data) );
        }
    }
    
    //include("../../php_scripts/db_connect_var.php");
    include("../../php_scripts/connect_database.php");
    include("../../php_scripts/connect_oci8_stand_alone.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");	
    include ("../report/rpt_function.php");
    //log file 
    //$logfile = '../../Excel/tmp/XWRITELOG_Err.txt';
    //call function
    if($_POST['Method']){ $Method = $_POST['Method']; }
    if($_POST['search_budget_year']){ $search_budget_year = @iconv('UTF-8','TIS-620',$_POST['search_budget_year']); }
    if($_POST['RPTORD_LIST']){ $RPTORD_LIST = @iconv('UTF-8','TIS-620',$_POST['RPTORD_LIST']); }
    if($_POST['PROVINCE_CODE']){ $PROVINCE_CODE = @iconv('UTF-8','TIS-620',$_POST['PROVINCE_CODE']); }
    if($_POST['PROVINCE_NAME']){ $PROVINCE_NAME = @iconv('UTF-8','TIS-620',$_POST['PROVINCE_NAME']); }
    if($_POST['MINISTRY_ID']){ $MINISTRY_ID = @iconv('UTF-8','TIS-620',$_POST['MINISTRY_ID']); }
    if($_POST['MINISTRY_NAME']){ $MINISTRY_NAME = @iconv('UTF-8','TIS-620',$_POST['MINISTRY_NAME']); }
    if($_POST['DEPARTMENT_ID']){ $DEPARTMENT_ID = $_POST['DEPARTMENT_ID']; }
    if($_POST['DEPARTMENT_NAME']){ $DEPARTMENT_NAME = @iconv('UTF-8','TIS-620',$_POST['DEPARTMENT_NAME']); }
    if($_POST['ENC_H']){ $db_host = base64_decode(trim($_POST[ENC_H]));}
    if($_POST['ENC_N']){ $db_name = base64_decode(trim($_POST[ENC_N]));}
    if($_POST['ENC_U']){ $db_user = base64_decode(trim($_POST[ENC_U]));}
    if($_POST['ENC_P']){ $db_pwd = base64_decode(trim($_POST[ENC_P]));}
    //---------------------------------------------------------------------------- connect ---------------------------------------------------------------------------
    //------------------------------------------------------------------new class connect database oci8---------------------------------------------------------------
    $conn = new connect_oci8_stand_alone($db_host, $db_name, $db_user, $db_pwd, $port);
    //---------------------------------------------------------------------------- end -------------------------------------------------------------------------------
    //--------------------------------------------------------------------------- start -----------------------------------------------------------------------------
    function send_message($id, $message, $progress){
        $d = array('message' => $message , 'progress' => $progress);
        echo json_encode($d) . PHP_EOL;
        //PUSH THE data out by all FORCE POSSIBLE
        ob_flush();
        flush();
    }
    function write_errtofile($logfile,$txt_err){
        if(is_file($logfile)){
            unlink($logfile);
        }
        $fp = fopen($logfile, 'a');
        fwrite($fp, $txt_err);
        fclose($fp);         
    }
    function GET_TOTAL_DATA ($conn){
        global $DEPARTMENT_ID;
        $str_SQL = " select count(a.POS_NO) cnt
                    from    PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_PROVINCE f, PER_ORG_TYPE g
                    where   a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and 
                            b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)	
                            and (a.POS_STATUS=1) and (a.DEPARTMENT_ID = $DEPARTMENT_ID) ";
        $data = "";
        $exQuery1 = OCIParse($conn, $str_SQL);
        OCIExecute($exQuery1, OCI_COMMIT_ON_SUCCESS);
        OCIFetchInto ($exQuery1, $data, OCI_ASSOC + OCI_RETURN_NULLS);
        return $data[CNT];
    }
    function CHECK_CREATE_VALIN_SYSCON($conn){
        global $db,$db_type;
        $strSQL ="    SELECT COUNT(a.CONFIG_NAME) AS CNT  from SYSTEM_CONFIG a 
                                    WHERE  a.CONFIG_NAME = 'GMIS_START_TIME_PROCESS' 
                                       OR  a.CONFIG_NAME = 'GMIS_END_TIME_PROCESS'
                                       OR  a.CONFIG_NAME = 'GMIS_STATUS_PROCESS' ";
        $data = "";
        if($db_type=="mysql"){
            $db->send_cmd($strSQL);
            $data = $db->get_array();
        }else{
            $exQuery_result = OCIParse($conn, $strSQL); 
            OCIExecute($exQuery_result, OCI_COMMIT_ON_SUCCESS);
            OCIFetchInto ($exQuery_result, $data, OCI_ASSOC + OCI_RETURN_NULLS);
        }
        if($data[CNT]!=0){
            return TRUE;
        }
        return FALSE;
    }
    function GET_TOTAL_TIME ($conn){
        global $db,$db_type;
        if(CHECK_CREATE_VALIN_SYSCON($conn)){
            $data = "";
            if($db_type=="mysql"){
                $strSQL = " 
                        SELECT * FROM 
                                (SELECT
                                    a.CONFIG_VALUE AS GMIS_START_TIME_PROCESS
                                    FROM SYSTEM_CONFIG a
                                    WHERE a.CONFIG_NAME = 'GMIS_START_TIME_PROCESS') a,
                                (SELECT
                                    a.CONFIG_VALUE AS GMIS_END_TIME_PROCESS
                                    FROM SYSTEM_CONFIG a
                                    WHERE a.CONFIG_NAME = 'GMIS_END_TIME_PROCESS') b,
                               (SELECT
                                    a.CONFIG_VALUE AS GMIS_STATUS_PROCESS
                                    FROM SYSTEM_CONFIG a
                                    WHERE a.CONFIG_NAME = 'GMIS_STATUS_PROCESS') c,
                               (SELECT
                                    '0' AS GMIS_TOTAL_TIME_DAY) d,
                               (SELECT
                                    CONVERT(EXTRACT(HOUR FROM (TIMEDIFF(DATE_FORMAT(STR_TO_DATE(b.END_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(STR_TO_DATE(a.START_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')))), char) AS GMIS_TOTAL_TIME_HOUR
                                    FROM (SELECT
                                            CONFIG_VALUE AS START_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_START_TIME_PROCESS') a,
                                        (SELECT
                                            CONFIG_VALUE AS END_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_END_TIME_PROCESS') b) e,
                               (SELECT
                                    CONVERT(EXTRACT(MINUTE FROM (TIMEDIFF(DATE_FORMAT(STR_TO_DATE(b.END_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(STR_TO_DATE(a.START_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')))), char) AS GMIS_TOTAL_TIME_MINUTE
                                    FROM (SELECT
                                            CONFIG_VALUE AS START_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_START_TIME_PROCESS') a,
                                        (SELECT
                                            CONFIG_VALUE AS END_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_END_TIME_PROCESS') b) f,
                               (SELECT
                                    CONVERT(EXTRACT(SECOND FROM (TIMEDIFF(DATE_FORMAT(STR_TO_DATE(b.END_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s'), DATE_FORMAT(STR_TO_DATE(a.START_TIME, '%d/%m/%Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s')))), char) AS GMIS_TOTAL_TIME_SECOND
                                    FROM (SELECT
                                            CONFIG_VALUE AS START_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_START_TIME_PROCESS') a,
                                        (SELECT
                                            CONFIG_VALUE AS END_TIME
                                            FROM SYSTEM_CONFIG
                                            WHERE CONFIG_NAME = 'GMIS_END_TIME_PROCESS') b) g

                ";
                $db->send_cmd($strSQL);
                $data = $db->get_array();
            }else{
                $strSQL = " 
                            WITH START_TIME_PROCESS AS (
                                    SELECT CONFIG_VALUE AS START_TIME FROM  SYSTEM_CONFIG   WHERE  CONFIG_NAME = 'GMIS_START_TIME_PROCESS'
                            ), END_TIME_PROCESS AS (
                                    SELECT CONFIG_VALUE AS END_TIME FROM  SYSTEM_CONFIG   WHERE  CONFIG_NAME = 'GMIS_END_TIME_PROCESS'
                            ), TOTAL_TIME AS (
                                    SELECT 
                                        TO_CHAR(EXTRACT( DAY FROM (TO_TIMESTAMP(b.END_TIME,'DD/MM/YYYY HH24:MI:SS')-TO_TIMESTAMP(a.START_TIME,'DD/MM/YYYY HH24:MI:SS')))) as TOTAL_DAY,
                                        TO_CHAR(EXTRACT( HOUR FROM (TO_TIMESTAMP(b.END_TIME,'DD/MM/YYYY HH24:MI:SS')-TO_TIMESTAMP(a.START_TIME,'DD/MM/YYYY HH24:MI:SS')))) as TOTAL_HOUR,
                                        TO_CHAR(EXTRACT( MINUTE FROM (TO_TIMESTAMP(b.END_TIME,'DD/MM/YYYY HH24:MI:SS')-TO_TIMESTAMP(a.START_TIME,'DD/MM/YYYY HH24:MI:SS')))) as TOTAL_MINUTE,
                                        TO_CHAR(EXTRACT( SECOND FROM (TO_TIMESTAMP(b.END_TIME,'DD/MM/YYYY HH24:MI:SS')-TO_TIMESTAMP(a.START_TIME,'DD/MM/YYYY HH24:MI:SS')))) as TOTAL_SECOND
                                    FROM  START_TIME_PROCESS a, END_TIME_PROCESS b 
                            ) , ALL_TABLE AS (
                                  SELECT a.CONFIG_NAME, a.CONFIG_VALUE from SYSTEM_CONFIG a  WHERE  a.CONFIG_NAME = 'GMIS_START_TIME_PROCESS'  OR  a.CONFIG_NAME = 'GMIS_END_TIME_PROCESS' OR  a.CONFIG_NAME = 'GMIS_STATUS_PROCESS'
                                  UNION ALL SELECT 'GMIS_TOTAL_TIME_DAY' AS CONFIG_NAME , TOTAL_DAY AS CONFIG_VALUE FROM TOTAL_TIME 
                                  UNION ALL SELECT 'GMIS_TOTAL_TIME_HOUR' AS CONFIG_NAME , TOTAL_HOUR AS CONFIG_VALUE FROM TOTAL_TIME
                                  UNION ALL SELECT 'GMIS_TOTAL_TIME_MINUTE' AS CONFIG_NAME , TOTAL_MINUTE AS CONFIG_VALUE FROM TOTAL_TIME
                                  UNION ALL SELECT 'GMIS_TOTAL_TIME_SECOND' AS CONFIG_NAME , TOTAL_SECOND AS CONFIG_VALUE FROM TOTAL_TIME
                            ) 
                            SELECT * FROM ( SELECT CONFIG_NAME , CONFIG_VALUE from ALL_TABLE )
                            PIVOT ( MAX(CONFIG_VALUE) for CONFIG_NAME  in ( 
                                                      'GMIS_START_TIME_PROCESS' AS GMIS_START_TIME_PROCESS, 'GMIS_END_TIME_PROCESS' AS GMIS_END_TIME_PROCESS, 
                                                      'GMIS_STATUS_PROCESS' AS GMIS_STATUS_PROCESS, 'GMIS_TOTAL_TIME_DAY' AS GMIS_TOTAL_TIME_DAY,
                                                      'GMIS_TOTAL_TIME_HOUR' AS GMIS_TOTAL_TIME_HOUR, 'GMIS_TOTAL_TIME_MINUTE' AS GMIS_TOTAL_TIME_MINUTE,
                                                      'GMIS_TOTAL_TIME_SECOND' AS GMIS_TOTAL_TIME_SECOND))
                ";
                $exQuery_result = OCIParse($conn, $strSQL);  
                OCIExecute($exQuery_result); 
                OCIFetchInto ($exQuery_result, $data, OCI_ASSOC + OCI_RETURN_NULLS);
            }
            
            if($data){
                array_push($data,array('message' => 'success')); $message = json_encode($data);
            }else {
                $data = array("status" => 'ok');array_push($data,array('message' => 'success')); $message = json_encode($data);
            }
        }else{
            $data = array("status" => 'not_ok');array_push($data,array('message' => 'not_success')); $message = json_encode($data);
        }
        if($db_type=="oci8"){ OCIFreeStatement($exQuery_result);}    
        return $message;
    }
    function GET_START_TIMES ($conn,$time){
        global $db,$db_type;
        $START_TIME = $time;
        $END_TIME = "";
        $strSQL_INUP_START_TIME = " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = '".$START_TIME."' WHERE CONFIG_NAME = 'GMIS_START_TIME_PROCESS' ";
        $strSQL_INUP_END_TIME =  " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = '".$END_TIME."' WHERE CONFIG_NAME = 'GMIS_END_TIME_PROCESS' ";
        $strSQL_INUP_STATUS = " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = 'PROCESSING' WHERE CONFIG_NAME = 'GMIS_STATUS_PROCESS' ";
        if($db_type=="mysql"){
            $db->send_cmd($strSQL_INUP_START_TIME);
            $db->send_cmd($strSQL_INUP_END_TIME);
            $db->send_cmd($strSQL_INUP_STATUS);
        } else {
            $exQuery_INUP_START_TIME = OCIParse($conn, $strSQL_INUP_START_TIME);
            $exQuery_INUP_END_TIME = OCIParse($conn, $strSQL_INUP_END_TIME);
            $exQuery_INUP_STATUS = OCIParse($conn, $strSQL_INUP_STATUS);
            OCIExecute($exQuery_INUP_START_TIME);
            OCIExecute($exQuery_INUP_END_TIME);
            OCIExecute($exQuery_INUP_STATUS);
            ocifreestatement($exQuery_INUP_START_TIME);
            ocifreestatement($exQuery_INUP_END_TIME);
            ocifreestatement($exQuery_INUP_STATUS);
        }
        return $START_TIME;
    }
    function GET_END_TIMES ($conn,$time){
        global $db,$db_type;
        $END_TIME = $time;
        $strSQL_INUP_END_TIME =  " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = '".$END_TIME."' WHERE CONFIG_NAME = 'GMIS_END_TIME_PROCESS' ";
        $strSQL_INUP_STATUS = " UPDATE SYSTEM_CONFIG SET CONFIG_VALUE = 'SUCCESS' WHERE CONFIG_NAME = 'GMIS_STATUS_PROCESS' ";
         
        if($db_type=="mysql"){
            $db->send_cmd($strSQL_INUP_END_TIME);
            $db->send_cmd($strSQL_INUP_STATUS);
        } else {
            $exQuery_INUP_END_TIME = OCIParse($conn, $strSQL_INUP_END_TIME);
            $exQuery_INUP_STATUS = OCIParse($conn, $strSQL_INUP_STATUS);
            OCIExecute($exQuery_INUP_END_TIME);
            OCIExecute($exQuery_INUP_STATUS);
            ocifreestatement($exQuery_INUP_END_TIME);
            ocifreestatement($exQuery_INUP_STATUS);
        }    
        return $END_TIME;
    }
    function generate_condition($current_index){
        global $arr_rpt_order;
        global $POS_NO;
        for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "POSNO" :	
                    if($POS_NO){ $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO')";}
                    else{ $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO' or a.POS_NO is null)";}
                break;
            } // end switch case
        } // end for
        $addition_condition = "";
        if(count($arr_addition_condition)){ $addition_condition = implode(" and ", $arr_addition_condition);}
        return $addition_condition;
    } // function
    function initialize_parameter($current_index){
        global $arr_rpt_order;
        global $POS_NO;
        for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                case "POSNO" :	
                    $POS_NO = -1;
                break;
            } // end switch case
        } // end for
    } // function
    if($Method=='GET_TOTAL_DATA'){
		$logfile = '../../Excel/tmp/results.txt';
        if(is_file($logfile)){
            unlink($logfile);
        }
        echo $Method($conn->connect_db_oci8());
        //unlink($logfile);
        //   ********** เปลี่ยนจาก Delete เป็น TRUNCATE table เพื่อให้การทำงานไวขึ้น ***************
        $cmddel1 = " TRUNCATE TABLE GMIS_GPIS ";//$cmddel1 = " delete from GMIS_GPIS ";
        $conn->execNonQuery($cmddel1);
        $cmddel2 = " TRUNCATE TABLE GMIS_GPIS_FLOW_IN ";//$cmddel2 = " delete from GMIS_GPIS_FLOW_IN ";
        $conn->execNonQuery($cmddel2);
        $cmddel3 = " TRUNCATE TABLE GMIS_GPIS_FLOW_OUT ";//$cmddel3 = " delete from GMIS_GPIS_FLOW_OUT ";
        $conn->execNonQuery($cmddel3);
        exit();
    }
    if($Method=='GET_START_TIMES'){
        $data_stime = $conn->getTimeServer();
        $START_TIME_SERVER = $data_stime[DATE_NOW]." ".$data_stime[TIME_NOW];
        echo $Method($conn->connect_db_oci8(),$START_TIME_SERVER);
        exit();
    }
    if($Method=='GET_END_TIMES'){
        $data_etime = $conn->getTimeServer();
        $END_TIME_SERVER = $data_etime[DATE_NOW]." ".$data_etime[TIME_NOW];
        echo $Method($conn->connect_db_oci8(),$END_TIME_SERVER);
        exit();
    }
    if($Method=='GET_TOTAL_TIME'){
        echo $Method($conn->connect_db_oci8());
        exit();
    }
    
    $budget_year = $search_budget_year - 543; 
    $budget_year_from = $budget_year - 1; 
    $budget_year_from = $budget_year_from.'-10-01'; 
    $budget_year_to = $budget_year.'-09-30';
    $arr_rpt_order = explode("|", $RPTORD_LIST);
    $select_list = "";
    $order_by = "";
    $heading_name = "";
    for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
        $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
        switch($REPORT_ORDER){
            case "POSNO" :
                if($select_list){ $select_list .= ", ";}
                if($order_by){ $order_by .= ", ";}
                $select_list .= "a.POS_NO";
                $order_by .= "to_number(replace(a.POS_NO,'-',''))";
                $heading_name .= " เลขที่ตำแหน่ง";
            break;
        } // end switch case
    } // end for
    if(!trim($order_by)){ $order_by = "a.POS_NO";}
    if(!trim($select_list)) {$select_list = "a.POS_NO";}
    $search_con_display = "";
    $arr_search_condition[] = "(a.POS_STATUS=1)";
    $list_type_text = $ALL_REPORT_TITLE;
    if($DEPARTMENT_ID){
        $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
        $list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
    }elseif($MINISTRY_ID){
        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
        $exQuery = OCIParse($conn->connect_db_oci8(), $cmd);
        OCIExecute($exQuery);
        while((OCIFetchInto($exQuery, $data, OCI_ASSOC + OCI_RETURN_NULLS))){ $arr_org_ref[] = $data[ORG_ID];}
        $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
        $list_type_text .= " - $MINISTRY_NAME";
    }elseif($PROVINCE_CODE){
        $PROVINCE_CODE = trim($PROVINCE_CODE);
        $arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
        $list_type_text .= " - $PROVINCE_NAME";
    } // end if
    if(count($arr_search_condition)){ $search_con_display = " where ". implode(" and ", $arr_search_condition);}

	// ===== select data =====
    $search_condition = str_replace(" where ", " and ", $search_con_display);	
    $cmd = "select		
                    b.ORG_ID_REF, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, a.PT_CODE, 
                    a.CL_NAME, g.OT_NAME, b.ORG_CODE, a.PM_CODE, a.PL_CODE, b.PV_CODE, f.PV_NAME, 
                    b.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.SKILL_CODE 
            from	PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_PROVINCE f, PER_ORG_TYPE g
            where	a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and 
                    b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)	
                    $search_condition
            order by b.ORG_ID_REF, $order_by   ";
    $exQuery1 = OCIParse($conn->connect_db_oci8(), $cmd);
    OCIExecute($exQuery1);
    initialize_parameter(0);
    $ORG_ID_REF = -1;
    $DATA_COUNT_SEQS = 1;
    $CNT_DATA_ALL = GET_TOTAL_DATA($conn->connect_db_oci8());
    while (OCIFetchInto($exQuery1, $data, OCI_ASSOC + OCI_RETURN_NULLS)){  
        if($ORG_ID_REF != $data[ORG_ID_REF]){
            $ORG_ID_REF = $data[ORG_ID_REF];

            $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
            $data2 = $conn->execQuery($cmd);
            $ORG_NAME_REF = $data2[ORG_NAME];
            $data_count++;
        } // end if

        $PER_TYPE = 1;
        $GPIS_FLAG = "DATA";
        //$data_arr = array();
        for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
            $REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
            switch($REPORT_ORDER){
                    case "POSNO" :
                            if($POS_NO != trim($data[POS_NO])){
                                    if ($PER_TYPE == 1){ $POS_NO = trim($data[POS_NO]);}
                                    $addition_condition = generate_condition($rpt_order_index);
                                    if($rpt_order_index < (count($arr_rpt_order) - 1)){ initialize_parameter($rpt_order_index + 1);}
                                    if($rpt_order_index == (count($arr_rpt_order) - 1)){	
                                            $data_row++;
                                            $POS_ID = $data[POS_ID];
                                            /*echo $POS_ID.'.';
                                            ob_flush(); 
                                            flush(); */
                                            $CL_NAME = trim($data[CL_NAME]);
                                            $PT_CODE = trim($data[PT_CODE]);
                                            $cmd_pt = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
                                            $data_pt = $conn->execQuery($cmd_pt);
                                            $PT_NAME = ($data_pt[PT_NAME]);

                                            $PM_CODE = trim($data[PM_CODE]);
                                            $PM_NAME = trim($data[PM_NAME]);					
                                            $PL_CODE = trim($data[PL_CODE]);
                                            $PL_NAME = trim($data[PL_NAME]);	
                                            $PV_CODE = trim($data[PV_CODE]);
                                            $PV_NAME = trim($data[PV_NAME]);
                                            $OT_CODE = trim($data[OT_CODE]);
                                            $OT_NAME = trim($data[OT_NAME]);
                                            $SKILL_CODE = trim($data[SKILL_CODE]);
                                            $SKILL_NAME = trim($data[SKILL_NAME]);					

                                            $ORG_CODE = substr(trim($data[ORG_CODE]),0,5);	
                                            $ORG_ID_2 = trim($data[ORG_ID]);
                                            $ORG_NAME_2 = trim($data[ORG_NAME]);	
                                            // === หาจังหวัดและประเทศตามโครงสร้าง
                                            $CT_NAME_ORG = $PV_NAME_ORG = "";
                                            $cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
                                                            from PER_ORG a, PER_PROVINCE b, PER_COUNTRY c 
                                                            where ORG_ID=$ORG_ID_2 and a.PV_CODE=b.PV_CODE(+) and 
                                                                            a.CT_CODE=c.CT_CODE(+) ";
                                            $data3 = $conn->execQuery($cmd);
                                            $PV_CODE_ORG = trim($data3[PV_CODE]);
                                            $PV_NAME_ORG = trim($data3[PV_NAME]);
                                            $CT_CODE_ORG = trim($data3[CT_CODE]);
                                            $CT_NAME_ORG = trim($data3[CT_NAME]);

                                            unset($tmp_ORG_ID, $ORG_ID_search, $ORG_ID_3, $ORG_ID_4, $ORG_NAME_3, $ORG_NAME_4);
                                            $ORG_ID_3 = $tmp_ORG_ID[] = trim($data[ORG_ID_1]);
                                            $ORG_ID_4 = $tmp_ORG_ID[] = trim($data[ORG_ID_2]);
                                            if($ORG_ID_3 && $ORG_ID_4){
                                                $ORG_ID_search = implode(", ", $tmp_ORG_ID);
                                                $cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_search) ";
                                                $exQuery2 = OCIParse($conn->connect_db_oci8(), $cmd);
                                                OCIExecute($exQuery2);
                                                while((OCIFetchInto($exQuery2, $data2, OCI_ASSOC + OCI_RETURN_NULLS))) {
                                                    $ORG_NAME_3 = ($ORG_ID_3 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_3";
                                                    $ORG_NAME_4 = ($ORG_ID_4 == trim($data2[ORG_ID]))? trim($data2[ORG_NAME]) : "$ORG_NAME_4";
                                                }	// while
                                            }
                                            $cmdOrg = " select a.ORG_NAME, b.ORG_NAME as ORGNAME2, c.ORG_NAME as ORGNAME1
                                                            from PER_ORG a, PER_ORG b, PER_ORG c
                                                            where a.ORG_ID=$ORG_ID_2 and a.ORG_ID_REF=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID";
                                            $data_org = $conn->execQuery($cmdOrg);
                                            $ORG_NAME = trim($data_org[ORGNAME1]);
                                            $ORG_NAME_1 = trim($data_org[ORGNAME2]);
                                            $BIRTHDATE = "";
                                            if ($DEPARTMENT_NAME=="กรมการปกครอง"){ $where = "PAY_ID=$POS_ID ";}
                                            else{ $where = "POS_ID=$POS_ID ";}
                                            if ($GPIS_FLAG == "DATA"){ $where .= " and PER_STATUS=1";}
                                            $cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
                                                                            PER_SALARY, PER_MGTSALARY, PER_OFFNO, PER_CARDNO, PN_CODE, 
                                                                            PER_NAME, PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, 
                                                                            ORG_ID, MOV_CODE, PER_OCCUPYDATE, PER_POSDATE, PER_RETIREDATE, PER_DISABILITY, RE_CODE, 
                                                                            PER_UNION, PER_UNION2, PER_UNION3, PER_UNION4, PER_UNION5  
                                                            from		PER_PERSONAL
                                                            where	PER_TYPE=$PER_TYPE and $where ";
                                            $data2 = $conn->execQuery($cmd);
                                            $PER_ID = $data2[PER_ID];
                                            $PER_GENDER = $data2[PER_GENDER];
                                            $PER_DISABILITY = $data2[PER_DISABILITY];
                                            $PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
                                            $BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
                                            if($PER_BIRTHDATE){
                                                    $arr_temp = explode("-", $PER_BIRTHDATE);
                                                    $BIRTHDATE_D = $arr_temp[2];
                                                    $BIRTHDATE_M = $arr_temp[1];
                                                    $BIRTHDATE_Y = $arr_temp[0] + 543;
                                                    $RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 60):($arr_temp[0] + 543 + 61);
                                                    $BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
                                            } // end if
                                            $STARTDATE = show_date_format($data2[PER_STARTDATE],1);
                                            $FLOWDATE = show_date_format($data2[PER_OCCUPYDATE],1);
                                            $LEVEL_NO = trim($data2[LEVEL_NO]);
                                            if (substr($LEVEL_NO,0,1)=='O'){ $LEVEL_GROUP = 'ทั่วไป';}
                                            elseif (substr($LEVEL_NO,0,1)=='K'){ $LEVEL_GROUP = 'วิชาการ';}
                                            elseif (substr($LEVEL_NO,0,1)=='D'){ $LEVEL_GROUP = 'อำนวยการ';}
                                            elseif (substr($LEVEL_NO,0,1)=='M'){ $LEVEL_GROUP = 'บริหาร';}
                                            $PER_SALARY = $data2[PER_SALARY];//.'.00';
                                            $PER_MGTSALARY = $data2[PER_MGTSALARY];
                                            $PER_OFFNO = $data2[PER_OFFNO];
                                            $PER_CARDNO = $data2[PER_CARDNO];
                                            $PN_CODE = $data2[PN_CODE];
                                            $PER_NAME = $data2[PER_NAME];
                                            $PER_SURNAME = $data2[PER_SURNAME];
                                            $PER_ENG_NAME = $data2[PER_ENG_NAME];
                                            $PER_ENG_SURNAME = $data2[PER_ENG_SURNAME];
                                            $PER_UNION = $data2[PER_UNION];
                                            $PER_UNION2 = $data2[PER_UNION2];
                                            $PER_UNION3 = $data2[PER_UNION3];
                                            $PER_UNION4 = $data2[PER_UNION4];
                                            $PER_UNION5 = $data2[PER_UNION5];
                                            $RE_CODE = trim($data2[RE_CODE]);

                                            $ORG_ID_ASS = trim($data2[ORG_ID]);
                                            if ($ORG_ID_ASS) {
                                                // === หาจังหวัดและประเทศตามมอบหมายงาน
                                                $cmd = " select a.PV_CODE, PV_NAME, a.CT_CODE, CT_NAME 
                                                                from PER_ORG_ASS a, PER_PROVINCE b, PER_COUNTRY c 
                                                                where ORG_ID=$ORG_ID_ASS and a.PV_CODE=b.PV_CODE(+) and 
                                                                                a.CT_CODE=c.CT_CODE(+) ";
                                                $data3 = $conn->execQuery($cmd);
                                                $PV_CODE_ORG_ASS = trim($data3[PV_CODE]);
                                                $PV_NAME_ORG_ASS = trim($data3[PV_NAME]);
                                                $CT_CODE_ORG_ASS = trim($data3[CT_CODE]);
                                                $CT_NAME_ORG_ASS = trim($data3[CT_NAME]);
                                            } else {
                                                $PV_CODE_ORG_ASS = $PV_CODE_ORG;
                                                $PV_NAME_ORG_ASS = $PV_NAME_ORG;						
                                                $CT_CODE_ORG_ASS = $CT_CODE_ORG;	
                                                $CT_NAME_ORG_ASS = $CT_NAME_ORG;							
                                            }
                                            $CLASS_NAME = "";
                                            $cmd_pn = " select PN_NAME, RANK_FLAG from PER_PRENAME where PN_CODE='$PN_CODE' ";
                                            $data2 = $conn->execQuery($cmd_pn);

                                            $PN_NAME = trim($data2[PN_NAME]);
                                            $RANK_FLAG = trim($data2[RANK_FLAG]);
                                            if ($RANK_FLAG==1) $CLASS_NAME = $PN_NAME;
                                            if (!$LEVEL_NO) {
                                                    $cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
                                                    $data2 = $conn->execQuery($cmd);
                                                    $LEVEL_NO = $data2[LEVEL_NO_MIN];
                                            }
                                            $cmd = " select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
                                            $data2 = $conn->execQuery($cmd);
                                            $LEVEL_NAME = trim($data2[LEVEL_NAME]);
                                            $POSITION_TYPE = trim($data2[POSITION_TYPE]);
                                            $cmd = " select SKILL_NAME from PER_SKILL where SKILL_CODE='$SKILL_CODE' ";
                                            $data2 = $conn->execQuery($cmd);
                                            $SKILL_NAME = trim($data2[SKILL_NAME]);
                                            $cmd = " select RE_NAME from PER_RELIGION where trim(RE_CODE)='$RE_CODE' ";
                                            $data3 = $conn->execQuery($cmd);
                                            $RE_NAME = trim($data3[RE_NAME]);
                                            $EL_CODE = $EL_NAME = $EN_NAME = $EM_NAME = $INS_CODE = $INS_NAME = $ST_CODE = $ST_NAME = $CT_CODE_EDU = $CT_NAME_EDU = "";
                                            $SAH_EFFECTIVEDATE = $POH_DOCNO = $POH_DOCDATE = $POH_EFFECTIVEDATE = $SCH_NAME = $DC_NAME = "";
                                            $RESULT1 = $RESULT2 = $PERCENT_SALARY1 = $PERCENT_SALARY2 = $UNION_CODE = "";
                                            $POH_EFFECTIVEDATE = $PROMOTEDATE = $MOV_NAME = $LEVEL_NO_C = "";
                                            $PT_CODE_C = "('')";
                                            if($PER_ID){
                                                /*วุฒิในตำแหน่งปัจจุบัน และ วุฒิสูงสุด */
                                                $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                        where a.PER_ID=$PER_ID 
                                                        and (a.EDU_TYPE like '%2%' and a.EDU_TYPE like '%4%')
                                                            and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                            and a.EM_CODE=d.EM_CODE(+)";
                                                $count_educate = $conn->num_row($cmd);
                                                if(!$count_educate){ //
                                                    /*วุฒิสูงสุด */
                                                    $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                        where a.PER_ID=$PER_ID 
                                                        and a.EDU_TYPE like '%4%'
                                                            and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                            and a.EM_CODE=d.EM_CODE(+)";
                                                    $count_educate = $conn->num_row($cmd);
                                                }
                                                if(!$count_educate){
                                                    /*วุฒิในตำแหน่งปัจจุบัน*/
                                                    $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                        where a.PER_ID=$PER_ID
                                                        and a.EDU_TYPE like '%2%'
                                                            and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                            and a.EM_CODE=d.EM_CODE(+)";
                                                    $count_educate = $conn->num_row($cmd);
                                                }
                                                if(!$count_educate){
                                                    /*วุฒิที่ใช้บรรจุ */
                                                    $cmd = "select a.EDU_TYPE,c.EL_CODE, c.EL_NAME, b.EN_NAME, d.EM_NAME, a.INS_CODE, a.ST_CODE, EDU_INSTITUTE  
                                                        from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c, PER_EDUCMAJOR d  
                                                        where a.PER_ID=$PER_ID 
                                                        and a.EDU_TYPE like '%1%'
                                                            and a.EN_CODE=b.EN_CODE(+) and trim(b.EL_CODE)=trim(c.EL_CODE(+)) 
                                                            and a.EM_CODE=d.EM_CODE(+)";
                                                }
                                                $data2 = $conn->execQuery($cmd);
                                                $EL_CODE = trim($data2[EL_CODE]);
                                                $EL_NAME = trim($data2[EL_NAME]);/*ที่ต้องปรับ????*/
                                                $EN_NAME = trim($data2[EN_NAME]);
                                                $EM_NAME = stripslashes(trim($data2[EM_NAME]));
                                                $INS_CODE = trim($data2[INS_CODE]);
                                                $ST_CODE = trim($data2[ST_CODE]);
                                                if ($INS_CODE) {    
                                                    // หาชื่อโรงเรียน และประเทศของโรงเรียน
                                                    $cmd = " select INS_NAME, a.CT_CODE, CT_NAME from PER_INSTITUTE a, PER_COUNTRY b 
                                                                    where INS_CODE='$INS_CODE' and a.CT_CODE=b.CT_CODE(+) ";			
                                                    $data2 = $conn->execQuery($cmd);
                                                    $INS_NAME = stripslashes(trim($data2[INS_NAME]));
                                                    $CT_CODE_EDU = trim($data2[CT_CODE]);
                                                    $CT_NAME_EDU = trim($data2[CT_NAME]);
                                                } else {	
                                                        $INS_NAME = trim($data2[EDU_INSTITUTE]);
                                                } // end if
                                                // === หาวันที่เงินเดือนมีผล 
                                                $cmd = " select SAH_EFFECTIVEDATE
                                                                from   PER_SALARYHIS
                                                                where PER_ID=$PER_ID 
                                                                order by SAH_EFFECTIVEDATE desc ";
                                                $data2 = $conn->execQuery($cmd);
                                                $SAH_EFFECTIVEDATE = show_date_format($data2[SAH_EFFECTIVEDATE],1);

                                                // === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
                                                $cmd = " select POH_DOCNO, POH_DOCDATE, POH_EFFECTIVEDATE
                                                                from   PER_POSITIONHIS a, PER_MOVMENT b
                                                                where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
                                                                (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                                order by POH_EFFECTIVEDATE desc ";
                                                $data2 = $conn->execQuery($cmd);
                                                $POH_DOCNO = trim($data2[POH_DOCNO]);
                                                $POH_DOCDATE = show_date_format($data2[POH_DOCDATE],1);
                                                $POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);

                                                // === หาชื่อทุน และแหล่งทุน
                                                $cmd = "	select 	ST_NAME  
                                                                from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
                                                                where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and a.ST_CODE=b.ST_CODE(+) ";
                                                $count_educate = $conn->num_row($cmd);
                                                if(!$count_educate){
                                                    $cmd = "	select 	ST_NAME  
                                                                    from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
                                                                    where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' and a.ST_CODE=b.ST_CODE(+) ";
                                                    $count_educate = $conn->num_row($cmd);
                                                }
                                                if(!$count_educate){
                                                    $cmd = "	select 	ST_NAME  
                                                                    from 	PER_EDUCATE a, PER_SCHOLARTYPE b  
                                                                    where	a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and a.ST_CODE=b.ST_CODE(+) ";
                                                    $count_educate = $conn->num_row($cmd);
                                                }
                                                if(!$count_educate){
                                                    $cmd = " select SCH_NAME, ST_NAME
                                                            from   PER_SCHOLAR a, PER_SCHOLARSHIP b, PER_SCHOLARTYPE c
                                                            where PER_ID=$PER_ID and a.SCH_CODE=b.SCH_CODE and 
                                                                       b.ST_CODE=c.ST_CODE
                                                            order by a.SC_ID desc ";
                                                    $count_educate = $conn->num_row($cmd);
                                                }
                                                $data2 = $conn->execQuery($cmd);
                                                $SCH_NAME = trim($data2[SCH_NAME]);
                                                $ST_NAME = trim($data2[ST_NAME]);
                                                // หาเครื่องราชฯ
                                                $cmd = " select DC_NAME from PER_DECORATEHIS a, PER_DECORATION b 
                                                                where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE(+)
                                                                order by DC_TYPE, DC_ORDER ";	
                                                $data2 = $conn->execQuery($cmd);
                                                $DC_NAME = trim($data2[DC_NAME]);
                                                // === หาร้อยละที่ได้รับการเลื่อนเงินเดือน 
                                                $cmd = " select MOV_NAME, SAH_PERCENT_UP
                                                                                from   PER_SALARYHIS a, PER_MOVMENT b
                                                                                where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year' and 
                                                                                                        SAH_KF_CYCLE = 1 and substr(SAH_EFFECTIVEDATE, 6,5) = '04-01' and MOV_SUB_TYPE <> '0'
                                                                                order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
                                                $data2 = $conn->execQuery($cmd);
                                                $RESULT1 = trim($data2[MOV_NAME]);
                                                $PERCENT_SALARY1 = trim($data2[SAH_PERCENT_UP]);
                                                $search_budget_year2 = $search_budget_year - 1;
                                                $cmd = " select MOV_NAME, SAH_PERCENT_UP
                                                                                from   PER_SALARYHIS a, PER_MOVMENT b
                                                                                where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and SAH_KF_YEAR = '$search_budget_year2' and 
                                                                                                        SAH_KF_CYCLE = 2  and substr(SAH_EFFECTIVEDATE, 6,5) = '10-01' and MOV_SUB_TYPE <> '0'
                                                                                order by SAH_EFFECTIVEDATE desc, SAH_CMD_SEQ desc, SAH_SALARY desc ";
                                                $data2 = $conn->execQuery($cmd);
                                                $RESULT2 = trim($data2[MOV_NAME]);
                                                $PERCENT_SALARY2 = trim($data2[SAH_PERCENT_UP]);
                                                // วันเข้าสู่ระดับปัจจุบัน 
                                                if ($LEVEL_NO=="M2") {$LEVEL_NO_C = "('11','10')"; $PT_CODE_C = "('32')";}
                                                elseif ($LEVEL_NO=="M1") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('32')";}
                                                elseif ($LEVEL_NO=="D2") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('32')";}
                                                elseif ($LEVEL_NO=="D1") {$LEVEL_NO_C = "('08')"; $PT_CODE_C = "('31')";}
                                                elseif ($LEVEL_NO=="K5") {$LEVEL_NO_C = "('10')"; $PT_CODE_C = "('22','21')";}
                                                elseif ($LEVEL_NO=="K4") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('22','21')";}
                                                elseif ($LEVEL_NO=="K3") {$LEVEL_NO_C = "('08')"; $PT_CODE_C = "('11','12', NULL)";}
                                                elseif ($LEVEL_NO=="K2") {$LEVEL_NO_C = "('07','06')"; $PT_CODE_C = "('11','12', NULL)";}
                                                elseif ($LEVEL_NO=="K1") {$LEVEL_NO_C = "('05','04','03')"; $PT_CODE_C = "('11','12', NULL)";}
                                                elseif ($LEVEL_NO=="O4") {$LEVEL_NO_C = "('09')"; $PT_CODE_C = "('11', NULL)";}
                                                elseif ($LEVEL_NO=="O3") {$LEVEL_NO_C = "('08','07')"; $PT_CODE_C = "('11', NULL)";}
                                                elseif ($LEVEL_NO=="O2") {$LEVEL_NO_C = "('06','05')"; $PT_CODE_C = "('11', NULL)";}
                                                elseif ($LEVEL_NO=="O2") {$LEVEL_NO_C = "('04','03','02','01')"; $PT_CODE_C = "('11', NULL)";}
                                                else {$LEVEL_NO_C = "('".$LEVEL_NO."')";}
                                                if ($MFA_FLAG==1 && $PM_CODE){ 
                                                    $cmd = " select POH_EFFECTIVEDATE
                                                            from   PER_POSITIONHIS a, PER_MOVMENT b
                                                            where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and PM_CODE='$PM_CODE' and  
                                                                                    (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                            order by POH_EFFECTIVEDATE ";
                                                }else{
                                                    $cmd = " select POH_EFFECTIVEDATE
                                                            from   PER_POSITIONHIS a, PER_MOVMENT b
                                                            where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and (LEVEL_NO='$LEVEL_NO' or 
                                                                                    (LEVEL_NO in $LEVEL_NO_C and PT_CODE in $PT_CODE_C)) and 
                                                                                    (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                            order by POH_EFFECTIVEDATE ";
                                                }    
                                                $data2 = $conn->execQuery($cmd);
                                                $PROMOTEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
                                                // วันดำรงตำแหน่งปัจจุบัน		
                                                $cmd = " select POH_EFFECTIVEDATE, MOV_NAME  
                                                                                from PER_POSITIONHIS a, PER_MOVMENT b 
                                                                                where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and 
                                                                                                                (MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
                                                                                order by POH_EFFECTIVEDATE desc";
                                                $data2 = $conn->execQuery($cmd);
                                                $POH_EFFECTIVEDATE = show_date_format($data2[POH_EFFECTIVEDATE],1);
                                                $MOV_NAME = trim($data2[MOV_NAME]);
                                                // ความเชี่ยวชาญพิเศษ และ เน้นทาง  col 1 07/11/2016 by somkiet
                                                if($PER_ID){
                                                    /*ปรับของเก่าให้เร็วขึ้น*/
                                                    $cmd = "    WITH TBCHECK AS (
                                                                    SELECT REF_CODE , SS_CODE
                                                                    FROM PER_SPECIAL_SKILLGRP
                                                                    START WITH REF_CODE IS null
                                                                    CONNECT BY NOCYCLE PRIOR trim(SS_CODE) = trim(REF_CODE)
                                                                ),  TB_MAIN AS ( 
                                                                    SELECT 	ROW_NUMBER() OVER (
                                                                        PARTITION BY b.PER_ID
                                                                        ORDER BY (CASE WHEN b.AUDIT_FLAG is NULL or b.AUDIT_FLAG='N' THEN '0' ELSE b.AUDIT_FLAG END) desc, b.SPS_SEQ_NO  ASC
                                                                    ) ROW_NUM , 
                                                                    b.PER_ID, c.SS_NAME, SPS_EMPHASIZE, b.SS_CODE, e.REF_CODE, b.LEVELSKILL_CODE,(CASE WHEN b.AUDIT_FLAG IS NULL OR b.AUDIT_FLAG='N' THEN '0' ELSE b.AUDIT_FLAG END) AS AUDIT_FLAG,
                                                                    b.SPS_FLAG , d.LEVELSKILL_NAME
                                                                    FROM 	 PER_SPECIAL_SKILL b ,PER_SPECIAL_SKILLGRP c ,PER_LEVELSKILL d ,TBCHECK e
                                                                    WHERE 	 b.SS_CODE = c.SS_CODE and b.LEVELSKILL_CODE = d.LEVELSKILL_CODE(+) AND  trim(b.SS_CODE) = trim(e.SS_CODE(+))
                                                                ),  TB1 AS (
                                                                    SELECT * FROM TB_MAIN WHERE  ROW_NUM=1
                                                                ),  TB2 AS (
                                                                    SELECT  ROW_NUM AS ROW_NUM2 , PER_ID AS PER_ID2, SS_NAME as SS_NAME2, SPS_EMPHASIZE as SPS_EMPHASIZE2,
                                                                            SS_CODE AS SS_CODE2, REF_CODE AS REF_CODE2, LEVELSKILL_CODE AS LEVELSKILL_CODE2, AUDIT_FLAG AS AUDIT_FLAG2,
                                                                            SPS_FLAG AS SPS_FLAG2 , LEVELSKILL_NAME as LEVELSKILL_NAME2
                                                                    FROM TB_MAIN WHERE  ROW_NUM=2
                                                                ) SELECT a.* , b.* FROM TB1 a ,TB2 b WHERE a.PER_ID=b.PER_ID2(+) AND a.PER_ID=$PER_ID";
                                                    //die("<pre>".$cmd);
                                                    $data2 = $conn->execQuery($cmd);
                                                    // ความเชี่ยวชาญพิเศษ และ เน้นทาง1
                                                    $SS_CHK_CODE = trim($data2[SS_CODE]);
                                                    if($SS_CHK_CODE !== "-1"){
                                                        $SS_CODE = trim($data2[SS_CODE]);
                                                        $REF_CODE = trim($data2[REF_CODE]);
                                                        $SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
                                                        $LEVELSKILL_CODE = trim($data2[LEVELSKILL_CODE]);
                                                        $LEVELSKILL_NAME = $data2[LEVELSKILL_NAME];
                                                    }else{
                                                        $SS_CODE = "";
                                                        $REF_CODE = "";
                                                        $SPS_EMPHASIZE = "";
                                                        $LEVELSKILL_CODE = "";
                                                        $LEVELSKILL_NAME = "";
                                                    }
                                                    $AUDIT_FLAG = trim($data2[AUDIT_FLAG]);
                                                    $SPS_FLAG_C = trim($data2[SPS_FLAG]);
                                                    if($SPS_FLAG_C == '1'){
                                                        $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญในงานราชการ";
                                                    }else if($SPS_FLAG_C == '2'){
                                                         $SPS_FLAG = $SPS_FLAG_C."-ความเชี่ยวชาญอื่น ๆ";
                                                    }else{
                                                         $SPS_FLAG = "";
                                                    }
                                                    // ความเชี่ยวชาญพิเศษ และ เน้นทาง2
                                                    $SS_CODE2 = trim($data2[SS_CODE2]);
                                                    $REF_CODE2 = trim($data2[REF_CODE2]);
                                                    $SPS_EMPHASIZE2 = trim($data2[SPS_EMPHASIZE2]);
                                                    $AUDIT_FLAG2 = trim($data2[AUDIT_FLAG2]);
                                                    $SPS_FLAG_C2 = trim($data2[SPS_FLAG2]);
                                                    if($SPS_FLAG_C2 == '1'){
                                                        $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญในงานราชการ";
                                                    }else if($SPS_FLAG_C2 == '2'){
                                                         $SPS_FLAG2 = $SPS_FLAG_C2."-ความเชี่ยวชาญอื่น ๆ";
                                                    }else{
                                                         $SPS_FLAG2 = "";
                                                    }
                                                    $LEVELSKILL_CODE2 = trim($data2[LEVELSKILL_CODE2]);
                                                    $LEVELSKILL_NAME2 = $data2[LEVELSKILL_NAME2];                                                                        
                                                }
                                                // === หารหัสสหภาพข้าราชการ
                                                $UNION_CODE = "22222";
                                                if ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "12222";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "21222";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "22122";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "22212";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5==1){ $UNION_CODE = "22221";}
                                                elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "11112";}
                                                elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "11122";}
                                                elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "11222";}
                                                elseif ($PER_UNION==1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "11212";}
                                                elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "12122";}
                                                elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "12112";}
                                                elseif ($PER_UNION==1 && $PER_UNION2!=1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "12212";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "21112";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3==1 && $PER_UNION4!=1 && $PER_UNION5!=1){ $UNION_CODE = "21122";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2==1 && $PER_UNION3!=1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "21212";}
                                                elseif ($PER_UNION!=1 && $PER_UNION2!=1 && $PER_UNION3==1 && $PER_UNION4==1 && $PER_UNION5!=1){ $UNION_CODE = "22112";}
                                            } // end if

                                            $cmd_level = " select LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
                                            $data_level = $conn->execQuery($cmd_level);
                                            $TMP_POSITION_TYPE = $data_level[POSITION_TYPE];
                                            $TMP_POSITION_LEVEL = $data_level[POSITION_LEVEL];
                                            if ($LEVEL_NO=="D1" || $LEVEL_NO=="D2" || $LEVEL_NO=="M1" || $LEVEL_NO=="M2"){
                                                    $TEMPLEVEL = $TMP_POSITION_TYPE.$TMP_POSITION_LEVEL; // บริหารสูง
                                            }else{
                                                    $TEMPLEVEL = $TMP_POSITION_LEVEL; // สูง
                                            }
                                            $PER_GENDER_NAME = $PER_DISABILITY_NAME = "";
                                            if ($PER_GENDER == 1){ $PER_GENDER_NAME = "ชาย";}
                                            elseif ($PER_GENDER == 2){ $PER_GENDER_NAME = "หญิง";}
                                            if ($PER_DISABILITY == 1){ $PER_DISABILITY_NAME = "ปกติ";}
                                            elseif ($PER_DISABILITY >= 2){ $PER_DISABILITY_NAME = "พิการ";}
                                            if ($BIRTHDATE=="//"){ $BIRTHDATE = "";}
                                            if ($STARTDATE=="//"){ $STARTDATE = "";}
                                            if ($FLOWDATE=="//"){ $FLOWDATE = "";}
                                            $RESIGNDATE = '';
                                            if ($RESIGNDATE=="//"){ $RESIGNDATE = "";}
                                            if ($PROMOTEDATE=="//"){ $PROMOTEDATE = "";}
                                            if ($PER_NAME){ $POS_STATUS = "มีคนถือครอง";}
                                            else {$POS_STATUS = "ตำแหน่งว่าง";}
                                            $LEVEL_GROUP = $LEVEL_GROUP;
                                            if ($SAH_EFFECTIVEDATE=="//"){ $SAH_EFFECTIVEDATE = "";}
                                            //$PER_SALARY = $arr_content[$data_count][per_salary].".00";
                                            //if ($PER_MGTSALARY) $PER_MGTSALARY .= ".00";
                                            if ($POH_EFFECTIVEDATE=="//"){ $POH_EFFECTIVEDATE = "";}
                                            if (!get_magic_quotes_gpc()) {
                                                    $EM_NAME = addslashes(str_replace('"', "&quot;", trim($EM_NAME)));
                                                    $INS_NAME = addslashes(str_replace('"', "&quot;", trim($INS_NAME)));
                                            }else{
                                                    $EM_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($EM_NAME))));
                                                    $INS_NAME = addslashes(str_replace('"', "&quot;", stripslashes(trim($INS_NAME))));
                                            }
                                            if($PER_NAME){
                                                if($SS_CHK_CODE === "-1"){}
                                                else{
                                                    if($REF_CODE == NULL){
                                                        $TMP_SS_CODE = $SS_CODE;
                                                        $TMP_REF_CODE = $REF_CODE;
                                                    }else if($REF_CODE){
                                                        $TMP_SS_CODE = $REF_CODE;
                                                        $TMP_REF_CODE = $SS_CODE;
                                                    }
                                                    $LEVELSKILL_NAME=$LEVELSKILL_NAME;
                                                    if($AUDIT_FLAG === "0"){
                                                        $SPS_EMPHASIZE = "*".$SPS_EMPHASIZE;
                                                    }else{                  
                                                        $SPS_EMPHASIZE = $AUDIT_FLAG.$SPS_EMPHASIZE;
                                                    }
                                                    $SPS_FLAG = $SPS_FLAG;
                                                    if($REF_CODE2 == NULL){
                                                        $TMP_SS_CODE2 = $SS_CODE2;
                                                        $TMP_REF_CODE2 = $REF_CODE2;
                                                    }else if($REF_CODE2){
                                                        $TMP_SS_CODE2 = $REF_CODE2;
                                                        $TMP_REF_CODE2 = $SS_CODE2;
                                                    }
                                                    $LEVELSKILL_NAME2 = $LEVELSKILL_NAME2;
                                                    if($AUDIT_FLAG2 === "0"){
                                                        $SPS_EMPHASIZE2 = "*".$SPS_EMPHASIZE2;
                                                    }else{
                                                        $SPS_EMPHASIZE2 = $AUDIT_FLAG2.$SPS_EMPHASIZE2;
                                                    }
                                                    $SPS_FLAG2 = $SPS_FLAG2;
                                                }
                                            }
                                            $PROMOTEDATE = $PROMOTEDATE;
                                            $EFFECTIVEDATE = $POH_EFFECTIVEDATE;

                                            $cmd1 = " SELECT TEMPPOSITIONNO FROM GMIS_GPIS WHERE TEMPPOSITIONNO = '$POS_NO' ";
                                            $data5 = $conn->execQuery($cmd1);
                                            $repeat_POS_NO = $data5[TEMPPOSITIONNO]; //error because TEMPLEVEL set 10 over 
                                            if(!$repeat_POS_NO){
												echo '.';
												ob_flush();
												flush();
                                                $strInsert = "    INSERT INTO GMIS_GPIS ( TEMPMINISTRY, TEMPORGANIZE, TEMPDIVISIONNAME, TEMPORGANIZETYPE, TEMPPOSITIONNO, 
                                                                                    TEMPMANAGEPOSITION, TEMPLINE, TEMPPOSITIONTYPE, TEMPLEVEL, TEMPCLNAME, 
                                                                                    TEMPSKILL, TEMPCOUNTRY, TEMPPROVINCE, TEMPPOSITIONSTATUS, TEMPCLASS, 
                                                                                    TEMPPRENAME, TEMPFIRSTNAME, TEMPLASTNAME, TEMPCARDNO, TEMPGENDER, 
                                                                                    TEMPSTATUSDISABILITY, TEMPRELIGION, TEMPBIRTHDATE, TEMPSALARY, TEMPPOSITIONSALARY, 
                                                                                    TEMPEDUCATIONLEVEL, TEMPEDUCATIONNAME, TEMPEDUCATIONMAJOR, TEMPGRADUATED, TEMPEDUCATIONCOUNTRY, 
                                                                                    TEMPSCHOLARTYPE, TEMPMOVEMENTTYPE, TEMPMOVEMENTDATE, TEMPSTARTDATE, TEMPFLOWDATE, 
                                                                                    TEMPRESIGNDATE, TEMPPROMOTEDATE, TEMPDECORATION, TEMPUNION, TEMPRESULT1, 
                                                                                    TEMPPERCENTSALARY1, TEMPRESULT2, TEMPPERCENTSALARY2, TEMPMPERSPSKILL1, TEMPSPERSPSKILL1, 
                                                                                    TEMPSKILLLEVEL1, TEMPPERSPSKILLDES1, TEMPSPS_FLAG1, TEMPMPERSPSKILL2, TEMPSPERSPSKILL2, 
                                                                                    TEMPSKILLLEVEL2, TEMPPERSPSKILLDES2, TEMPSPS_FLAG2, TEMPMPERSPSKILL3, TEMPSPERSPSKILL3, 
                                                                                    TEMPSKILLLEVEL3, TEMPPERSPSKILLDES3, TEMPSPS_FLAG3 )
                                                                            VALUES ('$ORG_NAME', '$ORG_NAME_1', '$ORG_NAME_2', '$OT_NAME', '$POS_NO', 
                                                                                    '$PM_NAME', '$PL_NAME', '$POSITION_TYPE', '$TEMPLEVEL', '$CL_NAME', 
                                                                                    '$SKILL_NAME', '$CT_NAME_ORG', '$PV_NAME_ORG', '$POS_STATUS', '$CLASS_NAME', 
                                                                                    '$PN_NAME', '$PER_NAME', '$PER_SURNAME', '$PER_CARDNO', '$PER_GENDER_NAME', 
                                                                                    '$PER_DISABILITY_NAME', '$RE_NAME', '$BIRTHDATE', '$PER_SALARY', '$PER_MGTSALARY', 
                                                                                    '$EL_NAME', '$EN_NAME', '".save_quote($EM_NAME)."', '".save_quote($INS_NAME)."', '$CT_NAME_EDU', 
                                                                                    '$ST_NAME', '$MOV_NAME', '$EFFECTIVEDATE', '$STARTDATE', '$FLOWDATE', 
                                                                                    '$RESIGNDATE', '$PROMOTEDATE', '$DC_NAME', '$UNION_CODE', '$RESULT1', 
                                                                                    '$PERCENT_SALARY1', '$RESULT2', '$PERCENT_SALARY2', '$TMP_SS_CODE', '$TMP_REF_CODE', 
                                                                                    '$LEVELSKILL_NAME', '$SPS_EMPHASIZE', '$SPS_FLAG', '$TMP_SS_CODE2', '$TMP_REF_CODE2', 
                                                                                    '$LEVELSKILL_NAME2', '$SPS_EMPHASIZE2', '$SPS_FLAG2', '', '', 
                                                                                    '', '', '')";
                                                $exQuery_Insert = OCIParse($conn->connect_db_oci8(), $strInsert);
                                               if(OCIExecute($exQuery_Insert)){////if(false){
                                                    
                                                } else {
                                                    $data_arr[] = array( 
                                                            "TEMPMINISTRY" => @iconv("tis-620","utf-8", htmlspecialchars($ORG_NAME)), 
                                                            "TEMPORGANIZE" => @iconv("tis-620","utf-8",htmlspecialchars($ORG_NAME_1)), 
                                                            "TEMPDIVISIONNAME" => @iconv("tis-620","utf-8",htmlspecialchars($ORG_NAME_2)), 
                                                            "TEMPORGANIZETYPE" => @iconv("tis-620","utf-8",htmlspecialchars($OT_NAME)), 
                                                            "TEMPPOSITIONNO" => @iconv("tis-620","utf-8",htmlspecialchars($POS_NO)), 
                                                            "TEMPMANAGEPOSITION" => @iconv("tis-620","utf-8",htmlspecialchars($PM_NAME)), 
                                                            "TEMPLINE" => @iconv("tis-620","utf-8",htmlspecialchars($PL_NAME)), 
                                                            "TEMPPOSITIONTYPE" => @iconv("tis-620","utf-8",htmlspecialchars($POSITION_TYPE)), 
                                                            "TEMPLEVEL" => @iconv("tis-620","utf-8",htmlspecialchars($TEMPLEVEL)), 
                                                            "TEMPCLNAME" => @iconv("tis-620","utf-8",htmlspecialchars($CL_NAME)), 
                                                            "TEMPSKILL" => @iconv("tis-620","utf-8",htmlspecialchars($SKILL_NAME)), 
                                                            "TEMPCOUNTRY" => @iconv("tis-620","utf-8",htmlspecialchars($CT_NAME_ORG)), 
                                                            "TEMPPROVINCE" => @iconv("tis-620","utf-8",htmlspecialchars($PV_NAME_ORG)), 
                                                            "TEMPPOSITIONSTATUS" => @iconv("tis-620","utf-8",htmlspecialchars($POS_STATUS)), 
                                                            "TEMPCLASS" => @iconv("tis-620","utf-8",htmlspecialchars($CLASS_NAME)), 
                                                            "TEMPPRENAME" => @iconv("tis-620","utf-8",htmlspecialchars($PN_NAME)), 
                                                            "TEMPFIRSTNAME" => @iconv("tis-620","utf-8",htmlspecialchars($PER_NAME)), 
                                                            "TEMPLASTNAME" => @iconv("tis-620","utf-8",htmlspecialchars($PER_SURNAME)), 
                                                            "TEMPCARDNO" => @iconv("tis-620","utf-8",htmlspecialchars($PER_CARDNO)), 
                                                            "TEMPGENDER" => @iconv("tis-620","utf-8",htmlspecialchars($PER_GENDER_NAME)), 
                                                            "TEMPSTATUSDISABILITY" => @iconv("tis-620","utf-8",htmlspecialchars($PER_DISABILITY_NAME)), 
                                                            "TEMPRELIGION" => @iconv("tis-620","utf-8",htmlspecialchars($RE_NAME)), 
                                                            "TEMPBIRTHDATE" => @iconv("tis-620","utf-8",htmlspecialchars($BIRTHDATE)), 
                                                            "TEMPSALARY" => @iconv("tis-620","utf-8",htmlspecialchars($PER_SALARY)), 
                                                            "TEMPPOSITIONSALARY" => @iconv("tis-620","utf-8",htmlspecialchars($PER_MGTSALARY)), 
                                                            "TEMPEDUCATIONLEVEL" => @iconv("tis-620","utf-8",htmlspecialchars($EL_NAME)), 
                                                            "TEMPEDUCATIONNAME" => @iconv("tis-620","utf-8",htmlspecialchars($EN_NAME)), 
                                                            "TEMPEDUCATIONMAJOR" => @iconv("tis-620","utf-8",htmlspecialchars($EM_NAME)), 
                                                            "TEMPGRADUATED" => @iconv("tis-620","utf-8",htmlspecialchars($INS_NAME)), 
                                                            "TEMPEDUCATIONCOUNTRY" => @iconv("tis-620","utf-8",htmlspecialchars($CT_NAME_EDU)), 
                                                            "TEMPSCHOLARTYPE" => @iconv("tis-620","utf-8",htmlspecialchars($ST_NAME)), 
                                                            "TEMPMOVEMENTTYPE" => @iconv("tis-620","utf-8",htmlspecialchars($MOV_NAME)), 
                                                            "TEMPMOVEMENTDATE" => @iconv("tis-620","utf-8",htmlspecialchars($EFFECTIVEDATE)), 
                                                            "TEMPSTARTDATE" => @iconv("tis-620","utf-8",htmlspecialchars($STARTDATE)), 
                                                            "TEMPFLOWDATE" => @iconv("tis-620","utf-8",htmlspecialchars($FLOWDATE)), 
                                                            "TEMPRESIGNDATE" => @iconv("tis-620","utf-8",htmlspecialchars($RESIGNDATE)), 
                                                            "TEMPPROMOTEDATE" => @iconv("tis-620","utf-8",htmlspecialchars($PROMOTEDATE)), 
                                                            "TEMPDECORATION" => @iconv("tis-620","utf-8",htmlspecialchars($DC_NAME)), 
                                                            "TEMPUNION" => @iconv("tis-620","utf-8",htmlspecialchars($UNION_CODE)), 
                                                            "TEMPRESULT1" => @iconv("tis-620","utf-8",htmlspecialchars($RESULT1)), 
                                                            "TEMPPERCENTSALARY1" => @iconv("tis-620","utf-8",htmlspecialchars($PERCENT_SALARY1)), 
                                                            "TEMPRESULT2" => @iconv("tis-620","utf-8",htmlspecialchars($RESULT2)), 
                                                            "TEMPPERCENTSALARY2" => @iconv("tis-620","utf-8",htmlspecialchars($PERCENT_SALARY2)), 
                                                            "TEMPMPERSPSKILL1" => @iconv("tis-620","utf-8",htmlspecialchars($TMP_SS_CODE)), 
                                                            "TEMPSPERSPSKILL1" => @iconv("tis-620","utf-8",htmlspecialchars($TMP_REF_CODE)), 
                                                            "TEMPSKILLLEVEL1" => @iconv("tis-620","utf-8",htmlspecialchars($LEVELSKILL_NAME)), 
                                                            "TEMPPERSPSKILLDES1" =>@iconv("tis-620","utf-8",htmlspecialchars($SPS_EMPHASIZE)), 
                                                            "TEMPSPS_FLAG1" => @iconv("tis-620","utf-8",htmlspecialchars($SPS_FLAG)), 
                                                            "TEMPMPERSPSKILL2" => @iconv("tis-620","utf-8",htmlspecialchars($TMP_SS_CODE2)), 
                                                            "TEMPSPERSPSKILL2" => @iconv("tis-620","utf-8",htmlspecialchars($TMP_REF_CODE2)), 
                                                            "TEMPSKILLLEVEL2" => @iconv("tis-620","utf-8",htmlspecialchars($LEVELSKILL_NAME2)), 
                                                            "TEMPPERSPSKILLDES2" => @iconv("tis-620","utf-8",htmlspecialchars($SPS_EMPHASIZE2)), 
                                                            "TEMPSPS_FLAG2" => @iconv("tis-620","utf-8",htmlspecialchars($SPS_FLAG2)), 
                                                            "TEMPMPERSPSKILL3" => "''", 
                                                            "TEMPSPERSPSKILL3" => "''", 
                                                            "TEMPSKILLLEVEL3" => "''", 
                                                            "TEMPPERSPSKILLDES3" => "''", 
                                                            "TEMPSPS_FLAG3" => "''",
                                                            "STATUS_ERR" => "ERROR"
                                                        );
                                                    //array_push($push_errdata_tofile, $data_arr);
                                                    $error = OCIError($exQuery_Insert);
                                                    if($error["offset"]){ $data_syt_arr[] = array ("str_err" => $error["message"]);}
                                                    else { $data_syt_arr[] = array ( "str_err" => $error["message"]);}
                                                }
                                            }
                                            $DATA_COUNT_SEQS++;
                                    } // end if
                            } // end if
                    break;
            } // end switch case
        } // end for
        $path_data_err = '../../Excel/tmp/results.txt';
        if(count($data_arr)>0){
            write_errtofile($path_data_err, json_encode($data_arr));
        }
        $path_data_err_syt_arr = '../../Excel/tmp/err_results.txt';
        if(count($data_arr)>0){
            write_errtofile($path_data_err_syt_arr, json_encode($data_syt_arr));
        }
    }
    exit();
   /*  $host = '164.115.146.226';
    *  $database ='ES';
    *  $user = 'coj091161';
    *  $password= 'coj091161';
    *  $port = '1521';
    *
    *  $sql_query = 'select * from per_line ';
    *  $x = '';
    *  $conn = connect_oci8_stand_alone($host, $database, $user, $password, $port);
    *  $conn2 = connect_oci8_stand_alone($host, $database, $user, $password, $port);
    *
    *  $count_data =  $conn->num_row($conn,$sql_query);
    *  $stid = oci_parse($conn2, $sql_query);
    *  OCIExecute($stid);
    *  while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
    *      echo $row[0] . " und " . $row[PL_NAME]   . " count_data = ".$count_data."<br>\n";
    *  }
    *  oci_free_statement($stid);
    *  oci_close($conn2);
    *  die();
    */
    /*function send_message($id, $message, $progress) 
    {
        $d = array('message' => $message , 'progress' => $progress);
        echo json_encode($d) . PHP_EOL;
        //PUSH THE data out by all FORCE POSSIBLE
        ob_flush();
        flush();
    }
    $serverTime = time();
    //LONG RUNNING TASK
    for($i = 0; $i < 10; $i++)
    {
        //Hard work!!
        sleep(1);
        //send status message
        $p = ($i+1)*10;	//Progress
        send_message($serverTime, $p . '% complete. server time: ' . date('h:i:s', time()) , $p); 
    }
    sleep(1);
    send_message($serverTime, 'COMPLETE');*/
    
    /*function GET_TOTAL_DATA (){
        echo 10;
        exit();
    }
    
    for($i=0;$i<10;$i++){
        echo '.';
        ob_flush(); 
        flush();
        sleep(1);
    }
    exit();*/
    //-------------------------------------------------------------------------- GET POST ------------------------------------------------------------------------