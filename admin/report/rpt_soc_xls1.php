<?
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";		
		$pos_no = "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";		
		$pos_no = "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";				
		$pos_no = "b.POEMS_NO";
	}elseif($search_per_type==4){ 
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
		$pos_no = "b.POT_NO";
	}

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POSNO"); 

	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POSNO" :

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(" . $pos_no . "), 0 , CLng(" . $pos_no . "))";
				if($DPISDB=="oci8") $order_by .= "to_number(replace(" . $pos_no . ",'-',''))";
				if($DPISDB=="mysql") $order_by .= $pos_no . "+0";

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = $pos_no;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type and a.PER_STATUS=1)";
	$list_type_text = $ALL_REPORT_TITLE;

	$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$report_code = "History Data";

	function print_header1(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 50);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ID_CARD", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "FNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "LNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "INS_CODE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "INS_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "POS_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "DEP_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition1($current_index){
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
	
	function initialize_parameter1($current_index){
		global $arr_rpt_order;
		global $POS_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	// ===== select data =====
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF 
						 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							$search_condition
				 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF
						 from			PER_PERSONAL a, $position_table b, PER_ORG c
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) 
							$search_condition
				order by		$order_by   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF 
						 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							$search_condition
				 order by		$order_by ";
	} // end if
	$db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	$data_count = $data_row = 0;
	initialize_parameter1(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
		$data_row++;
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);	
								
		$PER_ID = $data[PER_ID];
		$PER_CARDNO = $data[PER_CARDNO];
		$TITLE_CODE = $data[TITLE_CODE];
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
				
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TITLE_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];

		// === หาเครื่องราชย์
		$DC_NAME = $DEH_DATE = "";
		$cmd = " select DC_SHORTNAME, DEH_DATE, DEH_POSITION, DEH_ORG
								from   PER_DECORATEHIS a, PER_DECORATION b
								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								order by DEH_DATE desc ";
//								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_ORDER <= 18 
		$count_data = $db_dpis1->send_cmd($cmd);
		while($data1 = $db_dpis1->get_array()){
			$DC_NAME = trim($data1[DC_SHORTNAME]);
			$DEH_DATE = trim($data1[DEH_DATE]);
			$PL_NAME = trim($data1[DEH_POSITION]);
			$ORG_NAME = trim($data1[DEH_ORG]);
			$POH_EFFECTIVEDATE = $DEH_DATE;
			if ($DEH_DATE) {	
				$DEH_DATE = show_date_format(substr($DEH_DATE, 0, 10),1);
			}

			if (!$PL_NAME || !$ORG_NAME) {
				$PL_CODE = $PL_NAME = $ORG_NAME = "";
				$cmd = " select PL_CODE, POH_ORG2
										from   PER_POSITIONHIS
										where PER_ID=$PER_ID and POH_EFFECTIVEDATE <= '$POH_EFFECTIVEDATE'
										order by POH_EFFECTIVEDATE desc ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_CODE = trim($data2[PL_CODE]);
				$ORG_NAME = trim($data2[POH_ORG2]);

				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = $data2[PL_NAME];
			}
						
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $data_row;
			$arr_content[$data_count][per_cardno] = $PER_CARDNO;
			$arr_content[$data_count][per_name] = $PER_NAME;
			$arr_content[$data_count][per_surname] = $PER_SURNAME;
			$arr_content[$data_count][dc_name] = $DC_NAME;
			$arr_content[$data_count][deh_date] = $DEH_DATE;
			$arr_content[$data_count][pl_name] = $PL_NAME;
			$arr_content[$data_count][org_name] = $ORG_NAME;

			$data_count++;														
		} // end while
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	// ===== condition $count_data  from "select data" =====
	if($data_count){
		$xlsRow = 0;
		$count_org_ref = 0;

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_SURNAME = $arr_content[$data_count][per_surname];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$DEH_DATE = $arr_content[$data_count][deh_date];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$ORG_NAME = $arr_content[$data_count][org_name]; 	
			
			if($REPORT_ORDER == "ORG_REF"){
				if($data_count > 0) $count_org_ref++;

				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				
				//====================== SET FORMAT ======================//
				require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
				//====================== SET FORMAT ======================//

				$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
				for($i=0; $i<count($arr_title); $i++){
					$xlsRow = $i;
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				} // end if
		
				if($company_name){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if
		
				print_header1();
			}else{
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$DEH_DATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			} // end if
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"soc.xls\"");
	header("Content-Disposition: inline; filename=\"soc.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>