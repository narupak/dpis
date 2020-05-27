<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	   if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}
	
	ini_set("max_execution_time", $max_execution_time);
	
	$report_title = trim(iconv("utf-8","tis620",urldecode($report_title)));
	$report_code = "";
	

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	if ($FLAG_RTF) {
		$sum_w = array_sum($heading_width);
		for($h = 0; $h < count($heading_width); $h++) {
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}
		$fname= $report_title.".rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);
		} else {
			$fname= "T0201.pdf";
    $unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$orientation='P';
	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	$pdf->SetAutoPageBreak(true,10);
	}
	if ($FLAG_RTF) {
		$heading_width[0] = "20";
		$heading_width[1] = "45";
		$heading_width[2] = "35";
		$heading_width[3] = "35";
		$heading_width[4] = "46";
		$heading_width[5] = "6";
		$heading_width[6] = "6";
		$heading_width[7] = "6";
	}else{
		$heading_width[0] = "20";
		$heading_width[1] = "45";
		$heading_width[2] = "35";
		$heading_width[3] = "35";
		$heading_width[4] = "46";
		$heading_width[5] = "6";
		$heading_width[6] = "6";
		$heading_width[7] = "6";
	}
		
	$heading_text[0] = "เลขที่ตำแหน่ง";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "วัน-เวลาที่เริ่มใช้";
	$heading_text[3] = "วัน-เวลาที่สิ้นสุดรอบ";
	$heading_text[4] = "รอบการมาปฏิบัติราชการ";
	$heading_text[5] = "บัตร";
	$heading_text[6] = "นิ้ว";
	$heading_text[7] = "หน้า";
		
	$heading_align = array('C','C','C','C','C','C','C','C');
		
	$search_per_status = (isset($search_per_status))?  $search_per_status : 2;
  	if($search_org_id_1 && !$search_org_id_2 && !$search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_1=$search_org_id_1 or d.ORG_ID_1=$search_org_id_1 or e.ORG_ID_1=$search_org_id_1 or j.ORG_ID_1=$search_org_id_1)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_1=$search_org_id_1)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && !$search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_2=$search_org_id_2 or d.ORG_ID_2=$search_org_id_2 or e.ORG_ID_2=$search_org_id_2 or j.ORG_ID_2=$search_org_id_2)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_2=$search_org_id_2)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && !$search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_3=$search_org_id_3 or d.ORG_ID_3=$search_org_id_3 or e.ORG_ID_3=$search_org_id_3 or j.ORG_ID_3=$search_org_id_3)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_3=$search_org_id_3)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && $search_org_id_4 && !$search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_4=$search_org_id_4 or d.ORG_ID_4=$search_org_id_4 or e.ORG_ID_4=$search_org_id_4 or j.ORG_ID_4=$search_org_id_4)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_4=$search_org_id_4)";
                    }
                }elseif($search_org_id_1 && $search_org_id_2 && $search_org_id_3 && $search_org_id_4 && $search_org_id_5){ /* Release 5.1.0.4 */
                    if($select_org_structure==0){ //โครงสร้างตามกฎหมาย
                        $arr_search_condition[] = "(c.ORG_ID_5=$search_org_id_5 or d.ORG_ID_5=$search_org_id_5 or e.ORG_ID_5=$search_org_id_5 or j.ORG_ID_5=$search_org_id_5)";
                    }else if($select_org_structure==1){ //โครงสร้างตามมอบหมายงาน
                        $arr_search_condition[] = "(a.ORG_ID_5=$search_org_id_5)";
                    }
                }elseif($search_org_id){
			if($select_org_structure==0){
				$arr_search_condition[] = "(c.ORG_ID=$search_org_id or d.ORG_ID=$search_org_id or e.ORG_ID=$search_org_id or j.ORG_ID=$search_org_id)";
			}else if($select_org_structure==1){
				$arr_search_condition[] = "(a.ORG_ID=$search_org_id)";
			}
		}elseif($search_department_id){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $search_department_id)";
		}elseif($search_ministry_id){
			unset($arr_department);
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$search_ministry_id and OL_CODE='02' ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		}elseif($PROVINCE_CODE){
			$cmd = " select distinct ORG_ID_REF from PER_ORG where PV_CODE='$PROVINCE_CODE' and OL_CODE='03' ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_department[] = $data[ORG_ID_REF];
			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode(",", $arr_department) ."))";
		} // end if  

	 if(trim($search_per_status) < 5) {
            $temp_per_status = $search_per_status - 1;		
            $arr_search_condition[] = "(a.PER_STATUS = $temp_per_status)";	
    }

   	if(trim($search_name)) $arr_search_condition[] = "(a.PER_NAME like '".trim($search_name)."%' or UPPER(a.PER_ENG_NAME) like UPPER('".trim($search_name)."%'))";
   	 if(trim($search_surname)) $arr_search_condition[] = "(a.PER_SURNAME like '".trim($search_surname)."%' or UPPER(a.PER_ENG_SURNAME) like UPPER('".trim($search_surname)."%'))";

	
    if(trim($search_cardno)) $arr_search_condition[] = "(a.PER_CARDNO ='".trim($search_cardno)."')";
    if(trim($search_offno)) $arr_search_condition[] = "(a.PER_OFFNO ='".trim($search_offno)."')";
    
    if(trim($search_pos_no))  {	
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO) = '".trim($search_pos_no)."')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO) = '".trim($search_pos_no)."')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO) = '".trim($search_pos_no)."')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO) = '".trim($search_pos_no)."') or (trim(d.POEM_NO) = '".trim($search_pos_no)."') or (trim(e.POEMS_NO) = '".trim($search_pos_no)."') or (trim(j.POT_NO) = '".trim($search_pos_no)."')) ";
	}
    
    if(trim($search_pos_no_name)){
		if ($search_per_type == 1 || $search_per_type==5)
			$arr_search_condition[] = "(trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 2) 
			$arr_search_condition[] = "(trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%')";		
		elseif ($search_per_type == 3) 	
			$arr_search_condition[] = "(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%')";
		elseif ($search_per_type == 4) 	
			$arr_search_condition[] = "(trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')";
		else if ($search_per_type==0)		//ทั้งหมด
			$arr_search_condition[] = "((trim(c.POS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(d.POEM_NO_NAME) like '".trim($search_pos_no_name)."%') or 
			(trim(e.POEMS_NO_NAME) like '".trim($search_pos_no_name)."%') or (trim(j.POT_NO_NAME) like '".trim($search_pos_no_name)."%')) ";
	}
    
    if(trim($search_pay_no))  $arr_search_condition[] = "(trim(c.POS_NO) = '$search_pay_no' and a.PER_TYPE = 1)";
    if(trim($search_level_no)) $arr_search_condition[] = "(trim(a.LEVEL_NO) = '". trim($search_level_no) ."')";
    if(trim($search_pl_code)) $arr_search_condition[] = "(trim(c.PL_CODE) = '". trim($search_pl_code) ."')";
    if(trim($search_pm_code)) $arr_search_condition[] = "(trim(c.PM_CODE) = '". trim($search_pm_code) ."')";
    if(trim($search_per_type)) 	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
    
    if($search_date_min && $search_date_max){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " (   (substr(wch.START_DATE,1,10)  BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max') 
         					or  (substr(wch.END_DATE,1,10) BETWEEN '$tmpsearch_date_min' and '$tmpsearch_date_max' ) 
                            or ( '$tmpsearch_date_min'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
    						or ( '$tmpsearch_date_max'  BETWEEN substr(wch.START_DATE,1,10) and substr(wch.END_DATE,1,10) )
                            )";
	}else if($search_date_min && empty($search_date_max)){ 
		 $tmpsearch_date_min =  save_date($search_date_min);
         $arr_search_condition[] = " ('$tmpsearch_date_min' BETWEEN substr(wch.START_DATE,1,10) and  ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }else if(empty($search_date_min) && $search_date_max){ 
		 $tmpsearch_date_max =  save_date($search_date_max);
         $arr_search_condition[] = " ('$tmpsearch_date_max' BETWEEN substr(wch.START_DATE,1,10) and ( CASE WHEN wch.END_DATE IS NOT NULL THEN substr(wch.END_DATE,1,10) ELSE to_char(sysdate,'yyyy-mm-dd') END) ) ";
    }

     if(trim($search_wc_code)) $arr_search_condition[] = "(wch.WC_CODE ='".trim($search_wc_code)."')";
     
     if($search_per_status_scan==1) {
    	 $arr_search_condition[] = "(TRG.CARD_NO is not null OR TRG.FINGER_COUNT is not null OR TRG.FACE_COUNT is not null )";
     }else if($search_per_status_scan==2) {
     	$arr_search_condition[] = "(TRG.CARD_NO is null AND TRG.FINGER_COUNT is null AND TRG.FACE_COUNT is null )";
     }
     
    
    $search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);

	$Tmpsearch_per_status_workcyclehis = "";
    $Tmpsearch_per_status_workcyclehis = " AND wch.PER_ID is not  null
            															AND wch.WH_ID =(select max(WH_ID) as WH_ID FROM PER_WORK_CYCLEHIS WHERE PER_ID=wch.PER_ID) ";
                                                                        
    
    
    $cmd = " select 	wch.WH_ID,wc.WC_NAME,a.PER_TYPE,
							g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
							wch.START_DATE,wch.END_DATE,wch.REMARK,c.POS_NO,a.PER_ID,
							TRG.CARD_NO AS CARD_NO_S,TRG.FINGER_COUNT,TRG.FACE_COUNT,
							d.POEM_NO,e.POEMS_NO,j.POT_NO,a.PER_STATUS
                         	from  PER_PERSONAL a  
							left join  PER_WORK_CYCLEHIS wch on(a.PER_ID=wch.PER_ID)
							left join PER_WORK_CYCLE wc on(wc.WC_CODE=wch.WC_CODE)
							left join TA_REGISTERUSER TRG on (TRG.PER_ID=wch.PER_ID)
							left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
							left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
							left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
							left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
							left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
							left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
							left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
							left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
							left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                         	where 	1=1 $Tmpsearch_per_status_workcyclehis
                                        $search_condition 
							order by 	to_number(replace(c.POS_NO,'-','')) ASC, to_number(replace(d.POEM_NO,'-','')) ASC, to_number(replace(e.POEMS_NO,'-','')) ASC, to_number(replace(j.POT_NO,'-','')) ASC ";
