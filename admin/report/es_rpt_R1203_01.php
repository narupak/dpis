<?php
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/es_fpdf.php");
	include ("../../PDF/es_pdf_extends_DPIS.php");
	
	ini_set("max_execution_time", $max_execution_time);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	
	/*เงื่อนไขการออกรายงาน*/
	/*ประเภทบุคลากร*/
	$con_per_type = "";
	$con_per_type = " and a.PER_TYPE = $search_per_type ";
	
        /* หาสถานะ */
        $con_per_status = "";
        if($search_per_type){
            $con_per_status = "a.PER_STATUS in (". implode(", ", $search_per_status) .")";
        }
	
	/*วันที่*/
	function MonthDays($someMonth, $someYear)
	{
		return date("t", strtotime($someYear . "-" . $someMonth . "-01"));
	}
	
	if(trim($search_yearBgn)){
		
		$search_dateBgn = ($search_yearBgn - 543) ."-". substr("0".$search_month,-2) ."-01";
		$search_dateEnd = ($search_yearBgn- 543) ."-". substr("0".$search_month,-2) ."-". MonthDays(substr("0".$search_month,-2),($search_yearBgn-543));
		$show_date = ("1") ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn ." ถึงวันที่ ".MonthDays(substr("0".$search_month,-2),($search_yearBgn-543)) ." ". $month_full[($search_month + 0)][TH] ." ". $search_yearBgn;
	}
	
	$show_date= (($NUMBER_DISPLAY==2)?convert2thaidigit($show_date):$show_date);
	
	/*---------------------------------------*/

	$search_condition = "";
	//$list_type_text = $ALL_REPORT_TITLE;
	$list_type_text = "";
        $list_type_texts = "";
	/*รูปแบบการออกรายงาน*/

	/*สำนัก/กอง*/
	if( ($search_org_id > 0) || ($search_org_ass_id > 0)){
            if($select_org_structure==0) {
                if(trim($search_org_id)){ 
                    $search_condition .= " AND  (PP.ORG_ID = $search_org_id) ";
                    $list_type_text .= " - $search_org_name";
                    if($search_org_id_1){
                        $search_condition .= " AND  (PP.ORG_ID_1 = $search_org_id_1) ";
                        $list_type_texts = ' - '.$search_org_name_1;
                    }
                } // end if
            }else{
                if(trim($search_org_ass_id)){ 
                    $search_condition .= " AND  (a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= " - $search_org_ass_name";
                    if($search_org_ass_id_1){
                        $search_condition .= " AND  (a.ORG_ID_1 = $search_org_ass_id_1) ";
                        $list_type_texts = ' - '.$search_org_ass_name_1;
                    }
                } // end if
            }
	}else{
            $search_condition .= " AND (a.DEPARTMENT_ID = $DEPARTMENT_ID)";
	}
