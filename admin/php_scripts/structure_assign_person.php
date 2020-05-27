<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($command=="ADD" && $PER_ID){
		$cmd = " select PER_NAME, PER_SURNAME, PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];

		$ASSIGNED_ORG_ID = substr($ASSIGNED_ORG_ID, 1, -1);
		$ARR_ASSIGNED = explode(",", $ASSIGNED_ORG_ID);
		foreach($ARR_ASSIGNED as $ORG_ID){
			$cmd = " insert into PER_ORG_JOB 
							 	(PER_ID, ORG_ID, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
							 values
							 	($PER_ID, $ORG_ID, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')
						  ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end foreach
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการมอบหมายงาน [ $PER_ID : $PER_NAME $PER_SURNAME : $PER_CARDNO ]");
	} // end if

	if($command=="UPDATE" && $PER_ID){
		$cmd = " select PER_NAME, PER_SURNAME, PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];

		$cmd = " delete from PER_ORG_JOB where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$ASSIGNED_ORG_ID = substr($ASSIGNED_ORG_ID, 1, -1);
		$ARR_ASSIGNED = explode(",", $ASSIGNED_ORG_ID);
		foreach($ARR_ASSIGNED as $ORG_ID){
			$cmd = " insert into PER_ORG_JOB 
							 	(PER_ID, ORG_ID, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
							 values
							 	($PER_ID, $ORG_ID, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')
						  ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end foreach
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการมอบหมายงาน [ $PER_ID : $PER_NAME $PER_SURNAME : $PER_CARDNO ]");
	} // end if

	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME
							 from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME
 							 from		PER_PERSONAL, PER_PRENAME
							 where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME
							 from		PER_PERSONAL as PER
											left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
							 where	PER.PER_ID=$PER_ID
						  ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];

		if($DPISDB=="odbc"){
			$cmd = " select 	ORG_ID
							 from		PER_ORG_JOB
							 where	PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	ORG_ID
							 from		PER_ORG_JOB
							 where	PER_ID=$PER_ID
						  ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	ORG_ID
							 from		PER_ORG_JOB
							 where	PER_ID=$PER_ID
						  ";
		} // end if
		
		$ASSIGNED_ORG_ID = "";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			if($ASSIGNED_ORG_ID) $ASSIGNED_ORG_ID .= ",";
			$ASSIGNED_ORG_ID .= $data[ORG_ID];

			$ARR_ASSIGNED_ORG[] = $data[ORG_ID];
		} // end while
		if($ASSIGNED_ORG_ID) $ASSIGNED_ORG_ID = ",". $ASSIGNED_ORG_ID .",";
//		echo "ASSIGNED :: $ASSIGNED_ORG_ID";
		
	} // end if
	
	if($DPISDB=="odbc"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
						 order by PER_ID
					  ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
						 order by PER_ID
					  ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct PER_ID
						 from		PER_ORG_JOB
						 order by PER_ID
					  ";
	} // end if

	$EXCEPT_PER_ID = "";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if($EXCEPT_PER_ID) $EXCEPT_PER_ID .= ",";
		$EXCEPT_PER_ID .= $data[PER_ID];
	} // end while

	$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=ORG_ID ";
	$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$START_ORG_ID = $data[ORG_ID];

	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
	} // end if

	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = 1;
		$ORG_ID_REF = 1;
	} // end if

	if($ORG_ID && !$ORG_ID_REF)	{
		if($DPISDB=="odbc"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG_ASS where ORG_ID=$ORG_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID_REF = $data[ORG_ID_REF];
	} // end if

	function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $START_ORG_ID, $ARR_ASSIGNED_ORG;
		global $PAGE_AUTH, $UPD, $VIEW;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID , ORG_NAME, ORG_ID_REF from PER_ORG_ASS where ORG_ID_REF = $org_parent and ORG_ID <> $START_ORG_ID and ORG_ACTIVE=1 order by ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=". $data[ORG_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				echo "<tr>";
				echo "<td width=\"25\" align=\"center\"><input type=\"checkbox\" name=\"NODE_". $org_parent ."_". $data[ORG_ID] ."\" value=\"". $data[ORG_ID] ."\" onClick=\"edit_assigned_org(this.value, this.checked);\" ". (in_array($data[ORG_ID], $ARR_ASSIGNED_ORG)?"checked":"") ." ". ((($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW)?"":"disabled") ."></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span style=\"cursor:hand\">" . $data[ORG_NAME] . "</span></td>";
				echo "</tr>";
				
				if($count_sub_tree){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					echo "<div id=\"DIV_". $data[ORG_ID] ."\" style=\"display:block\">";
					list_tree_org("", $data[ORG_ID], $sel_org_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function
?>