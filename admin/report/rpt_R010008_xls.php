<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R010008_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 if($search_edu) { $search_edu.= ' or '; }
	$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; } 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;

	if(trim($RPTORD_LIST)=="SEX"){	//เลือกแยกตามเพศมาเพียงอันเดียวเท่านั้น
		$RPTORD_LIST = "ORG|".$RPTORD_LIST;
	}
	if(!trim($RPTORD_LIST)){ 	//กรณีไม่มีตัวเลือกแยกประเภทมาเลย
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);

	//แยกตามเงื่อนไขที่เลือก
	$select_list = "";		$order_by = "";	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_ID_REF as MINISTRY_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_ID_REF";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure==0) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $select_list .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
				else if($select_org_structure==1) $order_by .= "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";

//				$heading_name .= " ส่วนราชการ";
				break;
			case "LINE" :
				if($select_list) $select_list .= ", ";
				$select_list .= "a.PL_CODE";

				if($order_by) $order_by .= ", ";
				$order_by .= "a.PL_CODE";

//				$heading_name .= " สายงาน";
				break;
			case "SEX" :											//แยกตามเพศ+ระดับการศึกษา
				$set_header="SEX";
				if($select_list) $select_list .= ", ";
				$select_list .= "b.PER_GENDER";

				if($order_by) $order_by .= ", ";
				$order_by .= "b.PER_GENDER";
				
				$heading_name .= " และเพศ";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "g.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "g.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "g.ORG_SEQ_NO, g.ORG_CODE, a.DEPARTMENT_ID";
		}else{
			if($select_org_structure==0) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID";
		}
	} // end if

	$search_condition = "";
	$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(b.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_level_no_condi = (array) null;
	foreach ($LEVEL_NO as $search_level_no)
	{
        if ($search_level_no) $arr_level_no_condi[] = "'$search_level_no'";
//		echo "search_level_no=$search_level_no<br>";
	}
	if (count($arr_level_no_condi) > 0) $arr_search_condition[] = "(trim(b.LEVEL_NO) in (".implode(",",$arr_level_no_condi)."))";

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='01')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		if($select_org_structure==0) $arr_search_condition[] = "(trim(b.OT_CODE)='03')";
		elseif($select_org_structure==1) $arr_search_condition[] = "(trim(d.OT_CODE)='03')";
	}
	if(in_array("PER_TYPE", $list_type)){	//จะต้องมีค่าส่งมาถึงจะสร้างรายงานได้
		$list_type_text = "";
		// ตำแหน่งประเภท และตำแหน่งในสายงาน
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " $PROVINCE_NAME";
		} // end if
		
		if($search_pt_name){ $list_type_text .=" ตำแหน่งประเภท : $search_pt_name"; }
		if($search_per_type==1){
			if(trim($search_pl_code)){
				$search_pl_code = trim($search_pl_code);
				$arr_search_condition[] = "(trim(a.PL_CODE)='$search_pl_code')";
				$list_type_text .= " / $PL_TITLE : $search_pl_name";
			}
		}elseif($search_per_type==2){
			if(trim($search_pn_code)){
				$search_pn_code = trim($search_pn_code);
				$arr_search_condition[] = "(trim(a.PN_CODE)='$search_pn_code')";
				$list_type_text .= " / $POS_EMP_TITLE : $search_pn_name";
			}
		}elseif($search_per_type==3){
			if(trim($search_ep_code)){
				$search_ep_code = trim($search_ep_code);
				$arr_search_condition[] = "(trim(a.EP_CODE)='$search_ep_code')";
				$list_type_text .= " / $POS_EMPSER_TITLE : $search_ep_name";
			}
		} // end if
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(d.CT_CODE_EDU) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$arr_search_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(a.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	
	$report_title = "จำนวน$PERSON_TYPE[$search_per_type] จำแนกตามระดับการศึกษา $heading_name";
	$report_title .= " ประจำปีงบประมาณ  พ.ศ. $search_year";    
    
if($export_type=="report")	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "H08";

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
		$ws_head_line1 = (array) null;
		$ws_head_line2 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
//			echo "heading_text [$i]=".$heading_text[$i]."<br>";
			$ws_head_line1[] = $buff[0];
			if($set_header=="SEX")
				$ws_head_line2[] = $buff[1].$buff[3];
			else
				$ws_head_line2[] = $buff[1];
		}

		$ws_colmerge_line1 = (array) null;
		$ws_colmerge_line2 = (array) null;
		$ws_border_line1 = (array) null;
		$ws_border_line2 = (array) null;
		$ws_fontfmt_line1 = (array) null;
		$ws_headalign_line1 = (array) null;
		$ws_width = (array) null;

		$ws_border_line1[] = "TLR";
		$ws_border_line2[] = "LBR";
		$ws_colmerge_line1[] = 0;
		$ws_colmerge_line2[] = 0;
		$ws_fontfmt_line1[] = "B";
		$ws_width[] = "45";
		$ws_headalign_line1[] = "C";
		if($set_header=="SEX") $cnt = count($ws_head_line1)-1;
		else $cnt = count($ws_head_line1);
		for($i=1; $i<$cnt; $i++){
			$ws_border_line1[] = "TL";
			$ws_border_line2[] = "TLBR";
			$ws_colmerge_line1[] = 1;
			$ws_colmerge_line2[] = 0;
			$ws_fontfmt_line1[] = "B";
			$ws_width[] = "8";
			$ws_headalign_line1[] = "C";
		}
		if($set_header=="SEX") {
			$ws_border_line1[] = "TLR";
			$ws_border_line2[] = "LBR";
			$ws_colmerge_line1[] = 0;
			$ws_colmerge_line2[] = 0;
			$ws_fontfmt_line1[] = "B";
			$ws_width[] = "8";
			$ws_headalign_line1[] = "C";
		}
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
		global $set_header;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2;
		global $ws_colmerge_line1, $ws_colmerge_line2;
		global $ws_border_line1, $ws_border_line2;
		global $ws_fontfmt_line1;
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
	} // function		

	function count_person($education_level, $PER_GENDER, $search_condition, $addition_condition){
		global $DPISDB, $db_dpis2;
		global $search_per_type, $select_org_structure, $search_edu;
		global $EDU_TYPE_TXT;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		if($education_level == 1) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('1'))";
		elseif($education_level == 2) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('2'))";
		elseif($education_level == 3) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('3'))";
		elseif($education_level == 4) $search_condition .= (trim($search_condition)?" and ":" where ") . "(trim(f.EL_TYPE) in ('4'))";
		if($PER_GENDER){ 
			$search_condition .= (trim($search_condition)?" and ":" where ") . " (b.PER_GENDER=$PER_GENDER) ";
		}
		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;
		$search_condition = str_replace(" where ", " and ", $search_condition);

		//หาจำนวน
		if($search_per_type==1){
			$pos_tb="PER_POSITION";
			$join_tb="a.POS_ID=b.POS_ID";
		}elseif($search_per_type==2){	
			$pos_tb="PER_POS_EMP";
			$join_tb="a.POEM_ID=b.POEM_ID";
		}elseif($search_per_type==3){ 
			$pos_tb="PER_POS_EMPSER";
			$join_tb="a.POEMS_ID=b.POEMS_ID";
		}

		if($DPISDB=="odbc"){	
				$cmd = " select			count(b.PER_ID) as count_person
							 from		(	
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	b.PER_ID ";
		}elseif($DPISDB=="oci8"){
				$search_condition = str_replace(" where ", " and ", $search_condition);
				$cmd = " select			count(b.PER_ID) as count_person
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_EDUCLEVEL f, PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and  a.DEPARTMENT_ID=g.ORG_ID(+)
												and b.PER_ID=d.PER_ID  and ($search_edu) 
												$search_condition
							 group by	b.PER_ID ";
		}elseif($DPISDB=="mysql"){
				$cmd = " select			count(b.PER_ID) as count_person
							 from		(	
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
							 $search_condition  and ($search_edu)
							 group by	b.PER_ID ";
		}

		if($select_org_structure==1){ 
			$cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);
			 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		$count_person = $db_dpis2->send_cmd($cmd);
	//echo "<br>$cmd<br>";
	//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

	return $count_person;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order, $select_org_structure;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					if($MINISTRY_ID && $MINISTRY_ID!=-1) $arr_addition_condition[] = "(g.ORG_ID_REF = $MINISTRY_ID)";
				break;
				case "DEPARTMENT" :	
					if($DEPARTMENT_ID && $DEPARTMENT_ID!=-1) $arr_addition_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
				break;
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID && $ORG_ID!=-1) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}
				break;
				case "LINE" :
					if($PL_CODE) $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE')";
					else $arr_addition_condition[] = "(trim(a.PL_CODE) = '$PL_CODE' or a.PL_CODE is null)";
				break;
				case "SEX" :
					if($PER_GENDER) $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER)";
					else $arr_addition_condition[] = "(a.PER_GENDER = $PER_GENDER or a.PER_GENDER is null)";
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $MINISTRY_ID, $DEPARTMENT_ID, $ORG_ID, $PL_CODE,$PER_GENDER;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :	
					$MINISTRY_ID = -1;
				break;
				case "DEPARTMENT" :	
					$DEPARTMENT_ID = -1;
				break;
				case "ORG" :	
					$ORG_ID = -1;
				break;
				case "LINE" :
					$PL_CODE = -1;
				break;
				case "SEX" :
					$PER_GENDER = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	//หาชื่อส่วนราชการ 
	if($search_per_type==1){
		$pos_tb="PER_POSITION";
		$join_tb="a.POS_ID=b.POS_ID";
	}elseif($search_per_type==2){	
		$pos_tb="PER_POS_EMP";
		$join_tb="a.POEM_ID=b.POEM_ID";
	}elseif($search_per_type==3){ 
		$pos_tb="PER_POS_EMPSER";
		$join_tb="a.POEMS_ID=b.POEMS_ID";
	}
	if($DPISDB=="odbc"){
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
							 	order by		$order_by ";
	}elseif($DPISDB=="oci8"){
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			distinct $select_list
							 from			$pos_tb a, PER_PERSONAL b, PER_ORG c, PER_EDUCATE d, PER_EDUCLEVEL f, PER_ORG g
							 where		$join_tb(+) and a.ORG_ID=c.ORG_ID(+) and trim(d.EL_CODE)=trim(f.EL_CODE(+)) and a.DEPARTMENT_ID=g.ORG_ID(+)
							 					and b.PER_ID=d.PER_ID and ($search_edu) 
												$search_condition
							 order by		$order_by ";
	}elseif($DPISDB=="mysql"){
			$cmd = "select			distinct $select_list
							from		(
							 					(
													(
														(
															$pos_tb a 
															left join PER_PERSONAL b on ($join_tb) 
														) left join PER_ORG c on (a.ORG_ID=c.ORG_ID)
													) left join PER_EDUCATE d on (b.PER_ID=d.PER_ID)
												) inner join PER_EDUCLEVEL f on (trim(d.EL_CODE)=trim(f.EL_CODE))
											) left join PER_ORG g on (a.DEPARTMENT_ID=g.ORG_ID)
											$search_condition  and ($search_edu)
							 	order by		$order_by ";
	}

	if($select_org_structure==1){
		 $cmd = str_replace("a.ORG_ID", "b.ORG_ID", $cmd);	
		 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
// echo "<br>$cmd<br>";
//	$db_dpis->show_error();

	$data_count = 0;
	$GRAND_TOTAL_1 = $GRAND_TOTAL_2 = $GRAND_TOTAL_3 = $GRAND_TOTAL_4 = 0;
	$GRAND_TOTAL_5 = $GRAND_TOTAL_6 = $GRAND_TOTAL_7 = $GRAND_TOTAL_8 = 0;
	initialize_parameter(0);
	while($data = $db_dpis->get_array()){
		$small_REPORT_ORDER = $arr_rpt_order[count($arr_rpt_order)-1];	// ระดับต่ำสุด ไม่รวม SEX
		if ($small_REPORT_ORDER=="SEX") $small_REPORT_ORDER = $arr_rpt_order[count($arr_rpt_order)-2]; // ไม่เอา SEX
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "MINISTRY" :
					if($MINISTRY_ID != trim($data[MINISTRY_ID])){
						$MINISTRY_ID = trim($data[MINISTRY_ID]);
						if($MINISTRY_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$MINISTRY_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$MINISTRY_NAME = $data2[ORG_NAME];
							$MINISTRY_SHORT = $data2[ORG_SHORT];
						} // end if

if($f_all){
						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "MINISTRY";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $MINISTRY_NAME;
						$arr_content[$data_count][short_name] = $MINISTRY_SHORT;
	
						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="MINISTRY") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="MINISTRY") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
					
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
}					
					} // end if
				break;

				case "DEPARTMENT" :
					if($DEPARTMENT_ID != trim($data[DEPARTMENT_ID])){
						$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
						if($DEPARTMENT_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$DEPARTMENT_NAME = $data2[ORG_NAME];
							$DEPARTMENT_ORG_SHORT = $data2[ORG_SHORT];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "DEPARTMENT";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $DEPARTMENT_NAME;
						$arr_content[$data_count][short_name] = $DEPARTMENT_ORG_SHORT;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="DEPARTMENT") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="DEPARTMENT") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;

				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME,ORG_SHORT from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
							$ORG_SHORT = $data2[ORG_SHORT];
							if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
						} // end if

						$addition_condition = generate_condition($rpt_order_index);

						$arr_content[$data_count][type] = "ORG";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $ORG_NAME;
						$arr_content[$data_count][short_name] = $ORG_SHORT;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="ORG") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="ORG") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
				
				case "LINE" :
					if($PL_CODE != trim($data[PL_CODE])){
						$PL_CODE = trim($data[PL_CODE]);
						if($PL_CODE != ""){
							$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
						} // end if

						$addition_condition = generate_condition($rpt_order_index);
			
						$arr_content[$data_count][type] = "LINE";
						$arr_content[$data_count][name] = str_repeat(" ", ($rpt_order_index * 5)) . $PL_NAME;

						if(!trim($data[PER_GENDER])){
							$arr_content[$data_count][count_1] = count_person(1, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 0,$search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 0,$search_condition, $addition_condition);
						
							if ($small_REPORT_ORDER=="LINE") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];
							}
						}else{	//แยกตามเพศ
							//ชาย
							$arr_content[$data_count][count_1] = count_person(1, 1,$search_condition, $addition_condition);
							$arr_content[$data_count][count_2] = count_person(2, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_3] = count_person(3, 1, $search_condition, $addition_condition);
							$arr_content[$data_count][count_4] = count_person(4, 1, $search_condition, $addition_condition);
							//หญิง
							$arr_content[$data_count][count_5] = count_person(1, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_6] = count_person(2, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_7] = count_person(3, 2,$search_condition, $addition_condition);
							$arr_content[$data_count][count_8] = count_person(4, 2,$search_condition, $addition_condition);
	
							if ($small_REPORT_ORDER=="LINE") {
								$GRAND_TOTAL_1 += $arr_content[$data_count][count_1];
								$GRAND_TOTAL_2 += $arr_content[$data_count][count_2];
								$GRAND_TOTAL_3 += $arr_content[$data_count][count_3];
								$GRAND_TOTAL_4 += $arr_content[$data_count][count_4];			
								$GRAND_TOTAL_5 += $arr_content[$data_count][count_5];
								$GRAND_TOTAL_6 += $arr_content[$data_count][count_6];
								$GRAND_TOTAL_7 += $arr_content[$data_count][count_7];
								$GRAND_TOTAL_8 += $arr_content[$data_count][count_8];
							}
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);			
						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end while
	
	if($set_header=="SEX"){
		$GRAND_TOTAL_M = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);	//ชาย
		$GRAND_TOTAL_F = ($GRAND_TOTAL_5+ $GRAND_TOTAL_6 + $GRAND_TOTAL_7 + $GRAND_TOTAL_8);		//หญิง
	}else{
		$GRAND_TOTAL = ($GRAND_TOTAL_1 + $GRAND_TOTAL_2 + $GRAND_TOTAL_3 + $GRAND_TOTAL_4);
	}
	
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
			$wsdata_fontfmt_1 = (array) null;
			$wsdata_align_1 = (array) null;
			$wsdata_border_1 = (array) null;
			$wsdata_colmerge_1 = (array) null;
			$wsdata_colmerge_2 = (array) null;
			$wsdata_fontfmt_2 = (array) null;
			
			$wsdata_align_1[] = "L";
			$wsdata_fontfmt_1[] = "B";
			$wsdata_border_1[] = "TLBR";
			$wsdata_colmerge_1[] = 0;
			$wsdata_fontfmt_2[] = "";
			if($set_header=="SEX") $cnt = count($ws_head_line1)-1;
			else $cnt = count($ws_head_line1);
			for($i=1; $i<$cnt; $i++){
				$wsdata_align_1[] = "R";
				$wsdata_fontfmt_1[] = "B";
				$wsdata_border_1[] = "TLBR";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
			} // loop for
			if($set_header=="SEX") {
				$wsdata_align_1[] = "R";
				$wsdata_fontfmt_1[] = "B";
				$wsdata_border_1[] = "TLBR";
				$wsdata_colmerge_1[] = 0;
				$wsdata_fontfmt_2[] = "";
			}
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];

