<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_per_type = 1;

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(trim(f.DE_YEAR) = '$search_year')";
	if(trim($search_dc_type==1)){ 
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นสายสะพาย";
	}elseif(trim($search_dc_type==2)){
		$arr_search_condition[] = "(g.DC_TYPE=$search_dc_type)";
		$search_dc_name = "ชั้นต่ำกว่าสายสะพาย";
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(trim($search_org_id)){
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
	}
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$heading_width[0] = "70";
	$heading_width[1] = "70";

//	include ("rpt_R005006_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R005006_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "แบบ ขร 3/" . substr($search_year, -2);
	$report_title = "บัญชีรายชื่อข้าราชการผู้ขอพระราชทานเครื่องราชอิสริยาภรณ์||ประจำปี พ.ศ.$search_year||$MINISTRY_NAME||$DEPARTMENT_NAME";
	$report_code = "R0506";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}	
/*******	
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}
	}elseif($search_per_type==3){
		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_DECORDTL e, PER_DECOR f, PER_DECORATION g
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
												$search_condition
							 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 from			(
													(
														(
															(
																(
																	PER_PERSONAL a 
																	left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_DECORDTL e on (a.PER_ID=e.PER_ID)
													) inner join PER_DECOR f on (e.DE_ID=f.DE_ID)
												) inner join PER_DECORATION g on (e.DC_CODE=g.DC_CODE)
												$search_condition
							  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_GENDER, a.LEVEL_NO, 
												e.DC_CODE, g.DC_NAME, g.DC_SHORTNAME, g.DC_ORDER
							 order by		g.DC_ORDER, e.DC_CODE, a.LEVEL_NO desc ";
		}
	} // end if
********/
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if($DC_CODE != trim($data[DC_CODE])){			
			$DC_CODE = trim($data[DC_CODE]);
			$DC_NAME = trim($data[DC_NAME]);
			$DC_SHORTNAME = trim($data[DC_SHORTNAME]);

			$decorate_index = $data_count;
			$arr_content[$data_count][type] = "DECORATION";
			$arr_content[$data_count][dc_name] = "$DC_NAME";
			$arr_content[$data_count][dc_shortname] = "$DC_SHORTNAME";
			
			$data_count++;
			$data_row = 0;
		} // end if

		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PER_GENDER = $data[PER_GENDER];
		if($PER_GENDER == 1) $arr_content[$decorate_index][count_male]++;
		elseif($PER_GENDER == 2) $arr_content[$decorate_index][count_female]++;

		$data_row++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
