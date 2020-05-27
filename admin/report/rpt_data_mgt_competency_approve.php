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
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "��â�鹺ѭ�ռ���ҹ��û����Թ���ö����ѡ�ҧ��ú����� (੾�м���ҹ��ѡ�ٵ� ���.1)";
	$report_code = "";
	$orientation='P';

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
	
	$heading_width[0] = "20";
	$heading_width[1] = "20";
	$heading_width[2] = "60";
	$heading_width[3] = "50";
	$heading_width[4] = "50";

	$heading_text[0] = "�ѹ�������Ѻ|��û����Թ";
	$heading_text[1] = "�ѹ���|��鹺ѭ��";
	$heading_text[2] = "���� - ʡ��";
	$heading_text[3] = "���˹�";
	$heading_text[4] = "�ѧ�Ѵ";
	
	$heading_align = array('C','C','C','C','C');

	$temp_start =  save_date($search_approve_date_from);
	$temp_end =  save_date($search_approve_date_to);
	if (!$temp_start) $temp_start = "2000-01-01";
	if (!$temp_end) $temp_end = "2099-31-12";
	$cmd = " select CA_ID, CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT 
							where CA_APPROVE_DATE is NOT NULL and CA_APPROVE_DATE>='$temp_start' and CA_APPROVE_DATE<='$temp_end'
							order by CA_NAME, CA_SURNAME, CA_APPROVE_DATE ";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$CA_TEST_DATE = show_date_format(trim($data[CA_TEST_DATE]), $DATE_DISPLAY);
		$CA_APPROVE_DATE = show_date_format(trim($data[CA_APPROVE_DATE]), $DATE_DISPLAY);
//		$PER_ID = $data[PER_ID];
		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$CA_NAME = trim($data[CA_NAME]);
		$CA_SURNAME = trim($data[CA_SURNAME]);
		$CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
		$CA_DEPARTMENT_NAME = trim($data[CA_DEPARTMENT_NAME]);
		$CA_LINE = trim($data[CA_LINE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CA_MGT = trim($data[CA_MGT]);

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
					$TMP_POSITION = trim($data2[PL_NAME])?($data2[PL_NAME] ."".$LEVEL_NAME. ((trim($data2[PT_NAME]) != "�����" && $LEVEL_NO >= 6)?$data2[PT_NAME]:"")):"".$LEVEL_NAME;
				}
				$ORG_NAME = $data2[ORG_NAME];
			} else { // �����ҹ�����
				$POS_ID = "";
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = "";
				$ORG_NAME = trim($data[CA_ORG_NAME]);
				$TMP_POSITION = trim($data[CA_LINE]);
			}	// if ($data2 = $db_dpis2->get_array())
		}
		if (!$TMP_POSITION) $TMP_POSITION = $CA_LINE;
		if (!$ORG_NAME) $ORG_NAME = $CA_MINISTRY_NAME." ".$CA_DEPARTMENT_NAME;

		$PN_NAME = "";
		if ($PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][test_date] = $CA_TEST_DATE;
		$arr_content[$data_count][approve_date] = $CA_APPROVE_DATE;
		$arr_content[$data_count][ca_name] = "$PN_NAME$CA_NAME $CA_SURNAME";
		$arr_content[$data_count][position] = "$TMP_POSITION";
		$arr_content[$data_count][org_name] = "$ORG_NAME";
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CA_TEST_DATE = $arr_content[$data_count][test_date];
			$CA_APPROVE_DATE = $arr_content[$data_count][approve_date];
			$CA_NAME = $arr_content[$data_count][ca_name];
			$POSITION = $arr_content[$data_count][position];
			$ORG_NAME = $arr_content[$data_count][org_name];
		
			$arr_data = (array) null;
			$arr_data[] = $CA_TEST_DATE;
			$arr_data[] = $CA_APPROVE_DATE;
			$arr_data[] = $CA_NAME;
			$arr_data[] = $POSITION;
			$arr_data[] = $ORG_NAME;

			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for				
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>