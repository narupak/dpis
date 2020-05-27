<?
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "e.PL_CODE=f.PL_CODE";
		$pl_code = "e.PL_CODE";
		$line_code = "e.PL_CODE";
		$pl_name = "f.PL_NAME";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "e.PN_CODE=f.PN_CODE";
		$pl_code = "e.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "e.EP_CODE=f.EP_CODE";
		$pl_code = "e.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "e.TP_CODE=f.TP_CODE";
		$pl_code = "e.TP_CODE";
		$pl_name = "f.TP_NAME";
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "LINE";
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("LINE"); 

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	$arr_search_condition[] = "(g.MOV_SUB_TYPE = 2)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($export_type=="report"){
		include ("../report/rpt_condition3.php");
	}else if($export_type=="graph"){
		include ("../../admin/report/rpt_condition3.php");	//เงื่อนไขที่ต้องการแสดงผล
	}	
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่ย้ายระหว่างสายงาน ในปีงบประมาณ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0304";
	include ("rpt_R003004_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R003004_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='P';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from		(	
											(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on $line_join
										) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition
						 order by		$pl_code ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, $line_table f, PER_MOVMENT g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=e.PER_ID(+) and $line_join(+) and e.MOV_CODE=g.MOV_CODE(+)
											$search_condition
						 order by		$pl_code ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, $pl_code as PL_CODE, $pl_name as PL_NAME, e.POH_EFFECTIVEDATE
						 from		(
											(
												(
													(
														PER_PERSONAL a 
														left join $position_table b on $position_join 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on $line_join
										) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition
						 order by		$pl_code ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
	//$db_dpis->show_error();
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = 0;
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$POH_EFFECTIVEDATE = substr($data[POH_EFFECTIVEDATE], 0, 10);
		$NEW_PL_CODE = trim($data[PL_CODE]);
		$NEW_PL_NAME = $data[PL_NAME];
		
		if($DPISDB=="odbc")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e
											 left join $line_table f on $line_join
							 where 	 e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="oci8")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e, $line_table f
							 where 	 $line_join(+) and e.PER_ID=$PER_ID and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="mysql")
			$cmd = " select 	 $pl_code as PL_CODE, $pl_name as PL_NAME
							 from 		 PER_POSITIONHIS e
											 left join $line_table f on $line_join
							 where 	 e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by e.POH_EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
//		echo($cmd."<br><br>");
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_PL_CODE = trim($data2[PL_CODE]);
		$OLD_PL_NAME = $data2[PL_NAME];
		
		$POH_MOV_CODE = trim($data[MOV_CODE]);
		if(($OLD_PL_NAME && $OLD_PL_NAME != $NEW_PL_NAME) && ((in_array("PER_LINE", $list_type) && $search_per_type==1 && $search_pl_code==$OLD_PL_CODE) || (in_array("PER_LINE", $list_type) && $search_per_type==2 && $search_pn_code==$OLD_PL_CODE) ||  (in_array("PER_LINE", $list_type) && $search_per_type==3 && $search_ep_code==$OLD_PL_CODE) || !in_array("PER_LINE", $list_type)) || (in_array("PER_LINE", $list_type) && $search_per_type==4 && $search_tp_code==$OLD_PL_CODE)){
			$key = "$OLD_PL_NAME:$NEW_PL_NAME";
			if(!array_key_exists($key, $arr_content)){ 
				$arr_content[$key][old_pl] = $OLD_PL_NAME;
				$arr_content[$key][new_pl] = $NEW_PL_NAME;
				$arr_content[$key][count_1] = 0;
				$arr_content[$key][count_2] = 0;
			} // end if

			if($PER_GENDER==1){ 
				$arr_content[$key][count_1]++;
				$GRAND_TOTAL_1++;
			}elseif($PER_GENDER==2){ 
				$arr_content[$key][count_2]++;
				$GRAND_TOTAL_2++;
			} // end if
		} // end if
	} // end while

	ksort($arr_content);
	$GRAND_TOTAL = $GRAND_TOTAL_1 + $GRAND_TOTAL_2;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = count($arr_content);
	
	if($export_type=="report"){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

	    if($count_data){
			
	//		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_count = 0;
			foreach($arr_content as $key => $value){
				$NAME_1 = $arr_content[$key][old_pl];
				$NAME_2 = $arr_content[$key][new_pl];
				$COUNT_1 = $arr_content[$key][count_1];
				$COUNT_2 = $arr_content[$key][count_2];
				$COUNT_TOTAL = $COUNT_1 + $COUNT_2;

            	$arr_data = (array) null;
				$arr_data[] = $NAME_1;
				$arr_data[] = $NAME_2;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_TOTAL;
		
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
			if (!$FLAG_RTF)
				$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด			

			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = "";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL;
			
			$data_align = array("L", "R", "R", "R", "R");

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			if ($FLAG_RTF)
				$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			else
				$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} // end if
		if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
	
			$RTF->display($fname);
		} else {
			$pdf->close_tab(""); 

			$pdf->close();
			$pdf->Output();	
		}
	}else if($export_type=="graph"){//if($export_type=="report"){
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
		$arr_content_map = array("name1","name2","count_1","count_2","count_total"); // ชื่อ column ใน content ที่ map ให้ตรงกับ head pdf
		$arr_series_caption = array("สายงานเดิม","สายงานใหม่","ชาย","หญิง","รวม"); // หัวที่ต้องการเฉพาะออก กราฟ map ให้ตรงกับ head ของ pdf
		sort($arr_key);
		$arr_content_key = array_keys($arr_content);//print_r($arr_content_key);
		$arr_categories = array();
		$arr_series_caption_list = array(); 
		$f_first = true;
		$arr_grand_total = (array) null;
		for($i=0;$i<count($arr_content);$i++){
//			if($arr_content[$i][type]==$arr_rpt_order[0]){
				if($short_name=="y"){
					$old_pl_name = $arr_content[$key][old_pl_sho];
					$new_pl_name = $arr_content[$key][new_pl_sho];
				}else{
					$old_pl_name = $arr_content[$key][old_pl];
					$new_pl_name = $arr_content[$key][new_pl];
				}
				$arr_categories[$i] = trim(str_replace("/"," ",$old_pl_name." / ".$new_pl_name));
				$cntseq=0;
				for($j = 0; $j < count($arr_content_map); $j++) {
					if ($arr_column_sel[$arr_column_map[$j]]==1 && strpos($arr_content_map[$arr_column_map[$j]],"count")!==false) {
						$arr_series_caption_data[$cntseq][] = $arr_content[$i][$arr_content_map[$arr_column_map[$j]]];
						if ($f_first) $arr_series_caption_list[] = $arr_series_caption[$arr_column_map[$j]];
//						if ($f_first) echo "caption (j:$j)=".$arr_series_caption[$arr_content_map[$arr_column_map[$j]]]."  contentname=".$arr_content_map[$arr_column_map[$j]]."  mapseq=".$arr_column_map[$j]."<br>";
						$arr_grand_total[$cntseq] = ${"GRAND_TOTAL_".($arr_column_map[$j])};
						$cntseq++;
					}
				}
				$f_first=false;	// check สำหรับรอบแรกเท่านั้น
//			}//if($arr_content[$i][type]==$arr_rpt_order[0]){
		}//for($i=0;$i<count($arr_content);$i++){
/*		foreach($arr_content as $key => $value){
			if($short_name=="y"){
				$old_pl_name = $arr_content[$key][old_pl_sho];
				$new_pl_name = $arr_content[$key][new_pl_sho];
				}else{
				$old_pl_name = $arr_content[$key][old_pl];
				$new_pl_name = $arr_content[$key][new_pl];
				}
			$arr_categories[$key] = trim(str_replace("/"," ",$old_pl_name." / ".$new_pl_name));
			$arr_series_caption_data[0][] = $arr_content[$key][count_1];
			$arr_series_caption_data[1][] = $arr_content[$key][count_2];
		}*/
	//	echo "<pre>"; print_r($arr_series_caption_data); echo "</pre>";
//		$arr_series_list[0] = implode(";", $arr_series_caption_data[0]).";$GRAND_TOTAL_1";
//		$arr_series_list[1] = implode(";", $arr_series_caption_data[1]).";$GRAND_TOTAL_2";
		$series_caption_list = implode(";",$arr_series_caption_list);
//		echo "count (arr_series_caption_data)=".count($arr_series_caption_data)."<br>";
		for($j=0;$j<count($arr_series_caption_data);$j++){
			$arr_series_list[$j] = implode(";", $arr_series_caption_data[$j]).";".$arr_grand_total[$j];
		}
		
		$chart_title = trim(str_replace("|"," ",$report_title));
		$chart_subtitle = $company_name;
		if(!$setWidth){ $setWidth = "$GRAPH_WIDE";}else{ $setWidth = "800";}
		if(!$setHeight){ $setHeight = "$GRAPH_HIGH";}else{$setHeight = "600";}
		$selectedFormat = "SWF";
//		$series_caption_list = "ชาย;หญิง";
		$categories_list = implode(";", $arr_categories).";รวม";
		if(strtolower($graph_type)=="pie"){
			$series_list = $GRAND_TOTAL_1.";".$GRAND_TOTAL_2;
		}else{
			$series_list = implode("|", $arr_series_list);
		}
	//echo($series_list);
		switch( strtolower($graph_type) ){
			case "column" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Column/style/column.scs";
				break;
			case "bar" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Bar/style/bar.scs";
				break;
			case "line" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Line/style/line.scs";
				break;
			case "pie" :
				$style = $_SERVER['DOCUMENT_ROOT']."/graph/styles/Pie/style/pie.scs";
				break;
		} //switch( strtolower($graph_type) ){
	}//}else if($export_type=="graph"){
?>