//			$xlsRow++;
//			$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			if($set_header=="SEX"){
				//ชาย
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
				$COUNT_4 = $arr_content[$data_count][count_4];
				$COUNT_TOTAL_M = ($COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4);
				//หญิง
				$COUNT_5 = $arr_content[$data_count][count_5];
				$COUNT_6 = $arr_content[$data_count][count_6];
				$COUNT_7 = $arr_content[$data_count][count_7];
				$COUNT_8 = $arr_content[$data_count][count_8];
				$COUNT_TOTAL_F = ($COUNT_5 + $COUNT_6 + $COUNT_7 + $COUNT_8);

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $COUNT_4;
				$arr_data[] = $COUNT_TOTAL_M;
				$arr_data[] = $COUNT_5;
				$arr_data[] = $COUNT_6;
				$arr_data[] = $COUNT_7;
				$arr_data[] = $COUNT_8;
				$arr_data[] = $COUNT_TOTAL_F;
				$arr_data[] = ($COUNT_TOTAL_M+$COUNT_TOTAL_F);

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
			}else{
				$COUNT_1 = $arr_content[$data_count][count_1];
				$COUNT_2 = $arr_content[$data_count][count_2];
				$COUNT_3 = $arr_content[$data_count][count_3];
				$COUNT_4 = $arr_content[$data_count][count_4];
				$COUNT_TOTAL = ($COUNT_1 + $COUNT_2 + $COUNT_3 + $COUNT_4);
				
				$PERCENT_1 = $PERCENT_2 = $PERCENT_3 = $PERCENT_4 = $PERCENT_TOTAL = 0;
				if($COUNT_TOTAL){ 
					$PERCENT_1 = ($COUNT_1 / $COUNT_TOTAL) * 100;
					$PERCENT_2 = ($COUNT_2 / $COUNT_TOTAL) * 100;
					$PERCENT_3 = ($COUNT_3 / $COUNT_TOTAL) * 100;
					$PERCENT_4 = ($COUNT_4 / $COUNT_TOTAL) * 100;
				} // end if
				if($GRAND_TOTAL) $PERCENT_TOTAL = ($COUNT_TOTAL / $GRAND_TOTAL) * 100;

				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $COUNT_1;
				$arr_data[] = $PERCENT_1;
				$arr_data[] = $COUNT_2;
				$arr_data[] = $PERCENT_2;
				$arr_data[] = $COUNT_3;
				$arr_data[] = $PERCENT_3;
				$arr_data[] = $COUNT_4;
				$arr_data[] = $PERCENT_4;
				$arr_data[] = $COUNT_TOTAL;
				$arr_data[] = $PERCENT_TOTAL;

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
			} //end if
		} // end for
		
		//รวมทั้งหมด
		$PERCENT_TOTAL_1 = $PERCENT_TOTAL_2 = $PERCENT_TOTAL_3 = $PERCENT_TOTAL_4 = 0;
		if($GRAND_TOTAL){ 
			$PERCENT_TOTAL_1 = ($GRAND_TOTAL_1 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_2 = ($GRAND_TOTAL_2 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_3 = ($GRAND_TOTAL_3 / $GRAND_TOTAL) * 100;
			$PERCENT_TOTAL_4 = ($GRAND_TOTAL_4 / $GRAND_TOTAL) * 100;
		} // end if
		$PERCENT_TOTAL = ($GRAND_TOTAL / $GRAND_TOTAL) * 100;

		if($set_header=="SEX"){	
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL_3;
			$arr_data[] = $GRAND_TOTAL_4;
			$arr_data[] = $GRAND_TOTAL_M;
			$arr_data[] = $GRAND_TOTAL_5;
			$arr_data[] = $GRAND_TOTAL_6;
			$arr_data[] = $GRAND_TOTAL_7;
			$arr_data[] = $GRAND_TOTAL_8;
			$arr_data[] = $GRAND_TOTAL_F;
			$arr_data[] = ($GRAND_TOTAL_M+$GRAND_TOTAL_F);
		} else {
			$arr_data = (array) null;
			$arr_data[] = "รวม";
			$arr_data[] = $GRAND_TOTAL_1;
			$arr_data[] = $PERCENT_TOTAL_1;
			$arr_data[] = $GRAND_TOTAL_2;
			$arr_data[] = $PERCENT_TOTAL_2;
			$arr_data[] = $GRAND_TOTAL_3;
			$arr_data[] = $PERCENT_TOTAL_3;
			$arr_data[] = $GRAND_TOTAL_4;
			$arr_data[] = $PERCENT_TOTAL_4;
			$arr_data[] = $GRAND_TOTAL;
			$arr_data[] = $PERCENT_TOTAL;
		}

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
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if($set_header=="SEX"){
			$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if
//print_r($arr_content);
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