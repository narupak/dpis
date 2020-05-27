<?
//	map text data to table columns

	$table = "PER_POS_MGTSALARYHIS";
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
	$al_null = "<span class='label_alert'>*</span>"; //ห้ามว่าง
	$impfile_head_map = array("PMH_ID","PER_ID","PER_CARDNO","PMH_EFFECTIVEDATE","EX_CODE","PMH_AMT","PMH_ENDDATE","PMH_ACTIVE","PMH_REMARK","PMH_SEQ_NO","UPDATE_USER","UPDATE_DATE","AUDIT_FLAG");
	$head_map = array("รหัสเงินตามตำแหน่ง","รหัสบุคลากร","เลขประจำตัวประชาชน","วันที่มีผลบังคับใช้"," ประเภทเงินประจำตำแหน่ง","จำนวนเงิน","ถึงวันที่","ใช้งาน/ยกเลิก","หมายเหตุ","ลำดับที่","รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล","วันเวลา เปลี่ยนแปลงข้อมูล","ตรวจสอบ");
	$impfile_head_title = ""; //อธิบายเพิ่มเติม
	$impfile_head_thai = array( "$al_null เลขประจำตัวประชาชน","$al_null ชื่อ-นามสกุล","$al_null วันที่มีผลบังคับใช้","$al_null  ประเภทเงินประจำตำแหน่ง","$al_null จำนวนเงิน","ถึงวันที่","ใช้งาน/ยกเลิก","หมายเหตุ");
	$impfile_exam_map = array( "3100425369745","นายสมชาย ใจดี","31/12/2555","เงินเบี้ยกันดาร", "99999", "31/12/2557", "ใช้งาน", "รอการตรวจสอบ");

	$column_map = (array) null;
	$column_map[] = "running";
	$column_map[] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@1' or '@2' like '%'||PER_NAME||' '||PER_SURNAME ^NOTNULL";																																					// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled																															// 		มีค่า = ORG_ID ที่มี ORG_NAME = ค่าใน text file column ที่ {2};
	$column_map[] = "func-s-e-save_date(@3)";
	$column_map[] = "sql-s-d-select EX_CODE from PER_EXTRATYPE where EX_NAME=@4^NOTNULL";
	$column_map[] = "5";  
	$column_map[] = "func-s-e-save_date(@6)";
	$column_map[] = "update_user";
	$column_map[] = "update_date";
	$column_map[] = "1";
	$column_map[] = "7";
	$column_map[] = "8";
	$column_map[] = "9";
	$column_map[] = "10";
	$column_map[] = "func-s-e-save_date(@11)";
	$column_map[] = "func-n-e-check_0_1(@12)";
	$column_map[] = "func-s-e-check_Y_N(@13)";
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
