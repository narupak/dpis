<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("function_find_absent_day.php");

// �Ţ�����˹觤�ͧ 2 �� select pos_id, count(*) from per_personal where per_type=1 and per_status=1 group by pos_id having count(*) > 1

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
				if ($ABSENT_TYPE_NAME=="�һ���") $ABSENT_TYPE = "01";
				elseif ($ABSENT_TYPE_NAME=="�Ҥ�ʹ�ص�") $ABSENT_TYPE = "02";
				elseif ($ABSENT_TYPE_NAME=="�ҡԨ") $ABSENT_TYPE = "03";
				elseif ($ABSENT_TYPE_NAME=="�Ҿѡ��͹��Шӻ�") $ABSENT_TYPE = "04";
				elseif ($ABSENT_TYPE_NAME=="�����;Ը��Ѩ��" || $ABSENT_TYPE_NAME=="���ػ����") $ABSENT_TYPE = "05";
				elseif ($ABSENT_TYPE_NAME=="���֡�ҵ��/�Ѻ�ع/�Ԩ��") $ABSENT_TYPE = "07";
				elseif ($ABSENT_TYPE_NAME=="任گԺѵԧҹ�ͧ��� ���") $ABSENT_TYPE = "08";
				elseif ($ABSENT_TYPE_NAME=="�ҵԴ����������") $ABSENT_TYPE = "09";
				elseif ($ABSENT_TYPE_NAME=="�����") $ABSENT_TYPE = "10";
				elseif ($ABSENT_TYPE_NAME=="����������§�ٺص�") $ABSENT_TYPE = "11";
				elseif ($ABSENT_TYPE_NAME=="�Ҵ�Ҫ���") $ABSENT_TYPE = "13";
				elseif ($ABSENT_TYPE_NAME=="���¶���繡�÷�˹�ҷ��") $ABSENT_TYPE = "19";
				elseif ($ABS_REMARK=="�һ���") $ABSENT_TYPE = "01";
				elseif ($ABS_REMARK=="�Ҿѡ��͹��Шӻ�" || $ABS_REMARK=="�Ҿѡ��͹") $ABSENT_TYPE = "04";
				elseif ($ABS_REMARK=="��仵�ҧ�����") $ABSENT_TYPE = "08";
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
					if ($ABSENT_TYPE_NAME=="�һ���") $ABSENT_TYPE = "01";
					elseif ($ABSENT_TYPE_NAME=="�Ҥ�ʹ�ص�") $ABSENT_TYPE = "02";
					elseif ($ABSENT_TYPE_NAME=="�ҡԨ") $ABSENT_TYPE = "03";
					elseif ($ABSENT_TYPE_NAME=="�Ҿѡ��͹��Шӻ�") $ABSENT_TYPE = "04";
					elseif ($ABSENT_TYPE_NAME=="�����;Ը��Ѩ��" || $ABSENT_TYPE_NAME=="���ػ����") $ABSENT_TYPE = "05";
					elseif ($ABSENT_TYPE_NAME=="���֡�ҵ��/�Ѻ�ع/�Ԩ��") $ABSENT_TYPE = "07";
					elseif ($ABSENT_TYPE_NAME=="任گԺѵԧҹ�ͧ��� ���") $ABSENT_TYPE = "08";
					elseif ($ABSENT_TYPE_NAME=="�ҵԴ����������") $ABSENT_TYPE = "09";
					elseif ($ABSENT_TYPE_NAME=="�����") $ABSENT_TYPE = "10";
					elseif ($ABSENT_TYPE_NAME=="����������§�ٺص�") $ABSENT_TYPE = "11";
					elseif ($ABSENT_TYPE_NAME=="�Ҵ�Ҫ���") $ABSENT_TYPE = "13";
					elseif ($ABSENT_TYPE_NAME=="���¶���繡�÷�˹�ҷ��") $ABSENT_TYPE = "19";
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

				} elseif ($PER_CARDNO) echo "��辺������ $PER_CARDNO<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//���êྨ
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
		//���êྨ
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
				$FULLNAME = str_replace("���͡", "", $FULLNAME);
				$FULLNAME = str_replace("�.�. ", "", $FULLNAME);
				$FULLNAME = str_replace("�.�.", "", $FULLNAME);
				$FULLNAME = str_replace("(���³)", "", $FULLNAME);
				$FULLNAME = str_replace("(�ҵԴ���)", "", $FULLNAME);
				$FULLNAME = str_replace("��ҷ�����µ��", "", $FULLNAME);
				if (substr($FULLNAME,0,3)=="���") $FULLNAME = substr($FULLNAME,3);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,8)=="�.�.˭ԧ") $FULLNAME = substr($FULLNAME,8);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.ʧ") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�ͷ.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,6)=="�ҧ���") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="�.�.�.") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,3)=="�ҧ") $FULLNAME = substr($FULLNAME,3);

				if ($FULLNAME=="�ç��»���ط�") $FULLNAME = "�ç��� ��»���ط�";
				elseif ($FULLNAME=="��ó��� ������غ�ó� �Ѳ�������ظ") $FULLNAME = "��ó���  ������غ�ó� �Ѳ�������ظ";
				elseif ($FULLNAME=="���յ� �ػ�д�ɰ� � ��ظ��") $FULLNAME = "���յ� �ػ�д�ɰ � ��ظ��";
				elseif ($FULLNAME=="��������� ������ʴ�� (��ö�Է���)") $FULLNAME = "��������� ������ʴ��";
				elseif ($FULLNAME=="���ҡ� �ԢԤ�Ѳ�����") $FULLNAME = "���ҡ� �ԢԵ�Ѳ�����";
				elseif ($FULLNAME=="���� �Ի�ҹ��� (����¹�� �Ǵ�)") $FULLNAME = "�Ǵ� �Ի�ҹ����";
				elseif ($FULLNAME=="�����Ѳ�� �ѹ�кص�") $FULLNAME = "������Ѳ�� �ѹ�кص�";
				elseif ($FULLNAME=="���԰ �ԪԵ���") $FULLNAME = "���ԯ�� �ԪԵ���";
				elseif ($FULLNAME=="��Ѫ ��ѭ�Ե�") $FULLNAME = "��Ѫ ��ѭ�Ե��";
				elseif ($FULLNAME=="�ѹ����ѵ�� ������") $FULLNAME = "�ѹ���ѵ�� ������";
				elseif ($FULLNAME=="�ؤŸ� ��ø���") $FULLNAME = "�ؤŸ� ���и���";
				elseif ($FULLNAME=="�Ѫ��ó �Ҥ��Թ���") $FULLNAME = "�Ѫ��ó �Ҥ��Թ��� �.�.";
				elseif ($FULLNAME=="�ѹ��� ���ǹԵ��") $FULLNAME = "�ѹ��� ����йԵ";
				elseif ($FULLNAME=="���Ԩѡ��� �����и�ҹء��") $FULLNAME = "���Ԩѡ��� �����и�ҹء��";
				elseif ($FULLNAME=="ʸ� ����ѹ���") $FULLNAME = "ʸ� ����ѹ�� � ��ظ��";
				elseif ($FULLNAME=="����� ���ԡ����") $FULLNAME = "����� ���ԡ����";
				elseif ($FULLNAME=="��ɳ� �ѹ��������԰") $FULLNAME = "��ɳ� �ѹ�������԰";
				elseif ($FULLNAME=="�Ѱ �ԭ��Ѳ��վ") $FULLNAME = "�Ѫ �ԭ��Ѳ��վ";
				elseif ($FULLNAME=="�ʾԹ �ؾѲ��Ե") $FULLNAME = "�ʾԹ �ؾѲ��Ե��";
				elseif ($FULLNAME=="�ҵ�� ��èҹѹ��") $FULLNAME = "�ҵ�� ��è�ҹѹ��";
				elseif ($FULLNAME=="��ɳ� ����óЪ�") $FULLNAME = "��ɳ� ����óЪ�";
				elseif ($FULLNAME=="�����Ѳ�� �ѹ�����") $FULLNAME = "����Ѳ�� �ѹ�����";
				elseif ($FULLNAME=="��س��� ⾸��ͧ") $FULLNAME = "��س��� ⾸��ͧ ���������";
				elseif ($FULLNAME=="�ү��ҧ�� ��ç�ع�ê��") $FULLNAME = "�Ү��ҧ�� ��ç�ع�ê��";
				elseif ($FULLNAME=="��Ե �ҹԵ���") $FULLNAME = "��Ե �ҹԵ¡��";
				elseif ($FULLNAME=="����ɺ� �ح�Թѹ��") $FULLNAME = "����ɰ� �ح�Թѹ��";
				elseif ($FULLNAME=="���ô� ͹Ф���ҹ���") $FULLNAME = "���ô� ͹ؤ���ҹ���";
				elseif ($FULLNAME=="����Ѵ �ٵ�� ����") $FULLNAME = "����Ѵ�ٵ�� ����";
				elseif ($FULLNAME=="���ʾ� �ѷ�ժ�����") $FULLNAME = "���ʾ� �ѷ�ժ���";
				elseif ($FULLNAME=="����Է�� ��������") $FULLNAME = "����Է��� ��������";
				elseif ($FULLNAME=="����Ѩ�� ��Թ��ҧ����") $FULLNAME = "����Ѩ�� ��Թ��ҧ��� � ��ظ��";
				elseif ($FULLNAME=="����ԭ ෾��ʴԹ�") $FULLNAME = "����ԭ ෾��ʴԹ � ��ظ��";
				elseif ($FULLNAME=="�ҭ�ѭ ��ѭ�Ѳ�ҡԨ") $FULLNAME = "�ҭ��� ��ѭ�Ѳ�ҡԨ";
				elseif ($FULLNAME=="�èѡ� ��ó����ķ���") $FULLNAME = "�èѡ� ��ó����ķ��";
				elseif ($FULLNAME=="��þ��� �١�ѷ��") $FULLNAME = "��þ��� �١�ѷ���";
				elseif ($FULLNAME=="������ �Է����") $FULLNAME = "������ �Է�����";
				elseif ($FULLNAME=="�ؾ��� ����ҧ����") $FULLNAME = "�ؾ��� ����ҧ��� � ��ظ��";
				elseif ($FULLNAME=="�Է�� ��ɰ��è�") $FULLNAME = "�Է�� ��ɰ��è� �.�.";
				elseif ($FULLNAME=="���� �ҮФ���") $FULLNAME = "���� �үФ���";
				elseif ($FULLNAME=="��ɡ� �ġ�쾧��") $FULLNAME = "��ɡ� �ġɾ���";
				elseif ($FULLNAME=="ʹ�蹪ҵ� ෾��ʴԹ�") $FULLNAME = "ʹ�蹪ҵ� ෾��ʴԹ � ��ظ��";
				elseif ($FULLNAME=="����ѡ��� �ش��Ҵ��") $FULLNAME = "����ѡ��� �ش���ʴ��";
				elseif ($FULLNAME=="�������ɰ ����������԰") $FULLNAME = "�������ɰ� ����������԰";
				elseif ($FULLNAME=="ǹ��� ��������") $FULLNAME = "ǹ��� �����ྪ�";
				elseif ($FULLNAME=="ອ��Թ��� �ءҭ��ѷ��") $FULLNAME = "ອ��Թ��� �ءҭ��Ѩ��";
				elseif ($FULLNAME=="�Թ�� ��ҧ������ѡ��") $FULLNAME = "�Թ�� ��ҧ��������ѡ��";
				elseif ($FULLNAME=="��ͧ��ɰ �ѡ����ԭ") $FULLNAME = "��ͧ���ɰ �ѡ����ԭ";
				elseif ($FULLNAME=="�Ѵ������� �ͧ⤡��Ǵ") $FULLNAME = "����ô� �ͧ⤡��Ǵ";
				elseif ($FULLNAME=="���վ��� ����ҧ����") $FULLNAME = "���վ��� ����ҧ��� � ��ظ��";
				elseif ($FULLNAME=="�����ɮ� ǧ������Ѳ��") $FULLNAME = "�����ɯ� ǧ������Ѳ��";
				elseif ($FULLNAME=="���ѡɳ� ��žѹ��") $FULLNAME = "���ѡɳ� ��žѹ���";
				elseif ($FULLNAME=="��ó�� �ѹ�������") $FULLNAME = "��ó��� �ѹ�������";
				elseif ($FULLNAME=="�ѷ���� �����") $FULLNAME = "�ѷ���� �����";
				elseif ($FULLNAME=="�Ҿѹ�쪹Ե �Ԫ�´�ɰ") $FULLNAME = "�Ҿѹ�쪹Ե� �Ԫ�´�ɰ";
				elseif ($FULLNAME=="���� ���") $FULLNAME = "���� ��� ��»���ط�";
				elseif ($FULLNAME=="���� �Ե���վѹ��") $FULLNAME = "���� �յ���վѹ���";
				elseif ($FULLNAME=="����� �ѷ÷�Ѿ��") $FULLNAME = "����� �ѹ�÷�Ѿ��";
				elseif ($FULLNAME=="�����ó� ����ѷ�ҹѹ��") $FULLNAME = "�����ó� �����ѷ�ҹѹ��";
				elseif ($FULLNAME=="�Ѻ�������� �ҵ��е�") $FULLNAME = "�Ѻ�������� �ҵ��д�";
				elseif ($FULLNAME=="���õԾ� �Ѫþ�") $FULLNAME = "���õԾ� �Ѫ�С�";
				elseif ($FULLNAME=="���е� ���Ե�") $FULLNAME = "���е� ���Ե� �ѹ����";
				elseif ($FULLNAME=="�ԷԵ ����Ѳ��آ") $FULLNAME = "�ԷԵ ����Ѳ���آ";
				elseif ($FULLNAME=="�ķ�Ծ��� �س������") $FULLNAME = "�ķ�Ծ��� �س���⡺�";
				elseif ($FULLNAME=="�����Ѫ�� �ѵ��ġ��") $FULLNAME = "�����Ѫ�� �ѵ��ġ��";
				elseif ($FULLNAME=="વ ���оѲ��") $FULLNAME = "વ ��þѲ��";
				elseif ($FULLNAME=="�ط�� �ѳ������ʴ��") $FULLNAME = "�ط�� �ѹ������ʴ��";
				elseif ($FULLNAME=="�Һ��� �Թ��") $FULLNAME = "�Һ��� �Թ��";
				elseif ($FULLNAME=="�����Գ� �ԭ��Է��") $FULLNAME = "�����Թ� �ԭ��Է��";
				elseif ($FULLNAME=="�ѹ������ ����С��") $FULLNAME = "�ѹ���Ծ� ����С��";
				elseif ($FULLNAME=="ອ��ó �ʵШԹ��") $FULLNAME = "ອ���ó �ʵШԹ��";
				elseif ($FULLNAME=="��ҧ�Ծ�� �ҭ���ѵ�Ԩ") $FULLNAME = "��ҧ�Ծ�� �ҭ���ѵ��Ԩ";
				elseif ($FULLNAME=="��¾ѹ�� ͵�ᾷ��") $FULLNAME = "��¾ѹ��� ͵�ᾷ�� �.�.";
				elseif ($FULLNAME=="�⹷�� ����Ե��") $FULLNAME = "�⹷�� ����Ե�";
				elseif ($FULLNAME=="�չ�� ��������") $FULLNAME = "�չ�� ��������";
				elseif ($FULLNAME=="�Ѯ�� �ع�����") $FULLNAME = "�ѯ�� �ع�����";
				elseif ($FULLNAME=="����Ǵ� ⪵ԡ�ҹ") $FULLNAME = "����Ǵ� ⪵ԡ�ҳ";
				elseif ($FULLNAME=="���� ǧ������") $FULLNAME = "���� ǧ������";
				elseif ($FULLNAME=="��ҭ�Ե�� ���ʡع") $FULLNAME = "��ҭ�Ե�� ���ȡع";
				elseif ($FULLNAME=="��ط��� �ع��ô�") $FULLNAME = "��ط�� �ع��ô�";
				elseif ($FULLNAME=="��ҳ� ǳԪ�ѹ�ʡ��") $FULLNAME = "��ҳ� ǳԪ�ѹ��ʡ��";
				elseif ($FULLNAME=="�Ѱ���� ��Żҹҹ���") $FULLNAME = "�Ѱ���� ��Żҹҹ���";
				elseif ($FULLNAME=="�ž��� �ѧᾹ") $FULLNAME = "�ž��� �ѧᾹ";
				elseif ($FULLNAME=="ǧ��෾ ��ö�����Ƿ�") $FULLNAME = "ǧ��෾ ��ö����Ƿ�";
				elseif ($FULLNAME=="���� ��Ź��Թ") $FULLNAME = "���� ��Ź��Թ �.�.";
				elseif ($FULLNAME=="���ѵ�� ����¹�ó�") $FULLNAME = "���ѵ�� �����ҹ�ó�";
				elseif ($FULLNAME=="��ҷԾ�� ����Ѳ�ҹع���") $FULLNAME = "��ҷԾ�� ����Ѳ�ҹ���";
				elseif ($FULLNAME=="ອ��Թ��� �ءҭ���") $FULLNAME = "ອ��Թ��� �ءҭ��Ѩ��";
				elseif ($FULLNAME=="�����ش ⪵ԡоء��") $FULLNAME = "�����ش ⪵ԡоء���";
				elseif ($FULLNAME=="���� ����ؤ���") $FULLNAME = "���� ����ؤ���";
				elseif ($FULLNAME=="�Ҩ�Ե� ���Ѹ�ҹ���") $FULLNAME = "�Ҩ�Ե� ���Ѹ�ҹ���";
				elseif ($FULLNAME=="�Ե�� �ɯҩѵ�") $FULLNAME = "�Ե�� �ɮҩѵ�";
				elseif ($FULLNAME=="����ѹ�� �ͧ����") $FULLNAME = "����ѹ�� �ͧ����";
				elseif ($FULLNAME=="�ز��õ�� �ѵ���ԧ��") $FULLNAME = "�ز��õ�� �ѵ���ԧ��";
				elseif ($FULLNAME=="�ʴ� �ѹ���¡�") $FULLNAME = "�ʴ� �ѹ���¾�";
				elseif ($FULLNAME=="�Ѫ��� �����Ǫ") $FULLNAME = "�Ѫ�� �����Ǫ";
				elseif ($FULLNAME=="�ѳ��� ��͹ѹ����") $FULLNAME = "�ѳ��� ��͹ѹ����";
				elseif ($FULLNAME=="���վ��� ����ҧ����") $FULLNAME = "���վ��� ����ҧ��� � ��ظ��";
				elseif ($FULLNAME=="�๡ �Ҵ�") $FULLNAME = "�͹� �ҵ�";
				elseif ($FULLNAME=="���Թ ��觨Եø������") $FULLNAME = "���Թ ��觨Ե�������";
				elseif ($FULLNAME=="�ʡ��� ��ú�") $FULLNAME = "�ʡ��ä� ��ú�";
				elseif ($FULLNAME=="ǹԴ� �ص��Ѳ") $FULLNAME = "ǹԴ� �ص����";
				elseif ($FULLNAME=="�ѹʹ�� �������ѧ��") $FULLNAME = "�ѹʹ�� �������ѧ��";
				elseif ($FULLNAME=="�ѷùѹ�� �Ѳ���") $FULLNAME = "�ѷùѹ�� �Ѳ���";
				elseif ($FULLNAME=="�ѡ������ �鹸���ǧ��") $FULLNAME = "�ѡ������ �Ӻ�����";
				elseif ($FULLNAME=="�ҳ�ķ��� ���������ѹ") $FULLNAME = "���� ���������ѹ";
				elseif ($FULLNAME=="����പ �����Ǫ��������") $FULLNAME = "����പ �����Ǫ��������";
				elseif ($FULLNAME=="�ѡþѹ��� �����") $FULLNAME = "�ѡþѹ�� �����";
				elseif ($FULLNAME=="�����ɮ� ǧ������Ѳ��") $FULLNAME = "�����ɯ� ǧ������Ѳ��";
				elseif ($FULLNAME=="��þ��� �١�ѹ��") $FULLNAME = "��þ��� �١�ѷ���";
				elseif ($FULLNAME=="����ó� �١�ѷ��") $FULLNAME = "����ó� �١�ѷ���";
				elseif ($FULLNAME=="�Ź� �Ѫ���") $FULLNAME = "�Ź� �Ѫ�����";
				elseif ($FULLNAME=="��ö�ط��� �����ط�") $FULLNAME = "��ö�ط�� �����ط�";
				elseif ($FULLNAME=="���ѵ� �ѹʡ��") $FULLNAME = "���ѵ �ѹʡ��";
				elseif ($FULLNAME=="���� �Ե�Ԥس����") $FULLNAME = "�������� �Ե�Ԥس����";
				elseif ($FULLNAME=="�Դ��� �����Է��") $FULLNAME = "�Դ��� �����Է��";
				elseif ($FULLNAME=="���ɰ� ��Է�������ó�") $FULLNAME = "���ɰ� ��Է������ó�";
				elseif ($FULLNAME=="���� �������") $FULLNAME = "���� ������";
				elseif ($FULLNAME=="�ѭ��ѵ�� �ѧ���ѧ��") $FULLNAME = "�ѭ��ѵ�� �ѧ���ѧ��";
				elseif ($FULLNAME=="����ѧ ��������ó") $FULLNAME = "����ѧ ��������ó�";
				elseif ($FULLNAME=="�ҳؾѹ�� ⪵��ѧ��¡��") $FULLNAME = "�ҳؾѹ�� ⪵��ѧ���ҡ��";
				elseif ($FULLNAME=="�ǹҶ �������ѹ��") $FULLNAME = "�ǹҶ �������ѹ��";
				elseif ($FULLNAME=="�Ү�� �Ե������") $FULLNAME = "�ү�� �Ե������";
				elseif ($FULLNAME=="������ �Ե������ó�") $FULLNAME = "������ �Ե�����ó�";
				elseif ($FULLNAME=="��Ҿ� �س���") $FULLNAME = "�ҳ���� �س���";
				elseif ($FULLNAME=="���º�Ծ�� �չҹ���") $FULLNAME = "���º�Ծ�� ��ɳ����";
				elseif ($FULLNAME=="���� �ѹ���Թ���") $FULLNAME = "���� �ح�ش";
				elseif ($FULLNAME=="����� ���ԡ��") $FULLNAME = "����� ���ԡ��";
				elseif ($FULLNAME=="����ó ��ѧ�ҹ") $FULLNAME = "����ó �ҳ����Ż�";
				elseif ($FULLNAME=="�÷Ծ�� �������ó") $FULLNAME = "�÷Ծ�� ��աԨ�ѵ�Ծ�";
				elseif ($FULLNAME=="�����ó �ب�Ե���") $FULLNAME = "�����ó ������ó";
				elseif ($FULLNAME=="���ٹت ��ö�����Ƿ�") $FULLNAME = "���ٹت ���٤�";
				elseif ($FULLNAME=="����ó �����") $FULLNAME = "����ó ���Ѳ���Ѿ��";
				elseif ($FULLNAME=="�عѹ�� �������") $FULLNAME = "�عѹ�� ���͹���";
				elseif ($FULLNAME=="���Ҿ� ��ǧ���ѡ���") $FULLNAME = "��ѹ�ó� ��ǧ���ѡ���";
				elseif ($FULLNAME=="���ѹ �Թ�⪵� ����¹������ ���ʹѭ��") $FULLNAME = "���ѭ �Թ�⪵�";
				elseif ($FULLNAME=="Ǫ����ó� �����ҹԪ") $FULLNAME = "Ǫ����ó� �����ҹԪ";
				elseif ($FULLNAME=="��س�ѵ�� ������") $FULLNAME = "��س�ѵ�� �Թ��";
				elseif ($FULLNAME=="��ҳ� ��ո��") $FULLNAME = "��ҳ� ��������";
				elseif ($FULLNAME=="ਵ��� �ѹ����ѡ�մ�ʡ��") $FULLNAME = "ਵ��� ��秴͹��";
				elseif ($FULLNAME=="�ѯ�Թ� ���Ԥػ��") $FULLNAME = "�Ѯ�Թ� �ح���";
				elseif ($FULLNAME=="�ѵ�Ǵ� ���ѵ����") $FULLNAME = "�ѵ�Ǵ� �Թ��ǧ��";

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
					if (trim($arr_temp[1])=="���Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.�" || trim($arr_temp[1])=="Jan") $mm = "01";
					elseif (trim($arr_temp[1])=="����Ҿѹ��" || trim($arr_temp[1])=="������ѹ��" || trim($arr_temp[1])=="�����Ҿѹ��" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="��." || trim($arr_temp[1])=="Feb") $mm = "02";
					elseif (trim($arr_temp[1])=="�չҤ�" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="Mar") $mm = "03";
					elseif (trim($arr_temp[1])=="����¹" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="Apr") $mm = "04";
					elseif (trim($arr_temp[1])=="����Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="May") $mm = "05";
					elseif (trim($arr_temp[1])=="�Զع�¹" || trim($arr_temp[1])=="��.�." || trim($arr_temp[1])=="Jun") $mm = "06";
					elseif (trim($arr_temp[1])=="�á�Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="Jul") $mm = "07";
					elseif (trim($arr_temp[1])=="�ԧ�Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="Aug") $mm = "08";
					elseif (trim($arr_temp[1])=="�ѹ��¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="Sep") $mm = "09";
					elseif (trim($arr_temp[1])=="���Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="�.��." || trim($arr_temp[1])=="��" || trim($arr_temp[1])=="Oct") $mm = "10";
					elseif (trim($arr_temp[1])=="��Ȩԡ�¹" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="��" || trim($arr_temp[1])=="Nov") $mm = "11";
					elseif (trim($arr_temp[1])=="�ѹ�Ҥ�" || trim($arr_temp[1])=="�ѹ�Ҥ�" || trim($arr_temp[1])=="�.�." || trim($arr_temp[1])=="Dec") $mm = "12";
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
					if ($POST_ENDTIME=="10:00 �") $POST_ENDTIME = "10:00";
					if ($POST_ENDTIME=="���͡:00") {
						$POST_ENDTIME = "";
						$POST_REMARK = "���͡";
					}
					if ($POST_TEL=="ʹ.��º��, ��Ѻ�ҡ�����¹����ԡ�") {
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
				} elseif ($FULLNAME) echo "��辺������ $FULLNAME<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//���êྨ
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
				if (substr($FULLNAME,0,3)=="���") $FULLNAME = substr($FULLNAME,3);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,4)=="�.�.") $FULLNAME = substr($FULLNAME,4);
				elseif (substr($FULLNAME,0,6)=="�����") $FULLNAME = trim(substr($FULLNAME,6));
				elseif (substr($FULLNAME,0,6)=="�����") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,7)=="���͵��") $FULLNAME = substr($FULLNAME,7);
				elseif (substr($FULLNAME,0,6)=="�Ժ�͡") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="�ҧ���") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,6)=="�.�.�.") $FULLNAME = substr($FULLNAME,6);
				elseif (substr($FULLNAME,0,12)=="������Ҫǧ��") $FULLNAME = substr($FULLNAME,12);
				elseif (substr($FULLNAME,0,9)=="�������ǧ") $FULLNAME = substr($FULLNAME,9);
				elseif (substr($FULLNAME,0,3)=="�ҧ") $FULLNAME = substr($FULLNAME,3);
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
					$REH_PERFORMANCE = "����Ҫ��ô��� ��Шӻ�".$REH_YEAR;
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
				} elseif ($FULLNAME) echo "��辺������ $FULLNAME<br>";
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//���êྨ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="POSITIONHIS"){
		$cmd = " update per_positionhis set poh_ass_org = replace(poh_ass_org, '�����', '�����') where poh_ass_org like '%�����%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = replace(poh_ass_department, '�����', '�����') where poh_ass_org like '%�����%' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_ass_org, poh_ass_org = '' 
