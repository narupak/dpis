<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	set_time_limit(3600);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "MINISTRY|DEPARTMENT|ORG";
	
	$arr_order_name = explode("|", $RPTORD_LIST);

	foreach($arr_order_name as $ORDER_NAME){
		switch($ORDER_NAME){
			case "MINISTRY" :
				break;
			case "DEPARTMENT" :
				break;
			case "ORG" :
				break;
		} // switch case
	} // loop foreach
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = "";

	if(trim($search_org_id)){ 
//		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
//		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name";
		$list_type_text .= " ของ$search_org_name $DEPARTMENT_NAME $MINISTRY_NAME";
	}elseif($DEPARTMENT_ID){
//		$arr_search_condition[] = "(c.ORG_ID = $DEPARTMENT_ID)";
//		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		$list_type_text .= " ของ$DEPARTMENT_NAME $MINISTRY_NAME";
	}elseif($MINISTRY_ID){
//		$arr_search_condition[] = "(d.ORG_ID = $MINISTRY_ID)";
//		$list_type_text .= " - $MINISTRY_NAME";
		$list_type_text .= " ของ$MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
//		$PROVINCE_CODE = trim($PROVINCE_CODE);
//		$arr_search_condition[] = "(b.PV_CODE = '$PROVINCE_CODE')";
//		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บทสรุปผู้บริหาร".$list_type_text;
	$report_code = "บทสรุปผู้บริหาร";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $search_min_year, $search_max_year;
		
		$xlsRow++;
		$worksheet->set_row($xlsRow, 20);
		$worksheet->write($xlsRow, 0, "ประเภท/ระดับตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$j = 0;
		for($i=$search_min_year; $i<$search_max_year; $i++){
			$j++;
			$worksheet->write($xlsRow, $j, "$i", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		} // loop for
		$worksheet->write($xlsRow, ($j+1), "$search_max_year", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, ($j+2), "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, ($j+3), "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, ($j+1), $xlsRow, ($j+3));

		$xlsRow++;
		$worksheet->set_row($xlsRow, 40);
		$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$j = 0;
		for($i=$search_min_year; $i<$search_max_year; $i++){
			$j++;
			$worksheet->write($xlsRow, $j, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		} // loop for
		$worksheet->write($xlsRow, ($j+1), "จำนวน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, ($j+2), "อยู่ระหว่างการประเมิน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, ($j+3), "รวม", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // function		
	
	function find_total_position($LV_ID, $SUM_YEAR){
		global $DEPARTMENT_ID, $search_org_id;
		global $db_dpis2;	
		
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
//			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name";
		}elseif($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.ORG_ID_REF = $DEPARTMENT_ID)";
//			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		} // end if
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		
		$cmd = " select 	sum(a.SUM_QTY) as SUM_QTY
						 from 		EXECUTIVE_SUMMARY a, PER_ORG b
						 where	a.SUM_YEAR=$SUM_YEAR and a.LV_ID=$LV_ID and a.ORG_ID=b.ORG_ID
						 				$search_condition ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		return $data2[SUM_QTY];
	} // function 

	function find_evaluate_position($LV_ID, $SUM_YEAR){
		global $DEPARTMENT_ID, $search_org_id;
		global $db_dpis2;	
		
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
//			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name";
		}elseif($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.ORG_ID_REF = $DEPARTMENT_ID)";
//			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		} // end if
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		
		$cmd = " select 	sum(a.SUM_EVALUATION) as SUM_EVALUATION
						 from 		EXECUTIVE_SUMMARY a, PER_ORG b
						 where	a.SUM_YEAR=$SUM_YEAR and a.LV_ID=$LV_ID and a.ORG_ID=b.ORG_ID
						 				$search_condition ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		return $data2[SUM_EVALUATION];
	} // function 

	$cmd = " 	select		distinct LV_ID, LV_NAME, LV_DESCRIPTION
						from		PER_NEW_LEVEL
						where		LV_ID >= 2 and LV_ID <= 14
						order by	LV_ID desc ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	
	while($data = $db_dpis->get_array()){
/*
		foreach($arr_order_name as $ORDER_NAME){
			switch($ORDER_NAME){
				case "MINISTRY" :
					$MINISTRY_NAME = $data[MINISTRY_NAME];
					if(!$MINISTRY_NAME) $MINISTRY_NAME = "-";
					break;
				case "DEPARTMENT" :
					$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
					if(!$DEPARTMENT_NAME) $DEPARTMENT_NAME = "-";
					break;
				case "ORG" :
					$ORG_NAME = $data[ORG_NAME];
					if(!$ORG_NAME) $ORG_NAME = "-";
					break;
			} // switch case
		} // loop foreach
*/		
		$arr_content[$data_count][LV_NAME] = $data[LV_DESCRIPTION];
		for($i=$search_min_year; $i<$search_max_year; $i++){
			$arr_content[$data_count][$i] = find_total_position($data[LV_ID], $i);
		} // loop for
		$arr_content[$data_count][$search_max_year."_TOTAL"] = find_total_position($data[LV_ID], $search_max_year);
		$arr_content[$data_count][$search_max_year."_EVALUATE"] = find_evaluate_position($data[LV_ID], $search_max_year);
		$arr_content[$data_count][$search_max_year."_CURRENT"] = $arr_content[$data_count][$search_max_year."_TOTAL"] - $arr_content[$data_count][$search_max_year."_EVALUATE"];
		
		$data_count++;
	} // loop while
		
	$count_data = count($arr_content);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$page_count = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			if(($data_count % 65530) == 0){
				$page_count++;

				$worksheet = &$workbook->addworksheet("$report_code".(($page_count > 1)?" ($page_count)":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
			
				$worksheet->set_column(0, 0, 50);
				$j = 0;
				for($i=$search_min_year; $i<$search_max_year; $i++){
					$j++;
					$worksheet->set_column($j, $j, 10);
				} // loop for
				$worksheet->set_column(($j + 1), ($j + 1), 10);
				$worksheet->set_column(($j + 2), ($j + 2), 10);
				$worksheet->set_column(($j + 3), ($j + 3), 10);

				$xlsRow = -1;

				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
					$worksheet->merge_cells($xlsRow, 0, $xlsRow, ($search_max_year - $search_min_year) + 3);
				} // end if
		
				if($company_name){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $company_name, set_format_new("xlsFmtSubTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
					$worksheet->merge_cells($xlsRow, 0, $xlsRow, ($search_max_year - $search_min_year) + 3);
				} // end if

				print_header();	
			} // end if
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $arr_content[$data_count][LV_NAME], set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
			$j = 0;
			for($i=$search_min_year; $i<$search_max_year; $i++){
				$j++;
				$worksheet->write($xlsRow, $j, ($arr_content[$data_count][$i]?number_format($arr_content[$data_count][$i]):""), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->write($xlsRow, ($j+1), ($arr_content[$data_count][$search_max_year."_CURRENT"]?number_format($arr_content[$data_count][$search_max_year."_CURRENT"]):""), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, ($j+2), ($arr_content[$data_count][$search_max_year."_EVALUATE"]?number_format($arr_content[$data_count][$search_max_year."_EVALUATE"]):""), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
			$worksheet->write($xlsRow, ($j+3), number_format($arr_content[$data_count][$search_max_year."_TOTAL"]), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
		} // loop for
	}else{
		$worksheet = &$workbook->addworksheet("$report_code");
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);
	} // end if

	$workbook->close();

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