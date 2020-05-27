<?php
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
        
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
        $debug=0;/*0=close,1=open*/
        if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			$search_department_id1 = $DEPARTMENT_ID;
			$search_department_name1 = $DEPARTMENT_NAME;
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
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			$search_department_id1 = $DEPARTMENT_ID;
			$search_department_name1 = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			$search_department_id1 = $DEPARTMENT_ID;
			$search_department_name1 = $DEPARTMENT_NAME;
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
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			$search_ministry_id1 = $MINISTRY_ID;
			$search_ministry_name1 = $MINISTRY_NAME;
			$search_department_id1 = $DEPARTMENT_ID;
			$search_department_name1 = $DEPARTMENT_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			break;
	} // end switch case

	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	$search_per_type = 1;
	if(!$search_cp_cycle) $search_cp_cycle = array(1,2);
	if(!$search_cp_confirm) $search_cp_confirm = array(1,0);
	if(!$CP_YEAR)	$CP_YEAR = $KPI_BUDGET_YEAR;
	if(!$search_cp_year)	$search_cp_year = $KPI_BUDGET_YEAR;
	if(!trim($CP_YEAR)){
		if(date("Y-m-d") <= date("Y")."-10-01") $CP_YEAR = date("Y") + 543;
		else $CP_YEAR = (date("Y") + 543) + 1;
	} // end if
	if (!$CP_CYCLE) $CP_CYCLE = $KPI_CYCLE;
	if (!$CP_CYCLE) 
		if (substr($UPDATE_DATE,5,2) > "09" || substr($UPDATE_DATE,5,2) < "04") $CP_CYCLE = 1;
		elseif (substr($UPDATE_DATE,5,2) > "03" && substr($UPDATE_DATE,5,2) < "10") $CP_CYCLE = 2;
/*
	if(!isset($search_cp_cycle)){
		$search_cp_cycle[] = $CP_CYCLE;
	} // end if
*/	
	$_select_level_no_str = implode("','",$_select_level_no);
	
	if($search_department_id && $search_department_id != 'NULL'){
		$where = " and b.DEPARTMENT_ID = $search_department_id "; 
	}elseif($search_ministry_id){
		$where = "and b.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
	}
	if($search_org_id && $search_org_id != 'NULL') 
		if ($select_org_structure==0) $where .= " and b.ORG_ID_SALARY = $search_org_id ";
		elseif ($select_org_structure==1) $where .= " and b.ORG_ID_ASS = $search_org_id ";
	if($search_org_id_1 && $search_org_id_1 != 'NULL') 
		if ($select_org_structure==0) $where .= " and b.ORG_ID_1_SALARY = $search_org_id_1 ";
		elseif ($select_org_structure==1) $where .= " and b.ORG_ID_1_ASS = $search_org_id_1 ";
	
        if($search_org_id && $search_org_id != 'NULL' && $SAME_ASSESS_LEVEL != 1) $where_org = " and (DEPARTMENT_ID = $search_department_id and ORG_ID = $search_org_id) ";
	elseif($search_department_id && $search_department_id != 'NULL' ) $where_org = " and (DEPARTMENT_ID = $search_department_id and ORG_ID IS NULL) ";
	elseif($search_ministry_id) $where_org = " and (DEPARTMENT_ID IS NULL and ORG_ID IS NULL) ";
	else $where_org = " and (DEPARTMENT_ID IS NULL and ORG_ID IS NULL) ";
        

       // echo $search_ministry_id.'>>'.$where_org;
        
        
	$cmd = " select AL_CODE from PER_ASSESS_LEVEL 
					where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
					order by AL_POINT_MIN DESC ";
	$count_data = $db_dpis->send_cmd($cmd);
