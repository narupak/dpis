<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$KF_ID=$_GET['KF_ID'];
	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$chkKFStartDate = $data[KF_START_DATE];
	$chkKFEndDate = $data[KF_END_DATE];
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_1, 0, 6);
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_2, 0, 6);
	}

	$PER_ID = $data[PER_ID];
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from PER_PERSONAL where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
		
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
		
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PT_NAME = trim($data[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$report_title = "ตารางแสดงรายการผลการรับการประเมินสมรรถนะของ$PERSON_TYPE[$PER_TYPE] $DEPARTMENT_NAME";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR  $ORG_TITLE $ORG_NAME";
	$company_name1 = "ชื่อผู้รับการประเมิน $PER_NAME  ชื่อตำแหน่งงาน $PL_NAME";
	$report_code = "R_Person_Competency_WhoToMe";

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
		
		$worksheet->set_column(0, 0, 6);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 100);
		$worksheet->set_column(3, 3, 8);

		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 1));
		$worksheet->write($xlsRow, 1, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 1));
		$worksheet->write($xlsRow, 2, "คำถาม", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 1));
		$worksheet->write($xlsRow, 3, "คำตอบ", set_format("xlsFmtTableHeader", "B", "C", "TBLR", 1));
	} // function		

	$arr_content = (array) null;
	$arr_percent = (array) null;
	$arr_cftype = (array) null;

	$data_count = 0;
	$cmd = " SELECT * FROM PER_COMPETENCY_FORM WHERE CF_PER_ID = $PER_ID AND CF_STATUS=1 ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count=0;
	while($data = $db_dpis->get_array()){
		$U_KF_ID = $data[KF_ID];
		$cmd1 = " SELECT * FROM PER_KPI_FORM WHERE KF_ID=$U_KF_ID "; // KF_ID ของรายการ คนที่ประเมินให้ เพื่อเช็คว่าเป็นช่วงเดียวกัน
		$db_dpis2->send_cmd($cmd1);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$U_PER_ID = $data2[PER_ID];
		$othStartDate=$data2[KF_START_DATE];
		$othEndDate=$data2[KF_END_DATE];
		if ($othStartDate == $chkKFStartDate && $othEndDate == $chkKFEndDate) { // if startDate and endDate ตรงกัน
			$CF_ID = $data[CF_ID];
			$CF_TYPE = $data[CF_TYPE]; // ประเภทการประเมิน 1.ประเมินตนเอง 2 ประเมินผู้บังคับบัญชา 3 ประเมินเพื่อน 4 ประเมินผู้ใต้บังคับบัญชา
			// รายงานนี้แสดงยอดสรุปการประเมินตามผู้ถูกประเมิน ความหมายจึงเป็นตรงกันข้ามดังนี้ 1 ตนเองประเมินตนเอง 2 ผู้ใต้บังคับบัญชาประเมิน 3 เพื่อนประเมิน 4 ผู้บังคับบัญชาประเมิน
			// จากที่เห็นจึงต้องเปลี่ยน $CF_TYPE เป็นตรงกันข้าม ซึ่งมีที่ต้องสลับ 2 ตัวคือค่า 2 เป็น 4 และ 4 เป็น 2 รายงานจะจะลงถูกความหมาย
			if ($CF_TYPE==2) { $CF_TYPE=4; } else if ($CF_TYPE==4) { $CF_TYPE=2; }
			// % ตามประเภท CF_TYPE เป็น % ของการประเมิน ต้องกลับที่ 2 กับ 4 ด้วยให้เป็น % น้ำหนักของคะแนนที่ถูกประเมินโดยใคร ::: $CF_TYPE สลับแล้วใช้ได้เลย
			// จัดเตรียมค่า cf_type array เพื่อการอ่าน Percent น้ำหนัก
			if (!in_array($CF_TYPE, $arr_cftype)) {
				$arr_cftype[] = $CF_TYPE;
			}

			$cmd1 = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
							 from PER_PERSONAL where	PER_ID=$U_PER_ID ";
			$db_dpis2->send_cmd($cmd1);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$U_PN_CODE = trim($data2[PN_CODE]);
			$U_PER_NAME = trim($data2[PER_NAME]);
			$U_PER_SURNAME = trim($data2[PER_SURNAME]);
			$cmd1 = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$U_PN_CODE' ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$U_PN_NAME = trim($data2[PN_NAME]);
	
			$cmd1 = " SELECT a.QS_ID as Q_ID, QS_NAME, CP_CODE, CA_ANSWER FROM PER_COMPETENCY_ANSWER a, PER_QUESTION_STOCK b 
							  WHERE a.QS_ID=b.QS_ID AND CF_ID=$CF_ID ORDER BY a.QS_ID, CP_CODE "; // CF_ID
			$db_dpis2->send_cmd($cmd1);
//			$db_dpis2->show_error();
			while($data2 = $db_dpis2->get_array()) {
				$SUB_CP_CODE=$data2[CP_CODE];
				$SUB_QS_ID=$data2[Q_ID];
				$SUB_QS_NAME=$data2[QS_NAME];
				$SUB_ANSWER=$data2[CA_ANSWER];

				$cmd2 = " select CP_NAME, CP_MODEL from PER_COMPETENCE where CP_CODE='$SUB_CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
				$CP_NAME = $data3[CP_NAME];
				$CP_MODEL = $data3[CP_MODEL];
				$ST_CP_MODEL="";
				if($CP_MODEL==1) $ST_CP_MODEL = "สมรรถนะหลัก";
				elseif($CP_MODEL==2) $ST_CP_MODEL = "สมรรถนะผู้บริหาร";
				elseif($CP_MODEL==3) $ST_CP_MODEL = "สมรรถนะประจำสายงาน";

				$arr_content[$data_count][perid] = $U_PER_ID;
				$arr_content[$data_count][pername] = "$U_PN_NAME$U_PER_NAME $U_PER_SURNAME";
				$arr_content[$data_count][code] = $SUB_CP_CODE;
				$arr_content[$data_count][name] = $CP_NAME;
				$arr_content[$data_count][type] = $ST_CP_MODEL;
				$arr_content[$data_count][cftype] = $CF_TYPE;
				$arr_content[$data_count][qsid] = $SUB_QS_ID;
				$arr_content[$data_count][qsname] = $SUB_QS_NAME;
				$arr_content[$data_count][answer] = $SUB_ANSWER;
				$data_count++;
			} // end while ANSWER
		} // end if startDate and endDate ตรงกัน
	} // end while PER_COMPETENCY_FORM

	// เรียงชุดของ cftype เพื่อไปต่อทำเป็น key ในการเรียก per_pos_type
	array_multisort($arr_cftype, SORT_ASC);
