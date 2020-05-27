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
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=i.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "i.PL_NAME";
		$type_code ="a.PT_CODE";
		$select_type_code =",a.PT_CODE";
		$mgt_code ="a.PM_CODE";
		$select_mgt_code =",a.PM_CODE";
		$type_code_b ="b.PT_CODE";
		$select_type_code_b =",b.PT_CODE";
		$mgt_code_b ="b.PM_CODE";
		$select_mgt_code_b =",b.PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=i.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "i.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=i.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "i.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=i.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "i.TP_NAME";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 
	$search_per_type = 1;

	$search_condition = "";
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
	if($select_org_structure==0) {
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "แบบ ขร 4/" . substr($search_year, -2);
	$report_title = "บัญชีแสดงคุณสมบัติของข้าราชการ ซึ่งขอพระราชทานเครื่องราชอิสริยาภรณ์ $search_dc_name ปี พ.ศ.$search_year||$MINISTRY_NAME||$DEPARTMENT_NAME" . ($search_org_id?"||$search_org_name":"");
	$report_code = "R0504";
	include ("rpt_R005004_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R005004_rtf.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='L';
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
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function list_decoratehis($PER_ID, $LEVEL_NO){
		global $DPISDB, $db_dpis2, $db_dpis3;
		global $search_per_type, $arr_content, $data_count, $DATE_DISPLAY;
		global $pl_code,$pl_name,$line_table,$line_join;
		
		if($DPISDB=="odbc"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, SUBSTR(trim(a.DEH_DATE), 1, 10) as DEH_DATE
							 from		PER_DECORATEHIS a, PER_DECORATION b
							 where	a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
							 order by SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.DC_CODE, b.DC_SHORTNAME, LEFT(trim(a.DEH_DATE), 10) as DEH_DATE
							 from		PER_DECORATEHIS a
											inner join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(trim(a.DEH_DATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){
			$GET_DC_NAME = trim($data2[DC_SHORTNAME]);
			$GET_DEH_DATE = trim($data2[DEH_DATE]);
			if($GET_DEH_DATE){
				$SHOW_DEH_DATE = show_date_format($GET_DEH_DATE,$DATE_DISPLAY);
			} // end if

			if($DPISDB=="odbc"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from			PER_POSITIONHIS a, $line_table b, PER_LEVEL e
								 where		$line_join(+)
													and a.PER_ID=$PER_ID and SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) < '$GET_DEH_DATE' and a.LEVEL_NO=e.LEVEL_NO(+)
								 order by		SUBSTR(trim(a.POH_EFFECTIVEDATE), 1, 10) desc ";
			}elseif($DPISDB=="mysql"){
			$cmd = " select			$pl_code as PL_CODE, $pl_name as PL_CODE, a.LEVEL_NO, e.POSITION_LEVEL $select_type_code $select_mgt_code
								 from		(
													PER_POSITIONHIS a
													left join $line_table b on ($line_join)
												) left join PER_LEVEL e on (a.LEVEL_NO=e.LEVEL_NO)
								 where		a.PER_ID=$PER_ID and LEFT(trim(a.POH_EFFECTIVEDATE), 10) < '$GET_DEH_DATE'
								 order by		LEFT(trim(a.POH_EFFECTIVEDATE), 10) desc ";
			}
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$PL_CODE = trim($data3[PL_CODE]);
			$PL_NAME = trim($data3[PL_NAME]);
			$LEVEL_NO = trim($data3[LEVEL_NO]);
			$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
			$PM_CODE = trim($data3[PM_CODE]);	$PT_CODE = trim($data3[PT_CODE]);
			if(trim($PM_CODE)){
				$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PM_NAME = trim($data3[PM_NAME]);
			}
			if(trim($PT_CODE)){
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PT_NAME = trim($data3[PT_NAME]);
			}

			if($DPISDB=="odbc"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select		MIN(SUBSTR(trim(POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		MIN(LEFT(trim(POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE
								 from		PER_POSITIONHIS
								 where	PER_ID=$PER_ID and trim(LEVEL_NO) = '$LEVEL_NO' ";
			} // end if
			$db_dpis3->send_cmd($cmd);
//			$db_dpis3->show_error();
			$data3 = $db_dpis3->get_array();
			$POH_EFFECTIVEDATE = show_date_format($data3[POH_EFFECTIVEDATE],$DATE_DISPLAY);
			$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

			$data_count++;			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = $GET_DC_NAME;
			$arr_content[$data_count][last_deh_date] = $SHOW_DEH_DATE;
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = $REMARK;

			if($PM_CODE){
				$arr_content[$data_count][position] = $PM_NAME;
				
				$data_count++;
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = "";
				$arr_content[$data_count][name] = "";
				$arr_content[$data_count][level] = "";
				$arr_content[$data_count][effectivedate] = "";
				$arr_content[$data_count][salary] = "";
				$arr_content[$data_count][last_dc_name] = "";
				$arr_content[$data_count][last_deh_date] = "";
				$arr_content[$data_count][dc_name] = "";
				$arr_content[$data_count][remark] = "";
				$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
			}else{
				$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			} // end if
		} // end while		
	} // function
	
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
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
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
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, 
											PER_DECORDTL e, PER_DECOR f, PER_DECORATION g, PER_POSITIONHIS h, $line_table i, PER_LEVEL m
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID and e.DE_ID=f.DE_ID and e.DC_CODE=g.DC_CODE 
											and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=h.LEVEL_NO(+) and $line_join(+) and a.LEVEL_NO=m.LEVEL_NO(+)
											$search_condition
						 group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(SUBSTR(trim(h.POH_EFFECTIVEDATE), 1, 10)) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code as PL_CODE, $pl_name as PL_NAME, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE $select_type_code_b $select_mgt_code_b
						 from			(
												(
													(
														(
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
												) left join PER_POSITIONHIS h on (a.PER_ID=h.PER_ID and a.LEVEL_NO=h.LEVEL_NO)
											) left join $line_table i on ($line_join)
										) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
											$search_condition
						  group by	a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, 
											$pl_code, $pl_name, a.LEVEL_NO, m.POSITION_LEVEL, a.PER_SALARY, 
											e.DC_CODE, g.DC_SHORTNAME $select_type_code_b $select_mgt_code_b
						 order by		a.LEVEL_NO desc, MIN(LEFT(trim(h.POH_EFFECTIVEDATE), 10)) ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
//	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = trim($data[PL_NAME]);
		$LEVEL_NO = $data[LEVEL_NO];
		$POSITION_LEVEL = $data[POSITION_LEVEL];

		$PER_SALARY = $data[PER_SALARY];
		$DC_CODE = trim($data[DC_CODE]);
		$DC_NAME = trim($data[DC_SHORTNAME]);
		if(trim($type_code_b)){
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
		}
		if(trim($mgt_code_b)){
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
		}
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);
		$REMARK = "ระดับ ". $POSITION_LEVEL ." เมื่อ $POH_EFFECTIVEDATE";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
		$arr_content[$data_count][effectivedate] = "$POH_EFFECTIVEDATE";
		$arr_content[$data_count][salary] = "$PER_SALARY";
		$arr_content[$data_count][last_dc_name] = "-";
		$arr_content[$data_count][last_deh_date] = "";
		$arr_content[$data_count][dc_name] = "$DC_NAME";
		$arr_content[$data_count][remark] = "$REMARK";
		if($PM_CODE){
			$arr_content[$data_count][position] = $PM_NAME;
			
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = "";
			$arr_content[$data_count][name] = "";
			$arr_content[$data_count][level] = "";
			$arr_content[$data_count][effectivedate] = "";
			$arr_content[$data_count][salary] = "";
			$arr_content[$data_count][last_dc_name] = "";
			$arr_content[$data_count][last_deh_date] = "";
			$arr_content[$data_count][dc_name] = "";
			$arr_content[$data_count][remark] = "";
			$arr_content[$data_count][position] = "(". $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") .")";
		}else{
			$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
		} // end if
		
		list_decoratehis($PER_ID, $LEVEL_NO);
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
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

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$LEVEL_NO = $arr_content[$data_count][level];
			$EFFECTIVEDATE = $arr_content[$data_count][effectivedate];
			$SALARY = $arr_content[$data_count][salary];
			$LAST_DC_NAME = $arr_content[$data_count][last_dc_name];
			$LAST_DEH_DATE = $arr_content[$data_count][last_deh_date];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$REMARK = $arr_content[$data_count][remark];
			$POSITION = $arr_content[$data_count][position];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = level_no_format($LEVEL_NO);
			$arr_data[] = $EFFECTIVEDATE;
			$arr_data[] = $SALARY;
			$arr_data[] = $POSITION;
			$arr_data[] = $LAST_DC_NAME;
			$arr_data[] = $LAST_DEH_DATE;
			$arr_data[] = $DC_NAME;
			$arr_data[] = $REMARK;
	
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
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
?>