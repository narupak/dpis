<?php

	if(!$_GET['gid']) {
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 


	if($_GET['gid']) {
		$field_array = create_var_param($_GET['gid']);
		$selected_seq = get_field_colunm($_GET['gid'],'header_seq');
		$selected_show = get_field_colunm($_GET['gid'],'header_show');
		$selected_text = get_field_colunm($_GET['gid'],'header_text');
		$temp = get_rpt_general($_GET['gid']);
		$report_name = $temp['gname'];
		$report_title = $temp['gheader'];
	} else {
		$field_array = explode(';',$_POST['param']);
		$report_name = "default";
		$report_title = "default title";
	}

	$index = 1;
	unset($rows);

	echo '
	<table>
      <tr>
        <td>Report Name</td>
        <td><input type="text" name="report_name" id="report_name" value="'.$report_name.'" /></td>
        </tr>
      <tr>
        <td>Report Title</td>
        <td><input type="text" name="report_title" id="report_title" value="'.$report_title.'" /></td>
        </tr>
      <tr>
        <td valign="top">Header Text</td>
        <td><table width="100%" border="0">
          <tr>
		  	<td>Show</td>
            <td>Order</td>
            <td>Fields</td>
            <td>Text</td>
            </tr>
          ';
	foreach($field_array as $ext_name) {
		list($table,$field) = explode('_xfieldx_',$ext_name);
		$xfield = str_replace('_xfieldx_',':',$ext_name);

		if(!$selected_seq[$xfield]) $selected_seq[$xfield] = $index;
		if(!$selected_text[$xfield]) $selected_text[$xfield] = $field;
		if($selected_show[$xfield] || empty($_GET['gid'])) $selected_show[$xfield] = 'checked="checked"';
		echo '
		<tr>
			<td><input name="show_'.$ext_name.'" type="checkbox" id="show_'.$ext_name.'" value="1" '.$selected_show[$xfield].' /></td>
            <td><input name="seq_'.$ext_name.'" type="text" id="seq_'.$ext_name.'" size="5" maxlength="2" value="'.$selected_seq[$xfield].'" /></td>
            <td>'.$table.':'.$field.'</td>
            <td><input name="text_'.$ext_name.'" type="text" id="text_'.$ext_name.'" value="'.$selected_text[$xfield].'" /></td>
            </tr>
		';
		$index++;
	} 
	echo '
          </table><input type="text" name="hidJoinField" id="hidJoinField" /></td>
      </tr>
      <tr>
        <td colspan="2">
		<input type="submit" name="step5" id="step5" value="Show" onclick="joinValueHidden(\';\',\'field2\',\'hidJoinField\');sendRequest(\'php_scripts/xrpt_adv_success.php\',document.form1.hidJoinField.value,\'DIV_step6\')" />
		</td>
      </tr>
    </table>
	';

?>	