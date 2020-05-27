 <?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
//	echo "1..ORG_ID-$ORG_ID, ORG_NAME=$ORG_NAME<br>";	
	include("php_scripts/load_per_control.php");
	include("php_scripts/kpi_distribute_function.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($_POST[KPI_YEAR])		$KPI_YEAR = $_POST[KPI_YEAR];					// KPI_DEFINE
	$where_DEPARTMENT_ID="";
	if($DEPARTMENT_ID) $where_DEPARTMENT_ID=" DEPARTMENT_ID=".$DEPARTMENT_ID;

//	echo "2..ORG_ID-$ORG_ID, ORG_NAME=$ORG_NAME , RUN_ORG_ID=$RUN_ORG_ID<br>";	
	$log_org_id = $ORG_ID;	// ใช้ในกรณี log in ระดับ org_id เพราะ $ORG_ID จะถูกเปลี่ยนค่า เป็น $RUN_ORG_ID ใน kpi_distribute.php
	$log_org_name = $ORG_NAME;	// เหมือนบรรทัดบน

	if ($RUN_ORG_ID) $ORG_ID = $RUN_ORG_ID;
	if (!$ORG_ID && $DEPARTMENT_ID) { $ORG_ID=$DEPARTMENT_ID; $search_ol_code="02"; }

	// loop หา depth
	$tree_depth = find_org_dept($ORG_ID) - ($BKK_FLAG==1 ? 1 : 0);	// ถ้าเป็น BKK ค่า MINISTRY กับ DEPARTMENT เท่ากัน
//	echo "tree_depth=$tree_depth (ORG_ID=$ORG_ID)<br>";

//	echo "KPI_ID=$KPI_ID ++ KPI_YEAR=$KPI_YEAR / KPI_ORG_ID=$KPI_ORG_ID ~ KPI_ORG_NAME=$KPI_ORG_NAME --- KPI_DEFINE=$KPI_DEFINE<br>";	
	if($KPI_YEAR && $ORG_ID && $command=="SAVE") {
		$cmd = " select 	ORG_ID, ORG_NAME
						   from 		PER_ORG 
						   where 		 ORG_ID_REF=$ORG_ID
						   order by 	ORG_NAME ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "org .. cmd=$cmd ($count_data)<br>";
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$arr_org[id][] = $data[ORG_ID];
				$arr_org[name][] = $data[ORG_NAME];
			} // end while	
		}
		
		$t_kd_type = $tree_depth;	// - 1;
		$child_org_id = $ORG_ID;

		if ($BKK_FLAG==1) $order_str = "ORG_ID, KPI_TYPE, KPI_NAME";
		else $order_str = "ORG_ID, KPI_NAME";

		$cmd2 = " select 	KPI_ID, KPI_NAME, DEPARTMENT_ID from PER_KPI
							where KPI_YEAR = $KPI_YEAR and DEPARTMENT_ID=$DEPARTMENT_ID
							order by 	$order_str ";
		$count_kpi = $db_dpis2->send_cmd($cmd2);