//echo $cmd; die();
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();

	    $head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		$pdf->AutoPageBreak = false; 
	    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		          }
	if (!$result) echo "****** error ****** on open table for $table<br>";

	if($count_data){

		$data_count = $data_row = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			
			$DATA_POS_NO = trim($data[POS_NO]).trim($data[POEM_NO]).trim($data[POEMS_NO]).trim($data[POT_NO]);
			if($data[PER_STATUS] == 2){		//พ้นจากส่วนราชการ
                 $DATA_POS_NO = "";
            } // end if	
			$arr_data[] = $DATA_POS_NO;
			$arr_data[] = $data[FULLNAME_SHOW];

			$arr_data[] = show_date_format(substr($data[START_DATE],0,10), $DATE_DISPLAY)." ".substr($data[START_DATE],11,5);
			
			$DATA_END_DATE ="";
			if($data[END_DATE]){
				$DATA_END_DATE = show_date_format(substr($data[END_DATE],0,10), $DATE_DISPLAY)." ".substr($data[END_DATE],11,5);
			}
			
			$arr_data[] = $DATA_END_DATE;
		
			$arr_data[] = $data[WC_NAME];
		
			$arr_data[] = "<*img*".(($data[CARD_NO_S]=="" || $data[CARD_NO_S]=="0")?"../images/checkbox_blank.jpg":"../images/true.jpg")."*img*>";
			$arr_data[] = "<*img*".(($data[FINGER_COUNT]=="" || $data[FINGER_COUNT]=="0")?"../images/checkbox_blank.jpg":"../images/true.jpg")."*img*>";
			$arr_data[] = "<*img*".(($data[FACE_COUNT]=="" || $data[FACE_COUNT]=="0")?"../images/checkbox_blank.jpg":"../images/true.jpg")."*img*>";
			$data_align = array("C", "L", "C", "C","L", "C", "C","C");
			  if ($FLAG_RTF)
			 $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		    else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		} // end while
	}else{
	      if ($FLAG_RTF){
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		  }else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
                  }
		$data_align = array("C", "C", "C","C", "C", "C","C", "C");
	    if ($FLAG_RTF)
	    $result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		else	
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	  if ($FLAG_RTF) {
			$RTF->close_tab(); 
//			$RTF->close_section(); 
			$RTF->display($fname);
		} else {
			$pdf->close_tab(""); 
			$pdf->close();
			$pdf->Output($fname,'D');	
		}
	ini_set("max_execution_time", 30);
?>