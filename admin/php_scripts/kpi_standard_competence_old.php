<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	function list_per_level ($name, $val) {
		global $db_dpis, $DPISDB, $RPT_N;
		$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL
							where PER_TYPE = '1' AND LEVEL_ACTIVE = 1  
							order by PER_TYPE, LEVEL_SEQ_NO ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();	TO_NUMBER / CInt
		if ($RPT_N) 
			echo "<select name=\"$name\" class=\"selectbox\" >
				<option value=''>== Level ==</option>";
		else
			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_promote_c_comdtl_layer.html?LEVEL_NO=' + this.value\">
				<option value=''>== Level ==</option>";
		while ($data_list = $db_dpis->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$tmp_dat = trim($data_list[LEVEL_NO]);
			$tmp_name = trim($data_list[LEVEL_NAME]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='$tmp_dat' $sel>". $tmp_name ."</option>";
		}
		echo "</select>";
		return $val;
		//echo "<pre>";		
		//print_r($data_list);
		//echo "</pre>";	
	}	
	
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($command == "ADD" && trim($PL_CODE) && trim($LEVEL_NO) ){
		if($ORG_ID==""){
			$ORG_ID="0";
			}
		$cmd = " select PL_CODE from PER_STANDARD_COMPETENCE where PL_CODE='$PL_CODE' and DEPARTMENT_ID=$DEPARTMENT_ID and LEVEL_NO='$LEVEL_NO' and ORG_ID='$ORG_ID' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_duplicate <= 0){
			$cmd = " select CP_CODE from PER_LINE_COMPETENCE where LC_ACTIVE=1 AND PL_CODE='$PL_CODE' GROUP BY CP_CODE ORDER BY CP_CODE";
			$db_dpis->send_cmd($cmd);
			$i=1;
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				$cmd = " insert into PER_STANDARD_COMPETENCE 
								(PL_CODE, LEVEL_NO, ORG_ID, CP_CODE, TARGET_LEVEL, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) values 
								('$PL_CODE', '$LEVEL_NO', '$ORG_ID', '$CP_CODE', 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') 
							  ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				//echo($CP_CODE."<BR>");
				$i++;
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$PL_CODE, $LEVEL_NO, $ORG_ID, $CP_CODE]");
			} // end while
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "สำนัก/กอง ตำแหน่งในสายงาน และระดับตำแหน่งซ้ำ [".$PL_CODE . $LEVEL_NO. $ORG_ID."]";
		} // endif 
	}
//echo("command - ".$command."/PL_CODE - ".$PL_CODE."/LEVEL_NO - ".$LEVEL_NO."/ORG_ID - ".$ORG_ID);
	if($command == "DELETE" && trim($PL_CODE) && trim($LEVEL_NO) && trim($ORG_ID)){
		$cmd = " delete from PER_STANDARD_COMPETENCE where PL_CODE='$PL_CODE' and LEVEL_NO = '$LEVEL_NO' and ORG_ID = '$ORG_ID' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$PL_CODE $LEVEL_NO $ORG_ID]");
	}
	
	if($UPD && !$err_text){
		$cmd = "  select 	distinct a.PL_CODE, b.LEVEL_NAME, c.PL_NAME, a.DEPARTMENT_ID
				    from 		PER_STANDARD_COMPETENCE a, PER_LEVEL b, PER_LINE c
				    where 	a.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO' and a.PL_CODE='c.PL_CODE' and a.LEVEL_NO = b.LEVEL_NO
				";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		$POS_NO = $data[POS_NO];
		$PL_NAME = $data[PL_NAME];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME]; 
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$PL_CODE = "";
		$PL_NAME = "";
		$LEVEL_NO = "";
		$LEVEL_NAME = "";
		$ORG_ID = "";
		$ORG_NAME = "";
	} // end if
?>