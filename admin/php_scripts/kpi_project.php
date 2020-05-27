<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
//	echo "1..PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	if($_POST[PJ_YEAR])		$PJ_YEAR = $_POST[PJ_YEAR];
//	echo "2..PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	
	//	$BKK_FLAG==1
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID = " (DEPARTMENT_ID=".$DEPARTMENT_ID;
	if($where_DEPARTMENT_ID){	$where_DEPARTMENT_ID .= " or DEPARTMENT_ID=0)"; 	}else{	$where_DEPARTMENT_ID .= " DEPARTMENT_ID=0";	}
	if($ORG_ID) $where_DEPARTMENT_ID .= " and ORG_ID=".$ORG_ID;

//	echo "3..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID,  NEW_PJ_NAME=".trim($NEW_PJ_NAME).", NEW_PJ_PER_ID=$NEW_PJ_PER_ID, NEW_PFR_ID=$NEW_PFR_ID<br>";
	if($command=="ADD" && $PJ_YEAR && trim($NEW_PJ_NAME) && $NEW_PJ_PER_ID && $NEW_PFR_ID){
		$PJ_ID_REF = $PJ_ID;
		if(!$PJ_ID) $PJ_ID_REF = "NULL";
		if(!$NEW_KPI_ID) $NEW_KPI_ID = "NULL";
		if(!$ORG_ID)	$ORG_ID=0;
		if(!$NEW_PJ_EVALUATION) $NEW_PJ_EVALUATION = "NULL";
		if(!$NEW_PJ_BUDGET_RECEIVE)	$NEW_PJ_BUDGET_RECEIVE=0;
		if(!$NEW_PJ_BUDGET_USED)	$NEW_PJ_BUDGET_USED=0;
		$NEW_START_DATE =  save_date($NEW_START_DATE);
		$NEW_END_DATE =  save_date($NEW_END_DATE);

		$cmd = " select max(PJ_ID) as max_id from PER_PROJECT ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PJ_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_PROJECT	(PJ_ID, PJ_NAME, PJ_YEAR, KPI_ID, PFR_ID, PJ_TYPE, PJ_CLASS, PJ_STATUS, PJ_EVALUATION, 
						PJ_REPORT_STATUS, PJ_TARGET_STATUS, DEPARTMENT_ID, ORG_ID, START_DATE,  END_DATE, PJ_OBJECTIVE, 
						PJ_TARGET, PJ_ID_REF, UPDATE_USER, UPDATE_DATE, PJ_BUDGET_RECEIVE, PJ_BUDGET_USED)
						values ($PJ_ID, '$NEW_PJ_NAME', '$PJ_YEAR', $NEW_KPI_ID, $NEW_PFR_ID, '$NEW_PJ_TYPE', '$NEW_PJ_CLASS',
						'$NEW_PJ_STATUS', $NEW_PJ_EVALUATION, '$NEW_PJ_REPORT_STATUS', '$NEW_PJ_TARGET_STATUS', 
						$DEPARTMENT_ID, $ORG_ID,	'$NEW_START_DATE', '$NEW_END_DATE', '$NEW_PJ_OBJECTIVE', '$NEW_PJ_TARGET', 
						$PJ_ID_REF, $SESS_USERID, '$UPDATE_DATE', $NEW_PJ_BUDGET_RECEIVE, $NEW_PJ_BUDGET_USED) ";

		$db_dpis->send_cmd($cmd);				// ไม่มีฟิล์ด ORG_NAME ในตาราง???
