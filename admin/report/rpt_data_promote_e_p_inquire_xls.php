<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$MINISTRY_ID = $ORG_ID_1;
	$MINISTRY_NAME = $ORG_NAME_1;
	$DEPARTMENT_ID = $ORG_ID_2;
	$DEPARTMENT_NAME = $ORG_NAME_2;
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);

	$company_name = "";
	//if(trim($report_title)){ $report_title = $report_title;	}
	$report_title = "$PERSON_TYPE[$PER_TYPE]ที่มีคุณสมบัติได้เลื่อนตำแหน่ง";
	$report_code = "";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $xlsRow, $worksheet;
		global $PER_TYPE, $SHOW_PRO_DATE, $POS_POEM_NO, $POS_POEM_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME;

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15);
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ประเภทบุคลากร : ".$PERSON_TYPE[$PER_TYPE], set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, "วันที่ประมวลผล : ".($SHOW_PRO_DATE?$SHOW_PRO_DATE:""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$MINISTRY_TITLE : ".($MINISTRY_NAME?$MINISTRY_NAME:""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, "$DEPARTMENT_TITLE : ".($DEPARTMENT_NAME?$DEPARTMENT_NAME:""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง : ".($POS_POEM_NO?$POS_POEM_NO:""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง : ".($POS_POEM_NAME?$POS_POEM_NAME:""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "อัตราเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "วันเข้าสู่ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ระยะเวลาที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "อายุราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 7, "ดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 8, "(ปี/เดือน/วัน)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "(ปี/เดือน/วัน)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // end if
	
	if ($PER_TYPE == 1) {
		$search_main_from = "PER_PROMOTE_P";
		$search_pos = "POS_ID"; 
		$search_from = ", PER_POSITION d";		
		$search_field = ", d.POS_CHANGE_DATE, d.PM_CODE, d.PL_CODE, d.LEVEL_NO, d.PT_CODE, d.POS_NO";
	} elseif ($PER_TYPE == 2)	{
		$search_main_from = "PER_PROMOTE_E";
		$search_pos = "POEM_ID"; 
		$search_from = ", PER_POS_EMP d";				
		$search_field = ", d.EP_CODE,  d.PN_CODE, d.POEM_NO";
	} elseif ($PER_TYPE == 3) {
		$search_main_from = "";	
		$search_pos = "POEMS_ID"; 
		$search_from = ", PER_POS_EMPSER d";
		$search_field = ", d.POEMS_NO";
	}elseif ($PER_TYPE == 4) {
		$search_main_from = "";	
		$search_pos = "POT_ID"; 
		$search_from = ", PER_POS_TEMP d";
		$search_field = ",d.TP_CODE,  d.POT_NO";
	}

	if (trim($PRO_DATE)) {	
		$PRO_DATE =  save_date($PRO_DATE);
		$arr_search_condition[] = "(PRO_DATE like '$PRO_DATE%')";
		$SHOW_PRO_DATE = show_date_format($SHOW_PRO_DATE,$DATE_DISPLAY);
	} 
	if (trim($POS_POEM_ID)) {
		$arr_search_condition[] = "(a.$search_pos = $POS_POEM_ID)";	
	}	
	if (trim($search_from))		// if table ที่เก็บชื่อตำแหน่ง
		$arr_search_condition[] = "(b.$search_pos = d.$search_pos)";

	if($DEPARTMENT_ID){ 
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(a.DEPARTMENT_ID=$DEPARTMENT_ID)";
		elseif($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(b.DEPARTMENT_ID=$DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data=$db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	} // end if

	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = " select		a.$search_pos $search_field as POS_NO, 
										a.PER_ID, c.PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE
						 from 		$search_main_from a, PER_PERSONAL b, PER_PRENAME c
										$search_from 
						 where 	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE
										$search_condition
						 order by PRO_DATE desc, a.$search_pos, a.PER_ID
					  ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select 		a.$search_pos $search_field as POS_NO, 
								  			a.PER_ID, PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE
						  from 			$search_main_from a, PER_PERSONAL b, PER_PRENAME c
								  			$search_from 
						  where 		a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE(+)
								  			$search_condition
						 order by 	PRO_DATE desc, a.$search_pos, a.PER_ID 
					  ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.$search_pos $search_field as POS_NO, 
										a.PER_ID, c.PN_NAME, PER_NAME, PER_SURNAME, PER_SALARY, d.ORG_ID, PER_STARTDATE
						 from 		$search_main_from a, PER_PERSONAL b, PER_PRENAME c
										$search_from 
						 where 	a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE
										$search_condition
						 order by PRO_DATE desc, a.$search_pos, a.PER_ID
					  ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("d.ORG_ID", "b.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PER_NAME = trim($data[PN_NAME]) . trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
			$POS_NO = trim($data[POS_NO]);
			$PER_SALARY = number_format(trim($data[PER_SALARY]), 2, '.', ',');
			
			$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], 1);
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE], 1);
			$PER_STARTDATE = date_difference(date("Y-m-d"), trim($data[PER_STARTDATE]), "full");
			
			$LEVEL_NO = trim($data[LEVEL_NO]);

			$POH_EFFECTIVEDATE = "";
			$cmd = " select			POH_EFFECTIVEDATE
							 from			PER_POSITIONHIS
							 where		PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO' 
							 order by		POH_EFFECTIVEDATE ";
			$db_dpis1->send_cmd($cmd);
		//		$db_dpis1->show_error();
			$data1 = $db_dpis1->get_array();
			$POH_EFFECTIVEDATE = substr(trim($data1[POH_EFFECTIVEDATE]), 0, 10);
			$POS_STARTDATE = "";
			if($POH_EFFECTIVEDATE) $POS_STARTDATE = date_difference(date("Y-m-d"), $POH_EFFECTIVEDATE, "full");

			$PL_NAME = $PN_NAME = $PM_NAME = $ORG_NAME = "";
	
			if($PER_TYPE==1){
				$PL_CODE = trim($data[PL_CODE]);
				if($PL_CODE){
					$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[PL_NAME]);
				} // end if		

				$PT_CODE = trim($data[PT_CODE]);
				if($PT_CODE){
					$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PT_NAME = $data2[PT_NAME];
				} // end if
		
				$PM_CODE = trim($data[PM_CODE]);
				if($PM_CODE){
					$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PM_NAME = $data2[PM_NAME];
				} // end if
			}elseif($PER_TYPE==2){
				$PL_CODE = trim($data[PN_CODE]);
				if($PL_CODE){
					$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[PN_NAME]);
				} // end if		
			}elseif($PER_TYPE==3){
				$PL_CODE = trim($data[EP_CODE]);
				if($PL_CODE){
					$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[EP_NAME]);
				} // end if		
			} elseif($PER_TYPE==4){
				$PL_CODE = trim($data[TP_CODE]);
				if($PL_CODE){
					$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = trim($data2[EP_NAME]);
				} // end if		
			} // end if
			
			//หาระดับตำแหน่ง
			$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME=trim($data2[LEVEL_NAME]);
			$POSITION_LEVEL = $data2[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			$POSITION = ($PM_CODE?"$PM_NAME ( ":"") . $PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"") . ($PM_CODE?" )":"");

			$ORG_ID = trim($data[ORG_ID]);
			if($ORG_ID){
				$cmd = " select ORG_SHORT from PER_ORG where ORG_ID='$ORG_ID' ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = $data2[ORG_SHORT];
			} // end if
			
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $data_row;
			$arr_content[$data_count][per_name] = $PER_NAME;
			$arr_content[$data_count][per_position] = $POSITION;
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$arr_content[$data_count][pos_no] = $POS_NO;
			$arr_content[$data_count][per_salary] = $PER_SALARY;
			$arr_content[$data_count][per_change_date] = $POS_CHANGE_DATE;
			$arr_content[$data_count][pos_start] = $POS_STARTDATE;
			$arr_content[$data_count][per_start_date] = $PER_STARTDATE;
							
			$data_count++;
	} // end while

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
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_POSITION = $arr_content[$data_count][per_position];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$PER_CHANGE_DATE = $arr_content[$data_count][per_change_date];
			$POS_STARTDATE = $arr_content[$data_count][pos_start];
			$PER_START_DATE = $arr_content[$data_count][per_start_date];

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));  
			$worksheet->write_string($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$PER_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4,  (($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_SALARY):$PER_SALARY), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6,(($NUMBER_DISPLAY==2)?convert2thaidigit( $POS_CHANGE_DATE): $POS_CHANGE_DATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_STARTDATE):$POS_STARTDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>