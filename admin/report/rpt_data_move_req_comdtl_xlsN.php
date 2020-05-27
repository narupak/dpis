<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");	
	include ("../report/rpt_function.php");

	if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") $row_h = 409.5;
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("rpt_data_move_req_comdtlN_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
        //die($chk_salary); // ตัวแปรเเสดงเงินเดือน
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME, COM_GROUP  
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
	$COM_GROUP = trim($data[COM_GROUP]);
		
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
			if ($RID_FLAG==1)
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_DESC แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
			else
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0304";
	
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
	if ($COM_GROUP=="503")
		$head_txt = "ที่รับโอน";
	else
		$head_txt = "ที่ย้าย";
	if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
		$ws_width = array(5.29, 14.14, 17.14, 16.43, 7.14, 5.86, 5.57, 8, 14.29, 6.29, 14.43, 17.14, 7.14, 5.86, 5.57, 8.14, 9.57,10.29);
		if ($CARDNO_FLAG==1) $CARDNO_TITLE = ""; 
		$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"วุฒิที่ได้รับเพิ่มขึ้น/",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม","<**2**>สอบแข่งขันได้","<**2**>สอบแข่งขันได้","<**2**>สอบแข่งขันได้",$COM_HEAD_03.$head_txt,$COM_HEAD_03.$head_txt,$COM_HEAD_03.$head_txt,$COM_HEAD_03.$head_txt,$COM_HEAD_03.$head_txt,"ตั้งแต่","หมายเหตุ");
		$ws_head_line2= array("ที่","$CARDNO_TITLE","สถานศึกษา/วันที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตำแหน่ง","ลำดับที่","ประกาศผล","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","วันที่","");
		$ws_head_line3 = array("","","สำเร็จการศึกษา","","ประเภท","","","","","","การสอบของ","","ประเภท","","","","","");
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TBR","TLB","TB","TBR","TLB","TB","TB","TB","TB","TRL","TLR");
		$ws_border_line2 = array("RL","RL","RL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","RL","RL");
		$ws_border_line3 = array("RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	}else{
		$ws_width = array(5.29, 13.86, 14.43, 19.71, 7.71, 8.71, 6.86, 8.29, 15.86, 8.29, 9, 6.43, 7.86, 9.57, 14);
		$ws_head_line1 = array("ลำดับ","","",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_02.$head_txt,$COM_HEAD_02.$head_txt,$COM_HEAD_02.$head_txt,$COM_HEAD_02.$head_txt,$COM_HEAD_02.$head_txt,"","");
		$ws_head_line2 = array("ที่",$FULLNAME_HEAD,"วุฒิ/สาขา","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
		$ws_head_line3 = array("","","","","ประเภท","","","","","ประเภท","","","","","");
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TBR","TLB","TB","TB","TB","TB","TRL","TLR");
		$ws_border_line2 = array("RL","RL","RL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","RL","RL");
		$ws_border_line3 = array("RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
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
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_fontfmt_line1;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_headalign_line1, $ws_headalign_line2, $ws_width;
		global $report_title, $company_name, $colshow_cnt;
		
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
//		echo "report_title=$report_title<br>";
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
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
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
/*		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1);
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line2, $ws_wraptext_line2, $ws_rotate_line2);*/
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_EDUCATE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2,  a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW, a.PM_CODE
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
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
		if ($CARDNO_FLAG==1){
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID=card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
         
		 
		$EN_NAME = (array) null;
		$EN_SHORTNAME = (array) null;
		$EM_NAME = (array) null;
		$INS_NAME = (array) null;
		$ARR_EN_CODE = explode(",",$data[CMD_EDUCATE]);
		for($i = 0; $i < count($ARR_EN_CODE); $i++) {
			if($DPISDB=="odbc"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
									where		a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
			}elseif($DPISDB=="oci8"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
									where		a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
													and a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
			}elseif($DPISDB=="mysql"){
				$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
									from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where a.PER_ID = $PER_ID and a.EN_CODE = ".$ARR_EN_CODE[$i]." order by EDU_SEQ ";
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
				if (trim($data2[EM_NAME])) {
					$EM_NAME[] = "(".trim($data2[EM_NAME]).")";
				}
				if (trim($data2[INS_NAME])) {
					//$INS_NAME[] = trim($data2[INS_NAME]);
				} else {
					$INS_NAME[] = trim($data2[EDU_INSTITUTE]);
				}
				$EDU_HONOR = trim($data2[EDU_HONOR]);
				if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
				$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
				$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CT_NAME = $data2[CT_NAME];
				if ($CT_NAME=="ไทย") $CT_NAME = "";
			}
		} // end for loop
		
		$TMP_CMD_DATE = trim($data[CMD_DATE]);
		$cmd = "	select	LH_SUB_TYPE
							from		PER_LICENSEHIS
							where	PER_ID = $PER_ID and LH_EXPIRE_DATE > '$TMP_CMD_DATE' order by LH_SEQ_NO DESC ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$LH_SUB_TYPE = trim($data2[LH_SUB_TYPE]);
		if ($LH_SUB_TYPE && $SESS_DEPARTMENT_NAME=="กรมชลประทาน") $EN_SHORTNAME[] = "และได้รับใบอนุญาตเป็นผู้ประกอบวิชาชีพวิศวกรรมควบคุมระดับ".$LH_SUB_TYPE;

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
			$CMD_ORG2 = $data[CMD_ORG2];
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_NOW = trim($data[CMD_NOW]);
		if ($CMD_NOW=="Y") $CMD_DATE = "บัดนี้เป็นต้นไป";

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		if($PER_TYPE==1){
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];
		
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

			//$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);
                        $CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, c.OT_CODE, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.DEPARTMENT_ID
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_OT_CODE = trim($data2[OT_CODE]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PM_CODE = trim($data[PM_CODE]);

			$NEW_DEPARTMENT_ID = $data2[DEPARTMENT_ID];
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$NEW_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);
			
			// $NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME, $NEW_LEVEL_NO);
                         $NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME);
		}elseif($PER_TYPE==2){
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];
		
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";
                        $CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, a.POEM_NO_NAME, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.DEPARTMENT_ID
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_DEPARTMENT_ID = $data2[DEPARTMENT_ID];
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==3){
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];
		
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";
                        $CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, a.POEMS_NO_NAME, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.DEPARTMENT_ID
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_DEPARTMENT_ID = $data2[DEPARTMENT_ID];
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} elseif($PER_TYPE==4){
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];
		
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, a.POT_NO_NAME, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.DEPARTMENT_ID
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_DEPARTMENT_ID = $data2[DEPARTMENT_ID];
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		} // end if

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_DEPARTMENT_NAME = $data2[ORG_NAME];
		
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
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
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
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")";
//		$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"");
		$arr_content[$data_count][educate] = $EN_SHORTNAME[0]."\n".$EM_NAME[0]."\n".$INS_NAME[0];
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($EN_SHORTNAME[1]) $arr_content[$data_count][educate] .= "\n".$EN_SHORTNAME[1]."\n".$EM_NAME[1]."\n".$INS_NAME[1];
			if ($EN_SHORTNAME[2]) $arr_content[$data_count][educate] .= "\n".$EN_SHORTNAME[2]."\n".$EM_NAME[2]."\n".$INS_NAME[2];
			if ($EN_SHORTNAME[3]) $arr_content[$data_count][educate] .= "\n".$EN_SHORTNAME[3]."\n".$EM_NAME[3]."\n".$INS_NAME[3];
			if ($EN_SHORTNAME[4]) $arr_content[$data_count][educate] .= "\n".$EN_SHORTNAME[4]."\n".$EM_NAME[4]."\n".$INS_NAME[4];
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "\n".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "\n".trim($CT_NAME);
		}

		$arr_content[$data_count][position] = $PL_NAME;	//ตน. และตน.ที่สอบแข่งขันได้
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) { 
						$arr_content[$data_count][cmd_position] .= "\nที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
					} else {
						if ($CMD_ORG5) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG5);
						if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG4);
						if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG3);
					}
				} else {
					if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG4);
					if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG3);
					if ($DEPARTMENT_NAME) $arr_content[$data_count][cmd_position] .= "\n".trim($DEPARTMENT_NAME);
				}
			} else {
				if ($CMD_ORG5 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG5);
				if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG4);
				if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG3);
				if ($MFA_FLAG==1) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG2);
			}
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
                if($chk_salary){//เลือกเเสดงเงินเดือน
                    $arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
                }else {
                    $arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY = "-";
                }
		
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				if ($NEW_OT_CODE=="03") { 
					if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) {
						$arr_content[$data_count][new_position] .= "\nที่ทำการปกครอง".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
					} else {
						if ($NEW_ORG_NAME_2) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_2);
						if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_1);
						if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME);
					}
				} else { 
					if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_1);
					if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME);
					if ($DEPARTMENT_NAME) $arr_content[$data_count][new_position] .= "\n".trim($DEPARTMENT_NAME);
				}
			} else {
				if ($NEW_ORG_NAME_2 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_2);
				if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_1);
				if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME);
				if ($MFA_FLAG==1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_DEPARTMENT_NAME);
			}
		} 
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
                if($chk_salary){//เลือกเเสดงเงินเดือน
                 $arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
                }else{
                 $arr_content[$data_count][cmd_salary] = $CMD_SALARY = "-";
                }

		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;
