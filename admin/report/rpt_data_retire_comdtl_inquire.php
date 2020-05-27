<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

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
	
/*	if(!$order_by) $order_by=1;
  	if($order_by==1) $order_str = "PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	elseif($order_by==2) $order_str = "c.LEVEL_SEQ_NO $SortType[$order_by], PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	elseif($order_by==3) {
		if($DPISDB=="odbc") {
			if ($PER_TYPE==1 || $PER_TYPE==5) $order_str = "b.POS_NO_NAME $SortType[$order_by], iif(isnull(b.POS_NO),0,CLng(b.POS_NO)) $SortType[$order_by]";
			elseif ($PER_TYPE==2) $order_str = "b.POEM_NO_NAME $SortType[$order_by], iif(isnull(b.POEM_NO),0,CLng(b.POEM_NO)) $SortType[$order_by]";
			elseif ($PER_TYPE==3) $order_str = "b.POEMS_NO_NAME $SortType[$order_by], iif(isnull(b.POEMS_NO),0,CLng(b.POEMS_NO)) $SortType[$order_by]";
			elseif ($PER_TYPE==4) $order_str = "b.POT_NO_NAME $SortType[$order_by], iif(isnull(b.POT_NO),0,CLng(b.POT_NO)) $SortType[$order_by]";
		}elseif($DPISDB=="oci8"){
		 	if ($PER_TYPE==1 || $PER_TYPE==5) $order_str = "b.POS_NO_NAME $SortType[$order_by], to_number(replace(b.POS_NO,'-','')) $SortType[$order_by]";
		 	elseif ($PER_TYPE==2) $order_str = "b.POEM_NO_NAME $SortType[$order_by], to_number(replace(b.POEM_NO,'-','')) $SortType[$order_by]";
		 	elseif ($PER_TYPE==3) $order_str = "b.POEMS_NO_NAME $SortType[$order_by], to_number(replace(b.POEMS_NO,'-','')) SortType[$order_by]";
		 	elseif ($PER_TYPE==4) $order_str = "b.POT_NO_NAME $SortType[$order_by], to_number(replace(b.POT_NO,'-','')) $SortType[$order_by]";
		}elseif($DPISDB=="mysql"){ 
			if ($PER_TYPE==1 || $PER_TYPE==5) $order_str = "b.POEM_NO_NAME $SortType[$order_by], b.POS_NO+0 $SortType[$order_by]";
			elseif ($PER_TYPE==2) $order_str = "b.POEM_NO+0 $SortType[$order_by]";
			elseif ($PER_TYPE==3) $order_str = "b.POEMS_NO_NAME $SortType[$order_by], b.POEMS_NO+0 $SortType[$order_by]";
			elseif ($PER_TYPE==4) $order_str = "b.POT_NO_NAME $SortType[$order_by], b.POT_NO+0 $SortType[$order_by]";
		}
  	} elseif($order_by==4) $order_str = "b.ORG_ID $SortType[$order_by], PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	elseif($order_by==5) $order_str = "PER_STARTDATE $SortType[$order_by], PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]";
  	elseif($order_by==6) $order_str = "PER_BIRTHDATE $SortType[$order_by], PER_NAME $SortType[$order_by], PER_SURNAME $SortType[$order_by]"; */
	
	$search_field = "";
	if ($PER_TYPE == 1) {
		$search_field = ", b.PM_CODE, b.PT_CODE, b.PL_CODE, b.POS_NO as POS_NO, b.POS_NO_NAME  as POS_NO_NAME";
		$search_from = "PER_POSITION";
		$order_field = "POS_NO";
		$arr_search_condition[] = "(a.POS_ID=b.POS_ID)";
	} elseif ($PER_TYPE == 2) {
		$search_field = ", b.PN_CODE, b.POEM_NO as POS_NO, b.POEM_NO_NAME as POS_NO_NAME ";
		$search_from = "PER_POS_EMP";
		$order_field = "POS_NO";			
		$arr_search_condition[] = "(a.POEM_ID=b.POEM_ID)";
	} elseif ($PER_TYPE == 3) {
		$search_field = ", b.EP_CODE, b.POEMS_NO as POS_NO, b.POEMS_NO_NAME as POS_NO_NAME ";
		$search_from = "PER_POS_EMPSER";
		$order_field = "POS_NO";			
		$arr_search_condition[] = "(a.POEMS_ID=b.POEMS_ID)";
	} elseif ($PER_TYPE == 4) {
		$search_field = ", b.TP_CODE, b.POT_NO as POS_NO, b.POT_NO_NAME as POS_NO_NAME";
		$search_from = "PER_POS_TEMP";
		$order_field = "POS_NO";			
		$arr_search_condition[] = "(a.POT_ID=b.POT_ID)";
	}
	if ($ANNUAL_BUDGET){
		$search_birthdate = date_adjust((($ANNUAL_BUDGET - 544)."-10-02"), "y", -60);		
		$search_end_birthdate = date_adjust((($ANNUAL_BUDGET - 543)."-10-01"), "y", -60);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
									    (LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(PER_BIRTHDATE), 1, 10) >= '$search_birthdate') and 
									    (SUBSTR(trim(PER_BIRTHDATE), 1, 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
									    (LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
	} // end if
	if ($ANNUAL){
		$search_birthdate = date_adjust((($ANNUAL - 543)."-01-01"), "y", -60);
		$search_end_birthdate = date_adjust((($ANNUAL - 543)."-12-31"), "y", -60);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
									    (LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(PER_BIRTHDATE), 1, 10) >= '$search_birthdate') and 
									    (SUBSTR(trim(PER_BIRTHDATE), 1, 10) <= '$search_end_birthdate') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(LEFT(trim(PER_BIRTHDATE), 10) >= '$search_birthdate') and 
							(LEFT(trim(PER_BIRTHDATE), 10) <= '$search_end_birthdate') ";
	} // end if
/*	if ($LEVEL_START || $LEVEL_END) 
		$arr_search_condition[] = "(LEVEL_NO>='$LEVEL_START' and LEVEL_NO<='$LEVEL_END')";  */
		
		
		//หา LEVEL_SEQ_NO
	$search_level="";
	if ($LEVEL_START) {
		$cmd = "select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO='$LEVEL_START' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_SEQ_START=$data['LEVEL_SEQ_NO']; 

		 if($LEVEL_SEQ_START) $search_level.= "(LEVEL_SEQ_NO >=$LEVEL_SEQ_START)";
	}
	if($LEVEL_END) {
		$cmd = "select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO='$LEVEL_END' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_SEQ_END=$data['LEVEL_SEQ_NO']; 
	
		if($LEVEL_SEQ_END) $search_level.= "and (LEVEL_SEQ_NO<=$LEVEL_SEQ_END)";
	}
	
	//หา LEVEL NO เพื่อสร้างเงื่อนไข
	if($search_level){
		$cmd = "select LEVEL_NO from PER_LEVEL where $search_level order by LEVEL_SEQ_NO";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_level_search[] = "'".$data[LEVEL_NO]."'";
		$arr_search_condition[] = "(a.LEVEL_NO in (". implode(",", $arr_level_search) ."))";
	}
	if($PL_CODE) {  $arr_search_condition[] = "(b.PL_CODE = '$PL_CODE')";	}
		
	
	if ($ORG_ID){ 
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(a.ORG_ID = $ORG_ID)";			
		}else{
			$arr_search_condition[] = "(b.ORG_ID = $ORG_ID)";	
		}
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		$cmd = " select 	b.ORG_ID
				   from   	PER_ORG a, PER_ORG b
				   where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$MINISTRY_ID and b.ORG_ID_REF=a.ORG_ID
				   order by 	a.ORG_ID, b.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
		
	}elseif($PV_CODE){
		$cmd = " select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
		elseif($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID in (". implode(",", $arr_org) ."))";
		
	} // end if
		
	$search_condition = "";
	if(count($arr_search_condition)) 	$search_condition 	= " and ". implode(" and ", $arr_search_condition);	

	$report_title = "สอบถามรายชื่อ$PERSON_TYPE[$PER_TYPE]ที่จะเกษียณอายุราชการ";
	$report_code = "P0501";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	    if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_data_retire_comdtl_inquire.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="A4";
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
	$orientation='L';
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
	$heading_width[0] = "5";
	$heading_width[1] = "23";
	$heading_width[2] = "23";
	$heading_width[3] = "7";
	$heading_width[4] = "22";
	$heading_width[5] = "10";
	$heading_width[6] = "10";
	}else {
	$heading_width[0] = "13";
	$heading_width[1] = "65";
	$heading_width[2] = "65";
	$heading_width[3] = "20";
	$heading_width[4] = "70";
	$heading_width[5] = "27";
	$heading_width[6] = "23";
	}
		//new format*****************************************
	$heading_text[0] = "ลำดับที่|";
	$heading_text[1] = "ชื่อ-สกุล|";
	$heading_text[2] = "ตำแหน่ง/ระดับ";
	$heading_text[3] = "เลขที่|ตำแหน่ง";
	$heading_text[4] = "$ORG_TITLE";
	$heading_text[5] = "วันที่เริ่ม|รับราชการ";
	$heading_text[6] = "วันเดือนปีเกิด";
	
	$heading_align = array('C','C','C','C','C','C','C');
		

