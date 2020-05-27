<?
//	include("../../php_scripts/connect_database.php");
//	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

//	ini_set("max_execution_time", "1800");
	
	$get_fall = $_GET['fall'];
	$get_line = $_GET['line'];
//	echo "line=$get_line<br>";

	$arr_content[0]="line 0 -ทดสอบพิมพ์ บรรทัดที่  0 --- ทดสอบพิมพ์ บรรทัดที่ศูนย์";
	$arr_content[1]="line 1 -ทดสอบพิมพ์ บรรทัดที่  1 --- ทดสอบพิมพ์ บรรทัดที่หนึ่ง";
	$arr_content[2]="line 2 -ทดสอบพิมพ์ บรรทัดที่  2 --- ทดสอบพิมพ์ บรรทัดที่สอง";
	$arr_content[3]="line 3 -ทดสอบพิมพ์ บรรทัดที่  3 --- ทดสอบพิมพ์ บรรทัดที่สาม";
	$arr_content[4]="line 4 -ทดสอบพิมพ์ บรรทัดที่  4 --- ทดสอบพิมพ์ บรรทัดที่สี่";
	$arr_content[5]="line 5 -ทดสอบพิมพ์ บรรทัดที่  5 --- ทดสอบพิมพ์ บรรทัดที่ห้า";
	$arr_content[6]="line 6 -ทดสอบพิมพ์ บรรทัดที่  6 --- ทดสอบพิมพ์ บรรทัดที่หก";
	$arr_content[7]="line 7 -ทดสอบพิมพ์ บรรทัดที่  7 --- ทดสอบพิมพ์ บรรทัดที่เจ็ด";
	$arr_content[8]="line 8 -ทดสอบพิมพ์ บรรทัดที่  8 --- ทดสอบพิมพ์ บรรทัดที่แปด";
	$arr_content[9]="line 9 -ทดสอบพิมพ์ บรรทัดที่  9 --- ทดสอบพิมพ์ บรรทัดที่เก้า";
	$arr_content[10]="line 10-ทดสอบพิมพ์ บรรทัดที่  10 --- ทดสอบพิมพ์ บรรทัดที่สิบ";
	$arr_content[11]="line 11-ทดสอบพิมพ์ บรรทัดที่  11 --- ทดสอบพิมพ์ บรรทัดที่สิบเอ็ด";
	$arr_content[12]="line 12-ทดสอบพิมพ์ บรรทัดที่  12 --- ทดสอบพิมพ์ บรรทัดที่สิบสอง";
	$arr_content[13]="line 13-ทดสอบพิมพ์ บรรทัดที่  13 --- ทดสอบพิมพ์ บรรทัดที่สิบสาม";
	$arr_content[14]="line 14-ทดสอบพิมพ์ บรรทัดที่  14 --- ทดสอบพิมพ์ บรรทัดที่สิบสี่";
	$arr_content[15]="line 15-ทดสอบพิมพ์ บรรทัดที่  15 --- ทดสอบพิมพ์ บรรทัดที่สิบห้า";
	$arr_content[16]="line 16-ทดสอบพิมพ์ บรรทัดที่  16 --- ทดสอบพิมพ์ บรรทัดที่สิบหก";
	$arr_content[17]="line 17-ทดสอบพิมพ์ บรรทัดที่  17 --- ทดสอบพิมพ์ บรรทัดที่สิบเจ็ด";
	$arr_content[18]="line 18-ทดสอบพิมพ์ บรรทัดที่  18 --- ทดสอบพิมพ์ บรรทัดที่สิบแปด";
	$arr_content[19]="line 19-ทดสอบพิมพ์ บรรทัดที่  19 --- ทดสอบพิมพ์ บรรทัดที่สิบเก้า";
	$arr_content[20]="line 20-ทดสอบพิมพ์ บรรทัดที่  20 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบ";
	$arr_content[21]="line 21-ทดสอบพิมพ์ บรรทัดที่  21 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบเอ็ด";
	$arr_content[22]="line 22-ทดสอบพิมพ์ บรรทัดที่  22 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบสอง";
	$arr_content[23]="line 23-ทดสอบพิมพ์ บรรทัดที่  23 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบสาม";
	$arr_content[24]="line 24-ทดสอบพิมพ์ บรรทัดที่  24 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบสี่";
	$arr_content[25]="line 25-ทดสอบพิมพ์ บรรทัดที่  25 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบห้า";
	$arr_content[26]="line 26-ทดสอบพิมพ์ บรรทัดที่  26 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบหก";
	$arr_content[27]="line 27-ทดสอบพิมพ์ บรรทัดที่  27 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบเจ็ด";
	$arr_content[28]="line 28-ทดสอบพิมพ์ บรรทัดที่  28 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบแปด";
	$arr_content[29]="line 29-ทดสอบพิมพ์ บรรทัดที่  29 --- ทดสอบพิมพ์ บรรทัดที่ยี่สิบเก้า";
	$arr_content[30]="line 30-ทดสอบพิมพ์ บรรทัดที่  30 --- ทดสอบพิมพ์ บรรทัดที่สามสิบ";
	
	$count_data=31;
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "บ. นายบรรเจิด จันทร์หอม";
	$report_title = "ทดสอบ: การพิมพ์ตามบรรทัดที่กำหนด";
	$report_code = "RDUM001";
	$orientation='L';
