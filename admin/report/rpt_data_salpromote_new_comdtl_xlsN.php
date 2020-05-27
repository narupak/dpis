<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, COM_LEVEL_SALP, a.DEPARTMENT_ID, b.COM_DESC, b.COM_NAME as COM_TYPE_NAME
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd<br>";
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
	$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
	
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_DESC = str_replace("�����", "", $data[COM_DESC]);
	$COM_TYPE_NAME = trim($data[COM_TYPE_NAME]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	$cmd = " select	CMD_DATE from	PER_COMDTL where	COM_ID=$COM_ID ";
	$db_dpis->send_cmd($cmd);
//	echo "cmd=$cmd<br>";
	$data = $db_dpis->get_array();
	$CMD_DATE = $data[CMD_DATE];
	if (substr($CMD_DATE,4,6) == "-04-01") {
		$search_kf_cycle = 2;
		$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
		$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	} elseif (substr($CMD_DATE,4,6) == "-10-01") {
		$search_kf_cycle = 1;
		$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
		if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
		elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
	}

	if($DPISDB=="odbc") $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, CMD_POS_NO_NAME, CLng(CMD_POS_NO)";
	elseif($DPISDB=="oci8")  $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, nvl(e.ORG_CODE,d.ORG_CODE), f.ORG_SEQ_NO, nvl(f.ORG_CODE, nvl(e.ORG_CODE,d.ORG_CODE)), CMD_POS_NO_NAME, to_number(replace(CMD_POS_NO,'-',''))";
	elseif($DPISDB=="mysql")  $order_str = "g.ORG_SEQ_NO, g.ORG_CODE, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO , CMD_POS_NO_NAME, CMD_POS_NO+0";

	include ("rpt_data_salpromote_new_comdtl_xlsN_format.php");	// ��˹������е��������Ѻ�������¹�ٻẺ��§ҹ

	$company_name = "";
	$SHOW_START_DATE = show_date_format($KF_START_DATE,3);
	if ($BKK_FLAG==1)
		$report_title = "�ѭ����������´��û�Ѻ�ѵ���Թ��͹$PERSON_TYPE[$COM_PER_TYPE]||Ṻ���¤���� $COM_NO ŧ�ѹ��� $COM_DATE";   
	else
		$report_title = "$COM_TYPE_NAME||�ѭ����������´���$COM_NAME||Ṻ���¤���� $DEPARTMENT_NAME ��� $COM_NO ŧ�ѹ��� $COM_DATE";   
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "P0406";
	
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
	$ws_width = array(6,30,15,25,25,15,13,8,10,10,10,20);
	$ws_head_line1 = array("�ӴѺ",$FULLNAME_HEAD,"�Ţ��Шӵ��",$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,$COM_HEAD_01,"�Թ��͹","��ҵͺ᷹","������Ѻ","�����˵�");
	$ws_head_line2 = array("���","","��ЪҪ�","�ѧ�Ѵ","���˹�","���˹觻�����","�дѺ���˹�","�Ţ���","","�����","�Թ��͹","");
	$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,0,0,0,0);
	$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TB","TB","TLR","TLR","TLR","TLR");
	$ws_border_line2 = array("LBR","LBR","LBR","LBR","TLBR","TLBR","TLBR","TLBR","LBR","LBR","LBR","LBR");
	$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1);
	$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1);
	$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0);
	$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
	$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C");
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
		global $worksheet, $xlsRow, $COM_TYPE, $COM_LEVEL_SALP, $DEPARTMENT_NAME;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_border_line1, $ws_border_line2, $ws_border_line3;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_wraptext_line3, $ws_rotate_line1, $ws_rotate_line2, $ws_rotate_line3, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop ��˹��������ҧ�ͧ column
		$colseq=0;