//	echo $cmd;
	if (!$count_data) $where_org = " and DEPARTMENT_ID = '$search_department_id' and org_id is NULL ";

	if ($ISCS_FLAG == 1) 
		$array_xx = array('K'=>'k','D'=>'d','M'=>'m');
	else
		$array_xx = array('O'=>'o','K'=>'k','D'=>'d','M'=>'m');
	if(empty($Testing)) {
		foreach($array_xx as $key => $value) {
			$cmd = " select 	AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT
								from 	PER_ASSESS_LEVEL 
							 where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
							 order by AL_POINT_MIN DESC ";
			$db_dpis->send_cmd($cmd);
                        if($debug==1){echo 'Ln:'.__LINE__.' Testing:<pre>'.$cmd.'<br>';}
			while($data = $db_dpis->get_array()) {	
				$temp_code = $data[AL_CODE];
				$tmp_arr = "code_".$value."_".$temp_code ; 
				$code_arr = $$tmp_arr;
				$sf_code[$key][$temp_code] = $code_arr;
				$val_sf_code[$key][$temp_code] = "$temp_code,$code_arr";
			}	
		}
	} elseif($Testing == '1') {
		foreach($_POST as $key => $value) {
			if(substr($key,0,4) == 'code') {
				list($strcode,$code,$id) = explode('_',$key);
				$code = strtoupper($code);
				$sf_code[$code][$id] = $value;
				if($debug==1){echo 'Ln:'.__LINE__."$key - $value <br>";}
				$val_sf_code[$code][$id] = "$id,$value";
			}
		}
	}
	
	/*$cmd = " select LEVEL_NO, LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
					 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
					 from PER_LAYER where LAYER_TYPE = 0 and LAYER_NO = 0 ";*//*เดิม*/
        /*Release 5.2.1.6 Begin*/
        $cmd = "select LEVEL_NO, LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
                    LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2,
                    LAYER_SALARY_TEMPUP
                from PER_LAYER 
                where LAYER_TYPE = 0 and LAYER_NO = 0 ";
        /*Release 5.2.1.6 End*/
	$db_dpis->send_cmd($cmd);
	if($debug==1){echo 'Ln:'.__LINE__.' <pre>'.$cmd.'<br>';}
	while($data = $db_dpis->get_array()) {
		$xx_level_no = $data[LEVEL_NO];
                //echo $xx_level_no.'='.$data[LAYER_SALARY_MAX].','.$data[LAYER_SALARY_TEMPUP].'<br>';
		$SALARY_POINT[$xx_level_no][max]  = $data[LAYER_SALARY_MAX];
		$SALARY_POINT[$xx_level_no][mid]  = $data[LAYER_SALARY_MIDPOINT];
		$SALARY_POINT[$xx_level_no][mid1] = $data[LAYER_SALARY_MIDPOINT1];
		$SALARY_POINT[$xx_level_no][mid2] = $data[LAYER_SALARY_MIDPOINT2];
		$SALARY_POINT[$xx_level_no][full]  = $data[LAYER_SALARY_FULL]; //เงินเดือนพิเศษ
		$SALARY_POINT[$xx_level_no][extra]  = $data[LAYER_EXTRA_MIDPOINT];
		$SALARY_POINT[$xx_level_no][extra1] = $data[LAYER_EXTRA_MIDPOINT1];
		$SALARY_POINT[$xx_level_no][extra2] = $data[LAYER_EXTRA_MIDPOINT2];
                $SALARY_POINT[$xx_level_no][salary_tempup] = $data[LAYER_SALARY_TEMPUP];/*Release 5.2.1.6 Add New*/
	}

	if($CP_CYCLE == 1){
		$arr_temp = explode("/", $CP_START_DATE_1);
		$CP_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $CP_END_DATE_1);
		$CP_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	}elseif($CP_CYCLE == 2){
		$arr_temp = explode("/", $CP_START_DATE_2);
		$CP_START_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];

		$arr_temp = explode("/", $CP_END_DATE_2);
		$CP_END_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if

