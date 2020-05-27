<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
//	echo "nlevel=$nlevel, search_PM_CODE=$search_PM_CODE, DEPARTMENT_ID=$DEPARTMENT_ID, MINISTRY_ID=$MINISTRY_ID, PROVINCE_ID=$PROVINCE_ID<br>";
	
	if ($ISCS_FLAG==1) 
		$ARR_LEVEL_GROUP = array("M", "D", "K");
	else
		$ARR_LEVEL_GROUP = array("M", "D", "K", "O");
	$ARR_LEVEL_GROUP_NAME["M"] = "บริหาร";
	$ARR_LEVEL_GROUP_NAME["D"] = "อำนวยการ";
	$ARR_LEVEL_GROUP_NAME["K"] = "วิชาการ";
	if ($ISCS_FLAG!=1) 
		$ARR_LEVEL_GROUP_NAME["O"] = "ทั่วไป";

	$ARR_LEVEL["M"] = array("M2", "M1");
	$ARR_LEVEL["D"] = array("D2", "D1");
	if ($ISCS_FLAG==1) 
		$ARR_LEVEL["K"] = array("K5", "K4");
	else
		$ARR_LEVEL["K"] = array("K5", "K4", "K3", "K2", "K1");
	if ($ISCS_FLAG!=1) 
		if ($NOT_LEVEL_NO_O4=="Y") 
			$ARR_LEVEL["O"] = array("O3", "O2", "O1");
		else
			$ARR_LEVEL["O"] = array("O4", "O3", "O2", "O1");
	$ARR_LEVEL_NAME = (array) null;
	$ARR_LEVEL_NAME["M2"] = "บริหารระดับสูง";
	$ARR_LEVEL_NAME["M1"] = "บริหารระดับต้น";
	$ARR_LEVEL_NAME["D2"] = "อำนวยการระดับสูง";
	$ARR_LEVEL_NAME["D1"] = "อำนวยการระดับต้น";
	$ARR_LEVEL_NAME["K5"] = "วิชาการทรงคุณวุฒิ";
	$ARR_LEVEL_NAME["K4"] = "วิชาการเชี่ยวชาญ";
	$ARR_LEVEL_NAME["K3"] = "วิชาการชำนาญการพิเศษ";
	$ARR_LEVEL_NAME["K2"] = "วิชาการชำนาญการ";
	$ARR_LEVEL_NAME["K1"] = "วิชาการปฏิบัติการ";
	$ARR_LEVEL_NAME["O4"] = "ทั่วไปทักษะพิเศษ";
	$ARR_LEVEL_NAME["O3"] = "ทั่วไปอาวุโส";
	$ARR_LEVEL_NAME["O2"] = "ทั่วไปชำนาญงาน";
	$ARR_LEVEL_NAME["O1"] = "ทั่วไปปฏิบัติงาน";

	$levelGrp = $nlevel;
	$arr_levelNo = $ARR_LEVEL[$levelGrp];
	$PM_CODE = $search_PM_CODE = trim($search_PM_CODE);

	if(!trim($order_by)){ 
		if(!$MINISTRY_ID){ $order_by = "c.ORG_ID_REF";
		}elseif(!$DEPARTMENT_ID){ 
			$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $order_by = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if
	if(!trim($select_list)){ 
		if(!$MINISTRY_ID){ $select_list = "c.ORG_ID_REF as MINISTRY_ID";
		}elseif(!$DEPARTMENT_ID){ 
			$select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.DEPARTMENT_ID";
		}else{ 
			if($select_org_structure==0) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, a.ORG_ID";
			else if($select_org_structure==1) $select_list = "b.ORG_SEQ_NO, b.ORG_CODE, d.ORG_ID";
		}
	} // end if

	$search_condition = "";

	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
	}elseif($MINISTRY_ID){
		$arr_search_condition[] = "(c.ORG_ID_REF = $MINISTRY_ID)";
		$list_type_text .= " - $MINISTRY_NAME";
	}elseif($PROVINCE_CODE){
		$PROVINCE_CODE = trim($PROVINCE_CODE);
		$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		$list_type_text .= " - $PROVINCE_NAME";
	}
	if($search_PM_CODE){	// จริง ๆ เป็น PL_CODE ไม่ใช่ PM
		$search_PM_CODE = trim($search_PM_CODE);
		$arr_search_condition[] = "(trim(a.PL_CODE) = '$search_PM_CODE')";
		$list_type_text .= " - $search_PM_NAME";
	}
	if ($levelGrp && $nlevel != "ALL") {
		foreach($arr_levelNo as $key => $value) {
			$arr_levelNo[$key] = "'".$value."'";
//			echo "$key==>".$arr_levelNo[$key]."<br>";
		}
		$arr_search_condition[] = "a.LEVEL_NO in (".implode(",",$arr_levelNo).")";
	}
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	
	include ("rpt_R0100351_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R0100351_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$report_title = "รายชื่อข้าราชการ $DEPARTMENT_NAME";
    if($export_type=="report")$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = ($NUMBER_DISPLAY==2)?convert2thaidigit("R10351"):"R10351";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

	if(trim($addition_condition)) $search_condition .= (trim($search_condition)?" and ":" where ") . $addition_condition;		
//	$gender_condition = "d.PER_GENDER='$gender'";
	$gender_condition = "";

	$search_condition = str_replace(" where ", " and ", $search_condition);

	if($DPISDB=="odbc"){
		$cmd = " select			d.PER_ID, d.PER_NAME, d.PER_SURNAME, d.PN_CODE, d.PER_GENDER, b.CT_CODE, a.PL_CODE, a.LEVEL_NO
						from				(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
						 where			$search_condition and POS_STATUS=1
						 order by			a.PL_CODE, a.LEVEL_NO, b.CT_CODE, d.PER_GENDER, d.PER_ID ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			d.PER_ID, d.PER_NAME, d.PER_SURNAME, d.PN_CODE, d.PER_GENDER, b.CT_CODE, a.PL_CODE, a.LEVEL_NO
							 from			PER_POSITION a, PER_ORG b, PER_ORG c, PER_PERSONAL d
						 where			a.ORG_ID=b.ORG_ID(+) and a.DEPARTMENT_ID=c.ORG_ID(+) and a.POS_ID=d.POS_ID(+) and d.PER_TYPE=1 
						 						and d.PER_STATUS=1 and POS_STATUS=1
												$search_condition
							 order by		a.PL_CODE, a.LEVEL_NO, b.CT_CODE, d.PER_GENDER, d.PER_ID ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			d.PER_ID, d.PER_NAME, d.PER_SURNAME, d.PN_CODE, d.PER_GENDER, b.CT_CODE, a.PL_CODE, a.LEVEL_NO
						from				(
													(
														PER_POSITION a
														left join PER_ORG b on (a.ORG_ID=b.ORG_ID)
													) left join PER_ORG c on (a.DEPARTMENT_ID=c.ORG_ID)
												) left join PER_PERSONAL d on (a.POS_ID=d.POS_ID and d.PER_TYPE=1 and d.PER_STATUS=1)
						 where			$search_condition and POS_STATUS=1
						 order by			a.PL_CODE, a.LEVEL_NO, b.CT_CODE, d.PER_GENDER, d.PER_ID ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("a.ORG_ID", "d.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_all = $db_dpis->send_cmd($cmd);
//	echo "$cmd ($count_all)<br>";

	$arr_content = (array) null;

	$arr_PL_NAME = (array) null;
	$arr_rpt_order = (array) null;
	$arr_rpt_order[] = "LINE";
//	echo "arr_rpt_order=".implode(",",$arr_rpt_order)."<br>";
	$data_count = 0;
	while($data = $db_dpis->get_array()) {
//		$data = array_change_key_case($data, CASE_LOWER);

		for($rpt_order_index=0; $rpt_order_index < count($arr_rpt_order); $rpt_order_index++){
			$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
			switch($REPORT_ORDER){
				case "LINE" :

						$cmd = " select PN_NAME where trim(PN_CODE)='".$data[PN_CODE]."' ";
						$db_dpis2->send_cmd($cmd);
// 						$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$PN_NAME = trim($data2[PN_NAME]);
						
						$fullname = trim(($PN_NAME?$PN_NAME:"").($data["PER_NAME"]?" ".$data["PER_NAME"]:"").($data["PER_SURNAME"]?" ".$data["PER_SURNAME"]:""));

						if ($fullname) {
							$per_id = $data["PER_ID"];
							$pl = trim($data["PL_CODE"]);
							$lvl = $data["LEVEL_NO"];
							if ($data["CT_CODE"]==140)		// ในประเทศ
								$ct = "thai";
							else
								$ct = "nothai";
							$gender = $data["PER_GENDER"];
							$arr_content[$pl][$lvl][$ct][$gender][name][$per_id] = $fullname;
							$arr_content[$pl][$lvl][$ct][$gender][cnt]++;
							$arr_content[$pl][$lvl][$ct][cnt]++;
							$arr_content[$pl][$lvl][cnt]++;
							$arr_content[$pl][cnt]++;
						}
						
						if($rpt_order_index < (count($arr_rpt_order) - 1)) initialize_parameter($rpt_order_index + 1);
						$data_count++;
//					} // end if
					break;
		
			} // end switch case
		} // end for
	} // end while

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
	
	$arr_PL_NAME = (array) null;
	foreach($arr_content as $key_pl => $arr_pl) {
		$cmd = " select PL_NAME, PL_SHORTNAME from PER_LINE where trim(PL_CODE)='$key_pl' ";
		$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$PL_NAME = trim($data2[PL_SHORTNAME])?$data2[PL_SHORTNAME]:$data2[PL_NAME];
		$arr_PL_NAME[$key_pl] = $PL_NAME;
	} // end if
//	echo "[$PM_CODE]=".$arr_PL_NAME[$PM_CODE]."<br>";

//	$f_first = 1;
	foreach($arr_content as $key_pl => $arr_pl) {
		if ($arr_content[$key_pl][cnt] > 0) {
//			echo  "***** key_pl=".$arr_PL_NAME[$key_pl]."[$key_pl] , count=".$arr_content[$key_pl][cnt]."<br>";
			$report_title = "รายชื่อข้าราชการ ".$arr_PL_NAME[$key_pl]."  ".$ARR_LEVEL_GROUP_NAME[$nlevel]."";
			if (!$f_first) { 
				$RTF->paragraph(); 
				$RTF->new_page(); 
			} else $f_first=0;
		}
		foreach($arr_pl as $key_lvl => $arr_lvl) {
			if ($arr_content[$key_pl][$key_lvl][cnt] > 0) {
//				echo  $ARR_LEVEL_NAME[$key_lvl]."  มี ".$arr_content[$key_pl][$key_lvl][cnt]." คน<br>";
				$arr_border = array("","TLBR","","","","","","");
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = $ARR_LEVEL_NAME[$key_lvl]."  มี ".$arr_content[$key_pl][$key_lvl][cnt]." คน";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			}
		}
		foreach($arr_pl as $key_lvl => $arr_lvl) {
			if ($arr_content[$key_pl][$key_lvl][cnt] > 0) {
//				echo  "key_lvl=".$ARR_LEVEL_NAME[$key_lvl]."[$key_lvl] , count=".$arr_content[$key_pl][$key_lvl][cnt]."<br>";
				$arr_data = (array) null;
				$arr_data[] = "";
				$arr_data[] = $ARR_LEVEL_NAME[$key_lvl];
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				
//				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
//				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

			}
			foreach($arr_lvl as $key_ct => $arr_ct) {
				if ($arr_content[$key_pl][$key_lvl][$key_ct][cnt] > 0) {
//					echo  "key_ct=$key_ct , count=".$arr_content[$key_pl][$key_lvl][$key_ct][cnt]."<br>";
					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = ($key_ct==1?"ในประเทศ":"ต่างประเทศ");
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = "";
					
//					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
//					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				}
				$arr_per_m = (array) null;
				$arr_per_w = (array) null;
				foreach($arr_ct as $key_gd => $arr_gd) {
//					if ($arr_content[$key_pl][$key_lvl][$key_ct][$key_gd][cnt] > 0) 
//					echo  "key_gender=$key_gd , count=".$arr_content[$key_pl][$key_lvl][$key_ct][$key_gd][cnt]."<br>";
					foreach($arr_gd[name] as $key_per => $pname) {
						if ($key_gd==1) {
							$arr_per_m[] = $arr_content[$key_pl][$key_lvl][$key_ct][$key_gd][name][$key_per];
						} else {
							$arr_per_w[] = $arr_content[$key_pl][$key_lvl][$key_ct][$key_gd][name][$key_per];
						}
//						echo "pname=".$arr_content[$key_pl][$key_lvl][$key_ct][$key_gd][name][$key_per]."<br>";
					}
				}
				$cnt_max = (count($arr_per_m) > count($arr_per_w) ? count($arr_per_m) : count($arr_per_w));
				for($ii = 0; $ii < $cnt_max; $ii++) {
//					echo "arr_per_m=".$arr_per_m[$ii]." , arr_per_w=".$arr_per_w[$ii]."<br>";
					$arr_data = (array) null;
					$arr_data[] = ($arr_per_m[$ii]?$ordno:"");
					$arr_data[] = ($arr_per_m[$ii]?$arr_per_m[$ii]:"");
					$arr_data[] = "";
					$arr_data[] = "";
					$arr_data[] = ($arr_per_w[$ii]?$ordno:"");
					$arr_data[] = ($arr_per_w[$ii]?$arr_per_w[$ii]:"");
					$arr_data[] = "";
					$arr_data[] = "";
					
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

				}
			}
		}
	}
	
	if (!count($arr_content)) {
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//		$RTF->close_section(); 

	$RTF->display($fname);

?>