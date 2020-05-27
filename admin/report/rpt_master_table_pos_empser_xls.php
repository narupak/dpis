<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	//ini_set("max_execution_time", $max_execution_time);
        ini_set("max_execution_time", 0);
        //set_time_limit(900);

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("Sheet1");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header($xlsRow){
		global $worksheet, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2, $REMARK_TITLE;
                global $ORG_TITLE3,$ORG_TITLE4,$ORG_TITLE5,$ORG_SETLEVEL;
		$worksheet->set_column(0, 0, 12);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 20);
		$worksheet->set_column(3, 3, 30);
		$worksheet->set_column(4, 4, 30);
		$worksheet->set_column(5, 5, 30);
		$worksheet->set_column(6, 6, 30);
		$worksheet->set_column(7, 7, 30);
		$worksheet->set_column(8, 8, 30);/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->set_column(9, 9, 30);/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->set_column(10, 10, 30);/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->set_column(11, 11, 30);/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->set_column(12, 12, 30);/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                //ตามมอบหมาย
                $worksheet->set_column(10+$plus, 10+$plus, 30);/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->set_column(11+$plus, 11+$plus, 30);/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->set_column(12+$plus, 12+$plus, 30);/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=$plus+1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->set_column(13+$plus, 13+$plus, 30);/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=$plus+2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->set_column(14+$plus, 14+$plus, 30);/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=$plus+3;
                        }
                    }
                }
                
                $plus=$plus_ass;
		$worksheet->set_column($plus+12, $plus+12, 30);
		$worksheet->set_column($plus+13, $plus+13, 30);
		$worksheet->set_column($plus+14, $plus+14, 20);
		$worksheet->set_column($plus+15, $plus+15, 30);
		$worksheet->set_column($plus+16, $plus+16, 10);
		
                
                $worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow+1, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
		
                $worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 1, "กลุ่มงาน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 2, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 3, "ประเภทภารกิจ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 4, "ประเภทกรอบอัตรากำลัง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 5, "สถานะของตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, 6, "อัตราค่าจ้าง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));
                $worksheet->write($xlsRow+1, 7, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                
		$worksheet->write($xlsRow, 8, "ตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
                $worksheet->write($xlsRow+1, 8, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
                
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $worksheet->write($xlsRow+1, 9, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $worksheet->write($xlsRow+1, 10, "$ORG_TITLE3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $worksheet->write($xlsRow+1, 11, "$ORG_TITLE4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $worksheet->write($xlsRow+1, 12, "$ORG_TITLE5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                
                $worksheet->write($xlsRow, 10+$plus, "ตามมอบหมาย", set_format("xlsFmtTableHeader", "B", "C", "TL", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
                $worksheet->write($xlsRow+1, 10+$plus, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
                
		$worksheet->write($xlsRow, 11+$plus, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $worksheet->write($xlsRow+1, 11+$plus, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                
                $worksheet->write($xlsRow, 12+$plus, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $worksheet->write($xlsRow+1, 12+$plus, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                
                $plus_ass=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 13+$plus, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $worksheet->write($xlsRow+1, 13+$plus, "$ORG_TITLE3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus_ass=$plus+1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 14+$plus, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $worksheet->write($xlsRow+1, 14+$plus, "$ORG_TITLE4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus_ass=$plus+2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 15+$plus, "", set_format("xlsFmtTableHeader", "B", "C", "T", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $worksheet->write($xlsRow+1, 15+$plus, "$ORG_TITLE5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus_ass=$plus+3;
                        }
                    }
                }
                $plus =$plus_ass;
                
		$worksheet->write($xlsRow, $plus+13, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, $plus+13, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				
		$worksheet->write($xlsRow, $plus+14, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, $plus+14, "อัตราเงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, $plus+15, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, $plus+15, "$REMARK_TITLE", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, $plus+16, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, $plus+16, "ทักษะประสบการณ์", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
                
		$worksheet->write($xlsRow, $plus+17, "", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow+1, $plus+17, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
	} // end if
	
  	if(trim($search_poem_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEMS_NO) >= $search_poem_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEMS_NO,'-','')) >= $search_poem_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEMS_NO >= $search_poem_no_min)";
	} // end if
  	if(trim($search_poem_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEMS_NO) <= $search_poem_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEMS_NO,'-','')) <= $search_poem_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEMS_NO <= $search_poem_no_max)";
	} // end if
	if(trim($search_poem_no_min) && trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : $search_poem_no_min ถึง $search_poem_no_max";
	}elseif(trim($search_poem_no_min)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : มากกว่า $search_poem_no_min";
	}elseif(trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : น้อยกว่า $search_poem_no_max";
	} // end if
	if(trim($search_level_no)){ 
		$arr_search_condition[] = "(trim(LEVEL_NO) = '". trim($search_level_no) ."')";
		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='".trim($search_level_no)."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_level_name = $data[LEVEL_NAME];
		$print_search_condition[] = "กลุ่มงาน : $search_level_name";
	} // end if
	if(trim($search_ep_code)){ 
		$arr_search_condition[] = "(trim(a.EP_CODE) = '". trim($search_ep_code) ."')";
		$print_search_condition[] = "ชื่อตำแหน่ง : $search_ep_name";
	} // end if
  	if(trim($search_poem_salary_min)) $arr_search_condition[] = "(POEM_MIN_SALARY >= $search_poem_salary_min)";
  	if(trim($search_poem_salary_max)) $arr_search_condition[] = "(POEM_MAX_SALARY <= $search_poem_salary_max)";
	if(trim($search_poem_salary_min) && trim($search_poem_salary_max)){
		$print_search_condition[] = "อัตราค่าจ้าง : $search_poem_salary_min ถึง $search_poem_salary_max";
	}elseif(trim($search_poem_salary_min)){
		$print_search_condition[] = "อัตราค่าจ้าง : มากกว่า $search_poem_salary_min";
	}elseif(trim($search_poem_salary_max)){
		$print_search_condition[] = "อัตราค่าจ้าง : น้อยกว่า $search_poem_salary_max";
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
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "$MINISTRY_TITLE : $search_ministry_name";
	}elseif($PV_CODE){
		$cmd = " select 	a.ORG_ID as MINISTRY_ID, b.ORG_ID as DEPARTMENT_ID, c.ORG_ID
						 from   	PER_ORG a, PER_ORG b, PER_ORG c
						 where  	a.OL_CODE='01' and b.OL_CODE='02' and c.OL_CODE='03' and c.PV_CODE='$PV_CODE' 
						 				and a.ORG_ID=b.ORG_ID_REF and b.ORG_ID=c.ORG_ID_REF
						 order by a.ORG_ID, b.ORG_ID, c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		unset($arr_org);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(ORG_ID in (". implode(",", $arr_org) ."))";
		$print_search_condition[] = "จังหวัด : $PV_NAME";
	}

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

	if(trim($search_pos_status) == 1) $arr_search_condition[] = "(a.POEM_STATUS = 1)";
	if(trim($search_pos_status) == 2) $arr_search_condition[] = "(a.POEM_STATUS = 2)";
	$search_pps = implode(",", $search_pps_code);
	if($search_pps){$arr_search_condition[] = "(a.PPS_CODE in($search_pps))";}else{$arr_search_condition[] = "(a.PPS_CODE in(1,2,3,4))";}
	if($search_pos_status == 0){
		$print_search_condition[] = "สถานะ : ทั้งหมด";
	}elseif($search_pos_status == 1){
		$print_search_condition[] = "สถานะ : ใช้งาน";
	}elseif($search_pos_status == 2){
		$print_search_condition[] = "สถานะ : ยกเลิก";
	} // end if
        
        /*ปรังปรุงตามเคส http://dpis.ocsc.go.th/Service/node/1120*/
        /*S0203 ตำแหน่งพนักงานราชการส่งออกไฟล์ Excel แล้วไม่พบโครงสร้างตามมอบหมายงาน และระบบไม่กรองเงื่อนไขให้แสดงเฉพาะตำแหน่งว่าง*/
        if(trim($search_pos_situation) == 1) $arr_search_condition[] = "(c.PER_STATUS IS NULL)";
	if(trim($search_pos_situation) == 2) $arr_search_condition[] = "(c.PER_STATUS=1)";
        /**/
        
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		POEMS_ID, POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS , PPT_CODE, PEF_CODE, PPS_CODE, POEMS_NO_NAME, POEMS_REMARK, POEMS_SKILL
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where		a.EP_CODE=b.EP_CODE
											$search_condition
							order by IIf(IsNull(POEMS_NO), 0, CLng(POEMS_NO)) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		a.POEMS_ID, a.POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS , PPT_CODE, PEF_CODE, PPS_CODE, POEMS_NO_NAME, POEMS_REMARK, POEMS_SKILL,
                                                                                        ORG_ID_3,ORG_ID_4,ORG_ID_5 
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b,
                                                        (select POEMS_ID, PER_ID, PER_NAME, PER_STATUS from PER_PERSONAL where PER_TYPE=3 and PER_STATUS=1) c
							where		a.EP_CODE=b.EP_CODE and a.POEMS_ID=c.POEMS_ID(+)
											$search_condition
							order by to_number(replace(POEMS_NO,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		POEMS_ID, POEMS_NO, a.EP_CODE, b.EP_NAME, b.LEVEL_NO, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS , PPT_CODE, PEF_CODE, PPS_CODE, POEMS_NO_NAME, POEMS_REMARK, POEMS_SKILL
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							where		a.EP_CODE=b.EP_CODE
											$search_condition
							order by POEMS_NO ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
	
//	$db_dpis->show_error();
//die($cmd);

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
                
		$worksheet->write($xlsRow, $plus+10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, $plus+11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, $plus+12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, $plus+13, "", set_format("xlsFmtTitle", "B", "C", "", 1));

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow+1;//$data_count = $xlsRow;
		$PER_SALARY="";
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POEMS_ID = trim($data[POEMS_ID]);
			//$POEMS_NO = trim($data[POEMS_NO]);
			$POEMS_NO = trim($data[POEMS_NO_NAME]).trim($data[POEMS_NO]);
			$EP_CODE = trim($data[EP_CODE]);
			$EP_NAME = trim($data[EP_NAME]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$PPT_CODE = trim($data[PPT_CODE]);
			$PEF_CODE = trim($data[PEF_CODE]);
			$PPS_CODE = trim($data[PPS_CODE]);
			$POEM_MIN_SALARY = trim($data[POEM_MIN_SALARY]);
			$POEM_MAX_SALARY = trim($data[POEM_MAX_SALARY]);
			$POEM_SALARY = number_format($POEM_MIN_SALARY) . (trim($POEM_MAX_SALARY)?(" - ".number_format($POEM_MAX_SALARY)):"");
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			$ORG_ID_2 = trim($data[ORG_ID_2]);/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                        $ORG_ID_3 = trim($data[ORG_ID_3]);/*ต่ำกว่าสำนัก/กอง 3 ระดับ */
                        $ORG_ID_4 = trim($data[ORG_ID_4]);/*ต่ำกว่าสำนัก/กอง 4 ระดับ */
                        $ORG_ID_5 = trim($data[ORG_ID_5]);/*ต่ำกว่าสำนัก/กอง 5 ระดับ */
			$POEMS_REMARK = trim($data[POEMS_REMARK]);
			$POEMS_SKILL = trim($data[POEMS_SKILL]);
            $length_POEMS_SKILL = strlen($POEMS_SKILL);    
            if($length_POEMS_SKILL>255){
               $POEMS_SKILL = substr($POEMS_SKILL,0,254);
            }
           // die($POEMS_SKILL);
			$POEM_STATUS = (trim($data[POEM_STATUS])==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";;

			$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='".$LEVEL_NO."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
	
			$cmd = " select PPS_NAME from PER_POS_STATUS where trim(PPS_CODE)='".$PPS_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PPS_NAME = $data_dpis2[PPS_NAME];
			
			$cmd = " select PPT_NAME from PER_PRACTICE where trim(PPT_CODE)='".$PPT_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PPT_NAME = $data_dpis2[PPT_NAME];
			
			$cmd = " select PEF_NAME from PER_POS_EMPSER_FRAME where trim(PEF_CODE)='".$PEF_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PEF_NAME = $data_dpis2[PEF_NAME];
			$ORG_NAME = "";
			if($ORG_ID){ //เดิมเป็น $$ORG_ID
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_1 = "";/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			if($ORG_ID_1){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_1 = $data_dpis2[ORG_NAME];
			}
			$ORG_NAME_2 = "";/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
			if($ORG_ID_2){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_2 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_3 = "";/*ต่ำกว่าสำนัก/กอง 3 ระดับ */
                        if($ORG_ID_3){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='06' and ORG_ID=$ORG_ID_3 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_3 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_4 = "";/*ต่ำกว่าสำนัก/กอง 4 ระดับ */
                        if($ORG_ID_4){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='07' and ORG_ID=$ORG_ID_4 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_4 = $data_dpis2[ORG_NAME];
			}
                        $ORG_NAME_5 = "";/*ต่ำกว่าสำนัก/กอง 5 ระดับ */
                        if($ORG_ID_5){
				$cmd = " select ORG_NAME from PER_ORG where OL_CODE='08' and ORG_ID=$ORG_ID_5 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$ORG_NAME_5 = $data_dpis2[ORG_NAME];
			}
			if($DPISDB=="odbc"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS , a.PER_SALARY 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS, a.PER_SALARY  
                                                ,DEPARTMENT_ID_ASS,ORG_ID,  ORG_ID_1, ORG_ID_2,ORG_ID_3,ORG_ID_4,ORG_ID_5
								 from 		PER_PERSONAL a, PER_PRENAME b
								 where	a.PN_CODE=b.PN_CODE(+) and a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS , a.PER_SALARY 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEMS_ID=$POEMS_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=3 ";
			}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
	//		$db_dpis2->show_error();
	//echo "<pre>".$cmd;

			$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
			if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
                        
                        $DEPARTMENT_ID_ASS = trim($data_dpis2[DEPARTMENT_ID_ASS]);
                        $ORG_ID_ASS = trim($data_dpis2[ORG_ID]);
                        $ORG_ID_1_ASS = trim($data_dpis2[ORG_ID_1]);
                        $ORG_ID_2_ASS = trim($data_dpis2[ORG_ID_2]);
                        $ORG_ID_3_ASS = trim($data_dpis2[ORG_ID_3]);
                        $ORG_ID_4_ASS = trim($data_dpis2[ORG_ID_4]);
                        $ORG_ID_5_ASS = trim($data_dpis2[ORG_ID_5]);
                        $PER_SALARY   = trim($data_dpis2[PER_SALARY]);
						
						if($PER_SALARY > $POEM_MAX_SALARY && ($POEM_MAX_SALARY>0 || $POEM_MAX_SALARY!=null )){
							$PER_SALARY = number_format($PER_SALARY)."*";
						}else{
							$PER_SALARY   = (trim($data_dpis2[PER_SALARY]))?$PER_SALARY = number_format(trim($data_dpis2[PER_SALARY])):"";
						}
						
                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$DEPARTMENT_ID_ASS'";/*Release 5.1.0.4*/
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $DEPARTMENT_NAME_ASS = $data_dpis2[ORG_NAME];

                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_ASS'";/*Release 5.1.0.4*/
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $ORG_NAME_ASS = $data_dpis2[ORG_NAME];

                        $cmd = "select ORG_NAME from PER_ORG_ASS where ORG_ID='$ORG_ID_1_ASS'";/*Release 5.1.0.4*/
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $ORG_NAME_1_ASS = $data_dpis2[ORG_NAME];

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
                        
                        
                        

			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$POEMS_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$EP_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "$PPT_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 4, "$PEF_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$PPS_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 6, "$POEM_SALARY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 7, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 8, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			$worksheet->write($xlsRow, 9, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                        $plus=0;
                        if($ORG_SETLEVEL>2){
                            $worksheet->write($xlsRow, 10, "$ORG_NAME_3", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                            $plus=1;
                            if($ORG_SETLEVEL>3){
                                $worksheet->write($xlsRow, 11, "$ORG_NAME_4", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                $plus=2;
                                if($ORG_SETLEVEL>4){
                                    $worksheet->write($xlsRow, 12, "$ORG_NAME_5", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                    $plus=3;
                                }
                            }
                        }
                        
                        $worksheet->write($xlsRow, 10+$plus, "$ORG_NAME_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 11+$plus, "$ORG_NAME_1_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			$worksheet->write($xlsRow, 12+$plus, "$ORG_NAME_2_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                        $plus_ass=0;
                        if($ORG_SETLEVEL>2){
                            $worksheet->write($xlsRow, 13+$plus, "$ORG_NAME_3_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                            $plus_ass=$plus+1;
                            if($ORG_SETLEVEL>3){
                                $worksheet->write($xlsRow, 14+$plus, "$ORG_NAME_4_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                                $plus_ass=$plus+2;
                                if($ORG_SETLEVEL>4){
                                    $worksheet->write($xlsRow, 15+$plus, "$ORG_NAME_5_ASS", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                                    $plus_ass=$plus+3;
                                }
                            }
                        }
                        $plus=$plus_ass;
                        
			$worksheet->write($xlsRow, $plus+13, "$POS_PERSON", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $plus+14, "$PER_SALARY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, $plus+15, "$POEMS_REMARK", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $plus+16, "$POEMS_SKILL", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $plus+17, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, $plus+17, "$POEM_STATUS", 35, 4, 1, 0.8);
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