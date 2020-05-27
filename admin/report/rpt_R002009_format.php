<?
//	fix head for rpt_R002009

	if ($search_per_type == 1) {
		$cmd = "select LEVEL_NO, LEVEL_NAME, POSITION_LEVEL as LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (LEVEL_NO in $LIST_LEVEL_NO) 
						order by  LEVEL_SEQ_NO,LEVEL_NO";
	} else {
		$cmd = " select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL 
						where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO";
	}
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){ 
		$ARR_LEVEL_NO[] = $data[LEVEL_NO];		
		if(trim($data[LEVEL_SHORTNAME])){	$LNAME=str_replace("ระดับ","",$data[LEVEL_SHORTNAME]); }
		else{	$LNAME=str_replace("กลุ่มงาน","",$data[LEVEL_NAME]); }
		if ($data[LEVEL_NO]=="O2") $LNAME = "ชำนาญ*Enter*งาน";
		elseif ($data[LEVEL_NO]=="O4") $LNAME = "ทักษะ*Enter*พิเศษ";
		elseif ($data[LEVEL_NO]=="K2") $LNAME = "ชำนาญ*Enter*การ";
		elseif ($data[LEVEL_NO]=="K3") $LNAME = "ชำนาญ*Enter*การพิเศษ";
		elseif ($data[LEVEL_NO]=="K4") $LNAME = "เชี่ยว*Enter*ชาญ";
		elseif ($data[LEVEL_NO]=="K5") $LNAME = "ทรง*Enter*คุณวุฒิ";
		elseif ($data[LEVEL_NO]=="D1") $LNAME = "อำนวย*Enter*การต้น";
		elseif ($data[LEVEL_NO]=="D2") $LNAME = "อำนวย*Enter*การสูง";
		elseif ($data[LEVEL_NO]=="M1") $LNAME = "บริหาร*Enter*ต้น";
		elseif ($data[LEVEL_NO]=="M2") $LNAME = "บริหาร*Enter*สูง";
		$ARR_LEVEL_SHORTNAME[] = $LNAME;
	}

	$heading_text = (array) null;
	$heading_width = (array) null;
	$column_function = (array) null;
	$heading_align = (array) null;
	$data_align = (array) null;
	
	if (!$heading_name) $heading_name="ตัวแปร heading_name";
	$heading_text[] = "$heading_name|";
	if ($ISCS_FLAG==1)
		$heading_width[] = "120";
	else
		$heading_width[] = "85";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[] = "C";
	$data_align[] = "L";
	$cnt = count($ARR_LEVEL_SHORTNAME);
	for($i=0; $i<$cnt; $i++){ 
		$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
		$heading_text[] = "<**1**>ระดับตำแหน่ง|$tmp_level_shortname";
		if ($ISCS_FLAG==1)
			$heading_width[] = "15";
		else
			$heading_width[] = "12";
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$heading_align[] = "C";
		$data_align[] = "R";
	} // end for
	$heading_text[] = "รวม|";
	$heading_width[] = "15";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[] = "C";
	$data_align[] = "R";
	$heading_text[] = "$PERSON_TYPE[$search_per_type]|ทั้งหมด";
	$heading_width[] = "20";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[] = "C";
	$data_align[] = "R";
	$heading_text[] = "ร้อยละ|";
	$heading_width[] = "15";
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$heading_align[] = "C";
	$data_align[] = "R";

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