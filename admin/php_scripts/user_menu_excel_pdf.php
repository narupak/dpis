<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

//==============เพิ่ม can_pdf can_exc =======================//
//	echo "page_id=$page_id";
	if( !$page_id )	$page_id = 0;
	if( !$auth_list ) 	$auth_list = "";
	
	if( $ch_group ){
		$page_id = 0;
		$auth_list = "";
	}
	
	if( $command=="UPDATE" && $group_id ){
//		echo $auth_list."<br>";
		$arr_auth = explode("|", substr($auth_list, 1, -1));
		for($i=0; $i<count($arr_auth); $i++){
			$auth_detail = explode(",", $arr_auth[$i]);
			$cmd = " select group_id from user_privilege where group_id=$group_id and page_id=$auth_detail[1] and menu_id_lv0=$auth_detail[2] and menu_id_lv1=$auth_detail[3] and menu_id_lv2=$auth_detail[4] and menu_id_lv3=$auth_detail[5] ";
			if(!$db->send_cmd($cmd)) :
				$cmd = " insert into user_privilege (group_id,page_id,menu_id_lv0,menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,can_pdf,can_exc)
								 values ($group_id, $auth_detail[1], $auth_detail[2], $auth_detail[3], $auth_detail[4], $auth_detail[5], '$auth_detail[6]', '$auth_detail[7]', '$auth_detail[8]', '$auth_detail[9]', '$auth_detail[10]', '$auth_detail[11]', '$auth_detail[12]', '$auth_detail[13]') ";
				$db->send_cmd($cmd);
			else :
				$cmd = " update user_privilege 
								 set	can_add = '$auth_detail[6]',
								 		can_edit = '$auth_detail[7]',
										can_del = '$auth_detail[8]',
										can_inq = '$auth_detail[9]',
										can_print = '$auth_detail[10]',
										can_confirm = '$auth_detail[11]',
										can_pdf = '$auth_detail[12]',
										can_exc = '$auth_detail[13]'
								 where group_id=$group_id and page_id=$auth_detail[1] and menu_id_lv0=$auth_detail[2] and menu_id_lv1=$auth_detail[3] and menu_id_lv2=$auth_detail[4] and menu_id_lv3=$auth_detail[5] ";
				$db->send_cmd($cmd);
			endif;
//			$db->show_error();
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขสิทธิ์กลุ่มผู้ใช้งาน [$group_name_th]");
	}
		
	if( $command=="COPY" && trim($SELECTED_PER_ID) ){
//		echo $auth_list."<br>";
		$ARRSELECTED_PER_ID=explode(",",$SELECTED_PER_ID);
		for($j=0; $j < count($ARRSELECTED_PER_ID); $j++) {
				//อัพเดทพาสเวิร์ด 1234
/*				
				$setpwd = md5(1234);
				$cmd = " update user_detail set password = '$setpwd' where group_id=$ARRSELECTED_PER_ID[$j] ";
				$db->send_cmd($cmd);
*/				
				//echo "$cmd<br>";
				//------------------
			$arr_auth = explode("|", substr($auth_list, 1, -1));
			for($i=0; $i<count($arr_auth); $i++){
				$auth_detail = explode(",", $arr_auth[$i]);
				$cmd = " select group_id from user_privilege where group_id=$ARRSELECTED_PER_ID[$j] and page_id=$auth_detail[1] and menu_id_lv0=$auth_detail[2] and menu_id_lv1=$auth_detail[3] and menu_id_lv2=$auth_detail[4] and menu_id_lv3=$auth_detail[5] ";
				if(!$db->send_cmd($cmd)) :
					$cmd = " insert into user_privilege (group_id,page_id,menu_id_lv0,menu_id_lv1,menu_id_lv2,menu_id_lv3,can_add,can_edit,can_del,can_inq,can_print,can_confirm,can_pdf,can_exc)
									 values ($ARRSELECTED_PER_ID[$j], $auth_detail[1], $auth_detail[2], $auth_detail[3], $auth_detail[4], $auth_detail[5], '$auth_detail[6]', '$auth_detail[7]', '$auth_detail[8]', '$auth_detail[9]', '$auth_detail[10]', '$auth_detail[11]', '$auth_detail[12]', '$auth_detail[13]') ";
					$db->send_cmd($cmd);
				else :
					$cmd = " update user_privilege 
									 set	can_add = '$auth_detail[6]',
									 		can_edit = '$auth_detail[7]',
											can_del = '$auth_detail[8]',
											can_inq = '$auth_detail[9]',
											can_print = '$auth_detail[10]',
											can_confirm = '$auth_detail[11]',
											can_pdf = '$auth_detail[12]',
											can_exc = '$auth_detail[13]'
									 where group_id=$ARRSELECTED_PER_ID[$j] and page_id=$auth_detail[1] and menu_id_lv0=$auth_detail[2] and menu_id_lv1=$auth_detail[3] and menu_id_lv2=$auth_detail[4] and menu_id_lv3=$auth_detail[5] ";
					$db->send_cmd($cmd);
				endif;
//				$db->show_error();
			}
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > คัดลอกสิทธิ์กลุ่มผู้ใช้งาน [$group_name_th]");
	}
		
	if( $group_id ){
		$cmd = " select name_th, name_en, access_list from user_group where id=$group_id ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$name_th = $data[name_th];
		$name_en = $data[name_en];
		$page_access = $data[access_list];
		
		$cmd = "select id, name_th from user_section where id in (".substr($page_access,1,-1).")";
		$db->send_cmd($cmd);
		$i=-1;
		while($data=$db->get_array()){
			$data = array_change_key_case($data, CASE_LOWER);
			$i++;
			if(!$page_id) $page_id = $data[id];
			$arr_section[$i]["id"] = $data[id];
			$arr_section[$i]["name"] = $data[name_th];
		}		
		
		$init_auth = "";
		list_program(1, " backoffice_menu_bar_lv0 "," backoffice_menu_bar_lv1 "," backoffice_menu_bar_lv2 "," backoffice_menu_bar_lv3 "," flag_show ", $BACKOFFICE_MENU_DEPTH);
		list_program(2, " main_menu_bar_lv0 "," main_menu_bar_lv1 "," "," "," flag_show ", $MAIN_MENU_DEPTH);
//		list_program(3, " main_menu_bar_lv0 "," main_menu_bar_lv1 "," "," "," flag_show_2 ", $MAIN_MENU_DEPTH);
		if($init_auth) $init_auth = "|".$init_auth."|";
		
		if( !$auth_list ){
			for($k=0; $k<count($arr_section); $k++){
				$id_page = $arr_section[$k]["id"];
				switch($id_page){
					case 1 :		$menu_depth = $BACKOFFICE_MENU_DEPTH;
										break;
					case 2 :		$menu_depth = $MAIN_MENU_DEPTH;
										break;
				} // end 
				
				if($menu_depth >= 1){			
					for($i=0; $i<count($arr_program_lv0[$id_page]); $i++) :
						$program_id_lv0 = $arr_program_lv0[$id_page][$i]["id"];
						$cmd = " 	select 		can_add, can_edit, can_del, can_inq, can_print, can_confirm, can_pdf, can_exc 
								 			from 		user_privilege
								 			where 	group_id=$group_id and page_id=$id_page and 
									   						menu_id_lv0=$program_id_lv0 and menu_id_lv1=0 and menu_id_lv2=0 and menu_id_lv3=0 ";
						$db->send_cmd($cmd);
//						$db->show_error();
						$data = $db->get_array();
						$data = array_change_key_case($data, CASE_LOWER);
						if($auth_list) $auth_list .= "|";
						$auth_list .= $group_id.",".$id_page.",".$program_id_lv0.",0,0,0";
						$auth_list .= ",".(($data[can_add])?$data[can_add]:"N");
						$auth_list .= ",".(($data[can_edit])?$data[can_edit]:"N");
						$auth_list .= ",".(($data[can_del])?$data[can_del]:"N");
						$auth_list .= ",".(($data[can_inq])?$data[can_inq]:"N");
						$auth_list .= ",".(($data[can_print])?$data[can_print]:"N");
						$auth_list .= ",".(($data[can_confirm])?$data[can_confirm]:"N");
						$auth_list .= ",".(($data[can_pdf])?$data[can_pdf]:"N");
						$auth_list .= ",".(($data[can_exc])?$data[can_exc]:"N");
		
						if($menu_depth >= 2){
							for($j=0; $j<count($arr_program_lv1[$id_page][$program_id_lv0]); $j++){
								$program_id_lv1 = $arr_program_lv1[$id_page][$program_id_lv0][$j]["id"];
								$cmd = " 	select 		can_add, can_edit, can_del, can_inq, can_print, can_confirm , can_pdf, can_exc 
										 			from 		user_privilege
										 			where 	group_id=$group_id and page_id=$id_page and 
											   						menu_id_lv0=$program_id_lv0 and menu_id_lv1=$program_id_lv1 and 
																	menu_id_lv2=0 and menu_id_lv3=0 ";
								$db->send_cmd($cmd);
//								$db->show_error();
								$data = $db->get_array();
								$data = array_change_key_case($data, CASE_LOWER);
								if($auth_list) $auth_list .= "|";
								$auth_list .= $group_id.",".$id_page.",".$program_id_lv0.",".$program_id_lv1.",0,0";
								$auth_list .= ",".(($data[can_add])?$data[can_add]:"N");
								$auth_list .= ",".(($data[can_edit])?$data[can_edit]:"N");
								$auth_list .= ",".(($data[can_del])?$data[can_del]:"N");
								$auth_list .= ",".(($data[can_inq])?$data[can_inq]:"N");
								$auth_list .= ",".(($data[can_print])?$data[can_print]:"N");												
								$auth_list .= ",".(($data[can_confirm])?$data[can_confirm]:"N");				
								$auth_list .= ",".(($data[can_pdf])?$data[can_pdf]:"N");												
								$auth_list .= ",".(($data[can_exc])?$data[can_exc]:"N");									

								if($menu_depth >= 3){
									for($x=0; $x<count($arr_program_lv2[$id_page][$program_id_lv0][$program_id_lv1]); $x++) :
										$program_id_lv2 = $arr_program_lv2[$id_page][$program_id_lv0][$program_id_lv1][$x]["id"];
										$cmd = " 	select 		can_add, can_edit, can_del, can_inq, can_print, can_confirm , can_pdf, can_exc 
												 			from 		user_privilege
												 			where 	group_id=$group_id and page_id=$id_page and 
													   						menu_id_lv0=$program_id_lv0 and menu_id_lv1=$program_id_lv1 and menu_id_lv2=$program_id_lv2 and menu_id_lv3=0 ";
										$db->send_cmd($cmd);
//										$db->show_error();
										$data = $db->get_array();
										$data = array_change_key_case($data, CASE_LOWER);
										if($auth_list) $auth_list .= "|";
										$auth_list .= $group_id.",".$id_page.",".$program_id_lv0.",".$program_id_lv1.",".$program_id_lv2.",0";
										$auth_list .= ",".(($data[can_add])?$data[can_add]:"N");
										$auth_list .= ",".(($data[can_edit])?$data[can_edit]:"N");
										$auth_list .= ",".(($data[can_del])?$data[can_del]:"N");
										$auth_list .= ",".(($data[can_inq])?$data[can_inq]:"N");
										$auth_list .= ",".(($data[can_print])?$data[can_print]:"N");												
										$auth_list .= ",".(($data[can_confirm])?$data[can_confirm]:"N");			
										$auth_list .= ",".(($data[can_pdf])?$data[can_pdf]:"N");												
										$auth_list .= ",".(($data[can_exc])?$data[can_exc]:"N");										

										if($menu_depth >= 4){
											for($y=0; $y<count($arr_program_lv3[$id_page][$program_id_lv0][$program_id_lv1][$program_id_lv2]); $y++){
												$program_id_lv3 = $arr_program_lv3[$id_page][$program_id_lv0][$program_id_lv1][$program_id_lv2][$y]["id"];
												$cmd = " 	select 		can_add, can_edit, can_del, can_inq, can_print, can_confirm , can_pdf, can_exc 
																	from 		user_privilege
																	where 	group_id=$group_id and page_id=$id_page and 
																					menu_id_lv0=$program_id_lv0 and menu_id_lv1=$program_id_lv1 and menu_id_lv2=$program_id_lv2 and menu_id_lv3=$program_id_lv3
															  ";
												$db->send_cmd($cmd);
//												$db->show_error();
												$data = $db->get_array();
												$data = array_change_key_case($data, CASE_LOWER);
												if($auth_list) $auth_list .= "|";
												$auth_list .= $group_id.",".$id_page.",".$program_id_lv0.",".$program_id_lv1.",".$program_id_lv2.",".$program_id_lv3;
												$auth_list .= ",".(($data[can_add])?$data[can_add]:"N");
												$auth_list .= ",".(($data[can_edit])?$data[can_edit]:"N");
												$auth_list .= ",".(($data[can_del])?$data[can_del]:"N");
												$auth_list .= ",".(($data[can_inq])?$data[can_inq]:"N");
												$auth_list .= ",".(($data[can_print])?$data[can_print]:"N");												
												$auth_list .= ",".(($data[can_confirm])?$data[can_confirm]:"N");			
												$auth_list .= ",".(($data[can_pdf])?$data[can_pdf]:"N");												
												$auth_list .= ",".(($data[can_exc])?$data[can_exc]:"N");							
											} // for loop
										} // end if menu_depth >= 4

									endfor;
								} // end if menu_depth >= 3

							} // for loop
						} // end if menu_depth >= 2
							
					endfor;
				} // end if menu_depth >= 1
				
			} // for loop
			if($auth_list) $auth_list = "|".$auth_list."|";
		}
		if(!$auth_list) $auth_list = $init_auth;
//		echo $auth_list."<br>";
	}
	
//	echo $init_auth."<br>".$auth_list;
	
	function list_program($page_id, $table1, $table2, $table3, $table4, $flag_show, $menu_depth){
		global $db_host, $db_name, $db_user, $db_pwd;
		global $CATE_FID, $group_id, $init_auth;
		global $arr_program_lv0, $arr_program_lv1, $arr_program_lv2, $arr_program_lv3;
		
		$db_lv0 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		$db_lv1 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		$db_lv2 = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		$db_lv3 = new connect_db($db_host, $db_name, $db_user, $db_pwd);

		if($menu_depth>=1){
			$cmd = " select menu_id, menu_label from $table1 where fid=$CATE_FID and langcode='TH' and $flag_show='S' order by menu_order";
			$db_lv0->send_cmd($cmd);
//			$db_lv0->show_error();
			$i = -1;
			while( $data_lv0=$db_lv0->get_array() ) :
				$data_lv0 = array_change_key_case($data_lv0, CASE_LOWER);
				$i++;
				$arr_program_lv0[$page_id][$i]["id"] = $data_lv0[menu_id];
				$arr_program_lv0[$page_id][$i]["name"] = $data_lv0[menu_label];
				if($init_auth) $init_auth .= "|";
				$init_auth .= $group_id.",".$page_id.",".$data_lv0[menu_id].",0,0,0,N,N,N,N,N,N";
				
				if($menu_depth>=2){
					$cmd = " select menu_id, menu_label from $table2 where fid=$CATE_FID and langcode='TH' and flag_show='S' and parent_id_lv0=".$data_lv0[menu_id]." order by menu_order ";
					$db_lv1->send_cmd($cmd);
//					$db_lv1->show_error();
					$j = -1;
					while( $data_lv1=$db_lv1->get_array() ){
						$data_lv1 = array_change_key_case($data_lv1, CASE_LOWER);
						$j++;
						$arr_program_lv1[$page_id][$data_lv0[menu_id]][$j]["id"] = $data_lv1[menu_id];
						$arr_program_lv1[$page_id][$data_lv0[menu_id]][$j]["name"] = $data_lv1[menu_label];
						if($init_auth) $init_auth .= "|";
						$init_auth .= $group_id.",".$page_id.",".$data_lv0[menu_id].",".$data_lv1[menu_id].",0,0,N,N,N,N,N,N";

						if($menu_depth>=3){
							$cmd = " select menu_id, menu_label from $table3 where fid=$CATE_FID and langcode='TH' and flag_show='S' 
							and parent_id_lv0=".$data_lv0[menu_id]." and parent_id_lv1=".$data_lv1[menu_id]." order by menu_order ";
							$db_lv2->send_cmd($cmd);
	//						$db_lv2->show_error();
							$k = -1;
							while( $data_lv2=$db_lv2->get_array() ) :
								$data_lv2 = array_change_key_case($data_lv2, CASE_LOWER);
								$k++;
								$arr_program_lv2[$page_id][$data_lv0[menu_id]][$data_lv1[menu_id]][$k]["id"] = $data_lv2[menu_id];
								$arr_program_lv2[$page_id][$data_lv0[menu_id]][$data_lv1[menu_id]][$k]["name"] = $data_lv2[menu_label];
								if($init_auth) $init_auth .= "|";
								$init_auth .= $group_id.",".$page_id.",".$data_lv0[menu_id].",".$data_lv1[menu_id].",".$data_lv2[menu_id].",0,N,N,N,N,N,N";

								if($menu_depth>=4){
									$cmd = " select menu_id, menu_label from $table4 where fid=$CATE_FID and langcode='TH' and flag_show='S' and parent_id_lv0=".$data_lv0[menu_id]." and parent_id_lv1=".$data_lv1[menu_id]." and parent_id_lv2=".$data_lv2[menu_id]." order by menu_order ";
									//echo $cmd."<br>";
									$db_lv3->send_cmd($cmd);
//									$db_lv3->show_error();
									$l = -1;
									while( $data_lv3=$db_lv3->get_array() ){
										$data_lv3 = array_change_key_case($data_lv3, CASE_LOWER);
										$l++;
										$arr_program_lv3[$page_id][$data_lv0[menu_id]][$data_lv1[menu_id]][$data_lv2[menu_id]][$l]["id"] = $data_lv3[menu_id];
										$arr_program_lv3[$page_id][$data_lv0[menu_id]][$data_lv1[menu_id]][$data_lv2[menu_id]][$l]["name"] = $data_lv3[menu_label];
										if($init_auth) $init_auth .= "|";
										$init_auth .= $group_id.",".$page_id.",".$data_lv0[menu_id].",".$data_lv1[menu_id].",".$data_lv2[menu_id].",".$data_lv3[menu_id].",N,N,N,N,N,N";
									} // end while
								} // end if menu_depth >= 4
								
							endwhile;
						} // end if menu_depth >= 3
						
					} // end while
				} // end if menu_depth >= 2
				
			endwhile;
		} // end if	 menu_depth >= 1
	} // end function
?>