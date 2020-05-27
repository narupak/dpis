<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/psst_person.php");
	/*cdgs*/

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);

	$IMG_PATH = "../attachment/pic_personal/";	
	if($MAIN_VIEW==1) $UPD = 1;		//	if($MAIN_VIEW==1) $VIEW = 1;					//14/09/2012 
	if(!$PER_GENDER) $PER_GENDER = 1;
	
//	echo "$CTRL_TYPE :: $SESS_USERGROUP_LEVEL :: $MINISTRY_ID :: $MINISTRY_NAME :: $DEPARTMENT_ID :: $DEPARTMENT_NAME :: $SESS_MINISTRY_NAME :: $SESS_DEPARTMENT_NAME<br>";

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	if($command=="ADD" || $command=="UPDATE"){		//$command=="CHKADD" ||

		$ASS_DEPARTMENT_ID = (trim($ASS_DEPARTMENT_ID))? $ASS_DEPARTMENT_ID : $MAIN_DEPARTMENT_ID;
		$ASS_ORG_ID = (trim($ASS_ORG_ID))? $ASS_ORG_ID : "NULL";
		$ASS_ORG_ID_1 = (trim($ASS_ORG_ID_1))? $ASS_ORG_ID_1 : "NULL";
		$ASS_ORG_ID_2 = (trim($ASS_ORG_ID_2))? $ASS_ORG_ID_2 : "NULL";
		$ASS_ORG_ID_3 = (trim($ASS_ORG_ID_3))? $ASS_ORG_ID_3 : "NULL";
		$ASS_ORG_ID_4 = (trim($ASS_ORG_ID_4))? $ASS_ORG_ID_4 : "NULL";
		$ASS_ORG_ID_5 = (trim($ASS_ORG_ID_5))? $ASS_ORG_ID_5 : "NULL";
		$PER_ORGMGT = (trim($PER_ORGMGT))? $PER_ORGMGT : '0';
		$PER_ORGMGT = str_replace(",", "", $PER_ORGMGT);
		$PER_SALARY = (trim($PER_SALARY))? $PER_SALARY : '0';
		$PER_SALARY = str_replace(",", "", $PER_SALARY);
		$PER_MGTSALARY = (trim($PER_MGTSALARY))? $PER_MGTSALARY : '0';
		$PER_MGTSALARY = str_replace(",", "", $PER_MGTSALARY);
		$PER_SPSALARY = (trim($PER_SPSALARY))? $PER_SPSALARY : '0';
		$PER_SPSALARY = str_replace(",", "", $PER_SPSALARY);
		$PER_DISABILITY = (trim($PER_DISABILITY))? $PER_DISABILITY : '1';
		$PER_ORDAIN += 0;
		$PER_SOLDIER += 0;
		$PER_MEMBER += 0;
		$PER_UNION += 0;
		$PER_UNION2 += 0;
		$PER_UNION3 += 0;
		$PER_UNION4 += 0;
		$PER_UNION5 += 0;
		$PER_COOPERATIVE += 0;
		$PN_CODE_M = (trim($PN_CODE_M))? "'".$PN_CODE_M."'" : "NULL";
		$PN_CODE_F = (trim($PN_CODE_F))? "'".$PN_CODE_F."'" : "NULL";
		$LEVEL_NO = (trim($LEVEL_NO))? "'".$LEVEL_NO."'" : "NULL";
		$LEVEL_NO_SALARY = (trim($LEVEL_NO_SALARY))? "'".$LEVEL_NO_SALARY."'" : "NULL";
		$RE_CODE = (trim($RE_CODE))? "'".$RE_CODE."'" : "NULL";
		$PV_CODE_PER = (trim($PV_CODE_PER))? "'".$PV_CODE_PER."'" : "NULL";	
		$PER_CARDNO = (trim($PER_CARDNO))? trim($PER_CARDNO) : "NULL";
		$PER_OFFNO = (trim($PER_OFFNO))? "'".trim($PER_OFFNO)."'" : "NULL";
		$PER_TAXNO = (trim($PER_TAXNO))? "'".trim($PER_TAXNO)."'" : "NULL";
		$PER_NICKNAME = (trim($PER_NICKNAME))? "'".trim($PER_NICKNAME)."'" : "NULL";
		$PER_HOME_TEL = (trim($PER_HOME_TEL))? "'".trim($PER_HOME_TEL)."'" : "NULL";
		$PER_OFFICE_TEL = (trim($PER_OFFICE_TEL))? "'".trim($PER_OFFICE_TEL)."'" : "NULL";
		$PER_FAX = (trim($PER_FAX))? "'".trim($PER_FAX)."'" : "NULL";
		$PER_MOBILE = (trim($PER_MOBILE))? "'".trim($PER_MOBILE)."'" : "NULL";
		$PER_EMAIL = (trim($PER_EMAIL))? "'".trim($PER_EMAIL)."'" : "NULL";
		$PER_FILE_NO = (trim($PER_FILE_NO))? "'".trim($PER_FILE_NO)."'" : "NULL";
		$PER_BANK_ACCOUNT = (trim($PER_BANK_ACCOUNT))? "'".trim($PER_BANK_ACCOUNT)."'" : "NULL";
		$PER_CONTACT_PERSON = (trim($PER_CONTACT_PERSON))? "'".trim($PER_CONTACT_PERSON)."'" : "NULL";
		$PER_REMARK = (trim($PER_REMARK))? "'".trim($PER_REMARK)."'" : "NULL";
		$PER_START_ORG = (trim($PER_START_ORG))? "'".trim($PER_START_ORG)."'" : "NULL";
		$PER_COOPERATIVE_NO = (trim($PER_COOPERATIVE_NO))? "'".trim($PER_COOPERATIVE_NO)."'" : "NULL";
		$PL_NAME_WORK = (trim($PL_NAME_WORK))? "'".trim($PL_NAME_WORK)."'" : "NULL";
		$ORG_NAME_WORK = (trim($ORG_NAME_WORK))? "'".trim($ORG_NAME_WORK)."'" : "NULL";
		$PER_DOCNO = (trim($PER_DOCNO))? "'".trim($PER_DOCNO)."'" : "NULL";
		$PER_POS_REASON = (trim($PER_POS_REASON))? "'".trim($PER_POS_REASON)."'" : "NULL";
		$PER_POS_YEAR = (trim($PER_DOCNO))? "'".trim($PER_POS_YEAR)."'" : "NULL";
		$PER_POS_DOCTYPE = (trim($PER_POS_DOCTYPE))? "'".trim($PER_POS_DOCTYPE)."'" : "NULL";
		$PER_POS_DOCNO = (trim($PER_POS_DOCNO))? "'".trim($PER_POS_DOCNO)."'" : "NULL";
		$PER_POS_ORG = (trim($PER_POS_ORG))? "'".trim($PER_POS_ORG)."'" : "NULL";
		$PER_ORDAIN_DETAIL = (trim($PER_ORDAIN_DETAIL))? "'".trim($PER_ORDAIN_DETAIL)."'" : "NULL";
		$PER_POS_ORGMGT = (trim($PER_POS_ORGMGT))? "'".trim($PER_POS_ORGMGT)."'" : "NULL";
		$PER_CONTACT_COUNT = (trim($PER_CONTACT_COUNT))? "'".trim($PER_CONTACT_COUNT)."'" : "NULL";
		$PER_JOB = (trim($PER_JOB))? "'".trim($PER_JOB)."'" : "NULL";
		$PER_BIRTH_PLACE = (trim($PER_BIRTH_PLACE))? "'".trim($PER_BIRTH_PLACE)."'" : "NULL";
		$PER_SCAR = (trim($PER_SCAR))? "'".trim($PER_SCAR)."'" : "NULL";

		if ($PER_TYPE==1 || $PER_TYPE==5) {
			$POS_ID = (trim($POS_ID))? $POS_ID : "NULL";
			$PAY_ID = (trim($PAY_ID))? $PAY_ID : "NULL";
			$POEM_ID = $POEMS_ID = $POT_ID = "NULL";
		} elseif ($PER_TYPE==2) {
			$POEM_ID = (trim($POS_ID))? $POS_ID : "NULL";
			$POS_ID = $PAY_ID = $POEMS_ID = $POT_ID = "NULL";			
		} elseif ($PER_TYPE==3) {
			$POEMS_ID = (trim($POS_ID))? $POS_ID : "NULL";
			$POS_ID = $PAY_ID = $POEM_ID = $POT_ID = "NULL";			
		} elseif ($PER_TYPE==4) {
			$POT_ID = (trim($POS_ID))? $POS_ID : "NULL";
			$POS_ID = $PAY_ID = $POEM_ID = $POEMS_ID = "NULL";			
		}

		for ($i=0; $i<count($PER_HIP_FLAG); $i++) {
			$HIP_FLAG_TXT.= "||$PER_HIP_FLAG[$i]";
		}
		$HIP_FLAG_TXT = (trim($HIP_FLAG_TXT))? $HIP_FLAG_TXT."||" : "";
		$PER_CERT_OCC = trim($PER_CERT_OCC)? $PER_CERT_OCC : "";
		$PER_AUDIT_ABSENT_FLAG = trim($PER_AUDIT_ABSENT_FLAG)? $PER_AUDIT_ABSENT_FLAG : 0;
		$PER_PROBATION_FLAG = trim($PER_PROBATION_FLAG)? $PER_PROBATION_FLAG : 0;
		$PER_RENEW = trim($PER_RENEW)? $PER_RENEW : 0;
	
		if($PER_STATUS == 2)		$PER_POSDATE = $PER_POSDATE;
		else						$PER_POSDATE = "";
	
		if($PER_BIRTHDATE){
			$arr_temp = explode("/", $PER_BIRTHDATE);
			$PER_BIRTHDATE_PWD = ($arr_temp[0]).($arr_temp[1]).($arr_temp[2]);
			$passwd = md5($PER_BIRTHDATE_PWD);
			$PER_BIRTHDATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
			$RETIRE_YEAR = ($arr_temp[2] - 543) + 60;
			
			if($arr_temp[1] > 10 || ($arr_temp[1]==10 && $arr_temp[0] > "01"))    
				$RETIRE_YEAR += 1;
			$PER_RETIREDATE = $RETIRE_YEAR."-10-01"; 
			
		} // end if
		$PER_STARTDATE =  save_date($PER_STARTDATE);
		$PER_OCCUPYDATE =  save_date($PER_OCCUPYDATE);
		$PER_POSDATE =  save_date($PER_POSDATE);
		$PER_MEMBERDATE =  save_date($PER_MEMBERDATE);
		$PER_UNIONDATE =  save_date($PER_UNIONDATE);
		$PER_UNIONDATE2 =  save_date($PER_UNIONDATE2);
		$PER_UNIONDATE3 =  save_date($PER_UNIONDATE3);
		$PER_UNIONDATE4 =  save_date($PER_UNIONDATE4);
		$PER_UNIONDATE5 =  save_date($PER_UNIONDATE5);
		$PER_DOCDATE =  save_date($PER_DOCDATE);
		$PER_EFFECTIVEDATE =  save_date($PER_EFFECTIVEDATE);
		
		if ($PER_TYPE==1 || $PER_TYPE==5) {
			// �� PT_CODE ����Ѻ�Թ��͹�дѺ ����� ������ ���������дѺ�٧
			$cmd = "	select PT_CODE from PER_POSITION where POS_ID=$POS_ID ";	
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PT_CODE = trim($data1[PT_CODE]);
			// check ��� $PT_CODE �´٨ҡ table PER_TYPE 
			if ($PT_CODE == 32 && $LEVEL_NO_SALARY=="11")		$LAYER_TYPE = 2;			// ���������дѺ�٧
			else												$LAYER_TYPE = 1;			// �����
		} // if
		
		     // somsak 14/11/2562
			if($PER_STATUS == 1 && $PER_TYPE==1){
				$cmd = "update per_position set pos_salary = $PER_SALARY where pos_id = $CHK_POS_ID";
				$db_dpis->send_cmd($cmd);
			}
			// end somsak 14/11/2562
	} // end if

