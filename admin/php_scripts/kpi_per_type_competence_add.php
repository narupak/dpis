<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command == "ADD" && trim($PER_TYPE) && trim($CP_CODE) && trim($DEPARTMENT_ID)){
		$cmd = " SELECT * FROM   PER_TYPE_COMPETENCE
					  WHERE 	(PER_TYPE='$PER_TYPE' AND a.CP_CODE='$CP_CODE' AND a.DEPARTMENT_ID=$DEPARTMENT_ID)  ";
		$f_count=$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();	
		//echo "$cmd [".$f_count."]<br>";
		if ($f_count) { ?> 
			<script language="JavaScript">alert("ไม่สามารถเพิ่มได้! เพราะมีข้อมูลสถานะการประเมินนี้อยู่แล้ว");</script>
		<?
		} else {
			/*
			$cmd = " 	SELECT * FROM PER_COMPETENCE 
							WHERE DEPARTMENT_ID = $DEPARTMENT_ID AND CP_MODEL=1 ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$CP_CODE = $data[CP_CODE];
				$LC_ACTIVE = 1;
			*/
				$cmd = " insert into PER_TYPE_COMPETENCE (PER_TYPE, DEPARTMENT_ID, CP_CODE, UPDATE_USER, UPDATE_DATE) 
							  values ('$PER_TYPE', $DEPARTMENT_ID, '$CP_CODE', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<br>-> $cmd<hr>";

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$PER_TYPE : $DEPARTMENT_ID : $CP_CODE]");
			/*} // end while loop
			*/
			
			//echo "<script language='JavaScript'>";
			//echo "parent.refresh_opener('1<::><::><::>');";
			//echo "<script>";
		} // end if ($f_count)
	}// end if($command == "ADD" && trim($PL_CODE) && trim($ORG_ID))
	
	if($command == "UPDATE" && trim($CP_CODE)){	// CP_CODE = ตัวใหม่
		$cmd = " 	SELECT PER_TYPE, a.DEPARTMENT_ID, a.CP_CODE, CP_NAME
							FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE c
							WHERE (PER_TYPE='$PER_TYPE' AND a.CP_CODE='$CP_CODE' AND a.DEPARTMENT_ID=$DEPARTMENT_ID) 
							AND (a.CP_CODE=c.CP_CODE)";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<br>:::: $count_duplicate ~> $cmd";
		if($count_duplicate <= 0){
			$cmd = "update PER_TYPE_COMPETENCE set 
									PER_TYPE='$PER_TYPE', 
									DEPARTMENT_ID='$DEPARTMENT_ID', 
									CP_CODE='$CP_CODE', 
									UPDATE_USER=$SESS_USERID, 
									UPDATE_DATE='$UPDATE_DATE' 
							WHERE (PER_TYPE='$PER_TYPE' AND DEPARTMENT_ID='$upd_department_id' AND CP_CODE='$upd_cp_code') ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [จาก->$PER_TYPE : $DEPARTMENT_ID : $CP_CODE เป็น->$PER_TYPE : $upd_department_id : $upd_cp_code]");
		}else{
			$data = $db_dpis->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [$CP_CODE : $data[CP_NAME]]";
?>
				<script language='JavaScript'>
					alert('<?=$err_text; ?>'); 
				</script>
<?
			$UPD = 1;
		} // endif
	}//if($command == "UPDATE" && trim($PL_CODE)){
	
	if($command == "DELETE" && trim($CP_CODE)){
		$cmd = " delete FROM PER_TYPE_COMPETENCE WHERE (PER_TYPE='$PER_TYPE' AND CP_CODE='$CP_CODE' AND DEPARTMENT_ID=$DEPARTMENT_ID) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$PER_TYPE : $DEPARTMENT_ID : $CP_CODE]");
	}//if($command == "DELETE" && trim($PL_CODE)){
	
	
	if(trim($PER_TYPE) && trim($DEPARTMENT_ID)){
		$search_cp_code = "";
		if(trim($CP_CODE))	$search_cp_code = " AND a.CP_CODE = '$CP_CODE' ";
		$cmd = " SELECT 	a.CP_CODE, CP_NAME, ORG_NAME, a.DEPARTMENT_ID
						  FROM 		PER_TYPE_COMPETENCE a, PER_COMPETENCE c, PER_ORG d 
						  WHERE 	(PER_TYPE='$PER_TYPE' AND a.DEPARTMENT_ID=$DEPARTMENT_ID)  $search_cp_code AND 
						 (a.DEPARTMENT_ID=d.ORG_ID)  AND (a.CP_CODE=c.CP_CODE)";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CP_NAME = $data[CP_NAME];
		
		$upd_department_id =  $DEPARTMENT_ID;
		$upd_cp_code = $CP_CODE;		
	}
	
	if( (!$UPD && !$err_text) ){
		$upd_cp_code  = "";
		$CP_CODE = "";
		$CP_NAME = "";
	} // end if
?>