<?php

	if(!$_GET['gid']) 
	{
		include("../../php_scripts/connect_database.php");
		include("xrpt_lib.php");
	} 
	
	if($_GET['gid']) {
		$field_array = get_aliasname_selected($_GET['gid']);
		$selected_seq = get_easy_detail_colunm($_GET['gid'],'header_seq');
		$selected_show = get_easy_detail_colunm($_GET['gid'],'header_show');
		$selected_text = get_easy_detail_colunm($_GET['gid'],'header_text');
		$temp = get_easy_header($_GET['gid']);
		$report_name = $temp['gname'];
		$report_title = $temp['gheader'];
	} else {
		$aid_array = explode(';',$_POST['param']);
		$aliasname_array = get_aliasname_with_aid($aid_array);
		$report_name = "default";
		$report_title = "default title";
	}

	$index = 1;
	unset($rows);

	echo '
	<table class="normal_black">
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
	foreach($aliasname_array as $_aid => $_aliasname) {
		//list($table,$field) = explode('_xfieldx_',$ext_name);
		$field = $_aliasname;
		$xfield = $_aid;

		if(!$selected_seq[$xfield]) $selected_seq[$xfield] = $index;
		if(!$selected_text[$xfield]) $selected_text[$xfield] = $field;
		if($selected_show[$xfield] || empty($_GET['gid'])) $selected_show[$xfield] = 'checked="checked"';
		echo '
		<tr>
			<td><input name="show_'.$xfield.'" type="checkbox" id="show_'.$xfield.'" value="1" '.$selected_show[$xfield].' /></td>
            <td><input name="seq_'.$xfield.'" type="text" id="seq_'.$xfield.'" size="5" maxlength="2" value="'.$selected_seq[$xfield].'" /></td>
            <td>'.$field.'</td>
            <td><input name="text_'.$xfield.'" type="text" id="text_'.$xfield.'" value="'.$selected_text[$xfield].'" /></td>
            </tr>
		';
		$index++;
	} 
	echo '
    </table><input type="text" name="hidJoinField" id="hidJoinField" /></td>
      </tr>
      <tr>
        <td colspan="2">
		<input type="submit" name="step5" id="step5" value="Show" onclick="joinValueHidden(\';\',\'field2\',\'hidJoinField\');sendRequest(\'php_scripts/xrpt_easy_success.php\',document.form1.hidJoinField.value,\'DIV_step6\')" />
		</td>
      </tr>
    </table>
	';

?>