<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  {
		$f_all = true; 
		$RPTORD_LIST = "COUNTRY|$RPTORD_LIST";
	} else $f_all = false;	

	if(!trim($RPTORD_LIST)){ 		
		$RPTORD_LIST = "ORG|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_1|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST .= "ORG_2|";
		if(in_array("PER_ORG", $list_type) && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST .= "LINE|";
		if(in_array("PER_COUNTRY", $list_type) && trim($search_pv_code)!="") $RPTORD_LIST .= "PROVINCE|";
	} // end if
	$arr_rpt_order_setnew = explode("|", $RPTORD_LIST);
	$arr_rpt_order_tmp_setnew = array_unique($arr_rpt_order_setnew);	//ตัดค่าที่ซ้ำกันออก เพื่อไม่ให้ขึ้นข้อมูลซ้ำกัน 2 แถว	และเรียงตำแหน่ง index ใหม่ 0 1 2 ...
	foreach($arr_rpt_order_tmp_setnew as $key=>$value){
		$arr_rpt_order[]=$value;
	}
	unset($arr_rpt_order_setnew);	unset($arr_rpt_order_tmp_setnew);
	

	$select_list = "";
	$order_by = "";
	$heading_name = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "MINISTRY" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_ID_REF as MINISTRY_ID";	
				
				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_ID_REF"; 
				
				$heading_name .= " $MINISTRY_TITLE";
				break;
			case "DEPARTMENT" :
				if($select_list) $select_list .= ", ";
				$select_list .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				if($order_by) $order_by .= ", ";
				$order_by .= "e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID";

				$heading_name .= " $DEPARTMENT_TITLE";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)){
		if($select_org_structure==0){ $order_by = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; 	}
		else if($select_org_structure==1){	$order_by = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; }
	}
	if(!trim($select_list)){ 
		if($select_org_structure==0){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2"; }
		else if($select_org_structure==1){ $select_list = "c.ORG_SEQ_NO, c.ORG_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2"; } 
	}

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE in (". implode(", ", $search_per_type) .") and (a.POS_ID >= 0 or a.POEM_ID >= 0))";
	$arr_search_condition[] = "(a.PER_TYPE = 1)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";
