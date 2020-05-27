<?php
//phpinfo();
if ($SESS_USERGROUP == 1) {
    $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
    $SERVER_SOFTWARE = $_SERVER['SERVER_SOFTWARE'];
    
   

    
    function getOSServerName(){
        //Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36
        $php_uname_val= php_uname();
        //echo $php_uname_val;
         $poslen =  strpos($php_uname_val, "(");
	if(empty($poslen )){
		return  $php_uname_val;
	}else{
		$posBegin = strpos($php_uname_val, "(")+1;
        		$posEnd = strpos($php_uname_val, ")")-$posBegin;
        		return substr($php_uname_val,$posBegin,$posEnd);
	}
    }
  
    function getOSServer() {
       
        $php_uname_val= php_uname();

        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 10/i' => 'WIN', /* Windows 10 */
            '/windows nt 6.3/i' => 'WIN', /* 'Windows 8.1', */
            '/windows nt 6.2/i' => 'WIN', /* 'Windows 8', */
            '/windows nt 6.1/i' => 'WIN', /* 'Windows 7', */
            '/windows nt 6.0/i' => 'WIN', /* 'Windows Vista', */
            '/windows nt 5.2/i' => 'WIN', /* 'Windows Server 2003/XP x64', */
            '/windows nt 5.1/i' => 'WIN', /* 'Windows XP', */
            '/windows xp/i' => 'WIN', /* 'Windows XP', */
            '/windows nt 5.0/i' => 'WIN', /* 'Windows 2000', */
            
            '/windows me/i' => 'WIN', /* 'Windows ME', */
            '/win98/i' => 'WIN', /* 'Windows 98', */
            '/win95/i' => 'WIN', /* 'Windows 95', */
            '/win16/i' => 'WIN', /* 'Windows 3.11', */
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, "Win")) {
                $os_platform = $value;
            }
        }
        return $php_uname_val;
    }
    
     //echo ()."<br>";
     if(strpos(strtoupper(getOSServer()),"WIN")>=0){
         $OSNAME="<font color=green><b>".getOSServerName()." (Supported)</b></font>";
         $OSNAME_RECOM="";
     }else{
         $OSNAME="<font color=red><b>".getOSServerName()." (UnSupported)</b></font>";
         $OSNAME_RECOM="Window xp (ขั้นต่ำ)";
     }
     
    
    
    function getOSName(){
        global $user_agent;
        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }
    function getOS() {
        global $user_agent;

        $os_platform = "Unknown OS Platform";

        $os_array = array(
            '/windows nt 10/i' => 'WIN', /* Windows 10 */
            '/windows nt 6.3/i' => 'WIN', /* 'Windows 8.1', */
            '/windows nt 6.2/i' => 'WIN', /* 'Windows 8', */
            '/windows nt 6.1/i' => 'WIN', /* 'Windows 7', */
            '/windows nt 6.0/i' => 'WIN', /* 'Windows Vista', */
            '/windows nt 5.2/i' => 'WIN', /* 'Windows Server 2003/XP x64', */
            '/windows nt 5.1/i' => 'WIN', /* 'Windows XP', */
            '/windows xp/i' => 'WIN', /* 'Windows XP', */
            '/windows nt 5.0/i' => 'WIN', /* 'Windows 2000', */
            '/windows me/i' => 'WIN', /* 'Windows ME', */
            '/win98/i' => 'WIN', /* 'Windows 98', */
            '/win95/i' => 'WIN', /* 'Windows 95', */
            '/win16/i' => 'WIN', /* 'Windows 3.11', */
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }
    $valphpversion = str_replace(".", '', phpversion());
    if ($valphpversion >= 530) {
        $COMPUTER_NAME = @gethostname();
        $PHPVERSION = "<font color=green><b>".phpversion()." (Supported)</b></font>";
        $PHPVERSION_RECOM="";
    } else {
        $COMPUTER_NAME = "<font color=red>function gethostname() ไม่รองรับ</font>";
        $PHPVERSION = "<font color=red><b>".phpversion()." (UnSupported)</b></font>";
        $PHPVERSION_RECOM="5.3.13";
    }
   
    /*if(getOS()=='WIN'){
        $OSNAME="<font color=green><b>".getOSServer()." (Supported)</b></font>";
    }else{
        $OSNAME="<font color=red><b>".getOSName()." (UnSupported)</b></font>";
    }*/
    if($db_type=='mysql'){
        $DBNAME="<font color=red><b>Mysql (UnSupported)</b></font>";
        $DBNAME_RECOM="Oracle Version 11.2.0.1.0";
    }else{
        $DBNAME="<font color=red><b>UNKNOW</b></font>";
        if($db_type=='oci8'){
            $db_dpis_info = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
            $cmd = 'SELECT BANNER FROM V$VERSION WHERE BANNER LIKE \'%NLSRTL%\' ';
            $db_dpis_info->send_cmd($cmd);
            $data_info = $db_dpis_info->get_array();
            $BANNER = explode('NLSRTL', trim($data_info['BANNER'])) ;
            $BANNERVER = explode(' ',$BANNER[1]);
            $VersionNum = str_replace('.','', $BANNERVER[2]) ;
            if($VersionNum<112010){
                $DBNAME="<font color=red><b>".trim($BANNER[1])." (UnSupported)</b></font>";
                $DBNAME_RECOM="Version 11.2.0.1.0";
            }else{
                $DBNAME="<font color=green><b>".trim($BANNER[1])." (Supported)</b></font>";
                $DBNAME_RECOM="";
            }
        }
    }
    $SERVER_SOFTWARE_ARR = explode(' ',$SERVER_SOFTWARE);
    $SERVER_SOFTWARE_NUM = str_replace('.','',explode('/',$SERVER_SOFTWARE_ARR[0]));
    if($SERVER_SOFTWARE_NUM<2222){
        $SERVER_SOFTWARE ="<font color=red><b>".$SERVER_SOFTWARE_ARR[0]." (UnSupported)</b></font>";
        $SERVER_SOFTWARE_RECOM="Apache/2.2.22";
    }else{
        $SERVER_SOFTWARE ="<font color=green><b>".$SERVER_SOFTWARE_ARR[0]." (Supported)</b></font>";
        $SERVER_SOFTWARE_RECOM="";
    }
} else {
    echo "<font color=red><center><h1>คำเตือน!!! คุณไม่ได้รับสิทธิ์ให้เข้าถึงข้อมูลในไฟล์นี้</h1></center></font>";
    die();
}
?>
