<?
		$cmd = "select config_name, config_value from system_config";
		$db->send_cmd($cmd);
//		$db->show_error();
		while($data = $db->get_array()){ 
			$data = array_change_key_case($data, CASE_LOWER);
			${$data[config_name]} = $data[config_value];
//			echo "other-".$data[config_name].":".$data[config_name]."<br>";
		} // end while
		$RPT_N = "N";
		
		$ARR_LANGUAGE = explode(",", $LANGUAGE_SET);
?>