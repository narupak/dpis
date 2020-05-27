<?
//	fix head for rpt_R006015
	if ($have_pic) {
		$heading_width[] = "20";
		$heading_width[] = "20";
		$heading_width[] = "38";
	} else {
		$heading_width[] = "33";
		$heading_width[] = "45";
	}
	$heading_width[] = "40";
	$heading_width[] = "15";
	$heading_width[] = "20";
	$heading_width[] = "15";
	$heading_width[] = "20";
	$heading_width[] = "10";
	$heading_width[] = "15";
	$heading_width[] = "10";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";
	$heading_width[] = "15";

	if ($have_pic) {
		$heading_text[] = "รูป|";
		$heading_text[] = "เลขประจำตัว|ข้าราชการ";
	} else {
		$heading_text[] = "เลขประจำตัว|ข้าราชการ";
	}
	$heading_text[] = "สังกัด/ชื่อ|";
	$heading_text[] = "ตำแหน่ง|";
	$heading_text[] = "เลขที่|ตำแหน่ง";
	$heading_text[] = "<**1**>ถือจ่ายปีที่แล้ว|ระดับ";
	$heading_text[] = "<**1**>ถือจ่ายปีที่แล้ว|อัตรา";
    $heading_text[] ="<**2**>ถือจ่ายปีนี้|ระดับ";
	$heading_text[] ="<**2**>ถือจ่ายปีนี้|ขั้น";
	$heading_text[] ="<**2**>ถือจ่ายปีนี้|อัตรา";
	$heading_text[] = "ปรับ|ลด";
	$heading_text[] = "<**3**>จำนวนเงิน|เลื่อนขั้น";
	$heading_text[] = "<**3**>จำนวนเงิน|ปรับวุฒิ";
    $heading_text[] = "ปรับ|อัตรา";
    $heading_text[] = "อัตรา|ตั้งใหม่";

	// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
	//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
	//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
	//									TNUM-xn คือ เปลี่ยนเป็นตัวเลขของไทย  x คือ ไม่มี คอมม่า ถ้าไม่มี x ก็เป็นรูปแบบตัวเลขปกติ  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									TNUM0-xn คือ เปลี่ยนเป็นตัวเลขของไทย  x คือ ไม่มี คอมม่า ถ้าไม่มี x ก็เป็นรูปแบบตัวเลขปกติ  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//									ENUM-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี คอมม่า ถ้าไม่มี x ก็เป็นรูปแบบตัวเลขปกติ   n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									ENUM0-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี คอมม่า ถ้าไม่มี x ก็เป็นรูปแบบตัวเลขปกติ  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//
	if ($have_pic) {
		$column_function[] = "";
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	} else {
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
	} 
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	if($have_pic)
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	else
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	
	if($have_pic)
	$data_align = array("C","C","L","L","C","L","C","C","C","R","R","R","R","R","R");  
	else
	$data_align = array("C","L","L","C","L","C","C","C","R","R","R","R","R","R");

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