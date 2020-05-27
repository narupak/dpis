<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "50";
	$heading_width[1] = "30";
	$heading_width[2] = "10";
	$heading_width[3] = "40";
	$heading_width[4] = "70";
		
	function print_header(){
		global $pdf, $heading_width, $DEPARTMENT_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"สมรรถนะ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ระดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"รายละเอียด",'LTBR',1,'C',1);
	} // function		
		
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if

  	if(trim($search_department_id)) $arr_search_condition[] = "(a.DEPARTMENT_ID = '$search_department_id')";
  	if(trim($search_cp_code)) $arr_search_condition[] = "(a.$arr_fields[0] LIKE '$search_cp_code%')";
  	if(trim($search_cl_no)) $arr_search_condition[] = "($arr_fields[1] LIKE '%$search_cl_no%')";
  	if(trim($search_cl_name)) $arr_search_condition[] = "($arr_fields[2] LIKE '%$search_cl_name%')";
	$search_condition = "";
	$search_condition_count = "";
	if(count($arr_search_condition)) {
		$search_condition = " and " . implode(" and ", $arr_search_condition);
		$search_condition_count = " where " . implode(" and ", $arr_search_condition);
	}

	if($DPISDB=="odbc"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID  
											$search_condition
					order by 	$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID  
								$search_condition 
					order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select 		a.$arr_fields[0], a.$arr_fields[1], $arr_fields[2], $arr_fields[3], CP_NAME, a.DEPARTMENT_ID 
				 	from 		$table a, PER_COMPETENCE b  
					where		a.$arr_fields[0]=b.$arr_fields[0] and a.DEPARTMENT_ID=b.DEPARTMENT_ID  
								$search_condition 
					order by 	$order_str ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[2] = $data[$arr_fields[2]];
			$$arr_fields[3] = $data[$arr_fields[3]];
			$CP_NAME = $data[CP_NAME];

			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_DEPARTMENT_NAME = $data1[ORG_NAME];

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, $TMP_DEPARTMENT_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[1], 7, $CP_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $$arr_fields[1], $border , "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, $$arr_fields[2], $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, $$arr_fields[3], $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=4; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < $count_data){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end while
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>