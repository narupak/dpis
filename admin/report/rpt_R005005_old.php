<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
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
//	$search_per_type = 1;

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

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
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)){ 
		if($select_org_structure==0){
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		}else if($select_org_structure==1){
			$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		}
		$list_type_text = "$search_org_name";
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||ประวัติสำหรับเสนอขอพระราชทานเหรียญจักรพรรดิมาลา";
	$report_code = "R0505";
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
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$heading_width[0] = "20";
	$heading_width[1] = "50";
	$heading_width[2] = "50";
	$heading_width[3] = "10";
	$heading_width[4] = "20";
	$heading_width[5] = "50";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"วันเดือนปี",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"กรมหรือกระทรวง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"อายุ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่รับราชการ",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"",'LBR',1,'C',1);
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID;
				
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
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
		
	if ($BKK_FLAG==1) $DC_CODE_COND = "17";
	else $DC_CODE_COND = "61";
	$cmd = " select PER_ID from PER_DECORATEHIS where trim(DC_CODE)='$DC_CODE_COND' order by PER_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) $arr_exclude_person[] = $data[PER_ID];
	
	$cmd = " select 	DCON_TYPE, LEVEL_NO, DC_CODE, DCON_TIME1, DCON_TIME2, DC_CODE_OLD, DCON_TIME3, DCON_MGT 
					 from 		PER_DECORCOND
					 where	trim(DC_CODE)='$DC_CODE_COND' and DCON_TYPE=$search_per_type
					 order by DCON_TYPE, LEVEL_NO ";
	$count_cond = $db_dpis1->send_cmd($cmd);
//	echo $cmd;
	$data_count = 0;
	while($data1 = $db_dpis1->get_array()){
		unset($arr_addition_condition);
		unset($arr_include_person);

		if($data1[DCON_TIME1]){ 
			$input_date = ($search_year - 543) . "-12-05";
			$adjust_date = date_adjust($input_date, "year", (-1 * ($data1[DCON_TIME1] + 0)));
			if($DPISDB=="odbc") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
			elseif($DPISDB=="oci8") $arr_addition_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) <= '$adjust_date')";
			elseif($DPISDB=="mysql") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
		} // end if		
		if($data1[DCON_TIME2]){ 
			$input_date = ($search_year - 543) . "-12-05";
			$adjust_date = date_adjust($input_date, "year", (-1 * ($data1[DCON_TIME2] + 0)));
			if($DPISDB=="odbc") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
			elseif($DPISDB=="oci8") $arr_addition_condition[] = "(SUBSTR(a.PER_STARTDATE, 1, 10) <= '$adjust_date')";
			elseif($DPISDB=="mysql") $arr_addition_condition[] = "(LEFT(a.PER_STARTDATE, 10) <= '$adjust_date')";
		} // end if	
		if(trim($data1[DC_CODE_OLD])){
			if(trim($data1[DCON_TIME3])){
				$input_date = ($search_year - 543) . "-12-05";
				$adjust_date = date_adjust($input_date, "year", (-1 * ($data1[DCON_TIME3] + 0)));
				if($DPISDB=="odbc")
					$cmd = " select 	PER_ID 
									 from 		PER_DECORATEHIS 
									 where 	trim(DC_CODE)='". trim($data1[DC_CODE_OLD]) ."' and LEFT(DEH_DATE, 10) <= '$adjust_date'
													". (count($arr_exclude_person)?(" and PER_ID not in (". implode(",", $arr_exclude_person) .")"):"") ."
									 order by PER_ID ";
				elseif($DPISDB=="oci8")
					$cmd = " select 	PER_ID 
									 from 		PER_DECORATEHIS 
									 where 	trim(DC_CODE)='". trim($data1[DC_CODE_OLD]) ."' and SUBSTR(DEH_DATE, 1, 10) <= '$adjust_date'
													". (count($arr_exclude_person)?(" and PER_ID not in (". implode(",", $arr_exclude_person) .")"):"") ."
									 order by PER_ID ";
				elseif($DPISDB=="mysql")
					$cmd = " select 	PER_ID 
									 from 		PER_DECORATEHIS 
									 where 	trim(DC_CODE)='". trim($data1[DC_CODE_OLD]) ."' and LEFT(DEH_DATE, 10) <= '$adjust_date'
													". (count($arr_exclude_person)?(" and PER_ID not in (". implode(",", $arr_exclude_person) .")"):"") ."
									 order by PER_ID ";
			}else{
				$cmd = " select 	PER_ID 
								 from 		PER_DECORATEHIS 
								 where 	trim(DC_CODE)='". trim($data1[DC_CODE_OLD]) ."' 
								 				". (count($arr_exclude_person)?(" and PER_ID not in (". implode(",", $arr_exclude_person) .")"):"") ."
								 order by PER_ID ";
			} // end if
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_include_person[] = $data[PER_ID];
		} // end if
		if($data1[DCON_MGT]) $arr_addition_condition[] = "(b.PM_CODE IS NOT NULL)";
		$search_level_no = trim($data1[LEVEL_NO]);
		if(count($arr_exclude_person)) $arr_addition_condition[] = "(a.PER_ID not in (". implode(",", $arr_exclude_person) ."))";

		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);
		
		$addition_condition = $search_condition . (($addition_condition)?" and $addition_condition ":"");

		if($DPISDB=="odbc"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE
								from		(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and a.LEVEL_NO=e.LEVEL_NO)
												) left join $line_table f on ($line_join)
								where		a.LEVEL_NO='$search_level_no'
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												LEFT(trim(a.PER_STARTDATE), 10) $select_type_code
								order by a.DEPARTMENT_ID, LEFT(trim(a.PER_STARTDATE), 10), a.LEVEL_NO ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE
								from		PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, $line_table f
								where		a.LEVEL_NO='$search_level_no'
												and $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
												and a.PER_ID=e.PER_ID(+) and a.LEVEL_NO=e.LEVEL_NO(+) and $line_join(+)
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												SUBSTR(trim(a.PER_STARTDATE), 1, 10) $select_type_code
								order by a.DEPARTMENT_ID, SUBSTR(trim(a.PER_STARTDATE), 1, 10), a.LEVEL_NO ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name as PL_CODE, $pl_name as PL_NAME $select_type_code
												,LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE
								from		(
													(
														(
															(
																PER_PERSONAL a
																left join $position_table b on ($position_join)
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID and a.LEVEL_NO=e.LEVEL_NO)
												) left join $line_table f on ($line_join)
								where		a.LEVEL_NO='$search_level_no'
												$addition_condition
								group by a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
												a.LEVEL_NO, a.DEPARTMENT_ID, b.ORG_ID, c.ORG_NAME, $pl_name,
												LEFT(trim(a.PER_STARTDATE), 10) $select_type_code
								order by a.DEPARTMENT_ID, LEFT(trim(a.PER_STARTDATE), 10), a.LEVEL_NO ";
		} // end if
			if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_NAME = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$ORG_NAME = $data[ORG_NAME];
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$COMPLETE_CONDDATE = date_adjust($PER_STARTDATE, "year", 25);
			$PER_STARTDATE = show_date_format(trim($PER_STARTDATE),$DATE_DISPLAY);
			
			$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION_LEVEL = $data2[POSITION_LEVEL];

			if(ORG_ID_REF != $data[ORG_ID_REF]){
				$ORG_ID_REF = $data[ORG_ID_REF];
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
				
				$REF_ORG_ID_REF = $data2[ORG_ID_REF];
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$REF_ORG_ID_REF ";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];
			} // end if
			
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = (trim($PL_NAME))? "$PL_NAME". $POSITION_LEVEL : "";
			if(trim($type_code)){
				$PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where	PT_CODE=".$PT_CODE;
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
				$PL_NAME .= (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"");
			}
			
			$arr_person[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_person[$data_count][ministry_name] = $MINISTRY_NAME;
			$arr_person[$data_count][department_name] = $DEPARTMENT_NAME;
			$arr_person[$data_count][org_name] = $ORG_NAME;
			$arr_person[$data_count][per_id] = $PER_ID;
			$arr_person[$data_count][per_name] = $PER_NAME;
			$arr_person[$data_count][per_birthdate] = $PER_BIRTHDATE;
			$arr_person[$data_count][level_no] = $LEVEL_NO;
			$arr_person[$data_count][per_startdate] = $PER_STARTDATE;
			$arr_person[$data_count][complete_conddate] = $COMPLETE_CONDDATE;
			$arr_person[$data_count][pl_name] = $PL_NAME;

			$data_count++;
		} // end while
	} // end while
	
