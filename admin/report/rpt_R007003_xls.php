<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	include ("../report/rpt_function.php");

	include ("rpt_R007003_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
        function CheckPublicHoliday($YYYY_MM_DD){
            global $DPISDB,$db_dpis;
            if($DPISDB=="odbc"){ 
                $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
            }elseif($DPISDB=="oci8"){
                $cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$YYYY_MM_DD' ";
            }elseif($DPISDB=="mysql"){
                $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
            }    
            $IS_HOLIDAY = $db_dpis->send_cmd($cmd);
            if(!$IS_HOLIDAY){
                return false;
            }else{
                return true;
            }
	}
        
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_code = "b.PL_CODE";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);		
		$line_title = " สายงาน";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_code = "b.PN_CODE";
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);		
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_code = "b.EP_CODE";
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);		
		$line_title = " ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_code = "b.TP_CODE";
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);		
		$line_title = " ชื่อตำแหน่ง";
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE", "LEVEL", "POSNO", "NAME");

	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
				/*case "MINISTRY" :
						if($order_by) $order_by .= ", ";
						$order_by .= "e.ORG_ID_REF";
		
						$heading_name .= " $MINISTRY_TITLE";
					break;
				*/
			case "DEPARTMENT" : 
						if($order_by) $order_by .= ", ";
						$order_by .= "a.DEPARTMENT_ID";
		
						$heading_name .= " $DEPARTMENT_TITLE";
					break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE1";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure == 0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure == 1) $order_by .= "a.ORG_ID";

				$heading_name .= " $ORG_TITLE2";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= $line_code; 
				$heading_name .= $line_title;
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.LEVEL_NO";

				$heading_name .= " $LEVEL_TITLE";
				break;
			case "POSNO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "$position_no_name, IIf(IsNull($position_no), 0, CLng($position_no))";
				elseif($DPISDB=="oci8") $order_by .= "$position_no_name, to_number(replace($position_no,'-',''))";
				elseif($DPISDB=="mysql") $order_by .= "$position_no_name, $position_no";
			
				$heading_name .= " $POS_NO_TITLE";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";

				$heading_name .= " $FULLNAME_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_NAME, a.PER_SURNAME";

	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	/*if($list_person_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_person_type == "CONDITION"){
		if(trim($search_pos_no)){ 
			$arr_search_condition[] = "($position_no like '$search_pos_no%')";
		} // end if
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)) $arr_search_condition[] = "(a.LEVEL_NO >= '$search_min_level')";
		if(trim($search_max_level)) $arr_search_condition[] = "(a.LEVEL_NO <= '$search_max_level')";
	} // end if*/
        
	$LEVEL_NO_VAL ="";
     if($list_person_type=="SELECT"){
        if(!$SELECTED_PER_ID){$SELECTED_PER_ID=0;}
        $arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
    }elseif($list_person_type == "CONDITION"){
		
		if(trim($search_pos_no)){ 
                    if($search_per_type==1){ 
                        $arr_search_condition[] = "($position_no = '$search_pos_no')";
                    }elseif($search_per_type==2){ 
                        $arr_search_condition[] = "($position_no = '$search_pos_no')";
                    }elseif($search_per_type==3){ 
                        $arr_search_condition[] = "($position_no = '$search_pos_no')";
                    }elseif($search_per_type==4){ 
                        $arr_search_condition[] = "($position_no = '$search_pos_no')";
                    }

		} // end if
		if(trim($search_name)){ 
                    $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		} // end if
		
		if(trim($search_surname)){ 
                    $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		} // end if
		
		if($search_per_type==1){
                    if(trim($search_min_level1) & trim($search_max_level1)){ 
                            $search_min_level=$search_min_level1;
                            $search_max_level=$search_max_level1;

                            $LEVEL_NO_VAL = " and a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }elseif(trim($search_min_level2) & trim($search_max_level2)){ 
                            $search_min_level=$search_min_level2;
                            $search_max_level=$search_max_level2;

                            $LEVEL_NO_VAL = " and a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	
                    }elseif(trim($search_min_level3) & trim($search_max_level3)){ 
                            $search_min_level=$search_min_level3;
                            $search_max_level=$search_max_level3;

                            $LEVEL_NO_VAL = " and a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	


                    }elseif(trim($search_min_level4) & trim($search_max_level4)){ 
                            $search_min_level=$search_min_level4;
                            $search_max_level=$search_max_level4;

                            $LEVEL_NO_VAL = " and a.LEVEL_NO in (select LEVEL_NO  from PER_LEVEL where PER_TYPE = $search_per_type and
                                                                                            LEVEL_SEQ_NO between 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_min_level') and 
                                                                                              (select LEVEL_SEQ_NO from PER_LEVEL where LEVEL_NO = '$search_max_level')
                                                                                            ) ";	


                    }
		}
            $arr_search_condition[] = " a.PER_ID NOT IN(0) ";
	}elseif($list_person_type == "ALL"){
		$arr_search_condition[] = " a.PER_ID NOT IN(0) ";
        }else{ $arr_search_condition[] = "(a.PER_ID in ($SESS_PER_ID))";}

	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

        //---------------------------------------------นับวันทำการ-----------------------------------------------------------
        if(trim($search_date_min) && trim($search_date_max)){
            $strStartDate = $search_date_min;//"2011-08-01";
            $strEndDate = $search_date_max;//"2011-08-15";
            //echo $strStartDate.'==='.$strStartDate;
            $intWorkDay = 0;
            $intHoliday = 0;
            $intPublicHoliday = 0;
            $intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate))/  ( 60 * 60 * 24 )) + 1; 
            
            while (strtotime($strStartDate) <= strtotime($strEndDate)) {
                $DayOfWeek = date("w", strtotime($strStartDate));
                if($DayOfWeek == 0 or $DayOfWeek ==6){  // 0 = Sunday, 6 = Saturday;
                        $intHoliday++;
                        //echo "$strStartDate = <font color=red>Holiday</font><br>";
                }elseif(CheckPublicHoliday($strStartDate)){
                        $intPublicHoliday++;
                        //echo "$strStartDate = <font color=orange>Public Holiday</font><br>";
                }else{
                        $intWorkDay++;
                        //echo "$strStartDate = <b>Work Day</b><br>";
                }
                //$DayOfWeek = date("l", strtotime($strStartDate)); // return Sunday, Monday,Tuesday....
                $strStartDate = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)));
            }
        }else{
            $intWorkDay='';
        }
        $WORK_DAY=$intWorkDay;
        //--------------------------------------------------------------------------------------------------------
        //echo $WORK_DAY.'<br>';
        
	$list_type_text = $ALL_REPORT_TITLE;

