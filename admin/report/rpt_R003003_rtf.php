<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "ORG";
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	$arr_search_condition[] = "(f.MOV_SUB_TYPE = 2)";

	$list_type_text = $ALL_REPORT_TITLE;
	include ("../report/rpt_condition3.php");
	
	include ("rpt_R003003_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R003003_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||จำนวน$PERSON_TYPE[$search_per_type]ที่ย้ายระหว่าง$ORG_TITLE ในปีงบประมาณ $search_budget_year";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0303";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from		(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_POSITIONHIS e, PER_MOVMENT f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=e.PER_ID(+) and e.MOV_CODE=f.MOV_CODE(+)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PER_GENDER, e.MOV_CODE, e.ORG_ID_3, e.POH_ORG3, e.POH_EFFECTIVEDATE
						 from		(
											(
												(
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
										) left join PER_MOVMENT f on (e.MOV_CODE=f.MOV_CODE)
											$search_condition
						 order by		a.PER_ID, e.POH_ORG3 ";
	} // end if
	if($select_org_structure==1){ 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$GRAND_TOTAL = $GRAND_TOTAL_1 = $GRAND_TOTAL_2 = 0;
	while($data = $db_dpis->get_array()){
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
		$POH_EFFECTIVEDATE = substr($data[POH_EFFECTIVEDATE], 0, 10);
		
		$NEW_ORG_ID = $data[ORG_ID_3];
		$NEW_ORG_NAME = $data[POH_ORG3];
		$NEW_ORG_SHORT = $data[ORG_SHORT];
		
		if($DPISDB=="odbc")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
							 from 		 PER_POSITIONHIS a
							 				 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
							 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="oci8")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
							 from 		 PER_POSITIONHIS a, PER_ORG b
							 where 	 a.POH_ORG3=b.ORG_NAME(+) and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$POH_EFFECTIVEDATE' 
							 order by a.POH_EFFECTIVEDATE desc ";
		elseif($DPISDB=="mysql")
			$cmd = " select 	 a.ORG_ID_3, a.POH_ORG3, b.ORG_SHORT
					 from 		 PER_POSITIONHIS a
									 left join PER_ORG b on (a.POH_ORG3=b.ORG_NAME)
					 where 	 a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$POH_EFFECTIVEDATE' 
					 order by a.POH_EFFECTIVEDATE desc ";
		if($select_org_structure==1){ 
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_ID = $data2[ORG_ID_3];
		$OLD_ORG_NAME = $data2[POH_ORG3];
		$OLD_ORG_SHORT = $data2[ORG_SHORT];

		$POH_MOV_CODE = trim($data[MOV_CODE]);

		if(($OLD_ORG_NAME && $OLD_ORG_NAME != $NEW_ORG_NAME) && ((in_array("PER_ORG", $list_type) && $search_org_name==$OLD_ORG_NAME) || !in_array("PER_ORG", $list_type))){
			$key = "$OLD_ORG_NAME:$NEW_ORG_NAME";
			if(!array_key_exists($key, $arr_content)){ 
				$arr_content[$key][old_org] = $OLD_ORG_NAME;
				$arr_content[$key][old_sho] = $OLD_ORG_SHORT;
				$arr_content[$key][new_org] = $NEW_ORG_NAME;
				$arr_content[$key][new_sho] = $NEW_ORG_SHORT;
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
	
//new*****************************************************************
//		$RTF->open_section(1); 
//		$RTF->set_font($font, 20);
//		$RTF->color("0");	// 0=BLACK
		
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
		if (!$result) echo "****** error ****** on open table for $table<br>";
			
		if($count_data){
			
	//		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$data_count = 0;
			foreach($arr_content as $key => $value){
				$NAME_1 = $arr_content[$key][old_org];
				$NAME_2 = $arr_content[$key][new_org];
				$COUNT_1 = $arr_content[$key][count_1];
				$COUNT_2 = $arr_content[$key][count_2];
				$COUNT_TOTAL = $COUNT_1 + $COUNT_2;
		
			//new format************************************************************			
				$arr_data = (array) null;
				$arr_data[] = $NAME_1;
				$arr_data[] = $NAME_2;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_TOTAL;
		
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for

		//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] = "<**1**>รวม";
			$arr_data[] = "<**1**>รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL;
			
			$data_align = array("R", "R", "R", "R", "R");
			
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}else{
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
			if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
		} // end if
		$RTF->close_tab(); 
//		$RTF->close_section(); 
	
		$RTF->display($fname);
?>