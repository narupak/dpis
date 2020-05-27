<?php
	
	$sql = "select TABLE_NAME from tabs";
	$db_dpis->send_cmd($sql);
	$index = 0;

	while($temp = $db_dpis->get_array()) {
		$_table_array[$index] = $temp[TABLE_NAME];
		//print_a($temp);
		$index++;
	}

	if($_GET['gid']) {
		$t_array = get_tname($_GET['gid']);
		foreach($t_array as $temp) {
			$selected_table .= "<option value='".$temp."'>".$temp."</option>";
		}
		//print_a($t_array);
		$_table_array = array_diff($_table_array,$t_array);
	}
	
?>