//	include ("../report/rpt_condition3.php");
	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if

		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
	}
	if(in_array("PER_ORG", $list_type)){
            $list_type_text = "";
            if($select_org_structure==0) {
                if(trim($search_org_id)){ 
                    $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                    $list_type_text .= "$search_org_name";
                    $R_ORG_ID = "b.ORG_ID";
                } // end if
                if(trim($search_org_id_1)){ 
                    $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_text .= "$search_org_name_1";
                    $R_ORG_ID = "b.ORG_ID_1";
                } // end if
                if(trim($search_org_id_2)){ 
                    $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
                    $list_type_text .= "$search_org_name_2";
                    $R_ORG_ID = "b.ORG_ID_2";
                } // end if
                if(trim($search_org_id_3)){ 
                    $arr_search_condition[] = "(b.ORG_ID_3 = $search_org_id_3)";
                    $list_type_text .= "$search_org_name_3";
                    $R_ORG_ID = "b.ORG_ID_3";
                } // end if
                if(trim($search_org_id_4)){ 
                    $arr_search_condition[] = "(b.ORG_ID_4 = $search_org_id_4)";
                    $list_type_text .= "$search_org_name_4";
                    $R_ORG_ID = "b.ORG_ID_4";
                } // end if
                if(trim($search_org_id_5)){ 
                    $arr_search_condition[] = "(b.ORG_ID_5 = $search_org_id_5)";
                    $list_type_text .= "$search_org_name_5";
                    $R_ORG_ID = "b.ORG_ID_5";
                } // end if
            }else{
                if(trim($search_org_ass_id)){ 
                    $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= "$search_org_ass_name";
                    $R_ORG_ID = "a.ORG_ID";
                } // end if
                if(trim($search_org_ass_id_1)){ 
                    $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
                    $list_type_text .= "$search_org_ass_name_1";
                    $R_ORG_ID = "a.ORG_ID_1";
                } // end if
                if(trim($search_org_ass_id_2)){ 
                    $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
                    $list_type_text .= "$search_org_ass_name_2";
                    $R_ORG_ID = "a.ORG_ID_2";
                } // end if
                if(trim($search_org_ass_id_3)){ 
                    $arr_search_condition[] = "(a.ORG_ID_3 = $search_org_ass_id_3)";
                    $list_type_text .= "$search_org_ass_name_3";
                    $R_ORG_ID = "a.ORG_ID_3";
                } // end if
                if(trim($search_org_ass_id_4)){ 
                    $arr_search_condition[] = "(a.ORG_ID_4 = $search_org_ass_id_4)";
                    $list_type_text .= "$search_org_ass_name_4";
                    $R_ORG_ID = "a.ORG_ID_4";
                } // end if
                if(trim($search_org_ass_id_5)){ 
                    $arr_search_condition[] = "(a.ORG_ID_5 = $search_org_ass_id_5)";
                    $list_type_text .= "$search_org_ass_name_5";
                    $R_ORG_ID = "a.ORG_ID_5";
                } // end if
            }
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($line_code)='$line_search_code')";
			$list_type_text .= "$line_search_name";
		}
	}
	if(in_array("PER_COUNTRY", $list_type)){
		// ประเทศ , จังหวัด
		$list_type_text = "";
		if(trim($search_ct_code)){ 
			$search_ct_code = trim($search_ct_code);
			$arr_search_condition[] = "(trim(c.CT_CODE) = '$search_ct_code')";
			$list_type_text .= "$search_ct_name";
		} // end if
		if(trim($search_pv_code)){ 
			$search_pv_code = trim($search_pv_code);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$search_pv_code')";
			$list_type_text .= " - $search_pv_name";
		} // end if
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){ 
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการลารายบุคคล" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"")."||รวมวันมาปฏิบัติราชการ $WORK_DAY วันทำการ";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0703";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/dpis_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	$worksheet = &$workbook->addworksheet("$report_code");
	$worksheet->set_margin_right(0.50);
	$worksheet->set_margin_bottom(1.10);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน
    	$ws_head_line1 = (array) null;
    	$ws_head_line2 = (array) null;
    	$ws_head_line3 = (array) null;
    	$ws_head_line4 = (array) null;
    	$ws_head_line5 = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$buff = explode("|", $heading_text[$i]);
			$ws_head_line1[] = $buff[0];
			$ws_head_line2[] = $buff[1];
			$ws_head_line3[] = $buff[2];
			$ws_head_line4[] = $buff[3];
			$ws_head_line5[] = $buff[4];
		}
		$ws_colmerge_line1 = array(0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line2 = array(0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,1);
		$ws_colmerge_line3 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line4 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_colmerge_line5 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		$ws_border_line1 = array("TLR","TRL","TRL","TL","T","T","T","T","T","T","T","T","T","T","T","T","TR");
		$ws_border_line2 = array("LR","LR","LR","TRL","TRL","TRL","TRL","TRL","TL","T","T","T","T","T","T","T","TR");
		$ws_border_line3 = array("LR","LR","LR","LR","LR","LR","LR","LR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR","TLR");
		$ws_border_line4 = array("LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR","LR");
		$ws_border_line5 = array("LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR","LBR");
		$ws_fontfmt_line1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
		$ws_headalign_line1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
		$ws_width = array(10,15,15,10,10,10,10,10,10,10,10,10,10,10,10,10,10);
	// จบการกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนหัวรายงาน	

	// คำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
//		echo "bf..ws_width=".implode(",",$ws_width)."<br>";
		$sum_hdw = 0;
		$sum_wsw = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			$sum_wsw += $ws_width[$h];	// ws_width ยังไม่ได้ บวก ความกว้าง ตัวที่ถูกตัดเข้าไป
			if ($arr_column_sel[$h]==1) {
				$sum_hdw += $heading_width[$h];
			}
		}
		// บัญญัติไตรยางค์   ยอดรวมความกว้าง column ใน heading_width เทียบกับ ยอดรวมใน ws_width
		//                                แต่ละ column ใน ws_width[$h] = sum(ws_width) /sum(heading_width) * heading_width[$h]
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1) {
				$ws_width[$h] = $sum_wsw / $sum_hdw * $heading_width[$h];
			}
		}
