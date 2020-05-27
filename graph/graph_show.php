<?
	include("../php_scripts/connect_database.php");
	$query_str = $_SERVER['QUERY_STRING'];
	$pos = strpos($query_str, "&");
	if($pos === false) $query_str = substr($query_str, 0);
	else $query_str = substr($query_str, 0, $pos);
	$query_str = base64_decode(base64_decode($query_str));
	
	$arr_temp = explode("|", $query_str);
	$display_graph_type = $arr_temp[0];
	$group_id = $arr_temp[1];
	$display_id = $arr_temp[2];

	$graph_var = "swiff_type=Win&econ_type=2&group_id=$group_id&display_id=$display_id";
	if($setWidth) $graph_var .= "&setWidth=$setWidth";
	if($setHeight) $graph_var .= "&setHeight=$setHeight";

	include("http://$HTTP_HOST/".$virtual_site."graph/types/". ucfirst($display_graph_type) .".php?$graph_var");
?>