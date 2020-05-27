<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=ORG_ID ";
	$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$START_ORG_ID = $data[ORG_ID];

	if($command=="UPDATE" && $ORG_ID_REF && $ORG_ID && trim($ORG_CODE)){
		$ORG_DATE =  save_date($ORG_DATE);

		$ORG_JOB = str_replace("'", "&prime;", trim($ORG_JOB));
		if(trim($ORG_SEQ_NO)=="") $ORG_SEQ_NO = "NULL";

		if(trim($OL_CODE)) $OL_CODE = "'$OL_CODE'";
		else $OL_CODE = "NULL";
		if(trim($OT_CODE)) $OT_CODE = "'$OT_CODE'";
		else $OT_CODE = "NULL";
		if(trim($OP_CODE)) $OP_CODE = "'$OP_CODE'";
		else $OP_CODE = "NULL";
		if(trim($OS_CODE)) $OS_CODE = "'$OS_CODE'";
		else $OS_CODE = "NULL";
		if(trim($AP_CODE)) $AP_CODE = "'$AP_CODE'";
		else $AP_CODE = "NULL";
		if(trim($PV_CODE)) $PV_CODE = "'$PV_CODE'";
		else $PV_CODE = "NULL";
		if(trim($CT_CODE)) $CT_CODE = "'$CT_CODE'";
		else $CT_CODE = "NULL";

		$cmd = " update PER_ORG set
							ORG_CODE='$ORG_CODE', ORG_NAME='$ORG_NAME', ORG_SHORT='$ORG_SHORT',
							OL_CODE=$OL_CODE, OP_CODE=$OP_CODE, OT_CODE=$OT_CODE, OS_CODE=$OS_CODE,
							ORG_ADDR1='$ORG_ADDR1', ORG_ADDR2='$ORG_ADDR2', ORG_ADDR3='$ORG_ADDR3', ORG_JOB='$ORG_JOB',
							CT_CODE=$CT_CODE, PV_CODE=$PV_CODE, AP_CODE=$AP_CODE, ORG_DATE='$ORG_DATE', ORG_ACTIVE='$ORG_ACTIVE',
							ORG_SEQ_NO=$ORG_SEQ_NO, ORG_WEBSITE='$ORG_WEBSITE',
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE', ORG_ENG_NAME='$ORG_ENG_NAME'
						 where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงสร้าง [$ORG_REF_ID : $ORG_ID : $ORG_NAME]");
	} // end if
	
	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
		} //if(!$ORG_ID && !$ORG_ID_REF){

	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = 1;
		$ORG_ID_REF = 1;
		} //if(!$ORG_ID && !$ORG_ID_REF){

	if($ORG_ID && !$ORG_ID_REF)	{
		if($DPISDB=="odbc"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			}elseif($DPISDB=="oci8"){//if($DPISDB=="odbc"){
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			}elseif($DPISDB=="mysql"){//}elseif($DPISDB=="oci8"){
			$cmd = " select top 1 ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
			}//}elseif($DPISDB=="mysql"){
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID_REF = $data[ORG_ID_REF];
		} //if($ORG_ID && !$ORG_ID_REF)	{

		$cmd = "	select	PER_ID, PER_TYPE, PER_NAME, PER_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, DEPARTMENT_ID
						from		PER_PERSONAL 
						where	PER_ID=$PER_ID	";
		$db_dpis->send_cmd($cmd);
//		echo($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]); 
		$POEMS_ID = trim($data[POEMS_ID]);

		$MAIN_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$MAIN_DEPARTMENT_ID ";
//		echo($cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MAIN_DEPARTMENT_NAME = $data2[ORG_NAME];
		$MAIN_MINISTRY_ID = $data2[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MAIN_MINISTRY_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MAIN_MINISTRY_NAME = $data2[ORG_NAME];

		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);		
		$ORG_ID = trim($data[ORG_ID]);
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		if($POS_ID){
			$cmd = " select 	ORG_ID, POS_NO, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE, 
							CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, 
							POS_STATUS 
					from 	PER_POSITION where POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_NO = trim($data2[POS_NO]);
			}//if($POS_ID){
		if ($POEM_ID) {
			$cmd = " select 	ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, PN_CODE, 
							POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS  
					from 	PER_POS_EMP where POEM_ID=$POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POEM_NO = trim($data2[POEM_NO]);
			$POS_ID = $POEM_ID;
			$POS_NO = trim($data2[POEM_NO]);
			}//if ($POEM_ID) {
		if ($POEMS_ID) {
			$cmd = " select 	ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, 
							POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS  
					from 	PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POEMS_NO = trim($data2[POEMS_NO]);
			$POS_ID = $POEMS_ID;			
			$POS_NO = trim($data2[POEMS_NO]);
			}//if ($POEMS_ID) {
