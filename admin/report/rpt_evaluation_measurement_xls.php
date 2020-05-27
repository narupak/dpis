<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(b.PV_CODE = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายงานการบริหารวัดการใช้งานระบบประเมินค่างาน : เดือน ". $month_full[$search_month][TH] ." ปี ".$search_year;
	$report_code = "รายงานการวัดการประเมินค่างาน";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$worksheet->set_column(0, 0, 40);
	$worksheet->set_column(1, 1, 40);
	$worksheet->set_column(2, 2, 10);
	$worksheet->set_column(3, 3, 12);

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_row($xlsRow, 50);
		$worksheet->write($xlsRow, 0, "ส่วนราชการระดับ$MINISTRY_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 1, "ส่วนราชการระดับ$DEPARTMENT_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 2, "ความถี่ (ครั้ง/เดือน)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=M&fontSize=&wrapText=1"));
		$worksheet->write($xlsRow, 3, "ความสำเร็จ (ครั้ง/เดือน)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=B&fontSize=&wrapText=1"));
	} // function		
	
	function count_evaluation($DEPARTMENT_ID, $count_success){
		global $DPISDB, $db_dpis2;
		global $search_month, $search_year;
		
		$arr_search_condition[] = "(c.ORG_ID=$DEPARTMENT_ID)";
		if($count_success) $arr_search_condition[] = "(e.ISPASSED='Y')";
		$search_condition = "";
		$search_condition = " where ". implode(" and ", $arr_search_condition);
/*
		if($DPISDB=="odbc"){
			$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
												c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
												COUNT(e.pos_id) as SUCCESS_FREQ
								from		(
													(
														(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
													) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
												) inner join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
								$search_condition
								group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
												c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
												COUNT(e.pos_id) as SUCCESS_FREQ
								from		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d, JOB_EVALUATION e
								where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID_REF and c.ORG_ID_REF=d.ORG_ID_REF
												and a.POS_ID=e.POS_ID
												$search_condition
								group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}elseif($DPISDB=="mssql"){
			$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
												c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
												COUNT(e.pos_id) as SUCCESS_FREQ
								from		(
													(
														(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
													) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
												) inner join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
								$search_condition
								group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		return ($data2->SUCCESS_FREQ + 0);
*/

		if($DPISDB=="odbc"){
			$cmd = " 	select		a.POS_ID, e.TEST_TIME
								from		(
													(
														(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
													) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
												) inner join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
								$search_condition
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " 	select		a.POS_ID, e.TEST_TIME
								from		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d, JOB_EVALUATION e
								where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID_REF and c.ORG_ID_REF=d.ORG_ID_REF
												and a.POS_ID=e.POS_ID
												$search_condition
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}elseif($DPISDB=="mssql"){
			$cmd = " 	select		a.POS_ID, e.TEST_TIME
								from		(
													(
														(
															PER_POSITION a
															inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
														) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
													) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
												) inner join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
								$search_condition
								order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
						  ";
		}
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$SUCCESS_FREQ = 0;
		while($data2 = $db_dpis2->get_array()){
			$TEST_TIME = substr($data2[TEST_TIME], 0, 10);
			$arr_temp = explode("-", $TEST_TIME);
			$TEST_TIME = ($arr_temp[2].$arr_temp[1]);
//			echo ($search_year.$search_month)." :: $TEST_TIME<br>";
			if( $TEST_TIME >= ($search_year.$search_month) ) $SUCCESS_FREQ++;
		} // end while
		return $SUCCESS_FREQ;
	} // function
	
/*
	if($DPISDB=="odbc"){
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
											COUNT(e.pos_id) as EVALUATION_FREQ
							from		(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) left join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
							$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
											COUNT(e.pos_id) as EVALUATION_FREQ
							from		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d, JOB_EVALUATION e
							where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID_REF and c.ORG_ID_REF=d.ORG_ID_REF
											and a.POS_ID=e.POS_ID(+)
											$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME, 
											COUNT(e.pos_id) as EVALUATION_FREQ
							from		(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) left join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
							$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}
*/
	if($DPISDB=="odbc"){
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME
							from		(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) left join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
							$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME
							from		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d, JOB_EVALUATION e
							where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID_REF and c.ORG_ID_REF=d.ORG_ID_REF
											and a.POS_ID=e.POS_ID(+)
											$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		d.ORG_SEQ_NO as MINISTRY_SEQ_NO, d.ORG_ID as MINISTRY_ID, d.ORG_NAME as MINISTRY_NAME, 
                      						c.ORG_SEQ_NO as DEPARTMENT_SEQ_NO, c.ORG_ID as DEPARTMENT_ID, c.ORG_NAME as DEPARTMENT_NAME
							from		(
												(
													(
														PER_POSITION a
														inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
												) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
											) left join JOB_EVALUATION e on (a.POS_ID=e.POS_ID)
							$search_condition
							group by d.ORG_SEQ_NO, d.ORG_ID, d.ORG_NAME, c.ORG_SEQ_NO, c.ORG_ID, c.ORG_NAME
							order by	d.ORG_SEQ_NO, d.ORG_ID, c.ORG_SEQ_NO, c.ORG_ID
					  ";
	}
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	while($data = $db_dpis->get_array()){
		$MINISTRY_ID = $data[MINISTRY_ID];
		$MINISTRY_NAME = $data[MINISTRY_NAME];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];		
//		$EVALUATION_FREQ = $data[EVALUATION_FREQ];
		
		$EVALUATION_FREQ = count_evaluation($DEPARTMENT_ID, 0);
		
		if($EVALUATION_FREQ >= $search_min_freq && $EVALUATION_FREQ <= $search_max_freq){
			$SUCCESS_FREQ = count_evaluation($DEPARTMENT_ID, 1);

			$arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
			$arr_content[$data_count][evaluation_freq] = $EVALUATION_FREQ;
			$arr_content[$data_count][success_freq] = $SUCCESS_FREQ;

			$data_count++;
		} // end if
	} // end while
	
	$count_data = count($arr_content);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
			$xlsRow++;
		} // end if

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format_new("xlsFmtSubTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
			$xlsRow++;
		} // end if

		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
			$EVALUATION_FREQ = $arr_content[$data_count][evaluation_freq];
			$SUCCESS_FREQ = $arr_content[$data_count][success_freq];
			
			$xlsRow++;
			if($data_count==0 && $data_count==(count($arr_content) - 1)){
				$worksheet->write_string($xlsRow, 0, "$MINISTRY_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			}elseif($data_count==0){
				$worksheet->write_string($xlsRow, 0, "$MINISTRY_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			}elseif($data_count < (count($arr_content) - 1)){
				$worksheet->write_string($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=LR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			}else{
				$worksheet->write_string($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=LBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			} // end if
			$worksheet->write_string($xlsRow, 1, "$DEPARTMENT_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->write_string($xlsRow, 2, "$EVALUATION_FREQ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));
			$worksheet->write_string($xlsRow, 3, "$SUCCESS_FREQ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLBR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=B&fontSize=&wrapText=1"));			
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
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
?>B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
	} // end if

	$workbook->c