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
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = show_date_format($data[COM_DATE], $DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);
	
	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$select_position = "c.POS_ID,c.POS_NO, c.PT_CODE, c.PL_CODE, c.PM_CODE";
		$column_count=10;
		$type_name="�Թ��͹����Ҫ���";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$select_position = "c.POEM_ID,c.POEM_NO,c.PN_CODE";
		$column_count=8;
		$type_name="��Ҩ�ҧ�١��ҧ��Ш�";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$select_position = "c.POEMS_ID,c.POEMS_NO,c.EP_CODE";
		$column_count=8;
		$type_name="��Ҩ�ҧ��ѡ�ҹ�Ҫ���";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$select_position = "c.POT_ID,c.POT_NO,c.TP_CODE";
		$column_count=8;
		$type_name="��Ҩ�ҧ�١��ҧ���Ǥ���";
	} // end if
	$today = date('d')+ 0 ." ".$month_abbr[(date('m') + 0)][TH] ." ". (date('Y') + 543);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";

	$report_title = "�ѭ����������´�������͹���$type_name || � �ѹ��� $today ||Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
	$report_code = "R20001";
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
	$heading_width[3] = "70";
	$heading_width[4] = "20";
	$heading_width[5] = "15";
	$heading_width[6] = "15";
	if($COM_PER_TYPE == 1){	
		$heading_width[7] = "15";
		$heading_width[8] = "15";
		$heading_width[9] = "20";
		$heading_width[10] = "35";
	}else{
		$heading_width[7] = "20";
		$heading_width[8] = "35";
	}
	
	function print_header(){
		global $pdf, $heading_width;
		global $COM_PER_TYPE;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"�ӴѺ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"�Ţ��Шӵ��",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"����-���ʡ��",'LTR',0,'C',1);
		if($COM_PER_TYPE == 1){
			$pdf->Cell($heading_width[3] ,7,"���˹� �����ǹ�Ҫ���",'LTR',0,'C',1);
		}else{
			$pdf->Cell($heading_width[3] ,7,"���˹�/�ѧ�Ѵ",'LTR',0,'C',1);
		}
		$pdf->Cell($heading_width[4] ,7,"�Ţ���",'LTR',0,'C',1);
		if($COM_PER_TYPE == 1){
			$pdf->Cell(($heading_width[5] + $heading_width[6]) ,7,"�Թ��͹��͹����͹",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[7] + $heading_width[8]) ,7,"������Ѻ�Թ��͹",'LTR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"�Թ",'LTR',0,'C',1);
			$pdf->Cell($heading_width[10] ,7,"�����˵�",'LTR',1,'C',1);
		}else{
			$pdf->Cell(($heading_width[5] + $heading_width[6]) ,7,"�ѵ�Ҥ�Ҩ�ҧ (�ҷ)",'LTR',0,'C',1);
			$pdf->Cell($heading_width[7],7,"�Թ����͹���",'LTR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"�����˵�",'LTR',1,'C',1);
		}

		$pdf->Cell($heading_width[0] ,7,"���",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"��ЪҪ�",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		if($COM_PER_TYPE == 1){
			$pdf->Cell($heading_width[3] ,7,"�ѧ�Ѵ/���˹�",'LBR',0,'C',1);
		}else{
			$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		}
		$pdf->Cell($heading_width[4] ,7,"���˹�",'LBR',0,'C',1);
		if($COM_PER_TYPE == 1){
			$pdf->Cell($heading_width[5] ,7,"�ѹ�Ѻ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"�ѹ�Ѻ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"���",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"����͹���",'LBR',0,'C',1);
			$pdf->Cell($heading_width[10] ,7,"",'LBR',1,'C',1);
		}else{
			$pdf->Cell($heading_width[5] ,7,"��͹����͹",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"������Ѻ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
				   from			(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
							a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
							a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
			   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e
			   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+)
			   order by 		d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO
				";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
				   from		(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO
				";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		$arr_temp = explode("\|", $CMD_POSITION);
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
		$CMD_POSITION_M = $arr_temp[2];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$UP_SALARY = ($CMD_SALARY - $CMD_OLD_SALARY);

		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		if($PER_TYPE==1){		
			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO];
		
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "�����" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

		}elseif($PER_TYPE==2){	
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POEM_ID = $data[POEM_ID];
			$POS_NO = $data[POEM_NO];
			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select	 PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";
		}elseif($PER_TYPE==3){	
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POEMS_ID = $data[POEMS_ID];
			$POS_NO = $data[POEMS_NO];
			
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select	 EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
		}elseif($PER_TYPE==4){	
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POT_ID = $data[POT_ID];
			$POS_NO = $data[POT_NO];
			
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select	 TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
	} // end if
		if(!$CMD_POS_NO){	$CMD_POS_NO=$POS_NO;		}

		if($CMD_ORG3 != trim($data[CMD_ORG3])){
			$CMD_ORG3 = trim($data[CMD_ORG3]);
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $CMD_ORG3;
			$data_count++;
		} // end if
		
		if($CMD_ORG4 != trim($data[CMD_ORG4])){
			$CMD_ORG4 = trim($data[CMD_ORG4]);
			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $CMD_ORG4;
			$data_count++;
		} // end if

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";
		$arr_content[$data_count][cmd_position] = (trim($CMD_POSITION_M)?"$CMD_POSITION_M\n(":"") . $CMD_POSITION . (trim($CMD_POSITION_M)?")":"");
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		if($PER_TYPE==1){
			$arr_content[$data_count][cmd_level] = "�." . level_no_format($CMD_LEVEL);
		}
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		$arr_content[$data_count][up_salary] = $UP_SALARY?number_format($UP_SALARY):"-";	//�Թ����͹���
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
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
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			if($COM_PER_TYPE==1){
				$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			}
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$UP_SALARY = $arr_content[$data_count][up_salary];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
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
				if($COM_PER_TYPE==1){
					$pdf->Cell($heading_width[9], 7, "", $border, 0, 'L', 0);
					$pdf->Cell($heading_width[10], 7, "", $border, 0, 'L', 0);
				}
				
				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				
				for($i=0; $i<=$column_count; $i++){
					if($i < 2){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					}elseif($i==2){
						$line_start_y = $start_y;		$line_start_x += ($heading_width[2] + $heading_width[3]);
						$line_end_y = $max_y;		$line_end_x += ($heading_width[2] + $heading_width[3]);
					}elseif($i > 3){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					} // end if

					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================

				if(($pdf->h - $max_y) < 21){ 
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
				
				if($COM_PER_TYPE==1){
					$pdf->Cell($heading_width[5], 7, "$CMD_LEVEL", $border, 0, 'R', 0);
					$pdf->Cell($heading_width[6], 7, "$CMD_OLD_SALARY", $border, 0, 'C', 0);
					$pdf->Cell($heading_width[7], 7, "$CMD_LEVEL", $border, 0, 'R', 0);
					$pdf->MultiCell($heading_width[8], 7, "$CMD_SALARY", $border, "R");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
					$pdf->y = $start_y;
					$pdf->Cell($heading_width[9], 7, "$UP_SALARY", $border, 0, 'R', 0);
					$pdf->MultiCell($heading_width[10], 7, "$CMD_NOTE1", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
					$pdf->y = $start_y;
				}else{
					$pdf->Cell($heading_width[5], 7, "$CMD_OLD_SALARY", $border, 0, 'C', 0);
					$pdf->Cell($heading_width[6], 7, "$CMD_SALARY", $border, 0, 'C', 0);
					$pdf->Cell($heading_width[7], 7, "$UP_SALARY", $border, 0, 'R', 0);
					$pdf->MultiCell($heading_width[8], 7, "$CMD_NOTE1", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8];
					$pdf->y = $start_y;
				}

				//================= Draw Border Line ====================
				$line_start_y = $start_y;		$line_start_x = $start_x;
				$line_end_y = $max_y;		$line_end_x = $start_x;
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
				for($i=0; $i<=$column_count; $i++){
					$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
					$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
				} // end for
				//====================================================
	
				if($CMD_NOTE2){
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$pdf->MultiCell(array_sum($heading_width), 7, "�����˵� : $CMD_NOTE2", "LR", "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + array_sum($heading_width);
					$pdf->y = $start_y;
				} // end if
	
	//			if(($pdf->h - $max_y - 10) < 22){ 
				if(($pdf->h - $max_y) < 21){ 
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
			$pdf->Cell(15, 7, "�����˵� : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ����բ����� **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>