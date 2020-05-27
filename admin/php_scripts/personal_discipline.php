<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

/*	print("<pre>");
	print_r($_POST);
	print("</pre>");*/

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	
//echo "$command / $DCL_RECEIVE_DATE / $DCL_NO";	
if($command == "ADD" && trim($DCL_RECEIVE_DATE) && trim($DCL_NO)){			//	if($command == "ADD" && trim($DCL_RECEIVE_DATE) && trim($DCL_NO) && trim($PER_ID)){
		$DCL_RECEIVE_DATE =  save_date($DCL_RECEIVE_DATE);
		if($DCL_DOC_DATE) $DCL_DOC_DATE =  save_date($DCL_DOC_DATE);
/*****		
		$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, ORG_ID, ORG_ID_1, LEVEL_NO, DEPARTMENT_ID
						 from			PER_PERSONAL
						 where		PER_ID = $PER_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE == 1){
			$POS_ID = $data[POS_ID];
			$table = "PER_POSITION";
			$field = "POS_ID";
		}elseif($PER_TYPE == 2){
			$POS_ID = $data[POEM_ID];
			$table = "PER_POS_EMP";
			$field = "POEM_ID";
		}elseif($PER_TYPE == 3){
			$POS_ID = $data[POEMS_ID]; 
			$table = "PER_POS_EMPSER";
			$field = "POEMS_ID";
		}elseif($PER_TYPE == 4){
			$POS_ID = $data[POT_ID];
			$table = "PER_POS_TEMP";
			$field = "POT_ID";
		}
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

		$cmd = " select	ORG_ID, ORG_ID_1 from $table where $field = $POS_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID = $data[ORG_ID];
		$ORG_ID_1 = $data[ORG_ID_1];
		if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			
		if($DPISDB == "odbc"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO, PER_ID
					  		 from 		PER_DISCIPLINE 
							 where 	LEFT(DCL_RECEIVE_DATE, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and PER_ID = $PER_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO, PER_ID
					  		 from 		PER_DISCIPLINE 
							 where 	SUBSTR(DCL_RECEIVE_DATE, 1, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and PER_ID = $PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO, PER_ID
					  		 from 		PER_DISCIPLINE 
							 where 	LEFT(DCL_RECEIVE_DATE, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and PER_ID = $PER_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
*****/
			$cmd = " select max(DCL_ID) as max_id from PER_DISCIPLINE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$DCL_ID = $data[max_id] + 1;
			
			/*$cmd = " insert into PER_DISCIPLINE (DCL_ID, PER_ID, PER_CARDNO, DCL_RECEIVE_DATE, DCL_NO, DCL_PL_NAME, 
							DCL_PM_NAME, LEVEL_NO, DCL_POS_NO, DCL_ORG1, DCL_ORG2, DCL_ORG3, DCL_ORG4, DCL_ORG5, DCL_DESC, 
							PEN_CODE, DCL_DOC_DESC, DCL_DOC_NO, DCL_DOC_DATE, DCL_MINISTRY_RESULT, DCL_OCSC_RESULT, 
							DCL_COLLECT, DCL_REMARK, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							values ($DCL_ID, $PER_ID, '$PER_CARDNO', '$DCL_RECEIVE_DATE', '$DCL_NO', '$DCL_PL_NAME', '$DCL_PM_NAME', 
							'$LEVEL_NO', '$DCL_POS_NO', '$DCL_ORG1', '$DCL_ORG2', '$DCL_ORG3', '$DCL_ORG4', '$DCL_ORG5', '$DCL_DESC', '$PEN_CODE', 
							'$DCL_DOC_DESC', '$DCL_DOC_NO', '$DCL_DOC_DATE', '$DCL_MINISTRY_RESULT', '$DCL_OCSC_RESULT', '$DCL_COLLECT', 
							'$DCL_REMARK', $TMP_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') "; */
				$cmd = " insert into PER_DISCIPLINE (DCL_ID,	DCL_RECEIVE_DATE,DCL_NO,DCL_DESC,DCL_DOC_DESC,DCL_DOC_NO,DCL_DOC_DATE,DCL_MINISTRY_RESULT,DCL_OCSC_RESULT,DCL_COLLECT,DCL_REMARK,DEPARTMENT_ID,	UPDATE_USER,UPDATE_DATE) values ($DCL_ID, '$DCL_RECEIVE_DATE', '$DCL_NO',
							'$DCL_DESC','$DCL_DOC_DESC', '$DCL_DOC_NO', '$DCL_DOC_DATE', '$DCL_MINISTRY_RESULT', '$DCL_OCSC_RESULT', '$DCL_COLLECT', 
							'$DCL_REMARK', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') "; 
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "ADD -> ".$cmd;

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$DCL_ID." : ".trim($DCL_RECEIVE_DATE)." : ".$DCL_NO."]");
/*****
		}else{
			$data = $db_dpis->get_array();
			$TMP_DCL_RECEIVE_DATE = substr($data[DCL_RECEIVE_DATE], 0, 10);
			$DCL_NO = $data[DCL_NO];
			$PER_ID = $data[PER_ID];
			
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_CODE = $data[PN_CODE];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
			
			$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;

			$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$TMP_DCL_RECEIVE_DATE." ครั้งที่ ".$DCL_NO." ".$PER_FULLNAME."]";
		} // endif
*****/
	}

	if($command == "UPDATE" && trim($DCL_ID)  && trim($DCL_RECEIVE_DATE) && trim($DCL_NO)){
		$DCL_RECEIVE_DATE =  save_date($DCL_RECEIVE_DATE);
		if($DCL_DOC_DATE) $DCL_DOC_DATE =  save_date($DCL_DOC_DATE);
		if($DPISDB == "odbc"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO
					  		 from 		PER_DISCIPLINE 
							 where 	LEFT(DCL_RECEIVE_DATE, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and DCL_ID <> $DCL_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO
					  		 from 		PER_DISCIPLINE 
							 where 	SUBSTR(DCL_RECEIVE_DATE, 1, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and DCL_ID <> $DCL_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO
					  		 from 		PER_DISCIPLINE 
							 where 	LEFT(DCL_RECEIVE_DATE, 10) = '$DCL_RECEIVE_DATE' and  DCL_NO='$DCL_NO' and DCL_ID <> $DCL_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "->  $count_duplicate ";
		if($count_duplicate <= 0){
			$cmd = " update PER_DISCIPLINE set 
								DCL_RECEIVE_DATE='$DCL_RECEIVE_DATE', 
								DCL_NO='$DCL_NO', 
								DCL_DESC='$DCL_DESC',
								DCL_DOC_DESC='$DCL_DOC_DESC',
								DCL_DOC_NO='$DCL_DOC_NO',
								DCL_DOC_DATE='$DCL_DOC_DATE',
								DCL_MINISTRY_RESULT='$DCL_MINISTRY_RESULT',
								DCL_OCSC_RESULT='$DCL_OCSC_RESULT',
								DCL_COLLECT='$DCL_COLLECT',
								DCL_REMARK='$DCL_REMARK',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where DCL_ID=$DCL_ID ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "UPD -> ".$cmd;
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$DCL_ID." : ".trim($DCL_RECEIVE_DATE)." : ".$DCL_NO."]");
		}
/***
		else{
			$data = $db_dpis->get_array();
			$TMP_DCL_RECEIVE_DATE = substr($data[DCL_RECEIVE_DATE], 0, 10);
			$DCL_NO = $data[DCL_NO];
			$PER_ID = $data[PER_ID];
			
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_CODE = $data[PN_CODE];
			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
			
			$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;

			$err_text = "รหัสข้อมูลซ้ำ [".$TMP_DCL_RECEIVE_DATE." ".$DCL_NO." ".$PER_FULLNAME."]";
		} // end if
***/
	}
	
	if($command == "DELETE" && trim($DCL_ID)){
		$cmd = " select 	DCL_RECEIVE_DATE, DCL_NO
				  		 from 		PER_DISCIPLINE 
						 where 	DCL_ID = $DCL_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DCL_RECEIVE_DATE = substr($data[DCL_RECEIVE_DATE], 0, 10);
		$DCL_NO = $data[DCL_NO];
/***			
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
			
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
			
		$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
***/
	
		$cmd = " delete from PER_DISCIPLINE_DTL where DCL_ID=$DCL_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_DISCIPLINE where DCL_ID=$DCL_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $DCL_ID ." : ".$DCL_RECEIVE_DATE." ".$DCL_NO."]");
	}

	if(($UPD || $VIEW) && ($DCL_ID)){	// ดู / แก้ไข
		$cmd = " select 	DCL_ID, DCL_RECEIVE_DATE,DCL_NO,DCL_DESC,DCL_DOC_DESC,DCL_DOC_NO,DCL_DOC_DATE,DCL_MINISTRY_RESULT,DCL_OCSC_RESULT,DCL_COLLECT,DCL_REMARK,DEPARTMENT_ID,	UPDATE_USER,UPDATE_DATE
				  		 from 		PER_DISCIPLINE
						 where 	DCL_ID=$DCL_ID ";
//		echo "<br>>> $cmd<br>";	
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DCL_RECEIVE_DATE =  show_date_format($data[DCL_RECEIVE_DATE],1);
		$DCL_NO = trim($data[DCL_NO]);
		$DCL_DESC = $data[DCL_DESC];
		$DCL_DOC_DESC = $data[DCL_DOC_DESC];
		$DCL_DOC_NO = $data[DCL_DOC_NO];
		$DCL_DOC_DATE =  show_date_format($data[DCL_DOC_DATE],1);
		$DCL_MINISTRY_RESULT = $data[DCL_MINISTRY_RESULT];
		$DCL_OCSC_RESULT = $data[DCL_OCSC_RESULT];
		$DCL_COLLECT = $data[DCL_COLLECT];
		$DCL_REMARK = $data[DCL_REMARK];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		if($DEPARTMENT_ID){
			$cmd = " select ORG_NAME,ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
		}
		
/***	
		$cmd = " select PEN_NAME from PER_PENALTY where PEN_CODE='$PEN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PEN_NAME = trim($data[PEN_NAME]);

	
		$cmd = " select 	PN_CODE,PER_CARDNO, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		if(!trim($PER_CARDNO)) $PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_TYPE = trim($data[PER_TYPE]);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = trim($data[PN_NAME]);
		
		$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		if($PER_TYPE==1){
			$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
							 from 		PER_POSITION a, PER_LINE b, PER_ORG c
							 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PL_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
			$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
		}elseif($PER_TYPE==2){
			$cmd = " select 	b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PN_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE==3){
			$cmd = " select 	b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[EP_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		} // end if

		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID_REVIEW ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$REVIEW_PN_CODE = trim($data[PN_CODE]);
		$REVIEW_PER_NAME = trim($data[PER_NAME]);
		$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
		$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
		$REVIEW_POS_ID = trim($data[POS_ID]);
		$REVIEW_POEM_ID = trim($data[POEM_ID]);
		$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
		$REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$REVIEW_PN_NAME = trim($data[PN_NAME]);
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$REVIEW_POSITION_LEVEL) $REVIEW_POSITION_LEVEL = $REVIEW_LEVEL_NAME;
		
		$REVIEW_PER_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
		
		if($REVIEW_PER_TYPE==1){
			$cmd = " select 	b.PL_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b
							 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[PL_NAME]);
			$REVIEW_PT_CODE = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$REVIEW_PT_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$REVIEW_PT_NAME = trim($data2[PT_NAME]);
			if ($RPT_N)
			    $REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)? "$REVIEW_PL_NAME$REVIEW_POSITION_LEVEL" : "") . (trim($REVIEW_PM_NAME) ?")":"");
			else
				$REVIEW_PL_NAME = (trim($REVIEW_PM_NAME) ?"$REVIEW_PM_NAME (":"") . (trim($REVIEW_PL_NAME)?($REVIEW_PL_NAME ." ". level_no_format($REVIEW_LEVEL_NO) . (($REVIEW_PT_NAME != "ทั่วไป" && $REVIEW_LEVEL_NO >= 6)?"$REVIEW_PT_NAME":"")):"") . (trim($REVIEW_PM_NAME) ?")":"");
		}elseif($REVIEW_PER_TYPE==2){
			$cmd = " select 	b.PN_NAME
							 from 		PER_POS_EMP a, PER_POS_NAME b
							 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[PN_NAME]);
		}elseif($REVIEW_PER_TYPE==3){
			$cmd = " select 	b.EP_NAME
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
							 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PL_NAME = trim($data[EP_NAME]);
		} // end if
*****/  
	} // end if
	
	if( (!$UPD && !$VIEW) ){
		unset($DCL_ID);
		unset($DCL_RECEIVE_DATE);
		unset($DCL_NO);
		unset($DCL_DESC);
		unset($DCL_DOC_DESC);
		unset($DCL_DOC_NO);
		unset($DCL_DOC_DATE);
		unset($DCL_MINISTRY_RESULT);
		unset($DCL_OCSC_RESULT);
		unset($DCL_COLLECT);
		unset($DCL_REMARK);

		$search_per_type = "";
	} // end if
?>