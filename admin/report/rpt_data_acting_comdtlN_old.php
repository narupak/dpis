<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", 1800);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = show_date_format($data[COM_DATE],$DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data [COM_DESC]);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	
	$report_title = "บัญชีแนบท้ายคำสั่ง$COM_DESC || แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ ".($COM_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NO):$COM_NO):"-")." ลงวันที่ ". ($COM_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_DATE):$COM_DATE):"-");  
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
	
	$heading_width[0] = "10";
	$heading_width[1] = "35";
	$heading_width[2] = "35";
	$heading_width[3] = "17";
	$heading_width[4] = "20";
	$heading_width[5] = "15";
	$heading_width[6] = "35";
	$heading_width[7] = "17";
	$heading_width[8] = "20";
	$heading_width[9] = "15";
	$heading_width[10] = "20";
	if($COM_TYPE == "5090" ||"1309"){
		$heading_width[11] = "20";
		$heading_width[12] = "30";
	}elseif($COM_TYPE == "5100" ||"1400")
		$heading_width[11] = "30";
	//new format*****************************************
	if($COM_TYPE == "5090" ||"1309") $heading_name4="รักษาราชการแทน";
		elseif($COM_TYPE == "5100" ||"1400") $heading_name4="รักษาการในตำแหน่ง";
	$heading_text[0] = "ลำดับ|ที่";
	$heading_text[1] = "ชื่อ-นามสกุล|";
	$heading_text[2] =  "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่ง/สังกัด";
	$heading_text[3] =  "<**1**>ตำแหน่งและส่วนราชการ|ตำแหน่ง|ประเภท";
	$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการ|ระดับ";
	$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการ|เลขที่";
	$heading_text[6] =  "<**2**>$heading_name4|ตำแหน่ง/สังกัด";
	$heading_text[7] =  "<**2**>$heading_name4|ตำแหน่ง|ประเภท";
	$heading_text[8] =  "<**2**>$heading_name4|ระดับ";
	$heading_text[9] = "<**2**>$heading_name4|เลขที่";
	$heading_text[10] = "ตั้งแต่วันที่|";
	
	if($COM_TYPE == "5090"||"1300"){
	$heading_text[11] = "ถึงวันที่";
	$heading_text[12] = "หมายเหตุ";
	}elseif($COM_TYPE == "5100"||"1400")
	$heading_text[11] =  "หมายเหตุ";	
	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C');

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POT_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE,b.PER_RETIREDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd;
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PRENAME_CODE = trim($data[PRENAME_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRENAME_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_RETIREDATE = 	show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 

		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_DATE2 = show_date_format($data[CMD_DATE2],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		//--ตำแหน่งและส่วนราชการเดิม
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		//--ตำแหน่งและส่วนราชการที่ย้าย
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		if($PER_TYPE==1){
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME]."\n".$NEW_LEVEL_NAME;
			
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
//		$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?(" ".$NEW_PL_NAME ." ". $NEW_LEVEL_NAME. (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?(" ".$NEW_PL_NAME.(($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO_NAME, a.POEM_NO, b.PN_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		}elseif($PER_TYPE==3){
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO_NAME, a.POEMS_NO, b.EP_NAME, c.ORG_NAME
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		} elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO_NAME, a.POT_NO, b.TP_NAME, c.ORG_NAME
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POT_NO_NAME]);
			$NEW_POS_NO = trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_position_level] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_org3] = $CMD_ORG3;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		$arr_content[$data_count][retire_date] = $PER_RETIREDATE;
	
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_position_level] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no_name] = $NEW_POS_NO_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_date2] = $CMD_DATE2;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;

		$data_count++;

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][cmd_position] = $CMD_ORG3;
		$arr_content[$data_count][new_position] = $NEW_ORG_NAME;
		$arr_content[$data_count][card_no] =card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);

		$data_count++;

		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_position_level];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$PER_RETIREDATE = $arr_content[$data_count][retire_date];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_POSITION_LEVEL = $arr_content[$data_count][new_position_level];
			$NEW_POS_NO_NAME = $arr_content[$data_count][new_pos_no_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_DATE2 = $arr_content[$data_count][cmd_date2];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$PER_CARDNO = $arr_content[$data_count][card_no];

			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER);
			$arr_data[] ="$NAME";
			$arr_data[] ="$CMD_POSITION";
			$arr_data[] ="$CMD_POSITION_TYPE";
			$arr_data[] ="$CMD_LEVEL_NAME";
			$arr_data[] ="$CMD_POS_NO_NAME".(($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_POS_NO):$CMD_POS_NO);
			$arr_data[] ="$NEW_POSITION";
			$arr_data[] ="$NEW_POSITION_TYPE";
			$arr_data[] ="$NEW_POSITION_LEVEL";
			$arr_data[] ="$NEW_POS_NO_NAME".(($NUMBER_DISPLAY==2)?convert2thaidigit($NEW_POS_NO):$NEW_POS_NO);
			$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_DATE):$CMD_DATE);
			if($COM_TYPE == "5090"||"1300"){
				$column = 11;
				$arr_data[] = (($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_DATE2):$CMD_DATE2);
				$arr_data[] ="$CMD_NOTE1";
			}elseif($COM_TYPE == "5100"||"1400"){ 
				$column = 10;
				$arr_data[] ="$CMD_NOTE1";
			}
			$data_align = array("C", "L", "L", "C", "C", "C", "L", "C", "C", "C", "L", "C", "C");
			
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";	
			
			if($CMD_NOTE2){
				$arr_data = (array) null;
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
				
				$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		}
		$pdf->close_tab(""); 

		if($COM_NOTE){
			$head_text1 = "";
			$head_width1 = "20,252";
			$head_align1 = "L,L";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			$arr_data = (array) null;
			$arr_data[] = "หมายเหตุ : ";
			$arr_data[] = "$COM_NOTE";

			$data_align = array("L", "L");
				
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}

		$pdf->close_tab(""); 
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>