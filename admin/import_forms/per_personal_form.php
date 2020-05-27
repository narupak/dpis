<?
//	map text data to table columns

	$table = "PER_PERSONAL";
	// ค่าข้างล่าง ถ้ามีมากกว่า 1 ตัวจะขั้นด้วย |
	$dup_column = "0";	// เลขลำดับตามตารางในฐานข้อมูล 
	$prime = "0";		// เลขลำดับตามตารางในฐานข้อมูล
	$running = "-1";		// เลขลำดับตามตารางในฐานข้อมูล ถ้าไม่มี running ให้ = -1
	
    if (!isset($FixColumn)) $FixColumn = count(explode("|",$dup_column));	// แสดง keycolumn ที่ fix ไว้ไม่เลื่อนไปไหน
    if (!isset($showStartColumn)) $showStartColumn = $FixColumn; // เริ่มแสดงที่ column
	if (!isset($NumShowColumn)) $NumShowColumn = 8;	// จำนวน column ที่แสดง
	
	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "$";
	
	$column_map = (array) null;
	
	// ถ้า column map กันตรงตัว ใช้ ชุดนี้
//	for($i = 0; $i < 79; $i++) {
//		$column_map[] = "$i";	// 0-running number
//	}
	// จบชุด
	
//	$impfile_head_map = array("PER_CARDNO","FNAME","LNAME","INS_CODE","INS_DATE","POS_NAME","DEP_NAME");

//	$head_map = array("ฟิลด์ 1","ฟิลด์ 2","ฟิลด์ 3","ฟิลด์ 4","ฟิลด์ 5","ฟิลด์ 6","ฟิลด์ 7","","ฟิลด์ 9","");

	$column_map = (array) null;
	$column_map[] = "1";
	$column_map[] = "func-s-e-check_per_type(@2)";
	$column_map[] = "sql-s-d-select OT_CODE from PER_ORG_TYPE where OT_NAME='@3'";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@4'";
	$column_map[] = "5";
	$column_map[] = "6";
	$column_map[] = "7";
	$column_map[] = "8";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@9'";
	$column_map[] = "sql-n-d-select POS_ID from PER_POSITION where POS_NO='@10'";
	$column_map[] = "sql-n-d-select POEM_ID from PER_POS_EMP where POEM_NO='@11'";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME='@12'";
	$column_map[] = "13";
	$column_map[] = "14";
	$column_map[] = "15";
	$column_map[] = "16";
	$column_map[] = "func-s-e-check_gender(@17)";
	$column_map[] = "sql-s-d-select MR_CODE from PER_MARRIED where MR_NAME='@18'";
	$column_map[] = "19";
	$column_map[] = "20";
	$column_map[] = "21";
	$column_map[] = "func-s-e-check_blood(@22)";
	$column_map[] = "sql-s-d-select RE_CODE from PER_RELIGION where RE_NAME='@23'";
	$column_map[] = "func-s-e-save_date(@24)";
	$column_map[] = "func-s-e-save_date(@25)";
	$column_map[] = "func-s-e-save_date(@26)";
	$column_map[] = "func-s-e-save_date(@27)";
	$column_map[] = "func-s-e-save_date(@28)";
	$column_map[] = "func-s-e-save_date(@29)";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@30'";
	$column_map[] = "31";
	$column_map[] = "32";
	$column_map[] = "sql-s-d-select PN_CODE from PER_PRENAME where PN_NAME='@33'";
	$column_map[] = "34";
	$column_map[] = "35";
	$column_map[] = "36";
	$column_map[] = "37";
	$column_map[] = "sql-s-d-select PV_CODE from PER_PROVINCE where PV_NAME='@38";
	$column_map[] = "sql-s-d-select MOV_CODE from PER_MOVMENT where MOV_NAME='@39'";
	$column_map[] = "40";
	$column_map[] = "41";
	$column_map[] = "42";
	$column_map[] = "43";
	$column_map[] = "update_user";	// 44-map กับ UPDATE_USER จากระบบ
	$column_map[] = "update_date";	// 45-map กับ UPDATE_DATE จากระบบ
	$column_map[] = "sql-n-d-select POEMS_ID from PER_POS_EMPSER where POEMS_NO='@46'";
	$column_map[] = "47";
	$column_map[] = "48";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@49'";
	$column_map[] = "50";
	$column_map[] = "51";
	$column_map[] = "52";
	$column_map[] = "53";
	$column_map[] = "54";
	$column_map[] = "55";
	$column_map[] = "56";
	$column_map[] = "57";
	$column_map[] = "58";
	$column_map[] = "59";
	$column_map[] = "60";
	$column_map[] = "61";
	$column_map[] = "62";
	$column_map[] = "63";
	$column_map[] = "64";
	$column_map[] = "65";
	$column_map[] = "66";
	$column_map[] = "67";
	$column_map[] = "68";
	$column_map[] = "func-s-e-save_date(@69)";	// MEMBERDATE
	$column_map[] = "70";
	$column_map[] = "71";
	$column_map[] = "sql-n-d-select ES_CODE from PER_EMP_STATUS where ES_NAME='@72'";
	$column_map[] = "73";
	$column_map[] = "74";
	$column_map[] = "75";
	$column_map[] = "func-s-e-save_date(@76)";	// DOCDATE
	$column_map[] = "77";
	$column_map[] = "78";
	$column_map[] = "79";
	$column_map[] = "80";
	$column_map[] = "81";
	$column_map[] = "82";
	$column_map[] = "83";
	$column_map[] = "84";
	$column_map[] = "func-s-e-save_date(@85)";	// PER_POS_DOCDATE
	$column_map[] = "86";
	$column_map[] = "87";
	$column_map[] = "88";
	$column_map[] = "func-s-e-save_date(@89)";	// BOOKDATE
	$column_map[] = "90";
	$column_map[] = "91";
	$column_map[] = "sql-n-d-select POT_ID from PER_POS_TEMP where POT_NO='@92'";
	$column_map[] = "93";
	$column_map[] = "func-s-e-save_date(@94)";	// UNIONDATE
	$column_map[] = "95";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@96'";		// ORG_ID_1
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@97'";		// ORG_ID_2
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@98'";		// ORG_ID_3
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@99'";		// ORG_ID_4
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@100'";	// ORG_ID_5
	$column_map[] = "101";
	$column_map[] = "func-s-e-save_date(@102)";		// UNIONDATE2
	$column_map[] = "103";
	$column_map[] = "func-s-e-save_date(@104)";		// UNOINDATE3
	$column_map[] = "105";
	$column_map[] = "func-s-e-save_date(@106)";		// UNIONDATE4
	$column_map[] = "107";
	$column_map[] = "func-s-e-save_date(@108)";		// UNIONDATE5
	$column_map[] = "109";
	$column_map[] = "110";
	$column_map[] = "111";
	$column_map[] = "sql-n-d-select ORG_ID from PER_ORG where ORG_NAME='@112'";

	// ข้อมูลจาก form
