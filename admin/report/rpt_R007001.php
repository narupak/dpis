<?php
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
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
	//ini_set("max_execution_time", $max_execution_time); //เดิม
        ini_set("max_execution_time", 0); // pitak 0=ไม่จำกัดเวลา ทำจนกว่าจะเสร็จ
        
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if($search_per_type==1){ 
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$order_posno_name = "b.POS_NO_NAME";
		$order_posno = "b.POS_NO";
	}elseif($search_per_type==2){ 
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$order_posno_name = "b.POEM_NO_NAME";
		$order_posno = "b.POEM_NO";
	}elseif($search_per_type==3){ 
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$order_posno_name = "b.POEMS_NO_NAME";
		$order_posno = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$order_posno_name = "b.POT_NO_NAME";
		$order_posno = "b.POT_NO";
	} // end if
	
	$search_condition = "";
	$arr_search_condition[] = "(a.PER_TYPE = $search_per_type)";
	$arr_search_condition[] = "(a.PER_STATUS = 1)";

	if(trim($search_date_min)){
		$arr_temp = explode("/", $search_date_min);
		$search_date_min = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_min = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if

	if(trim($search_date_max)){
		$arr_temp = explode("/", $search_date_max);
		$search_date_max = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
		$show_date_max = ($arr_temp[0] + 0) ." ". $month_full[($arr_temp[1] + 0)][TH] ." ". $arr_temp[2];
	} // end if
        
        function CheckPublicHoliday($YYYY_MM_DD){
            global $DPISDB,$db_dpis;
            if($DPISDB=="odbc"){ 
                $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
            }elseif($DPISDB=="oci8"){
                $cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$YYYY_MM_DD' ";
            }elseif($DPISDB=="mysql"){
                $cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$YYYY_MM_DD' ";
            }    
            $IS_HOLIDAY = $db_dpis->send_cmd($cmd);
            if(!$IS_HOLIDAY){
                return false;
            }else{
                return true;
            }
	}
        //---------------------------------------------นับวันทำการ-----------------------------------------------------------
        if(trim($search_date_min) && trim($search_date_max)){
            $strStartDate = $search_date_min;//"2011-08-01";
            $strEndDate = $search_date_max;//"2011-08-15";
            //echo $strStartDate.'==='.$strStartDate;
            $intWorkDay = 0;
            $intHoliday = 0;
            $intPublicHoliday = 0;
            $intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate))/  ( 60 * 60 * 24 )) + 1; 
            
            while (strtotime($strStartDate) <= strtotime($strEndDate)) {
                $DayOfWeek = date("w", strtotime($strStartDate));
                if($DayOfWeek == 0 or $DayOfWeek ==6){  // 0 = Sunday, 6 = Saturday;
                        $intHoliday++;
                        //echo "$strStartDate = <font color=red>Holiday</font><br>";
                }elseif(CheckPublicHoliday($strStartDate)){
                        $intPublicHoliday++;
                        //echo "$strStartDate = <font color=orange>Public Holiday</font><br>";
                }else{
                        $intWorkDay++;
                        //echo "$strStartDate = <b>Work Day</b><br>";
                }
                //$DayOfWeek = date("l", strtotime($strStartDate)); // return Sunday, Monday,Tuesday....
                $strStartDate = date ("Y-m-d", strtotime("+1 day", strtotime($strStartDate)));
            }
        }else{
            $intWorkDay='';
        }
        $WORK_DAY=$intWorkDay;
        //--------------------------------------------------------------------------------------------------------
        //echo $WORK_DAY.'<br>';
	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
            $list_type_text = "";
            if($select_org_structure==0) {
             
                if(trim($search_org_id)){ 
                    $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
                    $list_type_text .= "$search_org_name";
                    $R_ORG_ID = "b.ORG_ID";
                } // end if
                if(trim($search_org_id_1)){ 
                    $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
                    $list_type_text .= "$search_org_name_1";
                    $R_ORG_ID = "b.ORG_ID_1";
                } // end if
                if(trim($search_org_id_2)){ 
                    $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
                    $list_type_text .= "$search_org_name_2";
                    $R_ORG_ID = "b.ORG_ID_2";
                } // end if
                if(trim($search_org_id_3)){ 
                    $arr_search_condition[] = "(b.ORG_ID_3 = $search_org_id_3)";
                    $list_type_text .= "$search_org_name_3";
                    $R_ORG_ID = "b.ORG_ID_3";
                } // end if
                if(trim($search_org_id_4)){ 
                    $arr_search_condition[] = "(b.ORG_ID_4 = $search_org_id_4)";
                    $list_type_text .= "$search_org_name_4";
                    $R_ORG_ID = "b.ORG_ID_4";
                } // end if
                if(trim($search_org_id_5)){ 
                    $arr_search_condition[] = "(b.ORG_ID_5 = $search_org_id_5)";
                    $list_type_text .= "$search_org_name_5";
                    $R_ORG_ID = "b.ORG_ID_5";
                } // end if
            }else{
                if(trim($search_org_ass_id)){ 
                    $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
                    $list_type_text .= "$search_org_ass_name";
                    $R_ORG_ID = "a.ORG_ID";
                } // end if
                if(trim($search_org_ass_id_1)){ 
                    $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_ass_id_1)";
                    $list_type_text .= "$search_org_ass_name_1";
                    $R_ORG_ID = "a.ORG_ID_1";
                } // end if
                if(trim($search_org_ass_id_2)){ 
                    $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_ass_id_2)";
                    $list_type_text .= "$search_org_ass_name_2";
                    $R_ORG_ID = "a.ORG_ID_2";
                } // end if
                if(trim($search_org_ass_id_3)){ 
                    $arr_search_condition[] = "(a.ORG_ID_3 = $search_org_ass_id_3)";
                    $list_type_text .= "$search_org_ass_name_3";
                    $R_ORG_ID = "a.ORG_ID_3";
                } // end if
                if(trim($search_org_ass_id_4)){ 
                    $arr_search_condition[] = "(a.ORG_ID_4 = $search_org_ass_id_4)";
                    $list_type_text .= "$search_org_ass_name_4";
                    $R_ORG_ID = "a.ORG_ID_4";
                } // end if
                if(trim($search_org_ass_id_5)){ 
                    $arr_search_condition[] = "(a.ORG_ID_5 = $search_org_ass_id_5)";
                    $list_type_text .= "$search_org_ass_name_5";
                    $R_ORG_ID = "a.ORG_ID_5";
                } // end if
            }
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
			$list_type_text .= " - $MINISTRY_NAME - $DEPARTMENT_NAME";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1 || $PER_AUDIT_FLAG==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
			$list_type_text .= " - $MINISTRY_NAME";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(c.PV_CODE) = '$PROVINCE_CODE')";
			$list_type_text .= " - $PROVINCE_NAME";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

    $company_name = "รูปแบบการออกรายงาน : ". ($select_org_structure==1 || $PER_AUDIT_FLAG==1 ?"โครงสร้างตามมอบหมายงาน - ":"โครงสร้างตามกฎหมาย - ") ."$list_type_text";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"")."||รวมวันมาปฏิบัติราชการ $WORK_DAY วันทำการ";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0701";
	include ("rpt_R007001_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
//		$sum_w = array_sum($heading_width);
		$sum_w = 0;
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
				$sum_w += $heading_width[$h];
		}
		for($h = 0; $h < count($heading_width); $h++) {
			if ($arr_column_sel[$h]==1)
			$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
		}

		$fname= "rpt_R007001_rtf.rtf";

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
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='L';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
	//	$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	function check_is_holiday($SELECT_YEAR,$SELECT_MONTH,$SELECT_DAY){
		global $DPISDB,$db_dpis3;
		
		if($DPISDB=="odbc") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="oci8") 
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		elseif($DPISDB=="mysql")
			$cmd_is_holiday = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$SELECT_YEAR-$SELECT_MONTH-$SELECT_DAY' ";
		$IS_HOLIDAY = $db_dpis3->send_cmd($cmd_is_holiday);
				
	return $IS_HOLIDAY;
	}

	function count_abs_days($AB_COUNT,$ABS_STARTDATE,$ABS_ENDDATE){
		global $search_month,$SHOW_MONTH,$search_year, $BKK_FLAG;
		
		$arr_temp = explode("-", $ABS_STARTDATE);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		
		$arr_temp = explode("-", $ABS_ENDDATE);
		$END_DAY = $arr_temp[2];
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[0];
		
		while($START_YEAR!=$END_YEAR || $START_MONTH!=$END_MONTH || $START_DAY!=$END_DAY){		//<<<<<<<<<<<<<<<<<<<<
			if($AB_COUNT==2){	//ไม่นับ = เสาร์อาทิตย์ และวันหยุดราขการ		//Numeric representation of the day of the week (0=sunday and 6=sat day)
				$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
				$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,$START_DAY);
				if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
			}else if($AB_COUNT==1){	//นับ = เลือกหยุดเสาร์อาทิตย์ และวันหยุดราชการได้
				$ABS_DAY++;
			}
//			echo "AB_COUNT=($AB_COUNT) , $START_YEAR-$START_MONTH-$START_DAY--->ABS_DAY=$ABS_DAY<br>";
			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
			$arr_temp = explode("-", $TMP_STARTDATE);
			$START_DAY = $arr_temp[2];
			$START_MONTH = $arr_temp[1];
			$START_YEAR = $arr_temp[0];
		} //end while
		if($AB_COUNT==2){	//ไม่นับ = เสาร์อาทิตย์ และวันหยุดราขการ		//Numeric representation of the day of the week (0=sunday and 6=sat day)
			$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
			$IS_HOLIDAY = check_is_holiday($START_YEAR,$START_MONTH,$START_DAY);
			if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY)	$ABS_DAY++;
		}else if($AB_COUNT==1){	//นับ = เลือกหยุดเสาร์อาทิตย์ และวันหยุดราชการได้
			$ABS_DAY++;
		}
		
		return $ABS_DAY;
	}

	function count_absent($PER_ID){
		global $DPISDB, $db_dpis2;
		global $arr_content, $data_count,$R_ORG_ID;
		global $search_year,$search_date_min, $search_date_max, $SHOW_MONTH, $BKK_FLAG;
                global $NUMBER_DISPLAY;
		$MONTH_TH_arr = array('','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.');
//		echo "search_date_min=$search_date_min , search_date_max=$search_date_max<br>";
		$search_condition = "";
		unset($arr_search_condition);
		if($DPISDB=="odbc" || $DPISDB=="mysql")		$column_startdate="LEFT(ABS_STARTDATE, 10)";				$column_enddate="LEFT(ABS_ENDDATE, 10)";
		if($DPISDB=="oci8")												
                    $column_startdate="SUBSTR(ABS_STARTDATE, 1, 10)";		
                $column_enddate="SUBSTR(ABS_ENDDATE, 1, 10)";
		
		
//		$arr_search_condition[] =  "(($column_startdate < '$search_date_min' OR $column_startdate >= '$search_date_min') AND $column_startdate <= '$search_date_max' AND $column_enddate >= '$search_date_min')";
                $arr_srch_cond = (array) null;
		$arr_srch_cond[] =  "($column_startdate <= '$search_date_min' AND $column_enddate >= '$search_date_max')";	// กรณีวันในฐานข้อมูลค่อมวันที่เลือก
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันเริ่มและวันถึงอยู่ในช่วงที่เลือก 
		$arr_srch_cond[] =  "($column_enddate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันถึงอยู่ในช่วง
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_startdate <= '$search_date_max')";	// กรณีวันเริ่มอยู่ในช่วงที่เลือก 
		$arr_search_condition[] = "(".implode(" OR ",$arr_srch_cond).")";
				
		if(count($arr_search_condition)) $search_condition = " AND ". implode(" AND ", $arr_search_condition);

                
               /* if(!empty($search_date_min) && !empty($search_date_max)){
                    $search_condition = " AND (".
                                        "'$search_date_min' >= ABS_STARTDATE AND '$search_date_min' <= ABS_ENDDATE OR " .
                                        "'$search_date_max' >= ABS_STARTDATE AND '$search_date_max' <= ABS_ENDDATE OR " .
                                        "ABS_STARTDATE >= '$search_date_min' AND ABS_STARTDATE <= '$search_date_max"."9' OR " .
                                        "ABS_ENDDATE >= '$search_date_min' AND ABS_ENDDATE <= '$search_date_max"."9')";
                }*/

