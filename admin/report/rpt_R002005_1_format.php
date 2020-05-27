<?
//	fix head for rpt_R002005

$heading_width[0] = "97";
$heading_width[1] = "20";
$heading_width[2] = "12";
$heading_width[3] = "12";
$heading_width[4] = "12";
$heading_width[5] = "12";
$heading_width[6] = "12";
$heading_width[7] = "12";
$heading_width[8] = "12";
$heading_width[9] = "12";
$heading_width[10] = "12";
$heading_width[11] = "12";
$heading_width[12] = "12";
$heading_width[13] = "22";
$heading_width[14] = "20";

if (!$heading_name) $heading_name="ตัวแปร heading_name";
$heading_text[0] = "$heading_name|";
$heading_text[1] = "<**1**>ช่วงอายุราชการ|น้อยกว่า 5 ปี";
$heading_text[2] = "<**1**>ช่วงอายุราชการ|5 ปี";
$heading_text[3] = "<**1**>ช่วงอายุราชการ|6 ปี";
$heading_text[4] = "<**1**>ช่วงอายุราชการ|7 ปี";
$heading_text[5] = "<**1**>ช่วงอายุราชการ|8 ปี";
$heading_text[6] = "<**1**>ช่วงอายุราชการ|9 ปี";
$heading_text[7] = "<**1**>ช่วงอายุราชการ|10 ปี";
$heading_text[8] = "<**1**>ช่วงอายุราชการ|11 ปี";
$heading_text[9] = "<**1**>ช่วงอายุราชการ|12 ปี";
$heading_text[10] = "<**1**>ช่วงอายุราชการ|13 ปี";
$heading_text[11] = "<**1**>ช่วงอายุราชการ|14 ปี";
$heading_text[12] = "<**1**>ช่วงอายุราชการ|15 ปี";
$heading_text[13] = "<**1**>ช่วงอายุราชการ|มากกว่า 15 ปี";
$heading_text[14] = "รวม|(คน)";

$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	

$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
	
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
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
    $column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");


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