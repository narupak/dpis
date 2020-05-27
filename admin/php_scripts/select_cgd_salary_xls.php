<?	
//	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SAH_ID = $data[max_id] + 1;
		
	if(!isset($search_budget_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_budget_year = date("Y") + 543;
		else $search_budget_year = (date("Y") + 543) + 1;
	} // end if
	$SAH_KF_CYCLE = $search_kf_cycle;
	$SAH_KF_YEAR = $search_budget_year;
	if($SAH_KF_CYCLE == 1) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-04-01";
	elseif($SAH_KF_CYCLE == 2) $SAH_EFFECTIVEDATE = ($search_budget_year-543) . "-10-01";

	$tmp_date = explode("-", $SAH_EFFECTIVEDATE);
	// 86400 วินาที = 1 วัน
	$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
	$before_cmd_date = date("Y-m-d", $before_cmd_date);

	if (trim($SAH_DOCDATE)) {
		$temp_date = explode("/", $SAH_DOCDATE);
		$SAH_DOCDATE = ($temp_date)? ($temp_date[2] - 543) . "-" . $temp_date[1] . "-" . $temp_date[0] : "";
	}

	if (trim($SAH_ENDDATE)) {
		$temp_date = explode("/", $SAH_ENDDATE);
		$SAH_ENDDATE = ($temp_date)? ($temp_date[2] - 543) . "-" . $temp_date[1] . "-" . $temp_date[0] : "";
	}

	if($command=="CONVERT" && trim($RealFile)){	
		$excel_msg = "";
		require_once('excelread.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[0]);
				$NAME = trim($fields[1]);
				$SAH_POS_NO = ($fields[2]) + 0;
				$SAH_POSITION = trim($fields[3]);
				$POSITION_LEVEL = trim($fields[4]);
				$PER_SALARY = trim($fields[5]);
				$PER_SALARY = str_replace(",", "", $PER_SALARY);
				$LAYER_SALARY_MAX = trim($fields[6]);
				$LAYER_SALARY_MAX = str_replace(",", "", $LAYER_SALARY_MAX);
				$SAH_SALARY_MIDPOINT = trim($fields[7]);
				$SAH_SALARY_MIDPOINT = str_replace(",", "", $SAH_SALARY_MIDPOINT);
				$SAH_TOTAL_SCORE = trim($fields[8]);
				$SAH_PERCENT_UP = trim($fields[9]);
//				$SAH_SALARY_UP = trim($fields[10]);
				$SAH_SALARY_UP = trim($fields[11]);
				$SAH_SALARY_UP = str_replace(",", "", $SAH_SALARY_UP);
				$SAH_SALARY_EXTRA = trim($fields[12]);
				$SAH_SALARY_EXTRA = str_replace(",", "", $SAH_SALARY_EXTRA);
				$SAH_SALARY_TOTAL = trim($fields[13]);
				$SAH_SALARY_TOTAL = str_replace(",", "", $SAH_SALARY_TOTAL);
				$SAH_SALARY = trim($fields[14]);
				$SAH_SALARY = str_replace(",", "", $SAH_SALARY);
				$AM_NAME = trim($fields[15]);
				$SAH_REMARK = trim($fields[16]);
				
				$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$AM_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$MOV_CODE = $data[MOV_CODE];
				} else {
					if ($AM_NAME=="ดีเด่น") $MOV_CODE = "21345";
					elseif ($AM_NAME=="ดีมาก") $MOV_CODE = "21335";
					elseif ($AM_NAME=="ดี") $MOV_CODE = "21325";
					elseif ($AM_NAME=="พอใช้") $MOV_CODE = "21315";
					elseif ($AM_NAME=="ขาดราชการ") $MOV_CODE = "22374";
					elseif ($AM_NAME=="ควรปรับปรุง" || $AM_NAME=="ปรับปรุง") $MOV_CODE = "22380";
					elseif ($AM_NAME=="ลาไปเข้ารับการอบรม") $MOV_CODE = "22384";
					elseif ($AM_NAME=="ลาไปศึกษาและรับเงินเดือน") $MOV_CODE = "22383";
					elseif ($AM_NAME=="ลาศึกษา") $MOV_CODE = "22383";
					elseif ($AM_NAME=="อบรมต่างประเทศ") $MOV_CODE = "22384";
					elseif ($AM_NAME=="สั่งพักราชการ") $MOV_CODE = "22377";
				}

				if ($POSITION_LEVEL=="ปฎิบัติงาน") $LEVEL_NO = "O1";
				elseif ($POSITION_LEVEL=="ชำนาญงาน") $LEVEL_NO = "O2";
				elseif ($POSITION_LEVEL=="อาวุโส") $LEVEL_NO = "O3";
				elseif ($POSITION_LEVEL=="ทักษะพิเศษ") $LEVEL_NO = "O4";
				elseif ($POSITION_LEVEL=="ปฏิบัติการ") $LEVEL_NO = "K1";
				elseif ($POSITION_LEVEL=="ชำนาญการ") $LEVEL_NO = "K2";
				elseif ($POSITION_LEVEL=="ชำนาญการพิเศษ") $LEVEL_NO = "K3";
				elseif ($POSITION_LEVEL=="เชี่ยวชาญ") $LEVEL_NO = "K4";
				elseif ($POSITION_LEVEL=="ทรงคุณวุฒิ") $LEVEL_NO = "K5";
				elseif ($POSITION_LEVEL=="ต้น") 
					if (substr($SAH_POSITION,0,9)=="นักบริหาร") $LEVEL_NO = "S1";
					else $LEVEL_NO = "D1";
				elseif ($POSITION_LEVEL=="สูง") 
					if (substr($SAH_POSITION,0,9)=="นักบริหาร") $LEVEL_NO = "S2";
					else $LEVEL_NO = "D2";

				if (!$MOV_CODE) $MOV_CODE = "213";
				if (!$EX_CODE) $EX_CODE = "024";
				if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
				if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
				$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		
				if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
				if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
				if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";

				// mai edit 29 09 54
				if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
				if (!$PER_SALARY_CURRENT_UPDATE) $PER_SALARY_CURRENT_UPDATE = "N";

				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					// update and insert into PER_SALARYHIS 
					$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$PER_ID 
									order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$tmp_SAH_ID = trim($data1[SAH_ID]);
					$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
					$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
									where SAH_ID=$tmp_SAH_ID";
					
					$db_dpis1->send_cmd($cmd);	
					//$db_dpis1->show_error();		

					if ( $SAH_LAST_SALARY == "Y" ) { // ตรวจสอบว่าถ้ารายการที่นำเข้าเป็นรายการล่าสุดให้แก้ไขรายการที่แล้วเป็น SAH_LAST_SALARY ='N'
					$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' 		
									where PER_ID=$PER_ID";
					$db_dpis1->send_cmd($cmd);	
					$db_dpis1->show_error();	
					}
					//echo "<br>";
					echo "</br>ข้อมูล $NAME";
					echo "</br>ให้เป็นประวัติเงินเดือนล่าสุด : ".$SAH_LAST_SALARY;
					echo "</br>วันที่สิ้นสุด : ".$SAH_ENDDATE;
					echo "</br>ให้ปรับปรุงเงินเดือนปัจจุบัน : ".$PER_SALARY_CURRENT_UPDATE;
					echo "</br>XLS เงินเดือนก่อนเลื่อน : ".$PER_SALARY;
					echo "</br>XLS เงินเดือนหลังเลื่อน : ".$SAH_SALARY;
					$cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
									SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
									LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
									SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ)
									values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', 
									'$SAH_DOCDATE', '$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', 
									$SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', 
									'$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_POS_NO', 
									$SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, 
									'$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
					if ($PER_SALARY_CURRENT_UPDATE=='Y' ){
						$cmd = " update PER_PERSONAL set PER_SALARY = $SAH_SALARY where PER_ID = $PER_ID ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}

					$SAH_ID++;
				} else echo "$PER_CARDNO $NAME<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}
		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
		if($SAH_DOCDATE){
			$arr_temp = explode("-", substr($SAH_DOCDATE, 0, 10));
			$SAH_DOCDATE =  $arr_temp[2]."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		} // end if

		if($SAH_ENDDATE){
			$arr_temp = explode("-", substr($SAH_ENDDATE, 0, 10));
			$SAH_ENDDATE =  $arr_temp[2]."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		} // end if
	} // end if
?>