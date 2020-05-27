<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends.php");

	// ==== use for testing phase =====
	$DPISDB = "mysql";
	$db_dpis = $db;
	// ==========================

	ini_set("max_execution_time", 1800);
	
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
	
	$heading_width[0] = "45";
	$heading_width[1] = "45";
	$heading_width[2] = "35";
	$heading_width[3] = "20";
	$heading_width[4] = "35";
	$heading_width[5] = "20";
		
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont('angsa','',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ระดับตำแหน่ง (ใหม่)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ประเภทตำแหน่ง (ใหม่)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ประเภทเงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ลำดับขั้น",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เงินเดือน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ใช้งาน/ยกเลิก",'LTBR',1,'C',1);
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

  	if(trim($search_type_code)) $arr_search_condition[] = "(pln.$arr_fields[1] = '$search_type_code')";
  	if(trim($search_group)) $arr_search_condition[] = "(pln.$arr_fields[0] = '$search_group')";
  	if(trim($search_layer)) $arr_search_condition[] = "(pln.$arr_fields[2] = '$search_layer')";
  	if(trim($search_layer_no_min)) $arr_search_condition[] = "(pln.$arr_fields[3] >= $search_layer_no_min)";
  	if(trim($search_layer_no_max)) $arr_search_condition[] = "(pln.$arr_fields[3] <= $search_layer_no_max)";
  	if(trim($search_salary_min)) $arr_search_condition[] = "((pln.$arr_fields[2] = 1 and pln.$arr_fields[4] >= $search_salary_min) or (pln.$arr_fields[2] = 2 and pln.$arr_fields[5] >= $search_salary_min))";
  	if(trim($search_salary_max)) $arr_search_condition[] = "((pln.$arr_fields[2] = 1 and pln.$arr_fields[4] <= $search_salary_max) or (pln.$arr_fields[2] = 2 and pln.$arr_fields[6] <= $search_salary_max))";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "  select		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						   from 		$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn		
						   where 		pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N 
						   					$search_condition 						   
						   order by 	pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2] desc, $arr_fields[3]
					   ";					   
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						  from 			$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn 
						  where 		pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N 
											$search_condition
						  order by 	pln.$arr_fields[0], TO_CHAR(pln.$arr_fields[1], '99'), $arr_fields[2] desc, $arr_fields[3] 
					  "; 
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], $arr_fields[6], $arr_fields[7], pgn.PT_GROUP_NAME, ptn.PT_NAME_N 
						   	from 		$table pln, PER_GROUP_N pgn, PER_TYPE_N ptn		
						   	where 	pln.PT_GROUP_N = pgn.PT_GROUP_N and pln.PT_CODE_N = ptn.PT_CODE_N  
											$search_condition
							order by 	pln.$arr_fields[0], pln.$arr_fields[1], $arr_fields[2] desc, $arr_fields[3]
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
			$$arr_fields[2] = ($data[$arr_fields[2]]==1)?"ขั้นเงินเดือน":(($data[$arr_fields[2]]==2)?"ช่วงเงินเดือน":"");
			if ($data[$arr_fields[2]] == 1) {
				$temp_show_layer = (($data[$arr_fields[3]] - floor($data[$arr_fields[3]]))==0)?number_format($data[$arr_fields[3]], 0):number_format($data[$arr_fields[3]], 1, ".", ",");;
				$temp_show_salary = number_format($data[$arr_fields[4]], 2, ".", ",");
			} elseif ($data[$arr_fields[2]] == 2) {
				$temp_show_layer = "";
				$temp_show_salary = number_format($data[$arr_fields[5]], 2, ".", ",") ." - ". $show_arr_fields[6] = number_format($data[$arr_fields[6]], 2, ".", ",");		
			}
			$$arr_fields[3] = $data[$arr_fields[3]];
			$PT_GROUP_NAME = $data[PT_GROUP_NAME];
			$PT_NAME_N = $data[PT_NAME_N];
			$$arr_fields[7] = ($data[$arr_fields[7]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";

			$border = "";
			$pdf->SetFont('angsa','',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->MultiCell($heading_width[0], 7, $PT_NAME_N, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[1], 7, $PT_GROUP_NAME, $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[2], 7, $$arr_fields[2], $border, 0, 'C', 0);
			$pdf->Cell($heading_width[3], 7, $temp_show_layer, $border, 0, 'C', 0);
			$pdf->Cell($heading_width[4], 7, $temp_show_salary, $border, 0, 'R', 0);
			$pdf->Image($$arr_fields[7],($pdf->x + ($heading_width[5] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=5; $i++){
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