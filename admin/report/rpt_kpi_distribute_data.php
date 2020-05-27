<? 
	if ($BYASS=="Y") $ORGTAB = "PER_ORG_ASS"; else $ORGTAB = "PER_ORG";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if (!$KD_TYPE) $KD_TYPE = 1;	// ขั้นตอนที่ 1
	
//	echo "ORGTAB=$ORGTAB , KPI_YEAR=$KPI_YEAR , DEPARTMENT_ID=$DEPARTMENT_ID<br>";

	$ORG_ID=$RUN_ORG_ID;

	// loop หา depth
	$tree_depth = find_org_dept($ORG_ID) - ($BKK_FLAG==1 ? 1 : 0);	// ถ้าเป็น BKK ค่า MINISTRY กับ DEPARTMENT เท่ากัน
//	if ($tree_depth == 3) {	 // คือ depth 3 คือ ขั้น 2 รายงานเหมือนกับ 2
//		$tree_depth = 2;
//		$arr_o = explode(",",get_org_2top($ORG_ID));
//		echo "arr_o (ORG_ID=$ORG_ID)(tree_depth=$tree_depth) = ".implode(",",$arr_o)." ";
//		$ORG_ID = $arr_o[count($arr_o)-2];
//		echo " (last_ORG_ID=$ORG_ID)<br>";
//	}
	
