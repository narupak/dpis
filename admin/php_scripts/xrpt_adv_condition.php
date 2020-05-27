<?php

	if(!$_GET['gid']) {
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 

	if($_GET['gid']) {
		$field_array = create_var_param($_GET['gid']);
		$selected_operator = get_field_colunm($_GET['gid'],'filter_operator');
		$selected_value = get_field_colunm($_GET['gid'],'filter_value');
		$selected_match = get_field_colunm($_GET['gid'],'filter_match');
		//echo $
	} else {
		$field_array = explode(';',$_POST['param']);
	}
	$xfield_array = $field_array;
	foreach($field_array as $ext_name) {
		$field = str_replace('_xfieldx_',':',$ext_name);
		
		$rows .= '
		<tr>
			<td>'.$field.'</td>
			<td>
				<select name="operator_'.$ext_name.'" id="operator_'.$ext_name.'">
				<option value="0">Select Operator</option>
				'.create_option_operator($selected_operator[$field]).'
				</select>
			</td>
			<td>
			  <input type="text" name="value_'.$ext_name.'" id="value_'.$ext_name.'" value="'.$selected_value[$field].'" />
			</td>
			<td>
			  	<select name="match_'.$ext_name.'" id="match_'.$ext_name.'">
				<option value="0">Select Field</option>
				'.create_option_match($selected_match[$field],$xfield_array).'
				</select>
			</td>
      	</tr>
		';
	}

	echo '
	<table>
      <tr>
        <td>Fields </td>
        <td>Operator</td>
        <td>Value</td>
      </tr>
	'.$rows.'
	  <tr>
        <td colspan="3">
			<input type="text" name="hidSetField" id="hidSetField" />
			<input type="button" name="step3" id="step3" value="Done" onclick="resetStep(\'3\');sendRequest(\'php_scripts/xrpt_adv_sort.php\',document.form1.hidSelectedField.value,\'DIV_step4\')" /></td>
      </tr>
    </table>
	';

?>	