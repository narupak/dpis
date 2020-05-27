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
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$arr_rpt_order = explode("|", $RPTORD_LIST);
	$order_by = "";
	for($rpt_order_index=0; $rpt_order_index<count($arr_rpt_order); $rpt_order_index++){
		$REPORT_ORDER = $arr_rpt_order[$rpt_order_index];
		
		switch($REPORT_ORDER){
			case "POS_NO" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "IIf(IsNull(b.POS_NO), 0, CLng(b.POS_NO))";
				elseif($DPISDB=="oci8") $order_by .= "to_number(replace(b.POS_NO,'-',''))";
				break;
			case "NAME" :
				if($order_by) $order_by .= ", ";
				$order_by .= "a.PER_NAME, a.PER_SURNAME";
				break;
			case "LINE" :
				if($order_by) $order_by .= ", ";
				$order_by .= "b.PL_CODE";
				break;
			case "LEVEL" :
				if($order_by) $order_by .= ", ";
				if($DPISDB=="odbc") $order_by .= "a.LEVEL_NO desc";
				elseif($DPISDB=="oci8") $order_by .= "a.LEVEL_NO desc";
				break;
			case "ORG" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID";
				else if($select_org_structure==1) $order_by .= "a.ORG_ID";
				break;
			case "ORG_1" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_1";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
			case "ORG_2" :
				if($order_by) $order_by .= ", ";
				if($select_org_structure==0) $order_by .= "b.ORG_ID_2";
				else if($select_org_structure==1)  $order_by .= "a.ORG_ID";
				break;
		} // end switch case
	} // end for
	if(!trim($order_by)) $order_by = "a.PER_ID";
	else $order_by .= ", a.PER_ID";

	$search_condition = "";