//	echo "tree_depth=$tree_depth (ORG_ID=$ORG_ID)<br>";

	if($KPI_YEAR && $ORG_ID) {
                	$tab = "";
                	$TMP_ORG_ID = $ORG_ID;
					$arr_org = (array) null;
					$firstloop=1;
					while ($TMP_ORG_ID > 0 && $TMP_ORG_ID != $MINISTRY_ID) {
						$cmd = " select ORG_NAME, ORG_ID_REF from $ORGTAB where ORG_ID=$TMP_ORG_ID ";
						$cnt = $db_dpis->send_cmd($cmd);
//						echo "1..$cmd ($cnt)<br>";
						if ($cnt > 0) {
							if ($data = $db_dpis->get_array()) {
//								echo "1..$data[ORG_NAME] ($TMP_ORG_ID)<br>";
								$arr_org[id][] = $TMP_ORG_ID;
								$arr_org[name][] = $data[ORG_NAME];
								$arr_org[id_ref][] = $data[ORG_ID_REF];
								if ($TMP_ORG_ID==$data[ORG_ID_REF]) {
									$TMP_ORG_ID = 0;
								} else {
									$TMP_ORG_ID = $data[ORG_ID_REF];
								}
// เพิ่มส่วนนี้เพื่อ เก็บ parant ไว้สำหรับการเลื่อนระดับขึ้น
								if ($firstloop == 1 && $TMP_ORG_ID > 1) {
									$firstloop=0; 
									$PARENT_ORG_ID=$data[ORG_ID_REF];
								} // end if ($firstloop == 1)
								// echo "$firstloop - $PARENT_ORG_ID ===> $cmd<br>";
// เพิ่มส่วนนี้เพื่อ เก็บ parant ไว้สำหรับการเลื่อนระดับขึ้น
							} // end if ($data = $db_dpis->get_array())
						} else {
                        	$TMP_ORG_ID = 0;
						} // end if ($cnt > 0)
					} // end loop while
					array_multisort($arr_org[id], SORT_ASC, $arr_org[name], SORT_ASC, $arr_org[id_ref], SORT_ASC);

					$arr_head_org = (array) null;
					for($i=0; $i < count($arr_org[id]); $i++) {
//						echo "arr_org [id] [$i] (".$arr_org[id][$i].")==ORG_ID ($ORG_ID)<br>";
//						echo "ORG_ID($ORG_ID) > DEPARTMENT_ID($DEPARTMENT_ID) && arr_org [id][$i](".$arr_org[id][$i]."==ORG_ID($ORG_ID) && count_data($count_data)==0<br>";
           	  			$arr_head_org[] = $arr_org[name][$i];
					} // end for $i
					
					$cmd = " select ORG_ID , ORG_NAME, ORG_ID_REF from $ORGTAB where ORG_ID_REF = $ORG_ID and ORG_NAME != '-' and ORG_ID_REF != ORG_ID
									order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE ";
					$count_data = $db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
//					echo "part1 $cmd ($count_data)<br>";
					if ($count_data) {
                    	$arr_KD = (array) null;
                    	$t_kd_type = $tree_depth;	// - 1;
						for($i=0; $i < count($arr_org[id]); $i++) {
                        	if ($arr_org[id][$i]==$ORG_ID) {
//								echo "arr_org [id] [$i] (".$arr_org[id][$i].")==ORG_ID ($ORG_ID) , t_kd_type=$t_kd_type<br>";
								$parent_org_id = $arr_org[id][$i];

								if ($BKK_FLAG==1) $order_str = "ORG_ID, KPI_TYPE, KPI_NAME";
								else $order_str = "ORG_ID, KPI_NAME";

								$cmd2 = " select 	KPI_ID, KPI_NAME, DEPARTMENT_ID from PER_KPI
													where KPI_YEAR = $KPI_YEAR and DEPARTMENT_ID=$DEPARTMENT_ID
													order by 	$order_str ";
								$count_kpi = $db_dpis2->send_cmd($cmd2);
//								echo "kpi .. cmd=$cmd2 ($count_kpi)<br>";
//								$db_dpis2->show_error();
								if ($count_kpi) {
									while($data2 = $db_dpis2->get_array()){
                                    	$t_kpi_id = $data2[KPI_ID];
                                    	$t_kpi_name = $data2[KPI_NAME];
                                    	$t_kpi_dept = $data2[DEPARTMENT_ID];
										$arr_KD[$t_kpi_id][0][KPI_NAME] = $t_kpi_name;

                                        if ($t_kd_type == 2) {
											// กรณี tree_depth ระดับ 2 คือขั้นที่ 2 ในรายงานจะแสดงข้อมูลในระดับลูก 1 ชั้น จึงต้องทำการอ่านลูกออกมาก่อนอีกระดับที่ KD_TYPE ต่ำไปอีกระดับคือ 3
											$cmd3 = " select ORG_ID , ORG_NAME from $ORGTAB where ORG_ID_REF = $parent_org_id and ORG_NAME != '-' and ORG_ACTIVE = 1
															order by ORG_SEQ_NO, ORG_CODE ";
											$count_inOrg = $db_dpis3->send_cmd($cmd3);
//											echo "type=2.select Org.. cmd=$cmd3 ($count_inOrg)<br>";
											$all_child_child_org = "";
											$all_child_child = (array) null;
											if ($count_inOrg) {
												while($data3 = $db_dpis3->get_array()){
													if (trim($data3[ORG_ID])=="-") {
//														$cmd3 = " select ORG_ID from $ORGTAB where ORG_ID_REF=". $data3[ORG_ID];
//														$count_sub_tree = $db_dpis3->send_cmd($cmd3);
//														$arr_person = get_arr_person($data2[ORG_ID], ($tree_depth+1));
//														$arr_page[$data_count][PER_ORG_ID] = $data2[ORG_ID];	// เก็บ ORG_ID  ของการอ่านราบบุคคล
														$all_child[] = $parent_org_id;
													} else 
														$all_child[] = $data3[ORG_ID];
												}
												$all_child_org = implode(",", $all_child);
											}
											if ($all_child_child_org) {
												$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2 as THIS_ORG, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
																	from 		PER_KPI_DISTRIBUTE
																	where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_kpi_dept
																					and ORG_ID_1 in ($all_child_org) and KD_TYPE=".($t_kd_type+1)."
																	order by 	KD_ID ";
											} else {	// ถ้าไม่มีข้อมูลในประเภท 3 ก็ให้อ่านที่ระดับในประเภทที่ 2 แทน
												$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1 as THIS_ORG, ORG_ID_2, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
																	from 		PER_KPI_DISTRIBUTE 
																	where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id  and DEPARTMENT_ID = $t_kpi_dept 
																					and ORG_ID = $parent_org_id and KD_TYPE=$t_kd_type
																	order by 	KD_ID ";
											}
										} else if ($t_kd_type == 3)
                                            $cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2 as THIS_ORG, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
                                                                from 		PER_KPI_DISTRIBUTE
                                                                where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_kpi_dept
                                                                				and ORG_ID_1 = $parent_org_id and KD_TYPE=$t_kd_type
                                                                order by 	KD_ID ";
                                        else if ($t_kd_type == 4)
                                            $cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
                                                                from 		PER_KPI_DISTRIBUTE
                                                                where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $t_kpi_dept
                                                                				and ORG_ID_2 = $parent_org_id and KD_TYPE=$t_kd_type
                                                                order by 	KD_ID ";
                                        else if ($t_kd_type == 1)
                                            $cmd3 = " select 		KD_ID, ORG_ID as THIS_ORG, ORG_ID_1, ORG_ID_2, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
                                                                from 		PER_KPI_DISTRIBUTE 
                                                                where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id 
																				and DEPARTMENT_ID = $parent_org_id and KD_TYPE=$t_kd_type
                                                                order by 	KD_ID ";
                                        $count_kpi_dist = $db_dpis3->send_cmd($cmd3);
//										echo "kpi .(type=$t_kd_type). cmd=$cmd3 ($count_kpi_dist)<br>";
//										$db_dpis3->show_error();
                                        if ($count_kpi_dist) {
                                            while($data3 = $db_dpis3->get_array()){
                                                $arr_KD[$t_kpi_id][0][KD_ID] = $data3[KD_ID];
                                                if ($data3[KD_PER_ID]) {
//                                                	echo "---per_id-->".$data3[KD_PER_ID]."<br>";
                                                    $arr_KD[$t_kpi_id][$data3[THIS_ORG]][$data3[KD_PER_ID]][KD_FLAG] = $data3[KD_FLAG];
                                                    $arr_KD[$t_kpi_id][$data3[THIS_ORG]][$data3[KD_PER_ID]][KD_REMARK] = $data3[KD_REMARK];
												} else {
//                                               	echo "---not per_id--><br>";
                                                    $arr_KD[$t_kpi_id][$data3[THIS_ORG]][KD_FLAG] = $data3[KD_FLAG];
                                                    $arr_KD[$t_kpi_id][$data3[THIS_ORG]][KD_REMARK] = $data3[KD_REMARK];
//													echo "this_org=".$data3[THIS_ORG].", flag=".$data3[KD_FLAG]."<br>";
												}
//												echo "KPI_ID=".$t_kpi_id.", KPI_NAME=".$t_kpi_name.", KPI_PER_ID=".$data3[KD_PER_ID]."<br>";
                                            } // end while	data3
                                        } // end if($count_kpi_dist)
									} // end while data2
								} // end if ($count_kpi)
							} // end if ($arr_org[id]==$ORG_ID)
						} // end for $arr_org
                        
						if($DPISDB=="odbc"){	
							$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
										  from $ORGTAB where ORG_ID_REF = $ORG_ID and ORG_ID_REF != ORG_ID and ORG_NAME != '-' and ORG_ACTIVE = 1
										  order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE
					  						";	
						}elseif($DPISDB=="oci8"){
							$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
                                            from $ORGTAB where ORG_ID_REF = $ORG_ID and ORG_ID_REF != ORG_ID and ORG_NAME != '-' and ORG_ACTIVE = 1
											order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE
											";
						}elseif($DPISDB=="mysql"){
							$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE from $ORGTAB 
                            				where ORG_ID_REF = $ORG_ID and ORG_ID_REF != ORG_ID and ORG_NAME != '-' and ORG_ACTIVE = 1
											order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE
											";
						} // end if
						$count_page_data = $db_dpis->send_cmd($cmd);
//						$db_dpis->show_error();
//						echo "part2-$cmd-($count_page_data)<br>";
						if($count_page_data){
                        	$arr_page = (array) null;
							$current_list = "";
							$data_count = 0;
							$grp_cnt = 0;
							while($data = $db_dpis->get_array()) {
//                            	echo "2..$data[ORG_NAME]<br>";
                            	if (($data[ORG_ID]!=$START_ORG_ID && $data[ORG_ID]!=0 && $data[ORG_ID]!=1) || $START_ORG_ID==$SESS_ORG_ID) {	//---ไม่เอา parent
									$cmd = " select ORG_ID, ORG_NAME  from $ORGTAB where ORG_ID_REF=". $data[ORG_ID];
									$count_sub_tree = $db_dpis2->send_cmd($cmd);
//									echo "cmd=$cmd ($count_sub_tree)<br>";

									$parent_name = "";
//									echo "tree_depth=$tree_depth<br>";
									if ($tree_depth == 2) {
										if ($count_sub_tree > 1)	$grp_cnt++;
//										echo "data_count=$data_count, grp_cnt=$grp_cnt , parent=".$data[ORG_NAME]."<br>";
										while($data2 = $db_dpis2->get_array()) {
											if ($data2[ORG_NAME]=="-") {
												$cmd3 = " select ORG_ID, ORG_NAME  from $ORGTAB where ORG_ID=". $ORG_ID;
												$count_parent = $db_dpis3->send_cmd($cmd3);
												if ($data3 = $db_dpis3->get_array()) $parent_name = $data3[ORG_NAME]; else $parent_name = "";
												$arr_page[$data_count][ORG_ID] = $data[ORG_ID];
												if ($arr_page[$data_count-1][PARENT_NAME] != $parent_name) $grp_cnt++;
												$arr_page[$data_count][grp_num] = $grp_cnt;
												$arr_page[$data_count][PARENT_NAME] = $parent_name;
//												echo "1..parent(".$parent_name.")==org(".$data[ORG_NAME].") ... grp_num=$grp_cnt<br>";
												$arr_page[$data_count][ORG_NAME] = $data[ORG_NAME];
											} else {
												$arr_page[$data_count][ORG_ID] = $data2[ORG_ID];
												$arr_page[$data_count][PARENT_NAME] = $data[ORG_NAME];
												if ($count_sub_tree > 1) $arr_page[$data_count][grp_num] = $grp_cnt;
//												echo "2..parent(".$data[ORG_NAME].")==org(".$data2[ORG_NAME].") ... grp_num=$grp_cnt<br>";
												$arr_page[$data_count][ORG_NAME] = $data2[ORG_NAME];
//												$arr_page[$data_count][count_sub_tree] = $count_sub_tree;
											}
											$data_count++;
											if($data_count > $data_per_page) break;
										}
									} else {
										$arr_page[$data_count][ORG_ID] = $data[ORG_ID];
										$arr_page[$data_count][PARENT_NAME] = "";
										$arr_page[$data_count][ORG_NAME] = $data[ORG_NAME];
//										$arr_page[$data_count][count_sub_tree] = $count_sub_tree;
//										$arr_person = get_arr_person($data[ORG_ID], $tree_depth);
//										$arr_page[$data_count][count_person] = count($arr_person);
//										echo "count_person=".count($arr_person)."<br>";
										$arr_page[$data_count][ORG_ACTIVE] = $data[ORG_ACTIVE];
//										echo "3..parent(".$data[ORG_NAME].")==org(".$data2[ORG_NAME].") ... grp_num=$grp_cnt<br>";
									
										$data_count++;
										if($data_count > $data_per_page) break;
									}
                                } // end if ($data[ORG_ID]!=$START_ORG_ID)
							} // end while						

							$current_list = "";
							$data_count = 0;

							$arr_content = (array) null;
							$arr_head_table = (array) null;
							$arr_head_table[] = "ตัวชี้วัด";
                            foreach($arr_KD as $key=>$value){
                                $t_kpi_id = $key;
                                $arr_ORG = $value;
								$t_kpi_name = $arr_ORG[0][KPI_NAME];
								$arr_content[$data_count][0] = $t_kpi_name;
//								echo "t_kpi_id($t_kpi_id)-->KPI_NAME(".$arr_ORG[0][KPI_NAME].")<br>";
                                
								foreach($arr_page as $cnt_org=>$dd) {
									$t_ORG_ID = $dd[ORG_ID];
//									$t_ORG_NAME = str_repeat(" ", ($tree_depth * 5)) .$dd[ORG_NAME];
									$t_PARENT_NAME = $dd[PARENT_NAME];
									$t_grp_num = $dd[grp_num];
									$t_ORG_NAME = $dd[ORG_NAME];
									$t_kd_id = $arr_ORG[0][KD_ID];
									$person_count = $dd[count_person];

//									echo "$OPEN_PER==\"OPEN_PER\" && $ORG_OPEN_PER == $t_ORG_ID<br>";
									if ($OPEN_PER=="OPEN_PER" && $ORG_OPEN_PER == $t_ORG_ID) {
										if ($data_count==0) $arr_head_org[] = $t_ORG_NAME;	// เพิ่ม ชื่อหัวเข้าไปใน head title
										$arr_KD1 = (array) null;
										$t_kd_type = 4; // กรณี รายบุคคล

										$cmd3 = " select 		KD_ID, ORG_ID, ORG_ID_1, ORG_ID_2, KD_PER_ID, KD_TYPE, KD_FLAG, KD_REMARK
															from 		PER_KPI_DISTRIBUTE
															where 	KPI_YEAR = $KPI_YEAR and KPI_ID = $t_kpi_id and DEPARTMENT_ID = $DEPARTMENT_ID
																			and ORG_ID_2 = $ORG_OPEN_PER and KD_TYPE=$t_kd_type
															order by 	KD_ID ";
										$count_kpi_dist = $db_dpis3->send_cmd($cmd3);
//										echo "kpi .. cmd=$cmd3 ($count_kpi_dist)<br>";
//										$db_dpis3->show_error();
										if ($count_kpi_dist) {
											while($data3 = $db_dpis3->get_array()){
												$arr_KD1[$t_kpi_id][0][KD_ID] = $data3[KD_ID];
												if ($data3[KD_PER_ID]) {
//	                                               	echo "---per_id-->".$data3[KD_PER_ID]."<br>";
													$arr_KD1[$t_kpi_id][$data3[ORG_ID_2]][$data3[KD_PER_ID]][KD_FLAG] = $data3[KD_FLAG];
													$arr_KD1[$t_kpi_id][$data3[ORG_ID_2]][$data3[KD_PER_ID]][KD_REMARK] = $data3[KD_REMARK];
												} else {
//		                                               	echo "---not per_id--><br>";
													$arr_KD1[$t_kpi_id][$data3[ORG_ID_2]][KD_FLAG] = $data3[KD_FLAG];
													$arr_KD1[$t_kpi_id][$data3[ORG_ID_2]][KD_REMARK] = $data3[KD_REMARK];
												}
//												echo "KPI_ID=".$t_kpi_id.", KPI_NAME=".$t_kpi_name.", KPI_PER_ID=".$data3[KD_PER_ID]."<br>";
											} // end while	data3
										} // end if($count_kpi_dist)

//										echo "t_kpi_id($t_kpi_id)-->KPI_NAME(".$arr_ORG[0][KPI_NAME].")<br>";

										// หา person
										$arr_person = get_arr_person($t_ORG_ID, $tree_depth);
										$cnt_person = count($arr_person);
										if ($cnt_person) {
											for($col_count = 0; $col_count < $cnt_person; $col_count++) {
												$tmp_PER_ID = $arr_person[$col_count][PER_ID];
												$tmp_PN_CODE = $arr_person[$col_count][PN_CODE];
												$tmp_PER_NAME = $arr_person[$col_count][PER_NAME];
												$tmp_PER_SURNAME = $arr_person[$col_count][PER_SURNAME];
												$tmp_full_name = $tmp_PER_NAME." ".$tmp_PER_SURNAME;
												$tmp_ORG_ID = $arr_person[$col_count][ORG_ID];
												$tmp_ORG_NAME = $arr_person[$col_count][ORG_NAME];
												$t_kd_flag1 = $arr_KD1[$t_kpi_id][$ORG_OPEN_PER][$tmp_PER_ID][KD_FLAG];
												$t_kd_remark1 = $arr_KD1[$t_kpi_id][$ORG_OPEN_PER][$tmp_PER_ID][KD_REMARK];
//												echo "KPI_ID($t_kpi_id)-ORG_ID($ORG_OPEN_PER)-PER_ID($tmp_PER_ID)  ==> $t_kd_flag1 :: $t_kd_remark1 |".($t_kd_flag1=='R' ? 'checked' : '')."|".($t_kd_flag1=='SR' ? 'checked' : '')."|".($t_kd_flag1=='JR' ? 'checked' : '')."|<br>";
												if ($data_count==0) $arr_head_table[] = $tmp_full_name;	// สร้างหัวของ table สำหรับ รายการตามบุคคล เฉพาะ loop แรก
												$arr_content[$data_count][$col_count+1] = $t_kd_flag1;
											} // end for $col_count
										} // end if ($cnt_person)
									} else {	// else if ($OPEN_PER=="OPEN_PER" && $ORG_OPEN_PER == $t_ORG_ID)
	                                    $t_kd_flag = $arr_ORG[$t_ORG_ID][KD_FLAG];
	                                    $t_kd_remark = $arr_ORG[$t_ORG_ID][KD_REMARK];
//										echo "ORG_ID=$t_ORG_ID :: flag=$t_kd_flag, remark=$t_kd_remark<br>";
										if ($data_count==0) {
											if ($t_PARENT_NAME==$t_ORG_NAME) $t_ORG_NAME = "";
											if ($t_grp_num) { // 	ถ้าตัวนี้เป็นกลุ่ม
												// สร้างหัวของ table สำหรับ รายการตามบุคคล เฉพาะ loop แรก
												$t_PARENT_NAME = "<**".$t_grp_num."**>".$t_PARENT_NAME;
//											} else { // ถ้าไม่เป็นกลุ่ม
											}
//											echo "org=".$t_PARENT_NAME."|".$t_ORG_NAME.""."<br>";
											$arr_head_table[] = ($t_PARENT_NAME ? $t_PARENT_NAME."|" : "").$t_ORG_NAME;	// สร้างหัวของ table สำหรับ รายการตาม ORG
										}
										$arr_content[$data_count][$cnt_org+1] = $t_kd_flag;
									} // end if ($OPEN_PER=="OPEN_PER" && $ORG_OPEN_PER == $t_ORG_ID)
                                } // end foreach $arr_page
								$data_count++;
							} // end foreach $arr_kpi
						} // end if($count_page_data)
					} // end if ($count_data)
					if (strpos($arr_head_table[1],"|")!==false) $arr_head_table[0] .= "|";
//					echo "arr_head_table=".implode(",",$arr_head_table)."<br>";
	} // end if ($KPI_YEAR && $ORG_ID)

?>