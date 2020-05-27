<?php

	if(!$_GET['gid']) 
	{
		include("../../php_scripts/connect_database.php");
				include("xrpt_lib.php");
	} 
	
	if($_GET['gid']) {
		$aliasname_array = get_aliasname_selected($_GET['gid']);	
		$selected_type = get_easy_detail_colunm($_GET['gid'],'order_type');
		$selected_index = get_easy_detail_colunm($_GET['gid'],'order_index');
		//print_a($selected_type);
		//print_a($selected_index);
	} else {
		$aid_array = explode(';',$_POST['param']);
		$aliasname_array = get_aliasname_with_aid($aid_array);
	}
	
	$index = 1;
	unset($rows);

	echo '
	<table class="normal_black">
      <tr>
        <td>Fields </td>
        <td>A->Z</td>
        <td>Z->A</td>
		<td>Order</td>
      </tr>
	';
	foreach($aliasname_array as $_aid => $_aliasname) {
		unset($selected_type_desc);unset($selected_type_asc);
		if(!$selected_index[$_aid]) $selected_index[$_aid] = $index;
		if($selected_type[$_aid] == 'DESC') $selected_type_desc = 'checked';
		if($selected_type[$_aid] == 'ASC') $selected_type_asc = 'checked';
		echo '      
		<tr>
			<td>'.$_aliasname.'</td>
			<td><input type="radio" name="order_'.$_aid.'" id="ASC_'.$_aid.'" value="ASC" '.$selected_type_asc.' /></td>
			<td><input type="radio" name="order_'.$_aid.'" id="DESC_'.$_aid.'" value="DESC" '.$selected_type_desc.' /></td>
			<td><input name="index_'.$_aid.'" type="text" id="index_'.$_aid.'" size="5" maxlength="2" value="'.$selected_index[$_aid].'" /></td>
      	</tr>
		';
		$index++;
	}
	echo '
	  <tr>
        <td colspan="3">
			<input type="text" name="hidSetField" id="hidSetField" />
			<input type="button" name="step4" id="step4" value="Done" onclick="sendRequest(\'php_scripts/xrpt_easy_report.php\',document.form1.hidSelectedField.value,\'DIV_step5\')" /></td>
      </tr>
    </table>
	';

?>