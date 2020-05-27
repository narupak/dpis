<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($list_type == "PER_ORG"){
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.ORG_ID_REF in (". implode($arr_org_ref, ",") ."))";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	} elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	$company_name = "";
	$report_title = "การจัดเตรียมข้อมูลเกี่ยวกับการศึกษาของ$PERSON_TYPE[$search_per_type]||$MINISTRY_NAME $DEPARTMENT_NAME";
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

		$worksheet->set_column(0, 0, 15);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 40);
		$worksheet->set_column(4, 4, 5);
		$worksheet->set_column(5, 5, 5);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 10);
		$worksheet->set_column(8, 8, 30);
		$worksheet->set_column(9, 9, 10);
		$worksheet->set_column(10, 10, 10);
		$worksheet->set_column(11, 11, 30);
		$worksheet->set_column(12, 12, 10);
		$worksheet->set_column(13, 13, 10);
		$worksheet->set_column(14, 14, 30);
		$worksheet->set_column(15, 15, 10);
		$worksheet->set_column(16, 16, 10);
		$worksheet->set_column(17, 17, 30);
		$worksheet->set_column(18, 18, 15);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "เลขประจำตัวประชาชน", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 2, "นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 3, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 4, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 5, "สถานะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 6, "รหัสระดับการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 7, "รหัสกรมบัญชีกลาง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 8, "ชื่อระดับการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 9, "รหัสวุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 10, "รหัสกรมบัญชีกลาง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 11, "ชื่อวุฒิการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 12, "รหัสสาขาวิชาเอก", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 13, "รหัสกรมบัญชีกลาง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 14, "ชื่อสาขาวิชาเอก", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 15, "รหัสสถาบันการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 16, "รหัสกรมบัญชีกลาง", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 17, "ชื่อสถาบันการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
		$worksheet->write($xlsRow, 18, "วันที่สำเร็จการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 0));
	} // function		
		
	if($DPISDB=="odbc"){  
					$cmd = " select		a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.PER_TYPE, a.LEVEL_NO, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID, SAH_DOCNO, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, 
										SAH_SALARY_UP, SAH_SALARY, SAH_SALARY_EXTRA, SAH_TOTAL_SCORE, SAH_REMARK, b.MOV_CODE
					  from			PER_PERSONAL a, PER_SALARYHIS b
					  where		a.PER_ID=b.PER_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 and 
										SAH_EFFECTIVEDATE = '$SAH_EFFECTIVEDATE' and SAH_KF_CYCLE = $KF_CYCLE and b.MOV_CODE not in ('215','21510','21520')
					  $search_condition
					  order by CLng(SAH_POS_NO) ";
	
	}elseif($DPISDB=="oci8"){ 
					$cmd = " select		PER_TYPE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, 
										a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, EDU_SEQ, EL_CODE, EN_CODE, 
										EM_CODE, INS_CODE, EDU_ENDDATE, EDU_INSTITUTE
					  from			PER_PERSONAL a, PER_EDUCATE b
					  where		a.PER_ID=b.PER_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 and EDU_TYPE like '%2%'
					  $search_condition
					  order by PER_NAME, PER_SURNAME ";	
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_TYPE = $data[PER_TYPE];
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID];
		$POEMS_ID = $data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$EDU_SEQ = $data[EDU_SEQ];
		$EL_CODE = trim($data[EL_CODE]);
		$EN_CODE = trim($data[EN_CODE]);
		$EM_CODE = trim($data[EM_CODE]);
		$INS_CODE = trim($data[INS_CODE]);
		$EDU_INSTITUTE = trim($data[EDU_INSTITUTE]);

		$cmd = " select EL_NAME, EL_CGD_CODE from PER_EDUCLEVEL where trim(EL_CODE)='$EL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EL_NAME = trim($data2[EL_NAME]);
		$EL_CGD_CODE = trim($data2[EL_CGD_CODE]);

		$cmd = " select EN_NAME, EN_CGD_CODE from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		$EN_CGD_CODE = trim($data2[EN_CGD_CODE]);

		$cmd = " select EM_NAME, EM_CGD_CODE from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EM_NAME = trim($data2[EM_NAME]);
		$EM_CGD_CODE = trim($data2[EM_CGD_CODE]);

		$cmd = " select INS_NAME, INS_CGD_CODE from PER_INSTITUTE where trim(INS_CODE)='$INS_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$INS_NAME = trim($data2[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = $EDU_INSTITUTE;
		$INS_CGD_CODE = trim($data2[INS_CGD_CODE]);
		$PL_CODE = $PL_NAME = "";
		if($PER_TYPE==1 && $POS_ID){
			$cmd = " select PL_CODE from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_CODE = trim($data2[PL_CODE]);
			
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = trim($data2[PL_NAME]);

		}elseif($PER_TYPE==2){
		}elseif($PER_TYPE==3){
		} // end if

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][cardno] = $PER_CARDNO;
		$arr_content[$data_count][name] = $PER_NAME;
		$arr_content[$data_count][surname] = $PER_SURNAME;
		$arr_content[$data_count][pl_name] = $PL_NAME;
		$arr_content[$data_count][edu_seq] = $EDU_SEQ;
		$arr_content[$data_count][edu_status] = $EDU_STATUS;
		$arr_content[$data_count][el_code] = $EL_CODE;
		$arr_content[$data_count][el_cgd_code] = $EL_CGD_CODE;
		$arr_content[$data_count][el_name] = $EL_NAME;
		$arr_content[$data_count][en_code] = $EN_CODE;
		$arr_content[$data_count][en_cgd_code] = $EN_CGD_CODE;
		$arr_content[$data_count][en_name] = $EN_NAME;
		$arr_content[$data_count][em_code] = $EM_CODE;
		$arr_content[$data_count][em_cgd_code] = $EM_CGD_CODE;
		$arr_content[$data_count][em_name] = $EM_NAME;
		$arr_content[$data_count][ins_code] = $INS_CODE;
		$arr_content[$data_count][ins_cgd_code] = $INS_CGD_CODE;
		$arr_content[$data_count][ins_name] = $INS_NAME;
		$arr_content[$data_count][edu_enddate] = $EDU_ENDDATE;

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
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$CARDNO = $arr_content[$data_count][cardno];
			$NAME = $arr_content[$data_count][name];
			$SURNAME = $arr_content[$data_count][surname];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$EDU_SEQ = $arr_content[$data_count][edu_seq];
			$EDU_STATUS = $arr_content[$data_count][edu_status];
			$EL_CODE = $arr_content[$data_count][el_code];
			$EL_CGD_CODE = $arr_content[$data_count][el_cgd_code];
			$EL_NAME = $arr_content[$data_count][el_name];
			$EN_CODE = $arr_content[$data_count][en_code];
			$EN_CGD_CODE = $arr_content[$data_count][en_cgd_code];
			$EN_NAME = $arr_content[$data_count][en_name];
			$EM_CODE = $arr_content[$data_count][em_code];
			$EM_CGD_CODE = $arr_content[$data_count][em_cgd_code];
			$EM_NAME = $arr_content[$data_count][em_name];
			$INS_CODE = $arr_content[$data_count][ins_code];
			$INS_CGD_CODE = $arr_content[$data_count][ins_cgd_code];
			$INS_NAME = $arr_content[$data_count][ins_name];
			$EDU_ENDDATE = $arr_content[$data_count][edu_enddate];

			if($CONTENT_TYPE=="CONTENT"){
				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, "$CARDNO", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 1, "$NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 2, "$SURNAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 3, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 4, "$EDU_SEQ", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 5, "$EDU_STATUS", set_format("xlsFmtTableDetail", "", "C", "LTRB", 0));
				$worksheet->write_string($xlsRow, 6, "$EL_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 7, "$EL_CGD_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 8, "$EL_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 9, "$EN_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 10, "$EN_CGD_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 11, "$EN_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 12, "$EM_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 13, "$EM_CGD_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 14, "$EM_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 15, "$INS_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 16, "$INS_CGD_CODE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 17, "$INS_NAME", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
				$worksheet->write_string($xlsRow, 18, "$EDU_ENDDATE", set_format("xlsFmtTableDetail", "", "L", "LTRB", 0));
			} // end if
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
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"การจัดเตรียมข้อมูลเกี่ยวกับการศึกษา.xls\"");
	header("Content-Disposition: inline; filename=\"การจัดเตรียมข้อมูลเกี่ยวกับการศึกษา.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>