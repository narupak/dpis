<?php

	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
    include("../php_scripts/function_share.php");
    include("../report/rpt_function.php");
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	ini_set("max_execution_time", 0);
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
    $report_title = "ORG_STRUCTURE";
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
        $worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

    $heading_width[] = "10"; //���;
    $heading_width[] = "15"; //������ǹ�Ҫ���;
    $heading_width[] = "40"; //������ǹ�Ҫ���;
    $heading_width[] = "30"; //���������ǹ�Ҫ���
    $heading_width[] = "18"; //���ʰҹТͧ˹��§ҹ;
    $heading_width[] = "20"; //���ͰҹТͧ˹��§ҹ;
    $heading_width[] = "10"; //���ʻ����;
    $heading_width[] = "20";//���ͻ����;
    $heading_width[] = "10"; //���ʨѧ��Ѵ;
    $heading_width[] = "20"; //���ͨѧ��Ѵ;
    $heading_width[] = "10"; //���������;
    $heading_width[] = "20"; //���������;
    $heading_width[] = "17"; //����˹��§ҹ����ѧ�Ѵ;
    $heading_width[] = "40"; //����˹��§ҹ����ѧ�Ѵ;
    
   // $heading_width[] = "30";

   // $ws_width = array(10,25,25,20,25,30,30,30,30,25,25,25,25,25,25,25,25,25,25);
        
    $heading_text[] = "���";
    $heading_text[] = "������ǹ�Ҫ���";
    $heading_text[] = "������ǹ�Ҫ���";
    $heading_text[] = "���������ǹ�Ҫ���";
    $heading_text[] = "���ʰҹТͧ˹��§ҹ";
    $heading_text[] = "���ͰҹТͧ˹��§ҹ";
    $heading_text[] = "���ʻ����";
    $heading_text[] = "���ͻ����";
    $heading_text[] = "���ʨѧ��Ѵ";
    $heading_text[] = "���ͨѧ��Ѵ";
    $heading_text[] = "���������";
    $heading_text[] = "���������";
    $heading_text[] = "����˹��§ҹ����ѧ�Ѵ";
    $heading_text[] = "����˹��§ҹ����ѧ�Ѵ";

       
        $arr_column_map = (array) null;
        $arr_column_sel = (array) null;
        for($i=0; $i < count($heading_text); $i++) {
                $arr_column_map[] = $i;		// link index �ͧ head 
                $arr_column_sel[] = 1;			// 1=�ʴ�	0=����ʴ�   �������ա�û�Ѻ����§ҹ �����ʴ����
        }
        $arr_column_width = $heading_width;	// �������ҧ
        $arr_column_align = $data_align;		// align
        $COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
        

        $ws_head_line1 = array("���","������ǹ�Ҫ���", "������ǹ�Ҫ���", "���������ǹ�Ҫ���", "���ʰҹТͧ˹��§ҹ", "���ͰҹТͧ˹��§ҹ","���ʻ����","���ͻ����","���ʨѧ��Ѵ","���ͨѧ��Ѵ","���������","���������","����˹��§ҹ����ѧ�Ѵ","����˹��§ҹ����ѧ�Ѵ");
        $ws_head_line2 = array("","","", "", "", "", "", "", "", "", "", "", "", "");
        $ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $ws_border_line1 = array("","","","","","","","","","","","","","");
        $ws_border_line2 = array("","","","","","","","","","","","","","");
        $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
        $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
        $ws_width = array(10,15,40,30,18,20,10,20,10,20,10,20,17,40);

        $worksheet->write(0, 8, "�ç���ҧ˹��§ҹ", set_format("xlsFmtTitle", "B", "L", "", 1));
        $worksheet->write(1, 8, "$search_org", set_format("xlsFmtTitle", "B", "L", "", 10));
		//$worksheet->write(1, 1, "��ª��͢���Ҫ��� ���˹� �ѧ�Ѵ", set_format("xlsFmtTitle", "B", "L", "", 1));
        
        $sum_hdw = 0;
        $sum_wsw = 0;
        $xlsRow=2;
        
        for($h = 0; $h < count($heading_width); $h++) {
            $sum_wsw += $ws_width[$h];	// ws_width �ѧ����� �ǡ �������ҧ ��Ƿ��١�Ѵ����
            if ($arr_column_sel[$h]==1) {
                $sum_hdw += $heading_width[$h];
            }
        }
        // �ѭ�ѵ����ҧ��   �ʹ����������ҧ column � heading_width ��º�Ѻ �ʹ���� ws_width
        // ���� column � ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
        for($h = 0; $h < count($heading_width); $h++) {
            if ($arr_column_sel[$h]==1) {
                $ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
            }
        }
        // loop ��˹��������ҧ�ͧ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
        
        $colshow_cnt = $colseq = 0;
        $pgrp="";
        for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
                        $buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
                        $ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
                        $worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
                        $colseq++;
                }
        }
        // loop ����� head ��÷Ѵ��� 2
        $xlsRow++;
        $colseq=0;
        for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// �����੾�з�����͡����ʴ�
                        $worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableDetail", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
                        $colseq++;
                }
        }
    $xlsRow++;
    // function get_org(){
    //     global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
    //     global $AUTH_CHILD_ORG, $ORGTAB;
        
    // }
  
    $cmd=" SELECT * from PER_ORG START WITH ORG_ID = $ORG_ID1
    CONNECT BY NOCYCLE PRIOR trim(ORG_ID) = trim(ORG_ID_REF)";
    if($BYASS =='Y'){$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);}

    $count_page_data = $db_dpis->send_cmd($cmd);
	$num_order = 0;
	while ($data = $db_dpis->get_array()) {
        $ORG_CODE = " ".$data[ORG_CODE];
        $ORG_NAME = $data[ORG_NAME];
        $ORG_SHORT = $data[ORG_SHORT];
        
        $cmd=" select OL_NAME from PER_ORG_LEVEL where OL_CODE = $data[OL_CODE]"; 
         $db_dpis1->send_cmd($cmd);
         $data1 = $db_dpis1->get_array(); 
        $OL_NAME = trim($data1[OL_NAME]);

        $cmd=" select CT_NAME from PER_COUNTRY where CT_CODE = $data[CT_CODE]"; 
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array(); 
        $CT_NAME = trim($data1[CT_NAME]);

        $cmd=" select PV_NAME from PER_PROVINCE where PV_CODE = $data[PV_CODE]"; 
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array(); 
        $PV_NAME = trim($data1[PV_NAME]);

        $cmd=" select AP_NAME from PER_AMPHUR where AP_CODE = $data[AP_CODE]"; 
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array(); 
        $AP_NAME = trim($data1[AP_NAME]);

        $cmd=" select ORG_NAME from PER_ORG where ORG_ID = $data[ORG_ID_REF]";
        if($BYASS =='Y'){$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);}
        $db_dpis1->send_cmd($cmd);
        $data1 = $db_dpis1->get_array(); 
        $ORG_ID_REF_NAME = trim($data1[ORG_NAME]);

        
        $num_order++;
        if($count_page_data){
            $worksheet->write($xlsRow, 0,  $num_order, set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 1,  $ORG_CODE, set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 2,  $ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 3,  $ORG_SHORT, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 4,  $data[OL_CODE], set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 5,  $OL_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 6,  $data[CT_CODE], set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 7,  $CT_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 8,  $data[PV_CODE], set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 9,  $PV_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 10,  $data[AP_CODE], set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 11,  $AP_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
            $worksheet->write($xlsRow, 12,  $data[ORG_ID_REF], set_format("xlsFmtTableDetail", "", "C", "", 0));
            $worksheet->write($xlsRow, 13,  $ORG_ID_REF_NAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
        
    $xlsRow++;
    $search_org_name_1="";
    $search_org_name="";
    $DEPARTMENT_NAME="";
    $MINISTRY_NAME="";
  }//end chk data  
}//end while

$worksheet = &$workbook->addworksheet("COUNT_ORG_STRUCTURE");
$worksheet->set_margin_right(0.50);
$worksheet->set_margin_bottom(1.10);

//====================== SET FORMAT ======================//
require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
//====================== SET FORMAT ======================//

    $worksheet->set_column(0, 2, 10);
    $worksheet->set_column(0, 3, 10);
    $worksheet->set_column(1, 2, 10);
    $worksheet->set_column(1, 3, 10);
    $worksheet->set_column(2, 2, 30);
    $worksheet->set_column(2, 3, 30);
    // $worksheet->set_column(3, 2, 20);
    // $worksheet->set_column(3, 3, 20);
    // $worksheet->set_column(4, 2, 20);
    // $worksheet->set_column(4, 3, 20);
    // $worksheet->set_column(5, 2, 20);
    // $worksheet->set_column(5, 3, 20);
    // $worksheet->set_column(6, 2, 30);
    // $worksheet->set_column(6, 3, 20);
    // $worksheet->set_column(7, 2, 30);
    // $worksheet->set_column(7, 3, 30);
    // $worksheet->set_column(8, 2, 30);
    // $worksheet->set_column(8, 3, 30);
    // $worksheet->set_column(9, 2, 30);
    // $worksheet->set_column(9, 3, 30);
    // $worksheet->set_column(10, 2, 30);
    // $worksheet->set_column(10, 3, 30);
    // $worksheet->set_column(11, 2, 30);
    // $worksheet->set_column(11, 3, 30);
    // $worksheet->set_column(12, 2, 30);
    // $worksheet->set_column(12, 3, 30);
    // $worksheet->set_column(13, 2, 30);
    // $worksheet->set_column(13, 3, 30);
    

$worksheet->write(0, 2, "", set_format("xlsFmtTableDetail", "B", "R", "", 0));
$worksheet->write(0, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
$worksheet->write(1, 2, "", set_format("xlsFmtTableDetail", "B", "R", "", 0));
$worksheet->write(1, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
$worksheet->write(2, 2, "", set_format("xlsFmtTableDetail", "B", "R", "", 0));
$worksheet->write(2, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));        
$worksheet->write(3, 2, "", set_format("xlsFmtTableDetail", "B", "R", "", 0));
$worksheet->write(3, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
$worksheet->write(4, 2, "", set_format("xlsFmtTableDetail", "B", "R", "", 0));
$worksheet->write(4, 3, "", set_format("xlsFmtTableDetail", "B", "C", "", 0));
$worksheet->write(5, 2, "˹��§ҹ", set_format("xlsFmtTableDetail", "B", "C", "", 0));
$worksheet->write(5, 3, "�ӹǹ", set_format("xlsFmtTableDetail", "B", "C", "", 0));  
$cmd = "SELECT count(po.OL_CODE) as cnt_org, pl.OL_NAME from PER_ORG po
LEFT JOIN  per_org_level pl on (pl.OL_CODE = po.OL_CODE )
group by po.OL_CODE, pl.OL_NAME 
START WITH po.ORG_ID = $ORG_ID1
CONNECT BY NOCYCLE PRIOR trim(po.ORG_ID) = trim(po.ORG_ID_REF)
ORDER BY po.OL_CODE";
if($BYASS =='Y'){$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd);}
$db_dpis2->send_cmd($cmd);
// echo "<pre>";
// die($cmd);
$xlsRow2=6;
while ($data2 = $db_dpis2->get_array()) {
    $worksheet->write($xlsRow2, 2, $data2[OL_NAME], set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
    $worksheet->write($xlsRow2, 3,  $data2[CNT_ORG], set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
    $xlsRow2++;
}
// $worksheet->write(6, 2, "��з�ǧ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
// $worksheet->write(6, 3, "5", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
// $worksheet->write(7, 2, "���", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
// $worksheet->write(7, 3, "10", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
// $worksheet->write(8, 2, "�ӹѡ/�ͧ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
// $worksheet->write(8, 3, "20", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
// $worksheet->write(9, 2, "��ӡ����ӹѡ/�ͧ 1 �дѺ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
// $worksheet->write(9, 3, "15", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));	
// $worksheet->write(10, 2, "��ӡ����ӹѡ/�ͧ 2 �дѺ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));
// $worksheet->write(10, 3, "15", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));		
// $worksheet->write(11, 2, "��ӡ����ӹѡ/�ͧ 3 �дѺ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));	
// $worksheet->write(11, 3, "15", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
// $worksheet->write(12, 2, "��ӡ����ӹѡ/�ͧ 4 �дѺ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));	
// $worksheet->write(12, 3, "15", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
// $worksheet->write(13, 2, "��ӡ����ӹѡ/�ͧ 5 �дѺ", set_format("xlsFmtTableDetail", "B", "R", "TLRB", 0));	
// $worksheet->write(13, 3, "15", set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));

    $workbook->close();
	ini_set("max_execution_time", 30);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
