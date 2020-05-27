<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_LEVEL_SALP, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd<br>";
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
	
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("คำสั่ง", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	$cmd = " select	CMD_DATE from	PER_COMDTL where	COM_ID=$COM_ID ";
	$db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd<br>";
	$data = $db_dpis->get_array();
	$CMD_DATE = $data[CMD_DATE];
	if (substr($CMD_DATE,4,6) == "-04-01") {
		$search_kf_cycle = 2;
		$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
		$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	} elseif (substr($CMD_DATE,4,6) == "-10-01") {
		$search_kf_cycle = 1;
		$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
		if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
		elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	}

	if ($print_order_by==1) $order_str = "a.CMD_SEQ"; 
	else 
		if($DPISDB=="odbc") $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, CMD_POS_NO_NAME, CLng(CMD_POS_NO)";
		elseif($DPISDB=="oci8")  $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, nvl(e.ORG_CODE,d.ORG_CODE), f.ORG_SEQ_NO, nvl(f.ORG_CODE, nvl(e.ORG_CODE,d.ORG_CODE)), CMD_POS_NO_NAME, to_number(replace(CMD_POS_NO,'-',''))";
		elseif($DPISDB=="mysql")  $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO , CMD_POS_NO_NAME, CMD_POS_NO+0";

	include ("rpt_data_salpromote_comdtl_xlsN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	$company_name = "";
	$SHOW_START_DATE = show_date_format($KF_START_DATE,3);
	if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==8) { //3=เงินตอบแทนพิเศษ
		if ($BKK_FLAG==1)
			$report_title = "บัญชีรายละเอียดการให้$PERSON_TYPE[$COM_PER_TYPE]กรุงเทพมหานครสามัญได้รับเงินตอบแทนพิเศษ||ณ วันที่ $SHOW_START_DATE||แนบท้ายคำสั่ง $COM_NO ลงวันที่ $COM_DATE";    	
		else
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดให้$PERSON_TYPE[$COM_PER_TYPE]ได้รับเงินตอบแทนพิเศษ||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";    	
	} elseif($COM_LEVEL_SALP==7 || $COM_LEVEL_SALP==9) {
		if ($BKK_FLAG==1)
			$report_title = "บัญชีรายละเอียดการปรับอัตราเงินเดือน$PERSON_TYPE[$COM_PER_TYPE]||แนบท้ายคำสั่ง $COM_NO ลงวันที่ $COM_DATE";   
		else
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการ$COM_NAME||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";   
	} else {
		if ($BKK_FLAG==1) {
			$arr_temp = explode("ที่", $COM_NO);
			if ($arr_temp[0]=="กทม. ") $DEPARTMENT_NAME = "กรุงเทพมหานคร";
			$report_title = "บัญชีรายละเอียดการ$COM_NAME แนบท้ายคำสั่ง$DEPARTMENT_NAME ที่ $arr_temp[1] ลงวันที่ $COM_DATE";
		} else
			$report_title = "$COM_TYPE_NAME||บัญชีรายละเอียดการเลื่อนเงินเดือน$PERSON_TYPE[$COM_PER_TYPE]||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";    		
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0406";
	
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
	if ($DEPARTMENT_NAME=="กรมการปกครอง") {
//		$ws_width = array(6,30,15,50,10,15);
//		$ws_head_line1 = array("ลำดับ","ชื่อ","เลขประจำตัว","ตำแหน่งและส่วนราชการ","","");
//		$ws_head_line2 = array("ที่","","ประชาชน","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง");
		if ($COM_LEVEL_SALP==6) { //6=เงินเดือนและเงินตอบแทนพิเศษ
			$ws_width = array(6,30,15,50,10,15,15,10,15,15,10,10,10,50);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ฐานในการ","เปอร์เซ็นต์","จำนวนเงิน","เงินตอบแทน","ให้ได้รับ","ผลการ","หมายเหตุ");
			$ws_head_line2 = array("ที่","","ประชาชน","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","คำนวณ","ที่เลื่อน","ที่เลื่อน","พิเศษ","เงินเดือน","ประเมิน","");
			$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		} elseif ($COM_LEVEL_SALP==3) { //3=เงินตอบแทนพิเศษ
			$ws_width = array(6,30,15,50,10,15,15,15,10,50);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","เงินตอบแทน","ผลการ","หมายเหตุ");
			$ws_head_line2 = array("ที่","","ประชาชน","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","เต็มขั้น","พิเศษ","ประเมิน","");
			$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		} else if ($COM_LEVEL_SALP==9) {
			if ($CARDNO_FLAG==1){
				$ws_width = array(6,30,15,50,10,15,15,15,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","หมายเหตุ");
				$ws_head_line2 = array("ที่","","ประชาชน","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
			}else{
				$ws_width = array(6,30,50,10,15,15,15,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","หมายเหตุ");
				$ws_head_line2 = array("ที่","","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","");
				$ws_colmerge_line1 = array(0,0,1,1,1,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
			}
		} else {
			if ($CARDNO_FLAG==1){
				$ws_width = array(6,30,15,50,10,15,15,15,15,10,10,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
				$ws_head_line2 = array("ที่","","ประชาชน","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
			}else{
				$ws_width = array(6,30,50,10,15,15,15,15,10,10,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
				$ws_head_line2 = array("ที่","","สังกัด / ตำแหน่ง","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
				$ws_colmerge_line1 = array(0,0,1,1,1,0,0,0,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
			}
		}
	} else {
//		$ws_width = array(6,30,15,15,50,10,15,15,15);
//		$ws_head_line1 = array("ลำดับ","ชื่อ","เลขประจำตัว","ประเภท","ตำแหน่งและส่วนราชการ","","");
//		$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง");
//		echo "ไม่ใช่กรมการปกครอง....COM_LEVEL_SALP=$COM_LEVEL_SALP<br>";
		if ($COM_LEVEL_SALP==6) { //6=เงินเดือนและเงินตอบแทนพิเศษ
			if ($CARDNO_FLAG==1) {
				$ws_width = array(6,30,15,15,50,10,17,15,15,10,15,15,10,10,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","เงินตอบแทน","เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
				$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","เต็มขั้น","พิเศษ","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
				$ws_colmerge_line1 = array(0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			} else { 
				$ws_width = array(6,30,15,50,10,17,15,15,10,15,15,10,10,10,50);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","เงินตอบแทน","เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
				$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","เต็มขั้น","พิเศษ","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0,0,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			}
		} else if ($COM_LEVEL_SALP==3) { //3=เงินตอบแทนพิเศษ
			if ($BKK_FLAG==1) {
				$ws_width = array(6,30,45,12,10,10,10,10,10,45);
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือนฐาน","<**2**>เงินค่าตอบแทนพิเศษ","<**2**>","หมายเหตุ");
				$ws_head_line2 = array("ที่","","สังกัด/ตำแหน่ง","ตำแหน่ง","ระดับ","เลขที่","","ร้อยละ 2","ร้อยละ 4","");
				$ws_head_line3 = array("","","","ประเภท","","","","","","");
				$ws_colmerge_line1 = array(0,0,1,1,1,1,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LR","LR","TLR","TLR","TLR","TLR","LR","TLR","TLR","LR");
				$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line3 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line3 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
			} else {
				if ($CARDNO_FLAG==1) {
                                        $ws_width = array(6,30,15,15,50,10,17,15,15,10,50);
					$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","เงินตอบแทน","ผลการ","หมายเหตุ");
					$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","เต็มขั้น","พิเศษ","ประเมิน","");
					$ws_colmerge_line1 = array(0,0,0,0,1,1,1,0,0,0,0);
					$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
					$ws_border_line1 = array("TLR","TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
					$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
					$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1);
					$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1);
					$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0);
					$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
					$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B");
					$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C");
				} else {
					$ws_width = array(6,30,15,50,10,17,15,15,10,50);
					$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","เงินตอบแทน","ผลการ","หมายเหตุ");
					$ws_head_line2 = array("ที่","$CARDNO_TITLE","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","เต็มขั้น","พิเศษ","ประเมิน","");
					$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0);
					$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
					$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR");
					$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
					$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
					$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
					$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
					$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
					$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
					$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
				}
			}
		} else if ($COM_LEVEL_SALP==9) {
			if($CARDNO_FLAG==1){
				$ws_width = array(6,30,17,40,12,13,8,10,10,10,20);
	//			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ค่าตอบแทนพิเศษ","ให้ได้รับ","เงินเดือน","ค่าตอบแทนพิเศษ","หมายเหตุ");
	//			$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนปรับ","ก่อนปรับ","เงินเดือน","ที่ปรับ","ที่ปรับ","");
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ค่าตอบแทน","ให้ได้รับ","หมายเหตุ");
				$ws_head_line2 = array("ที่","","ประชาชน","สังกัด/ตำแหน่ง","ตำแหน่งประเภท","ระดับตำแหน่ง","เลขที่","","พิเศษ","เงินเดือน","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,1,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C");
			}else{
				$ws_width = array(6,30,40,12,13,8,10,10,10,20);
	//			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ค่าตอบแทนพิเศษ","ให้ได้รับ","เงินเดือน","ค่าตอบแทนพิเศษ","หมายเหตุ");
	//			$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนปรับ","ก่อนปรับ","เงินเดือน","ที่ปรับ","ที่ปรับ","");
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ค่าตอบแทน","ให้ได้รับ","หมายเหตุ");
				$ws_head_line2 = array("ที่","","สังกัด/ตำแหน่ง","ตำแหน่งประเภท","ระดับตำแหน่ง","เลขที่","","พิเศษ","เงินเดือน","");
				$ws_colmerge_line1 = array(0,0,1,1,1,1,0,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TB","TLR","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
			}
		} elseif ($COM_LEVEL_SALP==8) {	//ประเภท เงินตอบแทนพิเศษ
                    /*เดิม*/
                    /*
                     $ws_width = array(6,30,15,15,50,10,17,15,15,10,10,10,50,50,50);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","เงินตอบแทน","ฐานในการ","ผลการ","หมายเหตุ");
			$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","พิเศษ","คำนวณ","ประเมิน","");
			$ws_colmerge_line1 = array(0,0,0,0,1,1,1,0,0,0,0,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
                     */
                     /* Release 5.1.0.6 Begin*/
                    if ($CARDNO_FLAG==1) {
                        $ws_width = array(6,30,15,15,50,10,17,15,15,10,10,10,50,50,50);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","เงินตอบแทน","ฐานในการ","ผลการ","หมายเหตุ");
			$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","พิเศษ","คำนวณ","ประเมิน","");
			$ws_colmerge_line1 = array(0,0,0,0,1,1,1,0,0,0,0,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
                    }else{
                        $ws_width = array(6,30,15,50,10,17,15,15,10,10,10,50,50,50);
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","เงินตอบแทน","ฐานในการ","ผลการ","หมายเหตุ");
			$ws_head_line2 = array("ที่","","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","พิเศษ","คำนวณ","ประเมิน","");
			$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0,0,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
                    }
			 /* Release 5.1.0.6 end*/
		}  else {
			if ($BKK_FLAG==1) {
				$ws_width = array(6,30,35,15,15,15,15,30);
				$ws_head_line1 = array("ลำดับที่",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือนฐาน","เงินเดือนที่ได้รับ","หมายเหตุ");
				$ws_head_line2 = array("","","สังกัด/ตำแหน่ง","ระดับ","เลขที่","","","");
				$ws_colmerge_line1 = array(0,0,1,1,1,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TLR","TLR","TLR");
				$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR");
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0);
				$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
				$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
			} else {
				if ($CARDNO_FLAG==1) { //ไว้คิดก่อน	
					$ws_width = array(6,30,15,15,50,10,17,15,15,15,10,10,10,50);
					$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"เลขประจำตัว","ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
					$ws_head_line2 = array("ที่","","ประชาชน","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
					$ws_colmerge_line1 = array(0,0,0,0,1,1,1,0,0,0,0,0,0,0);
					$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_border_line1 = array("TLR","TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
					$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
					$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
					$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
					$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
					$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
				} else { 
					$ws_width = array(6,30,15,50,10,17,15,15,15,10,10,10,50);
					$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"ประเภท",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"เงินเดือน","ให้ได้รับ","จำนวนเงิน","เปอร์เซ็นต์","ฐานในการ","ผลการ","หมายเหตุ");
					$ws_head_line2 = array("ที่","","ตำแหน่ง","ตำแหน่ง/สังกัด","เลขที่","ระดับตำแหน่ง","ก่อนเลื่อน","เงินเดือน","ที่เลื่อน","ที่เลื่อน","คำนวณ","ประเมิน","");
					$ws_colmerge_line1 = array(0,0,0,1,1,1,0,0,0,0,0,0,0);
					$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
					$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
					$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
					$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
					$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
					$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
					$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
				} 
			}
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
		global $worksheet, $xlsRow, $COM_TYPE, $COM_LEVEL_SALP, $DEPARTMENT_NAME;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_wraptext_line3, $ws_rotate_line1, $ws_rotate_line2, $ws_rotate_line3, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
//		echo "count=".count($ws_width)."<br>";
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
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a  
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where			a.COM_ID=$COM_ID
				   order by 		$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f, PER_ORG g
				   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and 
										c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+) and b.DEPARTMENT_ID=g.ORG_ID
				order by 		$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a 
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	$order_str ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd; 
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
		//if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		//}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
	
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {				//	print_order_by คือ ??????????????
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DIFF = $CMD_SALARY - $CMD_OLD_SALARY;
		$CMD_SPSALARY = $data[CMD_SPSALARY];
		$CMD_DATE = trim($data[CMD_DATE]);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_PERCENT = $data[CMD_PERCENT];
		$CMD_STEP = "";
		if ($CMD_NOTE1 == "ข้อ 7 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "0.5 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 8 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544" || 
			$CMD_NOTE1 == "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 14 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1.5 ขั้น";
		if ($CMD_PERCENT > 0) $CMD_STEP = $CMD_PERCENT;

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME =  $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];

		$cmd = " 	select SAH_SALARY_EXTRA from PER_SALARYHIS 
						where PER_ID=$PER_ID and SAH_EFFECTIVEDATE='2014-10-01'
						order by SAH_SALARY_EXTRA desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA] + 0;

		$TMP_SALARY_EXTRA = $CMD_SALARY4 = "";
		if ($CMD_NOTE1) {
			$EXTRA_FLAG = 1;
			$arr_temp = explode("เงินตอบแทนพิเศษ ", $CMD_NOTE1);
			if ($arr_temp[1] > "0.00") $SAH_SALARY_EXTRA = str_replace(",","",$arr_temp[1])+0;
			$TMP_SALARY_EXTRA = (ceil($SAH_SALARY_EXTRA/10))*10 ;
		}
		$CMD_SALARY_EXTRA = $CMD_OLD_SALARY  + $TMP_SALARY_EXTRA;
		if ($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2") {
			$CMD_SALARY4 = (ceil($CMD_SALARY_EXTRA * (4/100)  /10))*10;
		}

		if (substr($CMD_DATE,4,6) == "-04-01") {
			$search_kf_cycle = 1;
			$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
			if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
			elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		} elseif (substr($CMD_DATE,4,6) == "-10-01") {
			$search_kf_cycle = 2;
			$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
			$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		}

		if ($BKK_FLAG==1) {
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = "select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2= $db_dpis2->get_array();
			$MOV_NAME =  trim($data2[MOV_NAME]);
			if ($MOV_NAME=="เลื่อนขั้น 0.5 ขั้น" && $search_kf_cycle == 1) $CMD_NOTE1 = "ครึ่งขั้น";
			elseif ($MOV_NAME=="เลื่อนขั้น 1 ขั้น" && $search_kf_cycle == 2) $CMD_NOTE1 = "หนึ่งขั้น";
			elseif ($MOV_NAME=="เลื่อนขั้น 1.5 ขั้น") $CMD_NOTE1 = "หนึ่งขั้นครึ่ง";
			elseif ($MOV_NAME=="เลื่อนขั้น 2 ขั้น") $CMD_NOTE1 = "สองขั้น";
			elseif (substr($MOV_NAME,0,12)=="ไม่ได้เลื่อน") $CMD_NOTE1 = $MOV_NAME;
		}

		//ข้อมุลการศึกษา
		$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EDU_ENDDATE, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and  EDU_TYPE like '%2%' 
								and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		$db_dpis2->send_cmd($cmd);
//		echo "->".$cmd;
//		$db_dpis->show_error();
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		if (!trim($data2[EN_SHORTNAME])) {
			$EN_SHORTNAME = trim($data2[EN_NAME]);
		} else {
			$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
		}
		if (trim($data2[EM_NAME])) {
			$EM_NAME = "(".trim($data2[EM_NAME]).")";
		}
		$INS_NAME = trim($data2[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
		$EDU_ENDDATE = show_date_format($data2[EDU_ENDDATE],$CMD_DATE_DISPLAY);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="ไทย") $CT_NAME = "";
		
		if($PER_TYPE==1){
			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $PER_ID and KF_CYCLE = $search_kf_cycle and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
//			echo $cmd;
			$TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 		
			$TOTAL_SCORE = $TOTAL_SCORE?number_format($TOTAL_SCORE,2):"-";
		}elseif($PER_TYPE==3){
			$TOTAL_SCORE = "";
			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $PER_ID and 
							KF_START_DATE >= '$KF_START_DATE'  and  KF_END_DATE <= '$KF_END_DATE'
							order by KF_CYCLE ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$TEMP_TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 			 
				$TEMP_TOTAL_SCORE = $TEMP_TOTAL_SCORE?number_format($TEMP_TOTAL_SCORE,2):"-";
				if ($TOTAL_SCORE) $TOTAL_SCORE .= $TEMP_TOTAL_SCORE;
				else $TOTAL_SCORE = $TEMP_TOTAL_SCORE.","; 			 
			}
		}

		if($PER_TYPE==1){
			$cmd = " select PL_CODE, PM_CODE, PT_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			$CMD_PL_CODE = trim($data2[PL_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			if(!$CMD_PM_NAME){
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_PM_NAME = trim($data2[PM_NAME]);
			}

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);//1
                        

			$cmd = " select LAYER_TYPE from PER_LINE where PL_CODE = '$CMD_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;
	
			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
				$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
			} else {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
				$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
			}

			if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
			$CMD_SALARY_MAX = $LAYER_SALARY_MAX;
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $LEVEL_NAME):"";
		}elseif($PER_TYPE==3){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";		//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $LEVEL_NAME):"";	
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} elseif($PER_TYPE==4){
//			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} // end if

		//----------------------------------------------------------------------
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
		if ($CMD_POSITION==$CMD_PM_NAME) $CMD_PM_NAME = "";
		if ($RPT_N){
			if($PER_TYPE==1)
                            $CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)? "$CMD_POSITION" : "") . (trim($CMD_PM_NAME) ?")":"");/*Release 5.1.0.7*/
				/*เดิม*/ //$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)? "$CMD_POSITION$LEVEL_NAME" : "") . (trim($CMD_PM_NAME) ?")":"");
		}else{
			$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME) ?")":"");
		}
		//----------------------------------------------------------------------

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

				$data_count++;
			} // end if

			if($CMD_ORG5 != trim($data[CMD_ORG5]) && trim($data[CMD_ORG5]) && trim($data[CMD_ORG5]) != "-"){
				$CMD_ORG5 = trim($data[CMD_ORG5]);

				$arr_content[$data_count][type] = "ORG_2";
				$arr_content[$data_count][org_name] = $CMD_ORG5;

				$data_count++;
			} // end if
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_ORG4 = $data[CMD_ORG4];
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($print_order_by==1) {	
			$arr_content[$data_count][cmd_position] .= "\n".$CMD_ORG4." ".$CMD_ORG3;
		}
		$arr_content[$data_count][cmd_level] = "ท." . level_no_format($CMD_LEVEL);
		$arr_content[$data_count][cmd_level_name] =$CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY;
		
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY;
		$arr_content[$data_count][cmd_salary4] = $CMD_SALARY4;
		$arr_content[$data_count][cmd_salary_max] = $CMD_SALARY_MAX;
		$arr_content[$data_count][cmd_spsalary] = $CMD_SPSALARY;
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF;
		$arr_content[$data_count][sah_salary_extra] = $SAH_SALARY_EXTRA;
		$arr_content[$data_count][cmd_salary_extra] = $CMD_SALARY_EXTRA;

//		if ($COM_LEVEL_SALP==9){
//			$arr_content[$data_count][cmd_note1] = $EN_NAME;
//		}else{	
		if ($COM_LEVEL_SALP==6){
			$arr_content[$data_count][cmd_note1] = $CMD_NOTE2;
		}else{	
			$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		}		
			$arr_content[$data_count][cmd_step] = $CMD_STEP;
//		}		
		$arr_content[$data_count][cardno] = (($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID); //ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][cmd_midpoint] = $TMP_MIDPOINT;
		$arr_content[$data_count][total_score] = $TOTAL_SCORE;
//		if ($COM_LEVEL_SALP==9){
//			$arr_content[$data_count][cmd_note1] = $EM_NAME;
//			$arr_content[$data_count][cmd_note1] = "";
//		}else{	
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
//		}
		$data_count++;
	} // end while
//	echo "$print_order_by / $DEPARTMENT_NAME / ";
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
			$wsdata_border_3 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			$wsdata_wraptext = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "LR";
				$wsdata_border_2[] = "LR";
				$wsdata_border_3[] = "LRB";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
				$wsdata_wraptext[] = 1;
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$CARDNO = $arr_content[$data_count][cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
//			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SALARY4 = $arr_content[$data_count][cmd_salary4];
			$CMD_SALARY_MAX = $arr_content[$data_count][cmd_salary_max];
			$CMD_SPSALARY = $arr_content[$data_count][cmd_spsalary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$SAH_SALARY_EXTRA = $arr_content[$data_count][sah_salary_extra];
			$CMD_SALARY_EXTRA = $arr_content[$data_count][cmd_salary_extra];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_MIDPOINT = $arr_content[$data_count][cmd_midpoint];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
			
			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
					$ORG_NAME = $arr_content[$data_count][org_name];
//					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

					$arr_data = (array) null;
					if ($DEPARTMENT_NAME=="กรมการปกครอง") {
						$arr_data[] = "";
						$arr_data[] = "";
						if ($CARDNO_FLAG==1) $arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "$ORG_NAME";
						$arr_data[] = "";
						if ($COM_LEVEL_SALP==6) {
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L");
						} elseif ($COM_LEVEL_SALP==3) {
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";

							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L");
						} elseif ($COM_LEVEL_SALP==9) {
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L");
						} else {
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L");
						}
					} else {
						$arr_data[] = "";
						$arr_data[] = "";
						if ($CARDNO_FLAG==1) $arr_data[] = "";
						if ($BKK_FLAG!=1 && $COM_LEVEL_SALP!=9) $arr_data[] = "";
						$arr_data[] = "$ORG_NAME";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						if ($COM_LEVEL_SALP==6) {
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
						} elseif ($COM_LEVEL_SALP==3) {
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L");
						} elseif ($COM_LEVEL_SALP==9) {
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L");
						} elseif ($COM_LEVEL_SALP==8) {	//ประเภท เงินตอบแทนพิเศษ
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							$arr_data[] = "";
							
							$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
						} else {
							if ($BKK_FLAG==1) {
								$arr_data[] = "";

								$wsdata_align = array("L","L","L","L","L","L","L","L");
							} else {
								$arr_data[] = "";
								$arr_data[] = "";
								$arr_data[] = "";
								$arr_data[] = "";
								$arr_data[] = "";

								$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L");
							}
						}
					} // end if 
					$wsdata_border = $wsdata_border_2;
					if($CONTENT_TYPE=="ORG") $wsdata_fontfmt = $wsdata_fontfmt_1;	// ตัวหนา
					else $wsdata_fontfmt = $wsdata_fontfmt_2;	// ตัวธรรมดา
		
					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
						
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
		//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
							$colseq++;
						}
					}				
				} // end if
			} // end if

			if($CONTENT_TYPE=="CONTENT"){
//				$xlsRow++;
				$wsdata_fontfmt = $wsdata_fontfmt_2;	// ธรรมดา ไม่เป็นตัวหนา
				if ($DEPARTMENT_NAME=="กรมการปกครอง") {
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					if ($CARDNO_FLAG==1) $arr_data[] = $CARDNO;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					if ($COM_LEVEL_SALP==6) {
						$arr_data[] = $CMD_SALARY_MAX;
						$arr_data[] = $CMD_SPSALARY;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
						$arr_data[] = $CMD_STEP;
						$arr_data[] = $CMD_MIDPOINT;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;

						$wsdata_align = array("C","L","C","L","C","R","R","R","R","R","R","R","R","L");
					} elseif ($COM_LEVEL_SALP==3) {
						$arr_data[] = $LEVEL_NAME;
						$arr_data[] = $CMD_SALARY_MAX;
						$arr_data[] = $CMD_SPSALARY;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;
						
						$wsdata_align = array("C","L","C","L","C","L","R","R","R","L");
					} elseif ($COM_LEVEL_SALP==9) {
						$arr_data[] = $LEVEL_NAME;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
						$arr_data[] = $CMD_NOTE1;
						
						$wsdata_align = array("C","L","C","L","C","L","R","R","R","L");
					} else {
						$arr_data[] = $LEVEL_NAME;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
						$arr_data[] = $CMD_STEP;
						$arr_data[] = $CMD_MIDPOINT;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;
						
						$wsdata_align = array("C","L","C","L","C","L","R","R","R","R","R","R","L");
					} // end if
				} else { //end if ($DEPARTMENT_NAME=="กรมการปกครอง")
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					if ($CARDNO_FLAG==1) $arr_data[] = $CARDNO;
					if ($BKK_FLAG!=1 && $COM_LEVEL_SALP!=9) $arr_data[] = $TYPE_NAME;
					$arr_data[] = $CMD_POSITION;
					if (($BKK_FLAG==1 && $COM_LEVEL_SALP==3) || ($BKK_FLAG!=1 && $COM_LEVEL_SALP==9)) $arr_data[] = $TYPE_NAME;
					if ($BKK_FLAG==1 || $COM_LEVEL_SALP==9) {
						$arr_data[] = $LEVEL_NAME;
						$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					} else {
						$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
						$arr_data[] = $LEVEL_NAME;
					}
					if ($COM_LEVEL_SALP==6) {
						$arr_data[] = $CMD_SALARY_MAX;
						$arr_data[] = $CMD_SPSALARY;
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
						$arr_data[] = $CMD_STEP;
						$arr_data[] = $CMD_MIDPOINT;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;

						if ($CARDNO_FLAG==1) 
							$wsdata_align = array("C","L","C","C","L","C","L","R","R","R","R","R","R","R","R","L");
						else
							$wsdata_align = array("C","L","C","L","C","L","R","R","R","R","R","R","R","R","L");
					} elseif ($COM_LEVEL_SALP==3) {
						$arr_data[] = $CMD_SALARY_MAX;
						if ($BKK_FLAG==1) {
							$arr_data[] = $CMD_SPSALARY;
							$arr_data[] = $CMD_SPSALARY;
						} else {
							$arr_data[] = $CMD_SPSALARY;
							$arr_data[] = $TOTAL_SCORE;
						}
						$arr_data[] = $CMD_NOTE1;

						if ($BKK_FLAG==1) {
							$wsdata_align = array("C","L","L","C","C","C","R","R","R","L");
						} else {
							if ($CARDNO_FLAG==1) 
								$wsdata_align = array("C","L","C","C","L","C","L","R","R","R","L");
							else
								$wsdata_align = array("C","L","C","L","C","L","R","R","R","L");
						}
					} elseif ($COM_LEVEL_SALP==8) {	//ประเภท เงินตอบแทนพิเศษ
						/*$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
						$arr_data[] = $CMD_SPSALARY;//<<<<<<<<<<<<<<<<<<<<<<
						$arr_data[] = $CMD_STEP;
						$arr_data[] = $CMD_MIDPOINT;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;*/
                                            /* Release 5.1.0.6 Begin*/
                                                $arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_DIFF;
                                                $arr_data[] = $CMD_STEP;
						$arr_data[] = $CMD_SPSALARY;//<<<<<<<<<<<<<<<<<<<<<<
						$arr_data[] = $CMD_MIDPOINT;
						$arr_data[] = $TOTAL_SCORE;
						$arr_data[] = $CMD_NOTE1;
                                            /*  Release 5.1.0.6 End */

						$wsdata_align = array("C","L","C","C","L","C","L","R","R","R","R","R","R","R","L");
					} elseif ($COM_LEVEL_SALP==9) {
						$arr_data[] = $CMD_OLD_SALARY;
						$arr_data[] = $SAH_SALARY_EXTRA;
//						$arr_data[] = $CMD_SALARY;
//						$arr_data[] = $CMD_DIFF;
//						$arr_data[] = $CMD_SPSALARY;
						$arr_data[] = $CMD_SALARY;
						$arr_data[] = $CMD_NOTE2;

						if ($CARDNO_FLAG==1) 
							$wsdata_align = array("C","L","C","L","C","C","C","R","R","R","C");
						else
							$wsdata_align = array("C","L","L","C","C","C","R","R","R","C");
					} else {
//						echo "$ORDER $NAME OLD:$CMD_OLD_SALARY SALARY:$CMD_SALARY DIFF:$CMD_DIFF STEP:$CMD_STEP MIDPOINT:$CMD_MIDPOINT SCORE:$TOTAL_SCORE NOTE:$CMD_NOTE1<br>";
						if ($BKK_FLAG==1) {
							$arr_data[] = $CMD_OLD_SALARY;
							$arr_data[] = $CMD_SALARY;
							$arr_data[] = $CMD_NOTE1;

							$wsdata_align = array("C","L","L","C","L","R","R","L");
						} else {
							$arr_data[] = $CMD_OLD_SALARY;
							$arr_data[] = $CMD_SALARY;
							$arr_data[] = $CMD_DIFF;
							$arr_data[] = $CMD_STEP;
							$arr_data[] = $CMD_MIDPOINT;
							$arr_data[] = $TOTAL_SCORE;
							$arr_data[] = $CMD_NOTE1;

							if ($CARDNO_FLAG==1) 
								$wsdata_align = array("C","L","C","C","L","C","L","R","R","R","R","R","R","L");
							else
								$wsdata_align = array("C","L","C","L","C","L","R","R","R","R","R","R","L");
						}
					} // end if
//					$wsdata_border = $wsdata_border_1;
				} // end if

				if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;
//				$wsdata_border = $wsdata_border_1;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
	//					echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
						$colseq++;
					}
				}
			} // end if
		} // end for				

//		echo "COM_NOTE=$COM_NOTE<br>";
		if($COM_NOTE){
//			$xlsRow++;
			if ($DEPARTMENT_NAME=="กรมการปกครอง") {
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				if ($COM_LEVEL_SALP==6) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L");
				} elseif ($COM_LEVEL_SALP==3) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L");
				} elseif ($COM_LEVEL_SALP==9) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L");
				} else {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L");
				}
			} else {
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				$arr_data[] = "<**1**>";
				if ($BKK_FLAG!=1 || $CARDNO_FLAG==1) $arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				if ($COM_LEVEL_SALP==6) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
				} elseif ($COM_LEVEL_SALP==3) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L");
				} elseif ($COM_LEVEL_SALP==9) {
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L");
				} elseif ($COM_LEVEL_SALP==8) {	//ประเภท เงินตอบแทนพิเศษ
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					$arr_data[] = "<**1**>";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
				} else {
					if ($BKK_FLAG==1) {
						$wsdata_align = array("L","L","L","L","L","L","L","L");
					} else {
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						$arr_data[] = "<**1**>";
						
						$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L");
					}
				}
//				$wsdata_border = $wsdata_border_3;
			} // end if

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
		} // end if
	}else{
		$xlsRow = 0;
		if ($DEPARTMENT_NAME=="กรมการปกครอง") {
			$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if ($COM_LEVEL_SALP==6) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} elseif ($COM_LEVEL_SALP==3) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} elseif ($COM_LEVEL_SALP==3) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} else {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} else {
			$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if ($COM_LEVEL_SALP==6) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} elseif ($COM_LEVEL_SALP==3) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} elseif ($COM_LEVEL_SALP==9) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} elseif ($COM_LEVEL_SALP==8) {	//ประเภท เงินตอบแทนพิเศษ
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} else {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	if ($COM_LEVEL_SALP==9) {
		header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนข้าราชการ.xls\"");
		header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งปรับอัตราเงินเดือนข้าราชการ.xls\"");
	} else {
		header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งเลื่อนเงินเดือนข้าราชการ.xls\"");
		header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งเลื่อนเงินเดือนข้าราชการ.xls\"");
	}
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>