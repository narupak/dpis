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
		foreach($KPI_ORG_SCORE as $KPI_ID=>$KPI_SCORE){		// KPI_ID = รหัส , KPI_SCORE = คะแนน**
			if($KPI_ID!="" && $KPI_SCORE!=""){	
				$cmd = " update PER_KPI 
								set KPI_SCORE=$KPI_SCORE 
								where KPI_ID=$KPI_ID ";
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

	function list_tree_org ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $KPI_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID, $EDIT_TITLE,$DEPARTMENT_TITLE;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select 	ORG_ID, ORG_NAME
				   from 		PER_ORG 
				   where 	ORG_SCORE_FLAG > 0
				   order by 	ORG_SCORE_FLAG ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo $cmd;
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
            echo "<tr><td colspan='2'>".$DEPARTMENT_TITLE."ที่รับการประเมิน</td></tr>";
			while($data = $db_dpis->get_array()){
				$arrKPI_ORG_NAME[$data[ORG_ID]] = $data[ORG_NAME];
				
				$cmd2 = " select ORG_ID, KPI_ID, KPI_YEAR, KPI_NAME, KPI_MITI, KPI_SCORE 
									from PER_KPI 
									where KPI_YEAR = '$KPI_YEAR' and KPI_ASSESSOR_ID=$DEPARTMENT_ID and (DEPARTMENT_ID=$data[ORG_ID] or ORG_ID=$data[ORG_ID]) 
									order by KPI_SCORE_FLAG, KPI_NAME, KPI_MITI ";
				$count_sub_tree = $db_dpis2->send_cmd($cmd2);
				$data2 = $db_dpis2->get_array();
				$no_sub_miti[$data[ORG_ID]] = 0;
				if(!$count_sub_tree){ $icon_name[$data[ORG_ID]] = "ball.gif";		$no_sub_miti[$data[ORG_ID]] = 1;	}
				if($count_sub_tree && $cmd2){
					$class[$KPI_ORG_ID] = "table_body";
					$db_dpis3->send_cmd($cmd2);
					while($data3 = $db_dpis3->get_array()){
						if(!in_array($data3[KPI_MITI],$arrKPI_MITI[$data3[KPI_ID]])) {
							$arrtmp = explode(" ",$data3[KPI_NAME]);		$KPI_MITI = $arrtmp[0];
							$arrKPI_NAME[$data[ORG_ID]][$data3[KPI_ID]] = $data3[KPI_NAME];
							$arrKPI_MITI[$data[ORG_ID]][$data3[KPI_ID]] = ($KPI_MITI?$KPI_MITI:$data3[KPI_MITI]);	// $data3[KPI_MITI] = มันเอามาแค่ จุดเดียว  Ex.  2.2.1 (ขึ้นเป็น 2.2)
							$arrKPI_ORG_SCORE[$data[ORG_ID]][$data3[KPI_ID]] = $data3[KPI_SCORE];
						}
					} // end while
				} // end count
			} // end while	
			/*print("<pre>");
			print_r($arrKPI_ORG_NAME);					
			print("arrKPI_MITI");	print_r($arrKPI_MITI);	print("<hr>");
			print("arrKPI_NAME");	print_r($arrKPI_NAME);	print("<hr>");
			print("arrKPI_ORG_SCORE");	print_r($arrKPI_ORG_SCORE);	print("<hr>");
			print("</pre>"); */
			foreach($arrKPI_ORG_NAME as $key=>$value){
				$KPI_ORG_ID = $key;
				$KPI_ORG_NAME = $value;
				
					echo "<tr>"; 
					echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/".$icon_name[$KPI_ORG_ID]."\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
					echo "<td class=\"".$class[$KPI_ORG_ID]."\" height=\"22\">&nbsp;<span style=\"cursor:hand\"><strong>" .$KPI_ORG_NAME."</strong></span></td>";		//.$KPI_ORG_ID
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
					echo "<tr>";
					foreach($arrKPI_ORG_SCORE[$KPI_ORG_ID] as $key=>$value){
						$KPI_ID = $key;
						$KPI_ORG_SCORE = $value;
							echo "<td width=\"2\" align=\"center\" valign=\"top\">&nbsp;</td><td align=\"center\">&nbsp;<input name=\"KPI_ORG_SCORE[".$KPI_ID."]\" type=\"text\" value=\"".$KPI_ORG_SCORE."\" style=\"width:120%\" class=\"textbox\" onKeyPress=\"return NumOnly();\"></td>"; 
						} // end foreach
					echo "</tr>";
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
			echo "</table><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\"><input name=\"Submit22\" type=\"submit\" class=\"button\" onClick=\"form1.command.value='UPDATE_SCORE';\"  value=\"".$EDIT_TITLE."คะแนน\"></td></tr></table>";
		} // end if
	} // function

?>