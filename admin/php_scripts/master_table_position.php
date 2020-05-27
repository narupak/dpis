<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	/* cdgs */
	include("php_scripts/psst_position.php");
	/*cdgs */
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);
	
        
        
        
	//echo "1..VIEW=$VIEW, UPD=$UPD<br>";
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if(!isset($show_topic)) $show_topic = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!trim($SG_CODE)) {
		$SG_CODE = "990";
		$SG_NAME = "�ѧ���Ѵ�����";
	}
	if (!trim($SKILL_CODE)) {
		if ($BKK_FLAG==1) {
			$SKILL_CODE = "0000";
			$SKILL_NAME = "-";
		} else {
			$SKILL_CODE = "683";
			$SKILL_NAME = "������ҢҪӹҭ���";
		}
	}
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $POS_ID => $POS_SEQ_NO){
			if($POS_SEQ_NO=="") { $cmd = " update PER_POSITION set POS_SEQ_NO='' where POS_ID=$POS_ID "; }
			else { $cmd = " update PER_POSITION set PER_SEQ_NO=$POS_SEQ_NO where POS_ID=$POS_ID ";  }

			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "cmd=$cmd<br>";
		} // end foreach

		$command = "SEARCH";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ�ؤ�ҡ� [$PER_ID : $FULL_NAME]");
	} // end if

	if($SESS_USERGROUP_LEVEL == 5 && $ORG_ID){
		$cmd = " select AP_CODE, PV_CODE, CT_CODE, OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$AP_CODE = trim($data[AP_CODE]);
		$PV_CODE = trim($data[PV_CODE]);
		$CT_CODE = trim($data[CT_CODE]);
		$ORG_OT_CODE = trim($data[OT_CODE]);

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$AP_NAME = trim($data[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PV_NAME = trim($data[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CT_NAME = trim($data[CT_NAME]);

		$cmd = " select OT_NAME from PER_ORG_TYPE where trim(OT_CODE)='$ORG_OT_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_OT_NAME = trim($data[OT_NAME]);
	} // end if
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_POSITION set POS_STATUS = 2 where POS_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_POSITION set POS_STATUS = 1 where POS_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}

	if($command=="ADD" || $command=="UPDATE"){
		$POS_DOC_NO = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_DOC_NO)));
		$POS_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_REMARK)));
		$POS_CONDITION = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_CONDITION)));
		
		if($_POST[ORG_ID])	$ORG_ID =$_POST[ORG_ID];
		if($_POST[ORG_ID_1])	$ORG_ID_1 =$_POST[ORG_ID_1];
		if($_POST[ORG_ID_2])	$ORG_ID_2 =$_POST[ORG_ID_2];
		if($_POST[ORG_ID_3])	$ORG_ID_3 =$_POST[ORG_ID_3];
		if($_POST[ORG_ID_4])	$ORG_ID_4 =$_POST[ORG_ID_4];
		if($_POST[ORG_ID_5])	$ORG_ID_5 =$_POST[ORG_ID_5];

		if($ORG_ID=="") $ORG_ID = "NULL";
		if($ORG_ID_1=="") $ORG_ID_1 = "NULL";
		if($ORG_ID_2=="") $ORG_ID_2 = "NULL";
		if($ORG_ID_3=="") $ORG_ID_3 = "NULL";
		if($ORG_ID_4=="") $ORG_ID_4 = "NULL";
		if($ORG_ID_5=="") $ORG_ID_5 = "NULL"; 
		$PT_CODE = (trim($PT_CODE))? "".$PT_CODE."" : "NULL";
		$PC_CODE = (trim($PC_CODE))? "".$PC_CODE."" : "NULL";
		$PM_CODE = (trim($PM_CODE))? "".$PM_CODE."" : "NULL";
		$PPT_CODE = (trim($PPT_CODE))? "".$PPT_CODE."" : "NULL";
		$PR_CODE = (trim($PR_CODE))? "".$PR_CODE."" : "NULL";

		$POS_SALARY += 0;
		$POS_MGTSALARY += 0;

		$POS_DATE =  save_date($POS_DATE);
		$POS_CHANGE_DATE =  save_date($POS_CHANGE_DATE);
		$POS_VACANT_DATE =  save_date($POS_VACANT_DATE); /*���*/
                /*Release 5.1.0.2 Begin*/
                //$POS_VACANT_DATE = $POS_DATE;
                /*Release 5.1.0.2 End*/
	} // end if