//	echo "command=$command LEVEL_NO=$LEVEL_NO<br>";
	if($command=="ADD"){
		$c_duplicate=0;
		 
		 if ($CARD_NO_FILL=="Y") {	 // C06 ��ͧ��� �Ţ���ѵ� ��� ��� check ���
			$cmd2="select PER_CARDNO from PER_PERSONAL where trim(PER_CARDNO)='$PER_CARDNO' ";
			$db_dpis->send_cmd($cmd2);
			$data2 = $db_dpis->get_array();
			$card_no=$data2[PER_CARDNO];
			//$db_dpis->show_error(); echo "<br><hr>";
			//echo "$card_no - $cmd2";
		
			if($card_no) {  //���
				$c_duplicate=1; 
				$err_text = "�Ţ��Шӵ�ǻ�ЪҪ���� $PER_CARDNO";
			}else {	
				$cmd = " select PER_ID from PER_PERSONAL where trim(PER_CARDNO)='$PER_CARDNO' ";
				$count_person = $db_dpis->send_cmd($cmd);
				//echo "get cardno : $PER_CARDNO ($cmd ($count_person))<br>";
				if(!$count_person){
					$cmd = " select max(PER_ID) as max_id from PER_PERSONAL ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);		
					$PER_ID = $data[max_id] + 1;
					
					echo $cmd = " insert into PER_PERSONAL 
										(	PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
											PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID,  
											LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, 
											PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, 
											PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
											PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, 
											PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
											PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, 
											PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, PER_AUDIT_FLAG, DEPARTMENT_ID, 
											LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
											PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
											PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
											PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
											PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
											PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
											PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
											PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
											PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB, UPDATE_USER, UPDATE_DATE, 
											ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PER_PROBATION_FLAG, DEPARTMENT_ID_ASS, 
											PER_BIRTH_PLACE, PER_SCAR, PER_RENEW)
									 values
										(	$PER_ID, $PER_TYPE, '$OT_CODE', '".trim($PN_CODE). "', '".trim($PER_NAME). "', '".trim($PER_SURNAME). "', 
											'".trim($PER_ENG_NAME). "', '".trim($PER_ENG_SURNAME). "', $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
											$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, 
											$PER_GENDER, '$MR_CODE', '$PER_CARDNO', $PER_OFFNO, $PER_TAXNO, 
											'$PER_BLOOD', $RE_CODE, '$PER_BIRTHDATE', '$PER_RETIREDATE', '$PER_STARTDATE', 
											'$PER_OCCUPYDATE', '$PER_POSDATE', $PN_CODE_F, '".trim($PER_FATHERNAME). "', 
											'".trim($PER_FATHERSURNAME). "', $PN_CODE_M, '".trim($PER_MOTHERNAME). "', '".trim($PER_MOTHERSURNAME). "', 
											$PV_CODE_PER, '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, $PER_MEMBER, $PER_STATUS, 
											'$HIP_FLAG_TXT', '$PER_CERT_OCC', '$PER_AUDIT_ABSENT_FLAG', $MAIN_DEPARTMENT_ID, $LEVEL_NO_SALARY, 
											$PER_NICKNAME, $PER_HOME_TEL, $PER_OFFICE_TEL, $PER_FAX, $PER_MOBILE, $PER_EMAIL, 
											$PER_FILE_NO, $PER_BANK_ACCOUNT, $PER_CONTACT_PERSON, $PER_REMARK, $PER_START_ORG, 
											$PER_COOPERATIVE, $PER_COOPERATIVE_NO, '$PER_MEMBERDATE', '$ES_CODE', $PAY_ID, 
											$PL_NAME_WORK, $ORG_NAME_WORK, $PER_DOCNO, '$PER_DOCDATE', '$PER_EFFECTIVEDATE', 
											$PER_POS_REASON, $PER_POS_YEAR, $PER_POS_DOCTYPE, $PER_POS_DOCNO, $PER_POS_ORG, 
											$PER_ORDAIN_DETAIL, $PER_POS_ORGMGT, $PER_CONTACT_COUNT, $PER_DISABILITY, 
											$PER_UNION, '$PER_UNIONDATE', $PER_UNION2, '$PER_UNIONDATE2', $PER_UNION3, '$PER_UNIONDATE3', 
											$PER_UNION4, '$PER_UNIONDATE4', $PER_UNION5, '$PER_UNIONDATE5', $PER_JOB, $SESS_USERID, '$UPDATE_DATE', 
											$ASS_ORG_ID_1,$ASS_ORG_ID_2, $ASS_ORG_ID_3, $ASS_ORG_ID_4, $ASS_ORG_ID_5, '$PER_PROBATION_FLAG', 
											$ASS_DEPARTMENT_ID, $PER_BIRTH_PLACE, $PER_SCAR, $PER_RENEW) ";
				}
			}
		 }else{  // C06 ��������� �դ���� NULL ����ͧ� check ��� ��������ͧ check ��Ӵ��� 
			if(trim($PER_CARDNO)=='NULL'){
				$cmd = " select max(PER_ID) as max_id from PER_PERSONAL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);		
				$PER_ID = $data[max_id] + 1;
				
				$cmd = " insert into PER_PERSONAL 
									(	PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
										PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID,  
										LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, 
										PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, 
										PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
										PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, 
										PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
										PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, 
										PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, PER_AUDIT_FLAG, DEPARTMENT_ID, 
										LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
										PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
										PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
										PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
										PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
										PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
										PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
										PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
										PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB, UPDATE_USER, UPDATE_DATE, 
										ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PER_PROBATION_FLAG, DEPARTMENT_ID_ASS, 
										PER_BIRTH_PLACE, PER_SCAR, PER_RENEW)
								 values
									(	$PER_ID, $PER_TYPE, '$OT_CODE', '".trim($PN_CODE). "', '".trim($PER_NAME). "', '".trim($PER_SURNAME). "', 
										'".trim($PER_ENG_NAME). "', '".trim($PER_ENG_SURNAME). "', $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
										$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, 
										$PER_GENDER, '$MR_CODE', '$PER_CARDNO', $PER_OFFNO, $PER_TAXNO, 
										'$PER_BLOOD', $RE_CODE, '$PER_BIRTHDATE', '$PER_RETIREDATE', '$PER_STARTDATE', 
										'$PER_OCCUPYDATE', '$PER_POSDATE', $PN_CODE_F, '".trim($PER_FATHERNAME). "', 
										'".trim($PER_FATHERSURNAME). "', $PN_CODE_M, '".trim($PER_MOTHERNAME). "', '".trim($PER_MOTHERSURNAME). "', 
										$PV_CODE_PER, '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, $PER_MEMBER, $PER_STATUS, 
										'$HIP_FLAG_TXT', '$PER_CERT_OCC', '$PER_AUDIT_ABSENT_FLAG', $MAIN_DEPARTMENT_ID, $LEVEL_NO_SALARY, 
										$PER_NICKNAME, $PER_HOME_TEL, $PER_OFFICE_TEL, $PER_FAX, $PER_MOBILE, $PER_EMAIL, 
										$PER_FILE_NO, $PER_BANK_ACCOUNT, $PER_CONTACT_PERSON, $PER_REMARK, $PER_START_ORG, 
										$PER_COOPERATIVE, $PER_COOPERATIVE_NO, '$PER_MEMBERDATE', '$ES_CODE', $PAY_ID, 
										$PL_NAME_WORK, $ORG_NAME_WORK, $PER_DOCNO, '$PER_DOCDATE', '$PER_EFFECTIVEDATE', 
										$PER_POS_REASON, $PER_POS_YEAR, $PER_POS_DOCTYPE, $PER_POS_DOCNO, $PER_POS_ORG, 
										$PER_ORDAIN_DETAIL, $PER_POS_ORGMGT, $PER_CONTACT_COUNT, $PER_DISABILITY, 
										$PER_UNION, '$PER_UNIONDATE', $PER_UNION2, '$PER_UNIONDATE2', $PER_UNION3, '$PER_UNIONDATE3', 
										$PER_UNION4, '$PER_UNIONDATE4', $PER_UNION5, '$PER_UNIONDATE5', $PER_JOB, $SESS_USERID, '$UPDATE_DATE', 
										$ASS_ORG_ID_1,$ASS_ORG_ID_2, $ASS_ORG_ID_3, $ASS_ORG_ID_4, $ASS_ORG_ID_5, '$PER_PROBATION_FLAG', 
										$ASS_DEPARTMENT_ID, $PER_BIRTH_PLACE, $PER_SCAR, $PER_RENEW) ";
			}else{  // �к��� check ���
				$cmd2="select PER_CARDNO from PER_PERSONAL where trim(PER_CARDNO)='$PER_CARDNO' ";
				$db_dpis->send_cmd($cmd2);
				$data2 = $db_dpis->get_array();
				$card_no=$data2[PER_CARDNO];
				//$db_dpis->show_error(); echo "<br><hr>";
				//echo "$card_no - $cmd2";
			
				if($card_no) {  //���
					$c_duplicate=1; 
					$err_text = "�Ţ��Шӵ�ǻ�ЪҪ���� $PER_CARDNO";
				}else {	
					$cmd = " select PER_ID from PER_PERSONAL where trim(PER_CARDNO)='$PER_CARDNO' ";
					$count_person = $db_dpis->send_cmd($cmd);
					//echo "get cardno : $PER_CARDNO ($cmd ($count_person))<br>";
				
					if(!$count_person){
						$cmd = " select max(PER_ID) as max_id from PER_PERSONAL ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$data = array_change_key_case($data, CASE_LOWER);		
						$PER_ID = $data[max_id] + 1;
						
						$cmd = " insert into PER_PERSONAL 
											(	PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
												PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID,  
												LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, 
												PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, 
												PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
												PER_OCCUPYDATE, PER_POSDATE, PN_CODE_F, PER_FATHERNAME, 
												PER_FATHERSURNAME, PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
												PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, 
												PER_MEMBER, PER_STATUS, PER_HIP_FLAG, PER_CERT_OCC, PER_AUDIT_FLAG, DEPARTMENT_ID, 
												LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, PER_OFFICE_TEL, PER_FAX, 
												PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, PER_CONTACT_PERSON, 
												PER_REMARK, PER_START_ORG, PER_COOPERATIVE, PER_COOPERATIVE_NO, 
												PER_MEMBERDATE, ES_CODE, PAY_ID, PL_NAME_WORK, ORG_NAME_WORK, 
												PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, PER_POS_REASON, 
												PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, PER_POS_ORG, 
												PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, PER_DISABILITY, 
												PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, PER_UNION3, PER_UNIONDATE3, 
												PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, PER_JOB, UPDATE_USER, UPDATE_DATE, 
												ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PER_PROBATION_FLAG, DEPARTMENT_ID_ASS, 
												PER_BIRTH_PLACE, PER_SCAR, PER_RENEW)
										 values
											(	$PER_ID, $PER_TYPE, '$OT_CODE', '".trim($PN_CODE). "', '".trim($PER_NAME). "', '".trim($PER_SURNAME). "', 
												'".trim($PER_ENG_NAME). "', '".trim($PER_ENG_SURNAME). "', $ASS_ORG_ID, $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, 
												$LEVEL_NO, $PER_ORGMGT, $PER_SALARY, $PER_MGTSALARY, $PER_SPSALARY, 
												$PER_GENDER, '$MR_CODE', '$PER_CARDNO', $PER_OFFNO, $PER_TAXNO, 
												'$PER_BLOOD', $RE_CODE, '$PER_BIRTHDATE', '$PER_RETIREDATE', '$PER_STARTDATE', 
												'$PER_OCCUPYDATE', '$PER_POSDATE', $PN_CODE_F, '".trim($PER_FATHERNAME). "', 
												'".trim($PER_FATHERSURNAME). "', $PN_CODE_M, '".trim($PER_MOTHERNAME). "', '".trim($PER_MOTHERSURNAME). "', 
												$PV_CODE_PER, '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, $PER_MEMBER, $PER_STATUS, 
												'$HIP_FLAG_TXT', '$PER_CERT_OCC', '$PER_AUDIT_ABSENT_FLAG', $MAIN_DEPARTMENT_ID, $LEVEL_NO_SALARY, 
												$PER_NICKNAME, $PER_HOME_TEL, $PER_OFFICE_TEL, $PER_FAX, $PER_MOBILE, $PER_EMAIL, 
												$PER_FILE_NO, $PER_BANK_ACCOUNT, $PER_CONTACT_PERSON, $PER_REMARK, $PER_START_ORG, 
												$PER_COOPERATIVE, $PER_COOPERATIVE_NO, '$PER_MEMBERDATE', '$ES_CODE', $PAY_ID, 
												$PL_NAME_WORK, $ORG_NAME_WORK, $PER_DOCNO, '$PER_DOCDATE', '$PER_EFFECTIVEDATE', 
												$PER_POS_REASON, $PER_POS_YEAR, $PER_POS_DOCTYPE, $PER_POS_DOCNO, $PER_POS_ORG, 
												$PER_ORDAIN_DETAIL, $PER_POS_ORGMGT, $PER_CONTACT_COUNT, $PER_DISABILITY, 
												$PER_UNION, '$PER_UNIONDATE', $PER_UNION2, '$PER_UNIONDATE2', $PER_UNION3, '$PER_UNIONDATE3', 
												$PER_UNION4, '$PER_UNIONDATE4', $PER_UNION5, '$PER_UNIONDATE5', $PER_JOB, $SESS_USERID, '$UPDATE_DATE', 
												$ASS_ORG_ID_1,$ASS_ORG_ID_2, $ASS_ORG_ID_3, $ASS_ORG_ID_4, $ASS_ORG_ID_5, '$PER_PROBATION_FLAG', 
												$ASS_DEPARTMENT_ID, $PER_BIRTH_PLACE, $PER_SCAR, $PER_RENEW) ";
					}
				}	 //end else
			}	//end else �к���
		} // end }else{  // C06 ��������� �դ���� NULL ����ͧ� check ��� ��������ͧ check ��Ӵ��� 
		if($cmd)		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "<hr> add [$CARD_NO_FILL]---->".$cmd."<hr>";
   // echo "<pre>".$cmd;
/*
				if ($PER_TYPE==1 || $PER_TYPE==5) {
					$cmd = " update PER_POSITION set POS_SALARY = $PER_SALARY	where POS_ID=$POS_ID ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
				}
*/
				/*cdgs*/
				// $aaa=f_ins_psst_person($PER_ID);
				/*cdgs */
			if ($PER_TYPE==3) {
				 if ($PER_STATUS==1) 
					$cmd = " update PER_POS_EMPSER set PPS_CODE = 1	where POEMS_ID=$POEMS_ID ";
				 else
					$cmd = " update PER_POS_EMPSER set PPS_CODE = 2	where POEMS_ID=$POEMS_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}

			$cmd = " SELECT PER_ID FROM PER_PERSONAL WHERE (PER_CARDNO = '".trim($PER_CARDNO)."') ";
			$count_add_complete = $db_dpis->send_cmd($cmd);	
			if($count_add_complete){
				if($PN_CODE){
					$cmd = "select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE'";
					$db_dpis2->send_cmd($cmd);
					$data2  = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];
				}
				$FULL_PER_NAME = $PN_NAME.$PER_NAME." ".$PER_SURNAME;
?>			
				<script language='JavaScript'>
					var full_name = "<?=$FULL_PER_NAME;?>";
					alert('���������� '+full_name+' ���º��������'); 
				</script>
<?			
			} //end if

			if(($c_duplicate==0) || ($CARD_NO_FILL!="Y" && trim($PER_CARDNO)=='NULL')){   // ���������Ţ��� ��� ��� ������ loop ���
				// ���� Login User  // �ó� NULL
				$cmdchk_new_user = " SELECT USERNAME FROM USER_DETAIL WHERE USERNAME = '$PER_CARDNO' ";
				//$db_dpis->show_error();
				//echo "New user : ".$cmd;
				$count_data = $db_dpis->send_cmd($cmdchk_new_user);
				if (!$count_data) {
					$cmd = " select max(id) as max_id from user_detail ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$max_id = $data[max_id] + 1;
				
					$group_id = 3;
					$username = $PER_CARDNO;
					$user_link_id = $PER_ID;
					$user_name = "$PER_NAME $PER_SURNAME";
					$address = $ORG_NAME;
					$titlename = $PN_NAME;
				
					$cmd_new_user = " insert into USER_DETAIL (ID, GROUP_ID, USERNAME, PASSWORD, USER_LINK_ID, FULLNAME, 
									ADDRESS, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY, TITLENAME)
									 values ($max_id, $group_id, '$username', '$passwd', $user_link_id, '$user_name', 
									'$address', $update_date, $update_by, $update_date, $update_by, '$titlename') ";				
					$db_dpis->send_cmd($cmd_new_user);
					//$db_dpis->show_error();
				}

				if ($BKK_FLAG==1) {
					$cmd = " select ORG_ID from PER_POSITION where POS_ID = $POS_ID ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$TMP_ORG_ID = $data[ORG_ID];

					$cmd = " select max(CHANGEID) as max_id from DPIS_ORGCHANGE ";
					$db_dpis35->send_cmd($cmd);
					$data = $db_dpis35->get_array();
					$data = array_change_key_case($data, CASE_LOWER);		
					$CHANGEID = $data[max_id] + 1;
				
					$cmd = " insert into DPIS_ORGCHANGE (CHANGEID, PERID, MOVEPOSITION, MOVEORG, ISUSER, CHANGETYPE, CHANGEDATE, PXUPDATE, USERNAME, FULLNAME, MD5)
									values ($CHANGEID, $PER_ID, NULL, $TMP_ORG_ID, 1, 'ADD', to_date('$UPDATE_DATE','yyyy-mm-dd hh24:mi:ss'), 0, '$username', '$user_name', '$passwd') ";
					$db_dpis35->send_cmd($cmd);
					//$db_dpis35->show_error();
				}

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ����������$PERSON_TITLE [ $PER_ID : $PER_NAME $PER_SURNAME ]");
				//echo "<script language='JavaScript'>";
				//echo "parent.clearAll_input();";  //echo "parent.refresh_opener('2<::>$PER_ID<::>$PER_TYPE<::>UPD=1<::>')";
				//echo "<script>";
				//�������ҷ���������ҧ		
				$PER_ID=''; 
				$UPD=''; 
				if($UPD1)	$UPD1='';
				//$PER_TYPE = "";
				$PER_STATUS = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$PN_CODE = "";
				 }else{ 
						$PN_NAME = "";
				 } 
				$PER_NAME = "";
				$PER_SURNAME = "";
				$PN_ENG_NAME = "";
				$PER_NICKNAME = "";
				$PER_ENG_NAME = "";
				$PER_ENG_SURNAME = "";
				$OT_NAME = "";
				$PER_CARDNO = "";
				$PER_OFFNO = "";
				$PER_OFFNO = "";
				$PER_TAXNO = "";
				$ES_CODE = "";
				$ES_NAME = "";
				$PER_BIRTHDATE = "";
				$PV_NAME_PER = "";
				$PV_CODE_PER = "";
				$PER_STARTDATE = "";
				$PER_OCCUPYDATE = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
					$LEVEL_NO = "";
				 } else { 
					$LEVEL_NAME = "";
				 } 
				 if (!$RPT_N) { $LEVEL_NO_SALARY = ""; } 
				$PER_DISABILITY = "";		$PER_DISABILITY = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$PER_BLOOD = "";
				 } else { 
						$BL_NAME = "";
				 } 
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$RE_CODE = "";
				 } else { 
						$RE_NAME = "";
				 } 
				$PER_START_ORG = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$MR_CODE = "";
				 } else { 
						$MR_NAME = "";
				 } 
				$MOV_CODE = "";
				$MOV_NAME = "";
				$PER_POSDATE = "";
				$PER_DOCDATE = "";
				$PER_DOCNO = "";
				$PER_ADD1 = "";
				$PER_ADD2 = "";
				$PER_HOME_TEL = "";
				$PER_OFFICE_TEL = "";
				$PER_FAX = "";
				$PER_MOBILE = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$PN_CODE_F = "";
				 } else { 
						$PN_FATHERNAME = "";
				 } 
				$PER_FATHERNAME = "";
				$PER_FATHERSURNAME = "";
				 if(($PAGE_AUTH["add"]=="Y" || $PAGE_AUTH["edit"]=="Y") && !$VIEW) { 
						$PN_CODE_M = "";
				 } else { 
						$PN_MOTHERNAME = "";
				 } 
				$PER_MOTHERNAME = "";
				$PER_MOTHERSURNAME = "";
				$SHOW_SPOUSE = "";
				$PER_CERT_OCC = "";
				$PER_AUDIT_ABSENT_FLAG = "";
				$PER_RENEW = "";
				$PER_PROBATION_FLAG = "";
				$PER_EMAIL = "";
				$PER_FILE_NO = "";
				$PER_BANK_ACCOUNT = "";
				$PER_COOPERATIVE_NO = "";
				$PER_MEMBERDATE = "";
				$PER_UNIONDATE = "";
				$PER_UNIONDATE1 = "";
				$PER_UNIONDATE2 = "";
				$PER_UNIONDATE3 = "";
				$PER_UNIONDATE4 = "";
				$PER_UNIONDATE5 = "";
				$PER_ORDAIN_DETAIL = "";
				$PER_COOPERATIVE = "";
				$PER_MEMBER = "";
				$PER_UNION = "";
				$PER_UNION2 = "";
				$PER_UNION3 = "";
				$PER_UNION4 = "";
				$PER_UNION5 = "";
				$PER_ORDAIN = "";
				$PER_SOLDIER = "";
				$PER_CONTACT_COUNT = "";
				$PER_CONTACT_PERSON = "";
				$PER_JOB = "";
				$PER_REMARK = "";
				$PER_BIRTH_PLACE = "";
				$PER_SCAR = "";
				$SHOW_UPDATE_USER = "";
				$SHOW_UPDATE_DATE = "";
				 if ($POSITION_NO_CHAR=="Y") {  $POS_NO_NAME = "";   } 
				$POS_NO = "";
				$POS_ID = "";
				$CHK_POS_ID = "";
				$SG_NAME = "";
				$SKILL_NAME = "";
				$SG_NAME1 = "";
				$SKILL_NAME1 = "";
				 if ($SESS_DEPARTMENT_NAME=="�����û���ͧ") { 
					$PAY_NO = "";
					$PAY_ID = "";
					$CHK_PAY_ID = "";
				 } 	
				$PER_SALARY = 0;
				$PER_ORGMGT = "";
				$ASS_ORG_ID = "";
				$ASS_ORG_NAME = "";
				$ASS_ORG_ID_1 = "";
				$ASS_ORG_NAME_1 = "";
				$ASS_ORG_ID_2 = "";
				$ASS_ORG_NAME_2 = ""; 
				 if($SESS_ORG_SETLEVEL== 3 || $SESS_ORG_SETLEVEL== 4 || $SESS_ORG_SETLEVEL==5) { 
						$ASS_ORG_ID_3 = "";
						$ASS_ORG_NAME_3 = ""; 
				 } 
				 if($SESS_ORG_SETLEVEL== 4 || $SESS_ORG_SETLEVEL==5) { 
						$ASS_ORG_ID_4 = "";
						$ASS_ORG_NAME_4 = ""; 
				 } 
				 if($SESS_ORG_SETLEVEL==5) { 
						$ASS_ORG_ID_5 = "";
						$ASS_ORG_NAME_5 = ""; 
				 } 
				$PER_MGTSALARY = 0;
				$PER_EXTRA_INCOME = 0;
				$PL_NAME = "";
				$PM_NAME = "";
				$CL_NAME = "";
				$PT_NAME = "";
				 if($PER_TYPE==2){ 
					$PG_NAME = "";
					$PG_NAME_SALARY = "";
				 } 
				$LV_NAME = "";
				$ORG_NAME_1 = "";
				$ORG_NAME_2 = "";
				 if($SESS_ORG_SETLEVEL== 3 || $SESS_ORG_SETLEVEL== 4 || $SESS_ORG_SETLEVEL==5) { 
					$ORG_NAME_3 = "";
				 } 
				 if($SESS_ORG_SETLEVEL== 4 || $SESS_ORG_SETLEVEL==5) { 
					$ORG_NAME_4 = "";
				 } 
				 if($SESS_ORG_SETLEVEL==5) { 
					$ORG_NAME_5 = "";
				 } 
				$CT_NAME_POS = "";
				$PV_NAME_POS = "";
				$AP_NAME_POS = "";
				$ORG_TYPE_NAME = "";
				 if($PER_ID && $ES_CODE && $ES_CODE != "02" && ($UPD || $VIEW)){ $ORG_NAME_WORK = ""; } 
			}/***else{
				$err_text = "�Ţ��Шӵ�ǻ�ЪҪ���� $PER_CARDNO";
			} // end if 
		} // end if***/

		//�������ҷ���������ҧ
		//�ѧ�������ö ADD ��	��������������ŷ�� set = NULL ��� set = "" ���������� input �դ���ʴ� NULL
		if($ASS_ORG_ID=="NULL") $ASS_ORG_ID = "";
		if($ASS_ORG_ID_1=="NULL") $ASS_ORG_ID_1 = "";
		if($ASS_ORG_ID_2=="NULL") $ASS_ORG_ID_2 = "";
		if($ASS_ORG_ID_3=="NULL") $ASS_ORG_ID_3 = "";
		if($ASS_ORG_ID_4=="NULL") $ASS_ORG_ID_4 = "";
		if($ASS_ORG_ID_5=="NULL") $ASS_ORG_ID_5 = "";			
		if($POS_ID=="NULL") $POS_ID = "";
		if($POEM_ID=="NULL") $POEM_ID = "";
		if($POEMS_ID=="NULL") $POEMS_ID = "";
		if($POT_ID=="NULL") $POT_ID = "";
		if($PN_CODE_M=="NULL") $PN_CODE_M = "";
		if($PN_CODE_F=="NULL") $PN_CODE_F = "";
		if($LEVEL_NO=="NULL") $LEVEL_NO = "";
		if($LEVEL_NO_SALARY=="NULL") $LEVEL_NO_SALARY = "";
		if($RE_CODE=="NULL") $RE_CODE = "";
		if($PV_CODE_PER=="NULL") $PV_CODE_PER = "";
		//--------------------------------------------------------------------------------¡��� PER_POS_DOCTYPE
		if($PER_CARDNO =="NULL") $PER_CARDNO =	"";
		if($PER_ORGMGT=="NULL" || $PER_ORGMGT=='0') $PER_ORGMGT="";
		if($PER_SALARY =='0')	$PER_SALARY ="";
		if($PER_MGTSALARY=="NULL" || $PER_MGTSALARY =='0') $PER_MGTSALARY="";
		if($PER_SPSALARY=="NULL" || $PER_SPSALARY == '0') $PER_SPSALARY="";
		if($PER_ORDAIN== 0)	$PER_ORDAIN="";
		if($PER_SOLDIER == 0)	$PER_SOLDIER ="";
		if($PER_MEMBER == 0)	$PER_MEMBER = "";
		if($PER_UNION == 0)	$PER_UNION ="";
		if($PER_UNION2 == 0)	$PER_UNION2 ="";
		if($PER_UNION3 == 0)	$PER_UNION3 ="";
		if($PER_UNION4 == 0)	$PER_UNION4 ="";
		if($PER_UNION5 == 0)	$PER_UNION5 ="";
		if($PER_COOPERATIVE == 0)	$PER_COOPERATIVE = "";
		if($PER_OFFNO =="NULL") $PER_OFFNO =	"";
		if($PER_TAXNO =="NULL") $PER_TAXNO =	"";
		if($PER_NICKNAME=="NULL") $PER_NICKNAME=	"";
		if($PER_HOME_TEL =="NULL") $PER_HOME_TEL =	"";
		if($PER_OFFICE_TEL =="NULL") $PER_OFFICE_TEL =	"";
		if($PER_FAX =="NULL") $PER_FAX =	"";
		if($PER_MOBILE =="NULL") $PER_MOBILE =	"";
		if($PER_EMAIL =="NULL") $PER_EMAIL =	"";
		if($PER_FILE_NO=="NULL") $PER_FILE_NO=	"";
		if($PER_BANK_ACCOUNT =="NULL") $PER_BANK_ACCOUNT =	"";
		if($PER_CONTACT_PERSON=="NULL") $PER_CONTACT_PERSON=	"";
		if($PER_REMARK =="NULL") $PER_REMARK =	"";
		if($PER_START_ORG =="NULL") $PER_START_ORG =	"";
		if($PER_COOPERATIVE_NO =="NULL") $PER_COOPERATIVE_NO =	"";
		if($PL_NAME_WORK=="NULL") $PL_NAME_WORK=	"";
		if($ORG_NAME_WORK =="NULL") $ORG_NAME_WORK =	"";
		if($PER_DOCNO=="NULL") $PER_DOCNO=	"";
		if($PER_POS_REASON=="NULL") $PER_POS_REASON=	"";
		if($PER_POS_YEAR=="NULL") $PER_POS_YEAR=	"";
		if($PER_POS_DOCNO =="NULL") $PER_POS_DOCNO =	"";  
		if($PER_POS_ORG =="NULL") $PER_POS_ORG =	"";
		if($PER_ORDAIN_DETAIL =="NULL") $PER_ORDAIN_DETAIL =	"";
		if($PER_POS_ORGMGT || $PER_POS_ORGMGT =="NULL") 	$PER_POS_ORGMGT =	"";
		if($PER_CONTACT_COUNT =="NULL") $PER_CONTACT_COUNT =	""; 
		if($PER_JOB =="NULL") $PER_JOB =	"";
		if($PAY_ID =="NULL") $PAY_ID =	""; 
		if($PER_BIRTH_PLACE =="NULL") $PER_BIRTH_PLACE =	"";
		if($PER_SCAR =="NULL") $PER_SCAR =	"";
		
		$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE, 1);
		$PER_STARTDATE = show_date_format($PER_STARTDATE, 1);
		$PER_OCCUPYDATE = show_date_format($PER_OCCUPYDATE, 1);
		$PER_POSDATE = show_date_format($PER_POSDATE, 1);
		$PER_MEMBERDATE = show_date_format($PER_MEMBERDATE, 1);
		$PER_UNIONDATE = show_date_format($PER_UNIONDATE, 1);
		$PER_UNIONDATE2 = show_date_format($PER_UNIONDATE2, 1);
		$PER_UNIONDATE3 = show_date_format($PER_UNIONDATE3, 1);
		$PER_UNIONDATE4 = show_date_format($PER_UNIONDATE4, 1);
		$PER_UNIONDATE5 = show_date_format($PER_UNIONDATE5, 1);
		$PER_DOCDATE = show_date_format($PER_DOCDATE, 1);
		$PER_EFFECTIVEDATE = show_date_format($PER_EFFECTIVEDATE, 1);

		$command = "SEARCH"; 
	}// end if ����Ҫ��͹��ʡ�ŷ���͡����ҫ���������

