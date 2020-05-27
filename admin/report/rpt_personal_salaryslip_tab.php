<?
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");	// เกี่ยวกับการตัดคำภาษาไทย
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if(trim($_GET[SLIP_ID]))		$SLIP_ID=trim($_GET[SLIP_ID]);

	//date_default_timezone_set('Asia/Bangkok');    # Set default timezone 

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_personal_salaryslip_tab.pdf";	// กำหนดชื่อของ output
	if (!$font) $font = "AngsanaUPC";	// ถ้ามีค่า $font ส่งเข้ามาก็ใช้ค่านั้น  แต่ถ้าไม่มี ก็ให้ = AngsanaUPC

	$unit="mm";	// ให้หน่อยกระดาษเป็น มิลลิเมตร
	$paper_size="A4";	// ขนาดกระดาษ
	$lang_code="TH";	// รหัสภาษา ไทย
	$company_name = "";	//  ชื่อบริษัทจะพิมพ์ที่ หัวกระดาษซ้ายโดยอัตโนมัติ ถ้ามี
	$report_title = "";	//  ชื่อบริษัทจะพิมพ์ที่ หัวกระดาษตรงกลางโดยอัตโนมัติ ถ้ามีค่า รูปแบบแบ่งบรรทัดโดย || เช่น รายงานทดสอบ||สาขาที่ 1
	$report_code = "SLIP";	// รหัสรายงาน
	$orientation='L';	// L แนวนอน  P แนวตั้ง

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);		// เรียก class PDF
	
	// กำหนดค่าเริ่มต้นรายงาน
 	$pdf->Open();
	$pdf->SetMargins(10,10,10);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//#### ดึงลายเซ็น ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
		$count_pic_sign=$db_dpis->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data = $db_dpis->get_array();
		$TMP_PIC_SEQ = $data[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
			if (trim($PER_CARDNO) && trim($PER_CARDNO) != "NULL") {
				$TMP_PIC_NAME = $data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
				//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
			} else {
				$TMP_PIC_NAME = $data[PER_PICPATH].$data[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
				//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
			}
			if(file_exists("../".$TMP_PIC_NAME)){
				$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
			}
		} //end count	
		return $PIC_SIGN;
	}
	
	$cmd = "select	SLIP_YEAR, SLIP_MONTH, PN_NAME, PER_NAME, PER_SURNAME, DEPARTMENT_NAME, ORG_NAME, 
									BANK_NAME, BRANCH_NAME, PER_BANK_ACCOUNT, INCOME_01, INCOME_02, INCOME_03,
									INCOME_04, INCOME_05, INCOME_06, INCOME_07, INCOME_08, INCOME_09, INCOME_10, INCOME_11,
									INCOME_12, INCOME_13, INCOME_14, INCOME_15, INCOME_16, INCOME_17, INCOME_18, INCOME_19,
									INCOME_20, INCOME_NAME_01, EXTRA_INCOME_01, INCOME_NAME_02, EXTRA_INCOME_02, 
									INCOME_NAME_03, EXTRA_INCOME_03, INCOME_NAME_04, EXTRA_INCOME_04, OTHER_INCOME, 
									TOTAL_INCOME,	DEDUCT_01,	DEDUCT_02,	DEDUCT_03,	DEDUCT_04,	DEDUCT_05,	DEDUCT_06,	
									DEDUCT_07,	DEDUCT_08,	DEDUCT_09,	DEDUCT_10,	DEDUCT_11,	DEDUCT_12,	DEDUCT_13,	
									DEDUCT_14,	DEDUCT_15,	DEDUCT_16, DEDUCT_NAME_01, EXTRA_DEDUCT_01, DEDUCT_NAME_02, 
									EXTRA_DEDUCT_02, DEDUCT_NAME_03, EXTRA_DEDUCT_03, DEDUCT_NAME_04, EXTRA_DEDUCT_04, 
									DEDUCT_NAME_05, EXTRA_DEDUCT_05, DEDUCT_NAME_06, EXTRA_DEDUCT_06, DEDUCT_NAME_07, 
									EXTRA_DEDUCT_07, DEDUCT_NAME_08, EXTRA_DEDUCT_08, OTHER_DEDUCT, TOTAL_DEDUCT, 
									NET_INCOME, UPDATE_DATE
					from		PER_SLIP
					where	SLIP_ID = $SLIP_ID ";
