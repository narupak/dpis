<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("es_font_size.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("es_rpt_R1203_02_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	/*เงื่อนไขการออกรายงาน*/
	/*ประเภทบุคลากร*/
	$con_per_type = "";
	$con_per_type = " and a.PER_TYPE = $search_per_type ";
        
        /* หาสถานะ */
        $con_per_status = "";
        if($search_per_type){
            $con_per_status = "a.PER_STATUS in (". implode(", ", $search_per_status) .")";
        }
	
	/*ตามโครงสร้าง*/
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.PL_CODE";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);		
		$line_title = " สายงาน";
		
		$Frm_line = " PER_LINE ";
		$ON_code = "LIN.PL_CODE";
		$line_seq = "LIN.PL_SEQ_NO";
		$line_name = "LIN.PL_NAME";
		
		

	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.PN_CODE";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_POS_NAME ";
		$ON_code = "LIN.PN_CODE";
		$line_seq = "LIN.PN_SEQ_NO";
		$line_name = "LIN.PN_NAME";

	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.EP_CODE";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_EMPSER_POS_NAME ";
		$ON_code = "LIN.EP_CODE";
		$line_seq = "LIN.EP_SEQ_NO";
		$line_name = "LIN.EP_NAME";

	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$position_join_god = "orggod.ORG_ID=b.ORG_ID";
		$line_code = "b.TP_CODE";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);		
		$line_title = " ชื่อตำแหน่ง";
		
		$Frm_line = " PER_TEMP_POS_NAME ";
		$ON_code = "LIN.TP_CODE";
		$line_seq = "LIN.TP_SEQ_NO";
		$line_name = "LIN.TP_NAME";
	} // end if
	
	if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
		/*ตามโครงสร้างกฏหมาย*/
		$conTPER_ORG = " LEFT JOIN PER_ORG  c ON(c.ORG_ID=b.ORG_ID)
								     LEFT JOIN PER_ORG  e ON(e.ORG_ID=a.DEPARTMENT_ID) ";
	}else{
		/*ตามโครงสร้างมอบหมายงาน*/
		$conTPER_ORG = " LEFT JOIN PER_ORG_ASS  c ON(c.ORG_ID=a.ORG_ID)
								     LEFT JOIN PER_ORG_ASS  e ON(e.ORG_ID=a.DEPARTMENT_ID) ";
	}
	
	/*สถานที่*/
	/*$con_wl_code = "";
	if(trim($search_wl_code)){
		$con_wl_code = " and PWT.WL_CODE = '$search_wl_code' ";
	}*/
	
	
	/*รอบการปฏิบัติงาน*/
	/*$con_wc_code = "";
	if(trim($search_wc_code)){
		$con_wc_code = " and PWT.WC_CODE ='$search_wc_code' ";
	}*/
	
	
	/*วันที่*/
	function MonthDays($someMonth, $someYear)
	{
		return date("t", strtotime($someYear . "-" . $someMonth . "-01"));
	}
	
	if(trim($search_yearBgn)){
		
		$search_dateBgn = ($search_yearBgn - 543) ."-". substr("0".$search_month,-2) ."-01";
		$search_dateEnd = ($search_yearBgn- 543) ."-". substr("0".$search_month,-2) ."-". MonthDays(substr("0".$search_month,-2),($search_yearBgn-543));
		$show_date = ("1") ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn ." ถึงวันที่ ".MonthDays(substr("0".$search_month,-2),($search_yearBgn-543)) ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn;
	}
	
	$show_date= (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date);
	
	/*---------------------------------------*/
	
	
	$search_condition = "";
	//$list_type_text = $ALL_REPORT_TITLE;
	$list_type_text = "";
	/*รูปแบบการออกรายงาน*/
	
	/*ทั้งส่วนราชการ*/
	$conditionDEPARTMENT ="";
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$conditionDEPARTMENT = " AND (a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			//$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1 || $PER_AUDIT_FLAG==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd_fast($cmd);
			while($data = $db_dpis->get_array_array()) $arr_org_ref[] = $data[ORG_ID];

			$conditionDEPARTMENT = " AND (a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			//$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$conditionDEPARTMENT = " AND (trim(c.PV_CODE) = '$PROVINCE_CODE')";
			//$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	
	
	/*สำนัก/กอง*/
	$list_type_texts="";
        if($select_org_structure==0 || $PER_AUDIT_FLAG==2) {
            if(trim($search_org_id)){ 
                    $search_condition .= " AND  (b.ORG_ID = $search_org_id) ";
                    //$list_type_text .= " - $search_org_name"; ไม่ต้องแสดงมันจะซ้ำกัน
            } // end if
            if(trim($search_org_id_1)){ 
                    $search_condition .= " AND  (b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_texts = " - $search_org_name_1";
            } // end if
        }else{
            if(trim($search_org_ass_id)){ 
                     $search_condition .= " AND  (a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= " - $search_org_ass_name";
            } // end if
            if(trim($search_org_ass_id_1)){ 
                    $search_condition .= " AND  (a.ORG_ID_1 =  $search_org_ass_id_1)";
                    $list_type_texts = " - $search_org_ass_name_1";
            } // end if
        }
	
	/*ตำแหน่งในสายงาน*/
	if(in_array("PER_LINE", $list_type)){
		if($line_search_code){
			$search_condition .= " AND (trim($line_code)='$line_search_code')";
			$list_type_text .= " - $line_search_name";
		}
	}
	
	/*ส่วนกลาง/ส่วนกลางในภูมิภาค/ส่วนภูมิภาค/ต่างประเทศ*/
	$conOT_CODE="'00'";
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		$list_type_text = " - ส่วนกลาง";
		$conOT_CODE .= ",'01' ";
	}
	
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		$list_type_text .= " - ส่วนกลางในภูมิภาค";
		$conOT_CODE .= ",'02' ";
	}
	
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		$list_type_text .= " - ส่วนภูมิภาค";
		$conOT_CODE .= ",'03' ";
	}
	
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		$list_type_text .= " - ต่างประเทศ";
		$conOT_CODE .= ",'04' ";
	}
	
	if($conOT_CODE != "'00'"){
		$search_condition .= " AND  (trim(c.OT_CODE) in($conOT_CODE)) ";
	}
	
	/*ประเทศ*/
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$search_condition .= " AND (trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= " - $search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$search_condition .= " AND(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	
	
	$POS_NO_VAL1="";
	$PER_NAME_VAL = "";
	$PER_SURNAME_VAL = "";
	$LEVEL_NO_VAL ="";
     if($list_person_type=="SELECT"){
		 if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $PERID_VAL = " AND a.PER_ID IN($SELECTED_PER_ID) ";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
				if($search_per_type==1){ 
					$POS_NO_VAL1 = " AND  (b.POS_NO = '$search_pos_no')";
				
				}elseif($search_per_type==2){ 
					$POS_NO_VAL1 = " AND (b.POEM_NO = '$search_pos_no')";
					
				}elseif($search_per_type==3){ 
					$POS_NO_VAL1 = " AND (b.POEMS_NO = '$search_pos_no')";
					
				}elseif($search_per_type==4){ 
					$POS_NO_VAL1 = " AND (b.POT_NO = '$search_pos_no')";	
					
				}

		} // end if
		
		if(trim($search_name)){ 
				$PER_NAME_VAL = " AND (a.PER_NAME like '$search_name%')";	
		} // end if
		
		if(trim($search_surname)){ 
				$PER_SURNAME_VAL = " AND (a.PER_SURNAME like '$search_surname%')";	
		} // end if
		
		
		if($search_per_type==1){
                    if(trim($search_min_level1) & trim($search_max_level1)){ 
                            $search_min_level=$search_min_level1;
                            $search_max_level=$search_max_level1;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==2){        
                    if(trim($search_min_level2) & trim($search_max_level2)){ 
                            $search_min_level=$search_min_level2;
                            $search_max_level=$search_max_level2;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }
                }else if($search_per_type==3){
                    if(trim($search_min_level3) & trim($search_max_level3)){ 
                            $search_min_level=$search_min_level3;
                            $search_max_level=$search_max_level3;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	


                    }
                }else if($search_per_type==4){
                    if(trim($search_min_level4) & trim($search_max_level4)){ 
                            $search_min_level=$search_min_level4;
                            $search_max_level=$search_max_level4;

                            $LEVEL_NO_VAL = " AND a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";
                    }
		}
		
		
        $PERID_VAL = " ";
	}elseif($list_person_type == "ALL"){
		$PERID_VAL = "  ";
    }
