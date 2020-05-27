<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	//print_r($RPTORD_LIST);

	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "c.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "c.ORG_ID_REF";

				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.DEPARTMENT_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.ORG_ID";

				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

				$heading_name .= " สายงาน";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID) $order_by = "c.ORG_ID_REF";
		elseif(!$DEPARTMENT_ID) $order_by = "a.DEPARTMENT_ID";
		else $order_by = "a.ORG_ID";
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID) $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		elseif(!$DEPARTMENT_ID) $select_list = "a.DEPARTMENT_ID";
		else $select_list = "a.ORG_ID";
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(e.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(e.PER_STATUS = 1)";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		// ส่วนภูมิภาค
		//$list_type_text = "ส่วนภูมิภาค";
		//if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		//elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}elseif($list_type == "PER_LINE"){//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท$search_pt_name / "; }
		if($search_per_type==1){
			$per_name = "ข้าราชการ";
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pl_name";
			}
		}/**elseif($search_per_type==2){
			$per_name = "ลูกจ้างประจำ";
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_pn_name";
			}
		}elseif($search_per_type==3){
			$per_name = "พนักงานราชการ";
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " ตำแหน่งในสายงาน$search_ep_name";
			}
		} // end if**/
	}
	/**elseif($list_type == "PER_COUNTRY"){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(a.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}**/
	else{
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}/**elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if**/
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	
	//หาจำนวนข้าราชการ
	if($DPISDB=="odbc"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		count(e.PER_ID) as count_person
						from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
	}elseif($DPISDB=="mysql"){
			$cmd =" select 	count(e.PER_ID) as count_person
							from (
										(
											(
											(
												EAF_MASTER a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join  PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
									$search_condition
						order by 	a.EAF_ID
						";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis2->send_cmd($cmd);
	//	$db_dpis2->show_error();
	//  echo "<br>$cmd<br>";

	if($count_data==1){
		$data_dpis2 = $db_dpis2->get_array();
		$data_dpis2 = array_change_key_case($data_dpis2, CASE_LOWER);
		$count_person = $data_dpis2[count_person] ;
	} // end if

	$company_name .=" กรม $DEPARTMENT_NAME ";
	$company_name .=" จำนวนข้าราชการผู้มีผลสัมฤทธิ์สูง : $count_person คน";
	
	$report_title = "แบบสรุปการประเมินผลการปฏิบัติราชการ";
	$report_code = "Rkpi";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('angsa','',12);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[SUMMARY][0] = "30";
	$heading_width[SUMMARY][1] = "50";
	$heading_width[SUMMARY][2] = "30";
	$heading_width[SUMMARY][3] = "90";

	$heading_width[DEVELOPE][0] = "80";
	$heading_width[DEVELOPE][1] = "60";
	$heading_width[DEVELOPE][2] = "60";

	function print_header($HISTORY_NAME){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		switch($HISTORY_NAME){
			case "PERSONAL" :

				break;			
			case "SUMMARY" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"องค์ประกอบการประเมิน",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"คะแนน (ก)",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"น้ำหนัก (ข)",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][3] ,7,"รวมคะแนน (กxข)",'LTBR',1,'C',1);
				break;			
			case "DEVELOPE" :
				$pdf->Cell($heading_width[$HISTORY_NAME][0] ,7,"ความรู้ / ทักษะ / สมรรถนะที่ต้องได้รับการพัฒนา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][1] ,7,"วิธีการพัฒนา",'LTBR',0,'C',1);
				$pdf->Cell($heading_width[$HISTORY_NAME][2] ,7,"ช่วงเวลาที่ต้องการการพัฒนา",'LTBR',0,'C',1);
				break;			
			case "COMMENT" :

				break;			
		} // end switch case
	} // function

	//แสดงกรอบการสั่งสมประสบการณ์
	if($DPISDB=="odbc"){
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition); 
		$cmd = "select 		a.EAF_ID, PM_CODE, PL_CODE, e.LEVEL_NO, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
                                            a.EAF_NAME, a.EAF_ACTIVE,
											 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from 		EAF_MASTER a, PER_ORG b, PER_ORG c,EAF_PERSONAL d,PER_PERSONAL e,PER_PRENAME f
							where 	a.ORG_ID=b.ORG_ID and a.DEPARTMENT_ID=c.ORG_ID
											and a.EAF_ID=d.EAF_ID(+) and d.PER_ID=e.PER_ID(+)
											and trim(e.PN_CODE)=trim(f.PN_CODE) 
											$search_condition
							order by 	a.EAF_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd =" select 	a.EAF_ID, PM_CODE, PL_CODE, LEVEL_NO, PT_CODE, 
								a.ORG_ID, b.ORG_NAME, a.DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME,c.ORG_ID_REF as MINISTRY_ID,
								a.EAF_NAME, a.EAF_ACTIVE,
								 f.PN_NAME, e.PER_ID, e.PER_NAME, e.PER_SURNAME
							from (
										(
											(
												(
													EAF_MASTER a
													inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
												) inner join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
											) left join EAF_PERSONAL d on (a.EAF_ID=d.EAF_ID)
										) left join PER_PERSONAL e on (d.PER_ID=e.PER_ID)
									) left join PER_PRENAME f on (trim(e.PN_CODE)=trim(f.PN_CODE))
							$search_condition
							order by 	a.EAF_ID
						  ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//  echo "<br>$cmd<br>";

if($count_data){	
	$data_count = 0;
	$data_row = 0;
	while($data = $db_dpis->get_array()){
		$temp_EAF_ID = trim($data[EAF_ID]);
		$current_list .= ((trim($current_list))?", ":"") . $temp_EAF_ID;
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
		$MINISTRY_ID = trim($data[MINISTRY_ID]);
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PT_CODE = trim($data[PT_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
		
		//หาตำแหน่งประเภท
		$cmd = "select LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) and (LEVEL_NO='$LEVEL_NO')order by  LEVEL_SEQ_NO,LEVEL_NO";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		
		$arr_temp = explode(" ", trim($data_dpis2[LEVEL_NAME]));
		//หาชื่อตำแหน่งประเภท
		if($search_per_type==1){
			$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
		}elseif($search_per_type==2){
			$POSITION_TYPE = $arr_temp[0];
		}elseif($search_per_type==3){
			$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
		}
		//หาชื่อระดับตำแหน่ง
		//$arr_temp[1]=str_replace("ระดับ", "", $arr_temp[1]);
		$LEVEL_NAME =  trim($arr_temp[1]);						

		$EAF_NAME = trim($data[EAF_NAME]);
        $EAF_ACTIVE = trim($data[EAF_ACTIVE]);

		$cmd = " select ORG_NAME from PER_ORG where trim(ORG_ID)='".$MINISTRY_ID."' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$MINISTRY_NAME = $data_dpis2[ORG_NAME];

		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME = $data_dpis2[PM_NAME];

		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME = $data_dpis2[PL_NAME];

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME = $data_dpis2[PT_NAME];

		###########
		/***$data_row++;
		$arr_content[$data_count][sequence] = $data_row;
		$arr_content[$data_count][eaf_name] = $EAF_NAME;
		$arr_content[$data_count][name] = $FULLNAME;
		$arr_content[$data_count][position_type] = $POSITION_TYPE." ".$LEVEL_NAME;
		$arr_content[$data_count][position_lline] = $PL_NAME;
		$arr_content[$data_count][position_mgt] = $PM_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][show_els_period] = $SHOW_ELS_PERIOD;	
		//$arr_content[$data_count][org_name] = $MINISTRY_NAME." ".$DEPARTMENT_NAME." ".$ORG_NAME;
		$data_count++;***/

		$pdf->AutoPageBreak = false;
		
		for($history_index=0; $history_index<count($arr_history_name); $history_index++){
			$HISTORY_NAME = $arr_history_name[$history_index];
			switch($HISTORY_NAME){
				case "PERSONAL" : //ข้อมูลผู้รับการประเมิน
					//ส่วนที่ 1
					if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
					$pdf->SetFont('angsab','',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
					$pdf->Cell(200,7,"ส่วนที่ ".($history_index + 1).":  ข้อมูลของผู้รับการประเมิน",0,1,"L");
					print_header($HISTORY_NAME);
					
					$pdf->Cell(85,7,"รอบการประเมิน",0,1,"L"); 
					/*$$arr_fields[1] = ($data[$arr_fields[1]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
					$pdf->Image($$arr_fields[1],($pdf->x + ($heading_width[3] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
					$pdf->Image($$arr_fields[1],($pdf->x + (85 / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");*/
					$pdf->Cell(85,7,"ครั้งที่ 1 1 ตุลาคม $  ถึง 31 มีนาคม $ ",0,1,"L");
					$pdf->Cell(85,7,"ครั้งที่ 2 1 เมษายน $ ถึง 30 กันยายน $",1,1,"L");
					
					$pdf->Cell(90,7,"ชื่อผู้รับการประเมิน ".$FULL_NAME,0,1,"L");
					
					$pdf->Cell(100,7,"ตำแหน่ง",0,1,"L");
					$pdf->Cell(100,7,"ประเภทตำแหน่ง",1,1,"L");
					
					$pdf->Cell(150,7,"ระดับตำแหน่ง",0,1,"L");
					$pdf->Cell(150,7,"สังกัด",1,1,"L");
					
					$pdf->Cell(200,7,"ชื่อผู้บังคับบัญชา/ ผู้ประเมิน ".$FULL_NAME,0,1,"L");
					$pdf->Cell(200,7,"ตำแหน่ง ",1,1,"L");
					break;
				case "SUMMARY" : //การสรุปผลการประเมิน
					//ส่วนที่ 2
					if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
					$pdf->SetFont('angsab','',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
					$pdf->Cell(200,7,"ส่วนที่ ".($history_index + 1).":  การสรุปผลการประเมิน",0,1,"L");
					print_header($HISTORY_NAME);
							
					break;
				case "DEVELOPE" : //แผนพัฒนาผลการปฏิบัติราชการรายบุคคล
					//ส่วนที่ 3
					if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
					$pdf->SetFont('angsab','',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
					$pdf->Cell(200,7,"ส่วนที่ ".($history_index + 1).":  แผนพัฒนาผลการปฏิบัติราชการรายบุคคล (Individual Performance Improvement Plan: IPIP)",0,1,"L");
					print_header($HISTORY_NAME);
					
					break;
				case "COMMENT" : //ความเห็นของผู้บังคับเหนือขึ้นไป
					//ส่วนที่ 4
					if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
					$pdf->SetFont('angsab','',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					if($pdf->y != $page_start_y) $pdf->Cell(200,7,"",0,1,"L");
					$pdf->Cell(200,7,"ส่วนที่ ".($history_index + 1).":  ความเห็นของผู้บังคับเหนือขึ้นไป",0,1,"L");
					print_header($HISTORY_NAME);
					
					break;
			}//end switch
		}//end for

			if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>