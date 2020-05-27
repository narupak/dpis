<?
include("../php_scripts/connect_database.php");
/*cdgs*/
include("php_scripts/load_per_control.php");	
// include("php_scripts/function_gen_user.php");
/*cdgs*/

if ($DPISDB)
include("php_scripts/function_share.php");
include("php_scripts/function_manage_login.php");

$phpversion = phpversion();
//	echo 'Current PHP version: ' . $phpversion . " (".($phpversion > '5.4').")<br>";
if ($phpversion >= '5.4')
    include("php_scripts/add_session_func_v54.php");

    
  
/* disabled usergroup=2*/
$sqlchk=" SELECT GROUP_ACTIVE FROM USER_GROUP WHERE ID=2";
$db->send_cmd($sqlchk);
$dataG2 = $db->get_array();
if($dataG2[GROUP_ACTIVE]==0){
    $sql = " UPDATE user_detail SET user_flag='Y' WHERE group_id=2 ";$db->send_cmd($sql);
    $sql = " UPDATE USER_GROUP SET group_active=1 WHERE id=2 ";$db->send_cmd($sql);
}

$sqlchk="SELECT GROUP_ACTIVE FROM USER_GROUP WHERE ID=2";
$db_dpis->send_cmd($sqlchk);
$dataG22 = $db_dpis->get_array();
if($dataG22[GROUP_ACTIVE]==0){
    $sql = " UPDATE user_detail SET user_flag='Y' WHERE group_id=2 ";$db_dpis->send_cmd($sql);
    $sql = " UPDATE USER_GROUP SET group_active=1 WHERE id=2 ";$db_dpis->send_cmd($sql);
}



/* End*/    
    
//echo md5('19072524'); //3219900120577
//echo '<br>';
//echo md5('3100500428895'); 
//echo '<br>';
//	echo "1..username=$username<br>";
$username = str_replace("/", "", $username);
$username = str_replace("\\", "", $username);
$username = str_replace("\"", "", $username);
$username = str_replace("'", "", $username);
//	echo "2..username=$username<br>";

$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

function getOS() { 

    global $user_agent;

    $os_platform    =   "Unknown OS Platform";

    $os_array       =   array(
                            '/windows nt 10/i'     =>  'WIN', /*Windows 10*/
                            '/windows nt 6.3/i'     =>  'WIN', /*'Windows 8.1',*/
                            '/windows nt 6.2/i'     =>  'WIN', /*'Windows 8',*/
                            '/windows nt 6.1/i'     =>  'WIN', /*'Windows 7',*/
                            '/windows nt 6.0/i'     =>  'WIN', /*'Windows Vista',*/
                            '/windows nt 5.2/i'     =>  'WIN', /*'Windows Server 2003/XP x64',*/
                            '/windows nt 5.1/i'     =>  'WIN', /*'Windows XP',*/
                            '/windows xp/i'         =>  'WIN', /*'Windows XP',*/
                            '/windows nt 5.0/i'     =>  'WIN', /*'Windows 2000',*/
                            '/windows me/i'         =>  'WIN', /*'Windows ME',*/
                            '/win98/i'              =>  'WIN', /*'Windows 98',*/
                            '/win95/i'              =>  'WIN', /*'Windows 95',*/
                            '/win16/i'              =>  'WIN', /*'Windows 3.11',*/
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
    foreach ($os_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }   
    return $os_platform;
}


if ($db_type == "oci8"){
    $cmd = " SELECT DISTINCT to_char(sysdate,'mm/dd/YYYY') as datedef, to_char(sysdate,'HH24:MI:SS') as timedef FROM dual ";
    $db->send_cmd($cmd);
    $dataSet = $db->get_array_array();

    if(getOS()=='WIN'){
        //exec('../php_scripts/setdatetimec.exe');
        //exec('date '.$dataSet[0]);
        //exec('time '.$dataSet[1]);
    }else{
        //exec('date +%m/%d/%Y -s '.$dataSet[0]);
        //exec('date +%T -s '.$dataSet[1]);
    }
    
}else{
    $cmd = " select DATE_FORMAT(NOW(),'%m/%d/%Y') as datedef ,DATE_FORMAT(NOW(),'%k:%i:%s') as timedef  ";
    $db->send_cmd($cmd);
    $dataSet = $db->get_array();
    if(getOS()=='WIN'){
        //exec('../php_scripts/setdatetimec.exe');
    }else{
        //exec('date +%m/%d/%Y -s '.$dataSet[datedef]);
        //exec('date +%T -s '.$dataSet[timedef]);
    }
}

//die($dataSet[0].$dataSet[1]);

$cmd = " SELECT group_per_type  FROM user_group ";
/*,to_char(sysdate,'mm/dd/YYYY') as datedef,
        to_char(sysdate,'HH24:MI:SS') as timedef */
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    
    if ($db_type == "odbc")
        $cmd = " ALTER TABLE user_group ADD group_per_type SINGLE NULL ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_group ADD group_per_type NUMBER(1) NULL ";
    
    elseif ($db_type == "mysql")
        $cmd = " ALTER TABLE user_group ADD group_per_type SMALLINT(1) NULL ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT group_org_structure FROM user_group ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    if ($db_type == "odbc")
        $cmd = " ALTER TABLE user_group ADD group_org_structure SINGLE NULL ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_group ADD group_org_structure NUMBER(1) NULL ";
    elseif ($db_type == "mysql")
        $cmd = " ALTER TABLE user_group ADD group_org_structure SMALLINT(1) NULL ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT group_seq_no FROM user_group ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    if ($db_type == "odbc")
        $cmd = " ALTER TABLE user_group ADD group_seq_no INTEGER2 ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_group ADD group_seq_no NUMBER(5) ";
    elseif ($db_type == "mysql")
        $cmd = " ALTER TABLE user_group ADD group_seq_no SMALLINT(5) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT group_active FROM user_group ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    if ($db_type == "odbc")
        $cmd = " ALTER TABLE user_group ADD group_active SINGLE NULL ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_group ADD group_active NUMBER(1) NULL ";
    elseif ($db_type == "mysql")
        $cmd = " ALTER TABLE user_group ADD group_active SMALLINT(1) NULL ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " UPDATE user_group SET group_active = 1 WHERE group_active is NULL ";
$db->send_cmd($cmd);
//$db->show_error();

$cmd = " SELECT level_no_list FROM user_group ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    if ($db_type == "odbc" || $db_type == "mysql")
        $cmd = " ALTER TABLE user_group ADD level_no_list varchar(255) ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_group ADD level_no_list varchar2(255) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT password_last_update FROM user_detail ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    if ($db_type == "odbc" || $db_type == "mysql")
        $cmd = " ALTER TABLE user_detail ADD password_last_update varchar(19) ";
    elseif ($db_type == "oci8")
        $cmd = " ALTER TABLE user_detail ADD password_last_update varchar2(19) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT can_confirm FROM user_privilege ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    $cmd = " ALTER TABLE user_privilege ADD can_confirm char(1) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT can_audit FROM user_privilege ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    $cmd = " ALTER TABLE user_privilege ADD can_audit char(1) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

$cmd = " SELECT can_attach FROM user_privilege ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    $cmd = " ALTER TABLE user_privilege ADD can_attach char(1) ";
    $db->send_cmd($cmd);
    //	$db->show_error();
}

$cmd = " SELECT titlename FROM user_detail ";
$count_data = $db->send_cmd($cmd);
//$db->show_error();
if (!$count_data) {
    $cmd = " ALTER TABLE user_detail ADD titlename varchar(50) ";
    $db->send_cmd($cmd);
    //$db->show_error();
}

