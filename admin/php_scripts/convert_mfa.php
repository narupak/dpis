<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("function_find_absent_day.php");

// เลขที่ตำแหน่งครอง 2 คน select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis351 = new connect_dpis35($dpis35db_host, $dpis35db_name, $dpis35db_user, $dpis35db_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;

	if( $command=='ABSENT_MDB' || $command=='ABSENT_NO_ID' ){
		if( $command=='ABSENT_MDB' ) {
			$cmd = " delete from PER_ABSENTHIS where ABS_STARTDATE <= '2014-30-09' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 1) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}

		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if( $command=='ABSENT_MDB' ) 
			$cmd = " SELECT Reason2, StartDate, FinishDate, StartDateStatus, FinishDateStatus, 
							  TimesForAbsent, IgnoreDate, Reason, EmpID, left(IDCard,13) as PER_CARDNO, FnameT, LnameT
							  FROM Hr_exp2
						  WHERE IDCard is Not Null 
						  ORDER BY IDCard, StartDate ";
		elseif( $command=='ABSENT_NO_ID' ) 
			$cmd = " SELECT Reason2, StartDate, FinishDate, StartDateStatus, FinishDateStatus, 
							  TimesForAbsent, IgnoreDate, Reason, EmpID, left(IDCard,13) as PER_CARDNO, FnameT, LnameT
							  FROM Hr_exp2
							  WHERE IDCard is Null 
							  ORDER BY FnameT, LnameT, StartDate ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTHIS++;    
			$EmpID = trim($data[EmpID]);
			$FnameT = trim($data[FnameT]);
			$LnameT = trim($data[LnameT]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			if ($PER_CARDNO=="3104600185803") $PER_CARDNO = "3100600185803";
			elseif ($PER_CARDNO=="3100202504125") $PER_CARDNO = "3102002504125";
			elseif ($PER_CARDNO=="310140083010") $PER_CARDNO = "3101400883010";
			elseif ($PER_CARDNO=="3101700588751") $PER_CARDNO = "3101700532751";
			elseif ($PER_CARDNO=="3100901911710") $PER_CARDNO = "3100901911716";
			elseif ($PER_CARDNO=="3100901818449") $PER_CARDNO = "3100901818465";
			elseif ($PER_CARDNO=="3100500183090") $PER_CARDNO = "3100500193090";
			elseif ($PER_CARDNO=="3100202805037") $PER_CARDNO = "3100202808037";
			elseif ($PER_CARDNO=="310700308860") $PER_CARDNO = "3101700308860";
			elseif ($PER_CARDNO=="3702500030446") $PER_CARDNO = "3720500030446";
			elseif ($PER_CARDNO=="3941000316049") $PER_CARDNO = "3940100316049";
			elseif ($PER_CARDNO=="3100200298629") $PER_CARDNO = "3102200298629";
			elseif ($PER_CARDNO=="3200100721135") $PER_CARDNO = "3200100921135";
			elseif ($PER_CARDNO=="330010058784") $PER_CARDNO = "3300100508784";
			elseif ($PER_CARDNO=="3100601584882") $PER_CARDNO = "3100601584914";
			elseif ($PER_CARDNO=="3161401820585") $PER_CARDNO = "3101401820585";
			elseif ($PER_CARDNO=="3120622578015") $PER_CARDNO = "3120600578015";
			elseif ($PER_CARDNO=="3100200270001") $PER_CARDNO = "3100500270001";
			elseif ($PER_CARDNO=="310202242450") $PER_CARDNO = "3101202242450";
			elseif ($PER_CARDNO=="3100200057842") $PER_CARDNO = "3102200057842";
			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' or (PER_NAME='$FnameT' and PER_SURNAME='$LnameT') ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[PER_ID] + 0;

			if ($PER_ID > 0) {
				$ABSENT_TYPE_NAME = $data[Reason2];
				$ABS_REMARK = trim($data[Reason]);
				$ABSENT_TYPE = "";
				if ($ABSENT_TYPE_NAME=="ลาป่วย") $ABSENT_TYPE = "01";
				elseif ($ABSENT_TYPE_NAME=="ลาคลอดบุตร") $ABSENT_TYPE = "02";
				elseif ($ABSENT_TYPE_NAME=="ลากิจ") $ABSENT_TYPE = "03";
				elseif ($ABSENT_TYPE_NAME=="ลาพักผ่อนประจำปี") $ABSENT_TYPE = "04";
				elseif ($ABSENT_TYPE_NAME=="ลาเพื่อพิธีฮัจย์" || $ABSENT_TYPE_NAME=="ลาอุปสมบท") $ABSENT_TYPE = "05";
				elseif ($ABSENT_TYPE_NAME=="ลาศึกษาต่อ/รับทุน/วิจัย") $ABSENT_TYPE = "07";
				elseif ($ABSENT_TYPE_NAME=="ไปปฺฏิบัติงานในองค์กร ตปท") $ABSENT_TYPE = "08";
				elseif ($ABSENT_TYPE_NAME=="ลาติดตามคู่สมรส") $ABSENT_TYPE = "09";
				elseif ($ABSENT_TYPE_NAME=="มาสาย") $ABSENT_TYPE = "10";
				elseif ($ABSENT_TYPE_NAME=="ลาเพื่อเลี้ยงดูบุตร") $ABSENT_TYPE = "11";
				elseif ($ABSENT_TYPE_NAME=="ขาดราชการ") $ABSENT_TYPE = "13";
				elseif ($ABSENT_TYPE_NAME=="ลาโดยถือเป็นการทำหน้าที่") $ABSENT_TYPE = "19";
				elseif ($ABS_REMARK=="ลาป่วย") $ABSENT_TYPE = "01";
				elseif ($ABS_REMARK=="ลาพักผ่อนประจำปี" || $ABS_REMARK=="ลาพักผ่อน") $ABSENT_TYPE = "04";
				elseif ($ABS_REMARK=="ลาไปต่างประเทศ") $ABSENT_TYPE = "08";
				else echo "$ABSENT_TYPE_NAME<br>";
				$ABS_STARTDATE = substr(trim($data[StartDate]),0,10);
				$ABS_ENDDATE = substr(trim($data[FinishDate]),0,10);
				$ABS_DAY = $data[TimesForAbsent] + 0;
				$ABS_STARTPERIOD = trim($data[StartDateStatus]);
				$ABS_ENDPERIOD = trim($data[FinishDateStatus]);
				if ($ABS_STARTPERIOD==64) {
					$ABS_STARTPERIOD = 1;
					if ($ABS_DAY == 0.5) $ABS_ENDPERIOD = 1;
				} elseif ($ABS_STARTPERIOD==128) {
					$ABS_STARTPERIOD = 2;
					if ($ABS_DAY == 0.5) $ABS_ENDPERIOD = 2;
				}
				if ($ABS_ENDPERIOD==64) {
					$ABS_ENDPERIOD = 1;
				} elseif ($ABS_ENDPERIOD==128) {
					$ABS_ENDPERIOD = 2;
				}
				if ($ABS_STARTPERIOD==32 || $ABS_STARTPERIOD=="NULL" || !$ABS_STARTPERIOD) $ABS_STARTPERIOD = 3;
				if ($ABS_ENDPERIOD==32 || $ABS_ENDPERIOD=="NULL" || !$ABS_ENDPERIOD) $ABS_ENDPERIOD = 3;

				if (!$ABS_DAY || $ABS_DAY=="NULL") $ABS_DAY = 0;
				if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
				if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

				$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', $ABS_ENDPERIOD, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				} else {
					$MAX_ID++;
				}
			} else {
				if (!$PER_CARDNO || $PER_CARDNO != $TMP_PER_CARDNO) {
					if ($PER_CARDNO)
						echo "$EmpID - $PER_CARDNO - $FnameT - $LnameT<br>"; 
					else
						echo "$FnameT $LnameT<br>"; 
					$TMP_PER_CARDNO = $PER_CARDNO;
				}
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS where PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE = 1) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if( $command=='CHECKABSENT_MDB' ){
		$cmd = " SELECT DISTINCT EmpID, left(IDCard,13) as PER_CARDNO
						  FROM AbsentRec
						  WHERE IDCard is Not Null ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		$MAX_ID = 1;
		while($data = $db_dpis35->get_array()){
			$EmpID = trim($data[EmpID]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if (!$count_data) echo "$EmpID - $PER_CARDNO<br>"; 
		} // end while						
	} // end if

	if( $command=='ABSENT_ORACLE' ){
		$cmd = " delete from PER_ABSENTHIS where ABS_STARTDATE <= '2014-30-09' and PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE != 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK
						  FROM PER_ABSENTHIS
						  WHERE PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE != 1) 
						  ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTHIS++;    
			$PER_ID = $data[PER_ID];
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];
			$ABS_DAY = $data[ABS_DAY];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$ABS_REMARK = trim($data[ABS_REMARK]);

			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = "NULL";
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = "NULL";
			if (!$ABS_DAY) $ABS_DAY = 0;

			$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
							VALUES ($MAX_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', $ABS_ENDPERIOD, $ABS_DAY, 
							$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
			$db_dpis->send_cmd($cmd);

			$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
			$count_data = $db_dpis1->send_cmd($cmd1);
			if (!$count_data) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			} else {
				$MAX_ID++;
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS WHERE PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE != 1) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if( $command=='ABSENT_OCT' ){
		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK
						  FROM PER_ABSENTHIS
						  WHERE ABS_STARTDATE >= '2014-10-01' and  ABS_STARTDATE <= '2014-10-31'
						  ORDER BY ABS_ID ";
		$db_dpis35->send_cmd($cmd);
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$PER_ABSENTHIS++;    
			$PER_ID = $data[PER_ID];
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];
			$ABS_DAY = $data[ABS_DAY];
			$UPDATE_USER = $data[UPDATE_USER];
			$UPDATE_DATE = trim($data[UPDATE_DATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$ABS_REMARK = trim($data[ABS_REMARK]);

			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = "NULL";
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = "NULL";
			if (!$ABS_DAY) $ABS_DAY = 0;

			$TMP_ABS_STARTDATE = save_date($ABS_STARTDATE); 
			$TMP_ABS_ENDDATE = save_date($ABS_ENDDATE); 
			
			$cmd2 = " select  AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
								from PER_ABSENTHIS  
								where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$TMP_ABS_STARTDATE' and  
									ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$TMP_ABS_ENDDATE' and 
									ABS_ENDPERIOD='$ABS_ENDPERIOD' and ABS_DAY='$ABS_DAY' ";
			$count = $db_dpis->send_cmd($cmd2);
			//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
			if(!$count) { 
				$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
								ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
								VALUES ($MAX_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', $ABS_ENDPERIOD, $ABS_DAY, 
								$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
				$db_dpis->send_cmd($cmd);

				$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
				$count_data = $db_dpis1->send_cmd($cmd1);
				if (!$count_data) {
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";
				} else {
					$MAX_ID++;
				}
			}
		} // end while						

		$PER_ABSENTHIS--;
		$cmd = " select count(PER_ID) as COUNT_NEW from PER_ABSENTHIS WHERE PER_ID in (select PER_ID from PER_PERSONAL where PER_TYPE != 1) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_ABSENTHIS - $PER_ABSENTHIS - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_dpis35->free_result();
		$db_dpis351->free_result();
	} // end if

	if($command=="ABSENT_XLS" && is_file($RealFile)){
		echo $RealFile;
		$cmd = " delete from PER_ABSENTHIS where ABS_STARTDATE <= '2014-30-09' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select max(ABS_ID) as MAX_ID from PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[10]);
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;

				if ($PER_ID > 0) {
					$ABSENT_TYPE_NAME = trim($fields[1]);
					$ABSENT_TYPE = "";
					if ($ABSENT_TYPE_NAME=="ลาป่วย") $ABSENT_TYPE = "01";
					elseif ($ABSENT_TYPE_NAME=="ลาคลอดบุตร") $ABSENT_TYPE = "02";
					elseif ($ABSENT_TYPE_NAME=="ลากิจ") $ABSENT_TYPE = "03";
					elseif ($ABSENT_TYPE_NAME=="ลาพักผ่อนประจำปี") $ABSENT_TYPE = "04";
					elseif ($ABSENT_TYPE_NAME=="ลาเพื่อพิธีฮัจย์" || $ABSENT_TYPE_NAME=="ลาอุปสมบท") $ABSENT_TYPE = "05";
					elseif ($ABSENT_TYPE_NAME=="ลาศึกษาต่อ/รับทุน/วิจัย") $ABSENT_TYPE = "07";
					elseif ($ABSENT_TYPE_NAME=="ไปปฺฏิบัติงานในองค์กร ตปท") $ABSENT_TYPE = "08";
					elseif ($ABSENT_TYPE_NAME=="ลาติดตามคู่สมรส") $ABSENT_TYPE = "09";
					elseif ($ABSENT_TYPE_NAME=="มาสาย") $ABSENT_TYPE = "10";
					elseif ($ABSENT_TYPE_NAME=="ลาเพื่อเลี้ยงดูบุตร") $ABSENT_TYPE = "11";
					elseif ($ABSENT_TYPE_NAME=="ขาดราชการ") $ABSENT_TYPE = "13";
					elseif ($ABSENT_TYPE_NAME=="ลาโดยถือเป็นการทำหน้าที่") $ABSENT_TYPE = "19";
					else echo "$ABSENT_TYPE_NAME<br>";
					$ABS_STARTDATE = substr(trim($fields[12]),0,10);
					$ABS_ENDDATE = substr(trim($fields[13]),0,10);
	//				$SEX = trim($fields[4]);
	//				$DEPARTMENT_CODE = trim($fields[5]);
					$ABS_DAY = trim($fields[6]);
					$ABS_STARTPERIOD = 3;
					$ABS_ENDPERIOD = 3;
	//				$DIVISION_CODE = trim($fields[7]);
					$ABS_REMARK = trim($fields[8]);
				
					if (!$ABS_DAY || $ABS_DAY=="NULL") $ABS_DAY = 0;
					if (!$ABS_STARTDATE) $ABS_STARTDATE = '-';
					if (!$ABS_ENDDATE) $ABS_ENDDATE = '-';

					$cmd = " INSERT INTO PER_ABSENTHIS(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
									ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, ABS_REMARK)
									VALUES ($MAX_ID, $PER_ID, '$ABSENT_TYPE', '$ABS_STARTDATE', $ABS_STARTPERIOD, '$ABS_ENDDATE', $ABS_ENDPERIOD, $ABS_DAY, 
									$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$ABS_REMARK') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT ABS_ID FROM PER_ABSENTHIS WHERE ABS_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					} else {
						$MAX_ID++;
						$PER_ABSENTHIS = $MAX_ID;    
					}

				} elseif ($PER_CARDNO) echo "ไม่พบข้อมูล $PER_CARDNO<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="CHECKABSENT_XLS" && is_file($RealFile)){
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[10]);
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' ";
				$count_data = $db_dpis2->send_cmd($cmd);
				if (!$count_data) echo "$PER_CARDNO<br>"; 
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="POSTING" && is_file($RealFile)){
		echo $RealFile;
//		$cmd = " truncate table per_postinghis ";
//		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select max(POST_ID) as MAX_ID from PER_POSTINGHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$FULLNAME = trim($fields[0]);
				$FULLNAME = str_replace("  ", " ", $FULLNAME);
				$FULLNAME = str_replace("ลาออก", "", $FULLNAME);
				$FULLNAME = str_replace("ร.น. ", "", $FULLNAME);
				$FULLNAME = str_replace("ร.น.", "", $FULLNAME);
				$FULLNAME = str_replace("(เกษียณ)", "", $FULLNAME);
				$FULLNAME = str_replace("(ลาติดตาม)", "", $FULLNAME);
				$FULLNAME = str_replace("ว่าที่ร้อยตรี", "", $FULLNAME);
				if (substr($FULLNAME,0,3)=="นาย") $FULLNAME = substr($FULLNAME,3);
				elseif (substr($FULLNAME,0,4)=="น.ส.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,8)=="ร.ท.หญิง") $FULLNAME = substr($FULLNAME,8);
				elseif (substr($FULLNAME,0,4)=="ร.ท.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ม.ล.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="น.ต.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ร.ต.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ร.อ.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="น.สง") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ออท.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,6)=="นางสาว") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="ม.ร.ว.") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,3)=="นาง") $FULLNAME = substr($FULLNAME,3);

				if ($FULLNAME=="ทรงชัยปฏิยุทธ") $FULLNAME = "ทรงชัย ชัยปฏิยุทธ";
				elseif ($FULLNAME=="ปารณีย์ คล้ายสุบรรณ์ วัฒน์ธรรมาวุธ") $FULLNAME = "ปารณีย์  คล้ายสุบรรณ์ วัฒน์ธรรมาวุธ";
				elseif ($FULLNAME=="นารีตา สุประดิษฐ์ ณ อยุธยา") $FULLNAME = "นารีตา สุประดิษฐ ณ อยุธยา";
				elseif ($FULLNAME=="ดนย์วิศว์ พูลสวัสดิ์ (อรรถสิทธิ์)") $FULLNAME = "ดนย์วิศว์ พูลสวัสดิ์";
				elseif ($FULLNAME=="นิศากร ลิขิควัฒนโสภณ") $FULLNAME = "นิศากร ลิขิตวัฒนโสภณ";
				elseif ($FULLNAME=="นพดล นิปกานนท์ (เปลี่ยนป็น ชวดล)") $FULLNAME = "ชวดล นิปธานนนท์";
				elseif ($FULLNAME=="จริยวัฒน์ สันตะบุตร") $FULLNAME = "จริย์วัฒน์ สันตะบุตร";
				elseif ($FULLNAME=="พิสิฐ พิชิตมาร") $FULLNAME = "พิสิฏฐ์ พิชิตมาร";
				elseif ($FULLNAME=="ธวัช ขวัญจิตร") $FULLNAME = "ธวัช ขวัญจิตร์";
				elseif ($FULLNAME=="จันทร์รัตน์ งามชนะ") $FULLNAME = "จันทรรัตน์ งามชนะ";
				elseif ($FULLNAME=="ยุคลธร ธีรธรรม") $FULLNAME = "ยุคลธร ธีระธรรม";
				elseif ($FULLNAME=="ชัชวรรณ สาครสินธุ์") $FULLNAME = "ชัชวรรณ สาครสินธุ์ ร.น.";
				elseif ($FULLNAME=="จันตรี เภาวนิตย์") $FULLNAME = "จันตรี เภาวะนิต";
				elseif ($FULLNAME=="สุวิจักขณ์ พิริยะธนานุกุล") $FULLNAME = "สุวิจักขณ์ พิริยะธนานุกูล";
				elseif ($FULLNAME=="สธน เกษมสันต์ฯ") $FULLNAME = "สธน เกษมสันต์ ณ อยุธยา";
				elseif ($FULLNAME=="อัมพร วาสิกสิริ") $FULLNAME = "อัมพร วาสิกศิริ";
				elseif ($FULLNAME=="กฤษณี พันธุ์ประเสริฐ") $FULLNAME = "กฤษณี พันธ์ประเสริฐ";
				elseif ($FULLNAME=="ณัฐ ภิญโญวัฒนชีพ") $FULLNAME = "ณัช ภิญโญวัฒนชีพ";
				elseif ($FULLNAME=="โสพิน สุพัฒนกิต") $FULLNAME = "โสพิน สุพัฒนกิตติ";
				elseif ($FULLNAME=="ชาตรี อรรจานันท์") $FULLNAME = "ชาตรี อรรจนานันท์";
				elseif ($FULLNAME=="พิษณุ สุวรรณะชฏ") $FULLNAME = "พิษณุ สุวรรณะชฎ";
				elseif ($FULLNAME=="นนท์วัฒน์ จันทร์ตรี") $FULLNAME = "นนทวัฒน์ จันทร์ตรี";
				elseif ($FULLNAME=="อรุณรุ่ง โพธิ์ทอง") $FULLNAME = "อรุณรุ่ง โพธิ์ทอง ฮัมฟรีย์ส";
				elseif ($FULLNAME=="นาฏนภางค์ ดำรงสุนทรชัย") $FULLNAME = "นาฎนภางค์ ดำรงสุนทรชัย";
				elseif ($FULLNAME=="ชลิต มานิตกุล") $FULLNAME = "ชลิต มานิตยกุล";
				elseif ($FULLNAME=="สุรเชษบ์ บุญธินันท์") $FULLNAME = "สุรเชษฐ์ บุญธินันท์";
				elseif ($FULLNAME=="อภิรดี อนะคระหานนท์") $FULLNAME = "อภิรดี อนุคระหานนท์";
				elseif ($FULLNAME=="มูหมัด ลูตฟี อุเช็ง") $FULLNAME = "มูหมัดลูตฟี อุเซ็ง";
				elseif ($FULLNAME=="นภัสพร ภัทรีชวาลย์") $FULLNAME = "นภัสพร ภัทรีชวาล";
				elseif ($FULLNAME=="ประสิทธิ วิเศษแก้ว") $FULLNAME = "ประสิทธิ์ วิเศษแก้ว";
				elseif ($FULLNAME=="จุลวัจน์ นรินทรางกูรฯ") $FULLNAME = "จุลวัจน์ นรินทรางกูร ณ อยุธยา";
				elseif ($FULLNAME=="จงเจริญ เทพหัสดินฯ") $FULLNAME = "จงเจริญ เทพหัสดิน ณ อยุธยา";
				elseif ($FULLNAME=="ชาญชัญ จรัญวัฒนากิจ") $FULLNAME = "ชาญชัย จรัญวัฒนากิจ";
				elseif ($FULLNAME=="สรจักร บูรณะสัมฤทธิ์") $FULLNAME = "สรจักร บูรณะสัมฤทธิ";
				elseif ($FULLNAME=="อมรพงษ์ ผูกพัทธ์") $FULLNAME = "อมรพงษ์ ผูกพัทธิ์";
				elseif ($FULLNAME=="สมถวิล สิทธสระ") $FULLNAME = "สมถวิล สิทธิสระ";
				elseif ($FULLNAME=="สุพจน์ อิศรางกูรฯ") $FULLNAME = "สุพจน์ อิศรางกูร ณ อยุธยา";
				elseif ($FULLNAME=="อิทธิ ดิษฐบรรจง") $FULLNAME = "อิทธิ ดิษฐบรรจง ร.น.";
				elseif ($FULLNAME=="ถาวร นาฎะคายี") $FULLNAME = "ถาวร นาฏะคายี";
				elseif ($FULLNAME=="บุษกร พฤกษ์พงค์") $FULLNAME = "บุษกร พฤกษพงศ์";
				elseif ($FULLNAME=="สนั่นชาติ เทพหัสดินฯ") $FULLNAME = "สนั่นชาติ เทพหัสดิน ณ อยุธยา";
				elseif ($FULLNAME=="ขจรศักดิ์ สุดสวาดิ์") $FULLNAME = "ขจรศักดิ์ สุดสวาสดิ์";
				elseif ($FULLNAME=="ธิระเศรษฐ ลิ้มประเสริฐ") $FULLNAME = "ธิระเศรษฐ์ ลิ้มประเสริฐ";
				elseif ($FULLNAME=="วนาลี โล่ห์เพ็ชร") $FULLNAME = "วนาลี โล่ห์เพชร";
				elseif ($FULLNAME=="เบญจมินทร์ สุกาญจนัทที") $FULLNAME = "เบญจมินทร์ สุกาญจนัจที";
				elseif ($FULLNAME=="ลินดา บ่างวิรุฬรักษ์") $FULLNAME = "ลินดา บ่างวิรุฬห์รักษ์";
				elseif ($FULLNAME=="ครองนิษฐ รักษ์เจริญ") $FULLNAME = "ครองขนิษฐ รักษ์เจริญ";
				elseif ($FULLNAME=="ลัดดาวัลย์ กองโคกกรวด") $FULLNAME = "ลภัสรดา กองโคกกรวด";
				elseif ($FULLNAME=="จุลีพจน์ อิศรางกูรฯ") $FULLNAME = "จุลีพจน์ อิศรางกูร ณ อยุธยา";
				elseif ($FULLNAME=="พสุศิษฎ์ วงศ์สุรวัฒน์") $FULLNAME = "พสุศิษฏ์ วงศ์สุรวัฒน์";
				elseif ($FULLNAME=="สมลักษณ์ พูลพันธ์") $FULLNAME = "สมลักษณ์ พูลพันธุ์";
				elseif ($FULLNAME=="พรรณภา จันทรารมย์") $FULLNAME = "พรรณนภา จันทรารมย์";
				elseif ($FULLNAME=="ปัทมาลี ปาลลา") $FULLNAME = "ปัทมารี ปาลลา";
				elseif ($FULLNAME=="อาพันธ์ชนิต วิชัยดิษฐ") $FULLNAME = "อาพันธ์ชนิตร วิชัยดิษฐ";
				elseif ($FULLNAME=="ปวรี ชูโต") $FULLNAME = "ปวรี ชูโต ชัยปฏิยุทธ";
				elseif ($FULLNAME=="นิภา ปิติศรีพันธ์") $FULLNAME = "นิภา ปีติศรีพันธุ์";
				elseif ($FULLNAME=="โอภาส จัทรทรัพย์") $FULLNAME = "โอภาส จันทรทรัพย์";
				elseif ($FULLNAME=="จิราภรณ์ ธีรภัทรานันท์") $FULLNAME = "จิราภรณ์ ธีระภัทรานันท์";
				elseif ($FULLNAME=="อับดุลรอฮิม ดาตูมะตา") $FULLNAME = "อับดุลรอฮิม ดาตูมะดา";
				elseif ($FULLNAME=="เกียรติพร วัชรพร") $FULLNAME = "เกียรติพร วัชระกร";
				elseif ($FULLNAME=="มาระตี นะลิตา") $FULLNAME = "มาระตี นะลิตา อันดาโม";
				elseif ($FULLNAME=="วิทิต เภาวัฒนสุข") $FULLNAME = "วิทิต เภาวัฒนาสุข";
				elseif ($FULLNAME=="พฤทธิพงษ์ ปุณฑริโกมล") $FULLNAME = "พฤทธิพงษ์ ปุณฑริโกบล";
				elseif ($FULLNAME=="อุรัรัชต์ รัตนพฤกษ์") $FULLNAME = "อุรีรัชต์ รัตนพฤกษ์";
				elseif ($FULLNAME=="เชต ธีระพัฒนะ") $FULLNAME = "เชต ธีรพัฒนะ";
				elseif ($FULLNAME=="อุทัย ตัณฑุลสวัสดิ์") $FULLNAME = "อุทัย ตันฑุลสวัสดิ์";
				elseif ($FULLNAME=="ฟาบิโอ จินดา") $FULLNAME = "ฟาบีโอ จินดา";
				elseif ($FULLNAME=="วิลาสิณี ภิญโญวิทย์") $FULLNAME = "วิลาสินี ภิญโญวิทย์";
				elseif ($FULLNAME=="จันทร์ทิภา ภู่ตระกูล") $FULLNAME = "จันทร์ทิพา ภู่ตระกูล";
				elseif ($FULLNAME=="เบญวรรณ โสตะจินดา") $FULLNAME = "เบญจวรรณ โสตะจินดา";
				elseif ($FULLNAME=="ปรางทิพย์ กาญจนหัตกิจ") $FULLNAME = "ปรางทิพย์ กาญจนหัตถกิจ";
				elseif ($FULLNAME=="ปิยพันธ์ อติแพทย์") $FULLNAME = "ปิยพันธุ์ อติแพทย์ ร.น.";
				elseif ($FULLNAME=="อโนทัย หอมจิตร์") $FULLNAME = "อโนทัย หอมจิตร";
				elseif ($FULLNAME=="วีนัส อัสวภูมิ") $FULLNAME = "วีนัส อัศวภูมิ";
				elseif ($FULLNAME=="ณัฎฐา สุนทราภา") $FULLNAME = "ณัฏฐา สุนทราภา";
				elseif ($FULLNAME=="สุภาวดี โชติกญาน") $FULLNAME = "สุภาวดี โชติกญาณ";
				elseif ($FULLNAME=="มณฑล วงศ์เลิศ") $FULLNAME = "มณฑล วงษ์เลิศ";
				elseif ($FULLNAME=="สราญจิตต์ ศรีสกุน") $FULLNAME = "สราญจิตต์ ศรีศกุน";
				elseif ($FULLNAME=="ณรุทธิ์ สุนทโรดม") $FULLNAME = "ณรุทธ์ สุนทโรดม";
				elseif ($FULLNAME=="ปราณี วณิชพันธสกุล") $FULLNAME = "ปราณี วณิชพันธ์สกุล";
				elseif ($FULLNAME=="ณัฐสุภา นิลปานานนท์") $FULLNAME = "ณัฐสุภา นีลปานานนท์";
				elseif ($FULLNAME=="พลพงษ์ วังแพน") $FULLNAME = "พลพงศ์ วังแพน";
				elseif ($FULLNAME=="วงศ์เทพ อรรถไกรวัลวที") $FULLNAME = "วงศ์เทพ อรรถไกวัลวที";
				elseif ($FULLNAME=="โกเมศ กมลนาวิน") $FULLNAME = "โกเมศ กมลนาวิน ร.น.";
				elseif ($FULLNAME=="วรรัตน์ พิริยนสรณ์") $FULLNAME = "วรรัตน์ พิริยานสรณ์";
				elseif ($FULLNAME=="ชลาทิพย์ อภิวัฒนานุนนท์") $FULLNAME = "ชลาทิพย์ อภิวัฒนานนท์";
				elseif ($FULLNAME=="เบญจมินทร์ สุกาญนที") $FULLNAME = "เบญจมินทร์ สุกาญจนัจที";
				elseif ($FULLNAME=="สายหยุด โชติกะพุกณะ") $FULLNAME = "สายหยุด โชติกะพุกกณะ";
				elseif ($FULLNAME=="วิไล เกษรสุคนธ์") $FULLNAME = "วิไล เกสรสุคนธ์";
				elseif ($FULLNAME=="กาจฐิติ วิวัธนานนท์") $FULLNAME = "กาจฐิติ วิวัธวานนท์";
				elseif ($FULLNAME=="นิตยา เจษฏาฉัตร") $FULLNAME = "นิตยา เจษฎาฉัตร";
				elseif ($FULLNAME=="เกษมสันต์ ทองสิริ") $FULLNAME = "เกษมสันต์ ทองศิริ";
				elseif ($FULLNAME=="วุฒิโรตน์ รัตนะสิงห์") $FULLNAME = "วุฒิโรตม์ รัตนะสิงห์";
				elseif ($FULLNAME=="เจสดา นันทชัยกร") $FULLNAME = "เจสดา นันทชัยพร";
				elseif ($FULLNAME=="รัชฎ่า ถาวรเวช") $FULLNAME = "รัชฎา ถาวรเวช";
				elseif ($FULLNAME=="บัณรสี กออนันตกุล") $FULLNAME = "บัณรสี กออนันตกูล";
				elseif ($FULLNAME=="จุลีพจน์ อิศรางกูรฯ") $FULLNAME = "จุลีพจน์ อิศรางกูร ณ อยุธยา";
				elseif ($FULLNAME=="อเนก พาดา") $FULLNAME = "เอนก พาตา";
				elseif ($FULLNAME=="รสริน มุ่งจิตรธรรมมั่น") $FULLNAME = "รสริน มุ่งจิตธรรมมั่น";
				elseif ($FULLNAME=="เสกสรร สโรบล") $FULLNAME = "เสกสรรค์ สโรบล";
				elseif ($FULLNAME=="วนิดา คุตตวัฒ") $FULLNAME = "วนิดา คุตตวัส";
				elseif ($FULLNAME=="ศันสนีย สหัสสะรังสี") $FULLNAME = "ศันสนีย สหัสสะรังษี";
				elseif ($FULLNAME=="ภัทรนันท์ ภัฒิยะ") $FULLNAME = "ภัทรนันท์ พัฒิยะ";
				elseif ($FULLNAME=="พักตร์ประไพ ต้นธีระวงศ์") $FULLNAME = "พักตร์ประไพ คำบรรลือ";
				elseif ($FULLNAME=="ภาณุฤทธิ์ จำรัสโรมรัน") $FULLNAME = "ปรมะ จำรัสโรมรัน";
				elseif ($FULLNAME=="วีระเดช นิ่มเวชอารมณ์ชื่น") $FULLNAME = "วีระเดช นิ่มเวชอารมย์ชื่น";
				elseif ($FULLNAME=="จักรพันธุ์ ยุวรี") $FULLNAME = "จักรพันธ์ ยุวรี";
				elseif ($FULLNAME=="พสุศิษฎ์ วงศ์สุรวัฒน์") $FULLNAME = "พสุศิษฏ์ วงศ์สุรวัฒน์";
				elseif ($FULLNAME=="อมรพงษ์ ผูกพันธ์") $FULLNAME = "อมรพงษ์ ผูกพัทธิ์";
				elseif ($FULLNAME=="สุวรรณา ผูกพัทธ์") $FULLNAME = "สุวรรณา ผูกพัทธิ์";
				elseif ($FULLNAME=="ลลนา ชัชวาล") $FULLNAME = "ลลนา ชัชวาลย์";
				elseif ($FULLNAME=="อรรถยุทธิ์ ศรีสมุทร") $FULLNAME = "อรรถยุทธ์ ศรีสมุทร";
				elseif ($FULLNAME=="ภควัตร ตันสกุล") $FULLNAME = "ภควัต ตันสกุล";
				elseif ($FULLNAME=="นิภา กิตติคุณไกรสร") $FULLNAME = "พิมพ์นิภา กิตติคุณไกรสร";
				elseif ($FULLNAME=="เชิดชาย ไช้ไววิทย์") $FULLNAME = "เชิดชาย ใช้ไววิทย์";
				elseif ($FULLNAME=="ขนิษฐา สมิทธิ์สมบูรณ์") $FULLNAME = "ขนิษฐา สมิทธ์สมบูรณ์";
				elseif ($FULLNAME=="เปายี แวสะเเม") $FULLNAME = "เปายี แวสะแม";
				elseif ($FULLNAME=="ธัญญรัตน์ มังคลรังสี") $FULLNAME = "ธัญญรัตน์ มังคลรังษี";
				elseif ($FULLNAME=="สมหวัง เครือสุวรรณ") $FULLNAME = "สมหวัง เครือสุวรรณ์";
				elseif ($FULLNAME=="ภาณุพันธ์ โชติรังสียกุล") $FULLNAME = "ภาณุพันธ์ โชติรังสียากุล";
				elseif ($FULLNAME=="ชวนาถ ทั้งสัมพันธ์") $FULLNAME = "ชวนาถ ทั่งสัมพันธ์";
				elseif ($FULLNAME=="นาฎพร นิติมนตรี") $FULLNAME = "นาฏพร นิติมนตรี";
				elseif ($FULLNAME=="วัลลภา จิตร์สมบูรณ์") $FULLNAME = "วัลลภา จิตรสมบูรณ์";
				elseif ($FULLNAME=="นภาพร รุณภัย") $FULLNAME = "ปาณัสม์ รุณภัย";
				elseif ($FULLNAME=="เปรียบทิพย์ ภีนานนท์") $FULLNAME = "เปรียบทิพย์ กฤษณามระ";
				elseif ($FULLNAME=="วิภา พันธ์อินทร์") $FULLNAME = "วิภา บุญสุด";
				elseif ($FULLNAME=="ไกรรวี สิริกุล") $FULLNAME = "ไกรรวี ศิริกุล";
				elseif ($FULLNAME=="อรวรรณ หวังมาน") $FULLNAME = "อรวรรณ ญาณทศศิลป์";
				elseif ($FULLNAME=="พรทิพย์ หอมสุวรรณ") $FULLNAME = "พรทิพย์ กวีกิจปัตติพร";
				elseif ($FULLNAME=="กนกวรรณ สุจริตกุล") $FULLNAME = "กนกวรรณ เพ่งสุวรรณ";
				elseif ($FULLNAME=="ชมพูนุช อรรถไกลวัลวที") $FULLNAME = "ชมพูนุช ชมพูคำ";
				elseif ($FULLNAME=="อรวรรณ นิยะโต") $FULLNAME = "อรวรรณ วิวัฒนทรัพย์";
				elseif ($FULLNAME=="สุนันทา นิ่มกุล") $FULLNAME = "สุนันทา เรือนแก้ว";
				elseif ($FULLNAME=="จิราพร พ่วงชูศักดิ์") $FULLNAME = "ชลันธรณ์ พ่วงชูศักดิ์";
				elseif ($FULLNAME=="ยุวัน ชินะโชติ เปลี่ยนชื่อเป็น พัสสนัญช์") $FULLNAME = "ยุวัญ ชินะโชติ";
				elseif ($FULLNAME=="วชิราภรณ์ ภิระวานิช") $FULLNAME = "วชิราภรณ์ กิระวานิช";
				elseif ($FULLNAME=="อรุณรัตน์ คงเขียว") $FULLNAME = "อรุณรัตน์ จินดา";
				elseif ($FULLNAME=="ปราณี มณีธนู") $FULLNAME = "ปราณี บาร์คเลย์";
				elseif ($FULLNAME=="เจติยา พันธุ์ภักดีดิสกุล") $FULLNAME = "เจติยา เส็งดอนไพร";
				elseif ($FULLNAME=="ณัฏฐินี ศาลิคุปต์") $FULLNAME = "ณัฎฐินี บุญลือ";
				elseif ($FULLNAME=="ฉัตรวดี ออรัตนชัย") $FULLNAME = "ฉัตรวดี จินดาวงษ์";

				$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME||' '||PER_SURNAME=trim('$FULLNAME') ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;
				$PER_CARDNO = trim($data2[PER_CARDNO]);

				if ($PER_ID == 0) {
					$cmd = " select PER_ID, PER_CARDNO from PER_NAMEHIS where NH_NAME||' '||NH_SURNAME=trim('$FULLNAME') ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PER_ID = $data2[PER_ID] + 0;
					$PER_CARDNO = trim($data2[PER_CARDNO]);
				}

				if ($PER_ID > 0) {
					$POST_POSITION = trim($fields[1]);
					$POST_ORG_NAME = trim($fields[2]);
					$ENDDATE = trim($fields[3]);
					$arr_temp = explode("-", $ENDDATE);
					$dd = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					if (trim($arr_temp[1])=="มกราคม" || trim($arr_temp[1])=="ม.ค." || trim($arr_temp[1])=="ม.ค" || trim($arr_temp[1])=="Jan") $mm = "01";
					elseif (trim($arr_temp[1])=="กุมภาพันธ์" || trim($arr_temp[1])=="กุมภาภันธ์" || trim($arr_temp[1])=="กุมหภาพันธ์" || trim($arr_temp[1])=="ก.พ." || trim($arr_temp[1])=="กพ." || trim($arr_temp[1])=="Feb") $mm = "02";
					elseif (trim($arr_temp[1])=="มีนาคม" || trim($arr_temp[1])=="มี.ค." || trim($arr_temp[1])=="Mar") $mm = "03";
					elseif (trim($arr_temp[1])=="เมษายน" || trim($arr_temp[1])=="เม.ย." || trim($arr_temp[1])=="Apr") $mm = "04";
					elseif (trim($arr_temp[1])=="พฤษภาคม" || trim($arr_temp[1])=="พ.ค." || trim($arr_temp[1])=="May") $mm = "05";
					elseif (trim($arr_temp[1])=="มิถุนายน" || trim($arr_temp[1])=="มิ.ย." || trim($arr_temp[1])=="Jun") $mm = "06";
					elseif (trim($arr_temp[1])=="กรกฎาคม" || trim($arr_temp[1])=="ก.ค." || trim($arr_temp[1])=="Jul") $mm = "07";
					elseif (trim($arr_temp[1])=="สิงหาคม" || trim($arr_temp[1])=="ส.ค." || trim($arr_temp[1])=="Aug") $mm = "08";
					elseif (trim($arr_temp[1])=="กันยายน" || trim($arr_temp[1])=="ก.ย." || trim($arr_temp[1])=="Sep") $mm = "09";
					elseif (trim($arr_temp[1])=="ตุลาคม" || trim($arr_temp[1])=="ต.ค." || trim($arr_temp[1])=="ต.คย." || trim($arr_temp[1])=="ตค" || trim($arr_temp[1])=="Oct") $mm = "10";
					elseif (trim($arr_temp[1])=="พฤศจิกายน" || trim($arr_temp[1])=="พ.ย." || trim($arr_temp[1])=="พย" || trim($arr_temp[1])=="Nov") $mm = "11";
					elseif (trim($arr_temp[1])=="ธันวาคม" || trim($arr_temp[1])=="ธันวาคา" || trim($arr_temp[1])=="ธ.ค." || trim($arr_temp[1])=="Dec") $mm = "12";
					else echo "$arr_temp[0]**$arr_temp[1]**$arr_temp[2]<br>";
					$yy = trim($arr_temp[2]) + 600 - 543;
					$POST_ENDDATE = $yy."-".$mm."-".$dd;
					$ENDTIME = trim($fields[4]);
					$arr_temp = explode(".", $ENDTIME);
					$hh = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$ms = trim($arr_temp[1]);
					if (!$ms) $ms = "00";
					$ms = str_pad($ms, 2, "0", STR_PAD_RIGHT);
					$POST_ENDTIME = $hh.":".$ms;
					$POST_TEL = trim($fields[5]);
					if ($POST_TEL) $POST_TEL .= ", ";
					$POST_TEL .= trim($fields[6]);
					$POST_REMARK = trim($fields[7]);
					if ($POST_ENDTIME=="10:00 น") $POST_ENDTIME = "10:00";
					if ($POST_ENDTIME=="ลาออก:00") {
						$POST_ENDTIME = "";
						$POST_REMARK = "ลาออก";
					}
					if ($POST_TEL=="สน.นโยบาย, กลับจากลาเรียนอเมริกา") {
						$POST_ORG_NAME = $POST_TEL;
						$POST_TEL = "";
					}
				
					$cmd = " INSERT INTO PER_POSTINGHIS(POST_ID, PER_ID, PER_CARDNO, POST_STARTDATE, POST_ENDDATE, POST_ENDTIME, 
									POST_POSITION, POST_ORG_NAME, 	POST_TEL, POST_REMARK, UPDATE_USER, UPDATE_DATE)
									VALUES ($MAX_ID, $PER_ID, '$PER_CARDNO', '$POST_STARTDATE', '$POST_ENDDATE', '$POST_ENDTIME', '$POST_POSITION', 
									'$POST_ORG_NAME', '$POST_TEL', '$POST_REMARK', $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT POST_ID FROM PER_POSTINGHIS WHERE POST_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					} else {
						$MAX_ID++;
						$PER_POSTINGHIS = $MAX_ID;    
					}
				} elseif ($FULLNAME) echo "ไม่พบข้อมูล $FULLNAME<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="REWARD" && is_file($RealFile)){
		$cmd = " select max(REH_ID) as MAX_ID from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$FULLNAME = trim($fields[1]);
				$FULLNAME = str_replace("  ", " ", $FULLNAME);
				if (substr($FULLNAME,0,3)=="นาย") $FULLNAME = substr($FULLNAME,3);
				elseif (substr($FULLNAME,0,4)=="น.ส.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ร.ท.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ม.ล.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="น.ต.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ร.ต.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="ร.อ.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,6)=="ร้อยโท") $FULLNAME = trim(substr($FULLNAME,6));
				elseif (substr($FULLNAME,0,6)=="เรือโท") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,7)=="เรือตรี") $FULLNAME = substr($FULLNAME,7);
				elseif (substr($FULLNAME,0,6)=="สิบเอก") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="นางสาว") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="ม.ร.ว.") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,12)=="หม่อมราชวงศ์") $FULLNAME = substr($FULLNAME,12);
				elseif (substr($FULLNAME,0,9)=="หม่อมหลวง") $FULLNAME = substr($FULLNAME,9);
				elseif (substr($FULLNAME,0,3)=="นาง") $FULLNAME = substr($FULLNAME,3);
				$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME||' '||PER_SURNAME=trim('$FULLNAME') ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PER_ID = $data2[PER_ID] + 0;
				$PER_CARDNO = trim($data2[PER_CARDNO]);

				if ($PER_ID == 0) {
					$cmd = " select PER_ID, PER_CARDNO from PER_NAMEHIS where NH_NAME||' '||NH_SURNAME=trim('$FULLNAME') ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PER_ID = $data2[PER_ID] + 0;
					$PER_CARDNO = trim($data2[PER_CARDNO]);
				}

				if ($PER_ID > 0) {
					$REH_ORG = trim($fields[2]);
					$REH_YEAR = trim($fields[4]);
					$REH_PERFORMANCE = "ข้าราชการดีเด่น ประจำปี".$REH_YEAR;
					$REH_DATE = ($REH_YEAR-542)."-04-01";
				
					$cmd = " INSERT INTO PER_REWARDHIS (REH_ID, PER_ID, REH_DATE, REW_CODE, REH_ORG, REH_DOCNO, 
									UPDATE_USER, 	UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE)
									VALUES ($MAX_ID, $PER_ID, '$REH_DATE', '03', '$REH_ORG', '$REH_DOCNO', 
									$UPDATE_USER, '$UPDATE_DATE', '$PER_CARDNO', '$REH_YEAR', '$REH_PERFORMANCE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT REH_ID FROM PER_REWARDHIS WHERE REH_ID = $MAX_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					} else {
						$MAX_ID++;
						$PER_REWARDHIS = $MAX_ID;    
					}
				} elseif ($FULLNAME) echo "ไม่พบข้อมูล $FULLNAME<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="POSITIONHIS"){
		$cmd = " update per_positionhis set poh_ass_org = replace(poh_ass_org, 'เอเซีย', 'เอเชีย') where poh_ass_org like '%เอเซีย%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = replace(poh_ass_department, 'เอเซีย', 'เอเชีย') where poh_ass_org like '%เอเซีย%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_ass_org, poh_ass_org = '' 
