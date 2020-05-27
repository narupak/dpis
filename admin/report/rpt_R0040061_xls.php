<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";
//	require_once "../../Excel/Writer.php";

	//ini_set("max_execution_time", $max_execution_time); /*เดิม*/
        ini_set("max_execution_time", 0);
        
        set_time_limit(0);
        //ini_set("oci8.statement_cache_size","0");
        ini_set("memory_limit","512M"); 
        
        /* ตัวแปล $CHK=='P0119' เป็นเงื่อนไขใช้สำหรับออกรายงาน ปุ่ม excel กพ. งาน P0119 สอบถามข้อมูล */
        
       
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
        
        
        /*INDEX BEGIN*/
        if(1==0){
            //PER_LICENSEHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_LICENSEHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_LICENSEHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_LICENSEHIS' AND COLUMN_NAME='LT_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_LT_CODE ON PER_LICENSEHIS (LT_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_ACTINGHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_ACTINGHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_ACTINGHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_SPECIAL_SKILL
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SPECIAL_SKILL' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_SPECIAL_SKILL (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SPECIAL_SKILL' AND COLUMN_NAME='SS_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_SS_CODE ON PER_SPECIAL_SKILL (SS_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_SERVICEHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SERVICEHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_SERVICEHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SERVICEHIS' AND COLUMN_NAME='SV_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_SV_CODE ON PER_SERVICEHIS (SV_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SERVICEHIS' AND COLUMN_NAME='ORG_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_ORG_ID ON PER_SERVICEHIS (ORG_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SERVICEHIS' AND COLUMN_NAME='SRT_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_SRT_CODE ON PER_SERVICEHIS (SRT_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_NAMEHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_NAMEHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_NAMEHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_NAMEHIS' AND COLUMN_NAME='PN_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PN_CODE ON PER_NAMEHIS (PN_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_REWARDHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_REWARDHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_REWARDHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_REWARDHIS' AND COLUMN_NAME='REW_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_REW_CODE ON PER_REWARDHIS (REW_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_TIMEHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_TIMEHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_TIMEHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_TIMEHIS' AND COLUMN_NAME='TIME_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_TIME_CODE ON PER_TIMEHIS (TIME_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_PUNISHMENT
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_PUNISHMENT' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_PUNISHMENT (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_PUNISHMENT' AND COLUMN_NAME='CRD_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_CRD_CODE ON PER_PUNISHMENT (CRD_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_ABSENTHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_ABSENTHIS' AND COLUMN_NAME='AB_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_AB_CODE ON PER_ABSENTHIS (AB_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_HEIR
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_HEIR' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_HEIR (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_HEIR' AND COLUMN_NAME='HR_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_HR_CODE ON PER_HEIR (HR_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_ABILITY
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_ABILITY' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_ABILITY (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_ABILITY' AND COLUMN_NAME='AL_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_AL_CODE ON PER_ABILITY (AL_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_EXTRAHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EXTRAHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_EXTRAHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EXTRAHIS' AND COLUMN_NAME='EX_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_EX_CODE ON PER_EXTRAHIS (EX_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_POS_MGTSALARYHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POS_MGTSALARYHIS' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_POS_MGTSALARYHIS (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POS_MGTSALARYHIS' AND COLUMN_NAME='EX_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_EX_CODE ON PER_POS_MGTSALARYHIS (EX_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_TRAINING
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_TRAINING' AND COLUMN_NAME='PER_ID' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PER_ID ON PER_TRAINING (PER_ID) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_TRAINING' AND COLUMN_NAME='TR_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_TR_CODE ON PER_TRAINING (TR_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_EDUCATE
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EDUCATE' AND COLUMN_NAME='INS_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_INS_CODE ON PER_EDUCATE (INS_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_EDUCATE' AND COLUMN_NAME='CT_CODE_EDU' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_CT_CODE_EDU ON PER_EDUCATE (CT_CODE_EDU) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_SALARYHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_SALARYHIS' AND COLUMN_NAME='MOV_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_MOV_CODE ON PER_SALARYHIS (MOV_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
            //PER_POSITIONHIS
            $cmd = " SELECT COUNT(*) AS CNT FROM USER_IND_COLUMNS WHERE TABLE_NAME = 'PER_POSITIONHIS' AND COLUMN_NAME='PM_CODE' ";
            $db_dpis2->send_cmd($cmd);
            $data_index = $db_dpis2->get_array();
            $CNT = trim($data_index[CNT]);
            if($CNT==0){
                $cmd = " CREATE INDEX IDX_PM_CODE ON PER_POSITIONHIS (PM_CODE) ";
                $db_dpis2->send_cmd($cmd);
            }
            
        }
        /*INDEX END*/
	$arr_history_name = explode("|", $HISTORY_LIST);
//	$arr_history_name = array("POSITIONHIS", "SALARYHIS", "EXTRA_INCOMEHIS", "EDUCATE", "TRAINING", "ABILITY", "HEIR", "ABSENTHIS", "PUNISHMENT", "TIMEHIS", "REWARDHIS", "MARRHIS", "NAMEHIS", "DECORATEHIS", "SERVICEHIS", "SPECIALSKILLHIS"); 
	$search_condition = "";
        if($CHK=='P0119'){
            $arr_search_condition[] = "(a.PER_TYPE in (".$search_per_type."))";
        }else{
            $arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) ."))";
        } 
        
        if($CHK=='P0119'){
            if($search_department_id){
                    $arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_department_id ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $DEPARTMENT_NAME = $data[ORG_NAME];
            }else if($search_ministry_id){
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $search_ministry_id ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $MINISTRY_NAME = $data[ORG_NAME];

                    unset($arr_department);
                    $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
                    $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            }
        }else{
            if($DEPARTMENT_ID){
                    $arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $DEPARTMENT_NAME = $data[ORG_NAME];
            }else if($MINISTRY_ID){
                    $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $MINISTRY_NAME = $data[ORG_NAME];

                    unset($arr_department);
                    $cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
                    $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
            }else if($PROVINCE_CODE){
                    $cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
                    if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
                    $db_dpis->send_cmd($cmd);
                    while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
                    $arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";

            } // end if
        }    

	if($select_org_structure==0) {
            if($CHK=='P0119'){
                if(trim($search_org_id)){
                    if($search_per_type==1){
                        $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
                    }else if($search_per_type==2){
                        $arr_search_condition[] = "(d.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
                    }else if($search_per_type==3){
                        $arr_search_condition[] = "(e.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
                    }else{
                        $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
			$list_type_text .= "$search_org_name";
                    }
		} // end if
		if(trim($search_org_id_1)){ 
                    if($search_per_type==1){
                        $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
                    }else if($search_per_type==2){
                        $arr_search_condition[] = "(d.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
                    }else if($search_per_type==3){
                        $arr_search_condition[] = "(e.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
                    }else{
                        $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
                    }
		} // end if
		if(trim($search_org_id_2)){ 
                    if($search_per_type==1){
                        $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
                    }else if($search_per_type==2){
                        $arr_search_condition[] = "(d.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
                    }else if($search_per_type==3){
                        $arr_search_condition[] = "(e.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
                    }else{
                        $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
                    }
		} // end if
            }else{
		if(trim($search_org_id)){ 
			$arr_search_condition[] = "(c.ORG_ID = $search_org_id or d.ORG_ID = $search_org_id or e.ORG_ID = $search_org_id )";
			$list_type_text .= "$search_org_name";
		} // end if
		if(trim($search_org_id_1)){ 
			$arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1 or d.ORG_ID_1 = $search_org_id_1 or e.ORG_ID_1 = $search_org_id_1)";
			$list_type_text .= " - $search_org_name_1";
		} // end if
		if(trim($search_org_id_2)){ 
			$arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2 or d.ORG_ID_2 = $search_org_id_2 or e.ORG_ID_2 = $search_org_id_2)";
			$list_type_text .= " - $search_org_name_2";
		} // end if
            }    
	}else if($select_org_structure==1) {
		if(trim($search_org_ass_id)){ 
			$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
			$list_type_text .= "$search_org_ass_name";
		} // end if
		if(trim($search_org_ass_id_1)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_1)";
			$list_type_text .= " - $search_org_ass_name_1";
		} // end if
		if(trim($search_org_ass_id_2)){ 
			$arr_search_condition[] = "(a.ORG_ID =  $search_org_ass_id_2)";
			$list_type_text .= " - $search_org_ass_name_2";
		} // end if
	}

	if(in_array("SELECT", $list_type)){ 
            if(trim($SELECTED_PER_ID)){ 
                $cnt = 0;
                $SELECTED_LIST_ARR = explode(",",$SELECTED_PER_ID);
                $arr_condition .= "( a.PER_ID in ( ";
                for($i=0;$i<count($SELECTED_LIST_ARR);$i++){
                    if($cnt+1 < 1000 && $i+1 < count($SELECTED_LIST_ARR))  $comma = ",";
                    else $comma = "";
                    if($cnt < 1000){
                        $arr_condition .= $SELECTED_LIST_ARR[$i].$comma;
                        $cnt++;
                    }else{
                        $arr_condition .= " ) or a.PER_ID in ( ";
                        $arr_condition .= $SELECTED_LIST_ARR[$i].$comma;
                        $cnt = 0;
                    }
                }
                $arr_condition .=  " )) ";    
                $arr_search_condition[] = $arr_condition;
                // เดิม $arr_search_condition[] = "(a.PER_ID in (".$SELECTED_PER_ID."))"; //ติดปัญหาเรื่อง IN เกิน 1000 รายการ ฐานข้อมูล oracle ให้ in ได้แค่ 1000 รายการ
            }
	}elseif($list_type == "CONDITION"){
		//ทั้งข้าราชการ/ลูกจ้าง/พนง.ราชการ
		if(trim($search_pos_no)) $arr_search_condition[] = "(trim(c.POS_NO)='$search_pos_no' or trim(d.POEM_NO)='$search_pos_no' or trim(e.POEMS_NO)='$search_pos_no')";
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) >= '". str_pad($search_min_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		if(trim($search_max_level)){ 
			if($DPISDB=="odbc") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="oci8") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
			if($DPISDB=="mysql") $arr_search_condition[] = "(trim(a.LEVEL_NO) <= '". str_pad($search_max_level, 2, "0", STR_PAD_LEFT) ."')";
		}
		$arr_search_condition[] = "(a.PER_STATUS in (". implode(",", $search_per_status) ."))";
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
/*	$list_type_text = $ALL_REPORT_TITLE;
	$company_name = "รูปแบบการออกรายงาน : $list_type_text"; */
	$company_name = "";
	$report_title = $MINISTRY_NAME?$MINISTRY_NAME:"".$DEPARTMENT_NAME?$DEPARTMENT_NAME:"";
	$report_title .= "$KP7_TITLE";
        if($CHK=='P0119'){
            $report_code = "P0119";
        }else{
            $report_code = "R04061";
        }    

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	$heading_width[POSITIONHIS][0] = "15";
	$heading_width[POSITIONHIS][1] = "25";      
	$heading_width[POSITIONHIS][2] = "33";
	$heading_width[POSITIONHIS][3] = "25";
	$heading_width[POSITIONHIS][4] = "20";
	$heading_width[POSITIONHIS][5] = "22";
	$heading_width[POSITIONHIS][6] = "65";
	$heading_width[POSITIONHIS][7] = "20";
	$heading_width[POSITIONHIS][8] = "25";
	$heading_width[POSITIONHIS][9] = "30";
	$heading_width[POSITIONHIS][10] = "30";
	$heading_width[POSITIONHIS][11] = "30";
	$heading_width[POSITIONHIS][12] = "30";
	$heading_width[POSITIONHIS][13] = "30";
	$heading_width[POSITIONHIS][14] = "25";
	$heading_width[POSITIONHIS][15] = "30";
	$heading_width[POSITIONHIS][16] = "30";
	$heading_width[POSITIONHIS][17] = "30";
	$heading_width[POSITIONHIS][18] = "30";
	$heading_width[POSITIONHIS][19] = "30";
	$heading_width[POSITIONHIS][20] = "25";

	/*Release 5.2.1.8 Begin*/
	$heading_width[SALARYHIS][0] = "15";
	$heading_width[SALARYHIS][1] = "25";      
	$heading_width[SALARYHIS][2] = "33";
	$heading_width[SALARYHIS][3] = "25";
	$heading_width[SALARYHIS][4] = "20";
	$heading_width[SALARYHIS][5] = "22";
	$heading_width[SALARYHIS][6] = "65";
	$heading_width[SALARYHIS][7] = "20";
	$heading_width[SALARYHIS][8] = "25";
	$heading_width[SALARYHIS][9] = "25";
	$heading_width[SALARYHIS][10] = "25";
        
	$heading_width[EXTRAHIS][0] = "25";
	$heading_width[EXTRAHIS][1] = "25";
	$heading_width[EXTRAHIS][2] = "125";
	$heading_width[EXTRAHIS][3] = "25";
        /*Release 5.2.1.8  End*/
	
	$heading_width[EXTRA_INCOMEHIS][0] = "15";
	$heading_width[EXTRA_INCOMEHIS][1] = "25";
	$heading_width[EXTRA_INCOMEHIS][2] = "125";
	$heading_width[EXTRA_INCOMEHIS][3] = "25";

	$heading_width[EDUCATE][0] = "20";
	$heading_width[EDUCATE][1] = "20";
	$heading_width[EDUCATE][2] = "90";
        $heading_width[EDUCATE][3] = "20";
        $heading_width[EDUCATE][4] = "20";
        $heading_width[EDUCATE][5] = "20";

	$heading_width[TRAINING][0] = "20";
	$heading_width[TRAINING][1] = "25";
	$heading_width[TRAINING][2] = "90";
	$heading_width[TRAINING][3] = "30";
	$heading_width[TRAINING][4] = "25";

	$heading_width[ABILITY][0] = "30";
	$heading_width[ABILITY][1] = "100";

	$heading_width[HEIR][0] = "30";
	$heading_width[HEIR][1] = "100";

	$heading_width[ABSENTHIS][0] = "30";
	$heading_width[ABSENTHIS][1] = "20";
	$heading_width[ABSENTHIS][2] = "125";

	$heading_width[PUNISHMENT][0] = "30";
	$heading_width[PUNISHMENT][1] = "60";
	$heading_width[PUNISHMENT][2] = "55";

	$heading_width[TIMEHIS][0] = "30";
	$heading_width[TIMEHIS][1] = "25";
	$heading_width[TIMEHIS][2] = "25";

	$heading_width[REWARDHIS][0] = "30";
	$heading_width[REWARDHIS][1] = "140";

	$heading_width[MARRHIS][0] = "30";
	$heading_width[MARRHIS][1] = "140";

	$heading_width[NAMEHIS][0] = "30";
	$heading_width[NAMEHIS][1] = "140";

	$heading_width[DECORATEHIS][0] = "30";
	$heading_width[DECORATEHIS][1] = "54";
	$heading_width[DECORATEHIS][2] = "32";
	$heading_width[DECORATEHIS][3] = "32";
	$heading_width[DECORATEHIS][4] = "32";

	$heading_width[SERVICEHIS][0] = "30";
	$heading_width[SERVICEHIS][1] = "50";
	$heading_width[SERVICEHIS][2] = "85";
	$heading_width[SERVICEHIS][3] = "35";

	$heading_width[SPECIALSKILLHIS][0] = "30";
	$heading_width[SPECIALSKILLHIS][1] = "90";

	$heading_width[ACTINGHIS][0] = "50";
	$heading_width[ACTINGHIS][1] = "20";      
	$heading_width[ACTINGHIS][2] = "15";
	$heading_width[ACTINGHIS][3] = "15";
	$heading_width[ACTINGHIS][4] = "12";
	$heading_width[ACTINGHIS][5] = "40";
	$heading_width[ACTINGHIS][6] = "40";
	$heading_width[ACTINGHIS][7] = "35";

	$heading_width[LICENSEHIS][0] = "70";
	$heading_width[LICENSEHIS][1] = "35";
	$heading_width[LICENSEHIS][2] = "30";
	$heading_width[LICENSEHIS][3] = "30";
	$heading_width[LICENSEHIS][4] = "25";

	function print_header($HISTORY_NAME){
		global $worksheet, $xlsRow;
		global $heading_width, $DEPARTMENT_TITLE, $ORG_TITLE, $REMARK_TITLE, $PL_TITLE;

		switch($HISTORY_NAME){
			case "POSITIONHIS" :
				$worksheet->set_column(0, 0, $heading_width[POSITIONHIS][0]);
				$worksheet->set_column(1,1,$heading_width[POSITIONHIS][1]);
				$worksheet->set_column(2,2,$heading_width[POSITIONHIS][2]);
				$worksheet->set_column(3,3,$heading_width[POSITIONHIS][3]);
				$worksheet->set_column(4,4,$heading_width[POSITIONHIS][4]);
				$worksheet->set_column(5,5,$heading_width[POSITIONHIS][5]);
				$worksheet->set_column(6,6,$heading_width[POSITIONHIS][6]);
				$worksheet->set_column(7,7,$heading_width[POSITIONHIS][7]);
				$worksheet->set_column(8,8,$heading_width[POSITIONHIS][8]);
				$worksheet->set_column(9,9,$heading_width[POSITIONHIS][9]);
				$worksheet->set_column(10,10,$heading_width[POSITIONHIS][10]);
				$worksheet->set_column(11,11,$heading_width[POSITIONHIS][11]);
				$worksheet->set_column(12,12,$heading_width[POSITIONHIS][12]);
				$worksheet->set_column(13,13,$heading_width[POSITIONHIS][13]);
				$worksheet->set_column(14,14,$heading_width[POSITIONHIS][14]);
				$worksheet->set_column(15,15,$heading_width[POSITIONHIS][15]);
				$worksheet->set_column(16,16,$heading_width[POSITIONHIS][16]);
				$worksheet->set_column(17,17,$heading_width[POSITIONHIS][17]);
				$worksheet->set_column(18,18,$heading_width[POSITIONHIS][18]);
				$worksheet->set_column(19,19,$heading_width[POSITIONHIS][19]);
				$worksheet->set_column(20,20,$heading_width[POSITIONHIS][20]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"ประเภทการเคลื่อนไหว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,3,"เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,4,"ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,5,"อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,6,"เอกสาร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,7,"$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,8,"$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,9,"ต่ำกว่าสำนัก/กอง 1 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,10,"ต่ำกว่าสำนัก/กอง 2 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,11,"ต่ำกว่าสำนัก/กอง 3 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,12,"ต่ำกว่าสำนัก/กอง 4 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,13,"ต่ำกว่าสำนัก/กอง 5 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,14,"$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,15,"ต่ำกว่าสำนัก/กอง 1 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,16,"ต่ำกว่าสำนัก/กอง 2 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,17,"ต่ำกว่าสำนัก/กอง 3 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,18,"ต่ำกว่าสำนัก/กอง 4 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,19,"ต่ำกว่าสำนัก/กอง 5 ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,20,"$REMARK_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,3,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,5,"เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,6,"อ้างอิง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,7,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,8,"ตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,9,"ตามกฏหมาย  ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,10,"ตามกฏหมาย  ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,11,"ตามกฏหมาย  ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,12,"ตามกฏหมาย  ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,13,"ตามกฏหมาย  ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,14,"ตามมอบหมายงาน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,15,"ตามมอบหมายงาน ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,16,"ตามมอบหมายงาน ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,17,"ตามมอบหมายงาน ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,18,"ตามมอบหมายงาน ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,19,"ตามมอบหมายงาน ", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,20,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				break;	
			case "EDUCATE" :
				$worksheet->set_column(0,0,$heading_width[EDUCATE][0]);
				$worksheet->set_column(1,1,$heading_width[EDUCATE][1]);
				$worksheet->set_column(2,2,$heading_width[EDUCATE][2]);
                                $worksheet->set_column(3,3,$heading_width[EDUCATE][3]);
                                $worksheet->set_column(4,4,$heading_width[EDUCATE][4]);
                                $worksheet->set_column(5,5,$heading_width[EDUCATE][5]);

				$xlsRow++;	
				$worksheet->write($xlsRow,0,"สถานศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,1,"ตั้งแต่ - ถึง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				$worksheet->write($xlsRow,2,"วุฒิที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                                $worksheet->write($xlsRow,3,"ประเทศ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                                $worksheet->write($xlsRow,4,"เกียรตินิยม", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                                $worksheet->write($xlsRow,5,"ทุนการศึกษา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));	//ฝึกอบรม และดูงาน
				$worksheet->write($xlsRow,1,"(เดือน ปี)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				$worksheet->write($xlsRow,2,"ระบุสาขาวิชาเอก (ถ้ามี)", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                                $worksheet->write($xlsRow,3,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                                $worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                                $worksheet->write($xlsRow,5,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
				break;			
		case "TRAINING" :
				$worksheet->set_column(0,0,$heading_width[TRAINING][0]);
				$worksheet->set_column(1,1,$heading_width[TRAINING][1]);
				$worksheet->set_column(2,2,$heading_width[TRAINING][2]);
				$worksheet->set_column(3,3,$heading_width[TRAINING][3]);
				$worksheet->set_column(4,4,$heading_width[TRAINING][4]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"สถานที่ฝึกอบรม", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ตั้งแต่ - ถึง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"โครงการ, หลักสูตร", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"หน่วยงานที่จัด", set_format("xlsFmtTableHeader", "B", "C", "TLRB",1));
				$worksheet->write($xlsRow,4,"ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;				
                case "SALARYHIS" :
                    $worksheet->set_column(0, 0, $heading_width[POSITIONHIS][0]);
                    $worksheet->set_column(1,1,$heading_width[POSITIONHIS][1]);
                    $worksheet->set_column(2,2,$heading_width[POSITIONHIS][2]);
                    $worksheet->set_column(3,3,$heading_width[POSITIONHIS][3]);
                    $worksheet->set_column(4,4,$heading_width[POSITIONHIS][4]);
                    $worksheet->set_column(5,5,$heading_width[POSITIONHIS][5]);
                    $worksheet->set_column(6,6,$heading_width[POSITIONHIS][6]);
                    $worksheet->set_column(7,7,$heading_width[POSITIONHIS][7]);
                    $worksheet->set_column(8,8,$heading_width[POSITIONHIS][8]);
                    $worksheet->set_column(9,9,$heading_width[POSITIONHIS][9]);
                    $worksheet->set_column(10,10,$heading_width[POSITIONHIS][10]);

                    $xlsRow++;
                    $worksheet->write($xlsRow,0,"วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,1,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,2,"ประเภทการเคลื่อนไหว", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,3,"เลขที่", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,4,"ระดับ", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,5,"อัตรา", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,6,"เอกสาร", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,7,"$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,8,"$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,9,"$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));
                    $worksheet->write($xlsRow,10,"$REMARK_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLR", 0));

                    $xlsRow++;
                    $worksheet->write($xlsRow,0,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,1,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,2,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,3,"ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,4,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,5,"เงินเดือน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,6,"อ้างอิง", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,7,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,8,"ตามกฏหมาย", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,9,"ตามมอบหมายงาน", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    $worksheet->write($xlsRow,10,"", set_format("xlsFmtTableHeader", "B", "C", "LRB", 0));
                    break;	
                case "EXTRAHIS" :
                    $worksheet->set_column(0,0,$heading_width[EXTRA_INCOMEHIS][0]);
                    $worksheet->set_column(1,1,$heading_width[EXTRA_INCOMEHIS][1]);
                    $worksheet->set_column(2,2,$heading_width[EXTRA_INCOMEHIS][2]);
                    $worksheet->set_column(3,3,$heading_width[EXTRA_INCOMEHIS][3]);

                    $xlsRow++;
                    $worksheet->write($xlsRow,0,"วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
                    $worksheet->write($xlsRow,1,"ถึงวันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
                    $worksheet->write($xlsRow,2,"ประเภทเงิน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
                    $worksheet->write($xlsRow,3,"จำนวน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
                    break;    
		case "EXTRA_INCOMEHIS" :
				$worksheet->set_column(0,0,$heading_width[EXTRA_INCOMEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[EXTRA_INCOMEHIS][1]);
				$worksheet->set_column(2,2,$heading_width[EXTRA_INCOMEHIS][2]);
				$worksheet->set_column(3,3,$heading_width[EXTRA_INCOMEHIS][3]);

				$xlsRow++;
				$worksheet->write($xlsRow,0,"วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ถึงวันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"ประเภทเงินเพิ่มพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"เงินเพิ่มพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "ABILITY" :
				$worksheet->set_column(0,0,$heading_width[ABILITY][0]);
				$worksheet->set_column(1,1,$heading_width[ABILITY][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ด้านความสามารถพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ความสามารถพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "HEIR" :
				$worksheet->set_column(0,0,$heading_width[HEIR][0]);
				$worksheet->set_column(1,1,$heading_width[HEIR][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ความสัมพันธ์", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "ABSENTHIS" :
				$worksheet->set_column(0,0,$heading_width[ABSENTHIS][0]);
				$worksheet->set_column(1,1,$heading_width[ABSENTHIS][1]);
				$worksheet->set_column(2,2,$heading_width[ABSENTHIS][2]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วันที่ลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"ประเภทการลา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "PUNISHMENT" :
				$worksheet->set_column(0,0,$heading_width[PUNISHMENT][0]);
				$worksheet->set_column(1,1,$heading_width[PUNISHMENT][1]);
				$worksheet->set_column(2,2,$heading_width[PUNISHMENT][2]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ฐานความผิด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"กรณีความผิด", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"วันที่รับโทษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "TIMEHIS" :
				$worksheet->set_column(0,0,$heading_width[TIMEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[TIMEHIS][1]);
				$worksheet->set_column(2,2,$heading_width[TIMEHIS][2]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"เวลาทวีคูณ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"จำนวนวัน", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"จำนวนวันที่ไม่นับ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "REWARDHIS" :
				$worksheet->set_column(0,0,$heading_width[REWARDHIS][0]);
				$worksheet->set_column(1,1,$heading_width[REWARDHIS][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วันที่ได้รับความดีความชอบ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ความดีความชอบ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "MARRHIS" :
				$worksheet->set_column(0,0,$heading_width[MARRHIS][0]);
				$worksheet->set_column(1,1,$heading_width[MARRHIS][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ชื่อคู่สมรส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"วันที่สมรส", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "NAMEHIS" :
				$worksheet->set_column(0,0,$heading_width[NAMEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[NAMEHIS][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วันที่เปลี่ยนแปลง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"คำนำหน้า - ชื่อ - สกุล", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "DECORATEHIS" :
				$worksheet->set_column(0,0,$heading_width[DECORATEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[DECORATEHIS][1]);
				$worksheet->set_column(2,2,$heading_width[DECORATEHIS][2]);
				$worksheet->set_column(3,3,$heading_width[DECORATEHIS][3]);
				$worksheet->set_column(4,4,$heading_width[DECORATEHIS][4]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"(1)", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
				$worksheet->write($xlsRow,1,"(2)", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
				$worksheet->write($xlsRow,2,"(3)", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
				$worksheet->write($xlsRow,3,"(4)", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
				$worksheet->write($xlsRow,4,"(5)", set_format("xlsFmtTableHeader", "B", "C", "LTR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ตำแหน่ง (อดีต-ปัจจุบัน", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,1,"ลำดับเครื่องราชฯ ที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,2,"ว.ด.ป.", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,3,"เครื่องราชฯ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,4,"เครื่องราชฯ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"เฉพาะปีที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,1,"พระราชทานแล้ว", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,2,"ที่ได้รับ", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,3,"ตาม (2)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				$worksheet->write($xlsRow,4,"ตาม (2)", set_format("xlsFmtTableHeader", "B", "C", "LR", 0));
				
				$xlsRow++;
				$worksheet->write($xlsRow,0,"พระราชทานเครื่องราชฯ)", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
				$worksheet->write($xlsRow,1,"จากชั้นรองไปชั้นสูงตาม (1)", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
				$worksheet->write($xlsRow,2,"พระราชทานตาม (2)", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
				$worksheet->write($xlsRow,3,"รับมอบเมื่อ ", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
				$worksheet->write($xlsRow,4,"ส่งคืนเมื่อ ", set_format("xlsFmtTableHeader", "B", "C", "LBR", 0));
				break;			
			case "SERVICEHIS" :
				$worksheet->set_column(0,0,$heading_width[SERVICEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[SERVICEHIS][1]);
				$worksheet->set_column(2,2,$heading_width[SERVICEHIS][2]);
				$worksheet->set_column(3,3,$heading_width[SERVICEHIS][3]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"วัน เดือน ปี", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ประเภทราชการพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"หัวข้อราชการพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"หนังสือ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;			
			case "SPECIALSKILLHIS" :
				$worksheet->set_column(0,0,$heading_width[SPECIALSKILLHIS][0]);
				$worksheet->set_column(1,1,$heading_width[SPECIALSKILLHIS][1]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ด้านความเชี่ยวชาญพิเศษ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"เน้นทาง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;	
			case "ACTINGHIS" :
				$worksheet->set_column(0, 0, $heading_width[ACTINGHIS][0]);
				$worksheet->set_column(1,1,$heading_width[ACTINGHIS][1]);
				$worksheet->set_column(2,2,$heading_width[ACTINGHIS][2]);
				$worksheet->set_column(3,3,$heading_width[ACTINGHIS][3]);
				$worksheet->set_column(4,4,$heading_width[ACTINGHIS][4]);
				$worksheet->set_column(5,5,$heading_width[ACTINGHIS][5]);
				$worksheet->set_column(6,6,$heading_width[ACTINGHIS][6]);
				$worksheet->set_column(7,7,$heading_width[ACTINGHIS][7]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ประเภทการเคลื่อนไหว", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,1,"เลขที่คำสั่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,2,"ตั้งแต่วันที่", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,3,"ถึงวันนี้", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,4,"เลขที่ตำแหน่ง", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,5,"$PL_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,6,"$DEPARTMENT_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
				$worksheet->write($xlsRow,7,"$ORG_TITLE", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 0));
			break;	
		case "LICENSEHIS" :
				$worksheet->set_column(0,0,$heading_width[LICENSEHIS][0]);
				$worksheet->set_column(1,1,$heading_width[LICENSEHIS][1]);
				$worksheet->set_column(2,2,$heading_width[LICENSEHIS][2]);
				$worksheet->set_column(3,3,$heading_width[LICENSEHIS][3]);
				$worksheet->set_column(4,4,$heading_width[LICENSEHIS][4]);
			
				$xlsRow++;
				$worksheet->write($xlsRow,0,"ใบอนุญาตประกอบวิชาชีพ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,1,"ประเภท/ระดับของใบอนุญาต", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,2,"สาขา", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				$worksheet->write($xlsRow,3,"เลขที่ใบอนุญาต", set_format("xlsFmtTableHeader", "B", "C", "TLRB",1));
				$worksheet->write($xlsRow,4,"วันที่หมดอายุ", set_format("xlsFmtTableHeader", "B", "C", "TLRB", 1));
				break;				
		} // end switch case
	} // function		

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select			a.ORG_ID_1 as ORG_ID_1_ASS ,a.ORG_ID_2 as ORG_ID_2_ASS,a.ORG_ID as ORG_ID_ASS, a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
						from			PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f
						where		trim(a.PN_CODE)=trim(b.PN_CODE) and 
											a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and a.LEVEL_NO=f.LEVEL_NO(+)
											$search_condition
						order by		a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, a.LEVEL_NO, f.LEVEL_NAME, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_BIRTHDATE, 
											a.PER_SALARY, a.PER_MGTSALARY, a.PER_STARTDATE, a.PER_RETIREDATE,a.PER_OCCUPYDATE,
											a.PER_TYPE, a.PER_CARDNO,a.POS_ID, a.POEM_ID, a.POEMS_ID,
											a.OT_CODE,a.PN_CODE_F, a.PER_FATHERNAME, a.PER_FATHERSURNAME, a.PN_CODE_M, a.PER_MOTHERNAME, a.PER_MOTHERSURNAME,
											c.POS_NO_NAME, c.POS_NO, c.PL_CODE, c.PT_CODE, c.ORG_ID, c.ORG_ID_1, c.ORG_ID_2,
											d.POEM_NO_NAME as EMP_POS_NO_NAME, d.POEM_NO as EMP_POS_NO, d.PN_CODE as EMP_PL_CODE, 
											d.ORG_ID as EMP_ORG_ID, d.ORG_ID_1 as EMP_ORG_ID_1, d.ORG_ID_2 as EMP_ORG_ID_2,
											e.POEMS_NO_NAME as EMPSER_POS_NO_NAME, e.POEMS_NO as EMPSER_POS_NO, e.EP_CODE as EMPSER_PL_CODE, 
											e.ORG_ID as EMPSER_ORG_ID, e.ORG_ID_1 as EMPSER_ORG_ID_1, e.ORG_ID_2 as EMPSER_ORG_ID_2
				 from			PER_PRENAME b inner join 
				 				(
									(
										( 	
											(
										PER_PERSONAL a 
										left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
									) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
							) left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						) on (trim(a.PN_CODE)=trim(b.PN_CODE))
								$search_condition
				 order by			a.PER_NAME, a.PER_SURNAME ";
	}
	$count_data = $db_dpis->send_cmd($cmd);
        //echo "<pre>$cmd<br>";
        //die();
//	$db_dpis->show_error();
	if($count_data){
		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$xlsRow = $i;
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10,"", set_format("xlsFmtTitle", "B", "C", "", 1));
		}

		if($company_name){
			$xlsRow++;
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtSubTitle", "B", "L", "", 1));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		} // end if
	
                
                ob_start();
                
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$PER_ID = $data[PER_ID];
			$PER_TYPE = $data[PER_TYPE];
			
			if($PER_TYPE==1){
				$POS_ID = $data[POS_ID];
				$POS_NO = $data[POS_NO_NAME].$data[POS_NO];
				$PL_CODE = trim($data[PL_CODE]);
				$PT_CODE = trim($data[PT_CODE]);
				$ORG_ID = $data[ORG_ID];
				$ORG_ID_1 = $data[ORG_ID_1];
				$ORG_ID_2 = $data[ORG_ID_2];
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID  = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}

				$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]);

				$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE) = '$PT_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PT_NAME = trim($data2[PT_NAME]);
			}elseif($PER_TYPE==2){
				$POS_ID = $data[POEM_ID];
				$POS_NO = $data[EMP_POS_NO_NAME].$data[EMP_POS_NO];
				$PL_CODE = trim($data[EMP_PL_CODE]);
				$ORG_ID = $data[EMP_ORG_ID];
				$ORG_ID_1 = $data[EMP_ORG_ID_1];
				$ORG_ID_2 = $data[EMP_ORG_ID_2];
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID  = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}

				$cmd = " select PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PN_NAME]);
			}elseif($PER_TYPE==3){
				$POS_ID = $data[POEMS_ID];
				$POS_NO = $data[EMPSER_POS_NO_NAME].$data[EMPSER_POS_NO];
				$PL_CODE = trim($data[EMPSER_PL_CODE]);
				$ORG_ID = $data[EMPSER_ORG_ID];
				$ORG_ID_1 = $data[EMPSER_ORG_ID_1];
				$ORG_ID_2 = $data[EMPSER_ORG_ID_2];
				$ORG_ID_ASS = $data[ORG_ID_ASS];
				$ORG_ID_1_ASS = $data[ORG_ID_1_ASS];
				$ORG_ID_2_ASS = $data[ORG_ID_2_ASS];
				if($select_org_structure==1) { 
					$ORG_ID  = $ORG_ID_ASS;
					$ORG_ID_1 = $ORG_ID_1_ASS;
					$ORG_ID_2 = $ORG_ID_2_ASS;
				}

				$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE) = '$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[EP_NAME]);
			} 

			// ข้อมูลประเภทข้าราชการ
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);
			$ORG_NAME = "";
			if($ORG_ID){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_1 = "";
			if($ORG_ID_1){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_1 = trim($data2[ORG_NAME]);
			}
			$ORG_NAME_2 = "";
			if($ORG_ID_2){
			 $cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			 if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			 $db_dpis2->send_cmd($cmd);
//			 $db_dpis2->show_error();
			 $data2 = $db_dpis2->get_array();
			 $ORG_NAME_2 = trim($data2[ORG_NAME]);
			}
			$LEVEL_NO = trim($data[LEVEL_NO]);
			$LEVEL_NAME = trim(str_replace("ระดับ","",$data[LEVEL_NAME]));
			$PN_NAME = trim($data[PN_NAME]);
			$PER_NAME = trim($data[PER_NAME]);
			$PER_SURNAME = trim($data[PER_SURNAME]);
			$FULLNAME = ($PN_NAME)."$PER_NAME $PER_SURNAME";			
			$PER_CARDNO = $data[PER_CARDNO];			
			$img_file = "";
			if($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg")) $img_file = $IMG_PATH.$PER_CARDNO.".jpg";			

			$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE],$DATE_DISPLAY);
			$PER_RETIREDATE = show_date_format($data[PER_RETIREDATE],$DATE_DISPLAY);
			
			$PER_SALARY = $data[PER_SALARY];
			$PER_MGTSALARY = $data[PER_MGTSALARY];

			//วันบรรจุ
			$PER_STARTDATE = show_date_format($data[PER_STARTDATE],$DATE_DISPLAY);
			//วันที่เข้าส่วนราชการ
			$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE],$DATE_DISPLAY);
		
			// =====  ข้อมูลบิดา และมารดา =====
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=1 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_F = trim($data1[PN_CODE]);
			$PER_FATHERNAME = $data1[FML_NAME];
			$PER_FATHERSURNAME = $data1[FML_SURNAME];
			if (!$PER_FATHERNAME) {
				$PN_CODE_F = trim($data[PN_CODE_F]);
				$PER_FATHERNAME = trim($data[PER_FATHERNAME]);
				$PER_FATHERSURNAME = trim($data[PER_FATHERSURNAME]);
			}
			$PN_NAME_F = "";
			if($PN_CODE_F){
			 $cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_F ";
			 $db_dpis2->send_cmd($cmd);
			 $data_dpis2 = $db_dpis2->get_array();
			 $PN_NAME_F = trim($data_dpis2[PN_NAME]);
			}
			$FATHERNAME = ($PN_NAME_F)."$PER_FATHERNAME $PER_FATHERSURNAME";
		
			$cmd = "	select	PN_CODE, FML_NAME, FML_SURNAME
								from		PER_FAMILY
								where	PER_ID=$PER_ID and FML_TYPE=2 ";	
			$db_dpis2->send_cmd($cmd);
	//		$db_dpis2->show_error();
			$data1 = $db_dpis2->get_array();		
			$PN_CODE_M = trim($data1[PN_CODE]);
			$PER_MOTHERNAME = $data1[FML_NAME];
			$PER_MOTHERSURNAME = $data1[FML_SURNAME];
			if (!$$PER_MOTHERNAME) {
				$PN_CODE_M = trim($data[PN_CODE_M]);	
				$PER_MOTHERNAME = trim($data[PER_MOTHERNAME]);
				$PER_MOTHERSURNAME = trim($data[PER_MOTHERSURNAME]);
			}
			$PN_NAME_M = "";
			if($PN_CODE_M){
			 $cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE_M ";
			 $db_dpis2->send_cmd($cmd);
			 $data_dpis2 = $db_dpis2->get_array();
			 $PN_NAME_M = trim($data_dpis2[PN_NAME]);
			}
			$MOTHERNAME = ($PN_NAME_M)."$PER_MOTHERNAME $PER_MOTHERSURNAME";

			// =====  ข้อมูลคู่สมรส =====
			$SHOW_SPOUSE = "";
			$cmd = "	select 	PN_CODE, MAH_NAME, DV_CODE, MR_CODE 		
							from		PER_MARRHIS 
							where	PER_ID=$PER_ID 	
							order by	MAH_SEQ desc ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PN_CODE = trim($data_dpis2[PN_CODE]);
			$MAH_NAME = trim($data_dpis2[MAH_NAME]);
			$DV_CODE = trim($data_dpis2[DV_CODE]);
			$MR_CODE = trim($data_dpis2[MR_CODE]);
			if ($MR_CODE==2) {
				$cmd = "select PN_NAME from PER_PRENAME WHERE PN_CODE = $PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$PN_NAME=trim($data_dpis2[PN_NAME]);
                                $SHOW_SPOUSE = $PN_NAME.$MAH_NAME;
			}
			
			if (!$SHOW_SPOUSE) {
				$cmd = "	select PN_NAME, FML_NAME, FML_SURNAME from PER_FAMILY	a, PER_PRENAME b
									where a.PN_CODE = b.PN_CODE(+) and PER_ID=$PER_ID and FML_TYPE = 3 and (MR_CODE is NULL or trim(MR_CODE) not in ('3','4')) ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$SHOW_SPOUSE = trim($data_dpis2[PN_NAME]).trim($data_dpis2[FML_NAME])." ".trim($data_dpis2[FML_SURNAME]);
			}
			
			$worksheet->set_column(0, 0, 50);
			$worksheet->set_column(1,1, 50);
			$worksheet->set_column(2,2, 75);
			
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
			$worksheet->write($xlsRow, 0, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
										
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0, "ชื่อ  ".$FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1,"ชื่อคู่สมรส ".($SHOW_SPOUSE?$SHOW_SPOUSE:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2,"วันสั่งบรรจุ  ".($PER_STARTDATE?$PER_STARTDATE:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
	//		if($img_file){
	//			$image_x = ($pdf->x + 155);		$image_y = $pdf->y;		$image_w = 30;			$image_h = 35;
	//			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "TL", 0));
	//			$worksheet->insertBitmap($xlsRow, 4, "$img_file", 20, 10, $image_w, $image_h);
	//			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "TL", 0));
	//		} else {
				$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 0));
				$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 0));
	//		} // end if
		
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,"วัน เดือน ปี เกิด  ".($PER_BIRTHDATE?$PER_BIRTHDATE:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1,"ชื่อบิดา  ".($FATHERNAME?$FATHERNAME:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2,"วันเริ่มปฏิบัติราชการ  ".($PER_STARTDATE?$PER_STARTDATE:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			
			$xlsRow++;
			$worksheet->write_string($xlsRow, 0,"วันเกษียณอายุ ".($PER_RETIREDATE?$PER_RETIREDATE:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 1,"ชื่อมารดา  ".($MOTHERNAME?$MOTHERNAME:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write_string($xlsRow, 2,"ประเภท".($OT_NAME?$OT_NAME:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
			$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "L", "TBR", 0));
			$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "L", "", 0));
			$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "L", "", 0));

//		$pdf->AutoPageBreak = false;

			//สำหรับ กพ. 7
			$ORG_NAME_1 = "";		$ORG_NAME_2 = "";
			$POH_EFFECTIVEDATE = "";	$PL_NAME = "";	$TMP_PL_NAME = "";
			$LEVEL_NO = "";	$POH_POS_NO = "";	$POH_SALARY = "";
			$SAH_EFFECTIVEDATE = "";	$SAH_SALARY = "";	$MOV_NAME = "";
			$POH_UNDER_ORG1 =""; $POH_UNDER_ORG2 ="";$POH_UNDER_ORG3 ="";$POH_UNDER_ORG4 ="";$POH_UNDER_ORG5 ="";$POH_ASS_ORG1="";

			//########################
			//ประวัติการดำรงตำแหน่งข้าราชการ
			//########################
			if($DPISDB=="odbc"){
				$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_ASS_ORG, a.POH_EFFECTIVEDATE, a.MOV_CODE, d.PL_NAME, i.PN_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME,
													a.EP_CODE, a.TP_CODE, a.POH_REMARK, a.POH_SEQ_NO
								   from	(
												(
													(
														(
															PER_POSITIONHIS a
															left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
														) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
													) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
											)  left join PER_POS_NAME i on (a.PN_CODE=i.PN_CODE)
								   where		a.PER_ID=$PER_ID
								   /*order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, a.POH_SEQ_NO desc*/ ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = "select a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_UNDER_ORG1, a.POH_UNDER_ORG2,a.POH_UNDER_ORG3,a.POH_UNDER_ORG4,a.POH_UNDER_ORG5,
											a.POH_ASS_ORG, a.POH_ASS_ORG1, a.POH_ASS_ORG2, a.POH_ASS_ORG3, a.POH_ASS_ORG4, a.POH_ASS_ORG5, a.POH_EFFECTIVEDATE, a.MOV_CODE, d.PL_NAME, i.PN_NAME, 
                                            a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME,
                                            a.EP_CODE, a.TP_CODE, a.POH_REMARK, a.POH_SEQ_NO,a.POH_DOCDATE,a.POH_PL_NAME,a.POH_PM_NAME
                                        from PER_POSITIONHIS a, PER_LINE d, PER_TYPE e, PER_MGT f, PER_LEVEL g, PER_POS_NAME i
                                        where	a.PER_ID=$PER_ID and a.PL_CODE=d.PL_CODE(+) 
                                            and a.PT_CODE=e.PT_CODE(+) 
                                            and  a.PM_CODE=f.PM_CODE(+) 
                                            and a.LEVEL_NO=g.LEVEL_NO(+) 
                                            and a.PN_CODE=i.PN_CODE(+)
                                        /*order by	SUBSTR(a.POH_EFFECTIVEDATE,1,10) desc, a.POH_SEQ_NO desc*/ ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select		a.POH_ORG2 as ORG_NAME_1, a.POH_ORG3 as ORG_NAME_2, a.POH_ASS_ORG, a.POH_EFFECTIVEDATE, a.MOV_CODE, d.PL_NAME, i.PN_NAME, 
													a.LEVEL_NO, g.LEVEL_NAME, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_SALARY, a.POH_DOCNO, a.PT_CODE, e.PT_NAME, a.PM_CODE, f.PM_NAME,
													a.EP_CODE, a.TP_CODE, a.POH_REMARK, a.POH_SEQ_NO
								   from			(
														(
															(
															PER_POSITIONHIS a
															left join PER_LINE d on (a.PL_CODE=d.PL_CODE)
														) left join PER_TYPE e on (a.PT_CODE=e.PT_CODE)
													) left join PER_MGT f on (a.PM_CODE=f.PM_CODE)
												) left join PER_LEVEL g on (a.LEVEL_NO=g.LEVEL_NO)
								   where		a.PER_ID=$PER_ID
								   /*order by	LEFT(a.POH_EFFECTIVEDATE,10) desc, a.POH_SEQ_NO desc*/ ";
			} // end if
			$count_positionhis = $db_dpis2->send_cmd($cmd);
			//echo "<pre>";
			//die($cmd);
//							$db_dpis2->show_error();
			if($count_positionhis){
				$positionhis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$positionhis_count++;
					$POH_EFFECTIVEDATE = trim(substr($data2[POH_EFFECTIVEDATE],0,10));
					$POH_EFFECTIVEDATE = ($POH_EFFECTIVEDATE?$POH_EFFECTIVEDATE:"-");
					$LEVEL_NO = trim($data2[LEVEL_NO]);
					if( ("$PM_CODE $PL_NAME $LEVEL_NO $POH_POS_NO") != (trim($data2[PM_CODE])." ".trim($data2[PL_NAME])." ".$LEVEL_NO." ".trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]))){
						$PL_NAME = trim($data2[PL_NAME]);
						$PN_NAME = trim($data2[PN_NAME]);
						$LEVEL_NAME = trim(str_replace("ระดับ","",$data2[LEVEL_NAME]));
						$PT_CODE = trim($data2[PT_CODE]);
						$PT_NAME = trim($data2[PT_NAME]);
						$PM_CODE = trim($data2[PM_CODE]);
						$PM_NAME = trim($data2[PM_NAME]);
						$EP_CODE = trim($data2[EP_CODE]);
						$TP_CODE = trim($data2[TP_CODE]);
						$POH_REMARK = trim($data2[POH_REMARK]);
                                                
                                                //  Release 5.2.1.21
                                                $POH_PL_NAME = trim($data2[POH_PL_NAME]);
                                                $POH_PM_NAME = trim($data2[POH_PM_NAME]);
                                                // End  Release 5.2.1.21
                                                
						//$TMP_PL_NAME = (trim($PL_NAME)?($PL_NAME ." ". $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME);
						if($PL_NAME) {  
							$TMP_PL_NAME = trim($PL_NAME); 
						} elseif($PN_NAME) { 
							$TMP_PL_NAME = trim($PN_NAME); 
						} elseif($EP_CODE) { 
							$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$EP_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$TMP_PL_NAME = $data3[EP_NAME];
						} elseif($TP_CODE) { 
							$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
							$db_dpis3->send_cmd($cmd);
							$data3 = $db_dpis3->get_array();
							$TMP_PL_NAME = $data3[TP_NAME];
						}else{   //Release 5.2.1.21
                                                        $TMP_PL_NAME = (trim($POH_PL_NAME)?($POH_PL_NAME . $LEVEL_NAME . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):$LEVEL_NAME);
                                                        if($POH_PM_NAME){
                                                            $TMP_PL_NAME = $POH_PM_NAME."\n($TMP_PL_NAME)"; 
                                                        }
                                                } // End  Release 5.2.1.21
						if($PM_CODE && $PM_CODE!="9999") $TMP_PL_NAME = $PM_NAME." ($TMP_PL_NAME)";
					}else{
                                            
                    }
					
					$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
					$POH_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);	
					$POH_POS_NO = ($POH_POS_NO?$POH_POS_NO:"-");
					$POH_SALARY = number_format($data2[POH_SALARY]);		
					$POH_SALARY = ($POH_SALARY?$POH_SALARY:"-");
					$POH_DOCNO = (trim($data2[POH_DOCNO]))? $data2[POH_DOCNO] : "-" ;
					
					$POH_ORG_NAME_2 = $data2[ORG_NAME_1];//กรม
					$POH_ORG_NAME_3 = $data2[ORG_NAME_2];//สำนักกอง ตามกฎหมาย
					$POH_UNDER_ORG1 = trim($data2[POH_UNDER_ORG1]);//สำนักกอง ตามกฎหมาย ตำ่กว่า 1ระดับ
					$POH_UNDER_ORG2 = trim($data2[POH_UNDER_ORG2]);//สำนักกอง ตามกฎหมาย ตำ่กว่า 2ระดับ
					$POH_UNDER_ORG3 = trim($data2[POH_UNDER_ORG3]);//สำนักกอง ตามกฎหมาย ตำ่กว่า 3ระดับ
					$POH_UNDER_ORG4 = trim($data2[POH_UNDER_ORG4]);//สำนักกอง ตามกฎหมาย ตำ่กว่า 4ระดับ
					$POH_UNDER_ORG5 = trim($data2[POH_UNDER_ORG5]);//สำนักกอง ตามกฎหมาย ตำ่กว่า 5ระดับ
					$POH_ORGASS_NAME_3 = $data2[POH_ASS_ORG];//สำนักกอง ตามมอบหมาย
					$POH_ASS_ORG1 = trim($data2[POH_ASS_ORG1]);//สำนักกอง ตามมอบหมาย ตำ่กว่า 1ระดับ
					$POH_ASS_ORG2 = trim($data2[POH_ASS_ORG2]);//สำนักกอง ตามมอบหมาย ตำ่กว่า 2ระดับ
					$POH_ASS_ORG3 = trim($data2[POH_ASS_ORG3]);//สำนักกอง ตามมอบหมาย ตำ่กว่า 3ระดับ
					$POH_ASS_ORG4 = trim($data2[POH_ASS_ORG4]);//สำนักกอง ตามมอบหมาย ตำ่กว่า 4ระดับ
					$POH_ASS_ORG5 = trim($data2[POH_ASS_ORG5]);//สำนักกอง ตามมอบหมาย ตำ่กว่า 5ระดับ
					//echo $POH_ORG_NAME_2."|".$POH_UNDER_ORG1."|".$POH_UNDER_ORG2."|".$POH_UNDER_ORG3."|".$POH_UNDER_ORG4."|".$POH_UNDER_ORG5;
					//die();
	
					$MOV_CODE = trim($data2[MOV_CODE]);
                                        
                                        $POH_DOCDATE = trim(substr($data2[POH_DOCDATE],0,10));
					$POH_DOCDATE = ($POH_DOCDATE?$POH_DOCDATE:"-");

					//หาประเภทการเคลื่อนไหวของประวัติการดำรงตำแหน่งข้าราชการ
					$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
					$db_dpis3->send_cmd($cmd);
					//echo "<br>$cmd<br>";
					//$db_dpis3->show_error();
					$data3 = $db_dpis3->get_array();
					$MOV_NAME = $data3[MOV_NAME];
                                        
                                        
					
					$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
					//เก็บลง array ของ POSTION HIS
                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                            $ARR_POSITIONHIS[$PER_ID][] = array(
                                                'DOCDATE'=>$POH_DOCDATE,
                                                'DATE'=>$POH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$POH_SALARY,
                                                'DOC_NO'=>$POH_DOCNO,
                                                'POH_ORG_NAME_2'=>$POH_ORG_NAME_2,
                                                'POH_ORG_NAME_3'=>$POH_ORG_NAME_3 ,
												'POH_UNDER_ORG1'=>$POH_UNDER_ORG1 ,
												'POH_UNDER_ORG2'=>$POH_UNDER_ORG2 ,
												'POH_UNDER_ORG3'=>$POH_UNDER_ORG3 ,
												'POH_UNDER_ORG4'=>$POH_UNDER_ORG4 ,
												'POH_UNDER_ORG5'=>$POH_UNDER_ORG5 ,
                                                'POH_ORGASS_NAME_3'=>$POH_ORGASS_NAME_3,
												'POH_ASS_ORG1'=>$POH_ASS_ORG1,
												'POH_ASS_ORG2'=>$POH_ASS_ORG2,
												'POH_ASS_ORG3'=>$POH_ASS_ORG3,
												'POH_ASS_ORG4'=>$POH_ASS_ORG4,
												'POH_ASS_ORG5'=>$POH_ASS_ORG5,
                                                'REMARK'=>$POH_REMARK
                                            );
                                        }else{
                                            $ARR_POSITIONHIS[$PER_ID][] = array(
                                                'DATE'=>$POH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$POH_SALARY,
                                                'DOC_NO'=>$POH_DOCNO,
                                                'POH_ORG_NAME_2'=>$POH_ORG_NAME_2,
                                                'POH_ORG_NAME_3'=>$POH_ORG_NAME_3 ,
												'POH_UNDER_ORG1'=>$POH_UNDER_ORG1 ,
												'POH_UNDER_ORG2'=>$POH_UNDER_ORG2 ,
												'POH_UNDER_ORG3'=>$POH_UNDER_ORG3 ,
												'POH_UNDER_ORG4'=>$POH_UNDER_ORG4 ,
												'POH_UNDER_ORG5'=>$POH_UNDER_ORG5 ,
                                                'POH_ORGASS_NAME_3'=>$POH_ORGASS_NAME_3,
												'POH_ASS_ORG1'=>$POH_ASS_ORG1,
												'POH_ASS_ORG1'=>$POH_ASS_ORG1,
												'POH_ASS_ORG2'=>$POH_ASS_ORG2,
												'POH_ASS_ORG3'=>$POH_ASS_ORG3,
												'POH_ASS_ORG4'=>$POH_ASS_ORG4,
												'POH_ASS_ORG5'=>$POH_ASS_ORG5,
                                                'REMARK'=>$POH_REMARK
                                            );
                                        }
					//echo "<pre>";					
					//print_r($ARR_POSITIONHIS[$PER_ID]);
				} // end while
			} //end if 
		

//die();
			//########################
			//ประวัติการเลื่อนขั้นเงินเดือน
			//########################
			if($DPISDB=="odbc"){
				$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
													b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_REMARK, 1 AS POH_SEQ_NO
								 from			PER_SALARYHIS b
								 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
								 where		b.PER_ID=$PER_ID
								 /*order by		LEFT(b.SAH_EFFECTIVEDATE,10) desc*/ ";							   
			}elseif($DPISDB=="oci8"){
				$cmd = " select	SUBSTR(b.SAH_EFFECTIVEDATE,1,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
                                            b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_REMARK, 1 AS POH_SEQ_NO,b.SAH_DOCDATE 
                                         from PER_SALARYHIS b, PER_MOVMENT c 
                                         where b.PER_ID=$PER_ID and b.MOV_CODE=c.MOV_CODE 
                                                /*order by		b.SAH_EFFECTIVEDATE desc*/ ";		   					   
			}elseif($DPISDB=="mysql"){
				$cmd = " select			LEFT(b.SAH_EFFECTIVEDATE,10) as SAH_EFFECTIVEDATE, c.MOV_NAME, b.SAH_SALARY, b.SAH_DOCNO, 
													b.LEVEL_NO, b.SAH_POSITION, b.SAH_POS_NO_NAME, b.SAH_POS_NO, b.SAH_REMARK, 1 AS POH_SEQ_NO
								 from			PER_SALARYHIS b
								 inner join 	PER_MOVMENT c	 on	b.MOV_CODE=c.MOV_CODE					
								 where		b.PER_ID=$PER_ID
								 /*order by		b.SAH_EFFECTIVEDATE desc*/ ";
			} // end if
			$count_salaryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
			
			if($count_salaryhis){
				$salaryhis_count = 0;
				while($data2 = $db_dpis2->get_array()){
					$salaryhis_count++;
					$SAH_EFFECTIVEDATE = trim($data2[SAH_EFFECTIVEDATE]);
					$SAH_EFFECTIVEDATE = ($SAH_EFFECTIVEDATE?$SAH_EFFECTIVEDATE:"-");
					$MOV_NAME = trim($data2[MOV_NAME]);		
					$MOV_NAME = (trim($MOV_NAME)?trim($MOV_NAME):"-");
					$SAH_SALARY = number_format($data2[SAH_SALARY]);		
					$SAH_SALARY = ($SAH_SALARY?$SAH_SALARY:"-");
					$SAH_DOCNO = (trim($data2[SAH_DOCNO]))? $data2[SAH_DOCNO] : "-" ;
					$POH_POS_NO = trim($data2[SAH_POS_NO_NAME]).trim($data2[SAH_POS_NO]);				
					$POH_POS_NO =($POH_POS_NO?$POH_POS_NO:"-");
					$LEVEL_NO = trim($data2[LEVEL_NO]);	
					$TMP_PL_NAME = trim($data2[SAH_POSITION]);	
					$TMP_PL_NAME = (trim($TMP_PL_NAME)?trim($TMP_PL_NAME):"-");
					$SAH_REMARK = trim($data2[SAH_REMARK]);	
					$POH_SEQ_NO = trim($data2[POH_SEQ_NO]);
                                        
					$SAH_DOCDATE = trim($data2[SAH_DOCDATE]);
					$SAH_DOCDATE = ($SAH_DOCDATE?$SAH_DOCDATE:"-");
					//เก็บลง array ของ SALARYHIS
                                        if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                            $ARR_SALARYHIS[$PER_ID][] = array(
                                                'DOCDATE'=>$SAH_DOCDATE,
                                                'DATE'=>$SAH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$SAH_SALARY,
                                                'DOC_NO'=>$SAH_DOCNO,
                                                'REMARK'=>$SAH_REMARK
                                            );
                                        }else{
                                            $ARR_SALARYHIS[$PER_ID][] = array(
                                                'DATE'=>$SAH_EFFECTIVEDATE,
                                                'POH_SEQ_NO'=>$POH_SEQ_NO,
                                                'MOVE'=>$MOV_NAME,
                                                'POS_NAME'=>$TMP_PL_NAME,
                                                'POS_NO'=>$POH_POS_NO,
                                                'LEVEL'=>$LEVEL_NO,
                                                'SALARY'=>$SAH_SALARY,
                                                'DOC_NO'=>$SAH_DOCNO,
                                                'REMARK'=>$SAH_REMARK
                                            );
                                        }
                                        																			
				} // end while
			}// end if

			//######################################
			//รวมประวัติการดำรงตำแหน่ง + การเลื่อนขั้นเงินเดือน
			//######################################
			//array_multisort($ARR_POSITIONHIS[$PER_ID], SORT_ASC, $ARR_SALARYHIS[$PER_ID], SORT_ASC);
			//$ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID] , $ARR_SALARYHIS[$PER_ID]);
                        $ARRAY_POH_SAH[$PER_ID] = array_merge_recursive($ARR_POSITIONHIS[$PER_ID]);
                        
                        $ARRAY_SALARYHIS[$PER_ID] = $ARR_SALARYHIS[$PER_ID];/*Release 5.2.1.8 */
			unset($ARR_POSITIONHIS);
			unset($ARR_SALARYHIS);
								
			// เรียงข้อมูล ตามวันที่ / เงินเดือน น้อยไปมาก
			/*print("<pre>");
			print_r($ARRAY_POH_SAH);
			print("</pre>");*/
			/*foreach ($ARRAY_POH_SAH[$PER_ID] as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
				$DATE[$key]  = $value['DATE'];
				$MOVE[$key]  = $value['MOVE'];
				$POS_NO[$key]  = $value['POS_NO'];
				$LEVEL[$key]  = $value['LEVEL'];
				$SALARY[$key] = $value['SALARY'];
				$DOC_NO[$key]  = $value['DOC_NO'];
			} // end foreach
			array_multisort($DATE, SORT_ASC, $SALARY, SORT_ASC, $ARRAY_POH_SAH);*/
                        
                        /**/
                        for($in=0; $in < count($ARRAY_SALARYHIS[$PER_ID]);$in++){
				//เก็บค่าวันที่
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DATE_SALARYHIS[] = array(
                                    'DOCDATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOCDATE'],
                                    'DATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DATE'],
                                    'POH_SEQ_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_SEQ_NO'],
                                    'MOVE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['MOVE'],
                                    'POS_NAME'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NAME'],
                                    'POS_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NO'],
                                    'LEVEL'=>$ARRAY_SALARYHIS[$PER_ID][$in]['LEVEL'],
                                    'SALARY'=>$ARRAY_SALARYHIS[$PER_ID][$in]['SALARY'],
                                    'DOC_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOC_NO'],
                                    'POH_ORG_NAME_2'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_2'],
                                    'POH_ORG_NAME_3'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_3'],
                                    'POH_ORGASS_NAME_3'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORGASS_NAME_3'],
                                    'REMARK'=>$ARRAY_SALARYHIS[$PER_ID][$in]['REMARK']
                                );
                            }else{
                                $DATE_SALARYHIS[] = array('DATE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DATE'],
                                    'POH_SEQ_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_SEQ_NO'],
                                    'MOVE'=>$ARRAY_SALARYHIS[$PER_ID][$in]['MOVE'],
                                    'POS_NAME'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NAME'],
                                    'POS_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POS_NO'],
                                    'LEVEL'=>$ARRAY_SALARYHIS[$PER_ID][$in]['LEVEL'],
                                    'SALARY'=>$ARRAY_SALARYHIS[$PER_ID][$in]['SALARY'],
                                    'DOC_NO'=>$ARRAY_SALARYHIS[$PER_ID][$in]['DOC_NO'],
                                    'POH_ORG_NAME_2'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_2'],
                                    'POH_ORG_NAME_3'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_3'],
                                    'POH_ORGASS_NAME_3'=>$ARRAY_SALARYHIS[$PER_ID][$in]['POH_ORGASS_NAME_3'],
                                    'REMARK'=>$ARRAY_SALARYHIS[$PER_ID][$in]['REMARK']
                                );
                            }
				
			} // end for
			unset($ARRAY_SALARYHIS);
			//sort($DATE_HIS);
			array_multisort($DATE_HIS, SORT_DESC);
			foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DOCDATE[$key]  = $value['DOCDATE'];
                            }
                            $DATE[$key]  = $value['DATE'];
                            $POH_SEQ_NO[$key]  = $value['POH_SEQ_NO'];
                            $MOVE[$key]  = $value['MOVE'];
                            $POS_NAME[$key] = $value['POS_NAME'];
                            $POS_NO[$key]  = $value['POS_NO'];
                            $LEVEL[$key]  = $value['LEVEL'];
                            $SALARY[$key] = $value['SALARY'];
                            $DOC_NO[$key]  = $value['DOC_NO'];
                            $POH_ORG_NAME_2[$key]  = $value['POH_ORG_NAME_2'];
                            $POH_ORG_NAME_3[$key] = $value['POH_ORG_NAME_3'];
                            $POH_ORGASS_NAME_3[$key]  = $value['POH_ORGASS_NAME_3'];
                            $REMARK[$key] = $value['REMARK'];
				
			} // end foreach
			if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                            array_multisort($DOCDATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }else{
                            array_multisort($DATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }
			
			$POH_SALARYHIS[$PER_ID]=$DATE_SALARYHIS;
			unset($DATE_SALARYHIS);
                        /*xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/
                        
                        
                        
			for($in=0; $in < count($ARRAY_POH_SAH[$PER_ID]);$in++){
				//เก็บค่าวันที่
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DATE_HIS[] = array(
                                    'DOCDATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOCDATE'],
                                    'DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                    'POH_SEQ_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_SEQ_NO'],
                                    'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                    'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                    'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                    'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                    'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                    'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO'],
                                    'POH_ORG_NAME_2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORG_NAME_2'],
                                    'POH_ORG_NAME_3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORG_NAME_3'],
									'POH_UNDER_ORG1'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG1'],
									'POH_UNDER_ORG2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG2'],
									'POH_UNDER_ORG3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG3'],
									'POH_UNDER_ORG4'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG4'],
									'POH_UNDER_ORG5'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG5'],
                                    'POH_ORGASS_NAME_3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORGASS_NAME_3'],
									'POH_ASS_ORG1'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG1'],
									'POH_ASS_ORG2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG2'],
									'POH_ASS_ORG3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG3'],
									'POH_ASS_ORG4'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG4'],
									'POH_ASS_ORG5'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG5'],
                                    'REMARK'=>$ARRAY_POH_SAH[$PER_ID][$in]['REMARK']
                                );
                            }else{
                                $DATE_HIS[] = array('DATE'=>$ARRAY_POH_SAH[$PER_ID][$in]['DATE'],
                                    'POH_SEQ_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_SEQ_NO'],
                                    'MOVE'=>$ARRAY_POH_SAH[$PER_ID][$in]['MOVE'],
                                    'POS_NAME'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NAME'],
                                    'POS_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['POS_NO'],
                                    'LEVEL'=>$ARRAY_POH_SAH[$PER_ID][$in]['LEVEL'],
                                    'SALARY'=>$ARRAY_POH_SAH[$PER_ID][$in]['SALARY'],
                                    'DOC_NO'=>$ARRAY_POH_SAH[$PER_ID][$in]['DOC_NO'],
                                    'POH_ORG_NAME_2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORG_NAME_2'],
                                    'POH_ORG_NAME_3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORG_NAME_3'],
									'POH_UNDER_ORG1'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG1'],
									'POH_UNDER_ORG2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG2'],
									'POH_UNDER_ORG3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG3'],
									'POH_UNDER_ORG4'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG4'],
									'POH_UNDER_ORG5'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_UNDER_ORG5'],
                                    'POH_ORGASS_NAME_3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ORGASS_NAME_3'],
									'POH_ASS_ORG1'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG1'],
									'POH_ASS_ORG2'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG2'],
									'POH_ASS_ORG3'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG3'],
									'POH_ASS_ORG4'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG4'],
									'POH_ASS_ORG5'=>$ARRAY_POH_SAH[$PER_ID][$in]['POH_ASS_ORG5'],
                                    'REMARK'=>$ARRAY_POH_SAH[$PER_ID][$in]['REMARK']
                                );
                            }
				
			} // end for
			unset($ARRAY_POH_SAH);
			//sort($DATE_HIS);
			array_multisort($DATE_HIS, SORT_DESC);
			foreach ($DATE_HIS as $key => $value) {		//กรณีที่วันที่เดียวกัน แต่ต้องเอาเงินเดือนน้อยกว่าแสดงก่อน
                            if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                                $DOCDATE[$key]  = $value['DOCDATE'];
                            }
                            $DATE[$key]  = $value['DATE'];
                            $POH_SEQ_NO[$key]  = $value['POH_SEQ_NO'];
                            $MOVE[$key]  = $value['MOVE'];
                            $POS_NAME[$key] = $value['POS_NAME'];
                            $POS_NO[$key]  = $value['POS_NO'];
                            $LEVEL[$key]  = $value['LEVEL'];
                            $SALARY[$key] = $value['SALARY'];
                            $DOC_NO[$key]  = $value['DOC_NO'];
                            $POH_ORG_NAME_2[$key]  = $value['POH_ORG_NAME_2'];
                            $POH_ORG_NAME_3[$key] = $value['POH_ORG_NAME_3'];
                            $POH_ORGASS_NAME_3[$key]  = $value['POH_ORGASS_NAME_3'];
                            $REMARK[$key] = $value['REMARK'];
				
			} // end foreach
			if($chk_order_date_command=='Y'){ //ต้องการเรียงลำดับตามวันที่ลงนามในคำสั่ง http://dpis.ocsc.go.th/Service/node/1304
                            array_multisort($DOCDATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }else{
                            array_multisort($DATE, SORT_DESC,$POH_SEQ_NO, SORT_DESC, $DATE_HIS);
                        }
			
			$POH_SAH_HIS[$PER_ID]=$DATE_HIS;
			unset($DATE_HIS);
			/***print("<pre>");
			print_r($POH_SAH_HIS);
			print("</pre>");***/
			###IN CASE POSITIONHIS #######
			/////////////////////////////////////////////////////////////////
                           $no_index=0;
			for($history_index=0; $history_index<count($arr_history_name); $history_index++){
                       
				$HISTORY_NAME = $arr_history_name[$history_index];
				//set header width
				$xlsRow++;
				for($hw=0;	 $hw < count($heading_width[$HISTORY_NAME]); $hw++){
					$worksheet->set_column($hw, $hw, $heading_width[$HISTORY_NAME][$hw]);
					//เพิ่มช่องว่างบรรทัดใหม่
					$worksheet->write($xlsRow,$hw, "", set_format("xlsFmtTitle", "B", "C", "", 1));
				}
			switch($HISTORY_NAME){
				case "POSITIONHIS" : //รวมประวัติรับราชการ + เลื่อนขั้นเงินเดือนเข้าด้วยกัน
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรับราชการ ", set_format("xlsFmtSubTitle", "B", "L", "", 0));	// 0 สุดท้ายทำให้หัวข้อชิดซ้าย
							print_header($HISTORY_NAME);

	   					//ส่วนแสดงเนื้อหา หลังจากจัดเรียงข้อมูลแล้ว
						if(is_array($POH_SAH_HIS)  && !empty($POH_SAH_HIS)){
							$count_positionhis=count($POH_SAH_HIS[$PER_ID]);
							$positionhis_count=0;
							 for($in=0; $in < count($POH_SAH_HIS[$PER_ID]);$in++){
							 		$positionhis_count++;
									if($POH_SAH_HIS[$PER_ID][$in]['DATE']){
										$DATE_POH_SAH = show_date_format(substr($POH_SAH_HIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
									}
									//หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
									$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='".$POH_SAH_HIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";						
									//echo "<br>$cmd<br>";
									$db_dpis2->send_cmd($cmd);
									$data2 = $db_dpis2->get_array();
									$LEVEL_NAME = trim($data2[LEVEL_NAME]);
									$arr_temp = explode(" ", $LEVEL_NAME);
									//หาชื่อตำแหน่งประเภท
									$POSITION_TYPE="";
									if(strstr($LEVEL_NAME, 'ประเภท') == TRUE) {
										$POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
									}elseif(strstr($LEVEL_NAME, 'กลุ่มงาน') == TRUE) {
										$POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
									}else{
										if($arr_temp[0] != 'ระดับ'){
											$POSITION_TYPE = $arr_temp[0];
										}
									}
									//หาชื่อระดับตำแหน่ง 
									$LEVEL_NAME ="";
									if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
										$LEVEL_NAME =  str_replace("ระดับ", "", $arr_temp[1]);
									}else{
										$LEVEL_NAME =  level_no_format($arr_temp[1]);
									}
									//--------------------------------------------------------------------
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$DATE_POH_SAH, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); //$POH_SAH_HIS[$PER_ID][$in]['DATE']
									//$worksheet->write_string($xlsRow,1,$POH_SAH_HIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 	//ตำแหน่ง   /*เดิม*/
                                    $worksheet->write_string($xlsRow,1,$POH_SAH_HIS[$PER_ID][$in]['POS_NAME'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 	//ตำแหน่ง /*Release 5.2.1.8 */
									$worksheet->write_string($xlsRow,2,$POH_SAH_HIS[$PER_ID][$in]['MOVE'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));//$POH_SAH_HIS[$PER_ID][$in]['MOVE'] //ประเภทการเคลื่อนไหว
									$worksheet->write_string($xlsRow,3,$POH_SAH_HIS[$PER_ID][$in]['POS_NO'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));									
									$worksheet->write_string($xlsRow,4,($LEVEL_NAME?$LEVEL_NAME:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,5,$POH_SAH_HIS[$PER_ID][$in]['SALARY'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,6,$POH_SAH_HIS[$PER_ID][$in]['DOC_NO'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 
									$worksheet->write_string($xlsRow,7,$POH_SAH_HIS[$PER_ID][$in]['POH_ORG_NAME_2'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,8,$POH_SAH_HIS[$PER_ID][$in]['POH_ORG_NAME_3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,9,$POH_SAH_HIS[$PER_ID][$in]['POH_UNDER_ORG1'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,10,$POH_SAH_HIS[$PER_ID][$in]['POH_UNDER_ORG2'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,11,$POH_SAH_HIS[$PER_ID][$in]['POH_UNDER_ORG3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,12,$POH_SAH_HIS[$PER_ID][$in]['POH_UNDER_ORG4'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,13,$POH_SAH_HIS[$PER_ID][$in]['POH_UNDER_ORG5'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,14,$POH_SAH_HIS[$PER_ID][$in]['POH_ORGASS_NAME_3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,15,$POH_SAH_HIS[$PER_ID][$in]['POH_ASS_ORG1'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,16,$POH_SAH_HIS[$PER_ID][$in]['POH_ASS_ORG2'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,17,$POH_SAH_HIS[$PER_ID][$in]['POH_ASS_ORG3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,18,$POH_SAH_HIS[$PER_ID][$in]['POH_ASS_ORG4'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,19,$POH_SAH_HIS[$PER_ID][$in]['POH_ASS_ORG5'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,20,$POH_SAH_HIS[$PER_ID][$in]['REMARK'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 
								} //end for
                                                                unset($data2);
							}
							if($positionhis_count<=0){	//}else{
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 11, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 12, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if	
						break;
				case "EDUCATE" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow, 0,($no_index)."  ประวัติการศึกษา ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);
							
							$EDU_PERIOD="";	$EN_NAME = "";	$EM_NAME = "";	$INS_NAME = "";	$CT_NAME = "";
							if($DPISDB=="odbc"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE , a.EDU_HONOR , a.ST_CODE
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE , a.EDU_HONOR , a.ST_CODE
										from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
										where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and 
													a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) and 
                                                                                                        TRIM(a.CT_CODE_EDU)= TRIM(e.CT_CODE(+))
										order by		a.EDU_SEQ DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " 	select		a.EDU_STARTYEAR, a.EDU_ENDYEAR,b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME, a.EDU_INSTITUTE , a.EDU_HONOR , a.ST_CODE
									 	from			(((PER_EDUCATE a
													left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
													) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
													) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
													) left join PER_COUNTRY e on (a.CT_CODE_EDU=e.CT_CODE)
										 where		a.PER_ID=$PER_ID
										 order by		a.EDU_SEQ DESC ";			
							} // end if
							$count_educatehis = $db_dpis2->send_cmd($cmd);
                                                        //echo "demo=>>>".$cmd;
//							$db_dpis2->show_error();
							if($count_educatehis){
								$educatehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$educatehis_count++;
                                                                        $EDU_PERIOD='';
									$EDU_STARTYEAR = trim($data2[EDU_STARTYEAR]);
									if($EDU_STARTYEAR){	$EDU_PERIOD=$EDU_STARTYEAR;	}
									$EDU_ENDYEAR =  trim($data2[EDU_ENDYEAR]);
									if($EDU_PERIOD!="" && $EDU_ENDYEAR){
										$EDU_PERIOD.=" - $EDU_ENDYEAR";
									}else{
										$EDU_PERIOD.=$EDU_ENDYEAR;
									}

									$EN_NAME = trim($data2[EN_NAME]);
									$EM_NAME = trim($data2[EM_NAME]);
									if($EM_NAME!=""){ $EM_NAME="($EM_NAME)"; }
									$INS_NAME = trim($data2[INS_NAME]);
									if (!$INS_NAME) $INS_NAME = trim($data2[EDU_INSTITUTE]);
									$CT_NAME = trim($data2[CT_NAME]);
                                                                        //เพิ่มใหม่ http://dpis.ocsc.go.th/Service/node/2258
                                                                        $EDU_HONOR = trim($data2[EDU_HONOR]);
                                                                        $ST_CODE = trim($data2[ST_CODE]);
                                                                        $cmd = "select ST_NAME from PER_SCHOLARTYPE where ST_CODE='".$ST_CODE."'";
                                                                        $db_dpis4->send_cmd($cmd);
                                                                        $data_st = $db_dpis4->get_array();
                                                                        $ST_NAME = trim($data_st[ST_NAME]);
                                                                        //END เพิ่มใหม่

									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$INS_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,1,"$EDU_PERIOD", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
									$worksheet->write_string($xlsRow,2,"$EN_NAME  $EM_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                                                        $worksheet->write_string($xlsRow,3,"$CT_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                                                        $worksheet->write_string($xlsRow,4,"$EDU_HONOR ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                                                        $worksheet->write_string($xlsRow,5,"$ST_NAME ", set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                                                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                                                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                                                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                                                
							} // end if
						break;
			case "TRAINING" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)." ประวัติการฝึกอบรม/สัมมนา/ดูงาน ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$TRN_DURATION = "";	$TRN_STARTDATE = "";	$TRN_ENDDATE = "";	$TR_NAME = "";		$TRN_PLACE = "";
							if($DPISDB=="odbc"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
												 order by	a.TRN_STARTDATE DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select		a.TRN_STARTDATE, a.TRN_ENDDATE,a.TRN_ORG, b.TR_NAME, a.TRN_PLACE, a.TRN_FUND, a.TRN_COURSE_NAME, a.TRN_NO
												 from			PER_TRAINING a, PER_TRAIN b
												 where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE
												 order by	a.TRN_STARTDATE DESC ";							   
							}
							$count_traininghis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_traininghis){
								$traininghis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$traininghis_count++;
									$TRN_STARTDATE = show_date_format($data2[TRN_STARTDATE],$DATE_DISPLAY);
									$TRN_ENDDATE = show_date_format($data2[TRN_ENDDATE],$DATE_DISPLAY);
									if(trim($TRN_STARTDATE) && trim($TRN_ENDDATE)){
										$TRN_DURATION = "$TRN_STARTDATE - $TRN_ENDDATE";
										if($TRN_STARTDATE == $TRN_ENDDATE) $TRN_DURATION = "$TRN_STARTDATE";
									}
									$TRN_ORG = trim($data2[TRN_ORG]);
									$TR_NAME = trim($data2[TR_NAME]);
									if (!$TR_NAME || $TR_NAME=="อื่นๆ") $TR_NAME = trim($data2[TRN_COURSE_NAME]);
									//if($TR_NAME!=""){	$TR_NAME = str_replace("&quot;",""",$TR_NAME);		}
									$TRN_PLACE = trim($data2[TRN_PLACE]);
									$TRN_FUND = trim($data2[TRN_FUND]);
									$TRN_NO = trim($data2[TRN_NO]);
									if ($TRN_NO) $TR_NAME .= "รุ่นที่ ".$TRN_NO;
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$TRN_PLACE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$TRN_DURATION, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$TR_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,3,$TRN_ORG?$TRN_ORG:"-", set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
									$worksheet->write_string($xlsRow,4,$TRN_FUND?$TRN_FUND:"-",set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));//ทุน
								} // end while
                                                                unset($data2);
							}else{
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
                        case "SALARYHIS" :
                            $no_index++;
                            $xlsRow++;
                            $worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการเลื่อนขั้นเงินเดือน ", set_format("xlsFmtSubTitle", "B", "L", "", 0));	// 0 สุดท้ายทำให้หัวข้อชิดซ้าย
                            print_header($HISTORY_NAME);
                            if(is_array($POH_SALARYHIS)  && !empty($POH_SALARYHIS)){
                                $count_positionhis=count($POH_SALARYHIS[$PER_ID]);
                                $positionhis_count=0;
                                for($in=0; $in < count($POH_SALARYHIS[$PER_ID]);$in++){
                                    $positionhis_count++;
                                    if($POH_SALARYHIS[$PER_ID][$in]['DATE']){
                                        $DATE_POH_SAH = show_date_format(substr($POH_SALARYHIS[$PER_ID][$in]['DATE'], 0, 10),$DATE_DISPLAY);
                                    }
                                    //หาระดับตำแหน่ง (1,2,3,4,5,6,7,8,9);
                                    $cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='".$POH_SALARYHIS[$PER_ID][$in]['LEVEL']."' order by LEVEL_NO";						
                                    //echo "<br>$cmd<br>";
                                    $db_dpis2->send_cmd($cmd);
                                    $data2 = $db_dpis2->get_array();
                                    $LEVEL_NAME = trim($data2[LEVEL_NAME]);
                                    $arr_temp = explode(" ", $LEVEL_NAME);
                                    //หาชื่อตำแหน่งประเภท
                                    $POSITION_TYPE="";
                                    if(strstr($LEVEL_NAME, 'ประเภท') == TRUE) {
                                        $POSITION_TYPE = str_replace("ประเภท", "", $arr_temp[0]);
                                    }elseif(strstr($LEVEL_NAME, 'กลุ่มงาน') == TRUE) {
                                        $POSITION_TYPE = str_replace("กลุ่มงาน", "", $arr_temp[0]);
                                    }else{
                                        if($arr_temp[0] != 'ระดับ'){
                                            $POSITION_TYPE = $arr_temp[0];
                                        }
                                    }
                                    //หาชื่อระดับตำแหน่ง 
                                    $LEVEL_NAME ="";
                                    if(strstr($arr_temp[1], 'ระดับ') == TRUE) {
                                            $LEVEL_NAME =  str_replace("ระดับ", "", $arr_temp[1]);
                                    }else{
                                            $LEVEL_NAME =  level_no_format($arr_temp[1]);
                                    }
                                    //--------------------------------------------------------------------
                                    $xlsRow++;
                                    $worksheet->write_string($xlsRow,0,$DATE_POH_SAH, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); //$POH_SALARYHIS[$PER_ID][$in]['DATE']
                                    //$worksheet->write_string($xlsRow,1,$POH_SALARYHIS[$PER_ID][$in]['POS_NAME']." ".$LEVEL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 	//ตำแหน่ง   /*เดิม*/
                                    $worksheet->write_string($xlsRow,1,$POH_SALARYHIS[$PER_ID][$in]['POS_NAME'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 	//ตำแหน่ง /*Release 5.2.1.8 */
                                    $worksheet->write_string($xlsRow,2,$POH_SALARYHIS[$PER_ID][$in]['MOVE'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));//$POH_SALARYHIS[$PER_ID][$in]['MOVE'] //ประเภทการเคลื่อนไหว
                                    $worksheet->write_string($xlsRow,3,$POH_SALARYHIS[$PER_ID][$in]['POS_NO'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));									
                                    $worksheet->write_string($xlsRow,4,($LEVEL_NAME?$LEVEL_NAME:"-"), set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                    $worksheet->write_string($xlsRow,5,$POH_SALARYHIS[$PER_ID][$in]['SALARY'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                    $worksheet->write_string($xlsRow,6,$POH_SALARYHIS[$PER_ID][$in]['DOC_NO'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 
                                    $worksheet->write_string($xlsRow,7,$POH_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_2'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                    $worksheet->write_string($xlsRow,8,$POH_SALARYHIS[$PER_ID][$in]['POH_ORG_NAME_3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0));
                                    $worksheet->write_string($xlsRow,9,$POH_SALARYHIS[$PER_ID][$in]['POH_ORGASS_NAME_3'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 
                                    $worksheet->write_string($xlsRow,10,$POH_SALARYHIS[$PER_ID][$in]['REMARK'], set_format("xlsFmtTableDetail", "", "L", "TLRB", 0)); 
                                } //end for
                                unset($data2);
                            }
                            if($positionhis_count<=0){	//}else{
                                //$xlsRow = 0;
                                $xlsRow++;
                                $worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 8, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 9, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                $worksheet->write($xlsRow, 10, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                            } // end if
                            break;
                        case "EXTRAHIS" : 
                            $no_index++;
                            $xlsRow++;
                            $worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรับเงินตามตำแหน่ง ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
                            print_header($HISTORY_NAME);

                            $PMH_EFFECTIVEDATE = "";
                            $PMH_ENDDATE = "";
                            $EX_NAME = "";
                            $PMH_AMT = "";
	
                            $cmd =" select peh.PMH_EFFECTIVEDATE  ,peh.PMH_ENDDATE,pet.EX_NAME,peh.PMH_AMT
                                from PER_POS_MGTSALARYHIS peh, PER_EXTRATYPE pet
                                where peh.PER_ID=$PER_ID and trim(peh.EX_CODE)=trim(pet.EX_CODE) 
                                order by peh.PMH_EFFECTIVEDATE desc  ";
                            $count_extrahis = $db_dpis2->send_cmd($cmd);
                            //$db_dpis2->show_error();
                            if($count_extrahis){
                                    $extrahis_count = 0;
                                    while($data2 = $db_dpis2->get_array()){
                                            $extrahis_count++;
                                            $PMH_EFFECTIVEDATE = show_date_format($data2[PMH_EFFECTIVEDATE],$DATE_DISPLAY);
                                            $PMH_ENDDATE = show_date_format($data2[PMH_ENDDATE],$DATE_DISPLAY);
                                            $EX_NAME = trim($data2[EX_NAME]);
                                            $PMH_AMT = $data2[PMH_AMT];

                                            $xlsRow++;
                                            $worksheet->write_string($xlsRow,0,$PMH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
                                            $worksheet->write_string($xlsRow,1,$PMH_ENDDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
                                            $worksheet->write_string($xlsRow,2,$EX_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
                                            $worksheet->write_string($xlsRow,3,number_format($PMH_AMT), set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
                                    } // end while
                                    unset($data2);
                            }else{								
                                    //$xlsRow = 0;
                                    $xlsRow++;
                                    $worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
                                    $worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                    $worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                                    $worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
                            } // end if
                    break;
			case "EXTRA_INCOMEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรับเงินเพิ่มพิเศษ ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$EXH_EFFECTIVEDATE = "";
							$EXH_ENDDATE = "";
							$EX_NAME = "";
							$EXH_AMT = "";
							
							$cmd = " select		a.EXH_EFFECTIVEDATE, a.EXH_ENDDATE, b.EX_NAME, a.EXH_AMT
											 from			PER_EXTRAHIS a, PER_EXTRATYPE b
											 where		a.PER_ID=$PER_ID and a.EX_CODE=b.EX_CODE 
											 order by	a.EXH_EFFECTIVEDATE DESC ";							   
							$count_extrahis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_extrahis){
								$extrahis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$extrahis_count++;
									$EXH_EFFECTIVEDATE = show_date_format($data2[EXH_EFFECTIVEDATE],$DATE_DISPLAY);
									$EXH_ENDDATE = show_date_format($data2[EXH_ENDDATE],$DATE_DISPLAY);
									$EX_NAME = trim($data2[EX_NAME]);
									$EXH_AMT = $data2[EXH_AMT];
								
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$EXH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$EXH_ENDDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$EX_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,3,number_format($EXH_AMT), set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "ABILITY" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ความสามารถพิเศษ ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$AL_NAME = "";
							$ABI_DESC = "";

							$cmd = " select		b.AL_NAME, a.ABI_DESC
											 from			PER_ABILITY a, PER_ABILITYGRP b
											 where		a.PER_ID=$PER_ID and a.AL_CODE=b.AL_CODE
											 order by	a.ABI_ID ";							   
							$count_abilityhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_abilityhis){
								$abilityhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$abilityhis_count++;
									$AL_NAME = trim($data2[AL_NAME]);
									$ABI_DESC = trim($data2[ABI_DESC]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$AL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$ABI_DESC, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "HEIR" :
                                                         $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ข้อมูลทายาท ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$HR_NAME = "";	$HEIR_NAME = "";
							$cmd = " select		b.HR_NAME, a.HEIR_NAME
											 from			PER_HEIR a, PER_HEIRTYPE b
											 where		a.PER_ID=$PER_ID and a.HR_CODE=b.HR_CODE
											 order by	a.HEIR_ID ";							   
							$count_heirhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_heirhis){
								$heirhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$heirhis_count++;
									$HR_NAME = trim($data2[HR_NAME]);
									$HEIR_NAME = trim($data2[HEIR_NAME]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$HR_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
									$worksheet->write_string($xlsRow,1,$HEIR_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "ABSENTHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการลา สาย ขาด ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);
							
							$ABS_STARTDATE = "";
							$ABS_ENDDATE = "";
							$ABS_DAY = "";
							$AB_NAME = "";

							$cmd = " select		a.ABS_STARTDATE, a.ABS_ENDDATE, a.ABS_DAY, b.AB_NAME
											 from			PER_ABSENTHIS a, PER_ABSENTTYPE b
											 where		a.PER_ID=$PER_ID and a.AB_CODE=b.AB_CODE
											 order by	a.ABS_STARTDATE DESC ";							   
							$count_absenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_absenthis){
								$absenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$absenthis_count++;
									$ABS_STARTDATE = show_date_format($data2[ABS_STARTDATE],$DATE_DISPLAY);
									$ABS_ENDDATE = show_date_format($data2[ABS_ENDDATE],$DATE_DISPLAY);
									$ABS_DURATION = "$ABS_STARTDATE - $ABS_ENDDATE";
									if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = "$ABS_STARTDATE";
									$ABS_DAY = $data2[ABS_DAY];
									$AB_NAME = trim($data2[AB_NAME]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$ABS_DURATION, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,($ABS_DAY?$ABS_DAY:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$AB_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "PUNISHMENT" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติทางวินัย ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$CR_NAME = "";
							$CRD_NAME = "";
							$PUN_STARTDATE = "";
							$PUN_ENDDATE = "";							
							
							$cmd = " select		c.CR_NAME, b.CRD_NAME, a.PUN_STARTDATE, a.PUN_ENDDATE
											 from			PER_PUNISHMENT a, PER_CRIME_DTL b, PER_CRIME c
											 where		a.PER_ID=$PER_ID and a.CRD_CODE=b.CRD_CODE and b.CR_CODE=c.CR_CODE
											 order by	a.PUN_STARTDATE DESC ";							   
							$count_punishmenthis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_punishmenthis){
								$punishmenthis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$punishmenthis_count++;
									$CR_NAME = trim($data2[CR_NAME]);
									$CRD_NAME = trim($data2[CRD_NAME]);
									$PUN_STARTDATE = show_date_format($data2[PUN_STARTDATE],$DATE_DISPLAY);
									$PUN_ENDDATE = show_date_format($data2[PUN_ENDDATE],$DATE_DISPLAY);
									$PUN_DURATION = "$PUN_STARTDATE - $PUN_ENDDATE";
									if($PUN_STARTDATE == $PUN_ENDDATE) $PUN_DURATION = "$PUN_STARTDATE";
	
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$CR_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$CRD_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$PUN_DURATION, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "TIMEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติเวลาทวีคูณ ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$TIME_NAME = "";
							$TIME_DAY = "";
							$TIMEH_MINUS = "";
							
							$cmd = " select		b.TIME_NAME, b.TIME_DAY, a.TIMEH_MINUS
											 from			PER_TIMEHIS a, PER_TIME b
											 where		a.PER_ID=$PER_ID and a.TIME_CODE=b.TIME_CODE
											 order by	a.TIMEH_ID ";							   
							$count_timehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_timehis){
								$timehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$timehis_count++;
									$TIME_NAME = trim($data2[TIME_NAME]);
									$TIME_DAY = $data2[TIME_DAY];
									$TIMEH_MINUS = $data2[TIMEH_MINUS];
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,($TIME_DAY?$TIME_DAY:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,($TIMEH_MINUS?$TIMEH_MINUS:""), set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "REWARDHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรับความดีความชอบ ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$REH_DATE = "";
							$REW_NAME = "";
							
							$cmd = " select		a.REH_DATE, b.REW_NAME
											 from			PER_REWARDHIS a, PER_REWARD b
											 where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
											 order by	a.REH_DATE DESC ";							   
							$count_rewardhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_rewardhis){
								$rewardhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$rewardhis_count++;
									$REH_DATE = show_date_format($data2[REH_DATE],$DATE_DISPLAY);
									$REW_NAME = trim($data2[REW_NAME]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$REH_DATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$REW_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "MARRHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการสมรส ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$MAH_NAME = "";
							$MAH_MARRY_DATE = "";
							
							$cmd = " select			MAH_NAME, MAH_MARRY_DATE
											 from			PER_MARRHIS
											 where		PER_ID=$PER_ID
											 order by		MAH_SEQ DESC ";							   
							$count_marryhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_marryhis){
								$marryhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$marryhis_count++;
									$MAH_NAME = trim($data2[MAH_NAME]);
									$MAH_MARRY_DATE = show_date_format($data2[MAH_MARRY_DATE],$DATE_DISPLAY);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$MAH_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$MAH_MARRY_DATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "NAMEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการเปลี่ยนแปลงชื่อ-สกุล ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$NH_DATE = "";
							$PN_NAME = "";
							$NH_NAME = "";
							$NH_SURNAME = "";
							
							$cmd = " select		a.NH_DATE, b.PN_NAME, a.NH_NAME, a.NH_SURNAME
											 from			PER_NAMEHIS a, PER_PRENAME b
											 where		a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE
											 order by	a.NH_DATE DESC ";							   
							$count_namehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_namehis){
								$namehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$namehis_count++;
									$NH_DATE = show_date_format($data2[NH_DATE],$DATE_DISPLAY);
									$PN_NAME = trim($data2[PN_NAME]);
									$NH_NAME = trim($data2[NH_NAME]);
									$NH_SURNAME = trim($data2[NH_SURNAME]);
									$FULLNAME = ($PN_NAME?$PN_NAME:"")."$NH_NAME $NH_SURNAME";
									
								$xlsRow++;				
								$worksheet->write_string($xlsRow,0,$NH_DATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								$worksheet->write_string($xlsRow,1,$FULLNAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "DECORATEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรับเครื่องราชฯ ", set_format("xlsFmtSubTitle", "B", "L", "",0));
							print_header($HISTORY_NAME);

							$DC_NAME = "";	$DEH_DATE = "";	 $DEH_GAZETTE = "";
							$cmd = " select		b.DC_NAME, a.DEH_DATE, a.DEH_GAZETTE
											 from			PER_DECORATEHIS a, PER_DECORATION b
											 where		a.PER_ID=$PER_ID and a.DC_CODE=b.DC_CODE
											 order by	a.DEH_DATE DESC ";							   
							$count_decoratehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_decoratehis){
								$decoratehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$decoratehis_count++;
									$DC_NAME = trim($data2[DC_NAME]);
									$DEH_DATE = trim($data2[DEH_DATE]);
									if($DEH_DATE){
										$DEH_DATE = substr($DEH_DATE, 0, 10);
										$DEH_DATE1 = show_date_format($DEH_DATE,$DATE_DISPLAY);
									} // end if	
									$DEH_GAZETTE = trim($data2[DEH_GAZETTE]);
				
									//หาตำแหน่งที่ดำรง ณ ระหว่างวันที่ได้รับพระราชทานเครื่องราช
									if($DPISDB=="odbc"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, POH_SEQ_NO desc ";
									}elseif($DPISDB=="oci8"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														order by 		SUBSTR(POH_EFFECTIVEDATE,1,10) desc, POH_SEQ_NO desc ";
									}elseif($DPISDB=="mysql"){
										$cmd3 = "select 		POH_ID, POH_EFFECTIVEDATE, POH_ENDDATE, POH_ORG2, POH_ORG3, 
																			PL_CODE, PM_CODE, PT_CODE, PN_CODE, EP_CODE, LEVEL_NO, POH_POS_NO_NAME, POH_POS_NO    
														from 			PER_POSITIONHIS 
														where 		(PER_ID=$PER_ID) AND (POH_EFFECTIVEDATE <= '$DEH_DATE')
														ORDER BY	 LEFT(POH_EFFECTIVEDATE,10) desc, POH_SEQ_NO desc ";
									}
									//echo "<br>$cmd3<br>";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$POH_LEVEL_NO = trim($data3[LEVEL_NO]);
									$POH_PL_CODE = trim($data3[PL_CODE]);
									$POH_PM_CODE = trim($data3[PM_CODE]);
									$POH_PT_CODE = trim($data3[PT_CODE]);
									$POH_PN_CODE = $data3[PN_CODE];
									$POH_EP_CODE = $data3[EP_CODE];
		
									$DEH_PL_NAME = "";
									$cmd3= " select PL_NAME from PER_LINE where PL_CODE='$POH_PL_CODE' ";			
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PL_NAME = $data3[PL_NAME];
						
									$cmd3 = " 	select PM_NAME from PER_MGT	where PM_CODE='$POH_PM_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PM_NAME = trim($data3[PM_NAME]);
						
									$cmd3 = " 	select PT_NAME from PER_TYPE	where PT_CODE='$POH_PT_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_PT_NAME = trim($data3[PT_NAME]);
																		
									$cmd3 = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$POH_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$DEH_LEVEL_NAME = $data3[LEVEL_NAME];
								    $DEH_PL_NAME = (trim($DEH_PM_NAME) ?"$DEH_PM_NAME (":"") . (trim($DEH_PL_NAME)?($DEH_PL_NAME ." ". $DEH_LEVEL_NAME . (($DEH_PT_NAME != "ทั่วไป" && $DEH_LEVEL_NO >= 6)?"$DEH_PT_NAME":"")):"") . (trim($DEH_PM_NAME) ?")":"");

								$xlsRow++;
								$worksheet->write_string($xlsRow,0,$DEH_PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
								$worksheet->write_string($xlsRow,1,$DC_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								$worksheet->write_string($xlsRow,2,$DEH_DATE1, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								$worksheet->write_string($xlsRow,3,$DEH_GAZETTE?$DEH_GAZETTE:"-", set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								$worksheet->write_string($xlsRow,4,"-", set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
								} // end while
                                                                unset($data2);
                                                                unset($data3);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "SERVICEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการปฏิบัติราชการพิเศษ ",set_format("xlsFmtSubTitle", "B", "L", "",0));
							print_header($HISTORY_NAME);

							$SRH_STARTDATE = "";
							$SV_NAME = "";
							$SRH_DOCNO = "";
							$SRH_NOTE = "";
							
							$cmd = " select			a.SRH_STARTDATE, b.SV_NAME, a.SRH_DOCNO, a.SRH_NOTE, c.ORG_NAME, d.SRT_NAME, SRH_ORG, SRH_SRT_NAME
											 from			PER_SERVICEHIS a, PER_SERVICE b, PER_ORG c, PER_SERVICETITLE d
											 where		a.PER_ID=$PER_ID and a.SV_CODE=b.SV_CODE and a.ORG_ID=c.ORG_ID(+) and a.SRT_CODE=d.SRT_CODE(+)
											 order by		a.SRH_STARTDATE DESC ";							   
							$count_servicehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_servicehis){
								$servicehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$servicehis_count++;
									$SRH_STARTDATE = show_date_format($data2[SRH_STARTDATE],$DATE_DISPLAY);
									$SV_NAME = trim($data2[SV_NAME]);
									$SRH_DOCNO = trim($data2[SRH_DOCNO]);
									$SRH_NOTE = trim($data2[SRH_NOTE]);
									$SRH_ORG = trim($data2[ORG_NAME]);
									$SRT_NAME = trim($data2[SRT_NAME]);
									if (!$SRH_ORG) $SRH_ORG = trim($data2[SRH_ORG]);
									if (!$SRT_NAME) $SRT_NAME = trim($data2[SRH_SRT_NAME]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$SRH_STARTDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$SV_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$SRT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,3,$SRH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{								
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "SPECIALSKILLHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ความเชี่ยวชาญพิเศษ ",set_format("xlsFmtSubTitle", "B", "L", "",0));
							print_header($HISTORY_NAME);

							$SS_NAME = "";
							$SPS_EMPHASIZE = "";
							
							$cmd = " select		b.SS_NAME, a.SPS_EMPHASIZE
											 from			PER_SPECIAL_SKILL a, PER_SPECIAL_SKILLGRP b
											 where		a.PER_ID=$PER_ID and a.SS_CODE=b.SS_CODE
											 order by	a.SPS_ID ";							   
							$count_specialskillhis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_specialskillhis){
								$specialskillhis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$specialskillhis_count++;
									$SS_NAME = trim($data2[SS_NAME]);
									$SPS_EMPHASIZE = trim($data2[SPS_EMPHASIZE]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$SS_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$SPS_EMPHASIZE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
					case "ACTINGHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)."  ประวัติการรักษาราชการ/มอบหมายงาน",set_format("xlsFmtSubTitle", "B", "L", "",0));
							print_header($HISTORY_NAME);
							
					if($DPISDB=="odbc") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10) DESC, LEFT(ACTH_ENDDATE,10) DESC, ACTH_SEQ_NO DESC";
					elseif($DPISDB=="oci8") $order_by = "SUBSTR(ACTH_EFFECTIVEDATE,1,10) DESC, SUBSTR(ACTH_ENDDATE,1,10) DESC, ACTH_SEQ_NO DESC";			 
					elseif($DPISDB=="mysql") $order_by = "LEFT(ACTH_EFFECTIVEDATE,10) DESC, LEFT(ACTH_ENDDATE,10) DESC, ACTH_SEQ_NO DESC"; 
					if($DPISDB=="odbc"){
						$cmd = " SELECT 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
											PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
											ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN    
								FROM		PER_ACTINGHIS 
								WHERE		PER_ID=$PER_ID 
								ORDER BY	 $order_by ";
								//echo $cmd;
					}elseif($DPISDB=="oci8"){			 
						$cmd = "select 		ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN,
															PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, 
															ACTH_DOCNO, ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
												  from 		PER_ACTINGHIS 
												  where 		PER_ID=$PER_ID
												  order by 	$order_by ";				 					 
					}elseif($DPISDB=="mysql"){
						$cmd = " SELECT 	ACTH_ID, ACTH_EFFECTIVEDATE, ACTH_ENDDATE, ACTH_ORG2_ASSIGN, ACTH_ORG3_ASSIGN, 
									PL_CODE_ASSIGN, PM_CODE_ASSIGN, LEVEL_NO_ASSIGN, ACTH_POS_NO_ASSIGN, MOV_CODE, ACTH_DOCNO, 
									ACTH_PM_NAME_ASSIGN, ACTH_PL_NAME_ASSIGN
									FROM		PER_ACTINGHIS 
									WHERE PER_ID=$PER_ID 
									ORDER BY 	$order_by ";
					} // end if
					$count_actinghis = $db_dpis2->send_cmd($cmd);
	//				$db_dpis2->show_error();
					if($count_actinghis){
								$actinghis_count = 0;
								while($data = $db_dpis2->get_array()){
									$actinghis_count++;
									$TMP_ACTH_ID = $data[ACTH_ID];
									$TMP_ACTH_EFFECTIVEDATE = show_date_format($data[ACTH_EFFECTIVEDATE], $DATE_DISPLAY);
									$TMP_ACTH_ENDDATE = show_date_format($data[ACTH_ENDDATE], $DATE_DISPLAY);
									$TMP_LEVEL_NO = trim($data[LEVEL_NO_ASSIGN]);
									$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_LEVEL_NAME = $data2[LEVEL_NAME];
									
									$TMP_ACTH_POS_NO = (trim($data[ACTH_POS_NO_ASSIGN]))?   $data[ACTH_POS_NO_ASSIGN] : "-";
									
									$TMP_ORG_NAME_2 = $data[ACTH_ORG2_ASSIGN];
									$TMP_ORG_NAME_3 = $data[ACTH_ORG3_ASSIGN];
									
									$TMP_PL_CODE = trim($data[PL_CODE_ASSIGN]);
									$TMP_PM_CODE = trim($data[PM_CODE_ASSIGN]);
									$TMP_MOV_CODE = $data[MOV_CODE];
									$TMP_ACTH_DOCNO = $data[ACTH_DOCNO];
									
									$cmd = " select MOV_NAME from PER_MOVMENT where MOV_CODE='$TMP_MOV_CODE' ";
									$db_dpis3->send_cmd($cmd);
									$data2 = $db_dpis3->get_array();
									$TMP_MOV_NAME = $data2[MOV_NAME];		
									
									if($PER_TYPE==1){
										$TMP_PL_NAME = "";
										$cmd = " select PL_NAME from PER_LINE where PL_CODE='$TMP_PL_CODE' ";			
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PL_NAME];
									
										$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PM_NAME = trim($data2[PM_NAME]);
									
										$cmd = " 	select PT_NAME from PER_TYPE	where PT_CODE='$TMP_PT_CODE'  ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PT_NAME = trim($data2[PT_NAME]);
									
										if ($RPT_N)
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)? "$TMP_PL_NAME $TMP_LEVEL_NAME" : "") . (trim($TMP_PM_NAME) ?")":"");
										else
											$TMP_PL_NAME = (trim($TMP_PM_NAME) ?"$TMP_PM_NAME (":"") . (trim($TMP_PL_NAME)?($TMP_PL_NAME ." ". level_no_format($TMP_LEVEL_NO) . (($TMP_PT_NAME != "ทั่วไป" && $TMP_LEVEL_NO >= 6)?"$TMP_PT_NAME":"")):"") . (trim($TMP_PM_NAME) ?")":"");
									} elseif($PER_TYPE==2){
										$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='$TMP_PN_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[PN_NAME];	
									} elseif($PER_TYPE==3){
										$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='$TMP_EP_CODE' ";
										$db_dpis3->send_cmd($cmd);
										$data2 = $db_dpis3->get_array();
										$TMP_PL_NAME = $data2[EP_NAME];		
									} // end if
									if (!$TMP_PL_NAME) $TMP_PL_NAME = trim($data[ACTH_PL_NAME]);
											
										$xlsRow++;
										$worksheet->write_string($xlsRow,0,"$actinghis_count.) $TMP_MOV_NAME", set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
										$worksheet->write_string($xlsRow,1,$TMP_ACTH_DOCNO, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
										$worksheet->write_string($xlsRow,2,$TMP_ACTH_EFFECTIVEDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
										$worksheet->write_string($xlsRow,3,$TMP_ACTH_ENDDATE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
										$worksheet->write_string($xlsRow,4,$TMP_ACTH_POS_NO, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
										$worksheet->write_string($xlsRow,5,$TMP_PL_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
										$worksheet->write_string($xlsRow,6,$TMP_ORG_NAME_2, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
										$worksheet->write_string($xlsRow,7,$TMP_ORG_NAME_3, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
								} // end while
                                                                unset($data);
                                                                unset($data2);
							}else{
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 5, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 6, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 7, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if	
						break;
			case "LICENSEHIS" :
                                                        $no_index++;
							$xlsRow++;
							$worksheet->write_string($xlsRow,0,($no_index)." ประวัติใบอนุญาตประกอบวิชาชีพ", set_format("xlsFmtSubTitle", "B", "L", "", 0));
							print_header($HISTORY_NAME);

							$LH_LICENSE_DATE = "";	$LH_EXPIRE_DATE = "";	$LT_NAME = "";		$LH_MAJOR = "";
							if($DPISDB=="odbc"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE
												 order by	a.LH_EXPIRE_DATE DESC ";							   
							}elseif($DPISDB=="oci8"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE(+)
												 order by	a.LH_EXPIRE_DATE DESC ";							   
							}elseif($DPISDB=="mysql"){
								$cmd = " select		a.LH_LICENSE_DATE, a.LH_EXPIRE_DATE,a.LH_SUB_TYPE, b.LT_NAME, a.LH_MAJOR, a.LH_LICENSE_NO
												 from			PER_LICENSEHIS a, PER_LICENSE_TYPE b
												 where		a.PER_ID=$PER_ID and a.LT_CODE=b.LT_CODE
												 order by	a.LH_EXPIRE_DATE DESC ";							   
							}
							$count_licensehis = $db_dpis2->send_cmd($cmd);
//							$db_dpis2->show_error();
							if($count_licensehis){
								$licensehis_count = 0;
								while($data2 = $db_dpis2->get_array()){
									$licensehis_count++;
									$LH_LICENSE_DATE = show_date_format($data2[LH_LICENSE_DATE],$DATE_DISPLAY);
									$LH_EXPIRE_DATE = show_date_format($data2[LH_EXPIRE_DATE],$DATE_DISPLAY);
									$LH_SUB_TYPE = trim($data2[LH_SUB_TYPE]);
									$LT_NAME = trim($data2[LT_NAME]);
									$LH_MAJOR = trim($data2[LH_MAJOR]);
									$LH_LICENSE_NO = trim($data2[LH_LICENSE_NO]);
				
									$xlsRow++;
									$worksheet->write_string($xlsRow,0,$LT_NAME, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,1,$LH_SUB_TYPE, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,2,$LH_MAJOR, set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
									$worksheet->write_string($xlsRow,3,$LH_LICENSE_NO, set_format("xlsFmtTableDetail", "", "L", "TLRB",1));
									$worksheet->write_string($xlsRow,4,$LH_EXPIRE_DATE,set_format("xlsFmtTableDetail", "", "L", "TLRB", 1));
								} // end while
                                                                unset($data2);
							}else{
								//$xlsRow = 0;
								$xlsRow++;
								$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
								$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
							} // end if
						break;
				} // end switch
			} // end for
			//if($data_count < $count_data) $pdf->AddPage();
                     
		} // end while
	}else{ //end if($count_data)
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 1, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 2, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 3, "", set_format("xlsFmtTitle", "B", "C", "", 1));
		$worksheet->write($xlsRow, 4, "", set_format("xlsFmtTitle", "B", "C", "", 1));
	} // end if

	$workbook->close();
        unset($workbook);

	//ini_set("max_execution_time", 60);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/x-msexcel; name=\"$report_code.xls\"");
	header("Content-Disposition: inline; filename=\"$report_code.xls\"");
       
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	fclose($fh);
	unlink($fname);
?>