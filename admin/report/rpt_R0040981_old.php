<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	$arr_history_name = explode("|", $HISTORY_LIST);
	
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(a.POS_ID), 0, CLng(a.POS_ID))";
				elseif($DPISDB=="oci8") $order_by .= "TO_NUMBER(a.POS_ID)";
				elseif($DPISDB=="mysql") $order_by .= "a.POS_ID+0";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE, c.PN_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="mysql") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_1, c.ORG_ID_1";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.ORG_ID_2, c.ORG_ID_2";
				break;
		} // end switch case
	} // end for

	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select DEPARTMENT_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[DEPARTMENT_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select MINISTRY_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[MINISTRY_NAME];

		unset($arr_department);
		$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	}elseif($PROVINCE_CODE){
		$cmd = " select ORG_ID from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='02' ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
		$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
	} // end if
	//หาประเภทข้าราชการ
	$arr_search_condition[] = "(a.LEVEL_NO = '$LEVEL_NO')";

	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = $data[LEVEL_NAME];
//	if(!trim($select_org_structure)){
	if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
	if(trim($search_org_id)) $arr_search_condition[] = "(c.ORG_ID = $search_org_id)";
	if(trim($search_org_id_1)) $arr_search_condition[] = "(c.ORG_ID_1 = $search_org_id_1)";
	if(trim($search_org_id_2)) $arr_search_condition[] = "(c.ORG_ID_2 = $search_org_id_2)";
