<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/session_start.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	// read template content
	$filename = "rpt_1_1.rtf";
	$handle = fopen ($filename, "r");
	$rtf_contents = fread($handle, filesize($filename));
	fclose($handle);
//	echo $rtf_contents;

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select	COM_NO, COM_DATE from	PER_COMMAND where COM_ID=$COM_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$COM_NO = trim($data[COM_NO]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,3);

	$cmd = " select	COUNT(COM_SEQ) as QTY from PER_COMDTL where COM_ID=$COM_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$QTY = trim($data[QTY]);

	$rtf_contents = replaceRTF("@DEPARTMENT_NAME@", convert2rtfascii($DEPARTMENT_NAME), $rtf_contents);
	$rtf_contents = replaceRTF("@COM_NO@", convert2rtfascii($COM_NO), $rtf_contents);
	$rtf_contents = replaceRTF("@COM_DATE@", convert2rtfascii($COM_DATE), $rtf_contents);
	$rtf_contents = replaceRTF("@QTY@", convert2rtfascii($QTY), $rtf_contents);

	// write rtf content
	$filename = "../tmp/rpt_1_1_$SESS_USERNAME.rtf";
	$handle = fopen ($filename, "w");
	fwrite($handle, $rtf_contents);
	fclose($handle);
?>
	<meta http-equiv='refresh' content='0;URL=<?=$filename?>'>