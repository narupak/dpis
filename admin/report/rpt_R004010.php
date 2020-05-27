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
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE, b.PM_CODE";
		$f_name = "f.PL_NAME";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$f_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$f_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$f_name = "f.TP_NAME";
	} // end if

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; 

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "YEAR" :
				if($select_list) $select_list .= ", ";
				if($DPISDB=="odbc") $select_list .= "LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE";
				elseif($DPISDB=="oci8") $select_list .= "SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE";
				elseif($DPISDB=="mysql") $select_list .= "LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE";
				
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "LEFT(trim(a.PER_RETIREDATE), 10)";
				elseif($DPISDB=="oci8") $order_by .= "SUBSTR(trim(a.PER_RETIREDATE), 1, 10)";
				elseif($DPISDB=="mysql") $order_by .= "LEFT(trim(a.PER_RETIREDATE), 10)";
				
				$heading_name .= " ปีงบประมาณ";
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";
				
				$heading_name .= " $LEVEL_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID"; } 
	}

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".(($search_budget_year + 4) - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) > '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) <= '".(($search_budget_year + 4) - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) > '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) <= '".(($search_budget_year + 4) - 543)."-10-01')";
	
	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$show_budget_year_5 = $search_budget_year + 4;
	$show_budget_year_5 = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_budget_year_5):$show_budget_year_5);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่เกษียณอายุราชการ ล่วงหน้า 5 ปี||ปีงบประมาณ ".($show_budget_year)." - ".($show_budget_year_5);
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0410";
	include ("rpt_R004010_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R004010_rtf.rtf";

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

	function list_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $db_dpis3,$db_dpis4, $position_table, $position_join, $line_table, $line_join, $line_code, $f_name;
		global $arr_rpt_order, $rpt_order_index, $search_per_type, $arr_content, $data_count, $person_count, $search_budget_year;
		global $days_per_year, $days_per_month, $seconds_per_day, $DATE_DISPLAY,$select_org_structure;
		global $have_pic,$img_file;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_code, $f_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL,
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,a.PER_RETIREDATE
							 from		(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on $line_join
											) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
							$search_condition
							 order by 	LEFT(trim(a.PER_STARTDATE), 10) ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_code, $f_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL,
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE,a.PER_RETIREDATE
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e, $line_table f, PER_LEVEL h
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and $line_join(+) and a.LEVEL_NO=h.LEVEL_NO(+)
												$search_condition
							 order by 	SUBSTR(trim(a.PER_STARTDATE), 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $line_code, $f_name as PL_NAME, a.LEVEL_NO, h.POSITION_LEVEL,
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE,a.PER_RETIREDATE
							 from		(
												(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
												) left join $line_table f on $line_join
											) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
							$search_condition
							 order by 	LEFT(trim(a.PER_STARTDATE), 10) ";
		} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while($data = $db_dpis2->get_array()){
			$data_count++;
			$person_count++;
			$PER_ID = $data[PER_ID];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$PL_NAME = trim($data[PL_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
			$PT_CODE = trim($data[PT_CODE]);
			$PT_NAME = trim($data[PT_NAME]);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);

			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where	PM_CODE='$PM_CODE' ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$PM_NAME = trim($data2[PM_NAME]);

			$TMP_ORG_NAME = $data[ORG_NAME];

			$ORG_ID_1 = $data[ORG_ID_1];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis3->send_cmd($cmd);
			$data2 = $db_dpis3->get_array();
			$TMP_ORG_NAME_1 = $data2[ORG_NAME];

			if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด" || $PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
				$PM_NAME .= $TMP_ORG_NAME;
				$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
				$PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $PM_NAME); 
			} elseif ($PM_NAME=="นายอำเภอ") {
				$PM_NAME .= $TMP_ORG_NAME_1;
				$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
			}
			
			if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
			
			$arr_content[$data_count][type] = "CONTENT";
			if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
			}
			$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
			$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME ) . (trim($PM_NAME)?")":"");
			$arr_content[$data_count][startdate] = $PER_STARTDATE;	
			$arr_content[$data_count][birthdate] = $PER_BIRTHDATE;	
			$arr_content[$data_count][retiredate] = $PER_RETIREDATE;

