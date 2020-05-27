<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
//	require_once "../../Excel/Writer.php";

	ini_set("max_execution_time", $max_execution_time);

	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	$report_code = "R04064";

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

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.POSITION_TYPE, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, a.PER_RETIREDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, 
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PV_CODE,
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
		$cmd = "select			a.PER_ID, a.LEVEL_NO, f.POSITION_TYPE, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, a.PER_RETIREDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, 
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PV_CODE,
											g.POT_NO as EMPTEMP_POS_NO, g.TP_CODE as EMPTEMP_PL_CODE, 
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						order by		NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.POSITION_TYPE, f.POSITION_LEVEL, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, a.PER_RETIREDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, 
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, 
											a.PER_MOTHERSURNAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PV_CODE,
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
				$PL_NAME = trim($data2[PN_NAME]); //ตำแหน่งตามสายงาน
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2]; //ตำแหน่งตามสายงาน

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);  //ตำแหน่งตามสายงาน
			}elseif($PER_TYPE==4){
				$POS_ID = $data[POT_ID];
				$POS_NO = $data[EMPTEMP_POS_NO];
				$PL_CODE = trim($data[EMPTEMP_PL_CODE]);
				$ORG_ID = $data[EMPTEMP_ORG_ID];
				$ORG_ID_1 = $data[EMPTEMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMPTEMP_ORG_ID_2]; //ตำแหน่งตามสายงาน

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]); //ตำแหน่งตามสายงาน
			} 

			// สังกัด
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

			$PM_CODE = $data[PM_CODE];	
			$cmd = " select PM_NAME from PER_MGT where PM_CODE = $PM_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = $PV_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$TYPE_NAME =  $data[POSITION_TYPE];
			$LEVEL_NAME = $data[POSITION_LEVEL];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];	
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
//			$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
//			$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
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

			// =====  ข้อมูลคู่สมรส =====
			$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);

			// เครื่องราชฯ
			$cmd = " select			b.DC_NAME, a.DEH_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
								 order by	a.DEH_DATE desc ";	
			$count_decoratehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$DEH_SHOW = "";
			while($data2 = $db_dpis2->get_array()){
				$DC_NAME = trim($data2[DC_NAME]);
				$DEH_DATE = trim($data2[DEH_DATE]);
				$arr_temp = explode("-", $DEH_DATE);
				$DEH_YEAR = ($arr_temp[0] + 543);
				if ($DEH_SHOW) {
					$DEH_SHOW = "$DEH_SHOW\n$DC_NAME : ปี $DEH_YEAR";
				} else {
					$DEH_SHOW = "$DC_NAME : ปี $DEH_YEAR";
				}
			} // end while

			$xlsRow = 0;

