<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../php_scripts/thaiWrap_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
//	require_once "../../Excel/Writer.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$arr_history_name = explode("|", $HISTORY_LIST);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";

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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "";
	$report_title = "";
	$report_code = "R04063";

	$fname= "../../Excel/tmp/dpis_$token.xls";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);	

	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$heading_width[EDUCATE][0] = "24";
	$heading_width[EDUCATE][1] = "24";
	$heading_width[EDUCATE][2] = "24";
	$heading_width[EDUCATE][3] = "24";
	$heading_width[EDUCATE][4] = "24";
	$heading_width[EDUCATE][5] = "24";
	
	$heading_width[POSITIONHIS][0] = "24";
	$heading_width[POSITIONHIS][1] = "24";      
	$heading_width[POSITIONHIS][2] = "24";
	$heading_width[POSITIONHIS][3] = "24";
	$heading_width[POSITIONHIS][4] = "24";
	$heading_width[POSITIONHIS][5] = "24";

	// sort หัวข้อที่เลือก เพื่อจัดลำดับการแสดงก่อนหลัง ของ $arr_history_name ที่เลือกเข้ามา
	$arr_history_sort = array("EDUCATE", "PUNISHMENT", "NOTPAID", "POSITIONHIS"); 
	// sort หัวข้อที่เลือก
	
	function print_header($HISTORY_NAME){
		global $worksheet, $xlsRow;
		global $heading_width;
		
		switch($HISTORY_NAME){
			case "EDUCATE" :
				$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
				$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
				$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);
				$worksheet->set_column(3, 3, $heading_width[$HISTORY_NAME][3]);
				$worksheet->set_column(4, 4, $heading_width[$HISTORY_NAME][4]);
				$worksheet->set_column(5, 5, $heading_width[$HISTORY_NAME][5]);

				$xlsRow++;
				$worksheet->write($xlsRow,0,"สถานศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"ตั้งแต่ - ถึง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"วุฒิที่ได้รับ/สาขา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"สถานที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"ตั้งแต่ - ถึง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"หลักสูตร/รุ่นที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				break;
			case "POSITIONHIS" :
				$worksheet->set_column(0, 0, $heading_width[POSITIONHIS][0]);
				$worksheet->set_column(1, 1, $heading_width[POSITIONHIS][1]);
				$worksheet->set_column(2, 2, $heading_width[POSITIONHIS][2]);
				$worksheet->set_column(3, 3, $heading_width[POSITIONHIS][3]);
				$worksheet->set_column(4, 4, $heading_width[POSITIONHIS][4]);
				$worksheet->set_column(5, 5, $heading_width[POSITIONHIS][5]);

				$xlsRow++;
				$worksheet->write($xlsRow,0,"วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"เอกสารอ้างอิง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				// end set head
				break;
		} // end switch
	} // function
	
	function print_footer() {
		global $worksheet, $xlsRow;
		global $FULLNAME, $LEVEL_NAME, $MINISTRY_NAME, $DEPARTMENT_NAME, $page_no;
		
		$prt_text_footer = "ชื่อ ".$FULLNAME."..ระดับ ".$LEVEL_NAME."..".$MINISTRY_NAME."..".$DEPARTMENT_NAME;
		$prt_page_no = "หน้า [$page_no]";
		$xlsRow++;
		$worksheet->write($xlsRow,0,"$prt_text_footer", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
//		$worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
		$worksheet->write($xlsRow,5,"$prt_page_no", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
	} // function footer

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
				 		from		PER_PRENAME b inner join 
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
				 		order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO
											$search_condition
						order by		NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											a.PER_OFFNO, a.PV_CODE,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
					 	from		PER_PERSONAL a inner join PER_PRENAME b on (trim(a.PN_CODE)=trim(b.PN_CODE))
																  left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
																  left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																  left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
																  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
																  left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
						$search_condition
				 		order by  a.PER_NAME, a.PER_SURNAME ";
	}
//	echo "$cmd<\br>";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			$PER_OFFNO = $data[PER_OFFNO];
			
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
			}elseif($PER_TYPE==4){
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

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);

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
			$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
			$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE = show_date_format(substr($PER_OCCUPYDATE, 0, 10),$DATE_DISPLAY);
			} // end if
		
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

		// เครื่องราชฯ
		if($DPISDB=="odbc"){
			$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
							 from			PER_DECORATEHIS a, PER_DECORATION b
							 where		a.PER_ID=$PER_ID and
												a.DC_CODE=b.DC_CODE
												 order by		a.DEH_DATE ";							   
		}elseif($DPISDB=="oci8"){
			$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
							 from			PER_DECORATEHIS a, PER_DECORATION b
							 where		a.PER_ID=$PER_ID and
												a.DC_CODE=b.DC_CODE
							 order by		a.DEH_DATE ";							   
		}elseif($DPISDB=="mysql"){
			$cmd = " select			b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
							 from			PER_DECORATEHIS a, PER_DECORATION b
							 where		a.PER_ID=$PER_ID and
												a.DC_CODE=b.DC_CODE
							 order by		a.DEH_DATE ";	
		} // end if
		$count_decoratehis = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		if($count_decoratehis){
			$decoratehis_count = 0;
			$DEH_SHOW = "";
			while($data2 = $db_dpis2->get_array()){
				$decoratehis_count++;
				$DC_NAME = trim($data2[DC_NAME]);
				$DEH_DATE = trim($data2[DEH_DATE]);
				if($DEH_DATE){
					$DEH_DATE = substr($DEH_DATE, 0, 10);
					$arr_temp = explode("-", $DEH_DATE);
					$DEH_YEAR1 = ($arr_temp[0] + 543);
					$DEH_DATE1 = show_date_format($DEH_DATE,$DATE_DISPLAY);
				} // end if	
				$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				if ($DEH_SHOW) {
					$DEH_SHOW = "$DEH_SHOW,  ปี $DEH_YEAR1 $DC_NAME";
				} else {
					$DEH_SHOW = "ปี $DEH_YEAR1 $DC_NAME";
				}
			} // end while
		} // end if ($count_decoratehis)
		//------------------------------------------------------------------------------------------

			$xlsRow = 0;

			$heading_width[$HISTORY_NAME][0] = "25";
			$heading_width[$HISTORY_NAME][1] = "25";
			$heading_width[$HISTORY_NAME][2] = "25";
			$heading_width[$HISTORY_NAME][3] = "25";
			$heading_width[$HISTORY_NAME][4] = "25";
			$heading_width[$HISTORY_NAME][5] = "25";

			$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
			$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
			$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);
			$worksheet->set_column(3, 3, $heading_width[$HISTORY_NAME][3]);
			$worksheet->set_column(4, 4, $heading_width[$HISTORY_NAME][4]);
			$worksheet->set_column(5, 5, $heading_width[$HISTORY_NAME][5]);
			
			$worksheet->write($xlsRow, 0, "11. การได้รับโทษทางวินัย", set_format("xlsFmtTitle", "B", "C", "TBL", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 1));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "พ.ศ.", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "C", "TBR", 0));
			$worksheet->write($xlsRow, 2, "รายการ", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "", "C", "TBR", 0));
			$worksheet->write($xlsRow, 4, "เอกสารอ้างอิง", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "", "C", "TBR", 0));

			$CR_NAME = "";
			$CRD_NAME = "";
			$PUN_STARTDATE = "";
			$PUN_ENDDATE = "";							
							
			if($DPISDB=="odbc"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE, PUN_NO, PUN_REF_NO
								 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
								 where		a.PER_ID=$PER_ID and
													a.CRD_CODE=b.CRD_CODE and
													b.CR_CODE=c.CR_CODE
								 order by		a.PUN_STARTDATE ";		
			} // end if
			$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if ($count_punishmenthis) {
				$punishmenthis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$punishmenthis_count++;
					$CR_NAME = trim($data2[CR_NAME]);
					$CRD_NAME = trim($data2[CRD_NAME]);
					$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],$DATE_DISPLAY);
					$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],$DATE_DISPLAY);
					$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
					if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";

					$PUN_NO = trim($data2[PUN_NO]);
					$PUN_REF_NO = trim($data2[PUN_REF_NO]);
					$PUN_REF = ($PUN_REF_NO ? $PUN_REF_NO : $PUN_NO);
	
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "$PUN_DURATION", set_format("xlsFmtTitle", "", "L", "TBL", 0));
					$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "TBR", 0));
					$worksheet->write($xlsRow, 2, "$CRD_NAME", set_format("xlsFmtTitle", "", "L", "TBL", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "L", "TBR", 0));
					$worksheet->write($xlsRow, 4, "$PUN_REF", set_format("xlsFmtTitle", "", "L", "TBL", 0));
					$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "", "L", "TBR", 0));
				} // end while
			} else { // ไม่มีข้อมูล
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "*******ไม่มีข้อมูล*******", set_format("xlsFmtTitle", "B", "L", "TBL", 1));
				$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "", "L", "TBR", 1));
			}

