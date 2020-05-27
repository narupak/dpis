<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$debug=0;/*0 close, 1 open*/
	if($command == "DELETE" && trim($del_per_type) && trim($del_department_id)){		//CP_CODE='$del_cp_code' 
		$cmd = " delete from PER_TYPE_COMPETENCE where (PER_TYPE='$del_per_type' and DEPARTMENT_ID = $del_department_id) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo $cmd;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$del_per_type, $del_department_id]");
	}
	
	if($command == "GENDATA"){
		$where = "";
		if ($search_department_id) {
			$where .= " and (DEPARTMENT_ID = '$search_department_id')";
		}
		$cmd = " delete from PER_TYPE_COMPETENCE where PER_TYPE = $search_PER_TYPE $where ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if ($search_PER_TYPE==1)
			$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID, a.DEPARTMENT_ID FROM PER_POSITION a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POS_STATUS = 1 ";
		elseif ($search_PER_TYPE==3)
			$cmd = " SELECT DISTINCT EP_CODE as PL_CODE, a.ORG_ID, a.DEPARTMENT_ID FROM PER_POS_EMPSER a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POEM_STATUS = 1 ";
                
                if($debug==1){
                    echo $cmd;
                }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PL_CODE = trim($data[PL_CODE]);
			$TMP_ORG_ID = $data[ORG_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

/*****
			$cmd = " SELECT PL_CODE FROM PER_LINE WHERE PL_CODE = '$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$PL_CODE = trim($data1[PL_CODE]);
			if ($PL_CODE) {
				$arr_temp = explode("||", $PL_CODE);
				for ($count=0; $count<count($arr_temp); $count++) {           //---------------------->>>>>>>>>>>>>>>>>>>>>>>>>
					$cmd = " INSERT INTO PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
									VALUES('$search_PER_TYPE', $TMP_DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				}
			}
*****/			

			if ($COMPETENCE_FLAG==1)
				$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE CP_MODEL in (1,3) AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
			else
				$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE CP_MODEL = 1 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
			
                        if($debug==1){
                            echo $cmd.'<br>';
                        }
                        $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$CP_CODE = trim($data1[CP_CODE]);
				$cmd = " INSERT INTO PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
								VALUES('$search_PER_TYPE', $TMP_DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
			} // end while						
		} // end while						

		if ($search_PER_TYPE==1) {
			$cmd = " SELECT DISTINCT PL_CODE, a.ORG_ID, a.DEPARTMENT_ID FROM PER_POSITION a, PER_ORG b 
							WHERE a.DEPARTMENT_ID = b.ORG_ID and a.DEPARTMENT_ID > 1 and b.OL_CODE = '02' and POS_STATUS = 1  and LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') ";
			$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$TMP_ORG_ID = $data[ORG_ID];
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID]; 

				$cmd = " SELECT CP_CODE FROM PER_COMPETENCE WHERE CP_MODEL = 2 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				while($data1 = $db_dpis1->get_array()){
					$CP_CODE = trim($data1[CP_CODE]);
					$cmd = " INSERT INTO PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
									VALUES('$search_PER_TYPE', $TMP_DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') "; 
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
				} // end while						
			} // end while
		} // end if 
	} // end if 
?>