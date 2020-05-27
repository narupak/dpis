<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
    include ("../php_scripts/function_share.php");
    include ("../report/rpt_function.php");
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	ini_set("max_execution_time", 0);
    $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";
	$report_title = "R006020";
	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_title");
	$worksheet->set_margin_right(0.50);
        $worksheet->set_margin_bottom(1.10);
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	function compareDate($date1,$date2) {
		$arrDate1 = explode("-",$date1);
		$arrDate2 = explode("-",$date2);
		$timStmp1 = mktime(0,0,0,$arrDate1[1],$arrDate1[2],$arrDate1[0]);
		$timStmp2 = mktime(0,0,0,$arrDate2[1],$arrDate2[2],$arrDate2[0]);

		if ($timStmp1 == $timStmp2) {
			$h = 0;
		} else if ($timStmp1 < $timStmp2) {
			$h = 0;
		} else if ($timStmp1 > $timStmp2) {
			$h = 1;
		}
                return  $h;
    }
    if($search_org_name_1){
        $search_org =$search_org_name_1 ;
    }else if($search_org_name){
        $search_org =$search_org_name ;
    }else if($DEPARTMENT_NAME){
        $search_org = $DEPARTMENT_NAME;
    }else if($MINISTRY_NAME){
        $search_org = $DEPARTMENT_NAME;
    }
    
    

   
    
    

