<?php
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");
	
	if (!$f_show_full) $f_show_full = $_GET["f_show_full"];
	if (!$f_show_full) $f_show_full = $_POST["f_show_full"];
	if (!$f_show_full) $f_show_full = 1;	// 1- แสดงแบบกระจายหมด  2-แสดงตามจอภาพ

    if (!$group_open_depth) $group_open_depth = $_GET["group_open_depth"];
    if (!$group_open_depth) $group_open_depth = $_POST["group_open_depth"];
	if (!$group_open_depth) $group_open_depth = "";
    if (!$group_id) $group_id = $_GET["group_id"];
    if (!$group_id) $group_id = $_POST["group_id"];
	if (!$group_id) $group_id = "";
    if (!$auth_list) $auth_list = $_GET["auth_list"];
    if (!$auth_list) $auth_list = $_POST["auth_list"];
	if (!$auth_list) $auth_list = "";
    if (!$page_id) $page_id = $_GET["page_id"];
    if (!$page_id) $page_id = $_POST["page_id"];
	if (!$page_id) $page_id = "";

	$IMGPATH = "../images/";
	
	include("../php_scripts/user_menu.php"); 
	
	if(trim($SELECTED_PER_ID)){	  $ARRSELECTED_PER_ID=explode(",",trim($SELECTED_PER_ID));		}

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "สิทธิ์การใช้งานเมนู (".($f_show_full==1 ? "กระจายเมนูย่อยทั้งหมด" : "กระจายเมนูย่อยเฉพาะที่ต้องการ").")||ระบบ ".($page_id==1 ? "Back Office" : "Main Menu");
	$report_code = "สิทธิ์การใช้งาน";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	include ("rpt_user_menu_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

//	echo "	group_open_depth = $group_open_depth, group_id = $group_id, auth_list = $auth_list , page_id = $page_id, BACKOFFICE_MENU_DEPTH=$BACKOFFICE_MENU_DEPTH, MAIN_MENU_DEPTH=$MAIN_MENU_DEPTH<br>";
	switch($page_id){
			case 1 :		$menu_depth = $BACKOFFICE_MENU_DEPTH;
								break;
			case 2 :		$menu_depth = $MAIN_MENU_DEPTH;
								break;
	} // end 

	$arr_open_list = explode(",",$group_open_depth);

//	echo "menu_depth=$menu_depth<br>";
	if($menu_depth >= 1){
		$pdf->AutoPageBreak = false;
	
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
	//	echo "fields--".implode(",",$arr_fields)." (".count($arr_fields).")<br>";
	//	echo "$head_text1 (".count($heading_text).") | $head_width1 (".count($heading_width).") | $head_align1 | $col_function | $COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
	
		for($i=0; $i<count($arr_program_lv0[$page_id]); $i++) :
			$program_id_lv0 = $arr_program_lv0[$page_id][$i]["id"];
//			echo "1-".$arr_program_lv0[$page_id][$i]["id"]."-".$arr_program_lv0[$page_id][$i]["name"]."<br>";
			$search_str = "|".$group_id.",".$page_id.",".$program_id_lv0.",0,0,0";
			$search_offset = strpos($auth_list, $search_str) + strlen($search_str);
			$can_add_pos = $search_offset + 1;
			$can_edit_pos = $search_offset + 3;
			$can_del_pos = $search_offset + 5;
			$can_inq_pos = $search_offset + 7;
			$can_print_pos = $search_offset + 9;
			$can_confirm_pos = $search_offset + 11;
			$can_audit_pos = $search_offset + 13;
			$can_attach_pos = $search_offset + 15;

			$arr_data = (array) null;
//			if ($count1 && array_search($key_openlist, $arr_open_list)!==false) {
//				$arr_program_lv0[$page_id][$i]["flag"] = "<font color='#FF0000'><B>-</B></font>";
//			} else if ($count1) {
//				$arr_program_lv0[$page_id][$i]["flag"] = "<font color='#0000FF'><B>+</B></font>";
//			} else {
//				$arr_program_lv0[$page_id][$i]["flag"] = "<font color='#CCCCCC'><B>x</B></font>";
//			} // end if ($count1 && array_search($key_openlist, $arr_open_list)!==false)

			$sign = strip_tags($arr_program_lv0[$page_id][$i]["flag"]);
//			$arr_data[] = ($sign=="+"?"-":$sign)." ".$arr_program_lv0[$page_id][$i]["name"];   
			$arr_data[] = ($f_show_full==1 && $sign=="+"?"-":($sign=="x"?"":$sign))." ".$arr_program_lv0[$page_id][$i]["name"];   
/*			$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
			$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "เลือก" : "ไม่เลือก");*/
			$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
			$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$key_openlist = "1-".$program_id_lv0;
//			echo "[key $key_openlist in array =".array_search($key_openlist, $arr_open_list)."] ";
			if (array_search($key_openlist, $arr_open_list)!==false || $f_show_full==1) {
				if($menu_depth >= 2){
					for($j=0; $j<count($arr_program_lv1[$page_id][$program_id_lv0]); $j++){
						$program_id_lv1 = $arr_program_lv1[$page_id][$program_id_lv0][$j]["id"];
//						echo "2-".$arr_program_lv1[$page_id][$program_id_lv0][$j]["id"]."-".$arr_program_lv1[$page_id][$program_id_lv0][$j]["name"]."<br>";
						$search_str = "|".$group_id.",".$page_id.",".$program_id_lv0.",".$program_id_lv1.",0,0";
						$search_offset = strpos($auth_list, $search_str) + strlen($search_str);
						$can_add_pos = $search_offset + 1;
						$can_edit_pos = $search_offset + 3;
						$can_del_pos = $search_offset + 5;
						$can_inq_pos = $search_offset + 7;
						$can_print_pos = $search_offset + 9;
						$can_confirm_pos = $search_offset + 11;
						$can_audit_pos = $search_offset + 13;
						$can_attach_pos = $search_offset + 15;

						$arr_data = (array) null;
						$sign = strip_tags($arr_program_lv1[$page_id][$program_id_lv0][$j]["flag"]);
//						$arr_data[] = "     ".($f_show_full==1 && $sign=="+"?"-":$sign)." ".$arr_program_lv1[$page_id][$program_id_lv0][$j]["name"];
						$arr_data[] = "     ".($f_show_full==1 && $sign=="+"?"-":($sign=="x"?"":$sign))." ".$arr_program_lv1[$page_id][$program_id_lv0][$j]["name"];
/*						$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
						$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "เลือก" : "ไม่เลือก");*/
						$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
						$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");

						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
						
						$key_openlist1 = "2-".$program_id_lv1;
//						echo "[key $key_openlist1 in array =".array_search($key_openlist1, $arr_open_list)."] ";
						if (array_search($key_openlist1, $arr_open_list)!==false || $f_show_full==1) {
							if($menu_depth >= 3){
								for($x=0; $x<count($arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1]); $x++){
									$program_id_lv2 = $arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1][$x]["id"];
//									echo "3-".$arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1][$x]["name"]."<br>";
									$search_str = "|".$group_id.",".$page_id.",".$program_id_lv0.",".$program_id_lv1.",".$program_id_lv2.",0";
									$search_offset = strpos($auth_list, $search_str) + strlen($search_str);
									$can_add_pos = $search_offset + 1;
									$can_edit_pos = $search_offset + 3;
									$can_del_pos = $search_offset + 5;
									$can_inq_pos = $search_offset + 7;
									$can_print_pos = $search_offset + 9;
									$can_confirm_pos = $search_offset + 11;
									$can_audit_pos = $search_offset + 13;
									$can_attach_pos = $search_offset + 15;

									$arr_data = (array) null;
									$sign = strip_tags($arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1][$x]["flag"]);
//									$arr_data[] = "          ".($f_show_full==1 && $sign=="+"?"-":$sign)." ".$arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1][$x]["name"];
									$arr_data[] = "          ".($f_show_full==1 && $sign=="+"?"-":($sign=="x"?"":$sign))." ".$arr_program_lv2[$page_id][$program_id_lv0][$program_id_lv1][$x]["name"];
/*									$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
									$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "เลือก" : "ไม่เลือก");*/
									$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
									$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");

									$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
									if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
									
									$key_openlist2 = "3-".$program_id_lv2;
//									echo "[key $key_openlist2 in array =".array_search($key_openlist2, $arr_open_list)."] ";
									if (array_search($key_openlistd, $arr_open_list)!==false || $f_show_full==1) {
										if($menu_depth >= 4){
											for($y=0; $y<count($arr_program_lv3[$page_id][$program_id_lv0][$program_id_lv1][$program_id_lv2]); $y++){
												$program_id_lv3 = $arr_program_lv3[$page_id][$program_id_lv0][$program_id_lv1][$program_id_lv2][$y]["id"];
												$search_str = "|".$group_id.",".$page_id.",".$program_id_lv0.",".$program_id_lv1.",".$program_id_lv2.",".$program_id_lv3;
												$search_offset = strpos($auth_list, $search_str) + strlen($search_str);
												$can_add_pos = $search_offset + 1;
												$can_edit_pos = $search_offset + 3;
												$can_del_pos = $search_offset + 5;
												$can_inq_pos = $search_offset + 7;
												$can_print_pos = $search_offset + 9;
												$can_confirm_pos = $search_offset + 11;
												$can_audit_pos = $search_offset + 13;
												$can_attach_pos = $search_offset + 15;

												$arr_data = (array) null;
												$arr_data[] = "               ".$arr_program_lv3[$page_id][$program_id_lv0][$program_id_lv1][$program_id_lv2][$y]["name"];
/*												$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "เลือก" : "ไม่เลือก");
												$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "เลือก" : "ไม่เลือก");*/
												$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");
												$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.JPG*img*>" : "<*img*".$IMGPATH."checkbox_blank.JPG*img*>");

												$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
												if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
											} // end for 
										} // end if menu_depth >= 4
									} // end if (array_search($key_openlist2, $arr_open_list)!==false) loop menu_depth = 3
								} // end for 
							} // end if menu_depth >= 3
						} // end if (array_search($key_openlist1, $arr_open_list)!==false) loop menu_depth = 2
					} // end for 
				} // end if menu_depth >= 2
			} // end if (array_search($key_openlist, $arr_open_list)!==false) loop menu_depth = 1
		endfor; 
	} // end if menu_depth >= 1
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();
?>