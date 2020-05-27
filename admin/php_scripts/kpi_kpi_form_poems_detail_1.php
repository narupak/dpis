<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;
        $debug = 0;/*0=close ,1=open*/
        
        
        
        
        
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

	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
//	$REVIEW_PL_NAME = $REVIEW_PL_NAME0 = $REVIEW_PL_NAME1 = $REVIEW_PL_NAME2 = "";
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
        
        
	if(($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3 && trim($SESS_PER_ID)){
                
                 /* เก่า */   
            	/*$cmd = " select 	a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO, b.LEVEL_NAME, b.POSITION_LEVEL, 
					a.PER_SALARY, a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID , a.DEPARTMENT_ID, a.OT_CODE, a.PER_PROBATION_FLAG
				   from 		PER_PERSONAL  a, PER_LEVEL b
				   where 	PER_ID=$SESS_PER_ID and a.LEVEL_NO=b.LEVEL_NO ";*/ 
                
                /* ทดสอบ LEFT JOIN 17/02/2017 */                   
                $cmd = " select 	a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO, b.LEVEL_NAME, b.POSITION_LEVEL, 
					a.PER_SALARY, a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID , a.DEPARTMENT_ID, a.OT_CODE, a.PER_PROBATION_FLAG,
                                        a.PAY_ID
				   from 		PER_PERSONAL  a
                                   LEFT JOIN PER_LEVEL b ON (trim(a.LEVEL_NO)=trim(b.LEVEL_NO))
				   where 	a.PER_ID=$SESS_PER_ID ";                   
                
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $SESS_PER_ID;
		$PN_CODE = trim($data[PN_CODE]);
		$TMP_PER_NAME = trim($data[PER_NAME]) ." ". trim($data[PER_SURNAME]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if ($POSITION_LEVEL) $LEVEL_NAME = $POSITION_LEVEL;
		$dbPER_SALARY = $data[PER_SALARY];
		//$MY_PER_TYPE = $data[PER_TYPE];//เพิ่มเติม
                $PER_TYPE = $data[PER_TYPE];
                
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$OT_CODE = trim($data[OT_CODE]);
		$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
                $PAY_ID = trim($data[PAY_ID]);
		
		if($PER_TYPE == 1){
			$POS_ID = $data[POS_ID];
			$cmd = " select 	a.ORG_ID, d.ORG_NAME, b.PL_NAME, a.PT_CODE 
					   from 		PER_POSITION a, PER_LINE b, PER_ORG d
					   where 	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=d.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ORG_ID =  trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
			//$PL_NAME = trim($data[PL_NAME])?($data[PL_NAME] . $POSITION_LEVEL . ((trim($data[PT_NAME]) != "ทั่วไป" && $LEVEL_NO >= 6)?"$data[PT_NAME]":"")):" ".$LEVEL_NAME;/*เดิม*/
                        /*Release 5.1.0.5 Begin*/
                        $PL_NAME = trim($data[PL_NAME]);
                        /*Release 5.1.0.5 End*/
		}elseif($PER_TYPE == 2){
			$POEM_ID = $data[POEM_ID];
			$cmd = " select 	a.ORG_ID,b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PN_NAME]);
			$ORG_ID =  trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE==3){
			$POEMS_ID = $data[POEMS_ID];
			$cmd = " select 	a.ORG_ID,b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[EP_NAME]);
			$ORG_ID =  trim($data[ORG_ID]);
			$ORG_NAME = trim($data[ORG_NAME]);
		} // end if
                
                /**/
                if($PER_TYPE==1) 
                    if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
                            $cmd = " select 	ORG_ID, ORG_ID_1, PL_NAME, PM_CODE from PER_POSITION a, PER_LINE b 
                                                            where POS_ID = $PAY_ID and a.PL_CODE=b.PL_CODE ";		
                    else
                            $cmd = " select	 ORG_ID, ORG_ID_1, PL_NAME, PM_CODE from PER_POSITION a, PER_LINE b 
                                                            where POS_ID = $POS_ID and a.PL_CODE=b.PL_CODE ";		
                elseif($PER_TYPE==2) 
                        $cmd = " select	 ORG_ID, ORG_ID_1, PN_NAME as PL_NAME from PER_POS_EMP a, PER_POS_NAME b 
                                                        where POEM_ID = $POS_ID and a.PN_CODE=b.PN_CODE ";		
                elseif($PER_TYPE==3) 
                        $cmd = " select	 ORG_ID, ORG_ID_1, EP_NAME as PL_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
                                                        where POEMS_ID = $POS_ID and a.EP_CODE=b.EP_CODE ";		
                $db_dpis->send_cmd($cmd);
    //			$db_dpis->show_error();
                $data = $db_dpis->get_array();
                $ORG_ID = $data[ORG_ID];
                $ORG_ID_1 = $data[ORG_ID_1];
                if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
                $PL_NAME = trim($data[PL_NAME]);
                $PM_CODE = trim($data[PM_CODE]);
                $PM_NAME='';
                if ($PM_CODE) {
                        $cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
                        $db_dpis->send_cmd($cmd);
                        $data = $db_dpis->get_array();
                        $PM_NAME = trim($data[PM_NAME]);
                }
                /**/
                
		//echo "[$PL_NAME]";
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = trim($data[PN_NAME]).$TMP_PER_NAME;

		$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
	}
	
	function list_per_level_kpi ($name, $val, $POS_ID,$USER_VIEW='') {
		global $db_list, $db_dpis, $DPISDB, $PER_TYPE, $LEVEL_TITLE;
                /* ปรับเพิ่ม*/
                global $REVIEW_PER_TYPE,$REVIEW_PER_TYPE0,$REVIEW_PER_TYPE1,$REVIEW_PER_TYPE2;
                if($USER_VIEW=='USER_VIEW'){
                    $PER_TYPE=$REVIEW_PER_TYPE;
                }
                if($USER_VIEW=='USER_VIEW0'){
                    $PER_TYPE=$REVIEW_PER_TYPE0;
                }
                if($USER_VIEW=='USER_VIEW1'){
                    $PER_TYPE=$REVIEW_PER_TYPE1;
                }
                if($USER_VIEW=='USER_VIEW2'){
                    $PER_TYPE=$REVIEW_PER_TYPE2;
                }
                /**/
		if ($PER_TYPE==1) {
			$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX FROM PER_CO_LEVEL a, PER_POSITION b where a.CL_NAME = b.CL_NAME and b.POS_ID=$POS_ID ";
			$count_data = $db_list->send_cmd($cmd);
//			echo "cmd=$cmd ($count_data)<br>";
			if ($count_data) {
				$data = $db_list->get_array();
				$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]); 
				$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]); 
				$where = "";
				if ($LEVEL_NO_MIN)
					$where .= " and LEVEL_NO >= '$LEVEL_NO_MIN' ";
				if ($LEVEL_NO_MAX)
					$where .= " and LEVEL_NO <= '$LEVEL_NO_MAX' ";
			}
		}

		$cmd = "	select LEVEL_NO, LEVEL_NAME , POSITION_LEVEL FROM PER_LEVEL
							where PER_TYPE = $PER_TYPE AND LEVEL_ACTIVE = 1 $where
							order by PER_TYPE, LEVEL_SEQ_NO ";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();
		echo "<select name=\"$name\" id=\"$name\"  class=\"selectbox\" onChange=\"javascript:set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');\">";
		echo "<option value='X'>== ".$LEVEL_TITLE." ==</option>";
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$tmp_dat = trim($data_list[LEVEL_NO]);
			$tmp_name = trim($data_list[LEVEL_NAME]);
			$tmp_pos_level[$tmp_dat] = trim($data_list[POSITION_LEVEL]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='".$tmp_dat."' $sel>". $tmp_name ."</option>";	//'".$tmp_dat."[::]".$tmp_name."'
		} 
		echo "</select>";
                echo "<input type=\"hidden\" id=\"lnameX\"  value=\"X\">";
		foreach($tmp_pos_level as $key=>$value){
			echo "<input type=\"hidden\" id=\"lname$key\"  value=\"$value\">";
		}
		return $val;
	} // end function	

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
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case
        
	if(!$KF_COPY_FROMYEAR)	$KF_COPY_FROMYEAR = $KPI_BUDGET_YEAR;
	if(!$search_kf_year)	$search_kf_year = $KPI_BUDGET_YEAR;
	if(!$search_kf_year){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if

	if (!$KF_COPY_FROMCYCLE) $KF_COPY_FROMCYCLE = $KPI_CYCLE;
	if (!$KF_CYCLE) $KF_CYCLE = $KPI_CYCLE;
	if (!$KF_CYCLE) 
		if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $KF_CYCLE = 1;
		elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $KF_CYCLE = 2;
/*		
	if(!isset($search_kf_cycle)){
		$search_kf_cycle[] = $KF_CYCLE;
	} // end if
*/	
	if($KF_START_DATE_1){
		$KF_CYCLE = 1;
		$KF_START_DATE =  save_date($KF_START_DATE_1);
		$KF_END_DATE =  save_date($KF_END_DATE_1);
	}elseif($KF_START_DATE_2){
		$KF_CYCLE = 2;
		$KF_START_DATE =  save_date($KF_START_DATE_2);
		$KF_END_DATE =  save_date($KF_END_DATE_2);
	} // end if
	
	if($command == "ADD" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($PER_ID)){
		$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, ORG_ID, ORG_ID_1, PER_ID_REF, PER_ID_ASS_REF, 
											OT_CODE, LEVEL_NO, PER_PROBATION_FLAG, PER_SALARY
						 from			PER_PERSONAL
						 where		PER_ID = $PER_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_TYPE = $data[PER_TYPE];
		if($PER_TYPE == 1){
			$POS_ID = $data[POS_ID];
			$table = "PER_POSITION";
			$field = "POS_ID";
		}elseif($PER_TYPE == 2){
			$POS_ID = $data[POEM_ID];
			$table = "PER_POS_EMP";
			$field = "POEM_ID";
		}elseif($PER_TYPE == 3){
			$POS_ID = $data[POEMS_ID]; 
			$table = "PER_POS_EMPSER";
			$field = "POEMS_ID";
		}elseif($PER_TYPE == 4){
			$POS_ID = $data[POT_ID];
			$table = "PER_POS_TEMP";
			$field = "POT_ID";
		}
		$ORG_ID_ASS = $data[ORG_ID];
		$ORG_ID_1_ASS = $data[ORG_ID_1];
		$PER_ID_REF = $data[PER_ID_REF];
		$PER_ID_ASS_REF = $data[PER_ID_ASS_REF];
		$FRIEND_FLAG = "N";
		if ($PER_ID_REF) $FRIEND_FLAG = "Y";
		$OT_CODE = trim($data[OT_CODE]);
		$PER_PROBATION_FLAG = trim($data[PER_PROBATION_FLAG]);
		if ($PER_PROBATION_FLAG==1){
			$PERFORMANCE_WEIGHT = $WEIGHT_PROBATION;
			$COMPETENCE_WEIGHT = $WEIGHT_PROBATION;
			$OTHER_WEIGHT = "NULL";
		}elseif ($OT_CODE=="08"){
			$PERFORMANCE_WEIGHT = $WEIGHT_KPI_E;
			$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_E;
			$OTHER_WEIGHT = "NULL";
		}elseif ($OT_CODE=="09"){
			$PERFORMANCE_WEIGHT = $WEIGHT_KPI_S;
			$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_S;
			$OTHER_WEIGHT = "NULL";
		}else{
			$PERFORMANCE_WEIGHT = $WEIGHT_KPI;
			$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE;
			$OTHER_WEIGHT = $WEIGHT_OTHER;
		}
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$cmd = " select	ORG_ID, ORG_ID_1 from $table where $field = $POS_ID ";		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_ID = $data[ORG_ID];
		$ORG_ID_1 = $data[ORG_ID_1];
		if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
		if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
		if (!$ORG_ID_1_ASS) $ORG_ID_1_ASS = "NULL";
		if (!$PER_ID_REF) $PER_ID_REF = "NULL";
		if (!$PER_ID_ASS_REF) $PER_ID_ASS_REF = "NULL";
		if(!$PER_ID_REVIEW) $PER_ID_REVIEW = "NULL";
		if(!$PER_ID_REVIEW0) $PER_ID_REVIEW0 = "NULL";
		if(!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
		if(!$PER_ID_REVIEW2) $PER_ID_REVIEW2 = "NULL";
		if(!$OTHER_WEIGHT) $OTHER_WEIGHT = "NULL";
	
			
		if($DPISDB == "odbc"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	LEFT(KF_END_DATE, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	SUBSTR(KF_END_DATE, 1, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	LEFT(KF_END_DATE, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if(!$count_duplicate){
			$cmd = " select max(KF_ID) as max_id from PER_KPI_FORM ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$KF_ID = $data[max_id] + 1;
                        
                        /*แก้ปัญหา ข้อมูลขยะที่ทำให้เกิดข้อมูลซ้ำ*/
                        $cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);
                        /*++++++++++++++++++++++++++++++++++++++++++*/
			
			$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
							PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
							UPDATE_DATE, ORG_ID, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, FRIEND_FLAG, 
							ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, ORG_ID_1_ASS, 
							LEVEL_NO, PER_SALARY, PL_NAME, PM_NAME, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, 
							REVIEW_PM_NAME, REVIEW_LEVEL_NO, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
							REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, REVIEW_PN_NAME1, REVIEW_PER_NAME1, REVIEW_PL_NAME1, 
							REVIEW_PM_NAME1, REVIEW_LEVEL_NO1, REVIEW_PN_NAME2, REVIEW_PER_NAME2, REVIEW_PL_NAME2, 
							REVIEW_PM_NAME2, REVIEW_LEVEL_NO2) 
							values ($KF_ID, $PER_ID, '$PER_CARDNO', $KF_CYCLE, '$KF_START_DATE', '$KF_END_DATE', 	$PER_ID_REVIEW, 
							$PER_ID_REVIEW0, $PER_ID_REVIEW1, $PER_ID_REVIEW2, $DEPARTMENT_ID, $SESS_USERID, 
							'$UPDATE_DATE', $ORG_ID, 'Y', 	$ORG_ID, 'Y', $ORG_ID, $PER_ID_REF, '$FRIEND_FLAG', $ORG_ID_1, 
							$ORG_ID_ASS, $PERFORMANCE_WEIGHT, $COMPETENCE_WEIGHT, $OTHER_WEIGHT, $ORG_ID_1_ASS, 
							'$LEVEL_NO', $PER_SALARY, '$PL_NAME', '$PM_NAME', '$REVIEW_PN_NAME', '$REVIEW_PER_NAME', 
							'$REVIEW_PL_NAME', '$REVIEW_PM_NAME', '$REVIEW_LEVEL_NO', '$REVIEW_PN_NAME0', 
							'$REVIEW_PER_NAME0', '$REVIEW_PL_NAME0', '$REVIEW_PM_NAME0', '$REVIEW_LEVEL_NO0', 
							'$REVIEW_PN_NAME1', '$REVIEW_PER_NAME1', '$REVIEW_PL_NAME1', '$REVIEW_PM_NAME1', '$REVIEW_LEVEL_NO1', 
							'$REVIEW_PN_NAME2', '$REVIEW_PER_NAME2', '$REVIEW_PL_NAME2', '$REVIEW_PM_NAME2', '$REVIEW_LEVEL_NO2') ";
			$db_dpis->send_cmd($cmd);
                        //echo "1: ".$cmd."<br><br>";
//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");

			// ========================== insert competence from kpi_position_competence  ========================== //
			if($PER_TYPE==1 || $PER_TYPE==3) {
				$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$KC_ID = $data2[max_id] + 1;
		
                                /*เดิม*/
				/*$cmd = " select		CP_CODE, PC_TARGET_LEVEL
								 from		PER_POSITION_COMPETENCE
								 where	POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and PER_TYPE = $PER_TYPE
								 order by CP_CODE ";*/
                                /*Release 5.1.0.5 Begin*/
                                $cmd = " select pcom.CP_CODE, pcom.PC_TARGET_LEVEL
                                         from PER_POSITION_COMPETENCE pcom,PER_COMPETENCE  comp
                                         where pcom.CP_CODE=comp.CP_CODE and pcom.DEPARTMENT_ID=comp.DEPARTMENT_ID and pcom.DEPARTMENT_ID = $DEPARTMENT_ID 
                                             and comp.CP_ASSESSMENT = 'Y' 
                                             and pcom.POS_ID=$POS_ID and pcom.PC_TARGET_LEVEL > 0 and pcom.PER_TYPE = $PER_TYPE
                                         order by pcom.CP_CODE ";
                                /*Release 5.1.0.5 End*/
				$db_dpis->send_cmd($cmd);
                                //echo "2:".$cmd."<br><br>";
//				$db_dpis->show_error();
				while($data = $db_dpis->get_array()){
					$CP_CODE = $data[CP_CODE];
					$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
				
					$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and trim(CP_CODE)='$CP_CODE' ";
                                        //echo "3:".$cmd."<br><br>";
					$count_duplicate = $db_dpis2->send_cmd($cmd);
					if($count_duplicate <= 0){
						$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
										UPDATE_DATE) 
										values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
						
                                                //echo "4:".$cmd."<br><br>";
                                                $db_dpis2->send_cmd($cmd);
//						$db_dpis2->show_error();
						$KC_ID++;
					}
				} // end while
			} // endif($PER_TYPE==1 || $PER_TYPE==3) {
			// ========================== insert competence from kpi_position_competence  ========================== //
		}else{
			$KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
			
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_CODE = $data[PN_CODE];
			$TMP_PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
			
			$PER_FULLNAME = $PN_NAME . $TMP_PER_NAME . " " . $PER_SURNAME;

			//$err_text = "รหัสข้อมูลซ้ำ [ปีงบประมาณ ".$KF_YEAR." ครั้งที่ ".$KF_CYCLE." ".$PER_FULLNAME."]";/*เดิม*/
                        $err_text = "ตรวจพบข้อมูลการประเมินมีอยู่แล้ว [ปีงบประมาณ ".$KF_YEAR." ครั้งที่ ".$KF_CYCLE." ".$PER_FULLNAME."] หากต้องการทำการประเมินรอบดังกล่าว กรุณาทำรายการค้นหา และทำการแก้ไขข้อมูล";/*Release 5.1.0.5*/
			$KF_START_DATE_1 = "01/10/". ($KF_YEAR - 1);
			$KF_END_DATE_1 = "31/03/". $KF_YEAR;
			$KF_START_DATE_2 = "01/04/". $KF_YEAR;
			$KF_END_DATE_2 = "30/09/". $KF_YEAR;
		} // endif.
               
	}

//	echo "command=$command , KF_ID=$KF_ID , KF_YEAR=$KF_YEAR , KF_CYCLE=$KF_CYCLE , PER_ID=$PER_ID , UPD=$UPD<br>";
	if($command == "UPDATE" && trim($KF_ID)  && trim($KF_YEAR) && trim($KF_CYCLE) && trim($PER_ID)){
            //echo ">>>".$LEVEL_NO;
		if($DPISDB == "odbc"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	LEFT(KF_END_DATE, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID and KF_ID <> $KF_ID ";
		}elseif($DPISDB == "oci8"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	SUBSTR(KF_END_DATE, 1, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID and KF_ID <> $KF_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
					  		 from 		PER_KPI_FORM 
							 where 	LEFT(KF_END_DATE, 10) = '$KF_END_DATE' and  KF_CYCLE=$KF_CYCLE and PER_ID = $PER_ID and KF_ID <> $KF_ID ";
		} // end if
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if(!$count_duplicate){
			if(!$PER_ID_REVIEW) $PER_ID_REVIEW = "NULL";
			if(!$PER_ID_REVIEW0) $PER_ID_REVIEW0 = "NULL";
			if(!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
			if(!$PER_ID_REVIEW2) $PER_ID_REVIEW2 = "NULL";
			if(!$OTHER_WEIGHT) $OTHER_WEIGHT = "NULL";
			if(!$PER_SALARY) $PER_SALARY = "NULL";
			$cmd = " update PER_KPI_FORM set 
                                KF_CYCLE=$KF_CYCLE,
                                KF_START_DATE='$KF_START_DATE', 
                                KF_END_DATE='$KF_END_DATE', 
                                PER_ID=$PER_ID, 
                                PER_CARDNO='$PER_CARDNO', 
                                LEVEL_NO = '$txtLEVEL_NO',
                                PER_ID_REVIEW=$PER_ID_REVIEW,
                                PER_ID_REVIEW0=$PER_ID_REVIEW0,
                                PER_ID_REVIEW1=$PER_ID_REVIEW1,
                                PER_ID_REVIEW2=$PER_ID_REVIEW2,
                                PER_SALARY=$PER_SALARY, 
                                PL_NAME='$PL_NAME', 
                                PM_NAME='$PM_NAME', 
                                REVIEW_PN_NAME='$REVIEW_PN_NAME', 
                                REVIEW_PER_NAME='$REVIEW_PER_NAME', 
                                REVIEW_PL_NAME='$REVIEW_PL_NAME', 
                                REVIEW_PM_NAME='$REVIEW_PM_NAME', 
                                REVIEW_LEVEL_NO='$REVIEW_LEVEL_NO', 
                                REVIEW_PN_NAME0='$REVIEW_PN_NAME0', 
                                REVIEW_PER_NAME0='$REVIEW_PER_NAME0', 
                                REVIEW_PL_NAME0='$REVIEW_PL_NAME0', 
                                REVIEW_PM_NAME0='$REVIEW_PM_NAME0', 
                                REVIEW_LEVEL_NO0='$REVIEW_LEVEL_NO0', 
                                REVIEW_PN_NAME1='$REVIEW_PN_NAME1', 
                                REVIEW_PER_NAME1='$REVIEW_PER_NAME1', 
                                REVIEW_PL_NAME1='$REVIEW_PL_NAME1', 
                                REVIEW_PM_NAME1='$REVIEW_PM_NAME1', 
                                REVIEW_LEVEL_NO1='$REVIEW_LEVEL_NO1', 
                                REVIEW_PN_NAME2='$REVIEW_PN_NAME2', 
                                REVIEW_PER_NAME2='$REVIEW_PER_NAME2', 
                                REVIEW_PL_NAME2='$REVIEW_PL_NAME2', 
                                REVIEW_PM_NAME2='$REVIEW_PM_NAME2', 
                                REVIEW_LEVEL_NO2='$REVIEW_LEVEL_NO2', 
                                UPDATE_USER=$SESS_USERID, 
                                UPDATE_DATE='$UPDATE_DATE' ,
                                ORG_ID = '$hidden_org_id'     
                         where KF_ID=$KF_ID ";
                        //echo "<br><pre>".$cmd;
			$db_dpis->send_cmd($cmd); 
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_NAME."]");
		}else{
			$KF_YEAR = substr($KF_END_DATE, 0, 4) + 543;
			
			$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_CODE = $data[PN_CODE];
			$TMP_PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PN_NAME = $data[PN_NAME];
			
			$PER_FULLNAME = $PN_NAME . $TMP_PER_NAME . " " . $PER_SURNAME;

			$err_text = "รหัสข้อมูลซ้ำ [".$KF_YEAR." ".$KF_CYCLE." ".$PER_FULLNAME."]";
			
			$KF_START_DATE_1 = "01/10/". ($KF_YEAR - 1);
			$KF_END_DATE_1 = "31/03/". $KF_YEAR;
			$KF_START_DATE_2 = "01/04/". $KF_YEAR;
			$KF_END_DATE_2 = "30/09/". $KF_YEAR;			
		} // end if
	}
	
	if($command == "DELETE" && trim($KF_ID)){
		$cmd = " select 	KF_END_DATE, KF_CYCLE, PER_ID
				  		 from 		PER_KPI_FORM 
						 where 	KF_ID = $KF_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_END_DATE = substr($data[KF_END_DATE], 0, 10);
		//$KF_YEAR = substr($KF_END_DATE, 0, 4); /*เดิม*/
                /* Release 5.1.0.5 Begin*/
                $KF_YEAR = substr($KF_END_DATE, 0, 4)+543;
                /*Release 5.1.0.5 End*/
		$KF_CYCLE = $data[KF_CYCLE];
		$PER_ID = $data[PER_ID];
			
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		$TMP_PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
			
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
			
		$PER_FULLNAME = $PN_NAME . $TMP_PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $KF_ID ." : ".$KF_YEAR." ".$KF_CYCLE." ".$PER_FULLNAME."]");
	}
	//กำหนดค่าเริ่มต้นเมื่อเข้ามาเป็นคนนั้น จาก person_master_frame
	//if($_REQUEST[PER_ID]){ $PER_ID=$_REQUEST[PER_ID]; if(!trim($UPD)) $VIEW=1;  }
	//echo "sperid : ".$SESS_PER_ID." perid : ".$PER_ID;
//	echo "command=$command , KF_ID=$KF_ID , KF_YEAR=$KF_YEAR , KF_CYCLE=$KF_CYCLE , PER_ID=$PER_ID , UPD=$UPD , VIEW=$VIEW<br>";
        
	if($UPD || $VIEW){//ดู/แก้ไข
            
		$cmd = " select KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID, PER_CARDNO, PER_ID_REVIEW, PER_ID_REVIEW0, 
                                PER_ID_REVIEW1, PER_ID_REVIEW2, LEVEL_NO, PER_SALARY, PL_NAME, PM_NAME, REVIEW_PN_NAME, REVIEW_PER_NAME, 
                                REVIEW_PL_NAME, REVIEW_PM_NAME, REVIEW_LEVEL_NO, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
                                REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, REVIEW_PN_NAME1, REVIEW_PER_NAME1, REVIEW_PL_NAME1, REVIEW_PM_NAME1, 
                                REVIEW_LEVEL_NO1, REVIEW_PN_NAME2, REVIEW_PER_NAME2, REVIEW_PL_NAME2, REVIEW_PM_NAME2, REVIEW_LEVEL_NO2,
                                ORG_ID
                        from PER_KPI_FORM
                        where KF_ID=$KF_ID ";
                if($debug==1){echo '<pre>'.$cmd;}
		$count = $db_dpis->send_cmd($cmd);
//echo $cmd;
		if($count){
                   
			$data = $db_dpis->get_array();
			$KF_CYCLE = trim($data[KF_CYCLE]);
			if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
				$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
				$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
				$KF_YEAR = substr($KF_END_DATE_1, 6, 4);
			}else if($KF_CYCLE==2){
				$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
				$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
				$KF_YEAR = substr($KF_END_DATE_2, 6, 4);
			}

			if ($command=="LOOP2" && !$PER_ID && $data[PER_ID]) $PER_ID = $data[PER_ID];
			else if ($command!="LOOP2") $PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);		
			if ($command=="LOOP2" && !$PER_ID_REVIEW && $data[PER_ID_REVIEW]) $PER_ID_REVIEW = $data[PER_ID_REVIEW];
			else if ($command!="LOOP2") $PER_ID_REVIEW = $data[PER_ID_REVIEW];
                        
			//echo "command=$command , PER_ID_REVIEW0=$PER_ID_REVIEW0 , data[PER_ID_REVIEW0]=".$data[PER_ID_REVIEW0]." (".!$data[PER_ID_REVIEW0].")<br>";
                        
			if ($command=="LOOP2" && !$PER_ID_REVIEW0 && $data[PER_ID_REVIEW0]) $PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
			else if ($command!="LOOP2") $PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
			if ($command=="LOOP2" && !$PER_ID_REVIEW1 && $data[PER_ID_REVIEW1]) $PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
			else if ($command!="LOOP2") $PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
			if ($command!="LOOP2" && !$PER_ID_REVIEW2 && $data[PER_ID_REVIEW2]) $PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
			else if ($command!="LOOP2") $PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];

			$dbPER_SALARY = $data[PER_SALARY];//$PER_SALARY = $data[PER_SALARY];
			if ($command=="LOOP2" && !$PL_NAME && $data[PL_NAME]) $PL_NAME = trim($data[PL_NAME]);
			else if ($command!="LOOP2") $PL_NAME = trim($data[PL_NAME]);
			if ($command=="LOOP2" && !$PM_NAME && $data[PM_NAME]) $PM_NAME = trim($data[PM_NAME]);
			else if ($command!="LOOP2") $PM_NAME = trim($data[PM_NAME]);
			if ($command=="LOOP2" && !$LEVEL_NO && $data[LEVEL_NO]) $LEVEL_NO = trim($data[LEVEL_NO]);
			else if ($command!="LOOP2") $LEVEL_NO = trim($data[LEVEL_NO]);
			$REVIEW_PN_NAME = trim($data[REVIEW_PN_NAME]);
			$REVIEW_PER_NAME = trim($data[REVIEW_PER_NAME]);
			if ($command=="LOOP2" && !$REVIEW_PL_NAME && $data[REVIEW_PL_NAME]) $REVIEW_PL_NAME = trim($data[REVIEW_PL_NAME]);
			else if ($command!="LOOP2") $REVIEW_PL_NAME = trim($data[REVIEW_PL_NAME]);
			if ($command=="LOOP2" && !$REVIEW_PM_NAME && $data[REVIEW_PM_NAME]) $REVIEW_PM_NAME = trim($data[REVIEW_PM_NAME]);
			else if ($command!="LOOP2") $REVIEW_PM_NAME = trim($data[REVIEW_PM_NAME]);
			if ($command!="LOOP2" && !$REVIEW_LEVEL_NO && $data[REVIEW_LEVEL_NO]) $REVIEW_LEVEL_NO = trim($data[REVIEW_LEVEL_NO]);
			else if ($command!="LOOP2") $REVIEW_LEVEL_NO = trim($data[REVIEW_LEVEL_NO]);
			if ($command=="LOOP2" && !$REVIEW_FULL_NAME && trim($REVIEW_PN_NAME.$REVIEW_PER_NAME)) 
				$REVIEW_FULL_NAME = $REVIEW_PN_NAME.$REVIEW_PER_NAME;
			else if ($command!="LOOP2") 
				$REVIEW_FULL_NAME = $REVIEW_PN_NAME.$REVIEW_PER_NAME;
			$REVIEW_PN_NAME0 = trim($data[REVIEW_PN_NAME0]);
			$REVIEW_PER_NAME0 = trim($data[REVIEW_PER_NAME0]);
			if ($command=="LOOP2" && !$REVIEW_PL_NAME0 && $data[REVIEW_PL_NAME0]) $REVIEW_PL_NAME0 = trim($data[REVIEW_PL_NAME0]);
			else if ($command!="LOOP2") $REVIEW_PL_NAME0 = trim($data[REVIEW_PL_NAME0]);
			if ($command=="LOOP2" && !$REVIEW_PM_NAME0 && $data[REVIEW_PM_NAME0]) $REVIEW_PM_NAME0 = trim($data[REVIEW_PM_NAME0]);
			else if ($command!="LOOP2") $REVIEW_PM_NAME0 = trim($data[REVIEW_PM_NAME0]);
			if ($command=="LOOP2" && !$REVIEW_LEVEL_NO0 && $data[REVIEW_LEVEL_NO0]) $REVIEW_LEVEL_NO0 = trim($data[REVIEW_LEVEL_NO0]);
			else if ($command!="LOOP2") $REVIEW_LEVEL_NO0 = trim($data[REVIEW_LEVEL_NO0]);
                        if ($command=="LOOP2" && !$REVIEW_FULL_NAME0 && trim($REVIEW_PN_NAME0.$REVIEW_PER_NAME0))
				$REVIEW_FULL_NAME0 = $REVIEW_PN_NAME0.$REVIEW_PER_NAME0;
			else if ($command!="LOOP2")
				$REVIEW_FULL_NAME0 = $REVIEW_PN_NAME0.$REVIEW_PER_NAME0;
                        
			$REVIEW_PN_NAME1 = trim($data[REVIEW_PN_NAME1]);
			$REVIEW_PER_NAME1 = trim($data[REVIEW_PER_NAME1]);
			if ($command=="LOOP2" && !$REVIEW_PL_NAME1 && $data[REVIEW_PL_NAME1]) $REVIEW_PL_NAME1 = trim($data[REVIEW_PL_NAME1]);
			else if ($command!="LOOP2") $REVIEW_PL_NAME1 = trim($data[REVIEW_PL_NAME1]);
			if ($command=="LOOP2" && !$REVIEW_PM_NAME1 && $data[REVIEW_PM_NAME1]) $REVIEW_PM_NAME1 = trim($data[REVIEW_PM_NAME1]);
			else if ($command!="LOOP2") $REVIEW_PM_NAME1 = trim($data[REVIEW_PM_NAME1]);
			if ($command=="LOOP2" && !$REVIEW_LEVEL_NO1 && $data[REVIEW_LEVEL_NO1]) $REVIEW_LEVEL_NO1 = trim($data[REVIEW_LEVEL_NO1]);
			else if ($command!="LOOP2") $REVIEW_LEVEL_NO1 = trim($data[REVIEW_LEVEL_NO1]);
			if ($command=="LOOP2" && !$REVIEW_FULL_NAME1 && trim($REVIEW_PN_NAME1.$REVIEW_PER_NAME1))
				$REVIEW_FULL_NAME1 = $REVIEW_PN_NAME1.$REVIEW_PER_NAME1;
			else if ($command!="LOOP2")
				$REVIEW_FULL_NAME1 = $REVIEW_PN_NAME1.$REVIEW_PER_NAME1;
			$REVIEW_PN_NAME2 = trim($data[REVIEW_PN_NAME2]);
			$REVIEW_PER_NAME2 = trim($data[REVIEW_PER_NAME2]);
			if ($command=="LOOP2" && !$REVIEW_PL_NAME2 && $data[REVIEW_PL_NAME2]) $REVIEW_PL_NAME2 = trim($data[REVIEW_PL_NAME2]);
			else if ($command!="LOOP2") $REVIEW_PL_NAME2 = trim($data[REVIEW_PL_NAME2]);
			if ($command=="LOOP2" && !$REVIEW_PM_NAME2 && $data[REVIEW_PM_NAME2]) $REVIEW_PM_NAME2 = trim($data[REVIEW_PM_NAME2]);
			else if ($command!="LOOP2") $REVIEW_PM_NAME2 = trim($data[REVIEW_PM_NAME2]);
			if ($command=="LOOP2" && !$REVIEW_LEVEL_NO2 && $data[REVIEW_LEVEL_NO2]) $REVIEW_LEVEL_NO2 = trim($data[REVIEW_LEVEL_NO2]);
			else if ($command!="LOOP2") $REVIEW_LEVEL_NO2 = trim($data[REVIEW_LEVEL_NO2]);
			if ($command=="LOOP2" && !$REVIEW_FULL_NAME2 && trim($REVIEW_PN_NAME2.$REVIEW_PER_NAME2))
				$REVIEW_FULL_NAME2 = $REVIEW_PN_NAME2.$REVIEW_PER_NAME2;
			else if ($command!="LOOP2" && !$REVIEW_FULL_NAME2 && trim($REVIEW_PN_NAME2.$REVIEW_PER_NAME2))
				$REVIEW_FULL_NAME2 = $REVIEW_PN_NAME2.$REVIEW_PER_NAME2;
		}else{ //เข้ามา รายละเอียดข้าราชการ/ลูกจ้าง (KPI รายบุคคล)**ไม่มี KF_ID ระบุ จะกำหนดค่าเริ่มต้นของปีงบประมาณ / รอบการประเมิน
			if (!$KF_YEAR) $KF_YEAR = $KPI_BUDGET_YEAR;
			if(!$KF_YEAR){
				if(date("Y-m-d") <= date("Y")."-10-01") $KF_YEAR = date("Y") + 543;
				else $KF_YEAR = (date("Y") + 543) + 1;
			}
			$KF_START_DATE_1 = "01/10/". ($KF_YEAR - 1);
			$KF_END_DATE_1 = "31/03/". $KF_YEAR;
			$KF_START_DATE_2 = "01/04/". $KF_YEAR;
			$KF_END_DATE_2 = "30/09/". $KF_YEAR;
		}
                $view_ORG_ID = $data[ORG_ID];
		//echo $KF_CYCLE." $KF_YEAR : (".$KF_START_DATE_1."-".$KF_END_DATE_1.") + (".$KF_START_DATE_2."-".$KF_END_DATE_2.") ";

		// ถ้าไม่มี PER_ID จาก PER_KPI_FORM เอามาจาก GET / POST
		$cmd = " select 	PN_CODE, PER_CARDNO, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
		//echo '<pre>'.$cmd;
                $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		if(!trim($PER_CARDNO)) $PER_CARDNO = trim($data[PER_CARDNO]);
		$TMP_PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		if (!$LEVEL_NO) $LEVEL_NO = trim($data[LEVEL_NO]);
		if (!$dbPER_SALARY) $dbPER_SALARY = trim($data[PER_SALARY]);//if (!$PER_SALARY) $PER_SALARY = trim($data[PER_SALARY]);
		$PER_TYPE = trim($data[PER_TYPE]);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = trim($data[PN_NAME]);
		
		$PER_NAME = $PN_NAME . $TMP_PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if ($POSITION_LEVEL) $LEVEL_NAME = $POSITION_LEVEL;
		if (!$PL_NAME || !$ORG_NAME) {
			if($PER_TYPE==1){
				$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE 
								 from 		PER_POSITION a, PER_LINE b, PER_ORG c
								 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$PL_NAME) $PL_NAME = trim($data[PL_NAME]);
				if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
				$PT_CODE = trim($data[PT_CODE]);
				$PT_NAME = trim($data[PT_NAME]);
			}elseif($PER_TYPE==2){
				$cmd = " select 	b.PN_NAME, c.ORG_NAME 
								 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
								 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$PL_NAME) $PL_NAME = trim($data[PN_NAME]);
				if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
			}elseif($PER_TYPE==3){
				$cmd = " select 	b.EP_NAME, c.ORG_NAME 
								 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
								 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				if (!$PL_NAME) $PL_NAME = trim($data[EP_NAME]);
				if (!$ORG_NAME) $ORG_NAME = trim($data[ORG_NAME]);
			} // end if
		}
                /**/
                if($UPD==2 || $VIEW==2 ){
                    $view_ORG_NAME = "";
                    $hidden_org_id = '';
                    if ($view_ORG_ID) {
                            $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $view_ORG_ID ";
                            if($SESS_ORG_STRUCTURE==1) {
                                    $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                            }
                            $db_dpis2->send_cmd($cmd);
                            $data2 = $db_dpis2->get_array();
                            $view_ORG_NAME = trim($data2[ORG_NAME]);
                            if ($view_ORG_NAME=="-") $view_ORG_NAME = "";
                    }
                    $ORG_NAME = $view_ORG_NAME;
                    $hidden_org_id = $view_ORG_ID;
                }
                
                
                /**/
                

                //if (!$REVIEW_PER_NAME || !$REVIEW_PL_NAME || !$REVIEW_LEVEL_NO) {  // pitak
		  if($PER_ID_REVIEW){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW ";
                       // echo $cmd;
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE = trim($data[PN_CODE]);
			$REVIEW_PER_NAME = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE = trim($data[PER_TYPE]);
			$REVIEW_POS_ID = trim($data[POS_ID]);
			$REVIEW_POEM_ID = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO) $REVIEW_LEVEL_NO = trim($data[LEVEL_NO]);

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME = trim($data[PN_NAME]);
			
			$REVIEW_FULL_NAME = $REVIEW_PN_NAME . $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
			$REVIEW_PER_NAME = $REVIEW_PER_NAME . " " . $REVIEW_PER_SURNAME;
			
			if (!$REVIEW_PL_NAME) {
				if($REVIEW_PER_TYPE==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PL_NAME]);
					$REVIEW_PT_CODE = trim($data[PT_CODE]);
					$REVIEW_PT_NAME = trim($data[PT_NAME]);
					$REVIEW_PM_CODE = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE && !$REVIEW_PM_NAME){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$REVIEW_PM_NAME = $data[PM_NAME]; 
					} // end if
				}elseif($REVIEW_PER_TYPE==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME) $REVIEW_PL_NAME = trim($data[EP_NAME]);
				} // end if
			} // end if
		  }
		//} // pitak

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if ($REVIEW_POSITION_LEVEL) $REVIEW_LEVEL_NAME = $REVIEW_POSITION_LEVEL;