//echo $POS_NO .'&&'. $DEPARTMENT_ID;
	
	if($command=="ADD" ){
		// ========================== This code use before add DEPARTMENT_ID column to PER_POSITION table ==========================
		// $cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID order by ORG_ID ";
		// $db_dpis->send_cmd($cmd);
		// unset($arr_org);
		// while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		// $cmd = " select POS_ID, POS_NO from PER_POSITION where trim(POS_NO)='". trim($POS_NO) ."' and ORG_ID in (". implode(",", $arr_org) .") ";
		// =================================================================================================================

		// ====================== After add DEPARTMENT_ID column to PER_POSITION table use this code instead ==========================
		if ($POSITION_NO_CHAR=="Y")
			$cmd = " select POS_ID, POS_NO_NAME, POS_NO, POS_STATUS from PER_POSITION 
							where POS_NO_NAME='$POS_NO_NAME' and trim(POS_NO)='". trim($POS_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		else
			$cmd = " select POS_ID, POS_NO_NAME, POS_NO, POS_STATUS from PER_POSITION 
							where trim(POS_NO)='". trim($POS_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		// =================================================================================================================
		
                $count_duplicate = $db_dpis->send_cmd($cmd);
                $COMMA =","; 
                $idx = 0;
                $POS_NO_NAME_CHKS = "";
                $POS_NO_CHKS = "";
                while ($dataCHK = $db_dpis->get_array()){
                    $CHK_STATUS .=$dataCHK[POS_STATUS].$COMMA;
                    $POS_NO_NAME_CHKS = $dataCHK[POS_NO_NAME];
                    $POS_NO_CHKS = $dataCHK[POS_NO];
                }
                $CHK_EX = explode($COMMA,$CHK_STATUS,-1);
		if($count_duplicate <= 0 || $DEPARTMENT_NAME=='�����û���ͧ' || !in_array("1", $CHK_EX)){
			$cmd = " select max(POS_ID) as MAX_POS_ID from PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_ID = $data[MAX_POS_ID] + 1;
			//#######################################################
			//==����ͧ�����Ӥѭ%%%%%%%%%%�������¹�����ѹ�Ҩ�����������***************
			if(trim($PM_CODE) && $PM_CODE!="NULL"){
				$PM_CODE = str_replace("'","",$PM_CODE);
				$PM_CODE = str_replace($PM_CODE,"'$PM_CODE'",$PM_CODE);
			}
			if(trim($PC_CODE) && $PC_CODE!="NULL"){
				$PC_CODE = str_replace("'","",$PC_CODE);
				$PC_CODE = str_replace($PC_CODE,"'$PC_CODE'",$PC_CODE);
			}
                        if($FLAG_LEVEL == "Y"){
                            $FLAG_LEVEL = "Y";
                        }else if($FLAG_LEVEL == "" || $FLAG_LEVEL == "NULL"){
                            $FLAG_LEVEL = "N";
                        }

			$cmd = "insert into PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, 
						PM_CODE, PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, 
						POS_DOC_NO, POS_REMARK, POS_DATE, POS_VACANT_DATE, POS_CHANGE_DATE, POS_STATUS, 
						UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, PAY_NO, POS_ORGMGT, LEVEL_NO, POS_NO_NAME, 
						PPT_CODE, POS_RETIRE, POS_RESERVE, POS_RESERVE_DESC, POS_RESERVE_DOCNO, POS_RETIRE_REMARK, 
						PR_CODE, POS_RESERVE2,FLAG_LEVEL)
						values ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $PM_CODE, 
						'$PL_CODE', '$CL_NAME', $POS_SALARY, $POS_MGTSALARY,'$SKILL_CODE', $PT_CODE, $PC_CODE, '$POS_CONDITION', 
						'$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_VACANT_DATE', '$POS_CHANGE_DATE', $POS_STATUS, 
						$SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID, '$PAY_NO', '$POS_ORGMGT', '$LEVEL_NO', '$POS_NO_NAME', 
						$PPT_CODE, '$POS_RETIRE', '$POS_RESERVE', '$POS_RESERVE_DESC', '$POS_RESERVE_DOCNO', '$POS_RETIRE_REMARK', 
						$PR_CODE , '$POS_RESERVE2', '$FLAG_LEVEL') ";
			//#######################################################
			//echo "<pre>".$cmd;
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo " xxx :: $result ";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������ŵ��˹� [ $DEPARTMENT_ID : $POS_ID : $POS_NO_NAME : $POS_NO ]");
			$action_result = "<p><FONT SIZE='3' COLOR='#0000FF' align='center'>�����������Ţ�����˹� ".$POS_NO_NAME.$POS_NO." ���º��������</FONT></p>";
			
		}else{
			//$data = $db_dpis->get_array();
			//��� $err_text = "�Ţ�����˹觫�� [".$data[POS_NO_NAME].$data[POS_NO]."]";
                        $err_text = "�Ţ�����˹觫�� [".$POS_NO_NAME_CHKS.$POS_NO_CHKS."]";
			
//			if($PM_CODE == "NULL") $PM_CODE = "";
//			else $PM_CODE = substr($PM_CODE, 1, -1);

//			if($PC_CODE == "NULL") $PC_CODE = "";
//			else $PC_CODE = substr($PC_CODE, 1, -1);
			
/*			if($ORG_ID == "NULL") $ORG_ID = "";
			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";		*/
		} // end if
		$POS_ID="";	//���������������ҧ ��ѧ�ҡ ADD ����� 
		$POS_DATE = show_date_format($POS_DATE, 1);
		$POS_CHANGE_DATE = show_date_format($POS_CHANGE_DATE, 1);
		$POS_VACANT_DATE =  show_date_format($POS_VACANT_DATE, 1); /*���*/
                /*Release 5.1.0.2 Begin*/
                //$POS_VACANT_DATE =  $POS_DATE; /*Release 5.2.1.13 */
                /*Release 5.1.0.2 End*/
	} // end if

