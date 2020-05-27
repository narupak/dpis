<?
//	session_start();	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if (!$font) $font = "AngsanaUPC";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$fname= "rpt_data_mgt_competency_assessment_RTF.rtf";

//	$RTF = new RTF();
			$RTF = new RTF("a4", 1800, 1100, 600, 200);
//	$RTF = new RTF("a4", 1200, 1100, 600, 200);
	$RTF->set_default_font($font, 16);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($flag_type==1){	//วันที่ประเมิน 
		$HEAD_01_1 = "เข้ารับการประเมินวันที่ ";
		$FOOT_01_1 = "วันที่เข้ารับการประเมิน ";
	}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
		$HEAD_01_1 = "วันที่ขึ้นบัญชี ";
		$FOOT_01_1 = "วันที่ขึ้นบัญชี ";
	}
	
	$arr_condi = (array) null;
	if ($SEARCH_CA_ID) $arr_condi[] = "CA_ID = $SEARCH_CA_ID";
	else {
		if($flag_type==1){	//วันที่ประเมิน 
			$temp_start =  save_date($search_test_date_from);
			$temp_end =  save_date($search_test_date_to);
			if ($temp_start) $arr_condi[] = "CA_TEST_DATE>='$temp_start'";
			if ($temp_end) $arr_condi[] = "CA_TEST_DATE<='$temp_end'";
		}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
			$temp_start =  save_date($search_approve_date_from);
			$temp_end =  save_date($search_approve_date_to);
			if ($temp_start) $arr_condi[] = "CA_APPROVE_DATE>='$temp_start'";
			if ($temp_end) $arr_condi[] = "CA_APPROVE_DATE<='$temp_end'";
		}
//	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	}
	if (count($arr_condi) > 0) 	$search_condition 	= " and " . implode(" and ", $arr_condi);

	$cmd = "select CA_COURSE, ORG_CODE, CA_SEQ, CA_CODE, CA_TYPE, PER_ID, CA_TEST_DATE,
							CA_APPROVE_DATE, PN_CODE, CA_NAME, CA_SURNAME, CA_CARDNO, CA_CONSISTENCY, CA_SCORE_1, CA_SCORE_2,
							CA_SCORE_3, CA_SCORE_4, CA_SCORE_5, CA_SCORE_6, CA_SCORE_7, CA_SCORE_8, CA_SCORE_9, CA_SCORE_10,
							CA_SCORE_11, CA_SCORE_12, CA_MEAN, CA_MINISTRY_NAME, CA_DEPARTMENT_NAME, CA_ORG_NAME, CA_ORG_NAME1,
							CA_ORG_NAME2, CA_LINE, LEVEL_NO, CA_MGT, CA_NEW_SCORE_1, CA_NEW_SCORE_2, CA_NEW_SCORE_3, CA_NEW_SCORE_4,
							CA_NEW_SCORE_5, CA_NEW_SCORE_6, CA_NEW_SCORE_7, CA_NEW_SCORE_8, CA_NEW_SCORE_9, CA_NEW_SCORE_10,
							CA_NEW_SCORE_11, CA_NEW_MEAN, CA_REMARK, UPDATE_USER, UPDATE_DATE 
							from PER_MGT_COMPETENCY_ASSESSMENT
							where CA_CODE IS NOT NULL
							$search_condition
							$order_str ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";	
