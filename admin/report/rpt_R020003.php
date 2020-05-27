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

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(psh.MOV_CODE in('21420','21430'))";	//เงินตอบแทนร้อยละ 2 และร้อยละ 4
	//ค้นหา ณ วันที
	if(trim($search_end_date)){
		$arr_temp = explode("/", $search_end_date);
		$search_date = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date = ($arr_temp[0] + 0) ." ". $month_abbr[($arr_temp[1] + 0)][TH] ." ". ($arr_temp[2] + 0);
	}
	if(trim($search_date)){
		if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_date')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(SAH_EFFECTIVEDATE, 1, 10) <= '$search_date')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(SAH_EFFECTIVEDATE, 10) <= '$search_date')";
	}

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$select_position = "c.POS_ID,c.POS_NO, c.PT_CODE, c.PL_CODE, c.PM_CODE";
	}else if($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$select_position = "c.POEM_ID,c.POEM_NO,c.PN_CODE";
	}else if($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$select_position = "c.POEMS_ID,c.POEMS_NO,c.EP_CODE";
	}else if($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$select_position = "c.POT_ID,c.POT_NO,c.TP_CODE";
	} // end if

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	
	//เซตค่า
	$COM_DATE = $show_date;
	
	$report_title = "บัญชีรายละเอียดเงินตอบแทนพิเศษ$PERSON_TYPE[$search_per_type] || ณ วันที่ $show_date ||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
	$report_code = "R20003";
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
	
	$heading_width[0] = "10";
	$heading_width[1] = "25";
	$heading_width[2] = "50";
	$heading_width[3] = "75";
	$heading_width[4] = "20";
	$heading_width[5] = "35";
	$heading_width[6] = "22";
	$heading_width[7] = "22";
	$heading_width[8] = "30";
	
	function print_header(){
		global $pdf, $heading_width;
		global $search_per_type;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"เลขประจำตัว",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ชื่อ-นามสกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่ง/สังกัด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เลขที่",'LTR',0,'C',1);
		if($search_per_type == 1){	$heading_name5="เงินเดือน";	 }else{		$heading_name5="ค่าจ้าง";	}
		$pdf->Cell($heading_width[5] ,7,"อัตรา$heading_name5",'LTR',0,'C',1);
		$pdf->Cell(($heading_width[6] + $heading_width[7]) ,7,"เงินค่าตอบแทน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"หมายเหตุ",'LTR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ประชาชน",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ตำแหน่ง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ที่เต็มขั้น",'LBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ร้อยละ 2 (บาท)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"ร้อยละ 4 (บาท)",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
	} // function		
	
	//หาเงินตอบแทนพิเศษ
	function	find_income_salary($income_type,$PER_ID){
		global $DPISDB, $db_dpis2;
	
		$sum_income_salary = 0;
	
		if($DPISDB=="odbc"){
			$cmd = "select 			SUM(SAH_SALARY)	as SUM_INCOME_SALARY
							from 			PER_SALARYHIS psh
							where 		psh.PER_ID=$PER_ID and psh.MOV_CODE=$income_type
							order by 		SAH_ID, SAH_EFFECTIVEDATE,SAH_ENDDATE,MOV_CODE,SAH_SALARY
							";
		}elseif($DPISDB=="oci8"){
			$cmd = "select 			SUM(SAH_SALARY)	as SUM_INCOME_SALARY
							from 			PER_SALARYHIS psh
							where 		psh.PER_ID=$PER_ID and psh.MOV_CODE=$income_type
							order by 		SAH_ID, SAH_EFFECTIVEDATE,SAH_ENDDATE,MOV_CODE,SAH_SALARY
							";
		}elseif($DPISDB=="mysql"){
			$cmd = "select 			SUM(SAH_SALARY)	as SUM_INCOME_SALARY
							from 			PER_SALARYHIS psh
							where 		psh.PER_ID=$PER_ID and psh.MOV_CODE=$income_type
							order by 		SAH_ID, SAH_EFFECTIVEDATE,SAH_ENDDATE,MOV_CODE,SAH_SALARY
							";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
//		echo "<br>$cmd<br>";
		$data = $db_dpis2->get_array();
		if($data[SUM_INCOME_SALARY] > 0)		$sum_income_salary  = 	$data[SUM_INCOME_SALARY];
					
	return $sum_income_salary;
	}	
	
	//เริ่มแรก หารายชื่อคนที่ตรงตามเงื่อนไข ได้รับเงินตอบแทนพิเศษร้อยละ 2 และ 4
	//ดึงข้อมูลบุคคลมาเพื่อแสดง
	if($DPISDB=="odbc"){
		$cmd = "select 			distinct psh.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						from 			PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_SALARYHIS psh, PER_MOVMENT pm
						where 		b.PER_ID=psh.PER_ID and $position_join and psh.MOV_CODE=pm.MOV_CODE and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID
											$search_condition
						order by 		psh.PER_ID,b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select 			distinct psh.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						from 			PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_SALARYHIS psh, PER_MOVMENT pm
						where 		b.PER_ID=psh.PER_ID and $position_join(+) and psh.MOV_CODE=pm.MOV_CODE and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+)  
											$search_condition
						order by 		psh.PER_ID,b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						";
	}elseif($DPISDB=="mysql"){
		$cmd = "select 			distinct psh.PER_ID,b.PN_CODE as PREN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						from 			PER_PERSONAL b,$position_table c,PER_ORG d, PER_ORG e,PER_SALARYHIS psh, PER_MOVMENT pm
						where 		b.PER_ID=psh.PER_ID and $position_join and psh.MOV_CODE=pm.MOV_CODE and c.ORG_ID=d.ORG_ID and c.ORG_ID_1=e.ORG_ID
											$search_condition
						order by 		psh.PER_ID,b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,b.PER_SALARY,
											c.ORG_ID,c.ORG_ID_1,c.ORG_ID_2,c.DEPARTMENT_ID, $select_position
						";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "<br>$cmd<br>";
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];

		$PREN_CODE = trim($data[PREN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PREN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];

		$LEVEL_NO = trim($data[LEVEL_NO]);
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
	
		$PER_SALARY = $data[PER_SALARY];

		//หาเงินตอบแทนพิเศษของแต่ละคน
		$SAH_SALARY2[$PER_ID] = find_income_salary('21420',$PER_ID);	//ร้อยละ 2
		$SAH_SALARY4[$PER_ID] = find_income_salary('21430',$PER_ID);	//ร้อยละ 4

		$CMD_NOTE1 = "";

		//หาข้อมูลตำแหน่ง
		if($PER_TYPE==1){		
			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO];
		
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];		
			
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];		
			
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_NAME = $data2[PM_NAME];		
	
			if ($RPT_N)
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$POSITION_LEVEL" : "") . (trim($PM_NAME) ?")":"");
			else
				$CMD_POSITION = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
		}elseif($PER_TYPE==2){		
			$POEM_ID = $data[POEM_ID];
			$POS_NO = $data[POEM_NO];
			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select	 PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		}elseif($PER_TYPE==3){	
			$POEMS_ID = $data[POEMS_ID];
			$POS_NO = $data[POEMS_NO];
			
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select	 EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME$POSITION_LEVEL" : "";	
		} // end if

		if($CMD_ORG3 != trim($data[ORG_ID])){
			$CMD_ORG3 = trim($data[ORG_ID]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$CMD_ORG3 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
		
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $ORG_NAME;
			$data_count++;
		} // end if

			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][order] = $data_row;
			$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";
			$arr_content[$data_count][cmd_position] = $CMD_POSITION;
			$arr_content[$data_count][cmd_pos_no] = $POS_NO;
			$arr_content[$data_count][cmd_salary] = $PER_SALARY?number_format($PER_SALARY):"-";
		
			$arr_content[$data_count][cmd_extra2] = $SAH_SALARY2[$PER_ID]?number_format($SAH_SALARY2[$PER_ID]):"-";	//เงิน ร้อยละ 2
			$arr_content[$data_count][cmd_extra4] = $SAH_SALARY4[$PER_ID]?number_format($SAH_SALARY4[$PER_ID]):"-";	//เงิน ร้อยละ 4
			$arr_content[$data_count][cmd_note1]  = $CMD_NOTE1;
			//$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

	$data_count++;
} // end while

