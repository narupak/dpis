<? 
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include("../../php_scripts/connect_database.php");
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

	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	
	
	if($search_department_id){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
	}else if($search_ministry_id){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if

	if(trim($search_org_id)) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
	if(trim($search_pos_no)) $arr_search_condition[] = "(POS_NO like '$search_pos_no%')";

	if(trim($search_pl_code))  {
		$arr_search_condition[] = "(PL_CODE = '$search_pl_code')";
	}
	
	if(trim($search_pm_code))  {
		$arr_search_condition[] = "(PM_CODE = '$search_pm_code')";
	}
	
	if(trim($search_cl_name))  {
		$arr_search_condition[] = "(CL_NAME = '$search_cl_name')";
	}
	if(trim($search_pl_name)) $arr_search_condition[] = "(PL_NAME like '$search_pl_name%')";
	if(trim($search_pm_name)) $arr_search_condition[] = "(PM_NAME like '$search_pm_name%')";
	if(trim($search_org_name)) $arr_search_condition[] = "(ORG_NAME like '$search_org_name%')";
	
	if(trim($search_meeting_time))  $arr_search_condition[] = "(MEETING_TIME = '$search_meeting_time')";
	if(trim($search_meeting_year))  $arr_search_condition[] = "(MEETING_YEAR = '$search_meeting_year')";
	if(trim($search_committee) == 1) $arr_search_condition[] = "(COMMITTEE_RESULT = 1)";
	if(trim($search_committee) == 2) $arr_search_condition[] = "(COMMITTEE_RESULT = 2)";
	if(trim($search_committee) == 3) $arr_search_condition[] = "(COMMITTEE_RESULT = 3)";
	 if ($search_meeting_date_min) {
		$temp_start =  save_date($search_meeting_date_min);
		$arr_search_condition[] = "(MEETING_DATE >= '$temp_start')";
	} // end if
	
	if ($search_meeting_date_max) {
		$temp_end =  save_date($search_meeting_date_max);
		$arr_search_condition[] = "(MEETING_DATE <= '$temp_end')";
	} // end if 
	
	if(trim($search_okp_time))  $arr_search_condition[] = "(OKP_MEETING_TIME = '$search_okp_time')";
	if(trim($search_okp_year))  $arr_search_condition[] = "(OKP_MEETING_YEAR = '$search_okp_year')";
	if(trim($search_okp_committee) == 1) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 1)";
	if(trim($search_okp_committee) == 2) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 2)";
	if(trim($search_okp_committee) == 3) $arr_search_condition[] = "(OKP_COMMITTEE_RESULT = 3)";
	 if ($search_okp_date_min) {
		$temp_start =  save_date($search_okp_date_min);
		$arr_search_condition[] = "(OKP_MEETING_DATE >= '$temp_start')";
	} // end if
	
	if ($search_okp_date_max) {
		$temp_end =  save_date($search_okp_date_max);
		$arr_search_condition[] = "(OKP_MEETING_DATE <= '$temp_end')";
	} // end if 
	
	$search_condition = "";
	if(count($arr_search_condition)) 	$search_condition 	= " where " . implode(" and ", $arr_search_condition);

	$search_condition = str_replace(" where ", " and ", $search_condition);
	$cmd =" select count(a.JETHRO_ID) as count_data 
					from 		PER_JETHRO a, PER_ORG b
					where		a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition  ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$count_data = $data[count_data];	
//echo "$count_data : $cmd";
	
