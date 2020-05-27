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
				if($DPISDB=="oci8") $order_by .= "TO_NUMBER(" . $pos_no . ")";
				if($DPISDB=="mysql") $order_by .= $pos_no . "+0";

				$heading_name .= " �Ţ�����˹�";
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
	
	$company_name = "�ٻẺ����͡��§ҹ : $list_type_text";
	$report_title = "�к�����ͧ�Ҫ��ͧ�ӹѡ�ŢҸԡ�ä���Ѱ�����";
	$report_code = "Current Data";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
/*
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
*/
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
	while($data = $db_dpis->get_array()){
		if($ORG_ID_REF != $data[ORG_ID_REF]){
			$ORG_ID_REF = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_REF = $data2[ORG_NAME];

			$arr_content[$data_count][type] = "ORG_REF";
			$arr_content[$data_count][org_id_ref] = $ORG_ID_REF;
			$arr_content[$data_count][org_name_ref] = $ORG_NAME_REF;
			
			$data_count++;
		} // end if
		
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

		// === ���Թ��͹��͹��ѧ 5 �� 
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
				
		// === �ҵ��˹�����ش �Ţ�������, �ѹ����͡�����, �ѹ����ռ�
		$POH_EFFECTIVEDATE = "";
		$cmd = " select POH_EFFECTIVEDATE
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID 
						order by POH_EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
		if ($POH_EFFECTIVEDATE) {	
			$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),1);
		}
				
		// === �ҵ��˹觷���è�
		$START_PL_NAME = "";
		$cmd = " select PL_CODE, LEVEL_NO
						from   PER_POSITIONHIS
						where PER_ID=$PER_ID and (MOV_CODE like '101%' or MOV_CODE like '105%')
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

		// === ������ͧ�Ҫ�����ش����
		$DC_CODE = $DEH_DATE = "";
		$cmd = " select DC_CODE, DEH_DATE
						from   PER_DECORATEHIS
						where PER_ID=$PER_ID and DC_CODE < '40' 
						order by DEH_DATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DC_CODE = trim($data2[DC_CODE]);
		$DEH_DATE = trim($data2[DEH_DATE]);
		if ($DEH_DATE) {	
			$DEH_DATE = show_date_format(substr($DEH_DATE, 0, 10),1);
		}

		$cmd = " select DC_SHORTNAME from PER_DECORATION where DC_CODE='$DC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$DC_NAME = $data2[DC_SHORTNAME];
				
		// === �������
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
			
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][department_name] = $DEPARTMENT_NAME;
		$arr_content[$data_count][per_cardno] = $PER_CARDNO;
		$arr_content[$data_count][pn_name] = $PN_NAME;
		$arr_content[$data_count][rank_name] = $RANK_NAME;
		$arr_content[$data_count][per_name] = $PER_NAME;
		$arr_content[$data_count][per_surname] = $PER_SURNAME;
		$arr_content[$data_count][per_gender] = $PER_GENDER;
		$arr_content[$data_count][birthdate] = $BIRTHDATE;
		$arr_content[$data_count][startdate] = $STARTDATE;
		$arr_content[$data_count][start_level_no] = level_no_format($START_LEVEL_NO);
		$arr_content[$data_count][pos_no] = $POS_NO;
		$arr_content[$data_count][pl_code] = $PL_CODE;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][start_pl_name] = $START_PL_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][level_no] = level_no_format($BEG_LEVEL_NO);
		$arr_content[$data_count][sah_effectivedate] = $SAH_EFFECTIVEDATE;
//		$arr_content[$data_count][per_salary] = (($PER_SALARY)?number_format($PER_SALARY,0,'',','):"");
//		$arr_content[$data_count][per_salary5y] = (($PER_SALARY5Y)?number_format($PER_SALARY5Y,0,'',','):"");
//		$arr_content[$data_count][per_mgtsalary] = (($PER_MGTSALARY)?number_format($PER_MGTSALARY,0,'',','):"");
		$arr_content[$data_count][per_salary] = $PER_SALARY;
		$arr_content[$data_count][per_salary5y] = $PER_SALARY5Y;
		$arr_content[$data_count][per_mgtsalary] = $PER_MGTSALARY;
		$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE;
		$arr_content[$data_count][dc_name] = $DC_NAME;
		$arr_content[$data_count][deh_date] = $DEH_DATE;
		if ($search_per_type == 2)
			$arr_content[$data_count][level_name] = $LEVEL_NAME;
		else
			$arr_content[$data_count][level_name] = $LEVEL_NO;
		$arr_content[$data_count][beg_c_date] = $BEG_C_DATE;
		$arr_content[$data_count][beg_bef_c_date] = $BEG_BEF_C_DATE;
		$arr_content[$data_count][remark] = $OLD_NAME;

		$data_count++;														
						
	} // end while