//		echo "REVIEW_PER_NAME0=$REVIEW_PER_NAME0 || REVIEW_PL_NAME0=$REVIEW_PL_NAME0 || REVIEW_LEVEL_NO0=$REVIEW_LEVEL_NO0<br>";
		//if (!$REVIEW_PER_NAME0 || !$REVIEW_PL_NAME0 || !$REVIEW_LEVEL_NO0) { //pitak
		  if($PER_ID_REVIEW0){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW0 ";
			$db_dpis->send_cmd($cmd);
                        //echo $cmd;
	//		$db_dpis->show_error();			
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE0 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME0 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME0 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE0 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID0 = trim($data[POS_ID]);
			$REVIEW_POEM_ID0 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID0 = trim($data[POEMS_ID]);
                        //echo $REVIEW_LEVEL_NO0.'<br>';
			//if (!$REVIEW_LEVEL_NO0) $REVIEW_LEVEL_NO0 = trim($data[LEVEL_NO]); /*เดิม*/
                        if ($data[LEVEL_NO]) $REVIEW_LEVEL_NO0 =  trim($data[LEVEL_NO]);/*Release 5.2.1.6*/
                        //echo $REVIEW_LEVEL_NO0.'<br>';

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE0' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();		
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME0 = trim($data[PN_NAME]);
			
			$REVIEW_FULL_NAME0 = $REVIEW_PN_NAME0 . $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;
			$REVIEW_PER_NAME0 = $REVIEW_PER_NAME0 . " " . $REVIEW_PER_SURNAME0;

			
			if (!$REVIEW_PL_NAME0) {
				if($REVIEW_PER_TYPE0==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID0 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE0 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME0 = trim($data[PT_NAME]);
					$REVIEW_PM_CODE0 = trim($data[PM_CODE]);
//					echo "REVIEW_PM_CODE0=$REVIEW_PM_CODE0 && REVIEW_PM_NAME0=$REVIEW_PM_NAME0<br>";
					if($REVIEW_PM_CODE0 && !$REVIEW_PM_NAME0){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE0' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$REVIEW_PM_NAME0 = $data[PM_NAME]; 
					} // end if
				}elseif($REVIEW_PER_TYPE0==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID0 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE0==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID0 and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME0) $REVIEW_PL_NAME0 = trim($data[EP_NAME]);
				} // end if
			} // end if
		  }
		//} //

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO0' ";
                //echo $cmd;
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME0 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL0 = $data2[POSITION_LEVEL];
		if ($REVIEW_POSITION_LEVEL0) $REVIEW_LEVEL_NAME0 = $REVIEW_POSITION_LEVEL0;
			
		//if (!$REVIEW_PER_NAME1 || !$REVIEW_PL_NAME1 || !$REVIEW_LEVEL_NO1) { //pitak
		  if($PER_ID_REVIEW1){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW1 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();	
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE1 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME1 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME1 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE1 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID1 = trim($data[POS_ID]);
			$REVIEW_POEM_ID1 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID1 = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO1) $REVIEW_LEVEL_NO1 = trim($data[LEVEL_NO]);

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE1' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();		
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME1 = trim($data[PN_NAME]);
			
			$REVIEW_FULL_NAME1 = $REVIEW_PN_NAME1 . $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
			$REVIEW_PER_NAME1 = $REVIEW_PER_NAME1 . " " . $REVIEW_PER_SURNAME1;
			
			if (!$REVIEW_PL_NAME1) {
				if($REVIEW_PER_TYPE1==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID1 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE1 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME1 = trim($data[PT_NAME]);
					$REVIEW_PM_CODE1 = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE1 && !$REVIEW_PM_NAME1){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE1' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$REVIEW_PM_NAME1 = $data[PM_NAME];
					} // end if
				}elseif($REVIEW_PER_TYPE1==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID1 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();				
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE1==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID1 and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME1) $REVIEW_PL_NAME1 = trim($data[EP_NAME]);
				} // end if
			} // end if
		  }
		//} //pitak

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO1' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();		
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME1 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL1 = $data2[POSITION_LEVEL];
		if ($REVIEW_POSITION_LEVEL1) $REVIEW_LEVEL_NAME1 = $REVIEW_POSITION_LEVEL1;
			
		//if (!$REVIEW_PER_NAME2 || !$REVIEW_PL_NAME2 || !$REVIEW_LEVEL_NO2) { //pitak
		  if($PER_ID_REVIEW2){
			$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO
							 from		PER_PERSONAL
							 where	PER_ID=$PER_ID_REVIEW2 ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();		
			$data = $db_dpis->get_array();
			$REVIEW_PN_CODE2 = trim($data[PN_CODE]);
			$REVIEW_PER_NAME2 = trim($data[PER_NAME]);
			$REVIEW_PER_SURNAME2 = trim($data[PER_SURNAME]);
			$REVIEW_PER_TYPE2 = trim($data[PER_TYPE]);
			$REVIEW_POS_ID2 = trim($data[POS_ID]);
			$REVIEW_POEM_ID2 = trim($data[POEM_ID]);
			$REVIEW_POEMS_ID2 = trim($data[POEMS_ID]);
			if (!$REVIEW_LEVEL_NO2) $REVIEW_LEVEL_NO2 = trim($data[LEVEL_NO]);

			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$REVIEW_PN_CODE2' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();		
			$data = $db_dpis->get_array();
			$REVIEW_PN_NAME2 = trim($data[PN_NAME]);
			
			$REVIEW_FULL_NAME2 = $REVIEW_PN_NAME2 . $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
			$REVIEW_PER_NAME2 = $REVIEW_PER_NAME2 . " " . $REVIEW_PER_SURNAME2;
			
			if (!$REVIEW_PL_NAME2) {
				if($REVIEW_PER_TYPE2==1){
					$cmd = " select 	b.PL_NAME, a.PT_CODE, a.PM_CODE 
									 from 		PER_POSITION a, PER_LINE b
									 where	a.POS_ID=$REVIEW_POS_ID2 and a.PL_CODE=b.PL_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();	
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PL_NAME]);
					$REVIEW_PT_CODE2 = trim($data[PT_CODE]);
					$REVIEW_PT_NAME2 = trim($data[PT_NAME]);
					$REVIEW_PM_CODE2 = trim($data[PM_CODE]);
					if($REVIEW_PM_CODE2 && !$REVIEW_PM_NAME2){
						$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$REVIEW_PM_CODE2' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$REVIEW_PM_NAME2 = $data[PM_NAME];
					} // end if 
				}elseif($REVIEW_PER_TYPE2==2){
					$cmd = " select 	b.PN_NAME
									 from 		PER_POS_EMP a, PER_POS_NAME b
									 where	a.POEM_ID=$REVIEW_POEM_ID2 and a.PN_CODE=b.PN_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();				
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[PN_NAME]);
				}elseif($REVIEW_PER_TYPE2==3){
					$cmd = " select 	b.EP_NAME
									 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
									 where	a.POEMS_ID=$REVIEW_POEMS_ID2 and a.EP_CODE=b.EP_CODE ";
					$db_dpis->send_cmd($cmd);
		//			$db_dpis->show_error();				
					$data = $db_dpis->get_array();
					if (!$REVIEW_PL_NAME2) $REVIEW_PL_NAME2 = trim($data[EP_NAME]);
				} // end if
			} // end if
		  }
		//} //pitak

		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$REVIEW_LEVEL_NO2' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();	
		$data2 = $db_dpis2->get_array();
		$REVIEW_LEVEL_NAME2 = $data2[LEVEL_NAME];
		$REVIEW_POSITION_LEVEL2 = $data2[POSITION_LEVEL];
		if ($REVIEW_POSITION_LEVEL2) $REVIEW_LEVEL_NAME2 = $REVIEW_POSITION_LEVEL2;
			
		$KF_START_DATE_1 = "01/10/". ($KF_YEAR - 1);
		$KF_END_DATE_1 = "31/03/". $KF_YEAR;
		$KF_START_DATE_2 = "01/04/". $KF_YEAR;
		$KF_END_DATE_2 = "30/09/". $KF_YEAR;
	} // end if
	
	if($command == "GENDATA" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($search_per_type)){
		if (($CTRL_TYPE==2 || $CTRL_TYPE==3) && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_COMPETENCE where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_IPIP where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_FORM where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and 
						KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select max(KF_ID) as max_id from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KF_ID = $data[max_id] + 1;
			
		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
	
		$cmd = " select		PER_ID, PER_CARDNO, PER_ID_REF, PER_ID_ASS_REF, PER_TYPE, 
											POS_ID, POEM_ID, POEMS_ID, PAY_ID, ORG_ID, ORG_ID_1, LEVEL_NO, DEPARTMENT_ID, OT_CODE, LEVEL_NO, PER_PROBATION_FLAG, PER_SALARY
						 from			PER_PERSONAL
						 where		PER_TYPE = $search_per_type and PER_STATUS=1 $where 
						 order by		PER_ID ";		
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
//		echo "$cmd<br>";
                
                
                
                
		while($data1 = $db_dpis1->get_array()){
			$PER_ID = $data1[PER_ID];
			$PER_CARDNO = $data1[PER_CARDNO];
			$PER_ID_REF = $data1[PER_ID_REF];
			$PER_ID_ASS_REF = $data1[PER_ID_ASS_REF];
			$FRIEND_FLAG = "N";
			if ($PER_ID_REF) $FRIEND_FLAG = "Y";
			if (!$PER_ID_REF) $PER_ID_REF = "NULL";
			$PER_TYPE = $data1[PER_TYPE];
			if($PER_TYPE == 1) $POS_ID = $data1[POS_ID];
			elseif($PER_TYPE == 2) $POS_ID = $data1[POEM_ID];
			elseif($PER_TYPE == 3) $POS_ID = $data1[POEMS_ID];
			$PAY_ID = $data1[PAY_ID];
			$ORG_ID_ASS = $data1[ORG_ID];
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			$ORG_ID_1_ASS = $data1[ORG_ID_1];
			if (!$ORG_ID_1_ASS) $ORG_ID_1_ASS = "NULL";
			$DEPARTMENT_ID = $data1[DEPARTMENT_ID];
			$OT_CODE = trim($data1[OT_CODE]);
			$PER_PROBATION_FLAG = trim($data1[PER_PROBATION_FLAG]);
			$PER_SALARY = $data1[PER_SALARY];
			if ($PER_PROBATION_FLAG==1){
				$PERFORMANCE_WEIGHT = $WEIGHT_PROBATION;
				$COMPETENCE_WEIGHT = $WEIGHT_PROBATION;
				$OTHER_WEIGHT = "NULL";
			}elseif ($OT_CODE=="08"){
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI_E;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_E;
				$OTHER_WEIGHT = "NULL";
			}elseif ($OT_CODE=="09"){
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI_S;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE_S;
				$OTHER_WEIGHT = "NULL";
			}else{
				$PERFORMANCE_WEIGHT = $WEIGHT_KPI;
				$COMPETENCE_WEIGHT = $WEIGHT_COMPETENCE;
				$OTHER_WEIGHT = $WEIGHT_OTHER;
			}
			$LEVEL_NO = trim($data1[LEVEL_NO]);

			if($PER_TYPE==1) 
				if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง")
					$cmd = " select 	ORG_ID, ORG_ID_1, PL_NAME, PM_CODE from PER_POSITION a, PER_LINE b 
									where POS_ID = $PAY_ID and a.PL_CODE=b.PL_CODE ";		
				else
					$cmd = " select	 ORG_ID, ORG_ID_1, PL_NAME, PM_CODE from PER_POSITION a, PER_LINE b 
									where POS_ID = $POS_ID and a.PL_CODE=b.PL_CODE ";		
			elseif($PER_TYPE==2) 
				$cmd = " select	 ORG_ID, ORG_ID_1, PN_NAME as PL_NAME from PER_POS_EMP a, PER_POS_NAME b 
								where POEM_ID = $POS_ID and a.PN_CODE=b.PN_CODE ";		
			elseif($PER_TYPE==3) 
				$cmd = " select	 ORG_ID, ORG_ID_1, EP_NAME as PL_NAME from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
								where POEMS_ID = $POS_ID and a.EP_CODE=b.EP_CODE ";		
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_ID = $data[ORG_ID];
			$ORG_ID_1 = $data[ORG_ID_1];
			if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
			$PL_NAME = trim($data[PL_NAME]);
			$PM_CODE = trim($data[PM_CODE]);
			if ($PM_CODE) {
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PM_NAME = trim($data[PM_NAME]);
			}
                        
                        /*แก้ปัญหา ข้อมูลขยะที่ทำให้เกิดข้อมูลซ้ำ*/
                        $cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);

                        $cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
                        $db_dpis->send_cmd($cmd);
                        /*++++++++++++++++++++++++++++++++++++++++++*/
                        
			
			$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
							PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
							UPDATE_DATE, ORG_ID, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, FRIEND_FLAG, 
							ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, 
							ORG_ID_1_ASS, LEVEL_NO, PER_SALARY, PL_NAME, PM_NAME) 
							values ($KF_ID, $PER_ID, '$PER_CARDNO', $KF_CYCLE, '$KF_START_DATE', '$KF_END_DATE', 	$PER_ID_REF, 
							$PER_ID_REF, $PER_ID_REF, $PER_ID_REF, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $ORG_ID, 'Y', 
							$ORG_ID, 'Y', $ORG_ID, $PER_ID_REF, '$FRIEND_FLAG', $ORG_ID_1, $ORG_ID_ASS, $PERFORMANCE_WEIGHT, 
							$COMPETENCE_WEIGHT, $OTHER_WEIGHT, $ORG_ID_1_ASS, '$LEVEL_NO', $PER_SALARY, '$PL_NAME', '$PM_NAME') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$KF_ID." : ".trim($KF_YEAR)." : ".$KF_CYCLE." : ".$PER_CARDNO."]");

			if ($BKK_FLAG==1) {
				$cmd = " select max(PG_ID) as max_id from PER_PERFORMANCE_GOALS ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PG_ID = $data[max_id] + 1;
	
				for ( $PG_SEQ=1; $PG_SEQ<=3; $PG_SEQ++ ) { 
					if ($PG_SEQ==1) $KPI_NAME = "ตัวชี้วัดตามแผนปฏิบัติราชการประจำปี";		
					elseif ($PG_SEQ==2) $KPI_NAME = "ตัวชี้วัดตามหน้าที่ความรับผิดชอบหลัก";		
					elseif ($PG_SEQ==3) $KPI_NAME = "ตัวชี้วัดตามงานที่ได้รับมอบหมายพิเศษ";		
					$KPI_PER_ID = $PER_ID;
					$KF_TYPE = $PG_SEQ;		
					$KPI_ID = $KPI_WEIGHT = $KPI_TARGET_LEVEL1 = $KPI_TARGET_LEVEL2 = $KPI_TARGET_LEVEL3 = $KPI_TARGET_LEVEL4 = $KPI_TARGET_LEVEL5 = "NULL";
					
					$cmd = " 	insert into PER_PERFORMANCE_GOALS
										(KF_ID, PG_ID, PG_SEQ, KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, KF_TYPE,
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
										KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC,
										UPDATE_USER, UPDATE_DATE)
										values
										($KF_ID, $PG_ID, $PG_SEQ, $KPI_ID, '$KPI_NAME', $KPI_WEIGHT, '$KPI_MEASURE', $KPI_PER_ID, '$KF_TYPE',
										$KPI_TARGET_LEVEL1, $KPI_TARGET_LEVEL2, $KPI_TARGET_LEVEL3, $KPI_TARGET_LEVEL4, $KPI_TARGET_LEVEL5,
										'$KPI_TARGET_LEVEL1_DESC', '$KPI_TARGET_LEVEL2_DESC', '$KPI_TARGET_LEVEL3_DESC', '$KPI_TARGET_LEVEL4_DESC', '$KPI_TARGET_LEVEL5_DESC',
										$SESS_USERID, '$UPDATE_DATE')   ";
					$db_dpis2->send_cmd($cmd);
		//				$db_dpis2->show_error();
					$PG_ID++;
				}
			}

			// ==================== insert competence from kpi_position_competence  ==================== //
			$cmd = " select	a.CP_CODE, PC_TARGET_LEVEL
							 from		PER_POSITION_COMPETENCE a, PER_COMPETENCE b
							 where	a.CP_CODE=b.CP_CODE and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID = $DEPARTMENT_ID and 
											POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and CP_ASSESSMENT = 'Y' and a.PER_TYPE = $search_per_type
							 order by a.CP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "$cmd<br>";
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
				
				$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE) 
									values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
