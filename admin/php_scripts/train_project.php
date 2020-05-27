<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" && $TPJ_BUDGET_YEAR && trim($NEW_PROJ_NAME) && $NEW_KPI_PER_ID && $NEW_PFR_ID){
		$PROJ_ID_REF = $PROJ_ID;
		if(!$PROJ_ID) $PROJ_ID_REF = "NULL";

		if(!$NEW_KPI_WEIGHT) $NEW_KPI_WEIGHT = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL1) $NEW_KPI_TARGET_LEVEL1 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL2) $NEW_KPI_TARGET_LEVEL2 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL3) $NEW_KPI_TARGET_LEVEL3 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL4) $NEW_KPI_TARGET_LEVEL4 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL5) $NEW_KPI_TARGET_LEVEL5 = "NULL";
		if(!$NEW_KPI_EVALUATE) $NEW_KPI_EVALUATE = "NULL";
		if (!get_magic_quotes_gpc()) {
			$NEW_KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL1_DESC)));
			$NEW_KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL2_DESC)));
			$NEW_KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL3_DESC)));
			$NEW_KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL4_DESC)));
			$NEW_KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($NEW_KPI_TARGET_LEVEL5_DESC)));
		}else{
			$NEW_KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL1_DESC))));
			$NEW_KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL2_DESC))));
			$NEW_KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL3_DESC))));
			$NEW_KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL4_DESC))));
			$NEW_KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($NEW_KPI_TARGET_LEVEL5_DESC))));
		} // end if
		
		
		$cmd = " select max(PROJ_ID) as max_id from PER_TRAIN_PROJECT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PROJ_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRAIN_PROJECT
							(PROJ_ID, PROJ_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID,ORG_NAME, UNDER_ORG_NAME1, PFR_ID,
							 KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
							 KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, 
							 KPI_TARGET_LEVEL5_DESC, KPI_EVALUATE,
							 PROJ_ID_REF, TPJ_BUDGET_YEAR, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
						values
							($PROJ_ID, '$NEW_PROJ_NAME', $NEW_KPI_WEIGHT, '$NEW_KPI_MEASURE', $NEW_KPI_PER_ID, $NEW_ORG_NAME, $NEW_UNDER_ORG_NAME1, $NEW_PFR_ID,
							 $NEW_KPI_TARGET_LEVEL1, $NEW_KPI_TARGET_LEVEL2, $NEW_KPI_TARGET_LEVEL3, $NEW_KPI_TARGET_LEVEL4, 
							 $NEW_KPI_TARGET_LEVEL5,	'$NEW_KPI_TARGET_LEVEL1_DESC', '$NEW_KPI_TARGET_LEVEL2_DESC', 
							 '$NEW_KPI_TARGET_LEVEL3_DESC', '$NEW_KPI_TARGET_LEVEL4_DESC', '$NEW_KPI_TARGET_LEVEL5_DESC', 
							 $NEW_KPI_EVALUATE, $PROJ_ID_REF, '$TPJ_BUDGET_YEAR', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE')
					  ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มตัวชี้วัดย่อย [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");

		$PROJ_ID_REF += 0;

		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
		update_parent_evaluate($PROJ_ID, $PROJ_ID_REF);
		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");
	} // end if
	
	if($command=="UPDATE" && $PROJ_ID && $TPJ_BUDGET_YEAR && trim($PROJ_NAME) && $KPI_PER_ID && $PFR_ID){
		if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
		if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
		if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
		if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
		if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
		if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
		if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
		if(!$KPI_EVALUATE) $KPI_EVALUATE = "NULL";
		if (!get_magic_quotes_gpc()) {
			$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL1_DESC)));
			$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL2_DESC)));
			$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL3_DESC)));
			$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL4_DESC)));
			$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", trim($KPI_TARGET_LEVEL5_DESC)));
		}else{
			$KPI_TARGET_LEVEL1_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL1_DESC))));
			$KPI_TARGET_LEVEL2_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL2_DESC))));
			$KPI_TARGET_LEVEL3_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL3_DESC))));
			$KPI_TARGET_LEVEL4_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL4_DESC))));
			$KPI_TARGET_LEVEL5_DESC = addslashes(str_replace('"', "&quot;", stripslashes(trim($KPI_TARGET_LEVEL5_DESC))));
		} // end if
		
		$updae_evaulate = "";
		if($KPI_LEAF_NODE) $update_evaluate = "KPI_EVALUATE=$KPI_EVALUATE,";

		$cmd = " update PER_TRAIN_PROJECT set
							PROJ_NAME='$PROJ_NAME', 
							KPI_WEIGHT='$KPI_WEIGHT',
							KPI_MEASURE='$KPI_MEASURE',
							KPI_PER_ID='$KPI_PER_ID',
							ORG_NAME='$ORG_NAME',
							UNDER_ORG_NAME1='$UNDER_ORG_NAME1',
							PFR_ID='$PFR_ID',
							KPI_TARGET_LEVEL1=$KPI_TARGET_LEVEL1,
							KPI_TARGET_LEVEL2=$KPI_TARGET_LEVEL2,
							KPI_TARGET_LEVEL3=$KPI_TARGET_LEVEL3,
							KPI_TARGET_LEVEL4=$KPI_TARGET_LEVEL4,
							KPI_TARGET_LEVEL5=$KPI_TARGET_LEVEL5,
							KPI_TARGET_LEVEL1_DESC='$KPI_TARGET_LEVEL1_DESC',
							KPI_TARGET_LEVEL2_DESC='$KPI_TARGET_LEVEL2_DESC',
							KPI_TARGET_LEVEL3_DESC='$KPI_TARGET_LEVEL3_DESC',
							KPI_TARGET_LEVEL4_DESC='$KPI_TARGET_LEVEL4_DESC',
							KPI_TARGET_LEVEL5_DESC='$KPI_TARGET_LEVEL5_DESC',
							$update_evaluate
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						 where PROJ_ID=$PROJ_ID
					  ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงตัวชี้วัด [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");

		if($KPI_LEAF_NODE){
			// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			update_parent_evaluate($PROJ_ID, $PROJ_ID_REF);
			// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");
		} // end if
	} // end if

	if($command=="CHANGEKPIPARENT" && isset($NEW_PROJ_ID_REF)){
		if(!$NEW_PROJ_ID_REF) $NEW_PROJ_ID_REF = "NULL";

		$cmd = " update PER_TRAIN_PROJECT set PROJ_ID_REF=$NEW_PROJ_ID_REF where PROJ_ID=$PROJ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับตัวชี้วัด [$PROJ_ID : $PROJ_NAME | $PROJ_ID_REF => $NEW_PROJ_ID_REF]");	

		$PROJ_ID_REF = $NEW_PROJ_ID_REF;
		$PROJ_ID_REF += 0;

		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
		update_parent_evaluate($PROJ_ID, $PROJ_ID_REF);
		// ================================= UPDATE KPI_EVALUATE OF KPI PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน KPI PARENT ของ [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");
	} // end if

	if($command=="DELETE" && $PROJ_ID){
		delete_kpi($PROJ_ID, $PROJ_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบตัวชี้วัด [$PROJ_ID_REF : $PROJ_ID : $PROJ_NAME]");

		$PROJ_ID = $PROJ_ID_REF + 0;
		unset($PROJ_ID_REF);
		
		// ================================= UPDATE KPI_EVALUATE ============================= //
		$cmd = " select SUM(KPI_EVALUATE) as SUM_KPI_EVALUATE, COUNT(PROJ_ID) as COUNT_KPI_CHILD from PER_TRAIN_PROJECT where PROJ_ID_REF=$PROJ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SUM_KPI_EVALUATE = $data[SUM_KPI_EVALUATE] + 0;
		$COUNT_KPI_CHILD = $data[COUNT_KPI_CHILD] + 0;
			
		$KPI_EVALUATE = "NULL";
		if($SUM_KPI_EVALUATE > 0 && $COUNT_KPI_CHILD > 0) $KPI_EVALUATE = floor($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD);

		$cmd = " update PER_TRAIN_PROJECT set KPI_EVALUATE=$KPI_EVALUATE where PROJ_ID=$PROJ_ID ";
		$db_dpis->send_cmd($cmd);
		// ================================= UPDATE KPI_EVALUATE ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน [$PROJ_ID : $PROJ_NAME]");
	} // end if

	$cmd = " select distinct TPJ_BUDGET_YEAR from PER_TRAIN_PROJECT where DEPARTMENT_ID=$DEPARTMENT_ID order by TPJ_BUDGET_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[TPJ_BUDGET_YEAR];
		$arr_kpi_year[] = $data[TPJ_BUDGET_YEAR];
	} // end while
	
