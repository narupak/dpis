<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		CP_NAME, CP_START_DATE, CP_END_DATE, CP_CYCLE, ORG_ID, PER_TYPE 
					 from		PER_COMPENSATION_TEST 
					 where	CP_ID=$CP_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$CP_NAME = trim($data[CP_NAME]);
	$CP_START_DATE = $data[CP_START_DATE];
	$CP_END_DATE = $data[CP_END_DATE];
	$arr_temp = explode("-", $CP_END_DATE);
	$CP_YEAR = $arr_temp[0] + 543;
	$CP_CYCLE = trim($data[CP_CYCLE]);
	$ORG_ID = $data[ORG_ID];
	$PER_TYPE = $data[PER_TYPE];

	if ($ORG_ID) {
		$cmd = " select	ORG_NAME from	PER_ORG where	ORG_ID=$ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_NAME = trim($data[ORG_NAME]);
	}

	if($PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$position_order = "c.POS_NO";
	}elseif($PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$position_order = "c.POEMS_NO";
	} // end if

	$company_name = "";
	$report_title = " ��§ҹẺ���ͺ��ú����ä�ҵͺ᷹  $CP_NAME||$ORG_NAME  �է�����ҳ  $CP_YEAR  �ͺ��û����Թ  ���駷��  $CP_CYCLE ";
	$report_code = "";
	
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header(){
		global $worksheet, $xlsRow, $PER_TYPE, $BKK_FLAG;

		if($PER_TYPE == 1){
			$worksheet->set_column(0, 0, 6);
			$worksheet->set_column(1, 1, 30);
			$worksheet->set_column(2, 2, 15);
			//$worksheet->set_column(3, 3, 15);/*Release 5.2.1.8*/
			$worksheet->set_column(3, 3, 50);
			$worksheet->set_column(4, 4, 15);
			$worksheet->set_column(5, 5, 10);
                        $worksheet->set_column(6, 6, 15);
			$worksheet->set_column(7, 7, 15);/*Release 5.2.1.20*/
                        $worksheet->set_column(8, 8, 15);
                        $worksheet->set_column(9, 9, 15);
                        $worksheet->set_column(10, 10, 30);
                        
			$worksheet->set_column(11, 11, 30);/*Release 5.2.1.20*/
			$worksheet->set_column(12, 12, 15);
			$worksheet->set_column(13, 13, 15);/*Release 5.2.1.20*/
			$worksheet->set_column(14, 14, 10);

			$xlsRow++;
			
                        /*Release 5.1.0.7 Begin*/
                        $worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "����-���ʡ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "�Ţ��Шӵ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			//$worksheet->write($xlsRow, 3, "������xxxxx", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*Release 5.2.1.8*/
			if ($BKK_FLAG==1)
				$worksheet->write($xlsRow, 3, "���˹����˹��§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			else
				$worksheet->write($xlsRow, 3, "���˹������ǹ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			
                        $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
                        $worksheet->write($xlsRow, 6, "�Թ��͹���", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "�ѵ���Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));  /*Release 5.2.1.20*/
                        $worksheet->write($xlsRow, 8, "�ҹ㹡�äӹǳ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                        $worksheet->write($xlsRow, 9, "�����з��������͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                        $worksheet->write($xlsRow, 10, "�ӹǹ�Թ���������͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                        
                        $worksheet->write($xlsRow, 11, "�ӹǹ��ҵͺ᷹�����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*Release 5.2.1.20*/
			$worksheet->write($xlsRow, 12, "�Թ��͹������Ѻ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			//$worksheet->write($xlsRow, 11, "�š��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*Release 5.2.1.8*/
			$worksheet->write($xlsRow, 13, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                        $worksheet->write($xlsRow, 14, "�š��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 2, "��ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			//$worksheet->write($xlsRow, 3, "���˹�xxxxx", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));/*Release 5.2.1.8*/
			$worksheet->write($xlsRow, 3, "�ѧ�Ѵ/���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));   /*Release 5.2.1.20*/
			$worksheet->write($xlsRow, 4, "�дѺ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 5, "�Ţ�����˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 6, "(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 7, "�٧���Ң���٧(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));/*Release 5.2.1.20*/
                        $worksheet->write($xlsRow, 8, "(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                        $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                        $worksheet->write($xlsRow, 10, "(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));       /*Release 5.2.1.20*/
                        
			$worksheet->write($xlsRow, 11, "������Ѻ(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));/*Release 5.2.1.20*/
			$worksheet->write($xlsRow, 12, "(�ҷ)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			//$worksheet->write($xlsRow, 11, "�����Թ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));/*Release 5.2.1.8*/
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                        $worksheet->write($xlsRow, 14, "�����Թ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                        /*Release 5.1.0.7 End*/
                        /*���*/
                        /*$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "�Ţ��Шӵ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 3, "������", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			if ($BKK_FLAG==1)
				$worksheet->write($xlsRow, 4, "���˹����˹��§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			else
				$worksheet->write($xlsRow, 4, "���˹������ǹ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 7, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "�Թ�ͺ᷹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 10, "������Ѻ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "�ӹǹ�Թ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "�����繵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 13, "�ҹ㹡��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 14, "�š��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 15, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 2, "��ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 3, "���˹�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 4, "�ѧ�Ѵ/���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "������", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "�����", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 9, "��͹����͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 10, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 11, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 12, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 13, "�ӹǳ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 14, "�����Թ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));*/
		}elseif($PER_TYPE == 3){
			$worksheet->set_column(0, 0, 6);
			$worksheet->set_column(1, 1, 30);
			$worksheet->set_column(2, 2, 15);
			$worksheet->set_column(3, 3, 50);
			$worksheet->set_column(4, 4, 15);//�дѺ���˹�
			$worksheet->set_column(5, 5, 10);//�Ţ���
			$worksheet->set_column(6, 6, 15);
			$worksheet->set_column(7, 7, 15);
			$worksheet->set_column(8, 8, 15);
			$worksheet->set_column(9, 9, 15);
			$worksheet->set_column(10, 10, 10);
			$worksheet->set_column(11, 11, 10);
			$worksheet->set_column(12, 12, 50);

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "�ӴѺ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1, "����", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2, "�Ţ��Шӵ��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			if ($BKK_FLAG==1)
				$worksheet->write($xlsRow, 3, "���˹����˹��§ҹ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			else
				$worksheet->write($xlsRow, 3, "���˹������ǹ�Ҫ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 7, "�ѵ���Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 8, "������Ѻ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 9, "�ӹǹ�Թ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 10, "�����繵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 11, "�š��", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 12, "�����˵�", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 2, "��ЪҪ�", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 3, "�ѧ�Ѵ/���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "�дѺ���˹�", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 5, "�Ţ���", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                        $worksheet->write($xlsRow, 6, "���", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 7, "���������", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "�Թ��͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 9, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 10, "�������͹", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 11, "�����Թ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		} // end if
	} // function		
		
	if($DPISDB=="odbc"){
		$cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, a.AL_CODE, 
								d.ORG_NAME AS CMD_ORG3, e.ORG_NAME AS CMD_ORG4, f.ORG_NAME as CMD_ORG5, b.PER_SALARY, a.LEVEL_NO, 
								b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, a.CD_SALARY, a.CD_EXTRA_SALARY, a.CD_PERCENT
				   from		(
									(
										(
											(
												PER_COMPENSATION_TEST_DTL a  
												inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
											) left join $position_table c on ($position_join)
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
									) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
								) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				   where			a.CP_ID=$CP_ID
				   order by 		d.ORG_SEQ_NO, e.ORG_SEQ_NO, IIf(IsNull($position_order), 0, CLng($position_order)) ";
	}elseif($DPISDB=="oci8"){
                $cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, a.AL_CODE, 
								d.ORG_NAME AS CMD_ORG3, e.ORG_NAME AS CMD_ORG4, f.ORG_NAME as CMD_ORG5, b.PER_SALARY, a.LEVEL_NO,
								b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID, a.CD_SALARY, a.CD_EXTRA_SALARY, a.CD_PERCENT
				   from			PER_COMPENSATION_TEST_DTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f
				   where			a.CP_ID=$CP_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+)

				order by 		nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)), to_number(replace($position_order,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, a.AL_CODE, 
								d.ORG_NAME AS CMD_ORG3, e.ORG_NAME AS CMD_ORG4, f.ORG_NAME as CMD_ORG5, b.PER_SALARY, a.LEVEL_NO, 
								b.POS_ID, b.POEM_ID, b.POEMS_ID,b.POT_ID, a.CD_SALARY, a.CD_EXTRA_SALARY, a.CD_PERCENT
				   from		(
									(
										(
											(
												PER_COMPENSATION_TEST_DTL a 
												inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
											) left join $position_table c on ($position_join)
										) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
									) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
								) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				   where		a.CP_ID=$CP_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, $position_order+0 ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
      // echo '<pre>';
      //  die($cmd);
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
		$LEVEL_NO = trim($data[LEVEL_NO]);
//		$CMD_ORG3 = $data[CMD_ORG3];
		$PER_SALARY = $data[PER_SALARY];
		$CMD_DIFF = $data[CD_SALARY];
		$CD_EXTRA_SALARY = $data[CD_EXTRA_SALARY];
		$CMD_SALARY = $PER_SALARY + $CMD_DIFF;
                
                //if(empty($CMD_DIFF)){$CMD_DIFF=$CD_EXTRA_SALARY;}/*http://dpis.ocsc.go.th/Service/node/1878#*/  /* Release 5.2.1.20 */
                
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CD_PERCENT = $data[CD_PERCENT];
		$CMD_STEP = "";
		if ($CD_PERCENT > 0) $CMD_STEP = $CD_PERCENT;
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$AL_CODE = $data[AL_CODE];

		$cmd = " select AL_NAME from PER_ASSESS_LEVEL where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and AL_CODE='$AL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_NOTE1 = trim($data2[AL_NAME]);

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME = $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];

		if($PER_TYPE==1){
                    $cmd = " select TOTAL_SCORE, SALARY_FLAG, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $PER_ID and KF_CYCLE = $CP_CYCLE and 
                                                    KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                    $db_dpis2->send_cmd($cmd);
                    $data2 = $db_dpis2->get_array();
                    $TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 			 
                    $SALARY_FLAG = trim($data2[SALARY_FLAG]); 			 
                    $SALARY_REMARK1 = trim($data2[SALARY_REMARK1]); 			
                    if ($SALARY_FLAG=="N") $CMD_NOTE1 = $SALARY_REMARK1;
		}elseif($PER_TYPE==3){
                        //Release 5.0.0.43 Begin
                        //��Ѻ���� Flow...
                        $Comment ="";
                        //$SALARY_FLAG="";
                        //$SALARY_REMARK1="";
                        if(1==0){
                            /*******************************************/
                        // 1. ��Ǩ�ͺ����դ�ṹ�ͺ��� 2 �������
                            $cmdTot = " SELECT TOTAL_SCORE ,SALARY_FLAG, SALARY_REMARK1
                                 FROM PER_KPI_FORM 
                                 WHERE PER_ID = $PER_ID AND KF_CYCLE=2
                                     AND KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                            
                            $db_dpis2->send_cmd($cmdTot);
                            $dataTot = $db_dpis2->get_array();
                            $ChkTOTAL_SCORE =$dataTot[TOTAL_SCORE];
                            $ChkSALARY_FLAG =$dataTot[SALARY_FLAG];
                            $ChkSALARY_REMARK1 =$dataTot[SALARY_REMARK1];
                            if(!empty($ChkTOTAL_SCORE) && $ChkTOTAL_SCORE >=0 ){ // �դ�ṹ
                                //��Ǩ�ͺ ʶҹ�
                                if($ChkSALARY_FLAG=="Y"){ //����͹
                                    //��Ǩ������դ�ṹ�ͺ��� 1 �������...
                                   $cmdTot = " SELECT TOTAL_SCORE ,SALARY_FLAG, SALARY_REMARK1
                                        FROM PER_KPI_FORM 
                                        WHERE PER_ID = $PER_ID AND KF_CYCLE=1
                                            AND KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";

                                   $db_dpis2->send_cmd($cmdTot);
                                   $dataTot = $db_dpis2->get_array();
                                   $ChkTOTAL_SCORE =$dataTot[TOTAL_SCORE];
                                   $ChkSALARY_FLAG =$dataTot[SALARY_FLAG];
                                   $ChkSALARY_REMARK1 =$dataTot[SALARY_REMARK1];
                                   if(!empty($ChkTOTAL_SCORE) && $ChkTOTAL_SCORE >=0 ){ //�դ�ṹ
                                       $cmd = " SELECT (sum(TOTAL_SCORE)/sum(1)) as TOTAL_SCORE  
                                                FROM PER_KPI_FORM 
                                                WHERE PER_ID = $PER_ID 
                                                    AND KF_START_DATE >= '$CP_START_DATE'  AND  KF_END_DATE <= '$CP_END_DATE'  ";
                                       $db_dpis2->send_cmd($cmd);
                                       $data2 = $db_dpis2->get_array();
                                       $TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 
                                       //�֧�����˵�
                                        $cmd = " select SALARY_FLAG, SALARY_REMARK1 
                                             from PER_KPI_FORM 
                                             where PER_ID = $PER_ID AND KF_CYCLE=2
                                                 and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                                        $db_dpis2->send_cmd($cmd);
                                        $dataRemark = $db_dpis2->get_array();
                                        $SALARY_FLAG = trim($dataRemark[SALARY_FLAG]);
                                        $SALARY_REMARK1 = trim($dataRemark[SALARY_REMARK1]);
                                       
                                   }else{ // ����դ�ṹ
                                      //$Comment = "��辺�����Ť�ṹ�ͺ��û����Թ��� 1";
                                      $CMD_NOTE1= "��辺�����Ť�ṹ�ͺ��û����Թ��� 1";
                                   }
                                }else{ //�������͹
                                    //��Ǩ������դ�ṹ�ͺ��� 1 �������...
                                   $cmdTot = " SELECT TOTAL_SCORE ,SALARY_FLAG, SALARY_REMARK1
                                        FROM PER_KPI_FORM 
                                        WHERE PER_ID = $PER_ID AND KF_CYCLE=1
                                            AND KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";

                                   $db_dpis2->send_cmd($cmdTot);
                                   $dataTot = $db_dpis2->get_array();
                                   $ChkTOTAL_SCORE =$dataTot[TOTAL_SCORE];
                                   $ChkSALARY_FLAG =$dataTot[SALARY_FLAG];
                                   $ChkSALARY_REMARK1 =$dataTot[SALARY_REMARK1];
                                   if(!empty($ChkTOTAL_SCORE) && $ChkTOTAL_SCORE >=0 ){ //�դ�ṹ
                                        $cmd = " SELECT (sum(TOTAL_SCORE)/sum(1)) as TOTAL_SCORE  
                                                 FROM PER_KPI_FORM 
                                                 WHERE PER_ID = $PER_ID 
                                                     AND KF_START_DATE >= '$CP_START_DATE'  AND  KF_END_DATE <= '$CP_END_DATE'  ";
                                        $db_dpis2->send_cmd($cmd);
                                        $data2 = $db_dpis2->get_array();
                                        $TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 
                                        //�֧�����˵�
                                        $cmd = " select SALARY_FLAG, SALARY_REMARK1 
                                             from PER_KPI_FORM 
                                             where PER_ID = $PER_ID AND KF_CYCLE=2
                                                 and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                                        $db_dpis2->send_cmd($cmd);
                                        $dataRemark = $db_dpis2->get_array();
                                        $SALARY_FLAG = trim($dataRemark[SALARY_FLAG]);
                                        $SALARY_REMARK1 = trim($dataRemark[SALARY_REMARK1]);
                                   }else{// ����դ�ṹ
                                       $cmd = " select TOTAL_SCORE,SALARY_FLAG, SALARY_REMARK1 
                                             from PER_KPI_FORM 
                                             where PER_ID = $PER_ID AND KF_CYCLE=2
                                                 and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                                        $db_dpis2->send_cmd($cmd);
                                        $dataRemark = $db_dpis2->get_array();
                                        $TOTAL_SCORE = trim($dataRemark[TOTAL_SCORE]);
                                        $SALARY_FLAG = trim($dataRemark[SALARY_FLAG]);
                                        $SALARY_REMARK1 = trim($dataRemark[SALARY_REMARK1]);
                                   }
                                }
                            }else{ // ����դ�ṹ
                                $Comment ="��辺��ṹ�����Թ�ͺ��� 2";
                            }
                        /*******************************************/
                        }else{
                            $cmd = " select (sum(TOTAL_SCORE)/sum(1)) as TOTAL_SCORE , sum(1) as CNTCYCLE  
                                 from PER_KPI_FORM 
                                 where PER_ID = $PER_ID 
                                     and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 
                            $CNTCYCLE = $data2[CNTCYCLE];
                            //echo $cmd."<br>";
                            if ($CNTCYCLE==1){ //�����ͺ���� ���ѧ��������� �����ͺ�...
                                //�ó��� �ͺ�á��������..
                                $cmd2 = " select SALARY_FLAG, SALARY_REMARK1 
                                     from PER_KPI_FORM 
                                     where PER_ID = $PER_ID AND KF_CYCLE=1
                                         and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";

                                $cntCYCLE1 = $db_dpis2->send_cmd($cmd2);
                                $dataC1 = $db_dpis2->get_array();

                                $cmd2 = " select SALARY_FLAG, SALARY_REMARK1 
                                     from PER_KPI_FORM 
                                     where PER_ID = $PER_ID AND KF_CYCLE=2
                                         and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";

                                $cntCYCLE2 = $db_dpis2->send_cmd($cmd2);
                                $dataC2 = $db_dpis2->get_array();
                                
                                

                                if($cntCYCLE1==1 && $cntCYCLE2==0){ // �ͺ��� 1 �ͺ����
                                    $SALARY_FLAG = trim($dataC1[SALARY_FLAG]);
                                    if($SALARY_FLAG=="Y"){$SALARY_FLAG="N";}
                                    $SALARY_REMARK1 = trim($dataC1[SALARY_REMARK1]);
                                    $Comment =" (�������Ż����Թ�ͺ�á��ҹ��)";
                                }
                                if($cntCYCLE1==0 && $cntCYCLE2==1){ // �ͺ��� 2 �ͺ����
                                    $SALARY_FLAG = trim($dataC2[SALARY_FLAG]);
                                    if($SALARY_FLAG=="Y"){$SALARY_FLAG="N";}
                                    $SALARY_REMARK1 = trim($dataC2[SALARY_REMARK1]);
                                    $Comment =" (�ռŤ�ṹ�ͺ���� ����͹��������ͧ�ҡ �ӧҹ���ú 8 ��͹)";  
                                }
                                
                            }elseif($CNTCYCLE==2){ //$CNTCYCLE==2                      
                                $cmd2 = " select SALARY_FLAG, SALARY_REMARK1 
                                        from PER_KPI_FORM 
                                        where PER_ID = $PER_ID AND KF_CYCLE=2
                                            and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                               $db_dpis2->send_cmd($cmd2);
                               
                               $data3 = $db_dpis2->get_array();
                               $SALARY_FLAG = trim($data3[SALARY_FLAG]); 			 
                               $SALARY_REMARK1 = trim($data3[SALARY_REMARK1]); 
                            }
                        }
                        
                        
                         
                        
                        
                        if ($SALARY_FLAG=="N") 
                            $CMD_NOTE1 = $SALARY_REMARK1.$Comment;
                        //Release 5.0.0.43 End
                        
                        
                        
			//���
                        /*$cmd = " select (sum(TOTAL_SCORE)/2) as TOTAL_SCORE, SALARY_FLAG, SALARY_REMARK1 from PER_KPI_FORM where PER_ID = $PER_ID and 
							KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";*/
		}
               // die($PER_TYPE."-->".$cmd);
               
                //��� 
		/*$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TOTAL_SCORE = $data2[TOTAL_SCORE] + 0; 			 
		$SALARY_FLAG = trim($data2[SALARY_FLAG]); 			 
		$SALARY_REMARK1 = trim($data2[SALARY_REMARK1]); 			
		if ($SALARY_FLAG=="N") $CMD_NOTE1 = $SALARY_REMARK1;*/

		if($PER_TYPE==1){
			$cmd = " select POS_NO, PL_CODE, PM_CODE, PT_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POS_NO = trim($data2[POS_NO]);
			$CMD_PL_CODE = trim($data2[PL_CODE]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			
			$cmd = " select PL_NAME, LAYER_TYPE from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POSITION = trim($data2[PL_NAME]);
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_NAME = trim($data2[PT_NAME]);

			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION . (($CMD_PT_NAME != "�����" && $LEVEL_NO >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2,LAYER_SALARY_TEMPUP
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
                        
                        $LAYER_SALARY_TEMPUP =$data2[LAYER_SALARY_TEMPUP]; /*�Թ��͹����٧(2)*/
                        
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        // ��� if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY <= $data2[LAYER_SALARY_FULL]) {
			if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY < $data2[LAYER_SALARY_FULL]) {
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
                        //http://dpis.ocsc.go.th/Service/node/2770 �ʴ��ѵ���Թ��͹����٧���Ң���٧ ������ ���.
                        $LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
                        
			if($SALARY_POINT_MID > $PER_SALARY) {
				$TMP_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$TMP_MIDPOINT = $SALARY_POINT_MID2;
			}
                        /*Release 5.2.1.6 Begin*/
                        /*** ���˹���������зç�س�ز� ������Թ��͹��ǧ��*/
                        $comment=' ';
                        if($LEVEL_NO == "O3" || $LEVEL_NO == "K5"){
                            if( $PER_SALARY >=$data2[LAYER_SALARY_FULL]){
                                $TMP_MIDPOINT = $data2[LAYER_SALARY_MIDPOINT2];
                                
                            }
                            //$comment=' '.$PER_SALARY.'>='.$SALARY_POINT_MID;
                            if(($PER_SALARY>=$SALARY_POINT_MID || $PER_SALARY<=$SALARY_POINT_MID) ){
                                //$comment=' **';
                            }
                        }
                        /*Release 5.2.1.6 End*/
                        
                        //�������ʴ��Թ��͹���������ʹ����Թ���ش�ѭ���������
			//if ($CD_EXTRA_SALARY > 0) $CMD_SALARY_MAX = $LAYER_SALARY_MAX;    /* Release 5.2.1.20 */
			//else $CMD_SALARY_MAX = 0;                                        /* Release 5.2.1.20 */
                        $CMD_SALARY_MAX = $LAYER_SALARY_MAX;    /* Release 5.2.1.20 */
		}elseif($PER_TYPE==3){
			$cmd = " select a.POEMS_NO, a.EP_CODE, b.EP_NAME 
							  from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b where a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POS_NO = trim($data2[POEMS_NO]);
			$CMD_EP_CODE = trim($data2[EP_CODE]);
			$CMD_POSITION = trim($data2[EP_NAME]);

			$cmd = " select LAYER_SALARY_MAX from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_SALARY_MAX = $data2[LAYER_SALARY_MAX];
		} elseif($PER_TYPE==4){
			$cmd = " select a.POT_NO, a.TP_CODE, b.TP_NAME 
							  from PER_POS_TEMP a, PER_TEMP_POS_NAME b where a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_POS_NO = trim($data2[POT_NO]);
			$CMD_TP_CODE = trim($data2[TP_CODE]);
			$CMD_POSITION = trim($data2[TP_NAME]);

			$cmd = " select LAYER_SALARY_MAX from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$LEVEL_NO' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_SALARY_MAX = $data2[LAYER_SALARY_MAX];
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
		$arr_content[$data_count][per_cardno] = $PER_CARDNO;
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
                 /*Release 5.2.1.6 Begin*/
                $arr_content[$data_count][layer_salary_tempup] = $LAYER_SALARY_TEMPUP;
                $str_comment='';
                if($SALARY_FLAG=='Y'){
                    if($CMD_SALARY>=$LAYER_SALARY_TEMPUP){ //�Թ������Ѻ
                        $str_comment=' *';
                    }
                    if($PER_SALARY>=$LAYER_SALARY_TEMPUP){ //�Թ��͹���
                        if($PER_TYPE == 3){
                            $str_comment='';
                        }else{
                            $str_comment=' **';
                        }    
                        
                    }
                }
                /*if($CMD_DIFF){
                    if($PER_SALARY<$LAYER_SALARY_MAX){
                        $str_comment=' **';
                    }
                    if($PER_SALARY>=$LAYER_SALARY_MAX){
                        $str_comment=' *';
                    }
                }*/
		/*Release 5.2.1.6 End*/
                
		$arr_content[$data_count][per_salary] = $PER_SALARY?number_format($PER_SALARY):"-";
                
               
                
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		$arr_content[$data_count][cmd_salary_max] = $CMD_SALARY_MAX?number_format($CMD_SALARY_MAX):"-";
		$arr_content[$data_count][cd_extra_salary] = $CD_EXTRA_SALARY?number_format($CD_EXTRA_SALARY,2):"-";
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF?number_format($CMD_DIFF).$str_comment:"-"; //�ӹǹ�Թ���������͹/���/���� ��ҵͺ᷹�����(�ҷ) http://dpis.ocsc.go.th/Service/node/1878#
		
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1.$comment;
		$arr_content[$data_count][cmd_step] = $CMD_STEP;
		
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		$arr_content[$data_count][cmd_midpoint] = $TMP_MIDPOINT?number_format($TMP_MIDPOINT):"-";
		$arr_content[$data_count][total_score] = $TOTAL_SCORE?number_format($TOTAL_SCORE,2):"-";
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if($PER_TYPE == 1){
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} // end if
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			if($PER_TYPE == 1){
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			} // end if
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$TYPE_NAME = $arr_content[$data_count][type_name];
			$LEVEL_NAME = $arr_content[$data_count][level_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
//			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$PER_SALARY = $arr_content[$data_count][per_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SALARY_MAX = $arr_content[$data_count][cmd_salary_max];
			$CD_EXTRA_SALARY = $arr_content[$data_count][cd_extra_salary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$CMD_MIDPOINT = $arr_content[$data_count][cmd_midpoint];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
                        //$LAYER_SALARY_TEMPUP = $arr_content[$data_count][layer_salary_tempup];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if($CONTENT_TYPE=="ORG"){
					$xlsRow++;
					if($PER_TYPE == 1){
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						//$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));/*Release 5.2.1.8*/
						$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));/*Release 5.2.1.20*/
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));/*Release 5.2.1.20*/
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						//$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));/*Release 5.2.1.8*/
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
                                                $worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					}elseif($PER_TYPE == 3){
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					} // end if
				}else{
					$xlsRow++;
					if($PER_TYPE == 1){
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						//$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*Release 5.2.1.8*/
						$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*Release 5.2.1.20*/
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*Release 5.2.1.20*/
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						//$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*Release 5.2.1.8*/
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                                $worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*Release 5.2.1.9*/
					}elseif($PER_TYPE == 3){
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					} // end if
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
					$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					if($PER_TYPE == 1){
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					} // end if
				}else{
					$xlsRow++;
					if($PER_TYPE == 1){
						$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
						$worksheet->write_string($xlsRow, 2, card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						//$worksheet->write_string($xlsRow, 3, "$TYPE_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));/*Release 5.2.1.8*/
						$worksheet->write_string($xlsRow, 3, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
						$worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 5, "$CMD_POS_NO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 6, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 7, "$CMD_SALARY_MAX", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));/*Release 5.2.1.20*/
                                                $worksheet->write_string($xlsRow, 8, "$CMD_MIDPOINT", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 9, "$CMD_STEP", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 10, "$CMD_DIFF", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
                                                
						$worksheet->write_string($xlsRow, 11, "$CD_EXTRA_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));/*Release 5.2.1.20*/
                                                $worksheet->write_string($xlsRow, 12, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						//$worksheet->write_string($xlsRow, 11, "$TOTAL_SCORE", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));/*Release 5.2.1.8*/
						$worksheet->write_string($xlsRow, 13, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 14, "$TOTAL_SCORE", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));/*Release 5.2.1.9*/
					}elseif($PER_TYPE == 3){
						$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
						$worksheet->write_string($xlsRow, 2,card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						$worksheet->write_string($xlsRow, 3, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 4, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						$worksheet->write_string($xlsRow, 5, "$CMD_POS_NO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
                                                $worksheet->write_string($xlsRow, 6, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 7, "$CMD_SALARY_MAX", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						
						$worksheet->write_string($xlsRow, 8, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 9, "$CMD_DIFF", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 10, "$CMD_STEP", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 11, "$TOTAL_SCORE", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 12, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					} // end if
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			if($PER_TYPE == 1){
				$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			} // end if
		} // end if
                
                /*Release 5.2.1.6 ���������˵� * ** Begin*/
//                $xlsRow++;
//                $worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 1, "* ���¶֧ ��ҵͺ᷹����ɵ������º��з�ǧ��ä�ѧ��Ҵ��¡���ԡ���¤�ҵͺ᷹����ɢͧ����Ҫ�������١��ҧ��ШӼ�����Ѻ�Թ��͹���ͤ�Ҩ�ҧ����٧�������֧����٧�ͧ", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
        
        /*�١�Դ� Release 5.2.1.25*/
                /*$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 1, "* ���¶֧ �ӹǹ�Թ��͹���������͹����Ѻ�ӹǹ�Թ������Ѻ������鹵��˹ѧ����ӹѡ�ŢҸԡ�ä���Ѱ����� ��ǹ����ش ��� �� ����/� ��� ŧ�ѹ��� �� ���Ҥ� ����", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));*/
        /*�١�Դ� Release 5.2.1.25*/
        
            //http://dpis.ocsc.go.th/Service/node/2228
                $xlsRow++;
            if($PER_TYPE != 3){
                $worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 1, "* ���¶֧ �ӹǹ�Թ��͹���������͹����Ѻ�ӹǹ�Թ������Ѻ������鹵��˹ѧ����ӹѡ�ŢҸԡ�ä���Ѱ����� ��ǹ����ش ��� �� ����/� ��� ŧ�ѹ��� �� ���Ҥ� ����", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
            }
                
              
         /*
         //�١�Դ� Release 5.2.1.25
                $worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 1, "** ���¶֧ �ӹǹ�Թ������Ѻ������鹵��˹ѧ����ӹѡ�ŢҸԡ�ä���Ѱ����� ��ǹ����ش ��� �� ����/� ��� ŧ�ѹ��� �� ���Ҥ� ����", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
        //�١�Դ� Release 5.2.1.25
        */
               $xlsRow++;
            if($PER_TYPE != 3){
                $worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 1, "** ���¶֧ �ӹǹ�Թ������Ѻ������鹵��˹ѧ����ӹѡ�ŢҸԡ�ä���Ѱ����� ��ǹ����ش ��� �� ����/� ��� ŧ�ѹ��� �� ���Ҥ� ����", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
            }
          // http://dpis.ocsc.go.th/Service/node/2228     
       
                /*Release 5.2.1.6 End*/
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
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if($PER_TYPE == 1){
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"Ẻ���ͺ��ú����ä�ҵͺ᷹.xls\"");
	header("Content-Disposition: inline; filename=\"Ẻ���ͺ��ú����ä�ҵͺ᷹.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>