<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($_POST[KPI_YEAR])		$KPI_YEAR = $_POST[KPI_YEAR];					// KPI_DEFINE
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID=" DEPARTMENT_ID=".$DEPARTMENT_ID;
	
	echo "$KPI_ID ++ $KPI_YEAR / $KPI_ORG_ID ~ $KPI_ORG_NAME --- $KPI_DEFINE";	
	if($KPI_YEAR && $KPI_ORG_ID && $KPI_DEFINE){		//if($KPI_YEAR && $KPI_ORG_ID && $KPI_DEFINE){
		// หา KPI_ID ของ ORG_ID นี้
		$cmd = " select 	KPI_ID, KPI_NAME
				   from 		PER_KPI
				   where (KPI_YEAR=$KPI_YEAR) and (ORG_ID=$KPI_ORG_ID) 
				   order by 	ORG_SCORE_FLAG ";
		$count_data = $db_dpis->send_cmd($cmd);
		if(!$count_data){
			
		}else{
			$data = $db_dpis->get_array();
			$KPI_ID = $data[KPI_ID];
			$KPI_ORG_NAME = $data[KPI_NAME];
		}
	
		//if($KPI_ID){KPI_ID=$KPI_ID
			$cmd = " update PER_KPI 
			set KPI_DEFINE = '$KPI_DEFINE'
			where (KPI_YEAR = $KPI_YEAR and ORG_ID = $KPI_ORG_ID)
			";
//$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
			echo "-> $cmd";
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > การกระจายตัวชี้วัด $KPI_ID ปีงบประมาณ $KPI_YEAR ลงสู่ส่วนราชการ $KPI_ORG_NAME : $KPI_DEFINE");
		//}
	}

	$cmd = " select distinct KPI_YEAR from PER_KPI where (".$where_DEPARTMENT_ID.") order by KPI_YEAR desc ";
	$HAVE_YEAR = $db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		if(!$START_YEAR) $START_YEAR = $data[KPI_YEAR];
		$arr_kpi_year[] = $data[KPI_YEAR];
	} // end while
	
	if(!$KPI_YEAR || !in_array($KPI_YEAR, $arr_kpi_year)) $KPI_YEAR = $START_YEAR;

	function list_tree_org ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $KPI_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID, $EDIT_TITLE,$DEPARTMENT_TITLE;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//				   and			ORG_SCORE_FLAG > 0		
		$cmd = " select 	ORG_ID, ORG_NAME
				   from 		PER_ORG 
				   where 		(ORG_ID_REF=$DEPARTMENT_ID and OL_CODE=03)
				   order by 	ORG_SCORE_FLAG ";
		$count_data = $db_dpis->send_cmd($cmd);