//	echo "<pre>"; print_r($arr_kpi_year); echo "</pre>";

	if($command=="ADDYEAR" && $NEW_TPJ_BUDGET_YEAR){
		$TPJ_BUDGET_YEAR = $NEW_TPJ_BUDGET_YEAR;
		if(!in_array($NEW_TPJ_BUDGET_YEAR, $arr_kpi_year)) $arr_kpi_year[] = $NEW_TPJ_BUDGET_YEAR;
		sort($arr_kpi_year);
		$HAVE_YEAR += 1;	
		
//		echo "<pre>"; print_r($arr_kpi_year); echo "</pre>";
	} // end if
		
	if(!$TPJ_BUDGET_YEAR || !in_array($TPJ_BUDGET_YEAR, $arr_kpi_year)) $TPJ_BUDGET_YEAR = $START_YEAR;

	$cmd = " select PROJ_ID from PER_TRAIN_PROJECT ";
	$HAVE_KPI = $db_dpis->send_cmd($cmd);

	if($PROJ_ID)	{
		$cmd = " select PROJ_ID_REF from PER_TRAIN_PROJECT where PROJ_ID=$PROJ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROJ_ID_REF = $data[PROJ_ID_REF];

		$cmd = " select		PROJ_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID,ORG_NAME, UNDER_ORG_NAME1, PFR_ID, 
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, 
										KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, 
										KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC, KPI_EVALUATE
						 from		PER_TRAIN_PROJECT
						 where	PROJ_ID=$PROJ_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "Data :: <pre>"; print_r($data); echo "</pre>";
		$PROJ_NAME = $data[PROJ_NAME];
		$KPI_WEIGHT = $data[KPI_WEIGHT];
		$KPI_MEASURE = $data[KPI_MEASURE];
		$ORG_NAME = $data[ORG_NAME];
		$UNDER_ORG_NAME1 = $data[UNDER_ORG_NAME1];
		$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
		$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
		$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
		$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
		$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
		$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
		$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
		$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
		$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
		$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
		$KPI_EVALUATE = $data[KPI_EVALUATE];
		
		$KPI_PER_ID = $data[KPI_PER_ID];
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$KPI_PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$KPI_PER_NAME = $data2[PER_NAME] ." ". $data2[PER_SURNAME];
		
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$data2[PN_CODE]' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$KPI_PER_NAME = $data2[PN_NAME] . $KPI_PER_NAME;	
	
		$PFR_ID = $data[PFR_ID];
		$cmd = " select PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PFR_NAME = $data2[PFR_NAME];
		
		$cmd = " select PROJ_ID from PER_TRAIN_PROJECT where PROJ_ID_REF=$PROJ_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
		$KPI_LEAF_NODE = 0;
		if(!$count_child) $KPI_LEAF_NODE = 1;
	}else{
		$PROJ_NAME = "";
		$KPI_WEIGHT = "";
		$KPI_MEASURE = "";
		$KPI_PER_ID = "";
		$KPI_PER_NAME = "";
		$ORG_NAME = "";
		$UNDER_ORG_NAME1 = "";
		$PFR_ID = "";
		$PFR_NAME = "";
		$KPI_TARGET_LEVEL1 = "";
		$KPI_TARGET_LEVEL2 = "";
		$KPI_TARGET_LEVEL3 = "";
		$KPI_TARGET_LEVEL4 = "";
		$KPI_TARGET_LEVEL5 = "";
		$KPI_TARGET_LEVEL1_DESC = "";
		$KPI_TARGET_LEVEL2_DESC = "";
		$KPI_TARGET_LEVEL3_DESC = "";
		$KPI_TARGET_LEVEL4_DESC = "";
		$KPI_TARGET_LEVEL5_DESC = "";
		$KPI_EVALUATE = "";
		$KPI_LEAF_NODE = 0;
	} // end if

	function list_tree_kpi ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $TPJ_BUDGET_YEAR, $DEPARTMENT_ID;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
                
		$cmd = " select 	PROJ_ID , PLAN_ID, PROJ_NAME, PROJ_ID_REF, 
										TPJ_BUDGET_YEAR, TPJ_MANAGE_ORG, TPJ_RESPONSE_ORG, TPJ_APP_PER_ID, PG_ID, 
										TPJ_APP_DATE, TPJ_APP_DOC_NO, TPJ_INOUT_TRAIN, 
										TPJ_ZONE, TPJ_ACTIVE 
				   from 		PER_TRAIN_PROJECT 
				   where 	".(trim($kpi_parent)?"PROJ_ID_REF = $kpi_parent":"(PROJ_ID_REF = 0 or PROJ_ID_REF is null)")." 
				   			and TPJ_BUDGET_YEAR='$TPJ_BUDGET_YEAR' and DEPARTMENT_ID=$DEPARTMENT_ID
				   order by 	PROJ_NAME ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select PROJ_ID from PER_TRAIN_PROJECT where PROJ_ID_REF=". $data[PROJ_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[PROJ_ID] == $sel_kpi_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_kpi(". $data[PROJ_ID] .");";
				if(in_array($data[PROJ_ID], $arr_opened_kpi)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_kpi(". $data[PROJ_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "";

				$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
				$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
				$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
				$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
				$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
				$KPI_TARGET_LEVEL1_DESC = $data[KPI_TARGET_LEVEL1_DESC];
				$KPI_TARGET_LEVEL2_DESC = $data[KPI_TARGET_LEVEL2_DESC];
				$KPI_TARGET_LEVEL3_DESC = $data[KPI_TARGET_LEVEL3_DESC];
				$KPI_TARGET_LEVEL4_DESC = $data[KPI_TARGET_LEVEL4_DESC];
				$KPI_TARGET_LEVEL5_DESC = $data[KPI_TARGET_LEVEL5_DESC];
				$KPI_EVALUATE = $data[KPI_EVALUATE];

				switch($KPI_EVALUATE){
					case 1 :
						$KPI_IMG = "images/ball_red.gif";
						break;
					case 2 :
						$KPI_IMG = "images/ball_orange.gif";
						break;
					case 3 :
						$KPI_IMG = "images/ball_yellow.gif";
						break;
					case 4 :
						$KPI_IMG = "images/ball_green_light.gif";
						break;
					case 5 :
						$KPI_IMG = "images/ball_green.gif";
						break;
					default :
						$KPI_IMG = "images/space.gif";
				} // end switch case

				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<img src=\"$KPI_IMG\" width=\"11\" height=\"11\" hspace=\"4\"><span onClick=\"select_kpi(". $data[PROJ_ID] .",". ($data[PROJ_ID_REF] + 0) .");\" style=\"cursor:hand\">" . $data[PROJ_NAME] . "</span></td>";
				echo "</tr>";
				if($count_sub_tree && in_array($data[PROJ_ID], $arr_opened_kpi)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[PROJ_ID], $arr_opened_kpi)) $display = "block";
					echo "<div id=\"DIV_". $data[PROJ_ID] ."\" style=\"display:$display\">";
					list_tree_kpi("", $data[PROJ_ID], $sel_kpi_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function

	function delete_kpi($PROJ_ID, $PROJ_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select PROJ_ID, PROJ_ID_REF from PER_TRAIN_PROJECT where PROJ_ID_REF=$PROJ_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_kpi($data[PROJ_ID], $data[PROJ_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_TRAIN_PROJECT where PROJ_ID=$PROJ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_KPI = str_replace(",$PROJ_ID,", ",", $LIST_OPENED_KPI);
		
		return;
	} // function

	function update_parent_evaluate($PROJ_ID, $PROJ_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $TPJ_BUDGET_YEAR;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if(!$PROJ_ID_REF){
			return;
		}else{
			$cmd = " select PROJ_ID_REF from PER_TRAIN_PROJECT where PROJ_ID=$PROJ_ID_REF ";
			$db_dpis->send_cmd($cmd);
//			echo "select parent : $cmd <br>";
			$data = $db_dpis->get_array();			
			$PROJ_ID = $PROJ_ID_REF;
			$PROJ_ID_REF = $data[PROJ_ID_REF];

			$cmd = " select SUM(KPI_EVALUATE) as SUM_KPI_EVALUATE, COUNT(PROJ_ID) as COUNT_KPI_CHILD from PER_TRAIN_PROJECT where PROJ_ID_REF=$PROJ_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SUM_KPI_EVALUATE = $data[SUM_KPI_EVALUATE] + 0;
			$COUNT_KPI_CHILD = $data[COUNT_KPI_CHILD] + 0;
			
			$KPI_EVALUATE = "NULL";
			if($SUM_KPI_EVALUATE > 0 && $COUNT_KPI_CHILD > 0) $KPI_EVALUATE = floor($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD);

			$cmd = " update PER_TRAIN_PROJECT set KPI_EVALUATE=$KPI_EVALUATE where PROJ_ID=$PROJ_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "update parent : $cmd <br>";

			update_parent_evaluate($PROJ_ID, $PROJ_ID_REF);
		} // end if
	} // function
?>