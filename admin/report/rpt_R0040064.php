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
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("R04064"):"R04064");
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

			$PV_CODE = trim($data[PV_CODE]);
			$PV_NAME = "";
			if($PV_CODE){
			 $cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = '$PV_CODE' ";
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $PV_NAME = trim($data2[PV_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$TYPE_NAME =  $data[POSITION_TYPE];
			$LEVEL_NAME = $data[POSITION_LEVEL];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];

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

			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") {
				$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
				$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			} else {
				$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			}
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),3);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),3);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			if($PER_STARTDATE){
				$PER_STARTDATE = show_date_format(substr($PER_STARTDATE, 0, 10),3);
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

			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$this_y = $pdf->y;
			$this_x = $pdf->x;
			if($img_file){
				$image_x = $pdf->x;		$image_y = $pdf->y;		$image_w = 30;			$image_h = 35;
				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
			} // end if

			$pdf->x = $this_x+40;
			$pdf->y = $this_y;
			$pdf->SetFont($font,'',22);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->Cell(160 ,14,(($NUMBER_DISPLAY==2)?convert2thaidigit($FULLNAME):$FULLNAME),0,1,"C");

			$pdf->SetFont($font,'',20);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			
			$pdf->Cell(40 ,7,"",0,0,"L");
			$pdf->Cell(160 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PM_NAME ? $PM_NAME : $PL_NAME):$PM_NAME ? $PM_NAME : $PL_NAME),0,1,"C");

			$pdf->SetFont($font,'',14);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			
			$pdf->Cell(40 ,7,"",0,0,"L");
			$pdf->Cell(160 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit(($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME):($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME),0,1,"C");

			$pdf->Cell(200 ,7,"",0,1,"L");

			$pdf->Cell(40 ,7,"วัน เดือน ปี เกิด:",0,0,"L");
			$pdf->Cell(70 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE),0,0,"L");
			$pdf->Cell(40 ,7,"ภูมิลำเนา:",0,0,"L");
			$pdf->Cell(70 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PV_NAME):$PV_NAME),0,1,"L");
			
			$pdf->Cell(40 ,7,"บรรจุเข้ารับราชการ:",0,0,"L");
			$pdf->Cell(70 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_STARTDATE):$PER_STARTDATE),0,0,"L");
			$pdf->Cell(40 ,7,"เกษียณอายุราชการ:",0,0,"L");
			$pdf->Cell(70 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE),0,1,"L");

			$pdf->Cell(40 ,7,"คู่สมรส:",0,0,"L");
			$pdf->Cell(70 ,7,(($NUMBER_DISPLAY==2)?convert2thaidigit($SHOW_SPOUSE):$SHOW_SPOUSE),0,0,"L");
			$pdf->Cell(40 ,7,"ระดับ: ".(($NUMBER_DISPLAY==2)?convert2thaidigit($LEVEL_NAME):$LEVEL_NAME),0,0,"L");
			$pdf->Cell(70 ,7," เงินเดือน: ".(($NUMBER_DISPLAY==2)?convert2thaidigit(number_format($PER_SALARY)):number_format($PER_SALARY))." บาท",0,1,"L");

			$pdf->Cell(200 ,7,"",0,1,"L");

			$arr_DEH = explode("\n", $DEH_SHOW);
			$label = "เครื่องราชอิสริยาภรณ์:";
			for($ii = 0; $ii < count($arr_DEH); $ii++) {
				$pdf->Cell(40 ,7,$label,0,0,"L");
				$pdf->Cell(160 ,7,"- ".(($NUMBER_DISPLAY==2)?convert2thaidigit($arr_DEH[$ii]):$arr_DEH[$ii]),0,1,"L");
				$label = "";
			}

//			$pdf->AutoPageBreak = false;

							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							
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
//							$db_dpis2->show_error();
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
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									$edutext = ($arr_content[$row_count][en_name]?$arr_content[$row_count][en_name]."  : ":"").($arr_content[$row_count][em_name]?$arr_content[$row_count][em_name]."  : ":"").($arr_content[$row_count][edu_period]?$arr_content[$row_count][edu_period]."  : ":"").($arr_content[$row_count][ins_name]?$arr_content[$row_count][ins_name]." ":"");
									$pdf->Cell(40, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($label):$label),0, 0, "L");
									$pdf->Cell(160, 7, "- ".(($NUMBER_DISPLAY==2)?convert2thaidigit($edutext):$edutext), 0, 1, "L");
									$label = "";
								} // end for loop $row_count
//							}else{
//								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if

							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							
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
//							$db_dpis2->show_error();
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
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									// พิมพ์ TRN
									$trntext = ($arr_content[$row_count][tr_name]?$arr_content[$row_count][tr_name]." ":"").
													  ($arr_content[$row_count][trn_year]?$arr_content[$row_count][trn_year]:"");
									$pdf->Cell(40, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($label):$label),0, 0, "L");
									$pdf->Cell(160, 7, "- ".(($NUMBER_DISPLAY==2)?convert2thaidigit($trntext):$trntext), 0, 1, "L");
									$label = "";
								} // end for loop $row_count
