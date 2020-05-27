<?
//	fix head for rpt_R004009

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
		$heading_width[0] = "120";
		$heading_width[1] = "20";
		$heading_width[2] = "20";
		$heading_width[3] = "20";
		$heading_width[4] = "20";
		$heading_width[5] = "20";
		$heading_width[6] = "20";
		$heading_width[7] = "20";
	} else {
		$heading_width[0] = "110";
		$heading_width[1] = "12";
		$heading_width[2] = "12";
		$heading_width[3] = "12";
		$heading_width[4] = "12";
		$heading_width[5] = "12";
		$heading_width[6] = "12";
		$heading_width[7] = "12";
		$heading_width[8] = "12";
		$heading_width[9] = "12";
		if ($NOT_LEVEL_NO_O4=="Y") {
			$heading_width[10] = "12";
			$heading_width[11] = "12";
			$heading_width[12] = "12";
			$heading_width[13] = "15";
		} else {
			$heading_width[10] = "12";
			$heading_width[11] = "12";
			$heading_width[12] = "12";
			$heading_width[13] = "12";
			$heading_width[14] = "15";
		}
	}
	
	$heading_text[0] = "หลักสูตร|";
	for($i=0; $i<=count($ARR_LEVEL_SHORTNAME); $i++){ 
		$tmp_level_shortname = $ARR_LEVEL_SHORTNAME[$i];
		$heading_text[$i+1] = "<**1**>ระดับตำแหน่ง|$tmp_level_shortname";
		$column_function[$i+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
		$heading_align[$i+1] = "C";
		$data_align[$i+1] = "R";
	} // end for
	if ($ISCS_FLAG==1)
		$heading_text[7] = "รวม|";
	else
		if ($NOT_LEVEL_NO_O4=="Y") 
			$heading_text[13] = "รวม|";
		else
			$heading_text[14] = "รวม|";
	$column_function[$i+1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_align[$i+1] = "C";
	$data_align[$i+1] = "R";

	$heading_align = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");	
	
	$data_align = array("L","R","R","R","R","R","R","R","R","R","R","R","R","R","R");

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