//		echo "kpi .. cmd=$cmd2 ($count_kpi)<br>";
//		$db_dpis2->show_error();
		if ($count_kpi) {
			while($data2 = $db_dpis2->get_array()){
				$t_kpi_id = $data2[KPI_ID];
				$t_kpi_name = $data2[KPI_NAME];
				$t_kpi_dept = $data2[DEPARTMENT_ID];
				$arr_KD[$t_kpi_id][0][KPI_NAME] = $t_kpi_name;
				if ($OPEN_PER=="OPEN_PER") $t_kd_type = 4;

				if ($t_kd_type == 2)
					$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1 as THIS_ORG, ORG_ID_2, KD_PER_ID, KD_TYPE, KD_FLAG, KD_REMARK
										from 		PER_KPI_DISTRIBUTE 
										where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id  and DEPARTMENT_ID = $t_kpi_dept 
														 and ORG_ID = $child_org_id and KD_TYPE=$t_kd_type
										order by 	KD_ID ";
				else if ($t_kd_type == 3)
					$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2 as THIS_ORG, KD_PER_ID, KD_TYPE, KD_FLAG, KD_REMARK
										from 		PER_KPI_DISTRIBUTE
										where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_kpi_dept
														and ORG_ID_1 = $child_org_id and KD_TYPE=$t_kd_type
										order by 	KD_ID ";
				else if ($t_kd_type == 4)
					$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2 as THIS_ORG, KD_PER_ID, KD_TYPE, KD_FLAG, KD_REMARK
										from 		PER_KPI_DISTRIBUTE
										where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_kpi_dept
														and ORG_ID_2 = $ORG_OPEN_PER and KD_TYPE=$t_kd_type
										order by 	KD_ID ";
				else if ($t_kd_type == 1)
					$cmd3 = " select 		KD_ID, ORG_ID as THIS_ORG, ORG_ID_1, ORG_ID_2, KD_PER_ID, KD_TYPE, KD_FLAG, KD_REMARK
										from 		PER_KPI_DISTRIBUTE 
										where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id 
														and DEPARTMENT_ID = $child_org_id and KD_TYPE=$t_kd_type
										order by 	KD_ID ";
				$count_kpi_dist = $db_dpis3->send_cmd($cmd3);
//				echo "kpi distrubute .. cmd=$cmd3 ($count_kpi_dist)<br>";
//				$db_dpis3->show_error();
				if ($count_kpi_dist > 0) {
					while($data3 = $db_dpis3->get_array()){
						$arr_KD[$t_kpi_id][0][KD_ID] = $data3[KD_ID];
						$arr_KD[$t_kpi_id][$data3[THIS_ORG]][KD_FLAG] = $data3[KD_FLAG];
						$arr_KD[$t_kpi_id][$data3[THIS_ORG]][KD_REMARK] = $data3[KD_REMARK];
//						echo "KPI_ID=".$t_kpi_id.", KPI_NAME=".$t_kpi_name.", this_org=".$data3[THIS_ORG].", PER_ID=".$data3[KD_PER_ID]."<br>";
						if ($data3[KD_PER_ID]) { 
							$arr_KD[$t_kpi_id][$data3[THIS_ORG]][$data3[KD_PER_ID]][KD_FLAG] = $data3[KD_FLAG];
							$arr_KD[$t_kpi_id][$data3[THIS_ORG]][$data3[KD_PER_ID]][KD_REMARK] = $data3[KD_REMARK];
						}
					} // end while	data3
				} // end if($count_kpi_dist)
			} // end while data2
		} // end if ($count_kpi)

