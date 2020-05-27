<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", "1800");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.POS_ID >= 0 or a.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	$arr_search_condition[] = "(b.PT_CODE = '32')";

	$list_type_text = "ทั้งส่วนราชการ";

	if($list_type == "PER_ORG_TYPE_1"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}elseif($list_type == "PER_ORG_TYPE_2"){
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}elseif($list_type == "PER_ORG"){
		// สำนัก/กอง , ฝ่าย , งาน
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
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
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
		// ทั้งส่วนราชการ
		if($DEPARTMENT_ID){
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF = $DEPARTMENT_ID)";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			if($select_org_structure==0)
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			elseif($select_org_structure==1)
				$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			elseif($select_org_structure==1) $arr_search_condition[] = "(e.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			if($select_org_structure==0) $arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			elseif($select_org_structure==1) $arr_search_condition[] = "(trim(e.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายงานประวัติวุฒิการศึกษานักบริหาร";
	$report_code = "R1006";
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

	$heading_width[0] = "80";
	$heading_width[1] = "80";
	$heading_width[2] = "30";
	$heading_width[3] = "25";
	$heading_width[4] = "35";
	$heading_width[5] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"กระทรวง / กรม     ชื่อ - สกุล",'TB',0,'L',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'TB',0,'L',1);
		$pdf->Cell($heading_width[2] ,7,"ระดับวุฒิการศึกษา",'TB',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อวุฒิการศึกษา",'TB',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"สาขาวิชาเอก",'TB',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ปีเกษียณอายุ",'TB',1,'C',1);
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
				   from			(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, c.ORG_ID_REF, a.LEVEL_NO desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MAX(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10)
			  	";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME											
				   from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_TYPE g, PER_MGT h, 
				   				PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_DECORATION l
				   where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and c.ORG_ID_REF=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PT_CODE=g.PT_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
								and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and k.DC_CODE=l.DC_CODE(+) and l.DC_TYPE in (1, 2)
								$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10),
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, c.ORG_ID_REF, a.LEVEL_NO desc, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), MAX(l.DC_ORDER), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
			  	";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
				   from			(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, c.ORG_ID_REF, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, c.ORG_ID_REF, a.LEVEL_NO desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MAX(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10)
			  	";
	} // end if 

	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = $data[PM_NAME];

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$arr_temp = explode("-", $PER_BIRTHDATE);
		$PER_BIRTHDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);
		$PER_RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);

		$PER_SALARY = $data[PER_SALARY];
		
		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = $data2[EFFECTIVEDATE];
		$arr_temp = explode("-", $LEVEL_EFFECTIVEDATE);
		$LEVEL_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);

		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_EFFECTIVEDATE = $data2[EFFECTIVEDATE];
		$arr_temp = explode("-", $POSITION_EFFECTIVEDATE);
		$POSITION_EFFECTIVEDATE = ($arr_temp[2] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[0] + 543);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = $data_row . str_repeat(" ", 5) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"ระดับ ".level_no_format($LEVEL_NO)) . (trim($PM_NAME)?")":"");
		$arr_content[$data_count][pt_name] = $PT_NAME;
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][position_effectivedate] = $POSITION_EFFECTIVEDATE;
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$POSITION_EFFECTIVEDATE = $arr_content[$data_count][position_effectivedate];
			$RETIREYEAR = $arr_content[$data_count][retireyear];

			if($REPORT_ORDER == "MINISTRY"){
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$border = "0";
				$pdf->SetFont('angsab','',16);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$ORG_NAME = $arr_content[$data_count][name];

				$pdf->MultiCell(array_sum($heading_width), 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + array_sum($heading_width);
				$pdf->y = $start_y;

				if(($pdf->h - $max_y - 10) < 15){ 
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$border = "0";
				$pdf->SetFont('angsab','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$ORG_NAME = $arr_content[$data_count][name];

				$pdf->MultiCell(array_sum($heading_width), 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + array_sum($heading_width);
				$pdf->y = $start_y;

				if(($pdf->h - $max_y - 10) < 15){ 
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			}elseif($REPORT_ORDER == "CONTENT"){
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$border = "";
				$pdf->SetFont('angsa','',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[1], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[2], 7, "$PT_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[3], 7, "$LEVEL_EFFECTIVEDATE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$POSITION_EFFECTIVEDATE", $border, 0, 'C', 0);
		$pdf->Cell($heading_width[5], 7, "$RETIREYEAR", $border, 0, 'C', 0);
	
				if(($pdf->h - $max_y - 10) < 15){ 
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				} // end if
				$pdf->x = $start_x;			$pdf->y = $max_y;
			} // end if			
		} // end for
	}else{
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();			

?>