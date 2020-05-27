<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($PL_CODE) && trim($ORG_ID)){
		$cmd = " SELECT * FROM   PER_MAIN_JOB
					  WHERE 	PL_CODE = '$PL_CODE' AND ORG_ID = '$ORG_ID'  ";
		$f_count=$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();	
		//echo "$cmd [".$f_count."]<br>";
		if ($f_count) { ?>
			<script language="JavaScript">alert("ไม่สามารถเพิ่มได้! เพราะมีข้อมูลหน้าที่ความรับผิดชอบหลักนี้อยู่แล้ว");</script>
		<?
		} else {
			$cmd = " SELECT ORG_ID_REF FROM	PER_ORG WHERE ORG_ID = $ORG_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID_REF];

			$cmd = " 	SELECT * FROM PER_MAIN_JOB_TYPE 
							WHERE DEPARTMENT_ID = $ORG_ID_REF AND CP_MODEL=1 ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$MJT_CODE = $data[MJT_CODE];
				$LC_ACTIVE = 1;

				$cmd = " insert into PER_MAIN_JOB (PL_CODE, ORG_ID, MJT_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
							  values ('$PL_CODE', $ORG_ID, '$MJT_CODE', $LC_ACTIVE, $SESS_USERID, '$UPDATE_DATE', $ORG_ID_REF) ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<br>-> $cmd<hr>";

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$ORG_ID : $PL_CODE : $MJT_CODE]");
			} // end while loop
			
			//echo "<script language='JavaScript'>";
			//echo "parent.refresh_opener('1<::><::><::>');";
			//echo "<script>";
		} // end if ($f_count)
	}// end if($command == "ADD" && trim($PL_CODE) && trim($ORG_ID))
	
	if($PL_CODE&&$ORG_ID){
		$search_mjt_code = "";
		if($MJT_CODE)	$search_mjt_code = " AND a.MJT_CODE = '$MJT_CODE' ";
		if ($DPISDB=="oci8") {
			$cmd = " SELECT 	a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, PL_NAME, MJT_NAME, ORG_NAME
						   FROM	PER_MAIN_JOB a, PER_LINE b, PER_MAIN_JOB_TYPE c, PER_ORG d 
						  WHERE 	a.PL_CODE = '$PL_CODE' AND a.ORG_ID = '$ORG_ID'  $search_mjt_code AND 
										a.PL_CODE=trim(b.PL_CODE) AND a.ORG_ID=d.ORG_ID AND a.MJT_CODE=c.MJT_CODE AND a.DEPARTMENT_ID = c.DEPARTMENT_ID ";
		} else {
			$cmd = " SELECT 	a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, PL_NAME, MJT_NAME, ORG_NAME
						   FROM	PER_MAIN_JOB a, PER_LINE b, PER_MAIN_JOB_TYPE c, PER_ORG d 
						  WHERE 	a.PL_CODE = '$PL_CODE' AND a.ORG_ID = '$ORG_ID'  $search_mjt_code AND 
										a.PL_CODE=trim(b.PL_CODE) AND a.ORG_ID=d.ORG_ID AND a.MJT_CODE=c.MJT_CODE AND a.DEPARTMENT_ID = c.DEPARTMENT_ID ";
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];		
		$PL_NAME = $data[PL_NAME];
		$ORG_ID = $data[ORG_ID];				
		$ORG_NAME = $data[ORG_NAME];
		$MJT_CODE = $data[MJT_CODE];
		$LC_ACTIVE = $data[LC_ACTIVE];
		$PL_NAME = $data[PL_NAME];
		$ORG_NAME = $data[ORG_NAME];
		$MJT_NAME = $data[MJT_NAME];
		$upd_pl_code = $data[PL_CODE];
		$upd_mjt_code = $data[MJT_CODE];
	}
?>