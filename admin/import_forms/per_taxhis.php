<?
//	map text data to table columns

	$table = "PER_TAXHIS";
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
	$head_map = array("SLIP_ID", "PER_ID", "ปีภาษีที่จ่าย","วัน เดือน ปีพ.ศ. ที่ออกหนังสือรับรองฯ","เลขที่","เลขประจำตัวผู้เสียภาษีอากร","ชื่อหน่วยงาน","ที่อยู่","เลขประจำตัวประชาชน","เลขประจำตัวผู้เสียภาษีอากร","ชื่อ-นามสกุล","ที่อยู่","ลำดับที่","แบบยื่นรายการภาษีหักจ่าย","จำนวนเงินที่จ่าย","ภาษีที่หักและนำส่งไว้","รวมเงินที่จ่ายภาษี","รวมภาษีที่หักและนำส่งไว้","รวมภาษีที่หักและนำส่งไว้ (ตัวอักษร)","เงินสะสมที่จ่ายเข้า","เงินสะสมที่จ่าย","ผู้จ่ายเงิน");
	$data_type = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "n.2", "n.2", "n.2", "n.2", "s", "s", "n.2", "s", "s", "s", "s");	// ใช้เฉพาะที่นำเข้าจาก text file

	$column_map[STAX_ID] = "running";	// 0-running number
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@5";	// 1- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)
      																																									// 		มีค่า = PER_ID ที่มี PER_CARDNO = ค่าใน text file column ที่ @3
	$column_map[TAX_YEAR]  = "11";	// 2-map กับ column 1 ใน text หรือ excel
	$column_map[TAX_DATE]  = "20";	// 3-map กับ column 2 ใน text หรือ excel
	$column_map[TAX_NUM]   = "1";	// 4-map กับ column 3 ใน text หรือ excel
	$column_map[ORGTAX_NO] = "3";	// 5-map กับ column 4 ตรวจเช็คด้วย sql ชุดนี้
	$column_map[ORG_NAME]  = "2";	// 6-map กับ column 5 ใน text หรือ excel
	$column_map[ORG_ADDR]  = "4";	// 7-map กับ column 6 ใน text หรือ excel
	$column_map[PER_CARDNO]= "5";	// 8-map กับ column 7 ใน text หรือ excel
	$column_map[PER_TAXNO] = "6";	// 9-map กับ column 8 ใน text หรือ excel
	$column_map[PER_NAME]  = "7";	// 10-map กับ column 9 ใน text หรือ excel
	$column_map[PER_ADDR]  = "8";	// 11-map กับ column 10 ใน text หรือ excel
	$column_map[SEQ_NO]    = "9";	// 12-map กับ column 11 ใน text หรือ excel
	$column_map[FORMTAX_TYPE] = "10";	// 13-map กับ column 12 ใน text หรือ excel
	$column_map[INCOME]     = "12";	// 14-map กับ column 13 ใน text หรือ excel
	$column_map[TAX_INCOME] = "13";	// 15-map กับ column 14 ใน text หรือ excel
	$column_map[NET_INCOME] = "14";	// 16-map กับ column 15 ใน text หรือ excel
	$column_map[NETTAX_INCOME] = "15";	// 17-map กับ column 16 ใน text หรือ excel
	$column_map[NETTAX_CHAR]   = "16";	// 18-map กับ column 17 ใน text หรือ excel
	$column_map[NETSAVING_TYPE]= "17";	// 19-map กับ column 18 ใน text หรือ excel
	$column_map[NET_SAVING]    = "18";	// 20-map กับ column 19 ใน text หรือ excel
	$column_map[TAX_TYPE]      = "19";	// 21-map กับ column 20 ใน text หรือ excel

?>