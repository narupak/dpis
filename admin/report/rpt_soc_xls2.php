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
                $PERSONAL_TYPE='ข้าราชการ';
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";		
		$pos_no = "b.POEM_NO";
                $PERSONAL_TYPE='ลูกจ้างประจำ';
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";				
		$pos_no = "b.POEMS_NO";
                $PERSONAL_TYPE='พนักงานราชการ';
	}elseif($search_per_type==4){ 
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";
		$pos_no = "b.POT_NO";
                $PERSONAL_TYPE='ลูกจ้างชั่วคราว';
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
        $show_have_org = '';
        if($have_org){
            $show_have_org = $have_org;
            $order_org = 'b.ORG_ID ,';
        }
        //die($show_have_org.'xx');
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
	$report_code = "CurrentData";

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name ,$show_have_org;
		
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
		$worksheet->set_column(23, 23, 40);
                
                $worksheet->set_column(24, 24, 30);
                $worksheet->set_column(25, 25, 30);
                $worksheet->set_column(26, 26, 30);
                $worksheet->set_column(27, 27, 30);
                $worksheet->set_column(28, 28, 30);
                $worksheet->set_column(29, 29, 30);
                if($show_have_org){$worksheet->set_column(30, 30, 40);}
            
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
                
                $worksheet->write($xlsRow, 24, "RESIGN_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 25, "RETURN_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 26, "OLDF_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 27, "OLDL_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 28, "STATUS_EXP", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 29, "PERSONAL_TYPE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                if($show_have_org){$worksheet->write($xlsRow, 30, "ORG_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));}
                
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
		$cmd = " select	 	a.PER_ID, PER_GENDER, PER_BIRTHDATE, PER_STARTDATE, a.LEVEL_NO,
									PER_SALARY,PER_REMARK, PER_MGTSALARY, PER_CARDNO, a.PN_CODE as TITLE_CODE, PER_NAME, PER_SURNAME, $line_code as PL_CODE, ORG_ID_REF ,
                                                                        b.ORG_ID
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
	//echo "<pre>$cmd<br>";
        //die();