//	echo $command . "+++";  
	if($command == "ADD"){
		if($CP_ID){
		 $cmd = " select CP_ID,CP_NAME from PER_COMPENSATION_TEST where CP_ID = $CP_ID";
		 $count_duplicate = $db_dpis->send_cmd($cmd);
		}
		if($count_duplicate <= 0){
			$cmd = " select max(CP_ID) as MAX_ID from PER_COMPENSATION_TEST ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CP_ID = $data[MAX_ID] + 1;
			if (!$search_department_id) $search_department_id = "NULL";
			if (!$search_org_id) $search_org_id = "NULL";
			if (!$search_org_id_1) $search_org_id_1 = "NULL";
			
			$HOLD_SALARY_STR = str_replace(",","",$HOLD_SALARY);
			//$QTY_KEY_SUM_STR = implode(':',$QTY_KEY_SUM);
			foreach($QTY_KEY_SUM as $key => $value) $QTY_KEY_SUM_STR .= "$key=$value:";
			$QTY_KEY_SUM_STR = substr($QTY_KEY_SUM_STR,0,-1);
			
			//$SALARY_KEY_SUM_STR = implode(':',$SALARY_KEY_SUM);
			foreach($SALARY_KEY_SUM as $key => $value) $SALARY_KEY_SUM_STR .= "$key=$value:";
			$SALARY_KEY_SUM_STR = substr($SALARY_KEY_SUM_STR,0,-1);
			if(!$CP_RESULT) $CP_RESULT = 0;
			// insert header
			$cmd = " insert into PER_COMPENSATION_TEST (CP_ID, CP_NAME, CP_DATE, CP_CYCLE, CP_START_DATE, 
							  CP_END_DATE,	CP_BUDGET, ORG_ID, DEPARTMENT_ID, SF_CODE_O, SF_CODE_K, SF_CODE_D, SF_CODE_M, 
							  CP_RESULT, O_QTY, O_SALARY, K_QTY, K_SALARY, D_QTY, D_SALARY, M_QTY, M_SALARY, SUM_QTY, 
							  SUM_SALARY, HOLD_SALARY, UPDATE_USER, UPDATE_DATE, CP_CONFIRM, ORG_ID_1, PER_TYPE) 
                                values ($CP_ID, '$CP_NAME', '$UPDATE_DATE', $CP_CYCLE, '$CP_START_DATE', '$CP_END_DATE', $CP_BUDGET, 
							  $search_org_id, $search_department_id, '".$SF_CODE_O . "#". implode(':',$val_sf_code[O])."',
							  '".$SF_CODE_K . "#". implode(':',$val_sf_code[K])."', '".$SF_CODE_D . "#". implode(':',$val_sf_code[D])."',
							  '".$SF_CODE_M . "#". implode(':',$val_sf_code[M])."', $CP_RESULT, '$O_QTY_TOTAL', '$O_SALARY_TOTAL', 
							  '$K_QTY_TOTAL', '$K_SALARY_TOTAL', '$D_QTY_TOTAL', '$D_SALARY_TOTAL', '$M_QTY_TOTAL', 
							  '$M_SALARY_TOTAL', '$QTY_KEY_SUM_STR', '$SALARY_KEY_SUM_STR', '$HOLD_SALARY', $SESS_USERID, 
							  '$UPDATE_DATE', 0, $search_org_id_1, $search_per_type) ";
			$db_dpis->send_cmd($cmd);
                        if($debug==1){echo 'Ln:'.__LINE__.' <pre>'.$cmd.'<br>';}
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >  เพิ่มข้อมูล  [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");

			unset($AL_NAME);
			$cmd = " select AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
                                from PER_ASSESS_LEVEL
                                where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
                                order by AL_POINT_MIN DESC ";
                        if($debug==1){echo 'Ln:'.__LINE__.' <pre>'.$cmd.'<br>';}
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$AL_CODE = $data[AL_CODE];
				$AL_CODE_JOIN .= $AL_CODE . ":";
				$AL_NAME[$AL_CODE] = $data[AL_NAME];
				$AL[$AL_CODE][MIN] = $data[AL_POINT_MIN];
				$AL[$AL_CODE][MAX] = $data[AL_POINT_MAX];
				$AL[$AL_CODE][PERCENT] = $data[AL_PERCENT];
			}
			// insert dtl
			$cmd = " select max(CD_ID) as MAX_ID from PER_COMPENSATION_TEST_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CD_ID = $data[MAX_ID] + 1; 
			
			$cmd = " select a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, TOTAL_SCORE, POS_ID, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_KPI_FORM b
                            where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE = '$CP_START_DATE' 
							$where
							and KF_END_DATE =  '$CP_END_DATE' and a.LEVEL_NO in ('".$_select_level_no_str."') 
							order by a.DEPARTMENT_ID, a.PER_NAME, a.PER_SURNAME " ;
			$db_dpis->send_cmd($cmd);
			if($debug==1){echo 'Ln:'.__LINE__.' <pre>'.$cmd.'<br>';}
			while($data = $db_dpis->get_array()) {
                            $PER_ID = $data[PER_ID];
                            $PER_SALARY = $data[PER_SALARY];
                            $LEVEL_NO = $data[LEVEL_NO];
                            $SALARY_FLAG = $data[SALARY_FLAG];
                            $TOTAL_SCORE = $data[TOTAL_SCORE];
                            $POS_ID = $data[POS_ID];
                            $tmp_PER_SALARY = $PER_SALARY;
                            $PER_NAME = $data[PER_NAME];
                            $PER_SURNAME = $data[PER_SURNAME];

                            $cmd = " select LAYER_TYPE, a.POS_NO, a.PL_CODE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $LAYER_TYPE = $data1[LAYER_TYPE] + 0;
                            $POS_NO = $data1[POS_NO];
                            $PL_CODE = $data1[PL_CODE];
	
                            foreach($AL as $al_code => $point_range) {
                                if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
                                    $temp_level = $LEVEL_NO[0];
                                    
                                    /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/  
                                    //if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY <= $SALARY_POINT[$LEVEL_NO][full]) {
                                    if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY < $SALARY_POINT[$LEVEL_NO][full]) {
                                        $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][full];
                                        $SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][extra];
                                        $SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][extra1];
                                        $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][extra2];
                                    } else {
                                        $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                                        $SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][mid];
                                        $SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][mid1];
                                        $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
                                    }
                                    /*Release 5.2.1.6 Begin*/
                                    $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                                    //echo $LEVEL_NO.'='.$LAYER_SALARY_MAX.'<br>';
                                    if($LEVEL_NO == "O3" || $LEVEL_NO == "K5"){
                                        if($debug==1){echo __LINE__.':'.$PER_SALARY.'>='.$SALARY_POINT[$LEVEL_NO][full].'<br>'.'<br>';}
                                        if( $PER_SALARY >=$SALARY_POINT[$LEVEL_NO][full]){
                                            $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
                                            if($debug==1){echo __LINE__.':'.$command.'='.$SALARY_POINT_MID2.'<br>'.'<br>';}
                                        }
                                    }
                                    /*Release 5.2.1.6 End*/
                                    if($SALARY_POINT_MID > $PER_SALARY) {
                                        $PER_SALARY = $SALARY_POINT_MID1;
                                    } else {
                                        $PER_SALARY = $SALARY_POINT_MID2;
                                    }

                                    $CD_MIDPOINT = $PER_SALARY;
                                    $CD_PERCENT = $sf_code[$temp_level][$al_code];
                                    $CD_SALARY = round($PER_SALARY * (($sf_code[$temp_level][$al_code]/100)),2) ;
                                    $CD_SALARY_CEIL = (ceil($PER_SALARY * (($sf_code[$temp_level][$al_code]/100))  /10))*10 ;
                                    $CD_EXTRA_SALARY = 0;
                                    if ($tmp_PER_SALARY + $CD_SALARY_CEIL > $LAYER_SALARY_MAX) {
                                            $CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
                                            if ($tmp_PER_SALARY == $LAYER_SALARY_MAX) $CD_SALARY = 0;
                                            else $CD_SALARY -= $CD_EXTRA_SALARY;
                                    } else $CD_SALARY = $CD_SALARY_CEIL;
                                    if ($CD_SALARY < 0) echo "เกิดข้อผิดพลาด เงินเดือนไม่ถูกต้อง $PER_NAME $PER_SURNAME<br>";
                                    if($SALARY_FLAG != 'Y') $CD_SALARY = $CD_PERCENT = $CD_EXTRA_SALARY = 0;
                                    if($debug==1){echo __LINE__.':'.$CD_PERCENT.'<br>';}
                                    
                                    //if($CD_PERCENT){ /*เดิม*/
                                        $cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                                                        CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                                                values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY,$CD_PERCENT, 
                                                                        $CD_EXTRA_SALARY,$SESS_USERID,'$UPDATE_DATE', $CD_MIDPOINT) ";
                                        if($debug==1){echo __LINE__.':'.$cmd.'<br>';}
                                        $db_dpis1->send_cmd($cmd);
                                        //$db_dpis1->show_error();
                                        $CD_ID++;
                                    //}/*เดิม*/
                                }
                            }//foreach
			}//while
		}else{
			$data = $db_dpis->get_array();
			$CP_NAME = $data[CP_NAME];
			$err_text = "ชื่อข้อมูลซ้ำ";
		} // endif
                
	}

	if($command == "UPDATE" && trim($CP_ID) ){
			if (!$search_org_id) $search_org_id = "NULL";
			if (!$search_org_id_1) $search_org_id_1 = "NULL";
			
			$HOLD_SALARY_STR = str_replace(",","",$HOLD_SALARY);
			//$QTY_KEY_SUM_STR = implode(':',$QTY_KEY_SUM);
			foreach($QTY_KEY_SUM as $key => $value) $QTY_KEY_SUM_STR .= "$key=$value:";
			$QTY_KEY_SUM_STR = substr($QTY_KEY_SUM_STR,0,-1);
			
			//$SALARY_KEY_SUM_STR = implode(':',$SALARY_KEY_SUM);
			foreach($SALARY_KEY_SUM as $key => $value) $SALARY_KEY_SUM_STR .= "$key=$value:";
			$SALARY_KEY_SUM_STR = substr($SALARY_KEY_SUM_STR,0,-1);
			
			$cmd = " update PER_COMPENSATION_TEST set 
								CP_CYCLE=$CP_CYCLE,
								CP_NAME='$CP_NAME', 
								CP_BUDGET=$CP_BUDGET, 
								CP_RESULT=$CP_RESULT, 
								SF_CODE_O='".$SF_CODE_O . "#". implode(':',$val_sf_code[O])."',
								SF_CODE_K='".$SF_CODE_K . "#". implode(':',$val_sf_code[K])."',
								SF_CODE_D='".$SF_CODE_D . "#". implode(':',$val_sf_code[D])."',
								SF_CODE_M='".$SF_CODE_M . "#". implode(':',$val_sf_code[M])."',
								O_QTY='$O_QTY_TOTAL',
								O_SALARY='$O_SALARY_TOTAL',
								K_QTY='$K_QTY_TOTAL',
								K_SALARY='$K_SALARY_TOTAL',
								D_QTY='$D_QTY_TOTAL',
								D_SALARY='$D_SALARY_TOTAL',
								M_QTY='$M_QTY_TOTAL',
								M_SALARY='$M_SALARY_TOTAL',
								SUM_QTY='$QTY_KEY_SUM_STR',
								SUM_SALARY='$SALARY_KEY_SUM_STR',
								HOLD_SALARY='$HOLD_SALARY_STR',
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' ,
								ORG_ID_1=$search_org_id_1,
								PER_TYPE=$search_per_type
							 where CP_ID=$CP_ID ";
			$db_dpis->send_cmd($cmd);
                        if($debug==1){echo 'Ln:'.__LINE__.' <pre>'.$cmd.'<br>';}
//			echo "<pre>$cmd<br>";
//			$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");
			
			// insert DTL
			unset($AL_NAME);
			$cmd = " select 	AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
							  from PER_ASSESS_LEVEL 
							  where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
							  order by AL_POINT_MIN DESC ";
			$db_dpis->send_cmd($cmd);
			while ($data = $db_dpis->get_array()) {
				$AL_CODE = $data[AL_CODE];
				$AL_CODE_JOIN .= $AL_CODE . ":";
				$AL_NAME[$AL_CODE] = $data[AL_NAME];
				$AL[$AL_CODE][MIN] = $data[AL_POINT_MIN];
				$AL[$AL_CODE][MAX] = $data[AL_POINT_MAX];
				$AL[$AL_CODE][PERCENT] = $data[AL_PERCENT];
			}
			// insert dtl
			$cmd = " delete from PER_COMPENSATION_TEST_DTL where CP_ID = $CP_ID ";
			$db_dpis1->send_cmd($cmd);
			
			$cmd = " select max(CD_ID) as MAX_ID from PER_COMPENSATION_TEST_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CD_ID = $data[MAX_ID] + 1;
			
			$cmd = " select a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, TOTAL_SCORE, POS_ID, a.PER_NAME, a.PER_SURNAME 
							from PER_PERSONAL a, PER_KPI_FORM b
                            where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE = '$CP_START_DATE' 
							$where
							and KF_END_DATE =  '$CP_END_DATE' and a.LEVEL_NO in ('".$_select_level_no_str."') 
							order by a.PER_ID " ;
			if($debug==1){echo __LINE__.':'.$cmd.'<br>';}
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) {
				$PER_ID = $data[PER_ID];
				$PER_SALARY = $data[PER_SALARY];
				$LEVEL_NO = $data[LEVEL_NO];
				$SALARY_FLAG = $data[SALARY_FLAG];
				$TOTAL_SCORE = $data[TOTAL_SCORE];
				$POS_ID = $data[POS_ID];
				$tmp_PER_SALARY = $PER_SALARY;
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];

				$cmd = " select LAYER_TYPE from PER_POSITION a, PER_LINE b where POS_ID = $POS_ID and a.PL_CODE = b.PL_CODE ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$LAYER_TYPE = $data1[LAYER_TYPE] + 0;
	
				foreach($AL as $al_code => $point_range) {
                                    if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
                                        $temp_level = $LEVEL_NO[0];

                                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/  
                                        //if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY <= $SALARY_POINT[$LEVEL_NO][full]) {
                                        if ($LAYER_TYPE==1 && ($LEVEL_NO == "O3" || $LEVEL_NO == "K5") && $PER_SALARY < $SALARY_POINT[$LEVEL_NO][full]) {
                                            $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][full];
                                            $SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][extra];
                                            $SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][extra1];
                                            $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][extra2];
                                        } else {
                                            $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                                            $SALARY_POINT_MID = $SALARY_POINT[$LEVEL_NO][mid];
                                            $SALARY_POINT_MID1 = $SALARY_POINT[$LEVEL_NO][mid1];
                                            $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
                                        }
                                        
                                        /*Release 5.2.1.6 Begin*/
                                        $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                                        //echo $LEVEL_NO.'='.$LAYER_SALARY_MAX.'<br>';
                                        if($LEVEL_NO == "O3" || $LEVEL_NO == "K5"){
                                            ///echo $PER_SALARY.'>='.$SALARY_POINT[$LEVEL_NO][full].'<br>';
                                           
                                            if( $PER_SALARY >=$SALARY_POINT[$LEVEL_NO][full]){
                                                $SALARY_POINT_MID2 = $SALARY_POINT[$LEVEL_NO][mid2];
                                                 ///echo $command.'='.$SALARY_POINT_MID2.'<br>';
                                            }
                                        }
                                        /*Release 5.2.1.6 End*/
                                        
                                         
                                        if($SALARY_POINT_MID > $PER_SALARY) {
                                            $PER_SALARY = $SALARY_POINT_MID1;
                                        } else {
                                            $PER_SALARY = $SALARY_POINT_MID2;
                                        }

                                        $CD_MIDPOINT = $PER_SALARY;
                                        $CD_PERCENT = $sf_code[$temp_level][$al_code];
                                        $CD_SALARY = round($PER_SALARY * (($sf_code[$temp_level][$al_code]/100)),2) ;
                                        $CD_SALARY_CEIL = (ceil($PER_SALARY * (($sf_code[$temp_level][$al_code]/100))  /10))*10 ;
                                        $CD_EXTRA_SALARY = 0;
                                        if ($tmp_PER_SALARY + $CD_SALARY_CEIL > $LAYER_SALARY_MAX) {
                                                $CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
                                                if ($tmp_PER_SALARY == $LAYER_SALARY_MAX) $CD_SALARY = 0;
                                                else $CD_SALARY -= $CD_EXTRA_SALARY;
                                        } else $CD_SALARY = $CD_SALARY_CEIL;
                                        if ($CD_SALARY < 0) echo "เกิดข้อผิดพลาด เงินเดือนไม่ถูกต้อง $PER_NAME $PER_SURNAME<br>";
                                    if($SALARY_FLAG != 'Y') $CD_SALARY = $CD_PERCENT = $CD_EXTRA_SALARY = 0;

                                        $cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                                                        CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                                                                        values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
                                                                        $CD_EXTRA_SALARY, $SESS_USERID, '$UPDATE_DATE', $CD_MIDPOINT) ";
                                        $db_dpis1->send_cmd($cmd);
                                      
    //							$db_dpis1->show_error();
                                        $CD_ID++;
                                    }
				}

			}    
			
	}
	
