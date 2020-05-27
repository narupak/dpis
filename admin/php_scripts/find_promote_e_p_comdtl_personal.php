<?
//	include("../php_scripts/connect_database.php");
//	include("php_scripts/function_share.php");
//	include("php_scripts/function_list.php");		
	
//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	function load_default_en_code($per_id) {
		global $db_dpis;
		
		$cmd = " select b.EN_CODE, b.EDU_TYPE from PER_EDUCNAME a, PER_EDUCATE b  
						where 	EN_ACTIVE=1 and a.EN_CODE=b.EN_CODE and PER_ID=$per_id
						order by	b.EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data_count = 0;
		$ret_value = "";
		while($data = $db_dpis->get_array()) {
			$data_count++;
			$TMP_EN_CODE = trim($data[EN_CODE]);
			if (strpos($data[EDU_TYPE],"2") !== false) { // มีค่า 2 ใน EDU_TYPE
				$ret_value = "$ret_value,$TMP_EN_CODE";
			}
		} // end loop while $data
		$ret_value = substr($ret_value,1); // ตัด comma ตัวแรกออก
		
		return $ret_value;
	}

	if( trim($PER_ID) ) {
		$cmd = "	select 	PER_CARDNO, PER_BIRTHDATE, PER_TYPE, POS_ID, POEM_ID, LEVEL_NO, PER_SALARY, ES_CODE  
						from 		PER_PERSONAL 
						where 	PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_TYPE = trim($data[PER_TYPE]);		
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		
		$CMD_OLD_SALARY = trim($data[PER_SALARY]);
		$ES_CODE = trim($data[ES_CODE]);

		$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE = '$ES_CODE' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$ES_NAME = trim($data1[ES_NAME]);
	  	
		$LEVEL_NO = $data[LEVEL_NO];
		if(trim($LEVEL_NO)){	
			$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();					
			$LEVEL_NAME = trim($data[LEVEL_NAME]);		
		}
            
		$EN_CODE = load_default_en_code($PER_ID);
		//echo "[".$PER_TYPE."]";
		if ($PER_TYPE == 1) {
			$cmd = "	select	POS_NO, POS_NO_NAME, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID, LEVEL_NO 
							from		PER_POSITION a, PER_LINE b 
							where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
							
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	POEM_NO, POEM_NO_NAME, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
							from		PER_POS_EMP a, PER_POS_NAME b 
							where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
		}elseif ($PER_TYPE == 3) {
			$cmd = "	select	POEMS_NO, POEMS_NO_NAME, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
		}elseif ($PER_TYPE == 4) {
			$cmd = "	select	POT_NO, POT_NO_NAME, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
							where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
		}
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		//$CMD_POSITION 			= ($PER_TYPE == 1)? 		trim($data[PL_NAME]) : trim($data[PN_NAME]);
		if($PER_TYPE == 1) { $CMD_POSITION = trim($data[PL_NAME]); } 
		else if($PER_TYPE == 2) { $CMD_POSITION = trim($data[PN_NAME]); } 
		else if($PER_TYPE == 3) { $CMD_POSITION = trim($data[EP_NAME]); } 
		else if($PER_TYPE == 4) { $CMD_POSITION = trim($data[TP_NAME]); } 
		
		//$CMD_POSPOEM_NO 	= ($PER_TYPE == 1)?		trim($data[POS_NO]) : trim($data[POEM_NO]);
		if($PER_TYPE == 1) { $CMD_POSPOEM_NO = trim($data[POS_NO]);	$CMD_POSPOEM_NO_NAME = trim($data[POS_NO_NAME]); 	} 
		else if($PER_TYPE == 2) { $CMD_POSPOEM_NO = trim($data[POEM_NO]);	$CMD_POSPOEM_NO_NAME = trim($data[POEM_NO_NAME]); 				} 
		else if($PER_TYPE == 3) { $CMD_POSPOEM_NO = trim($data[POEMS_NO]);		$CMD_POSPOEM_NO_NAME = trim($data[POEMS_NO_NAME]); 			} 
		else if($PER_TYPE == 4) { $CMD_POSPOEM_NO = trim($data[POT_NO]);			$CMD_POSPOEM_NO_NAME = trim($data[POT_NO_NAME]); 						} 

		$LEVEL_NO_POS = trim($data[LEVEL_NO]);
                /*เดิม*/
                /*
                 if(trim($LEVEL_NO_POS)){	
			$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO) = '$LEVEL_NO_POS' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();					
			$LEVEL_NAME_POS = trim($data1[LEVEL_NAME]);		
		}
                 */
		if(trim($LEVEL_NO_POS)){	
			$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO) = '$LEVEL_NO_POS' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();					
			$LEVEL_NAME_POS = trim($data1[LEVEL_NAME]);		
		}else{
                    $LEVEL_NAME_POS =$LEVEL_NAME;
                }
		
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$POS_ORG_ID = trim($data[ORG_ID]);
		$POS_ORG_ID_1 = trim($data[ORG_ID_1]);
		$POS_ORG_ID_2 = trim($data[ORG_ID_2]);	
		$POS_ORG_ID_3 = trim($data[ORG_ID_3]);
		$POS_ORG_ID_4 = trim($data[ORG_ID_4]);
		$POS_ORG_ID_5 = trim($data[ORG_ID_5]);	
		$PM_CODE = trim($data[PM_CODE]);
		$PL_CODE = trim($data[PL_CODE]);
//		echo "PL_CODE=$PL_CODE<br>";
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($POS_ORG_ID)			$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)		$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)		$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)		$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)		$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)		$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
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
		
//		$PER_CARDNO = $PER_CARDNO;
//		$PER_BIRTHDATE = $PER_BIRTHDATE;
//		$PER_TYPE = $PER_TYPE;

//		$EN_CODE = $EN_CODE;

//		$CMD_POSITION = $CMD_POSITION;
//		$CMD_POSPOEM_NO = $CMD_POSPOEM_NO;	
//		if($CMD_POSPOEM_NO_NAME)	$CMD_POSPOEM_NO_NAME = $CMD_POSPOEM_NO_NAME;	
		$CMD_LEVEL2 = $LEVEL_NAME;	
		$CMD_LEVEL = $LEVEL_NO;
		$CMD_LEVEL3 = $LEVEL_NAME_POS;
		$CMD_LEVEL1 = $LEVEL_NO_POS;
//		$CMD_OLD_SALARY = $CMD_OLD_SALARY;
		$CMD_SALARY = $CMD_OLD_SALARY;
		$CMD_ORG1 = $MINISTRY_NAME;
		$CMD_ORG2 = $DEPARTMENT_NAME;
		$CMD_ORG3 = $ORG_NAME;
		$CMD_ORG4 = $ORG_NAME_1;
		$CMD_ORG5 = $ORG_NAME_2;
		if($CMD_ORG6) $CMD_ORG6 = $ORG_NAME_3;
		if($CMD_ORG7) $CMD_ORG7 = $ORG_NAME_4;
		if($CMD_ORG8) $CMD_ORG8 = $ORG_NAME_5;
		$CMD_ES_CODE = $ES_CODE;
		$CMD_ES_NAME = $ES_NAME;
//		$CMD_PM_NAME = $CMD_PM_NAME;
?>
<script>
//	<? if (!$RPT_N) { ?>
//	parent.change_salary_bylevel('<?=$LEVEL_NO?>', '<?=$PER_SALARY?>');
//	<? } ?>
//	parent.select_salary_inputtype('<?=$RPT_N?>', '<?=$PER_TYPE?>', '<?=$LEVEL_NO?>');
</script>