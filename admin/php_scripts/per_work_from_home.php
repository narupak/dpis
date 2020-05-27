
<?php
include("php_scripts/session_start.php");
include("php_scripts/function_share.php");
include("php_scripts/function_list.php");	
include("php_scripts/load_per_control.php");

$db_dpis1x = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$db_dpisDetail = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
$url=$_SERVER["REQUEST_URI"];
$query_str = parse_url($url, PHP_URL_QUERY);
if(empty($current_url)){
    $current_url=$query_str;
}
switch($CTRL_TYPE){
    case 2 :
        $search_pv_code = $PROVINCE_CODE;
        $search_pv_name = $PROVINCE_NAME;
        $PROVINCE_CODE = $PROVINCE_CODE;
        $PROVINCE_NAME = $PROVINCE_NAME;
    break;
    case 3 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
    break;
    case 4 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $search_department_id = $DEPARTMENT_ID;
        $search_department_name = $DEPARTMENT_NAME;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
        $MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
        $MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
    break;
} // end switch case


switch($SESS_USERGROUP_LEVEL){								
    case 2 :
        $search_pv_code = $PROVINCE_CODE;
        $search_pv_name = $PROVINCE_NAME;
        $PROVINCE_CODE = $PROVINCE_CODE;
        $PROVINCE_NAME = $PROVINCE_NAME;
    break;
    case 3 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
    break;
    case 4 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $search_department_id = $DEPARTMENT_ID;
        $search_department_name = $DEPARTMENT_NAME;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
        $MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
        $MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
    break;
    case 5 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $search_department_id = $DEPARTMENT_ID;
        $search_department_name = $DEPARTMENT_NAME;
        $search_org_id = $ORG_ID;
        $search_org_name = $ORG_NAME;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
        $MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
        $MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
        $MAIN_ORG_ID = $ORG_ID;
        $MAIN_ORG_NAME = $ORG_NAME;
    break;
    case 6 :
        $search_ministry_id = $MINISTRY_ID;
        $search_ministry_name = $MINISTRY_NAME;
        $search_department_id = $DEPARTMENT_ID;
        $search_department_name = $DEPARTMENT_NAME;
        $search_org_id = $ORG_ID;
        $search_org_name = $ORG_NAME;
        $search_org_id_1 = $ORG_ID_1;
        $search_org_name_1 = $ORG_NAME_1;
        $MAIN_MINISTRY_ID = $MINISTRY_ID;
        $MAIN_MINISTRY_NAME = $MINISTRY_NAME;
        $MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
        $MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
        $MAIN_ORG_ID = $ORG_ID;
        $MAIN_ORG_NAME = $ORG_NAME;
    break;
} // end switch case

function get_now($db_dpis){
    $cmd = "SELECT TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:mi:ss') as TIME from DUAL";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
     $UPDATE_DATE = $data[TIME];
    return $UPDATE_DATE;
}

$UPDATE_DATE = get_now($db_dpis);
$DATENOWS = get_now($db_dpis);

$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
$db->send_cmd($cmd);
//	$db->show_error();
$data = $db->get_array();
$data = array_change_key_case($data, CASE_LOWER);
$SESS_GROUPCODE = $data[code];