//	echo "<pre>"; print_r($arr_person); echo "</pre>";

	$sort_arr[0]['name'] = "org_id_ref";
	$sort_arr[0]['sort'] = "ASC";
	$sort_arr[0]['case'] = FALSE; //  Case sensitive
			
	$sort_arr[1]['name'] = "level_no";
	$sort_arr[1]['sort'] = "DESC";
	$sort_arr[1]['case'] = FALSE;

	$sort_arr[2]['name'] = "per_startdate";
	$sort_arr[2]['sort'] = "ASC";
	$sort_arr[2]['case'] = FALSE;
			
	array_sort($arr_person, $sort_arr);		

//	echo "<pre>"; print_r($arr_person); echo "</pre>";
	$count_data = count($arr_person);
	$data_count = 0;
	for($i=0; $i<$count_data; $i++){
		$ORG_ID_REF = $arr_person[$i][org_id_ref];
		$MINISTRY_NAME = $arr_person[$i][ministry_name];
		$DEPARTMENT_NAME = $arr_person[$i][department_name];
		$ORG_NAME = $arr_person[$i][org_name];
		$PER_ID = $arr_person[$i][per_id];
		$PER_NAME = $arr_person[$i][per_name];
		$PER_BIRTHDATE = $arr_person[$i][per_birthdate];
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_BIRTHDATE_D = $arr_temp[2] + 0;
			$PER_BIRTHDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$PER_BIRTHDATE_Y = $arr_temp[0] + 543;
		} // end if
		$LEVEL_NO = $arr_person[$i][level_no];
		$PER_STARTDATE = $arr_person[$i][per_startdate];
		$COMPLETE_CONDDATE = $arr_person[$i][complete_conddate];
		if($COMPLETE_CONDDATE){
			$arr_temp = explode("-", $COMPLETE_CONDDATE);
			$COMPLETE_CONDDATE_D = $arr_temp[2] + 0;
			$COMPLETE_CONDDATE_M = $month_full[($arr_temp[1] + 0)][TH];
			$COMPLETE_CONDDATE_Y = $arr_temp[0] + 543;
		} // end if
		$PL_NAME = $arr_person[$i][pl_name];
		
		$arr_content[$data_count][type] = "PERSON";
		$arr_content[$data_count][name] = $PER_NAME;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
		$arr_content[$data_count][ministry_name] = $MINISTRY_NAME;
		$arr_content[$data_count][per_birthdate_d] = $PER_BIRTHDATE_D;
		$arr_content[$data_count][per_birthdate_m] = $PER_BIRTHDATE_M;
		$arr_content[$data_count][per_birthdate_y] = $PER_BIRTHDATE_Y;
		$arr_content[$data_count][complete_conddate_d] = $COMPLETE_CONDDATE_D;
		$arr_content[$data_count][complete_conddate_m] = $COMPLETE_CONDDATE_M;
		$arr_content[$data_count][complete_conddate_y] = $COMPLETE_CONDDATE_Y;
		
		$data_count++;
		
		unset($arr_careerhis);
		unset($sort_arr);
		
		if($DPISDB=="odbc"){
			$cmd = " select 	LEFT(a.POH_EFFECTIVEDATE, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME
							 from		(
												PER_POSITIONHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(POH_EFFECTIVEDATE, 10) ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	SUBSTR(a.POH_EFFECTIVEDATE, 1, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME
							 from		PER_POSITIONHIS a, PER_MOVMENT b
							 where	a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE
							 order by SUBSTR(POH_EFFECTIVEDATE, 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	LEFT(a.POH_EFFECTIVEDATE, 10) as POH_EFFECTIVEDATE, a.LEVEL_NO, a.PL_CODE, a.PT_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.POH_SALARY, a.ORG_ID_1, a.ORG_ID_2, a.ORG_ID_3, a.MOV_CODE, b.MOV_NAME, a.POH_PL_NAME
							 from		(
												PER_POSITIONHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(POH_EFFECTIVEDATE, 10) ";
		} // end if
		$count_hist = $db_dpis->send_cmd($cmd);
		$hist_count = 0;
		while($data = $db_dpis->get_array()){
			$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
			$POH_AGE = floor(date_difference($POH_EFFECTIVEDATE, $PER_BIRTHDATE, "year"));

			$POH_LEVEL_NO = trim($data[LEVEL_NO]);
			$POH_SALARY = trim($data[POH_SALARY]);
			$POH_MOV_NAME = trim($data[MOV_NAME]);
			$POH_PL_NAME = trim($data[POH_PL_NAME]);
			if($search_per_type == 1 || $search_per_type == 5) $POH_PL_CODE = trim($data[PL_CODE]);
			elseif($search_per_type == 2) $POH_PL_CODE = trim($data[PN_CODE]);
			elseif($search_per_type == 3) $POH_PL_CODE = trim($data[EP_CODE]);
			elseif($search_per_type == 4) $POH_PL_CODE = trim($data[TP_CODE]);

			$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POSITION_LEVEL = $data2[POSITION_LEVEL];

			if (!$POH_PL_NAME) {
				$cmd = " select $pl_name as PL_NAME from $line_table b where trim($pl_code)='$POH_PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_PL_NAME = trim($data2[PL_NAME]);
			}

			$POH_POSITION = (trim($POH_PL_NAME))? "$POH_PL_NAME". $POSITION_LEVEL : "";
			if(trim($type_code)){
				$POH_PT_CODE = trim($data[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$POH_PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_PT_NAME = trim($data2[PT_NAME]);

				$POH_POSITION .= (($POH_PT_NAME != "ทั่วไป" && $POH_LEVEL_NO >= 6)?"$POH_PT_NAME":"");
			}
			
			$POH_ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_1 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];

			$POH_ORG_ID_2 = trim($data[ORG_ID_2]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_2 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];
			
			$POH_ORG_ID_3 = trim($data[ORG_ID_3]);
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_ORG_ID_3 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ORG_NAME = $data2[ORG_NAME];

			$arr_careerhis[$hist_count][type] = "POSITION";
			$arr_careerhis[$hist_count][effectivedate] = $POH_EFFECTIVEDATE;
			$arr_careerhis[$hist_count][position] = $POH_POSITION;
			$arr_careerhis[$hist_count][org_name] = $POH_ORG_NAME;
			$arr_careerhis[$hist_count][age] = $POH_AGE;
			$arr_careerhis[$hist_count][salary] = $POH_SALARY;
			$arr_careerhis[$hist_count][note] = $POH_MOV_NAME;

			$hist_count++;
		} // end while		

		if($DPISDB=="odbc"){
			$cmd = " select 	LEFT(a.SAH_EFFECTIVEDATE, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		(
												PER_SALARYHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(SAH_EFFECTIVEDATE, 10) ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	SUBSTR(a.SAH_EFFECTIVEDATE, 1, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		PER_SALARYHIS a, PER_MOVMENT b
							 where	a.PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE
							 order by SUBSTR(SAH_EFFECTIVEDATE, 1, 10) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	LEFT(a.SAH_EFFECTIVEDATE, 10) as SAH_EFFECTIVEDATE, a.SAH_SALARY, a.MOV_CODE, b.MOV_NAME, SAH_POSITION, SAH_ORG
							 from		(
												PER_SALARYHIS a
												inner join PER_MOVMENT b on (a.MOV_CODE=b.MOV_CODE)
											)
							 where	a.PER_ID=$PER_ID
							 order by LEFT(SAH_EFFECTIVEDATE, 10) ";
		} // end if
		$count_hist = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
			$SAH_AGE = floor(date_difference($SAH_EFFECTIVEDATE, $PER_BIRTHDATE, "year"));

			$SAH_SALARY = trim($data[SAH_SALARY]);
			$SAH_MOV_NAME = trim($data[MOV_NAME]);
			$SAH_POSITION = trim($data[SAH_POSITION]);
			$SAH_ORG = trim($data[SAH_ORG]);

			$arr_careerhis[$hist_count][type] = "SALARY";
			$arr_careerhis[$hist_count][effectivedate] = $SAH_EFFECTIVEDATE;
			$arr_careerhis[$hist_count][position] = $SAH_POSITION;
			$arr_careerhis[$hist_count][org_name] = $SAH_ORG;
			$arr_careerhis[$hist_count][age] = $SAH_AGE;
			$arr_careerhis[$hist_count][salary] = $SAH_SALARY;
			$arr_careerhis[$hist_count][note] = $SAH_MOV_NAME;

			$hist_count++;
		} // end while	
		
//		echo "<pre>"; print_r($arr_careerhis); echo "</pre>";
				
		$sort_arr[0]['name'] = "effectivedate";
		$sort_arr[0]['sort'] = "ASC";
		$sort_arr[0]['case'] = FALSE; //  Case sensitive
					
		array_sort($arr_careerhis, $sort_arr);

//		echo "<pre>"; print_r($arr_careerhis); echo "</pre>";

		for($x=0; $x<count($arr_careerhis); $x++){
			$HIST_TYPE = $arr_careerhis[$x][type];
			$EFFECTIVEDATE = show_date_format($arr_careerhis[$x][effectivedate],$DATE_DISPLAY);
			$AGE = $arr_careerhis[$x][age];
			$SALARY = $arr_careerhis[$x][salary];
			$NOTE = $arr_careerhis[$x][note];
			if($HIST_TYPE=="POSITION"){
				$POSITION = $arr_careerhis[$x][position];
				$ORG_NAME = $arr_careerhis[$x][org_name];
			} // end if
			
			$arr_content[$data_count][type] = "HISTORY";
			$arr_content[$data_count][hist_effectivedate] = $EFFECTIVEDATE;
			$arr_content[$data_count][hist_position] = $POSITION;
			$arr_content[$data_count][hist_org] = $ORG_NAME;
			$arr_content[$data_count][hist_age] = $AGE;
			$arr_content[$data_count][hist_salary] = number_format($SALARY);
			$arr_content[$data_count][hist_note] = $NOTE;
			
			$data_count++;
		} // end for

	} // end for

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
//		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];

			if($REPORT_ORDER == "PERSON"){
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				if($data_count > 0){
					if(($pdf->h - $max_y - 10) < 15) $pdf->AddPage();
					$pdf->Cell(200, 7, "", $border, 1, 'L', 0);
					$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
					$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]), 7, ("ลงชื่อ  ". str_repeat(".", 70)), $border, 1, 'C', 0);
					$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
					$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]), 7, "$NAME", $border, 1, 'C', 0);
					$pdf->AddPage();
				} // end if
				
				$NAME = $arr_content[$data_count][name];
				$POSITION = $arr_content[$data_count][position];
				$ORG_NAME = $arr_content[$data_count][org_name];
				$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
				$MINISTRY_NAME = $arr_content[$data_count][ministry_name];
				$PER_BIRTHDATE_D = $arr_content[$data_count][per_birthdate_d];
				$PER_BIRTHDATE_M = $arr_content[$data_count][per_birthdate_m];
				$PER_BIRTHDATE_Y = $arr_content[$data_count][per_birthdate_y];
				$COMPLETE_CONDDATE_D = $arr_content[$data_count][complete_conddate_d];
				$COMPLETE_CONDDATE_M = $arr_content[$data_count][complete_conddate_m];
				$COMPLETE_CONDDATE_Y = $arr_content[$data_count][complete_conddate_y];

				$pdf->Cell(100, 7, "ชื่อ  $NAME", $border, 0, 'L', 0);
				$pdf->Cell(100, 7, "ตำแหน่ง  $POSITION", $border, 1, 'L', 0);

				$pdf->Cell(100, 7, "$ORG_TITLE  $ORG_NAME", $border, 0, 'L', 0);
				$pdf->Cell(100, 7, "$DEPARTMENT_TITLE  $DEPARTMENT_NAME", $border, 1, 'L', 0);

				$pdf->Cell(100, 7, "$MINISTRY_TITLE  $MINISTRY_NAME", $border, 0, 'L', 0);
				$pdf->Cell(100, 7, "เกิดวันที่  $PER_BIRTHDATE_D  เดือน  $PER_BIRTHDATE_M  พ.ศ.  $PER_BIRTHDATE_Y", $border, 1, 'L', 0);
				
				$pdf->Cell(200, 7, "รับราชการมาครบ 25 ปี  เมื่อวันที่  $COMPLETE_CONDDATE_D  เดือน  $COMPLETE_CONDDATE_M  พ.ศ.  $COMPLETE_CONDDATE_Y", $border, 1, 'L', 0);
				$pdf->Cell(200, 7, "", $border, 1, 'L', 0);

				print_header();

				$max_y = $pdf->y;
				$pdf->x = $start_x;			$pdf->y = $max_y;
			}elseif($REPORT_ORDER == "HISTORY"){
				$EFFECTIVEDATE = $arr_content[$data_count][hist_effectivedate];
				$POSITION = $arr_content[$data_count][hist_position];
				$ORG_NAME = $arr_content[$data_count][hist_org];
				$AGE = $arr_content[$data_count][hist_age];
				$SALARY = $arr_content[$data_count][hist_salary];
				$NOTE = $arr_content[$data_count][hist_note];
				
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
				$pdf->Cell($heading_width[0], 7, "$EFFECTIVEDATE", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "$AGE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$SALARY", $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[5], 7, "$NOTE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
				$pdf->y = $start_y;
	
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=5; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
			} // end if

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1) || $arr_content[($data_count + 1)][type] == "PERSON") $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for				

		$border = "";
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		if($data_count > 0){
			if(($pdf->h - $max_y - 10) < 15) $pdf->AddPage();
			$pdf->Cell(200, 7, "", $border, 1, 'L', 0);
			$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
			$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]), 7, ("ลงชื่อ  ". str_repeat(".", 70)), $border, 1, 'C', 0);
			$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
			$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5]), 7, "$NAME", $border, 1, 'C', 0);
		} // end if
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
	
?>