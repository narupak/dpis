<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	
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
			$fname= $report_title.".pdf";
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
		$heading_width[0] = "35";
		$heading_width[1] = "55";
		$heading_width[2] = "35";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "25";
	}else{
		$heading_width[0] = "35";
		$heading_width[1] = "55";
		$heading_width[2] = "35";
		$heading_width[3] = "25";
		$heading_width[4] = "25";
		$heading_width[5] = "25";
	}	
	$heading_text[0] = "วัน-เวลาที่สแกน";
	$heading_text[1] = "ชื่อ-สกุล";
	$heading_text[2] = "ประเภทการลา";
	$heading_text[3] = "วันที่เริ่มต้น";
	$heading_text[4] = "วันที่สิ้นสุด";
	$heading_text[5] = "จำนวนวัน";
		
	$heading_align = array('C','C','C','C','C','C');
		

  



		$cmd = " 
					 select		q1.*
                             from (
					select 	DISTINCT
                                        abs.PER_ID,abs.ABS_STARTDATE,abs.ABS_STARTPERIOD,abs.ABS_ENDDATE,abs.ABS_ENDPERIOD,
                                        TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd') AS TIME_STAMP,
                                        a.PER_TYPE,g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
                                         c.POS_NO,d.POEM_NO,e.POEMS_NO,k.AB_NAME,j.POT_NO,abs.ABS_DAY
                                from  PER_ABSENTHIS  abs 
                                left join PER_TIME_ATTENDANCE att on(att.PER_ID=abs.PER_ID AND
												( TO_CHAR(att.TIME_STAMP,'yyyy-mm-dd')  BETWEEN substr(abs.ABS_STARTDATE,1,10)AND substr(abs.ABS_ENDDATE,1,10)))
                                left join PER_PERSONAL a on(a.PER_ID=abs.PER_ID)
                                left join PER_ORG b on(b.ORG_ID=a.ORG_ID) 
                                left join PER_POSITION c on(c.POS_ID=a.POS_ID) 
                                left join PER_POS_EMP d on(d.POEM_ID=a.POEM_ID) 
                                left join PER_POS_EMPSER e on(e.POEMS_ID=a.POEMS_ID) 
                                left join PER_LEVEL f on(f.LEVEL_NO=a.LEVEL_NO) 
                                left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
                                left join PER_MGT h on(h.PM_CODE=c.PM_CODE)
                                left join PER_LINE i on(i.PL_CODE=c.PL_CODE)
                                left join PER_POS_TEMP j on (j.POT_ID=a.POT_ID)
                                left join PER_ABSENTTYPE k on (k.AB_CODE=abs.AB_CODE)
                                
                            WHERE abs.AB_CODE NOT IN(10,13) AND att.TIME_STAMP IS NOT NULL
                            AND (att.TIME_STAMP  BETWEEN to_date('$BGN','yyyy-mm-dd hh24:mi:ss')   AND to_date('$END','yyyy-mm-dd hh24:mi:ss')) 
                        	AND (a.ORG_ID in (select org_id from PER_ORG_ASS start with ORG_ID in
                             								(select org_id from PER_ORG_ASS where org_id =$ORGID) 
                        									CONNECT BY PRIOR org_id = ORG_ID_REF)  )
															
						) q1  ORDER BY q1.TIME_STAMP asc ";
///echo  "<pre>".$cmd; die();

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
			
			$DATA_TIME_STAMP = show_date_format($data[TIME_STAMP], $DATE_DISPLAY);
			$cmd = " SELECT TO_CHAR(min(TIME_STAMP),'HH24:MI:SS') AS HHII FROM PER_TIME_ATTENDANCE  
						where PER_ID=".$data[PER_ID]." AND TO_CHAR(TIME_STAMP,'yyyy-mm-dd') ='".$data[TIME_STAMP]."'";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$TMP_HHII = $data1[HHII];
			$arr_data[] =$DATA_TIME_STAMP." ".$TMP_HHII;
			
			$DATA_FULLNAME_SHOW = trim($data[FULLNAME_SHOW]);
			$arr_data[] = $DATA_FULLNAME_SHOW;
			
			$DATA_AB_NAME = trim($data[AB_NAME]); 
            $arr_data[] = $DATA_AB_NAME; 
			 
			 $DATA_ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], $DATE_DISPLAY); 
			 $arr_data[] = $DATA_ABS_STARTDATE;  
			 
			 $DATA_ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], $DATE_DISPLAY); 
			 $arr_data[] = $DATA_ABS_ENDDATE;  
			 
			 $DATA_ABS_STARTPERIOD ="";
			if($data[ABS_STARTPERIOD]=="1"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งวันเช้า)";
			}elseif($data[ABS_STARTPERIOD]=="2"){
				$DATA_ABS_STARTPERIOD =" (ครึ่งวันบ่าย)";
			}
			if($data[ABS_DAY]!="0.5"){
				$DATA_ABS_STARTPERIOD ="";
			}
			
			$DATA_ABS_DAY = trim(round($data[ABS_DAY],2)).$DATA_ABS_STARTPERIOD;
			$arr_data[] = $DATA_ABS_DAY;  
			

			$data_align = array("C", "L", "L", "C", "C", "C");
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
                  }
		$data_align = array("C", "C", "C","C", "C","C");
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