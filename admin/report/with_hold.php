<?php

    include("../../php_scripts/connect_database.php");
    include("../php_scripts/pdf_wordarray_thaicut.php");
    include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");

    define('FPDF_FONTPATH', '../../PDF/font/');
    include ("../../PDF/fpdf.php");
    include ("../../PDF/pdf_extends_DPIS.php");

    ini_set("max_execution_time", $max_execution_time);

    $IMG_PATH = "../../attachment/pic_personal/";

    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

    //--------------------------------------------------select data-------------------------------------------------------
    // ���������硷�͹ԡ��
    $cmd_sign = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG WHERE CONFIG_NAME='E_SIGN' ";
    $db->send_cmd($cmd_sign);
    $data_sign = $db->get_array();
    $tmp_e_signs = explode("||", trim($data_sign['CONFIG_VALUE']));
    for ($i = 0; $i < count($tmp_e_signs); $i++) {
        if ($tmp_e_signs[$i] != "") {
            $E_SIGN[$tmp_e_signs[$i]] = 1;
        }
    }

    $RPT_PER_ID = $PER_ID;
    $RPT_STAX_ID = $STAX_IDS;
    $RPT_TAX_YEAR = $TAX_YEAR;

    $cmd = "SELECT DEPARTMENT_ID
                      FROM PER_PERSONAL
                      where  PER_ID = $RPT_PER_ID  ";
    $db_dpis2->send_cmd($cmd);
    $data = $db_dpis2->get_array();
    $TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
    //echo "search_per_type=$search_per_type<br>";

    $cmd = "SELECT STAX_ID,PER_ID,ORGTAX_NO,ORG_NAME,ORG_ADDR,PER_CARDNO,
                        PER_TAXNO,PER_NAME,PER_ADDR,SEQ_NO,FORMTAX_TYPE,INCOME ,TAX_YEAR,
                        NET_INCOME,NETTAX_INCOME,NETTAX_CHAR,NETSAVING_TYPE,NET_SAVING,TAX_INCOME,TAX_TYPE,TAX_DATE,TAX_NUM
                      FROM PER_TAXHIS
                      where  PER_ID = $RPT_PER_ID  and STAX_ID = $RPT_STAX_ID and TAX_YEAR = $RPT_TAX_YEAR";
    $db_dpis2->send_cmd($cmd);
    //die("<pre>".$cmd);
    $data = $db_dpis2->get_array();

    $ORGTAX_NO = $data[ORGTAX_NO];
    $ORG_NAME = $data[ORG_NAME];
    $ORG_ADDR = $data[ORG_ADDR];
    $PER_CARDNO = $data[PER_CARDNO];
    $PER_TAXNO = $data[PER_TAXNO];
    $PER_NAME = $data[PER_NAME];
    $PER_ADDR = $data[PER_ADDR];
    $SEQ_NO = $data[SEQ_NO];
    $SEQ_NO_TO_NUM = (int) $SEQ_NO;
    $TAX_NUM = $data[TAX_NUM];
    $TAX_NUM_TO_NUM = (int) $TAX_NUM;
    $FORMTAX_TYPE = $data[FORMTAX_TYPE];
    $INCOME_OR = number_format($data[INCOME], 2);
    $TAX_YEAR = $data[TAX_YEAR];
    $TAX_INCOME_OR = number_format($data[TAX_INCOME], 2);
    $NET_INCOME_OR = number_format($data[NET_INCOME], 2);
    $NETTAX_INCOME_OR = number_format($data[NETTAX_INCOME], 2);

    $INCOME = explode(".", $INCOME_OR);
    $INCOME_B = $INCOME[0];
    $INCOME_S = $INCOME[1];
    $TAX_INCOME = explode(".", $TAX_INCOME_OR);
    $TAX_INCOME_B = $TAX_INCOME[0];
    $TAX_INCOME_S = $TAX_INCOME[1];
    $NET_INCOME = explode(".", $NET_INCOME_OR);
    $NET_INCOME_B = $NET_INCOME[0];
    $NET_INCOME_S = $NET_INCOME[1];
    $NETTAX_INCOME = explode(".", $NETTAX_INCOME_OR);
    $NETTAX_INCOME_B = $NETTAX_INCOME[0];
    $NETTAX_INCOME_S = $NETTAX_INCOME[1];

    $NETTAX_CHAR = $data[NETTAX_CHAR];
    $NETSAVING_TYPE = $data[NETSAVING_TYPE];
    $NET_SAVING = number_format($data[NET_SAVING], 2);
    $TAX_TYPE = $data[TAX_TYPE];
    $TAX_DATE_OR = $data[TAX_DATE];
    if ($TAX_DATE_OR) {
        $TAX_DATE_A = getmonth_slip($TAX_DATE_OR);
    } else {
        $TAX_DATE_A = "  /                   /  ";
    }
    //$PIC_SIGN_PER = "";
    //--------------------------------------------------start pdf-------------------------------------------------------------
    $unit = "mm";
    $paper_size = "A4";
    $lang_code = "TH";
    $company_name = "";
    $report_title = "";
    $report_code = "";
    $orientation = 'P';
    $pdf = new PDF($orientation, $unit, $paper_size, $lang_code, $company_name, $report_title, $report_code, $heading, $heading_width, $heading_align);

    $pdf->Open();
    $pdf->SetMargins(30, 5, 5);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont($font, '', 12);
    $pdf->SetTextColor(hexdec("00"), hexdec("00"), hexdec("00"));
    $pdf->text(16, 10, "��Ѻ��� 1 (����Ѻ���١�ѡ���� � ������ ��Ṻ������ѺẺ�ʴ���¡������)");
    $pdf->text(16, 16, "��Ѻ��� 2 (����Ѻ���١�ѡ���� � ������ ���������ѡ�ҹ)");
    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    $pdf->Rect(10, 20, 190, 250, 'D'); //��ͺ�˭�
    $pdf->SetFont($font, '', 14);
    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;



    $pdf->SetFont($font, '', 14);
    $pdf->text(82, 26, "˹ѧ����Ѻ�ͧ����ѡ���� � ������");
    $pdf->text(185, 26, "");
    $pdf->text(175, 27, "�������.................");
    $pdf->Ln();

    $pdf->SetFont($font, '', 12);
    $pdf->text(82, 31, "����ҵ�� 50 ��� ��觻�������ɮҡ�");
    $pdf->SetFont($font, '', 14);
    $pdf->text(186, 32, "$TAX_NUM_TO_NUM");
    $pdf->text(175, 33, "�Ţ���.................");
    $pdf->Ln(15);


    //------------------------------------------------�ŧ  ��-��͹-�ѹ-------------------------------------------------------
    $day = substr($TAX_DATE_OR, 0, 2);
    $mounth = substr($TAX_DATE_OR, 2, 2);
    $year = substr($TAX_DATE_OR, 4, 4);
    $year_c = $year - 543;
    $YMD_SIGN = $year_c . "-" . $mounth . "-" . $day;


    //------------------------------------------------�Ѵ format �Ţ�ѵ�------------------------------------------------------
    if ($ORGTAX_NO) {
        $orgtax_no_one = substr($ORGTAX_NO, 0, 1);
        $orgtax_no_two = substr($ORGTAX_NO, 1, 4);
        $orgtax_no_three = substr($ORGTAX_NO, 5, 5);
        $orgtax_no_four = substr($ORGTAX_NO, 10, 2);
        $orgtax_no_five = substr($ORGTAX_NO, 12, 1);
        $F_ORGTAX_NO = $orgtax_no_one . "-" . $orgtax_no_two . "-" . $orgtax_no_three . "-" . $orgtax_no_four . "-" . $orgtax_no_five;
    } else {
        $F_ORGTAX_NO = "";
    }
    if ($PER_CARDNO) {
        $per_cardno_one = substr($PER_CARDNO, 0, 1);
        $per_cardno_two = substr($PER_CARDNO, 1, 4);
        $per_cardno_three = substr($PER_CARDNO, 5, 5);
        $per_cardno_four = substr($PER_CARDNO, 10, 2);
        $per_cardno_five = substr($PER_CARDNO, 12, 1);
        $F_PER_CARDNO = $per_cardno_one . "-" . $per_cardno_two . "-" . $per_cardno_three . "-" . $per_cardno_four . "-" . $per_cardno_five;
    } else {
        $F_PER_CARDNO = "";
    }
    if ($PER_TAXNO) {
        $per_taxno_one = substr($PER_TAXNO, 0, 1);
        $per_taxno_two = substr($PER_TAXNO, 1, 4);
        $per_taxno_three = substr($PER_TAXNO, 5, 4);
        $per_taxno_four = substr($PER_TAXNO, 9, 1);
        $F_PER_TAXNO = $per_taxno_one . "-" . $per_taxno_two . "-" . $per_taxno_three . "-" . $per_taxno_four;
    } else {
        $F_PER_TAXNO = "";
    }

    //-------------------------------------------------function----------------------------------------------------------


    function get_org_n_a($org_n_a) {
        $org_n_a_r = explode(" ", $org_n_a);
      
        $re_n_a = $org_n_a_r[0];
        return $re_n_a;
    }
    function get_org_n_a_add($org_n_a) {
        $org_n_a_r = explode(" ", $org_n_a);
        for ($i = 0; $i < count(explode(" ", $org_n_a, -1)); $i++) {
            $re_n_a .= $org_n_a_r[$i] . " ";
        }
        
        return $re_n_a;
    }

    //echo get_org_n_a($ORG_ADDR); //print_r(explode(" ",$ORG_ADDR,-1));



    function getmonth_slip($date) {
        $day = substr($date, 0, 2);
        $mounth = substr($date, 2, 2);
        $year = substr($date, 4, 4);

        if ($mounth == "1") {
            $full_mounth_th = "���Ҥ�";
        } else if ($mounth == "2") {
            $full_mounth_th = "����Ҿѹ��";
        } else if ($mounth == "3") {
            $full_mounth_th = "�չҤ�";
        } else if ($mounth == "4") {
            $full_mounth_th = "����¹";
        } else if ($mounth == "5") {
            $full_mounth_th = "����Ҥ�";
        } else if ($mounth == "6") {
            $full_mounth_th = "�Զع�¹";
        } else if ($mounth == "7") {
            $full_mounth_th = "�á�Ҥ�";
        } else if ($mounth == "8") {
            $full_mounth_th = "�ԧ��¹";
        } else if ($mounth == "9") {
            $full_mounth_th = "�ѹ��¹";
        } else if ($mounth == "10") {
            $full_mounth_th = "���Ҥ�";
        } else if ($mounth == "11") {
            $full_mounth_th = "��Ȩԡ�¹";
        } else if ($mounth == "12") {
            $full_mounth_th = "�ѹ�Ҥ�";
        }

        $dmy = $day . " / " . $full_mounth_th . " / " . $year;
        return $dmy;
    }

    //#### �֧����� ####//
    function getPIC_SIGN($PER_ID, $PER_CARDNO) {
        global $db_dpis, $db_dpis2;

        $PIC_SIGN = "";
        //���ٻ����������
        $cmd = "select * from PER_PERSONALPIC
                        where PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1 AND PIC_SHOW=1 ";

        $count_pic_sign = $db_dpis2->send_cmd($cmd);
        //echo $cmd . "<br>";
        if ($count_pic_sign > 0) {
            $data2 = $db_dpis2->get_array();

            $TMP_PIC_SEQ = $data2[PER_PICSEQ];
            $current_list .= ((trim($current_list)) ? "," : "") . $TMP_PIC_SEQ;
            $T_PIC_SEQ = substr("000", 0, 3 - strlen("$TMP_PIC_SEQ")) . "$TMP_PIC_SEQ";
            $TMP_SERVER = $data2[PIC_SERVER_ID];
            $TMP_PIC_SIGN = $data2[PIC_SIGN];

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
            if ($TMP_PIC_SIGN == 1) {
                $SIGN_NAME = "SIGN";
            }
            if (trim($PER_ID) && trim($PER_CARDNO) != "NULL") {
                $TMP_PIC_NAME = $data2[PER_PICPATH] . $PER_CARDNO . "-" . $SIGN_NAME . $T_PIC_SEQ . ".jpg" . ($tmp_SERVER_NAME ? " [" . $tmp_SERVER_NAME . "]" : "");
                //$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
            } else {
                $TMP_PIC_NAME = $data2[PER_PICPATH] . $data2[PER_GENNAME] . "-" . $SIGN_NAME . $T_PIC_SEQ . ".jpg" . ($tmp_SERVER_NAME ? " [" . $tmp_SERVER_NAME . "]" : "");
                //$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
            }
            if (file_exists("../" . $TMP_PIC_NAME)) {
                $PIC_SIGN = "../" . $TMP_PIC_NAME;  //	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
            }
        } //end count
        //echo $TMP_PIC_SIGN. "../".$TMP_PIC_NAME . "<BR>";
        return $PIC_SIGN;
    }

    // �Ҽ����˹�ҷ������Թ
    //print("<pre>"); print_r($E_SIGN); print("</pre>");		// 1-> Ẻ�����Թ�š�û�Ժѵ��Ҫ���   2->���   3->��Ի�Թ��͹   4->˹ѧ����駼š������͹�Թ��͹  5->˹ѧ����Ѻ�ͧ
    $PIC_SIGN = $SIGN_NAME = $SIGN_POSITION = $PIC_SIGN_PER = "";
    $SIGN_TYPE = 4;  // type ˹ѧ����Ѻ�ͧ����ѡ���� � ������
    //��������繤� �����˹�ҷ������Թ  NVL
    $cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION from PER_SIGN where DEPARTMENT_ID = $TMP_DEPARTMENT_ID
                                            and SIGN_TYPE = '$SIGN_TYPE'
                                            and SIGN_PER_TYPE = $search_per_type
                                            and ((SIGN_ENDDATE IS NOT NULL and ('$YMD_SIGN' between SIGN_STARTDATE and SIGN_ENDDATE or '$YMD_SIGN' between SIGN_STARTDATE
                                            and SIGN_ENDDATE))
                                            or (SIGN_ENDDATE IS NULL and '$YMD_SIGN' >= SIGN_STARTDATE))
                                            order by SIGN_STARTDATE desc, SIGN_ENDDATE desc";


    /* $cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION from PER_SIGN
      where DEPARTMENT_ID = $TMP_DEPARTMENT_ID and SIGN_TYPE = '$SIGN_TYPE' and SIGN_PER_TYPE = $search_per_type and ((SIGN_ENDDATE IS NOT NULL and ('$YMD_SIGN' between SIGN_STARTDATE and SIGN_ENDDATE or '$YMD_SIGN' between SIGN_STARTDATE and SIGN_ENDDATE)) or (SIGN_ENDDATE IS NULL and '$YMD_SIGN' >= SIGN_STARTDATE))
      order by SIGN_STARTDATE desc, SIGN_ENDDATE desc "; */

    //die($cmd);
    $count_exist = $db_dpis2->send_cmd($cmd);
    //echo "$count_exist -> $cmd";
    if ($count_exist > 0) {
        $data2 = $db_dpis2->get_array();
        $SIGN_PER_ID = $data2[PER_ID];
        $SIGN_NAME = trim($data2[SIGN_NAME]);
        $SIGN_POSITION = trim($data2[SIGN_POSITION]);
        if ($SIGN_PER_ID && $E_SIGN[4] == 1) { // ���ٻẺ�ͧ���������礷�͹ԡ��
            // �� PER_CARDNO
            //echo $SIGN_PER_ID;
            $cmd = " select 	PER_CARDNO from PER_PERSONAL where PER_ID=$SIGN_PER_ID ";
            $db_dpis2->send_cmd($cmd);
            $data2 = $db_dpis2->get_array();
            $SIGN_PER_CARDNO = trim($data2[PER_CARDNO]);

            //echo "$cmd ->$SIGN_FULL_NAME ".$E_SIGN[4];
            $PIC_SIGN_PER = getPIC_SIGN($SIGN_PER_ID, $SIGN_PER_CARDNO);
            //echo "pic = ".$SIGN_PER_ID.$SIGN_PER_CARDNO;
        }
    }

    //$ORG_NAME = "�ӹѡ�ҹ��С�����û�ͧ�ѹ��л�Һ������÷ب�Ե��Ҥ�Ѱ  ��ǹ�Ҫ�������ѧ�Ѵ�ӹѡ��¡�Ѱ�����  ��з�ǧ �����";
    //---------------------------------------------------- ��ͺ��ͧ���1-------------------------------------------------------

    $pdf->Rect(14, 35, 182, 30, 'D');
    $pdf->SetFont($font, '', 14);
    $pdf->text(20, 40, "�����˹�ҷ���ѡ���� � ������ :-", 0, 1, 'L');

    $pdf->SetFont($font, '', 12);
    $pdf->text(114, 40, "�Ţ��Шӵ�ǻ�ЪҪ�", 0, 1, 'L');
    $pdf->text(20, 48, "����   ", 0, 1, 'L');
    $pdf->text(24, 48, "    -------------------------------------------------------------------------------------");
    $pdf->text(28, 52, "( ����к������ �ؤ�� �ԵԺؤ�� ����ѷ ��Ҥ� ���ͤ�кؤ�� )");
    $pdf->text(116, 45, "�Ţ��Шӵ�Ǽ�����������ҡ�", 0, 1, 'L');
    $pdf->text(120, 50, "( ��͡੾�Сó��繼��������Ţ�ѵû�Шӵ�ǻ�ЪҪ� )", 0, 1, 'L');
    $pdf->text(20, 58, "�������   ", 0, 1, 'L');
    $pdf->text(24, 59, "    -------------------------------------------------------------------------------------------------------------------------------------------------------------------");
    $pdf->text(28, 63, "( ����к� �����Ҥ��/�����ҹ ��ͧ�Ţ��� ��鹷�� �Ţ��� ��͡/��� ������ ��� �Ӻ�/�ǧ �����/ࢵ �ѧ��Ѵ )");


    $pdf->SetFont($font, '', 13);

    $pdf->text(150, 44, $F_ORGTAX_NO);                                                //��ͧ��͡ ������
    $pdf->text(28, 45, get_org_n_a($ORG_NAME));                                                 //��ͧ��͡ ������
    $pdf->text(28, 57, get_org_n_a_add($ORG_ADDR));                                                //��ͧ��͡ ������
    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    //---------------------------------------------------- ��ͺ��ͧ���2-------------------------------------------------------

    $pdf->Rect(14, 68, 182, 52, 'D');
    $pdf->SetFont($font, '', 14);
    $pdf->text(20, 73, "���١�ѡ���� � ������ :-");

    $pdf->SetFont($font, '', 12);
    $pdf->text(114, 73, "�Ţ��Шӵ�ǻ�ЪҪ�");
    $pdf->text(20, 81, "����   ");
    $pdf->text(24, 81, "    -------------------------------------------------------------------------------------");
    $pdf->text(28, 84, "( ����к������ �ؤ�� �ԵԺؤ�� ����ѷ ��Ҥ� ���ͤ�кؤ�� )");
    $pdf->text(116, 78, "�Ţ��Шӵ�Ǽ�����������ҡ�");
    $pdf->text(120, 83, "( ��͡੾�Сó��繼��������Ţ�ѵû�Шӵ�ǻ�ЪҪ� )");
    $pdf->text(20, 91, "�������   ");
    $pdf->text(24, 91, "    -------------------------------------------------------------------------------------------------------------------------------------------------------------------");
    $pdf->text(28, 94, "( ����к� �����Ҥ��/�����ҹ ��ͧ�Ţ��� ��鹷�� �Ţ��� ��͡/��� ������ ��� �Ӻ�/�ǧ �����/ࢵ �ѧ��Ѵ )");

    $pdf->text(20, 102, "�ӴѺ���   ");
    $pdf->Rect(33, 97, 18, 7, 'D');
    $pdf->text(56, 102, "�Ẻ   ");

    $pdf->Rect(92, 97, 6, 6, 'D');
    $pdf->text(100, 101, "(1) �.�.�.1�");
    $pdf->Rect(116, 97, 6, 6, 'D');
    $pdf->SetFont($font, '', 18);
    $pdf->SetFont($font, '', 12);
    $pdf->text(124, 101, "(2) �.�.�.1� �����");
    $pdf->Rect(148, 97, 6, 6, 'D');
    $pdf->text(156, 101, "(3) �.�.�.2");
    $pdf->Rect(170, 97, 6, 6, 'D');
    $pdf->text(178, 101, "(4) �.�.�.3");

    $pdf->text(24, 110, "(�������ö��ҧ�ԧ�����ͺ�ѹ�Ѻ�������ҧ�ӴѺ���");
    $pdf->text(24, 114, "���˹ѧ����Ѻ�ͧϡѺẺ�����¡�������ѡ����)");
    $pdf->Rect(92, 107, 6, 6, 'D');
    $pdf->text(100, 111, "(5) �.�.�.2�");
    $pdf->Rect(116, 107, 6, 6, 'D');
    $pdf->text(124, 111, "(6) �.�.�.3�");
    $pdf->Rect(148, 107, 6, 6, 'D');
    $pdf->text(156, 111, "(7) �.�.�.53");

    $pdf->SetFont($font, '', 14);

    $pdf->text(145, 72, $F_PER_CARDNO);                                                      //��ͧ��͡ ������
    if ($F_PER_CARDNO) {
        $pdf->text(150, 77, "");                                                      //��ͧ��͡ ������
    } else {
        $pdf->text(150, 77, $F_PER_TAXNO);
    }
    $pdf->text(28, 78, $PER_NAME);                                                       //��ͧ��͡ ������
    $pdf->text(28, 89, $PER_ADDR);                                                      //��ͧ��͡ ������
    $pdf->text(40, 101, $SEQ_NO_TO_NUM);                                                      //��ͧ��͡ ������

    $pdf->SetFont($font, '', 18);
    if ($FORMTAX_TYPE == "1") {
        $pdf->text(94, 101, "X");
    } else if ($FORMTAX_TYPE == "2") {
        $pdf->text(118, 101, "X");
    } else if ($FORMTAX_TYPE == "3") {
        $pdf->text(150, 101, "X");
    } else if ($FORMTAX_TYPE == "4") {
        $pdf->text(172, 101, "X");
    } else if ($FORMTAX_TYPE == "5") {
        $pdf->text(94, 111, "X");
    } else if ($FORMTAX_TYPE == "6") {
        $pdf->text(118, 111, "X");
    } else if ($FORMTAX_TYPE == "7") {
        $pdf->text(150, 111, "X");
    }


    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    //---------------------------------------------------- ��ͺ��ͧ���3-------------------------------------------------------

    $pdf->Rect(14, 123, 182, 72, 'D');

    $pdf->Rect(14, 140, 182, 0, 'D');
    $pdf->Rect(110, 123, 0, 52, 'D');
    $pdf->Rect(136, 123, 0, 60, 'D');
    $pdf->Rect(165, 123, 0, 60, 'D');
    $pdf->Rect(156, 140, 0, 43, 'D');
    $pdf->Rect(187, 140, 0, 43, 'D');
    $pdf->Rect(14, 175, 182, 0, 'D');
    $pdf->Rect(136, 183, 60, 0, 'D');

    $pdf->SetFont($font, '', 14);

    $pdf->text(43, 132, "�������Թ��֧�����Թ����");
    $pdf->text(116, 130, " �ѹ ��͹ ");
    $pdf->text(112, 135, " ���ͻ����� ������");
    $pdf->text(140, 132, " �ӹǹ�Թ������");
    $pdf->text(174, 130, " ���շ���ѡ");
    $pdf->text(173, 135, "��й������");
    $pdf->text(19, 150, "1. �Թ��͹ ��Ҩ�ҧ ��������§⺹�� ��� ����ҵ�� 40(1)");
    $pdf->text(19, 155, "2. ��Ҹ������� ��ҹ��˹�� ��� ����ҵ�� 40(2)");
    $pdf->text(85, 181, "����Թ������������շ���ѡ����"); //-----��ػ�Թ
    $pdf->text(20, 191, "����Թ���շ���ѡ���� (����ѡ��)");

    $pdf->text(119, 150, $TAX_YEAR);                                         //��ͧ��͡ ������

    if ($INCOME_B == "0") {
        $pdf->text(153, 150, $INCOME_B);                                          //��ͧ��͡ ������
        $pdf->text(159, 150, $INCOME_S);                                              //��ͧ��͡ ������
    } else {
        $pdf->text(141, 150, $INCOME_B);                                          //��ͧ��͡ ������
        $pdf->text(159, 150, $INCOME_S);                                              //��ͧ��͡ ������
    }
    if ($TAX_INCOME_B == "0") {
        $pdf->text(184, 150, $TAX_INCOME_B);                                      //��ͧ��͡ ������
        $pdf->text(190, 150, $TAX_INCOME_S);
    } else {
        $pdf->text(174, 150, $TAX_INCOME_B);                                      //��ͧ��͡ ������
        $pdf->text(190, 150, $TAX_INCOME_S);                                      //��ͧ��͡ ������
    }
    if ($NET_INCOME_B == "0") {
        $pdf->text(153, 180, $NET_INCOME_B);                                      //��ͧ��͡ ������
        $pdf->text(159, 180, $NET_INCOME_S);
    } else {
        $pdf->text(141, 180, $NET_INCOME_B);                                      //��ͧ��͡ ������
        $pdf->text(159, 180, $NET_INCOME_S);                                                  //��ͧ��͡ ������
    }
    if ($NETTAX_INCOME_B == "0") {
        $pdf->text(184, 180, $NETTAX_INCOME_B);                                                   //��ͧ��͡ ������
        $pdf->text(190, 180, $NETTAX_INCOME_S);                                                  //��ͧ��͡ ������
    } else {
        $pdf->text(174, 180, $NETTAX_INCOME_B);                                                   //��ͧ��͡ ������
        $pdf->text(190, 180, $NETTAX_INCOME_S);                                                  //��ͧ��͡ ������
    }
    $pdf->setFillColor(230, 230, 230);
    $pdf->setY(185);
    $pdf->setX(85);
    $pdf->Cell(110, 8, $NETTAX_CHAR . ".", 0, 1, '', 1);                                  //��ͧ��͡ ������


    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;

    //---------------------------------------------------- ��ͺ��ͧ���4-------------------------------------------------------

    $pdf->Rect(14, 198, 182, 10, 'D');
    $pdf->SetFont($font, '', 14);
    $pdf->text(20, 204, "�Թ�������������");

    $pdf->SetFont($font, '', 14);
    $pdf->Rect(65, 200, 6, 6, 'D');
    $pdf->text(75, 204, "���.");


    $pdf->text(118, 204, "�ҷ");

    $pdf->Rect(138, 200, 6, 6, 'D');
    $pdf->text(148, 204, "�ʨ.");

    $pdf->text(178, 204, "�ҷ");
    //��ͧ��͡ ������
    if ($NETSAVING_TYPE == "1") {
        $pdf->SetFont($font, '', 18);
        $pdf->text(67, 205, "X");
        $pdf->SetFont($font, '', 14);
        $pdf->text(100, 204, $NET_SAVING);
    } else if ($NETSAVING_TYPE == "2") {
        $pdf->SetFont($font, '', 18);
        $pdf->text(140, 205, "X");
        $pdf->SetFont($font, '', 14);
        $pdf->text(160, 204, $NET_SAVING);
    } else if ($NETSAVING_TYPE == "0") {
        $pdf->SetFont($font, '', 18);
        $pdf->text(140, 205, "");
    }

    //---------------------------------------------------- ��ͺ��ͧ���5-------------------------------------------------------

    $pdf->Rect(14, 211, 182, 10, 'D');
    $pdf->SetFont($font, '', 14);
    $pdf->text(20, 217, "�������Թ");


    $pdf->SetFont($font, '', 14);
    $pdf->Rect(43, 213, 6, 6, 'D');
    $pdf->text(52, 217, "(1) �ѡ � ������");

    $pdf->Rect(76, 213, 6, 6, 'D');
    $pdf->text(85, 217, "(2) �͡����ʹ�");

    $pdf->Rect(114, 213, 6, 6, 'D');
    $pdf->text(123, 217, "(3) �͡����������");

    $pdf->Rect(152, 213, 6, 6, 'D');
    $pdf->text(160, 217, "(4) ���� (�к�) .............");

    $pdf->SetFont($font, '', 18);
    //��ͧ��͡ ������
    if ($TAX_TYPE == "1") {
        $pdf->text(45, 218, "X");
    } else if ($TAX_TYPE == "2") {
        $pdf->text(78, 218, "X");
    } else if ($TAX_TYPE == "3") {
        $pdf->text(116, 218, "X");
    } else if ($TAX_TYPE == "4") {
        $pdf->text(154, 218, "X");
    }

    //---------------------------------------------------- ��ͺ��ͧ���6-------------------------------------------------------

    $pdf->SetFont($font, '', 14);
    $pdf->Rect(14, 224, 81, 42, 'D');
    $pdf->text(16, 235, "����͹  �������˹�ҷ���͡˹ѧ����Ѻ�ͧ����ѡ���� � ������");
    $pdf->text(29, 241, "��ҽ׹��軯ԺѵԵ���ҵ�� 50 ��� ��觻�����");
    $pdf->text(29, 247, "��ɮҡ�  ��ͧ�Ѻ�ɷҧ�ҭҵ���ҵ�� 35");
    $pdf->SetFont($font, '', 13);
    $pdf->text(98, 233, "���Ѻ�ͧ��Ң�ͤ�����е���Ţ�ѧ����Ǣ�ҧ�鹶١��ͧ�ç�Ѻ������ԧ�ء��С��");
    $pdf->text(140, 238, "");
    if ($PIC_SIGN_PER == "") {
        //$pdf->Image("../../attachments/t.jpg", 128, 235, 30, 10,"jpg");
    } else {
        $pdf->Image($PIC_SIGN_PER, 128, 235, 30, 10, "jpg");
    }
    $pdf->text(106, 246, "ŧ����.....................................................................................�������Թ");

    $pdf->text(124, 259, "(�ѹ ��͹ �� ����͡˹ѧ����Ѻ�ͧ �)");
    $pdf->Rect(97, 224, 99, 42, 'D');


    $pdf->text(132, 252, $TAX_DATE_A);                                    //��ͧ��͡ ������

    $page_start_x = $pdf->x;
    $page_start_y = $pdf->y;


    $pdf->close();
    $filename = 'per_taxhis.pdf'; /**/
    $pdf->Output($filename, 'D');