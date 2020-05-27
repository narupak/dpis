<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_att1 = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;

//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
	if ($command=="CONVERT1" || $command=="CONVERT2" || $command=="CONVERT3" || $command=="CONVERT4" || $command=="CONVERT5") {
//		echo "_FILES['TEXT_FILE']['tmp_name']=".$_FILES["TEXT_FILE"]["tmp_name"]." , _FILES['TEXT_FILE']['name']=".$_FILES["TEXT_FILE"]["name"]."<br>";
//		$target_dir = "uploads/";
//		$target_file = $target_dir . basename($_FILES["TEXT_FILE"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
/*		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["TEXT_FILE"]["tmp_name"]);
			if($check !== false) {
				echo "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				echo "File is not an image.";
				$uploadOk = 0;
			}
		} */
		// Check if file already exists
		if (file_exists($target_file)) {
			unlink($target_file);
//			echo "Sorry, file already exists.";
//			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["EXCEL_FILE"]["size"] > 500000) {
			$excel_msg ="Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
//		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
//		&& $imageFileType != "gif" ) {
//			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
//			$uploadOk = 0;
//		}
		// Check if $uploadOk is set to 0 by an error
//		echo "target_file=$target_file<br>";
		if ($uploadOk == 0) {
			$excel_msg = "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
//			if (file_exists($target_file)) unlink($target_file);
			if (move_uploaded_file($_FILES["EXCEL_FILE"]["tmp_name"], $target_file)) {
				$excel_msg = "The file ". basename( $_FILES["EXCEL_FILE"]["name"]). " has been uploaded.";
			} else {
				$excel_msg = "Sorry, there was an error uploading your file.";
			}
		}
	} // end if ($command=="UPLOAD")

	if(($command=="CONVERT1" || $command=="CONVERT2" || $command=="CONVERT3") && $uploadOk){
		if ($command=="CONVERT1") $UPDATE_USER = 99991;
		elseif ($command=="CONVERT2") $UPDATE_USER = 99992;
		elseif ($command=="CONVERT3") $UPDATE_USER = 99993;

		$cmd = " delete from PER_PERSONAL where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from PER_POSITION where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POS_ID = $data[MAX_ID] + 1;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_PER_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		$PER_TYPE = 1;
		$MOV_CODE = "101";
		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$MINISTRY_NAME = trim($fields[0]);
				$DEPARTMENT_NAME = trim($fields[1]);
				if ($DEPARTMENT_NAME=="�ӹѡ�ҹ�Ѱ������") $DEPARTMENT_NAME = "�ӹѡ�ҹ�Ѱ�����";
				$ORG_NAME = trim($fields[2]);
				if ($ORG_NAME=="�ͧ������˹�ҷ�� (Ȼ�.)") $ORG_NAME = "�ͧ������˹�ҷ��";
				if ($ORG_NAME=="�ӹѡ�ҹ�Ѱ������") $ORG_NAME = "�ӹѡ�ҹ�Ѱ�����";
				$POS_NO = trim($fields[3]);
				$PL_NAME = trim($fields[4]);
				$POSITION_TYPE = trim($fields[5]);
				$LEVEL_NO = trim($fields[6]);
				if ($LEVEL_NO=="M1") $LEVEL_NO = "D1";
				elseif ($LEVEL_NO=="M2") $LEVEL_NO = "D2";
				elseif ($LEVEL_NO=="S1") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="S2") $LEVEL_NO = "M2";
				$PM_NAME = trim($fields[7]);
				$SKILL_NAME = trim($fields[8]);
				$OT_NAME = trim($fields[9]);
				if ($OT_NAME=='��ǹ��ҧ') $OT_CODE = "01";
				elseif ($OT_NAME=='��ǹ�����Ҥ') $OT_CODE = "03";
				$PV_NAME = trim($fields[10]);
				$POSITION_STATUS = trim($fields[11]);
				$PN_NAME = trim($fields[12]);
				$PER_NAME = trim($fields[13]);
				$PER_SURNAME = trim($fields[14]);
				$PER_CARDNO = trim($fields[15]);
				$PER_GENDER = trim($fields[16]);
				if ($PER_GENDER=='���' || $PN_NAME=='���') $PER_GENDER = 1;
				elseif ($PER_GENDER=='˭ԧ' || $PN_NAME=='�ҧ' || $PN_NAME=='�ҧ���' || $PN_NAME=='�.�.') $PER_GENDER = 2;
				$PER_BIRTHDATE = save_date(trim($fields[17]));
				$PER_STARTDATE = save_date(trim($fields[18]));
				$PER_SALARY = trim($fields[19])+0;
				$PER_MGTSALARY = trim($fields[20])+0;
				
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT_NAME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$DEPARTMENT_ID = trim($data[ORG_ID]);
				if (!$DEPARTMENT_ID) $DEPARTMENT_ID = 167;
				if ($DEPARTMENT_NAME=="�ӹѡ�ҹ�Ѱ�����") $DEPARTMENT_ID = 166;

				$cmd = " select POS_ID from PER_POSITION where DEPARTMENT_ID = $DEPARTMENT_ID and POS_NO = '$POS_NO' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$POS_ID = $data[POS_ID];

				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_SEQ_NO ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PL_CODE = trim($data[PL_CODE]);
				if (!$PL_CODE) echo "$cmd<br>";

				$PM_CODE = "";
				if ($PM_NAME) {
					$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' order by PM_SEQ_NO ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PM_CODE = trim($data[PM_CODE]);
				}

				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$ORG_NAME' and DEPARTMENT_ID = $DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ORG_ID = trim($data[ORG_ID]);
				if (!$ORG_ID) echo "$cmd<br>";

				$SKILL_CODE = "";
				if ($SKILL_NAME) {
					$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$SKILL_CODE = trim($data[SKILL_CODE]);
				}

				if ($PM_CODE && $PM_CODE != 'NULL') $PM_CODE = "'$PM_CODE'";
				else $PM_CODE = "NULL";
				if ($SKILL_CODE && $SKILL_CODE != 'NULL') $SKILL_CODE = "'$SKILL_CODE'";
				else $SKILL_CODE = "NULL";
				if ($LEVEL_NO=="O1") $CL_NAME = "��Ժѵԧҹ";
				elseif ($LEVEL_NO=="O2") $CL_NAME = "�ӹҭ�ҹ";
				elseif ($LEVEL_NO=="O3") $CL_NAME = "������";
				elseif ($LEVEL_NO=="O4") $CL_NAME = "�ѡ�о����";
				elseif ($LEVEL_NO=="K1") $CL_NAME = "��Ժѵԡ��";
				elseif ($LEVEL_NO=="K2") $CL_NAME = "�ӹҭ���";
				elseif ($LEVEL_NO=="K3") $CL_NAME = "�ӹҭ��þ����";
				elseif ($LEVEL_NO=="K4") $CL_NAME = "����Ǫҭ";
				elseif ($LEVEL_NO=="K5") $CL_NAME = "�ç�س�ز�";
				elseif ($LEVEL_NO=="D1") $CL_NAME = "�ӹ�¡�õ�";
				elseif ($LEVEL_NO=="D2") $CL_NAME = "�ӹ�¡���٧";
				elseif ($LEVEL_NO=="M1") $CL_NAME = "�����õ�";
				elseif ($LEVEL_NO=="M2") $CL_NAME = "�������٧";
				$POS_STATUS = 1;

				$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) {			
					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL 
									where PER_NAME = '$PER_NAME' and  PER_SURNAME = '$PER_SURNAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
				}
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$OLD_PER_NAME = trim($data[PER_NAME]);
					$OLD_PER_SURNAME = trim($data[PER_SURNAME]);
					$OLD_POS_ID = $data[POS_ID];
					$OLD_PER_SALARY = $data[PER_SALARY];
					$UPDATE_DATE = trim($data[UPDATE_DATE]);

					if ($OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME) echo "����-���ʡ�����ç�ѹ $OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME<br>";

					$cmd = " select POS_NO, PL_CODE from PER_POSITION where POS_ID = $OLD_POS_ID ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$OLD_POS_NO = trim($data[POS_NO]);
					$OLD_PL_CODE = trim($data[PL_CODE]);

					if ($POS_ID!=$OLD_POS_ID) echo "�Ţ�����˹����ç�ѹ $POS_NO_NAME $POS_NO!=$OLD_POS_NO_NAME $OLD_POS_NO<br>";
					if ($PL_CODE!=$OLD_PL_CODE) echo "���͵��˹����§ҹ���ç�ѹ $PL_CODE!=$OLD_PL_CODE<br>";

					if ($POS_ID) {
						$cmd = " update PER_POSITION set 
										PL_CODE = '$PL_CODE', 
										PM_CODE = $PM_CODE, 
										SKILL_CODE = $SKILL_CODE, 
										POS_SALARY = $PER_SALARY, 
										LEVEL_NO = '$LEVEL_NO', 
										DEPARTMENT_ID = $DEPARTMENT_ID 
										where POS_ID=$POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					} else {
						$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
										CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO)
										VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
										'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$LEVEL_NO') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$POS_ID = $MAX_POS_ID;

						$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_POS_ID++;
						else echo "$cmd<br>";
					}

					$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";

					$cmd = " update PER_PERSONAL set 
									POS_ID = $POS_ID, 
									PER_SALARY = $PER_SALARY, 
									LEVEL_NO = '$LEVEL_NO', 
									DEPARTMENT_ID = $DEPARTMENT_ID 
									where PER_ID=$PER_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
				} else {
					echo "��辺������ $PER_CARDNO $PER_NAME $PER_SURNAME<br>";
					if ($POS_ID) {
						$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					} else {
						$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
										CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO)
										VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
										'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$LEVEL_NO') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$POS_ID = $MAX_POS_ID;

						$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_POS_ID++;
						else echo "$cmd<br>";
					}

					if ($PER_NAME) {
						$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or PN_SHORTNAME = '$PN_NAME' ";
						$db_dpis2->send_cmd($cmd);
			//			$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$PN_CODE = trim($data2[PN_CODE]);

						if (!$PER_BIRTHDATE) $PER_BIRTHDATE = "-";
						if (!$PER_STARTDATE) $PER_STARTDATE = "-";
						$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
										PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
										PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
										PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
										PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
										PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
										PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
										APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
										LEVEL_NO_SALARY)
										VALUES ($MAX_PER_ID, $PER_TYPE, '01', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
										NULL, NULL, NULL, $POS_ID, NULL, '$LEVEL_NO', 0, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '1', 
										'$PER_CARDNO', NULL, NULL, NULL, NULL, '$PER_BIRTHDATE', NULL,	'$PER_STARTDATE', '$PER_STARTDATE', 
										NULL, NULL, NULL,	NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$PV_CODE', '$MOV_CODE', NULL, NULL, NULL, 1, 
										$UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();

						$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_PER_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_PER_ID++;
						else echo "$cmd<br>";
					}
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//���êྨ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if(($command=="CONVERT4") && $uploadOk){
		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 1 �����ҹ�' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 1 (�����ҹ�)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 2 �ؾ�ó����' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 2 (�ؾ�ó����)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 3 ��Ҩչ����' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 3 (��Ҩչ����)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 4 ��ШǺ���բѹ��' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 4 (��ШǺ���բѹ��)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 5 ����Ҫ����' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 5 (�����Ҫ����)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 6 �͹��' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 6 (�͹��)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 7 ʡŹ��' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 7 (ʡŹ��)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 8 ��ᾧྪ�' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 8 (��ᾧྪ�)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 9 ��ɳ��š' where ORG_NAME = '�ع���ͧ�ѹ��к�����Ҹ�ó���ࢵ 9 (��ɳ��š)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 10 �ӻҧ' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 10 (�ӻҧ)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 11 ����ɮ��ҹ�' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 11 (����ɯ��ҹ�)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " update PER_ORG set ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó��� ࢵ 12 ʧ���' where ORG_NAME = '�ٹ���ͧ�ѹ��к�����Ҹ�ó���ࢵ 12 (ʧ���)' ";
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from PER_PERSONAL where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from PER_POSITION where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ORG_ID = $data[MAX_ID] + 1;

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POS_ID = $data[MAX_ID] + 1;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_PER_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		$PER_TYPE = 1;
		$MOV_CODE = "101";
		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$MINISTRY_NAME = trim($fields[0]);
				$DEPARTMENT_NAME = trim($fields[1]);
				$ORG_NAME = trim($fields[2]);
				if ($ORG_NAME=="�ӹѡ�ҹ��ͧ�ѹ��к�����Ҹ�ó��¨ѧ��Ѵ����ɯ��ҹ�") $ORG_NAME = "�ӹѡ�ҹ��ͧ�ѹ��к�����Ҹ�ó��¨ѧ��Ѵ����ɮ��ҹ�";
				$POS_NO = trim($fields[3]);
				$PL_NAME = trim($fields[4]);
				$POSITION_TYPE = trim($fields[5]);
				$LEVEL_NO = trim($fields[6]);
				if ($LEVEL_NO=="M1") $LEVEL_NO = "D1";
				elseif ($LEVEL_NO=="M2") $LEVEL_NO = "D2";
				elseif ($LEVEL_NO=="S1") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="S2") $LEVEL_NO = "M2";
				$PM_NAME = trim($fields[7]);
				$SKILL_NAME = trim($fields[8]);
				$OT_NAME = trim($fields[9]);
				if ($OT_NAME=='��ǹ��ҧ') $OT_CODE = "01";
				elseif ($OT_NAME=='��ǹ�����Ҥ') $OT_CODE = "03";
				$PV_NAME = trim($fields[10]);
				$POSITION_STATUS = trim($fields[11]);
				$PN_NAME = trim($fields[12]);
				$PER_NAME = trim($fields[13]);
				$PER_SURNAME = trim($fields[14]);
				$PER_CARDNO = trim($fields[15]);
				$PER_GENDER = trim($fields[16]);
				if ($PER_GENDER=='���' || $PN_NAME=='���') $PER_GENDER = 1;
				elseif ($PER_GENDER=='˭ԧ' || $PN_NAME=='�ҧ' || $PN_NAME=='�ҧ���' || $PN_NAME=='�.�.') $PER_GENDER = 2;
				$PER_BIRTHDATE = trim($fields[17]);
