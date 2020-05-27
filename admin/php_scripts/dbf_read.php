<?
$tablename= "moph/moph.dbf"; //WARNING !!! CASE SENSITIVE APPLIED !!!!!

$tablename = stripcslashes($tablename);
?>
<table width="100%"  border="0" cellpadding="1" cellspacing="1" class="normal">
  <tr>
 <td><strong>&nbsp;Description</strong> : <?=$tabledes?></td>
  </tr>
  <tr>
 <td><strong>&nbsp;File</strong> : <?=$tablename?></td>
  </tr>
</table>

<?
if(file_exists($tablename)){ 
function get_dbf_header($dbfname){
	global $tdHeader;	
	$fdbf = fopen($dbfname,'r');
	$buff32 = array();
	$arrHeader = array();
	$i = 1;
	$index = 0;
	$goon = true;
	while ($goon) {
		if (!feof($fdbf)) {
		$buff32 = fread($fdbf,32);
		if ($i > 1) {
			if (substr($buff32,0,1) == chr(13)) {
				$goon = false;
				}else{//if (substr($buff32,0,1) == chr(13)) {
				$pos = strpos(substr($buff32,0,10),chr(0));
				$pos = ($pos == 0?10:$pos);
				$fieldname = substr($buff32,0,$pos);
				$fieldtype = substr($buff32,11,1);
				$fieldlen = ord(substr($buff32,16,1));
				$fielddec = ord(substr($buff32,17,1));
				$arrHeader[$index]["NAME"] = $fieldname;
				$arrHeader[$index]["TYPE"] = $fieldtype;
				$arrHeader[$index]["LEN"] = $fieldlen;
				$arrHeader[$index]["DEC"] = $fielddec;
				$tdHeader .= "<td align=center >$fieldname<br>($fieldtype,$fieldlen,$fielddec)</td>";
				$index++;
				}//if (substr($buff32,0,1) == chr(13)) {
			}//if ($i > 1) {
			$i++;
			}else{//if (!feof($fdbf)) {
			$goon = false;
			}//if (!feof($fdbf)) {
		}//while ($goon) {
	fclose($fdbf);
	return($arrHeader);
	}//function get_dbf_header($dbfname){

$PLUS_YEAR = 0;
$SRC_DBF = $tablename;
$dbfHeader = get_dbf_header($SRC_DBF);
$dbh = dbase_open($SRC_DBF,0);
if($dbh){
	$numFiled = dbase_numfields ($dbh);
	$numRow = dbase_numrecords ($dbh);
	echo ("<table border=1 width=100%><tr bgcolor=\"#EFEFEF\"><td>ORDER</td>$tdHeader</tr>");	
	for($r = 1 ; $r <= $numRow ; $r++) {
		echo ("<tr><td align=\"center\" bgcolor=\"#EFEFEF\">$r</td>");
		$rec = dbase_get_record($dbh, $r);
		for ($f=0; $f < $numFiled; $f++) {
			echo "<td>&nbsp;" .$rec[$f] . "</td>";
			}//for ($f=0; $f < $numFiled; $f++) {
		echo "</tr>";
		}//for ($r = 1 ; $r <= $numRow ; $r++) {
		echo ("</table>");
		dbase_close($dbh);
	}//if($dbh){
}else{ //if(file_exists($tablename)){ 
?> 
<table width="100%"  border="0" cellspacing="1" cellpadding="1" class=normal>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td align="center"><h3>File not exists </h3></td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
 </table>
<? 
	}//if(file_exists($tablename)){ 
?>