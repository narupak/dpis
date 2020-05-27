<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($SESS_PER_ID) $APPROVE_PER_ID = $SESS_PER_ID;

	if($APPROVE_PER_ID){
		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, a.POEM_ID, d.POEM_NO as EMP_POS_NO, a.POEMS_ID, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, f.POSITION_LEVEL, c.PT_CODE
							 from 		PER_PRENAME b
											inner join (
												(
													(
														(
															PER_PERSONAL a
															left join PER_POSITION c on a.POS_ID = c.POS_ID
														) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
													) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
												)  left join PER_LEVEL f on a.LEVEL_NO=f.LEVEL_NO
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $APPROVE_PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, a.POEM_ID, d.POEM_NO as EMP_POS_NO, a.POEMS_ID, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, f.POSITION_LEVEL
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$APPROVE_PER_ID and a.LEVEL_NO=f.LEVEL_NO(+) ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, a.POEM_ID, d.POEM_NO as EMP_POS_NO, a.POEMS_ID, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO, c.PT_CODE, f.POSITION_LEVEL
							 from 		PER_PERSONAL a
											inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
											left join PER_POSITION c on a.POS_ID = c.POS_ID
											left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
											left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											left join PER_LEVEL f on a.LEVEL_NO=f.LEVEL_NO
							where		a.PER_ID = $APPROVE_PER_ID ";
		} // end if	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();

		$APPROVE_PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];
		$LEVEL_NO = $data[LEVEL_NO];
		$LEVEL_NAME = $data[POSITION_LEVEL];
		
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1){ 
			$APPROVE_ORG_ID = $data[ORG_ID];
			$POS_ID = $data[POS_ID];
			$APPROVE_PL_CODE = $data[PL_CODE];
			$PT_CODE = $data[PT_CODE];			

			$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PT_NAME = $data[PT_NAME];
			
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='$APPROVE_PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = trim($data[PL_NAME])?($data[PL_NAME] .$LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"";
		}elseif($PER_TYPE==2){ 
			$APPROVE_ORG_ID = $data[EMP_ORG_ID];
			$POS_ID = $data[POEM_ID];
			$APPROVE_PL_CODE = $data[PN_CODE];

			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$APPROVE_PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = (trim($data[PN_NAME]))? "$data[PN_NAME]". $LEVEL_NAME : "";	
		}elseif ($PER_TYPE == 3){ 
			$APPROVE_ORG_ID = $data[EMPS_ORG_ID];
			$POS_ID = $data[POEMS_ID];
			$APPROVE_PL_CODE = $data[EP_CODE];

			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$APPROVE_PL_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$APPROVE_PL_NAME = (trim($data[EP_NAME]))? "$data[EP_NAME]". $LEVEL_NAME : "";	
		} // end if

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$APPROVE_ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_ORG_NAME = $data[ORG_NAME];

		$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$APPROVE_ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_DEPARTMENT_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$APPROVE_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_DEPARTMENT_NAME = $data[ORG_NAME];
		$APPROVE_MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$APPROVE_MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$APPROVE_MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($PER_ID){
		if($DPISDB=="odbc"){	
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID
						   ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO 
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID
						   ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
											b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
											a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
											c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO
							 from 		PER_PRENAME b
											inner join (
												((
													PER_PERSONAL a
													left join PER_POSITION c on a.POS_ID = c.POS_ID
												) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
											) on a.PN_CODE = b.PN_CODE
							where		a.PER_ID = $PER_ID
						   ";
		} // end if	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];					

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];

		$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];

		$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if
	
	if($command == "APPROVE" && $PER_ID && $ABS_ID){
		if(!$APPROVE_FLAG) $APPROVE_FLAG = "NULL";
		
		$cmd = " update PER_ABSENT set APPROVE_FLAG=$APPROVE_FLAG, APPROVE_PER_ID=$APPROVE_PER_ID where ABS_ID=$ABS_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > อนุมัติข้อมูลการลา [".$APPROVE_FLAG." : ".trim($ABS_ID)." : ".$PER_ID." - ".$PER_NAME." BY ".$APPROVE_PER_ID." - ".$APPROVE_PER_NAME."]");
	} // end if
	
	if($UPD || $VIEW){
		$cmd = " select 	a.AB_CODE, AB_NAME, ABS_STARTDATE, ABS_STARTPERIOD, 
										ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_LETTER, APPROVE_FLAG
						 from 		PER_ABSENT a, PER_ABSENTTYPE b
						 where 	ABS_ID=$ABS_ID and a.AB_CODE=b.AB_CODE  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_CODE = trim($data[AB_CODE]);
		$AB_NAME = trim($data[AB_NAME]);
		$ABS_STARTPERIOD = trim($data[ABS_STARTPERIOD]);
		$ABS_ENDPERIOD = trim($data[ABS_ENDPERIOD]);		
		$ABS_DAY = trim($data[ABS_DAY]);
		$ABS_LETTER = trim($data[ABS_LETTER]);
		$APPROVE_FLAG = $data[APPROVE_FLAG];		

		$ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], 1);
		$ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], 1);
		
		if(trim($AB_CODE) == "10") $ABS_ENDDATE = "";			
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$PER_ID = "";
		$PER_NAME = "";
		$ORG_ID = "";
		$ORG_NAME = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if

		$ABS_ID = "";
		$AB_CODE = "";
		$AB_NAME = "";
		$ABS_STARTDATE = "";
		$ABS_ENDDATE = "";
		$ABS_DAY = "";
		$ABS_STARTPERIOD = "";
		$ABS_ENDPERIOD = "";
		$ABS_LETTER = "";
		$APPROVE_FLAG = "";
	} // end if
	
	function list_approve_substitute($APPROVE_PER_ID){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $ARR_SUBSTITUTE_APPROVE;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " select PER_ID from PER_PERSONAL where REPLACE_PER_ID=$APPROVE_PER_ID and ABSENT_FLAG=1 ";
		$count_substitute = $db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$cmd = " select PER_ID from PER_PERSONAL where APPROVE_PER_ID=$data[PER_ID] ";
			$db_dpis2->send_cmd($cmd);
			while($data2 = $db_dpis2->get_array()) $ARR_SUBSTITUTE_APPROVE[] = $data2[PER_ID];
			
			list_approve_substitute($data[PER_ID]);
		} // end while
		
		return;
	}
?>