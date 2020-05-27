<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	include ("es_font_size.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("es_rpt_R1203_01_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

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
        $list_type_texts = "";
	/*รูปแบบการออกรายงาน*/
	
	/*สำนัก/กอง*/
	if( ($search_org_id > 0) || ($search_org_ass_id > 0)){ 
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
                            $search_condition .= " AND  (PP.ORG_ID = $search_org_id) ";
                            //$list_type_text .= " - $search_org_name"; ไม่ต้องแสดงมันจะซ้ำกัน
                            if($search_org_id_1){
                                $search_condition .= " AND  (PP.ORG_ID_1 = $search_org_id_1) ";
                                $list_type_texts= ' - '.$search_org_name_1;
                            }
			} // end if
			
		}else{
			if(trim($search_org_ass_id)){ 
                            $search_condition .= " AND  (a.ORG_ID = $search_org_ass_id)";
                            //$list_type_text .= " - $search_org_ass_name";
                            if($search_org_ass_id_1){
                                $search_condition .= " AND  (a.ORG_ID_1 = $search_org_ass_id_1) ";
                                $list_type_texts= ' - '.$search_org_ass_name_1;
                            }
			} // end if
			
		}
	}else{
		$search_condition .= " AND (a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}

/*------------------------------------------*/
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text"."$list_type_texts";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1205";

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
		$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","TRBL","TRBL","TRBL","TRBL","TRBL","TRBL","RBL","TRBL","TRBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(8,35,8,8,8,8,8,8,10,10,10,10,32);
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
	
	
	//แสดงข้อมูลว่าปิดรอบแล้วหรือยัง
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'IS_OPEN_TIMEATT_ES' ";
	$db_dpis->send_cmd_fast($cmd);
	$data = $db_dpis->get_array_array();
	$IS_OPEN_TIMEATT_ES = $data[CONFIG_VALUE];
	$tmpstar= "''";
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$search_monthapp = $search_month;
		$tmpstar= "(case when (select APPROVE_DATE from PER_WORK_TIME_CONTROL 
								WHERE  CLOSE_YEAR = $search_yearBgn
					  			AND CLOSE_MONTH = $search_monthapp
					  			AND DEPARTMENT_ID = (select ORG_ID from PER_PERSONAL where PER_ID=a.PER_ID)) is null then '*' else '' end
						)";
	}
	
	if($DPISDB=="oci8"){
		
		$cmd = file_get_contents('../es_rpt_R1205.sql');	
		$cmd=str_ireplace(":BEGINDATEAT","'".$search_dateBgn."'",$cmd);
		$cmd=str_ireplace(":TODATEAT","'".$search_dateEnd."'",$cmd);
		
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
			}
				
		}
		
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}
		
		if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
			/*ตามโครงสร้างกฏหมาย*/
			if($search_per_type==1){ 
				$POS_NO_FROM=" LEFT JOIN PER_POSITION PP ON (PP.POS_ID=a.POS_ID)";
			}elseif($search_per_type==2){ 
				$POS_NO_FROM=" LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=a.POEM_ID)";
			}elseif($search_per_type==3){ 
				$POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=a.POEMS_ID)";
			}elseif($search_per_type==4){ 
				$POS_NO_FROM="  LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=a.POT_ID)";
			}
			
			$conTPER_ORG = " LEFT JOIN PER_ORG  ORG ON(ORG.ORG_ID=PP.ORG_ID) ";
			
		}else{
			/*ตามโครงสร้างมอบหมายงาน*/
			$conTPER_ORG = " LEFT JOIN PER_ORG_ASS  ORG ON(ORG.ORG_ID=a.ORG_ID) ";
		}
		
		
		$cmd=" select ORG.ORG_NAME AS KONG,
					  PN.PN_NAME,a.PER_NAME,a.PER_SURNAME, ".$tmpstar." AS datastar,
					  x.* from PER_PERSONAL a left join  
					  (".$cmd.") x ON(a.PER_ID=x.PER_ID(+)) 
			left join PER_PRENAME PN on(PN.PN_CODE=a.PN_CODE) 
			$POS_NO_FROM
			$conTPER_ORG
			WHERE  $con_per_status
			
			$con_per_type
			$search_condition
			$CON_PER_AUDIT_FLAG
			order by ORG.ORG_NAME ASC ,a.PER_NAME ASC , a.PER_SURNAME ASC
		";
	}
	//echo $cmd;
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$chkKONG = "";

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
		$arr_content[$data_count][name] = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME].$data[DATASTAR];
		$arr_content[$data_count][LSICK] = $data[LSICK]==0?'':round($data[LSICK],2);
		$arr_content[$data_count][LSICKCNT] = $data[LSICKCNT]==0?'':round($data[LSICKCNT],2);		
		$arr_content[$data_count][LAKIT] = $data[LAKIT]==0?'':round($data[LAKIT],2);	
		$arr_content[$data_count][LAKITCNT] = $data[LAKITCNT]==0?'':round($data[LAKITCNT],2);	
		$arr_content[$data_count][LSICK_LAKIT] = $data[LSICK_LAKIT]==0?'':round($data[LSICK_LAKIT],2);	
		$arr_content[$data_count][LSICK_LAKITCNT] = $data[LSICK_LAKITCNT]==0?'':round($data[LSICK_LAKITCNT],2);	
		$arr_content[$data_count][LATE] = $data[LATE]==0?'':round($data[LATE],2);	
		$arr_content[$data_count][PAKPON] = $data[PAKPON]==0?'':round($data[PAKPON],2);
		$arr_content[$data_count][PAKPONCNT] = $data[PAKPONCNT]==0?'':round($data[PAKPONCNT],2);	
		$arr_content[$data_count][LAOTH] = $data[LAOTH]==0?'':round($data[LAOTH],2);	
		$data_count++;
	} // end while
	
	
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

