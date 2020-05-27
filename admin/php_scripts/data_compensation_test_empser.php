<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	//		CP_YEAR search_cp_year
	
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
			$search_org_id_11 = $ORG_ID_1;
			$search_org_name_11 = $ORG_NAME_1;
			break;
	} // end switch case

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	$search_per_type = (isset($search_per_type))?  $search_per_type : 3;
	if(!$search_cp_confirm) $search_cp_confirm = array(1,0);
	$CP_CYCLE =  2;		//รอบเดียวเท่านั้น
	$CP_START_DATE = ($CP_YEAR - 1 - 543) ."-10-01";
	$CP_END_DATE = ($CP_YEAR - 543) ."-09-30";
	
	if(!$CP_YEAR)	$CP_YEAR = $KPI_BUDGET_YEAR;
	if(!$search_cp_year)	$search_cp_year = $KPI_BUDGET_YEAR;
	if(!trim($CP_YEAR)){
		if(date("Y-m-d") <= date("Y")."-10-01") $CP_YEAR = date("Y") + 543;
		else $CP_YEAR = (date("Y") + 543) + 1;
	} // end if
	
	$_select_level_no_str = implode("','",$_select_level_no);
	
	if($search_department_id && $search_department_id != 'NULL'){
		$where = " and b.DEPARTMENT_ID = $search_department_id "; 
        $where_dpm = " and b.DEPARTMENT_ID = $search_department_id ";
	}elseif($search_ministry_id){
		$where = "and b.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
        $where_dpm = "and b.DEPARTMENT_ID in (select ORG_ID from PER_ORG where ORG_ID_REF = $search_ministry_id) ";
	}
	if($search_org_id && $search_org_id != 'NULL') 
		if ($select_org_structure==0) $where .= " and b.org_id_salary = $search_org_id ";
		elseif ($select_org_structure==1) $where .= " and b.org_id_ass = $search_org_id ";
	if($search_org_id_1 && $search_org_id_1 != 'NULL') 
		if ($select_org_structure==0) $where .= " and b.org_id_1_salary = $search_org_id_1 ";
		elseif ($select_org_structure==1) $where .= " and b.org_id_1_ass = $search_org_id_1 ";
	if($search_org_id && $search_org_id != 'NULL') $where_org = " and DEPARTMENT_ID = $search_department_id and org_id = $search_org_id ";
	elseif($search_department_id && $search_department_id != 'NULL') $where_org = " and (DEPARTMENT_ID = $search_department_id and ORG_ID IS NULL) ";
	elseif($search_ministry_id) $where_org = " and (DEPARTMENT_ID IS NULL and ORG_ID IS NULL) ";
	else $where_org = " and (DEPARTMENT_ID IS NULL and ORG_ID IS NULL) ";
	//and AM_CODE <> '1'
	$cmd = " select AL_CODE from PER_ASSESS_LEVEL 
					where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
					order by AL_POINT_MIN DESC ";
	$count_data = $db_dpis->send_cmd($cmd);
	if (!$count_data) $where_org = " and DEPARTMENT_ID = '$search_department_id' and org_id is NULL ";

	$array_xx = array('O'=>'o','K'=>'k');
	if(empty($Testing)) {
            
            
            
		foreach($array_xx as $key => $value) {
			//and AM_CODE <> '1'
			$cmd = " select AL_CODE from PER_ASSESS_LEVEL 
							where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
							order by AL_POINT_MIN DESC ";
			//echo $cmd;
                        $db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) {	
				$temp_code = $data[AL_CODE];
				$tmp_arr = "code_".$value."_".$temp_code ; 
				$code_arr = $$tmp_arr;
				$sf_code[$key][$temp_code] = $code_arr;
				$val_sf_code[$key][$temp_code] = "$temp_code,$code_arr";
			}	
		}
	} elseif($Testing == '1') {
            
            /* A
             * ตรวจสอบว่าข้อมูลสำหรับทุกคนที่ระบุว่าเลื่อนเงินเดือนต้องมีคะแนนประเมินครบทั้ง 2 รอบ
             *ถ้าไม่เป็นไปตามเงือนไข => alert('') แล้วจบ
             */
            $CP_START_DATE_Chk = ($CP_YEAR - 1 - 543) ."-10-01";
            $CP_END_DATE_Chk = ($CP_YEAR - 543) ."-10-01"; // ทั้งปี งบประมาณ
            
            //$search_department_id
            //if(empty($search_org_id)){$search_org_id=$search_department_id;}
            $conditionORG="";
            /*if(!empty($search_org_id)){
                $conditionORG=" AND a.ORG_ID = $search_org_id";//$search_org_id=$search_department_id;
                
            }*/
            if($search_org_id && $search_org_id != 'NULL') 
                /*เดิม*/
		/*if ($select_org_structure==0) $conditionORG = " and a.org_id_salary = $search_org_id ";
		elseif ($select_org_structure==1) $conditionORG = " and a.org_id_ass = $search_org_id ";*/
                
                /*http://dpis.ocsc.go.th/Service/node/1570*/
            $conditionORG = " and a.org_id_salary = $search_org_id";
            
            $incomplete = "TRUE";
            $notInPERID = "0";
            /*$cmdCycle ="SELECT count(*) AS CNTCYCLE  FROM (
                        SELECT distinct a.per_id, sum(1) as cntcycle  
                        FROM per_kpi_form a , PER_PERSONAL b
                        WHERE a.PER_ID=b.$PER_ID 
                          AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1
                          AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
                          AND a.kf_cycle IN(1,2) AND a.ORG_ID = $search_org_id
                        GROUP BY a.per_id
                      ) WHERE cntcycle=1 ";*/
		/*--------------------------------------------------------------------------------------------*/
//            $cmdCycle ="SELECT count(a.per_id) AS CNTCYCLE
//                        FROM per_kpi_form a , PER_PERSONAL b 
//                        WHERE a.PER_ID=b.PER_ID AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                          AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                          AND a.PER_ID in (
//                                          SELECT distinct a.per_id  
//                                          FROM per_kpi_form a , PER_PERSONAL b 
//                                          WHERE a.PER_ID=b.PER_ID AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                                            AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                                            AND a.kf_cycle IN(1,2) 
//                                            ".$conditionORG."
//                                          GROUP BY a.per_id
//                                          HAVING count(a.per_id)=1
//                          )
//                          AND a.kf_cycle=1 ";
//          // echo "<pre>".$cmdCycle; //$search_department_id
//            $db_dpis->send_cmd($cmdCycle);
//            $dataCycle = $db_dpis->get_array();
//            $CNTCYCLE = $dataCycle[CNTCYCLE];
//            
//            $ListNameError = "";
//            if($CNTCYCLE >0){
//               
//               $incomplete = "FALSE";
//               
//            $cmdList = "SELECT a.kf_cycle ,p.pn_shortname,b.PER_NAME,b.per_surname,a.salary_flag,a.per_id
//                        FROM per_kpi_form a , PER_PERSONAL b , per_prename p
//                        WHERE a.PER_ID=b.PER_ID AND b.pn_code = p.pn_code AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                        AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                        AND a.PER_ID in (
//                                        SELECT distinct a.per_id  
//                                        FROM per_kpi_form a , PER_PERSONAL b 
//                                        WHERE a.PER_ID=b.PER_ID AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                                          AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                                          AND a.kf_cycle IN(1,2) 
//                                          ".$conditionORG."
//                                        GROUP BY a.per_id
//                                        HAVING count(a.per_id)=1
//                        )
//                        AND a.kf_cycle=1";
//                $db_dpis->send_cmd($cmdList);
//				echo "<pre>".$cmdList; //$search_department_id
//                while($dataList = $db_dpis->get_array()) {
//                    $str="ไม่เลื่อน";
//                    if($dataList[SALARY_FLAG]=="Y"){$str="เลื่อน";}
//                    $ListNameError .="<br>".$dataList[PN_SHORTNAME].$dataList[PER_NAME]." ".$dataList[PER_SURNAME]." => พบเฉพาะข้อมูลคะแนนรอบการประเมินที่ ".$dataList[KF_CYCLE]." [$str]<br>";
//                    //$xx_level_no = $dataList[LEVEL_NO];
//                    //$SALARY_POINT[$xx_level_no][max]  = $data[LAYER_SALARY_MAX];
//                    $notInPERID .= ",".$dataList[PER_ID];
//                }
//                
//            }
//            
//                // ตรวจสอบมีคะแนนประเมินรอบสอง สถานะเลื่อน
//                $cmdList = "SELECT a.kf_cycle ,p.pn_shortname,b.PER_NAME,b.per_surname,a.salary_flag,a.per_id
//                         FROM per_kpi_form a , PER_PERSONAL b , per_prename p
//                         WHERE a.PER_ID=b.PER_ID AND b.pn_code = p.pn_code AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                         AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                         AND a.PER_ID in (
//                                         SELECT distinct a.per_id  
//                                         FROM per_kpi_form a , PER_PERSONAL b 
//                                         WHERE a.PER_ID=b.PER_ID AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
//                                           AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) < '$CP_END_DATE_Chk') 
//                                           AND a.kf_cycle IN(1,2) 
//                                           ".$conditionORG."
//                                         GROUP BY a.per_id
//                                         HAVING count(a.per_id)=1
//                         )
//                         AND a.kf_cycle=2 AND a.salary_flag='Y' ";
//                $db_dpis->send_cmd($cmdList);
//                 while($dataList = $db_dpis->get_array()) {
//                     $incomplete = "FALSE";
//                     $str="ไม่เลื่อน";
//                     if($dataList[SALARY_FLAG]=="Y"){$str="เลื่อน";}
//                     $ListNameError .=$dataList[PN_SHORTNAME].$dataList[PER_NAME]." ".$dataList[PER_SURNAME]." => พบข้อมูลคะแนนรอบประเมินรอบที่ ".$dataList[KF_CYCLE]." [$str แต่ไม่พบข้อมูลคะแนนรอบการประเมินที่ 1]<br>";
//                     //$xx_level_no = $dataList[LEVEL_NO];
//                     //$SALARY_POINT[$xx_level_no][max]  = $data[LAYER_SALARY_MAX];
//                     $notInPERID .= ",".$dataList[PER_ID];
//                 }
//		 $incomplete = "TRUE";
 	/*--------------------------------------------------------------------------------------------*/
	//หาPER_ID ไปหา แฟ้มการประเมิน	
		$ListNameError = "";
		$cmd = "SELECT distinct a.per_id  
                                FROM per_kpi_form a , PER_PERSONAL b 
                                WHERE a.PER_ID=b.PER_ID AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
                                AND (SUBSTR(a.KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(a.KF_END_DATE, 1, 10) <= '$CP_END_DATE_Chk') 
                                AND a.kf_cycle IN(1,2) 
                                ".$conditionORG."
                                GROUP BY a.per_id ";
        $db_dpis->send_cmd($cmd);
		//echo "<pre>".$cmd;
		while($data = $db_dpis->get_array()){
		$per_id_chk = $data[PER_ID];
		$cmd = "SELECT count(per_id) as per_id
                                FROM per_kpi_form 
                                WHERE PER_ID=$per_id_chk 
                                AND (SUBSTR(KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') 
                                AND (SUBSTR(KF_END_DATE, 1, 10) <= '$CP_END_DATE_Chk') and kf_cycle = 1" ;
		$db_dpis1->send_cmd($cmd);
		//echo "<pre>".$cmd;
		$data2 = $db_dpis1->get_array();
		$cnt_kf_cycle1 = $data2[PER_ID];
            
        $cmd = "SELECT count(per_id) as per_id 
                                FROM per_kpi_form 
                                WHERE PER_ID=$per_id_chk 
                                AND (SUBSTR(KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') 
                                AND (SUBSTR(KF_END_DATE, 1, 10) <= '$CP_END_DATE_Chk') and kf_cycle = 2 and org_id_salary = $search_org_id" ;
		$db_dpis1->send_cmd($cmd);
          //  echo "<pre>".$cmd;
		$data2 = $db_dpis1->get_array();
		$cnt_kf_cycle2 = $data2[PER_ID];    
            
            
            
            $dug=0;
        if($cnt_kf_cycle1 == 0 || $cnt_kf_cycle2 == 0){
				 $incomplete = "FALSE";
					$cmd = "SELECT a.kf_cycle ,p.pn_shortname,b.PER_NAME,b.per_surname,a.salary_flag,a.per_id
								FROM per_kpi_form a , PER_PERSONAL b , per_prename p
								WHERE a.PER_ID=b.PER_ID AND b.pn_code = p.pn_code AND b.PER_TYPE = '".$search_per_type."' AND b.per_status=1 
								AND (SUBSTR(KF_START_DATE, 1, 10) >= '$CP_START_DATE_Chk') AND (SUBSTR(KF_END_DATE, 1, 10) <= '$CP_END_DATE_Chk') 
								AND a.PER_ID = $per_id_chk";
					$db_dpis2->send_cmd($cmd);
                    if($dug == 1){echo __LINE__."<pre>$cmd<br>";}
					$dataList = $db_dpis2->get_array();
					$salary_flag_cn = $dataList[SALARY_FLAG];
//                    $str = "ไม่เลื่อน";
                    $cyc_cnt = 1;
//                    if($salary_flag_cn=="Y")$str = "เลื่อน";
					$notInPERID .= ",".$dataList[PER_ID];
                    if($dataList[KF_CYCLE]==1){
                     $cyc_cnt = 2;
                    }
				   $ListNameError .="<br>".$dataList[PN_SHORTNAME].$dataList[PER_NAME]." ".$dataList[PER_SURNAME]." => พบเฉพาะข้อมูลคะแนนรอบการประเมินที่ ".$dataList[KF_CYCLE]." [ แต่ไม่พบข้อมูลคะแนนรอบการประเมินที่ $cyc_cnt]";
                   //$ListNameError .=$dataList[PN_SHORTNAME].$dataList[PER_NAME]." ".$dataList[PER_SURNAME]." => พบข้อมูลคะแนนรอบประเมินรอบที่ ".$dataList[KF_CYCLE]." [$str แต่ไม่พบข้อมูลคะแนนรอบการประเมินที่ 1]<br>";
			}	
	
		}	
		
		foreach($_POST as $key => $value) {
			if(substr($key,0,4) == 'code') {
				list($strcode,$code,$id) = explode('_',$key);
				$code = strtoupper($code);
				$sf_code[$code][$id] = $value;
				$val_sf_code[$code][$id] = "$id,$value";
			}
		}
	}
	
	$cmd = " select LEVEL_NO, LAYER_SALARY_MAX
					 from PER_LAYER where LAYER_TYPE = 0 and LAYER_NO = 0 ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$xx_level_no = $data[LEVEL_NO];
		$SALARY_POINT[$xx_level_no][max]  = $data[LAYER_SALARY_MAX];
	}

//	echo $command . "+++";  
    if($command == "ADD"){
            if (!$search_org_id) $search_org_id = "NULL";
            if (!$search_org_id_1) $search_org_id_1 = "NULL";
        
        if($CP_ID){
            $cmd = " select CP_ID,CP_NAME from PER_COMPENSATION_TEST where CP_ID = $CP_ID ";
            $count_duplicate = $db_dpis->send_cmd($cmd);
        }
        if($count_duplicate <= 0){
            $cmd = " select max(CP_ID) as MAX_ID from PER_COMPENSATION_TEST ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $CP_ID = $data[MAX_ID] + 1;

            $HOLD_SALARY_STR = str_replace(",","",$HOLD_SALARY);
            //$QTY_KEY_SUM_STR = implode(':',$QTY_KEY_SUM);
            foreach($QTY_KEY_SUM as $key => $value) $QTY_KEY_SUM_STR .= "$key=$value:";
            $QTY_KEY_SUM_STR = substr($QTY_KEY_SUM_STR,0,-1);

            //$SALARY_KEY_SUM_STR = implode(':',$SALARY_KEY_SUM);
            foreach($SALARY_KEY_SUM as $key => $value) $SALARY_KEY_SUM_STR .= "$key=$value:";
            $SALARY_KEY_SUM_STR = substr($SALARY_KEY_SUM_STR,0,-1);

            // insert header
            //if(!$CP_RESULT) $CP_RESULT = 0;
            $cmd = " insert into PER_COMPENSATION_TEST (CP_ID, CP_NAME, CP_DATE, CP_CYCLE, CP_START_DATE, 
                                CP_END_DATE,	CP_BUDGET, ORG_ID, DEPARTMENT_ID, SF_CODE_O, SF_CODE_K, 
                                CP_RESULT, O_QTY, O_SALARY, K_QTY, K_SALARY, SUM_QTY, 
                                SUM_SALARY, HOLD_SALARY, UPDATE_USER, UPDATE_DATE, CP_CONFIRM, ORG_ID_1, PER_TYPE) 
                    values	($CP_ID, '$CP_NAME', '$UPDATE_DATE', $CP_CYCLE, '$CP_START_DATE', '$CP_END_DATE', $CP_BUDGET, 
                                $search_org_id, $search_department_id, '".$SF_CODE_O . "#". implode(':',$val_sf_code[O])."',
                                '".$SF_CODE_K . "#". implode(':',$val_sf_code[K])."', $CP_RESULT, '$O_QTY_TOTAL', '$O_SALARY_TOTAL', 
                                '$K_QTY_TOTAL', '$K_SALARY_TOTAL', '$QTY_KEY_SUM_STR', '$SALARY_KEY_SUM_STR', '$HOLD_SALARY', $SESS_USERID, 
                                '$UPDATE_DATE', 0, $search_org_id_1, $search_per_type) ";
            $db_dpis->send_cmd($cmd);
            //$db_dpis->show_error();
            //echo "<pre>".$cmd."<br>";

            insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");

            unset($AL_NAME);
			//and AM_CODE <> '1'
            $cmd = " select AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
                    from PER_ASSESS_LEVEL
                    where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
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
            $cmd = " select max(CD_ID) as MAX_ID from PER_COMPENSATION_TEST_DTL ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $CD_ID = $data[MAX_ID] + 1;

            
            // เดิม...
            /*$cmd = " select a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, SUM(TOTAL_SCORE) AS TOTAL_SCORE, a.PER_NAME, a.PER_SURNAME 
                    from PER_PERSONAL a, PER_KPI_FORM b
                    where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' 
                        and KF_END_DATE <=  '$CP_END_DATE' and a.LEVEL_NO in ('".$_select_level_no_str."') $where
                    group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, a.PER_NAME, a.PER_SURNAME
                    order by a.PER_ID " ;*/
            //Modidy By Pitak 22-10-2015 Begim
           // echo $not_per_id;
                        
            //Modidy By kong 12-11-2018 
            $cmd = "with tb_c1 as (
                    select PER_SALARY, PER_ID, LEVEL_NO,  PER_NAME, PER_SURNAME ,CNTROW,TOTAL_SCORE,KF_CYCLE
                                        from (
                              select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO, 
                                        a.PER_NAME, a.PER_SURNAME,sum(1) as CNTROW,b.TOTAL_SCORE,b.KF_CYCLE
                                 from PER_PERSONAL a, PER_KPI_FORM b 
                                 where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' and 
                       KF_END_DATE <=  '$CP_END_DATE' and 
                       a.LEVEL_NO in ('".$_select_level_no_str."') $where_dpm   and b.KF_CYCLE = 1
                       group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, a.PER_NAME, a.PER_SURNAME ,b.TOTAL_SCORE,b.KF_CYCLE

                  ) ),tb_c2 as (select PER_SALARY, PER_ID, LEVEL_NO,  PER_NAME, PER_SURNAME ,CNTROW,TOTAL_SCORE,KF_CYCLE
                                        from (
                              select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO,
                                        a.PER_NAME, a.PER_SURNAME,sum(1) as CNTROW,b.TOTAL_SCORE,b.KF_CYCLE
                                 from PER_PERSONAL a, PER_KPI_FORM b
                                 where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' and 
                       KF_END_DATE <=  '$CP_END_DATE' and 
                       a.LEVEL_NO in ('".$_select_level_no_str."') $where and b.KF_CYCLE = 2
                       group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, a.PER_NAME, a.PER_SURNAME, b.TOTAL_SCORE,b.KF_CYCLE

                  ))
                  select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO,  a.PER_NAME, a.PER_SURNAME ,(a.CNTROW + nvl(b.CNTROW,0)) as CNTROW, ((NVL(a.TOTAL_SCORE,0) + NVL(b.TOTAL_SCORE,0))/2)  as TOTAL_SCORE  
                  from tb_c2 a  LEFT JOIN tb_c1 b on (a.PER_ID=b.PER_ID) WHERE (a.CNTROW + nvl(b.CNTROW,0)) = 2
                  order by a.PER_ID ";
             
    //Modidy By kong 12-11-2018 END.
        //    echo "<pre>".$cmd."<br>";
            $db_dpis->send_cmd($cmd);
            while($data = $db_dpis->get_array()) {
                $PER_ID = $data[PER_ID];
                $PER_SALARY = $data[PER_SALARY];
                $LEVEL_NO = $data[LEVEL_NO];
                //หาค่า flag รอบสอง
                $cmdCYCLE1 = " select SALARY_FLAG, SALARY_REMARK1 
                                     from PER_KPI_FORM 
                                     where PER_ID = $PER_ID AND KF_CYCLE=1
                                         and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                $db_dpis1->send_cmd($cmdCYCLE1);
                $dataCYCLE1 = $db_dpis1->get_array();
                $CYCLE_SALARY_FLAG1 = trim($dataCYCLE1[SALARY_FLAG]); 			 
                $CYCLE_SALARY_REMARK1 = trim($dataCYCLE1[SALARY_REMARK1]); 

                $cmdCYCLE2 = " select SALARY_FLAG, SALARY_REMARK1 
                                     from PER_KPI_FORM 
                                     where PER_ID = $PER_ID AND KF_CYCLE=2
                                         and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
                $db_dpis1->send_cmd($cmdCYCLE2);
                $dataCYCLE2 = $db_dpis1->get_array();
                $CYCLE_SALARY_FLAG2 = trim($dataCYCLE2[SALARY_FLAG]); 			 
                $CYCLE_SALARY_REMARK2 = trim($dataCYCLE2[SALARY_REMARK1]);
                $CNT_CYCLE = 1;
                if(!empty($CYCLE_SALARY_FLAG1) && empty($CYCLE_SALARY_FLAG2)){ //ยังมีรอบ 1 รอบเดียว
                   $SALARY_FLAG =$CYCLE_SALARY_FLAG1;
                }
                if(empty($CYCLE_SALARY_FLAG1) && !empty($CYCLE_SALARY_FLAG2)){ //มีรอบ 2 รอบเดียว
                    $SALARY_FLAG = $CYCLE_SALARY_FLAG2;
                }
                if(!empty($CYCLE_SALARY_FLAG1) && !empty($CYCLE_SALARY_FLAG2)){ //มีรอบทั้ง 2 รอบให้ยึดผลรอบที่ 2
                    $SALARY_FLAG = $CYCLE_SALARY_FLAG2;
                    $CNT_CYCLE = 2;
                }
                
                // //Modidy By Pitak 22-10-2015 End.
                
                // $TOTAL_SCORE = $data[TOTAL_SCORE];
                // $SALARY_FLAG = $data[SALARY_FLAG]; //เดิม...
                // if ($EMPSER_SCORE_METHOD!=1) $TOTAL_SCORE = $TOTAL_SCORE / 2; // เดิม...
                // $TOTAL_SCORE = round(($TOTAL_SCORE / $CNT_CYCLE),2); // Modify By Pitak
                $TOTAL_SCORE = round(($data[TOTAL_SCORE]),2);// Modify By kong
                $tmp_PER_SALARY = $PER_SALARY;
                $PER_NAME = $data[PER_NAME];
                $PER_SURNAME = $data[PER_SURNAME];

                foreach($AL as $al_code => $point_range) {
                    
                    if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
                //    echo "<font color=blue>PER_ID</font> =".$PER_ID.":".$TOTAL_SCORE." >= ".$point_range[MIN]." && ".$TOTAL_SCORE." <= ".$point_range[MAX]."<br>";
    //							$temp_level = $LEVEL_NO[0];
                        if($LEVEL_NO[0] == 'E') $temp_level = "O";
                        elseif($LEVEL_NO[0] == 'S') $temp_level = "K";

                        $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                        $CD_MIDPOINT = $PER_SALARY;
                        $CD_PERCENT = $sf_code[$temp_level][$al_code];
                        
                        //$CD_SALARY = round($PER_SALARY * ($CD_PERCENT/100),2) ; //เดิม
                        //$CD_SALARY_CEIL = (ceil($PER_SALARY * ($CD_PERCENT/100)  /10))*10 ; ///เดิม
                        
                        $CD_SALARY = round(($PER_SALARY * $CD_PERCENT)/100,2) ; // Modify By Pitak
                        $CD_SALARY_CEIL = (ceil((($PER_SALARY * $CD_PERCENT)/100)  /10))*10  ; ///Modify By Pitak
                        
                        $CD_EXTRA_SALARY = 0;
                        
                        if ($tmp_PER_SALARY + $CD_SALARY_CEIL > $LAYER_SALARY_MAX) {
                            $CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
                            if ($tmp_PER_SALARY == $LAYER_SALARY_MAX) $CD_SALARY = 0;
                            else $CD_SALARY -= $CD_EXTRA_SALARY;
                        } else $CD_SALARY = $CD_SALARY_CEIL;
                        
                        if ($CD_SALARY < 0) echo "เกิดข้อผิดพลาด เงินเดือนไม่ถูกต้อง $PER_NAME $PER_SURNAME<br>";
                        if($SALARY_FLAG != 'Y') $CD_SALARY = $CD_PERCENT = $CD_EXTRA_SALARY = 0;
                        
                          //แก้ไขพนักงานราชการไม่มีค่าตอบแทนพิเศษ
                        /*$cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                            CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                                values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
                                            $CD_EXTRA_SALARY, $SESS_USERID, '$UPDATE_DATE', $CD_MIDPOINT) ";  */
                        $cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                            CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                                values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
                                            0, $SESS_USERID, '$UPDATE_DATE', $CD_MIDPOINT) ";               
                        $db_dpis1->send_cmd($cmd);
                      // echo "<pre>".$cmd."<br>";
                        //$db_dpis1->show_error();
                        $CD_ID++;
                    }else{
                        //echo "<font color=red>PER_ID</font> =".$PER_ID.":".$TOTAL_SCORE." >= ".$point_range[MIN]." && ".$TOTAL_SCORE." <= ".$point_range[MAX]."<br>";
                        //echo "PER_ID =>".$PER_ID."($TOTAL_SCORE >= ".$point_range[MIN]." AND $TOTAL_SCORE <= ".$point_range[MAX].")<br>";
                    }
                }
            }    

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
                    O_QTY='$O_QTY_TOTAL',
                    O_SALARY='$O_SALARY_TOTAL',
                    K_QTY='$K_QTY_TOTAL',
                    K_SALARY='$K_SALARY_TOTAL',
                    SUM_QTY='$QTY_KEY_SUM_STR',
                    SUM_SALARY='$SALARY_KEY_SUM_STR',
                    HOLD_SALARY='$HOLD_SALARY_STR',
                    UPDATE_USER=$SESS_USERID, 
                    UPDATE_DATE='$UPDATE_DATE' ,
                    ORG_ID_1=$search_org_id_1,
                    PER_TYPE=$search_per_type
                where CP_ID=$CP_ID ";
        $db_dpis->send_cmd($cmd);
       // echo "$cmd<br>";
        //$db_dpis->show_error();
	
        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$CP_ID." : ".trim($CP_NAME)." : ".$CP_CYCLE." : ".$CP_BUDGET."]");
			
        // insert DTL
        unset($AL_NAME);
		//and AM_CODE <> '1'
        $cmd = " select AL_CODE, AL_NAME , AL_POINT_MIN , AL_POINT_MAX, AL_PERCENT 
                 from PER_ASSESS_LEVEL 
                 where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
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
		
        
        
        //เดิม Old..
        /*$cmd = " select a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, SUM(TOTAL_SCORE) AS TOTAL_SCORE, a.PER_NAME, a.PER_SURNAME 
                 from PER_PERSONAL a, PER_KPI_FORM b
                 where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' 
                     and KF_END_DATE <=  '$CP_END_DATE' and a.LEVEL_NO in ('".$_select_level_no_str."') $where
                 group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, SALARY_FLAG, a.PER_NAME, a.PER_SURNAME
                 order by a.PER_ID " ;*/
        
        //Modidy By Pitak 22-10-2015 Begim
        $cmd = "with tb_c1 as (
                    select PER_SALARY, PER_ID, LEVEL_NO,  PER_NAME, PER_SURNAME ,CNTROW,TOTAL_SCORE,KF_CYCLE
                                        from (
                              select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO, 
                                        a.PER_NAME, a.PER_SURNAME,sum(1) as CNTROW,b.TOTAL_SCORE,b.KF_CYCLE
                                 from PER_PERSONAL a, PER_KPI_FORM b 
                                 where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' and 
                       KF_END_DATE <=  '$CP_END_DATE' and 
                       a.LEVEL_NO in ('".$_select_level_no_str."') $where_dpm   and b.KF_CYCLE = 1
                       group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, a.PER_NAME, a.PER_SURNAME ,b.TOTAL_SCORE,b.KF_CYCLE

                  ) ),tb_c2 as (select PER_SALARY, PER_ID, LEVEL_NO,  PER_NAME, PER_SURNAME ,CNTROW,TOTAL_SCORE,KF_CYCLE
                                        from (
                              select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO,
                                        a.PER_NAME, a.PER_SURNAME,sum(1) as CNTROW,b.TOTAL_SCORE,b.KF_CYCLE
                                 from PER_PERSONAL a, PER_KPI_FORM b
                                 where a.PER_ID = b.PER_ID and a.PER_TYPE = $search_per_type and KF_START_DATE >= '$CP_START_DATE' and 
                       KF_END_DATE <=  '$CP_END_DATE' and 
                       a.LEVEL_NO in ('".$_select_level_no_str."') $where and b.KF_CYCLE = 2
                       group by a.PER_ID, a.PER_SALARY, a.LEVEL_NO, a.PER_NAME, a.PER_SURNAME, b.TOTAL_SCORE,b.KF_CYCLE

                  ))
                  select  a.PER_SALARY, a.PER_ID, a.LEVEL_NO,  a.PER_NAME, a.PER_SURNAME ,(a.CNTROW + nvl(b.CNTROW,0)) as CNTROW, ((NVL(a.TOTAL_SCORE,0) + NVL(b.TOTAL_SCORE,0))/2)  as TOTAL_SCORE  
                  from tb_c2 a  LEFT JOIN tb_c1 b on (a.PER_ID=b.PER_ID) WHERE (a.CNTROW + nvl(b.CNTROW,0)) = 2
                  order by a.PER_ID ";
        //Modidy By Pitak 22-10-2015 End.
        //echo $cmd . "<br>";
        $db_dpis->send_cmd($cmd);
        //$db_dpis->show_error();
        while($data = $db_dpis->get_array()) {
            $PER_ID = $data[PER_ID];
            $PER_SALARY = $data[PER_SALARY];
            $LEVEL_NO = $data[LEVEL_NO];
            $TOTAL_SCORE = $data[TOTAL_SCORE];
            //Modidy By Pitak 22-10-2015 Begim
				
            //หาค่า flag รอบสอง
            $cmdCYCLE1 = " select SALARY_FLAG, SALARY_REMARK1 
                                 from PER_KPI_FORM 
                                 where PER_ID = $PER_ID AND KF_CYCLE=1
                                     and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
            $db_dpis1->send_cmd($cmdCYCLE1);
            $dataCYCLE1 = $db_dpis1->get_array();
            $CYCLE_SALARY_FLAG1 = trim($dataCYCLE1[SALARY_FLAG]); 			 
            $CYCLE_SALARY_REMARK1 = trim($dataCYCLE1[SALARY_REMARK1]); 
            
            $cmdCYCLE2 = " select SALARY_FLAG, SALARY_REMARK1 
                                 from PER_KPI_FORM 
                                 where PER_ID = $PER_ID AND KF_CYCLE=2
                                     and KF_START_DATE >= '$CP_START_DATE'  and  KF_END_DATE <= '$CP_END_DATE'  ";
            $db_dpis1->send_cmd($cmdCYCLE2);
            $dataCYCLE2 = $db_dpis1->get_array();
            $CYCLE_SALARY_FLAG2 = trim($dataCYCLE2[SALARY_FLAG]); 			 
            $CYCLE_SALARY_REMARK2 = trim($dataCYCLE2[SALARY_REMARK1]); 
            $CNT_CYCLE = 1;
            if(!empty($CYCLE_SALARY_FLAG1) && empty($CYCLE_SALARY_FLAG2)){ //ยังมีรอบ 1 รอบเดียว
               $SALARY_FLAG =$CYCLE_SALARY_FLAG1;
            }
            if(empty($CYCLE_SALARY_FLAG1) && !empty($CYCLE_SALARY_FLAG2)){ //มีรอบ 2 รอบเดียว
                $SALARY_FLAG = $CYCLE_SALARY_FLAG2;
            }
            if(!empty($CYCLE_SALARY_FLAG1) && !empty($CYCLE_SALARY_FLAG2)){ //มีรอบทั้ง 2 รอบให้ยึดผลรอบที่ 2
                $SALARY_FLAG = $CYCLE_SALARY_FLAG2;
                $CNT_CYCLE = 2;
            }
            // //Modidy By Pitak 22-10-2015 End.
            
           // $TOTAL_SCORE = $data[TOTAL_SCORE];
            //$SALARY_FLAG = $data[SALARY_FLAG]; //เดิม...
            //if ($EMPSER_SCORE_METHOD!=1) $TOTAL_SCORE = $TOTAL_SCORE / 2; //เดิม...
            $TOTAL_SCORE = round(($TOTAL_SCORE),2);  // Modift By Pitak
            $tmp_PER_SALARY = $PER_SALARY;
            $PER_NAME = $data[PER_NAME];
            $PER_SURNAME = $data[PER_SURNAME];
            foreach($AL as $al_code => $point_range) {
                if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])	{
                    //$temp_level = $LEVEL_NO[0];
                    if($LEVEL_NO[0] == 'E') $temp_level = "O";
                    elseif($LEVEL_NO[0] == 'S') $temp_level = "K";

                    $LAYER_SALARY_MAX = $SALARY_POINT[$LEVEL_NO][max];
                    $CD_MIDPOINT = $PER_SALARY;
                    $CD_PERCENT = $sf_code[$temp_level][$al_code];
                    //$CD_SALARY = round($PER_SALARY * ($CD_PERCENT/100),2) ; // เดิม...
                    //$CD_SALARY_CEIL = (ceil($PER_SALARY * ($CD_PERCENT/100)  /10))*10 ;// เดิม...
                    
                    $CD_SALARY = round(($PER_SALARY * $CD_PERCENT)/100,2) ; // Modify By Pitak
                    $CD_SALARY_CEIL = (ceil((($PER_SALARY * $CD_PERCENT)/100)  /10))*10  ; ///Modify By Pitak
                    $CD_EXTRA_SALARY = 0;
                    
                    if ($tmp_PER_SALARY + $CD_SALARY_CEIL > $LAYER_SALARY_MAX) {
                            $CD_EXTRA_SALARY = $tmp_PER_SALARY + $CD_SALARY - $LAYER_SALARY_MAX;
                            if ($tmp_PER_SALARY == $LAYER_SALARY_MAX) $CD_SALARY = 0;
                            else $CD_SALARY -= $CD_EXTRA_SALARY;
                    } else $CD_SALARY = $CD_SALARY_CEIL;
                    
                    if ($CD_SALARY < 0) echo "เกิดข้อผิดพลาด เงินเดือนไม่ถูกต้อง $PER_NAME $PER_SURNAME<br>";
                    if($SALARY_FLAG != 'Y') $CD_SALARY = $CD_PERCENT = $CD_EXTRA_SALARY = 0;

                                        //แก้ไขพนักงานราชการไม่มีค่าตอบแทนพิเศษ
                    /*$cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                         CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                             values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
                                     $CD_EXTRA_SALARY, $SESS_USERID, '$UPDATE_DATE', $CD_MIDPOINT) ";*/
                    $cmd = " insert into PER_COMPENSATION_TEST_DTL (CD_ID, CP_ID, PER_ID, LEVEL_NO, AL_CODE, CD_SALARY, 
                                         CD_PERCENT, CD_EXTRA_SALARY, UPDATE_USER, UPDATE_DATE, CD_MIDPOINT) 
                             values ($CD_ID, $CP_ID, $PER_ID, '$LEVEL_NO','$al_code', $CD_SALARY, $CD_PERCENT, 
                                     0, $SESS_USERID, '$UPDATE_DATE', $CD_MIDPOINT) ";
                    $db_dpis1->send_cmd($cmd);
