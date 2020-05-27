<?php
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=tis-620");
$kf_id = $_GET['KF_ID'];

/*2.1 ผลสำเร็จของงานจริง*/
$cmd = "SELECT 	SUM(A.PG_EVALUATE) AS SUMTOTAL FROM PER_PERFORMANCE_GOALS A, PER_KPI B, PER_PERFORMANCE_REVIEW C WHERE A.KF_ID=".$kf_id." AND A.KPI_ID=B.KPI_ID(+) AND B.PFR_ID=C.PFR_ID(+)";
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
$db_sumtotal=0;
if($data){ //ยึดเวลา ตามรอบ
    $db_sumtotal += $data[0];
}
/*2.2 สมรรถนะที่แสดงจริง*/
$cmd = "SELECT SUM(A.KC_EVALUATE * A.KC_WEIGHT) AS SUMTOTAL FROM PER_KPI_COMPETENCE A, PER_COMPETENCE B WHERE A.KF_ID=".$kf_id." AND A.CP_CODE=B.CP_CODE(+)";
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
if($data){ //ยึดเวลา ตามรอบ
    $db_sumtotal += $data[0];
}
/*ส่วนที่ 4.*/
$cmd = "SELECT COUNT(*) AS SUMTOTAL FROM PER_IPIP WHERE KF_ID=".$kf_id;
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
if($data){ //ยึดเวลา ตามรอบ
    $db_sumtotal += $data[0];
}
/*ส่วนที่ 5*/
$cmd = "SELECT 	COUNT(*) AS SUMTOTAL FROM PER_KPI_FORM WHERE KF_ID=".$kf_id." AND (AGREE_REVIEW1 IS NOT NULL OR DIFF_REVIEW1 IS NOT NULL OR AGREE_REVIEW2 IS NOT NULL OR DIFF_REVIEW2 IS NOT NULL)";
$db_dpis->send_cmd($cmd);
$data = $db_dpis->get_array_array();
if($data){ //ยึดเวลา ตามรอบ
    $db_sumtotal += $data[0];
}
echo $db_sumtotal;
?>