//echo "<pre>"; echo count($arr_content); print_r($arr_content); echo "</pre>";

	// ===== condition $count_data  from "select data" =====
	if($count_data){
		$xlsRow = 0;
		$count_org_ref = 0;

		for($data_count=0; $data_count<count($arr_content); $data_count++){
				$REPORT_ORDER = $arr_content[$data_count][type];
				$ORDER = $arr_content[$data_count][order];
				$DEPARTMENT_NAME = $arr_content[$data_count][department_name];
				$PER_CARDNO = $arr_content[$data_count][per_cardno];
				$PN_NAME = $arr_content[$data_count][pn_name];
				$RANK_NAME = $arr_content[$data_count][rank_name];
				$PER_NAME = $arr_content[$data_count][per_name];
				$PER_SURNAME = $arr_content[$data_count][per_surname];
				if ($arr_content[$data_count][per_gender] == 1)	$PER_GENDER = "���";
				elseif ($arr_content[$data_count][per_gender] == 2)	$PER_GENDER = "˭ԧ";	
				$BIRTHDATE = $arr_content[$data_count][birthdate];
				$STARTDATE = $arr_content[$data_count][startdate];
				$START_LEVEL_NO = $arr_content[$data_count][start_level_no];
				$POS_NO = $arr_content[$data_count][pos_no];
				$PL_CODE = $arr_content[$data_count][pl_code];
				$PL_NAME = $arr_content[$data_count][pl_name];
				$START_PL_NAME = $arr_content[$data_count][start_pl_name];
				$ORG_NAME = $arr_content[$data_count][org_name];
				$LEVEL_NO = $arr_content[$data_count][level_no];
				$SAH_EFFECTIVEDATE = $arr_content[$data_count][sah_effectivedate];
				$PER_SALARY = $arr_content[$data_count][per_salary];
				$PER_SALARY5Y = $arr_content[$data_count][per_salary5y];
				$PER_MGTSALARY = $arr_content[$data_count][per_mgtsalary];
				$POH_EFFECTIVEDATE = $arr_content[$data_count][poh_effectivedate];
				$DC_NAME = $arr_content[$data_count][dc_name];
				$DEH_DATE = $arr_content[$data_count][deh_date];
				$LEVEL_NAME = $arr_content[$data_count][level_name];
				$BEG_C_DATE = $arr_content[$data_count][beg_c_date];
				$BEG_BEF_C_DATE = $arr_content[$data_count][beg_bef_c_date];
				$REMARK = $arr_content[$data_count][remark];
			
			if($REPORT_ORDER == "ORG_REF"){
				if($data_count > 0) $count_org_ref++;

				$ORG_ID_REF = $arr_content[$data_count][org_id_ref];
				$ORG_NAME_REF = $arr_content[$data_count][org_name_ref];

				$worksheet = &$workbook->addworksheet("$report_code".($count_org_ref?"_$count_org_ref":""));
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
					$worksheet->write($xlsRow, 17, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 18, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 19, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 20, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 21, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 22, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
					$worksheet->write($xlsRow, 23, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				} // end if

				print_header();
			}else{	//if($REPORT_ORDER == "CONTENT"){
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
				$worksheet->write_string($xlsRow, 11, "$LEVEL_NO", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
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
			} // end if
		} 	// end for				
	}else{	// if($count_data)
		$worksheet = &$workbook->addworksheet("$report_code");
		$worksheet->set_margin_right(0.50);
		$worksheet->set_margin_bottom(1.10);
		
		//====================== SET FORMAT ======================//
		require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
		//====================== SET FORMAT ======================//

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
	} // end if

	include("rpt_soc_xls1.php");
?>