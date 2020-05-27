<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=tis-620");
$per_id = $_GET['PER_ID'];
$abs_startdate = $_GET['ABS_STARTDATE']; //dd/mm/yyyy
$abs_startdate_arr = explode('/', $abs_startdate);
$abs_startdate = ($abs_startdate_arr[2]-543).'-'.$abs_startdate_arr[1].'-'.$abs_startdate_arr[0];

$cmd = "select per_startdate from per_personal where per_id=".$per_id;
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
if($data){ //ยึดเวลา ตามรอบ
    $db_per_startdate = $data[0];
}else{ //ยึดเวลาที่ 0830
    $db_per_startdate='';
}
if($db_per_startdate==''){
    echo '';
}else{
    //$date_diff = date_difference($abs_startdate, $db_per_startdate, "full");
    $date_diff = date_vacation($abs_startdate, $db_per_startdate, "");
   
    $arr_temp = explode(" ", $date_diff);
    $Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
    echo $Governor_Age;
}

?>
