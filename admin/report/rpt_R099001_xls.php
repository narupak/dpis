<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	$search_condition = "";

	$arr_search_condition[] = "(c.KPI_YEAR = '".$search_budget_year."')";
	
	$list_type_text = $ALL_REPORT_TITLE;
	
	if($list_type == "PER_ORG"){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(c.ORG_NAME = '$search_org_name')";
			$list_type_text .= "$search_org_name";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(c.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$search_condition = str_replace(" where ", " and ", $search_condition);
	$cmd = " select SUM(KPI_EVALUATE) as SUM_KPI_EVALUATE, COUNT(KPI_ID) as COUNT_KPI_CHILD from PER_KPI c where trim(KPI_YEAR)='$search_budget_year' and (KPI_ID_REF IS NULL or KPI_ID_REF='') $search_condition";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SUM_KPI_EVALUATE = $data[SUM_KPI_EVALUATE] + 0;
	$COUNT_KPI_CHILD = $data[COUNT_KPI_CHILD] + 0;
	
	$APPROX_KPI_EVALUATE = "";
	$REAL_KPI_EVALUATE = "";							
	if($SUM_KPI_EVALUATE > 0 && $COUNT_KPI_CHILD > 0){ 
		$APPROX_KPI_EVALUATE = floor($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD);
		$REAL_KPI_EVALUATE = number_format(round(($SUM_KPI_EVALUATE / $COUNT_KPI_CHILD), 3), 3);
	} // end if

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if ($REAL_KPI_EVALUATE == "") 
	{	$report_title = "$DEPARTMENT_NAME||รายงานตัวชี้วัดประจำปีงบประมาณ $search_budget_year";
		$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	}
		else 
		{$report_title = "$DEPARTMENT_NAME||รายงานตัวชี้วัดประจำปีงบประมาณ $search_budget_year (คะแนน $REAL_KPI_EVALUATE)";
		$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
		}
	$report_code = "R9901";
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_code):$report_code);

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
		
		$worksheet->set_column(0, 0, 40);
		$worksheet->set_column(1, 1, 50);
		$worksheet->set_column(2, 2, 12);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 35);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ประเด็นด้านยุทธศาสตร์/\nภารกิจหลัก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ตัวชี้วัด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "น้ำหนัก\n(ร้อยละ)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "คะแนน\nที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ผลงานจริง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ผู้รับผิดชอบ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	$cmd = " select c.KPI_ID, c.KPI_NAME, c.KPI_YEAR, c.KPI_WEIGHT, c.KPI_MEASURE, c.KPI_EVALUATE, c.ORG_NAME, c.KPI_RESULT, c.KPI_ID_REF, a.pfr_name As 
									pfr_parent, b.pfr_name, e.pn_name, d.per_name, d.per_surname 
					from per_performance_review a, per_performance_review b, per_kpi c, per_personal d,per_prename e 
					where b.pfr_id_ref = a.pfr_id(+) and b.pfr_id = c.pfr_id and c.kpi_per_id=d.per_id and d.pn_code=e.pn_code $search_condition
					order by pfr_parent,b.pfr_name,c.kpi_name ";
	$db_dpis2->send_cmd($cmd);
	//echo $cmd;
	$data_count=0;

	while($data = $db_dpis2->get_array()){
		$KPI_ID=trim($data[KPI_ID]);
		$PFR_NAME_L1 = trim($data[PFR_PARENT]);
		$PFR_NAME_L2 = trim($data[PFR_NAME]);
		$KPI_NAME = trim($data[KPI_NAME]);
		$KPI_WEIGHT = trim($data[KPI_WEIGHT]);
		$KPI_EVALUATE = (trim($data[KPI_EVALUATE])?$data[KPI_EVALUATE]:"-");
		$KPI_RESULT = (trim($data[KPI_RESULT])?$data[KPI_RESULT]:"-");
		$ORG_NAME = trim($data[ORG_NAME]);
		$KPI_ID_REF=trim($data[KPI_ID_REF]);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][KPI_ID] = $KPI_ID;
		$arr_content[$data_count][PFR_NAME_L1] = $PFR_NAME_L1;
		$arr_content[$data_count][PFR_NAME_L2] = $PFR_NAME_L2;
		$arr_content[$data_count][KPI_NAME] = $KPI_NAME;
		$arr_content[$data_count][KPI_WEIGHT] = $KPI_WEIGHT;
		$arr_content[$data_count][KPI_EVALUATE] = $KPI_EVALUATE;
		$arr_content[$data_count][KPI_RESULT] = $KPI_RESULT;
		$arr_content[$data_count][ORG_NAME] = $ORG_NAME;
		$arr_content[$data_count][KPI_ID_REF] = $KPI_ID_REF;
		$data_count++;

	} // end while
		
	if($data_count){
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
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		$PFR_LEVEL1="";
		$PFR_LEVEL2="";
		$KPI_LEVE1="";
		$KPI_LEVE2="";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$REF_1 = $arr_content[$data_count][KPI_ID];
			$REF_2 = $arr_content[$data_count][KPI_ID_REF];
			$NAME_1 = $arr_content[$data_count][PFR_NAME_L1];
			$NAME_1 = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_1):$NAME_1);
			$NAME_2 = $arr_content[$data_count][PFR_NAME_L2];
			$NAME_2  = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_2 ):$NAME_2 );
			$NAME_3 = $arr_content[$data_count][KPI_NAME];
			$NAME_3  = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_3 ):$NAME_3 );
			$NAME_4 = $arr_content[$data_count][KPI_WEIGHT];
			$NAME_4  = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_4 ):$NAME_4 );
			$NAME_5 = $arr_content[$data_count][KPI_EVALUATE];
			$NAME_5  = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_5 ):$NAME_5 );
			$NAME_6 = $arr_content[$data_count][KPI_RESULT];
			$NAME_6  = (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME_6 ):$NAME_6 );
			$NAME_7 = $arr_content[$data_count][ORG_NAME];
	    
			
			$border = "";
			$xlsRow++;	
			if ($PFR_LEVEL1!=$NAME_1){
				$PFR_LEVEL1=$NAME_1;
				$worksheet->write($xlsRow, 0, $NAME_1, set_format("xlsFmtTableDetail", "B", "L", "L",0));
				$worksheet->write($xlsRow, 1, " ", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write($xlsRow, 2, " ", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write($xlsRow, 3, " ", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write($xlsRow, 4, " ", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write($xlsRow, 5, " ", set_format("xlsFmtTableDetail", "B", "L", "R", 0));
				
				$xlsRow++;
				$PFR_LEVEL2=$NAME_2;
				$worksheet->write_string($xlsRow, 0, "- $NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				//$worksheet->write_string($xlsRow, 1, $NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				if ($REF_2==""){
					$KPI_LEVEL1=$REF_1;
					$worksheet->write_string($xlsRow, 1, $NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, $NAME_4, set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				}else {
					if ($KPI_LEVEL1==$REF_2) {
						$KPI_LEVEL2=$REF_1;
						$worksheet->write_string($xlsRow, 1, "  ".$NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, $NAME_4, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}else{
						$worksheet->write_string($xlsRow, 1, "    ".$NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, "($NAME_4)", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}
				}
				$worksheet->write_string($xlsRow, 3, $NAME_5, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, $NAME_6, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, $NAME_7, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
				$KPI_LEVEL1=$REF_1;
			}else{
				if ($PFR_LEVEL2==$NAME_2){
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				}else{
					$PFR_LEVEL2=$NAME_2;
					$worksheet->write_string($xlsRow, 0, "- $NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				}

				if ($REF_2==""){
					$KPI_LEVEL1=$REF_1;
					$worksheet->write_string($xlsRow, 1, $NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, $NAME_4, set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
				}else {
					if ($KPI_LEVEL1==$REF_2) {
						$KPI_LEVEL2=$REF_1;
						$worksheet->write_string($xlsRow, 1, "     ".$NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, $NAME_4, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}else{
						$worksheet->write_string($xlsRow, 1, "          ".$NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, "($NAME_4)", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					}
				}

				$worksheet->write_string($xlsRow, 3, $NAME_5, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, $NAME_6, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, $NAME_7, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}
		} // end for		
				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		//$worksheet->write($xlsRow, 0, $cmd, set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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