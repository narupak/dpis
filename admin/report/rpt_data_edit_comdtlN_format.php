<?
//	fix head for  P1702

	if($COM_TYPE=="0202"||"5120"){
	        if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "22";
		$heading_width[2] = "25";
		$heading_width[3] = "21";
		$heading_width[4] = "15";
		$heading_width[5] = "12";
		$heading_width[6] = "14";
		$heading_width[7] = "14";
		$heading_width[8] = "21";
		$heading_width[9] = "12";
		$heading_width[10] = "17";
		$heading_width[11] = "22";
		$heading_width[12] = "15";
		$heading_width[13] = "12";	
		$heading_width[14] = "12";
		$heading_width[15] = "14";
		$heading_width[16] = "15";	
		$heading_width[17] = "15";		
		     }else if ($FLAG_RTF) {
		$heading_width[0] = "4";
		$heading_width[1] = "7";
		$heading_width[2] = "9";
		$heading_width[3] = "7";
		$heading_width[4] = "6";
		$heading_width[5] = "4";
		$heading_width[6] = "4";
		$heading_width[7] = "4";
		$heading_width[8] = "7";
		$heading_width[9] = "5";
		$heading_width[10] = "6";
		$heading_width[11] = "7";
		$heading_width[12] = "6";
		$heading_width[13] = "4";	
		$heading_width[14] = "4";
		$heading_width[15] = "4";
		$heading_width[16] = "6";	
		$heading_width[17] = "6";			
						}
	}else{
	    if (!$FLAG_RTF) {
		$heading_width[0] = "9";
		$heading_width[1] = "28";
		$heading_width[2] = "27";
		$heading_width[3] = "24";
		$heading_width[4] = "24";
		$heading_width[5] = "12";
		$heading_width[6] = "16";
		$heading_width[7] = "16";
		$heading_width[8] = "25";
		$heading_width[9] = "24";
		$heading_width[10] = "17";
		$heading_width[11] = "19";
		$heading_width[12] = "17";
		$heading_width[13] = "18";	
		$heading_width[14] = "25";
	  }else if ($FLAG_RTF) {	
			   $heading_width[0] = "4";
				$heading_width[1] = "9";
				$heading_width[2] = "9";
				$heading_width[3] = "8";
				$heading_width[4] = "8";
				$heading_width[5] = "4";
				$heading_width[6] = "5";
				$heading_width[7] = "5";
				$heading_width[8] = "8";
				$heading_width[9] = "7";
				$heading_width[10] = "6";
				$heading_width[11] = "6";
				$heading_width[12] = "6";
				$heading_width[13] = "7";	
				$heading_width[14] = "8";
				}
	}

	if($COM_TYPE=="0202"||"5120"){
		$heading_text[0] = "ลำดับ|ที่|";
		$heading_text[1] = "$FULLNAME_HEAD";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "*Enter*$CARDNO_TITLE";
		$heading_text[2] = "วุฒิที่ได้รับเพิ่มขึ้น/|สถานศึกษา/วันที่|สำเร็จการศึกษา";
		$heading_text[3] = $COM_HEAD_01."เดิม|ตำแหน่ง/สังกัด|";
		$heading_text[4] = $COM_HEAD_01."เดิม|ตำแหน่ง|ประเภท";
		$heading_text[5] = $COM_HEAD_01."เดิม|ระดับ|";
		$heading_text[6] = $COM_HEAD_01."เดิม|เลขที่|";
		$heading_text[7] = $COM_HEAD_01."เดิม|เงินเดือน|";
		$heading_text[8] = "<**2**>สอบแข่งขันได้|ตำแหน่ง|";
		$heading_text[9] = "<**2**>สอบแข่งขันได้|ลำดับที่|";
		$heading_text[10] = "<**2**>สอบแข่งขันได้|ประกาศผล|การสอบของ";
		$heading_text[11] = $COM_HEAD_03."ที่รับโอน|ตำแหน่ง/สังกัด|";
		$heading_text[12] = $COM_HEAD_03."ที่รับโอน|ตำแหน่ง|ประเภท";
		$heading_text[13] = $COM_HEAD_03."ที่รับโอน|ระดับ|";
		$heading_text[14] = $COM_HEAD_03."ที่รับโอน|เลขที่|";
		$heading_text[15] = $COM_HEAD_03."ที่รับโอน|เงินเดือน|";
		$heading_text[16] = "ตั้งแต่วันที่||";
		$heading_text[17] = "หมายเหตุ||";
	}else{
		$heading_text[0] = "ลำดับ|ที่";
		$heading_text[1] = "$FULLNAME_HEAD";
		if ($CARDNO_FLAG==1) $heading_text[1] .= "*Enter*$CARDNO_TITLE";
		$heading_text[2] = "วุฒิ/สาขา/|สถานศึกษา";
		$heading_text[3] = $COM_HEAD_01."เดิม|ตำแหน่ง/สังกัด";
		$heading_text[4] = $COM_HEAD_01."เดิม|ตำแหน่งประเภท";
		$heading_text[5] = $COM_HEAD_01."เดิม|ระดับ";
		$heading_text[6] = $COM_HEAD_01."เดิม|เลขที่";
		$heading_text[7] = $COM_HEAD_01."เดิม|เงินเดือน";
		$heading_text[8] = $COM_HEAD_02."ที่ย้าย|ตำแหน่ง/สังกัด";
		$heading_text[9] = $COM_HEAD_02."ที่ย้าย|ตำแหน่งประเภท";
		$heading_text[10] = $COM_HEAD_02."ที่ย้าย|ระดับ";
		$heading_text[11] = $COM_HEAD_02."ที่ย้าย|เลขที่";
		$heading_text[12] = $COM_HEAD_02."ที่ย้าย|เงินเดือน";
		$heading_text[13] = "ตั้งแต่วันที่|";
		$heading_text[14] = "หมายเหตุ|";
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
	if($COM_TYPE=="0202"||"5120"){
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[17] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		
		$data_align = array("C","L","L","L","C","C","C","R","L","R","L","L","C","C","C","R","C","L");
	} else {
		$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[5] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[6] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[7] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[8] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM-x":"ENUM-x");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		
		$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		
		$data_align = array("C","L","L","L","C","C","C","R","L","C","C","C","R","C","L");
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