<?
	include($_SERVER['DOCUMENT_ROOT']."/php_scripts/connect_database.php");
	include($_SERVER['DOCUMENT_ROOT']."/php_scripts/calendar_data.php");
	include($_SERVER['DOCUMENT_ROOT']."/admin/php_scripts/function_share.php");

	$graph_type = strtolower(substr(basename($PHP_SELF), 0, -4));
	//$gid = "gra_G0010001";
	echo $gid . $graph_type;
	if(isset($gid)) include($_SERVER['DOCUMENT_ROOT']."/admin/graph/".$gid.".php");
?>