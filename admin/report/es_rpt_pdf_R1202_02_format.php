<?php
//หาช่วงวันที่ 15 / 30
    if(strlen($search_month)==1){
        $search_month = '0'.$search_month;
    }
    
    $search_year=($search_year-543);
    $varBgnDate = $search_year.'-'.$search_month.'-01';
    
    $cmdBtw="SELECT to_char( work_date,'YYYY-MM-DD') AS WORK_DATE FROM (
                SELECT (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+rownum AS WORK_DATE FROM all_objects
                WHERE (TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))-1+ rownum <= last_day(TO_DATE(:BEGINDATEAT, 'YYYY-MM-DD'))
              )";
    $cmdBtw=str_ireplace(":BEGINDATEAT","'".$varBgnDate."'",$cmdBtw);
    $cntDay = $db_dpis2->send_cmd($cmdBtw);
    $workDateIN = "";
    $comma=",";
    $iloop = 0;
    if($chkmonth==15){$cntDay=15;}
    



    $heading_width[0] = '8';
    $heading_width[1] = '34';
    
    $cntMaxDay = $cntDay+1;
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $heading_width[$idx] = '6.5';
    }
    $heading_width[$idx+1] = '7.5';
    $heading_width[$idx+2] = '7.5';
    $heading_width[$idx+3] = '7.5';
    $heading_width[$idx+4] = '7.5';
    $heading_width[$idx+5] = '7.5';
    $heading_width[$idx+6] = '10';
    
    
    $heading_text[0] = 'ลำดับ';
    $heading_text[1] = 'ชื่อ-ชื่อสกุล';
    $day=0;
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $day++;
        $heading_text[$idx] = '<**1**>วันที่ปฏิบัติงาน|'.(($NUMBER_DISPLAY==2)?convert2thaidigit($day):$day);
    }
    $heading_text[$idx+1] = 'ป่วย|(วัน)';
    $heading_text[$idx+2] = 'กิจ|(วัน)';
    $heading_text[$idx+3] = 'ขาด|(วัน)';
    $heading_text[$idx+4] = 'สาย|(วัน)';
	$heading_text[$idx+5] = 'พักผ่อน|(วัน)';
    $heading_text[$idx+6] = 'หมายเหตุ';
    
    
    
    
    $column_function[0] = "ENUM";
    $column_function[1] = "TSTR";
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $column_function[$idx] = "TSTR";
    }
    $column_function[$idx+1] = "TSTR";
    $column_function[$idx+2] = "TSTR";
    $column_function[$idx+3] = "TSTR";
    $column_function[$idx+4] = "TSTR";
    $column_function[$idx+5] = "TSTR";
    $column_function[$idx+6] = "TSTR";
    
    $heading_align[] = "C";
    $heading_align[] = "C";
    
    $data_align[]="C";
    $data_align[]="L";
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $heading_align[] = "C";
        $data_align[]="C";
    }
    $heading_align[] = "C";
    $heading_align[] = "C";
    $heading_align[] = "C";
    $heading_align[] = "C";
    $heading_align[] = "C";
    $heading_align[] = "C";

    
    $data_align[]="C";
    $data_align[]="C";
    $data_align[]="C";
    $data_align[]="C";
    $data_align[]="C";
    $data_align[]="L";
    //$heading_align = array("C","C","C","C","C");
	
    //$data_align = array("C","L","C","C","C");
    
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
