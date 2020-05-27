<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	//	$BKK_FLAG==1
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID=" DEPARTMENT_ID=".$DEPARTMENT_ID;
	if($where_DEPARTMENT_ID){	$where_DEPARTMENT_ID .= " or DEPARTMENT_ID=0"; 	}else{	$where_DEPARTMENT_ID .= " DEPARTMENT_ID=0";	}
	
	if($command=="ADD" && $PFR_YEAR && trim($NEW_PFR_NAME)){
		$PFR_ID_REF = $PFR_ID;
		if(!$PFR_ID) $PFR_ID_REF = "NULL";
		if(!$DEPARTMENT_ID)	$DEPARTMENT_ID=0;		//		$BKK_FLAG==1
				
		$cmd = " select max(PFR_ID) as max_id from PER_PERFORMANCE_REVIEW ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PFR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_PERFORMANCE_REVIEW (PFR_ID, PFR_NAME, PFR_ID_REF, PFR_YEAR, PFR_TYPE, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
						values ($PFR_ID, '$NEW_PFR_NAME', $PFR_ID_REF, '$PFR_YEAR', '$NEW_PFR_TYPE', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประเด็นย่อย [$PFR_ID_REF : $PFR_ID : $PFR_NAME]");
		
		$PFR_ID_REF += 0;
		$DEPARTMENT_ID_SELECTED = $DEPARTMENT_ID;
	} // end if
	
	if($command=="UPDATE" && $PFR_ID && $PFR_YEAR && trim($PFR_NAME)){
		$cmd = " update PER_PERFORMANCE_REVIEW set
							PFR_NAME='$PFR_NAME',
							PFR_TYPE='$PFR_TYPE',
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						 where PFR_ID=$PFR_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงประเด็น [$PFR_ID_REF : $PFR_ID : $PFR_NAME]");
	} // end if

	if($command=="CHANGEPFRPARENT" && $PFR_ID){
		if(!$NEW_PFR_ID_REF) $NEW_PFR_ID_REF = "NULL";
		
		$cmd = " update PER_PERFORMANCE_REVIEW set PFR_ID_REF=$NEW_PFR_ID_REF where PFR_ID=$PFR_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับประเด็น [$PFR_ID : $PFR_NAME | $PFR_ID_REF => $NEW_PFR_ID_REF]");	

		$PFR_ID_REF = $NEW_PFR_ID_REF;
		$PFR_ID_REF += 0;
	} // end if

	if($command=="DELETE" && $PFR_ID){
		delete_pfr($PFR_ID, $PFR_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประเด็น [$PFR_ID_REF : $PFR_ID : $PFR_NAME]");

		$PFR_ID = $PFR_ID_REF + 0;
		unset($PFR_ID_REF);
		
		$PFR_ID="";
		$DEPARTMENT_ID_SELECTED = "";
	} // end if

	$cmd = " select distinct PFR_YEAR from PER_PERFORMANCE_REVIEW where (".$where_DEPARTMENT_ID.") order by PFR_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[PFR_YEAR];
		$arr_pfr_year[] = $data[PFR_YEAR];
	} // end while
	
	if($command=="ADDYEAR" && $NEW_PFR_YEAR){
		$PFR_YEAR = $NEW_PFR_YEAR;
		if(!in_array($NEW_PFR_YEAR, $arr_pfr_year)) $arr_pfr_year[] = $NEW_PFR_YEAR;
		sort($arr_pfr_year);
		$HAVE_YEAR += 1;				
	} // end if
	
	if(!$PFR_YEAR || !in_array($PFR_YEAR, $arr_pfr_year)) $PFR_YEAR = $START_YEAR;

	// echo "<pre>"; print_r($arr_pfr_year);   echo "<br> $HAVE_YEAR / ".$PFR_YEAR; echo "</pre>";

	if($PFR_ID){
		if($DPISDB=="odbc"){
			$cmd = " select top 1 PFR_ID_REF from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select PFR_ID_REF from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select PFR_ID_REF from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PFR_ID_REF = $data[PFR_ID_REF];

		$cmd = " select		PFR_NAME, PFR_TYPE
						 from		PER_PERFORMANCE_REVIEW
						 where	PFR_ID=$PFR_ID
						 order by PFR_NAME ";
		$db_dpis->send_cmd($cmd);
//		echo $cmd;
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
//		echo "Data :: <pre>"; print_r($data); echo "</pre>";
		$PFR_NAME = $data[PFR_NAME];
		$PFR_TYPE = $data[PFR_TYPE];
	}else{
		$PFR_NAME = "";
		$PFR_TYPE = "";
	} // end if

	function list_tree_pfr ($pre_image, $pfr_parent, $sel_pfr_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PFR, $PFR_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID;
		
		$opened_pfr = substr($LIST_OPENED_PFR, 1, -1);
		$arr_opened_pfr = explode(",", $opened_pfr);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if ($BKK_FLAG==1) $order_str = "PFR_NAME"; // PFR_ID
		else $order_str = "PFR_NAME";

		$cmd = " select 	PFR_ID , PFR_NAME, PFR_ID_REF , DEPARTMENT_ID
				   from 		PER_PERFORMANCE_REVIEW 
				   where 	".(trim($pfr_parent)?"PFR_ID_REF = $pfr_parent":"(PFR_ID_REF = 0 or PFR_ID_REF is null)")." 
				   			and PFR_YEAR='$PFR_YEAR' and ($where_DEPARTMENT_ID)
				   order by 	$order_str ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select PFR_ID from PER_PERFORMANCE_REVIEW where PFR_ID_REF=". $data[PFR_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo "$cmd<br>";

				$class = "table_body";
				if($data[PFR_ID] == $sel_pfr_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_pfr(". $data[PFR_ID] .");";
				if(in_array($data[PFR_ID], $arr_opened_pfr)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_pfr(". $data[PFR_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "ball.gif";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_pfr(". $data[PFR_ID] .",". ($data[PFR_ID_REF] + 0) .",".$data[DEPARTMENT_ID].");\" style=\"cursor:hand\"> ". $data[PFR_NAME] . "</span></td>";
				echo "</tr>";
				
				if($count_sub_tree && in_array($data[PFR_ID], $arr_opened_pfr)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[PFR_ID], $arr_opened_pfr)) $display = "block";
					echo "<div id=\"DIV_". $data[PFR_ID] ."\" style=\"display:$display\">";
					list_tree_pfr("", $data[PFR_ID], $sel_pfr_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table>";
		} // end if
	} // function

	function delete_pfr($PFR_ID, $PFR_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PFR;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select PFR_ID, PFR_ID_REF from PER_PERFORMANCE_REVIEW where PFR_ID_REF=$PFR_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_pfr($data[PFR_ID], $data[PFR_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_PERFORMANCE_REVIEW where PFR_ID=$PFR_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		$LIST_OPENED_PFR = str_replace(",$PFR_ID,", ",", $LIST_OPENED_PFR);
		
		return;
	} // function
?>