//		$pdf->AutoPageBreak = false;
		$RTF->paragraph();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			if($REPORT_ORDER == "DECORATION"){
				if($data_count > 0){
//					if(($pdf->h - $max_y) < 50) $pdf->AddPage();
	
					$border = "";
//					$pdf->SetFont($font,'',14);
//					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					$RTF->set_table_font($font, 14);
					
//					$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
					$RTF->close_line();

//					$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//					$pdf->Cell($heading_width[1], 7, "$DC_SHORTNAME          1 - $ORDER", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell("$DC_SHORTNAME          1 - $ORDER", $heading_width[1], "center", "0");
					$RTF->close_line();

//					$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//					$pdf->Cell($heading_width[1], 7, "บุรุษ  $COUNT_MALE          สตรี $COUNT_FEMALE", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell("บุรุษ  $COUNT_MALE          สตรี $COUNT_FEMALE", $heading_width[1], "center", "0");
					$RTF->close_line();

//					$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//					$pdf->Cell($heading_width[1], 7, "รับรองถูกต้อง", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell("รับรองถูกต้อง", $heading_width[1], "center", "0");
					$RTF->close_line();

//					$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
					$RTF->close_line();

//					$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//					$pdf->Cell($heading_width[1], 7, "( $confirm_name )", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell("( $confirm_name )", $heading_width[1], "center", "0");
					$RTF->close_line();

//					$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//					$pdf->Cell($heading_width[1], 7, "$confirm_position", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell("$confirm_position", $heading_width[1], "center", "0");
					$RTF->close_line();

//					$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
					$RTF->open_line();	
					$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
					$RTF->close_line();
				} // end if
				
				$DC_NAME = $arr_content[$data_count][dc_name];
				$DC_SHORTNAME = $arr_content[$data_count][dc_shortname];
				$COUNT_MALE = $arr_content[$data_count][count_male];
				$COUNT_FEMALE = $arr_content[$data_count][count_female];

				$border = "";
//				$pdf->SetFont($font,'b',14);
//				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$RTF->set_table_font($font, 14);
	
//				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
//				$pdf->Cell(array_sum($heading_width), 7, "$DC_NAME", $border, 1, 'C', 0);
				$RTF->open_line();	
				$RTF->cell("$DC_NAME", (string)(array_sum($heading_width)), "center", "0");
				$RTF->close_line();
			}elseif($REPORT_ORDER == "CONTENT"){
				$ORDER = $arr_content[$data_count][order];
				$NAME = $arr_content[$data_count][name];

				$border = "";
//				$pdf->SetFont($font,'',14);
//				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				$RTF->set_table_font($font, 14);
	
//				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				if(($ORDER % 2) == 1){ 
					if($arr_content[($data_count + 1)][type]=="DECORATION"){
//						$pdf->Cell(array_sum($heading_width), 7, str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", $border, 0, 'C', 0);
						$max_y += 7;
						$RTF->open_line();	
						$RTF->cell(str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", (string)(array_sum($heading_width)), "center", "0");
						$RTF->close_line();
					}else{
//						$pdf->Cell($heading_width[0], 7, str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", $border, 0, 'L', 0);
						$RTF->open_line();	
						$RTF->cell(str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", $heading_width[0], "center", "0");
						$RTF->cell("", $heading_width[1], "center", "0");
						$RTF->close_line();
					} // end if
				}elseif(($ORDER % 2) == 0){
//					$pdf->x += 100;
//					$pdf->Cell($heading_width[1], 7, str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", $border, 1, 'L', 0);
//					$max_y += 7;
					$RTF->open_line();	
					$RTF->cell("", $heading_width[0], "center", "0");
					$RTF->cell(str_pad($ORDER, 3, " ", STR_PAD_LEFT)."  $NAME", $heading_width[1], "center", "0");
					$RTF->close_line();
				} // end if

//				if(($pdf->h - $max_y) < 22){ 
//					if($data_count < (count($arr_content) - 1)){
//						$pdf->AddPage();
//						$max_y = $pdf->y;
//					} // end if
//				} // end if

//				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end if
		} // end for				

		if($data_count > 0){
//			if(($pdf->h - $max_y) < 50) $pdf->AddPage();

			$border = "";
//			$pdf->SetFont($font,'',14);
//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$RTF->set_table_font($font, 14);
			
//			$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
			$RTF->close_line();

//			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//			$pdf->Cell($heading_width[1], 7, "$DC_SHORTNAME          1 - $ORDER", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", $heading_width[0], "center", "0");
			$RTF->cell("$DC_SHORTNAME          1 - $ORDER", $heading_width[1], "center", "0");
			$RTF->close_line();

//			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//			$pdf->Cell($heading_width[1], 7, "บุรุษ  $COUNT_MALE          สตรี $COUNT_FEMALE", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", $heading_width[0], "center", "0");
			$RTF->cell("บุรุษ  $COUNT_MALE          สตรี $COUNT_FEMALE", $heading_width[1], "center", "0");
			$RTF->close_line();

//			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//			$pdf->Cell($heading_width[1], 7, "รับรองถูกต้อง", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", $heading_width[0], "center", "0");
			$RTF->cell("รับรองถูกต้อง", $heading_width[1], "center", "0");
			$RTF->close_line();

//			$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
			$RTF->close_line();

//			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//			$pdf->Cell($heading_width[1], 7, "( $confirm_name )", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", $heading_width[0], "center", "0");
			$RTF->cell("( $confirm_name )", $heading_width[1], "center", "0");
			$RTF->close_line();

//			$pdf->Cell($heading_width[0], 7, "", $border, 0, 'C', 0);
//			$pdf->Cell($heading_width[1], 7, "$confirm_position", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", $heading_width[0], "center", "0");
			$RTF->cell("$confirm_position", $heading_width[1], "center", "0");
			$RTF->close_line();

//			$pdf->Cell(array_sum($heading_width), 7, "", $border, 1, 'C', 0);
			$RTF->open_line();	
			$RTF->cell("", (string)(array_sum($heading_width)), "center", "0");
			$RTF->close_line();
		} // end if
	}else{
		$RTF->add_text("********** ไม่มีข้อมูล **********", "C");
	} // end if
//	$RTF->close_section(); 
	
	$RTF->display($fname);
	
?>