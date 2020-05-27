<?php

	if(!$_GET['gid']) {
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 
	if($_GET['gid']) {
		$aliasname_array = get_aliasname_selected($_GET['gid']);
		$selected_operator = get_easy_detail_colunm($_GET['gid'],'filter_operator');
		$selected_value = get_easy_detail_colunm($_GET['gid'],'filter_value');
		$selected_match = get_easy_detail_colunm($_GET['gid'],'filter_match');
		//print_a($selected_match);
	} else {
		$aid_array = explode(';',$_POST['param']);
		$aliasname_array = get_aliasname_with_aid($aid_array);
		//$selected_match = $selected_value = $selected_operator = array();
	}
	$xaliasname_array = $aliasname_array;
	foreach($aliasname_array as $_aid => $_aliasname) {
		
		$rows .= '
		<tr>
			<td>'.$_aliasname.'</td>
			<td>
				<select name="operator_'.$_aid.'" id="operator_'.$_aid.'">
				<option value="0">Select Operator</option>
				'.create_option_operator($selected_operator[$_aid]).'
				</select>
			</td>
			<td>
			  <input type="text" name="value_'.$_aid.'" id="value_'.$_aid.'" value="'.$selected_value[$_aid].'" />
			</td>
			<td>
			  	<select name="match_'.$_aid.'" id="match_'.$_aid.'">
				<option value="0">Select Field</option>
				'.create_option_array($xaliasname_array,$selected_match[$_aid]).'
				</select>
			</td>
      	</tr>
		';
	}

	echo '
	<table class="normal_black">
      <tr>
        <td>Fields </td>
        <td>Operator</td>
        <td>Value</td>
      </tr>
	'.$rows.'
	  <tr>
        <td colspan="3">
			<input type="text" name="hidSetField" id="hidSetField" />
			<input type="button" name="step3" id="step3" value="Done" onclick="resetStep(\'3\');sendRequest(\'php_scripts/xrpt_easy_sort.php\',document.form1.hidSelectedField.value,\'DIV_step4\')" /></td>
      </tr>
    </table>
	';

?>