<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");

	include ("rpt_data_assign_comdtlN_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

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
	$COM_DESC = str_replace("�����", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	if ($BKK_FLAG==1) {
		$arr_temp = explode("���", $COM_NO);
		if ($arr_temp[0]=="���. ") $DEPARTMENT_NAME = "��ا෾��ҹ��";
		$report_title = "�ѭ����������´���$COM_NAME Ṻ���¤����$DEPARTMENT_NAME ��� $arr_temp[1] ŧ�ѹ��� $COM_DATE";
	} else {
		if ($MFA_FLAG==1 && $TMP_DEPARTMENT_ID == "") {
			$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_NAME Ṻ���¤����$MINISTRY_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
		} else {
			if ($RID_FLAG==1)
				$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_DESC Ṻ���¤����$DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
			else
				$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_NAME Ṻ���¤����$DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
		}
	}
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P1502";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// ��˹���ҵ�������;���� ��ǹ�����§ҹ
		if (!$CARDNO_FLAG==1) $CARDNO_TITLE = "";
		$ws_width = array(5,15,20,8,10,8,20,10,20);
		$ws_head_line1 = array("�ӴѺ",$FULLNAME_HEAD,$COM_HEAD_01,"<**1**>","<**1**>","<**1**>","�ͺ������黯Ժѵ��Ҫ���","������ѹ��� - ","�����˵�");
		$ws_head_line2 = array("���","$CARDNO_TITLE","���˹�/�ѧ�Ѵ","���˹觻�����","�дѺ���˹�","�Ţ�����˹�","","�֧�ѹ���","");
		$ws_colmerge_line1 = array(0,0,1,1,1,1,0,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TBR","TLR","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","TLBR","LBR","LBR","LBR");
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C");
	// ����á�˹���ҵ�������;���� ��ǹ�����§ҹ	

	// �ӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width �ѧ����� �ǡ �������ҧ ��Ƿ��١�Ѵ����
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// �ѭ�ѵ����ҧ��   �ʹ����������ҧ column � heading_width ��º�Ѻ �ʹ���� ws_width
		//                                ���� column � ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// �������º��Ҥӹǹ���º��º��� $ws_width ���� ��º�Ѻ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop ��˹��������ҧ�ͧ column
		$colshow_cnt = 0;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
				$colshow_cnt++;
			}
		}
		// loop ����� head ��÷Ѵ��� 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop ����� head ��÷Ѵ��� 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER, a.CMD_POS_NO_NAME, a.CMD_POS_NO,
                                                                                        a.CMD_DATE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER, a.CMD_POS_NO_NAME, a.CMD_POS_NO,
                                                                                        a.CMD_DATE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, b.PER_MEMBER, a.CMD_POS_NO_NAME, a.CMD_POS_NO,
                                                                                        a.CMD_DATE2
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	a.CMD_SEQ ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
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
		if ($CARDNO_FLAG==1){ // ������͡���Ţ�ѵ���ѧ����
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		}
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_MEMBER = $data[PER_MEMBER];

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY)." - ".show_date_format($data[CMD_DATE2],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		if($PER_TYPE==1){
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
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} // end if
		
		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) && trim($data[CMD_ORG3]) != "-"){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) && trim($data[CMD_ORG4]) != "-"){
				$CMD_ORG4 = trim($data[CMD_ORG4]);

				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;

				$data_count++;
			} // end if
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
//		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n". $PER_CARDNO;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($CARDNO_FLAG==1) $arr_content[$data_count][name] .= "\n(".(($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID).")"; //������͡���Ţ�ѵ���ѧ����
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($print_order_by==1 || $COMMAND_PRT==1) {
			if ($DEPARTMENT_NAME=="�����û���ͧ") {
				if ($CMD_OT_CODE=="03") {
					if (!$CMD_ORG5 && !$CMD_ORG4) {
						$arr_content[$data_count][cmd_position] .= "\n���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
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
				if ($CMD_ORG5 && $SESS_DEPARTMENT_NAME!="����Ż�зҹ") $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG5);
				if ($CMD_ORG4) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG4);
				if ($CMD_ORG3) $arr_content[$data_count][cmd_position] .= "\n".trim($CMD_ORG3);
			}
		} 
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// �Ҩӹǹ column ����ʴ���ԧ
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
		
		// ��˹���ҵ�������;���� ��ǹ������
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
		// ����˹���ҵ�������;���� ��ǹ������

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME = $arr_content[$data_count][cmd_level_name];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME;
			$arr_data[] = $CMD_POSITION;
			$arr_data[] = $CMD_POSITION_TYPE;
			$arr_data[] = $CMD_LEVEL_NAME;
			$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
			$arr_data[] = $CMD_NOTE1;
			$arr_data[] = $CMD_DATE;
			$arr_data[] = $CMD_NOTE2;

			$wsdata_align = array( "C","L", "L","L", "L", "C", "L", "C", "L");

			if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
					$colseq++;
				}
			}
		} // end for				
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";

			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L");

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
		} // end if
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ����բ����� *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"�ѭ����������´����ͺ���§ҹ/��Ժѵ��Ҫ���᷹.xls\"");
	header("Content-Disposition: inline; filename=\"�ѭ����������´����ͺ���§ҹ/��Ժѵ��Ҫ���᷹.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>