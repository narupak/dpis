<?
	include("../../php_scripts/connect_database.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("ข้อมูลมาตรฐานกำหนดตำแหน่ง");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$worksheet->set_column(0, 0, 30);
	$worksheet->set_column(1, 1, 150);

	function print_header($heading_text1, $heading_text2){
		global $worksheet, $xlsRow;
		
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$heading_text1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "$heading_text2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
	} // end if

	$cmd = " select PL_CODE, LEVEL_NO from POS_DES_INFO where POS_DES_ID=$POS_DES_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_CODE = trim($data[PL_CODE]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	
	$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PL_NAME = trim($data[PL_NAME]);
	
	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);

	$cmd = " select 	a.ACC_TYPE_ID, b.ACC_TYPE_NAME
					 from		ACCOUNTABILITY_LEVEL_TYPE a, ACCOUNTABILITY_TYPE b
					 where	a.ACC_TYPE_ID=b.ACC_TYPE_ID and a.PL_CODE='$PL_CODE' and a.LEVEL_NO='$LEVEL_NO'
					 order by a.ACC_TYPE_ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) { 
		$ARR_ACCOUNT_TYPE[] = $data[ACC_TYPE_ID];
		$ARR_ACCOUNT_TYPE_NAME[$data[ACC_TYPE_ID]] = trim($data[ACC_TYPE_NAME]);
	} // loop while

	$xlsRow = 0;
	$worksheet->write($xlsRow, 0, "ข้อมูลมาตรฐานกำหนดตำแหน่ง - $PL_NAME - $LEVEL_NAME", set_format("xlsFmtTitle", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TLRB", 1));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "หน้าที่ความรับผิดชอบของตำแหน่ง", set_format("xlsFmtTitle", "B", "C", "TLRB", 1));
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TLRB", 1));

	$cmd = "	select		POS_JOB_DES_INFO
						from		POS_JOB_DES_INFO
						where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='a' ";
	$count_info = $db_dpis->send_cmd($cmd);
	if($count_info){
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ข้อมูลทั่วไป", set_format("xlsFmtSubTitle", "B", "L", "TLB", 0, "black"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "TRB", 0));

		$data = $db_dpis->get_array();
		$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);
		$arr_temp = explode("<br />", nl2br($POS_JOB_DES_INFO));

		for($i=0; $i<count($arr_temp); $i++){
			$content_value = trim($arr_temp[$i]);
			$xlsRow++;
			if($i == 0) $border = "TLR";
			elseif($i == (count($arr_temp) - 1)) $border = "LRB";
			else $border = "LR";
			if(strlen($content_value) > 150) $worksheet->set_row($xlsRow, 20 * ceil(strlen($content_value) / 150));
			$worksheet->write_string($xlsRow, 0, $content_value, set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
			$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
		} // loop for

	} // end if

	foreach($ARR_ACCOUNT_TYPE as $ACC_TYPE_ID){
		$xlsRow++;
		$worksheet->write($xlsRow, 0, $ARR_ACCOUNT_TYPE_NAME[$ACC_TYPE_ID], set_format("xlsFmtSubTitle", "B", "L", "TLB", 0, "black"));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "TRB", 0));

		$cmd = "	select		ACC_DESCRIPTION
							from		ACCOUNTABILITY_INFO_PRIMARY
							where		POS_DES_ID=$POS_DES_ID and ACC_TYPE_ID=$ACC_TYPE_ID
							order by ACC_ID ";
		$db_dpis->send_cmd($cmd);
		$ACC_COUNT = 0;
		while($data = $db_dpis->get_array()){
			$ACC_COUNT++;
			$ACC_DESCRIPTION = $ACC_COUNT." .". trim($data[ACC_DESCRIPTION]);
				
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, $ACC_DESCRIPTION, set_format("xlsFmtTableDetail", "", "L", "TLB", 1,"","",0,"","","",1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TRB", 1,"","",0,"","","",1));
		} // loop while
	} // loop foreach

	$ARR_JOB_TYPE = array("k", "s", "e");
	foreach($ARR_JOB_TYPE as $JOB_TYPE){
		if($JOB_TYPE == "k"){
			// ความรู้ที่จำเป็นในงาน
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ความรู้ที่จำเป็นในงาน", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1, "black"));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1));

			$cmd = "	select		POS_JOB_DES_INFO
								from		POS_JOB_DES_INFO
								where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='k' ";
			$count_info = $db_dpis->send_cmd($cmd);
			if($count_info){
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "ข้อมูลทั่วไป", set_format("xlsFmtSubTitle", "B", "L", "TLB", 0, "black"));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "TRB", 0));

				$data = $db_dpis->get_array();
				$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);
				$arr_temp = explode("<br />", nl2br($POS_JOB_DES_INFO));
		
				for($i=0; $i<count($arr_temp); $i++){
					$content_value = trim($arr_temp[$i]);
					$xlsRow++;
					if($i == 0) $border = "TLR";
					elseif($i == (count($arr_temp) - 1)) $border = "LRB";
					else $border = "LR";
					if(strlen($content_value) > 150) $worksheet->set_row($xlsRow, 20 * ceil(strlen($content_value) / 150));
					$worksheet->write_string($xlsRow, 0, $content_value, set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
					$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
				} // loop for

			} // end if

			$heading_text1 = "ชื่อความรู้ที่จำเป็นในงาน";
			$heading_text2 = "ระดับที่จำเป็นในงาน";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION
								from		POS_JOB_DES_PRIMARY a, KNOWLEDGE_INFO b, KNOWLEDGE_LEVEL c
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
												and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
								order by a.POS_JOB_DES_PRI_ID ";
		}elseif($JOB_TYPE == "s"){
			// ทักษะที่จำเป็นในงาน
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ทักษะที่จำเป็นในงาน", set_format("xlsFmtSubTitle", "B", "C", "", 1, "black"));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "C", "", 1));

			$heading_text1 = "ทักษะที่จำเป็นในงาน";
			$heading_text2 = "ระดับที่จำเป็นในงาน";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION
								from		POS_JOB_DES_PRIMARY a, SKILL_INFO b, SKILL_LEVEL c
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
												and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
								order by a.POS_JOB_DES_PRI_ID ";
		}elseif($JOB_TYPE == "e"){
			// ประสบการณ์ที่จำเป็นในงาน
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ประสบการณ์ที่จำเป็นในงาน", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1, "black"));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1));

			$cmd = "	select		POS_JOB_DES_INFO
								from		POS_JOB_DES_INFO
								where		POS_DES_ID=$POS_DES_ID and POS_JOB_DES_TYPE='e' ";
			$count_info = $db_dpis->send_cmd($cmd);
			if($count_info){
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "ข้อมูลทั่วไป", set_format("xlsFmtSubTitle", "B", "L", "TLB", 0, "black"));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "TRB", 0));

				$data = $db_dpis->get_array();
				$POS_JOB_DES_INFO = trim($data[POS_JOB_DES_INFO]);
				$arr_temp = explode("<br />", nl2br($POS_JOB_DES_INFO));
		
				for($i=0; $i<count($arr_temp); $i++){
					$content_value = trim($arr_temp[$i]);
					$xlsRow++;
					if($i == 0) $border = "TLR";
					elseif($i == (count($arr_temp) - 1)) $border = "LRB";
					else $border = "LR";
					if(strlen($content_value) > 150) $worksheet->set_row($xlsRow, 20 * ceil(strlen($content_value) / 150));
					$worksheet->write_string($xlsRow, 0, $content_value, set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "$border", 0,"","",0,"","","",1));
					$worksheet->merge_cells($xlsRow, 0, $xlsRow, 1);
				} // loop for

			} // end if

			$heading_text1 = "ชื่อประสบการณ์ที่จำเป็นในงาน";
			$heading_text2 = "จำนวน (ปี)";

			$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, a.JOB_DES_LEVEL
								from		POS_JOB_DES_PRIMARY a, EXP_INFO b
								where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
								order by a.POS_JOB_DES_PRI_ID ";
		} // end if

		print_header($heading_text1, $heading_text2);

		$count_info = $db_dpis->send_cmd($cmd);
		if($count_info){
			$info_count = 0;
			while($data = $db_dpis->get_array()){
				$info_count++;
				$JOB_DES_NAME = trim($data[JOB_DES_NAME]);
				if($JOB_TYPE == "e")	$JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL]);
				else $JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL_DESCRIPTION]);

				$xlsRow++;
				$worksheet->write_string($xlsRow, 0, $JOB_DES_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1,"","",0,"","","",1));
				$worksheet->write($xlsRow, 1, $JOB_DES_LEVEL_DESCRIPTION, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1,"","",0,"","","",1));
			} // loop while
		} // end if
	} // loop foreach

	// สมรรถนะที่จำเป็นในงาน
	$JOB_TYPE = "c";
	$xlsRow++;
	$worksheet->write($xlsRow, 0, "สมรรถนะที่จำเป็นในงาน", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1, "black"));
	$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "C", "TLRB", 1));

	$heading_text1 = "ชื่อสมรรถนะที่จำเป็นในงาน";
	$heading_text2 = "ระดับที่จำเป็นในงาน";

	$cmd = "	select		a.POS_JOB_DES_PRI_ID, b.JOB_DES_NAME, c.JOB_DES_LEVEL_DESCRIPTION,
										b.COMPETENCY_TYPE
						from		POS_JOB_DES_PRIMARY a, COMPETENCY_INFO b, COMPETENCY_LEVEL c
						where		a.POS_DES_ID=$POS_DES_ID and a.JOB_TYPE='$JOB_TYPE' and a.JOB_DES_ID=b.JOB_DES_ID 
										and a.JOB_DES_ID=c.JOB_DES_ID and a.JOB_DES_LEVEL=c.JOB_DES_LEVEL
						order by b.COMPETENCY_TYPE desc, a.POS_JOB_DES_PRI_ID ";

	print_header($heading_text1, $heading_text2);

	$count_info = $db_dpis->send_cmd($cmd);
	if($count_info){
		$info_count = 0;
		while($data = $db_dpis->get_array()){
			$info_count++;
			$JOB_DES_NAME = trim($data[JOB_DES_NAME]);
			$JOB_DES_LEVEL_DESCRIPTION = trim($data[JOB_DES_LEVEL_DESCRIPTION]);

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, $JOB_DES_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1,"","",0,"","","",1));
			$worksheet->write($xlsRow, 1, $JOB_DES_LEVEL_DESCRIPTION, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1,"","",0,"","","",1));
		} // loop while
	} // end if

	$workbook->close();

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"ข้อมูลมาตรฐานกำหนดตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"ข้อมูลมาตรฐานกำหนดตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>