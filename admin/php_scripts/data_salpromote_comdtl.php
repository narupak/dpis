<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//===tab ��� 2 - form11 - show_topic=2
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$ORG_ID_DTL = $ORG_ID;
		$ORG_NAME_DTL = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
		$ORG_ID_DTL = "";	//$ORG_ID_DTL = "0";
		$ORG_NAME_DTL = "";
	}

	if (!$DEPARTMENT_ID) $DEPARTMENT_ID = "NULL";
	if (is_null($ORG_ID) || $ORG_ID=="NULL"|| trim($ORG_ID)=="") $ORG_ID=0;
	if ($_POST[ORG_ID_DTL]!="") $ORG_ID_DTL = $_POST[ORG_ID_DTL];
	if (!$ORG_ID_DTL) $ORG_ID_DTL = "NULL";

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$list_type = (trim($list_type))? $list_type : 4;	
	
	if(!$SALQ_YEAR&&$_POST[SALQ_YEAR])	$SALQ_YEAR = $_POST[SALQ_YEAR];	
	$SALQ_TIME = (trim($SALQ_TIME))? $SALQ_TIME : 1;	
	
	if ($COM_PER_TYPE==1)	$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	else $COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 7; 
	
	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
		if ($SALQ_TYPE == 1) $SALQ_TYPE = 3;
		elseif ($SALQ_TYPE == 2) $SALQ_TYPE = 4;
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);
                
		// SALQ_YEAR �������ç�˹ ????   !!!!!!!!!!!!�ѧ����տ��촷������ SALQ_YEAR ���� table
		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, UPDATE_USER, UPDATE_DATE, COM_LEVEL_SALP ,COM_YEAR) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, $ORG_ID_DTL, $SESS_USERID, '$UPDATE_DATE', $COM_LEVEL_SALP , ($SALQ_YEAR-543)) ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "=> $cmd <br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