//		echo "OPEN_PER=='$OPEN_PER' && ORG_OPEN_PER == '$ORG_OPEN_PER' && ORG_ID=='$ORG_ID'<br>";
		if ($OPEN_PER=="OPEN_PER" && $ORG_OPEN_PER) {
			$orgdepth = get_org_2top($ORG_OPEN_PER);
			$arr_orgdepth = explode(",", $orgdepth);
			if ($BKK_FLAG==1) unset($arr_orgdepth[0]);
			$orgdepth = implode(",",$arr_orgdepth);
			
			$tree_depth1 = find_org_dept($ORG_OPEN_PER) - ($BKK_FLAG==1 ? 1 : 0);	// ถ้าเป็น BKK ค่า MINISTRY กับ DEPARTMENT เท่ากัน
			
//			echo "person .. tree_depth1=$tree_depth1 , orgdepth=$orgdepth<br>";

			foreach($arr_KD as $key=>$value){
				$t_kpi_id = $key;
				$arr_ORG = $value;
	
				// หา person
//				$arr_content = get_arr_person($ORG_OPEN_PER, ($ORG_OPEN_PER!=$ORG_ID ? ($tree_depth1-1) : $tree_depth1));
				$arr_content = get_arr_person($ORG_OPEN_PER, $tree_depth1-1);
//				echo "person in $ORG_OPEN_PER : ".implode(",",$arr_content)." , tree_depth1=$tree_depth1<br>";
				$cnt_person = count($arr_content);
				if ($cnt_person) {
					for($data_count = 0; $data_count < $cnt_person; $data_count++) {
						$tmp_PER_ID = $arr_content[$data_count][PER_ID];
						$tmp_PN_CODE = $arr_content[$data_count][PN_CODE];
						$tmp_PER_NAME = $arr_content[$data_count][PER_NAME];
						$tmp_PER_SURNAME = $arr_content[$data_count][PER_SURNAME];
						$tmp_full_name = $tmp_PER_NAME." ".$tmp_PER_SURNAME;
//						echo "$tmp_PER_ID-$tmp_full_name<br>";
//						$tmp_ORG_ID = $arr_content[$data_count][ORG_ID];
						$tmp_ORG_NAME = $arr_content[$data_count][ORG_NAME];
						$tmp_POS_ID = $arr_content[$data_count][POS_ID];
						
						$t_per_kd_flag = $arr_ORG[$ORG_OPEN_PER][$tmp_PER_ID][KD_FLAG];
						$t_per_kd_remark = $arr_ORG[$ORG_OPEN_PER][$tmp_PER_ID][KD_REMARK];
						
						$a_kd_flag = ${"kd_flag_".$t_kpi_id."_".$ORG_OPEN_PER."_".$tmp_PER_ID};
						$a_kd_remark = ${"kd_remark_".$t_kpi_id."_".$ORG_OPEN_PER."_".$tmp_PER_ID};
//						echo "name-$tmp_PER_ID-$tmp_full_name, flag-$t_per_kd_flag($a_kd_flag), remark-$t_per_kd_remark($a_kd_remark)<br>";

						$t_kd_type = 4;

						$cmd2 = " select  *
											from 	PER_KPI_DISTRIBUTE
											where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and ORG_ID_2 = $ORG_OPEN_PER and KD_PER_ID = $tmp_PER_ID 
															and KD_TYPE=$t_kd_type ";
						$ins_col = "DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, KD_PER_ID".($tmp_POS_ID ? ", KD_POS_ID" : "");
//						$ins_val = $orgdepth.",".$ORG_OPEN_PER.",".$tmp_PER_ID.($tmp_POS_ID ? ",".$tmp_POS_ID : "");
						$ins_val = $orgdepth.",".$tmp_PER_ID.($tmp_POS_ID ? ",".$tmp_POS_ID : "");
						
						$count_dist = $db_dpis2->send_cmd($cmd2);
//						echo "read DISTRIBUTE (person)-cmd2=$cmd2 ($count_dist)<br>";
						if ($count_dist) {	// ถ้ามีรายการอยู่แล้ว
							$data2 = $db_dpis2->get_array();
							if (!$a_kd_flag) {	// ถ้ามีการป้อน flag เป็น No คือไม่ให้มีค่อ เราจะเอาออก
								$cmd3 = " delete from PER_KPI_DISTRIBUTE where KD_ID = ".$data2[KD_ID]." ";
								$db_dpis3->send_cmd($cmd3);							
//								echo "delete :: $cmd3<br>";
							} else {	// ถ้ามีการป้อน flag มา ก็ให้ update ถ้าข้อมูลเปลี่ยน
								if ($a_kd_flag!="SR") $a_kd_remark = "";	// ถ้า flag ไม่ใช่ SR จะไม่มี Remark
								if ($data2[KD_FLAG]!=$a_kd_flag || $data2[KD_REMARK]!=$a_kd_remark) { 
									$cmd3 = " update PER_KPI_DISTRIBUTE set KD_FLAG='$a_kd_flag', KD_REMARK='$a_kd_remark', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where KD_ID = ".$data2[KD_ID]." ";
									$db_dpis3->send_cmd($cmd3);
//									echo "update (person) :: $cmd3<br>";
								}
							}
						} else {	// แปลว่ายังไม่มีรายการนี้
							if ($a_kd_flag) {	// ถ้ามีการป้อน flag ก็จะ เพิ่มรายการนี้เข้าไป
								$cmd2 = " select  max(KD_ID) as LAST_KD from PER_KPI_DISTRIBUTE ";
								$db_dpis2->send_cmd($cmd2);
								$data2 = $db_dpis2->get_array();
								if ($data2[LAST_KD])
									$last_kd_id = $data2[LAST_KD]+1;
								else
									$last_kd_id = 1;
								if ($a_kd_flag!="SR") $a_kd_remark = "";	// ถ้า flag ไม่ใช่ SR จะไม่มี Remark
								$cmd3 = " insert into PER_KPI_DISTRIBUTE (KD_ID, KD_TYPE, KPI_YEAR, KPI_ID, $ins_col, KD_FLAG, KD_REMARK, UPDATE_USER, UPDATE_DATE) values ($last_kd_id, $t_kd_type, '$KPI_YEAR', $t_kpi_id, $ins_val,  '$a_kd_flag', '$a_kd_remark', $SESS_USERID, '$UPDATE_DATE') ";
								$db_dpis3->send_cmd($cmd3);
//								echo "insert (person) :: $cmd3<br>";
							} // end if ($a_kpi_flag)
						} // end if ($count_dist)
					} // end for loop $data_count 
				} // end if ($cnt_person)
			} // end foreach $arr_KD
		} else if ($OPEN_PER=="") {
			$orgdepth = get_org_2top($ORG_ID);
//			echo "1..org .. tree_depth=$tree_depth , orgdepth=$orgdepth<br>";
			$arr_orgdepth = explode(",", $orgdepth);
			if ($BKK_FLAG==1) unset($arr_orgdepth[0]);
			$orgdepth = implode(",",$arr_orgdepth);
//			echo "2..org .. tree_depth=$tree_depth , orgdepth=$orgdepth<br>";

			$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
							from $ORGTAB where ORG_ID_REF = $ORG_ID and ORG_ID_REF != ORG_ID
							order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE
							";
			$count_child = $db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
	//		echo "part2-$cmd-($count_page_data)<br>";
			if ($count_child) {
				$arr_page = (array) null;
				$current_list = "";
				$data_count = 0;
				while($data = $db_dpis->get_array()) {
	//				echo "2..$data[ORG_NAME]<br>";
					if (($data[ORG_ID]!=$START_ORG_ID && $data[ORG_ID]!=0 && $data[ORG_ID]!=1) || $START_ORG_ID==$SESS_ORG_ID) {	//---ไม่เอา parent
						$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=". $data[ORG_ID];
	//					echo "$cmd<br>";
						$count_sub_tree = $db_dpis2->send_cmd($cmd);
	
						$arr_child[$data_count][ORG_ID] = $data[ORG_ID];
						$arr_child[$data_count][ORG_NAME] = $data[ORG_NAME];
						$arr_child[$data_count][count_sub_tree] = $count_sub_tree;
	
						$arr_person = get_arr_person($data[ORG_ID], $tree_depth);
						$arr_child[$data_count][count_person] = count($arr_person);
	//					echo "count_person=".count($arr_person)."<br>";
						$arr_child[$data_count][ORG_ACTIVE] = $data[ORG_ACTIVE];
						
						$data_count++;
					} // end if ($data[ORG_ID]!=$START_ORG_ID)
				} // end while						
	
				$current_list = "";
				$data_count = 0;
	
				foreach($arr_KD as $key=>$value){
					$t_kpi_id = $key;
					$arr_ORG = $value;
	//				echo "class [$t_kd_id]=".$class[$t_kd_id].", class [0]=".$class[0].", class [1]=".$class[1].", class [2]=".$class[2]."<br>";
	
					foreach($arr_child as $data_count=>$dd) {
						$t_ORG_ID = $dd[ORG_ID];						
	//					$t_ORG_NAME = str_repeat(" ", ($tree_depth * 5)) .$dd[ORG_NAME];
						$t_ORG_NAME = $dd[ORG_NAME];
						$t_kd_id = $arr_ORG[0][KD_ID];
						$t_kpi_name = $arr_ORG[0][KPI_NAME];
						$t_kd_flag = $arr_ORG[$t_ORG_ID][KD_FLAG];
						$t_kd_remark = $arr_ORG[$t_ORG_ID][KD_REMARK];
						$person_count = $dd[count_person];
	//					$t_kd_type = $tree_depth;
						$this_depth = find_org_dept($t_ORG_ID) - ($BKK_FLAG==1 ? 1 : 0);	// ถ้าเป็น BKK ค่า MINISTRY กับ DEPARTMENT เท่ากัน
						$t_kd_type = $this_depth;
	
						$a_kd_flag = ${"kd_flag_".$t_kpi_id."_".$t_ORG_ID};
						$a_kd_remark = ${"kd_remark_".$t_kpi_id."_".$t_ORG_ID};
	
						if ($t_kd_type == 2) {
							$cmd2 = " select  *
												from 	PER_KPI_DISTRIBUTE
												where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and ORG_ID = $t_ORG_ID and KD_TYPE=".($t_kd_type-1)." ";
							$ins_col = "DEPARTMENT_ID, ORG_ID";
							$ins_val = $orgdepth.",".$t_ORG_ID;
						} else if ($t_kd_type == 3) {
							$cmd2 = " select  *
												from 	PER_KPI_DISTRIBUTE
												where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and ORG_ID_1 = $t_ORG_ID and KD_TYPE=".($t_kd_type-1)." ";
							$ins_col = "DEPARTMENT_ID, ORG_ID, ORG_ID_1";
							$ins_val = $orgdepth.",".$t_ORG_ID;
						} else if ($t_kd_type == 4) {
							$cmd2 = " select  *
												from 	PER_KPI_DISTRIBUTE
												where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and ORG_ID_2 = $t_ORG_ID and KD_TYPE=".($t_kd_type-1)." ";
							$ins_col = "DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2";
							$ins_val = $orgdepth.",".$t_ORG_ID;
						} else if ($t_kd_type == 1) {
							$cmd2 = " select  *
												from 	PER_KPI_DISTRIBUTE
												where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_ORG_ID and KD_TYPE=".($t_kd_type-1)." ";
							$ins_col = "DEPARTMENT_ID, ORG_ID ";
							$ins_val = $orgdepth.",".$t_ORG_ID;
						}
						
						$count_dist = $db_dpis2->send_cmd($cmd2);
//						if ($count_dist)	echo "read DISTRIBUTE-cmd2=$cmd2 ($count_dist)<br>";
						if ($count_dist) {	// ถ้ามีรายการอยู่แล้ว
							$data2 = $db_dpis2->get_array();
							if (!$a_kd_flag) {	// ถ้ามีการป้อน flag เป็น No คือไม่ให้มีค่อ เราจะเอาออก
								$cmd3 = " delete from PER_KPI_DISTRIBUTE where KD_ID = ".$data2[KD_ID]." ";
								$db_dpis3->send_cmd($cmd3);							
//								echo "delete :: $cmd3<br>";
							} else {	// ถ้ามีการป้อน flag มา ก็ให้ update ถ้าข้อมูลเปลี่ยน
								if ($a_kd_flag!="SR") $a_kd_remark = "";	// ถ้า flag ไม่ใช่ SR จะไม่มี Remark
								if ($data2[KD_FLAG]!=$a_kd_flag || $data2[KD_REMARK]!=$a_kd_remark) { 
									$cmd3 = " update PER_KPI_DISTRIBUTE set KD_FLAG='$a_kd_flag', KD_REMARK='$a_kd_remark', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where KD_ID = ".$data2[KD_ID]." ";
									$db_dpis3->send_cmd($cmd3);
//									echo "update :: $cmd3<br>";
								}
							}
						} else {	// แปลว่ายังไม่มีรายการนี้
							if ($a_kd_flag) {	// ถ้ามีการป้อน flag ก็จะ เพิ่มรายการนี้เข้าไป
								$cmd2 = " select  max(KD_ID) as LAST_KD from PER_KPI_DISTRIBUTE ";
								$db_dpis2->send_cmd($cmd2);
								$data2 = $db_dpis2->get_array();
								if ($data2[LAST_KD])
									$last_kd_id = $data2[LAST_KD]+1;
								else
									$last_kd_id = 1;
								if ($a_kd_flag!="SR") $a_kd_remark = "";	// ถ้า flag ไม่ใช่ SR จะไม่มี Remark
//								$cmd3 = " insert into PER_KPI_DISTRIBUTE (KD_ID, KD_TYPE, KPI_YEAR, KPI_ID, DEPARTMENT_ID, ORG_ID, ORG_ID_1, KD_PER_ID, KD_POS_ID, KD_FLAG, KD_REMARK, UPDATE_USER, UPDATE_DATE) values ($last_kd_id, '1', '$KPI_YEAR', $t_kpi_id, $DEPARTMENT_ID, $t_ORG_ID,  $SESS_USERID, '$UPDATE_DATE') ";
								$cmd3 = " insert into PER_KPI_DISTRIBUTE (KD_ID, KD_TYPE, KPI_YEAR, KPI_ID, $ins_col, KD_FLAG, KD_REMARK, UPDATE_USER, UPDATE_DATE) values ($last_kd_id, ($t_kd_type-1), '$KPI_YEAR', $t_kpi_id, $ins_val, '$a_kd_flag', '$a_kd_remark', $SESS_USERID, '$UPDATE_DATE') ";
								$db_dpis3->send_cmd($cmd3);
//								echo "insert :: $cmd3<br>";
							} // end if ($a_kpi_flag)
						} // end if ($count_dist)
					} // end foreach $arr_page
				} // end foreach $arr_kpi
			} // end if($count_child)
		} // end else if ($OPEN_PER=="")
	} // end if SAVE

	if($KPI_YEAR && $KPI_ORG_ID && $KPI_DEFINE){		//if($KPI_YEAR && $KPI_ORG_ID && $KPI_DEFINE){
		// หา KPI_ID ของ ORG_ID นี้
		$cmd = " select 	KPI_ID
						   from 		PER_KPI
						   where (KPI_YEAR=$KPI_YEAR) and (ORG_ID=$KPI_ORG_ID) 
						   order by 	ORG_SCORE_FLAG ";
		$count_data = $db_dpis->send_cmd($cmd);
		if(!$count_data){
			
		}else{
			$data = $db_dpis->get_array();
			$KPI_ID = $data[KPI_ID];
		}
	
		//if($KPI_ID){KPI_ID=$KPI_ID
			$cmd = " update PER_KPI 
							 set KPI_DEFINE = '$KPI_DEFINE'
							 where (KPI_YEAR = $KPI_YEAR and ORG_ID = $KPI_ORG_ID) ";
//			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "-> $cmd";
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > การกระจายตัวชี้วัด $KPI_ID ปีงบประมาณ $KPI_YEAR ลงสู่ส่วนราชการ $KPI_ORG_NAME : $KPI_DEFINE");
		//}
	}

	if ($where_DEPARTMENT_ID) {
		$cmd = " select distinct KPI_YEAR from PER_KPI where (".$where_DEPARTMENT_ID.") order by KPI_YEAR desc ";
		$HAVE_YEAR = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			if(!$START_YEAR) $START_YEAR = $data[KPI_YEAR];
			$arr_kpi_year[] = $data[KPI_YEAR];
		} // end while
	}
	