//	$arr_search_condition[] = "(g.PT_GROUP = '3')";
	$arr_search_condition[] = "(a.LEVEL_NO in ('M1', 'M2','D2'))";

	if(in_array("ALL", $list_type) && !$DEPARTMENT_ID)  $f_all = true; else $f_all = false;
	
	$list_type_text = $ALL_REPORT_TITLE;

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
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	if ($BKK_FLAG==1)
		$report_title = "รายชื่อนักบริหารจำแนกตามหน่วยงาน";
	else
		$report_title = "รายชื่อนักบริหารจำแนกตามส่วนราชการ";
	$report_code = "H05";
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;

	include ("rpt_R010005_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน

	if ($f_all) {
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$group_str = "a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
						b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
						SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10),
						a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_SEQ_NO";
			$order_str = "d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
							a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
						SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)";					
		}elseif($DPISDB=="mysql"){
		}
	} else {
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$group_str = "a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
						b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), 
						SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_RETIREDATE), 1, 10),
						a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, d.ORG_NAME, e.ORG_NAME, m.LEVEL_SEQ_NO";
			$order_str = "h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
							a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
						SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)";					
		}elseif($DPISDB=="mysql"){
		}
	}

	if($DPISDB=="odbc"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME as MINISTRY_NAME, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME
				   from			(
										(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
													) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
												) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
										) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
									) left join PER_LEVEL m on(a.LEVEL_NO=m.LEVEL_NO)
				   where						$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_SEQ_NO
				   order by d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
				   				LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) "; 
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, 
								SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME as MINISTRY_NAME, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME
				   from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG d, PER_ORG e, PER_LINE f, PER_MGT h, 
				   				PER_PRENAME i, PER_LEVEL m
				   where		a.POS_ID=b.POS_ID and b.ORG_ID=c.ORG_ID and a.DEPARTMENT_ID=d.ORG_ID and d.ORG_ID_REF=e.ORG_ID 
								and b.PL_CODE=f.PL_CODE and b.PM_CODE=h.PM_CODE(+) and a.PN_CODE=i.PN_CODE(+) and a.LEVEL_NO=m.LEVEL_NO(+)
								$search_condition
				   group by	$group_str
				   order by		$order_str ";					
	}elseif($DPISDB=="mysql"){
		$cmd = " select			a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, 
								LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 10) as PER_RETIREDATE,
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID as DEPARTMENT_ID, d.ORG_NAME as DEPARTMENT_NAME, 
								d.ORG_ID_REF as MINISTRY_ID, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME as MINISTRY_NAME, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME
				   from			(
										(
											(
												(
													(
														(
															(
																PER_PERSONAL a
																inner join PER_POSITION b on (a.POS_ID=b.POS_ID)
															) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) inner join PER_ORG d on (a.DEPARTMENT_ID=d.ORG_ID)
													) inner join PER_ORG e on (d.ORG_ID_REF=e.ORG_ID)
												) inner join PER_LINE f on (b.PL_CODE=f.PL_CODE)
											) left join PER_MGT h on (b.PM_CODE=h.PM_CODE)
										) left join PER_PRENAME i on (a.PN_CODE=i.PN_CODE)
									)left join PER_LEVEL m on (a.LEVEL_NO=m.LEVEL_NO)
				   where		  				$search_condition
				   group by		a.PER_ID, i.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, m.POSITION_LEVEL, m.POSITION_TYPE, m.LEVEL_SHORTNAME, b.POS_NO, 
								b.PL_CODE, f.PL_NAME, b.PM_CODE, h.PM_SEQ_NO, h.PM_NAME, LEFT(trim(a.PER_BIRTHDATE), 10), 
								LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_RETIREDATE), 10),
								a.PER_SALARY, b.ORG_ID, b.ORG_ID_1, c.ORG_NAME, a.DEPARTMENT_ID, d.ORG_NAME, d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, e.ORG_NAME, m.LEVEL_SEQ_NO
				   order by	d.ORG_ID_REF, d.ORG_SEQ_NO, d.ORG_CODE, a.DEPARTMENT_ID, h.PM_SEQ_NO, m.LEVEL_SEQ_NO desc, a.LEVEL_NO desc, 
									a.PER_NAME, a.PER_SURNAME, a.PER_SALARY desc, 
				   					LEFT(trim(a.PER_STARTDATE), 10), LEFT(trim(a.PER_BIRTHDATE), 10) "; 
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
//   $db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$MINISTRY_ID = -1;
	$DEPARTMENT_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($MINISTRY_ID != $data[MINISTRY_ID]){
			$MINISTRY_ID = $data[MINISTRY_ID];
			$MINISTRY_NAME = $data[MINISTRY_NAME];
			
			if($f_all){
			$arr_content[$data_count][type] = "MINISTRY";
			$arr_content[$data_count][name] = $MINISTRY_NAME;

			$data_row = 0;
			$data_count++;
			}
		} // end if
		
		if (!$f_all) {
			if($DEPARTMENT_ID != $data[DEPARTMENT_ID]){
				$DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
				
				$arr_content[$data_count][type] = "DEPARTMENT";
				$arr_content[$data_count][name] = $DEPARTMENT_NAME;

				$data_row = 0;
				$data_count++;
			} // end if
		} // end if

		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$POSITION_LEVEL = trim($data[POSITION_LEVEL]);
		if ($BKK_FLAG==1)
			$POSITION_TYPE = trim($data[LEVEL_SHORTNAME]);
		else
			$POSITION_TYPE = trim($data[POSITION_TYPE]);
		$PL_CODE = trim($data[PL_CODE]);
		$PL_NAME = $data[PL_NAME];
		$PM_CODE = trim($data[PM_CODE]);
		$PM_NAME = $data[PM_NAME];
		$TMP_ORG_NAME = $data[ORG_NAME];

		$ORG_ID_1 = $data[ORG_ID_1];
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_ORG_NAME_1 = $data2[ORG_NAME];

		if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด") {
			$PM_NAME .= $TMP_ORG_NAME;
			$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
		} elseif ($PM_NAME=="นายอำเภอ") {
			$PM_NAME .= $TMP_ORG_NAME_1;
			$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
		}
		
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = $data[PT_NAME];
		
		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		if($PER_BIRTHDATE){
			$arr_temp = explode("-", $PER_BIRTHDATE);
			$PER_RETIRE_YEAR = ($arr_temp[0] + 543) + (($arr_temp[1] >= 10)?61:60);
			$PER_BIRTHDATE = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
		}

		$PER_SALARY = $data[PER_SALARY];
		
		// วันเลื่อนระดับ (????)
		$cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' order by EFFECTIVEDATE desc ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$LEVEL_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);
		//	echo " $cmd [ $LEVEL_EFFECTIVEDATE ]  <<<<<>>>>> ";

		// วันเข้าเลขที่ตำแหน่งปัจจุบัน		//ok
		//___ $cmd = " select MIN(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(PL_CODE)='$PL_CODE' ";
		$cmd = " select POH_EFFECTIVEDATE as EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and trim(LEVEL_NO)='$LEVEL_NO' order by EFFECTIVEDATE desc";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POSITION_EFFECTIVEDATE = show_date_format($data2[EFFECTIVEDATE],$DATE_DISPLAY);

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] =  "$data_row." .str_repeat(" ", 3) . $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][pl_name] = $PL_NAME.$POSITION_LEVEL;
		$arr_content[$data_count][pm_name] = $PM_NAME;
		$arr_content[$data_count][pt_name] = $POSITION_TYPE;
		$arr_content[$data_count][level_effectivedate] = $LEVEL_EFFECTIVEDATE;
		$arr_content[$data_count][position_effectivedate] = $POSITION_EFFECTIVEDATE;
		$arr_content[$data_count][retireyear] = $PER_RETIRE_YEAR;
				
		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";