//		echo($cmd);
		$ORG_ID = trim($data2[ORG_ID]);
		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
//		echo($cmd);
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);

		$ORG_ID_1 = trim($data2[ORG_ID_1]);
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
//		echo($cmd);
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME_1 = trim($data_dpis1[ORG_NAME]);

		$ORG_ID_2 = trim($data2[ORG_ID_2]); 
		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME_2 = trim($data_dpis1[ORG_NAME]);
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
//		echo($cmd);
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$DEPARTMENT_NAME = trim($data_dpis1[ORG_NAME]);
		$MINISTRY_ID = $data_dpis1[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
//		echo($cmd);
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MINISTRY_NAME = trim($data_dpis1[ORG_NAME]);


	$NEW_CT_CODE = "140";
	$NEW_CT_NAME = "ไทย";
	

	function list_tree_org ($pre_image, $org_parent, $sel_org_id, $sel_per_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG,$LIST_OPENED_ORG_PER,$LIST_OPENED_PER, $START_ORG_ID;
		
		$opened_org = substr($LIST_OPENED_ORG, 1, -1);//delete front comma
		$opened_org_per = substr($LIST_OPENED_ORG_PER, 1, -1);//delete front comma
		$opened_per = substr($LIST_OPENED_PER, 1, -1);//delete front comma
		$arr_opened_org = explode(",", $opened_org);//explode to array
		$arr_opened_org_per = explode(",", $opened_org_per);//explode to array
		$arr_opened_per = explode(",", $opened_per);//explode to array
//		print_r($arr_opened_org_per);
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_NAME,OL_CODE , ORG_ID_REF from PER_ORG where ORG_ID_REF = $org_parent and ORG_ID <> $START_ORG_ID order by ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
				if($data[OL_CODE]==3){
					$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									where c.ORG_ID=". $data[ORG_ID]." or d.ORG_ID=". $data[ORG_ID]." or e.ORG_ID=". $data[ORG_ID]."";
					}else  if ($data[OL_CODE]==4) {
					$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									where c.ORG_ID_1=". $data[ORG_ID]." or d.ORG_ID_1=". $data[ORG_ID]." or e.ORG_ID_1=". $data[ORG_ID]."";
					}else if ($data[OL_CODE]==5) {
					$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									where c.ORG_ID_2=". $data[ORG_ID]." or d.ORG_ID_2=". $data[ORG_ID]." or e.ORG_ID_2=". $data[ORG_ID]."";
					}
//				$cmd = " select ppe.PER_NAME,ppe.ORG_ID from PER_PERSONAL ppe  where ORG_ID=". $data[ORG_ID];
//				echo($cmd_per);
				$count_sub_tree_per = $db_dpis2->send_cmd($cmd_per);
//				echo("count_sub_tree_per - ".$count_sub_tree_per);
				$data2 = $db_dpis2->get_array();
				$orgid_opened_org_per = $data[ORG_ID];
//				echo($orgid_opened_org_per);
				$cmd = " select po.ORG_ID,po.OL_CODE from PER_ORG po  where ORG_ID_REF=". $data[ORG_ID];
//				echo($cmd);
				$count_sub_tree = $db_dpis2->send_cmd($cmd);
				$class = "table_body";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_org(". $data[ORG_ID] .");";
				if(in_array($data[ORG_ID], $arr_opened_org)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_org(". $data[ORG_ID] .");";
					} //if(in_array($data[ORG_ID], $arr_opened_org)){ 
				if(!$count_sub_tree&&$count_sub_tree_per){
					$icon_name = "icon_plus.png";
					$onClick = "add_opened_org_per(". $data[ORG_ID] .");";
					}
				if(!$count_sub_tree&&in_array($orgid_opened_org_per, $arr_opened_org_per)){ 
					$icon_name = "icon_minus.png";
					$onClick = "remove_closed_org_per(". $data[ORG_ID] .");";
					} //if(in_array($data[ORG_ID], $arr_opened_org)){ 
//				print_r($arr_opened_org_per);
				if(!$count_sub_tree&&!$count_sub_tree_per) $icon_name = "";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" ><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;" .$data[ORG_NAME] ."";
				if($count_sub_tree_per) echo " ($count_sub_tree_per)";
				echo "</td>";
				echo "</tr>";
				if($count_sub_tree && in_array($data[ORG_ID], $arr_opened_org)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[ORG_ID], $arr_opened_org)) $display = "block";
					echo "<div id=\"DIV_". $data[ORG_ID] ."\" style=\"display:$display\">";
					list_tree_org("", $data[ORG_ID], $sel_org_id, $sel_per_id, ($tree_depth + 1));
					list_tree_org_per("", $data[ORG_ID], $sel_org_id, $data[OL_CODE],$sel_per_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
					} //if($count_sub_tree && in_array($data[ORG_ID], $arr_opened_org)){
				if($count_sub_tree_per&&in_array($orgid_opened_org_per, $arr_opened_org_per)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($data[ORG_ID], $arr_opened_org_per)) $display = "block";
					echo "<div id=\"DIV_". $data[ORG_ID] ."\" style=\"display:$display\">";
					list_tree_org_per("", $data[ORG_ID], $sel_org_id, $data[OL_CODE], $sel_per_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
					} //if($count_sub_tree && in_array($data[ORG_ID], $arr_opened_org)){
				} //while($data = $db_dpis->get_array()){
			echo "</table>";
		} // end if
	} //function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {

	function delete_org($ORG_ID, $ORG_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG, $START_ORG_ID;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_ID_REF from PER_ORG where ORG_ID_REF=$ORG_ID and ORG_ID<>$START_ORG_ID ";
		$count_child = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		if($count_child){
			while($data = $db_dpis->get_array()){
				delete_org($data[ORG_ID], $data[ORG_ID_REF]);
			} // end while
		} // end if

		$cmd = " delete from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$LIST_OPENED_ORG = str_replace(",$ORG_ID,", ",", $LIST_OPENED_ORG);
		
		return;
	} // function
	
	function list_tree_org_per ($pre_image, $org_parent, $sel_org_id, $org_parent_ol_code, $sel_per_id, $tree_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PER, $START_ORG_ID;
//		echo("$org_parent_ol_code<br>");
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		if($org_parent_ol_code==3){
			$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							where (c.ORG_ID=". $org_parent." or d.ORG_ID=". $org_parent." or e.ORG_ID=". $org_parent.")
							and (c.ORG_ID_1 IS NULL and d.ORG_ID_1 IS NULL and e.ORG_ID_1 IS NULL and c.ORG_ID_2 IS NULL and d.ORG_ID_2 IS NULL and e.ORG_ID_2 IS NULL)
							";
			}else  if ($org_parent_ol_code==4) {
			$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							where (c.ORG_ID_1=". $org_parent." or d.ORG_ID_1=". $org_parent." or e.ORG_ID_1=". $org_parent.")
							and (c.ORG_ID_2 IS NULL and d.ORG_ID_2 IS NULL and e.ORG_ID_2 IS NULL)
							";
			}else if ($org_parent_ol_code==5) {
			$cmd_per = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							where c.ORG_ID_2=". $org_parent." or d.ORG_ID_2=". $org_parent." or e.ORG_ID_2=". $org_parent."";
			}
//			$cmd = " select PER_ID , PER_NAME, PER_SURNAME from PER_PERSONAL ppe where ppe.ORG_ID = $org_parent and ppe.ORG_ID <> $START_ORG_ID order by ppe.PER_NAME, ppe.PER_SURNAME ";
/*		$cmd = " select PER_ID , PER_NAME, PER_SURNAME from 	((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
		where c.ORG_ID=$org_parent or d.ORG_ID=$org_parent or e.ORG_ID=$org_parent";
*/		$count_data = $db_dpis->send_cmd($cmd_per);
//		echo "$cmd";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			//$class = "table_body";
			while($data = $db_dpis->get_array()){
			//echo($data[PER_ID]==$sel_per_id);
			$class = "";
			if($data[PER_ID] == $sel_per_id) $class = "table_body_over";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
				echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_per(". $data[PER_ID] .");\" style=\"cursor:hand\"><font color=\"#000000\" >" . $data[PER_NAME]." ". $data[PER_SURNAME] . "</font></span></td>";
				echo "</tr>";
				}//while($data = $db_dpis->get_array()){
			echo "</table>";
		} //if($count_data){
	} //function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {
?>