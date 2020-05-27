<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
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
						$order_by .= "b.DEPARTMENT_ID";
		
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
				elseif($DPISDB=="oci8") $order_by .= "$position_no_name, TO_NUMBER($position_no)";
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
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (b.POS_ID >= 0 or e.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if($list_person_type == "SELECT"){
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}elseif($list_person_type == "CONDITION"){
		if(trim($search_pos_no)){ 
			$arr_search_condition[] = "($position_no like '$search_pos_no%')";
		} // end if
		if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '$search_name%')";
		if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '$search_surname%')";
		if(trim($search_min_level)) $arr_search_condition[] = "(a.LEVEL_NO >= '$search_min_level')";
		if(trim($search_max_level)) $arr_search_condition[] = "(a.LEVEL_NO <= '$search_max_level')";
	} // end if

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

	$list_type_text = $ALL_REPORT_TITLE;

//	include ("../report/rpt_condition3.php");
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

		$list_type_text = "ส่วนกลาง";
		$arr_search_condition[] = "(trim(c.OT_CODE)='01')";
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

		$list_type_text = "ส่วนกลางในภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='02')";
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

		$list_type_text = "ส่วนภูมิภาค";
		$arr_search_condition[] = "(trim(c.OT_CODE)='03')";
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
		$arr_search_condition[] = "(trim(c.OT_CODE)='04')";
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
			if($select_org_structure==1){		//มอบหมายงาน
				//หา ORG ที่อยู่ภายใต้ DEPARTMENT_ID นี้
				$cmd = "select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];
				$arr_search_condition[] = "(a.ORG_ID in (". implode($arr_org_ref, ",") ."))";
			}else{	//ตามกฎหมาย
				$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
			}
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(b.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	include ("rpt_R007003_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R007003_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการลารายบุคคล" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0703";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2;
		global $arr_content, $data_count;
		global $search_date_min, $search_date_max, $select_org_structure, $DATE_DISPLAY, $BKK_FLAG;
		
		$search_condition = "";
		unset($arr_search_condition);
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
			${"TOTAL_".$AB_CODE} += $ABS_DAY;

			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
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
			for($i=1; $i<=15; $i++) $arr_content[$data_count][("DAY_".str_pad($i, 2, "0", STR_PAD_LEFT))] = ${"TOTAL_".str_pad($i, 2, "0", STR_PAD_LEFT)};	//ลาผักผ่อนนับวันหยุด (11)
		} // end if
	} // function
	
	if($DPISDB=="odbc"){
		$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
						 from			(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
						 $search_condition
						 order by		$order_by ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no_name as POS_NO_NAME, $position_no as POS_NO
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d
						 where		a.PER_STATUS=1 and $position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+)
											$search_condition
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
						 $search_condition
						 order by		$order_by ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo $DEPARTMENT_ID."===".$select_org_structure.":::".$list_type.":::".$cmd;
	//$db_dpis->show_error();
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
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";
		
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$ORG_NAME = $arr_content[$data_count][org_name];
			$POS_NO = $arr_content[$data_count][pos_no];
			$ORDER = $arr_content[$data_count][ORDER];
			$STARTDATE = $arr_content[$data_count][STARTDATE];
			$ENDDATE = $arr_content[$data_count][ENDDATE];
			for($i=1; $i<=15; $i++) ${"DAY_".str_pad($i, 2, "0", STR_PAD_LEFT)} = $arr_content[$data_count]["DAY_".str_pad($i, 2, "0", STR_PAD_LEFT)];

			if($REPORT_ORDER == "PERSON"){
				if($arr_content[($data_count + 1)][type] != "PERSON" && ($data_count < (count($arr_content) - 1))){ 
					if ($data_count >  0) $RTF->new_page();	
//					$pdf->print_tab_header();
					$arr_data = (array) null;
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					$RTF->print_tab_header();
				}else{			//กรณีคนที่ไม่มีวันลา แต่เลือกเงื่อนไขให้แสดงจาก เลือกรายบุคคลมากกว่า 1 คน
//					$pdf->SetFont($fontb,'',16);
//					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//					$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
					if ($data_count >  0) $RTF->new_page();	
//					$pdf->print_tab_header();

					$arr_data = (array) null;
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**1**>ชื่อ $NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**2**>สังกัด $ORG_NAME";
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);
					$arr_data[] = "<**3**>เลขที่ตำแหน่ง ".(($NUMBER_DISPLAY==2)?convert2thaidigit($POS_NO):$POS_NO);

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					$RTF->print_tab_header();

					$arr_data = (array) null;
					$arr_data[] = "<**1**>********** ไม่มีข้อมูลการลา **********";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**> .";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";
					$arr_data[] = "<**1**>. ";

					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
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

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>