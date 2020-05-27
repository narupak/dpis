<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	  } else{
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");
	}

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= "rpt_master_table_pos_temp.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
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
	$pdf->SetAutoPageBreak(true,10);
	} 
	if ($FLAG_RTF) {
	$heading_width[0] = "8";
	$heading_width[1] = "21";
	$heading_width[2] = "21";
	$heading_width[3] = "21";
	$heading_width[4] = "21";
	$heading_width[5] = "8";
	} else {
	$heading_width[0] = "20";
	$heading_width[1] = "60";
	$heading_width[2] = "60";
	$heading_width[3] = "60";
	$heading_width[4] = "60";
	$heading_width[5] = "20";
	          }	
	$heading_text[0] = "$POS_NO_TITLE";
	$heading_text[1] = "ชื่อตำแหน่ง";
	$heading_text[2] = "$ORG_TITLE";
	$heading_text[3] = "$ORG_TITLE1";
	$heading_text[4] = "ผู้ครองตำแหน่ง";
	$heading_text[5] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C','C','C');
		
  	if ($POSITION_NO_CHAR=="Y") {
		$POT_NO_NUM = "POT_NO";
	} else {
		if($DPISDB=="odbc") $POT_NO_NUM = "CLng(POT_NO)";
		elseif($DPISDB=="oci8") $POT_NO_NUM = "to_number(replace(POT_NO,'-',''))";
		elseif($DPISDB=="mysql") $POT_NO_NUM = "POT_NO+0";
	} // end if
  	if(trim($search_pot_no_min)){ 
		$arr_search_condition[] = "(".$POT_NO_NUM." >= $search_pot_no_min)";
	} // end if
  	if(trim($search_pot_no_max)){ 
		$arr_search_condition[] = "(".$POT_NO_NUM." <= $search_pot_no_max)";
	} // end if
	if(trim($search_tp_code)) $arr_search_condition[] = "(trim(a.TP_CODE) = '". trim($search_tp_code) ."')";

	if(trim($search_org_id)){ 
		if($SESS_ORG_STRUCTURE==1){	
			$arr_search_condition[] = "(c.ORG_ID = $search_org_id)";			
		}else{
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
	}elseif(trim($search_department_id)){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}elseif(trim($search_ministry_id)){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	}elseif($PV_CODE){
		$cmd = " select 	a.ORG_ID as MINISTRY_ID, b.ORG_ID as DEPARTMENT_ID, c.ORG_ID
						 from   	PER_ORG a, PER_ORG b, PER_ORG c
						 where  	a.OL_CODE='01' and b.OL_CODE='02' and c.OL_CODE='03' and c.PV_CODE='$PV_CODE' 
						 				and a.ORG_ID=b.ORG_ID_REF and b.ORG_ID=c.ORG_ID_REF
						 order by a.DEPARTMENT_ID, a.ORG_ID, b.ORG_ID, c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID in (". implode(",", $arr_org) ."))";
	}

	if(trim($search_org_id_1)){
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
		}
	}
	if(trim($search_org_id_2)){
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		}
  	}
        if(trim($search_org_id_3)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_3 = $search_org_id_3)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_3 = $search_org_id_3)";
		}
  	}
        if(trim($search_org_id_4)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_4 = $search_org_id_4)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_4 = $search_org_id_4)";
		}
  	}
        if(trim($search_org_id_5)){ /* Release 5.1.0.4 */
		if($SESS_ORG_STRUCTURE==1){
			$arr_search_condition[] = "(c.ORG_ID_5 = $search_org_id_5)";
		}else{
	 		$arr_search_condition[] = "(a.ORG_ID_5 = $search_org_id_5)";
		}
  	}
	if(trim($search_pot_salary_min)) $arr_search_condition[] = "(POT_MIN_SALARY >= $search_pot_salary_min)";
  	if(trim($search_pot_salary_max)) $arr_search_condition[] = "(POT_MAX_SALARY <= $search_pot_salary_max)";

	if(!isset($search_pos_status)) $search_pos_status = 1;
	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POT_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POT_STATUS = 2)";

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
	$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
											a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							from	(
											(
												PER_POS_TEMP a
												inner join PER_TEMP_POS_NAME b on (a.TP_CODE=b.TP_CODE)
												) left join PER_PERSONAL c on (a.POT_ID=c.POT_ID and c.PER_TYPE=4 and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POT_ID=d.POT_ID and d.PER_TYPE=4 and (d.PER_STATUS=0 or d.PER_STATUS=2))	
							where		a.TP_CODE=b.TP_CODE 
											$search_condition
							group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
											a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							order by a.DEPARTMENT_ID, iif(isnull(POT_NO),0,$POT_NO_NUM) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									from		PER_POS_TEMP a, PER_TEMP_POS_NAME b,
										(select POT_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=4 and PER_STATUS=1) c, 
										(select POT_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=4 and (PER_STATUS=0 or PER_STATUS=2)) d
									where		a.TP_CODE=b.TP_CODE and a.POT_ID=c.POT_ID(+) and a.POT_ID=d.POT_ID(+)
													$search_condition
									group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									order by a.DEPARTMENT_ID, $POT_NO_NUM ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.DEPARTMENT_ID, a.POT_ID, POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
							from	(
											(
												PER_POS_TEMP a
												inner join PER_TEMP_POS_NAME b on (a.TP_CODE=b.TP_CODE)
												) left join PER_PERSONAL c on (a.POT_ID=c.POT_ID and c.PER_TYPE=4 and c.PER_STATUS=1)
											) left join PER_PERSONAL d on (a.POT_ID=d.POT_ID and d.PER_TYPE=4 and (d.PER_STATUS=0 or d.PER_STATUS=2))	
									where		a.TP_CODE=b.TP_CODE 
													$search_condition
									group by a.DEPARTMENT_ID, a.POT_ID, a.POT_NO, a.TP_CODE, b.TP_NAME, POT_MIN_SALARY, POT_MAX_SALARY, 
													a.ORG_ID, ORG_ID_1, ORG_ID_2, POT_STATUS, POT_NO_NAME
									order by a.DEPARTMENT_ID, $POT_NO_NUM ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		foreach($print_search_condition as $show_condition){
		if ($FLAG_RTF){ 
		$company_name .= "$show_condition"."  ";
		$RTF->set_company_name($company_name);
		}else{
		$pdf->Cell(array_sum($heading_width), 7, $show_condition, "", 1, 'L', 0);
		           }
		} // end foreach
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
		    $pdf->SetFont($font,'',14);
		    $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->AutoPageBreak = false; 
		    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		         }
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$temp_POT_ID = trim($data[POT_ID]);
			$POT_NO = trim($data[POT_NO_NAME]).trim($data[POT_NO]);
			$TP_NAME = trim($data[TP_NAME]);
			$POT_MIN_SALARY = trim($data[POT_MIN_SALARY]);
			$POT_MAX_SALARY = trim($data[POT_MAX_SALARY]);
			$POT_SALARY = number_format($POT_MIN_SALARY) . (trim($POT_MAX_SALARY)?(" - ".number_format($POT_MAX_SALARY)):"");
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
			
			$ORG_NAME = "";
			if($ORG_ID){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME) $ORG_NAME = trim($data_dpis2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_1 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_1) $ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME, ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_2 = trim($data_dpis2[ORG_SHORT]);
				if (!$ORG_NAME_2) $ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
			}
			
			if($DPISDB=="odbc"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
												left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a, PER_PRENAME b
								 where	a.PN_CODE=b.PN_CODE(+) and a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
												left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POT_ID=$temp_POT_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=4 ";
			}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
			if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");

			$arr_data = (array) null;
			$arr_data[] = "$POT_NO";
			$arr_data[] = "$TP_NAME";
			$arr_data[] = "$ORG_NAME";
			$arr_data[] = "$ORG_NAME_1";
			$arr_data[] = "$POS_PERSON";
			$arr_data[] = "<*img*".(($data[POT_STATUS]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "L", "L", "L", "C");
			 if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		} // end while
	}else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";

		$data_align = array("C", "C", "C", "C", "C", "C");
		if ($FLAG_RTF)
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", "000000", "");
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