//	echo "2..command=$command LEVEL_NO=$LEVEL_NO<br>";
	if($command=="UPDATE" && $PER_ID){
		$cmd = " select PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OLD_PER_CARDNO = trim($data[PER_CARDNO]);
		if($PER_CARDNO != $OLD_PER_CARDNO){
			$cmd = " select PER_ID from PER_PERSONAL where trim(PER_CARDNO)='$PER_CARDNO' ";
			$count_person = $db_dpis->send_cmd($cmd);
		} // end if
		
		if(!$count_person){
			//MOV_CODE='$tmp_MOV_CODE', PER_STATUS=2, PER_POSDATE='$tmp_CMD_DATE'		
			if ($BKK_FLAG==1) {
				if ($PER_TYPE==1) {
					$cmd = " select DEPARTMENT_ID, ORG_ID from PER_POSITION where POS_ID=$POS_ID ";
				} elseif ($PER_TYPE==2) {
					$cmd = " select DEPARTMENT_ID, ORG_ID from PER_POS_EMP where POEM_ID=$POEM_ID ";
				} elseif ($PER_TYPE==3) {
					$cmd = " select DEPARTMENT_ID, ORG_ID from PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
				} elseif ($PER_TYPE==4) {
					$cmd = " select DEPARTMENT_ID, ORG_ID from PER_POS_TEMP where POT_ID=$POT_ID ";
				}
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$MAIN_DEPARTMENT_ID = $data_dpis1[DEPARTMENT_ID];
				$TMP_ORG_ID = $data_dpis1[ORG_ID];
			}
                        
			$cmd = " update PER_PERSONAL  set
									PER_TYPE = $PER_TYPE, 
									OT_CODE = '".trim($OT_CODE)."', 
									PN_CODE = '".trim($PN_CODE)."', 
									PER_NAME = '".trim($PER_NAME)."', 
									PER_SURNAME = '".trim($PER_SURNAME)."', 
									PER_ENG_NAME = '".trim($PER_ENG_NAME)."', 
									PER_ENG_SURNAME = '".trim($PER_ENG_SURNAME)."', 
									DEPARTMENT_ID_ASS = $ASS_DEPARTMENT_ID, 
									ORG_ID = $ASS_ORG_ID, 
									POS_ID = $POS_ID, 
									POEM_ID = $POEM_ID, 
									POEMS_ID = $POEMS_ID, 
									POT_ID = $POT_ID, 
									LEVEL_NO = $LEVEL_NO, 
									PER_ORGMGT = $PER_ORGMGT, 
									PER_SALARY = $PER_SALARY, 
									PER_MGTSALARY = $PER_MGTSALARY, 
									PER_SPSALARY = $PER_SPSALARY, 
									PER_GENDER = $PER_GENDER, 
									MR_CODE = '".trim($MR_CODE)."', 
									PER_CARDNO = '$PER_CARDNO', 
									PER_OFFNO = $PER_OFFNO, 
									PER_TAXNO = $PER_TAXNO, 
									PER_BLOOD = '".trim($PER_BLOOD)."', 
									RE_CODE = $RE_CODE, 
									PER_BIRTHDATE = '$PER_BIRTHDATE', 
									PER_RETIREDATE = '$PER_RETIREDATE', 
									PER_STARTDATE = '$PER_STARTDATE', 
									PER_OCCUPYDATE = '$PER_OCCUPYDATE', 
									PER_POSDATE = '$PER_POSDATE', 
									PN_CODE_F = $PN_CODE_F, 
									PER_FATHERNAME = '".trim($PER_FATHERNAME)."', 
									PER_FATHERSURNAME = '".trim($PER_FATHERSURNAME)."', 
									PN_CODE_M = $PN_CODE_M,
									PER_MOTHERNAME = '".trim($PER_MOTHERNAME)."', 
									PER_MOTHERSURNAME = '".trim($PER_MOTHERSURNAME)."', 
									PV_CODE = $PV_CODE_PER, 
									MOV_CODE = '".trim($MOV_CODE)."', 
									PER_ORDAIN = $PER_ORDAIN, 
									PER_SOLDIER = $PER_SOLDIER, 
									PER_MEMBER = $PER_MEMBER, 
									PER_STATUS = $PER_STATUS, 
									PER_HIP_FLAG = '$HIP_FLAG_TXT', 
									PER_CERT_OCC = '$PER_CERT_OCC', 
									PER_AUDIT_FLAG = '$PER_AUDIT_ABSENT_FLAG',
									PER_PROBATION_FLAG = '$PER_PROBATION_FLAG',
									DEPARTMENT_ID = $MAIN_DEPARTMENT_ID,
									LEVEL_NO_SALARY = $LEVEL_NO_SALARY, 
									PER_NICKNAME =	$PER_NICKNAME, 
									PER_HOME_TEL =	$PER_HOME_TEL, 
									PER_OFFICE_TEL =	$PER_OFFICE_TEL, 
									PER_FAX =	$PER_FAX, 
									PER_MOBILE =	$PER_MOBILE, 
									PER_EMAIL =	$PER_EMAIL, 
									PER_FILE_NO =	$PER_FILE_NO, 
									PER_BANK_ACCOUNT =	$PER_BANK_ACCOUNT, 
									PER_CONTACT_PERSON =	$PER_CONTACT_PERSON, 
									PER_REMARK =	$PER_REMARK, 
									PER_START_ORG =	$PER_START_ORG, 
									PER_COOPERATIVE =	$PER_COOPERATIVE, 
									PER_COOPERATIVE_NO =	$PER_COOPERATIVE_NO, 
									PER_MEMBERDATE =	'$PER_MEMBERDATE', 
									ES_CODE = '".trim($ES_CODE)."', 
									PAY_ID = $PAY_ID, 
									PL_NAME_WORK = $PL_NAME_WORK, 
									ORG_NAME_WORK = $ORG_NAME_WORK, 
									PER_DOCNO =	$PER_DOCNO, 
									PER_DOCDATE =	'$PER_DOCDATE', 
									PER_EFFECTIVEDATE =	'$PER_EFFECTIVEDATE', 
									PER_POS_REASON =	$PER_POS_REASON, 
									PER_POS_YEAR =	$PER_POS_YEAR, 
									PER_POS_DOCTYPE =	$PER_POS_DOCTYPE, 
									PER_POS_DOCNO =	$PER_POS_DOCNO, 
									PER_POS_ORG =	$PER_POS_ORG, 
									PER_ORDAIN_DETAIL =	$PER_ORDAIN_DETAIL, 
									PER_POS_ORGMGT =	$PER_POS_ORGMGT, 
									PER_CONTACT_COUNT =	$PER_CONTACT_COUNT, 
									PER_DISABILITY =	$PER_DISABILITY, 
									PER_UNION = $PER_UNION, 
									PER_UNIONDATE =	'$PER_UNIONDATE', 
									PER_UNION2 = $PER_UNION2, 
									PER_UNIONDATE2 =	'$PER_UNIONDATE2', 
									PER_UNION3 = $PER_UNION3, 
									PER_UNIONDATE3 =	'$PER_UNIONDATE3', 
									PER_UNION4 = $PER_UNION4, 
									PER_UNIONDATE4 =	'$PER_UNIONDATE4', 
									PER_UNION5 = $PER_UNION5, 
									PER_UNIONDATE5 =	'$PER_UNIONDATE5', 
									PER_JOB =	$PER_JOB, 
									PER_BIRTH_PLACE =	$PER_BIRTH_PLACE, 
									PER_SCAR =	$PER_SCAR, 
									PER_RENEW = $PER_RENEW,
									UPDATE_USER = $SESS_USERID, 
									UPDATE_DATE = '$UPDATE_DATE',
									ORG_ID_1 = $ASS_ORG_ID_1, 
									ORG_ID_2 = $ASS_ORG_ID_2, 
									ORG_ID_3 = $ASS_ORG_ID_3, 
									ORG_ID_4 = $ASS_ORG_ID_4, 
									ORG_ID_5 = $ASS_ORG_ID_5
							 where PER_ID=$PER_ID	  ";
			$db_dpis->send_cmd($cmd);
			
			
			// kittiphat 13/09/2561
			if($PER_STATUS==2){
				$cmdChk2 = "SELECT count(*)AS CNT FROM user_tables WHERE  TABLE_NAME = 'TA_REGISTERUSER'";
				$db_dpis->send_cmd($cmdChk2);
				$dataChk2 = $db_dpis->get_array();
				if($dataChk2[CNT]==1){
					
					$cmd = " update TA_REGISTERUSER  set
										ACTIVE_FLAG = 0, 
										UPDATE_DATE = SYSDATE
								 where PER_ID=$PER_ID	  ";
					$db_dpis->send_cmd($cmd);
			
				}
			}

			// End kittiphat 
			
			
                        //echo $cmd;
			//$db_dpis->show_error();
			//exit;
			//echo " update ===> <pre>$cmd<br>";
/*
			if ($PER_TYPE==1 || $PER_TYPE==5) {
				$cmd = " update PER_POSITION set POS_SALARY = $PER_SALARY	where POS_ID=$POS_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
*/
			/* cdgs */
			// $aaa=f_ins_psst_person($PER_ID);
			/*cdgs */
			if ($PER_TYPE==3) {
				 if ($PER_STATUS==1) 
					$cmd = " update PER_POS_EMPSER set PPS_CODE = 1	where POEMS_ID=$POEMS_ID ";
				 else
					$cmd = " update PER_POS_EMPSER set PPS_CODE = 2	where POEMS_ID=$POEMS_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
                        if($PER_TYPE==1){
                            /*http://dpis.ocsc.go.th/Service/node/1582 begin*/
                            if ($PER_STATUS==2){
                                $cmd = " update PER_POSITION set POS_VACANT_DATE = '$PER_POSDATE' where POS_ID=$POS_ID "; 
                            }else{
                                $cmd = " update PER_POSITION set POS_VACANT_DATE = NULL where POS_ID=$POS_ID "; 
                            }
                            $db_dpis->send_cmd($cmd);
                            /*http://dpis.ocsc.go.th/Service/node/1582 end*/
                            
                            $cmd = " update PER_POSITION set LEVEL_NO = $LEVEL_NO where POS_ID=$POS_ID ";
                            $db_dpis->send_cmd($cmd);
                            
                            
                            
                        }
                        if($PER_TYPE==2){
                           $cmd = " update PER_POS_EMP set LEVEL_NO = $LEVEL_NO where POEM_ID=$POEM_ID ";
				$db_dpis->send_cmd($cmd);
                            
                        }
			if ($BKK_FLAG==1 && $PER_STATUS==2) {
				$cmd = " select max(CHANGEID) as max_id from DPIS_ORGCHANGE ";
				$db_dpis35->send_cmd($cmd);
				$data = $db_dpis35->get_array();
				$data = array_change_key_case($data, CASE_LOWER);		
				$CHANGEID = $data[max_id] + 1;
			
				$cmd = " insert into DPIS_ORGCHANGE (CHANGEID, PERID, MOVEPOSITION, MOVEORG, ISUSER, CHANGETYPE, CHANGEDATE, PXUPDATE, USERNAME, FULLNAME, MD5)
								values ($CHANGEID, $PER_ID, NULL, $TMP_ORG_ID, 1, 'DELETE', to_date('$UPDATE_DATE','yyyy-mm-dd hh24:mi:ss'), 0, '$username', '$user_name', '$passwd') ";
				$db_dpis35->send_cmd($cmd);
				//$db_dpis35->show_error();
			}
		
			$command = "SEARCH";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��䢢�����$PERSON_TITLE [ $PER_ID : $PER_NAME $PER_SURNAME ]");
		}else{			
			$err_text = "�Ţ��Шӵ�ǻ�ЪҪ���� $PER_CARDNO";
			//��������������ŷ�� set = NULL ��� set = "" ���������� input �դ���ʴ� NULL
			if($ASS_ORG_ID=="NULL") $ASS_ORG_ID = "";
			if($ASS_ORG_ID_1=="NULL") $ASS_ORG_ID_1 = "";
			if($ASS_ORG_ID_2=="NULL") $ASS_ORG_ID_2 = "";
			if($ASS_ORG_ID_3=="NULL") $ASS_ORG_ID_3 = "";
			if($ASS_ORG_ID_4=="NULL") $ASS_ORG_ID_4 = "";
			if($ASS_ORG_ID_5=="NULL") $ASS_ORG_ID_5 = "";
			if($POS_ID=="NULL") $POS_ID = "";
			if($POEM_ID=="NULL") $POEM_ID = "";
			if($POEMS_ID=="NULL") $POEMS_ID = "";
			if($POT_ID=="NULL") $POT_ID = "";
			if($PN_CODE_M=="NULL") $PN_CODE_M = "";
			if($PN_CODE_F=="NULL") $PN_CODE_F = "";
			if($LEVEL_NO_SALARY=="NULL") $LEVEL_NO_SALARY = "";
			if($RE_CODE=="NULL") $RE_CODE = "";
			if($PV_CODE_PER=="NULL") $PV_CODE_PER = "";
			//--------------------------------------------------------------------------------¡��� PER_POS_DOCTYPE
			if($PER_CARDNO =="NULL") $PER_CARDNO =	"";
			if($PER_ORGMGT=="NULL" || $PER_ORGMGT=='0') $PER_ORGMGT="";
			if($PER_SALARY =='0')	$PER_SALARY ="";
			if($PER_MGTSALARY=="NULL" || $PER_MGTSALARY =='0') $PER_MGTSALARY="";
			if($PER_SPSALARY=="NULL" || $PER_SPSALARY == '0') $PER_SPSALARY="";
			if($PER_ORDAIN== 0)	$PER_ORDAIN="";
			if($PER_SOLDIER == 0)	$PER_SOLDIER ="";
			if($PER_MEMBER == 0)	$PER_MEMBER = "";
			if($PER_UNION == 0)	$PER_UNION ="";
			if($PER_UNION2 == 0)	$PER_UNION2 ="";
			if($PER_UNION3 == 0)	$PER_UNION3 ="";
			if($PER_UNION4 == 0)	$PER_UNION4 ="";
			if($PER_UNION5 == 0)	$PER_UNION5 ="";
			if($PER_COOPERATIVE == 0)	$PER_COOPERATIVE = "";
			if($PER_OFFNO =="NULL") $PER_OFFNO =	"";
			if($PER_TAXNO =="NULL") $PER_TAXNO =	"";
			if($PER_NICKNAME=="NULL") $PER_NICKNAME=	"";
			if($PER_HOME_TEL =="NULL") $PER_HOME_TEL =	"";
			if($PER_OFFICE_TEL =="NULL") $PER_OFFICE_TEL =	"";
			if($PER_FAX =="NULL") $PER_FAX =	"";
			if($PER_MOBILE =="NULL") $PER_MOBILE =	"";
			if($PER_EMAIL =="NULL") $PER_EMAIL =	"";
			if($PER_FILE_NO=="NULL") $PER_FILE_NO=	"";
			if($PER_BANK_ACCOUNT =="NULL") $PER_BANK_ACCOUNT =	"";
			if($PER_CONTACT_PERSON=="NULL") $PER_CONTACT_PERSON=	"";
			if($PER_REMARK =="NULL") $PER_REMARK =	"";
			if($PER_START_ORG =="NULL") $PER_START_ORG =	"";
			if($PER_COOPERATIVE_NO =="NULL") $PER_COOPERATIVE_NO =	"";
			if($PL_NAME_WORK=="NULL") $PL_NAME_WORK=	"";
			if($ORG_NAME_WORK =="NULL") $ORG_NAME_WORK =	"";
			if($PER_DOCNO=="NULL") $PER_DOCNO=	"";
			if($PER_POS_REASON=="NULL") $PER_POS_REASON=	"";
			if($PER_POS_YEAR=="NULL") $PER_POS_YEAR=	"";
			if($PER_POS_DOCNO =="NULL") $PER_POS_DOCNO =	"";  
			if($PER_POS_ORG =="NULL") $PER_POS_ORG =	"";
			if($PER_ORDAIN_DETAIL =="NULL") $PER_ORDAIN_DETAIL =	"";
			if($PER_POS_ORGMGT =="NULL") $PER_POS_ORGMGT =	"";
			if($PER_CONTACT_COUNT =="NULL") $PER_CONTACT_COUNT =	""; 
			if($PER_JOB =="NULL") $PER_JOB =	"";
			if($PAY_ID =="NULL") $PAY_ID =	""; 
			if($PER_BIRTH_PLACE =="NULL") $PER_BIRTH_PLACE =	"";
			if($PER_SCAR =="NULL") $PER_SCAR =	"";
			
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE, 1);
			$PER_STARTDATE = show_date_format($PER_STARTDATE, 1);
			$PER_OCCUPYDATE = show_date_format($PER_OCCUPYDATE, 1);
			$PER_POSDATE = show_date_format($PER_POSDATE, 1);
			$PER_MEMBERDATE = show_date_format($PER_MEMBERDATE, 1);
			$PER_UNIONDATE = show_date_format($PER_UNIONDATE, 1);
			$PER_UNIONDATE2 = show_date_format($PER_UNIONDATE2, 1);
			$PER_UNIONDATE3 = show_date_format($PER_UNIONDATE3, 1);
			$PER_UNIONDATE4 = show_date_format($PER_UNIONDATE4, 1);
			$PER_UNIONDATE5 = show_date_format($PER_UNIONDATE5, 1);
			$PER_DOCDATE = show_date_format($PER_DOCDATE, 1);
			$PER_EFFECTIVEDATE = show_date_format($PER_EFFECTIVEDATE, 1);
		} // end if
		if($_POST[MAIN_VIEW])	$MAIN_VIEW = $_POST[MAIN_VIEW];
	} // end if
	
