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
	$pdf->SetMargins(5,5,5);/*5,5,5*/
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	
	$heading_width[0] = "45";/*65*/
	$heading_width[1] = "10";
	$heading_width[2] = "67";/*70*/
	$heading_width[3] = "35";
        $heading_width[4] = "20";
	$heading_width[5] = "20";
		
	function print_header(){
		global $pdf, $heading_width, $DEPARTMENT_TITLE, $ACTIVE_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"$DEPARTMENT_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ÃËÑÊ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ª×èÍÊÁÃÃ¶¹Ğ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"âÁà´Å",'LTBR',0,'C',1);
                $pdf->Cell($heading_width[4] ,7,"¤èÒ¹éÓË¹Ñ¡",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"$ACTIVE_TITLE",'LTBR',1,'C',1);
	} // function		
		
  	if(trim($search_department_id)) $arr_search_condition[] = "(DEPARTMENT_ID = '$search_department_id')";
  	if(trim($search_code)) $arr_search_condition[] = "(CP_CODE like '$search_code%')";
  	if(trim($search_name)) $arr_search_condition[] = "(CP_NAME like '%$search_name%')";
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	$cmd = "	select		CP_CODE, CP_NAME, CP_MEANING, CP_MODEL, CP_ACTIVE, DEPARTMENT_ID,DEF_WEIGHT 
					from		PER_COMPETENCE
					$search_condition
					order by 	DEPARTMENT_ID, CP_CODE ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$CP_CODE = $data[CP_CODE];
			$CP_NAME = $data[CP_NAME];
			$CP_MEANING = $data[CP_MEANING];
			$CP_MODEL = $data[CP_MODEL];
                        $DEF_WEIGHT = $data[DEF_WEIGHT];
			$CP_ACTIVE = ($data[CP_ACTIVE]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";

			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_DEPARTMENT_NAME = $data1[ORG_NAME];

			if ($CP_MODEL == 1)			$CP_MODEL = "ÊÁÃÃ¶¹ĞËÅÑ¡";
			elseif ($CP_MODEL == 2)		$CP_MODEL = "ÊÁÃÃ¶¹Ğ¼ÙéºÃÔËÒÃ";
			elseif ($CP_MODEL == 3)		$CP_MODEL = "ÊÁÃÃ¶¹Ğ»ÃĞ¨ÓÊÒÂ§Ò¹";

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $TMP_DEPARTMENT_NAME, $border, 0, 'L', 0);
			$pdf->MultiCell($heading_width[1], 7, $CP_CODE, $border, "C");
			
                        if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $CP_NAME, $border, "L");
			
                        if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			
                        $pdf->MultiCell($heading_width[3], 7, $CP_MODEL, $border, "L");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
                        
                        $pdf->MultiCell($heading_width[4], 7, $DEF_WEIGHT, $border, "R");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3]+ $heading_width[4];
			$pdf->y = $start_y;
			
                        $pdf->Image($CP_ACTIVE,($pdf->x + ($heading_width[5] / 2) - 1.5), ($pdf->y + 1.5), 5, 5,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4]+ $heading_width[5];

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
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** äÁèÁÕ¢éÍÁÙÅ **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>