<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "to_number(replace(b.POS_NO,'-',''))";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
		$arr_search_condition[] = "(a.PER_TYPE=1)";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[ORG_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[ORG_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";

	} // end if

//	if(!trim($select_org_structure)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
//	}
	
	if($list_type == "SELECT"){//ค้นหารายบุคคล
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if ($service_list_type=="SELECT"){ //ค้นหารายประเภทราชการพิเศษ
		$sv_search_condition = "and (psh.SV_CODE in ($SELECTED_SV_CODE))";
	}else{
		$sv_search_condition ="";
	}

	$report_title = "$DEPARTMENT_NAME||แบบประวัติการดำรงตำแหน่งในราชการพิเศษ";
	$report_code = "RA01001";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		
		$worksheet->set_column(0, 0, 7);
		$worksheet->set_column(1, 1, 30);
		$worksheet->set_column(2, 2, 30);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 13);
		$worksheet->set_column(5, 5, 13);
		$worksheet->set_column(6, 6, 12);
		$worksheet->set_column(7, 7, 12);

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "ลำดับที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "คณะกรรมการ/คณะทำงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "หน่วยงาน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "การดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 4, "คำสั่ง/ประกาศ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "ระยะเวลาการดำรงตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLB", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TRB", 1));
		$worksheet->write($xlsRow, 7, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 2, "เจ้าของเรื่อง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 4, "เลขที่/ลงวันที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 5, "ตั้งแต่วันที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 6, "ถึงวันที่", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
		
	} // function	

	if($DPISDB=="odbc"){
		$cmd = " select			b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
						 from		(
											(
												(
													(
														(
															(
															PER_PERSONAL a 
															left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
														) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
													) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
												) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
											) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
										) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
									) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
											$search_condition and a.PER_STATUS=1
							 group by b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
						 order by $order_by";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
						 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+) and a.PER_STATUS=1
											$search_condition
							 group by b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
						 order by $order_by";
	}
	
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	//echo $cmd . "<br>";

	$data_count2 = 0;
	while($data = $db_dpis->get_array()){
		$data_row++;
		$arr_content[$data_count][PER_ID] = $data[PER_ID];
		$arr_content[$data_count][PN_NAME] = $data[PN_NAME];
		$arr_content[$data_count][PER_NAME] = $data[PER_NAME];
		$arr_content[$data_count][PER_SURNAME] = $data[PER_SURNAME];
		$arr_content[$data_count][PL_NAME] = $data[PL_NAME];
		$arr_content[$data_count][LEVEL_NO] = $data[LEVEL_NO];
		$arr_content[$data_count][POSITION_LEVEL] = $data[POSITION_LEVEL];
		$arr_content[$data_count][PT_CODE] = trim($data[PT_CODE]);
		$arr_content[$data_count][ORG_NAME] = $data[ORG_NAME];

		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$arr_content[$data_count][PER_BIRTHDATE] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		$arr_content[$data_count][PER_STARTDATE] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
		$arr_content[$data_count][POH_EFFECTIVEDATE] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);

		$arr_content[$data_count][AGE] = date_difference(date("Y-m-d"), $data[PER_BIRTHDATE], "ymd");
		$arr_content[$data_count][WORK_DURATION] = date_difference(date("Y-m-d"), $data[PER_STARTDATE], "ymd");
	
		$cmd = "SELECT SRH_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_DOCNO, SRH_NOTE,  psh.SV_CODE, psh.SRT_CODE, psh.ORG_ID, ps.SV_NAME, pst.SRT_NAME, 
										po.ORG_NAME, psh.UPDATE_USER, psh.UPDATE_DATE  
					FROM		PER_SERVICEHIS psh, PER_SERVICE ps, PER_SERVICETITLE pst, PER_ORG po  
					WHERE	psh.SV_CODE=ps.SV_CODE and psh.SRT_CODE=pst.SRT_CODE and psh.ORG_ID=po.ORG_ID and psh.PER_ID=$data[PER_ID] $sv_search_condition ORDER BY SRH_STARTDATE DESC, SRH_ENDDATE DESC";

		$count_data2 = $db_dpis2->send_cmd($cmd);
		//echo $cmd . "<br><br>";
		//echo $count_data2;
		while($data2 = $db_dpis2->get_array()){
			$arr_content2[$data_count2][PER_ID] = $data[PER_ID];

			$arr_content2[$data_count2][SRH_STARTDATE] = show_date_format($data2[SRH_STARTDATE], $DATE_DISPLAY);
			$arr_content2[$data_count2][SRH_ENDDATE] = show_date_format($data2[SRH_ENDDATE], $DATE_DISPLAY);
			$arr_content2[$data_count2][SRH_NOTE] = trim($data2[SRH_NOTE]);
			$arr_content2[$data_count2][SRH_DOCNO] = trim($data2[SRH_DOCNO]);
			$arr_content2[$data_count2][SV_NAME] = trim($data2[SV_NAME]);
			$arr_content2[$data_count2][ORG_ID] = trim($data2[ORG_ID]);
			$arr_content2[$data_count2][ORG_NAME] = trim($data2[ORG_NAME]);
			$arr_content2[$data_count2][SRT_NAME] = trim($data2[SRT_NAME]);		

			$data_count2++;
		}
		//echo count($arr_content2)."<br><br>";
		$data_count++;
	} // end while
	
	$xlsRow = 0;
	
	if($count_data){
		$data2_check = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){

			if ($data_count!=0) $xlsRow++;
			
			$arr_title = explode("||", $report_title);
			for($i=0; $i<count($arr_title); $i++){
				$xlsRow++;
				$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			} // end if

			$xlsRow++;

			$worksheet->write($xlsRow, 0, $arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],  set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $arr_content[$data_count][ORG_NAME],  set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));

			print_header();

			$data2_check_PER_ID=0;
			$ORDERrun=0;

			if ((count($arr_content2)>0) && (count($arr_content2)>= $data2_check)) {
				while ($arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]){
					$ORDERrun++;
					$ORDER = $ORDERrun;
					$SRT_NAME = $arr_content2[$data2_check][SRT_NAME];
					$ORG_NAME = $arr_content2[$data2_check][ORG_NAME];
					$SV_NAME = $arr_content2[$data2_check][SV_NAME];
					$SRH_DOCNO = $arr_content2[$data2_check][SRH_DOCNO];
					$SRH_STARTDATE = $arr_content2[$data2_check][SRH_STARTDATE];
					$SRH_ENDDATE = $arr_content2[$data2_check][SRH_ENDDATE];
					$SRH_NOTE = $arr_content2[$data2_check][SRH_NOTE];

					$xlsRow++;
					$worksheet->write_string($xlsRow, 0, "$ORDER ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 1, "$SRT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 2, "$ORG_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 3, "$SV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 4, "$SRH_DOCNO ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 5, "$SRH_STARTDATE", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 6, "$SRH_ENDDATE ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					$worksheet->write_string($xlsRow, 7, "$SRH_NOTE ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
					
					$data2_check++;
				}// end while
			}
		} // end for
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
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