//		echo "count=".count($ws_width)."<br>";
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
					$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			} // end for loop
		}
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a  
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where			a.COM_ID=$COM_ID
				   order by 		$order_str ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f, PER_ORG g
				   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and 
										c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+) and b.DEPARTMENT_ID=g.ORG_ID
				order by 		$order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO, a.MOV_CODE
				   from		(
									(
										(
											(
												(
													PER_COMDTL a 
													inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
												) left join $position_table c on ($position_join)
											) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
										) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
									) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
								) left join PER_ORG g on (b.DEPARTMENT_ID=g.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	$order_str ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd; 
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
	
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_ORG4 = $data[CMD_ORG4];
		$CMD_ORG5 = $data[CMD_ORG5];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DIFF = $CMD_SALARY - $CMD_OLD_SALARY;
		$CMD_SPSALARY = $data[CMD_SPSALARY];
		$CMD_DATE = trim($data[CMD_DATE]);
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

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME =  $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];

		$cmd = " 	select SAH_SALARY_EXTRA from PER_SALARYHIS 
						where PER_ID=$PER_ID and SAH_EFFECTIVEDATE='2014-10-01'
						order by SAH_SALARY_EXTRA desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SAH_SALARY_EXTRA = $data2[SAH_SALARY_EXTRA] + 0;

		$TMP_SALARY_EXTRA = $CMD_SALARY4 = "";
		if ($CMD_NOTE1) {
			$EXTRA_FLAG = 1;
			$arr_temp = explode("�Թ�ͺ᷹����� ", $CMD_NOTE1);
			if ($arr_temp[1] > "0.00") $SAH_SALARY_EXTRA = str_replace(",","",$arr_temp[1])+0;
			$TMP_SALARY_EXTRA = (ceil($SAH_SALARY_EXTRA/10))*10 ;
		}
		$CMD_SALARY_EXTRA = $CMD_OLD_SALARY  + $TMP_SALARY_EXTRA;
		if ($CMD_LEVEL=="O1" || $CMD_LEVEL=="O2" || $CMD_LEVEL=="K1" || $CMD_LEVEL=="K2") {
			$CMD_SALARY4 = (ceil($CMD_SALARY_EXTRA * (4/100)  /10))*10;
		}

		if (substr($CMD_DATE,4,6) == "-04-01") {
			$search_kf_cycle = 1;
			$KF_START_DATE = (substr($CMD_DATE,0,4)-1) . "-10-01";
			if($COM_PER_TYPE == 1) $KF_END_DATE = substr($CMD_DATE,0,4) . "-03-31";
			elseif($COM_PER_TYPE == 3) $KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		} elseif (substr($CMD_DATE,4,6) == "-10-01") {
			$search_kf_cycle = 2;
			$KF_START_DATE = substr($CMD_DATE,0,4) . "-04-01";
			$KF_END_DATE = substr($CMD_DATE,0,4) . "-09-30";
		}

		if ($BKK_FLAG==1) {
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = "select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2= $db_dpis2->get_array();
			$MOV_NAME =  trim($data2[MOV_NAME]);
			if ($MOV_NAME=="����͹��� 0.5 ���" && $search_kf_cycle == 1) $CMD_NOTE1 = "���觢��";
			elseif ($MOV_NAME=="����͹��� 1 ���" && $search_kf_cycle == 2) $CMD_NOTE1 = "˹�觢��";
			elseif ($MOV_NAME=="����͹��� 1.5 ���") $CMD_NOTE1 = "˹�觢�鹤���";
			elseif ($MOV_NAME=="����͹��� 2 ���") $CMD_NOTE1 = "�ͧ���";
			elseif (substr($MOV_NAME,0,12)=="���������͹") $CMD_NOTE1 = $MOV_NAME;
		}

		//�����š���֡��
		$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EDU_ENDDATE, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and  EDU_TYPE like '%2%' 
								and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		$db_dpis2->send_cmd($cmd);
//		echo "->".$cmd;
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
		$EDU_ENDDATE = show_date_format($data2[EDU_ENDDATE],$CMD_DATE_DISPLAY);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"���õԹ���") !== true) $EDU_HONOR = "���õԹ���" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="��") $CT_NAME = "";
		
		if($PER_TYPE==1){
			$cmd = " select TOTAL_SCORE, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $PER_ID and KF_CYCLE = $search_kf_cycle and 
							KF_START_DATE = '$KF_START_DATE'  and  KF_END_DATE = '$KF_END_DATE'  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
//			echo $cmd;
			$TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 		
			$TOTAL_SCORE = $TOTAL_SCORE?number_format($TOTAL_SCORE,2):"-";
			if (!$CMD_NOTE1) $CMD_NOTE1 = trim($data2[SALARY_REMARK1]);
		}elseif($PER_TYPE==3){
			$TOTAL_SCORE = "";
			$cmd = " select TOTAL_SCORE from PER_KPI_FORM where PER_ID = $PER_ID and 
							KF_START_DATE >= '$KF_START_DATE'  and  KF_END_DATE <= '$KF_END_DATE'
							order by KF_CYCLE ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$TEMP_TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 			 
				$TEMP_TOTAL_SCORE = $TEMP_TOTAL_SCORE?number_format($TEMP_TOTAL_SCORE,2):"-";
				if ($TOTAL_SCORE) $TOTAL_SCORE .= $TEMP_TOTAL_SCORE;
				else $TOTAL_SCORE = $TEMP_TOTAL_SCORE.","; 			 
			}
		}

		if($PER_TYPE==1){
			$cmd = " select PL_CODE, PM_CODE, PT_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			$CMD_PL_CODE = trim($data2[PL_CODE]);
			
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			if(!$CMD_PM_NAME){
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$CMD_PM_NAME = trim($data2[PM_NAME]);
			}

			$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$cmd = " select LAYER_TYPE from PER_LINE where PL_CODE = '$CMD_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;
	
			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
                        
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        // ��� if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
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

			if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
			$CMD_SALARY_MAX = $LAYER_SALARY_MAX;
		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $LEVEL_NAME):"";
		}elseif($PER_TYPE==3){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";		//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". $LEVEL_NAME):"";	
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} elseif($PER_TYPE==4){
//			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			$TMP_MIDPOINT = $CMD_OLD_SALARY;
		} // end if

		//----------------------------------------------------------------------
		if($DPISDB=="mysql"){
			$tmp_data = explode("|", trim($data[CMD_POSITION]));
		}else{
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
		}
		//㹡óշ���� CMD_PM_NAME
		if(is_array($tmp_data)){
			$CMD_POSITION = $tmp_data[0];
			$CMD_PM_NAME = $tmp_data[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}
		if ($CMD_POSITION==$CMD_PM_NAME) $CMD_PM_NAME = "";
		if ($RPT_N){
			if($PER_TYPE==1)
				$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)? "$CMD_POSITION$LEVEL_NAME" : "") . (trim($CMD_PM_NAME) ?")":"");
		}else{
			$CMD_POSITION = (trim($CMD_PM_NAME) ?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "�����" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME) ?")":"");
		}
		//----------------------------------------------------------------------

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][org_name] = $CMD_ORG4." ".$CMD_ORG3;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_level] = "�." . level_no_format($CMD_LEVEL);
		$arr_content[$data_count][cmd_level_name] =$CMD_LEVEL_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY;
		
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY;
		$arr_content[$data_count][cmd_salary4] = $CMD_SALARY4;
		$arr_content[$data_count][cmd_salary_max] = $CMD_SALARY_MAX;
		$arr_content[$data_count][cmd_spsalary] = $CMD_SPSALARY;
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF;
		$arr_content[$data_count][sah_salary_extra] = $SAH_SALARY_EXTRA;
		$arr_content[$data_count][cmd_salary_extra] = $CMD_SALARY_EXTRA;

