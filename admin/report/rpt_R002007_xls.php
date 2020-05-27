<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R002007_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	for ($i=0;$i<count($EDU_TYPE);$i++) {
	 	if($search_edu) { $search_edu.= ' or '; }
		$search_edu.= "d.EDU_TYPE like '%$EDU_TYPE[$i]%' "; 
	} 
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "b.PL_NAME";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "b.PN_NAME";	
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "b.EP_NAME";	
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "b.TP_NAME";	
	} // end if

//	if(trim($RPTORD_LIST)) $arr_rpt_order = explode("|", $RPTORD_LIST);
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
//	$arr_rpt_order = array("EDUCNAME", "EDUCCOUNTRY");

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	
	if($search_en_code) $arr_search_condition[] = "(trim(d.EN_CODE)=trim('$search_en_code'))";
	if($search_en_ct_code) $arr_search_condition[] = "(trim(d.CT_CODE_EDU)=trim('$search_en_ct_code'))";

	$list_type_text = $ALL_REPORT_TITLE;

	include ("../report/rpt_condition3.php");
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]จำแนกตามวุฒิ/ประเทศ";
	$report_title .= (trim($search_en_code)?"||$search_en_name":"") . (trim($search_en_ct_code)?((trim($search_en_code)?" ":"||")."ประเทศ$search_en_ct_name"):"");
	$report_code = "R0207";

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
		$ws_head_line1 = array("ชื่อ - สกุล","ตำแหน่ง/ระดับ","สังกัด","สาขาวิชาเอก","สถานศึกษา");
		$ws_colmerge_line1 = array(0,1,1,1,1,);
		$ws_border_line1 = array("TLR","TRBL","TRBL","TRBL","TRBL");
		$ws_fontfmt_line1 = array("B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C");
		$ws_width = array(25,30,25,30,40);
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
		$colshow_cnt = $colseq;

		// loop พิมพ์ head บรรทัดที่ 1
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($colseq == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // function		

	function count_person($search_condition, $addition_condition){
		global $DPISDB, $db_dpis2, $position_table, $position_join;
		global $search_per_type, $search_edu;

		if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;

		if($DPISDB=="odbc"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		}elseif($DPISDB=="oci8"){				
			$search_condition = str_replace(" where ", " and ", $search_condition);
			$cmd = " select			count(a.PER_ID) as count_person
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f
							 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+)
												and a.PER_ID=d.PER_ID  and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE
												$search_condition
							 group by	a.PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			count(a.PER_ID) as count_person
							 from			(
													(
														(
															(
																PER_PERSONAL a 
																left join $position_table b on $position_join 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
													) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
												) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
							 $search_condition  and ($search_edu)
							 group by	a.PER_ID ";
		} // end if
		if($select_org_structure==1) {	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
		$count_person = $db_dpis2->send_cmd($cmd);
	//$db_dpis2->show_error();
		if($count_person==1){
			$data = $db_dpis2->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			if($data[count_person] == 0) $count_person = 0;
		} // end if

		return $count_person;
	} // function

	if($DPISDB=="odbc"){
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE $select_type_code
						 from			(
												(
													(
														(
															(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
										) inner join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition  and ($search_edu)
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE $select_type_code
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_EDUCATE d, PER_INSTITUTE e, PER_EDUCNAME f, PER_LEVEL g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PER_ID=d.PER_ID  and ($search_edu) and d.INS_CODE=e.INS_CODE(+) and d.EN_CODE=f.EN_CODE and a.LEVEL_NO=g.LEVEL_NO
											$search_condition
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, g.LEVEL_NAME, g.POSITION_LEVEL,
											$line_code as PL_CODE, b.ORG_ID, c.ORG_NAME, b.ORG_ID_1, b.ORG_ID_2,
											d.EN_CODE, f.EN_NAME, d.EM_CODE, d.INS_CODE, e.INS_NAME, d.CT_CODE_EDU, d.EDU_INSTITUTE $select_type_code
						 from			(
												(
													(
														(
															(	
															PER_PERSONAL a 
															left join $position_table b on $position_join 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) inner join PER_EDUCATE d on (a.PER_ID=d.PER_ID)
												) left join PER_INSTITUTE e on (d.INS_CODE=e.INS_CODE)
											) inner join PER_EDUCNAME f on (d.EN_CODE=f.EN_CODE)
										) inner join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											$search_condition  and ($search_edu)
						 order by		d.EN_CODE, d.CT_CODE_EDU, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, a.LEVEL_NO, $line_code ";
	}
	if($select_org_structure==1) {	$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);	}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
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
		} // end if
		
		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
		
		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B");
			$wsdata_align_1 = array("L","L","L","L","L");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_colmerge_1 = array(0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		$data_count = $data_row = $group_count = 0;
		$EN_CODE = -1;
		$CT_CODE = -1;
		while($data = $db_dpis->get_array()){
			$data_count++;
						
			if($EN_CODE != trim($data[EN_CODE]) || $CT_CODE != trim($data[CT_CODE])){
				$group_count++;
								
				$EN_CODE = trim($data[EN_CODE]);
				if(!trim($search_en_code)){
					if($EN_CODE) $EN_NAME = trim($data[EN_NAME]);
				} // end if
	
				$CT_CODE = trim($data[CT_CODE]);	
				if(!trim($search_en_ct_code)){
					if($CT_CODE){
						$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$CT_NAME = ($EN_CODE?" ":"") . "ประเทศ" . trim($data2[CT_NAME]);
					}
				} // end if
				
				if(!trim($search_en_code) || !trim($search_en_ct_code)){
					if(trim($search_en_code)) $count_person = count_person($search_condition, "(trim(d.CT_CODE_EDU)='$CT_CODE')");
					elseif(trim($search_en_ct_code)) $count_person = count_person($search_condition, "(trim(d.EN_CODE)='$EN_CODE')");
					else $count_person = count_person($search_condition, "(trim(d.EN_CODE)='$EN_CODE' and trim(d.CT_CODE_EDU)='$CT_CODE')");
	
					if($group_count > 1){ 
						$xlsRow++;
						$worksheet->write_string($xlsRow, 0, "", $xlsFmtHeader[left]);
						$worksheet->write($xlsRow, 1, "", $xlsFmtHeader[left]);
						$worksheet->write($xlsRow, 2, "", $xlsFmtHeader[left]);
						$worksheet->write($xlsRow, 3, "", $xlsFmtHeader[left]);
						$worksheet->write($xlsRow, 4, "", $xlsFmtHeader[left]);
					} // end if

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, ((($NUMBER_DISPLAY==2)?convert2thaidigit($group_count):$group_count). ' ' . $EN_NAME . $CT_NAME), $xlsFmtHeader[left]);
					$worksheet->write($xlsRow, 1, "", $xlsFmtHeader[left]);
					$worksheet->write($xlsRow, 2, "", $xlsFmtHeader[left]);
					$worksheet->write($xlsRow, 3, "", $xlsFmtHeader[left]);
					$worksheet->write($xlsRow, 4, "", $xlsFmtHeader[left]);

					$data_row = 0;
				} // end if
			} // end if		
			if($data_row == 0) print_header();

			$data_row++;

			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = "$PN_NAME $PER_NAME $PER_SURNAME";
			
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$POSITION_LEVEL = trim($data[POSITION_LEVEL]);

			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);

			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select $line_name as PL_NAME from $line_table b where trim($line_code)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if($search_per_type == 1){
				$PL_NAME = trim($data2[PL_NAME]) . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");
			}else{	// 2 || 3 || 4
				$PL_NAME = trim($data2[PN_NAME]) ;
			}
			
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];

			$EM_CODE = trim($data[EM_CODE]);
			$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EM_NAME = trim($data2[EM_NAME]);

			$INS_CODE = trim($data[INS_CODE]);
			$INS_NAME = $data[INS_NAME];
			if (!$INS_NAME) $INS_NAME = $data[EDU_INSTITUTE];

			$arr_data = (array) null;
			$arr_data[] = $data_row. ' ' .$FULLNAME;
			$arr_data[] = $PL_NAME;
			$arr_data[] = $ORG_NAME;
			$arr_data[] = $EM_NAME;
			$arr_data[] = $INS_NAME;

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
		} // end while
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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