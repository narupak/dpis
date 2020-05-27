<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
  	if(trim($ORG_ID)){ 
		$arr_search_condition[] = "(c.ORG_ID=$ORG_ID)";
	}elseif($DEPARTMENT_ID){
		$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($MINISTRY_ID){
		$cmd = " select 	b.ORG_ID
						 from   	PER_ORG a, PER_ORG b
						 where  	a.OL_CODE='02' and b.OL_CODE='03' and a.ORG_ID_REF=$MINISTRY_ID and b.ORG_ID_REF=a.ORG_ID
						 order by a.ORG_ID, b.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}elseif($PV_CODE){
		$cmd = " select 	ORG_ID
						 from   	PER_ORG
						 where  	OL_CODE='03' and PV_CODE='$PV_CODE'
						 order by ORG_ID		  ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(c.ORG_ID in (". implode(",", $arr_org) ."))";
	}

	if(trim($PER_TYPE))	$arr_search_condition[] = "(b.PER_TYPE=$PER_TYPE)";
	if ($PER_TYPE==1) {
		$search_from = ", PER_POSITION c";
		$arr_search_condition[] = "b.POS_ID=c.POS_ID";
	} elseif ($PER_TYPE==2) { 
		$search_from = ", PER_POS_EMP c";
		$arr_search_condition[] = "b.POEM_ID=c.POEM_ID";
	} elseif ($PER_TYPE==3) { 
		$search_from = ", PER_POS_EMPSER c";
		$arr_search_condition[] = "b.POEMS_ID=c.POEMS_ID"; 
	} elseif ($PER_TYPE==4) { 
		$search_from = ", PER_POS_TEMP c";
		$arr_search_condition[] = "b.POT_ID=c.POT_ID"; 
	}

	$search_condition = "";
	if ($arr_search_condition)  $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition)? " and " : "") . $search_condition;	

	$now_ABS_YEAR = $KF_YEAR - 543;
	$old_ABS_YEAR = $KF_YEAR - 544;
	if ($KF_CYCLE == 1) {
		$tmp_search = "('$old_ABS_YEAR-10', '$old_ABS_YEAR-11', '$old_ABS_YEAR-12', '$now_ABS_YEAR-01', '$now_ABS_YEAR-02', '$now_ABS_YEAR-03')";
	} elseif ($KF_CYCLE == 2) {	
		$tmp_search = "('$now_ABS_YEAR-04', '$now_ABS_YEAR-05', '$now_ABS_YEAR-06', '$now_ABS_YEAR-07', '$now_ABS_YEAR-08', '$now_ABS_YEAR-09')";
	}		
	
	if ($DPISDB=="odbc") {
		$search_monthyear = "(left(ABS_ENDDATE,7) in $tmp_search)";	
	} elseif($DPISDB=="oci8") {
		$search_monthyear = "(substr(ABS_ENDDATE,1,7) in $tmp_search)";			
	}elseif($DPISDB=="mysql"){
		$search_monthyear = "(left(ABS_ENDDATE,7) in $tmp_search)";	
	}

	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานแสดงข้อมูล$PERSON_TYPE[$search_per_type]ที่ลาเกินกำหนด";
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

		$worksheet->set_column(0, 0, 35);
		$worksheet->set_column(1, 1, 35);
		$worksheet->set_column(2, 2, 12);
		$worksheet->set_column(3, 3, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "จำนวนวันที่ลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "จำนวนวันที่เกิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		
		
	if ($BKK_FLAG==1) $where = " '1', '3' ";
	else $where = " '01', '03','12' ";
	//  ลาป่วย + ลากิจ
	if($DPISDB=="odbc"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, sum(ABS_DAY) as DAY_ILL, count(a.PER_ID) TIME_ILL
						 from 			PER_ABSENTHIS a, PER_PERSONAL b 
											$search_from 
						 where 		AB_CODE IN ($where) and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME 
						 having 		(sum(ABS_DAY) > 0) or (count(a.PER_ID) > 0) ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, sum(ABS_DAY) as DAY_ILL, count(a.PER_ID) TIME_ILL
						 from 			PER_ABSENTHIS a, PER_PERSONAL b 
											$search_from 
						 where 		AB_CODE IN ($where) and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME  
						 having 		(sum(ABS_DAY) > 0) or (count(a.PER_ID) > 0) ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, sum(ABS_DAY) as DAY_ILL, count(a.PER_ID) TIME_ILL
						 from 			PER_ABSENTHIS a, PER_PERSONAL b 
											$search_from 
						 where 		AB_CODE IN ($where) and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME 
						 having 		(sum(ABS_DAY) > 0) or (count(a.PER_ID) > 0) ";
	} // end if
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();	
	while ($data = $db_dpis->get_array()) {
		$tmp_key = $data[PER_NAME] . $data[PER_ID];
		$arr_person[] = $tmp_key;
		$arr_per_id[$tmp_key] = $data[PER_ID];		

		$PN_CODE = $data[PN_CODE];
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE'";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();		
		$PN_NAME = $data1[PN_NAME];
		$arr_name[$tmp_key] = $PN_NAME . $data[PER_NAME] ." ". $data[PER_SURNAME];	
		//if ( ($data[DAY_ILL] > 23) || ($data[TIME_ILL] > 10) ) {
                if ( ($data[DAY_ILL] > $DAY_COND) || ($data[TIME_ILL] > $TIME_COND) ) {
			$arr_ill_day[$tmp_key] = $data[DAY_ILL];
			$arr_ill_time[$tmp_key] = $data[TIME_ILL];
		}
	}

	// สาย 
	if($DPISDB=="odbc"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, count(a.PER_ID) as TIME_LATE
						 from 			PER_ABSENTHIS a, PER_PERSONAL b
											$search_from 
						 where 		AB_CODE IN ('10') and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME  
						 having 		count(a.PER_ID) > 0 ";	
	}elseif($DPISDB=="oci8"){
            /*เดิม*/
		/*$cmd = " select 		a.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, count(a.PER_ID) as TIME_LATE
						 from 			PER_ABSENTHIS a, PER_PERSONAL b
											$search_from 
						 where 		AB_CODE IN ('10') and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, PN_CODE, PER_NAME, PER_SURNAME  
						 having 		count(a.PER_ID) > 0 ";	*/
            /*Release 5.1.0.6 Begin*/
            $cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, SUM(a.ABS_DAY) as TIME_LATE
						 from 			PER_ABSENTHIS a, PER_PERSONAL b
											$search_from 
						 where 		AB_CODE IN ('10') and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME ";	
            /*Release 5.1.0.6 End*/
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME, count(a.PER_ID) as TIME_LATE
						 from 			PER_ABSENTHIS a, PER_PERSONAL b
											$search_from 
						 where 		AB_CODE IN ('10') and a.PER_ID=b.PER_ID and PER_STATUS = 1 
											$search_condition and $search_monthyear
						 group by 	a.PER_ID, b.PN_CODE, PER_NAME, PER_SURNAME  
						 having 		count(a.PER_ID) > 0 ";	
	} // end if
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();	
	while ($data = $db_dpis->get_array()) {
		$tmp_key = $data[PER_NAME] . $data[PER_ID];
		$arr_person[] = $tmp_key;
		$arr_per_id[$tmp_key] = $data[PER_ID];

		$PN_CODE = $data[PN_CODE];
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE'";
		$db_dpis1->send_cmd($cmd);		
		$data1 = $db_dpis1->get_array();
		$PN_NAME = $data1[PN_NAME];
		$arr_name[$tmp_key] = $PN_NAME . $data[PER_NAME] ." ". $data[PER_SURNAME];		
		//if ( $data[TIME_LATE] > 18 ) { 
                if ( $data[TIME_LATE] > $DAY_LATE ) { 
			$arr_late[$tmp_key] = $data[TIME_LATE];
		}
	}
	$arr_person = array_unique($arr_person);
	sort($arr_person);
	$count_data = count($arr_late) + count($arr_ill_time) + count($arr_ill_day);
        
	$data_count = $data_row = 0;
        //var_dump($arr_person);
        /*var_dump($arr_ill_day);
        var_dump($arr_ill_time);
        var_dump($arr_late);*/
	for ($i=0; $i<count($arr_person); $i++) {
		$key_per_id = $arr_person[$i];

		for ($j=0; $j<=2; $j++) {
			//if ( ($j==0) && ($arr_late[$key_per_id]) && ($arr_late[$key_per_id] > 18) ) {
                        if ( ($j==0) && ($arr_late[$key_per_id]) && ($arr_late[$key_per_id] > $DAY_LATE) ) {
				$data_num++;
				$rest_late = $arr_late[$key_per_id] - $DAY_LATE;

				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][per_name] = "$data_num. $arr_name[$key_per_id]";
				$arr_content[$data_count][ab_name] = "สาย";
				$arr_content[$data_count][abs_day] = $arr_late[$key_per_id];
				$arr_content[$data_count][abs_over] = $rest_late;

				$data_count++;
			}		// if ($j==0) 

			//if ( ($j==1) && ($arr_ill_time[$key_per_id]) && ($arr_ill_time[$key_per_id] > 10) ) {
                        if ( ($j==1) && ($arr_ill_time[$key_per_id]) && ($arr_ill_time[$key_per_id] > 10) ) {
				$data_num++;
				$rest_ill_time = $arr_ill_time[$key_per_id] - 10;

				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][per_name] = "$data_num. $arr_name[$key_per_id]";
				$arr_content[$data_count][ab_name] = "ลากิจ ป่วยและป่วยจำเป็นเกิน(จำนวนครั้ง)";
				$arr_content[$data_count][abs_day] = $arr_ill_time[$key_per_id];
				$arr_content[$data_count][abs_over] = $rest_ill_time;

				$data_count++;
			}	// if ($j==1)
			
			//if ( ($j==2) && ($arr_ill_day[$key_per_id]) && (($arr_ill_day[$key_per_id] > 23) || ($arr_ill_time[$key_per_id] > 10)) ) {
                        if ( ($j==2) && ($arr_ill_day[$key_per_id]) && (($arr_ill_day[$key_per_id] > $DAY_COND) || ($arr_ill_time[$key_per_id] > $TIME_COND)) ) {
				$data_num++;
				$rest_ill_day = $arr_ill_day[$key_per_id] - $DAY_COND;
				$rest_ill_time = $arr_ill_time[$key_per_id] - $TIME_COND;

				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][per_name] = "$data_num. $arr_name[$key_per_id]";
				$arr_content[$data_count][ab_name] = "ลากิจ ป่วยและป่วยจำเป็นเกิน(จำนวนวัน/ครั้ง)";
				$arr_content[$data_count][abs_day] = "$arr_ill_day[$key_per_id]/$arr_ill_time[$key_per_id]";
				$arr_content[$data_count][abs_over] = "$rest_ill_day/$rest_ill_time";

				$data_count++;
			}	// if ($j==2)
		}  		// for ($j)  
	} // for ($i)
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";
        
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$PER_NAME = $arr_content[$data_count][per_name];
			$AB_NAME = $arr_content[$data_count][ab_name];
			//$ABS_DAY = number_format($arr_content[$data_count][abs_day], 1);
			//$ABS_OVER = number_format($arr_content[$data_count][abs_over], 1);
                        $ABS_DAY = $arr_content[$data_count][abs_day];
			$ABS_OVER = $arr_content[$data_count][abs_over];
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$PER_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$AB_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$ABS_DAY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$ABS_OVER", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
		} // end for				
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"รายงานแสดงข้อมูลลาเกินกำหนด.xls\"");
	header("Content-Disposition: inline; filename=\"รายงานแสดงข้อมูลลาเกินกำหนด.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>