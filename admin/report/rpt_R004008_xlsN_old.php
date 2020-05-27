<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
	} // end if

	$cmd = " select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL 
					where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		$ARR_LEVEL_SHORTNAME[] = $data[LEVEL_SHORTNAME];
	}
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	if($search_en_ct_code) $arr_search_condition[] = "(trim(d.CT_CODE)='$search_en_ct_code')";

	$arr_temp = explode("/", $search_date_min);
	$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_min = show_date_format($search_date_min, $DATE_DISPLAY);
	$show_date_min = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_min):$show_date_min);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '$search_date_min')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_STARTDATE), 10) >= '$search_date_min')";

	$arr_temp = explode("/", $search_date_max);
	$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	$show_date_max = show_date_format($search_date_max, $DATE_DISPLAY);
	$show_date_max = (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date_max):$show_date_max);
	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(d.TRN_ENDDATE), 1, 10) <= '$search_date_max')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(d.TRN_ENDDATE), 10) <= '$search_date_max')";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "�ٻẺ����͡��§ҹ : ". ($select_org_structure==1?"�ç���ҧ����ͺ���§ҹ - ":"�ç���ҧ��������� - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||�ӹǹ$PERSON_TYPE[$search_per_type]����֡�� �֡ͺ�� ������ �٧ҹ".($search_en_ct_code?" � �����$search_en_ct_name":"")."||������ѹ��� $show_date_min �֧�ѹ��� $show_date_max";
	$report_code = "R0408";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $ARR_LEVEL_NO,$ARR_LEVEL_SHORTNAME;
		
		$worksheet->set_column(0, 0, 50);
		$worksheet->set_column(1, count($ARR_LEVEL_NO), 8);
		$worksheet->set_column(count($ARR_LEVEL_NO)+1, count($ARR_LEVEL_NO)+1, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		for($i=2; $i<=count($ARR_LEVEL_NO); $i++){
			$worksheet->write($xlsRow, $i, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		} // loop for
		$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
			$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
			$worksheet->write($xlsRow,$i+1 , "$tmp_level_shortname", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}
		$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	function count_scholar($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and d.INS_CODE=e.INS_CODE(+) and d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(	
															PER_PERSONAL a 
															left join $position_table b on $position_join
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
							 where		d.SC_TYPE=1 and trim(a.LEVEL_NO) = '$level_no'
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
							 group by	a.PER_ID ";
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
	
	function count_train($level_no, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type,$select_org_structure;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);
		
		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
												and a.PER_ID=d.PER_ID and trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
							 where		trim(a.LEVEL_NO) = '$level_no'
												$search_condition
							 group by	a.PER_ID ";
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

	if($DPISDB=="odbc"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			distinct d.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID
											$search_condition
						 order by		d.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct d.CT_CODE
						 from			(
												(	
													PER_PERSONAL a 
													left join $position_table b on $position_join
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
											$search_condition
						 order by		d.CT_CODE ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$search_condition = str_replace(" where ", " and ", $search_condition);
	if($DPISDB=="odbc"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			distinct e.CT_CODE
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_SCHOLAR d, PER_INSTITUTE e
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
											and a.PER_ID=d.PER_ID and d.SC_TYPE=1 and d.INS_CODE=e.INS_CODE(+)
												". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct e.CT_CODE
						 from			(
												(
													(	
														PER_PERSONAL a 
														left join $position_table b on $position_join
													) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) inner join PER_SCHOLAR d on (a.PER_ID=d.PER_ID)
											) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
						where			d.SC_TYPE=1
											". str_replace("TRN_ENDDATE", "SC_ENDDATE", str_replace("TRN_STARTDATE", "SC_STARTDATE", $search_condition)) ."
						 order by		e.CT_CODE ";
	} // end if
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$CT_CODE = trim($data[CT_CODE]);
		$arr_country[] = $CT_CODE;
	} // end while

//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	array_unique($arr_country);
	$count_data = count($arr_country);
//	echo "<pre>"; print_r($arr_country); echo "</pre>";

	$data_count = 0;
	foreach($arr_country as $CT_CODE){
//		$CT_CODE = trim($data[CT_CODE]);
		if($CT_CODE == ""){
			$CT_NAME = "[����кػ����]";

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE' or e.CT_CODE is null)";
		}else{
			$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];

			$addition_condition = "(trim(e.CT_CODE) = '$CT_CODE')";
		} // end if

		$arr_content[$data_count][type] = "COUNTRY";
		$arr_content[$data_count][name] = "�����$CT_NAME";
//		$arr_content[$data_count][count_1] = count_person($search_condition, $addition_condition);
//		$GRAND_TOTAL += $arr_content[$data_count][count_1];

		$search_condition = str_replace("d.CT_CODE", "e.CT_CODE", $search_condition);
		$search_condition = str_replace("d.TRN_STARTDATE", "d.SC_STARTDATE", $search_condition);
		$search_condition = str_replace("d.TRN_ENDDATE", "d.SC_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- �֡��";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_scholar(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, $addition_condition);
		} // end for
		$addition_condition = str_replace("e.CT_CODE", "d.CT_CODE", $addition_condition);
		$search_condition = str_replace("e.CT_CODE", "d.CT_CODE", $search_condition);
		$search_condition = str_replace("d.SC_STARTDATE", "d.TRN_STARTDATE", $search_condition);
		$search_condition = str_replace("d.SC_ENDDATE", "d.TRN_ENDDATE", $search_condition);

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- �֡ͺ�� / ������";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (1, 3))"));
		} // end for

		$data_count++;
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", 5) . "- �٧ҹ";
		for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
			$tmp_level_no = $ARR_LEVEL_NO[$i];
			$arr_content[$data_count]["count_".$tmp_level_no] = count_train(str_pad($tmp_level_no, 2, "0", STR_PAD_LEFT), $search_condition, ($addition_condition . " and (d.TRN_TYPE in (2))"));
		} // end for

		$data_count++;
	} // end foreach
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=1; $j<=count($ARR_LEVEL_NO)+1; $j++) $worksheet->write($xlsRow, $j, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			//$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($i=1; $i<=count($ARR_LEVEL_NO)+1; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			//$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTitle", "B", "L", "", 1));
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$COUNT_TOTAL = 0;
			for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
				$tmp_level_no = $ARR_LEVEL_NO[$i];
				${"COUNT_".$tmp_level_no} = $arr_content[$data_count]["count_".$tmp_level_no];
				$COUNT_TOTAL += ${"COUNT_".$tmp_level_no};
			} // end for
			
			if($REPORT_ORDER=="COUNTRY"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$worksheet->write_string($xlsRow, $i+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
				} // end for
				$worksheet->write_string($xlsRow, count($ARR_LEVEL_NO)+1, "", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
			}elseif($REPORT_ORDER=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				for($i=0; $i<count($ARR_LEVEL_NO); $i++){ 
					$tmp_level_no = $ARR_LEVEL_NO[$i];
					$worksheet->write_string($xlsRow, $i+1, (${"COUNT_".$tmp_level_no}?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(${"COUNT_".$tmp_level_no})):number_format(${"COUNT_".$tmp_level_no})):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				} // end for
				$worksheet->write_string($xlsRow, count($ARR_LEVEL_NO)+1, ($COUNT_TOTAL?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($COUNT_TOTAL)):number_format($COUNT_TOTAL)):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			} // end if
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		for($i=1; $i<=count($ARR_LEVEL_NO)+1; $i++) $worksheet->write($xlsRow, $i, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		//$worksheet->write($xlsRow, (count($ARR_LEVEL_NO)+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
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