// ============================================================
	// เมื่อมีการยืนยันแบบทดสอบการบริหารค่าตอบแทน
	if( $command == "CONFIRM" && trim($CP_ID) ) {
		$cmd = " select 	CP_START_DATE, CP_END_DATE, CP_CYCLE, ORG_ID, ORG_ID_1 
						  from PER_COMPENSATION_TEST where	CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_START_DATE = $data[CP_START_DATE];
		$CP_END_DATE = $data[CP_END_DATE];
		$CP_CYCLE = $data[CP_CYCLE];
		$ORG_ID = $data[ORG_ID];
		$ORG_ID_1 = $data[ORG_ID_1];

		$cmd = " update PER_COMPENSATION_TEST set  
						  CP_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						  where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);		
//		$db_dpis->show_error();

		if ($ORG_ID_1) 
			$cmd = " update PER_COMPENSATION_TEST set CP_CONFIRM=0
							  where CP_ID!=$CP_ID and CP_START_DATE='$CP_START_DATE' and CP_END_DATE = '$CP_END_DATE' and
							  CP_CYCLE = $CP_CYCLE and ORG_ID = $ORG_ID and ORG_ID_1 = $ORG_ID_1 ";
		elseif ($ORG_ID)
			$cmd = " update PER_COMPENSATION_TEST set CP_CONFIRM=0
							  where CP_ID!=$CP_ID and CP_START_DATE='$CP_START_DATE' and CP_END_DATE = '$CP_END_DATE' and
							  CP_CYCLE = $CP_CYCLE and ORG_ID = $ORG_ID and ORG_ID_1 is NULL ";
		else
			$cmd = " update PER_COMPENSATION_TEST set CP_CONFIRM=0
							  where CP_ID!=$CP_ID and CP_START_DATE='$CP_START_DATE' and CP_END_DATE = '$CP_END_DATE' and
							  CP_CYCLE = $CP_CYCLE and ORG_ID is NULL and ORG_ID_1 is NULL ";
		$db_dpis->send_cmd($cmd);		