//	}

	//$temp_date = explode("/", trim($SALARY_AT_DATE));
	//$SALARY_AT_DATE = ($temp_date)? ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0] : ""; 
	
	$arr_search_condition[] = "(a.POS_ID is not null)";
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||ข้อมูลข้าราชการ" . $LEVEL_NAME;
	$report_code = "R040981";
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
	$pdf->SetFont($font,'',12);
	
	$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	
	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'',9);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		$heading_width[0] = "8";
		$heading_width[1] = "32";
		$heading_width[2] = "30";
		$heading_width[3] = "30";
		$heading_width[4] = "15";
		$heading_width[5] = "17";
		$heading_width[6] = "13";
		$heading_width[7] = "10";
		$heading_width[8] = "13";
		$heading_width[9] = "13";
		$heading_width[10] = "8";
		$heading_width[11] = "19";
		$heading_width[12] = "27";
		$heading_width[13] = "27";
		$heading_width[14] = "27";
		//$heading_width[15] = "17";
		
		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"ชื่อ - สกุล",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"ตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3]+$heading_width[4]+$heading_width[5] ,7,"ส่วนราชการและตำแหน่งที่กำหนดใหม่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"วันเลื่อนระดับ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"เงินเดือน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"วันบรรจุ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"วันเกิด",'LTR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"เกษียณ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"ดำรงตำแหน่งปัจจุบัน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[12]+$heading_width[13]+$heading_width[14] ,7,"วุฒิการศึกษา",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"ชื่อตำแหน่งในการบริหารงาน",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"ประเภทตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ระดับตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[8] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[9] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[10] ,7,"พ.ศ.",'LBR',0,'C',1);
		$pdf->Cell($heading_width[11] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[12] ,7,"ปริญญาตรี",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[13] ,7,"ปริญญาโท",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[14] ,7,"ปริญญาเอก",'LTBR',1,'C',1);
	} // function	

	// ข้าราชการ
	if($DPISDB=="odbc"){
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, LEFT(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,LEFT(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
						 from		(	
											(
												(
													(
														(
															(
																(	
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
										) left join PER_MGT k on (b.PM_CODE = k.PM_CODE)
									)  left join PER_SALARYHIS n on (a.PER_ID = n.PER_ID)
											$search_condition
						 order by		$order_by
					   ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, substr(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,substr(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, SUBSTR(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
		from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i, PER_POSITIONHIS j, PER_MGT k, PER_SALARYHIS n
		where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+) and b.PM_CODE = k.PM_CODE (+) and a.PER_ID = n.PER_ID (+) $search_condition
		group by a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.LEVEL_NAME, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10), SUBSTR(trim(a.PER_STARTDATE), 1, 10) , SUBSTR(trim(a.PER_RETIREDATE), 1, 4), k.PM_NAME, h.PT_NAME, a.PER_SALARY
		order by		$order_by";
	}elseif($DPISDB=="mysql"){
		$cmd = "select a.PER_ID, a.POS_ID,  f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, LEFT(i.LEVEL_NAME,1,instr(i.LEVEL_NAME,' ')-1) as LEVEL_NAME1,LEFT(i.LEVEL_NAME,instr(i.LEVEL_NAME,' ')+1) as LEVEL_NAME2, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE, LEFT(trim(a.PER_RETIREDATE), 1, 4) as PER_RETIREDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE, k.PM_NAME, h.PT_NAME, a.PER_SALARY
						 from		(	
											(
												(
													(
														(
															(
																(	
																	(
																	PER_PERSONAL a 
																	left join PER_POSITION b on (a.POS_ID=b.POS_ID) 
																) left join PER_ORG c on (b.ORG_ID=c.ORG_ID)
															) left join PER_PRENAME f on (a.PN_CODE=f.PN_CODE)
														) left join PER_LINE g on (b.PL_CODE=g.PL_CODE)
													) left join PER_TYPE h on (b.PT_CODE=h.PT_CODE)
												) left join PER_LEVEL i on (a.LEVEL_NO=i.LEVEL_NO)
											) left join PER_POSITIONHIS j on (a.PER_ID=j.PER_ID)
										) left join PER_MGT k on (b.PM_CODE = k.PM_CODE)
									)  left join PER_SALARYHIS n on (a.PER_ID = n.PER_ID)
											$search_condition
						 order by		$order_by
					   ";
	}

	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;

	while($data = $db_dpis->get_array()){
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		$PL_NAME = $data[PL_NAME];
		$PM_NAME = $data[PM_NAME];
		$LEVEL_NAME1 = $data[LEVEL_NAME1];
		$LEVEL_NAME2 = $data[LEVEL_NAME2];
		
		if($DPISDB=="odbc"){

		}elseif($DPISDB=="oci8"){
			$cmd = "select MIN(SUBSTR(trim(b.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE  from PER_PERSONAL a, PER_POSITIONHIS b where a.PER_ID = b.PER_ID and a.PER_ID = '$PER_ID' and b.LEVEL_NO = '$LEVEL_NO'";
		}elseif($DPISDB=="mysql"){

		}

		//echo $cmd . "<br>";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$UPTO_DATE = $data2[POH_EFFECTIVEDATE];
		$UPTO_DATE_SALARY = $data2[POH_EFFECTIVEDATE];
		$UPTO_DATE = show_date_format($UPTO_DATE,$DATE_DISPLAY);
		if ($UPTO_DATE=="0  543") $UPTO_DATE="";
		
		if($DPISDB=="odbc"){

		}elseif($DPISDB=="oci8"){
			$cmd = "select MAX(b.SAH_SALARY) as SAH_SALARY from PER_PERSONAL a, PER_SALARYHIS b where a.PER_ID = b.PER_ID and a.PER_ID = $PER_ID";
			//$cmd = "select b.SAH_SALARY from PER_PERSONAL a, PER_SALARYHIS b where a.PER_ID = b.PER_ID and a.PER_ID = $PER_ID and SUBSTR(trim(b.SAH_EFFECTIVEDATE), 1, 10) = '$UPTO_DATE_SALARY'";
		}elseif($DPISDB=="mysql"){

		}
		
		//$db_dpis2->send_cmd($cmd);
		//$data2 = $db_dpis2->get_array();
		//$SALARY = $data2[SAH_SALARY];
		//if ($SALARY=="0.00" || $SALARY=="") $SALARY=$data["PER_SALARY"];
		$SALARY=$data["PER_SALARY"];

		$PT_CODE = trim($data[PT_CODE]);
		$ORG_SHORT = $data[ORG_SHORT];
		$ORG_NAME = $data[ORG_NAME];

		$BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$BIRTHDATE = show_date_format($BIRTHDATE,$DATE_DISPLAY);
		if ($BIRTHDATE=="0  543") $BIRTHDATE="";

		$STARTDATE = trim($data[PER_STARTDATE]);
		$STARTDATE = show_date_format($STARTDATE,$DATE_DISPLAY);
		if ($STARTDATE=="0  543") $STARTDATE="";

		$RETIREYEAR= trim($data[PER_RETIREDATE]);
		$arr_temp = explode("-", $RETIREYEAR);
		$RETIREYEAR = ($arr_temp[0] + 543);
		
		$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
		$POH_EFFECTIVEDATE = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);
		if ($POH_EFFECTIVEDATE=="0  543") $POH_EFFECTIVEDATE="";
		
		//ป.ตรี
		$EDUCATE40="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '40' or trim(b.el_code) = '001') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE40!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE40 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE40 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE40 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE40 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		//ป.โท
		$EDUCATE60="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '60' or trim(b.el_code) = '002') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE60!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE60 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE60 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE60 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE60 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		//ป.เอก
		$EDUCATE80="";
		if($DPISDB=="odbc"){
		}elseif($DPISDB=="oci8"){
			$cmd = "select a.per_id,c.en_name,c.en_shortname,d.em_name from per_personal a, per_educate b, per_educname c, per_educmajor d where a.per_id=b.per_id and b.en_code=c.en_code and a.per_id='$PER_ID' and  (b.el_code = '80' or trim(b.el_code) = '003') and b.em_code=d.em_code order by b.edu_seq";
			//หากต้องการวุฒิในตำแหน่ง ให้เพิ่มเงื่อนไข  and edu_type='||2||'
		}elseif($DPISDB=="mysql"){
		}
		$data_row2=0;
		$db_dpis2->send_cmd($cmd);
		while($data2 = $db_dpis2->get_array()){
			if ($EDUCATE80!=""){
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE80 .="|| " . $data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE80 .="|| " . $data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
			else{
				if ($data2["EN_SHORTNAME"]!="") $EDUCATE80 .=$data2["EN_SHORTNAME"] . "(" . $data2["EM_NAME"] . ")";
				else $EDUCATE80 .=$data2["EN_NAME"] . "(" . $data2["EM_NAME"] . ")";
			}
		}

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][order] = "$data_row.";
		$arr_content[$data_count][name] = $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		$arr_content[$data_count][position] = $PL_NAME;
		//$arr_content[$data_count][org_name] = ($ORG_SHORT)?"$ORG_SHORT":"$ORG_NAME";
		$arr_content[$data_count][pmname] = $PM_NAME;
		$arr_content[$data_count][level1] = str_replace("ประเภท","",$LEVEL_NAME1);
		$arr_content[$data_count][level2] = str_replace("ระดับ","",$LEVEL_NAME2);
		$arr_content[$data_count][uptodate] = $UPTO_DATE;
		$arr_content[$data_count][salary] = number_format($SALARY);
		$arr_content[$data_count][startdate] = $STARTDATE;
		$arr_content[$data_count][birthdate] = $BIRTHDATE;
		$arr_content[$data_count][retireyear] = $RETIREYEAR;
		$arr_content[$data_count][poheffectivedate] = $POH_EFFECTIVEDATE;
		$arr_content[$data_count][educate1] = $EDUCATE40;
		$arr_content[$data_count][educate2] = $EDUCATE60;
		$arr_content[$data_count][educate3] = $EDUCATE80;

		$data_count++;
	} // end while
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		print_header();
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$ORDER = $arr_content[$data_count][order];
			$NAME_1 = $arr_content[$data_count][name];
			$NAME_2 = $arr_content[$data_count][position];
			$NAME_3 = $arr_content[$data_count][pmname];
			$NAME_4 = $arr_content[$data_count][level1];
			$NAME_5 = $arr_content[$data_count][level2];
			$NAME_6 = $arr_content[$data_count][uptodate];
			$NAME_7 = $arr_content[$data_count][salary];
			$NAME_8 = $arr_content[$data_count][startdate];
			$NAME_9 = $arr_content[$data_count][birthdate];
			$NAME_10 = $arr_content[$data_count][retireyear];
			$NAME_11 = $arr_content[$data_count][poheffectivedate];
			$NAME_12 = $arr_content[$data_count][educate1];
			$NAME_13 = $arr_content[$data_count][educate2];
			$NAME_14 = $arr_content[$data_count][educate3];
			
			$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

			$border = "";
			$pdf->SetFont($font,'',9);
			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

			$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R', 0);
			$pdf->MultiCell($heading_width[1], 7, "$NAME_1", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[2], 7, "$NAME_2", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
			$pdf->y = $start_y;
			$pdf->MultiCell($heading_width[3], 7, "$NAME_3", $border, "L");
			if($pdf->y > $max_y) $max_y = $pdf->y;
			$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3];
			$pdf->y = $start_y;
			$pdf->Cell($heading_width[4], 7, "$NAME_4", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[5], 7, "$NAME_5", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[6], 7, "$NAME_6", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[7], 7, "$NAME_7", $border, 0, 'R', 0);
			$pdf->Cell($heading_width[8], 7, "$NAME_8", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[9], 7, "$NAME_9", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[10], 7, "$NAME_10", $border, 0, 'C', 0);
			$pdf->Cell($heading_width[11], 7, "$NAME_11", $border, 0, 'C', 0);
			//$pdf->MultiCell($heading_width[12], 7, "$NAME_12", $border, 0, "L", 0);
			//$pdf->MultiCell($heading_width[13], 7, "$NAME_13", $border, 0, "L", 0);
			//$pdf->MultiCell($heading_width[14], 7, "$NAME_14", $border, 1, "L", 0);
			if ($NAME_12!=""){
				$pdf->MultiCell($heading_width[12], 7, "$NAME_12", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]+ $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12];
				$pdf->y = $start_y;
			}else $pdf->Cell($heading_width[12], 7, "$NAME_12", $border, 0, 'L', 0);

			if ($NAME_13!=""){
				$pdf->MultiCell($heading_width[13], 7, "$NAME_13", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7]+ $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13];
				$pdf->y = $start_y;
			} else $pdf->Cell($heading_width[13], 7, "$NAME_13", $border, 0, 'L', 0);

			if ($NAME_14!=""){
				$pdf->MultiCell($heading_width[14], 7, "$NAME_14", $border, "L");
				if($pdf->y > $max_y) $max_y = $pdf->y;
				$pdf->y = $start_y;
			} else $pdf->Cell($heading_width[14], 7, "$NAME_14", $border, 1, 'L', 0);

			//$pdf->Cell($heading_width[15], 7, "", $border, 1, 'L', 0);

			//================= Draw Border Line ====================
			$line_start_y = $start_y;		$line_start_x = $start_x;
			$line_end_y = $max_y;		$line_end_x = $start_x;
			$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			
			for($i=0; $i<=15; $i++){
				$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
				$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
				$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
			} // end for
			//====================================================

			if(($pdf->h - $max_y - 10) < 15){ 
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				if($data_count < (count($arr_content) - 1)){
					$pdf->AddPage();
					print_header();
					$max_y = $pdf->y;
				} // end if
			}else{
				if($data_count == (count($arr_content) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
			} // end if
			$pdf->x = $start_x;			$pdf->y = $max_y;
		} // end for
		$border = "LTBR";
		$pdf->SetFont($fontb,'',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

		$pdf->Cell(($heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7] + $heading_width[8] + $heading_width[9] + $heading_width[10] + $heading_width[11] + $heading_width[12] + $heading_width[13] + $heading_width[14]), 7, " ", $border, 1, 'L', 0);

	}else{
		$pdf->SetFont($fontb,'',10);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
		//$pdf->Cell(287,10,$search_condition,0,1,'C');
	} // end if


	$pdf->close();
	$pdf->Output();		
	
?>