<?php
    function connect_oci8_test ($host, $database, $user, $password, $port){
        if (!$port) $port = 1521;
        if ($database=="misusers") $port = 1522;

        $databases = "(DESCRIPTION =
                            (ADDRESS =
                                (PROTOCOL = TCP)
                                (HOST = $host)
                                (PORT = $port)
                            )
                      (CONNECT_DATA = (SERVICE_NAME = $database))
                    )";

        $link_con = OCIPLogon ($user, $password, $databases);
        return $link_con;
    }

    function num_row($link_con,$sql_queryx){
        $sql_query = $sql_queryx;
        $result = OCIParse($link_con, $sql_query);
        $ERROR = OCIExecute($result, OCI_COMMIT_ON_SUCCESS);
        $columns = OCINumCols($result);
        $numrows = 0;
        while (OCIFetchInto($result, $row, OCI_ASSOC)) {
                $numrows++;
        }
        oci_free_statement($sql_queryx);
        oci_close($link_con);
        return $numrows;

    }

    include("../../php_scripts/db_connect_var.php");
   /*  $host = '164.115.146.226';
    *  $database ='ES';
    *  $user = 'coj091161';
    *  $password= 'coj091161';
    *  $port = '1521';
    *
    *
    *  $sql_query = 'select * from per_line ';
    *  $x = '';
    *  $conn = connect_oci8_test($host, $database, $user, $password, $port);
    *  $conn2 = connect_oci8_test($host, $database, $user, $password, $port);
    *
    *
    *  $count_data =  num_row($conn,$sql_query);
    *  $stid = oci_parse($conn2, $sql_query);
    *  oci_execute($stid);
    *  while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
    *      echo $row[0] . " und " . $row[PL_NAME]   . " count_data = ".$count_data."<br>\n";
    *  }
    *  oci_free_statement($stid);
    *  oci_close($conn2);
    *  die();
    */
    $intWorkDay = 0;
    if($_POST['KF_START_DATE_1']){ $KF_START_DATE_1 = @iconv('UTF-8','TIS-620',$_POST['KF_START_DATE_1']); }
    if($_POST['KF_END_DATE_2']){ $KF_END_DATE_2 = @iconv('UTF-8','TIS-620',$_POST['KF_END_DATE_2']); }
    
    ini_set("max_execution_time",0);
    set_time_limit(0);
    ini_set("memory_limit","512M");
    //ini_set("session.gc_maxlifetime",60*60*24);
    $db_dpis  = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
    $db_dpis1 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
    $db_dpis2 = connect_oci8_test($db_host, $db_name, $db_user, $db_pwd, $port);
   
    function CheckPublicHoliday($YYYY_MM_DD){
        global $db_dpis;
        $cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$YYYY_MM_DD' ";
        $IS_HOLIDAY = num_row($db_dpis,$cmd);
        if(!$IS_HOLIDAY){
            return false;
        }else{
            return true;
        }
    }

    $arr_KF_STARTDATE = explode("/",$KF_START_DATE_1);
    $arr_KF_END = explode("/",$KF_END_DATE_2);
    
    $tmp_KF_STARTDATE = ($arr_KF_STARTDATE[2]-543)."-".$arr_KF_STARTDATE[1]."-".$arr_KF_STARTDATE[0];
    $tmp_KF_END = ($arr_KF_END[2]-543)."-".$arr_KF_END[1]."-".$arr_KF_END[0];
    //---------------------------------------------นับวันทำการ-----------------------------------------------------------
    if(trim($tmp_KF_STARTDATE) && trim($tmp_KF_END)){
        $strStartDate = $tmp_KF_STARTDATE;//"2011-08-01";
        $strEndDate = $tmp_KF_END;//"2011-08-15";
        //echo $strStartDate.'==='.$strStartDate;
        $intWorkDay = 0;
        $intHoliday = 0;
        $intPublicHoliday = 0;
        $intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate))/  ( 60 * 60 * 24 )) + 1; 

        while (strtotime($strStartDate) <= strtotime($strEndDate)) {
            $DayOfWeek = date("w", strtotime($strStartDate));
            if($DayOfWeek == 0 or $DayOfWeek ==6){  // 0 = Sunday, 6 = Saturday;
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
        $intWorkDay='';
    }
    
    if($intWorkDay){
        echo $intWorkDay;
    } else {
        echo 0;
    }
    