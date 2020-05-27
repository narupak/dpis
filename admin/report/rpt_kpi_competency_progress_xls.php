<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	function expand_escape($string) {
    	return preg_replace_callback(
        	'/\\\([nrtvf]|[0-7]{1,3}|[0-9A-Fa-f]{1,2})?/',
        	create_function(
            	'$matches',
            	'return ($matches[0] == "\\\\") ? "" : eval( sprintf(\'return "%s";\', $matches[0]) );'
        	),
        	$string
    	);
	}
	$SEARCH_CONDITION = expand_escape($SEARCH_CONDITION);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$report_title = "รายงานสถานะของการประเมินของแต่ละบุคคล ของ $DEPARTMENT_NAME";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
	$report_code = "R_KPI_Competency_Progress";
	
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 40);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 17);
		$worksheet->set_column(5, 5, 17);
		$worksheet->set_column(6, 6, 17);
		
		$worksheet->write($xlsRow, 0, "เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "ชื่อ - นามสกุล", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "ประเมินตนเอง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "ประเมินผู้บังคับบัญชา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ประเมินเพื่อนร่วมงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ประเมินผู้ใต้บังคับบัญชา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if

	if($DPISDB=="odbc"){
		$cmd = "	select	a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, ORG_ID_KPI, a.ORG_ID, b.PER_TYPE, b.POS_ID
							from		PER_KPI_FORM a, PER_PERSONAL b
							where		a.PER_ID=b.PER_ID
											$SEARCH_CONDITION
							order by 	a.KF_END_DATE, a.KF_CYCLE, a.ORG_ID, b.PER_NAME, b.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select	a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, ORG_ID_KPI, a.ORG_ID, b.PER_TYPE, b.POS_ID
								from		PER_KPI_FORM a, PER_PERSONAL b
								where		a.PER_ID=b.PER_ID
												$SEARCH_CONDITION
								order by 	a.KF_END_DATE, a.KF_CYCLE, a.ORG_ID, b.PER_NAME, b.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select	a.KF_ID, a.KF_END_DATE, a.KF_CYCLE, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, a.SUM_KPI, a.SUM_COMPETENCE, a.PER_ID, a.PER_ID_REVIEW, a.PER_ID_REVIEW1, a.PER_ID_REVIEW2, CHIEF_PER_ID, FRIEND_FLAG, TOTAL_SCORE, ORG_ID_KPI, a.ORG_ID, b.PER_TYPE, b.POS_ID
							from		PER_KPI_FORM a, PER_PERSONAL b
							where		a.PER_ID=b.PER_ID
											$SEARCH_CONDITION
							order by 	a.KF_END_DATE, a.KF_CYCLE, a.ORG_ID, b.PER_NAME, b.PER_SURNAME ";
	} // end if
	
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	//echo $cmd;

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		$xlsRow = 1;
		$worksheet->write($xlsRow, 0, "$company_name", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		print_header(2);
		$data_count = 2;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$KF_ID = trim($data[KF_ID]);
			$PER_ID = trim($data[PER_ID]);
			$POS_ID = trim($data[POS_ID]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PN_CODE = $data[PN_CODE];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
			$SELF_PER_ID = $PER_ID;
			$CHIEF_PER_ID = $data[CHIEF_PER_ID]; 	// รหัสหัวหน้า
			$FRIEND_FLAG = $data[FRIEND_FLAG];
			$ORG_ID_KPI = $data[ORG_ID_KPI];
			$PER_TYPE = trim($data[PER_TYPE]);
			
		//============ ระดับตำแหน่ง ===========	
			$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data2=$db_dpis2->get_array();
			$LEVEL_NAME=trim($data2[LEVEL_NAME]);
			
			$arr_temp = explode(" ", $LEVEL_NAME);
			//หาชื่อระดับตำแหน่ง                                                                                                                                                                                    		
			if($PER_TYPE==1){
				$NEW_POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
			}elseif($PER_TYPE==2){
				$NEW_POSITION_TYPE = $arr_temp[0];
			}elseif($PER_TYPE==3){
				$NEW_POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
			}
			
			if ($PN_CODE) {
				$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PN_SHORTNAME = $data2[PN_SHORTNAME];
			}

			if($PER_TYPE==1){
				$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
								 from 		PER_POSITION a, PER_LINE b, PER_ORG c
								 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = $data2[POS_NO];
				$PL_NAME = trim($data2[PL_NAME]);
				$ORG_NAME = trim($data2[ORG_NAME]);
				$PT_CODE = trim($data2[PT_CODE]);
				$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
				$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
			}elseif($PER_TYPE==2){
				$cmd = " select 	b.PN_NAME, c.ORG_NAME 
								 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
				$ORG_NAME = trim($data2[ORG_NAME]);
			}elseif($PER_TYPE==3){
				$cmd = " select 	b.EP_NAME, c.ORG_NAME 
								 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
				if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
				$ORG_NAME = trim($data2[ORG_NAME]);
			} // end if
			
			// หา status ของรายการประเมิน (=1 ประเมินเสร็จแล้ว) ของตัวเอง
			$cmd = " select CF_STATUS from PER_COMPETENCY_FORM where KF_ID = $KF_ID And CF_PER_ID = $SELF_PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TYPE1=($data2[CF_STATUS] == 1 ? "ประเมินแล้ว" : "ยังไม่ได้ประเมิน");

			// หา status ของรายการประเมิน (=1 ประเมินเสร็จแล้ว) ของผู้บังคับบัญชา
			$cmd = " select CF_STATUS from PER_COMPETENCY_FORM where KF_ID = $KF_ID And CF_PER_ID = $CHIEF_PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TYPE2=(!$data2 ? "-" : ($data2[CF_STATUS] == 1 ? "ประเมินแล้ว" : "ยังไม่ได้ประเมิน"));

			// หา status ของรายการประเมิน (=1 ประเมินเสร็จแล้ว) ของผู้ใต้บังคับบัญชา
	    	$SUB_SUM_STAT1 = 0;
	    	$SUB_SUM_ALL = 0;
			$cmd = " select PER_ID from PER_KPI_FORM where CHIEF_PER_ID=$SELF_PER_ID ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$cmd = " select CF_STATUS from PER_COMPETENCY_FORM where KF_ID = $KF_ID And CF_PER_ID = $data2[PER_ID] ";
				$db_dpis3->send_cmd($cmd);
				if ($data3 = $db_dpis3->get_array()) {
					if ($data3[CF_STATUS] == 1) {  $SUB_SUM_STAT1++; }
				}
				$SUB_SUM_ALL++;
			}
			$TYPE4=($SUB_SUM_ALL == 0 ? "-" : ($SUB_SUM_STAT1==$SUB_SUM_ALL ? "ประเมินครบแล้ว($SUB_SUM_STAT1 คน)" : "$SUB_SUM_STAT1 / $SUB_SUM_ALL"));

			// หา status ของรายการประเมิน (=1 ประเมินเสร็จแล้ว) ของเพื่อนร่วมงาน
			$TYPE3="-";
			if ($FRIEND_FLAG=="Y") {
		    	$FRIEND_SUM_STAT1 = 0;
		    	$FRIEND_SUM_ALL = 0;
				$cmd = " select PER_ID from PER_KPI_FORM where ORG_ID_KPI=$ORG_ID_KPI ";
				$db_dpis2->send_cmd($cmd);
				while ($data2 = $db_dpis2->get_array()) {
					if ($data2[PER_ID]!=$CHIEF_PER_ID && $data2[PER_ID]!=$SELF_PER_ID) {
						$cmd = " select CF_STATUS from PER_COMPETENCY_FORM where KF_ID = $KF_ID And CF_PER_ID = $data2[PER_ID] ";
						$db_dpis3->send_cmd($cmd);
						if ($data3 = $db_dpis3->get_array()) {
								if ($data3[CF_STATUS] == 1) {  $FRIEND_SUM_STAT1++; }
						}
						$FRIEND_SUM_ALL++;
					}
				}
				$TYPE3=($FRIEND_SUM_ALL == 0 ? "-" : ($FRIEND_SUM_ALL==$FRIEND_SUM_STAT1 ? "ประเมินครบแล้ว ($FRIEND_SUM_STAT1 คน)" :  "$FRIEND_SUM_STAT1 / $FRIEND_SUM_ALL"));
			}

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, $PER_ID, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, (($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME).$PER_NAME." ".$PER_SURNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2, $PL_NAME." ".$ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, $TYPE1, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, $TYPE2, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, $TYPE3, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, $TYPE4, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			
		} // end while
	}else{
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

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>