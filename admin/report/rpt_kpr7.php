<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

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
	$arr_rpt_order = array("POSNO"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POSNO" :
				if($select_list) $select_list .= ", ";

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") {
					$select_list .= "a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO)) as POS_NO";				
					$order_by .= "a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0 , CLng(a.POS_NO))";
				}
				if($DPISDB=="oci8") {
					$select_list .= "a.POS_NO_NAME, a.POS_NO";
					$order_by .= "a.POS_NO_NAME, to_number(replace(a.POS_NO,'-',''))";
				}elseif($DPISDB=="mysql"){
					$select_list .= "a.POS_NO_NAME, a.POS_NO";				
					$order_by .= "a.POS_NO_NAME, a.POS_NO";
				}

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.POS_NO_NAME, a.POS_NO";
	if(!trim($select_list)) $select_list = "a.POS_NO_NAME, a.POS_NO";

	$search_condition = "";
	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$todate = date("Y-m-d");
	$show_date = show_date_format($todate, 3);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "แบบ คปร.7 - พลเรือน ส่วนราชการ : $list_type_text";
	$report_title = "ข้อมูลสถานภาพของตำแหน่ง และผู้ถือครองตำแหน่งข้าราชการพลเรือน ณ วันที่ ". ($show_date?(($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date):"-");
	$report_code = "P1103";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
//	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "7";
	$heading_width[1] = "10";
	$heading_width[2] = "17";
	$heading_width[3] = "17";
	$heading_width[4] = "22";
	$heading_width[5] = "22";
	$heading_width[6] = "11";
	$heading_width[7] = "12";
	$heading_width[8] = "12";
	$heading_width[9] = "12";
	$heading_width[10] = "18";
	$heading_width[11] = "12";
	$heading_width[12] = "11";
	$heading_width[13] = "5";
	$heading_width[14] = "5";
	$heading_width[15] = "5";
	$heading_width[16] = "5";
	$heading_width[17] = "5";
	$heading_width[18] = "5";
	$heading_width[19] = "11";
	$heading_width[20] = "13";
	$heading_width[21] = "11";
	$heading_width[22] = "12";
	$heading_width[23] = "11";
	$heading_width[24] = "11";
	$heading_width[25] = "11";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9]) ,7,"ข้อมูลตำแหน่งข้าราชการ / หน่วยงานที่สังกัด",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18] + $heading_width[19] + $heading_width[20] + $heading_width[21] + $heading_width[22] + $heading_width[23] + $heading_width[24]) ,7,"ข้อมูลข้าราชการผู้ครองตำแหน่ง / หน่วยงาน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[25] ,7,"",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"(1)",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"(2)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2]+$heading_width[3] ,7,"(3)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"(4)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"(5)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"(6)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"(7)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"(8)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"(9)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"(10)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"(11)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"(12)",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[13] + $heading_width[14] + $heading_width[15]) ,7,"(13)",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[16] + $heading_width[17] + $heading_width[18]) ,7,"(14)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"(15)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"(16)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"(17)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[22] ,7,"(18)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[23] ,7,"(19)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[24] ,7,"(20)",'LTR',0,'C',1);
		$pdf->Cell($heading_width[25] ,7,"(21)",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] +$heading_width[3],7,"ชื่อส่วนราชการ",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"เลข",'LR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"",'LR',0,'C',1);
		$pdf->Cell(($heading_width[13] + $heading_width[14] + $heading_width[15]) ,7,"",'LR',0,'C',1);
		$pdf->Cell(($heading_width[16] + $heading_width[17] + $heading_width[18]) ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"ระดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"อัตรา",'LR',0,'C',1);
		$pdf->Cell($heading_width[22] ,7,"เงิน",'LR',0,'C',1);
		$pdf->Cell($heading_width[23] ,7,"ระดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[24] ,7,"",'LR',0,'C',1);
		$pdf->Cell($heading_width[25] ,7,"",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[2],7,"$ORG_TITLE",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[3],7,"เขต/แขวง",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ชื่อตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ชื่อตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ระดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ประเภท",'LR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"ส่วนกลาง",'LR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"พื้นที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"ชื่อ-สกุล",'LR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ประจำตัว",'LR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"เพศ",'LR',0,'C',1);
		$pdf->Cell(($heading_width[13] + $heading_width[14] + $heading_width[15]) ,7,"เกิด",'LRB',0,'C',1);
		$pdf->Cell(($heading_width[16] + $heading_width[17] + $heading_width[18]) ,7,"บรรจุ",'LRB',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"ปีที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[22] ,7,"ประจำ",'LR',0,'C',1);
		$pdf->Cell($heading_width[23] ,7,"การ",'LR',0,'C',1);
		$pdf->Cell($heading_width[24] ,7,"สาขา",'LR',0,'C',1);
		$pdf->Cell($heading_width[25] ,7,"หมายเหตุ",'LR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);	
		$pdf->Cell($heading_width[3] ,7,"/ศูนย์",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ในการบริหาร",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ในสายงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"/ภูมิภาค",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"ปฏิบัติงาน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ประชาชน",'LBR',0,'C',1);	
		$pdf->Cell($heading_width[12] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"ว",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"ด",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[15] ,7,"ป",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[16] ,7,"ว",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[17] ,7,"ด",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[18] ,7,"ป",'TLBR',0,'C',1);
		$pdf->Cell($heading_width[19] ,7,"เกษียณ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[20] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[21] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[22] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[23] ,7,"ศึกษา",'LBR',0,'C',1);
		$pdf->Cell($heading_width[24] ,7,"วิชา",'LBR',0,'C',1);
		$pdf->Cell($heading_width[25] ,7,"",'LBR',1,'C',1);
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POS_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					if($POS_NO) $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO')";
					else $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO' or a.POS_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POS_NO, $SESS_ORG_STRUCTURE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
														(
															(
																PER_POSITION a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
														) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
													) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			PER_POSITION a, PER_ORG b, PER_LINE c, PER_MGT d, PER_TYPE e, PER_PROVINCE f, PER_ORG_TYPE g
						 where		a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE and a.PM_CODE=d.PM_CODE(+) and a.PT_CODE=e.PT_CODE(+)
						 					and b.PV_CODE=f.PV_CODE(+) and b.OT_CODE=g.OT_CODE(+)
											$search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct 
											a.DEPARTMENT_ID, $select_list, a.POS_ID, b.ORG_NAME, c.PL_NAME, d.PM_NAME, e.PT_NAME, a.CL_NAME, f.PV_NAME, g.OT_NAME
						 from			(
												(
													(
														(
															(
																PER_POSITION a
																inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
															) inner join PER_LINE c on (a.PL_CODE=c.PL_CODE)
														) left join PER_MGT d on (a.PM_CODE=d.PM_CODE)
													) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
												) left join PER_PROVINCE f on (b.PV_CODE=f.PV_CODE)
											) left join PER_ORG_TYPE g on (b.OT_CODE=g.OT_CODE)
						 $search_condition
						 order by		a.DEPARTMENT_ID, $order_by ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			$ORG_NAME_REF = "";
			if($ORG_ID_REF){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME_REF = $data2[ORG_NAME];
			}
			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :
					if($POS_NO != trim($data[POS_NO_NAME]).trim($data[POS_NO])){
						$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);

						$addition_condition = generate_condition($rpt_order_index);

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);

						if($rpt_order_index == (count($arr_rpt_order) - 1)){	
							$data_row++;
							$POS_ID = $data[POS_ID];
							$ORG_NAME = trim($data[ORG_NAME]);
							$ORG_NAME2 = "";
							$PM_NAME = trim($data[PM_NAME]);
							$PL_NAME = trim($data[PL_NAME]);
							$CL_NAME = trim($data[CL_NAME]);
							$PT_NAME = trim($data[PT_NAME]);
							$OT_NAME = trim($data[OT_NAME]);
							$PV_NAME = trim($data[PV_NAME]);
							
							if ($DEPARTMENT_NAME=="กรมการปกครอง") 
								$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
												PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
												 from		PER_PERSONAL
												 where	PAY_ID=$POS_ID and PER_TYPE=1 and PER_STATUS=1 ";
							else
								$cmd = " select 	PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, LEVEL_NO, 
												PER_SALARY,PER_MGTSALARY, PER_NAME , PER_SURNAME,PER_CARDNO
												 from		PER_PERSONAL
												 where	POS_ID=$POS_ID and PER_TYPE=1 and PER_STATUS=1 ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$PER_ID = $data2[PER_ID];
							$PER_GENDER = $data2[PER_GENDER];
							$PER_NAME = $data2[PER_NAME];
							$PER_SURNAME = $data2[PER_SURNAME];
							$PER_CARDNO = $data2[PER_CARDNO];
							$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
							$PER_BIRTHDATE = substr(trim($data2[PER_BIRTHDATE]), 0, 10);
							$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $RETIREDATE_Y = "";
							if($PER_BIRTHDATE){
								$arr_temp = explode("-", $PER_BIRTHDATE);
								$BIRTHDATE_D = $arr_temp[2];
								$BIRTHDATE_M = $arr_temp[1];
								$BIRTHDATE_Y = substr(($arr_temp[0] + 543), -2);
								$RETIREDATE_Y = ("$BIRTHDATE_M-$BIRTHDATE_D" >= "10-01")?($arr_temp[0] + 543 + 61):($arr_temp[0] + 543 + 60);
							} // end if
							$PER_STARTDATE = substr(trim($data2[PER_STARTDATE]), 0, 10);
							$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
							if($PER_STARTDATE){
								$arr_temp = explode("-", $PER_STARTDATE);
								$STARTDATE_D = $arr_temp[2];
								$STARTDATE_M = $arr_temp[1];
								$STARTDATE_Y = substr(($arr_temp[0] + 543), -2);
							} // end if
							$LEVEL_NO = trim($data2[LEVEL_NO]);
							$PER_SALARY = $data2[PER_SALARY];
							$PER_MGTSALARY = $data2[PER_MGTSALARY];					
							
								$cmd = " select sum(EX_AMT) as EX_AMT from PER_POS_MGTSALARY a, PER_EXTRATYPE b 
												  where trim(a.EX_CODE)=trim(b.EX_CODE) and POS_ID=$POS_ID and POS_STATUS = 1 ";
								$db_dpis2->send_cmd($cmd);
								$data2 = $db_dpis2->get_array();
								$EX_AMT = $data2[EX_AMT];
								if ($EX_AMT) $PER_MGTSALARY = $EX_AMT;

							$cmd="select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
							$db_dpis2->send_cmd($cmd);
							$data_level = $db_dpis2->get_array();
							$LEVEL_NAME=$data_level[LEVEL_NAME];
							$NEW_POSITION_TYPE=$data_level[POSITION_TYPE];
							$arr_temp = explode(" ", $LEVEL_NAME);
							$LEVEL_NAME =  $arr_temp[1];
							
							$EDU_TYPE="";		$EL_NAME = "";			$EM_NAME = "";		$EN_SHORTNAME="";		$EN_NAME="";
							//หาข้อมูลการศึกษาเลือก วุฒิสูงสุด ถ้าไม่มีเอาวุฒิในตน.ปัจจุบันมา
							if($PER_ID){
									if($DPISDB=="odbc"){
										$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													    from ( 	
											 						(
																		PER_EDUCATE a
																		left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																	) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
													  where a.PER_ID=$PER_ID and a.EDU_TYPE like '%4%' ";									
									}elseif($DPISDB=="oci8"){
										$cmd = " select	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
														 from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
													   where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%4%' 
													   			and a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) 
																and a.EM_CODE=d.EM_CODE(+) ";				
									}elseif($DPISDB=="mysql"){
										$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
													    from ( 	
														 			(
																		PER_EDUCATE a
																		left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																	) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
													    where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%4%' ";	
									} // end if
									$ed = $db_dpis2->send_cmd($cmd);
									if ($ed) { 
										$data2 = $db_dpis2->get_array();
									
										$EDU_TYPE = trim($data2[EDU_TYPE]);
										$EL_NAME = trim($data2[EL_NAME]);
										$EM_NAME = trim($data2[EM_NAME]);		//สาขา
										$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
										$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
									} else {
										if($DPISDB=="odbc"){
											$cmd = " select 	a.EDU_TYPE,c.EL_NAME,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
															from ( 	
																		(
																			PER_EDUCATE a
																			left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																		) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																	) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
														  where a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' ";									
										}elseif($DPISDB=="oci8"){
											$cmd = " select	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
															 from PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCLEVEL c,PER_EDUCMAJOR d
														   where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%2%'
																	and a.EN_CODE=b.EN_CODE(+) and b.EL_CODE=c.EL_CODE(+) 
																	and a.EM_CODE=d.EM_CODE(+) ";				
										}elseif($DPISDB=="mysql"){
											$cmd = " select 	a.EDU_TYPE,c.EL_NAME ,d.EM_NAME,b.EN_SHORTNAME,b.EN_NAME
															from ( 	
																		(
																			PER_EDUCATE a
																			left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																		) left join PER_EDUCLEVEL c on (b.EL_CODE=c.EL_CODE)
																	) left join PER_EDUCMAJOR d on (a.EM_CODE=d.EM_CODE)
															where a.PER_ID=$PER_ID and  a.EDU_TYPE like '%2%' ";	
										} // end if
										$db_dpis2->send_cmd($cmd);
										$data2 = $db_dpis2->get_array();
									
										$EDU_TYPE = trim($data2[EDU_TYPE]);
										$EL_NAME = trim($data2[EL_NAME]);
										$EM_NAME = trim($data2[EM_NAME]);		//สาขา
										$EN_SHORTNAME =  trim($data2[EN_SHORTNAME]);
										$EN_NAME =  trim($data2[EN_NAME]);		//ชื่อวุฒิ
									} // end if
							} // end if
							
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][order] = $data_row;
							$arr_content[$data_count][pos_no] = $POS_NO;
							$arr_content[$data_count][org_name] = $ORG_NAME;
							$arr_content[$data_count][org_name2] = $ORG_NAME2;
							$arr_content[$data_count][pm_name] = ($PM_NAME?$PM_NAME:$PL_NAME);
							$arr_content[$data_count][pl_name] = "$PL_NAME $CL_NAME";
							$arr_content[$data_count][cl_name] = $CL_NAME;
							$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
							//$arr_content[$data_count][pt_name] = $PT_NAME;
							$arr_content[$data_count][ot_name] = $OT_NAME;
							$arr_content[$data_count][pv_name] = $PV_NAME;
							$arr_content[$data_count][per_name] = $PER_NAME;
							$arr_content[$data_count][per_surname] = $PER_SURNAME;
							$arr_content[$data_count][per_cardno] =(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID);
							$arr_content[$data_count][per_gender] = ($PER_GENDER==1?"ชาย":($PER_GENDER==2?"หญิง":"ว่าง"));
							$arr_content[$data_count][birthdate_d] = $BIRTHDATE_D;
							$arr_content[$data_count][birthdate_m] = $BIRTHDATE_M;
							$arr_content[$data_count][birthdate_y] = $BIRTHDATE_Y;
							$arr_content[$data_count][startdate_d] = $STARTDATE_D;
							$arr_content[$data_count][startdate_m] = $STARTDATE_M;
							$arr_content[$data_count][startdate_y] = $STARTDATE_Y;
							$arr_content[$data_count][retiredate_y] = $RETIREDATE_Y;
							$arr_content[$data_count][level_no] = $LEVEL_NAME;
							$arr_content[$data_count][per_salary] = ($PER_SALARY?number_format($PER_SALARY):"");
							$arr_content[$data_count][per_mgtsalary] = ($PER_MGTSALARY?number_format($PER_MGTSALARY):"");
							$arr_content[$data_count][el_name] = 	$EL_NAME;	//"$EDU_TYPE - $EL_NAME - $EM_NAME - $EN_SHORTNAME";
							$arr_content[$data_count][em_name] = $EN_NAME;	//$EN_SHORTNAME;
	
							$data_count++;														
						} // end if
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
//		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$ORG_NAME2 = $arr_content[$data_count][org_name2];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$CL_NAME = $arr_content[$data_count][cl_name];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			//$PT_NAME = $arr_content[$data_count][pt_name];
			$OT_NAME = $arr_content[$data_count][ot_name];
			$PV_NAME = $arr_content[$data_count][pv_name];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$BIRTHDATE_D = $arr_content[$data_count][birthdate_d];
			$BIRTHDATE_M = $arr_content[$data_count][birthdate_m];
			$BIRTHDATE_Y = $arr_content[$data_count][birthdate_y];
			$STARTDATE_D = $arr_content[$data_count][startdate_d];
			$STARTDATE_M = $arr_content[$data_count][startdate_m];
			$STARTDATE_Y = $arr_content[$data_count][startdate_y];
			$RETIREDATE_Y = $arr_content[$data_count][retiredate_y];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EM_NAME = $arr_content[$data_count][em_name];
			
			if($REPORT_ORDER == "ORG_REF"){
				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				//$pdf->report_title = "$ORG_NAME_REF||$report_title";
				$pdf->report_title = "$report_title";
				$pdf->AddPage();
				print_header();
			}else{
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->Cell($heading_width[0], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO)
, $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$ORG_NAME2", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[4], 7, "$PM_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[5], 7, "$PL_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[6], 7, "$CL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[7], 7, "$NEW_POSITION_TYPE", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[8], 7, "$OT_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[9], 7, "$PV_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[10], 7, "$PER_NAME   $PER_SURNAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[11], 7, "$PER_CARDNO", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[12], 7, "$PER_GENDER", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[13], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_D):$BIRTHDATE_D)
, $border, 0, 'C', 0);
				$pdf->Cell($heading_width[14], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_M):$BIRTHDATE_M), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[15], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE_Y):$BIRTHDATE_Y), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[16], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_D):$STARTDATE_D), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[17], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_M):$STARTDATE_M), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[18], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE_Y):$STARTDATE_Y), $border, 0, 'C', 0);
				$pdf->Cell($heading_width[19], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($RETIREDATE_Y):$RETIREDATE_Y), $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[20], 7, "$LEVEL_NO", $border,'L');
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18] + $heading_width[19] + $heading_width[20] ;
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[21], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[22], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_MGTSALARY):$PER_MGTSALARY), $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[23], 7, "$EL_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18] + $heading_width[19] + $heading_width[20] + $heading_width[21] + $heading_width[22] + $heading_width[23];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[24], 7, "$EM_NAME", $border, "C");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14] + $heading_width[15] + $heading_width[16] + $heading_width[17] + $heading_width[18] + $heading_width[19] + $heading_width[20] + $heading_width[21] + $heading_width[22] + $heading_width[23] + $heading_width[24];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[25], 7, "", $border, 0, 'L', 0);
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=25; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if(($pdf->h - $max_y - 10) < 22){ 
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
			} // end if
		} // end for	
		$pdf->Cell(285,7,"ผู้ให้ข้อมูล.................................................................โทร..................................................",0,1,'L');
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
?>