//			$data_count++;
		} // end while
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PER_RETIREYEAR, $LEVEL_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(e.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){	
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else if($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "YEAR" :	
					if($DPISDB=="odbc"){
						$arr_addition_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) >= '".($PER_RETIREYEAR - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) < '".($PER_RETIREYEAR)."-10-01')";
					}elseif($DPISDB=="oci8"){
						$arr_addition_condition[] = "(SUBSTR(trim(a.PER_RETIREDATE), 1, 10) >= '".($PER_RETIREYEAR - 1)."-10-01' and SUBSTR(trim(a.PER_RETIREDATE), 1, 10) < '".($PER_RETIREYEAR)."-10-01')";
					}elseif($DPISDB=="mysql"){
						$arr_addition_condition[] = "(LEFT(trim(a.PER_RETIREDATE), 10) >= '".($PER_RETIREYEAR - 1)."-10-01' and LEFT(trim(a.PER_RETIREDATE), 10) < '".($PER_RETIREYEAR)."-10-01')";
					} // end if
				break;
				case "LEVEL" :	
					if($LEVEL_NO) $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '". str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT) ."')";
					else $arr_addition_condition[] = "(trim(a.LEVEL_NO) = '' or a.LEVEL_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PER_RETIREYEAR, $LEVEL_NO;
		global $MINISTRY, $DEPARTMENT;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					$MINISTRY_ID = -1;
					$MINISTRY = -1;
				break;
				case "DEPARTMENT" :
					$DEPARTMENT_ID = -1;
					$DEPARTMENT = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "YEAR" :	
					$PER_RETIREYEAR = -1;
				break;
				case "LEVEL" :	
					$LEVEL_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on $position_join 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct $select_list
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_ORG e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct $select_list
						 from		(
											(
												PER_PERSONAL a 
												left join $position_table b on $position_join 
											) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
											$search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "cmd=$cmd ($count_data)<br>";
//	$data_count = 0;
	while($data = $db_dpis->get_array()){
		if ($f_all)	$content_list = "1";
		else $content_list = "";

		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					$MINISTRY = $data[MINISTRY_ID];

					if($content_list) $content_list .= "|";
					$content_list .= $MINISTRY;
				break;
				case "DEPARTMENT" :
					$DEPARTMENT = $data[DEPARTMENT_ID];

					if($content_list) $content_list .= "|";
					$content_list .= $DEPARTMENT;
				break;
				case "ORG" :
					$ORG_ID = $data[ORG_ID];

					if($content_list) $content_list .= "|";
					$content_list .= $ORG_ID;
				break;

				case "YEAR" :
					$PER_RETIREDATE = $data[PER_RETIREDATE];
					$PER_RETIREYEAR = substr($PER_RETIREDATE, 0, 4);					
					if($PER_RETIREDATE >= ($PER_RETIREYEAR."-10-01")) $PER_RETIREYEAR++;

					if($content_list) $content_list .= "|";
					$content_list .= $PER_RETIREYEAR;
				break;

				case "LEVEL" :
					$LEVEL_NO = $data[LEVEL_NO];

					if($content_list) $content_list .= "|";
					$content_list .= str_pad($LEVEL_NO, 2, "0", STR_PAD_LEFT);
				break;
			} // end switch case
		} // end for	

		if(!in_array($content_list, $arr_prepare_data)) $arr_prepare_data[] = $content_list;
//		$data_count++;
	} // end while
	
	sort($arr_prepare_data);
