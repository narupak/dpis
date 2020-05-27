<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_history_name = explode("|", $HISTORY_LIST);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
//	$arr_search_condition[] = "(a.PER_TYPE=1)";

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
		if(trim($search_pos_no))  {	
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO) = '$search_pos_no')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO) = '$search_pos_no')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO) = '$search_pos_no')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO) = '$search_pos_no') or (trim(POEM_NO) = '$search_pos_no') or (trim(POEMS_NO) = '$search_pos_no') or (trim(POT_NO) = '$search_pos_no')) ";
		}
		if(trim($search_pos_no_name)){
			if ($search_per_type == 1 || $search_per_type==5)
				$arr_search_condition[] = "(trim(POS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 2) 
				$arr_search_condition[] = "(trim(POEM_NO_NAME) = '$search_pos_no_name')";		
			elseif ($search_per_type == 3) 	
				$arr_search_condition[] = "(trim(POEMS_NO_NAME) = '$search_pos_no_name')";
			elseif ($search_per_type == 4) 	
				$arr_search_condition[] = "(trim(POT_NO_NAME) = '$search_pos_no_name')";
			else if ($search_per_type==0)		//ทั้งหมด
				$arr_search_condition[] = "((trim(POS_NO_NAME) = '$search_pos_no_name') or (trim(POEM_NO_NAME) = '$search_pos_no_name') or (trim(POEMS_NO_NAME) = '$search_pos_no_name') or (trim(POT_NO_NAME) = '$search_pos_no_name')) ";
		}
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
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("R04065"):"R04065");
	$orientation='P';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;
	
	if($DPISDB=="odbc"){
		$cmd = " select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_TYPE, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID,
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
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
		$cmd = "select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_TYPE, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID,
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f,PER_POS_TEMP g
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.POT_ID=g.POT_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						order by		NLSSORT(a.PER_NAME,'NLS_SORT=THAI_DICTIONARY'), NLSSORT(a.PER_SURNAME,'NLS_SORT=THAI_DICTIONARY') ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select		a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, f.POSITION_TYPE, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,
											a.PER_TYPE, a.PER_CARDNO, a.POS_ID, a.POEM_ID, a.POEMS_ID,a.POT_ID,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2, 
											c.PM_CODE, a.PER_OFFNO, a.ES_CODE, a.PAY_ID,
											g.POT_NO_NAME as TEMP_POS_NO_NAME, g.POT_NO as TEMP_POS_NO, g.TP_CODE as TEMP_PL_CODE, 
											g.ORG_ID as TEMP_ORG_ID, g.ORG_ID_1 as TEMP_ORG_ID_1, g.ORG_ID_2 as TEMP_ORG_ID_2
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
			$PAY_ID = $data[PAY_ID];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO_NAME = trim($data[POS_NO_NAME]);
				if (substr($POS_NO_NAME,0,4)=="กปด.")
					$POS_NO = $POS_NO_NAME." ".trim($data[POS_NO]);
				else
					$POS_NO = $POS_NO_NAME.trim($data[POS_NO]);
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
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
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
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
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
				$POS_NO = $data[TEMP_POS_NO_NAME].$data[TEMP_POS_NO];
				$PL_CODE = trim($data[TEMP_PL_CODE]);
				$ORG_ID = $data[TEMP_ORG_ID];
				$ORG_ID_1 = $data[TEMP_ORG_ID_1];
				$ORG_ID_2 = $data[TEMP_ORG_ID_2];

				$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[TP_NAME]);
			} 

			// สถานะการดำรงตำแหน่ง
			$ES_CODE = trim($data[ES_CODE]);
			$cmd = " select ES_NAME from PER_EMP_STATUS where trim(ES_CODE)='$ES_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ES_NAME = trim($data2[ES_NAME]);

			// สังกัด
			$ORG_NAME = "";
			if($ORG_ID){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$PM_CODE = $data[PM_CODE];	
			$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim($data[LEVEL_NAME]);
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
			$arr_temp = explode(" ", $LEVEL_NAME);
			$LEVEL_NAME ="";
			$LEVEL_NAME =  $arr_temp[1];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";
			$PER_CARDNO = $data[PER_CARDNO];	
			$PER_OFFNO = $data[PER_OFFNO];	

//			$img_file = "";
//			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";
//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
//echo "IMG:$cmd<br>";
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
					$PIC_SHOW = trim($data2[PIC_SHOW]);

					if ($PIC_SHOW == '1') {
						$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgshow[] = 1;
						$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
					} else {
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
					}
				} // end while loop
			} else {
				//$img_file="";
				$img_file=$IMG_PATH."shadow.jpg";
			}

			if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
				$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
				if ($db_dpis2->send_cmd($cmd)) { 
					$data2 = $db_dpis2->get_array();
					$SERVER_NAME = trim($data2[SERVER_NAME]);
					$ftp_server = trim($data2[FTP_SERVER]);
					$ftp_username = trim($data2[FTP_USERNAME]);
					$ftp_password = trim($data2[FTP_PASSWORD]);
					$main_path = trim($data2[MAIN_PATH]);
					$http_server = trim($data2[HTTP_SERVER]);
					if ($http_server) {
//						echo "1.".$http_server."/".$img_file."<br>";
						$fp = @fopen($http_server."/".$img_file, "r");
						if ($fp !== false) $img_file = $http_server."/".$img_file;
						else $img_file=$IMG_PATH."shadow.jpg";
						fclose($fp);
					} else {
//						echo "2.".$img_file."<br>";
						$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					}
				} else $img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
			} else $img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
