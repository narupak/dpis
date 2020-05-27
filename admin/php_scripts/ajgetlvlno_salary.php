<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$LEVEL_NO = $_GET['LEVEL_NO'];
$cmd = "select LAYER_SALARY_MIN,LAYER_SALARY_MAX,LAYER_SALARY_TEMPUP
        from PER_LAYER 
        where LAYER_TYPE=0 and LEVEL_NO = '".$LEVEL_NO."' and LAYER_NO = 0";
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
if($data){
    $db_layer_salary_min_max = $data[0].','.$data[1];
}else{ //ยึดเวลาที่ 0830
    $db_layer_salary_min_max=',';
}
echo $db_layer_salary_min_max;
?>
