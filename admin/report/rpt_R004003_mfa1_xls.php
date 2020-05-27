<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R004003_mfa1_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$search_per_type = 1;
	$position_table = "PER_POSITION";
	$position_join = "a.POS_ID=b.POS_ID";
	$line_table = "PER_LINE";
	$line_join = "b.PL_CODE=f.PL_CODE";
	$pl_code = "b.PL_CODE";
	$pl_name = "f.PL_NAME";
	$position_no = "b.POS_NO_NAME, b.POS_NO";
	$line_search_code = trim($search_pl_code);
	$line_search_name = trim($search_pl_name);
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	
	
	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$search_level_no = trim($search_level_no);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(rtrim(a.LEVEL_NO) = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}else if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			} // end if
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($pl_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
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
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);	

	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่ดำรงตำแหน่งในระดับ ". $level_name ." เรียงตามลำดับอาวุโส";
	$report_code = "R0403";

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
		$ws_head_line1 = array("อาวุโส", "ชื่อ - สกุล", "ระดับ 10", "ระดับ 9", "ระดับ 8", "เงินเดือน", "เข้ารับราชการ", "เครื่องราชฯ", "วันเดือนปีเกิด", "เกษียณ", "สังกัด (อดีต - ปัจจุบัน)", "การอบรม");
		$ws_colmerge_line1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(5,30,10,10,10,8,10,15,10,8,60,25);
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
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	//---ต้องมีระดับตำแหน่งของตัวมันเองด้วย ในกรณีเข้ามาถึงก็เป็นระดับตำแหน่งนี้เลย ไม่มีระดับก่อนหน้า
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O1','O2');
	$L["O3"]=array('08','07','06','O2','O3');
	$L["O4"]=array('09','08','O3','O4');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K1','K2');
	$L["K3"]=array('08','07','K2','K3');
	$L["K4"]=array('09','08','K3','K4');
	$L["K5"]=array('10','09','K4','K5');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D1','D2');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M1','M2');
	//----วน loop ตามระดับตำแหน่ง ที่เลือกมา -------------
	$arrkeep = array();
	for($i=0; $i < count($L[$search_level_no]); $i++){	
		$index=$L[$search_level_no][$i];		//$index=level no
	if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)), a.PER_SALARY desc, MIN(i.DC_ORDER),
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";   
	}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select 	distinct e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
											MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, 
											SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
											a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
								from 	PER_PERSONAL a,  $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, $line_table f, PER_TYPE g, PER_DECORATEHIS h, 
											PER_DECORATION i, PER_LEVEL j 
								where $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID(+) and $line_join(+) 
											and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
								$search_condition and e.POH_LEVEL_NO='$index' 											
								 group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
											 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE, a.DEPARTMENT_ID
								 order by MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)), a.PER_SALARY desc, MIN(i.DC_ORDER),
											SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)  "; 
								//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
								/*	order by MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MIN(i.DC_ORDER),
											SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)  ";  	*/
	}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, 
												LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	
												MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, MIN(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME, c.ORG_SHORT, a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)), a.PER_SALARY desc, MIN(i.DC_ORDER),
								LEFT(trim(a.PER_STARTDATE),10), LEFT(trim(a.PER_BIRTHDATE),10) ";   
	}		
	//สร้าง query ใหม่ สำหรับข้อมูล record เดียว
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		//$cmd = str_replace("b.ORG_ID_1", "a.ORG_ID_1", $cmd);
		//$cmd = str_replace("b.ORG_ID_2", "a.ORG_ID_2", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
