<?
	
	include("../../php_scripts/connect_database.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//$sql = "select PER_ABSENTTYPE.AB_NAME as AB_NAME,PER_ABSENTTYPE.AB_QUOTA as AB_QUOTA from PER_ABSENTTYPE order by PER_ABSENTTYPE.AB_CODE ASC";
	$db_dpis2->send_cmd($sql);
	while($row = $db_dpis2->get_array()) 
	{
		$idx = 0;
		foreach($row as $key => $value) {
			if(!in_array($key,$arr_series_caption))
				$arr_series_caption[] = $key;
			if(!$idx) $arr_categories[] = $value;
			$arr_series[$idx][] = $value;
			$idx++;
		}
	}

/*
	$arr_series_caption = $arr_categories = array("a","b","c","d");
	$arr_series = array(array("12","23","34","45"),array("12","45","23","34"),array("34","12","23","45"));
*/
	$categories_list = implode(';',$arr_categories);
	$series_caption_list = implode(';',$arr_series_caption);
	foreach($arr_series as $row) {
		$arr_series_row[] = implode(";",$row);
	}
	$series_list =	implode("|",$arr_series_row);

	$chart_title = $report_title;
	$chart_subtitle = $company_name;
	$selectedFormat = "SWF";

	
	switch( strtolower($graph_type))
	{
		case "bar" :
			include($_SERVER['DOCUMENT_ROOT']."/graph/types/xrpt_bar.php");
			break;
		case "line" :
			include($_SERVER['DOCUMENT_ROOT']."/graph/types/xrpt_line.php");
			break;
		case "pie" :
			include($_SERVER['DOCUMENT_ROOT']."/graph/types/xrpt_pie.php");
			break;
	} //switch
	
?>