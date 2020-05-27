<?php
    include("../../php_scripts/db_connect_var.php");
    include("../../php_scripts/connect_database.php");
    if($_POST['DOC_NAME']){ $DOC_NAME = @iconv('UTF-8','TIS-620',$_POST['DOC_NAME']); }
    ini_set("max_execution_time",0);
    set_time_limit(0);
    ini_set("memory_limit","512M");
   
    if($DOC_NAME){
        $cmd = "    SELECT count(*) AS CNT from (
                        SELECT 
                            a.PER_ID
                        FROM PER_POSITIONHIS   a 
                        WHERE  trim(a.POH_DOCNO) =  trim('$DOC_NAME')
                    ) p 
                ";
        $count_data = $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array();
        //$count_data = 1;
        if($count_data){
            echo $data[CNT];
        }else{
            echo '0';
        }
    } else {
        echo '';
    }