//die($search_condition);
		if ($BKK_FLAG==1) 
			$ab_code = " '1', '3', '6', '12' ";
		else
			$ab_code = " '01', '03', '04', '10' ";
		$arr_ab_code = explode(",",$ab_code);

		for($i=0; $i < count($arr_ab_code); $i++){
                    
			$cmd = " select		a.AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD,b.AB_COUNT 
							 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b 
							 where	PER_ID=$PER_ID and a.AB_CODE =$arr_ab_code[$i] and a.AB_CODE=b.AB_CODE
											$search_condition
							 order by ABS_STARTDATE ";
                        
			/*$cmd = " select x.*,b.AB_COUNT  from  (select a.AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD 
				 from 		PER_ABSENTHIS a
				 where	PER_ID=$PER_ID and a.AB_CODE =$arr_ab_code[$i] 
								$search_condition
                                  ) x, PER_ABSENTTYPE b  
                                                      where x.AB_CODE=b.AB_CODE
        			 order by ABS_STARTDATE ";*/

	//die('<pre>'.$cmd);
			$cnt_nabs = $db_dpis2->send_cmd($cmd);
//			echo "normal abs :  $cmd ($cnt_nabs)<br>";
	//		$db_dpis2->show_error();
                        $TXT_ABS_CODE = '';
			while($data2 = $db_dpis2->get_array()){		//	$data2 = $db_dpis2->get_array();
				$ABS_ID = trim($data2[ABS_ID]);
				$AB_CODE = trim($data2[AB_CODE]);
				if ($BKK_FLAG==1) {
					if ($AB_CODE=="1") $AB_CODE = "01";
					elseif ($AB_CODE=="3") $AB_CODE = "03";
					elseif ($AB_CODE=="6") $AB_CODE = "04";
					elseif ($AB_CODE=="12") $AB_CODE = "10";
	//				elseif ($AB_CODE=="5") $AB_CODE = "11";
				}
				$AB_COUNT = trim($data2[AB_COUNT]);
				$ABS_DAY = $data2[ABS_DAY];
				$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
                                $ABS_STARTDATE_CHECK = substr($data2[ABS_STARTDATE], 0, 10);
				if ($ABS_STARTDATE < $search_date_min)  {
					$ABS_STARTDATE = $search_date_min;
					$ABS_STARTPERIOD = 3;			// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
				} else 
					$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
				$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
				if ($ABS_ENDDATE > $search_date_max) {
					$ABS_ENDDATE = $search_date_max;
					$ABS_ENDPERIOD = 3;				// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
				} else 
					$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
				
				$arr_temp = explode("-", $ABS_STARTDATE);
				$START_DATE = $arr_temp[2]+0;
				$START_MONTH = $arr_temp[1]+0;
				$START_YEAR = $arr_temp[0] - 543;			//???????????/
				if($ABS_STARTPERIOD==1) $START_DATE .= " ช";			//ครึ่งวัน เช้า
				elseif($ABS_STARTPERIOD==2) $START_DATE .= " บ";	//ครึ่งวัน บ่าย
				
				$arr_temp = explode("-", $ABS_ENDDATE);
				$END_DATE = $arr_temp[2]+0;
				$END_MONTH = $arr_temp[1]+0;
				$END_YEAR = $arr_temp[0] - 543;			//???????????/
				if($ABS_ENDPERIOD==1) $END_DATE .= " ช";				//ครึ่งวัน เช้า
				elseif($ABS_ENDPERIOD==2) $END_DATE .= " บ";		//ครึ่งวัน เช้า
				
				if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION[$AB_CODE] = $START_DATE;
				elseif($START_MONTH == $END_MONTH) $ABS_DURATION[$AB_CODE] = $START_DATE ."-". $END_DATE;
				else $ABS_DURATION[$AB_CODE] = "$START_DATE/$START_MONTH-$END_DATE/$END_MONTH";	//เดือนเริ่ม กับ เดือนสิ้นสุด คนละเดือนกัน
	
				$AB_CODE_EXIST .= $AB_CODE.",";
				$ABS_ID_EXIST .= $ABS_ID.",";
				
				$ABS_DURATION[$AB_CODE] = $ABS_DURATION[$AB_CODE]?$ABS_DURATION[$AB_CODE]:"";
				$ABS_DURATION_EXIST[$AB_CODE] .= $ABS_DURATION[$AB_CODE].","; 
				
				$arr_content[$data_count][AB_CODE] = $AB_CODE_EXIST;
				$arr_content[$data_count][("ABS_ID_".$AB_CODE)] = substr($ABS_ID_EXIST,0,-1);
				$arr_content[$data_count][("DATE_".$AB_CODE)] = substr($ABS_DURATION_EXIST[$AB_CODE],0,-1);			//.= (($arr_content[$data_count][("DATE_".$AB_CODE)])?",":"") . $ABS_DURATION;
				
				$START_DATE_CNT = ($search_date_min > $ABS_STARTDATE ? $search_date_min : $ABS_STARTDATE);
				$END_DATE_CNT = ($search_date_max < $ABS_ENDDATE ? $search_date_max : $ABS_ENDDATE);
				//--------------------------------------------------------------------------------------------------------------
				$ABS_DAY_EXIST[$AB_CODE] += count_abs_days($AB_COUNT,$START_DATE_CNT,$END_DATE_CNT);		//ตรวจสอบการลานับวันหยุด เสาร์อาทิตย์
//				echo "1..day=".$ABS_DAY_EXIST[$AB_CODE]."($ABS_STARTDATE, $ABS_STARTPERIOD, $ABS_ENDDATE, $ABS_ENDPERIOD, $AB_CODE)<br>";
				// เอาครึ่งวันไปหัก
				if(($ABS_STARTPERIOD==1 || $ABS_STARTPERIOD==2) || ($ABS_ENDPERIOD==1 || $ABS_ENDPERIOD==2)){//สำหรับลา ครึงวันเช้าครึ่งวันบ่าย
					$ABS_DAY_EXIST[$AB_CODE] = ($ABS_DAY_EXIST[$AB_CODE] - 0.5);
				}
//				echo "2..day=".$ABS_DAY_EXIST[$AB_CODE]."($ABS_STARTDATE, $ABS_STARTPERIOD, $ABS_ENDDATE, $ABS_ENDPERIOD, $AB_CODE)<br>";
				//--------------------------------------------------------------------------------------------------------------
				$arr_content[$data_count][("DAY_".$AB_CODE)] = $ABS_DAY_EXIST[$AB_CODE];		//+= $ABS_DAY;		= $ABS_DAY?$ABS_DAY:"-";
                                if($AB_CODE!=$TXT_ABS_CODE){
                                    $TXT_ABS_CODE = $AB_CODE;
                                    $PLUS = 0;
                                }
                                if($ABS_STARTDATE_CHECK>=$search_date_min){
                                    $PLUS += 1;
                                }  
                                
                                $arr_content[$data_count][("NUM_".$AB_CODE)] = $PLUS; // เดิม แก้เรื่องนับครั้ง คร่อมวันลา $arr_content[$data_count][("NUM_".$AB_CODE)] += 1; //${"NUM_".$AB_CODE."_EXIST"};
                                
                                //$arr_content[$data_count][("DATE_".$AB_CODE)] =$data2[ABS_STARTDATE]."-".$data2[ABS_ENDDATE].",";
                                //$arr_content[$data_count][("STARTDATE_".$AB_CODE)] = $data2[ABS_STARTDATE];
                                //$arr_content[$data_count][("ENDDATE_".$AB_CODE)] = $data2[ABS_ENDDATE];
                                
                                $arr_begindate = explode("-", trim($data2[ABS_STARTDATE])); //2016-01-01
                                $dd = number_format($arr_begindate[2]);
                                $mm = $MONTH_TH_arr[intval($arr_begindate[1])];
                                $yyyy = $arr_begindate[0]+543;
                                $Show_begindate =$dd." ".$mm." ".$yyyy;

                                $arr_enddate = explode("-", trim($data2[ABS_ENDDATE])); //2016-01-01
                                $dd = number_format($arr_enddate[2]);
                                $mm = $MONTH_TH_arr[intval($arr_enddate[1])];
                                $yyyy = $arr_enddate[0]+543;
                                $Show_enddate =$dd." ".$mm." ".$yyyy;
                                if(implode("",$arr_begindate) == implode("",$arr_enddate)){
                                    $arr_content[$data_count][DATEBEGIN_END_OTHER] .= ",[".$Show_begindate."] ";
                                }else{
                                    $arr_content[$data_count][DATEBEGIN_END_OTHER] .= ",[".$Show_begindate."-".$Show_enddate."] ";
                                }
                                
                                
                               // $arr_content[$data_count][("DATEBEGIN_END_OTHER")]="DD/MM/YYYY";
                                
                                
			} // end while
		}  //end for

		//----------------หาการลาอื่นๆด้วย-------------------------------------------------------------------------
		$cmd = " select a.AB_CODE, b.AB_NAME,b.AB_COUNT, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD
						 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b
						 where	PER_ID=$PER_ID and a.AB_CODE not in ($ab_code) and a.AB_CODE=b.AB_CODE
						 				$search_condition
						 order by ABS_STARTDATE ";
               /* $cmd = " select x.*,b.AB_COUNT,b.AB_NAME  from  (select a.AB_CODE, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD 
				 from 		PER_ABSENTHIS a
				 where	PER_ID=$PER_ID and a.AB_CODE not in ($ab_code) 
								$search_condition
                                  ) x, PER_ABSENTTYPE b  
                                                      where x.AB_CODE=b.AB_CODE
        			 order by ABS_STARTDATE ";*/
                
		$db_dpis2->send_cmd($cmd);
