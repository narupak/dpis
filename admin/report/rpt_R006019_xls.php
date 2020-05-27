<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($search_per_type==1 || $search_per_type==5){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_join = "b.PL_CODE=e.PL_CODE";
		$line_name = "e.PL_NAME";
		$position_no= "b.POS_NO";
		$line_short_name = "PL_SHORTNAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		 $line_title=" สายงาน";
		 $type_code ="b.PT_CODE";
		 $select_type_code =",b.PT_CODE";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";   
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_join = "b.PN_CODE=e.PN_CODE";
		$line_name = "e.PN_NAME";
		$position_no= "b.POEM_NO";
		$line_search_code=trim($search_pn_code);
		$line_search_name=trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_join = "b.EP_CODE=e.EP_CODE";
		$line_name = "e.EP_NAME";
		$position_no= "b.POEMS_NO";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_join = "b.TP_CODE=e.TP_CODE";
		$line_name = "e.TP_NAME";
		$position_no= "b.POT_NO";
		$line_search_code=trim($search_tp_code);
		$line_search_name=trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if 	
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(!trim($RPTORD_LIST)){ 
	
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG"); 

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "ORG" :
				if($select_list) $select_list .= ", ";
				if($select_org_structure == 0) $select_list .= "c.ORG_ID";
				else if($select_org_structure == 1) $select_list .= "c.ORG_ID";

				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "c.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "c.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
		} // end switch case
	} // end for

	$search_condition = "";
	$list_type_text = $ALL_REPORT_TITLE;

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
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
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "$DEPARTMENT_NAME||เปรียบเทียบอัตราการเลื่อนเงินเดือนรายบุคคลตามระบบเดิมกับระบบใหม่ $search_layer_no ขั้น ในช่วง 5 ปี||นับตั้งแต่ปีงบประมาณ $search_budget_year";
	$report_code = "R0619";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);

	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($name){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $search_budget_year;
		
		$worksheet->set_column(0, 0, 45);
		$worksheet->set_column(1, 5, 10);
		$worksheet->set_column(2, 5, 10);
		$worksheet->set_column(3, 5, 10);
		$worksheet->set_column(4, 5, 10);
		$worksheet->set_column(5, 5, 10);
		$worksheet->set_column(6, 5, 10);

		if($name=="1"){	//คำนวณเงินงบประมาณเปรียบเทียบการเลื่อนเงินเดือนระบบเดิมกับระบบใหม่ว่าใช้วงเงินงบประมาณมากหรือน้อยกว่าเดิม
			$worksheet->set_column(7, 5, 15);
			$worksheet->set_column(8, 5, 15);
			$worksheet->set_column(9, 5, 15);
			$worksheet->set_column(10, 5, 15);
			$worksheet->set_column(11, 5, 20);
				
			$xlsRow++;
			$worksheet->write($xlsRow, 0,"ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 1,"ระบบการเลื่อน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			$worksheet->write($xlsRow, 2,"การเลื่อนเงินเดือนครั้งที่ 1", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 3,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 4,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 5,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6,"การเลื่อนเงินเดือนครั้งที่ 2", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 7,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 8,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 9,"", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,10,"รวมวงเงินที่ใช้ทั้งปี", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,11, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
	
			$xlsRow++;
			$worksheet->write($xlsRow,0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow,1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow,2, "ฐานเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,4, "ใช้เงินเพิ่ม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow, 6, "ฐานเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,7, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,8, "ใช้เงินเพิ่ม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,9, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,10, "บาท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			$worksheet->write($xlsRow,11, "%เทียบกับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
			$worksheet->write($xlsRow, 2, "1-มี.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "1-เม.ย.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "บาท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "%เทียบกับ1มี.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "1-ก.ย.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "1-ต.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "บาท", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "%เทียบกับ1ก.ย.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 11, "1มี.ค.", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		}else if($name=="2"){	//เปรียบเทียบอัตราการเลื่อนเงินเดือนรายบุคคลตามระบบเดิมกับระบบใหม่
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "% ที่ได้เลื่อน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow, 1, "สัดส่วนของผู้ได้รับการเลื่อนในแต่ละ %", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
		
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				//for($i=0; $i<6; $i++) $worksheet->write($xlsRow, ($i + 1), "ปี ".($search_budget_year - $i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				for($i=0; $i<6; $i++) $worksheet->write($xlsRow, ($i + 1), "ปี ".($search_budget_year - $i), set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
		} //end if
	} // function		

	//PER_POSITION a
	//$PERCENT_NAME = array("น้อยกว่า 2%", "ตั้งแต่ 2% แต่ไม่ถึง 3%", "ตั้งแต่ 3% แต่ไม่ถึง 4%", "ตั้งแต่ 4% แต่ไม่ถึง 5%", "ตั้งแต่ 5% แต่ไม่ถึง 6%", "6%");
	function count_promote($search_budget_year,$percentname,$name){
		global $DPISDB, $db_dpis2;
		global $percent,$search_condition;

		if($name=="1"){	//คำนวณเงินงบประมาณเปรียบเทียบการเลื่อนเงินเดือนระบบเดิมกับระบบใหม่ว่าใช้วงเงินงบประมาณมากหรือน้อยกว่าเดิม
		
		}else if($name=="2"){	//เปรียบเทียบอัตราการเลื่อนเงินเดือนรายบุคคลตามระบบเดิมกับระบบใหม่
				if($percent==0){	//น้อยกว่า 2%   
					$percent_condition = " and ((SAH_SALARY - SAH_OLD_SALARY)/100) < 2";
				}else if($percent==1){	//ตั้งแต่ 2% แต่ไม่ถึง 3%		   
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 2 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<3)";
				}else if($percent==2){	//ตั้งแต่ 3% แต่ไม่ถึง 4%		  
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 3 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<4)";
				}else if($percent==3){	//ตั้งแต่ 4% แต่ไม่ถึง 5%		   
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 4 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<5)";
				}else if($percent==4){	//ตั้งแต่ 5% แต่ไม่ถึง 6%			
					$percent_condition = " and (((SAH_SALARY - SAH_OLD_SALARY)/100) >= 5 and ((SAH_SALARY - SAH_OLD_SALARY)/100)<6)";
				}else if($percent==5){	//6%											
					$percent_condition = " and ((SAH_SALARY - SAH_OLD_SALARY)/100) = 6";
				}
				
				//if ($percent==0)
		/*
		select        PER_ID, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SALARY, ((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) as count_salaryhis
									 from            PER_SALARYHIS
									where        SAH_EFFECTIVEDATE >= '2008-10-01' and 
														SAH_EFFECTIVEDATE < '2009-10-01' and (((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) >= 0 and ((nvl(SAH_SALARY_UP,0) + nvl(SAH_SALARY_EXTRA,0)) * 100 / nvl(SAH_SALARY,0)) < 10)
														and nvl(SAH_SALARY,0) > 0 and (nvl(SAH_SALARY_UP,0) > 0 or nvl(SAH_SALARY_EXTRA,0) > 0)
		*/
					if($DPISDB=="odbc"){	//4006
						$cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from		PER_SALARYHIS inner join
													(	
														( 	
															(
															PER_PERSONAL a 
															left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
															) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
													) on (PER_SALARYHIS.PER_ID=a.PER_ID)
										where		(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}elseif($DPISDB=="oci8"){
						$search_condition = str_replace(" where ", " and ", $search_condition);
						/* $cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from			PER_SALARYHIS,PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
										where		PER_SALARYHIS.PER_ID=a.PER_ID and  a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and
														(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and 
														SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";*/
						$cmd = " select		count(a.PER_ID) as count_salaryhis
										from			PER_SALARYHIS,PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
										where		PER_SALARYHIS.PER_ID=a.PER_ID and  a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and
														(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}elseif($DPISDB=="mysql"){
						$cmd = " select		sum(SAH_SALARY - SAH_OLD_SALARY) as count_salaryhis
										from		PER_SALARYHIS inner join
													(	
														( 	
															(
															PER_PERSONAL a 
															left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
															) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
													) on (PER_SALARYHIS.PER_ID=a.PER_ID)
										where		(SAH_EFFECTIVEDATE >= '".($search_budget_year - 543 - 1)."-10-01' and SAH_EFFECTIVEDATE < '".($search_budget_year - 543)."-10-01') 
										$search_condition $percent_condition";
					}
					$count_salaryhis = $db_dpis2->send_cmd($cmd);
		//		$db_dpis2->show_error();
				if($count_salaryhis==1){
					$data = $db_dpis2->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					if($data[count_salaryhis] == 0) $count_salaryhis = 0;
				} // end if
		} //end else if		
		
		//echo "<br>".$count_salaryhis."::".$data[count_salaryhis]."=[ ".$percentname." ] : ".$cmd."<br>";
		return $count_salaryhis;
	} // function
	
	function generate_condition($current_index){
		global $DPISDB, $arr_rpt_order;
		global $ORG_ID;
				
		for($rpt_order_index=0; $rpt_order_index <= $current_index; $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					if($select_org_structure==0){
						if($ORG_ID) $arr_addition_condition[] = "(b.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(b.ORG_ID = 0 or b.ORG_ID is null)";
					}elseif($select_org_structure==1){
						if($ORG_ID) $arr_addition_condition[] = "(a.ORG_ID = $ORG_ID)";
						else $arr_addition_condition[] = "(a.ORG_ID = 0 or a.ORG_ID is null)";
					}
				break;
			} // end switch case
		} // end for
		
		$addition_condition = "";
		if(count($arr_addition_condition)) $addition_condition = implode(" and ", $arr_addition_condition);

		return $addition_condition;
	} // function
	
	function initialize_parameter($current_index){
		global $arr_rpt_order;
		global $ORG_ID;
		for($rpt_order_index=$current_index; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :	
					$ORG_ID = -1;
				break;
			} // end switch case
		} // end for
	} // function
	
	$data_count = $data_row = 0;
	initialize_parameter(0);	
	$arrlevelsystem=array("เดิม (ข้อมูลจริง)","กรณีระบบใหม่");	$budget_year = array();
	$PERCENT_NAME = array("น้อยกว่า 2%", "ตั้งแต่ 2% แต่ไม่ถึง 3%", "ตั้งแต่ 3% แต่ไม่ถึง 4%", "ตั้งแต่ 4% แต่ไม่ถึง 5%", "ตั้งแต่ 5% แต่ไม่ถึง 6%", "6%");
	$percent_min = array(	0, 2, 3, 4, 5, 6);
	$percent_max = array( 2, 3, 4, 5, 6, 12);
	
	$data_row++;
	$arr_content[$data_count][type] = "DETAIL1";
	for($i=1; $i<=11; $i++) {
			for($j=0;$j<count($arrlevelsystem);$j++){
				for($k=0; $k<6; $k++){
					$arr_content[$data_count][name] = "ปี ".($search_budget_year - $k)."=="."==".$arrlevelsystem[$j];
				}
			}
	} 

	for ($percent=0; $percent < 6; $percent++){
		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "ORG" :
					if($ORG_ID != trim($data[ORG_ID])){
						$ORG_ID = trim($data[ORG_ID]);
						if($ORG_ID != ""){
							$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
							if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
							$db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$data2 = $db_dpis2->get_array();
							$ORG_NAME = $data2[ORG_NAME];
						} // end if
					} // end if

					if($rpt_order_index == (count($arr_rpt_order) - 1)){
						$data_row++;

						$arr_content[$data_count][type] = "DETAIL2";
						//$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 6)) . "$data_row.". $PERCENT_NAME[$percent];
						$arr_content[$data_count][name] = $PERCENT_NAME[$percent];
						for($i=1; $i<=6; $i++) $arr_content[$data_count]["count_".$i] = count_promote(($search_budget_year - ($i - 1)),$PERCENT_NAME[$percent],"2");

						$data_count++;
					} // end if
				break;
			} // end switch case
		} // end for
	} // end for
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
$count_data=1;
if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
		} // end if
	
print_header("1");
for($i=1; $i<=11; $i++) {
		${"COUNT_".$i} = $i;
} // end for
$xlsRow++;
$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
for($j=0;$j<count($arrlevelsystem);$j++){
	for($k=0; $k<6; $k++){ 
		$worksheet->write($xlsRow, ($k+1), "ปี ".($search_budget_year - $k)."==".$arrlevelsystem[$j], set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
	}
} //end for

print_header("2");
for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];

			for($i=1; $i<=6; $i++){
				${"COUNT_".$i} = $arr_content[$data_count]["count_".$i];
			} //end for
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "$NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			//__for($i=1; $i<=6; $i++){ $worksheet->write_string($xlsRow, ($i), (${"COUNT_".$i}?"X":""), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0)); }
			for($i=1; $i<=6; $i++){ $worksheet->write_string($xlsRow,$i, ${"COUNT_".$i}, set_format("xlsFmtTableDetail", "", "C", "TLRB", 0)); }
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