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
	$report_title = "แผนฝึกอบรมประจำปี $year";
	$report_code = "";
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
	
	$heading_width[0] = "45";
	$heading_width[1] = "45";
	$heading_width[2] = "45";
	$heading_width[3] = "45";
	$heading_width[4] = "25";
	$heading_width[5] = "25";
	$heading_width[6] = "25";
	$heading_width[7] = "25";
	
	function print_header(){
		global $pdf, $heading_width;
		global $POS_NO_TITLE, $FULLNAME_TITLE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ชื่อโครงการ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"หน่วยที่จัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"หน่วยงานที่รับผิดชอบ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ผู้อนุมัติ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"หมวดโครงการ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"วันที่อนุมัติ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เลขที่หนังสือ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"สถานที่อบรม",'LTBR',1,'C',1);
	} // function		

	$cmd = " select PROJ_NAME, PROJ_ID_REF, TPJ_BUDGET_YEAR, DEPARTMENT_ID, TPJ_MANAGE_ORG, TPJ_RESPONSE_ORG, TPJ_APP_PER_ID, PG_ID, TPJ_APP_DATE, TPJ_APP_DOC_NO, TPJ_INOUT_TRAIN, PLAN_ID from PER_TRAIN_PROJECT where TPJ_BUDGET_YEAR='$year' order by TPJ_BUDGET_YEAR, PLAN_ID, PROJ_NAME ";
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PROJ_NAME = $data[PROJ_NAME];
		$PROJ_ID_REF = $data[PROJ_ID_REF];
		$TPJ_BUDGET_YEAR = $data[TPJ_BUDGET_YEAR];
//		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$TPJ_MANAGE_ORG = $data[TPJ_MANAGE_ORG];
		$TPJ_RESPONSE_ORG = $data[TPJ_MANAGE_ORG];

		$TPJ_APP_PER_ID = $data[TPJ_APP_PER_ID];
		$cmd2 = " select PER_NAME, PER_SURNAME, PN_NAME
					   from	PER_PERSONAL a, PER_PRENAME b
					  where a.PN_CODE=b.PN_CODE and PER_ID=$TPJ_APP_PER_ID ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$TPJ_APP_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];

		$PG_ID = $data[PG_ID];
		$cmd2 = " select PG_ID, PG_NAME from PER_PROJECT_GROUP where PG_ID=$PG_ID and PG_ACTIVE=1 ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$PG_NAME = $data2[PG_NAME];

		$TPJ_APP_DATE = $data[TPJ_APP_DATE];
		$TPJ_APP_DOC_NO = $data[TPJ_APP_DOC_NO];
		$TPJ_INOUT_TRAIN = $data[TPJ_INOUT_TRAIN];

		$PLAN_ID = $data[PLAN_ID];
		$cmd2 = " select * from PER_TRAIN_PLAN where PLAN_ID=$PLAN_ID ";
		$db_dpis2->send_cmd($cmd2);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();	
		$PLAN_NAME = $data2[PLAN_NAME];

		$arr_content[$data_count][pj_name]=$PROJ_NAME;
		$arr_content[$data_count][pj_id_ref] = $PROJ_ID_REF;
		$arr_content[$data_count][bg_year] = $TPJ_BUDGET_YEAR;
		$arr_content[$data_count][dept_id] = $DEPARTMENT_ID;
		$arr_content[$data_count][manage_org] = $TPJ_MANAGE_ORG;
		$arr_content[$data_count][respon_org] = $TPJ_RESPONSE_ORG;
		$arr_content[$data_count][per_id] = $TPJ_APP_PER_ID;
		$arr_content[$data_count][per_name] = $TPJ_APP_PER_NAME;
		$arr_content[$data_count][pg_id] = $PG_ID;
		$arr_content[$data_count][pg_name] = $PG_NAME;
		$arr_content[$data_count][app_date] = $TPJ_APP_DATE;
		$arr_content[$data_count][doc_no] = $TPJ_APP_DOC_NO;
		$arr_content[$data_count][io_train] = $TPJ_INOUT_TRAIN;
		$arr_content[$data_count][plan_name] = $PLAN_NAME;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		$PLAN_NAME = "";
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$PROJ_NAME = $arr_content[$data_count][pj_name];
			$PROJ_ID_REF = $arr_content[$data_count][pj_id_ref];
//			$TPJ_BUDGET_YEAR = $arr_content[$data_count][bg_year];
//			$DEPARTMENT_ID = $arr_content[$data_count][dept_id];
			$TPJ_MANAGE_ORG = $arr_content[$data_count][manage_org];
			$TPJ_RESPONSE_ORG = $arr_content[$data_count][respon_org];
//			$TPJ_APP_PER_ID = $arr_content[$data_count][per_id];
			$TPJ_APP_PER_NAME = $arr_content[$data_count][per_name];
//			$PG_ID = $arr_content[$data_count][pg_id];
			$PG_NAME = $arr_content[$data_count][pg_name];
			$TPJ_APP_DATE = $arr_content[$data_count][app_date];
			$TPJ_APP_DOC_NO = $arr_content[$data_count][doc_no];
			$TPJ_INOUT_TRAIN = ($arr_content[$data_count][io_train]=="1"?"อบรมภายใน":"อบรมภายนอก");
			
			
			if ($PLAN_NAME != $arr_content[$data_count][plan_name]) {
				if(($pdf->h - $max_y - 10) < 30){	// เผื่อไว้สำหรับข้อมูล อย่างน้อย 1 บรรทัด 
																	// (หัว PLAN_NAME อีก 1 บรรทัด บรรทัดละ 15 รวมเป็น 30)
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					if($data_count < (count($arr_content) - 1)){
						$pdf->AddPage();
						print_header();
						$max_y = $pdf->y;
					} // end if
				}
								
				$PLAN_NAME = $arr_content[$data_count][plan_name];
				
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("FF"),hexdec("00"),hexdec("00"));

				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
				$pdf->MultiCell($heading_width[0], 7, "$PLAN_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

				for($i=0; $i<=7; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for

				$pdf->x = $start_x;			$pdf->y = $max_y;
			}
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			$pdf->MultiCell($heading_width[0], 7, "$PROJ_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[1], 7, "$TPJ_MANAGE_ORG", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$TPJ_RESPONSE_ORG", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, "$TPJ_APP_PER_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, "$PG_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[5], 7, "$TPJ_APP_DATE", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[6], 7, "$TPJ_APP_DOC_NO", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[7], 7, "$TPJ_INOUT_TRAIN", $border, "C");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);

			for($i=0; $i<=7; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for				
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>