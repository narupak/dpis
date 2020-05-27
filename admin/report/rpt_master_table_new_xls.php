<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rpt_function.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================

	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	
	include ("rpt_master_table_new_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	$report_code = "rpt_master_".$form_part_name."_xls";

	$workbook = new writeexcel_workbook($fname);
	if ($table=="PER_GROUP_N") {
		$worksheet = &$workbook->addworksheet("ประเภทตำแหน่ง(ใหม่)");
	} else {
		if (strlen($report_title) >= 32) 
			$worksheet = &$workbook->addworksheet("Sheet1");
		else
			$worksheet = &$workbook->addworksheet("$report_title");
	}
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//	
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
	$ws_width = (array) null;
	$ws_head_line1 = (array) null;
	$ws_colmerge_line1 = (array) null;
	$ws_border_line1 = (array) null;
	$ws_fontfmt_line1 = (array) null;
	$ws_headalign_line1 = (array) null;
	for($i = 0; $i < count($heading_text); $i++) {
		$ws_w = floor($heading_width[$i]/2);	// โดยประมาณ ความกว้างใน excel จะกว้างกว่า ใน pdf 2 เท่า ที่ตัวเลขชุดเดียวกัน
		$ws_width[] = ($ws_w > 5 ? $ws_w : $heading_width[$i]);	// ถ้ามี column ไหน <= 5 ไม่ต้องหาร 2
		$ws_head_line1[] = $heading_text[$i];
		$ws_colmerge_line1[] = 0;
		$ws_border_line1[] = "TLBR";
		$ws_fontfmt_line1[] = "B";
		$ws_headalign_line1[] = "C";
	}
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
	
	function print_header($xlsRow){
		global $worksheet;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_colmerge_line1, $ws_border_line1;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop กำหนดความกว้างของ column
		$colshow_cnt = 0;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
				$colshow_cnt++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//				echo "$border; $merge<br>";
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
//		echo "xlsRow=$xlsRow HEAD<br>";
/*
		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 90);
		$worksheet->set_column(2, 2, 10);

		$worksheet->write($xlsRow, 0, "รหัส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
*/
	} // end if

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
		endfor;
	} // end if

//	echo "table=$table sort_by=$sort_by sort_type=$sort_type arr=".$SortType."<br>";
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;

