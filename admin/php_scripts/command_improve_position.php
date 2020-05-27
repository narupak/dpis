<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");
	/*cdgs*/
	include("php_scripts/psst_position.php");
	/*cdgs*/

//	echo ">> command=$command, ORD_ID=$ORD_ID, ORD_SEQ=$ORD_SEQ, ORD_CONFIRM=$ORD_CONFIRM, UPD=$UPD, VIEW=$VIEW<br>";

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
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	
	if (($BKK_FLAG==1 || $MFA_FLAG==1) && (trim($DEPARTMENT_ID)=="" || $DEPARTMENT_ID=="NULL" || is_null($DEPARTMENT_ID))) $DEPARTMENT_ID="''";
	$ORD_CONFIRM += 0;
	$ORD_DATE =  save_date($ORD_DATE);

	if($command == "CONFIRMCOMMAND"){
//		echo ">>$list_confirm_id<br>";
//		foreach($list_confirm_id as $ORD_ID){
        $debug=0;
			$cmd = " select ORD_YEAR, ORD_NO, ORD_TITLE from PER_ORDER where ORD_ID=$ORD_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data = $db_dpis->get_array();
			$ORD_YEAR = $data[ORD_YEAR];
			$ORD_NO = $data[ORD_NO];
			$ORD_TITLE = $data[ORD_TITLE];
			
			$cmd = " update PER_ORDER set ORD_CONFIRM=1 where ORD_ID=$ORD_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis2->show_error();
			
			$cmd = " select 		POS_ID_OLD, ORD_POS_NO_NAME, ORD_POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME,  PT_CODE, SKILL_CODE, PC_CODE, 
								ORD_CONDITION, ORD_SALARY, ORD_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, ORD_EFF_DATE,
								LEVEL_NO, ORD_REMARK
					 from 		PER_ORDER_DTL 
					 where 		ORD_ID=$ORD_ID and ORD_RESULT=1
					 order by 	ORD_SEQ	  ";
			$db_dpis->send_cmd($cmd);
        if($debug==1){echo "���¡���˹���ѧ��Ѻ��ا| <pre>".$cmd."<br>";}
			while($data = $db_dpis->get_array()){
				$POS_ID_OLD = $data[POS_ID_OLD];
				$ORD_POS_NO_NAME = trim($data[ORD_POS_NO_NAME]);
				$ORD_POS_NO = trim($data[ORD_POS_NO]);
				$OT_CODE = $data[OT_CODE];
				$PL_CODE = $data[PL_CODE];
				$PM_CODE = $data[PM_CODE];
				$CL_NAME = $data[CL_NAME];
				$PT_CODE = $data[PT_CODE];
				$PT_CODE_tmp = (trim($PT_CODE))? "'".$PT_CODE."'" : "NULL";
				$SKILL_CODE = $data[SKILL_CODE];
				$PC_CODE = $data[PC_CODE];
				$PC_CODE_tmp = (trim($PC_CODE))? "'".$PC_CODE."'" : "NULL";
				$ORD_CONDITION = $data[ORD_CONDITION];
				$ORD_SALARY = ($data[ORD_SALARY]!="")?$data[ORD_SALARY]:"NULL";
				$ORD_MGTSALARY = ($data[ORD_MGTSALARY]!="")?$data[ORD_MGTSALARY]:"NULL";
				$ORG_ID = ($data[ORG_ID]!="")?$data[ORG_ID]:"NULL";
				$ORG_ID_1 = ($data[ORG_ID_1]!="")?$data[ORG_ID_1]:"NULL";
				$ORG_ID_2 = ($data[ORG_ID_2]!="")?$data[ORG_ID_2]:"NULL";
				$ORG_ID_3 = ($data[ORG_ID_3]!="")?$data[ORG_ID_3]:"NULL";
				$ORG_ID_4 = ($data[ORG_ID_4]!="")?$data[ORG_ID_4]:"NULL";
				$ORG_ID_5 = ($data[ORG_ID_5]!="")?$data[ORG_ID_5]:"NULL";
				$ORD_EFF_DATE = trim($data[ORD_EFF_DATE]);
				$LEVEL_NO = $data[LEVEL_NO];
				$ORD_REMARK = $data[ORD_REMARK];
				
				$cmd = " select 	OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, PC_CODE, POS_CONDITION,
								POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, 
								POS_STATUS, POS_DOC_NO, LEVEL_NO, POS_REMARK
						 from	PER_POSITION
						 where	POS_ID=$POS_ID_OLD ";
				$db_dpis2->send_cmd($cmd);
                if($debug==1){echo "���¡���˹觡�͹��Ѻ��ا| <pre>".$cmd."<br>";}
				$data2 = $db_dpis2->get_array();
				$PREV_OT_CODE = $data2[OT_CODE];
				$PREV_PL_CODE = $data2[PL_CODE];
				$PREV_PM_CODE = $data2[PM_CODE];
				$PREV_CL_NAME = $data2[CL_NAME];
				$PREV_PT_CODE = $data2[PT_CODE];
				$PREV_PT_CODE_tmp = (trim($PREV_PT_CODE))? "'".$PREV_PT_CODE."'" : "NULL";				
				$PREV_SKILL_CODE = $data2[SKILL_CODE];
				$PREV_PC_CODE = $data2[PC_CODE];
				$PREV_PC_CODE_tmp = (trim($PREV_PC_CODE))? "'".$PREV_PC_CODE."'" : "NULL";				
				$PREV_POS_CONDITION = $data2[POS_CONDITION];
				$PREV_POS_SALARY = ($data2[POS_SALARY]!="")?$data2[POS_SALARY]:"NULL";
				$PREV_POS_MGTSALARY = ($data2[POS_MGTSALARY]!="")?$data2[POS_MGTSALARY]:"NULL";
				$PREV_ORG_ID = ($data2[ORG_ID]!="")?$data2[ORG_ID]:"NULL";
				$PREV_ORG_ID_1 = ($data2[ORG_ID_1]!="")?$data2[ORG_ID_1]:"NULL";
				$PREV_ORG_ID_2 = ($data2[ORG_ID_2]!="")?$data2[ORG_ID_2]:"NULL";
				$PREV_ORG_ID_3 = ($data2[ORG_ID_3]!="")?$data2[ORG_ID_3]:"NULL";
				$PREV_ORG_ID_4 = ($data2[ORG_ID_4]!="")?$data2[ORG_ID_4]:"NULL";
				$PREV_ORG_ID_5 = ($data2[ORG_ID_5]!="")?$data2[ORG_ID_5]:"NULL";
				$PREV_POS_STATUS = $data2[POS_STATUS];
				$PREV_POS_DOC_NO = $data2[POS_DOC_NO];
				$PREV_LEVEL_NO = $data2[LEVEL_NO];
				$PREV_POS_REMARK = $data2[POS_REMARK];
				
				// ================================= �ѹ�֡����ѵԵ��˹� =======================================
				$POS_DATE = date("Y-m-d 00:00:00");	
				$cmd = " insert into PER_POS_MOVE	(POS_ID, POS_DATE, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, 
									SKILL_CODE, PC_CODE, POS_CONDITION, POS_SALARY, POS_MGTSALARY, ORG_ID, ORG_ID_1, 
									ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_STATUS, POS_DOC_NO, LEVEL_NO, 
									POS_REMARK, UPDATE_USER, UPDATE_DATE)
								 values ($POS_ID_OLD, '$ORD_EFF_DATE', '$PREV_OT_CODE', '$PREV_PL_CODE', '$PREV_PM_CODE', '$PREV_CL_NAME', $PREV_PT_CODE_tmp,
									'$PREV_SKILL_CODE', $PREV_PC_CODE_tmp, '$PREV_POS_CONDITION', $PREV_POS_SALARY, $PREV_POS_MGTSALARY,
									$PREV_ORG_ID, $PREV_ORG_ID_1, $PREV_ORG_ID_2, $PREV_ORG_ID_3, $PREV_ORG_ID_4, $PREV_ORG_ID_5, '$PREV_POS_STATUS', 
									'$PREV_POS_DOC_NO', '$PREV_LEVEL_NO', '$PREV_POS_REMARK',	$SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
                if($debug==1){echo "�ѹ�֡���˹觡�͹��Ѻ��ا| <pre>".$cmd."<br>";}
				//$db_dpis2->show_error();
				
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �ѹ�֡����ѵԵ��˹� [". $POS_ID_OLD ." : ".$ORD_POS_NO_NAME ." : ".trim($ORD_POS_NO)." : ".$PREV_PL_CODE."]");

				// ================================= ��Ѻ��ا���˹� =======================================
				$cmd = " update PER_POSITION set 
									OT_CODE = '$OT_CODE',
									PL_CODE = '$PL_CODE',
									PM_CODE = '$PM_CODE',
									CL_NAME = '$CL_NAME',
									PT_CODE = $PT_CODE_tmp,
									SKILL_CODE = '$SKILL_CODE',
									PC_CODE = $PC_CODE_tmp,
									POS_CONDITION = '$ORD_CONDITION',
									POS_SALARY = $ORD_SALARY,
									POS_MGTSALARY = $ORD_MGTSALARY,
									ORG_ID = $ORG_ID,
									ORG_ID_1 = $ORG_ID_1,
									ORG_ID_2 = $ORG_ID_2,
									ORG_ID_3 = $ORG_ID_3,
									ORG_ID_4 = $ORG_ID_4,
									ORG_ID_5 = $ORG_ID_5,
									LEVEL_NO = '$LEVEL_NO',
									POS_REMARK = '$ORD_REMARK',
									POS_DOC_NO = '$ORD_NO',
									POS_CHANGE_DATE = '$ORD_EFF_DATE',
									UPDATE_USER = $SESS_USERID,
									UPDATE_DATE = '$UPDATE_DATE'
								 where POS_ID=$POS_ID_OLD ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				if($debug==1){echo "�ѹ�֡���˹���ѧ��Ѻ��ا| <pre>".$cmd."<br>";}
				//cdgs
				$aa=f_ins_psst_position($POS_ID_OLD,"11",$ORD_POS_NO);
				//cdgs
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��Ѻ��ا���˹� [". $POS_ID_OLD ." : ".$ORD_POS_NO_NAME ." : ".trim($ORD_POS_NO)." : ".$PL_CODE."]");
			} // end while
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �׹�ѹ������ [". $ORD_ID ." : ".trim($ORD_YEAR)." : ".$ORD_NO." : ".$ORD_TITLE."]");
//		} // end foreach
	} // end if

	if($command == "ADD" && trim($ORD_YEAR) && trim($ORD_NO)){
		$cmd = " select 		ORD_YEAR, ORD_NO, ORD_TITLE
				   from 		PER_ORDER 
				   where 	trim(ORD_YEAR)='". trim($ORD_YEAR) ."' and trim(ORD_NO)='". trim($ORD_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//		echo "add select - $cmd (count_duplicate=$count_duplicate)<br>";
		if($count_duplicate <= 0){
			$cmd = " select max(ORD_ID) as max_id from PER_ORDER ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ORD_ID = $data[max_id] + 1;

			$cmd = " insert into PER_ORDER (ORD_ID, ORD_YEAR, ORD_NO, ORD_TITLE, ORD_DATE, ORD_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
						   values ($ORD_ID, '".trim($ORD_YEAR)."', '".$ORD_NO."', '".$ORD_TITLE."', '".$ORD_DATE."', 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "add insert - $cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".$ORD_ID." : ".trim($ORD_YEAR)." : ".$ORD_NO." : ".$ORD_TITLE." : ".$ORD_DATE."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [$DEPARTMENT_NAME ".$data[ORD_YEAR]." ".$data[ORD_NO]." ".$data[ORD_TITLE]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($ORD_ID) && trim($ORD_YEAR) && trim($ORD_NO)){
		$cmd = " update PER_ORDER set 
							ORD_YEAR='".$ORD_YEAR."', 
							ORD_NO='".$ORD_NO."', 
							ORD_TITLE='".$ORD_TITLE."', 
							ORD_DATE='".$ORD_DATE."', 
							ORD_CONFIRM = $ORD_CONFIRM,
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "update insert - $cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [".$ORD_ID." : ".trim($ORD_YEAR)." : ".$ORD_NO." : ".$ORD_TITLE." : ".$ORD_DATE."]");
	}
	
	if($command == "DELETE" && trim($ORD_ID)){
		$cmd = " select ORD_YEAR, ORD_NO, ORD_TITLE from PER_ORDER where ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORD_YEAR = $data[ORD_YEAR];
		$ORD_NO = $data[ORD_NO];
		$ORD_TITLE = $data[ORD_TITLE];
		
		$cmd = " delete from PER_ORDER_DTL where ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " delete from PER_ORDER where ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [". $ORD_ID ." : ".trim($ORD_YEAR)." : ".$ORD_NO." : ".$ORD_TITLE."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 		ORD_YEAR, ORD_NO, ORD_TITLE, ORD_DATE, ORD_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE 
				   from 		PER_ORDER 
				   where 	ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ORD_YEAR = trim($data[ORD_YEAR]);
		$ORD_NO = trim($data[ORD_NO]);
		$ORD_TITLE = trim($data[ORD_TITLE]);
		$ORD_DATE = show_date_format($data[ORD_DATE], 1);
		$ORD_CONFIRM = $data[ORD_CONFIRM];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$MINISTRY_ID = $DEPARTMENT_NAME = "";
		if ($DEPARTMENT_ID) {
			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		}
		$MINISTRY_NAME = "";
		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
		
		$cmd = " select 	ORD_POS_NO_NAME, ORD_POS_NO, ORD_EFF_DATE, ORD_RESULT 
						 from 		PER_ORDER_DTL 
						 where 	ORD_ID=$ORD_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			if(trim($data[ORD_EFF_DATE]) == ""){
				$ARR_DETAIL_ERR[] = "�Ţ�����˹� $data[ORD_POS_NO_NAME]$data[ORD_POS_NO] �ѧ������к��ѹ����ռźѧ�Ѻ��";
			}elseif(trim($data[ORD_RESULT]) == ""){
				$ARR_DETAIL_ERR[] = "�Ţ�����˹� $data[ORD_POS_NO_NAME]$data[ORD_POS_NO] �ѧ������кؼš�õ�Ǩ�ͺ�����";
			} // end if
		} // end while
	} // end if
	
//	echo "UPD=$UPD && DEL=$DEL && VIEW=$VIEW && err_text=$err_text<br>";
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$ORD_ID = "";
		$ORD_YEAR = "";
		$ORD_NO = "";
		$ORD_TITLE = "";
		$ORD_DATE = "";
		$ORD_CONFIRM = "";
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
	} // end if
?>