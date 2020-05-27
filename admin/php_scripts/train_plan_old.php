<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	// add project
	//echo $command . "+++++++++";
	if($command=="ADDPROJ" && $TP_BUDGET_YEAR && trim($NEW_PROJ_NAME)){
		
		if(!$PLAN_ID) $PROJ_ID_REF = "NULL";
				
		$cmd = " select max(PROJ_ID) as max_id from PER_TRAIN_PROJECT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PLAN_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRAIN_PROJECT (PROJ_ID, PROJ_NAME, TPJ_BUDGET_YEAR, DEPARTMENT_ID,PLAN_ID,
									TPJ_MANAGE_ORG,TPJ_RESPONSE_ORG,TPJ_APP_PER_ID,PG_ID,TPJ_APP_DATE,TPJ_APP_DOC_NO,
									TPJ_INOUT_TRAIN, UPDATE_USER, UPDATE_DATE)
						values	($PLAN_ID, '$NEW_PROJ_NAME', '$TP_BUDGET_YEAR', $DEPARTMENT_ID,$PLAN_ID,
								 '$NEW_PROJ_MANAGE','$NEW_PROJ_REPONSE','$NEW_PROJ_PER_ID','$PG_ID','$NEW_PROJ_DATE','$NEW_PROJ_DOCNO',
								 '$NEW_PROJ_INOUT',$SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มโครงการ [$PLAN_ID_REF : $PLAN_ID : $PLAN_NAME]");
		
		$PLAN_ID_REF += 0;
	} // end if

	// update project
	if($command=="UPDATEPROJECT" && $PLAN_ID && $TP_BUDGET_YEAR && trim($PROJ_NAME)){
		$cmd = " update PER_TRAIN_PROJECT set
							PROJ_NAME = '$PROJ_NAME', 
							PROJ_ID_REF = '$PROJ_ID_REF', 
							TPJ_BUDGET_YEAR = '$TP_BUDGET_YEAR', 
							DEPARTMENT_ID = '$DEPARTMENT_ID',
							TPJ_MANAGE_ORG = '$TPJ_MANAGE_ORG',
							TPJ_RESPONSE_ORG = '$TPJ_RESPONSE_ORG',
							TPJ_APP_PER_ID = '$TPJ_APP_PER_ID',
							PG_ID = '$PG_ID',
							TPJ_APP_DATE = '$TPJ_APP_DATE',
							TPJ_APP_DOC_NO = '$TPJ_APP_DOC_NO',
							TPJ_INOUT_TRAIN = '$TPJ_INOUT_TRAIN', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where PROJ_ID=$PLAN_ID ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงการ [$PLAN_ID_REF : $PLAN_ID : $PLAN_NAME]");
	} // end if

	// add plan
	if($command=="ADD" && $TP_BUDGET_YEAR && trim($NEW_PLAN_NAME)){
		$PLAN_ID_REF = $PLAN_ID;
		if(!$PLAN_ID) $PLAN_ID_REF = "NULL";
				
		$cmd = " select max(PLAN_ID) as max_id from PER_TRAIN_PLAN ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PLAN_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRAIN_PLAN (PLAN_ID, PLAN_NAME, PLAN_ID_REF, TP_BUDGET_YEAR, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
						values	($PLAN_ID, '$NEW_PLAN_NAME', $PLAN_ID_REF, '$TP_BUDGET_YEAR', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มแผนฝึกอบรมย่อย [$PLAN_ID_REF : $PLAN_ID : $PLAN_NAME]");
		
		$PLAN_ID_REF += 0;
	} // end if
	
	// update plan
	if($command=="UPDATE" && $PLAN_ID && $TP_BUDGET_YEAR && trim($PLAN_NAME)){
		$cmd = " update PER_TRAIN_PLAN set
							PLAN_NAME='$PLAN_NAME',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where PLAN_ID=$PLAN_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงแผนฝึกอบรม [$PLAN_ID_REF : $PLAN_ID : $PLAN_NAME]");
	} // end if

	// delete plan
	if($command=="DELETE" && $PLAN_ID){
		delete_plan($PLAN_ID, $PLAN_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบแผนฝึกอบรม [$PLAN_ID_REF : $PLAN_ID : $PLAN_NAME]");

		$PLAN_ID = $PLAN_ID_REF + 0;
		unset($PLAN_ID_REF);
	} // end if

	$cmd = " select distinct TP_BUDGET_YEAR from PER_TRAIN_PLAN where DEPARTMENT_ID=$DEPARTMENT_ID order by TP_BUDGET_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[TP_BUDGET_YEAR];
		$arr_plan_year[] = $data[TP_BUDGET_YEAR];
	} // end while
	
	if($command=="ADDYEAR" && $NEW_TP_BUDGET_YEAR){
		$TP_BUDGET_YEAR = $NEW_TP_BUDGET_YEAR;
		if(!in_array($NEW_TP_BUDGET_YEAR, $arr_plan_year)) $arr_plan_year[] = $NEW_TP_BUDGET_YEAR;
		sort($arr_plan_year);
		$HAVE_YEAR += 1;				
	} // end if

//	echo "<pre>"; print_r($arr_plan_year); echo "</pre>";

	if(!$TP_BUDGET_YEAR || !in_array($TP_BUDGET_YEAR, $arr_plan_year)) $TP_BUDGET_YEAR = $START_YEAR;

	if($PLAN_ID && $TREE_DEPTH == '1'){
		if($DPISDB=="odbc"){
			$cmd = " select top 1 PLAN_ID_REF from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select PLAN_ID_REF from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select PLAN_ID_REF from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PLAN_ID_REF = $data[PLAN_ID_REF];

		$cmd = " select		PLAN_NAME
						 from		PER_TRAIN_PLAN
						 where	PLAN_ID=$PLAN_ID
						 order by PLAN_NAME
					  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "Data :: <pre>"; print_r($data); echo "</pre>";
		$PLAN_NAME = $data[PLAN_NAME];
		
	}else{
		$PLAN_NAME = "";
	} // end if
	

	if($PLAN_ID && $TREE_DEPTH == '2'){
		$PROJ_ID = $PLAN_ID;

		$cmd = " select PROJ_NAME,TPJ_MANAGE_ORG,TPJ_RESPONSE_ORG ,
						TPJ_APP_PER_ID,PG_ID,TPJ_APP_DATE,TPJ_APP_DOC_NO,
						TPJ_INOUT_TRAIN,TPJ_ZONE
					from		PER_TRAIN_PROJECT 
					where PROJ_ID=$PROJ_ID";
		$db_dpis->send_cmd($cmd);
		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		//echo "Data :: <pre>"; print_r($data); echo "</pre>";
		foreach($data as $key => $values) {
			//echo "$key => $values<br>";
			$$key = $values;
		}
		$TPJ_APP_DATE = implode('/',array_reverse(explode('-',$TPJ_APP_DATE)));
	}else{
		$PLAN_NAME = "";
	} // end if

	function list_tree_plan ($pre_image, $plan_parent, $sel_plan_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PLAN, $TP_BUDGET_YEAR, $DEPARTMENT_ID;
	
		$arr_opened_plan = explode(",",substr($LIST_OPENED_PLAN, 1, -1));

//		print_r($arr_opened_plan);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if($tree_depth == '1') {
			$cmd = " select 	PLAN_ID , PLAN_NAME, PLAN_ID_REF 
				   from 		PER_TRAIN_PLAN 
				   where (PLAN_ID_REF = 0 or PLAN_ID_REF is null) 
				   			and TP_BUDGET_YEAR='$TP_BUDGET_YEAR' and DEPARTMENT_ID=$DEPARTMENT_ID
				   order by 	PLAN_NAME ";
		} elseif($tree_depth == '2') {
				$cmd = "select PROJ_ID as PLAN_ID, PROJ_NAME as PLAN_NAME,PLAN_ID as PLAN_ID_REF
				from PER_TRAIN_PROJECT 
				where PLAN_ID = '$plan_parent'";
		}
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
//				print_r($data);
//				if($tree_depth == '1') {
					$cmd = " select PROJ_ID as PLAN_ID from PER_TRAIN_PROJECT where PLAN_ID=". $data[PLAN_ID];
					$count_sub_tree = $db_dpis2->send_cmd($cmd);
//				}
//				$db_dpis2->show_error();
//				echo "$cmd<br>";

				$class = "table_body";
				if($data[PLAN_ID] == $sel_plan_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_plan(". $data[PLAN_ID] .");";
				if(in_array($data[PLAN_ID], $arr_opened_plan)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_plan(". $data[PLAN_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_plan(". $data[PLAN_ID] .",". ($data[PLAN_ID_REF] + 0) .",". $tree_depth .");\" style=\"cursor:hand\">" . $data[PLAN_NAME] . "</span></td>";
				echo "</tr>";
				
				if(in_array($data[PLAN_ID], $arr_opened_plan)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[PLAN_ID], $arr_opened_plan)) $display = "block";
					echo "<div id=\"DIV_". $data[PLAN_ID] ."\" style=\"display:$display\">";
					list_tree_plan("", $data[PLAN_ID], $sel_plan_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if

			} // end while						
			echo "</table>";
		} // end if
	} // function

	function delete_plan($PLAN_ID, $PLAN_ID_REF){	
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PLAN;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select PROG_ID from PER_TRAIN_PROJECT where PLAN_ID=$PLAN_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_plan($data[PLAN_ID], $data[PLAN_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		$LIST_OPENED_PLAN = str_replace(",$PLAN_ID,", ",", $LIST_OPENED_PLAN);
		
		return;
	} // function
?>