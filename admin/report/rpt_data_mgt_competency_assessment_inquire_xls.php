<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "การประเมินสมรรถนะหลักทางการบริหาร";
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

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 18);
		$worksheet->set_column(5, 5, 18);
		$worksheet->set_column(6, 6, 18);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ-สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง/ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "วันที่ประเมิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "วันที่ขึ้นทะเบียน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "Mean", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // function		
		
	$from = "PER_MGT_COMPETENCY_ASSESSMENT a";		
	//echo $from."+".$search_condition."+".$order_str; exit;
		
	if($DPISDB=="odbc"){	
		$cmd = " select 	a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID    
						 from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where	a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
										$limit_data
						$order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select		a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID   
								from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
	   							where 		a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+)
												$search_condition
												$limit_data
								$order_str 
					 ";						 
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.PN_CODE, a.CA_ID, CA_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, a.CA_APPROVE_DATE, a.CA_ORG_NAME, a.CA_DEPARTMENT_NAME, CA_LINE, CA_MEAN, a.PER_ID    
						 from 		$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where	a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
										$limit_data
						$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "$cmd ($count_data)<br>";		exit;
	$data_count = $data_row = 0;
     while ($data = $db_dpis->get_array()) {
            $data_row++;

            $temp_CA_ID = $data[CA_ID];
            $current_list .= ((trim($current_list))?", ":"") . "'" . $temp_CA_ID ."'";
    
            $CA_CODE = $data[CA_CODE];
            $PN_CODE = trim($data[PN_CODE]);
            $CA_NAME = $data[CA_NAME];		
            $CA_SURNAME = $data[CA_SURNAME];
            $CA_TEST_DATE = show_date_format($data[CA_TEST_DATE], 1);
            $CA_APPROVE_DATE = show_date_format($data[CA_APPROVE_DATE], 1);
            $TMP_CA_MEAN = trim($data[CA_MEAN]);
    
            $PER_ID = trim($data[PER_ID]);
//			$PER_ID = trim($data[CA_ID]);
			$TMP_POSITION = "";
            if ($PER_ID) {
                $cmd = " select POS_ID, a.LEVEL_NO, b.LEVEL_NAME,b.POSITION_LEVEL, PN_CODE, PER_NAME, PER_SURNAME 
							from PER_PERSONAL a, PER_LEVEL b 
							where PER_ID=$PER_ID and a.LEVEL_NO=b.LEVEL_NO ";
                $db_dpis2->send_cmd($cmd);
                if ($data2 = $db_dpis2->get_array()) {
                    $POS_ID = trim($data2[POS_ID]);
                    $LEVEL_NO = trim($data2[LEVEL_NO]);
                    $LEVEL_NAME = trim($data2[POSITION_LEVEL]);
                    $PN_CODE = trim($data2[PN_CODE]);
                    $CA_NAME = trim($data2[PER_NAME]);
                    $CA_SURNAME = trim($data2[PER_SURNAME]);
        
                    if ($POS_ID) { 
                        $cmd = " select b.PL_NAME, a.CL_NAME, c.ORG_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b, PER_ORG c 
                                where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
                        $db_dpis2->send_cmd($cmd);
                        //echo $cmd;
                        $data2 = $db_dpis2->get_array();
        //				$TMP_POSITION = $data2[PL_NAME] . " " . $data2[CL_NAME];
                        $TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
                    }
                    $ORG_NAME = $data2[ORG_NAME];
                } else { // ถ้าอ่านไม่ได้
                    $POS_ID = "";
                    $LEVEL_NO = trim($data[LEVEL_NO]);
                    $LEVEL_NAME = "";
                    $ORG_NAME = trim($data[CA_ORG_NAME]).trim($data[CA_DEPARTMENT_NAME]);
                    $TMP_POSITION = trim($data[CA_LINE]);
                }	// if ($data2 = $db_dpis2->get_array())
            }	// if ($PER_ID)
            
			$PN_NAME = "";
            if ($PN_CODE) {
                $cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
                $db_dpis2->send_cmd($cmd);
                $data2 = $db_dpis2->get_array();
                $PN_NAME = $data2[PN_NAME];
            }

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $data_row;
			$arr_content[$data_count][per_name] = "$PN_NAME$CA_NAME $CA_SURNAME";
			$arr_content[$data_count][position] = $TMP_POSITION;
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$arr_content[$data_count][ca_test_date] =  $CA_TEST_DATE;
			$arr_content[$data_count][ca_approve_date] =  $CA_APPROVE_DATE;
			$arr_content[$data_count][ca_mean] =  $TMP_CA_MEAN;
					
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

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$PER_NAME = $arr_content[$data_count][per_name];
			$POSITION = $arr_content[$data_count][position];
			$ORG_NAME = $arr_content[$data_count][org_name];
			 $CA_TEST_DATE = $arr_content[$data_count][ca_test_date];
			$CA_APPROVE_DATE = $arr_content[$data_count][ca_approve_date];
			$CA_MEAN = $arr_content[$data_count][ca_mean];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, " $CA_TEST_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$CA_APPROVE_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$CA_MEAN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"การประเมินสมรรถนะหลักทางการบริหาร .xls\"");
	header("Content-Disposition: inline; filename=\"การประเมินสมรรถนะหลักทางการบริหาร .xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>