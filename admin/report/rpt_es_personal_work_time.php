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
		$orientation='L';
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
	$orientation='L';
	$report_title = "ข้อมูลประมวลผลการลงเวลาเบื้องต้น||$PER_NAME";
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
		$heading_width[0] = "17";
		$heading_width[1] = "30";
		$heading_width[2] = "17";
		$heading_width[3] = "17";
		$heading_width[4] = "42";
		$heading_width[5] = "54";
		$heading_width[6] = "55";
		$heading_width[7] = "56";
		//$heading_width[8] = "53";
	}else{
		$heading_width[0] = "17";
		$heading_width[1] = "30";
		$heading_width[2] = "17";
		$heading_width[3] = "17";
		$heading_width[4] = "42";
		$heading_width[5] = "54";
		$heading_width[6] = "55";
		$heading_width[7] = "56";
		//$heading_width[8] = "53";
	}	
	$heading_text[0] = "$SEQ_NO_TITLE";
	$heading_text[1] = "วันที่";
	$heading_text[2] = "เวลาเข้า";
	$heading_text[3] = "เวลาออก";
	$heading_text[4] = "รอบเวลา";
	$heading_text[5] = "สถานะการลงเวลา";
	$heading_text[6] = "สถานะการลา";
	$heading_text[7] = "สถานะวัน/คำร้อง";
	//$heading_text[8] = "คำร้อง";
		
	$heading_align = array('C','C','C','C','C','C','C','C');
	
	$cmd_min = "select  to_char(greatest(
			nvl((select trunc(min(TIME_STAMP),'MONTH') from PER_TIME_ATTENDANCE where per_id=$PER_ID), trunc(sysdate)-61),
			nvl((select last_day(to_date(to_char((close_year-543))||to_char(close_month,'00')||'01','YYYYMMDD'))+1 xx
				from (select * from(select close_year, close_month from per_work_time_control where  APPROVE_DATE is not null
				and DEPARTMENT_ID =(select ORG_ID from per_personal where per_id=$PER_ID)
		  order by close_year desc , close_month desc) where rownum=1) x
		), trunc(sysdate)-61)
	),'yyyy-mm-dd hh24:mi:ss') y from dual" ; 
				
	$db_dpis->send_cmd_fast($cmd_min);
	$data_min = $db_dpis->get_array_array();
	$TIME_STAMP_min = $data_min[0];
	
	$xx = $TIME_STAMP_min;
	$yy = date("Y-m-d")." 23:59:59";
	$cmd = file_get_contents('../../admin/GetWorkTimeByPerID.sql');	

	$cmd_con = " SELECT CONFIG_VALUE FROM SYSTEM_CONFIG  where config_name = 'P_SCANTYPE' ";
	$db_dpis->send_cmd_fast($cmd_con);
	$data_con = $db_dpis->get_array_array();
	$SCANTYPE = $data_con[CONFIG_VALUE];
			
	$cmd=str_ireplace(":PER_ID",$PER_ID,$cmd);
	$cmd=str_ireplace(":BEGINDATEAT","'".$xx."'",$cmd);
	$cmd=str_ireplace(":TODATEAT","'".$yy."'",$cmd);
	$cmd=str_ireplace(":SCANTYPE",$SCANTYPE,$cmd);
	$cmd=$cmd." desc ";
	
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
		while($data4 = $db_dpis4->get_array_array()){
			$data_count++;
			$data_row++;			

			$arr_data = (array) null;
			
			$arr_data[] = $data_row;  
			
			$DATA_WORK_DATE = show_date_format($data4[WORK_DATE], $DATE_DISPLAY);
			
			
			$DATA_ENTTIME = $data4[ENTTIME];
			 
			
			$DATA_EXITTIME = $data4[EXITTIME];
			
			 
			
			$cmd = " select WC_NAME FROM PER_WORK_CYCLE WHERE WC_CODE=".$data4[WC_CODE];       
			$db_dpis3->send_cmd_fast($cmd);
			$data_cy = $db_dpis3->get_array_array();
			$DATA_LATE_TIME = "";
			if($data4[LATE_TIME]){
				$DATA_LATE_TIME = "+เวลาพิเศษ ".substr($data4[LATE_TIME],0,2).":".substr($data4[LATE_TIME],2,2);
			}
			$DATA_WC_NAME=$data_cy[WC_NAME].$DATA_LATE_TIME;
			 
			
			
			
			$DATA_WORK_FLAG = "";
			if($data4[WORK_FLAG]==1){
				if($data4[REMARK]){
					$DATA_WORK_FLAG = $data4[REMARK];
				}else{
					$DATA_WORK_FLAG = "สาย";
				}
				$HIDLATE1++;
			}else if($data4[WORK_FLAG]==2){
				$cmd = " select cl.END_LATETIME
                            FROM  PER_WORK_CYCLEHIS cyh
                            left join PER_WORK_CYCLE cl on(cl.WC_CODE=cyh.WC_CODE) 
                            WHERE cyh.PER_ID =$PER_ID 
                            AND 
                            (sysdate between to_date(cyh.START_DATE,'yyyy-mm-dd hh24:mi:ss') AND case 
							when cyh.END_DATE is not null then to_date(cyh.END_DATE,'yyyy-mm-dd hh24:mi:ss') 
                            else sysdate end ) ";
				$db_dpis->send_cmd($cmd);
				$data_today = $db_dpis->get_array();
				$TODAY_END_LATETIME = trim($data_today[END_LATETIME]); // สิ้นสุดเวลาสาย
				
				if($TODAY_END_LATETIME){
					$WORK_NOW = date("Y-m-d"); // วันที่ปัจจุบัน
					$TODAY_TIME_NOW = date("Hi"); // เวลาปัจจุบัน
					// 0 = ขาด, 2 = ลาบ่าย, 10= ลาเช้า, 12= ลาเช้าและลาบ่าย, 30 = ลาทั้งวัน
					if($data4[ABSENT] !="0,0" && $data4[ABSENT] != "313,0"){
					   if($WORK_NOW==$data4[WORK_DATE]){ 
							if($TODAY_TIME_NOW > $TODAY_END_LATETIME){   
								$DATA_WORK_FLAG = "ขาดราชการ";
							}else{
								$DATA_WORK_FLAG = "";
							}
					   }else{
							$DATA_WORK_FLAG = "ขาดราชการ";
					   }
						
					}else{
						if($WORK_NOW==$data4[WORK_DATE]){ 
							if($TODAY_TIME_NOW > $TODAY_END_LATETIME){   
								$DATA_WORK_FLAG = "ขาดราชการ";
							}else{
								$DATA_WORK_FLAG = "";
							}
					   }else{
							$DATA_WORK_FLAG = "ขาดราชการ";
					   }
					}
					
				 }else{
					$DATA_WORK_FLAG = "ขาดราชการ";
				 }
			}else if($data4[WORK_FLAG]==3){
				if($data4[REMARK]){
					$DATA_WORK_FLAG = $data4[REMARK];
				}else{
					$DATA_WORK_FLAG = "ออกก่อน";
				}
				
			}else if($data4[WORK_FLAG]==4){
				$WORK_NOW = date("Y-m-d");
				if($data4[WC_CODE]=="-1" && $WORK_NOW==$data4[WORK_DATE]){ 
					$DATA_WORK_FLAG = "";
				}else{
					if($data4[REMARK]){
						$DATA_WORK_FLAG = $data4[REMARK];
					}else{
						$DATA_WORK_FLAG = "ไม่ได้ลงเวลา";
					}
					
				}
			}else if($data4[WORK_FLAG]==0){
				if($SCANTYPE=="2" && date("Y-m-d")==$data4[WORK_DATE]){ 
					$DATA_EXITTIME = "";
				}
				$DATA_WORK_FLAG = "ปกติ";
			}else if($data4[WORK_FLAG]==5){
				$DATA_WORK_FLAG = $data4[REMARK];
			}
			
			
			
			$DATA_ABSENT = "";
			$ARR_ABSENT = explode(",", $data4[ABSENT]);
		
			$DATA_AB_NAME = "";
			$DATA_AB_NAME_AFTERNOON = '';
			
			if(substr($ARR_ABSENT[0],0,1)==1 || substr($ARR_ABSENT[0],0,1)==2 || substr($ARR_ABSENT[0],0,1)==3){
				if(substr($ARR_ABSENT[0],-2) != '10' && substr($ARR_ABSENT[0],-2) != '13'){
					$cmd_AB ="select AB_NAME
					from PER_ABSENTTYPE
					where AB_CODE = ".substr($ARR_ABSENT[0],-2);
					//echo $cmd_AB; die();
					$db_dpis_AB->send_cmd_fast($cmd_AB);
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
					$db_dpis_AB->send_cmd_fast($cmd_AB);
					$data_AB_NAME = $db_dpis_AB->get_array_array();
					$DATA_AB_NAME_AFTERNOON = $data_AB_NAME[AB_NAME];
				}
			}
			
			$dbAbsent = "";
			
			if($data4[ABSENT] !='0,0'){
				if(substr($ARR_ABSENT[0],-2)==10 || substr($ARR_ABSENT[0],-2)==13){
						$dbAbsent = $DATA_AB_NAME;
				}else{
					if(substr($ARR_ABSENT[0],0,1)==3){
						$dbAbsent = $DATA_AB_NAME." (ทั้งวัน)";
					}elseif(substr($ARR_ABSENT[0],0,1)==1){
						$dbAbsent = $DATA_AB_NAME." (ครึ่งเช้า)";
						if(substr($ARR_ABSENT[1],0,1)==2){
							if(substr($ARR_ABSENT[0],0,1)==1){
								$dbAbsent .= ',';
								$dbAbsent .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
							}
						}
					 }elseif(substr($ARR_ABSENT[0],0,1)==2){
						$dbAbsent = $DATA_AB_NAME." (ครึ่งบ่าย)";
					}elseif(substr($ARR_ABSENT[0],0,1)==0){
						if(substr($ARR_ABSENT[1],0,1)==2){
							$dbAbsent .= $DATA_AB_NAME_AFTERNOON." (ครึ่งบ่าย)";
						}
					}
				 }
			}
			
			 
			
			$DATA_HOLIDAY = "";
			if($data4[HOLIDAY]==1){
				$DATA_HOLIDAY = "วันหยุด ";
			}
			
			
			/*คำร้อง*/
        
			 $cmd ="select START_FLAG,START_TIME,END_FLAG,END_TIME,
						MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE,
						REQ_FLAG,REQ_TIME,REQUEST_NOTE,REQ_SPEC,
						REQ_SPEC_NOTE
						from TA_REQUESTTIME
						where PER_ID = $PER_ID AND APPROVE_FLAG=1 AND REQUEST_DATE='".$data4[WORK_DATE]."'";
			$db_dpis_AB->send_cmd($cmd);
			$data_AB = $db_dpis_AB->get_array();
			$Detail_type = "";
			if($data_AB[START_FLAG]==1){
				$Detail_type = "ขอลงเวลาเข้า (".substr($data_AB[START_TIME],0,2).":".substr($data_AB[START_TIME],2,2)." น.) ";
			}
			
			$Detail_type1="";
			if($data_AB[END_FLAG]==1){
				$Detail_type1 = $Detail_type1."ขอลงเวลาออก (".substr($data_AB[END_TIME],0,2).":".substr($data_AB[END_TIME],2,2)." น.) ";
			} 
			
			$Detail_type11=""; 
		   if($data_AB[MEETING_FLAG]==1){
				$Detail_type11 = $Detail_type11."ติดประชุม/สัมมนา/อบรม ";
			}
			
			if($data_AB[SCAN_FLAG]==1){
				$Detail_type11 = $Detail_type11."ลืมสแกน ";
			}
			
			if($data_AB[REQUEST_NOTE]==1){
				$Detail_type11 = $Detail_type11."ลาชั่วโมง ";
			}
			
			if($data_AB[REQ_TIME]==1){
				$Detail_type11 = $Detail_type11."ไปปฏิบัติราชการ ";
			}
			
			if($data_AB[REQ_SPEC]==1){
				$Detail_type11 = $Detail_type11." ".$data_AB[REQ_SPEC_NOTE]."";
			}

			if($data_AB[OTH_FLAG]==1){
				$Detail_type11 = $Detail_type11." ".$data_AB[OTH_NOTE]." ";
			
			}
				
			$Detail_type2="";
			
			
			 /*เช็คพ้นจากราชการแล้วหรือยัง*/
        
		   $cmd ="select PER_POSDATE
						from PER_PERSONAL
						where PER_ID = $PER_ID and PER_STATUS=2 ";
			$db_dpis_AB->send_cmd($cmd);
			$data_AB = $db_dpis_AB->get_array();
			
			if($data_AB[PER_POSDATE]){
				 if(substr($data4[WORK_DATE],0,4).substr($data4[WORK_DATE],5,2).substr($data4[WORK_DATE],8,2) >= substr($data_AB[PER_POSDATE],0,4).substr($data_AB[PER_POSDATE],5,2).substr($data_AB[PER_POSDATE],8,2)){
					$DATA_WORK_FLAG = "";
				 }
			}
			
			
			
			
			$arr_data[] = $DATA_WORK_DATE;  
			$arr_data[] = $DATA_ENTTIME; 
			$arr_data[] = $DATA_EXITTIME; 
			$arr_data[] = $DATA_WC_NAME; 
			$arr_data[] = $DATA_WORK_FLAG;  
			$arr_data[] = $dbAbsent; 
			$arr_data[] = $DATA_HOLIDAY.$Detail_type.$Detail_type1.$Detail_type11.$Detail_type2;  
			
			

			$data_align = array("C", "C", "C", "C", "L", "C", "C", "L");
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
		$data_align = array("C", "C", "C","C", "C","C", "C","C");
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