//		echo "$ORG_ID_DTL // $RPT_N // $SALQ_YEAR //   $ORG_ID --- $ORG_ID_ASS ";

		// ===== ������� insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) =====
		if ($RPT_N) {
			if($_POST[CT_CODE]) $CT_CODE=$_POST[CT_CODE];	 
			if($_POST[PV_CODE]) $PV_CODE=$_POST[PV_CODE];

			if ($list_type==1) $where = " and d.OT_CODE = '01' ";		
			elseif ($list_type==2) $where = " and d.PV_CODE = '$PV_CODE' ";		
			elseif ($list_type==3) $where = " and d.CT_CODE = '$CT_CODE' ";		

			if ($DEPARTMENT_ID && $DEPARTMENT_ID!="NULL") $where .= " and b.DEPARTMENT_ID = $DEPARTMENT_ID ";

			if ($COM_LEVEL_SALP == 1) {
				if ($BKK_FLAG==1) { // 1=�дѺ��Ժѵԧҹ �ӹҭ�ҹ ������ ��Ժѵԡ�� �ӹҭ��� 
					$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('O1','O2','O3','K1','K2') ";		
				} else { // 1=�дѺ��Ժѵԧҹ �ӹҭ�ҹ ������ ��Ժѵԡ�� �ӹҭ��� �ӹҭ��þ���� �ӹ�¡�õ�
					$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('O1','O2','O3','K1','K2','K3','D1') ";		
				}
			} elseif ($COM_LEVEL_SALP == 2) {					// 2=�дѺ�ѡ�о���� ����Ǫҭ �ç�س�ز� �ӹ�¡���٧ �����õ� �������٧
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('O4','K4','K5','D2','M1','M2') ";
			} elseif ($COM_LEVEL_SALP == 3) { 					
				if ($BKK_FLAG==1) { // 3=�Թ��������� �дѺ��Ժѵԧҹ �ӹҭ�ҹ ������ ��Ժѵԡ�� �ӹҭ��� 
					$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('O1','O2','O3','K1','K2') ";	
				} else { // 3=�Թ��������� �дѺ��Ժѵԧҹ �ӹҭ�ҹ ������ ��Ժѵԡ�� �ӹҭ��� �ӹҭ��þ���� �ӹ�¡�õ�
					$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('O1','O2','O3','K1','K2','K3','D1') ";	
				}
			} elseif ($COM_LEVEL_SALP == 4) {					// 4=�Թ��������� �дѺ�ѡ�о���� ����Ǫҭ �ç�س�ز� �ӹ�¡���٧ �����õ� �������٧
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('O4','K4','K5','D2','M1','M2') ";	
			} elseif ($COM_LEVEL_SALP == 5) {					// 5=���³
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			} elseif ($COM_LEVEL_SALP == 6) {					// 6=����Ҫ��÷�����
				$where_com_level_salp = "";		
			} elseif ($COM_LEVEL_SALP == 7) {					// 7=����
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 ";
			} elseif ($COM_LEVEL_SALP == 8) {					// 8=�Թ���������
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 ";
			} elseif ($COM_LEVEL_SALP == 9) {					// 9=���³
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			} elseif ($COM_LEVEL_SALP == 10) {				// 10=�١��ҧ��Ш����;�ѡ�ҹ�Ҫ��÷�����
				$where_com_level_salp = "";
			} elseif ($COM_LEVEL_SALP == 11) {				// 11=�дѺ�ӹҭ��þ���� �ӹ�¡�õ�
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('K3','D1') ";		
			} elseif ($COM_LEVEL_SALP == 13) { 				// 13=�Թ��������� �дѺ�ӹҭ��þ���� �ӹ�¡�õ� 	
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('K3','D1') ";	
			}
		} else {
			if ($COM_LEVEL_SALP == 1)					// 1=�дѺ 8 ŧ��
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO_SALARY <= '08' ";		
			elseif ($COM_LEVEL_SALP == 2)					// 2=�дѺ 9 ����
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO_SALARY >= '09' ";
			elseif ($COM_LEVEL_SALP == 3) 				// 3=�Թ��������� �дѺ 8 ŧ��
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO_SALARY <= '08' ";	
			elseif ($COM_LEVEL_SALP == 4)					// 4=�Թ��������� �дѺ 9 ����
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO_SALARY >= '09' ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=���³
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=����Ҫ��÷�����
				$where_com_level_salp = "";		
			elseif ($COM_LEVEL_SALP == 7)					// 7=����
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 ";
			elseif ($COM_LEVEL_SALP == 8)					// 8=�Թ���������
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 ";
			elseif ($COM_LEVEL_SALP == 9)					// 9=���³
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			elseif ($COM_LEVEL_SALP == 10)				// 10=�١��ҧ��Ш����;�ѡ�ҹ�Ҫ��÷�����
				$where_com_level_salp = "";
		}		
		/*
		if ($COM_LEVEL_SALP == 5 || $COM_LEVEL_SALP == 9) {		// 5, 9=���³
			$where_com_level_salp = "";		
			$ORG_ID = $ORG_ID_ASS = "";
		}
		*/

		if ($ORG_ID || $ORG_ID==0) {					
			$cmd1 = " select	a.PER_ID, SALP_LEVEL, SALP_DATE, SALP_SALARY_OLD, SALP_SALARY_NEW, SALP_SPSALARY, 
							SALP_PERCENT, SALP_REMARK, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
					   from 	PER_SALPROMOTE a, PER_PERSONAL b, $position_table c , PER_ORG d
					   where 	a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.PER_ID=b.PER_ID and  
									$position_join and c.ORG_ID = d.ORG_ID 
						$where_com_level_salp $where ";
		} elseif ($ORG_ID_ASS) {			
			$cmd1 = " select	a.PER_ID, SALP_LEVEL, SALP_DATE, SALP_SALARY_OLD, SALP_SALARY_NEW, SALP_SPSALARY, 
							SALP_PERCENT, SALP_REMARK, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
					   from 	PER_SALPROMOTE a, PER_PERSONAL b, PER_ORG_ASS d 
					   where 	a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.PER_ID=b.PER_ID and 
							b.ORG_ID = d.ORG_ID 
							$where_com_level_salp $where ";		
		}
		$count_temp = $db_dpis->send_cmd($cmd1);
