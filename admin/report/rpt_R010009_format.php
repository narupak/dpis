<?
//	fix head for rpt_R010009

	if (!$search_per_type) $search_per_type = 1;
	
	unset($ARR_pt_name);
	unset($ARR_LEVEL_NO);
	unset($ARR_POSITION_TYPE);
	if ($ISCS_FLAG==1) $where = "and LEVEL_NO in $LIST_LEVEL_NO"; 
	$cmd = "select LEVEL_NO, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL 
					where (LEVEL_ACTIVE=1) ".($search_pt_code ? "and (LEVEL_NO LIKE '%$search_pt_code%')" : "and (LEVEL_NO in $LIST_LEVEL_NO)")." ".($search_per_type ? "and (PER_TYPE = $search_per_type)" : "")." $where order by LEVEL_SEQ_NO ,LEVEL_NO"; 
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$POSITION_TYPE = trim($data[POSITION_TYPE]);
		$LEVEL_NAME = trim($data[POSITION_LEVEL]);
		if ($POSITION_TYPE=="อำนวยการ" || $POSITION_TYPE=="บริหาร") $LEVEL_NAME = $POSITION_TYPE . $LEVEL_NAME;
		$ARR_POSITION_TYPE[$POSITION_TYPE][] = $LEVEL_NAME;
		if (!in_array($POSITION_TYPE, $ARR_pt_name)) $ARR_pt_name[] = $POSITION_TYPE;
	
		$ARR_LEVEL_NO[$LEVEL_NAME] = trim($data[LEVEL_NO]);
	}
//	echo "arr_pt_name:".implode(",", $ARR_pt_name)."<br>";

	$cnt_pt = count($ARR_pt_name);
	$heading_width[0] = "100";
	if ($cnt_pt > 1)
		$heading_text[0] = "|ส่วนราชการ|";
	else
		$heading_text[0] = "ส่วนราชการ|";
	$heading_align[0] = "C";
	$data_align[0] = "L";
	$column_function[0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$cnt = 0;
	for($k=0;$k<$cnt_pt; $k++){
		$pt_name = $ARR_pt_name[$k];
		$count_position_type=count($ARR_POSITION_TYPE[$pt_name]);
		for($i=0; $i<$count_position_type; $i++){ 
			$tmp_pt_type = $ARR_POSITION_TYPE[$pt_name][$i];
			$heading_width[$cnt+1] = "20";
			if ($cnt_pt > 1)
				$heading_text[$cnt+1] = "<**".($k+1)."**>$pt_name|$tmp_pt_type";
			else
				$heading_text[$cnt+1] = "<**1**>$pt_name|$tmp_pt_type";
			$heading_align[$cnt+1] = "C";
			$data_align[$cnt+1] = "R";
			$column_function[$cnt+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
			$cnt++;
		} // end for
	} // end for $k
	$heading_width[$cnt+1] = "24";
	if ($cnt_pt > 1)
		$heading_text[$cnt+1] = "|รวม|";
	else
		$heading_text[$cnt+1] = "รวม|";
	$heading_align[$cnt+1] = "C";
	$data_align[$cnt+1] = "R";
	$column_function[$cnt+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
//	echo "heading_text:".implode(",",$heading_text).", heading_width:".implode(",",$heading_width)."<br>";

	$total_head_width = 0;
	for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	$sum_total_w = $total_head_width;
	
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