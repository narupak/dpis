<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");	
	include ("../report/rpt_function.php");

	include ("rpt_data_move_comdtl_N1_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
									a.COM_TYPE, COM_CONFIRM, b.COM_DESC 
					 from		PER_COMMAND a, PER_COMTYPE b
					 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
		
	$COM_NO = trim($data[COM_NO]);
	$COM_NAME = trim($data[COM_NAME]);
	$COM_DATE = $data[COM_DATE];
	$COM_DATE = show_date_format($COM_DATE,5);
	$COM_NOTE = trim($data[COM_NOTE]);
	$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
	
	$COM_TYPE = trim($data[COM_TYPE]);
	$COM_CONFIRM = trim($data[COM_CONFIRM]);

	/*เพิ่มใหม่ 06/07/2018 http://dpis.ocsc.go.th/Service/node/1657*/
	if ($print_order_by==1){  $order_str = "tb_main.CMD_SEQ";  //เดิม $order_str = "a.CMD_SEQ";
        }else if($print_order_by==2){ $order_str =  " tb_main.GROUP_MANAGE_OLD, tb_main.ORG1_SEQ_OLD, tb_main.ORG1_CODE_OLD, tb_main.ORG2_SEQ_OLD, tb_main.ORG2_CODE_OLD,tb_main.ORG3_SEQ_OLD,tb_main.ORG3_CODE_OLD,tb_main.ORG4_SEQ_OLD,tb_main.ORG4_CODE_OLD,tb_main.CMD_POS_NO";   // เดิม $order_str = "a.CMD_ORG3, a.CMD_ORG4, a.CMD_POS_NO_NAME, a.CMD_POS_NO";
        }else{ $order_str =  " tb_main.GROUP_MANAGE_NEW, tb_main.ORG1_SEQ_NEW, tb_main.ORG1_CODE_NEW, tb_main.ORG2_SEQ_NEW,tb_main.ORG2_CODE_NEW,tb_main.ORG3_SEQ_NEW, tb_main.ORG3_CODE_NEW,tb_main.ORG4_SEQ_NEW,tb_main.ORG4_CODE_NEW,tb_main.CMD_POS_NO_NEW";  
        }

	$company_name = "";
	$report_title = "บัญชีรายละเอียดการย้าย$PERSON_TYPE[$COM_PER_TYPE]แนบท้ายคำสั่ง $DEPARTMENT_NAME ที่ $COM_NO ลงวันที่ $COM_DATE";
	$report_code = "P0311";
	
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
		$ws_head_line1 = array("ลำดับ","ชื่อ","วุฒิ/สาขา/สถานศึกษา","<**1**>ตำแหน่งและส่วนราชการเดิม","<**1**>","<**1**>","<**1**>","<**2**>ตำแหน่งและส่วนราชการที่ย้าย","<**2**>","<**2**>","<**2**>","ตั้งแต่วันที่","หมายเหตุ");
		$ws_head_line2 = array("ที่","(เลขประจำตัวประชาชน)","","ตำแหน่ง","สังกัด","เลขที่","เงินเดือน","ตำแหน่ง","สังกัด","เลขที่","เงินเดือน","","");
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,0,0);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TB","TB","TB","TBR","TBL","TB","TB","TB","TLR","TLR");
		$ws_border_line2 = array("LBR","LBR","LBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(6,45,60,40,45,6,10,40,45,6,10,10,100);
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
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2, $ws_border_line1, $ws_border_line2;
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

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	}elseif($DPISDB=="oci8"){
		/* เดิม    
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO,a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
            */ 
            /*เพิ่มใหม่ 06/07/2018 http://dpis.ocsc.go.th/Service/node/1657*/
            $cmd = "WITH tb_main AS (
                            SELECT  DISTINCT
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(OL_CODE)='01' and org_id_ref=1) AS ORG1_SEQ_OLD,
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(OL_CODE)='01' and org_id_ref=1) AS ORG1_SEQ_NEW,
                                  
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(OL_CODE)='01' and org_id_ref=1) AS ORG1_CODE_OLD,
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(OL_CODE)='01' and org_id_ref=1) AS ORG1_CODE_NEW,
                                  
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o.OL_CODE)='02' and o.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)) AS ORG2_SEQ_OLD,
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o.OL_CODE)='02' and o.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)) AS ORG2_SEQ_NEW,  

                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o.OL_CODE)='02' and o.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)) AS ORG2_CODE_OLD,
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o.OL_CODE)='02' and o.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)) AS ORG2_CODE_NEW,

                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_name=substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1)and trim(o.OL_CODE)='03' and o.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1))) AS ORG3_SEQ_OLD,
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org3,'\|',1)=0 THEN a.cmd_org3 ELSE substr(a.cmd_org3,INSTR(a.cmd_org3,'\|',1)+2,length(a.cmd_org3)) END END
                                  and trim(o.OL_CODE)='03' and o.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1))) AS ORG3_SEQ_NEW,
                                  
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_name=substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1)and trim(o.OL_CODE)='03' and o.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1))) AS ORG3_CODE_OLD,
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org3,'\|',1)=0 THEN a.cmd_org3 ELSE substr(a.cmd_org3,INSTR(a.cmd_org3,'\|',1)+2,length(a.cmd_org3)) END END
                                  and trim(o.OL_CODE)='03' and o.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1))) AS ORG3_CODE_NEW,

                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_name=substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_name=substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) AS ORG4_SEQ_OLD, 
                                (SELECT o.ORG_SEQ_NO FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org4,'\|',1)=0 THEN a.cmd_org4 ELSE substr(a.cmd_org4,INSTR(a.cmd_org4,'\|',1)+2,length(a.cmd_org4)) END END 
                                  and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org3,'\|',1)=0 THEN a.cmd_org3 ELSE substr(a.cmd_org3,INSTR(a.cmd_org3,'\|',1)+2,length(a.cmd_org3)) END END 
                                  and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) AS ORG4_SEQ_NEW,
                                  
                                  
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_name=substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_name=substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) AS ORG4_CODE_OLD,
                                (SELECT o.ORG_CODE FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org4,'\|',1)=0 THEN a.cmd_org4 ELSE substr(a.cmd_org4,INSTR(a.cmd_org4,'\|',1)+2,length(a.cmd_org4)) END END 
                                  and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org3,'\|',1)=0 THEN a.cmd_org3 ELSE substr(a.cmd_org3,INSTR(a.cmd_org3,'\|',1)+2,length(a.cmd_org3)) END END 
                                  and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                  and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                  and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) AS ORG4_CODE_NEW,
                                
                                CASE WHEN (SELECT o.ORG_CODE FROM per_org o WHERE o.org_name=substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_name=substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_name=substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_name=substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) IS NULL THEN '0' ELSE '1' END   AS GROUP_MANAGE_OLD,  
                                CASE WHEN (SELECT o.ORG_CODE FROM per_org o WHERE o.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org4,0,INSTR(a.cmd_org4,'\|',1)-1) ELSE 
                                    CASE WHEN INSTR(a.cmd_org4,'\|',1)=0 THEN a.cmd_org4 ELSE substr(a.cmd_org4,INSTR(a.cmd_org4,'\|',1)+2,length(a.cmd_org4)) END END 
                                     and trim(o.OL_CODE)='04' and o.org_id_ref IN(SELECT o3.org_id FROM per_org o3 WHERE o3.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org3,0,INSTR(a.cmd_org3,'\|',1)-1) ELSE 
                                    CASE WHEN INSTR(a.cmd_org3,'\|',1)=0 THEN a.cmd_org3 ELSE substr(a.cmd_org3,INSTR(a.cmd_org3,'\|',1)+2,length(a.cmd_org3)) END END 
                                     and trim(o3.OL_CODE)='03' and o3.org_id_ref IN(SELECT o2.org_id FROM per_org o2 WHERE o2.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org2,0,INSTR(a.cmd_org2,'\|',1)-1) ELSE 
                                    CASE WHEN INSTR(a.cmd_org2,'\|',1)=0 THEN a.cmd_org2 ELSE substr(a.cmd_org2,INSTR(a.cmd_org2,'\|',1)+2,length(a.cmd_org2)) END END 
                                     and trim(o2.OL_CODE)='02' and o2.org_id_ref IN(SELECT o1.org_id FROM per_org o1 WHERE o1.org_id=CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1), ' +-.0123456789',' '))) is null THEN substr(a.cmd_org1,0,INSTR(a.cmd_org1,'\|',1)-1) ELSE 
                                    CASE WHEN INSTR(a.cmd_org1,'\|',1)=0 THEN a.cmd_org1 ELSE substr(a.cmd_org1,INSTR(a.cmd_org1,'\|',1)+2,length(a.cmd_org1)) END END 
                                     and trim(o1.OL_CODE)='01' and o1.org_id_ref=1)))) IS NULL THEN '0' ELSE '1' END GROUP_MANAGE_NEW,
  
                                CASE WHEN substr(a.CMD_POSITION,INSTR(a.CMD_POSITION,'\|',1,1)+2,INSTR(a.CMD_POSITION,'\|',1,2)-INSTR(a.CMD_POSITION,'\|',1,1)-2) is not null 
                                    THEN TO_NUMBER(substr(a.CMD_POSITION,INSTR(a.CMD_POSITION,'\|',1,1)+2,INSTR(a.CMD_POSITION,'\|',1,2)-INSTR(a.CMD_POSITION,'\|',1,1)-2))
                                Else  CASE WHEN LENGTH(TRIM(TRANSLATE(substr(a.CMD_POSITION,INSTR(a.CMD_POSITION,'\|',1,1)+2,length(a.CMD_POSITION)), ' +-.0123456789',' '))) is null 
                                     THEN TO_NUMBER(substr(a.CMD_POSITION,INSTR(a.CMD_POSITION,'\|',1,1)+2,length(a.CMD_POSITION))) END END AS CMD_POS_NO_NEW,

                                a.CMD_SEQ,a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
                                a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL,
                                a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO,a.CMD_SALARY,
                                a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN, 
                                a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
                            FROM PER_COMDTL a,PER_PERSONAL b 
                            WHERE a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID )
                    SELECT * from tb_main 
                    order by $order_str ";
                                                        
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, b.PER_CARDNO, b.PER_BIRTHDATE, b.PER_TYPE, b.LEVEL_NO,
											a.EN_CODE, a.CMD_POSITION, a.CMD_ORG3, a.CMD_ORG4, a.CMD_OLD_SALARY, a.CMD_LEVEL, 
											a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO as NEW_LEVEL_NO, a.CMD_SALARY,
											a.PL_CODE_ASSIGN, a.PN_CODE_ASSIGN, a.EP_CODE_ASSIGN, a.TP_CODE_ASSIGN, 
											a.CMD_DATE, a.CMD_NOTE1, a.CMD_NOTE2, a.CMD_POS_NO_NAME, a.CMD_POS_NO
						 from			PER_COMDTL a, PER_PERSONAL b
						 where		a.COM_ID=$COM_ID and a.PER_ID=b.PER_ID 
						 order by 	$order_str ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = $data_row = 0;
