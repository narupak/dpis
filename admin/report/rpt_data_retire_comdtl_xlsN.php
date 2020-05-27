<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");
	
	if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") $row_h = 409.5;
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("rpt_data_retire_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);
		
	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";

	$company_name = "";
	if ($BKK_FLAG==1) {
		$arr_temp = explode("ที่", $COM_NO);
		if ($arr_temp[0]=="กทม. ") $DEPARTMENT_NAME = "กรุงเทพมหานคร";
		$report_title = "บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $arr_temp[1] ลงวันที่ $COM_DATE";
	} else {
		if ($MFA_FLAG==1 && $TMP_DEPARTMENT_ID == "") {
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$MINISTRY_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		} else {
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0502";

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
	$ws_width = (array) null;
	$ws_width[] = 5;
	$ws_width[] = 20;
	$ws_width[] = 20;
	$ws_width[] = 8;
	$ws_width[] = 8;
	$ws_width[] = 6;
	$ws_width[] = 8;
	if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113"))){
		$ws_width[] = 10;
		$ws_width[] = 10;
		$ws_width[] = 20;
	}elseif(in_array($COM_TYPE, array("0305", "5035"))){
		$ws_width[] = 20;
		$ws_width[] = 10;
		$ws_width[] = 20;
	}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){
		$ws_width[] = 10;
		$ws_width[] = 30;
		$ws_width[] = 30;
		$ws_width[] = 30;
	}else if($COM_TYPE=="1706"){
		$ws_width[] = 10;
		$ws_width[] = 30;
		$ws_width[] = 30;
		$ws_width[] = 30;
		$ws_width[] = 30;
	}else{
		$ws_width[] = 15;
		$ws_width[] = 40;
	}
	$ws_head_line1 = (array) null;
	$ws_head_line2 = (array) null;
	$ws_head_line3 = (array) null;
	$ws_colmerge_line1 = (array) null;
	$ws_colmerge_line2 = (array) null;
	$ws_colmerge_line3 = (array) null;
	$ws_border_line1 = (array) null;
	$ws_border_line2 = (array) null;
	$ws_border_line3 = (array) null;
	$ws_fontfmt_line1 = (array) null;
	$ws_headalign_line1 = (array) null;
	$ws_wraptext_line1 = (array) null;
	$ws_wraptext_line2 = (array) null;
	$ws_wraptext_line3 = (array) null;
	for($i = 0; $i < count($heading_text); $i++) {
		$buff = explode("|", $heading_text[$i]);
		$ws_head_line1[] = $buff[0];
		$ws_head_line2[] = $buff[1];
		$ws_head_line3[] = $buff[2];
		if (strpos($buff[0], "<**")!==false) {
			$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
		} else {
			$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
		}
		if (strpos($buff[1], "<**")!==false) {
			$ws_colmerge_line2[] = 1;	$ws_border_line2[] = "TB";
		} else {
			$ws_colmerge_line2[] = 0;	$ws_border_line2[] = "LR";
		}
		if (strpos($buff[2], "<**")!==false) {
			$ws_colmerge_line3[] = 1;	$ws_border_line3[] = "";
		} else {
			$ws_colmerge_line3[] = 0;	$ws_border_line3[] = "LBR";
		}
		$ws_fontfmt_line1[] = "B";
		$ws_headalign_line1[] = "C";
		$ws_wraptext_line1[] = 1;
		$ws_wraptext_line2[] = 1;
		$ws_wraptext_line3[] = 1;
	}
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	
	
	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $COM_TYPE;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_wraptext_line3, $ws_rotate_line1, $ws_rotate_line2, $ws_rotate_line3, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width, $wsdata_wraptext;
		global $report_title, $company_name, $colshow_cnt;

		if ($xlsRow) $xlsRow++;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if
		
		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} // end if

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
		if ($ws_head_line3) {
			// loop พิมพ์ head บรรทัดที่ 3
			$xlsRow++;
			$colseq=0;
			$pgrp="";
			for($i=0; $i < count($ws_width); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
					$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			} // end for loop
		}
		// loop พิมพ์ head บรรทัดที่ 1
//		$xlsRow++;
//		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1);
		// loop พิมพ์ head บรรทัดที่ 2