//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CARDNO = $arr_content[$data_count][cardno];
			$NAME = $arr_content[$data_count][name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_EXTRA2 = $arr_content[$data_count][cmd_extra2];
			$CMD_EXTRA4 = $arr_content[$data_count][cmd_extra4];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			//$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
				
				$border = "";
				if($CONTENT_TYPE == "ORG") $pdf->SetFont($font,'b',14);
				else $pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2]), 7, "", $border, 0, 'L', 0);
				$pdf->MultiCell($heading_width[3], 7, "$ORG_NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[5], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[6], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[7], 7, "", $border, 0, 'L', 0);
				$pdf->Cell($heading_width[8], 7, "", $border, 0, 'L', 0);
				
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=8; $i++){
					if($i < 3){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					}elseif($i==3){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[3] + $heading_width[4]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[3] + $heading_width[4]);
					}elseif($i > 4){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					} // end if

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y - 10) < 21){ 
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
			}elseif($CONTENT_TYPE=="CONTENT"){
				$border = "";
				$pdf->SetFont($font,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	
				$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
				
				$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[1], 7, "$CARDNO", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[2], 7, "$NAME", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->MultiCell($heading_width[3], 7, "$CMD_POSITION", $border,"L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[4], 7, "$CMD_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_SALARY", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[6], 7, ($CMD_EXTRA2?$CMD_EXTRA2:""), $border, 0, 'R', 0);
				$pdf->Cell($heading_width[7], 7, ($CMD_EXTRA4?$CMD_EXTRA4:""), $border, 0, 'R', 0);
				$pdf->MultiCell($heading_width[8], 7, "$CMD_NOTE1", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
				$pdf->y = $start_y;

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=8; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "หมายเหตุ : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
	
				if(($pdf->h - $max_y - 10) < 21){ 
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
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$border = "";
			$pdf->SetFont($font,'b',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "หมายเหตุ : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>