//		echo "af..ws_width=".implode(",",$ws_width)."<br>";
	// จบการเทียบค่าคำนวนเปลียบเทียบค่า $ws_width ใหม่ เทียบกับ $heading_width
	
	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_column_map, $arr_column_sel, $arr_column_align, $arr_column_width;
		global $ws_head_line1, $ws_head_line2,  $ws_head_line3, $ws_head_line4, $ws_head_line5;
		global $ws_colmerge_line1, $ws_colmerge_line2, $ws_colmerge_line3, $ws_colmerge_line4, $ws_colmerge_line4;
		global $ws_border_line1, $ws_border_line2, $ws_border_line3, $ws_border_line4, $ws_border_line5;
		global $ws_fontfmt_line1, $ws_headalign_line1, $ws_width;

		// loop กำหนดความกว้างของ column
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->set_column($colseq, $colseq, $ws_width[$arr_column_map[$i]]);
//				echo "$i-map[".$arr_column_map[$i]."], width=".$ws_width[$arr_column_map[$i]]."<br />";
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 1
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line1[$arr_column_map[$i]], $ws_border_line1[$arr_column_map[$i]], $ws_colmerge_line1[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 2
		$xlsRow++;
		$colseq=0;
		$pgrp="";
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {	// พิมพ์เฉพาะที่เลือกให้แสดง
				$buff = explode("|",doo_merge_cell($ws_head_line2[$arr_column_map[$i]], $ws_border_line2[$arr_column_map[$i]], $ws_colmerge_line2[$arr_column_map[$i]], $pgrp, ($i == count($ws_width)-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 3
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line3[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line3[$arr_column_map[$i]], $ws_colmerge_line3[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 4
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line4[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line4[$arr_column_map[$i]], $ws_colmerge_line4[$arr_column_map[$i]]));
				$colseq++;
			}
		}
		// loop พิมพ์ head บรรทัดที่ 5
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($ws_width); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) { 	// พิมพ์เฉพาะที่เลือกให้แสดง
				$worksheet->write($xlsRow, $colseq, $ws_head_line5[$arr_column_map[$i]], set_format("xlsFmtTableHeader", $ws_fontfmt_line1[$arr_column_map[$i]], $ws_headalign_line1[$arr_column_map[$i]], $ws_border_line5[$arr_column_map[$i]], $ws_colmerge_line5[$arr_column_map[$i]]));
				$colseq++;
			}
		}
	} // function		

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2;
		global $arr_content, $data_count;
		global $search_date_min, $search_date_max, $select_org_structure, $DATE_DISPLAY, $BKK_FLAG;
		
		$search_condition = "";
		unset($arr_search_condition);
		/*
		if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
		} // end if
		if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
		} // end if
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		*/
		if(trim($search_date_min) && trim($search_date_max)){
			if($DPISDB=="odbc") {
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) < '$search_date_min' and LEFT(ABS_ENDDATE, 10) > '$search_date_max')";	// กรณีลาคล่อมช่วงวันที่เลือก
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min' and LEFT(ABS_ENDDATE, 10) <= '$search_date_max')"; // กรณีลาอยู่ในช่วง
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min' and LEFT(ABS_STARTDATE, 10) <= '$search_date_max')"; // กรณีวันเริ่มอยู่ในช่วง
				$arr_search_condition1[] = "(LEFT(ABS_ENDDATE, 10) >= '$search_date_min' and LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";	// กรณีวันสิ้นสุดอยู่ในช่วง
			}elseif($DPISDB=="oci8") {
				$arr_search_condition1[] = "(SUBSTR(ABS_STARTDATE, 1, 10) < '$search_date_min' and SUBSTR(ABS_ENDDATE, 1, 10) > '$search_date_max')";	// กรณีลาคล่อมช่วงวันที่เลือก
				$arr_search_condition1[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min' and SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_date_max')"; // กรณีลาอยู่ในช่วง
				$arr_search_condition1[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min' and SUBSTR(ABS_STARTDATE, 1, 10) <= '$search_date_max')"; // กรณีวันเริ่มอยู่ในช่วง
				$arr_search_condition1[] = "(SUBSTR(ABS_ENDDATE, 1, 10) >= '$search_date_min' and SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_date_max')";	// กรณีวันสิ้นสุดอยู่ในช่วง
			}elseif($DPISDB=="mysql") {
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) < '$search_date_min' and LEFT(ABS_ENDDATE, 10) > '$search_date_max')";	// กรณีลาคล่อมช่วงวันที่เลือก
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min' and LEFT(ABS_ENDDATE, 10) <= '$search_date_max')"; // กรณีลาอยู่ในช่วง
				$arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min' and LEFT(ABS_STARTDATE, 10) <= '$search_date_max')"; // กรณีวันเริ่มอยู่ในช่วง
				$arr_search_condition1[] = "(LEFT(ABS_ENDDATE, 10) >= '$search_date_min' and LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";	// กรณีวันสิ้นสุดอยู่ในช่วง
			}
		} else if(trim($search_date_min)){
			if($DPISDB=="odbc") $arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
			elseif($DPISDB=="oci8") $arr_search_condition1[] = "(SUBSTR(ABS_STARTDATE, 1, 10) >= '$search_date_min')";
			elseif($DPISDB=="mysql") $arr_search_condition1[] = "(LEFT(ABS_STARTDATE, 10) >= '$search_date_min')";
		} else if(trim($search_date_max)){
			if($DPISDB=="odbc") $arr_search_condition1[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
			elseif($DPISDB=="oci8") $arr_search_condition1[] = "(SUBSTR(ABS_ENDDATE, 1, 10) <= '$search_date_max')";
			elseif($DPISDB=="mysql") $arr_search_condition1[] = "(LEFT(ABS_ENDDATE, 10) <= '$search_date_max')";
		} // end if
		$arr_search_condition[] = "(".implode(" or ", $arr_search_condition1).")";
		if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
		
		$cmd = " select		AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD
						 from 		PER_ABSENTHIS
						 where	PER_ID=$PER_ID
						 				$search_condition
						 order by ABS_STARTDATE ";
		$count_absent = $db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data_row = 0;
		while($data2 = $db_dpis2->get_array()){
			$data_row++;
			$data_count++;

			$AB_CODE = trim($data2[AB_CODE]);
			if ($BKK_FLAG==1) {
				if ($AB_CODE=="1") $AB_CODE = "01";
				elseif ($AB_CODE=="2") $AB_CODE = "12";
				elseif ($AB_CODE=="3") $AB_CODE = "03";
				elseif ($AB_CODE=="4") $AB_CODE = "02";
				elseif ($AB_CODE=="5") $AB_CODE = "11";
				elseif ($AB_CODE=="6") $AB_CODE = "04";
				elseif ($AB_CODE=="7") $AB_CODE = "05";
				elseif ($AB_CODE=="8") $AB_CODE = "07";
				elseif ($AB_CODE=="9") $AB_CODE = "09";
				elseif ($AB_CODE=="10") $AB_CODE = "06";
				elseif ($AB_CODE=="11") $AB_CODE = "13";
				elseif ($AB_CODE=="12") $AB_CODE = "10";
				elseif ($AB_CODE=="13") $AB_CODE = "05";
				elseif ($AB_CODE=="14") $AB_CODE = "06";
				elseif ($AB_CODE=="15") $AB_CODE = "15";
			} 
			$ABS_DAY = $data2[ABS_DAY];
//			${"TOTAL_".$AB_CODE} += $ABS_DAY;

			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			if ($ABS_STARTDATE < $search_date_min) {
				$ABS_STARTDATE = $search_date_min;
				$ABS_STARTPERIOD = 3;				// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
			} else 
				$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			if ($ABS_ENDDATE > $search_date_max) {
				$ABS_ENDDATE = $search_date_max;
				$ABS_ENDPERIOD = 3;				// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
			} else 
				$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
			
			if($ABS_STARTDATE == $ABS_ENDDATE){
				if($ABS_STARTPERIOD == 1) $ABS_DAY .= " ช";
				elseif($ABS_STARTPERIOD == 2) $ABS_DAY .= " บ";
				elseif($ABS_ENDPERIOD == 1) $ABS_DAY .= " ช";
				elseif($ABS_ENDPERIOD == 2) $ABS_DAY .= " บ";
			}else{
				if($ABS_STARTPERIOD != 3 && $ABS_ENDPERIOD == 3){
					if($ABS_STARTPERIOD == 1) $ABS_DAY .= " ช";
					elseif($ABS_STARTPERIOD == 2) $ABS_DAY .= " บ";
				}elseif($ABS_STARTPERIOD == 3 && $ABS_ENDPERIOD != 3){
					if($ABS_ENDPERIOD == 1) $ABS_DAY .= " ช";
					elseif($ABS_ENDPERIOD == 2) $ABS_DAY .= " บ";
				} // end if
			} // end if

			$ABS_DAY = get_day($ABS_STARTDATE, $ABS_STARTPERIOD, $ABS_ENDDATE, $ABS_ENDPERIOD, $AB_CODE);
			${"TOTAL_".$AB_CODE} += $ABS_DAY;

			$ABS_STARTDATE = show_date_format($ABS_STARTDATE,$DATE_DISPLAY);
			$ABS_ENDDATE = show_date_format($ABS_ENDDATE,$DATE_DISPLAY);

			$arr_content[$data_count][type] = "ABSENT";
			$arr_content[$data_count][ORDER] = $data_row;
			$arr_content[$data_count][STARTDATE] = $ABS_STARTDATE;
			$arr_content[$data_count][ENDDATE] = $ABS_ENDDATE;
			$arr_content[$data_count][("DAY_".$AB_CODE)] = $ABS_DAY;			
		} // end while
		
		if($count_absent){
			$data_count++;			
			$arr_content[$data_count][type] = "TOTAL";
			for($i=1; $i<=15; $i++) $arr_content[$data_count][("DAY_".str_pad($i, 2, "0", STR_PAD_LEFT))] = ${"TOTAL_".str_pad($i, 2, "0", STR_PAD_LEFT)};			//ลาผักผ่อนนับวันหยุด (11)
		} // end if
	} // function
        
        
        $CON_PER_AUDIT_FLAG="";
	if($SESS_AuditArray){
		if ( $PER_AUDIT_FLAG==1 ){
			
			$tCon="(";
						//$InGroupHRG = false;
			for ($i=0; $i < count($SESS_AuditArray); $i++) {
				if ($i>0)
					$tCon .= " or ";
				$tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
				if ($SESS_AuditArray[$i][1] != 0)
					$tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
				$tCon .= ")";
							   // $InGroupHRG = true;
			}
			$tCon .= ")";
			 $CON_PER_AUDIT_FLAG .= $tCon;
			//if($search_org_ass_id && $InGroupHRG==true){
			if($search_org_ass_id){
				 $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID=$search_org_ass_id)";
			 }
					
		}
					
			
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}
	
	}
	
	if($DPISDB=="odbc"){
			$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
							 from			(
							 						(
														PER_PERSONAL a
														inner join $position_table b on ($position_join)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							 $search_condition $CON_PER_AUDIT_FLAG
							 order by		$order_by ";
		}elseif($DPISDB=="oci8"){
			$search_condition = str_replace("where", "and", $search_condition);
			$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
							 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d
							 where		a.PER_STATUS=1 and $position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+)
												$search_condition $CON_PER_AUDIT_FLAG
							 order by		$order_by ";
//			$cmd = "select * from (select rownum rnum, q1.* from ( ".$cmd." )  q1) where rnum between 0 and 100";
		}elseif($DPISDB=="mysql"){
			$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
							 from			(
							 						(
														PER_PERSONAL a
														inner join $position_table b on ($position_join)
													) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
												) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
							 $search_condition $CON_PER_AUDIT_FLAG
							 order by		$order_by ";
		} // end if
	
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
        //echo '<pre>'.$cmd;
        //die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count = 0;
	while($data = $db_dpis->get_array()){		
		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$ORG_NAME = $data[ORG_NAME];
		$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);

		$arr_content[$data_count][type] = "PERSON";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][org_name] = $ORG_NAME;
		$arr_content[$data_count][pos_no] = $POS_NO;
		
		count_absent($PER_ID);

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$colshow_cnt=0;		// หาจำนวน column ที่แสดงจริง
		for($i=0; $i<count($arr_column_sel); $i++){
			if ($arr_column_sel[$arr_column_map[$i]]==1) $colshow_cnt++;
		}

		$xlsRow = 0;
		$arr_title = explode("||", $report_title);
		for($i=0; $i<count($arr_title); $i++){
			$worksheet->write($xlsRow, 0, $arr_title[$i], set_format("xlsFmtTitle", "B", "C", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //for($i=0; $i<count($arr_title); $i++){

		if($company_name){
			$worksheet->write($xlsRow, 0, $company_name, set_format("xlsFmtSubTitle", "B", "L", "", 1));
			for($j=0; $j < $colshow_cnt-1; $j++) 
				$worksheet->write($xlsRow, ($j+1), "", set_format("xlsFmtTitle", "B", "C", "", 1));
			$xlsRow++;
		} //if($company_name){

//		print_header();		

		// กำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล
			$wsdata_fontfmt_1 = array("B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B","B");
			$wsdata_align_1 = array("C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			$wsdata_align_2 = array("L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L","L");
			$wsdata_align_total = array("R","R","R","C","C","C","C","C","C","C","C","C","C","C","C","C","C","C");
			$wsdata_border_1 = array("TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB","TLRB");
			$wsdata_border_2 = array("","","","","","","","","","","","","","","","","","");
			$wsdata_colmerge_1 = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_colmerge_name = array(1,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1);
			$wsdata_colmerge_total = array(1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
			$wsdata_fontfmt_2 = array("","","","","","","","","","","","","","","","","","");
		// จบกำหนดค่าตัวแปรเพื่อพิมพ์ ส่วนข้อมูล

		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORDER = $arr_content[$data_count][ORDER];
			$STARTDATE = $arr_content[$data_count][STARTDATE];
			$ENDDATE = $arr_content[$data_count][ENDDATE];
			for($i=1; $i<=15; $i++) ${"DAY_".str_pad($i, 2, "0", STR_PAD_LEFT)} = $arr_content[$data_count]["DAY_".str_pad($i, 2, "0", STR_PAD_LEFT)];			//ลาผักผ่อนนับวันหยุด (11)
			 
			if($REPORT_ORDER == "PERSON"){
				if($arr_content[($data_count + 1)][type] != "PERSON" && ($data_count < (count($arr_content) - 1))){ 
					if($data_count > 0){ 
						$arr_data = (array) null;
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
						$arr_data[] = "";
                                                $arr_data[] = "";
                                                

						$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
							
						$xlsRow++;
						$colseq=0;
						for($i=0; $i < count($arr_column_map); $i++) {
							if ($arr_column_sel[$arr_column_map[$i]]==1) {
								if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
								else $ndata = $arr_data[$arr_column_map[$i]];
								$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
								$colseq++;
							}
						}
					} // end if
					
					$arr_data = (array) null;
					$arr_data[] = "ชื่อ $NAME";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "สังกัด $ORG_NAME";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "เลขที่ตำแหน่ง ".$POS_NO;
					$arr_data[] = "";
                                        $arr_data[] = "";

					$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
						
					$xlsRow++;
					$colseq=0;
					for($i=0; $i < count($arr_column_map); $i++) {
						if ($arr_column_sel[$arr_column_map[$i]]==1) {
							if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
							else $ndata = $arr_data[$arr_column_map[$i]];
							if (trim($ndata))
								$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_2[$arr_column_map[$i]], $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
							else
								$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_1[$arr_column_map[$i]], $wsdata_align_2[$arr_column_map[$i]], $wsdata_border_2[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
						}
					}

					$xlsRow++;
					print_header();
				}else{
				} // end if
			}elseif($REPORT_ORDER == "ABSENT"){
				$arr_data = (array) null;
				$arr_data[] = $ORDER;
				$arr_data[] = $STARTDATE;
				$arr_data[] = $ENDDATE;
				$arr_data[] = $DAY_01;
				$arr_data[] = $DAY_03;
				$arr_data[] = $DAY_10;
				$arr_data[] = $DAY_04;
				$arr_data[] = $DAY_13;
				$arr_data[] = $DAY_02;
				$arr_data[] = $DAY_05;
				$arr_data[] = $DAY_06;
				$arr_data[] = $DAY_07;
				$arr_data[] = $DAY_08;
				$arr_data[] = $DAY_09;
				$arr_data[] = $DAY_14;
				$arr_data[] = $DAY_15;
				$arr_data[] = $DAY_11;
                                $arr_data[] = $DAY_12;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$worksheet->write_string($xlsRow, $colseq, $ndata, set_format("xlsFmtTitle", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]]));
						$colseq++;
					}
				}
			}elseif($REPORT_ORDER == "TOTAL"){
				$arr_data = (array) null;
				$arr_data[] = "<**1**>รวม";
				$arr_data[] = "<**1**>รวม";
				$arr_data[] = "<**1**>รวม";
				$arr_data[] = $DAY_01;
				$arr_data[] = $DAY_03;
				$arr_data[] = $DAY_10;
				$arr_data[] = $DAY_04;
				$arr_data[] = $DAY_13;
				$arr_data[] = $DAY_02;
				$arr_data[] = $DAY_05;
				$arr_data[] = $DAY_06;
				$arr_data[] = $DAY_07;
				$arr_data[] = $DAY_08;
				$arr_data[] = $DAY_09;
				$arr_data[] = $DAY_14;
				$arr_data[] = $DAY_15;
				$arr_data[] = $DAY_11;
                                $arr_data[] = $DAY_12;

				$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
					
				$xlsRow++;
				$colseq=0;
				for($i=0; $i < count($arr_column_map); $i++) {
					if ($arr_column_sel[$arr_column_map[$i]]==1) {
						if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
						else $ndata = $arr_data[$arr_column_map[$i]];
						$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_total[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
						$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
						$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge));
						$colseq++;
					}
				}

			} // end if			
		} // end for
	}else{
		$xlsRow = 0;
		$arr_data = (array) null;
		$arr_data[] = "***** ไม่มีข้อมูล *****";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
		$arr_data[] = "";
                $arr_data[] = "";

		$arr_aggreg_data = explode("|",do_aggregate($arr_data, $column_function, $arr_column_sel, $arr_column_map));
			
		$xlsRow++;
		$colseq=0;
		for($i=0; $i < count($arr_column_map); $i++) {
			if ($arr_column_sel[$arr_column_map[$i]]==1) {
				if ($arr_aggreg_data[$i]) $ndata = $arr_aggreg_data[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
				else $ndata = $arr_data[$arr_column_map[$i]];
				$buff = explode("|",doo_merge_cell($ndata, $wsdata_border_1[$arr_column_map[$i]], $wsdata_colmerge_1[$arr_column_map[$i]], $pgrp, ($i == $colshow_cnt-1)));
				$ndata = $buff[0]; $border = $buff[1]; $merge = $buff[2]; $pgrp = $buff[3];
				if (trim($ndata))
					$worksheet->write($xlsRow, $colseq, $ndata, set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge));
				else
					$worksheet->write($xlsRow, $colseq, "", set_format("xlsFmtTableDetail", $wsdata_fontfmt_2[$arr_column_map[$i]], $wsdata_align_1[$arr_column_map[$i]], $border, $merge));
				$colseq++;
			}
		}
	} // end if

	$workbook->close();

	ini_set("max_execution_time", 30);
	
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