/*
		if ($print_order_by==1 && $COMMAND_PRT!=1) {
			$arr_content[$data_count][type] = "CONTENT";
//			$arr_content[$data_count][name] = (($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID);
//		$arr_content[$data_count][educate] = $INS_NAME;
			if($EN_SHORTNAME[1])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1]."\n".$EM_NAME[1];
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
		
		} */
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		if ($COMMAND_PRT!=1) {
			if($EN_SHORTNAME[1]){
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1]."\n".$EM_NAME[1]."\n".$INS_NAME[1];

				$data_count++;

				if($EN_SHORTNAME[2]){
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][educate] = $EN_SHORTNAME[2]."\n".$EM_NAME[2]."\n".$INS_NAME[2];

					$data_count++;

					if($EN_SHORTNAME[3]){
						$arr_content[$data_count][type] = "CONTENT";
						$arr_content[$data_count][educate] = $EN_SHORTNAME[3]."\n".$EM_NAME[3]."\n".$INS_NAME[3];

						$data_count++;

						if($EN_SHORTNAME[4]){
							$arr_content[$data_count][type] = "CONTENT";
							$arr_content[$data_count][educate] = $EN_SHORTNAME[4]."\n".$EM_NAME[4]."\n".$INS_NAME[4];

							$data_count++;
						} // end if 4
					} // end if 3
				} // end if 2
			} // end if 1
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "\n".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "\n".trim($CT_NAME);
		}

		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		}
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

