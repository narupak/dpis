<?php

	if(!$_GET['gid']) {
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 

	if($_GET['gid']) {
		// update process
		$table_array = get_tname($_GET['gid']);
		$selected_field_array = get_fname($_GET['gid']);
	} else {
		$table_array = explode(';',$_POST['param']);
	}
	
	foreach($table_array as $table_current) {
		$sql = "SELECT * FROM ".$table_current;
		$db_dpis->send_cmd($sql);
		$temp_array = $db_dpis->get_array();
		foreach($temp_array as $key => $value) {
			//echo $key . "==";
			//if(is_int($key)) continue;
			$field_array[$table_current][] = $key;
		}
	}
	if(is_array($selected_field_array)) {
		foreach($table_array as $_tname) {
			$field_array[$_tname] = array_diff($field_array[$_tname], $selected_field_array[$_tname]);	
		}
		$selected_option = create_option_field($selected_field_array);
	} 
	$field_option1 = create_option_field($field_array);
	echo '
	<table>
      <tr>
        <td>All Fields </td>
        <td>&gt;&gt;</td>
        <td>Selected Field(s)</td>
      </tr>
      <tr>
        <td><select name="field1" size="10" multiple="multiple" id="field1" ondbclick="field.init(document.forms[0]);field.transferRight()">
		'.$field_option1.'
        </select></td>
        <td align="center"><input type="button" name="add2" id="add2" value="&gt;" onclick="field.init(document.forms[0]);field.transferRight()" />
          <br />
          <input type="button" name="add_all2" id="add_all2" value="&gt;&gt;" onclick="field.init(document.forms[0]);field.transferAllRight()" />
          <br />
          <input type="button" name="del2" id="del2" value="&lt;" onclick="field.init(document.forms[0]);field.transferLeft()" />
          <br />
          <input type="button" name="del_all2" id="del_all2" value="&lt;&lt;" onclick="field.init(document.forms[0]);field.transferAllLeft()" /></td>
        <td><select name="field2" size="10" multiple="multiple" id="field2" ondbclick="field.init(document.forms[0]);field.transferLeft()">
		'.$selected_option.'
        </select>
		<input type="hidden" name="hidSelectedField" id="hidSelectedField" /></td>
      </tr>
      <tr>
        <td colspan="3"><input type="submit" name="step2" id="step2" value="Done" onclick="resetStep(\'2\');placeInHidden(\';\', \'field2\', \'hidSelectedField\');sendRequest(\'php_scripts/xrpt_adv_condition.php\',document.form1.hidSelectedField.value,\'DIV_step3\')" />
          </label></td>
      </tr>
    </table>
	';

?>