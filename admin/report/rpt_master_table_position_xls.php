<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
        // 19/07/2560  15.23
	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	ini_set("max_execution_time", $max_execution_time);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$row = 3;
	if ($MUNICIPALITY_FLAG==1) {
		$row = 4;
	}
	function print_header($xlsRow){
		global $worksheet;
		global $POS_NO_TITLE, $PM_TITLE, $PL_TITLE, $CL_TITLE, $DEPARTMENT_TITLE, $ORG_TITLE, 
                        $ORG_TITLE1, $ORG_TITLE2,$ORG_TITLE3,$ORG_TITLE4,$ORG_TITLE5,
                        $PT_TITLE, $PT_TITLE1, $PT_TITLE2, $LEVEL_TITLE, $ACTIVE_TITLE, $NUMBER_DISPLAY, $row,
                        $ORG_SETLEVEL;

		$worksheet->set_column(0, 0, 10);
		$worksheet->set_column(1, 1, 25);
		$worksheet->set_column(2, 2, 25);
		$worksheet->set_column(3, 3, 20);
		if ($MUNICIPALITY_FLAG==1) {
			$worksheet->set_column(4, 4, 15);
		}
		$worksheet->set_column($row+1, $row+1, 15);
		$worksheet->set_column($row+2, $row+2, 20);
		$worksheet->set_column($row+3, $row+3, 20);
                
		$worksheet->set_column($row+4, $row+4, 20);/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
		$worksheet->set_column($row+5, $row+5, 20);/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->set_column($row+6, $row+6, 20);/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->set_column($row+7, $row+7, 20);/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->set_column($row+8, $row+8, 20);/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                

		$worksheet->set_column($row+$plus+6, $row+$plus+6, 20);
		$worksheet->set_column($row+$plus+7, $row+$plus+7, 20);
		$worksheet->set_column($row+$plus+8, $row+$plus+8, 20);/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
		$worksheet->set_column($row+$plus+9, $row+$plus+9, 20);/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                $plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->set_column($row+$plus+10, $row+$plus+10, 20);/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=$plus+1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->set_column($row+$plus+11, $row+$plus+11, 20);/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=$plus+2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->set_column($row+$plus+12, $row+$plus+12, 20);/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=$plus+3;
                        }
                    }
                    
                }
                $plus=$plus_ass;
		$worksheet->set_column($row+$plus+10, $row+$plus+10, 20);
		$worksheet->set_column($row+$plus+11, $row+$plus+11, 15);
		$worksheet->set_column($row+$plus+12, $row+$plus+12, 15);
		$worksheet->set_column($row+$plus+13, $row+$plus+13, 20);
		$worksheet->set_column($row+$plus+14, $row+$plus+14, 50);
		$worksheet->set_column($row+$plus+15, $row+$plus+15, 15);
		$worksheet->set_column($row+$plus+16, $row+$plus+16, 10);
		$worksheet->set_column($row+$plus+17, $row+$plus+17, 20);
		$worksheet->set_column($row+$plus+18, $row+$plus+18, 15);
		
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		if ($MUNICIPALITY_FLAG==1) {
			$worksheet->write($xlsRow, 4, "$PT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		}
		$worksheet->write($xlsRow, $row+1, "$PT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+2, "", set_format("xlsFmtTableHeader", "B", "C", "TL", 0));
		$worksheet->write($xlsRow, $row+3, "ตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "T", 0));
                
		$worksheet->write($xlsRow, $row+4, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่า 1 ระดับ*/
		$worksheet->write($xlsRow, $row+5, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่า 2 ระดับ*/
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, $row+6, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่า 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, $row+7, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่า 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, $row+8, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่า 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }

		$worksheet->write($xlsRow, $row+$plus+6, "", set_format("xlsFmtTableHeader", "B", "C", "TL", 0));
		$worksheet->write($xlsRow, $row+$plus+7, "ตามมอบหมายงาน", set_format("xlsFmtTableHeader", "B", "C", "T", 0));
		
                $worksheet->write($xlsRow, $row+$plus+8, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่า 1 ระดับ*/
		$worksheet->write($xlsRow, $row+$plus+9, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่า 2 ระดับ*/
                $plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, $row+$plus+10, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=$plus+1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, $row+$plus+11, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=$plus+2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableHeader", "B", "C", "TR", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=$plus+3;
                        }
                    }
                    
                }
                $plus=$plus_ass;
                
		$worksheet->write($xlsRow, $row+$plus+10, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+11, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+13, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+14, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+15, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+16, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+17, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+18, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

		$xlsRow++;
		$worksheet->write($xlsRow, 0, "$POS_NO_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 1, "$PM_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 2, "$PL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, 3, "$CL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		if ($MUNICIPALITY_FLAG==1) {
			$worksheet->write($xlsRow, 4, (($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE1"):"$PT_TITLE1"), set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		}
		$worksheet->write($xlsRow, $row+1, (($NUMBER_DISPLAY==2)?convert2thaidigit("$PT_TITLE2"):"$PT_TITLE2"), set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+2, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+3, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		
                $worksheet->write($xlsRow, $row+4, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 1 ระดับ*/
		$worksheet->write($xlsRow, $row+5, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 2 ระดับ*/
		$plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, $row+6, "$ORG_TITLE3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, $row+7, "$ORG_TITLE4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 3 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, $row+8, "$ORG_TITLE5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 3 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                $worksheet->write($xlsRow, $row+$plus+6, "$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $row+$plus+7, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                
		$worksheet->write($xlsRow, $row+$plus+8, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 1 ระดับ*/
		$worksheet->write($xlsRow, $row+$plus+9, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่า 2 ระดับ*/
		$plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, $row+$plus+10, "$ORG_TITLE3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=$plus+1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, $row+$plus+11, "$ORG_TITLE4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=$plus+2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, $row+$plus+12, "$ORG_TITLE5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=$plus+3;
                        }
                    }
                    
                }
                $plus=$plus_ass;
                $worksheet->write($xlsRow, $row+$plus+10, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+11, "วันที่ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+12, "$LEVEL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+13, "รายละเอียดเงื่อนไขตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+14, "หมายเหตุ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+15, "อัตราเงินเดือนถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+16, "$ACTIVE_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+17, "ผู้ครองถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		$worksheet->write($xlsRow, $row+$plus+18, "วันที่ครองถือจ่าย", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
	} // end if
	
	if($DPISDB=="odbc") $POS_NO_NUM = "CLng(POS_NO)";
	elseif($DPISDB=="oci8") $POS_NO_NUM = "to_number(replace(POS_NO,'-',''))";
	elseif($DPISDB=="mysql") $POS_NO_NUM = "POS_NO+0";
	if(!$sort_by) $sort_by=1;
	$sort_type = (isset($sort_type))?  $sort_type : "1:asc";
	$arrSort=explode(":",$sort_type);
	$SortType[$arrSort[0]]	=$arrSort[1];
	if(!$order_by) $order_by=1;
	if($order_by==1){
		$org_cond = "";
		if ($POSITION_NO_CHAR=="Y") $org_cond = ", b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by]";
		if($DPISDB=="odbc")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], iif(isnull(POS_NO),0,$POS_NO_NUM) $SortType[$order_by]";
		if($DPISDB=="oci8")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
		if($DPISDB=="mysql")	$order_str = "a.DEPARTMENT_ID $SortType[$order_by]".$org_cond.", POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
  	}elseif($order_by==2) {
		$order_str = "PM_NAME $SortType[$order_by]";
  	} elseif($order_by==3){
		$order_str = "PL_NAME $SortType[$order_by]";
  	} elseif($order_by==4) {
		$order_str = "a.CL_NAME $SortType[$order_by]";
	} else 	if($order_by==5){
		$order_str = "a.LEVEL_NO $SortType[$order_by]";
  	}elseif($order_by==6) {
		$order_str = "a.DEPARTMENT_ID $SortType[$order_by], b.ORG_SEQ_NO $SortType[$order_by], b.ORG_CODE $SortType[$order_by], POS_NO_NAME $SortType[$order_by], $POS_NO_NUM $SortType[$order_by]";
  	} elseif($order_by==7){
		$order_str = "b.ORG_NAME $SortType[$order_by]";
  	} elseif($order_by==8) {
		$order_str = "a.ORG_ID_1 $SortType[$order_by]";
	} elseif($order_by==9) {	
		$order_str = "a.POS_SEQ_NO $SortType[$order_by]";
	}		
  	if(trim($search_pos_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) >= $search_pos_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) >= $search_pos_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO >= $search_pos_no_min)";
	} // end if
  	if(trim($search_pos_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POS_NO) <= $search_pos_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POS_NO,'-','')) <= $search_pos_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POS_NO <= $search_pos_no_max)";
	} // end if
	if(trim($search_pos_no_min) && trim($search_pos_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : $search_pos_no_min ถึง $search_pos_no_max";
	}elseif(trim($search_pos_no_min)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : มากกว่า $search_pos_no_min";
	}elseif(trim($search_pos_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : น้อยกว่า $search_pos_no_max";
	} // end if
  	if(trim($search_pay_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) >= $search_pay_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) >= $search_pay_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 >= $search_pay_no_min)";
	} // end if
  	if(trim($search_pay_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(PAY_NO) <= $search_pay_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(PAY_NO,'-','')) <= $search_pay_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(PAY_NO+0 <= $search_pay_no_max)";
	} // end if
	if(trim($search_pl_code)){ 
		$arr_search_condition[] = "(trim(PL_CODE) = '". trim($search_pl_code) ."')";
		$print_search_condition[] = "$PL_TITLE : $search_pl_name";
	} // end if
	if(trim($search_pm_code)){ 
		$arr_search_condition[] = "(trim(PM_CODE) = '". trim($search_pm_code) ."')";
		$print_search_condition[] = "$PM_TITLE : $search_pm_name";
	} // end if
	if(trim($search_cl_name)){ 
		$arr_search_condition[] = "(trim(CL_NAME) = '". trim($search_cl_name) ."')";
		$print_search_condition[] = "$CL_TITLE : $search_cl_name";
	} // end if
	if(trim($search_level_no)){ 
		$arr_search_condition[] = "(trim(LEVEL_NO) = '". trim($search_level_no) ."')";
		$print_search_condition[] = "$LEVEL_TITLE : $search_level_no";
	} // end if
	if(trim($search_pt_code)){ 
		$arr_search_condition[] = "(trim(PT_CODE) = '". trim($search_pt_code) ."')";
		$print_search_condition[] = "$PT_TITLE : $search_pt_name";
	} // end if
	if(trim($search_skill_code)){ 
		$arr_search_condition[] = "(trim(SKILL_CODE) = '". trim($search_skill_code) ."')";
		$print_search_condition[] = "$SKILL_TITLE : $search_skill_name";
	} // end if
	if(trim($search_pc_code)){ 
		$arr_search_condition[] = "(trim(PC_CODE) = '". trim($search_pc_code) ."')";
		$print_search_condition[] = "$PC_TITLE : $search_pc_name";
	} // end if

	if(trim($search_org_id)){ 
		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
		$print_search_condition[] = "$ORG_TITLE : $search_org_name";
	}elseif(trim($search_department_id)){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
		$print_search_condition[] = "$DEPARTMENT_TITLE : $search_department_name";
	}elseif(trim($search_ministry_id)){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
	} // end if
	
	if(trim($search_org_id_1)){ 
		$arr_search_condition[] = "(ORG_ID_1 = $search_org_id_1)";
		$print_search_condition[] = "$ORG_TITLE1 : $search_org_name_1";
	} // end if
	if(trim($search_org_id_2)){ 
		$arr_search_condition[] = "(ORG_ID_2 = $search_org_id_2)";
		$print_search_condition[] = "$ORG_TITLE2 : $search_org_name_2";
	} // end if
        if(trim($search_org_id_3)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_3 = $search_org_id_3)";
		$print_search_condition[] = "$ORG_TITLE3 : $search_org_name_3";
	} 
        if(trim($search_org_id_4)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_4 = $search_org_id_4)";
		$print_search_condition[] = "$ORG_TITLE4 : $search_org_name_4";
	} 
        if(trim($search_org_id_5)){ /*Release 5.1.0.4*/
		$arr_search_condition[] = "(ORG_ID_5 = $search_org_id_5)";
		$print_search_condition[] = "$ORG_TITLE5 : $search_org_name_5";
	} 
        
        
	if(trim($search_ct_code)){ 
		$arr_search_condition[] = "(trim(b.CT_CODE) = '". trim($search_ct_code) ."')";
		$print_search_condition[] = "ประเทศ : $search_ct_name";
	} // end if
	if(trim($search_pv_code)){ 
		$arr_search_condition[] = "(trim(b.PV_CODE) = '". trim($search_pv_code) ."')";
		$print_search_condition[] = "จังหวัด : $search_pv_name";
	} // end if
	if(trim($search_ap_code)){
		$arr_search_condition[] = "(trim(b.AP_CODE) = '". trim($search_ap_code) ."')";
		$print_search_condition[] = "อำเภอ : $search_ap_name";
	} // end if
	if(trim($search_ot_code)){ 
		$arr_search_condition[] = "(trim(b.OT_CODE) = '". trim($search_ot_code) ."')";
		$print_search_condition[] = "สังกัด : $search_ot_name";
	} // end if

	if(trim($search_pr_code)){ 
		$arr_search_condition[] = "(trim(a.PR_CODE) = '". trim($search_pr_code) ."')";
		$print_search_condition[] = "สงวนตำแหน่ง : $search_pr_name";
	} // end if

	if(trim($search_pos_date_min)){
		$search_pos_date_min =  save_date($search_pos_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) >= '$search_pos_date_min')";
		$search_pos_date_min = show_date_format($search_pos_date_min, 1);
		$show_search_pos_date_min = show_date_format($search_pos_date_min, 2);
	} // end if
	if(trim($search_pos_date_max)){
		$search_pos_date_max =  save_date($search_pos_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_DATE, 1, 10) <= '$search_pos_date_max')";
		$search_pos_date_max = show_date_format($search_pos_date_max, 1);
		$show_search_pos_date_max = show_date_format($search_pos_date_max, 2);
	} // end if
	if(trim($search_pos_date_min) && trim($search_pos_date_max)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : $show_search_pos_date_min ถึง $show_search_pos_date_max";
	}elseif(trim($search_pos_date_min)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : ตั้งแต่ $show_search_pos_date_min";
	}elseif(trim($search_pos_date_max)){
		$print_search_condition[] = "วันที่ ก.พ. กำหนดตำแหน่ง : ก่อน $show_search_pos_date_max";
	} // end if
	if(trim($search_pos_change_date_min)){
		$search_pos_change_date_min =  save_date($search_pos_change_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) >= '$search_pos_change_date_min')";
		$search_pos_change_date_min = show_date_format($search_pos_change_date_min, 1);
		$show_search_pos_change_date_min = show_date_format($search_pos_change_date_min, 2);
	} // end if
	if(trim($search_pos_change_date_max)){
		$search_pos_change_date_max =  save_date($search_pos_change_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_CHANGE_DATE, 1, 10) <= '$search_pos_change_date_max')";
		$search_pos_change_date_max = show_date_format($search_pos_change_date_max, 1);
		$show_search_pos_change_date_max = show_date_format($search_pos_change_date_max, 2);
	} // end if
	if(trim($search_pos_change_date_min) && trim($search_pos_change_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : $show_search_pos_change_date_min ถึง $show_search_pos_change_date_max";
	}elseif(trim($search_pos_change_date_min)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : ตั้งแต่ $show_search_pos_change_date_min";
	}elseif(trim($search_pos_change_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งเปลี่ยนสถานภาพ : ก่อน $show_search_pos_change_date_max";
	} // end if
	if(trim($search_pos_vacant_date_min)){
		$search_pos_vacant_date_min =  save_date($search_pos_vacant_date_min);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) >= '$search_pos_vacant_date_min')";
		$search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 1);
		$show_search_pos_vacant_date_min = show_date_format($search_pos_vacant_date_min, 2);
	} // end if
	if(trim($search_pos_vacant_date_max)){
		$search_pos_vacant_date_max =  save_date($search_pos_vacant_date_max);
		if($DPISDB=="odbc") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(Mid(POS_VACANT_DATE, 1, 10) <= '$search_pos_vacant_date_max')";
		$search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 1);
		$show_search_pos_vacant_date_max = show_date_format($search_pos_vacant_date_max, 2);
	} // end if
	if(trim($search_pos_vacant_date_min) && trim($search_pos_vacant_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : $show_search_pos_vacant_date_min ถึง $show_search_pos_vacant_date_max";
	}elseif(trim($search_pos_vacant_date_min)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : ตั้งแต่ $show_search_pos_vacant_date_min";
	}elseif(trim($search_pos_vacant_date_max)){
		$print_search_condition[] = "วันที่ตำแหน่งว่าง : ก่อน $show_search_pos_vacant_date_max";
	} // end if
  	if(trim($search_pos_salary_min)) $arr_search_condition[] = "(POS_SALARY >= $search_pos_salary_min)";
  	if(trim($search_pos_salary_max)) $arr_search_condition[] = "(POS_SALARY <= $search_pos_salary_max)";
	if(trim($search_pos_salary_min) && trim($search_pos_salary_max)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : $search_pos_salary_min ถึง $search_pos_salary_max";
	}elseif(trim($search_pos_salary_min)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : มากกว่า $search_pos_salary_min";
	}elseif(trim($search_pos_salary_max)){
		$print_search_condition[] = "อัตราเงินเดือนถือจ่าย : น้อยกว่า $search_pos_salary_max";
	} // end if
	if(trim($search_pos_situation) == 1){ 
		$arr_search_condition[] = "(c.PER_STATUS IS NULL)";
		$print_search_condition[] = "สถานภาพของตำแหน่ง : ว่าง";
	} // end if
	if(trim($search_pos_situation) == 2){ 
		$arr_search_condition[] = "(c.PER_STATUS=1)";
		$print_search_condition[] = "สถานภาพของตำแหน่ง : มีคนครอง";
	} // end if
	if(trim($search_pay_situation) == 1){ 
		$arr_search_condition[] = "(e.PER_STATUS IS NULL)";
		$print_search_condition[] = "สถานภาพของถือจ่าย : ว่าง";
	} // end if
	if(trim($search_pay_situation) == 2){ 
		$arr_search_condition[] = "(e.PER_STATUS=1)";
		$print_search_condition[] = "สถานภาพของถือจ่าย : มีคนครอง";
	} // end if

	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POS_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POS_STATUS = 2)";
	if($search_pos_status == 0){
		$print_search_condition[] = "สถานะ : ทั้งหมด";
	}elseif($search_pos_status == 1){
		$print_search_condition[] = "สถานะ : ใช้งาน";
	}elseif($search_pos_status == 2){
		$print_search_condition[] = "สถานะ : ยกเลิก";
	} // end if
  	if(trim($search_pos_reserve)) $arr_search_condition[] = "(POS_RESERVE IS NOT NULL or POS_RESERVE2 IS NOT NULL)";
  	if(trim($search_pos_reserve1)) $arr_search_condition[] = "(POS_RESERVE like '$search_pos_reserve1%')";
  	if(trim($search_pos_reserve2)) $arr_search_condition[] = "(POS_RESERVE2 like '$search_pos_reserve2%')";

	$check_condition = "";
	if ($search_data==1) 
		$check_condition = " and (a.LEVEL_NO is NULL) ";
	elseif ($search_data==2)
		$check_condition = " and (a.PL_CODE in (select d.PL_CODE from PER_LINE d where a.PL_CODE = d.PL_CODE and a.LEVEL_NO not between LEVEL_NO_MIN and LEVEL_NO_MAX)) ";

	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID and f.PER_TYPE=1 and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, IIf(IsNull(a.POS_NO), 0, CLng(a.POS_NO)), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by $order_str ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "	select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')) as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
											a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2,
                                                                                        a.ORG_ID_3,a.ORG_ID_4,a.ORG_ID_5, a.POS_STATUS, 
											c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
											f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
							from 		PER_POSITION a, PER_ORG b, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) c, 
											(select POS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) d,
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and PER_STATUS=1) e, 
											(select PAY_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=1 and (PER_STATUS=0)) f
							where 	a.ORG_ID=b.ORG_ID and a.POS_ID=c.POS_ID(+) and a.POS_ID=d.POS_ID(+) and a.POS_ID=e.PAY_ID(+) and a.POS_ID=f.PAY_ID(+)
											$search_condition $check_condition
							group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, to_number(replace(POS_NO,'-','')), PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
											 a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2,a.ORG_ID_3,a.ORG_ID_4,a.ORG_ID_5, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
											e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
							order by $order_str ";
	}elseif($DPISDB=="mysql"){
		$cmd =" select 		a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO as POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, 
										a.ORG_ID, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, 
										c.PER_STATUS as PER_STATUS1, d.PER_STATUS as PER_STATUS2, e.PER_STATUS as PER_STATUS3, 
										f.PER_STATUS as PER_STATUS4, a.POS_CONDITION, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						from (
									(
										(
											(
												PER_POSITION a
												inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
											) left join PER_PERSONAL c on (a.POS_ID=c.POS_ID and c.PER_TYPE=1 and c.PER_STATUS=1)
										) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and (d.PER_STATUS=0))
									) left join PER_PERSONAL e on (a.POS_ID=e.PAY_ID and e.PER_TYPE=1 and e.PER_STATUS=1)
								) left join PER_PERSONAL f on (a.POS_ID=f.PAY_ID  and f.PER_TYPE=1and (f.PER_STATUS=0))
						$search_condition $check_condition
						group by a.POS_ID, a.DEPARTMENT_ID, a.POS_NO_NAME, a.POS_NO, PM_CODE, PL_CODE, CL_NAME, PT_CODE, a.POS_CONDITION,
										a.ORG_ID, b.ORG_SEQ_NO, b.ORG_CODE, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, a.POS_STATUS, c.PER_STATUS, d.PER_STATUS, 
										e.PER_STATUS, f.PER_STATUS, a.POS_REMARK, a.LEVEL_NO, a.PAY_NO, a.POS_SALARY
						order by $order_str ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "$cmd<br>";
