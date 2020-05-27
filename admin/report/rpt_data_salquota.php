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
	
	if ($SALQ_TYPE == 1) 		 {  $SALQ_TYPE1 = 1;		$SALQ_TYPE2 = 1;  }
	elseif ($SALQ_TYPE == 2)  {  $SALQ_TYPE1 = 1;		$SALQ_TYPE2 = 2;  }
	elseif ($SALQ_TYPE == 3)  {  $SALQ_TYPE1 = 2;		$SALQ_TYPE2 = 1;  }		
	elseif ($SALQ_TYPE == 4)  {  $SALQ_TYPE1 = 2;		$SALQ_TYPE2 = 2;  }
	elseif ($SALQ_TYPE == 5)  {  $SALQ_TYPE1 = 3;		$SALQ_TYPE2 = 1;  }
	elseif ($SALQ_TYPE == 6)  {  $SALQ_TYPE1 = 3;		$SALQ_TYPE2 = 2;  }

	$cmd = " select		SALQ_PERCENT, SALQ_DATE
					 from		PER_SALQUOTA
					 where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	
	$SALQ_PERCENT = $data[SALQ_PERCENT];
	$SALQ_DATE = substr(trim($data[SALQ_DATE]), 0, 10);
	if($SALQ_DATE){
		$SALQ_DATE = show_date_format($SALQ_DATE,$DATE_DISPLAY);
	} // end if

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "โควตาและหลักเกณฑ์การเลื่อนขั้นเงินเดือน";
	$report_code = "P0401";
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
	
	$heading_width[0] = "15";
	$heading_width[1] = "100";
	$heading_width[2] = "30";
	$heading_width[3] = "30";
	
	
	//new format ********************************
	$heading_text[0] = "ลำดับ|ที่";
	$heading_text[1] = "$ORG_TITLE";
	$heading_text[2] = ($SALQ_TYPE2==1?"จำนวนคนที่ได้ 1 ขั้น":($SALQ_TYPE2==2?"วงเงินที่เพิ่ม":""));
	$heading_text[3] = ($SALQ_TYPE2==1?"จำนวนคนที่จัดสรร":($SALQ_TYPE2==2?"วงเงินที่จัดสรร":""));
	
	$heading_align = array('C','C','C','C');
/*
	function print_header(){
		global $pdf, $heading_width;
		global $SALQ_TYPE2;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7, ($SALQ_TYPE2==1?"จำนวนคนที่ได้ 1 ขั้น":($SALQ_TYPE2==2?"วงเงินที่เพิ่ม":"")),'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,($SALQ_TYPE2==1?"จำนวนคนที่จัดสรร":($SALQ_TYPE2==2?"วงเงินที่จัดสรร":"")),'LTBR',1,'C',1);
	} // function		
*/
	if($DPISDB=="odbc"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	}elseif($DPISDB=="oci8"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	}elseif($DPISDB=="mysql"){
		if($SALQDTL_TYPE == 1){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL1 a, PER_ORG b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		}elseif($SALQDTL_TYPE == 2){
			$cmd = " select			a.ORG_ID, b.ORG_NAME, a.SALQD_QTY1, a.SALQD_QTY2
							 from			PER_SALQUOTADTL2 a, PER_ORG_ASS b
							 where		a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID 
							 					and a.ORG_ID=b.ORG_ID 
							 order by 	b.ORG_SEQ_NO, b.ORG_CODE ";	
		} // end if
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$TOTAL_SALQD_QTY1 = $TOTAL_SALQ_QTY2 = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = trim($data[ORG_NAME]);
		$SALQD_QTY1 = $data[SALQD_QTY1];
		$SALQD_QTY2 = $data[SALQD_QTY2];
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][salqd_qty1] = $SALQD_QTY1;
		$arr_content[$data_count][salqd_qty2] = $SALQD_QTY2;
		
		$TOTAL_SALQD_QTY1 += $SALQD_QTY1;
		$TOTAL_SALQD_QTY2 += $SALQD_QTY2;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	if($count_data){
		$pdf->AutoPageBreak = false;
		
		$pdf->Cell(100, 7, "$MINISTRY_NAME", $border, 0, 'L', 0);
		$pdf->Cell(100, 7, "$DEPARTMENT_NAME", $border, 1, 'L', 0);
		
		$pdf->Cell(100, 7, "ปีงบประมาณ $SALQ_YEAR", $border, 0, 'L', 0);
		$pdf->Cell(100, 7, $PERSON_TYPE[$SALQ_TYPE1], $border, 1, 'L', 0);

		$pdf->Cell(100, 7, "โควตาการเลื่อนขั้นเงินเดือนหนึ่งขั้นได้ไม่เกินร้อยละ $SALQ_PERCENT", $border, 0, 'L', 0);
		$pdf->Cell(100, 7, ($SALQ_TYPE2==1?"เลื่อนครั้งที่ 1":($SALQ_TYPE2==2?"เลื่อนครั้งที่ 2":"")), $border, 1, 'L', 0);

		$pdf->Cell(100, 7, "ของจำนวน$PERSON_TYPE[$SALQ_TYPE1] ณ วันที่ $SALQ_DATE", $border, 0, 'L', 0);
		$pdf->Cell(100, 7, ($SALQDTL_TYPE==1?"โครงสร้างตามกฎหมาย":($SALQDTL_TYPE==2?"โครงสร้างตามคำสั่งมอบหมายงาน":"")), $border, 1, 'L', 0);

        $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
	//	print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$SALQD_QTY1 = number_format($arr_content[$data_count][salqd_qty1], 2);
			$SALQD_QTY2 = number_format($arr_content[$data_count][salqd_qty2], 2);
			//new format************************************************************			
			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] = "$ORG_NAME";
			$arr_data[] =  (($NUMBER_DISPLAY==2)?convert2thaidigit($SALQD_QTY1):$SALQD_QTY1);
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($SALQD_QTY2):$SALQD_QTY2);
				
			$data_align = array("C", "L", "R", "R");
			
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "", "000000", "");		// เส้นปิดบรรทัด			
		
		//new format************************************************************			
		$arr_data = (array) null;
		$arr_data[] ="<**1**>รวม";
		$arr_data[] ="<**1**>รวม";
		$arr_data[] = ($TOTAL_SALQD_QTY1?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_SALQD_QTY1,2)):number_format($TOTAL_SALQD_QTY1, 2)):"-");
		$arr_data[] = ($TOTAL_SALQD_QTY2?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_SALQD_QTY2,2)):number_format($TOTAL_SALQD_QTY2, 2)):"-");
		
		$data_align = array("R", "R", "R", "R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "b", "000000", "");		// เส้นปิดบรรทัด				
	//new format************************************************************			
		$arr_data = (array) null;
		$arr_data[] ="<**1**>คงเหลือ";
		$arr_data[] ="<**1**>คงเหลือ";
		$arr_data[] = "";
		$arr_data[] = (($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2)?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format(($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2),2)):number_format(($TOTAL_SALQD_QTY1 - $TOTAL_SALQD_QTY2), 2)):"-");
					
		$data_align = array("R", "R", "R", "R");
		
		$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "12", "", "000000", "");		//TRHBL
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "b", "000000", "");		// เส้นปิดบรรทัด				

	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		
	} // end if

	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>