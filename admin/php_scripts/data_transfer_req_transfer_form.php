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
		if($TR_BIRTH_DATE)	$TR_BIRTH_DATE =  save_date($TR_BIRTH_DATE);
		if($TR_STARTDATE)	$TR_STARTDATE =  save_date($TR_STARTDATE);
		if($TR_DATE)	$TR_DATE =  save_date($TR_DATE);
		if($TR_BEGINDATE)	$TR_BEGINDATE =  save_date($TR_BEGINDATE);
		
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
		$PN_CODE = 		(trim($PN_CODE))? 	"'".trim($PN_CODE)."'" : "NULL";
		$EN_CODE = 		(trim($EN_CODE))? 	"'".trim($EN_CODE)."'" : "NULL";
		$EM_CODE = 		(trim($EM_CODE))? 	"'".trim($EM_CODE)."'" : "NULL";
		$INS_CODE = 	(trim($INS_CODE))? 	"'".trim($INS_CODE)."'" : "NULL";
		$PL_CODE_1 = 	(trim($PL_CODE_1))? 	"'".trim($PL_CODE_1)."'" : "NULL";
		$PN_CODE_1 = 	(trim($PN_CODE_1))? 	"'".trim($PN_CODE_1)."'" : "NULL";
		$LEVEL_NO_1 = 	(trim($LEVEL_NO_1))? 	"'".trim($LEVEL_NO_1)."'" : "NULL";
		$PL_CODE_2 = 	(trim($PL_CODE_2))? 	"'".trim($PL_CODE_2)."'" : "NULL";
		$PN_CODE_2 = 	(trim($PN_CODE_2))? 	"'".trim($PN_CODE_2)."'" : "NULL";
		$LEVEL_NO_2 = 	(trim($LEVEL_NO_2))? 	"'".trim($LEVEL_NO_2)."'" : "NULL";
		$PL_CODE_3 = 	(trim($PL_CODE_3))? 	"'".trim($PL_CODE_3)."'" : "NULL";
		$PN_CODE_3 = 	(trim($PN_CODE_3))? 	"'".trim($PN_CODE_3)."'" : "NULL";
		$LEVEL_NO_3 =	(trim($LEVEL_NO_3))? 	"'".trim($LEVEL_NO_3)."'" : "NULL";
		$ORG_ID1 = 	(trim($ORG_ID1))?  	trim($ORG_ID1) : "NULL";	
		$ORG_ID2 = 	(trim($ORG_ID2))?  	trim($ORG_ID2) : "NULL";	
		$ORG_ID3 = 	(trim($ORG_ID3))?  	trim($ORG_ID3) : "NULL";	
	} // end if
	
	if($command=="ADD"){
		$cmd = " select max(TR_ID) as max_id from PER_TRANSFER_REQ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$TR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRANSFER_REQ (TR_ID, TR_PER_TYPE, TR_TYPE, TR_CARDNO, TR_GENDER, 
							PN_CODE, TR_NAME, TR_ADDRESS, TR_TEL, TR_BIRTH_DATE, EN_CODE, EM_CODE, INS_CODE, 
							TR_STARTDATE, TR_STARTPOS, TR_STARTLEVEL, TR_STARTORG1, TR_STARTORG2, 
							TR_STARTORG3, TR_POSITION, TR_LEVEL, TR_SALARY, TR_ORG1, TR_ORG2, TR_ORG3, 
							TR_ORG_TEL, TR_JOB, PL_CODE_1, PN_CODE_1, ORG_ID_1, LEVEL_NO_1, 
							PL_CODE_2, PN_CODE_2, ORG_ID_2, LEVEL_NO_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, LEVEL_NO_3, 
							TR_REASON, TR_DATE, TR_BEGINDATE, TR_REMARK, TR_WAIT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE )
						values ($TR_ID, $TR_PER_TYPE, 1, '$TR_CARDNO', $TR_GENDER, $PN_CODE, '$TR_NAME', 
							'$TR_ADDRESS', '$TR_TEL', '$TR_BIRTH_DATE', $EN_CODE, $EM_CODE, $INS_CODE, '$TR_STARTDATE', 
							'$TR_STARTPOS', '$TR_STARTLEVEL', '$TR_STARTORG1', '$TR_STARTORG2', '$TR_STARTORG3', 
							'$TR_POSITION', '$TR_LEVEL', $TR_SALARY, '$TR_ORG1', '$TR_ORG2', '$TR_ORG3', '$TR_ORG_TEL', '$TR_JOB', 
							$PL_CODE_1, $PN_CODE_1, $ORG_ID1, $LEVEL_NO_1, $PL_CODE_2, $PN_CODE_2, $ORG_ID2, $LEVEL_NO_2, 
							$PL_CODE_3, $PN_CODE_3, $ORG_ID3, $LEVEL_NO_3, '$TR_REASON', '$TR_DATE', '$TR_BEGINDATE', 
							'$TR_REMARK', NULL, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
		//echo $cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลข้าราชการ/ลูกจ้างขอโอน [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");
		$TR_ID = "";
	} // end if

	if($command=="UPDATE" && $TR_ID){
		$cmd = " update PER_TRANSFER_REQ set
								TR_PER_TYPE = $TR_PER_TYPE, 
								TR_TYPE = 1, 
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
								TR_WAIT = NULL,
								DEPARTMENT_ID = $DEPARTMENT_ID,
								UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where TR_ID=$TR_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//echo "--> $cmd";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ/ลูกจ้างขอโอน [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");
	} // end if
	
	if($command=="DELETE" && $TR_ID){
		$cmd = " select  TR_NAME from PER_TRANSFER_REQ where TR_ID=$TR_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_NAME = $data[TR_NAME];

		$cmd = " delete from PER_TRANSFER_REQ where TR_ID=$TR_ID ";	
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลข้าราชการ/ลูกจ้างขอโอน [ $DEPARTMENT_ID : $TR_ID : $TR_NAME ]");	
/*
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::>')";
		echo "</script>";
*/
		$TR_ID="";
	} // end if

	if($TR_ID){
		$cmd = "	select	TR_ID, TR_PER_TYPE, TR_TYPE, TR_CARDNO, TR_GENDER, PN_CODE, TR_NAME, 
									TR_ADDRESS, TR_TEL, TR_BIRTH_DATE, EN_CODE, EM_CODE, INS_CODE, 
									TR_STARTDATE, TR_STARTPOS, TR_STARTLEVEL, TR_STARTORG1, TR_STARTORG2, 
									TR_STARTORG3, TR_POSITION, TR_LEVEL, TR_SALARY, TR_ORG1, TR_ORG2, TR_ORG3, 
									TR_ORG_TEL, TR_JOB, PL_CODE_1, PN_CODE_1, ORG_ID_1, LEVEL_NO_1, 
									PL_CODE_2, PN_CODE_2, ORG_ID_2, LEVEL_NO_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, LEVEL_NO_3, 
									TR_REASON, TR_DATE, TR_BEGINDATE, TR_REMARK, TR_WAIT, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE
						from		PER_TRANSFER_REQ
						where	TR_ID=$TR_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "~~~ $cmd (command=$command)<br>";
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

		$TR_BIRTH_DATE = $data[TR_BIRTH_DATE];
		$TR_BIRTH_DATE = show_date_format($TR_BIRTH_DATE, 1);
		// echo save_date($_POST[TR_BIRTH_DATE])."---> $TR_BIRTH_DATE / $_POST[TR_BIRTH_DATE] / $data[TR_BIRTH_DATE]";
		 
		$TR_STARTDATE = $data[TR_STARTDATE];
		$TR_STARTDATE = show_date_format($TR_STARTDATE, 1);
		
		$TR_DATE = $data[TR_DATE];
		$TR_DATE = show_date_format($TR_DATE, 1);
		
		$TR_BEGINDATE = $data[TR_BEGINDATE];
		$TR_BEGINDATE = show_date_format($TR_BEGINDATE, 1);
		
		$PN_CODE = trim($data[PN_CODE]);
		$EN_CODE = trim($data[EN_CODE]);
		$EM_CODE = trim($data[EM_CODE]);
		$INS_CODE = trim($data[INS_CODE]);
		
		$LEVEL_NO_1 = trim($data[LEVEL_NO_1]);
		$LEVEL_NO_2 = trim($data[LEVEL_NO_2]);
		$LEVEL_NO_3 = trim($data[LEVEL_NO_3]);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		//$PN_NAME = $EN_NAME = $EM_CODE = $INS_CODE = $ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = ""; /*เดิม*/
                $PN_NAME = $EN_NAME   = $ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_NAME = trim($data_dpis1[PN_NAME]);
				
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EN_NAME = trim($data_dpis1[EN_NAME]);

		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EM_NAME = trim($data_dpis1[EM_NAME]);

		$cmd = " select INS_NAME from PER_INSTITUTE where trim(INS_CODE)='$INS_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$INS_NAME = trim($data_dpis1[INS_NAME]);

		$DTLFORM_ORG_ID1 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
		$DTLFORM_ORG_ID2 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;		
		$DTLFORM_ORG_ID3 = (trim($data[ORG_ID_3]))? trim($data[ORG_ID_3]) : 0;
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID IN ($DTLFORM_ORG_ID1, $DTLFORM_ORG_ID2, $DTLFORM_ORG_ID3) ";
		$db_dpis1->send_cmd($cmd);
		while ( $data_dpis1 = $db_dpis1->get_array() ) {
			$temp_id = trim($data_dpis1[ORG_ID]);
			$ORG_NAME_1 = ($temp_id == $DTLFORM_ORG_ID1)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_1;
			$ORG_NAME_2 = ($temp_id == $DTLFORM_ORG_ID2)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_2;
			$ORG_NAME_3 = ($temp_id == $DTLFORM_ORG_ID3)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_3;						
		}
		//echo ">><<<  $DTLFORM_ORG_ID1 - $DTLFORM_ORG_ID2 - $DTLFORM_ORG_ID3 <hr><hr>";

		if($CTRL_TYPE != 4 && $DTLFORM_ORG_ID1){
//			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DTLFORM_ORG_ID1 ";
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

		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
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

		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
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
/*
		// POST คือ ค่าที่แก้ไขใหม่ เพื่อไม่ให้มันแสดงค่าเดิมจาก database แล้วไม่เปลี่ยนเป็นค่าใหม่ที่เราแก้ไข
		if($command!='UPDATE' && !$_POST[TR_PER_TYPE])	$TR_PER_TYPE = trim($data[TR_PER_TYPE]);
		if($command!='UPDATE' && !$_POST[TR_TYPE])	 $TR_TYPE = trim($data[TR_TYPE]);
		if($command!='UPDATE' && !$_POST[TR_CARDNO]) $TR_CARDNO = trim($data[TR_CARDNO]);
		if($command!='UPDATE' && !$_POST[TR_GENDER]) $TR_GENDER = trim($data[TR_GENDER]); 
		if($command!='UPDATE' && !$_POST[TR_NAME]) $TR_NAME = trim($data[TR_NAME]);
		if($command!='UPDATE' && !$_POST[TR_ADDRESS]) $TR_ADDRESS = trim($data[TR_ADDRESS]);
		if($command!='UPDATE' && !$_POST[TR_TEL]) $TR_TEL = trim($data[TR_TEL]);
		if($command!='UPDATE' && !$_POST[TR_STARTPOS]) $TR_STARTPOS = trim($data[TR_STARTPOS]);
		if($command!='UPDATE' && !$_POST[TR_STARTLEVEL]) $TR_STARTLEVEL = trim($data[TR_STARTLEVEL]);
		if($command!='UPDATE' && !$_POST[TR_STARTORG1]) $TR_STARTORG1 = trim($data[TR_STARTORG1]);
		if($command!='UPDATE' && !$_POST[TR_STARTORG2]) $TR_STARTORG2 = trim($data[TR_STARTORG2]);
		if($command!='UPDATE' && !$_POST[TR_STARTORG3]) $TR_STARTORG3 = trim($data[TR_STARTORG3]);
		if($command!='UPDATE' && !$_POST[TR_POSITION]) $TR_POSITION = trim($data[TR_POSITION]);
		if($command!='UPDATE' && !$_POST[TR_LEVEL]) $TR_LEVEL = trim($data[TR_LEVEL]);
		if($command!='UPDATE' && !$_POST[TR_SALARY]) $TR_SALARY = trim($data[TR_SALARY]);
		if($command!='UPDATE' && !$_POST[TR_ORG1]) $TR_ORG1 = trim($data[TR_ORG1]);
		if($command!='UPDATE' && !$_POST[TR_ORG2]) $TR_ORG2 = trim($data[TR_ORG2]);
		if($command!='UPDATE' && !$_POST[TR_ORG3]) $TR_ORG3 = trim($data[TR_ORG3]);
		if($command!='UPDATE' && !$_POST[TR_ORG_TEL]) $TR_ORG_TEL = trim($data[TR_ORG_TEL]);
		if($command!='UPDATE' && !$_POST[TR_JOB]) $TR_JOB = trim($data[TR_JOB]);
		if($command!='UPDATE' && !$_POST[TR_REASON]) $TR_REASON = trim($data[TR_REASON]);
		if($command!='UPDATE' && !$_POST[TR_REMARK]) $TR_REMARK = trim($data[TR_REMARK]);

		if($command!='UPDATE' && !$_POST[TR_BIRTH_DATE])	$TR_BIRTH_DATE = $data[TR_BIRTH_DATE];
		else		$TR_BIRTH_DATE = save_date($_POST[TR_BIRTH_DATE]);
		 $TR_BIRTH_DATE = show_date_format($TR_BIRTH_DATE, 1);
		// echo save_date($_POST[TR_BIRTH_DATE])."---> $TR_BIRTH_DATE / $_POST[TR_BIRTH_DATE] / $data[TR_BIRTH_DATE]";
		 
		if($command!='UPDATE' && !$_POST[TR_STARTDATE]) 	$TR_STARTDATE = $data[TR_STARTDATE];
		else		$TR_STARTDATE = save_date($_POST[TR_STARTDATE]);
		$TR_STARTDATE = show_date_format($TR_STARTDATE, 1);
		
		if($command!='UPDATE' && !$_POST[TR_DATE]) $TR_DATE = $data[TR_DATE];
		else		$TR_DATE = save_date($_POST[TR_DATE]);
		$TR_DATE = show_date_format($TR_DATE, 1);
		
		if($command!='UPDATE' && !$_POST[TR_BEGINDATE]) $TR_BEGINDATE = $data[TR_BEGINDATE];
		else		$TR_BEGINDATE = save_date($_POST[TR_BEGINDATE]);
		$TR_BEGINDATE = show_date_format($TR_BEGINDATE, 1);

		$PN_NAME = $EN_NAME = $EM_CODE = $INS_CODE = $ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
		if($command!='UPDATE' && !$_POST[PN_CODE]) $PN_CODE = trim($data[PN_CODE]);
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PN_NAME = trim($data_dpis1[PN_NAME]);
				
		if($command!='UPDATE' && !$_POST[EN_CODE]) $EN_CODE = trim($data[EN_CODE]);
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EN_NAME = trim($data_dpis1[EN_NAME]);

		if($command!='UPDATE' && !$_POST[EM_CODE]) $EM_CODE = trim($data[EM_CODE]);
		$cmd = " select EM_NAME from PER_EDUCMAJOR where trim(EM_CODE)='$EM_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EM_NAME = trim($data_dpis1[EM_NAME]);

		if($command!='UPDATE' && !$_POST[INS_CODE]) $INS_CODE = trim($data[INS_CODE]);
		$cmd = " select INS_NAME from PER_INSTITUTE where trim(INS_CODE)='$INS_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$INS_NAME = trim($data_dpis1[INS_NAME]);

		if($command!='UPDATE' && !$_POST[ORG_ID1]) $DTLFORM_ORG_ID1 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
		if($command!='UPDATE' && !$_POST[ORG_ID2]) $DTLFORM_ORG_ID2 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;		
		if($command!='UPDATE' && !$_POST[ORG_ID3]) $DTLFORM_ORG_ID3 = (trim($data[ORG_ID_3]))? trim($data[ORG_ID_3]) : 0;
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID IN ($DTLFORM_ORG_ID1, $DTLFORM_ORG_ID2, $DTLFORM_ORG_ID3) ";
		$db_dpis1->send_cmd($cmd);
		while ( $data_dpis1 = $db_dpis1->get_array() ) {
			$temp_id = trim($data_dpis1[ORG_ID]);
			$ORG_NAME_1 = ($temp_id == $DTLFORM_ORG_ID1)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_1;
			$ORG_NAME_2 = ($temp_id == $DTLFORM_ORG_ID2)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_2;
			$ORG_NAME_3 = ($temp_id == $DTLFORM_ORG_ID3)?  trim($data_dpis1[ORG_NAME]) : $ORG_NAME_3;						
		}
		//echo ">><<<  $DTLFORM_ORG_ID1 - $DTLFORM_ORG_ID2 - $DTLFORM_ORG_ID3 <hr><hr>";

		if($CTRL_TYPE != 4 && $DTLFORM_ORG_ID1){
//			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DTLFORM_ORG_ID1 ";
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
										
		if($command!='UPDATE' && !$_POST[LEVEL_NO_1]) $LEVEL_NO_1 = trim($data[LEVEL_NO_1]);
		if($command!='UPDATE' && !$_POST[LEVEL_NO_2]) $LEVEL_NO_2 = trim($data[LEVEL_NO_2]);
		if($command!='UPDATE' && !$_POST[LEVEL_NO_3]) $LEVEL_NO_3 = trim($data[LEVEL_NO_3]);

		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
		if ($TR_PER_TYPE == 1) {			
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_1]) $PL_PN_CODE_1 = trim($data[PL_CODE_1]);
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_2]) $PL_PN_CODE_2 = trim($data[PL_CODE_2]);
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_3]) $PL_PN_CODE_3 = trim($data[PL_CODE_3]);		
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) IN ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3') ";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[PL_CODE]);
				$PL_PN_NAME_1 = ($temp_id == $PL_PN_CODE_1)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_1;
				$PL_PN_NAME_2 = ($temp_id == $PL_PN_CODE_2)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_2;
				$PL_PN_NAME_3 = ($temp_id == $PL_PN_CODE_3)?  trim($data_dpis1[PL_NAME]) : $PL_PN_NAME_3;	
			}								
		}

		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
		if ($TR_PER_TYPE == 2) {
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_1]) $PL_PN_CODE_1 = trim($data[PN_CODE_1]);		
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_2]) $PL_PN_CODE_2 = trim($data[PN_CODE_2]); 
			if($command!='UPDATE' && !$_POST[PL_PN_CODE_3]) $PL_PN_CODE_3 = trim($data[PN_CODE_3]);	
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) IN ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[PN_CODE]);
				$PL_PN_NAME_1 = ($temp_id == $PL_PN_CODE_1)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_1;
				$PL_PN_NAME_2 = ($temp_id == $PL_PN_CODE_2)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_2;
				$PL_PN_NAME_3 = ($temp_id == $PL_PN_CODE_3)?  trim($data_dpis1[PN_NAME]) : $PL_PN_NAME_3;	
			}	
		}
*/
	}else{ 	// 	if($TR_ID){
		//--- เคลียร์ค่าใน input สำหรับเพิ่มคนถัดไป
		$PN_NAME="";
		$PN_CODE="";
		$TR_BIRTH_DATE="";
		$TR_ADDRESS="";
		$TR_TEL="";
		$EM_NAME="";
		$EM_CODE="";
		$TR_STARTDATE="";
		$TR_STARTLEVEL=0;
		$TR_STARTORG2="";
		$TR_POSITION="";
		$TR_SALARY="";
		$TR_ORG3="";
		$TR_ORG1="";
		$TR_JOB="";
		$ORG_NAME_1="";
		$ORG_ID1="";
		$ORG_NAME_2="";
		$ORG_ID2="";
		$ORG_NAME_3="";
		$ORG_ID3="";
		$PL_PN_NAME_1="";
		$PL_PN_CODE_1="";
		$PL_PN_NAME_2="";
		$PL_PN_CODE_2="";
		$PL_PN_NAME_3="";
		$PL_PN_CODE_3="";
		$TR_REASON="";
		$TR_DATE="";
		$TR_CARDNO="";
		$TR_NAME="";
		$TR_GENDER=1;
		$EN_NAME="";
		$EN_CODE="";
		$INS_NAME="";
		$INS_CODE="";
		$TR_STARTPOS="";
		$TR_STARTORG3="";
		$TR_STARTORG1="";
		$TR_LEVEL=0;
		$TR_ORG_TEL="";
		$TR_ORG2="";
		$LEVEL_NO_1=0;
		$LEVEL_NO_2=0;
		$LEVEL_NO_3=0;
		$TR_BEGINDATE="";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		if ($command=="ADD")	$show_topic = 1;
	}
?>