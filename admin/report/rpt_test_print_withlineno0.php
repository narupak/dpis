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

	$arr_content[0]="line 0 -���ͺ����� ��÷Ѵ���  0 --- ���ͺ����� ��÷Ѵ����ٹ��";
	$arr_content[1]="line 1 -���ͺ����� ��÷Ѵ���  1 --- ���ͺ����� ��÷Ѵ���˹��";
	$arr_content[2]="line 2 -���ͺ����� ��÷Ѵ���  2 --- ���ͺ����� ��÷Ѵ����ͧ";
	$arr_content[3]="line 3 -���ͺ����� ��÷Ѵ���  3 --- ���ͺ����� ��÷Ѵ������";
	$arr_content[4]="line 4 -���ͺ����� ��÷Ѵ���  4 --- ���ͺ����� ��÷Ѵ������";
	$arr_content[5]="line 5 -���ͺ����� ��÷Ѵ���  5 --- ���ͺ����� ��÷Ѵ������";
	$arr_content[6]="line 6 -���ͺ����� ��÷Ѵ���  6 --- ���ͺ����� ��÷Ѵ���ˡ";
	$arr_content[7]="line 7 -���ͺ����� ��÷Ѵ���  7 --- ���ͺ����� ��÷Ѵ�����";
	$arr_content[8]="line 8 -���ͺ����� ��÷Ѵ���  8 --- ���ͺ����� ��÷Ѵ���Ỵ";
	$arr_content[9]="line 9 -���ͺ����� ��÷Ѵ���  9 --- ���ͺ����� ��÷Ѵ������";
	$arr_content[10]="line 10-���ͺ����� ��÷Ѵ���  10 --- ���ͺ����� ��÷Ѵ����Ժ";
	$arr_content[11]="line 11-���ͺ����� ��÷Ѵ���  11 --- ���ͺ����� ��÷Ѵ����Ժ���";
	$arr_content[12]="line 12-���ͺ����� ��÷Ѵ���  12 --- ���ͺ����� ��÷Ѵ����Ժ�ͧ";
	$arr_content[13]="line 13-���ͺ����� ��÷Ѵ���  13 --- ���ͺ����� ��÷Ѵ����Ժ���";
	$arr_content[14]="line 14-���ͺ����� ��÷Ѵ���  14 --- ���ͺ����� ��÷Ѵ����Ժ���";
	$arr_content[15]="line 15-���ͺ����� ��÷Ѵ���  15 --- ���ͺ����� ��÷Ѵ����Ժ���";
	$arr_content[16]="line 16-���ͺ����� ��÷Ѵ���  16 --- ���ͺ����� ��÷Ѵ����Ժˡ";
	$arr_content[17]="line 17-���ͺ����� ��÷Ѵ���  17 --- ���ͺ����� ��÷Ѵ����Ժ��";
	$arr_content[18]="line 18-���ͺ����� ��÷Ѵ���  18 --- ���ͺ����� ��÷Ѵ����ԺỴ";
	$arr_content[19]="line 19-���ͺ����� ��÷Ѵ���  19 --- ���ͺ����� ��÷Ѵ����Ժ���";
	$arr_content[20]="line 20-���ͺ����� ��÷Ѵ���  20 --- ���ͺ����� ��÷Ѵ�������Ժ";
	$arr_content[21]="line 21-���ͺ����� ��÷Ѵ���  21 --- ���ͺ����� ��÷Ѵ�������Ժ���";
	$arr_content[22]="line 22-���ͺ����� ��÷Ѵ���  22 --- ���ͺ����� ��÷Ѵ�������Ժ�ͧ";
	$arr_content[23]="line 23-���ͺ����� ��÷Ѵ���  23 --- ���ͺ����� ��÷Ѵ�������Ժ���";
	$arr_content[24]="line 24-���ͺ����� ��÷Ѵ���  24 --- ���ͺ����� ��÷Ѵ�������Ժ���";
	$arr_content[25]="line 25-���ͺ����� ��÷Ѵ���  25 --- ���ͺ����� ��÷Ѵ�������Ժ���";
	$arr_content[26]="line 26-���ͺ����� ��÷Ѵ���  26 --- ���ͺ����� ��÷Ѵ�������Ժˡ";
	$arr_content[27]="line 27-���ͺ����� ��÷Ѵ���  27 --- ���ͺ����� ��÷Ѵ�������Ժ��";
	$arr_content[28]="line 28-���ͺ����� ��÷Ѵ���  28 --- ���ͺ����� ��÷Ѵ�������ԺỴ";
	$arr_content[29]="line 29-���ͺ����� ��÷Ѵ���  29 --- ���ͺ����� ��÷Ѵ�������Ժ���";
	$arr_content[30]="line 30-���ͺ����� ��÷Ѵ���  30 --- ���ͺ����� ��÷Ѵ�������Ժ";
	
	$count_data=31;
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "�. ��º���Դ �ѹ������";
	$report_title = "���ͺ: ��þ��������÷Ѵ����˹�";
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
		$pdf->Cell($heading_width[0] ,7,'�������ͧ 1','LTR',1,'C',1);
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
							$pdf->Cell($heading_width[0] ,7,'�������ͧ 1','LTR',0,'C',1);
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