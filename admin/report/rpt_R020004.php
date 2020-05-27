<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "f.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
		$up_type="เงินเดือน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "f.PN_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
		$up_type="ค่าจ้าง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "f.EP_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
		$up_type="ค่าจ้าง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "f.TP_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
		$up_type="ค่าจ้าง";
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

	$select_list = "";	$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				 $order_by .= "g.ORG_ID_REF";

				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" : 
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break; 
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;

			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "b.ORG_ID_1";
				else if($select_org_structure==1) $select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";

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
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(e.MOV_CODE in ('21310','21351','21320','21352','214','21410'))";	//ค้นหาเฉพาะเลื่อนขั้นเงินเดือน 0.5 และ 1 ขั้น และเต็มขั้น(งดเลื่อน)
	
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

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(c.OT_CODE), 1)='1')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(c.OT_CODE), 1, 1)='1')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(c.OT_CODE), 1)='1')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(c.OT_CODE), 1)='2')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(c.OT_CODE), 1, 1)='2')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(c.OT_CODE), 1)='2')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if(trim($search_org_id)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
//	$company_name = "";
	
	if($SALQ_TYPE==1){
		$sal_type = "รอบแรก 1 เมษายน $search_budget_year";
	}elseif($SALQ_TYPE==2){
		$sal_type = "รอบสอง 1 ตุลาคม $search_budget_year";
	}
	$report_title = "รายชื่อ$PERSON_TYPE[$search_per_type] เพื่อพิจารณาเลื่อนขั้น$up_type $sal_type ";
	$report_code = "R20004";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "10";
	$heading_width[1] = "35";
	$heading_width[2] = "45";
	$heading_width[3] = "12";
	$heading_width[4] = "12";
	$heading_width[5] = "12";
	$heading_width[6] = "12";
	$heading_width[7] = "12";
	$heading_width[8] = "12";
	$heading_width[9] = "12";
	$heading_width[10] = "12";
	$heading_width[11] = "15";
	$heading_width[12] = "17";
	$heading_width[13] = "17";
	$heading_width[14] = "17";
	$heading_width[15] = "17";
	$heading_width[16] = "22";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year;
		global $SALQ_TYPE;

		$this_year = substr(($search_budget_year),2);
		if($SALQ_TYPE==1){	//รอบแรก เม.ย.
			$absent_period="1 ต.ค. ".($this_year-1)." - 31 มี.ค. ".($this_year);
		}else{
			$absent_period="1 เม.ย. ".$this_year." - 30 ก.ย. ".$this_year;
		}

		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - นามสกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"สังกัด/ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] +$heading_width[4] +$heading_width[5] +$heading_width[6],7,"ประวัติการเลื่อนขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] +$heading_width[8] +$heading_width[9] +$heading_width[10] ,7,"ประวัติการลา $absent_period",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12]+$heading_width[13] ,7,"เลื่อน 0.5 ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[14]+$heading_width[15] ,7,"เลื่อน 1 ขั้น",'LTR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		for($i=4; $i>0; $i--){ 	//ย้อนหลัง 4 ปี
			$j++;
			$pdf->Cell($heading_width[$j+2] ,7,"ปี ".substr(($search_budget_year - $i),2),'LTBR',0,'C',1);
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

	//หาประวัติจำนวนขั้นที่ได้เลื่อน (ทุกขั้น)
	function count_salpromote_level($search_year,$PER_ID){
		global $DPISDB, $db_dpis2;
		global $SALQ_TYPE,$search_budget_year,$select_org_structure;	//($search_budget_year - 543)

		$sum_salpro_level = 0;

		$search_condition = "";
		//ค้นหาเฉพาะประเภทการเคลื่อนไหวแบบเลื่อนขั้นเงินเดือน ... ขั้น
		$search_condition = "and (psh.MOV_CODE in ('21310','21351','21320','21352','21330','21353','21340','21354')) ";

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
			switch($MOV_CODE){
				case "21310" :
				case "21351" :
					$PR_LEVEL[] = 0.5;
					break;
				case "21320" :
				case "21352" :
					$PR_LEVEL[] = 1.0;
					break;
				case "21330" :
				case "21353" :
					$PR_LEVEL[] = 1.5;
					break;
				case "21340" :
				case "21354" :
					$PR_LEVEL[] = 2.0;
					break;
				default :
					$PR_LEVEL[] = 0;
			} // end switch case
			//$SAH_SALARY = $data[SAH_SALARY];	//เงินที่ได้เลื่อนขั้น
		} //end while
			//รวมจำนวนขั้นทั้งหมดที่ได้เลื่อนในปีนั้น
			if(array_sum($PR_LEVEL) > 0)	$sum_salpro_level = array_sum($PR_LEVEL);

	return $sum_salpro_level;
	}

	//หาจำนวนวันลา แยกแต่ละประเภท
	function count_absent_days($ab_code,$PER_ID){
		global $DPISDB, $db_dpis2;
		global $SALQ_TYPE,$search_budget_year;
		
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

	function count_promote($search_cycle, $search_budget_year, $PER_ID){	//?????
		global $DPISDB, $db_dpis2;
		global $search_per_type;
/*
		if($DPISDB=="odbc"){
			$cmd = " select			count(SAH_ID) as count_salaryhis
							 from			PER_SALARYHIS
							where			PER_ID=$PER_ID 
												and (LEFT(trim(SAH_EFFECTIVEDATE), 10) >= '".($search_budget_year - 543 - 1)."-10-01') 
												and (LEFT(trim(SAH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')
												and $search_condition ";
		}elseif($DPISDB=="oci8"){				
			$cmd = " select			count(SAH_ID) as count_salaryhis
							 from			PER_SALARYHIS
							where			PER_ID=$PER_ID 
												and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '".($search_budget_year - 543 - 1)."-10-01') 
												and (SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')
												and $search_condition ";
		}elseif($DPISDB=="mysql"){
		} // end if

		$count_salaryhis = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_salaryhis==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_salaryhis] == 0) $count_salaryhis = 0;
		} // end if
		
		return $count_salaryhis;
*/
		if($search_cycle > 0 && $search_per_type==2) $search_cycle += 2;
		$search_condition = "";
		if($search_cycle) $search_condition = "and SALQ_TYPE = $search_cycle";
		
		$cmd = " select			sum(SALP_LEVEL) as sum_level, max(SALP_SALARY_NEW) as new_salary
						 from			PER_SALPROMOTE
						where			PER_ID=$PER_ID
											and SALQ_YEAR = '$search_budget_year'
											and SALP_YN = 1
											$search_condition ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data = $db_dpis2->get_array();
		$sum_level = $data[sum_level];
		$new_salary = $data[new_salary];
		
		$return_value = "";
		if($sum_level > 0) $return_value = "$sum_level|$new_salary";
		
	return $return_value;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type,$select_org_structure;
		global $ORG_ID, $ORG_ID_1;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :

				break;
				case "DEPARTMENT" : 

				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}else{
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
				case "ORG_1" :	
					if($select_org_structure==0){
						if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
						else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
					}else{
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
				case "MINISTRY" :
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" : 
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :	
					$ORG_ID_1 = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($search_per_type==1){
		if($DPISDB=="odbc"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												$line_code, $line_name, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, a.PER_SALARY,
												LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
							 from			(
							 						(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
													) left join $line_table f on ($line_join)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												$search_condition
							 order by		$order_by, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, to_number(replace($position_no,'-','')) as POS_NO, 
												$line_code, $line_name, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, a.PER_SALARY,
												SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f, PER_TYPE g
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
							 					and a.PER_ID=e.PER_ID and $line_join(+) and b.PT_CODE=g.PT_CODE(+)
												$search_condition
							 order by		$order_by, to_number(replace($position_no,'-','')), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, 
												$line_code, $line_name, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, a.PER_SALARY,
												LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
							 from			(
							 						(
														(
															(
																(
																	PER_PERSONAL a 
																	left join $position_table b on ($position_join)
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
														) inner join PER_SALARYHIS e on (a.PER_ID=e.PER_ID)
													) left join $line_table f on ($line_join)
												) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												$search_condition
							 order by		$order_by, $position_no, e.SAH_EFFECTIVEDATE desc ";
		}
	}else{	//2 || 3 || 4
		if($DPISDB=="odbc"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
												LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
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
							 order by		$order_by, IIf(IsNull($position_no), 0, CLng($position_no)), LEFT(trim(e.SAH_EFFECTIVEDATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, to_number(replace($position_no,'-','')) as POS_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
												SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
							 					and a.PER_ID=e.PER_ID and $line_join(+)
												$search_condition
							 order by		$order_by, to_number(replace($position_no,'-','')), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, 
												$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
												LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY
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
							 order by		$order_by, $position_no, e.SAH_EFFECTIVEDATE desc ";
		}
	} //end if
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
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID !=trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

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
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

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
					$POS_NO = $data[POS_NO];
					$PL_CODE = trim($data[PL_CODE]);
					$PL_NAME = trim($data[PL_NAME]);
					$LEVEL_NO = $data[LEVEL_NO];
					$PT_CODE = trim($data[PT_CODE]);
					$PT_NAME = trim($data[PT_NAME]);
					$PER_SALARY = $data[PER_SALARY];
					
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
			
			$CMD_NOTE1="";
			//แยกตามประเภทการเลื่อนขั้นเงินเดือน (กี่ขั้น)
			//เลือกเฉพาะ 0.5 และ 1 ขั้น
			$MOV_CODE = trim($data[MOV_CODE]);
			$SAH_SALARY = $data[SAH_SALARY];	//เงินที่ได้เลื่อนขั้น
			switch($MOV_CODE){
				case "21310" :
				case "21351" :
					$PR_LEVEL = 0.5;
					$SAH_SALARY05[$PER_ID] += $SAH_SALARY;
					break;
				case "21320" :
				case "21352" :
					$PR_LEVEL = 1.0;
					$SAH_SALARY10[$PER_ID] += $SAH_SALARY;
					break;
				/***
				case "21330" :
				case "21353" :
					$PR_LEVEL = 1.5;
					break;
				case "21340" :
				case "21354" :
					$PR_LEVEL = 2.0;
					break;
				***/
				case "214" :
				case "21410" :
					$CMD_NOTE1 = "เต็มขั้น";
					break;
				default :
					$PR_LEVEL = 0;
			} // end switch case
			
			$SUM_SALARY05	= ($PER_SALARY+$SAH_SALARY05[$PER_ID]);	//เงินเดือนเลื่อน 0.5 ขั้น = เงินเดือน+เงินเลื่อน 0.5
			$SUM_SALARY10	= ($PER_SALARY+$SAH_SALARY10[$PER_ID]);	//เงินเดือนเลื่อน 1 ขั้น = เงินเดือน+เงินเลื่อน 1

			$arr_content[$data_count][sum_salary05] = number_format($SUM_SALARY05?$SUM_SALARY05:0);	//รวมเงินที่ได้ = เงินเดือนปัจจุบัน + เงินเลื่อน 0.5 ขั้น
			$arr_content[$data_count][count_sah_salary05] = number_format($SAH_SALARY05[$PER_ID]?$SAH_SALARY05[$PER_ID]:0);	//เงินที่ได้เลื่อน 0.5 ขั้นทั้งหมด
			$arr_content[$data_count][sum_salary10] = number_format($SUM_SALARY10?$SUM_SALARY10:0);	//รวมเงินที่ได้ = เงินเดือนปัจจุบัน + เงินเลื่อน 1 ขั้น
			$arr_content[$data_count][count_sah_salary10] = number_format($SAH_SALARY10[$PER_ID]?$SAH_SALARY10[$PER_ID]:0);	//เงินที่ได้เลื่อน 1 ขั้นทั้งหมด
			$arr_content[$data_count][cmd_note1] = ($CMD_NOTE1?$CMD_NOTE1:"-");

			$data_count++;
			} // end if
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

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
			
			$border = "";
			if($REPORT_ORDER == "DETAIL") $pdf->SetFont('angsa','',14);
			else $pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$POSITION", $border, "L");	//ใช้ร่วมกับแสดงหน่วยงาน
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2]; 
			$pdf->y = $start_y;

			if($REPORT_ORDER == "DETAIL"){
				//แสดงประวัติการเลื่อนขั้นย้อนหลัง 4 ปี
				for($i=0; $i<4; $i++){ 
					$j=4;
					$year = ($search_budget_year - ($j-$i));	
					$pdf->Cell($heading_width[$i+3], 7, ${"COUNT_".$year}?${"COUNT_".$year}:"-", $border, 0, 'R', 0);	
					}
				//แสดงประวัติการลา ป ก	พ	ส 
				for($i=1; $i<=4; $i++){
					$pdf->Cell($heading_width[$i+6], 7, (${"COUNT_ABSENT".$i}?${"COUNT_ABSENT".$i}:"-"), $border, 0, 'R', 0);
					}	

				$pdf->Cell($heading_width[11], 7, $PER_SALARY, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[12], 7, $PER_SUM_SALARY05, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[13], 7, $PER_SALARY05, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[14], 7, $PER_SUM_SALARY10, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[15], 7, $PER_SALARY10, $border, 0, 'R', 0);
				$pdf->Cell($heading_width[16], 7, "$CMD_NOTE1", $border, 0, 'L', 0);
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
		} // end for				
	}else{
		$pdf->SetFont('angsa','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>