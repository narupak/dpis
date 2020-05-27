<?
//	fix head for rpt_R010035

	if ($ISCS_FLAG==1)
		$head_width = 15;
	else
		$head_width = 15;
	$heading_width[0] = "10";
	if ($ISCS_FLAG==1)
		$heading_width[1] = "65";
	else
		$heading_width[1] = "53";
	$heading_width[2] = $head_width;
	$heading_width[3] = $head_width;
	$heading_width[4] = $head_width;
	$heading_width[5] = $head_width;
	$heading_width[6] = $head_width;
	$heading_width[7] = $head_width;
	$heading_width[8] = $head_width;
	$heading_width[9] = $head_width;
	if ($ISCS_FLAG!=1) {
		$heading_width[10] = $head_width;
		$heading_width[11] = $head_width;
		$heading_width[12] = $head_width;
		$heading_width[13] = $head_width;
		$heading_width[14] = $head_width;
		$heading_width[15] = $head_width;
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_width[16] = $head_width;
		}
	}

	if (!$heading_name) $heading_name="ตัวแปร heading_name";
	if ($ISCS_FLAG==1) {
		$heading_text[0] = "|ลำดับ";
		$heading_text[1] = "|$heading_name";
		$heading_text[2] = "|เพศ";
		$heading_text[3] = "<**1**>บริหาร|ระดับสูง";
		$heading_text[4] = "<**1**>บริหาร|ระดับต้น";
		$heading_text[5] = "<**2**>อำนวยการ|ระดับสูง";
		$heading_text[6] = "<**2**>อำนวยการ|ระดับต้น";
		$heading_text[7] = "<**3**>วิชาการ|ทรงคุณวุฒิ";
		$heading_text[8] = "<**3**>วิชาการ|เชี่ยวชาญ";
		$heading_text[9] = "รวม|";
	} else {
		$heading_text[0] = "|ลำดับ|";
		$heading_text[1] = "|$heading_name|";
		$heading_text[2] = "|เพศ";
		$heading_text[3] = "<**1**>บริหาร||ระดับสูง";
		$heading_text[4] = "<**1**>บริหาร||ระดับต้น";
		$heading_text[5] = "<**2**>อำนวยการ||ระดับสูง";
		$heading_text[6] = "<**2**>อำนวยการ||ระดับต้น";
		$heading_text[7] = "<**3**>วิชาการ|ทรง|คุณวุฒิ";
		$heading_text[8] = "<**3**>วิชาการ||เชี่ยวชาญ";
		$heading_text[9] = "<**3**>วิชาการ|ชำนาญ|การพิเศษ";
		$heading_text[10] = "<**3**>วิชาการ|ชำนาญ|การ";
		$heading_text[11] = "<**3**>วิชาการ|ปฏิบัติ|การ";
		if ($NOT_LEVEL_NO_O4=="Y") {
			$heading_text[12] = "<**4**>ทั่วไป||อาวุโส";
			$heading_text[13] = "<**4**>ทั่วไป|ชำนาญ|งาน";
			$heading_text[14] = "<**4**>ทั่วไป|ปฏิบัติ|งาน";
			$heading_text[15] = "|รวม|";
		} else {
			$heading_text[12] = "<**4**>ทั่วไป|ทักษะ|พิเศษ";
			$heading_text[13] = "<**4**>ทั่วไป||อาวุโส";
			$heading_text[14] = "<**4**>ทั่วไป|ชำนาญ|งาน";
			$heading_text[15] = "<**4**>ทั่วไป|ปฏิบัติ|งาน";
			$heading_text[16] = "|รวม|";
		}
	}

	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
	
	$data_align = array("C","L","C","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

	// function ประเภท aggregate มี  SUM, AVG, PERC ตามแนวบรรทัด (ROW)
	// 									SUM-columnindex1-columnindex2-....  Ex SUM-1-3-4-7 หมายถึง ผลรวมของ column ที่ 1,3,4 และ 7
	// 									AVG-columnindex1-columnindex2-....  Ex AVG-1-3-4-7 หมายถึง ค่าเฉลี่ยของ column ที่ 1,3,4 และ 7
	// 									PERCn-columnindex1-columnindex2-....  Ex PERCn-1-3-4-7 n คือ column ที่ เป็นเป้าหมายเปรียบเทียบร้อยละ (ค่า 1 ค่า ใน column 1 3 4 7) 
	//																												ถ้าไม่ใส่ค่า n ก็คือ ค่าสรุป (100%) PERC3-1-3-4-7 ก็คือ ค่าใน column ที่ 3 เป็นร้อยละเท่าใดของผลรวม column 1,3,4,7
	//	function ประเภท รูปแบบ (format) ซึ่งจะต้องตามหลัง function ประเภท aggregate เสมอ (ถ้ามี)
	//									TNUM-xn คือ เปลี่ยนเป็นตัวเลขของไทย  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									TNUM0-xn คือ เปลี่ยนเป็นตัวเลขของไทย   x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
	//									ENUM-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น -
	//									ENUM0-xn คือ แสดงเป็นเลขอาราบิค  x คือ ไม่มี comma ถ้าไม่มี x แสดงว่ามี comma ทุกหลัก 1,000  n คือ จำนวนทศนิยม ถ้ามีค่าเป็น ศูนย์ จะแสดงเป็น 0
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
	if ($ISCS_FLAG!=1) {
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($NOT_LEVEL_NO_O4!="Y") {
			$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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