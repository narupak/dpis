<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POS_NO", "NAME", "LINE", "LEVEL", "ORG", "ORG_1", "ORG_2"); 
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "TO_NUMBER(b.POS_NO)";
				elseif($DPISDB=="mysql") $order_by .= "b.POS_NO+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.PER_STATUS = 1) and (b.POS_ID >= 0) )";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";	
	
	$list_type_text = $ALL_REPORT_TITLE;
/*	if($select_org_structure==0){ $list_type_text .= " โครงสร้างตามกฏหมาย"; 	}
	elseif($select_org_structure==1){ $list_type_text .= " โครงสร้างตามมอบหมายงาน"; }  */		
	if($list_type == "PER_ORG_TYPE_1"){
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
		$arr_search_condition[] = "(d.OT_CODE='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(d.OT_CODE='02')";
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
		$arr_search_condition[] = "(d.OT_CODE='03')";
	}elseif($list_type == "PER_ORG_TYPE_4"){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(d.OT_CODE='04')";
	}elseif($list_type == "PER_ORG"){
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
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if(trim($search_pl_code) && trim($search_pn_code)  && trim($search_ep_code) ){ 
			$search_pl_code = trim($search_pl_code);
			$search_pn_code = trim($search_pn_code);
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name, $search_pn_name,$search_ep_code";
		}elseif(trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE)='$search_pl_code'))";
			$list_type_text .= "$search_pl_name";
		}elseif(trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_pn_name";
		}elseif(trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "((a.PER_TYPE=1 and trim(b.PL_CODE) like '%'))";
			$list_type_text .= "$search_ep_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}else{
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
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	if ($search_date != ""){
		$arr_temp = explode("/", $search_date);
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];

		//ค้นหาสำหรับคำสั่งช่วยราชการที่ยังไม่ถึงวันสิ้นสุด ณ วันที่ระบุ
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(k.ACTH_ENDDATE), 10) >= '$search_date' or k.ACTH_ENDDATE is null) and LEFT(trim(k.ACTH_EFFECTIVEDATE), 10) <= '$search_date' ";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(k.ACTH_ENDDATE), 1, 10) >= '$search_date' or k.ACTH_ENDDATE is null) and SUBSTR(trim(k.ACTH_EFFECTIVEDATE), 1, 10) <= '$search_date' ";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(k.ACTH_ENDDATE), 10) >= '$search_date' or k.ACTH_ENDDATE is null) and LEFT(trim(k.ACTH_EFFECTIVEDATE), 10)<= '$search_date' ";
	}

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	if(in_array(1, $search_per_type)) $report_title = "$DEPARTMENT_NAME||รายชื่อข้าราชการ ตำแหน่ง สังกัด ที่ไปช่วยราชการ";
	if ($search_date != "") $report_title .= "||(ประมวลผลข้อมูล ณ วันที่ $show_date)";
	$report_code = "R0496";
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
	$pdf->SetFont('angsa','',7);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "6";
	$heading_width[1] = "7";
	$heading_width[2] = "15";
	$heading_width[3] = "13";
	$heading_width[4] = "22";
	$heading_width[5] = "10";
	$heading_width[6] = "12";
	$heading_width[7] = "7";
	$heading_width[8] = "25";
	$heading_width[9] = "12";
	$heading_width[10] = "10";
	$heading_width[11] = "18";
	$heading_width[12] = "22";
	$heading_width[13] = "10";
	$heading_width[14] = "12";
	$heading_width[15] = "25";
	$heading_width[16] = "12";
	$heading_width[17] = "10";
	$heading_width[18] = "18";
	$heading_width[19] = "15";
	$heading_width[20] = "10";

	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',7);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อ",'LTR',0,'C',1);		
		$pdf->Cell($heading_width[3] ,7,"สกุล",'LTR',0,'C',1);	
		$pdf->Cell($heading_width[4]+$heading_width[5]+$heading_width[6]+$heading_width[7]+$heading_width[8]+$heading_width[9]+$heading_width[10]+$heading_width[11] ,7,"ข้อมูลตำแหน่งที่ได้รับแต่งตั้ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[12]+$heading_width[13]+$heading_width[14]+$heading_width[15]+$heading_width[16]+$heading_width[17]+$heading_width[18]+$heading_width[19] +$heading_width[20],7,"ข้อมูลการไปช่วยราชการของข้าราชการ",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);		
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LTBR',0,'C',1);		
		$pdf->Cell($heading_width[5] ,7,"ประเภท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"กลุ่ม/ฝ่าย/สำนัก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"จังหวัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"สังกัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"ตำแหน่ง",'LTBR',0,'C',1);		
		$pdf->Cell($heading_width[13] ,7,"ประเภท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"กลุ่ม/ฝ่าย/สำนัก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"จังหวัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[17] ,7,"สังกัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[18] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"กรมเดียวกัน/ต่างกรม",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"ตั้งแต่วันที่",'LTBR',1,'C',1);
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
						 from	(
										(
											(
												(
													( 	
														(
															(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (d.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_ACTINGHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
										from PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d , PER_LEVEL h, PER_PROVINCE i, PER_ORG_TYPE j, PER_ACTINGHIS k, PER_LEVEL p 
										where a.POS_ID=b.POS_ID (+) and b.ORG_ID=d.ORG_ID (+) and a.LEVEL_NO=h.LEVEL_NO (+) and d.PV_CODE=i.PV_CODE (+) 
										and d.OT_CODE=j.OT_CODE (+) and a.PER_ID=k.PER_ID (+) and d.department_id=c.org_id (+) and k.LEVEL_NO_ASSIGN=p.LEVEL_NO
										$search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, h.LEVEL_NAME, a.PER_SALARY, a.PER_STARTDATE, a.PER_TYPE, 
										b.PL_CODE, b.PT_CODE, b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, b.POS_CHANGE_DATE, i.PV_NAME, j.OT_NAME,c.ORG_NAME AS DEPARTMENT_NAME, d.ORG_NAME, a.PER_CARDNO, a.PER_BIRTHDATE, k.ACTH_PL_NAME_ASSIGN,k.acth_org2_AssIGN, k.ACTH_ORG3_ASSIGN, 
										p.LEVEL_NAME AS LEVEL_NAME_ASSIGN, k.ACTH_EFFECTIVEDATE, k.ACTH_ENDDATE 
						 from	(
										(
											(
												(
													( 	
														(
															(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG d on (b.ORG_ID=d.ORG_ID)
													) left join PER_ORG c on (d.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_LEVEL h on (a.LEVEL_NO=h.LEVEL_NO)
											) left join PER_PROVINCE i on (d.PV_CODE=i.PV_CODE)											
										) left join PER_ORG_TYPE j on (d.OT_CODE=j.OT_CODE)
									) left join PER_ACTINGHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_LEVEL p on (k.LEVEL_NO_ASSIGN=p.LEVEL_NO)
								$search_condition
						 order by		$order_by ";
	}
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd."<br>";
//	$db_dpis->show_error();
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];

			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", $PER_BIRTHDATE);
				$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			}
			
			$POS_NO = $data[POS_NO];
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$NAME = ($PN_NAME)."$PER_NAME";
			$SURNAME = trim($data[PER_SURNAME]);

			$ORG_ID = $data[ORG_ID];

			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_NAME = trim($PL_NAME) . " " . level_no_format($LEVEL_NAME) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");
			$PL_NAME_ASSIGN =trim($data[ACTH_PL_NAME_ASSIGN]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$level=trim($data[LEVEL_NAME]);
			$LEVEL_NAME1 = substr($level,strpos($level,"ประเภท")+6);
			$LEVEL_NAME1 = substr($LEVEL_NAME1,0,strlen($LEVEL_NAME1)-strlen(substr($LEVEL_NAME1,strpos($LEVEL_NAME1,"ระดับ")-1)));
			$LEVEL_NAME2 = substr($level,strpos($level,"ระดับ")+5);
			
			$level=trim($data[LEVEL_NAME_ASSIGN]);
			$LEVEL_NAME1_ASSIGN = substr($level,strpos($level,"ประเภท")+6);
			$LEVEL_NAME1_ASSIGN = substr($LEVEL_NAME1_ASSIGN, 0, strlen($LEVEL_NAME1_ASSIGN) - strlen(substr($LEVEL_NAME1_ASSIGN, strpos($LEVEL_NAME1_ASSIGN, "ระดับ") - 1)));
			$LEVEL_NAME2_ASSIGN = substr($level,strpos($level,"ระดับ")+5);

			$PER_SALARY = $data[PER_SALARY];

			$ORG_NAME = $data[ORG_NAME];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			$ORG_NAME_ASSIGN = $data[ACTH_ORG3_ASSIGN];
			$DEPARTMENT_NAME_ASSIGN = $data[ACTH_ORG2_ASSIGN];

			$PV_NAME=trim($data[PV_NAME]);
			$cmd = " SELECT PV_NAME FROM PER_PROVINCE a, PER_ORG b WHERE a.PV_CODE=b.PV_CODE and b.ORG_NAME='$ORG_NAME_ASSIGN' and b.DEPARTMENT_ID = (SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME='$DEPARTMENT_NAME_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PV_NAME_ASSIGN=trim($data[PV_NAME]);

			$OT_NAME=trim($data[OT_NAME]);
			$cmd = " SELECT OT_NAME FROM PER_ORG_TYPE a, PER_ORG b WHERE a.OT_CODE=b.OT_CODE and b.ORG_NAME='$ORG_NAME_ASSIGN' and b.DEPARTMENT_ID = (SELECT ORG_ID FROM PER_ORG WHERE ORG_NAME='$DEPARTMENT_NAME_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OT_NAME_ASSIGN=trim($data[OT_NAME]);

			if ($DEPARTMENT_NAME == $DEPARTMENT_NAME_ASSIGN) $DEPARTMENT_DIFF="กรมเดียวกัน";
			else  $DEPARTMENT_DIFF="ต่างกรม";

			$ACTH_EFFECTIVEDATE = trim($data[ACTH_EFFECTIVEDATE]);
			if($ACTH_EFFECTIVEDATE){
				$arr_temp = explode("-", substr($ACTH_EFFECTIVEDATE, 0, 10));
				$ACTH_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
			} // end if

			$border = "";
			$pdf->SetFont('angsa','',7);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, "$data_count ", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, "$POS_NO", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[2], 7, "$NAME", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[3], 7, "$SURNAME", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[4], 7, "$PL_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;	

			$pdf->Cell($heading_width[5], 7, "$LEVEL_NAME1", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[6], 7, "$LEVEL_NAME2", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[7], 7, ($PER_SALARY?number_format($PER_SALARY, ","):"-"), $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[8], 7, "$ORG_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
			$pdf->y = $start_y;

			$pdf->Cell($heading_width[9], 7, "$PV_NAME", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[10], 7, "$OT_NAME", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[11], 7, "$DEPARTMENT_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11];
			$pdf->y = $start_y;

			$pdf->MultiCell($heading_width[12], 7, "$PL_NAME_ASSIGN", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12];
			$pdf->y = $start_y;	

			$pdf->Cell($heading_width[13], 7, "$LEVEL_NAME1_ASSIGN", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[14], 7, "$LEVEL_NAME2_ASSIGN", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[15], 7, "$ORG_NAME_ASSIGN", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15];
			$pdf->y = $start_y;

			$pdf->Cell($heading_width[16], 7, "$PV_NAME_ASSIGN", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[17], 7, "$OT_NAME_ASSIGN", $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[18], 7, "$DEPARTMENT_NAME_ASSIGN", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18];
			$pdf->y = $start_y;

			$pdf->Cell($heading_width[19], 7, "$DEPARTMENT_DIFF", $border, 0, 'L', 0);
			$pdf->Cell($heading_width[20], 7, "$ACTH_EFFECTIVEDATE", $border, 1, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=21; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == $count_data) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
		$border = "LTBR";
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18] + $heading_width[19] + $heading_width[20]), 7, " ", $border, 1, 'L', 0);
	}else{
		$pdf->SetFont('angsa','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>