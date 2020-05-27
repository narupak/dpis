<?php

	if(!$_GET['gid']) {
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 


	if($_GET['gid']) {
		$field_array = create_var_param($_GET['gid']);	
		$selected_type = get_field_colunm($_GET['gid'],'order_type');
		$selected_index = get_field_colunm($_GET['gid'],'order_index');
	} else {
		$field_array = explode(';',$_POST['param']);
	}
	
	$index = 1;
	unset($rows);

	echo '
	<table>
      <tr>
        <td>Fields </td>
        <td>A->Z</td>
        <td>Z->A</td>
		<td>Order</td>
      </tr>
	';
	foreach($field_array as $ext_name) {
		unset($selected_type_desc);unset($selected_type_asc);
		$field = str_replace('_xfieldx_',':',$ext_name);
		if(!$selected_index[$field]) $selected_index[$field] = $index;
		if($selected_type[$field] == 'DESC') $selected_type_desc = 'checked="checked"';
		if($selected_type[$field] == 'ASC') $selected_type_asc = 'checked="checked"';
		echo '      
		<tr>
			<td>'.$field.'</td>
			<td><input type="radio" name="order_'.$ext_name.'" id="ASC_'.$ext_name.'" value="ASC" '.$selected_type_asc.' /></td>
			<td><input type="radio" name="order_'.$ext_name.'" id="DESC_'.$ext_name.'" value="DESC" '.$selected_type_desc.' /></td>
			<td><input name="index_'.$ext_name.'" type="text" id="index_'.$ext_name.'" size="5" maxlength="2" value="'.$selected_index[$field].'" /></td>
      	</tr>
		';
		$index++;
	}
	echo '
	  <tr>
        <td colspan="3">
			<input type="text" name="hidSetField" id="hidSetField" />
			<input type="button" name="step4" id="step4" value="Done" onclick="sendRequest(\'php_scripts/xrpt_adv_report.php\',document.form1.hidSelectedField.value,\'DIV_step5\')" /></td>
      </tr>
    </table>
	';

?>