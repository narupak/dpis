<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
	
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

	function print_header($xlsRow){
		global $worksheet, $ORG_TITLE, $ORG_TITLE1, $ORG_TITLE2;
                global $ORG_TITLE3, $ORG_TITLE4, $ORG_TITLE5,$ORG_SETLEVEL;
                
		$worksheet->set_column(0, 0, 10.5);
		$worksheet->set_column(1, 1, 20);
		$worksheet->set_column(2, 2, 30);
                $worksheet->set_column(3, 3, 30);/*ระดับตำแหน่ง*/
		$worksheet->set_column(4, 4, 15);
		$worksheet->set_column(5, 5, 15);/*อัตราค่าจ้าง,อัตราค่าจ้างขั้นต่ำ*/
                $worksheet->set_column(6, 6, 15);/*อัตราค่าจ้างขั้นสูง*/
                $worksheet->set_column(7, 7, 15);/*อัตราค่าจ้างขั้นสูง*/
                $worksheet->set_column(8, 8, 25);/*อัตราค่าขั้นสูงกว่าขั้นสูง*/
		$worksheet->set_column(9, 9, 25);/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->set_column(10, 10, 25);/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->set_column(10, 10, 25);/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->set_column(11, 11, 25);/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->set_column(12, 12, 25);/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
		$worksheet->set_column($plus+10, $plus+10, 50);
		$worksheet->set_column($plus+11, $plus+11, 10);
		
                
		$worksheet->write($xlsRow, 0, "เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 1, "หมวด", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 2, "ชื่อตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                $worksheet->write($xlsRow, 3, "ระดับตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ระดับตำแหน่ง*/
		$worksheet->write($xlsRow, 4, "กลุ่มบัญชีค่าจ้าง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 5, "อัตราค่าจ้างขั้นต่ำ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*อัตราค่าจ้าง,อัตราค่าจ้างขั้นต่ำ*/
                $worksheet->write($xlsRow, 6, "อัตราค่าจ้างขั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*อัตราค่าจ้างขั้นสูง*/
                $worksheet->write($xlsRow, 7, "อัตราค่าจ้างขั้นสูงกว่าขั้นสูง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*อัตราค่าจ้างขั้นสูงกว่าขั้นสูง*/
		$worksheet->write($xlsRow, 8, "$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, 9, "$ORG_TITLE1", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->write($xlsRow, 10, "$ORG_TITLE2", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                $plus=0;
                if($ORG_SETLEVEL>2){
                    $worksheet->write($xlsRow, 10, "$ORG_TITLE3", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 3 ระดับ*/
                    $plus=1;
                    if($ORG_SETLEVEL>3){
                        $worksheet->write($xlsRow, 11, "$ORG_TITLE4", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 4 ระดับ*/
                        $plus=2;
                        if($ORG_SETLEVEL>4){
                            $worksheet->write($xlsRow, 12, "$ORG_TITLE5", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));/*ต่ำกว่าสำนัก/กอง 5 ระดับ*/
                            $plus=3;
                        }
                    }
                }
		$worksheet->write($xlsRow, $plus+10, "ผู้ครองตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow, $plus+11, "ใช้งาน/ยกเลิก", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // end if
	
  	if(trim($search_poem_no_min)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEM_NO) >= $search_poem_no_min)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEM_NO,'-','')) >= $search_poem_no_min)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEM_NO >= $search_poem_no_min)";
	} // end if
  	if(trim($search_poem_no_max)){ 
		if($DPISDB=="odbc") $arr_search_condition[] = "(CLng(POEM_NO) <= $search_poem_no_max)";
		elseif($DPISDB=="oci8") $arr_search_condition[] = "(to_number(replace(POEM_NO,'-','')) <= $search_poem_no_max)";
		elseif($DPISDB=="mysql") $arr_search_condition[] = "(POEM_NO <= $search_poem_no_max)";
	} // end if
	if(trim($search_poem_no_min) && trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : $search_poem_no_min ถึง $search_poem_no_max";
	}elseif(trim($search_poem_no_min)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : มากกว่า $search_poem_no_min";
	}elseif(trim($search_poem_no_max)){
		$print_search_condition[] = "เลขที่ตำแหน่ง : น้อยกว่า $search_poem_no_max";
	} // end if
	if(trim($search_pg_code)){ 
		$arr_search_condition[] = "(trim(PG_CODE) = '". trim($search_pg_code) ."')";
		$print_search_condition[] = "หมวด : $search_pg_name";
	} // end if
	if(trim($search_pn_code)){ 
		$arr_search_condition[] = "(trim(a.PN_CODE) = '". trim($search_pn_code) ."')";
		$print_search_condition[] = "ชื่อตำแหน่ง : $search_pn_name";
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
	if($search_pos_status == 0){
		$print_search_condition[] = "สถานะ : ทั้งหมด";
	}elseif($search_pos_status == 1){
		$print_search_condition[] = "สถานะ : ใช้งาน";
	}elseif($search_pos_status == 2){
		$print_search_condition[] = "สถานะ : ยกเลิก";
	} // end if
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	if($DPISDB=="odbc"){
		$cmd = "	select		POEM_ID, POEM_NO, a.PN_CODE, b.PN_NAME, b.PG_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEM_REMARK , PG_CODE_SALARY, POEM_NO_NAME
							from		PER_POS_EMP a, PER_POS_NAME b
							where		a.PN_CODE=b.PN_CODE
											$search_condition
							order by POEM_NO_NAME, IIf(IsNull(POEM_NO), 0, CLng(POEM_NO)) ";
	}elseif($DPISDB=="oci8"){
		$cmd = "	select		POEM_ID, POEM_NO, a.PN_CODE, b.PN_NAME, b.PG_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEM_REMARK , PG_CODE_SALARY, POEM_NO_NAME,
                                                                                        ORG_ID_3,ORG_ID_4,ORG_ID_5
							from		PER_POS_EMP a, PER_POS_NAME b
							where		a.PN_CODE=b.PN_CODE
											$search_condition
							order by POEM_NO_NAME, to_number(replace(POEM_NO,'-','')) ";
	}elseif($DPISDB=="mysql"){
		$cmd = "	select		POEM_ID, POEM_NO, a.PN_CODE, b.PN_NAME, b.PG_CODE, POEM_MIN_SALARY, POEM_MAX_SALARY, 
											ORG_ID, ORG_ID_1, ORG_ID_2, POEM_STATUS, POEM_REMARK , PG_CODE_SALARY, POEM_NO_NAME
							from		PER_POS_EMP a, PER_POS_NAME b
							where		a.PN_CODE=b.PN_CODE
											$search_condition
							order by POEM_NO_NAME, POEM_NO ";
	} // end if

	$count_data = $db_dpis->send_cmd($cmd);
       // die('<pre>'.$cmd);
//	$db_dpis->show_error();

	if($count_data){
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "$report_title", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ระดับตำแหน่ง*/
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*อัตราค่าจ้าง,อัตราค่าจ้างขั้นต่ำ*/
                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*อัตราค่าจ้างขั้นสูง*/
                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*อัตราค่าจ้างขั้นสูงกว่าขั้นสูง*/
		$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
		$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
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

		foreach($print_search_condition as $show_condition){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "$show_condition", set_format("xlsFmtTitle", "B", "L", "", 0));
		} // end foreach

		$xlsRow++;
		print_header($xlsRow);
		$data_count = $xlsRow;
		
		while($data = $db_dpis->get_array()){
			$data_count++;

			$POEM_ID = trim($data[POEM_ID]);
			$POEM_NO = trim($data[POEM_NO_NAME]).trim($data[POEM_NO]);
			$PN_CODE = trim($data[PN_CODE]);
			$PN_NAME = trim($data[PN_NAME]);
			$PG_CODE = trim($data[PG_CODE]);
			$PG_CODE_SALARY = trim($data[PG_CODE_SALARY]);
                        
                        
                        $cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS, a.LEVEL_NO 
							 from 		PER_PERSONAL a, PER_PRENAME b
							 where	a.PN_CODE=b.PN_CODE(+) and a.POEM_ID=$POEM_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=2 ";
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $LEVEL_NO = $data_dpis2[LEVEL_NO];
			
                        $cmd = " select MIN_SALARY, MAX_SALARY, UP_SALARY, GROUP_SALARY from PER_POS_LEVEL_SALARY where PN_CODE='$PN_CODE' and LEVEL_NO='$LEVEL_NO' ";
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $MIN_SALARY = $data_dpis2[MIN_SALARY];
                        $MAX_SALARY = $data_dpis2[MAX_SALARY];
                        $UP_SALARY = $data_dpis2[UP_SALARY];
                        //$POEM_SALARY = number_format($MIN_SALARY) . (trim($MAX_SALARY)?(" - ".number_format($MAX_SALARY)):"");
                        $GROUP_SALARY = $data_dpis2[GROUP_SALARY];
                        $grp = explode(",", $GROUP_SALARY);
                           $grp_end = end($grp);
                           $num_grp = strlen($GROUP_SALARY);
                           if($num_grp == 5){
                                $grp_r = $grp[0]."-".$grp_end;
                           }else{
                                $grp_r = str_replace(",", "-", $GROUP_SALARY);
                           }
                           if(!$grp_r){
                                $grp_r = $PG_NAME_SALARY;
                           }else{
                                $grp_r = "กลุ่มที่ ".$grp_r; 
                           }
                
                        $POEM_MIN_SALARY = number_format($MIN_SALARY);
			$POEM_MAX_SALARY = number_format($MAX_SALARY);
                        $UP_SALARY_C    = number_format($UP_SALARY);
                        
                        //$POEM_MIN_SALARY = number_format(trim($data[POEM_MIN_SALARY]));
			//$POEM_MAX_SALARY = number_format(trim($data[POEM_MAX_SALARY]));
                        //$POEM_SALARY = number_format($POEM_MIN_SALARY) . (trim($POEM_MAX_SALARY)?(" - ".number_format($POEM_MAX_SALARY)):"");
			$ORG_ID = trim($data[ORG_ID]);
			$ORG_ID_1 = trim($data[ORG_ID_1]);/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			$ORG_ID_2 = trim($data[ORG_ID_2]);/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
                        $ORG_ID_3 = trim($data[ORG_ID_3]);/*ต่ำกว่าสำนัก/กอง 3 ระดับ */
                        $ORG_ID_4 = trim($data[ORG_ID_4]);/*ต่ำกว่าสำนัก/กอง 4 ระดับ */
                        $ORG_ID_5 = trim($data[ORG_ID_5]);/*ต่ำกว่าสำนัก/กอง 5 ระดับ */
                        
			$POEM_REMARK = trim($data[POEM_REMARK]);
			$POEM_STATUS = (trim($data[POEM_STATUS])==1)?"../images/checkbox_check.bmp":"../images/checkbox_blank.bmp";;

			$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='".$PG_CODE."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PG_NAME = $data_dpis2[PG_NAME];
			
			$cmd = " select PG_NAME_SALARY from PER_POS_GROUP where trim(PG_CODE)='".$PG_CODE_SALARY."' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PG_NAME_SALARY = $data_dpis2[PG_NAME_SALARY];
			$ORG_NAME = "";
			if($ORG_ID){
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
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEM_ID=$POEM_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=2 ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS ,a.LEVEL_NO 
								 from 		PER_PERSONAL a, PER_PRENAME b
								 where	a.PN_CODE=b.PN_CODE(+) and a.POEM_ID=$POEM_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=2 ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS 
								 from 		PER_PERSONAL a
								 				left join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
								 where	a.POEM_ID=$POEM_ID and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=2 ";
			}
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
	//		$db_dpis2->show_error();
			$POS_PERSON = "$data_dpis2[PN_NAME]$data_dpis2[PER_NAME] $data_dpis2[PER_SURNAME]";
			if($data_dpis2[PER_ID]) $POS_PERSON .= (($data_dpis2[PER_STATUS]==0)?" <span class=\"label_alert\">(รอบรรจุ)</span>":"");
                        /*เพิ่ม ระดับตำแหน่ง จ๊ะ*/
                        $LEVEL_NO = $data_dpis2[LEVEL_NO];
                        $cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $LEVEL_NAME = $data_dpis2[LEVEL_NAME];
                        /*เพิ่ม ระดับตำแหน่ง จ๊ะ*/    
			$xlsRow = $data_count;
			$worksheet->write($xlsRow, 0, "$POEM_NO", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));
			$worksheet->write($xlsRow, 1, "$PG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 2, "$PN_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                        $worksheet->write($xlsRow, 3, "$LEVEL_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ระดับตำแหน่ง*/
			$worksheet->write($xlsRow, 4, "$grp_r", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 5, "$POEM_MIN_SALARY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));/*อัตราค่าจ้าง,อัตราค่าจ้างขั้นต่ำ*/
                        $worksheet->write($xlsRow, 6, "$POEM_MAX_SALARY", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));/*อัตราค่าจ้างขั้นสูง*/
                        $worksheet->write($xlsRow, 7, "$UP_SALARY_C", set_format("xlsFmtTableDetail", "", "C", "TLRB", 0));/*อัตราค่าจ้างขั้นสูง*/
			$worksheet->write($xlsRow, 8, "$ORG_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 9, "$ORG_NAME_1", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 1 ระดับ */
			$worksheet->write($xlsRow, 10, "$ORG_NAME_2", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));/*ต่ำกว่าสำนัก/กอง 2 ระดับ */
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
			$worksheet->write($xlsRow, $plus+10, "$POS_PERSON", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, $plus+11, "", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->insert_bitmap($xlsRow, $plus+11, "$POEM_STATUS", 35, 4, 1, 0.8);
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