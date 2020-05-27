<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("../report/rpt_function.php");
	
	include ("rpt_data_map_emp_comdtl_pos_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = show_date_format($data[COM_DATE],$DATE_DISPLAY);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);
		
	$COM_TYPE = trim($data[COM_TYPE]);

	$company_name = "";
	$report_title = "บัญชีจัดตำแหน่งลูกจ้างประจำเข้ากลุ่มงานตามระบบตำแหน่งลูกจ้างประจำ ณ วันที่ 1 เมษายน 2553 || (กำหนดตามหนังสือสำนักงาน ก.พ. ที่ นร 1008/ ว  ลงวันที่  มีนาคม 2553) || $DEPARTMENT_NAME  $MINISTRY_NAME";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "";

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
		$ws_width = array(6,6,15,15,30,15,25,6,25,20,6,25,6,40);
		$ws_head_line1 = array("ลำดับ","ตำแหน่ง","<**1**>สังกัด/พื้นที่ปฏิบัติงาน","<**1**>สังกัด/พื้นที่ปฏิบัติงาน","<**1**>สังกัด/พื้นที่ปฏิบัติงาน","<**2**>ตำแหน่งตามระบบเดิม","<**2**>ตำแหน่งตามระบบเดิม","<**2**>ตำแหน่งตามระบบเดิม","<**2**>ตำแหน่งตามระบบเดิม","<**3**>ตำแหน่งตามระบบใหม่","<**3**>ตำแหน่งตามระบบใหม่","<**3**>ตำแหน่งตามระบบใหม่","<**3**>ตำแหน่งตามระบบใหม่","หมายเหตุ");
		$ws_head_line2 = array("ที่","เลขที่","กลาง/ภูมิภาค","จังหวัด","สำนัก/เขต/แขวง/ศูนย์","หมวด","ชื่อตำแหน่ง","ชั้น","ชื่อผู้ครองตำแหน่ง","กลุ่มงาน","รหัส","ชื่อตำแหน่ง","ระดับ","");
		$ws_colmerge_line1 = array(0,0,1,1,1,1,1,1,1,1,1,1,1,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TB","TB","TBR","TB","TB","TB","TBR","TB","TB","TB","TB","TLR");
		$ws_border_line2 = array("LBR","LBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","LBR");
		$ws_wraptext_line1 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_wraptext_line2 = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_rotate_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_rotate_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C");
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
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width, $column_function;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2;
		global $ws_wraptext_line1, $ws_wraptext_line2, $ws_rotate_line1, $ws_rotate_line2, $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

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
	} // function		

	$search2 = "";
	if ($POSITION_NO_CHAR=="Y") { 
		if($DPISDB=="oci8"){	
			$search2 = "a.CMD_POS_NO_NAME=c.POEM_NO_NAME(+) and ";
		}else{
			$search2 = "a.CMD_POS_NO_NAME=c.POEM_NO_NAME and ";
		}
	}
	if($DPISDB=="odbc"){
		$select_top = ($current_page==$total_page)?($count_data - ($data_per_page * ($current_page - 1))):$data_per_page;			
		$cmd = "	select		b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, a.PN_CODE, a.PN_CODE_ASSIGN,
							a.CMD_POSITION, a.CMD_LEVEL, a.POEM_ID, a.LEVEL_NO as NEW_LEVEL_NO,
							a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
				from			(PER_COMDTL a 
							     left join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
							) left join PER_POS_EMP c on ($search2 a.CMD_POS_NO=c.POEM_NO)
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and c.POEM_STATUS = 1
				order by 	a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	}elseif($DPISDB=="oci8"){
		$cmd = "  select		b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, a.PN_CODE, a.PN_CODE_ASSIGN,
											a.CMD_POSITION, a.CMD_LEVEL, a.POEM_ID, a.LEVEL_NO as NEW_LEVEL_NO,
											a.CMD_NOTE1, a.CMD_NOTE2 , a.CMD_POS_NO_NAME, a.CMD_POS_NO
				from			PER_COMDTL a, PER_PERSONAL b, PER_POS_EMP c
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and a.PER_ID=b.PER_ID(+) and 
									a.CMD_POS_NO=c.POEM_NO(+) and  $search2
									c.POEM_STATUS = 1
				order by 		a.CMD_POS_NO_NAME, a.CMD_POS_NO ";	
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		b.PN_CODE as PRENAME_CODE, b.PER_NAME, b.PER_SURNAME, a.PN_CODE, a.PN_CODE_ASSIGN,
							a.CMD_POSITION, a.CMD_LEVEL, a.POEM_ID, a.LEVEL_NO as NEW_LEVEL_NO,
							a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
				from			(PER_COMDTL a 
							     left join PER_PERSONAL b on (a.PER_ID=b.PER_ID)
							) left join PER_POS_EMP c on ($search2 a.CMD_POS_NO=c.POEM_NO)
				where		a.COM_ID=$COM_ID and c.DEPARTMENT_ID = $DEPARTMENT_ID and c.POEM_STATUS = 1
				order by 		a.CMD_POS_NO_NAME, a.CMD_POS_NO ";
	} // end if	
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd";
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;
	
		$PN_CODE = trim($data[PRENAME_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = trim($data2[PN_NAME]);		
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$CMD_PN_CODE = trim($data[PN_CODE]);
		$CMD_PN_CODE_NEW = trim($data[PN_CODE_ASSIGN]);
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];	//รูปแบบใหม่เก็บแต่ชื่อตำแหน่ง
		//รูปแบบที่เก็บแบบเดิม ใส่ POS_NO| POSITION บัญชีเก่าก่อนแก้รูปแบบใหม่
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $CMD_POSITION);
		}else{
			$arr_temp = explode("\|", $CMD_POSITION);
		}
		if(is_array($arr_temp)){
			if($arr_temp[1]){
				$CMD_POEM_NO = $arr_temp[0];
				$CMD_POSITION = $arr_temp[1];
			}else{
				$CMD_POSITION = $arr_temp[0];
			}
		}
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);
		$CMD_POS_NO_NAME =  trim($data[CMD_POS_NO_NAME]);
		$CMD_POS_NO =  trim($data[CMD_POS_NO]);			//*******

		$cmd = " select PN_NAME, PG_NAME from PER_POS_NAME a, PER_POS_GROUP b 
						where trim(PN_CODE)='$CMD_PN_CODE' and a.PG_CODE = b.PG_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PN_NAME = trim($data2[PN_NAME]);
		$CMD_PG_NAME = trim($data2[PG_NAME]);

		$cmd = " select PN_NAME, PG_NAME from PER_POS_NAME a, PER_POS_GROUP b 
						where trim(PN_CODE)='$CMD_PN_CODE_NEW' and a.PG_CODE = b.PG_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_PN_NAME = trim($data2[PN_NAME]);
		$NEW_PG_NAME = trim($data2[PG_NAME]);

		$POEM_ID = $data[POEM_ID];
		$cmd = "	select a.ORG_ID_1, b.PN_NAME, c.ORG_NAME, c.OT_CODE, c.PV_CODE
					from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
					where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = trim($data2[ORG_NAME]);
		$OT_CODE = trim($data2[OT_CODE]);
		$PV_CODE = trim($data2[PV_CODE]);
		$ORG_ID_1 = $data2[ORG_ID_1];
			
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data2[ORG_NAME]);

		if ($OT_CODE == "03" && $ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
			$ORG_NAME = $ORG_NAME_1;

		$cmd = " select OT_NAME from PER_ORG_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_ORG_TYPE = trim($data2[OT_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CMD_PV_NAME = trim($data2[PV_NAME]);

		$cmd = " select POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		$PER_NAME = $PER_NAME?$PN_NAME . $PER_NAME . ' ' . $PER_SURNAME:'ว่าง';
		if (strpos($CMD_POSITION,"ชั้น  2") !== false || strpos($CMD_POSITION,"ชั้น 2") !== false || strpos($CMD_POSITION,"ชั้น2") !== false) $CMD_CLASS = "2";
		elseif (strpos($CMD_POSITION,"ชั้น  3") !== false || strpos($CMD_POSITION,"ชั้น 3") !== false || strpos($CMD_POSITION,"ชั้น3") !== false) $CMD_CLASS = "3";
		elseif (strpos($CMD_POSITION,"ชั้น  4") !== false || strpos($CMD_POSITION,"ชั้น 4") !== false || strpos($CMD_POSITION,"ชั้น4") !== false) $CMD_CLASS = "4";

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO_NAME.$CMD_POS_NO;	//$CMD_POEM_NO
		$arr_content[$data_count][cmd_org_type] = $CMD_ORG_TYPE;
		$arr_content[$data_count][cmd_pv_name] = $CMD_PV_NAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][cmd_pg_name] = $CMD_PG_NAME;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		$arr_content[$data_count][cmd_class] = $CMD_CLASS;
		$arr_content[$data_count][per_name] = $PER_NAME;
		$arr_content[$data_count][new_pg_name] = $NEW_PG_NAME;
		$arr_content[$data_count][new_pn_code] = $CMD_PN_CODE_NEW;
		$arr_content[$data_count][new_pn_name] = $NEW_PN_NAME;
		$arr_content[$data_count][new_level_name] = $NEW_LEVEL_NAME;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

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
			$wsdata_fontfmt_1 = array("B", "B", "B", "B", "B", "B", "B", "B", "B", "B", "B", "B", "B", "B");
			$wsdata_align_1 = array("C", "C", "L", "L", "L", "L", "L", "L", "L", "L", "C", "L", "L", "L");
			$wsdata_border_1 = array("TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR", "TLBR");
			$wsdata_border_2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "");
			$wsdata_border_3 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
			$wsdata_colmerge_1 = array("C", "C", "L", "L", "L", "L", "L", "L", "L", "L", "C", "L", "L", "L");
			$wsdata_wraptext = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1);
			$wsdata_fontfmt_2 = array("", "", "", "", "", "", "", "", "", "", "", "", "", "");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$CMD_POEM_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_ORG_TYPE = $arr_content[$data_count][cmd_org_type];
			$CMD_PV_NAME = $arr_content[$data_count][cmd_pv_name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$CMD_PG_NAME = $arr_content[$data_count][cmd_pg_name];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_CLASS = $arr_content[$data_count][cmd_class];
			$PER_NAME = $arr_content[$data_count][per_name];
			$NEW_PG_NAME = $arr_content[$data_count][new_pg_name];
			$NEW_PN_CODE = $arr_content[$data_count][new_pn_code];
			$NEW_PN_NAME = $arr_content[$data_count][new_pn_name];
			$NEW_LEVEL_NAME = $arr_content[$data_count][new_level_name];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];

			if($CONTENT_TYPE=="CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $CMD_POEM_NO;
				$arr_data[] = $CMD_ORG_TYPE;
				$arr_data[] = $CMD_PV_NAME;
				$arr_data[] = $ORG_NAME;
				$arr_data[] = $CMD_PG_NAME;
				$arr_data[] = $CMD_POSITION;
				$arr_data[] = $CMD_CLASS;
				$arr_data[] = $PER_NAME;
				$arr_data[] = $NEW_PG_NAME;
				$arr_data[] = $NEW_PN_CODE;
				$arr_data[] = $NEW_PN_NAME;
				$arr_data[] = $NEW_LEVEL_NAME;
				$arr_data[] = $CMD_NOTE1;

				if ($data_count==count($arr_content)-1) $wsdata_border = $wsdata_border_3; else $wsdata_border = $wsdata_border_1;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
	//					echo "1..$xlsRow-->$ndata, ".$wsdata_border_1[$arr_column_map[$i]].", ".$wsdata_colmerge_1[$arr_column_map[$i]].", map_border=".$wsdata_border_1[$arr_column_map[$i]]." , border=$border<br>";
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge, $wsdata_wraptext[$arr_column_map[$i]], 0));
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
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งจัดตำแหน่ง.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งจัดตำแหน่ง.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>