<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");
	
	$IMGPATH = "../images/";		
		
	include ("rpt_data_absent_xls_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($DPISDB=="odbc"){	
		$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
										b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
										a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
										c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO
						 from 		PER_PRENAME b
										inner join (
										(	
											(
												(
												PER_PERSONAL a
												left join PER_POSITION c on a.POS_ID = c.POS_ID
											) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
										 ) 	left join PER_POS_TEMP f on a.POT_ID = f.POT_ID
										) on a.PN_CODE = b.PN_CODE
						where		a.PER_ID = $PER_ID ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO 
							  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e,PER_POS_TEMP f
							  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID = f.POT_ID(+)
							  				and a.PER_ID=$PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, f.POT_NO as EMPTEMP_POS_NO,
										b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
										a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID, f.ORG_ID as EMPTEMP_ORG_ID,
										c.PL_CODE, d.PN_CODE, e.EP_CODE,f.TP_CODE, a.PER_SALARY, a.LEVEL_NO
						 from 		PER_PERSONAL a
						 				inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
										left join PER_POSITION c on a.POS_ID = c.POS_ID
										left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
										left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
										left join PER_POS_TEMP f on a.POT_ID = f.POT_ID
						where		a.PER_ID = $PER_ID ";
	} // end if
	if($select_org_structure==1) { 
			$cmd = str_replace("c.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("d.ORG_ID", "a.ORG_ID", $cmd);
			$cmd = str_replace("e.ORG_ID", "a.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();

	$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
	$PER_TYPE = $data[PER_TYPE];
	if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
	elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
	elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];		
	elseif ($PER_TYPE == 4) $ORG_ID = $data[EMPTEMP_ORG_ID];			

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
	if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$ORG_NAME = $data2[ORG_NAME];

	$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
	if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
	if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
	if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];

	$search_condition = "";
         /*http://dpis.ocsc.go.th/Service/node/1141  */
        /*if($search_org_id){
            if($select_org_structure=='0' || empty($select_org_structure) ) $arr_search_condition[] = "(d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or f.ORG_ID=$search_org_id or g.ORG_ID=$search_org_id)";
            if($select_org_structure=='1' )$arr_search_condition[] = "(b.ORG_ID=$search_org_id)";			
        }*/
        if(!$search_abs_approve)	$search_abs_approve=3;
        if($search_abs_approve==4){	
            $arr_search_condition[] = "(a.APPROVE_FLAG in(0,1,2) or a.APPROVE_FLAG is null)";
        }else{
            if($search_abs_approve==3){
                $arr_search_condition[] = "( (a.APPROVE_FLAG = 0 or a.APPROVE_FLAG is null) and ( a.CANCEL_FLAG = 0 or a.CANCEL_FLAG = 8 or a.CANCEL_FLAG = 9 ))";
            } else{
                if($search_abs_approve==1)	$search_abs_approve_tmp = 1; 
                if($search_abs_approve==2)	$search_abs_approve_tmp = 2; 
                $arr_search_condition[] = "(a.APPROVE_FLAG in($search_abs_approve_tmp))";
            }
        }
        if ($search_abs_startdate || $search_abs_enddate) {
            $temp_date = explode("/", $search_abs_startdate);
            $temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
            $temp_date = explode("/", $search_abs_enddate);
            $temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
            $arr_search_condition[] = "( a.ABS_STARTDATE between '$temp_start' and '$temp_end'   
                                         OR  a.ABS_ENDDATE between '$temp_start' and '$temp_end' 
                                         OR '$temp_start' between  a.ABS_STARTDATE and a.ABS_ENDDATE 
                                         or '$temp_end' between  a.ABS_STARTDATE and a.ABS_ENDDATE )";
        }
        if(trim($search_per_name)) $arr_search_condition[] = "(b.PER_NAME like '$search_per_name%')";
        if(trim($search_per_surname)) $arr_search_condition[] = "(b.PER_SURNAME like '$search_per_surname%')";
        if ($search_per_type){
            $arr_search_condition[] = "(trim(b.PER_TYPE) = $search_per_type)";
        }else if ($search_per_type==0)	{	//ทั้งหมด
            $arr_search_condition[] = "(trim(b.PER_TYPE) in('1','2','3','4')) ";
        }
        if ($SESS_PER_ID){
            if($search_onlyme_flag==1){
                $arr_search_condition[] = "(a.PER_ID = $SESS_PER_ID)";
            }else{
                if($SESS_PER_AUDIT_FLAG == 1){
                    $arr_search_condition[] = "(a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID OR a.update_user = $SESS_PER_ID OR 1=1)";
                }else{
                    $arr_search_condition[] = "(a.PER_ID = $SESS_PER_ID or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID)";
                }
            }
        }
        if($search_cancel_flag==1) $arr_search_condition[] = "(a.CANCEL_FLAG = 1)";
		
		$tCon='';
		if($SESS_PER_AUDIT_FLAG == 1){
			$cnt_AuditArray=count($SESS_AuditArray);
			if($cnt_AuditArray>0){
				$tCon="(";
				for ($i=0; $i < $cnt_AuditArray; $i++) {
					if ($i>0)
						$tCon .= " or ";
					$tCon .= "( b.ORG_ID=" .$SESS_AuditArray[$i][0];
					if ($SESS_AuditArray[$i][1] != 0)
						$tCon .= ' and b.ORG_ID_1='. $SESS_AuditArray[$i][1];
					$tCon .= ")";
				}
				$conORGASS=' OR a.PER_ID='.$SESS_PER_ID;
				$tCon=$tCon.$conORGASS;
				//die($tCon.$conORGASS);
				$conditionto_audit ='';
				if(empty($search_org_id)){
					$conditionto_audit =" or a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID ";
				}
		
				$tCon .= " $conditionto_audit )";
				$arr_search_condition[] = $tCon;
			}else{
				$arr_search_condition[] =" ( a.REVIEW1_PER_ID = $SESS_PER_ID or a.REVIEW2_PER_ID = $SESS_PER_ID or a.AUDIT_PER_ID = $SESS_PER_ID or a.APPROVE_PER_ID = $SESS_PER_ID) ";
			}
		
			if($search_org_id && $select_org_structure==0){
				$arr_search_condition[] = "(d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or f.ORG_ID=$search_org_id or g.ORG_ID=$search_org_id)";
				//$arr_search_condition[] = "";
			}elseif($search_org_id && $select_org_structure==1){
				$arr_search_condition[] = "(b.ORG_ID=$search_org_id ) ";
			}
		}elseif($search_org_id){
			if($select_org_structure=='0' || empty($select_org_structure) ) $arr_search_condition[] = "(d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or f.ORG_ID=$search_org_id or g.ORG_ID=$search_org_id)";
			if($select_org_structure=='1' )$arr_search_condition[] = "(b.ORG_ID=$search_org_id)";			
		}elseif($search_department_id){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $search_department_id)";
		}elseif($search_ministry_id){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		} // end if
        /*--------------------------End--------------------------*/
        
	if ($arr_search_condition)		$search_condition = " and " . implode(" and ", $arr_search_condition);

	$company_name = "";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการลา/สาย/ขาด";
	if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "rpt_data_absent_xls";
	
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
		$ws_head_line1 = array("ลำดับ","ประเภทบุคลากร", "$FULLNAME_TITLE","ประเภทการลา","วันที่ส่งใบลา", "$FROM_DATE_TITLE","$TO_DATE_TITLE", "จำนวนวัน","ตรวจสอบ", "ความเห็น", "ความเห็น", "อนุญาต", "วันที่อนุญาต","ชื่อผู้อนญาต","ยกเลิก");
		$ws_head_line2 = array("","","","","","","","","","(ขั้นต้น)","(เหนือขึ้นไป)","","","","");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL","TRL");
		$ws_border_line2 = array("LBR","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL","RBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(8,15,30,14,25,14,14,10,12,12,12,14,30,12);
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
		global $ws_head_line1, $ws_head_line2, $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2, $ws_fontfmt_line1, $ws_fontfmt_line2;
		global $ws_headalign_line1, $ws_headalign_line2, $ws_width;

/****		
		global $SEQ_NO_TITLE,$FULLNAME_TITLE,$FROM_DATE_TITLE,$TO_DATE_TITLE;

		$worksheet->set_column(0, 0, 8);
		$worksheet->set_column(1, 1, 15);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 14);
		$worksheet->set_column(4, 4, 14);
		$worksheet->set_column(5, 5, 14);
		$worksheet->set_column(6, 6, 10);
		$worksheet->set_column(7, 7, 12);
		$worksheet->set_column(8, 8, 12);
		$worksheet->set_column(9, 9, 12);
		$worksheet->set_column(10, 10,  12);
		$worksheet->set_column(11, 11, 12);
		$worksheet->set_column(12, 12, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 1, "ประเภทบุคลากร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 2, "$FULLNAME_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 3, "ประเภทการลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 4, "วันที่ส่งใบลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 5, "$FROM_DATE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		$worksheet->write($xlsRow, 6, "$TO_DATE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 7, "จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 8, "ตรวจสอบ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 9, "ความเห็น (ขั้นต้น)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 10, "ความเห็น (เหนือขึ้นไป)", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 11, "อนุญาต", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		 $worksheet->write($xlsRow, 12, "ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
****/

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
				$worksheet->write($xlsRow, $colseq, $ws_head_line1[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line2[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		
        
       
	if($DPISDB=="odbc"){
		$cmd = " select 		ABS_ID, b.PER_TYPE, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
						a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
						a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c 
						 where 		a.PER_ID=b.PER_ID(+) and a.AB_CODE=c.AB_CODE(+)
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";		
	}elseif($DPISDB=="oci8"){	
						$cmd = " select 		ABS_ID, b.PER_TYPE, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE,APPROVE_DATE, ABS_DAY, ABS_LETTER,
						a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
						a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c ,
                                                 PER_POSITION d, PER_POS_EMP e, PER_POS_EMPSER f, PER_POS_TEMP g
						 where 		a.PER_ID=b.PER_ID(+) and a.AB_CODE=c.AB_CODE(+)
                                                 and b.POS_ID=d.POS_ID(+) and b.POEM_ID=e.POEM_ID(+) and b.POEMS_ID=f.POEMS_ID(+) and b.POT_ID=g.POT_ID(+) 
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	}elseif($DPISDB=="mysql"){
		$cmd = " select 		ABS_ID, b.PER_TYPE, a.PER_ID, a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_ENDDATE, ABS_DAY, ABS_LETTER,
						a.APPROVE_FLAG, a.APPROVE_PER_ID,a.AUDIT_FLAG, a.AUDIT_PER_ID, a.CANCEL_FLAG,
						a.REVIEW1_FLAG, a.REVIEW1_PER_ID,a.REVIEW2_FLAG, a.REVIEW2_PER_ID, a.CREATE_DATE, b.PER_CARDNO
						 from 			PER_ABSENT a, PER_PERSONAL b, PER_ABSENTTYPE c 
						 where 		a.PER_ID=b.PER_ID(+) and a.AB_CODE=c.AB_CODE(+)
											$search_condition 
						 order by 	PER_NAME, PER_SURNAME ";	
	} // end if
	$count_data = $db_dpis->send_cmd($cmd);
        //die('<pre>'.$cmd);
	//$db_dpis->show_error();
	$data_count = $data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$TMP_ABS_ID = $data[ABS_ID];
		
		$TMP_PER_TYPE = $data[PER_TYPE];
		$TMP_PER_ID = $data[PER_ID];
		$TMP_PER_CARDNO = $data[PER_CARDNO];
		$cmd = " select b.PN_CODE, b.PER_NAME, b.PER_SURNAME from PER_PERSONAL b 
						where  b.PER_ID = $TMP_PER_ID";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_PN_CODE = $data2[PN_CODE];
		$TMP_PER_NAME = $data2[PER_NAME];
		$TMP_PER_SURNAME = $data2[PER_SURNAME];

		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_PN_NAME = $data2[PN_NAME];
		$APPROVE_DATE = $data[APPROVE_DATE];
		$TMP_PER_NAME = $TMP_PN_NAME.$TMP_PER_NAME." ".$TMP_PER_SURNAME;
		
		$TMP_CREATE_DATE = $TMP_CREATE_TIME= $TMP_ABS_STARTDATE = $TMP_ABS_ENDDATE = "";
		$temp_date = explode("-", trim($data[ABS_STARTDATE]));
		$TMP_ABS_STARTDATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
		$TMP_ABS_STARTDATE_CHECK = ($temp_date[0])."-".$temp_date[1]."-".substr($temp_date[2],0,2) ;
		$temp_date = explode("-", trim($data[ABS_ENDDATE]));
		$TMP_ABS_ENDDATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
		$temp_date = explode("-", trim($data[APPROVE_DATE]));
		$TMP_APPROVE_DATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
		if($APPROVE_DATE=="NULL"){
			$TMP_APPROVE_DATE = "";
		}
		
		
		//die($APPROVE_DATE);
		
		$ABS_CONF_YEAR = $temp_date[0];
		$ABS_CONF_MONTH = $temp_date[1];
		if($data[CREATE_DATE]!="" && $data[CREATE_DATE]!="NULL"){	
			$TMP_CREATE_DATE  = substr($data[CREATE_DATE],0,10);
			if($TMP_CREATE_DATE){
				if(substr($data[CREATE_DATE],12,strlen($data[CREATE_DATE]))){
					$TMP_CREATE_TIME  = substr($data[CREATE_DATE],11,strlen($data[CREATE_DATE]));
				}
				$temp_date = explode("-", trim($TMP_CREATE_DATE));
				$TMP_CREATE_DATE = substr($temp_date[2],0,2) ."/". $temp_date[1] ."/". ($temp_date[0] + 543);
				if($TMP_CREATE_TIME)	$TMP_CREATE_DATE = 	$TMP_CREATE_DATE." (".$TMP_CREATE_TIME.")";
			}
		}
		
		$TMP_ABS_DAY = trim($data[ABS_DAY]);
		$TMP_AB_CODE = trim($data[AB_CODE]);
		$TMP_AB_NAME = trim($data[AB_NAME]);		
		$TMP_ABS_LETTER = trim($data[ABS_LETTER]);
		if ($TMP_ABS_LETTER == 1) 				$ABS_LETTER_STR = "ยังไม่ได้ส่ง";
		elseif ($TMP_ABS_LETTER == 2) 		$ABS_LETTER_STR = "ถูกต้อง";
		elseif ($TMP_ABS_LETTER == 3) 		$ABS_LETTER_STR = "ไม่ถูกต้อง";
		if(trim($TMP_AB_CODE=="10")) $ABS_LETTER_STR = "-";

		$TMP_REVIEW1_FLAG = trim($data[REVIEW1_FLAG]);
		$TMP_REVIEW1_PER_ID = trim($data[REVIEW1_PER_ID]);
		
		$TMP_REVIEW2_FLAG = trim($data[REVIEW2_FLAG]);
		$TMP_REVIEW2_PER_ID = trim($data[REVIEW2_PER_ID]);

		$TMP_AUDIT_FLAG = trim($data[AUDIT_FLAG]);
		$TMP_AUDIT_PER_ID = trim($data[AUDIT_PER_ID]);

		$TMP_APPROVE_FLAG = trim($data[APPROVE_FLAG]);
		$TMP_APPROVE_PER_ID = trim($data[APPROVE_PER_ID]);

		$TMP_CANCEL_FLAG = $data[CANCEL_FLAG];

		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_REVIEW1_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_REVIEW1_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
		
		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_REVIEW2_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_REVIEW2_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];

		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_AUDIT_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_AUDIT_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];

		$cmd = " select b.PN_NAME, a.PER_NAME, a.PER_SURNAME from PER_PERSONAL a, PER_PRENAME b where a.PER_ID=$TMP_APPROVE_PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_APPROVE_PER_NAME = $data2[PN_NAME].$data2[PER_NAME]." ".$data2[PER_SURNAME];
	 if($TMP_PER_TYPE ==1) $TMP_PER_TYPE_NAME = "ข้าราชการ";
	  else  if($TMP_PER_TYPE ==2) $TMP_PER_TYPE_NAME = "ลูกจ้างประจำ";
	  else  if($TMP_PER_TYPE ==3) $TMP_PER_TYPE_NAME = "พนักงานราชการ";
	  else  if($TMP_PER_TYPE ==4) $TMP_PER_TYPE_NAME = "ลูกจ้างชั่วคราว"; 	
		
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = $data_row;
		$arr_content[$data_count][abs_per_type] = $TMP_PER_TYPE_NAME;
		$arr_content[$data_count][abs_per_name] = $TMP_PER_NAME;
		$arr_content[$data_count][abs_name] = $TMP_AB_NAME;
		$arr_content[$data_count][abs_create_date] = $TMP_CREATE_DATE;
		$arr_content[$data_count][abs_startdate] = $TMP_ABS_STARTDATE;
		$arr_content[$data_count][abs_enddate] = ((trim($TMP_AB_CODE) != "10")?$TMP_ABS_ENDDATE:"-");
		$arr_content[$data_count][abs_day] = $TMP_ABS_DAY;
		$arr_content[$data_count][abs_audit_flag] = $TMP_AUDIT_FLAG; 
		$arr_content[$data_count][abs_review1_flag] = $TMP_REVIEW1_FLAG;
		$arr_content[$data_count][abs_review2_flag] = $TMP_REVIEW2_FLAG;
		$arr_content[$data_count][abs_approve_flag] = $TMP_APPROVE_FLAG;
		$arr_content[$data_count][approve_date] 	= $TMP_APPROVE_DATE;
		$arr_content[$data_count][approve_per_name] = $TMP_APPROVE_PER_NAME;
		$arr_content[$data_count][abs_cancel_flag] 	= $TMP_CANCEL_FLAG;

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	$xlsRow = 0;
/****	
	$arr_title = explode("||", $report_title);
	for($i=0; $i<count($arr_title); $i++){
		$xlsRow = $i;
		$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	if($company_name){
		$xlsRow++;
		$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
	} // end if

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, $ORG_NAME, set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, (trim($search_abs_month)?("เดือน  ".$month_full[$search_abs_month][TH]):""), set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, (trim($search_abs_year)?"ปี  $search_abs_year":""), set_format("xlsFmtTableDetail", "B", "L", "", 0));

	$xlsRow++;
	$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 1, "$PER_NAME", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
	$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableDetail", "B", "L", "", 0));
****/

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
		
		print_header();
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","L","L","L","L","L","C","C","C","C","C","L","L","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล		

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$ORDER = $arr_content[$data_count][order];
			$TMP_PER_TYPE_NAME = $arr_content[$data_count][abs_per_type];
			$TMP_PER_NAME = $arr_content[$data_count][abs_per_name];
			$TMP_AB_NAME = $arr_content[$data_count][abs_name];
			$TMP_CREATE_DATE = $arr_content[$data_count][abs_create_date];
			$TMP_ABS_STARTDATE = $arr_content[$data_count][abs_startdate];
			$TMP_ABS_ENDDATE = $arr_content[$data_count][abs_enddate];
			$TMP_ABS_DAY = $arr_content[$data_count][abs_day];
			$TMP_AUDIT_FLAG = $arr_content[$data_count][abs_audit_flag]; 
			$TMP_REVIEW1_FLAG = $arr_content[$data_count][abs_review1_flag];
			$TMP_REVIEW2_FLAG = $arr_content[$data_count][abs_review2_flag];
			$TMP_APPROVE_FLAG = $arr_content[$data_count][abs_approve_flag];
			$TMP_APPROVE_DATE	= $arr_content[$data_count][approve_date];
			$TMP_APPROVE_PER_NAME =	$arr_content[$data_count][approve_per_name];
			$TMP_CANCEL_FLAG = $arr_content[$data_count][abs_cancel_flag];
/***			
			$TMP_AUDIT_FLAG = ($TMP_AUDIT_FLAG==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$TMP_REVIEW1_FLAG = ($TMP_REVIEW1_FLAG==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$TMP_REVIEW2_FLAG = ($TMP_REVIEW2_FLAG==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$TMP_APPROVE_FLAG = ($TMP_APPROVE_FLAG ==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
			$TMP_CANCEL_FLAG = ($TMP_CANCEL_FLAG==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";		
***/			
			//____________________--
			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $TMP_PER_TYPE_NAME;
			$arr_data[] = $TMP_PER_NAME;
			$arr_data[] = $TMP_AB_NAME;
			$arr_data[] = $TMP_CREATE_DATE;
			$arr_data[] = $TMP_ABS_STARTDATE;
			$arr_data[] = $TMP_ABS_ENDDATE;
			$arr_data[] = $TMP_ABS_DAY;
			$arr_data[] = ($TMP_AUDIT_FLAG==1 ? "<*img*".$IMGPATH."../images/checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");	
			$arr_data[] = ($TMP_REVIEW1_FLAG==1 ? "<*img*".$IMGPATH."../images/checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");	
			$arr_data[] = ($TMP_REVIEW2_FLAG==1 ? "<*img*".$IMGPATH."../images/checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");	
			$arr_data[] = ($TMP_APPROVE_FLAG==1 ? "<*img*".$IMGPATH."../images/checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");
			$arr_data[] = $TMP_APPROVE_DATE;
			$arr_data[] = $TMP_APPROVE_PER_NAME;
			$arr_data[] = ($TMP_CANCEL_FLAG==1 ? "<*img*".$IMGPATH."../images/checkbox_check.BMP*img*>" : "<*img*".$IMGPATH."checkbox_blank.BMP*img*>");	

			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$k]], $wsdata_colmerge_1[$arr_column_map[$k]], $pgrp, ($colseq == $colshow_cnt-1)));
					$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3]; $img = $buff[4];
					if ($img) {
//						echo "1..$img<br>";
						$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
//						$worksheet->insert_bitmap($xlsRow, $colseq, $img, (($ws_width[$arr_column_map[$k]]/2)-0), 6, 1, 0.5);
						$worksheet->insert_bitmap($xlsRow, $colseq, $img, 50, 3, 1, 0.5);
						/*
					if(array_search($REPORT_ORDER, $arr_rpt_order) !== false  && array_search($REPORT_ORDER, $arr_rpt_order) == 0 && count($arr_rpt_order) > 1)
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						*/						
					} else {
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						//$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$k]], $wsdata_align_1[$arr_column_map[$k]], $border, $merge));
					}
					$colseq++;
				}
			}
			
/*****
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit($ORDER):$ORDER), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$TMP_PER_TYPE_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2,"$TMP_PER_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3,"$TMP_AB_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4,convert2thaidigit($TMP_CREATE_DATE), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5,convert2thaidigit($TMP_ABS_STARTDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6,convert2thaidigit($TMP_ABS_ENDDATE), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 7,(($TMP_ABS_DAY)?convert2thaidigit($TMP_ABS_DAY):""), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, 8, $TMP_AUDIT_FLAG, 35, 4, 1, 0.8);
			$worksheet->insert_bitmap($xlsRow, 9, $TMP_REVIEW1_FLAG, 35, 4, 1, 0.8);
			$worksheet->insert_bitmap($xlsRow, 10, $TMP_REVIEW2_FLAG, 35, 4, 1, 0.8);
			$worksheet->insert_bitmap($xlsRow, 11, $TMP_APPROVE_FLAG, 35, 4, 1, 0.8);
			$worksheet->insert_bitmap($xlsRow, 12, $TMP_CANCEL_FLAG, 35, 4, 1, 0.8);
*****/
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
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"รายงานการลา/สาย/ขาด.xls\"");
	header("Content-Disposition: inline; filename=\"รายงานการลา/สาย/ขาด.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>