<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
    if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_name = "f.PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
		 $line_title = " สายงาน";
		 $up_type = "เงินเดือน";
		 $type_code = "b.PT_CODE";
		 $select_type_code = ", b.PT_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_name = "f.PN_NAME";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
		$line_title = " ชื่อตำแหน่ง";
		$up_type = "ค่าจ้าง";		
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_name = "f.EP_NAME";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
		$line_title = " ชื่อตำแหน่ง";
		$up_type = "ค่าจ้าง";		
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_name = "f.TP_NAME";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
		$line_title = " ชื่อตำแหน่ง";
		$up_type = "ค่าจ้าง";		
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
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";	
	$order_by = "";	
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;

			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure == 1)  $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure == 0) $order_by .= "b.ORG_ID";
		else if($select_org_structure == 1) $order_by .= "a.ORG_ID";
	}
	if(!trim($select_list)){
		if($select_org_structure == 0) $select_list .= "b.ORG_ID";
		else if($select_org_structure == 1) $select_list .= "a.ORG_ID";
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if ($BKK_FLAG==1) {
		$arr_search_condition[] = "(e.MOV_CODE in ('030', '052', '013', '032') or (e.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and SM_CODE in ('2', '3')) or e.SAH_PERCENT_UP > 0)";
	} else {
		$arr_search_condition[] = "(e.MOV_CODE in ('21310','21351','21320','21352','214','21410') or e.SAH_PERCENT_UP > 0)";	//ค้นหาเฉพาะเลื่อนขั้นเงินเดือน 0.5 และ 1 ขั้น และเต็มขั้น(งดเลื่อน)
	}
	
	if($DPISDB=="odbc"){
		if($SALQ_TYPE==1){	//รอบแรก เม.ย.
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543) ."-04-01')";
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-09-30')"; 
		}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543) ."-10-01')";
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543 + 1) ."-03-31')"; 
		}
	}elseif($DPISDB=="oci8"){	
		if($SALQ_TYPE==1){	//รอบแรก เม.ย.
			$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543) ."-04-01')";
			$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543) ."-09-30')"; 
		}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
			$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543) ."-10-01')";
			$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543 + 1) ."-03-31')"; 
		}
	}elseif($DPISDB=="mysql"){
		if($SALQ_TYPE==1){	//รอบแรก เม.ย.
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543) ."-04-01')";
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-09-30')"; 
		}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543) ."-10-01')";
			$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543 + 1) ."-03-31')"; 
		}
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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R006018_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006018_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	if($SALQ_TYPE==1){
		$sal_type = "รอบแรก 1 เมษายน $search_budget_year";
	}elseif($SALQ_TYPE==2){
		$sal_type = "รอบสอง 1 ตุลาคม $search_budget_year";
	}
	$report_title = "รายชื่อ$PERSON_TYPE[$search_per_type] เพื่อพิจารณาเลื่อนขั้น$up_type $sal_type ";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R6018";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

