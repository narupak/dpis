<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
	
	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
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

	$list_type_text = $ALL_REPORT_TITLE;

	if(in_array("PER_ORG", $list_type)){
		$list_type_text = "";
		if($select_org_structure==0) {
			if(trim($search_org_id)){ 
				$arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
				$list_type_text .= "$search_org_name";
			} // end if
		}
		if($select_org_structure==1) {
			if(trim($search_org_ass_id)){ 
				 $arr_search_condition[] = "(a.ORG_ID = $search_org_ass_id)";
				 //
				$list_type_text .= "$search_org_ass_name";
			} // end if
		}
	}
	if(in_array("ALL", $list_type) || !isset($list_type)){	//กรณีทั้งหมด หรือไม่ติ๊กเลือก check box list_type เลย
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
	} // end if
	if(count($arr_search_condition)) $search_condition = " where ". implode(" and ", $arr_search_condition);

	include ("rpt_R007001_format.php");	// กำหนดหัวและตัวแปรสำหรับการเปลี่ยนรูปแบบรายงาน
	$sum_w = array_sum($heading_width);
	for($h = 0; $h < count($heading_width); $h++) {
		$heading_width[$h] = $heading_width[$h] / $sum_w * 100;
	}

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R007001_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

//	$company_name = "รูปแบบการออกรายงาน : $list_type_text";
	$company_name = "";
	$report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type]" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "R0701";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);

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
		global $arr_content, $data_count;
		global $search_year,$search_date_min, $search_date_max, $SHOW_MONTH, $BKK_FLAG;
		
//		echo "search_date_min=$search_date_min , search_date_max=$search_date_max<br>";
		$search_condition = "";
		unset($arr_search_condition);
		if($DPISDB=="odbc" || $DPISDB=="mysql")		$column_startdate="LEFT(ABS_STARTDATE, 10)";				$column_enddate="LEFT(ABS_ENDDATE, 10)";
		if($DPISDB=="oci8")												$column_startdate="SUBSTR(ABS_STARTDATE, 1, 10)";		$column_enddate="SUBSTR(ABS_ENDDATE, 1, 10)";
		
		$arr_srch_cond = (array) null;
//		$arr_search_condition[] =  "(($column_startdate < '$search_date_min' OR $column_startdate >= '$search_date_min') AND $column_startdate <= '$search_date_max' AND $column_enddate >= '$search_date_min')";
		$arr_srch_cond[] =  "($column_startdate <= '$search_date_min' AND $column_enddate >= '$search_date_max')";	// กรณีวันในฐานข้อมูลค่อมวันที่เลือก
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันเริ่มและวันถึงอยู่ในช่วงที่เลือก 
		$arr_srch_cond[] =  "($column_enddate >= '$search_date_min' AND $column_enddate <= '$search_date_max')";	// กรณีวันถึงอยู่ในช่วง
		$arr_srch_cond[] =  "($column_startdate >= '$search_date_min' AND $column_startdate <= '$search_date_max')";	// กรณีวันเริ่มอยู่ในช่วงที่เลือก 
		$arr_search_condition[] = "(".implode(" OR ",$arr_srch_cond).")";
				
		if(count($arr_search_condition)) $search_condition = " AND ". implode(" AND ", $arr_search_condition);

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
	
			$cnt_nabs = $db_dpis2->send_cmd($cmd);
//			echo "normal abs :  $cmd ($cnt_nabs)<br>";
	//		$db_dpis2->show_error();
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
				$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
				$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
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
//				echo "AB_CODE=$AB_CODE , ABS_DAY_EXIST=".$ABS_DAY_EXIST[$AB_CODE]." , AB_COUNT=$AB_COUNT ,  ABS_STARTDATE=$ABS_STARTDATE , min=$START_DATE_CNT ,  ABS_ENDDATE=$ABS_ENDDATE , max=$END_DATE_CNT<br>";
				// เอาครึ่งวันไปหัก
				if(($ABS_STARTPERIOD==1 || $ABS_STARTPERIOD==2) || ($ABS_ENDPERIOD==1 || $ABS_ENDPERIOD==2)){//สำหรับลา ครึงวันเช้าครึ่งวันบ่าย
					$ABS_DAY_EXIST[$AB_CODE] = ($ABS_DAY_EXIST[$AB_CODE] - 0.5);
				}
				//--------------------------------------------------------------------------------------------------------------
				$arr_content[$data_count][("DAY_".$AB_CODE)] = $ABS_DAY_EXIST[$AB_CODE];		//+= $ABS_DAY;		= $ABS_DAY?$ABS_DAY:"-";
				$arr_content[$data_count][("NUM_".$AB_CODE)] += 1; //${"NUM_".$AB_CODE."_EXIST"};
			} // end while
		}  //end for

		//----------------หาการลาอื่นๆด้วย-------------------------------------------------------------------------
		$cmd = " select		a.AB_CODE, b.AB_NAME,b.AB_COUNT, ABS_ID, ABS_DAY, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD
						 from 		PER_ABSENTHIS a, PER_ABSENTTYPE b
						 where	PER_ID=$PER_ID and a.AB_CODE not in ($ab_code) and a.AB_CODE=b.AB_CODE
						 				$search_condition
						 order by ABS_STARTDATE ";
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
			$ABS_STARTPERIOD = $data2[ABS_STARTPERIOD];
			$ABS_ENDDATE = substr($data2[ABS_ENDDATE], 0, 10);
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
			
			//$data_count++;
		} // end while
	} // function

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
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, iif(isnull($order_posno),0,$order_posno), a.PER_NAME, a.PER_SURNAME ";
	}elseif($DPISDB=="oci8"){
		$search_condition = str_replace("where", "and", $search_condition);
		$cmd = " select			e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, 
												b.ORG_ID, c.ORG_NAME, a.PER_ID, d.PN_NAME, a.PER_NAME, a.PER_SURNAME
						 from			PER_PERSONAL a, $position_table b, PER_ORG c, PER_PRENAME d, PER_ORG e
						 where		$position_join and b.ORG_ID=c.ORG_ID and a.PN_CODE=d.PN_CODE(+) and a.DEPARTMENT_ID=e.ORG_ID(+)
											$search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, TO_NUMBER($order_posno), a.PER_NAME, a.PER_SURNAME ";
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
						 $search_condition
						 order by		e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, $order_posno_name, $order_posno+0, a.PER_NAME, a.PER_SURNAME ";
	} // end if
	if($select_org_structure==1) { 
		$cmd = str_replace("b.ORG_ID", "a.ORG_ID", $cmd);
		$cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
	}
	$count_data = $db_dpis->send_cmd($cmd);