//		$db_dpis->show_error();
//echo "==========>         $cmd1<br><br>";

		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$TMP_PER_ID = $data[PER_ID] + 0;		
			$CMD_OLD_SALARY = $data[SALP_SALARY_OLD] + 0;
			$CMD_SALARY = $data[SALP_SALARY_NEW] + 0;
			$CMD_SPSALARY = $data[SALP_SPSALARY] + 0;
			$TMP_SALP_LEVEL = $data[SALP_LEVEL] + 0;
			$CMD_DATE = trim($data[SALP_DATE]);
			if ($BKK_FLAG==1) {
				$cmd = " 	select MOV_CODE from PER_MOVMENT 
									where trim(MOV_NAME)='����͹��� $TMP_SALP_LEVEL ���' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MOV_CODE = trim($data2[MOV_CODE]);
				if (!$MOV_CODE) {
					if ($TMP_SALP_LEVEL == 0.5) 		$MOV_CODE = "SA1032";
					elseif ($TMP_SALP_LEVEL == 1)		$MOV_CODE = "SA1033";
					elseif ($TMP_SALP_LEVEL == 1.5) 	$MOV_CODE = "SA1016";
					elseif ($TMP_SALP_LEVEL == 2) 		$MOV_CODE = "SA1012";
				}
				$TMP_SALP_PERCENT = $data[SALP_PERCENT] + 0;				
				if ($TMP_SALP_PERCENT == 2)		$MOV_CODE = "SA4002";
				elseif ($TMP_SALP_PERCENT == 4)	$MOV_CODE = "SA4004";
			} else {
				if ($TMP_SALP_LEVEL == 0.5) {
					if ($COM_PER_TYPE==2)	{
						if (substr($CMD_DATE,5,5)=="04-01") $MOV_CODE = "24010";
						else $MOV_CODE = "24050";
					} else $MOV_CODE = "21310";
				} elseif ($TMP_SALP_LEVEL == 1) {								
					if ($COM_PER_TYPE==2)	{
						if (substr($CMD_DATE,5,5)=="04-01") $MOV_CODE = "24020";
						else $MOV_CODE = "24030";
					} else $MOV_CODE = "21320";
				} elseif ($TMP_SALP_LEVEL == 1.5) {								
					if ($COM_PER_TYPE==2)	$MOV_CODE = "24040";
					else $MOV_CODE = "21330";
				} elseif ($TMP_SALP_LEVEL == 2) 		$MOV_CODE = "21340";
				$TMP_SALP_PERCENT = $data[SALP_PERCENT] + 0;				
				if ($TMP_SALP_PERCENT == 2)		$MOV_CODE = "21420";
				elseif ($TMP_SALP_PERCENT == 4)	$MOV_CODE = "21430";
			}
			//$tmp_date = explode("-", substr(trim($data[SALP_DATE]), 0, 10));
			$CMD_NOTE1 = trim($data[SALP_REMARK]);
			
			$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
			$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : "NULL";
			$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
			$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
			$POT_ID = (trim($data[POT_ID]))? $data[POT_ID] : "NULL";
			$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";
			if ($COM_PER_TYPE==1) {
				$cmd = " 	select POS_NO, PL_NAME, PM_CODE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, POS_NO_NAME
									from PER_POSITION a, PER_LINE b 
									where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$POS_NO_NAME =  trim($data2[POS_NO_NAME]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				if ($PL_NAME)				$CMD_POSITION = "$PL_NAME";
				if ($POS_NO)					$CMD_POS_NO = $POS_NO;
				if ($POS_NO_NAME)	$CMD_POS_NO_NAME = $POS_NO_NAME;

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
				if ($PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$PM_NAME";
	
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
	
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==2) {
				$cmd = " 	select POEM_NO, PN_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 , POEM_NO_NAME
									from PER_POS_EMP a, PER_POS_NAME b 
									where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEM_NO = trim($data2[POEM_NO]);
				$POEM_NO_NAME = trim($data2[POEM_NO_NAME]);
				$PN_NAME = trim($data2[PN_NAME]);
				if ($PN_NAME)				$CMD_POSITION = "$PN_NAME";
				if ($POEM_NO)				$CMD_POS_NO = $POEM_NO;
				if ($POEM_NO_NAME)	$CMD_POS_NO_NAME = $POEM_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEM_ID=$POEM_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $PN_NAME.' '.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==3) {
				$cmd = " 	select POEMS_NO, EP_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 , POEMS_NO_NAME
									from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEMS_NO = trim($data2[POEMS_NO]);
				$POEMS_NO_NAME = trim($data2[POEMS_NO_NAME]);
				$EP_NAME = trim($data2[EP_NAME]);
				if ($EP_NAME)					$CMD_POSITION = "$EP_NAME";
				if ($POEMS_NO)				$CMD_POS_NO = $POEMS_NO;
				if ($POEMS_NO_NAME)	$CMD_POS_NO_NAME = $POEMS_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $EP_NAME.' '.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==4) {
				$cmd = " 	select POT_NO, TP_NAME, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2 , POT_NO_NAME
									from PER_POS_TEMP a, PER_TEMP_POS_NAME b 
									where POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POT_NO = trim($data2[POT_NO]);		
				$POT_NO_NAME = trim($data2[POT_NO_NAME]);			
				$TP_NAME = trim($data2[TP_NAME]);
				if ($TP_NAME)				$CMD_POSITION = "$TP_NAME";
				if ($POT_NO)					$CMD_POS_NO = $POT_NO;
				if ($POT_NO_NAME)	$CMD_POS_NO_NAME = $POT_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POT_ID=$POT_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $TP_NAME.' '.$POSITION_LEVEL;
			}
			$DEPARTMENT_ID = (trim($data2[DEPARTMENT_ID]))? trim($data2[DEPARTMENT_ID]) : 0;
			$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
			$CMD_ORG1 = $CMD_ORG2 = $CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($DEPARTMENT_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG2 = ($temp_id == $DEPARTMENT_ID)?  trim($data2[ORG_NAME]) : $CMD_ORG2;
				$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}
				
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_ID = $data2[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CMD_ORG1 = $data2[ORG_NAME];				

			$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OT_CODE = trim($data2[OT_CODE]);
	
			if ($CMD_ORG3=="-") $CMD_ORG3 = "";
			if ($CMD_ORG4=="-") $CMD_ORG4 = "";
			if ($CMD_ORG5=="-") $CMD_ORG5 = "";
			if ($OT_CODE == "03") 
				if (!$CMD_ORG5 && !$CMD_ORG4 && $CMD_ORG2=="�����û���ͧ") 
					$ORG_NAME_WORK = "���ӡ�û���ͧ".$CMD_ORG3." ".$CMD_ORG3;
				else 
					$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
			elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$CMD_ORG2);
			else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);

//echo "<br> $ORG_ID_DTL - $ORG_ID_1 == $COM_LEVEL_SALP - $CMD_SALARY > $CMD_OLD_SALARY $COM_PER_TYPE<br>";
//echo "TMP_PER_ID:".$TMP_PER_ID."=".$CMD_SALARY." > ".$CMD_OLD_SALARY."<br><br>";
			if (!$ORG_ID_DTL || $ORG_ID_DTL == "NULL" || $ORG_ID_DTL == $ORG_ID_1) { 
			   /*���*/	
                            /*if ((($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) && $CMD_SPSALARY > 0) || 
					($COM_LEVEL_SALP!=3 && $COM_LEVEL_SALP!=4 && $COM_LEVEL_SALP!=8 && $CMD_SALARY > $CMD_OLD_SALARY)) {*/
                            /*Release 5.1.0.6 Begin*/
                            if ((($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) && $CMD_SPSALARY > 0) || 
					($COM_LEVEL_SALP!=3 && $COM_LEVEL_SALP!=4 && $COM_LEVEL_SALP!=8 && $CMD_SALARY >= $CMD_OLD_SALARY)) {
                            /*Release 5.1.0.6 End*/     
					$cmd_seq++;
					$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
									CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
									POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
									PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
									UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, PL_NAME_WORK, ORG_NAME_WORK ,CMD_POS_NO_NAME, CMD_POS_NO)
									values ($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
									$CMD_LEVEL, '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
									$CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
									$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
									$SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, '$PL_NAME_WORK', '$ORG_NAME_WORK','$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
					$db_dpis1->send_cmd($cmd);

					//$db_dpis1->show_error();
//echo "<br>$cmd<br>=======================<br>";
				}
			}
		}	// end while
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		

		// ===== ����ش insert �����Ũҡ��������������͹����Թ��͹ ����������źѭ��Ṻ���¤���� (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )


	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);
                if(empty($SALQ_YEAR)){$SALQ_YEAR ="NULL";}else{$SALQ_YEAR=$SALQ_YEAR-543;} 

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' ,
                                                COM_YEAR = $SALQ_YEAR 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
                //echo "<pre>".$cmd;
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	

