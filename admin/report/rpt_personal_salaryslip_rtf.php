<?
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if(trim($_GET[SLIP_ID]))		$SLIP_ID=trim($_GET[SLIP_ID]);
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_personal_salaryslip_RTF.rtf";
	if (!$font) $font = "AngsanaUPC";

//	$RTF = new RTF();
	$RTF = new RTF("a4", 1150, 720, 600, 200);
	$RTF->set_default_font($font, 16);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "";
	$report_code = "SLIP";

//session_cache_limiter("nocache");
//	session_cache_limiter("private");

	//#### �֧����� ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//���ٻ����������
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
									NET_INCOME
					from		PER_SLIP
					where	SLIP_ID = $SLIP_ID ";
//	echo "$cmd<br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data = $db_dpis->get_array();
		$SLIP_YEAR = trim($data[SLIP_YEAR]);
		$SLIP_MONTH = trim($data[SLIP_MONTH]);
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
		$EXTRA_INCOME_01 = $data[EXTRA_INCOME_01];
		$INCOME_NAME_02 = trim($data[INCOME_NAME_02]);
		$EXTRA_INCOME_02 = $data[EXTRA_INCOME_02];
		$INCOME_NAME_03 = trim($data[INCOME_NAME_03]);
		$EXTRA_INCOME_03 = $data[EXTRA_INCOME_03];
		$INCOME_NAME_04 = trim($data[INCOME_NAME_04]);
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
		$EXTRA_DEDUCT_01 = $data[EXTRA_DEDUCT_01];
		$DEDUCT_NAME_02 = trim($data[DEDUCT_NAME_02]);
		$EXTRA_DEDUCT_02 = $data[EXTRA_DEDUCT_02];
		$DEDUCT_NAME_03 = trim($data[DEDUCT_NAME_03]);
		$EXTRA_DEDUCT_03 = $data[EXTRA_DEDUCT_03];
		$DEDUCT_NAME_04 = trim($data[DEDUCT_NAME_04]);
		$EXTRA_DEDUCT_04 = $data[EXTRA_DEDUCT_04];
		$DEDUCT_NAME_05 = trim($data[DEDUCT_NAME_05]);
		$EXTRA_DEDUCT_05 = $data[EXTRA_DEDUCT_05];
		$DEDUCT_NAME_06 = trim($data[DEDUCT_NAME_06]);
		$EXTRA_DEDUCT_06 = $data[EXTRA_DEDUCT_06];
		$DEDUCT_NAME_07 = trim($data[DEDUCT_NAME_07]);
		$EXTRA_DEDUCT_07 = $data[EXTRA_DEDUCT_07];
		$DEDUCT_NAME_08 = trim($data[DEDUCT_NAME_08]);
		$EXTRA_DEDUCT_08 = $data[EXTRA_DEDUCT_08];
		$OTHER_DEDUCT = $data[OTHER_DEDUCT];
		$TOTAL_DEDUCT = $data[TOTAL_DEDUCT];
		$NET_INCOME = $data[NET_INCOME];

		$RTF->open_line();			
		$RTF->cellImage("../images/logo_ocsc.jpg", 50, 100, "center", 0, "");
		$RTF->close_line();

		$HEAD_01 = "��Ѻ�ͧ��è����Թ��͹ - ��Ҩ�ҧ��Ш�����Թ���";
		$HEAD_02_1 = "��Ш���͹";
//		$HEAD_02_2 = "$month_full[($SLIP_MONTH+0)][TH] �� �.�. $SLIP_YEAR+0";
		$HEAD_02_2 = "".$month_full[($SLIP_MONTH+0)][TH]." �� �.�. ".($SLIP_YEAR+0)."";
		$HEAD_02_3 = " �ͧ ";
		$HEAD_03 = "˹��§ҹ ";
		$HEAD_04_1 = "�͹�Թ��� ";
		$HEAD_04_2 = " �Ţ���ѭ�� ";
		$RTF->set_font($font, 20);
