<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = trim($report_title);
	$report_code = "P0805";
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
	$heading_width[1] = "60";
	$heading_width[2] = "60";
	$heading_width[3] = "50";
	$heading_width[4] = "50";
	$heading_width[5] = "25";
	$heading_width[6] = "25";
	
	//new format*******************************************************
    $heading_text[0] = "ลำดับ";
	$heading_text[1] ="ชื่อ-สกุล";
	$heading_text[2] = "ตำแหน่ง / ระดับ";
	$heading_text[3] ="$ORG_TITLE";
	$heading_text[4] ="$ORG_TITLE1";
	$heading_text[5] = "ผลคัดเลือก";
	$heading_text[6] = 	"ผลการประเมิน";
	$heading_align = array('C','C','C','C','C','C','C');

	/*	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ-สกุล",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง / ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"$ORG_TITLE",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"$ORG_TITLE1",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ผลคัดเลือก",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ผลการประเมิน",'LTBR',1,'C',1);
	} // function		
		*/
	/* $cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd); */
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

  if(trim($search_code)) $arr_search_condition[] = "(a.PER_ID = $search_code)";
  	if(trim($search_per_id)) $arr_search_condition[] = "(a.PER_ID = $search_per_id)";
  	if(trim($search_result)) {
		$search_result_chk = $search_result - 1;
		$arr_search_condition[] = "(COD_RESULT = $search_result_chk)";	
	}
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = implode(" and ", $arr_search_condition);
	$search_condition = (trim($search_condition)? " and " : "") . $search_condition;

	if($DPISDB=="odbc"){
		$cmd = " select	 a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO 
						from 	PER_COURSEDTL a, PER_PERSONAL b
						where	CO_ID=$CO_ID and a.PER_ID=b.PER_ID
									$search_condition
								order by	b.PER_NAME, b.PER_SURNAME";			
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO  
							from 		PER_COURSEDTL a, PER_PERSONAL b
							where		CO_ID=$CO_ID and a.PER_ID=b.PER_ID
											$search_condition
											$limit_data		
							order by 		b.PER_NAME, b.PER_SURNAME 
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select	 a.PER_ID, COD_RESULT, COD_PASS, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, b.LEVEL_NO 
						from 	PER_COURSEDTL a, PER_PERSONAL b
						where	CO_ID=$CO_ID and a.PER_ID=b.PER_ID
									$search_condition
								order by	b.PER_NAME, b.PER_SURNAME";	
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

//new format************************************************************	
 	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
	$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "", "0066CC", "EEEEFF", 0);
	if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;		
			$num++;	

		$TMP_PER_ID = $data[PER_ID];
		$current_list .= ((trim($current_list))?",":"") . "$TMP_PER_ID";

		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$COD_RESULT = ($data[COD_RESULT]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
		$COD_PASS = ($data[COD_PASS]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);
		$LEVEL_NO = trim(level_no_format($data[LEVEL_NO]));
		
		
		/*	$$arr_fields[0] = $data[$arr_fields[0]];
			$$arr_fields[1] = ($data[$arr_fields[1]]==1)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg";
			$$arr_fields[4] = $data[$arr_fields[4]];
			$$arr_fields[5] = $data[$arr_fields[5]];
			$$arr_fields[5] = $PERSON_TYPE[$$arr_fields[5]];  */
			
		$PN_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "";
		$cmd = "select PN_NAME from PER_PRENAME where PN_CODE= '". $PN_CODE . "'";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PN_NAME = $data_dpis2[PN_NAME];
		$POS_NAME = $POS_TYPE = "";
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";	
		$db_dpis1 ->send_cmd($cmd);
		$data_level = $db_dpis1->get_array();
		$LEVEL_NAME = $data_level[LEVEL_NAME];	
		$POSITION_LEVEL = $data_level[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		if ($POS_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, PL_CODE, PT_CODE
							from 	PER_POSITION 
							where 	POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_CODE = trim($data_dpis2[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' "; 
			$db_dpis1 ->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_NAME = $data1[PL_NAME];
			
			$PT_CODE = trim($data_dpis2[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' "; 
			$db_dpis1 ->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_TYPE = ($PT_CODE == "11")? "" : $data1[PT_NAME];
				
							
		} elseif ($POEM_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							from 	PER_POS_EMP
							where 	POEM_ID=$POEM_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			//$db_dpis2->show_error();
			//echo "<hr>$cmd";
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
				$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$PN_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[PN_NAME];
							
		} elseif ($POEMS_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							from 	PER_POS_EMPSER 
							where 	POEMS_ID=$POEMS_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$EP_CODE = trim($data_dpis2[EP_CODE]);
				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[EP_NAME];

		}	elseif ($POT_ID) {
			$cmd_dpis2 = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2, TP_CODE 
							from 	PER_POS_TEMP 
							where 	POT_ID=$POT_ID  ";
			$db_dpis2->send_cmd($cmd_dpis2);
			$data_dpis2 = $db_dpis2->get_array();
			$TP_CODE = trim($data_dpis2[TP_CODE]);
				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' "; 
				$db_dpis1 ->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POS_NAME = $data1[TP_NAME];

		}			
		$POS_NAME = ($POSITION_LEVEL)? "$POS_NAME$POSITION_LEVEL" : "$POS_NAME";
		
		$ORG_ID = (trim($data_dpis2[ORG_ID]))? trim($data_dpis2[ORG_ID]) : 0;
		$ORG_ID_1 = (trim($data_dpis2[ORG_ID_1]))? trim($data_dpis2[ORG_ID_1]) : 0;
		$ORG_ID_2 = (trim($data_dpis2[ORG_ID_2]))? trim($data_dpis2[ORG_ID_2]) : 0;		
		$ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = "-";
		$cmd = "	select 	ORG_ID, ORG_NAME
				from		PER_ORG 
				where	ORG_ID IN ( $ORG_ID, $ORG_ID_1, $ORG_ID_2 )";
		$db_dpis2->send_cmd($cmd);
		while ( $data_dpis2 = $db_dpis2->get_array() )  {
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID )				$ORG_NAME = trim( $data_dpis2[ORG_NAME] );
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID_1 )			$ORG_NAME_1 = trim( $data_dpis2[ORG_NAME] );
			if ( trim($data_dpis2[ORG_ID]) == $ORG_ID_2 )			$ORG_NAME_2 = trim( $data_dpis2[ORG_NAME] );										
		}

			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$pdf->Cell($heading_width[0], 7, $num, $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$PN_NAME$PER_NAME $PER_SURNAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, $POS_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7,$ORG_NAME, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[4], 7, $ORG_NAME_1, $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4];
			$pdf->y = $start_y;
			$pdf->Image($COD_RESULT,($pdf->x + ($heading_width[5] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5];
			$pdf->Image($COD_PASS,($pdf->x + ($heading_width[6] / 2) - 1.5), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];

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
				if($data_count < $count_data){
					$pdf->AddPage();
			//		print_header();
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