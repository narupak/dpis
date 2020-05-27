<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	function list_per_level ($name, $val) {
		global $db_dpis, $DPISDB, $RPT_N, $LEVEL_TITLE, $BKK_FLAG, $ISCS_FLAG, $LIST_LEVEL_NO;
		if ($BKK_FLAG==1)
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL
								where PER_TYPE = 1 AND LEVEL_ACTIVE = 1  
								order by PER_TYPE, LEVEL_SEQ_NO ";
		elseif ($ISCS_FLAG==1)
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL
								where PER_TYPE = 1 AND LEVEL_ACTIVE = 1 AND LEVEL_NO in $LIST_LEVEL_NO 
								order by PER_TYPE, LEVEL_SEQ_NO ";
		else
			$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL
								where PER_TYPE in (1,3) AND LEVEL_ACTIVE = 1  
								order by PER_TYPE, LEVEL_SEQ_NO ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();	
		if ($RPT_N) 
			echo "<select name=\"$name\" class=\"selectbox\" >
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		else
			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_promote_c_comdtl_layer.html?LEVEL_NO=' + this.value\">
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
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
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($command == "ADD" && trim($LEVEL_NO) ){
		$cmd = " select LEVEL_NO from PER_STANDARD_COMPETENCE where LEVEL_NO='$LEVEL_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_duplicate <= 0){
			$cmd = " select CP_CODE from PER_COMPETENCE where DEPARTMENT_ID = $DEPARTMENT_ID and CP_ACTIVE=1 ORDER BY CP_CODE";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				$cmd = " insert into PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, TARGET_LEVEL, UPDATE_USER, 
								  UPDATE_DATE, DEPARTMENT_ID) 
								  values ('$LEVEL_NO', '$CP_CODE', 0, $SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID) ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//echo $cmd;
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$LEVEL_NO, $CP_CODE, $DEPARTMENT_ID]");
			} // end while
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "ระดับตำแหน่งซ้ำ [$LEVEL_NO]";
		} // endif 
	}
 
	if($command == "DELETE" && trim($LEVEL_NO)){
		$cmd = " delete from PER_STANDARD_COMPETENCE where LEVEL_NO = '$LEVEL_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$LEVEL_NO, $DEPARTMENT_ID]");
	}

	if($command == "GENDATA"){
		$cmd = " DELETE FROM PER_STANDARD_COMPETENCE WHERE DEPARTMENT_ID=$DEPARTMENT_ID OR DEPARTMENT_ID IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		if ($DEPARTMENT_ID) $arr_dept[] = $DEPARTMENT_ID;
		elseif($CTRL_TYPE==1 || $CTRL_TYPE==2 || $CTRL_TYPE==3 || $BKK_FLAG==1){
			$cmd = " SELECT DISTINCT a.DEPARTMENT_ID FROM PER_POSITION a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) $arr_dept[] = $data[DEPARTMENT_ID];
		}else $arr_dept[] = $DEPARTMENT_ID;

		for ( $j=0; $j<count($arr_dept); $j++ ) { 
			if ($BKK_FLAG==1) {
				$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
				$target = array(	1, 1, 2, 3, 1, 2, 3, 3, 3, 3, 3, 3, 3 );
			} else {
				$level = array(	"O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2", "E1", "E2", "E3", "E4", "E5", "E7", "S1", "S2", "S3" );
				$target = array(	1, 1, 2, 3, 1, 2, 3, 4, 5, 3, 4, 4, 5, 1, 1, 2, 3, 4, 1, 3, 4, 5 );
			}
			for ( $i=0; $i<count($level); $i++ ) { 
				$PER_TYPE = 1;
				if ($level[$i]=="E1" || $level[$i]=="E2" || $level[$i]=="E3" || $level[$i]=="E4" || $level[$i]=="E5" || $level[$i]=="E7" || $level[$i]=="S1" || $level[$i]=="S2" || $level[$i]=="S3")
					$PER_TYPE = 3;
				$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
								WHERE PER_TYPE = $PER_TYPE AND a.DEPARTMENT_ID = $arr_dept[$j] AND a.DEPARTMENT_ID = b.DEPARTMENT_ID AND 
								a.CP_CODE = b.CP_CODE AND CP_MODEL in (1,3) ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
 //               echo "<pre>". $cmd;
				while($data2 = $db_dpis2->get_array()){
					$CP_CODE = trim($data2[CP_CODE]);
					if ($level[$i]=="O1" && substr($CP_CODE,0,1)=="3") $TARGET_LEVEL = 0; else $TARGET_LEVEL = $target[$i]; 
					$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE)
									VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $TARGET_LEVEL,  $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
                       // echo "<pre>".$cmd."| |".substr($CP_CODE,0,1)."<br>";
					//$db_dpis->show_error();
				} // end while
			} // end for
			
			if ($BKK_FLAG!=1) {
				$level = array(	"D1", "D2", "M1", "M2" );
				$target = array(	1, 2, 3, 4 );
				for ( $i=0; $i<count($level); $i++ ) { 
					$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
									WHERE PER_TYPE = 1 AND a.DEPARTMENT_ID = $arr_dept[$j] AND a.DEPARTMENT_ID = b.DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL = 2 ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					while($data2 = $db_dpis2->get_array()){
						$CP_CODE = trim($data2[CP_CODE]);
						$cmd = " INSERT INTO PER_STANDARD_COMPETENCE (LEVEL_NO, CP_CODE, DEPARTMENT_ID, TARGET_LEVEL, UPDATE_USER, 
										UPDATE_DATE)
										VALUES ('$level[$i]', '$CP_CODE', $arr_dept[$j], $target[$i],  $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} // end while
				} // end for
			}	// end if	
		} // end for
	} // end if 
/*	
	if($UPD && !$err_text){
		$cmd = "  select 	distinct b.LEVEL_NAME
				    from 		PER_STANDARD_COMPETENCE a, PER_LEVEL b
				    where 	a.LEVEL_NO='$LEVEL_NO' and a.LEVEL_NO = b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
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
*/	
	if( (!$UPD && !$DEL && !$err_text) ){
		$LEVEL_NO = "";
		$LEVEL_NAME = "";
	} // end if
?>