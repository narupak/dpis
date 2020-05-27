<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($_POST[KPI_YEAR])		$KPI_YEAR = $_POST[KPI_YEAR];
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID=" DEPARTMENT_ID=".$DEPARTMENT_ID;
	if($command=="UPDATE_SCORE"){	//สำหรับอัพเดท คะแนน KPI_KPI
		foreach($KPI_KPI_SCORE as $KPI_ID=>$KPI_SCORE){		// KPI_ID = รหัส , KPI_SCORE = คะแนน**
			if($KPI_ID!="" && $KPI_SCORE!=""){	
				$cmd = " update PER_KPI set KPI_SCORE=$KPI_SCORE where KPI_ID=$KPI_ID ";
				$db_dpis->send_cmd($cmd);
				// $db_dpis->show_error();
			}
		}
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงคะแนนการประเมินผลระดับหน่วยงาน $DEPARTMENT_NAME");
	}

	$cmd = " select distinct KPI_YEAR from PER_KPI where (".$where_DEPARTMENT_ID.") order by KPI_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[KPI_YEAR];
		$arr_kpi_year[] = $data[KPI_YEAR];
	} // end while
	
	if(!$KPI_YEAR || !in_array($KPI_YEAR, $arr_kpi_year)) $KPI_YEAR = $START_YEAR;

	function list_tree_kpi ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $KPI_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID, $EDIT_TITLE;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		if ($BKK_FLAG==1) $order_str = "KPI_TYPE, KPI_NAME";
		else $order_str = "KPI_NAME";

		$cmd = " select 	KPI_ID , KPI_NAME, KPI_ID_REF, KPI_SCORE
				   from 		PER_KPI 
				   where 	".(trim($kpi_parent)?"KPI_ID_REF = $kpi_parent":"(KPI_ID_REF = 0 or KPI_ID_REF is null)")." 
				   			and KPI_YEAR='$KPI_YEAR' and ($where_DEPARTMENT_ID) and KPI_SCORE_FLAG = 1
				   order by 	$order_str ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo $cmd;
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select KPI_ID from PER_KPI where KPI_ID_REF=". $data[KPI_ID] ." and KPI_SCORE_FLAG = 1 ";
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[KPI_ID] == $sel_kpi_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_kpi(". $data[KPI_ID] .");";
				if(in_array($data[KPI_ID], $arr_opened_kpi)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_kpi(". $data[KPI_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "ball.gif";

				echo "<tr>"; 
				echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span style=\"cursor:hand\">" . $data[KPI_NAME] . "</span></td><td class=\"$class\"  width=\"100\" height=\"22\" valign=\"top\"><input name=\"KPI_KPI_SCORE[".$data[KPI_ID]."]\" type=\"text\" value=\"".$data[KPI_SCORE]."\" style=\"width:98%\" class=\"textbox\" onKeyPress=\"return NumOnly();\"></td>";
				echo "</tr>";
				if($count_sub_tree && in_array($data[KPI_ID], $arr_opened_kpi)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[KPI_ID], $arr_opened_kpi)) $display = "block";
					echo "<div id=\"DIV_". $data[KPI_ID] ."\" style=\"display:$display\">";
					list_tree_kpi("", $data[KPI_ID], $sel_kpi_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end while						
			echo "</table><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\"><input name=\"Submit22\" type=\"submit\" class=\"button\" onClick=\"form1.command.value='UPDATE_SCORE';\"  value=\"".$EDIT_TITLE."คะแนน\"></td></tr></table>";
		} // end if
	} // function

?>