//echo $cmd;
//$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
            echo "<tr><td colspan='2'>".$DEPARTMENT_TITLE."ที่รับการประเมิน</td></tr>";
			while($data = $db_dpis->get_array()){
				$arrKPI_ORG_NAME[$data[ORG_ID]] = $data[ORG_NAME];
			} // end while	

			foreach($arrKPI_ORG_NAME as $key=>$value){
				$KPI_ORG_ID = $key;
				$KPI_ORG_NAME = $value;
				
					echo "<tr>"; 
					echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/".$icon_name[$KPI_ORG_ID]."\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
					echo "<td class=\"".$class[$KPI_ORG_ID]."\" height=\"22\">&nbsp;<span style=\"cursor:hand\"><strong>" .$KPI_ORG_NAME."</strong></span><br><input name='KPI_R' type='button' value='R' title='$KPI_ORG_NAME (R)' onClick=\"form1.KPI_ORG_ID.value='$KPI_ORG_ID';form1.KPI_ORG_NAME.value='$KPI_ORG_NAME';form1.KPI_DEFINE.value=this.value;form1.submit();\">&nbsp;<input name='KPI_SR' type='button' value='SR' title='$KPI_ORG_NAME (SR)' onClick=\"if(form1.KPI_SR_TEXT_$KPI_ORG_ID.value==''){  alert('กรุณาระบุสำหรับ SR ($KPI_ORG_NAME) เมื่อระบุแล้วกดปุ่ม SR อีกครั้ง'); form1.KPI_SR_TEXT_$KPI_ORG_ID.focus(); }else{ form1.KPI_ORG_ID.value='$KPI_ORG_ID';form1.KPI_ORG_NAME.value='$KPI_ORG_NAME';form1.KPI_DEFINE.value=form1.KPI_SR_TEXT_$KPI_ORG_ID.value;form1.submit(); }\">&nbsp;<input name='KPI_JR' type='button' value='JR' title='$KPI_ORG_NAME (JR)' onClick=\"form1.KPI_ORG_ID.value='$KPI_ORG_ID';form1.KPI_ORG_NAME.value='$KPI_ORG_NAME';form1.KPI_DEFINE.value=this.value;form1.submit();\"><br><input name='KPI_SR_TEXT_$KPI_ORG_ID' type='text' value='' title='ระบุสำหรับ SR ($KPI_ORG_NAME)'></td>";	
					echo "</tr>";
			
			if($no_sub_miti[$KPI_ORG_ID]==0){					
					echo "<tr><td width=\"15\" align=\"center\" valign=\"top\">&nbsp;</td>"; 	
					echo "<td><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";	
					echo "<tr>";
					foreach($arrKPI_MITI[$KPI_ORG_ID] as $key=>$value){
						$KPI_ID = $key;
						$KPI_MITI = $value;
							echo "<td width=\"2\" align=\"center\" valign=\"top\">&nbsp;</td><td align=\"center\" style=\"color:#0000FF;background-color:#FF9966\">&nbsp;<span style=\"cursor:hand\"><strong title=\"".$arrKPI_NAME[$KPI_ORG_ID][$KPI_ID]."\">".$KPI_MITI."</strong></span></td>"; 				
						} // end foreach
					echo "</tr>";
					/*echo "<tr>";
					foreach($arrKPI_ORG_SCORE[$KPI_ORG_ID] as $key=>$value){
						$KPI_ID = $key;
						$KPI_ORG_SCORE = $value;
							echo "<td width=\"2\" align=\"center\" valign=\"top\">&nbsp;</td><td align=\"center\">&nbsp;<input name=\"KPI_ORG_SCORE[".$KPI_ID."]\" type=\"text\" value=\"".$KPI_ORG_SCORE."\" style=\"width:120%\" class=\"textbox\" onKeyPress=\"NumOnly();\"></td>"; 
						} // end foreach
					echo "</tr>"; */
			echo "</table></td></tr>";
			
				if($count_sub_tree && in_array($KPI_ID, $arr_opened_kpi)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($KPI_ID, $arr_opened_kpi)) $display = "block";
					echo "<div id=\"DIV_". $KPI_ID ."\" style=\"display:$display\">";
//					list_tree_org("", $KPI_ID, $sel_kpi_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				} // end if
			} // end if($no_sub_miti[$KPI_ORG_ID]==0)			
			} // end foreach ทุก ORG ___1
			/*echo "</table><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\"><input name=\"Submit22\" type=\"submit\" class=\"button\" onClick=\"form1.command.value='UPDATE_SCORE';\"  value=\"".$EDIT_TITLE."คะแนน\"></td></tr></table>";  */
		} // end if
	} // function
	
/*
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
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span style=\"cursor:hand\">" . $data[KPI_NAME] . "</span></td><td class=\"$class\"  width=\"100\" height=\"22\" valign=\"top\"><input name=\"KPI_KPI_SCORE[".$data[KPI_ID]."]\" type=\"text\" value=\"".$data[KPI_SCORE]."\" style=\"width:98%\" class=\"textbox\" onKeyPress=\"NumOnly();\"></td>";
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
*/
?>