//	echo implode("",$arr_cftype)+"|";
	$postype=substr($LEVEL_NO,0,1).implode("",$arr_cftype);
//	echo "postype=$postype<br>";
	$cmd = " SELECT * FROM PER_POS_TYPE WHERE POS_TYPE='$postype' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$arr_percent[1] = $SEFT_RATIO = $data[SEFT_RATIO];
	$arr_percent[2] = $CHIEF_RATIO = $data[CHIEF_RATIO];
	$arr_percent[3] = $FRIEND_RATIO = $data[FRIEND_RATIO];
	$arr_percent[4] = $SUB_RATIO = $data[SUB_RATIO];
//	echo "postype=$postype - RATIO[$postype]=$SEFT_RATIO,$CHIEF_RATIO,$FRIEND_RATIO,$SUB_RATIO<br>";

	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		if($company_name1){
			$worksheet->write($xlsRow, 0, $company_name1, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} //if($company_name1){
		
		print_header();
	
		$U_PER_ID=0;
		$SUB_CP_CODE="";
		$cnt_cp_code = 0;
		$cnt_question = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
//			echo "$U_PER_ID != ".$arr_content[$data_count][perid]."<br>";
			if ($U_PER_ID != $arr_content[$data_count][perid]) {
				$U_PER_ID = $arr_content[$data_count][perid];
				if ($U_PER_ID == $PER_ID) {
					$U_PER_NAME = "ตนเอง";
				} else {
					$U_PER_NAME = $arr_content[$data_count][pername];
				}
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "ผู้ประเมิน : $U_PER_ID - $U_PER_NAME", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
				$seq_no=0;
			}
			if ($SUB_CP_CODE == $arr_content[$data_count][code]) {
				$CP_NAME = "";
				$prt_cnt_cp = "";
			} else {
				$CP_NAME = $arr_content[$data_count][name];
				$SUB_CP_CODE = $arr_content[$data_count][code];
				$cnt_cp_code++;
				$prt_cnt_cp = number_format($cnt_cp_code);
				$cnt_question = 0;
			}
			$cnt_question++;
			$ST_CP_MODEL = $arr_content[$data_count][type];
			$CF_TYPE = $arr_content[$data_count][cftype];
			$SUB_QS_ID = $arr_content[$data_count][qsid];
			$SUB_QS_NAME = $arr_content[$data_count][qsname];
			$SUB_ANSWER = $arr_content[$data_count][answer];
			
			$xlsRow++;
			$seq_no++;
//			$worksheet->write($xlsRow, 0, ($seq_no?number_format($seq_no):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, 0, $prt_cnt_cp, set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$CP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
//			$worksheet->write_string($xlsRow, 2, "$SUB_QS_ID:$SUB_QS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, "$cnt_question:$SUB_QS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$SUB_ANSWER", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
		} // end for
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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