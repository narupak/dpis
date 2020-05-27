<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_condition2.php");
	
	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาสายของ$PERSON_TYPE[$search_per_type] จำแนกตาม$ORG_TITLE";
	$report_code = "R0710";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$heading_width[0] = "147";
	$heading_width[1] = "10";
	$heading_width[2] = "15";
	$heading_width[3] = "15";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 70);
		$worksheet->set_column(1, 11, 5);
		$worksheet->set_column(12, 12, 8);
		$worksheet->set_column(13, 13, 8);

		$worksheet->write($xlsRow, 0, "$heading_name", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=1; $i<=11; $i++) $worksheet->write($xlsRow, $i, "$i", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "คน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "ร้อยละ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function count_person($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $arr_rpt_order, $search_per_type, $select_org_structure;
		
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$level_no = str_pad($level_no, 2, "0", STR_PAD_LEFT);

		if($search_per_type==1){
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				} // end if
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
									 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
									 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}
			} // end if
		}elseif($search_per_type==2){
			if($DPISDB=="odbc"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				} // end if
			}elseif($DPISDB=="oci8"){				
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
									 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
														and trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				} // end if
			}elseif($DPISDB=="mysql"){
				if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															(	
																PER_PERSONAL a 
																left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
									 where		trim(a.LEVEL_NO) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}else{
					$cmd = " select			count(a.PER_ID) as count_person
									 from			(
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
									 where		trim(a.LEVEL_NO)) = '$level_no'
														$search_condition
									 group by	a.PER_ID ";
				}
			} // end if
		} // end if

		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $search_per_type, $select_org_structure;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE;
				
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
				case "ORG_2" :
					if($select_org_structure==0){
						if($ORG_ID_2) $arr_addition_condition[] = "(b.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(b.ORG_ID_2 = 0 or b.ORG_ID_2 is null)";
					}else if($select_org_structure==1){
						if($ORG_ID_2) $arr_addition_condition[] = "(a.ORG_ID_2 = $ORG_ID_2)";
						else $arr_addition_condition[] = "(a.ORG_ID_2 = 0 or a.ORG_ID_2 is null)";
					}
				break;
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE) $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE')";
						else $arr_addition_condition[] = "(trim(b.PL_CODE) = '$PL_CODE' or b.PL_CODE is null)";
					}elseif($search_per_type==2){
						if($PN_CODE) $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE')";
						else $arr_addition_condition[] = "(trim(b.PN_CODE) = '$PN_CODE' or b.PN_CODE is null)";
					} // end if
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
				case "PROVINCE" :
					if($PV_CODE) $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE')";
					else $arr_addition_condition[] = "(trim(c.PV_CODE) = '$PV_CODE' or c.PV_CODE is null)";
				break;
				case "EDUCLEVEL" :
					if($EN_CODE) $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE')";
					else $arr_addition_condition[] = "(trim(d.EN_CODE) = '$EN_CODE' or d.EN_CODE is null)";
				break;
				case "EDUCMAJOR" :
					if($EM_CODE) $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE')";
					else $arr_addition_condition[] = "(trim(d.EM_CODE) = '$EM_CODE' or d.EM_CODE is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID, $ORG_ID_1, $ORG_ID_2, $PL_CODE, $PN_CODE, $PER_GENDER, $PV_CODE, $EM_CODE, $EN_CODE;
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
				case "SEX" :
					$PER_GENDER = -1;
				break;
				case "PROVINCE" :
					$PV_CODE = -1;
					$PN_CODE = -1;
				break;
				case "EDUCLEVEL" :
					$EN_CODE = -1;
				break;
				case "EDUCMAJOR" :
					$EM_CODE = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	if($search_per_type==1){
		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by ";
			} // end if
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_EDUCATE d
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c
								 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 order by		$order_by ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by ";
			} // end if
		} // end if
	}elseif($search_per_type==2){
		if($DPISDB=="odbc"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by ";
			} // end if
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c, PER_EDUCATE d
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PER_ID=d.PER_ID(+) and d.EDU_TYPE like '%2%'
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			PER_PERSONAL a, PER_POS_EMP b, PER_ORG c
								 where		a.POEM_ID=b.POEM_ID(+) and b.ORG_ID=c.ORG_ID(+)
													$search_condition
								 order by		$order_by ";
			} // end if
		}elseif($DPISDB=="mysql"){
			if(in_array("EDUCLEVEL", $arr_rpt_order) || in_array("EDUCMAJOR", $arr_rpt_order)){
				$cmd = " select			distinct $select_list
								 from			(
														(	
															PER_PERSONAL a 
															left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (a.PER_ID=d.PER_ID and d.EDU_TYPE like '%2%')
													$search_condition
								 order by		$order_by ";
			}else{
				$cmd = " select			distinct $select_list
								 from			(
														PER_PERSONAL a 
														left join PER_POS_EMP b on (a.POEM_ID=b.POEM_ID) 
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													$search_condition
								 order by		$order_by ";
			} // end if
		} // end if
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	unset($LEVEL_TOTAL);
	$GRAND_TOTAL = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
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

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for

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

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_1";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_1;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "ORG_2" :
					if($ORG_ID_2 != trim($data[ORG_ID_2])){
						$ORG_ID_2 = trim($data[ORG_ID_2]);
						if($ORG_ID_2 != ""){
							if($select_org_structure==0) $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME_2 = $data2[ORG_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "ORG_2";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME_2;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "LINE" :
					if($search_per_type==1){
						if($PL_CODE != trim($data[PL_CODE])){
							$PL_CODE = trim($data[PL_CODE]);
							if($PL_CODE != ""){
								$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;
							for($i=1; $i<=11; $i++){ 
								$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
								if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
							} // end for
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					}elseif($search_per_type==2){
						if($PN_CODE != trim($data[PN_CODE])){
							$PN_CODE = trim($data[PN_CODE]);
							if($PN_CODE != ""){
								$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
								$db_dpis2->send_cmd($cmd);
//								$db_dpis2->show_error();
								$data2 = $db_dpis2->get_array();
								$PN_NAME = $data2[PN_NAME];
							} // end if
	
							$addition_condition = generate_condition($rpt_order_index);
				
							$arr_content[$data_count][type] = "LINE";
							$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PN_NAME;
							for($i=1; $i<=11; $i++){ 
								$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
								if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
							} // end for
				
							if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
							$data_count++;
						} // end if
					} // end if
				break;
		
				case "SEX" :
					if($PER_GENDER != trim($data[PER_GENDER])){
						$PER_GENDER = trim($data[PER_GENDER]) + 0;

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "SEX";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . (($PER_GENDER==1)?"ชาย":(($PER_GENDER==2)?"หญิง":""));
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
		
				case "PROVINCE" :
					if($PV_CODE != trim($data[PV_CODE])){
						$PV_CODE = trim($data[PV_CODE]);
						if($PV_CODE != ""){
							$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PV_NAME = $data2[PV_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "PROVINCE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PV_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCLEVEL" :
					if($EN_CODE != trim($data[EN_CODE])){
						$EN_CODE = trim($data[EN_CODE]);
						if($EN_CODE != ""){
							$cmd = " select EN_SHORTNAME, EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
//							$EN_NAME = trim($data2[EN_SHORTNAME])?$data2[EN_SHORTNAME]:$data2[EN_NAME];
							$EN_NAME = $data2[EN_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCLEVEL";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EN_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "EDUCMAJOR" :
					if($EM_CODE != trim($data[EM_CODE])){
						$EM_CODE = trim($data[EM_CODE]);
						if($EM_CODE != ""){
							$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$EM_NAME = $data2[EM_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "EDUCMAJOR";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $EM_NAME;
						for($i=1; $i<=11; $i++){ 
							$arr_content[$data_count]["level_".$i] = count_person($i, $search_condition, $addition_condition);
							if($rpt_order_index == 0) $LEVEL_TOTAL[$i] += $arr_content[$data_count]["level_".$i];
						} // end for
			
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	if(array_search("EDUCLEVEL", $arr_rpt_order) !== false  && array_search("EDUCLEVEL", $arr_rpt_order) == 0)
		for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, $search_condition, "");
	if(array_search("EDUCMAJOR", $arr_rpt_order) !== false  && array_search("EDUCMAJOR", $arr_rpt_order) == 0)
		for($i=1; $i<=11; $i++) $LEVEL_TOTAL[$i] = count_person($i, $search_condition, "");

	$GRAND_TOTAL = array_sum($LEVEL_TOTAL);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			unset($COUNT_LEVEL);
			for($i=1; $i<=11; $i++) $COUNT_LEVEL[$i] = $arr_content[$data_count]["level_".$i];
			$COUNT_TOTAL = array_sum($COUNT_LEVEL);
			
			if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

			if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=1; $i<=11; $i++) 
					$worksheet->write($xlsRow, $i, ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 12, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($i=1; $i<=11; $i++) 
					$worksheet->write($xlsRow, $i, ($COUNT_LEVEL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_LEVEL[$i])):number_format($COUNT_LEVEL[$i])):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 12, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write($xlsRow, 13, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
		} // end for
				
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		$xlsRow++;
		$worksheet->write_string($xlsRow, 0, "รวม", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
		for($i=1; $i<=11; $i++) 
			$worksheet->write($xlsRow, $i, ($LEVEL_TOTAL[$i]?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($LEVEL_TOTAL[$i])):number_format($LEVEL_TOTAL[$i])):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 12, ($GRAND_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($GRAND_TOTAL)):number_format($GRAND_TOTAL)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
		$worksheet->write($xlsRow, 13, ($PERCENT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PERCENT_TOTAL,2)):number_format($PERCENT_TOTAL, 2)):"-"), set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>