//		$RTF->new_line();
		$RTF->add_text($RTF->bold(1) . $HEAD_01 . $RTF->bold(0) , "center");
		$RTF->new_line();
		$RTF->add_text($RTF->bold(1) . $HEAD_02_1 . $RTF->bold(0) . (($NUMBER_DISPLAY==2)?convert2thaidigit($HEAD_02_2):$HEAD_02_2) . $RTF->bold(1) . $HEAD_02_3 . $RTF->bold(0) . $PN_NAME . $PER_NAME . "  " . $PER_SURNAME , "center");
		$RTF->new_line();
		$RTF->add_text($RTF->bold(1) . $HEAD_03 . $RTF->bold(0) . $DEPARTMENT_NAME . "  " . $ORG_NAME , "center");
		$RTF->new_line();
		$RTF->add_text($RTF->bold(1) . $HEAD_04_1 . $RTF->bold(0) . $BANK_NAME . "  " . $BRANCH_NAME . $RTF->bold(1) . $HEAD_04_2 . $RTF->bold(0) . (($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BANK_ACCOUNT):$PER_BANK_ACCOUNT) , "center");
		$RTF->ln();
		$RTF->add_text($RTF->bold(1) . $RTF->underline(1) . "��¡���Ѻ" . $RTF->underline(0) . $RTF->bold(0) , "left");