//	$ORG_ID = $ORG_ID_1 = $ORG_ID_2 = -1;
	$CMD_ORG3 = $CMD_ORG4 = -1;
	while($data = $db_dpis->get_array()){
		$data_row++;
		
		$PER_ID = $data[PER_ID];
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PN_NAME = $data2[PN_NAME];		
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PER_CARDNO = $data[PER_CARDNO];
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$CMD_DATE_DISPLAY);
		$PER_TYPE = $data[PER_TYPE];
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$EDU_TYPE = "%2%";
		if($DPISDB=="odbc"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, EDU_INSTITUTE, EDU_HONOR, CT_CODE_EDU
								from		( 
													(
														PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								where		a.PER_ID = $PER_ID and a.EDU_TYPE like '$EDU_TYPE' ";
		} // end if
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$EN_NAME = trim($data2[EN_NAME]);
		if (!trim($data2[EN_SHORTNAME])) {
			$EN_SHORTNAME = trim($data2[EN_NAME]);
		} else {
			$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
		}
		if (trim($data2[EM_NAME])) {
			$EM_NAME = "(".trim($data2[EM_NAME]).")";
		}
		$INS_NAME = trim($data2[INS_NAME]);
		if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
		$EDU_HONOR = trim($data2[EDU_HONOR]);
		if ($EDU_HONOR && strpos($EDUCATION_NAME,"เกียรตินิยม") !== true) $EDU_HONOR = "เกียรตินิยม" . $EDU_HONOR;
		$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_EDU' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		if ($CT_NAME=="ไทย") $CT_NAME = "";
		
		$CMD_LEVEL = trim($data[CMD_LEVEL]);
		$CMD_POSITION = $data[CMD_POSITION];
		if($DPISDB=="mysql"){
			$arr_temp = explode("|", $CMD_POSITION);
		}else{
			$arr_temp = explode("\|", $CMD_POSITION);
		}
                if(count($arr_temp)==5){
                    $TMP_PL_NAME = $arr_temp[0];
                    $TMP_NEW_POS_NO = $arr_temp[1];
                    $TMP_PM_NAME = $arr_temp[2];
                    $TMP_NEW_PM_NAME = $arr_temp[3];
                }elseif(count($arr_temp)==2){ /*เพิ่มเติม 23/02/2017*/
                   $TMP_PL_NAME = $arr_temp[0];
                    $TMP_NEW_POS_NO = $arr_temp[1];
                    $TMP_PM_NAME = $arr_temp[2];
                    $TMP_NEW_PM_NAME = "";
                }elseif(count($arr_temp)==3){ /*เพิ่มเติม 23/02/2017*/
                   $TMP_PL_NAME = $arr_temp[0];
                    $TMP_NEW_POS_NO = $arr_temp[1];
                    $TMP_PM_NAME = $arr_temp[2];
                    $TMP_NEW_PM_NAME = ""; /*Release 5.2.1.6*/
                    //$TMP_NEW_PM_NAME = $tmp_data[2]; /*เดิม*/
                }elseif(count($arr_temp)==4){
                    $TMP_PL_NAME = $arr_temp[0];
                    $TMP_NEW_POS_NO = $arr_temp[1];
                    $TMP_PM_NAME = '';
                    $TMP_NEW_PM_NAME = $arr_temp[2];
                    
                }else{ //cnt=3,
                    $TMP_PL_NAME = $arr_temp[0];
                    $TMP_NEW_POS_NO = $arr_temp[1];
                    $TMP_PM_NAME = $arr_temp[2];
                }
		$CMD_POSITION = $TMP_PL_NAME;
		$NEW_POS_NO = $TMP_NEW_POS_NO;
		$NEW_PM_NAME = $TMP_NEW_PM_NAME;
                /*เดิม*/
		/*$CMD_POSITION = $arr_temp[0];
		$NEW_POS_NO = $arr_temp[1];
		$NEW_PM_NAME = $arr_temp[2];*/
                
		$CMD_POS_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POS_NO = trim($data[CMD_POS_NO]); 
		if ($print_order_by==1) {
			$CMD_ORG3 = $data[CMD_ORG3];
			$CMD_ORG4 = $data[CMD_ORG4];
			$CMD_ORG5 = $data[CMD_ORG5];
		}
		$CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
		$NEW_LEVEL_NO = trim($data[NEW_LEVEL_NO]);
		$CMD_SALARY = $data[CMD_SALARY];
		$CMD_DATE = show_date_format($data[CMD_DATE],$CMD_DATE_DISPLAY);
		$CMD_NOTE1 = trim($data[CMD_NOTE1]);
		$CMD_NOTE2 = trim($data[CMD_NOTE2]);

		//--ตำแหน่งและส่วนราชการเดิม
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$CMD_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$CMD_LEVEL_NAME = trim($data2[POSITION_LEVEL]);
		
		//--ตำแหน่งและส่วนราชการที่ย้าย
		$cmd = "select POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$NEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2= $db_dpis2->get_array();
		$NEW_POSITION_TYPE = trim($data2[POSITION_TYPE]);
		$NEW_LEVEL_NAME = trim($data2[POSITION_LEVEL]);

		$NEW_ORG_NAME = $NEW_ORG_NAME_1 = $NEW_ORG_NAME_2 =  "";
		if($PER_TYPE==1){
			$cmd = " select a.PM_CODE, a.PT_CODE, b.PT_NAME from PER_POSITION a, PER_TYPE b where trim(a.POS_NO)='$CMD_POS_NO' and a.PT_CODE=b.PT_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PT_CODE = trim($data2[PT_CODE]);
			$CMD_PT_NAME = trim($data2[PT_NAME]);
			$CMD_PM_CODE = trim($data2[PM_CODE]);
			
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$CMD_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_PM_NAME = trim($data2[PM_NAME]);
                        
                        /*เดิม*/
			//$CMD_POSITION = pl_name_format($CMD_POSITION, $CMD_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);
                        $CMD_POSITION = pl_name_format($CMD_POSITION, $TMP_PM_NAME, $CMD_PT_NAME, $CMD_LEVEL);

			$POS_ID = $data[POS_ID];
			$cmd = "	select		a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE
						from		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						where		a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			//-o--$NEW_POS_NO = trim($data2[POS_NO]);
			//-o--$NEW_PL_NAME = trim($data2[PL_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			$NEW_PT_CODE = trim($data2[PT_CODE]);
			$NEW_PT_NAME = trim($data2[PT_NAME]);
			$NEW_PM_CODE = trim($data2[PM_CODE]);

			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			/*$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$NEW_PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PM_NAME = trim($data2[PM_NAME]);*/
			
			$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where PL_CODE= '$PL_PN_CODE_ASSIGN' ";			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PL_NAME = trim($data2[PL_NAME]);
			
			$NEW_PL_NAME = pl_name_format($NEW_PL_NAME, $NEW_PM_NAME, $NEW_PT_NAME, $NEW_LEVEL_NO);
		}elseif($PER_TYPE==2){
			//$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

                        /*แก้ไข เพิ่มเติม 01/03/2017*/
                        $CMD_LEVEL_NAME_ARR = explode(" ",$CMD_LEVEL_NAME);
                        $CMD_POSITION  = trim($CMD_POSITION)?($CMD_POSITION ." ". $CMD_LEVEL_NAME_ARR[1]):"";
                        
			$POEM_ID = $data[POEM_ID];
			$cmd = "	select		a.POEM_NO, b.PN_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								where		a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			//-o--$NEW_POS_NO = trim($data2[POEM_NO]);
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			//-o--$NEW_PL_NAME = trim($data2[PN_NAME]);

			$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_PL_NAME = trim($data2[PN_NAME]);
			
			//$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
                        /*แก้ไข เพิ่มเติม 01/03/2017*/
                        $NEW_LEVEL_NAME_ARR = explode(" ",$NEW_LEVEL_NAME);
                        $NEW_PL_NAME  = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". $NEW_LEVEL_NAME_ARR[1]):"";
                        
		}elseif($PER_TYPE==3){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POEMS_ID = $data[POEMS_ID];
			$cmd = "	select		a.POEMS_NO, b.EP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_EMP a, PER_EMPSER_POS_NAME b, PER_ORG c
								where		a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			//-o--$NEW_POS_NO = trim($data2[POEMS_NO]);
			//-o--$NEW_PL_NAME = trim($data2[EP_NAME]);
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			//-o--$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);
			$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
					where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[EP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data2[EP_NAME]) : $PL_PN_NAME;
				$NEW_PL_NAME = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data2[EP_NAME]) : $PL_PN_NAME_ASSIGN;
			}
			$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		}  elseif($PER_TYPE==4){
			$CMD_POSITION = trim($CMD_POSITION)?($CMD_POSITION ." ". level_no_format($CMD_LEVEL)):"";

			$POT_ID = $data[POT_ID];
			$cmd = "	select		a.POT_NO, b.TP_NAME, c.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2
								from		PER_POS_TEMP a, PER_TEMP_POS_NAME b, PER_ORG c
								where		a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE and a.ORG_ID=c.ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
           
			//-o--$NEW_POS_NO = trim($data2[POEMS_NO]);
			//-o--$NEW_PL_NAME = trim($data2[EP_NAME]);
			//$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_NAME = trim($data2[ORG_NAME]);
			//$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
			$NEW_ORG_ID_1 = $data2[ORG_ID_1];
			$NEW_ORG_ID_2 = $data2[ORG_ID_2];
			
			$PL_PN_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]);
			$cmd = " select TP_CODE, TP_NAME from PER_TEMP_POS_NAME 
					where trim(TP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[TP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data2[TP_NAME]) : $PL_PN_NAME;
				$NEW_PL_NAME = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data2[TP_NAME]) : $PL_PN_NAME_ASSIGN;
			}
				$NEW_PL_NAME = trim($NEW_PL_NAME)?($NEW_PL_NAME ." ". level_no_format($NEW_LEVEL_NO)):"";
		} // end if
		$NEW_ORG_NAME_1 = "";
		if($NEW_ORG_ID_1){	
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		}
		$NEW_ORG_NAME_2 = "";
		if($NEW_ORG_ID_2){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$NEW_ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME_2 = $data2[ORG_NAME];
		}
		if ($CMD_ORG3=="-") $CMD_ORG3 = "";
		if ($CMD_ORG4=="-") $CMD_ORG4 = "";
		if ($CMD_ORG5=="-") $CMD_ORG5 = "";
		if ($NEW_ORG_NAME=="-") $NEW_ORG_NAME = "";
		if ($NEW_ORG_NAME_1=="-") $NEW_ORG_NAME_1 = "";
		if ($NEW_ORG_NAME_2=="-") $NEW_ORG_NAME_2 = "";
		
		$CMD_ORG3 = trim($data[CMD_ORG3]);
		
		if($DPISDB=="mysql"){
			$tmp_org3 = explode("|", trim($data[CMD_ORG3]));
		}else{
			$tmp_org3 = explode("\|", trim($data[CMD_ORG3]));
		}
		$CMD_ORG3 = trim($tmp_org3[0]);
		$ORG_ID = $tmp_org3[1]; 
		$NEW_ORG_NAME = "";
		if($ORG_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME = $data2[ORG_NAME]; 
		}
			
		$CMD_ORG4 = trim($data[CMD_ORG4]);
		
		if($DPISDB=="mysql"){
			$tmp_org4 = explode("|", trim($data[CMD_ORG4]));
		}else{
			$tmp_org4 = explode("\|", trim($data[CMD_ORG4]));
		}
		$CMD_ORG4 = trim($tmp_org4[0]);
		$ORG_ID_1 = $tmp_org4[1]; 
		$NEW_ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$NEW_ORG_NAME_1 = $data2[ORG_NAME];
		}
			
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		if ($print_order_by==2 ||$print_order_by==3 ) 
			$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"") ."\n". ($INS_NAME?"$INS_NAME":"");
		else
			$arr_content[$data_count][educate] = $EN_NAME ."\n". ($EM_NAME?"$EM_NAME":"");
		if ($MFA_FLAG==1 && $EDU_HONOR) $arr_content[$data_count][educate] .= "\n".trim($EDU_HONOR);
		if ($MFA_FLAG==1 && $CT_NAME) $arr_content[$data_count][educate] .= "\n".trim($CT_NAME);
		$arr_content[$data_count][cmd_pos_no_name] = $CMD_POS_NO_NAME;
		$arr_content[$data_count][cmd_pos_no] = $CMD_POS_NO;
		$arr_content[$data_count][cmd_position] = $CMD_POSITION;
		if ($DEPARTMENT_NAME=="กรมการปกครอง") {
			if ($CMD_OT_CODE=="03") {
				if (!$CMD_ORG5 && !$CMD_ORG4) $arr_content[$data_count][cmd_position] = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
				else $arr_content[$data_count][cmd_org3] = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
			} else {
				$arr_content[$data_count][cmd_org3] = trim($CMD_ORG4." ".$CMD_ORG3." ".$DEPARTMENT_NAME);
			}
			if ($NEW_OT_CODE=="03") { 
				if (!$NEW_ORG_NAME_2 && !$NEW_ORG_NAME_1) $arr_content[$data_count][new_position] = "ที่ทำการปกครอง".$NEW_ORG_NAME." ".$NEW_ORG_NAME;
				else $arr_content[$data_count][new_org3] = trim($NEW_ORG_NAME_2." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME);
			} else { 
				$arr_content[$data_count][new_org3] = trim($NEW_ORG_NAME_1." ".$NEW_ORG_NAME." ".$DEPARTMENT_NAME);
			}
		} else {
			$arr_content[$data_count][cmd_org3] = trim($CMD_ORG3." ".$CMD_ORG4." ".$CMD_ORG5);
			$arr_content[$data_count][new_org3] = trim($NEW_ORG_NAME." ".$NEW_ORG_NAME_1." ".$NEW_ORG_NAME_2);
		}
		$arr_content[$data_count][cmd_old_salary] = $CMD_OLD_SALARY?number_format($CMD_OLD_SALARY):"-";
		
		$arr_content[$data_count][new_position] = $NEW_PL_NAME;
		$arr_content[$data_count][new_pos_no_name] = $NEW_POS_NO_NAME;
		$arr_content[$data_count][new_pos_no] = $NEW_POS_NO;
		$arr_content[$data_count][cmd_salary] = $CMD_SALARY?number_format($CMD_SALARY):"-";
		
		$arr_content[$data_count][cmd_date] = $CMD_DATE;
//		$arr_content[$data_count][cmd_note] = $CMD_NOTE1;
		$arr_content[$data_count][cmd_note1] = $CMD_NOTE1;
		
		$data_count++;
		
		if ($print_order_by==1) {
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][name] = card_no_format($PER_CARDNO,$CARD_NO_DISPLAY);
			$arr_content[$data_count][educate] = $INS_NAME;
                        /*ชื่อย่อวุฒิ*/
			/*if($EN_SHORTNAME[1])
				$arr_content[$data_count][educate] = $EN_SHORTNAME[1];*/
			$data_count++;
		} // end if
		
		if($CMD_NOTE2){
			$arr_content[$data_count][type] = "CONTENT";
			$arr_content[$data_count][cmd_note2] = $CMD_NOTE2;

			$data_count++;
		}
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
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_border_2 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			for($k=0; $k<count($ws_head_line1); $k++) {
				$wsdata_fontfmt_1[] = "B";
				$wsdata_align_1[] = "C";
				$wsdata_border_1[] = "TLRB";
				$wsdata_border_2[] = "";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){				// ใช้ $worksheet->write_string แทน $worksheet->write เพราะข้อมูลตัวเลขไม่แสดงในรายงาน
			$CONTENT_TYPE = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME = $arr_content[$data_count][name];
			$EDUCATE = $arr_content[$data_count][educate];
			$CMD_POS_NO_NAME = $arr_content[$data_count][cmd_pos_no_name];
			$CMD_POS_NO = $arr_content[$data_count][cmd_pos_no];
			$CMD_POSITION = $arr_content[$data_count][cmd_position];
			$CMD_ORG3 = $arr_content[$data_count][cmd_org3];
			$CMD_OLD_SALARY = $arr_content[$data_count][cmd_old_salary];
			
			$NEW_POSITION = $arr_content[$data_count][new_position];
			$NEW_POS_NO_NAME = $arr_content[$data_count][new_pos_no_name];
			$NEW_POS_NO = $arr_content[$data_count][new_pos_no];
			$NEW_ORG3 = $arr_content[$data_count][new_org3];
			$CMD_SALARY = $arr_content[$data_count][cmd_salary];
			
			$CMD_DATE = $arr_content[$data_count][cmd_date];
//			$CMD_NOTE = $arr_content[$data_count][cmd_note];
			$CMD_NOTE1 = $arr_content[$data_count][cmd_note1];
			$CMD_NOTE2 = $arr_content[$data_count][cmd_note2];
			
			if($CONTENT_TYPE=="ORG" || $CONTENT_TYPE=="ORG_1" || $CONTENT_TYPE=="ORG_2"){			//ตาม  rpt_data_promote_e_p_comdtl_xlsN.php
				$ORG_NAME = $arr_content[$data_count][org_name];	
				$NEW_ORG_NAME = $arr_content[$data_count][new_org_name];
	
					if($CONTENT_TYPE=="ORG"){
							$arr_data = (array) null;
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";	
							$arr_data[] = "";	
			
							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}else{
							$arr_data = (array) null;
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**1**>";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**2**>$ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";
							$arr_data[] = "<**3**>$NEW_ORG_NAME";	
							$arr_data[] = "";	
			
							$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
					}
					
							$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
							$xlsRow++;
							$colseq=0;
							for($i=0; $i < count($arr_column_map); $i++) {
								if ($arr_column_sel[$arr_column_map[$i]]==1) {
									if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
									else $ndata = $arr_data[$arr_column_map[$i]];
									$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
									$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
									$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
									$colseq++;
								}
							}
			} // end if ORG

			if($CONTENT_TYPE=="CONTENT"){
					$arr_data = (array) null;
					$arr_data[] = $ORDER;
					$arr_data[] = $NAME;
					$arr_data[] = $EDUCATE;
					$arr_data[] = $CMD_POSITION;
					$arr_data[] = $CMD_ORG3;
					$arr_data[] = $CMD_POS_NO_NAME.$CMD_POS_NO;
					$arr_data[] = $CMD_OLD_SALARY;
					$arr_data[] = $NEW_POSITION;
					$arr_data[] = $NEW_ORG3;
					$arr_data[] = $NEW_POS_NO_NAME.$NEW_POS_NO;
					$arr_data[] = $CMD_SALARY;
					$arr_data[] = $CMD_DATE;
					$arr_data[] = $CMD_NOTE1;
	
					$wsdata_align = array("C", "L", "L", "L", "L", "C", "R", "L", "L", "C", "R", "C", "L");

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
							$colseq++;
						}
					}

				if($CMD_NOTE2){
					$arr_data = (array) null;
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";
					$arr_data[] = "<**1**>หมายเหตุ : $CMD_NOTE2";

					$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");
 
 					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
							$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
							$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
							$colseq++;
						}
					}
				}
			} // end if
		} // end for

		if($COM_NOTE){
			$arr_data = (array) null;
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";
			$arr_data[] = "<**1**>หมายเหตุ : $COM_NOTE";

			$wsdata_align = array("L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L", "L");

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
	
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align[$arr_column_map[$i]], $border, $merge));
					$colseq++;
				}
			}
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
	header("Content-Type: application/x-msexcel; name=\"บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่.xls\"");
	header("Content-Disposition: inline; filename=\"บัญชีแนบท้ายคำสั่งจัดคนลงตามโครงสร้างส่วนราชการใหม่.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>