//	echo "3..command=$command LEVEL_NO=$LEVEL_NO<br>";
	if($command=="DELETE" && $PER_ID){
		$cmd = " select  PER_NAME, PER_SURNAME, PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME_DEL = $data[PER_NAME];
		$PER_SURNAME_DEL = $data[PER_SURNAME];
		$PER_CARDNO_DEL = $data[PER_CARDNO];

// =======  ź table ����������� PER_ID =======		
		$cmd = " delete from PER_POSITIONHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SALARYHIS where PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_EXTRAHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_EDUCATE where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_TRAINING where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ABILITY where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_HEIR where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ABSENTHIS where PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PUNISHMENT where PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SERVICEHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_REWARDHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_MARRHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_NAMEHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_DECORATEHIS where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_TIMEHIS where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PERSONALPIC where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PER_COMDTL where PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_MOVE_REQ where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PROMOTE_C where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PROMOTE_P where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_PROMOTE_E where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SALPROMOTE where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_BONUSPROMOTE where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ABSENT where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_INVEST1DTL where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_INVEST2DTL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_SCHOLAR where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_COURSEDTL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_DECORDTL where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_LETTER where PER_ID=$PER_ID ";		
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

// ====================================		
		$cmd = " delete from PER_PERSONAL where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from USER_DETAIL where USERNAME = '$PER_CARDNO_DEL' ";
		$db->send_cmd($cmd);
		//$db->show_error();
				
		if ($BKK_FLAG==1) {
			$cmd = " select max(CHANGEID) as max_id from DPIS_ORGCHANGE ";
			$db_dpis35->send_cmd($cmd);
			$data = $db_dpis35->get_array();
			$data = array_change_key_case($data, CASE_LOWER);		
			$CHANGEID = $data[max_id] + 1;
		
			$cmd = " insert into DPIS_ORGCHANGE (CHANGEID, PERID, MOVEPOSITION, MOVEORG, ISUSER, CHANGETYPE, CHANGEDATE, PXUPDATE, USERNAME, FULLNAME, MD5)
							values ($CHANGEID, $PER_ID, NULL, $TMP_ORG_ID, 1, 'DELETE', to_date('$UPDATE_DATE','yyyy-mm-dd hh24:mi:ss'), 0, '$username', '$user_name', '$passwd') ";
			$db_dpis35->send_cmd($cmd);
			//$db_dpis35->show_error();
		}
		/*cdgs */
		$aaa=f_del_psst_person($PER_ID);
		/*cdgs */

		$command = "SEARCH";
		$PER_ID = "";		//��������
		$show_topic = 1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ź������$PERSON_TITLE [ $PER_ID : $PER_NAME_DEL $PER_SURNAME_DEL ]");		
	} // end if

