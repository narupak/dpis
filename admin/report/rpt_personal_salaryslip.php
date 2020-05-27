<?php
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
        
        //decode url
        $_GET[SLIP_ID] = urldecode(base64_decode(trim($_GET[SLIP_ID])));
        if(trim($_GET[SLIP_ID])){
           $ARR_SLIP_ID = explode("_", trim($_GET[SLIP_ID]));
           $SLIP_ID = $ARR_SLIP_ID[0];
           $PER_ID = $ARR_SLIP_ID[1];
        }
	//if(trim($_GET[SLIP_ID]))$SLIP_ID=trim($_GET[SLIP_ID]); //เดิม
	
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_personal_salaryslip.pdf";
	if (!$font) $font = "AngsanaUPC";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = "SLIP";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(10,10,10);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
//session_cache_limiter("nocache");
//	session_cache_limiter("private");

	//#### ดึงลายเซ็น ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		/*เดิม...*/
                /*$cmd = " select * from 	PER_PERSONALPIC
			where 	PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";*/
                /*-------------------*/
                /*Release 5.1.0.4 Begin*/
               $cmd = " SELECT * FROM 	PER_PERSONALPIC 
			WHERE PER_ID=$PER_ID AND PER_CARDNO = '$PER_CARDNO' AND  PIC_SIGN=1 AND (PIC_SHOW=1)";
                /*Release 5.1.0.4 End*/
                
                
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
        function Card_Mask($number, $maskingCharacter = 'X') {
            return substr($number, 0, 3) . str_repeat($maskingCharacter, strlen($number) - 6) . substr($number, -3);
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
									NET_INCOME
					from		PER_SLIP
					where	SLIP_ID = $SLIP_ID AND PER_ID = $PER_ID ";
	//echo "<>$cmd<br>";
       // die('<pre>'.$cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data = $db_dpis->get_array();
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
                if($PER_BANK_ACCOUNT){
                    $PER_BANK_ACCOUNT = Card_Mask($PER_BANK_ACCOUNT);
                }else{
                    $PER_BANK_ACCOUNT = "";
                }
		$INCOME_01 = $data[INCOME_01]; //เงินเดือน/ค่าจ้างประจำ
		$INCOME_02 = $data[INCOME_02]; //เงินเดือนตกเบิก / ค่าจ้างประจำตกเบิก
		$INCOME_03 = $data[INCOME_03]; //เงิน ปตจ.
		$INCOME_04 = $data[INCOME_04]; //เงิน ปจต. ตกเบิก
		$INCOME_05 = $data[INCOME_05]; //พ.ข.อ./ตกเบิก
		$INCOME_06 = $data[INCOME_06]; //พ.ส.ร./ตกเบิก
		$INCOME_07 = $data[INCOME_07]; //พ.ค.ว./ตกเบิก
		$INCOME_08 = $data[INCOME_08]; //พ.ป.ผ./ตกเบิก
		$INCOME_09 = $data[INCOME_09]; //สปพ./ตกเบิก
		$INCOME_10 = $data[INCOME_10]; //ตปพ./ตกเบิก
		$INCOME_11 = $data[INCOME_11]; //ต.ข.ท.ปจต.
		$INCOME_12 = $data[INCOME_12]; //ต.ข.ท.ปจต. ตกเบิก
		$INCOME_13 = $data[INCOME_13]; //ต.ข.8-8ว.
		$INCOME_14 = $data[INCOME_14]; //ต.ข.8-8ว. ตกเบิก
		$INCOME_15 = $data[INCOME_15]; //ต.ด.ข.1-7/ตกเบิก,ต.ด.จ./ตกเบิก
		$INCOME_16 = $data[INCOME_16]; //ง.ต.พ.ข./ตกเบิก,ง.ต.พ.จ./ตกเบิก
		$INCOME_17 = $data[INCOME_17]; //ค่าเช่าบ้าน/ตกเบิก
		$INCOME_18 = $data[INCOME_18]; //ช่วยเหลือบุตร/ตกเบิก
		$INCOME_19 = $data[INCOME_19]; //การศึกษาบุตร/ตกเบิก
		$INCOME_20 = $data[INCOME_20]; //เงินรางวัล/เงินท้าทาย
                
		$INCOME_NAME_01 = trim($data[INCOME_NAME_01]); //ชื่อเงินเพิ่มรายการที่ 1
		$EXTRA_INCOME_01 = $data[EXTRA_INCOME_01]; //จำนวนเงินเพิ่มรายการที่ 1
                
		$INCOME_NAME_02 = trim($data[INCOME_NAME_02]); //ชื่อเงินเพิ่มรายการที่ 2
		$EXTRA_INCOME_02 = $data[EXTRA_INCOME_02]; //จำนวนเงินเพิ่มรายการที่ 2
                
		$INCOME_NAME_03 = trim($data[INCOME_NAME_03]); //ชื่อเงินเพิ่มรายการที่ 3
		$EXTRA_INCOME_03 = $data[EXTRA_INCOME_03]; //จำนวนเงินเพิ่มรายการที่ 3
                
		$INCOME_NAME_04 = trim($data[INCOME_NAME_04]); //ชื่อเงินเพิ่มรายการที่ 4
		$EXTRA_INCOME_04 = $data[EXTRA_INCOME_04]; //จำนวนเงินเพิ่มรายการที่ 4
                
		$OTHER_INCOME = $data[OTHER_INCOME]; //เงินเพิ่มอื่น ๆ
		$TOTAL_INCOME = $data[TOTAL_INCOME]; //รวมรับทั้งเดือน
                
		$DEDUCT_01 = $data[DEDUCT_01]; //ภาษี/ตกเบิก
		$DEDUCT_02 = $data[DEDUCT_02]; //เงินกู้เพื่อที่อยู่อาศัย
		$DEDUCT_03 = $data[DEDUCT_03]; //ค่าหุ้น ? เงินกู้สหกรณ์
		$DEDUCT_04 = $data[DEDUCT_04]; //เงินกู้เพื่อการศึกษา
		$DEDUCT_05 = $data[DEDUCT_05]; //กบข./ตกเบิก,กสจ./ตกเบิก
		$DEDUCT_06 = $data[DEDUCT_06]; //กบข.ส่วนเพิ่ม/ตกเบิก
		$DEDUCT_07 = $data[DEDUCT_07]; //ง.ก.บ.(ธอส.)
		$DEDUCT_08 = $data[DEDUCT_08]; //ง.ก.บ.(อส.)
		$DEDUCT_09 = $data[DEDUCT_09]; //ง.ก.ธ.
		$DEDUCT_10 = $data[DEDUCT_10]; //เงินกู้ ธพ.
		$DEDUCT_11 = $data[DEDUCT_11]; //ชดใช้ทางแพ่ง
		$DEDUCT_12 = $data[DEDUCT_12]; //เงินเรียกคืน
		$DEDUCT_13 = $data[DEDUCT_13]; //ค่าสาธารณูปโภค
		$DEDUCT_14 = $data[DEDUCT_14]; //เงินสวัสดิการสโมสร
		$DEDUCT_15 = $data[DEDUCT_15]; //ค่าฌาปนกิจ
		$DEDUCT_16 = $data[DEDUCT_16]; //งท. สงเคราะห์
                
		$DEDUCT_NAME_01 = trim($data[DEDUCT_NAME_01]); //ชื่อเงินหักรายการที่ 1
		$EXTRA_DEDUCT_01 = $data[EXTRA_DEDUCT_01]; //จำนวนเงินหักรายการที่ 1
                
		$DEDUCT_NAME_02 = trim($data[DEDUCT_NAME_02]); //ชื่อเงินหักรายการที่ 2
		$EXTRA_DEDUCT_02 = $data[EXTRA_DEDUCT_02]; //จำนวนเงินหักรายการที่ 2
                
		$DEDUCT_NAME_03 = trim($data[DEDUCT_NAME_03]); //ชื่อเงินหักรายการที่ 3
		$EXTRA_DEDUCT_03 = $data[EXTRA_DEDUCT_03]; //จำนวนเงินหักรายการที่ 3
                
		$DEDUCT_NAME_04 = trim($data[DEDUCT_NAME_04]); //ชื่อเงินหักรายการที่ 4
		$EXTRA_DEDUCT_04 = $data[EXTRA_DEDUCT_04];//จำนวนเงินหักรายการที่ 4
                
		$DEDUCT_NAME_05 = trim($data[DEDUCT_NAME_05]); //ชื่อเงินหักรายการที่ 5
		$EXTRA_DEDUCT_05 = $data[EXTRA_DEDUCT_05]; //จำนวนเงินหักรายการที่ 5
                
		$DEDUCT_NAME_06 = trim($data[DEDUCT_NAME_06]); //ชื่อเงินหักรายการที่ 6
		$EXTRA_DEDUCT_06 = $data[EXTRA_DEDUCT_06]; //จำนวนเงินหักรายการที่ 6
                
		$DEDUCT_NAME_07 = trim($data[DEDUCT_NAME_07]); //ชื่อเงินหักรายการที่ 7
		$EXTRA_DEDUCT_07 = $data[EXTRA_DEDUCT_07]; //จำนวนเงินหักรายการที่ 7
                
		$DEDUCT_NAME_08 = trim($data[DEDUCT_NAME_08]); //ชื่อเงินหักรายการที่ 8
		$EXTRA_DEDUCT_08 = $data[EXTRA_DEDUCT_08]; //จำนวนเงินหักรายการที่ 8
                
		$OTHER_DEDUCT = $data[OTHER_DEDUCT]; //เงินลด/หักอื่นๆ
		$TOTAL_DEDUCT = $data[TOTAL_DEDUCT]; //รวมจ่ายทั้งเดือน
		$NET_INCOME = $data[NET_INCOME]; //รับสุทธิ

		$HEAD_01 = "ใบรับรองการจ่ายเงินเดือน - ค่าจ้างประจำและเงินอื่น";
//		$HEAD_02 = "ประจำเดือน $month_full[$SLIP_MONTH][TH] ปี พ.ศ. $SLIP_YEAR ของ $PN_NAME $PER_NAME $PER_SURNAME";
		$HEAD_02_1 = "ประจำเดือน ";
		$HEAD_02_2 = trim("".$month_full[$SLIP_MONTH][TH]." ปี พ.ศ. ".$SLIP_YEAR."");
		$HEAD_02_3 = " ของ ";
		$HEAD_03 = "หน่วยงาน ";
		$HEAD_04_1 = "โอนเงินเข้า ";
		$HEAD_04_2 = " เลขที่บัญชี ";

		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		if ($SLIP_LOGO==1)
			$pdf->Image("../images/logo_ocsc.jpg", 15, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26
		elseif ($SLIP_LOGO==2)
			$pdf->Image("../images/logo_ocsc.jpg", 180, 15, 20, 26,"jpg");		// Original size = 50x65 => 20x26

		$pdf->Cell(200,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$HEAD_01"):"$HEAD_01"),0,1,"C");			//HEADER

		$line_h = 7;
		
		$arr_text = array($HEAD_02_1,$HEAD_02_2,$HEAD_02_3,trim($PN_NAME . $PER_NAME . "  " . $PER_SURNAME));
		$arr_align = array("C","C","C","C");
		$arr_border = array("","","","");
		$arr_font = array("$font|b|14|","$font||14","$font||14","$font||14");
		$arr_width = array("200","-","-","-");
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",);
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);
		
		$arr_text = array($HEAD_03,$DEPARTMENT_NAME . "  " . $ORG_NAME);
		$arr_align = array("C","C");
		$arr_border = array("","");
		$arr_font = array("$font|b|14|","$font||14");
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2");
		$arr_width = array("200","-");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);
		
		$arr_text = array($HEAD_04_1,$BANK_NAME . "  " . $BRANCH_NAME,$HEAD_04_2,$PER_BANK_ACCOUNT);
		$arr_align = array("C","C","C","C");
		$arr_border = array("","","","");
		$arr_font = array("$font|b|14|","$font||14","$font|b|14|","$font||14");
//		echo "text:".implode(",", $arr_text).", font:".implode(",", $arr_font)."<br>";
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$arr_width = array("200","-","-","-");
		$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);

		$pdf->SetFont($font,'bU',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ซ้าย
		$pdf->Cell(195,7,"รายการรับ",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ไม่พิมพ์หัว
		$heading_width_s= array(5,125,50,15,5);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("C","C","C","C","C");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$col_function = implode(",",$column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s,$heading_text_s, $heading_width_s, $heading_align_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","L","R","L","L");
                
                //chk การแสดงผลรายงานเกินหน้า
		$arr_all_chk = (array) null;
                
                
		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "เงินเดือน/ค่าจ้างประจำ";
		$arr_data[2] = $INCOME_01;
		$arr_data[3] = " บาท";
		$arr_data[4] = "";
                
                $arr_all_chk[0]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินเดือน/ค่าจ้างประจำ (ตกเบิก)";
		$arr_data[2] = $INCOME_02;
		$arr_data[3] = " บาท";
		$arr_data[4] = "";
                $arr_all_chk[1]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		$arr_data[0] = "";
		$arr_data[1] = "เงินประจำตำแหน่ง/วิชาชีพ";
		$arr_data[2] = $INCOME_03;
		$arr_data[3] = " บาท";
		$arr_data[4] = "";
                $arr_all_chk[2]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินประจำตำแหน่ง/วิชาชีพ (ตกเบิก)";
		$arr_data[2] = $INCOME_04;
		$arr_data[3] = " บาท";
		$arr_data[4] = "";
                $arr_all_chk[3]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($INCOME_05 > 0) {
			$arr_data[0] = "พ.ข.อ./ตกเบิก";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_05;
			$arr_data[3] = " บาท";
			$arr_data[4] = "";
                        $arr_all_chk[4]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_06 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "พ.ส.ร./ตกเบิก";
			$arr_data[2] = $INCOME_06;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[5]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_07 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "พ.ค.ว./ตกเบิก";
			$arr_data[2] = $INCOME_07;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[6]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_08 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "พ.ป.ผ./ตกเบิก";
			$arr_data[2] = $INCOME_08;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[7]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_09 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "สปพ./ตกเบิก";
			$arr_data[2] = $INCOME_09;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[8]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_10 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "ตปพ./ตกเบิก";
			$arr_data[2] = $INCOME_10;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[9]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		$arr_data[0] = "";
		$arr_data[1] = "เงินค่าตอบแทนรายเดือนข้าราชการเท่ากับเงินประจำตำแหน่ง";
		$arr_data[2] = $INCOME_11;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[10]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินค่าตอบแทนรายเดือนข้าราชการเท่ากับเงินประจำตำแหน่ง (ตกเบิก)";
		$arr_data[2] = $INCOME_12;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[11]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินค่าตอบแทนรายเดือนข้าราชการระดับ 8 และ 8 ว.";
		$arr_data[2] = $INCOME_13;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[12]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินค่าตอบแทนรายเดือนข้าราชการระดับ 8 และ 8 ว. (ตกเบิก)";
		$arr_data[2] = $INCOME_14;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[13]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินค่าตอบแทนรายเดือนสำหรับข้าราชการระดับ 1-7/ลูกจ้าง/ตกเบิก";
		$arr_data[2] = $INCOME_15;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[14]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินตอบแทนพิเศษ(2%,4%)ข้าราชการ/ลูกจ้างประจำ/ตกเบิก";
		$arr_data[2] = $INCOME_16;
		$arr_data[3] = " บาท";				
		$arr_data[4] = "";
                $arr_all_chk[15]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
            
            /* http://dpis.ocsc.go.th/Service/node/2042 เช็คเอาค่าเช่าบ้าน/ตกเบิก ไปรวมกับค่าอื่นๆ  02/07/2018      
		if ($INCOME_17 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_17;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[16]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
            */  
            
		if ($INCOME_18 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_18;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[17]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_19 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_19;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[18]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_20 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_20;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[19]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_01 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_01";
			$arr_data[2] = $EXTRA_INCOME_01;
			$arr_data[3] = " บาท";				
			$arr_data[4] = "";
                        $arr_all_chk[20]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_02 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_02";
			$arr_data[2] = $EXTRA_INCOME_02;
			$arr_data[3] = " บาท";			
			$arr_data[4] = "";
                        $arr_all_chk[21]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_03 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_03";
			$arr_data[2] = $EXTRA_INCOME_03;
			$arr_data[3] = " บาท";			
			$arr_data[4] = "";
                        $arr_all_chk[22]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_04 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_04";
			$arr_data[2] = $EXTRA_INCOME_04;
			$arr_data[3] = " บาท";			
			$arr_data[4] = "";
                        $arr_all_chk[23]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}	
		 // http://dpis.ocsc.go.th/Service/node/2042 เช็คเอาค่าเช่าบ้าน/ตกเบิก ไปรวมกับค่าอื่นๆ  02/07/2018
                $arr_data[0] = "";
		$arr_data[1] = "อื่นๆ";
		$arr_data[2] = $OTHER_INCOME+$INCOME_17;
		$arr_data[3] = " บาท";			
		$arr_data[4] = "";
                $arr_all_chk[24]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "รวมรายการรับ";
		$arr_data[2] = $TOTAL_INCOME;
		$arr_data[3] = " บาท";			
		$arr_data[4] = "";
                $arr_all_chk[25]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab(""); 

		$pdf->SetFont($font,'bU',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ซ้าย
		$pdf->Cell(195,7,"รายการหัก",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ไม่พิมพ์หัว
		$heading_width_s= array(5,125,50,15,5);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("C","C","C","C","C");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM0-2":"ENUM0-2",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$col_function = implode(",",$column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s,$heading_text_s, $heading_width_s, $heading_align_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","L","R","L","L");
		
		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "ภาษี/ตกเบิก";
		$arr_data[2] = $DEDUCT_01;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[26]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($DEDUCT_02 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "เงินกู้เพื่อที่อยู่อาศัย";
			$arr_data[2] = $DEDUCT_02;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		
         
		$arr_data[0] = "";
		$arr_data[1] = "ค่าหุ้น/เงินกู้สหกรณ์";
		$arr_data[2] = $DEDUCT_03;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[27]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_04 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "เงินกู้เพื่อการศึกษา";
			$arr_data[2] = $DEDUCT_04;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";

			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
		$arr_data[1] = "กบข./ตกเบิก,กสจ./ตกเบิก";
		$arr_data[2] = $DEDUCT_05;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[28]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "กบข. (สะสมเพิ่ม)";
		$arr_data[2] = $DEDUCT_06;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[29]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "เงินกู้ (ธอส.)";
		$arr_data[2] = $DEDUCT_07;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[30]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_08 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "ง.ก.บ.(อส.)";
			$arr_data[2] = $DEDUCT_08;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[31]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_09 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "ง.ก.ธ.";
			$arr_data[2] = $DEDUCT_09;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_10 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "เงินกู้ ธพ.";
			$arr_data[2] = $DEDUCT_10;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
                $arr_data[1] = "ชดใช้ทางแพ่ง";
		$arr_data[2] = $DEDUCT_11;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[32]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

		$arr_data[0] = "";
		$arr_data[1] = "เงินเรียกคืน";
		$arr_data[2] = $DEDUCT_12;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[33]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_13 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "ค่าสาธารณูปโภค";
			$arr_data[2] = $DEDUCT_13;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
                $arr_data[1] = "เงินสวัสดิการ";
		$arr_data[2] = $DEDUCT_14;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[34]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
                $arr_data[0] = "";
                $arr_data[1] = "ค่าฌาปนกิจ";
		$arr_data[2] = $DEDUCT_15;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[35]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_16 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "งท. สงเคราะห์";
			$arr_data[2] = $DEDUCT_16;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_01 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_01";
			$arr_data[2] = $EXTRA_DEDUCT_01;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[36]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_02 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_02";
			$arr_data[2] = $EXTRA_DEDUCT_02;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[37]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_03 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_03";
			$arr_data[2] = $EXTRA_DEDUCT_03;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[38]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_04 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_04";
			$arr_data[2] = $EXTRA_DEDUCT_04;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[39]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_05 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_05";
			$arr_data[2] = $EXTRA_DEDUCT_05;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[39]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_06 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_06";
			$arr_data[2] = $EXTRA_DEDUCT_06;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[40]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_07 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_07";
			$arr_data[2] = $EXTRA_DEDUCT_07;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[41]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_08 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_08";
			$arr_data[2] = $EXTRA_DEDUCT_08;
			$arr_data[3] = " บาท";		
			$arr_data[4] = "";
                        $arr_all_chk[42]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		
                
               
                $arr_data[0] = "";
                $arr_data[1] = "อื่นๆ";
		$arr_data[2] = $OTHER_DEDUCT; 
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[43]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

                $arr_data[0] = "";
                $arr_data[1] = "รวมรายการหัก";
		$arr_data[2] = $TOTAL_DEDUCT;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[44]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
                $arr_data[0] = "";
                $arr_data[1] = "จำนวนเงินที่โอนเข้าธนาคาร";
		$arr_data[2] = $NET_INCOME;
		$arr_data[3] = " บาท";		
		$arr_data[4] = "";
                $arr_all_chk[45]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

                //echo count($arr_all_chk);
                
		$pdf->close_tab("");

		// หาผู้มีหน้าที่จ่ายเงิน
		//print("<pre>"); print_r($SESS_E_SIGN); print("</pre>");		// 1-> แบบประเมินผลการปฏิบัติราชการ   2->ใบลา   3->สลิปเงินเดือน   4->หนังสือรับรอง 		
                $DAY = date("t",strtotime(($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-01"));
		$PIC_SIGN="";
		$SIGN_TYPE = 1;	 // type สลิปเงินเดือน
		$SIGN_START_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-01";
		$SIGN_END_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-".$DAY;
		//หาว่าใครเป็นคน ผู้มีหน้าที่จ่ายเงิน  NVL
                $cmd      = " select PER_ID, SIGN_NAME, SIGN_POSITION   
                                                from PER_SIGN 
                                                WHERE SIGN_TYPE = '$SIGN_TYPE' 
                                                AND SIGN_PER_TYPE = 1
                                                AND ((( SIGN_STARTDATE <='$SIGN_START_DATE' or SIGN_STARTDATE BETWEEN '$SIGN_START_DATE' and '$SIGN_END_DATE') AND SIGN_ENDDATE is null) 
                                                   OR (SIGN_STARTDATE BETWEEN '$SIGN_START_DATE' and '$SIGN_END_DATE' and (SIGN_ENDDATE >= '$SIGN_START_DATE' or '$SIGN_END_DATE' <=SIGN_ENDDATE))
                                                   OR ('$SIGN_START_DATE' BETWEEN SIGN_STARTDATE and SIGN_ENDDATE and ('$SIGN_END_DATE' BETWEEN SIGN_STARTDATE and SIGN_ENDDATE)))
                                                order by SIGN_STARTDATE desc, SIGN_ENDDATE desc";
                
                  

		/* $cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION from PER_SIGN 
						where SIGN_TYPE = '$SIGN_TYPE' and SIGN_PER_TYPE = 1 and (SIGN_STARTDATE<='$SIGN_START_DATE' and SIGN_STARTDATE<='$SIGN_END_DATE') 
						 and ((SIGN_ENDDATE>='$SIGN_START_DATE' and SIGN_ENDDATE>='$SIGN_END_DATE') or (SIGN_ENDDATE IS NULL))
						order by SIGN_STARTDATE desc, SIGN_ENDDATE desc ";	*/
		$count_exist=$db_dpis->send_cmd($cmd);
		//echo "$count_exist -> <pre>$cmd";
		if($count_exist>0){
			$data = $db_dpis->get_array();
			$SIGN_PER_ID = $data[PER_ID];
			$SIGN_NAME  = trim($data[SIGN_NAME]);
			$SIGN_POSITION  = trim($data[SIGN_POSITION]);
			if($SIGN_PER_ID && $SESS_E_SIGN[3]==1){		// ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
				// หา PER_CARDNO
				$cmd = " select 	PER_CARDNO from PER_PERSONAL where PER_ID=$SIGN_PER_ID ";
				$db_dpis2->send_cmd($cmd);	
				$data2 = $db_dpis2->get_array();
				$SIGN_PER_CARDNO = trim($data2[PER_CARDNO]);
				//echo "$cmd ->$SIGN_FULL_NAME ".$SESS_E_SIGN[2];
				$PIC_SIGN_PER = getPIC_SIGN($SIGN_PER_ID,$SIGN_PER_CARDNO);
			}
		} 
		
		
                if(count($arr_all_chk)=='27' || count($arr_all_chk)=='28'){
                    $pdf->Cell(200, 1, "", 0, 1, 'C', 0);		// space ซ้าย
                    if($PIC_SIGN_PER){  // มีรูปลายเซ็น
    /***			
                            $pdf->Cell(150, 10, "ลงชื่อ", 0, 0, 'L', 0);
                            $pdf->Cell(15, 10, "", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x+80), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
                            $save_x = $pdf->x;		$save_y = $pdf->y;
                            $pdf->x += 5;			$pdf->y -= 15;

                            $pdf->Cell(130,10,"ผู้มีหน้าที่จ่ายเงิน", 0, 1, 'L', 0);

                            $pdf->Cell(200,10, "", 0, 1, 'C', 0);		// space ซ้าย
                            $pdf->Cell(200,30,"($SIGN_FULL_NAME)",0,1,"R");
    ****/
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(70, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(30, 15, "ลงชื่อ", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x-14), ($pdf->y), 40, 10,"jpg");	// Original size = wxh (60x15) 200/5
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(30, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(68, 15, convert2thaidigit("ผู้มีหน้าที่จ่ายเงิน"), 0, 1, 'L', 0);
                    }else{
                            $pdf->Cell(220,7,"ลงชื่อ.....................................................................................ผู้มีหน้าที่จ่ายเงิน",0,1,"C");
                    }		
                }  else {
                    $pdf->Cell(200, 10, "", 0, 1, 'C', 0);		// space ซ้าย
                    if($PIC_SIGN_PER){  // มีรูปลายเซ็น
    /***			
                            $pdf->Cell(150, 10, "ลงชื่อ", 0, 0, 'L', 0);
                            $pdf->Cell(15, 10, "", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x+80), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
                            $save_x = $pdf->x;		$save_y = $pdf->y;
                            $pdf->x += 5;			$pdf->y -= 15;

                            $pdf->Cell(130,10,"ผู้มีหน้าที่จ่ายเงิน", 0, 1, 'L', 0);

                            $pdf->Cell(200,10, "", 0, 1, 'C', 0);		// space ซ้าย
                            $pdf->Cell(200,30,"($SIGN_FULL_NAME)",0,1,"R");
    ****/
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(70, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(30, 15, "ลงชื่อ", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x-14), ($pdf->y), 40, 14,"jpg");	// Original size = wxh (60x15) 200/5
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(30, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(68, 15, convert2thaidigit("ผู้มีหน้าที่จ่ายเงิน"), 0, 1, 'L', 0);
                    }else{
                            $pdf->Cell(220,7,"ลงชื่อ.....................................................................................ผู้มีหน้าที่จ่ายเงิน",0,1,"C");
                    }
                }	
		$pdf->SetFont($font,'',14);
                
		$pdf->Cell(80, 7, "", 0, 0, 'L', 0);		//space
                
                if(count($arr_all_chk)=='27' || count($arr_all_chk)=='28'){
                    if ($SIGN_NAME){
                            $pdf->Cell(50, 10 ,"($SIGN_NAME)",0,0,"C");
                    }else{
                            $pdf->Cell(50, 10 ,"(.....................................................................................)",0,0,"C");
                    }        
                }else {
                    if ($SIGN_NAME){
                            $pdf->Cell(50, 15 ,"($SIGN_NAME)",0,0,"C");
                    }else{
                            $pdf->Cell(50, 15 ,"(.....................................................................................)",0,0,"C");        
                    }        
                }   
		$pdf->Cell(40, 7, "", 0, 1, 'L', 0);		//space

		$pdf->AddPage();
		$pdf->SetFont($font,'bU',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ซ้าย
		$pdf->Cell(195,7,"หมายเหตุ",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ไม่พิมพ์หัว
		$heading_width_s= array(5,17,35,138,5);
		$head_width1 = implode(",",$heading_width_s);
		$heading_align_s = array("L","L","L","L","L");
		$head_align1 = implode(",",$heading_align_s);
		$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x",($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$col_function = implode(",",$column_function_s);
		$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text_s,$heading_text_s, $heading_width_s, $heading_align_s, $heading_align_s);
//		echo "Personal Information--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for Personal Information<br>";

		$data_align = array("L","L","L","L","L");
		
		$arr_data = (array) null;		
		$arr_data[0] = "";
		$arr_data[1] = "เงินรับ";
		$arr_data[2] = "ต.ร.ปจต.";
		$arr_data[3] = "เงินค่าตอบแทนเหมาจ่ายแทนการจัดหารถประจำตำแหน่ง";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ค.ช.ข.";
		$arr_data[3] = "เงินเพิ่มการครองชีพชั่วคราวข้าราชการ";			
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
		$arr_data[0] = "";
		$arr_data[1] = "";
		$arr_data[2] = "ค.ช.จ.";
		$arr_data[3] = "เงินเพิ่มการครองชีพชั่วคราวลูกจ้างประจำ";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

		$arr_data[0] = "";
		$arr_data[1] = "เงินหัก";
		$arr_data[2] = "อายัดเงิน";
		$arr_data[3] = "อายัดเงิน";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "สวัสดิ.ธอส.";
		$arr_data[3] = "เงินกู้สวัสดิการธนาคารอาคารสงเคราะห์";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ง.น.ส.";
		$arr_data[3] = "เงินเรียกคืนและเงินนำส่ง (เงินเดือน/ค่าจ้างประจำ เงินที่เบิกจากงบบุคลากร)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ต.ข.ว319";
		$arr_data[3] = "เรียกคืนเงินตอบแทนพิเศษข้าราชการผู้ได้รับเงินเดือนขั้นสูง ( เต็มขั้น)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ต.จ.ว319";
		$arr_data[3] = "เรียกคืนเงินตอบแทนพิเศษลูกจ้างประจำผู้ได้รับค่าจ้างถึงขั้นสูง (เต็มขั้น)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ง.บ.ส.ก0100";
		$arr_data[3] = "เรียกคืนและนำส่งเงินประจำตำแหน่งผู้บริหารระดับสูง-กลาง";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ค.ช.ข.";
		$arr_data[3] = "เรียกคืนเงินเพิ่มการครองชีพชั่วคราวข้าราชการ";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ค.ช.จ.";
		$arr_data[3] = "เรียกคืนเงินเพิ่มการครองชีพชั่วคราวลูกจ้างประจำ";
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ต.ข.ท.ปจต.";
		$arr_data[3] = "เรียกคืนและนำส่งเงินตอบแทนรายเดือนข้าราชการเท่าเงินประจำตำแหน่ง";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ต.ด.ข.1-7";
		$arr_data[3] = "เรียกคืนเงินและนำส่งเงินค่าตอบแทนรายเดือนข้าราชการระดับ 1-7";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.ต.ข.8-8 ว.";
		$arr_data[3] = "เรียกคืนเงินและนำส่งเงินตอบแทนรายเดือนข้าราชการ ระดับ 8 และ 8 ว.";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ง.ฝ.สหกรณ์";
		$arr_data[3] = "เงินฝากสหกรณ์";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ง.ก. Comp ICT";
		$arr_data[3] = "เงินกู้ Computer ICT";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "การศึกษา";
		$arr_data[3] = "เงินกู้เพื่อการศึกษา";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ง.ก.บ. (กรุงไทย)";
		$arr_data[3] = "เงินกู้ของธนาคารกรุงไทย";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "รค.พ.ต.ก.";
		$arr_data[3] = "เรียกคืนและนำส่งเงินเพิ่มเหตุพิเศษของขรก.พลเรือน (ผู้ปฏิบัติงานด้านนิติกร)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("");

	}else{
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********",0,1,"C");
	} // end if

	$pdf->close();
        $pdf->Output();
        //$filename = 'personal_salary.pdf';/* แก้ไข ไม่ให้ดาวน์โหลดไฟล์เลย ให้ Popup */
	//$pdf->Output($filename,'D');/* แก้ไข ไม่ให้ดาวน์โหลดไฟล์เลย ให้ Popup */
	
	ini_set("max_execution_time", 60);

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