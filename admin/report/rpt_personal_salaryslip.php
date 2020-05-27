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
	//if(trim($_GET[SLIP_ID]))$SLIP_ID=trim($_GET[SLIP_ID]); //���
	
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

	//#### �֧����� ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//���ٻ����������
		/*���...*/
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
		$INCOME_01 = $data[INCOME_01]; //�Թ��͹/��Ҩ�ҧ��Ш�
		$INCOME_02 = $data[INCOME_02]; //�Թ��͹���ԡ / ��Ҩ�ҧ��Шӵ��ԡ
		$INCOME_03 = $data[INCOME_03]; //�Թ ���.
		$INCOME_04 = $data[INCOME_04]; //�Թ ���. ���ԡ
		$INCOME_05 = $data[INCOME_05]; //�.�.�./���ԡ
		$INCOME_06 = $data[INCOME_06]; //�.�.�./���ԡ
		$INCOME_07 = $data[INCOME_07]; //�.�.�./���ԡ
		$INCOME_08 = $data[INCOME_08]; //�.�.�./���ԡ
		$INCOME_09 = $data[INCOME_09]; //ʻ�./���ԡ
		$INCOME_10 = $data[INCOME_10]; //���./���ԡ
		$INCOME_11 = $data[INCOME_11]; //�.�.�.���.
		$INCOME_12 = $data[INCOME_12]; //�.�.�.���. ���ԡ
		$INCOME_13 = $data[INCOME_13]; //�.�.8-8�.
		$INCOME_14 = $data[INCOME_14]; //�.�.8-8�. ���ԡ
		$INCOME_15 = $data[INCOME_15]; //�.�.�.1-7/���ԡ,�.�.�./���ԡ
		$INCOME_16 = $data[INCOME_16]; //�.�.�.�./���ԡ,�.�.�.�./���ԡ
		$INCOME_17 = $data[INCOME_17]; //�����Һ�ҹ/���ԡ
		$INCOME_18 = $data[INCOME_18]; //��������ͺص�/���ԡ
		$INCOME_19 = $data[INCOME_19]; //����֡�Һص�/���ԡ
		$INCOME_20 = $data[INCOME_20]; //�Թ�ҧ���/�Թ��ҷ��
                
		$INCOME_NAME_01 = trim($data[INCOME_NAME_01]); //�����Թ������¡�÷�� 1
		$EXTRA_INCOME_01 = $data[EXTRA_INCOME_01]; //�ӹǹ�Թ������¡�÷�� 1
                
		$INCOME_NAME_02 = trim($data[INCOME_NAME_02]); //�����Թ������¡�÷�� 2
		$EXTRA_INCOME_02 = $data[EXTRA_INCOME_02]; //�ӹǹ�Թ������¡�÷�� 2
                
		$INCOME_NAME_03 = trim($data[INCOME_NAME_03]); //�����Թ������¡�÷�� 3
		$EXTRA_INCOME_03 = $data[EXTRA_INCOME_03]; //�ӹǹ�Թ������¡�÷�� 3
                
		$INCOME_NAME_04 = trim($data[INCOME_NAME_04]); //�����Թ������¡�÷�� 4
		$EXTRA_INCOME_04 = $data[EXTRA_INCOME_04]; //�ӹǹ�Թ������¡�÷�� 4
                
		$OTHER_INCOME = $data[OTHER_INCOME]; //�Թ������� �
		$TOTAL_INCOME = $data[TOTAL_INCOME]; //����Ѻ�����͹
                
		$DEDUCT_01 = $data[DEDUCT_01]; //����/���ԡ
		$DEDUCT_02 = $data[DEDUCT_02]; //�Թ������ͷ�����������
		$DEDUCT_03 = $data[DEDUCT_03]; //������ ? �Թ����ˡó�
		$DEDUCT_04 = $data[DEDUCT_04]; //�Թ������͡���֡��
		$DEDUCT_05 = $data[DEDUCT_05]; //���./���ԡ,�ʨ./���ԡ
		$DEDUCT_06 = $data[DEDUCT_06]; //���.��ǹ����/���ԡ
		$DEDUCT_07 = $data[DEDUCT_07]; //�.�.�.(���.)
		$DEDUCT_08 = $data[DEDUCT_08]; //�.�.�.(��.)
		$DEDUCT_09 = $data[DEDUCT_09]; //�.�.�.
		$DEDUCT_10 = $data[DEDUCT_10]; //�Թ��� ��.
		$DEDUCT_11 = $data[DEDUCT_11]; //����ҧ��
		$DEDUCT_12 = $data[DEDUCT_12]; //�Թ���¡�׹
		$DEDUCT_13 = $data[DEDUCT_13]; //����Ҹ�óٻ���
		$DEDUCT_14 = $data[DEDUCT_14]; //�Թ���ʴԡ�������
		$DEDUCT_15 = $data[DEDUCT_15]; //��Ҭһ��Ԩ
		$DEDUCT_16 = $data[DEDUCT_16]; //��. ʧ������
                
		$DEDUCT_NAME_01 = trim($data[DEDUCT_NAME_01]); //�����Թ�ѡ��¡�÷�� 1
		$EXTRA_DEDUCT_01 = $data[EXTRA_DEDUCT_01]; //�ӹǹ�Թ�ѡ��¡�÷�� 1
                
		$DEDUCT_NAME_02 = trim($data[DEDUCT_NAME_02]); //�����Թ�ѡ��¡�÷�� 2
		$EXTRA_DEDUCT_02 = $data[EXTRA_DEDUCT_02]; //�ӹǹ�Թ�ѡ��¡�÷�� 2
                
		$DEDUCT_NAME_03 = trim($data[DEDUCT_NAME_03]); //�����Թ�ѡ��¡�÷�� 3
		$EXTRA_DEDUCT_03 = $data[EXTRA_DEDUCT_03]; //�ӹǹ�Թ�ѡ��¡�÷�� 3
                
		$DEDUCT_NAME_04 = trim($data[DEDUCT_NAME_04]); //�����Թ�ѡ��¡�÷�� 4
		$EXTRA_DEDUCT_04 = $data[EXTRA_DEDUCT_04];//�ӹǹ�Թ�ѡ��¡�÷�� 4
                
		$DEDUCT_NAME_05 = trim($data[DEDUCT_NAME_05]); //�����Թ�ѡ��¡�÷�� 5
		$EXTRA_DEDUCT_05 = $data[EXTRA_DEDUCT_05]; //�ӹǹ�Թ�ѡ��¡�÷�� 5
                
		$DEDUCT_NAME_06 = trim($data[DEDUCT_NAME_06]); //�����Թ�ѡ��¡�÷�� 6
		$EXTRA_DEDUCT_06 = $data[EXTRA_DEDUCT_06]; //�ӹǹ�Թ�ѡ��¡�÷�� 6
                
		$DEDUCT_NAME_07 = trim($data[DEDUCT_NAME_07]); //�����Թ�ѡ��¡�÷�� 7
		$EXTRA_DEDUCT_07 = $data[EXTRA_DEDUCT_07]; //�ӹǹ�Թ�ѡ��¡�÷�� 7
                
		$DEDUCT_NAME_08 = trim($data[DEDUCT_NAME_08]); //�����Թ�ѡ��¡�÷�� 8
		$EXTRA_DEDUCT_08 = $data[EXTRA_DEDUCT_08]; //�ӹǹ�Թ�ѡ��¡�÷�� 8
                
		$OTHER_DEDUCT = $data[OTHER_DEDUCT]; //�ԹŴ/�ѡ����
		$TOTAL_DEDUCT = $data[TOTAL_DEDUCT]; //������·����͹
		$NET_INCOME = $data[NET_INCOME]; //�Ѻ�ط��

		$HEAD_01 = "��Ѻ�ͧ��è����Թ��͹ - ��Ҩ�ҧ��Ш�����Թ���";