//		$RTF->new_line();

		// ��������ҧ
		$RTF->ln();			
		$RTF->set_table_font($font, 16);

		$RTF->open_line();			
		$RTF->cell("�Թ��͹/��Ҩ�ҧ��Ш�", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_01,2)):number_format($INCOME_01, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ��͹/��Ҩ�ҧ��Ш� (���ԡ)", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_02,2)):number_format($INCOME_02, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ��Шӵ��˹�/�ԪҪվ", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_03,2)):number_format($INCOME_03, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ��Шӵ��˹�/�ԪҪվ (���ԡ)", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_04,2)):number_format($INCOME_04, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($INCOME_05 > 0) {
			$RTF->open_line();			
			$RTF->cell("�.�.�./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_05,2)):number_format($INCOME_05, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_06 > 0) {
			$RTF->open_line();			
			$RTF->cell("�.�.�./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_06,2)):number_format($INCOME_06, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_07 > 0) {
			$RTF->open_line();			
			$RTF->cell("�.�.�./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_07,2)):number_format($INCOME_07, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_08 > 0) {
			$RTF->open_line();			
			$RTF->cell("�.�.�./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_08,2)):number_format($INCOME_08, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_09 > 0) {
			$RTF->open_line();			
			$RTF->cell("ʻ�./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_09,2)):number_format($INCOME_09, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_10 > 0) {
			$RTF->open_line();			
			$RTF->cell("���./���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_10,2)):number_format($INCOME_10, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹�", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_11,2)):number_format($INCOME_11, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ��ҵͺ᷹�����͹����Ҫ�����ҡѺ�Թ��Шӵ��˹� (���ԡ)", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_12,2)):number_format($INCOME_12, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �."):"�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �."), "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_13,2)):number_format($INCOME_13, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �. (���ԡ)"):"�Թ��ҵͺ᷹�����͹����Ҫ����дѺ 8 ��� 8 �. (���ԡ)"), "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_14,2)):number_format($INCOME_14, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7/�١��ҧ/���ԡ"):"�Թ��ҵͺ᷹�����͹����Ѻ����Ҫ����дѺ 1-7/�١��ҧ/���ԡ"), "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_15,2)):number_format($INCOME_15, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("�Թ�ͺ᷹�����(2%,4%)����Ҫ���/�١��ҧ��Ш�/���ԡ"):"�Թ�ͺ᷹�����(2%,4%)����Ҫ���/�١��ҧ��Ш�/���ԡ"), "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_16,2)):number_format($INCOME_16, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($INCOME_17 > 0) {
			$RTF->open_line();			
			$RTF->cell("�����Һ�ҹ/���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_17,2)):number_format($INCOME_17, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_18 > 0) {
			$RTF->open_line();			
			$RTF->cell("��������ͺص�/���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_18,2)):number_format($INCOME_18, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_19 > 0) {
			$RTF->open_line();			
			$RTF->cell("����֡�Һص�/���ԡ", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_19,2)):number_format($INCOME_19, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($INCOME_20 > 0) {
			$RTF->open_line();			
			$RTF->cell("�Թ�ҧ���/�Թ��ҷ��", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($INCOME_20,2)):number_format($INCOME_20, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_INCOME_01 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($INCOME_NAME_01):$INCOME_NAME_01), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_INCOME_01,2)):number_format($EXTRA_INCOME_01, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_INCOME_02 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($INCOME_NAME_02):$INCOME_NAME_02), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_INCOME_02,2)):number_format($EXTRA_INCOME_02, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_INCOME_03 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($INCOME_NAME_03):$INCOME_NAME_03), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_INCOME_03,2)):number_format($EXTRA_INCOME_03, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_INCOME_04 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($INCOME_NAME_04):$INCOME_NAME_04), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_INCOME_04,2)):number_format($EXTRA_INCOME_04, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("����", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($OTHER_INCOME,2)):number_format($OTHER_INCOME, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�����¡���Ѻ", "70", "left", "0");
		$RTF->cell($RTF->underline(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_INCOME,2)):number_format($TOTAL_INCOME, 2)) . $RTF->underline(0), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();

//		$RTF->ln();
		$RTF->add_text($RTF->bold(1) . $RTF->underline(1) . "��¡���ѡ" . $RTF->underline(0) . $RTF->bold(0) , "left");

		// ��������ҧ
		$RTF->ln();			
		$RTF->set_table_font($font, 16);

		$RTF->open_line();			
		$RTF->cell("����/���ԡ", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_01,2)):number_format($DEDUCT_01, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($DEDUCT_02 > 0) {
			$RTF->open_line();			
			$RTF->cell("�Թ������ͷ�����������", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_02,2)):number_format($DEDUCT_02, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("������/�Թ����ˡó�", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_03,2)):number_format($DEDUCT_03, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($DEDUCT_04 > 0) {
			$RTF->open_line();			
			$RTF->cell("�Թ������͡���֡��", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_04,2)):number_format($DEDUCT_04, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("���./���ԡ,�ʨ./���ԡ", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_05,2)):number_format($DEDUCT_05, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("���. (��������)", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_06,2)):number_format($DEDUCT_06, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ��� (���.)", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_07,2)):number_format($DEDUCT_07, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($DEDUCT_08 > 0) {
			$RTF->open_line();			
			$RTF->cell("�Թ��� (��.)", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_08,2)):number_format($DEDUCT_08, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($DEDUCT_09 > 0) {
			$RTF->open_line();			
			$RTF->cell("�.�.�.", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_09,2)):number_format($DEDUCT_09, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($DEDUCT_10 > 0) {
			$RTF->open_line();			
			$RTF->cell("�Թ��� (��.)", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_10,2)):number_format($DEDUCT_10, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("����ҧ��", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_11,2)):number_format($DEDUCT_11, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ���¡�׹", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_12,2)):number_format($DEDUCT_12, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($DEDUCT_13 > 0) {
			$RTF->open_line();			
			$RTF->cell("����Ҹ�óٻ���", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_13,2)):number_format($DEDUCT_13, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("�Թ���ʴԡ�������", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_14,2)):number_format($DEDUCT_14, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("��Ҭһ��Ԩ", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_15,2)):number_format($DEDUCT_15, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		if ($DEDUCT_16 > 0) {
			$RTF->open_line();			
			$RTF->cell("��. ʧ������", "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($DEDUCT_16,2)):number_format($DEDUCT_16, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_01 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_01):$DEDUCT_NAME_01), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_01,2)):number_format($EXTRA_DEDUCT_01, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_02 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_02):$DEDUCT_NAME_02), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_02,2)):number_format($EXTRA_DEDUCT_02, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_03 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_03):$DEDUCT_NAME_03), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_03,2)):number_format($EXTRA_DEDUCT_03, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_04 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_04):$DEDUCT_NAME_04), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_04,2)):number_format($EXTRA_DEDUCT_04, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_05 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_05):$DEDUCT_NAME_05), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_05,2)):number_format($EXTRA_DEDUCT_05, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_06 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_06):$DEDUCT_NAME_06), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_06,2)):number_format($EXTRA_DEDUCT_06, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_07 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_07):$DEDUCT_NAME_07), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_07,2)):number_format($EXTRA_DEDUCT_07, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		if ($EXTRA_DEDUCT_08 > 0) {
			$RTF->open_line();			
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($DEDUCT_NAME_08):$DEDUCT_NAME_08), "70", "left", "0");
			$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($EXTRA_DEDUCT_08,2)):number_format($EXTRA_DEDUCT_08, 2)), "15", "right", "0");
			$RTF->cell("�ҷ", "5", "right", "0");
			$RTF->close_line();
		}
		
		$RTF->open_line();			
		$RTF->cell("����", "70", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($OTHER_DEDUCT,2)):number_format($OTHER_DEDUCT, 2)), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�����¡���ѡ", "70", "left", "0");
		$RTF->cell($RTF->underline(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($TOTAL_DEDUCT,2)):number_format($TOTAL_DEDUCT, 2)) . $RTF->underline(0), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�ӹǹ�Թ����͹��Ҹ�Ҥ��", "70", "left", "0");
		$RTF->cell($RTF->bold(1) . $RTF->underline(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($NET_INCOME,2)):number_format($NET_INCOME, 2)) . $RTF->underline(0) . $RTF->bold(0), "15", "right", "0");
		$RTF->cell("�ҷ", "5", "right", "0");
		$RTF->close_line();
		
