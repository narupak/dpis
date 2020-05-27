<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$company_name = "";
	$report_title = "สอบถามข้อมูลวิทยากร";
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
		global $POS_NO_TITLE, $FULLNAME_TITLE, $SEQ_NO_TITLE, $SEX_TITLE, $PER_TYPE_TITLE, $BIRTHDATE_TITLE;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 15);
		$worksheet->set_column(3, 3, 15);
		$worksheet->set_column(4, 4, 20);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 30);
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
		$worksheet->set_column(17, 17, 30);
		$worksheet->set_column(18, 18, 30);
		$worksheet->set_column(19, 19, 30);
		$worksheet->set_column(20, 20, 30);
		$worksheet->set_column(21, 21, 30);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$SEQ_NO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "$FULLNAME_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "$SEX_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "$PER_TYPE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "$BIRTHDATE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ประวัติการศึกษา 1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 6, "ประวัติการศึกษา 2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 7, "ประวัติการศึกษา 3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 8, "ตำแหน่งปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "สถานที่ทำงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 10, "โทรศัพท์ที่ทำงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 11, "ประสบการณ์การทำงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 12, "ประวัติการอบรมดูงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 13, "ที่อยู่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 14, "โทรศัพท์", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 15, "ผลงานด้านวิชาการ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 16, "หัวข้อบรรยายที่ถนัด 1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 17, "หัวข้อบรรยายที่ถนัด 2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 18, "หัวข้อบรรยายที่ถนัด 3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 19, "หน่วยงานที่เคยไปบรรยาย", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 20, "ความสามารถพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 21, "งานอดิเรก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));

	} // function		
	
	if(trim($search_name)) $arr_search_condition[] = "(TRAINNER_NAME like '$search_name%')";
  	if(trim($search_train_skill)) $arr_search_condition[] = "(TN_TRAIN_SKILL1 like '$search_train_skill%' || TN_TRAIN_SKILL2 like '$search_train_skill%' || TN_TRAIN_SKILL3 like '$search_train_skill%')";
	if(trim($search_inout_org) < 4) {
		$temp_per_status = $search_inout_org - 1;		
		$arr_search_condition[] = "(TN_INOUT_ORG = $temp_per_status)";	
	} 
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){	
		$cmd = " select 	distinct 	*
						 from 		PER_TRAINNER
						$search_condition
						 $order_str";
	}elseif($DPISDB=="oci8"){
		$cmd = " select 	distinct *
								  from 		PER_TRAINNER
								  $search_condition
								  $order_str";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	distinct	*
							from 	PER_TRAINNER
								$search_condition
								$order_str";
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
//	echo "$cmd ($count_data)<br>";
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TN_INOUT_ORG = trim($data[TN_INOUT_ORG]);

		$TRAINNER_NAME = trim($data[TRAINNER_NAME]);

		$TN_GENDER = trim($data[TN_GENDER]);
		if($TN_GENDER==1) { $TN_GENDER="ชาย"; } elseif ($TN_GENDER==2) { $TN_GENDER="หญิง";}
		$TN_INOUT_ORG = trim($data[TN_INOUT_ORG]);
		if($TN_INOUT_ORG==0) { $TN_INOUT_ORG="ในองค์กร"; } elseif ($TN_INOUT_ORG==1) { $TN_INOUT_ORG="นอกองค์กร";}
		$TN_EDU_HIS1 = trim($data[TN_EDU_HIS1]);
		$TN_EDU_HIS2 = trim($data[TN_EDU_HIS2]);		
		$TN_EDU_HIS3 = trim($data[TN_EDU_HIS3]);
		$TN_POSITION = trim($data[TN_POSITION]);
		$TN_WORK_PLACE = trim($data[TN_WORK_PLACE]);	
		$TN_WORK_TEL = trim($data[TN_WORK_TEL]);
		$TN_WORK_EXPERIENCE = trim($data[TN_WORK_EXPERIENCE]);
		$TN_TRAIN_EXPERIENCE = trim($data[TN_TRAIN_EXPERIENCE]);

		$TN_BIRTHDATE = show_date_format($data[TN_BIRTHDATE], 1);
		$TN_ADDRESS = trim($data[TN_ADDRESS]);
		$TN_ADDRESS_TEL = trim($data[TN_ADDRESS_TEL]);
		$TN_TECHONOLOGY_HIS = trim($data[TN_TECHNOLOGY_HIS]);
		$TN_TRAIN_SKILL1 = trim($data[TN_TRAIN_SKILL1]);
		$TN_TRAIN_SKILL2 = trim($data[TN_TRAIN_SKILL2]);
		$TN_TRAIN_SKILL3 = trim($data[TN_TRAIN_SKILL3]);
		$TN_DEPT_TRAIN = trim($data[TN_DEPT_TRAIN]);
		$TN_SPEC_ABILITY = trim($data[TN_SPEC_ABILITY]);
		$TN_HOBBY = trim($data[TN_HOBBY]);
		$TN_TECHNOLOGY_HIS = trim($data[TN_TECHNOLOGY_HIS]);

		$arr_content[$data_count][tn_id] = $data[TRAINNER_ID];
		$arr_content[$data_count][tn_name] = $TRAINNER_NAME;
		$arr_content[$data_count][tn_gender] = $TN_GENDER;
		$arr_content[$data_count][tn_inout_org] = $TN_INOUT_ORG;
		$arr_content[$data_count][tn_birthdate] = $TN_BIRTHDATE;
		$arr_content[$data_count][tn_edu_his1] = $TN_EDU_HIS1;
		$arr_content[$data_count][tn_edu_his2] = $TN_EDU_HIS2;
		$arr_content[$data_count][tn_edu_his3] = $TN_EDU_HIS3;
		$arr_content[$data_count][tn_pos] = $TN_POSITION;
		$arr_content[$data_count][tn_work_place] = $TN_WORK_PLACE;
		$arr_content[$data_count][tn_work_tel] = $TN_WORK_TEL;
		$arr_content[$data_count][tn_work_experience] = $TN_WORK_EXPERIENCE;
		$arr_content[$data_count][tn_train_experience] = $TN_TRAIN_EXPERIENCE;
		$arr_content[$data_count][tn_address] = $TN_ADDRESS;
		$arr_content[$data_count][tn_address_tel] = $TN_ADDRESS_TEL;
		$arr_content[$data_count][tn_techonology_his] = $TN_TECHONOLOGY_HIS;
		$arr_content[$data_count][tn_train_skill1] = $TN_TRAIN_SKILL1;
		$arr_content[$data_count][tn_train_skill2] = $TN_TRAIN_SKILL2;
		$arr_content[$data_count][tn_train_skill3] = $TN_TRAIN_SKILL3;
		$arr_content[$data_count][tn_dept_train] = $TN_DEPT_TRAIN;
		$arr_content[$data_count][tn_spec_ability] = $TN_SPEC_ABILITY;
		$arr_content[$data_count][tn_spec_ability] = $TN_HOBBY;
				
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
			$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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
		} // end if

		print_header();

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			/* $TRAINNER_ID = $arr_content[$data_count][tn_id];
			$TRAINNER_NAME = $arr_content[$data_count][tn_name];
			$TN_POSITION = $arr_content[$data_count][tn_pos];
			$TN_WORK_PLACE = $arr_content[$data_count][tn_workplace];
			$TN_WORK_TEL = $arr_content[$data_count][tn_wroktel]; */
			
		$TRAINNER_ID = $arr_content[$data_count][tn_id];
		$TRAINNER_NAME = $arr_content[$data_count][tn_name];
		$TN_GENDER = $arr_content[$data_count][tn_gender];
		$TN_INOUT_ORG = $arr_content[$data_count][tn_inout_org];
		$TN_BIRTHDATE = $arr_content[$data_count][tn_birthdate];
		$TN_EDU_HIS1= $arr_content[$data_count][tn_edu_his1];
		$TN_EDU_HIS2 = $arr_content[$data_count][tn_edu_his2];
		$TN_EDU_HIS3 = $arr_content[$data_count][tn_edu_his3];
		$TN_POSITION = $arr_content[$data_count][tn_pos];
		$TN_WORK_PLACE = $arr_content[$data_count][tn_work_place];
		$TN_WORK_TEL = $arr_content[$data_count][tn_work_tel];
		$TN_WORK_EXPERIENCE = $arr_content[$data_count][tn_work_experience];
		$TN_TRAIN_EXPERIENCE = $arr_content[$data_count][tn_train_experience];
		$TN_ADDRESS = $arr_content[$data_count][tn_address];
		$TN_ADDRESS_TEL = $arr_content[$data_count][tn_address_tel];
		$TN_TECHONOLOGY_HIS = $arr_content[$data_count][tn_techonology_his];
		$TN_TRAIN_SKILL1 = $arr_content[$data_count][tn_train_skill1];
		$TN_TRAIN_SKILL2 = $arr_content[$data_count][tn_train_skill2];
		$TN_TRAIN_SKILL3 = $arr_content[$data_count][tn_train_skill3];
		$TN_DEPT_TRAIN = $arr_content[$data_count][tn_dept_train];
		$TN_SPEC_ABILITY = $arr_content[$data_count][tn_spec_ability];
		$TN_HOBBY = $arr_content[$data_count][tn_spec_ability];
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,(($NUMBER_DISPLAY==2)?convert2thaidigit($TRAINNER_ID):$TRAINNER_ID), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$TRAINNER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$TN_GENDER", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$TN_INOUT_ORG", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit($TN_BIRTHDATE):$TN_BIRTHDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$TN_EDU_HIS1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$TN_EDU_HIS2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$TN_EDU_HIS3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$TN_POSITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$TN_WORK_PLACE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 10, (($NUMBER_DISPLAY==2)?convert2thaidigit($TN_WORK_TEL):$TN_WORK_TEL), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 11, "$TN_WORK_EXPERIENCE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 12, "$TN_TRAIN_EXPERIENCE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 13, "$TN_ADDRESS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 14,  (($NUMBER_DISPLAY==2)?convert2thaidigit($TN_ADDRESS_TEL):$TN_ADDRESS_TEL), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 15, "$TN_TECHONOLOGY_HIS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 16, "$TN_TRAIN_SKILL1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 17, "$TN_TRAIN_SKILL2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 18, "$TN_TRAIN_SKILL3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 19, "$TN_DEPT_TRAIN", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 20, "$TN_WORK_TEL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 21, "$TN_SPEC_ABILITY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 21, "$TN_HOBBY", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			
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
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"สอบถามข้อมูลวิทยากร.xls\"");
	header("Content-Disposition: inline; filename=\"สอบถามข้อมูลวิทยากร.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>