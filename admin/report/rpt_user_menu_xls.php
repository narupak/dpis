<?php
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");
	
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
	
	include ("rpt_user_menu_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	include("../php_scripts/user_menu.php"); 
	
	if(trim($SELECTED_PER_ID)){	  $ARRSELECTED_PER_ID=explode(",",trim($SELECTED_PER_ID));		}

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	
	$company_name = "";
	$report_title = "สิทธิ์การใช้งานเมนู (".($f_show_full==1 ? "กระจายเมนูย่อยทั้งหมด" : "กระจายเมนูย่อยเฉพาะที่ต้องการ").")||ระบบ ".($page_id==1 ? "Back Office" : "Main Menu");
	$report_code = "สิทธิ์การใช้งาน";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = $heading_text;
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = $heading_align;
		$ws_width = $heading_width;
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
	
	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_colmerge_line1;
		global $ws_border_line1, $ws_fontfmt_line1;
		global $ws_headalign_line1, $ws_width;
		
		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		$colshow_cnt = $colseq;

		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function

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
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("","","","","","","","","");
			$wsdata_align_1 = array("L","C","C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0);
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
	
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
			$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			$pgrp="";
			for($k=0; $k < count($arr_column_map); $k++) {
				if ($arr_column_sel[$arr_column_map[$k]]==1) {
					if ($arr_aggreg_data[$k]) $ndata = $arr_aggreg_data[$k];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$k]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$k]], $wsdata_colmerge_1[$arr_column_map[$k]], $pgrp, ($colseq == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
					if ($img) {
//						echo "1..$img<br>";
						$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
//						$worksheet->insert_bitmap($xlsRow, $colseq, $img, (($ws_width[$arr_column_map[$k]]/2)-0), 6, 1, 0.5);
						$worksheet->insert_bitmap($xlsRow, $colseq, $img, 50, 3, 1, 0.5);
					} else {
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
					}
					$colseq++;
				}
			}

			$key_openlist = "1-".$program_id_lv0;
//			echo "[key $key_openlist in array =".array_search($key_openlist, $arr_open_list)."] ";
			if (array_search($key_openlist, $arr_open_list)!==false || $f_show_full==1) {	//  full
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
						$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
						$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");

						$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
							
						$xlsRow++;
						$colseq=0;
						$pgrp="";
						for($k=0; $k < count($arr_column_map); $k++) {
							if ($arr_column_sel[$arr_column_map[$k]]==1) {
								if ($arr_aggreg_data[$k]) $ndata = $arr_aggreg_data[$k];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
								else $ndata = $arr_data[$arr_column_map[$k]];
								$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$k]], $wsdata_colmerge_1[$arr_column_map[$k]], $pgrp, ($colseq == $colshow_cnt-1)));
								$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
								if ($img) {
//									echo "2..$img<br>";
									$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
//									$worksheet->insert_bitmap($xlsRow, $colseq, $img, (($ws_width[$arr_column_map[$k]]/2)-0), 6, 1, 0.5);
									$worksheet->insert_bitmap($xlsRow, $colseq, $img, 50, 3, 1, 0.5);
								} else {
									$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
								}
								$colseq++;
							}
						}
						
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
									$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
									$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");

									$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
										
									$xlsRow++;
									$colseq=0;
									$pgrp="";
									for($k=0; $k < count($arr_column_map); $k++) {
										if ($arr_column_sel[$arr_column_map[$k]]==1) {
											if ($arr_aggreg_data[$k]) $ndata = $arr_aggreg_data[$k];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
											else $ndata = $arr_data[$arr_column_map[$k]];
											$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$k]], $wsdata_colmerge_1[$arr_column_map[$k]], $pgrp, ($colseq == $colshow_cnt-1)));
											$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
											if ($img) {
												// echo "1..$img<br>";
												$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
//												$worksheet->insert_bitmap($xlsRow, $colseq, $img, (($ws_width[$arr_column_map[$k]]/2)-0), 6, 1, 0.5);
												$worksheet->insert_bitmap($xlsRow, $colseq, $img, 50, 3, 1, 0.5);
											} else {
												$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
											}
											$colseq++;
										}
									}
									
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
												$arr_data[] = ($auth_list[$can_inq_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_add_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_edit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_del_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_print_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_confirm_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_audit_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
												$arr_data[] = ($auth_list[$can_attach_pos]=="Y" ? "<*img*".$IMGPATH."checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");

												$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
													
												$xlsRow++;
												$colseq=0;
												$pgrp="";
												for($k=0; $k < count($arr_column_map); $k++) {
													if ($arr_column_sel[$arr_column_map[$k]]==1) {
														if ($arr_aggreg_data[$k]) $ndata = $arr_aggreg_data[$k];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
														else $ndata = $arr_data[$arr_column_map[$k]];
														$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$k]], $wsdata_colmerge_1[$arr_column_map[$k]], $pgrp, ($colseq == $colshow_cnt-1)));
														$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
														if ($img) {
															// echo "1..$img<br>";
															$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
//															$worksheet->insert_bitmap($xlsRow, $colseq, $img, (($ws_width[$arr_column_map[$k]]/2)-0), 6, 1, 0.5);
															$worksheet->insert_bitmap($xlsRow, $colseq, $img, 50, 3, 1, 0.5);
														} else {
															$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
														}
														$colseq++;
													}
												}
												
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
	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>