<?
//	map text data to table columns

	$table = "PER_DECORATEHIS";
	$dup_column = "1";	// เลขลำดับตามตารางในฐานข้อมูล
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
	
	$impfile_head_map = array("PER_CARDNO","FNAME","LNAME","INS_CODE","INS_DATE","POS_NAME","DEP_NAME");

	$head_map = array("DEH_ID","PER_ID","เครื่องราชฯ ที่ได้รับ","วันที่ได้รับเครื่องราชฯ","","","เลขประจำตัวประชาชน","ราชกิจจานุเบกษา","รับเครื่องราชฯ","คืนเครื่องราชฯ","วันที่คืนเครื่องราชฯ","คืนเป็นเครื่องราชฯ/เงินสด","วันที่ราชกิจจานุเบกษา","เลขที่หนังสือนำส่ง","วันที่หนังสือนำส่ง","หมายเหตุ","ตำแหน่ง","สังกัด","ฉบับทะเบียนฐานันดร/ฉบับพิเศษ","เล่ม","ตอนที่","หน้า","ลำดับ","ตรวจสอบแล้ว","เล่ม","ตอนที่","หน้า","ลำดับ","วันที่ราชกิจจานุเบกษา");
	$impfile_head_title = "";
	$impfile_head_thai = array( "เลขประจำตัวประชาชน","ชื่อ","นามสกุล", "เครื่องราชฯ ที่ได้รับ", "วันที่ได้รับเครื่องราชฯ", "ชื่อตำแหน่ง", "ชื่อหน่วยงาน","เล่ม","ตอนที่","หน้า","ลำดับ","วันที่ราชกิจจานุเบกษา");
	$impfile_exam_map = array( "3100425369745","รักชาติ","ยิ่งชีพ", "ท.ม.", "12/05/2546", "นักวิเคราะห์นโยบายและแผน", "สำนักเลขาธิการคณะรัฐมนตรี","1","2","3","12","12/06/2546");

	$column_map[DEH_ID] = "running";	// 0-running number DEH_ID
	$column_map[PER_ID] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@1";	// 1- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)
																																										// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled
																																										// 		มีค่า = PER_ID ที่มี PER_CARDNO = ค่าใน text file column ที่ {1}
	$column_map[DC_CODE] = "sql-s-e-select DC_CODE from PER_DECORATION where DC_SHORTNAME=@4";	// 2-จาก sql อ่านด้วย INS_CODE =>DC_CODE
	$column_map[DEH_DATE] = "func-s-e-save_date(@5)";	// 3-map กับ column 4 ใน text หรือ excel DEH_DATE = save_date($INS_DATE)
	$column_map[UPDATE_USER] = "update_user";	// 82-map กับ UPDATE_USER จากระบบ
	$column_map[UPDATE_DATE] = "update_date";	// 83-map กับ UPDATE_DATE จากระบบ
	$column_map[PER_CARDNO] = "1";	// 6-map กับ column 1 ใน text หรือ excel PER_CARDNO
	$column_map[DEH_GAZETTE] = "";		// 7-ไม่มีค่าอะไร DEH_GAZETTE
	$column_map[DEH_RECEIVE_FLAG] = "";		// 8-ไม่มีค่าอะไร DEH_RECEIVE_FLAG
	$column_map[DEH_RETURN_FLAG] = "";		// 9-ไม่มีค่าอะไร DEH_RETURN_FLAG
	$column_map[DEH_RETURN_DATE] = "";		// 10-ไม่มีค่าอะไร DEH_RETURN_DATE
	$column_map[DEH_RETURN_TYPE] = "";		// 11-ไม่มีค่าอะไร DEH_RETURN_TYPE
	$column_map[DEH_RECEIVE_DATE] = "func-s-e-save_date(@12)";		// 12-ไม่มีค่าอะไร DEH_RECEIVE_DATE
	$column_map[DEH_BOOK_NO] = "";		// 13-ไม่มีค่าอะไร DEH_BOOK_NO
	$column_map[DEH_BOOK_DATE] = "";		// 14-ไม่มีค่าอะไร DEH_BOOK_DATE
	$column_map[DEH_REMARK] = "";	// fmla-s-e-@6+' '+@7 15-formula ข้อมูลจาก text 6,7 DEH_REMARK = '$POS_NAME(6) $DEP_NAME(7)'
	$column_map[DEH_POSITION] = "6";		// 16-ไม่มีค่าอะไร DEH_POSITION
	$column_map[DEH_ORG] = "7";		// 17-ไม่มีค่าอะไร DEH_ORG
	$column_map[DEH_ISSUE] = "";		// 18-ไม่มีค่าอะไร DEH_ISSUE
	$column_map[DEH_BOOK] = "8";		// 19-ไม่มีค่าอะไร DEH_BOOK
	$column_map[DEH_PART] = "9";		// 20-ไม่มีค่าอะไร DEH_PART
	$column_map[DEH_PAGE] = "10";		// 21-ไม่มีค่าอะไร DEH_PAGE
	$column_map[DEH_ORDER_DECOR] = "11";		// 22-ไม่มีค่าอะไร DEH_ORDER_DECOR
	$column_map[AUDIT_FLAG] = "";		// 23-ไม่มีค่าอะไร เว้น ข้ามไปได้ AUDIT_FLAG
        
        
        
        
        
        /* เดิม
$column_map[] = "running";	// 0-running number DEH_ID
	$column_map[] = "sql-s-d-select PER_ID from PER_PERSONAL where PER_CARDNO=@1";	// 1- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)
																																										// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled
																																										// 		มีค่า = PER_ID ที่มี PER_CARDNO = ค่าใน text file column ที่ {1}
	$column_map[] = "sql-s-e-select DC_CODE from PER_DECORATION where DC_SHORTNAME=@4";	// 2-จาก sql อ่านด้วย INS_CODE =>DC_CODE
	$column_map[] = "func-s-e-save_date(@5)";	// 3-map กับ column 4 ใน text หรือ excel DEH_DATE = save_date($INS_DATE)
	$column_map[] = "update_user";	// 82-map กับ UPDATE_USER จากระบบ
	$column_map[] = "update_date";	// 83-map กับ UPDATE_DATE จากระบบ
	$column_map[] = "1";	// 6-map กับ column 1 ใน text หรือ excel PER_CARDNO
	$column_map[] = "";		// 7-ไม่มีค่าอะไร DEH_GAZETTE
	$column_map[] = "";		// 8-ไม่มีค่าอะไร DEH_RECEIVE_FLAG
	$column_map[] = "";		// 9-ไม่มีค่าอะไร DEH_RETURN_FLAG
	$column_map[] = "";		// 10-ไม่มีค่าอะไร DEH_RETURN_DATE
	$column_map[] = "";		// 11-ไม่มีค่าอะไร DEH_RETURN_TYPE
	$column_map[] = "";		// 12-ไม่มีค่าอะไร DEH_RECEIVE_DATE
	$column_map[] = "";		// 13-ไม่มีค่าอะไร DEH_BOOK_NO
	$column_map[] = "";		// 14-ไม่มีค่าอะไร DEH_BOOK_DATE
	$column_map[] = "";	// fmla-s-e-@6+' '+@7 15-formula ข้อมูลจาก text 6,7 DEH_REMARK = '$POS_NAME(6) $DEP_NAME(7)'
	$column_map[] = "6";		// 16-ไม่มีค่าอะไร DEH_POSITION
	$column_map[] = "7";		// 17-ไม่มีค่าอะไร DEH_ORG
	$column_map[] = "";		// 18-ไม่มีค่าอะไร DEH_ISSUE
	$column_map[] = "";		// 19-ไม่มีค่าอะไร DEH_BOOK
	$column_map[] = "";		// 20-ไม่มีค่าอะไร DEH_PART
	$column_map[] = "";		// 21-ไม่มีค่าอะไร DEH_PAGE
	$column_map[] = "";		// 22-ไม่มีค่าอะไร DEH_ORDER_DECOR
	$column_map[] = "";		// 23-ไม่มีค่าอะไร เว้น ข้ามไปได้ AUDIT_FLAG
         */
	
	// ข้อมูลจาก form
	$scrform[] = "";

?>