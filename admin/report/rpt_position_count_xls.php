<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require_once "../../Excel/class.writeexcel_workbook.inc.php";
	require_once "../../Excel/class.writeexcel_worksheet.inc.php";

	set_time_limit(3600);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if(!trim($RPTORD_LIST)) $RPTORD_LIST = "MINISTRY";
	
	$arr_order_name = explode("|", $RPTORD_LIST);

	foreach($arr_order_name as $ORDER_NAME){
		switch($ORDER_NAME){
			case "MINISTRY" :
				$arr_order_text[$ORDER_NAME] = "$MINISTRY_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "d.ORG_SEQ_NO, d.ORG_NAME as MINISTRY_NAME";
				$arr_group_list[] = "d.ORG_SEQ_NO, d.ORG_NAME";
				$arr_order_list[] = "d.ORG_SEQ_NO, d.ORG_NAME";
				break;
			case "DEPARTMENT" :
				$arr_order_text[$ORDER_NAME] = "$DEPARTMENT_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "c.ORG_SEQ_NO, c.ORG_NAME as DEPARTMENT_NAME";
				$arr_group_list[] = "c.ORG_SEQ_NO, c.ORG_NAME";
				$arr_order_list[] = "c.ORG_SEQ_NO, c.ORG_NAME";
				break;
			case "ORG" :
				$arr_order_text[$ORDER_NAME] = "$ORG_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "b.ORG_SEQ_NO, b.ORG_NAME";
				$arr_group_list[] = "b.ORG_SEQ_NO, b.ORG_NAME";
				$arr_order_list[] = "b.ORG_SEQ_NO, b.ORG_NAME";
				break;
			case "PERMGT" :
				$arr_order_text[$ORDER_NAME] = "$PM_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.PM_CODE, l.PM_NAME";
				$arr_group_list[] = "a.PM_CODE, l.PM_NAME";
				$arr_order_list[] = "a.PM_CODE";
				if($DPISDB=="odbc"){
					$JOIN_PER_MGT[TABLE] = " left join PER_MGT l on (a.PM_CODE=l.PM_CODE)";
				}elseif($DPISDB=="oci8"){
					$JOIN_PER_MGT[TABLE] = ", PER_MGT l";
					$JOIN_PER_MGT[COND] = "and a.PM_CODE=l.PM_CODE(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_PER_MGT[TABLE] = " left join PER_MGT l on (a.PM_CODE=l.PM_CODE)";
				}
				break;
			case "PERLINE" :
				$arr_order_text[$ORDER_NAME] = "$PL_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.PL_CODE, j.PL_NAME";
				$arr_group_list[] = "a.PL_CODE, j.PL_NAME";
				$arr_order_list[] = "a.PL_CODE";
				if($DPISDB=="odbc"){
					$JOIN_PER_LINE[TABLE] = " left join PER_LINE j on (a.PL_CODE=j.PL_CODE)";
				}elseif($DPISDB=="oci8"){
					$JOIN_PER_LINE[TABLE] = ", PER_LINE j";
					$JOIN_PER_LINE[COND] = "and a.PL_CODE=j.PL_CODE(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_PER_LINE[TABLE] = " left join PER_LINE j on (a.PL_CODE=j.PL_CODE)";
				}
				break;
			case "COLEVEL" :
				$arr_order_text[$ORDER_NAME] = "ระดับ (เดิม)";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.CL_NAME";
				$arr_group_list[] = "a.CL_NAME";
				$arr_order_list[] = "a.CL_NAME";
				break;
			case "LEVEL" :
				$arr_order_text[$ORDER_NAME] = "ระดับ (ใหม่)";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.LEVEL_NO";
				$arr_group_list[] = "a.LEVEL_NO";
				$arr_order_list[] = "a.LEVEL_NO";
				break;
			case "PERTYPE" :
				$arr_order_text[$ORDER_NAME] = "$PT_TITLE";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.PT_CODE, k.PT_NAME";
				$arr_group_list[] = "a.PT_CODE, k.PT_NAME";
				$arr_order_list[] = "a.PT_CODE";
				if($DPISDB=="odbc"){
					$JOIN_PER_TYPE[TABLE] = " left join PER_TYPE k on (a.PT_CODE=k.PT_CODE)";
				}elseif($DPISDB=="oci8"){
					$JOIN_PER_TYPE[TABLE] = ", PER_TYPE k";
					$JOIN_PER_TYPE[COND] = "and a.PT_CODE=k.PT_CODE(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_PER_TYPE[TABLE] = " left join PER_TYPE k on (a.PT_CODE=k.PT_CODE)";
				}
				break;
			case "SALARY" :
				$arr_order_text[$ORDER_NAME] = "เงินเดือน";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "a.POS_SALARY";
				$arr_group_list[] = "a.POS_SALARY";
				$arr_order_list[] = "a.POS_SALARY";
				break;
			case "KNOWLEDGE" :
				$arr_order_text[$ORDER_NAME] = "ความรู้";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "f.JOB_DES_ID as KNOWLEDGE_ID, m.JOB_DES_NAME as KNOWLEDGE_NAME";
				$arr_group_list[] = "f.JOB_DES_ID, m.JOB_DES_NAME";
				$arr_order_list[] = "f.JOB_DES_ID";
				if($DPISDB=="odbc"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_KNOWLEDGE[TABLE] = " left join POS_JOB_DES_PRIMARY f on (e.POS_DES_ID=f.POS_DES_ID and f.JOB_TYPE='k')";
					$JOIN_KNOWLEDGE_INFO[TABLE] = " left join KNOWLEDGE_INFO m on (f.JOB_DES_ID=m.JOB_DES_ID)";
				}elseif($DPISDB=="oci8"){
					$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
					$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
					$JOIN_KNOWLEDGE[TABLE] = ", POS_JOB_DES_PRIMARY f";
					$JOIN_KNOWLEDGE[COND] = "and e.POS_DES_ID=f.POS_DES_ID(+) and f.JOB_TYPE='k'";
					$JOIN_KNOWLEDGE_INFO[TABLE] = ", KNOWLEDGE_INFO m";
					$JOIN_KNOWLEDGE_INFO[COND] = "and f.JOB_DES_ID=m.JOB_DES_ID(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_KNOWLEDGE[TABLE] = " left join POS_JOB_DES_PRIMARY f on (e.POS_DES_ID=f.POS_DES_ID and f.JOB_TYPE='k')";
					$JOIN_KNOWLEDGE_INFO[TABLE] = " left join KNOWLEDGE_INFO m on (f.JOB_DES_ID=m.JOB_DES_ID)";
				}
				break;
			case "SKILL" :
				$arr_order_text[$ORDER_NAME] = "ทักษะ";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "g.JOB_DES_ID as SKILL_ID, n.JOB_DES_NAME as SKILL_NAME";
				$arr_group_list[] = "g.JOB_DES_ID, n.JOB_DES_NAME";
				$arr_order_list[] = "g.JOB_DES_ID";
				if($DPISDB=="odbc"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_SKILL[TABLE] = " left join POS_JOB_DES_PRIMARY g on (e.POS_DES_ID=g.POS_DES_ID and g.JOB_TYPE='s')";
					$JOIN_SKILL_INFO[TABLE] = " left join SKILL_INFO n on (g.JOB_DES_ID=n.JOB_DES_ID)";
				}elseif($DPISDB=="oci8"){
					$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
					$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
					$JOIN_SKILL[TABLE] = ", POS_JOB_DES_PRIMARY g";
					$JOIN_SKILL[COND] = "and e.POS_DES_ID=g.POS_DES_ID(+) and g.JOB_TYPE='s'";
					$JOIN_SKILL_INFO[TABLE] = ", SKILL_INFO n";
					$JOIN_SKILL_INFO[COND] = "and g.JOB_DES_ID=n.JOB_DES_ID(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_SKILL[TABLE] = " left join POS_JOB_DES_PRIMARY g on (e.POS_DES_ID=g.POS_DES_ID and g.JOB_TYPE='s')";
					$JOIN_SKILL_INFO[TABLE] = " left join SKILL_INFO n on (g.JOB_DES_ID=n.JOB_DES_ID)";
				}
				break;
			case "EXP" :
				$arr_order_text[$ORDER_NAME] = "ประสบการณ์";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "h.JOB_DES_ID as EXP_ID, o.JOB_DES_NAME as EXP_NAME";
				$arr_group_list[] = "h.JOB_DES_ID, o.JOB_DES_NAME";
				$arr_order_list[] = "h.JOB_DES_ID";
				if($DPISDB=="odbc"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_EXP[TABLE] = " left join POS_JOB_DES_PRIMARY h on (e.POS_DES_ID=h.POS_DES_ID and h.JOB_TYPE='e')";
					$JOIN_EXP_INFO[TABLE] = " left join EXP_INFO o on (h.JOB_DES_ID=o.JOB_DES_ID)";
				}elseif($DPISDB=="oci8"){
					$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
					$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
					$JOIN_EXP[TABLE] = ", POS_JOB_DES_PRIMARY h";
					$JOIN_EXP[COND] = "and e.POS_DES_ID=h.POS_DES_ID(+) and h.JOB_TYPE='e'";
					$JOIN_EXP_INFO[TABLE] = ", EXP_INFO o";
					$JOIN_EXP_INFO[COND] = "and h.JOB_DES_ID=o.JOB_DES_ID(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_EXP[TABLE] = " left join POS_JOB_DES_PRIMARY h on (e.POS_DES_ID=h.POS_DES_ID and h.JOB_TYPE='e')";
					$JOIN_EXP_INFO[TABLE] = " left join EXP_INFO o on (h.JOB_DES_ID=o.JOB_DES_ID)";
				}
				break;
			case "COMPETENCY" :
				$arr_order_text[$ORDER_NAME] = "สมรรถนะ";
				$arr_order_width[$ORDER_NAME] = 20;
				$arr_select_list[] = "i.JOB_DES_ID as COMPETENCY_ID, p.JOB_DES_NAME as COMPETENCY_NAME";
				$arr_group_list[] = "i.JOB_DES_ID, p.JOB_DES_NAME";
				$arr_order_list[] = "i.JOB_DES_ID";
				if($DPISDB=="odbc"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_COMPETENCY[TABLE] = " left join POS_JOB_DES_PRIMARY i on (e.POS_DES_ID=i.POS_DES_ID and i.JOB_TYPE='c')";
					$JOIN_COMPETENCY_INFO[TABLE] = " left join COMPETENCY_INFO p on (i.JOB_DES_ID=p.JOB_DES_ID)";
				}elseif($DPISDB=="oci8"){
					$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
					$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
					$JOIN_COMPETENCY[TABLE] = ", POS_JOB_DES_PRIMARY i";
					$JOIN_COMPETENCY[COND] = "and e.POS_DES_ID=i.POS_DES_ID(+) and i.JOB_TYPE='c'";
					$JOIN_COMPETENCY_INFO[TABLE] = ", COMPETENCY_INFO p";
					$JOIN_COMPETENCY_INFO[COND] = "and i.JOB_DES_ID=p.JOB_DES_ID(+)";
				}elseif($DPISDB=="mssql"){
					$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
					$JOIN_COMPETENCY[TABLE] = " left join POS_JOB_DES_PRIMARY i on (e.POS_DES_ID=i.POS_DES_ID and i.JOB_TYPE='c')";
					$JOIN_COMPETENCY_INFO[TABLE] = " left join COMPETENCY_INFO p on (i.JOB_DES_ID=p.JOB_DES_ID)";
				}
				break;
		} // switch case
	} // loop foreach
	
	$select_list = implode(",", $arr_select_list);
	$groupby = implode(",", $arr_group_list);
	$orderby = implode(",", $arr_order_list);

	$search_condition = "";
