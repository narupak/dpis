<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(peh.EX_CODE in('012', '013', '014'))";
	//ค้นหา ณ วันที
	if(trim($search_end_date)){
		$arr_temp = explode("/", $search_end_date);
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[2] + 0);	//ณ วันที่
	}
	if(trim($search_date)){	}
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$select_position = "c.POS_ID, c.POS_NO_NAME, c.POS_NO, c.PT_CODE, c.PL_CODE, c.PM_CODE";
		$column_count=8;
	}else if($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$select_position = "c.POEM_ID, c.POEM_NO_NAME, c.POEM_NO, c.PN_CODE";
		$column_count=8;
	}else if($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$select_position = "c.POEMS_ID, c.POEMS_NO_NAME, c.POEMS_NO, c.EP_CODE";
		$column_count=8;
	}else if($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$select_position = "c.POT_ID, c.POT_NO_NAME, c.POT_NO, c.TP_CODE";
		$column_count=8;
	} // end if

	//เซตค่า
	$COM_DATE = $show_date;

	$company_name = "";
	$report_title = "บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว$PERSON_TYPE[$search_per_type] ||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ ". (($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NO):$COM_NO) ."ลงวันที่ ".  (($NUMBER_DISPLAY==2)?convert2thaidigit($COM_DATE):$COM_DATE);
	$report_code = "R6017";
	
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
		global $worksheet, $xlsRow, $COM_LEVEL_SALP;
		global $search_per_type;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 50);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 40);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		if($search_per_type == 1){
			$worksheet->write($xlsRow, 3, "ตำแหน่งและส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		}else{
			$worksheet->write($xlsRow, 3, "ตำแหน่ง/สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		}
		$worksheet->write($xlsRow, 4, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 5, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 6, "ค่าตอบแทน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 7, "ให้ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 8, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		//แถวที่ 2
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		if($search_per_type == 1){
			$worksheet->write($xlsRow, 3, "สังกัด/ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}else{
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}
		$worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "พิเศษชั่วคราว", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										b.PER_SALARY,b.PER_MGTSALARY, c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID,$select_position 
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						where 	b.PER_ID=peh.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										 b.PER_SALARY,b.PER_MGTSALARY,c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						where 	b.PER_ID=peh.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL 
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";	
	}elseif($DPISDB=="mysql"){
		$cmd = "select 		EXH_ID, EXH_EFFECTIVEDATE, pet.EX_NAME, EXH_AMT, EXH_ENDDATE,
										b.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										b.PER_SALARY,b.PER_MGTSALARY,c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID,$select_position 
						from 		PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_EXTRAHIS peh, PER_EXTRATYPE pet
						 where 	b.PER_ID=peh.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID and	peh.EX_CODE=pet.EX_CODE and EXH_ENDDATE is NULL
										$search_condition
						order by 	$select_position, d.ORG_SEQ_NO, e.ORG_SEQ_NO, EXH_EFFECTIVEDATE desc ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PREN_CODE = trim($data[PREN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PREN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];	
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], $DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$PER_SALARY = $data[PER_SALARY];
		$PER_MGTSALARY = $data[PER_MGTSALARY];	//เงินประจำตำแหน่ง

		$EXH_EFFECTIVEDATE = show_date_format($data[EXH_EFFECTIVEDATE], $DATE_DISPLAY);
		$EX_NAME = trim($data[EX_NAME]);
		$EXH_AMT = trim($data[EXH_AMT]);
		$EXH_ENDDATE = show_date_format($data[EXH_ENDDATE], $DATE_DISPLAY);

		//นำเงินเพิ่มค่าครองชีพ (เช่น เงินช่วยเหลือค่าครองชีพ, เงินตอบแทนพิเศษรายเดือน) มารวมกับเงินประจำตำแหน่ง (ถ้ามี)
		$MGTS_NAME = "";
		if($PER_MGTSALARY > 0){
			$EXH_AMT = ($EXH_AMT+$PER_MGTSALARY);
			$MGTS_NAME = ",เงินประจำตำแหน่ง";
		}
		//ให้ได้รับ = รวมเงินเดือนปัจจุบัน กับ เงินที่ได้รับ	
		$SUM_RCV = ($PER_SALARY + $EXH_AMT);

		//หาข้อมูลตำแหน่ง
		if($PER_TYPE==1){		
			$POS_ID = $data[POS_ID];
			$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
		
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];		
			
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];		
			
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];		
	
			if ($RPT_N)
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
			else
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
		}elseif($PER_TYPE==2){		
			$POEM_ID = $data[POEM_ID];
			$POS_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);
			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select	 PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		}elseif($PER_TYPE==3){	
			$POEMS_ID = $data[POEMS_ID];
			$POS_NO = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]);
			
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select	 EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		} // end if

		if($CMD_ORG3 != trim($data[ORG_ID])){
			$CMD_ORG3 = trim($data[ORG_ID]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CMD_ORG3 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
		
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$data_count++;
		} // end if
		
		/***
		if($CMD_ORG4 != trim($data[ORG_ID_1])){
			$CMD_ORG4 = trim($data[ORG_ID_1]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CMD_ORG4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
		
			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$data_count++;
		} // end if
		***/

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO, $CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_pos_no] = $POS_NO;
		$arr_content[$data_count][cmd_level] = "ท." . level_no_format($LEVEL_NO);
		$arr_content[$data_count][cmd_salary] = ($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY)):"-");
		$arr_content[$data_count][cmd_extra] = 	($EXH_AMT?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXH_AMT)):number_format($EXH_AMT)):"-");
		$arr_content[$data_count][cmd_sumrcv] = ($SUM_RCV?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($SUM_RCV)):number_format($SUM_RCV)):"-");
		$arr_content[$data_count][cmd_note1] = ""; //$EX_NAME.$MGTS_NAME;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CARDNO = $arr_content[$data_count][cardno];
			$NAME = $arr_content[$data_count][name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_EXTRA = $arr_content[$data_count][cmd_extra];
			$CMD_SUMRCV = $arr_content[$data_count][cmd_sumrcv];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if($CONTENT_TYPE=="ORG"){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
				}else{
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				} // end if
			}elseif($CONTENT_TYPE=="CONTENT"){
				if($CMD_NOTE2){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "หมายเหตุ : $CMD_NOTE2", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 1,  (($NUMBER_DISPLAY==2)?convert2thaidigit($CARDNO):$CARDNO), set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 3, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_POS_NO ):$CMD_POS_NO ), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 5, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
					$worksheet->write($xlsRow, 6, "$CMD_EXTRA", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
					$worksheet->write($xlsRow, 7, "$CMD_SUMRCV", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
					$worksheet->write($xlsRow, 8, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				} // end if
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 1, "หมายเหตุ : $COM_NOTE", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 1));
		} // end if
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว$type_name.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีรายละเอียดเงินเพิ่มการครองชีพชั่วคราว$type_name.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>