//			$heading_width[$HISTORY_NAME][0] = "80";
//			$heading_width[$HISTORY_NAME][1] = "150";
//			$heading_width[$HISTORY_NAME][2] = "80";

//			$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
//			$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
//			$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "", "L", "LB", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "B", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "", "L", "B", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "L", "B", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "", "L", "B", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "", "L", "RB", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "12. วันที่ไม่ได้รับเงินเดือน หรือวันที่ได้รับเงินเดือนไม่เต็ม หรือวันที่มิได้ประจำปฏิบัติหน้าที่อยู่ในเขตที่ได้มีประกาศใช้กฎอัยการศึก", set_format("xlsFmtTitle", "B", "C", "TBL", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "", "L", "TB", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "", "L", "TBR", 1));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ตั้งแต่-ถึง", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "รายการ", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "เอกสารอ้างอิง", set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "", "L", "TBR", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TBR", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TBL", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 0));

//			$heading_width[$HISTORY_NAME][0] = "65";
//			$heading_width[$HISTORY_NAME][1] = "115";
//			$heading_width[$HISTORY_NAME][2] = "20";

//			$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
//			$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
//			$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);

			$xlsRow++;
			$worksheet->write($xlsRow, 0, ($MINISTRY_NAME?$MINISTRY_NAME:""), set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTableHeader", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, ($DEPARTMENT_NAME?$DEPARTMENT_NAME:""), set_format("xlsFmtTableHeader", "B", "R", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTableHeader", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 5, "$KP7_TITLE", set_format("xlsFmtTableHeader", "B", "R", "TBR", 0));
			
