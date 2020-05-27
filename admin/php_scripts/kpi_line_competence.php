<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command == "DELETE" && trim($del_pl_code)){
		$cmd = " delete from PER_LINE_COMPETENCE where PL_CODE='$del_pl_code' and ORG_ID = $del_org_id ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$del_pl_code, $del_org_id]");
	}
	
	if($command == "GENDATA"){
		$where = "";
		if ($search_PL_CODE) {
			$where = "where (PL_CODE = '$search_PL_CODE')";
		}
		$cmd = " delete from PER_LINE_COMPETENCE $where ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;

		$cmd = " SELECT DISTINCT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION WHERE DEPARTMENT_ID > 1 AND POS_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$TMP_ORG_ID = $data[ORG_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " SELECT PL_CP_CODE FROM PER_LINE WHERE PL_CODE = '$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$PL_CP_CODE = trim($data1[PL_CP_CODE]);
			if ($PL_CP_CODE) {
				$arr_temp = explode("|", $PL_CP_CODE);
				for ($count=0; $count<count($arr_temp); $count++) {
					$CP_CODE = $arr_temp[$count];
					$cmd = " SELECT CP_CODE FROM PER_TYPE_COMPETENCE 
									WHERE PER_TYPE = 1 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND CP_CODE = '$CP_CODE' ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$data1 = $db_dpis1->get_array();
					$CP_CODE = trim($data1[CP_CODE]);
					if ($CP_CODE) {
						$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
										VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
					}
				}
			}

			$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
							WHERE PER_TYPE = 1 AND a.DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  = 1 ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$CP_CODE = trim($data1[CP_CODE]);
				$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
								VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			} // end while						
		} // end while						

		$cmd = " SELECT DISTINCT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION  
						WHERE DEPARTMENT_ID > 0 AND POS_STATUS = 1 AND LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$TMP_ORG_ID = $data[ORG_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
							WHERE PER_TYPE = 1 AND a.DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  = 2 ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$CP_CODE = trim($data1[CP_CODE]);
				$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
								VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			} // end while						
		} // end while						
	} // end if 
?>