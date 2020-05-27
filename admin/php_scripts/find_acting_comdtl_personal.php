<?
//	include("../php_scripts/connect_database.php");
//	include("php_scripts/function_share.php");

//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if( trim($PER_ID) ) {
		$cmd = "	select 	PER_CARDNO, PER_BIRTHDATE, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, PER_SALARY  
				from 	PER_PERSONAL 
				where 	PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_TYPE = trim($data[PER_TYPE]);		
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);
		$CMD_OLD_SALARY = trim($data[PER_SALARY]);		
	
		$LEVEL_NO = trim($data[LEVEL_NO]);
	  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();					
		$LEVEL_NAME = trim($data[LEVEL_NAME]);		
				
		if ($PER_TYPE == 1) {
			$cmd = "	select	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
							from		PER_POSITION a, PER_LINE b 
							where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	POEM_NO_NAME, POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, DEPARTMENT_ID  
							from		PER_POS_EMP a, PER_POS_NAME b 
							where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
		} elseif ($PER_TYPE == 3) {
			$cmd = "	select	POEMS_NO_NAME, POEMS_NO, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
		}
		elseif ($PER_TYPE == 4) {
			$cmd = "	select	POT_NO_NAME, POT_NO, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
							where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
		}
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		if($PER_TYPE == 1) { 
			$CMD_POSITION = trim($data[PL_NAME]); 
			$CMD_POSPOEM_NO = trim($data[POS_NO]);
			$CMD_POSPOEM_NO_NAME = trim($data[POS_NO_NAME]);
			$PL_PN_CODE = trim($data[PL_CODE]);
		} else if($PER_TYPE == 2) { 
			$CMD_POSITION = trim($data[PN_NAME]); 
			$CMD_POSPOEM_NO = trim($data[POEM_NO]);
			$CMD_POSPOEM_NO_NAME = trim($data[POEM_NO_NAME]);
			$PL_PN_CODE = trim($data[PN_CODE]);
		} else if($PER_TYPE == 3) { 
			$CMD_POSITION = trim($data[EP_NAME]); 
			$CMD_POSPOEM_NO = trim($data[POEMS_NO]);
			$CMD_POSPOEM_NO_NAME = trim($data[POEMS_NO_NAME]);
			$PL_PN_CODE = trim($data[EP_CODE]);
		} else if($PER_TYPE == 4) { 
			$CMD_POSITION = trim($data[TP_NAME]); 
			$CMD_POSPOEM_NO = trim($data[POT_NO]);
			$CMD_POSPOEM_NO_NAME = trim($data[POT_NO_NAME]);
			$PL_PN_CODE = trim($data[TP_CODE]);
		} 
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];	
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = $PL_PN_CODE;
		
		$LEVEL_NO_POS = trim($data[LEVEL_NO]);
		if(trim($LEVEL_NO_POS)){
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO_POS' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();					
			$LEVEL_NAME_POS = trim($data[LEVEL_NAME]);		
		}

		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($POS_ORG_ID)		$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)	$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)	$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)	$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
		$db_dpis->send_cmd($cmd);		
		while ( $data = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data[ORG_ID]))? 		trim($data[ORG_NAME]) : "$DEPARTMENT_NAME";
			$ORG_NAME = ($POS_ORG_ID == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME ;
			$ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_1 ;
			$ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_2 ;						
			$ORG_NAME_3 = ($POS_ORG_ID_3 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_3 ;
			$ORG_NAME_4 = ($POS_ORG_ID_4 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_4 ;
			$ORG_NAME_5 = ($POS_ORG_ID_5 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_5 ;	
		}
	}

	$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$CMD_PM_NAME = $data[PM_NAME];
	
	$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_ID = $data[ORG_ID_REF];
	
	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];
		
//	$PER_CARDNO = $PER_CARDNO;
//	$PER_BIRTHDATE = $PER_BIRTHDATE;
//	$PER_TYPE = $PER_TYPE;

//	$PL_PN_CODE = $PL_PN_CODE;
//	$CMD_POSITION = $CMD_POSITION;
//	if($CMD_POSPOEM_NO_NAME)	$CMD_POSPOEM_NO_NAME = $CMD_POSPOEM_NO_NAME;	
//	$CMD_POSPOEM_NO = $CMD_POSPOEM_NO;	
	$CMD_LEVEL2 = $LEVEL_NAME;
	$CMD_LEVEL = $LEVEL_NO;	
	$CMD_LEVEL3 = $LEVEL_NAME_POS;
	$CMD_LEVEL1 = $LEVEL_NO_POS;	
	$CMD_OLD_SALARY = $CMD_OLD_SALARY;
	$CMD_ORG1 = $MINISTRY_NAME;
	$CMD_ORG2 = $DEPARTMENT_NAME;
	$CMD_ORG3 = $ORG_NAME;
	$CMD_ORG4 = $ORG_NAME_1;
	$CMD_ORG5 = $ORG_NAME_2;
	if($CMD_ORG6)	$CMD_ORG6 = $ORG_NAME_3;
	if($CMD_ORG7)	$CMD_ORG7 = $ORG_NAME_4;
	if($CMD_ORG8)	$CMD_ORG8 = $ORG_NAME_5;
?>