//	echo "$cmd<br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data = $db_dpis->get_array();
		$cmd = " SELECT POS_NO FROM PER_POSITION a, PER_PERSONAL b WHERE a.POS_ID=b.POS_ID and PER_ID=$PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POS_NO = trim($data2[POS_NO]);

		$SLIP_YEAR = trim($data[SLIP_YEAR])+0;
		$SLIP_MONTH = trim($data[SLIP_MONTH])+0;
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$BANK_NAME = trim($data[BANK_NAME]);
		$BRANCH_NAME = trim($data[BRANCH_NAME]);
		$PER_BANK_ACCOUNT = trim($data[PER_BANK_ACCOUNT]);
		$INCOME_01 = $data[INCOME_01];
		$INCOME_02 = $data[INCOME_02];
		$INCOME_03 = $data[INCOME_03];
		$INCOME_04 = $data[INCOME_04];
		$INCOME_05 = $data[INCOME_05];
		$INCOME_06 = $data[INCOME_06];
		$INCOME_07 = $data[INCOME_07];
		$INCOME_08 = $data[INCOME_08];
		$INCOME_09 = $data[INCOME_09];
		$INCOME_10 = $data[INCOME_10];
		$INCOME_11 = $data[INCOME_11];
		$INCOME_12 = $data[INCOME_12];
		$INCOME_13 = $data[INCOME_13];
		$INCOME_14 = $data[INCOME_14];
		$INCOME_15 = $data[INCOME_15];
		$INCOME_16 = $data[INCOME_16];
		$INCOME_17 = $data[INCOME_17];
		$INCOME_18 = $data[INCOME_18];
		$INCOME_19 = $data[INCOME_19];
		$INCOME_20 = $data[INCOME_20];
		$INCOME_NAME_01 = trim($data[INCOME_NAME_01]);
		if ($INCOME_NAME_01=="NULL") $INCOME_NAME_01 = "";
		$EXTRA_INCOME_01 = $data[EXTRA_INCOME_01];
		$INCOME_NAME_02 = trim($data[INCOME_NAME_02]);
		if ($INCOME_NAME_02=="NULL") $INCOME_NAME_02 = "";
		$EXTRA_INCOME_02 = $data[EXTRA_INCOME_02];
		$INCOME_NAME_03 = trim($data[INCOME_NAME_03]);
		if ($INCOME_NAME_03=="NULL") $INCOME_NAME_03 = "";
		$EXTRA_INCOME_03 = $data[EXTRA_INCOME_03];
		$INCOME_NAME_04 = trim($data[INCOME_NAME_04]);
		if ($INCOME_NAME_04=="NULL") $INCOME_NAME_04 = "";
		$EXTRA_INCOME_04 = $data[EXTRA_INCOME_04];
		$OTHER_INCOME = $data[OTHER_INCOME];
		$TOTAL_INCOME = $data[TOTAL_INCOME];
		$DEDUCT_01 = $data[DEDUCT_01];
		$DEDUCT_02 = $data[DEDUCT_02];
		$DEDUCT_03 = $data[DEDUCT_03];
		$DEDUCT_04 = $data[DEDUCT_04];
		$DEDUCT_05 = $data[DEDUCT_05];
		$DEDUCT_06 = $data[DEDUCT_06];
		$DEDUCT_07 = $data[DEDUCT_07];
		$DEDUCT_08 = $data[DEDUCT_08];
		$DEDUCT_09 = $data[DEDUCT_09];
		$DEDUCT_10 = $data[DEDUCT_10];
		$DEDUCT_11 = $data[DEDUCT_11];
		$DEDUCT_12 = $data[DEDUCT_12];
		$DEDUCT_13 = $data[DEDUCT_13];
		$DEDUCT_14 = $data[DEDUCT_14];
		$DEDUCT_15 = $data[DEDUCT_15];
		$DEDUCT_16 = $data[DEDUCT_16];
		$DEDUCT_NAME_01 = trim($data[DEDUCT_NAME_01]);
		if ($DEDUCT_NAME_01=="NULL") $DEDUCT_NAME_01 = "";
		$EXTRA_DEDUCT_01 = $data[EXTRA_DEDUCT_01];
		$DEDUCT_NAME_02 = trim($data[DEDUCT_NAME_02]);
		if ($DEDUCT_NAME_02=="NULL") $DEDUCT_NAME_02 = "";
		$EXTRA_DEDUCT_02 = $data[EXTRA_DEDUCT_02];
		$DEDUCT_NAME_03 = trim($data[DEDUCT_NAME_03]);
		if ($DEDUCT_NAME_03=="NULL") $DEDUCT_NAME_03 = "";
		$EXTRA_DEDUCT_03 = $data[EXTRA_DEDUCT_03];
		$DEDUCT_NAME_04 = trim($data[DEDUCT_NAME_04]);
		if ($DEDUCT_NAME_04=="NULL") $DEDUCT_NAME_04 = "";
		$EXTRA_DEDUCT_04 = $data[EXTRA_DEDUCT_04];
		$DEDUCT_NAME_05 = trim($data[DEDUCT_NAME_05]);
		if ($DEDUCT_NAME_05=="NULL") $DEDUCT_NAME_05 = "";
		$EXTRA_DEDUCT_05 = $data[EXTRA_DEDUCT_05];
		$DEDUCT_NAME_06 = trim($data[DEDUCT_NAME_06]);
		if ($DEDUCT_NAME_06=="NULL") $DEDUCT_NAME_06 = "";
		$EXTRA_DEDUCT_06 = $data[EXTRA_DEDUCT_06];
		$DEDUCT_NAME_07 = trim($data[DEDUCT_NAME_07]);
		if ($DEDUCT_NAME_07=="NULL") $DEDUCT_NAME_07 = "";
		$EXTRA_DEDUCT_07 = $data[EXTRA_DEDUCT_07];
		$DEDUCT_NAME_08 = trim($data[DEDUCT_NAME_08]);
		if ($DEDUCT_NAME_08=="NULL") $DEDUCT_NAME_08 = "";
		$EXTRA_DEDUCT_08 = $data[EXTRA_DEDUCT_08];
		$OTHER_DEDUCT = $data[OTHER_DEDUCT];
		$TOTAL_DEDUCT = $data[TOTAL_DEDUCT];
		$NET_INCOME = $data[NET_INCOME];
		$UPDATE_DATE = show_date_format($data[UPDATE_DATE], 1);

		$HEAD_01 = "ใบรับรองการจ่ายเงินเดือน และเงินอื่น ประจำเดือน ".trim("".$month_full[$SLIP_MONTH][TH]." ปี พ.ศ. ".$SLIP_YEAR."");
		$HEAD_02_1 = " ชื่อ-นามสกุล ".trim($PN_NAME . $PER_NAME . "  " . $PER_SURNAME);
		$HEAD_02_2 = "กรม ".trim($DEPARTMENT_NAME);
		$HEAD_02_3 = "สำนัก/กอง/ศูนย์ ".trim($ORG_NAME);
		$HEAD_03_1 = " รายการรายรับ "."เลขตำแหน่ง ".trim($POS_NO);
		$HEAD_03_2 = "โอนเงินเข้า ".trim($BANK_NAME);
