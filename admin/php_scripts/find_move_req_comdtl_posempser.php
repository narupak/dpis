<?
//	include("../../php_scripts/connect_database.php");
//	include("session_start.php");
	
//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	if( trim($PER_ID) ) {
		$cmd = " select 	POEMS_ID from PER_PERSONAL 	where PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);	
		$data = $db_dpis->get_array();
		$POEMS_ID = trim($data[POEMS_ID]);
	}
        $POEMS_ID=$POS_POEM_ID;/*Release 5.1.0.8*/
	if(trim($POEMS_ID)){
		$cmd = " 	select 	ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5,  EP_CODE, DEPARTMENT_ID 
							from  	PER_POS_EMPSER  
							where POEMS_ID=$POEMS_ID ";
               // echo '<br>'.$cmd;
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();

		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$ORG_ID_4 = trim($data[ORG_ID_4]);
		$ORG_ID_5 = trim($data[ORG_ID_5]);
		$EP_CODE = trim($data[EP_CODE]);

		$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OT_CODE = trim($data[OT_CODE]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($ORG_ID)			$tmp_ORG_ID[] =  $ORG_ID;
		if ($ORG_ID_1)		$tmp_ORG_ID[] =  $ORG_ID_1;
		if ($ORG_ID_2)		$tmp_ORG_ID[] =  $ORG_ID_2;
		if ($ORG_ID_3)		$tmp_ORG_ID[] =  $ORG_ID_3;
		if ($ORG_ID_4)		$tmp_ORG_ID[] =  $ORG_ID_4;
		if ($ORG_ID_5)		$tmp_ORG_ID[] =  $ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 =   $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 =  "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
               
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ( $data1 = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$DEPARTMENT_NAME";
			$ORG_NAME = 		($ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME";
			$ORG_NAME_1 = 	($ORG_ID_1 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_1";
			$ORG_NAME_2 = 	($ORG_ID_2 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_2";
			$ORG_NAME_3 = 	($ORG_ID_3 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_3";
			$ORG_NAME_4 = 	($ORG_ID_4 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_4";
			$ORG_NAME_5 = 	($ORG_ID_5 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_5";
		}	// while		
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
		
		$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EP_NAME = $data[EP_NAME];
		
		$cmd = " select a.LEVEL_NO, LEVEL_NAME, POSITION_LEVEL 
						from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO and a.PER_TYPE=3 and PER_STATUS=1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		$LEVEL_NO = $data[LEVEL_NO];
		
		$PL_NAME_WORK = $EP_NAME." ".$POSITION_LEVEL;
		
		if ($ORG_NAME=="-") $ORG_NAME = "";		
		if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
		if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
		if ($OT_CODE == "03") 
			if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="�����û���ͧ") 
				$ORG_NAME_WORK = "���ӡ�û���ͧ".$ORG_NAME." ".$ORG_NAME;
			else 
				$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
		elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
		else $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME);
		
		if ($PROGRAM_NAME=="data_promote_e_p")			
			$PL_NAME_WORK = "����͹����觵������ç���˹� ". $PL_NAME_WORK;
		elseif ($PROGRAM_NAME=="data_transfer_req")			 
			$PL_NAME_WORK = $MOV_NAME . " ����觵������ç���˹� " . $PL_NAME_WORK;
	} 		// if 
	if(!$formName)	$formName="form1";
	
	$POS_POEM_ORG1 = $MINISTRY_NAME;
	$POS_POEM_ORG2 = $DEPARTMENT_NAME;
	$POS_POEM_ORG3 = $ORG_NAME;
	$POS_POEM_ORG4 = $ORG_NAME_1;
	$POS_POEM_ORG5 = $ORG_NAME_2;
	if($POS_POEM_ORG6) $POS_POEM_ORG6 = $ORG_NAME_3;
	if($POS_POEM_ORG7) $POS_POEM_ORG7 = $ORG_NAME_4;
	if($POS_POEM_ORG8) $POS_POEM_ORG8 = $ORG_NAME_5;
	$POS_POEM_NAME = $EP_NAME;
        
        
        /*��˹���˹��§ҹ����ͺ���� Release 5.2.1.8 Begin*/
	/*�ç���ҧ����ͺ���§ҹ �ӹѡ/�ͧ*/
	$ORG_NAME_ASS='';
	$ORG_ID_ASS3='';
	if($ORG_NAME){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_ASS= $data_ass[ORG_NAME];
			$ORG_ID_ASS3= $data_ass[ORG_ID];
		}
	}
	/*�ç���ҧ����ͺ���§ҹ ��ӡ����ӹѡ/�ͧ 1 �дѺ*/
	$ORG_NAME_1_ASS='';
	$ORG_ID_1_ASS4='';
	if($ORG_NAME_1){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME_1)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_1_ASS= $data_ass[ORG_NAME];
			$ORG_ID_1_ASS4= $data_ass[ORG_ID];
		}
	}
	/*�ç���ҧ����ͺ���§ҹ ��ӡ����ӹѡ/�ͧ 2 �дѺ*/
	$ORG_NAME_2_ASS='';
	$ORG_ID_2_ASS5='';
	if($ORG_NAME_2){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME_2)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_2_ASS= $data_ass[ORG_NAME];
			$ORG_ID_2_ASS5= $data_ass[ORG_ID];
		}
	}
	/*Release 5.2.1.8  End*/
        
/*	$LEVEL_NO = $LEVEL_NO;	
	$LEVEL_NAME = $LEVEL_NAME;
	$ES_CODE = $ES_CODE;
	$ES_NAME = $ES_NAME;
	$PL_NAME_WORK = $PL_NAME_WORK;	
	$ORG_NAME_WORK = $ORG_NAME_WORK;	
*/
?>
