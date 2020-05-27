<?php
//Send some headers to keep the user's browser from caching the response.

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=windows-874");

//Get our database abstraction file
require('../../php_scripts/connect_database.php');
///Make sure that a value was sent.

if (isset($_GET['search']) && $_GET['search'] != '') {
	//Add slashes to any quotes to avoid SQL problems.
	$search = addslashes($_GET['search']);
	//Get every page title for the site.
	
	$cmd = "SELECT distinct(pl_name) as suggest FROM per_line WHERE pl_name like('" . $search . "%') ORDER BY pl_name";
	$db_dpis->send_cmd($cmd);
	while ( $data_dpis = $db_dpis->get_array()) {
		//Return each page title seperated by a newline.
		echo $data_dpis[SUGGEST] . "\n";
	}
}
?>