//		echo "$cmd<br>";
//	$db_dpis->show_error();

		$NEW_START_DATE = show_date_format($data[NEW_START_DATE], 1);
		$NEW_END_DATE = show_date_format($data[NEW_END_DATE], 1);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มโครงการย่อย [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");

		$PJ_ID_REF += 0;

		// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
		update_parent_evaluate($PJ_ID, $PJ_ID_REF);
		// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน PJ PARENT ของ [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");
	} // end if
	
	if($command=="UPDATE" && $PJ_ID && $PJ_YEAR && trim($PJ_NAME) && $PJ_PER_ID && $PFR_ID){
		if(!$KPI_ID) $KPI_ID = "NULL";
		if(!$ORG_ID) $ORG_ID = "NULL";
		if(!$PJ_EVALUATION) $PJ_EVALUATION = "NULL";
		if(!$PJ_BUDGET_RECEIVE)	$PJ_BUDGET_RECEIVE=0;
		if(!$PJ_BUDGET_USED)	$PJ_BUDGET_USED=0;
		$START_DATE =  save_date($START_DATE);
		$END_DATE =  save_date($END_DATE);
		
		$cmd = " update PER_PROJECT set
							PJ_NAME='$PJ_NAME', 
							PJ_YEAR='$PJ_YEAR',
							KPI_ID=$KPI_ID,
							PFR_ID=$PFR_ID,
							PJ_TYPE='$PJ_TYPE',
							PJ_CLASS='$PJ_CLASS',
							PJ_STATUS='$PJ_STATUS',
							PJ_EVALUATION=$PJ_EVALUATION,
							PJ_REPORT_STATUS='$PJ_REPORT_STATUS',
							PJ_TARGET_STATUS='$PJ_TARGET_STATUS',
							DEPARTMENT_ID=$DEPARTMENT_ID,
							ORG_ID=$ORG_ID,
							START_DATE='$START_DATE',
							END_DATE='$END_DATE',
							PJ_OBJECTIVE='$PJ_OBJECTIVE',
							PJ_TARGET='$PJ_TARGET', 
							PJ_BUDGET_RECEIVE=$PJ_BUDGET_RECEIVE,
							PJ_BUDGET_USED=$PJ_BUDGET_USED,
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where PJ_ID=$PJ_ID "; 
					
		$db_dpis->send_cmd($cmd);
		echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงการ [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");

		if($PJ_LEAF_NODE){
			// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
			update_parent_evaluate($PJ_ID, $PJ_ID_REF);
			// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน PJ PARENT ของ [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");
		} // end if
	} // end if

	if($command=="CHANGEPJPARENT" && isset($NEW_PJ_ID_REF)){
		if(!$NEW_PJ_ID_REF) $NEW_PJ_ID_REF = "NULL";

		$cmd = " update PER_PROJECT set PJ_ID_REF=$NEW_PJ_ID_REF where PJ_ID=$PJ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับโครงการ [$PJ_ID : $PJ_NAME | $PJ_ID_REF => $NEW_PJ_ID_REF]");	

		$PJ_ID_REF = $NEW_PJ_ID_REF;
		$PJ_ID_REF += 0;

		// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
		update_parent_evaluate($PJ_ID, $PJ_ID_REF);
		// ================================= UPDATE PJ_EVALUATE OF PJ PARENT ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน PJ PARENT ของ [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");
	} // end if

	if($command=="DELETE" && $PJ_ID){
		delete_pj($PJ_ID, $PJ_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบโครงการ [$PJ_ID_REF : $PJ_ID : $PJ_NAME]");

		$PJ_ID = $PJ_ID_REF + 0;
		unset($PJ_ID_REF);
		
		// ================================= UPDATE PJ_EVALUATE ============================= //
		$cmd = " select SUM(PJ_EVALUATE) as SUM_PJ_EVALUATE, COUNT(PJ_ID) as COUNT_PJ_CHILD from PER_PROJECT where PJ_ID_REF=$PJ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SUM_PJ_EVALUATE = $data[SUM_PJ_EVALUATE] + 0;
		$COUNT_PJ_CHILD = $data[COUNT_PJ_CHILD] + 0;
			
		$PJ_EVALUATE = "NULL";
		if($SUM_PJ_EVALUATE > 0 && $COUNT_PJ_CHILD > 0) $PJ_EVALUATE = floor($SUM_PJ_EVALUATE / $COUNT_PJ_CHILD);

		$cmd = " update PER_PROJECT set PJ_EVALUATE=$PJ_EVALUATE where PJ_ID=$PJ_ID ";
		$db_dpis->send_cmd($cmd);
		// ================================= UPDATE PJ_EVALUATE ============================= //
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงผลการประเมิน [$PJ_ID : $PJ_NAME]");
	} // end if

	$cmd = " select distinct PJ_YEAR from PER_PROJECT where (".$where_DEPARTMENT_ID.") order by PJ_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[PJ_YEAR];
		$arr_pj_year[] = $data[PJ_YEAR];
	} // end while
	
//	echo "<pre>"; print_r($arr_pj_year); echo "</pre>";

//	echo "8..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	if($command=="ADDYEAR" && $NEW_PJ_YEAR){
		$PJ_YEAR = $NEW_PJ_YEAR;
		if(!in_array($NEW_PJ_YEAR, $arr_pj_year)) $arr_pj_year[] = $NEW_PJ_YEAR;
		sort($arr_pj_year);
		$HAVE_YEAR += 1;	
		
//		echo "<pre>"; print_r($arr_pj_year); echo "</pre>";
	} // end if
		
