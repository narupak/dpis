<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" && trim($NEW_KPI_NAME) && $NEW_KPI_PER_ID && $NEW_PFR_ID){
		$KPI_ID_REF = $KPI_ID;
		if(!$KPI_ID) $KPI_ID_REF = "NULL";
				
		if(!$NEW_KPI_WEIGHT) $NEW_KPI_WEIGHT = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL1) $NEW_KPI_TARGET_LEVEL1 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL2) $NEW_KPI_TARGET_LEVEL2 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL3) $NEW_KPI_TARGET_LEVEL3 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL4) $NEW_KPI_TARGET_LEVEL4 = "NULL";
		if(!$NEW_KPI_TARGET_LEVEL5) $NEW_KPI_TARGET_LEVEL5 = "NULL";
		if(!$NEW_KPI_EVALUATE) $NEW_KPI_EVALUATE = "NULL";
		
		$cmd = " select max(KPI_ID) as max_id from PER_KPI ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KPI_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_KPI
							(KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID,
							 KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, KPI_EVALUATE,
							 KPI_ID_REF, UPDATE_USER, UPDATE_DATE)
						values
							($KPI_ID, '$NEW_KPI_NAME', $NEW_KPI_WEIGHT, '$NEW_KPI_MEASURE', $NEW_KPI_PER_ID, $NEW_PFR_ID,
							 $NEW_KPI_TARGET_LEVEL1, $NEW_KPI_TARGET_LEVEL2, $NEW_KPI_TARGET_LEVEL3, $NEW_KPI_TARGET_LEVEL4, $NEW_KPI_TARGET_LEVEL5, $NEW_KPI_EVALUATE,
							 $KPI_ID_REF, $SESS_USERID, '$UPDATE_DATE')
					  ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มตัวชี้วัดย่อย [$KPI_REF_ID : $KPI_ID : $KPI_NAME]");

		$KPI_ID_REF += 0;
	} // end if
	
	if($command=="UPDATE" && $KPI_ID && trim($KPI_NAME) && $KPI_PER_ID && $PFR_ID){
		if(!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
		if(!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
		if(!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
		if(!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
		if(!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
		if(!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
		if(!$KPI_EVALUATE) $KPI_EVALUATE = "NULL";

		$cmd = " update PER_KPI set
							KPI_NAME='$KPI_NAME', 
							KPI_WEIGHT=$KPI_WEIGHT,
							KPI_MEASURE='$KPI_MEASURE',
							KPI_PER_ID=$KPI_PER_ID,
							PFR_ID=$PFR_ID,
							KPI_TARGET_LEVEL1=$KPI_TARGET_LEVEL1,
							KPI_TARGET_LEVEL2=$KPI_TARGET_LEVEL2,
							KPI_TARGET_LEVEL3=$KPI_TARGET_LEVEL3,
							KPI_TARGET_LEVEL4=$KPI_TARGET_LEVEL4,
							KPI_TARGET_LEVEL5=$KPI_TARGET_LEVEL5,
							KPI_EVALUATE=$KPI_EVALUATE,
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
						 where KPI_ID=$KPI_ID
					  ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงตัวชี้วัด [$KPI_REF_ID : $KPI_ID : $KPI_NAME]");
	} // end if

	if($command=="CHANGEKPIPARENT" && isset($NEW_KPI_ID_REF)){
		if(!$NEW_KPI_ID_REF) $NEW_KPI_ID_REF = "NULL";

		$cmd = " update PER_KPI set KPI_ID_REF=$NEW_KPI_ID_REF where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เปลี่ยนระดับตัวชี้วัด [$KPI_ID : $KPI_NAME | $KPI_ID_REF => $NEW_KPI_ID_REF]");	

		$KPI_ID_REF = $NEW_KPI_ID_REF;
		$KPI_ID_REF += 0;
	} // end if

	if($command=="DELETE" && $KPI_ID){
		delete_kpi($KPI_ID, $KPI_ID_REF);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบตัวชี้วัด [$KPI_REF_ID : $KPI_ID : $KPI_NAME]");

		$KPI_ID = $KPI_ID_REF + 0;
		unset($KPI_ID_REF);
	} // end if

	$cmd = " select KPI_ID from PER_KPI ";
	$HAVE_KPI = $db_dpis->send_cmd($cmd);

	if($KPI_ID)	{
		if($DPISDB=="odbc"){
			$cmd = " select top 1 KPI_ID_REF from PER_KPI where KPI_ID=$KPI_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select KPI_ID_REF from PER_KPI where KPI_ID=$KPI_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select KPI_ID_REF from PER_KPI where KPI_ID=$KPI_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$KPI_ID_REF = $data[KPI_ID_REF];
	} // end if

	$cmd = " select		KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID, 
									KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, KPI_EVALUATE
					 from		PER_KPI
					 where	KPI_ID=$KPI_ID
				  ";
	$db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
//	echo "Data :: <pre>"; print_r($data); echo "</pre>";
	$KPI_NAME = $data[KPI_NAME];
	$KPI_WEIGHT = $data[KPI_WEIGHT];
	$KPI_MEASURE = $data[KPI_MEASURE];
	$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
	$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
	$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
	$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
	$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
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

	function list_tree_kpi ($pre_image, $kpi_parent, $sel_kpi_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI;
		
		$opened_kpi = substr($LIST_OPENED_KPI, 1, -1);
		$arr_opened_kpi = explode(",", $opened_kpi);
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select 	KPI_ID , KPI_NAME, KPI_ID_REF, 
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5, KPI_EVALUATE 
						 from 		PER_KPI 
						 where 	".(trim($kpi_parent)?"KPI_ID_REF = $kpi_parent":"(KPI_ID_REF = 0 or KPI_ID_REF is null)")." 
						 order by KPI_NAME ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"black_normal\">";		
			while($data = $db_dpis->get_array()){
				$cmd = " select KPI_ID from PER_KPI where KPI_ID_REF=". $data[KPI_ID];
				$count_sub_tree = $db_dpis2->send_cmd($cmd);

				$class = "table_body";
				if($data[KPI_ID] == $sel_kpi_id) $class = "table_body_over";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_kpi(". $data[KPI_ID] .");";
				if(in_array($data[KPI_ID], $arr_opened_kpi)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_kpi(". $data[KPI_ID] .");";
				} // end if
				if(!$count_sub_tree) $icon_name = "";

				$KPI_TARGET_LEVEL1 = $data[KPI_TARGET_LEVEL1];
				$KPI_TARGET_LEVEL2 = $data[KPI_TARGET_LEVEL2];
				$KPI_TARGET_LEVEL3 = $data[KPI_TARGET_LEVEL3];
				$KPI_TARGET_LEVEL4 = $data[KPI_TARGET_LEVEL4];
				$KPI_TARGET_LEVEL5 = $data[KPI_TARGET_LEVEL5];
				$KPI_EVALUATE = $data[KPI_EVALUATE];
				
				if($KPI_EVALUATE >= 0 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL1) $KPI_IMG = "images/ball_red.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL1 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL2) $KPI_IMG = "images/ball_orange.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL2 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL3) $KPI_IMG = "images/ball_yellow.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL3 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL4) $KPI_IMG = "images/ball_green_light.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL4 && $KPI_EVALUATE <= $KPI_TARGET_LEVEL5) $KPI_IMG = "images/ball_green.gif";
				elseif($KPI_EVALUATE > $KPI_TARGET_LEVEL5) $KPI_IMG = "images/ball_green.gif";

				echo "<tr>";
				echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<img src=\"$KPI_IMG\" width=\"10\" height=\"10\" hspace=\"4\"><span onClick=\"select_kpi(". $data[KPI_ID] .",". ($data[KPI_ID_REF] + 0) .");\" style=\"cursor:hand\">" . $data[KPI_NAME] . "</span></td>";
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
			echo "</table>";
		} // end if
	} // function

	function delete_kpi($KPI_ID, $KPI_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select KPI_ID, KPI_ID_REF from PER_KPI where KPI_ID_REF=$KPI_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_kpi($data[KPI_ID], $data[KPI_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_KPI where KPI_ID=$KPI_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_KPI = str_replace(",$KPI_ID,", ",", $LIST_OPENED_KPI);
		
		return;
	} // function
?>