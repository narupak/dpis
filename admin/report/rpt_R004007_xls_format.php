<?
//	fix head for rpt_R004007

	$heading_width[0] = 6;
	$heading_width[1] = 25;
	if ($ITA_FLAG==1) {
		$heading_width[2] = 30;		
		$heading_width[3] = 40;
		$heading_width[4] = 30;
		$heading_width[5] = 25;
	} else {
		$heading_width[2] = 20;		
		$heading_width[3] = 12;
		$heading_width[4] = 30;
                if($search_per_type==3){
                    $heading_width[5] = 20;
                    $minus = 2;
                }else{
                    $heading_width[5] = 20;
                    $heading_width[6] = 20;
                    $heading_width[7] = 20;
                }
		$heading_width[8-$minus] =  8;
		$heading_width[9-$minus] = 25;
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
			$heading_width[10-$minus] = 8;
			$heading_width[11-$minus] = 20;
			$heading_width[12-$minus] = 20;
			$heading_width[13-$minus] = 10;
			$heading_width[14-$minus] = 12;
			$heading_width[15-$minus] = 12;
                        $heading_width[16-$minus] = 12;
			$heading_width[17-$minus] = 10;
			$heading_width[18-$minus] = 18;
			$heading_width[19-$minus] = 18;
			$heading_width[20-$minus] = 8;
			$heading_width[21-$minus] = 20;
                        $heading_width[22-$minus] = 20;
			$heading_width[23-$minus] = 8;
			$heading_width[24-$minus] = 25;
			$heading_width[25-$minus] = 25;
			$heading_width[26-$minus] = 30;
			$heading_width[27-$minus] = 20;
			$heading_width[28-$minus] = 25;
			$heading_width[29-$minus] = 25;
			$heading_width[30-$minus] = 30;
			$heading_width[31-$minus] = 20;
			$heading_width[32-$minus] = 25;
			$heading_width[33-$minus] = 25;
			$heading_width[34-$minus] = 30;
			$heading_width[35-$minus] = 20;
			$heading_width[36-$minus] = 15;
			$heading_width[37-$minus] = 20;
		} else {
			$heading_width[10-$minus] = 20;
			$heading_width[11-$minus] = 20;
			$heading_width[12-$minus] = 10;
			$heading_width[13-$minus] = 12;
			$heading_width[14-$minus] = 12;
			$heading_width[15-$minus] = 12;
			$heading_width[16-$minus] = 18;
			$heading_width[17-$minus] = 18;
			$heading_width[18-$minus] = 16;
			$heading_width[19-$minus] = 16;
			$heading_width[20-$minus] = 25;
			$heading_width[21-$minus] = 25;
			$heading_width[22-$minus] = 30;
			$heading_width[23-$minus] = 20;
			$heading_width[24-$minus] = 25;
			$heading_width[25-$minus] = 25;
			$heading_width[26-$minus] = 30;
			$heading_width[27-$minus] = 20;
			$heading_width[28-$minus] = 25;
			$heading_width[29-$minus] = 25;
			$heading_width[30-$minus] = 30;
			$heading_width[31-$minus] = 20;
			$heading_width[32-$minus] = 15;
			$heading_width[33-$minus] = 20;
			$heading_width[34-$minus] = 15;
			$heading_width[35-$minus] = 20;
		}
	}

	$heading_text[0] = "ลำดับที่|";
	$heading_text[1] = "ชื่อ - สกุล|"; 
	if ($ITA_FLAG==1) {
		$heading_text[2] = "ตำแหน่ง|";
		$heading_text[3] = "ที่อยู่ตามทะเบียนบ้าน|หรือ ที่อยู่ปัจจุบัน";
		$heading_text[4] = "โทรศัพท์|";
		$heading_text[5] = "e-mail|";
	} else {
		$heading_text[2] = "เลขประจำตัว|ประชาชน";		
		$heading_text[3] = "วัน เดือน ปี|เกิด";
		$heading_text[4] = "ตำแหน่ง|";
                if($search_per_type==3){
                    $heading_text[5] = "กลุ่มงาน|";
                    $minus = 2;
                }else{
                    $heading_text[5] = "ตำแหน่งประเภท|";
                    $heading_text[6] = "ระดับตำแหน่ง|";
                    $heading_text[7] = "ช่วงระดับตำแหน่ง|";
                }
		$heading_text[8-$minus] = "เลขที่|ตำแหน่ง";
		$heading_text[9-$minus] = "$ORG_TITLE|";
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
			$heading_text[10-$minus] = "รหัสจังหวัด|";
			$heading_text[11-$minus] = "$ORG_TITLE1|";
			$heading_text[12-$minus] = "$ORG_TITLE2|";
			$heading_text[13-$minus] = "เงินเดือน|";
			$heading_text[14-$minus] = "วันบรรจุ|";
			$heading_text[15-$minus] = "วันเข้าสู่ระดับ|";
			$heading_text[16-$minus] =  "วันที่|ดำรงตำแหน่ง";
			$heading_text[17-$minus] = "เครื่องราชฯ|";
			$heading_text[18-$minus] = "อายุ|";
			$heading_text[19-$minus] = "อายุราชการ|";
			$heading_text[20-$minus] = "ระยะเวลาที่|ดำรงตำแหน่ง|";
			$heading_text[21-$minus] = "เลขถือจ่าย|";
			$heading_text[22-$minus] = "$ORG_TITLE|";
			$heading_text[23-$minus] = "รหัสจังหวัด|";
			$heading_text[24-$minus] = "|ระดับการศึกษา";
			$heading_text[25-$minus] = "|วุฒิการศึกษา";
			$heading_text[26-$minus] = "วุฒิที่ใช้บรรจุ|สาขาวิชาเอก";
			$heading_text[27-$minus] = "|สถาบันการศึกษา";
			$heading_text[28-$minus] = "|ระดับการศึกษา";
			$heading_text[29-$minus] = "|วุฒิการศึกษา";
			$heading_text[30-$minus] = "วุฒิในตำแหน่งปัจจุบัน|สาขาวิชาเอก";
			$heading_text[31-$minus] = "|สถาบันการศึกษา";
			$heading_text[32-$minus] = "|ระดับการศึกษา";
			$heading_text[33-$minus] = "|วุฒิการศึกษา";
			$heading_text[34-$minus] = "วุฒิสูงสุด|สาขาวิชาเอก";
			$heading_text[35-$minus] = "|สถาบันการศึกษา";
			$heading_text[36-$minus] = "วันเกษียณอายุ|";
			$heading_text[37-$minus] = "ใบอนุญาตประกอบวิชาชีพ|";
		} else {
			$heading_text[10-$minus] = "$ORG_TITLE1|";
			$heading_text[11-$minus] = "$ORG_TITLE2|";
			$heading_text[12-$minus] = "เงินเดือน|";
			$heading_text[13-$minus] = "วันบรรจุ|";
			$heading_text[14-$minus] = "วันเข้าสู่ระดับ|";
			$heading_text[15-$minus] =  "วันที่|ดำรงตำแหน่ง";
			$heading_text[16-$minus] = "เครื่องราชฯ|";
			$heading_text[17-$minus] = "อายุ|";
			$heading_text[18-$minus] = "อายุราชการ|";
			$heading_text[19-$minus] = "ระยะเวลาที่|ดำรงตำแหน่ง|";
			$heading_text[20-$minus] = "ฐานในการ|คำนวณ";
			$heading_text[21-$minus] = "$ORG_TITLE|มอบหมายงาน";
			$heading_text[22-$minus] = "|ระดับการศึกษา";
			$heading_text[23-$minus] = "|วุฒิการศึกษา";
			$heading_text[24-$minus] = "วุฒิที่ใช้บรรจุ|สาขาวิชาเอก";
			$heading_text[25-$minus] = "|สถาบันการศึกษา";
			$heading_text[26-$minus] = "|ระดับการศึกษา";
			$heading_text[27-$minus] = "|วุฒิการศึกษา";
			$heading_text[28-$minus] = "วุฒิในตำแหน่งปัจจุบัน|สาขาวิชาเอก";
			$heading_text[29-$minus] = "|สถาบันการศึกษา";
			$heading_text[30-$minus] = "|ระดับการศึกษา";
			$heading_text[31-$minus] = "|วุฒิการศึกษา";
			$heading_text[32-$minus] = "วุฒิสูงสุด|สาขาวิชาเอก";
			$heading_text[33-$minus] = "|สถาบันการศึกษา";
			$heading_text[34-$minus] = "วันเกษียณอายุ|";
			$heading_text[35-$minus] = "ใบอนุญาตประกอบวิชาชีพ|";
		}
	}

	// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
	//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
	//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
	//									TNUM-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									TNUM0-n คือ เปลี่ยนเป็นตัวเลขของไทย n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//									ENUM-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									ENUM0-n คือ แสดงเป็นเลขอาราบิค n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); 
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	
	if ($ITA_FLAG!=1) {
                if($search_per_type==3){
                    $column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
                    $minus = 2;
                }else{
                    $column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
                    $column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
                    $column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
                }   
		$column_function[8-$minus] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[9-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
			$column_function[10-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[11-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[12-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[13-$minus] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
			$column_function[14-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[15-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[16-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[17-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[18-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[19-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[20-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[21-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[22-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[23-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[24-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[25-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[26-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[27-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[28-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[29-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[30-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[31-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[32-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[33-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[34-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[35-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[36-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[37-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			
			$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			
			$data_align = array("R", "L", "C", "L", "L", "L", "L", "C", "C", "L", "L", "L", "L", "R", "C", "C", "L", "L", "L", "C", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "C", "L");
		} else {
			$column_function[10-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[11-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[12-$minus] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
			$column_function[13-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[14-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[15-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[16-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[17-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[18-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[19-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[20-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[21-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[22-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[23-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[24-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[25-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[26-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[27-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[28-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[29-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[30-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[31-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[32-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[33-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[34-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[35-$minus] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			
			$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");

			$data_align = array("R", "L", "C", "L", "L", "L", "L", "C", "C", "L", "L", "L", "R", "C", "C", "L", "L", "L", "R", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "C", "L");
		}
	}

	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
	if (!$COLUMN_FORMAT) {	// ต้องกำหนดเป็น element ให้อยู่ใน form1 ด้วย  <input type="hidden" name="COLUMN_FORMAT" value="<?=$COLUMN_FORMAT
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
	} else {
		$arrbuff = explode("|",$COLUMN_FORMAT);
		$arr_column_map = explode(",",$arrbuff[0]);		// index ของ head เริ่มต้น
		$arr_column_sel = explode(",",$arrbuff[1]);	// 1=แสดง	0=ไม่แสดง
		$arr_column_width = explode(",",$arrbuff[2]);	// ความกว้าง
		$heading_width = $arr_column_width;	// ความกว้าง
		$arr_column_align = explode(",",$arrbuff[3]);		// align
	}
	
	$total_show_width = 0;
	for($iii=0; $iii < count($arr_column_width); $iii++) 	if ($arr_column_sel[$iii]==1) $total_show_width += (int)$arr_column_width[$iii];

?>