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
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";

				$heading_name .= " สำนัก/กอง";
				break;
			case "ORG_1" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_1";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1";

				$heading_name .= " ฝ่าย";
				break;
			case "ORG_2" :
				if($select_list) $select_list .= ", ";
				$select_list .= "b.ORG_ID_2";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2";

				$heading_name .= " งาน";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				if($search_per_type==1) $select_list .= "b.PL_CODE";
				elseif($search_per_type==2) $select_list .= "b.PN_CODE";
				elseif($search_per_type==3) $select_list .= "b.EP_CODE";

				if($order_by) $order_by .= ", ";
				if($search_per_type==1) $order_by .= "b.PL_CODE";
				elseif($search_per_type==2) $order_by .= "b.PN_CODE";
				elseif($search_per_type==3) $order_by .= "b.EP_CODE";

				if($search_per_type==1) $heading_name .= " สายงาน";
				elseif($search_per_type==2) $heading_name .= " ชื่อตำแหน่ง";
				elseif($search_per_type==3) $heading_name .= " ชื่อตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "b.ORG_ID";
	if(!trim($select_list)) $select_list = "b.ORG_ID";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
//	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
//	if($DPISDB=="odbc") $arr_search_condition[] = "((LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01') or (LEFT(trim(e.POH_ENDDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
//	elseif($DPISDB=="oci8") $arr_search_condition[] = "((SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01') or (SUBSTR(trim(e.POH_ENDDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_ENDDATE), 1, 10) < '".($search_budget_year - 543)."-10-01'))";

	$list_type_text = "ทั้งส่วนราชการ";

	include ("../report/rpt_condition3.php");

	if($DPISDB=="odbc"){ 
		$addwhere="and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
		$search_condition .= $addwhere;
		$addwhere_movement_type2=" or (LEFT(trim(e.POH_ENDDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
	}elseif($DPISDB=="oci8"){ 
		$addwhere =  "and (SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
		$search_condition .= $addwhere;
		$addwhere_movement_type2=" or (SUBSTR(trim(e.POH_ENDDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_ENDDATE), 1, 10) < '".($search_budget_year - 543)."-10-01'))";
	}elseif($DPISDB=="mysql"){ 
		$addwhere =  "and (LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
		$search_condition .= $addwhere;
		$addwhere_movement_type2=" or (LEFT(trim(e.POH_ENDDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_ENDDATE), 10) < '".($search_budget_year - 543)."-10-01'))";
	}	
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if($search_per_type==1) $report_title = "$DEPARTMENT_NAME||อัตราการเข้าออกของข้าราชการในปีงบประมาณ $search_budget_year";
	elseif($search_per_type==2) $report_title = "$DEPARTMENT_NAME||อัตราการเข้าออกของลูกจ้างในปีงบประมาณ $search_budget_year";
	elseif($search_per_type==3) $report_title = "$DEPARTMENT_NAME||อัตราการเข้าออกของพนักงานราชการในปีงบประมาณ $search_budget_year";
	$report_code = "R0306";
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

	$heading_width[0] = "167";
	$heading_width[1] = "20";
	$heading_width[2] = "20";

	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,$heading_name,'LTR',0,'C',1);
		$pdf->Cell(($heading_width[1] * 4) ,7,"จำนวนข้าราชการ",'LTBR',0,'C',1);
		$pdf->Cell(($heading_width[2] * 2) ,7,"อัตรา (ร้อยละ)",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ต้นปี",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เข้า",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ออก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สิ้นปี",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"เข้า",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ออก",'LTBR',1,'C',1);
	} // function		

	function count_person($search_budget_year, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $arr_search_condition, $search_per_type;
		
		$search_condition = "";
		for($i=0; $i<count($arr_search_condition); $i++){
			if(strpos($arr_search_condition[$i], "trim(e.POH_EFFECTIVEDATE)")===false){
				if($search_condition) $search_condition .= " and ";
				$search_condition .= $arr_search_condition[$i];
			} // end if
		} // end for
		if(trim($search_condition)) $search_condition = " where ". $search_condition;
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								 $search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								 $search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								 $search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
								 $search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		/*******
		if($DPISDB=="odbc") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="oci8") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="mysql") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		$count_person_movein = count_person_movement(1, $new_search_condition, "");

		if($DPISDB=="odbc") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".($search_budget_year - 543)."-10-01' or LEFT(trim(e.POH_ENDDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="oci8") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".($search_budget_year - 543)."-10-01' or SUBSTR(trim(e.POH_ENDDATE), 1, 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="mysql") $new_search_condition = (trim($search_condition)?" $search_condition and ":" where ") . "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".($search_budget_year - 543)."-10-01' or LEFT(trim(e.POH_ENDDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		$count_person_moveout = count_person_movement(2, str_replace("b.ORG_ID", "e.ORG_ID_3", $new_search_condition), "");
		*********/
		$count_person_movein = count_person_movement(1, $new_search_condition, ""); //มีอยู่ใน $search_condition แล้ว เหมือน (คือ $addwhere)

		if($DPISDB=="odbc") $new_search_condition =  $search_condition." or LEFT(trim(e.POH_ENDDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="oci8") $new_search_condition = $search_condition." or SUBSTR(trim(e.POH_ENDDATE), 1, 10) >= '".($search_budget_year - 543)."-10-01')";
		elseif($DPISDB=="mysql") $new_search_condition = $search_condition." or LEFT(trim(e.POH_ENDDATE), 10) >= '".($search_budget_year - 543)."-10-01')";
		$count_person_moveout = count_person_movement(2, str_replace("b.ORG_ID", "e.ORG_ID_3", $new_search_condition), "");

		$count_person = $count_person - $count_person_movein + $count_person_moveout;
		return $count_person;
	} // function

	function count_person_movement($movement_type, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $arr_search_condition, $search_per_type, $search_budget_year;
		global	$addwhere_movement_type2;
		
		if(trim($addition_condition)){ 
			$search_condition = "";
			for($i=0; $i<count($arr_search_condition); $i++){
				if(strpos($arr_search_condition[$i], "trim(e.POH_EFFECTIVEDATE)")===false){
					if($search_condition) $search_condition .= " and ";
					$search_condition .= $arr_search_condition[$i];
				} // end if
			} // end for
			if(trim($search_condition)) $search_condition = " where ". $search_condition;
			$search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		} // end if
		
		switch($movement_type){
			case 1 :
				if(trim($addition_condition)){
					//มีอยู่ใน $search_condition แล้ว เหมือน (คือ $addwhere)
					
				} // end if
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520'))";
			break;
			case 2 :
				if(trim($addition_condition)){
					$search_condition .= $search_condition.$addwhere_movement_type2;
				} // end if
				$search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(e.MOV_CODE) in ('106', '10610', '10620', '118', '11810', '11820', '11830', '119', '11910', '120', '12010', '12020', '12030', '12040', '121', '12110', '122', '12210', '123', '12310'))";
			break;
		} // end switch case

		if($search_per_type==1){
			// ข้าราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_POSITIONHIS e
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		}elseif($search_per_type==2){
			// ลูกจ้าง
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_POSITIONHIS e
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		elseif($search_per_type==3){
			// พนักงานราชการ
			if($DPISDB=="odbc"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="oci8"){				
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(a.PER_ID) as count_person
								 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_POSITIONHIS e
								 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
								 					and a.PER_ID=e.PER_ID(+)
													$search_condition
								 group by	a.PER_ID
							   ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select			count(a.PER_ID) as count_person
								 from			(
														(
															PER_PERSONAL a 
															left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
								$search_condition
								 group by	a.PER_ID
							   ";
			} // end if
		} // end if
		
		$count_person_movement = $db_dpis2->send_cmd($cmd);
//		echo "$movement_type :: $addition_condition<br>";
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		if($count_person_movement==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person_movement = 0;
		} // end if

		return $count_person_movement;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE,$EP_CODE;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
					else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
				break;
				case "ORG_1" :
					if($ORG_ID_1) $arr_addition_condition[] = "(b.ORG_ID_1 = $ORG_ID_1)";
					else $arr_addition_condition[] = "(b.ORG_ID_1 = 0 or b.ORG_ID_1 is null)";
				break;
				case "ORG_2" :
					if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
					else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
				break;
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE) $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE')";
						else $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE' or b.PL_CODE is null)";
					}elseif($search_per_type==2){
						if($PN_CODE) $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE')";
						else $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE' or b.PN_CODE is null)";
					} // end if
					elseif($search_per_type==3){
						if($EP_CODE) $arr_addition_condition[] = "(trim(b.EP_CODE) = '$EP_CODE')";
						else $arr_addition_condition[] = "(trim(b.EP_CODE) = '$EP_CODE' or b.EP_CODE is null)";
					} // end if
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE,$EP_CODE;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "ORG_1" :
					$ORG_ID_1 = -1;
				break;
				case "ORG_2" :
					$ORG_ID_2 = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	$search_condition_where = " and (trim(e.MOV_CODE) in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520','106', '10610', '10620', '118', '11810', '11820', '11830', '119', '11910', '120', '12010', '12020', '12030', '12040', '121', '12110', '122', '12210', '123', '12310'))";
	if($search_per_type==1){
		// ข้าราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}
	}elseif($search_per_type==2){
		// ลูกจ้าง
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}
	} // end if
	elseif($search_per_type==3){
		// พนักงานราชการ
		if($DPISDB=="odbc"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			PER_PERSONAL a, PER_POS_EMPSER b, PER_ORG c, PER_POSITIONHIS e
							 where		a.POEMS_ID=b.POEMS_ID(+) and b.ORG_ID=c.ORG_ID(+)
							 					and a.PER_ID=e.PER_ID(+)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			distinct $select_list
							 from			(
													(
														PER_PERSONAL a 
														left join PER_POS_EMPSER b on (a.POEMS_ID=b.POEMS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												$search_condition $search_condition_where
							 order by		$order_by
						   ";
		}
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != $data[ORG_ID]){
						$ORG_ID = $data[ORG_ID];
						if($ORG_ID == ""){
							$ORG_NAME = "[ไม่ระบุสำนัก/กอง]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
//						$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, str_replace("b.ORG_ID", "e.ORG_ID_3", $addition_condition));
						$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						} // end if

						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_1" :
					if($ORG_ID_1 != $data[ORG_ID_1]){
						$ORG_ID_1 = $data[ORG_ID_1];
						if($ORG_ID_1 == ""){
							$ORG_NAME_1 = "[ไม่ระบุฝ่าย]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_1 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != $data[ORG_ID_2]){
						$ORG_ID_2 = $data[ORG_ID_2];
						if($ORG_ID_2 == ""){
							$ORG_NAME_2 = "[ไม่ระบุงาน]";
						}else{
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
						$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
						$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
						$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);

						if($rpt_order_index == 0){ 
							$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
							$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
							$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
							$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
						} // end if
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE != trim($data[PL_CODE])){
							$PL_CODE = trim($data[PL_CODE]);
							if($PL_CODE == ""){
								$PL_NAME = "[ไม่ระบุสายงาน]";
							}else{
								$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
							$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);
	
							if($rpt_order_index == 0){ 
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					}elseif($search_per_type==2){
						if($PN_CODE != trim($data[PN_CODE])){
							$PN_CODE = trim($data[PN_CODE]);
							if($PN_CODE == ""){
								$PN_NAME = "[ไม่ระบุชื่อตำแหน่ง]";
							}else{
								$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PN_NAME = $data2[PN_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PN_NAME;
							$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);
	
							if($rpt_order_index == 0){ 
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
					elseif($search_per_type==3){
						if($EP_CODE != trim($data[EP_CODE])){
							$EP_CODE = trim($data[EP_CODE]);
							if($EP_CODE == ""){
								$EP_NAME = "[ไม่ระบุชื่อตำแหน่ง]";
							}else{
								$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$EP_NAME = $data2[EP_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EP_NAME;
							$arr_content[$data_count][count_1] = count_person(($search_budget_year - 1), $search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person_movement(1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person_movement(2, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person($search_budget_year, $search_condition, $addition_condition);
	
							if($rpt_order_index == 0){ 
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							} // end if
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
				break;		
			} // end switch case
		} // end for
	} // end while
	
	$GRAND_TOTAL = $GRANT_TOTAL_2 + $GRAND_TOTAL_3;
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_1 = $arr_content[$data_count][count_1];
			$COUNT_2 = $arr_content[$data_count][count_2];
			$COUNT_3 = $arr_content[$data_count][count_3];
			$COUNT_4 = $arr_content[$data_count][count_4];
			$COUNT_TOTAL = $COUNT_1 + $COUNT_4;
			$PERCENT_1 = $PERCENT_2 = 0;
			if($COUNT_1){ 
				$PERCENT_2 = ($COUNT_2 / $COUNT_1) * 100;
				$PERCENT_3 = ($COUNT_3 / $COUNT_1) * 100;
			} // end if
			
			$border = "";
			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			}else{
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			} // end if

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[1], 7, ($COUNT_1?number_format($COUNT_1):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_2?number_format($COUNT_2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_3?number_format($COUNT_3):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[1], 7, ($COUNT_4?number_format($COUNT_4):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_2?number_format($PERCENT_2, 2):"-"), $border, 0, 'R', 0);
			$pdf->Cell($heading_width[2], 7, ($PERCENT_3?number_format($PERCENT_3, 2):"-"), $border, 0, 'R', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=6; $i++){
				if($i==0){
					$line_start_y = $start_y;		$line_start_x += $heading_width[0];
					$line_end_y = $max_y;		$line_end_x += $heading_width[0];
				}elseif($i>=1 && $i<=4){
					$line_start_y = $start_y;		$line_start_x += $heading_width[1];
					$line_end_y = $max_y;		$line_end_x += $heading_width[1];
				}elseif($i>4){
					$line_start_y = $start_y;		$line_start_x += $heading_width[2];
					$line_end_y = $max_y;		$line_end_x += $heading_width[2];
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
				
		if($GRAND_TOTAL_1){ 
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL_1) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL_1) * 100;
		} // end if

		$border = "LTBR";
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

		$pdf->MultiCell($heading_width[0], 7, "รวม", $border, "L");
		if($pdf->y > $max_y) $max_y = $pdf->y;
		$pdf->x = $start_x + $heading_width[0];
		$pdf->y = $start_y;
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_1?number_format($GRAND_TOTAL_1):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_2?number_format($GRAND_TOTAL_2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_3?number_format($GRAND_TOTAL_3):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL_4?number_format($GRAND_TOTAL_4):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_2?number_format($PERCENT_TOTAL_2, 2):"-"), $border, 0, 'R', 0);
		$pdf->Cell($heading_width[2], 7, ($PERCENT_TOTAL_3?number_format($PERCENT_TOTAL_3, 2):"-"), $border, 0, 'R', 0);
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>