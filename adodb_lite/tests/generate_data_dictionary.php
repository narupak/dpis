<?php
if(empty($_POST['databasetype']))
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>ADOdb Lite Test Program</title>
<style type="text/css">
<!--
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FF0000;
}
.style10 {
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-weight: bold; 
	font-size: 16px; 
	color: #000000; 
}
.style11 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<table width="800"  border="1" cellspacing="1" cellpadding="5" align="center">
  <tr>
    <td><div align="center">
      <p>
        <img src="adodblite_thumb.jpg" border="0" title="ADOdb Lite Thumbnail Logo" alt="ADOdb Lite Thumbnail Logo"><br><br><span class="style10"><u>ADOdb Lite Test Program</u></span><br>
      </p>
    </div></td>
  </tr>
  <tr>
    <td valign="top">
<form action="generate_data_dictionary.php" method="POST" enctype="multipart/form-data">
	<table width="80%"  border="0" align="center" cellpadding="10" cellspacing="1">
      <tr>
        <td valign="middle"><div align="right"><span class="style2">Select your Database Type</span></div></td>
        <td valign="middle"><div align="left"><span class="style2">
            <select name="databasetype">
                <option value="mysql" selected>MySql</option>
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
            </select>
        </span></div></td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Load the Transaction Module</span></div></td>
        <td width="50%"><input type="radio" name="transactions" value="" checked>No&nbsp;&nbsp;-&nbsp;&nbsp;<input type="radio" name="transactions" value="transaction:">Yes</td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Load the ADOdb Lite Module</span></div></td>
        <td width="50%"><input type="radio" name="adodblite" value="" checked>No&nbsp;&nbsp;-&nbsp;&nbsp;<input type="radio" name="adodblite" value="adodblite:">Yes</td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Load the Extend Module</span></div></td>
        <td width="50%"><input type="radio" name="extend" value="" checked>No&nbsp;&nbsp;-&nbsp;&nbsp;<input type="radio" name="extend" value="extend:">Yes</td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Load the Date Module</span></div></td>
        <td width="50%"><input type="radio" name="date" value="" checked>No&nbsp;&nbsp;-&nbsp;&nbsp;<input type="radio" name="date" value="date:">Yes</td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Test DSN Connection</span></div></td>
        <td width="50%"><input type="radio" name="dsn_connection" value="0" checked>No&nbsp;&nbsp;-&nbsp;&nbsp;<input type="radio" name="dsn_connection" value="1">Yes</td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Database Name</span></div></td>
        <td width="50%"><input type="text" name="databasename"></td>
      </tr>
      <tr>
        <td width="50%"><div align="right"><span class="style2">Database User Name</span></div></td>
        <td>
          <input type="text" name="dbusername">
        </td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2"> Database User Password </span></div></td>
        <td><input type="text" name="dbpassword"></td>
      </tr>
      <tr>
        <td><div align="right"><span class="style2">Database Host </span></div></td>
        <td><input type="text" name="dbhost" value="localhost"></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center">
          <input type="submit" name="Submit Form" value="Submit">
        </div></td>
        </tr>    </table>
</form>
</td>
  </tr>
  </table>
<?php
}
else
{

if(empty($_POST['dbhost']) || empty($_POST['dbusername']) || empty($_POST['databasename']))
{
	header("location: test_adodb_lite.php?databasetype= \n");
	die();
}

require_once '../adodb.inc.php'; 

if($_POST['dsn_connection'])
{
	$dsn = $_POST['databasetype'] . "://" . $_POST['dbusername'] . ":" . $_POST['dbpassword'] . "@" . $_POST['dbhost'] . "/" . $_POST['databasename'] . "#" . $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear";
	$db = ADONewConnection($dsn);
	if(!$db)
	{
		die("Could not connect to the database.");
	}
}
else
{
	$db = ADONewConnection($_POST['databasetype'], $_POST['transactions'] . $_POST['extend'] . $_POST['date'] . $_POST['adodblite'] . "pear");
	$db->createdatabase = true;
	$result = $db->Connect( $_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['databasename'] );
	if(!$result)
	{
		die("Could not connect to the database.");
	}
}

$db->debug = true;

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>ADOdb Lite Test Program</title>
<style type="text/css">
<!--
.style0 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #000000;
}
.style2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FF0000;
}
.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	background-color: #E9E9E9;
}
.style4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	background-color: #E9E9E9;
	color: #FF0000
}
.style5 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	background-color: #E9E9E9;
	color: #008080;
}
.style6 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	background-color: #E9E9E9;
	color: #0000FF;
}
.style7 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	background-color: #E9E9E9;
	color: #000000;
}
.style8 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	background-color: #E9E9E9;
	color: #008000;
}
.style10 {
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-weight: bold; 
	font-size: 16px; 
	color: #000000; 
}
.style11 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 9px;
	font-weight: bold;
}
.style12 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	background-color: #E9E9E9;
	color: #0000FF;
}
-->
</style>
</head>

<body>
<table width="800"  border="1" cellspacing="1" cellpadding="5" align="center">
  <tr>
    <td colspan="2" valign="middle"><div align="center">
      <p>
        <img src="adodblite_thumb.jpg" border="0" title="ADOdb Lite Thumbnail Logo" alt="ADOdb Lite Thumbnail Logo"><br><br><span class="style10"><u>ADOdb Lite Test Program</u><br>(<?=$_POST['databasetype'];?>)<br>
<?php
if($_POST['dsn_connection'])
{
	echo "<br>DSN: $dsn<br>";
}
?>
</span>      </p>
    </div></td>
  </tr>
  <tr>
    <td width="50%" valign="top"><pre>
	<? 
		$rs = $db->Execute('show tables');
		$totaltrecords=$rs->RecordCount(); 
		$getit=$rs->GetArray();
		for ($i = 0; $i < $totaltrecords; $i++ ){
			echo $getit[$i][Tables_in_dpis35] ." &nbsp;";
		}
	?></pre>
    </td>
    <td width="50%" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
<?php
// Extend Module Test Start
if($_POST['extend'])
{
?>
<?php
}
// Extend Module Test End
?>
<?php
// ADOdb Lite Module Test Start
if($_POST['adodblite'])
{
?>
<?php
}
// ADOdb Lite Module Test End
?>
<?php
// Date Module Test Start
if($_POST['date'])
{
?>
<?php
}
// Date Module Test End
?></table>
<?php
}
?>
</body>
</html>