//	$db_dpis->show_error();

	$fname= "../../Excel/tmp/rpt_soc_xls2";
	$fname1=$fname.".xls";
	$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
			require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

	$file_limit = 5000;
	$data_limit = 1000;
	$f_new = true;
	$data_count = $data_row = 0;
	initialize_parameter(0);
	$ORG_ID_REF = -1;
        $ORG_ID = -1;
        $CHECK_DATE = date("Y-m-d");
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];
			$REPORT_ORDER = "ORG_REF";
		} // end if
                if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];
		} // end if
		// เช็คจบที่ข้อมูล $data_limit
		if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//			echo "1..data_count=$data_count>>file_limit=$file_limit<br>";
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
//		if($REPORT_ORDER == "ORG_REF" || ($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
		if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
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

			$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
			
			//====================== SET FORMAT ======================//
			require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
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
                                
                                $worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
                                
                                $worksheet->write($xlsRow, 24, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 25, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 26, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 27, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 28, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 29, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                $worksheet->write($xlsRow, 30, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			} // end if

			print_header();
		}
		
		$data_row++;
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);	
								
		$PER_ID = $data[PER_ID];
		$PER_GENDER = $data[PER_GENDER];
                if($PER_GENDER==1){
                    $PER_GENDER='ชาย';
                }elseif($PER_GENDER==2){
                    $PER_GENDER='หญิง';
                }else{
                    $PER_GENDER='';
                }
		$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
		$BIRTHDATE = show_date_format($PER_BIRTHDATE,1);
		$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
		$STARTDATE = show_date_format($PER_STARTDATE,1);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = $data[PER_SALARY];
                $REMARK = $data[PER_REMARK];
		//$PER_MGTSALARY = $data[PER_MGTSALARY]; //เดิม
                /*Release 5.2.1.7 begin*/
                $cmd = " select sum(PMH_AMT) as PER_MGTSALARY 
						  from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
						  where PER_ID = $PER_ID and trim(a.EX_CODE)=trim(b.EX_CODE) and PMH_ACTIVE = 1 and MGT_FLAG = 1 and 
						  (PMH_ENDDATE is NULL or PMH_ENDDATE >= '$CHECK_DATE')
						  group by PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_mgt = $db_dpis2->get_array();
		$PER_MGTSALARY = $data_mgt[PER_MGTSALARY]+0;
                
                /*Release 5.2.1.7 end*/
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
                
		//$DATE5Y = ($tmp_date[0]-5) ."-". $tmp_date[1] ."-". $tmp_date[2];
                //$DATE5Y = (($search_year-543)-5) ."-". $tmp_date[1] ."-". $tmp_date[2];
                $DATE5Y = (($search_year-543)-5) ."-04-01";/*Release 5.2.1.16 */
		$PER_SALARY5Y = "";
                /*เดิม*/
		/*$cmd = " select SAH_SALARY
						from   PER_SALARYHIS
						where PER_ID=$PER_ID and SAH_EFFECTIVEDATE < '$DATE5Y' 
						order by SAH_EFFECTIVEDATE desc  ";*/
                /*Release 5.1.0.7 Begin*/
                $cmd = " select SAH_SALARY
						from   PER_SALARYHIS
						where PER_ID=$PER_ID and SAH_EFFECTIVEDATE <= '$DATE5Y' and SAH_EFFECTIVEDATE like '%-04-01%'
						order by SAH_EFFECTIVEDATE desc ,SAH_SALARY desc ";
               
                /*Release 5.1.0.7 end*/
                //if($PER_ID==119){die($cmd);}
                
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PER_SALARY5Y = $data2[SAH_SALARY];
				
		// === หาตำแหน่งล่าสุด เลขที่คำสั่ง, วันที่ออกคำสั่ง, วันที่มีผล
		$POH_EFFECTIVEDATE = "";
		$cmd = " select POH_EFFECTIVEDATE
						from   PER_POSITIONHIS a, PER_MOVMENT b
						where PER_ID=$PER_ID and LEVEL_NO = '$LEVEL_NO' and a.MOV_CODE = b.MOV_CODE and b.MOV_SUB_TYPE != 7 
						order by POH_EFFECTIVEDATE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
		if ($POH_EFFECTIVEDATE) {	
			$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),1);
		}
				
		// === หาตำแหน่งที่บรรจุ
                /* เดิม ใช้ระดับตำแหน่ง จากคอลัมน์ (ระดับของตำแหน่ง: LEVEL_NO) ปรับเปลี่ยนใหม่ให้ใช้จาก (ระดับของผู้ดำรงตำแหน่ง: POH_LEVEL_NO) */
                /* http://dpis.ocsc.go.th/Service/node/2251 */
		$START_PL_NAME = "";
			$cmd = " select PL_CODE, POH_LEVEL_NO
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=1
						order by POH_EFFECTIVEDATE";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$START_PL_CODE = trim($data2[PL_CODE]);
		$START_LEVEL_NO = trim($data2[POH_LEVEL_NO]);

		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$START_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$START_PL_NAME = $data2[PL_NAME];
				
		// === BEG_C_DATE
                /* เดิม ใช้ระดับตำแหน่ง จากคอลัมน์ (ระดับของตำแหน่ง: LEVEL_NO) ปรับเปลี่ยนใหม่ให้ใช้จาก (ระดับของผู้ดำรงตำแหน่ง: POH_LEVEL_NO) */
                /* http://dpis.ocsc.go.th/Service/node/2251 */
		$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE, POH_LEVEL_NO
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID and POH_LEVEL_NO <= '11'
						group by POH_LEVEL_NO, POH_EFFECTIVEDATE
						order by POH_LEVEL_NO desc, MIN(POH_EFFECTIVEDATE) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		//echo "<br>$cmd<br>";
		$BEG_LEVEL_NO = trim($data2[POH_LEVEL_NO]);
		$BEG_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
		if ($BEG_C_DATE) {	
			$BEG_C_DATE = show_date_format(substr($BEG_C_DATE, 0, 10),1);
		}

		// === BEG_BEF_C_DATE
		$cmd = " select MIN(POH_EFFECTIVEDATE) as MIN_POH_EFFECTIVEDATE, POH_LEVEL_NO
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID and POH_LEVEL_NO < '$BEG_LEVEL_NO'
						group by POH_LEVEL_NO, POH_EFFECTIVEDATE
						order by POH_LEVEL_NO desc, MIN(POH_EFFECTIVEDATE) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		//echo "<br>$cmd<br>";
		$BEG_BEF_C_DATE = trim($data2[MIN_POH_EFFECTIVEDATE]);
		if ($BEG_BEF_C_DATE) {	
			$BEG_BEF_C_DATE = show_date_format(substr($BEG_BEF_C_DATE, 0, 10),1);
		}
                /* END ปรับ */
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
//		$OLD_NAME = "";
//		$cmd = " select NH_NAME, NH_SURNAME
//						from   PER_NAMEHIS
//						where PER_ID=$PER_ID 
//						order by NH_DATE desc ";
//		$db_dpis2->send_cmd($cmd);
//		while($data2 = $db_dpis2->get_array()){
//			$NH_NAME = trim($data2[NH_NAME]);
//			$NH_SURNAME = trim($data2[NH_SURNAME]);
//			$OLD_NAME .= $NH_NAME . " " . $NH_SURNAME . ", ";
//		}
//		$REMARK = substr($OLD_NAME,0, -2);
                
                
                /*Release 5.2.1.18 ....	Begin */
                /*-----------เพิ่มเติ่ม-----------*/
                //วันที่ออกจากราชการ
                $SEMICOLON=';;';
                $RESIGN_DATE='';
                $cmd="  SELECT POH_EFFECTIVEDATE 
                        FROM PER_POSITIONHIS PH
                        LEFT JOIN PER_MOVMENT MOV ON(MOV.MOV_CODE=PH.MOV_CODE)
                        WHERE PH.PER_ID=".$PER_ID." AND MOV.MOV_SUB_TYPE=90 
                        ORDER BY POH_EFFECTIVEDATE ";
                $db_dpis2->send_cmd($cmd);
                while($data2 = $db_dpis2->get_array()){
                    if(!empty($data2[POH_EFFECTIVEDATE])){
                        $RESIGN_DATE .= show_date_format(substr($data2[POH_EFFECTIVEDATE], 0, 10),1).$SEMICOLON;
                    }        
		}
                $RESIGN_DATE = substr($RESIGN_DATE,0, -2);
                //วันที่บรรจุกลับเป็นราชการ
                $RETURN_DATE='';
                $cmd="  SELECT POH_EFFECTIVEDATE 
                        FROM PER_POSITIONHIS PH
                        LEFT JOIN PER_MOVMENT MOV ON(MOV.MOV_CODE=PH.MOV_CODE)
                        WHERE PH.PER_ID=".$PER_ID." AND MOV.MOV_SUB_TYPE=11 
                        ORDER BY POH_EFFECTIVEDATE ";
                $db_dpis2->send_cmd($cmd);
                while($data2 = $db_dpis2->get_array()){
                    if(!empty($data2[POH_EFFECTIVEDATE])){
                        $RETURN_DATE .= show_date_format(substr($data2[POH_EFFECTIVEDATE], 0, 10),1).$SEMICOLON;
                    }        
		}
                $RETURN_DATE = substr($RETURN_DATE,0, -2);
                
                //ชื่อเดิม //นามสกุลเดิม
                $OLDF_NAME='';
                $OLDL_NAME='';
		$cmd = "SELECT NH_NAME, NH_SURNAME
                        FROM PER_NAMEHIS
                        WHERE PER_ID=$PER_ID 
                        ORDER BY NH_DATE DESC ";
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			$OLDF_NAME .= trim($data2[NH_NAME]).$SEMICOLON;
			$OLDL_NAME .= trim($data2[NH_SURNAME]).$SEMICOLON;
		}
		$OLDF_NAME = substr($OLDF_NAME,0, -2);
                $OLDL_NAME = substr($OLDL_NAME,0, -2);
                //สถานะ
                //ประเภทบุคลากร
                /*-----------เพิ่มเติ่ม-----------*/
                /*Release 5.2.1.18 ....	End */
                
