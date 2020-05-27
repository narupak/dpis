<?php

include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");
//$TPMREQUEST_DATE = str_replace("-","",$REQUEST_DATE);
$temp_date = explode("/", $REQUEST_DATE);
$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0]." 00:00:00";

echo "<select class='selectbox' style='width:145px;' onChange=dochange('P_EXTRATIME_SHOW');dup_time(); name='WC_CODE' id='WC_CODE' <? if( ($SESS_PER_ID==$PER_ID && $ODL_CHK_REQ_STATUS==0) || ($SESS_USERGROUP==1) || ($NAME_GROUP_HRD=='HRD') || ($PER_AUDIT_FLAG) ){ echo '';}else{ echo 'disabled';} ?>>";

if(($SESS_USERGROUP==1) || ($NAME_GROUP_HRD=='HRD') || ($PER_AUDIT_FLAG==1)){
	$cmd = " select * from(
		select distinct  a.WC_CODE, a.WC_NAME ,a.WC_SEQ_NO
				from PER_WORK_CYCLE  a
				left join PER_WORK_CYCLEHIS b on(b.WC_CODE=a.WC_CODE) 
				where a.WC_ACTIVE = 1
				and b.PER_ID=$PER_ID
				and ('$temp_start' between b.START_DATE and case when b.END_DATE is not null then b.END_DATE else '$temp_start'  end)
		) q1
			  order by q1.WC_SEQ_NO asc ,q1.WC_CODE asc ";
			  
			  
	$db_dpis2->send_cmd($cmd);
	while($data2 = $db_dpis2->get_array()){					
		$WC_CODE = $data2[WC_CODE];
	}
	
	$con = "";
}else{
	
	$WC_CODE = "";
	$con = " and b.PER_ID=$PER_ID
			    and ('$temp_start' between b.START_DATE and case when b.END_DATE is not null then b.END_DATE else '$temp_start'  end)";
}

$cmd = " select * from(
	select distinct  a.WC_CODE, a.WC_NAME ,a.WC_SEQ_NO
			from PER_WORK_CYCLE  a
			left join PER_WORK_CYCLEHIS b on(b.WC_CODE=a.WC_CODE) 
			where a.WC_ACTIVE = 1 ".$con."
			
	) q1
		  order by q1.WC_SEQ_NO asc ,q1.WC_CODE asc ";
		  
		  
$db_dpis->send_cmd($cmd);

$CHK = 0; 
while($data = $db_dpis->get_array()){					
	$TMP_WC_CODE = $data[WC_CODE];
	$TMP_WC_NAME = $data[WC_NAME];
        // 09/11/2108 ปรับแก้เรื่อง ให้โปรแกรม default ที่รอบ 8 โมง กรณีหาค่ารอบไม่ตรงกับรอบใดๆ // by somkiet
        $select = "selected";
	if($WC_CODE==$TMP_WC_CODE && $WC_CODE!=""){
		echo "<option value='".$TMP_WC_CODE."' selected>".$TMP_WC_NAME."</option>";
                $CHK = 1;                                                                       //เช็คกรณีหาค่า รอบตรงแล้ว ให้โปรแกรม selected ที่ค่ารอบนี้
	}else{
                echo "<option value='".$TMP_WC_CODE."'".($TMP_WC_CODE=='02' && $CHK != 1 ?$select:'').">".$TMP_WC_NAME."</option>"; //echo "<option value='".$TMP_WC_CODE."'>".$TMP_WC_NAME."</option>"; //เดิม
	}
}  

echo '</select>'; 
?>