//			$heading_width[$HISTORY_NAME][0] = "65";
//			$heading_width[$HISTORY_NAME][1] = "70";
//			$heading_width[$HISTORY_NAME][2] = "65";

//			$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
//			$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
//			$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);

			$xlsRow++;			
			$worksheet->write($xlsRow, 0, "1. ชื่อ  ".$FULLNAME, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "5. ชื่อบิดา  ".($FATHERNAME?$FATHERNAME:"-"), set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
//			if($img_file){
//				$image_x = ($pdf->x + 155);		$image_y = $pdf->y;		$image_w = 30;			$image_h = 35;
//				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TL", 0));
//				$worksheet->insertBitmap($xlsRow, 4, "$img_file", 20, 10, $image_w, $image_h);
//				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TL", 0));
//			} else {
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TL", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TR", 0));
//			} // end if

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "2. วัน เดือน ปี เกิด  ".$PER_BIRTHDATE, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "6. ชื่อมารดา  ".($MOTHERNAME?$MOTHERNAME:"-"), set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "L", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "R", 0));
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "3. วันครบเกษียณอายุ ".$PER_RETIREDATE, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "7. วันสั่งบรรจุ  ".$PER_STARTDATE, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "L", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "R", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "4. ชื่อคู่สมรส ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-"), set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "8. วันเริ่มปฏิบัติราชการ  ".$PER_STARTDATE, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "L", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "R", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "    ภูมิลำเนา ".($PV_NAME?$PV_NAME:"-"), set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 2, "9. ประเภทข้าราชการ ".$OT_NAME, set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "L", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "R", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "เครื่องราชอิสริยาภรณ์", set_format("xlsFmtTitle", "B", "L", "TL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "T", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "T", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TR", 0));
			$worksheet->write($xlsRow, 4, "เลขประจำตัวประชาชน :  ".card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "L", "BL", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "B", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "B", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "BR", 0));
			$worksheet->write($xlsRow, 4, "เลขประจำตัว ขรก.ปค. : $PER_OFFNO", set_format("xlsFmtTitle", "B", "L", "TBL", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));


			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";

