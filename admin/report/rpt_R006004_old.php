<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$line_code = "b.PL_CODE";
		$line_name = "f.PL_NAME";
		$position_no= "b.POS_NO";
		$position_no_name= "b.POS_NO_NAME";
		$type_code ="b.PT_CODE";
		$select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$line_code = "b.PN_CODE";
		$line_name = "f.PN_NAME";
		$position_no= "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$line_code = "b.EP_CODE";
		$line_name = "f.EP_NAME";
		$position_no= "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$line_code = "b.TP_CODE";
		$line_name = "f.TP_NAME";
		$position_no= "b.POT_NO";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
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
				else if($select_org_structure == 1) $select_list .= "a.ORG_ID";

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
	if($DPISDB=="odbc"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="oci8"){
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) < '". ($search_budget_year - 543) ."-12-31')";
	}elseif($DPISDB=="mysql"){
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) >= '". ($search_budget_year - 543 - 5) ."-01-01')";
		$arr_search_condition[] = "(LEFT(trim(e.SAH_EFFECTIVEDATE), 10) < '". ($search_budget_year - 543) ."-12-31')";
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

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
//	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน $PERSON_TYPE[$search_per_type] ย้อนหลัง 5 ปี||กรม/ส่วนราชการที่เทียบเท่า $DEPARTMENT_NAME";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0604";
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
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "13";
	$heading_width[1] = "50";
	$heading_width[2] = "15";
	$heading_width[3] = "42";
	$heading_width[4] = "20";
	$heading_width[5] = "15";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		global $search_budget_year,$NUMBER_DISPLAY;
       $aaa =(($NUMBER_DISPLAY==2)?convert2thaidigit(1):1);
	   $bbb =(($NUMBER_DISPLAY==2)?convert2thaidigit(2):2);
	       
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[5] * 10) ,7,"ปีงบประมาณ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"เลขที่",'LR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง",'LR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year)):($search_budget_year)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year)):($search_budget_year)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 1)):($search_budget_year - 1)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 1)):($search_budget_year - 1)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 2)):($search_budget_year - 2)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 2)):($search_budget_year - 2)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 3)):($search_budget_year - 3)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 3)):($search_budget_year - 3)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 4)):($search_budget_year - 4)),'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($search_budget_year - 4)):($search_budget_year - 4)),'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ปัจจุบัน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $bbb",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่  $aaa",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $bbb",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่  $aaa",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $bbb",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $aaa",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $bbb",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $aaa",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $bbb",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ครั้งที่ $aaa",'LBR',1,'C',1);
	} // function		

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
						if($ORG_ID_1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID_1)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
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
	