//							$db_dpis1->show_error();
                    $CD_ID++;
                } //if($TOTAL_SCORE >= $point_range[MIN] && $TOTAL_SCORE <= $point_range[MAX])
            } //foreach($AL as $al_code => $point_range) {
        } //while($data = $db_dpis->get_array()) {   
    }//if($command == "UPDATE" && trim($CP_ID) ){
	
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
		
		$cmd = " select 	CP_NAME, CP_START_DATE, CP_END_DATE, CP_CYCLE, CP_BUDGET, SF_CODE_O, SF_CODE_K, DEPARTMENT_ID, ORG_ID, ORG_ID_1, PER_TYPE
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
		//and AM_CODE <> '1'
		$CP_YEAR = substr($CP_END_DATE,0,4) + 543;
		$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
						order by AL_POINT_MIN DESC ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) {
			$init_al_code[] = $data[AL_CODE] . ",";
			$init_al_name[] =  "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
		}

		list($SF_CODE_O,$list_code_o) = explode('#',$SF_CODE_O);
		list($SF_CODE_K,$list_code_k) = explode('#',$SF_CODE_K);

		$INIT_O = $list_code_o . ":";
		$INIT_K = $list_code_k . ":";

		$temp_str = str_replace(":","'>  ",$INIT_O);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_o = str_replace("[]","_o",$temp_1);
		
		$temp_str = str_replace(":","'>  ",$INIT_K);
		$temp_1 = str_replace($init_al_code,$init_al_name,$temp_str);
		$temp_k = str_replace("[]","_k",$temp_1);
		
		$init_div_o = $temp_o;
		$init_div_k = $temp_k;
	} 
	//echo $Testing;
	if((!$UPD||!$VIEW) && !$Testing){
		$_select_level_no = array("E1","E2","E7","E3","E4","E5","S1","S2","S3");
	}
	
	if (!$CP_YEAR) 
		if(date("Y-m-d") <= date("Y")."-10-01") $CP_YEAR = date("Y") + 543;
		else $CP_YEAR = (date("Y") + 543) + 1;

	//___echo "$UPD && $DEL && $VIEW && $err_text && $Testing";
	if( (!$UPD && !$DEL && !$VIEW && !$err_text && !$Testing) ){
		unset($O_QTY_TOTAL);
		unset($O_SALARY_TOTAL);
		unset($K_QTY_TOTAL);
		unset($K_SALARY_TOTAL);
		unset($QTY_KEY_SUM_STR);
		unset($SALARY_KEY_SUM_STR);
		
		unset($QTY_GRANDTOTAL);
		unset($SALARY_GRANDTOTAL);
		unset($CP_RESULT);
		
//		unset($CP_CYCLE);
		unset($CP_ID);
		unset($CP_NAME);
		unset($CP_BUDGET);
		unset($SF_CODE_O);unset($SF_CODE_K);
		unset($_POST);
		if($SESS_USERGROUP_LEVEL == 2){ //2 = จังหวัด
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
	//and AM_CODE <> '1'
	$cmd = " select AL_CODE, AL_NAME from PER_ASSESS_LEVEL 
					where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
					order by AL_POINT_MIN DESC ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()) {
		$init_al_code[] = $data[AL_CODE] . ",";
		$init_al_name[] = "<input name=code[]_".$data[AL_CODE]." type=text class=textbox size=5 maxlength=6 value='";
	}
	
	//echo "$command  && $command  && $VIEW && $UPD && $Testing";
	
	 // Press Testing
	if($Testing) {
		//and AM_CODE <> '1'
		$cmd = " select AL_CODE, AL_NAME, AL_PERCENT from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
						order by AL_POINT_MIN DESC ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
// 		echo "-> $cmd";
		while($data = $db_dpis->get_array()) {
			$AL_CODE = $data[AL_CODE];
			$AL_NAME[$AL_CODE] = $data[AL_NAME];
	//		$temp_al_code[] = $data[AL_CODE] . ",";
			$temp_xx_code = $data[AL_CODE];
			$temp_o = "<input name=code_o_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_k = "<input name=code_k_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$div_sf_code_o[$temp_xx_code] = $temp_o;
			$div_sf_code_k[$temp_xx_code] = $temp_k;
		}
                //print_r($_POST);

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
				} // switch
			}
		} // foreach
     
	} elseif($UPD || $VIEW) {
		error_reporting(0);
		unset($div_sf_code_o);
		unset($div_sf_code_k);
		$cmd = "select SF_CODE_O,SF_CODE_K from PER_COMPENSATION_TEST where CP_ID=$CP_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$xx_code_o_array = explode(':',substr($data['SF_CODE_O'],1));
		$xx_code_k_array = explode(':',substr($data['SF_CODE_K'],1));
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
	} else {
		unset($div_sf_code_o);
		unset($div_sf_code_k);
		//and AM_CODE <> '1'
		$cmd = " select AL_CODE, AL_NAME, AL_PERCENT from PER_ASSESS_LEVEL 
						where AL_YEAR = '$CP_YEAR' and AL_CYCLE = $CP_CYCLE and PER_TYPE = $search_per_type and AL_ACTIVE =  1  $where_org 
						order by AL_POINT_MIN DESC ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()) {
	//		$temp_al_code[] = $data[AL_CODE] . ",";
			$temp_xx_code = $data[AL_CODE];
			$temp_o = "<input name=code_o_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$temp_k = "<input name=code_k_".$temp_xx_code." type=text class=textbox size=5 maxlength=6 value='".$data[AL_PERCENT]."'> ";
			$div_sf_code_o[$temp_xx_code] = $temp_o;
			$div_sf_code_k[$temp_xx_code] = $temp_k;
		}
	} 
	
?>