/*			if($img_file){
				$image_x = 20;		$image_y = 10;		$image_w = 30;			$image_h = 35;
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 0));
				$worksheet->insertBitmap($xlsRow, 4, "$img_file", $image_x, $image_y, $image_w, $image_h);
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 0));
			} else {
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 0));
			} // end if
*/
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $FULLNAME, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, ($PM_NAME ? $PM_NAME : $PL_NAME), set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, ($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "วัน เดือน ปี เกิด:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $PER_BIRTHDATE, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "ภูมิลำเนา:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, $PV_NAME, set_format("xlsFmtTitle", "B", "L", "", 0));
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "บรรจุเข้ารับราชการ:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $PER_STARTDATE, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "เกษียณอายุราชการ:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, $PER_RETIREDATE, set_format("xlsFmtTitle", "B", "L", "", 0));

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "คู่สมรส:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, $SHOW_SPOUSE, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "ระดับ: ".$LEVEL_NAME, set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, " เงินเดือน: ".number_format($PER_SALARY)." บาท", set_format("xlsFmtTitle", "B", "L", "", 0));

			$arr_DEH = explode("\n", $DEH_SHOW);
			$label = "เครื่องราชอิสริยาภรณ์:";
			for($ii = 0; $ii < count($arr_DEH); $ii++) {
				$xlsRow++;
				$worksheet->write($xlsRow, 0, $label, set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 1, "- ".$arr_DEH[$ii], set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
				$label = "";
			}

			if($DPISDB=="odbc"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
							 	from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								 where		a.PER_ID=$PER_ID
								 order by		a.EDU_SEQ desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
								from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and 
												a.INS_CODE=d.INS_CODE(+) 
								order by		a.EDU_SEQ desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
							 	from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								 where		a.PER_ID=$PER_ID
								 order by		a.EDU_SEQ desc ";			
			} // end if
			$count_educatehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$arr_content = (array) null;
			if($count_educatehis){
				$row_count = 0;
				while($data2 = $db_dpis2->get_array()){
						$EDU_ENDYEAR =  "ปี ".trim($data2[EDU_ENDYEAR]);
						$EN_NAME = trim($data2[EN_NAME]);
						$EM_NAME = trim($data2[EM_NAME]);
						if($EM_NAME!="") $EM_NAME = "($EM_NAME)"; 
						$INS_NAME = trim($data2[INS_NAME]);
						if(!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
						$arr_content[$row_count][edu_period] = $EDU_ENDYEAR;
						$arr_content[$row_count][en_name] = $EN_NAME;
						$arr_content[$row_count][em_name] = $EM_NAME;
						$arr_content[$row_count][ins_name] = $INS_NAME;
						$row_count++;
					} // end while $data2 for EDUCATION
			} // end if($count_educatehis)
			if (count($arr_content) > 0) {
					$label = "วุฒิการศึกษา:";
					for($row_count=0; $row_count < count($arr_content); $row_count++) {
						$edutext = ($arr_content[$row_count][en_name]?$arr_content[$row_count][en_name]."  : ":"").($arr_content[$row_count][em_name]?$arr_content[$row_count][em_name]."  : ":"").($arr_content[$row_count][edu_period]?$arr_content[$row_count][edu_period]."  : ":"").($arr_content[$row_count][ins_name]?$arr_content[$row_count][ins_name]." ":"");
						$xlsRow++;
						$worksheet->write($xlsRow, 0, $label, set_format("xlsFmtTitle", "B", "L", "", 0));
						$worksheet->write($xlsRow, 1, "- ".$edutext, set_format("xlsFmtTitle", "B", "L", "", 0));
						$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
						$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
						$label = "";
					} // end for loop $row_count
			}else{
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, "********** ไม่มีข้อมูล **********", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			} // end if

			if($DPISDB=="odbc"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
								 order by		a.TRN_STARTDATE desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
								 order by		a.TRN_STARTDATE desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
								 order by		a.TRN_STARTDATE desc ";		
			} // end if
			$count_traininghis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$arr_content = (array) null;
			if($count_traininghis){
				$row_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$TRN_ENDDATE = trim($data2[TRN_ENDDATE]);
					$arr_temp = explode("-", $TRN_ENDDATE);
					$TRN_YEAR = "ปี ".($arr_temp[0] + 543);
					$TRN_ORG = trim($data2[TRN_ORG]);
					$TR_NAME = trim($data2[TR_NAME]);				
					if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
					//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
					$TRN_NO = trim($data2[TRN_NO]);
					if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
					$arr_content[$row_count][trn_year]=$TRN_YEAR;
					$arr_content[$row_count][tr_name] = $TR_NAME;
					$row_count++;
				} // end while data2 for TRAINING
			} // end if($count_traininghis)
			if (count($arr_content) > 0) {
				$label = "อบรมดูงาน:";
				for($row_count=0; $row_count < count($arr_content); $row_count++) {
					$border = "";
					$trntext = ($arr_content[$row_count][tr_name]?$arr_content[$row_count][tr_name]." ":"").
								    ($arr_content[$row_count][trn_year]?$arr_content[$row_count][trn_year]:"");
					$xlsRow++;
					$worksheet->write($xlsRow, 0, $label, set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, "- ".$trntext, set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$label = "";
				} // end for loop $row_count
			}else{
				$xlsRow++;
				$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 1, "********** ไม่มีข้อมูล **********", set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
				$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			} // end if

			$xlsRow++;
			$worksheet->write($xlsRow, 0, "ประวัติการดำรงตำแหน่ง:", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));

			$cmd = " select	POH_EFFECTIVEDATE, POH_PL_NAME, POH_ORG from PER_POSITIONHIS 
						  where PER_ID=$PER_ID and POH_ISREAL != 'N' order by POH_EFFECTIVEDATE, POH_SEQ_NO ";							   
			$count_positionhis = $db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			if($count_positionhis){
				while($data2 = $db_dpis2->get_array()){
					$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
					if($POH_EFFECTIVEDATE){
						$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
					} // end if
			
					$POH_PL_NAME = trim($data2[POH_PL_NAME]);
					$POH_ORG = trim($data2[POH_ORG]);
					$postext = "- ".$POH_EFFECTIVEDATE." ".$POH_PL_NAME." ".$POH_ORG;
									
					$xlsRow++;
					$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 1, $postext, set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
					$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
				} // end while
			} //end if 
		} // end while
	}else{
		$xlsRow++;
		$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "L", "", 0));
		$worksheet->write($xlsRow, 1, "********** ไม่มีข้อมูล **********", set_format("xlsFmtTitle", "B", "L", "", 0));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "L", "", 0));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "", 0));
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