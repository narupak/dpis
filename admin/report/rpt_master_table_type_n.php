<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends.php");

	// ==== use for testing phase =====
	$DPISDB = "mysql";
	$db_dpis = $db;
	// ==========================

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
	$heading_width[1] = "120";
	$heading_width[2] = "40";
	$heading_width[3] = "20";
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"รหัส",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ระดับตำแหน่ง (ใหม่)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ประเภทตำแหน่ง (ใหม่)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ใช้งาน/ยกเลิก",'LTBR',1,'C',1);
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

  	if(trim($search_code)) $arr_search_condition[] = "(a.$arr_fields[0] like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(a.$arr_fields[1] like '%$search_name%')";
  	if(trim($search_group)) $arr_search_condition[] = "(a.$arr_fields[2] = '$search_group')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], a.$arr_fields[3], b.PT_GROUP_NAME
							from		$table a, PER_GROUP_N b
							where		a.$arr_fields[2]=b.PT_GROUP_N
											$search_condition
							order by PT_SEQ_NO, a.$arr_fields[0] 
					   ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], a.$arr_fields[3], b.PT_GROUP_NAME
							from		$table a, PER_GROUP_N b
							where		a.$arr_fields[2]=b.PT_GROUP_N
											$search_condition
							order by PT_SEQ_NO, $arr_fields[0] 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.$arr_fields[0], a.$arr_fields[1], a.$arr_fields[3], b.PT_GROUP_NAME
							from		$table a, PER_GROUP_N b
							where		a.$arr_fields[2]=b.PT_GROUP_N
											$search_condition
							order by PT_SEQ_NO, $arr_fields[0] 
					   ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = $data[$arr_fields[1]];
			$$arr_fields[3] = ($data[$arr_fields[3]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
			$PT_GROUP_NAME = $data[PT_GROUP_NAME];

			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $$arr_fields[0], $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, $$arr_fields[1], $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $PT_GROUP_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Image($$arr_fields[3],($pdf->x + ($heading_width[3] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=3; $i++){
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
		$pdf->SetFont('angsab','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>