//die('<pre>'.$cmd);
	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                
		$worksheet->write($xlsRow, 9+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 10+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 11+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
		$worksheet->write($xlsRow, 12+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                $plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 13+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 14+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 15+$plus, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=3;
                        }
                    }
                }
                
		$worksheet->write($xlsRow, 13+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 14+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($MUNICIPALITY_FLAG==1) {
			$worksheet->write($xlsRow, 22+$plus_ass, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow+1;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POS_ID = trim($data[POS_ID]);
			$POS_NO = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
			$PAY_NO = trim($data[PAY_NO]);
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			$PM_CODE = trim($data[PM_CODE]);
			$PL_CODE = trim($data[PL_CODE]);
			$PT_CODE = trim($data[PT_CODE]);
			$CL_NAME = trim($data[CL_NAME]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$ORG_ID_2 = trim($data[ORG_ID_2]);
                        $ORG_ID_3 = trim($data[ORG_ID_3]);
                        $ORG_ID_4 = trim($data[ORG_ID_4]);
                        $ORG_ID_5 = trim($data[ORG_ID_5]);
                        
			$POS_CONDITION=  substr(trim($data[POS_CONDITION]),0,255) ;
			$POS_SALARY=trim($data[POS_SALARY]);
			$POS_REMARK = substr(trim($data[POS_REMARK]),0,255);
			$LEVEL_NO=trim($data[LEVEL_NO]);
			$POS_STATUS = (trim($data[POS_STATUS])==1)?"ใช้งาน":"ยกเลิก";
			//$POS_STATUS = (trim($data[POS_STATUS])==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";
	
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='".$PM_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = $data_dpis2[PM_NAME];
	
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='".$PL_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = $data_dpis2[PL_NAME];
	
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='".$PT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = $data_dpis2[PT_NAME];
			$DEPARTMENT_NAME = "";
			if($DEPARTMENT_ID){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_2 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_3 = "";
			if($ORG_ID_3){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_3 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_4 = "";
			if($ORG_ID_4){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_4 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_5 = "";
			if($ORG_ID_5){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_5 = $data_dpis2[ORG_NAME];
			}
                                               
                        
                        
                        
                        
                        
			if (!$LEVEL_NO) {
				$cmd = " select LEVEL_NO_MIN from PER_CO_LEVEL where trim(CL_NAME)='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$LEVEL_NO = $data_dpis2[LEVEL_NO_MIN];
			}
		
			$cmd = "select LEVEL_NAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
			$NEW_POSITION_TYPE = $data_dpis2[POSITION_TYPE];
			$POSITION_LEVEL = $data_dpis2[POSITION_LEVEL];

			$arr_temp = explode(" ", $LEVEL_NAME);
			$LEVEL_NAME =  $arr_temp[1];
		
			if($PAY_NO && $SESS_DEPARTMENT_NAME=="กรมการปกครอง"){
				if($DPISDB=="odbc"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		PER_POSITION a, PER_PERSONAL b, PER_PRENAME c
										where		a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PN_CODE=c.PN_CODE(+) and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
										from		
													(
														PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
													) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
										where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
//				echo "$cmd<br>";
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);
				$PAYNAME = (trim($data2[PN_NAME])?($data2[PN_NAME]):"") . $data2[PER_NAME] ." ". $data2[PER_SURNAME];

				$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID_PAY and SAH_PAY_NO='$PAY_NO'
								order by SAH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PAY_DATE = show_date_format($data2[SAH_EFFECTIVEDATE], 1);
			} // end if

			$cnt = 0;
			$PER_ID = $FULLNAME = $POH_DATE = "";
                        $DEPARTMENT_NAME_ASS = "";
                        $ORG_NAME_ASS = "";
                        $ORG_NAME_1_ASS = "";
                        $ORG_NAME_2_ASS = "";
                        
			$cmd = "	select PER_ID, PER_STATUS from	PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE = 1 ";
			$db_dpis1->send_cmd($cmd);
//echo ">>>".$cmd."<br><br>";
//			$db_dpis1->show_error();
                        $getCnt=0;  /* Release 5.1.0.8 Begin*/
			while($data1 = $db_dpis1->get_array()){
				$cnt++;
				$PER_ID[$cnt] = trim($data1[PER_ID]);
				$PER_STATUS = $data1[PER_STATUS];
				if($PER_ID[$cnt] && $PER_STATUS != 2){
                                    $getCnt++; /* Release 5.1.0.8 Begin*/
					$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS, DEPARTMENT_ID_ASS, ORG_ID, 
                                            ORG_ID_1, ORG_ID_2,ORG_ID_3,ORG_ID_4,ORG_ID_5
									 from 		PER_PERSONAL a, PER_PRENAME b 
									 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] ";
					
                                       // echo $cmd."<br><br>";
                                        $db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
                                        
					/*เดิม*/
                                        /*$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");*/
                                        /* Release 5.1.0.8 Begin*/
                                        $FULLNAME[$getCnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
					$FULLNAME[$getCnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
                                        /* Release 5.1.0.8 End*/
					$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
					$DEPARTMENT_ID_ASS = trim($data_dpis2[DEPARTMENT_ID_ASS]);
					$ORG_ID_ASS = trim($data_dpis2[ORG_ID]);
					$ORG_ID_1_ASS = trim($data_dpis2[ORG_ID_1]);
					$ORG_ID_2_ASS = trim($data_dpis2[ORG_ID_2]);
                                        $ORG_ID_3_ASS = trim($data_dpis2[ORG_ID_3]);
                                        $ORG_ID_4_ASS = trim($data_dpis2[ORG_ID_4]);
                                        $ORG_ID_5_ASS = trim($data_dpis2[ORG_ID_5]);

					$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$DEPARTMENT_ID_ASS'";*//*เดิม*/
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$DEPARTMENT_ID_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$DEPARTMENT_NAME_ASS = $data_dpis2[ORG_NAME];
                                        
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_ASS'";*//*เดิม*/
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_ASS = $data_dpis2[ORG_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_1_ASS'";*//*เดิม*/
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_1_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_1_ASS = $data_dpis2[ORG_NAME];
		
					/*$cmd = "select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID_2_ASS'";*//*เดิม*/
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_2_ASS'";/*Release 5.1.0.4*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_2_ASS = $data_dpis2[ORG_NAME];
                                        
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_3_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_3_ASS = $data_dpis2[ORG_NAME];
                                        
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_4_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_4_ASS = $data_dpis2[ORG_NAME];
                                        
                                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_5_ASS'";/*Release 5.1.0.8*/
					$db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$ORG_NAME_5_ASS = $data_dpis2[ORG_NAME];
                                        
                                        
                                        
                                        
                                        
                                        
		
					$arr_temp = explode(" ", $LEVEL_NAME);
					$LEVEL_NAME =  $arr_temp[1];
					$POS_SITUATION = 3;
			
					if (!$RPT_N) {
						$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' ";
						$count_mgtsalary = $db_dpis2->send_cmd($cmd);
						$data_dpis2 = $db_dpis2->get_array();
						$POS_MGTSALARY = $data_dpis2[MS_SALARY];
					}

					$cmd = " select POH_EFFECTIVEDATE, POH_POS_NO_NAME, POH_POS_NO, POH_ORG2
									from   PER_POSITIONHIS
									where PER_ID=$PER_ID[$cnt]
									order by POH_EFFECTIVEDATE desc, POH_SEQ_NO DESC ";
					$db_dpis2->send_cmd($cmd);
					while($data_dpis2 = $db_dpis2->get_array()){
						$POH_POS_NO_NAME = trim($data_dpis2[POH_POS_NO_NAME]);
						$POH_POS_NO = trim($data_dpis2[POH_POS_NO]);
						$POH_ORG2 = trim($data_dpis2[POH_ORG2]);
						if ($POH_POS_NO_NAME.$POH_POS_NO==$POS_NO && $POH_ORG2==$DEPARTMENT_NAME) 
							$POH_EFFECTIVEDATE = trim($data_dpis2[POH_EFFECTIVEDATE]);
						else break;
					}
					if ($POH_EFFECTIVEDATE) {	
						//$POH_DATE[$cnt] = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10), 1); /*เดิม*/
                                           $POH_DATE[$getCnt] = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10), 1); /* Release 5.1.0.8 Begin*/
					}
				}else{
					$POS_SITUATION = 1;
				} // end if
			} // end while
                        
//echo "1:".$FULLNAME[1]."<br>";
//echo "2:".$FULLNAME[2]."<br>";
//echo "3:".$FULLNAME[3]."<br>";
//echo "4:".$FULLNAME[4]."<br>";
//die();
			if ($MUNICIPALITY_FLAG==1) $POS_NO = pos_no_format($POS_NO,2);
			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_NO"):"$POS_NO"), set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$PL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 3, "$CL_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			if ($MUNICIPALITY_FLAG==1) {
				$worksheet->write($xlsRow, 4, "$PT_NAME", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			}
			$worksheet->write($xlsRow, $row+1, "$NEW_POSITION_TYPE", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, $row+2, "$DEPARTMENT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+3, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        
			$worksheet->write($xlsRow, $row+4, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 1 ระดับ*/
			$worksheet->write($xlsRow, $row+5, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 2 ระดับ*/
                        $plus=0;
                        if($ORG_SETLEVEL>2){
                            $worksheet->write($xlsRow, $row+6, "$ORG_NAME_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 3 ระดับ*/
                            $plus=1;
                            if($ORG_SETLEVEL>3){
                                $worksheet->write($xlsRow, $row+7, "$ORG_NAME_4", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 4 ระดับ*/
                                $plus=2;
                                if($ORG_SETLEVEL>4){
                                    $worksheet->write($xlsRow, $row+8, "$ORG_NAME_5", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 5 ระดับ*/
                                    $plus=3;
                                }
                            }
                        }
                        
			$worksheet->write($xlsRow, $row+$plus+6, "$DEPARTMENT_NAME_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+7, "$ORG_NAME_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+8, "$ORG_NAME_1_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 1 ระดับ*/
			$worksheet->write($xlsRow, $row+$plus+9, "$ORG_NAME_2_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 2 ระดับ*/
                        
                        
                        $plus_ass=0;
                        if($ORG_SETLEVEL>2){
                            $worksheet->write($xlsRow, $row+$plus+10, "$ORG_NAME_3_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 3 ระดับ*/
                            $plus_ass=$plus+1;
                            if($ORG_SETLEVEL>3){
                                $worksheet->write($xlsRow, $row+$plus+11, "$ORG_NAME_4_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 4 ระดับ*/
                                $plus_ass=$plus+2;
                                if($ORG_SETLEVEL>4){
                                    $worksheet->write($xlsRow, $row+$plus+12, "$ORG_NAME_5_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่า 5 ระดับ*/
                                    $plus_ass=$plus+3;
                                }
                            }
                        }
                        $plus=$plus_ass;
			$worksheet->write($xlsRow, $row+$plus+10, "$FULLNAME[1]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+11, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POH_DATE[1]"):"$POH_DATE[1]"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+12, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+13, "$POS_CONDITION", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+14, "$POS_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+15, (($NUMBER_DISPLAY==2)?convert2thaidigit("$POS_SALARY"):"$POS_SALARY"), set_format("xlsFmtTableDetail", "", "R", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+16, "$POS_STATUS", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			//$worksheet->insert_bitmap($xlsRow, $row+$plus+16, "$POS_STATUS", 35, 4, 1, 0.8);
			$worksheet->write($xlsRow, $row+$plus+17, "$PAYNAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $row+$plus+18, (($NUMBER_DISPLAY==2)?convert2thaidigit("$PAY_DATE"):"$PAY_DATE"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));

			if ($FULLNAME[2]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				if ($MUNICIPALITY_FLAG==1) {
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				}
				$worksheet->write($xlsRow, $row+1, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, $row+2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus=1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus=2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus=3;
                                        }
                                    }
                                }
				$worksheet->write($xlsRow, $row+$plus+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+$plus+9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus_ass=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+$plus+10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus_ass=$plus+1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+$plus+11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus_ass=$plus+2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus_ass=$plus+3;
                                        }
                                    }
                                }
                                $plus=$plus_ass;
				$worksheet->write($xlsRow, $row+$plus+10, "$FULLNAME[2]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+11, "$POH_DATE[2]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+13, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+15, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+16, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+17, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+18, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}

			if ($FULLNAME[3]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				if ($MUNICIPALITY_FLAG==1) {
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				}
				$worksheet->write($xlsRow, $row+1, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, $row+2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus=1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus=2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus=3;
                                        }
                                    }
                                }
				$worksheet->write($xlsRow, $row+$plus+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+$plus+9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus_ass=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+$plus+10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus_ass=$plus+1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+$plus+11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus_ass=$plus+2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus_ass=$plus+3;
                                        }
                                    }
                                }
                                $plus=$plus_ass;
				$worksheet->write($xlsRow, $row+$plus+10, "$FULLNAME[3]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+11, "$POH_DATE[3]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+13, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+15, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+16, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+17, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+18, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}

			if ($FULLNAME[4]) {
				$data_count++;
				$xlsRow = $data_count;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				if ($MUNICIPALITY_FLAG==1) {
					$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				}
				$worksheet->write($xlsRow, $row+1, "", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
				$worksheet->write($xlsRow, $row+2, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+3, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+4, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+5, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus=1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus=2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus=3;
                                        }
                                    }
                                }
				$worksheet->write($xlsRow, $row+$plus+6, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+7, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+8, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ*/
				$worksheet->write($xlsRow, $row+$plus+9, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ*/
                                $plus_ass=0;
                                if($ORG_SETLEVEL>2){
                                    $worksheet->write($xlsRow, $row+$plus+10, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                                    $plus_ass=$plus+1;
                                    if($ORG_SETLEVEL>3){
                                        $worksheet->write($xlsRow, $row+$plus+11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                        $plus_ass=$plus+2;
                                        if($ORG_SETLEVEL>4){
                                            $worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                            $plus_ass=$plus+3;
                                        }
                                    }
                                }
                                $plus=$plus_ass;
				$worksheet->write($xlsRow, $row+$plus+10, "$FULLNAME[4]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+11, "$POH_DATE[4]", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+12, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+13, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+14, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+15, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+16, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+17, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
				$worksheet->write($xlsRow, $row+$plus+18, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			}
		} // end while
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
		$worksheet->write($xlsRow, 14, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 15, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 16, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 17, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 18, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 19, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 20, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 21, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		if ($MUNICIPALITY_FLAG==1) {
			$worksheet->write($xlsRow, 22, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_title.xls\"");
	header("Content-Disposition: inline; filename=\"$report_title.xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>