//	echo "search_condition=$search_condition<br>";
/*
    for($i=0; $i < count($arr_col_search); $i++) {
//    	echo "$i-->".$arr_col_search[$i]."<br>";
		$sub_col_search = explode(",",$arr_col_search[$i]);
        $arr_sub_condition = (array) null;
        for($j=0; $j < count($sub_col_search); $j++) {
//        	echo "search_".$arr_fields[$sub_col_search[0]]."=".${"search_".$arr_fields[$sub_col_search[0]]}."  arr_c_srch_cond=".$arr_c_srch_cond[$i]."<br>";
		  	if(trim( ${"search_".$arr_fields[$sub_col_search[0]]} ))
            	if ($arr_c_srch_cond[$i] == "%%")
	            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." like '%".${"search_".$arr_fields[$sub_col_search[0]]}."%')";
            	else if ($arr_c_srch_cond[$i] == "%")
	            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." like '".${"search_".$arr_fields[$sub_col_search[0]]}."%')";
            	else if ($arr_c_srch_cond[$i] == "!=" || $arr_c_srch_cond[$i] == "<=" || $arr_c_srch_cond[$i] == ">=")
                	if ((strpos($arr_fldtyp[$sub_col_search[$i]],"CHAR")!==false) || (strpos($arr_fldtyp[$sub_col_search[$i]],"DATE")!==false))
		            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." ".$arr_c_srch_cond[$i]." '".${"search_".$arr_fields[$sub_col_search[0]]}."')";
					else
		            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." ".$arr_c_srch_cond[$i]." ".${"search_".$arr_fields[$sub_col_search[0]]}.")";
            	else if ($arr_c_srch_cond[$i] == "=" || $arr_c_srch_cond[$i] == "<" || $arr_c_srch_cond[$i] == ">")
                	if ((strpos($arr_fldtyp[$sub_col_search[$i]],"CHAR")!==false) || (strpos($arr_fldtyp[$sub_col_search[$i]],"DATE")!==false))
		            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." ".$arr_c_srch_cond[$i]." '".${"search_".$arr_fields[$sub_col_search[0]]}."')";
					else
		            	$arr_sub_condition[] = "(".$arr_fields[$sub_col_search[$j]]." ".$arr_c_srch_cond[$i]." ".${"search_".$arr_fields[$sub_col_search[0]]}.")";
		}
        if (count($arr_sub_condition) > 0)	$arr_search_condition[] = "(".implode(" or ",$arr_sub_condition).")";
	}
// 	if(trim($search_name)) $arr_search_condition[] = "($arr_fields[1] like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);
//	echo "search_condition=$search_condition<br>";
*/

	$search_condition = stripslashes($search_condition);
	
	for($ii = 0; $ii < count($tab_sort); $ii++) {
    	if ($tab_sort[$ii]==1) {
            if ($order_by==1){	//(ค่าเริ่มต้น) ลำดับที่
				if ($seq_order_index) {
	                $order_str = "".$arr_fields[$seq_order_index]." ".$SortType[$order_by].", ".$arr_fields[$key_index]." ".$SortType[$order_by];
					$report_title .= "||เรียงลำดับตาม ".$col_label[$seq_order_index].($SortType[$order_by]=="asc" ? " น้อยไปมาก" : " มากไปน้อย");
				} else {
	                $order_str = "".$arr_fields[$key_index]." ".$SortType[$order_by];
					$report_title .= "||เรียงลำดับตาม ".$col_label[$key_index].($SortType[$order_by]=="asc" ? " น้อยไปมาก" : " มากไปน้อย");
				}
			} else if ($order_by > 1){
				$order_str = "".$arr_fields[$col_map[($order_by-1)]]." ".$SortType[$order_by];
				$report_title .= "||เรียงลำดับตาม ".$col_label[$col_map[($order_by-1)]].($SortType[$order_by]=="asc" ? " น้อยไปมาก" : " มากไปน้อย");
            }
            break;
		}
    }

	// กรณี มีการป้อนค่าเพื่อการค้นหา
	if (trim($search_condition)) {
		$arr_search_condi_title = (array) null;
		for($ii = 0; $ii < count($arr_col_search); $ii++) {
			$arr_sub_co_search = explode(",", $arr_col_search[$ii]);
			if ($arr_c_srch_cond[$ii]=="<>") {
//				if (strtolower(substr($col_spec_datatype[$i],0,4))=="date") {
				if ( strpos($arr_fldtyp[$arr_sub_co_search[0]],"CHAR")!==false && $arr_fldlen[$arr_sub_co_search[0]]==19) {
					if (valid_char19_date_ddmmyyyy(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"}))!="NO" )
						$svalmin = "'".save_date(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"}))."'";
					else
						$svalmin = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"});	// ชื่อที่ตั้งใน search form จะตั้งตาม [0]
					if (valid_char19_date_ddmmyyyy(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"}))!="NO" )
						$svalmax = "'".save_date(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"}))."'";
					else
						$svalmax = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"});	// ชื่อที่ตั้งใน search form จะตั้งตาม [0]
					$showvalmin = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"});
					$showvalmax = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"});
 				} else {
					$svalmin = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"});	// ชื่อที่ตั้งใน search form จะตั้งตาม [0]
					$svalmax = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"});	// ชื่อที่ตั้งใน search form จะตั้งตาม [0]
					$showvalmin = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_min"});
					$showvalmax = trim(${"search_".$arr_fields[$arr_sub_co_search[0]]."_max"});
				}
				// แปลงค่า ตาม col_datalink ถ้ามี โดย map $arr_sub_co_search[0] ไปยัง col_map ให้ได้ index ของ col_datalink มา 
				// แล้ว ดูใน col_datalink ว่ามีค่าหรือไม่ เป็นค่าอะไร หานำมาแทนค่า sval
				$idx = array_search($arr_sub_co_search[0], $col_map);
				$sval_expmin = ""; $sval_expmax = "";
//				echo "--arr_sub_co_search[0]=".$arr_sub_co_search[0].", datalink [$idx]=".trim($col_datalink[$idx])."<br>";
				if (trim($col_datalink[$idx])) {
					$sub_dlink = explode("|",$col_datalink[$idx]);
					if ($sub_dlink[0]=="code2show") {
						for($kk = 1; $kk < count($sub_dlink); $kk++) {
							$s_sub_dlink = explode(",",$sub_dlink[$kk]);
							if ($svalmin && $svalmin == $s_sub_dlink[0]) {
								$sval_expmin = "(".$s_sub_dlink[1].")";
							}
							if ($svalmax && $svalmax == $s_sub_dlink[0]) {
								$sval_expmax = "(".$s_sub_dlink[1].")";
							}
						}
//						echo "code2show-->".$sval_exp."<br>";
					} else if ($sub_dlink[0]=="columnlink") {
						$tab = $sub_dlink[1];
						$arr_colsel = explode(",",$sub_dlink[2]);		// List ของชื่อ column ที่ต้องการ
						// หาค่า min
						$arr_output = (array) null;
						$wherecondition = $sub_dlink[5];
						$wherecondition = str_replace("var1", trim($svalmin), $wherecondition);
						$sql = " select  ".$sub_dlink[2]." from ".$tab.($wherecondition ? " where ".$wherecondition : "");
						$db_dpis1->send_cmd($sql);
						$data1 = $db_dpis1->get_array();
//						echo "sql=$sql<br>";
						for($jj=0; $jj < count($arr_colsel); $jj++) {	// loop ตาม column ที่ต้องการ select เข้ามา
							$arr_output[] = $data1[$arr_colsel[$jj]];	// select column move to output
//							echo ">>output=".$arr_output[$jj]." | ".$arr_fields[$col_map[$ii]]."<br>";
						}
						if (count($arr_output) > 0) $sval_expmin = "(".implode(",",$arr_output).")";
						// หาค่า max
						$arr_output = (array) null;
						$wherecondition = $sub_dlink[5];
						$wherecondition = str_replace("var1", trim($svalmax), $wherecondition);
						$sql = " select  ".$sub_dlink[2]." from ".$tab.($wherecondition ? " where ".$wherecondition : "");
						$db_dpis1->send_cmd($sql);
						$data1 = $db_dpis1->get_array();
//						echo "sql=$sql<br>";
						for($jj=0; $jj < count($arr_colsel); $jj++) {	// loop ตาม column ที่ต้องการ select เข้ามา
							$arr_output[] = $data1[$arr_colsel[$jj]];	// select column move to output
//							echo ">>output=".$arr_output[$jj]." | ".$arr_fields[$col_map[$ii]]."<br>";
						}
						if (count($arr_output) > 0) $sval_expmax = "(".implode(",",$arr_output).")";
					}
				}
				// จบการหาค่า col_datalink

				$buff = (array) null;
//				echo "[$showvalmin]<>[$showvalmax]<br>";
				if ( $showvalmin && $showvalmax ) {
					for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
						$buff[] = $col_label[$arr_sub_co_search[$jj]];
					}
	                if ( (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"CHAR")!==false) || (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"DATE")!==false) )
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องอยู่ในช่วง '$showvalmin'$sval_expmin - '$showvalmax'$sval_expmin";
					else
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องอยู่ในช่วง $showvalmin$sval_expmin - $showvalmax$sval_expmin";
				} else if ( $showvalmin ) {
					for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
						$buff[] = $col_label[$arr_sub_co_search[$jj]];
					}
	                if ( (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"CHAR")!==false) || (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"DATE")!==false) )
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมีค่าตั้งแต่ '$showvalmin'$sval_expmin ขึ้นไป";
					else
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมีค่าตั้งแต่ $showvalmin$sval_expmin ขึ้นไป";
				} else if ( $showvalmax ) {
					for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
						$buff[] = $col_label[$arr_sub_co_search[$jj]];
					}
	                if ( (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"CHAR")!==false) || (strpos($arr_fldtyp[$arr_sub_co_search[$i]],"DATE")!==false) )
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมีค่าไม่เกิน '$showvalmax'$sval_expmin";
					else
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมีค่าไม่เกิน $showvalmax$sval_expmin";
				}
			} else { // เงื่อนไขไม่ใช่ <>
//				if (strtolower(substr($col_spec_datatype[$i],0,4))=="date")
				if (valid_char19_date_ddmmyyyy(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]}))!="NO" ) 
					$sval = "'".save_date(trim(${"search_".$arr_fields[$arr_sub_co_search[0]]}))."'";
				else
					$sval = ${"search_".$arr_fields[$arr_sub_co_search[0]]};	// ชื่อที่ตั้งใน search form จะตั้งตาม [0]
				$showval = ${"search_".$arr_fields[$arr_sub_co_search[0]]};
				// แปลงค่า ตาม col_datalink ถ้ามี โดย map $arr_sub_co_search[0] ไปยัง col_map ให้ได้ index ของ col_datalink มา 
				// แล้ว ดูใน col_datalink ว่ามีค่าหรือไม่ เป็นค่าอะไร หานำมาแทนค่า sval
				$idx = array_search($arr_sub_co_search[0], $col_map);
				$sval_exp = "";
