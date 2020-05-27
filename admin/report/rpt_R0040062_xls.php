<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

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
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}
		
	if($list_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no' or trim(e.POEMS_NO)='$search_pos_no' or trim(g.POT_NO)='$search_pos_no')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	}else if($list_type == "ELECTRONICS"){	//ก.พ.7 อิเล็กทรอนิกส์ 
		if(trim($SELECTED_PER_ID)){	$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";	}
				
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
/*	$list_type_text = $ALL_REPORT_TITLE;
	$company_name = "รูปแบบการออกรายงาน : $list_type_text"; */
	$company_name = "";
	//___$report_title = $MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:"";
	//___$report_title .= "ก.พ.7 อิเล็กทรอนิกส์   $START_DATE  - $line_start - $page_no - $PRNTYPE";
	$report_title .= "ตำแหน่ง และอัตราเงินเดือน ";                            //ใบ กพ. 7 แผ่นที่ ..........";	
	//rpt_test_print_withlineno0.php?fall=a&line=5
	
	//_____include("rpt_test_print_withlineno0.php?fall=".$fall."&line=".$line);
	$report_code = "R04062";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$heading_width[0] = "20";
	$heading_width[1] = "60";      
	$heading_width[2] = "18";
	$heading_width[3] = "15";
	$heading_width[4] = "18";
	$heading_width[5] = "38";

	function print_header($HISTORY_NAME){
		global $worksheet, $xlsRow;
		global $heading_width;

		switch($HISTORY_NAME){
			case "POSITIONHIS" :
				$worksheet->set_column(0, 0, $heading_width[0]);
				$worksheet->set_column(1,1,$heading_width[1]);
				$worksheet->set_column(2,2,$heading_width[2]);
				$worksheet->set_column(3,3,$heading_width[3]);
				$worksheet->set_column(4,4,$heading_width[4]);
				$worksheet->set_column(5,5,$heading_width[5]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"เอกสาร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,2,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,4,"เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				break;	
		} // end switch case
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 			(	
								(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)	
					) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by		a.PER_NAME, a.PER_SURNAME
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO
											$search_condition
						order by		a.PER_NAME, a.PER_SURNAME
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 			(	
								(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
					) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME
				";
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "$cmd<br>";
//	$db_dpis->show_error();
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
		}

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			}else if($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[EMPTEMP_POS_NO];
				$PL_CODE = trim($data[EMPTEMP_PL_CODE]);
				$ORG_ID = $data[EMPTEMP_ORG_ID];
				$ORG_ID_1 = $data[EMPTEMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMPTEMP_ORG_ID_2];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 

			// ข้อมูลประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];			
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$PER_RETIREDATE = ($arr_temp[0] + 60 + ((substr($PER_BIRTHDATE, 5, 5) >= "10-01")?1:0)."-10-01";
				$PER_RETIREDATE = show_date_format($PER_RETIREDATE,$DATE_DISPLAY);
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);
		
			// =====  ข้อมูลบิดา และมารดา =====
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_F=trim($data_dpis2[PN_NAME]);

			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
			
			$PN_CODE_M = trim($data[PN_CODE_M]);	
			$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ORDER BY PN_CODE";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME_M=trim($data_dpis2[PN_NAME]);
						
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
					where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);
			
			$START_DATE = show_date_format($START_DATE,$DATE_DISPLAY);
			//------------------------------------------------------------------------------------------
			$worksheet->set_column(0, 0, 50);
			$worksheet->set_column(1,1, 50);
			$worksheet->set_column(2,2, 50);
			$worksheet->set_column(3,3, 50);
			$worksheet->set_column(4,4, 50);
			$worksheet->set_column(5,5, 75);
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));

			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1,"", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 2,"", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3,"", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 5, (card_no_format($PER_CARDNO,$CARD_NO_DISPLAY)?card_no_format($PER_CARDNO,$CARD_NO_DISPLAY):""), set_format("xlsFmtTableDetail", "", "L", "", 0));
										
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "ชื่อ ".$FULLNAME, set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 1,($LEVEL_NAME?$LEVEL_NAME:""), set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 2,($MINISTRY_NAME?$MINISTRY_NAME:""), set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write_string($xlsRow, 3,($DEPARTMENT_NAME?$DEPARTMENT_NAME:""), set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableDetail", "", "L", "", 0));
			$worksheet->write($xlsRow, 5, ("ณ วันที่ ".$START_DATE?$START_DATE:""), set_format("xlsFmtTableDetail", "", "L", "", 0));
				
//		$pdf->AutoPageBreak = false;

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

							//########################
							//ประวัติการดำรงตำแหน่งข้าราชการ
							//########################
							if($DPISDB=="odbc"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by	LEFT(a.POH_EFFECTIVEDATE,10),a.POH_SALARY, LEFT(a.POH_ENDDATE,10) ";							   
							}elseif($DPISDB=="oci8"){
							 	$cmd = "select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												from			PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
												where		a.PER_ID=$PER_ID and
																a.PL_CODE=d.PL_CODE(+) and
																a.PT_CODE=e.PT_CODE(+) and
																a.PM_CODE=f.PM_CODE(+) and 
																a.LEVEL_NO=g.LEVEL_NO(+)
												order by	SUBSTR(a.POH_EFFECTIVEDATE,1,10) ,a.POH_SALARY, SUBSTR(a.POH_ENDDATE,1,10) ";
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by	LEFT(a.POH_EFFECTIVEDATE,10),a.POH_SALARY, LEFT(a.POH_ENDDATE,10) ";
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
									$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".trim($data2[LEVEL_NO])." ".trim($data2[POH_POS_NO]))){
										$PL_NAME = trim($data2[PL_NAME]);
										$LEVEL_NO = trim($data2[LEVEL_NO]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										$TMP_PL_NAME = trim($PL_NAME);
										if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO = trim($data2[POH_POS_NO]);	$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									//$LEVEL_NO = 		trim($data2[LEVEL_NO]);			$LEVEL_NO = ($LEVEL_NO?$LEVEL_NO:"-");
									$POH_SALARY = number_format($data2[POH_SALARY], ",");		$POH_SALARY = ($POH_SALARY?$POH_SALARY:"-");
									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$MOV_CODE = trim($data2[MOV_CODE]);

									//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
									$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$MOV_NAME = $data3[MOV_NAME];

									//เก็บลง array ของ POSTION HIS
									/*$ARR_POSITIONHIS[$PER_ID][] = array(
																					'DATE'=>$POH_EFFECTIVEDATE,
																					'MOVE'=>$TMP_PL_NAME,
																					'POS_NO'=>$POH_POS_NO,
																					'LEVEL'=>$LEVEL_NO,
																					'SALARY'=>$POH_SALARY,
																					'DOC_NO'=>$POH_DOCNO
																				); */
									//เก็บลง array ของ POSTION HIS
									$ARR_POSITIONHIS[$PER_ID][] = array(
																					'DATE'=>$POH_EFFECTIVEDATE,
																					'MOVE'=>$MOV_NAME,
																					'POS_NAME'=>$TMP_PL_NAME,
																					'POS_NO'=>$POH_POS_NO,
																					'LEVEL'=>$LEVEL_NO,
																					'SALARY'=>$POH_SALARY,
																					'DOC_NO'=>$POH_DOCNO
																				);
								} // end while
							} //end if 
	
							//########################
							//ประวัติการเลื่อนขั้นเงินเดือน
							//########################
							if($DPISDB=="odbc"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		 SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO
															 from			PER_SALARYHIS b, PER_MOVMENT c
															 where		b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE and b.MOV_CODE!='1901' 
															 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID and b.MOV_CODE!='1901'
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";
							} // end if
							$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							//echo "<br>$cmd<br>";
							if($count_salaryhis){
								$salaryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$salaryhis_count++;
									$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
									$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
									$MOV_NAME = trim($data2[MOV_NAME]);		$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"-");
									$SAH_SALARY = number_format($data2[SAH_SALARY], ",");		$SAH_SALARY = ($SAH_SALARY?$SAH_SALARY:"-");
									$SAH_DOCNO = (trim($data2[SAH_DOCNO]))? $data2[SAH_DOCNO] : "-" ;

									//หาเลขที่ตำแหน่ง และระดับล่าสุดก่อนหน้าจะออกคำสั่งเลื่อนขั้นเงินเดือน
									if($DPISDB=="odbc"){
											/*$cmd = " select		a.POH_POS_NO, a.LEVEL_NO
															from			PER_POSITIONHIS a
															where 	(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
															order by LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc
															";*/
										$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where	(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
												  order by LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc ";
									}elseif($DPISDB=="oci8"){
											/****$cmd = " select		a.POH_POS_NO, a.LEVEL_NO
															from			PER_POSITIONHIS a
															where 	(a.PER_ID=$PER_ID) AND (SUBSTR(a.POH_EFFECTIVEDATE,1,10) <= '$SAH_EFFECTIVEDATE')
															order by SUBSTR(a.POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(a.POH_ENDDATE,1,10) desc
															";****/
										$cmd = "select			a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																			a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
															from			PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
															where		(a.PER_ID=$PER_ID) and
																			a.PL_CODE=d.PL_CODE(+) and
																			a.PT_CODE=e.PT_CODE(+) and
																			a.PM_CODE=f.PM_CODE(+) and 
																			a.LEVEL_NO=g.LEVEL_NO(+) and
																			 (SUBSTR(a.POH_EFFECTIVEDATE,1,10) <= '$SAH_EFFECTIVEDATE')
															order by SUBSTR(a.POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(a.POH_ENDDATE,1,10) desc ";
									}elseif($DPISDB=="mysql"){
											/*$cmd = " select		a.POH_POS_NO, a.LEVEL_NO
															from			PER_POSITIONHIS a
															where 	(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
															order by LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc
															";*/
										$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,a.MOV_CODE, d.PL_NAME, 
																		a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
												   order by	LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc ";			
									}
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POH_POS_NO = trim($data3[POH_POS_NO]);				$POH_POS_NO =($POH_POS_NO?$POH_POS_NO:"-");
									$LEVEL_NO = trim($data3[LEVEL_NO]);								//$LEVEL_NO = ($LEVEL_NO?$LEVEL_NO:"-");
									//หา POS_NAME
									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data3[PM_CODE])." ".trim($data3[PL_NAME])." ".trim($data3[LEVEL_NO])." ".trim($data3[POH_POS_NO]))){
										$PL_NAME = trim($data3[PL_NAME]);
										$LEVEL_NO = trim($data3[LEVEL_NO]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data3[LEVEL_NAME]));
										$PT_CODE = trim($data3[PT_CODE]);
										$PT_NAME = trim($data3[PT_NAME]);
										$PM_CODE = trim($data3[PM_CODE]);
										$PM_NAME = trim($data3[PM_NAME]);
										$TMP_PL_NAME = trim($PL_NAME);
										if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									
									//เก็บลง array ของ SALARYHIS
									/*$ARR_SALARYHIS[$PER_ID][] = array(
																					'DATE'=>$SAH_EFFECTIVEDATE,
																					'MOVE'=>$MOV_NAME,
																					'POS_NO'=>$POH_POS_NO,
																					'LEVEL'=>$LEVEL_NO,
																					'SALARY'=>$SAH_SALARY,
																					'DOC_NO'=>$SAH_DOCNO
																			); */

									$ARR_SALARYHIS[$PER_ID][] = array(
																					'DATE'=>$SAH_EFFECTIVEDATE,
																					'MOVE'=>$MOV_NAME,
																					'POS_NAME'=>$TMP_PL_NAME,
																					'POS_NO'=>$POH_POS_NO,
																					'LEVEL'=>$LEVEL_NO,
																					'SALARY'=>$SAH_SALARY,
																					'DOC_NO'=>$SAH_DOCNO
																			);																			
								} // end while
							}// end if

						//######################################
						//รวมประวัติการดำรงตำแหน่ง + การเลื่อนขั้นเงินเดือน
						//######################################
						//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
						$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
						unset($ARR_POSITIONHIS);
						unset($ARR_SALARYHIS);
											
						// เรียงข้อมูล ตามวันที่ / เงินเดือน น้อยไปมาก
						/*print("<pre>");
						print_r($ARRAY_POH_SAH);
						print("</pre>");*/
						/*foreach ($ARRAY_POH_SAH[$PER_ID] as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
							$DATE[$key]  = $value['DATE'];
							$MOVE[$key]  = $value['MOVE'];
							$POS_NO[$key]  = $value['POS_NO'];
							$LEVEL[$key]  = $value['LEVEL'];
							$SALARY[$key] = $value['SALARY'];
							$DOC_NO[$key]  = $value['DOC_NO'];
						} // end foreach
						array_multisort($DATE, SORT_ASC, $SALARY, SORT_ASC, $ARRAY_POH_SAH);*/
						for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]);$in++){
							//เก็บค่าวันที่
							$DATE_HIS[] = array('DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
														'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
														'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
														'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
														'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
														'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
														'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO']);
						} // end for
						unset($ARRAY_POH_SAH);
						sort($DATE_HIS);
						foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
							$DATE[$key]  = $value['DATE'];
							$MOVE[$key]  = $value['MOVE'];
							$POS_NAME[$key] = $value['POS_NAME'];
							$POS_NO[$key]  = $value['POS_NO'];
							$LEVEL[$key]  = $value['LEVEL'];
							$SALARY[$key] = $value['SALARY'];
							$DOC_NO[$key]  = $value['DOC_NO'];
						} // end foreach
						array_multisort($DATE, SORT_ASC, $SALARY, SORT_ASC, $DATE_HIS);
						$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
						unset($DATE_HIS);
						/***print("<pre>");
						print_r($POH_SAH_HIS);
						print("</pre>");***/
			###IN CASE POSITIONHIS #######
			/////////////////////////////////////////////////////////////////

			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
				$HISTORY_NAME = $arr_history_name[$history_index];
				//set header width
				$xlsRow++;
				for($hw=0;	 $hw <5; $hw++){
					$worksheet->set_column($hw, $hw, $heading_width[$hw]);
					//เพิ่มช่องว่างบรรทัดใหม่
					$worksheet->write($xlsRow,$hw, "", set_format("xlsFmtTitle", "B", "C", "", 0));  
				}
			switch($HISTORY_NAME){
				case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
							$xlsRow++;
							print_header($HISTORY_NAME);

	   					//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
						if(is_array($POH_SAH_HIS)  && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=0;
							 for($in=0; $in < count($POH_SAH_HIS[$PER_ID]);$in++){
							 		$positionhis_count++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
									}
									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";						
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME = trim($data2[LEVEL_NAME]);
									$POSITION_TYPE = trim($data2[POSITION_TYPE]);
									$arr_temp = explode(" ", $LEVEL_NAME);
									//หาชื่อระดับตำแหน่ง 
									$LEVEL_NAME ="";
									if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
										$LEVEL_NAME =  str_replace("ระดับ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME =  $arr_temp[1];
									}
									//--------------------------------------------------------------------
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,"(".$xlsRow.") ".($DATE_POH_SAH?$DATE_POH_SAH:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); //$POH_SAH_HIS[$PER_ID][$in]['DATE']
									$worksheet->write_string($xlsRow,1,($POH_SAH_HIS[$PER_ID][$in]['POS_NAME']?$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']:"")." ".($LEVEL_NAME?$LEVEL_NAME:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 	//ตำแหน่ง
									$worksheet->write_string($xlsRow,2,("ภ.".$POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));	//เลขที่ ตน.
									$worksheet->write($xlsRow,3,($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));	//ระดับ					
									$worksheet->write_string($xlsRow,4,($POH_SAH_HIS[$PER_ID][$in]['SALARY']?$POH_SAH_HIS[$PER_ID][$in]['SALARY']:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));	//เงินเดือน
									$worksheet->write_string($xlsRow,5,($POH_SAH_HIS[$PER_ID][$in]['DOC_NO']?$POH_SAH_HIS[$PER_ID][$in]['DOC_NO']:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));	//เอกสารอ้างอิง
								} //end for
							}
							if($positionhis_count<=0){	//}else{
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if	
						break;
				} // end switch
			} // end for
			//if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{ //end if($count_data)
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();


	ini_set("max_execution_time", 60);
	
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