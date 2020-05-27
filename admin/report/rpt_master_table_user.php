<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
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
	$pdf->SetFont('angsa','',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "20";
	$heading_width[1] = "70";
	$heading_width[2] = "90";
	$heading_width[3] = "20";
		
	$heading_text[0] = "$CODE_TITLE";
	$heading_text[1] = "$SHORTNAME_TITLE";
	$heading_text[2] = "$PL_TITLE";
	$heading_text[3] = "$ACTIVE_TITLE";
		
	$heading_align = array('C','C','C','C');
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_code)) $arr_search_condition[] = "($arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(($arr_fields[1] like '%$search_name%') or ($arr_fields[2] like '%$search_name%'))";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
							from		$table
							$search_condition
							order by $arr_fields[0] ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
							from		$table
							$search_condition
							order by $arr_fields[0] ";
	}elseif($DPISDB=="mssql"){
		$cmd = "	select		$arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3]
							from		$table
							$search_condition
							order by $arr_fields[0] ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	$pdf->AutoPageBreak = false;

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			$arr_data[] = $data[$arr_fields[0]];
			$arr_data[] = $data[$arr_fields[2]];
			$arr_data[] = $data[$arr_fields[1]];
			$arr_data[] = "<*img*".(($data[$arr_fields[3]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";

			$data_align = array("C", "L", "L", "C");
			
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";

		$data_align = array("C", "C", "C", "C");
			
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>