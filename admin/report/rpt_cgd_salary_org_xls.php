<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$KF_CYCLE = $search_kf_cycle;
	if(trim($search_budget_year)){ 
		if($KF_CYCLE == 1){
			$KF_START_DATE = ($search_budget_year-544) . "-10-01";
			$KF_END_DATE = ($search_budget_year-543) . "-03-31";
			$KF_DATE = ($search_budget_year-543) . "-04-01";
		}elseif($KF_CYCLE == 2){
			$KF_START_DATE = ($search_budget_year-543) . "-04-01";
			$KF_END_DATE = ($search_budget_year-543) . "-09-30";
			$KF_DATE = ($search_budget_year-543) . "-10-01";
		} // end if
	} // end if
	$KF_DATE = show_date_format($KF_DATE,$DATE_DISPLAY);

	if($list_type == "PER_ORG"){
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=c.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=c.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=c.POEMS_ID";
	} elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=c.POT_ID";
	} // end if

	$company_name = "";
	$report_title = "บัญชีรายละเอียดให้$PERSON_TYPE[$search_per_type]ได้รับการเลื่อนเงินเดือนรอบวันที่ ". ($KF_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($KF_DATE):$KF_DATE):"-") ." ||$MINISTRY_NAME $DEPARTMENT_NAME";
	$report_code = "";
	
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

		$worksheet->set_column(0, 0, 14);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 10);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 14);
		$worksheet->set_column(5, 5, 14);
		$worksheet->set_column(6, 6, 14);
		$worksheet->set_column(7, 7, 14);
		$worksheet->set_column(8, 8, 14);
		$worksheet->set_column(9, 9, 14);
		$worksheet->set_column(10, 10, 14);
		$worksheet->set_column(11, 11, 14);
		$worksheet->set_column(12, 12, 14);
		$worksheet->set_column(13, 13, 14);
		$worksheet->set_column(14, 14, 50);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เลขตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "เงินเดือนก่อน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือนสูงสุด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ฐานในการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "ร้อยละของ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "จำนวนเงินที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "เงินตอบแทนฯ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "ผลการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ประชาชน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "เลื่อน 1 ตค 53", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "1 ตค 53", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "แต่ละประเภทฯ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "คำนวณ (บาท)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "การเลื่อน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "การเลื่อนจริง(บาท)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "หลังเลื่อน (บาท)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 13, "ประเมิน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		
		
	if($KF_CYCLE == 1) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-04-01";
	elseif($KF_CYCLE == 2)	$SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-10-01"; /*เดิม*/

	if($DPISDB=="odbc"){  
						$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.PER_TYPE, a.LEVEL_NO, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, SAH_DOCNO, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, 
										SAH_SALARY_UP, SAH_SALARY, SAH_SALARY_EXTRA, SAH_TOTAL_SCORE, SAH_REMARK, SAH_POS_NO_NAME, SAH_POS_NO, b.MOV_CODE,
										c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2
					  from			PER_PERSONAL a, PER_SALARYHIS b, $position_table c
					  where		a.PER_ID=b.PER_ID and $position_join and PER_TYPE = $search_per_type and PER_STATUS = 1 and 
										SAH_EFFECTIVEDATE = '$SAH_EFFECTIVEDATE' and SAH_KF_CYCLE = $KF_CYCLE and b.MOV_CODE not in ('215','21510','21520')
					  $search_condition
					  order by c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, SAH_POS_NO_NAME, CLng(SAH_POS_NO) ";
	
	}elseif($DPISDB=="oci8"){ 
								$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.PER_TYPE, a.LEVEL_NO, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, SAH_DOCNO, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, 
										SAH_SALARY_UP, SAH_SALARY, SAH_SALARY_EXTRA, SAH_TOTAL_SCORE, SAH_REMARK, SAH_POS_NO_NAME, SAH_POS_NO, b.MOV_CODE,
										c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2
					  from			PER_PERSONAL a, PER_SALARYHIS b, $position_table c
					  where		a.PER_ID=b.PER_ID and $position_join and PER_TYPE = $search_per_type and PER_STATUS = 1 and 
										SAH_EFFECTIVEDATE = '$SAH_EFFECTIVEDATE' and SAH_KF_CYCLE = $KF_CYCLE and b.MOV_CODE not in ('215','21510','21520')
					  $search_condition
					  order by c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, SAH_POS_NO_NAME, to_number(replace(SAH_POS_NO,'-','')) ";	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = trim($data2[PN_NAME]);		
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$SAH_DOCNO = trim($data[SAH_DOCNO]);
		$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT]?number_format($data[SAH_SALARY_MIDPOINT],2):"";
		$SAH_PERCENT_UP = $data[SAH_PERCENT_UP]?number_format($data[SAH_PERCENT_UP],4):"";
		$SAH_SALARY_UP = $data[SAH_SALARY_UP]?number_format($data[SAH_SALARY_UP],2):"";
		$SAH_SALARY = $data[SAH_SALARY]?number_format($data[SAH_SALARY],2):"";
		$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA]?number_format($data[SAH_SALARY_EXTRA],2):"";
		$SAH_TOTAL_SCORE = $data[SAH_TOTAL_SCORE]?number_format($data[SAH_TOTAL_SCORE],2):"";
		$PER_SALARY = $data[SAH_SALARY] - $data[SAH_SALARY_UP];
		$PER_SALARY = number_format($PER_SALARY,2);
		$SAH_SALARY_TOTAL = ($data[SAH_SALARY] + $data[SAH_SALARY_EXTRA])?number_format(($data[SAH_SALARY] + $data[SAH_SALARY_EXTRA]),2):"";
		$SAH_REMARK = trim($data[SAH_REMARK]);
		$MOV_CODE = trim($data[MOV_CODE]);
		$POS_NO = trim($data[SAH_POS_NO_NAME]).trim($data[SAH_POS_NO]);

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME =  $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];

		if (!$SAH_TOTAL_SCORE) {
			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $PER_ID and KF_CYCLE = $KF_CYCLE and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SAH_TOTAL_SCORE = $data2[TOTAL_SCORE]?number_format($data2[TOTAL_SCORE],2):"";
		}

		$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE = '$MOV_CODE'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MOV_NAME = trim($data2[MOV_NAME]); 			 

		if($PER_TYPE==1){
			$cmd = " select PL_CODE, PM_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_CODE = trim($data2[PM_CODE]);
			$PL_CODE = trim($data2[PL_CODE]);
			
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);

			$PL_NAME = (trim($PM_CODE)?"$PM_NAME (":"") . $PL_NAME . (trim($PM_CODE)?")":"");

			$cmd = " select LAYER_TYPE from PER_LINE where PL_CODE = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;
	
			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
                        
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        // เดิม if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
				$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
			} else {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
				$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
			}

			if($SALARY_POINT_MID > $OLD_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
			$LAYER_SALARY_MAX = number_format($LAYER_SALARY_MAX,2);
		}elseif($PER_TYPE==2){
		}elseif($PER_TYPE==3){
		} // end if

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][fullname] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][sah_salary] = $SAH_SALARY;
		$arr_content[$data_count][layer_salary_max] = $LAYER_SALARY_MAX;
		$arr_content[$data_count][sah_salary_extra] = $SAH_SALARY_EXTRA;
		$arr_content[$data_count][sah_salary_up] = $SAH_SALARY_UP;
		$arr_content[$data_count][sah_percent_up] = $SAH_PERCENT_UP;
		$arr_content[$data_count][cardno] = $PER_CARDNO;
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][sah_salary_midpoint] = $SAH_SALARY_MIDPOINT;
		$arr_content[$data_count][sah_salary_total] = $SAH_SALARY_TOTAL;
		$arr_content[$data_count][sah_total_score] = $SAH_TOTAL_SCORE;
		$arr_content[$data_count][mov_name] = $MOV_NAME;
		$arr_content[$data_count][sah_remark] = $SAH_REMARK;

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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][fullname];
			$CARDNO = $arr_content[$data_count][cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$SAH_SALARY = $arr_content[$data_count][sah_salary];
			$LAYER_SALARY_MAX = $arr_content[$data_count][layer_salary_max];
			$SAH_SALARY_EXTRA = $arr_content[$data_count][sah_salary_extra];
			$SAH_SALARY_UP = $arr_content[$data_count][sah_salary_up];
			$SAH_PERCENT_UP = $arr_content[$data_count][sah_percent_up];
			$SAH_REMARK = $arr_content[$data_count][sah_remark];
			$MOV_NAME = $arr_content[$data_count][mov_name];
			$SAH_SALARY_MIDPOINT = $arr_content[$data_count][sah_salary_midpoint];
			$SAH_SALARY_TOTAL = $arr_content[$data_count][sah_salary_total];
			$SAH_TOTAL_SCORE = $arr_content[$data_count][sah_total_score];

			if($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($CARDNO):$CARDNO), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 2,(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 5, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 6, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($LAYER_SALARY_MAX):$LAYER_SALARY_MAX), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_SALARY_MIDPOINT):$SAH_SALARY_MIDPOINT), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 9,(($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_PERCENT_UP):$SAH_PERCENT_UP), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_SALARY_UP):$SAH_SALARY_UP), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 11,(($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_SALARY_EXTRA):$SAH_SALARY_EXTRA), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 12, (($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_SALARY):$SAH_SALARY), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 13,  (($NUMBER_DISPLAY==2)?convert2thaidigit($SAH_TOTAL_SCORE):$SAH_TOTAL_SCORE), set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
				$worksheet->write_string($xlsRow, 14, "$SAH_REMARK", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
			} // end if
		} // end for				
		
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีรายละเอียดให้ข้าราชการได้รับการเลื่อนเงินเดือน.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีรายละเอียดให้ข้าราชการได้รับการเลื่อนเงินเดือน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>