//		print title

		$xlsRow = 0;
	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
			$wsdata_fontfmt_1 = array("","","","","","","","","","","","","","","","","","");
			$wsdata_align_1 = array("C","L","C","C","L","L","L","R","L","R","C","C","C","C","C","R","C","C");
//				$wsdata_border_1 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
			$wsdata_border_1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
			$wsdata_border_2 = array("","","","","","","","","","","","","","","","","","");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_colmerge_2 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,0,0);
			$wsdata_colmerge_3 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_wraptext = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","");
		}else{
			$wsdata_fontfmt_1 = array("","","","","","","","","","","","","","","");
			$wsdata_align_1 = array("C","L","C","C","L","L","L","R","L","R","C","C","R","C","L");
//				$wsdata_border_1 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
			$wsdata_border_1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
			$wsdata_border_2 = array("","","","","","","","","","","","","","","");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_colmerge_2 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,0,0);
			$wsdata_colmerge_3 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_wraptext = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","");
		}
		$wsdata_border_3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
	// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		if ($SESS_DEPARTMENT_NAME!="กรมชลประทาน") print_header();
		
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
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];

			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
					$ORG_NAME = $arr_content[$data_count][org_name];
					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

					if($CONTENT_TYPE=="ORG"){
						$arr_data = (array) null;
						if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "";
							$arr_data[] = "";

							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						}else{
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
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
							$arr_data[] = "";
							$arr_data[] = "";

							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						} 

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
								$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
								$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//								echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_2[$arr_column_map[$i]].", border=$border<br>";
								$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