//		$xlsRow++;
//		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line2, $ws_wraptext_line2, $ws_rotate_line2);
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER , CMD_POS_NO_NAME, CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
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
		if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_MEMBER = $data[PER_MEMBER];

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		if($DPISDB=="mysql"){
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
		if(in_array($COM_TYPE, array("0305", "5035"))){
			$CMD_ORG2 = $data[CMD_ORG2];
			if($DPISDB=="mysql"){
				$arr_temp = explode("|", $data[CMD_ORG2]);
			}else{
				$arr_temp = explode("\|", $data[CMD_ORG2]);
			}
			$CMD_ORG2 = $arr_temp[1];
			if (!$CMD_ORG2) $CMD_ORG2 = $arr_temp[0];
		}

		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_DATE2 = show_date_format($data[CMD_DATE2],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		if($PER_TYPE==1){
			$cmd = " select PM_CODE, PT_CODE from PER_POSITION  
							where trim(POS_NO)='$CMD_POS_NO' and DEPARTMENT_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (!$CMD_PM_NAME) $CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} // end if
		
		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				$CMD_ORG4 = trim($data[CMD_ORG4]);

				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;

				$data_count++;
			} // end if
		}
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$arr_content[$data_count][educate] = $EN_NAME . ($EM_NAME?"\n"."$EM_NAME":"");
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($COMMAND_PRT==1) {
			if ($CMD_ORG5 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG5);
			if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG4);
			if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG3);
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_date2] = $CMD_DATE2;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		$arr_content[$data_count][cmd_org2] = $CMD_ORG2;
		
		$data_count++;

		if ($print_order_by==1 && $COMMAND_PRT!=1) {
			$arr_content[$data_count][type] = "CONTENT";
			///$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
			$arr_content[$data_count][educate] = $INS_NAME;
			$arr_content[$data_count][cmd_position] = $CMD_ORG3;
	//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

			$data_count++;
		} // end if
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$wsdata_fontfmt_1 = (array) null;
		$wsdata_align_1 = (array) null;
		$wsdata_border_1 = (array) null;
		$wsdata_border_2 = (array) null;
		$wsdata_border_3 = (array) null;
		$wsdata_colmerge_1 = (array) null;
		$wsdata_fontfmt_2 = (array) null;
		$wsdata_wraptext = (array) null;
		for($k=0; $k<count($ws_head_line1); $k++) {
			$wsdata_fontfmt_1[] = "";
			$wsdata_align_1[] = "C";
			$wsdata_border_1[] = "LR";
			$wsdata_border_2[] = "";
			$wsdata_border_3[] = "LRB";
			$wsdata_colmerge_1[] = 0;
			$wsdata_fontfmt_2[] = "";
			$wsdata_wraptext[] = 1;
		}
	// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		if ($SESS_DEPARTMENT_NAME!="กรมชลประทาน") print_header();
		
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
			$CMD_DATE2 = $arr_content[$data_count][cmd_date2];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_ORG2 = $arr_content[$data_count][cmd_org2];
			
			$arr_data = (array) null;
			$wsdata_align = (array) null;
                        //die($COM_TYPE);
			if($CMD_NOTE2){
				$arr_data[] = "";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113", "0305", "5035"))){
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";		$wsdata_align[] = "L";
				}
				$wsdata_border = $wsdata_border_2;
			}else{
				$arr_data[] = $ORDER;		$wsdata_align[] = "C";
				$arr_data[] = $NAME;			$wsdata_align[] = "L";
				if($COM_TYPE=="1703" || $COM_TYPE=="5113"){
					$arr_data[] = $EDUCATE;						$wsdata_align[] = "L";
				}
				$arr_data[] = $CMD_POSITION;			$wsdata_align[] = "L";
				$arr_data[] = $CMD_POSITION_TYPE;		$wsdata_align[] = "L";
				$arr_data[] = $CMD_LEVEL_NAME;		$wsdata_align[] = "L";
				$arr_data[] = $CMD_POS_NO_NAME.' '.$CMD_POS_NO;		$wsdata_align[] = "C";
				$arr_data[] = $CMD_OLD_SALARY;		$wsdata_align[] = "R";
				if($COM_TYPE=="1703" || $COM_TYPE=="5113"){
					$arr_data[] = $CMD_DATE;						$wsdata_align[] = "L";
					$arr_data[] =$CMD_NOTE1;					$wsdata_align[] = "L";
				}else{
					if($COM_TYPE=="1702" || $COM_TYPE=="5112"){
						$arr_data[] = $CMD_DATE;				$wsdata_align[] = "C";
						$arr_data[] = $CMD_DATE2;				$wsdata_align[] = "L";
						$arr_data[] = $CMD_NOTE1;		$wsdata_align[] = "L";
					}elseif($COM_TYPE=="0305" || $COM_TYPE=="5035"){
						$arr_data[] = $CMD_ORG2;				
                                                $wsdata_align[] = "C";
						$arr_data[] = $CMD_DATE;				
                                                $wsdata_align[] = "L";
						$arr_data[] = $CMD_NOTE1;		
                                                $wsdata_align[] = "L";
					}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){
						$arr_data[] = "หน้าที่ที่ปฏิบัติ";			$wsdata_align[] = "C";
						$arr_data[] = "เวลา..ปี";					$wsdata_align[] = "L";
						$arr_data[] = $CMD_DATE;		$wsdata_align[] = "L";
						$arr_data[] = $CMD_NOTE1;		$wsdata_align[] = "L";
					}else if($COM_TYPE=="1706"){
						$arr_data[] = "หน้าที่ที่ปฏิบัติ";			$wsdata_align[] = "C";
						$arr_data[] = "เวลา..ปี";					$wsdata_align[] = "L";
						$arr_data[] = "เงินเดือนระหว่างปฏิบัติงาน";		$wsdata_align[] = "L";
						$arr_data[] = $CMD_DATE;			$wsdata_align[] = "L";
						$arr_data[] = $CMD_NOTE1;		$wsdata_align[] = "L";
					}else{	//1701
						$arr_data[] = $CMD_DATE;			$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1;		$wsdata_align[] = "L";
					}
				}
				$wsdata_border = $wsdata_border_1;
			} // end if

			if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน" || $data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") print_header();
					
			$xlsRow++;
			if ($row_h) $worksheet->set_row($xlsRow, $row_h); // agument ตัวที่ 2 คือ row height = $row_h
			$colseq=0;
			$pgrp="";
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
					if($CMD_NOTE2)
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					else
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for				
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$wsdata_align = (array) null;
			$arr_data[] = "";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113","0305", "5035"))){
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			}else if($COM_TYPE=="1706"){	
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";		$wsdata_align[] = "L";
			}
			$wsdata_border = $wsdata_border_2;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
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
		if(in_array($COM_TYPE, array("1702", "1703", "5112", "5113","0305", "5035"))){
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}elseif($COM_TYPE=="ออกจากราชการไปปฏิบัติงานตามมติ ครม."){
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}else if($COM_TYPE=="1706"){	
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีรายละเอียดการอนุญาตให้ข้าราชการลาออกจากราชการ.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีรายละเอียดการอนุญาตให้ข้าราชการลาออกจากราชการ.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>