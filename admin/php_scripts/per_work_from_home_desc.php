
<?php
include("php_scripts/session_start.php");
include("php_scripts/function_share.php");
include("php_scripts/function_list.php");	
include("php_scripts/load_per_control.php");

$db_dpis1_desc = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis2_desc = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis3_desc = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis4_desc = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

$urldetail=$_SERVER["REQUEST_URI"];
$query_strdetail = parse_url($urldetail, PHP_URL_QUERY);
if(empty($current_urldetail)){
    $current_urldetail=$query_strdetail;
}

if(trim($WH_ID)){
	/* อัพเดทกรณี เช็คไม่พบงานที่แล้วเสร็จ */
	$StrSQLCHK = "SELECT COUNT(*) AS CNT FROM PER_WORKHOME_DTL where WH_ID = $WH_ID and DO_FLAG=1"; 
	$db_dpis4_desc->send_cmd($StrSQLCHK);
	$dataCHK = $db_dpis4_desc->get_array();
	$CNT_CHK_ADD = $dataCHK[CNT];
}


function get_now($db_dpis){
    $cmd = "SELECT TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:mi:ss') as TIME from DUAL";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
     $UPDATE_DATE = $data[TIME];
    return $UPDATE_DATE;
}


$UPDATE_DATE = get_now($db_dpis);
$GET_DATE_NOW = get_now($db_dpis);
$GET_DATE_NOW_A = explode("-",$GET_DATE_NOW);
$DATE_EX = explode(" ",$GET_DATE_NOW_A[2]);
$DO_DATE =  $DATE_EX[0]."/".$GET_DATE_NOW_A[1]."/".($GET_DATE_NOW_A[0]+543);

if($command == "ADD_DESC" && trim($WH_ID)){
    $WH_ID = trim($WH_ID);
    $cmd = " select max(WHD_ID) as max_id from PER_WORKHOME_DTL ";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $data = array_change_key_case($data, CASE_LOWER);
    $WHD_ID = $data[max_id] + 1;
    
    $DO_DATE_I = ($DO_DATE)?trim($DO_DATE):"";
    $DO_DATES = "";
    if($DO_DATE_I){
        $DO_DATE_A = explode("/",$DO_DATE_I);
        $DO_DATES =  $DO_DATE_A[2].$DO_DATE_A[1].$DO_DATE_A[0];
    }
    $CHK_DO_FLAGS = ($CHK_DO_FLAG)?$CHK_DO_FLAG:0;
    $cmd = " insert into PER_WORKHOME_DTL (  WHD_ID, WH_ID, DO_DATE, DO_DESC, DO_FLAG,
                                             CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_USER ) 
                                    values ( $WHD_ID, $WH_ID, '$DO_DATES', '$TXT_DO_DESC', '$CHK_DO_FLAGS',
                                            '$UPDATE_DATE', $SESS_USERID, '$UPDATE_DATE', $SESS_USERID
                                    ) ";
    $db_dpis->send_cmd($cmd);
    //echo "$cmd";
    if($CHK_DO_FLAGS==1){
        $cmd = " update PER_WORKHOME set WORK_FLAG = 1  where WH_ID=$WH_ID ";
        $db_dpis->send_cmd($cmd);
    }
	
	header("location: per_work_from_home_desc.html?$current_urldetail");
    exit;
    $command="";
    //echo "<pre>".$cmd."<br><br>";
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$WH_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
}

if($command=="UPDATE_DESC" && $WHD_ID && $WH_ID){
    $CHK_DO_FLAGS = ($CHK_DO_FLAG)?$CHK_DO_FLAG:0;
    $cmd = "UPDATE PER_WORKHOME_DTL SET
                DO_DESC = '$TXT_DO_DESC',
                DO_FLAG = '$CHK_DO_FLAGS',
                UPDATE_USER=  $SESS_USERID, 
                UPDATE_DATE=  '$UPDATE_DATE'
            WHERE WHD_ID = $WHD_ID and WH_ID = $WH_ID ";
    $db_dpis->send_cmd($cmd);

    /* อัพเดทกรณีเช็คงานแล้วเสร็จ */
	if($CHK_DO_FLAGS==1){
        $cmd = " update PER_WORKHOME set WORK_FLAG = 1  where WH_ID=$WH_ID ";
        $db_dpis->send_cmd($cmd);
    }
	
	/* อัพเดทกรณีเช็คไม่พบงานที่แล้วเสร็จ */
	$StrSQLUPdate = "SELECT COUNT(*) AS CNT FROM PER_WORKHOME_DTL where WH_ID = $WH_ID and DO_FLAG=1"; 
	$db_dpis->send_cmd($StrSQLUPdate);
	$dataUPdate = $db_dpis->get_array();
	$CNT_CHK_ADD = $dataUPdate[CNT];
	if($CNT_CHK_ADD==0){
        $cmd = " update PER_WORKHOME set WORK_FLAG = 0  where WH_ID=$WH_ID ";
        $db_dpis->send_cmd($cmd);
    }
	$UPD = "";
    header("location: per_work_from_home_desc.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&MENU_ID_LV3=$MENU_ID_LV3&WH_ID=$WH_ID&PER_ID_DESC=$PER_ID_DESC");
    exit;
    //echo "UPDATE=><pre>".$cmd;
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติด้านพฤติกรรม [$PER_ID : $BEH_ID]");
    $command="";
}

if($command=="DELETE_DESC" && $WHD_ID && $WH_ID){
    $cmd = " delete from PER_WORKHOME_DTL where WHD_ID=$WHD_ID and WH_ID=$WH_ID ";
    $db_dpis->send_cmd($cmd);
    //echo $cmd;
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$PER_ID : $WH_ID]");
	header("location: per_work_from_home_desc.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&MENU_ID_LV3=$MENU_ID_LV3&WH_ID=$WH_ID&PER_ID_DESC=$PER_ID_DESC");
    exit;
    $command="";
} // end if

if(($UPD && $WHD_ID && $WH_ID) || ($VIEW && $WHD_ID && $WH_ID)){
    $cmd = " SELECT * FROM  PER_WORKHOME_DTL
                where 	WHD_ID=$WHD_ID and WH_ID = $WH_ID ";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $DO_DATE = substr($data[DO_DATE],6,2).'/'.substr($data[DO_DATE],4,2).'/'.substr($data[DO_DATE],0,4);
    $TXT_DO_DESC = $data[DO_DESC];
    $CHK_DO_FLAG = $data[DO_FLAG];
}
    
if ($command == "CANCEL" && !$WHD_ID) {
    $WHD_ID = "";
    $TXT_DO_DESC = "";
    $CHK_DO_FLAG = "";
    $command="";
	$UPD = "";
	header("location: per_work_from_home_desc.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&MENU_ID_LV3=$MENU_ID_LV3&WH_ID=$WH_ID&PER_ID_DESC=$PER_ID_DESC");
    exit;
}

if( (!$UPD && !$VIEW) ){
    $WHD_ID = "";
    $TXT_DO_DESC = "";
    $CHK_DO_FLAG = "";
}