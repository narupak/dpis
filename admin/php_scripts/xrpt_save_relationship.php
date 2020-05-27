<?php
	
	include("../../php_scripts/connect_database.php");
	include("xrpt_lib.php");
	
	//print_r($_POST);
	foreach($_POST as $_to_field => $_from_field) {
		if($_to_field == 'param') continue;
		$_to_field = substr($_to_field,3);
		$sql = "select fid from rpt_fk where to_field = '$_to_field'";
		$db->send_cmd($sql);
		$temp = $db->get_array();
		//
		if($temp['fid']) {
			// update process
			$sql = "update rpt_fk set from_field = '$_from_field' where to_field = $_to_field";
		} else {
			// insert process
			$sql = "insert into rpt_fk (from_field,to_field) values ('$_from_field','$_to_field')";
		}
		echo $sql . " ++ <br>";
		$db->send_cmd($sql);
	}
	echo 'Save Success !!!';

?>	