where poh_ass_department is null and  poh_ass_org in 
('สำนักงานรัฐมนตรี', 'สำนักงานปลัดกระทรวงการต่างประเทศ', 'กรมการกงสุล', 'กรมพิธีการทูต', 'กรมยุโรป', 'กรมเศรษฐกิจระหว่างประเทศ', 'กรมสนธิสัญญาและกฎหมาย', 'กรมสารนิเทศ', 'กรมองค์การระหว่างประเทศ', 'กรมอเมริกาและแปซิฟิกใต้', 'กรมอาเซียน', 'กรมเอเชียตะวันออก', 'กรมเอเชียใต้ ตะวันออกกลางและแอฟริกา', 'กรมวิเทศสหการ') and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'สำนักงานปลัดกระทรวงการต่างประเทศ' 
where poh_ass_department is null and  (poh_ass_org like 'สถานเอกอัครราชทูต%'  or poh_ass_org like 'สถานกงสุลใหญ่%') and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'สำนักงานรัฐมนตรี' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2953) and  poh_ass_org != 'ส่วนกลาง' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'สำนักงานปลัดกระทรวงการต่างประเทศ' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2954) and  poh_ass_org != 'ส่วนกลาง' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมการกงสุล' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3101) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมพิธีการทูต' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2955) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมยุโรป' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3103) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมเศรษฐกิจระหว่างประเทศ' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3105) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมสนธิสัญญาและกฎหมาย' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2956) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมสารนิเทศ' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2957) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมองค์การระหว่างประเทศ' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2958) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมอเมริกาและแปซิฟิกใต้' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3109) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมอาเซียน' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2959) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมเอเชียตะวันออก' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3111) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'กรมเอเชียใต้ ตะวันออกกลางและแอฟริกา' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3112) and  poh_ass_org != 'ส่วนกลาง' and  poh_ass_org != 'สำนักงานเลขานุการกรม' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_ass_org , poh_ass_org = poh_ass_org1 , poh_ass_org1 = poh_ass_org2 , poh_ass_org2 = poh_ass_org3 where poh_ass_department is null and  poh_ass_org is not null and poh_ass_org1 is not null  and poh_ass_org2 is not null and poh_ass_org in ('กรมการกงสุล', 'กรมพิธีการทูต', 'กรมสารนิเทศ', 'กรมอาเซียน', 'กรมเอเชียตะวันออก', 'สำนักงานปลัดกระทรวง', 'สำนักงานปลัดกระทรวงการต่างประเทศ') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = 'สำนักงานปลัดกระทรวงการต่างประเทศ' where poh_ass_department is null and  poh_ass_org is not null and poh_ass_org1 is not null  and poh_ass_org2 is not null and poh_ass_org in ('กองกลาง', 'กองการเจ้าหน้าที่และฝึกอบรม', 'กองการพัสดุ', 'กองคลัง', 'ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร', 'สำนักจัดหาและบริหารทรัพย์สิน', 'สำนักนโยบายและแผน', 'สำนักบริหารการคลัง', 'สำนักบริหารบุคคล', 'สำนักผู้อำนวยการ') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_org2 where poh_ass_org = 'สำนักงานเลขานุการกรม' and poh_ass_department is null and  poh_ass_org1 is null  and poh_ass_org2 is null and poh_last_position = 'Y' and per_id in (select per_id from per_personal where per_type = 1 and per_status = 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

	} // end if

	if( $command=='VACATION' ){
/*		$cmd = " SELECT ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY
						FROM PER_ABSENTHIS
						ORDER BY PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, UPDATE_DATE ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){
			$ABS_ID = $data[ABS_ID];
			$PER_ID = $data[PER_ID];
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];
			$ABS_DAY = $data[ABS_DAY]+0;
			if ($PER_ID==$TMP_PER_ID && $AB_CODE==$TMP_AB_CODE && $ABS_STARTDATE==$TMP_ABS_STARTDATE && $ABS_STARTPERIOD==$TMP_ABS_STARTPERIOD && 
				$ABS_ENDDATE==$TMP_ABS_ENDDATE && $ABS_ENDPERIOD==$TMP_ABS_ENDPERIOD) {
				if ($TMP_ABS_DAY==0) $cmd = " delete from PER_ABSENTHIS where ABS_ID = $TMP_ABS_ID ";
				else $cmd = " delete from PER_ABSENTHIS where ABS_ID = $ABS_ID ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
			}
			$TMP_ABS_ID = $ABS_ID;
			$TMP_PER_ID = $PER_ID;
			$TMP_AB_CODE = $AB_CODE;
			$TMP_ABS_STARTDATE = $ABS_STARTDATE;
			$TMP_ABS_STARTPERIOD = $ABS_STARTPERIOD;
			$TMP_ABS_ENDDATE = $ABS_ENDDATE;
			$TMP_ABS_ENDPERIOD = $ABS_ENDPERIOD;
			$TMP_ABS_DAY = $ABS_DAY;
		}
*/
		$cmd = " truncate table PER_VACATION ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " SELECT Employee.EmpID, Employee.IDCard, Employee.EmpThaiName, Employee.EmpThaiSurName, Employee.EmpEngName, 
											Employee.EmpEngSurName, Year(AbsentTotal.YearRecord) AS FiscalYear, AbsentTotal.RestDay
						FROM AbsentTotal LEFT JOIN Employee ON AbsentTotal.EmpID = Employee.EmpID
						ORDER BY Employee.EmpID, Year(AbsentTotal.YearRecord) ";
		$db_dpis35->send_cmd($cmd);
//						WHERE EmpThaiName='อริยาวรรณ'
//		$db_dpis35->show_error();
//		echo "<br>";
		while($data = $db_dpis35->get_array()){
			$EmpID = trim($data[EmpID]);
			$PER_CARDNO = trim($data[IDCard]);
			$FnameT = trim($data[EmpThaiName]);
			$LnameT = trim($data[EmpThaiSurName]);
			if ($PER_CARDNO=="3104600185803") $PER_CARDNO = "3100600185803";
			elseif ($PER_CARDNO=="3100202504125") $PER_CARDNO = "3102002504125";
			elseif ($PER_CARDNO=="310140083010") $PER_CARDNO = "3101400883010";
			elseif ($PER_CARDNO=="3101700588751") $PER_CARDNO = "3101700532751";
			elseif ($PER_CARDNO=="3100901911710") $PER_CARDNO = "3100901911716";
			elseif ($PER_CARDNO=="3100901818449") $PER_CARDNO = "3100901818465";
			elseif ($PER_CARDNO=="3100500183090") $PER_CARDNO = "3100500193090";
			elseif ($PER_CARDNO=="3100202805037") $PER_CARDNO = "3100202808037";
			elseif ($PER_CARDNO=="310700308860") $PER_CARDNO = "3101700308860";
			elseif ($PER_CARDNO=="3702500030446") $PER_CARDNO = "3720500030446";
			elseif ($PER_CARDNO=="3941000316049") $PER_CARDNO = "3940100316049";
			elseif ($PER_CARDNO=="3100200298629") $PER_CARDNO = "3102200298629";
			elseif ($PER_CARDNO=="3200100721135") $PER_CARDNO = "3200100921135";
			elseif ($PER_CARDNO=="330010058784") $PER_CARDNO = "3300100508784";
			elseif ($PER_CARDNO=="3100601584882") $PER_CARDNO = "3100601584914";
			elseif ($PER_CARDNO=="3161401820585") $PER_CARDNO = "3101401820585";
			elseif ($PER_CARDNO=="3120622578015") $PER_CARDNO = "3120600578015";
			elseif ($PER_CARDNO=="3100200270001") $PER_CARDNO = "3100500270001";
			elseif ($PER_CARDNO=="310202242450") $PER_CARDNO = "3101202242450";
			elseif ($PER_CARDNO=="3100200057842") $PER_CARDNO = "3102200057842";
			$cmd = " select PER_ID, PER_STARTDATE from PER_PERSONAL where PER_CARDNO='$PER_CARDNO' or (PER_NAME='$FnameT' and PER_SURNAME='$LnameT') ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PER_ID = $data2[PER_ID] + 0;

			if ($PER_ID > 0) {
				$search_vc_year = trim($data[FiscalYear]);
				$REST_DAY = $data[RestDay];
				$CHECK_DATE = ($search_vc_year-543)."-10-01";
				$TMP_START_DATE = ($search_vc_year-1-544)."-10-01";
				$TMP_END_DATE = ($search_vc_year-1-543)."-09-30";
				$tmp_vc_year = $search_vc_year - 1;

				$PER_STARTDATE = trim($data2[PER_STARTDATE]);
				$AGE_DIFF = date_difference($CHECK_DATE, $PER_STARTDATE, "full");
				$arr_temp = explode(" ", $AGE_DIFF);
				$total_year = $arr_temp[0];
				$total_month = $arr_temp[2];
				$total_day = $arr_temp[4];
				if ($total_month > 0) $total_year += $total_month / 12;
				if ($total_day > 0) $total_year += $total_day / 365;
				
				$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
								  WHERE AC_FLAG = 1 AND $total_year < AC_GOV_AGE ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//echo "$cmd<br>";
				//$db_dpis1->show_error();
				if (!$count_data) {
					$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
									  WHERE AC_FLAG = 2 AND $total_year >= AC_GOV_AGE ";
					$count_data = $db_dpis1->send_cmd($cmd);
					//echo "$cmd<br>";
					//$db_dpis1->show_error();
				}
				$data1 = $db_dpis1->get_array();
				$AC_DAY = $data1[AC_DAY];
				$AC_COLLECT = $data1[AC_COLLECT];

				$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$TMP_AB_CODE_04 = $data1[abs_day]+0; 

				$cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
								where PER_ID=$PER_ID and START_DATE >= '$TMP_START_DATE' and END_DATE <= '$TMP_END_DATE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$TMP_AB_CODE_04 += $data1[abs_day]+0; 
		
				$cmd = " select VC_DAY from PER_VACATION 
								where VC_YEAR='$tmp_vc_year'and PER_ID=$PER_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$VC_DAY = $data1[VC_DAY]; 
				if ($VC_DAY && ($VC_DAY - $TMP_AB_CODE_04) > 0) $AC_DAY += $VC_DAY - $TMP_AB_CODE_04;
				if ($AC_DAY > $AC_COLLECT) $AC_DAY = $AC_COLLECT;

				$cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
								values ('$search_vc_year', $PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
//				echo "-> $cmd <hr>";  
			} else {
				if (!$PER_CARDNO || $PER_CARDNO != $TMP_PER_CARDNO) {
					if ($PER_CARDNO)
						echo "$EmpID - $PER_CARDNO - $FnameT - $LnameT<br>"; 
					else
						echo "$FnameT $LnameT<br>"; 
					$TMP_PER_CARDNO = $PER_CARDNO;
				}
			}
		} // end while						
	} // end if

	if( $command=='GENABSENTSUM' ){
// สร้างสรุปวันลาสะสม *******************************************************
		$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AS_ID = $data[max_id] + 1;
					
		$cmd = " SELECT distinct a.PER_ID, PER_STARTDATE, a.PER_CARDNO 
						  FROM PER_PERSONAL a, PER_ABSENTSUM b
						  WHERE a.PER_ID=b.PER_ID AND PER_TYPE = 1 AND PER_STATUS = 1
						  ORDER BY a.PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
			$PER_CARDNO = trim($data[PER_CARDNO]);

			$cmd="select   START_DATE from PER_ABSENTSUM where PER_ID=$PER_ID ORDER BY START_DATE ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PER_STARTDATE = trim($data1[START_DATE]);

			if (substr($PER_STARTDATE,5,2) > "09" || substr($PER_STARTDATE,5,2) < "04") $AS_CYCLE = 1;
			elseif (substr($PER_STARTDATE,5,2) > "03" && substr($PER_STARTDATE,5,2) < "10")	$AS_CYCLE = 2;
			$TEMP_YEAR = substr($PER_STARTDATE, 0, 4);
			$UPDATE_YEAR = "2014";
			for ( $AS_YEAR=$TEMP_YEAR; $AS_YEAR<=$UPDATE_YEAR; $AS_YEAR++ ) { 
				$TMP_AS_YEAR = $AS_YEAR;
				$AS_YEAR_BDH = $TMP_AS_YEAR + 543;

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR_BDH' and AS_CYCLE = 1 ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$START_DATE = ($TMP_AS_YEAR - 1) . "-10-01";
					$END_DATE = $TMP_AS_YEAR . "-03-31";
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$AS_YEAR_BDH', 1, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$AS_ID++;
				}

				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$AS_YEAR_BDH' and AS_CYCLE = 2 ";
				$count=$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$START_DATE = $TMP_AS_YEAR . "-04-01";
					$END_DATE = $TMP_AS_YEAR . "-09-30"; 
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$AS_YEAR_BDH', 2, '$START_DATE', '$END_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
					$AS_ID++;
				}
			}
		} // end while						
	} // end if

	if( $command=='GENVACATION' ){
// สร้างวันลาพักผ่อนสะสม *******************************************************
		$cmd = " SELECT distinct a.PER_ID, AS_YEAR, PER_STARTDATE 
						FROM PER_ABSENTSUM a, PER_PERSONAL b
						WHERE a.PER_ID=b.PER_ID
						ORDER BY a.PER_ID, AS_YEAR ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "<br>";
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$search_vc_year = trim($data[AS_YEAR]);
			$cmd="select   VC_YEAR from PER_VACATION where PER_ID=$PER_ID and AS_YEAR = '$search_vc_year' ";
			$count=$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
			if(!$count) { 
				$CHECK_DATE = ($search_vc_year-543)."-10-01";
				$TMP_START_DATE = ($search_vc_year-1-544)."-10-01";
				$TMP_END_DATE = ($search_vc_year-1-543)."-09-30";
				$tmp_vc_year = $search_vc_year - 1;

				$PER_STARTDATE = trim($data[PER_STARTDATE]);
				$AGE_DIFF = date_difference($CHECK_DATE, $PER_STARTDATE, "full");
				$arr_temp = explode(" ", $AGE_DIFF);
				$total_year = $arr_temp[0];
				$total_month = $arr_temp[2];
				$total_day = $arr_temp[4];
				if ($total_month > 0) $total_year += $total_month / 12;
				if ($total_day > 0) $total_year += $total_day / 365;
				
				$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
								  WHERE AC_FLAG = 1 AND $total_year < AC_GOV_AGE ";
				$count_data = $db_dpis1->send_cmd($cmd);
				//echo "$cmd<br>";
				//$db_dpis1->show_error();
				if (!$count_data) {
					$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
									  WHERE AC_FLAG = 2 AND $total_year >= AC_GOV_AGE ";
					$count_data = $db_dpis1->send_cmd($cmd);
					//echo "$cmd<br>";
					//$db_dpis1->show_error();
				}
				$data1 = $db_dpis1->get_array();
				$AC_DAY = $data1[AC_DAY];
				$AC_COLLECT = $data1[AC_COLLECT];

				$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
								where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$TMP_AB_CODE_04 = $data1[abs_day]+0; 

				$cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
								where PER_ID=$PER_ID and START_DATE >= '$TMP_START_DATE' and END_DATE <= '$TMP_END_DATE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$data1 = array_change_key_case($data1, CASE_LOWER);
				$TMP_AB_CODE_04 += $data1[abs_day]+0; 
		
				$cmd = " select VC_DAY from PER_VACATION 
								where VC_YEAR='$tmp_vc_year'and PER_ID=$PER_ID ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$VC_DAY = $data1[VC_DAY]; 
				if ($VC_DAY && ($VC_DAY - $TMP_AB_CODE_04) > 0) $AC_DAY += $VC_DAY - $TMP_AB_CODE_04;
				if ($AC_DAY > $AC_COLLECT) $AC_DAY = $AC_COLLECT;

				$cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
								values ('$search_vc_year', $PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
	//				$db_dpis1->show_error();
	//				echo "-> $cmd <hr>";  
			} // end if
		} // end while						
	} // end if

	if( $command=='CALABSENT' ){
		$cmd = " SELECT ABS_ID, PER_ID, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, AB_COUNT
						  FROM PER_ABSENTHIS a, PER_ABSENTTYPE b
						  WHERE a.AB_CODE=b.AB_CODE AND ABS_DAY = 0 
						  ORDER BY ABS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "<br>";
		$chkSave = 0;	// คำนวนเฉย ๆ ไม่มีการ save
		while($data = $db_dpis->get_array()){
			$ABS_ID = $data[ABS_ID];
			$PER_ID = $data[PER_ID];
			$AB_CODE = trim($data[AB_CODE]);
			$ABS_STARTDATE = trim($data[ABS_STARTDATE]);
			$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];
			$ABS_ENDDATE = trim($data[ABS_ENDDATE]);
			$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];
			$AB_COUNT = $data[AB_COUNT];

			if (!$ABS_STARTPERIOD) $ABS_STARTPERIOD = "NULL";
			if (!$ABS_ENDPERIOD) $ABS_ENDPERIOD = "NULL";
			$ABS_DAY = find_absent_day($ABS_STARTDATE, $ABS_STARTPERIOD, $ABS_ENDDATE, $ABS_ENDPERIOD, $AB_COUNT, $PER_ID, $chkSave);
			if ($ABS_DAY==0) {
				if ($ABS_ENDDATE > $ABS_STARTDATE)
					$ABS_DAY = 2;
				else
					$ABS_DAY = 1;
			}

			$cmd = " UPDATE PER_ABSENTHIS SET ABS_DAY = $ABS_DAY WHERE ABS_ID = $ABS_ID ";
			$db_dpis1->send_cmd($cmd);

		} // end while						
	} // end if

?>