//echo "$cmd<br>===============<br>";
		//---สร้าง query ของแต่ละตัว แล้ววนข้อมูลมาอีกครั้ง
		$count_data = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){		
			if($PER_ID == $data[PER_ID]) continue;
			//$person_count++;		

			$PER_ID = $data[PER_ID];
			//---เก็บ PER_ID ของคนนั้นลงใน array ถ้ามีชื่อในระดับตำแหน่งที่เรียงตามลำดับแรกมาแล้ว อันถัดไปไม่นำมาเก็บอีกต่อไปแล้ว
			if(!in_array($PER_ID,$arrkeep)){
				$arrkeep[] = $PER_ID;
				//=================================
				if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
					$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
				}
				
				$ARRSORTLEVEL['per_id'][] = $PER_ID;
				
				$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
				$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
				$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
				$POS_NO[$PER_ID] = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
				$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
				$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
				$LEVEL_NOHIS[$PER_ID] = trim($data[LEVEL_NOHIS]);
				
				if ($search_level_no=="M2") {
					$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='09' order by POH_EFFECTIVEDATE ";		
					$count_data = $db_dpis2->send_cmd($cmd);
				//	$db_dpis2->show_error();
		
					if (!$count_data) {
						$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='09' order by POH_EFFECTIVEDATE ";		
						$db_dpis2->send_cmd($cmd);
					}
		
					//เอาตน.ที่วันที่ว่าง ไว้ท้ายสุด
					// เริ่มต้น ให้หาวันที่น้อยที่สุดก่อน แล้วมาหา query ถัดไปของคนนั้น
					$data2 = $db_dpis2->get_array();
					$POH_EFFECTIVEDATE = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
					$POH_EFFECTIVEDATE9[$PER_ID] = show_date_format($POH_EFFECTIVEDATE,4);

					$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='08' order by POH_EFFECTIVEDATE ";		
					$count_data = $db_dpis2->send_cmd($cmd);
				//	$db_dpis2->show_error();
		
					if (!$count_data) {
						$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(POH_LEVEL_NO)='08' order by POH_EFFECTIVEDATE ";		
						$db_dpis2->send_cmd($cmd);
					}
		
					//เอาตน.ที่วันที่ว่าง ไว้ท้ายสุด
					// เริ่มต้น ให้หาวันที่น้อยที่สุดก่อน แล้วมาหา query ถัดไปของคนนั้น
					$data2 = $db_dpis2->get_array();
					$POH_EFFECTIVEDATE = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
					$POH_EFFECTIVEDATE8[$PER_ID] = show_date_format($POH_EFFECTIVEDATE,4);
				}
				$ARRSORTLEVEL['per_sortlevel'][] = $LEVEL_NOHIS[$PER_ID];		//--------
				
				$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
				$POSITION_LEVEL[$PER_ID] = trim($data[POSITION_LEVEL]);
				$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
				$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
				if (trim($data[ORG_SHORT]))
					$ORG_NAME[$PER_ID] = trim($data[ORG_SHORT]);
				else
					$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
				if ($ORG_NAME[$PER_ID]=="-") $ORG_NAME[$PER_ID] = "";
				$DEPARTMENT_ID=trim($data[DEPARTMENT_ID]);
				$ORG_ID_1=trim($data[ORG_ID_1]);
				$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
				$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
				if(trim($POH_EFFECTIVEDATE)){
					//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.POH_LEVEL_NO และ e.POH_EFFECTIVEDATE)
					$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;		//$PER_EFFECTIVEDATE[$index][$PER_ID] =$POH_EFFECTIVEDATE;
					$POH_EFFECTIVEDATE2[$PER_ID] = show_date_format($POH_EFFECTIVEDATE,4);
				} // end if	
					
				$ARRSORTLEVEL['poh_effectivedate'][] = $POH_EFFECTIVEDATE;		//--------
					
				$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);
				
				$ARRSORTLEVEL['per_salary'][] = $PER_SALARY[$PER_ID];	//--------
				
				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
				if(trim($PER_BIRTHDATE)){
					$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
					$PER_BIRTHDATE2[$PER_ID] = show_date_format($PER_BIRTHDATE,4);
				} // end if	
					
				$ARRSORTLEVEL['per_birthdate'][] = $PER_BIRTHDATE;	//--------
					
				$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
				if(trim($PER_STARTDATE)){
					$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
					$PER_STARTDATE2[$PER_ID] = show_date_format($PER_STARTDATE,4);
				} // end if	
				
				$ARRSORTLEVEL['per_startdate'][] = $PER_STARTDATE;	//--------
				
				if(trim($PER_RETIREDATE)){
					$PER_RETIREDATE2[$PER_ID] = show_date_format($PER_RETIREDATE,1);
				} // end if	
			
				$where = "";
				if ($search_level_no=="M2" || $search_level_no=="M1") $where = " and (TRN_FLAG=1) ";
				$cmd = " select	TRN_SHORTNAME from	PER_TRAINING 
						   where	PER_ID=$PER_ID $where
						   order by 	TRN_STARTDATE ";
				$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
				$PER_TRAINING[$PER_ID] = "";
				while($data2 = $db_dpis2->get_array()){
					if($PER_TRAINING[$PER_ID]) $PER_TRAINING[$PER_ID] .= "/";
					$PER_TRAINING[$PER_ID] .= trim($data2[TRN_SHORTNAME]);
				} // end while
			
				if($DPISDB=="odbc"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a, PER_DECORATION b
							   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2) and a.DC_CODE=b.DC_CODE(+)
							   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE in (1,2)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);
				
				$ARRSORTLEVEL['per_decorate'][] = $PER_DECORATE[$PER_ID];	//--------
				
				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$DEPT_NAME[$PER_ID]=trim($data_org[ORG_NAME]);

				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$ORG_ID_1_NAME[$PER_ID]=trim($data_org[ORG_NAME]);
				if ($ORG_ID_1_NAME[$PER_ID]=="-") $ORG_ID_1_NAME[$PER_ID] = "";

				$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";		
				$count_data = $db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
	
				if (!$count_data) {
					$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and POH_LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";		
					$db_dpis2->send_cmd($cmd);
				}
	
				//เอาตน.ที่วันที่ว่าง ไว้ท้ายสุด
				// เริ่มต้น ให้หาวันที่น้อยที่สุดก่อน แล้วมาหา query ถัดไปของคนนั้น
				$data2 = $db_dpis2->get_array();
				$CURR_EFFECTIVEDATE_SORT[$LEVEL_NOHIS][$PER_ID] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
				
				$ARRSORTLEVEL['curr_effectivedate'][] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);	//--------
				
				$CURR_EFFECTIVEDATE[$PER_ID] = show_date_format(trim($data2[POH_EFFECTIVEDATE]),4);