if(($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3 && trim($SESS_PER_ID)){
    /* ทดสอบ LEFT JOIN 17/02/2017 */                   
    $cmd = " select a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO, b.LEVEL_NAME, b.POSITION_LEVEL, 
                    a.PER_SALARY, a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID , a.DEPARTMENT_ID, a.OT_CODE, a.PER_PROBATION_FLAG,
                    a.PAY_ID
            from PER_PERSONAL  a
            LEFT JOIN PER_LEVEL b ON (trim(a.LEVEL_NO)=trim(b.LEVEL_NO))
            where a.PER_ID=$SESS_PER_ID ";                   

    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $PER_ID = $SESS_PER_ID;
    $PN_CODE = trim($data[PN_CODE]);
    $PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
    $PER_CARDNO = trim($data[PER_CARDNO]);
    $LEVEL_NO = trim($data[LEVEL_NO]);
    $POSITION_LEVEL = $data[POSITION_LEVEL];
    //$MY_PER_TYPE = $data[PER_TYPE];//เพิ่มเติม
    $PER_TYPE = $data[PER_TYPE];

    $PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
    $OT_CODE = trim($data[OT_CODE]);

    if($PER_TYPE == 1){
            $POS_ID = $data[POS_ID];
            $cmd = " select  a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE 
                    from  PER_POSITION a, PER_LINE b, PER_ORG d
                    where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $ORG_ID =  trim($data[ORG_ID]);
            $ORG_NAME = trim($data[ORG_NAME]);
            $PL_NAME = trim($data[PL_NAME]);
    }elseif($PER_TYPE == 2){
            $POEM_ID = $data[POEM_ID];
            $cmd = " select  a.ORG_ID,b.PN_NAME, c.ORG_NAME 
                    from    PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
                    where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $PL_NAME = trim($data[PN_NAME]);
            $ORG_ID =  trim($data[ORG_ID]);
            $ORG_NAME = trim($data[ORG_NAME]);
    }elseif($PER_TYPE==3){
            $POEMS_ID = $data[POEMS_ID];
            $cmd = " select  a.ORG_ID,b.EP_NAME, c.ORG_NAME 
                     from  PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
                     where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $PL_NAME = trim($data[EP_NAME]);
            $ORG_ID =  trim($data[ORG_ID]);
            $ORG_NAME = trim($data[ORG_NAME]);
    } // end if

    /**/
    if($PER_TYPE==1) 
        $cmd = " select	 ORG_ID, ORG_ID_1, PL_NAME, PM_CODE from PER_POSITION a, PER_LINE b 
        where POS_ID = $POS_ID and a.PL_CODE=b.PL_CODE ";		
    elseif($PER_TYPE==2) 
        $cmd = " select	 ORG_ID, ORG_ID_1, PN_NAME as PL_NAME from PER_POS_EMP a, PER_POS_NAME b 
        where POEM_ID = $POEM_ID and a.PN_CODE=b.PN_CODE ";		
    elseif($PER_TYPE==3) 
        $cmd = " select	 ORG_ID, ORG_ID_1, EP_NAME as PL_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
        where POEMS_ID = $POEMS_ID and a.EP_CODE=b.EP_CODE ";		
    $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
    $data = $db_dpis->get_array();
    $ORG_ID = $data[ORG_ID];
    $ORG_ID_1 = $data[ORG_ID_1];
    if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
    $PL_NAME = trim($data[PL_NAME]);
    $PM_CODE = trim($data[PM_CODE]);
    $PM_NAME='';
    if ($PM_CODE) {
        $cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
        $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array();
        $PM_NAME = trim($data[PM_NAME]);
    }

    $cmd = " select POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $LEVEL_NAME = $data[POSITION_LEVEL];

    $PER_LINE = $PL_NAME.' '.$LEVEL_NAME;
    $PER_MG = $PM_NAME;     
}

if($command == "ADD" && trim($PER_ID)){
    
    $cmd = " select max(WH_ID) as max_id from PER_WORKHOME ";
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $data = array_change_key_case($data, CASE_LOWER);
    $WH_ID = $data[max_id] + 1;
    
    $WORKHOME_END_DATE_I = ($WORKHOME_END_DATE)?$WORKHOME_END_DATE:"";
    if($WORKHOME_END_DATE_I){
        $WORKHOME_END_DATE_A = explode("/",$WORKHOME_END_DATE_I);
        $WORKHOME_END =  $WORKHOME_END_DATE_A[2].$WORKHOME_END_DATE_A[1].$WORKHOME_END_DATE_A[0];
    }
    
    $cmd = " insert into PER_WORKHOME ( WH_ID, PER_ID, PER_CARDNO, PER_ASSID_1, PER_ASSID_2, WORK_DESC, GOAL_DESC, END_DATE,
                                        PER_NAME, PER_LINE, PER_MG, PERASS_NAME_1, PERASS_LINE_1, PERASS_MG_1, PERASS_NAME_2, 
                                        PERASS_LINE_2, PERASS_MG_2, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_USER ) 
                                    values ($WH_ID, '$PER_ID', '$PER_CARDNO', '$PER_ASSID_1', '$PER_ASSID_2', '$TXT_WORKDESC', '$TXT_GOALDESC', '$WORKHOME_END',
                                        '$PER_NAME', '$PER_LINE', '$PER_MG', '$PERASS_NAME_1', '$PERASS_LINE_1', '$PERASS_MG_1', '$PERASS_NAME_2',
                                        '$PERASS_LINE_2', '$PERASS_MG_2', '$UPDATE_DATE', $SESS_USERID, '$UPDATE_DATE', $SESS_USERID
                                    ) ";
    $db_dpis->send_cmd($cmd);
    //echo "<pre>".$cmd."<br><br>";
    header("location: per_work_from_home.html?$current_url");
    exit;
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$WH_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
    $command="";
}

