<?
//	map text data to table columns

	$table = "PER_TRAINING";
	// ค่าข้างล่าง ถ้ามีมากกว่า 1 ตัวจะขั้นด้วย |
	$dup_column = "0";	// เลขลำดับตามตารางในฐานข้อมูล 
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
	
//	$impfile_head_map = array("PER_CARDNO","FNAME","LNAME","INS_CODE","INS_DATE","POS_NAME","DEP_NAME");

//	$head_map = array("ฟิลด์ 1","ฟิลด์ 2","ฟิลด์ 3","ฟิลด์ 4","ฟิลด์ 5","ฟิลด์ 6","ฟิลด์ 7","","ฟิลด์ 9","");

	$column_map = (array) null;
	$column_map[] = "running";
	$column_map[] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_ID=@2";		// 2- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)																																					// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled																															// 		มีค่า = ORG_ID ที่มี ORG_NAME = ค่าใน text file column ที่ {2};
	$column_map[] = "func-n-e-check_TRN_TYPE(@3)";
	$column_map[] = "sql-s-d-select TR_CODE from PER_TRAIN where TR_NAME=@4";
	$column_map[] = "5";
	$column_map[] = "func-s-e-save_date(@6)";
	$column_map[] = "func-s-e-save_date(@7)";
	$column_map[] = "8";
	$column_map[] = "9";
	$column_map[] = "sql-s-d-select CT_CODE from PER_COUNTRY where CT_NAME=@10";
	$column_map[] = "11";
	$column_map[] = "sql-s-d-select CT_CODE from PER_COUNTRY where CT_NAME=@12";
	$column_map[] = "update_user";
	$column_map[] = "update_date";
	$column_map[] = "sql-n-d-select PER_CARDNO from PER_PERSONAL where PER_ID=@2";
	$column_map[] = ""; // คำนวณวัน
	$column_map[] = "17";		
	$column_map[] = "func-n-e-check_0_1(@18)";
	$column_map[] = "19";
	$column_map[] = "func-s-e-save_date(@20)";
	$column_map[] = "21";	
	$column_map[] = "22";	
	$column_map[] = "23";	
	$column_map[] = "24";
	$column_map[] = "25";
	$column_map[] = "26";
	$column_map[] = "func-s-e-save_date(@27)";
	$column_map[] = "func-s-e-check_Y_N(@28)";
	$column_map[] = "func-n-e-check_0_1(@29)";
	$column_map[] = "30";
	$column_map[] = "func-s-e-save_date(@31)";
	$column_map[] = "func-n-e-check_TRN_FLAG(@32)";
	$column_map[] = "33";	
	// ข้อมูลจาก form
/*	$screenform[] = "selectlistfix^value=1|ข้าราชการ|selected^name=sel_pertype^label=ประเภทบุคคลากร : ";	// text  radio  checkbox  selectlist  datecalendar
	$screenform[] = "text^value=func|thisyear^size=10^name=txt_year^label=ปีงบประมาณ : ";
	$screenform[] = "radio^value=1|ครั้งที่ 1|checked,2|ครั้งที่ 2^name=radio_part";
	$screenform[] = "checkbox^value=1|เป็นประวัติการรับเงินเดือนล่าสุด|checked^name=chk_lastdate^onclick=check_adate^+";	// + ต่อกันกับรายการถัดไป
	$screenform[] = "textcalandar^size=10^name=txtc_lastdate^label=[ วันที่สิ้นสุด ] :นี้";
	$screenform[] = "text^value=^size=20^name=txt_docu^label=คำสั่งเลขที่ : ";
	$screenform[] = "textcalandar^size=10^name=txtc_docudate^label=วันที่ผู้บริหารลงนามในคำสั่ง : ";
*/
	include("function_share_form.php");
?>
