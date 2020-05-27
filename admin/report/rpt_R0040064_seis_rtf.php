<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_R0040064_seis_RTF.rtf";
//	echo "font=$font<br>";
	if (!$font) $font = "AngsanaUPC";

	$RTF = new RTF();
	$RTF->set_default_font($font, 16);

	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.DEPARTMENT_ID,
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
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.DEPARTMENT_ID, 
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
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_OCCUPYDATE, a.DEPARTMENT_ID, 
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
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
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
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
			$ORG_ID_REF = trim($data2[ORG_ID_REF]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = trim($data2[ORG_NAME]);
			
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
			$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PM_NAME = trim($data2[PM_NAME]);

			$PV_CODE = trim($data[PV_CODE]);
			$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE = '$PV_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PV_NAME = trim($data2[PV_NAME]);

			$LEVEL_NO = trim($data[LEVEL_NO]);
			$POSITION_TYPE =  $data[POSITION_TYPE];
			$LEVEL_NAME = $data[POSITION_LEVEL];
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = card_no_format(trim($data[PER_CARDNO]),3);

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
			$START_YEAR = substr($today,0,4) + 543 - 15 - 2500;
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			$AGE_DIFF = date_difference($today, $PER_BIRTHDATE, "full");
//			$PER_RETIREDATE = date_adjust($PER_BIRTHDATE,'d',-1);
//			$PER_RETIREDATE = date_adjust($PER_RETIREDATE,'y',60);
			$PER_RETIREDATE = trim($data[PER_RETIREDATE]);
			if($PER_BIRTHDATE){
				$PER_BIRTHDATE = show_date_format(substr($PER_BIRTHDATE, 0, 10),2);
			} // end if
			
			if($PER_RETIREDATE){
				$PER_RETIREDATE = substr($PER_RETIREDATE, 0, 4)+543;
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$STARTDATE_DIFF = date_difference($today, $PER_STARTDATE, "full");
			if($PER_STARTDATE){
				$PER_STARTDATE =  show_date_format(substr($PER_STARTDATE, 0, 10),2);
			} // end if

			$cmd = " select POH_EFFECTIVEDATE
							from   PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and LEVEL_NO='$LEVEL_NO' and 
										(MOV_SUB_TYPE=1 or MOV_SUB_TYPE=2 or MOV_SUB_TYPE=3 or MOV_SUB_TYPE=6 or MOV_SUB_TYPE=10 or MOV_SUB_TYPE=11)
							order by POH_EFFECTIVEDATE ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
			if ($POH_EFFECTIVEDATE) {	
				$POS_UP_DIFF = date_difference($today, $POH_EFFECTIVEDATE, "full");
				$POS_UP_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),2);
			}

			$cmd = " select POH_EFFECTIVEDATE, PL_CODE, POH_ORG2
							from   PER_POSITIONHIS
							where PER_ID=$PER_ID
							order by POH_EFFECTIVEDATE desc, POH_SEQ_NO DESC ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$POH_PL_CODE = trim($data2[PL_CODE]);
				$POH_ORG2 = trim($data2[POH_ORG2]);
				if ($POH_PL_CODE==$PL_CODE && $POH_ORG2==$DEPARTMENT_NAME) 
					$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
				else break;
			}
			if ($POH_EFFECTIVEDATE) {	
				$LINE_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),2);
			}

			// เริ่มตาราง
//			$RTF->cell("(".(($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_CARDNO):$PER_CARDNO).")", "30", "left", "0");
			$POSITION_NAME = pl_name_format($PL_NAME, $PM_NAME, $PT_NAME, $LEVEL_NO, 2, $ORG_NAME, $ORG_NAME1);	
			$RTF->set_table_font($font, 22);

			$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."รายงานประวัติการศึกษาและการดำรงตำแหน่ง".$RTF->underline(0).$RTF->bold(0), "100", "center", "0");
			$RTF->close_line();

    		$RTF->ln();			
