<?
		$cmd = " select 	config_name, config_value
						 from		template_config
						 where	group_id = $TEMPLATE_GROUP
 					   ";
		$db->send_cmd($cmd);
//		$db->show_error();
		while($data = $db->get_object()){ 
			${$data->config_name} = $data->config_value;
		} // end while
		
		$ARR_TEMPLATE_STYLE = explode(",", $STYLE_SET);
?>