//	$db_dpis->show_error();exit;

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$CA_COURSE = $data[CA_COURSE];
			$ORG_CODE = trim($data[ORG_CODE]);
			$CA_SEQ = $data[CA_SEQ];
			$CA_CODE = trim($data[CA_CODE]);
			$CA_TYPE = $data[CA_TYPE];
			$PER_ID = $data[PER_ID];
			$CA_TEST_DATE = show_date_format($data[CA_TEST_DATE], 3);
			$CA_APPROVE_DATE = show_date_format($data[CA_APPROVE_DATE], 3);
			$PN_CODE = trim($data[PN_CODE]);
			$CA_NAME = trim($data[CA_NAME]);
			$CA_SURNAME = trim($data[CA_SURNAME]);
			$CA_CARDNO = trim($data[CA_CARDNO]);
			$CA_CONSISTENCY = $data[CA_CONSISTENCY];
			$CA_SCORE_1 = $data[CA_SCORE_1];
			$CA_SCORE_2 = $data[CA_SCORE_2];
			$CA_SCORE_3 = $data[CA_SCORE_3];
			$CA_SCORE_4 = $data[CA_SCORE_4];
			$CA_SCORE_5 = $data[CA_SCORE_5];
			$CA_SCORE_6 = $data[CA_SCORE_6];
			$CA_SCORE_7 = $data[CA_SCORE_7];
			$CA_SCORE_8 = $data[CA_SCORE_8];
			$CA_SCORE_9 = $data[CA_SCORE_9];
			$CA_SCORE_10 = $data[CA_SCORE_10];
			$CA_SCORE_11 = $data[CA_SCORE_11];
			$CA_SCORE_12 = $data[CA_SCORE_12];
			$CA_MEAN = $data[CA_MEAN];
			$CA_MINISTRY_NAME = trim($data[CA_MINISTRY_NAME]);
			$CA_DEPARTMENT_NAME = trim($data[CA_DEPARTMENT_NAME]);
			$CA_ORG_NAME = trim($data[CA_ORG_NAME]);
			$CA_ORG_NAME1 = trim($data[CA_ORG_NAME1]);
			$CA_ORG_NAME2 = trim($data[CA_ORG_NAME2]);
			$CA_LINE = trim($data[CA_LINE]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$CA_MGT = trim($data[CA_MGT]);
			$CA_NEW_SCORE_1 = $data[CA_NEW_SCORE_1];
			$CA_NEW_SCORE_2 = $data[CA_NEW_SCORE_2];
			$CA_NEW_SCORE_3 = $data[CA_NEW_SCORE_3];
			$CA_NEW_SCORE_4 = $data[CA_NEW_SCORE_4];
			$CA_NEW_SCORE_5 = $data[CA_NEW_SCORE_5];
			$CA_NEW_SCORE_6 = $data[CA_NEW_SCORE_6];
			$CA_NEW_SCORE_7 = $data[CA_NEW_SCORE_7];
			$CA_NEW_SCORE_8 = $data[CA_NEW_SCORE_8];
			$CA_NEW_SCORE_9 = $data[CA_NEW_SCORE_9];
			$CA_NEW_SCORE_10 = $data[CA_NEW_SCORE_10];
			$CA_NEW_SCORE_11 = $data[CA_NEW_SCORE_11];
			$CA_NEW_MEAN = $data[CA_NEW_MEAN];
			$CA_REMARK = trim($data[CA_REMARK]);

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE) = '$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			if ($data_count > 1) $RTF->new_page();

			$logo_path = "../images/";
			$logo_file = $logo_path."logo.jpg";
	//		echo "1.-".$logo_file." @@@@ (".file_exists($logo_file).")<br>";

			// เริ่มตาราง
			$RTF->ln();			
			$RTF->set_table_font($font, 16);

