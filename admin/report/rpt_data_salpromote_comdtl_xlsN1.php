<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_LEVEL_SALP, b.COM_DESC 
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
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
		
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = trim($data[COM_DESC]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$type_name="����Ҫ���";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$type_name="�١��ҧ��Ш�";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$type_name="��ѡ�ҹ�Ҫ���";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$type_name="�١��ҧ���Ǥ���";
	} // end if

	$company_name = "";
	$report_title = "�ѭ����������´�������͹����Թ��͹$type_name||Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";
	$report_code = "";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = &new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header(){
		global $worksheet, $xlsRow, $COM_TYPE, $COM_LEVEL_SALP;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 50);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15);
		if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
			$worksheet->set_column(9, 9, 50);
		} else {
			$worksheet->set_column(9, 9, 15);
			$worksheet->set_column(10, 10, 10);
			$worksheet->set_column(11, 11, 10);
			$worksheet->set_column(12, 12, 50);
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "�Ţ��Шӵ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "������", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "���˹������ǹ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
			$worksheet->write($xlsRow, 7, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "�Թ�ͺ᷹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		} else  {
			$worksheet->write($xlsRow, 7, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "������Ѻ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "�ӹǹ�Թ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			if ($COM_TYPE=="1005")
				$worksheet->write($xlsRow, 10, "�����繵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			else
				$worksheet->write($xlsRow, 10, "�ӹǹ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "�ҹ㹡��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "��ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "�ѧ�Ѵ/���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
			$worksheet->write($xlsRow, 7, "������", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "�����", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		} else  {
			$worksheet->write($xlsRow, 7, "��͹����͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 9, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			if ($COM_TYPE=="1005")
				$worksheet->write($xlsRow, 10, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			else
				$worksheet->write($xlsRow, 10, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 11, "�ӹǳ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT
				   from		(
									(
										(
											(
												PER_COMDTL a  
												inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
											) left join $position_table c on ($position_join)
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
									) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
								) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				   where			a.COM_ID=$COM_ID
				   order by 		d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, IIf(IsNull(a.CMD_POSITION), 0, CLng(LEFT(a.CMD_POSITION, (InStr(a.CMD_POSITION, '\|') - 1)))) ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT
				   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f
				   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+)
				order by 		nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)), TO_NUMBER(SUBSTR(replace(a.CMD_POSITION,'-',''), 1, (INSTR(a.CMD_POSITION, '\|') - 1))) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT
				   from		(
									(
										(
											(
												PER_COMDTL a 
												inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
											) left join $position_table c on ($position_join)
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
									) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
								) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, left(cmd_position,instr(cmd_position,'|'))+0 ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
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
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $data[CMD_POSITION]);
		}else{
			$arr_temp = explode("\|", $data[CMD_POSITION]);
		}
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
		$CMD_POSITION_M = $arr_temp[2];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DIFF = $CMD_SALARY - $CMD_OLD_SALARY;
		$CMD_SPSALARY = $data[CMD_SPSALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_PERCENT = $data[CMD_PERCENT];
		$CMD_STEP = "";
		if ($CMD_NOTE1 == "��� 7 �� �.�.��Ҵ��¡������͹����Թ��͹ �.�.2544")
			$CMD_STEP = "0.5 ���";
		elseif ($CMD_NOTE1 == "��� 8 �� �.�.��Ҵ��¡������͹����Թ��͹ �.�.2544" || 
			$CMD_NOTE1 == "��� 9 ����º��з�ǧ��ä�ѧ��Ҵ��¡������͹����Թ��͹ �.�.2544")
			$CMD_STEP = "1 ���";
		elseif ($CMD_NOTE1 == "��� 14 �� �.�.��Ҵ��¡������͹����Թ��͹ �.�.2544")
			$CMD_STEP = "1.5 ���";
		if ($CMD_PERCENT > 0) $CMD_STEP = $CMD_PERCENT;

		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$arr_temp = explode(" ", $data2[LEVEL_NAME]);
		$TYPE_NAME = substr($arr_temp[0],6);
		$LEVEL_NAME = $arr_temp[1];
		$arr_temp = explode("�дѺ", $LEVEL_NAME);
		$LEVEL_NAME = $arr_temp[1];
		if($PER_TYPE==1){
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION . (($CMD_PT_CODE != "11" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$cmd = " select LAYER_TYPE from PER_LINE where PL_CODE = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;
	
			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if ($LAYER_TYPE==2) {
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

			if($SALARY_POINT_MID > $TMP_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION):"";
		} elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
		} // end if
		if($CMD_ORG3 != trim($data[CMD_ORG3])){
			$CMD_ORG3 = trim($data[CMD_ORG3]);

			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $CMD_ORG3;
//			$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

			$data_count++;
		} // end if
		
		if($CMD_ORG4 != trim($data[CMD_ORG4])){
			$CMD_ORG4 = trim($data[CMD_ORG4]);

			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $CMD_ORG4;

			$data_count++;
		} // end if

		if($CMD_ORG5 != trim($data[CMD_ORG5])){
			$CMD_ORG5 = trim($data[CMD_ORG5]);

			$arr_content[$data_count][type] = "ORG_2";
			$arr_content[$data_count][org_name] = $CMD_ORG5;

			$data_count++;
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = trim($CMD_POSITION_M)?$CMD_POSITION_M:$CMD_POSITION;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		$arr_content[$data_count][cmd_spsalary] = $CMD_SALARY?number_format($CMD_SPSALARY):"-";
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF?number_format($CMD_DIFF):"-";
		
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_step] = $CMD_STEP;
		
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][cmd_midpoint] = $TMP_MIDPOINT?number_format($TMP_MIDPOINT):"-";
		if(trim($CMD_POSITION_M)) $arr_content[$data_count][cmd_position] = "($CMD_POSITION)";
//		$arr_content[$data_count][cmd_position] = $CMD_ORG3;
//		$arr_content[$data_count][new_position] = $NEW_ORG_NAME;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE2;

		$data_count++;
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		} // end if
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} else {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			} else {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			}
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$CARDNO = $arr_content[$data_count][cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
//			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SPSALARY = $arr_content[$data_count][cmd_spsalary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_MIDPOINT = $arr_content[$data_count][cmd_midpoint];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if($CONTENT_TYPE=="ORG"){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "$ORG_NAME ", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					} else {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					}
				}else{
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					} else {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					}
				} // end if
			}elseif($CONTENT_TYPE=="CONTENT"){
				if($CMD_NOTE2){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "�����˵� : $CMD_NOTE2", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					} else {
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					}
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 2, "$CARDNO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 3, "$TYPE_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 4, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 5, "$CMD_POS_NO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 6, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
						$worksheet->write_string($xlsRow, 7, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 8, "$CMD_SPSALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 9, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					} else {
						$worksheet->write_string($xlsRow, 7, "$CMD_OLD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 8, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 9, "$CMD_DIFF", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 10, "$CMD_STEP", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 11, "$CMD_MIDPOINT", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 12, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					}
				} // end if
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 1, "�����˵� : $COM_NOTE", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			} else {
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
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
		if ($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) {
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} else {
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"�ѭ��Ṻ���¤��������͹����Թ��͹.xls\"");
	header("Content-Disposition: inline; filename=\"�ѭ��Ṻ���¤��������͹����Թ��͹.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>