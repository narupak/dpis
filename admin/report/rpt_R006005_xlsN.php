<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R006005_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$search_per_type = 1;
	$search_per_status = 1;
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";

	$list_type_text = $ALL_REPORT_TITLE;

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
		$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "บัญชีสรุปจำนวนข้าราชการและอัตราเงินเดือนรวมของข้าราชการ||สำหรับการเลื่อนขั้นเงินเดือน ประจำปีงบประมาณ $search_budget_year||$DEPARTMENT_NAME $MINISTRY_NAME";
	$report_code = "R0605";

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
		$ws_head_line1 = array("<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max","<**1**>กลุ่มข้าราชการระดับ $search_level_min - $search_level_max");
		$ws_head_line2 = array("","จำนวน","<**1**>เลื่อน 1 เมษายน ". ($search_budget_year - 1),"<**1**>เลื่อน 1 เมษายน ". ($search_budget_year - 1),"<**1**>เลื่อน 1 เมษายน ". ($search_budget_year - 1),"อัตรา","วงเงิน","เงินเลื่อน","วงเงิน","<**2**>เลื่อน 1 ตุลาคม ". ($search_budget_year - 1),"<**2**>เลื่อน 1 ตุลาคม ". ($search_budget_year - 1),"<**2**>เลื่อน 1 ตุลาคม ". ($search_budget_year - 1),"","รวม");
		$ws_head_line3 = array("ส่วนราชการ","อัตราที่มี","ร้อยละ","1 ขั้น","ค่า","เงินเดือน","เลื่อนขั้น","ขั้นที่ใช้","เลื่อนขั้น","ร้อยละ","1 ขั้น","1.5 ขั้น","ค่า","ทั้งปี");
		$ws_head_line4 = array("","ผู้ครองอยู่","15","","ตอบแทน","รวม ณ","ร้อยละ 6","ณ 1 เม.ย.","คงเหลือ","15","","","ตอบแทน","2 ขั้น");
		$ws_head_line5 = array("","ณ 1 มี.ค.","","","4 %","1 ก.ย.","","","","","","","4 %","");
		$ws_colmerge_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line2 = array(0,0,1,1,1,0,0,0,0,1,1,1,0,0);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line4 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line5 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("","","","","","","","","","","","","","");
		$ws_border_line2 = array("TLR","TRL","TBRL","TBRL","TBRL","TRL","TRL","TRL","TRL","TBRL","TBRL","TBRL","TRL","TRL");
		$ws_border_line3 = array("LR","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL");
		$ws_border_line4 = array("LR","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL","RL");
		$ws_border_line5 = array("LBR","RBL","RBL","RBL","RBL","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(25,10,8,8,10,12,10,10,10,8,8,8,10,8);
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
	
	function print_header($search_level_min, $search_level_max){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $search_budget_year;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_head_line3, $ws_head_line4, $ws_head_line5;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4, $ws_colmerge_line4;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4, $ws_border_line5, $ws_fontfmt_line1;
		global $ws_headalign_line1, $ws_width;

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
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line3[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 4
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line4[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line4[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 5
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line5[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line5[$arr_column_map[$i]], $ws_colmerge_line5[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	$cmd = " select PER_ID from PER_SALARYHIS where SAH_KF_YEAR='$search_budget_year' and SAH_KF_CYCLE in (1,2) ";
	$count_data = $db_dpis->send_cmd($cmd);
	
	$data_count = 0;
	for($i=0; $i<4; $i++){
		if($i == 0 || ($i % 2) == 0){
			$arr_content[$data_count][type] = "HEADER";
			if ($BKK_FLAG==1)
				$arr_content[$data_count][name] = "กรุงเทพมหานคร";
			else
				$arr_content[$data_count][name] = "ราชการบริหารส่วนกลาง";
			
			$data_count++;
		}else{
			if($i==1){ //?????
				$search_level_min = "01";			//1
				$search_level_max = "08";			//8
			}elseif($i==3){
				$search_level_min = "09";			//9
				$search_level_max = "11";		//11
			} // end if
					
			if($DPISDB=="odbc"){ 

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			}elseif($DPISDB=="oci8"){

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and SUBSTR(trim(PER_STARTDATE), 1, 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "$cmd <br>";
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			}elseif($DPISDB=="mysql"){

				$cmd = " select count(PER_ID) as TOTAL_PERSON from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-03-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_person = $data[TOTAL_PERSON] + 0;
				$percent_person = ($total_person * 15) / 100;
					
				$cmd = " select sum(PER_SALARY) as TOTAL_SALARY from PER_PERSONAL where PER_TYPE = 1 and PER_STATUS = 1 and LEFT(trim(PER_STARTDATE), 10) < '". ($search_budget_year - 543 - 1) ."-09-01' and trim(LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$total_salary = $data[TOTAL_SALARY] + 0;
				$percent_salary = ($total_salary * 6) / 100;

				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no1_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select sum(b.SALP_SALARY_NEW - b.SALP_SALARY_OLD) as PROMOTED_SALARY from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=1 and b.SALP_YN=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_salary = $data[PROMOTED_SALARY] + 0;					
				$remain_salary = $percent_salary - $promoted_salary;
						
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_1 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_LEVEL=1.5 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_2 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALQ_TYPE=2 and b.SALP_YN=1 and b.SALP_PERCENT=4 and a.PER_ID=b.PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_no2_3 = $data[PROMOTED_PERSON] + 0;
	
				$cmd = " select count(a.PER_ID) as PROMOTED_PERSON from PER_PERSONAL a, PER_SALPROMOTE b where trim(a.LEVEL_NO) >= '". str_pad($search_level_min, 2, "0", STR_PAD_LEFT) ."' and trim(a.LEVEL_NO) <= '". str_pad($search_level_max, 2, "0", STR_PAD_LEFT) ."' and b.SALQ_YEAR='$search_budget_year' and b.SALP_YN=1 and a.PER_ID=b.PER_ID having sum(b.SALP_LEVEL) >= 2 ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
				$data = $db_dpis->get_array();
				$promoted_person_all = $data[PROMOTED_PERSON] + 0;

			} // end if

			$arr_content[$data_count][type] = "DETAIL";
			$arr_content[$data_count][name] = str_repeat(" ", 5) . "$DEPARTMENT_NAME";
			$arr_content[$data_count][total_person] = number_format($total_person);
			$arr_content[$data_count][percent_person] = number_format($percent_person, 2);
			$arr_content[$data_count][promoted_person_no1_1] = number_format($promoted_person_no1_1);
			$arr_content[$data_count][promoted_person_no1_2] = number_format($promoted_person_no1_2);
			$arr_content[$data_count][total_salary] = number_format($total_salary, 2);
			$arr_content[$data_count][percent_salary] = number_format($percent_salary, 2);
			$arr_content[$data_count][promoted_salary] = number_format($promoted_salary, 2);
			$arr_content[$data_count][remain_salary] = number_format($remain_salary, 2);
			$arr_content[$data_count][promoted_person_no2_1] = number_format($promoted_person_no2_1);
			$arr_content[$data_count][promoted_person_no2_2] = number_format($promoted_person_no2_2);
			$arr_content[$data_count][promoted_person_no2_3] = number_format($promoted_person_no2_3);
			$arr_content[$data_count][promoted_person_all] = number_format($promoted_person_all);
			
			$data_count++;
		} // end if
	} // end for	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		/**$temp_report_title = "$REF_NAME||$NAME||$report_title";
		$arr_title = explode("||", $temp_report_title);**/
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
		
//		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R", "R");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$TOTAL_PERSON = $arr_content[$data_count][total_person];
			$PERCENT_PERSON = $arr_content[$data_count][percent_person];
			$PROMOTED_PERSON_NO1_1 = $arr_content[$data_count][promoted_person_no1_1];
			$PROMOTED_PERSON_NO1_2 = $arr_content[$data_count][promoted_person_no1_2];
			$TOTAL_SALARY = $arr_content[$data_count][total_salary];
			$PERCENT_SALARY = $arr_content[$data_count][percent_salary];
			$PROMOTED_SALARY = $arr_content[$data_count][promoted_salary];
			$REMAIN_SALARY = $arr_content[$data_count][remain_salary];
			$PROMOTED_PERSON_NO2_1 = $arr_content[$data_count][promoted_person_no2_1];
			$PROMOTED_PERSON_NO2_2 = $arr_content[$data_count][promoted_person_no2_2];
			$PROMOTED_PERSON_NO2_3 = $arr_content[$data_count][promoted_person_no2_3];
			$PROMOTED_PERSON_ALL = $arr_content[$data_count][promoted_person_all];
			if ($BKK_FLAG==1)
				$CHECK_DEPT = "สำนักการคลังตรวจสอบแล้ว";
			else
				$CHECK_DEPT = "กรมบัญชีกลางตรวจสอบแล้ว";
			
			if(($data_count % 2) == 0){
				if($data_count > 0){ 
					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 2, "ได้ตรวจสอบถูกต้องแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));
					$worksheet->write_string($xlsRow, 10, "$CHECK_DEPT", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					if ($confirm_name) 
						$worksheet->write_string($xlsRow, 2, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));
					else
						$worksheet->write_string($xlsRow, 2, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));
					$worksheet->write_string($xlsRow, 10, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					if ($confirm_position) 
						$worksheet->write_string($xlsRow, 2, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));
					else
						$worksheet->write_string($xlsRow, 2, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));
					$worksheet->write_string($xlsRow, 10, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 2, "วันที่                         ตุลาคม ". ($search_budget_year - 1), set_format("xlsFmtTableDetail", "", "C", "", 0));
					$worksheet->write_string($xlsRow, 10, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
					$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
					$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
				} // end if

				if($data_count == 0) 			print_header("O1", "O3");		//print_header(1, 8);
				elseif($data_count == 2) 	print_header("K1", "K3");		//print_header(9, 11);
			} // end if

			$arr_data = (array) null;
			$arr_data[] = $NAME;
			$arr_data[] = $TOTAL_PERSON;
			$arr_data[] = $PERCENT_PERSON;
			$arr_data[] = $PROMOTED_PERSON_NO1_1;
			$arr_data[] = $PROMOTED_PERSON_NO1_2;
			$arr_data[] = $TOTAL_SALARY;
			$arr_data[] = $PERCENT_SALARY;
			$arr_data[] = $PROMOTED_SALARY;
			$arr_data[] = $REMAIN_SALARY;
			$arr_data[] = $PERCENT_PERSON;
			$arr_data[] = $PROMOTED_PERSON_NO2_1;
			$arr_data[] = $PROMOTED_PERSON_NO2_2;
			$arr_data[] = $PROMOTED_PERSON_NO2_3;
			$arr_data[] = $PROMOTED_PERSON_ALL;

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					if($REPORT_ORDER == "DETAIL")
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					else
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for				

		if($data_count > 0){ 
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

			$xlsRow++;
//			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
//			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "ได้ตรวจสอบถูกต้องแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));
//			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 10, "กรมบัญชีกลางตรวจสอบแล้ว", set_format("xlsFmtTableDetail", "", "C", "", 0));
//			$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
//			$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 5, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 6, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 7, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 8, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 9, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 10, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 11, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 12, "", set_format("xlsFmtTableDetail", "", "R", "", 0));
			$worksheet->write_string($xlsRow, 13, "", set_format("xlsFmtTableDetail", "", "R", "", 0));

			$xlsRow++;
			if ($confirm_name) 
				$worksheet->write_string($xlsRow, 2, "( $confirm_name )", set_format("xlsFmtTableDetail", "", "C", "", 0));
			else
				$worksheet->write_string($xlsRow, 2, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 10, "( ............................................................ )", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			if ($confirm_position) 
				$worksheet->write_string($xlsRow, 2, "ตำแหน่ง  $confirm_position", set_format("xlsFmtTableDetail", "", "C", "", 0));
			else
				$worksheet->write_string($xlsRow, 2, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 10, "ตำแหน่ง ............................................................ ", set_format("xlsFmtTableDetail", "", "C", "", 0));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 2, "วันที่                         ตุลาคม ". ($search_budget_year - 1), set_format("xlsFmtTableDetail", "", "C", "", 0));
			$worksheet->write_string($xlsRow, 10, "วันที่ ............................................. ", set_format("xlsFmtTableDetail", "", "C", "", 0));
		} // end if
	}else{
		$xlsRow = 0;
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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 13, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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