//			$RTF->set_table_font($font, 16);
			$RTF->set_table_font($font, 14);

    		$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1).$MINISTRY_NAME.$RTF->underline(0).$RTF->bold(0), "80", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("", "2", "left", "0");
			$RTF->cell($RTF->bold(1).$DEPARTMENT_NAME.$RTF->bold(0), "80", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("", "4", "left", "0");
			$RTF->cell($RTF->bold(1).$FULLNAME.$RTF->bold(0), "30", "left", "0");
			$RTF->cell($RTF->bold(1)."ตำแหน่ง".$RTF->bold(0), "10", "left", "0");
			$RTF->cell($POSITION_NAME, "30", "left", "0");
			$RTF->cell($RTF->bold(1)."วันเลื่อนระดับ".$RTF->bold(0), "14", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($LINE_DATE):$LINE_DATE)), "25", "left", "0");
    		$RTF->close_line();
			
    		$RTF->open_line();			
			$RTF->cell("", "6", "left", "0");
			$RTF->cell($RTF->bold(1)."วันดำรงตำแหน่งปัจจุบัน".$RTF->bold(0), "22", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($POS_UP_DATE):$POS_UP_DATE)), "15", "left", "0");
			$RTF->cell($RTF->bold(1)."เงินเดือน".$RTF->bold(0), "10", "left", "0");
			$RTF->cell(number_format($PER_SALARY)." บาท", "20", "left", "0");
			$RTF->cell($RTF->bold(1)."ปีเกษียณอายุ".$RTF->bold(0), "15", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE)), "20", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("", "6", "left", "0");
			$RTF->cell($RTF->bold(1)."วันเดือนปีเกิด".$RTF->bold(0), "22", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_BIRTHDATE):$PER_BIRTHDATE)), "15", "left", "0");
			$RTF->cell($RTF->bold(1)."วันบรรจุ".$RTF->bold(0), "10", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_STARTDATE):$PER_STARTDATE)), "20", "left", "0");
			$RTF->cell($RTF->bold(1)."ประเภทตำแหน่ง".$RTF->bold(0), "15", "left", "0");
			$RTF->cell($POSITION_TYPE, "20", "left", "0");
    		$RTF->close_line();
			
    		$RTF->open_line();			
			$RTF->cell("", "4", "left", "0");
			$RTF->cell($RTF->bold(1)."ตำแหน่งทางการบริหาร".$RTF->bold(0), "22", "left", "0");
			$RTF->cell($PM_NAME, "28", "left", "0");
			$RTF->cell($RTF->bold(1)."ตำแหน่งในสายงาน".$RTF->bold(0), "20", "left", "0");
			$RTF->cell($PL_NAME, "30", "left", "0");
    		$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ประวัติการศึกษา".$RTF->underline(0).$RTF->bold(0), "100", "center", "0");
			$RTF->close_line();

			$RTF->set_table_font($font, 14);
			$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ระดับวุฒิการศึกษา".$RTF->underline(0).$RTF->bold(0), "15", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ชื่อวุฒิการศึกษา".$RTF->underline(0).$RTF->bold(0), "20", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."สาขาวิชาเอก".$RTF->underline(0).$RTF->bold(0), "25", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."สถาบัน".$RTF->underline(0).$RTF->bold(0), "25", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ประเทศ".$RTF->underline(0).$RTF->bold(0), "15", "left", "0");
			$RTF->close_line();

			if($DPISDB=="odbc"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EL_CODE, CT_CODE_EDU
							 	from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								 where		a.PER_ID=$PER_ID
								 order by		a.EDU_SEQ ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EL_CODE, CT_CODE_EDU
								from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and 
												a.INS_CODE=d.INS_CODE(+) 
								order by		a.EDU_SEQ ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME, a.EL_CODE, CT_CODE_EDU
							 	from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								 where		a.PER_ID=$PER_ID
								 order by		a.EDU_SEQ ";			
			} // end if
			$count_educatehis = $db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			if($count_educatehis){
				while($data2 = $db_dpis2->get_array()){
					$EN_NAME = trim($data2[EN_NAME]);
					$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
					if ($EN_SHORTNAME) $EN_NAME = $EN_SHORTNAME;
					$EM_NAME = trim($data2[EM_NAME]);
					$INS_NAME = trim($data2[INS_NAME]);
					if(!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);

					$EL_CODE = trim($data2[EL_CODE]);
					$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE = '$EL_CODE' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$EL_NAME = str_replace("หรือเทียบเท่า", "", trim($data3[EL_NAME]));

					$CT_CODE_EDU = trim($data2[CT_CODE_EDU]);
					$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE = '$CT_CODE_EDU' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$CT_NAME = trim($data3[CT_NAME]);


					$RTF->open_line();			
					$RTF->cell($EL_NAME, "15", "left", "0");
					$RTF->cell($EN_NAME, "20", "left", "0");
					$RTF->cell($EM_NAME, "25", "left", "0");
					$RTF->cell($INS_NAME, "25", "left", "0");
					$RTF->cell($CT_NAME, "15", "left", "0");
					$RTF->close_line();
				} // end while $data2 for EDUCATION
			}else{
				$RTF->new_line();
				$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
			} // end if

			$RTF->set_table_font($font, 16);
			$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ประวัติการดำรงตำแหน่งสำคัญ ฯ".$RTF->underline(0).$RTF->bold(0), "100", "center", "0");
			$RTF->close_line();

			$RTF->set_table_font($font, 14);
			$RTF->open_line();			
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."วัน/เดือน/ปี".$RTF->underline(0).$RTF->bold(0), "13", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ตำแหน่ง".$RTF->underline(0).$RTF->bold(0), "40", "left", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."ระดับ".$RTF->underline(0).$RTF->bold(0), "9", "right", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."กอง/สำนัก".$RTF->underline(0).$RTF->bold(0), "18", "right", "0");
			$RTF->cell($RTF->bold(1).$RTF->underline(1)."กรม".$RTF->underline(0).$RTF->bold(0), "20", "right", "0");
			$RTF->close_line();

			$cmd = " select	POH_EFFECTIVEDATE, POH_PL_NAME, POH_ORG, POH_POS_NO_NAME, POH_POS_NO, LEVEL_NO, 
											POH_SALARY, a.MOV_CODE, POH_DOCNO, POH_DOCDATE, PL_CODE, PM_CODE, PT_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1
							from PER_POSITIONHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and nvl(MOV_SUB_TYPE,0) not in (5,7) and nvl(POH_ISREAL,'Y') != 'N'  and LEVEL_NO > '07'
							order by POH_EFFECTIVEDATE, POH_SEQ_NO ";							   
			$count_positionhis = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			if($count_positionhis){
				$arr_buff = array();
				while($data2 = $db_dpis1->get_array()){
					$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
					if($POH_EFFECTIVEDATE){
						$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),2);
					} // end if
			
					$POH_PL_NAME = trim($data2[POH_PL_NAME]);
					$POH_ORG = trim($data2[POH_ORG]);
					$POH_ORG1 = trim($data2[POH_ORG1]);
					$POH_ORG2 = trim($data2[POH_ORG2]);
