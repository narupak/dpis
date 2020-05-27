<?
	$graph_type = strtolower(substr(basename($PHP_SELF), 0, -4));
	if(isset($gid)) include($_SERVER['DOCUMENT_ROOT']."/admin/report/".$gid."");
?>