/*---------------------------------------*/
/**/
if(!trim($RPTORD_LIST)){ 
	$RPTORD_LIST = "MINISTRY|";
}
$arr_rpt_order = explode("|", $RPTORD_LIST);
$order_by = "";
$Frm_order_by = "";
$Select_in_order_by = "";
$Select_out_order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){

			/*case "DEPARTMENT" : 
					if($order_by) $order_by .= ", ";
					$order_by .= "xx.DEPARTMENT";
					break;
			case "ORG" :
				$order_by .= ",xx.KONG";
				break; เริ่มอันนี้*/
			case "ORG_1" :
				$Frm_order_by .=" LEFT JOIN PER_ORG  org1 ON(org1.ORG_ID=b.ORG_ID_1) ";
				$order_by .=" ,org1.ORG_SEQ_NO,org1.ORG_ID  ";
				break;
			case "ORG_2" :
				$Frm_order_by .=" LEFT JOIN PER_ORG  org2 ON(org2.ORG_ID=b.ORG_ID_2) ";
				$order_by .=" ,org2.ORG_SEQ_NO,org2.ORG_ID  ";
				break;
			case "PROVINCE" :
				
				$Frm_order_by .=" LEFT JOIN PER_PROVINCE  PVN ON(PVN.PV_CODE=a.PV_CODE) ";
				$order_by .= ",PVN.PV_SEQ_NO,PVN.PV_CODE";
				break;
			case "LINE" :
				$Frm_order_by .=" LEFT JOIN $Frm_line  LIN ON($ON_code=$line_code) ";
				$order_by .= ",$line_seq,$ON_code"; 
				break;
			case "LEVEL" :
				
				$Frm_order_by .=" LEFT JOIN PER_LEVEL  LEV ON(LEV.LEVEL_NO=a.LEVEL_NO) ";
				$order_by .= " ,LEV.LEVEL_SEQ_NO,LEV.LEVEL_NO";
				
				break;
			case "POSNO" :
				
				$order_by .=" ,$position_no_name , to_number(replace($position_no,'-',''))  ";
				break;
			case "NAME" :
				$order_by .= ",a.PER_NAME, a.PER_SURNAME";
				break;
			case "GENDER" :
				$order_by .= ",a.PER_GENDER";
				break;
			case "STRATDATE" :
				$order_by .= ",a.START_DATE";
				break;
			
		} // end switch case
	} // end for
	

