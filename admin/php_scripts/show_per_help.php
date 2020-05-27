<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select		HELP_NAME, HELP_DESC
					 from		PER_HELP
					 where	HELP_ID_REF=$HELP_ID_REF and HELP_ID=$HELP_ID
				  ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
//	echo "Data :: <pre>"; print_r($data); echo "</pre>";
	$HELP_NAME = $data[HELP_NAME];
	$HELP_DESC = str_replace("\r", "<br>", str_replace("\n", "", $data[HELP_DESC]));
	
	function list_tree_node ($pre_image, $node_parent, $sel_node_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_NODE, $START_NODE_ID;
		
		$opened_node = substr($LIST_OPENED_NODE, 1, -1);
		$arr_opened_node = explode(",", $opened_node);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select HELP_ID , HELP_NAME, HELP_ID_REF from PER_HELP where HELP_ID_REF = $node_parent order by HELP_ID ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"black_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select HELP_ID from PER_HELP where HELP_ID_REF=". $data[HELP_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[HELP_ID] == $sel_node_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_node(". $data[HELP_ID] .");";
				if(in_array($data[HELP_ID], $arr_opened_node)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_node(". $data[HELP_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_node(". $data[HELP_ID] .",". $data[HELP_ID_REF] .");\" style=\"cursor:hand\">" . $data[HELP_NAME] . "</span></td>";
				echo "</tr>";
				
				if($count_sub_tree && in_array($data[HELP_ID], $arr_opened_node)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[HELP_ID], $arr_opened_node)) $display = "block";
					echo "<div id=\"DIV_". $data[HELP_ID] ."\" style=\"display:$display\">";
					list_tree_node("", $data[HELP_ID], $sel_node_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function
?>d_node)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					ec