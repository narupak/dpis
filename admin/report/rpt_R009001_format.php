<?php
//	fix head for rpt_R009001

	$heading_width[] = "15"; //ลำดับ
	if ($have_pic) {
            if(!$have_al && !$have_am){
		$heading_width[] = "30"; //รูป
		$heading_width[] = "70"; //ชื่อ - สกุล
            }else{
                $heading_width[] = "20"; //รูป
		$heading_width[] = "40"; //ชื่อ - สกุล
            }    
	} else {
            if(!$have_al && !$have_am){
		$heading_width[] = "90"; //ชื่อ - สกุล "70"
            }else{
                $heading_width[] = "50"; //ชื่อ - สกุล "70"
            }    
	}
	$heading_width[] = "25"; //เลขทีตำแหน่ง 
	if ($have_pic) {
	$heading_width[] = "55"; //ตำแหน่ง "100"
	}else{
	$heading_width[] = "64"; //ตำแหน่ง "100"
	}
	$heading_width[] = "10"; //ครั้งที่ 12
	$heading_width[] = "35"; //คะแนนผลสำเร็จของงาน 30
	$heading_width[] = "28"; //คะแนนสมรรถนะ 30
	$heading_width[] = "20"; //ผลรวม 30
        if($have_am && !$have_al){
            $heading_width[] = "40"; 
        }else if($have_al && !$have_am){
            $heading_width[] = "40"; 
        }else if($have_al && $have_am){
            $heading_width[] = "40"; 
        }
        
	
	$heading_text[] = "ลำดับ";
	if ($have_pic) {
		$heading_text[] = "รูป|";
		$heading_text[] = "ชื่อ - สกุล|";
	} else {
		$heading_text[] = "ชื่อ - สกุล|";
	}
	$heading_text[] = "เลขที่ตำแหน่ง";
	$heading_text[] = "ตำแหน่ง";
	$heading_text[] = "ครั้งที่";
	$heading_text[] = "คะแนนผลสำเร็จของงาน";//"ผลสำเร็จของงาน";
	$heading_text[] = "คะแนนสมรรถนะ";//สมรรถนะ";
	$heading_text[] = "ผลรวม";//"ระดับผลการประเมิน";
        if($have_am && !$have_al){
            $heading_text[] = "ระดับผลการประเมิน(หลัก)";
        }else if($have_al && !$have_am){
            $heading_text[] = "ระดับผลการประเมิน(ย่อย)";
        }else if($have_al && $have_am){
            $heading_text[] = "ระดับผลการประเมิน|(หลัก/ย่อย)";
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
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	if ($have_pic) {
		$column_function[] = "";
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	} else {
		$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	} 
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-xn":"ENUM-xn");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
	$column_function[] = (($NUMBER_DISPLAY==2)?"TNUM-2":"ENUM-2");
        if($have_am || $have_al){
            $column_function[] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM"); 
        }
	
	if($have_pic)
	$heading_align = array("C","C","C","C","C","C","C","C","C","C");	
	else
	$heading_align = array("C","C","C","C","C","C","C","C","C");
	
	if($have_pic)
	$data_align = array("R","C","C","L","L","C","R","R","C","C");
	else
	$data_align = array("R","L","C","L","C","R","R","R","R");

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