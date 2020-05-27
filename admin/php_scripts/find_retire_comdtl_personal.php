<?
//	include("../php_scripts/connect_database.php");
//	include("php_scripts/function_share.php");
//	include("php_scripts/function_list.php");		

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
		$LEVEL_NO = $data[LEVEL_NO];
		$CMD_OLD_SALARY = trim($data[PER_SALARY]);
		
	  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$Level = $db_dpis->get_array();				
		$LEVEL_NAME = trim($Level[LEVEL_NAME]);		
		
		//echo $cmd;
		
		if ($PER_TYPE == 1) {
			$cmd = "	select	POS_NO, POS_NO_NAME, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, a.PL_CODE, PM_CODE, DEPARTMENT_ID, LEVEL_NO 
							from		PER_POSITION a, PER_LINE b 
							where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";		
			$db_dpis->send_cmd($cmd);		
			$data = $db_dpis->get_array();
			$CMD_POSITION = trim($data[PL_NAME]);
			$CMD_POSPOEM_NO_NAME = trim($data[POS_NO_NAME]);
			$CMD_POSPOEM_NO = trim($data[POS_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PM_CODE = trim($data[PM_CODE]);
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	POEM_NO, POEM_NO_NAME, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, a.LEVEL_NO, b.PG_CODE 
							from		PER_POS_EMP a, PER_POS_NAME b 
							where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
			$db_dpis->send_cmd($cmd);		
			$data = $db_dpis->get_array();
			$CMD_POSITION = trim($data[PN_NAME]);
			$CMD_POSPOEM_NO_NAME = trim($data[POEM_NO_NAME]);
			$CMD_POSPOEM_NO = trim($data[POEM_NO]);
			$PG_CODE = trim($data[PG_CODE]);
		} elseif ($PER_TYPE == 3) {
			$cmd = "	select	POEMS_NO, POEMS_NO_NAME, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
			$db_dpis->send_cmd($cmd);		
			$data = $db_dpis->get_array();
			$CMD_POSITION = trim($data[EP_NAME]);
			$CMD_POSPOEM_NO_NAME = trim($data[POEMS_NO_NAME]);
			$CMD_POSPOEM_NO = trim($data[POEMS_NO]);
		} elseif ($PER_TYPE == 4) {
			$cmd = "	select	POT_NO, POT_NO_NAME, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
							where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
			$db_dpis->send_cmd($cmd);		
			$data = $db_dpis->get_array();
			$CMD_POSITION = trim($data[TP_NAME]);
			$CMD_POSPOEM_NO_NAME = trim($data[POT_NO_NAME]);
			$CMD_POSPOEM_NO = trim($data[POT_NO]);
		}
		$LEVEL_NO_POS = trim($data[LEVEL_NO]);
		if(trim($LEVEL_NO_POS)){	
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO_POS' ";
			$db_dpis->send_cmd($cmd);
			$Level = $db_dpis->get_array();				
			$LEVEL_NAME_POS = trim($Level[LEVEL_NAME]);	
		}
		
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];	
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];	
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		$cmd = " select OT_CODE from PER_ORG where ORG_ID=$POS_ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OT_CODE = trim($data[OT_CODE]);
		
		if ($POS_ORG_ID)			$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)	$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)	$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)	$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);

		$cmd = "	select		ORG_ID, ORG_NAME 
						from			PER_ORG 
						where		ORG_ID in ($search_org_id) ";
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
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
		
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME = $data[PM_NAME];
		$CMD_PM_NAME = $PM_NAME;
		
		$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		
		if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$CMD_POSITION.$POSITION_LEVEL.")";
		else $PL_NAME_WORK = $CMD_POSITION.$POSITION_LEVEL;
		
		if ($ORG_NAME=="-") $ORG_NAME = "";		
		if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
		if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
		if ($OT_CODE == "03") 
			if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="�����û���ͧ") 
				$ORG_NAME_WORK = "���ӡ�û���ͧ".$ORG_NAME." ".$ORG_NAME;
			else 
				$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
		elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
		else $ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
			 
		// ����繡����������Ҫ���/�١��ҧ��Ш� ����Ѻ�ѭ��Ṻ���¤��������͹����Թ��͹
		// �� select ੾���Թ��͹��дѺ�ͧ����Ҫ���/�١��ҧ��Ш� ���ʴ���� listbox �Թ��͹
		if ($sel_salary) {
			$arr = 1;		$selectfield="";
			if ($PER_TYPE == 2) {	
				$selectfield="LAYERE_SALARY";
				$cmd = " select $selectfield from PER_LAYEREMP where (PG_CODE='$PG_CODE' AND LAYERE_ACTIVE=1) order by LAYERE_NO ";
			} else{
				$selectfield="LAYER_SALARY";
				$cmd = " select $selectfield from PER_LAYER where LEVEL_NO='$LEVEL_NO' and LAYER_ACTIVE=1 order by LAYER_NO ";
			}
			$count_layer_salary = $db_dpis->send_cmd($cmd);
			if ($count_layer_salary)  $cmd_salary_list .= "parent.document.form1.CMD_SALARY.options[0] = new Option('== ���͡�ѵ���Թ��͹ ==','');";
			while ( $data = $db_dpis->get_array() ) {
				$layer_salary_value = $data[$selectfield];
				$layer_salary_show = number_format($data[$selectfield], 2, ".", ",");
				$cmd_salary_list .= " parent.document.form1.CMD_SALARY.options[$arr] = new Option('$layer_salary_show','$layer_salary_value'); ";
				if ($CMD_OLD_SALARY==$layer_salary_value) $cmd_salary_list .= " parent.document.form1.CMD_SALARY.selectedIndex = $arr; ";
				$arr++;				
			}
		}
	}	// end if (trim($PER_ID)) 
			
	$CMD_LEVEL = $LEVEL_NO_POS;		//�дѺ���˹� 
	$CMD_LEVEL_NAME = $LEVEL_NAME_POS;	//�����дѺ���˹� 
	$CMD_LEVEL1 = $LEVEL_NO_POS;			//�дѺ���˹� 
	$CMD_LEVEL3 = $LEVEL_NAME_POS;		//�����дѺ���˹� 
		
	$CMD_ORG3 = $ORG_NAME;			
	$CMD_ORG4 = $ORG_NAME_1;		
	$CMD_ORG5 = $ORG_NAME_2;		
	$CMD_ORG6 = $ORG_NAME_3;
	$CMD_ORG7 = $ORG_NAME_4;
	$CMD_ORG8 = $ORG_NAME_5;
	$CMD_SALARY = $cmd_salary_list;
?>
