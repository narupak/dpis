<?
//	fix head for rpt_R010007
	
	if($search_pt_code == 'O') $search_pt_name = "ทั่วไป";
	elseif($search_pt_code == 'K') $search_pt_name = "วิชาการ";
	elseif($search_pt_code == 'D') $search_pt_name = "อำนวยการ";
	elseif($search_pt_code == 'M') $search_pt_name = "บริหาร";
	else $search_pt_name = "";

	if (!$search_per_type) $search_per_type = 1;

	unset($ARR_pt_name);
	unset($ARR_LEVEL_NO);
	unset($ARR_POSITION_TYPE);
	unset($ARR_GENDER);
	$cmd = "select LEVEL_NO, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL 
					where (LEVEL_ACTIVE=1) and (LEVEL_NO in $LIST_LEVEL_NO) ".($search_pt_code ? "and (LEVEL_NO LIKE '%$search_pt_code%')" : "and (LEVEL_NO in $LIST_LEVEL_NO)")." ".($search_per_type ? "and (PER_TYPE = $search_per_type)" : "")." order by LEVEL_SEQ_NO ,LEVEL_NO"; 
	$db_dpis->send_cmd($cmd);
//	echo "cmd:$cmd<br>";
	
	if ($FLAG_RTF) 
		$ARR_COL=array("ช","ญ","ร");		//SEX
	else
		$ARR_COL=array("ชาย","หญิง","รวม");		//SEX
	while($data = $db_dpis->get_array()) {
		$POSITION_TYPE = trim($data[POSITION_TYPE]);
		if (!in_array($POSITION_TYPE, $ARR_pt_name)) $ARR_pt_name[] = $POSITION_TYPE;
		$LEVEL_NAME = trim($data[POSITION_LEVEL]);
//		echo "$POSITION_TYPE-$LEVEL_NAME<br>";
		if ($POSITION_TYPE=="อำนวยการ" || $POSITION_TYPE=="บริหาร") $LEVEL_NAME = $POSITION_TYPE . $LEVEL_NAME;
		$ARR_POSITION_TYPE[$POSITION_TYPE][] = $LEVEL_NAME;

		$ARR_LEVEL_NO[$LEVEL_NAME] = trim($data[LEVEL_NO]);
		$ARR_GENDER[$POSITION_TYPE][] = $ARR_COL;
	}
//	echo "arr_pt_name=".implode(",", $ARR_pt_name)."<br>";

	$heading_text[0] = "|ส่วนราชการ|";
	if ($ISCS_FLAG==1 || $BKK_FLAG==1) 
		$heading_width[0] = "70";
	else
		$heading_width[0] = "40";
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[0] = "C";
	$data_align[0] = "L";
	$cnt = 0;
	for($k=0;$k<count($ARR_pt_name);$k++) {
		$count_position_type=count($ARR_POSITION_TYPE[$ARR_pt_name[$k]]);
		$cnt1 = 0;
		for($i=0;$i<$count_position_type;$i++) {
			$tmp_position_type=$ARR_POSITION_TYPE[$ARR_pt_name[$k]][$i];
			$count=count($ARR_GENDER[$ARR_pt_name[$k]][$i]);
			for($j=0;$j<$count;$j++) {
				$tmp_gender = $ARR_GENDER[$ARR_pt_name[$k]][$i][$j];
				$cnt++; $cnt1++;
				$heading_text[$cnt] = "<**".($k+1)."**>".$ARR_pt_name[$k]."|<**".($i+1)."**>$tmp_position_type|$tmp_gender";
				if ($ISCS_FLAG==1 || $BKK_FLAG==1) 
					$heading_width[$cnt] = "10";
				else
					$heading_width[$cnt] = "7";
				$column_function[$cnt] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
				$heading_align[$cnt] = "C";
				$data_align[$cnt] = "R";
			}
		}
	}
	$heading_text[$cnt+1] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>จำนวนรวม|ชาย";
	$heading_text[$cnt+2] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>จำนวนรวม|หญิง";
	$heading_text[$cnt+3] = "<**".($k+2)."**>|<**".($cnt1+2)."^**>จำนวนรวม|รวม";
	$heading_width[$cnt+1] = "12";
	$heading_width[$cnt+2] = "12";
	$heading_width[$cnt+3] = "12";
	$column_function[$cnt+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$cnt+2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[$cnt+3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[$cnt+1] = "C";
	$heading_align[$cnt+2] = "C";
	$heading_align[$cnt+3] = "C";
	$data_align[$cnt+1] = "R";
	$data_align[$cnt+2] = "R";
	$data_align[$cnt+3] = "R";
	
	$sum_total_w = 0;
	for($i=0; $i<count($heading_width); $i++) $sum_total_w += (int)$heading_width[$i];

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