/*	$screenform[] = "selectlistfix^value=1|ข้าราชการ|selected^name=sel_pertype^label=ประเภทบุคคลากร : ";	// text  radio  checkbox  selectlist  datecalendar
	$screenform[] = "text^value=func|thisyear^size=10^name=txt_year^label=ปีงบประมาณ : ";
	$screenform[] = "radio^value=1|ครั้งที่ 1|checked,2|ครั้งที่ 2^name=radio_part";
	$screenform[] = "checkbox^value=1|เป็นประวัติการรับเงินเดือนล่าสุด|checked^name=chk_lastdate^onclick=check_adate^+";	// + ต่อกันกับรายการถัดไป
	$screenform[] = "textcalandar^size=10^name=txtc_lastdate^label=[ วันที่สิ้นสุด ] :นี้";
	$screenform[] = "text^value=^size=20^name=txt_docu^label=คำสั่งเลขที่ : ";
	$screenform[] = "textcalandar^size=10^name=txtc_docudate^label=วันที่ผู้บริหารลงนามในคำสั่ง : ";
*/

	function check_per_type($pertype) {
		if ($pertype>="1" && $pertype<="5") {
			return $pertype;
		} else if ($pertype=="ข้าราชการ") {
			return "1";
		} else if ($pertype=="พนักงานราชการ") {
			return "2";
		} else if ($pertype=="ลูกจ้างชั่วคราว") {
			return "3";
		} else {
			return "**** ค่่าไม่ถูกต้อง *****";
		}
	}
	
	function check_gender($gender) {
		if ($gender=="M" || $gender=="F") {
			return $gender;
		} else if ($gender=="ชาย") {
			return "M";
		} else if ($gender=="หญิง") {
			return "F";
		} else {
			return "**** ค่่าไม่ถูกต้อง *****";
		}
	}

	function check_blood($blood) {
		$blood = strtoupper($blood);
		if ($blood=="AB" || $blood=="A" || $blood=="B" || $blood=="O") {
			return $blood;
		} else {
			return "**** ค่าไม่ถูกต้อง *****";
		}
	}
	
?>