/*------------------------------------------*/
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text"."$list_type_texts";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1206";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	//$worksheet = &$workbook->addworksheet($report_code);
	//$worksheet->set_margin_right(0.50);
	//$worksheet->set_margin_bottom(1.10);
	$PRINT_FONT= 99;
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
    	$ws_head_line1 = (array) null;
    	$ws_head_line2 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[] = $buff[0];
			$ws_head_line2[] = $buff[1];
		}
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL","TRBL","TRBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(30,25,7,7,7,7,7,7,10,20);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2,  $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
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
	} // function	
	
	if($DPISDB=="oci8"){

		$CON_PER_AUDIT_FLAG="";
		if ( $PER_AUDIT_FLAG==1 ){
			$tCon="(";
			for ($i=0; $i < count($SESS_AuditArray); $i++) {
				if ($i>0)
					$tCon .= " or ";
				$tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
				if ($SESS_AuditArray[$i][1] != 0)
					$tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
				$tCon .= ")";
			}
			$tCon .= ")";
			 $CON_PER_AUDIT_FLAG .= $tCon;
			
			if($search_org_ass_id){
                            $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID=$search_org_ass_id)";
                            if($search_org_ass_id_1){
                                $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID_1=$search_org_ass_id_1)";
                            }
			}
		}
		
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}elseif($PER_AUDIT_FLAG==2){
			$CON_PER_AUDIT_FLAG .= " AND a.PER_ID=$SESS_PER_ID ";
		}

		$cmd = " with AllDay As
						(
						  SELECT (TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))-1+rownum AS WORK_DATE FROM all_objects
							WHERE (TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))-1+ rownum <= last_day(TO_DATE(substr('$search_dateBgn 00:00:00',1,10), 'YYYY-MM-DD'))
						)
						,Work as
						(
						  SELECT PER_ID,WORK_DATE,APV_ENTTIME,APV_EXITTIME,WC_CODE,WORK_FLAG,ABSENT_FLAG,HOLIDAY_FLAG,SCAN_ENTTIME,REMARK AS WORK_REMARK
						  FROM PER_WORK_TIME 
						  WHERE 
						  WORK_DATE BETWEEN  to_date('$search_dateBgn 00:00:00','yyyy-mm-dd hh24:mi:ss') 
								        and to_date('$search_dateEnd 23:59:59','yyyy-mm-dd hh24:mi:ss')
						  
						)
						,Per_AllDay as
						(
						  SELECT distinct w.PER_ID,a.WORK_DATE
						  from (select distinct per_id from Work) w
						  left join AllDay a on (1=1)
						)
						,PER_WORK_TIME_ALL as
						(
						  select * from Work
						  
						  union
						  SELECT w.PER_ID,w.WORK_DATE,null,null,null,null,null,3,null,null
						  from Per_AllDay w
						  where not exists (select null from Work a where a.per_id=w.PER_ID and a.WORK_DATE=w.WORK_DATE)
						)
		
		
						SELECT
					  e.ORG_NAME AS DEPARTMENT, c.ORG_NAME AS KONG,
					  orggod.ORG_NAME AS KONG_GOD,
					  $ON_code AS PL_NAME,
					  PN.PN_NAME,a.PER_NAME,a.PER_SURNAME,a.PER_CARDNO,
					  TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
					wt.WC_CODE,
					TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
					TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,
					
					(CASE WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=10   THEN 0.5
                			  WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=2   THEN 0.5
							  WHEN wt.WORK_FLAG=2 AND wt.ABSENT_FLAG=0   THEN 1
          				      ELSE 0 END) KHAD,
					
					  (CASE WHEN wt.ABSENT_FLAG=30 OR wt.ABSENT_FLAG=12  THEN 1 ELSE 
					  CASE WHEN wt.ABSENT_FLAG=0 THEN 0 ELSE
						  CASE WHEN 
						  (select sum(1) from PER_ABSENTHIS h,PER_ABSENTTYPE t
							where h.PER_ID=wt.PER_ID and h.AB_CODE=t.AB_CODE and t.AB_COUNT=1 and h.ABS_ENDPERIOD=3
								  AND cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) between h.ABS_STARTDATE and h.ABS_ENDDATE
						  )>0 THEN 1
						  ELSE  0.5 END END END)  as LA,
						CASE WHEN wt.ABSENT_FLAG=10 OR wt.ABSENT_FLAG=12 OR wt.WORK_REMARK is not null  THEN 0 ELSE
					  	trunc((wt.apv_enttime - 
        							case when cl.WorkCycle_Type=1 then
        							  TRUNC(wt.WORK_DATE)+cl.NextDay_OnTime+((to_number(substr(cl.WC_Start,1,2))*60)+to_number(substr(cl.WC_Start,3,2)))/1440
        							 else   
        							  TRUNC(wt.WORK_DATE)+((to_number(substr(cl.On_Time,1,2))*60)+to_number(substr(cl.On_Time,3,2)))/1440
        							end
        								 )*1440) END  AS SAY,
					  (select NUM_HRS from TA_PER_OT 
						  where PER_ID=wt.PER_ID AND OT_DATE=TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') 
								AND HOLYDAY_FLAG=0 and APPROVE_FLAG=1 ) AS OTWORKDAY,
					  (select NUM_HRS from TA_PER_OT 
						  where PER_ID=wt.PER_ID AND OT_DATE=TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') 
								AND HOLYDAY_FLAG=1 and APPROVE_FLAG=1 ) AS OTHOLIDAY,
					  case when (wt.HOLIDAY_FLAG=3 and hl.HOL_NAME is null) or wt.HOLIDAY_FLAG=1 then 'วันหยุดประจำสัปดาห์' else hl.HOL_NAME end REMARK, 
					  a.PER_ID
					
					from PER_PERSONAL a
					 LEFT JOIN PER_WORK_TIME_ALL wt  ON(a.PER_ID=wt.PER_ID)
					 LEFT JOIN PER_WORK_CYCLE cl on(cl.WC_CODE=wt.WC_CODE)
					 LEFT JOIN PER_PRENAME PN ON(PN.PN_CODE=a.PN_CODE)
					 LEFT JOIN PER_HOLIDAY hl on (SUBSTR(hl.HOL_DATE,1,10)=to_char(wt.WORK_DATE,'YYYY-MM-DD'))
					 LEFT JOIN $position_table b ON($position_join)
					 LEFT JOIN PER_ORG  orggod ON($position_join_god)
					 LEFT JOIN $Frm_line LIN ON($ON_code=$line_code)
					 left join PER_TIME_ATTENDANCE pt on (pt.PER_ID=wt.PER_ID and (pt.TIME_STAMP=wt.SCAN_ENTTIME or pt.TIME_ADJUST=wt.SCAN_ENTTIME))
          			 left join PER_TIME_ATT pta on (pta.TA_CODE=pt.TA_CODE)
          			 left join PER_WORK_LATE pwl on (pwl.WL_CODE=pta.WL_CODE and pwl.WC_CODE=wt.WC_CODE and pwl.WORK_DATE=to_char(wt.WORK_DATE,'YYYY-MM-DD'))
					 $conTPER_ORG
					 $Frm_order_by
					 WHERE $con_per_status
					 			AND wt.PER_ID IS NOT NULL 
								$CON_PER_AUDIT_FLAG
								$conditionDEPARTMENT 
								$search_condition
								$PERID_VAL
								$POS_NO_VAL1
								$PER_NAME_VAL
								$PER_SURNAME_VAL
								$LEVEL_NO_VAL
					
					ORDER BY e.ORG_SEQ_NO,e.ORG_NAME,
							 c.ORG_SEQ_NO,c.ORG_NAME,
							 orggod.ORG_NAME,
							 
							 $line_name
							 ,a.PER_NAME||a.PER_SURNAME,wt.work_date
		 ";
	}
	//echo "<pre>".$cmd; die();
	$db_dpis->send_cmd_fast($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$chkKONG = "";
	$CurName="";
	$data_rowP = 0;
	while($data = $db_dpis->get_array_array()){		
		if($chkKONG != $data[KONG]){
			$chkKONG = $data[KONG];
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][KONG] = $data[KONG];

			$data_row = 0;
			$data_count++;

		} // end if
		
		$data_row++;
		$arr_content[$data_count][type] = "CONTENT";
		
		$arr_content[$data_count][ORDER] = $data_row; 
		$arr_content[$data_count][name] = $data[PER_CARDNO]." ".$data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME]." ". $data[PL_NAME]." ". $data[KONG_GOD];
		$arr_content[$data_count][WORK_DATE] = substr($data[WORK_DATE],8,2)."/".substr($data[WORK_DATE],5,2)."/".(substr($data[WORK_DATE],0,4)+543);
		$arr_content[$data_count][WC_CODE] = (($NUMBER_DISPLAY==2)?convert2thaidigit($data[WC_CODE]):$data[WC_CODE]);		
		$arr_content[$data_count][APV_ENTTIME] = $data[APV_ENTTIME]=="" && $data[APV_EXITTIME]==""? "" :$data[APV_ENTTIME]."-".$data[APV_EXITTIME];	
		if($data[REMARK]){$KHAD="";}else{$KHAD=$data[KHAD];}
		if($data[REMARK] && $data[LA] !='1'){$LA="";}else{$LA=$data[LA];}
		$arr_content[$data_count][KHAD] = $KHAD<=0 ? '':round($KHAD,2);	
		$arr_content[$data_count][LA] = $LA<=0 ? '':round($LA,2);	
		$arr_content[$data_count][SAY] = $data[SAY]<=0 || $data[REMARK]!="" ? '':round($data[SAY],2);	
		$arr_content[$data_count][OTWORKDAY] = $data[OTWORKDAY]<=0 ? '':round($data[OTWORKDAY],2); 
		$arr_content[$data_count][OTHOLIDAY] = $data[OTHOLIDAY]<=0 ? '':round($data[OTHOLIDAY],2);
		$arr_content[$data_count][REMARK] = $data[REMARK];
		$data_count++;
	} // end while
	
	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	$count_data = $db_dpis->send_cmd($cmd);
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

