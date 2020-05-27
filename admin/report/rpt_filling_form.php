<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_no_footer.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$search_condition = "";
//	if(trim($search_per_type)) $arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
	if(trim($search_org_id)){ 
	if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
	else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and PV_CODE='$PROVINCE_CODE' ";
		if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		
		if($SESS_ORG_STRUCTURE==0) $arr_search_condition[] = "(b.ORG_ID in (". implode(",", $arr_org) ."))";
		else if($SESS_ORG_STRUCTURE==1) $arr_search_condition[] = "(a.ORG_ID in (". implode(",", $arr_org) ."))";
	} // end if
	if($SESS_ORG_STRUCTURE==0) {
	if(trim($search_org_id_1)) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
	if(trim($search_org_id_2)) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
	} elseif($SESS_ORG_STRUCTURE==1) {
	if(trim($search_org_id_1)) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)";
	if(trim($search_org_id_2)) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
	}
	if($list_type == "SELECT"){
		if(trim($search_per_id)) $arr_search_condition[] = "(a.PER_ID = $search_per_id)";
	}elseif($list_type == "CONDITION"){
		if(trim($search_pos_no)) $arr_search_condition[] = "(b.POS_NO like '$search_pos_no%' or c.POEM_NO like '$search_pos_no%')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(a.LEVEL_NO <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(a.LEVEL_NO <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(a.LEVEL_NO <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
//		if(trim($search_per_status) != "") $arr_search_condition[] = "(a.PER_STATUS = $search_per_status)";
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
//	$report_title = "แบบกรอกข้อมูลในระบบจ่ายตรงค่ารักษาพยาบาล";
	$report_code = "";
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,10,5);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.RE_CODE, a.PER_CARDNO, a.PER_GENDER,
											a.PER_BIRTHDATE, a.MR_CODE, a.PER_MEMBER, a.POS_ID, a.PER_ADD2, a.PER_STARTDATE, a.PER_TYPE,
											a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, 
											a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2,
											c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, c.ORG_ID_2 as EMP_ORG_ID_2,
											d.POEMS_NO as EMPSER_POS_NO, d.ORG_ID as EMPSER_ORG_ID, d.ORG_ID_1 as EMPSER_ORG_ID_1, d.ORG_ID_2 as EMPSER_ORG_ID_2,
											e.POT_NO as POT_POS_NO, e.ORG_ID as POT_ORG_ID, e.ORG_ID_1 as POT_ORG_ID_1, e.ORG_ID_2 as POT_ORG_ID_2
						 from		( 
						 					(
												(
						 						PER_PERSONAL a 
												left join PER_POSITION b on (a.POS_ID = b.POS_ID)
											) left join PER_POS_EMP c on (a.POEM_ID = c.POEM_ID)
										)  left join PER_POS_EMPSER d on (a.POEMS_ID = d.POEMS_ID)
									)  left join PER_POS_TEMP e on (a.POT_ID = e.POT_ID)
											$search_condition
						 order by		b.ORG_ID, CLng(b.POS_NO), CLng(c.POEM_NO), CLng(d.POEMS_NO), CLng(e.POT_NO)
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.RE_CODE, a.PER_CARDNO, a.PER_GENDER,
											a.PER_BIRTHDATE, a.MR_CODE, a.PER_MEMBER, a.POS_ID, a.PER_ADD2, a.PER_STARTDATE, a.PER_TYPE,
											a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, 
											a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2,
											c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, c.ORG_ID_2 as EMP_ORG_ID_2,
											d.POEMS_NO as EMPSER_POS_NO, d.ORG_ID as EMPSER_ORG_ID, d.ORG_ID_1 as EMPSER_ORG_ID_1, d.ORG_ID_2 as EMPSER_ORG_ID_2,
											e.POT_NO as POT_POS_NO, e.ORG_ID as POT_ORG_ID, e.ORG_ID_1 as POT_ORG_ID_1, e.ORG_ID_2 as POT_ORG_ID_2
						 from			PER_PERSONAL a, PER_POSITION b, PER_POS_EMP c
						 where		a.POS_ID=b.POS_ID(+) and a.POEM_ID=c.POEM_ID(+) and a.POEMS_ID = d.POEMS_ID(+) and  a.POT_ID = e.POT_ID
											$search_condition
						 order by		b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2, a.PER_NAME, a.PER_SURNAME
					   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.RE_CODE, a.PER_CARDNO, a.PER_GENDER,
											a.PER_BIRTHDATE, a.MR_CODE, a.PER_MEMBER, a.POS_ID, a.PER_ADD2, a.PER_STARTDATE, a.PER_TYPE,
											a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, 
											a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											b.POS_NO, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2,
											c.POEM_NO as EMP_POS_NO, c.ORG_ID as EMP_ORG_ID, c.ORG_ID_1 as EMP_ORG_ID_1, c.ORG_ID_2 as EMP_ORG_ID_2,
											d.POEMS_NO as EMPSER_POS_NO, d.ORG_ID as EMPSER_ORG_ID, d.ORG_ID_1 as EMPSER_ORG_ID_1, d.ORG_ID_2 as EMPSER_ORG_ID_2,
											e.POT_NO as POT_POS_NO, e.ORG_ID as POT_ORG_ID, e.ORG_ID_1 as POT_ORG_ID_1, e.ORG_ID_2 as POT_ORG_ID_2
						 from		( 
						 					(
												(
						 						PER_PERSONAL a 
												left join PER_POSITION b on (a.POS_ID = b.POS_ID)
											) left join PER_POS_EMP c on (a.POEM_ID = c.POEM_ID)
										)  left join PER_POS_EMPSER d on (a.POEMS_ID = d.POEMS_ID)
									)  left join PER_POS_TEMP e on (a.POT_ID = e.POT_ID)
											$search_condition
						 order by		b.ORG_ID, CLng(b.POS_NO), CLng(c.POEM_NO), CLng(d.POEMS_NO), CLng(e.POT_NO)
					   ";
	}
	if($SESS_ORG_STRUCTURE==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			//$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	if($count_data){
		$pdf->AutoPageBreak = false;
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);			
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = $data2[PN_NAME];		
			
			$RE_CODE = trim($data[RE_CODE]);
			if($RE_CODE != "01" && $RE_CODE != "02" && $RE_CODE != "03"){
				$cmd = " select RE_NAME from PER_RELIGION where trim(RE_CODE)='$RE_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$RE_NAME = $data2[RE_NAME];
			}else{
				$RE_NAME = "";
			} // end if
			
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_GENDER = trim($data[PER_GENDER]);
			
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$BIRTHDATE_D = $BIRTHDATE_M = $BIRTHDATE_Y = $YEAR_AGE = "";
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$BIRTHDATE_D = $arr_temp[2] + 0;
				$BIRTHDATE_M = $month_full[($arr_temp[1] + 0)][TH];
				$BIRTHDATE_Y = $arr_temp[0] + 543;

				$dateDiff = calculate_sec(date("d"), date("m"), date("Y")) - calculate_sec($BIRTHDATE_D, $BIRTHDATE_M, ($BIRTHDATE_Y - 543));
				$yearDiff = floor($dateDiff/60/60/24/365);
				$YEAR_AGE = $yearDiff;
				//$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$MR_CODE = trim($data[MR_CODE]);
			$PER_MEMBER = trim($data[PER_MEMBER]);
			$PER_ADD2 = trim($data[PER_ADD2]);

			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$STARTDATE_D = $STARTDATE_M = $STARTDATE_Y = "";
			if($PER_STARTDATE){
				$arr_temp = explode("-", substr($PER_STARTDATE, 0, 10));
				$STARTDATE_D = $arr_temp[2] + 0;
				$STARTDATE_M = $month_full[($arr_temp[1] + 0)][TH];
				$STARTDATE_Y = $arr_temp[0] + 543;
				//$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE_F' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME_F = $data2[PN_NAME];		

			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			$PN_CODE_M = trim($data[PN_CODE_M]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE_M' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME_M = $data2[PN_NAME];
			
			$PER_TYPE = trim($data[PER_TYPE]);
			$POS_ID = trim($data[POS_ID]);

			if($PER_TYPE==1){
				$POS_NO = trim($data[POS_NO]);
				$ORG_ID = trim($data[ORG_ID]);
				$ORG_ID_1 = trim($data[ORG_ID_1]);
				$ORG_ID_2 = trim($data[ORG_ID_2]);
			}elseif($PER_TYPE==2){
				$POS_NO = trim($data[EMP_POS_NO]);
				$ORG_ID = trim($data[EMP_ORG_ID]);
				$ORG_ID_1 = trim($data[EMP_ORG_ID_1]);
				$ORG_ID_2 = trim($data[EMP_ORG_ID_2]);
			}elseif($PER_TYPE==3){
				$POS_NO = trim($data[EMPSER_POS_NO]);
				$ORG_ID = trim($data[EMPSER_ORG_ID]);
				$ORG_ID_1 = trim($data[EMPSER_ORG_ID_1]);
				$ORG_ID_2 = trim($data[EMPSER_ORG_ID_2]);
			} elseif($PER_TYPE==4){
				$POS_NO = trim($data[POT_POS_NO]);
				$ORG_ID = trim($data[POT_ORG_ID]);
				$ORG_ID_1 = trim($data[POT_ORG_ID_1]);
				$ORG_ID_2 = trim($data[POT_ORG_ID_2]);
			} // end if
			
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);			
	
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);			
	
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
			if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);

			$cmd = " select 		MAH_NAME, MAH_MARRY_DATE, DV_CODE 
							 from 			PER_MARRHIS 
							 where 		PER_ID=$PER_ID 
							 order by 	MAH_MARRY_DATE desc 
						  ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MAH_FULLNAME = trim($data2[MAH_NAME]);
			$MAH_FULLNAME = str_replace("  ", " ", $MAH_FULLNAME);
			$MAH_NAME = $MAH_SURNAME = "";
			if($MAH_FULLNAME){
				$arr_temp = explode(" ", $MAH_FULLNAME, 2);
				$MAH_NAME = $arr_temp[0];
				$MAH_SURNAME = $arr_temp[1];
			} // end if

			$MAH_MARRY_DATE = trim($data2[MAH_MARRY_DATE]);
			$MARRYDATE_D = $MARRYDATE_M = $MARRYDATE_Y = "";
			if($MAH_MARRY_DATE){
				$arr_temp = explode("-", substr($MAH_MARRY_DATE, 0, 10));
				$MARRYDATE_D = $arr_temp[2] + 0;
				$MARRYDATE_M = $month_full[($arr_temp[1] + 0)][TH];
				$MARRYDATE_Y = $arr_temp[0] + 543;
				//$MAH_MARRY_DATE = show_date_format(substr(substr($MAH_MARRY_DATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$DV_CODE = trim($data2[DV_CODE]);			

			for($i=1; $i<=5; $i++){
				${"HEIR_FULLNAME_". $i} = "";
				${"HEIR_NAME_". $i} = "";
				${"HEIR_SURNAME_". $i} = "";
				${"HEIR_BIRTHDAY_". $i} = "";
				${"HEIR_BIRTHDAY_". $i ."_D"} = "";
				${"HEIR_BIRTHDAY_". $i ."_M"} = "";
				${"HEIR_BIRTHDAY_". $i ."_Y"} = "";
				${"HEIR_AGE_". $i} = "";
			} // end for
			
			$cmd = " select 		HEIR_NAME, HEIR_BIRTHDAY
							 from 			PER_HEIR 
							 where 		PER_ID=$PER_ID and HR_CODE='01'
							 order by 	HEIR_BIRTHDAY 
						  ";
			$db_dpis2->send_cmd($cmd);
			$heir_count = 0;
			while($data2 = $db_dpis2->get_array()){
				$heir_count++;
				${"HEIR_FULLNAME_" . $heir_count} = trim($data2[HEIR_NAME]);
				${"HEIR_FULLNAME_" . $heir_count} = str_replace("  ", " ", ${"HEIR_FULLNAME_" . $heir_count});
				${"HEIR_NAME_" . $heir_count} = ${"HEIR_SURNAME_" . $heir_count} = "";
				if(${"HEIR_FULLNAME_" . $heir_count}){
					$arr_temp = explode(" ", ${"HEIR_FULLNAME_" . $heir_count}, 2);
					${"HEIR_NAME_" . $heir_count} = $arr_temp[0];
					${"HEIR_SURNAME_" . $heir_count} = $arr_temp[1];
				} // end if

				${"HEIR_BIRTHDAY_" . $heir_count} = trim($data2[HEIR_BIRTHDAY]);
				${"HEIR_BIRTHDAY_" . $heir_count . "_D"} = ${"HEIR_BIRTHDAY_" . $heir_count . "_M"} = ${"HEIR_BIRTHDAY_" . $heir_count . "_Y"} = ${"HEIR_AGE_" . $heir_count} = "";
				if(${"HEIR_BIRTHDAY_" . $heir_count}){
					$arr_temp = explode("-", substr(${"HEIR_BIRTHDAY_" . $heir_count}, 0, 10));
					${"HEIR_BIRTHDAY_" . $heir_count . "_D"} = $arr_temp[2] + 0;
					${"HEIR_BIRTHDAY_" . $heir_count . "_M"} = $month_full[($arr_temp[1] + 0)][TH];
					${"HEIR_BIRTHDAY_" . $heir_count . "_Y"} = $arr_temp[0] + 543;

					$dateDiff = calculate_sec(date("d"), date("m"), date("Y")) - calculate_sec(${"HEIR_BIRTHDAY_" . $heir_count . "_D"}, ${"HEIR_BIRTHDAY_" . $heir_count . "_M"}, (${"HEIR_BIRTHDAY_" . $heir_count . "_Y"} - 543));
					$yearDiff = floor($dateDiff/60/60/24/365);
					${"HEIR_AGE_" . $heir_count} = $yearDiff;
				} // end if
			} // end while

			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			
			//================================================== เจ้าของสิทธิ ======================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->SetFont($font,'b','',18);
			$pdf->Cell(200, 7, "แบบกรอกข้อมูลในระบบจ่ายตรงค่ารักษาพยาบาล", 0, 0, "C");

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เจ้าของสิทธิ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 24, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 24, ($pdf->y - 1.0));
			
			$pdf->x = 5;		$pdf->y += 5;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "ส่วนราชการ ", 0, 0, "L");
			$pdf->Cell(10, 7, "$ORG_TITLE ", 0, 0, "L");
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(15, 7, "$ORG_NAME", 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(110, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "เลขที่ตำแหน่ง ", 0, 0, "L");
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(15, 7, "$POS_NO", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "$ORG_TITLE1 ", 0, 0, "L");
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(15, 7, "$ORG_NAME_1", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "$ORG_TITLE2 ", 0, 0, "L");
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(15, 7, "$ORG_NAME_2", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 14;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");
			
			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$PN_NAME $PER_NAME", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$PER_SURNAME", 0, 0, "L");
			
			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(12, 7, "สัญชาติ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "ไทย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(40, 7, str_repeat(" ", 40), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			if($RE_CODE=="01") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			if($RE_CODE=="02") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			if($RE_CODE=="03") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			if($RE_CODE != "01" && $RE_CODE != "02" && $RE_CODE != "03") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;
			if($RE_NAME){
				$pdf->SetFont($font,'b','',16);	
				$pdf->x = 170;
				$pdf->Cell(10, 7, "$RE_NAME", 0, 0, "L");
			} // end if
			
			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			if($PER_GENDER==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			if($PER_GENDER==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 50;
			$pdf->Cell(10, 7,card_no_format($PER_CARDNO,$CARD_NO_DISPLAY), 0, 0, "L");
			
			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$BIRTHDATE_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$BIRTHDATE_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$BIRTHDATE_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$YEAR_AGE", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			if($MR_CODE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หม้าย ", 0, 0, "L");
			if($MR_CODE==3 && $DV_CODE=="01") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			if($MR_CODE==3 && ($DV_CODE=="02" || $DV_CODE=="03")) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			if($MR_CODE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(35, 7, "", 0, 0, "L");
			$pdf->Cell(19, 7, "จำนวนบุตร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(46, 7, str_repeat(" ", 47), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "คน ", 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(35, 7, "เป็นสมาชิก กบข./กสจ.: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ไม่เป็น ", 0, 0, "L");
			if($PER_MEMBER==0) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "เป็นแบบสะสม ", 0, 0, "L");
			if($PER_MEMBER==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(48, 7, "วันบรรจุเข้ารับราชการ เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 31), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(65, 7, str_repeat(" ", 69), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(20, 7, str_repeat(" ", 42), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 65;
			$pdf->Cell(10, 7, "$STARTDATE_D", 0, 0, "L");
			$pdf->x = 110;
			$pdf->Cell(10, 7, "$STARTDATE_M", 0, 0, "L");
			$pdf->x = 170;
			$pdf->Cell(10, 7, "$STARTDATE_Y", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(48, 7, "ที่อยู่ที่สามารถติดต่อได้สะดวก : ", 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "", 0, 0, "L");
			$pdf->SetFont($font,'b','',16);
			$pdf->MultiCell(180, 7, "$PER_ADD2", 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(50, 7, "ถ้าต้องการแก้ไขที่อยู่ : บ้านเลขที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 31), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(8, 7, "หมู่ที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(20, 7, str_repeat(" ", 21), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(17, 7, "หมู่บ้านชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(65, 7, str_repeat(" ", 78), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "ซอย ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(55, 7, str_repeat(" ", 58), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(8, 7, "ถนน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(55, 7, str_repeat(" ", 57), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(19, 7, "ตำบล/แขวง ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(55, 7, str_repeat(" ", 58), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(17, 7, "อำเภอ/เขต ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(60, 7, str_repeat(" ", 64), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(11, 7, "จังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(60, 7, str_repeat(" ", 64), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 30), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(14, 7, "โทรศัพท์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 74), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(11, 7, "E-mail ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 58), 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 35;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันเจ้าของสิทธิ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 45, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 45, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชน ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาทะเบียนบ้าน ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 35;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(115, 7, "", 0, 0, "L");
			$pdf->Cell(6, 7, "ขอรับรองว่ารายการที่แสดงไว้เป็นความจริงทุกประการ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(115, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ลงชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(55, 7, str_repeat(" ", 57), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "เจ้าของสิทธิ ", 0, 0, "L");

			//================================================== คู่สมรส ===========================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "คู่สมรส", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 17, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 17, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 14;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$MAH_NAME", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$MAH_SURNAME", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			if($PER_GENDER==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			if($PER_GENDER==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			if($DV_CODE=="01") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			if($MR_CODE==2) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			if($MR_CODE==3 && $DV_CODE=="01") $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			if($MR_CODE==3 && ($DV_CODE=="02" || $DV_CODE=="03")) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			if($MR_CODE==1) $pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");
			else $pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "ณ จังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 57), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 25;
			$pdf->Cell(10, 7, "$MARRYDATE_D", 0, 0, "L");
			$pdf->x = 60;
			$pdf->Cell(10, 7, "$MARRYDATE_M", 0, 0, "L");
			$pdf->x = 115;
			$pdf->Cell(10, 7, "$MARRYDATE_Y", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(23, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(90, 7, str_repeat(" ", 95), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 67), 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 21;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันความสัมพันธ์ระหว่างผู้มีสิทธิกับคู่สมรส", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 87, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 87, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชนหรือสำเนาทะเบียนบ้านของคู่สมรส ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาทะเบียนสมรสของผู้มีสิทธิ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     3.  สำเนาหนังสือสำคัญแสดงการเปลี่ยนชื่อ-สกุลของคู่สมรส (ถ้ามี) ", 0, 0, "L");

			//======================================================== บิดา =========================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บิดา", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 14;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$PN_NAME_F $PER_FATHERNAME", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$PER_FATHERSURNAME", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(138, 7, str_repeat(" ", 147), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบิดาโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนสมรสกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดารับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(70, 7, "โดยบิดาอยู่กินกับมารดาก่อน 1 ตุลาคม 2478 ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 75), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยคำสั่งศาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "ณ จังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 57), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(23, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(90, 7, str_repeat(" ", 95), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 67), 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 21;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันความสัมพันธ์ระหว่างผู้มีสิทธิกับบิดา", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 82, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 82, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชนหรือสำเนาทะเบียนบ้านของบิดา ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาสูติบัตรของผู้มีสิทธิ (หากไม่มี ให้ใช้สำเนาทะเบียนบ้านของผู้มีสิทธิ) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     3.  สำเนาทะเบียนสมรสของบิดา หรือสำเนาทะเบียนหย่า (ในกรณีที่มีการหย่าร้างกับมารดาผู้มีสิทธิ) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     4.  กรณีบิดาของผู้มีสิทธิมิได้จดทะเบียนสมรสกับมารดาของผู้มีสิทธิ ให้ใช้หลักฐานต่อไปนี้ในการยืนยันความสัมพันธ์ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          แทนหลักฐานในข้อ 2. และ 3. ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          4.1  สำเนาการจดทะเบียนรับรองบุตรว่าผู้มีสิทธิเป็นบุตรโดยชอบด้วยกฎหมายของบิดา (แบบ คร.11) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          4.2  สำเนาคำสั่งศาลหรือสำเนาคำพิพากษาว่าผู้มีสิทธิเป็นบุตรโดยชอบด้วยกฎหมายของบิดา ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     5.  สำเนาหนังสือสำคัญแสดงการเปลี่ยนชื่อ-สกุลของบิดา (ถ้ามี) ", 0, 0, "L");

			//========================================================= มารดา ======================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "มารดา", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 16, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 16, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 14;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$PN_NAME_M $PER_MOTHERNAME", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$PER_MOTHERSURNAME", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(136, 7, str_repeat(" ", 145), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_check.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "เป็นมารดา: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "โดยสายเลือด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สูติบัตร หรือ ทะเบียนบ้านเจ้าของสิทธิ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "ณ จังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 57), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(23, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(90, 7, str_repeat(" ", 95), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 67), 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 21;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันความสัมพันธ์ระหว่างผู้มีสิทธิกับมารดา", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 86, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 86, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชนหรือสำเนาทะเบียนบ้านของมารดา ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาสูติบัตรของผู้มีสิทธิ (หากไม่มี ให้ใช้สำเนาทะเบียนบ้านของผู้มีสิทธิ) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     3.  สำเนาหนังสือสำคัญแสดงการเปลี่ยนชื่อ-สกุลของมารดา (ถ้ามี) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 10;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "หมายเหตุ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "1.  ในกรณีที่บิดามารดาของผู้มีสิทธิอยู่กินกันฉันสามีภรรยาก่อนวันที่ 1 ตุลาคม 2478 ซึ่งกฎหมายไม่บังคับให้ต้องจดทะเบียนสมรสกัน ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     ให้ผู้มีสิทธิใช้หนังสือรับรองของผู้ควรเชื่อถือได้ ที่รับรองว่าบิดามารดาของผู้มีสิทธิอยู่กินกันฉันสามีภรรยาก่อนวันที่ 1 ตุลาคม 2478 ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     (เช่น คำรับรองของนายอำเภอหรือปลัดอำเภอหรือผู้อำนวยการสำนักงานเขตต่าง ๆ ) เป็นเอกสารอ้างอิงในการยืนยันความถูกต้อง ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     ของข้อมูล ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "2.  ในกรณีที่บิดามารดาของผู้มีสิทธิเป็นคนต่างด้าว ซึ่งบุคคลต่างด้าวจะไม่มีบัตรประชาชน แต่จะมีเลขที่บัตรประชาชน 13 หลัก ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     ปรากฎอยู่ในทะเบียนบ้านของบุคคลเหล่านั้น ดังนั้นการตรวจสอบสิทธิของบิดาหรือมารดาของผู้มีสิทธิที่เป็นคนต่างด้าวให้ใช้ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     สำเนาเลขที่บัตรประชาชน 13 หลักในทะเบียนบ้าน ประกอบกับสำเนาใบสำคัญทะเบียนต่างด้าวเป็นเอกสารแทนสำเนาบัตรประจำ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     ตัวประชาชน เพื่อใช้อ้างอิงในการยืนยันความถูกต้องของข้อมูล ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "3.  หากเอกสารสำคัญทางทะเบียนสูญหาย เช่น ทะเบียนสมรส สูติบัตร หรือหนังสือการจดทะเบียนรับรองบุตร ให้ดำเนินการติดต่อ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     ขอคัดสำเนาได้ ณ สำนักงานเขตหรือที่ว่าการอำเภอที่เคยจดทะเบียนไว้ ", 0, 0, "L");

			//======================================================= บุตร 1 ========================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บุตร", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$HEIR_NAME_1", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$HEIR_SURNAME_1", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_1_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_1_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_1_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$HEIR_AGE_1", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบุตรโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 85), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(28, 7, "ทะเบียนบ้านบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 32), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "สูติบัตรบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(24, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(34, 7, str_repeat(" ", 34), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "", 0, 0, "L");
			$pdf->Cell(90, 7, "ไร้ความสามารถ / เสมือนไร้ความสามารถ : เอกสารอ้างอิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 94), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(50, 7, "คำสั่งศาล - ไร้ความสามารถ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 54), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			//======================================================= บุตร 2 ========================================================
			$pdf->x = 5;		$pdf->y += 24;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บุตร", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$HEIR_NAME_2", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$HEIR_SURNAME_2", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_2_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_2_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_2_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$HEIR_AGE_2", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบุตรโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 85), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(28, 7, "ทะเบียนบ้านบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 32), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "สูติบัตรบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(24, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(34, 7, str_repeat(" ", 34), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "", 0, 0, "L");
			$pdf->Cell(90, 7, "ไร้ความสามารถ / เสมือนไร้ความสามารถ : เอกสารอ้างอิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 94), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(50, 7, "คำสั่งศาล - ไร้ความสามารถ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 54), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			//======================================================= บุตร 3 ========================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บุตร", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$HEIR_NAME_3", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$HEIR_SURNAME_3", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_3_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_3_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_3_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$HEIR_AGE_3", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบุตรโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 85), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(28, 7, "ทะเบียนบ้านบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 32), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "สูติบัตรบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(24, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(34, 7, str_repeat(" ", 34), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "", 0, 0, "L");
			$pdf->Cell(90, 7, "ไร้ความสามารถ / เสมือนไร้ความสามารถ : เอกสารอ้างอิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 94), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(50, 7, "คำสั่งศาล - ไร้ความสามารถ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 54), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			//======================================================= บุตร 4 ========================================================
			$pdf->x = 5;		$pdf->y += 24;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บุตร", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$HEIR_NAME_4", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$HEIR_SURNAME_4", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_4_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_4_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_4_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$HEIR_AGE_4", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบุตรโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 85), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(28, 7, "ทะเบียนบ้านบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 32), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "สูติบัตรบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(24, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(34, 7, str_repeat(" ", 34), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "", 0, 0, "L");
			$pdf->Cell(90, 7, "ไร้ความสามารถ / เสมือนไร้ความสามารถ : เอกสารอ้างอิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 94), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(50, 7, "คำสั่งศาล - ไร้ความสามารถ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 54), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			//======================================================= บุตร 5 ========================================================
			$pdf->AliasNbPages();
			$pdf->AddPage();

			$pdf->x = 5;		$pdf->y += 12;			
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "บุตร", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 13, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 13, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "ชื่อ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(88, 7, str_repeat(" ", 93), 0, 0, "L");
			$pdf->SetFont($font,'',16);			
			$pdf->Cell(16, 7, " นามสกุล ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(85, 7, str_repeat(" ", 95), 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 20;
			$pdf->Cell(10, 7, "$HEIR_NAME_5", 0, 0, "L");
			$pdf->x = 129;
			$pdf->Cell(10, 7, "$HEIR_SURNAME_5", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(37, 7, "รหัสประจำตัวประชาชน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(120, 7, str_repeat(" ", 127), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "เพศ: ", 0, 0, "L");
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "ชาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, str_repeat(" ", 5), 0, 0, "L");
			$pdf->Cell(7, 7, "หญิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เกิดวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 37), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(70, 7, str_repeat(" ", 75), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(33, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "อายุ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(21, 7, str_repeat(" ", 22), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "ปี ", 0, 0, "L");

			$pdf->y -= 0.5;
			$pdf->SetFont($font,'b','',16);	
			$pdf->x = 30;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_5_D", 0, 0, "L");
			$pdf->x = 75;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_5_M", 0, 0, "L");
			$pdf->x = 150;
			$pdf->Cell(10, 7, "$HEIR_BIRTHDAY_5_Y", 0, 0, "L");
			$pdf->x = 185;
			$pdf->Cell(10, 7, "$HEIR_AGE_5", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(15, 7, "สถานภาพ: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "มีชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");			
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "สาบสูญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(13, 7, "เสียชีวิต ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 17), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(12, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ศาสนา: ", 0, 0, "L");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "พุทธ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "คริสต์ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "อิสลาม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(30, 7, str_repeat(" ", 43), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(10, 7, "อาชีพ: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "ค้าขาย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "พนักงานท้องถิ่น/เทศบาล ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "รับจ้าง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 15), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "รับเบี้ยหวัดบำนาญ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "นักการเมือง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "รับราชการ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เกษตรกร/กสิกรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่รัฐวิสาหกิจ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(30, 7, "เจ้าหน้าที่องค์การของรัฐ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 35), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(15, 7, "ไม่ทำงาน ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 20), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 70), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เป็นบุตรโดย: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับรองบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยการรับเป็นบุตรบุญธรรม ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(40, 7, "โดยบิดาจดทะเบียนกับมารดา ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 45), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(30, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "อื่นๆ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 13), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 85), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(28, 7, "ทะเบียนบ้านบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 32), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(25, 7, "สูติบัตรบุตร ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 29), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "สถานภาพสมรส: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "สมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 11), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(10, 7, "หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 14), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(7, 7, "โสด ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 10.5), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(25, 7, "ประเภทเอกสาร: ", 0, 0, "L");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "ทะเบียนสมรส ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(8, 7, "หย่า ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 12), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(7, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "มรณบัตร-หม้าย ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 24), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(8, 7, "", 0, 0, "L");
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 74), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(35, 7, str_repeat(" ", 35), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(15, 7, str_repeat(" ", 15), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(24, 7, "อาศัยในจังหวัด ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(34, 7, str_repeat(" ", 34), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(22, 7, "รหัสไปรษณีย์ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;
			$pdf->SetFont($font,'',16);
			$pdf->Cell(5, 7, "", 0, 0, "L");
			$pdf->Cell(90, 7, "ไร้ความสามารถ / เสมือนไร้ความสามารถ : เอกสารอ้างอิง ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 94), ($pdf->y + 1.5), 4, 4,"jpg");
			$pdf->Cell(10, 7, "", 0, 0, "L");
			$pdf->Cell(50, 7, "คำสั่งศาล - ไร้ความสามารถ ", 0, 0, "L");
			$pdf->Image("../images/checkbox_blank.jpg", ($pdf->x - 54), ($pdf->y + 1.5), 4, 4,"jpg");

			$pdf->y -= 0.5;

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(20, 7, "เลขที่เอกสาร ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 52), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(13, 7, "เมื่อวันที่ ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(9, 7, "เดือน ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(50, 7, str_repeat(" ", 53), 0, 0, "L");
			$pdf->SetFont($font,'',16);
			$pdf->Cell(7, 7, "พ.ศ. ", 0, 0, "L");
			$pdf->SetFont($font,'u',16);
			$pdf->Cell(25, 7, str_repeat(" ", 25), 0, 0, "L");
			$pdf->SetFont($font,'',16);

			$pdf->x = 5;		$pdf->y += 21;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันความสัมพันธ์ระหว่างผู้มีสิทธิกับบุตร (กรณีผู้มีสิทธิเป็นบิดา)", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 117, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 117, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชนหรือสำเนาทะเบียนบ้านของบุตร ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาสูติบัตรของบุตร (หากไม่มี ให้ใช้สำเนาทะเบียนบ้านของบุตร) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     3.  สำเนาทะเบียนสมรสของผู้มีสิทธิกับคู่สมรสที่เป็นมารดาของบุตร หรือสำเนาทะเบียนหย่า (ในกรณีที่มีการหย่าร้างกับมารดา ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          ของบุตร) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     4.  กรณีผู้มีสิทธิมิได้จดทะเบียนสมรสกับมารดาของบุตร ให้ใช้หลักฐานต่อไปนี้ในการยืนยันความสัมพันธ์แทนหลักฐาน ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          ในข้อ 2. และ 3. ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          4.1  สำเนาการจดทะเบียนรับรองบุตรว่าผู้มีสิทธิเป็นบิดาโดยชอบด้วยกฎหมายของบุตร (แบบ คร.11) หรือ ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "          4.2  สำเนาคำสั่งศาลหรือสำเนาคำพิพากษาว่าผู้มีสิทธิเป็นบิดาโดยชอบด้วยกฎหมายของบุตร ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     5.  สำเนาหนังสือสำคัญแสดงการเปลี่ยนชื่อ-สกุลของบุตร (ถ้ามี) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 10;
			$pdf->SetFont($font,'b','',16);
			$pdf->Cell(200, 7, "เอกสารยืนยันความสัมพันธ์ระหว่างผู้มีสิทธิกับบุตร (กรณีผู้มีสิทธิเป็นมารดา)", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->Line(6, ($pdf->y - 1.5), 121, ($pdf->y - 1.5));
			$pdf->Line(6, ($pdf->y - 1.0), 121, ($pdf->y - 1.0));

			$pdf->x = 5;		$pdf->y += 3;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     1.  สำเนาบัตรประจำตัวประชาชนหรือสำเนาทะเบียนบ้านของบุตร ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     2.  สำเนาสูติบัตรของบุตร (หากไม่มี ให้ใช้สำเนาทะเบียนบ้านของบุตร) ", 0, 0, "L");

			$pdf->x = 5;		$pdf->y += 7;			
			$pdf->SetFont($font,'',16);
			$pdf->Cell(6, 7, "     3.  สำเนาหนังสือสำคัญแสดงการเปลี่ยนชื่อ-สกุลของบุตร (ถ้ามี) ", 0, 0, "L");

		} // end while

	}else{
		$pdf->AliasNbPages();
		$pdf->AddPage();

		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>