//			echo "(".($data_count+1).") $PER_CARDNO, $PER_NAME $PER_SURNAME<br>";
/*			
		$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
						  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
						(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE') and MGT_FLAG = 1
						  order by EX_SEQ_NO, b.EX_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PER_MGTSALARY = $data2[PMH_AMT];
*/
		$REPORT_ORDER = "CONTENT";
		$ORDER = $data_row;
		if(is_numeric($START_LEVEL_NO))	$START_LEVEL_NO += 0;
		if(is_numeric($BEG_LEVEL_NO))	$BEG_LEVEL_NO += 0;
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
                
                /*Release 5.2.1.18 ....	Begin */
                $worksheet->write_string($xlsRow, 24, "$RESIGN_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 25, "$RETURN_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 26, "$OLDF_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 27, "$OLDL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 28, "มีชีวิตอยู่", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write_string($xlsRow, 29, "$PERSONAL_TYPE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                if($show_have_org){$worksheet->write_string($xlsRow, 30, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));}
                /*Release 5.2.1.18 ....	End */
		$data_count++;
	} // end while
//echo "<pre>"; echo count($arr_content); print_r($arr_content); echo "</pre>";

	if(!$data_count){
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 23, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                
                $worksheet->write($xlsRow, 24, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 25, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 26, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 27, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 28, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 29, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                if($show_have_org){$worksheet->write($xlsRow, 30, "", set_format("xlsFmtTitle", "B", "C", "", 1));}
	} // end if

	$workbook->close();
	$arr_file[] = $fname1;
?>
<html>
<head>
<title><?=$webpage_title?> - <?=$MENU_TITLE_LV0?><?if($MENU_ID_LV1){?> - <?=$MENU_TITLE_LV1?><?}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-874">
<link rel="stylesheet" href="<?="../$cssfileselected";?>" type="text/css">
<link rel="alternate stylesheet" type="text/css" media="all" href="../stylesheets/calendar-blue.css" title="winter"/>
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="label_normal">
    <tr> 
	  <td align="left" valign="top">
<!--// copy from current_location.html -->
		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td height="28"><table border="0" cellspacing="0" cellpadding="0" class="header_current_location_table">
                  <? if(!$HIDE_HEADER){ ?>
		    <tr>
                    <td width="10" height="">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$MENU_TITLE_LV0?><? if($MENU_ID_LV1>0){ ?> &gt; <?=$MENU_TITLE_LV1?><? } ?><? if($MENU_ID_LV2>0){ ?> &gt; <?=$MENU_TITLE_LV2?><? } ?><? if($MENU_ID_LV3>0){ ?> &gt; <?=$MENU_TITLE_LV3?><? } ?><?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right" valign="middle" style="background:#FFF;" nowrap>&nbsp;
                    <?
             			// หา จำนวน user ที่ยังไม่มีการ logout
						$cmd = " select distinct user_id, from_ip from user_last_access where f_logout != '1' or f_logout is null ";
						$cnt = $db->send_cmd($cmd);
                        echo "<font size=\"+1\" color=\"#0000FF\"><B>$cnt</B>&nbsp;<img src=\"../images/man_small.gif\" height=\"18\" width=\"20\">&nbsp;online</font>";
                    ?>
                    </td>
                  </tr>
		    <? }else{ ?>
		    <tr>
                    <td width="10" height="28">&nbsp;</td>
                    <td class="header_current_location">&reg;&nbsp;<?=$OPTIONAL_TITLE?> </td>
					<td width="40" class="header_current_location_right">&nbsp;</td>
					<td align="right">&nbsp;</td>
                  </tr>
		    <? } // end if ?>
                </table></td>
              </tr>
            </table>
<!--// end copy from current_location.html -->
	  </td>	
	</tr>
<tr><td>
   	<div style=" margin-top:5px; margin-bottom:5px; width:100%"><table style="border: 1px solid #666666;" width="100%">
<?
	ini_set("max_execution_time", 30);

	if (count($arr_file) > 0) {
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>แฟ้ม Excel ที่สร้าง จำนวน ".count($arr_file)." แฟ้ม</td></tr>";
		echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
		$grp = "";
		echo "<tr><td width='3%'>&nbsp;</td><td>";
		echo "<table width='95%' class='table_body'>";
		$num=0;
		for($i_file = 0; $i_file < count($arr_file); $i_file++) {
			if (strpos($arr_file[$i_file],"rpt_soc_xls2")!==false && $grp!="2") {
				$grp="2";
				echo "<tr><td width='25%'>ข้อมูลเครื่องราชฯปัจจุบัน</td>";
				$num=1;
			} else {
				echo "<tr><td width='25%'></td>";
			}
			echo "<td><font size='-1' color='#CC7733'><B>".$num."</B></font> : <a href=\"".$arr_file[$i_file]."\">".$arr_file[$i_file]."</a></td></tr>";
			$num++;
		}
		echo "</table>";
		echo "</td></tr>";
	}
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	$time2 = time();
	$tdiff = $time2 - $time1;
	$s = $tdiff % 60;	// วินาที
	$m = floor($tdiff / 60);	// นาที
	$h = floor($m / 60);	// ชม.
	$m = $m % 60;	// นาที
	$show_lap = ($h?"$h ชม. ":"").($m?"$m นาที ":"")."$s วินาที";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>เริ่ม : <font color='#FF0000'>".date("d-m-Y h:i:s",$time1)."</font><font color='#000000'> จบ : </font><font color='#FF0000'>".date("d-m-Y h:i:s",$time2)."</font><font color='#000000'> ใช้เวลา </font><font color='#FF0000'>$show_lap</font> <font color='#333333'>[</font><font color='#000000'>$tdiff วินาที</font><font color='#333333'>]</font></td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td width='3%'>&nbsp;</td><td>จบรายงาน </td></tr>";
?>
	</table></div>
    </td></tr>
</table>
</body>
</html>