//	echo "<br>$cmd<br>";
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

		$arr_content[$data_count][type] = "CONTENT";
		$arr_content[$data_count][name] = "$data_row.". $PN_NAME . $PER_NAME ." ". $PER_SURNAME;
		
		count_absent($PER_ID);

		$data_count++;
	} // end while
	
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	//new format******************************************
//	$RTF->open_section(1); 
//	$RTF->set_font($font, 20);
//	$RTF->color("0");	// 0=BLACK
		
	$RTF->add_header("", 0, false);	// header default
	$RTF->add_footer("", 0, false);		// footer default
		
	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";
		
	if($count_data){
		for($data_count=0; $data_count<count($arr_content); $data_count++){
			$REPORT_ORDER = $arr_content[$data_count][type];
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
			$TOTALWORK=$DAY_TOT+	$DAY_10+$DAY_04+$DAY_11+$DAY_OTHER;	
			$DETAIL_OTHER = $arr_content[$data_count][DETAIL_OTHER];

			if($REPORT_ORDER == "ORG"){
	//			$pdf->report_title = "$DEPARTMENT_NAME||รายงานการมาปฏิบัติราชการของ$PERSON_TYPE[$search_per_type] $NAME" . (($search_date_min || $search_date_max)?"||":"") . (($search_date_min)?"ตั้งแต่วันที่ $show_date_min ":"") . (($search_date_max)?"ถึงวันที่ $show_date_max":"");
//				echo "order 1-->ORG<br>";
//				if ($data_count >  0) $pdf->print_tab_line() ;
				$RTF->new_page();
				$RTF->print_tab_header();
				$arr_data = (array) null;
				$arr_data[] = $NAME;
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTENT"){
				$arr_data = (array) null;
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
						$arr_data[] = "(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")";
				} else {	
					$TOTAL_WORK = "";
					if ($TOTALWORK > 0) $TOTAL_WORK = "วันทำการ ".$TOTALWORK." วัน ";
					if ($DAY_OTHER) {
						$arr_data[] = $TOTAL_WORK."(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")";
					} else {
						$arr_data[] = $TOTAL_WORK.$DETAIL_OTHER;
					}
				}
//				echo ">>".$data_count."|".$NAME.$DAY_01."|".$NUM_01."|".$DAY_03."|".$NUM_03."|".$DAY_TOT."|".$NUM_TOT."|".$DAY_10."|".$DAY_04."|".$DAY_11."|".$DAY_OTHER."|".$DETAIL_OTHER."<br>";
//				echo "order 2-->CONTENT<br>";

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			}elseif($REPORT_ORDER == "CONTINUE"){
				$arr_data = (array) null;
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
					$arr_data[] = "(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")";
				} else { 	
					$TOTAL_WORK = "";
					if ($TOTALWORK > 0) $TOTAL_WORK = "วันทำการ ".$TOTALWORK." วัน ";
					if ($DAY_OTHER) {
						$arr_data[] = $TOTAL_WORK."(".$DAY_OTHER." วัน ".$DETAIL_OTHER.")";
					} else {
						$arr_data[] = $TOTAL_WORK.$DETAIL_OTHER;
					}
				}
//				echo "order 3-->CONTINUE<br>";

				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
			} // end if			
		} // end for
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	$RTF->close_tab(); 
//	$RTF->close_section(); 
	
	$RTF->display($fname);
?>