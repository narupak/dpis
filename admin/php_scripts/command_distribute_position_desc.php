<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	switch($CTRL_TYPE){
		case 2 :
			$OLD_PV_CODE = $PV_CODE;
			$OLD_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$OLD_PV_CODE = $PV_CODE;
			$OLD_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
		case 5 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			$OLD_ORG_ID = $ORG_ID;
			$OLD_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case

	if($MINISTRY_ID && !trim($MINISTRY_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$OLD_MINISTRY_ID = $MINISTRY_ID;
		$OLD_MINISTRY_NAME = $MINISTRY_NAME;
	} // end if

	if($DEPARTMENT_ID && !trim($DEPARTMENT_NAME)){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];

		$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
		$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if(trim($REQ_RESULT) == "") $REQ_RESULT = "NULL";
	if(trim($REQ_RESULT) != 1) $REQ_EFFECTIVE = "NULL";
	if(!trim($ORG_ID)) $ORG_ID = "NULL";
	if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
	if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
	if(!trim($ORG_ID_3)) $ORG_ID_3 = "NULL";
	if(!trim($ORG_ID_4)) $ORG_ID_4 = "NULL";
	if(!trim($ORG_ID_5)) $ORG_ID_5 = "NULL";
	if(trim($PM_CODE) && $PM_CODE!="NULL"){ 
		$PM_CODE = "'$PM_CODE'";
	}else{ $PM_CODE = "NULL";	}
	if(trim($PC_CODE) && $PC_CODE!="NULL"){ //$PC_CODE = (trim($PC_CODE))? "'".$PC_CODE."'" : "NULL";
		$PC_CODE = "'$PC_CODE'";
	}else{ $PC_CODE = "NULL"; }
	if(trim($SKILL_CODE) && $SKILL_CODE!="NULL"){ //$SKILL_CODE = (trim($SKILL_CODE))? "'".$SKILL_CODE."'" : "NULL";
		$SKILL_CODE = "'$SKILL_CODE'";
	}else{ $SKILL_CODE = "NULL"; }

	$REQ_SALARY += 0;
	$REQ_MGTSALARY += 0;

	if(trim($REQ_EFF_DATE)){
		$arr_temp = explode("/", $REQ_EFF_DATE);
		$REQ_EFF_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

	if($command == "ADD" && trim($REQ_SEQ) && trim($REQ_POS_NO)){
		$cmd = " select 	REQ_SEQ, REQ_POS_NO
				  		 from 		PER_REQ3_DTL 
						 where 	REQ_ID=$REQ_ID and trim(REQ_POS_NO)='". trim($REQ_POS_NO) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){			//ตำแหน่งใหม่ที่จะปรับปรุง ยังไม่ได้ยืนยัน
			$cmd = " insert into PER_REQ3_DTL (REQ_ID, REQ_SEQ, REQ_POS_NO, REQ_EFF_DATE, REQ_RESULT, REQ_EFFECTIVE, 
							REQ_CONFIRM, POS_ID, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
							REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5,
							UPDATE_USER, UPDATE_DATE, LEVEL_NO)
							values ($REQ_ID, $REQ_SEQ, '".trim($REQ_POS_NO)."', '".trim($REQ_EFF_DATE)."', $REQ_RESULT, $REQ_EFFECTIVE, 0,
							$POS_ID, '$OT_CODE', '$PL_CODE', $PM_CODE, '$CL_NAME', '$PT_CODE', $SKILL_CODE, $PC_CODE,
							'$REQ_CONDITION', $REQ_SALARY, $REQ_MGTSALARY, $ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5,
							$SESS_USERID, '$UPDATE_DATE', '$LEVEL_NO') ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();

			if($REQ_RESULT==1){
			//insert ลง PER_POSITIONHIS
  			//หาข้อมูลเพื่อนำมา insert ลง POSITIONHIS
			$SAV_POH_EFFECTIVEDATE = trim($REQ_EFF_DATE);
			$SAV_POH_ENDDATE = trim($REQ_EFF_DATE);
			/*
			$MOV_CODE=10510;		//กำหนด MOV_CODE เป็น รับโอนข้าราชการพลเรือนสามัญ
			$cmd = " select 	PER_ID 
							from 		PER_PERSONAL
							where 	 POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PER_ID = $data[PER_ID];
			$POH_DOCNO=$data[PER_POS_DOCNO];
			$SAV_POH_DOCDATE= $data[PER_POS_DOCDATE];
			$PN_CODE=$data[PN_CODE];
			$PER_CARDNO=$data[PER_CARDNO];

			//----------------------------------			
			$cmd = " select 	OT_CODE, PL_CODE, PM_CODE, CL_NAME,  PT_CODE, SKILL_CODE, PC_CODE, POS_CONDITION,
							POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2,LEVEL_NO
							from 		PER_POSITION 
							where 	POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);

			$POH_POS_NO=$data[POS_NO];
			$POH_ORGMGT="";
			$ORG_ID_1= $ORG_ID_1;
			$ORG_ID_2= $ORG_ID_2;
			$ORG_ID_3= $data[ORG_ID_3];
			$POH_UNDER_ORG1_NAME=
			$POH_UNDER_ORG2_NAME=
			$POH_ASS_ORG_NAME=
			$POH_ASS_ORG1_NAME=
			$POH_ASS_ORG2_NAME=
			$POH_SALARY_POS=$data[POS_SALARY];
			$POH_REMARK=$data[POS_REMARK];
			$ORG_NAME_1=
			$ORG_NAME_2=
			$ORG_NAME_3=
			$PL_CODE=$PL_CODE;

			$EP_CODE=
			$PM_CODE=$PM_CODE;
			$LEVEL_NO=$data[LEVEL_NO];
			$PT_CODE=$data[ PT_CODE];
			$CT_CODE_tmp=
			$POH_PV_CODE=
			$AP_CODE=
			$POH_ORG_TRANSFER=
			$POH_ORG=
			$PM_NAME=
			$POH_PL_NAME=
			$POH_SEQ_NO=
			$POH_LAST_POSITION=
			$POH_CMD_SEQ=
			$POH_ISREAL=
			$ES_CODE=
			$POH_LEVEL_NO=
			$SESS_USERID=$SESS_USERID
			$UPDATE_DATE='$UPDATE_DATE'
			
			*/

			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1;	
/*
			$cmd = " insert into PER_POSITIONHIS 
						(POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, 
						POH_DOCDATE, POH_POS_NO, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, 
						POH_UNDER_ORG2, POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, 
						POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, PL_CODE, PN_CODE, EP_CODE, PER_CARDNO, 
						PM_CODE, LEVEL_NO, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORG_TRANSFER, POH_ORG, 
						POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, 
						ES_CODE, POH_LEVEL_NO, UPDATE_USER, UPDATE_DATE)
												
						values ($POH_ID, $PER_ID, '$SAV_POH_EFFECTIVEDATE', '$MOV_CODE', '$SAV_POH_ENDDATE', '$POH_DOCNO', 
						'$SAV_POH_DOCDATE', '$POH_POS_NO', $POH_ORGMGT, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3,
						'$POH_UNDER_ORG1_NAME', '$POH_UNDER_ORG2_NAME', '$POH_ASS_ORG_NAME', 
						'$POH_ASS_ORG1_NAME', '$POH_ASS_ORG2_NAME', '$POH_SALARY', '$POH_SALARY_POS', 
						'$POH_REMARK', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $PL_CODE, $PN_CODE, 
						$EP_CODE, $PER_CARDNO, $PM_CODE, $LEVEL_NO, $PT_CODE, $CT_CODE_tmp, $POH_PV_CODE, 
						$AP_CODE, '$POH_ORG_TRANSFER', '$POH_ORG', '$PM_NAME', '$POH_PL_NAME', $POH_SEQ_NO,  
						'$POH_LAST_POSITION', $POH_CMD_SEQ, '$POH_ISREAL', $ES_CODE, $POH_LEVEL_NO, $SESS_USERID, '$UPDATE_DATE' )  ";
			$db_dpis->send_cmd($cmd);			
*/			
			//ถ้ามีมติ และได้รับการอนุมัติ REQ_RESULT=1 อัพเดทตำแหน่งนั้นใน POSITION			
			$cmd = " update PER_POSITION  set
								ORG_ID = $ORG_ID,
								POS_NO='$REQ_POS_NO',
								OT_CODE = '$OT_CODE',
								ORG_ID_1 = $ORG_ID_1, 
								ORG_ID_2 = $ORG_ID_2, 
								ORG_ID_3 = $ORG_ID_3, 
								ORG_ID_4 = $ORG_ID_4, 
								ORG_ID_5 = $ORG_ID_5, 
								PM_CODE = $PM_CODE, 				
								PL_CODE = '$PL_CODE', 				
								CL_NAME = '$CL_NAME', 				
								POS_SALARY = $REQ_SALARY, 	
								POS_MGTSALARY = $REQ_MGTSALARY, 	
								SKILL_CODE = $SKILL_CODE,	
								PT_CODE = '$PT_CODE', 				
								PC_CODE = $PC_CODE, 				
								POS_CONDITION = '$REQ_CONDITION',	
								LEVEL_NO='$LEVEL_NO',
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								DEPARTMENT_ID = $DEPARTMENT_ID
							 where POS_ID=$POS_ID ";
					$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
					//echo $cmd;
					//echo ">> $REQ_RESULT :: ".trim($REQ_POS_NO)."  PER_POSITION";
			}	//end if

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$POS_ID." : ".$PL_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[REQ_SEQ]." ".$data[REQ_POS_NO]."]";

			if(trim($REQ_EFF_DATE)){
				$arr_temp = explode("-", $REQ_EFF_DATE);
				$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
			} // end if
		} // endif
	}

	if($command == "UPDATE" && trim($REQ_SEQ) && trim($REQ_POS_NO)){
		$cmd = " update PER_REQ3_DTL set 
						 	REQ_POS_NO = '$REQ_POS_NO', 
							REQ_EFF_DATE = '$REQ_EFF_DATE',
							REQ_RESULT = $REQ_RESULT,
							REQ_EFFECTIVE = $REQ_EFFECTIVE,
							POS_ID = $POS_ID,
							OT_CODE = '$OT_CODE',
							PL_CODE = '$PL_CODE',
							PM_CODE = $PM_CODE,
							CL_NAME = '$CL_NAME',
							PT_CODE = '$PT_CODE',
							SKILL_CODE = $SKILL_CODE,
							PC_CODE = $PC_CODE,
							REQ_CONDITION = '$REQ_CONDITION',
							REQ_SALARY = $REQ_SALARY,
							REQ_MGTSALARY = $REQ_MGTSALARY,
							ORG_ID = $ORG_ID,
							ORG_ID_1 = $ORG_ID_1,
							ORG_ID_2 = $ORG_ID_2,
							ORG_ID_3 = $ORG_ID_3,
							ORG_ID_4 = $ORG_ID_4,
							ORG_ID_5 = $ORG_ID_5,
							LEVEL_NO='$LEVEL_NO',
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						 where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd); 
//		$db_dpis->show_error();

		//ถ้ามีมติ และได้รับการอนุมัติ REQ_RESULT=1 อัพเดทตำแหน่งนั้นใน POSITION
		if($REQ_RESULT==1){
			$cmd = " update PER_POSITION  set
								ORG_ID = $ORG_ID,
								POS_NO='$REQ_POS_NO',
								OT_CODE = '$OT_CODE',
								ORG_ID_1 = $ORG_ID_1, 
								ORG_ID_2 = $ORG_ID_2, 
								ORG_ID_3 = $ORG_ID_3, 
								ORG_ID_4 = $ORG_ID_4, 
								ORG_ID_5 = $ORG_ID_5, 
								PM_CODE = $PM_CODE, 
								PL_CODE = '$PL_CODE',
								CL_NAME = '$CL_NAME', 				
								POS_SALARY = $REQ_SALARY, 	
								POS_MGTSALARY = $REQ_MGTSALARY, 	
								SKILL_CODE = $SKILL_CODE,	
								PT_CODE = '$PT_CODE', 				
								PC_CODE = $PC_CODE, 				
								POS_CONDITION = '$REQ_CONDITION',	
								LEVEL_NO='$LEVEL_NO',
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								DEPARTMENT_ID = $DEPARTMENT_ID
							 where POS_ID=$POS_ID ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				//echo $cmd;	
				//___echo ">> $REQ_RESULT :: ".trim($REQ_POS_NO)."  PER_POSITION";
		}	//end if

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$POS_ID." : ".$PL_NAME."]");
	}
	
	if($command == "DELETE" && trim($REQ_SEQ)){
		$cmd = " select REQ_POS_NO, POS_ID from PER_REQ3_DTL where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_POS_NO = $data[REQ_POS_NO];
		$POS_ID = $data[POS_ID];
		
		$cmd = " select b.PL_NAME from PER_POSITION a, PER_LINE b where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
		$cmd = " delete from PER_REQ3_DTL where REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($REQ_SEQ)." : ".$REQ_POS_NO." : ".$POS_ID." : ".$PL_NAME."]");
	}
	
	if($UPD || $VIEW){
		//ข้อมูลตำแหน่งใหม่ที่จะอัพเดท***
		$cmd = " select 	REQ_SEQ, REQ_POS_NO, REQ_EFF_DATE, REQ_RESULT, REQ_EFFECTIVE,
										POS_ID, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, 
										REQ_CONDITION, REQ_SALARY, REQ_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5
				  		 from 		PER_REQ3_DTL 
						 where 	REQ_ID=$REQ_ID and REQ_SEQ=$REQ_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();		
		
		$REQ_CONDITION = $data[REQ_CONDITION];
		$REQ_SALARY=$data[REQ_SALARY];
		$REQ_MGTSALARY=$data[REQ_MGTSALARY];
	
		$OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OT_NAME = $data2[OT_NAME];

		$PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PL_NAME = $data2[PL_NAME];

		$PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = $data2[PM_NAME];

		$CL_NAME = $data[CL_NAME];
		$CL_CODE = $CL_NAME;
		
		$LEVEL_NO = $data[LEVEL_NO];
		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_NAME = $data2[LEVEL_NAME];

		$PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = $data2[PT_NAME];

		$SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$SKILL_NAME = $data2[SKILL_NAME];
		$SG_CODE = $data2[SG_CODE];
		$SG_NAME = $data2[SG_NAME];

		$PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PC_NAME = $data2[PC_NAME];

		$ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
		$CT_CODE = $data2[CT_CODE];
		$PV_CODE = $data2[PV_CODE];
		$AP_CODE = $data2[AP_CODE];
		$ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_OT_NAME = $data2[OT_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_1 = $data2[ORG_NAME];

		$ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_2 = $data2[ORG_NAME];

		$ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_3 = $data2[ORG_NAME];

		$ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_4 = $data2[ORG_NAME];

		$ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME_5 = $data2[ORG_NAME];

		$REQ_SEQ = $data[REQ_SEQ];
		$REQ_POS_NO = $data[REQ_POS_NO];
		$REQ_EFF_DATE = substr($data[REQ_EFF_DATE], 0, 10);
		if($REQ_EFF_DATE){
			$arr_temp = explode("-", $REQ_EFF_DATE);
			$REQ_EFF_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		$REQ_EFFECTIVE = $data[REQ_EFFECTIVE];		
		$REQ_RESULT = $data[REQ_RESULT];	
		//***************************
		$POS_ID = $data[POS_ID];
		//***************************
		
		//ให้ดึงข้อมูลเก่าจาก PER_POSITIONHIS

		
		//==ข้อมูลตำแหน่งเก่า (ที่ยังไม่เลือกอนุมัติ) แต่ถ้าอนุมัติแล้วคืออัพเดทไปแล้ว (จะกลายเป็นตำแหน่งใหม่ที่ปรับปรุง)
		$cmd = " select 	OT_CODE, PL_CODE, PM_CODE, CL_NAME,  PT_CODE, SKILL_CODE, PC_CODE, POS_CONDITION,
										POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, LEVEL_NO
				  		 from 		PER_POSITION 
						 where 	POS_ID=$POS_ID ";		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NO=$data[LEVEL_NO];
		$OLD_POS_CONDITION = $data[POS_CONDITION];
		$OLD_POS_SALARY = $data[POS_SALARY];
		$OLD_POS_MGTSALARY = $data[POS_MGTSALARY];
		
		$OLD_OT_CODE = $data[OT_CODE];
		$cmd = " select OT_NAME from PER_OFF_TYPE where OT_CODE='$OLD_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_OT_NAME = $data2[OT_NAME]; 

		$OLD_PL_CODE = $data[PL_CODE];
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$OLD_PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_PL_NAME = $data2[PL_NAME];

		$OLD_PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$OLD_PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_PM_NAME = $data2[PM_NAME];

		$OLD_CL_NAME = $data[CL_NAME];
		$OLD_CL_CODE = $OLD_CL_NAME;
		
		$OLD_LEVEL_NO = $data[LEVEL_NO];
		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$OLD_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_LEVEL_NAME = $data2[LEVEL_NAME];

		$OLD_PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$OLD_PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_PT_NAME = $data2[PT_NAME];

		$OLD_SKILL_CODE = $data[SKILL_CODE];
		$cmd = " select a.SKILL_NAME, a.SG_CODE, b.SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SKILL_CODE='$OLD_SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_SKILL_NAME = $data2[SKILL_NAME];
		$OLD_SG_CODE = $data2[SG_CODE];
		$OLD_SG_NAME = $data2[SG_NAME];

		$OLD_PC_CODE = $data[PC_CODE];
		$cmd = " select PC_NAME from PER_CONDITION where PC_CODE='$OLD_PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_PC_NAME = $data2[PC_NAME];

		$OLD_ORG_ID = $data[ORG_ID];
		$cmd = " select ORG_NAME, CT_CODE, PV_CODE, AP_CODE, OT_CODE from PER_ORG where ORG_ID=$OLD_ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME = $data2[ORG_NAME];
		$OLD_CT_CODE = $data2[CT_CODE];
		$OLD_PV_CODE = $data2[PV_CODE];
		$OLD_AP_CODE = $data2[AP_CODE];
		$OLD_ORG_OT_CODE = $data2[OT_CODE];

		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$OLD_CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_CT_NAME = $data2[CT_NAME];

		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$OLD_PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_PV_NAME = $data2[PV_NAME];

		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$OLD_AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_AP_NAME = $data2[AP_NAME];

		$cmd = " select OT_NAME from PER_ORG_TYPE where OT_CODE='$OLD_ORG_OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_OT_NAME = $data2[OT_NAME];

		$OLD_ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME_1 = $data2[ORG_NAME];

		$OLD_ORG_ID_2 = $data[ORG_ID_2];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME_2 = $data2[ORG_NAME];
	
		$OLD_ORG_ID_3 = $data[ORG_ID_3];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME_3 = $data2[ORG_NAME];

		$OLD_ORG_ID_4 = $data[ORG_ID_4];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME_4 = $data2[ORG_NAME];
	
		$OLD_ORG_ID_5 = $data[ORG_ID_5];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$OLD_ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$OLD_ORG_NAME_5 = $data2[ORG_NAME];
	
	//	echo $LEVEL_NO."---".$OLD_LEVEL_NO."<br>";
	} // end if
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$REQ_SEQ = "";
		$cmd = " select max(REQ_SEQ) as MAX_SEQ from PER_REQ3_DTL where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REQ_SEQ = $data[max_seq] + 1;
		
		$REQ_POS_NO = "";
		$REQ_EFF_DATE = "";
		$REQ_RESULT = "";
		$REQ_EFFECTIVE = "";
		$POS_ID = "";
		
		$OT_NAME = "";
		$OT_CODE = "";
		$PL_NAME = "";
		$PL_CODE = "";
		$PM_NAME = "";
		$PM_CODE = "";
		$CL_NAME = "";
		$CL_CODE = "";
		$PT_NAME = "";
		$PT_CODE = "";
		$SG_NAME = "";
		$SG_CODE = "";
		$SKILL_NAME = "";
		$SKILL_CODE = "";
		$PC_NAME = "";
		$PC_CODE = "";
		$REQ_CONDITION = "";
		$REQ_SALARY = "";
		$REQ_MGTSALARY = "";
		$ORG_NAME = "";
		$ORG_ID = "";
		$ORG_NAME_1 = "";
		$ORG_ID_1 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_2 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_5 = "";
		$ORG_ID_5 = "";
		$CT_NAME = "";
		$CT_CODE = "";
		$PV_NAME = "";
		$PV_CODE = "";
		$AP_NAME = "";
		$AP_CODE = "";
		$ORG_OT_CODE = "";
		$ORG_OT_NAME = "";
		$LEVEL_NO="";
		$LEVEL_NAME="";

		$OLD_OT_NAME = "";
		$OLD_PL_NAME = "";
		$OLD_PM_NAME = "";
		$OLD_CL_NAME = "";
		$OLD_PT_NAME = "";
		$OLD_SG_NAME = "";
		$OLD_SKILL_NAME = "";
		$OLD_PC_NAME = "";
		$OLD_POS_CONDITION = "";
		$OLD_POS_SALARY = "";
		$OLD_POS_MGTSALARY = "";
		$OLD_ORG_NAME = "";
		$OLD_ORG_NAME_1 = "";
		$OLD_ORG_NAME_2 = "";
		$OLD_ORG_NAME_3 = "";
		$OLD_ORG_NAME_4 = "";
		$OLD_ORG_NAME_5 = "";
		$OLD_CT_NAME = "";
		$OLD_PV_NAME = "";
		$OLD_AP_NAME = "";
		$OLD_ORG_OT_NAME = "";
		$OLD_LEVEL_NAME="";
	} // end if
?>