//	echo "new PDF<br>";
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align,"","");
	
//	echo "open PDF<br>";
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
//	$pdf->SetTextColor(0, 0, 0);
//	$pdf->SetFont('angsa','',14);
	
	$page_start_x = $pdf->x;	 $page_start_y = $pdf->y;

	// set head
		$pdf->SetFont('angsa','',14);
		if ($get_fall == "a") {
			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
			$pdf->SetDrawColor(hexdec("10"),hexdec("10"),hexdec("10"));
		} else {
			$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
			$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
			$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
		}
		$heading_width[0] = "200";
		$pdf->Cell($heading_width[0] ,7,'หัวเรื่อง 1','LTR',1,'C',1);
	// end set head
	
	if($count_data > 0){
		$pdf->AutoPageBreak = false;

		for($data_count=0; $data_count < $get_line; $data_count++){
				$border = "LTBR";
				$pdf->SetFont('angsab','',14);
				
				if ($get_fall <> "a" and $data_count < $get_line-1) {
					$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
					$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
				} else {
					$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
					$pdf->SetFillColor(hexdec("FF"),hexdec("00"),hexdec("00"));
				}
//				$start_x = $pdf->x; 
				$pdf->x=$page_start_x;
				$start_y = $pdf->y; $max_y = $pdf->y;
				
				$pdf->MultiCell($heading_width[0], 7, "$arr_content[$data_count]", $border, "L");
//				$pdf->Cell($heading_width[0] ,7,"$arr_content[$data_count]", $border, 0, "L", 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
//				$pdf->x = $start_x + $heading_width[0];
				$pdf->y = $start_y;
//				$pdf->Cell($heading_width[1], 7, ($GRAND_TOTAL1[$DEPARTMENT_ID]?number_format($GRAND_TOTAL1[$DEPARTMENT_ID]):"-"), $border, 0, 'R', 0);
	
				//================= Draw Border Line ====================
				if ($get_fall <> "a"  and $data_count < $get_line-2) {
					$pdf->SetDrawColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
				} else {
					$pdf->SetDrawColor(hexdec("10"),hexdec("10"),hexdec("10"));
				}
					$line_start_y = $start_y;		$line_start_x = $start_x;
					$line_end_y = $max_y;		$line_end_x = $start_x;
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
//					for($i=0; $i<=0; $i++){
//						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
//						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
//						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
//					} // end for
					//====================================================
	
					if(($pdf->h - $max_y - 10) < 15){ 
						if($data_count < (count($arr_content) - 1)){
							$pdf->AddPage();
//							if ($get_fall=="a") {
//								$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
//								$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
//							} else {
//								$pdf->SetTextColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//								$pdf->SetFillColor(hexdec("FF"),hexdec("FF"),hexdec("FF"));
//							}
							$pdf->Cell($heading_width[0] ,7,'หัวเรื่อง 1','LTR',0,'C',1);
							$max_y = $pdf->y;
						} // end if
					}else{
						if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					} // end if
					//$pdf->x = $start_x; 
					$pdf->y = $max_y;
		} // end for
//		if ($get_fall == "a") {		
//			$pdf->SetDrawColor(hexdec("10"),hexdec("10"),hexdec("10"));
//			while ($pdf->y < ($pdf->h-25)) {
//					if($pdf->y > $max_y) $max_y = $pdf->y;
//					$start_y=$pdf->y;
//					$line_start_y = $start_y;		$line_start_x = $start_x;
//					$line_end_y = $max_y;		$line_end_x = $start_x;
//					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
//					$pdf->y = $max_y+($line_end_y-$line_start_y);
					
//					for($i=0; $i<=0; $i++){
//						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
//						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
//						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
//					} // end for				
//			}  // end while
//		} // end if 
	} // end if

	$pdf->close();
	$pdf->Output();
?>