<?php
	
	include("../../php_scripts/connect_database.php");
	// create array for insert 
	foreach($_POST as $key => $value) {
		if($key == 'param') continue;
		list($type,$name) = explode('_',$key,2);
		$temp_array[$name][$type] = $value;
		list($_table_name,$_field_name) = explode("_xfieldx_",$name);
	}
/*
	$sql = "SELECT * FROM ".$_table_name." limit 1";
	$db->send_cmd($sql);
	$field_array = $db->get_flags($sql);
*/	
	foreach($temp_array as $key => $filed_row) {
		list($_table_name,$_field_name) = explode("_xfieldx_",$key);
		$sql = "select aid from rpt_aliasname where fname = '$key'";
		$db->send_cmd($sql);
		$temp = $db->get_data();
		if(in_array('primary_key',$field_array[$_field_name])) {
			$pk_flag = 1;
		} else {
			$pk_flag = 0;
		}
		if($temp['aid']) {
			// update process
			$sql = "update rpt_aliasname set aname = '".$filed_row[aname]."' where aid = " . $temp['aid'];
		} else {
			// insert process
			$sql = "insert into rpt_aliasname (tname,fname,aname,pk_flag) 
				values ('$_table_name','$key','".$filed_row[aname]."','$pk_flag')";
		}
		$db->send_cmd($sql);
	}
	echo 'Save Success !!!';

?>	