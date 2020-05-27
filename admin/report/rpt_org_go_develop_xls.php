<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$arr_content = (array) null;
	$arr_target = (array) null;
	$arr_percent = (array) null;
	$arr_cftype = (array) null;
	$arr_N = (array) null;

	if ($DEPARTMENT_ID == "total") {
		$txt_dept = "";
	} else {
		$txt_dept = "and DEPARTMENT_ID=$DEPARTMENT_ID";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$txt_dept = $txt_dept." and ORG_ID=$ORG_ID";
		}
	}
	$cmd = "  select  a.*, b.*, b.KF_ID as CF_KF_ID  from PER_KPI_FORM a, PER_COMPETENCY_FORM b
					where KF_CYCLE=$KF_CYCLE and KF_START_DATE='$KF_START_DATE' $txt_dept and PER_ID=CF_PER_ID
					order by CF_PER_ID, CF_TYPE ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$arr_cftype = (array) null;
	while ($data = $db_dpis->get_array()) {
		if (!$KF_YEAR) {
			$KF_YEAR = substr($data[KF_END_DATE], 0, 4) + 543;
		}
		if ($CF_PER_ID != $data[CF_PER_ID]) {
			$CF_PER_ID = $data[CF_PER_ID];
			// อ่านระดับของผู้ถูกประเมิน
			$cmd1 = " select 	LEVEL_NO from PER_PERSONAL where PER_ID=$CF_PER_ID ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CF_LEVEL_NO = trim($data2[LEVEL_NO]);
		}
		$CF_TYPE = $data[CF_TYPE];
		// รายงานนี้แสดงยอดสรุปการประเมินตามผู้ถูกประเมิน ความหมายจึงเป็นตรงกันข้ามดังนี้ 
		//															1 ตนเองประเมินตนเอง 2 ผู้ใต้บังคับบัญชาประเมิน 3 เพื่อนประเมิน 4 ผู้บังคับบัญชาประเมิน
		// จากที่เห็นจึงต้องเปลี่ยน $CF_TYPE เป็นตรงกันข้าม ซึ่งมีที่ต้องสลับ 2 ตัวคือค่า 2 เป็น 4 และ 4 เป็น 2 รายงานจะจะลงถูกความหมาย
		if ($CF_TYPE==2) { $CF_TYPE=4; } else if ($CF_TYPE==4) { $CF_TYPE=2; }
		// จัดเตรียมค่า cf_type array เพื่อการอ่าน Percent น้ำหนัก
		if (!in_array($CF_TYPE, $arr_cftype)) {
			$arr_cftype[] = $CF_TYPE;
		}
		if ($CF_TYPE == 1) { $CF_KF_ID = $data[CF_KF_ID]; }
			
//		$CF_SCORE = $data2[CF_SCORE];
		$CF_SCORE = $data[CF_SCORE];
		$ARR_POINT = explode(",",$CF_SCORE);
		for($score_i = 0; $score_i < count($ARR_POINT); $score_i++) {
			$POINT_K = explode(":",$ARR_POINT[$score_i]);
			$CP_CODE = $POINT_K[0];
			$POINT = $POINT_K[1];
//			echo "$CP_CODE:$POINT<br>";

//			if (!$arr_target[$CF_PER_ID][$CP_CODE]) {
				$cmd2 = " select PC_TARGET_LEVEL, KC_EVALUATE, KC_WEIGHT from PER_KPI_COMPETENCE 
								  where KF_ID=$CF_KF_ID and CP_CODE='$CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
				$PC_TARGET_LEVEL = $data3[PC_TARGET_LEVEL];
				$KC_EVALUATE = $data3[KC_EVALUATE];
//			}

			$arr_target[$CF_PER_ID][$CP_CODE] = $PC_TARGET_LEVEL;
//			$arr_content[$CF_PER_ID][$CP_CODE][$CF_TYPE] = $arr_content[$CF_PER_ID][$CP_CODE][$CF_TYPE] + $POINT;
//			$arr_N[$CF_PER_ID][$CP_CODE][$CF_TYPE]++;
			$arr_content[$CF_PER_ID][$CP_CODE] = $KC_EVALUATE;
//			echo "weight[$CF_PER_ID][$CP_CODE]="+$KC_EVALUATE+"<br>";
//			echo "[$CF_PER_ID][$CP_CODE][$CF_TYPE]<br>";
		} //  end for $score_i
		// เรียงชุดของ cftype เพื่อไปต่อทำเป็น key ในการเรียก per_pos_type
		array_multisort($arr_cftype, SORT_ASC);
		//	echo implode("",$arr_cftype)+"|";
		$postype=substr($CF_LEVEL_NO,0,1).implode("",$arr_cftype);
		//	echo "LEVEL_NO=$CF_LEVEL_NO<br>";
		$cmd1 = " SELECT * FROM PER_POS_TYPE WHERE POS_TYPE='$postype' ";
		$db_dpis2->send_cmd($cmd1);
		//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$arr_percent[$CF_PER_ID][1] = $data2[SEFT_RATIO];
		$arr_percent[$CF_PER_ID][2] = $data2[CHIEF_RATIO];
		$arr_percent[$CF_PER_ID][3] = $data2[FRIEND_RATIO];
		$arr_percent[$CF_PER_ID][4] = $data2[SUB_RATIO];
	} // end while read PER_KPI_FORM
	
	if(count($arr_content) > 0) {	
		$arr_per_gap = (array) null;;
		$arr_per_n = (array) null;;
		foreach($arr_content as $I_PER_ID => $val) {
			foreach($val as $CP_CODE => $val1) {
				$cnt_col=1;
				$tot = 0;
//				for($p=1; $p <= 4; $p++) { // loop ตาม $CF_TYPE
//					$cnt_col++;
//					$avg = (!$val1[$p] ? 0 : $val1[$p]) / $arr_N[$I_PER_ID][$CP_CODE][$p];
//					$avg_perc = $avg * $arr_percent[$I_PER_ID][$p] / 100; // % รวมในแต่ละ cf_type cp_code และ แต่ละ บุคคล
//				} // end for $CF_TYPE
//				$gap = floor(abs($arr_target[$I_PER_ID][$CP_CODE] - $avg_perc))+1; 
				// gap ผลค่าง ระหว่าง เป้าหมายตามการประเมินรายบุคคล กับ % คะแนนรวมแต่ละบุคคล ทำเป๋น + แล้วปัดเศษออก
				$gaptemp = $arr_content[$I_PER_ID][$CP_CODE] - $arr_target[$I_PER_ID][$CP_CODE]; 
				if ($gaptemp < 0) {
					$weight_int = ceil($arr_content[$I_PER_ID][$CP_CODE]);
					$gap = ($weight_int == $arr_content[$I_PER_ID][$CP_CODE] ? $weight_int+1 : $weight_int);
					$arr_per_gap[$CP_CODE][$gap]++;
//					echo "[$CP_CODE][$gap]".$arr_per_gap[$CP_CODE][$gap]."<br>";
				}
			} // end foreach
		} // end foreach $I_PER_ID
	} // if(count($arr_content) > 0)
	
	if ($DEPARTMENT_ID == "total") {
		$head_dept = "";
	} else {
		$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$DEPARTMENT_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
		$db_dpis2->send_cmd($cmd1);
		//	$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$head_dept = "$data2[ORG_NAME]";
		if ($ORG_ID <> $DEPARTMENT_ID) {
			$cmd1 = " SELECT * FROM PER_ORG WHERE ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd1);
			$db_dpis2->send_cmd($cmd1);
		//		$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$head_dept = $head_dept."  $data2[ORG_NAME]";
		}
	}	
	$report_title = "ตารางแสดงจำนวนระดับที่ต้องพัฒนาของบุคคลแยกตามสมรรถนะและระดับ ของข้าราชการ $head_dept";
	$company_name = "ประจำรอบการประเมินครั้งที่ $KF_CYCLE พ.ศ. $KF_YEAR ";
	$report_code = "R_ORG_Go_Develop";

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
		$worksheet->set_column(1, 1, 70);
		$worksheet->set_column(2, 2, 12);
		$worksheet->set_column(3, 3, 12);
		$worksheet->set_column(4, 4, 12);
		$worksheet->set_column(5, 5, 12);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);

		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));
		$worksheet->write($xlsRow, 1, "สมรรถนะ", set_format("xlsFmtTableHeader", "B", "L", "TLR", 1));
		$worksheet->write($xlsRow, 2, "ระดับที่ต้องพัฒนา", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TB", 1));
		$worksheet->write($xlsRow, 7, "รวม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 1));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "3", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 5, "4", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "5", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
	} // function
	
	if(count($arr_per_gap) > 0) {
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$xlsRow++;
		} //if($company_name){
		
		print_header();
		
		$ctot = (array) null;
		$gtotal=0;
		foreach($arr_per_gap as $CP_CODE => $val) {
//			echo "CP_CODE=$CP_CODE<br>";
			$cmd1 = " select * from PER_COMPETENCE where CP_CODE=$CP_CODE ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$CP_NAME = $data2[CP_NAME];
				
			$xlsRow++;
			$seq_no++;
			$worksheet->write($xlsRow, 0, ($seq_no?number_format($seq_no):"-"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1, "$CP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$cnt_col=1;
			$tot = 0;
			for($p=1; $p <= 5; $p++) { // loop ตาม gap
				$cnt_col++;
				$gap_cnt = (!$val[$p] ? "-" : $val[$p]);
				$worksheet->write_string($xlsRow, $cnt_col, "$gap_cnt", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$ctot[$p]=$ctot[$p]+$gap_cnt;
				$tot=$tot+$gap_cnt;
				$gtotal=$gtotal+$gap_cnt;
			} // end for $CF_TYPE
			$worksheet->write($xlsRow, $cnt_col+1, $tot, set_format("xlsFmtTableDetail", "B", "C", "TLRB", 0));
		} // end foreach
//		$ctot[1]=(!$ctot[1] ? "-" : $ctot[1]);
//		$ctot[2]=(!$ctot[2] ? "-" : $ctot[2]);
//		$ctot[3]=(!$ctot[3] ? "-" : $ctot[3]);
//		$ctot[4]=(!$ctot[4] ? "-" : $ctot[4]);
//		$ctot[5]=(!$ctot[5] ? "-" : $ctot[5]);
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "BL", 1));
		$worksheet->write($xlsRow, 1, "รวม", set_format("xlsFmtTableHeader", "B", "C", "B", 1));
		$worksheet->write($xlsRow, 2, "$ctot[1]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 3, "$ctot[2]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 4, "$ctot[3]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 5, "$ctot[4]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 6, "$ctot[5]", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
		$worksheet->write($xlsRow, 7, "$gtotal", set_format("xlsFmtTableHeader", "B", "C", "BLR", 1));
	} else { // else if ($count_data)
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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