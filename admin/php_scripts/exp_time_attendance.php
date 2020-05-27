<?php
	include("../../php_scripts/connect_database.php");
	$db_expimg = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$SQLExp = "SELECT PER_ID,to_char(time_stamp, 'ddmmyyyyHH24miss') TIME_STAMP,PICTUREDATA  FROM PER_TIME_ATTENDANCE";
	$db_expimg->send_cmd($SQLExp);
	while ($dataExp = $db_expimg->get_array()) {
		//$db_POS_NO = $datachk[POS_NO];
		if($dataExp['PICTUREDATA']){ 
			$file_name =$dataExp['PER_ID'].'_'.$dataExp['TIME_STAMP'].'.png';
			//$img= $dataExp['PICTUREDATA']->load();
			//print('<img  src="data:image/png;base64,'.base64_encode($img).'" />');
			file_put_contents('../../att_time_attendance/'.$file_name, $dataExp['PICTUREDATA']->load());
		}else { 
			$imgShow="<font color='red'><strong>ไม่พบข้อมูลรูปภาพ</strong></font>";
		}
		
	}
	//
?>