if($command=="UPDATE" && $PER_ID && $WH_ID){
    $WORKHOME_END_DATE_I = ($WORKHOME_END_DATE)?$WORKHOME_END_DATE:""; $WORKHOME_END = "";
    if($WORKHOME_END_DATE_I){
        $WORKHOME_END_DATE_A = explode("/",$WORKHOME_END_DATE_I); 
        $WORKHOME_END =  $WORKHOME_END_DATE_A[2].$WORKHOME_END_DATE_A[1].$WORKHOME_END_DATE_A[0];
    }
    if(!$PER_ASSID_1){
        $ASSIGN_FLAG_1_HIDDEN = "";
        $ASSIGN_DATE_1 = "";
    }
    if($ASSIGN_FLAG_1_HIDDEN && !$ASSIGN_DATE_1){
        $ASSIGN_DATE_1 = $UPDATE_DATE;
    }
    if($ASSIGN_FLAG_2_HIDDEN && !$ASSIGN_DATE_2){
        $ASSIGN_DATE_2 = $UPDATE_DATE;
    }
   
    $cmd = "UPDATE PER_WORKHOME SET
                PER_ASSID_1 =  '$PER_ASSID_1',
                PER_ASSID_2 =  '$PER_ASSID_2',
                WORK_DESC =  '$TXT_WORKDESC',
                GOAL_DESC =  '$TXT_GOALDESC',
                END_DATE =  '$WORKHOME_END',
                PERASS_NAME_1 =  '$PERASS_NAME_1',
                PERASS_LINE_1 =  '$PERASS_LINE_1',
                PERASS_MG_1 =  '$PERASS_MG_1',
                PERASS_NAME_2 =  '$PERASS_NAME_2',
                PERASS_LINE_2 =  '$PERASS_LINE_2',
                PERASS_MG_2 =  '$PERASS_MG_2',
                ASSIGN_FLAG_1 =  '$ASSIGN_FLAG_1_HIDDEN',
                ASSIGN_FLAG_2 =  '$ASSIGN_FLAG_2_HIDDEN',
                ASSIGN_DATE_1 =  '$ASSIGN_DATE_1',
                ASSIGN_DATE_2 =  '$ASSIGN_DATE_2',
                UPDATE_USER=  $SESS_USERID, 
                UPDATE_DATE=  '$UPDATE_DATE'
            WHERE WH_ID = $WH_ID ";
    $db_dpis->send_cmd($cmd);
    //echo "UPDATE=><pre>".$cmd;
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติด้านพฤติกรรม [$PER_ID : $BEH_ID]");
    $command="";
    header("location: per_work_from_home.html?$current_url");
    exit;
}

if($command=="DELETE" && $PER_ID && $WH_ID){
    $cmd = " delete from PER_WORKHOME_DTL where WH_ID=$WH_ID ";
    $db_dpis->send_cmd($cmd);
    
    $cmd2 = " delete from PER_WORKHOME where PER_ID=$PER_ID and WH_ID=$WH_ID ";
    $db_dpis->send_cmd($cmd2); 
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$PER_ID : $WH_ID]");
    $command="";
} // end if

