<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_R0040064_mfa_RTF.rtf";
	if (!$font) $font = "AngsanaUPC";

	$RTF = new RTF();
	$RTF->set_default_font($font, 16);

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
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no' or trim(e.POEMS_NO)='$search_pos_no')";
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
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("R04064"):"R04064");

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

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
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2, a.MR_CODE
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
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2, a.MR_CODE
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
											g.ORG_ID as EMPTEMP_ORG_ID, g.ORG_ID_1 as EMPTEMP_ORG_ID_1, g.ORG_ID_2 as EMPTEMP_ORG_ID_2, a.MR_CODE
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
			$MR_CODE = $data[MR_CODE];
			
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
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
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
				$PER_STARTDATE =  show_date_format(substr($PER_STARTDATE, 0, 10),3);
			} // end if

			// =====  ข้อมูลคู่สมรส =====
			if ($MR_CODE==1) $SHOW_SPOUSE = "โสด";
			elseif ($MR_CODE==2) {
				$cmd = "	select 	MAH_NAME 		from		PER_MARRHIS 
								where	PER_ID=$PER_ID 	order by	MAH_SEQ desc ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[MAH_NAME]);
			}

			if($img_file){
				$img_dim = 30;
	    		$RTF->open_line();
//				$RTF->paragraph();
				$RTF->cell($RTF->set_font($font, 22) . $RTF->add_text($RTF->bold(1) . $FULLNAME . $RTF->bold(0), "center") .	$RTF->set_font($font, 20) . $RTF->new_line() . $RTF->add_text($RTF->bold(1) . ($PM_NAME ? $PM_NAME : $PL_NAME) . $RTF->bold(0) , "center") . $RTF->new_line() . $RTF->set_font($font, 16) . $RTF->add_text($RTF->bold(1) . ($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME . $RTF->bold(0) , "center") , "80", "center", 0);
				$RTF->cellImage("$img_file", $img_dim, "20", "left", 0);
	    		$RTF->close_line();
			} else {
				$RTF->set_font($font, 20);
				$RTF->add_text($RTF->bold(1) . $FULLNAME . $RTF->bold(0) , "center");
				$RTF->new_line();
				$RTF->add_text($RTF->bold(1) . ($PM_NAME ? $PM_NAME : $PL_NAME) . $RTF->bold(0) , "center");
				$RTF->new_line();
				$RTF->set_font($font, 16);
				$RTF->add_text($RTF->bold(1) . ($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME . $RTF->bold(0) , "center");
			} // end if

			// เริ่มตาราง
    		$RTF->ln();			
			$RTF->set_table_font($font, 16);

    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."วันเดือนปีเกิด".$RTF->underline(0).$RTF->bold(0), "25", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE)), "30", "left", "0");
    		$RTF->close_line();
			
    		$RTF->ln();			
    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."เกษียณ".$RTF->underline(0).$RTF->bold(0), "25", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE)), "30", "left", "0");
    		$RTF->close_line();

    		$RTF->ln();			
    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."สถานภาพการสมรส".$RTF->underline(0).$RTF->bold(0), "25", "left", "0");
			$RTF->cell($SHOW_SPOUSE, "80", "left", "0");
    		$RTF->close_line();
			// จบตาราง

    		$RTF->ln();			
			if($DPISDB=="odbc"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
							 	from			(((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
												) left join PER_EDUCLEVEL e on (trim(a.EL_CODE)=trim(e.EL_CODE))
								 where		a.PER_ID=$PER_ID and e.EL_TYPE > '1'
								 order by		a.EDU_SEQ ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
								from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_EDUCLEVEL e
								where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and 
												a.INS_CODE=d.INS_CODE(+) and trim(a.EL_CODE)=trim(e.EL_CODE(+)) and e.EL_TYPE > '1' 
								order by		a.EDU_SEQ ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, c.EM_NAME, d.INS_NAME
							 	from			(((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
												) left join PER_EDUCLEVEL e on (trim(a.EL_CODE)=trim(e.EL_CODE))
								 where		a.PER_ID=$PER_ID and e.EL_TYPE > '1'
								 order by		a.EDU_SEQ ";			
			} // end if
			$count_educatehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_educatehis){
				while($data2 = $db_dpis2->get_array()){
						$EDU++;
						$EDU_ENDYEAR =  "ปี ".trim($data2[EDU_ENDYEAR]);
						$EN_NAME = trim($data2[EN_NAME]);
						$EM_NAME = trim($data2[EM_NAME]);
						if($EM_NAME!="") $EM_NAME = "($EM_NAME)"; 
						$INS_NAME = trim($data2[INS_NAME]);
						if(!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
						$EDU_TEXT = $EN_NAME . " " . $EM_NAME . " " . $INS_NAME;
						$RTF->open_line();			
						if ($EDU==1)
							$RTF->cell($RTF->bold(1).$RTF->underline(1)."การศึกษา".$RTF->underline(0).$RTF->bold(0), "18", "left", "0");
						else
							$RTF->cell(" ", "18", "left", "0");
						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($EDU):$EDU)).". ", "10", "right", "0");
						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($EDU_TEXT):$EDU_TEXT)), "80", "left", "0");
						$RTF->close_line();
					} // end while $data2 for EDUCATION
			} // end if($count_educatehis)

			if($DPISDB=="odbc"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME, TRN_PLACE
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
								 order by		a.TRN_STARTDATE desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME, TRN_PLACE
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
								 order by		a.TRN_STARTDATE desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			a.TRN_ENDDATE, b.TR_NAME, a.TRN_NO, a.TRN_COURSE_NAME, TRN_PLACE
								 from			PER_TRAINING a, PER_TRAIN b
								 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
								 order by		a.TRN_STARTDATE desc ";		
			} // end if
			$count_traininghis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_traininghis){
				while($data2 = $db_dpis2->get_array()){
					$EDU++;
					$TRN_ENDDATE = trim($data2[TRN_ENDDATE]);
					$arr_temp = explode("-", $TRN_ENDDATE);
					$TRN_YEAR = "พ.ศ. ".($arr_temp[0] + 543);
					$TRN_ORG = trim($data2[TRN_ORG]);
					$TR_NAME = trim($data2[TR_NAME]);				
					if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
					//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
					$TRN_NO = trim($data2[TRN_NO]);
					if($TRN_NO && $TR_NAME) $TR_NAME .= " รุ่นที่ $TRN_NO";
					$TRN_PLACE = trim($data2[TRN_PLACE]);
					$EDU_TEXT = $TR_NAME . " " . $TRN_PLACE . " " . $TRN_YEAR;
					$RTF->open_line();			
					$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($EDU):$EDU)).". ", "28", "right", "0");
					$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($EDU_TEXT):$EDU_TEXT)), "80", "left", "0");
					$RTF->close_line();
				} // end while data2 for TRAINING
			} // end if($count_traininghis)

    		$RTF->ln();			
    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ประวัติการรับราชการ".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
    		$RTF->close_line();

			$cmd = " select	POH_EFFECTIVEDATE, POH_LEVEL_NO, LEVEL_NO, PL_CODE, POH_PL_NAME, PM_CODE, PT_CODE, POH_ORG from PER_POSITIONHIS 
						  where PER_ID=$PER_ID and POH_ISREAL != 'N' order by POH_EFFECTIVEDATE, POH_SEQ_NO ";							   
			$count_positionhis = $db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			if($count_positionhis){
				$arr_buff = array();
				while($data2 = $db_dpis2->get_array()){
					if ($POH_PL_NAME != trim($data2[POH_PL_NAME]) || $POH_ORG != trim($data2[POH_ORG])) {
						$TMP_LEVEL_NO = trim($data2[POH_LEVEL_NO]);
						if (!$TMP_LEVEL_NO) $TMP_LEVEL_NO = trim($data2[LEVEL_NO]);
						$TMP_PL_CODE = trim($data2[PL_CODE]);
						$TMP_POH_PL_NAME = trim($data2[POH_PL_NAME]);
						$TMP_PM_CODE = trim($data2[PM_CODE]);
						$TMP_PT_CODE = trim($data2[PT_CODE]);

						$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$TMP_LEVEL_NAME = $data3[LEVEL_NAME];
						$TMP_POSITION_LEVEL = $data3[POSITION_LEVEL];
						if (!$TMP_POSITION_LEVEL) $TMP_POSITION_LEVEL = $TMP_LEVEL_NAME;

						$TMP_PL_NAME = "";
						$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";			
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$TMP_PL_NAME = $data3[PL_NAME];
						if (!$TMP_PL_NAME) $TMP_PL_NAME = $TMP_POH_PL_NAME;

						$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$TMP_PM_NAME = trim($data3[PM_NAME]);

						$cmd = " 	select PT_NAME from PER_TYPE	where PT_CODE='$TMP_PT_CODE'  ";
						$db_dpis3->send_cmd($cmd);
						$data3 = $db_dpis3->get_array();
						$TMP_PT_NAME = trim($data3[PT_NAME]);

						$POH_EFFECTIVEDATE = substr(trim($data2[POH_EFFECTIVEDATE]),0,4)+543;
						$POH_PL_NAME = trim($data2[POH_PL_NAME]);
						$POH_ORG = trim($data2[POH_ORG]);
					    $TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". $TMP_POSITION_LEVEL . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
						$RTF->open_line();			
						$RTF->cell("พ.ศ. ".trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($POH_EFFECTIVEDATE):$POH_EFFECTIVEDATE)), "12", "left", "0");
						$RTF->cell("ดำรงตำแหน่ง", "14", "left", "0");
						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($TMP_PL_NAME." ".$POH_ORG):$TMP_PL_NAME." ".$POH_ORG)), "80", "left", "0");
						$RTF->close_line();
					}
				} // end while
			} else {
				$RTF->new_line();
				$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
			} //end if 

    		$RTF->ln();			
    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."เครื่องราชอิสริยาภรณ์".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
    		$RTF->close_line();

			$where = "";
			if ($MFA_FLAG==1) $where = " and a.DC_CODE!='01' ";
			$cmd = " select			b.DC_NAME, a.DEH_DATE
								 from			PER_DECORATEHIS a, PER_DECORATION b
								 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE $where
								 order by	a.DEH_DATE ";	
			$count_decoratehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			while($data2 = $db_dpis2->get_array()){
				$DEH++;
				$DC_NAME = trim($data2[DC_NAME]);
				$DEH_DATE = show_date_format($data2[DEH_DATE],3);
				$RTF->open_line();			
				$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH):$DEH)).". ", "15", "right", "0");
				$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DC_NAME):$DC_NAME)), "40", "left", "0");
				$RTF->cell(" เมื่อ ".trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_DATE):$DEH_DATE)), "30", "left", "0");
				$RTF->close_line();
			} // end while

			if ($MFA_FLAG==1) {
				$cmd = " select			DEH_DATE, DEH_GAZETTE, DEH_REMARK
									 from			PER_DECORATEHIS 
									 where		PER_ID=$PER_ID and DC_CODE='01' 
									 order by	DEH_DATE ";	
				$count_decoratehis = $db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
				if ($count_decoratehis) {
					$RTF->ln();			
					$RTF->open_line();			
					$RTF->cell($RTF->bold(1).$RTF->underline(1)."เครื่องราชอิสริยาภรณ์ต่างประเทศ".$RTF->underline(0).$RTF->bold(0), "50", "left", "0");
					$RTF->close_line();
					while($data2 = $db_dpis2->get_array()){
						$DEH_MFA++;
						$DEH_DATE = show_date_format($data2[DEH_DATE],3);
						$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
						$DEH_REMARK = trim($data2[DEH_REMARK]);
						$RTF->open_line();			
						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_MFA):$DEH_MFA)).". ", "15", "right", "0");
						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_REMARK):$DEH_REMARK)), "100", "left", "0");
						$RTF->close_line();
						$RTF->open_line();			
						$RTF->cell(" ", "15", "right", "0");
						$RTF->cell(" ", "40", "right", "0");
//						$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_REMARK):$DEH_REMARK)), "40", "left", "0");
						$RTF->cell(" เมื่อ ".trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_DATE):$DEH_DATE)), "30", "left", "0");
						$RTF->close_line();
					} // end while
				}
			}
		} // end while
	}else{
		$RTF->new_line();
		$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 60);	
?>