//		print_header();
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("R","L","R","R","R","R","R","R","R","R","R","R","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","");
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
				$arr_data[] = $arr_content[$data_count][ORDER];
				$arr_data[] = $arr_content[$data_count][name];
				$arr_data[] = $arr_content[$data_count][LSICK];
				$arr_data[] = $arr_content[$data_count][LSICKCNT];
				$arr_data[] = $arr_content[$data_count][LAKIT];
				$arr_data[] = $arr_content[$data_count][LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKIT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LATE];
				$arr_data[] = $arr_content[$data_count][PAKPON];
				$arr_data[] = $arr_content[$data_count][PAKPONCNT];
				$arr_data[] = $arr_content[$data_count][LAOTH];
				$arr_data[] = "";
				//$arr_data[] = $arr_content[$data_count][REMARK];
				
				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
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
				
				//if ($data_row==$arr_content[$data_count][ORDER]) {
				//$worksheet->write($xlsRow+2, 0,$data_rowxxxxx, set_format("xlsFmtSubTitle", "", "L", "", 0));	
				//}
			
				
			} // end if
			
			
			/*	 if($REPORT_ORDER == "ORG"){
					 
					 $worksheet ->write(10,0, $PERSON_TYPE[$search_per_type]."ทั้งหมด "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+1,0, "ตำแหน่งว่าง "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+2,0, "ยืมตัวมาช่วยราชการ "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+3,0, "มาปฏิบัติราชการ "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+4,0, "ไปราชการ "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+5,0, "มาสาย "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+6,0, "ไม่มาปฏิบัติราชการ "." 999 "." คน", set_format("xlsFmtTitle", "B", "C", "", 1));
					 $worksheet ->write(10+7,0, "ผู้ตรวจ". str_repeat(".", 70), set_format("xlsFmtTitle", "B", "C", "", 1));
					 
				 }*/

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
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if
	

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"R1205.xls\"");
	header("Content-Disposition: inline; filename=\"R1205.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>