//new format****************************************************************
	if($count_data){
		$pdf->AutoPageBreak = false; 
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$NAME = $arr_content[$data_count][name];
			$PL_NAME = $arr_content[$data_count][pl_name];
			$PM_NAME = $arr_content[$data_count][pm_name];
			$PT_NAME = $arr_content[$data_count][pt_name];
			$LEVEL_EFFECTIVEDATE = $arr_content[$data_count][level_effectivedate];
			$POSITION_EFFECTIVEDATE = $arr_content[$data_count][position_effectivedate];
			$RETIREYEAR = $arr_content[$data_count][retireyear];

			if($REPORT_ORDER == "MINISTRY"){
				$ORG_NAME = $arr_content[$data_count][name];
            	$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
            	$arr_data[] ="";
		    	
				$data_align = array("L", "L", "L", "C", "C", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
			}elseif($REPORT_ORDER == "DEPARTMENT"){
				$ORG_NAME = $arr_content[$data_count][name];
         		$arr_data = (array) null;
				$arr_data[] ="$NAME";
				$arr_data[] =  "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] ="";
				$arr_data[] ="";
            	$arr_data[] ="";
		    	
				$data_align = array("L", "L", "L", "C", "C", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "b", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTENT"){
		       	$arr_data = (array) null;
				$arr_data[] = $NAME;
				$arr_data[] = $PL_NAME;
				$arr_data[] = $PM_NAME;
				$arr_data[] = $PT_NAME;
				$arr_data[] = $LEVEL_EFFECTIVEDATE;
				$arr_data[] = $POSITION_EFFECTIVEDATE;
            	$arr_data[] = $RETIREYEAR;
		    	
				$data_align = array("L", "L", "L", "C", "C", "C", "C");
				
				$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if	
		} // end for
		$pdf->add_data_tab("", 0, "RHBL", $data_align, "", "12", "b", "000000", "");		
	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();			

?>