//	if ($search_condition) $search_condition = $search_condition." and a.PER_ID < 1000"; else $search_condition = "a.PER_ID < 1000";
	if($DPISDB=="odbc"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, IIf(IsNull($position_no), 0, CLng($position_no)) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
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
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, TO_NUMBER($position_no) as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_SALARYHIS e, $line_table f
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID and $line_join(+)
											$search_condition
						 order by		$order_by, TO_NUMBER($position_no), SUBSTR(trim(e.SAH_EFFECTIVEDATE), 1, 10) desc ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			$select_list, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no as POS_NO, $position_no_name as POS_NO_NAME,
											$line_code as PL_CODE, $line_name as PL_NAME, a.LEVEL_NO, a.PER_SALARY,
											LEFT(trim(e.SAH_EFFECTIVEDATE), 10) as SAH_EFFECTIVEDATE, e.MOV_CODE, e.SAH_SALARY, e.SAH_PERCENT_UP $select_type_code
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
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd."<br>";
	$data_count = $data_row = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		if (!($MINISTRY_ID==$DEPARTMENT_ID && $DEPARTMENT_ID==1)) {
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
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
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
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

			if($rpt_order_index == (count($arr_rpt_order) - 1)){
				if($PER_ID != $data[PER_ID]){
					$data_row++;
							
					$PER_ID = $data[PER_ID];
					$PN_NAME = trim($data[PN_NAME]);
					$PER_NAME = trim($data[PER_NAME]);
					$PER_SURNAME = trim($data[PER_SURNAME]);
					//$POS_NO = $data[POS_NO];
					$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
					$PL_CODE = trim($data[PL_CODE]);
					$PL_NAME = trim($data[PL_NAME]);
					$LEVEL_NO = $data[LEVEL_NO];
					if(trim($type_code)){
						$PT_CODE = trim($data[PT_CODE]);
						$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$PT_NAME = trim($data3[PT_NAME]);
					}
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
					$arr_content[$data_count][salary] = $PER_SALARY;
					for($i=1; $i<=10; $i++){ 
						$arr_content[$data_count]["count_".$i] = 0;
						$arr_content[($data_count + 1)]["count_".$i] = 0;
					} // end for
	
					$data_count++;
						
					$arr_content[$data_count][type] = "DETAIL2";
					$data_count++;
				} // end if

				$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
				$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
				$SAH_YEAR = $arr_temp[0] + 543;
				$SAH_MONTH = $arr_temp[1] + 0;
				$SAH_DATE = $arr_temp[2] + 0;
							
				$MOV_CODE = trim($data[MOV_CODE]);
				$PR_LEVEL = 0;
				if ($BKK_FLAG==1) {
					if ($MOV_CODE == '030' || $MOV_CODE == '052') {
						$PR_LEVEL = 0.5;
				    } elseif ($MOV_CODE == '013' || $MOV_CODE == '032') {
						$PR_LEVEL = 1.0;
					} elseif ($MOV_CODE == '031' || $MOV_CODE == '033') {
						$PR_LEVEL = 1.5;
					} elseif ($MOV_CODE == '014' || $MOV_CODE == '034') {
						$PR_LEVEL = 2.0;
					} elseif ($MOV_CODE == '14' || $MOV_CODE == '25' || $MOV_CODE == '52' || $MOV_CODE == '53' || $MOV_CODE == '044' || $MOV_CODE == '050' || 
						$MOV_CODE == '059' || $MOV_CODE == '20020' || $MOV_CODE == '20021' || $MOV_CODE == '20027') {
						if ($SM_CODE == '2') {
							$PR_LEVEL = 0.5;
						} elseif ($SM_CODE == '3') {
							$PR_LEVEL = 1.0;
						} elseif ($SM_CODE == '4') {
							$PR_LEVEL = 1.5;
						} elseif ($SM_CODE == '5') {
							$PR_LEVEL = 2.0;
						}
					} else {
						$PR_LEVEL = 0;
					}
				} else { 
					if ($MOV_CODE == '21310' || $MOV_CODE == '21351') {
						$PR_LEVEL = 0.5;
					} elseif ($MOV_CODE == '21320' || $MOV_CODE == '21352') {
						$PR_LEVEL = 1.0;
					} elseif ($MOV_CODE == '21330' || $MOV_CODE == '21353') {
						$PR_LEVEL = 1.5;
					} elseif ($MOV_CODE == '21340' || $MOV_CODE == '21354') {
						$PR_LEVEL = 2.0;
					} else {
						$PR_LEVEL = 0;
					}
				} // end if
				$SAH_SALARY = $data[SAH_SALARY];
				$SAH_PERCENT_UP = $data[SAH_PERCENT_UP];
				if ($SAH_PERCENT_UP) $PR_LEVEL = $SAH_PERCENT_UP;
				
				if($SAH_MONTH == 10){
					$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] += $PR_LEVEL;
					if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 1)] = $SAH_SALARY;
				}elseif($SAH_MONTH == 4){
					$arr_content[($data_count - 2)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] += $PR_LEVEL;
					if($SAH_SALARY > $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)]) $arr_content[($data_count - 1)]["count_".((($search_budget_year - $SAH_YEAR) * 2) + 2)] = $SAH_SALARY;
				}
			} // end if
		} // end for
		}
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
			$PER_SALARY = $arr_content[$data_count][salary];
			for($i=1; $i<=10; $i++) ${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
			
			$border = "";
			if($REPORT_ORDER == "DETAIL" || $REPORT_ORDER == "DETAIL2") $pdf->SetFont($font,'',14);
			else $pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[2], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[3], 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($POSITION):$POSITION), $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			if($REPORT_ORDER == "DETAIL"){
				$pdf->Cell($heading_width[4], 7,  (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY)), $border, 0, 'R', 0);
				for($i=1; $i<=10; $i++) $pdf->Cell($heading_width[5], 7,(${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit(${"COUNT_".$i}):${"COUNT_".$i}):"-"), $border, 0, 'C', 0);
			}elseif($REPORT_ORDER == "DETAIL2"){
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'R', 0);
				for($i=1; $i<=10; $i++) $pdf->Cell($heading_width[5], 7,(${"COUNT_".$i}?(($NUMBER_DISPLAY==2)?convert2thaidigit("(".number_format(${"COUNT_".$i}).")"):("(".number_format(${"COUNT_".$i})).")"):""), $border, 0, 'C', 0);
			}else{
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'R', 0);
				for($i=1; $i<=10; $i++) $pdf->Cell($heading_width[5], 7, "", $border, 0, 'C', 0);
			} // end if

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=15; $i++){
				if($i <= 4){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				}else{
					$line_start_y = $start_y;		$line_start_x += $heading_width[5];
					$line_end_y = $max_y;		$line_end_x += $heading_width[5];
				} // end if
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
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
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
//		echo "********** ไม่มีข้อมูล **********";
	} // end if

	$pdf->close();
	$pdf->Output();	
?>