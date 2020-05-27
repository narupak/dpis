<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
				echo $cmd;
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,3);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการ$COM_DESC แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
	
	$report_code = "P0502";
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
	$heading_width[1] = "60";
	$heading_width[2] = "55";
	$heading_width[3] = "25";
	$heading_width[4] = "17";
	$heading_width[5] = "13";
	$heading_width[6] = "17";
	$heading_width[7] = "20";
	$heading_width[8] = "30";
	if(in_array($COM_TYPE, array("1702", "1703"))){
		$heading_width[9] = "30";
	}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){	//1707
		$heading_width[9] = "85";
		$heading_width[10] = "85";
	}else if($COM_TYPE=="1706"){
		$heading_width[9] = "25";
		$heading_width[10] = "25";
		$heading_width[11] = "85";
	}
	/*
		//new format*****************************************
	$heading_text[0] = "ลำดับ|ที่";
	$heading_text[1] = "ชื่อ-นามสกุล|";
if($COM_TYPE=="1702"){	
	$heading_text[2] = "<**1**>ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ|ตำแหน่ง/สังกัด";
	$heading_text[3] = "<**1**>ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ|ตำแหน่งประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ|เลขที่";
	$heading_text[6] = "<**1**>ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ|เงินเดือน";
	$heading_text[7] ="ทดลอง|ตั้งแต่วันที่";
	$heading_text[8] = "ให้ออก|ตั้งแต่วันที่";
	$heading_text[9] = "หมายเหตุ";
	}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){	//1707
	$heading_text[2] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่ง/สังกัด";
	$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่งประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการ|เลขที่";
	$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการ|เงินเดือน";
	$heading_text[7] ="ไปปฏิบัติ|หน้าที่";
	$heading_text[8] = "มีกำหนด|เวลา";
	$heading_text[9] = "ให้ออก|ตั้งแต่วันที่";
	$heading_text[10] = "หมายเหตุ";
	}elseif($COM_TYPE=="1703"){
	$heading_text[2] = "วุฒิ/สาขา/สถานศึกษา";
	$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่งประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการ|เลขที่";
	$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการ|เงินเดือน";
	$heading_text[7] ="<**1**>ตำแหน่งและส่วนราชการ|ตั้งแต่วันที่";
	$heading_text[8] = "ให้ออก|ตั้งแต่วันที่";
	$heading_text[9] = "หมายเหตุ";
	}else if($COM_TYPE=="1706"){
	$heading_text[2] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่ง/สังกัด";
	$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่งประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการ|เลขที่";
	$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการ|เงินเดือน";
	$heading_text[7] ="ไปปฏิบัติ|หน้าที่";
	$heading_text[8] = "มีกำหนด|เวลา";
	$heading_text[9] = "ได้รับเงินเดือน|ระหว่างปฏิบัติงานจาก";
	$heading_text[10] = "ไปปฏิบัติงาน|ตั้งแต่วันที่";
	$heading_text[11] = "หมายเหตุ";	
	}else{	//1701,1705
	$heading_text[2] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่ง/สังกัด";
	$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่งประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการ|เลขที่";
	$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการ|เงินเดือน";
	$heading_name6="ออก";
	if($COM_TYPE=="1701"){	
	$heading_name6="ลาออก";
	}
	$heading_text[7] ="ให้$heading_name6|ตั้งแต่วันที่";
	$heading_text[8] = "หมายเหตุ";
}
	
	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C');
	*/	
	
	function print_header(){
		global $pdf, $heading_width;
		
		$pdf->SetFont($font,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		$pdf->Cell(100,2,"",0,1,'C');

		$pdf->Cell($heading_width[0] ,7,"ลำดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ/นามสกุล",'LTR',0,'C',1);
		if($COM_TYPE=="1702"){
			$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]+$heading_width[6]) ,7,"ตำแหน่งที่ทดลองปฏิบัติหน้าที่ราชการ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"ทดลอง",'LTR',1,'C',1);
			$pdf->Cell($heading_width[8] ,7,"ให้ออก",'LTR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){	//1706
			$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]) ,7,"ตำแหน่งและส่วนราชการ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"ไปปฏิบัติ",'LTR',1,'C',1);
			$pdf->Cell($heading_width[8] ,7,"มีกำหนด",'LTR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"ให้ออก",'LTR',1,'C',1);
			$pdf->Cell($heading_width[10] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}elseif($COM_TYPE=="1703"){
			$pdf->Cell($heading_width[2] ,7,"วุฒิ/สาขา/สถานศึกษา",'LTR',0,'C',1);
			$pdf->Cell(($heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]+$heading_width[7]) ,7,"ตำแหน่งและส่วนราชการ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"ให้ออก",'LTR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}else if($COM_TYPE=="1706"){
			$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5]) ,7,"ตำแหน่งและส่วนราชการ",'LTBR',0,'C',1);
			$pdf->Cell($heading_width[6] ,7,"เงินเดือน",'LTR',0,'C',1);
			$pdf->Cell($heading_width[7] ,7,"ไปปฏิบัติ",'LTR',1,'C',1);
			$pdf->Cell($heading_width[8] ,7,"มีกำหนด",'LTR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"ได้รับเงินเดือน",'LTR',1,'C',1);
			$pdf->Cell($heading_width[10] ,7,"ไปปฏิบัติงาน",'LTR',1,'C',1);
			$pdf->Cell($heading_width[11] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}else{	//1701,1705
			$pdf->Cell(($heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6]) ,7,"ตำแหน่งและส่วนราชการ",'LTBR',0,'C',1);
			$heading_name6="ออก";
			if($COM_TYPE=="1701"){	$heading_name6="ลาออก";		}
			$pdf->Cell($heading_width[7] ,7,"ให้$heading_name6",'LTR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"หมายเหตุ",'LTR',1,'C',1);
		}

		$pdf->Cell($heading_width[0] ,7,"ที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง/สังกัด",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ตำแหน่งประเภท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ระดับ",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"เลขที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"เงินเดือน",'LTBR',0,'C',1);
		if(in_array($COM_TYPE, array("1702", "1703"))){
			$pdf->Cell($heading_width[7] ,7,"ตั้งแต่วันที่",'LBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"ตั้งแต่วันที่",'LBR',1,'C',1);
			$pdf->Cell($heading_width[9] ,7,"",'LBR',1,'C',1);
		}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){	//1707
			$pdf->Cell($heading_width[7] ,7,"หน้าที่",'LBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"เวลา",'LBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"ตั้งแต่วันที่",'LBR',1,'C',1);
			$pdf->Cell($heading_width[10] ,7,"",'LBR',1,'C',1);
		}else if($COM_TYPE=="1706"){
			$pdf->Cell($heading_width[7] ,7,"หน้าที่",'LBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"เวลา",'LBR',0,'C',1);
			$pdf->Cell($heading_width[9] ,7,"ระหว่างปฏิบัติงานจาก",'LBR',1,'C',1);
			$pdf->Cell($heading_width[10] ,7,"ตั้งแต่วันที่",'LBR',1,'C',1);
			$pdf->Cell($heading_width[11] ,7,"",'LBR',1,'C',1);
		}else{
			$pdf->Cell($heading_width[7] ,7,"ตั้งแต่วันที่",'LBR',0,'C',1);
			$pdf->Cell($heading_width[8] ,7,"",'LBR',1,'C',1);
		}
	} // function		
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
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
		$PER_MEMBER = $data[PER_MEMBER];

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		if($DPISDB=="mysql")	{
			$tmp_data = explode("|", trim($data[CMD_POSITION]));
		}else{
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
		}
		//ในกรณีที่มี CMD_PM_NAME
		if(is_array($tmp_data)){
			$CMD_POSITION = $tmp_data[0];
			$CMD_PM_NAME = $tmp_data[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}		
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		//--ตำแหน่งและส่วนราชการเดิม
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		//echo $cmd;
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		if($PER_TYPE==1){
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

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION. (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
//		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";	
		$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"") ."\n". ($INS_NAME?"$INS_NAME":"");
		
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION ."\n". ($CMD_ORG3?"$CMD_ORG3":"");
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1 . ($CMD_NOTE2?("\n".$CMD_NOTE2):"");
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME = $arr_content[$data_count][cmd_level_name];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
			
			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'C', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			if($COM_TYPE=="1703"){
				$pdf->MultiCell($heading_width[2], 7, "$EDUCATE", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "$CMD_POSITION", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$CMD_POSITION_TYPE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_LEVEL_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[6], 7, "$CMD_POS_NO_NAME"."$CMD_POS_NO", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[7], 7, "$CMD_OLD_SALARY", $border, "R");
				$pdf->Cell($heading_width[8], 7, "$CMD_DATE", $border, "L");
				$pdf->MultiCell($heading_width[9], 7, "$CMD_NOTE1", $border, 0, 'C', 0);
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8]+ $heading_width[9];
			}else{
				$pdf->MultiCell($heading_width[2], 7, "$CMD_POSITION", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
				$pdf->y = $start_y;
				$pdf->Cell($heading_width[3], 7, "$CMD_POSITION_TYPE", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[4], 7, "$CMD_LEVEL_NAME", $border, 0, 'C', 0);
				$pdf->Cell($heading_width[5], 7, "$CMD_POS_NO_NAME"."$CMD_POS_NO", $border, 0, 'C', 0);
				$pdf->MultiCell($heading_width[6], 7, "$CMD_OLD_SALARY", $border, "R");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6];
				$pdf->y = $start_y;
				if($COM_TYPE=="1702"){
						$pdf->Cell($heading_width[7], 7, "วันที่ทดลอง", $border, 0, 'C', 0);
						$pdf->Cell($heading_width[8], 7, "$CMD_DATE", $border, "L");
						$pdf->MultiCell($heading_width[9], 7, "$CMD_NOTE1", $border, 0, 'C', 0);
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9];
				}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){	//1707
						$pdf->Cell($heading_width[7], 7, "หน้าที่ที่ปฏิบัติ", $border, 0, 'C', 0);
						$pdf->Cell($heading_width[8], 7, "เวลากำหนด", $border, "L");
						$pdf->Cell($heading_width[9], 7, "$CMD_DATE ให้ออกวันที่", $border, 0, 'C', 0);
						$pdf->MultiCell($heading_width[10], 7, "$CMD_NOTE1", $border, 0, 'C', 0);
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10];
				}else if($COM_TYPE=="1706"){
						$pdf->Cell($heading_width[7], 7, "หน้าที่ที่ปฏิบัติ", $border, 0, 'C', 0);
						$pdf->Cell($heading_width[8], 7, "เวลากำหนด", $border, "L");
						$pdf->Cell($heading_width[9], 7, "เงินเดือนระหว่างปฏิบัติงาน", $border, 0, 'C', 0);
						$pdf->Cell($heading_width[10], 7, "$CMD_DATE ให้ออกวันที่", $border, 0, 'C', 0);
						$pdf->MultiCell($heading_width[11], 7, "$CMD_NOTE1", $border, 0, 'C', 0);
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11];
				}else{	//1701,1705
						$pdf->Cell($heading_width[7], 7, "$CMD_DATE", $border, 0, 'C', 0);
						$pdf->MultiCell($heading_width[8], 7, "$CMD_NOTE1", $border, "L");
						if($pdf->y > $max_y) $max_y = $pdf->y;
						$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]+ $heading_width[8];
				}
			}
			$pdf->y = $start_y;
			
			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			/*$n=7;
			if(in_array($COM_TYPE, array("1702", "1703"))){	
				$n=8;
			}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){
				$n=9;
			}
			for($i=0; $i<=$n; $i++){*/
			for($i=0; $i<=count($heading_width); $i++){
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

//			if(($pdf->h - $max_y - 10) < 22){ 
			if(($pdf->h - $max_y) < 55){ 
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
		
		if($COM_NOTE){
			$border = "";
			$pdf->SetFont($fontb,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(15, 7, "หมายเหตุ : ", $border, 0, 'L', 0);
	
			$border = "";
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->MultiCell(272, 7, "$COM_NOTE", $border, "L");
		} // end if
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>