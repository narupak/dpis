<?
	if( trim($ACTH_POS_ID_ASSIGN) ) {
		$cmd = "	select 	 LEVEL_NO 
				from 	PER_PERSONAL 
				where 	POS_ID=$ACTH_POS_ID_ASSIGN and PER_TYPE=1 and PER_STATUS=1  ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$LEVEL_NO_ASSIGN = trim($data[LEVEL_NO]);
				
		$cmd = "	select	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
						ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
						from		PER_POSITION a, PER_LINE b 
						where	POS_ID=$ACTH_POS_ID_ASSIGN and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$PL_NAME_ASSIGN = trim($data[PL_NAME]); 
		$ACTH_POS_NO_ASSIGN = trim($data[POS_NO]);
		$PL_CODE_ASSIGN = trim($data[PL_CODE]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];	
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];
		$PM_CODE_ASSIGN = trim($data[PM_CODE]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($POS_ORG_ID)		$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)	$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)	$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)	$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$DEPARTMENT_NAME = $POS_ORG_NAME = $POS_ORG_NAME_1 = $POS_ORG_NAME_2 = $POS_ORG_NAME_3 = $POS_ORG_NAME_4 = $POS_ORG_NAME_5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
		$db_dpis->send_cmd($cmd);		
		while ( $data = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data[ORG_ID]))? 		trim($data[ORG_NAME]) : "$DEPARTMENT_NAME";
			$POS_ORG_NAME = ($POS_ORG_ID == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME ;
			$POS_ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_1 ;
			$POS_ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_2 ;						
			$POS_ORG_NAME_3 = ($POS_ORG_ID_3 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_3 ;
			$POS_ORG_NAME_4 = ($POS_ORG_ID_4 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_4 ;
			$POS_ORG_NAME_5 = ($POS_ORG_ID_5 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_5 ;	
		}

		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE_ASSIGN' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME_ASSIGN = $data[PM_NAME];
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
			
		$ORG_ID_1_ASSIGN = $MINISTRY_ID;
		$ORG_NAME_1_ASSIGN = $MINISTRY_NAME;
		$ORG_ID_2_ASSIGN = $DEPARTMENT_ID;
		$ORG_NAME_2_ASSIGN = $DEPARTMENT_NAME;
		$ORG_ID_3_ASSIGN = $POS_ORG_ID;
		$ORG_NAME_3_ASSIGN = $POS_ORG_NAME;
		$ORG_NAME_4_ASSIGN = $POS_ORG_NAME_1;
		$ORG_NAME_5_ASSIGN = $POS_ORG_NAME_2;
		if($ORG_NAME_6_ASSIGN)	$CMD_ORG6 = $POS_ORG_NAME_3;
		if($ORG_NAME_7_ASSIGN)	$CMD_ORG7 = $POS_ORG_NAME_4;
		if($ORG_NAME_8_ASSIGN)	$CMD_ORG8 = $POS_ORG_NAME_5;
	}

	if( trim($ACTH_POS_ID) ) {
		$cmd = "	select 	 LEVEL_NO 
				from 	PER_PERSONAL 
				where 	POS_ID=$ACTH_POS_ID and PER_TYPE=1 and PER_STATUS=1  ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$LEVEL_NO = trim($data[LEVEL_NO]);
				
		$cmd = "	select	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
						ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
						from		PER_POSITION a, PER_LINE b 
						where	POS_ID=$ACTH_POS_ID and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PL_NAME]); 
		$ACTH_POS_NO = trim($data[POS_NO]);
		$PL_CODE = trim($data[PL_CODE]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];	
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];
		$PM_CODE = trim($data[PM_CODE]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($POS_ORG_ID)		$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)	$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)	$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)	$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$DEPARTMENT_NAME = $POS_ORG_NAME = $POS_ORG_NAME_1 = $POS_ORG_NAME_2 = $POS_ORG_NAME_3 = $POS_ORG_NAME_4 = $POS_ORG_NAME_5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
		$db_dpis->send_cmd($cmd);		
		while ( $data = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data[ORG_ID]))? 		trim($data[ORG_NAME]) : "$DEPARTMENT_NAME";
			$POS_ORG_NAME = ($POS_ORG_ID == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME ;
			$POS_ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_1 ;
			$POS_ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_2 ;						
			$POS_ORG_NAME_3 = ($POS_ORG_ID_3 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_3 ;
			$POS_ORG_NAME_4 = ($POS_ORG_ID_4 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_4 ;
			$POS_ORG_NAME_5 = ($POS_ORG_ID_5 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_5 ;	
		}

		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME = $data[PM_NAME];
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
			
		$ORGATH_ID_1 = $MINISTRY_ID;
		$ORG_NAME_1 = $MINISTRY_NAME;
		$ORG_ID_2 = $DEPARTMENT_ID;
		$ORG_NAME_2 = $DEPARTMENT_NAME;
		$ORG_ID_3 = $POS_ORG_ID;
		$ORG_NAME_3 = $POS_ORG_NAME;
		$ORG_NAME_4 = $POS_ORG_NAME_1;
		$ORG_NAME_5 = $POS_ORG_NAME_2;
		if($ORG_NAME_6)	$CMD_ORG6 = $POS_ORG_NAME_3;
		if($ORG_NAME_7)	$CMD_ORG7 = $POS_ORG_NAME_4;
		if($ORG_NAME_8)	$CMD_ORG8 = $POS_ORG_NAME_5;
	}

?>