// ============================================================
	// ������ա���׹�ѹ�����Ţͧ�ѭ��Ṻ���¤����
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		// ��� insert �����Ũҡ per_comdtl � per_personal ��� per_positionhis
		$cmd = " select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_OLD_SALARY, CMD_SALARY, 
										CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, CMD_ORG1, CMD_ORG2, CMD_ORG3, 
										CMD_ORG4, CMD_ORG5, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, LEVEL_NO_SALARY, CMD_PERCENT, 
										CMD_MIDPOINT, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ_NO, CMD_SEQ, PER_CARDNO,CMD_POS_NO_NAME, CMD_POS_NO
						from		PER_COMDTL 
						where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID];
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = $data[POS_ID];
			$tmp_POEM_ID = $data[POEM_ID];
			$tmp_POEMS_ID = $data[POEMS_ID];
			$tmp_POT_ID = $data[POT_ID];
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);
			$tmp_CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
			$tmp_CMD_SALARY = $data[CMD_SALARY];
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY];
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PER_CARDNO = trim($data[PER_CARDNO]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_ORG1 = trim($data[CMD_ORG1]);		
			$tmp_CMD_ORG2 = trim($data[CMD_ORG2]);		
			$tmp_CMD_ORG3 = trim($data[CMD_ORG3]);		
			$tmp_CMD_ORG4 = trim($data[CMD_ORG4]);		
			$tmp_CMD_ORG5 = trim($data[CMD_ORG5]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_CMD_PERCENT = $data[CMD_PERCENT];
			$tmp_CMD_SALARY_UP = ($tmp_CMD_SALARY)?$tmp_CMD_SALARY - $tmp_CMD_OLD_SALARY:'';
			$tmp_CMD_MIDPOINT = $data[CMD_MIDPOINT] + 0;
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$tmp_CMD_SEQ_NO = $data[CMD_SEQ_NO] + 0;
			$tmp_CMD_SEQ = $data[CMD_SEQ] + 0;
			if ($tmp_CMD_SEQ_NO==0) $tmp_CMD_SEQ_NO = $tmp_CMD_SEQ;
			$tmp_CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
			$tmp_CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
						
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			if (trim($tmp_POS_ID)) {									// ���˹觢���Ҫ���
				$cmd = "  select POS_NO,POS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POS_NO]);
				$SAH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ���˹��١��ҧ��Ш�
				$cmd = "  select POEM_NO, POEM_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POEM_NO]);
				$SAH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);		
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ���˹觾�ѡ�ҹ�Ҫ���
				$cmd = "  select POEMS_NO, POEMS_NO_NAME , ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POEMS_NO]);
				$SAH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);				
				$EP_CODE = trim($data2[EP_CODE]);							
			} elseif (trim($tmp_POT_ID)) {						// ���˹��١��ҧ���Ǥ���
				$cmd = "  select POT_NO, POT_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, TP_CODE 
							   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POT_NO]);
				$SAH_POS_NO_NAME = trim($data2[POT_NO_NAME]);
				$TP_CODE = trim($data2[TP_CODE]);				   
			}
			$ORG_ID_3 = trim($data2[ORG_ID]);		

			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			
			// update status of PER_PERSONAL 
			$cmd = "update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY, 
						LEVEL_NO_SALARY='$tmp_LEVEL_NO_SALARY' 
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();	
//echo "<br>";
			// update into PER_POSITION 
			$cmd = "	update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";				
				
			// update and insert into PER_SALARYHIS 
			if($DPISDB=="oci8")
				$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by substr(SAH_EFFECTIVEDATE,1,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";
			else
				$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by left(SAH_EFFECTIVEDATE,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			if (substr($before_cmd_date,0,10) < substr($tmp_SAH_EFFECTIVEDATE,0,10)) $before_cmd_date = $tmp_SAH_EFFECTIVEDATE;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
					where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();							
//echo "<br>";

			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);

			if (!$tmp_CMD_PERCENT) $tmp_CMD_PERCENT = "NULL";
			if (!$tmp_CMD_SALARY_UP) $tmp_CMD_SALARY_UP = "NULL";
			if (!$tmp_CMD_SPSALARY) $tmp_CMD_SPSALARY = "NULL";
			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 

			$SAH_SEQ_NO = 1;
			$SAH_REMARK = "NULL";
			$EX_CODE = "024";
			if (!$SALQ_YEAR) $SALQ_YEAR = "NULL";
			if (!$SALQ_TYPE) $SALQ_TYPE = "NULL";
			if($COM_PER_TYPE == 2){
				 if($SALQ_TYPE == 3) $SALQ_TYPE = 1;
				 if($SALQ_TYPE == 4) $SALQ_TYPE = 2;
			}
			if (!$tmp_TOTAL_SCORE) $tmp_TOTAL_SCORE = "NULL";
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, SAH_SALARY_UP, 
							SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, 
							EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE,
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME) 
							values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$COM_DATE', '', $SESS_USERID, '$UPDATE_DATE', $tmp_CMD_PERCENT, $tmp_CMD_SALARY_UP, 
							$tmp_CMD_SPSALARY, $SAH_SEQ_NO, $SAH_REMARK, '$tmp_LEVEL_NO', '$SAH_POS_NO', 
							'$PL_NAME_WORK', '$ORG_NAME_WORK', '$EX_CODE', '$SAH_POS_NO', $tmp_CMD_MIDPOINT, 
							$SALQ_YEAR, $SALQ_TYPE, $tmp_TOTAL_SCORE, 'Y', '$SM_CODE', $tmp_CMD_SEQ_NO, 
							'$SAH_ORG_DOPA_CODE', $tmp_CMD_OLD_SALARY, '$SAH_POS_NO_NAME') ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<pre>".$cmd."<br>";
		}	// 		while ($data = $db_dpis->get_array())		
		
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================
	// ������ա���觨ҡ�����Ҥ
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = $UPD_DTL = "";
	}

	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID , a.ORG_ID, a.COM_LEVEL_SALP, UPDATE_USER, UPDATE_DATE ,
                                                a.COM_YEAR
				from		PER_COMMAND a, PER_COMTYPE b
				where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
                //echo '<pre>'.$cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
                $ORI_COM_DATE = substr(trim($data[COM_DATE]),0,10);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		$COM_LEVEL_SALP = $data[COM_LEVEL_SALP];
                
                $COM_YEAR_DB = $data[COM_YEAR];
                //���ͺ��û����Թ�ҡ ŧ�ѹ���ͧ�����
                if($ORI_COM_DATE ){
                    $ARR_COMDATE = explode("-",$ORI_COM_DATE);
                    $txt_COM_DATE = $COM_YEAR_DB.'-'.$ARR_COMDATE[1].'-'.$ARR_COMDATE[2];
                    if($txt_COM_DATE >= ($COM_YEAR_DB-1)."-10-01" && $txt_COM_DATE <= $COM_YEAR_DB."-03-30"){
                        $CMD_Cycle = 1;
                    }else {
                        $CMD_Cycle = 2;
                    }
                }
                
		$ORG_ID_DTL = $data[ORG_ID];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		$ORG_NAME_DTL = "";
		if($ORG_ID_DTL){
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ORG_NAME_DTL = $data[ORG_NAME];
		}
		if ($DEPARTMENT_ID) {
			$DEPARTMENT_NAME = $MINISTRY_NAME = $MINISTRY_ID = "";
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
		$search_per_name = "";
		$search_per_surname = "";		
		$list_type = "4";		
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){ 
			$ORG_ID_DTL = "";
			$ORG_NAME_DTL = "";
		} // end if
	} // end if		
?>