//		if ($COM_LEVEL_SALP==9){
//			$arr_content[$data_count][cmd_note1] = $EN_NAME;
//		}else{	
			$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
			$arr_content[$data_count][cmd_step] = $CMD_STEP;
//		}		
		$arr_content[$data_count][cardno] = $PER_CARDNO; 
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][cmd_midpoint] = $TMP_MIDPOINT;
		$arr_content[$data_count][total_score] = $TOTAL_SCORE;
//		if ($COM_LEVEL_SALP==9){
//			$arr_content[$data_count][cmd_note1] = $EM_NAME;
//			$arr_content[$data_count][cmd_note1] = "";
//		}else{	
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
//		}
		$data_count++;
	} // end while
//	echo "$print_order_by / $DEPARTMENT_NAME / ";
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
				$wsdata_border_2[] = "LR";
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
			$CARDNO = $arr_content[$data_count][cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$ORG_NAME = $arr_content[$data_count][org_name];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SALARY4 = $arr_content[$data_count][cmd_salary4];
			$CMD_SALARY_MAX = $arr_content[$data_count][cmd_salary_max];
			$CMD_SPSALARY = $arr_content[$data_count][cmd_spsalary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$SAH_SALARY_EXTRA = $arr_content[$data_count][sah_salary_extra];
			$CMD_SALARY_EXTRA = $arr_content[$data_count][cmd_salary_extra];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_MIDPOINT = $arr_content[$data_count][cmd_midpoint];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
			
			if($CONTENT_TYPE=="CONTENT"){
//				$xlsRow++;
				$wsdata_fontfmt = $wsdata_fontfmt_2;	// ������ ����繵��˹�
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $CARDNO;
				$arr_data[] = $ORG_NAME;
				$arr_data[] = $CMD_POSITION;
				$arr_data[] = $TYPE_NAME;
				$arr_data[] = $LEVEL_NAME;
				$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
				$arr_data[] = $CMD_OLD_SALARY;
				$arr_data[] = $SAH_SALARY_EXTRA;
//						$arr_data[] = $CMD_SALARY;
//						$arr_data[] = $CMD_DIFF;
//						$arr_data[] = $CMD_SPSALARY;
				$arr_data[] = $CMD_SALARY;
				$arr_data[] = $CMD_NOTE2;

				$wsdata_align = array("C","L","C","L","L","C","C","C","R","R","R","C");
//					$wsdata_border = $wsdata_border_1;

				if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;
//				$wsdata_border = $wsdata_border_1;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
	//					echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
						$colseq++;
					}
				}
			} // end if
		} // end for				

//		echo "COM_NOTE=$COM_NOTE<br>";
		if($COM_NOTE){
//			$xlsRow++;
			$arr_data = (array) null;
			$arr_data[] = "";
			$arr_data[] = "<**1**>�����˵� : $COM_NOTE";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			$arr_data[] = "<**1**>";
			
			$wsdata_align = array("L","L","L","L","L","L","L","L","L","L","L","L");
//				$wsdata_border = $wsdata_border_3;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ��ҹ��� map �����º��������
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
//					echo "2..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
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
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
//		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"�ѭ��Ṻ���¤���觻�Ѻ�ѵ���Թ��͹����Ҫ���.xls\"");
	header("Content-Disposition: inline; filename=\"�ѭ��Ṻ���¤���觻�Ѻ�ѵ���Թ��͹����Ҫ���.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>