//		$db_dpis->show_error();

	}		// 	if( $command == "CONFIRM" && trim($CP_ID) ) 	
	
	if($command == "DELETE" && trim($CP_ID)){
		$cmd = " delete from PER_COMPENSATION_TEST_DTL where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $CP_ID ." : ".$CP_NAME." ".$CP_CYCLE." ".$CP_BUDGET."]");
	}
	
	if($UPD || $VIEW){
		
		$cmd = " select 	CP_NAME, CP_START_DATE, CP_END_DATE, CP_CYCLE, CP_BUDGET, SF_CODE_O, SF_CODE_K, SF_CODE_D, SF_CODE_M, 
						DEPARTMENT_ID, ORG_ID, ORG_ID_1, PER_TYPE
				  		 from 		PER_COMPENSATION_TEST
						 where 	CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CP_NAME = trim($data[CP_NAME]);
		$CP_START_DATE = trim($data[CP_START_DATE]);
		$CP_END_DATE = trim($data[CP_END_DATE]);
		$CP_CYCLE = $data[CP_CYCLE];
		$CP_BUDGET = $data[CP_BUDGET];
		$SF_CODE_O = trim($data[SF_CODE_O]);
		$SF_CODE_K = trim($data[SF_CODE_K]);
		$SF_CODE_D = trim($data[SF_CODE_D]);
		$SF_CODE_M = trim($data[SF_CODE_M]);
		$search_per_type = $data[PER_TYPE];
		if (!$search_department_id) {
			$search_department_id = $data[DEPARTMENT_ID];
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $search_department_id ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$search_department_name = $data1[ORG_NAME];
			$search_ministry_id = $data1[ORG_ID_REF];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_ministry_id ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$search_ministry_name = $data1[ORG_NAME];
		}
		$search_org_id = $data[ORG_ID];
		if ($search_org_id) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_org_id ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$search_org_name = $data1[ORG_NAME];
		}
		$search_org_id_1 = $data[ORG_ID_1];
		if ($search_org_id_1) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_org_id_1 ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$search_org_name_1 = $data1[ORG_NAME];
		}
		unset($_select_level_no);
		$cmd = " select 	DISTINCT LEVEL_NO from PER_COMPENSATION_TEST_DTL where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$_select_level_no[] = $data[LEVEL_NO];
		}
	
		//print_r($_select_level_no);
		// create init div 
		unset($init_al_code);unset($init_al_name);
		$CP_YEAR = substr($CP_END_DATE,0,4) + 543;
		$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
						order by AL_POINT_MIN DESC ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$init_al_code[] = $data[AL_CODE] . ",";
			$init_al_name[] =  "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
		}

		list($SF_CODE_O,$list_code_o) = explode('#',$SF_CODE_O);
		list($SF_CODE_K,$list_code_k) = explode('#',$SF_CODE_K);
		list($SF_CODE_D,$list_code_d) = explode('#',$SF_CODE_D);
		list($SF_CODE_M,$list_code_m) = explode('#',$SF_CODE_M);
		
		$INIT_O = $list_code_o . ":";
		$INIT_K = $list_code_k . ":";
		$INIT_D = $list_code_d . ":";
		$INIT_M = $list_code_m . ":";

		$temp_str = str_replace(":","'>  ",$INIT_O);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_o = str_replace("[]","_o",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_K);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_k = str_replace("[]","_k",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_D);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_d = str_replace("[]","_d",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_M);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_m = str_replace("[]","_m",$temp_1);

		$init_div_o = $temp_o;
		$init_div_k = $temp_k;
		$init_div_d = $temp_d;
		$init_div_m = $temp_m;
	} 
	if($debug==1){echo __LINE__.':'.$Testing.'<br>';}
	// echo $UPD."||".$VIEW." && ".$Testing;
	// if( !$UPD || !$VIEW || !$Testing){
	// 	$_select_level_no = array("O1","O2","O3","O4","K1","K2","K3","K4","K5","D1","D2","M1","M2");
    //             if($debug==1){echo __LINE__.':<pre>'.  print_r($_select_level_no).'<br>';}
	// }
	// print_r($_select_level_no);
	if (!$CP_YEAR) 
		if(date("Y-m-d") <= date("Y")."-10-01") $CP_YEAR = date("Y") + 543;
		else $CP_YEAR = (date("Y") + 543) + 1;
	$CP_START_DATE_1 = "01/10/". ($CP_YEAR - 1);
	$CP_END_DATE_1 = "31/03/". $CP_YEAR;
	$CP_START_DATE_2 = "01/04/". $CP_YEAR;
	$CP_END_DATE_2 = "30/09/". $CP_YEAR;
	

	//___echo "$UPD && $DEL && $VIEW && $err_text && $Testing";
	if( (!$UPD && !$DEL && !$VIEW && !$err_text && !$Testing) ){
		$_select_level_no = array("O1","O2","O3","O4","K1","K2","K3","K4","K5","D1","D2","M1","M2");
    
		unset($O_QTY_TOTAL);
		unset($O_SALARY_TOTAL);
		unset($K_QTY_TOTAL);
		unset($K_SALARY_TOTAL);
		unset($D_QTY_TOTAL);
		unset($D_SALARY_TOTAL);
		unset($M_QTY_TOTAL);
		unset($M_SALARY_TOTAL);
		unset($QTY_KEY_SUM_STR);
		unset($SALARY_KEY_SUM_STR);
		
		unset($QTY_GRANDTOTAL);
		unset($SALARY_GRANDTOTAL);
		unset($CP_RESULT);
		
//		unset($CP_CYCLE);
		unset($CP_ID);
		unset($CP_NAME);
		unset($CP_BUDGET);
		unset($SF_CODE_O);unset($SF_CODE_K);unset($SF_CODE_D);unset($SF_CODE_M);
		unset($_POST);
		if($SESS_USERGROUP_LEVEL == 2){ //2 =จังหวัด
			unset($search_ministry_id);
			unset($search_ministry_name);
			unset($search_department_id);
			unset($search_department_name);
		}
		if($SESS_USERGROUP_LEVEL < 5){ 
			unset($search_org_id);
			unset($search_org_name);
			unset($search_org_id_1);
			unset($search_org_name_1);
		}
	} // end if
	
	// create init div 
	unset($init_al_code);unset($init_al_name);
	$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL 
					where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' $where_org 
					order by AL_POINT_MIN DESC ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$init_al_code[] = $data[AL_CODE] . ",";
		$init_al_name[] = "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
	}
	
	//echo "$command  && $command  && $VIEW && $UPD && $Testing";
	
	 // Press Testing
	if($Testing) {
		//unset($div_sf_code_o);
                
//	    unset($div_sf_code_k);
//		unset($div_sf_code_d);
//		unset($div_sf_code_m);
		$cmd = " select AL_CODE, AL_NAME, AL_PERCENT from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' 
						$where_org 
						order by AL_POINT_MIN DESC ";
		//echo "Testing php ~> $cmd<br>";
                if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) {
			$AL_CODE = $data[AL_CODE];
			$AL_NAME[$AL_CODE] = $data[AL_NAME];
	//		$temp_al_code[] = $data[AL_CODE] . ",";
			$temp_xx_code = $data[AL_CODE];
			$temp_o = "<input name=code_o_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_k = "<input name=code_k_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_d = "<input name=code_d_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_m = "<input name=code_m_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$div_sf_code_o[$temp_xx_code] = $temp_o;
			$div_sf_code_k[$temp_xx_code] = $temp_k;
			$div_sf_code_d[$temp_xx_code] = $temp_d;
			$div_sf_code_m[$temp_xx_code] = $temp_m;
		}

		// ตรงนี้ทำให้สร้าง text input ซ้ำขึ้นมา
		foreach($_POST as $key => $percent) {
			if(substr($key,0,4) == 'code') {
				list($strcode,$scode,$code) = explode('_',$key);
				//$code = strtoupper($code);
				switch($scode) {
					case 'o' : 					
									$temp_o = "<input name=code_o_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
									$div_sf_code_o[$code] = $temp_o;
									break;
					case 'k' :					
									$temp_k = "<input name=code_k_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
									$div_sf_code_k[$code] = $temp_k;
									break;
					case 'd' :					
									$temp_d = "<input name=code_d_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
									$div_sf_code_d[$code] = $temp_d;
									break;
					case 'm' :					
									$temp_m = "<input name=code_m_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
									$div_sf_code_m[$code] = $temp_m;
									break;
				} // switch
			}
		} // foreach

	} elseif($UPD || $VIEW) {
		error_reporting(0);
		unset($div_sf_code_o);
		unset($div_sf_code_k);
		unset($div_sf_code_d);
		unset($div_sf_code_m);
		$cmd = "select SF_CODE_O,SF_CODE_K,SF_CODE_D,SF_CODE_M from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$xx_code_o_array = explode(':',substr($data['SF_CODE_O'],1));
		$xx_code_k_array = explode(':',substr($data['SF_CODE_K'],1));
		$xx_code_d_array = explode(':',substr($data['SF_CODE_D'],1));
		$xx_code_m_array = explode(':',substr($data['SF_CODE_M'],1));
		
		foreach($xx_code_o_array as $key => $value) {
			$temp_array = explode(",",$value);
			$code = $temp_array[0];
			$percent = $temp_array[1];
			$temp_o = "<input name=code_o_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
			$div_sf_code_o[$code] = $temp_o;
		}

		foreach($xx_code_k_array as $key => $value) {
			$temp_array = explode(",",$value);
			$code = $temp_array[0];
			$percent = $temp_array[1];
			$temp_k = "<input name=code_k_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
			$div_sf_code_k[$code] = $temp_k;
		}
		foreach($xx_code_d_array as $key =>  $value) {
			$temp_array = explode(",",$value);
			$code = $temp_array[0];
			$percent = $temp_array[1];
			$temp_d = "<input name=code_d_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
			$div_sf_code_d[$code] = $temp_d;
		}
		foreach($xx_code_m_array as $key => $value) {
			$temp_array = explode(",",$value);
			$code = $temp_array[0];
			$percent = $temp_array[1];
			$temp_m = "<input name=code_m_".$code." type=text class=textbox size=5 maxlength=6 value='".$percent."'> ";
			$div_sf_code_m[$code] = $temp_m;
		}
	} else {
		unset($div_sf_code_o);
		unset($div_sf_code_k);
		unset($div_sf_code_d);
		unset($div_sf_code_m);
		$cmd = " select AL_CODE, AL_NAME, AL_PERCENT from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1 and AM_CODE <> '1' 
						$where_org 
						order by AL_POINT_MIN DESC ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) {
	//		$temp_al_code[] = $data[AL_CODE] . ",";
			$temp_xx_code = $data[AL_CODE];
			$temp_o = "<input name=code_o_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_k = "<input name=code_k_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_d = "<input name=code_d_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_m = "<input name=code_m_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$div_sf_code_o[$temp_xx_code] = $temp_o;
			$div_sf_code_k[$temp_xx_code] = $temp_k;
			$div_sf_code_d[$temp_xx_code] = $temp_d;
			$div_sf_code_m[$temp_xx_code] = $temp_m;
		}
	} 
?>