//	echo "<pre>"; print_r($arr_prepare_data); echo " ()</pre>";
//	echo "af..MINISTRY=$MINISTRY, DEPARTMENT=$DEPARTMENT, ORG_ID=$ORG_ID<br>";
	$count_data = count($arr_prepare_data);

	$data_count = 0;
	$person_count = 0;
	initialize_parameter(0);
	foreach($arr_prepare_data as $content_list){
		$data = explode("|", $content_list, 4);
//		echo "content_list=$content_list , arr_rpt_order=".implode(",",$arr_rpt_order)."<br>";
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
//					echo "MINISTRY($MINISTRY) != data(".$data[$rpt_order_index].")<br>";
					if($MINISTRY != $data[$rpt_order_index]){
						$MINISTRY = $data[$rpt_order_index];
//						echo "---------------------> MINISTRY($MINISTRY)<br>";
						if($MINISTRY == ""){
							$MINISTRY_NAME = "[ไม่ระบุ$MINISTRY_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY ";
							$cnt = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
//							echo "cmd=$cmd ($cnt)<br>";
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", (($f_all ? ($rpt_order_index-1) : $rpt_order_index) * 5)) . $MINISTRY_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
				
				case "DEPARTMENT" :
//					echo "DEPARTMENT($DEPARTMENT) != data(".$data[$rpt_order_index].")<br>";
					if($DEPARTMENT != $data[$rpt_order_index]){
						$DEPARTMENT = $data[$rpt_order_index];
//						echo "---------------------> DEPARTMENT($DEPARTMENT)<br>";
						if($DEPARTMENT == ""){
							$DEPARTMENT_NAME = "[ไม่ระบุ$DEPARTMENT_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", (($f_all ? ($rpt_order_index-1) : $rpt_order_index) * 5)) . $DEPARTMENT_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
				
				case "ORG" :
//					echo "ORG_ID($ORG_ID) != data(".$data[$rpt_order_index].")<br>";
					if($ORG_ID != $data[$rpt_order_index]){
						$ORG_ID = $data[$rpt_order_index];
//						echo "---------------------> ORG_ID($ORG_ID)<br>";
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุ$ORG_TITLE]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", (($f_all ? ($rpt_order_index-1) : $rpt_order_index) * 5)) . $ORG_NAME;

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;

				case "YEAR" :
//					echo "PER_RETIREYEAR($PER_RETIREYEAR) != data(".$data[$rpt_order_index].")<br>";
					if($PER_RETIREYEAR != $data[$rpt_order_index]){
						$PER_RETIREYEAR = trim($data[$rpt_order_index]);
//						echo "---------------------> PER_RETIREYEAR($PER_RETIREYEAR)<br>";

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "YEAR";
						$arr_content[$data_count][name] = str_repeat(" ", (($f_all ? ($rpt_order_index-1) : $rpt_order_index) * 5)) ."ปีงบประมาณ ". ($PER_RETIREYEAR + 543 - 1);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;

				case "LEVEL" :
//					echo "LEVEL_NO($LEVEL_NO) != data(".$data[$rpt_order_index].")<br>";
					if($LEVEL_NO != ($data[$rpt_order_index] + 0)){
						$LEVEL_NO = $data[$rpt_order_index] + 0;
//						echo "---------------------> LEVEL_NO($LEVEL_NO)<br>";

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "LEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", (($f_all ? ($rpt_order_index-1) : $rpt_order_index) * 5)) ."ระดับ ". level_no_format($LEVEL_NO);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						if($rpt_order_index == (count($arr_rpt_order) - 1)) list_person($search_condition, $addition_condition);
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for		
	} // end foreach
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
			
	//	echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	} else {
		$pdf->AutoPageBreak = false; 
	//	echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
	}
	if (!$result) echo "****** error ****** on open table for $table<br>";
		
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][startdate];
			$NAME_4 = $arr_content[$data_count][birthdate];
			$NAME_5 = $arr_content[$data_count][retiredate];

			$arr_data = (array) null;
			if($REPORT_ORDER == "CONTENT"){
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				$arr_data[] = $NAME_1;
				$arr_data[] = $NAME_2;
				$arr_data[] = $NAME_3;
				$arr_data[] = $NAME_4;
				$arr_data[] = $NAME_5;
			}else{
				if ($have_pic && $img_file) $arr_data[] =  "<**1**>$NAME_1";
				$arr_data[] =  "<**1**>$NAME_1";
				$arr_data[] =  "<**1**>$NAME_1";
				$arr_data[] =  "<**1**>$NAME_1";
				$arr_data[] =  "<**1**>$NAME_1";
				$arr_data[] =  "<**1**>$NAME_1";
			}

			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด			
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
?>