//	echo "9..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	if(!$PJ_YEAR || !in_array($PJ_YEAR, $arr_pj_year)) $PJ_YEAR = $START_YEAR;

//	echo "10..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	$cmd = " select PJ_ID from PER_PROJECT ";
	$HAVE_PJ = $db_dpis->send_cmd($cmd);

//	echo "11..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	if($PJ_ID)	{
		$cmd = " select		PJ_NAME, PJ_YEAR, KPI_ID, PFR_ID, PJ_TYPE, PJ_CLASS, PJ_STATUS, 
										PJ_EVALUATION, PJ_REPORT_STATUS, PJ_TARGET_STATUS, DEPARTMENT_ID, ORG_ID, 
										START_DATE, END_DATE, PJ_OBJECTIVE, PJ_TARGET, PJ_ID_REF, PJ_BUDGET_RECEIVE, PJ_BUDGET_USED
						 from		PER_PROJECT
						 where	PJ_ID=$PJ_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "Data :: <pre>"; print_r($data); echo "</pre>";
		$PJ_NAME = trim($data[PJ_NAME]);
		$PJ_YEAR = trim($data[PJ_YEAR]);
		$KPI_ID = $data[KPI_ID];
		$PFR_ID = $data[PFR_ID];
		$PJ_TYPE = trim($data[PJ_TYPE]);
		$PJ_CLASS = trim($data[PJ_CLASS]);
		$PJ_STATUS = trim($data[PJ_STATUS]);
		$PJ_EVALUATION = $data[PJ_EVALUATION];
		$PJ_REPORT_STATUS = trim($data[PJ_REPORT_STATUS]);
		$PJ_TARGET_STATUS = trim($data[PJ_TARGET_STATUS]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$ORG_ID = $data[ORG_ID];
		$START_DATE = show_date_format($data[START_DATE], 1);
		$END_DATE = show_date_format($data[END_DATE], 1);
		$PJ_OBJECTIVE = trim($data[PJ_OBJECTIVE]);
		$PJ_TARGET = trim($data[PJ_TARGET]);
		$PJ_ID_REF = $data[PJ_ID_REF];
		$PJ_BUDGET_RECEIVE = $data[PJ_BUDGET_RECEIVE];
		$PJ_BUDGET_USED = $data[PJ_BUDGET_USED];

		$cmd = " select KPI_NAME from PER_KPI where KPI_ID=$KPI_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$KPI_NAME = $data2[KPI_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];	
		
		$cmd = " select PFR_NAME from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PFR_NAME = $data2[PFR_NAME];
		
		$cmd = " select PJ_ID from PER_PROJECT where PJ_ID_REF=$PJ_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
		$PJ_LEAF_NODE = 0;
		if(!$count_child) $PJ_LEAF_NODE = 1;
	}else{
		$PJ_NAME = "";
//		$PJ_YEAR = "";	// ตัดออกเพราะ ถ้ามีแล้วมันจะเพิ่ม ปีงบประมาณใหม่ไม่ได้
		$KPI_ID = "";
		$KPI_NAME = "";
		$PFR_ID = "";
		$PFR_NAME = "";
		$PJ_TYPE = "";
		$PJ_CLASS = "";
		$PJ_STATUS = "";
		$PJ_EVALUATION = "";
		$PJ_REPORT_STATUS = "";
		$PJ_TARGET_STATUS = "";
		$START_DATE = "";
		$END_DATE = "";
		$PJ_OBJECTIVE = "";
		$PJ_TARGET = "";
		$PJ_LEAF_NODE = 0;
	} // end if

