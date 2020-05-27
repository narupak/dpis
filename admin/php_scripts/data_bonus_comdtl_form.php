<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ = trim($data[max_id]) + 1;	
	}
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	
	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE =  save_date($CMD_DATE);
		
		$POS_ID = $POEM_ID = $POEMS_ID = "";
		$PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = "";
		if ($PER_TYPE == 1) {
			$PL_CODE = trim($PL_PN_CODE);
			$PL_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POS_ID = trim($POS_POEM_ID);
		} elseif ($PER_TYPE == 2) {
			$PN_CODE = trim($PL_PN_CODE);
			$PN_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEM_ID = trim($POS_POEM_ID);
		} elseif ($PER_TYPE == 3) {
			$EP_CODE = trim($PL_PN_CODE);
			$EP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEMS_ID = trim($POS_POEM_ID);			
		}	elseif ($PER_TYPE == 4) {
			$TP_CODE = trim($PL_PN_CODE);
			$TP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POT_ID = trim($POS_POEM_ID);			
		}	// end if	
	
		$CMD_POS_NO_NAME = trim($CMD_POSPOEM_NO_NAME);
		$CMD_POS_NO = trim($CMD_POSPOEM_NO);
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		$CMD_SALARY = str_replace(",", "", $CMD_SALARY) + 0;	
		$CMD_SPSALARY = str_replace(",", "", $CMD_SPSALARY) + 0;
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);	
		
		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
		$PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
		$POS_ID = trim($POS_ID)? $POS_ID : "NULL";
		$POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
		$POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
		$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
		$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
		$EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
		$PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
		$PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
		$EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";								
		$PL_NAME_WORK = trim($PL_NAME_WORK)? "'".$PL_NAME_WORK."'" : "NULL";		
		$ORG_NAME_WORK = trim($ORG_NAME_WORK)? "'".$ORG_NAME_WORK."'" : "NULL";		
		if ($CMD_PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$CMD_PM_NAME";
	} // end if
	
	if($command=="ADD"){
		//*********************************** start gen bonus **********************************************//
		$cmd = " select * from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CMD_SALARY = $data[PER_SALARY];
		
		$cmd = " select * from PER_BONUS_RULE where BR_YEAR='$YEAR_ACTIVATE' and BR_ACTIVE=1 order by BR_TYPE, BR_POINT_MIN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$bonus_rule_org = (array) null;
		$bonus_rule_per2 = (array) null;
		$bonus_rule_per3 = (array) null;
		$bonus_rule_per = (array) null;
		while ($data = $db_dpis->get_array()) {
			if ($data[BR_TYPE]=="1") {	// หน่วยงาน
				$bonus_rule_org[min][] = $data[BR_POINT_MIN];
				$bonus_rule_org[max][] = $data[BR_POINT_MAX];
				$bonus_rule_org[times][] = $data[BR_TIMES];
			} elseif ($data[BR_TYPE]=="2") {	// ข้าราชการ
				$bonus_rule_per2[min][] = $data[BR_POINT_MIN];
				$bonus_rule_per2[max][] = $data[BR_POINT_MAX];
				$bonus_rule_per2[times][] = $data[BR_TIMES];
			} else {	// ลูกจ้างประจำ
				$bonus_rule_per3[min][] = $data[BR_POINT_MIN];
				$bonus_rule_per3[max][] = $data[BR_POINT_MAX];
				$bonus_rule_per3[times][] = $data[BR_TIMES];
			}
		}
		if ($PER_TYPE==1) {	// ถ้าคนนี้เป็นข้าราชการ ก็ได้โบนัสตามกลุ่มข้าราชการ
			$bonus_rule_per[min] = $bonus_rule_per2[min];
			$bonus_rule_per[max] = $bonus_rule_per2[max];
			$bonus_rule_per[times] = $bonus_rule_per2[times];
		} else {	// ถ้าไม่ใช่ ก็ได้โบนัสตามกลุ่ม ลูกจ้างประจำ
			$bonus_rule_per[min] = $bonus_rule_per3[min];
			$bonus_rule_per[max] = $bonus_rule_per3[max];
			$bonus_rule_per[times] = $bonus_rule_per3[times];
		}
		$cmd = " select a.DEPARTMENT_ID, KF_START_DATE, KF_END_DATE, TOTAL_SCORE, sum(KPI_SCORE) as sum_KPI_SCORE from PER_KPI_FORM a left join PER_KPI b on (a.DEPARTMENT_ID=b.DEPARTMENT_ID) where PER_ID=$PER_ID and KPI_YEAR='$YEAR_ACTIVATE' group by a.DEPARTMENT_ID, KF_START_DATE, KF_END_DATE, TOTAL_SCORE order by KF_START_DATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//		echo "$cmd<br>";
		$bonus_org = 0;
		$bonus_per = 0;
		$total_per_score = 0;
		$cnt_kpi_times=0;
		$bonus_formula = "";
		while ($data = $db_dpis->get_array()) {
			$num_month = date_difference($data[KF_START_DATE], $data[KF_END_DATE], "m");
			$org_times = 1;	// at least 1 times อย่างน้อยได้ 1 เท่า
			for($n=0; $n < count($bonus_rule_org[min]); $n++) {
				if ($bonus_rule_org[min][$n] <= $data[SUM_KPI_SCORE] && $data[SUM_KPI_SCORE] <= $bonus_rule_org[max][$n]) {
					$org_times = $bonus_rule_org[times][$n];
					$sub_bonus_formula = "".$bonus_rule_org[min][$n]."|".$data[SUM_KPI_SCORE]."|".$bonus_rule_org[max][$n]."";
					break;
				}
			}
			$bonus_org += $CMD_SALARY * $num_month * $org_times;
			$bonus_formula .= "".$data[DEPARTMENT_ID]."&".$sub_bonus_formula."&$CMD_SALARY&$num_month&$org_times,"; 
			$total_per_score += $data[TOTAL_SCORE];
			$cnt_kpi_times++;
		}	// end while loop
		$avg_score = ($total_per_score / $cnt_kpi_times);
		$per_times = 1;		// at least 1 times อย่างน้อยได้ 1 เท่า
		$sub_bonus_formula = "";
		for($n=0; $n < count($bonus_rule_per[min]); $n++) {
			if ($bonus_rule_per[min][$n] <= $avg_score && $avg_score <= $bonus_rule_per[max][$n]) {
				$per_times = $bonus_rule_per[times][$n];
				$sub_bonus_formula = "".$bonus_rule_org[min][$n]."|$avg_score|$total_per_score|$cnt_kpi_times|".$bonus_rule_org[max][$n]."";
				break;
			}
		}
		$bonus_per = $CMD_SALARY * $per_times;
		$bonus_formula .= "***$CMD_SALARY&$per_times&$sub_bonus_formula"; 
		//*********************************** end gen bonus **********************************************//
		$cmd = " insert into PER_COMDTL
							(	COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
								CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
								POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
								PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
								CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
								PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK ,CMD_POS_NO_NAME, CMD_POS_NO)
						 values
						 	(	$COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
								'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $bonus_org, 
								$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $bonus_per, 
								$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
								'$bonus_formula', '$CMD_NOTE2', '$MOV_CODE', 0, 
								$PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', $PL_NAME_WORK, $ORG_NAME_WORK , '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$UPD_PER=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $ADD_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
		$cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								EN_CODE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION', 
								CMD_ORG1 = '$CMD_ORG1', 
								CMD_ORG2 = '$CMD_ORG2', 
								CMD_ORG3 = '$CMD_ORG3', 
								CMD_ORG4 = '$CMD_ORG4', 
								CMD_ORG5 = '$CMD_ORG5', 
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
								POS_ID = $POS_ID, 
								POEM_ID = $POEM_ID, 
								POEMS_ID = $POEMS_ID, 
								LEVEL_NO = $LEVEL_NO, 								
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = $CMD_SPSALARY, 
								PL_CODE_ASSIGN = '', 
								PN_CODE_ASSIGN = '', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								PL_NAME_WORK = $PL_NAME_WORK, 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO'
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$UPD_PER=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $EDIT_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $DEL_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");		
	} // end if

	// ค้นหาเพื่อแสดงผลข้อมูล	
	if(($UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ) ){
		$cmd = "	select	PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
									CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, 
									POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
									CMD_NOTE1, CMD_NOTE2, MOV_CODE, PL_NAME_WORK, ORG_NAME_WORK , CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE
						from		PER_COMDTL
						where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($DPISDB=="mysql"){
			$tmp_data = explode("|", trim($data[CMD_POSITION]));
		}else{
			$tmp_data = explode("\|", trim($data[CMD_POSITION]));
		}
		//ในกรณีที่มี CMD_PM_NAME
		if(is_array($tmp_data)){
			$CMD_POSITION = ($tmp_data[0])? "$tmp_data[0]" : "";
			$CMD_PM_NAME = $tmp_data[1];
		}else{
			$CMD_POSITION = $data[CMD_POSITION];
		}
		$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
		$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
		$CMD_ORG1 = trim($data[CMD_ORG1]); 
		$CMD_ORG2 = trim($data[CMD_ORG2]); 
		$CMD_ORG3 = trim($data[CMD_ORG3]); 
		$CMD_ORG4 = trim($data[CMD_ORG4]); 
		$CMD_ORG5 = trim($data[CMD_ORG5]); 
		$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
		$CMD_SALARY =  number_format(trim($data[CMD_SALARY]), 2, '.', ','); 
		$CMD_SPSALARY = number_format(trim($data[CMD_SPSALARY]), 2, '.', ','); 
		$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
		$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
		$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
		$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
		$buff = $data[CMD_OLD_SALARY]+$data[CMD_SPSALARY];
		$TOTAL_BONUS = number_format(trim($buff), 2, '.', ',');

		$CMD_LEVEL = trim($data[CMD_LEVEL]); 
		$LEVEL_NO = trim($data[LEVEL_NO]); 
		$PER_ID = trim($data[PER_ID]);
		$EN_CODE = trim($data[EN_CODE]);
		$MOV_CODE = trim($data[MOV_CODE]); 
		$CMD_DATE = show_date_format($data[CMD_DATE], 1);
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$CMD_LEVEL_NAME = trim($data_dpis1[LEVEL_NAME]);

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$LEVEL_NAME = trim($data_dpis1[LEVEL_NAME]);

		$cmd = " select PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
					  from 	PER_PERSONAL a, PER_PRENAME b 
					  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
		$db_dpis1->send_cmd($cmd);
		$data_dpis2 = $db_dpis1->get_array();
		$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
		$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
		$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
		$PER_TYPE = trim($data_dpis2[PER_TYPE]);
			
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EN_NAME = trim($data_dpis1[EN_NAME]);

		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);		

		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
		if ($PER_TYPE == 1) {			
			$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 		
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";			
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);

			$POS_ID = trim($data[POS_ID]);
			$cmd = " 	select 	POS_NO, a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
							from 		PER_POSITION a, PER_LINE b 
							where 	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE";
			$db_dpis1->send_cmd($cmd);	
			$data1 = $db_dpis1->get_array();
			$POS_POEM_NO = trim($data1[POS_NO]);
			$POS_POEM_NAME = trim($data1[PL_NAME]);
		}

		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
		if ($PER_TYPE == 2) {
			$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]); 				
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data_dpis1[PN_NAME]);

			$POEM_ID = trim($data[POEM_ID]); 
			$cmd = " 	select 	POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID,b.PG_CODE 
							from 		PER_POS_EMP a, PER_POS_NAME b
							where 	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);			
			$data1 = $db_dpis1->get_array();						
			$POS_POEM_NO = trim($data1[POEM_NO]);
			$POS_POEM_NAME = trim($data1[PN_NAME]);
			$PG_CODE = trim($data1[PG_CODE]);
		}
		
		//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_EMPSER_POS_NAME =====  TR_PER_TYPE=3
		if ($PER_TYPE == 3) {
			$PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]); 
			$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
					where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[EP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME;
				$PL_PN_NAME_ASSIGN = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME_ASSIGN;
			}	
			
			$POEMS_ID = $POS_POEM_ID = trim($data[POEMS_ID]); 
			$cmd = " 	select 	POEMS_NO, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
					from 	PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
					where 	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";	
			$db_dpis1->send_cmd($cmd);			
			$data1 = $db_dpis1->get_array();						
			$POS_POEM_NO = trim($data1[POEMS_NO]);
			$POS_POEM_NAME = trim($data1[EP_NAME]);
		}	
	
		$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
		$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
		$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($DTLFORM_ORG_ID)			$tmp_ORG_ID[] =  $DTLFORM_ORG_ID;
		if ($DTLFORM_ORG_ID_1)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_1;
		if ($DTLFORM_ORG_ID_2)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_2;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
		while ( $data1 = $db_dpis->get_array() ) {
			$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG2";
			$POS_POEM_ORG3 = ($DTLFORM_ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($DTLFORM_ORG_ID_1 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($DTLFORM_ORG_ID_2 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
		}	// while				
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MINISTRY_ID = $data1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];
		
// ******************************************************** แปลงสูตร ***********************************************************
		$bonus_formula = "";
		$a_part = explode(",", $CMD_NOTE1); 	// ตัดแต่ละชุดการคำนวณออกก่อน
		for($i=0; $i < count($a_part); $i++) {
			if (substr($a_part[$i],0,3)=="***") {	// เป็นส่วนของแต่ละบุคคล
				$per = explode("&", substr($a_part[$i],3));
				$salary = (float)$per[0];
				$per_times = (float)$per[1];
				$a_sub = explode("|", $per[2]);
				if ((float)$a_sub[1] > 0)
					if ((int)$a_sub[3] > 1)
						$sub_bonus_formula = "{คะแนนอยู่ในช่วง ".$a_sub[0]." <= ค่าเฉลี่ย ".$a_sub[3]." ค่า ของ TOTAL_SCORE(".$a_sub[1].") <= ".$a_sub[4]."}";
					else
						$sub_bonus_formula = "{คะแนนอยู่ในช่วง ".$a_sub[0]." <= TOTAL_SCORE(".$a_sub[1].") <= ".$a_sub[4]."}";
				else
					$sub_bonus_formula = "";
				$bonus_per = $salary * $per_times;
				$bonus_formula .= "  รวมกับ โบนัสส่วนตัว = เงินเดือน($salary) x $per_times $sub_bonus_formula"; 
			} else {	// เป็นส่วนของ แต่ละ department_id
				$dept = explode("&", $a_part[$i]);
				$cmd = " select * from PER_ORG where ORG_ID=".$dept[0]." ";
				$db_dpis1->send_cmd($cmd);
		//		$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$TMP_DEPT_NAME = $data1[ORG_NAME];
				if (strpos($dept[1], "|") !== false) {
					$a_sub = explode("|", $dept[1]);
					$sub_bonus_formula = "{คะแนนอยู่ในช่วง ".$a_sub[0]." <= KPI_SCORE(".$a_sub[1].") <= ".$a_sub[2]."}";
				} else {
					$sub_bonus_formula = "";
				}
				$salary = (float)$dept[2];
				$num_month = (int)$dept[3];
				$org_times = (float)$dept[4];
				$bonus_org += $salary * $num_month * $org_times;
				if ($bonus_formula) $bonus_formula .= "  ";
				$bonus_formula .= "สำหรับ $TMP_DEPT_NAME : ".$sub_bonus_formula." โบนัส=เงินเดือน($salary) x เดือน($num_month) x $org_times"; 
			} // if (substr($a_part[$i],0,3)=="***")
		}	// end for loop $i
		$CMD_NOTE1 = $bonus_formula;
// ******************************************************** จบการแปลงสูตร ***********************************************************
	} else { // not ($UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)
		$CMD_POSITION = "";
		$CMD_PM_NAME = "";
		$CMD_POSPOEM_NO_NAME = ""; 
		$CMD_POSPOEM_NO = ""; 
		$CMD_ORG1 = ""; 
		$CMD_ORG2 = ""; 
		$CMD_ORG3 = ""; 
		$CMD_ORG4 = ""; 
		$CMD_ORG5 = ""; 
		$CMD_OLD_SALARY = ""; 
		$CMD_SALARY = ""; 
		$CMD_SPSALARY = ""; 
		$CMD_NOTE1 = ""; 
		$CMD_NOTE2 = ""; 
		$PL_NAME_WORK = ""; 
		$ORG_NAME_WORK = "";
		$TOTAL_BONUS = "";

		$CMD_LEVEL = ""; 
		$CMD_LEVEL_NAME = "";

		$LEVEL_NO = ""; 
		$LEVEL_NAME = "";

		$CMD_DATE = "";
		
		$PER_ID = "";
		$PER_CARDNO = "";	
		$PER_NAME = "";
		$PER_BIRTHDATE = "";
		
//		$PER_TYPE = "";
			
		$EN_CODE = "";
		$EN_NAME = "";

		$MOV_CODE = ""; 
		$MOV_NAME = "";		

		$PL_PN_CODE_ASSIGN = ""; 		
		$PL_PN_NAME_ASSIGN = "";
			
		$POS_ID = "";
		$POS_POEM_NO = "";
		$POS_POEM_NAME = "";

		$POEM_ID = ""; 
		$PG_CODE = "";
		$PL_PN_NAME = "";
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		unset($SHOW_UPDATE_USER_DTL);
		unset($SHOW_UPDATE_DATE_DTL);
	} 	// 	if($COM_ID)
?>