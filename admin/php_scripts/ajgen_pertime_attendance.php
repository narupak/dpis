<?php

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");
//$TPMREQUEST_DATE = str_replace("-","",$REQUEST_DATE);

		

$showtable = "
<table width='100%' border='0' cellpadding='0' cellspacing='0' class='table_body_3'>
	<tr>
	  <td align='center' height='30'>ข้อมูลจากเครื่องบันทึกเวลา</td>
	</tr>
 </table>
<table width='100%' border='0' align='center' cellpadding='1' cellspacing='1' class='label_normal'>
			<tr align='center' class='table_head'> 
				<td nowrap width='20%' height='40'><strong>ลำดับ</strong></td>
				<td nowrap width='40%' height='40'><strong>วัน-เวลาที่สแกน</strong></td>
				<td nowrap width='40%' height='40'><strong>เครื่องบันทึกเวลา</strong></td>
			</tr>
			";
$cmd = " select att.TA_CODE,tat.TA_NAME,
						TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP1,
						TO_CHAR(att.TIME_STAMP,'HH24:MI:SS')  AS ATT_STARTTIME
					from PER_TIME_ATTENDANCE att 
                    left join PER_TIME_ATT tat on(tat.TA_CODE=att.TA_CODE)
					WHERE 	TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')='$TIME_STAMP'  and  att.PER_ID=$PER_ID	  
					order by att.TIME_STAMP asc ";
$db_dpis->send_cmd($cmd);
$data_num = 0;
$showtr = "";
while($data = $db_dpis->get_array()){	
	$data_num++;	
				
	$TIME_STAMP_STR = "";
	if ($data[TIME_STAMP1]) {
		$TIME_STAMP_STR  = substr($data[TIME_STAMP1],8,2)."/".substr($data[TIME_STAMP1],5,2)."/".(substr($data[TIME_STAMP1],0,4)+543);
	}
	
	$DATA_att_starttime = "";
	if ($data[ATT_STARTTIME]) { 
		$DATA_att_starttime = "(".$data[ATT_STARTTIME].")";
	}
	
	$DATA_TA_NAME = $data[TA_NAME];
	
	$showtr = $showtr. "<tr align='center' class='table_body'>
			 	<td height='25' align='center'>".$data_num."</td>
			 	<td align='center'>".$TIME_STAMP_STR." ".$DATA_att_starttime."</td>
			 	<td align='left'>&nbsp;".$DATA_TA_NAME."</td>
			</tr>";
} 
if($data_num>0){
	echo $showtable.$showtr."</table>";
}else{
	echo $showtable."<tr align='center' class='table_body'><td height='25' align='center' colspan='3'><span class='label_alert'>ไม่พบข้อมูล</span></td></tr></table>"; 
}

?>