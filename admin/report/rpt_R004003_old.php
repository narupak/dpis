<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type == 1){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_join = "b.PL_CODE=f.PL_CODE";
		$pl_code = "b.PL_CODE";
		$pl_name = "f.PL_NAME";
		$position_no = "b.POS_NO_NAME, b.POS_NO";
		$line_search_code = trim($search_pl_code);
		$line_search_name = trim($search_pl_name);
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_join = "b.PN_CODE=f.PN_CODE";
		$pl_code = "b.PN_CODE";
		$pl_name = "f.PN_NAME";
		$position_no = "b.POEM_NO_NAME, b.POEM_NO";
		$line_search_code = trim($search_pn_code);
		$line_search_name = trim($search_pn_name);
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_join = "b.EP_CODE=f.EP_CODE";
		$pl_code = "b.EP_CODE";
		$pl_name = "f.EP_NAME";
		$position_no = "b.POEMS_NO_NAME, b.POEMS_NO";
		$line_search_code = trim($search_ep_code);
		$line_search_name = trim($search_ep_name);
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_join = "b.TP_CODE=f.TP_CODE";
		$pl_code = "b.TP_CODE";
		$pl_name = "f.TP_NAME";
		$position_no = "b.POT_NO_NAME, b.POT_NO";
		$line_search_code = trim($search_tp_code);
		$line_search_name = trim($search_tp_name);
	} // end if
	
	if(!trim($RPTORD_LIST)){ 
		if($list_type=="PER_ORG" && trim($search_org_id_1)=="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_1";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)=="") $RPTORD_LIST = "ORG_2";
		elseif($list_type=="PER_ORG" && trim($search_org_id_1)!="" && trim($search_org_id_2)!="") $RPTORD_LIST = "LINE";
		else $RPTORD_LIST = "ORG";
	} // end if
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$search_level_no = trim($search_level_no);
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS in (". implode(", ", $search_per_status) ."))";
	$arr_search_condition[] = "(a.LEVEL_NO = '$search_level_no')";
	$arr_search_condition[] = "(i.DC_TYPE in (1,2))";

	$list_type_text = $ALL_REPORT_TITLE;

	if($list_type == "PER_ORG_TYPE_1"){
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
	}elseif($list_type == "PER_ORG_TYPE_2"){
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
	}elseif($list_type == "PER_ORG_TYPE_3"){
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
	}elseif($list_type == "PER_ORG_TYPE_4"){
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
	}elseif($list_type == "PER_ORG"){
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
				$arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_1)";
				$list_type_text .= " - $search_org_ass_name_1";
			} // end if
			if(trim($search_org_ass_id_2)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id_2)";
				$list_type_text .= " - $search_org_ass_name_2";
			} // end if
		}
	}elseif($list_type == "PER_LINE"){
		// สายงาน
		$list_type_text = "";
		if($line_search_code){
			$arr_search_condition[] = "(trim($pl_code)='$line_search_code')";
			$list_type_text .= " $line_search_name";
		}
	}elseif($list_type == "PER_COUNTRY"){
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
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(b.DEPARTMENT_ID = $DEPARTMENT_ID)";
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
	
	$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$search_level_no' ";
	$db_dpis2->send_cmd($cmd2);
	//$db_dpis2->show_error();
	$data=$db_dpis2->get_array();
	$level_name=$data[LEVEL_NAME];

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายชื่อ$PERSON_TYPE[$search_per_type]ที่ดำรงตำแหน่งในระดับ ". $level_name ." เรียงตามลำดับอาวุโส";
	$report_code = "R0403";
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

	$heading_width[0] = "40";
	$heading_width[1] = "55";
	$heading_width[2] = "13";
	$heading_width[3] = "25";
	$heading_width[4] = "23";
	$heading_width[5] = "20";
	$heading_width[6] = "18";
	$heading_width[7] = "15";
	$heading_width[8] = "18";
	$heading_width[9] = "22";
	$heading_width[10] = "22";
	$heading_width[11] = "18";
	
    $heading_text[0] = "ชื่อ - สกุล|";
	$heading_text[1] = "ตำแหน่ง/ระดับ|$ORG_TITLE";
	$heading_text[2] = "เลขที่|ตำแหน่ง";
	$heading_text[3] = "วุฒิ|";
	$heading_text[4] = "สาขา|วิชาเอก";
	$heading_text[5] = "วันเข้า|สู่ระดับ";
	$heading_text[6] = "ระดับ|";
	$heading_text[7] = "เงินเดือน|";
	$heading_text[8] = "เครื่องราชฯ|";
	$heading_text[9] = "วันบรรจุ|(อายุราชการ)";
	$heading_text[10] = "วันเดือนปีเกิด|(อายุตัว)";
	$heading_text[11] = "วันเกษียณ|";

	$heading_align = array('C','C','C','C','C','C','C','C','C','C','C','C');
	
	//---ต้องมีระดับตำแหน่งของตัวมันเองด้วย ในกรณีเข้ามาถึงก็เป็นระดับตำแหน่งนี้เลย ไม่มีระดับก่อนหน้า
	$L["O1"]=array('04','03','02','01','O1');
	$L["O2"]=array('06','05','04','O1','O2');
	$L["O3"]=array('08','07','06','O2','O3');
	$L["O4"]=array('09','08','O3','O4');
	$L["K1"]=array('05','04','03','K1');		
	$L["K2"]=array('07','06','05','K1','K2');
	$L["K3"]=array('08','07','K2','K3');
	$L["K4"]=array('09','08','K3','K4');
	$L["K5"]=array('10','09','K4','K5');
	$L["D1"]=array('08','07','D1');
	$L["D2"]=array('09','08','D1','D2');
	$L["M1"]=array('09','08','M1');
	$L["M2"]=array('11','10','09','M1','M2');
	//----วน loop ตามระดับตำแหน่ง ที่เลือกมา -------------
	$arrkeep = array();
	for($i=0; $i < count($L[$search_level_no]); $i++){	
		$index=$L[$search_level_no][$i];		//$index=level no
		if($DPISDB=="odbc"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
												MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";   
		}elseif($DPISDB=="oci8"){	
			$search_condition = str_replace(" where ", " and ", $search_condition);	
			$cmd = " select 	distinct e.PER_ID, a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
											MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, a.PER_RETIREDATE,MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, 
											MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
								from 	PER_PERSONAL a,  $position_table b, PER_ORG c, PER_PRENAME d, PER_POSITIONHIS e, $line_table f, PER_TYPE g, PER_DECORATEHIS h, PER_DECORATION i, PER_LEVEL j 
								where $position_join(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=d.PN_CODE(+) 
											and a.PER_ID=e.PER_ID(+) and $line_join(+) 
											and b.PT_CODE=g.PT_CODE(+) and a.PER_ID=h.PER_ID(+) and a.LEVEL_NO=j.LEVEL_NO(+) 
								$search_condition and e.POH_LEVEL_NO='$index' 											
								 group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
											 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, SUBSTR(trim(a.PER_STARTDATE), 1, 10), 
											SUBSTR(trim(a.PER_BIRTHDATE), 1, 10),a.PER_RETIREDATE, a.DEPARTMENT_ID
								 order by MIN(SUBSTR(trim(e.POH_EFFECTIVEDATE), 1, 10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
											SUBSTR(trim(a.PER_STARTDATE), 1, 10),SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)  "; 
								//**การเปลี่ยน group by/order by มีผลต่อการแสดงผลที่จะไม่ดึงวันที่เริ่มต้นเข้าสู่ระดับ ของระดับตำแหน่งก่อนหน้าจะเข้าสู่ระดับปัจจุบัน
		}elseif($DPISDB=="mysql"){
			$cmd = " select		a.PER_ID, b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO, j.LEVEL_NAME, j.POSITION_LEVEL, 
												MAX(e.POH_LEVEL_NO) as LEVEL_NOHIS, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE), 10) as PER_STARTDATE, 
												LEFT(trim(a.PER_BIRTHDATE), 10) as PER_BIRTHDATE, a.PER_RETIREDATE,	MAX(LEFT(trim(e.POH_EFFECTIVEDATE), 10)) as POH_EFFECTIVEDATE, 
												MAX(i.DC_ORDER) as DC_ORDER, a.DEPARTMENT_ID
					   from		(
										(
											(
												(
													(
														(
															(
																(
																PER_PERSONAL a 
																left join $position_table b on ($position_join) 
															) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
														) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
													) left join PER_POSITIONHIS e on (a.PER_ID=e.PER_ID)
												) left join $line_table f on ($line_join)
											) left join PER_TYPE g on (b.PT_CODE=g.PT_CODE)
										) left join PER_DECORATEHIS h on (a.PER_ID=h.PER_ID)
									) left join PER_DECORATION i on (h.DC_CODE=i.DC_CODE)
								) left join PER_LEVEL j on (a.LEVEL_NO=j.LEVEL_NO)
								$search_condition and e.POH_LEVEL_NO='$index' and (e.POH_LEVEL_NO < a.LEVEL_NO)
						group by e.PER_ID,e.POH_LEVEL_NO,a.PER_ID,b.ORG_ID_1,d.PN_NAME, a.PER_NAME, a.PER_SURNAME, $position_no, $pl_name, a.LEVEL_NO,
								 j.LEVEL_NAME, j.POSITION_LEVEL, b.PT_CODE, g.PT_NAME, c.ORG_NAME,a.PER_SALARY, LEFT(trim(a.PER_STARTDATE),10), 
								LEFT(trim(a.PER_BIRTHDATE),10),a.PER_RETIREDATE, a.DEPARTMENT_ID
						order by  MIN(LEFT(trim(e.POH_EFFECTIVEDATE),10)),a.PER_SALARY desc,MAX(i.DC_ORDER) desc,
								LEFT(trim(a.PER_STARTDATE),10),LEFT(trim(a.PER_BIRTHDATE),10) ";   
		}		
		
		//สร้าง query ใหม่ สำหรับข้อมูล record เดียว
		if($select_org_structure==1) { 
			$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
			//$cmd = str_replace("b.ORG_ID_1", "a.ORG_ID_1", $cmd);
			//$cmd = str_replace("b.ORG_ID_2", "a.ORG_ID_2", $cmd);
			$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
		}
		//echo "$cmd <-----> <br>===============<br>";
		//---สร้าง query ของแต่ละตัว แล้ววนข้อมูลมาอีกครั้ง
		$count_data = $db_dpis->send_cmd($cmd);
// $db_dpis->show_error(); echo"<hr>";
		while($data = $db_dpis->get_array()){		
			if($PER_ID == $data[PER_ID]) continue;
			//$person_count++;		

			$PER_ID = $data[PER_ID];
			//---เก็บ PER_ID ของคนนั้นลงใน array ถ้ามีชื่อในระดับตำแหน่งที่เรียงตามลำดับแรกมาแล้ว อันถัดไปไม่นำมาเก็บอีกต่อไปแล้ว
			if(!in_array($PER_ID,$arrkeep)){
				$arrkeep[] = $PER_ID;
				//=================================
				if(in_array($PER_ID,$ARRALL_PERID_LEVELNO)){
					$ARRALL_HAVE_PERID_LEVELNO[] = $PER_ID;
				}
				
				$ARRSORTLEVEL['per_id'][] = $PER_ID;
				
				$PN_NAME[$PER_ID] = trim($data[PN_NAME]);
				$PER_NAME[$PER_ID] = trim($data[PER_NAME]);
				$PER_SURNAME[$PER_ID] = trim($data[PER_SURNAME]);
				$POS_NO[$PER_ID] = trim($data[POS_NO_NAME]).trim($data[POS_NO]);
				$PL_NAME[$PER_ID] = trim($data[PL_NAME]);
				$LEVEL_NO[$PER_ID] = trim($data[LEVEL_NO]);
				$LEVEL_NOHIS[$PER_ID] = trim($data[LEVEL_NOHIS]);

				$ARRSORTLEVEL['per_sortlevel'][] = $LEVEL_NOHIS[$PER_ID];		//--------

				$LEVEL_NAME[$PER_ID] = trim($data[LEVEL_NAME]);
				$POSITION_LEVEL[$PER_ID] = trim($data[POSITION_LEVEL]);
				$PT_CODE[$PER_ID] = trim($data[PT_CODE]);
				$PT_NAME[$PER_ID] = trim($data[PT_NAME]);
				$ORG_NAME[$PER_ID] = trim($data[ORG_NAME]);
				if ($ORG_NAME[$PER_ID]=="-") $ORG_NAME[$PER_ID] = "";
				$DEPARTMENT_ID=trim($data[DEPARTMENT_ID]);
				$ORG_ID_1=trim($data[ORG_ID_1]);
				$PER_RETIREDATE=trim($data[PER_RETIREDATE]);
				$POH_EFFECTIVEDATE = substr(trim($data[POH_EFFECTIVEDATE]), 0, 10);
				if(trim($POH_EFFECTIVEDATE)){
					//เก็บวันเริ่มต้นเข้าสู่ระดับ ที่เป็นระดับตำแหน่งก่อนเลื่อนระดับล่าสุด เพื่อใช้จัดเรียงข้อมูลระดับตำแหน่ง/วันที่ (e.POH_LEVEL_NO และ e.POH_EFFECTIVEDATE)
					$PER_EFFECTIVEDATE[$LEVEL_NOHIS][$PER_ID] =$POH_EFFECTIVEDATE;		//$PER_EFFECTIVEDATE[$index][$PER_ID] =$POH_EFFECTIVEDATE;
					$POH_EFFECTIVEDATE2[$PER_ID] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);
				} // end if	

				$ARRSORTLEVEL['poh_effectivedate'][] = $POH_EFFECTIVEDATE;		//--------

				$PER_SALARY[$PER_ID] = trim($data[PER_SALARY]);

				$ARRSORTLEVEL['per_salary'][] = $PER_SALARY[$PER_ID];	//--------

				$PER_BIRTHDATE = substr(trim($data[PER_BIRTHDATE]), 0, 10);
				if(trim($PER_BIRTHDATE)){
					$PER_AGE[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_BIRTHDATE), "year"));		//floor
					$PER_BIRTHDATE2[$PER_ID] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);
				} // end if	

				$ARRSORTLEVEL['per_birthdate'][] = $PER_BIRTHDATE;	//--------

				$PER_STARTDATE = substr(trim($data[PER_STARTDATE]), 0, 10);
				if(trim($PER_STARTDATE)){
					$PER_WORK[$PER_ID]=round(date_difference(date("Y-m-d"), trim($PER_STARTDATE), "year"));		//floor
					$PER_STARTDATE2[$PER_ID] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);
				} // end if	

				$ARRSORTLEVEL['per_startdate'][] = $PER_STARTDATE;	//--------

				if(trim($PER_RETIREDATE)){
					$PER_RETIREDATE2[$PER_ID] = show_date_format($PER_RETIREDATE,$DATE_DISPLAY);
				} // end if	
			
				if($DPISDB=="odbc"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		
											( PER_EDUCATE a 
										left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
											) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c
							   where	a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.EN_CODE, b.EN_SHORTNAME, c.EM_NAME
							   from		
										(  PER_EDUCATE a
										left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
										)	left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
							   where	a.PER_ID=$PER_ID and (a.EDU_TYPE like '%2%' or a.EDU_TYPE like '%3%')
							   order by 	a.EDU_SEQ ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
				$PER_EDUCATE[$PER_ID] = "";
				while($data2 = $db_dpis2->get_array()){
					if($PER_EDUCATE[$PER_ID]) $PER_EDUCATE[$PER_ID] .= ", ";
					$PER_EDUCATE[$PER_ID] .= trim($data2[EN_SHORTNAME]);
					$EM_NAME[$PER_ID] = trim($data2[EM_NAME]);
				} // end while
			
				if($DPISDB=="odbc"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				}elseif($DPISDB=="oci8"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a, PER_DECORATION b
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3) and a.DC_CODE=b.DC_CODE(+)
							   order by 	SUBSTR(trim(a.DEH_DATE), 1, 10) desc ";
				}elseif($DPISDB=="mysql"){
					$cmd = " select		a.DC_CODE, b.DC_SHORTNAME
							   from		PER_DECORATEHIS a
										left join PER_DECORATION b on (a.DC_CODE=b.DC_CODE)
							   where	a.PER_ID=$PER_ID and DC_TYPE not in (3)
							   order by 	LEFT(trim(a.DEH_DATE), 10) desc ";
				} // end if
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PER_DECORATE[$PER_ID] = trim($data2[DC_SHORTNAME]);

				$ARRSORTLEVEL['per_decorate'][] = $PER_DECORATE[$PER_ID];	//--------
				
				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID";
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$DEPARTMENT_NAME[$PER_ID]=trim($data_org[ORG_NAME]);

				$cmd2="select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1";
				if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis3->send_cmd($cmd2);
				$data_org=$db_dpis3->get_array();
				$ORG_ID_1_NAME[$PER_ID]=trim($data_org[ORG_NAME]);
				if ($ORG_ID_1_NAME[$PER_ID]=="-") $ORG_ID_1_NAME[$PER_ID] = "";

				$cmd = " select	 POH_EFFECTIVEDATE from PER_POSITIONHIS where PER_ID=$PER_ID and LEVEL_NO='$LEVEL_NO[$PER_ID]' order by POH_EFFECTIVEDATE";		
				$db_dpis2->send_cmd($cmd);
			//	$db_dpis2->show_error();
	
	
				//เอาตน.ที่วันที่ว่าง ไว้ท้ายสุด
				// เริ่มต้น ให้หาวันที่น้อยที่สุดก่อน แล้วมาหา query ถัดไปของคนนั้น
				$data2 = $db_dpis2->get_array();
				$CURR_EFFECTIVEDATE_SORT[$LEVEL_NOHIS][$PER_ID] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
				
				$ARRSORTLEVEL['curr_effectivedate'][] = substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);	//--------
				
				$CURR_EFFECTIVEDATE[$PER_ID] = show_date_format(trim($data2[POH_EFFECTIVEDATE]),$DATE_DISPLAY);