//	echo "4.command=$command LEVEL_NO=$LEVEL_NO<br>";
	if($_GET[UPD]) $UPD=$_GET[UPD];
	if($_GET[VIEW]) $VIEW=$_GET[VIEW];
	//echo " $PER_ID && ( $UPD || $VIEW )";
	if($PER_ID && ($UPD || $VIEW) && $form1command!="personal"){
			
		$cmd = "	select	PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
									PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, 
									LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, 
									PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, PER_TAXNO, 
									PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
									PER_OCCUPYDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
									PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, 
									PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, PER_ORDAIN, PER_SOLDIER, 
									PER_MEMBER, PER_STATUS, PER_ORGMGT, PER_HIP_FLAG, PER_CERT_OCC, PER_AUDIT_FLAG, 
									PER_POSDATE, DEPARTMENT_ID, LEVEL_NO_SALARY, PER_NICKNAME, PER_HOME_TEL, 
									PER_OFFICE_TEL, PER_FAX, PER_MOBILE, PER_EMAIL, PER_FILE_NO, PER_BANK_ACCOUNT, 
									PER_CONTACT_PERSON, PER_REMARK, PER_START_ORG, PER_COOPERATIVE, 
									PER_COOPERATIVE_NO, PER_MEMBERDATE, PAY_ID, ES_CODE, PL_NAME_WORK, 
									ORG_NAME_WORK, PER_DOCNO, PER_DOCDATE, PER_EFFECTIVEDATE, 
									PER_POS_REASON, PER_POS_YEAR, PER_POS_DOCTYPE, PER_POS_DOCNO, 
									PER_POS_ORG, PER_ORDAIN_DETAIL, PER_POS_ORGMGT, PER_CONTACT_COUNT, 
									PER_DISABILITY, PER_UNION, PER_UNIONDATE, PER_UNION2, PER_UNIONDATE2, 
									PER_UNION3, PER_UNIONDATE3, PER_UNION4, PER_UNIONDATE4, PER_UNION5, PER_UNIONDATE5, 
									PER_JOB, UPDATE_USER, UPDATE_DATE, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, 
									PER_PROBATION_FLAG, DEPARTMENT_ID_ASS, PER_BIRTH_PLACE, PER_SCAR, PER_RENEW
						from		PER_PERSONAL 
						where	PER_ID=$PER_ID	";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		// echo  "<pre> ==========> ".($cmd);
		$data = $db_dpis->get_array();
		if($PAGE_AUTH["edit"]=="Y" && $_POST[PER_TYPE]){		//��Ƿ�����͡��䢨ҡ dropdown
			$PER_TYPE = trim($_POST[PER_TYPE]);
		}
		if(!$PER_TYPE)		$PER_TYPE = trim($data[PER_TYPE]);
		$PAY_ID = trim($data[PAY_ID]);

		$MAIN_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$MAIN_DEPARTMENT_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MAIN_DEPARTMENT_NAME = $data_dpis1[ORG_NAME];
		$MAIN_MINISTRY_ID = $data_dpis1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MAIN_MINISTRY_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MAIN_MINISTRY_NAME = $data_dpis1[ORG_NAME];

		$ASS_DEPARTMENT_ID = trim($data[DEPARTMENT_ID_ASS]);	
		$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_DEPARTMENT_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ASS_DEPARTMENT_NAME = trim($data_dpis1[ORG_NAME]);
		
		$OT_CODE = trim($data[OT_CODE]);
		$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$OT_NAME = trim($data_dpis1[OT_NAME]);
				
		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME, PN_ENG_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_NAME = trim($data_dpis1[PN_NAME]);
		$PN_ENG_NAME = trim($data_dpis1[PN_ENG_NAME]);
						
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);		
		$PER_ENG_NAME = trim($data[PER_ENG_NAME]);
		$PER_ENG_SURNAME = trim($data[PER_ENG_SURNAME]);