/*	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LTBR',1,'C',1);
	} // function		
		*/
	if($DPISDB=="odbc"){
		$cmd = " select 		PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE , c.LEVEL_SEQ_NO
											$search_field 
						 from 			PER_PERSONAL a	, $search_from b , PER_LEVEL c
						 where 		a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO
											$search_condition 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE 
											$search_field 
						 from 			PER_PERSONAL a	, $search_from b , PER_LEVEL c
						 where 		a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO(+)
											$search_condition 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE 
											$search_field 
						 from 			PER_PERSONAL a	, $search_from b
						 where 		a.PER_TYPE=$PER_TYPE and PER_STATUS=1
											$search_condition 
						 order by 	$order_field ";	
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_NAME = trim($data[PER_NAME]);
		$TMP_PER_SURNAME = trim($data[PER_SURNAME]);
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$TMP_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$TMP_POSITION_LEVEL) $TMP_POSITION_LEVEL = $TMP_LEVEL_NAME;

		$TMP_PER_STARTDATE = trim($data[PER_STARTDATE]);
		$TMP_PER_STARTDATE = show_date_format(substr($TMP_PER_STARTDATE, 0, 10),$DATE_DISPLAY);
		
		$TMP_PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$TMP_PER_BIRTHDATE = show_date_format(substr($TMP_PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
		$TMP_POS_NO_NAME = $data[POS_NO_NAME];
		$TMP_POS_NO = $TMP_POS_NO_NAME.$data[POS_NO];
		
		$TMP_PM_NAME = $TMP_PL_NAME = $TMP_PN_NAME = $TMP_EP_NAME = $TMP_ORG_NAME = "";
		
		$TMP_PREN_CODE = trim($data[PREN_CODE]);
		if($TMP_PREN_CODE){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PREN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PREN_NAME = $data2[PN_NAME];
		} // end if		
		
		$TMP_PM_CODE = trim($data[PM_CODE]);
		if($TMP_PM_CODE){
			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$TMP_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PM_NAME = $data2[PM_NAME];
		} // end if
		
		$TMP_PL_CODE = $data[PL_CODE];
		$TMP_PT_CODE = trim($data[PT_CODE]);
		if($TMP_PL_CODE){
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PL_NAME = $data2[PL_NAME];

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$TMP_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PT_NAME = $data2[PT_NAME];

			$TMP_POS_NAME = trim($TMP_PL_NAME)?($TMP_PL_NAME . $TMP_POSITION_LEVEL . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):" $TMP_LEVEL_NAME";
		} // end if

		$TMP_PN_CODE = $data[PN_CODE];
		if($TMP_PN_CODE){
			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_POS_NAME = $data2[PN_NAME];
		} // end if

		$TMP_EP_CODE = $data[EP_CODE];
		if($TMP_EP_CODE){
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_POS_NAME = $data2[EP_NAME];
		} // end if
		
		$TMP_TP_CODE = $data[TP_CODE];
		if($TMP_EP_CODE){
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TMP_TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_POS_NAME = $data2[TP_NAME];
		} // end if


		$TMP_ORG_ID = $data[ORG_ID];
		if($TMP_ORG_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$TMP_PREN_NAME$TMP_PER_NAME $TMP_PER_SURNAME";
		$arr_content[$data_count][position] = $TMP_POS_NAME;
		$arr_content[$data_count][pos_no] = $TMP_POS_NO;
		$arr_content[$data_count][org_name] = $TMP_ORG_NAME;
		$arr_content[$data_count][per_startdate] = $TMP_PER_STARTDATE;
		$arr_content[$data_count][per_birthdate] = $TMP_PER_BIRTHDATE;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	//new format**********************************************************
	if($count_data){
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

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$POSITION = $arr_content[$data_count][position];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$PER_STARTDATE = $arr_content[$data_count][per_startdate];
			$PER_BIRTHDATE = $arr_content[$data_count][per_birthdate];
		//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] = "$PER_NAME" ;
			$arr_data[] ="$POSITION" ;
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
			$arr_data[] ="$ORG_NAME";
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE);
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE);
			$data_align = array("C","L", "L", "C", "L", "C", "C", "C");
			if ($FLAG_RTF)
			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		if (!$FLAG_RTF)
		$pdf->add_data_tab("", 7, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด
	}else{
	     if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		         }
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