//					$cmd = " select ORG_SHORT from PER_ORG where ORG_NAME = '$POH_ORG2' and DEPARTMENT_ID =  ";
					$cmd = " select ORG_SHORT from PER_ORG where ORG_NAME = '$POH_ORG2' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					if (trim($data3[ORG_SHORT])) $POH_ORG2 = trim($data3[ORG_SHORT]);

					$POH_ORG3 = trim($data2[POH_ORG3]);
					$POH_UNDER_ORG1 = trim($data2[POH_UNDER_ORG1]);
					$POH_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
					$POSITION_LEVEL = level_no_format($LEVEL_NO,2);
//					if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") 
//						$POH_PL_NAME .= " ".level_no_format($LEVEL_NO);
//					else
//						$POH_PL_NAME .= $POSITION_LEVEL;

					$PL_CODE = trim($data2[PL_CODE]);
					$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$PL_CODE' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$PL_NAME = trim($data3[PL_NAME]);

					$PM_CODE = trim($data2[PM_CODE]);
					$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$PM_CODE' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$PM_NAME = trim($data3[PM_NAME]);

					$PT_CODE = trim($data2[PT_CODE]);
					$cmd = " select PT_NAME from PER_TYPE where PT_CODE = '$PT_CODE' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$PT_NAME = trim($data3[PT_NAME]);
