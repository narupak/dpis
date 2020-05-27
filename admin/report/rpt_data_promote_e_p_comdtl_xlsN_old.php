<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	include ("rpt_data_promote_e_p_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

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

	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";

	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนตำแหน่ง$PERSON_TYPE[$COM_PER_TYPE] แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ ". ($COM_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NO):$COM_NO):"-")." ลงวันที่ ". ($COM_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_DATE):$COM_DATE):"-");
	$report_code = "P0310";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		$ws_head_line1 = array("ลำดับ","","","<**1**>ตำแหน่งและส่วนราชการเดิม","<**1**>","<**1**>","<**1**>","<**1**>","ดำรงตำแหน่ง","<**2**>ตำแหน่งและส่วนราชการที่เลื่อน","<**2**>","<**2**>","<**2**>","<**2**>","","");
		$ws_head_line2 = array("ที่","ชื่อ-นามสกุล","วุฒิ/สาขา","ตำแหน่ง/สังกัด","ตำแหน่งประเภท","ระดับ","เลขที่","เงินเดือน","ในระดับปัจจุบันเมื่อ","ตำแหน่ง/สังกัด","ตำแหน่งประเภท","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
//		$ws_head_line3 = array("","","","","","","","","ปัจจุบันเมื่อ","","","","","","","");
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,0,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TB","TLR","TB","TB","TB","TB","TB","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","TLBR","TLBR","LBR","TLBR","TLBR","TLBR","TLBR","TLBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5,20,20,30,10,10,12,10,15,30,10,10,12,10,12,30);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2 , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2 , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2 , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";
	} // end if
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
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
	    $cardID=card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);

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
		if($DPISDB=="mysql") {
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
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = substr($data[CMD_DATE], 0, 10);
		if(trim($CMD_DATE)){
			$arr_temp = explode("-", $CMD_DATE);
			if($CMD_DATE >= "$arr_temp[0]-10-01") $CMD_BUDGET_YEAR = ($arr_temp[0] + 543 + 1);
			else $CMD_BUDGET_YEAR = ($arr_temp[0] + 543);
			$CMD_DATE = show_date_format($CMD_DATE,$DATE_DISPLAY);
		} // end if
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
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b 
							where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			if ($CMD_POSITION==$CMD_PM_NAME) $CMD_PM_NAME = "";
			$CMD_POSITION = (trim($CMD_PM_NAME)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME)?")":"");

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			if ($NEW_PL_NAME==$NEW_PM_NAME) $NEW_PM_NAME = "";
			$NEW_PL_NAME = (trim($NEW_PM_NAME)?"$NEW_PM_NAME (":"") . (trim($NEW_PL_NAME)?($NEW_PL_NAME . (($NEW_PT_NAME != "ทั่วไป" && $NEW_LEVEL_NO >= 6)?"$NEW_PT_NAME":"")):"") . (trim($NEW_PM_NAME)?")":"");
		}elseif($PER_TYPE==2){
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
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, a.POEMS_NO_NAME, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
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
		}elseif($PER_TYPE==4){
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

		$cmd = " select POH_EFFECTIVEDATE as LEVEL_EFFECTIVEDATE
						from   PER_POSITIONHIS a, PER_MOVMENT b
						where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=3
						order by POH_EFFECTIVEDATE DESC ";
//		$cmd = " select 	min(POH_EFFECTIVEDATE) as LEVEL_EFFECTIVEDATE 
//						 from 		PER_POSITIONHIS 
//						 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[LEVEL_EFFECTIVEDATE],$DATE_DISPLAY);
		
		$cmd = " select 	SAH_SALARY 
						 from 		PER_SALARYHIS 
						 where 	PER_ID=$PER_ID 
						 				and SAH_EFFECTIVEDATE < '". ($CMD_BUDGET_YEAR - 543) ."-10-01'
						 				and SAH_EFFECTIVEDATE >= '". ($CMD_BUDGET_YEAR - 543 - 1) ."-10-01'
						 order by SAH_EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$BUDGET_YEAR_SALARY = $data2[SAH_SALARY];

		$cmd = " select 	SAH_SALARY 
						 from 		PER_SALARYHIS 
						 where 	PER_ID=$PER_ID 
						 				and SAH_EFFECTIVEDATE < '". ($CMD_BUDGET_YEAR - 543 - 1) ."-10-01'
						 				and SAH_EFFECTIVEDATE >= '". ($CMD_BUDGET_YEAR - 543 - 2) ."-10-01'
						 order by SAH_EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PREV_BUDGET_YEAR_SALARY = $data2[SAH_SALARY];
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		if ($BKK_FLAG==1 || $SESS_DEPARTMENT_NAME=="กรมชลประทาน")
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		else
			$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME."\n(". (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE).")\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")";
