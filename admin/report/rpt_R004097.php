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
		$line_join = "b.PL_CODE=f.PL_CODE";
		$i_name = "f.PL_NAME";
		$line_code = "b.PL_CODE";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$i_name = "f.PN_NAME";
		$line_code = "b.PN_CODE";
		 $line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$i_name = "f.EP_NAME";
		$line_code = "b.EP_CODE";
		 $line_search_code=trim($search_ep_code);
		 $line_search_name=trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$i_name = "f.TP_NAME";
		$line_code = "b.TP_CODE";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
	} // end if	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if

	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$search_level_no = trim($search_level_no);
	$search_condition = $search_condition1 = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if ($search_per_type==1)	$arr_search_condition[] = "(rtrim(a.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = $ALL_REPORT_TITLE;

//	include ("../report/rpt_condition3.php");
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
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
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);	
	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่ดำรงตำแหน่งใน ". $level_name ."  เรียงตามลำดับอาวุโส";
	$report_code = "R0497";
	include ("rpt_R004097_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
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

		$fname= "rpt_R004097_rtf.rtf";

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
		$pdf->SetFont('angsa','',12);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	//---ต้องมีระดับตำแหน่งของตัวมันเองด้วย ในกรณีเข้ามาถึงก็เป็นระดับตำแหน่งนี้เลย ไม่มีระดับก่อนหน้า
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O2','O1');
	$L["O3"]=array('08','07','06','O3','O2');
	$L["O4"]=array('09','08','O4','O3');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K2','K1');
	$L["K3"]=array('08','07','K3','K2');
	$L["K4"]=array('09','08','K4','K3');
	$L["K5"]=array('10','09','K5','K4');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D2','D1');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M2','M1');
	//----วน loop ตามระดับตำแหน่ง ที่เลือกมา -------------
	$arrkeep = array();
	for($i=0; $i < count($L[$search_level_no]); $i++){	
		$index=$L[$search_level_no][$i];		//$index=level no
		
		if($DPISDB=="odbc"){
				//หากต้องการ ให้นำ  and rtrim(e.LEVEL_NO)='$index' and (e.LEVEL_NO < a.LEVEL_NO) ต่อท้าย $search_condition
				$cmd = " select			a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
													c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
													MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER				
						   from			(
											(
												(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by  e.PER_ID, MAX(e.LEVEL_NO) desc, MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc, MIN(i.DC_ORDER), a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";
		}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
				//หากต้องการ ให้นำ   and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select 	distinct a.PER_CARDNO,e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 		
												a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER
									from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
									where a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
												and a.PER_ID=e.PER_ID(+) and b.PL_CODE=f.PL_CODE(+) 
												and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
									$search_condition and e.level_no is not null
									 group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
												 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
									 order by e.PER_ID, MAX(e.LEVEL_NO) desc, MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) asc, a.PER_SALARY desc, MIN(i.DC_ORDER),
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) asc, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) asc "; 
									//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
		}elseif($DPISDB=="mysql"){
				//หากต้องการ ให้นำ   and rtrim(e.LEVEL_NO)='$index'  ต่อท้าย $search_condition
				$cmd = " select			a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
													c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
													MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER	
						   from			(
											(
												(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and e.LEVEL_NO < a.LEVEL_NO)
													) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
						group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID, MAX(e.LEVEL_NO) desc, MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc, MIN(i.DC_ORDER), a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10)  ";
		}
