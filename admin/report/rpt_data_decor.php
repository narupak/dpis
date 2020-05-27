<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$search_condition = "";
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	if($DC_TYPE == 1) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นสายสะพาย";
	elseif($DC_TYPE == 2) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นต่ำกว่าสายสะพาย";
	elseif($DC_TYPE == 3) $report_title = "$DEPARTMENT_NAME||รายงานการข้าราชการได้รับเครื่องราชฯ ชั้นเหรียญตรา";
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
	
	$heading_width[0] = "15";
	$heading_width[1] = "40";
	$heading_width[2] = "40";
	$heading_width[3] = "15";
	$heading_width[4] = "60";
	$heading_width[5] = "77";
	$heading_width[6] = "40";
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เครื่องราชฯ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7, "$ORG_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7, "เพศ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7, "ชื่อ - สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7, "ตำแหน่ง/ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7, "เครื่องราชฯ ล่าสุดที่ได้รับ",'LTBR',1,'C',1);
	} // function		

	$tmp_DE_DATE =  save_date($DE_DATE);

	if($DPISDB=="odbc"){
		$cmd = " select			a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE,  c.LEVEL_NO, e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE ,DC_NAME  
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c, PER_DECORATION d, PER_LEVEL e
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and d.DC_TYPE=$DC_TYPE and 
											a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE  and c.LEVEL_NO=e.LEVEL_NO
												$search_condition 
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select 		a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE, c.LEVEL_NO,  e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE, DC_NAME
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c, PER_DECORATION d , PER_LEVEL e 
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and 
											d.DC_TYPE=$DC_TYPE and a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE  and c.LEVEL_NO=e.LEVEL_NO
												$search_condition
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.DE_ID, b.PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_SALARY, PER_GENDER, c.PER_TYPE,  c.LEVEL_NO,  e.POSITION_LEVEL, 
											POS_ID, POEM_ID, POEMS_ID, POT_ID, b.DC_CODE,DC_NAME  
						 from			PER_DECOR a, PER_DECORDTL b, PER_PERSONAL c , PER_DECORATION d, PER_LEVEL e
						 where		a.DE_ID=$DE_ID and c.PER_TYPE=$PER_TYPE and d.DC_TYPE=$DC_TYPE and 
											a.DE_ID=b.DE_ID and b.PER_ID=c.PER_ID and b.DC_CODE=d.DC_CODE  and c.LEVEL_NO=e.LEVEL_NO
												$search_condition 
						 order by 	b.DC_CODE, ORG_ID, PER_NAME, PER_SURNAME ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_GENDER = (trim($data[PER_GENDER])==1)?  "ชาย" : "หญิง";
		$TMP_LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_LEVEL_NAME = trim($data[POSITION_LEVEL]);
		$TMP_DE_ID = trim($data[DE_ID]);
		$TMP_DC_NAME = trim($data[DC_NAME]);
		$TMP_DC_CODE = trim($data[DC_CODE]);
		$TMP_PN_CODE = trim($data[PN_CODE]);
		
		if ($TMP_PN_CODE) {
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_PN_NAME = trim($data2[PN_NAME]);
		}
		$TMP_PER_NAME = $TMP_PN_NAME . $data[PER_NAME] ." ". $data[PER_SURNAME];		

		$TMP_POS_ID = $data[POS_ID];
		if($TMP_POS_ID){
			$cmd = " select a.ORG_ID, ORG_NAME, a.PL_CODE, PL_NAME, a.PT_CODE
						  from PER_POSITION a, PER_ORG b, PER_LINE c
						  where POS_ID=$TMP_POS_ID and a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE ";	
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];			
			$PL_NAME = $data2[PL_NAME];			
			$PT_CODE = $data2[PT_CODE];			
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];			
			$TMP_POSITION = trim($PL_NAME)?($PL_NAME . $TMP_LEVEL_NAME . ((trim($PT_NAME) != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?trim($PT_NAME):"")):" ".$TMP_LEVEL_NAME;
		} // end if
		
		$TMP_POEM_ID = $data[POEM_ID];
		if($TMP_POEM_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.PN_CODE, PN_NAME
						  from PER_POS_NAME a, PER_ORG b, PER_POS_EMP c  
						  where POEM_ID=$TMP_POEM_ID and b.ORG_ID=c.ORG_ID and a.PN_CODE=c.PN_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();			
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[PN_NAME];
		} // end if

		$TMP_POEMS_ID = $data[POEMS_ID];
		if($TMP_POEMS_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.EP_CODE, EP_NAME
						  from PER_EMPSER_POS_NAME a, PER_ORG b, PER_POS_EMPSER c  
						  where POEMS_ID=$TMP_POEMS_ID and b.ORG_ID=c.ORG_ID and a.EP_CODE=c.EP_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[EP_NAME];			
		} // end if
		
		$TMP_POT_ID = $data[POT_ID];
		if($TMP_POT_ID){
			$cmd = " select c.ORG_ID, ORG_NAME, a.TP_CODE, TP_NAME
						  from PER_TEMP_POS_NAME a, PER_ORG b, PER_POS_TEMP  c  
						  where POT_ID=$TMP_POT_ID and b.ORG_ID=c.ORG_ID and a.TP_CODE=c.TP_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_NAME = $data2[ORG_NAME];
			$TMP_POSITION = $data2[TP_NAME];			
		} // end if

		// หาเครื่องราชที่ได้รับล่าสุด
		if($DPISDB=="odbc"){  
			$cmd= " select top 1 pdh.DC_CODE,DC_NAME from PER_DECORATEHIS pdh, PER_DECORATION pd
						where pdh.PER_ID=$TMP_PER_ID and pdh.DC_CODE=pd.DC_CODE
						order by DEH_DATE  desc";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
		}else {
			$cmd = " 	select 	b.DC_CODE, DC_NAME from PER_DECOR a, PER_DECORDTL b, PER_DECORATION c 
				  		where 	b.PER_ID=$TMP_PER_ID and DE_DATE < '$tmp_DE_DATE' and 
									a.DE_ID=b.DE_ID and b.DC_CODE=c.DC_CODE
						order by DE_DATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
		}
		//$db_dpis->show_error(); echo "<hr>";
		$TMP_DC_CODE_OLD = $data2[DC_CODE];		
		$TMP_DC_NAME_OLD = (trim($data2[DC_NAME]))? $data2[DC_NAME] : "-";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][dc_name] = $TMP_DC_NAME;
		$arr_content[$data_count][org_name] = $TMP_ORG_NAME;
		$arr_content[$data_count][per_gender] = $TMP_PER_GENDER;
		$arr_content[$data_count][per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][per_position] = $TMP_POSITION;
		$arr_content[$data_count][dc_name_old] = $TMP_DC_NAME_OLD;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

	$border = "";
	$pdf->SetFont($font,'',14);
	$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "$MINISTRY_TITLE ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$MINISTRY_NAME", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "$DEPARTMENT_TITLE ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$DEPARTMENT_NAME", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);
		
	$pdf->Cell(27, 7, "", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "ปี พ.ศ. ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$DE_YEAR", $border, 0, 'L', 0);
	$pdf->Cell(25, 7, "วันที่ได้รับ ", $border, 0, 'L', 0);
	$pdf->Cell(95, 7, "$DE_DATE", $border, 0, 'L', 0);
	$pdf->Cell(20, 7, "", $border, 1, 'L', 0);

	if($count_data){
		$pdf->AutoPageBreak = false;
		
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$DC_NAME = $arr_content[$data_count][dc_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$PER_GENDER = $arr_content[$data_count][per_gender];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_POSITION = $arr_content[$data_count][per_position];
			$DC_NAME_OLD = $arr_content[$data_count][dc_name_old];

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$DC_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[3], 7, "$PER_GENDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[4], 7, "$PER_NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[5], 7, "$PER_POSITION", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[6], 7, "$DC_NAME_OLD", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
			$pdf->y = $start_y;

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
			for($i=0; $i<=6; $i++){
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

	$pdf->Close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>