$datenow = date('Y-m-d');
    $heading_width[] = "10";
    $heading_width[] = "25";
    $heading_width[] = "10";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "20";
    $heading_width[] = "15";
    $heading_width[] = "25";
    $heading_width[] = "20";
    $heading_width[] = "15";
    $heading_width[] = "25";
    $heading_width[] = "25";
    $heading_width[] = "20";
    $heading_width[] = "20";
    if($search_per_type != 4){
    $heading_width[] = "15";    
    $heading_width[] = "30";
    }else{
        $heading_width[] = "30";
    } 
   // $heading_width[] = "30";

   // $ws_width = array(10,25,25,20,25,30,30,30,30,25,25,25,25,25,25,25,25,25,25);
        
    $heading_text[] = "ที่";
    $heading_text[] = "ชื่อ-สกุล";
    $heading_text[] = "เลขที่ตำแหน่ง";
    $heading_text[] = "ชื่อ - สกุล|";
    $heading_text[] = "ตำแหน่ง|";
    $heading_text[] = "<**1**>โครงสร้างตามกฏหมาย|สำนัก/กอง";
    $heading_text[] = "<**1**>โครงสร้างตามกฏหมาย|ต่ำกว่าสำนัก/กอง 1 ระดับ";
    $heading_text[] = "<**2**>โครงสร้างตามมอบหมาย|สำนัก/กอง";
    $heading_text[] = "<**2**>โครงสร้างตามมอบหมาย|ต่ำกว่าสำนัก/กอง 1 ระดับ";
    $heading_text[] = "<**3**>เงินเดือน|เลขที่คำสั่ง";
    $heading_text[] = "<**3**>เงินเดือน|วันทีมีผล";
    $heading_text[] = "<**3**>เงินเดือน|จำนวนเงิน";
    $heading_text[] = "<**4**>เงินตามตำแหน่ง|ประเภทเงินประจำตำแหน่ง";
    $heading_text[] = "<**4**>งินตามตำแหน่ง|วันทีมีผล";
    $heading_text[] = "<**4**>เงินตามตำแหน่ง|จำนวนเงิน";
    $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|ประเภทเงินเพิ่มพิเศษ";
    $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|เลขที่คำสั่ง";
    $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|ลงวันที่";
    $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|วันที่มีผล";
    if($search_per_type != 4){
        $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|จำนวนเงิน";
        $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|หมายเหตุ";
    }else{
        //$heading_text[] = "<**5**>เงินเพิ่มพิเศษ|จำนวนเงิน";
        $heading_text[] = "<**5**>เงินเพิ่มพิเศษ|หมายเหตุ";
    }
    
    //โครงสร้างตามกฎหมาย
    $sh_type_level ="";
    $ORG_ID ="";
    $ORG_ID_1 ="";
    if($search_org_id){
        $ORG_ID = "and pn.ORG_ID = ".$search_org_id;
    }
     if($search_org_id_1){
        $ORG_ID_1 = "and pn.ORG_ID_1 = ".$search_org_id_1; 
    }

    if($search_per_type == 1){
        $sh_type_level = "ตำแหน่ง";
        $pl_name ="PL_NAME";
        $per_position = "PER_POSITION";
        $per_line="PER_LINE";
        $pos_id = "POS_ID";
        $pl_code = "PL_CODE";
        $pos_no = "POS_NO";
        $join="";
        $order_by = "POS_ID";
       $search_type_name='ข้าราชการ';
    }else if($search_per_type == 2){
        $sh_type_level = "หมวด";
        $pl_name ="PN_NAME as PL_NAME";
        $per_position = "PER_POS_EMP";
        $per_line="PER_POS_NAME";
        $pos_id = "POEM_ID";
        $pl_code = "PN_CODE";
        $pos_no = "POEM_NO";
        $join="";
        $order_by = "POEM_ID";
        $search_type_name='ลูกจ้างประจำ';

    }else if($search_per_type == 3){
        $sh_type_level = "กลุ่มงาน";
        $pl_name ="EP_NAME as PL_NAME";
        $per_position = "PER_POS_EMPSER";
        $per_line="PER_EMPSER_POS_NAME";
        $pos_id = "POEMS_ID";
        $pl_code = "EP_CODE";
        $pos_no = "POEMS_NO";
        $join="";
        $order_by = "POEMS_ID";
        $search_type_name='พนักงาราชการ';
    }else if($search_per_type == 4){
        $sh_type_level = "ชื่อตำแหน่ง";
        $pl_name ="TP_NAME as PL_NAME";
        $per_position = "PER_POS_TEMP";
        $per_line="PER_TEMP_POS_NAME";
        $pos_id = "POT_ID";
        $pl_code = "TP_CODE";
        $pos_no = "POT_NO";
        $join="(+)";
        $order_by = "POT_ID";
        $search_type_name='ลูกจ้างชั่วคราว';
    }
       
        $arr_column_map = (array) null;
        $arr_column_sel = (array) null;
        for($i=0; $i < count($heading_text); $i++) {
                $arr_column_map[] = $i;		// link index ของ head 
                $arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
        }
        $arr_column_width = $heading_width;	// ความกว้าง
        $arr_column_align = $data_align;		// align
        $COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
        if($search_per_type == 1 || $search_per_type == 2){
        $ws_head_line1 = array("ที่","ชื่อ-สกุล","เลขที่ตำแหน่ง","$sh_type_level","ระดับตำแหน่ง", "<**1**>โครงสร้างตามกฏหมาย", "<**1**>โครงสร้างตามกฏหมาย","<**2**>โครงสร้างตามมอบหมาย","<**2**>โครงสร้างตามมอบหมาย","<**3**>เงินเดือน","<**3**>เงินเดือน","<**3**>เงินเดือน","<**4**>เงินตามตำแหน่ง","<**4**>งินตามตำแหน่ง","<**4**>เงินตามตำแหน่ง","<**5**>เงินเพิ่มพิเศษ", "<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>งินเพิ่มพิเศษ","หมายเหตุ");
        $ws_head_line2 = array("","","","","", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "เลขที่คำสั่ง", "วันทีมีผล", "จำนวนเงิน", " ประเภทเงินประจำตำแหน่ง", "วันทีมีผล", "จำนวนเงิน", "ประเภทเงินเพิ่มพิเศษ", "เลขที่คำสั่ง", "ลงวันที่","วันที่มีผล","จำนวนเงิน","");
        $ws_colmerge_line1 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_colmerge_line2 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
        $ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL","RBL");
        $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
        $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
        $ws_width = array(10,25,25,15,20,25,30,30,30,30,25,25,25,25,25,25,25,25,25,25,30);
        }else if($search_per_type == 3){
        $ws_head_line1 = array("ที่","ชื่อ-สกุล","เลขที่ตำแหน่ง","ชื่อตำแหน่ง", "$sh_type_level", "<**1**>โครงสร้างตามกฏหมาย", "<**1**>โครงสร้างตามกฏหมาย","<**2**>โครงสร้างตามมอบหมาย","<**2**>โครงสร้างตามมอบหมาย","<**3**>เงินเดือน","<**3**>เงินเดือน","<**3**>เงินเดือน","<**4**>เงินตามตำแหน่ง","<**4**>งินตามตำแหน่ง","<**4**>เงินตามตำแหน่ง","<**5**>เงินเพิ่มพิเศษ", "<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>งินเพิ่มพิเศษ","หมายเหตุ");
        $ws_head_line2 = array("","","","","", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "เลขที่คำสั่ง", "วันทีมีผล", "จำนวนเงิน", " ประเภทเงินประจำตำแหน่ง", "วันทีมีผล", "จำนวนเงิน", "ประเภทเงินเพิ่มพิเศษ", "เลขที่คำสั่ง", "ลงวันที่","วันที่มีผล","จำนวนเงิน","");
        $ws_colmerge_line1 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_colmerge_line2 = array(0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
        $ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL","RBL");
        $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
        $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
        $ws_width = array(10,25,15,25,20,25,30,30,30,25,25,25,25,25,25,25,25,25,25,25,30);
        }else{

        $ws_head_line1 = array("ที่","ชื่อ-สกุล","เลขที่ตำแหน่ง","$sh_type_level", "<**1**>โครงสร้างตามกฏหมาย", "<**1**>โครงสร้างตามกฏหมาย","<**2**>โครงสร้างตามมอบหมาย","<**2**>โครงสร้างตามมอบหมาย","<**3**>เงินเดือน","<**3**>เงินเดือน","<**3**>เงินเดือน","<**4**>เงินตามตำแหน่ง","<**4**>งินตามตำแหน่ง","<**4**>เงินตามตำแหน่ง","<**5**>เงินเพิ่มพิเศษ", "<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>เงินเพิ่มพิเศษ","<**5**>งินเพิ่มพิเศษ","หมายเหตุ");
        $ws_head_line2 = array("","","","", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "สำนัก/กอง", "ต่ำกว่าสำนัก/กอง 1 ระดับ", "เลขที่คำสั่ง", "วันทีมีผล", "จำนวนเงิน", " ประเภทเงินประจำตำแหน่ง", "วันทีมีผล", "จำนวนเงิน", "ประเภทเงินเพิ่มพิเศษ", "เลขที่คำสั่ง", "ลงวันที่","วันที่มีผล","จำนวนเงิน","");
        $ws_colmerge_line1 = array(0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_colmerge_line2 = array(0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0);
        $ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
        $ws_border_line2 = array("LBR","RBL","RBL","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL");
        $ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
        $ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
        $ws_width = array(10,25,15,25,20,25,30,30,30,25,25,25,25,25,25,25,25,25,25,30);
        }
        
        $worksheet->write(0, 8, "รายงานเงินเพิ่มพิเศษ ของ$search_type_name ", set_format("xlsFmtTitle", "B", "L", "", 1));
        $worksheet->write(1, 8, "$search_org", set_format("xlsFmtTitle", "B", "L", "", 1));
		//$worksheet->write(1, 1, "รายชื่อข้าราชการ ตำแหน่ง สังกัด", set_format("xlsFmtTitle", "B", "L", "", 1));
        
        $sum_hdw = 0;
        $sum_wsw = 0;
        $xlsRow=2;
        
        for($h = 0; $h < count($heading_width); $h++) {
            $sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
            if ($arr_column_sel[$h]==1) {
                $sum_hdw += $heading_width[$h];
            }
        }
        // บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
        // แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
        for($h = 0; $h < count($heading_width); $h++) {
            if ($arr_column_sel[$h]==1) {
                $ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
            }
        }
        // loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
        
        $colshow_cnt = $colseq = 0;
        $pgrp="";
        for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
                        $buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
                        $ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
                        $worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
                        $colseq++;
                }
        }
        // loop พิมพ์ head บรรทัดที่ 2
        $xlsRow++;
        $colseq=0;
        for($i=0; $i < count($ws_width); $i++) {
                if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
                        $worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]]));
                        $colseq++;
                }
        }
    $xlsRow++;
    
    // $cmd=" SELECT pl.$pos_id as tmp_pos_no, pl.ORG_ID,pl.PER_ID,(pp.PN_NAME||''||pl.PER_NAME||' '||pl.PER_SURNAME) as Fullname ,pe.$pl_name/**/, pll.LEVEL_NAME, po.ORG_NAME, po1.ORG_NAME as ORG_NAME1, po2.ORG_NAME as ORG_NAME2,
    //     ps.ORG_NAME as ORG_NAME_ASS, ps1.ORG_NAME as ORG_NAME_ASS_1, ps2.ORG_NAME as ORG_NAME_ASS_2
    //     FROM PER_PERSONAL pl, PER_PRENAME pp, $per_position pn/**/, $per_line pe/**/, PER_LEVEL pll, PER_ORG po, 
    //     PER_ORG po1, PER_ORG po2, PER_ORG_ASS ps, PER_ORG_ASS ps1, PER_ORG_ASS ps2 
    //     where pl.$pos_id = pn.$pos_id/**/ 
    //     and pe.$pl_code = pn.$pl_code/**/
    //     and pl.LEVEL_NO = pll.LEVEL_NO $join

    //     and pl.PER_STATUS=1 
    //     and pl.PER_TYPE = $search_per_type 
    //     and pl.PN_CODE = pp.PN_CODE
    //     and pn.ORG_ID = po.ORG_ID
    //     and pn.ORG_ID_1 = po1.ORG_ID(+)
    //     and pn.ORG_ID_2 = po2.ORG_ID(+)
    //     and pl.ORG_ID = ps.ORG_ID(+)
    //     and pl.ORG_ID_1 = ps1.ORG_ID(+)
    //     and pl.ORG_ID_2 = ps2.ORG_ID(+)
    //     and po.DEPARTMENT_ID = $DEPARTMENT_ID   
    //     $ORG_ID  $ORG_ID_1
    //     ORDER BY pl.$order_by";
        $cmd = "with per_sonal as (
            SELECT pl.ORG_ID,pl.PER_ID,pn.$pos_no as tmp_pos_no,(pp.PN_NAME||''||pl.PER_NAME||' '||pl.PER_SURNAME) as Fullname ,pe.$pl_name/**/, pll.LEVEL_NAME, po.ORG_NAME, po1.ORG_NAME as ORG_NAME1, po2.ORG_NAME as ORG_NAME2,
        ps.ORG_NAME as ORG_NAME_ASS, ps1.ORG_NAME as ORG_NAME_ASS_1, ps2.ORG_NAME as ORG_NAME_ASS_2
        FROM PER_PERSONAL pl, PER_PRENAME pp, $per_position pn/**/, $per_line pe/**/, PER_LEVEL pll, PER_ORG po, 
        PER_ORG po1, PER_ORG po2, PER_ORG_ASS ps, PER_ORG_ASS ps1, PER_ORG_ASS ps2 
        where pl.$pos_id = pn.$pos_id/**/ 
        and pe.$pl_code = pn.$pl_code/**/
        and pl.LEVEL_NO = pll.LEVEL_NO $join

        and pl.PER_STATUS=1 
        and pl.PER_TYPE = $search_per_type 
        and pl.PN_CODE = pp.PN_CODE
        and pn.ORG_ID = po.ORG_ID
        and pn.ORG_ID_1 = po1.ORG_ID(+)
        and pn.ORG_ID_2 = po2.ORG_ID(+)
        and pl.ORG_ID = ps.ORG_ID(+)
        and pl.ORG_ID_1 = ps1.ORG_ID(+)
        and pl.ORG_ID_2 = ps2.ORG_ID(+)
        and po.DEPARTMENT_ID = $DEPARTMENT_ID   
        $ORG_ID  $ORG_ID_1
        ORDER BY pl.$order_by
        ), TB_SALARYHIS as(
            SELECT * from (
                SELECT
                  ROW_NUMBER() OVER (PARTITION BY per_id ORDER BY per_id,sah_effectivedate DESC ) AS ROW_NUMSAL ,
                  per_id as per_idsal ,sah_docno, sah_effectivedate, sah_salary
                FROM PER_SALARYHIS
            ) WHERE ROW_NUMSAL=1
        
        ),
        TB_MGHIS as(
            SELECT * from (
                SELECT
                  ROW_NUMBER() OVER (PARTITION BY per_id ORDER BY per_id,PMH_EFFECTIVEDATE DESC ) AS ROW_NUMMGT ,
                  per_id as per_idmgt ,a.PMH_EFFECTIVEDATE, a.PMH_AMT, b.EX_NAME, PMH_ENDDATE
               FROM PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b where a.EX_CODE = b.EX_CODE 
            ) WHERE ROW_NUMMGT=1
        
        ),
          TB_EXHIS as(
              SELECT a.PER_ID as PER_IDEXH, b.EX_NAME as EX_NAME2, a.EXH_DOCNO as EXH_DOCNO2, a.EXH_DOCDATE as EXH_DOCDATE2,  a.EXH_EFFECTIVEDATE as EXH_EFFECTIVEDATE2, a.EXH_AMT as EXH_AMT2, a.EXH_REMARK as EXH_REMARK2
                , a.EXH_ENDDATE as EXH_ENDDATE2 ,a.EXH_ACTIVE as  EXH_ACTIVE2 
              FROM PER_EXTRAHIS a, PER_EXTRATYPE b
              where a.EX_CODE = b.EX_CODE 
        )
          SELECT p.*,sa.*, m.*, e.* from per_sonal p 
          LEFT JOIN TB_SALARYHIS sa on (p.per_id=sa.per_idsal)
          LEFT JOIN TB_MGHIS m on (p.per_id=m.per_idmgt)
          LEFT JOIN TB_EXHIS e on (p.per_id=e.PER_IDEXH)";
  //echo "<pre>".$cmd;
  //die();
    $count_page_data = $db_dpis->send_cmd($cmd);
    $num_order = 0;
    $per_idx="";
	while ($data = $db_dpis->get_array()) {
        $PER_ID = $data[PER_ID];
        $fullname = $data[FULLNAME];
        $PL_NAME = $data[PL_NAME];
        $LEVEL_NAME = $data[LEVEL_NAME];
        $ORG_NAME = $data[ORG_NAME];
        $ORG_NAME1 = $data[ORG_NAME1];
        $ORG_NAME_ASS = $data[ORG_NAME_ASS];
        $ORG_NAME_ASS_1 = $data[ORG_NAME_ASS_1];
        $ORG_NAME_ASS_2 = $data[ORG_NAME_ASS_2];
        $TEMP_POST_NO = $data[TMP_POS_NO];
        //ประวัติเงินล่าสุด
        
        // $cmd = "select * from 
        //         (SELECT sah_docno, sah_effectivedate, sah_salary FROM PER_SALARYHIS  
        //             where PER_ID = $PER_ID   ORDER by sah_effectivedate desc )where  rownum = 1";
        // $db_dpis1->send_cmd($cmd);
        // $data = $db_dpis1->get_array();
        $SAH_DOCNO = $data[SAH_DOCNO];
        $SAH_EFFECTIVEDATE = show_date_format($data[SAH_EFFECTIVEDATE], 3);  
        $SAH_SALARY = ($data[SAH_SALARY])?number_format($data[SAH_SALARY]):"";
   
        //echo "<pre>".$cmd;
        //ประวัติเงินประจำตำแหน่ง
        // $cmd=" select * from (SELECT a.PER_ID, a.PMH_EFFECTIVEDATE, a.PMH_AMT, b.EX_NAME, PMH_ENDDATE FROM PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b
        //     where a.EX_CODE = b.EX_CODE and per_id =  $PER_ID  ORDER by a.PMH_EFFECTIVEDATE desc) where rownum = 1";
        // $db_dpis1->send_cmd($cmd);
        //$data = $db_dpis1->get_array();
        if($data[PMH_ENDDATE] ){ //เช็คว่าเงินประจำตำแหน่งมีวันที่สิ้นสุดหรือไม่
               $chk_END_MGSALARY = compareDate($datenow, $data[PMH_ENDDATE]);
              // if($data[PMH_ACTIVE]==1){ $chk_END_MGSALARY=1;}//ถ้าไม่ได้ Active จะให้เข้า else
              if($chk_END_MGSALARY !=1){//เช็คว่าเลยวันที่สินสุดหรือยัง 0 ยังไม่เลยวันที่สิ้นสุด 1 คือเลยมาเเล้ว
                $PMH_EFFECTIVEDATE = show_date_format($data[PMH_EFFECTIVEDATE], 3);
                $PMH_AMT = ($data[PMH_AMT])?number_format($data[PMH_AMT]):"";
                $PMH_NAME = $data[EX_NAME];
             }else{
                 $PMH_EFFECTIVEDATE="";
                 $PMH_AMT="";
                 $PMH_NAME="";
             }      
        }else{ //ถ้าไม่มีวันที่สิ้นสุด ให้เอาเงินมาเเสดงเลย
                $PMH_EFFECTIVEDATE = show_date_format($data[PMH_EFFECTIVEDATE], 3);
                $PMH_AMT = ($data[PMH_AMT])?number_format($data[PMH_AMT]):"";
                $PMH_NAME = $data[EX_NAME];
        }

        
        //ประวัติเงินเพิ่มพิเศษ
        // $cmd=" SELECT a.PER_ID, b.EX_NAME, a.EXH_DOCNO, a.EXH_DOCDATE,  a.EXH_EFFECTIVEDATE, a.EXH_AMT, a.EXH_REMARK, EXH_ENDDATE  FROM PER_EXTRAHIS a, PER_EXTRATYPE b
        //     where a.EX_CODE = b.EX_CODE and per_id =$PER_ID   ORDER by a.EXH_EFFECTIVEDATE desc";
        // $db_dpis1->send_cmd($cmd);
        // $data = $db_dpis1->get_array();
        
        if($data[EXH_ENDDATE2]){ //เช็คว่าเงินประจำตำแหน่งมีวันที่สิ้นสุดหรือไม่
               $chk_END_EXTRASALARY = compareDate($datenow, $data[EXH_ENDDATE2]);
               //if($data[EXH_ACTIVE2]==1){ $chk_END_EXTRASALARY=1;}//ถ้าไม่ได้ Active จะให้เข้า else
            if($chk_END_EXTRASALARY !=1 ){//เช็คว่าเลยวันที่สินสุดหรือยัง 0 ยังไม่เลยวันที่สิ้นสุด 1 คือเลยมาเเล้ว
                $EXH_NAME = $data[EX_NAME2];
                $EXH_DOCNO = $data[EXH_DOCNO2];
                $EXH_DOCDATE = show_date_format($data[EXH_DOCDATE2], 3);
                $EXH_EFFECTIVEDATE = show_date_format($data[EXH_EFFECTIVEDATE2], 3);
                $EXH_AMT = ($data[EXH_AMT2])?number_format($data[EXH_AMT2]):"";  
                $EXH_REMARK = $data[EXH_REMARK2];
                $EXH_ENDDATE = $data[EXH_ENDDATE2];
                $write_scrip = "Y";
            
             }else{
                 $EXH_NAME="";
                 $EXH_DOCNO="";
                 $EXH_DOCDATE="";
                 $EXH_EFFECTIVEDATE="";
                 $EXH_AMT="";
                 $EXH_REMARK="";
                 $write_scrip = "N";
             }      
        }else{ //ถ้าไม่มีวันที่สิ้นสุด ให้เอาเงินมาเเสดงเลย
                $EXH_NAME = $data[EX_NAME2];
                $EXH_DOCNO = $data[EXH_DOCNO2];
                $EXH_DOCDATE = show_date_format($data[EXH_DOCDATE2], 3);
                $EXH_EFFECTIVEDATE = show_date_format($data[EXH_EFFECTIVEDATE2], 3);
                $EXH_AMT = ($data[EXH_AMT2])?number_format($data[EXH_AMT2]):"";  
                $EXH_REMARK = $data[EXH_REMARK2];
                $EXH_ENDDATE = $data[EXH_ENDDATE2];
                $write_scrip = "Y";
        }
        

        if($count_page_data){
            
        if($search_per_type == 4){
            if($write_scrip=="Y"){
                if($per_idx!=$PER_ID){
                    $per_idx=$PER_ID;
                    $num_order ++;
            $worksheet->write($xlsRow, 0,  $num_order, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            $worksheet->write($xlsRow, 1,  $fullname, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 2,  $TEMP_POST_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            $worksheet->write($xlsRow, 3,  $PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 4,  $ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 5,  $ORG_NAME1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 6,  $ORG_NAME_ASS, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 7,  $ORG_NAME_ASS_1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                //ประวัติเงินล่าสุ
            $worksheet->write($xlsRow, 8,  $SAH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 9,  $SAH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 10, $SAH_SALARY, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                //ประวัติเงินประจำตำแหน่ง
            $worksheet->write($xlsRow, 11, $PMH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 12, $PMH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 13, $PMH_AMT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                //ประวัติเงินเพิ่มพิเศษ
            $worksheet->write($xlsRow, 14,$EXH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 15,$EXH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 16,$EXH_DOCDATE , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 17,$EXH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 18,$EXH_AMT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            $worksheet->write($xlsRow, 19,$EXH_REMARK, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            }else{
                $worksheet->write($xlsRow, 0,  "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 1,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 2,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 3,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 4,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 5,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 6,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                    //ประวัติเงินล่าสุ
                $worksheet->write($xlsRow, 7,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 8,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                    //ประวัติเงินประจำตำแหน่ง
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                 
            }//เช็คคนซ้ำ 
               //ประวัติเงินเพิ่มพิเศษ
               $worksheet->write($xlsRow, 14,$EXH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
               $worksheet->write($xlsRow, 15,$EXH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
               $worksheet->write($xlsRow, 16,$EXH_DOCDATE , set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
               $worksheet->write($xlsRow, 17,$EXH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
               $worksheet->write($xlsRow, 18,$EXH_AMT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
               $worksheet->write($xlsRow, 19,$EXH_REMARK, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            }  
        }else{
            if($write_scrip=="Y"){
            if($per_idx!=$PER_ID){
                $per_idx=$PER_ID;
                $num_order ++;
                $worksheet->write($xlsRow, 0,  $num_order, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 1,  $fullname, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 2,  $TEMP_POST_NO, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 3,  $PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 4,  $LEVEL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 5,  $ORG_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 6,  $ORG_NAME1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 7,  $ORG_NAME_ASS, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 8,  $ORG_NAME_ASS_1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                    //ประวัติเงินล่าสุด
                $worksheet->write($xlsRow, 9,  $SAH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 10,  $SAH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 11, $SAH_SALARY, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                    //ประวัติเงินประจำตำแหน่ง
                $worksheet->write($xlsRow, 12, $PMH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 13, $PMH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 14, $PMH_AMT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            }else{
                $worksheet->write($xlsRow, 0,  "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 1,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 2,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 3,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 4,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 5,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 6,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 7,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                    //ประวัติเงินล่าสุด
                $worksheet->write($xlsRow, 8,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 9,  "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                    //ประวัติเงินประจำตำแหน่ง
                $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                $worksheet->write($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
                $worksheet->write($xlsRow, 14, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));

            }//เช็คคนซ้ำ  
            
                //ประวัติเงินเพิ่มพิเศษ
           // if($write_scrip == "Y"){ 
            $worksheet->write($xlsRow, 15,$EXH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 16,$EXH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 17,$EXH_DOCDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 18,$EXH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
            $worksheet->write($xlsRow, 19,$EXH_AMT, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
            $worksheet->write($xlsRow, 20,$EXH_REMARK, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
        }
         }  
if($write_scrip == "Y"){
    $xlsRow++;
}         
  
    $search_org_name_1="";
    $search_org_name="";
    $DEPARTMENT_NAME="";
    $MINISTRY_NAME="";
  }//end chk data  
}//end while
//die();
    $workbook->close();
	ini_set("max_execution_time", 30);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
