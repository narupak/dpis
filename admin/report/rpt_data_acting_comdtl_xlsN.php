<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");

	include ("rpt_data_acting_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

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
	$COM_DATE = show_date_format($data[COM_DATE],5);
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
			if ($RID_FLAG==1)
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_DESC แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
			else
				$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P1501";

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
	if (!$CARDNO_FLAG==1) $CARDNO_TITLE = "";
	if ($BKK_FLAG==1) {
		if($COM_TYPE == "5090" || $COM_TYPE == "1300") {
			$heading_name4="รักษาราชการแทน";
			$ws_width = array(5,15,20,8,8,8,20,8,8,8,10,10,20);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","<**2**>$heading_name4","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่","ถึงวันที่","หมายเหตุ");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","","","");
			$ws_head_line3 = array("","","","ประเภท","","","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TBR","TLR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		} elseif($COM_TYPE == "5100" || $COM_TYPE == "1400") {
			$heading_name4="รักษาการในตำแหน่ง";
			$ws_width = array(5,15,20,8,8,20,10,10,20);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>",$heading_name4,"ตั้งแต่วันที่","ถึงวันที่","หมายเหตุ");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","","","","");
			$ws_head_line3 = array("","","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TBR","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","LR","LR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
		} else {
			$heading_name4="รักษาราชการแทน";
			$ws_width = array(5,15,20,8,8,8,20,8,8,8,10);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","<**2**>$heading_name4","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","");
			$ws_head_line3 = array("","","","ประเภท","","","","ประเภท","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TBR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C");
		}
	} else {
		if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))) {
			if ($MFA_FLAG==1)
				$heading_name4="รักษาการในตำแหน่ง";
			else
				$heading_name4="รักษาราชการแทน";
			$ws_width = array(5,15,20,8,8,8,20,8,8,8,10,10,20);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","<**2**>$heading_name4","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่","ถึงวันที่","หมายเหตุ");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","","","");
			$ws_head_line3 = array("","","","ประเภท","","","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TBR","TLR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		} elseif($COM_TYPE == "5100" || $COM_TYPE == "1400") {
			$heading_name4="รักษาการในตำแหน่ง";
			$ws_width = array(5,15,20,8,8,8,20,8,8,8,10,20);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","<**2**>$heading_name4","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่","หมายเหตุ");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","","");
			$ws_head_line3 = array("","","","ประเภท","","","","ประเภท","","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TBR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
		} else {
			$heading_name4="รักษาราชการแทน";
			$ws_width = array(5,15,20,8,8,8,20,8,8,8,10);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","<**2**>$heading_name4","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่");
			$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","");
			$ws_head_line3 = array("","","","ประเภท","","","","ประเภท","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
			$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TBR","TLR");
			$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C");
		}
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
		$colshow_cnt = 0;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
				$colshow_cnt++;
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
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_RETIREDATE, 
											b.PER_TYPE, b.LEVEL_NO, a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_RETIREDATE, 
											b.PER_TYPE, b.LEVEL_NO, a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POT_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_RETIREDATE, 
											b.PER_TYPE, b.LEVEL_NO, a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.CMD_NOW
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd;
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	$CMD_ORG3 = $CMD_ORG4 = -1;
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
		if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_RETIREDATE = 	show_date_format($data[PER_RETIREDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
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
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_DATE2 = show_date_format($data[CMD_DATE2],$CMD_DATE_DISPLAY);
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
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME]."\n".$NEW_LEVEL_NAME;
			
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

			if (!$CMD_PM_NAME) {
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_PM_NAME = trim($data2[PM_NAME]);
			}

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO_NAME, a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);
			
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
			
			$NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME, $NEW_LEVEL_NO);
		}elseif($PER_TYPE==2){
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO_NAME, a.POEM_NO, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEM_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];

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
			$cmd = "	select		a.POEMS_NO_NAME, a.POEMS_NO, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);
			$NEW_POS_NO = trim($data2[POEMS_NO]);
			$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
		}elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME]."\n".$NEW_LEVEL_NAME;

			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO_NAME, a.POT_NO, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO_NAME = trim($data2[POT_NO_NAME]);
			$NEW_POS_NO = trim($data2[POT_NO]);
			$NEW_PL_NAME = trim($data2[TP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];

//			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME):"";
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME):"";
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
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
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
			}
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_position_level] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_org3] = $CMD_ORG3;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		$arr_content[$data_count][retire_date] = $PER_RETIREDATE;
		
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
			}
		} 
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_position_level] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no_name] = $NEW_POS_NO_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_date2] = $CMD_DATE2;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;

		$arr_content[$data_count][card_no] =card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);

		$data_count++;
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>" print_r($arr_content); echo "</pre>";
	
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
				$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
				if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$wsdata_align[] = "L";
					$wsdata_align[] = "L";
				} elseif($COM_TYPE == "5100" || $COM_TYPE == "1400") {
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$wsdata_align[] = "L";
				}

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
		
				$xlsRow++;
				$colseq=0;
				$pgrp="";
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
						$colseq++;
					}
				}
			}else{
				$arr_data = (array) null;
				if ($BKK_FLAG==1) {
					$column = 9;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_DATE2;
					$arr_data[] = $CMD_NOTE1;
					$wsdata_align = array("C", "L", "L", "C", "C", "L", "C", "C", "C");
				} else {
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_POSITION_LEVEL;
					$arr_data[] = $NEW_POS_NO_NAME.$NEW_POS_NO;
					$arr_data[] = $CMD_DATE;
					$wsdata_align = array("C", "L", "L", "C", "C", "C", "L", "C", "C", "C", "L");
					if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
						$column = 11;
						$arr_data[] = $CMD_DATE2;
						$arr_data[] = $CMD_NOTE1;
						$wsdata_align[] = "C";
						$wsdata_align[] = "C";
					}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400"){ 
						$column = 10;
						$arr_data[] = $CMD_NOTE1;
						$wsdata_align[] = "C";
					}
				}

				if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

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
						$colseq++;
					}
				}
			} // end if
		} // end for
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
				$wsdata_align[] = "L";
				$wsdata_align[] = "L";
			} elseif($COM_TYPE == "5100" || $COM_TYPE == "1400") {
				$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE";
				$wsdata_align[] = "L";
			}
			$wsdata_border = $wsdata_border_2;

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
		} //end if
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
		if($COM_TYPE == "5090" || $COM_TYPE == "1300" || ($MFA_FLAG==1 && ($COM_TYPE == "5100" || $COM_TYPE == "1400"))){
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}elseif($COM_TYPE == "5100" || $COM_TYPE == "1400")
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งรักษาราชการแทน/รักษาการในตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งรักษาราชการแทน/รักษาการในตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>