//				echo "--arr_sub_co_search[0]=".$arr_sub_co_search[0].", datalink [$idx]=".trim($col_datalink[$idx])."<br>";
				if (trim($col_datalink[$idx])) {
					$sub_dlink = explode("|",$col_datalink[$idx]);
					if ($sub_dlink[0]=="code2show") {
						for($kk = 1; $kk < count($sub_dlink); $kk++) {
							$s_sub_dlink = explode(",",$sub_dlink[$kk]);
							if ($sval == $s_sub_dlink[0]) {
								$sval_exp = "(".$s_sub_dlink[1].")";
								break;
							}
						}
//						echo "code2show-->".$sval_exp."<br>";
					} else if ($sub_dlink[0]=="columnlink") {
						$tab = $sub_dlink[1];
						$arr_colsel = explode(",",$sub_dlink[2]);		// List ของชื่อ column ที่ต้องการ
						$arr_output = (array) null;
						$wherecondition = $sub_dlink[5];
						$wherecondition = str_replace("var1", trim($sval), $wherecondition);
						$sql = " select  ".$sub_dlink[2]." from ".$tab.($wherecondition ? " where ".$wherecondition : "");
						$db_dpis1->send_cmd($sql);
						$data1 = $db_dpis1->get_array();
//						echo "sql=$sql<br>";
						for($jj=0; $jj < count($arr_colsel); $jj++) {	// loop ตาม column ที่ต้องการ select เข้ามา
							$arr_output[] = $data1[$arr_colsel[$jj]];	// select column move to output
//							echo ">>output=".$arr_output[$jj]." | ".$arr_fields[$col_map[$ii]]."<br>";
						}
						if (count($arr_output) > 0) $sval_exp = "(".implode(",",$arr_output).")";
					}
				}
				// จบการหาค่า col_datalink

				if ($showval) {
					$buff = (array) null;
					if ($arr_c_srch_cond[$ii]=="%%") {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมี '$showval'$sval_exp อยู่ในประโยค";
					} else if ($arr_c_srch_cond[$ii]=="%")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมี '$showval'$sval_exp นำหน้าประโยค";
					} else if ($arr_c_srch_cond[$ii]=="=")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องเท่ากับค่า $showval$sval_exp";
					} else if ($arr_c_srch_cond[$ii]=="!=")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องไม่เท่ากับค่า $showval$sval_exp";
					} else if ($arr_c_srch_cond[$ii]=="<=")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องน้อยกว่าหรือเท่ากับค่า $showval$sval_exp";
					} else if ($arr_c_srch_cond[$ii]=="<")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องน้อยกว่าค่า $showval$sval_exp";
					} else if ($arr_c_srch_cond[$ii]==">=")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมากกว่าหรือเท่ากับค่า $showval$sval_exp";
					} else if ($arr_c_srch_cond[$ii]==">")  {
						for($jj=0; $jj < count($arr_sub_co_search); $jj++) {
							$buff[] = $col_label[$arr_sub_co_search[$jj]];
						}
						$arr_search_condi_title[] = "ค่า ".implode(", ",$buff)." ต้องมากกว่าค่า $showval$sval_exp";
					}
				}
			} // end if ($arr_c_srch_cond[$ii]=="<>")
		} // end for $ii loop
		$search_condi_title = "ตามเงื่อนไข::".implode(" และ ", $arr_search_condi_title);
		$report_title .= "||$search_condi_title";
	} // end if (trim($search_condition))

