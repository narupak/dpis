<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/upload_command_file.php");			//จัดการเกี่ยวกับไฟล์

//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//สร้างโฟลเดอร์สำหรับเก็บไฟล์
	$FILE_PATH = $PATH_MAIN."/".$CATEGORY."/".$LAST_SUBDIR;
	//ชื่อที่เก็บลงใน database
	$file_namedb = $CATEGORY."_".$LAST_SUBDIR;
	echo "PATH_MAIN=".$PATH_MAIN."/PER_CARDNO=".$PER_CARDNO."/ CATEGORY=".$CATEGORY."/ LAST_SUBDIR=".$LAST_SUBDIR." (file_namedb=$file_namedb)<br>";
	//=====================//
	switch($CATEGORY){
		case "PER_COMMAND" :
			$TITLE = "เอกสารแนบ";
		break;
	} // end switch case
?>