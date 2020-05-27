<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	$arr_search_condition[] = "(a.LEVEL_NO >= '09')";

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
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ข้อมูลข้าราชการระดับ 9 ขึ้นไปหรือเทียบเท่า";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	
	$report_code = "R1002";

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
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 8);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 8);
		$worksheet->set_column(7, 7, 10);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$MINISTRY_TITLE / $DEPARTMENT_TITLE     ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 2, "ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 3, "วันเดือนปีเกิด", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 4, "อายุตัว", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 5, "วันบรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 6, "อายุงาน", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
				   from			(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, b.PT_CODE desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MIN(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10)
			  	";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select		a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME											
				   from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_TYPE g, PER_MGT h, 
				   				PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_DECORATION l
				   where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PT_CODE=g.PT_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
								and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and k.DC_CODE=l.DC_CODE(+) and l.DC_TYPE in (1, 2)
								$search_condition
				   group by		a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, b.PT_CODE desc, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), MIN(l.DC_ORDER), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
			  	";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME
				   from			(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_TYPE, a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, e.ORG_NAME
				   order by		d.ORG_ID_REF, a.DEPARTMENT_ID, a.LEVEL_NO desc, b.PT_CODE desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MIN(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10)
			  	";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}	
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_TYPE = $data[PER_TYPE];
		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis->show_error();
		$data2 = $db_dpis2->get_array();
		$POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		$PL_NAME = $data[PL_NAME];
		$PM_NAME = $data[PM_NAME];

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "year"));
		$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		$PER_WORKAGE = floor(date_difference(date("Y-m-d"), $PER_STARTDATE, "year"));
		$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$PER_SALARY = $data[PER_SALARY];

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." .str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". $POSITION_TYPE. (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$POSITION_TYPE ) . (trim($PM_NAME)?")":"");
		$arr_content[$data_count][level_no] =  $LEVEL_NAME;
		$arr_content[$data_count][birthdate] = $PER_BIRTHDATE;
		$arr_content[$data_count][age] = $PER_AGE;
		$arr_content[$data_count][startdate] = $PER_STARTDATE;
		$arr_content[$data_count][workage] = $PER_WORKAGE;
		$arr_content[$data_count][salary] = number_format($PER_SALARY);
				
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
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$LEVEL_NO = $arr_content[$data_count][level_no];
			$BIRTHDATE = $arr_content[$data_count][birthdate];
			$AGE = $arr_content[$data_count][age];
			$STARTDATE = $arr_content[$data_count][startdate];
			$WORKAGE = $arr_content[$data_count][workage];
			$SALARY = $arr_content[$data_count][salary];
			
			if($REPORT_ORDER == "MINISTRY"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
				$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
			}elseif($REPORT_ORDER == "CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($NAME):$NAME), set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 1, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NO):$LEVEL_NO), set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 3, (($NUMBER_DISPLAY==2)?convert2thaidigit($BIRTHDATE):$BIRTHDATE), set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 4,(($NUMBER_DISPLAY==2)?convert2thaidigit($AGE):$AGE), set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 5,(($NUMBER_DISPLAY==2)?convert2thaidigit($STARTDATE):$STARTDATE), set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 6,(($NUMBER_DISPLAY==2)?convert2thaidigit($WORKAGE):$WORKAGE), set_format("xlsFmtTableDetail", "", "C", "", 0));
				$worksheet->write_string($xlsRow, 7,(($NUMBER_DISPLAY==2)?convert2thaidigit($SALARY):$SALARY), set_format("xlsFmtTableDetail", "", "R", "", 0));
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