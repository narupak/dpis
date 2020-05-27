<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$SC_FUND = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($SC_FUND)));
	if (!$SC_GRADE) $SC_GRADE = "NULL";
	if (!$SC_TEST_RESULT) $SC_TEST_RESULT = "NULL";
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_SCHOLAR set AUDIT_FLAG = 'N' where SC_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_SCHOLAR set AUDIT_FLAG = 'Y' where SC_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(SC_ID) as max_id from PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SC_ID = $data[max_id] + 1;
		
		$SCH_CODE = (trim($SCH_CODE))? "'" . trim($SCH_CODE) . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . trim($EL_CODE) . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . trim($EN_CODE) . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . trim($EM_CODE) . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . trim($INS_CODE) . "'"  : "NULL";	
		$SC_STARTDATE = save_date($SC_STARTDATE); 
		$SC_ENDDATE = save_date($SC_ENDDATE); 
		$SC_FINISHDATE = save_date($SC_FINISHDATE); 
		$SC_BACKDATE = save_date($SC_BACKDATE); 
		$SC_DOCDATE = save_date($SC_DOCDATE); 
		$SC_TEST_DATE = save_date($SC_TEST_DATE); 
		
		$cmd = " insert into PER_SCHOLAR	(SC_ID, PER_ID, SC_TYPE, SC_STARTDATE, SC_ENDDATE, SCH_CODE, 
				CT_CODE, SC_FUND, EN_CODE, EM_CODE, INS_CODE, PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE,
				EL_CODE, SC_FINISHDATE, SC_BACKDATE, SC_GRADE, SC_HONOR, SC_DOCNO, SC_DOCDATE, 
				SC_REMARK, SC_INSTITUTE, SC_TEST_DATE, SC_TEST_RESULT)
				values ($SC_ID, $PER_ID, 1, '$SC_STARTDATE', '$SC_ENDDATE', $SCH_CODE, $CT_CODE, 
				'$SC_FUND', $EN_CODE, $EM_CODE, $INS_CODE, '$PER_CARDNO', $PER_ID_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE',
				$EL_CODE, '$SC_FINISHDATE', '$SC_BACKDATE', $SC_GRADE, '$SC_HONOR', '$SC_DOCNO', 
				'$SC_DOCDATE', '$SC_REMARK', '$SC_INSTITUTE', '$SC_TEST_DATE', $SC_TEST_RESULT) ";
		$db_dpis->send_cmd($cmd);
		//echo $cmd;
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการลาศึกษาต่อ [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if
	
	if($command=="UPDATE" && $PER_ID && $SC_ID){
		$SCH_CODE = (trim($SCH_CODE))? "'" . trim($SCH_CODE) . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . trim($EL_CODE) . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . trim($EN_CODE) . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . trim($EM_CODE) . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . trim($INS_CODE) . "'"  : "NULL";		
		$SC_STARTDATE = save_date($SC_STARTDATE); 
		$SC_ENDDATE = save_date($SC_ENDDATE); 
		$SC_FINISHDATE = save_date($SC_FINISHDATE); 
		$SC_BACKDATE = save_date($SC_BACKDATE); 
		$SC_DOCDATE = save_date($SC_DOCDATE); 
		$SC_TEST_DATE = save_date($SC_TEST_DATE); 

		$cmd = " update PER_SCHOLAR set
						SC_STARTDATE='$SC_STARTDATE', 
						SC_ENDDATE='$SC_ENDDATE', 
						SCH_CODE=$SCH_CODE, 
						CT_CODE=$CT_CODE,
						SC_FUND='$SC_FUND', 
						EN_CODE=$EN_CODE, 
						EM_CODE=$EM_CODE, 
						INS_CODE=$INS_CODE, 
						PER_CARDNO='$PER_CARDNO', 
						EL_CODE=$EL_CODE, 
						SC_FINISHDATE='$SC_FINISHDATE',
						SC_BACKDATE='$SC_BACKDATE',
						SC_GRADE=$SC_GRADE, 
						SC_HONOR='$SC_HONOR', 
						SC_DOCNO='$SC_DOCNO', 
						SC_DOCDATE='$SC_DOCDATE', 
						SC_REMARK='$SC_REMARK', 
						SC_INSTITUTE='$SC_INSTITUTE', 
						SC_TEST_DATE='$SC_TEST_DATE',
						SC_TEST_RESULT=$SC_TEST_RESULT, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
					where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการลาศึกษาต่อ [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SC_ID){
		$cmd = " select EN_CODE from PER_SCHOLAR where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EN_CODE = $data[EN_CODE];
		
		$cmd = " delete from PER_SCHOLAR where SC_ID=$SC_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการลาศึกษาต่อ [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if

	if(($UPD && $PER_ID && $SC_ID) || ($VIEW && $PER_ID && $SC_ID)){
		$cmd = "	select	SC_STARTDATE, SC_ENDDATE, SC_FUND, SCH_CODE, CT_CODE, 
											EN_CODE, EM_CODE, INS_CODE, UPDATE_USER, UPDATE_DATE, 
											EL_CODE, SC_FINISHDATE, SC_BACKDATE, SC_GRADE, SC_HONOR, 
											SC_DOCNO, SC_DOCDATE, SC_REMARK, SC_INSTITUTE, SC_TEST_DATE, SC_TEST_RESULT
							from		PER_SCHOLAR
							where	SC_ID=$SC_ID ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SC_STARTDATE = show_date_format(trim($data[SC_STARTDATE]), 1);
		$SC_ENDDATE = show_date_format(trim($data[SC_ENDDATE]), 1);
		$SC_FUND = $data[SC_FUND];
		$SC_FINISHDATE = show_date_format(trim($data[SC_FINISHDATE]), 1);
		$SC_BACKDATE = show_date_format(trim($data[SC_BACKDATE]), 1);
		$SC_GRADE = $data[SC_GRADE];
		$SC_HONOR = trim($data[SC_HONOR]);
		$SC_DOCNO = trim($data[SC_DOCNO]);
		$SC_DOCDATE = show_date_format(trim($data[SC_DOCDATE]), 1);
		$SC_REMARK = trim($data[SC_REMARK]);
		$SC_INSTITUTE = trim($data[SC_INSTITUTE]);
		$SC_TEST_DATE = show_date_format(trim($data[SC_TEST_DATE]), 1);
		$SC_TEST_RESULT = $data[SC_TEST_RESULT];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$SCH_NAME = $CT_NAME = $EL_NAME = $EN_NAME = $EM_NAME = $INS_NAME = $INS_COUNTRY = "";
		$SCH_CODE = $data[SCH_CODE];
		if($SCH_CODE){
			$cmd = " select SCH_NAME from PER_SCHOLARSHIP where SCH_CODE='$SCH_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$SCH_NAME = $data2[SCH_NAME];
		} // end if

		$CT_CODE = $data[CT_CODE];
		if($CT_CODE){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		} // end if

		$EL_CODE = $data[EL_CODE];
		if($EL_CODE){
			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EL_NAME = $data2[EL_NAME];
		} // end if

		$EN_CODE = $data[EN_CODE];
		if($EN_CODE){
			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE='$EN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_NAME = $data2[EN_NAME];
		} // end if

		$EM_CODE = $data[EM_CODE];
		if($EM_CODE){
			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE='$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EM_NAME = $data2[EM_NAME];
		} // end if

		$INS_CODE = $data[INS_CODE];
		if($INS_CODE){
			if($DPISDB=="odbc"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	PER_INSTITUTE.INS_NAME, PER_COUNTRY.CT_NAME
								 from 		PER_INSTITUTE, PER_COUNTRY
								 where 	PER_INSTITUTE.CT_CODE=PER_COUNTRY.CT_CODE(+) and PER_INSTITUTE.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			} // end if
			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$INS_NAME = $data2[INS_NAME];
			$INS_COUNTRY = $data2[CT_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($SC_ID);
		unset($SC_STARTDATE);
		unset($SC_ENDDATE);
		unset($SC_FUND);
		unset($SCH_CODE);
		unset($SCH_NAME);
		unset($CT_CODE);
		unset($CT_NAME);
		unset($EN_CODE);
		unset($EN_NAME);
		unset($EM_CODE);
		unset($EM_NAME);
		unset($INS_CODE);
		unset($INS_NAME);
		$INS_COUNTRY = "ไทย";
		unset($EL_CODE);
		unset($EL_NAME);
		unset($SC_FINISHDATE);
		unset($SC_BACKDATE);
		unset($SC_GRADE);
		unset($SC_HONOR);
		unset($SC_DOCNO);
		unset($SC_DOCDATE);
		unset($SC_REMARK);
		unset($SC_INSTITUTE);
		unset($SC_TEST_DATE);
		unset($SC_TEST_RESULT);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>