//		echo  "==========> $PER_ID : $PER_NAME $PER_SURNAME ";
		
		$ASS_ORG_ID = trim($data[ORG_ID]);	
		$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ASS_ORG_NAME = trim($data_dpis1[ORG_NAME]);
		if($ASS_ORG_ID=="NULL") $ASS_ORG_ID = "";
		if($ASS_ORG_NAME=="NULL") $ASS_ORG_NAME = "";
		//echo ">>>".$ASS_ORG_ID;

		$ASS_ORG_ID_1 = trim($data[ORG_ID_1]);	
		$ASS_ORG_NAME_1 = "";
		if ($ASS_ORG_ID_1) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID_1 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ASS_ORG_NAME_1 = trim($data_dpis1[ORG_NAME]);
		}
		
		$ASS_ORG_ID_2 = trim($data[ORG_ID_2]);	
		$ASS_ORG_NAME_2 = "";
		if ($ASS_ORG_ID_2) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID_2 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ASS_ORG_NAME_2 = trim($data_dpis1[ORG_NAME]);
		}
		
		$ASS_ORG_ID_3 = trim($data[ORG_ID_3]);	
		$ASS_ORG_NAME_3 = "";
		if ($ASS_ORG_ID_3) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID_3 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ASS_ORG_NAME_3 = trim($data_dpis1[ORG_NAME]);
		}
		
		$ASS_ORG_ID_4 = trim($data[ORG_ID_4]);	
		$ASS_ORG_NAME_4 = "";
		if ($ASS_ORG_ID_4) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID_4 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ASS_ORG_NAME_4 = trim($data_dpis1[ORG_NAME]);
		}
		
		$ASS_ORG_ID_5 = trim($data[ORG_ID_5]);	
		$ASS_ORG_NAME_5 = "";
		if ($ASS_ORG_ID_5) {
			$cmd = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$ASS_ORG_ID_5 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ASS_ORG_NAME_5 = trim($data_dpis1[ORG_NAME]);
		}

		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]); 
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		
		$cmd = " select LEVEL_NAME, POSITION_TYPE from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$LEVEL_NAME = trim($data_dpis1[LEVEL_NAME]);
		$LV_NAME = trim($data_dpis1[POSITION_TYPE]);
		if($PER_TYPE == 3){
		
		 
		}
 

		$PER_NICKNAME = trim($data[PER_NICKNAME]);
		$PER_HOME_TEL = trim($data[PER_HOME_TEL]);
		$PER_OFFICE_TEL = trim($data[PER_OFFICE_TEL]);
		$PER_FAX = trim($data[PER_FAX]);
		$PER_MOBILE = trim($data[PER_MOBILE]);
		$PER_EMAIL = trim($data[PER_EMAIL]);
		$PER_FILE_NO = trim($data[PER_FILE_NO]);
		$PER_BANK_ACCOUNT = trim($data[PER_BANK_ACCOUNT]);
		$PER_CONTACT_PERSON = trim($data[PER_CONTACT_PERSON]);
		$PER_REMARK = trim($data[PER_REMARK]);
		$PER_START_ORG = trim($data[PER_START_ORG]);
		$PER_COOPERATIVE_NO = trim($data[PER_COOPERATIVE_NO]);
		$PER_BIRTH_PLACE = trim($data[PER_BIRTH_PLACE]);
		$PER_SCAR = trim($data[PER_SCAR]);
		$PER_RENEW = trim($data[PER_RENEW]);
		
		$PER_ORGMGT = trim($data[PER_ORGMGT]);
		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_MGTSALARY = trim($data[PER_MGTSALARY]);
		$PER_SPSALARY = trim($data[PER_SPSALARY]);
		$TMP_PER_SPSALARY = $TMP_PER_MGTSALARY = $PER_TOTALSALARY = $EXH_SEQ = $EX_SEQ = "";
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
			$TMP_PER_SPSALARY .= $EXH_SEQ.". ".$EX_NAME." ".number_format($data2[EXH_AMT])."  �ҷ<br>";
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
				$TMP_PER_MGTSALARY .= $EX_SEQ.". ".$EX_NAME." ".number_format($data2[PMH_AMT])."  �ҷ<br>";
			}
			$PER_TOTALSALARY = number_format($PER_TOTALSALARY,2);
		}
//		$PER_MGTSALARY = $TMP_PER_MGTSALARY;
//		$PER_SPSALARY = $TMP_PER_SPSALARY;

		$PER_GENDER = trim($data[PER_GENDER]);
		$MR_CODE = trim($data[MR_CODE]);		
		$cmd = " select MR_NAME from PER_MARRIED where MR_CODE='$MR_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$MR_NAME = trim($data_dpis2[MR_NAME]);

//		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
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

