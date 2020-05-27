<?	
//	map text data to table columns
//ini_set('error_reporting', 30719); //ปิด warning
 //ini_set('error_reporting', 30711); // บังคับให้แสดง warning
	$table = "PER_SPECIAL_SKILL";
	// ค่าข้างล่าง ถ้ามีมากกว่า 1 ตัวจะขั้นด้วย |
	$dup_column = "0";	// เลขลำดับตามตารางในฐานข้อมูล
	$prime = "0";		// เลขลำดับตามตารางในฐานข้อมูล
	$running = "0";		// เลขลำดับตามตารางในฐานข้อมูล ถ้าไม่มี running ให้ = -1
//echo "โปรแกรมฉบับชั่วคราว (beta 5.1.0.0 : 15 ต.ค. 2558)<br>";
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
	
	//$impfile_head_map = array("PER_CARDNO", "NAME", "SAH_POS_NO", "SAH_POSITION", "POSITION_LEVEL", "SAH_OLD_SALARY", "LAYER_SALARY_MAX", "SAH_SALARY_MIDPOINT",	"SAH_TOTAL_SCORE", "SAH_PERCENT_UP", "", "SAH_SALARY_UP",	"SAH_SALARY_EXTRA", "SAH_SALARY_TOTAL", "SAH_SALARY", "AM_NAME", "SAH_REMARK");
	$impfile_head_title = "(สำหรับลูกจ้างประจำ ในช่องผลการประเมิน ให้ป้อนข้อมูล 0.5 ขั้น, 1 ขั้น, 1.5 ขั้น, 2 ขั้น, เงินตอบแทนพิเศษ 2% ,เงินตอบแทนพิเศษ 4% หรือข้อมูลหลักประเภทการเคลื่อนไหว)";
	$impfile_head_thai = array( "เลขประจำตัวประชาชน","คำนำหน้าชื่อ","ชื่อ","นามสกุล","กระทรวง", "กรม", "จังหวัด", "ชื่อตำแหน่งในสายงาน", "ระดับตำแหน่ง", "ประเภทความเชี่ยวชาญ", "ด้านความเชี่ยวชาญ(ด้านหลัก)",	"ด้านความเชี่ยวชาญพิเศษ(ด้านรอง)", "ระดับความสามารถ", "รายละเอียด/คำอธิบาย", "สถานะ");
	$impfile_exam_map = array( "1234567890123","นาย","สมชาย","ใจดี","สำนักนายกรัฐมนตรี", "สำนักงาน ก.พ.", "นนทบุรี", "นักวิชาการคอมพิวเตอร์", "ชำนาญการ", "ความเชี่ยวชาญในราชการ", "ด้านวิทยาศาสตร์และเทคโนโลยี", "ด้านวิทยาศาสตร์และเทคโนโลยี/การสื่อสาร", "05-สามารถสอนผู้อื่น", "เป็นผู้ดูแลระบบเครื่อข่ายของหน่วยงาน....", "N");

//	$head_map = array("เลขที่","ชื่อ","นามสกุล","Email","สถานะ","เพศ","วันเกิด","","");
        $head_map = array("0",
            "PER_ID",
            "เลขประจำตัวประชาชน", 
            "ด้านความเชี่ยวชาญพิเศษ",
            "รายละเอียด/คำอธิบาย", 
            "รหัสผู้ใช้ที่เปลี่ยนแปลงข้อมูล", 
            "วันเวลาเปลี่ยนแปลงข้อมูล", 
            "หมายเหตุ", 
            "สถานะ",
            "ลำดับที่",
            "ระดับความสามารถ",
            "ประเภทความเชี่ยวชาญ"); 

	$column_map[SPS_ID] = "running";	// 0-running number SAH_ID
	$column_map[PER_ID] = "sql-n-d-select PER_ID from PER_PERSONAL where PER_CARDNO='@3'";
																																											// 		field ที่แสดง ให้เป็นแบบ d = disabled หรือ e = enabled
																																											// 		มีค่า = PER_ID ที่มี PER_CARDNO = ค่าใน text file column ที่ {1}
	$column_map[PER_CARDNO] = "3";		
	$column_map[SS_CODE] = "13";		
	$column_map[SPS_EMPHASIZE] = "16";		
	$column_map[UPDATE_USER] = "update_user";		
	$column_map[UPDATE_DATE] = "update_date";	
	$column_map[SPS_REMARK] = "";	
	$column_map[AUDIT_FLAG] = "17";	
	$column_map[SPS_SEQ_NO] = "24";	
        $column_map[LEVELSKILL_CODE] = "15";
        $column_map[SPS_FLAG] = "12";
	
	// เป็น function สำหรับ กำหนดค่าพิเศษ
?>