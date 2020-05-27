<?php
	
	include("../../php_scripts/connect_database.php");
	include("xrpt_lib.php");

	$table_name = $_POST['param'];

	// get primary key from rpt_aliasname
	// for create forein key
	$sql = "select * from rpt_aliasname where pk_flag = '1'";
	$db->send_cmd($sql);
	while($rows = $db->get_array()) {
		$_fname = $rows['fname'];
		$_aid = $rows['aid'];
		if($rows['aname']) $_aname = $rows['aname'];
		else $_aname = $rows['fname'];
		$_rows[$_fname][name] = $_aname;
		$_rows[$_fname][id] = $_aid;
		list($temp_tname,$_fname) = explode('_xfieldx_',$_fname);
		
		$_pk_rows[$_aid][fname] = $_fname;
		$_pk_rows[$_aid][aname] = $_aname;		
	}
	
	$sql = "select aid, fname,pk_flag,from_field
					from rpt_aliasname left join rpt_fk
						on rpt_aliasname.aid = rpt_fk.to_field 
					where tname = '$table_name'";
	$db->send_cmd($sql);
	while($temp = $db->get_array()) {
		$option_pk = create_option_pk($temp['from_field'],$_rows);
		//if($temp['pk_flag'] != '1') {
			$_aid = $temp['aid'];
			list($temp_tname,$_fname) = explode('_xfieldx_',$temp['fname']);
			$rows .= '
			  <tr class="table_body_2">
				<td>'.$_fname.'</td>
				<td nowrap="nowrap">
					<select name="pk_'.$_aid.'" id="pk_'.$_aid.'" onchange="setHidden(\';\',\''.$_aid.'\',\'hidField\')"><option value="0">Select Primary Key</option>'
					.$option_pk.'</select>
				</td>
			  </tr>
			';
		//}
	}
	if(empty($rows)) { 
		$rows = '<tr class="table_body_2"><td colspan="2">Please goto <a href="xrpt_set_aliasname.html">Set aliasname</a></td></tr>'; 
	}
	echo '
	<table border="0" class="table_body">
	 <tr>
	  <th colspan="2" scope="col"> Set Forein Key [ '.$table_name.' ]</th>
	  </tr>
	<tr>
	  <th scope="col">Field</th>
	  <th scope="col">Forein Key</th>
	  </tr>
	  '.$rows.'
      <tr>
        <td colspan="2">
		<input type="text" name="hidField" id="hidField" value="'.$all_fields.'" />
		<input type="submit" name="save_alias" id="save_alias" value="Save Alias" onclick="joinValueHidden(\';\',\'hidField\',\'hidJoinField\');sendRequest(\'php_scripts/xrpt_save_relationship.php\',document.form1.hidJoinField.value,\'DIV_step3\')" /></td>
      </tr>	
      </table>
	';

?>