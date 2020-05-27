<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("../report/rpt_function.php");

	include ("rpt_R010015_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
	//ผู้ดำรงตำแหน่งประเภทบริหาร (ระดับต้น, ระดับสูง)
	$arr_search_condition[] = "(a.LEVEL_NO in (". stripslashes(implode($search_per_level, ",")) ."))";

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;
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
	if(in_array("PER_ORG_TYPE_2", $list_type) ){
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
	if(in_array("PER_ORG", $list_type) ){
		$list_type_text = "";
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure == 0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure == 1)  $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
	}
	if(in_array("PER_LINE", $list_type) ){
		// สายงาน
		$list_type_text = "";
		if($search_per_type==1 && trim($search_pl_code)){
			$search_pl_code = trim($search_pl_code);
			$arr_search_condition[] = "(trim(b.PL_CODE)='$search_pl_code')";
			$list_type_text .= "$search_pl_name";
		}elseif($search_per_type==2 && trim($search_pn_code)){
			$search_pn_code = trim($search_pn_code);
			$arr_search_condition[] = "(trim(b.PN_CODE)='$search_pn_code')";
			$list_type_text .= "$search_pn_name";
		} // end if
		elseif($search_per_type==3 && trim($search_ep_code)){
			$search_ep_code = trim($search_ep_code);
			$arr_search_condition[] = "(trim(b.EP_CODE)='$search_ep_code')";
			$list_type_text .= "$search_ep_name";
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type) ){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
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
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "รายชื่อผู้ดำรงตำแหน่งประเภทบริหาร";
	if(trim($search_per_level)){ 
		for($i=0; $i < count($search_per_level); $i++){
			$cmd=" select LEVEL_NAME from PER_LEVEL where LEVEL_NO=$search_per_level[$i] order by LEVEL_SEQ_NO ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$per_level_name = $data[LEVEL_NAME];
			$report_title .= str_replace("ประเภทบริหาร", "", $per_level_name);
		} //end ffor
	} //end if
	$report_code = "R1015";

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
		$ws_head_line1 = array("ส่วนราชการ     ชื่อ - สกุล","ตำแหน่ง","ตำแหน่งประเภท","วันเลื่อนระดับ","วันดำรงตำแหน่งปัจจุบัน","วัน เดือน ปีเกิด","วันเกษียณอาย (1 ต.ค.)","อบรม");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR","TLBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C");
		$ws_width = array(40,50,15,15,15,15,15,60);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align;
		global $ws_head_line1, $ws_colmerge_line1;
		global $ws_border_line1, $ws_fontfmt_line1;
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
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME,
								MIN(LEFT(trim( j.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL
				   from	 (	
				   				(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
				 			) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL, m.LEVEL_SEQ_NO
				   order by		d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO,  b.PT_CODE desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MAX(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 	SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME,
								MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL
				   from		PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_TYPE g, PER_MGT h, 
				   				PER_PRENAME i, PER_POSITIONHIS j, PER_DECORATEHIS k, PER_DECORATION l, PER_LEVEL m
				   where	a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PT_CODE=g.PT_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+)
								and a.PER_ID=j.PER_ID(+) and a.PER_ID=k.PER_ID(+) and k.DC_CODE=l.DC_CODE(+) and l.DC_TYPE in (1, 2)
								and a.LEVEL_NO=m.LEVEL_NO(+)
								$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL, m.LEVEL_SEQ_NO
				   order by		d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO,  b.PT_CODE desc, MIN(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, 
				   				SUBSTR(trim(a.PER_STARTDATE), 1, 10), MAX(l.DC_ORDER), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,  LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
								a.PER_SALARY, b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, d.ORG_ID_REF as MINISTRY_ID, e.ORG_NAME as MINISTRY_NAME,
								MIN(LEFT(trim( j.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL
				   from  (		
				   				(
									(
										(
											(
												(
													(
														(
															(
																(
																	(
																		PER_PERSONAL a
																		inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
																	) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
																) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
															) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
														) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
													) inner join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
												) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
											) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
										) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
									) left join  PER_DECORATEHIS k on (a.PER_ID=k.PER_ID)
								) left join PER_DECORATION l on (k.DC_CODE=l.DC_CODE)
							) left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where		l.DC_TYPE in (1, 2)
				   				$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, b.PT_CODE, g.PT_NAME, f.PL_NAME, h.PM_SEQ_NO, h.PM_NAME,
								LEFT(trim(a.PER_BIRTHDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10), LEFT(trim(a.PER_STARTDATE), 10), a.PER_SALARY,
								b.ORG_ID, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_NAME, m.POSITION_TYPE, m.POSITION_LEVEL, m.LEVEL_SEQ_NO
				   order by		d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, h.PM_SEQ_NO,  b.PT_CODE desc, MIN(LEFT(trim(j.POH_EFFECTIVEDATE), 10)), a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), MAX(l.DC_ORDER), LEFT(trim(a.PER_BIRTHDATE), 10) ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
if($f_all){			
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
}		
		} // end if
		
		if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
			
			$arr_content[$data_count][type] = "DEPARTMENT";
			$arr_content[$data_count][name] = $DEPARTMENT_NAME;

			$data_row = 0;
			$data_count++;
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$LEVEL_NAME =  trim($data[LEVEL_NAME]);
		$POSITION_TYPE =  trim($data[POSITION_TYPE]);
		$POSITION_LEVEL =  trim($data[POSITION_LEVEL]);
		
		$PL_NAME = $data[PL_NAME];
		$PM_NAME = $data[PM_NAME];

		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$PER_AGE = floor(date_difference(date("Y-m-d"), $PER_BIRTHDATE, "year"));
		if(trim($PER_BIRTHDATE)){		
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		} // end if

		//หาวันเกษียณอายุ
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		$PER_WORKAGE = floor(date_difference(date("Y-m-d"), $PER_STARTDATE, "year"));
		if(trim($PER_STARTDATE)){	
			$PER_STARTDATE = show_date_format($PER_STARTDATE,$DATE_DISPLAY);
		} // end if
		
		//หาวันเลื่อนระดับ
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		$PER_SALARY = $data[PER_SALARY];
		
		//หาหลักสูตรที่อบรม
		$cmd="select b.TR_NAME from PER_TRAINING a, PER_TRAIN b where a.TR_CODE=b.TR_CODE(+) and a.PER_ID=$PER_ID order by a.TRN_STARTDATE";
		$db_dpis2->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//	$db_dpis2->show_error();
		while($data2 = $db_dpis2->get_array()){
			$PER_TRAINING[$PER_ID] .= str_replace("หลักสูตร", "", $data2[TR_NAME]).", ";
		}
		$PER_TRAINING[$PER_ID]=substr($PER_TRAINING[$PER_ID],0, -2);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row." . str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = (trim($PM_NAME)?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL. (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$POSITION_LEVEL) . (trim($PM_NAME)?")":"");
		$arr_content[$data_count][position_type] = $POSITION_TYPE;

		$arr_content[$data_count][leveldate] = 		$POH_EFFECTIVEDATE;		//วันที่เลื่อนระดับ
		$arr_content[$data_count][effectivedate] = $PER_STARTDATE;		//วันดำรงตำแหน่ง
		$arr_content[$data_count][birthdate] = 			$PER_BIRTHDATE;		//วัน เดือน ปีเกิด
		$arr_content[$data_count][retiredate] = 		$PER_RETIREDATE;	//วันเกษียณอายุ
		$arr_content[$data_count][training] = 			$PER_TRAINING[$PER_ID];			//หลักสูตรที่อบรม

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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("L","L","C","C","C","C","C","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$POSITION = $arr_content[$data_count][position];
			$POSITION_TYPE = $arr_content[$data_count][position_type];
			$LEVELDATE=$arr_content[$data_count][leveldate];
			$EFFECTIVEDATE=$arr_content[$data_count][effectivedate];
			$BIRTHDATE=$arr_content[$data_count][birthdate];
			$RETIREDATE=$arr_content[$data_count][retiredate];
			$TRAINING = $arr_content[$data_count][training];
			
			if($REPORT_ORDER == "MINISTRY"){
            	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
				$arr_data[] ="";
		    	$arr_data[] ="";

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
			}elseif($REPORT_ORDER == "DEPARTMENT"){
    	     	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
                $arr_data[] ="";		    	
				$arr_data[] ="";		    

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
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $POSITION;
				$arr_data[] = $POSITION_TYPE;
				$arr_data[] = $LEVELDATE;
				$arr_data[] = $EFFECTIVEDATE;
				$arr_data[] = $BIRTHDATE;
				$arr_data[] = $RETIREDATE;
				$arr_data[] = $TRAINING;

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