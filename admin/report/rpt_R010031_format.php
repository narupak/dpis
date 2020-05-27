<?
//	fix head for rpt_R010031

	if ($ISCS_FLAG==1)
		$head_width = 15;
	else
		$head_width = 8;
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
	$heading_width[10] = $head_width;
	$heading_width[11] = $head_width;
	$heading_width[12] = $head_width;
	$heading_width[13] = $head_width;
	$heading_width[14] = $head_width;
	$heading_width[15] = $head_width;
	if ($ISCS_FLAG!=1) {
		$heading_width[16] = $head_width;
		$heading_width[17] = $head_width;
		$heading_width[18] = $head_width;
		$heading_width[19] = $head_width;
		$heading_width[20] = $head_width;
		$heading_width[21] = $head_width;
		$heading_width[22] = $head_width;
		$heading_width[23] = $head_width;
		$heading_width[24] = $head_width;
		$heading_width[25] = $head_width;
		$heading_width[26] = $head_width;
		$heading_width[27] = $head_width;
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_width[28] = $head_width;
			$heading_width[29] = $head_width;
		}
	}
	
	if (!$heading_name) $heading_name="ตัวแปร heading_name";
	if ($ISCS_FLAG==1) {
		$heading_text[0] = "|ลำดับ|";
		$heading_text[1] = "|$heading_name|";
		$heading_text[2] = "<**1**>บริหาร|<**6**>บริหารสูง|ชาย";
		$heading_text[3] = "<**1**>บริหาร|<**6**>บริหารสูง|หญิง";
		$heading_text[4] = "<**1**>บริหาร|<**7**>บริหารต้น|ชาย";
		$heading_text[5] = "<**1**>บริหาร|<**7**>บริหารต้น|หญิง";
		$heading_text[6] = "<**2**>อำนวยการ|<**8**>อำนวยการสูง|ชาย";
		$heading_text[7] = "<**2**>อำนวยการ|<**8**>อำนวยการสูง|หญิง";
		$heading_text[8] = "<**2**>อำนวยการ|<**9**>อำนวยการต้น|ชาย";
		$heading_text[9] = "<**2**>อำนวยการ|<**9**>อำนวยการต้น|หญิง";
		$heading_text[10] = "<**3**>วิชาการ|<**10**>ทรงคุณวุฒิ|ชาย";
		$heading_text[11] = "<**3**>วิชาการ|<**10**>ทรงคุณวุฒิ|หญิง";
		$heading_text[12] = "<**3**>วิชาการ|<**11**>เชี่ยวชาญ|ชาย";
		$heading_text[13] = "<**3**>วิชาการ|<**11**>เชี่ยวชาญ|หญิง";
		$heading_text[14] = "<**5**>รวม|<**19^**>|ชาย";
		$heading_text[15] = "<**5**>รวม|<**19^**>|หญิง";
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
		
		$data_align = array("C","L","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
	} else {
		$heading_text[0] = "|ลำดับ||";
		$heading_text[1] = "|$heading_name||";
		$heading_text[2] = "<**1**>บริหาร|<**6**>|<**6^**>บริหารสูง|ชาย";
		$heading_text[3] = "<**1**>บริหาร|<**6**>|<**6^**>บริหารสูง|หญิง";
		$heading_text[4] = "<**1**>บริหาร|<**7**>|<**7^**>บริหารต้น|ชาย";
		$heading_text[5] = "<**1**>บริหาร|<**7**>|<**7^**>บริหารต้น|หญิง";
		$heading_text[6] = "<**2**>อำนวยการ|<**8**>|<**8^**>อำนวยการสูง|ชาย";
		$heading_text[7] = "<**2**>อำนวยการ|<**8**>|<**8^**>อำนวยการสูง|หญิง";
		$heading_text[8] = "<**2**>อำนวยการ|<**9**> |<**9^**>อำนวยการต้น|ชาย";
		$heading_text[9] = "<**2**>อำนวยการ|<**9**> |<**9^**>อำนวยการต้น|หญิง";
		$heading_text[10] = "<**3**>วิชาการ|<**10**>ทรง|<**10^**>คุณวุฒิ|ชาย";
		$heading_text[11] = "<**3**>วิชาการ|<**10**>ทรง|<**10^**>คุณวุฒิ|หญิง";
		$heading_text[12] = "<**3**>วิชาการ|<**11**> |<**11^**>เชี่ยวชาญ|ชาย";
		$heading_text[13] = "<**3**>วิชาการ|<**11**> |<**11^**>เชี่ยวชาญ|หญิง";
		$heading_text[14] = "<**3**>วิชาการ|<**12**>ชำนาญ|<**12^**>การพิเศษ|ชาย";
		$heading_text[15] = "<**3**>วิชาการ|<**12**>ชำนาญ|<**12^**>การพิเศษ|หญิง";
		$heading_text[16] = "<**3**>วิชาการ|<**13**>ชำนาญ|<**13^**>การ|ชาย";
		$heading_text[17] = "<**3**>วิชาการ|<**13**>ชำนาญ|<**13^**>การ|หญิง";
		$heading_text[18] = "<**3**>วิชาการ|<**14**>ปฏิบัติ|<**14^**>การ|ชาย";
		$heading_text[19] = "<**3**>วิชาการ|<**14**>ปฏิบัติ|<**14^**>การ|หญิง";
		if ($NOT_LEVEL_NO_O4!="Y") {
			$heading_text[20] = "<**4**>ทั่วไป|<**15**>ทักษะ|<**15^**>พิเศษ|ชาย";
			$heading_text[21] = "<**4**>ทั่วไป|<**15**>ทักษะ|<**15^**>พิเศษ|หญิง";
			$heading_text[22] = "<**4**>ทั่วไป|<**16**> |<**16^**>อาวุโส|ชาย";
			$heading_text[23] = "<**4**>ทั่วไป|<**16**> |<**16^**>อาวุโส|หญิง";
			$heading_text[24] = "<**4**>ทั่วไป|<**17**>ชำนาญ|<**17^**>งาน|ชาย";
			$heading_text[25] = "<**4**>ทั่วไป|<**17**>ชำนาญ|<**17^**>งาน|หญิง";
			$heading_text[26] = "<**4**>ทั่วไป|<**18**>ปฏิบัติ|<**18^**>งาน|ชาย";
			$heading_text[27] = "<**4**>ทั่วไป|<**18**>ปฏิบัติ|<**18^**>งาน|หญิง";
			$heading_text[28] = "<**5**>|<**19^**>รวม|<**19^**>|ชาย";
			$heading_text[29] = "<**5**>|<**19^**>รวม|<**19^**>|หญิง";
		} else {
			$heading_text[20] = "<**4**>ทั่วไป|<**16**> |<**16^**>อาวุโส|ชาย";
			$heading_text[21] = "<**4**>ทั่วไป|<**16**> |<**16^**>อาวุโส|หญิง";
			$heading_text[22] = "<**4**>ทั่วไป|<**17**>ชำนาญ|<**17^**>งาน|ชาย";
			$heading_text[23] = "<**4**>ทั่วไป|<**17**>ชำนาญ|<**17^**>งาน|หญิง";
			$heading_text[24] = "<**4**>ทั่วไป|<**18**>ปฏิบัติ|<**18^**>งาน|ชาย";
			$heading_text[25] = "<**4**>ทั่วไป|<**18**>ปฏิบัติ|<**18^**>งาน|หญิง";
			$heading_text[26] = "<**5**>|<**19^**>รวม|<**19^**>|ชาย";
			$heading_text[27] = "<**5**>|<**19^**>รวม|<**19^**>|หญิง";
		}
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
		
		$data_align = array("C","L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");
	}

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
	$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($ISCS_FLAG!=1) {
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[18] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[19] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[20] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[21] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[22] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[23] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[24] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[25] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[26] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[27] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($NOT_LEVEL_NO_O4!="Y") {
			$column_function[28] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[29] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
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