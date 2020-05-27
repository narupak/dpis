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
	$COM_DATE = show_date_format($data[COM_DATE], 5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
	$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
		
	$COM_TYPE = trim($data[COM_TYPE]);

	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	} elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	if ($print_order_by==1) $order_str = "a.CMD_SEQ";
	else 
		if($DPISDB=="odbc") $order_str = "d.ORG_SEQ_NO , e.ORG_SEQ_NO, f.ORG_SEQ_NO,  CLng(CMD_POS_NO) , CMD_POS_NO_NAME , d.ORG_CODE , e.ORG_CODE , f.ORG_CODE ";
		elseif($DPISDB=="oci8")  $order_str = "nvl(d.ORG_SEQ_NO,0), nvl(e.ORG_SEQ_NO,0), nvl(f.ORG_SEQ_NO,0), to_number(replace(CMD_POS_NO,'-','')) , CMD_POS_NO_NAME, d.ORG_CODE, nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)) ";
		elseif($DPISDB=="mysql")  $order_str = "d.ORG_SEQ_NO, e.ORG_SEQ_NO, f.ORG_SEQ_NO, CMD_POS_NO+0, CMD_POS_NO_NAME, d.ORG_CODE, e.ORG_CODE , f.ORG_CODE ";
		
	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้นเงินเดือน$PERSON_TYPE[$search_per_type]||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่  ".($COM_NO?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_NO):$COM_NO):"-")." ลงวันที่ ". ($COM_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($COM_DATE):$COM_DATE):"-");    
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
		global $worksheet, $xlsRow, $COM_TYPE, $COM_LEVEL_SALP, $BKK_FLAG;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 50);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 50);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ประเภท", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		if ($BKK_FLAG==1)
			$worksheet->write($xlsRow, 4, "ตำแหน่งและหน่วยงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		else
			$worksheet->write($xlsRow, 4, "ตำแหน่งและส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 7, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ประชาชน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "สังกัด/ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		
		
	if($DPISDB=="odbc"){	
		$cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID,a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
										a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO
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
						where	a.COM_ID=$COM_ID and a.AM_SHOW=1
						order by 	$order_str ";
	}elseif($DPISDB=="oci8"){			
		$cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
										a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
										a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						from		PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e, PER_ORG f
						where	a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and 
										c.ORG_ID_2=f.ORG_ID(+) and a.AM_SHOW=1
						order by	$order_str ";
	}elseif($DPISDB=="mysql"){		
		$cmd = " select	a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
										a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY, a.CMD_SPSALARY,
										a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_PERCENT, a.CMD_POS_NO_NAME, a.CMD_POS_NO
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
						where	a.COM_ID=$COM_ID and a.AM_SHOW=1
						order by 	$order_str ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) { $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);  }
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo "-> $cmd";
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
		$CMD_POSITION = $arr_temp[0];
		$CMD_POSITION_M = $arr_temp[1];

		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DIFF = $CMD_SALARY - $CMD_OLD_SALARY;
		$CMD_SPSAARY = $data[CMD_SPSALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_PERCENT = $data[CMD_PERCENT];
		$CMD_STEP = "";
		if ($CMD_NOTE1 == "ข้อ 7 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "0.5 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 8 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544" || 
			$CMD_NOTE1 == "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1 ขั้น";
		elseif ($CMD_NOTE1 == "ข้อ 14 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544")
			$CMD_STEP = "1.5 ขั้น";
		if ($CMD_PERCENT > 0) $CMD_STEP = $CMD_PERCENT;

		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$TYPE_NAME =  $data2[POSITION_TYPE];
		$LEVEL_NAME = $data2[POSITION_LEVEL];
		if($PER_TYPE==1){
			$cmd = " select PM_CODE, PT_CODE 
							from PER_POSITION 
							where trim(POS_NO_NAME)='$CMD_POS_NO_NAME' and trim(POS_NO)='$CMD_POS_NO' and DEPARTMENT_ID = $DEPARTMENT_ID";
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

			$CMD_POSITION = (trim($CMD_PM_NAME)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_NAME)?")":"");

		}elseif($PER_TYPE==2){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
		}elseif($PER_TYPE==3){
//			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
		} // end if

		if ($print_order_by==2) {
			if($CMD_ORG3 != trim($data[CMD_ORG3])){
				$CMD_ORG3 = trim($data[CMD_ORG3]);

				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $CMD_ORG3;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME;

				$data_count++;
			} // end if
		
			if($CMD_ORG4 != trim($data[CMD_ORG4])){
				$CMD_ORG4 = trim($data[CMD_ORG4]);

				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $CMD_ORG4;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_1;

				$data_count++;
			} // end if

			if($CMD_ORG5 != trim($data[CMD_ORG5])){
				$CMD_ORG5 = trim($data[CMD_ORG5]);

				$arr_content[$data_count][type] = "ORG_2";
				$arr_content[$data_count][org_name] = $CMD_ORG5;
				$arr_content[$data_count][new_org_name] = $NEW_ORG_NAME_2;

				$data_count++;
			} // end if
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO_NAME.$CMD_POS_NO;
		//	$arr_content[$data_count][cmd_position] = trim($CMD_POSITION_M)?$CMD_POSITION_M:$CMD_POSITION;
		//	if(trim($CMD_POSITION_M)) $arr_content[$data_count][cmd_position] = "($CMD_POSITION)";		
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		$arr_content[$data_count][cmd_spsalary] = $CMD_SALARY?number_format($CMD_SPSALARY):"-";
		$arr_content[$data_count][cmd_diff] = $CMD_DIFF?number_format($CMD_DIFF):"-";
		
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		$arr_content[$data_count][cmd_step] = $CMD_STEP;
		
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][type_name] = $TYPE_NAME;
		$arr_content[$data_count][level_name] = $LEVEL_NAME;
		if ($print_order_by==1) {
			$data_count++;
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_position] = $CMD_ORG4." ".$CMD_ORG3;
			$arr_content[$data_count][cmd_note] = $CMD_NOTE2;
		}

		$data_count++;
		
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
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];

			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_SPSALARY = $arr_content[$data_count][cmd_spsalary];
			$CMD_DIFF = $arr_content[$data_count][cmd_diff];
			$CMD_STEP = $arr_content[$data_count][cmd_step];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if ($print_order_by==2) {
				if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
					$ORG_NAME = $arr_content[$data_count][org_name];
					$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

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
					} // end if
				} // end if
			}

			if($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 2, (($NUMBER_DISPLAY==2)?convert2thaidigit($CARDNO):$CARDNO) ,set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 3, "$TYPE_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 4, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 5, "$CMD_POS_NO".(($NUMBER_DISPLAY==2)?convert2thaidigit($CMD_POS_NO):$CMD_POS_NO), set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 6, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 7, "$CMD_NOTE2", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
			} // end if
		} // end for				
		
		if($COM_NOTE){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 1, "หมายเหตุ : $COM_NOTE", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
		} // end if
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>