//				echo "$cmd<br>";
				$KC_ID++;
			} // end while
			$KF_ID++;
			// ==================== insert competence from kpi_position_competence  ==================== //
		} // end while
	}

	if($command == "RECALC" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($search_per_type)){
		if ($CTRL_TYPE==2 && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " select KF_ID, COMPETENCE_WEIGHT, SCORE_COMPETENCE, SUM_COMPETENCE, SUM_KPI, SUM_OTHER, TOTAL_SCORE
						from PER_KPI_FORM where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and 
						KF_END_DATE = '$KF_END_DATE' $where ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		while($data1 = $db_dpis1->get_array()){
			$KF_ID = $data1[KF_ID];
			$COMPETENCE_WEIGHT = $data1[COMPETENCE_WEIGHT];
			$SCORE_COMPETENCE = $data1[SCORE_COMPETENCE];
			$SUM_COMPETENCE = $data1[SUM_COMPETENCE];
			$SUM_KPI = $data1[SUM_KPI];
			$SUM_OTHER = $data1[SUM_OTHER];
			$TOTAL_SCORE = $data1[TOTAL_SCORE];
			
			// ========================================= re-calculate score_competence, sum_competence ==================================//
			$TOTAL_PC_SCORE = 0;
			$FULL_SCORE = 5;
			$tmp_arr_count = $tmp_arr_score = "";
			$cmd = " select 	KC_EVALUATE, KC_WEIGHT, PC_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
			$count_competence = $db_dpis->send_cmd($cmd);
			$BALANCE_WEIGHT = 100 / $count_competence;
			while($data = $db_dpis->get_array()){
				$KC_EVALUATE = $data[KC_EVALUATE];
				$KC_WEIGHT = $data[KC_WEIGHT] + 0;
				$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL] + 0;
				if ($COMPETENCY_SCALE==5) 
					$TOTAL_PC_SCORE += $KC_EVALUATE * $BALANCE_WEIGHT / 100;
				else
					$TOTAL_PC_SCORE += $KC_EVALUATE * $KC_WEIGHT / 100;
				$SUM_EVALUATE += $KC_EVALUATE;
				$SUM_TARGET_LEVEL += $PC_TARGET_LEVEL;
				
				if($KC_EVALUATE != ""){
					if($KC_EVALUATE >= $PC_TARGET_LEVEL) $tmp_arr_count[GE] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 1) $tmp_arr_count[L1] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 2) $tmp_arr_count[L2] += 1;
					elseif(($PC_TARGET_LEVEL - $KC_EVALUATE) <= 3) $tmp_arr_count[L3] += 1;
					else $tmp_arr_count[L3] += 1;
				} // end if
			} // end while
			
			$tmp_arr_score[GE] = $tmp_arr_count[GE] * 3;
			$tmp_arr_score[L1] = $tmp_arr_count[L1] * 2;
			$tmp_arr_score[L2] = $tmp_arr_count[L2] * 1;
			$tmp_arr_score[L3] = $tmp_arr_count[L3] * 0;
			
			if ($COMPETENCY_SCALE==1) {
				$score_competence = array_sum($tmp_arr_score);	
				$sum_competence = round(round((($score_competence / ($count_competence * 3)) * $COMPETENCE_WEIGHT), 3), 2);
			} elseif ($COMPETENCY_SCALE==2 || $COMPETENCY_SCALE==5) { 		
				$score_competence = $TOTAL_PC_SCORE;	
				$sum_competence = round(round((($score_competence / $FULL_SCORE) * $COMPETENCE_WEIGHT), 3), 2);
			} elseif ($COMPETENCY_SCALE==3) { 		
				$score_competence = $SUM_EVALUATE;	
				$sum_competence = round(round((($score_competence / $SUM_TARGET_LEVEL) * $COMPETENCE_WEIGHT), 3), 2);
			}

			if($KF_ID){
				if($DPISDB=="oci8")
					$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
										TOTAL_SCORE=nvl(SUM_KPI,0)+nvl(SUM_OTHER,0)+$sum_competence,
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
				else
					$cmd = " UPDATE PER_KPI_FORM SET
										SCORE_COMPETENCE=$score_competence, SUM_COMPETENCE=$sum_competence, 
										TOTAL_SCORE=SUM_KPI+SUM_OTHER+$sum_competence,
										UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE KF_ID=$KF_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
			// ========================================= re-calculate score_competence, sum_competence ==================================//		
		} // end while
	}

	if($command == "COPY" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($search_per_type)){
		if ($CTRL_TYPE==2 && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " delete from PER_KPI_COMPETENCE where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
	
		$cmd = " select KF_ID, PER_ID 
						from PER_KPI_FORM 
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
		while($data1 = $db_dpis1->get_array()){
			$KF_ID = $data1[KF_ID];
			$PER_ID = $data1[PER_ID];

			$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID
							 from			PER_PERSONAL
							 where		PER_ID = $PER_ID ";		
			$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PER_TYPE = $data[PER_TYPE];
			if($PER_TYPE == 1) $POS_ID = $data[POS_ID];
			elseif($PER_TYPE == 2) $POS_ID = $data[POEM_ID];
			elseif($PER_TYPE == 3) $POS_ID = $data[POEMS_ID];

			// ==================== insert competence from kpi_position_competence  ==================== //
			$cmd = " select	a.CP_CODE, PC_TARGET_LEVEL
							 from		PER_POSITION_COMPETENCE a, PER_COMPETENCE b
							 where	a.CP_CODE=b.CP_CODE and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID = $DEPARTMENT_ID and 
											POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and CP_ASSESSMENT = 'Y' and a.PER_TYPE = $search_per_type
							 order by a.CP_CODE ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				$PC_TARGET_LEVEL = $data[PC_TARGET_LEVEL];
				
				$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE) 
									values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$KC_ID++;
			} // end while
			// ==================== insert competence from kpi_position_competence  ==================== //
		} // end while
	}
	
	//echo "$command ,$KF_COPY_FROMYEAR ,$KF_COPY_TOYEAR,$KF_COPY_FROMCYCLE,$KF_COPY_TOCYCLE";
	if($command == "COPY_KPI" && (trim($KF_COPY_FROMYEAR) && trim($KF_COPY_TOYEAR)) && (trim($KF_COPY_FROMCYCLE) && trim($KF_COPY_TOCYCLE))){
            if($debug==1){echo __LINE__.'Begin <br>----------------------<br>';}
		if ($SESS_PER_ID) $where = "and PER_ID = $SESS_PER_ID";
		elseif ($CTRL_TYPE==2 && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		if (!$search_per_type) $search_per_type = $PER_TYPE;
		if ($KF_COPY_FROMCYCLE==1) {
			$FROM_START_DATE = ($KF_COPY_FROMYEAR - 544) . "-10-01";
			$FROM_END_DATE = ($KF_COPY_FROMYEAR - 543) . "-03-31";
		} elseif ($KF_COPY_FROMCYCLE==2) {
			$FROM_START_DATE = ($KF_COPY_FROMYEAR - 543) . "-04-01";
			$FROM_END_DATE = ($KF_COPY_FROMYEAR - 543) . "-09-30";
		}
		if ($KF_COPY_TOCYCLE==1) {
			$TO_START_DATE = ($KF_COPY_TOYEAR - 544) . "-10-01";
			$TO_END_DATE = ($KF_COPY_TOYEAR - 543) . "-03-31";
		} elseif ($KF_COPY_TOCYCLE==2) {
			$TO_START_DATE = ($KF_COPY_TOYEAR - 543) . "-04-01";
			$TO_END_DATE = ($KF_COPY_TOYEAR - 543) . "-09-30";
		}
		$cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_COPY_TOCYCLE and KF_START_DATE = '$TO_START_DATE' and KF_END_DATE = '$TO_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_COMPETENCE where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_COPY_TOCYCLE and KF_START_DATE = '$TO_START_DATE' and KF_END_DATE = '$TO_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//		$db_dpis->show_error();

		$cmd = " delete from PER_IPIP where KF_ID in (select KF_ID from PER_KPI_FORM 
						where KF_CYCLE = $KF_COPY_TOCYCLE and KF_START_DATE = '$TO_START_DATE' and KF_END_DATE = '$TO_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where) ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//		$db_dpis->show_error();

		$cmd = " delete from PER_KPI_FORM where KF_CYCLE = $KF_COPY_TOCYCLE and KF_START_DATE = '$TO_START_DATE' and 
						KF_END_DATE = '$TO_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis->send_cmd($cmd);
                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//		$db_dpis->show_error();

		$cmd = " select max(KF_ID) as max_id from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KF_ID = $data[max_id] + 1;
	
		$cmd = " select max(PG_ID) as max_id from PER_PERFORMANCE_GOALS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$PG_ID = $data[max_id] + 1;
	
		$cmd = " select max(KC_ID) as max_id from PER_KPI_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$KC_ID = $data[max_id] + 1;
                
                /*แก้ปัญหา ข้อมูลขยะที่ทำให้เกิดข้อมูลซ้ำ*/
                $cmd = " delete from PER_PERFORMANCE_GOALS where KF_ID=$KF_ID ";
                $db_dpis->send_cmd($cmd);

                $cmd = " delete from PER_KPI_COMPETENCE where KF_ID=$KF_ID ";
                $db_dpis->send_cmd($cmd);

                $cmd = " delete from PER_IPIP where KF_ID=$KF_ID ";
                $db_dpis->send_cmd($cmd);

                $cmd = " delete from PER_KPI_FORM where KF_ID=$KF_ID ";
                $db_dpis->send_cmd($cmd);
                /*++++++++++++++++++++++++++++++++++++++++++*/
                
                
	
		$cmd = " select 	KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
							PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
							UPDATE_DATE, ORG_ID, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, FRIEND_FLAG, 
							ORG_ID_1_SALARY, ORG_ID_ASS, ORG_ID_1_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, 
							REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, REVIEW_PM_NAME, REVIEW_LEVEL_NO, 
							REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, 
							REVIEW_PN_NAME1, REVIEW_PER_NAME1, REVIEW_PL_NAME1, REVIEW_PM_NAME1, REVIEW_LEVEL_NO1, 
							REVIEW_PN_NAME2, REVIEW_PER_NAME2, REVIEW_PL_NAME2, REVIEW_PM_NAME2, REVIEW_LEVEL_NO2
				  		 from 		PER_KPI_FORM
						 where KF_CYCLE = $KF_COPY_FROMCYCLE and KF_START_DATE = '$FROM_START_DATE' and 
						KF_END_DATE = '$FROM_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$OLD_KF_ID = $data[KF_ID];
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);		
			$KF_CYCLE = $data[KF_CYCLE];
			$KF_START_DATE = trim($data[KF_START_DATE]);		
			$KF_END_DATE = trim($data[KF_END_DATE]);		
			$PER_ID_REVIEW = $data[PER_ID_REVIEW];
			$PER_ID_REVIEW0 = $data[PER_ID_REVIEW0];
			$PER_ID_REVIEW1 = $data[PER_ID_REVIEW1];
			$PER_ID_REVIEW2 = $data[PER_ID_REVIEW2];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$TMP_UPDATE_USER = $data[UPDATE_USER];
			$TMP_UPDATE_DATE = trim($data[UPDATE_DATE]);		
			$TMP_ORG_ID = $data[ORG_ID];
			$SALARY_FLAG = trim($data[SALARY_FLAG]);		
			$ORG_ID_SALARY = $data[ORG_ID_SALARY];
			$KPI_FLAG = trim($data[KPI_FLAG]);		
			$ORG_ID_KPI = $data[ORG_ID_KPI];
			$CHIEF_PER_ID = $data[CHIEF_PER_ID];
			$FRIEND_FLAG = trim($data[FRIEND_FLAG]);		
			$ORG_ID_1_SALARY = $data[ORG_ID_1_SALARY];
			$ORG_ID_ASS = $data[ORG_ID_ASS];
			$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
			$PERFORMANCE_WEIGHT = $data[PERFORMANCE_WEIGHT];
			$COMPETENCE_WEIGHT = $data[COMPETENCE_WEIGHT];
			$OTHER_WEIGHT = $data[OTHER_WEIGHT];
			$FRIEND_FLAG = trim($data[FRIEND_FLAG]);		
			$REVIEW_PN_NAME = trim($data[REVIEW_PN_NAME]);		
			$REVIEW_PER_NAME = trim($data[REVIEW_PER_NAME]);		
			$REVIEW_PL_NAME = trim($data[REVIEW_PL_NAME]);		
			$REVIEW_PM_NAME = trim($data[REVIEW_PM_NAME]);		
			$REVIEW_LEVEL_NO = trim($data[REVIEW_LEVEL_NO]);		
			$REVIEW_PN_NAME0 = trim($data[REVIEW_PN_NAME0]);		
			$REVIEW_PER_NAME0 = trim($data[REVIEW_PER_NAME0]);		
			$REVIEW_PL_NAME0 = trim($data[REVIEW_PL_NAME0]);		
			$REVIEW_PM_NAME0 = trim($data[REVIEW_PM_NAME0]);		
			$REVIEW_LEVEL_NO0 = trim($data[REVIEW_LEVEL_NO0]);		
			$REVIEW_PN_NAME1 = trim($data[REVIEW_PN_NAME1]);		
			$REVIEW_PER_NAME1 = trim($data[REVIEW_PER_NAME1]);		
			$REVIEW_PL_NAME1 = trim($data[REVIEW_PL_NAME1]);		
			$REVIEW_PM_NAME1 = trim($data[REVIEW_PM_NAME1]);		
			$REVIEW_LEVEL_NO1 = trim($data[REVIEW_LEVEL_NO1]);		
			$REVIEW_PN_NAME2 = trim($data[REVIEW_PN_NAME2]);		
			$REVIEW_PER_NAME2 = trim($data[REVIEW_PER_NAME2]);		
			$REVIEW_PL_NAME2 = trim($data[REVIEW_PL_NAME2]);		
			$REVIEW_PM_NAME2 = trim($data[REVIEW_PM_NAME2]);		
			$REVIEW_LEVEL_NO2 = trim($data[REVIEW_LEVEL_NO2]);		
			
			$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, ORG_ID, ORG_ID_1, LEVEL_NO, PER_SALARY
							 from			PER_PERSONAL
							 where		PER_ID = $PER_ID ";		
			$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_TYPE = $data2[PER_TYPE];
			$POS_ID = $data2[POS_ID];
			$POEM_ID = $data2[POEM_ID];
			$POEMS_ID = $data2[POEMS_ID];
			$POT_ID = $data2[POT_ID];
			$ORG_ID_ASS = $data2[ORG_ID];
			$ORG_ID_1_ASS = $data2[ORG_ID_1];
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			$PER_SALARY = trim($data2[PER_SALARY]);
			if($PER_TYPE == 1) {
				$cmd = " select	ORG_ID, ORG_ID_1, b.PL_NAME, a.PT_CODE, a.PM_CODE 
									from PER_POSITION a, PER_LINE b 
									where POS_ID = $POS_ID and a.PL_CODE=b.PL_CODE ";		
			} elseif($PER_TYPE == 2) {
				$cmd = " select	ORG_ID, ORG_ID_1, b.PN_NAME as PL_NAME
									from PER_POS_EMP a, PER_POS_NAME b 
									where POEM_ID = $POEM_ID and a.PN_CODE=b.PN_CODE ";		
			} elseif($PER_TYPE == 3) {
				$cmd = " select	ORG_ID, ORG_ID_1, b.EP_NAME as PL_NAME 
									from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where POEMS_ID = $POEMS_ID and a.EP_CODE=b.EP_CODE ";		
			} elseif($PER_TYPE == 4) {
				$cmd = " select	ORG_ID, ORG_ID_1, b.TP_NAME as PL_NAME 
									from PER_POS_TEMP a, PER_TEMP_POS_NAME b 
									where POT_ID = $POT_ID and a.TP_CODE=b.TP_CODE ";		
			}

			$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_ID = $data2[ORG_ID];
			$ORG_ID_1_SALARY = $data2[ORG_ID_1];
			$ORG_ID_SALARY = $TMP_ORG_ID;
			$ORG_ID_KPI = $TMP_ORG_ID;
			$PL_NAME = trim($data2[PL_NAME]);
			$PM_CODE = trim($data2[PM_CODE]);
			$PM_NAME = "";
			if($PM_CODE){
				$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PM_NAME = trim($data2[PM_NAME]);
			} // end if 

			if (!$PER_ID_REVIEW) $PER_ID_REVIEW = "NULL";
			if (!$PER_ID_REVIEW0) $PER_ID_REVIEW0 = "NULL";
			if (!$PER_ID_REVIEW1) $PER_ID_REVIEW1 = "NULL";
			if (!$PER_ID_REVIEW2) $PER_ID_REVIEW2 = "NULL";
			if (!$TMP_DEPARTMENT_ID) $TMP_DEPARTMENT_ID = "NULL";
			if (!$TMP_ORG_ID) $TMP_ORG_ID = "NULL";
			if (!$ORG_ID_SALARY) $ORG_ID_SALARY = "NULL";
			if (!$ORG_ID_KPI) $ORG_ID_KPI = "NULL";
			if (!$CHIEF_PER_ID) $CHIEF_PER_ID = "NULL";
			if (!$ORG_ID_1_SALARY) $ORG_ID_1_SALARY = "NULL";
			if (!$ORG_ID_ASS) $ORG_ID_ASS = "NULL";
			if (!$ORG_ID_1_ASS) $ORG_ID_1_ASS = "NULL";
			if (!$PERFORMANCE_WEIGHT) $PERFORMANCE_WEIGHT = "NULL";
			if (!$COMPETENCE_WEIGHT) $COMPETENCE_WEIGHT = "NULL";
			if (!$OTHER_WEIGHT) $OTHER_WEIGHT = "NULL";

			$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, PER_CARDNO, KF_CYCLE, KF_START_DATE, KF_END_DATE, 
							PER_ID_REVIEW, PER_ID_REVIEW0, PER_ID_REVIEW1, PER_ID_REVIEW2, DEPARTMENT_ID, UPDATE_USER, 
							UPDATE_DATE, ORG_ID, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, CHIEF_PER_ID, FRIEND_FLAG, 
							ORG_ID_1_SALARY, ORG_ID_ASS, PERFORMANCE_WEIGHT, COMPETENCE_WEIGHT, OTHER_WEIGHT, ORG_ID_1_ASS, 
							LEVEL_NO, PER_SALARY, PL_NAME, PM_NAME, REVIEW_PN_NAME, REVIEW_PER_NAME, REVIEW_PL_NAME, 
							REVIEW_PM_NAME, REVIEW_LEVEL_NO, REVIEW_PN_NAME0, REVIEW_PER_NAME0, REVIEW_PL_NAME0, 
							REVIEW_PM_NAME0, REVIEW_LEVEL_NO0, REVIEW_PN_NAME1, REVIEW_PER_NAME1, REVIEW_PL_NAME1, 
							REVIEW_PM_NAME1, REVIEW_LEVEL_NO1, REVIEW_PN_NAME2, REVIEW_PER_NAME2, REVIEW_PL_NAME2, 
							REVIEW_PM_NAME2, REVIEW_LEVEL_NO2) 
							values ($KF_ID, $PER_ID, '$PER_CARDNO', $KF_COPY_TOCYCLE, '$TO_START_DATE', '$TO_END_DATE', $PER_ID_REVIEW, 
							$PER_ID_REVIEW0, $PER_ID_REVIEW1, $PER_ID_REVIEW2, $TMP_DEPARTMENT_ID, $TMP_UPDATE_USER, 
							'$TMP_UPDATE_DATE', $TMP_ORG_ID, '$SALARY_FLAG', $ORG_ID_SALARY, '$KPI_FLAG', $ORG_ID_KPI, $CHIEF_PER_ID, 
							'$FRIEND_FLAG', $ORG_ID_1_SALARY, $ORG_ID_ASS, $PERFORMANCE_WEIGHT, $COMPETENCE_WEIGHT, $OTHER_WEIGHT, 
							$ORG_ID_1_ASS, '$LEVEL_NO', $PER_SALARY, '$PL_NAME', '$PM_NAME', '$REVIEW_PN_NAME', '$REVIEW_PER_NAME', 
							'$REVIEW_PL_NAME', '$REVIEW_PM_NAME', '$REVIEW_LEVEL_NO', '$REVIEW_PN_NAME0', '$REVIEW_PER_NAME0', 
							'$REVIEW_PL_NAME0', '$REVIEW_PM_NAME0', '$REVIEW_LEVEL_NO0', '$REVIEW_PN_NAME1', '$REVIEW_PER_NAME1', 
							'$REVIEW_PL_NAME1', '$REVIEW_PM_NAME1', '$REVIEW_LEVEL_NO1', '$REVIEW_PN_NAME2', '$REVIEW_PER_NAME2', 
							'$REVIEW_PL_NAME2', '$REVIEW_PM_NAME2', '$REVIEW_LEVEL_NO2') ";
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//				$db_dpis2->show_error();

			$cmd = " select 	PG_SEQ, KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, KF_TYPE,
										KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
										KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, 
										KPI_TARGET_LEVEL5_DESC, KPI_OTHER
							 from 		PER_PERFORMANCE_GOALS
							 where KF_ID = $OLD_KF_ID ";
			$db_dpis1->send_cmd($cmd);
	//			$db_dpis1->show_error();
			while($data1 = $db_dpis1->get_array()){
				$PG_SEQ = $data1[PG_SEQ];
				$KPI_ID = $data1[KPI_ID];
				$KPI_NAME = trim($data1[KPI_NAME]);		
				$KPI_WEIGHT = $data1[KPI_WEIGHT];
				$KPI_MEASURE = trim($data1[KPI_MEASURE]);		
				$KPI_PER_ID = $data1[KPI_PER_ID];
				$KF_TYPE = trim($data1[KF_TYPE]);		
				$KPI_TARGET_LEVEL1 = $data1[KPI_TARGET_LEVEL1];
				$KPI_TARGET_LEVEL2 = $data1[KPI_TARGET_LEVEL2];
				$KPI_TARGET_LEVEL3 = $data1[KPI_TARGET_LEVEL3];
				$KPI_TARGET_LEVEL4 = $data1[KPI_TARGET_LEVEL4];
				$KPI_TARGET_LEVEL5 = $data1[KPI_TARGET_LEVEL5];
				$KPI_TARGET_LEVEL1_DESC = trim($data1[KPI_TARGET_LEVEL1_DESC]);		
				$KPI_TARGET_LEVEL2_DESC = trim($data1[KPI_TARGET_LEVEL2_DESC]);		
				$KPI_TARGET_LEVEL3_DESC = trim($data1[KPI_TARGET_LEVEL3_DESC]);		
				$KPI_TARGET_LEVEL4_DESC = trim($data1[KPI_TARGET_LEVEL4_DESC]);		
				$KPI_TARGET_LEVEL5_DESC = trim($data1[KPI_TARGET_LEVEL5_DESC]);		
				$KPI_ID = "NULL";
				$KPI_OTHER = trim($data1[KPI_OTHER]);		
				if (!$KPI_WEIGHT) $KPI_WEIGHT = "NULL";
				if (!$KPI_TARGET_LEVEL1) $KPI_TARGET_LEVEL1 = "NULL";
				if (!$KPI_TARGET_LEVEL2) $KPI_TARGET_LEVEL2 = "NULL";
				if (!$KPI_TARGET_LEVEL3) $KPI_TARGET_LEVEL3 = "NULL";
				if (!$KPI_TARGET_LEVEL4) $KPI_TARGET_LEVEL4 = "NULL";
				if (!$KPI_TARGET_LEVEL5) $KPI_TARGET_LEVEL5 = "NULL";
				
				$cmd = " 	insert into PER_PERFORMANCE_GOALS
									(KF_ID, PG_ID, PG_SEQ, KPI_ID, KPI_NAME, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, KF_TYPE,
									KPI_TARGET_LEVEL1, KPI_TARGET_LEVEL2, KPI_TARGET_LEVEL3, KPI_TARGET_LEVEL4, KPI_TARGET_LEVEL5,
									KPI_TARGET_LEVEL1_DESC, KPI_TARGET_LEVEL2_DESC, KPI_TARGET_LEVEL3_DESC, KPI_TARGET_LEVEL4_DESC, KPI_TARGET_LEVEL5_DESC,
									KPI_OTHER, UPDATE_USER, UPDATE_DATE)
									values
									($KF_ID, $PG_ID, $PG_SEQ, $KPI_ID, '$KPI_NAME', $KPI_WEIGHT, '$KPI_MEASURE', $KPI_PER_ID, '$KF_TYPE',
									$KPI_TARGET_LEVEL1, $KPI_TARGET_LEVEL2, $KPI_TARGET_LEVEL3, $KPI_TARGET_LEVEL4, $KPI_TARGET_LEVEL5,
									'$KPI_TARGET_LEVEL1_DESC', '$KPI_TARGET_LEVEL2_DESC', '$KPI_TARGET_LEVEL3_DESC', '$KPI_TARGET_LEVEL4_DESC', '$KPI_TARGET_LEVEL5_DESC',
									'$KPI_OTHER', $SESS_USERID, '$UPDATE_DATE')   ";
				$db_dpis2->send_cmd($cmd);
                                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
	//				$db_dpis2->show_error();
				$PG_ID++;
			} // end while
		
			$cmd = " select		PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID
							 from			PER_PERSONAL
							 where		PER_ID = $PER_ID ";		
			$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PER_TYPE = $data2[PER_TYPE];
			if($PER_TYPE == 1) $POS_ID = $data2[POS_ID];
			elseif($PER_TYPE == 2) $POS_ID = $data2[POEM_ID];
			elseif($PER_TYPE == 3) $POS_ID = $data2[POEMS_ID];
			elseif($PER_TYPE == 4) $POS_ID = $data2[POT_ID];

			// ==================== insert competence from kpi_position_competence  ==================== //
			$cmd = " select	a.CP_CODE, PC_TARGET_LEVEL
							 from		PER_POSITION_COMPETENCE a, PER_COMPETENCE b
							 where	a.CP_CODE=b.CP_CODE and a.DEPARTMENT_ID=b.DEPARTMENT_ID and a.DEPARTMENT_ID = $DEPARTMENT_ID and 
											POS_ID=$POS_ID and PC_TARGET_LEVEL > 0 and CP_ASSESSMENT = 'Y' and a.PER_TYPE = $search_per_type
							 order by a.CP_CODE ";
			$db_dpis2->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//			$db_dpis2->show_error();
			while($data2 = $db_dpis2->get_array()){
				$CP_CODE = $data2[CP_CODE];
				$PC_TARGET_LEVEL = $data2[PC_TARGET_LEVEL];
				
				$cmd = " insert into PER_KPI_COMPETENCE (KC_ID, KF_ID, CP_CODE, PC_TARGET_LEVEL, UPDATE_USER, 
									UPDATE_DATE) 
									values ($KC_ID, $KF_ID, '$CP_CODE', $PC_TARGET_LEVEL, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
                                if($debug==1){echo __LINE__.':'.$cmd.'<br>----------------------<br>';}
//				$db_dpis1->show_error();
				$KC_ID++;
			} // end while
			// ==================== insert competence from kpi_position_competence  ==================== //

			$KF_ID++;
		} // end while
		if($debug==1){echo __LINE__.'Begin <br>----------------------<br>';}
                
	}

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_kf_id);
		$cmd = " update PER_KPI_FORM set KF_SCORE_STATUS = 0 where KF_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_KPI_FORM set KF_SCORE_STATUS = 1 where KF_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูลการอนุญาตให้เห็นคะแนน [KF_ID : ".trim(stripslashes($setflagshow))."]");
	}
	
	if($command == "SCORE" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($search_per_type)){
		if ($CTRL_TYPE==2 && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " update PER_KPI_FORM set KF_SCORE_STATUS = 1
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
	}
	
	if($command == "NOSCORE" && trim($KF_YEAR) && trim($KF_CYCLE) && trim($search_per_type)){
		if ($CTRL_TYPE==2 && $SESS_USERID==1 && !$DEPARTMENT_ID) $where = "";
		else $where = "and DEPARTMENT_ID = $DEPARTMENT_ID";
		$cmd = " update PER_KPI_FORM set KF_SCORE_STATUS = NULL
						where KF_CYCLE = $KF_CYCLE and KF_START_DATE = '$KF_START_DATE' and KF_END_DATE = '$KF_END_DATE' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = $search_per_type) $where ";
		$db_dpis1->send_cmd($cmd);
//		$db_dpis1->show_error();
	}
        
        /*http://dpis.ocsc.go.th/Service/node/1111*/
        if($command == "UPDATESALARYNOW" && trim($KF_YEAR) && trim($KF_CYCLE1) && trim($search_per_type)){
             /*var CYCLE = f.KF_CYCLE1.value;
                    var date1 = f.KF_START_DATE_1.value+' ถึง '+f.KF_END_DATE_1.value;
                    var date2 = f.KF_START_DATE_2.value+' ถึง '+f.KF_END_DATE_2.value;*/
            
           $cmd = "UPDATE PER_KPI_FORM kpifrm
                    SET kpifrm.PER_SALARY = 
                        (select max(psh.SAH_SALARY) as SAH_SALARY from PER_SALARYHIS psh  
                         where psh.PER_ID=kpifrm.PER_ID  and SUBSTR(psh.SAH_EFFECTIVEDATE, 1, 10) = SUBSTR(kpifrm.KF_START_DATE, 1, 10) ) 
                    WHERE kpifrm.DEPARTMENT_ID = $DEPARTMENT_ID and kpifrm.KF_CYCLE =$KF_CYCLE1 
                      and (SUBSTR(kpifrm.KF_START_DATE, 1, 10) >= '$KF_START_DATE') and (SUBSTR(kpifrm.KF_END_DATE, 1, 10) <= '$KF_END_DATE') ";
         
            $db_dpis1->send_cmd($cmd); 
            $db_dpis1->send_cmd("COMMIT"); 
        }
        /**/
        
        
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$KF_ID = "";
		if (!$KF_YEAR) $KF_YEAR = $KPI_BUDGET_YEAR;
		if (!$KF_YEAR)
			if(date("Y-m-d") <= date("Y")."-10-01") $KF_YEAR = date("Y") + 543;
			else $KF_YEAR = (date("Y") + 543) + 1;
		$KF_CYCLE = $KPI_CYCLE;
		$KF_START_DATE_1 = "01/10/". ($KF_YEAR - 1);
		$KF_END_DATE_1 = "31/03/". $KF_YEAR;
		$KF_START_DATE_2 = "01/04/". $KF_YEAR;
		$KF_END_DATE_2 = "30/09/". $KF_YEAR;

		if($SESS_GROUPCODE != "BUREAU" && substr($SESS_GROUPCODE, 0, 7) != "BUREAU_"  && $SESS_USERGROUP!=3){
			$PER_ID = "";
			$PER_CARDNO = "";
			$PER_NAME = "";
			$PL_NAME = "";
			$PM_NAME = "";
			$LEVEL_NAME = "";
			$ORG_NAME = "";
			$PER_SALARY = "";
		} // end if

		$PER_ID_REVIEW = "";
		$REVIEW_FULL_NAME = "";
		$REVIEW_PN_NAME = "";
		$REVIEW_PER_NAME = "";
		$REVIEW_PL_NAME = "";
		$REVIEW_PM_NAME = "";
		$REVIEW_LEVEL_NO = "";
		$REVIEW_LEVEL_NAME = "";

		$PER_ID_REVIEW0 = "";
		$REVIEW_FULL_NAME0 = "";
		$REVIEW_PN_NAME0 = "";
		$REVIEW_PER_NAME0 = "";
		$REVIEW_PL_NAME0 = "";
		$REVIEW_PM_NAME0 = "";
		$REVIEW_LEVEL_NO0 = "";
		$REVIEW_LEVEL_NAME0 = "";

		$PER_ID_REVIEW1 = "";
		$REVIEW_FULL_NAME1 = "";
		$REVIEW_PN_NAME1 = "";
		$REVIEW_PER_NAME1 = "";
		$REVIEW_PL_NAME1 = "";
		$REVIEW_PM_NAME1 = "";
		$REVIEW_LEVEL_NO1 = "";
		$REVIEW_LEVEL_NAME1 = "";

		$PER_ID_REVIEW2 = "";
		$REVIEW_FULL_NAME2 = "";
		$REVIEW_PN_NAME2 = "";
		$REVIEW_PER_NAME2 = "";
		$REVIEW_PL_NAME2 = "";
		$REVIEW_PM_NAME2 = "";
		$REVIEW_LEVEL_NO2 = "";
		$REVIEW_LEVEL_NAME2 = "";

		$search_per_type = "";
	} // end if

	if ($command=="LOOP2" && !$KF_ID) { 	// กรณีเป็น loop 2 และเป็นการเพิ่มข้อมูล
		$command = ""; 
		$UPD=0; 	// กำหนดสถานะ update ให้กลับเป็น add
	}

?>