//							}else{
//								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//								$pdf->Cell(200,7,"********** ไม่มีข้อมูล **********","LBR",1,'C');
							} // end if

							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							
							if($DPISDB=="odbc"){
								$cmd = " select			a.REH_YEAR, b.REW_NAME, a.REH_ORG, a.REH_OTHER_PERFORMANCE
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE desc ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select			a.REH_YEAR, b.REW_NAME, a.REH_ORG, a.REH_OTHER_PERFORMANCE
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE desc ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select			a.REH_YEAR, b.REW_NAME, a.REH_ORG, a.REH_OTHER_PERFORMANCE
												 from			PER_REWARDHIS a, PER_REWARD b
												 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												 order by		a.REH_DATE desc ";		
							} // end if
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							$arr_content = (array) null;
							if($count_rewardhis){
								$row_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$REH_YEAR = "ปี ".trim($data2[REH_YEAR]);
									$REH_ORG = trim($data2[REH_ORG]);
									$REW_NAME = trim($data2[REW_NAME]);				
									if ($REW_NAME=="อื่น ๆ") $REW_NAME = trim($data2[REH_OTHER_PERFORMANCE]);
									$arr_content[$row_count][reh_year]=$REH_YEAR;
									$arr_content[$row_count][reh_org] = $REH_ORG;
									$arr_content[$row_count][rew_name] = $REW_NAME;
									$row_count++;
								} // end while data2 for PER_REWARDHIS
							} // end if($count_rewardhis)
							if (count($arr_content) > 0) {
								$label = "ผลงานดีเด่น:";
								for($row_count=0; $row_count < count($arr_content); $row_count++) {
									$border = "";
									$pdf->SetFont($font,'',14);
									$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
									// พิมพ์ REW
									$trntext = ($arr_content[$row_count][rew_name]?$arr_content[$row_count][rew_name]."  : ":"").
													  ($arr_content[$row_count][reh_year]?$arr_content[$row_count][reh_year]."  : ":"").
													  ($arr_content[$row_count][reh_org]?$arr_content[$row_count][reh_org]:"");
									$pdf->Cell(40, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($label):$label),0, 0, "L");
									$pdf->MultiCell(160, 7, "- ".(($NUMBER_DISPLAY==2)?convert2thaidigit($trntext):$trntext), 0, 1, "L");
									$label = "";
								} // end for loop $row_count
							} // end if

							if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();
							$pdf->SetFont($font,'b',14);
							$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
							$pdf->Cell(30 ,7,"ประวัติการดำรงตำแหน่ง:",0,1,"L");
                                                        $date_order_by='POH_EFFECTIVEDATE, POH_SEQ_NO';
                                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                                            $date_order_by='POH_DOCDATE, POH_SEQ_NO';
                                                        }
							$cmd = " select	POH_EFFECTIVEDATE, POH_PL_NAME, POH_ORG, LEVEL_NO, MOV_CODE 
                                                                from PER_POSITIONHIS 
                                                                where PER_ID=$PER_ID and POH_ISREAL != 'N' 
                                                                order by  ".$date_order_by;
							$count_positionhis = $db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
							if($count_positionhis){
								$border = "";
								$pdf->SetFont($font,'',14);
								$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
								while($data2 = $db_dpis2->get_array()){
									$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
									if($POH_EFFECTIVEDATE){
										$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),3);
									} // end if
			
									$POH_PL_NAME = trim($data2[POH_PL_NAME]);
									$POH_ORG = trim($data2[POH_ORG]);
									$LEVEL_NO = $data2[LEVEL_NO];	
                                    $MOV_CODE = trim($data2[MOV_CODE]);
                                    
									$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
						//			$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
									$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
                                   
                                    
                                    $cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
						//			$db_dpis3->show_error();
									$data3 = $db_dpis3->get_array();
                                    $MOV_NAME = trim($data3[MOV_NAME]);
                                   //  echo "<pre>".$cmd;
                                    
									if (strpos($POH_PL_NAME,$POSITION_LEVEL)===false) 
										if(is_numeric($POSITION_LEVEL))
											$POH_PL_NAME .= " ".$POSITION_LEVEL;
										else
											$POH_PL_NAME .= $POSITION_LEVEL;
                                    
                                    $postext = "- ".$POH_EFFECTIVEDATE." ".$POH_PL_NAME." ".$POH_ORG;
                                    
                                    if($chk_mov_name=="Y"){ //ถ้าติกเเสดงประเภทการเคลื่อนไหว
                                        $postext = "- ".$POH_EFFECTIVEDATE." ".$MOV_NAME." ".$POH_PL_NAME." ".$POH_ORG;
                                    }else{
                                        $postext = "- ".$POH_EFFECTIVEDATE." ".$POH_PL_NAME." ".$POH_ORG;
                                    }

									
									$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;
	
//									$pdf->Cell(30, 7, $label,0, 0, "L");
									$pdf->MultiCell(200, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($postext):$postext), "", "L");
									if($pdf->y > $max_y) $max_y = $pdf->y;
									$pdf->x = $start_x;
//									$pdf->y = $start_y;
									
								} // end while
							} //end if 
	
			if($data_count < $count_data) $pdf->AddPage();
		} // end while
	}else{
		$pdf->SetFont($font,'b',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

//	$pdf->close();
	$pdf->Output();
?>