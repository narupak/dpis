<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$per_list = array();
	$arr_struct_pic =  array();
	$cntnext_per=0;
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	if (!$LIST_OPENED_ORG) {
		$LIST_OPENED_ORG = ",$SESS_MINISTRY_ID,$SESS_DEPARTMENT_ID,";
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_ORG_ID ? "$SESS_ORG_ID," : "");
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_PROVINCE_CODE ? "$SESS_PROVINCE_CODE," : "");
	}

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "UPDATE" && $PERH_ID && $PERD_LIST) {
		$arr_perd_id = explode(",",$PERD_LIST);
		for($i=0; $i < count($arr_perd_id); $i++) {
			$cmd_upd = "  update PER_PERSONAL set PER_ID_ASS_REF='$PERH_ID', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
									where PER_ID=".$arr_perd_id[$i]." ";
			$db_dpis->send_cmd($cmd_upd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับปรุงโครงสร้างการบังคับบัญชาตามมอบหมายงาน [$PERH_ID : ".$arr_perd_id[$i]."]");
		} // end for loop
	}

	if ($ORG_ID){
		$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ORG_ID and ORG_ACTIVE = 1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SHOW_ORGNAME = $data[ORG_NAME];
	}

	$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=ORG_ID and ORG_ACTIVE = 1 ";
	$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$START_ORG_ID = $data[ORG_ID];

	$NEW_CT_CODE = "140";
	$NEW_CT_NAME = "ไทย";	
	function list_tree_org ($pre_image, $org_parent, $sel_org_id, $sel_per_id, $tree_depth) {	//แสดงหน่วยงาน 
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG,$LIST_OPENED_ORG_PER,$LIST_OPENED_PER, $START_ORG_ID,$RPT_N,$TOP_PER_ID,$SELECTED_PER_ID;
		global $SESS_DEPARTMENT_ID,$SESS_MINISTRY_ID,$BKK_FLAG;	//SESSION LOGIN
		
		$opened_org = substr($LIST_OPENED_ORG, 1, -1);//delete front comma
		$opened_org_per = substr($LIST_OPENED_ORG_PER, 1, -1);//delete front comma
		$opened_per = substr($LIST_OPENED_PER, 1, -1);//delete front comma
		$arr_opened_org = explode(",", $opened_org);//explode to array
		$arr_opened_org_per = explode(",", $opened_org_per);//explode to array
		$arr_opened_per = explode(",", $opened_per);//explode to array
//		print_r($arr_opened_org_per);
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd_org = " select ORG_ID, ORG_NAME,OL_CODE , ORG_ID_REF,DEPARTMENT_ID 
									from PER_ORG_ASS 
									where ORG_ID_REF = $org_parent and ORG_ID <> $START_ORG_ID and ORG_ACTIVE = 1 
									order by ORG_SEQ_NO, ORG_CODE ";
		$count_data = $db_dpis->send_cmd($cmd_org);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			while($data = $db_dpis->get_array()){
					$OL_CODE = $data[OL_CODE];
					$orgid_opened_org_per = $data[ORG_ID];	//ORG_ID= $START_ORG_ID
					$LIST_ORG_NAME = $data[ORG_NAME];

					$ORG_ID_REF = $data[ORG_ID_REF];	//DEPARTMENT_ID = $org_parent
					$DEPARTMENT_ID = $data[DEPARTMENT_ID];
				
				//รวมทั้งข้าราชการ ลูกจ้างฯ และพนักงานฯ
				$where = " and a.PER_STATUS = 1 ";
				if ($BKK_FLAG==1) $where .= " and a.PER_TYPE = 1";
				 if($OL_CODE==02){
					$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_ID_ASS_REF
											from 	((((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
											) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
											) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									where a.DEPARTMENT_ID=". $orgid_opened_org_per."
										and a.ORG_ID IS NULL and a.ORG_ID_1 IS NULL and a.ORG_ID_2 IS NULL $where
									order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
				}else if($OL_CODE==03){
					$cmd_per = " select a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_ID_ASS_REF
									from 	((((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.ORG_ID=". $orgid_opened_org_per." 
								and a.DEPARTMENT_ID IS NOT NULL	and a.ORG_ID_1 IS NULL and a.ORG_ID_2 IS NULL $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";				
					}else  if ($OL_CODE==04) {
					$cmd_per = " select a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_ID_ASS_REF 
											from 	((((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
											) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
											) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									where a.ORG_ID_1=". $orgid_opened_org_per." 
										and a.ORG_ID IS NOT NULL and a.ORG_ID_2 IS NULL $where 
									order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
					}else if ($OL_CODE==05) {
					$cmd_per = " select a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_ID_ASS_REF
											from 	((((	PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
											) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
											) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
											) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
											) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									where a.ORG_ID_2=". $orgid_opened_org_per." 
											and a.ORG_ID_1 IS NOT NULL $where 
									order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
					}
				$count_sub_tree_per = $db_dpis2->send_cmd($cmd_per);
				//echo "<br>$OL_CODE (".$count_sub_tree_per." ) : $cmd_per<br>";			
if($count_sub_tree_per){	
				$data1 = $db_dpis2->get_array();
				//หาหัวหน้าแต่ละหน่วยงาน เลือกเฉพาะคนแรกที่เรียงตามPER_TYPEน้อยสุด คือ1 (ข้าราชการเท่านั้น) และระดับตำแหน่งมากสุดขึ้นมา) =====
//				if (in_array(trim($data1[PER_ID]),$TOP_PER_ID)) {
//					$tper = trim($data1[PER_ID])."_$orgid_opened_org_per";
//				} else {
//					$tper = trim($data1[PER_ID]);
//				}
//				$TOP_PER_ID[$orgid_opened_org_per]=$tper;
				$TOP_PER_ID[$orgid_opened_org_per]=trim($data1[PER_ID]);
				$LEVEL_NO = trim($data1[LEVEL_NO]);
				$LEVEL_NAME = trim($data1[POSITION_LEVEL]);
				$PER_NAME = $data1[PER_NAME];
				$PER_SURNAME = $data1[PER_SURNAME];
				$FULLNAME = "$PER_NAME $PER_SURNAME";
				$PER_TYPE = $data1[PER_TYPE];
				$PER_STATUS = $data1[PER_STATUS];
				$PN_CODE = trim($data1[PN_CODE]);
				$POS_ID = $data1[POS_ID];		$POEM_ID = $data1[POEM_ID];	$POEMS_ID = $data1[POEMS_ID];
			
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}
			$PRENAME = ($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME;
			$FULLNAME = $PRENAME.$FULLNAME;

		$POSEM_NO = "";		$TMP_PL_NAME = "";		$ORG_NAME = "";		$ORG_NAME_REF = "";
		//หาชื่อหัวหน้าของหน่วยงานนั้นๆ (ต้องเป็น ข้าราชการ เท่านั้น)
		if (trim($PER_TYPE) == 1 && trim($POS_ID)) {
				$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, pt.PT_NAME, po.ORG_ID, po.ORG_ID_REF , pp.PM_CODE
									from		PER_POSITION pp, PER_LINE pl, PER_ORG po, PER_TYPE pt 
									where		pp.POS_ID=$POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE and pp.PT_CODE=pt.PT_CODE ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POSEM_NO = $data2[POS_NO_NAME].$data2[POS_NO];
				$PL_NAME = $data2[PL_NAME];
				$ORG_NAME = $data2[ORG_NAME];
				$ORG_ID = trim($data2[ORG_ID]);
				$ORG_ID_REF = trim($data2[ORG_ID_REF]);
				$PT_CODE = trim($data2[PT_CODE]);
				$PT_NAME = trim($data2[PT_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);

				$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
				if ($RPT_N)
				    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
				else
				    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
		} 
		//		echo $PER_TYPE." - $LIST_ORG_NAME($orgid_opened_org_per) - ".$TOP_PER_ID[$orgid_opened_org_per]."->[".$POS_ID."]-[".$POEM_ID."]-[".$POEMS_ID."] <$POSEM_NO> = ".$FULLNAME;
}	//end if($count_sub_tree_per) 

			//=========================================================================
				$cmd = " select po.ORG_ID,po.OL_CODE from PER_ORG_ASS po  where ORG_ID_REF= $orgid_opened_org_per and ORG_ACTIVE = 1 ";
//				echo($cmd);
				$count_sub_tree = $db_dpis2->send_cmd($cmd);
				$class = "table_body";
				$icon_name = "icon_plus.gif";
				$onClick = "add_opened_org(". $orgid_opened_org_per .");";
				if(in_array($orgid_opened_org_per, $arr_opened_org)){ 
					$icon_name = "icon_minus.gif";
					$onClick = "remove_closed_org(". $orgid_opened_org_per .");";
				} //if(in_array($orgid_opened_org_per, $arr_opened_org)){ 
				if(!$count_sub_tree&&$count_sub_tree_per){
					$icon_name = "icon_plus.png";
					$onClick = "add_opened_org_per(". $orgid_opened_org_per .");";
					$icon_name = "";
					$onClick = "";
				}
				if(!$count_sub_tree&&in_array($orgid_opened_org_per, $arr_opened_org_per)){ 
					$icon_name = "icon_minus.png";
					$onClick = "remove_closed_org_per(". $orgid_opened_org_per .");";
					$icon_name = "";
					$onClick = "";
				} //if(in_array($orgid_opened_org_per, $arr_opened_org)){ 
//				print_r($arr_opened_org_per);
				if(!$count_sub_tree&&!$count_sub_tree_per) $icon_name = "";
				echo "<tr>";
				echo "<td width=\"15\" align=\"center\" ><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\">				
</td>";
				if($count_sub_tree_per) 
					echo "<td class=\"$class\" height=\"22\">&nbsp;<span onClick=\"select_org_per($orgid_opened_org_per, $orgid_opened_org_per, ".$TOP_PER_ID[$orgid_opened_org_per].", $OL_CODE);\" style=\"cursor:hand\"><font style=\"text-decoration:underline;color:#000000\" >" .$LIST_ORG_NAME ."</font></span>"; // $orgid_opened_org_per 
				else
					echo "<td class=\"$class\" height=\"22\">&nbsp;" .$LIST_ORG_NAME .""; // $orgid_opened_org_per 
					if($orgid_opened_org_per==$SESS_MINISTRY_ID){	echo "&nbsp;<span class=\"label_alert\">**</span>";	}	
					if($count_sub_tree_per) echo "&nbsp;<font style=\"font-size:12px;color:#FF0000\">($count_sub_tree_per)</font>";
				echo "</td>";
				echo "</tr>";
				if($count_sub_tree && in_array($orgid_opened_org_per, $arr_opened_org)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($orgid_opened_org_per, $arr_opened_org)) $display = "block";
					echo "<div id=\"DIV_". $orgid_opened_org_per ."\" style=\"display:$display\">";
					list_tree_org("", $orgid_opened_org_per, $sel_org_id, $sel_per_id, ($tree_depth + 1));
//					list_tree_org_per("", $orgid_opened_org_per, $sel_org_id, $OL_CODE, $sel_per_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
					} //if($count_sub_tree && in_array($orgid_opened_org_per, $arr_opened_org)){
				if($count_sub_tree_per&&in_array($orgid_opened_org_per, $arr_opened_org_per)){
					echo "<tr>";
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "none";
					if(in_array($orgid_opened_org_per, $arr_opened_org_per)) $display = "block";
					echo "<div id=\"DIV_". $orgid_opened_org_per ."\" style=\"display:$display;\">";
					list_tree_org_per("", $orgid_opened_org_per, $sel_org_id, $OL_CODE, $sel_per_id, ($tree_depth + 1));
					echo "</div>";
					echo "</td>";
					echo "</tr>";
					} //if($count_sub_tree && in_array($orgid_opened_org_per, $arr_opened_org)){
				} //while($data = $db_dpis->get_array()){
			echo "</table>";
		} // end if
	} //function list_tree_org ($pre_image, $org_parent, $sel_org_id, $tree_depth) {

	function delete_org($ORG_ID, $ORG_ID_REF){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_ORG, $START_ORG_ID;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select ORG_ID, ORG_ID_REF from PER_ORG_ASS where ORG_ID_REF=$ORG_ID and ORG_ID<>$START_ORG_ID ";
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
	
	//แสดงรายชื่อทั้งหมด ข้าราชการ,ลูกจ้างฯ และ พนักงานฯ  ???????????
	function list_tree_org_per ($pre_image, $org_parent, $sel_org_id, $org_parent_ol_code, $sel_per_id, $tree_depth) {	
//		echo "** list_tree_org_per sel_per_id=$sel_per_id, org_ref=$org_parent, org_id=$sel_org_id, ol_code=$org_parent_ol_code**<br>";
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_PER, $START_ORG_ID,$RPT_N,$TOP_PER_ID,$SELECTED_PER_ID,$BKK_FLAG;
		global $per_list, $cntnext_per, $arr_struct_pic;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//(ORG_ID ระดับเหนือขึ้นไปต้องไม่เป็น NULL แต่ ORG_ID ระดับต่ำลงมาต้องไม่มี (NULL)
		$where = " and a.PER_STATUS = 1 ";
		if ($BKK_FLAG==1) $where .= " and a.PER_TYPE = 1";
		if($org_parent_ol_code==02){//	echo("กรม DEPARTMENT_ID"); 
			$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
									from 	((((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.DEPARTMENT_ID=". $org_parent." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
		}else if($org_parent_ol_code==03){
			$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
									from 	((((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.ORG_ID=". $org_parent." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
		}else  if ($org_parent_ol_code==04) {
			$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO 
									from 	((((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.ORG_ID_1=". $org_parent." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
		}else if ($org_parent_ol_code==05) {
			$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
									 from 	((((	PER_PERSONAL a
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
									) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
									) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
									) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.ORG_ID_2=". $org_parent." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";
		}
		$count_data = $db_dpis->send_cmd($cmd_per);
//		echo "$cmd_per<br>";
//		$db_dpis->show_error();
		if($count_data){
			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
			//$class = "table_body";
			echo "<tr>";
//			echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
			echo "<td class=\"$class\" style=\"white-space: nowrap\" height=\"22\">&nbsp;<font color=\"#000000\" >ชื่อ</font></td>";
			echo "<td>ผู้บังคับบัญชา</td>";
			echo "<td>ผู้ใต้บังคับบัญชา</td>";
			echo "</tr>";
			$i=0;
			while($data = $db_dpis->get_array()){
				$cntnext_per++;
				$i++;
				$PER_ID = trim($data[PER_ID]);
				if (!in_array($PER_ID, $per_list)) { // ถ้าไม่มี PER_ID นี้ ใน list ที่แสดงไปแล้วในโครงสร้างบุคคล ถึงแสดงได้
					$per_list[] = $PER_ID;
					$LEVEL_NO = trim($data[LEVEL_NO]);
					$LEVEL_NAME = trim($data[POSITION_LEVEL]);
					$PER_NAME = $data[PER_NAME];
					$PER_SURNAME = $data[PER_SURNAME];
					$FULLNAME = "$PER_NAME $PER_SURNAME";
					$PER_CARDNO = $data[PER_CARDNO];
					//-------------หารูปภาพที่ใช้แสดง
		//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
					$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
		//echo "IMG:$cmd<br>";
					$piccnt = $db_dpis2->send_cmd($cmd);
					if ($piccnt > 0) { 
						while ($data2 = $db_dpis2->get_array()) {
							$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
							$PER_GENNAME = trim($data2[PER_GENNAME]);
							$PIC_PATH = trim($data2[PER_PICPATH]);
							$PIC_SEQ = trim($data2[PER_PICSEQ]);
							$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
							$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
							$PIC_SHOW = trim($data2[PIC_SHOW]);

							if ($PIC_SHOW == '1') {
								$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_imgshow[] = 1;
								$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
							} else {
								$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
								$arr_imgshow[] = 0;
							}
						} // end while loop
					} else {
						//$img_file="";
						$img_file=$IMG_PATH."shadow.jpg";
					}

					if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
						$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
						if ($db_dpis2->send_cmd($cmd)) { 
							$data2 = $db_dpis2->get_array();
							$SERVER_NAME = trim($data2[SERVER_NAME]);
							$ftp_server = trim($data2[FTP_SERVER]);
							$ftp_username = trim($data2[FTP_USERNAME]);
							$ftp_password = trim($data2[FTP_PASSWORD]);
							$main_path = trim($data2[MAIN_PATH]);
							$http_server = trim($data2[HTTP_SERVER]);
							if ($http_server) {
								//echo "1.".$http_server."/".$img_file."<br>";
								$fp = @fopen($http_server."/".$img_file, "r");
								if ($fp !== false) $img_file = $http_server."/".$img_file;
								else $img_file=$IMG_PATH."shadow.jpg";
								fclose($fp);
							} else {
		//						echo "2.".$img_file."<br>";
								$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
							}
						} else{
							$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
						}
					} else{ 
						//$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
						$img_file = file_exists($img_file)?($img_file):$IMG_PATH."shadow.jpg";
		//				echo "../".$img_file." @@@@ ".file_exists("../".$img_file);
					}
		//echo "img_file=$img_file // $PIC_SERVER_ID<br>";

					$PN_CODE = trim($data[PN_CODE]);
					if ($PN_CODE) {
						$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PN_NAME = $data2[PN_NAME];
						$PN_SHORTNAME = $data2[PN_SHORTNAME];
					}
					$PRENAME = ($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME;
					$FULLNAME = $PRENAME.$FULLNAME;

					$PER_TYPE = $data[PER_TYPE];
					$PER_STATUS = $data[PER_STATUS];
					$POSEM_NO = "";
					$TMP_PL_NAME = "";
					$ORG_NAME = "";
					$ORG_NAME_REF = "";
					if ($PER_TYPE == 1) { // ข้าราชการ
						$POS_ID = $data[POS_ID];
						if ($POS_ID) {
							$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, pt.PT_NAME, po.ORG_ID, po.ORG_ID_REF , pp.PM_CODE
											from		PER_POSITION pp, PER_LINE pl, PER_ORG po, PER_TYPE pt 
											where		pp.POS_ID=$POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE and pp.PT_CODE=pt.PT_CODE ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$POSEM_NO = $data2[POS_NO_NAME].$data2[POS_NO];
							$PL_NAME = $data2[PL_NAME];
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_ID = trim($data2[ORG_ID]);
							$ORG_ID_REF = trim($data2[ORG_ID_REF]);
							$PT_CODE = trim($data2[PT_CODE]);
							$PT_NAME = trim($data2[PT_NAME]);
							$PM_CODE = trim($data2[PM_CODE]);

							$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PM_NAME = trim($data2[PM_NAME]);
							if ($RPT_N)
							    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
							else
							    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
						} // end if ($POS_ID) 
					} elseif ($PER_TYPE == 2) { // 
						$POEM_ID = $data[POEM_ID];
						if ($POEM_ID) {
							$cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
											from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
											where		pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
							$PL_NAME = trim($data2[PN_NAME]);
							$ORG_NAME = trim($data2[ORG_NAME]);
							$ORG_ID = trim($data2[ORG_ID]);
							$ORG_ID_REF = trim($data2[ORG_ID_REF]);
							$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
						}
					} elseif ($PER_TYPE == 3) {
						$POEMS_ID = $data[POEMS_ID];
						if ($POEMS_ID) {
							$cmd = " 	select		POEMS_NO_NAME, POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
											from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
											where		pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$POSEM_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
							$PL_NAME = trim($data2[EP_NAME]);
							$ORG_NAME = trim($data2[ORG_NAME]);
							$ORG_ID = trim($data2[ORG_ID]);
							$ORG_ID_REF = trim($data2[ORG_ID_REF]);
							$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
						}
					} elseif ($PER_TYPE == 4) { // 
						$POT_ID = $data[POT_ID];
						if ($POT_ID) {
							$cmd = " 	select		POT_NO_NAME, POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
											from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
											where		pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$POSEM_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
							$PL_NAME = trim($data2[TP_NAME]);
							$ORG_NAME = trim($data2[ORG_NAME]);
							$ORG_ID = trim($data2[ORG_ID]);
							$ORG_ID_REF = trim($data2[ORG_ID_REF]);
							$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
						}
					} // end if PER_TYPE
					$class = "";
					$deptstr="";
					if($PER_ID== $sel_per_id) $class = "table_body_over";
//					if($PER_ID != $TOP_PER_ID[$org_parent]){ //ไม่เอาชื่อหัวหน้ามาแสดงในรายชื่อ (sub ย่อย)
						echo "<tr>";
//						echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
//						echo "<td class=\"$class\" style=\"white-space: nowrap\" height=\"22\" width=\"80%\">&nbsp;$deptstr<font color=\"#000000\" >$cntnext_per - <span onClick=\"show_per_pic('$img_file');\" style=\"cursor:hand\">".$FULLNAME."</span></font><sub>".$PER_TYPE."</sub><font style=\"font-size:12px;color:#F26713\">".$TMP_PL_NAME."</font></td>";
						echo "<td class=\"$class\" style=\"white-space: nowrap\" height=\"22\" width=\"80%\">&nbsp;$deptstr<font color=\"#000000\" >$cntnext_per - <span onMouseOver=\"show_per_pic('$img_file');\" onMouseOut=\"removeElement();\" style=\"cursor:hand\">".$ORG_NAME. " ".$FULLNAME."</span></font><sub>".$PER_TYPE."</sub><font style=\"font-size:12px;color:#F26713\">".$TMP_PL_NAME."</font>&nbsp;&nbsp;&nbsp;<a href='javascript:call_chart(\"$PER_ID\",\"$ORG_ID\",\"$ORG_ID_REF\",\"$org_parent_ol_code\")'><img src=\"images/picture.gif\"  alt=\"พิมพ์ชาร์ด\" width=\"16\" height=\"16\" border=\"0\"></a></td>"; 
						echo "<td><input type=\"checkbox\" name=\"perH".$PER_ID."\" value=\"".$PER_ID."\" ></td>";
						echo "<td><input type=\"checkbox\" name=\"perD".$PER_ID."\" value=\"".$PER_ID."\" ></td>";
						echo "</tr>";
						echo "<tr>";
						// ถ้ามีผู้ใต้บังตับบัญชา
						$cmd_sub = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
													  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
												from   ((((PER_PERSONAL a
																left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
																) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
											where a.PER_ID_ASS_REF=". $PER_ID." $where
											order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
						$count_data2 = $db_dpis2->send_cmd($cmd_sub);
						if ($count_data2) {
							echo "<td width=\"15\" align=\"center\"></td>";
							echo "<td>";
							$display = "block";
							echo "<div id=\"DIV_". $PER_ID ."\" style=\"display:$display;margin-top:0;\">";
							$arr_struct_pic[0]="";
							$arr_struct_pic[1]="";
							list_tree_per($PER_ID, 2);
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
//					}	//end if($PER_ID != $TOP_PER_ID)
				} // end if (!in_array($PER_ID, $per_list))
			}//end while
			echo "</table>";
			$SELECTED_PER_ID = implode(",",$per_list);
		} //end if($count_data)
	} //end function
	
	//แสดงรายชื่อตามสายบังคับบัญชา
	function list_tree_per ($parent_per_id, $dept) {	
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $per_list, $cntnext_per, $arr_struct_pic,$RPT_N,$BKK_FLAG;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//หา PER_ID ตาม PER_ID_REF = $parent_per_id
		$where = " and a.PER_STATUS = 1 ";
		if ($BKK_FLAG==1) $where .= " and a.PER_TYPE = 1";
		$cmd_per = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
										  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
								from   ((((PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
												) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
												) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
												) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
												) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.PER_ID_ASS_REF=". $parent_per_id." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
		$count_data = $db_dpis->send_cmd($cmd_per);
//		echo "$cmd_per<br>";
//		$db_dpis->show_error();
		if($count_data){
			$i=0;
			while($data = $db_dpis->get_array()){
				$cntnext_per++;
				$i++;
				$PER_ID = trim($data[PER_ID]);
				$per_list[] = $PER_ID;
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = trim($data[POSITION_LEVEL]);
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];
				$FULLNAME = "$PER_NAME $PER_SURNAME";
				$PER_CARDNO = $data[PER_CARDNO];
					//-------------หารูปภาพที่ใช้แสดง
					$cmd = "	SELECT * FROM PER_PERSONALPIC
									WHERE	(PER_ID=$PER_ID and PIC_SHOW=1)
									ORDER BY	 PER_PICSEQ ";
					$db_dpis2->send_cmd($cmd);
					$data1 = $db_dpis2->get_array();
					$TMP_PIC_SEQ = $data1[PER_PICSEQ];
					$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
					$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
					if (trim($data1[PER_CARDNO]) && trim($data1[PER_CARDNO]) != "NULL")
						$TMP_PIC_NAME = $data1[PER_PICPATH].$data1[PER_CARDNO]."-".$T_PIC_SEQ.".jpg";
					else
						$TMP_PIC_NAME = $data1[PER_PICPATH].$data1[PER_GENNAME]."-".$T_PIC_SEQ.".jpg";
					//----------------------------------------------------------
					$img_file = "";
					$IMG_PATH = "../attachment/pic_personal/";
					if($PER_CARDNO && file_exists($TMP_PIC_NAME)) $img_file = $TMP_PIC_NAME;
					if($img_file=="")	$img_file="images/my_preview.gif";
				
				$PN_CODE = trim($data[PN_CODE]);
				if ($PN_CODE) {
					$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];
					$PN_SHORTNAME = $data2[PN_SHORTNAME];
				}
				$PRENAME = ($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME;
				$FULLNAME = $PRENAME.$FULLNAME;

				$PER_TYPE = $data[PER_TYPE];
				$PER_STATUS = $data[PER_STATUS];
				$POSEM_NO = "";
				$TMP_PL_NAME = "";
				$ORG_NAME = "";
				$ORG_NAME_REF = "";
				if ($PER_TYPE == 1) { // ข้าราชการ
					$POS_ID = $data[POS_ID];
					if ($POS_ID) {
						$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, pt.PT_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF , pp.PM_CODE
										from		PER_POSITION pp, PER_LINE pl, PER_ORG po, PER_TYPE pt 
										where		pp.POS_ID=$POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE and pp.PT_CODE=pt.PT_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = $data2[POS_NO_NAME].$data2[POS_NO];
						$PL_NAME = $data2[PL_NAME];
						$ORG_NAME = $data2[ORG_NAME];
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$PT_CODE = trim($data2[PT_CODE]);
						$PT_NAME = trim($data2[PT_NAME]);
						$PM_CODE = trim($data2[PM_CODE]);

						$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PM_NAME = trim($data2[PM_NAME]);
						if ($RPT_N)
						    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
						else
						    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
					} // end if ($POS_ID) 
				} elseif ($PER_TYPE == 2) { // 
					$POEM_ID = $data[POEM_ID];
					if ($POEM_ID) {
						$cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
										where		pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
						$PL_NAME = trim($data2[PN_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 3) {
					$POEMS_ID = $data[POEMS_ID];
					if ($POEMS_ID) {
						$cmd = " 	select		POEMS_NO_NAME, POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
										where		pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
						$PL_NAME = trim($data2[EP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 4) { // 
					$POT_ID = $data[POT_ID];
					if ($POT_ID) {
						$cmd = " 	select		POT_NO_NAME, POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
										where		pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
						$PL_NAME = trim($data2[TP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} // end if PER_TYPE
				if ($ORG_ID_2) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$ORG_NAME = $data2[ORG_NAME];
				}
				if ($ORG_ID_1) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$ORG_NAME = $data2[ORG_NAME];
				}

				$class = "table_body_over";
//				$deptstr=str_repeat("&nbsp;", ($dept-1)*5)."|__";
				if ($i >= $count_data) {
					$struct_pic = "images/struct_corner.png"; 
					$arr_struct_pic[$dept] = "images/struct_space.png"; 
				} else {
					$struct_pic = "images/struct_cross.png";
					$arr_struct_pic[$dept] = "images/struct_hline.png"; 
				}
				$deptstr="";
				for($j = 2; $j < $dept; $j++) {
					$deptstr="$deptstr<img src=\"".$arr_struct_pic[$j]."\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
				}
				$deptstr="$deptstr"."<img src=\"$struct_pic\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
				echo "<tr>";
//				echo "<td width=\"15\" align=\"center\"><img src=\"images/$icon_name\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\"></td>";
						echo "<td class=\"$class\" style=\"white-space: nowrap\" height=\"22\" width=\"80%\">$deptstr&nbsp;<font color=\"#000000\" >$cntnext_per - <span onMouseOver=\"show_per_pic('$img_file');\" onMouseOut=\"removeElement();\" style=\"cursor:hand\">".$ORG_NAME. " ".$FULLNAME."</span></font><sub>".$PER_TYPE."</sub><font style=\"font-size:12px;color:#F26713\">".$TMP_PL_NAME."</font>&nbsp;&nbsp;&nbsp;<a href='javascript:call_chart(\"$PER_ID\",\"$ORG_ID\",\"$ORG_ID_REF\",\"$org_parent_ol_code\")'><img src=\"images/picture.gif\"  alt=\"พิมพ์ชาร์ด\" width=\"16\" height=\"16\" border=\"0\"></a></td>";
				echo "<td><input type=\"checkbox\" name=\"perH".$PER_ID."\" value=\"".$PER_ID."\" ></td>";
				echo "<td><input type=\"checkbox\" name=\"perD".$PER_ID."\" value=\"".$PER_ID."\" ></td>";
				echo "</tr>";
				$cmd_sub = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
												  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS 
										from   ((((PER_PERSONAL a
														left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
														) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
														) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
														) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									where a.PER_ID_ASS_REF=". $PER_ID." $where
									order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
				$count_data2 = $db_dpis2->send_cmd($cmd_sub);
				if ($count_data2) {
					echo "<td width=\"15\" align=\"center\"></td>";
					echo "<td>";
					$display = "block";
					echo "<div id=\"DIV_". $PER_ID ."\" style=\"display:$display;margin-top:0;\">";
					list_tree_per($PER_ID, $dept+1);
					echo "</div>";
					echo "</td>";
					echo "</tr>";
				}
			} // end loop while
		} // if($count_data)
	} // end function
?>