//echo "$count_data : $cmd";
	

  	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;

	if($order_by==1){	//ชื่อ-สกุล
		$order_str = "ORDER BY MEETING_YEAR $SortType[$order_by], MEETING_TIME $SortType[$order_by], POS_NO $SortType[$order_by]";
  	}elseif($order_by==4) {	//ตำแหน่ง - ระดับ
		$order_str = "ORDER BY AB_NAME  ".$SortType[$order_by];
  	} elseif($order_by==3) {	
		$order_str = "ORDER BY f.ORG_NAME ".$SortType[$order_by];
	} elseif($order_by==2) {	//ชื่อทุน / หลักสูตร
		$order_str =  "ORDER BY a.SCH_CODE  ".$SortType[$order_by];
	}elseif($order_by==5) {	//สถานศึกาษา
		$order_str = "ORDER BY INS_CODE ".$SortType[$order_by];
  	} elseif($order_by==6) {	//วันที่เริ่มศึกษา
		$order_str = "ORDER BY SC_STARTDATE  ".$SortType[$order_by];
	}elseif($order_by==7) {	//วันที่สิ้นสุดระยะเวลาศึกษา
		$order_str = "ORDER BY SC_ENDDATE ".$SortType[$order_by];
	}

	$company_name = "";
	if(!$search_ministry_name) $search_ministry_name = "ระดับกระทรวง";
	if($search_department_name) $search_department_name ="||".$search_department_name;
	$report_title = "การพิจารณากำหนดตำแหน่งระดับสูงของ $search_ministry_name $search_department_name";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "  ";
	
	include ("rpt_data_jethro_inquire_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_data_jethro_summary.rtf";

	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="A4";
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
	
	if($DPISDB=="odbc"){	
		$cmd = " select JETHRO_ID,a.ORG_ID,a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
					     COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME   
					from PER_JETHRO  a, PER_ORG b 
					where a.DEPARTMENT_ID=b.ORG_ID(+)
				    $search_condition
					$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "    select	JETHRO_ID,a.ORG_ID,a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
							COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME    
					from 		PER_JETHRO a, PER_ORG b
					where		a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition 
					$order_str ";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select JETHRO_ID,a.ORG_ID, a.DEPARTMENT_ID, MEETING_TIME,OKP_MEETING_TIME,MEETING_YEAR,OKP_MEETING_YEAR,POS_NO, MEETING_DATE,OKP_MEETING_DATE,OKP_COMMITTEE_RESULT,a.ORG_NAME as ORG_NAME,NEW_ORG_NAME,PL_NAME,NEW_PL_NAME,PM_NAME,NEW_PM_NAME,CL_NAME,NEW_CL_NAME, 
						COMMITTEE_RESULT, COMMITTEE_REMARK, b.ORG_NAME as DEPARTMENT_NAME  
					from PER_JETHRO a, PER_ORG b 
					where a.DEPARTMENT_ID=b.ORG_ID(+)
					$search_condition  
					$order_str ";
	} // end if

	$count_page_data = $db_dpis->send_cmd($cmd);
//echo "cmd=$cmd ($count_page_data)<br>";
//$db_dpis->show_error();

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

	if ($count_page_data) {
		//$current_list = "";
		$data_count = 0;
		$buff_org_id = 0;
        while ($data = $db_dpis->get_array()) {
			$data_count++;
			//$temp_JETHRO_ID = $data[JETHRO_ID];
			$ORG_ID = $data[ORG_ID];
			$TMP_ORG_NAME = trim($data[ORG_NAME]);
			$NEW_ORG_NAME = trim($data[NEW_ORG_NAME]);
			//$current_list .= ((trim($current_list))?", ":"") . "'" . $temp_JETHRO_ID ."'";
			$TMP_MEETING_TIME = $data[MEETING_TIME];
			$OKP_MEETING_TIME = $data[OKP_MEETING_TIME];
			$TMP_MEETING_YEAR = $data[MEETING_YEAR];
			$OKP_MEETING_YEAR = $data[OKP_MEETING_YEAR];			
			$TMP_POS_NO = $data[POS_NO];
			$TMP_MEETING_DATE = show_date_format($data[MEETING_DATE], 1);
			$OKP_MEETING_DATE = show_date_format($data[OKP_MEETING_DATE], 1);
			$OKP_COMMITTEE_RESULT = trim($data[OKP_COMMITTEE_RESULT]);
			if ($OKP_COMMITTEE_RESULT==1) $OKP_COMMITTEE_RESULT = "อนุมัติ";
			elseif ($OKP_COMMITTEE_RESULT==2) $OKP_COMMITTEE_RESULT = "ไม่อนุมัติ";
			elseif ($OKP_COMMITTEE_RESULT==3) $OKP_COMMITTEE_RESULT = "ยุบเลิก";
			$TMP_PL_NAME = trim($data[PL_NAME]);
			$NEW_PL_NAME = trim($data[NEW_PL_NAME]);
			$TMP_PM_NAME = trim($data[PM_NAME]);
			$NEW_PM_NAME = trim($data[NEW_PM_NAME]);
			$TMP_CL_NAME = trim($data[CL_NAME]);
			$NEW_CL_NAME = trim($data[NEW_CL_NAME]);
			$TMP_COMMITTEE_RESULT = trim($data[COMMITTEE_RESULT]);
			if ($TMP_COMMITTEE_RESULT==1) $TMP_COMMITTEE_RESULT = "ผ่าน";
			elseif ($TMP_COMMITTEE_RESULT==2) $TMP_COMMITTEE_RESULT = "ไม่ผ่าน";
			elseif ($TMP_COMMITTEE_RESULT==3) $TMP_COMMITTEE_RESULT = "ยุบเลิก";
			$TMP_COMMITTEE_REMARK = trim($data[COMMITTEE_REMARK]);
			$TMP_DEPARTMENT_NAME = $data[DEPARTMENT_NAME];

			if ($buff_org_id != $ORG_ID) {
				$buff_org_id = $ORG_ID;
				$arr_data = (array) null;
				if (!$FLAG_RTF)
				$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด	
				$fontformat1 = array("", "", "", "biu", "", "", "","biu", "", "", "", "", "", "","");
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $TMP_ORG_NAME; 
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $NEW_ORG_NAME;
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", $fontformat1, $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else 
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", $fontformat1, "000000", "");		//TRHBL
//						echo "out...pdf-x::".$pdf->x." , pdf-y::".$pdf->y."<br>";
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				if (!$FLAG_RTF)
				$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "14", "", "000000", "");		// เส้นปิดบรรทัด	
			}
				$arr_data = (array) null;
				$arr_data[] = $TMP_MEETING_TIME."/".$TMP_MEETING_YEAR;
				$arr_data[] = $TMP_MEETING_DATE;
				$arr_data[] = $TMP_POS_NO;
				if(!$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
				$arr_data[] = $TMP_PM_NAME;
				$arr_data[] = $TMP_PL_NAME;
				////////เช็คประเภทตำแหน่ง//////// {
				$cmd = "select	CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE
							from PER_CO_LEVEL
							where CL_ACTIVE=1 and CL_NAME = '$TMP_CL_NAME'";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LEVEL_NO_MIN =  trim($data1[LEVEL_NO_MIN]);
				$LEVEL_TYPE = substr(trim(strtoupper($LEVEL_NO_MIN)),0,1); 
				if($LEVEL_TYPE == "D") $LEVEL_TYPE = "อำนวยการ";
				else if($LEVEL_TYPE == "M") $LEVEL_TYPE = "บริหาร";
				else if($LEVEL_TYPE == "O") $LEVEL_TYPE = "ทั่วไป";
				else if($LEVEL_TYPE == "K") $LEVEL_TYPE = "วิชาการ";
				////////เช็คประเภทตำแหน่ง//////// }
				$arr_data[] = $LEVEL_TYPE;
				$arr_data[] = $TMP_CL_NAME;
				if(!$NEW_PM_NAME) $NEW_PM_NAME = $NEW_PL_NAME;
				$arr_data[] = $NEW_PM_NAME;
				$arr_data[] = $NEW_PL_NAME;
				////////เช็คประเภทตำแหน่ง//////// {
				$cmd = "select	CL_NAME, LEVEL_NO_MIN, LEVEL_NO_MAX, CL_ACTIVE
							from PER_CO_LEVEL
							where CL_ACTIVE=1 and CL_NAME = '$NEW_CL_NAME'";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$NEW_LEVEL_NO_MIN =  trim($data1[LEVEL_NO_MIN]);
				$NEW_LEVEL_TYPE = substr(trim(strtoupper($NEW_LEVEL_NO_MIN)),0,1); 
				if($NEW_LEVEL_TYPE == "D") $NEW_LEVEL_TYPE = "อำนวยการ";
				else if($NEW_LEVEL_TYPE == "M") $NEW_LEVEL_TYPE = "บริหาร";
				else if($NEW_LEVEL_TYPE == "O") $NEW_LEVEL_TYPE = "ทั่วไป";
				else if($NEW_LEVEL_TYPE == "K") $NEW_LEVEL_TYPE = "วิชาการ";
				////////เช็คประเภทตำแหน่ง//////// }
				$arr_data[] = $NEW_LEVEL_TYPE;
				$arr_data[] = $NEW_CL_NAME;
				$arr_data[] = $TMP_COMMITTEE_RESULT;
				$arr_data[] = $OKP_MEETING_TIME."/".$OKP_MEETING_YEAR;
				$arr_data[] = $OKP_MEETING_DATE;
				$arr_data[] = $OKP_COMMITTEE_RESULT;
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else 
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
//						echo "out...pdf-x::".$pdf->x." , pdf-y::".$pdf->y."<br>";
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			 
		}
		  
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********",7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********",7, "", "C", "", "14", "b", 0, 0);
			
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//		$RTF->close_section(); 
		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();
	}
      
?>


