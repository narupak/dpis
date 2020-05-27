<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R009001_format_excel.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID(+)";
		$line_table = "PER_LINE";
		$line_join = "c.PL_CODE=e.PL_CODE(+)";
		$line_code = "c.PL_CODE";
		$line_code_po = "c.POS_NO";
                $order_position = "to_number(replace(c.POS_NO,'-',''))";
		$line_name = "e.PL_NAME  as PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID(+)";
		$line_table = "PER_POS_NAME";
		$line_join = "c.PN_CODE=e.PN_CODE(+)";
		$line_code = "c.PN_CODE";
		$line_code_po = "c.POEM_NO";
                $order_position = "to_number(replace(c.POEM_NO,'-',''))";
		$line_name = "e.PN_NAME  as PL_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID(+)";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "c.EP_CODE=e.EP_CODE(+)";
		$line_code = "c.EP_CODE";
		$line_code_po = "c.POEMS_NO";
                $order_position = "to_number(replace(c.POEMS_NO,'-',''))";
		$line_name = "e.EP_NAME  as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID(+)";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "c.TP_CODE=e.TP_CODE(+)";
		$line_code = "c.TP_CODE";
		$line_code_po = "c.POT_NO";
                $order_position = "to_number(replace(c.POT_NO,'-',''))";
		$line_name = "e.TP_NAME as PL_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
        
        if($order_by==1)$t_order_by = ' a.ORG_ID ,  a.TOTAL_SCORE desc, b.LEVEL_NO ';
        else if($order_by==2)$t_order_by = '  a.ORG_ID ,  a.TOTAL_SCORE asc, b.LEVEL_NO ';
        else $t_order_by = " a.ORG_ID , $order_position ,  a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
        
	//$arr_search_condition[] = "(b.PER_STATUS = 1)";
   $arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
  	if(trim($search_budget_year)){ 
		if($DPISDB=="odbc"){ 
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="oci8"){
			$arr_search_condition[] = "(SUBSTR(a.KF_START_DATE, 1, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(SUBSTR(a.KF_END_DATE, 1, 10) < '". ($search_budget_year - 543)."-10-01')";
		}elseif($DPISDB=="mysql"){
			$arr_search_condition[] = "(LEFT(a.KF_START_DATE, 10) >= '". ($search_budget_year - 543 - 1)."-10-01')";
			$arr_search_condition[] = "(LEFT(a.KF_END_DATE, 10) < '". ($search_budget_year - 543)."-10-01')";
		} // end if
	} // end if
	$arr_search_condition[] = "(a.KF_CYCLE in (". implode(",", $search_kf_cycle) ."))";

	$list_type_text = $ALL_REPORT_TITLE;
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			//เดิม
			//if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			//else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                        $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= $line_search_name;
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(d.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการประเมิน KPI รายบุคคล ปีงบประมาณ $search_budget_year";
	if(in_array(1, $search_kf_cycle)) $report_title .= " ครั้งที่ 1";
	if(in_array(2, $search_kf_cycle)) $report_title .= (in_array(1, $search_kf_cycle)?" และ":"")." ครั้งที่ 2";
	if($search_diff_type == -1) $report_title .= "||(เฉพาะระดับของสมรรถนะต่ำกว่าที่กำหนด)";
	elseif($search_diff_type == 1) $report_title .= "||(เฉพาะระดับของสมรรถนะสูงกว่าที่กำหนด)";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0901";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
		//$ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","ตำแหน่ง","ครั้งที่","ผลสำเร็จของงาน","สมรรถนะ","ระดับผลการประเมิน");
                if ($have_am && $have_al) {
                    $ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","เลขที่ตำแหน่ง","ตำแหน่ง","ครั้งที่","คะแนนผลสำเร็จของงาน","คะแนนสมรรถนะ","ผลรวม","ระดับผลการประเมิน(หลัก)","ระดับผลการประเมิน(ย่อย)","อนุญาตให้เห็นคะแนน");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0);
                    $ws_border_line1   = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
                    $ws_fontfmt_line1  = array("B","B","B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1= array("C","C","C","C","C","C","C","C","C","C","C");
                    $ws_width = array(10,35,25,55,10,15,15,15,15,15,15);
                } else if ($have_am && !$have_al) {
                    $ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","เลขที่ตำแหน่ง","ตำแหน่ง","ครั้งที่","คะแนนผลสำเร็จของงาน","คะแนนสมรรถนะ","ผลรวม","ระดับผลการประเมิน(หลัก)","อนุญาตให้เห็นคะแนน");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0);
                    $ws_border_line1   = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
                    $ws_fontfmt_line1  = array("B","B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1= array("C","C","C","C","C","C","C","C","C","C");
                    $ws_width = array(10,35,25,55,10,15,15,15,15,15);
                } else if ($have_al && !$have_am) {
                    $ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","เลขที่ตำแหน่ง","ตำแหน่ง","ครั้งที่","คะแนนผลสำเร็จของงาน","คะแนนสมรรถนะ","ผลรวม","ระดับผลการประเมิน(ย่อย)","อนุญาตให้เห็นคะแนน");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0);
                    $ws_border_line1   = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
                    $ws_fontfmt_line1  = array("B","B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1= array("C","C","C","C","C","C","C","C","C","C");
                    $ws_width = array(10,35,25,55,10,15,15,15,15,15);
                }else{
                    $ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","เลขที่ตำแหน่ง","ตำแหน่ง","ครั้งที่","คะแนนผลสำเร็จของงาน","คะแนนสมรรถนะ","ผลรวม","อนุญาตให้เห็นคะแนน");
                    $ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0);
                    $ws_border_line1   = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
                    $ws_fontfmt_line1  = array("B","B","B","B","B","B","B","B","B");
                    $ws_headalign_line1= array("C","C","C","C","C","C","C","C","C");
                    $ws_width = array(10,35,25,55,10,15,15,15,15);
                }
                if($IS_ACCEPT_CONFIG){
                    $ws_head_line1_is_ac = array("รับทราบในการประเมินคะแนน");
                    $ws_colmerge_line1_is_ac = array(0);
                    $ws_border_line1_is_ac   = array("TLBR");
                    $ws_fontfmt_line1_is_ac  = array("B");
                    $ws_headalign_line1_is_ac = array("C");
                    $ws_width_is_ac = array(15);
                    
                    $ws_head_line1 = array_merge($ws_head_line1,$ws_head_line1_is_ac);
                    $ws_colmerge_line1 = array_merge($ws_colmerge_line1_is_ac,$ws_colmerge_line1);
                    $ws_border_line1   = array_merge($ws_border_line1_is_ac,$ws_border_line1);
                    $ws_fontfmt_line1  = array_merge($ws_fontfmt_line1_is_ac,$ws_fontfmt_line1);
                    $ws_headalign_line1 = array_merge($ws_headalign_line1_is_ac,$ws_headalign_line1);
                    $ws_width = array_merge($ws_width,$ws_width_is_ac);
                }
                
               /* 
                var_dump($ws_head_line1);
                var_dump($ws_colmerge_line1);
                var_dump($ws_border_line1);
                var_dump($ws_fontfmt_line1);
                var_dump($ws_headalign_line1);
                var_dump($ws_width);
                die();
                */
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
		global $ws_head_line1;
		global $ws_colmerge_line1;
		global $ws_border_line1;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			(
												(
													(
														(
															PER_KPI_FORM a
															inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)
						 $search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		/*$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			PER_KPI_FORM a, PER_PERSONAL b, $position_table c, PER_ORG d, $line_table e, PER_PRENAME f
						 where		a.PER_ID=b.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and $line_join 
						 					and b.PN_CODE=f.PN_CODE(+)
											$search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";*/
                /*Release 5.2.1.19 */
                $cmd = "    select a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, a.ORG_ID as ORG_ID, d.ORG_NAME, $line_name, $line_code_po,
                                a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE ,a.KF_SCORE_STATUS , a.ACCEPT_FLAG
                            from PER_KPI_FORM a, 
                                PER_PERSONAL b, 
                                $position_table c, 
                                PER_ORG d, 
                                $line_table e, 
                                PER_PRENAME f
                            where a.PER_ID=b.PER_ID 
                                and $position_join 
                                and a.ORG_ID=d.ORG_ID 
                                and $line_join 
                                and b.PN_CODE=f.PN_CODE(+)
                                $search_condition
                            order by $t_order_by ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											a.SUM_KPI, a.SUM_COMPETENCE, a.TOTAL_SCORE, a.KF_CYCLE
						 from			(
												(
													(
														(
															PER_KPI_FORM a
															inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)
						 $search_condition
						 order by		c.ORG_ID, a.KF_CYCLE, a.TOTAL_SCORE desc, b.LEVEL_NO ";
	} // end if
	if($select_org_structure==1) { 
		/*$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);*/
            $cmd = str_replace("a.ORG_ID", "a.ORG_ID_ASS", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        /*echo "<pre>".$cmd;
        die();*/
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$PL_NAME = $data[PL_NAME];
		$SUM_KPI = $data[SUM_KPI];
		$SUM_COMPETENCE = $data[SUM_COMPETENCE];
		$TOTAL_SCORE = $data[TOTAL_SCORE];
		$KF_CYCLE = $data[KF_CYCLE];
		if($search_per_type==1){$pos_no = $data[POS_NO];}//เลขที่ตำแหน่งข้าราชการ
		if($search_per_type==2){$pos_no = $data[POEM_NO];}//เลขที่ตำแหน่งลูกจ้างประจำ
		if($search_per_type==3){$pos_no = $data[POEMS_NO];}//เลขที่ตำแหน่งพนักงานราชการ
		if($search_per_type==4){$pos_no = $data[POT_NO];}//เลขที่ตำแหน่งลูกจ้างชั่วคราว
                
                $KF_SCORE_STATUS = $data[KF_SCORE_STATUS];
                $ACCEPT_FLAG = $data[ACCEPT_FLAG];
                if($ACCEPT_FLAG=='0'){
                    $TXT_ACCEPT_FLAG = "ไม่รับทราบ";
                }else if($ACCEPT_FLAG=='1'){
                    $TXT_ACCEPT_FLAG = "รับทราบ";
                }else{
                    $TXT_ACCEPT_FLAG = "";
                }

		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis3->send_cmd($cmd);
//		$db_dpis->show_error();
		$data3 = $db_dpis3->get_array();
		$LEVEL_NAME=$data3[LEVEL_NAME];
		$POSITION_LEVEL = $data3[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = $data2[PT_NAME];
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][perline] = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"".$LEVEL_NAME;
		$arr_content[$data_count][pos_no] = $pos_no;
		$arr_content[$data_count][sum_kpi] = $SUM_KPI;
		$arr_content[$data_count][sum_competence] = $SUM_COMPETENCE;
		$arr_content[$data_count][total_score] = $TOTAL_SCORE;
		$arr_content[$data_count][kf_cycle] = $KF_CYCLE;
                
                //หา ระดับผลการประเมิน Begin...
                $BudgetYear = $search_budget_year;
                /*$cmd = " SELECT AL_NAME 
                    FROM PER_ASSESS_LEVEL 
                    WHERE AL_YEAR = '$BudgetYear' AND AL_CYCLE = $KF_CYCLE ";*/
                $cmd =" select	DISTINCT AL.AL_NAME  , AM.AM_NAME
                        from	PER_ASSESS_LEVEL AL , PER_ASSESS_MAIN AM
                        where AL.AM_CODE=AM.AM_CODE(+) 
                        AND (AL.DEPARTMENT_ID = $DEPARTMENT_ID AND AL.org_id=$ORG_ID) 
                        AND (AL.PER_TYPE=$search_per_type) 
                        AND (AL.AL_YEAR = '$BudgetYear') 
                        AND (AL.AL_CYCLE = $KF_CYCLE) 
                        AND  $TOTAL_SCORE BETWEEN  AL.AL_POINT_MIN AND AL.AL_POINT_MAX";
                //echo "<pre>".$cmd;
                //die();
		$cnt = $db_dpis2->send_cmd($cmd);
                if(!empty($cnt)){
                    $data2 = $db_dpis2->get_array();
                    $AL_NAME = trim($data2[AL_NAME]);
                    $AM_NAME = trim($data2[AM_NAME]);
                }else{
                   $AL_NAME=""; 
                   $AM_NAME=""; 
                }
                if(empty($SUM_KPI) && empty($SUM_COMPETENCE) && empty($TOTAL_SCORE)){ $AL_NAME=""; $AM_NAME=""; }
                $arr_content[$data_count][EvaluationResults] = $AL_NAME;
                $arr_content[$data_count][EvaluationResultsAMname] = $AM_NAME;
                if($KF_SCORE_STATUS){
                    $KF_SCORE_STATUS="อนุญาตให้เห็นคะแนน";
                }else{
                    $KF_SCORE_STATUS="";
                }
                $arr_content[$data_count][kf_score_status] = $KF_SCORE_STATUS;
                $arr_content[$data_count][accept_flag] = $TXT_ACCEPT_FLAG;
                
		// end.
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){

		print_header();		

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
                        if ($have_am && $have_al) {
                            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("C","L","C","L","C","C","C","C","C","C","C","C");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TRB","TRB","TRB","TRB","TRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0);
                            $wsdata_colmerge_2 = array(1,1,1,1,1,1,1,1,1,1,1);
                            $wsdata_fontfmt_2 = array("","","","","","");
                        }else if ($have_am && !$have_al) {
                            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("C","L","C","L","C","C","C","C","C","C","C");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TRB","TRB","TRB","TRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
                            $wsdata_colmerge_2 = array(1,1,1,1,1,1,1,1,1,1);
                            $wsdata_fontfmt_2 = array("","","","","","","","","","");
                        }else if ($have_al && !$have_am) {
                             $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("C","L","C","L","C","C","C","C","C","C","C");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TRB","TRB","TRB","TRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
                            $wsdata_colmerge_2 = array(1,1,1,1,1,1,1,1,1,1);
                            $wsdata_fontfmt_2 = array("","","","","","","","","","");
                        }else{
                            $wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B");
                            $wsdata_align_1 = array("C","L","C","L","C","C","C","C","C","C");
                            $wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
                            $wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TRB","TRB","TRB");
                            $wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0);
                            $wsdata_colmerge_2 = array(1,1,1,1,1,1,1,1,1);
                            $wsdata_fontfmt_2 = array("","","","","","","","","");
                        }
                        if($IS_ACCEPT_CONFIG){
                            $wsdata_fontfmt_1_is_ac = array("B");
                            $wsdata_align_1_is_ac = array("C");
                            $wsdata_border_1_is_ac = array("TLRB");
                            $wsdata_border_2_is_ac = array("TRB");
                            $wsdata_colmerge_1_is_ac = array(0);
                            $wsdata_colmerge_2_is_ac = array(1);
                            $wsdata_fontfmt_2_is_ac = array("");
                          
                            $wsdata_fontfmt_1 = array_merge($wsdata_fontfmt_1,$wsdata_fontfmt_1_is_ac);
                            $wsdata_align_1 = array_merge($wsdata_align_1,$wsdata_align_1_is_ac);
                            $wsdata_border_1 = array_merge($wsdata_border_1,$wsdata_border_1_is_ac);
                            $wsdata_border_2 = array_merge($wsdata_border_2,$wsdata_border_2_is_ac);
                            $wsdata_colmerge_1 = array_merge($wsdata_colmerge_1,$wsdata_colmerge_1_is_ac);
                            $wsdata_colmerge_2 = array_merge($wsdata_colmerge_2,$wsdata_colmerge_2_is_ac);
                            $wsdata_fontfmt_2 = array_merge($wsdata_fontfmt_2,$wsdata_fontfmt_2_is_ac); 
                        }
                        
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$PL_NAME = $arr_content[$data_count][perline];
			$SUM_KPI = $arr_content[$data_count][sum_kpi];
			$SUM_COMPETENCE = $arr_content[$data_count][sum_competence];
			$TOTAL_SCORE = $arr_content[$data_count][total_score];
			$KF_CYCLE = $arr_content[$data_count][kf_cycle];
                        if ($have_am){
                            $EvaluationResultsAMname=$arr_content[$data_count][EvaluationResultsAMname];
                        }
                        if ($have_al){
                            $EvaluationResults=$arr_content[$data_count][EvaluationResults];
                        }    
                        $KF_SCORE_STATUS = $arr_content[$data_count][kf_score_status];
                        $ACCEPT_FLAG = $arr_content[$data_count][accept_flag];
                        
                        
                           
                        
			if($REPORT_ORDER == "ORG"){
				$arr_data = (array) null;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
				$arr_data[] = "<**1**>".$NAME;
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], "L", $border, $merge));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $NAME;
				$arr_data[] = $POS_NO;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $KF_CYCLE;
				$arr_data[] = $SUM_KPI;
				$arr_data[] = $SUM_COMPETENCE;
				$arr_data[] = $TOTAL_SCORE;
                                if ($have_am){
                                    $arr_data[] = $EvaluationResultsAMname;
                                } 
                                if ($have_al){
                                    $arr_data[] = $EvaluationResults;
                                }    
                                $arr_data[] = $KF_SCORE_STATUS;
                                if($IS_ACCEPT_CONFIG){
                                    $arr_data[] = $ACCEPT_FLAG;
                                }    
                                
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge));
						$colseq++;
					}
				}
			} // end if
		} // end for
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>