//				echo "==>  $LEVEL_NOHIS / $PER_ID = ".substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
			} 
		} // end while
	} //end for
	
//	print("<pre>");	print_r($CURR_EFFECTIVEDATE);	print("</pre>");

//	$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');

	array_multisort($ARRSORTLEVEL['curr_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['poh_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_ASC,
              					$ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_ASC,
              					$ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
								);

	//แสดงรายการทั้งหมด
	$data_count = 0;
	$person_count = 0;
	//foreach($PER_EFFECTIVEDATE as $key=>$value){		//[$LEVEL_NOHIS][$PER_ID]
	foreach($ARRSORTLEVEL['per_id'] as $key=>$value){
				$PER_ID=trim($value);
					
					$person_count++;		
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][name] = $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
					$arr_content[$data_count][position] = $PL_NAME[$PER_ID] . $POSITION_LEVEL[$PER_ID] . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
					$arr_content[$data_count][level] = $LEVEL_NAME[$PER_ID] . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
//					$arr_content[$data_count][org] = trim($ORG_ID_1_NAME[$PER_ID] ." ". $ORG_NAME[$PER_ID]);
					$arr_content[$data_count][org] = $ORG_NAME[$PER_ID];
					$arr_content[$data_count][posno] = $POS_NO[$PER_ID];
					$arr_content[$data_count][training] = $PER_TRAINING[$PER_ID];
					$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	
					$arr_content[$data_count][poh_effectivedate9] = $POH_EFFECTIVEDATE9[$PER_ID];	
					$arr_content[$data_count][poh_effectivedate8] = $POH_EFFECTIVEDATE8[$PER_ID];	
					$arr_content[$data_count][curr_effectivedate] = $CURR_EFFECTIVEDATE[$PER_ID];	
					//หาระดับตำแหน่งนั้น--------------------
					$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='".$LEVEL_NOHIS[$PER_ID]."' ";
					$db_dpis3->send_cmd($cmd2);
					$levelname=$db_dpis3->get_array();
					//---------------------------------------------
					$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$LEVEL_NO?level_no_format($LEVEL_NO):"";
					$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
					$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
					$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID];
					$arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID];	
					$arr_content[$data_count][per_retiredate]=$PER_RETIREDATE2[$PER_ID];
					$data_count++;
	} //end foreach
	if($person_count>0){	$count_data=$person_count;	}
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
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","L","C","C","C","R","C","L","C","C","L","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$seq = $data_count + 1;
			$name = $arr_content[$data_count][name];
			$position = $arr_content[$data_count][position];
			$level = $arr_content[$data_count][level];
			$org = $arr_content[$data_count][org];
			$posno = $arr_content[$data_count][posno];
			$training = $arr_content[$data_count][training];
			$poh_effectivedate = $arr_content[$data_count][poh_effectivedate];
			$poh_effectivedate9 = $arr_content[$data_count][poh_effectivedate9];
			$poh_effectivedate8 = $arr_content[$data_count][poh_effectivedate8];
			$curr_effectivedate = $arr_content[$data_count][curr_effectivedate];
			$per_sortlevel = $arr_content[$data_count][per_sortlevel];
			$per_salary = $arr_content[$data_count][per_salary];
			$decorate = $arr_content[$data_count][decorate];
			$per_startdate = $arr_content[$data_count][per_startdate];
			$per_birthdate = $arr_content[$data_count][per_birthdate];
			$per_retiredate=$arr_content[$data_count][per_retiredate];

			$arr_data = (array) null;
			$arr_data[] = $seq;	
			$arr_data[] = $name;	
			$arr_data[] = $poh_effectivedate;			
			$arr_data[] = $poh_effectivedate9;			
			$arr_data[] = $poh_effectivedate8;			
			$arr_data[] = $per_salary;			
			$arr_data[] = $per_startdate;			
			$arr_data[] = $decorate;			
			$arr_data[] = $per_birthdate;			
			$arr_data[] = substr($per_retiredate,6,4);			
			$arr_data[] = $org;			
			$arr_data[] = $training;			
	
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