//				echo "==>  $LEVEL_NOHIS / $PER_ID = ".substr(trim($data2[POH_EFFECTIVEDATE]), 0, 10);
			} 
		} // end while
	} //end for
	
//	print("<pre>");	print_r($CURR_EFFECTIVEDATE);	print("</pre>");

//	$SORTBY=array('poh_effectivedate'=>'asc','per_salary'=>'desc','per_startdate'=>'asc','per_birthdate'=>'asc','per_decorate'=>'desc');

	array_multisort($ARRSORTLEVEL['curr_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_sortlevel'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['poh_effectivedate'], SORT_ASC, SORT_STRING,
              					$ARRSORTLEVEL['per_salary'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_decorate'], SORT_NUMERIC, SORT_DESC,
              					$ARRSORTLEVEL['per_startdate'], SORT_STRING, SORT_DESC,
              					$ARRSORTLEVEL['per_birthdate'], SORT_STRING, SORT_DESC,
              					$ARRSORTLEVEL['per_id'], SORT_NUMERIC, SORT_ASC
								);

//แสดงรายการทั้งหมด
$data_count = 0;
$person_count = 0;
	//foreach($PER_EFFECTIVEDATE as $key=>$value){		//[$LEVEL_NOHIS][$PER_ID]
	foreach($ARRSORTLEVEL['per_id'] as $key=>$value){
				$PER_ID=trim($value);
					
					$person_count++;		
					$arr_content[$data_count][type] = "CONTENT";
					$arr_content[$data_count][name] = str_repeat(" ", (($rpt_order_index + 1) * 2)) . $person_count .". ". $PN_NAME[$PER_ID].$PER_NAME[$PER_ID]." ". $PER_SURNAME[$PER_ID];
					if ($BKK_FLAG==1)
						$arr_content[$data_count][position] = $PL_NAME[$PER_ID] . $POSITION_LEVEL[$PER_ID] . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"")."\n$ORG_ID_1_NAME[$PER_ID]  $ORG_NAME[$PER_ID] $DEPARTMENT_NAME[$PER_ID]";
					else
						$arr_content[$data_count][position] = $PL_NAME[$PER_ID] . $POSITION_LEVEL[$PER_ID] . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"")."\n$ORG_ID_1_NAME[$PER_ID]  $ORG_NAME[$PER_ID]";
					$arr_content[$data_count][level] = $LEVEL_NAME[$PER_ID] . (($PT_NAME[$PER_ID] != "ทั่วไป" && $LEVEL_NO[$PER_ID] >= 6)?"$PT_NAME[$PER_ID]":"");
					if ($BKK_FLAG==1)
						$arr_content[$data_count][org] = trim($ORG_ID_1_NAME[$PER_ID] ." ". $ORG_NAME[$PER_ID] ." ". $DEPARTMENT_NAME[$PER_ID]);
					else
						$arr_content[$data_count][org] = trim($ORG_ID_1_NAME[$PER_ID] ." ". $ORG_NAME[$PER_ID]);
					$arr_content[$data_count][posno] = $POS_NO[$PER_ID];
					$arr_content[$data_count][educate] = $PER_EDUCATE[$PER_ID];
					$arr_content[$data_count][poh_effectivedate] = $POH_EFFECTIVEDATE2[$PER_ID];	
					$arr_content[$data_count][curr_effectivedate] = $CURR_EFFECTIVEDATE[$PER_ID];	
					//หาระดับตำแหน่งนั้น--------------------
					$cmd2="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='".$LEVEL_NOHIS[$PER_ID]."' ";
					$db_dpis3->send_cmd($cmd2);
					$levelname=$db_dpis3->get_array();
					//---------------------------------------------
					$arr_content[$data_count][per_sortlevel]=$levelname[LEVEL_NAME];				//___$LEVEL_NO?level_no_format($LEVEL_NO):"";
					$arr_content[$data_count][per_salary] = $PER_SALARY[$PER_ID]?number_format($PER_SALARY[$PER_ID]):"";
					$arr_content[$data_count][decorate] = $PER_DECORATE[$PER_ID];
					$arr_content[$data_count][per_startdate] = $PER_STARTDATE2[$PER_ID]."\n (".$PER_WORK[$PER_ID]." ปี)";
					$arr_content[$data_count][per_birthdate] = $PER_BIRTHDATE2[$PER_ID]."\n (".$PER_AGE[$PER_ID]." ปี)";	
					$arr_content[$data_count][per_retiredate]=$PER_RETIREDATE2[$PER_ID];
					$arr_content[$data_count][em_name]=$EM_NAME[$PER_ID];
					$data_count++;
} //end foreach
//print_r($PER_EFFECTIVEDATE);
//echo "<hr> =>".$count_data."+".$data_count."+".$person_count;
	if($person_count>0){	$count_data=$person_count;	}
	
