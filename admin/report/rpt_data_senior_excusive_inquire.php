<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("rpt_data_senior_excusive_inquire_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "สอบถามข้อมูลผู้ฝึกอบรมหลักสูตรนักบริหารระดับสูง";
	$report_code = "rpt_data_senior_excusive_inquire";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
/*
	$heading_width[0] = "10";
	$heading_width[1] = "40";
	$heading_width[2] = "35";
	$heading_width[3] = "40";
	$heading_width[4] = "40";
	$heading_width[5] = "45";
	$heading_width[6] = "35";
	$heading_width[7] = "35";
	$heading_width[8] = "10";

	$heading_text[0] = "ลำดับที่";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ตำแหน่ง/ระดับ";
	$heading_text[3] = "สังกัด";
	$heading_text[4] ="ตำแหน่งระหว่างอบรม";
	$heading_text[5] = "กรม";
	$heading_text[6] = "กระทรวง";
	$heading_text[7] ="ระยะเวลา";
	$heading_text[8] ="รุ่น";
	
	$heading_align = array('C','C','C','C','C','C','C','C','C');
*/
	$from = "PER_SENIOR_EXCUSIVE a, PER_LEVEL b";		
	if(trim($search_cardno)) $arr_search_condition[] = "(SE_CARDNO like '".trim($search_cardno)."%')";	
	if(trim($search_per_id)) $arr_search_condition[] = "(a.PER_ID = $search_per_id)";
	if(trim($search_name)) $arr_search_condition[] = "(SE_NAME like '$search_name%')";
	if(trim($search_surname)) $arr_search_condition[] = "(SE_SURNAME like '$search_surname%')";
	if($search_ministry_name){
		$arr_search_condition[] = "(SE_MINISTRY_NAME = '".trim($search_ministry_name)."')";	
	}
	if($search_department_name){
		$arr_search_condition[] = "(SE_DEPARTMENT_NAME = '".trim($search_department_name)."')";			
	}
	if($search_train_ministry_name){
		$arr_search_condition[] = "(SE_TRAIN_MINISTRY = '".trim($search_train_ministry_name)."')";	
	}
	if($search_train_department_name){
		$arr_search_condition[] = "(SE_TRAIN_DEPARTMENT = '".trim($search_train_department_name)."')";			
	}
	if(trim($search_org_name))  {
		$arr_search_condition[] = "(SE_ORG_NAME = '".trim($search_org_name)."')";	
	}
	if( trim($search_se_no) ) {
		$arr_search_condition[] = "(trim(SE_NO) = '".trim($search_se_no)."')";	
	}
	if(trim($search_se_startdate)) {
		$search_se_startdate_tmp =  save_date($search_se_startdate);
		$arr_search_condition[] = "(SE_STARTDATE <= '$search_se_startdate_tmp' and SE_ENDDATE >= '$search_se_startdate_tmp')";
	} 
	if ($search_se_enddate) {
		$search_se_enddate_tmp =  save_date($search_se_enddate);
		$arr_search_condition[] = "(SE_ENDDATE <= '$search_se_enddate_tmp')";
	} // end if 
	$search_condition = "";
	if(count($arr_search_condition)) 	$search_condition 	= " and " . implode(" and ", $arr_search_condition);	
	
	if($DPISDB=="odbc"){	
		$cmd = " select  distinct a.PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID   
						 from 		$from, PER_ORG f, PER_POSITION e, PER_PERSONAL d 
						 where		a.LEVEL_NO=b.LEVEL_NO  and a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID 
											$search_condition
						$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select	 distinct a.PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID   
								from 			$from , PER_ORG f, PER_POSITION e, PER_PERSONAL d
	   							where		a.LEVEL_NO=b.LEVEL_NO(+) and a.PER_ID = d.PER_ID(+) and d.POS_ID=e.POS_ID(+) and e.ORG_ID=f.ORG_ID(+)
													$search_condition   
							$order_str";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct PN_CODE, a.SE_ID, SE_NAME, SE_SURNAME, SE_NO, a.SE_BIRTHDATE, a.LEVEL_NO, SE_TRAIN_POSITION, a.PER_ID, 
												a.SE_TYPE, a.SE_MINISTRY_NAME, a.SE_DEPARTMENT_NAME, a.SE_ORG_NAME, a.SE_LINE, a.SE_MGT, a.SE_YEAR, 
												a.SE_TRAIN_MINISTRY, a.SE_TRAIN_DEPARTMENT, a.SE_CODE, a.SE_STARTDATE, a.SE_ENDDATE, a.PER_ID  
						 from 		$from, PER_ORG f, PER_POSITION e, PER_PERSONAL d     
						 where	a.LEVEL_NO=b.LEVEL_NO and a.PER_ID = d.PER_ID and d.POS_ID=e.POS_ID and e.ORG_ID=f.ORG_ID
										$search_condition
						$order_str";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "$cmd ($count_data)<br>";		exit;
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_SE_ID = $data[SE_ID];
		$current_list .= ((trim($current_list))?",":"") . $TMP_SE_ID;

		$TMP_PN_CODE = trim($data[PN_CODE]);
		$TMP_SE_NAME = $data[SE_NAME];
		$TMP_SE_SURNAME = $data[SE_SURNAME];
		$TMP_PER_NAME = $TMP_SE_NAME ." ". $TMP_SE_SURNAME;
		$TMP_SE_NO = trim($data[SE_NO]);
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_SE_TRAIN_POSITION = trim($data[SE_TRAIN_POSITION]);
		$TMP_SE_TRAIN_DEPARTMENT = trim($data[SE_TRAIN_DEPARTMENT]);
		$TMP_SE_TRAIN_MINISTRY = trim($data[SE_TRAIN_MINISTRY]);
		$TMP_PER_ID = trim($data[PER_ID]);
		//$dateDiff = calculate_sec(substr($data[SE_ENDDATE], 8, 2), substr($data[SE_ENDDATE], 5, 2), substr($data[SE_ENDDATE], 0, 4)) - calculate_sec(substr($data[SE_STARTDATE], 8, 2), substr($data[SE_STARTDATE], 5, 2), substr($data[SE_STARTDATE], 0, 4));
		//$TMP_RESTDATE = floor($dateDiff/60/60/24);						
		$TMP_RESTDATE = floor(date_difference($data[SE_ENDDATE], $data[SE_STARTDATE], "d"));
		$SE_STARTDATE = show_date_format($data[SE_STARTDATE], 1);
		$SE_ENDDATE = show_date_format($data[SE_ENDDATE], 1);

		$cmd = "select LEVEL_NAME from PER_LEVEL
				where LEVEL_NO = '$TMP_LEVEL_NO'  ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[SCH_NAME]);
		
		if ($TMP_PER_ID) {
			$cmd = "select POS_ID, POEM_ID, POEMS_ID,POT_ID, a.LEVEL_NO, b.LEVEL_NAME,b.POSITION_LEVEL, PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL a, PER_LEVEL b where PER_ID=$TMP_PER_ID and a.LEVEL_NO=b.LEVEL_NO";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_ID = trim($data2[POS_ID]);
			$POEM_ID = trim($data2[POEM_ID]);
			$POEMS_ID = trim($data2[POEMS_ID]);
			$POT_ID = trim($data2[POT_ID]);
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			$LEVEL_NAME = trim($data2[POSITION_LEVEL]);
			
			$TMP_PN_CODE = trim($data2[PN_CODE]);
			$TMP_PER_NAME = trim($data2[PER_NAME]) ." ". trim($data2[PER_SURNAME]);

			if ($POS_ID) { 
				$cmd = " select b.PL_NAME, a.CL_NAME, c.ORG_NAME, a.PT_CODE from PER_POSITION a, PER_LINE b, PER_ORG c 
						where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				//echo $cmd;
				$data2 = $db_dpis2->get_array();
//				$TMP_POSITION = $data2[PL_NAME] . " " . $data2[CL_NAME];
				$TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
			} elseif ($POEM_ID) {
				$cmd = "	select PN_NAME, ORG_NAME from PER_POS_EMP a, PER_POS_NAME b, PER_ORG c  
						where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[PN_NAME];
			}elseif ($POEMS_ID) {
				$cmd = "	select EP_NAME, ORG_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c  
						where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[EP_NAME];
			}elseif ($POT_ID) {
				$cmd = "	select TP_NAME, ORG_NAME from PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c  
						where POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$TMP_POSITION = $data2[TP_NAME];
			}
			$ORG_NAME = $data2[ORG_NAME];					
		}	// if ($TMP_PER_ID)
		
		if ($TMP_PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = $data2[PN_NAME];
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][per_name] = "$TMP_PN_NAME$TMP_PER_NAME";
		$arr_content[$data_count][position] = $TMP_POSITION;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][train_position] = $TMP_SE_TRAIN_POSITION;
		$arr_content[$data_count][train_department] = $TMP_SE_TRAIN_DEPARTMENT;
		$arr_content[$data_count][train_ministry] = $TMP_SE_TRAIN_MINISTRY;
		$arr_content[$data_count][sten_date] = "$SE_STARTDATE - $SE_ENDDATE";
		$arr_content[$data_count][se_no] = $TMP_SE_NO;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
	
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "head_text:$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$arr_data = (array) null;
			$arr_data[] = $arr_content[$data_count][order];
			$arr_data[] = $arr_content[$data_count][per_name];
			$arr_data[] = $arr_content[$data_count][position];
			$arr_data[] = $arr_content[$data_count][org_name];
			$arr_data[] = $arr_content[$data_count][train_position];
			$arr_data[] = $arr_content[$data_count][train_department];
			$arr_data[] = $arr_content[$data_count][train_ministry];
			$arr_data[] = $arr_content[$data_count][sten_date];
			$arr_data[] = $arr_content[$data_count][se_no];
			
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** แสดงข้อความว่า 'ไม่มีข้อมูล'<br>";
	} // end if

	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>