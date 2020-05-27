<?
//	fix head for rpt_R010036

	if ($ISCS_FLAG==1) 
		$ARR_LEVEL_GROUP = array("M", "D", "K");
	else
		$ARR_LEVEL_GROUP = array("M", "D", "K", "O");
	$ARR_LEVEL_GROUP_NAME["M"] = "บริหาร";
	$ARR_LEVEL_GROUP_NAME["D"] = "อำนวยการ";
	$ARR_LEVEL_GROUP_NAME["K"] = "วิชาการ";
	if ($ISCS_FLAG!=1) 
		$ARR_LEVEL_GROUP_NAME["O"] = "ทั่วไป";

	$ARR_LEVEL["M"] = array("M2", "M1");
	$ARR_LEVEL["D"] = array("D2", "D1");
	if ($ISCS_FLAG==1) 
		$ARR_LEVEL["K"] = array("K5", "K4");
	else
		$ARR_LEVEL["K"] = array("K5", "K4", "K3", "K2", "K1");
	if ($ISCS_FLAG!=1) 
		$ARR_LEVEL["O"] = array("O4", "O3", "O2", "O1");
	$ARR_LEVEL_NAME["M2"][1] = "บริหารสูง";
	$ARR_LEVEL_NAME["M1"][1] = "บริหารต้น";
	$ARR_LEVEL_NAME["D2"][1] = "อำนวยการสูง";
	$ARR_LEVEL_NAME["D1"][1] = "อำนวยการต้น";
	$ARR_LEVEL_NAME["K5"][1] = "ทรงคุณวุฒิ";
	$ARR_LEVEL_NAME["K4"][1] = "เชี่ยวชาญ";
	if ($ISCS_FLAG!=1) { 
		$ARR_LEVEL_NAME["K3"][1] = "ชำนาญการพิเศษ";
		$ARR_LEVEL_NAME["K2"][1] = "ชำนาญการ";
		$ARR_LEVEL_NAME["K1"][1] = "ปฏิบัติการ";
		$ARR_LEVEL_NAME["O4"][1] = "ทักษะพิเศษ";
		$ARR_LEVEL_NAME["O3"][1] = "อาวุโส";
		$ARR_LEVEL_NAME["O2"][1] = "ชำนาญงาน";
		$ARR_LEVEL_NAME["O1"][1] = "ปฏิบัติงาน";
	}
	
	$TOTAL_LEVEL = 0;
	foreach($ARR_LEVEL_GROUP as $LEVEL_GROUP) $TOTAL_LEVEL += count($ARR_LEVEL[$LEVEL_GROUP]);

	$heading_width[0] = "15";
	$heading_text[0] = "||ลำดับ|";
	if ($ISCS_FLAG==1)
		$heading_width[1] = "90";
	else
		$heading_width[1] = "50";
	$heading_text[1] = "||ส่วนราชการ|";
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[0] = "C";
	$heading_align[1] = "C";
	$data_align[0] = "C";
	$data_align[1] = "L";
	$cnt_level_grp = count($ARR_LEVEL_GROUP);
	$cnt_level_det = 0;
	$col_sum1 = "";
	$col_sum2 = "";
	for($i=0; $i<$cnt_level_grp; $i++){
		$LEVEL_GROUP = $ARR_LEVEL_GROUP[$i];
		for($j=0; $j<count($ARR_LEVEL[$LEVEL_GROUP]); $j++){
			$LEVEL_NO = $ARR_LEVEL[$LEVEL_GROUP][$j];
			$heading_text[$cnt_level_det*2+2] = "<**1**>ตำแหน่งประเภท|<**".($i+1)."**>".$ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP]."|<**".($cnt_level_det+1)."**>".$ARR_LEVEL_NAME[$LEVEL_NO][1]."|ครอง";
			$heading_text[$cnt_level_det*2+3] = "<**1**>ตำแหน่งประเภท|<**".($i+1)."**>".$ARR_LEVEL_GROUP_NAME[$LEVEL_GROUP]."|<**".($cnt_level_det+1)."**>".$ARR_LEVEL_NAME[$LEVEL_NO][1]."|กรอบ";
			if ($ISCS_FLAG==1) {
				$heading_width[$cnt_level_det*2+2] = "13";
				$heading_width[$cnt_level_det*2+3] = "13";
			} else {
				$heading_width[$cnt_level_det*2+2] = "8";
				$heading_width[$cnt_level_det*2+3] = "8";
			}
			$col_sum1 .= "-".($cnt_level_det*2+2);
			$col_sum2 .= "-".($cnt_level_det*2+3);
			$column_function[$cnt_level_det*2+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[$cnt_level_det*2+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$heading_align[$cnt_level_det*2+2] = "C";
			$heading_align[$cnt_level_det*2+3] = "C";
			$data_align[$cnt_level_det*2+2] = "R";
			$data_align[$cnt_level_det*2+3] = "R";
//			echo "idx:".($cnt_level_det*2+2)."->".$heading_text[$cnt_level_det*2+2]."<br>";
//			echo "idx:".($cnt_level_det*2+3)."->".$heading_text[$cnt_level_det*2+3]."<br>";
			$cnt_level_det++;
		}
	} // loop for
	$heading_text[$cnt_level_det*2+2] = "<**2**>|<**".($cnt_level_grp+1)."^**>รวม|<**".($cnt_level_det+1)."^**>|ครอง";
	$heading_text[$cnt_level_det*2+3] = "<**2**>|<**".($cnt_level_grp+1)."^**>รวม|<**".($cnt_level_det+1)."^**>|กรอบ";
//	echo "idx:".($cnt_level_det*2+2)."->".$heading_text[$cnt_level_det*2+2]."<br>";
//	echo "idx:".($cnt_level_det*2+3)."->".$heading_text[$cnt_level_det*2+3]."<br>";
	if ($ISCS_FLAG==1) {
		$heading_width[$cnt_level_det*2+2] = "13";
		$heading_width[$cnt_level_det*2+3] = "13";
	} else {
		$heading_width[$cnt_level_det*2+2] = "8";
		$heading_width[$cnt_level_det*2+3] = "8";
	}
	$column_function[$cnt_level_det*2+2] = "SUM".$col_sum1."|".(($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$cnt_level_det*2+3] = "SUM".$col_sum2."|".(($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[$cnt_level_det*2+2] = "C";
	$heading_align[$cnt_level_det*2+3] = "C";
	$data_align[$cnt_level_det*2+2] = "R";
	$data_align[$cnt_level_det*2+3] = "R";

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

//	echo "heading_text=".implode("^",$heading_text)."<br>";

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

//	echo "COLUMN_FORMAT=$COLUMN_FORMAT<br>";
?>