<?php

	if(!$_GET['gid']) 
	{
		include("../../php_scripts/connect_database.php");
	} 
	if($_GET['gid']) {
		// update process
		$selected_field_array = get_aliasname_selected($_GET['gid']);
	}
	$_alias_array = get_aliasname_with_aid();
	if(is_array($selected_field_array)) {
		$_alias_array = array_diff_assoc($_alias_array,$selected_field_array);
		$selected_option = create_option_array($selected_field_array);
	}
	
	$field_option1 = create_option_array($_alias_array);
	
	
	//echo "<pre>";print_r($_alias_array);print_r($selected_field_array);echo "</pre>";
	
	echo '
	<table class="normal_black">
      <tr class="table_body">
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
        <td colspan="3"><input type="submit" name="step2" id="step2" value="Done" onclick="resetStep(\'2\');placeInHidden(\';\', \'field2\', \'hidSelectedField\');sendRequest(\'php_scripts/xrpt_easy_condition.php\',document.form1.hidSelectedField.value,\'DIV_step3\')" />
          </label></td>
      </tr>
    </table>
	';

?>