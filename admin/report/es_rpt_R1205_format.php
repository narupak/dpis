<?php
//หาช่วงวันที่ 15 / 30 
	$mk_data=mktime(0, 0, 0, substr("0".$search_month,-2), '01', ($search_yearBgn-543));
	$start = date("Y-m-d", $mk_data);

	$bgnbackMonth= ($search_yearBgn-543)."-".substr("0".$search_month,-2)."-01";
	$end=($search_yearBgn-543)."-".substr("0".$search_month,-2)."-".date("t",strtotime($bgnbackMonth));

    $cmdBtw="select distinct ot_date from ta_per_ot 
				where 
					ORG_ID=$CHKsearch_org_id
					AND NVL(DEPARTMENT_ID,-1)=$CHKDEPARTMENT_ID
					AND NVL(ORG_LOWER1,-1)=$CHKORG_LOWER1
					AND NVL(ORG_LOWER2,-1)=$CHKORG_LOWER2
					AND NVL(ORG_LOWER3,-1)=$CHKORG_LOWER3
					and SET_FLAG=1 
					AND CONTROL_ID in(select CONTROL_ID from TA_PER_OT_CONTROL 
								where BUDGET_YEAR=$search_yearBgn 
				     					AND PAY_MONTH=$search_month)
					 order by ot_date ";
    $cntDay = $db_dpis2->send_cmd($cmdBtw);
	while ($dataDAY = $db_dpis2->get_array_array()) {
        	$dayList[]= substr($dataDAY[OT_DATE],-2);
    }
    $workDateIN = "";
    $comma=",";
    $iloop = 0;
    



    $heading_width[0] = '8';
    $heading_width[1] = '30';
    
	
	$PTOTAL='170';
	$CntWightx = round($PTOTAL/$cntDay,2);
	//echo $cntDay; die();
	//$CntWightx = '$CntWightx';
    $cntMaxDay = $cntDay+1;
    for($idx=2;$idx<=$cntMaxDay;$idx++){
		
        $heading_width[$idx] = $CntWightx;
    }
    $heading_width[$idx+1] = '10';
    $heading_width[$idx+2] = '10';
    $heading_width[$idx+3] = '11';
    $heading_width[$idx+4] = '17';
	$heading_width[$idx+5] = '17';
    $heading_width[$idx+6] = '15';
    
    
    $heading_text[0] = 'ลำดับที่';
    $heading_text[1] = 'ชื่อ-ชื่อสกุล';
    $day=0;
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $day++;
        $heading_text[$idx] = '<**1**>วันที่ปฏิบัติงาน|'.(($NUMBER_DISPLAY==2)?convert2thaidigit(($dayList[$idx-2]+0)):($dayList[$idx-2]+0));
    }
    $heading_text[$idx+1] = '<**2**>รวมเวลาปฏิบัติงาน|วันปกติ (ชั่วโมง)';
    $heading_text[$idx+2] = '<**2**>รวมเวลาปฏิบัติงาน|วันหยุด (ชั่วโมง)';
    $heading_text[$idx+3] = 'จำนวนเงิน';
    $heading_text[$idx+4] = 'วันเดือนปีที่รับเงิน';
    $heading_text[$idx+5] = 'ลายมือชื่อผู้รับเงิน';
	$heading_text[$idx+6] = 'หมายเหตุ';
    
    
    
    
    $column_function[0] = "ENUM";
    $column_function[1] = "TSTR";
    for($idx=2;$idx<=$cntMaxDay;$idx++){
        $column_function[$idx] = "TSTR";
    }
    $column_function[$idx+1] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
    $column_function[$idx+2] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
    $column_function[$idx+3] = (($NUMBER_DISPLAY==2)?"TNUM-z":"ENUM-z");
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
