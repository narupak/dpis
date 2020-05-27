<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_AB = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
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
	$report_title = "ข้อมูลประวัติการลงเวลา||$PER_NAME";
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
		$heading_width[0] = "15";
		$heading_width[1] = "25";
		$heading_width[2] = "15";
		$heading_width[3] = "15";
		$heading_width[4] = "30";
		$heading_width[5] = "25";
		$heading_width[6] = "25";
		$heading_width[7] = "25";
		$heading_width[8] = "25";
	}else{
		$heading_width[0] = "12";
		$heading_width[1] = "22";
		$heading_width[2] = "15";
		$heading_width[3] = "15";
		$heading_width[4] = "30";
		$heading_width[5] = "25";
		$heading_width[6] = "23";
		$heading_width[7] = "23";
		$heading_width[8] = "34";
	}	
	$heading_text[0] = "$SEQ_NO_TITLE";
	$heading_text[1] = "วันที่";
	$heading_text[2] = "เวลาเข้า";
	$heading_text[3] = "เวลาออก";
	$heading_text[4] = "รอบเวลา";
	$heading_text[5] = "สถานะการลงเวลา";
	$heading_text[6] = "สถานะการลา";
	$heading_text[7] = "สถานะวัน";
	$heading_text[8] = "หมายเหตุ";
		
	$heading_align = array('C','C','C','C','C','C','C','C','C');
	
	if(!$search_date_min){
    	$bgnbackMonth= date('Y-m',strtotime('-1 month'));
        $search_date_min= "01/".substr($bgnbackMonth,5,2)."/".(substr($bgnbackMonth,0,4)+543);
    }
    
    if(!$search_date_max){
        $bgnbackMonth= date('Y-m',strtotime('-1 month'))."-01";
        $MonthEND=date('Y-m',strtotime('-1 month'))."-".date("t",strtotime($bgnbackMonth));
        $search_date_max= substr($MonthEND,8,2)."/".substr($MonthEND,5,2)."/".(substr($MonthEND,0,4)+543);
        
    }
	
	if($search_date_min && $search_date_max){ 
                 $tmpsearch_date_min =  save_date($search_date_min);
                 $tmpsearch_date_max =  save_date($search_date_max);
                 $condition= " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_min 00:00:00','yyyy-mm-dd hh24:mi:ss')  and to_date('$tmpsearch_date_max  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
            }else if($search_date_min && empty($search_date_max)){ 
                 $tmpsearch_date_min =  save_date($search_date_min);
                 $condition = " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_min 00:00:00','yyyy-mm-dd hh24:mi:ss') and to_date('$tmpsearch_date_min  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
            }else if(empty($search_date_min) && $search_date_max){ 
                 $tmpsearch_date_max =  save_date($search_date_max);
                 $condition = " AND ( wt.WORK_DATE BETWEEN  to_date('$tmpsearch_date_max 00:00:00','yyyy-mm-dd hh24:mi:ss') and to_date('$tmpsearch_date_max  23:59:59','yyyy-mm-dd hh24:mi:ss')) ";
    		}
	
	
	$cmd = " select  TO_CHAR(wt.WORK_DATE,'yyyy-mm-dd') AS WORK_DATE,
				TO_CHAR(wt.APV_ENTTIME,'hh24:mi') AS APV_ENTTIME,
				TO_CHAR(wt.APV_EXITTIME,'hh24:mi') AS APV_EXITTIME,cl.WC_NAME,wt.ABSENT_FLAG,
				wt.WORK_FLAG,wt.HOLIDAY_FLAG,wt.REMARK,
				TO_CHAR(wt.SCAN_ENTTIME,'hh24:mi') AS SCAN_ENTTIME,
				TO_CHAR(wt.SCAN_EXITTIME,'hh24:mi') AS SCAN_EXITTIME,
				case when (exists (select null from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and trim(ab_code) not in ('10','13')and 
					  pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)))) then '3'||
						(select trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and trim(ab_code) not in ('10','13') and
					  pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))) || ',0'
				else 
		
					nvl((select '3'||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=3 and trim(ab_code) not in ('10','13')and 
					  pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)))
					,
		
					  nvl(
						(select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod<>2 and /*trim(ab_code) not in ('10','13')and */
						pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
						   (
							  nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod<>2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
								  nvl((select to_char(abs_endperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_endperiod<>2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate < cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
									0)
								)
						  )
						) || ',' ||
						nvl(
						(select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
						pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
						   (
							  nvl((select to_char(abs_startperiod)||trim(ab_code) from PER_ABSENTHIS pa where rownum=1 and pa.PER_ID=wt.PER_ID and abs_startperiod=2 and trim(ab_code) not in ('10','13')and 
									pa.abs_startdate = cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19)) and pa.abs_enddate > cast(to_char(wt.WORK_DATE,'YYYY-MM-DD') as char(19))),
								  0
								)
						  )
						)
		
					  )
		   end ABSENT,ctl.CLOSE_DATE
				
				from PER_WORK_TIME wt 
				left join PER_WORK_CYCLE cl on(cl.WC_CODE=wt.WC_CODE) 
				left join PER_WORK_TIME_CONTROL ctl on(ctl.CONTROL_ID=wt.CONTROL_ID) 
				WHERE wt.PER_ID =$PER_ID  
				$condition
			   order by  wt.WORK_DATE ASC ";
	
	//echo "<pre>".$cmd; die();
  

	$count_data = $db_dpis4->send_cmd($cmd);
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
		while($data4 = $db_dpis4->get_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			
			$arr_data[] = $data_row;  
			
			$DATA_WORK_DATE = show_date_format($data4[WORK_DATE], $DATE_DISPLAY);
        	$arr_data[] = $DATA_WORK_DATE;  
        
        
			if($data4[APV_ENTTIME]){
				$DATA_ENTTIME = $data4[APV_ENTTIME];
			}else{
				//$DATA_ENTTIME = $data4[SCAN_ENTTIME];
				$DATA_ENTTIME = "";
			}
			$arr_data[] = $DATA_ENTTIME;  
			
			
			if($data4[APV_EXITTIME]){
				$DATA_EXITTIME = $data4[APV_EXITTIME];
			}else{
				//$DATA_EXITTIME = $data4[SCAN_EXITTIME];
				$DATA_EXITTIME = "";
			}
			$arr_data[] = $DATA_EXITTIME;  
			
			$DATA_WC_NAME = $data4[WC_NAME];
			$arr_data[] = $DATA_WC_NAME;  
			
			
			$ARR_ABSENT = explode(",", $data4[ABSENT]);
			$DATA_WORK_FLAG = "";
			if($data4[CLOSE_DATE]){
				if($data4[WORK_FLAG]==1){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = $data4[REMARK];
					}else{
						$DATA_WORK_FLAG = "สาย";
					}
					
					$HIDLATE1++;
				}else if($data4[WORK_FLAG]==2){
					// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
					/*if($data4[ABSENT_FLAG] !=0){
						$DATA_WORK_FLAG = "";
					}else{
						$DATA_WORK_FLAG = "ขาดราชการ";
					}*/
					if($data4[ABSENT_FLAG] !=0){
						
						if(substr($ARR_ABSENT[0],0,1)==0 || substr($ARR_ABSENT[1],0,1)==0){
							$DATA_WORK_FLAG = "ขาดราชการ";
						}else{
							$DATA_WORK_FLAG = "";
						} 
						
					}else{
						$DATA_WORK_FLAG = "ขาดราชการ";
					}
				}else if($data4[WORK_FLAG]==3){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = "ปกติ";
					}else{
						$DATA_WORK_FLAG = "ออกก่อน";
					}
				}else if($data4[WORK_FLAG]==4){
					if($data4[REMARK]){
						$DATA_WORK_FLAG = "ปกติ";
					}else{
						$DATA_WORK_FLAG = "ไม่ได้ลงเวลา";
					}
					
				}else if($data4[WORK_FLAG]==0){
					$DATA_WORK_FLAG = "ปกติ";
				}else if($data4[WORK_FLAG]==5){
                	$DATA_WORK_FLAG = "ปกติ";
				}
			}
			$arr_data[] = $DATA_WORK_FLAG; 
			
			
			$DATA_ABSENT = "";
			
		
			$DATA_AB_NAME = "";
			$DATA_AB_NAME_AFTERNOON = '';
			
			if(substr($ARR_ABSENT[0],0,1)==1 || substr($ARR_ABSENT[0],0,1)==2 || substr($ARR_ABSENT[0],0,1)==3){
				if(substr($ARR_ABSENT[0],-2) != '10' && substr($ARR_ABSENT[0],-2) != '13'){
					$cmd_AB ="select AB_NAME
					from PER_ABSENTTYPE
					where AB_CODE = ".substr($ARR_ABSENT[0],-2);
					//echo $cmd_AB; die();
					$db_dpis_AB->send_cmd($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME = $data_AB_NAME[AB_NAME];
				}
			}
			if(substr($ARR_ABSENT[1],0,1)==2){
				if(substr($ARR_ABSENT[1],-2) != '10' && substr($ARR_ABSENT[1],-2) != '13'){
					$cmd_AB ="select AB_NAME
					from PER_ABSENTTYPE
					where AB_CODE = ".substr($ARR_ABSENT[1],-2);
					//echo $cmd_AB; die();
					$db_dpis_AB->send_cmd($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME_AFTERNOON = $data_AB_NAME[AB_NAME];
				}
			}
			
			if($data4[ABSENT] !='0,0'){
				if(substr($ARR_ABSENT[0],-2)==10 || substr($ARR_ABSENT[0],-2)==13){
						$DATA_ABSENT = $DATA_AB_NAME;
				} 
				else {
					if(substr($ARR_ABSENT[0],0,1)==3){
							$DATA_ABSENT = $DATA_AB_NAME." (ทั้งวัน)";
					} 
					else {
						if(substr($ARR_ABSENT[0],0,1)==1){
								$DATA_ABSENT = $DATA_AB_NAME." (ครึ่งเช้า)";
						} 
						if(substr($ARR_ABSENT[1],0,1)==2){
							if(substr($ARR_ABSENT[0],0,1)==1)
								$DATA_ABSENT .= ',';
							$DATA_ABSENT .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
						} 
					}
				}
			}
			
			$arr_data[] = $DATA_ABSENT; 
			
			
			
			$DATA_HOLIDAY = "";
			if($data4[HOLIDAY_FLAG]==1){
				$DATA_HOLIDAY = "วันหยุด";
			}
			$arr_data[] = $DATA_HOLIDAY; 
			
			$DATA_REMARK = "";
			if($data4[REMARK]){
				$DATA_REMARK = $data4[REMARK];
			}
			$arr_data[] = $DATA_REMARK; 
			
			

			$data_align = array("C", "C", "C", "C", "L", "C", "C", "L", "L");
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
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
                  }
		$data_align = array("C", "C", "C","C", "C","C", "C","C","C");
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