//		$HEAD_03_2 = "โอนเงินเข้า ".trim($BANK_NAME . "  " . $BRANCH_NAME);
		$HEAD_03_3 = "เลขที่บัญชี ".trim($PER_BANK_ACCOUNT);

		$pdf->SetFont($font,'b',14);	// กำหนด font ตัวหนา ขนาด 14
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));	// 	กำหนดสีตัวอักษร R G B

		$pdf->Image("../images/logo_ocsc.jpg", 250, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26

		$pdf->Cell(280,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$HEAD_01"):"$HEAD_01"),0,1,"C");			//HEADER

		$line_h = 7;
		
		// $pdf->add_lineFreeTab พิมพ์บรรทัดอย่างอิสระ  
		$arr_text = array($HEAD_02_1,$HEAD_02_2,$HEAD_02_3,"");	// array ของข้อความ
		$arr_align = array("L","L","L","");	// array ของ alignment
		$arr_border = array("","","","");	// array ของเส้นขอบตาราง
		$arr_font = array("$font|b|14|","$font||14","$font||14","");	// array ของลักษณะตัวอักษร
		$arr_width = array("90","90","90","10");	// ขนาดความกว้างของแต่ละ cell ถ้าจะให้ประโยคต่อกัน กำหนดดังนี้ "200","-","-" ทั้งหมด กว้าง 200 cell 2 cell ที่เป็น - จะต่อติดกัน ไป
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x"); // functio กำหนดรูปแบบการพิมพ์ TNUM ตัวเลขไทย ENUM ตัวเลขอราบิค ถ้ามี -2 ทศนิยม 2 ตำแหน่ง เป็นต้น
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);
		
		// หัวข้อใหม่ แต่รูปแบบเหมือนบรรทัดบน
		$arr_text = array($HEAD_03_1,$HEAD_03_2,$HEAD_03_3,"");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);

		// กำหนดหัวและรูปแบบของตารางแรก
		$pdf->SetFont($font,'',12);
		$heading_text_s= array("","","","","","","","","","","","");	// 	กำหนดข้อความหัวตาราง ซึ่งในที่นี้ไม่มีค่า
		$head_text1 = implode(",",$heading_text_s);	// ไม่พิมพ์หัว
		$heading_width_s= array(29,15,29,15,29,15,29,15,29,15,30,30);	 // ความกว้างของหัวและทั้งตาราง
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("L","R","L","R","L","R","L","R","L","R","L","R");	// align ของหัว
		$head_align1 = implode(",",$heading_align_s);
		$heading_border_s = array("TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","",""); // รูปแบบการวาดกรอบ
		$head_border1 = implode(",",$heading_border_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");	// function กำหนดการพิมพ์ แต่ละ column
		$col_function = implode(",",$column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s,$heading_text_s, $heading_width_s, $heading_align_s, $heading_align_s);	// function นี้จัดการสร้างรูปแบบ array ที่ใช้กับตาราง
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, $head_border1, $head_align1, "", "12", "", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true); // เปิดตารางแบบไม่พิมพ์หัวแรก (argument ตัวสุดท้ายเป็น true ถ้าเป็น false จะพิมพ์หัวแรก)
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","R","L","R","L","R","L","R","L","R","L","R");	// align ของข้อมูล แต่ละ column

		$arr_data = (array) null;	// array ของข้อมูล
		$arr_data[0] = "เงินเดือน";
		$arr_data[1] = $INCOME_01;
		$arr_data[2] = "พ.ส.ร./ตกเบิก";
		$arr_data[3] = $INCOME_06;
		$arr_data[4] = "ต.ข.ท.ปจต.";
		$arr_data[5] = $INCOME_11;
		$arr_data[6] = "ง.ต.พ.ข./ตกเบิก";
		$arr_data[7] = $INCOME_16;
		$arr_data[8] = $INCOME_NAME_01;
		$arr_data[9] = $EXTRA_INCOME_01;
		$arr_data[10] = "";
		$arr_data[11] = "";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");	// พิมพ์ข้อมูลลงตาราง
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "เงินเดือนตกเบิก";
		$arr_data[1] = $INCOME_02;
		$arr_data[2] = "พ.ค.ว./ตกเบิก";
		$arr_data[3] = $INCOME_07;
		$arr_data[4] = "ต.ข.ท.ปจต./ตกเบิก";
		$arr_data[5] = $INCOME_12;
		$arr_data[6] = "ค่าเช่าบ้าน/ตกเบิก";
		$arr_data[7] = $INCOME_17;
		$arr_data[8] = $INCOME_NAME_02;
		$arr_data[9] = $EXTRA_INCOME_02;
		$arr_data[10] = "";
		$arr_data[11] = "";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "ป.จ.ต./วิทยฯ";
		$arr_data[1] = $INCOME_03;
		$arr_data[2] = "พ.ป.ผ./ตกเบิก";
		$arr_data[3] = $INCOME_08;
		$arr_data[4] = "ต.ข.8-8ว.";
		$arr_data[5] = $INCOME_13;
		$arr_data[6] = "ช่วยเหลือบุตร/ตกเบิก";
		$arr_data[7] = $INCOME_18;
		$arr_data[8] = $INCOME_NAME_03;
		$arr_data[9] = $EXTRA_INCOME_03;
		$arr_data[10] = "รวมรับทั้งเดือน ";
		$arr_data[11] = $TOTAL_INCOME;

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "ป.จ.ต./วิทยฯ ตกเบิก";
		$arr_data[1] = $INCOME_04;
		$arr_data[2] = "สปพ./ตกเบิก";
		$arr_data[3] = $INCOME_09;
		$arr_data[4] = "ต.ข.8-8ว./ตกเบิก";
		$arr_data[5] = $INCOME_14;
		$arr_data[6] = "การศึกษาบุตร/ตกเบิก";
		$arr_data[7] = $INCOME_19;
		$arr_data[8] = $INCOME_NAME_04;
		$arr_data[9] = $EXTRA_INCOME_04;
		$arr_data[10] = "รวมจ่ายทั้งเดือน ";
		$arr_data[11] = $TOTAL_DEDUCT;

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "พ.ข.อ./ตกเบิก";
		$arr_data[1] = $INCOME_05;
		$arr_data[2] = "ตปพ./ตกเบิก";
		$arr_data[3] = $INCOME_10;
		$arr_data[4] = "ต.ค.ข.1-7/ตกเบิก";
		$arr_data[5] = $INCOME_15;
		$arr_data[6] = "เงินรางวัล/เงินท้าทาย";
		$arr_data[7] = $INCOME_20;
		if ($OTHER_INCOME)
			$arr_data[8] = "อื่น ๆ";
		else
			$arr_data[8] = "";
		$arr_data[9] = $OTHER_INCOME;
		$arr_data[10] = "รับสุทธิ ";
		$arr_data[11] = $NET_INCOME;

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("","no"); 	// argument 1 = "", argument 2 = no ไม่พิมพ์เส้นปิดตาราง , ถ้า = close จะพิมพ์เส้นต่อท้ายให้เต็มหน้าและเส้นปิดตาราง
		
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"รายการรายจ่าย",0,1,"L");

		$pdf->SetFont($font,'',12);
		$heading_text_s= array("","","","","","","","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ไม่พิมพ์หัว
		$heading_width_s= array(29,15,29,15,29,15,29,15,29,15,60);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("L","R","L","R","L","R","L","R","L","R","C");
		$head_align1 = implode(",",$heading_align_s);
		$heading_border_s = array("TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","TLHBR","");
		$head_border1 = implode(",",$heading_border_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM0",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$col_function = implode(",",$column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s,$heading_text_s, $heading_width_s, $heading_align_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, $head_border1, $head_align1, "", "12", "", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","R","L","R","L","R","L","R","L","R","C");
		
		$arr_data = (array) null;
		$arr_data[0] = "ภาษี/ตกเบิก";
		$arr_data[1] = $DEDUCT_01;
		$arr_data[2] = "กบข./ตกเบิก";
		$arr_data[3] = $DEDUCT_05;
		$arr_data[4] = "ง.ก.ธ.";
		$arr_data[5] = $DEDUCT_09;
		$arr_data[6] = "ค่าสาธารณูปโภค";
		$arr_data[7] = $DEDUCT_13;
		$arr_data[8] = $DEDUCT_NAME_05;
		$arr_data[9] = $EXTRA_DEDUCT_05;
		$arr_data[10] = "ลงชื่อ..............................................ผู้มีหน้าที่จ่ายเงิน";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "เงินกู้เพื่อที่อยู่อาศัย";
		$arr_data[1] = $DEDUCT_02;
		$arr_data[2] = "สะสมเพิ่ม/ตกเบิก";
		$arr_data[3] = $DEDUCT_06;
		$arr_data[4] = "เงินกู้ ธพ.";
		$arr_data[5] = $DEDUCT_10;
		$arr_data[6] = "เงินสวัสดิการสโมสร";
		$arr_data[7] = $DEDUCT_14;
		$arr_data[8] = $DEDUCT_NAME_06;
		$arr_data[9] = $EXTRA_DEDUCT_06;
		$arr_data[10] = "( $UPDATE_DATE )";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "ค่าหุ้น/เงินกู้สหกรณ์";
		$arr_data[1] = $DEDUCT_03;
		$arr_data[2] = "ง.ก.บ.(ธอส.)";
		$arr_data[3] = $DEDUCT_07;
		$arr_data[4] = "ชดใช้ทางแพ่ง";
		$arr_data[5] = $DEDUCT_11;
		$arr_data[6] = "ค่าฌาปนกิจ";
		$arr_data[7] = $DEDUCT_15;
		$arr_data[8] = $DEDUCT_NAME_07;
		$arr_data[9] = $EXTRA_DEDUCT_07;
		$arr_data[10] = "วัน เดือน ปี ที่ออกหนังสือรับรอง";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = "เงินกู้เพื่อการศึกษา";
		$arr_data[1] = $DEDUCT_04;
		$arr_data[2] = "ง.ก.บ.(อส.)";
		$arr_data[3] = $DEDUCT_08;
		$arr_data[4] = "เงินเรียกคืน";
		$arr_data[5] = $DEDUCT_12;
		$arr_data[6] = "งท.สงเคราะห์";
		$arr_data[7] = $DEDUCT_16;
		$arr_data[8] = $DEDUCT_NAME_08;
		$arr_data[9] = $EXTRA_DEDUCT_08;
		$arr_data[10] = "";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data = (array) null;
		$arr_data[0] = $DEDUCT_NAME_01;
		$arr_data[1] = $EXTRA_DEDUCT_01;
		$arr_data[2] = $DEDUCT_NAME_02;
		$arr_data[3] = $EXTRA_DEDUCT_02;
		$arr_data[4] = $DEDUCT_NAME_03;
		$arr_data[5] = $EXTRA_DEDUCT_03;
		$arr_data[6] = $DEDUCT_NAME_04;
		$arr_data[7] = $EXTRA_DEDUCT_04;
		if ($OTHER_DEDUCT)
			$arr_data[8] = "อื่น ๆ";
		else
			$arr_data[8] = "";
		$arr_data[9] = $OTHER_DEDUCT;
		$arr_data[10] = "";

		$result = $pdf->add_data_tab($arr_data, 7, $heading_border_s, $data_align, "", "12", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("","no");  	// argument 1 = "", argument 2 = no ไม่พิมพ์เส้นปิดตาราง , ถ้า = close จะพิมพ์เส้นต่อท้ายให้เต็มหน้าและเส้นปิดตาราง

	}else{
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********",0,1,"C");
	} // end if

	$pdf->close();
	$pdf->Output();
	
	ini_set("max_execution_time", 60);

	// function do_COLUMN_FORMAT ใช้สร้างตัวแปรที่จำเป็นต่อการพิมพ์ตาราง
	function 	do_COLUMN_FORMAT($heading_text, $heading_width, $data_align) {
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>