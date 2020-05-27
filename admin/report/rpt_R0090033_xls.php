<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R0090033_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
		$position_id = "b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "c.PL_CODE=e.PL_CODE";
		$line_code = "c.PL_CODE";
		$line_name = "e.PL_NAME";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);		
		$line_title=" สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		$position_id = "b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "c.PN_CODE=e.PN_CODE";
		$line_code = "c.PN_CODE";
		$line_name = "e.PN_NAME  as PL_NAME";	
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
		$position_id = "b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "c.EP_CODE=e.EP_CODE";
		$line_code = "c.EP_CODE";
		$line_name = "e.EP_NAME  as PL_NAME";	
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);		
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
		$position_id = "b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "c.TP_CODE=e.TP_CODE";
		$line_code = "c.TP_CODE";
		$line_name = "e.TP_NAME  as PL_NAME";	
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);		
		$line_title=" ชื่อตำแหน่ง";
	} // end if	
	
	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS = 1)";
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
	if($search_diff_type != 0){
		switch($search_diff_type){
			case 1 :
				if($DPISDB=="odbc"){
					if($plus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = $plus_diff)";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) >= 0)";
				}elseif($DPISDB=="oci8"){
					if($plus_diff) $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) = $plus_diff)";
					else $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) >= 0)";
				}elseif($DPISDB=="mysql"){
					if($plus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = $plus_diff)";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) >= 0)";
				} // end if
			break;
			case -1 :
				if($DPISDB=="odbc"){
					if($minus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) < 0)";
				}elseif($DPISDB=="oci8"){
					if($minus_diff) $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((nvl(g.KC_EVALUATE, 0) - nvl(h.PC_TARGET_LEVEL, 0)) < 0)";
				}elseif($DPISDB=="mysql"){
					if($minus_diff) $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) = ". ($minus_diff * -1) .")";
					else $arr_search_condition[] = "((g.KC_EVALUATE - h.PC_TARGET_LEVEL) < 0)";
				} // end if
			break;
		} // end switch case
	} // end if

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
		if(trim($search_org_id)  || trim($search_org_ass_id)){ 
			if($select_org_structure==0){
                        /*ปรับแก้ 2017/04/19 */
                            // ของเดิม $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";   
                          //  $arr_search_condition[] = "(a.ORG_ID_SALARY = $search_org_id)";  /*เพิ่ม*/ 
                            $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
                        }else if($select_org_structure==1){ 
                            // ของเดิม $arr_search_condition[] = "(b.ORG_ID = $search_org_id)"; 
                          //  $arr_search_condition[] = "(a.ORG_ID_ASS = $search_org_id)"; /* เพิ่ม */
                            $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			}
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0){ 
                            // ของเดิม $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
                            $arr_search_condition[] = "(a.ORG_ID_1_SALARY = $search_org_id_1)";       /* เพิ่ม */
			}else if($select_org_structure==1){ 
                            // ของเดิม $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
                            $arr_search_condition[] = "(a.ORG_ID_1_ASS = $search_org_id_1)";  /* เพิ่ม */
                        
                        }
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
	$report_title = "$DEPARTMENT_NAME||รายงานแผนพัฒนาผลการปฏิบัติงานรายบุคคล ปีงบประมาณ $search_budget_year";
	if(in_array(1, $search_kf_cycle)) $report_title .= " ครั้งที่ 1";
	if(in_array(2, $search_kf_cycle)) $report_title .= (in_array(1, $search_kf_cycle)?" และ":"")." ครั้งที่ 2";
	if($search_diff_type == -1) $report_title .= "||(เฉพาะระดับของสมรรถนะต่ำกว่าที่กำหนด)";
	elseif($search_diff_type == 1) $report_title .= "||(เฉพาะระดับของสมรรถนะสูงกว่าที่กำหนด)";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0902";

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
		$ws_head_line1 = array("ลำดับ","ชื่อ - สกุล","ตำแหน่ง","สมรรถนะ","ระดับที่กำหนด","ผลการประเมิน","ผลต่าง");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C");
		$ws_width = array(10,35,35,30,12,12,12);
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
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_KPI_FORM a
																		inner join PER_KPI_COMPETENCE g on (a.KF_ID=g.KF_ID)
																	) inner join PER_COMPETENCE i on (a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE)
																) inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
															) inner join PER_POSITION_COMPETENCE h on ($position_id=h.POS_ID) and (g.CP_CODE=h.CP_CODE) and h.PER_TYPE=$search_per_type
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)						 
						 $search_condition
						 order by		c.ORG_ID, a.PER_ID, (g.KC_EVALUATE - g.PC_TARGET_LEVEL), g.CP_CODE ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		/*เดิม 18/04/2017 */
                /*$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			PER_KPI_FORM a, PER_PERSONAL b, $position_table c, PER_ORG d, $line_table e, PER_PRENAME f, 
						 					PER_KPI_COMPETENCE g, PER_POSITION_COMPETENCE h, PER_COMPETENCE i
						 where		a.PER_ID=b.PER_ID and $position_join and c.ORG_ID=d.ORG_ID and $line_join  
						 					and b.PN_CODE=f.PN_CODE(+) and a.KF_ID=g.KF_ID and $position_id=h.POS_ID and g.CP_CODE=h.CP_CODE 
											and a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE and h.PER_TYPE=$search_per_type
											$search_condition
						 order by		c.ORG_ID, a.PER_ID, (nvl(g.KC_EVALUATE, 0) - nvl(g.PC_TARGET_LEVEL, 0)), g.CP_CODE ";*/
                /*แก้ไข Release 5.2.1.7  18/04/2017*/
                $cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, a.ORG_ID as ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			PER_KPI_FORM a, PER_PERSONAL b, $position_table c, PER_ORG d, $line_table e, PER_PRENAME f, 
						 					PER_KPI_COMPETENCE g, PER_POSITION_COMPETENCE h, PER_COMPETENCE i
						 where		a.PER_ID=b.PER_ID and $position_join and a.ORG_ID=d.ORG_ID and $line_join  
						 					and b.PN_CODE=f.PN_CODE(+) and a.KF_ID=g.KF_ID and $position_id=h.POS_ID and g.CP_CODE=h.CP_CODE 
											and a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE and h.PER_TYPE=$search_per_type 
											$search_condition
						 order by		a.ORG_ID, a.PER_ID, (nvl(g.KC_EVALUATE, 0) - nvl(g.PC_TARGET_LEVEL, 0)), g.CP_CODE ";                                 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, b.PER_NAME, b.PER_SURNAME, b.LEVEL_NO, c.ORG_ID, d.ORG_NAME, $line_name,
											g.CP_CODE, i.CP_NAME, g.KC_EVALUATE, g.PC_TARGET_LEVEL
						 from			(
												(
													(
														(
															(
																(
																	(
																		PER_KPI_FORM a
																		inner join PER_KPI_COMPETENCE g on (a.KF_ID=g.KF_ID)
																	) inner join PER_COMPETENCE i on (a.DEPARTMENT_ID=i.DEPARTMENT_ID and g.CP_CODE=i.CP_CODE)
																) inner join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
															) inner join PER_POSITION_COMPETENCE h on ($position_id=h.POS_ID) and (g.CP_CODE=h.CP_CODE) and h.PER_TYPE=$search_per_type
														) inner join $position_table c on ($position_join)
													) inner join PER_ORG d on (c.ORG_ID=d.ORG_ID)
												) inner join $line_table e on ($line_join)
											) left join PER_PRENAME f on (b.PN_CODE=f.PN_CODE)						 
						 $search_condition
						 order by		c.ORG_ID, a.PER_ID, (g.KC_EVALUATE - g.PC_TARGET_LEVEL), g.CP_CODE ";
	} // end if
	if($select_org_structure==1) { 
		/*$cmd = str_replace("c.ORG_ID", "b.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);*/
            $cmd = str_replace("a.ORG_ID", "a.ORG_ID_ASS", $cmd);
            $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	$PER_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
		if($ORG_NAME=="" || $ORG_NAME=="NULL"  || $ORG_NAME =="-")	$ORG_NAME="[ไม่ระบุ $ORG_TITLE]";
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$DATA_ORD = "";
		$PN_NAME = "";
		$PER_NAME = "";
		$PER_SURNAME = "";
		$PL_NAME = "";
		$PT_NAME = "";
		$LEVEL_NO = "";
		
		if($PER_ID != $data[PER_ID]){ 
			$data_row++;

			$PER_ID = $data[PER_ID];
			$PN_NAME = $data[PN_NAME];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$LEVEL_NO = $data[LEVEL_NO];
			$PL_NAME = $data[PL_NAME];
			
			$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis3->send_cmd($cmd);
//			$db_dpis->show_error();
			$data3 = $db_dpis3->get_array();
			$LEVEL_NAME=$data3[LEVEL_NAME];
			$POSITION_LEVEL = $data3[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
                        if($search_per_type==3){ 
                            $POSITION_LEVEL = "";
                        }

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = $data2[PT_NAME];
	
			$DATA_ORD = "$data_row.";
		} // end if

                $CP_CODE = $data[CP_CODE];
		$CP_NAME = $data[CP_NAME];
                $KC_EVALUATE = " ".$data[KC_EVALUATE]." ";
                $PC_TARGET_LEVEL[$CP_CODE] = $data[PC_TARGET_LEVEL];
//		$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
		$PC_DIFF = " ".($KC_EVALUATE-$PC_TARGET_LEVEL[$CP_CODE])." ";
//		$PC_DIFF = $KC_EVALUATE - $PC_TARGET_LEVEL;
		//if ($KPI_SCORE_DECIMAL==1) $KC_EVALUATE = number_format($KC_EVALUATE);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $DATA_ORD;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][perline] = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
		$arr_content[$data_count][cp_name] = $CP_NAME;
		$arr_content[$data_count][kc_evaluate] = $KC_EVALUATE;
		$arr_content[$data_count][pc_target_level] = $PC_TARGET_LEVEL[$CP_CODE];
		$arr_content[$data_count][pc_diff] = $PC_DIFF;
		
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_border_2 = array("TLB","TB","TB","TB","TB","TB","TRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0);
			$wsdata_colmerge_2 = array(1,1,1,1,1,1,1);
			$wsdata_fontfmt_2 = array("","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][perline];
			$CP_NAME = $arr_content[$data_count][cp_name];
			$KC_EVALUATE = $arr_content[$data_count][kc_evaluate];
			$PC_TARGET_LEVEL = $arr_content[$data_count][pc_target_level];
			$PC_DIFF = $arr_content[$data_count][pc_diff];
			
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
				$arr_data[] = $PL_NAME;
				$arr_data[] = $CP_NAME;
				$arr_data[] = $PC_TARGET_LEVEL;
				$arr_data[] = $KC_EVALUATE;
				$arr_data[] = $PC_DIFF;
	
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else 	$ndata = $arr_data[$arr_column_map[$i]];
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