//		print_header();
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","C","C","C","C","C","C","C","C","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		$Rowaddworksheet = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$KONG = $arr_content[$data_count][KONG];
			
			if($REPORT_ORDER == "ORG"){
				$Rowaddworksheet ++;
				$worksheet = &$workbook->addworksheet($Rowaddworksheet);
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
				$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
				$xlsRow = 0;
				$arr_title = explode("||", $report_title);
				for($i=0; $i<count($arr_title); $i++){
					
					$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
					$xlsRow++;
				} //for($i=0; $i<count($arr_title); $i++){
					
				$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$KONG"."$list_type_texts";
				
				if($company_name){
					$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "", "L", "", 0));
					for($j=0; $j < $colshow_cnt-1; $j++) 
						$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtSubTitle", "", "L", "", 0));
					$xlsRow++;
				} //if($company_name){

				print_header();
		

			}elseif($REPORT_ORDER == "CONTENT"){

				$arr_data = (array) null;
				if($CurName != $arr_content[$data_count][name]){
					$data_rowP++;
					$arr_data[] = $data_rowP."/".$arr_content[$data_count][name];
				}else{
					$arr_data[] = "";
				}

				
				$arr_data[] = $arr_content[$data_count][WORK_DATE];
				$arr_data[] = $arr_content[$data_count][WC_CODE];
				$arr_data[] = $arr_content[$data_count][APV_ENTTIME];
				$arr_data[] = $arr_content[$data_count][KHAD];
				$arr_data[] = $arr_content[$data_count][LA];
				$arr_data[] = $arr_content[$data_count][SAY];
				$arr_data[] = $arr_content[$data_count][OTWORKDAY];
				$arr_data[] = $arr_content[$data_count][OTHOLIDAY];
				$arr_data[] = $arr_content[$data_count][REMARK];
				
				
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
				$CurName=$arr_content[$data_count][name];
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
						
					}
					
					
					
				}
				
				
				
			} // end if
			
			

		} // end for
			
						
	}else{
		$xlsRow = 0;
		$worksheet = &$workbook->addworksheet($Rowaddworksheet);
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));

	} // end if
	

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"R1206.xls\"");
	header("Content-Disposition: inline; filename=\"R1206.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>