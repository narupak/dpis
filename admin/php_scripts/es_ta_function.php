<?
function GetListAuditArray($aa, $level)
{
	$retstr="";
    for ($i=0; $i < count($aa); $i++) {
    	if ($aa[$i][$level] != 0) {
        	if ($retstr != "")
	        	$retstr .= ",";
        	$retstr .= $aa[$i][$level];
        }
    }
	return ($retstr);
}

function GetListAuditArrayDepOnly($aa)
{
	$retstr="";
    for ($i=0; $i < count($aa); $i++) {
    	if ($aa[$i][1] == 0) {
        	if ($retstr != "")
	        	$retstr .= ",";
        	$retstr .= $aa[$i][0];
        }
    }
	return ($retstr);
}

function GetListNumberOfORGID($aa, $ORG_ID)
{
    for ($i=0; $i < count($aa); $i++) {
    	if ($aa[$i][0] == $ORG_ID) 
			return ($i);
    }
	return (-1);
}


////return TRUE ถ้ามีหน่วยงานระดับต่ำกว่าสำนัก 1 ระดับ
function IsLower1($aa, $nList) 
{
    if ($aa[$nList][1] != 0) {
        return (TRUE);
    }
  	return (FALSE);
}
?>