//		$arr_content[$data_count][educate] = $EN_NAME . ($EM_NAME?"/$EM_NAME":"");
		$arr_content[$data_count][educate] = $EN_SHORTNAME[0];
		
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][budget_year_salary] = $BUDGET_YEAR_SALARY?number_format($BUDGET_YEAR_SALARY):"-";
		$arr_content[$data_count][prev_budget_year_salary] = $PREV_BUDGET_YEAR_SALARY?number_format($PREV_BUDGET_YEAR_SALARY):"-";

		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;

		if ($print_order_by==1) {
			$arr_content[$data_count][type] = "CONTENT";
//		$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
//		$arr_content[$data_count][educate] = $INS_NAME;
			if($EN_SHORTNAME[1])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1];
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
			$data_count++;
		
		}
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

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
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){

		print_header();
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_border_2 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "B";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "TLRB";
				$wsdata_border_2[] = "";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_level_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$BUDGET_YEAR_SALARY = $arr_content[$data_count][budget_year_salary];
			$PREV_BUDGET_YEAR_SALARY = $arr_content[$data_count][prev_budget_year_salary];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_LEVEL_NAME=$arr_content[$data_count][new_level_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
					$ORG_NAME = $arr_content[$data_count][org_name];
					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

					if($CONTENT_TYPE=="ORG"){
						$arr_data = (array) null;
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**2**>$ORG_NAME";
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
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
	
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");

						$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
						$xlsRow++;
						$colseq=0;
						for($i=0; $i < count($arr_column_map); $i++) {
							if ($arr_column_sel[$arr_column_map[$i]]==1) {
								if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
								else $ndata = $arr_data[$arr_column_map[$i]];
								$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
								$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
								$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
								$colseq++;
							}
						}
					}else{
						$arr_data = (array) null;
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**2**>$ORG_NAME";
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
						$arr_data[] = "<**3**>$NEW_ORG_NAME";
						$arr_data[] = "<**3**>$NEW_ORG_NAME";

						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						
						$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
						$xlsRow++;
						$colseq=0;
						for($i=0; $i < count($arr_column_map); $i++) {
							if ($arr_column_sel[$arr_column_map[$i]]==1) {
								if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
								else $ndata = $arr_data[$arr_column_map[$i]];
								$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
								$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
								$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
								$colseq++;
							}
						}
					} // end if
				} // end if
			}

			if($CONTENT_TYPE=="CONTENT"){
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

					$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					
					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
							$colseq++;
						}
					}
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $EDUCATE;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $CMD_OLD_SALARY;
					$arr_data[] = $LEVEL_EFFECTIVEDATE;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_LEVEL_NAME;
					$arr_data[] = $NEW_POS_NO;
					$arr_data[] = $CMD_SALARY;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_NOTE1;

					$wsdata_align = array("C", "L", "L", "L", "C", "L", "C", "R", "C", "L", "C", "C", "C", "R", "L", "L");
					
					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
							$colseq++;
						}
					}
				} // end if
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";

			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
		} // end if
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งเลื่อนตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งเลื่อนตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>