//		$RTF->new_line();
		$RTF->add_text($RTF->underline(1) . "                                                                                                                                                                                                              " . $RTF->underline(0), "left");

		// �Ҽ����˹�ҷ������Թ
		//print("<pre>"); print_r($SESS_E_SIGN); print("</pre>");		// 1-> Ẻ�����Թ�š�û�Ժѵ��Ҫ���   2->���   3->��Ի�Թ��͹   4->˹ѧ����Ѻ�ͧ 
		$PIC_SIGN="";
		$SIGN_TYPE = 1;	 // type ��Ի�Թ��͹
		$SIGN_START_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-01";
		$SIGN_END_DATE = ($SLIP_YEAR-543)."-".str_pad(trim($SLIP_MONTH), 2, "0", STR_PAD_LEFT)."-31";
		//��������繤� �����˹�ҷ������Թ  NVL
		$cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION from PER_SIGN 
						where SIGN_TYPE = '$SIGN_TYPE' and SIGN_PER_TYPE = 1 and (SIGN_STARTDATE<='$SIGN_START_DATE' and SIGN_STARTDATE<='$SIGN_END_DATE') 
						 and ((SIGN_ENDDATE>='$SIGN_START_DATE' and SIGN_ENDDATE>='$SIGN_END_DATE') or (SIGN_ENDDATE IS NULL))
						order by SIGN_STARTDATE desc, SIGN_ENDDATE desc ";	
		$count_exist=$db_dpis->send_cmd($cmd);
