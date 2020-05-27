<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_history_name = explode("|", $HISTORY_LIST);

	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(a.POS_ID), 0, CLng(a.POS_ID))";
				elseif($DPISDB=="oci8") $order_by .= "TO_NUMBER(a.POS_ID)";
				elseif($DPISDB=="mysql") $order_by .= "a.POS_ID+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE, c.PN_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1, c.ORG_ID_1";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2, c.ORG_ID_2";
				break;
		} // end switch case
	} // end for

	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select DEPARTMENT_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select MINISTRY_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[MINISTRY_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	//หาประเภทข้าราชการ
	$arr_search_condition[] = "(a.LEVEL_NO = '$LEVEL_NO')";

	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = $data[LEVEL_NAME];

	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
	if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
	if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
	
	//$temp_date = explode("/", trim($SALARY_AT_DATE));
	//$SALARY_AT_DATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 

	$arr_search_condition[] = "(a.POS_ID is not null)";
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	$report_title = "$DEPARTMENT_NAME||ข้อมูลข้าราชการ" . $LEVEL_NAME;
	$report_code = "R040981";

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
		global $heading_name;
		
		$worksheet->set_column(0, 0, 7);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 13);
		$worksheet->set_column(5, 5, 13);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10, 12);
		$worksheet->set_column(11, 11, 16);
		$worksheet->set_column(12, 12, 30);
		$worksheet->set_column(13, 13, 30);
		$worksheet->set_column(14, 14, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ส่วนราชการและตำแหน่งที่กำหนดใหม่", set_format("xlsFmtTableHeader", "B", "C", "TLB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TRB", 1));
		$worksheet->write($xlsRow, 6, "วันเลื่อนระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "วันบรรจุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "วันเกิด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "เกษียณ พ.ศ.", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "ดำรงตำแหน่งปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "วุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLB", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableHeader", "B", "C", "TRB", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่งในการบริหารงาน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "ประเภทตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 12, "ปริญญาตรี", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "ปริญญาโท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "ปริญญาเอก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		
	} // function	

		// ข้าราชการ
	if($DPISDB=="odbc"){
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, LEFT(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,LEFT(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
						 from		(	
											(
												(
													(
														(
															(
																(	
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
										) left join PER_MGT k on (b.PM_CODE = k.PM_CODE)
									)  left join PER_SALARYHIS n on (a.PER_ID = n.PER_ID)
											$search_condition
						 order by		$order_by
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, substr(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,substr(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
		from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i, PER_POSITIONHIS j, PER_MGT k, PER_SALARYHIS n
		where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+) and b.PM_CODE = k.PM_CODE (+) and a.PER_ID = n.PER_ID (+) $search_condition
		group by a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10) , SUBSTR(trim(a.PER_RETIREDATE), 1, 4), k.PM_NAME, h.PT_NAME, a.PER_SALARY
		order by		$order_by";
	}elseif($DPISDB=="mysql"){
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, LEFT(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,LEFT(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
						 from		(	
											(
												(
													(
														(
															(
																(	
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
										) left join PER_MGT k on (b.PM_CODE = k.PM_CODE)
									)  left join PER_SALARYHIS n on (a.PER_ID = n.PER_ID)
											$search_condition
						 order by		$order_by
					   ";
	}

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$PM_NAME = $data[PM_NAME];
		$LEVEL_NAME1 = $data[LEVEL_NAME1];
		$LEVEL_NAME2 = $data[LEVEL_NAME2];
		
		if($DPISDB=="odbc"){

		}elseif($DPISDB=="oci8"){
			$cmd = "select MIN(SUBSTR(trim(b.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE  from PER_PERSONAL a, PER_POSITIONHIS b where a.PER_ID = b.PER_ID and a.PER_ID = '$PER_ID' and b.LEVEL_NO = '$LEVEL_NO'";
		}elseif($DPISDB=="mysql"){

		}

		//echo $cmd . "<br>";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$UPTO_DATE = $data2[POH_EFFECTIVEDATE];
		$UPTO_DATE_SALARY = $data2[POH_EFFECTIVEDATE];
		$UPTO_DATE = show_date_format($UPTO_DATE,$DATE_DISPLAY);
		if ($UPTO_DATE=="0  543") $UPTO_DATE="";
		
		if($DPISDB=="odbc"){

		}elseif($DPISDB=="oci8"){
			$cmd = "select MAX(b.SAH_SALARY) as SAH_SALARY from PER_PERSONAL a, PER_SALARYHIS b where a.PER_ID = b.PER_ID and a.PER_ID = $PER_ID";
			//$cmd = "select b.SAH_SALARY from PER_PERSONAL a, PER_SALARYHIS b where a.PER_ID = b.PER_ID and a.PER_ID = $PER_ID and SUBSTR(trim(b.SAH_EFFECTIVEDATE), 1, 10) = '$UPTO_DATE_SALARY'";
		}elseif($DPISDB=="mysql"){

		}
		
		//$db_dpis2->send_cmd($cmd);
		//$data2 = $db_dpis2->get_array();
		//$SALARY = $data2[SAH_SALARY];
		//if ($SALARY=="0.00" || $SALARY=="") $SALARY=$data["PER_SALARY"];
		$SALARY=$data["PER_SALARY"];

		$PT_CODE = trim($data[PT_CODE]);
		$ORG_SHORT = $data[ORG_SHORT];
		$ORG_NAME = $data[ORG_NAME];

		$BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$BIRTHDATE = show_date_format($BIRTHDATE,$DATE_DISPLAY);
		if ($BIRTHDATE=="0  543") $BIRTHDATE="";

		$STARTDATE = trim($data[PER_STARTDATE]);
		$STARTDATE = show_date_format($STARTDATE,$DATE_DISPLAY);
		if ($STARTDATE=="0  543") $STARTDATE="";

		$RETIREYEAR= trim($data[PER_RETIREDATE]);
		$arr_temp = explode("-", $RETIREYEAR);
		$RETIREYEAR = ($arr_temp[0] + 543);
		
		$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
		$POH_EFFECTIVEDATE = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);
		if ($POH_EFFECTIVEDATE=="0  543") $POH_EFFECTIVEDATE="";
		
		//ป.ตรี
		$EDUCATE40="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '40' or trim(b.el_code) = '001') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE40!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE40 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE40 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE40 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE40 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		//ป.โท
		$EDUCATE60="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '60' or trim(b.el_code) = '002') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE60!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE60 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE60 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE60 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE60 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		//ป.เอก
		$EDUCATE80="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '80' or trim(b.el_code) = '003') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE80!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE80 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE80 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE80 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE80 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME;
		//$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
		$arr_content[$data_count][pmname] = $PM_NAME;
		$arr_content[$data_count][level1] = str_replace("ประเภท","",$LEVEL_NAME1);
		$arr_content[$data_count][level2] = str_replace("ระดับ","",$LEVEL_NAME2);
		$arr_content[$data_count][uptodate] = $UPTO_DATE;
		$arr_content[$data_count][salary] = number_format($SALARY);
		$arr_content[$data_count][startdate] = $STARTDATE;
		$arr_content[$data_count][birthdate] = $BIRTHDATE;
		$arr_content[$data_count][retireyear] = $RETIREYEAR;
		$arr_content[$data_count][poheffectivedate] = $POH_EFFECTIVEDATE;
		$arr_content[$data_count][educate1] = $EDUCATE40;
		$arr_content[$data_count][educate2] = $EDUCATE60;
		$arr_content[$data_count][educate3] = $EDUCATE80;

		$data_count++;
	} // end while
	
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
	} // end if

	if($count_data){
		$xlsRow++;
		
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][pmname];
			$NAME_4 = $arr_content[$data_count][level1];
			$NAME_5 = $arr_content[$data_count][level2];
			$NAME_6 = $arr_content[$data_count][uptodate];
			$NAME_7 = $arr_content[$data_count][salary];
			$NAME_8 = $arr_content[$data_count][startdate];
			$NAME_9 = $arr_content[$data_count][birthdate];
			$NAME_10 = $arr_content[$data_count][retireyear];
			$NAME_11 = $arr_content[$data_count][poheffectivedate];
			$NAME_12 = $arr_content[$data_count][educate1];
			$NAME_13 = $arr_content[$data_count][educate2];
			$NAME_14 = $arr_content[$data_count][educate3];

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$ORDER ", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$NAME_1 ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$NAME_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$NAME_4", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$NAME_5 ", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$NAME_6", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$NAME_7", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$NAME_8", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$NAME_9 ", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$NAME_10", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$NAME_11", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, "$NAME_12", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, "$NAME_13", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, "$NAME_14", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

		} // end for
		
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
	} // end if
	

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