//			echo "img_file=$img_file<br>";

			$today = date("Y-m-d");
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$AGE_DIFF = date_difference($today, $PER_BIRTHDATE, "full");
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),$DATE_DISPLAY);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_SPSALARY = $PER_MGTSALARY = $PER_TOTALSALARY = $EXH_SEQ = $EX_SEQ = "";
			$cmd = " select EX_NAME, EXH_AMT	from PER_EXTRAHIS a, PER_EXTRATYPE b
							where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and EXH_ACTIVE = 1 and 
							(EXH_ENDDATE is NULL or EXH_ENDDATE >= '$UPDATE_DATE')
							order by EX_SEQ_NO, b.EX_CODE ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$EXH_SEQ++;
				$EX_NAME = trim($data2[EX_NAME]);
				$EXH_AMT = $data2[EXH_AMT];
				$PER_TOTALSALARY += $EXH_AMT;
				$PER_SPSALARY .= $EXH_SEQ.". ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[EXH_AMT])."  บาท"."\n";
			}

			if ($PER_TYPE=="1" || $PER_TYPE==5) {
				$cmd = " select EX_NAME, PMH_AMT from PER_POS_MGTSALARYHIS a, PER_EXTRATYPE b 
								  where trim(a.EX_CODE)=trim(b.EX_CODE) and PER_ID=$PER_ID and PMH_ACTIVE = 1 and 
								(PMH_ENDDATE is NULL or PMH_ENDDATE >= '$UPDATE_DATE')
								  order by EX_SEQ_NO, b.EX_CODE ";
				$db_dpis2->send_cmd($cmd);
				while($data2 = $db_dpis2->get_array()){
					$EX_SEQ++;
					$EX_NAME = trim($data2[EX_NAME]);
					$PMH_AMT = $data2[PMH_AMT];
					$PER_TOTALSALARY += $PMH_AMT;
				$PER_MGTSALARY .= $EX_SEQ.". ".$EX_NAME."  จำนวนเงิน : ".number_format($data2[PMH_AMT])."  บาท"."\n";
				}
				$PER_TOTALSALARY = number_format($PER_TOTALSALARY,2);
			}

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$DATE_DIFF = date_difference($today, $PER_STARTDATE, "full");
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),$DATE_DISPLAY);
			} // end if

			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=3
							order by POH_EFFECTIVEDATE DESC ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			if ($POH_EFFECTIVEDATE) {	
				$POS_UP_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}

			$cmd = " select POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=5
							order by POH_EFFECTIVEDATE DESC ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			$POH_ENDDATE = trim($data2[POH_ENDDATE]);
			$POH_ORG = trim($data2[POH_ORG]);
			if ($POH_EFFECTIVEDATE) {	
				$HELP_START_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}
			if ($POH_ENDDATE) {	
				$HELP_END_DATE = show_date_format(substr($POH_ENDDATE, 0, 10),$DATE_DISPLAY);
			}

			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID and POH_POS_NO='$POS_NO'
							order by POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			if ($POH_EFFECTIVEDATE) {	
				$POH_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
			}

			if ($PAY_ID) {			
				$cmd = " select 	POS_NO 
						from 	PER_POSITION where POS_ID=$PAY_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$PAY_NO = trim($data_dpis2[POS_NO]);
			}
			$PER_ID_PAY = "";
			if($PAY_NO){
				if($DPISDB=="odbc"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO='$PAY_NO' and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="oci8"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a, PER_PERSONAL b
										where	a.POS_ID=b.PAY_ID(+) and a.PAY_NO='$PAY_NO' and PER_TYPE = 1 and PER_STATUS = 1 ";
				}elseif($DPISDB=="mysql"){
					$cmd = "	select	b.PER_ID
										from		PER_POSITION a
														left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
										where	a.PAY_NO='$PAY_NO' and PER_TYPE = 1 and PER_STATUS = 1 ";
				} // end if
				$db_dpis2->send_cmd($cmd);
	//			echo "$cmd<br>";
	//			$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_ID_PAY = trim($data2[PER_ID]);
			}
			//echo $PAY_NO;
			$SAH_EFFECTIVEDATE = "";
			if($PER_ID_PAY){
				$cmd = " select SAH_EFFECTIVEDATE
								from   PER_SALARYHIS
								where PER_ID=$PER_ID_PAY and SAH_PAY_NO='$PAY_NO'
								order by SAH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
				if ($SAH_EFFECTIVEDATE) {	
					$PAY_DATE = show_date_format(substr($SAH_EFFECTIVEDATE, 0, 10),$DATE_DISPLAY);
				}
			}
			$cmd = " select PM_CODE, PL_CODE, CL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2
							from   PER_POSITION
							where POS_NO='$PAY_NO' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PM_CODE = trim($data2[PM_CODE]);
			$PL_CODE = trim($data2[PL_CODE]);
			$CL_NAME = trim($data2[CL_NAME]);
			$ORG_ID = $data2[ORG_ID];
			$ORG_ID_1 = $data2[ORG_ID_1];
			$ORG_ID_2 = $data2[ORG_ID_2];
			$PAY_ORG_NAME = "";
			if($ORG_ID){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $PAY_ORG_NAME = trim($data2[ORG_NAME]);
			}
			$PAY_ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $PAY_ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$PAY_ORG_NAME_2 = "";
			if($ORG_ID_2){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $PAY_ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PAY_PM_NAME = trim($data2[PM_NAME]);

			$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PAY_PL_NAME = trim($data2[PL_NAME]);

			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$CL_NAME' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PAY_LEVEL_NAME = trim($data[LEVEL_NAME]);

			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$this_y = $pdf->y;
			$this_x = $pdf->x;
			if($img_file){
				$image_x = $pdf->x;		$image_y = $pdf->y;		$image_w = 30;		$image_h = 35;
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} // end if

			$pdf->x = $this_x+35;
			$pdf->y = $this_y;
			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(80 ,7,"ชื่อ :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($FULLNAME):$FULLNAME),0,0,"L");
			$pdf->Cell(80, 7, "เลขประจำตัว :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_OFFNO):$PER_OFFNO), 0, 1, "L");

			$pdf->x = $this_x+35;
			$pdf->Cell(80 ,7,"เลขประจำตัวประชาชน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit(card_no_format($PER_CARDNO,$CARD_NO_DISPLAY)):card_no_format($PER_CARDNO,$CARD_NO_DISPLAY)),0,0,"L");
			$pdf->Cell(80 ,7,"วันเกิด :  ".($PER_BIRTHDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE):"-")."  อายุ :  ".($AGE_DIFF?(($NUMBER_DISPLAY==2)?convert2thaidigit($AGE_DIFF):$AGE_DIFF):"-"),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(80 ,7,"วันที่บรรจุเข้ารับราชการ :  ".($PER_STARTDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE):"-"),0,0,"L");
			$pdf->Cell(80 ,7,"อายุราชการ :  ".($DATE_DIFF?(($NUMBER_DISPLAY==2)?convert2thaidigit($DATE_DIFF):$DATE_DIFF):"-"),0,1,"L");
			
			$pdf->x = $this_x+35;
			$pdf->Cell(80 ,7,"วันเกษียณอายุราชการ :  ".($PER_RETIREDATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE):"-"),0,0,"L");
			$pdf->Cell(80 ,7,"อัตราเงินเดือน :  ".($PER_SALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY))." บาท":($PER_MGTSALARY?(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_MGTSALARY):$PER_MGTSALARY)." บาท":"-")),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(80 ,7,"วันเลื่อนระดับ :  ".($POS_UP_DATE?(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_UP_DATE):$POS_UP_DATE):"-"),0,0,"L");
			$pdf->Cell(80 ,7,"ระดับตำแหน่ง :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NAME):$LEVEL_NAME),0,1,"L");

			$pdf->Cell(30 ,7,"ตำแหน่ง",0,1,"C");
			
			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,"สถานะการดำรงตำแหน่ง :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($ES_NAME):$ES_NAME),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("เลขที่ตำแหน่ง :  $POS_NO"."    ตำแหน่ง :  ".($PM_NAME?$PM_NAME:$PL_NAME)."    สายงาน :  $PL_NAME"):"เลขที่ตำแหน่ง :  $POS_NO"."    ตำแหน่ง :  ".($PM_NAME?$PM_NAME:$PL_NAME)."    สายงาน :  $PL_NAME"),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ประเภทตำแหน่ง :  $POSITION_TYPE $LEVEL_NAME"):"ประเภทตำแหน่ง :  $POSITION_TYPE $LEVEL_NAME"),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,"สังกัด :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit(($ORG_NAME_2?$ORG_NAME_2:"")." ".($ORG_NAME_1?$ORG_NAME_1:"")." ".($ORG_NAME?$ORG_NAME:"")):($ORG_NAME_2?$ORG_NAME_2:"")." ".($ORG_NAME_1?$ORG_NAME_1:"")." ".($ORG_NAME?$ORG_NAME:"")),0,1,"L");

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,"วันที่ครองตำแหน่ง :  ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POH_DATE):$POH_DATE),0,1,"L");

			if ($ES_CODE != "02") {
				$pdf->Cell(30 ,7,"ช่วยราชการ",0,1,"C");
			
				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$POH_ORG"):"$POH_ORG"),0,1,"L");

				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("วันที่เริ่ม :  $HELP_START_DATE"."  ถึงวันที่ :  $HELP_END_DATE"):"วันที่เริ่ม :  $HELP_START_DATE"."  ถึงวันที่ :  $HELP_END_DATE"),0,1,"L");
			}
			
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
				$pdf->Cell(30 ,7,"เงินเดือนตั้งจ่าย",0,1,"C");
				
				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("เลขที่เงินเดือน :  $PAY_NO"."    ตำแหน่ง :  ".($PAY_PM_NAME?$PAY_PM_NAME:$PAY_PL_NAME)."    สายงาน :  $PAY_PL_NAME"):"เลขที่เงินเดือน :  $PAY_NO"."    ตำแหน่ง :  ".($PAY_PM_NAME?$PAY_PM_NAME:$PAY_PL_NAME)."    สายงาน :  $PAY_PL_NAME"),0,1,"L");

				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ประเภทตำแหน่ง :  $PAY_LEVEL_NAME"):"ประเภทตำแหน่ง :  $PAY_LEVEL_NAME"),0,1,"L");

				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("สังกัด :  ".($PAY_ORG_NAME_2?$PAY_ORG_NAME_2:"")." ".($PAY_ORG_NAME_1?$PAY_ORG_NAME_1:"")." ".($PAY_ORG_NAME?$PAY_ORG_NAME:"")):"สังกัด :  ".($PAY_ORG_NAME_2?$PAY_ORG_NAME_2:"")." ".($PAY_ORG_NAME_1?$PAY_ORG_NAME_1:"")." ".($PAY_ORG_NAME?$PAY_ORG_NAME:"")),0,1,"L");

				$pdf->x = $this_x+35;
				$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("วันที่ครองเลขถือจ่าย :  $PAY_DATE"):"วันที่ครองเลขถือจ่าย :  $PAY_DATE"),0,1,"L");
			}

			$pdf->Cell(30 ,7,"ค่าตอบแทนอื่น ๆ",0,1,"C");
			
			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,"เงินตอบแทนบุคคล :",0,1,"L");

			if ($PER_SPSALARY)	{ 
				$pdf->x = $this_x+35;
				$pdf->Cell(165 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$PER_SPSALARY"):"$PER_SPSALARY"),0,1,"L");
			}

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,"เงินประจำตำแหน่ง :",0,1,"L");

			if ($PER_MGTSALARY)	{ 
				$pdf->x = $this_x+35;
				$pdf->MultiCell(165 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("$PER_MGTSALARY"):"$PER_MGTSALARY"),0,1,"L");
			}

			$pdf->x = $this_x+35;
			$pdf->Cell(40 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("รวมเป็นเงิน :  $PER_TOTALSALARY  บาท"):"รวมเป็นเงิน :  $PER_TOTALSALARY  บาท"),0,1,"L");
			
			if ($data_count < $count_data) $pdf->AddPage();
		} // end loop while $data
	}else{
		$pdf->SetFont($font,'b','',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();
?>