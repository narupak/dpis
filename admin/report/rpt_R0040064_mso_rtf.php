<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);

	$fname= "rpt_R0040064_mso_RTF.rtf";
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
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
			
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
				$PER_RETIREDATE = show_date_format(substr($PER_RETIREDATE, 0, 10),2);
			} // end if
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_OCCUPYDATE = trim($data[PER_OCCUPYDATE]);
			$OCCUPY_FLAG = 0;
			if ($PER_STARTDATE!=$PER_OCCUPYDATE) $OCCUPY_FLAG = 1;
			$STARTDATE_DIFF = date_difference($today, $PER_STARTDATE, "full");
			if($PER_STARTDATE){
				$PER_STARTDATE =  show_date_format(substr($PER_STARTDATE, 0, 10),2);
			} // end if

			$OCCUPYDATE_DIFF = date_difference($today, $PER_OCCUPYDATE, "full");
			if($PER_OCCUPYDATE){
				$PER_OCCUPYDATE =  show_date_format(substr($PER_OCCUPYDATE, 0, 10),2);
			} // end if

			if ($OCCUPY_FLAG) {
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS a, PER_MOVMENT b
								where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE=90
								order by POH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
				if ($POH_EFFECTIVEDATE) {	
					$POS_DATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),2);
				}
			}

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

			$cmd = " select SAH_SALARY
							from   PER_SALARYHIS
							where PER_ID=$PER_ID and SAH_SALARY < $PER_SALARY
							order by SAH_EFFECTIVEDATE desc, SAH_SALARY desc ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SAH_SALARY = trim($data2[SAH_SALARY]);

			$cmd = " select SAH_EFFECTIVEDATE, MOV_SUB_TYPE, SM_CODE, SAH_PERCENT_UP
							from   PER_SALARYHIS a, PER_MOVMENT b
							where PER_ID=$PER_ID and a.MOV_CODE=b.MOV_CODE and MOV_SUB_TYPE not in (0, 8, 81)
							order by SAH_EFFECTIVEDATE, SAH_SALARY ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()){
				$SAH_YEAR = substr($data2[SAH_EFFECTIVEDATE]+544, 2, 2);
				if ($SAH_YEAR > $START_YEAR) {
					$MOV_SUB_TYPE = trim($data2[MOV_SUB_TYPE]);
					$SM_CODE = trim($data2[SM_CODE]);
					$SAH_PERCENT_UP = trim($data2[SAH_PERCENT_UP]);
					if ($MOV_SUB_TYPE == '45' || $SM_CODE == '1') 
						$SAH_UP[$SAH_YEAR] += 0.5;
					elseif ($MOV_SUB_TYPE == '46' || $SM_CODE == '2') 
						$SAH_UP[$SAH_YEAR] += 1;
					elseif ($MOV_SUB_TYPE == '47' || $SM_CODE == '3') 
						$SAH_UP[$SAH_YEAR] += 1.5;
					elseif ($MOV_SUB_TYPE == '48' || $SM_CODE == '4') 
						$SAH_UP[$SAH_YEAR] += 2;
					elseif ($SAH_PERCENT_UP) 
						if ($SAH_UP[$SAH_YEAR])
							$SAH_UP[$SAH_YEAR] .= "+".$SAH_PERCENT_UP;
						else
							$SAH_UP[$SAH_YEAR] = $SAH_PERCENT_UP;
				}
			} // end while

			// === หาวันที่มีผล
			$level = array(	"02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "O1", "O2", "O3", "O4", "K1", "K2", "K3", "K4", "K5", "D1", "D2", "M1", "M2" );
			for ( $i=0; $i<count($level); $i++ ) { 
				$LEVEL_DATE[$level[$i]] = "";
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID and LEVEL_NO='$level[$i]'
								order by POH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if (trim($data2[POH_EFFECTIVEDATE])) {	
					$LEVEL_DATE[$level[$i]] = $data2[POH_EFFECTIVEDATE];
				}
			} // end for

			// === แต่งตั้งปฏิบัติ ช่วยราชการ
			$cmd = " select SV_CODE, SRH_ORG
							from   PER_SERVICEHIS 
							where PER_ID=$PER_ID and (SRH_ENDDATE is NULL or SRH_ENDDATE >=  '$today')
							order by SRH_STARTDATE DESC ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if (trim($data2[SRH_ORG])) {	
				$SV_CODE = trim($data2[SV_CODE]);
				if ($SV_CODE=="10" || $SV_CODE=="24" || $SV_CODE=="25" || $SV_CODE=="26" || $SV_CODE=="29") $SV_NAME = "ช่วยราชการ";
				elseif ($SV_CODE=="11" || $SV_CODE=="14" || $SV_CODE=="16" || $SV_CODE=="17" || $SV_CODE=="19" || $SV_CODE=="20" || $SV_CODE=="22" || $SV_CODE=="37" || $SV_CODE=="39" || $SV_CODE=="43" || $SV_CODE=="46" || $SV_CODE=="48" || $SV_CODE=="49") $SV_NAME = "ปฏิบัติ";
				elseif ($SV_CODE=="45") $SV_NAME = "แต่งตั้ง";
				$SRH_ORG = trim($data2[SRH_ORG]);
			}

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
/*
			if($img_file){
				$img_dim = 30;
	    		$RTF->open_line();
//				$RTF->paragraph();
				$RTF->cellImage("$img_file", $img_dim, "20", "left", 0);
				$RTF->cell($RTF->set_font($font, 22) . $RTF->add_text($RTF->bold(1) . $FULLNAME . $RTF->bold(0), "center") .	$RTF->set_font($font, 20) . $RTF->new_line() . $RTF->add_text($RTF->bold(1) . ($PM_NAME ? $PM_NAME : $PL_NAME) . $RTF->bold(0) , "center") . $RTF->new_line() . $RTF->set_font($font, 16) . $RTF->add_text($RTF->bold(1) . ($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:"")." ".$DEPARTMENT_NAME . $RTF->bold(0) , "center") , "80", "center", 0);
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
*/
			// เริ่มตาราง
    		$RTF->ln();			
			$RTF->set_table_font($font, 16);

    		$RTF->open_line();			
			$RTF->cell("ชื่อ", "10", "left", "0");
			$RTF->cell($FULLNAME, "40", "left", "0");
			$RTF->cell("(".(($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_CARDNO):$PER_CARDNO).")", "30", "left", "0");
    		$RTF->close_line();
			
    		$RTF->open_line();			
			$RTF->cell("อายุ", "10", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($AGE_DIFF." (".$PER_BIRTHDATE.")"):$AGE_DIFF." (".$PER_BIRTHDATE.")")), "40", "left", "0");
			$RTF->cell("เกษียณ", "10", "left", "0");
			$RTF->cell(trim((($NUMBER_DISPLAY==2) ? convert2thaidigit($PER_RETIREDATE):$PER_RETIREDATE)), "40", "left", "0");
    		$RTF->close_line();
			
			if($DPISDB=="odbc"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
							 	from			((PER_EDUCATE a
												left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
												) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
												) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
								 where		a.PER_ID=$PER_ID
								 order by		a.EDU_SEQ desc ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
								from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d
								where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and 
												a.INS_CODE=d.INS_CODE(+) 
								order by		a.EDU_SEQ desc ";							   
			}elseif($DPISDB=="mysql"){
				$cmd = " 	select		a.EDU_ENDYEAR, a.EDU_INSTITUTE, b.EN_NAME, b.EN_SHORTNAME, c.EM_NAME, d.INS_NAME
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
						$EN_SHORTNAME = trim($data2[EN_SHORTNAME]);
						if ($EN_SHORTNAME) $EN_NAME = $EN_SHORTNAME;
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
				$label = "วุฒิการศึกษา";
				$arr_buff = array();
				for($row_count=0; $row_count < count($arr_content); $row_count++) {
					$edutext = ($arr_content[$row_count][en_name]?$arr_content[$row_count][en_name]." ":"").($arr_content[$row_count][em_name]?$arr_content[$row_count][em_name]." ":"").($arr_content[$row_count][ins_name]?$arr_content[$row_count][ins_name].", ":"").($arr_content[$row_count][edu_period]?$arr_content[$row_count][edu_period]:"");
					$arr_buff[] = trim(($NUMBER_DISPLAY==2) ? convert2thaidigit($edutext):$edutext);
				} // end for loop $row_count
				$RTF->add_text($label, "left");
				$RTF->new_line();
				$RTF->add_list($arr_buff, "left", $font, 16);
			}else{
				$RTF->new_line();
				$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
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
				$label = "อบรม";
				$arr_buff = array();
				for($row_count=0; $row_count < count($arr_content); $row_count++) {
					$trntext = ($arr_content[$row_count][tr_name]?$arr_content[$row_count][tr_name]." ":"").
								    ($arr_content[$row_count][trn_year]?$arr_content[$row_count][trn_year]:"");
					$arr_buff[] = trim(($NUMBER_DISPLAY==2) ? convert2thaidigit($trntext):$trntext);
				} // end for loop $row_count
				$RTF->add_text($label, "left");
				$RTF->new_line();
				$RTF->add_list($arr_buff, "left", $font, 16);
			}else{
				$RTF->new_line();
				$RTF->add_text("********** ไม่มีข้อมูล **********", "center");
			} // end if

    		$RTF->open_line();			
			$RTF->cell("ปัจจุบันตำแหน่ง", "20", "left", "0");
			$RTF->cell(($PM_NAME ? $PM_NAME : $PL_NAME.$LEVEL_NAME), "30", "left", "0");
			$RTF->cell("เลขที่ ".$POS_NO, "20", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("", "20", "left", "0");
			$RTF->cell(($ORG_NAME_2?$ORG_NAME_2." ":"").($ORG_NAME_1?$ORG_NAME_1." ":"").($ORG_NAME?$ORG_NAME:""), "80", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("", "20", "left", "0");
			$RTF->cell($DEPARTMENT_NAME, "80", "left", "0");
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("ดำรงตำแหน่ง", "20", "left", "0");
			$RTF->cell($POS_UP_DATE." (".$POS_UP_DIFF.")", "50", "left", "0");
    		$RTF->close_line();

			if($SRH_ORG){
				$RTF->open_line();			
				$RTF->cell($SV_NAME, "20", "left", "0");
				$RTF->cell($SRH_ORG, "80", "left", "0");
				$RTF->close_line();
			}

    		$RTF->open_line();			
			$RTF->cell("เงินเดือน", "20", "left", "0");
			$RTF->cell(number_format($PER_SALARY)." บาท", "30", "left", "0");
			$RTF->cell("ปีที่แล้ว", "10", "left", "0");
			$RTF->cell(number_format($SAH_SALARY)." บาท", "40", "left", "0");
    		$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("เริ่มรับราชการ", "20", "left", "0");
			$RTF->cell($PER_STARTDATE." (".$STARTDATE_DIFF.")", "80", "left", "0");
    		$RTF->close_line();

			if($OCCUPY_FLAG){
				if ($POS_DATE) {
					$RTF->open_line();			
					$RTF->cell("ลาออก", "20", "left", "0");
					$RTF->cell($POS_DATE, "80", "left", "0");
					$RTF->close_line();
				}

				$RTF->open_line();			
				if ($POS_DATE) 
					$RTF->cell("บรรจุกลับ", "20", "left", "0");
				else
					$RTF->cell("รับโอน", "20", "left", "0");
				$RTF->cell($PER_OCCUPYDATE." (".$OCCUPYDATE_DIFF.")", "80", "left", "0");
				$RTF->close_line();
			}

			if ($LEVEL_NO=="K1") {
				if ($LEVEL_DATE["K1"]) {
					$LEVEL_NO1 = "ปก.";
					$LEVEL_DATE1 = $LEVEL_DATE["K1"];
					if ($LEVEL_DATE["04"]) {
						$LEVEL_NO1 = "4";
						$LEVEL_DATE1 = $LEVEL_DATE["04"];
					} elseif ($LEVEL_DATE["03"]) {
						$LEVEL_NO2 = "3";
						$LEVEL_DATE2 = $LEVEL_DATE["03"];
					}
				} elseif ($LEVEL_DATE["04"]) {
					$LEVEL_NO1 = "4";
					$LEVEL_DATE1 = $LEVEL_DATE["04"];
					$LEVEL_NO2 = "3";
					$LEVEL_DATE2 = $LEVEL_DATE["03"];
				}
			} elseif ($LEVEL_NO=="K2") {
				if ($LEVEL_DATE["K2"]) {
					$LEVEL_NO1 = "ชก.";
					$LEVEL_DATE1 = $LEVEL_DATE["K2"];
					if ($LEVEL_DATE["06"]) {
						$LEVEL_NO1 = "6";
						$LEVEL_DATE1 = $LEVEL_DATE["06"];
					} elseif ($LEVEL_DATE["05"]) {
						$LEVEL_NO2 = "5";
						$LEVEL_DATE2 = $LEVEL_DATE["05"];
					} elseif ($LEVEL_DATE["04"]) {
						$LEVEL_NO2 = "4";
						$LEVEL_DATE2 = $LEVEL_DATE["04"];
					}
				} elseif ($LEVEL_DATE["07"]) {
					$LEVEL_NO1 = "6";
					$LEVEL_DATE1 = $LEVEL_DATE["06"];
					if ($LEVEL_DATE["05"]) {
						$LEVEL_NO2 = "5";
						$LEVEL_DATE2 = $LEVEL_DATE["05"];
					} elseif ($LEVEL_DATE["04"]) {
						$LEVEL_NO2 = "4";
						$LEVEL_DATE2 = $LEVEL_DATE["04"];
					}
				}
			} elseif ($LEVEL_NO=="K3") {
				if ($LEVEL_DATE["K3"]) {
					$LEVEL_NO1 = "ชพ.";
					$LEVEL_DATE1 = $LEVEL_DATE["K3"];
					if ($LEVEL_DATE["08"]) {
						$LEVEL_NO1 = "8";
						$LEVEL_DATE1 = $LEVEL_DATE["08"];
					} elseif ($LEVEL_DATE["07"]) {
						$LEVEL_NO2 = "7";
						$LEVEL_DATE2 = $LEVEL_DATE["07"];
					}
				} elseif ($LEVEL_DATE["08"]) {
					$LEVEL_NO1 = "8";
					$LEVEL_DATE1 = $LEVEL_DATE["08"];
					$LEVEL_NO2 = "7";
					$LEVEL_DATE2 = $LEVEL_DATE["07"];
				}
			} elseif ($LEVEL_NO=="K4") {
				$LEVEL_NO1 = "9";
				$LEVEL_DATE1 = $LEVEL_DATE["09"];
				$LEVEL_NO2 = "8";
				$LEVEL_DATE2 = $LEVEL_DATE["08"];
			} elseif ($LEVEL_NO=="D1") {
				$LEVEL_NO1 = "8";
				$LEVEL_DATE1 = $LEVEL_DATE["08"];
				$LEVEL_NO2 = "7";
				$LEVEL_DATE2 = $LEVEL_DATE["07"];
			} elseif ($LEVEL_NO=="D2") {
				$LEVEL_NO1 = "9";
				$LEVEL_DATE1 = $LEVEL_DATE["09"];
				$LEVEL_NO2 = "8";
				$LEVEL_DATE2 = $LEVEL_DATE["08"];
			} elseif ($LEVEL_NO=="M1") {
				$LEVEL_NO1 = "9";
				$LEVEL_DATE1 = $LEVEL_DATE["09"];
				$LEVEL_NO2 = "8";
				$LEVEL_DATE2 = $LEVEL_DATE["08"];
			} elseif ($LEVEL_NO=="M2") {
				$LEVEL_NO1 = "10";
				$LEVEL_DATE1 = $LEVEL_DATE["10"];
				$LEVEL_NO2 = "9";
				$LEVEL_DATE2 = $LEVEL_DATE["09"];
			}
			$LEVEL1_DIFF = date_difference($today, $LEVEL_DATE1, "full");
			$LEVEL_DATE1 = show_date_format(substr($LEVEL_DATE1, 0, 10),2);
			$LEVEL2_DIFF = date_difference($today, $LEVEL_DATE2, "full");
			$LEVEL_DATE2 = show_date_format(substr($LEVEL_DATE2, 0, 10),2);

    		$RTF->open_line();			
			$RTF->cell("เลื่อนระดับ ".$LEVEL_NO1, "20", "left", "0");
			$RTF->cell($LEVEL_DATE1." (".$LEVEL1_DIFF.")", "80", "left", "0");
    		$RTF->close_line();

			if ($LEVEL_NO2) {
				$RTF->open_line();			
				$RTF->cell("เลื่อนระดับ ".$LEVEL_NO2, "20", "left", "0");
				$RTF->cell($LEVEL_DATE2." (".$LEVEL2_DIFF.")", "80", "left", "0");
				$RTF->close_line();
			}

    		$RTF->open_line();			
			$RTF->cell("เริ่มดำรงตำแหน่งในสายงาน", "25", "left", "0");
			$RTF->cell($LINE_DATE, "30", "left", "0");
    		$RTF->close_line();

			if ($DEH_SHOW) {
				$DEH_SHOW = trim(($NUMBER_DISPLAY==2) ? convert2thaidigit($DEH_SHOW):$DEH_SHOW);
				$arr_DEH = explode("\n", $DEH_SHOW);
				$label = "เครื่องราชอิสริยาภรณ์:";
				$RTF->add_text($label, "left");
				$RTF->new_line();
	//			$RTF->set_font($font, 16);
				$RTF->add_list($arr_DEH, "left", $font, 16);
			}
/*
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
//			$db_dpis2->show_error();
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
				$arr_buff = array();
				for($row_count=0; $row_count < count($arr_content); $row_count++) {
					$rewtext = ($arr_content[$row_count][rew_name]?$arr_content[$row_count][rew_name]."  : ":"").
									  ($arr_content[$row_count][reh_year]?$arr_content[$row_count][reh_year]."  : ":"").
									  ($arr_content[$row_count][reh_org]?$arr_content[$row_count][reh_org]:"");
					$arr_buff[] = trim(($NUMBER_DISPLAY==2) ? convert2thaidigit($rewtext):$rewtext);
				} // end for loop $row_count
				$RTF->new_line();
				$RTF->add_text($label, "left");
				$RTF->new_line();
				$RTF->add_list($arr_buff, "left", $font, 16);
			} // end if
*/
    		$RTF->open_line();			
			$RTF->cell("ปีงบ", "13", "right", "0");
			foreach($SAH_UP as $key => $val) {
				if ($key > '53')
					$RTF->cell($key, "10", "center", "0");
				else
					$RTF->cell($key, "4", "center", "0");
			}
    		$RTF->close_line();

    		$RTF->open_line();			
			$RTF->cell("เลื่อนเงินเดือน", "13", "right", "0");
			foreach($SAH_UP as $key => $val) {
				if ($key > '53')
					$RTF->cell($val, "10", "center", "0");
				else
					$RTF->cell($val, "4", "center", "0");
			}
    		$RTF->close_line();

			$RTF->open_line();			
			$RTF->cell("ประวัติการดำรงตำแหน่ง", "100", "left", "0");
			$RTF->close_line();

			$first = 1;
			$cmd = " select	POH_EFFECTIVEDATE, POH_PL_NAME, POH_ORG, POH_POS_NO_NAME, POH_POS_NO, LEVEL_NO, 
											POH_SALARY, MOV_CODE, POH_DOCNO, POH_DOCDATE, PL_CODE, PM_CODE, PT_CODE, POH_ORG1, POH_ORG2, POH_ORG3, POH_UNDER_ORG1 
							from PER_POSITIONHIS 
							where PER_ID=$PER_ID and POH_ISREAL != 'N' 
							order by POH_EFFECTIVEDATE, POH_SEQ_NO ";							   
			$count_positionhis = $db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
			if($count_positionhis){
				$arr_buff = array();
				while($data2 = $db_dpis1->get_array()){
					$POH_EFFECTIVEDATE = trim($data2[POH_EFFECTIVEDATE]);
					if ($first==0) {
						$cmd = " select a.SV_CODE, SV_NAME, SRH_ORG, SRH_STARTDATE, SRH_DOCNO, SRH_REMARK
										from   PER_SERVICEHIS a, PER_SERVICE b
										where PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and SRH_STARTDATE >= '$CHK_EFFECTIVEDATE' and SRH_STARTDATE < '$POH_EFFECTIVEDATE'
										order by SRH_STARTDATE ";
						$db_dpis3->send_cmd($cmd);
						while($data3 = $db_dpis3->get_array()){
							if (trim($data3[SRH_ORG])) {	
								$SV_CODE = trim($data3[SV_CODE]);
								$SV_NAME = trim($data3[SV_NAME]);
								$SRH_STARTDATE = trim($data3[SRH_STARTDATE]);
								if($SRH_STARTDATE){
									$SRH_STARTDATE = show_date_format(substr($SRH_STARTDATE, 0, 10),4);
								} // end if
								if ($SV_CODE=="10" || $SV_CODE=="24" || $SV_CODE=="25" || $SV_CODE=="26" || $SV_CODE=="29") $SV_NAME = "ช่วยราชการ";
								elseif ($SV_CODE=="11" || $SV_CODE=="14" || $SV_CODE=="16" || $SV_CODE=="17" || $SV_CODE=="19" || $SV_CODE=="20" || $SV_CODE=="22" || $SV_CODE=="37" || $SV_CODE=="39" || $SV_CODE=="43" || $SV_CODE=="46" || $SV_CODE=="48" || $SV_CODE=="49") $SV_NAME = "ปฏิบัติ";
								elseif ($SV_CODE=="12" || $SV_CODE=="21") $SV_NAME = "รักษาราชการแทน";
								elseif ($SV_CODE=="13" || $SV_CODE=="23") $SV_NAME = "รักษาการในตำแหน่ง";
								elseif ($SV_CODE=="45") $SV_NAME = "แต่งตั้ง";
								$SRH_ORG = trim($data3[SRH_ORG]);
								$SRH_DOCNO = trim($data3[SRH_DOCNO]);
								$SRH_REMARK = trim($data3[SRH_REMARK]);

								$RTF->open_line();			
								$RTF->cell($SRH_STARTDATE, "12", "left", "0");
								$RTF->cell($SV_NAME.' '.$SRH_ORG, "68", "left", "0");
								$RTF->cell($SRH_DOCNO, "20", "right", "0");
								$RTF->close_line();
							}
						}
						$CHK_EFFECTIVEDATE = $POH_EFFECTIVEDATE;
					}
					if ($first==1) {
						$CHK_EFFECTIVEDATE = $POH_EFFECTIVEDATE;
						$first = 0;
					}

					if($POH_EFFECTIVEDATE){
						$POH_EFFECTIVEDATE = show_date_format(substr($POH_EFFECTIVEDATE, 0, 10),4);
					} // end if
			
					$POH_PL_NAME = trim($data2[POH_PL_NAME]);
					$POH_ORG = trim($data2[POH_ORG]);
					$POH_ORG1 = trim($data2[POH_ORG1]);
					$POH_ORG2 = trim($data2[POH_ORG2]);
					$cmd = " select ORG_SHORT from PER_ORG where ORG_NAME = '$POH_ORG2' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					if (trim($data3[ORG_SHORT])) $POH_ORG2 = trim($data3[ORG_SHORT]);

					$POH_ORG3 = trim($data2[POH_ORG3]);
					$POH_UNDER_ORG1 = trim($data2[POH_UNDER_ORG1]);
					$POH_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					$cmd = " select LEVEL_SHORTNAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
					$db_dpis3->send_cmd($cmd);
		//			$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$LEVEL_SHORTNAME = trim($data3[LEVEL_SHORTNAME]);
					$POSITION_LEVEL = trim($data3[POSITION_LEVEL]);
					if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9") 
						$POH_PL_NAME .= " ".level_no_format($LEVEL_NO);
					else
						$POH_PL_NAME .= $POSITION_LEVEL;

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
					if ($LEVEL_NO >= "1" && $LEVEL_NO <= "9" && $PT_NAME!="ทั่วไป") $POH_PL_NAME .= $PT_NAME;
					$POH_PL_NAME = pl_name_format($PL_NAME, $PM_NAME, $PT_NAME, $LEVEL_NO, 2, $POH_ORG3, $POH_UNDER_ORG1);	
					$POH_ORG = trim($POH_UNDER_ORG1. " ".$POH_ORG3. " ".$POH_ORG2);

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
					$RTF->cell($POH_EFFECTIVEDATE, "12", "left", "0");
					$RTF->cell($MOV_NAME.' '.$POH_PL_NAME, "68", "left", "0");
					$RTF->cell($POH_POS_NO, "7", "right", "0");
					$RTF->cell($LEVEL_SHORTNAME, "5", "right", "0");
					$RTF->cell(number_format($POH_SALARY), "8", "right", "0");
					$RTF->close_line();

					$RTF->open_line();			
					$RTF->cell("", "12", "left", "0");
					$RTF->cell($POH_ORG, "63", "left", "0");
					$RTF->cell($POH_DOCNO, "25", "right", "0");
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