//	echo " arr_field=>".implode(",",$arr_fields)." ($seq_order_index) ($key_index)<br>";
	if($DPISDB=="odbc"){
		$cmd = "	select	*
							from		$table
											$search_condition
							order by $order_str ";
//							order by IIF(ISNULL($arr_fields[$seq_order_index]), 9999, $arr_fields[$seq_order_index]),$arr_fields[$key_index] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select	*
							from		$table
											$search_condition
							order by $order_str ";
//							order by $arr_fields[$seq_order_index],$arr_fields[$key_index] ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	*
							from		$table
											$search_condition
							order by $order_str ";
//							order by $arr_fields[$seq_order_index],$arr_fields[$key_index] ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
//			echo "xlsRow=$xlsRow arr_title [$i] = ".$arr_title[$i]."<br>";
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtSubTitle", "B", "C", "", 1));
//			echo "xlsRow=$xlsRow company_name = ".$company_name."<br>";
			$xlsRow++;
		} //if($company_name){

		print_header($xlsRow);
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_border_2 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "B";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "TLRB";
				$wsdata_border_2[] = "";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		$data_count = 1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$arr_data = (array) null;
			for($i=0; $i < count($col_map); $i++) {
//				echo ">>--$i--".$col_map[$i]."=field=>".$arr_fields[$col_map[$i]]." (".($data[$arr_fields[$col_map[$i]]]).")<br>";
				if (valid_char19_date_yyyymmdd(trim($data[$arr_fields[$col_map[$i]]]))!="NO" )
					${"temp_".$arr_fields[$col_map[$i]]} = show_date_format($data[$arr_fields[$col_map[$i]]],1);
				else
					${"temp_".$arr_fields[$col_map[$i]]} = $data[$arr_fields[$col_map[$i]]];
				if ($seq_order_index && $col_map[$i]==$seq_order_index) { // map ORDER
					if (strpos($arr_fldtyp[$col_map[$i]],"CHAR")!==false) {
//						echo "map $i = ".$col_map[$i]."..char...(".$arr_fldtyp[$col_map[$i]].")<br>";
						$arr_data[] = "'".$data[$arr_fields[$col_map[$i]]]."'";
					} else {
//						echo "map $i = ".$col_map[$i]."..numb...(".$arr_fldtyp[$col_map[$i]].")<br>";
						$arr_data[] = $data[$arr_fields[$col_map[$i]]];
					}
//					$arr_data[] = $data[$arr_fields[$col_map[$i]]]."(".$col_map[$i].")";
//					echo "seq--".$col_map[$i]."<br>";
				} else if ($active_index && $col_map[$i]==$active_index) { // map ACTIVE
					$arr_data[] = "<*img*".(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp")."*img*>";
//					echo "active--$i--".$col_map[$i]." (".($data[$arr_fields[$col_map[$i]]]).")<br>";
				} else if ($col_map[$i]==-1) { // กรณี map EDIT Column || DELETE Column
//					$arr_data[] = $data[$arr_fields[$col_map[$i]]];
//					echo "edit, del".$col_map[$i]."<br>";
				} else {
					if (!is_null($col_map[$i])) {
//						echo "col_datalink [$i]=".$col_datalink[$i]."<br>";
						if ($col_datalink[$i]) {
							$sub_dlink = explode("|",$col_datalink[$i]);
							if ($sub_dlink[0]=="code2show") {
								$arr_b = (array) null;
								for($kk = 1; $kk < count($sub_dlink); $kk++) {
									$s_sub_dlink = explode(",",$sub_dlink[$kk]);
									$arr_b[$s_sub_dlink[0]] = $s_sub_dlink[1];
								}
	//							echo "code2show=>".implode(":",$arr_b)." (".${"temp_".$arr_fields[$col_map[$i]]}.")<br>";
								$arr_data[] = $arr_b[${"temp_".$arr_fields[$col_map[$i]]}];
							} else if ($sub_dlink[0]=="columnlink") {
								$tab = $sub_dlink[1];
								$arr_colsel = explode(",",$sub_dlink[2]);		// List ของชื่อ column ที่ต้องการ
								$arr_condi = explode(",",$sub_dlink[3]);		// List ของตัวแปรที่ใช้ในเงื่อนไข
								$arr_output = explode(",",$sub_dlink[4]);		// List ของตัวแปรที่ใช้เก็บข้อมูลที่อ่านได้
								$wherecondition = $sub_dlink[5];
								for($jj=count($arr_condi)-1; $jj >= 0 ; $jj--) {
									$varname = "var".($jj+1);
									if ( valid_char19_date_ddmmyyyy(trim($$arr_condi[$jj]))!="NO" )
										$wherecondition = str_replace($varname, save_date(trim($$arr_condi[$jj])), $wherecondition);
									else
										$wherecondition = str_replace($varname, trim($$arr_condi[$jj]), $wherecondition);
								}
								$sql = " select  ".$sub_dlink[2]." from ".$tab.($wherecondition ? " where ".$wherecondition : "");
								$db_dpis1->send_cmd($sql);
								$data1 = $db_dpis1->get_array();
//								echo "sql=$sql<br>";
								for($jj=0; $jj < count($arr_colsel); $jj++) {
									if ( valid_char19_date_yyyymmdd(trim($data1[$arr_colsel[$jj]]))!="NO" )
										$arr_data[] = show_date_format($data1[$arr_colsel[$jj]],1);	// select column move to output
									else
										$arr_data[] = $data1[$arr_colsel[$jj]];	// select column move to output
//									echo ">>output=".$arr_colsel[$jj]." | ".$data1[$arr_colsel[$jj]]."<br>";
								}
							}
						} else {
							if (strtolower(substr($col_spec_datatype[$col_map[$i]],0,4))=="date") {
								$date_formno = substr($col_spec_datatype[$col_map[$i]],4);
								$arr_data[] = str_replace("||","#",show_date_format(trim($data[$arr_fields[$col_map[$i]]]),($date_formno ? $date_formno : 1)));
							} else if (strpos($arr_fldtyp[$col_map[$i]],"CHAR")!==false) {
//								$arr_data[] = "'".str_replace("||","#",$data[$arr_fields[$col_map[$i]]])."'";
								if ( strpos($arr_fldtyp[$col_map[$i]],"CHAR")!==false && $arr_fldlen[$col_map[$i]]==19 && valid_char19_date_yyyymmdd(trim($data[$arr_fields[$col_map[$i]]]))!="NO" )
									$arr_data[] = str_replace("||","#",show_date_format(trim($data[$arr_fields[$col_map[$i]]]),($date_formno ? $date_formno : 1)));
								else
									$arr_data[] = str_replace("||","#",$data[$arr_fields[$col_map[$i]]]);
							} else {
								$arr_data[] = $data[$arr_fields[$col_map[$i]]];
							}
						}
					}
				}
			}
//			echo "$i-->".implode(" , ",$arr_data)." (".count($arr_data).")<br>";

			$wsdata_align = $data_align;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
//					echo "image->($img)<br>";
					if ($img) {
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
						list($width, $height, $type, $attr) = getimagesize($img);
//						echo "image->($img, $width, $height) (align [$i] =".$wsdata_align[$arr_column_map[$i]].") w=".($ws_width[$arr_column_map[$i]])."<br>";
//						$xpos = $worksheet->_size_col($ws_width[$arr_column_map[$i]])/2-($width/2);
						$xpos = $worksheet->_size_col($ws_width[$arr_column_map[$i]])/2;
						$ypos = $worksheet->_size_row($ws_width[$arr_column_map[$i]])/2-($height/2);
						$worksheet->insert_bitmap($xlsRow, $colseq, $img, $xpos, $ypos);
					} else
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
//			echo "xlsRow=$xlsRow  DATA<br>";
/*
			$xlsRow = $data_count;
			$worksheet->write_string($xlsRow, 0, $$arr_fields[0], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, $$arr_fields[1], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 2, $$arr_fields[2], 35, 4, 1, 0.8);			
*/
		} // end while

	}else{
		$arr_data = (array) null;
		$wsdata_align = (array) null;
		for($i=0; $i < count($arr_fields); $i++) {
			$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
			$wsdata_align[] = "C";
		}
		
		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));

		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // end if

	$workbook->close();

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