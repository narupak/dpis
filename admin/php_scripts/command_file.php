<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/upload_command_file.php");			//�Ѵ�������ǡѺ���

//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//���ҧ����������Ѻ�����
	$FILE_PATH = $PATH_MAIN."/".$CATEGORY."/".$LAST_SUBDIR;
	//���ͷ����ŧ� database
	$file_namedb = $CATEGORY."_".$LAST_SUBDIR;
	echo "PATH_MAIN=".$PATH_MAIN."/PER_CARDNO=".$PER_CARDNO."/ CATEGORY=".$CATEGORY."/ LAST_SUBDIR=".$LAST_SUBDIR." (file_namedb=$file_namedb)<br>";
	//=====================//
	switch($CATEGORY){
		case "PER_COMMAND" :
			$TITLE = "�͡���Ṻ";
		break;
	} // end switch case
?>