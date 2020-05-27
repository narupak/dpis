<?
//	map text data to table columns

	$table = "PER_SLIP";
	$dup_column = "2|3|4";	// เลขลำดับตามตารางในฐานข้อมูล
	$prime = "0";		// เลขลำดับตามตารางในฐานข้อมูล
	$running = "0";		// เลขลำดับตามตารางในฐานข้อมูล ถ้าไม่มี running ให้ = -1
	
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
	
//	$impfile_head_map = array("","","","","","","");	
	$head_map = array("SLIP_ID", "PER_ID", "ปีที่จ่ายเงินเดือน","เดือนที่จ่ายเงินเดือน","เลขประจำตัวประชาชน","คำนำหน้าชื่อ","ชื่อ","นามสกุล","ชื่อหน่วยงานระดับกรม","ชื่อหน่วยงานระดับสำนัก / กอง","รหัสธนาคาร","ชื่อธนาคาร","รหัสสาขาธนาคาร","ชื่อสาขาธนาคาร","เลขที่บัญชีธนาคาร(บัญชีเงินเดือน)","เงินเดือน / ค่าจ้างประจำ","เงินเดือนตกเบิก / ค่าจ้างประจำตกเบิก","เงิน ปตจ.","เงิน ปจต. ตกเบิก","พ.ข.อ./ตกเบิก","พ.ส.ร./ตกเบิก","พ.ค.ว./ตกเบิก","พ.ป.ผ./ตกเบิก","สปพ./ตกเบิก","ตปพ./ตกเบิก","ต.ข.ท.ปจต.","ต.ข.ท.ปจต. ตกเบิก","ต.ข.8-8ว.","ต.ข.8-8ว. ตกเบิก","ต.ด.ข.1-7/ตกเบิก","ง.ต.พ.ข./ตกเบิก","ค่าเช่าบ้าน/ตกเบิก","ช่วยเหลือบุตร/ตกเบิก","การศึกษาบุตร/ตกเบิก","เงินรางวัล/เงินท้าทาย","ชื่อเงินเพิ่มรายการที่ 1","จำนวนเงินเพิ่มรายการที่ 1","ชื่อเงินเพิ่มรายการที่ 2","จำนวนเงินเพิ่มรายการที่ 2","ชื่อเงินเพิ่มรายการที่ 3","จำนวนเงินเพิ่มรายการที่ 3","ชื่อเงินเพิ่มรายการที่ 4","จำนวนเงินเพิ่มรายการที่ 4","เงินเพิ่มอื่น ๆ","รวมรับทั้งเดือน","ภาษี/ตกเบิก","เงินกู้เพื่อที่อยู่อาศัย","ค่าหุ้น ? เงินกู้สหกรณ์","เงินกู้เพื่อการศึกษา","กบข./ตกเบิก","กบข.ส่วนเพิ่ม/ตกเบิก","ง.ก.บ.(ธอส.)","ง.ก.บ.(อส.)","ง.ก.ธ.","เงินกู้ ธพ.","ชดใช้ทางแพ่ง","เงินเรียกคืน","ค่าสาธารณูปโภค","เงินสวัสดิการสโมสร","ค่าฌาปนกิจ","งท. สงเคราะห์","ชื่อเงินหักรายการที่ 1","จำนวนเงินหักรายการที่ 1","ชื่อเงินหักรายการที่ 2","จำนวนเงินหักรายการที่ 2","ชื่อเงินหักรายการที่ 3","จำนวนเงินหักรายการที่ 3","ชื่อเงินหักรายการที่ 4","จำนวนเงินหักรายการที่ 4","ชื่อเงินหักรายการที่ 5","จำนวนเงินหักรายการที่ 5","ชื่อเงินหักรายการที่ 6","จำนวนเงินหักรายการที่ 6","ชื่อเงินหักรายการที่ 7","จำนวนเงินหักรายการที่ 7","ชื่อเงินหักรายการที่ 8","จำนวนเงินหักรายการที่ 8","เงินลด/หักอื่นๆ","รวมจ่ายทั้งเดือน","รับสุทธิ","วัน/เดือน/ปีพ.ศ. ที่ออกหนังสือรับรอง");
	$data_type = array("s","s","s","s","s","s","s","s","s","s","s","s","s","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","s","n.2","s","n.2","s","n.2","s","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","s","n.2","n.2","n.2","n.2","d");	// ใช้เฉพาะที่นำเข้าจาก text file

	$column_map[SLIP_ID] = "running";	// 0-running number
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where  PER_ID=@3";	// /*เดิม*/ "sql-s-d-select PER_ID from PER_PERSONAL where PER_STATUS=1 AND PER_CARDNO=@3";// 1- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)
																																										// 		field @3 ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled
																																										// 		มีค่า = PER_ID ที่มี PER_CARDNO = ค่าใน text file column ที่ @3
	$column_map[SLIP_YEAR] = "1";	// 2-map กับ column 1 ใน text หรือ excel
	$column_map[SLIP_MONTH] = "2";	// 3-map กับ column 2 ใน text หรือ excel
	$column_map[PER_CARDNO] = "sql-s-d-select PER_CARDNO from PER_PERSONAL where  PER_ID=@3";	///*เดิม*/ 3 // 4-map กับ column 3 ใน text หรือ excel
	$column_map[PN_NAME] = "sql-s-d-select PN_NAME from PER_PRENAME where PN_NAME=@4";	// 5-map กับ column 4 ตรวจเช็คด้วย sql ชุดนี้
	$column_map[PER_NAME] = "5";	// 6-map กับ column 5 ใน text หรือ excel
	$column_map[PER_SURNAME] = "6";	// 7-map กับ column 6 ใน text หรือ excel
	$column_map[DEPARTMENT_NAME] = "7";	// 8-map กับ column 7 ใน text หรือ excel
	$column_map[ORG_NAME] = "8";	// 9-map กับ column 8 ใน text หรือ excel
	$column_map[BANK_CODE] = "9";	// 10-map กับ column 9 ใน text หรือ excel
	$column_map[BANK_NAME] = "10";	// 11-map กับ column 10 ใน text หรือ excel
	$column_map[BRANCH_CODE] = "11";	// 12-map กับ column 11 ใน text หรือ excel
	$column_map[BRANCH_NAME] = "12";	// 13-map กับ column 12 ใน text หรือ excel
	$column_map[PER_BANK_ACCOUNT] = "13";	// 14-map กับ column 13 ใน text หรือ excel
	$column_map[INCOME_01] = "14";	// 15-map กับ column 14 ใน text หรือ excel
	$column_map[INCOME_02] = "15";	// 16-map กับ column 15 ใน text หรือ excel
	$column_map[INCOME_03] = "16";	// 17-map กับ column 16 ใน text หรือ excel
	$column_map[INCOME_04] = "17";	// 18-map กับ column 17 ใน text หรือ excel
	$column_map[INCOME_05] = "18";	// 19-map กับ column 18 ใน text หรือ excel
	$column_map[INCOME_06] = "19";	// 20-map กับ column 19 ใน text หรือ excel
	$column_map[INCOME_07] = "20";	// 21-map กับ column 20 ใน text หรือ excel
	$column_map[INCOME_08] = "21";	// 22-map กับ column 21 ใน text หรือ excel
	$column_map[INCOME_09] = "22";	// 23-map กับ column 22 ใน text หรือ excel
	$column_map[INCOME_10] = "23";	// 24-map กับ column 23 ใน text หรือ excel
	$column_map[INCOME_11] = "24";	// 25-map กับ column 24 ใน text หรือ excel
	$column_map[INCOME_12] = "25";	// 26-map กับ column 25 ใน text หรือ excel
	$column_map[INCOME_13] = "26";	// 27-map กับ column 26 ใน text หรือ excel
	$column_map[INCOME_14] = "27";	// 28-map กับ column 27 ใน text หรือ excel
	$column_map[INCOME_15] = "28";	// 29-map กับ column 28 ใน text หรือ excel
	$column_map[INCOME_16] = "29";	// 30-map กับ column 29 ใน text หรือ excel
	$column_map[INCOME_17] = "30";	// 31-map กับ column 30 ใน text หรือ excel
	$column_map[INCOME_18] = "31";	// 32-map กับ column 31 ใน text หรือ excel
	$column_map[INCOME_19] = "32";	// 33-map กับ column 32 ใน text หรือ excel
	$column_map[INCOME_20] = "33";	// 34-map กับ column 33 ใน text หรือ excel
	$column_map[INCOME_NAME_01] = "34";	// 35-map กับ column 34 ใน text หรือ excel
	$column_map[EXTRA_INCOME_01] = "35";	// 36-map กับ column 35 ใน text หรือ excel
	$column_map[INCOME_NAME_02] = "36";	// 37-map กับ column 36 ใน text หรือ excel
	$column_map[EXTRA_INCOME_02] = "37";	// 38-map กับ column 37 ใน text หรือ excel
	$column_map[INCOME_NAME_03] = "38";	// 39-map กับ column 38 ใน text หรือ excel
	$column_map[EXTRA_INCOME_03] = "39";	// 40-map กับ column 39 ใน text หรือ excel
	$column_map[INCOME_NAME_04] = "40";	// 41-map กับ column 40 ใน text หรือ excel
	$column_map[EXTRA_INCOME_04] = "41";	// 42-map กับ column 41 ใน text หรือ excel
	$column_map[OTHER_INCOME] = "42";	// 43-map กับ column 42 ใน text หรือ excel
	$column_map[TOTAL_INCOME] = "43";	// 44-map กับ column 43 ใน text หรือ excel
	$column_map[DEDUCT_01] = "44";	// 45-map กับ column 44 ใน text หรือ excel
	$column_map[DEDUCT_02] = "45";	// 46-map กับ column 45 ใน text หรือ excel
	$column_map[DEDUCT_03] = "46";	// 47-map กับ column 46 ใน text หรือ excel
	$column_map[DEDUCT_04] = "47";	// 48-map กับ column 47 ใน text หรือ excel
	$column_map[DEDUCT_05] = "48";	// 49-map กับ column 48 ใน text หรือ excel
	$column_map[DEDUCT_06] = "49";	// 50-map กับ column 49 ใน text หรือ excel
	$column_map[DEDUCT_07] = "50";	// 51-map กับ column 50 ใน text หรือ excel
	$column_map[DEDUCT_08] = "51";	// 52-map กับ column 51 ใน text หรือ excel
	$column_map[DEDUCT_09] = "52";	// 53-map กับ column 52 ใน text หรือ excel
	$column_map[DEDUCT_10] = "53";	// 54-map กับ column 53 ใน text หรือ excel
	$column_map[DEDUCT_11] = "54";	// 55-map กับ column 54 ใน text หรือ excel
	$column_map[DEDUCT_12] = "55";	// 56-map กับ column 55 ใน text หรือ excel
	$column_map[DEDUCT_13] = "56";	// 57-map กับ column 56 ใน text หรือ excel
	$column_map[DEDUCT_14] = "57";	// 58-map กับ column 57 ใน text หรือ excel
	$column_map[DEDUCT_15] = "58";	// 59-map กับ column 58 ใน text หรือ excel
	$column_map[DEDUCT_16] = "59";	// 60-map กับ column 59 ใน text หรือ excel
	$column_map[DEDUCT_NAME_01] = "60";	// 61-map กับ column 60 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_01] = "61";	// 62-map กับ column 61 ใน text หรือ excel
	$column_map[DEDUCT_NAME_02] = "62";	// 63-map กับ column 62 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_02] = "63";	// 64-map กับ column 63 ใน text หรือ excel
	$column_map[DEDUCT_NAME_03] = "64";	// 65-map กับ column 64 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_03] = "65";	// 66-map กับ column 65 ใน text หรือ excel
	$column_map[DEDUCT_NAME_04] = "66";	// 67-map กับ column 66 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_04] = "67";	// 68-map กับ column 67 ใน text หรือ excel
	$column_map[DEDUCT_NAME_05] = "68";	// 69-map กับ column 68 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_05] = "69";	// 70-map กับ column 69 ใน text หรือ excel
	$column_map[DEDUCT_NAME_06] = "70";	// 71-map กับ column 70 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_06] = "71";	// 72-map กับ column 71 ใน text หรือ excel
	$column_map[DEDUCT_NAME_07] = "72";	// 73-map กับ column 72 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_07] = "73";	// 74-map กับ column 73 ใน text หรือ excel
	$column_map[DEDUCT_NAME_08] = "74";	// 75-map กับ column 74 ใน text หรือ excel
	$column_map[EXTRA_DEDUCT_08] = "75";	// 76-map กับ column 75 ใน text หรือ excel
	$column_map[OTHER_DEDUCT] = "76";	// 77-map กับ column 76 ใน text หรือ excel
	$column_map[TOTAL_DEDUCT] = "77";	// 78-map กับ column 77 ใน text หรือ excel
	$column_map[NET_INCOME] = "78";	// 79-map กับ column 78 ใน text หรือ excel
	$column_map[APPROVE_DATE] = "79";	// 80-map กับ column 79 ใน text หรือ excel
	$column_map[UPDATE_USER] = "update_user";	// 82-map กับ UPDATE_USER จากระบบ
	$column_map[UPDATE_DATE] = "update_date";	// 83-map กับ UPDATE_DATE จากระบบ
	$column_map[AUDIT_FLAG] = "";	// 84-ไม่มีค่าอะไร เว้น ข้ามไปได้

?>