//			$RTF->add_image($img_name, $img_ratio, $align);
//			$RTF->paragraph();
			$RTF->open_line();			
			$RTF->cellImage("$logo_file", 150, 500, "center");
			$RTF->close_line();

			if($flag_type==1){	//วันที่ประเมิน 
				$HEAD_01_2 = "$CA_TEST_DATE";
			}else if($flag_type==2){	//วันที่ขึ้นทะเบียน
				$HEAD_01_2 = "$CA_APPROVE_DATE";
			}
			$HEAD_01_4 = "รหัส ";
			$HEAD_01_5 = "$CA_CODE";
			$HEAD_02_4 = "ชื่อ  ";
			$HEAD_02_5 = "$PN_NAME$CA_NAME $CA_SURNAME";		

			$RTF->open_line();			
			$RTF->cell(($NUMBER_DISPLAY==2)?convert2thaidigit(($HEAD_01_1." ".$HEAD_01_2)):($HEAD_01_1." ".$HEAD_01_2), "100", "left", "0");
			$RTF->cell("", "40", "center", "0");
			$RTF->cell(($NUMBER_DISPLAY==2)?convert2thaidigit(($HEAD_01_4." ".$HEAD_01_5)):($HEAD_01_4." ".$HEAD_01_5), "60", "left", "0");
			$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("", "100", "left", "0");
			$RTF->cell("", "40", "center", "0");
			$RTF->cell(($NUMBER_DISPLAY==2)?convert2thaidigit(($HEAD_02_4." ".$HEAD_02_5)):($HEAD_02_4." ".$HEAD_02_5), "60", "left", "0");
			$RTF->close_line();

			$RTF->ln();			
			$RTF->open_line();			
			$RTF->cell("", "30", "left", "0");
			$RTF->cell(" รายงานผลการประเมินสมรรถนะหลักทางการบริหาร ", "140", "center", "0", "TLBR");
			$RTF->cell("", "30", "right", "0");
			$RTF->close_line();
			$RTF->ln();			

			$RTF->open_line();			
			$RTF->cell("", "5", "left", "0");
			$RTF->cell("รายงานนี้เป็นการอธิบายผลการประเมินสมรรถนะหลักทางการบริหาร ซึ่งเป็น", "195", "left", "0");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("คุณลักษณะที่จำเป็นสำหรับตำแหน่งนักบริหารระดับสูง", "200", "left", "0");
			$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("", "5", "left", "0");
			$RTF->cell("ผลการประเมินมี 3 ระดับ คือ", "195", "left", "0");
			$RTF->close_line();
			
			$RTF->open_line();			
			$RTF->cell("", "5", "left", "0");
			$RTF->cell("1. 75.00 % ขึ้นไป", "65", "left", "0");
			$RTF->cell("หมายถึง  มีสมรรถนะนี้ดีแล้ว", "130", "left", "0");
			$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("", "5", "left", "0");
			$RTF->cell("2. 60.00 – 74.99 %", "65", "left", "0");
			$RTF->cell("หมายถึง  มีสมรรถนะนี้แล้วในระดับกลาง แต่ควรพัฒนาเพิ่มขึ้น", "130", "left", "0");
			$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("", "5", "left", "0");
			$RTF->cell("3. ต่ำกว่า 60.00 %", "65", "left", "0");
			$RTF->cell("หมายถึง  มีความจำเป็นต้องพัฒนาสมรรถนะนี้", "130", "left", "0");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("สมรรถนะหลักทางการบริหาร", "159", "left", "0", "TLBR");
			$RTF->cell("ผลการประเมิน", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("1. ความรอบรู้ในการบริหาร", "159", "left", "0", "TLBR");
			$RTF->cell("", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การบริหารการเปลี่ยนแปลง (Managing Change)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_1", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การมีจิตมุ่งบริการ (Customer Service Orientation)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_2", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การวางแผนเชิงกลยุทธ์ (Strategic Planning)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_3", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("2. การบริหารอย่างมืออาชีพ", "159", "left", "0", "TLBR");
			$RTF->cell("", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การตัดสินใจ (Decision Making)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_4", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การคิดเชิงกลยุทธ์ (Strategic Thinking)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_5", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • ความเป็นผู้นำ (Leadership)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_6", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("3. การบริหารคน", "159", "left", "0", "TLBR");
			$RTF->cell("", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การปรับตัวและความยืดหยุ่น (Adaptability and Flexibility)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_7", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • ความสามารถและทักษะในการสื่อสาร (Communication)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_8", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การประสานสัมพันธ์ (Collaborative)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_9", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("4. การบริหารแบบมุ่งผลสัมฤทธิ์", "159", "left", "0", "TLBR");
			$RTF->cell("", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การรับผิดชอบตรวจสอบได้ (Accountability)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_10", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การทำงานให้บรรลุผลสัมฤทธิ์ (Achieving Result)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_11", "40", "center", "0", "TLBR");
			$RTF->close_line();
			
			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("  • การบริหารทรัพยากร (Managing Resources)", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_SCORE_12", "40", "center", "0", "TLBR");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell("ผลเฉลี่ยการประเมินสมรรถนะหลักทางการบริหาร", "159", "left", "0", "TLBR");
			$RTF->cell("$CA_MEAN", "40", "center", "0", "TLBR");
			$RTF->close_line();
			
			$RTF->open_line();	
			$RTF->cell("", "135", "left", "0");
			$RTF->cell("ลงชื่อ _____________________", "65", "center", "0", "");
			$RTF->close_line();
			
			$RTF->open_line();	
			$RTF->cell("เจ้าหน้าที่ตรวจทานผลการประเมินฯ", "200", "right", "0");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "135", "left", "0");
			$RTF->cell("วันที่ _____________________", "65", "center", "0", "");
			$RTF->close_line();

			$RTF->open_line();	
			$RTF->cell("", "1", "left", "0");
			$RTF->cell(" หมายเหตุ ผลการประเมินสมรรถนะหลักทางการบริหารใช้ได้ 2 ปี นับแต่$FOOT_01_1", "195", "left", "0", "");
			$RTF->close_line();
		} // end while
	}else{
		$RTF->open_line();	
		$RTF->cell("***** ไม่มีข้อมูล *****", "200", "center", "0", "TLBR");
		$RTF->close_line();
	} // end if

	$RTF->display($fname);
	
	ini_set("max_execution_time", 60);
?>