// By Kittiphat 
if($db_type == "oci8"){
    $cmdChk=" SELECT COUNT(COLUMN_NAME) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME='PER_PERSONAL' AND UPPER(COLUMN_NAME) IN('PER_OT_FLAG')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
            $cmdA=" ALTER TABLE PER_PERSONAL ADD PER_OT_FLAG NUMBER(10,0) ";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);

    }else{
		$cmdChk="SELECT DATA_PRECISION FROM USER_TAB_COLS  WHERE TABLE_NAME = 'PER_PERSONAL' AND  	UPPER(COLUMN_NAME) IN ('PER_OT_FLAG')";
		$db_dpis->send_cmd($cmdChk);
		$dataChk = $db_dpis->get_array();
		if($dataChk[DATA_PRECISION] < 10){
			$cmdA=" ALTER TABLE PER_PERSONAL MODIFY PER_OT_FLAG NUMBER(10,0)";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);
		}
	}
	
	// ��¡��ԡ�ѹ�� By Kittiphat  08/02/2561
	$cmdChk=" SELECT COUNT(COLUMN_NAME) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME='PER_ABSENT' AND UPPER(COLUMN_NAME) IN('ORI_APPROVE_DATE','ORI_APPROVE_PER_ID','CANCEL_DATE','CANCEL_BY')";
    $db_dpis->send_cmd($cmdChk);
    $dataChk = $db_dpis->get_array();
    if($dataChk[CNT]=="0"){
            $cmdA=" ALTER TABLE PER_ABSENT ADD (
			                    ORI_APPROVE_DATE varchar2(19),
								ORI_APPROVE_PER_ID number(10),
								CANCEL_DATE varchar2(19),
								CANCEL_BY number(10)
							) ";
            $db_dpis->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db_dpis->send_cmd($cmdA);

    }
    
    //���ҧ Table 2018/02/03  �ʡ������ѵ�� ����Ǩ�ͺ�ѹ���������ö��Ǩ�ͺ��
    $cmdChk2 = "SELECT count(*)AS CNT FROM user_tables WHERE  TABLE_NAME = 'ta_per_audit'";
    $db_dpis->send_cmd($cmdChk2);
    $dataChk2 = $db_dpis->get_array();
    if($dataChk2[CNT]=="0"){
        $cmdA = "create table ta_per_audit(
                            per_cardno	varchar2(13)  not null,
                            department_id  number(10)  not null,
                            org_ass_id	number(10)  not null,
                            org_lower1	number(10),
                            org_lower2	number(10),
                            org_lower3	number(10),
                            org_lower4	number(10),
                            org_lower5	number(10),
                            create_user	number(11),
                            create_date	varchar2(19)		
                        )";

        $db_dpis->send_cmd($cmdA);
        $cmdA = 'COMMIT';
        $db_dpis->send_cmd($cmdA);
        //echo 'no table => create table<br>';
    }
    
    // chk ����ա���� HRG �����ѧ
    $cmd = "select CODE FROM user_group WHERE CODE = 'HRG' ";
    $db_dpis->send_cmd($cmd);
    $cnt_data = $db_dpis->get_array();
    
    //����� HRG 
    if(!$cnt_data){
       $cmd = " select ORG_ID from PER_CONTROL "; //	�ҡ�� �ҡ�ҹ�����ŷ�������������
		$db_dpis->send_cmd($cmd);	
		$data = $db_dpis->get_array();
		$ORG_ID_TMP = $data[ORG_ID];
			
        $cmdM = "SELECT MAX(ID) AS MAXOFID FROM user_group";
            $db_dpis->send_cmd($cmdM);
            $dataMax = $db_dpis->get_array();
            $MAXOFID =  $dataMax[MAXOFID];
            $MAXOFID = $MAXOFID+1;
        $cmdIns = "Insert into user_group (ID,CODE,NAME_TH,NAME_EN,ACCESS_LIST,GROUP_LEVEL,ORG_ID,PV_CODE,DPISDB,DPISDB_HOST,DPISDB_NAME,DPISDB_USER,DPISDB_PWD,GROUP_PER_TYPE,GROUP_ORG_STRUCTURE,GROUP_ACTIVE,GROUP_SEQ_NO,CREATE_DATE,CREATE_BY,UPDATE_DATE,UPDATE_BY,LEVEL_NO_LIST) 
                                           values ($MAXOFID,'HRG','���������Ǩ�ͺ�����','���������Ǩ�ͺ�����',',1,',4,$ORG_ID_TMP,null,null,null,null,null,null,0,2,1,8,sysdate,1,sysdate,1,null)"; 
        $db_dpis->send_cmd($cmdIns);                             
        //echo 'no group HRG =>'.$MAXOFID;
    }

}

/*Att*/
$cmdChk =" SELECT COUNT(*) AS CNT FROM SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)='P_EXPIATE' ";
$db->send_cmd($cmdChk);
$data = $db->get_array();
if($data){
    $cnt_chk=$data['CNT'];
    if($cnt_chk==0){
        $MAXOFCONFIG_ID=$MAXOFCONFIG_ID+1;
        $cmd_insert ="INSERT INTO SYSTEM_CONFIG (CONFIG_ID,CONFIG_NAME,CONFIG_VALUE,CONFIG_REMARK) 
            select max(CONFIG_ID)+1 as CONFIG_ID,'P_EXPIATE' as CONFIG_NAME,'N' as CONFIG_VALUE,'N-����ͧ��������, Y-��������' as CONFIG_REMARK from SYSTEM_CONFIG ";
        $db->send_cmd($cmd_insert);
    }
}
/*Att*/

$cmd = " select config_name from system_config where config_id = 62 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (62, 'xlsFmtTitle_color', '8^000000', '����ʴ����ѡ��� excel Ẻ Title 1') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 63 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (63, 'xlsFmtTitle_bgcolor', '9^FFFFFF', '����ʴ��վ��� excel Ẻ Title 1') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 64 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (64, 'xlsFmtSubTitle_color', '8^000000', '����ʴ��յ���ѡ��� excel Ẻ Title 2') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 65 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (65, 'xlsFmtSubTitle_bgcolor', '9^FFFFFF', '����ʴ��վ��� excel Ẻ Title 2') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 66 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (66, 'xlsFmtTableHeader_color', '8^000000', '����ʴ��յ���ѡ����ǵ��ҧ excel') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 67 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (67, 'xlsFmtTableHeader_bgcolor', '9^FFFFFF', '����ʴ��վ����ǵ��ҧ excel') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 68 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (68, 'xlsFmtTableDetail_color', '8^000000', '����ʴ��յ���ѡ��㹵��ҧ excel') ";
$db->send_cmd($cmd);

$cmd = " select config_name from system_config where config_id = 69 ";
$count_data = $db->send_cmd($cmd);
if (!$count_data)
    $cmd = " insert into system_config (config_id, config_name, config_value, config_remark) 
						values (69, 'xlsFmtTableDetail_bgcolor', '9^FFFFFF', '����ʴ��վ��㹵��ҧ excel') ";
$db->send_cmd($cmd);