//	echo "12..command=$command, PJ_YEAR=$PJ_YEAR,  PJ_ID=$PJ_ID<br>";
	function list_tree_pj ($pre_image, $pj_parent, $sel_pj_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PJ, $PJ_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID;
		
		$opened_pj = substr($LIST_OPENED_PJ, 1, -1);
		$arr_opened_pj = explode(",", $opened_pj);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if ($BKK_FLAG==1) $order_str = "PJ_TYPE, PJ_NAME";
		else $order_str = "PJ_NAME";

		$cmd = " select 	PJ_ID , PJ_NAME, PJ_ID_REF 
				   from 		PER_PROJECT 
				   where 	".(trim($pj_parent)?"PJ_ID_REF = $pj_parent":"(PJ_ID_REF = 0 or PJ_ID_REF is null)")." 
				   			and PJ_YEAR='$PJ_YEAR' and ($where_DEPARTMENT_ID)
				   order by 	$order_str ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select PJ_ID from PER_PROJECT where PJ_ID_REF=". $data[PJ_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[PJ_ID] == $sel_pj_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_pj(". $data[PJ_ID] .");";
				if(in_array($data[PJ_ID], $arr_opened_pj)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_pj(". $data[PJ_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "ball.gif";

				$PJ_TARGET_LEVEL1 = $data[PJ_TARGET_LEVEL1];
				$PJ_TARGET_LEVEL2 = $data[PJ_TARGET_LEVEL2];
				$PJ_TARGET_LEVEL3 = $data[PJ_TARGET_LEVEL3];
				$PJ_TARGET_LEVEL4 = $data[PJ_TARGET_LEVEL4];
				$PJ_TARGET_LEVEL5 = $data[PJ_TARGET_LEVEL5];
				$PJ_TARGET_LEVEL1_DESC = $data[PJ_TARGET_LEVEL1_DESC];
				$PJ_TARGET_LEVEL2_DESC = $data[PJ_TARGET_LEVEL2_DESC];
				$PJ_TARGET_LEVEL3_DESC = $data[PJ_TARGET_LEVEL3_DESC];
				$PJ_TARGET_LEVEL4_DESC = $data[PJ_TARGET_LEVEL4_DESC];
				$PJ_TARGET_LEVEL5_DESC = $data[PJ_TARGET_LEVEL5_DESC];
				$PJ_EVALUATE = $data[PJ_EVALUATE];
				$PJ_RESULT = $data[PJ_RESULT];
				switch($PJ_EVALUATE){
					case 1 :
						$PJ_IMG = "images/ball_red.gif";
						break;
					case 2 :
						$PJ_IMG = "images/ball_orange.gif";
						break;
					case 3 :
						$PJ_IMG = "images/ball_yellow.gif";
						break;
					case 4 :
						$PJ_IMG = "images/ball_green_light.gif";
						break;
					case 5 :
						$PJ_IMG = "images/ball_green.gif";
						break;
					default :
						$PJ_IMG = "images/space.gif";
				} // end switch case

				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<img src=\"$PJ_IMG\" width=\"11\" height=\"11\" hspace=\"4\"><span onClick=\"select_pj(". $data[PJ_ID] .",". ($data[PJ_ID_REF] + 0) .");\" style=\"cursor:hand\">" . $data[PJ_NAME] . "</span></td>";
				echo "</tr>";
				if($count_sub_tree && in_array($data[PJ_ID], $arr_opened_pj)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[PJ_ID], $arr_opened_pj)) $display = "block";
					echo "<div id=\"DIV_". $data[PJ_ID] ."\" style=\"display:$display\">";
					list_tree_pj("", $data[PJ_ID], $sel_pj_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function

	function delete_pj($PJ_ID, $PJ_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PJ;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select PJ_ID, PJ_ID_REF from PER_PROJECT where PJ_ID_REF=$PJ_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_pj($data[PJ_ID], $data[PJ_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_PROJECT where PJ_ID=$PJ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_PJ = str_replace(",$PJ_ID,", ",", $LIST_OPENED_PJ);
		
		return;
	} // function

	function update_parent_evaluate($PJ_ID, $PJ_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $PJ_YEAR;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if(!$PJ_ID_REF){
			return;
		}else{
			$cmd = " select PJ_ID_REF from PER_PROJECT where PJ_ID=$PJ_ID_REF ";
			$db_dpis->send_cmd($cmd);
//			echo "select parent : $cmd <br>";
			$data = $db_dpis->get_array();			
			$PJ_ID = $PJ_ID_REF;
			$PJ_ID_REF = $data[PJ_ID_REF];

			$cmd = " select SUM(PJ_EVALUATE) as SUM_PJ_EVALUATE, COUNT(PJ_ID) as COUNT_PJ_CHILD from PER_PROJECT where PJ_ID_REF=$PJ_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SUM_PJ_EVALUATE = $data[SUM_PJ_EVALUATE] + 0;
			$COUNT_PJ_CHILD = $data[COUNT_PJ_CHILD] + 0;
			
			$PJ_EVALUATE = "NULL";
			if($SUM_PJ_EVALUATE > 0 && $COUNT_PJ_CHILD > 0) $PJ_EVALUATE = floor($SUM_PJ_EVALUATE / $COUNT_PJ_CHILD);

			$cmd = " update PER_PROJECT set PJ_EVALUATE=$PJ_EVALUATE where PJ_ID=$PJ_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "update parent : $cmd <br>";

			update_parent_evaluate($PJ_ID, $PJ_ID_REF);
		} // end if
	} // function
?>