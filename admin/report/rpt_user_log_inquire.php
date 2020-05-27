<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
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

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	ini_set("max_execution_time", $max_execution_time);
	
	
	$report_title = trim($report_title);
	$report_code = "";
	
	if (!$PER_TYPE) $PER_TYPE = 1;
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "2:desc";
	$arrSort = explode(":",$sort_type);
	$SortType[$arrSort[0]]	= $arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1) $order_str = "LOG_DATE $SortType[$order_by], FULLNAME $SortType[$order_by]";
  	elseif($order_by==2) $order_str = "FULLNAME $SortType[$order_by], LOG_DATE $SortType[$order_by]";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_user_log_inquire.rtf";
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
	$orientation='L';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	}
	if ($FLAG_RTF) {
	$heading_width[0] = "4";
	$heading_width[1] = "13";
	$heading_width[2] = "15";
	$heading_width[3] = "43";
	$heading_width[4] = "25";
	}else{
	$heading_width[0] = "10";
	$heading_width[1] = "30";
	$heading_width[2] = "40";
	$heading_width[3] = "140";
	$heading_width[4] = "70";
	}
	//new format**************************************************
    $heading_text[0] = "ลำดับที่";
	$heading_text[1] = "วันที่บันทึก";
	$heading_text[2] = "ชื่อผู้ใช้งาน";
	$heading_text[3] = "รายละเอียด";
	$heading_text[4] = "รายละเอียดเพิ่มเติม";
	
	$heading_align = array('C','C','C','C','C');

//	echo "search_date_from=$search_date_from , search_date_to=$search_date_to<br>";
	$arr_search_condition = (array) null;
	if(trim($search_date_from)) {
		$temp_start = (substr($search_date_from, 6, 4) - 543) ."-". substr($search_date_from, 3, 2) ."-". substr($search_date_from, 0, 2);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(LOG_DATE), 10) >= '$temp_start') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(LOG_DATE), 1, 10) >= '$temp_start') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(SUBSTRING(trim(LOG_DATE), 1, 10) >= '$temp_start') ";
	}
	if(trim($search_date_to)){
		$temp_end = (substr($search_date_to, 6, 4) - 543) ."-". substr($search_date_to, 3, 2) ."-". substr($search_date_to, 0, 2);
		if($DPISDB=="odbc") 
			$arr_search_condition[] = "(LEFT(trim(LOG_DATE), 10) >= '$temp_end') ";
		elseif($DPISDB=="oci8") 
			$arr_search_condition[] = "(SUBSTR(trim(LOG_DATE), 1, 10) <= '$temp_end') ";
		elseif($DPISDB=="mysql")
			$arr_search_condition[] = "(SUBSTRING(trim(LOG_DATE), 1, 10) >= '$temp_end') ";
	}
	
	//if($search_username) {  $arr_search_condition[] = "(USERNAME like '%$search_username%' or FULLNAME like '%$search_username%')";	}
	if($search_username) {  $arr_search_condition[] = "(UPPER(USERNAME) LIKE UPPER ('%$search_username%') or UPPER(FULLNAME) LIKE UPPER ('%$search_username%') or 
	                                                    LOWER(USERNAME) LIKE LOWER ('%$search_username%') or LOWER(FULLNAME) LIKE LOWER ('%$search_username%'))";	}
	if($search_log_detail) {  $arr_search_condition[] = "UPPER(LOG_DETAIL) LIKE UPPER ('%$search_log_detail%') or LOWER(LOG_DETAIL) LIKE LOWER ('%$search_log_detail%')";}

	$search_condition = "";           
	if ($arr_search_condition)		$search_condition = implode(" and ", $arr_search_condition);
		$cmd ="select 	LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE
									  from 		USER_LOG ".($search_condition ? "where ".$search_condition : "")." ";
		$count_data = $db_dpis->send_cmd($cmd);

	if($DPISDB=="odbc"){
			$cmd = "	
							select		PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE , c.LEVEL_SEQ_NO, a.DEPARTMENT_ID
										$search_field 
							from			PER_PERSONAL a, $search_from b , PER_LEVEL c
							where		a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO
										$search_condition
							order by 	$order_str ";
		}elseif($DPISDB=="oci8"){
			$cmd = "select * from (
							   select rownum rnum, q1.* from ( 
									  select 	LOG_ID, USER_ID, USERNAME, FULLNAME, LOG_DETAIL, LOG_DATE
									  from 		USER_LOG ".($search_condition ? "where ".$search_condition : "")."
									  order by 	$order_str  
							   )  q1
						) ";	
//									  where 	a.PER_TYPE=$PER_TYPE and PER_STATUS=1 and a.LEVEL_NO = c.LEVEL_NO(+)
//												$search_condition 
		}elseif($DPISDB=="mysql"){
			$cmd = "	select	PER_ID, a.PN_CODE as PREN_CODE, PER_NAME, PER_SURNAME, a.LEVEL_NO, b.ORG_ID, PER_STARTDATE, PER_BIRTHDATE, a.DEPARTMENT_ID
										$search_field 
							from		PER_PERSONAL a, $search_from b
							where	a.PER_TYPE=$PER_TYPE and PER_STATUS=1
										$search_condition
							order by $order_str ";
		} // end if
		$count_page_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	    if($count_page_data){
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
				
		$data_count = $data_row = 0;
		while ($data = $db_dpis->get_array()) {
		$data_count++;
		$data_num++;
		$TMP_LOG_ID = $data[LOG_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_LOG_ID;
		$TMP_FULLNAME = trim($data[FULLNAME]);
		$TMP_LOG_DETAIL = trim($data[LOG_DETAIL]);
		include("../php_scripts/user_log_inquire_decode.php");
		$TMP_LOG_DATE = show_date_format($data[LOG_DATE], 1);


	//new format************************************************************			
            	$arr_data = (array) null;
				$arr_data[] ="$data_num";
				$arr_data[] ="$TMP_LOG_DATE";
				$arr_data[] ="$TMP_FULLNAME";
				$arr_data[] ="$TMP_LOG_DETAIL";
				$arr_data[] ="$detail_id_1";
				$data_align = array("C","L","L","L","L");
				  if ($FLAG_RTF)
		    	 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		         else	
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end for
		}else{
		 if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$pdf->SetFont($font,'b',16);
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