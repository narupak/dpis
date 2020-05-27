<?
	$time1 = time();
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
		
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";		
		$pos_no = "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";		
		$pos_no = "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";				
		$pos_no = "b.POEMS_NO";
	}elseif($search_per_type==4){ 
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
		$pos_no = "b.POT_NO";
	}

	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		elseif($list_type=="PER_COUNTRY" && trim($search_pv_code)!="") $RPTORD_LIST = "PROVINCE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("POSNO"); 

	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POSNO" :

				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(" . $pos_no . "), 0 , CLng(" . $pos_no . "))";
				if($DPISDB=="oci8") $order_by .= "to_number(replace(" . $pos_no . ",'-',''))";
				if($DPISDB=="mysql") $order_by .= $pos_no . "+0";

				$heading_name .= " เลขที่ตำแหน่ง";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = $pos_no;

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE=$search_per_type and a.PER_STATUS=1)";
	$list_type_text = $ALL_REPORT_TITLE;

	$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ระบบเครื่องราชย์ของสำนักเลขาธิการคณะรัฐมนตรี";
	$report_code = "rpt_soc";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 30);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 25);
		$worksheet->set_column(5, 5, 25);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 15);
		$worksheet->set_column(8, 8, 15);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 15);
		$worksheet->set_column(11, 11, 5);
		$worksheet->set_column(12, 12, 15);
		$worksheet->set_column(13, 13, 15);
		$worksheet->set_column(14, 14, 15);
		$worksheet->set_column(15, 15, 15);
		$worksheet->set_column(16, 16, 15);
		$worksheet->set_column(17, 17, 10);
		$worksheet->set_column(18, 18, 15);
		$worksheet->set_column(19, 19, 25);
		$worksheet->set_column(20, 20, 25);
		$worksheet->set_column(21, 21, 15);
		$worksheet->set_column(22, 22, 15);
		$worksheet->set_column(23, 23, 20);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "DEP_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ID_CARD", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "TIT_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "RANK_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "FNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "LNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "SEX", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "BIRTH_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 8, "BEG_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 9, "BEG_C", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 10, "BEG_POS_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 11, "CC", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 12, "BEG_C_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 13, "BEG_BEF_C_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 14, "SALARY", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 15, "SALARY5Y", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 16, "POS_AMT", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 17, "POS_CODE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 18, "POS_LEV", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 19, "BEG_POS_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 20, "POS_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 21, "L_INS_CODE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 22, "L_INS_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 23, "REMARK", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $POS_NO;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					if($POS_NO) $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO')";
					else $arr_addition_condition[] = "(trim(a.POS_NO) = '$POS_NO' or a.POS_NO is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $POS_NO;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "POSNO" :	
					$POS_NO = -1;
				break;
			} // end switch case
		} // end for
	} // function

	// ===== select data =====
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF 
						 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							$search_condition
				 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF
						 from			PER_PERSONAL a, $position_table b, PER_ORG c
						 where			$position_join and b.ORG_ID=c.ORG_ID(+) 
							$search_condition
				order by		$order_by   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF 
						 from			(
													PER_PERSONAL a
													left join $position_table b on ($position_join)
												) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
							$search_condition
				 order by		$order_by ";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
	$file_limit = 5000;
	$data_limit = 1000;
	$xlsRow = 0;
	$count_org_ref = 0;
	$sheet_no = 0; $sheet_no_text="";
	
	$arr_file = (array) null;
	$f_new = false;
	$fname= "../../Excel/tmp/rpt_soc_xls";
	$fname1 = $fname.".xls";
	$fnum = 0; $fnumtext="";
	$workbook = new writeexcel_workbook($fname1);

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.set_format.php";	// use set_format(set of parameter) funtion , help is in file
	require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
	//====================== SET FORMAT ======================//

	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];
			$REPORT_ORDER = "ORG_REF";
		} // end if

		// เช็คจบที่ข้อมูล $data_limit
		if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//			echo "$data_count>>$xls_fname>>$fname1<br>";
			$workbook->close();
			$arr_file[] = $fname1;
	
			$fnum++;
			$fname1=$fname."_$fnum.xls";
			$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
			require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

			$f_new = true;
		};
		// เช็คจบที่ข้อมูล $data_limit
		if($REPORT_ORDER == "ORG_REF" || ($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
			if ($f_new) {
				$sheet_no=0; $sheet_no_text="";
				if($data_count > 0) $count_org_ref++;
				$f_new = false;
//				echo "new file....$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
			} else if ($REPORT_ORDER == "ORG_REF") {
				$f_org_ref_skip = true;
				$sheet_no=0; $sheet_no_text="";
				if($data_count > 0) $count_org_ref++;
				$REPORT_ORDER = "";
//				echo "org_ref..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
			} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
				$sheet_no++;
				$sheet_no_text = "_$sheet_no";
//				echo "newsheet..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
			}

//			echo "report_code:$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
			$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
			
			//====================== SET FORMAT ======================//
//			require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
			//====================== SET FORMAT ======================//

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
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} // end for

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
				$worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			} // end if

			print_header();
		}

		// $REPORT_ORDER = "CONTENT";
			$data_row++;
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);	
								
			$PER_ID = $data[PER_ID];
			$PER_GENDER = $data[PER_GENDER];
			$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
			$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
			$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
			$STARTDATE = show_date_format($PER_STARTDATE,1);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];
			$PER_CARDNO = $data[PER_CARDNO];
			$TITLE_CODE = $data[TITLE_CODE];
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
				
			$RANK_NAME = "";
			$cmd = " select PN_NAME, RANK_FLAG from PER_PRENAME where PN_CODE='$TITLE_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];
			$RANK_FLAG = $data2[RANK_FLAG];
			if ($RANK_FLAG==1) {
				$RANK_NAME = $PN_NAME;
				$PN_NAME = "";
			}

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data2[LEVEL_NAME];

			// === หาเงินเดือนย้อนหลัง 5 ปี 
			$UPDATE_DATE = date("Y-m-d H:i:s");
			$tmp_date = explode("-", substr(trim($UPDATE_DATE), 0, 10));
			$DATE5Y = ($tmp_date[0]-5) ."-". $tmp_date[1] ."-". $tmp_date[2];
			$PER_SALARY5Y = "";
			$cmd = " select SAH_SALARY
							from   PER_SALARYHIS
							where PER_ID=$PER_ID and SAH_EFFECTIVEDATE < '$DATE5Y' 
							order by SAH_EFFECTIVEDATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PER_SALARY5Y = $data2[SAH_SALARY];
				
			// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
			$POH_EFFECTIVEDATE = "";
			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and LEVEL_NO = '$LEVEL_NO'
							order by POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			if ($POH_EFFECTIVEDATE) {	
				$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),1);
			}
				
			// === หาตำแหน่งที่บรรจุ
			$START_PL_NAME = "";
			$cmd = " select PL_CODE, LEVEL_NO
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=1
							order by POH_EFFECTIVEDATE";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$START_PL_CODE = trim($data2[PL_CODE]);
			$START_LEVEL_NO = trim($data2[LEVEL_NO]);

			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$START_PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$START_PL_NAME = $data2[PL_NAME];
				
			// === BEG_C_DATE
			$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE, LEVEL_NO
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and LEVEL_NO <= '11'
							group by LEVEL_NO, POH_EFFECTIVEDATE
							order by LEVEL_NO desc, MIN(POH_EFFECTIVEDATE) ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			//echo "<br>$cmd<br>";
			$BEG_LEVEL_NO = trim($data2[LEVEL_NO]);
			$BEG_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
			if ($BEG_C_DATE) {	
				$BEG_C_DATE = show_date_format(substr($BEG_C_DATE, 0, 10),1);
			}

			// === BEG_BEF_C_DATE
			$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE, LEVEL_NO
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and LEVEL_NO < '$BEG_LEVEL_NO'
							group by LEVEL_NO, POH_EFFECTIVEDATE
							order by LEVEL_NO desc, MIN(POH_EFFECTIVEDATE) ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			//echo "<br>$cmd<br>";
			$BEG_BEF_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
			if ($BEG_BEF_C_DATE) {	
				$BEG_BEF_C_DATE = show_date_format(substr($BEG_BEF_C_DATE, 0, 10),1);
			}

			// === หาเครื่องราชย์ชั้นสุดท้าย
			$DC_NAME = $DEH_DATE = "";
			$cmd = " select DC_SHORTNAME, DEH_DATE, DEH_POSITION, DEH_ORG
									from   PER_DECORATEHIS a, PER_DECORATION b
									where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_FLAG = 1 
							order by DEH_DATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DC_NAME = trim($data2[DC_SHORTNAME]);
			$DEH_DATE = trim($data2[DEH_DATE]);
			if ($DEH_DATE) {	
				$DEH_DATE = show_date_format(substr($DEH_DATE, 0, 10),1);
			}

			// === ชื่อเดิม
			$OLD_NAME = "";
			$cmd = " select NH_NAME, NH_SURNAME
							from   PER_NAMEHIS
							where PER_ID=$PER_ID 
							order by NH_DATE desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NH_NAME = trim($data2[NH_NAME]);
			$NH_SURNAME = trim($data2[NH_SURNAME]);
			$OLD_NAME = $NH_NAME . " " . $NH_SURNAME;
