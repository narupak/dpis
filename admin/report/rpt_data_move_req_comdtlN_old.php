<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

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
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,$DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_DESC = str_replace("คำสั่ง", "", $data [COM_DESC]);
	
	if( !$order_by ) $order_by = 1;
	if ($order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการ$COM_DESC แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
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
	if($COM_TYPE=="0202"){
		$heading_width[0] = "9";
		$heading_width[1] = "22";
		$heading_width[2] = "25";
		$heading_width[3] = "21";
		$heading_width[4] = "15";
		$heading_width[5] = "12";
		$heading_width[6] = "14";
		$heading_width[7] = "14";
		$heading_width[8] = "21";
		$heading_width[9] = "12";
		$heading_width[10] = "17";
		$heading_width[11] = "22";
		$heading_width[12] = "15";
		$heading_width[13] = "12";	
		$heading_width[14] = "14";
		$heading_width[15] = "14";
		$heading_width[16] = "15";	
		$heading_width[17] = "20";		

		$heading_text[0] = "ลำดับ|ที่|";
		$heading_text[1] = "ชื่อ/นามสกุล/|เลขประจำตัว|ประชาชน";
		$heading_text[2] = "วุฒิที่ได้รับเพิ่มขึ้น/|สถานศึกษา/วันที่|สำเร็จการศึกษา";
		$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ตำแหน่ง/สังกัด|";
		$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ตำแหน่ง|ประเภท";
		$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ระดับ|";
		$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการเดิม|เลขที่|";
		$heading_text[7] = "<**1**>ตำแหน่งและส่วนราชการเดิม|เงินเดือน|";
		$heading_text[8] = "<**2**>สอบแข่งขันได้|ตำแหน่ง|";
		$heading_text[9] = "<**2**>สอบแข่งขันได้|ลำดับที่|";
		$heading_text[10] = "<**2**>สอบแข่งขันได้|ประกาศผล|การสอบของ";
		$heading_text[11] = "<**3**>ตำแหน่งและส่วนราชการที่รับโอน|ตำแหน่ง/สังกัด|";
		$heading_text[12] = "<**3**>ตำแหน่งและส่วนราชการที่รับโอน|ตำแหน่ง|ประเภท";
		$heading_text[13] = "<**3**>ตำแหน่งและส่วนราชการที่รับโอน|ระดับ|";	
		$heading_text[14] = "<**3**>ตำแหน่งและส่วนราชการที่รับโอน|เลขที่|";
		$heading_text[15] = "<**3**>ตำแหน่งและส่วนราชการที่รับโอน|เงินเดือน|";
		$heading_text[16] = "ตั้งแต่วันที่||";	
		$heading_text[17] = "หมายเหตุ||";		
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');
	}else{
		$heading_width[0] = "9";
		$heading_width[1] = "28";
		$heading_width[2] = "27";
		$heading_width[3] = "25";
		$heading_width[4] = "23";
		$heading_width[5] = "17";
		$heading_width[6] = "10";
		$heading_width[7] = "14";
		$heading_width[8] = "25";
		$heading_width[9] = "23";
		$heading_width[10] = "17";
		$heading_width[11] = "10";
		$heading_width[12] = "14";
		$heading_width[13] = "18";	
		$heading_width[14] = "25";

		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "ชื่อ/นามสกุล/|เลขประจำตัวประชาชน";
		$heading_text[2] = "วุฒิ/สาขา/|สถานศึกษา";
		$heading_text[3] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ตำแหน่ง/สังกัด";
		$heading_text[4] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ตำแหน่งประเภท";
		$heading_text[5] = "<**1**>ตำแหน่งและส่วนราชการเดิม|ระดับ";
		$heading_text[6] = "<**1**>ตำแหน่งและส่วนราชการเดิม|เลขที่";
		$heading_text[7] = "<**1**>ตำแหน่งและส่วนราชการเดิม|เงินเดือน";
		$heading_text[8] = "<**2**>ตำแหน่งและส่วนราชการที่ย้าย|ตำแหน่ง/สังกัด";
		$heading_text[9] = "<**2**>ตำแหน่งและส่วนราชการที่ย้าย|ตำแหน่งประเภท";
		$heading_text[10] = "<**2**>ตำแหน่งและส่วนราชการที่ย้าย|ระดับ";
		$heading_text[11] = "<**2**>ตำแหน่งและส่วนราชการที่ย้าย|เลขที่";
		$heading_text[12] = "<**2**>ตำแหน่งและส่วนราชการที่ย้าย|เงินเดือน";
		$heading_text[13] = "ตั้งแต่วันที่|";	
		$heading_text[14] = "หมายเหตุ|";
		
		$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');
	}
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PER_ID = $data[PER_ID];
		$PRE_PN_CODE = trim($data[PRE_PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRE_PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];	
		$PL_CODE = trim($data[PL_CODE]);		
		$PN_CODE = trim($data[PN_CODE]);
		$EP_CODE = trim($data[EP_CODE]);
		$TP_CODE = trim($data[TP_CODE]);
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$EN_NAME = (array) null;
		$EN_SHORTNAME = (array) null;
		$EM_NAME = (array) null;
		$INS_NAME = (array) null;
		$ARR_EN_CODE = explode(",",$data[CMD_EDUCATE]);
		for($i = 0; $i < count($ARR_EN_CODE); $i++) {
			if($DPISDB=="odbc"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									where		a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." ";
			}elseif($DPISDB=="oci8"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
									from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
													and a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." ";
			}elseif($DPISDB=="mysql"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." ";
			} // end if
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			while ($data2 = $db_dpis2->get_array()) {
				$EN_NAME[] = trim($data2[EN_NAME]);
				if (!trim($data2[EN_SHORTNAME])) {
					$EN_SHORTNAME[] = trim($data2[EN_NAME]);
				} else {
					$EN_SHORTNAME[] = trim($data2[EN_SHORTNAME]);
				}
				$EM_NAME[] = trim($data2[EM_NAME]);
				$INS_NAME[] = trim($data2[INS_NAME]);
			}
		} // end for loop
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		$CMD_POSITION = $arr_temp[0];
		$CMD_PM_NAME = $arr_temp[1];

		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = $CMD_POS_NO_NAME.trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
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
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME]."\n".$NEW_LEVEL_NAME;
		
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b 
							  where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
/*			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]); */

			$CMD_POSITION = (trim($CMD_PM_NAME)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, c.OT_CODE, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_OT_CODE = trim($data2[OT_CODE]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			$NEW_PL_NAME = (trim($NEW_PM_CODE)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME . (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_CODE)?")":"");
		}elseif($PER_TYPE==2){
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME]."\n".$NEW_LEVEL_NAME;
		
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, a.POEM_NO_NAME, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==3){
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME]."\n".$NEW_LEVEL_NAME;
		
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, a.POEMS_NO_NAME, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} elseif($PER_TYPE==4){
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME]."\n".$NEW_LEVEL_NAME;
		
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, a.POT_NO_NAME, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} // end if
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_ORG_NAME_2 = $data2[ORG_NAME];

		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($NEW_ORG_NAME=="-") $NEW_ORG_NAME = "";
		if ($NEW_ORG_NAME_1=="-") $NEW_ORG_NAME_1 = "";
		if ($NEW_ORG_NAME_2=="-") $NEW_ORG_NAME_2 = "";
		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				$CMD_ORG4 = trim($data[CMD_ORG4]);

				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

				$data_count++;
			} // end if
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
//		$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"");
		$arr_content[$data_count][educate] = $EN_SHORTNAME[0];
		
		$arr_content[$data_count][position] = $PL_NAME;	//ตน. และตน.ที่สอบแข่งขันได้
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;

		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//		$arr_content[$data_count][educate] = $INS_NAME;
		if($EN_SHORTNAME[1])
			$arr_content[$data_count][educate] = $EN_SHORTNAME[1];
		if ($print_order_by==1) {
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) $arr_content[$data_count][cmd_position] = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					else $arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				} else {
					$arr_content[$data_count][cmd_position] = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
				}
				if ($NEW_OT_CODE=="03") { 
					if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) $arr_content[$data_count][new_position] = "ที่ทำการปกครอง".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
					else $arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
				} else { 
					$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_1." ".$NEW_ORG_NAME." ".$DEPARTMENT_NAME);
				}
			} else {
				$arr_content[$data_count][cmd_position] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
				$arr_content[$data_count][new_position] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
			}
		}
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		$data_count++;

		if($CMD_NOTE2 || $EN_SHORTNAME[2]){
			$arr_content[$data_count][type] = "CONTENT";
			if($CMD_NOTE2)
				$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
			if($EN_SHORTNAME[2])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[2];

			$data_count++;

			if($EN_SHORTNAME[3]){
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][educate] = $EN_SHORTNAME[3];

				$data_count++;

				if($EN_SHORTNAME[4]){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][educate] = $EN_SHORTNAME[4];

					$data_count++;
				}
			} // end if 3
		} // end if 2
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_level_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_LEVEL_NAME=$arr_content[$data_count][new_level_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
		
			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2" ){
					$ORG_NAME = $arr_content[$data_count][org_name];
					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
				
					$arr_data = (array) null;
					if($COM_TYPE=="0202"){
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**3**>";
						$arr_data[] = "<**3**>";
						$arr_data[] = "<**3**>";
						$arr_data[] = "<**4**>$NEW_ORG_NAME";
						$arr_data[] = "<**4**>$NEW_ORG_NAME";
						$arr_data[] = "<**4**>$NEW_ORG_NAME";
						$arr_data[] = "<**4**>$NEW_ORG_NAME";
						$arr_data[] = "<**4**>$NEW_ORG_NAME";

						$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					}else{
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**2**>$ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";

						$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					} 
				} // end if
			}

			if($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				if($COM_TYPE=="0202"){
					$arr_data[] = "$ORDER";
					$arr_data[] = "$NAME";
					$arr_data[] = "$EDUCATE";
					$arr_data[] = "$CMD_POSITION";
					$arr_data[] = "$CMD_POSITION_TYPE";
					$arr_data[] = "$CMD_LEVEL_NAME";
					$arr_data[] = "$CMD_POS_NO";
					$arr_data[] = "$CMD_OLD_SALARY";
					$arr_data[] = "$POSITION";
					$arr_data[] = "$CMD_ACC_NO";
					$arr_data[] = "$CMD_ACCOUNT";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_LEVEL_NAME";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";

					$data_align = array("C", "L", "L", "L", "C", "C", "C", "R", "L", "R", "L", "L", "C", "C", "C", "R", "C", "L");
					
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
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
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

						$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					} // end if
				}else{
					$arr_data[] = "$ORDER";
					$arr_data[] = "$NAME";
					$arr_data[] = "$EDUCATE";
					$arr_data[] = "$CMD_POSITION";
					$arr_data[] = "$CMD_POSITION_TYPE";
					$arr_data[] = "$CMD_LEVEL_NAME";
					$arr_data[] = "$CMD_POS_NO";
					$arr_data[] = "$CMD_OLD_SALARY";
					$arr_data[] = "$NEW_POSITION";
					$arr_data[] = "$NEW_POSITION_TYPE";
					$arr_data[] = "$NEW_LEVEL_NAME";
					$arr_data[] = "$NEW_POS_NO";
					$arr_data[] = "$CMD_SALARY";
					$arr_data[] = "$CMD_DATE";
					$arr_data[] = "$CMD_NOTE1";

					$data_align = array("C", "L", "L", "L", "C", "L", "C", "R", "L", "C", "L", "C", "R", "C", "L");
					
					$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
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
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

						$data_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					
						$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
						if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					} // end if
				}
			} // end if
		} // end for				
		$pdf->close_tab(""); 
		
		if($COM_NOTE){
			$head_text1 = "";
			$head_width1 = "20,265";
			$head_align1 = "L,L";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
			if (!$result) echo "****** error ****** on open table for $table<br>";
			
			$arr_data = (array) null;
			$arr_data[] = "หมายเหตุ : ";
			$arr_data[] = "$CMD_NOTE";

			$data_align = array("L", "L");
				
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "cordia", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			$pdf->close_tab(""); 
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