/*------------------------------------------*/
	$company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R1205";
	include ("es_rpt_R1203_01_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	//$orientation='L';
	$orientation='L';

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
	$pdf->Open();
	//$pdf->SetMargins(20,5,17);
	$pdf->SetFont($font,'',$CH_PRINT_SIZE);
    $pdf->SetMargins($Px_DISLEFT,$Px_DISHEAD,$Px_DISRIGHT);
	$pdf->AliasNbPages();
	$pdf->SetTextColor(0, 0, 0);
	
	
	//แสดงข้อมูลว่าปิดรอบแล้วหรือยัง
	$cmd = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'IS_OPEN_TIMEATT_ES' ";
	$db_dpis->send_cmd_fast($cmd);
	$data = $db_dpis->get_array_array();
	$IS_OPEN_TIMEATT_ES = $data[CONFIG_VALUE];
	$tmpstar= "''";
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$search_monthapp = $search_month;
		$tmpstar= "(case when (select APPROVE_DATE from PER_WORK_TIME_CONTROL 
								WHERE  CLOSE_YEAR = $search_yearBgn
					  			AND CLOSE_MONTH = $search_monthapp
					  			AND DEPARTMENT_ID = (select ORG_ID from PER_PERSONAL where PER_ID=a.PER_ID)) is null then '*' else '' end
						)";
	}

	if($DPISDB=="oci8"){
		$cmd = file_get_contents('../es_rpt_R1205.sql');	
		$cmd=str_ireplace(":BEGINDATEAT","'".$search_dateBgn."'",$cmd);
		$cmd=str_ireplace(":TODATEAT","'".$search_dateEnd."'",$cmd);
		
		$CON_PER_AUDIT_FLAG="";
		if ( $PER_AUDIT_FLAG==1 ){
                    $tCon="(";
                    for ($i=0; $i < count($SESS_AuditArray); $i++) {
                            if ($i>0)
                                    $tCon .= " or ";
                            $tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
                            if ($SESS_AuditArray[$i][1] != 0)
                                    $tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
                            $tCon .= ")";
                    }
                    $tCon .= ")";
                     $CON_PER_AUDIT_FLAG .= $tCon;

                    if($search_org_ass_id){
                        $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID=$search_org_ass_id)";
                        if($search_org_ass_id_1){
                            $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID_1=$search_org_ass_id_1)";
                        }
                    }
				
		}
		
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}
		
		if($select_org_structure==0 || $PER_AUDIT_FLAG==2) { 
			/*ตามโครงสร้างกฏหมาย*/
			if($search_per_type==1){ 
				$POS_NO_FROM=" LEFT JOIN PER_POSITION PP ON (PP.POS_ID=a.POS_ID)";
			}elseif($search_per_type==2){ 
				$POS_NO_FROM=" LEFT JOIN PER_POS_EMP PP ON (PP.POEM_ID=a.POEM_ID)";
			}elseif($search_per_type==3){ 
				$POS_NO_FROM=" LEFT JOIN PER_POS_EMPSER PP ON (PP.POEMS_ID=a.POEMS_ID)";
			}elseif($search_per_type==4){ 
				$POS_NO_FROM="  LEFT JOIN PER_POS_TEMP PP ON (PP.POT_ID=a.POT_ID)";
			}
			
			$conTPER_ORG = " LEFT JOIN PER_ORG  ORG ON(ORG.ORG_ID=PP.ORG_ID) ";
			
		}else{
			/*ตามโครงสร้างมอบหมายงาน*/
			$conTPER_ORG = " LEFT JOIN PER_ORG_ASS  ORG ON(ORG.ORG_ID=a.ORG_ID) ";
		}
		
		
		$cmd=" select ORG.ORG_NAME AS KONG,
					  PN.PN_NAME,a.PER_NAME,a.PER_SURNAME, ".$tmpstar." AS datastar,
					  x.* from PER_PERSONAL a left join  
					  (".$cmd.") x ON(a.PER_ID=x.PER_ID(+)) 
			left join PER_PRENAME PN on(PN.PN_CODE=a.PN_CODE) 
			$POS_NO_FROM
			$conTPER_ORG
			WHERE  $con_per_status
			
			$con_per_type
			$search_condition
			$CON_PER_AUDIT_FLAG
			order by ORG.ORG_NAME ASC ,a.PER_NAME ASC , a.PER_SURNAME ASC
		";
		
		
		
	}
    //echo "<pre>\n";
	//echo $cmd;
	//die();
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "<pre>\n";
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$chkKONG = "";
	while($data = $db_dpis->get_array_array()){		
		if($chkKONG != $data[KONG]){
			$chkKONG = $data[KONG];
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][KONG] = $data[KONG];

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$arr_content[$data_count][type] = "CONTENT";

		$arr_content[$data_count][ORDER] = $data_row;
		$arr_content[$data_count][name] = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME].$data[DATASTAR];
		$arr_content[$data_count][LSICK] = $data[LSICK]==0?'':round($data[LSICK],2);
		$arr_content[$data_count][LSICKCNT] = $data[LSICKCNT]==0?'':round($data[LSICKCNT],2);		
		$arr_content[$data_count][LAKIT] = $data[LAKIT]==0?'':round($data[LAKIT],2);	
		$arr_content[$data_count][LAKITCNT] = $data[LAKITCNT]==0?'':round($data[LAKITCNT],2);	
		$arr_content[$data_count][LSICK_LAKIT] = $data[LSICK_LAKIT]==0?'':round($data[LSICK_LAKIT],2);	
		$arr_content[$data_count][LSICK_LAKITCNT] = $data[LSICK_LAKITCNT]==0?'':round($data[LSICK_LAKITCNT],2);	
		$arr_content[$data_count][LATE] = $data[LATE]==0?'':round($data[LATE],2);	
		$arr_content[$data_count][PAKPON] = $data[PAKPON]==0?'':round($data[PAKPON],2);
		$arr_content[$data_count][PAKPONCNT] = $data[PAKPONCNT]==0?'':round($data[PAKPONCNT],2);	
		$arr_content[$data_count][LAOTH] = $data[LAOTH]==0?'':round($data[LAOTH],2);	

		$data_count++;
	} // end while
	
	//echo "<pre>"; print_r($arr_content); echo "</pre>";
	//die();
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);

		$pdf->AutoPageBreak = false; 
