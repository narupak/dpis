<?php 
if($SESS_USERGROUP==1){
    function formatBytes($size) {
        $base = log($size) / log(1024);
        $suffix = array("", "k", "M", "G", "T");
        return round(pow(1024, $base - floor($base)), 2) . ' ' . $suffix[floor($base)];
    }
    function getOSServerName(){
        //Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
        $php_uname_val= php_uname();
        $posBegin = strpos($php_uname_val, "(")+1;
        $posEnd = strpos($php_uname_val, ")")-$posBegin;
        return substr($php_uname_val,$posBegin,$posEnd);
    }
   $valphpversion=str_replace(".",'',phpversion());
   if($valphpversion>=530){
       $COMPUTER_NAME = @gethostname();
   }else{
       $COMPUTER_NAME = "<font color=red>function gethostname() ไม่รองรับ</font>";;
   }
   
    
    
    
    $HTTP_HOST = $_SERVER['HTTP_HOST'];
    $SERVER_SOFTWARE = $_SERVER['SERVER_SOFTWARE'];
    $SERVER_PORT = $_SERVER['SERVER_PORT'];
    $HTTP_USER_AGENT = getOSServerName();//$_SERVER['HTTP_USER_AGENT'];
    $CURRENT_PHP_VERSION = phpversion();
    
    $db_dpis_info = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    if($db_type=="oci8"){
        $DATABASE_TYPE="ORACLE";
        $cmd = 'SELECT BANNER FROM V$VERSION ';
        $db_dpis_info->send_cmd($cmd);
        $data_info = $db_dpis_info->get_array();
        $BANNER = trim($data_info['BANNER']);
    }
    if($db_type=="mysql"){
        $DATABASE_TYPE="MYSQL";
        $cmd = "SELECT VERSION() AS BANNER";
        $db_dpis_info->send_cmd($cmd);
        $data_info = $db_dpis_info->get_array();
        $BANNER = trim($data_info['BANNER']);
    }
    if($db_type=="odbc"){$DATABASE_TYPE="ODBC";$BANNER="-";}
    $cmd = 'SELECT MSG_HEADER FROM PER_MESSAGE ORDER BY msg_id desc ';
    $db_dpis_info->send_cmd($cmd);
    $data_info = $db_dpis_info->get_array();
    $MSG_HEADER = trim($data_info['MSG_HEADER']);
    
    $DATABASE_HOST=$dpisdb_host;
    $DATABASE_NAME=$dpisdb_name;
    
    $DISK_TOTAL = formatBytes(disk_total_space('/'));
    $DISK_USE = formatBytes(disk_total_space('/') - disk_free_space('/'));
    $DISK_FREE = formatBytes(disk_free_space('/'));
    
}else{
    echo "<font color=red><center><h1>คำเตือน!!! คุณไม่ได้รับสิทธิ์ให้เข้าถึงข้อมูลในไฟล์นี้</h1></center></font>";
    die();
}
    
    
?>
