<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

//	echo "table=$table form_part=$form_part sort_by=$sort_by sort_type=$sort_type arr=".$SortType."<br>";
	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";
	$orientation='P';

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";

	unset($arr_fields);
	unset($arr_fldtyp);
	unset($arr_fldlen);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
//			echo ">>$i>".$field_list[$i]["name"].", ".$field_list[$i]["type"].", ".$field_list[$i]["len"]."<br>";
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
			$arr_fldtyp[] = $field_list[$i]["type"];
			$arr_fldlen[] = $field_list[$i]["len"];
		endfor;
	} // end if

	include ("rpt_master_table_new_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$report_code = "rpt_master_".$form_part_name."";

//	echo "table=$table form_part=$form_part sort_by=$sort_by sort_type=$sort_type arr=".$SortType."<br>";

	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;

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
//			echo "order_by=$order_by, order_str=$order_str, col_map=".$col_map[($order_by-1)]."<br>";
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
	//$db_dpis->show_error();
//	echo "cmd=$cmd ($count_data)<br>";

	$pdf->AutoPageBreak = false;

	$head_text1 = implode(",", $heading_text);
	if ($f_auto_fullpagePDF)  { 
		$a = adjust_column_width();
		$h_width = (array) null;
		for($ii=0; $ii < count($a); $ii++) {
			$h_width[] = (string)$a[$ii];
		}
		$head_width1 = implode(",", $h_width);
	} else {
		$head_width1 = implode(",", $heading_width);
	}
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "fields--".implode(",",$arr_fields)." (".count($arr_fields).")<br>";
//	echo "$head_text1 (".count($heading_text).") | $head_width1 (".count($heading_width).") | $head_align1 | $col_function | $COLUMN_FORMAT<br>";
	$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

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
					$arr_data[] = "<*img*".(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";
//					$arr_data[] = "<*img*".(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*> (".$col_map[$i].",".(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")." (".file_exists(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg").")";
//					echo "act--".$col_map[$i]." (".(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg").") (".file_exists(($data[$arr_fields[$col_map[$i]]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg").")<br>";
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
										$arr_data[] = trim($data1[$arr_colsel[$jj]]);	// select column move to output
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
									$arr_data[] = str_replace("||","#", trim($data[$arr_fields[$col_map[$i]]]));
							} else {
								$arr_data[] = trim($data[$arr_fields[$col_map[$i]]]);
							}
						}
					}
				}
			}
//			echo "$i-->".implode(" , ",$arr_data)." (".count($arr_data).")  align=(".implode(" , ",$data_align).")<br>";

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		} // end while
	}else{
		$arr_data = (array) null;
		for($i=0; $i < count($arr_fields); $i++) {
			$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		}

		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "b", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
	
	function adjust_column_width() {
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $heading_width, $arr_column_width;
		global $orientation;

		$sum_w = 0;
//		echo "function.. head_width=".implode(",",$heading_width)."<br>";
		for($i=0; $i < count($heading_width); $i++) {
			if ($arr_column_sel[$i]==1)
				$sum_w += (int)$heading_width[$i];
		}
//		echo "2..sum_w=$sum_w  orientation=$orientation<br>";
		$w_radio = ($sum_w > 0 ? ($orientation=="P" ? 200/$sum_w : 290/$sum_w) : 1);	// 200 = ความกว้างกระดาษ A4P, 290 = ความกว้างกระดาษ A4L
//		echo "w_space=$w_space, sum_w=$sum_w, rest_w=$rest_w, cnt_space=$cnt_space, w_each_space=$w_each_space, w_radio=$w_radio<br>";
		$arr_w = (array) null;
		$sums = 0;
		for($i=0; $i < count($heading_width); $i++) {
//			$w = -1;
//			if ($arr_column_sel[$i]==1) {
				$w = floor((int)$heading_width[$i] * $w_radio);
				$arr_w[] = $w;
//			}
			if ($arr_column_sel[$i]==1) { $sums += (int)$heading_width[$i]; $sumn += $w; }
//			echo "$i..".$heading_width[$i]."==".$w." (x $w_radio) (sums=$sums) (sumn=$sumn)<br>";
		}
		
		return $arr_w;	// array_width
	} // end function adjust_column_width

?>