//	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_TYPE=1)";

  	if($DEPARTMENT_ID){
		$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$DEPARTMENT_NAME = $data[ORG_NAME];
	}elseif($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$MINISTRY_NAME = $data[ORG_NAME];

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

//	if(!trim($select_org_structure)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
		if(trim($search_org_id_1)) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
		if(trim($search_org_id_2)) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
//	}
	
	if(in_array("SELECT", $list_type)){//ค้นหารายบุคคล
		$arr_search_condition[] = "(a.PER_ID in ($SELECTED_PER_ID))";
	}
	
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	if ($service_list_type=="SELECT"){ //ค้นหารายประเภทราชการพิเศษ
		$sv_search_condition = "and (psh.SV_CODE in ($SELECTED_SV_CODE))";
	}else{
		$sv_search_condition ="";
	}
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||แบบประวัติการดำรงตำแหน่งในราชการพิเศษ";
	$report_code = "RA01001";
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
	
	function print_header(){
		global $pdf, $heading_width, $heading_name;
		
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
		$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
		
		$heading_width[0] = "12";
		$heading_width[1] = "85";
		$heading_width[2] = "55";
		$heading_width[3] = "30";
		$heading_width[4] = "25";
		$heading_width[5] = "20";
		$heading_width[6] = "20";
		$heading_width[7] = "40";
		
		$pdf->Cell($heading_width[0] ,7,"ลำดับที่",'LTR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"คณะกรรมการ/คณะทำงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"หน่วยงาน",'LTR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"การดำรงตำแหน่ง",'LTR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"คำสั่ง/ประกาศ",'LTR',0,'C',1);
		$pdf->Cell($heading_width[5]+$heading_width[6] ,7,"ระยะเวลาการดำรงตำแหน่ง",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"หมายเหตุ",'LTBR',1,'C',1);

		$pdf->Cell($heading_width[0] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[1] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[2] ,7,"เจ้าของเรื่อง",'LBR',0,'C',1);
		$pdf->Cell($heading_width[3] ,7,"",'LBR',0,'C',1);
		$pdf->Cell($heading_width[4] ,7,"เลขที่/ลงวันที่",'LBR',0,'C',1);
		$pdf->Cell($heading_width[5] ,7,"ตั้งแต่วันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[6] ,7,"ถึงวันที่",'LTBR',0,'C',1);
		$pdf->Cell($heading_width[7] ,7,"",'LBR',1,'C',1);

	} // function	

	if($DPISDB=="odbc"){
		$cmd = " select			b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, LEFT(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(LEFT(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
						 from		(
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
									) left join PER_POSITIONHIS j on (a.POS_ID=j.POS_ID) 
											$search_condition and a.PER_STATUS=1
							 group by b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, LEFT(trim(a.PER_BIRTHDATE), 1, 10), LEFT(trim(a.PER_STARTDATE), 1, 10)
						order by $order_by";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace(" where ", " and ", $search_condition);
		$cmd = " select			b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_BIRTHDATE), 1, 10) as PER_BIRTHDATE, SUBSTR(trim(a.PER_STARTDATE), 1, 10) as PER_STARTDATE,MAX(SUBSTR(trim(j.POH_EFFECTIVEDATE), 1, 10)) as POH_EFFECTIVEDATE
						 from			PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_PRENAME f, PER_LINE g, PER_TYPE h, PER_LEVEL i,PER_POSITIONHIS j
						 where		a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) 
											and a.PN_CODE=f.PN_CODE(+) and b.PL_CODE=g.PL_CODE(+) and b.PT_CODE=h.PT_CODE(+) and a.LEVEL_NO=i.LEVEL_NO(+) and a.PER_ID=j.PER_ID(+) and a.PER_STATUS=1
											$search_condition
							 group by b.POS_NO, a.PER_ID, f.PN_NAME, a.PER_NAME, a.PER_SURNAME, g.PL_NAME, a.LEVEL_NO, i.POSITION_LEVEL, i.LEVEL_SEQ_NO, c.ORG_SHORT, c.ORG_NAME, SUBSTR(trim(a.PER_STARTDATE), 1, 10), SUBSTR(trim(a.PER_BIRTHDATE), 1, 10)
						order by $order_by";
	}
	
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data_count = 0;
	//echo $cmd . "<br>";

	$data_count2 = 0;
	while($data = $db_dpis->get_array()){
		$arr_content[$data_count][PER_ID] = $data[PER_ID];
		$arr_content[$data_count][PN_NAME] = $data[PN_NAME];
		$arr_content[$data_count][PER_NAME] = $data[PER_NAME];
		$arr_content[$data_count][PER_SURNAME] = $data[PER_SURNAME];
		$arr_content[$data_count][PL_NAME] = $data[PL_NAME];
		$arr_content[$data_count][LEVEL_NO] = $data[LEVEL_NO];
		$arr_content[$data_count][POSITION_LEVEL] = $data[POSITION_LEVEL];
		$arr_content[$data_count][PT_CODE] = trim($data[PT_CODE]);
		$arr_content[$data_count][ORG_NAME] = $data[ORG_NAME];

		$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
		$arr_content[$data_count][PER_BIRTHDATE] = show_date_format($PER_BIRTHDATE,$DATE_DISPLAY);

		$PER_STARTDATE = trim($data[PER_STARTDATE]);
		$arr_content[$data_count][PER_STARTDATE] = show_date_format($PER_STARTDATE,$DATE_DISPLAY);

		$POH_EFFECTIVEDATE = trim($data[POH_EFFECTIVEDATE]);
		$arr_content[$data_count][POH_EFFECTIVEDATE] = show_date_format($POH_EFFECTIVEDATE,$DATE_DISPLAY);

		$arr_content[$data_count][AGE] = date_difference(date("Y-m-d"), $data[PER_BIRTHDATE], "ymd");
		$arr_content[$data_count][WORK_DURATION] = date_difference(date("Y-m-d"), $data[PER_STARTDATE], "ymd");
	
		$cmd = "SELECT SRH_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_DOCNO, SRH_NOTE,  psh.SV_CODE, psh.SRT_CODE, psh.ORG_ID, ps.SV_NAME, pst.SRT_NAME, 
										po.ORG_NAME, psh.UPDATE_USER, psh.UPDATE_DATE  
					FROM		PER_SERVICEHIS psh, PER_SERVICE ps, PER_SERVICETITLE pst, PER_ORG po  
					WHERE	psh.SV_CODE=ps.SV_CODE and psh.SRT_CODE=pst.SRT_CODE and psh.ORG_ID=po.ORG_ID and psh.PER_ID=$data[PER_ID] $sv_search_condition ORDER BY SRH_STARTDATE DESC, SRH_ENDDATE DESC";

		$count_data2 = $db_dpis2->send_cmd($cmd);
		//echo $cmd . "<br><br>";
		//echo $count_data2;
		while($data2 = $db_dpis2->get_array()){
			$arr_content2[$data_count2][PER_ID] = $data[PER_ID];

			$arr_content2[$data_count2][SRH_STARTDATE] = show_date_format(trim($data2[SRH_STARTDATE]), $DATE_DISPLAY);
			$arr_content2[$data_count2][SRH_ENDDATE] = show_date_format(trim($data2[SRH_ENDDATE]), $DATE_DISPLAY);
			$arr_content2[$data_count2][SRH_NOTE] = trim($data2[SRH_NOTE]);
			$arr_content2[$data_count2][SRH_DOCNO] = trim($data2[SRH_DOCNO]);
			$arr_content2[$data_count2][SV_NAME] = trim($data2[SV_NAME]);
			$arr_content2[$data_count2][ORG_ID] = trim($data2[ORG_ID]);
			$arr_content2[$data_count2][ORG_NAME] = trim($data2[ORG_NAME]);
			$arr_content2[$data_count2][SRT_NAME] = trim($data2[SRT_NAME]);		

			$data_count2++;
		}
		//echo count($arr_content2)."<br><br>";
		$data_count++;
	} // end while
	if($count_data){
		$data2_check=0;
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			if ($data_count==0){
				$pdf->Cell(287 ,7,$arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME],0,1,"C");
				$pdf->Cell(287 ,7,$arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],0,1,"C");
				$pdf->Cell(287 ,7,$arr_content[$data_count][ORG_NAME],0,1,"C");
				$pdf->AutoPageBreak = false;
				print_header();

			}else{
				$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
				$pdf->AddPage();

				$PER_ID_PREV=$arr_content[$data_count][PER_ID];
				$pdf->Cell(287 ,7,$arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME],0,1,"C");
				$pdf->Cell(287 ,7,$arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],0,1,"C");
				$pdf->Cell(287 ,7,$arr_content[$data_count][ORG_NAME],0,1,"C");
				$pdf->AutoPageBreak = false;
				print_header();
				//$max_y = $pdf->y;
				//$pdf->x = $start_x;			$pdf->y = $max_y;
			}
			
			$REPORT_ORDER = $arr_content[$data_count][type];
			//echo "count(arr_content2) = " . count($arr_content2) . "<br>";
			//echo "data2_check = " . $data2_check . "<br>";
			$ORDERrun=0;
			
			if ((count($arr_content2)>0) && (count($arr_content2)> $data2_check)) {
				while ($arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]){
					//echo "in while <br><br><br><br>";
					$ORDERrun++;
					$ORDER = $ORDERrun;
					$SRT_NAME = $arr_content2[$data2_check][SRT_NAME];
					$ORG_NAME = $arr_content2[$data2_check][ORG_NAME];
					$SV_NAME = $arr_content2[$data2_check][SV_NAME];
					$SRH_DOCNO = $arr_content2[$data2_check][SRH_DOCNO];
					$SRH_STARTDATE = $arr_content2[$data2_check][SRH_STARTDATE];
					$SRH_ENDDATE = $arr_content2[$data2_check][SRH_ENDDATE];
					$SRH_NOTE = $arr_content2[$data2_check][SRH_NOTE];
					
					$start_x = $pdf->x;			$start_y = $pdf->y;				$max_y = $pdf->y;

					$border = "";
					$pdf->SetFont($font,'',14);
					$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
					$countarr=count($arr_content2) - 1;
					$pdf->Cell($heading_width[0], 7, "$ORDER", $border, 0, 'R');
					$pdf->MultiCell($heading_width[1], 7, "$SRT_NAME", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1];
					$pdf->y = $start_y;

					$pdf->MultiCell($heading_width[2], 7, "$ORG_NAME", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2];
					$pdf->y = $start_y;

					$pdf->Cell($heading_width[3], 7, "$SV_NAME", $border, 0, 'C');
					$pdf->Cell($heading_width[4], 7, "$SRH_DOCNO", $border, 0, 'L');
					$pdf->Cell($heading_width[5], 7, "$SRH_STARTDATE", $border, 0, 'C');
					$pdf->Cell($heading_width[6], 7, "$SRH_ENDDATE", $border, 0, 'C');

					$pdf->MultiCell($heading_width[7], 7, "$SRH_NOTE", $border, "L");
					if($pdf->y > $max_y) $max_y = $pdf->y;
					$pdf->x = $start_x + $heading_width[0] + $heading_width[1] + $heading_width[2] + $heading_width[3] + $heading_width[4] + $heading_width[5] + $heading_width[6] + $heading_width[7];
					$pdf->y = $start_y;

					//================= Draw Border Line ====================
					$line_start_y = $start_y;		$line_start_x = $start_x;
					$line_end_y = $max_y;		$line_end_x = $start_x;
					$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					
					for($i=0; $i<=7; $i++){
						$line_start_y = $start_y;		$line_start_x += $heading_width[$i];
						$line_end_y = $max_y;		$line_end_x += $heading_width[$i];
						$pdf->Line($line_start_x, $line_start_y, $line_end_x, $line_end_y);
					} // end for
					$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
					//====================================================

					if(($pdf->h - $max_y - 10) < 15){ 
						//$pdf->Line($start_x, $max_y, $pdf->x, $max_y);
						if($data_count < (count($arr_content2) - 1)){
							$pdf->AddPage();
							$pdf->Cell(287 ,7,$arr_content[$data_count][PN_NAME] . $arr_content[$data_count][PER_NAME] ." ". $arr_content[$data_count][PER_SURNAME],0,1,"C");
							$pdf->Cell(287 ,7,$arr_content[$data_count][PL_NAME] . $arr_content[$data_count][POSITION_LEVEL],0,1,"C");
							$pdf->Cell(287 ,7,$arr_content[$data_count][ORG_NAME],0,1,"C");
							$pdf->Cell(287 ,7,"(ต่อ)",0,1,"R");
							$pdf->AutoPageBreak = false;
							print_header();
							$max_y = $pdf->y;
						} // end if
					}else{
						if($data_count == (count($arr_content2) - 1)) $pdf->Line($start_x, $max_y, $pdf->x, $max_y);		
					} // end if
					$pdf->x = $start_x;			$pdf->y = $max_y;
					$data2_check++;
				}// end while $arr_content[$data_count][PER_ID]==$arr_content2[$data2_check][PER_ID]
			}
		} // end for
		
		$border = "LTBR";
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	}else{
		$pdf->SetFont($font,'b',14);
		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
		$pdf->Cell(287,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$pdf->close();
	$pdf->Output();		
	
?>