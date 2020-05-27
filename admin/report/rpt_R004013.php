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
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$pl_name = "g.PL_NAME";
		$order_pl = "b.PL_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$pl_name = "g.PN_NAME";
		$order_pl = "b.PN_CODE";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$pl_name = "g.EP_NAME";
		$order_pl = "b.EP_CODE";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$pl_name = "g.TP_NAME";
		$order_pl = "b.TP_CODE";
	} // end if

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($search_tr_code){
            $arr_search_condition[] = "(trim(d.TR_CODE)=trim('$search_tr_code'))";
        }
        if($search_st_date){
            $ARR_START = explode('/',$search_st_date);
            $E_START_DATE = ($ARR_START[2]-543).'-'.$ARR_START[1].'-'.$ARR_START[0];
            $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".$E_START_DATE."'";
        }
        if($search_end_date){
            $ARR_END = explode('/',$search_end_date);
            $E_END_DATE = ($ARR_END[2]-543).'-'.$ARR_END[1].'-'.$ARR_END[0];
            $arr_search_condition[] = "SUBSTR(trim(d.TRN_ENDDATE), 1, 10) <= '".$E_END_DATE."'";
        }
        
	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type]ที่ผ่านการอบรมหลักสูตร $search_tr_name";
	$report_code = "R0413";
	include ("rpt_R004013_format_pdf.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R004013_rtf.rtf";

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
		
		$page_start_x = $pdf->x;			
                $page_start_y = $pdf->y;
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
											d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE, TRN_ORG
						 from			(
												(
													(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join $position_table b on $position_join 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on $line_join
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		LEFT(trim(d.TRN_STARTDATE), 10), $order_pl, a.LEVEL_NO ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select	a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
                            d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, TRN_ORG,
                            d.TRN_PASS 
			from PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
			where $position_join(+) and b.ORG_ID=c.ORG_ID(+) 
                            and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
                            and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
                            $search_condition
                         order by  SUBSTR(trim(d.TRN_STARTDATE), 1, 10), $order_pl, a.LEVEL_NO "; //,e.TR_SEQ_NO,e.TR_CODE 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
											d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE, TRN_ORG
						 from			(
												(
													(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on $line_join
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		LEFT(trim(d.TRN_STARTDATE), 10), $order_pl, a.LEVEL_NO ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        //echo "<pre>";
        //die($cmd);
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PT_CODE = trim($data[PT_CODE]);
		$ORG_SHORT = $data[ORG_SHORT];
		$ORG_NAME = $data[ORG_NAME];
		$TRN_NO = $data[TRN_NO];
		$TRN_STARTDATE = show_date_format($data[TRN_STARTDATE],$DATE_DISPLAY);
		$TRN_ENDDATE = show_date_format($data[TRN_ENDDATE],$DATE_DISPLAY);
		$TRN_ORG = $data[TRN_ORG];
                
                $TRN_PASS= $data[TRN_PASS];
                $strTRN_PASS = '';
                if($TRN_PASS==0){
                     $strTRN_PASS = '*';
                }
		
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
			}
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME.' '.$strTRN_PASS;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
//		$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][trn_no] = $TRN_NO;
		$arr_content[$data_count][trn_org] = $TRN_ORG;
		$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
		$arr_content[$data_count][trn_enddate] = $TRN_ENDDATE;

		$data_count++;
	} // end while
        
	
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
			$ORDER = $arr_content[$data_count][order];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][level];
			$NAME_4 = $arr_content[$data_count][org_name];
			$NAME_5 = $arr_content[$data_count][trn_no];
			$NAME_6 = $arr_content[$data_count][trn_org];
			$NAME_7 = $arr_content[$data_count][trn_startdate];
			$NAME_8 = $arr_content[$data_count][trn_enddate];
			
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			if ($have_pic && $img_file) $arr_data[] = $IMAGE;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			$arr_data[] = $NAME_6;
			$arr_data[] = $NAME_7;
			$arr_data[] = $NAME_8;
			
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF)
			$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด				

		$arr_data = (array) null;
		if ($have_pic && $img_file) 
                    $arr_data[] =  "<**1**>รวม";
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = "<**1**>รวม";;
		$arr_data[] = $data_row ."  คน  ";
                
                
                
                
                
		
                
                
		
		//$data_align = array( "C","C", "C","C", "C", "C", "R");
		
		if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else
                    $result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");	
                    
                
                    $arr_data1 = (array) null;
                $arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
		$arr_data1[] =  "<**3**>หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร";
                $data_align = array( "L","L", "L","L", "L", "L", "L");
                    $result = $pdf->add_data_tab($arr_data1, 7, "TRHL", $data_align, "", "14", "", "000000", "");	
				//TRHBL
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
?>