//echo "<br>1 = > $command=='UPDATE' && $POS_ID && $POS_NO && $DEPARTMENT_ID";
	if($command=="UPDATE" && $POS_ID && $POS_NO && $DEPARTMENT_ID){	
		//==����ͧ�����Ӥѭ%%%%%%%%%%�������¹�����ѹ�Ҩ�����������***************
		if(trim($PM_CODE) && $PM_CODE!="NULL"){	$PM_CODE = "'$PM_CODE'";		}
		if(trim($PT_CODE) && $PT_CODE!="NULL"){	$PT_CODE = "'$PT_CODE'";		}
		if(trim($PC_CODE) && $PC_CODE!="NULL"){	$PC_CODE = "'$PC_CODE'";		}
		if(trim($PPT_CODE) && $PPT_CODE!="NULL"){	$PPT_CODE = "'$PPT_CODE'";		}
		if(trim($PR_CODE) && $PR_CODE!="NULL"){	$PR_CODE = "'$PR_CODE'";		}
		if ($POSITION_NO_CHAR=="Y")
			$cmd = " select POS_ID, POS_NO_NAME, POS_NO from PER_POSITION 
							where POS_STATUS=1 and POS_NO_NAME='$POS_NO_NAME' and trim(POS_NO)='". trim($POS_NO) ."' and POS_ID!=$POS_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		else
			$cmd = " select POS_ID, POS_NO_NAME, POS_NO from PER_POSITION 
							where POS_STATUS=1 and trim(POS_NO)='". trim($POS_NO) ."' and POS_ID!=$POS_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		// =================================================================================================================
		
                $count_duplicate = $db_dpis->send_cmd($cmd);
                //echo "<pre>".$cmd;
//echo "<br>2 = > $count_duplicate <= 0 || $DEPARTMENT_NAME";
		if($count_duplicate <= 0 || $DEPARTMENT_NAME=='�����û���ͧ'){
			$cmd = " update PER_POSITION set
								ORG_ID = $ORG_ID, 
								POS_NO_NAME = '$POS_NO_NAME',
								POS_NO = '$POS_NO',
								PAY_NO = '$PAY_NO',
								OT_CODE = '$OT_CODE', 
								ORG_ID_1 = $ORG_ID_1, 
								ORG_ID_2 = $ORG_ID_2, 
								ORG_ID_3 = $ORG_ID_3, 
								ORG_ID_4 = $ORG_ID_4, 
								ORG_ID_5 = $ORG_ID_5, 
								PM_CODE = $PM_CODE, 
								PL_CODE = '$PL_CODE', 
								CL_NAME = '$CL_NAME', 
								POS_SALARY = $POS_SALARY, 
								POS_MGTSALARY = $POS_MGTSALARY, 
								SKILL_CODE = '$SKILL_CODE',
								PT_CODE = $PT_CODE, 
								PC_CODE = $PC_CODE, 
								POS_CONDITION = '$POS_CONDITION',
								POS_DOC_NO = '$POS_DOC_NO', 
								POS_REMARK = '$POS_REMARK',
								POS_DATE = '$POS_DATE', 
								POS_VACANT_DATE = '$POS_VACANT_DATE', 
								POS_CHANGE_DATE = '$POS_CHANGE_DATE', 
								POS_STATUS = $POS_STATUS, 
								POS_ORGMGT = '$POS_ORGMGT', 
								LEVEL_NO = '$LEVEL_NO', 
								PPT_CODE = $PPT_CODE, 
								POS_RETIRE = '$POS_RETIRE', 
								POS_RESERVE = '$POS_RESERVE', 
								POS_RESERVE_DESC = '$POS_RESERVE_DESC', 
								POS_RESERVE_DOCNO = '$POS_RESERVE_DOCNO', 
								POS_RETIRE_REMARK = '$POS_RETIRE_REMARK', 
								PR_CODE = $PR_CODE, 
								POS_RESERVE2 = '$POS_RESERVE2', 
								DEPARTMENT_ID = $DEPARTMENT_ID,
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
                                                                FLAG_LEVEL  = '$FLAG_LEVEL'    
							 where POS_ID=$POS_ID ";
		//echo "<pre>$cmd<br>";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$UPD = 1;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��䢢����ŵ��˹� [ $DEPARTMENT_ID : $POS_ID : $POS_NO_NAME : $POS_NO ]");
			
			//��ҧ˹�Ң��������
			
		}else{
			$data = $db_dpis->get_array();
			$err_text = "�Ţ�����˹觫�� [".$data[POS_NO_NAME].$data[POS_NO]."]";
		} // end if
	} // end if
	
	if($command=="DELETE" && $POS_ID){
		$cmd = " select	POS_NO_NAME, POS_NO from PER_POSITION where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO_NAME = $data[POS_NO_NAME];
		$POS_NO = $data[POS_NO];

		$cmd = " delete from PER_POS_MOVE where POS_ID=$POS_ID ";	
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from PER_ORDER_DTL where POS_ID_OLD=$POS_ID ";	
		$db_dpis->send_cmd($cmd);
		
		$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";	
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from PER_POSITION where POS_ID=$POS_ID ";	
		$db_dpis->send_cmd($cmd);
		$command='SEARCH';	
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ź�����ŵ��˹� [ $DEPARTMENT_ID : $POS_ID : $POS_NO_NAME : $POS_NO ]");
	} // end if

	if($POS_ID && $command != 'search_pl'){
		$cmd = "	select	POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE, POS_DATE, 
                                    POS_VACANT_DATE, POS_SALARY, POS_MGTSALARY, PC_CODE, POS_CONDITION, POS_STATUS, 
                                    POS_CHANGE_DATE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, 
                                    POS_DOC_NO, POS_REMARK, UPDATE_USER, UPDATE_DATE, PAY_NO, POS_ORGMGT, LEVEL_NO, 
                                    POS_NO_NAME, PPT_CODE, POS_RETIRE, POS_RESERVE, POS_RESERVE_DESC, POS_RESERVE_DOCNO, 
                                    POS_RETIRE_REMARK, PR_CODE, POS_RESERVE2,FLAG_LEVEL
							from		PER_POSITION 
							where	POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		//echo "<pre>$cmd";
		$data = $db_dpis->get_array();
		$POS_NO_NAME = trim($data[POS_NO_NAME]);
		$POS_NO = trim($data[POS_NO]);
		$PAY_NO = trim($data[PAY_NO]);
		$FLAG_LEVEL = trim($data[FLAG_LEVEL]);
                
		$OT_CODE = trim($data[OT_CODE]);
		$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$OT_NAME = trim($data_dpis2[OT_NAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME = trim($data_dpis2[PL_NAME]);

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME = trim($data_dpis2[PM_NAME]);
		
		$CL_NAME = trim($data[CL_NAME]);
		$CL_CODE = $CL_NAME;

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME = trim($data_dpis2[PT_NAME]);

		$SKILL_CODE = trim($data[SKILL_CODE]);
		$cmd = " select SKILL_NAME, a.SG_CODE, SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SG_CODE=b.SG_CODE and trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$SKILL_NAME = trim($data_dpis2[SKILL_NAME]);
		$SG_CODE = trim($data_dpis2[SG_CODE]);
		$SG_NAME = trim($data_dpis2[SG_NAME]);
		
		$POS_DATE = show_date_format($data[POS_DATE], 1);
		$POS_VACANT_DATE = show_date_format($data[POS_VACANT_DATE], 1);
		$POS_CHANGE_DATE = show_date_format($data[POS_CHANGE_DATE], 1);

		$POS_SALARY = trim($data[POS_SALARY]);
		$POS_MGTSALARY = trim($data[POS_MGTSALARY]);

		$PC_CODE = trim($data[PC_CODE]);
		$cmd = " select PC_NAME from PER_CONDITION where trim(PC_CODE)='$PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PC_NAME = trim($data_dpis2[PC_NAME]);

		$PPT_CODE = trim($data[PPT_CODE]);
		$cmd = " select PPT_NAME from PER_PRACTICE where trim(PPT_CODE)='$PPT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PPT_NAME = trim($data_dpis2[PPT_NAME]);

		$PR_CODE = trim($data[PR_CODE]);
		$cmd = " select PR_NAME from PER_POS_RESERVE where trim(PR_CODE)='$PR_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PR_NAME = trim($data_dpis2[PR_NAME]);

		$POS_CONDITION = trim($data[POS_CONDITION]);
		$POS_STATUS = trim($data[POS_STATUS]);
		$POS_RETIRE = trim($data[POS_RETIRE]);
		$POS_RESERVE = trim($data[POS_RESERVE]);
		$POS_RESERVE_DESC = trim($data[POS_RESERVE_DESC]);
		$POS_RESERVE2 = trim($data[POS_RESERVE2]);
		$POS_RESERVE_DOCNO = trim($data[POS_RESERVE_DOCNO]);
		$POS_RETIRE_REMARK = trim($data[POS_RETIRE_REMARK]);

		if($CTRL_TYPE < 4){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data_dpis2[ORG_NAME]);
			$MINISTRY_ID = $data_dpis2[ORG_ID_REF];
			if($CTRL_TYPE < 3 && $MINISTRY_ID){
				$MINISTRY_NAME = "";
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$MINISTRY_NAME = trim($data_dpis2[ORG_NAME]);
			} // end if
		} // end if
		
		$ORG_ID = trim($data[ORG_ID]);
                
                /*Release 5.2.1.8  Begin*/
                /** ��� :*/
                /*$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
                $db_dpis2->send_cmd($cmd);
                $data_dpis2 = $db_dpis2->get_array();
                $DEPARTMENT_NAME = trim($data_dpis2[ORG_NAME]);
                $ORG_ID_REF = $data_dpis2[ORG_ID_REF];
                if($CTRL_TYPE < 3 && $ORG_ID_REF){
                        $MINISTRY_NAME = "";
                        $cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_REF ";
                        $db_dpis2->send_cmd($cmd);
                        $data_dpis2 = $db_dpis2->get_array();
                        $MINISTRY_NAME = trim($data_dpis2[ORG_NAME]);
                } */
                /*Release 5.2.1.8  End*/
                
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$ORG_ID_4 = trim($data[ORG_ID_4]); 
		$ORG_ID_5 = trim($data[ORG_ID_5]); 
		if ($ORG_ID_5) $TMP_ORG_ID = $ORG_ID_5;
		elseif ($ORG_ID_4) $TMP_ORG_ID = $ORG_ID_4;
		elseif ($ORG_ID_3) $TMP_ORG_ID = $ORG_ID_3;
		elseif ($ORG_ID_2) $TMP_ORG_ID = $ORG_ID_2;
		elseif ($ORG_ID_1) $TMP_ORG_ID = $ORG_ID_1;
		elseif ($ORG_ID) $TMP_ORG_ID = $ORG_ID;

		if($DPISDB=="odbc"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		(
							 					(
													( 
														PER_ORG a 
														left join PER_COUNTRY b on a.CT_CODE=b.CT_CODE
													) left join PER_PROVINCE c on a.PV_CODE=c.PV_CODE
												) left join PER_AMPHUR d on a.AP_CODE=d.AP_CODE
											) left join PER_ORG_TYPE e on a.OT_CODE=e.OT_CODE
							 where 	ORG_ID=$TMP_ORG_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		PER_ORG a, PER_COUNTRY b, PER_PROVINCE c, PER_AMPHUR d, PER_ORG_TYPE e
							 where 	a.CT_CODE=b.CT_CODE(+) and a.PV_CODE=c.PV_CODE(+)
											and a.AP_CODE=d.AP_CODE(+) and a.OT_CODE=e.OT_CODE(+)
							 				and ORG_ID=$TMP_ORG_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		(
							 					(
													( 
														PER_ORG a 
														left join PER_COUNTRY b on a.CT_CODE=b.CT_CODE
													) left join PER_PROVINCE c on a.PV_CODE=c.PV_CODE
												) left join PER_AMPHUR d on a.AP_CODE=d.AP_CODE
											) left join PER_ORG_TYPE e on a.OT_CODE=e.OT_CODE
							 where 	ORG_ID=$TMP_ORG_ID ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data_dpis2 = $db_dpis2->get_array();
		$CT_CODE = trim($data_dpis2[CT_CODE]);
		$CT_NAME = trim($data_dpis2[CT_NAME]);
		$PV_CODE = trim($data_dpis2[PV_CODE]);
		$PV_NAME = trim($data_dpis2[PV_NAME]);
		$AP_CODE = trim($data_dpis2[AP_CODE]);
		$AP_NAME = trim($data_dpis2[AP_NAME]);
		$ORG_OT_CODE = trim($data_dpis2[OT_CODE]);
		$ORG_OT_NAME = trim($data_dpis2[OT_NAME]);
		
		$ORG_NAME = "";
		if($ORG_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_1 = "";
		if($ORG_ID_1){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_2 = "";
		if($ORG_ID_2){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_3 = "";
		if($ORG_ID_3){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_4 = "";
		if($ORG_ID_4){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);
		}
		$ORG_NAME_5 = "";
		if($ORG_ID_5){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);
		}
		
		$POS_DOC_NO = trim($data[POS_DOC_NO]);
		$POS_REMARK = trim($data[POS_REMARK]);
		$POS_ORGMGT = trim($data[POS_ORGMGT]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), 1);

		if($DPISDB=="odbc"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		
											(
												PER_POSITION a
												left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
											) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
								where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		PER_POSITION a, PER_PERSONAL b, PER_PRENAME c
								where		a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PN_CODE=c.PN_CODE(+) and PER_TYPE = 1 and PER_STATUS = 1 ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		
											(
												PER_POSITION a
												left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
											) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
								where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_ID_PAY = trim($data[PER_ID]);
		$PAYNAME = (trim($data[PN_NAME])?($data[PN_NAME]):"") . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PAY_DATE = "";
		if($PER_ID_PAY && $PAY_NO){
			$cmd = " select SAH_EFFECTIVEDATE
							from   PER_SALARYHIS
							where PER_ID=$PER_ID_PAY and SAH_PAY_NO='$PAY_NO'
							order by SAH_EFFECTIVEDATE ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PAY_DATE = show_date_format($data[SAH_EFFECTIVEDATE], 1);
		}
		$POS_SITUATION = 1;
		$cmd = "	select PER_ID from	PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE = 1 and PER_STATUS = 1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$cnt++;
			$PER_ID[$cnt] = trim($data[PER_ID]);

			if($PER_ID[$cnt]){
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS
								 from 		PER_PERSONAL a, PER_PRENAME b 
								 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] and PER_TYPE = 1 ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
				$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (�ͺ�è�)":"");
//				$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
				$POS_SITUATION = 2;
/*			
				if (!$RPT_N) {
					$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' ";
					$count_mgtsalary = $db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$POS_MGTSALARY = $data_dpis2[MS_SALARY];
				}
*/
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID[$cnt] and POH_POS_NO_NAME='$POS_NO_NAME' and POH_POS_NO='$POS_NO'
								order by POH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$POH_DATE[$cnt] = show_date_format($data_dpis2[POH_EFFECTIVEDATE], 1);
			} // end if
		} // end while
	} // end if

	//echo $ORG_ID."<<".$POS_ID;
//	echo "UPD=$UPD && DEL=$DEL && VIEW=$VIEW && err_text=$err_text && command=$command<br>";

if($command == 'up_pos_salary'){
	if($DEPARTMENT_ID){
		$arr_search[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID) ";	
	}if($ORG_ID){
		$arr_search[] = "(a.ORG_ID = $ORG_ID) ";	
	}if($ORG_ID_1){
		$arr_search[] = "(a.ORG_ID_1 = $ORG_ID_1) ";			
	}	
	 if(count($arr_search)) $search_all_department = "and".  implode(" and ", $arr_search);
	//�Է�������� Login

	$sql = "select a.PER_SALARY,b.POS_ID 
				from per_personal a, PER_POSITION b where 
			  a.POS_ID = b.POS_ID
			  and b.POS_STATUS = 1
			  and a.per_type = 1 
			  and a.PER_STATUS = 1  
			  $search_all_department";
			  //echo $sql ."<br>";
				$db_dpis2->send_cmd($sql);
			 while ($data = $db_dpis2->get_array()) {
				 $cmd = "UPDATE per_position set POS_SALARY = $data[PER_SALARY]
				 where POS_ID =  $data[POS_ID]"; 
	 
				 $db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
				/*cdgs */
				$a=f_del_psst_position($POS_ID,"11");
				/*cdgs */
				$UPD = 1;
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��Ѻ��ا�Թ��͹��ͨ������ç�Ѻ�Թ��͹�ͧ����ͧ���˹觻Ѩ�غѹ [ $DEPARTMENT_ID : $POS_ID : $POS_NO_NAME : $POS_NO ]");
			 } 
			 $command="";
}


	if( (!$UPD && !$DEL && !$VIEW && !$err_text && $command != "search_pl") ){
		$POS_ID = "";
		$POS_NO_NAME = "";
		$POS_NO = "";
		$OT_CODE = "";
		$OT_NAME = "";
		$PL_CODE = "";
		$PL_NAME = "";
		$PM_CODE = "";
		$PM_NAME = "";
		$CL_CODE = "";
		$CL_NAME = "";
//		$SG_CODE = "";
//		$SG_NAME = "";
//		$SKILL_CODE = "";
//		$SKILL_NAME = "";
		$PC_CODE = "";
		$PC_NAME = "";
		$PT_CODE = "";
		$PT_NAME = "";
		$PPT_CODE = "";
		$PPT_NAME = "";
		$PR_CODE = "";
		$PR_NAME = "";
		$CT_CODE = "";
		$CT_NAME = "";
		$PV_CODE = "";
		$PV_NAME = "";
		$AP_CODE = "";
		$AP_NAME = "";
		$ORG_OT_CODE = "";
		$ORG_OT_NAME = "";
		$POS_DOC_NO = "";
		$POS_SITUATION = "";
		$POS_DATE = "";
		$POS_VACANT_DATE = "";
		$POS_CHANGE_DATE = "";
		$LEVEL_NO = "";
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} 
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		}
		if($SESS_USERGROUP_LEVEL < 5){
			$ORG_ID = "";
			$ORG_NAME = "";
		} 
		if($SESS_USERGROUP_LEVEL < 6){
			$ORG_ID_1 = "";
			$ORG_NAME_1 = "";
		}
		$ORG_ID_2 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_5 = "";
		$ORG_NAME_5 = "";
		$POS_SALARY = "";
		$POS_MGTSALARY = "";
		$POS_STATUS = 1;
		$POS_CONDITION = "";
		$POS_REMARK = "";
		$POS_ORGMGT = "";
		$POS_RETIRE = "";
		$POS_RESERVE = "";
		$POS_RESERVE_DESC = "";
		$POS_RESERVE2 = "";
		$POS_RESERVE_DOCNO = "";
		$POS_RETIRE_REMARK = "";
		$PER_ID[1] = "";
		$FULLNAME[1] = "";
		$POH_DATE[1] = "";
		$PER_ID[2] = "";
		$FULLNAME[2] = "";
		$POH_DATE[2] = "";
		$PER_ID[3] = "";
		$FULLNAME[3] = "";
		$POH_DATE[3] = "";
		$PER_ID[4] = "";
		$FULLNAME[4] = "";
		$POH_DATE[4] = "";
		$PAYNAME = "";
		$PAY_DATE = "";
		$SHOW_UPDATE_USER = "";
		$SHOW_UPDATE_DATE = "";
	} // end if	
//	echo "2..VIEW=$VIEW, UPD=$UPD<br>";

        if($command=="test"){
            if($FLAG_LEVEL == "Y"){
                $FLAG_LEVEL = "Y";
                echo "=> $FLAG_LEVEL";
            }else if($FLAG_LEVEL == "" || $FLAG_LEVEL == "NULL"){
                $FLAG_LEVEL = "N";
                echo "=> $FLAG_LEVEL";
            }
        }
?>