//			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
			for($history_index=0; $history_index<count($arr_history_sort); $history_index++){
				if (in_array($arr_history_sort[$history_index],$arr_history_name)) {
					$HISTORY_NAME = $arr_history_sort[$history_index];
				} else {
					$HISTORY_NAME = "";
				}
				switch($HISTORY_NAME){
					case "EDUCATE" :
					// เฉพาะ EDUCATE และ TRAINING
//						$heading_width[$HISTORY_NAME][0] = "35";
//						$heading_width[$HISTORY_NAME][1] = "33";
//						$heading_width[$HISTORY_NAME][2] = "33";
//						$heading_width[$HISTORY_NAME][4] = "33";
//						$heading_width[$HISTORY_NAME][5] = "33";
//						$heading_width[$HISTORY_NAME][6] = "33";

//						$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
//						$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
//						$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);
//						$worksheet->set_column(3, 3, $heading_width[$HISTORY_NAME][0]);
//						$worksheet->set_column(4, 4, $heading_width[$HISTORY_NAME][1]);
//						$worksheet->set_column(5, 5, $heading_width[$HISTORY_NAME][2]);
						
						$xlsRow++;
						$worksheet->write($xlsRow, 0, "10. ประวัติการศึกษา ฝึกอบรมและดูงาน", set_format("xlsFmtTitle", "B", "C", "TBL", 1));
						$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
						$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
						$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 1));

						$xlsRow++;
						$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTableHeader", "B", "C", "TBL", 0));
						$worksheet->write($xlsRow, 1, "ประวัติการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTableHeader", "B", "C", "TBR", 0));
						$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTableHeader", "B", "C", "TBL", 0));
						$worksheet->write($xlsRow, 4, "ประวัติการฝึกอบรมและดูงาน", set_format("xlsFmtTableHeader", "B", "C", "TB", 0));
						$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTableHeader", "B", "C", "TBR", 0));

						print_header($HISTORY_NAME);
							
						$EDU_PERIOD="";
						$EN_NAME = "";
						$EM_NAME = "";
						$INS_NAME = "";
						$CT_NAME = "";

							if($DPISDB=="odbc"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
										 	from			((PER_EDUCATE a
														left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
														) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
														) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
											 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
										where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+)
										order by		a.EDU_SEQ ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
									 	from			((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ ";			
							} // end if
							$count_educatehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_content = (array) null;
								$row_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
									$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
									if($EDU_STARTYEAR && $EDU_STARTYEAR != "-" && $EDU_ENDYEAR){
										$EDU_PERIOD = "$EDU_STARTYEAR - $EDU_ENDYEAR";
									}else{
										$EDU_PERIOD = $EDU_ENDYEAR;
									}
									$arr_content[$row_count][edu_period] = $EDU_PERIOD;

									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
									$INS_NAME = trim($data2[INS_NAME]);
									if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
									$arr_content[$row_count][en_name] = $EN_NAME;
									$arr_content[$row_count][em_name] = $EM_NAME;
									$arr_content[$row_count][ins_name] = $INS_NAME;
									$row_count++;
								} // end while $data2 for EDUCATION
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE , a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.TRN_STARTDATE, a.TRN_ENDDATE, a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a. TRN_FUND, a.TRN_NO, a.TRN_COURSE_NAME
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by		a.TRN_STARTDATE ";		
							} // end if
							$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$row_count = 0;
							while($data2 = $db_dpis2->get_array()){
								$TRN_DURATION = "";
								$TRN_STARTDATE = "";
								$TRN_ENDDATE = "";
								$TR_NAME = "";
								$TRN_PLACE = "";
							
								$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
								$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],$DATE_DISPLAY);
								if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
									$TRN_DURATION = $TRN_STARTDATE." - "."\n".$TRN_ENDDATE;
									if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
								}
								$arr_content[$row_count][trn_duration]=$TRN_DURATION;
								$TRN_ORG = trim($data2[TRN_ORG]);
								$TR_NAME = trim($data2[TR_NAME]);				
								if(!$TR_NAME) $TR_NAME = trim($data2[TRN_COURSE_NAME]);
								//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
								$TRN_PLACE = trim($data2[TRN_PLACE]);
								$TRN_FUND = trim($data2[TRN_FUND]);
								$TRN_NO = trim($data2[TRN_NO]);
								if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
								$arr_content[$row_count][tr_name] = $TR_NAME;
								$arr_content[$row_count][trn_org] = $TRN_ORG;
								$arr_content[$row_count][trn_place] = $TRN_PLACE;
								$arr_content[$row_count][trn_fund] = $TRN_FUND;
								$row_count++;
							} // end while data2 for TRAINING

							for($row_count=0; $row_count < count($arr_content); $row_count++) {
								// พิมพ์ EDU
								$xlsRow++;
								$worksheet->write($xlsRow, 0, $arr_content[$row_count][ins_name], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
								$worksheet->write($xlsRow, 1, $arr_content[$row_count][edu_period], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
								$worksheet->write($xlsRow, 2, $arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
								// พิมพ์ TRN
								$worksheet->write($xlsRow, 3, $arr_content[$row_count][trn_place], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
								$worksheet->write($xlsRow, 4, $arr_content[$row_count][trn_duration], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
								$worksheet->write($xlsRow, 5, $arr_content[$row_count][tr_name], set_format("xlsFmtTitle", "", "L", "TBRL", 0));

								// พิมพ์ EDU
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][0], 7, $arr_content[$row_count][ins_name], $border, "L");
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][1], 7, $arr_content[$row_count][edu_period], $border, "L");
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][2], 7, $arr_content[$row_count][en_name]."  ".$arr_content[$row_count][em_name], $border, "L");
								// พิมพ์ TRN
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][3], 7, $arr_content[$row_count][trn_place], $border, "L");
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][4], 7, $arr_content[$row_count][trn_duration], $border, "L");
//									$pdf->MultiCell($heading_width[$HISTORY_NAME][5], 7, $arr_content[$row_count][tr_name], $border, "L");
							} // end for loop $row_count
							break;
					case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
