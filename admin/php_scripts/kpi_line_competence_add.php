<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($PL_CODE) && trim($ORG_ID)){
		$cmd = " SELECT * FROM   PER_LINE_COMPETENCE
					  WHERE 	PL_CODE = '$PL_CODE' AND ORG_ID = '$ORG_ID'  ";
		$f_count=$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();	
		//echo "$cmd [".$f_count."]<br>";
		if ($f_count) { ?>
			<script language="JavaScript">alert("ไม่สามารถเพิ่มได้! เพราะมีข้อมูลสมรรถนะของสายงานนี้อยู่แล้ว");</script>
		<?
		} else {
			$cmd = " SELECT ORG_ID_REF FROM	PER_ORG WHERE ORG_ID = $ORG_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID_REF];

			$cmd = " 	SELECT * FROM PER_COMPETENCE 
							WHERE DEPARTMENT_ID = $ORG_ID_REF AND CP_MODEL=1 ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$CP_CODE = $data[CP_CODE];
				$LC_ACTIVE = 1;

				$cmd = " insert into PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
							  values ('$PL_CODE', $ORG_ID, '$CP_CODE', $LC_ACTIVE, $SESS_USERID, '$UPDATE_DATE', $ORG_ID_REF) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<br>-> $cmd<hr>";

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ORG_ID : $PL_CODE : $CP_CODE]");
			} // end while loop
			
			$cmd = " SELECT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION  
							WHERE DEPARTMENT_ID = $ORG_ID_REF AND PL_CODE = '$PL_CODE' AND ORG_ID = $ORG_ID AND POS_STATUS = 1 AND LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') ";
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
			//echo "<script language='JavaScript'>";
			//echo "parent.refresh_opener('1<::><::><::>');";
			//echo "<script>";
		} // end if ($f_count)
	}// end if($command == "ADD" && trim($PL_CODE) && trim($ORG_ID))
	
	if($PL_CODE&&$ORG_ID){
		$search_cp_code = "";
		if($CP_CODE)	$search_cp_code = " AND a.CP_CODE = '$CP_CODE' ";
		if ($DPISDB=="oci8") {
			$cmd = " SELECT 	a.PL_CODE, a.ORG_ID, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME, ORG_NAME
						   FROM	PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c, PER_ORG d 
						  WHERE 	a.PL_CODE = '$PL_CODE' AND a.ORG_ID = '$ORG_ID'  $search_cp_code AND 
										a.PL_CODE=trim(b.PL_CODE) AND a.ORG_ID=d.ORG_ID AND a.CP_CODE=c.CP_CODE AND a.DEPARTMENT_ID = c.DEPARTMENT_ID ";
		} else {
			$cmd = " SELECT 	a.PL_CODE, a.ORG_ID, a.CP_CODE, LC_ACTIVE, PL_NAME, CP_NAME, ORG_NAME
						   FROM	PER_LINE_COMPETENCE a, PER_LINE b, PER_COMPETENCE c, PER_ORG d 
						  WHERE 	a.PL_CODE = '$PL_CODE' AND a.ORG_ID = '$ORG_ID'  $search_cp_code AND 
										a.PL_CODE=trim(b.PL_CODE) AND a.ORG_ID=d.ORG_ID AND a.CP_CODE=c.CP_CODE AND a.DEPARTMENT_ID = c.DEPARTMENT_ID ";
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];		
		$PL_NAME = $data[PL_NAME];
		$ORG_ID = $data[ORG_ID];				
		$ORG_NAME = $data[ORG_NAME];
		$CP_CODE = $data[CP_CODE];
		$LC_ACTIVE = $data[LC_ACTIVE];
		$PL_NAME = $data[PL_NAME];
		$ORG_NAME = $data[ORG_NAME];
		$CP_NAME = $data[CP_NAME];
		$upd_pl_code = $data[PL_CODE];
		$upd_cp_code = $data[CP_CODE];
	}
?>