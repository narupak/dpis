<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
header("content-type: application/x-javascript; charset=TIS-620");
 $cmdCycle = "select to_char(sysdate,'YYYY-MM-DD HH24:MI:SS') from dual";
    $db_dpis->send_cmd($cmdCycle);
    $datasysdate = $db_dpis->get_array_array();
    echo $datasysdate[0];
?>