//	echo "KPI_YEAR=$KPI_YEAR, START_YEAR=$START_YEAR (".implode(",",$arr_kpi_year).")<br>";
	if(!$KPI_YEAR || !in_array((int)$KPI_YEAR, $arr_kpi_year)) $KPI_YEAR = $START_YEAR;

	function list_tree_org ($in_depth) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $LIST_OPENED_KPI, $KPI_YEAR, $DEPARTMENT_ID, $BKK_FLAG, $where_DEPARTMENT_ID, $EDIT_TITLE,$DEPARTMENT_TITLE, $KD_TYPE;
		global $arr_table_width;

		$arr_opened_kpi = explode(",", $in_depth);
		$tree_depth = count($arr_opened_kpi);	// 1 คือ ระดับ กรม (ORG_ID), 2 คือ ORG_ID_1, 3 คือ ORG_ID_2, 4 คือ KD_PER_ID
		$search_org = ($arr_opened_kpi[0] ? ($arr_opened_kpi[1] ? ($arr_opened_kpi[2] ? $arr_opened_kpi[2] : $arr_opened_kpi[1]) : $arr_opened_kpi[0]) : "$DEPARTMENT_ID");
		$search_kd_typ = $tree_depth+1;	// KD_TYPE = 1 คือ ขั้นที่ 1 , 2 คือ ขั้นที่ 2 , 3 คือ ขั้นที่ 3
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if ($BKK_FLAG==1) $order_str = "a.ORG_ID, a.KPI_TYPE, a.KPI_NAME";
		else $order_str = "a.ORG_ID, a.KPI_NAME";