/*
	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year;
		global $SALQ_TYPE,$NUMBER_DISPLAY;
        $a=(($NUMBER_DISPLAY==2)?convert2thaidigit(0.5):0.5);      
		$b=(($NUMBER_DISPLAY==2)?convert2thaidigit(1):1);    
		 
		$this_year = substr(($search_budget_year),2);
		if($SALQ_TYPE==1){	//รอบแรก เม.ย.
			$absent_period="1 ต.ค. ".($this_year-1)."- 31 มี.ค. ".($this_year);
		}else{
			$absent_period="1 เม.ย. ".$this_year."- 30 ก.ย. ".$this_year;
		}

		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - นามสกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สังกัด/ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] +$heading_width[4] +$heading_width[5] +$heading_width[6],7,"ประวัติการเลื่อนขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] +$heading_width[8] +$heading_width[9] +$heading_width[10] ,7,"ประวัติการลา".(($NUMBER_DISPLAY==2)?convert2thaidigit($absent_period):$absent_period),'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12]+$heading_width[13] ,7,"เลื่อน $a ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[14]+$heading_width[15] ,7,"เลื่อน $b ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		
		for($i=4; $i>0; $i--){ 	//ย้อนหลัง 4 ปี
			$j++;
			$pdf->Cell($heading_width[$j+2] ,7,"ปี ".substr((($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - $i)):($search_budget_year - $i)),2),'LTBR',0,'C',1);
		} // end for
		$pdf->Cell($heading_width[7] ,7,"ป",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"พ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ส",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"ใช้เงิน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"ใช้เงิน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"",'LBR',1,'C',1);
	} // function		
*/
	//หาประวัติจำนวนขั้นที่ได้เลื่อน (ทุกขั้น)
	function count_salpromote_level($search_year,$PER_ID){
		global $DPISDB, $db_dpis2;
		global $SALQ_TYPE,$search_budget_year, $select_org_structure, $BKK_FLAG;	//($search_budget_year - 543)

		$sum_salpro_level = 0;

		$search_condition = "";
		//ค้นหาเฉพาะประเภทการเคลื่อนไหวแบบเลื่อนขั้นเงินเดือน ... ขั้น
		if ($BKK_FLAG==1) {
			$search_mov_code = "and (psh.MOV_CODE in ('030', '052', '013', '032', '031', '033', '014', '034') or (psh.MOV_CODE in ('14', '25', '52', '53', '044', '050', '059', '20020', '20021', '20027') and SM_CODE in ('2', '3', '4', '5')))";
		} else {
			$search_condition = "and (psh.MOV_CODE in ('21310','21351','21320','21352','21330','21353','21340','21354')) ";
		}

		/***
		//ค้นหาจำนวนขั้นจากประวัติการเลื่อนขั้น จากรอบการเลื่อนขั้นที่เลือกมา ???
		if($DPISDB=="odbc" || $DPISDB=="mysql"){
			if($SALQ_TYPE==1){	//รอบแรก เม.ย.
				$search_condition .= " and (LEFT(trim(SAH_EFFECTIVEDATE),10) >= '". ($search_year) ."-04-01') and (LEFT(trim(SAH_EFFECTIVEDATE),10) <= '". ($search_year) ."-09-30')"; 
			}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
				$search_condition .= " and (LEFT(trim(SAH_EFFECTIVEDATE),10) >= '". ($search_year) ."-10-01') and (LEFT(trim(SAH_EFFECTIVEDATE),10) <= '". ($search_year+1) ."-03-01')"; 
			}
		}elseif($DPISDB=="oci8"){
			if($SALQ_TYPE==1){	//รอบแรก เม.ย.
				$search_condition .= " and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_year) ."-04-01') and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) <= '". ($search_year) ."-09-30')"; 
			}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
				$search_condition .= " and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_year) ."-10-01') and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) <= '". ($search_year+1) ."-03-01')"; 
			}
		} //end if
		***/
		//ค้นหาจำนวนขั้นทั้งหมดในปีนั้น
		if($DPISDB=="odbc" || $DPISDB=="mysql"){
			$search_condition .= " and (LEFT(trim(SAH_EFFECTIVEDATE),10) >= '". ($search_year) ."-01-01') and (LEFT(trim(SAH_EFFECTIVEDATE),10) <= '". ($search_year) ."-12-31')"; 
		}elseif($DPISDB=="oci8"){
			$search_condition .= " and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_year) ."-01-01') and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) <= '". ($search_year) ."-12-31')"; 
		}

		if($DPISDB=="odbc"){
			$cmd = " SELECT 	SAH_EFFECTIVEDATE, psh.MOV_CODE, SAH_SALARY, SAH_ENDDATE 
							 FROM		PER_SALARYHIS psh, PER_MOVMENT pm 
							 WHERE		psh.PER_ID=$PER_ID and psh.MOV_CODE=pm.MOV_CODE
							 					$search_condition
							ORDER BY	 LEFT(SAH_EFFECTIVEDATE,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";	
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 	SAH_EFFECTIVEDATE, psh.MOV_CODE, SAH_SALARY, SAH_ENDDATE 
							from 		PER_SALARYHIS psh, PER_MOVMENT pm 
							where 	psh.PER_ID=$PER_ID and psh.MOV_CODE=pm.MOV_CODE
											$search_condition
							order by 	SUBSTR(SAH_EFFECTIVEDATE,1,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " SELECT 	SAH_EFFECTIVEDATE, psh.MOV_CODE, SAH_SALARY, SAH_ENDDATE 
							 FROM		PER_SALARYHIS psh, PER_MOVMENT pm 
							 WHERE		psh.PER_ID=$PER_ID and psh.MOV_CODE=pm.MOV_CODE
							 					$search_condition	
						    ORDER BY	 LEFT(SAH_EFFECTIVEDATE,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";	
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		while($data = $db_dpis2->get_array()){

			//หาขั้นที่ได้เลื่อนเพื่อนำมารวมกัน
			$MOV_CODE = trim($data[MOV_CODE]);
			if ($BKK_FLAG==1) {
				if ($MOV_CODE == '030' || $MOV_CODE == '052') {
					$PR_LEVEL[] = 0.5;
				} elseif ($MOV_CODE == '013' || $MOV_CODE == '032') {
					$PR_LEVEL[] = 1.0;
				} elseif ($MOV_CODE == '031' || $MOV_CODE == '033') {
					$PR_LEVEL[] = 1.5;
				} elseif ($MOV_CODE == '014' || $MOV_CODE == '034') {
					$PR_LEVEL[] = 2.0;
				} elseif ($MOV_CODE == '14' || $MOV_CODE == '25' || $MOV_CODE == '52' || $MOV_CODE == '53' || $MOV_CODE == '044' || $MOV_CODE == '050' || 
					$MOV_CODE == '059' || $MOV_CODE == '20020' || $MOV_CODE == '20021' || $MOV_CODE == '20027') {
					if ($SM_CODE == '2') {
						$PR_LEVEL[] = 0.5;
					} elseif ($SM_CODE == '3') {
						$PR_LEVEL[] = 1.0;
					} elseif ($SM_CODE == '4') {
						$PR_LEVEL[] = 1.5;
					} elseif ($SM_CODE == '5') {
						$PR_LEVEL[] = 2.0;
					} else {
						$PR_LEVEL[] = 0;
					}
				} else {
					$PR_LEVEL[] = 0;
				}
			} else { 
				if ($MOV_CODE == '21310' || $MOV_CODE == '21351') {
					$PR_LEVEL[] = 0.5;
				} elseif ($MOV_CODE == '21320' || $MOV_CODE == '21352') {
					$PR_LEVEL[] = 1.0;
				} elseif ($MOV_CODE == '21330' || $MOV_CODE == '21353') {
					$PR_LEVEL[] = 1.5;
				} elseif ($MOV_CODE == '21340' || $MOV_CODE == '21354') {
					$PR_LEVEL[] = 2.0;
				} else {
					$PR_LEVEL[] = 0;
				}
			} // end if
			//$SAH_SALARY = $data[SAH_SALARY];	//เงินที่ได้เลื่อนขั้น
		} //end while
			//รวมจำนวนขั้นทั้งหมดที่ได้เลื่อนในปีนั้น
			if(array_sum($PR_LEVEL) > 0)	$sum_salpro_level = array_sum($PR_LEVEL);

	return $sum_salpro_level;
	}

	//หาจำนวนวันลา แยกแต่ละประเภท
	function count_absent_days($ab_code,$PER_ID){
		global $DPISDB, $db_dpis2;
		global $SALQ_TYPE,$search_budget_year, $select_org_structure;
		
		$sum_abs_days = 0;
		
		$search_condition = "";
		//ค้นหาช่วงวันที่หยุด จากรอบการเลื่อนขั้น
		if($DPISDB=="odbc" || $DPISDB=="mysql"){
			if($SALQ_TYPE==1){	//รอบแรก เม.ย.
				$search_condition = " and (LEFT(trim(pah.ABS_STARTDATE),10) >= '". ($search_budget_year - 543 - 1) ."-10-01') and (LEFT(trim(pah.ABS_STARTDATE),10) <= '". ($search_budget_year - 543) ."-03-31')"; 
			}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
				$search_condition = " and (LEFT(trim(pah.ABS_STARTDATE),10) >= '". ($search_budget_year - 543) ."-04-01') and (LEFT(trim(pah.ABS_STARTDATE),10) <= '". ($search_budget_year - 543) ."-09-30')"; 
			}
		}elseif($DPISDB=="oci8"){
			if($SALQ_TYPE==1){	//รอบแรก เม.ย.
				$search_condition = " and (SUBSTR(trim(pah.ABS_STARTDATE), 1, 10) >= '". ($search_budget_year - 543 -1) ."-10-01') and (SUBSTR(trim(pah.ABS_STARTDATE), 1, 10) <= '". ($search_budget_year - 543) ."-03-31')"; 
			}elseif($SALQ_TYPE==2){ //รอบสอง ต.ค.
				$search_condition = " and (SUBSTR(trim(pah.ABS_STARTDATE), 1, 10) >= '". ($search_budget_year - 543) ."-04-01') and (SUBSTR(trim(pah.ABS_STARTDATE), 1, 10) <= '". ($search_budget_year - 543) ."-09-30')"; 
			}
		} //end if
		
		//หาจำนวนวันทั้งหมดที่ลา แต่ละประเภทการลา
		if($DPISDB=="odbc"){
			$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAYS
							from 		PER_ABSENTHIS pah, PER_ABSENTTYPE pat
							where 	pah.AB_CODE=pat.AB_CODE and 
											PER_ID=$PER_ID and pah.AB_CODE=$ab_code
											$search_condition
							order by 	ABS_STARTDATE ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAYS
							from 		PER_ABSENTHIS pah, PER_ABSENTTYPE pat
							where 	pah.AB_CODE=pat.AB_CODE and 
											PER_ID=$PER_ID and pah.AB_CODE=$ab_code
											$search_condition
							order by 	ABS_STARTDATE ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	sum(ABS_DAY) as SUM_ABS_DAYS
							from 		PER_ABSENTHIS pah, PER_ABSENTTYPE pat
							where 	pah.AB_CODE=pat.AB_CODE and 
											PER_ID=$PER_ID and pah.AB_CODE=$ab_code
											$search_condition
							order by 	ABS_STARTDATE ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		$data = $db_dpis2->get_array();
		if($data[SUM_ABS_DAYS] > 0)	$sum_abs_days = $data[SUM_ABS_DAYS];
	
	//echo "<br>$cmd<br>";
	return $sum_abs_days;
	} //end function

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $ORG_ID_1;
				
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
				case "ORG_1" :	
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID_1 = 0 or a.ORG_ID_1 is null)";
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
		global $ORG_ID, $ORG_ID_1;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :	
					$ORG_ID_1 = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY $select_type_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
											$search_condition
						 order by		$order_by, $position_no_name, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, TO_NUMBER($position_no) as POS_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID and $line_join(+)
											$search_condition
						 order by		$order_by, $position_no_name, TO_NUMBER($position_no), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO, 
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY $select_type_code
						 from			(
												(
													(
														(
															PER_PERSONAL a 
															left join $position_table b on ($position_join) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
												) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
											) left join $line_table f on ($line_join)
											$search_condition
						 order by		$order_by, $position_no_name, $position_no, e.SAH_EFFECTIVEDATE desc ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();		
//	echo "<br>$cmd<br>";

	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
	if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			//แสดงหน่วยงานต่างๆ
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if
                   if($ORG_NAME=="" || $ORG_NAME=="NULL"  || $ORG_NAME =="-")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][name] = $ORG_NAME;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;

				case "ORG_1" :
					if($ORG_ID_1 != trim($data[ORG_ID_1])){
						$ORG_ID_1 = trim($data[ORG_ID_1]);
						if($ORG_ID_1 != ""){
							if($select_org_structure==0) $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if
                  if($ORG_NAME_1=="" || $ORG_NAME_1=="NULL"   || $ORG_NAME_1 =="-")	$ORG_NAME_1="[ไม่ระบุ$ORG_TITLE1]";
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG_1";
//						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][name] = $ORG_NAME_1;
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						$data_count++;
					} // end if
				break;
			} // end switch case


			//echo "<br>".$rpt_order_index." - ".(count($arr_rpt_order) - 1)."<br>";

			if($rpt_order_index == (count($arr_rpt_order) - 1)){
				if($PER_ID != $data[PER_ID]){
					$data_row++;
							
					$PER_ID = $data[PER_ID];
					$PN_NAME = trim($data[PN_NAME]);
					$PER_NAME = trim($data[PER_NAME]);
					$PER_SURNAME = trim($data[PER_SURNAME]);
					$POS_NO_NAME = trim($data[POS_NO_NAME]);
					if (substr($POS_NO_NAME,0,4)=="กปด.")
						$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
					else
						$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
					$PL_CODE = trim($data[PL_CODE]);
					$PL_NAME = trim($data[PL_NAME]);
					$LEVEL_NO = $data[LEVEL_NO];
					$PER_SALARY = $data[PER_SALARY];

					if(trim($type_code)){
						$PT_CODE = trim($data[$type_code]);
						$cmd = " select PT_NAME from PER_TYPE where	PT_CODE='$PT_CODE' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PT_NAME = trim($data3[PT_NAME]);
					}
					
					$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
					$db_dpis3->send_cmd($cmd);
//					$db_dpis->show_error();
					$data3 = $db_dpis3->get_array();
					$LEVEL_NAME=$data3[LEVEL_NAME];
					$POSITION_LEVEL = $data3[POSITION_LEVEL];
					if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
					$arr_content[$data_count][type] = "DETAIL";
					$arr_content[$data_count][order] = $data_row;
//					$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
					$arr_content[$data_count][pos_no] = $POS_NO;
					$arr_content[$data_count][position] = $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");

				} // end if	 PER_ID	

				//หาขั้นที่เลื่อนจากประวัติการเลื่อนขั้น
				for($i=4; $i>0; $i--){ 	//ย้อนหลัง 4 ปี
					$year = ($search_budget_year - $i);
					$arr_content[$data_count]["count_".$year] = count_salpromote_level(($year-543),$PER_ID);
				}
				//หาประวัติการลาแยกตามประเภท
				$arr_content[$data_count][count_absent1] =	count_absent_days('01',$PER_ID);	//ป่วย
				$arr_content[$data_count][count_absent2] = 	count_absent_days('02',$PER_ID);	//กิจ
				$arr_content[$data_count][count_absent3] = 	count_absent_days('04',$PER_ID);	//พักผ่อน
				$arr_content[$data_count][count_absent4] = 	count_absent_days('10',$PER_ID);	//สาย

				$arr_content[$data_count][salary] = number_format($PER_SALARY?$PER_SALARY:0);
			//--------------------------------------------------------------------------------------	
			//วันที่มีผลบังคับใช้ ของราบการเลื่อนนั้น
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);		
			$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
			$SAH_YEAR = $arr_temp[0] + 543 + 1;
			$SAH_MONTH = $arr_temp[1] + 0;
			$SAH_DATE = $arr_temp[2] + 0;
			$SAH_EFFECTIVEDATE = show_date_format($SAH_EFFECTIVEDATE,$DATE_DISPLAY);
			
			$CMD_NOTE1="";
			//แยกตามประเภทการเลื่อนขั้นเงินเดือน (กี่ขั้น)
			//เลือกเฉพาะ 0.5 และ 1 ขั้น
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_SALARY = $data[SAH_SALARY];	//เงินที่ได้เลื่อนขั้น
			if ($BKK_FLAG==1) {
				if ($MOV_CODE == '030' || $MOV_CODE == '052') {
					$PR_LEVEL = 0.5;
					$SAH_SALARY05[$PER_ID] += $SAH_SALARY;
				} elseif ($MOV_CODE == '013' || $MOV_CODE == '032') {
					$PR_LEVEL = 1.0;
					$SAH_SALARY10[$PER_ID] += $SAH_SALARY;
//				} elseif ($MOV_CODE == '' || $MOV_CODE == '') {
//					$CMD_NOTE1 = "เต็มขั้น";
				} elseif ($MOV_CODE == '14' || $MOV_CODE == '25' || $MOV_CODE == '52' || $MOV_CODE == '53' || $MOV_CODE == '044' || $MOV_CODE == '050' || 
					$MOV_CODE == '059' || $MOV_CODE == '20020' || $MOV_CODE == '20021' || $MOV_CODE == '20027') {
					if ($SM_CODE == '2') {
						$PR_LEVEL = 0.5;
						$SAH_SALARY05[$PER_ID] += $SAH_SALARY;
					} elseif ($SM_CODE == '3') {
						$PR_LEVEL = 1.0;
						$SAH_SALARY10[$PER_ID] += $SAH_SALARY;
//					} elseif ($SM_CODE == '') {
//						$CMD_NOTE1 = "เต็มขั้น";
					} else {
						$PR_LEVEL = 0;
					}
				} else {
					$PR_LEVEL = 0;
				}
			} else { 
				if ($MOV_CODE == '21310' || $MOV_CODE == '21351') {
					$PR_LEVEL = 0.5;
					$SAH_SALARY05[$PER_ID] += $SAH_SALARY;
				} elseif ($MOV_CODE == '21320' || $MOV_CODE == '21352') {
					$PR_LEVEL = 1.0;
					$SAH_SALARY10[$PER_ID] += $SAH_SALARY;
				} elseif ($MOV_CODE == '214' || $MOV_CODE == '21410') {
					$CMD_NOTE1 = "เต็มขั้น";
				} else {
					$PR_LEVEL = 0;
				}
			} // end if
			
			if ($search_per_type==1) {
				$MAX_LAYER_NO = $MAX_LAYER_SALARY = $non_promote_text = "";
				// update เงินเดือนใหม่ เมื่อมีการเลื่อนขั้น
				// เช็ค PT_CODE ว่ารับเงินเดือนระดับ ทั่วไป หรือเป็น ผู้บริหารระดับสูง
				$cmd = "	select a.PT_CODE, b.LAYER_TYPE from PER_POSITION a, PER_LINE b, PER_PERSONAL c 
						where PER_ID=$PER_ID and a.POS_ID=c.POS_ID and a.PL_CODE=b.PL_CODE ";	
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PT_CODE = trim($data1[PT_CODE]);
				// check ค่า $PT_CODE โดยดูจาก table PER_TYPE 
				if ($PT_CODE == 32 && $LEVEL_NO_SALARY=="11")	$LAYER_TYPE = 2;		// ผู้บริหารระดับสูง
				else											$LAYER_TYPE = 1;		// ทั่วไป	
				if ($RPT_N) $LAYER_TYPE = trim($data1[LAYER_TYPE]);
				
				if ($RPT_N) {
					$cmd = " select 	LAYER_NO, a.LEVEL_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY, LAYER_SALARY_MIDPOINT, 
							LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_SALARY_FULL, LAYER_SALARY_TEMP 
							from 	PER_PERSONAL a, PER_LAYER b
							where 	a.PER_ID=$PER_ID and a.LEVEL_NO=b.LEVEL_NO and LAYER_SALARY=PER_SALARY and 
				 					LAYER_TYPE=$LAYER_TYPE 
							order by 	LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$CAL_LAYER_NO = trim($data1[LAYER_NO]);
					$CAL_LEVEL_NO = trim($data1[LEVEL_NO]);
					$CAL_LEVEL_NO_SALARY = trim($data1[LEVEL_NO_SALARY]);
					$PER_SALARY = $data1[PER_SALARY] + 0;
					$CAL_LAYER_SALARY_MIDPOINT = $data1[LAYER_SALARY_MIDPOINT] + 0;
					$CAL_LAYER_SALARY_MIDPOINT1 = $data1[LAYER_SALARY_MIDPOINT1] + 0;
					$CAL_LAYER_SALARY_MIDPOINT2 = $data1[LAYER_SALARY_MIDPOINT2] + 0;
					$CAL_LAYER_SALARY_FULL = $data1[LAYER_SALARY_FULL] + 0;
					$CAL_LAYER_SALARY_TEMP = $data1[LAYER_SALARY_TEMP] + 0;
					$SALP_SPSALARY = 0;
					$SUM_SALARY05 = $CAL_LAYER_SALARY_MIDPOINT;
					$SUM_SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1;
					if ($CAL_LAYER_SALARY_MIDPOINT <= $PER_SALARY) {
						$SUM_SALARY05 = $CAL_LAYER_SALARY_FULL;
						$SALARY05 = $CAL_LAYER_SALARY_MIDPOINT;
						$CMD_NOTE1 = "เต็มขั้น";
					} else {
						$SALARY05 = $CAL_LAYER_SALARY_MIDPOINT - $PER_SALARY;
					}
					if ($CAL_LAYER_SALARY_MIDPOINT1 <= $PER_SALARY) {
						$SUM_SALARY10 = $CAL_LAYER_SALARY_FULL;
						$SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1;
						$CMD_NOTE1 = "เต็มขั้น";
					} else {
						$SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1 - $PER_SALARY;
					}
				} else {
					$cmd = " select 	LAYER_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY 
							from 	PER_PERSONAL a, PER_LAYER b
							where 	a.PER_ID=$PER_ID and LAYER_TYPE=$LAYER_TYPE and a.LEVEL_NO_SALARY=b.LEVEL_NO and 
									LAYER_SALARY>=PER_SALARY 
							order by 	LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$UP_SALP_LAYER = $data1[LAYER_NO] + $SALP_LEVEL;
					$LEVEL_NO_SALARY = $data1[LEVEL_NO_SALARY];
					// select ว่า LAYER_NO ที่เพิ่มขึ้นเกินจำนวน LAYER_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
					// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
					$cmd = " select LAYER_NO, LAYER_SALARY
							from PER_LAYER where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_ACTIVE=1 
							order by LAYER_NO desc";
					$count_layer_no = $db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$MAX_LAYER_NO = trim($data2[LAYER_NO]) + 0;
					$MAX_LAYER_SALARY = trim($data2[LAYER_SALARY]);
	//echo ":: UP_SALP_LAYER=$UP_SALP_LAYER || LEVEL_NO=$data1[LEVEL_NO_SALARY] || LAYER_TYPE=$LAYER_TYPE ||	MAX_LAYER_NO=$MAX_LAYER_NO<br>";
					if ($UP_SALP_LAYER > $MAX_LAYER_NO) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน max layer
						$UP_SALP_LAYER = $MAX_LAYER_NO;		
						$non_promote_text .= "alert('เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น');";
					} else { 											// ถ้าไม่เกินจำนวน max layer
						$cmd = " select LAYER_SALARY from PER_LAYER 
								  where LAYER_TYPE=$LAYER_TYPE and LAYER_NO=$UP_SALP_LAYER and LEVEL_NO='$data1[LEVEL_NO_SALARY]' 
								  order by LAYER_SALARY ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();				
						$SALP_SALARY_NEW = $data1[LAYER_SALARY] + 0;
						$SUM_SALARY05 = $SALP_SALARY_NEW;
						$SUM_SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1;
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SUM_SALARY05 = $CAL_LAYER_SALARY_FULL;
							$SALARY05 = $CAL_LAYER_SALARY_MIDPOINT;
							$CMD_NOTE1 = "เต็มขั้น";
						} else {
							$SALARY05 = $CAL_LAYER_SALARY_MIDPOINT - $PER_SALARY;
						}
						if ($CAL_LAYER_SALARY_MIDPOINT1 <= $PER_SALARY) {
							$SUM_SALARY10 = $CAL_LAYER_SALARY_FULL;
							$SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1;
							$CMD_NOTE1 = "เต็มขั้น";
						} else {
							$SALARY10 = $CAL_LAYER_SALARY_MIDPOINT1 - $PER_SALARY;
						}
						// echo ":: SALP_SALARY_NEW = $SALP_SALARY_NEW<br>";	
					}	// end if ($UP_SALP_LAYER > $MAX_LAYER_NO) 
				}
			} elseif ($search_per_type==2) {
				// คำนวนผลการเลื่อนขั้นเงินเดือน
				$cmd = " select c.PG_CODE, LAYERE_NO, LAYERE_SALARY   
							  from 	PER_PERSONAL a, PER_POS_EMP b, PER_POS_NAME c, PER_LAYEREMP d 
							  where 	a.PER_ID=$PER_ID and a.PER_SALARY=LAYERE_SALARY and a.POEM_ID=b.POEM_ID and 
							  		b.PN_CODE=c.PN_CODE and c.PG_CODE=d.PG_CODE 
							  order by LAYERE_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$UP_SALP_LAYER = $data1[LAYERE_NO] + $SALP_LEVEL;

				// select ว่า LAYERE_NO ที่เพิ่มขึ้นเกินจำนวน LAYERE_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
				// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
				$cmd = " select LAYERE_NO, LAYERE_SALARY
						from PER_LAYEREMP where PG_CODE='$data1[PG_CODE]' and LAYERE_ACTIVE=1 order by LAYERE_NO desc";
				$count_layer_no = $db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MAX_LAYERE_NO = trim($data2[LAYERE_NO]);
				$MAX_LAYERE_SALARY = trim($data2[LAYERE_SALARY]);
				if ($UP_SALP_LAYER > $MAX_LAYERE_NO) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน max layer
					$UP_SALP_LAYER = $MAX_LAYERE_NO;
					$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
				} else {												// ถ้าไม่เกินจำนวน max layer
					$cmd = " select LAYERE_SALARY from PER_LAYEREMP
							  where PG_CODE='$data1[PG_CODE]' and LAYERE_NO=$UP_SALP_LAYER   
							  order by LAYERE_SALARY ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();				
					$SALP_SALARY_NEW = $data1[LAYERE_SALARY] + 0;			
					if ($SALP_LEVEL == 1 && $SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
				}
			} elseif ($search_per_type==3) {
				// คำนวนผลการเลื่อนขั้นเงินเดือน			
				$cmd = " select c.EG_CODE, LAYERES_NO, LAYERES_SALARY   
							  from PER_PERSONAL a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c, PER_LAYER_EMPSER d 
							  where a.PER_ID=$PER_ID and a.POEMS_ID=b.POEMS_ID and b.PN_CODE=c.PN_CODE and c.EG_CODE=d.EG_CODE
							  order by LAYERES_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$UP_SALP_LAYER = $data1[LAYERES_NO] + $SALP_LEVEL;

				// select ว่า LAYER_NO ที่เพิ่มขึ้นเกินจำนวน LAYER_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
				// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
				$cmd = " select LAYER_NO, LAYER_SALARY
						from PER_LAYER where LEVEL_NO='$LEVEL_NO' and LAYER_ACTIVE=1 order by LAYER_NO desc";
				$count_layer_no = $db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$MAX_LAYER_NO = trim($data1[LAYER_NO]);
				$MAX_LAYER_SALARY = trim($data1[LAYER_SALARY]);
				if ($UP_SALP_LAYER > $MAX_LAYER_NO) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน max layer
					$UP_SALP_LAYER = $MAX_LAYER_NO;
					$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
				} else {												// ถ้าไม่เกินจำนวน max layer
					$cmd = " select LAYERES_SALARY from PER_LAYER_EMPSER
								  where EG_CODE=$data1[EG_CODE] and LAYERES_NO='$UP_SALP_LAYER'  
								  order by LAYERES_SALARY ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();				
					$SALP_SALARY_NEW = $data1[LAYERES_SALARY] + 0;
				}
			} elseif ($search_per_type==4) {
				
			}

//			$SUM_SALARY05	= ($PER_SALARY+$SAH_SALARY05[$PER_ID]);	//เงินเดือนเลื่อน 0.5 ขั้น = เงินเดือน+เงินเลื่อน 0.5
//			$SUM_SALARY10	= ($PER_SALARY+$SAH_SALARY10[$PER_ID]);	//เงินเดือนเลื่อน 1 ขั้น = เงินเดือน+เงินเลื่อน 1

			$arr_content[$data_count][sum_salary05] = number_format($SUM_SALARY05?$SUM_SALARY05:0);	//รวมเงินที่ได้ = เงินเดือนปัจจุบัน + เงินเลื่อน 0.5 ขั้น
			$arr_content[$data_count][count_sah_salary05] = number_format($SALARY05?$SALARY05:0);	//เงินที่ได้เลื่อน 0.5 ขั้นทั้งหมด
			$arr_content[$data_count][sum_salary10] = number_format($SUM_SALARY10?$SUM_SALARY10:0);	//รวมเงินที่ได้ = เงินเดือนปัจจุบัน + เงินเลื่อน 1 ขั้น
			$arr_content[$data_count][count_sah_salary10] = number_format($SALARY10?$SALARY10:0);	//เงินที่ได้เลื่อน 1 ขั้นทั้งหมด
			$arr_content[$data_count][cmd_note1] = ($CMD_NOTE1?$CMD_NOTE1:"");

			$data_count++;
			} // end if
		} // end for
		}
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$POSITION = $arr_content[$data_count][position];

			for($i=4; $i>0; $i--){ 	//ประวัติการเลื่อนขั้นย้อนหลัง 4 ปี
					$year = ($search_budget_year - $i);	//>>>>
					${"COUNT_".$year} = $arr_content[$data_count]["count_".$year];	
			}
			for($i=1; $i<=4; $i++){ 	${"COUNT_ABSENT".$i} = $arr_content[$data_count]["count_absent".$i];	}	//ประวัติการลาแยกตามประเภท
			$PER_SALARY = $arr_content[$data_count][salary];
			$PER_SALARY05 = $arr_content[$data_count][count_sah_salary05];
			$PER_SALARY10= $arr_content[$data_count][count_sah_salary10];
			$PER_SUM_SALARY05 = $arr_content[$data_count][sum_salary05];
			$PER_SUM_SALARY10 = $arr_content[$data_count][sum_salary10];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];			

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $POSITION;
			if($REPORT_ORDER == "DETAIL"){
				for($i=0; $i<4; $i++){ 
					$j=4;
					$year = ($search_budget_year - ($j-$i));	
					$arr_data[] = ${"COUNT_".$year};
				}
				//แสดงประวัติการลา ป ก	พ	ส 
				for($i=1; $i<=4; $i++){
					$arr_data[] = ${"COUNT_ABSENT".$i};
				}	
				
				$arr_data[] = $PER_SALARY;
				$arr_data[] = $PER_SUM_SALARY05;
				$arr_data[] = $PER_SALARY05;
				$arr_data[] = $PER_SUM_SALARY10;
				$arr_data[] = $PER_SALARY10;
				$arr_data[] = $CMD_NOTE1;
				
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			} else {
				for($i=0; $i<4; $i++){ 
					$arr_data[] = "";
				}
				//แสดงประวัติการลา ป ก	พ	ส 
				for($i=1; $i<=4; $i++){
					$arr_data[] = "";
				}	
				
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			}
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
/*
			$border = "";
			if($REPORT_ORDER == "DETAIL") $pdf->SetFont($font,'',14);
			else $pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[1], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), $border, "L");	//ใช้ร่วมกับแสดงหน่วยงาน
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2]; 
			$pdf->y = $start_y;

			if($REPORT_ORDER == "DETAIL"){
				//แสดงประวัติการเลื่อนขั้นย้อนหลัง 4 ปี
				for($i=0; $i<4; $i++){ 
					$j=4;
					$year = ($search_budget_year - ($j-$i));	
					$pdf->Cell($heading_width[$i+3], 7, (${"COUNT_".$year}?(($NUMBER_DISPLAY==2)?convert2thaidigit(${"COUNT_".$year}):${"COUNT_".$year}):"-"), $border, 0, 'R', 0);	
					}
				//แสดงประวัติการลา ป ก	พ	ส 
				for($i=1; $i<=4; $i++){
					$pdf->Cell($heading_width[$i+6], 7, (${"COUNT_ABSENT".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(${"COUNT_ABSENT".$i}):${"COUNT_ABSENT".$i}):"-"), $border, 0, 'R', 0);   
					}	

				$pdf->Cell($heading_width[11], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[12], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SUM_SALARY05):$PER_SUM_SALARY05), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[13], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY05):$PER_SALARY05), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[14], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SUM_SALARY10):$PER_SUM_SALARY10), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[15], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY10):$PER_SALARY10), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[16], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_NOTE1):$CMD_NOTE1), $border, 0, 'L', 0);
			}	//end if
			
			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=16; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 21){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
*/
		} // end for				
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>