//					if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9" && $PT_NAME!="ทั่วไป") $POH_PL_NAME .= $PT_NAME;
					$POH_PL_NAME = pl_name_format($PL_NAME, $PM_NAME, $PT_NAME, $LEVEL_NO, 3, $POH_ORG3, $POH_UNDER_ORG1);	
//					if ($PT_NAME && $PT_NAME!="ทั่วไป" && $POSITION_LEVEL >= "1" && $POSITION_LEVEL <="9") $POSITION_LEVEL .= $PT_NAME;

					$POH_SALARY = trim($data2[POH_SALARY]);
					$MOV_CODE = trim($data2[MOV_CODE]);
					$cmd = " select MOV_SUB_TYPE, MOV_NAME from PER_MOVMENT where MOV_CODE = '$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$MOV_SUB_TYPE = trim($data3[MOV_SUB_TYPE]);
					$TMP_MOV_NAME = trim($data3[MOV_NAME]);
					if ($MOV_SUB_TYPE=="1") $MOV_NAME = "บรรจุ";
					elseif ($MOV_SUB_TYPE=="10") $MOV_NAME = "รับโอน";
					elseif ($MOV_SUB_TYPE=="11") $MOV_NAME = "บรรจุกลับ";
					elseif ($MOV_SUB_TYPE=="2") $MOV_NAME = "ย้ายไป";
					elseif ($MOV_SUB_TYPE=="3") $MOV_NAME = "เลื่อน";
					elseif ($MOV_SUB_TYPE=="5") $MOV_NAME = "ช่วยราชการ";
					elseif ($MOV_SUB_TYPE=="6") $MOV_NAME = "จัดลง";
					elseif ($MOV_SUB_TYPE=="7") $MOV_NAME = "รักษาการในตำแหน่ง";
					elseif ($MOV_SUB_TYPE=="") $MOV_NAME = "ปฏิบัติ";
					elseif ($MOV_SUB_TYPE=="") $MOV_NAME = "ปฏิบัติหน้าที่ราชการ";

					if (strpos($TMP_MOV_NAME,"ไม่พ้นทดลอง") !== false) $MOV_NAME = "ไม่พ้นทดลอง";
					elseif (strpos($TMP_MOV_NAME,"พ้นทดลอง") !== false) $MOV_NAME = "พ้นทดลอง";

					$POH_DOCNO = trim($data2[POH_DOCNO]);
					$POH_DOCDATE = "";
					if(trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-"){
						if(trim($data2[POH_DOCDATE])){
							$POH_DOCDATE = "ลว. ".show_date_format(substr($data2[POH_DOCDATE], 0, 10),4);
						}
						$POH_DOCNO = $data2[POH_DOCNO]." ".$POH_DOCDATE;
						if (trim($data2[POH_DOCNO]) && trim($data2[POH_DOCNO]) != "-" && strpos($data2[POH_DOCNO],"คำสั่ง") === false && 
							strpos($data2[POH_DOCNO],"บันทึก") === false && strpos($data2[POH_DOCNO],"หนังสือ") === false && $MOV_CODE != "12" && $MOV_CODE != "21" && $MOV_CODE != "027")
							$POH_DOCNO = "คำสั่ง ".$POH_DOCNO;
					}

					$RTF->open_line();			
					$RTF->cell($POH_EFFECTIVEDATE, "13", "left", "0");
//					$RTF->cell($MOV_NAME.' '.$POH_PL_NAME, "40", "left", "0");
					$RTF->cell($POH_PL_NAME, "40", "left", "0");
					$RTF->cell($POSITION_LEVEL, "9", "right", "0");
					$RTF->cell($POH_ORG3, "18", "right", "0");
					$RTF->cell($POH_ORG2, "20", "right", "0");
					$RTF->close_line();
				} // end while
			} else {
				$RTF->new_line();
				$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
			} //end if 
		} // end while
	}else{
		$RTF->new_line();
		$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 60);	
?>