if(($UPD && $PER_ID_EDIT && $WH_ID) || ($VIEW && $PER_ID_EDIT && $WH_ID)){
    $cmd = " SELECT * FROM  
                    PER_WORKHOME
                where 	PER_ID=$PER_ID_EDIT and WH_ID = $WH_ID ";
    //echo $cmd;
    $db_dpis->send_cmd($cmd);
    $data = $db_dpis->get_array();
    $WH_ID = $data[WH_ID];
    $PER_ID = $data[PER_ID];
    $PER_CARDNO = $data[PER_CARDNO];
    $PER_ASSID_1 = $data[PER_ASSID_1];
    $PER_ASSID_2 = $data[PER_ASSID_2];
    $TXT_WORKDESC = $data[WORK_DESC];
    $TXT_GOALDESC = $data[GOAL_DESC];
    $WORKHOME_END_DATE = substr($data[END_DATE],6,2).'/'.substr($data[END_DATE],4,2).'/'.substr($data[END_DATE],0,4);
    $PER_NAME = $data[PER_NAME];
    $PER_LINE = $data[PER_LINE];
    $PER_MG = $data[PER_MG];
    $PERASS_NAME_1 = $data[PERASS_NAME_1];
    $PERASS_LINE_1 = $data[PERASS_LINE_1];
    $PERASS_MG_1 = $data[PERASS_MG_1];
    $PERASS_NAME_2 = $data[PERASS_NAME_2];
    $PERASS_LINE_2 = $data[PERASS_LINE_2];
    $PERASS_MG_2 = $data[PERASS_MG_2];
    $ASSIGN_FLAG_1  = $data[ASSIGN_FLAG_1];
    $ASSIGN_FLAG_2  = $data[ASSIGN_FLAG_2];
    $ASSIGN_DATE_1 = $data[ASSIGN_DATE_1];
    $ASSIGN_DATE_2 = $data[ASSIGN_DATE_2];
    
    $UPDATE_USER = $data[UPDATE_USER];

    $SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
    $cmd ="select TITLENAME, FULLNAME,UPDATE_DATE from USER_DETAIL where id =  $UPDATE_USER ";
    $db->send_cmd($cmd);
    $data2 = $db->get_array();
    $SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
    
    if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
        $cmdx = " select a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID 
            from PER_PERSONAL  a
            where a.PER_ID=$PER_ID ";  
        $db_dpis2->send_cmd($cmdx);
        $datax = $db_dpis2->get_array();
        $PER_TYPE = $datax[PER_TYPE];
        if($PER_TYPE == 1){
                $POS_ID = $datax[POS_ID];
                $cmd = " select  a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE 
                        from  PER_POSITION a, PER_LINE b, PER_ORG d
                        where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
                $db_dpis3->send_cmd($cmd);
                $datat = $db_dpis3->get_array();
                $ORG_ID =  trim($datat[ORG_ID]);
                $ORG_NAME = trim($datat[ORG_NAME]);
        }elseif($PER_TYPE == 2){
                $POEM_ID = $datax[POEM_ID];
                $cmd = " select  a.ORG_ID,b.PN_NAME, c.ORG_NAME 
                        from    PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
                        where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
                $db_dpis3->send_cmd($cmd);
                $datat = $db_dpis3->get_array();
                $ORG_ID =  trim($datat[ORG_ID]);
                $ORG_NAME = trim($datat[ORG_NAME]);
        }elseif($PER_TYPE==3){
                $POEMS_ID = $datax[POEMS_ID];
                $cmd = " select  a.ORG_ID,b.EP_NAME, c.ORG_NAME 
                         from  PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
                         where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
                $db_dpis3->send_cmd($cmd);
                $datat = $db_dpis3->get_array();
                $ORG_ID =  trim($datat[ORG_ID]);
                $ORG_NAME = trim($datat[ORG_NAME]);
        }
    } // end if
 } // end if
 
if ($command == "SETFLAG1") {
    
    if( count($list_chk_assid_1) > 0 ){
        $setflag =  implode(",",$list_chk_assid_1);
        $cmd = " update PER_WORKHOME set ASSIGN_FLAG_1 = 1 , ASSIGN_DATE_1='$UPDATE_DATE' where WH_ID in (".stripslashes($setflag).") ";
        $db_dpis2->send_cmd($cmd);
        //echo "$cmd<br>";
    }
    $command="";
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลการอนุญาตให้เห็นคะแนน [KF_ID : ".trim(stripslashes($setflagshow))."]");
    header("location: per_work_from_home.html?$current_url");
    exit;
}

if ($command == "SETFLAG2") {
   if( count($list_chk_assid_2) > 0 ){
        $setflag2 =  implode(",",$list_chk_assid_2);
        $cmd = " update PER_WORKHOME set ASSIGN_FLAG_2 = 1 , ASSIGN_DATE_2='$UPDATE_DATE' where WH_ID in (".stripslashes($setflag2).") ";
        $db_dpis2->send_cmd($cmd);
    }
    $command="";
    header("location: per_work_from_home.html?$current_url");
    exit;
    //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลการอนุญาตให้เห็นคะแนน [KF_ID : ".trim(stripslashes($setflagshow))."]");
}
 
if ($command == "CANCEL" && !$WH_ID && !$SESS_PER_ID) {
    $PER_ID = "";
    $PER_ID_EDIT = "";
    $PER_NAME = "";
    $ORG_ID = "";
    $ORG_NAME = "";
    $UPD = "";
    $command="";
}

if( (!$UPD && !$VIEW) ){
    $WH_ID = "";
    $PER_ID_EDIT = "";
    $PER_ASSID_1 = "";
    $PER_ASSID_2 = "";
    $TXT_WORKDESC = "";
    $TXT_GOALDESC = "";
    $WORKHOME_END_DATE = "";
    $PERASS_NAME_1 = "";
    $PERASS_LINE_1 = "";
    $PERASS_MG_1 = "";
    $PERASS_NAME_2 = "";
    $PERASS_LINE_2 = "";
    $PERASS_MG_2 = "";
    
    $ASSIGN_FLAG_1 = "";
    $ASSIGN_DATE_1 = "";
    $ASSIGN_FLAG_2 = "";
    $ASSIGN_DATE_2 ="";
    if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
        $PER_ID = "";
        $PER_ID_EDIT = "";
        $PER_CARDNO = "";
        $PER_NAME = "";
        $PER_LINE = "";
        $PER_MG = "";
        $ORG_NAME = "";
        $ORG_ID = "";
    } // end if
}
    