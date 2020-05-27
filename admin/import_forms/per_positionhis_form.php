<?
//	map text data to table columns

	$table = "PER_POSITIONHIS";
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
	
	$al_null = "<span class='label_alert'>*</span>"; //แปะค่าไม่ไห้บอกผู้ใช้เกิดค่าว่าง
	$impfile_head_map = array("POH_ID","PER_ID","POH_EFFECTIVEDATE","MOV_CODE","POH_ENDDATE","POH_DOCNO","POH_DOCDATE","POH_POS_NO","PM_CODE","LEVEL_NO","PL_CODE","PN_CODE","PT_CODE","CT_CODE","PV_CODE","AP_CODE","POH_ORGMGT","ORG_ID_1","ORG_ID_2","ORG_ID_3","POH_UNDER_ORG1","POH_UNDER_ORG2","POH_ASS_ORG","POH_ASS_ORG1","POH_ASS_ORG2","POH_SALARY","POH_SALARY_POS","POH_REMARK","UPDATE_USER","UPDATE_DATE","PER_CARDNO","EP_CODE","POH_ORG1","POH_ORG2","POH_ORG3","POH_ORG_TRANSFER","POH_ORG","POH_PM_NAME","POH_PL_NAME","POH_SEQ_NO","POH_LAST_POSITION","POH_CMD_SEQ","POH_ISREAL","POH_ORG_DOPA_CODE","ES_CODE","POH_LEVEL_NO","TP_CODE","POH_UNDER_ORG3","POH_UNDER_ORG4","POH_UNDER_ORG5","POH_ASS_ORG3","POH_ASS_ORG4","POH_ASS_ORG5","POH_REMARK1","POH_REMARK2","POH_POS_NO_NAME","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","AUDIT_FLAG","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$head_map = array("รหัสประวัติดำรงตำแหน่ง","รหัสบุคลากร","วันที่เข้าดำรงตำแหน่ง","ประเภทการเคลื่อนไหว","วันที่สิ้นสุดการดำรงตำแหน่ง","เลขที่คำสั่ง","วันที่ออกคำสั่ง","เลขที่ตำแหน่ง","ตำแหน่งในการบริหารงาน","ระดับตำแหน่ง","ตำแหน่งในสายงาน","ชื่อตำแหน่งลูกจ้างประจำ","ตำแหน่งประเภท","ประเทศ","จังหวัด","อำเภอ","ตำแหน่งทางบริหารที่กำหนดเป็นการภายใน","กระทรวง","กรม","สำนัก/กอง","ต่ำกว่าสำนัก/กอง 1 ระดับ","ต่ำกว่าสำนัก/กอง 2 ระดับ","สำนัก/กอง ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 1 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 2 ระดับ ตามมอบหมายงาน","อัตราเงินเดือน","เงินประจำตำแหน่ง","หมายเหตุ","รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล","วันเวลา เปลี่ยนแปลงข้อมูล","เลขประจำตัวประชาชน","ชื่อตำแหน่งพนักงานราชการ","ชื่อกระทรวง","ชื่อกรม","ชื่อสำนัก/กอง","ส่วนราชการที่รับโอน/ให้โอน","ข้อมูลเดิม (ก่อนถ่ายโอน)","ชื่อตำแหน่งในการบริหารงาน","ชื่อตำแหน่งในสายงาน","ลำดับที่กรณีวันที่เดียวกัน","ดำรงตำแหน่งล่าสุด","ลำดับที่ในบัญชีแนบท้ายคำสั่ง","ดำรงตำแหน่ง","รหัสอื่น","สถานะการดำรงตำแหน่ง","ระดับตำแหน่ง","ชื่อตำแหน่งลูกจ้างชั่วคราว","ต่ำกว่าสำนัก/กอง 3 ระดับ","ต่ำกว่าสำนัก/กอง 4 ระดับ","ต่ำกว่าสำนัก/กอง 5 ระดับ","ต่ำกว่าสำนัก/กอง 3 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 4 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 5 ระดับ ตามมอบหมายงาน","หมายเหตุ 1","หมายเหตุ 2","ชื่อเลขที่ตำแหน่ง","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","ตรวจสอบ","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$impfile_head_title = "";
	$impfile_head_thai = array( "$al_null เลขประจำตัวประชาชน","$al_null ชื่อ-นามสกุล","$al_null วันที่เข้าดำรงตำแหน่ง","$al_null ประเภทการเคลื่อนไหว","วันที่สิ้นสุดการดำรงตำแหน่ง","เลขที่คำสั่ง","วันที่ออกคำสั่ง","เลขที่ตำแหน่ง","ตำแหน่งในการบริหารงาน","ระดับตำแหน่ง","ตำแหน่งในสายงาน","ชื่อตำแหน่งลูกจ้างประจำ","ตำแหน่งประเภท","$al_null ประเทศ","จังหวัด","อำเภอ","$al_null ตำแหน่งทางบริหารที่กำหนดเป็นการภายใน","กระทรวง","กรม","สำนัก/กอง","ต่ำกว่าสำนัก/กอง 1 ระดับ","ต่ำกว่าสำนัก/กอง 2 ระดับ","สำนัก/กอง ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 1 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 2 ระดับ ตามมอบหมายงาน","$al_null อัตราเงินเดือน","$al_null เงินประจำตำแหน่ง","หมายเหตุ","ชื่อตำแหน่งพนักงานราชการ","ชื่อกระทรวง","ชื่อกรม","ชื่อสำนัก/กอง","ส่วนราชการที่รับโอน/ให้โอน","ข้อมูลเดิม (ก่อนถ่ายโอน)","ชื่อตำแหน่งในการบริหารงาน","ชื่อตำแหน่งในสายงาน","ลำดับที่กรณีวันที่เดียวกัน","ดำรงตำแหน่งล่าสุด","ลำดับที่ในบัญชีแนบท้ายคำสั่ง","ดำรงตำแหน่ง","รหัสอื่น","สถานะการดำรงตำแหน่ง","ระดับตำแหน่ง","ชื่อตำแหน่งลูกจ้างชั่วคราว","ต่ำกว่าสำนัก/กอง 3 ระดับ","ต่ำกว่าสำนัก/กอง 4 ระดับ","ต่ำกว่าสำนัก/กอง 5 ระดับ","ต่ำกว่าสำนัก/กอง 3 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 4 ระดับ ตามมอบหมายงาน","ต่ำกว่าสำนัก/กอง 5 ระดับ ตามมอบหมายงาน","หมายเหตุ 1","หมายเหตุ 2","ชื่อเลขที่ตำแหน่ง","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","ตรวจสอบ","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");
	$impfile_exam_map = array( "3100425369745","นายสมชาย ใจดี","31/12/2555","ย้าย","31/12/2555","12/2555", "31/12/2555", "1234","รองเลขาธิการ","ระดับ 4","นักปกครอง","พนักงานเข้าเล่มและเย็บ","ทั่วไป","ไทย","กรุงเทพมหานคร","เมือง","ใช่","สำนักนายกรัฐมนตรี","สำนักงาน ก.พ.","ส่วนกลาง","ฝ่ายบริหารทั่วไป","กลุ่มกฎหมาย","กลุ่ม กพร.","กลุ่มการศึกษาและฝึกอบรม","ฝ่ายวางแผนและบริหาร","29800","21000","ได้รับเงินเพิ่มเดือนละ1000บาท","นักจัดการงานทั่วไป","สำนักนายกรัฐมนตรี","สำนักงาน ก.พ.","กองกลาง","กรมราชทัณฑ์","ส่วนกลาง สำนักงานกพ.","ที่ปรึกษากฎหมาย","นิติกร","1","ใช่","1","จริง","4567","ตรงตามตำแหน่ง","ระดับ 5","นักสังคมสงเคราะห์","กลุ่มช่วยอำนวยการ","กลุ่มวิเทศสัมพันธ์","นักวิเคราะห์นโยบายและแผน","กลุ่มงานอำนวยการ","กลุ่มงานวิเทศสัมพันธ์","นักวิเคราะห์แผนงาน","ตำแหน่งยังไม่เป็นปัจจุบัน","ตำแหน่งรอการสับเปลี่ยน","กก/2555","POH_DOCNO_EDIT","POH_DOCDATE_EDIT","AUDIT_FLAG","POH_SPECIALIST","POH_REF_DOC","POH_ACTH_SEQ","POH_ASS_DEPARTMENT");

	$column_map = (array) null;
	$column_map[] = "running";
	$column_map[] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@1' or '@2' like '%'||PER_NAME||' '||PER_SURNAME ^NOTNULL";		// 2- ดึงข้อมูลจาก sql เป็นข้อมูลประเภท n = number (หรือ s = string)																																					// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled																															// 		มีค่า = ORG_ID ที่มี ORG_NAME = ค่าใน text file column ที่ {2};
	$column_map[] = "func-s-e-save_date(@3)^NOTNULL";
	$column_map[] = "sql-s-d-select MOV_CODE from PER_MOVMENT where MOV_NAME=@4^NOTNULL";
	$column_map[] = "func-s-e-save_date(@5)";
	$column_map[] = "6";
	$column_map[] = "func-s-e-save_date(@7)";
	$column_map[] = "8";
	$column_map[] = "sql-s-d-select PM_CODE from PER_MGT where PM_NAME=@9";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME=@10";
	$column_map[] = "sql-s-d-select PL_CODE from PER_LINE where PL_NAME=@11";
	$column_map[] = "sql-s-d-select PN_CODE from PER_POS_NAME where PN_NAME=@12";
	$column_map[] = "sql-s-d-select PT_CODE from PER_TYPE where PT_NAME=@13";
	$column_map[] = "sql-s-d-select CT_CODE from PER_COUNTRY where CT_NAME=@14^NOTNULL";
	$column_map[] = "sql-s-d-select PV_CODE from PER_PROVINCE where PV_NAME=@15";
	$column_map[] = "sql-s-d-select AP_CODE from PER_AMPHUR where AP_NAME=@16";
	$column_map[] = "func-n-e-check_1_2(@17)";		// 30-map ตัวแปร ในส่วน php SAH_CMD_SEQ
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@18";
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@19";
	$column_map[] = "sql-s-d-select ORG_ID from PER_ORG where ORG_NAME=@20";
	$column_map[] = "21";	
	$column_map[] = "22";	
	$column_map[] = "23";	
	$column_map[] = "24";
	$column_map[] = "25";
	$column_map[] = "26"; 
	$column_map[] = "27";
	$column_map[] = "28";
	$column_map[] = "update_user";
	$column_map[] = "update_date";
	$column_map[] = "1";	
	$column_map[] = "sql-s-d-select EP_CODE from PER_EMPSER_POS_NAME where EP_NAME=@29";	
	$column_map[] = "30";	
	$column_map[] = "31";
	$column_map[] = "32";
	$column_map[] = "33";
	$column_map[] = "34";
	$column_map[] = "35";
	$column_map[] = "36";
	$column_map[] = "37";
	$column_map[] = "func-s-e-check_Y_N(@38)";
	$column_map[] = "39";
	$column_map[] = "func-s-e-check_Y_N(@40)";
	$column_map[] = "41";
	$column_map[] = "sql-s-d-select ES_CODE from PER_EMP_STATUS where ES_NAME=@42";
	$column_map[] = "sql-s-d-select LEVEL_NO from PER_LEVEL where LEVEL_NAME=@43";
	$column_map[] = "sql-s-d-select TP_CODE from PER_TEMP_POS_NAME where TP_NAME=@44"; 
	$column_map[] = "45";
	$column_map[] = "46";
	$column_map[] = "47";
	$column_map[] = "48";
	$column_map[] = "49";
	$column_map[] = "50";
	$column_map[] = "51";
	$column_map[] = "52";
	$column_map[] = "53";
	$column_map[] = "54";
	$column_map[] = "func-s-e-save_date(@55)";
	$column_map[] = "func-s-e-check_Y_N(@56)";
	$column_map[] = "57";
	$column_map[] = "58";
	$column_map[] = "59";
	$column_map[] = "60";
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