//				echo "$PER_BIRTHDATE<br>";
				$arr_temp = explode("/",$PER_BIRTHDATE);
				$PER_BIRTHDATE = ($arr_temp[2]-543)."-".str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT)."-".str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
				$PER_STARTDATE = trim($fields[18]);
				$arr_temp = explode("/",$PER_STARTDATE);
				$PER_STARTDATE = ($arr_temp[2]-543)."-".str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT)."-".str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
				$PER_SALARY = trim($fields[19])+0;
				$PER_MGTSALARY = trim($fields[20])+0;
				
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT_NAME' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$DEPARTMENT_ID = trim($data[ORG_ID]);
				if (!$DEPARTMENT_ID) $DEPARTMENT_ID = 171;

				$cmd = " select POS_ID from PER_POSITION where DEPARTMENT_ID = $DEPARTMENT_ID and POS_NO = '$POS_NO' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$POS_ID = $data[POS_ID];

				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_SEQ_NO ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$PL_CODE = trim($data[PL_CODE]);

				$PM_CODE = "";
				if ($PM_NAME) {
					$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' order by PM_SEQ_NO ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PM_CODE = trim($data[PM_CODE]);
				}

				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$ORG_NAME' and DEPARTMENT_ID = $DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ORG_ID = trim($data[ORG_ID]);
				if (!$ORG_ID) {
					if (strpos($ORG_NAME,"��طû�ҡ��") !== false) $PV_CODE = "1100";
					elseif (strpos($ORG_NAME,"�������") !== false) $PV_CODE = "1200";
					elseif (strpos($ORG_NAME,"�����ҹ�") !== false) $PV_CODE = "1300";
					elseif (strpos($ORG_NAME,"��й�������ظ��") !== false) $PV_CODE = "1400";
					elseif (strpos($ORG_NAME,"��ҧ�ͧ") !== false) $PV_CODE = "1500";
					elseif (strpos($ORG_NAME,"ž����") !== false) $PV_CODE = "1600";
					elseif (strpos($ORG_NAME,"�ԧ�����") !== false) $PV_CODE = "1700";
					elseif (strpos($ORG_NAME,"��¹ҷ") !== false) $PV_CODE = "1800";
					elseif (strpos($ORG_NAME,"��к���") !== false) $PV_CODE = "1900";
					elseif (strpos($ORG_NAME,"�ź���") !== false) $PV_CODE = "2000";
					elseif (strpos($ORG_NAME,"���ͧ") !== false) $PV_CODE = "2100";
					elseif (strpos($ORG_NAME,"�ѹ�����") !== false) $PV_CODE = "2200";
					elseif (strpos($ORG_NAME,"��Ҵ") !== false) $PV_CODE = "2300";
					elseif (strpos($ORG_NAME,"���ԧ���") !== false) $PV_CODE = "2400";
					elseif (strpos($ORG_NAME,"��Ҩչ����") !== false) $PV_CODE = "2500";
					elseif (strpos($ORG_NAME,"��ù�¡") !== false) $PV_CODE = "2600";
					elseif (strpos($ORG_NAME,"������") !== false) $PV_CODE = "2700";
					elseif (strpos($ORG_NAME,"����Ҫ����") !== false) $PV_CODE = "3000";
					elseif (strpos($ORG_NAME,"���������") !== false) $PV_CODE = "3100";
					elseif (strpos($ORG_NAME,"���Թ���") !== false) $PV_CODE = "3200";
					elseif (strpos($ORG_NAME,"�������") !== false) $PV_CODE = "3300";
					elseif (strpos($ORG_NAME,"�غ��Ҫ�ҹ�") !== false) $PV_CODE = "3400";
					elseif (strpos($ORG_NAME,"��ʸ�") !== false) $PV_CODE = "3500";
					elseif (strpos($ORG_NAME,"�������") !== false) $PV_CODE = "3600";
					elseif (strpos($ORG_NAME,"�ӹҨ��ԭ") !== false) $PV_CODE = "3700";
					elseif (strpos($ORG_NAME,"�֧���") !== false) $PV_CODE = "3800";
					elseif (strpos($ORG_NAME,"˹ͧ�������") !== false) $PV_CODE = "3900";
					elseif (strpos($ORG_NAME,"�͹��") !== false) $PV_CODE = "4000";
					elseif (strpos($ORG_NAME,"�شøҹ�") !== false) $PV_CODE = "4100";
					elseif (strpos($ORG_NAME,"���") !== false) $PV_CODE = "4200";
					elseif (strpos($ORG_NAME,"˹ͧ���") !== false) $PV_CODE = "4300";
					elseif (strpos($ORG_NAME,"�����ä��") !== false) $PV_CODE = "4400";
					elseif (strpos($ORG_NAME,"�������") !== false) $PV_CODE = "4500";
					elseif (strpos($ORG_NAME,"����Թ���") !== false) $PV_CODE = "4600";
					elseif (strpos($ORG_NAME,"ʡŹ��") !== false) $PV_CODE = "4700";
					elseif (strpos($ORG_NAME,"��þ��") !== false) $PV_CODE = "4800";
					elseif (strpos($ORG_NAME,"�ء�����") !== false) $PV_CODE = "4900";
					elseif (strpos($ORG_NAME,"��§����") !== false) $PV_CODE = "5000";
					elseif (strpos($ORG_NAME,"�Ӿٹ") !== false) $PV_CODE = "5100";
					elseif (strpos($ORG_NAME,"�ӻҧ") !== false) $PV_CODE = "5200";
					elseif (strpos($ORG_NAME,"�صôԵ��") !== false) $PV_CODE = "5300";
					elseif (strpos($ORG_NAME,"���") !== false) $PV_CODE = "5400";
					elseif (strpos($ORG_NAME,"��ҹ") !== false) $PV_CODE = "5500";
					elseif (strpos($ORG_NAME,"�����") !== false) $PV_CODE = "5600";
					elseif (strpos($ORG_NAME,"��§���") !== false) $PV_CODE = "5700";
					elseif (strpos($ORG_NAME,"�����ͧ�͹") !== false) $PV_CODE = "5800";
					elseif (strpos($ORG_NAME,"������ä�") !== false) $PV_CODE = "6000";
					elseif (strpos($ORG_NAME,"�ط�¸ҹ�") !== false) $PV_CODE = "6100";
					elseif (strpos($ORG_NAME,"��ᾧྪ�") !== false) $PV_CODE = "6200";
					elseif (strpos($ORG_NAME,"�ҡ") !== false) $PV_CODE = "6300";
					elseif (strpos($ORG_NAME,"��⢷��") !== false) $PV_CODE = "6400";
					elseif (strpos($ORG_NAME,"��ɳ��š") !== false) $PV_CODE = "6500";
					elseif (strpos($ORG_NAME,"�ԨԵ�") !== false) $PV_CODE = "6600";
					elseif (strpos($ORG_NAME,"ྪú�ó�") !== false) $PV_CODE = "6700";
					elseif (strpos($ORG_NAME,"�Ҫ����") !== false) $PV_CODE = "7000";
					elseif (strpos($ORG_NAME,"�ҭ������") !== false) $PV_CODE = "7100";
					elseif (strpos($ORG_NAME,"�ؾ�ó����") !== false) $PV_CODE = "7200";
					elseif (strpos($ORG_NAME,"��û��") !== false) $PV_CODE = "7300";
					elseif (strpos($ORG_NAME,"��ط��Ҥ�") !== false) $PV_CODE = "7400";
					elseif (strpos($ORG_NAME,"��ط�ʧ����") !== false) $PV_CODE = "7500";
					elseif (strpos($ORG_NAME,"ྪú���") !== false) $PV_CODE = "7600";
					elseif (strpos($ORG_NAME,"��ШǺ���բѹ��") !== false) $PV_CODE = "7700";
					elseif (strpos($ORG_NAME,"�����ո����Ҫ") !== false) $PV_CODE = "8000";
					elseif (strpos($ORG_NAME,"��к��") !== false) $PV_CODE = "8100";
					elseif (strpos($ORG_NAME,"�ѧ��") !== false) $PV_CODE = "8200";
					elseif (strpos($ORG_NAME,"����") !== false) $PV_CODE = "8300";
					elseif (strpos($ORG_NAME,"����ɮ��ҹ�") !== false) $PV_CODE = "8400";
					elseif (strpos($ORG_NAME,"�йͧ") !== false) $PV_CODE = "8500";
					elseif (strpos($ORG_NAME,"�����") !== false) $PV_CODE = "8600";
					elseif (strpos($ORG_NAME,"ʧ���") !== false) $PV_CODE = "9000";
					elseif (strpos($ORG_NAME,"ʵ��") !== false) $PV_CODE = "9100";
					elseif (strpos($ORG_NAME,"��ѧ") !== false) $PV_CODE = "9200";
					elseif (strpos($ORG_NAME,"�ѷ�ا") !== false) $PV_CODE = "9300";
					elseif (strpos($ORG_NAME,"�ѵ�ҹ�") !== false) $PV_CODE = "9400";
					elseif (strpos($ORG_NAME,"����") !== false) $PV_CODE = "9500";
					elseif (strpos($ORG_NAME,"��Ҹ����") !== false) $PV_CODE = "9600";
					$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
									CT_CODE, PV_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
									VALUES ($MAX_ORG_ID, '$MAX_ORG_ID', '$ORG_NAME', '$ORG_NAME', '03', '02', 
									'140', '$PV_CODE', $DEPARTMENT_ID, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $MAX_ORG_ID ";
					$count_data = $db_dpis->send_cmd($cmd1);
					if ($count_data) {
						$ORG_ID = $MAX_ORG_ID;
						$MAX_ORG_ID++;
					} else echo "$cmd<br>";
				}

				$SKILL_CODE = "";
				if ($SKILL_NAME) {
					$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$SKILL_CODE = trim($data[SKILL_CODE]);
				}

				if ($PM_CODE && $PM_CODE != 'NULL') $PM_CODE = "'$PM_CODE'";
				else $PM_CODE = "NULL";
				if ($SKILL_CODE && $SKILL_CODE != 'NULL') $SKILL_CODE = "'$SKILL_CODE'";
				else $SKILL_CODE = "NULL";
				if ($LEVEL_NO=="O1") $CL_NAME = "��Ժѵԧҹ";
				elseif ($LEVEL_NO=="O2") $CL_NAME = "�ӹҭ�ҹ";
				elseif ($LEVEL_NO=="O3") $CL_NAME = "������";
				elseif ($LEVEL_NO=="O4") $CL_NAME = "�ѡ�о����";
				elseif ($LEVEL_NO=="K1") $CL_NAME = "��Ժѵԡ��";
				elseif ($LEVEL_NO=="K2") $CL_NAME = "�ӹҭ���";
				elseif ($LEVEL_NO=="K3") $CL_NAME = "�ӹҭ��þ����";
				elseif ($LEVEL_NO=="K4") $CL_NAME = "����Ǫҭ";
				elseif ($LEVEL_NO=="K5") $CL_NAME = "�ç�س�ز�";
				elseif ($LEVEL_NO=="D1") $CL_NAME = "�ӹ�¡�õ�";
				elseif ($LEVEL_NO=="D2") $CL_NAME = "�ӹ�¡���٧";
				elseif ($LEVEL_NO=="M1") $CL_NAME = "�����õ�";
				elseif ($LEVEL_NO=="M2") $CL_NAME = "�������٧";
				$POS_STATUS = 1;

				$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) {			
					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL 
									where PER_NAME = '$PER_NAME' and  PER_SURNAME = '$PER_SURNAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
				}
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$OLD_PER_NAME = trim($data[PER_NAME]);
					$OLD_PER_SURNAME = trim($data[PER_SURNAME]);
					$OLD_POS_ID = $data[POS_ID];
					$OLD_PER_SALARY = $data[PER_SALARY];
					$UPDATE_DATE = trim($data[UPDATE_DATE]);

					if ($OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME) echo "����-���ʡ�����ç�ѹ $OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME<br>";

					$cmd = " select POS_NO, PL_CODE from PER_POSITION where POS_ID = $OLD_POS_ID ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$OLD_POS_NO = trim($data[POS_NO]);
					$OLD_PL_CODE = trim($data[PL_CODE]);

					if ($POS_ID!=$OLD_POS_ID) echo "�Ţ�����˹����ç�ѹ $POS_NO_NAME $POS_NO!=$OLD_POS_NO_NAME $OLD_POS_NO<br>";
					if ($PL_CODE!=$OLD_PL_CODE) echo "���͵��˹����§ҹ���ç�ѹ $PL_CODE!=$OLD_PL_CODE<br>";

					if ($POS_ID) {
						$cmd = " update PER_POSITION set 
										PL_CODE = '$PL_CODE', 
										PM_CODE = $PM_CODE, 
										SKILL_CODE = $SKILL_CODE, 
										POS_SALARY = $PER_SALARY, 
										LEVEL_NO = '$LEVEL_NO', 
										DEPARTMENT_ID = $DEPARTMENT_ID 
										where POS_ID=$POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					} else {
						$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
										CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO)
										VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
										'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$LEVEL_NO') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$POS_ID = $MAX_POS_ID;

						$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_POS_ID++;
						else echo "$cmd<br>";
					}

					$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";

					$cmd = " update PER_PERSONAL set 
									POS_ID = $POS_ID, 
									PER_SALARY = $PER_SALARY, 
									LEVEL_NO = '$LEVEL_NO', 
									DEPARTMENT_ID = $DEPARTMENT_ID 
									where PER_ID=$PER_ID ";
					$count_data = $db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
				} else {
					echo "��辺������ $PER_CARDNO $PER_NAME $PER_SURNAME<br>";
					if ($POS_ID) {
						$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					} else {
						$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
										CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO)
										VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
										'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$LEVEL_NO') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$POS_ID = $MAX_POS_ID;

						$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_POS_ID++;
						else echo "$cmd<br>";
					}

					if ($PER_NAME) {
						$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or PN_SHORTNAME = '$PN_NAME' ";
						$db_dpis2->send_cmd($cmd);
			//			$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$PN_CODE = trim($data2[PN_CODE]);

						if (!$PER_BIRTHDATE) $PER_BIRTHDATE = "-";
						if (!$PER_STARTDATE) $PER_STARTDATE = "-";
						$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
										PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
										PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
										PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
										PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
										PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
										PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
										APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
										LEVEL_NO_SALARY)
										VALUES ($MAX_PER_ID, $PER_TYPE, '01', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
										NULL, NULL, NULL, $POS_ID, NULL, '$LEVEL_NO', 0, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '1', 
										'$PER_CARDNO', NULL, NULL, NULL, NULL, '$PER_BIRTHDATE', NULL,	'$PER_STARTDATE', '$PER_STARTDATE', 
										NULL, NULL, NULL,	NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$PV_CODE', '$MOV_CODE', NULL, NULL, NULL, 1, 
										$UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, NULL, NULL, NULL, NULL, NULL, NULL, NULL) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();

						$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_PER_ID ";
						$count_data = $db_dpis->send_cmd($cmd1);
						if ($count_data) $MAX_PER_ID++;
						else echo "$cmd<br>";
					}
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//���êྨ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if(($command=="CONVERT5") && $uploadOk){
		$cmd = " delete from PER_PERSONAL where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " delete from PER_POSITION where UPDATE_USER = $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_POS_ID = $data[MAX_ID] + 1;

		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_PER_ID = $data[MAX_ID] + 1;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		$PER_TYPE = 1;
		$OT_CODE = "01";
		$RE_CODE = "01";
		$PV_NAME = "11";
		$PER_MGTSALARY = 0;
		$PER_STARTDATE = "-";
		$MOV_CODE = "101";
		$DEPARTMENT_ID = 14051;
		$SKILL_CODE = "NULL";
		$PER_ORDAIN = 0;
		$PER_SOLDIER = 0;
		$PER_MEMBER = 0;
		$PER_COOPERATIVE = 0;
		$ES_CODE = "02";
		$PER_DISABILITY = 1;
		$PER_UNION = 0;
		$PER_UNION2 = 0;
		$PER_UNION3 = 0;
		$PER_UNION4 = 0;
		$PER_UNION5 = 0;
		$PER_AUDIT_FLAG = 0;
		$PER_PROBATION_FLAG = 0;
		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$SEQ_NO = trim($fields[0])+0;
				if ($SEQ_NO > 0 && $SEQ_NO != 814) {
					echo $SEQ_NO." ";
					$FULLNAME = trim($fields[1]);
					$FULLNAME = str_replace("  ", " ", $FULLNAME);
					$FULLNAME = str_replace("  ", " ", $FULLNAME);
					$FULLNAME = str_replace("  ", " ", $FULLNAME);
					$FULLNAME = str_replace("  ", " ", $FULLNAME);
					$PN_CODE = $PER_GENDER = "";
					if (substr($FULLNAME,0,3)=="���") {
						$FULLNAME = substr($FULLNAME,3);
						$PN_CODE = "003";
						$PER_GENDER = 1;
					} elseif (substr($FULLNAME,0,4)=="�.�.") {
						$FULLNAME = substr($FULLNAME,4);
						$PN_CODE = "004";
						$PER_GENDER = 2;
					} elseif (substr($FULLNAME,0,3)=="�ҧ") {
						$FULLNAME = substr($FULLNAME,3);
						$PN_CODE = "005";
						$PER_GENDER = 2;
					} elseif (substr($FULLNAME,0,4)=="�.�.") {
						$FULLNAME = substr($FULLNAME,4);
						$PN_CODE = "374";
						$PER_GENDER = 1;
					}
					$arr_temp = explode(" ",$FULLNAME);
					$PER_NAME = trim($arr_temp[0]);
					$PER_SURNAME = trim($arr_temp[1]);
					$PM_NAME = trim($fields[2]);
					$PL_NAME = trim($fields[3]);
					if ($PL_NAME=="(�ѡ�����çҹ�����)") $PL_NAME = "�ѡ�����çҹ�����";
					elseif ($PL_NAME=="(�ѡ�����çҹ�Ҹ�ó�آ)") $PL_NAME = "�ѡ�����çҹ�Ҹ�ó�آ";
					$LEVEL_NO = trim($fields[4]);
					$CL_NAME = $LEVEL_NO;
					if ($LEVEL_NO=="1") $LEVEL_NO = "01";
					elseif ($LEVEL_NO=="2") $LEVEL_NO = "02";
					elseif ($LEVEL_NO=="3") $LEVEL_NO = "03";
					elseif ($LEVEL_NO=="4") $LEVEL_NO = "04";
					elseif ($LEVEL_NO=="5") $LEVEL_NO = "05";
					elseif ($LEVEL_NO=="6" || $LEVEL_NO=="6�" || $LEVEL_NO=="6 �") $LEVEL_NO = "06";
					elseif ($LEVEL_NO=="7" || $LEVEL_NO=="7�" || $LEVEL_NO=="7 �" || $LEVEL_NO=="7Ǫ" || $LEVEL_NO=="7 Ǫ") $LEVEL_NO = "07";
					elseif ($LEVEL_NO=="8" || $LEVEL_NO=="8�" || $LEVEL_NO=="8 �" || $LEVEL_NO=="8Ǫ" || $LEVEL_NO=="8 Ǫ") $LEVEL_NO = "08";
					elseif ($LEVEL_NO=="9") $LEVEL_NO = "09";
					$POS_NO = trim($fields[5]);
					$POS_NO = str_replace("-", "", $POS_NO);
					$POS_NO = str_replace("29.1", "91", $POS_NO);
					$POS_NO = str_replace("29.2", "92", $POS_NO);
					$PER_SALARY = trim($fields[6])+0;
					$PER_REMARK = trim($fields[7]);
					$ORG_NAME = trim($fields[19]);
//					if ($ORG_NAME=="�ӹѡ�ҹ��ͧ�ѹ��к�����Ҹ�ó��¨ѧ��Ѵ����ɯ��ҹ�") $ORG_NAME = "�ӹѡ�ҹ��ͧ�ѹ��к�����Ҹ�ó��¨ѧ��Ѵ����ɮ��ҹ�";
					
					$cmd = " select POS_ID from PER_POSITION where DEPARTMENT_ID = $DEPARTMENT_ID and POS_NO = '$POS_NO' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$POS_ID = $data[POS_ID];

					$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_SEQ_NO ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PL_CODE = trim($data[PL_CODE]);

					$PM_CODE = "";
					if ($PM_NAME) {
						$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' order by PM_SEQ_NO ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$PM_CODE = trim($data[PM_CODE]);
					}

					if ($ORG_NAME) {
						$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$ORG_NAME' and DEPARTMENT_ID = $DEPARTMENT_ID ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$ORG_ID = trim($data[ORG_ID]);
						if (!$ORG_ID) echo "$cmd<br>";
					}

					if ($PM_CODE && $PM_CODE != 'NULL') $PM_CODE = "'$PM_CODE'";
					else $PM_CODE = "NULL";
					$POS_STATUS = 1;

					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL 
									where PER_NAME||' '||PER_SURNAME=trim('$FULLNAME') ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$PER_ID = $data[PER_ID];
						$OLD_PER_NAME = trim($data[PER_NAME]);
						$OLD_PER_SURNAME = trim($data[PER_SURNAME]);
						$OLD_POS_ID = $data[POS_ID];
						$OLD_PER_SALARY = $data[PER_SALARY];
						$UPDATE_DATE = trim($data[UPDATE_DATE]);

						if ($OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME) echo "����-���ʡ�����ç�ѹ $OLD_PER_NAME!=$PER_NAME || $OLD_PER_SURNAME!=$PER_SURNAME<br>";

						$cmd = " select POS_NO, PL_CODE from PER_POSITION where POS_ID = $OLD_POS_ID ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$OLD_POS_NO = trim($data[POS_NO]);
						$OLD_PL_CODE = trim($data[PL_CODE]);

						if ($POS_ID!=$OLD_POS_ID) echo "�Ţ�����˹����ç�ѹ $POS_NO_NAME $POS_NO!=$OLD_POS_NO_NAME $OLD_POS_NO<br>";
						if ($PL_CODE!=$OLD_PL_CODE) echo "���͵��˹����§ҹ���ç�ѹ $PL_CODE!=$OLD_PL_CODE<br>";

						if ($POS_ID) {
							$cmd = " update PER_POSITION set 
											PL_CODE = '$PL_CODE', 
											PM_CODE = $PM_CODE, 
											POS_SALARY = $PER_SALARY
											where POS_ID=$POS_ID ";
							$count_data = $db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "$cmd<br>";
						} else {
							$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
											CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
											VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
											'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$POS_ID = $MAX_POS_ID;

							$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
							$count_data = $db_dpis->send_cmd($cmd1);
							if ($count_data) $MAX_POS_ID++;
							else echo "$cmd<br>";
						}

						$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";

						$cmd = " update PER_PERSONAL set 
										POS_ID = $POS_ID, 
										PER_SALARY = $PER_SALARY, 
										LEVEL_NO = '$LEVEL_NO'
										where PER_ID=$PER_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					} else {
						echo "��辺������ $FULLNAME $PER_NAME $PER_SURNAME<br>";
						if ($POS_ID) {
							$cmd = " update PER_PERSONAL set POS_ID = NULL where POS_ID=$POS_ID ";
							$count_data = $db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "$cmd<br>";
						} else {
							$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, 
											CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
											VALUES ($MAX_POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $PM_CODE, '$PL_CODE', 
											'$CL_NAME', $PER_SALARY, $PER_MGTSALARY, $SKILL_CODE, $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$POS_ID = $MAX_POS_ID;

							$cmd1 = " SELECT POS_ID FROM PER_POSITION WHERE POS_ID = $MAX_POS_ID ";
							$count_data = $db_dpis->send_cmd($cmd1);
							if ($count_data) $MAX_POS_ID++;
							else echo "$cmd<br>";
						}

						if ($PER_NAME && $PER_NAME != "��ҧ" && $PER_NAME != "(��ҧ)") {
							$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, 
											PER_SURNAME, PER_ENG_NAME, PER_ENG_SURNAME, ORG_ID, POS_ID, POEM_ID, LEVEL_NO, PER_ORGMGT, 
											PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, MR_CODE, PER_CARDNO, PER_OFFNO, 
											PER_TAXNO, PER_BLOOD, RE_CODE, PER_BIRTHDATE, PER_RETIREDATE, PER_STARTDATE, 
											PER_OCCUPYDATE, PER_POSDATE, PER_SALDATE, PN_CODE_F, PER_FATHERNAME, PER_FATHERSURNAME, 
											PN_CODE_M, PER_MOTHERNAME, PER_MOTHERSURNAME, PER_ADD1, PER_ADD2, PV_CODE, MOV_CODE, 
											PER_ORDAIN, PER_SOLDIER, PER_MEMBER, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, 
											APPROVE_PER_ID, REPLACE_PER_ID, ABSENT_FLAG, POEMS_ID, PER_HIP_FLAG, PER_CERT_OCC, 
											LEVEL_NO_SALARY, PER_REMARK, PER_COOPERATIVE, ES_CODE, PER_DISABILITY, PER_UNION, PER_UNION2,
											PER_UNION3, PER_UNION4, PER_UNION5, PER_AUDIT_FLAG, PER_PROBATION_FLAG, DEPARTMENT_ID_ASS)
											VALUES ($MAX_PER_ID, $PER_TYPE, '01', '$PN_CODE', '$PER_NAME', '$PER_SURNAME', 
											NULL, NULL, NULL, $POS_ID, NULL, '$LEVEL_NO', 0, $PER_SALARY, $PER_MGTSALARY, 0, $PER_GENDER, '1', 
											'$PER_CARDNO', NULL, NULL, NULL, '$RE_CODE', '$PER_BIRTHDATE', NULL,	'$PER_STARTDATE', '$PER_STARTDATE', 
											NULL, NULL, NULL,	NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$PV_CODE', '$MOV_CODE', $PER_ORDAIN, $PER_SOLDIER, 
											$PER_MEMBER, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, NULL, NULL, NULL, NULL, NULL, NULL, 
											'$LEVEL_NO', '$PER_REMARK', $PER_COOPERATIVE, '$ES_CODE', $PER_DISABILITY, $PER_UNION, $PER_UNION2,
											$PER_UNION3, $PER_UNION4, $PER_UNION5, $PER_AUDIT_FLAG, $PER_PROBATION_FLAG, $DEPARTMENT_ID) ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();

							$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $MAX_PER_ID ";
							$count_data = $db_dpis->send_cmd($cmd1);
							if ($count_data) $MAX_PER_ID++;
							else echo "$cmd<br>";
						}
					}
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//���êྨ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

?>