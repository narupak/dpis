<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004013_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=g.PL_CODE";
		$pl_name = "g.PL_NAME";
		$order_pl = "b.PL_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=g.PN_CODE";
		$pl_name = "g.PN_NAME";
		$order_pl = "b.PN_CODE";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=g.EP_CODE";
		$pl_name = "g.EP_NAME";
		$order_pl = "b.EP_CODE";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=g.TP_CODE";
		$pl_name = "g.TP_NAME";
		$order_pl = "b.TP_CODE";
	} // end if

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
        
        if($search_tr_code){
            $arr_search_condition[] = "(trim(d.TR_CODE)=trim('$search_tr_code'))";
        } 
        if($search_st_date){
            $ARR_START = explode('/',$search_st_date);
            $E_START_DATE = ($ARR_START[2]-543).'-'.$ARR_START[1].'-'.$ARR_START[0];
            $arr_search_condition[] = "SUBSTR(trim(d.TRN_STARTDATE), 1, 10) >= '".$E_START_DATE."'";
        }
        if($search_end_date){
            $ARR_END = explode('/',$search_end_date);
            $E_END_DATE = ($ARR_END[2]-543).'-'.$ARR_END[1].'-'.$ARR_END[0];
            $arr_search_condition[] = "SUBSTR(trim(d.TRN_ENDDATE), 1, 10) <= '".$E_END_DATE."'";
        }

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

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
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานข้อมูล$PERSON_TYPE[$search_per_type]ที่ผ่านการอบรมหลักสูตร $search_tr_name";
	$report_code = "R0413";

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
		$ws_head_line1 = array("ลำดับที่", "ชื่อ - สกุล", "ตำแหน่ง", "ระดับตำแหน่ง", "สังกัด", "ชื่อหลักสูตร" , "รุ่นที่", "หน่วยงานที่จัด", "วันเข้ารับการอบรม", "วันสิ้นสุดการอบรม");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(8,30,30,10,30,30,8,25,30,30);
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
				$buff = explode(",",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				/*$ndata = $buff[0]; 
				$border = $buff[1]; 
				$merge = $buff[2]; 
				$pgrp = $buff[3];*/
				$buff1 = explode("|",$buff[0]);
				$ndata = $buff1[0]; 
				$border = $buff1[1]; 
				$merge = $buff1[2]; 
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
											d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE, TRN_ORG
						 from			(
												(
													(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join $position_table b on $position_join 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on $line_join
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		LEFT(trim(d.TRN_STARTDATE), 10), $order_pl, a.LEVEL_NO ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
											d.TRN_NO, SUBSTR(trim(d.TRN_STARTDATE), 1, 10) as TRN_STARTDATE, SUBSTR(trim(d.TRN_ENDDATE), 1, 10) as TRN_ENDDATE, TRN_ORG,
                                                                                        d.TRN_PASS , e.TR_NAME 
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_TRAINING d, PER_TRAIN e, PER_PRENAME f, $line_table g, PER_LEVEL i
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID and d.TR_CODE=e.TR_CODE(+)
											and a.PN_CODE=f.PN_CODE(+) and $line_join(+) and a.LEVEL_NO=i.LEVEL_NO(+)
											$search_condition
						 order by   SUBSTR(trim(d.TRN_STARTDATE), 1, 10), $order_pl, a.LEVEL_NO "; //,e.TR_SEQ_NO,e.TR_CODE 
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, $pl_name as PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.POSITION_LEVEL, c.ORG_SHORT, c.ORG_NAME,
											d.TRN_NO, LEFT(trim(d.TRN_STARTDATE), 10) as TRN_STARTDATE, LEFT(trim(d.TRN_ENDDATE), 10) as TRN_ENDDATE, TRN_ORG
						 from			(
												(
													(
														(
															(
																(	
																	PER_PERSONAL a 
																	left join $position_table b on $position_join
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) inner join PER_TRAINING d on (a.PER_ID=d.PER_ID)
														) left join PER_TRAIN e on (d.TR_CODE=e.TR_CODE)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join $line_table g on $line_join
											) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											$search_condition
						 order by		LEFT(trim(d.TRN_STARTDATE), 10), $order_pl, a.LEVEL_NO ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		$PT_CODE = trim($data[PT_CODE]);
		$ORG_SHORT = $data[ORG_SHORT];
		$ORG_NAME = $data[ORG_NAME];
		$TRN_NO = $data[TRN_NO];
		$TRN_STARTDATE = show_date_format($data[TRN_STARTDATE],$DATE_DISPLAY);
		$TRN_ENDDATE = show_date_format($data[TRN_ENDDATE],$DATE_DISPLAY);
		$TRN_ORG = $data[TRN_ORG];
                $TR_NAME = $data[TR_NAME];
                 
                $TRN_PASS= $data[TRN_PASS];
                $strTRN_PASS = '';
                if($TRN_PASS==0){
                     $strTRN_PASS = '*';
                }

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME.' '.$strTRN_PASS;
		$arr_content[$data_count][position] = $PL_NAME;
		$arr_content[$data_count][level] = $POSITION_LEVEL;
//		$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
		$arr_content[$data_count][org_name] = $ORG_NAME;
                $arr_content[$data_count][tr_name] = $TR_NAME;
		$arr_content[$data_count][trn_no] = $TRN_NO;
		$arr_content[$data_count][trn_org] = $TRN_ORG;
		$arr_content[$data_count][trn_startdate] = $TRN_STARTDATE;
		$arr_content[$data_count][trn_enddate] = $TRN_ENDDATE;

		$data_count++;
	} // end while
	
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
		
		print_header();

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","L","C","L","L","C","L","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][level];
			$NAME_4 = $arr_content[$data_count][org_name];
                        $NAME_5 = $arr_content[$data_count][tr_name];
			$NAME_6 = $arr_content[$data_count][trn_no];
			$NAME_7 = $arr_content[$data_count][trn_org];
			$NAME_8 = $arr_content[$data_count][trn_startdate];
			$NAME_9 = $arr_content[$data_count][trn_enddate];

			$arr_data = (array) null;
			$arr_data[] = $ORDER;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			$arr_data[] = $NAME_6;
			$arr_data[] = $NAME_7;
			$arr_data[] = $NAME_8;
                        $arr_data[] = $NAME_9;
	
			$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
				
			$xlsRow++;
			$colseq=0;
			for($i=0; $i < count($arr_column_map); $i++) {
				if ($arr_column_sel[$arr_column_map[$i]]==1) {
					if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
					else $ndata = $arr_data[$arr_column_map[$i]];
					$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
					$colseq++;
				}
			}
		} // end for
		
		$arr_data = (array) null;
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
                $arr_data[] = "";
		$arr_data[] = "รวม";
		$arr_data[] = $data_row ."  คน  ";

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
				$colseq++;
			}
		}
                $worksheet->write($xlsRow+1, 1, "หมายเหตุ * ท้ายชื่อ-สกุล หมายถึง ไม่ผ่านการประเมินตามหลักสูตร", set_format("xlsFmtTableDetail", "B", "C", "", 1));
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