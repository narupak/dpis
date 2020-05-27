<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
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
	
	$report_title = trim($report_title);
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	   $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_master_table_pos_mgtsalary.rtf";
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
	$company_name = "";
	$orientation='P';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	if ($FLAG_RTF) {
	$heading_width[0] = "10";
	$heading_width[1] = "20";
	$heading_width[2] = "20";
	$heading_width[3] = "30";
	$heading_width[4] = "10";
	$heading_width[5] = "10";
	}else{
	$heading_width[0] = "20";
	$heading_width[1] = "40";
	$heading_width[2] = "40";
	$heading_width[3] = "60";
	$heading_width[4] = "20";
	$heading_width[5] = "20";
	}	
	$heading_text[0] = "$POS_NO_TITLE";
	$heading_text[1] = "$MINISTRY_TITLE*Enter*$DEPARTMENT_TITLE";
	$heading_text[2] = "$PL_TITLE*Enter*$PM_TITLE";
	$heading_text[3] = "ประเภทเงินเพิ่มพิเศษ";
	$heading_text[4] = "จำนวนเงิน";
	$heading_text[5] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C','C','C');
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_department_id)) $arr_search_condition[] = "(b.DEPARTMENT_ID = $search_department_id)";
    if(trim($search_pos_id)) $arr_search_condition[] = "(a.POS_ID like '$search_pos_id%')";
  	if(trim($search_ex_code)) $arr_search_condition[] = "(a.EX_CODE like '%$search_ex_code%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)){
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select	a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID
									from	PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c,PER_PERSONAL d  
									where 	a.POS_ID=b.POS_ID and a.EX_CODE=c.EX_CODE and  a.POS_ID=d.POS_ID
											$search_condition 
									order by b.POS_NO_NAME, iif(isnull(b.POS_NO),0,CLng(b.POS_NO)), a.EX_CODE ";
	}elseif($DPISDB=="oci8"){
				$cmd = "	  select 	a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID 
								  from 		PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c 
								  where 	a.POS_ID=b.POS_ID and trim(a.EX_CODE)=trim(c.EX_CODE) 
											$search_condition 
								  order by 	b.POS_NO_NAME, to_number(replace(b.POS_NO,'-','')), a.EX_CODE ";						   
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.POS_ID, a.EX_CODE, POS_STARTDATE, a.POS_STATUS, POS_NO_NAME, POS_NO, PL_CODE, PM_CODE, EX_NAME, EX_AMT, b.DEPARTMENT_ID 
					from 		PER_POS_MGTSALARY a, PER_POSITION b, PER_EXTRATYPE c,PER_PERSONAL d  
					where 		a.POS_ID=b.POS_ID and a.EX_CODE=c.EX_CODE and  a.POS_ID=d.POS_ID
								$search_condition 
					order by 	b.POS_NO_NAME, b.POS_NO+0, a.EX_CODE ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	    $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		$pdf->AutoPageBreak = false; 
	    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		           }
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);
			if (!$PM_NAME) $PM_NAME = $PL_NAME;

			$EX_AMT = $data[EX_AMT];
			$EX_AMT = ($EX_AMT?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EX_AMT)):number_format($EX_AMT)):"-");
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_DEPARTMENT_NAME = $data2[ORG_NAME];
			$TMP_MINISTRY_ID = $data2[ORG_ID_REF];

			$cmd = " 	select ORG_NAME from PER_ORG	where ORG_ID=$TMP_MINISTRY_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_MINISTRY_NAME = trim($data2[ORG_NAME]);

			$arr_data = (array) null;
			$arr_data[] = $data[POS_NO_NAME].$data[POS_NO];
			$arr_data[] = "$TMP_MINISTRY_NAME*Enter*$TMP_DEPARTMENT_NAME";
			$arr_data[] = "$PL_NAME*Enter*$PM_NAME";
			$arr_data[] = trim($data[EX_NAME]);		
			$arr_data[] = $EX_AMT;
			$arr_data[] = "<*img*".(($data[POS_STATUS]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "L", "L", "C", "C");
			if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	     if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
         }
		$data_align = array("C", "C", "C", "C", "C", "C");
		if ($FLAG_RTF)
	    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else		
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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
	ini_set("max_execution_time", 30);
?>