/*****************		
		if($search_per_type==1){
			if($DPISDB=="odbc"){
				//หากต้องการ ให้นำ  and rtrim(e.LEVEL_NO)='$index' and (e.LEVEL_NO < a.LEVEL_NO) ต่อท้าย $search_condition
				$cmd = " select			a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
													c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
													MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER				
						   from			(
											(
												(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
													) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by  e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
			}elseif($DPISDB=="oci8"){	
				$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
				//หากต้องการ ให้นำ   and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select 	distinct a.PER_CARDNO,e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 		
												a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER
									from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_LINE f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
									where a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
												and a.PER_ID=e.PER_ID(+) and b.PL_CODE=f.PL_CODE(+) 
												and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
									$search_condition and e.level_no is not null
									 group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
												 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
									 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) asc,a.PER_SALARY desc,MIN(i.DC_ORDER),
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) asc,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) asc "; 
									//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
			}elseif($DPISDB=="mysql"){
				//หากต้องการ ให้นำ   and rtrim(e.LEVEL_NO)='$index'  ต่อท้าย $search_condition
				$cmd = " select			a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, 
													c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
													MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER	
						   from			(
											(
												(
													(
														(
															(
																(
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and e.LEVEL_NO < a.LEVEL_NO)
													) left join PER_LINE f on (b.PL_CODE=f.PL_CODE)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
						group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POS_NO, f.PL_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
						order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10)  ";
			}
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				//หากต้องการ ให้นำ  and e.LEVEL_NO in$arr_level_no[$search_level_no] ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index'  ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER					 
							  from			(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
				//หากต้องการ ให้นำ    and e.LEVEL_NO in$arr_level_no[$search_level_no]  ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
												c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER 
						   from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_POS_NAME f,
										PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
						   where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
										and a.PER_ID=e.PER_ID(+) and b.PN_CODE=f.PN_CODE(+)
										and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)
										$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
										 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
							 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
										SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";	
			}elseif($DPISDB=="mysql"){
				//หากต้องการ ให้นำ    and e.LEVEL_NO in $arr_level_no[$search_level_no]  ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER			
						   from			(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_POS_NAME f on (b.PN_CODE=f.PN_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEM_NO, f.PN_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									 LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
			}
		}elseif($search_per_type==3){
			if($DPISDB=="odbc"){
				//หากต้องการ ให้นำ    and e.LEVEL_NO in $arr_level_no[$search_level_no]  ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER	
						   from			(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";
			}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);			$search_condition1 = str_replace(" where ", " and ", $search_condition1);
				//หากต้องการ ให้นำ    and e.LEVEL_NO in$arr_level_no[$search_level_no]  ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 							
												c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER 			 
							   from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, PER_EMPSER_POS_NAME f,
										PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j
						   where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
										and a.PER_ID=e.PER_ID(+) and b.EP_CODE=f.EP_CODE(+)
										and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+)
										$search_condition and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
										 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE
							 order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
										 SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";		
			}elseif($DPISDB=="mysql"){
				//หากต้องการ ให้นำ    and e.LEVEL_NO in$arr_level_no[$search_level_no]  ไว้ก่อนหน้า $search_condition และ  and rtrim(e.LEVEL_NO)='$index' ต่อท้าย $search_condition
				$cmd = " select		a.PER_CARDNO,a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME as PL_NAME, a.LEVEL_NO, j.LEVEL_NAME, MAX(e.LEVEL_NO) as LEVEL_NOHIS, 
												c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE,LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER	
						   from			(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join PER_EMPSER_POS_NAME f on (b.EP_CODE=f.EP_CODE)
											) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
										) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
									) left join PER_LEVEL j (a.LEVEL_NO=j.LEVEL_NO)
									$search_condition  and e.level_no is not null
							group by a.PER_CARDNO,e.PER_ID,e.LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, b.POEMS_NO, f.EP_NAME, a.LEVEL_NO,
									 j.LEVEL_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE
							order by e.PER_ID,MAX(e.LEVEL_NO) desc,MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)) asc,MIN(i.DC_ORDER),a.PER_SALARY desc,
									LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";
			}
		} // end if
	} *****************/
		
		//echo $cmd."<br>===============<br>";
		//สร้าง query ใหม่ สำหรับข้อมูล record เดียว
		$cmd1=$cmd;
		$cmd1 = str_replace($search_condition,$search_condition1,$cmd1); 
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		
		//---สร้าง query ของแต่ละตัว แล้ววนข้อมูลมาอีกครั้ง
		$count_data = $db_dpis->send_cmd($cmd);

		while($data = $db_dpis->get_array()){		
			if($PER_ID == $data[PER_ID]) continue;
			//$person_count++;		

			$PER_ID = $data[PER_ID];
			//---เก็บ PER_ID ของคนนั้นลงใน array ถ้ามีชื่อในระดับตำแหน่งที่เรียงตามลำดับแรกมาแล้ว อันถัดไปไม่นำมาเก็บอีกต่อไปแล้ว
			if(!in_array($PER_ID,$arrkeep)){
				$arrkeep[] = $PER_ID;
				//=================================
				if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
					$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
				}
				$PER_CARDNO[$PER_ID] = trim($data[PER_CARDNO]);
				$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
				$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
				$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
				$POS_NO[$PER_ID] = trim($data[POS_NO]);
				$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
				$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
				$LEVEL_NOHIS = trim($data[LEVEL_NOHIS]);
				$ARRSORTLEVEL[$PER_ID]['per_sortlevel'] = $LEVEL_NOHIS;		//--------
				$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
				$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
				$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
				$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
				$ORG_ID_1=trim($data[ORG_ID_1]);
				$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
				$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
				if(trim($POH_EFFECTIVEDATE)){
					$ARRSORTLEVEL[$PER_ID]['poh_effectivedate'] = $POH_EFFECTIVEDATE;		//--------
					//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.LEVEL_NO และ e.POH_EFFECTIVEDATE)
					$PER_WORK[$PER_ID]=date_difference(date("Y-m-d"), trim($POH_EFFECTIVEDATE), "ymd");		//floor
					$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;		//$PER_EFFECTIVEDATE[$index][$PER_ID] =$POH_EFFECTIVEDATE;
					$arr_temp = explode("-", $POH_EFFECTIVEDATE);
					$POH_EFFECTIVEDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);	
				} // end if	
				$PER_SALARY[$PER_ID] = number_format(trim($data[PER_SALARY]));;
				$ARRSORTLEVEL[$PER_ID]['per_salary'] = $PER_SALARY[$PER_ID];	//--------
				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
				if(trim($PER_BIRTHDATE)){
					$ARRSORTLEVEL[$PER_ID]['per_birthdate'] = $PER_BIRTHDATE;	//--------
					$PER_AGE[$PER_ID]=date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "ymd");		//floor
					$arr_temp = explode("-", $PER_BIRTHDATE);
					$PER_BIRTHDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
				} // end if	
				$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
				if(trim($PER_RETIREDATE)){
					$arr_temp = explode("-", $PER_RETIREDATE);
					$PER_RETIREDATE2[$PER_ID] = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
				} // end if	
				//echo "<br>".$PER_CARDNO[$PER_ID]."<br>";
				//ประวัติการศึกษา
				if($DPISDB=="odbc"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,a.EDU_SEQ
							   from		
										(
											(
												(
												PER_EDUCATE a 
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
											) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										) lect join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
							   where	a.PER_ID=$PER_ID
							   order by 	a.EDU_SEQ desc ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, e.CT_NAME,a.EDU_SEQ
							   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
							   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE (+) and d.CT_CODE=e.CT_CODE
							   order by 	a.EDU_SEQ desc ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME, d.INS_CODE, d.INS_NAME, e.CT_NAME,a.EDU_SEQ
							   from		
										(
											(
												(
												PER_EDUCATE a 
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
											) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										) lect join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
							   where	a.PER_ID=$PER_ID
							   order by 	a.EDU_SEQ desc ";
				} // end if
				$db_dpis2->send_cmd($cmd);
				//echo $cmd;
				$PER_EDUCATE[$PER_ID] = "";
				$count=0;
				while($data2 = $db_dpis2->get_array()){
					$count++;
					if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= "*Enter*";
					$PER_EDUCATE[$PER_ID] .= $count . ". " . trim($data2[EN_SHORTNAME]) . " / " . trim($data2[EM_NAME]) . " / " . trim($data2[INS_NAME]);
					if (trim($data2[CT_NAME])!="ไทย") $PER_EDUCATE[$PER_ID] .= " / " . trim($data2[CT_NAME]);
				} // end while
				
				//ประวัติการอบรม
				if($DPISDB=="odbc"){
					$cmd = "select		top 2 a.TR_CODE, b.TR_NAME, b.TR_TYPE, a.TRN_NO, a.TRN_PLACE, a.TRN_STARTDATE, a.TRN_ENDDATE
							   from		PER_TRAINING a left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
							   where	a.PER_ID='$PER_ID'
							   order by a.TRN_STARTDATE DESC, a.TRN_ENDDATE DESC ";
				}elseif($DPISDB=="oci8"){
					$cmd = "select		a.TR_CODE, b.TR_NAME, b.TR_TYPE, a.TRN_NO, a.TRN_PLACE, a.TRN_STARTDATE, a.TRN_ENDDATE
								from per_training a, per_train b
								where a.TR_CODE=b.TR_CODE and a.PER_ID='$PER_ID' and ROWNUM<=2
								order by a.TRN_STARTDATE DESC, a.TRN_ENDDATE DESC ";
				}elseif($DPISDB=="mysql"){
					$cmd = "select	    a.TR_CODE, b.TR_NAME, b.TR_TYPE, a.TRN_NO, a.TRN_PLACE, a.TRN_STARTDATE, a.TRN_ENDDATE
							   from		PER_TRAINING a left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
							   where	a.PER_ID='$PER_ID'
							   order by a.TRN_STARTDATE DESC, a.TRN_ENDDATE DESC
							   LIMIT 2 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
				//echo $cmd;
				$PER_TRAIN[$PER_ID] = "";
				$count=0;
				while($data2 = $db_dpis2->get_array()){
					$count++;
					if($PER_TRAIN[$PER_ID]) $PER_TRAIN[$PER_ID] .= "*Enter*";

					if (trim($data2[TR_TYPE]) == "1") $PER_TRAIN[$PER_ID] .= $count . ". " . "อบรม ";
					elseif (trim($data2[TR_TYPE]) == "2") $PER_TRAIN[$PER_ID] .= $count . ". " . "ดูงาน ";
					elseif (trim($data2[TR_TYPE]) == "3") $PER_TRAIN[$PER_ID] .= $count . ". " . "สัมมนา ";

					$PER_TRAIN[$PER_ID] .= trim($data2[TR_NAME]) . " /";
					if (trim($data2[TR_TYPE]) == "1"){
						if (trim($data2[TRN_NO])!="") $PER_TRAIN[$PER_ID] .= " รุ่นที่ " . trim($data2[TRN_NO]) . " /";
					}

					if (trim($data2[TRN_PLACE])!="") $PER_TRAIN[$PER_ID] .= " " . trim($data2[TRN_PLACE]) . " /";

					$TRN_STARTDATE = trim($data2[TRN_STARTDATE]);
					$arr_temp = explode("-", $TRN_STARTDATE);
					$TRN_STARTDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);

					$TRN_ENDDATE = trim($data2[TRN_ENDDATE]);
					$arr_temp = explode("-", $TRN_ENDDATE);
					$TRN_ENDDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
					
					$PER_TRAIN[$PER_ID] .= " " .  $TRN_STARTDATE;
					if ($TRN_ENDDATE != "0  543") $PER_TRAIN[$PER_ID] .= " - " . $TRN_ENDDATE;
				} // end while

				if($DPISDB=="odbc"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a, PER_DECORATION b
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
							   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
				$ARRSORTLEVEL[$PER_ID]['per_decorate'] = $PER_DECORATE[$PER_ID];	//--------
				
				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$ORG_ID_1_NAME[$PER_ID]=$data_org[ORG_NAME];
			} //if(!in_array($PER_ID,$arrkeep)){
			//echo $PER_ID . " " . $PER_EDUCATE[$PER_ID] . "<BR>";
		} // end while

	/*******************************
		//การเรียงลำดับโดย... asort = น้อย->มาก / arsort = มาก->น้อย
		1. วันเข้าสู่ระดับ <
		2. ถ้า 1. เท่ากันหาเงินเดือน >
		3. ถ้า 2. เท่ากันหาวันบรรจุ <
		4. ถ้า 3. เท่ากันหาาวันเกิด <
		5. ถ้า 4. เท่ากันหาเครื่องราช >
	********************************/	
	
		$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');
		
		//แสดงรายการทั้งหมด
		$data_count = 0;
		$person_count = 0;
		foreach($PER_EFFECTIVEDATE as $key=>$value){		//[$LEVEL_NOHIS][$PER_ID]
			$LEVEL_NO=trim($key);
			//___asort($PER_EFFECTIVEDATE[$LEVEL_NO]);		//เรียงลำดับตาม ระดับตน. และวันที่เข้าสู่ระดับ
			foreach($PER_EFFECTIVEDATE[$LEVEL_NO] as $key2=>$value2){
				$PER_ID=trim($key2);
				//__arsort();
				
				$person_count++;		
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][order] = "$person_count.";
				$arr_content[$data_count][name] = $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
				$arr_content[$data_count][name] .= "*Enter*เกิด : ". str_repeat(" ", (($rpt_order_index + 1) * 2)) . $PER_BIRTHDATE2[$PER_ID];
				$arr_content[$data_count][name] .= "*Enter*อายุ : ". str_repeat(" ", (($rpt_order_index + 1) * 2)) . $PER_AGE[$PER_ID];
				$arr_content[$data_count][name] .= "*Enter*เกษียณ : ". str_repeat(" ", (($rpt_order_index + 1) * 2)) . $PER_RETIREDATE2[$PER_ID];
				$arr_content[$data_count][name] .= "*Enter*เงินเดือน : " . $PER_SALARY[$PER_ID];
				$arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
				$arr_content[$data_count][position] = $PL_NAME[$PER_ID];
				$level=level_no_format($LEVEL_NAME[$PER_ID]) . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
				$arr_content[$data_count][level] = substr($level,strpos($level,"ระดับ")+5);
				$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	//$value2
				$arr_content[$data_count][poh_effectivedate] .= "*Enter*(".$PER_WORK[$PER_ID].")";
		
				//หาระดับตำแหน่งนั้น--------------------
				$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
				$db_dpis3->send_cmd($cmd2);
				$levelname=$db_dpis3->get_array();
				//---------------------------------------------
				$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$LEVEL_NO?level_no_format($LEVEL_NO):"";
				$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
				$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
				
				$arr_content[$data_count][em_name]=$EM_NAME[$PER_ID];
				$arr_content[$data_count][train]=$PER_TRAIN[$PER_ID];
				$data_count++;
			} //end foreach($i=0; $i < count($value); $i++)
		} //end foreach
		
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
				$NAME_1 = $arr_content[$data_count][name];
				$NAME_2 = $arr_content[$data_count][educate];
				$NAME_3 = $arr_content[$data_count][position];
				$NAME_4 = $arr_content[$data_count][level];
				$NAME_5 = $arr_content[$data_count][poh_effectivedate];
				$NAME_6 = $arr_content[$data_count][train];
	
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME_1;
				$arr_data[] = $NAME_2;
				$arr_data[] = $NAME_3;
				$arr_data[] = $NAME_4;
				$arr_data[] = $NAME_5;
				$arr_data[] = $NAME_6;	
		
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
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
	}
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