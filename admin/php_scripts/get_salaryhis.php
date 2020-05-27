<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$date = $_POST[date_select];
$card_no = $_POST[card_no];
$data_con = save_date($date);
$cmd ="select * from (
    select sah_salary 
    from per_salaryhis 
    where per_cardno ='$card_no'  
    and sah_effectivedate <= '$data_con' 
    ORDER by SAH_EFFECTIVEDATE desc) where ROWNUM = 1";

$db_dpis2->send_cmd($cmd);
$data = $db_dpis2->get_array();		
$old_salary =number_format(trim($data[SAH_SALARY]), 2, '.', ','); //  $data[SAH_SALARY];
echo json_encode($old_salary);

?>