//								$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
								$colseq++;
							}
						}
					}else{
						$arr_data = (array) null;
						if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**3**> ";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "<**4**>$NEW_ORG_NAME";
							$arr_data[] = "";
							$arr_data[] = "";
	
							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						}else{
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
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
							$arr_data[] = "";
							$arr_data[] = "";

							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
						}

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
								$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
								$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//								echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_2[$arr_column_map[$i]].", border=$border<br>";
								$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
//								$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
								$colseq++;
							}
						}
					} // end if
				} // end if
			} // end if ($print_order_by==2)

			if($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				if($CMD_NOTE2){
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

					$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
					$xlsRow++;
					$colseq=0;
					$pgrp="";
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//							echo "3..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_[$arr_column_map[$i]].", border=$border<br>";
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
	//						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							$colseq++;
						}
					}
				}else{ // else ไม่มี $CMD_NOTE2
					if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
						$arr_data[] = $ORDER;
						$arr_data[] = $NAME;
						$arr_data[] = $EDUCATE;
						$arr_data[] = $CMD_POSITION;
						$arr_data[] = $CMD_POSITION_TYPE;
						$arr_data[] = $CMD_LEVEL_NAME;
						$arr_data[] = $CMD_POS_NO;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $POSITION;
						$arr_data[] = $CMD_ACC_NO;
						$arr_data[] = $CMD_ACCOUNT;
						$arr_data[] = $NEW_POSITION;
						$arr_data[] = $NEW_POSITION_TYPE;
						$arr_data[] = $NEW_LEVEL_NAME;
						$arr_data[] = $NEW_POS_NO;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DATE;
						$arr_data[] = $CMD_NOTE1;
	
						$wsdata_align = array("C", "L", "C", "L", "C", "C", "C", "C", "L", "L", "R", "L", "L", "C", "C", "C", "C", "C", "L");
					}else{
						$arr_data[] = $ORDER;
						$arr_data[] = $NAME;
						$arr_data[] = $EDUCATE;
						$arr_data[] = $CMD_POSITION;
						$arr_data[] = $CMD_POSITION_TYPE;
						$arr_data[] = $CMD_LEVEL_NAME;
						$arr_data[] = $CMD_POS_NO;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $NEW_POSITION;
						$arr_data[] = $NEW_POSITION_TYPE;
						$arr_data[] = $NEW_LEVEL_NAME;
						$arr_data[] = $NEW_POS_NO;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DATE;
						$arr_data[] = $CMD_NOTE1;
	
						$wsdata_align = array("C", "L", "C", "L", "C", "C", "C", "C", "L", "C", "C", "C", "C", "C", "L");
					}

					if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน" || $data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;
//					$wsdata_border = $wsdata_border_1;

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
//							echo "4..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
	//						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							$colseq++;
						}
					}
				} // end if
				if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน" && $data_count < count($arr_content)-1) {
					$xlsRow++;
				}
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "";
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

			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			}

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
			$xlsRow++;
			$colseq=0;
			$pgrp="";
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "5..$xlsRow-->$ndata, ".$wsdata_border_2[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", border=$border, merge=$merge<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
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
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "", "L", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "", "L", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "", "L", "", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "", "L", "", 0));
		if($COM_TYPE=="0202" || $COM_TYPE=="5022"){
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "", "L", "", 1));
			$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "", "L", "", 0));
			$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "", "L", "", 0));
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งย้าย.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งย้าย.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>