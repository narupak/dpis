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
	$report_code = "HistoryData";

	function print_header1(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 50);
                $worksheet->set_column(7, 7, 30);
                $worksheet->set_column(8, 8, 30);
                $worksheet->set_column(9, 9, 30);
                $worksheet->set_column(10, 10, 30);
                $worksheet->set_column(11, 11, 30);
                $worksheet->set_column(12, 12, 30);
                $worksheet->set_column(13, 13, 30);
                $worksheet->set_column(14, 14, 30);
                $worksheet->set_column(15, 15, 30);
                $worksheet->set_column(16, 16, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ID_CARD", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "FNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "LNAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "INS_CODE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "INS_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "POS_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		$worksheet->write($xlsRow, 6, "DEP_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 7, "DEH_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 8, "DC_NAME", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 9, "DEH_GAZETTE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 10, "DEH_BOOK", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 11, "DEH_PART", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 12, "DEH_PAGE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 13, "DEH_ORDER_DECOR", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 14, "DEH_RECEIVE_FLAG", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 15, "DEH_RECEIVE_DATE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 16, "DEH_RETURN_FLAG", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // function		

	function generate_condition1($current_index){
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
	
	function initialize_parameter1($current_index){
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
				order by		$order_by ";
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
	$db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();

	$fname= "../../Excel/tmp/rpt_soc_xls6";
	$fname1=$fname.".xls";
	$workbook = new writeexcel_workbook($fname1);

			//====================== SET FORMAT ======================//
			require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
			//====================== SET FORMAT ======================//

	$file_limit = 2000;
	$data_limit = 1000;
	$f_new = true;

	$data_count = $data_row = 0;
	initialize_parameter1(0);
	$ORG_ID_REF = -1;
	while($data = $db_dpis->get_array()){
//		echo "$data_count -> $ORG_ID_REF != ".$data[ORG_ID_REF].", REPORT_ORDER=$REPORT_ORDER<br>";
		if($ORG_ID_REF != $data[ORG_ID_REF]){
//			if ($ORG_ID_REF==-1) $f_new = false;
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];
			$REPORT_ORDER = "ORG_REF";
//		} else {
//			$REPORT_ORDER = "";
		} // end if
/*
		// เช็คจบที่ข้อมูล $data_limit
		if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//			echo "$data_count>>$xls_fname>>$fname1<br>";
			$workbook->close();
			$arr_file[] = $fname1;
	
			$fnum++;
			$fname1=$fname."_$fnum.xls";
			$workbook = new writeexcel_workbook($fname1);
//			echo "open workbook<br>";

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
				echo "1..new file....$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
			} else if ($REPORT_ORDER == "ORG_REF") {
				$f_org_ref_skip = true;
				$sheet_no=0; $sheet_no_text="";
				if($data_count > 0) $count_org_ref++;
				$REPORT_ORDER = "";
				echo "1..org_ref..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
			} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
				$sheet_no++;
				$sheet_no_text = "_$sheet_no";
				echo "1..newsheet..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
			}

//			echo "report_code:$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
			$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
			$worksheet->set_margin_right(0.50);
			$worksheet->set_margin_bottom(1.10);
			
			//====================== SET FORMAT ======================//
			require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
			//====================== SET FORMAT ======================//

			$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
			for($i=0; $i<count($arr_title); $i++){
				$xlsRow = $i;
//				echo "arr_title[$i]=".$arr_title[$i]."<br>";
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
                                $worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                $worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			} // end if
		
			print_header1();
		}
*/
		$data_row++;
		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select $line_name as PL_NAME from $line_table b where $line_code='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_NAME]);	
								
		$PER_ID = $data[PER_ID];
		$PER_CARDNO = $data[PER_CARDNO];
		$TITLE_CODE = $data[TITLE_CODE];
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
				
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TITLE_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];

		// === หาเครื่องราชย์
		$DC_NAME = $DEH_DATE = "";
                $DEH_RECEIVE_DATE = "";
		$cmd = " select DC_SHORTNAME, DEH_DATE, DEH_POSITION, DEH_ORG,a.DEH_DATE, b.DC_NAME ,DEH_GAZETTE,DEH_BOOK,DEH_PART,DEH_PAGE,DEH_ORDER_DECOR,DEH_RECEIVE_FLAG,DEH_RECEIVE_DATE,DEH_RETURN_FLAG
								from   PER_DECORATEHIS a, PER_DECORATION b
								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								order by a.DEH_DATE desc ";
//								where PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE and DC_ORDER <= 18 
		$count_data = $db_dpis1->send_cmd($cmd);
//		die("$cmd ($count_data)<br>");
		if ($count_data > 0) {
			while($data1 = $db_dpis1->get_array()){
				$DC_NAME = trim($data1[DC_SHORTNAME]);
				$DEH_DATE = trim($data1[DEH_DATE]);
				$PL_NAME = trim($data1[DEH_POSITION]);
				$ORG_NAME = trim($data1[DEH_ORG]);
                                
                                $DEH_DATE = trim($data1[DEH_DATE]);
                                $DC_NAME_FULL = trim($data1[DC_NAME]);
                                $DEH_GAZETTE = trim($data1[DEH_GAZETTE]);
                                $DEH_BOOK = trim($data1[DEH_BOOK]);
                                $DEH_PART = trim($data1[DEH_PART]);
                                $DEH_PAGE = trim($data1[DEH_PAGE]);
                                $DEH_ORDER_DECOR = trim($data1[DEH_ORDER_DECOR]);
                                $DEH_RECEIVE_FLAG = trim($data1[DEH_RECEIVE_FLAG]);
                                $DEH_RECEIVE_DATE = trim($data1[DEH_RECEIVE_DATE]);
                                $DEH_RETURN_FLAG = trim($data1[DEH_RETURN_FLAG]);
                                
                                if($DEH_RECEIVE_FLAG == "0"){
                                    $DEH_RECEIVE_FLAG_N = "ยังไม่ได้รับเครื่องราชฯ"; 
                                }else if($DEH_RECEIVE_FLAG == "1"){
                                    $DEH_RECEIVE_FLAG_N = "รับเครื่องราชฯแล้ว";
                                }else if($DEH_RECEIVE_FLAG == ""){
                                    $DEH_RECEIVE_FLAG_N = ""; 
                                }
                                if($DEH_RETURN_FLAG == "1"){
                                    $DEH_RETURN_FLAG_N = "ยังไม่ได้คืนเครื่องราชฯ";
                                }else if($DEH_RETURN_FLAG == "2"){
                                    $DEH_RETURN_FLAG_N = "คืนเครื่องราชฯแล้ว";
                                }else if($DEH_RETURN_FLAG == ""){
                                    $DEH_RETURN_FLAG_N = "";
                                }
                                if($DEH_RECEIVE_DATE){
                                    $DEH_RECEIVE_DATE = show_date_format(substr($DEH_RECEIVE_DATE, 0, 10),1);
                                }
                                
				$POH_EFFECTIVEDATE = $DEH_DATE;
				if ($DEH_DATE) {	
					$DEH_DATE = show_date_format(substr($DEH_DATE, 0, 10),1);
				}

				if (!$PL_NAME || !$ORG_NAME) {
					$PL_CODE = $PL_NAME = $ORG_NAME = "";
					$cmd = " select PL_CODE, POH_ORG2
										from   PER_POSITIONHIS
									where PER_ID=$PER_ID and POH_EFFECTIVEDATE <= '$POH_EFFECTIVEDATE'
									order by POH_EFFECTIVEDATE desc ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_CODE = trim($data2[PL_CODE]);
					$ORG_NAME = trim($data2[POH_ORG2]);

					$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PL_NAME = $data2[PL_NAME];
				}

				// เช็คจบที่ข้อมูล $data_limit
				if ($data_count > 0 && ($data_count % $file_limit) == 0) {
//					echo "1..data_count=$data_count>>file_limit=$file_limit<br>";
					$workbook->close();
					$arr_file[] = $fname1;
	
					$fnum++;
					$fname1=$fname."_$fnum.xls";
					$workbook = new writeexcel_workbook($fname1);
//					echo "open workbook<br>";

					//====================== SET FORMAT ======================//
					require "../../Excel/my.defined_format.inc.format_param.php";	// define format parameter
					//====================== SET FORMAT ======================//

					$f_new = true;
				};
		// 		เช็คจบที่ข้อมูล $data_limit
//				if($REPORT_ORDER == "ORG_REF" || ($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
				if(($data_count > 0 && ($data_count % $data_limit) == 0) || $f_new){
					if ($f_new) {
						$sheet_no=0; $sheet_no_text="";
						if($data_count > 0) $count_org_ref++;
						$f_new = false;
//						echo "1..new file....$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
					} else if ($REPORT_ORDER == "ORG_REF") {
						$f_org_ref_skip = true;
						$sheet_no=0; $sheet_no_text="";
						if($data_count > 0) $count_org_ref++;
						$REPORT_ORDER = "";
//						echo "1..org_ref..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
					} else if ($data_count > 0 && ($data_count % $data_limit) == 0) {
						$sheet_no++;
						$sheet_no_text = "_$sheet_no";
//						echo "1..newsheet..$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."...data_count=$data_count<br>";
					}

//					echo "report_code:$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text."<br>";
					$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":"").$sheet_no_text);
					$worksheet->set_margin_right(0.50);
					$worksheet->set_margin_bottom(1.10);
			
					//====================== SET FORMAT ======================//
					require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
					//====================== SET FORMAT ======================//

					$arr_title = explode("||", "$ORG_NAME_REF||$report_title");
					for($i=0; $i<count($arr_title); $i++){
						$xlsRow = $i;
//						echo "arr_title[$i]=".$arr_title[$i]."<br>";
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
                                                $worksheet->write($xlsRow, 15, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                                $worksheet->write($xlsRow, 16, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
                                                
					} // end if
		
					print_header1();
				}
			
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$PER_CARDNO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 2, "$PER_SURNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 3, "$DC_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 4, "$DEH_DATE", set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
				$worksheet->write_string($xlsRow, 5, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write_string($xlsRow, 6, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                
                                $worksheet->write_string($xlsRow, 7, "$DEH_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 8, "$DC_NAME_FULL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 9, "$DEH_GAZETTE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 10, "$DEH_BOOK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 11, "$DEH_PART", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 12, "$DEH_PAGE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 13, "$DEH_ORDER_DECOR", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 14, "$DEH_RECEIVE_FLAG_N", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 15, "$DEH_RECEIVE_DATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                $worksheet->write_string($xlsRow, 16, "$DEH_RETURN_FLAG_N", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));                         

				$data_count++;
			} // end while
		} else { // ถ้าไม่มีข้อมูล
//
		}
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";	
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
			if (strpos($arr_file[$i_file],"rpt_soc_xls6")!==false && $grp!="6") {
				$grp="6";
				echo "<tr><td width='25%'>ตรวจสอบการรับ/คืนเครื่องราชอิสริยาภรณ์</td>";
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
