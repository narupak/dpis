<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "บัญชีจัดข้าราชการกรุงเทพมหานครสามัญเข้าประเภทตำแหน่ง สายงาน และระดับตำแหน่ง||ตามพระราชบัญญัติระเบียบข้าราชการกรุงเทพมหานครและบุคลากรกรุงเทพมหานคร  พ.ศ. 2554||$search_department_name บัญชี 2";
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

		$worksheet->set_column(0, 0, 6.29);
		$worksheet->set_column(1, 1, 7.71);
		$worksheet->set_column(2, 2, 24.57);
		$worksheet->set_column(3, 3, 18.57);
		$worksheet->set_column(4, 4, 27.29);
		$worksheet->set_column(5, 5, 18.57);
		$worksheet->set_column(6, 6, 24.57);
		$worksheet->set_column(7, 7, 11.57);
		$worksheet->set_column(8, 8, 13.14);
		$worksheet->set_column(9, 9, 18.57);
		$worksheet->set_column(10, 10, 24.57);
		$worksheet->set_column(11, 11, 11.57);
		$worksheet->set_column(12, 12, 13.14);
		$worksheet->set_column(13, 13, 11.57);
		$worksheet->set_column(14, 14, 27.29);
//		$worksheet->set_column(15, 15, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TRL", 1));
		$worksheet->write($xlsRow, 1, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 2, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 3, "ข้อมูลผู้ดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 4, " ", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 5, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 6, "ส่วนราชการและตำแหน่งที่ ก.ก. กำหนดไว้เดิม", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 7, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 8, " ", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 9, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 10, "ส่วนราชการและตำแหน่งที่ ก.ก. กำหนดใหม่", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 11, " ", set_format("xlsFmtTableHeader", "B", "C", "T", 1));
		$worksheet->write($xlsRow, 12, " ", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 13, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TR", 1));
		$worksheet->write($xlsRow, 14, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));
//		$worksheet->write($xlsRow, 15, "อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));

		set_format("xlsFmtTableHeader", "B", "C", "LRB", 0);
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "ชื่อ-นามสกุลผู้ดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "คุณวุฒิ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLB", 0));
		$worksheet->write($xlsRow, 6, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "ชื่อตำแหน่งในสายงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 12, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
//		$worksheet->write($xlsRow, 15, "เงินประจำตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function		

	if($DPISDB=="odbc"){
		$select_top = ($current_page==$total_page)?($count_data - ($data_per_page * ($current_page - 1))):$data_per_page;			
		$cmd = "	select	a.PL_CODE, a.POS_NO_NAME, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, d.ORG_NAME,
											a.LEVEL_NO as NEW_LEVEL_NO, a.PM_CODE, a.PT_CODE, a.SKILL_CODE, a.POS_CONDITION, a.CL_NAME, a.POS_SALARY, a.POS_ID
				from			(((PER_POSITION a 
							) left join PER_ORG d on (a.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (a.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (a.ORG_ID_2=f.ORG_ID)
				where		a.DEPARTMENT_ID = $search_department_id and a.POS_STATUS = 1
				order by 		IIf(IsNull(d.ORG_SEQ_NO), 0, d.ORG_SEQ_NO), d.ORG_CODE, 
							IIf(IsNull(e.ORG_SEQ_NO), 0, e.ORG_SEQ_NO), IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), 
							IIf(IsNull(f.ORG_SEQ_NO), 0, f.ORG_SEQ_NO), IIf(IsNull(f.ORG_CODE),  IIf(IsNull(e.ORG_CODE), d.ORG_CODE, e.ORG_CODE), f.ORG_CODE), 
							a.POS_NO_NAME, CLng(a.POS_NO) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select		a.PL_CODE, a.POS_NO_NAME, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, d.ORG_NAME,
											a.LEVEL_NO as NEW_LEVEL_NO, a.PM_CODE, a.PT_CODE, a.SKILL_CODE, a.POS_CONDITION, a.CL_NAME, a.POS_SALARY, a.POS_ID
				from			PER_POSITION a, PER_ORG d, PER_ORG e, PER_ORG f
				where		a.DEPARTMENT_ID = $search_department_id and
									a.ORG_ID=d.ORG_ID(+) and a.ORG_ID_1=e.ORG_ID(+) and a.ORG_ID_2=f.ORG_ID(+) and a.POS_STATUS = 1
				order by 		nvl(d.ORG_SEQ_NO,0), d.ORG_CODE, nvl(e.ORG_SEQ_NO,0), nvl(e.ORG_CODE,d.ORG_CODE), nvl(f.ORG_SEQ_NO,0), nvl(f.ORG_CODE,nvl(e.ORG_CODE,d.ORG_CODE)), a.POS_NO_NAME, to_number(replace(a.POS_NO,'-','')) ";	
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	a.PL_CODE, a.POS_NO_NAME, a.POS_NO, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, d.ORG_NAME,
											a.LEVEL_NO as NEW_LEVEL_NO, a.PM_CODE, a.PT_CODE, a.SKILL_CODE, a.POS_CONDITION, a.CL_NAME, a.POS_SALARY, a.POS_ID
				from			(((PER_POSITION a 
							) left join PER_ORG d on (a.ORG_ID=d.ORG_ID)
							) left join PER_ORG e on (a.ORG_ID_1=e.ORG_ID)
							) left join PER_ORG f on (a.ORG_ID_2=f.ORG_ID)
				where		a.DEPARTMENT_ID = $search_department_id and a.POS_STATUS = 1
				order by 		d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_SEQ_NO, e.ORG_CODE, f.ORG_SEQ_NO, f.ORG_CODE, a.POS_NO_NAME, a.POS_NO+0	";	
	} // end if	
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = $data_row = 0;
	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PL_CODE = trim($data[PL_CODE]);

		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_PT_CODE = trim($data[PT_CODE]);
		$CMD_PM_CODE = trim($data[PM_CODE]);
		$SKILL_CODE = trim($data[SKILL_CODE]);
		$POS_CONDITION = trim($data[POS_CONDITION]);
		$CL_NAME = trim($data[CL_NAME]);
		$CL_NAME = str_replace("/", " หรือ ", $CL_NAME);
		$POS_SALARY = $data[POS_SALARY];

		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CMD_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PT_NAME = trim($data2[PT_NAME]);
			
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$CMD_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PL_NAME = trim($data2[PL_NAME]);

		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
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

		$POS_ID = $data[POS_ID];
		$cmd = "  select		PER_ID, PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, PER_STATUS, PER_CARDNO, PER_MGTSALARY, PER_SALARY
							from		PER_PERSONAL 
							where	POS_ID=$POS_ID and PER_STATUS = 1 ";
		$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PER_ID = $data2[PER_ID];
		$PN_CODE = trim($data2[PN_CODE]);
		$PER_NAME = $data2[PER_NAME];
		$PER_SURNAME = $data2[PER_SURNAME];
		$PER_TYPE = $data2[PER_TYPE];
		$PER_STATUS = $data2[PER_STATUS];
		if ($PER_STATUS==2) $PER_NAME = "";
		$PER_MGTSALARY = $data2[PER_MGTSALARY];
		$PER_SALARY = $data2[PER_SALARY];
		$PER_CARDNO = trim($data2[PER_CARDNO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		

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
//		$EN_NAME1 = (trim($data2[EN_NAME])?trim($data2[EN_NAME]):"วุฒิที่ใช้บรรจุ");
//		$EM_NAME1 = (trim($data2[EM_NAME])?trim($data2[EM_NAME]):"สาขาวิชาเอก");
		$EN_NAME1 = trim($data2[EN_NAME]);
		$EM_NAME1 = trim($data2[EM_NAME]);
		
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
//		$EN_NAME2 = (trim($data2[EN_NAME])?trim($data2[EN_NAME]):"วุฒิในตำแหน่งปัจจุบัน");
//		$EM_NAME2 = (trim($data2[EM_NAME])?trim($data2[EM_NAME]):"สาขาวิชาเอก");
		$EN_NAME2 = trim($data2[EN_NAME]);
		$EM_NAME2 = trim($data2[EM_NAME]);
			
		$ORG_NAME = trim($data[ORG_NAME]);
		$NEW_PM_CODE = trim($data[PM_CODE]);
		if ($CMD_PT_NAME=="ว") $CMD_PT_NAME="ทั่วไป";  
			
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_PM_NAME = trim($data2[PM_NAME]);
		if ($SKILL_NAME && $SKILL_NAME != 'ไม่มีสาขาชำนาญการ') $NEW_PM_NAME= "$NEW_PM_NAME($SKILL_NAME)";
			
		if($ORG_ID != trim($data[ORG_ID])){
			$ORG_ID = trim($data[ORG_ID]);

			if ($ORG_NAME!="-") {
				$arr_content[$data_count][type] = "ORG";
				$arr_content[$data_count][org_name] = $ORG_NAME;
				$arr_content[$data_count][new_org_name] = $ORG_NAME;

				$data_count++;
			}
		} // end if
		
		if($ORG_ID_1 != trim($data[ORG_ID_1])){
			$ORG_ID_1 = trim($data[ORG_ID_1]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];
		
			if ($ORG_NAME_1!="-") {
				$arr_content[$data_count][type] = "ORG_1";
				$arr_content[$data_count][org_name] = $ORG_NAME_1;
				$arr_content[$data_count][new_org_name] = $ORG_NAME_1;

				$data_count++;
			}
		} // end if
		
		if($ORG_ID_2 != trim($data[ORG_ID_2])){
			$ORG_ID_2 = trim($data[ORG_ID_2]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data2[ORG_NAME];

			if ($ORG_NAME_2!="-") {
				$arr_content[$data_count][type] = "ORG_2";
				$arr_content[$data_count][org_name] = $ORG_NAME_2;
				$arr_content[$data_count][new_org_name] = $ORG_NAME_2;

				$data_count++;
			}
		} // end if
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][per_educate1] = "";
		$arr_content[$data_count][per_educate2] = "";
		if (!$PER_NAME) {
			$arr_content[$data_count][per_name] = "ว่าง";
		} else {
			$arr_content[$data_count][per_name] = $PN_NAME . $PER_NAME . ' ' . $PER_SURNAME;
			if ($EN_NAME1) $arr_content[$data_count][per_educate1] = $EN_NAME1;
			if ($EM_NAME1) $arr_content[$data_count][per_educate1] .= ' (' . $EM_NAME1 . ')';
			if ($EN_NAME2) 
				if ($EN_NAME1) 
					$arr_content[$data_count][per_educate2] = $EN_NAME2;
				else
					$arr_content[$data_count][per_educate1] = $EN_NAME2;
			if ($EM_NAME2) 
				if ($EN_NAME1) 
					$arr_content[$data_count][per_educate2] .= ' (' . $EM_NAME2 . ')';
				else
					$arr_content[$data_count][per_educate1] .= ' (' . $EM_NAME2 . ')';
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
		$arr_content[$data_count][new_level_name] = $CL_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][per_salary] = $PER_SALARY?number_format($PER_SALARY):number_format($POS_SALARY);
		
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
//			$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$POS_NO = $arr_content[$data_count][pos_no];
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
			$PER_SALARY = $arr_content[$data_count][per_salary];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			$POS_CONDITION = $arr_content[$data_count][pos_condition];
			$PER_CARDNO = $arr_content[$data_count][per_cardno];
			$PER_EDUCATE1 = $arr_content[$data_count][per_educate1];
			$PER_EDUCATE2 = $arr_content[$data_count][per_educate2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){
				$ORG_NAME = $arr_content[$data_count][org_name];
				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];

				if ($ORG_NAME) {				
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
						$worksheet->write($xlsRow, 9, "$ORG_NAME", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "B", "L", "TLRB", 0));
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
						$worksheet->write($xlsRow, 9, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
//						$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					} // end if
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
					$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//					$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
				}else{
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$ORDER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 1, "$POS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 3,card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 4, "$PER_EDUCATE1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 5, "$CMD_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 6, "$CMD_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 7, "$CMD_PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 8, "$CMD_LEVEL", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
					$worksheet->write_string($xlsRow, 9, "$NEW_PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 10, "$NEW_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 11, "$NEW_PT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 12, "$NEW_LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write($xlsRow, 13, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
					$worksheet->write_string($xlsRow, 14, "$POS_CONDITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
						$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
						$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
						$worksheet->write_string($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
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
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
//		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"บัญชี 2.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชี 2.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>