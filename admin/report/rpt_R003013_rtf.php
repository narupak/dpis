<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
//	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "e.PL_CODE=f.PL_CODE";
		$e_code = "e.PL_CODE";
		$f_code = "f.PL_CODE";
		$f_name = "f.PL_NAME";
		$line_search_code=trim($search_pl_code);
		$line_search_name=trim($search_pl_name);
		$line_title=" สายงาน";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "e.PN_CODE=f.PN_CODE";
		$e_code = "e.PN_CODE";
		$f_code = "f.PN_CODE";
		$f_name = "f.PN_NAME";
		$line_search_code=trim($search_pn_code);
		$line_search_name =trim($search_pn_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "e.EP_CODE=f.EP_CODE";
		$e_code = "e.EP_CODE";
		$f_code = "f.EP_CODE";
		$f_name = "f.EP_NAME";
		$line_search_code=trim($search_ep_code);
		$line_search_name=trim($search_ep_name);
		$line_title=" ชื่อตำแหน่ง";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "e.TP_CODE=f.TP_CODE";
		$e_code = "e.TP_CODE";
		$f_code = "f.TP_CODE";
		$f_name = "f.TP_NAME";
		$line_search_code =trim($search_tp_code);
		$line_search_name =trim($search_tp_name);
		$line_title=" ชื่อตำแหน่ง";
	} // end if

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
//	$arr_rpt_order = array("ORG", "ORG_1", "ORG_2", "LINE"); 
	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(g.MOV_SUB_TYPE = 2)";

	if($DPISDB=="odbc") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="oci8") $arr_search_condition[] = "(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '".($search_budget_year - 543)."-10-01')";
	elseif($DPISDB=="mysql") $arr_search_condition[] = "(LEFT(trim(e.POH_EFFECTIVEDATE), 10) >= '".(($search_budget_year - 543) - 1)."-10-01' and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '".($search_budget_year - 543)."-10-01')";
	
	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG_TYPE_1", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลาง
		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(c.OT_CODE='01')";
	}
	if(in_array("PER_ORG_TYPE_2", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนกลางในภูมิภาค
		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='02')";
	}
	if(in_array("PER_ORG_TYPE_3", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ส่วนภูมิภาค
		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(c.OT_CODE='03')";
	}
	if(in_array("PER_ORG_TYPE_4", $list_type)){
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		} // end if
		
		// ต่างประเทศ
		$list_type_text = "ต่างประเทศ";
		$arr_search_condition[] = "(c.OT_CODE='04')";
	}
	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
					
			if(trim($search_org_id_1)){ 
				$arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
				$list_type_text .= " - $search_org_name_1";
			} // end if
			if(trim($search_org_id_2)){ 
				 $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
				$list_type_text .= " - $search_org_name_2";
			} // end if
		}elseif($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				$list_type_text .= "$search_org_ass_name";
			} // end if
			
			if(trim($search_org_ass_id_1)){ 
				$arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID_2  = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}
	if(in_array("PER_LINE", $list_type)){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($e_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
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
	
	include ("rpt_R003013_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R003013_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$show_budget_year = (($NUMBER_DISPLAY==2)?convert2thaidigit($search_budget_year):$search_budget_year);
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่ย้ายระหว่างกอง/สายงาน ในปีงบประมาณ $show_budget_year";
	$report_code = "R0313";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	if($DPISDB=="odbc"){
		$cmd = " select			distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, e.POH_POS_NO_NAME, e.POH_POS_NO, $f_name as PL_NAME, e.LEVEL_NO,
											LEFT(trim(e.POH_EFFECTIVEDATE), 10) as POH_EFFECTIVEDATE, e.POH_ORG2, e.POH_ORG3
						 from		(
											(
												(
													(
														(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on $line_join
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition
						group by 	a.PER_ID, e.POH_POS_NO_NAME, e.POH_POS_NO, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name , e.LEVEL_NO, 
											SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10), e.POH_ORG2, e.POH_ORG3
						 order by		a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) , 
						 					a.PER_ID, d.PN_NAME , e.POH_POS_NO , PL_NAME , e.LEVEL_NO";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select		distinct a.PER_ID, e.POH_POS_NO_NAME, e.POH_POS_NO, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name as PL_NAME, e.LEVEL_NO, 
											SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) as POH_EFFECTIVEDATE, e.POH_ORG2, e.POH_ORG3
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, $line_table f, PER_MOVMENT g
						 where		$position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+)
											and a.PER_ID=e.PER_ID(+) and $line_join(+) and e.MOV_CODE=g.MOV_CODE(+) 
											$search_condition
						group by 	a.PER_ID, e.POH_POS_NO_NAME, e.POH_POS_NO, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name , e.LEVEL_NO, 
											SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10), e.POH_ORG2, e.POH_ORG3
						 order by		a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) , 
						 					a.PER_ID, d.PN_NAME , e.POH_POS_NO , PL_NAME , e.LEVEL_NO ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			distinct a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, e.POH_POS_NO_NAME, e.POH_POS_NO, $f_name as PL_NAME, e.LEVEL_NO,
											LEFT(trim(e.POH_EFFECTIVEDATE), 10) as POH_EFFECTIVEDATE, e.POH_ORG2, e.POH_ORG3
						 from		(
											(
												(
													(
														(
																PER_PERSONAL a 
																left join $position_table b on $position_join
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on $line_join
											) left join PER_MOVMENT g on (e.MOV_CODE=g.MOV_CODE)
											$search_condition
						group by 	a.PER_ID, e.POH_POS_NO_NAME, e.POH_POS_NO, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $f_name , e.LEVEL_NO, 
											SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10), e.POH_ORG2, e.POH_ORG3
						 order by		a.PER_NAME, a.PER_SURNAME, SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) , 
						 					a.PER_ID, d.PN_NAME , e.POH_POS_NO , PL_NAME , e.LEVEL_NO ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	//echo "=> $cmd<br>";
	$data_count = 0;
	$person_count = 0;
	while($data = $db_dpis->get_array()){
		$person_count++;
		
		$PER_ID = $data[PER_ID];
		$PN_NAME = trim($data[PN_NAME]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);
		$POH_POS_NO_NAME = trim($data[POH_POS_NO_NAME]);
		if (substr($POH_POS_NO_NAME,0,4)=="กปด.")
			$POS_NO = $POH_POS_NO_NAME." ".trim($data[POH_POS_NO]);
		else
			$POS_NO = $POH_POS_NO_NAME.trim($data[POH_POS_NO]);
		$PL_NAME = trim($data[PL_NAME]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = trim($data[PT_NAME]);
				
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data2=$db_dpis2->get_array();
		$LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
		$PL_NAME = trim($PL_NAME) . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?" $PT_NAME":"");
		if ($BKK_FLAG==1)
			$POH_ORG = trim($data[POH_ORG3])." ".trim($data[POH_ORG2]);
		else
			$POH_ORG = trim($data[POH_ORG3]);
		
		$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE],$DATE_DISPLAY);
		
		if($DPISDB=="odbc"){
			$cmd = " select		e.POH_POS_NO_NAME, e.POH_POS_NO, $f_name as PL_NAME, e.LEVEL_NO, b.ORG_NAME
							 from		(
												PER_POSITIONHIS e
												left join PER_ORG b on (e.ORG_ID_3=b.ORG_ID)
											) left join $line_table f on $line_join
							 where	e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$data[POH_EFFECTIVEDATE]'
							 order by LEFT(trim(e.POH_EFFECTIVEDATE), 10) desc ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select		e.POH_POS_NO_NAME, e.POH_POS_NO, $f_name as PL_NAME, e.LEVEL_NO, b.ORG_NAME
							 from		PER_POSITIONHIS e, PER_ORG b, $line_table f
							 where	e.ORG_ID_3=b.ORG_ID(+) and $line_join(+)
											and e.PER_ID=$PER_ID and SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) < '$data[POH_EFFECTIVEDATE]'
							 order by SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10) desc ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select		e.POH_POS_NO_NAME, e.POH_POS_NO, $f_name as PL_NAME, e.LEVEL_NO, b.ORG_NAME
							 from		(
												PER_POSITIONHIS e
												left join PER_ORG b on (e.ORG_ID_3=b.ORG_ID)
											) left join $line_table f on $line_join
							 where	e.PER_ID=$PER_ID and LEFT(trim(e.POH_EFFECTIVEDATE), 10) < '$data[POH_EFFECTIVEDATE]'
							 order by LEFT(trim(e.POH_EFFECTIVEDATE), 10) desc ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OLD_POS_NO = trim($data2[POH_POS_NO_NAME]).trim($data2[POH_POS_NO]);
		$OLD_PL_NAME = trim($data2[PL_NAME]);
		$OLD_LEVEL_NO = trim($data2[LEVEL_NO]);
		$OLD_PT_CODE = trim($data[PT_CODE]);
		$OLD_PT_NAME = trim($data[PT_NAME]);
		$OLD_ORG_NAME = trim($data2[ORG_NAME]);
		
		$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$OLD_LEVEL_NO'";
		$db_dpis2->send_cmd($cmd);
		$data2=$db_dpis2->get_array();
		$OLD_LEVEL_NAME = trim($data2[LEVEL_NAME]);
		$OLD_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if (!$OLD_POSITION_LEVEL) $OLD_POSITION_LEVEL = $OLD_LEVEL_NAME;
		
		$OLD_PL_NAME = trim($OLD_PL_NAME) . $OLD_POSITION_LEVEL . (($OLD_PT_NAME != "ทั่วไป" && $OLD_LEVEL_NO >= 6)?" $OLD_PT_NAME":"");
			
		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 5)) . $person_count .". ". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][old_posno] = $OLD_POS_NO;
		$arr_content[$data_count][old_position] = $OLD_PL_NAME ."/". $OLD_ORG_NAME;
		$arr_content[$data_count][new_posno] = $POS_NO;
		$arr_content[$data_count][new_position] = $PL_NAME ."/". $POH_ORG;
		$arr_content[$data_count][movedate] = $POH_EFFECTIVEDATE;
		
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
//new format*****************************************************************
//	echo "count_data=$count_data<br>";
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
			
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";
			
	if($count_data){
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][old_posno];
			$NAME_3 = $arr_content[$data_count][old_position];
			$NAME_4 = $arr_content[$data_count][new_posno];
			$NAME_5 = $arr_content[$data_count][new_position];
			$NAME_6 = $arr_content[$data_count][movedate];

			$arr_data = (array) null;
			$arr_data[] = $NAME_1;
			$arr_data[] = $NAME_2;
			$arr_data[] = $NAME_3;
			$arr_data[] = $NAME_4;
			$arr_data[] = $NAME_5;
			$arr_data[] = $NAME_6;

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
	}else{

		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>