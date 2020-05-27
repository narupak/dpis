<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");	
	//echo $command . "+++++++++++++++===";

	if($command == "ADD"){
		$cmd = " select 	SF_NAME,SF_CODE
					  		 from 		PER_SALARY_FORMULA
							 where 	SF_NAME = '$SF_NAME'";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if(empty($ORG_ID)) $ORG_ID = 'NULL';
		if($count_duplicate <= 0){
			foreach($SF_PERCENT as $key => $value) {
				$SF_PERCENT_JOIN .= substr($key,1,-1) . "," . $value . ":";
			}
			$SF_PERCENT_JOIN = substr($SF_PERCENT_JOIN,0,-1);
			$cmd = " insert into PER_SALARY_FORMULA 
							(SF_CODE,SF_NAME,AL_CODE,SF_PERCENT,SF_ACTIVE,ORG_ID,DEPARTMENT_ID,UPDATE_USER, UPDATE_DATE) 
							values 
							('$SF_CODE', '$SF_NAME', '$AL_CODE_JOIN', '$SF_PERCENT_JOIN', '1',$ORG_ID,'$DEPARTMENT_ID', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$SF_CODE." : ".$SF_NAME." : ".$SF_PERCENT_JOIN."]");

		}else{
			$data = $db_dpis->get_array();
			$SF_NAME = $data[SF_NAME];
			$SF_CODE = $data[SF_CODE];
			$err_text = "ชื่อแบบทดสอบข้อมูล $SF_NAME ซ้ำ";
		} // endif count
	} //if ADD

	if($command == "UPDATE" && trim($SF_CODE) ){
		foreach($SF_PERCENT as $key => $value) {
			$SF_PERCENT_JOIN .= substr($key,1,-1) . "," . $value . ":";
		}
		$SF_PERCENT_JOIN = substr($SF_PERCENT_JOIN,0,-1);
		if(empty($ORG_ID)) $ORG_ID = 'NULL';
		$cmd = " update PER_SALARY_FORMULA set 
							SF_NAME='$SF_NAME',
							SF_PERCENT='$SF_PERCENT_JOIN', 								
							AL_CODE='$AL_CODE_JOIN', 
							ORG_ID=$ORG_ID,
							DEPARTMENT_ID='$DEPARTMENT_ID',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where SF_CODE=$SF_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$SF_CODE." : ".trim($SF_NAME)." : ".$SF_PERCENT_JOIN."]");
	}
	
	if($command == "DELETE" && trim($SF_CODE)){
		$cmd = " delete from PER_SALARY_FORMULA where SF_CODE='$SF_CODE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$SF_CODE." : ".trim($SF_NAME)." : ".$SF_PERCENT_JOIN."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	SF_NAME, AL_CODE, SF_PERCENT
				  		 from 		PER_SALARY_FORMULA
						 where 	SF_CODE='$SF_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$SF_NAME = $data[SF_NAME];
		$AL_CODE = $data[AL_CODE];
		$TMP_SF_PERCENT = explode(":",$data[SF_PERCENT]);
		foreach($TMP_SF_PERCENT as $value) {
			list($key,$tmp) = explode(",",$value);
			$SF_PERCENT[$key] = $tmp;
		}
	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$SF_NAME = "";
		$SF_PERCENT_JOIN = "";
		$SF_CODE = "";
		$SF_PERCENT = "";
	} // end if
	
?>