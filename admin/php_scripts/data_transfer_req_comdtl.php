<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/transfer_person.php");		
	/*cdgs*/	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);

	//---------------------------------------------------------------------------------
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
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

	$COM_SEND_STATUS = "";
	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
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
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$COM_SEND_STATUS = "S";
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case
	if (is_null($ORG_ID) || $ORG_ID=="NULL") $ORG_ID=0;
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
		$search_org_id = "0";
		$search_org_name = "";
	}	
	//---------------------------------------------------------------------------------

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	//=========================================================================================
	$tmp_COM_DATE =  save_date($COM_DATE);
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
			$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$COM_ID = $data[max_id] + 1;
			
			if (trim($search_org_id)=="" || $search_org_id=="NULL" || is_null($search_org_id)) {
				$search_org_id="0";
			}
	
			$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, UPDATE_USER, UPDATE_DATE) 
							 VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							'$COM_TYPE', 0, '', $DEPARTMENT_ID, $search_org_id, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������źѭ��Ṻ���¤���觺�è�/�Ѻ�͹ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]"); 	
			$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
			$count_comdtl = $db_dpis->send_cmd($cmd);
			// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
			echo "<script>";
			echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::>!<::>?UPD=1<::>')";
			echo "</script>";
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$tmp_COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', ORG_ID=$search_org_id, 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����źѭ��Ṻ���¤���觺�è�/�Ѻ�͹ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )

	
// ============================================================
	// ������ա���׹�ѹ�����Ţͧ�ѭ��Ṻ���¤���� (CONFIRM �ѭ��Ṻ���¤����)
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		// ��� insert �����Ũҡ per_comdtl � per_personal ��� per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
									CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
									CMD_NOTE1, CMD_NOTE2, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
									CMD_ORG6, CMD_ORG7, CMD_ORG8, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ  
				from		PER_COMDTL 
				where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error(); echo "<hr>";
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID];
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = $data[POS_ID];
			$tmp_POEM_ID = $data[POEM_ID];
			$tmp_POEMS_ID = $data[POEMS_ID];
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = $data[CMD_SALARY];
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY];
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? trim($data[CMD_NOTE2]) : "&rsquo;";
			$tmp_PL_NAME_WORK = trim($data[PL_NAME_WORK]);		
			$tmp_ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);		
			$tmp_CMD_SEQ = trim($data[CMD_SEQ]);
			$tmp_POH_ORG_TRANSFER = trim($data[CMD_ORG1]) . ' ' . trim($data[CMD_ORG2]) . ' ' . trim($data[CMD_ORG3]) . ' ' . trim($data[CMD_ORG4]) . ' ' . trim($data[CMD_ORG5]);			
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);
			
			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			if (trim($tmp_POS_ID)) {									// ���˹觢���Ҫ���
				$cmd = "  select POS_NO,POS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error(); echo "<hr>";
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$POH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
				//cdgs
				$strPosIDDpis=$tmp_POS_ID;
				$strPerType="11";
				//cdgs
			} elseif (trim($tmp_POEM_ID)) {						// ���˹��١��ҧ��Ш�
				$cmd = "  select POEM_NO,POEM_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);
				$POH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);				
				$PN_CODE = trim($data2[PN_CODE]);
				//cdgs
				$strPosIDDpis=$tmp_POEM_ID;
				$strPerType="21";
				//cdgs	
			} elseif (trim($tmp_POEMS_ID)) {						// ���˹觾�ѡ�ҹ�Ҫ���
				$cmd = "  select POEMS_NO, POEMS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);
				$POH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);				
				$EP_CODE = trim($data2[EP_CODE]);
				//cdgs
				$strPosIDDpis=$tmp_POEMS_ID;
				$strPerType="14";
				//	cdgs	
										   
			}
			$ORG_ID_3 = trim($data2[ORG_ID]);		
			$ORG_ID_4 = trim($data2[ORG_ID_1]);
			$ORG_ID_5 = trim($data2[ORG_ID_2]);
			$ORG_ID_6 = trim($data2[ORG_ID_3]);		
			$ORG_ID_7 = trim($data2[ORG_ID_4]);
			$ORG_ID_8 = trim($data2[ORG_ID_5]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_6 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_6 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_7 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_7 = $data2[ORG_NAME];				

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_8 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_8 = $data2[ORG_NAME];				

			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			//echo "<hr>";
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			//echo "AP_CODE = $AP_CODE<hr>";
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";				
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
			}			
			if (!$tmp_POS_ID) $tmp_POS_ID = "NULL";
			if (!$tmp_POEM_ID) $tmp_POEM_ID = "NULL";
			if (!$tmp_POEMS_ID) $tmp_POEMS_ID = "NULL";
			if (!$tmp_CMD_SALARY) $tmp_CMD_SALARY = 0;
			if (!$tmp_CMD_SPSALARY) $tmp_CMD_SPSALARY = 0;
			$ES_CODE = "02";
			//==================================================
			
			$cmd = " update PER_PERSONAL set 
							POS_ID = $tmp_POS_ID, 
							POEM_ID = $tmp_POEM_ID, 
							POEMS_ID = $tmp_POEMS_ID, 
							LEVEL_NO = '$tmp_LEVEL_NO', 
							PER_SALARY = $tmp_CMD_SALARY,
							PER_SPSALARY = $tmp_CMD_SPSALARY, 
							PER_OCCUPYDATE = '$tmp_CMD_DATE', 
							MOV_CODE='$tmp_MOV_CODE', 
							ES_CODE='$ES_CODE', 
							PER_DOCNO='$COM_NO', 
							PER_DOCDATE='$tmp_COM_DATE', 
							PAY_ID = $tmp_POS_ID, 
							PER_STATUS=1,
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE'
						where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";
		//=====================================

			if ($tmp_POS_ID) {
				$cmd = " update PER_POSITION set POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID";
				$db_dpis1->send_cmd($cmd);	
				//$db_dpis1->show_error();				
				//echo "<br>";
			}

			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc, POH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === �׹�ѹ����觺�è�/�Ѻ�͹㹡óշ���ջ���ѵԡ�ô�ç���˹���� ����ͧ�Դ record ��͹˹�������� write record ������� ?????????????
			// === �����������Ӿٴ��ҹ�� �֧ comment ������������� sql � query 
			
			$POH_ORG_TRANSFER = "";
			if ($tmp_MOV_CODE=="105" || $tmp_MOV_CODE=="10510" || $tmp_MOV_CODE=="10520") 
				$POH_ORG_TRANSFER = trim($tmp_POH_ORG_TRANSFER);			

			$cmd = " update PER_POSITIONHIS set POH_LAST_POSITION='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data= array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 	
			
			$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
			$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
			$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
			$CT_CODE_tmp = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
			$PV_CODE = (trim($_POST[PV_CODE]))? "'$_POST[PV_CODE]'" : "NULL";
			$AP_CODE = (trim($AP_CODE))? "'$AP_CODE'" : "NULL";
			$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";

			if ($COM_PER_TYPE==1) {	
				$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
				$PN_CODE = $EP_CODE = $TP_CODE =  "NULL";
			} elseif ($COM_PER_TYPE==2) {
				$PN_CODE = (trim($PN_CODE))? "'$PN_CODE'" : "NULL";	
				$PL_CODE = $EP_CODE = $TP_CODE =  "NULL";
			} elseif ($COM_PER_TYPE==3) {
				$EP_CODE = (trim($EP_CODE))? "'$EP_CODE'" : "NULL";	
				$PL_CODE = $PN_CODE = $TP_CODE = "NULL";
			} elseif ($COM_PER_TYPE==4) {
				$TP_CODE = (trim($TP_CODE))? "'$TP_CODE'" : "NULL";	
				$PL_CODE = $PN_CODE = $EP_CODE = "NULL";
			}
			
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, 
								POH_DOCNO, POH_DOCDATE, POH_ENDDATE, POH_POS_NO, PM_CODE, LEVEL_NO, 
								PL_CODE, PN_CODE, EP_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, 
								ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, 
						   		POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, POH_SALARY, 
								POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, POH_PL_NAME, 
								POH_ORG, POH_CMD_SEQ, UPDATE_USER, UPDATE_DATE, POH_ORG_TRANSFER, 
								POH_SEQ_NO, POH_LAST_POSITION, ES_CODE, POH_LEVEL_NO, POH_ISREAL, POH_POS_NO_NAME)
						  		values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$tmp_COM_DATE', '', 
						   		'$POH_POS_NO', $PM_CODE, '$tmp_LEVEL_NO', $PL_CODE, $PN_CODE, $EP_CODE,  
								$CT_CODE, $PV_CODE, $AP_CODE, '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', 
								'$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', $tmp_CMD_SALARY, 
								$tmp_CMD_SPSALARY, '$tmp_CMD_NOTE2', '$ORG_NAME_1', '$ORG_NAME_2', 
								'$ORG_NAME_3', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', $tmp_CMD_SEQ, 
								$SESS_USERID, '$UPDATE_DATE', '$POH_ORG_TRANSFER', 1, 'Y', '02', '$tmp_LEVEL_NO', 'Y', '$POH_POS_NO_NAME') ";
			$db_dpis1->send_cmd($cmd);
			//echo "<hr>";
			//$db_dpis1->show_error();
			//$db_dpis1->show_error(); echo "<hr>";

			// update and insert into PER_SALARYHIS 
			$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
			//$db_dpis1->send_cmd($cmd);	
			// === �׹�ѹ����觺�è�/�Ѻ�͹㹡óշ���ջ���ѵԡ���Ѻ�Թ��͹��� ����ͧ�Դ record ��͹˹�������� write record �������
			// === �����������Ӿٴ��ҹ�� �֧ comment ������������� sql � query 	

			// === ��ú�è�/�Ѻ�͹ ����駤�ҡ������͹����Թ��͹�� 21390 (�Թ��͹�á��è�)
			$tmp_SALARY_MOV_CODE = "21390";
			$tmp_SM_CODE = "15";
			if ($tmp_MOV_CODE=="10140" || $tmp_MOV_CODE=="10150" || $tmp_MOV_CODE=="10160" || $tmp_MOV_CODE=="10170" || $tmp_MOV_CODE=="10180") 
				$tmp_SALARY_MOV_CODE = "21394";
			elseif ($tmp_MOV_CODE=="105" || $tmp_MOV_CODE=="10510" || $tmp_MOV_CODE=="10520" || $tmp_MOV_CODE=="10530" || $tmp_MOV_CODE=="10540") {
				$tmp_SALARY_MOV_CODE = "21395";
				$tmp_SM_CODE = "16";
			}
			
			$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' where PER_ID=$tmp_PER_ID";
			$db_dpis1->send_cmd($cmd);	

			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
								SAH_DOCDATE, SAH_ENDDATE, SAH_POSITION, SAH_ORG, EX_CODE, SAH_POS_NO, SAH_PAY_NO,
								SAH_SEQ_NO, LEVEL_NO, SAH_LAST_SALARY, SAH_CMD_SEQ, SM_CODE, UPDATE_USER, UPDATE_DATE) 
								values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_SALARY_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
								'$tmp_COM_DATE', '', '$tmp_PL_NAME_WORK', '$tmp_ORG_NAME_WORK', '024', '$POH_POS_NO', '$POH_POS_NO', 
								1, '$tmp_LEVEL_NO', 'Y', $tmp_CMD_SEQ, '$tmp_SM_CODE', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//echo "$cmd<br>";
		//	$db_dpis1->show_error();
		/*cdgs*/
		$aa=f_new_person($tmp_PER_ID,$tmp_LEVEL_NO,$tmp_CMD_DATE,f_get_movement_code($tmp_MOV_CODE),$strPosIDDpis,$COM_ID,$strPerType);
		/*cdgs*/	
		}				

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����š�ô�ç���˹� �����������ѵԡ�ô�ç���˹�������׹�ѹ�����źѭ��Ṻ���¤���觺�è�/�Ѻ�͹ [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);

		echo "<script>";
		echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::>1<::>?UPD=1<::>')";
		echo "</script>";
	}		// 	if( $command == "COMMAND" && trim($COM_ID) ) 	
//=========================================================================================
//=========================================================================================

// ============================================================
	// ������ա���觨ҡ�����Ҥ
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �觢����źѭ��Ṻ���¤���觺�è�/�觵��/�Ѻ�͹ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ���觺�è�/�Ѻ�͹ [".trim($COM_ID)." : ".$PER_ID."]");
		
		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
		echo "<script>";
		if ($count_comdtl > 0)
			echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		else
			echo "parent.refresh_opener('3<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		echo "</script>";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����źѭ����Т���Ҫ���/�١��ҧṺ���ºѭ�դ���觺�è�/�Ѻ�͹ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";

		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::><::><::><::><::><::>')";
		echo "</script>";
	}
	
	if (trim($COM_ID)) {
		$cmd = " select		COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
										a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID
						 from		PER_COMMAND a, PER_COMTYPE b
					 	 where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
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


	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";		
		
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
	} // end if		
?>