//		$HEAD_02 = "��Ш���͹ $month_full[$SLIP_MONTH][TH] �� �.�. $SLIP_YEAR �ͧ $PN_NAME $PER_NAME $PER_SURNAME";
		$HEAD_02_1 = "��Ш���͹ ";
		$HEAD_02_2 = trim("".$month_full[$SLIP_MONTH][TH]." �� �.�. ".$SLIP_YEAR."");
		$HEAD_02_3 = " �ͧ ";
		$HEAD_03 = "˹��§ҹ ";
		$HEAD_04_1 = "�͹�Թ��� ";
		$HEAD_04_2 = " �Ţ���ѭ�� ";

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
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ����
		$pdf->Cell(195,7,"��¡���Ѻ",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
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
                
                //chk ����ʴ�����§ҹ�Թ˹��
		$arr_all_chk = (array) null;
                
                
		$arr_data = (array) null;
		$arr_data[0] = "";
		$arr_data[1] = "�Թ��͹/��Ҩ�ҧ��Ш�";
		$arr_data[2] = $INCOME_01;
		$arr_data[3] = " �ҷ";
		$arr_data[4] = "";
                
                $arr_all_chk[0]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��͹/��Ҩ�ҧ��Ш� (���ԡ)";
		$arr_data[2] = $INCOME_02;
		$arr_data[3] = " �ҷ";
		$arr_data[4] = "";
                $arr_all_chk[1]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		$arr_data[0] = "";
		$arr_data[1] = "�Թ��Шӵ��˹�/�ԪҪվ";
		$arr_data[2] = $INCOME_03;
		$arr_data[3] = " �ҷ";
		$arr_data[4] = "";
                $arr_all_chk[2]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��Шӵ��˹�/�ԪҪվ (���ԡ)";
		$arr_data[2] = $INCOME_04;
		$arr_data[3] = " �ҷ";
		$arr_data[4] = "";
                $arr_all_chk[3]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($INCOME_05 > 0) {
			$arr_data[0] = "�.�.�./���ԡ";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_05;
			$arr_data[3] = " �ҷ";
			$arr_data[4] = "";
                        $arr_all_chk[4]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_06 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "�.�.�./���ԡ";
			$arr_data[2] = $INCOME_06;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[5]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_07 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "�.�.�./���ԡ";
			$arr_data[2] = $INCOME_07;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[6]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_08 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "�.�.�./���ԡ";
			$arr_data[2] = $INCOME_08;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[7]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_09 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "ʻ�./���ԡ";
			$arr_data[2] = $INCOME_09;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[8]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_10 > 0) {
			$arr_data[0] = "";
			$arr_data[1] = "���./���ԡ";
			$arr_data[2] = $INCOME_10;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[9]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹�";
		$arr_data[2] = $INCOME_11;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[10]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹� (���ԡ)";
		$arr_data[2] = $INCOME_12;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[11]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �.";
		$arr_data[2] = $INCOME_13;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[12]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �. (���ԡ)";
		$arr_data[2] = $INCOME_14;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[13]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7/�١��ҧ/���ԡ";
		$arr_data[2] = $INCOME_15;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[14]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ�ͺ᷹�����(2%,4%)����Ҫ���/�١��ҧ��Ш�/���ԡ";
		$arr_data[2] = $INCOME_16;
		$arr_data[3] = " �ҷ";				
		$arr_data[4] = "";
                $arr_all_chk[15]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
            
            /* http://dpis.ocsc.go.th/Service/node/2042 ����Ҥ����Һ�ҹ/���ԡ �����Ѻ�������  02/07/2018      
		if ($INCOME_17 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_17;
			$arr_data[3] = " �ҷ";				
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
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[17]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_19 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_19;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[18]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($INCOME_20 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "";
			$arr_data[2] = $INCOME_20;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[19]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_01 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_01";
			$arr_data[2] = $EXTRA_INCOME_01;
			$arr_data[3] = " �ҷ";				
			$arr_data[4] = "";
                        $arr_all_chk[20]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_02 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_02";
			$arr_data[2] = $EXTRA_INCOME_02;
			$arr_data[3] = " �ҷ";			
			$arr_data[4] = "";
                        $arr_all_chk[21]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_03 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_03";
			$arr_data[2] = $EXTRA_INCOME_03;
			$arr_data[3] = " �ҷ";			
			$arr_data[4] = "";
                        $arr_all_chk[22]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}

		if ($EXTRA_INCOME_04 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "$INCOME_NAME_04";
			$arr_data[2] = $EXTRA_INCOME_04;
			$arr_data[3] = " �ҷ";			
			$arr_data[4] = "";
                        $arr_all_chk[23]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}	
		 // http://dpis.ocsc.go.th/Service/node/2042 ����Ҥ����Һ�ҹ/���ԡ �����Ѻ�������  02/07/2018
                $arr_data[0] = "";
		$arr_data[1] = "����";
		$arr_data[2] = $OTHER_INCOME+$INCOME_17;
		$arr_data[3] = " �ҷ";			
		$arr_data[4] = "";
                $arr_all_chk[24]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�����¡���Ѻ";
		$arr_data[2] = $TOTAL_INCOME;
		$arr_data[3] = " �ҷ";			
		$arr_data[4] = "";
                $arr_all_chk[25]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab(""); 

		$pdf->SetFont($font,'bU',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ����
		$pdf->Cell(195,7,"��¡���ѡ",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
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
		$arr_data[1] = "����/���ԡ";
		$arr_data[2] = $DEDUCT_01;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[26]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		if ($DEDUCT_02 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "�Թ������ͷ�����������";
			$arr_data[2] = $DEDUCT_02;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		
         
		$arr_data[0] = "";
		$arr_data[1] = "������/�Թ����ˡó�";
		$arr_data[2] = $DEDUCT_03;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[27]='1';
                
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_04 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "�Թ������͡���֡��";
			$arr_data[2] = $DEDUCT_04;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";

			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
		$arr_data[1] = "���./���ԡ,�ʨ./���ԡ";
		$arr_data[2] = $DEDUCT_05;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[28]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "���. (��������)";
		$arr_data[2] = $DEDUCT_06;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[29]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$arr_data[0] = "";
		$arr_data[1] = "�Թ��� (���.)";
		$arr_data[2] = $DEDUCT_07;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[30]='1';

		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_08 > 0) {		
                        $arr_data[0] = "";
			$arr_data[1] = "�.�.�.(��.)";
			$arr_data[2] = $DEDUCT_08;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[31]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_09 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "�.�.�.";
			$arr_data[2] = $DEDUCT_09;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($DEDUCT_10 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "�Թ��� ��.";
			$arr_data[2] = $DEDUCT_10;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
                $arr_data[1] = "����ҧ��";
		$arr_data[2] = $DEDUCT_11;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[32]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

		$arr_data[0] = "";
		$arr_data[1] = "�Թ���¡�׹";
		$arr_data[2] = $DEDUCT_12;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[33]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_13 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "����Ҹ�óٻ���";
			$arr_data[2] = $DEDUCT_13;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		

                $arr_data[0] = "";
                $arr_data[1] = "�Թ���ʴԡ��";
		$arr_data[2] = $DEDUCT_14;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[34]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
                $arr_data[0] = "";
                $arr_data[1] = "��Ҭһ��Ԩ";
		$arr_data[2] = $DEDUCT_15;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[35]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		
		if ($DEDUCT_16 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "��. ʧ������";
			$arr_data[2] = $DEDUCT_16;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_01 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_01";
			$arr_data[2] = $EXTRA_DEDUCT_01;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[36]='1';
                        
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_02 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_02";
			$arr_data[2] = $EXTRA_DEDUCT_02;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[37]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_03 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_03";
			$arr_data[2] = $EXTRA_DEDUCT_03;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[38]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_04 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_04";
			$arr_data[2] = $EXTRA_DEDUCT_04;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[39]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_05 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_05";
			$arr_data[2] = $EXTRA_DEDUCT_05;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[39]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_06 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_06";
			$arr_data[2] = $EXTRA_DEDUCT_06;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[40]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_07 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_07";
			$arr_data[2] = $EXTRA_DEDUCT_07;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[41]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}
		
		if ($EXTRA_DEDUCT_08 > 0) {		
                        $arr_data[0] = "";
                        $arr_data[1] = "$DEDUCT_NAME_08";
			$arr_data[2] = $EXTRA_DEDUCT_08;
			$arr_data[3] = " �ҷ";		
			$arr_data[4] = "";
                        $arr_all_chk[42]='1';
		
			$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";
		}		
                
               
                $arr_data[0] = "";
                $arr_data[1] = "����";
		$arr_data[2] = $OTHER_DEDUCT; 
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[43]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

                $arr_data[0] = "";
                $arr_data[1] = "�����¡���ѡ";
		$arr_data[2] = $TOTAL_DEDUCT;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[44]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
                $arr_data[0] = "";
                $arr_data[1] = "�ӹǹ�Թ����͹��Ҹ�Ҥ��";
		$arr_data[2] = $NET_INCOME;
		$arr_data[3] = " �ҷ";		
		$arr_data[4] = "";
                $arr_all_chk[45]='1';
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "bU", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

                //echo count($arr_all_chk);
                
		$pdf->close_tab("");

		// �Ҽ����˹�ҷ������Թ
		//print("<pre>"); print_r($SESS_E_SIGN); print("</pre>");		// 1-> Ẻ�����Թ�š�û�Ժѵ��Ҫ���   2->���   3->��Ի�Թ��͹   4->˹ѧ����Ѻ�ͧ 		
                $DAY = date("t",strtotime(($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-01"));
		$PIC_SIGN="";
		$SIGN_TYPE = 1;	 // type ��Ի�Թ��͹
		$SIGN_START_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-01";
		$SIGN_END_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-".$DAY;
		//��������繤� �����˹�ҷ������Թ  NVL
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
			if($SIGN_PER_ID && $SESS_E_SIGN[3]==1){		// ���ٻẺ�ͧ���������礷�͹ԡ��
				// �� PER_CARDNO
				$cmd = " select 	PER_CARDNO from PER_PERSONAL where PER_ID=$SIGN_PER_ID ";
				$db_dpis2->send_cmd($cmd);	
				$data2 = $db_dpis2->get_array();
				$SIGN_PER_CARDNO = trim($data2[PER_CARDNO]);
				//echo "$cmd ->$SIGN_FULL_NAME ".$SESS_E_SIGN[2];
				$PIC_SIGN_PER = getPIC_SIGN($SIGN_PER_ID,$SIGN_PER_CARDNO);
			}
		} 
		
		
                if(count($arr_all_chk)=='27' || count($arr_all_chk)=='28'){
                    $pdf->Cell(200, 1, "", 0, 1, 'C', 0);		// space ����
                    if($PIC_SIGN_PER){  // ���ٻ�����
    /***			
                            $pdf->Cell(150, 10, "ŧ����", 0, 0, 'L', 0);
                            $pdf->Cell(15, 10, "", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x+80), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
                            $save_x = $pdf->x;		$save_y = $pdf->y;
                            $pdf->x += 5;			$pdf->y -= 15;

                            $pdf->Cell(130,10,"�����˹�ҷ������Թ", 0, 1, 'L', 0);

                            $pdf->Cell(200,10, "", 0, 1, 'C', 0);		// space ����
                            $pdf->Cell(200,30,"($SIGN_FULL_NAME)",0,1,"R");
    ****/
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(70, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(30, 15, "ŧ����", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x-14), ($pdf->y), 40, 10,"jpg");	// Original size = wxh (60x15) 200/5
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(30, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(68, 15, convert2thaidigit("�����˹�ҷ������Թ"), 0, 1, 'L', 0);
                    }else{
                            $pdf->Cell(220,7,"ŧ����.....................................................................................�����˹�ҷ������Թ",0,1,"C");
                    }		
                }  else {
                    $pdf->Cell(200, 10, "", 0, 1, 'C', 0);		// space ����
                    if($PIC_SIGN_PER){  // ���ٻ�����
    /***			
                            $pdf->Cell(150, 10, "ŧ����", 0, 0, 'L', 0);
                            $pdf->Cell(15, 10, "", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x+80), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
                            $save_x = $pdf->x;		$save_y = $pdf->y;
                            $pdf->x += 5;			$pdf->y -= 15;

                            $pdf->Cell(130,10,"�����˹�ҷ������Թ", 0, 1, 'L', 0);

                            $pdf->Cell(200,10, "", 0, 1, 'C', 0);		// space ����
                            $pdf->Cell(200,30,"($SIGN_FULL_NAME)",0,1,"R");
    ****/
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(70, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(30, 15, "ŧ����", 0, 0, 'L', 0);
                            $pdf->Image($PIC_SIGN_PER,($pdf->x-14), ($pdf->y), 40, 14,"jpg");	// Original size = wxh (60x15) 200/5
                            $pdf->SetFont($font,'',14);
                            $pdf->Cell(30, 7, "", 0, 0, 'L', 0);		//space
                            $pdf->Cell(68, 15, convert2thaidigit("�����˹�ҷ������Թ"), 0, 1, 'L', 0);
                    }else{
                            $pdf->Cell(220,7,"ŧ����.....................................................................................�����˹�ҷ������Թ",0,1,"C");
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
		$pdf->Cell(5,7, "", 0, 0, 'L', 0);		// space ����
		$pdf->Cell(195,7,"�����˵�",0,1,"L");

		$heading_text_s= array("","","","","");
		$head_text1 = implode(",",$heading_text_s);	// ����������
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
		$arr_data[1] = "�Թ�Ѻ";
		$arr_data[2] = "�.�.���.";
		$arr_data[3] = "�Թ��ҵͺ᷹���Ҩ���᷹��èѴ��ö��Шӵ��˹�";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "�.�.�.";
		$arr_data[3] = "�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���";			
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
		$arr_data[0] = "";
		$arr_data[1] = "";
		$arr_data[2] = "�.�.�.";
		$arr_data[3] = "�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

		$arr_data[0] = "";
		$arr_data[1] = "�Թ�ѡ";
		$arr_data[2] = "���Ѵ�Թ";
		$arr_data[3] = "���Ѵ�Թ";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "���ʴ�.���.";
		$arr_data[3] = "�Թ������ʴԡ�ø�Ҥ���Ҥ��ʧ������";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "�.�.�.";
		$arr_data[3] = "�Թ���¡�׹����Թ���� (�Թ��͹/��Ҩ�ҧ��Ш� �Թ����ԡ�ҡ���ؤ�ҡ�)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�319";
		$arr_data[3] = "���¡�׹�Թ�ͺ᷹����ɢ���Ҫ��ü�����Ѻ�Թ��͹����٧ ( ������)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�319";
		$arr_data[3] = "���¡�׹�Թ�ͺ᷹������١��ҧ��ШӼ�����Ѻ��Ҩ�ҧ�֧����٧ (������)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";	
		
        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.�0100";
		$arr_data[3] = "���¡�׹��й����Թ��Шӵ��˹觼��������дѺ�٧-��ҧ";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.";
		$arr_data[3] = "���¡�׹�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.";
		$arr_data[3] = "���¡�׹�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�";
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.���.";
		$arr_data[3] = "���¡�׹��й����Թ�ͺ᷹�����͹����Ҫ�������Թ��Шӵ��˹�";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.1-7";
		$arr_data[3] = "���¡�׹�Թ��й����Թ��ҵͺ᷹�����͹����Ҫ����дѺ 1-7";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.8-8 �.";
		$arr_data[3] = "���¡�׹�Թ��й����Թ�ͺ᷹�����͹����Ҫ��� �дѺ 8 ��� 8 �.";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "�.�.�ˡó�";
		$arr_data[3] = "�Թ�ҡ�ˡó�";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "�.�. Comp ICT";
		$arr_data[3] = "�Թ��� Computer ICT";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "����֡��";
		$arr_data[3] = "�Թ������͡���֡��";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "�.�.�. (��ا��)";
		$arr_data[3] = "�Թ���ͧ��Ҥ�á�ا��";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";		

        $arr_data[0] = "";
        $arr_data[1] = "";
		$arr_data[2] = "ä.�.�.�.";
		$arr_data[3] = "���¡�׹��й����Թ�����˵ؾ���ɢͧ�á.�����͹ (��黯Ժѵԧҹ��ҹ�Եԡ�)";		
		$arr_data[4] = "";
	
		$result = $pdf->add_data_tab($arr_data, 7, "", $data_align, "", "14", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

		$pdf->close_tab("");

	}else{
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,7,"********** ����բ����� **********",0,1,"C");
	} // end if

	$pdf->close();
        $pdf->Output();
        //$filename = 'personal_salary.pdf';/* ��� �������ǹ���Ŵ������ ��� Popup */
	//$pdf->Output($filename,'D');/* ��� �������ǹ���Ŵ������ ��� Popup */
	
	ini_set("max_execution_time", 60);

	function 	do_COLUMN_FORMAT($heading_text, $heading_width, $data_align) {
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index �ͧ head 
			$arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
		}
		$arr_column_width = $heading_width;	// �������ҧ
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>