//	   and		ORG_SCORE_FLAG > 0		OL_CODE=03	// สำนักกอง/ส่วนราชการ
		$cmd = " select 	ORG_ID, ORG_NAME
						   from 		PER_ORG 
						   where 		ORG_ID_REF=$search_org
						   order by 	ORG_NAME ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "org .. cmd=$cmd ($count_data)<br>";
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$arrKPI_ORG_NAME[$data[ORG_ID]] = $data[ORG_NAME];
			} // end while	
		}

		if ($tree_depth > 0)
			$cmd = " select 		KD_ID, a.KPI_ID, a.KPI_NAME, b.ORG_ID, b.KD_TYPE, b.KD_FLAG, b.KD_REMARK
								from 		PER_KPI a left join PER_KPI_DISTRIBUTE b on (a.KPI_YEAR = b.KPI_YEAR and a.KPI_ID = b.KPI_ID and a.DEPARTMENT_ID=b.DEPARTMENT_ID)
								where 	(a.KPI_YEAR = $KPI_YEAR and a.ORG_ID = $search_org and (KD_TYPE=$search_kd_typ or KD_TYPE is null))
								order by 	$order_str ";
		else
			$cmd = " select 		KD_ID, a.KPI_ID, a.KPI_NAME, b.ORG_ID, b.KD_TYPE, b.KD_FLAG, b.KD_REMARK
								from 		PER_KPI a left join PER_KPI_DISTRIBUTE b on (a.KPI_YEAR = b.KPI_YEAR and a.KPI_ID = b.KPI_ID and a.DEPARTMENT_ID=b.DEPARTMENT_ID)
								where 	(a.KPI_YEAR = $KPI_YEAR and a.DEPARTMENT_ID = $DEPARTMENT_ID and (KD_TYPE=$search_kd_typ or KD_TYPE is null))
								order by 	$order_str ";
		$count_data = $db_dpis->send_cmd($cmd);
