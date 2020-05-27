<?
//	fix head for rpt_R004015

	if ($search_per_type == 1) {
		$cmd = "select LEVEL_NO, LEVEL_NAME, POSITION_LEVEL as LEVEL_SHORTNAME from PER_LEVEL where (LEVEL_ACTIVE=1) and (LEVEL_NO in $LIST_LEVEL_NO) 
						order by  LEVEL_SEQ_NO,LEVEL_NO";
	} else {
		$cmd = " select LEVEL_NO, LEVEL_NAME, LEVEL_SHORTNAME from PER_LEVEL 
						where (LEVEL_ACTIVE=1) and (PER_TYPE = $search_per_type) order by LEVEL_SEQ_NO,LEVEL_NO ";
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

	if ($ISCS_FLAG==1) {
		$heading_width[0] = "100";
		$heading_width[1] = "20";
		$heading_width[2] = "20";
		$heading_width[3] = "20";
		$heading_width[4] = "20";
		$heading_width[5] = "20";
		$heading_width[6] = "20";
		$heading_width[7] = "20";
		$heading_width[8] = "20";
		$heading_width[9] = "20";
	} else {
		$heading_width[0] = "75";
		$heading_width[1] = "12";
		$heading_width[2] = "12";
		$heading_width[3] = "12";
		if ($NOT_LEVEL_NO_O4=="Y") {
			$heading_width[4] = "12";
			$heading_width[5] = "12";
			$heading_width[6] = "12";
			$heading_width[7] = "12";
			$heading_width[8] = "12";
			$heading_width[9] = "12";
			$heading_width[10] = "12";
			$heading_width[11] = "12";
			$heading_width[12] = "12";
			$heading_width[13] = "20";
			$heading_width[14] = "20";
			$heading_width[15] = "20";
		} else {
			$heading_width[4] = "12";
			$heading_width[5] = "12";
			$heading_width[6] = "12";
			$heading_width[7] = "12";
			$heading_width[8] = "12";
			$heading_width[9] = "12";
			$heading_width[10] = "12";
			$heading_width[11] = "12";
			$heading_width[12] = "12";
			$heading_width[13] = "12";
			$heading_width[14] = "20";
			$heading_width[15] = "20";
			$heading_width[16] = "20";
		}
	}
	
	if($search_per_type==1) { $search_per_name = "ขรก."; }
	else if($search_per_type==2) { $search_per_name = "ลจปจ."; }
	else if($search_per_type==3) { $search_per_name = "พรก."; }
	else if($search_per_type==4) { $search_per_name = "ลจชค."; }

	if (!$heading_name) $heading_name="ตัวแปร heading_name";
	$heading_text[0] = "|$heading_name||";
	for($i=0; $i<count($ARR_LEVEL_SHORTNAME); $i++){ 
		$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
		$heading_text[$i+1] = "<**1**>ระดับตำแหน่ง|$tmp_level_shortname|";
	} // end for
	$heading_text[(count($ARR_LEVEL_SHORTNAME)+1)] = "|รวม||";		
	$heading_text[(count($ARR_LEVEL_SHORTNAME)+2)] = "จำนวน|$search_per_name|ทั้งหมด|";	
	$heading_text[(count($ARR_LEVEL_SHORTNAME)+3)] = "|ร้อยละ||";	

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
	if ($ISCS_FLAG==1) {
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");	
	} else {
		$column_function[9] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[10] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[11] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[12] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[13] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$column_function[14] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		if ($NOT_LEVEL_NO_O4=="Y") {
			$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");	
		} else {
			$column_function[15] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$column_function[16] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");	
		}
//		$column_function[16] = (($NUMBER_DISPLAY==2)?"FORM(@14/@15)*100|TNUM-2":"FORM(@14/@15)*100|ENUM-2");
	}
	
	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
	
	$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

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