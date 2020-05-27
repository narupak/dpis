	<?php
	
	include("../../php_scripts/connect_database.php");

	$table_name = $_POST['param'];
	$sql = "SELECT * FROM rpt_aliasname where tname = '".$table_name."'";
	$db_dpis->send_cmd($sql);
	while($rows = $db_dpis->get_array()) {
		$_fname = $rows['fname'];
		if($rows['aname']) $_aname = $rows['aname'];
		else $_aname = $rows['fname'];
		$_rows[$table_name][$_fname] = $_aname;
		
	}

/*	$sql = "SELECT * FROM $table_name";
	$db_dpis->send_cmd($sql);
	$field_array = $db_dpis->get_array($sql);
	print_r($field_array);*/
	$sql = "SELECT * FROM $table_name";
	$db_dpis->send_cmd($sql);
	$field_array = $db_dpis->list_fields($table_name);
	//echo "<pre>"; print_r($field_array);	echo "</pre>";
	foreach($field_array as $row) {
		$fieldname = $row['name'];
		$ext_name = $table_name . "_xfieldx_" . $fieldname;
		if($_rows[$table_name][$ext_name]) $fieldvalue = $_rows[$table_name][$ext_name];
		else $fieldvalue = $fieldname;
		$all_fields .= ";$ext_name";
		$rows .= '
		  <tr class="table_body_2">
			<td>'.$fieldname.'</td>
			<td><input type="text" name="aname_'.$ext_name.'" id="aname_'.$ext_name.'" value="'.$fieldvalue.'" /></td>
		  </tr>
		';
	}

	echo '
	<table border="0" class="label_normal" cellpadding="3" cellspacing="2">
      <tr>
        <td colspan="2" align="center">Table name : '.$table_name.'</td>
      </tr>	
      <tr align="center" class="table_body">
        <td>Field Name</td>
        <td>Alias Name</td>
      </tr>
	  '.$rows.'
      <tr>
        <td colspan="2">
		<input type="text" name="hidField" id="hidField" value="'.$all_fields.'" />
		<input type="submit" name="save_alias" id="save_alias" value="Save Alias" onclick="joinValueHidden(\';\',\'hidField\',\'hidJoinField\');sendRequest(\'php_scripts/xrpt_save_aliasname.php\',document.form1.hidJoinField.value,\'DIV_step3\')" /></td>
      </tr>	
      </table>
	';

?>