//		echo "kpi .. cmd=$cmd ($count_data)<br>";
//		$db_dpis->show_error();
		if($count_data){
//			echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"label_normal\">";		
//			echo "<tr><td colspan='2'>".$DEPARTMENT_TITLE."ที่รับการประเมิน</td></tr>";
			while($data = $db_dpis->get_array()){
				$arr_KD[$data[KPI_ID]][0][KD_ID] = $data[KD_ID];
				$arr_KD[$data[KPI_ID]][0][KPI_NAME] = $data[KPI_NAME];
				$arr_KD[$data[KPI_ID]][$data[ORG_ID]][KD_FLAG] = $data[KD_FLAG];
				$arr_KD[$data[KPI_ID]][$data[ORG_ID]][KD_REMARK] = $data[KD_REMARK];
			} // end while	

			foreach($arr_KD as $key=>$value){
				$t_kpi_id = $key;
				$arr_ORG = $value;
//				echo "class [$t_kd_id]=".$class[$t_kd_id].", class [0]=".$class[0].", class [1]=".$class[1].", class [2]=".$class[2]."<br>";
				
				$org_i = 0;
				foreach($arrKPI_ORG_NAME as $key1=>$value1) {
					$t_ORG_ID = $key1;
					$t_ORG_NAME = str_repeat(" ", ($tree_depth * 5)) .$value1;
					$t_kd_id = $arr_ORG[0][KD_ID];
					$t_kpi_name = $arr_ORG[0][KPI_NAME];
					$t_kd_flag = $arr_ORG[$t_ORG_ID][KD_FLAG];
					$t_kd_remark = $arr_ORG[$t_ORG_ID][KD_REMARK];
					
//					echo "..........$t_ORG_ID-$t_ORG_NAME<br>";
					$class = "table_body";
					$onmouse_event = " onMouseOver=\"this.className='table_body_over';\" onMouseOut=\"this.className='table_body';\" ";
//					if($$arr_fields[0]==${"temp_".$arr_fields[0]}){ 
//						$class = "table_body_over";
//						$onmouse_event = "";
//					} // end if
					
					echo "<tr class='$class' $onmouse_event>"; 
//					echo "<td width=\"15\" align=\"center\" valign=\"top\"><img src=\"images/".$icon_name[$KPI_ID]."\" width=\"9\" height=\"9\" onClick=\"$onClick\" style=\"cursor:hand;\" vspace=\"5\"></td>";
					
					if ($org_i==0) 
						echo "<td  height=\"22\" width=\"".$arr_table_width[0]."%\">&nbsp;<span style=\"cursor:hand\"><strong>" .$t_kpi_name."</strong></span></td>";	
					else
						echo "<td  height=\"22\" width=\"".$arr_table_width[0]."%\"></td>";
					echo "<td  height=\"22\" width=\"".$arr_table_width[1]."%\" onclick=\"\">&nbsp;<span style=\"cursor:hand\"><strong>" .$t_ORG_NAME."</strong></span></td>";
//					echo "<td height=\"22\">&nbsp;<select name=\"kd_flag_".$t_kd_id."\"><option  value='R' ".($t_kd_flag=="R" ? "selected" : "").">R</option>&nbsp;<option  value='SR' ".($t_kd_flag=="SR" ? "selected" : "").">SR</option>&nbsp;<option  value='JR' ".($t_kd_flag=="JR" ? "selected" : "").">JR</option></td>";	
					echo "<td  height=\"22\" width=\"".$arr_table_width[2]."%\">&nbsp;R<input style='width: 20px;' type='radio' name='kd_flag_".$t_kpi_id."_".$t_ORG_ID."' value='R' ".($t_kd_flag=="R" ? "checked" : "")."/>&nbsp;SR<input style='width: 20px;' type='radio' name='kd_flag_".$t_kpi_id."_".$t_ORG_ID."' value='SR' ".($t_kd_flag=="SR" ? "checked" : "")."/>&nbsp;JR<input style='width: 20px;' type='radio' name='kd_flag_".$t_kpi_id."_".$t_ORG_ID."' value='JR' ".($t_kd_flag=="JR" ? "checked" : "")."/>&nbsp;No<input style='width: 20px;' type='radio' name='kd_flag_".$t_kpi_id."_".$t_ORG_ID."' value='' ".($t_kd_flag=="" ? "checked" : "")."/></td>";	
					echo "<td height=\"22\">&nbsp;<span style=\"cursor:hand\"><input type='text' name='kd_remark_".$t_kpi_id."_".$t_ORG_ID."' value='$t_kd_remark' size='50'></span></td>";
//					echo "<td height=\"22\">&nbsp;<input name='KPI_R' type='button' value='R' title='$KPI_NAME (R)' onClick=\"form1.KPI_ID.value='$KPI_ID';form1.KPI_NAME.value='$KPI_NAME';form1.KPI_DEFINE.value=this.value;form1.submit();\">&nbsp;<input name='KPI_SR' type='button' value='SR' title='$KPI_NAME (SR)' onClick=\"if(form1.KPI_SR_TEXT_$KPI_ID.value==''){  alert('กรุณาระบุสำหรับ SR ($KPI_NAME) เมื่อระบุแล้วกดปุ่ม SR อีกครั้ง'); form1.KPI_SR_TEXT_$KPI_ID.focus(); }else{ form1.KPI_ID.value='$KPI_ID';form1.KPI_NAME.value='$KPI_NAME';form1.KPI_DEFINE.value=form1.KPI_SR_TEXT_$KPI_ID.value;form1.submit(); }\">&nbsp;<input name='KPI_JR' type='button' value='JR' title='$KPI_NAME (JR)' onClick=\"form1.KPI_ID.value='$KPI_ID';form1.KPI_NAME.value='$KPI_NAME';form1.KPI_DEFINE.value=this.value;form1.submit();\"><br><input name='KPI_SR_TEXT_$KPI_ID' type='text' value='' title='ระบุสำหรับ SR ($KPI_NAME)'></td>";	
					echo "</tr>";
					$org_i++;
				} // end foreach $arrKPI_ORG_NAME
				echo "<tr><td colspan='4' class='table_head'>&nbsp;</td></tr>";
			} // end foreach $arr_KD
			/*echo "</table><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"right\"><input name=\"Submit22\" type=\"submit\" class=\"button\" onClick=\"form1.command.value='UPDATE_SCORE';\"  value=\"".$EDIT_TITLE."คะแนน\"></td></tr></table>";  */
		} // end if
	} // function
	
?>