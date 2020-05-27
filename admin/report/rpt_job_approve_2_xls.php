<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	//echo($JOB_EVA_ID);
	$ARR_JOB_EVA_ID = explode(",", $JOB_EVA_ID);
	for($i=0;$i<count($ARR_JOB_EVA_ID);$i++){
		$cmd = " select POS_ID from JOB_EVALUATION where JOB_EVA_ID = '".$ARR_JOB_EVA_ID[$i]."' ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
		$data = $db_dpis->get_array();	
		$ARR_POS_ID[] = trim($data[POS_ID]);
		}
	//$ARR_POS_ID = explode(",", $POS_ID);
	//print_r($ARR_POS_ID);
	$cmd = " select		PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
									ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
									ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8
					 from		JOB_ANALYSIS
					 where	JOB_EVA_ID=$JOB_EVA_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$PIORITY = trim($data[PIORITY]);
	$WORKLOAD = trim($data[WORKLOAD]);
	$KNOWLEDGE = trim($data[KNOWLEDGE]);
	$SKILL = trim($data[SKILL]);
	$EXP = trim($data[EXP]);
	$COMPETENCY = trim($data[COMPETENCY]);
	$ACCOUNTABILITY1 = trim($data[ACCOUNTABILITY1]);
	$ACCOUNTABILITY2 = trim($data[ACCOUNTABILITY2]);
	$ACCOUNTABILITY3 = trim($data[ACCOUNTABILITY3]);
	$ACCOUNTABILITY4 = trim($data[ACCOUNTABILITY4]);
	$ACCOUNTABILITY5 = trim($data[ACCOUNTABILITY5]);
	$ACCOUNTABILITY6 = trim($data[ACCOUNTABILITY6]);
	$ACCOUNTABILITY7 = trim($data[ACCOUNTABILITY7]);
	$ACCOUNTABILITY8 = trim($data[ACCOUNTABILITY8]);
	
	$cmd = " select 	ISPASSED_FIRST, REASON_FIRST, APPROVE_FIRST_BY, APPROVE_FIRST_TIME,
									ISPASSED_SECOND, REASON_SECOND, APPROVE_SECOND_BY, APPROVE_SECOND_TIME
					 from 		JOB_EVALUATION_APPROVED 
					 where 	JOB_EVA_ID=$JOB_EVA_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$ISPASSED_FIRST = trim($data[ISPASSED_FIRST]);
	$REASON_FIRST = trim($data[REASON_FIRST]);
	$APPROVE_FIRST_BY = trim($data[APPROVE_FIRST_BY]);
	$APPROVE_FIRST_TIME = trim($data[APPROVE_FIRST_TIME]);

	$ISPASSED_SECOND = trim($data[ISPASSED_SECOND]);
	$REASON_SECOND = trim($data[REASON_SECOND]);
	$APPROVE_SECOND_BY = trim($data[APPROVE_SECOND_BY]);
	$APPROVE_SECOND_TIME = trim($data[APPROVE_SECOND_TIME]);

	$company_name = "";
	$report_title = "";
	$report_code = "ผลการประเมินค่างาน";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format or set_format_new funtion , help is in file
	//====================== SET FORMAT ======================//

	$worksheet = &$workbook->addworksheet("ข้อมูลตำแหน่งและบทวิเคราะห์งาน");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);

	$worksheet->set_column(0, 0, 8);
	$worksheet->set_column(1, 1, 12);
	$worksheet->set_column(2, 2, 12);	
	$worksheet->set_column(3, 3, 12);	
	$worksheet->set_column(4, 4, 12);	
	$worksheet->set_column(5, 5, 12);	
	$worksheet->set_column(6, 6, 12);	
	$worksheet->set_column(7, 7, 12);	
	$worksheet->set_column(8, 8, 12);	
	$worksheet->set_column(9, 9, 12);	
	$worksheet->set_column(10, 10, 12);	
	$worksheet->set_column(11, 11, 12);	
	$worksheet->set_column(12, 12, 12);	
	$worksheet->set_column(13, 13, 25);	

	$xlsRow = 0;
	$worksheet->set_row($xlsRow, 20);
	$worksheet->write($xlsRow, 0, "ตารางแสดงตำแหน่งงานที่ต้องการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);
//--------------------------------------------------------head page--------------------------------------------------------
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "เลขที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "ตำแหน่งใน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "ตำแหน่งใน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "ประเภท /", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ประเภท /", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "ชื่อหน่วยงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 6, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 8, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 9, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 5, $xlsRow, 9);
	$worksheet->write($xlsRow, 10, "ที่ตั้ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 12, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 10, $xlsRow, 12);
	$worksheet->write($xlsRow, 13, "หน้าที่รับผิดชอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "การบริหาร", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "สายงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "ระดับตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ระดับตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 6, "ส่วนราชการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 7, "$ORG_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 8, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 9, "ต่ำกว่ากอง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 10, "อำเภอ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 11, "จังหวัด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 12, "ประเทศ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 13, "ของหน่วยงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LR&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "(เดิม)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "(ใหม่)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "ระดับ$MINISTRY_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 6, "ระดับ$DEPARTMENT_TITLE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 7, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 8, "1 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 9, "2 ระดับ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 10, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 11, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 12, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 13, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=LRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
//--------------------------------------------------------head page--------------------------------------------------------
//--------------------------------------------------------bady page--------------------------------------------------------
for($i=0;$i<count($ARR_POS_ID);$i++){
	if($DPISDB=="odbc"){
		$cmd =" select 		a.POS_ID, a.POS_NO, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.LEVEL_NO, 
										d.ORG_NAME as MINISTRY_NAME, c.ORG_NAME as DEPARTMENT_NAME, 
										b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS,
										b.AP_CODE, b.PV_CODE, b.CT_CODE, b.ORG_JOB
						from 		PER_POSITION a
										inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
										inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
						where 	a.POS_ID=$ARR_POS_ID[$i]
						order by d.ORG_ID, c.ORG_ID, b.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, cLng(a.POS_NO) ";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	a.POS_ID, a.POS_NO, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.LEVEL_NO, 
										d.ORG_NAME as MINISTRY_NAME, c.ORG_NAME as DEPARTMENT_NAME, 
										b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS,
										b.AP_CODE, b.PV_CODE, b.CT_CODE, b.ORG_JOB
						 from 		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d
						 where 	a.POS_ID=$ARR_POS_ID[$i] and 
						 				a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID and c.ORG_ID_REF=d.ORG_ID
						 order by d.ORG_ID, c.ORG_ID, b.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, to_number(replace(a.POS_NO,'-','')) ";
	}elseif($DPISDB=="mssql"){
		$cmd =" select 		a.POS_ID, a.POS_NO, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.LEVEL_NO, 
										d.ORG_NAME as MINISTRY_NAME, c.ORG_NAME as DEPARTMENT_NAME, 
										b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS,
										b.AP_CODE, b.PV_CODE, b.CT_CODE, b.ORG_JOB
						from 		PER_POSITION a
										inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
										inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
										inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
						where 	a.POS_ID=$ARR_POS_ID[$i]
						order by d.ORG_ID, c.ORG_ID, b.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.POS_NO + 0 ";
	} // end if
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//exit;
	$data = $db_dpis->get_array();
	$POS_NO = trim($data[POS_NO]);
	$ARR_POS_NO[] = trim($data[POS_NO]);
	$PL_CODE = trim($data[PL_CODE]);
	$CL_NAME = trim($data[CL_NAME]);
	$PM_CODE = trim($data[PM_CODE]);
	$LEVEL_NO = trim($data[LEVEL_NO]);

	$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$PL_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$PL_NAME = $data_dpis2[PL_NAME];

	$cmd = " select PM_NAME from PER_MGT where PM_CODE='".$PM_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$PM_NAME = $data_dpis2[PM_NAME];
	
	if($LEVEL_NO == "NOT SPECIFY" || !$LEVEL_NO){
		$LEVEL_NAME = "ยังไม่มีการกำหนดตำแหน่งงานใหม่";
	}elseif($LEVEL_NO){
		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
	} //if($LEVEL_NO == "NOT SPECIFY" || !$LEVEL_NO){

	$MINISTRY_NAME = trim($data[MINISTRY_NAME]);
	$DEPARTMENT_NAME = trim($data[DEPARTMENT_NAME]);
	$ORG_NAME = trim($data[ORG_NAME]);
	$ORG_ID_1 = trim($data[ORG_ID_1]);
	$ORG_ID_2 = trim($data[ORG_ID_2]);
	$AP_CODE = trim($data[AP_CODE]);
	$PV_CODE = trim($data[PV_CODE]);
	$CT_CODE = trim($data[CT_CODE]);
	$ORG_JOB = trim($data[ORG_JOB]);

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$ORG_NAME_1 = $data_dpis2[ORG_NAME];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$ORG_NAME_2 = $data_dpis2[ORG_NAME];
	
	$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='".$AP_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$AP_NAME = $data_dpis2[AP_NAME];

	$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='".$PV_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$PV_NAME = $data_dpis2[PV_NAME];

	$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='".$CT_CODE."' ";
	$db_dpis2->send_cmd($cmd);
	$data_dpis2 = $db_dpis2->get_array();
	$CT_NAME = $data_dpis2[CT_NAME];
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$POS_NO", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$PM_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "$PL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "$CL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "$LEVEL_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "$MINISTRY_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 6, "$DEPARTMENT_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 7, "$ORG_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 8, "$ORG_NAME_1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 9, "$ORG_NAME_2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 10, "$AP_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 11, "$PV_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 12, "$CT_NAME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 13, "$ORG_JOB", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	}//for($i=0;$i<count($ARR_POS_ID);$i++){
//--------------------------------------------------------bady page--------------------------------------------------------	
	
//----------------------------new sheet------------------------------------

	$cmd = " select SCORE, CONSISTENCY, ISPASSED, TESTER_ID, TEST_TIME from JOB_EVALUATION where JOB_EVA_ID=$JOB_EVA_ID  ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$SCORE = trim($data[SCORE]);
	$CONSISTENCY = trim($data[CONSISTENCY]);
	$EVALUATE_RESULT = trim($data[ISPASSED]);
	$TESTER_ID = trim($data[TESTER_ID]);
	$TEST_TIME = trim($data[TEST_TIME]);	
	
	$ARR_SCORE = explode(",", $SCORE);
	$KH1 = $ARR_SCORE[0];
	$KH2 = $ARR_SCORE[1];
	$KH3 = $ARR_SCORE[2];
	$KH_SCORE = $ARR_SCORE[3];
	
	$PS1 = $ARR_SCORE[4];
	$PS2 = $ARR_SCORE[5];
	$PS_SCORE = $ARR_SCORE[6];
	$PS_KH_SCORE = $ARR_SCORE[7];
	
	$ACC1 = $ARR_SCORE[8];
	$ACC2 = $ARR_SCORE[9];
	$ACC3 = $ARR_SCORE[10];
	$ACC_SCORE = $ARR_SCORE[11];
	
	$TOTAL_POINTS = $ARR_SCORE[12];
	$PROFILE_CHECK = $ARR_SCORE[13];
	$EVALUATE_LEVEL_NO = $ARR_SCORE[14];
	//echo($EVALUATE_LEVEL_NO);
	
	$ARR_CONSISTENCY = explode(",", $CONSISTENCY);
	$KH1_CONSISTENCY = $ARR_CONSISTENCY[0];
	$KH2_CONSISTENCY = $ARR_CONSISTENCY[1];
	$KH3_CONSISTENCY = $ARR_CONSISTENCY[2];
	$KH_CONSISTENCY = $ARR_CONSISTENCY[3];	

	$PS1_CONSISTENCY = $ARR_CONSISTENCY[4];
	$PS2_CONSISTENCY = $ARR_CONSISTENCY[5];
	$PS_CONSISTENCY = $ARR_CONSISTENCY[6];	
	
	$ACC1_CONSISTENCY = $ARR_CONSISTENCY[7];
	$ACC_CONSISTENCY = $ARR_CONSISTENCY[8];

	$PC_CONSISTENCY = $ARR_CONSISTENCY[9];
	
	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$EVALUATE_LEVEL_NO' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$EVALUATE_LEVEL_NAME = $data2[LEVEL_NAME];
	
	$cmd = " select MINIMUM,MAXIMUM from JEM_GRADE where NAME ='$EVALUATE_LEVEL_NO' ";
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MINIMUM = $data2[MINIMUM];
	$MAXIMUM = $data2[MAXIMUM];
	
	$cmd = " select 		a.QUESTION_ID, b.TOPIC, c.ANSWER_INFO, a.DESCRIPTION 
					from 		JOB_EVALUATION_ANSWER_HISTORY a, JEM_QUESTION_INFO b, JEM_ANSWER_INFO c
					where 		a.JOB_EVA_ID=$JOB_EVA_ID and a.QUESTION_ID=b.ID 
					 				and a.QUESTION_ID=c.QUESTION_NO and a.ANSWER_ID=c.ID
					order by 	b.ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_QUESTION_TOPIC[$data[QUESTION_ID]] = trim($data[TOPIC]);
		$ARR_ANSWER_INFO[$data[QUESTION_ID]] = trim($data[ANSWER_INFO]);
		$ARR_ANSWER_DESCRIPTION[$data[QUESTION_ID]] = trim($data[DESCRIPTION]);
	} // loop while
/*
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "บทวิเคราะห์งานที่ต้องการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=red&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความสำคัญของงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$PIORITY", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ภาระงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$WORKLOAD", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความรับผิดชอบหลักของงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	for($i=1; $i<=8; $i++){
		$xlsRow++;
		$worksheet->write($xlsRow, 0, ($i.". ".${"ACCOUNTABILITY".$i}), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);
	} // loop for

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความรู้ที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$KNOWLEDGE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ทักษะที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$SKILL", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ประสบการณ์ที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$EXP", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "สมรรถนะที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "$COMPETENCY", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 9);
*/
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 13);

	$worksheet = &$workbook->addworksheet("ผลการประเมินค่างาน");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	$worksheet->set_column(0, 0, 30);
	$worksheet->set_column(1, 1, 7);
	$worksheet->set_column(2, 2, 30);	
	$worksheet->set_column(3, 3, 7);	
	$worksheet->set_column(4, 4, 30);	
	$worksheet->set_column(5, 5, 7);	

	$xlsRow = 0;
	$worksheet->set_row($xlsRow, 20);
	$worksheet->write($xlsRow, 0, "ตารางแสดงผลการประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);

	$xlsRow++;
	for($i=0; $i<=5; $i++){
		$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // loop for
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ตำแหน่งเลขที่", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, implode(",",$ARR_POS_NO), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ประเภท/ระดับตำแหน่ง (ใหม่)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$LEVEL_NAME($LEVEL_NO)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
	
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ประเมินเป็น", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$EVALUATE_LEVEL_NAME($EVALUATE_LEVEL_NO)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);
	
	$xlsRow++;
	for($i=0; $i<=5; $i++){
		$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // loop for
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความรู้ที่จำเป็นในงาน", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
	$worksheet->write($xlsRow, 2, "ความสามารถในการแก้ปัญหา", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 2, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, "ภาระรับผิดชอบ", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);
/*
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "(Know - how)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
	$worksheet->write($xlsRow, 2, "(Problem Solving)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 2, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, "(Accountability)", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);
*/
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความรู้และความชำนาญ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$KH1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "กรอบของอำนาจและอิสระในการคิด", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "$PS1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "อิสระในการปฏิบัติงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "$ACC1", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "การบริหารจัดการ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$KH2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "ความท้าทายในการคิดแก้ปัญหา", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "$PS2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ผลกระทบจากการปฏิบัติงาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "$ACC2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "การสื่อสารและปฏิสัมพันธ์", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$KH3", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "ร้อยละของความรู้ (%)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "$PS_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ลักษณะงานที่ปฏิบัติของตำแหน่ง", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "$ACC3", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "$KH_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "$PS_KH_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "$ACC_SCORE", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ความสอดคล้องขององค์ประกอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, (($KH_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "ความสอดคล้องขององค์ประกอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, (($PS_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ความสอดคล้องขององค์ประกอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, (($ACC_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
/*
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	$worksheet->merge_cells(($xlsRow - 1), 1, $xlsRow, 1);
	$worksheet->write($xlsRow, 2, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	$worksheet->merge_cells(($xlsRow - 1), 3, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, "(Consistency)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=M&fontSize=&wrapText=1"));
	$worksheet->merge_cells(($xlsRow - 1), 5, $xlsRow, 5);
*/
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "คะแนนรวม  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, "$TOTAL_POINTS", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "การตรวจสอบวัตถุประสงค์หลักของงานของตำแหน่ง  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, ""/*"$PROFILE_CHECK"*/, set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, (($PC_CONSISTENCY=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=42&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "สรุปผลการประเมิน  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, (($EVALUATE_RESULT=="Y")?"ผ่าน":"ไม่ผ่าน"), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);

$xlsRow++;
	$worksheet->write($xlsRow, 0, "ช่วงคะแนนของตำแหน่ง  ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 3);
	$worksheet->write($xlsRow, 4, "$MINIMUM - $MAXIMUM คะแนน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->merge_cells($xlsRow, 4, $xlsRow, 5);

	$xlsRow++;
	for($i=0; $i<=5; $i++){
		$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // loop for
	$worksheet->merge_cells($xlsRow, 0, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผลสรุปการประเมิน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	for($i=1; $i<=5; $i++){
		$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=B&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // loop for
	$worksheet->merge_cells($xlsRow, 1, $xlsRow, 5);

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "ผู้ทดสอบและวันที่ประเมินค่างาน", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "ผู้ทดสอบและวันที่ประเมินครั้งแรก", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "ผู้ทดสอบและวันที่ประเมินครั้งที่ 2", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "$APPROVE_FIRST_NAME $APPROVE_FIRST_TIME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "$APPROVE_SECOND_NAME $APPROVE_SECOND_TIME", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "เหตุผลในการอนุมัติ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "เหตุผลในการอนุมัติ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 1, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 2, "$REASON_FIRST", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 3, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 4, "$REASON_SECOND", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));
	$worksheet->write($xlsRow, 5, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=&fontSize=&wrapText=1"));



	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);

?>