$UPDATE_DATE = date("Y-m-d H:i:s");
// �����ѡ��� �������� check ���� password
$PASSWORD_UPDATE_DATE = date("Y-m-d");
//echo 'command:'.$command1;
if ($command1 == 'UPDATE') {
    $old_password = md5($old_passwd);

    $cmd = " select id from user_detail where username = '$username' and password = '$old_password' and user_flag='Y' ";
    $count_id = $db->send_cmd($cmd);
    if ($count_id > 0) {
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $USER_ID = $data[id];
        $allow_update = 1;
        if ($passwd)
            $set_password = "password = '" . md5($passwd) . "'";
        $cmd = "update user_detail set 
								$set_password,
								update_date = '$UPDATE_DATE', 
								update_by = $USER_ID,
								password_last_update = '$PASSWORD_UPDATE_DATE'
							where id=$USER_ID";
        $db->send_cmd($cmd);
        		//echo "$cmd<br>";
        $ERR = 9; // ������ʼ�ҹ����
        insert_log("LOGON PAGE > ���ʼ�ҹ������� ������ʼ�ҹ���� [$username -> $user_name]");
    }else {
        $ERR = 3; // �����ʴ���ͤ��� ��� �������ҹ
        $err_text = "�������ö������ʼ�ҹ������!!!";
    }
} else { //else command1 != UPDATE
    // �� ��ǹ���� �����ѡ��� �������� check ���� password
    $cmd = " SELECT group_per_type FROM user_group ";
    $count_data = $db->send_cmd($cmd);
    //$db->show_error();
    if (!$count_data) {
        if ($db_type == "odbc")
            $cmd = " ALTER TABLE user_group ADD group_per_type single ";
        elseif ($db_type == "oci8")
            $cmd = " ALTER TABLE user_group ADD group_per_type number(1) ";
        elseif ($db_type == "mysql")
            $cmd = " ALTER TABLE user_group ADD group_per_type smallint(1) ";
        $db->send_cmd($cmd);
        //$db->show_error();
    }

    $cmd = " SELECT group_org_structure FROM user_group ";
    $count_data = $db->send_cmd($cmd);
    //$db->show_error();
    if (!$count_data) {
        if ($db_type == "odbc")
            $cmd = " ALTER TABLE user_group ADD group_org_structure single ";
        elseif ($db_type == "oci8")
            $cmd = " ALTER TABLE user_group ADD group_org_structure number(1) ";
        elseif ($db_type == "mysql")
            $cmd = " ALTER TABLE user_group ADD group_org_structure smallint(1) ";
        $db->send_cmd($cmd);
        //$db->show_error();
    }
    
    if ($db_type == "odbc"){
        $cmd = " SELECT FIX_CONTROL FROM PER_CONTROL ";
        $count_data = $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
        if (!$count_data) {
            if ($db_type == "odbc")
                $cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL single ";
            elseif ($db_type == "oci8")
                $cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL number(1) ";
            elseif ($db_type == "mysql")
                $cmd = " ALTER TABLE PER_CONTROL ADD FIX_CONTROL smallint(1) ";
            $db_dpis->send_cmd($cmd);
            //$db_dpis->show_error();
        }
    }
    
    

    $cmd = " SELECT COUNT(SITE_ID) AS COUNT_DATA FROM SITE_INFO ";
    $count_data = $db->send_cmd($cmd);
//	$db->show_error();
    if (!$count_data) {
        if ($db_type == "odbc")
            $cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR(10) NOT NULL,
			ORG_ID VARCHAR(10) NULL,
			DPISDB VARCHAR(50) NULL,		
			DPISDB_HOST VARCHAR(50) NULL,		
			DPISDB_NAME VARCHAR(50) NULL,		
			DPISDB_USER VARCHAR(50) NULL,		
			DPISDB_PWD VARCHAR(50) NULL,		
			SITE_NAME VARCHAR(100) NULL,		
			SITE_BG_LEFT VARCHAR(100) NULL,		
			SITE_BG_LEFT_X INTEGER2 NULL,
			SITE_BG_LEFT_Y INTEGER2 NULL,
			SITE_BG_LEFT_W INTEGER2 NULL,
			SITE_BG_LEFT_H INTEGER2 NULL,
			SITE_BG_LEFT_ALPHA NUMBER NULL,
			SITE_BG VARCHAR(100) NULL,		
			SITE_BG_X INTEGER2 NULL,
			SITE_BG_Y INTEGER2 NULL,
			SITE_BG_W INTEGER2 NULL,
			SITE_BG_H INTEGER2 NULL,
			SITE_BG_ALPHA NUMBER NULL,
			SITE_BG_RIGHT VARCHAR(100) NULL,		
			SITE_BG_RIGHT_X INTEGER2 NULL,
			SITE_BG_RIGHT_Y INTEGER2 NULL,
			SITE_BG_RIGHT_W INTEGER2 NULL,
			SITE_BG_RIGHT_H INTEGER2 NULL,
			SITE_BG_RIGHT_ALPHA NUMBER NULL,
			HEAD_FONT_NAME VARCHAR(100) NULL,		
			HEAD_FONT_SIZE INTEGER2 NULL,
			CSS_NAME VARCHAR(100) NULL,		
			SITE_LEVEL SINGLE NULL,	
			PV_CODE VARCHAR(10) NULL,		
			HEAD_HEIGHT INTEGER2 NULL,
			UPDATE_USER INTEGER2 NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
        elseif ($db_type == "oci8")
            $cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR2(10) NOT NULL,
			ORG_ID VARCHAR2(10) NULL,
			DPISDB VARCHAR2(50) NULL,		
			DPISDB_HOST VARCHAR2(50) NULL,		
			DPISDB_NAME VARCHAR2(50) NULL,		
			DPISDB_USER VARCHAR2(50) NULL,		
			DPISDB_PWD VARCHAR2(50) NULL,		
			SITE_NAME VARCHAR2(100) NULL,		
			SITE_BG_LEFT VARCHAR2(100) NULL,		
			SITE_BG_LEFT_X NUMBER(5) NULL,
			SITE_BG_LEFT_Y NUMBER(5) NULL,
			SITE_BG_LEFT_W NUMBER(5) NULL,
			SITE_BG_LEFT_H NUMBER(5) NULL,
			SITE_BG_LEFT_ALPHA NUMBER(4,2) NULL,
			SITE_BG VARCHAR2(100) NULL,		
			SITE_BG_X NUMBER(5) NULL,
			SITE_BG_Y NUMBER(5) NULL,
			SITE_BG_W NUMBER(5) NULL,
			SITE_BG_H NUMBER(5) NULL,
			SITE_BG_ALPHA NUMBER(4,2) NULL,
			SITE_BG_RIGHT VARCHAR2(100) NULL,		
			SITE_BG_RIGHT_X NUMBER(5) NULL,
			SITE_BG_RIGHT_Y NUMBER(5) NULL,
			SITE_BG_RIGHT_W NUMBER(5) NULL,
			SITE_BG_RIGHT_H NUMBER(5) NULL,
			SITE_BG_RIGHT_ALPHA NUMBER(4,2) NULL,
			HEAD_FONT_NAME VARCHAR2(100) NULL,		
			HEAD_FONT_SIZE NUMBER(5) NULL,
			CSS_NAME VARCHAR2(100) NULL,		
			SITE_LEVEL NUMBER(1) NULL,	
			PV_CODE VARCHAR2(10) NULL,		
			HEAD_HEIGHT NUMBER(5) NULL,
			UPDATE_USER NUMBER(5) NOT NULL,
			UPDATE_DATE VARCHAR2(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
        elseif ($db_type == "mysql")
            $cmd = " CREATE TABLE SITE_INFO(
			SITE_ID VARCHAR(10) NOT NULL,
			ORG_ID VARCHAR(10) NULL,
			DPISDB VARCHAR(50) NULL,		
			DPISDB_HOST VARCHAR(50) NULL,		
			DPISDB_NAME VARCHAR(50) NULL,		
			DPISDB_USER VARCHAR(50) NULL,		
			DPISDB_PWD VARCHAR(50) NULL,		
			SITE_NAME VARCHAR(100) NULL,		
			SITE_BG_LEFT VARCHAR(100) NULL,		
			SITE_BG_LEFT_X SMALLINT(5) NULL,
			SITE_BG_LEFT_Y SMALLINT(5) NULL,
			SITE_BG_LEFT_W SMALLINT(5) NULL,
			SITE_BG_LEFT_H SMALLINT(5) NULL,
			SITE_BG_LEFT_ALPHA DECIMAL(4,2) NULL,
			SITE_BG VARCHAR(100) NULL,		
			SITE_BG_X SMALLINT(5) NULL,
			SITE_BG_Y SMALLINT(5) NULL,
			SITE_BG_W SMALLINT(5) NULL,
			SITE_BG_H SMALLINT(5) NULL,
			SITE_BG_ALPHA DECIMAL(4,2) NULL,
			SITE_BG_RIGHT VARCHAR(100) NULL,		
			SITE_BG_RIGHT_X SMALLINT(5) NULL,
			SITE_BG_RIGHT_Y SMALLINT(5) NULL,
			SITE_BG_RIGHT_W SMALLINT(5) NULL,
			SITE_BG_RIGHT_H SMALLINT(5) NULL,
			SITE_BG_RIGHT_ALPHA DECIMAL(4,2) NULL,
			HEAD_FONT_NAME VARCHAR(100) NULL,		
			HEAD_FONT_SIZE SMALLINT(5) NULL,
			CSS_NAME VARCHAR(100) NULL,		
			SITE_LEVEL SMALLINT(1) NULL,	
			PV_CODE VARCHAR(10) NULL,		
			HEAD_HEIGHT SMALLINT(5) NULL,
			UPDATE_USER SMALLINT(5) NOT NULL,
			UPDATE_DATE VARCHAR(19) NOT NULL,		
			CONSTRAINT PK_SITE_INFO PRIMARY KEY (SITE_ID)) ";
        $db->send_cmd($cmd);
//		$db->show_error();
    } // end if

    $cmd = " SELECT SITE_ID FROM SITE_INFO ";
    $db->send_cmd($cmd);
//	$db->show_error();
    $data = $db->get_array();
    $SITE_ID = $data[SITE_ID];
    if (!$SITE_ID) {
        $cmd = " select ORG_ID, CTRL_TYPE, PV_CODE,CTRL_ALTER from PER_CONTROL ";
        $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
        $data = $db_dpis->get_array();
        $ORG_ID = $data[ORG_ID];
        $CTRL_TYPE = $data[CTRL_TYPE];
        $dbCTRL_ALTER= $data[CTRL_ALTER];
        $PV_CODE = $data[PV_CODE];
        if (!$ORG_ID)
            $ORG_ID = "NULL";
        $UPDATE_USER = 1;

        $CSS_NAME = "style.css";
        if ($CTRL_TYPE == 1) {
            $SITE_BG_LEFT = "images/top_left_ses.jpg";
            $SITE_BG = "images/top_bg_ses.jpg";
            $SITE_BG_RIGHT = "images/top_right_ses.jpg";
        } elseif ($CTRL_TYPE == 2) {
            $SITE_BG_LEFT = "images/top_left_prov.jpg";
            $SITE_BG = "images/top_bg_prov.jpg";
            $SITE_BG_RIGHT = "images/top_right_prov.jpg";
        } elseif (($CTRL_TYPE == 3 && $TEMP_ORG_NAME == "��з�ǧ�ɵ�����ˡó�") || $TEMP_ORG_NAME == "�ӹѡ�ҹ��Ѵ��з�ǧ�ɵ�����ˡó�") {
            $SITE_BG_LEFT = "images/top_left_moac.jpg";
            $SITE_BG = "images/top_bg_moac.jpg";
            $SITE_BG_RIGHT = "images/top_right_moac.jpg";
            $SITE_NAME = "������к��ҹ���������ʹ�ȡ�ú����÷�Ѿ�ҡúؤ��";
        } elseif (($CTRL_TYPE == 3 && $TEMP_ORG_NAME == "��з�ǧ�ص��ˡ���") || $TEMP_ORG_NAME == "�ӹѡ�ҹ��Ѵ��з�ǧ�ص��ˡ���") {
            $SITE_BG_LEFT = "images/top_left_moi.jpg";
            $SITE_BG = "images/top_bg_moi.jpg";
            $SITE_BG_RIGHT = "images/top_right_moi.jpg";
            $SITE_NAME = "������к��ҹ���������ʹ�ȡ�ú����÷�Ѿ�ҡúؤ��";
        } elseif ($TEMP_ORG_NAME == "�����û���ͧ") {
            $SITE_BG_LEFT = "images/top_left_dopa.swf";
            $SITE_BG = "images/top_bg_dopa.jpg";
            $SITE_BG_RIGHT = "images/top_right_dopa.swf";
            $SITE_NAME = "�к��ҹ���������ʹ�ȡ�ú����çҹ��Ѿ�ҡúؤ��";
        } elseif ($TEMP_ORG_NAME == "����ص��ˡ�����鹰ҹ��С������ͧ���") {
            $SITE_BG_LEFT = "images/top_left_dpim.jpg";
            $SITE_BG = "images/top_bg_dpim.jpg";
            $SITE_BG_RIGHT = "images/top_right_dpim.jpg";
            $SITE_NAME = "������к��ҹ���������ʹ�ȡ�ú����÷�Ѿ�ҡúؤ��";
        } elseif ($TEMP_ORG_NAME == "�ӹѡ�ҹ�ҵðҹ��Ե�ѳ���ص��ˡ���") {
            $SITE_BG_LEFT = "images/top_left_tisi.jpg";
            $SITE_BG = "images/top_bg_tisi.jpg";
            $SITE_BG_RIGHT = "images/top_right_tisi.jpg";
            $SITE_NAME = "������к��ҹ���������ʹ�ȡ�ú����÷�Ѿ�ҡúؤ��";
        } elseif ($TEMP_ORG_NAME == "ʶҺѹ�ѳ�Ե�Ѳ���Ż�") {
            $SITE_BG_LEFT = "images/top_left_bpi.jpg";
            $SITE_BG = "images/top_bg_bpi.jpg";
            $SITE_BG_RIGHT = "images/top_right_bpi.jpg";
            $SITE_NAME = "������к��ҹ���������ʹ�ȡ�ú����÷�Ѿ�ҡúؤ��";
        } elseif ($TEMP_ORG_NAME == "�ӹѡ�ҹ �.�.xxxxxxxxxx") {
            $SITE_BG_LEFT = "images/top_left_51.swf";
            $SITE_BG = "images/top_bg_51.jpg";
            $SITE_BG_RIGHT = "images/top_right_51.swf";
        } else {
            $SITE_BG_LEFT = "images/top_left.jpg";
            $SITE_BG = "images/top_bg.jpg";
            $SITE_BG_RIGHT = "images/top_right.jpg";
        }
        $cmd = " INSERT INTO SITE_INFO (SITE_ID, ORG_ID, SITE_NAME, SITE_BG_LEFT, SITE_BG, SITE_BG_RIGHT, 
						  CSS_NAME, SITE_LEVEL, PV_CODE, UPDATE_USER, UPDATE_DATE)
						  VALUES (1, $ORG_ID, '$SITE_NAME', '$SITE_BG_LEFT', '$SITE_BG', '$SITE_BG_RIGHT', 
						  '$CSS_NAME', $CTRL_TYPE, '$PV_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
        $db->send_cmd($cmd);
        //$db->show_error() ;
    } // end if
    /*Show Release Now*/
    $cmd = " select msg_header 
        from PER_MESSAGE 
        WHERE msg_type=0 
          AND msg_id IN(select max(msg_id) from PER_MESSAGE where msg_type=0 AND msg_header LIKE '%Patch Update DPIS Release%' ) ";
    
        $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array_array();
        $DPIS_Release = trim(substr($data[0], 18,30)) ;
        session_register("DPIS_Release");
    /**/
    
    if ($db_type == "mysql") {
        $update_date = "NOW()";
        $update_by = "'$SESS_USERNAME'";
    } elseif ($db_type == "mssql") {
        $update_date = "GETDATE()";
        $update_by = $SESS_USERID;
    } elseif ($db_type == "oci8" || $db_type == "odbc") {
        $update_date = date("Y-m-d H:i:s");
        $update_date = "'$update_date'";
        $update_by = $SESS_USERID;
    }

    $sql = " select group_id from user_privilege ";
    $count_data = $db->send_cmd($sql);

    if (!$count_data) {
        $db_insert = new connect_db($db_host, $db_name, $db_user, $db_pwd);

        // $sql = " 	select 		a.menu_id as menu_id_lv0, b.menu_id as menu_id_lv1
		// 				from		backoffice_menu_bar_lv0 a, backoffice_menu_bar_lv1 b
		// 				where		a.menu_id=b.parent_id and a.langcode='TH' and b.langcode='TH'
        // 				order by	a.menu_id, b.menu_id ";
       
        // by keang CDGS
        $sql = " 	select 		a.\"menu_id\" as menu_id_lv0, b.\"menu_id\" as menu_id_lv1
						from		backoffice_menu_bar_lv0 a, backoffice_menu_bar_lv1 b
						where		a.\"menu_id\"=b.\"parent_id_lv0\" and a.\"langcode\"='TH' and b.\"langcode\"='TH'
						order by	a.\"menu_id\", b.\"menu_id\" ";
        $db->send_cmd($sql);
        while ($data = $db->get_array()) {
            $data = array_change_key_case($data, CASE_LOWER);
            if ($data[menu_id_lv0] != $menu_id) {
                $sql = "	insert into user_privilege
									(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, create_date, create_by, update_date, update_by)
									values
									(1, 1, $data[menu_id], 0, 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', $update_date, $update_by, $update_date, $update_by) ";
                $db_insert->send_cmd($sql);
                $menu_id = $data[menu_id_lv0];
            } // end if

            $sql = "	insert into user_privilege
								(group_id, page_id, menu_id_lv0, menu_id_lv1, menu_id_lv2, menu_id_lv3, can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_audit, create_date, create_by, update_date, update_by)
								values
								(1, 1, $data[menu_id_lv0], $data[menu_id_lv1], 0, 0, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', $update_date, $update_by, $update_date, $update_by) ";
            $db_insert->send_cmd($sql);
        } // end while
    } // end if

    $username = trim($username);
    $password = trim($password);
   
    if ($command == "LOGIN" && $username != "" && ($password != "" || $fpass_pwd == 1)) {
        $encrypt_password = md5($password);
        //cdgs
		//$encrypt_password = md5($password);		
		// $encrypt_password=encryptPwd($password);
		//echo "pwd=$password,encrypt_password=$encrypt_password";
		//cdgs
        $ERR = 1;
       // die('IN LOGIN ');
        /*Release 5.2.1.21 Begin*/
        //�����ҹ�Ѻ�ҹ MySQL �����
        /* $cmdChk="SELECT COUNT(*) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME = 'USER_DETAIL' AND UPPER(COLUMN_NAME) = 'USER_FLAG'";
        $db->send_cmd($cmdChk);
        $dataChk = $db->get_array();
        if($dataChk[CNT]=="0"){
            $cmdA=" ALTER TABLE USER_DETAIL ADD USER_FLAG CHAR(1) DEFAULT 'Y' ";
            $db->send_cmd($cmdA);
            $cmdA = "COMMIT";
            $db->send_cmd($cmdA);
        }*/
        //��Ѻ������ ���ӧҹ�Ѻ MySQL ��
        if ($db_type == "oci8"){
             $cmdChk="SELECT COUNT(*) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME = 'USER_DETAIL' AND UPPER(COLUMN_NAME) = 'USER_FLAG'";
             $db->send_cmd($cmdChk);
             $dataChk = $db->get_array();
             if($dataChk[CNT]=="0"){
                $cmdA=" ALTER TABLE USER_DETAIL ADD USER_FLAG CHAR(1) DEFAULT 'Y' ";
                $db->send_cmd($cmdA);
                $cmdA = "COMMIT";
                $db->send_cmd($cmdA);
             }
        }elseif ($db_type == "mysql"){
            $cmdChk="SELECT user_flag FROM user_detail"; 
            $count_data = $db->send_cmd($cmdChk);
            if (!$count_data) {
                $cmdA=" ALTER TABLE USER_DETAIL ADD USER_FLAG CHAR(1) DEFAULT 'Y' ";
                $db->send_cmd($cmdA);
            }
        }
        // add and user_flag='Y'
        /*Release 5.2.1.21 End*/

        ///////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////
        $cmd = "select	create_by from user_detail where username = '$username' and user_flag='Y' ";
        $db->send_cmd($cmd);
        $data = $db->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        
        if ($data[create_by] == 'LDAP') {
            /// authenication by ldap 
            $cmd = "select config_name,config_value from ldap_config";
            $db->send_cmd($cmd);
            while ($data = $db->get_array()) {
                $data = array_change_key_case($data, CASE_LOWER);
                $xx = $data[config_name];
                $ldap_cfg[$xx] = $data[config_value];
            }

            //$user = "CN=17 Temp,OU=Class_17,OU=Student,DC=nist,DC=ac,DC=th";
            //$pass = "";
            // connect to active directory
            $ad = ldap_connect($ldap_cfg['server_address'], $ldap_cfg['server_port']);
            if (!$ad) {
                die("Connect not connect to " . $ldap_cfg['server_address']);
            } else {
                if ($fpass_pwd == 1) { // �Ѻ $_POST  �͡������ʹ� password ������ check password
                    $cmd = "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update, b.group_active, b.code, b.level_no_list
							from	user_detail a, user_group b
							where a.group_id=b.id and a.username = '$username' and a.user_flag='Y' ";
                } else {
                    $b = @ldap_bind($ad, "uid=" . $username . ",ou=People,dc=doeb,dc=go,dc=th", $password);
                    if ($b) {
                        $cmd = "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update, b.group_active, b.code, b.level_no_list
								from	user_detail a, user_group b
								where a.group_id=b.id and a.username = '$username' and a.user_flag='Y' ";
                    } else {
                        /* $cmd =  "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure
                          from	user_detail a, user_group b
                          where a.group_id=b.id and a.username = '$username' "; */

                        $cmd = "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update, b.group_active, b.code, b.level_no_list
													from	user_detail a, user_group b
													where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' and a.user_flag='Y' ";
                    }
                } // end if ($fpass_pwd==1)
            }
        } else {
            if ($fpass_pwd == 1) { // �Ѻ $_POST  �͡������ʹ� password ������ check password
                $cmd = "	select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, a.password_last_update, b.group_active, b.code, b.level_no_list
						from	user_detail a, user_group b
						where a.group_id=b.id and a.username = '$username' and a.user_flag='Y' ";
            } else {
                $cmd = "select	a.id, a.fullname, a.address, a.group_id, a.inherit_group, a.user_link_id, 
                            b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure, 
                            a.password_last_update, b.group_active, b.code, b.level_no_list
                        from user_detail a, user_group b
                        where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' and a.user_flag='Y' ";
            }
            
        }
        //echo 'ERR=1 :'.$cmd.'<br>';
        ///////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////

        /*
          $cmd =  "	select	a.id, a.fullname, a.group_id, a.inherit_group, a.user_link_id, b.group_level, b.pv_code, b.org_id,b.group_per_type,b.group_org_structure
          from	user_detail a, user_group b
          where a.group_id=b.id and a.username = '$username' and a.password = '$encrypt_password' ";
          $db->send_cmd($cmd);
         */
       
        /**/
        $isUserActive=1;
        $resultChk = $db->send_cmd($cmd);
        if($resultChk){
            $dataUserActive = $db->get_array();
            $dataUserActive = array_change_key_case($dataUserActive, CASE_LOWER);
            $UserActive_user_link_id=$dataUserActive[user_link_id];
            if(!empty($UserActive_user_link_id)){
                $cmdChk = " select COUNT(*) AS CNT
                                 from PER_PERSONAL a, PER_PRENAME b 
                                 where 	a.PN_CODE=b.PN_CODE(+) and a.PER_STATUS !=2 and a.PER_ID=" . $UserActive_user_link_id;
                $db_dpis->send_cmd($cmdChk);
                $data_dpis_UserActive = $db_dpis->get_array();
                if($data_dpis_UserActive[CNT]==0){
                    $isUserActive=0;
                    $ERR = 11;//11
                    /*if($txtErrorCode==11){
                        $isUserActive=1;
                    }*/
                }else{
                    $isUserActive=1;
                }
            }
        }else{
            $isUserActive=0;
        }
        
        
        
        $result = $db->send_cmd($cmd);
//		echo "$cmd ($result)<br>";
//		$db->show_error();
        if ($result && $isUserActive==1) {
            $data = $db->get_array();
            $data = array_change_key_case($data, CASE_LOWER);

            // �ó� �� user ��� ¡��ԡ�����
            $group_active = $data[group_active];
            $group_code = $data[code];
            // �����ѡ��� �������� check ���� password
            $today = date("Y-m-d");
            $pwd_lastupd = $data[password_last_update];

            $cmd1 = " select config_value from system_config where config_name='password_age' ";
            $db->send_cmd($cmd1);
            $data1 = $db->get_array();
            $data1 = array_change_key_case($data1, CASE_LOWER);
            $pwd_age = trim($data1['config_value']);

            if ($pwd_age != "0") { // ��ҡ�˹���� system_config ������� password_age ��� = 0 ������ա�� check ���� password
                if (strpos(strtolower($pwd_age), "d") !== false) {
                    $pwd_age_type = "d";
                    $pwd_diff = substr($pwd_age, 0, strlen($pwd_age) - 1);
                } else if (strpos(strtolower($pwd_age), "y") !== false) {
                    $pwd_age_type = "y";
                    $pwd_diff = substr($pwd_age, 0, strlen($pwd_age) - 1);
                } else if (strpos(strtolower($pwd_age), "m") !== false) {
                    $pwd_age_type = "m";
                    $pwd_diff = substr($pwd_age, 0, strlen($pwd_age) - 1);
                } else {
                    $pwd_age_type = "m";
                    $pwd_diff = $pwd_age;
                }
                $diff = date_difference($pwd_lastupd, $today, $pwd_age_type);
//				echo "$pwd_age::$diff::$pwd_lastupd::$today<br>";
            } // end if ($pwd_age!="0") {
//echo '<br>';
//echo $group_active.','.$pwd_age.','.$diff.','.$pwd_diff;

            if ($group_active != 1) {
//				echo "����� $username �١¡��ԡ�����";
                $ERR = 10;
                // �� �ó� �� user ��� ¡��ԡ�����
            } else if ($pwd_age != "0" && (real) $diff > (real) $pwd_diff) {
//				echo "password expire...";
                $ERR = 2;
            } else {
                // �� ��ǹ���� �����ѡ��� �������� check ���� password
                $SESS_USERNAME = $username;
                $SESS_PASSWORD_KEY = $password; //ત�����������¹���ʼ�ҹ ���������ѹ��׹���Դ
                $SESS_USERADDR = $data[address];
                $SESS_USERID = $data[id];
                $user_link_id = $data[user_link_id];
                //echo "UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id<br>";
                if (!trim($user_link_id)) {
                    $SESS_PER_ID = "";
                    $SESS_FIRSTNAME = $data[fullname];
                    //echo "old user UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                } else {
                    //�Ң����Ţͧ�����͡�Թ
                    $SESS_PER_ID = $user_link_id;
                    if ($DPISDB == "odbc")
                        $cmd = " select  a.PER_ID,b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.DEPARTMENT_ID, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID
										 from 		PER_PERSONAL a
										 				left join PER_PRENAME b on a.PN_CODE=b.PN_CODE
										 where 	a.PER_ID=" . $SESS_PER_ID;
                    elseif ($DPISDB == "oci8")
                        $cmd = " select a.PER_ID,b.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
                                    a.DEPARTMENT_ID, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID,a.PER_AUDIT_FLAG,a.ORG_ID,a.ORG_ID_1,a.PER_OT_FLAG ,
                                    a.PER_BIRTHDATE
                                 from PER_PERSONAL a, PER_PRENAME b 
                                 where 	a.PN_CODE=b.PN_CODE(+) and a.PER_STATUS!=2 and a.PER_ID=" . $SESS_PER_ID;
                    elseif ($DPISDB == "mysql")
                        $cmd = " select a.PER_ID,b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.DEPARTMENT_ID , a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID
										 from 		PER_PERSONAL a
										 				left join PER_PRENAME b on a.PN_CODE=b.PN_CODE
										 where 	a.PER_ID=" . $SESS_PER_ID;
                    $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
                    //die("$cmd<br>");

                    $data_dpis = $db_dpis->get_array();
                    
                    
                    
                    $SESS_FIRSTNAME = $data_dpis[PN_NAME] . (trim($data_dpis[PN_NAME]) ? " " : "") . $data_dpis[PER_NAME] . " " . $data_dpis[PER_SURNAME];
//					echo "new user UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                    $PER_DEPARTMENT_ID = $data_dpis[DEPARTMENT_ID];
                    
                    $PER_BIRTHDATE= $data_dpis[PER_BIRTHDATE];
                    $PASSWORD_DB="";
                    if(!empty($PER_BIRTHDATE)){
                        $PER_BIRTHDATE_ARR = explode("-",$PER_BIRTHDATE); //1959-03-17
                        /*$PER_BIRTHDATE_ARR[0]+543;
                        $PER_BIRTHDATE_ARR[1];
                        $PER_BIRTHDATE_ARR[2];*/
                        $PASSWORD_DB=trim($PER_BIRTHDATE_ARR[2]).trim($PER_BIRTHDATE_ARR[1]).trim(($PER_BIRTHDATE_ARR[0]+543));
                    }
                    $SESS_PASSWORD_DB = $PASSWORD_DB;
                    
                    $POS_ID = trim($data_dpis[POS_ID]);
                    $POEM_ID = trim($data_dpis[POEM_ID]);
                    $POEMS_ID = trim($data_dpis[POEMS_ID]);
                    $POT_ID = trim($data_dpis[POT_ID]);

                    $SESS_PER_AUDIT_FLAG=$data_dpis[PER_AUDIT_FLAG]; /*TIME ATT*/  
                    $ASS_ORG_ID=$data_dpis[ORG_ID]; /*TIME ATT*/    
                    $ASS_ORG_ID_1=$data_dpis[ORG_ID_1]; /*TIME ATT*/    
                    $SESS_PERID_REPORT = $data_dpis[PER_ID]; // 
					
					$SESS_PER_OT_FLAG=$data_dpis[PER_OT_FLAG]; /*TIME ATT*/  

                    if ($POS_ID) {
                        $cmd = " select 	ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
										from 	PER_POSITION where POS_ID=$POS_ID  ";
                    }
                    if ($POEM_ID) {
                        $cmd = " select 	ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
										from 	PER_POS_EMP where POEM_ID=$POEM_ID ";
                    }
                    if ($POEMS_ID) {
                        $cmd = " select 	ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
										from 	PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
                    }
                    if ($POT_ID) {
                        $cmd = " select 	ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
										from 	PER_POS_TEMP where POT_ID=$POT_ID ";
                    }
                    $db_dpis->send_cmd($cmd);
                    $data_dpis = $db_dpis->get_array();
                    $PER_ORG_ID = trim($data_dpis[ORG_ID]);
                    $PER_ORG_ID_1 = trim($data_dpis[ORG_ID_1]);
                    $PER_ORG_ID_2 = trim($data_dpis[ORG_ID_2]);
                    $PER_ORG_ID_3 = trim($data_dpis[ORG_ID_3]);
                    $PER_ORG_ID_4 = trim($data_dpis[ORG_ID_4]);
                    $PER_ORG_ID_5 = trim($data_dpis[ORG_ID_5]);
                } // end if
//				echo "2. ID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                $SESS_PER_TYPE = $data[group_per_type];
                $SESS_ORG_STRUCTURE = $data[group_org_structure];
                //��Ǩ�ͺ�����͡�Թ���������繻��������� ?
                $ALL_DEPARTMENT_ID = "";
                
                $SESS_USERGROUP = $data[group_id];
                $SESS_USERGROUP_LEVEL = $data[group_level];
                $SESS_LEVEL_NO_LIST = "(" . $data[level_no_list] . ")";
                
                //die($SESS_PER_AUDIT_FLAG.",".$group_code);
				
				// GROUP OT
				
				if($SESS_PER_OT_FLAG!=0){
					$cmdGroup = "SELECT ID FROM user_group WHERE upper(CODE)='OT' ";
                    $db_dpis->send_cmd($cmdGroup);
                    $dataG = $db_dpis->get_array();
                    $SESS_USERGROUP_OT = trim($dataG[ID]);
				}
				
                if($SESS_PER_AUDIT_FLAG==1){
                    $cmdGroup = "SELECT ID FROM user_group WHERE upper(CODE)='HRG' ";
                    $db_dpis->send_cmd($cmdGroup);
                    $dataG = $db_dpis->get_array();
                    $SESS_USERGROUP_HRG = trim($dataG[ID]);// $SESS_USERGROUP = 199999991;
                    
                    $cmdOrgAss = "SELECT DEPARTMENT_ID,ORG_ASS_ID,NVL(ORG_LOWER1,0) AS ORG_LOWER1,ORG_LOWER2,ORG_LOWER3,ORG_LOWER4,ORG_LOWER5 
                                    FROM TA_PER_AUDIT WHERE trim(PER_CARDNO)='".trim($username)."' ";
                    
					$cntTa = $db_dpis->send_cmd($cmdOrgAss);
		    $AuditArray = (array) NULL;			
                    if($cntTa){
                        $iRows =0;
                        while ($dataOrg2 = $db_dpis->get_array()) {
                            $AuditArray[$iRows][0]=$dataOrg2[ORG_ASS_ID];
                            $AuditArray[$iRows][1]=$dataOrg2[ORG_LOWER1];
                            $iRows++;
                        }       
                        $SESS_AuditArray = $AuditArray;		
                    }else{
                        if(!empty($ASS_ORG_ID)){
                            $AuditArray[0][0]=$ASS_ORG_ID;
                            if(empty($ASS_ORG_ID_1)){$ASS_ORG_ID_1=0;}
                            $AuditArray[0][1]=$ASS_ORG_ID_1;
                        }
                        $SESS_AuditArray = $AuditArray;	
                        
                        $SESS_USERGROUP = $data[group_id];
                    }
                }
               
                
                
                switch ($SESS_USERGROUP_LEVEL) {
                    case 2 :
                        $SESS_PROVINCE_CODE = $data[pv_code];
                        break;
                    case 3 :
                        $SESS_MINISTRY_ID = $data[org_id];
                        //�ҡ��������㹡�з�ǧ��� ���͵�Ǩ�ͺ����ա���˹��ҧ���͹حҵ������ PKG ��
                        $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$SESS_MINISTRY_ID ";
                        if ($SESS_ORG_STRUCTURE == 1) {
                            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                        }
                        $db_dpis->send_cmd($cmd);
                        //		$db_dpis->show_error();
                        while ($data2 = $db_dpis->get_array()) {
                            //��˹����������� ����Ѻ PKG
                            $SPKG1[$data2[ORG_ID]] = "N";
                            $SPKG2[$data2[ORG_ID]] = "N";
                            $SCOMPETENCY[$data2[ORG_ID]] = "N";
                            $ALL_DEPARTMENT_ID .= $data2[ORG_ID] . ",";
                        }
                        break;
                    case 4 :
                        $SESS_DEPARTMENT_ID = $data[org_id];
                        //��˹����������� ����Ѻ PKG
                        $SPKG1[$SESS_DEPARTMENT_ID] = "N";
                        $SPKG2[$SESS_DEPARTMENT_ID] = "N";
                        $SCOMPETENCY[$SESS_DEPARTMENT_ID] = "N";
                        $ALL_DEPARTMENT_ID = $SESS_DEPARTMENT_ID . ",";
                        break;
                    case 5 :
                        $SESS_ORG_ID = $data[org_id];
                        $cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
                        if ($SESS_ORG_STRUCTURE == 1) {
                            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                        }
                        $db_dpis->send_cmd($cmd);
                        //			$db_dpis->show_error();
                        while ($data2 = $db_dpis->get_array()) {
                            //��˹����������� ����Ѻ PKG
                            $SPKG1[$data2[ORG_ID_REF]] = "N";
                            $SPKG2[$data2[ORG_ID_REF]] = "N";
                            $SCOMPETENCY[$data2[ORG_ID_REF]] = "N";
                            $ALL_DEPARTMENT_ID .= $data2[ORG_ID_REF] . ",";
                        }
                        break;
                    case 6 :
                        $SESS_ORG_ID_1 = $data[org_id];
                        break;
                } // end switch case

                if ($SESS_ORG_ID_1) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID_1 ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $SESS_ORG_NAME_1 = $data[ORG_NAME];
                    $SESS_ORG_ID = $data[ORG_ID_REF];
                } // end if

                if ($SESS_ORG_ID) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_ORG_ID ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $SESS_ORG_NAME = $data[ORG_NAME];
                    $SESS_DEPARTMENT_ID = $data[ORG_ID_REF];
                } // end if
//				echo "2.1. UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                if ($SESS_DEPARTMENT_ID) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$SESS_DEPARTMENT_ID ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $SESS_DEPARTMENT_NAME = $data[ORG_NAME];
                    $SESS_MINISTRY_ID = $data[ORG_ID_REF];
                } // end if

                if ($SESS_MINISTRY_ID) {
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$SESS_MINISTRY_ID ";
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $SESS_MINISTRY_NAME = $data[ORG_NAME];
                } // end if

                if ($SESS_PROVINCE_CODE) {
                    $cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$SESS_PROVINCE_CODE' ";
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $SESS_PROVINCE_NAME = $data[PV_NAME];
                } // end if

                if ($PER_ORG_ID_1) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$PER_ORG_ID_1 ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $PER_ORG_ID_NAME_1 = $data[ORG_NAME];
                    $PER_ORG_ID = $data[ORG_ID_REF];
                }

                if ($PER_ORG_ID) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$PER_ORG_ID ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $PER_ORG_NAME = $data[ORG_NAME];
                    $PER_DEPARTMENT_ID = $data[ORG_ID_REF];
                } // end if

                if ($PER_DEPARTMENT_ID) {
                    $cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$PER_DEPARTMENT_ID ";
                    if ($SESS_ORG_STRUCTURE == 1) {
                        $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    }
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $PER_DEPARTMENT_NAME = $data[ORG_NAME];
                    $PER_MINISTRY_ID = $data[ORG_ID_REF];
                } // end if

                if ($PER_MINISTRY_ID) {
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$PER_MINISTRY_ID ";
                    $db_dpis->send_cmd($cmd);
                    //			$db_dpis->show_error();
                    $data = $db_dpis->get_array();
                    $PER_MINISTRY_NAME = $data[ORG_NAME];
                } // end if
                //�Ң����Ţͧ�����͡�Թ
                if ($SESS_PER_ID) {
                    if ($PER_MINISTRY_ID)
                        $SESS_MINISTRY_ID = $PER_MINISTRY_ID; $MINISTRY_ID = $PER_MINISTRY_ID;
                    if ($PER_MINISTRY_NAME)
                        $SESS_MINISTRY_NAME = $PER_MINISTRY_NAME; $MINISTRY_NAME = $PER_MINISTRY_NAME;
                    if ($PER_DEPARTMENT_ID)
                        $SESS_DEPARTMENT_ID = $PER_DEPARTMENT_ID; $DEPARTMENT_ID = $PER_DEPARTMENT_ID;
                    if ($PER_DEPARTMENT_NAME)
                        $SESS_DEPARTMENT_NAME = $PER_DEPARTMENT_NAME; $DEPARTMENT_NAME = $PER_DEPARTMENT_NAME;
                    if ($PER_ORG_ID)
                        $SESS_ORG_ID = $PER_ORG_ID; $ORG_ID = $PER_ORG_ID;
                    if ($PER_ORG_NAME)
                        $SESS_ORG_NAME = $PER_ORG_NAME; $ORG_NAME = $PER_ORG_NAME;
                    if ($PER_ORG_ID_1)
                        $SESS_ORG_ID_1 = $PER_ORG_ID_1;
                    if ($PER_ORG_ID_NAME_1)
                        $SESS_ORG_NAME_1 = $PER_ORG_ID_NAME_1;
                }
//				echo "2.2. UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";

                $SESS_INHERITGROUP = $data[inherit_group];
                $ACCESSIBLE_GROUP = $SESS_USERGROUP . (trim($SESS_INHERITGROUP) ? ",$SESS_INHERITGROUP" : "");

                //��ª��͡��������㹡�з�ǧ �����͡�Թ�繡�з�ǧ
                if (trim($ALL_DEPARTMENT_ID)) {
                    $ALL_DEPARTMENT_ID = substr($ALL_DEPARTMENT_ID, 0, -1);  //�Ѵ comma ��Ƿ��·��
                    $ARR_ALL_DEPARTMENT_ID = explode(",", $ALL_DEPARTMENT_ID);
                }

                $SESS_ORG_SETLEVEL = (trim($SESS_ORG_SETLEVEL)) ? $SESS_ORG_SETLEVEL : 2;  //����ѧ����ա�� set ����繵�ӡ��� 2 �дѺ
                $cmd = " select config_value from system_config where config_name='ORG_SETLEVEL' ";
                $db->send_cmd($cmd);
                $data = $db->get_array();
                $data = array_change_key_case($data, CASE_LOWER);
                $SESS_ORG_SETLEVEL = trim($data['config_value']);

                //�Ըա�û����Թ���ö��
                $cmd = " select config_value from system_config where config_name='COMPETENCY_METHOD' ";
                $db->send_cmd($cmd);
                $data = $db->get_array();
                $data = array_change_key_case($data, CASE_LOWER);
                $SESS_COMPETENCY_METHOD = str_replace("|", "", trim($data['config_value']));

                $cmd = " select config_value from system_config where config_name='PKG1' ";
                $count = $db->send_cmd($cmd);
                //		$db->show_error();
                if ($count > 0) {
                    $data = $db->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    $ARR_PKG1EXIST = explode(",", $data['config_value']);
                    if (is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_PKG1EXIST)) {  //��駡�з�ǧ/���
                        for ($i = 0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++) {
                            if (in_array(md5("PKG1" . $ARR_ALL_DEPARTMENT_ID[$i]), $ARR_PKG1EXIST)) {
                                $SPKG1[$ARR_ALL_DEPARTMENT_ID[$i]] = "Y";
                                $PAGE_AUTH_GRAPH = "Y";
                            }
                        }
                    }
                } //end count

                $cmd = " select config_value from system_config where config_name='PKG2' ";
                $count = $db->send_cmd($cmd);
                //		$db->show_error();
                if ($count > 0) {
                    $data = $db->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    $ARR_PKG2EXIST = explode(",", $data['config_value']);
                    if (is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_PKG2EXIST)) {  //��駡�з�ǧ/���
                        for ($i = 0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++) {
                            if (in_array(md5("PKG2" . $ARR_ALL_DEPARTMENT_ID[$i]), $ARR_PKG2EXIST)) {
                                $SPKG2[$ARR_ALL_DEPARTMENT_ID[$i]] = "Y";
                            }
                        }
                    }
                } //end count

                $cmd = " select config_value from system_config where config_name='COMPETENCY' ";
                $count = $db->send_cmd($cmd);
                //			$db->show_error();
                if ($count > 0) {
                    $data = $db->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    $ARR_COMPETENCYEXIST = explode(",", $data['config_value']);
                    if (is_array($ARR_ALL_DEPARTMENT_ID) && is_array($ARR_COMPETENCYEXIST)) {  //��駡�з�ǧ/���
                        for ($i = 0; $i < count($ARR_ALL_DEPARTMENT_ID); $i++) {
                            if (in_array(md5("COMPETENCY" . $ARR_ALL_DEPARTMENT_ID[$i]), $ARR_COMPETENCYEXIST)) {
                                $SCOMPETENCY[$ARR_ALL_DEPARTMENT_ID[$i]] = "Y";
                            }
                        }
                    }
                } //end count

                $PAGE_AUTH_GRAPH = "Y";

                // ���������硷�͹ԡ��
                $cmd = " select config_value from system_config where config_name='E_SIGN' ";
                $db->send_cmd($cmd);
                $data = $db->get_array();
                $data = array_change_key_case($data, CASE_LOWER);
                $tmp_e_sign = explode("||", trim($data['config_value']));
                for ($i = 0; $i < count($tmp_e_sign); $i++) {
                    if ($tmp_e_sign[$i] != "")
                        $SESS_E_SIGN[$tmp_e_sign[$i]] = 1;
                }
                //��˹����  1=Ẻ�����Թ�š�û�Ժѵ��Ҫ���   2=���   3=��Ի�Թ��͹   4=˹ѧ����駼š������͹�Թ��͹ 5=˹ѧ����Ѻ�ͧ   (�ҡ C06 ��˹���������) 
