<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/transfer_person.php");
	/*cdgs*/

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);

	//-----------------------------------------------------------------------
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
	} // end switch case
	if (is_null($ORG_ID) || $ORG_ID=="NULL") $ORG_ID=0;
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
		$search_org_id = "0";
		$search_org_name = "";
	}	
	//-----------------------------------------------------------------------

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//=========================================================================
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 1;
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);

		if (trim($search_org_id)=="" || $search_org_id=="NULL" || is_null($search_org_id)) {
			$search_org_id="0";
		}

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
							COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, UPDATE_USER, UPDATE_DATE) 
							VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
							'$COM_TYPE', 0, '', $DEPARTMENT_ID, $search_org_id, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������źѭ��Ṻ���¤��������͹�дѺ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )


	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		//	ORG_ID=$search_org_id,			//����������� DEPARTMENT_ID / ORG_ID �� ������ҧ�ҵ����͹�����ѭ�� **** 
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE' , 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����źѭ��Ṻ���¤�������� [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");

	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	
	
// ============================================================
	// ������ա���׹�ѹ�����Ţͧ�ѭ��Ṻ���¤����
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		// ��� insert �����Ũҡ per_comdtl � per_personal ��� per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
									CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
									EP_CODE_ASSIGN, CMD_NOTE1 
						from		PER_COMDTL 
						where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = trim($data[PER_ID]);
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = trim($data[POS_ID]);
			$tmp_POEM_ID = trim($data[POEM_ID]);
			$tmp_POEMS_ID = trim($data[POEMS_ID]);
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_CMD_SALARY = trim($data[CMD_SALARY]) + 0;
			$tmp_CMD_SPSALARY = trim($data[CMD_SPSALARY]);
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			//cdgs
			$strCmdPosition=trim($data[CMD_POSITION]);
			$tmp_CMD_LEVEL=trim($data[CMD_LEVEL]);
			//cdgs
			
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 �Թҷ� = 1 �ѹ
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			// select PER_SALARY to compare with CMD_SALARY before insert into PER_SALARYHIS
			$PER_SALARY = "";
			$cmd = " select PER_SALARY from PER_PERSONAL where PER_ID=$tmp_PER_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PER_SALARY = $data2[PER_SALARY] + 0;
			
			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = $PT_CODE = $POH_ASS_ORG = "";
			$cmd = " select ORG_NAME from PER_PERSONAL a, PER_ORG_ASS b where PER_ID=$tmp_PER_ID and a.ORG_ID=b.ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POH_ASS_ORG = $data2[ORG_NAME];

			if (trim($tmp_POS_ID)) {									// ���˹觢���Ҫ���
				$cmd = "  select POS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, PL_CODE, PT_CODE  
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POS_NO]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
				$PT_CODE = trim($data2[PT_CODE]);
				//cdgs
				$strPosIDDpis=$tmp_POS_ID;
				//cdgs
			} elseif (trim($tmp_POEM_ID)) {						// ���˹��١��ҧ��Ш�
				$cmd = "  select POEM_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEM_NO]);				
				$PN_CODE = trim($data2[PN_CODE]);	
				//cdgs
				$strPosIDDpis=$tmp_POEM_ID;
				//cdgs
			} elseif (trim($tmp_POEMS_ID)) {						// ���˹觾�ѡ�ҹ�Ҫ���
				$cmd = "  select POEMS_NO, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POH_POS_NO = trim($data2[POEMS_NO]);				
				$EP_CODE = trim($data2[EP_CODE]);	
				//cdgs
				$strPosIDDpis=$tmp_POEMS_ID;
				//cdgs									   
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
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			$ORG_ID_1 = (trim($ORG_ID_1))? trim($ORG_ID_1) : "NULL";
			$ORG_ID_2 = (trim($ORG_ID_2))? trim($ORG_ID_2) : "NULL";
			$ORG_ID_3 = (trim($ORG_ID_3))? trim($ORG_ID_3) : "NULL";					
			$ORG_ID_4 = (trim($ORG_ID_1))? trim($ORG_ID_4) : "NULL";
			$ORG_ID_5 = (trim($ORG_ID_2))? trim($ORG_ID_5) : "NULL";
			$ORG_ID_6 = (trim($ORG_ID_3))? trim($ORG_ID_6) : "NULL";					
			$ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 = $ORG_NAME_6 = "";
			$cmd = " select ORG_ID, ORG_NAME FROM PER_ORG where ORG_ID in ( $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $ORG_ID_6 ) ";
			$db_dpis2->send_cmd($cmd);
			while ($data2 = $db_dpis2->get_array()) {
				$ORG_NAME_1 = ($data2[ORG_ID] == $ORG_ID_1)? trim($data2[ORG_NAME]) : $ORG_NAME_1;
				$ORG_NAME_2 = ($data2[ORG_ID] == $ORG_ID_2)? trim($data2[ORG_NAME]) : $ORG_NAME_2;
				$ORG_NAME_3 = ($data2[ORG_ID] == $ORG_ID_3)? trim($data2[ORG_NAME]) : $ORG_NAME_3;
				$ORG_NAME_4 = ($data2[ORG_ID] == $ORG_ID_4)? trim($data2[ORG_NAME]) : $ORG_NAME_4;
				$ORG_NAME_5 = ($data2[ORG_ID] == $ORG_ID_5)? trim($data2[ORG_NAME]) : $ORG_NAME_5;
				$ORG_NAME_6 = ($data2[ORG_ID] == $ORG_ID_6)? trim($data2[ORG_NAME]) : $ORG_NAME_6;
			}		
			
			// update status of PER_PERSONAL 
			$cmd = " 	update PER_PERSONAL set  MOV_CODE='$tmp_MOV_CODE', LEVEL_NO='$tmp_LEVEL_NO', LEVEL_NO_SALARY='$tmp_LEVEL_NO', PER_SALARY=$tmp_CMD_SALARY, PER_STATUS=1 where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";

			// update status of PER_POSITION 
			$cmd = " update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY, POS_CHANGE_DATE='$tmp_CMD_DATE' where POS_ID=$tmp_POS_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();	
			//echo "<br>";
			
			// update and insert into PER_POSITIONHIS	
			$cmd = " select POH_ID from PER_POSITIONHIS where PER_ID=$tmp_PER_ID order by PER_ID, POH_EFFECTIVEDATE desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_POH_ID = trim($data1[POH_ID]);
			$cmd = " update PER_POSITIONHIS set POH_ENDDATE='$before_cmd_date' where POH_ID=$tmp_POH_ID";
			$db_dpis1->send_cmd($cmd);	
			//$db_dpis1->show_error();				
			//echo "<br>";
			
			$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$POH_ID = $data[max_id] + 1; 			 
			$cmd = " 	insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_DOCNO, 
								POH_DOCDATE, POH_ENDDATE, POH_POS_NO, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, 
								EP_CODE, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, 
								ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, 
								POH_UNDER_ORG5, POH_ASS_ORG, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
								POH_ORG1, POH_ORG2, POH_ORG3, UPDATE_USER, UPDATE_DATE)
								values ($POH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', '$COM_NO', '$COM_DATE', NULL, 
								'$POH_POS_NO', '$PM_CODE', '$tmp_LEVEL_NO', '$PL_CODE', '$PN_CODE', '$EP_CODE', '$PT_CODE',   
								'$CT_CODE', '$PV_CODE', '$AP_CODE', '2', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, '$ORG_NAME_4', 
								'$ORG_NAME_5', '$ORG_NAME_6', '$ORG_NAME_7', '$ORG_NAME_8', '$POH_ASS_ORG', 
								$tmp_CMD_SALARY, $tmp_CMD_SPSALARY, '$tmp_CMD_NOTE1', '$ORG_NAME_1', '$ORG_NAME_2', 
								'$ORG_NAME_3', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();				
			//echo "<br>";

			// ����Թ��͹�ա������¹�ŧ ������ҧ record � PER_SALARYHIS ����
			if($PER_SALARY != $tmp_CMD_SALARY){
				// update and insert into PER_SALARYHIS 
				$cmd = " select SAH_ID from PER_SALARYHIS where PER_ID=$tmp_PER_ID 
								order by PER_ID, SAH_EFFECTIVEDATE desc, SAH_SALARY desc, SAH_DOCNO desc ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$tmp_SAH_ID = trim($data1[SAH_ID]);
				$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
				$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date' where SAH_ID=$tmp_SAH_ID";
				$db_dpis1->send_cmd($cmd);	
				//$db_dpis1->show_error();							
				//echo "<br>";
	
				$tmp_SALARY_MOV_CODE = "21360";
				// === �������͹�дѺ ����駤�ҡ������͹����Թ��͹�� 21360 (����͹����Թ��͹���ͧ�ҡ����͹���˹�)

				$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
				$db_dpis1->send_cmd($cmd);
				$data = $db_dpis1->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$SAH_ID = $data[max_id] + 1; 			 
				$cmd = "	insert into PER_SALARYHIS 
								(SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
								SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE) 
								values 
								($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_SALARY_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO', 
								'$COM_DATE', '', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();	
				//echo "<br>";
			} // end if -- insert PER_SALARYHIS
			/*cdgs*/
			$aa=f_promote_person(f_get_personID($tmp_PER_ID),$strPosIDDpis,$strCmdPosition,$tmp_LEVEL_NO,$tmp_CMD_LEVEL,$tmp_CMD_DATE,f_get_movement_code($tmp_MOV_CODE),$tmp_CMD_DATE);
			/*cdgs*/
		}				

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����š�ô�ç���˹� �����������ѵԡ�ô�ç���˹�������׹�ѹ�����źѭ��Ṻ���¤��������͹�дѺ [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	}		// 	if( $command == "COMMAND" && trim($COM_ID) ) 	
// ============================================================
		
// ============================================================
	// ������ա���觨ҡ�����Ҥ
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �觢����źѭ��Ṻ���¤��������͹�дѺ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
			
		/***if($command == "DELETE" && trim($COM_ID) ){
			$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
				
			$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����źѭ��Ṻ���¤�������� [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		}***/
	//=========================================================================

	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ��������͹�дѺ [".trim($COM_ID)." : ".$PER_ID."]");
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����źѭ����Т���Ҫ���/�١��ҧṺ���ºѭ�դ��������͹�дѺ [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";
	}
	
	if ($command == "CONFIRM_ADD_ALL") {
		$cmd = " select from PER_PROMOTE_C where DE_YEAR= ";
	}

	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE,  
										a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID 
							from	PER_COMMAND a, PER_COMTYPE b
							where	COM_ID=$COM_ID and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
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