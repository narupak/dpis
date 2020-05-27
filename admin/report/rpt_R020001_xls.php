<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = show_date_format($data[COM_DATE], $DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);
	
	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$select_position = "c.POS_ID,c.POS_NO, c.PT_CODE, c.PL_CODE, c.PM_CODE";
		$column_count=10;
		$type_name="เงินเดือนข้าราชการ";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$select_position = "c.POEM_ID,c.POEM_NO,c.PN_CODE";
		$column_count=8;
		$type_name="ค่าจ้างลูกจ้างประจำ";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$select_position = "c.POEMS_ID,c.POEMS_NO,c.EP_CODE";
		$column_count=8;
		$type_name="ค่าจ้างพนักงานราชการ";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$select_position = "c.POT_ID,c.POT_NO,c.TP_CODE";
		$column_count=8;
		$type_name="ค่าจ้างลูกจ้างชั่วคราว";
	} // end if
	$today = date('d')+ 0 ." ".$month_abbr[(date('m') + 0)][TH] ." ". (date('Y') + 543);

	$company_name = "";
	$report_title = "บัญชีรายละเอียดการเลื่อนขั้น$type_name || ณ วันที่ $today ||แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
	$report_code = "R20001";
	
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
		global $worksheet, $xlsRow, $COM_LEVEL_SALP;
		global $COM_PER_TYPE;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 50);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 15);
		$worksheet->set_column(6, 6, 15);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15); 
		if($COM_PER_TYPE == 1){	
			$worksheet->set_column(9, 9, 25);
			$worksheet->set_column(10, 10, 30);
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		if($COM_PER_TYPE == 1){
			$worksheet->write($xlsRow, 3, "ตำแหน่งและส่วนราชการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		}else{
			$worksheet->write($xlsRow, 3, "ตำแหน่ง/สังกัด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		}
		$worksheet->write($xlsRow, 4, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		if($COM_PER_TYPE == 1){
			$worksheet->write($xlsRow, 5, "เงินเดือนก่อนเลื่อน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 7, "ให้ได้รับเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 9, "เงิน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
			$worksheet->write($xlsRow, 10, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}else{
			$worksheet->write($xlsRow, 5, "อัตราค่าจ้าง (บาท)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 7, "เงินเลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		}

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		if($COM_PER_TYPE == 1){	
			$worksheet->write($xlsRow, 3, "สังกัด/ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}else{
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}
		$worksheet->write($xlsRow, 4, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		if($COM_PER_TYPE == 1){
			$worksheet->write($xlsRow, 5, "อันดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "อันดับ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "ขั้น", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 9, "เลื่อนขั้น", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}else{
			$worksheet->write($xlsRow, 5, "ก่อนเลื่อน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "ให้ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
				   from			(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
							a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
							a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
			   from			PER_COMDTL a, PER_PERSONAL b, $position_table c, PER_ORG d, PER_ORG e
			   where			a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID and $position_join(+) and c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+)
			   order by 		d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
								a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
								a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
								a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, d.ORG_SEQ_NO, e.ORG_SEQ_NO,$select_position
				   from		(
									(
										(
											PER_COMDTL a 
											inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
										) left join $position_table c on ($position_join)
									) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
								) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
				   where		a.COM_ID=$COM_ID
				   order by 	d.ORG_SEQ_NO, e.ORG_SEQ_NO, a.CMD_POS_NO_NAME, a.CMD_POS_NO
				";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
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
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		$arr_temp = explode("\|", $CMD_POSITION);
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
		$CMD_POSITION_M = $arr_temp[2];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$UP_SALARY = ($CMD_SALARY - $CMD_OLD_SALARY);

		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		if($PER_TYPE==1){		
			$POS_ID = $data[POS_ID];
			$POS_NO = $data[POS_NO];
		
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);

			$CMD_POSITION = (trim($CMD_PM_CODE)?"$CMD_PM_NAME (":"") . (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL) . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= 6)?"$CMD_PT_NAME":"")):"") . (trim($CMD_PM_CODE)?")":"");

		}elseif($PER_TYPE==2){		
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POEM_ID = $data[POEM_ID];
			$POS_NO = $data[POEM_NO];
			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select	 PN_NAME from PER_POS_NAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";
		}elseif($PER_TYPE==3){	
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POEMS_ID = $data[POEMS_ID];
			$POS_NO = $data[POEMS_NO];
			
			$EP_CODE = trim($data[EP_CODE]);
			$cmd = " select	 EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$EP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
		}elseif($PER_TYPE==4){	
			///$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";
			
			$POT_ID = $data[POT_ID];
			$POS_NO = $data[POT_NO];
			
			$TP_CODE = trim($data[TP_CODE]);
			$cmd = " select	 TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$TP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];		

			$CMD_POSITION = (trim($PL_NAME))? "$PL_NAME $LEVEL_NAME" : "";	
	} // end if
		if(!$CMD_POS_NO){	$CMD_POS_NO=$POS_NO;		}

		if($CMD_ORG3 != trim($data[CMD_ORG3])){
			$CMD_ORG3 = trim($data[CMD_ORG3]);
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][org_name] = $CMD_ORG3;
			$data_count++;
		} // end if
		
		if($CMD_ORG4 != trim($data[CMD_ORG4])){
			$CMD_ORG4 = trim($data[CMD_ORG4]);
			$arr_content[$data_count][type] = "ORG_1";
			$arr_content[$data_count][org_name] = $CMD_ORG4;
			$data_count++;
		} // end if

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cardno] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME ."\n";
		$arr_content[$data_count][cmd_position] = (trim($CMD_POSITION_M)?"$CMD_POSITION_M\n(":"") . $CMD_POSITION . (trim($CMD_POSITION_M)?")":"");
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		if($PER_TYPE==1){
			$arr_content[$data_count][cmd_level] = "ท." . level_no_format($CMD_LEVEL);
		}
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		$arr_content[$data_count][up_salary] = $UP_SALARY?number_format($UP_SALARY):"-";	//เงินเลื่อนขั้น
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;
		
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
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			if($PER_TYPE==1){
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			if($PER_TYPE==1){
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "L", "", 1));
			}
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CARDNO = $arr_content[$data_count][cardno];
			$NAME = $arr_content[$data_count][name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			if($COM_PER_TYPE==1){
				$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			}
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$UP_SALARY = $arr_content[$data_count][up_salary];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
//				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if($CONTENT_TYPE=="ORG"){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					if($COM_PER_TYPE==1){
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					}
				}else{
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 3, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					if($COM_PER_TYPE==1){
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					}
				} // end if
			}elseif($CONTENT_TYPE=="CONTENT"){
				if($CMD_NOTE2){
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "หมายเหตุ : $CMD_NOTE2", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					if($COM_PER_TYPE==1){
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					}
				}else{
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					$worksheet->write_string($xlsRow, 1, "$CARDNO", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 2, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write_string($xlsRow, 3, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					$worksheet->write($xlsRow, 4, "$CMD_POS_NO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
					if($COM_PER_TYPE==1){
						$worksheet->write_string($xlsRow, 5, "$CMD_LEVEL", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
						$worksheet->write($xlsRow, 6, "$CMD_OLD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 7, "$CMD_LEVEL", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 8, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write_string($xlsRow, 9, "$UP_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 10, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					}else{
						$worksheet->write_string($xlsRow, 5, "$CMD_OLD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 6, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 7, "$UP_SALARY", set_format("xlsFmtTableDetail", "", "R", "LTRB", 0));
						$worksheet->write($xlsRow, 8, "$CMD_NOTE1", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
					}
				} // end if
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
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			if($COM_PER_TYPE==1){
				$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 0));
				$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			}
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
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if($COM_PER_TYPE==1){
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีรายละเอียดการเลื่อนขั้น$type_name.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีรายละเอียดการเลื่อนขั้น$type_name.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>