//							$heading_width[$HISTORY_NAME][0] = "35";
//							$heading_width[$HISTORY_NAME][1] = "33";
//							$heading_width[$HISTORY_NAME][2] = "33";
//							$heading_width[$HISTORY_NAME][4] = "33";
//							$heading_width[$HISTORY_NAME][5] = "33";
//							$heading_width[$HISTORY_NAME][6] = "33";

//							$worksheet->set_column(0, 0, $heading_width[$HISTORY_NAME][0]);
//							$worksheet->set_column(1, 1, $heading_width[$HISTORY_NAME][1]);
//							$worksheet->set_column(2, 2, $heading_width[$HISTORY_NAME][2]);
//							$worksheet->set_column(3, 3, $heading_width[$HISTORY_NAME][0]);
//							$worksheet->set_column(4, 4, $heading_width[$HISTORY_NAME][1]);
//							$worksheet->set_column(5, 5, $heading_width[$HISTORY_NAME][2]);
						
							$xlsRow++;
							$worksheet->write($xlsRow, 0, "13. ตำแหน่งและอัตราเงินเดือน", set_format("xlsFmtTitle", "B", "C", "TBL", 1));
							$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
							$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
							$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
							$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TB", 1));
							$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 1));
							
							print_header($HISTORY_NAME);

							//########################
							//ประวัติการดำรงตำแหน่งข้าราชการ
							//########################
							if($DPISDB=="odbc"){
								$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, a.POH_DOCDATE, 
																a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												   from			(
																		(
																			(
																			PER_POSITIONHIS a
																			left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																		) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																	) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where		a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";							   
							}elseif($DPISDB=="oci8"){
							 	$cmd = "select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, a.POH_DOCDATE,
																a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												from		PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
												where	a.PER_ID=$PER_ID and a.PL_CODE=d.PL_CODE(+) and
																a.PT_CODE=e.PT_CODE(+) and a.PM_CODE=f.PM_CODE(+) and 
																a.LEVEL_NO=g.LEVEL_NO(+)
												order by	a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME, a.POH_DOCDATE,
																a.UPDATE_USER, a.POH_PL_NAME, a.POH_ORG, a.POH_SEQ_NO
												  from	(
																(
																	(
																		PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																	) 	left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
															) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   where	a.PER_ID=$PER_ID
												   order by		a.POH_EFFECTIVEDATE, a.POH_SALARY, a.POH_ENDDATE ";
							} // end if
							$count_positionhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_positionhis){
								$positionhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$positionhis_count++;
									$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
									$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
									$LEVEL_NO = trim($data2[LEVEL_NO]);
									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO]))){
										$PL_NAME = trim($data2[PL_NAME]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
										$PT_CODE = trim($data2[PT_CODE]);
										$PT_NAME = trim($data2[PT_NAME]);
										$PM_CODE = trim($data2[PM_CODE]);
										$PM_NAME = trim($data2[PM_NAME]);
										$POH_PL_NAME = trim($data2[POH_PL_NAME]);
										$POH_ORG = trim($data2[POH_ORG]);
										$TMP_PL_NAME = trim($PL_NAME);
										$TMP_PL_NAME = $POH_PL_NAME."\n".$POH_ORG;
										//if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
									$POH_POS_NO = trim($data2[POH_POS_NO]);	$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
									$POH_SALARY = number_format($data2[POH_SALARY], ",");		$POH_SALARY = ($POH_SALARY?$POH_SALARY:"-");
									$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
									$MOV_CODE = trim($data2[MOV_CODE]);
									
									if(trim($data2[POH_DOCNO])){
										if($data2[POH_DOCDATE]){
											$POH_DOCDATE = "ลว. ".show_date_format(substr($data2[POH_DOCDATE], 0, 10),$DATE_DISPLAY);
										}
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname]; 
										}
										$POH_DOCNO = "คส. ".$data2[POH_DOCNO]."\n".$POH_DOCDATE."\n".$USRNAME;
									}

									//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
									$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
									//$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$MOV_NAME = $data3[MOV_NAME];

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
																	,b.SAH_DOCDATE,b.UPDATE_USER
												 from			PER_SALARYHIS b
												 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
												 where		b.PER_ID=$PER_ID
												 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		 SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO
																 ,b.SAH_DOCDATE,b.UPDATE_USER
															 from			PER_SALARYHIS b, PER_MOVMENT c
															 where		b.PER_ID=$PER_ID and
																				b.MOV_CODE=c.MOV_CODE 
															 order by		b.SAH_SALARY, b.SAH_EFFECTIVEDATE ";		   					   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO
																	,b.SAH_DOCDATE,b.UPDATE_USER
												 from			PER_SALARYHIS b  inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE
												 where			b.PER_ID=$PER_ID
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
									$SAH_EFFECTIVEDATE_MAX = show_date_format($SAH_EFFECTIVEDATE,$DATE_DISPLAY). " 23:59:59";
									$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
									$MOV_NAME = trim($data2[MOV_NAME]);		$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"");
									$SAH_SALARY = number_format($data2[SAH_SALARY], ",");		$SAH_SALARY = ($SAH_SALARY?$SAH_SALARY:"-");
									if(trim($data2[SAH_DOCNO])){
										if($data2[SAH_DOCDATE]){
											$SAH_DOCDATE = "ลว. ".show_date_format(substr($data2[SAH_DOCDATE], 0, 10),$DATE_DISPLAY);
										}
										if($data2[UPDATE_USER]){
											//ดึงชื่อจาก ตาราง user_detail ของ mysql
											$cmd1 ="select fullname from user_detail where id=$data2[UPDATE_USER]";
											$db->send_cmd($cmd1);
											//	$db->show_error();
											$datausr = $db->get_array();
											$datausr = array_change_key_case($datausr, CASE_LOWER);
											$USRNAME = $datausr[fullname];
											
										}
										$SAH_DOCNO = "คส. ".$data2[SAH_DOCNO]."\n".$SAH_DOCDATE."\n".$USRNAME;
									}

									//หาเลขที่ตำแหน่ง และระดับล่าสุดก่อนหน้าจะออกคำสั่งเลื่อนขั้นเงินเดือน
									if($DPISDB=="odbc"){
										$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																	a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																	a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   		from		(
																		(
																			(
																				PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																			) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																		) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																	) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   		where	(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
												  		order by LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc ";
									}elseif($DPISDB=="oci8"){
										$cmd = " select  	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																	a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																	a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
														from		PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g
														where	(a.PER_ID=$PER_ID) and
																	 a.PL_CODE=d.PL_CODE(+) and
																	 a.PT_CODE=e.PT_CODE(+) and
																	 a.PM_CODE=f.PM_CODE(+) and 
																	 a.LEVEL_NO=g.LEVEL_NO(+) and
																	 (SUBSTR(a.POH_EFFECTIVEDATE,1,10) <= '$SAH_EFFECTIVEDATE')
														order by SUBSTR(a.POH_EFFECTIVEDATE,1,10)  desc, SUBSTR(a.POH_ENDDATE,1,10) desc ";
									}elseif($DPISDB=="mysql"){
										$cmd = " select	a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_EFFECTIVEDATE,
																	a.MOV_CODE, d.PL_NAME, a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO, a.POH_SALARY, 
																	a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME
												   		from		(
																		(
																			(
																				PER_POSITIONHIS a left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
																			) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
																		) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
																	) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
												   		where	(a.PER_ID=$PER_ID) AND (LEFT(a.POH_EFFECTIVEDATE,10) <= '$SAH_EFFECTIVEDATE')
												   		order by	LEFT(a.POH_EFFECTIVEDATE,10)  desc, LEFT(a.POH_ENDDATE,10) desc ";			
									}
									$db_dpis3->send_cmd($cmd);
									//echo "<br>$cmd<br>";
//									$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POH_POS_NO = trim($data3[POH_POS_NO]);				
									$POH_POS_NO =($POH_POS_NO?$POH_POS_NO:"-");
									$LEVEL_NO = trim($data3[LEVEL_NO]);	
									//หา POS_NAME
									if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data3[PM_CODE])." ".trim($data3[PL_NAME])." ".$LEVEL_NO." ".trim($data3[POH_POS_NO]))){
										$PL_NAME = trim($data3[PL_NAME]);
										$LEVEL_NAME = trim(str_replace("ระดับ","",$data3[LEVEL_NAME]));
										$PT_CODE = trim($data3[PT_CODE]);
										$PT_NAME = trim($data3[PT_NAME]);
										$PM_CODE = trim($data3[PM_CODE]);
										$PM_NAME = trim($data3[PM_NAME]);
										$TMP_PL_NAME = trim($PL_NAME);
										if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
									}
									$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"");
									
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
							for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]); $in++){
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
							$DATE = (array) null;
							$SEQ = (array) null;
							$LEVEL = (array) null;
							$FOOTERLEVEL = (array) null;
							$SALARY1 = (array) null;
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

							//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
							if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=$first_line-1;
							//ถ้าไม่ได้ใส่ line มาคือแสดงทั้งหมด 
							if(!isset($get_line) || $get_line==""){		$get_line=$count_positionhis;		}
							$linenum = ceil($pdf->y / 7);  // พิมพ์มาแล้วกี่บรรทัด
							$linecnt = 0;
							for($in=0; $in < $count_positionhis; $in++){
									$positionhis_count++;
									$linecnt++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
									}

									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
//									$db_dpis2->show_error();
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
									
									//กำหนดชื่อตำแหน่ง -----------------------
									if(trim($POH_SAH_HIS[$PER_ID][$in]['POS_NAME'])){		//สำหรับการเคลื่อนไหวของ ตน.
										$f_pos_color = "blue";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] = $POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME;	
									}else if(trim($POH_SAH_HIS[$PER_ID][$in]['MOVE'])){		//สำหรับการเลื่อนขั้นเงินเดือน
										$f_pos_color = "black";
										$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'] =  $POH_SAH_HIS[$PER_ID][$in]['MOVE'];
									}

									$xlsRow++;
									$worksheet->write($xlsRow, 0, ($DATE_POH_SAH?$DATE_POH_SAH:""), set_format("xlsFmtTitle", "", "L", "TBRL", 0));