//new format************************************************************	
 	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$pdf->AutoPageBreak = false; 
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$seq = $data_count + 1;
			$name = $arr_content[$data_count][name];
			$position = $arr_content[$data_count][position];
			$level = $arr_content[$data_count][level];
			$org = $arr_content[$data_count][org];
			$posno = $arr_content[$data_count][posno];
			$educate = $arr_content[$data_count][educate];
			$em_name = $arr_content[$data_count][em_name];
			$poh_effectivedate = $arr_content[$data_count][poh_effectivedate];
			$curr_effectivedate = $arr_content[$data_count][curr_effectivedate];
			$per_sortlevel = $arr_content[$data_count][per_sortlevel];
			$per_salary = $arr_content[$data_count][per_salary];
			$decorate = $arr_content[$data_count][decorate];
			$per_startdate = $arr_content[$data_count][per_startdate];
			$per_work = $arr_content[$data_count][per_work];
			$per_birthdate = $arr_content[$data_count][per_birthdate];
			$per_age = $arr_content[$data_count][per_age];
			$per_retiredate=$arr_content[$data_count][per_retiredate];
			
			$arr_data = (array) null;
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($name):$name);	
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($position):$position);	
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($posno):$posno);					
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($educate):$educate);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($em_name):$em_name);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($poh_effectivedate):$poh_effectivedate);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($per_sortlevel):$per_sortlevel);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($per_salary):$per_salary);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($decorate):$decorate);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($per_startdate):$per_startdate);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($per_birthdate):$per_birthdate);			
			$arr_data[] =(($NUMBER_DISPLAY==2)?convert2thaidigit($per_retiredate):$per_retiredate);			
	
			$data_align = array("L", "L", "C", "L", "L", "C", "L", "C", "C", "C", "C", "C");
			
			$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "cordia", "14", "", "000000", "");		//TRHBL
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end for
		$pdf->add_data_tab("", 7, "RHBL", $data_align, "cordia", "12", "", "000000", "");		// เส้นปิดบรรทัด	
	}else{
		$pdf->SetFont($fontb,'',16);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();	
?>