//		echo "other abs :  $cmd<hr>";
//		$db_dpis2->show_error();
		$data_row = 0;		$ABS_CUT_PERIOD = 0;
		while($data2 = $db_dpis2->get_array()){
			$data_row++;
			if($data_row > 1){ 
				$data_count++;
				
				$arr_content[$data_count][type] = "CONTINUE";
			} // end if
 
 			$AB_COUNT = trim($data2[AB_COUNT]);
			$ABS_DAY = $data2[ABS_DAY];
			$AB_NAME = trim($data2[AB_NAME]);

			$ABS_STARTDATE = substr($data2[ABS_STARTDATE], 0, 10);
			if ($ABS_STARTDATE < $search_date_min)  {
				$ABS_STARTDATE = $search_date_min;
				$ABS_STARTPERIOD = 3;			// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
			} else 
				$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
			if ($ABS_ENDDATE > $search_date_max) {
				$ABS_ENDDATE = $search_date_max;
				$ABS_ENDPERIOD = 3;			// กรณีวันเริ่มนับเปลี่ยนเป็นช่วงกลาง Period เป็นเต็มวัน
			} else 
				$ABS_ENDPERIOD = $data2[ABS_ENDPERIOD];
			
			$arr_temp = explode("-", $ABS_STARTDATE);
			$START_DATE = $arr_temp[2]+0;
			$START_MONTH = $arr_temp[1]+0;
			$START_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_STARTPERIOD==1) $START_DATE .= " ช";		//ครึ่งวัน เช้า
			elseif($ABS_STARTPERIOD==2) $START_DATE .= " บ";	//ครึ่งวัน บ่าย
			
			$arr_temp = explode("-", $ABS_ENDDATE);
			$END_DATE = $arr_temp[2]+0;
			$END_MONTH = $arr_temp[1]+0;
			$END_YEAR = $arr_temp[0] - 543;			//???????????/
			if($ABS_ENDPERIOD==1) $END_DATE .= " ช";	//ครึ่งวัน เช้า
			elseif($ABS_ENDPERIOD==2) $END_DATE .= " บ";	//ครึ่งวัน บ่าย

			if($ABS_STARTDATE == $ABS_ENDDATE) $ABS_DURATION = $START_DATE;
			elseif($START_MONTH == $END_MONTH) $ABS_DURATION = $START_DATE ."-". $END_DATE;
			else $ABS_DURATION = "$START_DATE/$START_MONTH-$END_DATE/$END_MONTH";	//เดือนเริ่ม กับ เดือนสิ้นสุด คนละเดือนกัน

			$arr_content[$data_count][DATE_OTHER] = $ABS_DURATION;
			$arr_content[$data_count][DAY_OTHER] = $ABS_DAY?$ABS_DAY:"-";
			$arr_content[$data_count][DETAIL_OTHER] = $AB_NAME;
                        
                        $arr_begindate = explode("-", trim($data2[ABS_STARTDATE])); //2016-01-01
                        $dd = number_format($arr_begindate[2]);
                        $mm = $MONTH_TH_arr[intval($arr_begindate[1])];
                        $yyyy = $arr_begindate[0]+543;
                        $Show_begindate =$dd." ".$mm." ".$yyyy;
                        
                        $arr_enddate = explode("-", trim($data2[ABS_ENDDATE])); //2016-01-01
                        $dd = number_format($arr_enddate[2]);
                        $mm = $MONTH_TH_arr[intval($arr_enddate[1])];
                        $yyyy = $arr_enddate[0]+543;
                        $Show_enddate =$dd." ".$mm." ".$yyyy;

                        if(implode("",$arr_begindate) == implode("",$arr_enddate)){
                            $arr_content[$data_count][DATEBEGIN_END_OTHER] .= ",[".$Show_begindate."] ";
                        }else{
                            $arr_content[$data_count][DATEBEGIN_END_OTHER] .= ",[".$Show_begindate."-".$Show_enddate."] ";
                        }
			
			//$data_count++;
		} // end while
	} // function
	
	$CON_PER_AUDIT_FLAG="";
	if($SESS_AuditArray){
		if ( $PER_AUDIT_FLAG==1 ){
			
			$tCon="(";
						//$InGroupHRG = false;
			for ($i=0; $i < count($SESS_AuditArray); $i++) {
				if ($i>0)
					$tCon .= " or ";
				$tCon .= "(a.ORG_ID=" .$SESS_AuditArray[$i][0];
				if ($SESS_AuditArray[$i][1] != 0)
					$tCon .= ' and a.ORG_ID_1='. $SESS_AuditArray[$i][1];
				$tCon .= ")";
							   // $InGroupHRG = true;
			}
			$tCon .= ")";
			 $CON_PER_AUDIT_FLAG .= $tCon;
			//if($search_org_ass_id && $InGroupHRG==true){
			if($search_org_ass_id){
				 $CON_PER_AUDIT_FLAG .= " AND (a.ORG_ID=$search_org_ass_id)";
			 }
					
		}
					
			
		if ( $PER_AUDIT_FLAG==1 ){
			$CON_PER_AUDIT_FLAG = " AND (".$CON_PER_AUDIT_FLAG.")";
		}
	
	}
    if($R_ORG_ID =="") $R_ORG_ID = "b.ORG_ID";
	if($DPISDB=="odbc"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition $CON_PER_AUDIT_FLAG
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, iif(isnull($order_posno),0,$order_posno), a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e
						 where		$position_join and $R_ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition $CON_PER_AUDIT_FLAG
						 order by		a.DEPARTMENT_ID, e.ORG_NAME,c.ORG_NAME, to_number(replace($order_posno,'-','')),a.PER_NAME, a.PER_SURNAME   ";
	}elseif($DPISDB=="mysql"){
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME
						 from		(
											(
												(
													PER_PERSONAL a
													inner join $position_table b on ($position_join)
												) inner join PER_ORG c on (b.ORG_ID=c.ORG_ID)
											) left join PER_PRENAME d on (a.PN_CODE=d.PN_CODE)
										) left join PER_ORG e on (a.DEPARTMENT_ID=e.ORG_ID)
						 $search_condition $CON_PER_AUDIT_FLAG
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, $order_posno+0, a.PER_NAME, a.PER_SURNAME ";
	//ของเดิม
	//order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, to_number(replace($order_posno,'-','')), a.PER_NAME, a.PER_SURNAME ";
	
	} // end if
	if($select_org_structure==1 || $PER_AUDIT_FLAG==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
	//echo "<pre>$cmd<br>";
        //die("<br>$cmd<br>");
	//$db_dpis->show_error();
	$data_count = 0;
	$data_row = 0;
	$ORG_ID = -1;
	while($data = $db_dpis->get_array()){		
		if($ORG_ID != $data[ORG_ID]){
			$ORG_ID = $data[ORG_ID];
			$ORG_NAME = $data[ORG_NAME];
			if($ORG_NAME=="-")	$ORG_NAME = $ORG_BKK_TITLE;
			
			$arr_content[$data_count][type] = "ORG";
			$arr_content[$data_count][name] = $ORG_NAME;

			$data_row = 0;
			$data_count++;
		} // end if
		
		$data_row++;

		$PER_ID = $data[PER_ID];
		$PN_NAME = $data[PN_NAME];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
		
		if ($have_pic) $img_file = show_image($PER_ID,1); //1 = $db_dpis2,$db_dpis3 2 = $db_dpis3,$db_dpis4

		$arr_content[$data_count][type] = "CONTENT";
		if ($have_pic && $img_file){
				if ($FLAG_RTF)
				$arr_content[$data_count][image] = "<*img*".$img_file.",15*img*>";
				else
				$arr_content[$data_count][image] = "<*img*".$img_file.",4*img*>";
		}
		$arr_content[$data_count][name] = "$data_row.". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		count_absent($PER_ID);

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	//new format******************************************
	if($count_data){
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$col_function = implode(",", $column_function);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
				
		//	echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
		} else {
			$pdf->AutoPageBreak = false; 
	//		echo "$head_text1<br>";
			$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";
		
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
			if ($have_pic && $img_file) $IMAGE = $arr_content[$data_count][image];
			$NAME = $arr_content[$data_count][name];
			$DAY_01 = $arr_content[$data_count][DAY_01];
			$NUM_01 = $arr_content[$data_count][NUM_01];
			$DAY_03 = $arr_content[$data_count][DAY_03];
			$NUM_03 = $arr_content[$data_count][NUM_03];
			$DAY_TOT = $DAY_01 + $DAY_03;
			$NUM_TOT = $NUM_01 + $NUM_03;
			$DAY_10 = $arr_content[$data_count][DAY_10];
			$NUM_10 = $arr_content[$data_count][NUM_10];
			$DAY_04 = $arr_content[$data_count][DAY_04];
			$NUM_04 = $arr_content[$data_count][NUM_04];
			$DAY_11 = $arr_content[$data_count][DAY_11];
			$NUM_11 = $arr_content[$data_count][NUM_11];
			$DAY_OTHER = $arr_content[$data_count][DAY_OTHER];
			$TOTALWORK = $DAY_TOT+	$DAY_10+$DAY_04+$DAY_11+$DAY_OTHER;	
			$DETAIL_OTHER = $arr_content[$data_count][DETAIL_OTHER];
                        $DATEBEGIN_END_OTHER =$arr_content[$data_count][DATEBEGIN_END_OTHER];

			if($REPORT_ORDER == "ORG"){
	//			$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type] $NAME" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
//				echo "order 1-->ORG<br>";
				if ($FLAG_RTF) {
					$RTF->new_page();
					$RTF->print_tab_header();
				} else {
					if ($data_count >  0) $pdf->print_tab_line() ;
					$pdf->AddPage();
					$pdf->print_tab_header();
				}
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
				if ($have_pic && $img_file) $arr_data[] = $IMAGE;
				$arr_data[] = $NAME;
				$arr_data[] = $DAY_01;
				$arr_data[] = $NUM_01;
				$arr_data[] = $DAY_03;
				$arr_data[] = $NUM_03;
				$arr_data[] = $DAY_TOT;
				$arr_data[] = $NUM_TOT;
				$arr_data[] = $DAY_10;
				$arr_data[] = $DAY_04;
				$arr_data[] = $NUM_04;
				$arr_data[] = $DAY_OTHER;
				if (!$DAY_TOT && !$DAY_10 && !$DAY_04 && !$DAY_11 && $DAY_OTHER) {
						$arr_data[] = "(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")".$DATEBEGIN_END_OTHER;
				} else {	
					$TOTAL_WORK = "";
					if ($TOTALWORK > 0) $TOTAL_WORK = "วันทำการ ".$TOTALWORK." วัน ";
					if ($DAY_OTHER) {
						$arr_data[] = $TOTAL_WORK."(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")".$DATEBEGIN_END_OTHER;
					} else {
						$arr_data[] = $TOTAL_WORK.$DETAIL_OTHER.$DATEBEGIN_END_OTHER;
					}
				}
//				echo ">>".$data_count."|".$NAME.$DAY_01."|".$NUM_01."|".$DAY_03."|".$NUM_03."|".$DAY_TOT."|".$NUM_TOT."|".$DAY_10."|".$DAY_04."|".$DAY_11."|".$DAY_OTHER."|".$DETAIL_OTHER."<br>";
//				echo "order 2-->CONTENT<br>";

				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTINUE"){
				$arr_data = (array) null;
				if ($have_pic && $img_file) $arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = "";
				$arr_data[] = $DAY_OTHER;
				
				if (!$DAY_TOT && !$DAY_10 && !$DAY_04 && !$DAY_11 && $DAY_OTHER) {
					$arr_data[] = "(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")".$DATEBEGIN_END_OTHER;
				} else { 	
					$TOTAL_WORK = "";
					if ($TOTALWORK > 0) $TOTAL_WORK = "วันทำการ ".$TOTALWORK." วัน ";
					if ($DAY_OTHER) {
						$arr_data[] = $TOTAL_WORK."(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")".$DATEBEGIN_END_OTHER;
					} else {
						$arr_data[] = $TOTAL_WORK.$DETAIL_OTHER.$DATEBEGIN_END_OTHER;
					}
				}
//				echo "order 3-->CONTINUE<br>";

				if ($FLAG_RTF)
					$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				else
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if			
		} // end for
	}else{
		if ($FLAG_RTF)
			$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		else
			$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
//			$RTF->close_section(); 

		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
	
		$pdf->close();
		$fname = "R007001.pdf";
		$pdf->Output($fname,'D');	
	}
?>