<?php

	if(!$_GET['gid']) 
	{
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 

	if($_POST['gid']) {
		$sql = "delete from rpt_easy_header where gid = " . $_POST['gid'];
		$db->send_cmd($sql);
		$sql = "delete from rpt_easy_detail where gid = " . $_POST['gid'];
		$db->send_cmd($sql);
	}

	// process variable for prepare insert 
	foreach($_POST as $var_name => $var_value) {
		if($var_name == 'param' || $var_name == 'report_name' || $var_name == 'report_title' ) 
			continue;
		list($var_type,$var_tablefield) = explode("_",$var_name,2);
		$xaid_array[]=$var_tablefield;
		switch($var_type) {
			case 'match' :		
							$field_array[$var_tablefield]['filter_match'] = $var_value; 
							break;
					
			case 'operator' :
							$field_array[$var_tablefield]['filter_operator'] = $var_value; 
							break;
					
			case 'value' : 	
							$field_array[$var_tablefield]['filter_value'] = $var_value; 
							break;
			
			case 'show' :	
							$field_array[$var_tablefield]['header_show'] = $var_value; 
							break;
			
			case 'text' : 	$field_array[$var_tablefield]['header_text'] = $var_value; 
							break;
			
			case 'seq' : 	$field_array[$var_tablefield]['header_seq'] = $var_value; 
							break;
			
			case 'ASC': case 'DESC' : 
							$field_array[$var_tablefield]['order_type'] = $var_value; 
							break;
			case 'index' :		
							$field_array[$var_tablefield]['order_index'] = $var_value; 
							break;
		}
	}
	$xfields_array = get_fname_with_aid($xaid_array);
	$xtable_array = get_tname_with_aid($xaid_array);

	// insert data 
	// General
	$sql = "select max(gid) from rpt_easy_header";
	$db->send_cmd($sql);
	$result = $db->get_array();
	$gid = $result[0]+1;
	$sql = "insert into rpt_easy_header (gid,gname,gheader) values ('$gid','$_POST[report_name]','$_POST[report_title]')";
	$db->send_cmd($sql);
	
	//$gid = $db->get_insert_id();
	
	// field
	foreach($field_array as $fieldname => $field_row) {
		$sql = "insert into rpt_easy_detail (gid,aid,header_show,header_text,header_seq,filter_operator,filter_value".
				",filter_match,order_type,order_index) values ('$gid','$fieldname','".$field_row['header_show']."','".$field_row['header_text']."',".
				"'".$field_row['header_seq']."','".$field_row['filter_operator']."','".$field_row['filter_value']."',".
				"'".$field_row['filter_match']."','".$field_row['order_type']."','".$field_row['order_index']."')";
		$db->send_cmd($sql);
		
		//echo $field_array[$fieldname]['header_show'] . " <= show = $fieldname +++" . $field_row['header_text'] . "<br>";
		
		if($field_array[$fieldname]['header_show'])
			$fields_array[$field_row['header_seq']] = field_process($xfields_array[$fieldname],$field_row['header_text']);
		if($field_row['filter_value'] || $field_row['filter_match']) 
			$condition_array[] = condition_process($xfields_array[$fieldname],$field_row['filter_operator'],$field_row['filter_value'],$field_row['filter_match']);
		if($field_row['order_type']) 
			$order_array[$field_row['order_index']] = order_process($xfields_array[$fieldname],$field_row['order_type']);
	}
	// procress from array to SQL Command
	if($join_string = join_process($xtable_array)) {
		$sql = "select ".implode_ksort(',',$fields_array)." from ".$join_string;
	} else {
		$sql = "select ".implode_ksort(',',$fields_array)." from ".implode(',',$xtable_array);
	}
	if(count($condition_array))	
		$sql = $sql . " where " . implode(' and ',$condition_array);
	if(count($order_array))
		$sql = $sql . " order by " . implode_ksort(',',$order_array);
	
	$db_dpis->send_cmd($sql);
	echo "<br>$sql<br>";
	
	$field_array = $db_dpis->list_fields();
	$_col = 0;
	$col_array[$_col] = "";
	$width_pdf[$_col] = "0";
	foreach($field_array as $row) {
		$_col++;
		$header .= "<th scope=\"col\">".$row['name']."</th>";
		if($width_pdf[$_col] < strlen($row['name'])) {
			$width_pdf[$_col] = strlen($row['name']);
		}
		$col_array[$_col] = $row['name'];
	}
	$seq = 0;
	$rows = "<tr><th scope=\"col\">&nbsp;</th>$header</tr>\n";
	while($temp = $db_dpis->get_array()) {
		unset($cols);
		$seq++;
		$_col = 0;
		$array_content[$seq][$_col] = $seq;
		if($width_pdf[$_col] < strlen($value)) {
			$width_pdf[$_col] = strlen($value);
		}
		foreach($temp as $key => $value) {
			if(is_int($key)) continue;
			$_col++;
			$cols .= "<td>$value</td>";
			$array_content[$seq][$_col] = $value;
			if($width_pdf[$_col] < strlen($value)) {
				$width_pdf[$_col] = strlen($value);
			}
		}
		$rows .= "<tr><th scope=\"row\">$seq</th>$cols</tr>\n";
	}
	
	echo "<pre>";
	print_r($width_pdf);
	include("../xrpt/xrpt_excel.php");
	include("../xrpt/xrpt_pdf.php");
	echo "</pre>";
	
	echo '
	<input type="hidden" name="gid" id="gid" value="'.$gid.'" />
	<p>'.$_POST['report_title'].'</p>
	<table border="1" cellpadding="3" cellspacing="0">
	'.$rows.'
	</table>	
	<a href="'.$fname.'">View as excel</a><br>
	<a href="'.$fname_pdf.'">View as PDF</a><br>
<select name="graph_type" onchange="MM_jumpMenu(\'parent\',this,0)">
  <option value="" selected="selected">select graph type</option>
  <option value="xrpt/xrpt_graph.php?sql='.urlencode($sql).'&report_title='.$_POST['report_title'].'&graph_type=line">กราฟเส้น</option>
  <option value="xrpt/xrpt_graph.php?sql='.urlencode($sql).'&report_title='.$_POST['report_title'].'&graph_type=pie">กราฟวงกลม</option>
  <option value="xrpt/xrpt_graph.php?sql='.urlencode($sql).'&report_title='.$_POST['report_title'].'&graph_type=bar">กราฟแท่ง</option>
</select>
	';

?>