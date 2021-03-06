<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	if(!$TR_GENDER) $TR_GENDER = 1;
	if(!$TR_PER_TYPE) $TR_PER_TYPE = 1;	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" || $command=="UPDATE"){
		$TR_BIRTH_DATE =  save_date($TR_BIRTH_DATE);
		$TR_STARTDATE =  save_date($TR_STARTDATE);
		$TR_POSDATE =  save_date($TR_POSDATE);
		$TR_DATE =  save_date($TR_DATE);
		$TR_BEGINDATE =  save_date($TR_BEGINDATE);
		
		$PL_CODE_1 = $PL_CODE_2 = $PL_CODE_3 = $PN_CODE_1 = $PN_CODE_2 = $PN_CODE_3 = "";
		if ($TR_PER_TYPE == 1) {
			$PL_CODE_1 = trim($PL_PN_CODE_1);
			$PL_CODE_2 = trim($PL_PN_CODE_2);
			$PL_CODE_3 = trim($PL_PN_CODE_3);
		} elseif ($TR_PER_TYPE == 2) {
			$PN_CODE_1 = trim($PL_PN_CODE_1);
			$PN_CODE_2 = trim($PL_PN_CODE_2);
			$PN_CODE_3 = trim($PL_PN_CODE_3);				
		}	// end if		
		
		$TR_SALARY = str_replace(",", "", $TR_SALARY) + 0;
		$PN_CODE = 	(trim($PN_CODE))? "'".trim($PN_CODE)."'" : "NULL";
		$EN_CODE = 	(trim($EN_CODE))? "'".trim($EN_CODE)."'" : "NULL";
		$EM_CODE = 	(trim($EM_CODE))? "'".trim($EM_CODE)."'" : "NULL";
		$INS_CODE = 	(trim($INS_CODE))? "'".trim($INS_CODE)."'" : "NULL";
		$PL_CODE_1 =	(trim($PL_CODE_1))? "'".trim($PL_CODE_1)."'" : "NULL";
		$PN_CODE_1 = 	(trim($PN_CODE_1))? "'".trim($PN_CODE_1)."'" : "NULL";
		$LEVEL_NO_1 = 	(trim($LEVEL_NO_1))? "'".trim($LEVEL_NO_1)."'" : "NULL";
		$PL_CODE_2 = 	(trim($PL_CODE_2))? "'".trim($PL_CODE_2)."'" : "NULL";
		$PN_CODE_2 = (trim($PN_CODE_2))? "'".trim($PN_CODE_2)."'" : "NULL";
		$LEVEL_NO_2 = (trim($LEVEL_NO_2))? "'".trim($LEVEL_NO_2)."'" : "NULL";
		$PL_CODE_3 = 	(trim($PL_CODE_3))? "'".trim($PL_CODE_3)."'" : "NULL";
		$PN_CODE_3 = (trim($PN_CODE_3))? "'".trim($PN_CODE_3)."'" : "NULL";
		$LEVEL_NO_3 =	(trim($LEVEL_NO_3))? "'".trim($LEVEL_NO_3)."'" : "NULL";
		$ORG_ID1 = 	(trim($ORG_ID1))?  trim($ORG_ID1) : "NULL";	
		$ORG_ID2 = 	(trim($ORG_ID2))?  trim($ORG_ID2) : "NULL";	
		$ORG_ID3 = 	(trim($ORG_ID3))?  trim($ORG_ID3) : "NULL";	
	} // end if
	
	if($command=="ADD"){
		$cmd = " select max(TR_ID) as max_id from PER_TRANSFER_REQ";
		$db_dpis->send_cmd($cmd);
		//echo $cmd;
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$TR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRANSFER_REQ (TR_ID, TR_PER_TYPE, TR_TYPE, TR_CARDNO, TR_GENDER, 
							PN_CODE, TR_NAME, TR_ADDRESS, TR_TEL, TR_BIRTH_DATE, EN_CODE, EM_CODE, INS_CODE, 
							TR_STARTDATE, TR_STARTPOS, TR_STARTLEVEL, TR_STARTORG1, TR_STARTORG2, 
							TR_STARTORG3, TR_POSITION, TR_LEVEL, TR_SALARY, TR_ORG1, TR_ORG2, TR_ORG3, 
							TR_ORG_TEL, TR_JOB, TR_POSDATE, PL_CODE_1, PN_CODE_1, ORG_ID_1, LEVEL_NO_1, 
							PL_CODE_2, PN_CODE_2, ORG_ID_2, LEVEL_NO_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, LEVEL_NO_3, 
							TR_REASON, TR_DATE, TR_BEGINDATE, TR_REMARK, TR_WAIT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE )
							values ($TR_ID, $TR_PER_TYPE, 2, '$TR_CARDNO', $TR_GENDER, $PN_CODE, '$TR_NAME', 
							 '$TR_ADDRESS', '$TR_TEL', '$TR_BIRTH_DATE', $EN_CODE, $EM_CODE, $INS_CODE, '$TR_STARTDATE', 
							'$TR_STARTPOS', '$TR_STARTLEVEL', '$TR_STARTORG1', '$TR_STARTORG2', '$TR_STARTORG3', 
							'$TR_POSITION', '$TR_LEVEL', $TR_SALARY, '$TR_ORG1', '$TR_ORG2', '$TR_ORG3', '$TR_ORG_TEL', '$TR_JOB', '$TR_POSDATE', 
							$PL_CODE_1, $PN_CODE_1, $ORG_ID1, $LEVEL_NO_1, $PL_CODE_2, $PN_CODE_2, $ORG_ID2, $LEVEL_NO_2, 
							$PL_CODE_3, $PN_CODE_3, $ORG_ID3, $LEVEL_NO_3, '$TR_REASON', '$TR_DATE', '$TR_BEGINDATE', 
							'$TR_REMARK', $TR_WAIT, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE' ) ";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error();
	//	echo "<br>$cmd<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������Ţ���Ҫ���/�١��ҧ���͹ [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");
	} // end if

	if($command=="UPDATE" && $TR_ID){
		$cmd = " update PER_TRANSFER_REQ set
								TR_PER_TYPE = $TR_PER_TYPE, 
								TR_TYPE = 2, 
								TR_CARDNO = '$TR_CARDNO', 
								TR_GENDER = $TR_GENDER, 
								PN_CODE = $PN_CODE, 
								TR_NAME = '$TR_NAME', 
							 	TR_ADDRESS = '$TR_ADDRESS', 
								TR_TEL = '$TR_TEL', 
								TR_BIRTH_DATE = '$TR_BIRTH_DATE', 
								EN_CODE = $EN_CODE, 
								EM_CODE = $EM_CODE, 
								INS_CODE = $INS_CODE, 
								TR_STARTDATE = '$TR_STARTDATE', 
								TR_STARTPOS = '$TR_STARTPOS', 
								TR_STARTLEVEL = '$TR_STARTLEVEL', 
								TR_STARTORG1 = '$TR_STARTORG1', 
								TR_STARTORG2 = '$TR_STARTORG2', 
								TR_STARTORG3 = '$TR_STARTORG3', 
								TR_POSITION = '$TR_POSITION', 
								TR_LEVEL = '$TR_LEVEL', 
								TR_SALARY = $TR_SALARY, 
								TR_ORG1 = '$TR_ORG1', 
								TR_ORG2 = '$TR_ORG2', 
								TR_ORG3 = '$TR_ORG3', 
								TR_ORG_TEL = '$TR_ORG_TEL', 
								TR_JOB = '$TR_JOB', 
								TR_POSDATE = '$TR_POSDATE',
								PL_CODE_1 = $PL_CODE_1, 
								PN_CODE_1 = $PN_CODE_1, 
								ORG_ID_1 = $ORG_ID1, 
								LEVEL_NO_1 = $LEVEL_NO_1, 
								PL_CODE_2 = $PL_CODE_2, 
								PN_CODE_2 = $PN_CODE_2, 
								ORG_ID_2 = $ORG_ID2, 
								LEVEL_NO_2 = $LEVEL_NO_2, 
								PL_CODE_3 = $PL_CODE_3, 
								PN_CODE_3 = $PN_CODE_3, 
								ORG_ID_3 = $ORG_ID3, 
								LEVEL_NO_3 = $LEVEL_NO_3, 
								TR_REASON = '$TR_REASON', 
								TR_DATE = '$TR_DATE', 
								TR_BEGINDATE = '$TR_BEGINDATE', 
								TR_REMARK = '$TR_REMARK', 
								TR_WAIT = $TR_WAIT,
								DEPARTMENT_ID = $DEPARTMENT_ID,
								UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where TR_ID=$TR_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��䢢����Ţ���Ҫ���/�١��ҧ���͹ [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");
	} // end if
	
	if($command=="DELETE" && $TR_ID){
		$cmd = " select  TR_NAME from PER_TRANSFER_REQ where TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_NAME = $data[TR_NAME];

		$cmd = " delete from PER_TRANSFER_REQ where TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ź�����Ţ���Ҫ���/�١��ҧ���͹ [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");	

		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::>')";
		echo "</script>";
	} // end if


	if($TR_ID){
		$cmd = "select	TR_ID, TR_PER_TYPE, TR_TYPE, TR_CARDNO, TR_GENDER, PN_CODE, TR_NAME, 
									TR_ADDRESS, TR_TEL, TR_BIRTH_DATE, EN_CODE, EM_CODE, INS_CODE, 
									TR_STARTDATE, TR_STARTPOS, TR_STARTLEVEL, TR_STARTORG1, TR_STARTORG2, 
									TR_STARTORG3, TR_POSITION, TR_LEVEL, TR_SALARY, TR_ORG1, TR_ORG2, TR_ORG3, 
									TR_ORG_TEL, TR_JOB, TR_POSDATE, PL_CODE_1, PN_CODE_1, ORG_ID_1, LEVEL_NO_1, 
									PL_CODE_2, PN_CODE_2, ORG_ID_2, LEVEL_NO_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, LEVEL_NO_3, 
									TR_REASON, TR_DATE, TR_BEGINDATE, TR_REMARK, TR_WAIT, UPDATE_USER, UPDATE_DATE 
						from	PER_TRANSFER_REQ
						where	TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "$cmd<br>";
		$data = $db_dpis->get_array();
		
		$TR_PER_TYPE = trim($data[TR_PER_TYPE]);
		$TR_TYPE = trim($data[TR_TYPE]);
		$TR_CARDNO = trim($data[TR_CARDNO]);
		$TR_GENDER = trim($data[TR_GENDER]); 
		$TR_NAME = trim($data[TR_NAME]);
		$TR_ADDRESS = trim($data[TR_ADDRESS]);
		$TR_TEL = trim($data[TR_TEL]);
		$TR_STARTPOS = trim($data[TR_STARTPOS]);
		$TR_STARTLEVEL = trim($data[TR_STARTLEVEL]);
		$TR_STARTORG1 = trim($data[TR_STARTORG1]);
		$TR_STARTORG2 = trim($data[TR_STARTORG2]);
		$TR_STARTORG3 = trim($data[TR_STARTORG3]);
		$TR_POSITION = trim($data[TR_POSITION]);
		$TR_LEVEL = trim($data[TR_LEVEL]);
		$TR_SALARY = trim($data[TR_SALARY]);
		$TR_ORG1 = trim($data[TR_ORG1]);
		$TR_ORG2 = trim($data[TR_ORG2]);
		$TR_ORG3 = trim($data[TR_ORG3]);
		$TR_ORG_TEL = trim($data[TR_ORG_TEL]);
		$TR_JOB = trim($data[TR_JOB]);
		$TR_REASON = trim($data[TR_REASON]);
		$TR_REMARK = trim($data[TR_REMARK]);
		$TR_WAIT = trim($data[TR_WAIT]);

		$TR_POSDATE = show_date_format($data[TR_POSDATE], 1);
		$TR_BIRTH_DATE = show_date_format($data[TR_BIRTH_DATE], 1);
		$TR_STARTDATE = show_date_format($data[TR_STARTDATE], 1);
		$TR_DATE = show_date_format($data[TR_DATE], 1);
		$TR_BEGINDATE = show_date_format($data[TR_BEGINDATE], 1);

		$PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_NAME = trim($data_dpis1[PN_NAME]);
				
		$EN_CODE = trim($data[EN_CODE]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EN_NAME = trim($data_dpis1[EN_NAME]);

		$EM_CODE = trim($data[EM_CODE]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EM_NAME = trim($data_dpis1[EM_NAME]);

		$INS_CODE = trim($data[INS_CODE]);
		$cmd = " select INS_NAME from PER_INSTITUTE where trim(INS_CODE)='$INS_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$INS_NAME = trim($data_dpis1[INS_NAME]);

		$ORG_ID1 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
		$ORG_ID2 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;
		$ORG_ID3 = (trim($data[ORG_ID_3]))? trim($data[ORG_ID_3]) : 0;
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID IN ($ORG_ID1, $ORG_ID2, $ORG_ID3) ";
		$db_dpis1->send_cmd($cmd);
		while ( $data_dpis1 = $db_dpis1->get_array() ) {
			$temp_id = trim($data_dpis1[ORG_ID]);
			$ORG_NAME_1 = ($temp_id == $ORG_ID1)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_1;
			$ORG_NAME_2 = ($temp_id == $ORG_ID2)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_2;
			$ORG_NAME_3 = ($temp_id == $ORG_ID3)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_3;						
		}

		if($CTRL_TYPE != 4 && $ORG_ID1){
//			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID1 ";
//			$db_dpis1->send_cmd($cmd);
//			$data1 = $db_dpis1->get_array();
//			$DEPARTMENT_ID = $data1[ORG_ID_REF];

			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$DEPARTMENT_NAME = $data1[ORG_NAME];

			if($CTRL_TYPE != 3){ 
				$MINISTRY_ID = $data1[ORG_ID_REF];

				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$MINISTRY_NAME = $data1[ORG_NAME];
			} // end if
		} // end if		
										
		$LEVEL_NO_1 = trim($data[LEVEL_NO_1]);
		$LEVEL_NO_2 = trim($data[LEVEL_NO_2]);
		$LEVEL_NO_3 = trim($data[LEVEL_NO_3]);

		//  ===== ����繢���Ҫ��� SELECT �����ŵ��˹觨ҡ table PER_LINE =====  TR_PER_TYPE=1
		if ($TR_PER_TYPE == 1) {			
			$PL_PN_CODE_1 = trim($data[PL_CODE_1]);
			$PL_PN_CODE_2 = trim($data[PL_CODE_2]);
			$PL_PN_CODE_3 = trim($data[PL_CODE_3]);		
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) IN ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3') ";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[PL_CODE]);
				$PL_PN_NAME_1 = ($temp_id == $PL_PN_CODE_1)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_1;
				$PL_PN_NAME_2 = ($temp_id == $PL_PN_CODE_2)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_2;
				$PL_PN_NAME_3 = ($temp_id == $PL_PN_CODE_3)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_3;	
			}								
		}

		//  ===== ������١��ҧ��Ш� SELECT �����ŵ��˹觨ҡ table PER_POS_NAME =====  TR_PER_TYPE=2
		if ($TR_PER_TYPE == 2) {
			$PL_PN_CODE_1 = trim($data[PN_CODE_1]);		
			$PL_PN_CODE_2 = trim($data[PN_CODE_2]); 
			$PL_PN_CODE_3 = trim($data[PN_CODE_3]);	
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) IN ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[PN_CODE]);
				$PL_PN_NAME_1 = ($temp_id == $PL_PN_CODE_1)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_1;
				$PL_PN_NAME_2 = ($temp_id == $PL_PN_CODE_2)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_2;
				$PL_PN_NAME_3 = ($temp_id == $PL_PN_CODE_3)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_3;	
			}	
		}
	} 	// 	if($TR_ID){
	
?>