//echo "$count_exist -> $cmd";
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
		$RTF->ln();
		if($PIC_SIGN_PER){  // ���ٻ�����
			$RTF->open_line();	
			$RTF->cell("", "20", "left", "0");
			$RTF->cell("ŧ����", "10", "right", "0");
			$RTF->cellImagexy($PIC_SIGN_PER, 35, 14, 30, "center", 0);
			$RTF->cell("�����˹�ҷ������Թ", "20", "left", "0");
			$RTF->close_line();
		}else{
			$RTF->open_line();	
			$RTF->cell("", "25", "left", "0");
			$RTF->cell("ŧ����.....................................................................................�����˹�ҷ������Թ", "70", "center", "0");
			$RTF->close_line();
		}		

		$RTF->open_line();	
		$RTF->cell("", "30", "left", "0");
		if ($SIGN_NAME)
			$RTF->cell("($SIGN_NAME)", "35", "center", "0");
		else
			$RTF->cell("(.....................................................................................)", "50", "center", "0");
		$RTF->cell("", "20", "right", "0");
		$RTF->close_line();

		$RTF->new_line();
		$RTF->add_text($RTF->bold(1) . "�����˵�" . $RTF->bold(0), "left");

		// ��������ҧ
		$RTF->ln();			
		$RTF->set_table_font($font, 16);

		$RTF->open_line();			
		$RTF->cell("�Թ�Ѻ", "10", "left", "0");
		$RTF->cell("�.�.���.", "20", "left", "0");
		$RTF->cell("�Թ��ҵͺ᷹���Ҩ���᷹��èѴ��ö��Шӵ��˹�", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�.�.", "20", "left", "0");
		$RTF->cell("�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�.�.", "20", "left", "0");
		$RTF->cell("�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("�Թ�ѡ", "10", "left", "0");
		$RTF->cell("���Ѵ�Թ", "20", "left", "0");
		$RTF->cell("���Ѵ�Թ", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("���ʴ�.���.", "20", "left", "0");
		$RTF->cell("�Թ������ʴԡ�ø�Ҥ���Ҥ��ʧ������", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�.�.", "20", "left", "0");
		$RTF->cell("�Թ���¡�׹����Թ���� (�Թ��͹/��Ҩ�ҧ��Ш� �Թ����ԡ�ҡ���ؤ�ҡ�)", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("ä.�.�.�319"):"ä.�.�.�319"), "20", "left", "0");
		$RTF->cell("���¡�׹�Թ�ͺ᷹����ɢ���Ҫ��ü�����Ѻ�Թ��͹����٧ ( ������)", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("ä.�.�.�319"):"ä.�.�.�319"), "20", "left", "0");
		$RTF->cell("���¡�׹�Թ�ͺ᷹������١��ҧ��ШӼ�����Ѻ��Ҩ�ҧ�֧����٧ (������)", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("ä.�.�.�.�0100"):"ä.�.�.�.�0100"), "20", "left", "0");
		$RTF->cell("���¡�׹��й����Թ��Шӵ��˹觼��������дѺ�٧-��ҧ", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("ä.�.�.�.", "20", "left", "0");
		$RTF->cell("���¡�׹�Թ������ä�ͧ�վ���Ǥ��Ǣ���Ҫ���", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("ä.�.�.�.", "20", "left", "0");
		$RTF->cell("���¡�׹�Թ������ä�ͧ�վ���Ǥ����١��ҧ��Ш�", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("ä.�.�.�.���.", "20", "left", "0");
		$RTF->cell("���¡�׹��й����Թ�ͺ᷹�����͹����Ҫ�������Թ��Шӵ��˹�", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("ä.�.�.�.1-7"):"ä.�.�.�.1-7"), "20", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("���¡�׹�Թ��й����Թ��ҵͺ᷹�����͹����Ҫ����дѺ 1-7"):"���¡�׹�Թ��й����Թ��ҵͺ᷹�����͹����Ҫ����дѺ 1-7"), "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("ä.�.�.8-8 �."):"ä.�.�.8-8 �."), "20", "left", "0");
		$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit("���¡�׹�Թ��й����Թ�ͺ᷹�����͹����Ҫ��� �дѺ 8 ��� 8 �."):"���¡�׹�Թ��й����Թ�ͺ᷹�����͹����Ҫ��� �дѺ 8 ��� 8 �."), "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�.�ˡó�", "20", "left", "0");
		$RTF->cell("�Թ�ҡ�ˡó�", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�. Comp ICT", "20", "left", "0");
		$RTF->cell("�Թ��� Computer ICT", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("����֡��", "20", "left", "0");
		$RTF->cell("�Թ������͡���֡��", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("�.�.�. (��ا��)", "20", "left", "0");
		$RTF->cell("�Թ���ͧ��Ҥ�á�ا��", "70", "left", "0");
		$RTF->close_line();
		
		$RTF->open_line();			
		$RTF->cell("", "10", "left", "0");
		$RTF->cell("ä.�.�.�.", "20", "left", "0");
		$RTF->cell("���¡�׹��й����Թ�����˵ؾ���ɢͧ�á.�����͹ (��黯Ժѵԧҹ��ҹ�Եԡ�)", "70", "left", "0");
		$RTF->close_line();
		
	}else{
		$RTF->new_line();
		$RTF->add_text("********** ����բ����� **********", "center");
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 60);	
?>