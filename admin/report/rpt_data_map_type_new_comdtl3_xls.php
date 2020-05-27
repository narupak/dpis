<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = show_date_format($data[COM_DATE],$DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);

	$company_name = "";
	$report_title = "บัญชีแต่งตั้งข้าราชการพลเรือนสามัญให้ดำรงตำแหน่งตามมาตรา 131 พระราชบัญญัติระเบียบข้าราชการพลเรือน พ.ศ.2551<br>ใน$DEPARTMENT_NAME  $MINISTRY_NAME  บัญชี 3";
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
		global $worksheet, $xlsRow;

		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 6);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 40);
		$worksheet->set_column(5, 5, 35);
		$worksheet->set_column(6, 6, 35);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15);
		$worksheet->set_column(9, 9, 15);
		$worksheet->set_column(10, 10, 40);
//		$worksheet->set_column(15, 15, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TRL", 1));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 2, "ชื่อ-นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 3, "เลขประจำตัว", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 4, "คุณวุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 5, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 6, "ส่วนราชการและตำแหน่งที่ กำหนดใหม่", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 7, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 8, " ", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 9, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 10, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));
//		$worksheet->write($xlsRow, 15, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));

		set_format("xlsFmtTableHeader", "B", "C", "LRB", 0);
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ผู้ดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ประชาชน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
//		$worksheet->write($xlsRow, 15, "เงินประจำตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$select_top = ($current_page==$total_page)?($count_data - ($data_per_page * ($current_page - 1))):$data_per_page;			
		$cmd = "	select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_TYPE, b.PER_STATUS, b.PER_CARDNO, a.PL_CODE, a.PL_CODE_ASSIGN, b.PER_MGTSALARY,
							a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
							a.POS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, c.PM_CODE, c.PT_CODE, c.SKILL_CODE, c.POS_CONDITION, c.CL_NAME
				from			((((PER_COMDTL a 
							     left join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
							) left join PER_POSITION c on (a.CMP_POS_NO_NAME=c.POS_NO_NAME and a.CMD_POS_NO=c.POS_NO)
							) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and c.POS_STATUS = 1
				order by 		IIf(IsNull(d.ORG_SEQ_NO), 0, d.ORG_SEQ_NO), d.ORG_CODE, 
							IIf(IsNull(e.ORG_SEQ_NO), 0, e.ORG_SEQ_NO), IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), 
							IIf(IsNull(f.ORG_SEQ_NO), 0, f.ORG_SEQ_NO), IIf(IsNull(f.ORG_CODE),  IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), f.ORG_CODE), 
							a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_TYPE, b.PER_STATUS, b.PER_CARDNO, a.PL_CODE, a.PL_CODE_ASSIGN, b.PER_MGTSALARY,
											a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, c.PM_CODE, c.PT_CODE, c.SKILL_CODE, c.POS_CONDITION, c.CL_NAME
				from			PER_COMDTL a, PER_PERSONAL b, PER_POSITION c, PER_ORG d, PER_ORG e, PER_ORG f
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and a.PER_ID=b.PER_ID(+) and 
									a.CMP_POS_NO_NAME=c.POS_NO_NAME(+) and a.CMD_POS_NO=c.POS_NO(+) and 
									c.ORG_ID=d.ORG_ID(+) and c.ORG_ID_1=e.ORG_ID(+) and c.ORG_ID_2=f.ORG_ID(+) and c.POS_STATUS = 1
				order by 		nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)), a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_TYPE, b.PER_STATUS, b.PER_CARDNO, a.PL_CODE, a.PL_CODE_ASSIGN, b.PER_MGTSALARY,
							a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_ORG5, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
							a.POS_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
							a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, c.PM_CODE, c.PT_CODE, c.SKILL_CODE, c.POS_CONDITION, c.CL_NAME
				from			((((PER_COMDTL a 
							     left join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
							) left join PER_POSITION c on (a.CMP_POS_NO_NAME=c.POS_NO_NAME and a.CMD_POS_NO=c.POS_NO)
							) left join PER_ORG d on (c.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (c.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (c.ORG_ID_2=f.ORG_ID)
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and c.POS_STATUS = 1
				order by 		d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	} // end if	
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_TYPE = $data[PER_TYPE];
		$PER_STATUS = $data[PER_STATUS];
		if ($PER_STATUS==2) $PER_NAME = "";
		$PER_MGTSALARY = $data[PER_MGTSALARY];
		$CMD_PL_CODE = trim($data[PL_CODE]);
		$CMD_PL_CODE_NEW = trim($data[PL_CODE_ASSIGN]);

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $CMD_POSITION);
		}else{
			$arr_temp = explode("\|", $CMD_POSITION);
		}
		$CMD_POS_NO = $arr_temp[0];
		$CMD_POSITION = $arr_temp[1];
//		$CMD_ORG3 = $data[CMD_ORG3];
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_PT_CODE = trim($data[PT_CODE]);
		$CMD_PM_CODE = trim($data[PM_CODE]);
		$SKILL_CODE = trim($data[SKILL_CODE]);
		$POS_CONDITION = trim($data[POS_CONDITION]);
		$CMD_CL_NAME = trim($data[CL_NAME]);
		$CL_NAME = str_replace("/", " หรือ ", $CMD_CL_NAME);
		$PER_CARDNO = trim($data[PER_CARDNO]);

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PT_NAME = trim($data2[PT_NAME]);
			
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PL_NAME = trim($data2[PL_NAME]);

		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE_NEW' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_PL_NAME = trim($data2[PL_NAME]);

		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PM_NAME = trim($data2[PM_NAME]);
		if (!$CMD_PM_NAME) $CMD_PM_NAME = $CMD_POSITION;

		$cmd = " select SKILL_NAME from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SKILL_NAME = trim($data2[SKILL_NAME]);
		if ($SKILL_NAME && $SKILL_NAME != 'ไม่มีสาขาชำนาญการ') $CMD_PM_NAME= "$CMD_PM_NAME($SKILL_NAME)";

		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$arr_temp = explode(" ", $LEVEL_NAME);
		$NEW_PT_NAME = $arr_temp[0];
		$NEW_PT_NAME = str_replace("ประเภท", "", $NEW_PT_NAME);
		$NEW_LEVEL_NAME = $arr_temp[1];
		if ($NEW_LEVEL_NAME != "ระดับต้น" && $NEW_LEVEL_NAME != "ระดับสูง") $NEW_LEVEL_NAME = str_replace("ระดับ", "", $NEW_LEVEL_NAME);

		$CMD_POSITION = (trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"");
		if ($CMD_PT_NAME=="ว" || $CMD_PT_NAME=="วช." || $CMD_PT_NAME=="ชช.")
			$CMD_POSITION = (trim($CMD_POSITION)?($CMD_POSITION . (($CMD_PT_NAME != "ทั่วไป" && $CMD_LEVEL >= "06")?" ". "$CMD_PT_NAME":"")):"");

		$PER_ID = $data[PER_ID];
		if ($PER_ID > 900000000) {
		} else {
			if($DPISDB=="odbc"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		(PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%' and
										a.EN_CODE=b.EN_CODE(+) and 
										a.EM_CODE=c.EM_CODE(+) 
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		(PER_EDUCATE a 
										left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
										) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%1%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";	
			} // end if
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EN_NAME1 = (trim($data2[EN_NAME])?trim($data2[EN_NAME]):"วุฒิที่ใช้บรรจุ");
			$EM_NAME1 = (trim($data2[EM_NAME])?trim($data2[EM_NAME]):"สาขาวิชาเอก");
			
			if($DPISDB=="odbc"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		(PER_EDUCATE a 
									left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
									) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%' and
										a.EN_CODE=b.EN_CODE(+) and 
										a.EM_CODE=c.EM_CODE(+) 
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select		b.EN_NAME, c.EM_NAME
						 from		(PER_EDUCATE a 
										left join PER_EDUCNAME b on (trim(a.EN_CODE)=trim(b.EN_CODE))
										) left join PER_EDUCMAJOR c on (trim(a.EM_CODE)=trim(c.EM_CODE))
						 where		a.PER_ID=$PER_ID and a.EDU_TYPE like '%2%'
						 order by		a.EDU_SEQ desc, a.EDU_ENDYEAR desc ";	
			} // end if
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$EN_NAME2 = (trim($data2[EN_NAME])?trim($data2[EN_NAME]):"วุฒิในตำแหน่งปัจจุบัน");
			$EM_NAME2 = (trim($data2[EM_NAME])?trim($data2[EM_NAME]):"สาขาวิชาเอก");
		}
			
		$POS_ID = $data[POS_ID];
		$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_POS_NO = trim($data2[POS_NO]);
		$NEW_ORG_NAME = trim($data2[ORG_NAME]);
		$NEW_PM_CODE = trim($data2[PM_CODE]);
		$NEW_PT_CODE = trim($data2[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$NEW_PT_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$NEW_PT_NAME = trim($data1[PT_NAME]);
		if ($CMD_PT_NAME=="ว") $CMD_PT_NAME="ทั่วไป";  
			
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$NEW_PM_NAME = trim($data1[PM_NAME]);
		if (!$NEW_PM_NAME) $NEW_PM_NAME = $NEW_PL_NAME;
		if ($SKILL_NAME && $SKILL_NAME != 'ไม่มีสาขาชำนาญการ') $NEW_PM_NAME= "$NEW_PM_NAME($SKILL_NAME)";
			
		$NEW_ORG_ID_1 = $data2[ORG_ID_1];
		$NEW_ORG_ID_2 = $data2[ORG_ID_2];

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
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		if ($PER_ID > 900000000 || $PER_STATUS == 2) {
			$arr_content[$data_count][per_name] = "ว่าง";
			$arr_content[$data_count][per_educate1] = "";
			$arr_content[$data_count][per_educate2] = "";
		} else {
			$arr_content[$data_count][per_name] = $PN_NAME . $PER_NAME . ' ' . $PER_SURNAME;
			$arr_content[$data_count][per_educate1] = '1.' . $EN_NAME1 . ' (' . $EM_NAME1 . ')';
			$arr_content[$data_count][per_educate2] = '2.' . $EN_NAME2 . ' (' . $EM_NAME2 . ')';
		}
		$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY?number_format($PER_MGTSALARY):"-";
		$arr_content[$data_count][cmd_level] = level_no_format($CMD_LEVEL);
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_pm_name] = $CMD_PM_NAME;
		$arr_content[$data_count][cmd_pt_name] = $CMD_PT_NAME;
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_pl_name] = $NEW_PL_NAME;
		$arr_content[$data_count][new_pt_name] = $NEW_PT_NAME;
		$arr_content[$data_count][new_pm_name] = $NEW_PM_NAME;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][pos_condition] = $POS_CONDITION;
		$arr_content[$data_count][per_cardno] = $PER_CARDNO;
		
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
//			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
//			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$PER_NAME = $arr_content[$data_count][per_name];
			$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
			$CMD_LEVEL = $arr_content[$data_count][cmd_level];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_PT_NAME = $arr_content[$data_count][cmd_pt_name];
			$CMD_PM_NAME = $arr_content[$data_count][cmd_pm_name];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			$NEW_PL_NAME = $arr_content[$data_count][new_pl_name];
			$NEW_PT_NAME = $arr_content[$data_count][new_pt_name];
			$NEW_PM_NAME = $arr_content[$data_count][new_pm_name];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_level_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			$CMD_DATE = $arr_content[$data_count][cmd_date];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$POS_CONDITION = $arr_content[$data_count][pos_condition];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_EDUCATE1 = $arr_content[$data_count][per_educate1];
			$PER_EDUCATE2 = $arr_content[$data_count][per_educate2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if ($ORG_NAME)				
					if($CONTENT_TYPE=="ORG"){
						$xlsRow++;
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 5, "$ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
//						$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
					}else{
						$xlsRow++;
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 5, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
//						$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
					$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 1, "$CMD_POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 3, card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 4, "$PER_EDUCATE1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 5, "$NEW_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 6, "$NEW_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 7, "$NEW_PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 8, "$NEW_LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 9, "$CMD_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write_string($xlsRow, 10, "$POS_CONDITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
//					$worksheet->write($xlsRow, 15, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					if ($PER_EDUCATE2) {
						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 4, "$PER_EDUCATE2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
						$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
//						$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
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
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
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
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
//		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งจัดคนลง.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งจัดคนลง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>