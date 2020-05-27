<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") $row_h = 409.5;
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("rpt_data_transfer_req_comdtlN_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

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
	$report_code = "P0203";

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
		if($COM_TYPE=="0101" || $COM_TYPE=="5011"){	//สอบแข่งขันได้
			if ($BKK_FLAG==1) {
				$ws_head_line1 = array("","","","<**1**>สอบแข่งขันได้","<**1**>สอบแข่งขันได้","<**1**>สอบแข่งขันได้","<**2**>ตำแหน่งและสังกัดที่บรรจุ","<**2**>ตำแหน่งและสังกัดที่บรรจุ","<**2**>ตำแหน่งและสังกัดที่บรรจุ","<**2**>ตำแหน่งและสังกัดที่บรรจุ","","","");
				$ws_head_line2 = array("ลำดับ",$FULLNAME_HEAD,"วุฒิ/สาขา","ตำแหน่ง","ลำดับที่","ประกาศผล","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
				$ws_head_line3 = array("ที่","","","","","การสอบ","","ประเภท","","","","","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,0,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TBR","TBL","TB","TB","TBR","TLR","TLR","TLR");
				$ws_border_line2 = array("LR","LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR","LR");
				$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			} else {
				$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"วุฒิ/สาขา/","<**1**>สอบแข่งขันได้","<**1**>สอบแข่งขันได้","<**1**>สอบแข่งขันได้",$COM_HEAD_02."ที่บรรจุแต่งตั้ง",$COM_HEAD_02."ที่บรรจุแต่งตั้ง",$COM_HEAD_02."ที่บรรจุแต่งตั้ง",$COM_HEAD_02."ที่บรรจุแต่งตั้ง",$COM_HEAD_02."ที่บรรจุแต่งตั้ง","","");
				$ws_head_line2 = array("ที่","(วันเดือนปีเกิด)","สถานศึกษา","ตำแหน่ง","ลำดับที่","ประกาศผล","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
				$ws_head_line3 = array("","เลขประจำตัวประชาชน","","","","การสอบของ","","ประเภท","","","","","");
				$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,0,0);
				$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TBR","TBL","TB","TB","TB","TBR","TLR","TLR");
				$ws_border_line2 = array("LR","LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR");
				$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			}
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
			$ws_width = array(4.86, 21.71, 16, 17.71, 5.14, 14.14, 18.86, 6.86, 6.14, 6.57, 7.86, 9.43, 18.14);
		}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){//บรรจุผู้ได้รับคัดเลือก
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,"วุฒิ/สาขา/",$COM_HEAD_01."ที่บรรจุ",$COM_HEAD_01."ที่บรรจุ",$COM_HEAD_01."ที่บรรจุ",$COM_HEAD_01."ที่บรรจุ",$COM_HEAD_01."ที่บรรจุ","","");
			$ws_head_line2 = array("ที่","(วันเดือนปีเกิด)","สถานศึกษา","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
			$ws_head_line3 = array("","เลขประจำตัวประชาชน","","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TBR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","LR","TLR","TLR","TLR","TLR","TLR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
			$ws_width = array(4.71, 23.14, 24.29, 20.43, 9.57, 9.57, 9.57, 9.57, 11.71, 16.43);
		}elseif(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
			if($COM_TYPE == "แต่งตั้งรักษาราชการแทน"){
				$heading_name4="รักษาราชการแทน";
			}elseif($COM_TYPE == "แต่งตั้งให้รักษาการในตำแหน่ง"){
				$heading_name4="รักษาการในตำแหน่ง";
			}
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"<**2**>$heading_name4ุ","<**2**>$heading_name4ุ","<**2**>$heading_name4ุ","<**2**>$heading_name4ุ","","","");
			$ws_head_line2 = array("ที่","","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","ตั้งแต่วันที่","ถึงวันที่","หมายเหตุ");
			$ws_head_line3 = array("","","","ประเภท","","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,0,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TB","TB","TBR","TBL","TB","TB","TB","TLR","TLR","TLR");
			$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
			$ws_width = array(6,45,60,65,10,25,10,10,10,10,10,100);
		}else if($COM_TYPE=="0302" || $COM_TYPE=="5032"){   //รับโอนข้าราชการผู้ได้รับวุฒิเพิ่มขึ้น
			$ws_head_line1 = array("ลำดับ",$FULLNAME_HEAD."/","วุฒิที่ได้รับเพิ่มขึ้น/",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม",$COM_HEAD_01."เดิม","<**2**>สอบแข่งขันได้","<**2**>สอบแข่งขันได้","<**2**>สอบแข่งขันได้",$COM_HEAD_03."ที่รับโอน",$COM_HEAD_03."ที่รับโอน",$COM_HEAD_03."ที่รับโอน",$COM_HEAD_03."ที่รับโอน",$COM_HEAD_03."ที่รับโอน","","");
			$ws_head_line2 = array("ที่","เลขประจำตัวประชาชน","สถานศึกษา/วันที่","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เงินเดือน","ตำแหน่ง","ลำดับที่","ประกาศผล","ตำแหน่ง/สังกัด","ตำแหน่ง","ระดับ","เลขที่","เงินเดือน","ตั้งแต่วันที่","หมายเหตุ");
			$ws_head_line3 = array("","","สำเร็จการศึกษา","","ประเภท","","","","","การสอบของ","","ประเภท","","","","","");
			$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,0,0);
			$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TBR","TBL","TB","TBR","TBL","TB","TB","TB","TBR","TLR","TLR");
			$ws_border_line2 = array("LR","LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR");
			$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			$ws_width = array(5,15,12,20,8,8,8,50,15,25,55,20,12,10,12,16,95);
		}else{ //########
			$ws_width = (array) null;
			if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 8;
				$ws_width[7] = 10;
				$ws_width[8] = 10;
				$ws_width[9] = 20;
				$ws_width[10] = 8;
				$ws_width[11] = 8;
				$ws_width[12] = 6;
				$ws_width[13] = 8;
				$ws_width[14] = 10;
				$ws_width[15] = 20;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}elseif(in_array($COM_TYPE, array("0107","5018"))){
				$ws_width[0] = 3.86;
				$ws_width[1] = 20.14;
				$ws_width[2] = 14.43;
				$ws_width[3] = 18.14;
				$ws_width[4] = 7.43;
				$ws_width[5] = 9.14;
				$ws_width[6] = 7.71;
				$ws_width[7] = 9;
				$ws_width[8] = 18.14;
				$ws_width[9] = 7.43;
				$ws_width[10] = 9.14;
				$ws_width[11] = 6;
				$ws_width[12] = 7.43;
				$ws_width[13] = 9.71;
				$ws_width[14] = 12.43;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}elseif(in_array($COM_TYPE, array("0108","5019"))){
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 8;
				$ws_width[7] = 10;
				$ws_width[8] = 20;
				$ws_width[9] = 8;
				$ws_width[10] = 8;
				$ws_width[11] = 6;
				$ws_width[12] = 8;
				$ws_width[13] = 10;
				$ws_width[14] = 20;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}elseif(in_array($COM_TYPE, array("0109","5015"))){	//รับโอน พนง. ส่วนท้องถิ่น
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 30;
				$ws_width[7] = 25;
				$ws_width[8] = 14;
				$ws_width[9] = 33;
				$ws_width[10] = 30;
				$ws_width[11] = 40;
				$ws_width[12] = 14;
				$ws_width[13] = 15;
				$ws_width[14] = 20;
				$ws_width[15] = 15;
				$ws_width[16] = 20;
				$ws_width[17] = 100;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}elseif($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031"))){	//รับโอนข้าราชการพลเรือนสามัญ
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 6.43;
				$ws_width[7] = 8;
				$ws_width[8] = 23.71;
				$ws_width[9] = 6.57;
				$ws_width[10] = 6.14;
				$ws_width[11] = 6.43;
				$ws_width[12] = 7.71;
				$ws_width[13] = 9.43;
				$ws_width[14] = 18.14;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}elseif(in_array($COM_TYPE, array("0303","5033"))){	//รับโอนข้าราชการพลเรือนสามัญมาดำรงตำแหน่งในระดับที่สูงขึ้น
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 6.43;
				$ws_width[7] = 8;
				$ws_width[8] = 12.29;
				$ws_width[9] = 23.71;
				$ws_width[10] = 6.57;
				$ws_width[11] = 6.14;
				$ws_width[12] = 6.43;
				$ws_width[13] = 7.71;
				$ws_width[14] = 9.43;
				$ws_width[15] = 18.14;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			}else{	//--0104,0301			//โอน
				$ws_width[0] = 4.86;
				$ws_width[1] = 18.37;
				$ws_width[2] = 12.29;
				$ws_width[3] = 21.57;
				$ws_width[4] = 6.57;
				$ws_width[5] = 6.14;
				$ws_width[6] = 8;
				$ws_width[7] = 23.71;
				$ws_width[8] = 6.57;
				$ws_width[9] = 6.14;
				$ws_width[10] = 6.43;
				$ws_width[11] = 7.71;
				$ws_width[12] = 9.43;
				$ws_width[13] = 18.14;
				$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
				$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			}
			$ws_colmerge_line1 = (array) null;
			$ws_border_line1 = (array) null;
			$ws_head_line1 = (array) null;
			$ws_head_line1[] = "ลำดับ";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			$ws_head_line1[] = $FULLNAME_HEAD;	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			if ($BKK_FLAG==1)
				$ws_head_line1[] = "วุฒิ/สาขา";	
			else
				$ws_head_line1[] = "วุฒิ/สาขา/";	
			$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			$ws_head_line1[] = $COM_HEAD_01."เดิม";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
			$ws_head_line1[] = $COM_HEAD_01."เดิม";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
			$ws_head_line1[] = $COM_HEAD_01."เดิม";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
			if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
				$ws_head_line1[] = $COM_HEAD_01."เดิม";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
			}
			$ws_head_line1[] = $COM_HEAD_01."เดิม";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBR";
			if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
				if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
					$heading_name7="ออกจาก";
					$heading_name8="พ้นจากราชการ";
				}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
					$heading_name7="ออกไปปฏิบัติงาน";
					$heading_name8="พ้นจากการปฏิบัติงาน";
				}
				$ws_head_line1[] = "$heading_name7";		$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "$heading_name8";		$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			}elseif(in_array($COM_TYPE, array("0107","5018","0108","5019"))){
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBL";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่บรรจุ";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			}elseif(in_array($COM_TYPE, array("0109","5015"))){
				$ws_head_line1[] = "<**2**>สอบแข่งขันได้";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBL";
				$ws_head_line1[] = "<**2**>สอบแข่งขันได้";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "<**2**>สอบแข่งขันได้";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "<**2**>สอบแข่งขันได้";	$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBR";
				$ws_head_line1[] = $COM_HEAD_03."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBL";
				$ws_head_line1[] = $COM_HEAD_03."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_03."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_03."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_03."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			}elseif(in_array($COM_TYPE, array("0303","5033"))){
				$ws_head_line1[] = "ดำรงตำแหน่ง";		$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอนมาเลื่อน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBL";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอนมาเลื่อน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอนมาเลื่อน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอนมาเลื่อน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอนมาเลื่อน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			}else{
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TBL";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = $COM_HEAD_02."ที่รับโอน";		$ws_colmerge_line1[] = 1;	$ws_border_line1[] = "TB";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
				$ws_head_line1[] = "";	$ws_colmerge_line1[] = 0;	$ws_border_line1[] = "TLR";
			}
	
			//แถวที่ 2 ------------------------------
			$ws_head_line2 = (array) null;
			$ws_border_line2 = (array) null;
			$ws_head_line2[] = "ที่";		$ws_border_line2[] = "LR";
			if ($BKK_FLAG==1)
				$ws_head_line2[] = "";
			else
				$ws_head_line2[] = "(วัน เดือน ปีเกิด)";		
			$ws_border_line2[] = "LR";
			if(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
				$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "LR";
				$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "";		$ws_border_line2[] = "LR";
				$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
				$ws_head_line2[] = "";		$ws_border_line2[] = "LR";
			}else{
				if ($BKK_FLAG==1)
					$ws_head_line2[] = "";
				else
					$ws_head_line2[] = "สถานศึกษา";		
				$ws_border_line2[] = "LR";
				$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
				$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
				if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
				}
				$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
				
				if(in_array($COM_TYPE, array("0105","0106", "5016", "5017"))){
					if($COM_TYPE=="0105" || $COM_TYPE=="5016"){
						$heading_name7="ราชการเมื่อ";
						$heading_name8="ทหารเมื่อ";
					}elseif($COM_TYPE=="0106" || $COM_TYPE=="5017"){
						$heading_name7="ตามมติ ครม.เมื่อ";
						$heading_name8="ตามมติ ครม. เมื่อ";
					}
					$ws_head_line2[] = "$heading_name7";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "$heading_name8";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "หมายเหตุ";		$ws_border_line2[] = "LR";
				}elseif(in_array($COM_TYPE, array("0107","5018","0108","5019"))){
					$ws_head_line2[] = "ลาออกเมื่อ";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "หมายเหตุ";		$ws_border_line2[] = "LR";
				}elseif(in_array($COM_TYPE, array("0109","5015"))){
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ลำดับที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ประกาศผลการสอบของ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "หมายเหตุ";		$ws_border_line2[] = "LR";
				}elseif(in_array($COM_TYPE, array("0303","5033"))){
					$ws_head_line2[] = "ในระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "หมายเหตุ";		$ws_border_line2[] = "LR";
				}else{
					$ws_head_line2[] = "ตำแหน่ง/สังกัด";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตำแหน่ง";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ระดับ";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เลขที่";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "เงินเดือน";		$ws_border_line2[] = "TLR";
					$ws_head_line2[] = "ตั้งแต่วันที่";		$ws_border_line2[] = "LR";
					$ws_head_line2[] = "หมายเหตุ";		$ws_border_line2[] = "LR";
				}
			}

			//แถวที่ 3 ------------------------------
			$ws_head_line3 = (array) null;
			$ws_border_line3 = (array) null;
			$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
			$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
			if(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
			}else{
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}
				
				if(in_array($COM_TYPE, array("0105","0106", "5016", "5017"))){
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}elseif(in_array($COM_TYPE, array("0107","5018","0108","5019"))){
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}elseif(in_array($COM_TYPE, array("0109","5015"))){
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}elseif(in_array($COM_TYPE, array("0303","5033"))){
					$ws_head_line3[] = "ปัจจุบันเมื่อ";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}else{
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "ประเภท";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
					$ws_head_line3[] = "";		$ws_border_line3[] = "LBR";
				}
			}
		} // end if
		$ws_colmerge_line2 = (array) null;
		$ws_fontfmt_line1 = (array) null;
		$ws_headalign_line1 = (array) null;
		for($k=0; $k<count($ws_head_line1); $k++) {
			$ws_colmerge_line2[] = 0;
			$ws_fontfmt_line1[] = "B";
			$ws_headalign_line1[] = "C";
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
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;
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
/*		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line1, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line1, $ws_colmerge_line1, $ws_headalign_line1, $ws_wraptext_line1, $ws_rotate_line1);
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line2, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line2, $ws_wraptext_line2, $ws_rotate_line2);
		if ($ws_head_line3) {
			// loop พิมพ์ head บรรทัดที่ 3
			$xlsRow++;
		$result = wswrite_aggregate_merge($worksheet, $xlsRow, "xlsFmtTableHeader", $ws_fontfmt_line1, $ws_head_line3, $column_function, $arr_column_sel, $arr_column_map, $ws_border_line2, $ws_colmerge_line2, $ws_headalign_line2, $ws_wraptext_line2, $ws_rotate_line2);
		}
*/
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.CMD_DATE3
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.CMD_DATE3
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.TP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG2, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW__NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_DATE2, a.CMD_NOTE1, a.CMD_NOTE2, CMD_POS_NO_NAME, CMD_POS_NO, a.CMD_NOW, a.CMD_DATE3
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
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
		if ($CARDNO_FLAG==1){ // ถ้าเลือกโชวเลขบัตรหลังชื่อ
		$PER_CARDNO = $data[PER_CARDNO];
	    $cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$EDU_TYPE = "%1%";
		if($DPISDB=="odbc"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
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
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="ไทย") $CT_NAME = "";
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		//ในกรณีที่มี CMD_PM_NAME
		if(is_array($arr_temp)){
			$CMD_POSITION = $arr_temp[0];
			$CMD_PM_NAME = $arr_temp[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		$CMD_ORG2 = $data[CMD_ORG2];
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_ORG4 = $data[CMD_ORG4];
		$CMD_ORG5 = $data[CMD_ORG5];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_DATE2 = show_date_format($data[CMD_DATE2],$CMD_DATE_DISPLAY);
		$CMD_DATE3 = show_date_format($data[CMD_DATE3],$CMD_DATE_DISPLAY);
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

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, a.POS_NO_NAME, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO_NAME]).trim($data2[POS_NO]);
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
			$PL_NAME = $data2[PN_NAME];

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
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==3){
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];

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
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
		}elseif($PER_TYPE==4){
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];

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
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3])  && trim($data[CMD_ORG3]) != "-"){
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
		
		$cmd = " select POH_EFFECTIVEDATE as LEVEL_EFFECTIVEDATE
						from   PER_POSITIONHIS a, PER_MOVMENT b
						where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=3
						order by POH_EFFECTIVEDATE DESC ";
//		$cmd = " select 	min(POH_EFFECTIVEDATE) as LEVEL_EFFECTIVEDATE 
//						 from 		PER_POSITIONHIS 
//						 where 	PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[LEVEL_EFFECTIVEDATE],$CMD_DATE_DISPLAY);
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($COMMAND_PRT==1 && $BKK_FLAG!=1) {
			if ($PER_BIRTHDATE) $arr_content[$data_count][name] .= "\n(".trim($PER_BIRTHDATE).")";
			if ($cardID) $arr_content[$data_count][name] .= "\n".trim($cardID);
		}
		$arr_content[$data_count][educate] = $EN_SHORTNAME;
		if ($COMMAND_PRT==1) {
			if ($EM_NAME) $arr_content[$data_count][educate] .= "\n".trim($EM_NAME);
			if ($INS_NAME) $arr_content[$data_count][educate] .= "\n".trim($INS_NAME);
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "\n".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "\n".trim($CT_NAME);
		}
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
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
				if ($CMD_ORG2 && $MFA_FLAG==1) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG2);
			}
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_position_level] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		if ($COMMAND_PRT==1) {
			if ($NEW_ORG_NAME_2 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_2);
			if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_1);
			if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME);
			if ($DEPARTMENT_NAME && $MFA_FLAG==1) $arr_content[$data_count][new_position] .= "\n".trim($DEPARTMENT_NAME);
		} 
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE?$NEW_POSITION_TYPE:"-";
		$arr_content[$data_count][new_position_level] = $NEW_LEVEL_NAME?$NEW_LEVEL_NAME:"-";
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_date2] = $CMD_DATE2;
		$arr_content[$data_count][cmd_date3] = $CMD_DATE3;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;

		$data_count++;

		if ($print_order_by==1 && $COMMAND_PRT!=1) {
			$arr_content[$data_count][type] = "CONTENT";
			if ($EM_NAME)
				$arr_content[$data_count][educate] = $EM_NAME."\n".$INS_NAME;
			else
				$arr_content[$data_count][educate] = $INS_NAME;
			if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "\n".trim($EDU_HONOR);
			if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "\n".trim($CT_NAME);
			//$arr_content[$data_count][new_position] = $NEW_ORG_NAME;/*ของเดิม*/
            /*http://dpis.ocsc.go.th/Service/node/2507*/
            if ($NEW_ORG_NAME_2 && $SESS_DEPARTMENT_NAME!="กรมชลประทาน") $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_2);
			if ($NEW_ORG_NAME_1) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME_1);
			if ($NEW_ORG_NAME) $arr_content[$data_count][new_position] .= "\n".trim($NEW_ORG_NAME);
			if ($DEPARTMENT_NAME && $MFA_FLAG==1) $arr_content[$data_count][new_position] .= "\n".trim($DEPARTMENT_NAME);
			//$arr_content[$data_count][card_no] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
	//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

			$arr_content[$data_count][name] = "(". (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE).")";
			if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")";
			$data_count++;
		}
		
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
			$wsdata_border_3[] = "LBR";
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
			$EDUCATE = $arr_content[$data_count][educate];
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_position_level];
			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_POSITION_LEVEL = $arr_content[$data_count][new_position_level];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_position_level];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_DATE2 = $arr_content[$data_count][cmd_date2];
			$CMD_DATE3 = $arr_content[$data_count][cmd_date3];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($COM_TYPE=="0101" || $COM_TYPE=="5011"){
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "";
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

					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L"); 
					$wsdata_border = $wsdata_border_2;
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $EDUCATE;
					$arr_data[] = $POSITION;
					$arr_data[] = $CMD_ACC_NO;
					$arr_data[] = $CMD_ACCOUNT;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_POSITION_LEVEL;
					$arr_data[] = $NEW_POS_NO;
					$arr_data[] = $CMD_SALARY;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_NOTE1;

					$wsdata_align = array("C","L","L","L","C","L","L","C","C","C","R","C","L"); 
					$wsdata_border = $wsdata_border_1;
				} // end if
			}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					
					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L"); 
					$wsdata_border = $wsdata_border_2;
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $EDUCATE;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_POSITION_LEVEL;
					$arr_data[] = $NEW_POS_NO;
					$arr_data[] = $CMD_SALARY;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_NOTE1;
					
					$wsdata_align = array("C","L","L","L","C","C","C","R","C","L");
					$wsdata_border = $wsdata_border_1;
				} // end if
			}elseif(in_array($COM_TYPE, array("แต่งตั้งรักษาราชการแทน","แต่งตั้งให้รักษาการในตำแหน่ง"))){
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "";
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

					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L");
					$wsdata_border = $wsdata_border_2;
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_POSITION_LEVEL;
					$arr_data[] = $CMD_ACC_NO;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_DATE2;
					$arr_data[] = $CMD_NOTE1;

					$wsdata_align = array("C","L","C","C","L","L","L","C","L","R","C","L");
					$wsdata_border = $wsdata_border_1;
				} // end if
			}elseif($COM_TYPE=="0302" || $COM_TYPE=="5032"){  //รับโอนข้าราชการผู้ได้รับวุฒิเพิ่มขึ้น
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "";
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

					$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
					$wsdata_border = $wsdata_border_2;
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $EDUCATE;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
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

					$wsdata_align = array("C", "L", "L", "L", "C", "C", "R", "L", "R", "L", "L", "C", "C", "C", "R", "C", "L");
					$wsdata_border = $wsdata_border_1;
				} // end if
			}else{
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "";
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
					if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}elseif(in_array($COM_TYPE, array("0107","5018"))){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}elseif(in_array($COM_TYPE, array("0108","5019"))){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}elseif(in_array($COM_TYPE, array("0109","5015"))){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}elseif(in_array($COM_TYPE, array("0303","5033"))){
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}else{
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
						
						$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}
					$wsdata_border = $wsdata_border_1;
				}else{
					$arr_data = (array) null;
					$wsdata_align = (array) null;
					if(in_array($COM_TYPE, array("0108","5019"))){
						$arr_data[] = $ORDER; 	$wsdata_align[] = "C";
						$arr_data[] = $NAME; 	$wsdata_align[] = "L";
						$arr_data[] = $EDUCATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $CMD_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_LEVEL_NAME; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_OLD_SALARY; 	$wsdata_align[] = "R";
					}else{
						$arr_data[] = $ORDER; 	$wsdata_align[] = "C";
						$arr_data[] = $NAME; 	$wsdata_align[] = "L";
						$arr_data[] = $EDUCATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $CMD_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_LEVEL_NAME; 	$wsdata_align[] = "C";
						if($MFA_FLAG==1 && in_array($COM_TYPE, array("0301","5031","0303","5033"))) {
							$arr_data[] = $CMD_POS_NO; 	$wsdata_align[] = "C";
						}
						$arr_data[] = $CMD_OLD_SALARY; 	$wsdata_align[] = "R";
					}
					if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
						if($COM_TYPE=="0105" || $COM_TYPE=="5016" || $COM_TYPE=="0106" || $COM_TYPE=="5017"){
							$PER_DATE=$CMD_DATE3;
						}
						$arr_data[] = $CMD_DATE2; 	$wsdata_align[] = "C";
						$arr_data[] = $PER_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}elseif(in_array($COM_TYPE, array("0107","5018"))){
						$arr_data[] = $CMD_DATE2; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}elseif(in_array($COM_TYPE, array("0108","5019"))){
						$arr_data[] = $CMD_DATE2; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}elseif(in_array($COM_TYPE, array("0109","5015"))){
						$arr_data[] = $POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $CMD_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $CMD_ACC_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_ACCOUNT; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}elseif(in_array($COM_TYPE, array("0303","5033"))){
						$arr_data[] = $LEVEL_EFFECTIVEDATE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}else{
						$arr_data[] = $NEW_POSITION; 	$wsdata_align[] = "L";
						$arr_data[] = $NEW_POSITION_TYPE; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POSITION_LEVEL; 	$wsdata_align[] = "C";
						$arr_data[] = $NEW_POS_NO; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_SALARY; 	$wsdata_align[] = "R";
						$arr_data[] = $CMD_DATE; 	$wsdata_align[] = "C";
						$arr_data[] = $CMD_NOTE1; 	$wsdata_align[] = "L";
					}
					$wsdata_border = $wsdata_border_1;
				} // end if
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
			if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน" && $data_count < count($arr_content)-1) {
				$xlsRow++;
			}
		} // end for

		if($COM_NOTE){
			$COM_NOTE = (($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NOTE):$COM_NOTE);
			if($COM_TYPE=="0101" || $COM_TYPE=="5011"){
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
			}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){
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
			}else{
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
				if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}elseif(in_array($COM_TYPE, array("0107","5018"))){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}elseif(in_array($COM_TYPE, array("0108","5019"))){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}elseif(in_array($COM_TYPE, array("0109","5015"))){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}elseif($COM_TYPE=="0302" || $COM_TYPE=="5032"){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}elseif($COM_TYPE=="0303" || $COM_TYPE=="5033"){
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}else{
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
					$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
				}
				
				$wsdata_align = (array) null;
				$wsdata_cmerge = (array) null;
				for($i = 0; $i < count($arr_data); $i++) {
					$wsdata_align[] = "L";
					$wsdata_cmerge[] = 1;
				}
				$wsdata_border = $wsdata_border_2;
			} // end if

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_cmerge[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1), $wsdata_align[0]));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border, merge=".$wsdata_cmerge[$arr_column_map[$i]]."<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end if
	}else{
		if($COM_TYPE=="0101" || $COM_TYPE=="5011"){
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
		}elseif(in_array($COM_TYPE, array("0102", "0103", "0504", "0604", "0704", "0803","5012","5013"))){
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
			if(in_array($COM_TYPE, array("0105", "0106", "5016", "5017"))){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}elseif(in_array($COM_TYPE, array("0107","5018"))){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}elseif(in_array($COM_TYPE, array("0108","5019"))){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}elseif(in_array($COM_TYPE, array("0109","5015"))){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}elseif($COM_TYPE=="0302" || $COM_TYPE=="5032"){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}elseif(in_array($COM_TYPE, array("0109","5015"))){
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}else{
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} // end if
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งบรรจุรับโอน.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งบรรจุรับโอน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>