$next_search_pic = 1;					
if($next_search_pic==1){
					if ($PIC_SHOW == '1') {		// ੾�з�� ��˹� �ʴ��ٻ�Ҿ ��ҹ��
						if($PIC_SERVER_ID > 0){
							if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip �ҡ��駤���к� C06				 �� \ 
								// �� # �ó� server ��� ����¹ # ����� \ ������㹡���Ѿ��Ŵ�ٻ
								$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
								$PIC_PATH = str_replace("#","'",$PIC_PATH);
								$PIC_PATH = addslashes($PIC_PATH);
								$PIC_PATH = str_replace("'","",$PIC_PATH);
							
								$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_img[] = $img_file;
								$arr_imgshow[] = 1;
								$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
							}else{  // other server
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
											//echo "1.".$http_server."/".$img_file."<br>";
											$fp = @fopen($http_server."/".$img_file, "r");
											if ($fp !== false) $img_file = $http_server."/".$img_file;
											else $img_file=$IMG_PATH."shadow.jpg";
											fclose($fp);
										} else {
					//						echo "2.".$img_file."<br>";
											$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
										}
									} // end db_dpis2
							}
						}else{ // localhost  $PIC_SERVER_ID == 0
							$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_imgshow[] = 1;
							$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
						}
					} else { // PIC_SHOW==1
						/*
					$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
					$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
					$arr_imgshow[] = 0;
						*/
					}
} // end if($next_search_pic==1)
			} // end while loop
		} else {
			//$img_file="";
			$img_file=$IMG_PATH."shadow.jpg";
		}

			//echo "($PIC_PATH) -> img_file=$img_file // $PIC_SERVER_ID<br>";

		$PER_CARDNO = trim($data[PER_CARDNO]); // �ҡ PER_PERSONAL
		$PER_CARDNO_OLD = trim($data[PER_CARDNO]); 

		$PER_OFFNO = trim($data[PER_OFFNO]);
		$PER_TAXNO = trim($data[PER_TAXNO]);	
		$PER_BLOOD = trim($data[PER_BLOOD]);
		$cmd = " select BL_NAME from PER_BLOOD where BL_CODE='$PER_BLOOD' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$BL_NAME = trim($data_dpis2[BL_NAME]);

		$RE_CODE = trim($data[RE_CODE]);
		$cmd = " select RE_NAME from PER_RELIGION where RE_CODE='$RE_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$RE_NAME = trim($data_dpis2[RE_NAME]);

		$PER_CERT_OCC = trim($data[PER_CERT_OCC]);
		$PER_AUDIT_ABSENT_FLAG = trim($data[PER_AUDIT_FLAG]);
		$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
		$PER_DOCNO = trim($data[PER_DOCNO]);
		$PER_POS_REASON = trim($data[PER_POS_REASON]);
		$PER_POS_YEAR = trim($data[PER_POS_YEAR]);
		$PER_POS_DOCTYPE = trim($data[PER_POS_DOCTYPE]);
		$PER_POS_DOCNO = trim($data[PER_POS_DOCNO]);
		$PER_POS_ORG = trim($data[PER_POS_ORG]);
		$PER_ORDAIN_DETAIL = trim($data[PER_ORDAIN_DETAIL]);
		//select ���ʹ֧ �����˵ص��˹�  �ҡ����ѵԵ��˹�᷹ �ͧ�����֧�ҡ��Ǥ�
		$cmd="SELECT * FROM PER_POSITIONHIS WHERE PER_ID = $PER_ID AND  POH_LAST_POSITION = 'Y'";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$POH_REMARK = trim($data_dpis2[POH_REMARK]);
		//$PER_POS_ORGMGT = $POH_REMARK; //�����˵ص���觢ͧ���
		if($POH_REMARK){// create by somsak http://dpis.ocsc.go.th/Service/node/2107
			$PER_POS_ORGMGT = $POH_REMARK;
		}else{
			$PER_POS_ORGMGT = trim($data[PER_POS_ORGMGT]);
		}	
		$PER_CONTACT_COUNT = trim($data[PER_CONTACT_COUNT]);
		$PER_DISABILITY = trim($data[PER_DISABILITY]);
		$PER_JOB = trim($data[PER_JOB]);

		$PER_HIP_FLAG = trim($data[PER_HIP_FLAG]);
		$tmp_hip_flag = explode("||", $PER_HIP_FLAG);
		for ($i=1; $i<count($tmp_hip_flag)-1; $i++) {
			if ($tmp_hip_flag[$i]==1)		$chk_hip1 = "checked";
			if ($tmp_hip_flag[$i]==2)		$chk_hip2 = "checked";
			if ($tmp_hip_flag[$i]==3)		$chk_hip3 = "checked";
			if ($tmp_hip_flag[$i]==4)		$chk_hip4 = "checked";
			if ($tmp_hip_flag[$i]==5)		$chk_hip5 = "checked";
			if ($tmp_hip_flag[$i]==6)		$chk_hip6 = "checked";
		}

		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], 1);
		$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE], 1);
		$PER_STARTDATE1 = show_date_format($data[PER_STARTDATE], 1);
		$PER_OCCUPYDATE1 = show_date_format($data[PER_OCCUPYDATE], 1);
		$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE], 1);
		$PER_POSDATE = show_date_format($data[PER_POSDATE], 1);
		$PER_MEMBERDATE = show_date_format($data[PER_MEMBERDATE], 1);
		$PER_UNIONDATE = show_date_format($data[PER_UNIONDATE], 1);
		$PER_UNIONDATE2 = show_date_format($data[PER_UNIONDATE2], 1);
		$PER_UNIONDATE3 = show_date_format($data[PER_UNIONDATE3], 1);
		$PER_UNIONDATE4 = show_date_format($data[PER_UNIONDATE4], 1);
		$PER_UNIONDATE5 = show_date_format($data[PER_UNIONDATE5], 1);
		$PER_DOCDATE = show_date_format($data[PER_DOCDATE], 1);
		$PER_EFFECTIVEDATE = show_date_format($data[PER_EFFECTIVEDATE], 1);

		$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
							from		PER_FAMILY
							where	PER_ID=$PER_ID and FML_TYPE=1 ";	
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		$data1 = $db_dpis1->get_array();		
		$PN_CODE_F = trim($data1[PN_CODE]);
		$PER_FATHERNAME = $data1[FML_NAME];
		$PER_FATHERSURNAME = $data1[FML_SURNAME];
		if (!$PER_FATHERNAME) {
			$PN_CODE_F = trim($data[PN_CODE_F]);
			$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
			$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
		}

		$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
							from		PER_FAMILY
							where	PER_ID=$PER_ID and FML_TYPE=2 ";	
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		$data1 = $db_dpis1->get_array();		
		$PN_CODE_M = trim($data1[PN_CODE]);
		$PER_MOTHERNAME = $data1[FML_NAME];
		$PER_MOTHERSURNAME = $data1[FML_SURNAME];
		if (!$PER_MOTHERNAME) {
			$PN_CODE_M = trim($data[PN_CODE_M]);		
			$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
			$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
		}
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE_F' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_FATHERNAME = trim($data_dpis1[PN_NAME]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE_M' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_MOTHERNAME = trim($data_dpis1[PN_NAME]);

		$cmd = " select ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT, DT_CODE, 
										AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE 
						from PER_ADDRESS where PER_ID=$PER_ID and ADR_TYPE=2 ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();

		$PER_ADD1 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
		$ADR_HOME_TEL = trim($data_dpis1[ADR_HOME_TEL]);
		$ADR_OFFICE_TEL = trim($data_dpis1[ADR_OFFICE_TEL]);
		$ADR_FAX = trim($data_dpis1[ADR_FAX]);
		$ADR_MOBILE = trim($data_dpis1[ADR_MOBILE]);
		$ADR_EMAIL = trim($data_dpis1[ADR_EMAIL]);
		$DT_CODE_ADR = trim($data_dpis1[DT_CODE]);
		$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$DT_NAME_ADR = trim($data_dpis2[DT_NAME]);
		if (!$DT_NAME_ADR) $DT_NAME_ADR = $data_dpis1[ADR_DISTRICT];
		
		$AP_CODE_ADR = trim($data_dpis1[AP_CODE]);
		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$AP_NAME_ADR = trim($data_dpis2[AP_NAME]);
				
		$PV_CODE_ADR = trim($data_dpis1[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PV_NAME_ADR = trim($data_dpis2[PV_NAME]);
				
		$PER_ADD1 = "";
		if($data_dpis1[ADR_VILLAGE]) $PER_ADD1 .= "�����ҹ".$data_dpis1[ADR_VILLAGE]." ";
		if($data_dpis1[ADR_BUILDING]) $PER_ADD1 .= "�Ҥ��".$data_dpis1[ADR_BUILDING]." ";
		if($data_dpis1[ADR_NO]) $PER_ADD1 .= "�Ţ��� ".$data_dpis1[ADR_NO]." ";
		if($data_dpis1[ADR_MOO]) $PER_ADD1 .= "�. ".$data_dpis1[ADR_MOO]." ";
		if($data_dpis1[ADR_SOI]) $PER_ADD1 .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_SOI]))." ";
		if($data_dpis1[ADR_ROAD]) $PER_ADD1 .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_ROAD]))." ";
		if($DT_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD1 .= "�ǧ".$DT_NAME_ADR." ";
			} else {
				$PER_ADD1 .= "�.".$DT_NAME_ADR." ";
			}
		}
		if($AP_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD1 .= "ࢵ".$AP_NAME_ADR." ";
			} else {
				$PER_ADD1 .= "�.".$AP_NAME_ADR." ";
			}
		}
		if($PV_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD1 .= $PV_NAME_ADR." ";
			} else {
				$PER_ADD1 .= "�.".$PV_NAME_ADR." ";
			}
		}
		if($data_dpis1[ADR_POSTCODE]) $PER_ADD1 .= $data_dpis1[ADR_POSTCODE]." ";
		if (!$PER_ADD1) $PER_ADD1 = trim($data[PER_ADD1]);
		
		
		
		
		//---------------------------------------------- xxxxxxxxxxxxxxxxxxxxxxxx--------------------------------------------------------------
		
		$cmd = " select ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT, DT_CODE, 
										AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE 
						from PER_ADDRESS where PER_ID=$PER_ID and ADR_TYPE=4 ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();

		$PER_ADD_OLD = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
		$ADR_HOME_TEL = trim($data_dpis1[ADR_HOME_TEL]);
		$ADR_OFFICE_TEL = trim($data_dpis1[ADR_OFFICE_TEL]);
		$ADR_FAX = trim($data_dpis1[ADR_FAX]);
		$ADR_MOBILE = trim($data_dpis1[ADR_MOBILE]);
		$ADR_EMAIL = trim($data_dpis1[ADR_EMAIL]);
		$DT_CODE_ADR = trim($data_dpis1[DT_CODE]);
		$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$DT_NAME_ADR = trim($data_dpis2[DT_NAME]);
		if (!$DT_NAME_ADR) $DT_NAME_ADR = $data_dpis1[ADR_DISTRICT];
		
		$AP_CODE_ADR = trim($data_dpis1[AP_CODE]);
		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$AP_NAME_ADR = trim($data_dpis2[AP_NAME]);
				
		$PV_CODE_ADR = trim($data_dpis1[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PV_NAME_ADR = trim($data_dpis2[PV_NAME]);
				
		$PER_ADD_OLD = "";
		if($data_dpis1[ADR_VILLAGE]) $PER_ADD_OLD .= "�����ҹ".$data_dpis1[ADR_VILLAGE]." ";
		if($data_dpis1[ADR_BUILDING]) $PER_ADD_OLD .= "�Ҥ��".$data_dpis1[ADR_BUILDING]." ";
		if($data_dpis1[ADR_NO]) $PER_ADD_OLD .= "�Ţ��� ".$data_dpis1[ADR_NO]." ";
		if($data_dpis1[ADR_MOO]) $PER_ADD_OLD .= "�. ".$data_dpis1[ADR_MOO]." ";
		if($data_dpis1[ADR_SOI]) $PER_ADD_OLD .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_SOI]))." ";
		if($data_dpis1[ADR_ROAD]) $PER_ADD_OLD .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_ROAD]))." ";
		if($DT_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD_OLD .= "�ǧ".$DT_NAME_ADR." ";
			} else {
				$PER_ADD_OLD .= "�.".$DT_NAME_ADR." ";
			}
		}
		if($AP_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD_OLD .= "ࢵ".$AP_NAME_ADR." ";
			} else {
				$PER_ADD_OLD .= "�.".$AP_NAME_ADR." ";
			}
		}
		if($PV_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD_OLD .= $PV_NAME_ADR." ";
			} else {
				$PER_ADD_OLD .= "�.".$PV_NAME_ADR." ";
			}
		}
		if($data_dpis1[ADR_POSTCODE]) $PER_ADD_OLD .= $data_dpis1[ADR_POSTCODE]." ";
		if (!$PER_ADD_OLD) $PER_ADD_OLD = trim($data[PER_ADD1]);
		//-----------------------------------------------xxxxxxxxxxxxxxxxxxxxxxxx--------------------------------------------------------------

		
		
		
		
		$cmd = " select ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT, DT_CODE, 
										AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE 
						from PER_ADDRESS where PER_ID=$PER_ID and ADR_TYPE=1 ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();

		$PER_ADD2 = $DT_CODE_ADR = $AP_CODE_ADR = $PV_CODE_ADR = "";
		if (!$ADR_HOME_TEL) $ADR_HOME_TEL = trim($data_dpis1[ADR_HOME_TEL]);
		if (!$ADR_OFFICE_TEL) $ADR_OFFICE_TEL = trim($data_dpis1[ADR_OFFICE_TEL]);
		if (!$ADR_FAX) $ADR_FAX = trim($data_dpis1[ADR_FAX]);
		if (!$ADR_MOBILE) $ADR_MOBILE = trim($data_dpis1[ADR_MOBILE]);
		if (!$ADR_EMAIL) $ADR_EMAIL = trim($data_dpis1[ADR_EMAIL]);
		$DT_CODE_ADR = trim($data_dpis1[DT_CODE]);
		$cmd = " select DT_NAME from PER_DISTRICT where trim(DT_CODE)='$DT_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$DT_NAME_ADR = trim($data_dpis2[DT_NAME]);
		if (!$DT_NAME_ADR) $DT_NAME_ADR = $data_dpis1[ADR_DISTRICT];
			
		$AP_CODE_ADR = trim($data_dpis1[AP_CODE]);
		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$AP_NAME_ADR = trim($data_dpis2[AP_NAME]);
				
		$PV_CODE_ADR = trim($data_dpis1[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PV_NAME_ADR = trim($data_dpis2[PV_NAME]);
				
		if($data_dpis1[ADR_VILLAGE]) $PER_ADD2 .= "�����ҹ".$data_dpis1[ADR_VILLAGE]." ";
		if($data_dpis1[ADR_BUILDING]) $PER_ADD2 .= "�Ҥ��".$data_dpis1[ADR_BUILDING]." ";
		if($data_dpis1[ADR_NO]) $PER_ADD2 .= "�Ţ��� ".$data_dpis1[ADR_NO]." ";
		if($data_dpis1[ADR_MOO]) $PER_ADD2 .= "�.".$data_dpis1[ADR_MOO]." ";
		if($data_dpis1[ADR_SOI]) $PER_ADD2 .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_SOI]))." ";
		if($data_dpis1[ADR_ROAD]) $PER_ADD2 .= "�.".str_replace("�.","",str_replace("���","",$data_dpis1[ADR_ROAD]))." ";
		if($DT_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD2 .= "�ǧ".$DT_NAME_ADR." ";
			} else {
				$PER_ADD2 .= "�.".$DT_NAME_ADR." ";
			}
		}
		if($AP_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD2 .= "ࢵ".$AP_NAME_ADR." ";
			} else {
				$PER_ADD2 .= "�.".$AP_NAME_ADR." ";
			}
		}
		if($PV_NAME_ADR) {
			if ($PV_CODE_ADR=="1000") {
				$PER_ADD2 .= $PV_NAME_ADR." ";
			} else {
				$PER_ADD2 .= "�.".$PV_NAME_ADR." ";
			}
		}
		if($data_dpis1[ADR_POSTCODE]) $PER_ADD2 .= $data_dpis1[ADR_POSTCODE]." ";
		if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);

		/*���*/
                //if (!$PER_HOME_TEL) $PER_HOME_TEL = $ADR_HOME_TEL;
                //if (!$PER_OFFICE_TEL) $PER_OFFICE_TEL =  $ADR_OFFICE_TEL;
                //if (!$PER_FAX) $PER_FAX = $ADR_FAX;
		//if (!$PER_MOBILE) $PER_MOBILE = $ADR_MOBILE;
		//if (!$PER_EMAIL) $PER_EMAIL = $ADR_EMAIL;
		/*���*/
                /*Release 5.1.0.6 Begin*/
                if (!$PER_HOME_TEL) $PER_HOME_TEL = substr ($ADR_HOME_TEL,0,50); 
                if (!$PER_OFFICE_TEL) $PER_OFFICE_TEL = substr ($ADR_OFFICE_TEL,0,30);
		if (!$PER_FAX) $PER_FAX = substr ($ADR_FAX,0,20);
		if (!$PER_MOBILE) $PER_MOBILE = substr ($ADR_MOBILE,0,20);
		if (!$PER_EMAIL) $PER_EMAIL = substr ($ADR_EMAIL,0,100);
                /*Release 5.1.0.6 End*/    
		$PV_CODE_PER = trim($data[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_PER' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PV_NAME_PER = trim($data_dpis1[PV_NAME]);
				
		$MOV_CODE = trim($data[MOV_CODE]);
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);

		$ES_CODE = trim($data[ES_CODE]);
		$cmd = " select ES_NAME from PER_EMP_STATUS where trim(ES_CODE)='$ES_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ES_NAME = trim($data_dpis1[ES_NAME]);
		if ($ES_CODE != "02") {
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
		}

		$chk_ordain = (trim($data[PER_ORDAIN]) == 1)?  "checked" : "";
		$chk_soldier = (trim($data[PER_SOLDIER]) == 1)? "checked" : "";
		$chk_member = (trim($data[PER_MEMBER]) == 1)? "checked" : "";
		$chk_union = (trim($data[PER_UNION]) == 1)? "checked" : "";
		$chk_union2 = (trim($data[PER_UNION2]) == 1)? "checked" : "";
		$chk_union3 = (trim($data[PER_UNION3]) == 1)? "checked" : "";
		$chk_union4 = (trim($data[PER_UNION4]) == 1)? "checked" : "";
		$chk_union5 = (trim($data[PER_UNION5]) == 1)? "checked" : "";
		$chk_cooperative = (trim($data[PER_COOPERATIVE]) == 1)? "checked" : "";

		$PER_STATUS = trim($data[PER_STATUS]);
		$chk_orgmgt = (trim($data[PER_ORGMGT]) == 1)? "checked" : "";	

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format($data[UPDATE_DATE], 1);

		// ===== select �����Ť������ =====
		$cmd = "	select MAH_NAME , PN_CODE from PER_MARRHIS where PER_ID=$PER_ID and trim(MR_CODE) = '2' order by MAH_SEQ desc ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
		$data_dpis2 = $db_dpis2->get_array();
		$MAH_NAME = $data_dpis2[MAH_NAME];	
		$PN_CODE2 = trim($data_dpis2[PN_CODE]);
		if($PN_CODE2){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE2' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_NAME2 = $data_dpis2[PN_NAME];
		} // end if
		$SHOW_SPOUSE = trim($PN_NAME2)." ".trim($MAH_NAME);
		
		if (!$SHOW_SPOUSE) {
			$cmd = "	select FML_NAME, FML_SURNAME from PER_FAMILY	where PER_ID=$PER_ID and FML_TYPE = 3 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SHOW_SPOUSE = trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
		}
			
		//  ===== ����繢���Ҫ��� SELECT �����ŵ��˹觨ҡ table PER_POSITION =====  PER_TYPE=1
		if ($PER_TYPE==1 || $PER_TYPE==5) {			
			$cmd = " select 	ORG_ID, POS_NO, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE, 
							CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_STATUS, POS_NO_NAME,FLAG_LEVEL 
					from 	PER_POSITION where POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			$TMP_ORG_ID_1 = trim($data_dpis2[ORG_ID_1]);
			$TMP_ORG_ID_2 = trim($data_dpis2[ORG_ID_2]); 
			$TMP_ORG_ID_3 = trim($data_dpis2[ORG_ID_3]);
			$TMP_ORG_ID_4 = trim($data_dpis2[ORG_ID_4]); 
			$TMP_ORG_ID_5 = trim($data_dpis2[ORG_ID_5]); 
			$POS_NO = trim($data_dpis2[POS_NO]);
			$POS_NO_NAME = trim($data_dpis2[POS_NO_NAME]);
                        $FLAG_LEVEL = trim($data_dpis2[FLAG_LEVEL]);

			$PM_CODE = trim($data_dpis2[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";			
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PM_NAME = trim($data_dpis1[PM_NAME]);
			
			$PL_CODE = trim($data_dpis2[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PL_NAME]);
			
			$CL_NAME = trim($data_dpis2[CL_NAME]);
			$CL_CODE = $CL_NAME;
			$POS_SALARY = number_format(trim($data[POS_SALARY]), 2, '.', ',');
			$POS_MGTSALARY = number_format(trim($data[POS_MGTSALARY]), 2, '.', ',');

			$SKILL_CODE = trim($data_dpis2[SKILL_CODE]);
			$cmd = " select SKILL_NAME, SG_CODE from PER_SKILL where trim(SKILL_CODE)='$SKILL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$SKILL_NAME = trim($data_dpis1[SKILL_NAME]);
			
			$SG_CODE = trim($data_dpis1[SG_CODE]);
			$cmd = " select SG_NAME from PER_SKILL_GROUP where trim(SG_CODE)='$SG_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();			
			$SG_NAME = trim($data_dpis1[SG_NAME]);
			
			$PT_CODE = trim($data_dpis2[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PT_NAME = trim($data_dpis1[PT_NAME]);
			
			$POS_STATUS = trim($data_dpis2[POS_STATUS]);
			
			if (!$RPT_N) {
				// ======== �Թ��Шӵ��˹觴֧�ҡ table PER_MGTSALARY ========= 
				$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' and		MS_ACTIVE=1 ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PER_MGTSALARY = number_format($data_dpis1[MS_SALARY], 2);
			}
		}
		if ($PAY_ID) {			
			$cmd = " select 	POS_NO 
					from 	PER_POSITION where POS_ID=$PAY_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PAY_NO = trim($data_dpis2[POS_NO]);
		}

		//  ===== ������١��ҧ��Ш� SELECT �����ŵ��˹觨ҡ table PER_POS_EMP =====  PER_TYPE=2
		if ($POEM_ID) {
			$cmd = " select 	ORG_ID, POEM_NO, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE, 
							POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS, PG_CODE_SALARY, POEM_NO_NAME
					from 	PER_POS_EMP where POEM_ID=$POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			$TMP_ORG_ID_1 = trim($data_dpis2[ORG_ID_1]);
			$TMP_ORG_ID_2 = trim($data_dpis2[ORG_ID_2]); 
			$TMP_ORG_ID_3 = trim($data_dpis2[ORG_ID_3]);
			$TMP_ORG_ID_4 = trim($data_dpis2[ORG_ID_4]); 
			$TMP_ORG_ID_5 = trim($data_dpis2[ORG_ID_5]); 
			$POEM_NO = trim($data_dpis2[POEM_NO]);
			$POS_ID = $POEM_ID;
			$POS_NO = trim($data_dpis2[POEM_NO]);
			$POS_NO_NAME = trim($data_dpis2[POEM_NO_NAME]);
			$PG_CODE_SALARY = trim($data_dpis2[PG_CODE_SALARY]);
			
			//  table  PER_POS_EMP = ���˹��١��ҧ��Ш�
			$PER_POS_CODE = trim($data_dpis2[PN_CODE]);
			$cmd = " select PN_NAME, PG_CODE from PER_POS_NAME where trim(PN_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PER_POS_NAME = trim($data_dpis1[PN_NAME]);
			$PL_NAME = trim($data_dpis1[PN_NAME]);
			$PG_CODE = trim($data_dpis1[PG_CODE]);
			
			$POEM_MIN_SALARY = trim($data_dpis1[POEM_MIN_SALARY]);
			$POEM_MAX_SALARY = trim($data_dpis1[POEM_MAX_SALARY]);	
			$POEM_STATUS = trim($data_dpis1[POEM_STATUS]);

			$cmd = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='$PG_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PG_NAME = $data_dpis2[PG_NAME];

			$cmd = " select PG_NAME_SALARY from PER_POS_GROUP where trim(PG_CODE)='$PG_CODE_SALARY' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PG_NAME_SALARY = $data_dpis2[PG_NAME_SALARY];
		}

		//  ===== ����繾�ѡ�ҹ�Ҫ��� SELECT �����ŵ��˹觨ҡ table PER_POS_EMPSER =====  PER_TYPE=3
		if ($POEMS_ID) {
			
			$cmd = " select 	ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE, 
							POEM_MIN_SALARY, POEM_MAX_SALARY, POEM_STATUS , POEMS_NO_NAME
					from 	PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			$TMP_ORG_ID_1 = trim($data_dpis2[ORG_ID_1]);
			$TMP_ORG_ID_2 = trim($data_dpis2[ORG_ID_2]); 
			$TMP_ORG_ID_3 = trim($data_dpis2[ORG_ID_3]);
			$TMP_ORG_ID_4 = trim($data_dpis2[ORG_ID_4]); 
			$TMP_ORG_ID_5 = trim($data_dpis2[ORG_ID_5]); 
			$POEMS_NO = trim($data_dpis2[POEMS_NO]);
			$POS_ID = $POEMS_ID;			
			$POS_NO = trim($data_dpis2[POEMS_NO]);
			$POS_NO_NAME = trim($data_dpis2[POEMS_NO_NAME]);
			//  table  PER_POS_EMP = ���˹觾�ѡ�ҹ�Ҫ���
			$PER_POS_CODE = trim($data_dpis2[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PER_POS_NAME = trim($data_dpis1[EP_NAME]);
			$PL_NAME = trim($data_dpis1[EP_NAME]);			
			
			$POEM_MIN_SALARY = trim($data_dpis2[POEM_MIN_SALARY]);
			$POEM_MAX_SALARY = trim($data_dpis2[POEM_MAX_SALARY]);	
			$POEM_STATUS = trim($data_dpis1[POEM_STATUS]);
			
			 
		}

		//  ===== ������١��ҧ���Ǥ��� SELECT �����ŵ��˹觨ҡ table PER_POS_TEMP =====  PER_TYPE=4
		if ($POT_ID) {
			$cmd = " select 	ORG_ID, POT_NO, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, TP_CODE, 
							POT_MIN_SALARY, POT_MAX_SALARY, POT_STATUS , POT_NO_NAME
					from 	PER_POS_TEMP where POT_ID=$POT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			$TMP_ORG_ID_1 = trim($data_dpis2[ORG_ID_1]);
			$TMP_ORG_ID_2 = trim($data_dpis2[ORG_ID_2]); 
			$TMP_ORG_ID_3 = trim($data_dpis2[ORG_ID_3]);
			$TMP_ORG_ID_4 = trim($data_dpis2[ORG_ID_4]); 
			$TMP_ORG_ID_5 = trim($data_dpis2[ORG_ID_5]); 
			$POT_NO = trim($data_dpis2[POT_NO]);
			$POS_ID = $POT_ID;
			$POS_NO = trim($data_dpis2[POT_NO]);
			$POS_NO_NAME = trim($data_dpis2[POT_NO_NAME]);
			
			//  table  PER_POS_TEMP = ���˹��١��ҧ���Ǥ���
			$PER_POS_CODE = trim($data_dpis2[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PER_POS_NAME = trim($data_dpis1[TP_NAME]);
			$PL_NAME = trim($data_dpis1[TP_NAME]);
			
			$POEM_MIN_SALARY = trim($data_dpis1[POT_MIN_SALARY]);
			$POEM_MAX_SALARY = trim($data_dpis1[POT_MAX_SALARY]);	
			$POEM_STATUS = trim($data_dpis1[POT_STATUS]);
		}

		$cmd = " select ORG_NAME, AP_CODE, PV_CODE, CT_CODE, OT_CODE, ORG_ID_REF from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);

		$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
		$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		$CT_CODE_POS = trim($data_dpis1[CT_CODE]);
		$ORG_TYPE_CODE = trim($data_dpis1[OT_CODE]); 
		if($TMP_ORG_ID) $DEPARTMENT_ID = $data_dpis1[ORG_ID_REF];
		
		if ($TMP_ORG_ID_1) {
			$cmd = " select ORG_NAME, AP_CODE, PV_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID_1 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ORG_NAME_1 = trim($data_dpis1[ORG_NAME]);
			$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
			$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		}
		
		if ($TMP_ORG_ID_2) {
			$cmd = " select ORG_NAME, AP_CODE, PV_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID_2 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ORG_NAME_2 = trim($data_dpis1[ORG_NAME]);
			$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
			$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		}
			
		if ($TMP_ORG_ID_3) {
			$cmd = " select ORG_NAME, AP_CODE, PV_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID_3 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ORG_NAME_3 = trim($data_dpis1[ORG_NAME]);
			$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
			$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		}
		
		if ($TMP_ORG_ID_4) {
			$cmd = " select ORG_NAME, AP_CODE, PV_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID_4 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ORG_NAME_4 = trim($data_dpis1[ORG_NAME]);
			$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
			$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		}
			
		if ($TMP_ORG_ID_5) {
			$cmd = " select ORG_NAME, AP_CODE, PV_CODE from PER_ORG where ORG_ID=$TMP_ORG_ID_5 ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ORG_NAME_5 = trim($data_dpis1[ORG_NAME]);
			$AP_CODE_POS = trim($data_dpis1[AP_CODE]);
			$PV_CODE_POS = trim($data_dpis1[PV_CODE]);
		}
		
		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_POS' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$AP_NAME_POS = trim($data_dpis1[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_POS' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PV_NAME_POS = trim($data_dpis1[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE_POS' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$CT_NAME_POS = trim($data_dpis1[CT_NAME]);
				
		// ===== �ѧ�Ѵ =====
		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_TYPE_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_TYPE_NAME = trim($data_dpis1[OT_NAME]);		

		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$DEPARTMENT_NAME = trim($data_dpis1[ORG_NAME]);
		$MINISTRY_ID = $data_dpis1[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MINISTRY_NAME = trim($data_dpis1[ORG_NAME]);

	} 	// 	if($PER_ID){
//	echo "5.command=$command LEVEL_NO=$LEVEL_NO<br>";

?>