//		echo "$head_text1<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", $CH_PRINT_SIZE, "b", "000", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		
		if (!$result) echo "****** error ****** on open table for $table<br>";
		$footKONG = "";
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			$KONG = $arr_content[$data_count][KONG];
			if($REPORT_ORDER == "ORG"){
				$pdf->SetFont($font,'',$CH_PRINT_SIZE);
				$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
				$pdf->SetFont($font,'',$CH_PRINT_SIZE);
				$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$KONG"."$list_type_texts ";
	
				$pdf->SetFont($font,'',$CH_PRINT_SIZE);
				$pdf->AddPage();
	//				print_header();
				$pdf->print_tab_header();
				
			}elseif($REPORT_ORDER == "CONTENT"){
				
				$arr_data = (array) null;

				$arr_data[] = $arr_content[$data_count][ORDER];
				$arr_data[] = $arr_content[$data_count][name];
				$arr_data[] = $arr_content[$data_count][LSICK];
				$arr_data[] = $arr_content[$data_count][LSICKCNT];
				$arr_data[] = $arr_content[$data_count][LAKIT];
				$arr_data[] = $arr_content[$data_count][LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKIT];
				$arr_data[] = $arr_content[$data_count][LSICK_LAKITCNT];
				$arr_data[] = $arr_content[$data_count][LATE];
				$arr_data[] = $arr_content[$data_count][PAKPON];
				$arr_data[] = $arr_content[$data_count][PAKPONCNT];
				$arr_data[] = $arr_content[$data_count][LAOTH];
				$arr_data[] = "";

				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", $CH_PRINT_SIZE, "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				
				
			} // end if			
		} // end for
	}else{
		$pdf->SetFont($font,'',$CH_PRINT_SIZE);
		$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]||ตั้งแต่วันที่ $show_date";
		$pdf->SetFont($font,'',$CH_PRINT_SIZE);
		$pdf->company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text"."$list_type_texts";

		$pdf->SetFont($font,'',$CH_PRINT_SIZE);
		$pdf->print_tab_header();
		$pdf->AddPage();
		$h = 35;
		$pdf->Text(140,$h,"ไม่พบข้อมูล");
	} // end if
	

	$pdf->close_tab(""); 
	
	if($IS_OPEN_TIMEATT_ES=="OPEN"){ // กรณีมีระบบลงเวลา
		$y = $pdf->y;	
		$h = 6;
		if ($y+($h*9) > ($pdf->PageBreakTrigger)) {
			
			//$pdf->SetFont($font,'b',$CH_PRINT_SIZE);
			$pdf->AddPage();
			$pdf->SetTextColor(0, 0, 0);
			$y = $pdf->y;	
		}
		
		$y += $h;
		$pdf->Text(8+$Px_DISLEFT,$y+$h,"* = ข้อมูลยังไม่ถูกปิดรอบเดือน (ตามมอบหมายงาน)");
	}
	
	$pdf->close();
	$fname = "R1205.pdf"; 
	$pdf->Output($fname,'D');	
	//$pdf->Output();
?>