//				echo "2.3. UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
//				session_register("SESS_E_SIGN");
//				echo "2.4. UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";

                $cmd = " select config_value from system_config where config_name='KPI_PER_REVIEW' ";
                $db->send_cmd($cmd);
                $data = $db->get_array();
                $data = array_change_key_case($data, CASE_LOWER);
                $SESS_KPI_PER_REVIEW = trim($data['config_value']);
//echo ">>>".$SESS_MINISTRY_NAME;
                if ($SESS_MINISTRY_NAME != "��ا෾��ҹ��") {
                    $MSG_HEADER = 'Patch Update DPIS Release 5.2.1.43';
                    
                    $MSG_DETAIL = '#��§ҹ
 -R0802 ��ª��͢���Ҫ��÷��١ŧ�ɷҧ�Թ�� ��Ѻ���͹����ǹ�ͧ����ʴ���§ҹ PDF ��� Excel �������͹�ѹ
 -R0604 �ѭ����������´�������͹����Թ��͹��͹��ѧ 5 �� ��Ѻ logic 㹡����Ҷ֧�����Ţͧ����������ҹ��дѺ�ӹѡ/�ͧ �����������ö����¡�â�������
#��û����Թ
 -K08 ��û����Թ KPI ��ºؤ�� ��Ѻ����ͧ�Է�ԡ����Ҷ֧�ͧ �������Թ��������价��Ẻ�����Թ���ǹ��� 2 ��� 2.2 ��ͧ�к�-�˵ء�ó�ĵԡ��� �����ʴ�
#��ú����ä�ҵͺ᷹ 
 -A05 ��ú�����ǧ�Թ������ҳ����͹�Թ��͹ ��Ѻ����ͧ����觤�� id 㹡���ʴ���¡�� ����������ʴ���¡����١��ͧ
��Ѻ�������
 -P0605 �ѹ�֡����ͧ�����ŧ���� ��ӡ���������٧ҹ��������ͧ�Ѻ Work From Home
 -K17 Ẻ����ͺ���§ҹ ��ӡ���������ٹ�������ͧ�Ѻ��õԴ����ҹ �óշ��ӧҹ�ҡ����ҹ
 -M0602 ��ԷԹ�ѹ��ش ��Ѻ logic 㹡��ź�������ѹ��ش �óշ�辺���㹪�ǧ���� ���ӡ������ �ӹǹ�ѹ㹻���ѵԡ���ҹ���
 -P0111 ����ѵԷҧ�Թ�� ��Ѻ�ѧ�������������ö�ӡ�úѹ�֡�������� �óշ���繢��������
 -R0205 �ç���ҧ�����Ҫ��� ��Ѻ�������ö�͡��§ҹ��ʴ�������¡����»���
 -R0802 ��ª��͢���Ҫ��÷��١ŧ�ɷҧ�Թ�� ��Ѻ�������ö���͡ ��ª��͢���Ҫ��÷��١ŧ�ɷҧ�Թ����㹡���͡��§ҹ��';
                    $MSG_SOURCE = '�ӹѡ�ҹ �.�.';
                    $MSG_POST_DATE = '2020-04-01';
                    $MSG_START_DATE = '2020-04-01';
                    $MSG_FINISH_DATE = '2020-04-30';
                    $USER_ID = 1;
                    $MSG_TYPE = 0;
                    $MSG_DOCUMENT = '';
                    $MSG_ORG_NAME = '';
                    $MSG_SHOW = 1;
                    $UPDATE_USER = 1;
                    $MSG_HEADER = 'Patch Update DPIS Release 5.2.1.43';    
                    $cmd = " select MSG_ID from PER_MESSAGE where MSG_HEADER = '$MSG_HEADER' ";
                    $count_data = $db_dpis->send_cmd($cmd);
                   
                    if (!$count_data) {
                        $cmd = " SELECT MAX(MSG_ID) as MSG_ID FROM PER_MESSAGE ";
                        $db_dpis->send_cmd($cmd);
                        //					$db_dpis->show_error();
                        //					echo "$cmd<br>";
                        $data_dpis = $db_dpis->get_array();
                        $MSG_ID = $data_dpis[MSG_ID] + 1;

                        $cmd = " INSERT INTO PER_MESSAGE(MSG_ID, MSG_HEADER, MSG_DETAIL, MSG_SOURCE, MSG_POST_DATE, MSG_START_DATE, 
										MSG_FINISH_DATE, USER_ID, MSG_TYPE, MSG_DOCUMENT, MSG_ORG_NAME, MSG_SHOW, UPDATE_USER, UPDATE_DATE)
										VALUES ($MSG_ID, '$MSG_HEADER', '$MSG_DETAIL', '$MSG_SOURCE', '$MSG_POST_DATE', '$MSG_START_DATE', 
										'$MSG_FINISH_DATE', $USER_ID, $MSG_TYPE, '$MSG_DOCUMENT', '$MSG_ORG_NAME', $MSG_SHOW, $UPDATE_USER, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
   //echo '<pre>'; die($cmd);

                        $cmd = " INSERT INTO PER_MESSAGE_USER(MSG_ID, USER_ID, MSG_STATUS, MSG_READ, UPDATE_USER, UPDATE_DATE)
										VALUES ($MSG_ID, 1, 0, NULL, $UPDATE_USER, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
                    }
                }
             
                srand((double) microtime() * 1000000);
                $session_id = md5(uniqid(rand()));
                session_id($session_id);
//				echo "3. new user UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                session_start();
//				echo "4. UID=$SESS_USERID, UNAME=$SESS_USERNAME, ULINK=$user_link_id $SESS_FIRSTNAME $SESS_LASTNAME<br>";

                manage_login(""); // ���ҧ ��¡�� user_last_access ����Ѻ �� login
//				setcookie(session_name(), session_id($session_id), time()+28800, "/");
               
                $CHK_PASS = 1;
                $BKK_FLAG = $REPORT_GEN = $COMMAND_PRT = $MSOCIETY_FLAG = $MFA_FLAG = $RTF_FLAG = 0;
                if ($SESS_MINISTRY_NAME == "��ا෾��ҹ��") {
                    $BKK_FLAG = 1;
                    if ($group_code == "BUREAU") {
                        $SESS_DEPARTMENT_ID = $PER_DEPARTMENT_ID;
                        $SESS_DEPARTMENT_NAME = $PER_DEPARTMENT_NAME;
                        $SESS_MINISTRY_ID = $PER_MINISTRY_ID;
                        $SESS_MINISTRY_NAME = $PER_MINISTRY_NAME;
                        $SESS_ORG_ID = $PER_ORG_ID;
                        $SESS_ORG_NAME = $PER_ORG_NAME;
                        $SESS_ORG_ID_1 = $PER_ORG_ID_1;
                        $SESS_ORG_NAME_1 = $PER_ORG_NAME_1;
                    }
                }
                include("../npkweb.php");

                session_register("SESS_E_SIGN");  // �����ҡ�ҡ��÷Ѵ 716
                session_register("CHK_PASS");
                session_register("SESS_USERNAME");
                
                session_register("SESS_PASSWORD_KEY");
                session_register("SESS_PASSWORD_DB");
                
                
                session_register("SESS_USERADDR");
                session_register("SESS_USERID");
                session_register("SESS_PER_ID");
                session_register("SESS_FIRSTNAME");
                session_register("SESS_LASTNAME");
                session_register("SESS_USERGROUP");
                session_register("SESS_USERGROUP_LEVEL");
                session_register("SESS_LEVEL_NO_LIST");
                session_register("SESS_PROVINCE_CODE");
                session_register("SESS_PROVINCE_NAME");
                session_register("SESS_MINISTRY_ID");
                session_register("SESS_MINISTRY_NAME");
                session_register("SESS_DEPARTMENT_ID");
                session_register("SESS_DEPARTMENT_NAME");
                session_register("SESS_ORG_ID");
                session_register("SESS_ORG_NAME");
                session_register("SESS_ORG_ID_1");
                session_register("SESS_ORG_NAME_1");
                session_register("SESS_INHERITGROUP");
                session_register("ACCESSIBLE_GROUP");
                session_register("SPKG1");
                session_register("SPKG2");
                session_register("SCOMPETENCY");
                session_register("SESS_PER_TYPE");
                session_register("SESS_ORG_STRUCTURE");
                session_register("PAGE_AUTH_GRAPH");
                session_register("SESS_ORG_SETLEVEL");
                session_register("SESS_COMPETENCY_METHOD");
                session_register("SESS_KPI_PER_REVIEW");
                
                session_register("SESS_AuditArray");/*TIME ATT*/
                session_register("SESS_PER_AUDIT_FLAG");/*TIME ATT*/
                session_register("SESS_USERGROUP_HRG");/*TIME ATT*/
				
				session_register("SESS_PER_OT_FLAG");/*TIME OT*/
				session_register("SESS_USERGROUP_OT");/*TIME OT*/
				
                

                if ($SESS_PER_ID) { //੾����͡�Թ�繤�
                    session_register("MINISTRY_ID");
                    session_register("MINISTRY_NAME");
                    session_register("DEPARTMENT_ID");
                    session_register("DEPARTMENT_NAME");
                    //session_register("ORG_ID");
                    //session_register("ORG_NAME");
                }
//				echo "PER_ID=$SESS_PER_ID $SESS_FIRSTNAME $SESS_LASTNAME<br>";
                // ���ҧ session �ѹ��ش�ѡ��ġ��
                $holi_year = (string) ((int) substr($today, 0, 4) - 10) . "-0-0";
                $cmd = " select * from PER_HOLIDAY where HOL_DATE > '$holi_year' and HOL_NAME != '�ѹ�����' and HOL_NAME != '�ѹ�ҷԵ��' ";
                $cnt_holi = $db->send_cmd($cmd);
                $arr_holi = (array) null;
                $SESS_HOLIDAY = "";
                if ($cnt_holi) {
                    while ($data = $db->get_array()) {
                        $data = array_change_key_case($data, CASE_LOWER);
//						$arr_holi[] = trim($data['hol_date'])."|".trim($data['hol_name']);
                        $arr_holi[] = trim($data['hol_date']) . "|";
                    }
                    $SESS_HOLIDAY = implode("&", $arr_holi);
                }
                session_register("SESS_HOLIDAY");
                // ��������ҧ session �ѹ��ش�ѡ��ġ��
//				echo "Location: http://$HTTP_HOST/$session_id/".$virtual_site."admin/main.html<br>";
                

                /*============= BEGIN ==============*/
                $SkipControlPassword=''; //SKIP=����ͧ��Ǩ�ͺ����ͧ��úѧ�Ѻ����¹���ʼ�ҹ ,��ҧ =�ѧ�Ѻ����¹
                if($SkipControlPassword=='SKIP'){
                    if ($MENU_TYPE == 2)
                        header("Location: /$session_id/" . $virtual_site . "admin/main_1.html");
                    else
                        header("Location: /$session_id/" . $virtual_site . "admin/main.html");
                }else{
                    if($SESS_PASSWORD_KEY==$SESS_PASSWORD_DB){
                    $ERR = 4;//die('���ʼ�ҹ�ͧ��ҹ�Ҵ������� ���ͤ�����ʹ��¡�س�����¹���ʼ�ҹ���� \\n !!! ��������ѹ��͹���Դ !!!');
                    }else if($SESS_USERNAME==$SESS_PASSWORD_KEY){
                        $ERR = 5;//die('���ʼ�ҹ�ͧ��ҹ�Ҵ������� ���ͤ�����ʹ��¡�س�����¹���ʼ�ҹ���� \\n !!!  Username ��� Password ������繤�����ǡѹ !!!');
                    }else{
                        if ($MENU_TYPE == 2)
                            header("Location: /$session_id/" . $virtual_site . "admin/main_1.html");
                        else
                            header("Location: /$session_id/" . $virtual_site . "admin/main.html");
                    }
                }
                /*============ END ==================*/
                

                
            } // end if check password last updatedate diff
        } // end if read user_detail pass == if ($result)
    }elseif ($command == "LOGIN" && $username != "" && $password == "") {
        $ERR = 1;
    } // end if
//	echo "$SESS_PER_ID | $SESS_USERID :: $SESS_MINISTRY_NAME :: $SESS_DEPARTMENT_NAME :: $SESS_ORG_NAME :: $SESS_ORG_NAME_1 :: $SESS_PROVINCE_NAME :: $SESS_ORG_SETLEVEL :: $SESS_COMPETENCY_METHOD :: $SESS_PER_TYPE :: $SESS_ORG_SETLEVEL <br>";
} //end command1 = UPDATE
?>