where poh_ass_department is null and  poh_ass_org in 
('�ӹѡ�ҹ�Ѱ�����', '�ӹѡ�ҹ��Ѵ��з�ǧ��õ�ҧ�����', '�����á����', '����Ըա�÷ٵ', '������û', '������ɰ�Ԩ�����ҧ�����', '���ʹ���ѭ����С�����', '�����ù���', '���ͧ���������ҧ�����', '�������ԡ����ừԿԡ��', '�������¹', '�������µ��ѹ�͡', '���������� ���ѹ�͡��ҧ����Ϳ�ԡ�', '��������ˡ��') and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�ӹѡ�ҹ��Ѵ��з�ǧ��õ�ҧ�����' 
where poh_ass_department is null and  (poh_ass_org like 'ʶҹ�͡�Ѥ��Ҫ�ٵ%'  or poh_ass_org like 'ʶҹ������˭�%') and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�ӹѡ�ҹ�Ѱ�����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2953) and  poh_ass_org != '��ǹ��ҧ' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�ӹѡ�ҹ��Ѵ��з�ǧ��õ�ҧ�����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2954) and  poh_ass_org != '��ǹ��ҧ' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�����á����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3101) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '����Ըա�÷ٵ' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2955) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '������û' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3103) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '������ɰ�Ԩ�����ҧ�����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3105) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '���ʹ���ѭ����С�����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2956) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�����ù���' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2957) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '���ͧ���������ҧ�����' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2958) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�������ԡ����ừԿԡ��' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3109) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�������¹' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 2959) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�������µ��ѹ�͡' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3111) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '���������� ���ѹ�͡��ҧ����Ϳ�ԡ�' 
where poh_ass_department is null and  poh_ass_org in (select org_name from per_org where ol_code = '03' and org_id_ref = 3112) and  poh_ass_org != '��ǹ��ҧ' and  poh_ass_org != '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_org1 is null  and poh_ass_org2 is null ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_ass_org , poh_ass_org = poh_ass_org1 , poh_ass_org1 = poh_ass_org2 , poh_ass_org2 = poh_ass_org3 where poh_ass_department is null and  poh_ass_org is not null and poh_ass_org1 is not null  and poh_ass_org2 is not null and poh_ass_org in ('�����á����', '����Ըա�÷ٵ', '�����ù���', '�������¹', '�������µ��ѹ�͡', '�ӹѡ�ҹ��Ѵ��з�ǧ', '�ӹѡ�ҹ��Ѵ��з�ǧ��õ�ҧ�����') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = '�ӹѡ�ҹ��Ѵ��з�ǧ��õ�ҧ�����' where poh_ass_department is null and  poh_ass_org is not null and poh_ass_org1 is not null  and poh_ass_org2 is not null and poh_ass_org in ('�ͧ��ҧ', '�ͧ������˹�ҷ����н֡ͺ��', '�ͧ��þ�ʴ�', '�ͧ��ѧ', '�ٹ��෤��������ʹ����С���������', '�ӹѡ�Ѵ����к����÷�Ѿ���Թ', '�ӹѡ��º�����Ἱ', '�ӹѡ�����á�ä�ѧ', '�ӹѡ�����úؤ��', '�ӹѡ����ӹ�¡��') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update per_positionhis set poh_ass_department = poh_org2 where poh_ass_org = '�ӹѡ�ҹ�Ţҹء�á��' and poh_ass_department is null and  poh_ass_org1 is null  and poh_ass_org2 is null and poh_last_position = 'Y' and per_id in (select per_id from per_personal where per_type = 1 and per_status = 1) ";
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
//						WHERE EmpThaiName='�������ó'
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
// ���ҧ��ػ�ѹ������ *******************************************************
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
// ���ҧ�ѹ�Ҿѡ��͹���� *******************************************************
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
		$chkSave = 0;	// �ӹǹ�� � ����ա�� save
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