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
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_code = "b.PL_CODE";
		$position_no= "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_code = "b.PN_CODE";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_code = "b.EP_CODE";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_code = "b.TP_CODE";
		$position_no= "b.POT_NO";
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
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "POSNO", "NAME");

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
				if($select_org_structure == 0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID_1";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_2";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID_2";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= $line_code;

				if($order_by) $order_by .= ", ";
				$order_by .= $line_code; 
				$heading_name .= $line_title;
				break;
			case "LEVEL" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.LEVEL_NO";
				
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
			case "POSNO" :
				if($select_list) $select_list .= ", ";
				if($DPISDB=="odbc") $select_list .= "IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO";
				elseif($DPISDB=="oci8") $select_list .= "to_number(replace($position_no,'-','')) as POS_NO";
				elseif($DPISDB=="mysql") $select_list .= "$position_no as POS_NO";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull($position_no), 0, CLng($position_no))";
				elseif($DPISDB=="oci8") $order_by .= "to_number(replace($position_no,'-',''))";
				elseif($DPISDB=="mysql") $order_by .= $position_no;
			
				$heading_name .= " $POS_NO_TITLE";
				break;
			case "NAME" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PER_NAME, a.PER_SURNAME";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";

				$heading_name .= " $FULLNAME_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_NAME, a.PER_SURNAME";
	if(!trim($select_list)) $select_list = "a.PER_NAME, a.PER_SURNAME";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if($list_person_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_person_type == "CONDITION"){
		if(trim($search_pos_no)){ 
			$arr_search_condition[] = "($position_no like '$search_pos_no%')";	
		} // end if
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)) $arr_search_condition[] = "(a.LEVEL_NO >= '$search_min_level')";
		if(trim($search_max_level)) $arr_search_condition[] = "(a.LEVEL_NO <= '$search_max_level')";
	} // end if

	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการรายบุคคล" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
	$report_code = "R0704";
	include ("rpt_R007004_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R007004_rtf.rtf";

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

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis1, $db_dpis2;
		global $arr_content, $data_count;
		global $search_date_min, $search_date_max, $select_org_structure,$DATE_DISPLAY;
		
		$search_condition = "";
		unset($arr_search_condition);
		if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(START_DATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(START_DATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(START_DATE, 10) >= '$search_date_min')";
		} // end if
		if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(END_DATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(END_DATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(END_DATE, 10) <= '$search_date_max')";
		} // end if
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		
		$cmd = " select		WL_CODE, b.WC_NAME, START_DATE, END_DATE, ABSENT_FLAG, REMARK
						 from 		PER_WORK_TIME a, PER_WORK_CYCLE b
						 where	PER_ID=$PER_ID and a.WC_CODE = b.WC_CODE
						 				$search_condition
						 order by START_DATE ";
		$count_absent = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data_row = 0;
		while($data2 = $db_dpis2->get_array()){
			$data_row++;
			$data_count++;

			$WL_CODE = trim($data2[WL_CODE]);
			$cmd = "	select WL_NAME from PER_WORK_LOCATION where WL_CODE='$WL_CODE'";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$WL_NAME = $data1[WL_NAME];
			$WC_NAME = trim($data2[WC_NAME]);
			$START_DATE = substr($data2[START_DATE], 0, 10);
			$END_DATE = substr($data2[END_DATE], 0, 10);
			$START_TIME = substr($data2[START_DATE], 11, 8);
			$END_TIME = substr($data2[END_DATE], 11, 8);
			$ABSENT_FLAG = $data2[ABSENT_FLAG];
			$REMARK = $data2[REMARK];
			
			$START_DATE = show_date_format($START_DATE,$DATE_DISPLAY)." ". $START_TIME;
			$END_DATE =  show_date_format($END_DATE,$DATE_DISPLAY)." ". $END_TIME;

			$ABSENT_NAME = "";
			if ($ABSENT_FLAG=="1") $ABSENT_NAME = "วันหยุด";
			elseif ($ABSENT_FLAG=="2") $ABSENT_NAME = "ลา";
			elseif ($ABSENT_FLAG=="3") $ABSENT_NAME = "สาย";
			elseif ($ABSENT_FLAG=="4") $ABSENT_NAME = "ปฏิบัติราชการนอกสถานที่";
			elseif ($ABSENT_FLAG=="5") $ABSENT_NAME = "ขาดราชการ";
			elseif ($ABSENT_FLAG=="9") $ABSENT_NAME = "ไม่บันทึกเวลา";

			$arr_content[$data_count][type] = "ABSENT";
			$arr_content[$data_count][ORDER] = $data_row;
			$arr_content[$data_count][WL_NAME] = $WL_NAME;
			$arr_content[$data_count][WC_NAME] = $WC_NAME;
			$arr_content[$data_count][START_DATE] = $START_DATE;
			$arr_content[$data_count][END_DATE] = $END_DATE;
			$arr_content[$data_count][ABSENT_NAME] = $ABSENT_NAME;			
			$arr_content[$data_count][REMARK] = $REMARK;			
		} // end while
		
	} // function
	
	
		if($DPISDB=="odbc"){
			$cmd = " select			$select_list, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_OFFNO, $position_no as POS_NO
							 from		(
												(
							 						(
														PER_PERSONAL a
														inner join $position_table b on ($position_join)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
							 $search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace("where", "and", $search_condition);
			$cmd = " select			$select_list, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_OFFNO, $position_no as POS_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e
							 where		a.PER_STATUS=1 and $position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
												$search_condition
							 order by		$order_by ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$select_list, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_OFFNO, $position_no as POS_NO
							 from		(
												(
							 						(
														PER_PERSONAL a
														inner join $position_table b on ($position_join)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
							 $search_condition
							 order by		$order_by ";
		} // end if
	

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	while($data = $db_dpis->get_array()){		
		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_OFFNO = $data[PER_OFFNO];
		$PER_OFFNO = (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_OFFNO):$PER_OFFNO);
		$ORG_NAME = $data[ORG_NAME];
		$POS_NO = $data[POS_NO];
		$POS_NO = (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
		
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

		$arr_content[$data_count][type] = "PERSON";
		if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
		}
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME . "  $CARDNO_TITLE " . $PER_OFFNO;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][pos_no] = $POS_NO;
		
		count_absent($PER_ID);

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
				
		//	echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORDER = $arr_content[$data_count][ORDER];
			$WL_NAME = $arr_content[$data_count][WL_NAME];
			$WC_NAME = $arr_content[$data_count][WC_NAME];
			$START_DATE = $arr_content[$data_count][START_DATE];
			$END_DATE = $arr_content[$data_count][END_DATE];
			$ABSENT_NAME = $arr_content[$data_count][ABSENT_NAME];
			$REMARK = $arr_content[$data_count][REMARK];

			if($REPORT_ORDER == "PERSON"){
				if($data_count > 0) 
					if ($FLAG_RTF)
						$RTF->new_page();
					else {
						$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด			
						$pdf->AddPage();
					}
/*
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					
				$pdf->Cell(90,7,"ชื่อ $NAME",0,0,'L');
				$pdf->Cell(105,7,"สังกัด $ORG_NAME",0,0,'L');
				$pdf->Cell(88,7,"เลขที่ตำแหน่ง $POS_NO",0,1,'R');
*/
				$arr_data = (array) null;
				$arr_data[] = "<**1**>ชื่อ $NAME";
				$arr_data[] = "<**1**>ชื่อ $NAME";
				$arr_data[] = "<**2**>สังกัด $ORG_NAME";
				$arr_data[] = "<**2**>สังกัด $ORG_NAME";
				$arr_data[] = "<**2**>สังกัด $ORG_NAME";
				if($have_pic && $img_file){
				$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".$POS_NO;
				$arr_data[] = "$IMAGE";
				}else{
				$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".$POS_NO;
				$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".$POS_NO;
				}

				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "TRHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				if($arr_content[($data_count + 1)][type] != "PERSON" && ($data_count < (count($arr_content) - 1))){ 
					if ($FLAG_RTF)
						$RTF->print_tab_header();
					else
						$pdf->print_tab_header();
				}else{
					if ($FLAG_RTF)
						$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
					else {
						$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด			
						$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
					}
					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
				} // end if
			}elseif($REPORT_ORDER == "ABSENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $WL_NAME;
				$arr_data[] = $START_DATE;
				$arr_data[] = $END_DATE;
				$arr_data[] = $WC_NAME;
				$arr_data[] = $ABSENT_NAME;
				$arr_data[] = $REMARK;

				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if			
		} // end for
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else {
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด			
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		}
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