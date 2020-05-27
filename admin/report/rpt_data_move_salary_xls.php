<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");	

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");

	include ("rpt_data_move_salary_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC  
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
		
	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";

	$company_name = "";
	if ($BKK_FLAG==1)
		$report_title = "�ѭ����������´��õѴ�͹�ѵ���Թ��͹����Ҫ��� Ṻ���¤���� $COM_NO ŧ�ѹ��� $COM_DATE";     
	else
		$report_title = "�ѭ����������´��õѴ�͹�ѵ���Թ��͹����Ҫ��� Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";     
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0408";
	
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
		$ws_head_line1 = array("�ӴѺ","���� - ʡ��","","<**1**>�ѵ���Թ��͹���","<**1**>","<**1**>","<**1**>","<**2**>�ѵ���Թ��͹���Ѵ�͹仵�駨�������","<**2**>","<**2**>","<**2**>","","");
		$ws_head_line2 = array("���","","�дѺ","���˹�/�ѧ�Ѵ","������","�дѺ","�Ţ���","���˹�/�ѧ�Ѵ","������","�дѺ","�Ţ���","�Թ��͹","�����˵�");
		$ws_head_line3 = array("","","","","���˹�","","���˹�","","���˹�","","���˹�","","");
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TB","TB","TB","TB","TBR","TR","TR","TR","TR","TLR","TLR");
		$ws_border_line2 = array("LR","LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","LR","LR");
		$ws_border_line3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5, 30, 12, 25, 8, 12, 8, 25, 8, 12, 8, 8, 20);
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
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop ��˹��������ҧ�ͧ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
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
		if ($ws_head_line3) {
			// loop ����� head ��÷Ѵ��� 3
			$xlsRow++;
			$colseq=0;
			$pgrp="";
			for($i=0; $i < count($ws_width); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {	// �����੾�з�����͡����ʴ�
					$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			} // end for loop
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_SEQ_NO, a.CMD_SEQ, a.CMD_POS_NO, a.CMD_POS_NO_NAME
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_SEQ_NO, a.CMD_SEQ, a.CMD_POS_NO, a.CMD_POS_NO_NAME
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID(+)
						 order by 	$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE as PRE_PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.PL_CODE, a.PN_CODE, a.EP_CODE, a.CMD_AC_NO, a.CMD_ACCOUNT,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_SEQ_NO, a.CMD_SEQ, a.CMD_POS_NO, a.CMD_POS_NO_NAME
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PER_ID = $data[PER_ID];
		$PRE_PN_CODE = trim($data[PRE_PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PRE_PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];	
		$PL_CODE = trim($data[PL_CODE]);
		$PN_CODE = trim($data[PN_CODE]);
		$EP_CODE = trim($data[EP_CODE]);
		$TP_CODE = trim($data[TP_CODE]);
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$cardID = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		
		$FULL_NAME = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if(!trim($FULL_NAME)){	$FULL_NAME=" - ��ҧ - ";		}	
		
		$PER_BIRTHDATE =  show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$CMD_SEQ_NO = $data[CMD_SEQ_NO];
		$CMD_SEQ = $data[CMD_SEQ];
		if (!$CMD_SEQ_NO) $CMD_SEQ_NO = $CMD_SEQ;
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_ACC_NO = trim($data[CMD_AC_NO]);
		$CMD_ACCOUNT = trim($data[CMD_ACCOUNT]);
		$CMD_POS_NO = trim($data[CMD_POS_NO]);
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]);
		$CMD_POSITION = trim($data[CMD_POSITION]);
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
			$CMD_OT_NAME = $DEPARTMENT_NAME;
			if (strpos($CMD_ORG3,"�ѧ��Ѵ") !== false) $CMD_OT_NAME = "";
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		//***�����˵� : �óյ�.��� ����Ѻ PER_ID ������ҧ����դ���ͧ (�������� PER_ID) �ѹ����Ҩҡ query ����Ѻ PER_PERSONAL ����� ��ͧ��Ҩҡ PER_POSITION
		$POS_ID = trim($data[POS_ID]); 
		$POEM_ID = trim($data[POEM_ID]);  
		$POEMS_ID = trim($data[POEMS_ID]);		
		$POT_ID = trim($data[POT_ID]);		
		//-------------------------------------------------
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

		if($POS_ID){
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

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, c.OT_CODE, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_POS_NO = trim($data2[POS_NO]);
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_OT_CODE = trim($data2[OT_CODE]);
			$NEW_OT_NAME = "";
			if ($NEW_OT_CODE=="01") $NEW_OT_NAME = $DEPARTMENT_NAME;
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
		$arr_content[$data_count][order] = $CMD_SEQ_NO;
		$arr_content[$data_count][name] = $FULL_NAME;		
		$arr_content[$data_count][position] = $PL_NAME;	//��. 
		$arr_content[$data_count][cmd_acc_no] = $CMD_ACC_NO;
		$arr_content[$data_count][cmd_account] = $CMD_ACCOUNT;
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_position_type] = $CMD_POSITION_TYPE;
		$arr_content[$data_count][cmd_level_name] = $CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_position_type] = $NEW_POSITION_TYPE;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = (($NUMBER_DISPLAY==2)?convert2thaidigit($cardID):$cardID);
		if ($print_order_by==1) {
			if ($CMD_ORG5 || $NEW_ORG_NAME_2) {
				$arr_content[$data_count][cmd_position] = $CMD_ORG5;
				$arr_content[$data_count][new_position] = $NEW_ORG_NAME_2;

				$data_count++; 
			}
			if ($CMD_ORG4 || $NEW_ORG_NAME_1) {
				if ($CMD_ORG5 || $NEW_ORG_NAME_2) $arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][cmd_position] = $CMD_ORG4;
				$arr_content[$data_count][new_position] = $NEW_ORG_NAME_1;

				$data_count++; 
			}
			if ($CMD_ORG3 || $NEW_ORG_NAME) {
				if ($CMD_ORG4 || $NEW_ORG_NAME_1) $arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][cmd_position] = $CMD_ORG3;
				$arr_content[$data_count][new_position] = $NEW_ORG_NAME;

				$data_count++; 
			}
			if ($CMD_OT_NAME || $NEW_OT_NAME) {
				$arr_content[$data_count][type] = "CONTENT";
				$arr_content[$data_count][cmd_position] = $CMD_OT_NAME;
				$arr_content[$data_count][new_position] = $NEW_OT_NAME;

				$data_count++; 
			}
		}
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if

		$arr_content[$data_count][type] = "CONTENT";

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
				$wsdata_fontfmt_1[] = "B";
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
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$POSITION = $arr_content[$data_count][position];
			$CMD_ACC_NO = $arr_content[$data_count][cmd_acc_no];
			$CMD_ACCOUNT = $arr_content[$data_count][cmd_account];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_POSITION_TYPE = $arr_content[$data_count][cmd_position_type];
			$CMD_LEVEL_NAME=$arr_content[$data_count][cmd_level_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POSITION_TYPE = $arr_content[$data_count][new_position_type];
			$NEW_LEVEL_NAME=$arr_content[$data_count][new_level_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="CONTENT"){
				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";
					$arr_data[] = "<**1**>�����˵� : $CMD_NOTE2";

					$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
						
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
		//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
		//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							$colseq++;
						}
					}
				}else{
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_POSITION_TYPE;
					$arr_data[] = $CMD_LEVEL_NAME;
					$arr_data[] = $CMD_POS_NO_NAME.' '.$CMD_POS_NO;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_POSITION_TYPE;
					$arr_data[] = $NEW_LEVEL_NAME;
					$arr_data[] = $NEW_POS_NO;
					$arr_data[] = $CMD_SALARY;
					$arr_data[] = $CMD_NOTE1;
		 
					$wsdata_align = array("C","L","C","C","L","L","L","C","L","R","C","C","L");

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
						
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
		//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
							$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
		//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							$colseq++;
						}
					}
				} // end if
			}else if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				$arr_data = (array) null;
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**1**>";
				$arr_data[] = "<**2**>$ORG_NAME";
				$arr_data[] = "<**2**>$ORG_NAME";
				$arr_data[] = "<**2**>$ORG_NAME";
				$arr_data[] = "<**2**>$ORG_NAME";
				$arr_data[] = "<**3**>$NEW_ORG_NAME";
				$arr_data[] = "<**3**>$NEW_ORG_NAME";
				$arr_data[] = "<**3**>$NEW_ORG_NAME";
				$arr_data[] = "<**3**>$NEW_ORG_NAME";
				$arr_data[] = "";
				$arr_data[] = "";

				$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");

				if($CONTENT_TYPE=="ORG")
					$wsdata_fontfmt = $wsdata_fontfmt_1;	// bold
				else
					$wsdata_fontfmt = $wsdata_fontfmt_2;	// normal

				if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
	//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
	//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			} // end else if
		} // end for				
		
		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";
			$arr_data[] = "<**1**>�����˵� : $CMD_NOTE";

			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
			
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
//					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "", "L", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "", "L", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"�ѭ��Ṻ���¤��������.xls\"");
	header("Content-Disposition: inline; filename=\"�ѭ��Ṻ���¤��������.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>