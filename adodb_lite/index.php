<?
require_once 'adodb.inc.php'; 
require_once 'generate_data_dictionary.php'; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874" />
<title>Data Dictionary</title>
<style type="text/css">
<!--
.style2 {	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FF0000;
}
-->
</style>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="center">
      <td colspan="2"><table width="100%" align="center">
        <tr>
          <td width="10%" align="center"><? if($dbid) { ?><a href="javascript:form1.action='export_pdf.php'; form1.submit()"><img src="tests/images/download_f2.png" width="32" height="32" border="0" /></a><br />
            EXPORT<? } ?></td>
          <td width="90%"><table  border="0" cellpadding="5" cellspacing="1">
            <tr align="center">
              <td valign="middle"> Type</td>
              <td valign="middle">DB Name</td>
              <td valign="middle">User Name</td>
              <td valign="middle"> Password </td>
              <td valign="middle">DB Host</td>
              <td valign="middle">&nbsp;</td>
            </tr>
            <tr align="center">
              <td><select name="databasetype">
                <option value="mysql" selected="selected">MySql</option>
                <option value="mysqli">MySqli</option>
                <option value="MySqlt">MySqlt</option>
                <option value="fbsql">Front Base</option>
                <option value="maxdb">Max DB</option>
                <option value="msql">Mini Sql</option>
                <option value="mssql">Microsoft SQL</option>
                <option value="mssqlpo">Microsoft SQL Pro</option>
                <option value="postgres">Postgres</option>
                <option value="postgres64">Postgres 6.4</option>
                <option value="postgres7">Postgres 7</option>
                <option value="postgres8">Postgres 8</option>
                <option value="sqlite">SQLite</option>
                <option value="sqlitepo">SQLite Pro</option>
                <option value="sybase">SyBase</option>
                <option value="sybase_ase">SyBase ASE</option>
              </select></td>
              <td><input name="databasename" type="text" value="dpis35" size="10" /></td>
              <td><input name="dbusername" type="text" value="root" size="10" /></td>
              <td><input name="dbpassword" type="text" value="123456" size="10" /></td>
              <td><input name="dbhost" type="text" value="localhost" size="10" />
                <input name="dbid" type="hidden" id="dbid" value="<?=$dbid?>" /></td>
              <td><input type="submit" name="connect" value="Connect" /></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
    <tr align="center">
      <td width="15%" align="left" valign="top"><?=$_POST['databasename']?>
        <ul>
        <?
			if(is_array($tablename_array)) {
				foreach($tablename_array as $row_id => $row) {
					echo '<li><a href="javascript:form1.table_name.value=\''.$row['table_name'].'\';form1.table_id.value=\''.$row_id.'\'; form1.submit(); ">'. $row['table_name'].'</li>';
				}
			}
		?>
        </ul></td>
      <td width="85%" align="center" valign="top"><table width="95%">
        <tr>
          <td align="right">Table Name</td>
          <td colspan="5" align="left"><input name="table_name" type="text" id="table_name" value="<?=$_POST['table_name']?>" readonly="readonly" />
            <input type="hidden" name="table_id" id="table_id" value="<?=$_POST['table_id']?>" /></td>
        </tr>
        <tr>
          <td align="right">Description</td>
          <td colspan="5" align="left"><input type="text" name="table_desc" id="table_desc" />
            <input type="submit" name="update" id="button" value="Update Data" /></td>
        </tr>
        <?
		if(is_array($fieldname_array)) {
        foreach($fieldname_array as $field_id => $field_row) {
		?>
        <?
        	} // foreach
		} // is_array
		?>
      </table>
        <table width="95%" border="1" cellpadding="5" cellspacing="0">
        <tr align="center">
          <td width="17%" bgcolor="#EEEEEE"><strong>Field Name</strong></td>
          <td width="20%" bgcolor="#EEEEEE"><strong>Data Type</strong></td>
          <td width="5%" bgcolor="#EEEEEE"><strong>Null</strong></td>
          <td width="25%" bgcolor="#EEEEEE"><strong>Description</strong></td>
          <td width="8%" bgcolor="#EEEEEE"><strong>Index</strong></td>
          <td width="25%" bgcolor="#EEEEEE"><strong>Referance</strong></td>
        </tr>
		<?
		if(is_array($fieldname_array)) {
        foreach($fieldname_array as $field_id => $field_row) {
		?>
        <tr>
          <td><input type="text" name="field_name[<?=$field_id?>]" id="field_name[<?=$field_id?>]" value="<?=$field_row['field_name']?>" readonly="readonly" /></td>
          <td><input type="text" name="field_prop[<?=$field_id?>]2" id="field_name[<?=$field_id?>]2" value="<?=$field_row['field_prop'] ?>" readonly="readonly" />            </td>
          <td align="center"><input type="checkbox" name="field_null[<?=$field_id?>]" id="field_null[<?=$field_id?>]" value="NULL" <? if($field_row['field_null'] == 'NULL') {?>checked<? } ?> /></td>
          <td><input type="text" name="field_desc[<?=$field_id?>]" id="field_desc[<?=$field_id?>]" value="<?=$field_row['field_desc']?>" /></td>
          <td><input name="field_index[<?=$field_id?>]" type="text" id="field_index[<?=$field_id?>]" size="10" value="<?=$field_row['field_index']?>" /></td>
          <td><input type="text" name="field_refer[<?=$field_id?>]" id="field_refer[<?=$field_id?>]" value="<?=$field_row['field_refer']?>" /></td>
        </tr>
        <?
        	} // foreach
		} // is_array
		?>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
</body>
</html>