//	$arr_search_condition[] = "(a.POS_STATUS=1)";

	$list_type_text = $ALL_REPORT_TITLE;

	if(trim($search_pm_code)){ 
		$search_pm_code = trim($search_pm_code);
		$arr_search_condition[] = "(a.PM_CODE = '$search_pm_code')";
		$list_type_text .= "$search_pm_name";

		if($DPISDB=="odbc"){
			$JOIN_PER_MGT[TABLE] = " left join PER_MGT l on (a.PM_CODE=l.PM_CODE)";
		}elseif($DPISDB=="oci8"){
			$JOIN_PER_MGT[TABLE] = ", PER_MGT l";
			$JOIN_PER_MGT[COND] = "and a.PM_CODE=e.PM_CODE(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_PER_MGT[TABLE] = " left join PER_MGT l on (a.PM_CODE=l.PM_CODE)";
		}
	} // end if
	if(trim($search_pl_code)){ 
		$search_pl_code = trim($search_pl_code);
		$arr_search_condition[] = "(a.PL_CODE = '$search_pl_code')";
		$list_type_text .= "$search_pl_name";

		if($DPISDB=="odbc"){
			$JOIN_PER_LINE[TABLE] = " left join PER_LINE j on (a.PL_CODE=j.PL_CODE)";
		}elseif($DPISDB=="oci8"){
			$JOIN_PER_LINE[TABLE] = ", PER_LINE j";
			$JOIN_PER_LINE[COND] = "and a.PL_CODE=j.PL_CODE(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_PER_LINE[TABLE] = " left join PER_LINE j on (a.PL_CODE=j.PL_CODE)";
		}
	} // end if
	if(trim($search_cl_code)){ 
		$search_cl_code = trim($search_cl_code);
		$arr_search_condition[] = "(a.CL_NAME = '$search_cl_code')";
		$list_type_text .= "$search_cl_name";
	} // end if
	if(trim($search_lv_code)){ 
		$search_lv_code = trim($search_lv_code);
		$arr_search_condition[] = "(a.LEVEL_NO = '$search_lv_code')";
		$list_type_text .= "$search_lv_name";
	} // end if

	if(trim($search_org_id)){ 
		$arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME - $search_org_name";
	}elseif($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(b.PV_CODE = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	} // end if

	if(trim($search_pt_code)){ 
		$search_pt_code = trim($search_pt_code);
		$arr_search_condition[] = "(a.PT_CODE = '$search_pt_code')";
		$list_type_text .= "$search_pt_name";

		if($DPISDB=="odbc"){
			$JOIN_PER_TYPE[TABLE] = " left join PER_TYPE k on (a.PT_CODE=k.PT_CODE)";
		}elseif($DPISDB=="oci8"){
			$JOIN_PER_TYPE[TABLE] = ", PER_TYPE k";
			$JOIN_PER_TYPE[COND] = "and a.PT_CODE=k.PT_CODE(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_PER_TYPE[TABLE] = " left join PER_TYPE k on (a.PT_CODE=k.PT_CODE)";
		}
	} // end if
	if(trim($search_pos_salary)){ 
		$search_pos_salary = trim($search_pos_salary);
		$arr_search_condition[] = "(a.POS_SALARY = $search_pos_salary)";
		$list_type_text .= "$search_pos_salary";
	} // end if

	if(trim($search_knowledge_id)){ 
		$search_knowledge_id = trim($search_knowledge_id);
		$arr_search_condition[] = "(f.JOB_DES_ID = $search_knowledge_id)";
		$list_type_text .= "$search_knowledge_name";

		if($DPISDB=="odbc"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_KNOWLEDGE[TABLE] = " left join POS_JOB_DES_PRIMARY f on (e.POS_DES_ID=f.POS_DES_ID and f.JOB_TYPE='k')";
			$JOIN_KNOWLEDGE_INFO[TABLE] = " left join KNOWLEDGE_INFO m on (f.JOB_DES_ID=m.JOB_DES_ID)";
		}elseif($DPISDB=="oci8"){
			$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
			$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
			$JOIN_KNOWLEDGE[TABLE] = ", POS_JOB_DES_PRIMARY f";
			$JOIN_KNOWLEDGE[COND] = "and e.POS_DES_ID=f.POS_DES_ID(+) and f.JOB_TYPE='k'";
			$JOIN_KNOWLEDGE_INFO[TABLE] = ", KNOWLEDGE_INFO m";
			$JOIN_KNOWLEDGE_INFO[COND] = "and f.JOB_DES_ID=m.JOB_DES_ID(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_KNOWLEDGE[TABLE] = " left join POS_JOB_DES_PRIMARY f on (e.POS_DES_ID=f.POS_DES_ID and f.JOB_TYPE='k')";
			$JOIN_KNOWLEDGE_INFO[TABLE] = " left join KNOWLEDGE_INFO m on (f.JOB_DES_ID=m.JOB_DES_ID)";
		}
	} // end if
	if(trim($search_skill_id)){ 
		$search_skill_id = trim($search_skill_id);
		$arr_search_condition[] = "(g.JOB_DES_ID = $search_skill_id)";
		$list_type_text .= "$search_skill_name";

		if($DPISDB=="odbc"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_SKILL[TABLE] = " left join POS_JOB_DES_PRIMARY g on (e.POS_DES_ID=g.POS_DES_ID and g.JOB_TYPE='s')";
			$JOIN_SKILL_INFO[TABLE] = " left join SKILL_INFO n on (g.JOB_DES_ID=n.JOB_DES_ID)";
		}elseif($DPISDB=="oci8"){
			$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
			$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
			$JOIN_SKILL[TABLE] = ", POS_JOB_DES_PRIMARY g";
			$JOIN_SKILL[COND] = "and e.POS_DES_ID=g.POS_DES_ID(+) and g.JOB_TYPE='s'";
			$JOIN_SKILL_INFO[TABLE] = ", SKILL_INFO n";
			$JOIN_SKILL_INFO[COND] = "and g.JOB_DES_ID=n.JOB_DES_ID(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_SKILL[TABLE] = " left join POS_JOB_DES_PRIMARY g on (e.POS_DES_ID=g.POS_DES_ID and g.JOB_TYPE='s')";
			$JOIN_SKILL_INFO[TABLE] = " left join SKILL_INFO n on (g.JOB_DES_ID=n.JOB_DES_ID)";
		}
	} // end if
	if(trim($search_exp_id)){ 
		$search_exp_id = trim($search_exp_id);
		$arr_search_condition[] = "(h.JOB_DES_ID = $search_exp_id)";
		$list_type_text .= "$search_exp_name";

		if($DPISDB=="odbc"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_EXP[TABLE] = " left join POS_JOB_DES_PRIMARY h on (e.POS_DES_ID=h.POS_DES_ID and h.JOB_TYPE='e')";
			$JOIN_EXP_INFO[TABLE] = " left join EXP_INFO o on (h.JOB_DES_ID=o.JOB_DES_ID)";
		}elseif($DPISDB=="oci8"){
			$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
			$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
			$JOIN_EXP[TABLE] = ", POS_JOB_DES_PRIMARY h";
			$JOIN_EXP[COND] = "and e.POS_DES_ID=h.POS_DES_ID(+) and h.JOB_TYPE='e'";
			$JOIN_EXP_INFO[TABLE] = ", EXP_INFO o";
			$JOIN_EXP_INFO[COND] = "and h.JOB_DES_ID=o.JOB_DES_ID(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_EXP[TABLE] = " left join POS_JOB_DES_PRIMARY h on (e.POS_DES_ID=h.POS_DES_ID and h.JOB_TYPE='e')";
			$JOIN_EXP_INFO[TABLE] = " left join EXP_INFO o on (h.JOB_DES_ID=o.JOB_DES_ID)";
		}
	} // end if
	if(trim($search_competency_id)){ 
		$search_competency_id = trim($search_competency_id);
		$arr_search_condition[] = "(i.JOB_DES_ID = $search_competency_id)";
		$list_type_text .= "$search_competency_name";

		if($DPISDB=="odbc"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_COMPETENCY[TABLE] = " left join POS_JOB_DES_PRIMARY i on (e.POS_DES_ID=i.POS_DES_ID and i.JOB_TYPE='c')";
			$JOIN_COMPETENCY_INFO[TABLE] = " left join COMPETENCY_INFO p on (i.JOB_DES_ID=p.JOB_DES_ID)";
		}elseif($DPISDB=="oci8"){
			$JOIN_POS_DES_INFO[TABLE] = ", POS_DES_INFO e";
			$JOIN_POS_DES_INFO[COND] = "and a.PL_CODE=e.PL_CODE(+) and a.LEVEL_NO=e.LEVEL_NO(+)";
			$JOIN_COMPETENCY[TABLE] = ", POS_JOB_DES_PRIMARY i";
			$JOIN_COMPETENCY[COND] = "and e.POS_DES_ID=i.POS_DES_ID(+) and i.JOB_TYPE='c'";
			$JOIN_COMPETENCY_INFO[TABLE] = ", COMPETENCY_INFO p";
			$JOIN_COMPETENCY_INFO[COND] = "and i.JOB_DES_ID=p.JOB_DES_ID(+)";
		}elseif($DPISDB=="mssql"){
			$JOIN_POS_DES_INFO[TABLE] = " left join POS_DES_INFO e on (a.PL_CODE=e.PL_CODE and a.LEVEL_NO=e.LEVEL_NO)";
			$JOIN_COMPETENCY[TABLE] = " left join POS_JOB_DES_PRIMARY i on (e.POS_DES_ID=i.POS_DES_ID and i.JOB_TYPE='c')";
			$JOIN_COMPETENCY_INFO[TABLE] = " left join COMPETENCY_INFO p on (i.JOB_DES_ID=p.JOB_DES_ID)";
		}
	} // end if

	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$report_title = "ข้อมูลตำแหน่ง";
	$report_code = "ข้อมูลตำแหน่ง";

	$token = md5(uniqid(rand(), true)); 
	$fname= "../../Excel/tmp/jethro_$token.xls";

	$workbook = new writeexcel_workbook($fname);
	
	//====================== SET FORMAT ======================//
	require_once "../../Excel/my.defined_format.inc.php";	// use set_format(set of parameter) funtion , help is in file
	//====================== SET FORMAT ======================//

	function print_header(){
		global $worksheet, $xlsRow;
		global $heading_name;
		global $arr_order_name, $arr_order_width, $arr_order_text, $total_order;
		
		$xlsRow++;
		$worksheet->set_row($xlsRow, 20);
		$worksheet->write($xlsRow, 0, "ข้อมูลที่คำนวณและตรวจสอบ", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		for($i=1; $i<$total_order; $i++){
			$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=43&setRotation=0&valignment=&fontSize=&wrapText=1"));
		} // loop for
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, ($total_order - 1));
		$worksheet->write($xlsRow, $total_order, "จำนวน (ตำแหน่ง)", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLR&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));

		$xlsRow++;
		$worksheet->set_row($xlsRow, 20);
		for($i=0; $i<$total_order; $i++){
			$worksheet->write($xlsRow, $i, $arr_order_text[$arr_order_name[$i]], set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=TLRB&isMerge=0&fgColor=black&bgColor=51&setRotation=0&valignment=&fontSize=&wrapText=1"));
		} // loop for
		$worksheet->write($xlsRow, $total_order, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=C&border=LRB&isMerge=0&fgColor=black&bgColor=41&setRotation=0&valignment=&fontSize=&wrapText=1"));
	} // function		
	
	if($DPISDB=="odbc"){
		$cmd = " 	select		distinct 
											$select_list, 
											count(POS_ID) as POSITION_COUNT
							from		(
												(	
													(
														(
															(
																(
																	(
																		(
																			(
																				(
																					(
																						(
																							(
																								(
																									PER_POSITION a
																									inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																								) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
																							) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
																						)".$JOIN_PER_LINE[TABLE]."
																					)".$JOIN_PER_TYPE[TABLE]."
																				)".$JOIN_PER_MGT[TABLE]."
																			)".$JOIN_POS_DES_INFO[TABLE]."
																		)".$JOIN_KNOWLEDGE[TABLE]."
																	)".$JOIN_KNOWLEDGE_INFO[TABLE]."
																)".$JOIN_SKILL[TABLE]."
															)".$JOIN_SKILL_INFO[TABLE]."
														)".$JOIN_EXP[TABLE]."
													)".$JOIN_EXP_INFO[TABLE]."
												)".$JOIN_COMPETENCY[TABLE]."
											)".$JOIN_COMPETENCY_INFO[TABLE]."
							$search_condition
							group by $groupby
							order by	$orderby ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);	
		$cmd = " 	select		distinct 
											$select_list, 
											count(POS_ID) as POSITION_COUNT
							from		PER_POSITION a, PER_ORG b, PER_ORG c, PER_ORG d
											".$JOIN_PER_LINE[TABLE]."
											".$JOIN_PER_TYPE[TABLE]."
											".$JOIN_PER_MGT[TABLE]."	
											".$JOIN_POS_DES_INFO[TABLE]."	
											".$JOIN_KNOWLEDGE[TABLE]."	
											".$JOIN_KNOWLEDGE_INFO[TABLE]."	
											".$JOIN_SKILL[TABLE]."	
											".$JOIN_SKILL_INFO[TABLE]."	
											".$JOIN_EXP[TABLE]."	
											".$JOIN_EXP_INFO[TABLE]."	
											".$JOIN_COMPETENCY[TABLE]."	
											".$JOIN_COMPETENCY_INFO[TABLE]."	
							where		a.ORG_ID=b.ORG_ID and b.ORG_ID_REF=c.ORG_ID and c.ORG_ID_REF=d.ORG_ID
											".$JOIN_PER_LINE[COND]."
											".$JOIN_PER_TYPE[COND]."
											".$JOIN_PER_MGT[COND]."	
											".$JOIN_POS_DES_INFO[COND]."	
											".$JOIN_KNOWLEDGE[COND]."	
											".$JOIN_KNOWLEDGE_INFO[COND]."	
											".$JOIN_SKILL[COND]."	
											".$JOIN_SKILL_INFO[COND]."	
											".$JOIN_EXP[COND]."	
											".$JOIN_EXP_INFO[COND]."	
											".$JOIN_COMPETENCY[COND]."	
											".$JOIN_COMPETENCY_INFO[COND]."	
											$search_condition
							group by $groupby
							order by	$orderby ";
	}elseif($DPISDB=="mssql"){
		$cmd = " 	select		distinct 
											$select_list, 
											count(POS_ID) as POSITION_COUNT
							from		(
												(	
													(
														(
															(
																(
																	(
																		(
																			(
																				(
																					(
																						(
																							(
																								(
																									PER_POSITION a
																									inner join PER_ORG b on (a.ORG_ID=b.ORG_ID)
																								) inner join PER_ORG c on (b.ORG_ID_REF=c.ORG_ID)
																							) inner join PER_ORG d on (c.ORG_ID_REF=d.ORG_ID)
																						)".$JOIN_PER_LINE[TABLE]."
																					)".$JOIN_PER_TYPE[TABLE]."
																				)".$JOIN_PER_MGT[TABLE]."
																			)".$JOIN_POS_DES_INFO[TABLE]."
																		)".$JOIN_KNOWLEDGE[TABLE]."
																	)".$JOIN_KNOWLEDGE_INFO[TABLE]."
																)".$JOIN_SKILL[TABLE]."
															)".$JOIN_SKILL_INFO[TABLE]."
														)".$JOIN_EXP[TABLE]."
													)".$JOIN_EXP_INFO[TABLE]."
												)".$JOIN_COMPETENCY[TABLE]."
											)".$JOIN_COMPETENCY_INFO[TABLE]."
							$search_condition
							group by $groupby
							order by	$orderby ";
	}
	if($SESS_ORG_STRUCTURE==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo $cmd;
	$data_count = 0;
	
	while($data = $db_dpis->get_array()){
		foreach($arr_order_name as $ORDER_NAME){
			switch($ORDER_NAME){
				case "MINISTRY" :
					$MINISTRY_NAME = $data[MINISTRY_NAME];
					if(!$MINISTRY_NAME) $MINISTRY_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $MINISTRY_NAME;
					break;
				case "DEPARTMENT" :
					$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
					if(!$DEPARTMENT_NAME) $DEPARTMENT_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $DEPARTMENT_NAME;
					break;
				case "ORG" :
					$ORG_NAME = $data[ORG_NAME];
					if(!$ORG_NAME) $ORG_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $ORG_NAME;
					break;
				case "PERMGT" :
					$PM_CODE = $data[PM_CODE];
					$PM_NAME = $data[PM_NAME];					
//					$cmd = " select PM_NAME from PER_MGT where PM_CODE='".$PM_CODE."' ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$PM_NAME = $data_dpis2[PM_NAME];
					if(!$PM_NAME) $PM_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $PM_NAME;
					break;
				case "PERLINE" :
					$PL_CODE = $data[PL_CODE];
					$PL_NAME = $data[PL_NAME];
//					$cmd = " select PL_NAME from PER_LINE where PL_CODE='".$PL_CODE."' ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$PL_NAME = $data_dpis2[PL_NAME];
					if(!$PL_NAME) $PL_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $PL_NAME;
					break;
				case "COLEVEL" :
					$CL_NAME = $data[CL_NAME];
					if(!$CL_NAME) $CL_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $CL_NAME;
					break;
				case "LEVEL" :
					$LEVEL_NO = $data[LEVEL_NO];
					if($LEVEL_NO == "NOT SPECIFY" || !$LEVEL_NO){ 
						$LEVEL_NAME = "ยังไม่มีการกำหนดตำแหน่งงานใหม่";
					}elseif($LEVEL_NO){
						$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
						$db_dpis2->send_cmd($cmd);
						$data_dpis2 = $db_dpis2->get_array();
						$LEVEL_NAME = $data_dpis2[LEVEL_NAME];
					} // end if
					if(!$LEVEL_NAME) $LEVEL_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $LEVEL_NAME;
					break;
				case "PERTYPE" :
					$PT_CODE = $data[PT_CODE];
					$PT_NAME = $data[PT_NAME];
//					$cmd = " select PT_NAME from PER_TYPE where PT_CODE='".$PT_CODE."' ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$PT_NAME = $data_dpis2[PT_NAME];
					if(!$PT_NAME) $PT_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $PT_NAME;
					break;
				case "SALARY" :
					$PER_SALARY = $data[PER_SALARY];
					if($PER_SALARY=="") $PER_SALARY = "-";
					$arr_content[$data_count][$ORDER_NAME] = number_format($PER_SALARY);
					break;
				case "KNOWLEDGE" :
					$JOB_DES_ID = $data[KNOWLEDGE_ID];
					$JOB_DES_NAME = $data[KNOWLEDGE_NAME];
//					$cmd = " select JOB_DES_NAME from KNOWLEDGE_INFO where JOB_DES_ID=$JOB_DES_ID ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$JOB_DES_NAME = $data_dpis2[JOB_DES_NAME];
					if(!$JOB_DES_NAME) $JOB_DES_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $JOB_DES_NAME;
					break;
				case "SKILL" :
					$JOB_DES_ID = $data[SKILL_ID];
					$JOB_DES_NAME = $data[SKILL_NAME];
//					$cmd = " select JOB_DES_NAME from SKILL_INFO where JOB_DES_ID=$JOB_DES_ID ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$JOB_DES_NAME = $data_dpis2[JOB_DES_NAME];
					if(!$JOB_DES_NAME) $JOB_DES_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $JOB_DES_NAME;
					break;
				case "EXP" :
					$JOB_DES_ID = $data[EXP_ID];
					$JOB_DES_NAME = $data[EXP_NAME];
//					$cmd = " select JOB_DES_NAME from EXP_INFO where JOB_DES_ID=$JOB_DES_ID ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$JOB_DES_NAME = $data_dpis2[JOB_DES_NAME];
					if(!$JOB_DES_NAME) $JOB_DES_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $JOB_DES_NAME;
					break;
				case "COMPETENCY" :
					$JOB_DES_ID = $data[COMPETENCY_ID];
					$JOB_DES_NAME = $data[COMPETENCY_NAME];
//					$cmd = " select JOB_DES_NAME from COMPETENCY_INFO where JOB_DES_ID=$JOB_DES_ID ";
//					$db_dpis2->send_cmd($cmd);
//					$data_dpis2 = $db_dpis2->get_array();
//					$JOB_DES_NAME = $data_dpis2[JOB_DES_NAME];
					if(!$JOB_DES_NAME) $JOB_DES_NAME = "-";
					$arr_content[$data_count][$ORDER_NAME] = $JOB_DES_NAME;
					break;
			} // switch case
		} // loop foreach
		
		$arr_content[$data_count][POSITION_COUNT] = $data[POSITION_COUNT];
		$total_position += $data[POSITION_COUNT];
		
		$data_count++;
	} // loop while
		
	$count_data = count($arr_content);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$page_count = 0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			if(($data_count % 65530) == 0){
				$page_count++;

				$worksheet = &$workbook->addworksheet("$report_code".(($page_count > 1)?" ($page_count)":""));
				$worksheet->set_margin_right(0.50);
				$worksheet->set_margin_bottom(1.10);
			
				$total_order = count($arr_order_name);
				for($i=0; $i<$total_order; $i++){
					$worksheet->set_column($i, $i, $arr_order_width[$arr_order_name[$i]]);
				} // loop for
				$worksheet->set_column($i, $i, 25);

				$xlsRow = -1;
				print_header();				
			} // end if
			
			$xlsRow++;
			for($i=0; $i<$total_order; $i++){
				$worksheet->write($xlsRow, $i, $arr_content[$data_count][$arr_order_name[$i]], set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
			} // loop for
			$worksheet->write($xlsRow, $total_order, number_format($arr_content[$data_count][POSITION_COUNT]), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
		} // loop for

		$xlsRow++;
		for($i=0; $i<$total_order; $i++){
			$worksheet->write($xlsRow, $i, "", set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=L&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
		} // loop for
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, ($total_order - 1));
		$worksheet->write($xlsRow, $total_order, number_format($total_position), set_format_new("xlsFmtTableDetail", "fontFormat=&alignment=R&border=TLRB&isMerge=0&fgColor=black&bgColor=white&setRotation=0&valignment=T&fontSize=&wrapText=1"));
	}else{
		$xlsRow = 0;
		$worksheet->write($xlsRow, 0, "***** ไม่มีข้อมูล *****", set_format_new("xlsFmtTitle", "fontFormat=B&alignment=C&border=&isMerge=0&fgColor=&bgColor=&setRotation=0&valignment=B&fontSize=&wrapText=1"));
		$worksheet->merge_cells($xlsRow, 0, $xlsRow, $total_order);
	} // end if

	$workbook->close();

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