//									if ($f_pos_color == "blue") {
//										$pdf->SetTextColor(hexdec("65"),hexdec("00"),hexdec("CA"));	//สีน้ำเงิน
//									} else {
//										$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00")); // สีดำ
//									}
									$worksheet->write($xlsRow, 1, $POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
									$worksheet->write($xlsRow, 2, ("ภ.".$POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""), set_format("xlsFmtTitle", "", "L", "TBRL", 0));
									$worksheet->write($xlsRow, 3, ($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""), set_format("xlsFmtTitle", "", "L", "TBRL", 0));
									$worksheet->write($xlsRow, 4, ($POH_SAH_HIS[$PER_ID][$in]['SALARY']?$POH_SAH_HIS[$PER_ID][$in]['SALARY']:""), set_format("xlsFmtTitle", "", "L", "TBRL", 0));
									$worksheet->write($xlsRow, 5, $POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], set_format("xlsFmtTitle", "", "L", "TBRL", 0));
//									$pdf->Cell($heading_width[POSITIONHIS][0] ,7,($DATE_POH_SAH?$DATE_POH_SAH:""),$border,0,"C");
//									$sub_doc = explode("\n", $POH_SAH_HIS[$PER_ID][$in]['DOC_NO']);
//									$pdf->Cell($heading_width[POSITIONHIS][2] ,7,("ภ.".$POH_SAH_HIS[$PER_ID][$in]['POS_NO']?$POH_SAH_HIS[$PER_ID][$in]['POS_NO']:""),$border,0,"C");
//									$pdf->Cell($heading_width[POSITIONHIS][3] ,7,($POH_SAH_HIS[$PER_ID][$in]['LEVEL']?$POH_SAH_HIS[$PER_ID][$in]['LEVEL']:""),$border,0,"C");
//									$pdf->Cell($heading_width[POSITIONHIS][4] ,7,($POH_SAH_HIS[$PER_ID][$in]['SALARY']?$POH_SAH_HIS[$PER_ID][$in]['SALARY']:""),$border,0,"C");
							} // end for $in
							if($in<=0){	//}else{
									$xlsRow++;
									$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "TBL", 0));
									$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
									$worksheet->write($xlsRow, 2, "********** ไม่มีข้อมูล **********", set_format("xlsFmtTitle", "B", "C", "TB", 0));
									$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
									$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
									$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 0));
							} else {
								$page_no++;
//								print_footer();
							} // end if	
						} // end if(is_array($POH_SAH_HIS) && !empty($POH_SAH_HIS))
						break;
				} // end switch
			} // end for
		} // end while
	}else{
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "TBL", 0));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 2, "********** ไม่มีข้อมูล **********", set_format("xlsFmtTitle", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TB", 0));
		$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TBR", 0));
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