//			echo "(".($data_count+1).") $PER_CARDNO, $PER_NAME $PER_SURNAME<br>";
			
			$REPORT_ORDER = "CONTENT";
			$ORDER = $data_row;
			$START_LEVEL_NO = level_no_format($START_LEVEL_NO);
			$BEG_LEVEL_NO = level_no_format($BEG_LEVEL_NO);
			if ($search_per_type == 2)
				$LEVEL_NAME = $LEVEL_NAME;
			else
				$LEVEL_NAME = $LEVEL_NO;

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$RANK_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 4, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 5, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 6, "$PER_GENDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7, "$BIRTHDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 8, "$STARTDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 9, "$START_LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, "$POH_EFFECTIVEDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 11, "$BEG_LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 12, "$BEG_C_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 13, "$BEG_BEF_C_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 15, "$PER_SALARY5Y", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 16, "$PER_MGTSALARY", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 17, "$PL_CODE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 18, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 19, "$START_PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 20, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 21, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 22, "$DEH_DATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 23, "$REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

			$data_count++;
		//} // end if
	} // end while
//echo "<pre>"; echo count($arr_content); print_r($arr_content); echo "</pre>";

	$workbook->close();
	$arr_file[] = $fname1;
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?=$cssfileselected?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="stylesheets/calendar-blue.css" title="winter"/>
</head>
<?

//	echo "ทำต่อรายงานชุด 2<br>";

	include("rpt_soc_xls_ext.php");
?>