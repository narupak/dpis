<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_att1 = new connect_att($attdb_host, $attdb_name, $attdb_user, $attdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
	if ($command=="CONVERT1" || $command=="CONVERT2" || $command=="RETIRE" || $command=="TRAIN" || $command=="MAP_PER" || $command=="TRAINING" || $command=="SALARYHIS" || $command=="DECORATEHIS" || $command=="KPI_FORM") {
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

	if($command=="CONVERT1" && $uploadOk){	
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[0]);
//				$RANK_CODE = trim($fields[1]);
				$RANK_NAME = trim($fields[1]);
				$FNAME = trim($fields[2]);
				$LNAME = trim($fields[3]);
				$SEX = trim($fields[4]);
				$DEPARTMENT_CODE = trim($fields[5]);
				$DEPARTMENT_NAME = trim($fields[6]);
				$DIVISION_CODE = trim($fields[7]);
				$DIVISION_NAME = trim($fields[8]);
				$SECTION_CODE = trim($fields[9]);
				$SECTION_NAME = trim($fields[10]);
				$JOB_CODE = trim($fields[11]);
				$JOB_NAME = trim($fields[12]);
				$POS_NO_NAME = trim($fields[13]);
				$POS_NO = trim($fields[14]);
				$PL_CODE = trim($fields[15]);
				$PL_NAME = trim($fields[16]);
				$PM_CODE = trim($fields[17]);
				$PM_NAME = trim($fields[18]);
				$MP_CEE = trim($fields[19]);
				$MP_CEE_NAME = trim($fields[20]);
				$SALARY_POS_ABB_NAME = trim($fields[21]);
				$PER_SALARY = trim($fields[22]);
				$COST_LIVING_AMOUNT = trim($fields[23]);
				$HELP_LIVING_AMOUNT = trim($fields[24]);
				$SAL_POS_AMOUNT_1 = trim($fields[25]);
				$SAL_POS_AMOUNT_2 = trim($fields[26]);
				$PAYMENT_AMT = trim($fields[27]);
				$SPECIAL_AMT = trim($fields[28]);
				$FLAG_PERSON_TYPE = trim($fields[29]);
				$FLAG_CUR_ST = trim($fields[30]);
				if ($MP_CEE == "21") $LEVEL_NO = "O1";
				elseif ($MP_CEE == "22") $LEVEL_NO = "O2";
				elseif ($MP_CEE == "23") $LEVEL_NO = "O3";
				elseif ($MP_CEE == "24") $LEVEL_NO = "O4";
				elseif ($MP_CEE == "26") $LEVEL_NO = "K1";
				elseif ($MP_CEE == "27") $LEVEL_NO = "K2";
				elseif ($MP_CEE == "28") $LEVEL_NO = "K3";
				elseif ($MP_CEE == "29") $LEVEL_NO = "K4";
				elseif ($MP_CEE == "30") $LEVEL_NO = "K5";
				elseif ($MP_CEE == "32") $LEVEL_NO = "D1";
				elseif ($MP_CEE == "33") $LEVEL_NO = "D2";
				elseif ($MP_CEE == "34") $LEVEL_NO = "M1";
				elseif ($MP_CEE == "35") $LEVEL_NO = "M2";
				
				$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) {			
					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, POS_ID, PER_SALARY, UPDATE_DATE from PER_PERSONAL where PER_NAME = '$FNAME' and  PER_SURNAME = '$LNAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
				}
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$PER_NAME = trim($data[PER_NAME]);
					$PER_SURNAME = trim($data[PER_SURNAME]);
					$OLD_POS_ID = $data[POS_ID];
					$OLD_PER_SALARY = $data[PER_SALARY];
					$UPDATE_DATE = trim($data[UPDATE_DATE]);

					if ($FNAME!=$PER_NAME || $LNAME!=$PER_SURNAME) echo "ชื่อ-นามสกุลไม่ตรงกัน $FNAME!=$PER_NAME || $LNAME!=$PER_SURNAME<br>";

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

					$cmd = " select POS_NO_NAME, POS_NO, PL_CODE from PER_POSITION where POS_ID = $OLD_POS_ID ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$OLD_POS_NO_NAME = trim($data[POS_NO_NAME]);
					$OLD_POS_NO = trim($data[POS_NO]);
					$OLD_PL_CODE = trim($data[PL_CODE]);

					$cmd = " select POS_ID, DEPARTMENT_ID from PER_POSITION where POS_NO_NAME = '$POS_NO_NAME' and POS_NO = '$POS_NO' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$POS_ID = $data[POS_ID];
					$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
					if ($POS_ID!=$OLD_POS_ID) echo "เลขที่ตำแหน่งไม่ตรงกัน $POS_NO_NAME $POS_NO!=$OLD_POS_NO_NAME $OLD_POS_NO<br>";
					if ($PL_CODE!=$OLD_PL_CODE) echo "ชื่อตำแหน่งในสายงานไม่ตรงกัน $PL_CODE!=$OLD_PL_CODE<br>";

//					if ($UPDATE_DATE < "2013-03-31") {
//						$cmd = " update PER_POSITION set PL_CODE = '$PL_CODE', PM_CODE = '$PM_CODE', POS_SALARY = $PER_SALARY, LEVEL_NO = '$LEVEL_NO' where POS_ID=$POS_ID ";
//						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";

//						$cmd = " update PER_PERSONAL set POS_ID = $POS_ID, PER_SALARY = $PER_SALARY, LEVEL_NO = '$LEVEL_NO', DEPARTMENT_ID = $TMP_DEPARTMENT_ID 
						$cmd = " update PER_PERSONAL set PER_SALARY = $PER_SALARY 
										where PER_ID=$PER_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
//					}
				} else echo "ไม่พบข้อมูล $PER_CARDNO $FNAME $LNAME<br>";
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="RETIRE" && $uploadOk){
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$OT_NAME = trim($fields[0]);
				$PER_CARDNO = trim($fields[1]);
				$FNAME = trim($fields[2]);
				$LNAME = trim($fields[3]);
				$PL_NAME = trim($fields[4]);
				$MP_CEE_NAME = trim($fields[5]);
				$DEPARTMENT_CODE = trim($fields[6]);
				$DIVISION_CODE = trim($fields[7]);
				$SECTION_CODE = trim($fields[8]);
				$JOB_CODE = trim($fields[9]);
				$ORG_NAME = trim($fields[10]);
				$PER_STATUS = trim($fields[11]);
				$MOV_NAME = trim($fields[12]);
				
				$cmd = " select PER_ID, PER_NAME, PER_SURNAME, PER_STATUS from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) {			
					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, PER_STATUS from PER_PERSONAL where PER_NAME = '$FNAME' and  PER_SURNAME = '$LNAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
				}
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$PER_NAME = trim($data[PER_NAME]);
					$PER_SURNAME = trim($data[PER_SURNAME]);
					$PER_STATUS = $data[PER_STATUS];

					if ($FNAME!=$PER_NAME || $LNAME!=$PER_SURNAME) echo "ชื่อ-นามสกุลไม่ตรงกัน $FNAME!=$PER_NAME || $LNAME!=$PER_SURNAME<br>";

					if ($PER_STATUS!=2) {
						$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$MOV_NAME' ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$MOV_CODE = $data[MOV_CODE];
						if (!$MOV_CODE) $MOV_CODE = "0";

						$cmd = " update PER_PERSONAL set PER_STATUS = 2, MOV_CODE = '$MOV_CODE' 
										where PER_ID=$PER_ID ";
						$count_data = $db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
					}
				} else echo "ไม่พบข้อมูล $PER_CARDNO $FNAME $LNAME<br>";
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="CONVERT2" && $uploadOk){
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$FULLNAME = trim($fields[0]);
				$ADDRESS = trim($fields[1]);
				$EMAIL = trim($fields[2]);
				$TEL = trim($fields[3]);
				$USERNAME = trim($fields[4]);

				$cmd = " select id from user_detail where username='$USERNAME' ";
				if($db->send_cmd($cmd)){
					$error_signin = "Error :: Username ซ้ำ";
				}else{
					$cmd = " select max(id) as max_id from user_detail ";
					$db->send_cmd($cmd);
					$data = $db->get_array();
					$data = array_change_key_case($data, CASE_LOWER);
					$max_id = $data[max_id] + 1;
					$PASSWORD = md5($USERNAME);
					$cmd = " insert into user_detail (id, group_id, username, password, inherit_group, user_link_id, fullname, address, 
									district_id, amphur_id, province_id, email, tel, fax, create_date, create_by, update_date, update_by)
									 values ($max_id, NULL, '$USERNAME', '$PASSWORD', NULL, NULL, 
									'$FULLNAME', '$ADDRESS', NULL, NULL, NULL, '$EMAIL', '$TEL', 
									NULL, '$UPDATE_DATE', $SESS_USERID, '$UPDATE_DATE', $SESS_USERID) ";
					$db->send_cmd($cmd);
		//			$db->show_error();
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if( $command=='PERFORMANCE_REVIEW' ){
// ยุทธศาสตร์ 
		$cmd = " DELETE FROM PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$search_budget_year' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " SELECT POLICY_ID, POLICY, POLICY_YEAR 
						  FROM TBL_POLICY_MAIN
						  ORDER BY POLICY_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW++;
			$POLICY_ID = trim($data[POLICY_ID]);
			$POLICY = trim($data[POLICY]);
			$POLICY_YEAR = trim($data[POLICY_YEAR]);
			$DEPT_ID = 0;

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($POLICY_ID, '$POLICY', '$POLICY_YEAR', $DEPT_ID, 1, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$search_budget_year' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW - $COUNT_NEW<br>";
		
		$cmd = " SELECT BRANCH_ID, BRANCH, BRANCH_DETAIL, POLICY_ID, POLICY_YEAR 
						  FROM TBL_POLICY_MAIN1
						  ORDER BY BRANCH_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW++;
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$BRANCH = trim($data[BRANCH]);
			$BRANCH_DETAIL = trim($data[BRANCH_DETAIL]);
			$POLICY_ID = trim($data[POLICY_ID]);
			$POLICY_YEAR = trim($data[POLICY_YEAR]);

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($BRANCH_ID, '$BRANCH', '$POLICY_YEAR', $POLICY_ID, $DEPT_ID, 2, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
		$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$search_budget_year' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW - $COUNT_NEW<br>";
		
		$cmd = " SELECT STRATEGY_ID, STRATEGY, STRATEGY_DETAIL, BRANCH_ID
						  FROM TBL_POLICY_MAIN2
						  ORDER BY STRATEGY_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PERFORMANCE_REVIEW++;
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$STRATEGY = trim($data[STRATEGY]);
			$STRATEGY_DETAIL = trim($data[STRATEGY_DETAIL]);
			$BRANCH_ID = trim($data[BRANCH_ID]);

			$cmd = " INSERT INTO PER_PERFORMANCE_REVIEW(PFR_ID, PFR_NAME, PFR_YEAR, PFR_ID_REF, DEPARTMENT_ID, PFR_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($STRATEGY_ID, '$STRATEGY', '$POLICY_YEAR', $BRANCH_ID, $DEPT_ID, 3, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>==================<br>";
//			$db_dpis->show_error();
//			echo "<br>end ". ++$i  ."=======================<br>";
		} // end while						

		$cmd = " select count(PFR_ID) as COUNT_NEW from PER_PERFORMANCE_REVIEW WHERE PFR_YEAR = '$search_budget_year' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PERFORMANCE_REVIEW - $PER_PERFORMANCE_REVIEW - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='KPI' ){
// ตัวชี้วัด 
		$cmd = " DELETE FROM PER_KPI WHERE KPI_YEAR = '$search_budget_year' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

//		$cmd = " SELECT KPI_ID, a.DEPART_ID, SUBDEPART_ID, KPI_YEAR, KPI_NAME, MITI_NUM, KPI_TYPE, KPI_LEVEL, KEY_SUCCESS, POLICY_ID,
//						  BRANCH_ID, STRATEGY_ID, UNIT_COUNT, TARGET_NUM, REPORT_NUM, KPI_DEFINE, CACULATION, KPI_MEMO, PROJECT_MEMO, 
//						  TARGET_MEMO, BYDEPARTMENT, USE_CALCULATE, b.DEPART_ID2, b.DEPARTMENT
//						  FROM TBL_KPI_MAIN a, TBL_DEPARTMENT_CODE b
//						  WHERE a.DEPART_ID=b.DEPART_ID(+)
//						  ORDER BY KPI_ID ";
		$cmd = " SELECT KPI_ID, DEPART_ID, SUBDEPART_ID, KPI_YEAR, KPI_NAME, MITI_NUM, KPI_TYPE, KPI_LEVEL, KEY_SUCCESS, POLICY_ID,
						  BRANCH_ID, STRATEGY_ID, UNIT_COUNT, TARGET_NUM, REPORT_NUM, KPI_DEFINE, CACULATION, KPI_MEMO, PROJECT_MEMO, 
						  TARGET_MEMO, BYDEPARTMENT, USE_CALCULATE
						  FROM TBL_KPI_MAIN
						  ORDER BY KPI_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_KPI++;
			$KPI_ID = trim($data[KPI_ID]);
			$KPI_ID = str_replace("-", "", $KPI_ID);
			$DEPART_ID = trim($data[DEPART_ID]);
			$SUBDEPART_ID = trim($data[SUBDEPART_ID]);
			$KPI_YEAR = trim($data[KPI_YEAR]);
			$KPI_NAME = trim($data[KPI_NAME]);
			$KPI_TYPE = trim($data[KPI_TYPE]);
			$KPI_LEVEL = trim($data[KPI_LEVEL]);
			$KEY_SUCCESS = trim($data[KEY_SUCCESS]);
			$POLICY_ID = trim($data[POLICY_ID]);
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$UNIT_COUNT = trim($data[UNIT_COUNT]);
			$TARGET_NUM = $data[TARGET_NUM]+0;
			$REPORT_NUM = trim($data[REPORT_NUM]);
			$KPI_DEFINE = trim($data[KPI_DEFINE]);
			if (!($KPI_DEFINE) || $KPI_DEFINE=="-") $KPI_DEFINE = trim($data[CACULATION]);
			$CONFIRM_SCORE = trim($data[CONFIRM_SCORE]);
			$REPORT_DATE = trim($data[REPORT_DATE]);
//			$DEPART_ID2 = substr($data[DEPART_ID2],0,2);
//			$DEPARTMENT = trim($data[DEPARTMENT]);

			if ($STRATEGY_ID) $PFR_ID = $STRATEGY_ID;
			elseif ($BRANCH_ID) $PFR_ID = $BRANCH_ID;
			elseif ($POLICY_ID) $PFR_ID = $POLICY_ID;

			$ORG_CODE1 = substr($DEPART_ID,0,2);
			$ORG_CODE2 = substr($DEPART_ID,0,4);
			if (substr($DEPART_ID,2,2)=="00") 
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE1' ";
			else
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE2' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$DEPT_ID = $data1[ORG_ID];
			if (!$DEPT_ID) echo "หน่วยงาน - $cmd<br>";

			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_CODE = '$SUBDEPART_ID' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$ORG_ID = $data1[ORG_ID];
			$ORG_NAME = $data1[ORG_NAME];
			if (!$ORG_NAME) echo "ส่วนราชการ - $cmd<br>";

			$cmd = " INSERT INTO PER_KPI(KPI_ID, KPI_NAME, KPI_YEAR, KPI_WEIGHT, KPI_MEASURE, KPI_PER_ID, PFR_ID, 
							  DEPARTMENT_ID, ORG_ID, ORG_NAME, UNDER_ORG_NAME1, KPI_DEFINE, KPI_TYPE, UPDATE_USER, UPDATE_DATE)
							 VALUES ($KPI_ID, '$KPI_NAME', '$KPI_YEAR', $TARGET_NUM, '$UNIT_COUNT', 1, $PFR_ID, $DEPT_ID, $ORG_ID, 
							  '$ORG_NAME', '$UNDER_ORG_NAME1', '$KPI_DEFINE', '$KPI_TYPE', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select KPI_ID from PER_KPI where KPI_ID = $KPI_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CHECK_KPI_ID = $data2[KPI_ID]+0;
			if ($CHECK_KPI_ID!=$KPI_ID) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(KPI_ID) as COUNT_NEW from PER_KPI WHERE KPI_YEAR = '$search_budget_year' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_KPI - $PER_KPI - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	if( $command=='PROJECT' ){
// โครงการ 
		$cmd = " DELETE FROM PER_PROJECT WHERE PJ_YEAR = '$search_budget_year' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

//		$cmd = " SELECT PROJECT_ID, PROJECT_YEAR, PROJECT_NAME, KPI_ID, POLICY_ID, BRANCH_ID, STRATEGY_ID, 
//						  PROJECT_TYPE, PROJECT_CLASS, PROJECT_STATUS, EVALUATION, REPORT_STATUS, TARGET_STATUS, 
//						  a.DEPART_ID, SUBDEPART_ID, START_DATE, FINISH_DATE, OBJECTIVE_NOTE, TARGET_NOTE, 
//						  CONTACT_PERSON, TELEPHONE_NO, b.DEPART_ID2, b.DEPARTMENT
//						  FROM TBL_PROJECT_MAIN a, TBL_DEPARTMENT_CODE b
//						  WHERE a.DEPART_ID=b.DEPART_ID(+)
//						  ORDER BY PROJECT_ID ";
		$cmd = " SELECT PROJECT_ID, PROJECT_YEAR, PROJECT_NAME, POLICY_ID, BRANCH_ID, STRATEGY_ID, KPI_ID, 
						  BUDGET_AMT, BUDGET_PAY, EVALUATION, TARGET_POINT, 
						  a.DEPART_ID, a.SUBDEPART_ID, START_DATE, FINISH_DATE, OBJECTIVE_NOTE, TARGET_NOTE, 
						  CONTACT_PERSON, LAST_UPDATE, EDIT_FLAG, b.DEPART_ID2, b.DEPARTMENT, c.SUBDEPARTMENT
						  FROM TBL_PROJECT_MAIN a, TBL_DEPARTMENT_CODE b, TBL_DEPARTMENT_DETAIL c
						  WHERE a.DEPART_ID=b.DEPART_ID and a.SUBDEPART_ID=c.SUBDEPART_ID
						  ORDER BY PROJECT_ID ";
		$db_att->send_cmd($cmd);
//		$db_att->show_error();
//		echo "<br>";
		while($data = $db_att->get_array()){
			$PER_PROJECT++;
			$PROJECT_ID = trim($data[PROJECT_ID]);
			$PROJECT_ID = str_replace("0000-", "", $PROJECT_ID);
			$PROJECT_YEAR = trim($data[PROJECT_YEAR]);
			$PROJECT_NAME = trim($data[PROJECT_NAME]);
			$KPI_ID = trim($data[KPI_ID]);
			$KPI_ID = str_replace("-", "", $KPI_ID);
			$POLICY_ID = trim($data[POLICY_ID]);
			$BRANCH_ID = trim($data[BRANCH_ID]);
			$STRATEGY_ID = trim($data[STRATEGY_ID]);
			$PROJECT_TYPE = trim($data[PROJECT_TYPE]);
			$PROJECT_CLASS = trim($data[PROJECT_CLASS]);
			$PROJECT_STATUS = trim($data[PROJECT_STATUS]);
			$BUDGET_AMT = trim($data[BUDGET_AMT]);
			$BUDGET_PAY = trim($data[BUDGET_PAY]);
			$EVALUATION = trim($data[EVALUATION]);
			$REPORT_STATUS = trim($data[REPORT_STATUS]);
			$TARGET_STATUS = trim($data[TARGET_STATUS]);
			$DEPART_ID = trim($data[DEPART_ID]);
			$SUBDEPART_ID = trim($data[SUBDEPART_ID]);
			$START_DATE = trim($data[START_DATE]);
			$FINISH_DATE = trim($data[FINISH_DATE]);
			$OBJECTIVE_NOTE = trim($data[OBJECTIVE_NOTE]);
			$TARGET_NOTE = trim($data[TARGET_NOTE]);
			$CONTACT_PERSON = trim($data[CONTACT_PERSON]);
			$TELEPHONE_NO = trim($data[TELEPHONE_NO]);
			$DEPART_ID2 = substr($data[DEPART_ID2],0,2);
			$DEPARTMENT = trim($data[DEPARTMENT]);
			$SUBDEPARTMENT = trim($data[SUBDEPARTMENT]);

			$ORG_CODE1 = substr($DEPART_ID,0,2);
			$ORG_CODE2 = substr($DEPART_ID,0,4);
			if (substr($DEPART_ID,2,2)=="00") 
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE1' ";
			else
				$cmd = " select ORG_ID from PER_ORG where ORG_CODE = '$ORG_CODE2' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$DEPT_ID = $data1[ORG_ID];
//			if (!$DEPT_ID) {
//				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT' ";
//				$db_dpis1->send_cmd($cmd);
//				$data1 = $db_dpis1->get_array();
//				$DEPT_ID = $data1[ORG_ID];
				if (!$DEPT_ID) echo "หน่วยงาน - $cmd<br>";
//			}

			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_CODE = '$SUBDEPART_ID' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$ORG_ID = $data1[ORG_ID];
			$ORG_NAME = $data1[ORG_NAME];
			if (!$ORG_NAME) {
				$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_NAME = '$SUBDEPARTMENT' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$ORG_ID = $data1[ORG_ID];
				$ORG_NAME = $data1[ORG_NAME];
				if (!$ORG_NAME) echo "ส่วนราชการ - $cmd<br>";
			}

			$cmd = " select PFR_ID from PER_KPI where KPI_ID = '$KPI_ID' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PFR_ID = $data1[PFR_ID];
			if (!$PFR_ID) echo "$cmd<br>";

			if (!$ORG_ID) $ORG_ID = "NULL";
			if (!$BUDGET_AMT) $BUDGET_AMT = "NULL";
			if (!$BUDGET_PAY) $BUDGET_PAY = "NULL";
//			$PFR_ID = 1;

			$cmd = " INSERT INTO PER_PROJECT(PJ_ID, PJ_NAME, PJ_YEAR, KPI_ID, PFR_ID, PJ_TYPE, PJ_CLASS, 
							  PJ_STATUS, PJ_EVALUATION, PJ_REPORT_STATUS, PJ_TARGET_STATUS, DEPARTMENT_ID, 
							  ORG_ID, START_DATE, END_DATE, PJ_OBJECTIVE, PJ_TARGET, PJ_ID_REF, PJ_BUDGET_RECEIVE, 
							  PJ_BUDGET_USED, UPDATE_USER, UPDATE_DATE)
							  VALUES ($PROJECT_ID, '$PROJECT_NAME', '$PROJECT_YEAR', $KPI_ID, $PFR_ID, '$PROJECT_TYPE', 
							  '$PROJECT_CLASS', '$PROJECT_STATUS', '$EVALUATION', '$REPORT_STATUS', '$TARGET_STATUS', $DEPT_ID, 
							  $ORG_ID, '$START_DATE', '$FINISH_DATE', '$OBJECTIVE_NOTE', '$TARGET_NOTE', NULL, $BUDGET_AMT, 
							  $BUDGET_PAY, $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);

			$cmd = " select PJ_ID from PER_PROJECT where PJ_ID = $PROJECT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CHECK_PJ_ID = $data2[PJ_ID]+0;
			if ($CHECK_KPI_ID!=$PJ_ID) {
				echo "$cmd<br>==================<br>";
				$db_dpis->show_error();
				echo "<br>end ". ++$i  ."=======================<br>";
			}
		} // end while						

		$cmd = " select count(PJ_ID) as COUNT_NEW from PER_PROJECT WHERE PJ_YEAR = '$search_budget_year' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$COUNT_NEW = $data2[COUNT_NEW] + 0;
		echo "PER_PROJECT - $PER_PROJECT - $COUNT_NEW<br>";
		
		$db_dpis->free_result();
		$db_dpis1->free_result();
		$db_dpis2->free_result();
		$db_att->free_result();
		$db_att1->free_result();
	} // end if

	$UPDATE_USER_TRAIN = 12345;
	if($command=="TRAIN" && $uploadOk){
// ข้อมูลหลักฝึกอบรม (เจริญกรุง) 
		$cmd = " SELECT TRNNO FROM MAP_TRAIN ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			$cmd = " CREATE TABLE MAP_TRAIN (
			TRNNO VARCHAR2(10) NOT NULL,
			TRNCD NUMBER(1) NULL,
			GETDATE VARCHAR2(19) NULL,
			BDGYEAR VARCHAR2(4) NULL,
			TITLE VARCHAR2(255) NULL,
			PLACE VARCHAR2(255) NULL,
			CHNGWTCD VARCHAR2(2) NULL,		
			CNTCD VARCHAR2(3) NULL,		
			STARTDATE VARCHAR2(19) NULL,		
			ENDDATE VARCHAR2(19) NULL,		
			DAYNUM NUMBER(6) NULL,		
			HOURNUM NUMBER(6) NULL,		
			PLCMNG VARCHAR2(1000) NULL,		
			TYPEMONCD NUMBER(1) NULL,		
			TRNAMT NUMBER(16,2) NULL,		
			MNGTYPE NUMBER(1) NULL,		
			TRNMNGCD NUMBER(1) NULL,		
			PLCTYPE NUMBER(1) NULL,
			EXPERT VARCHAR2(255) NULL,
			CONSTRAINT PK_MAP_TRAIN  PRIMARY KEY (TRNNO)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}

		$cmd = " DELETE FROM MAP_TRAIN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";

		$cmd = " DELETE FROM PER_TRAIN WHERE UPDATE_USER = $UPDATE_USER_TRAIN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;
					echo "$cmd<br>==================<br>";
					$db_dpis->show_error();
					echo "<br>end ". ++$i  ."=======================<br>";

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$TRNNO = trim($fields[0]);
				$TRNCD = trim($fields[1]);
				if ($TRNCD==1) $TR_TYPE = 1;
				elseif ($TRNCD==2) $TR_TYPE = 1;
				elseif ($TRNCD==3) $TR_TYPE = 4;
				elseif ($TRNCD==4) $TR_TYPE = 3;
				elseif ($TRNCD==5) $TR_TYPE = 2;
				else $TR_TYPE = 1;

				$GETDATE = trim($fields[2]);
				$BDGYEAR = trim($fields[3]);
				$TITLE = trim($fields[4]);
				$PLACE = trim($fields[5]);
				$CHNGWTCD = trim($fields[6]);
				$CNTCD = trim($fields[7]);
				$STARTDATE = trim($fields[8]);
				$ENDDATE = trim($fields[9]);
				$DAYNUM = trim($fields[10]);
				$HOURNUM = trim($fields[11]);
				$PLCMNG = trim($fields[12]);
				$TYPEMONCD = trim($fields[13]);
				$TRNAMT = trim($fields[14]);
				$MNGTYPE = trim($fields[15]);
				$TRNMNGCD = trim($fields[16]);
				$PLCTYPE = trim($fields[17]);
				$EXPERT = trim($fields[18]);
				if (!$DAYNUM) $DAYNUM = "NULL";
				if (!$HOURNUM) $HOURNUM = "NULL";
				if (!$TYPEMONCD) $TYPEMONCD = "NULL";
				if (!$TRNAMT) $TRNAMT = "NULL";
				if (!$MNGTYPE) $MNGTYPE = "NULL";
				if (!$TRNMNGCD) $TRNMNGCD = "NULL";
				if (!$PLCTYPE) $PLCTYPE = "NULL";
				$TITLE = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TITLE)));
				$PLACE = str_replace(",", "", trim($PLACE));

				$cmd = " select TRNNO from MAP_TRAIN where TRNNO='$TRNNO' ";
				$count_data = $db->send_cmd($cmd);
				if (!$count_data) {
					$cmd = " insert into MAP_TRAIN (TRNNO, TRNCD, GETDATE, BDGYEAR, TITLE, PLACE, CHNGWTCD, CNTCD, STARTDATE, ENDDATE, 
									DAYNUM, HOURNUM, PLCMNG, TYPEMONCD, TRNAMT, MNGTYPE, TRNMNGCD, PLCTYPE, EXPERT)
									 values ('$TRNNO', $TRNCD, '$GETDATE', '$BDGYEAR', '$TITLE', '$PLACE', '$CHNGWTCD', '$CNTCD', '$STARTDATE', '$ENDDATE', 
									 $DAYNUM, $HOURNUM, '$PLCMNG', $TYPEMONCD, $TRNAMT, $MNGTYPE, $TRNMNGCD, $PLCTYPE, '$EXPERT') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT TRNNO FROM MAP_TRAIN WHERE TRNNO = '$TRNNO' "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
				}

				$cmd = " select TR_CODE from PER_TRAIN where TR_NAME='$TITLE' ";
				$count_data = $db->send_cmd($cmd);
				if (!$count_data) {
					$cmd = " insert into PER_TRAIN (TR_CODE, TR_TYPE, TR_NAME, TR_ACTIVE, UPDATE_USER, UPDATE_DATE)
									 values ('$TRNNO', $TR_TYPE, '$TITLE', 1, $UPDATE_USER_TRAIN, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT TR_CODE FROM PER_TRAIN WHERE TR_CODE = '$TR_CODE' "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="MAP_PER" && $uploadOk){
// บุคลากร (เจริญกรุง) 
		$cmd = " SELECT TRNNO FROM MAP_PERSONAL ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			$cmd = " CREATE TABLE MAP_PERSONAL (
			PERID VARCHAR2(10) NOT NULL,
			PERTYPE NUMBER(1) NULL,
			IDCRD VARCHAR2(13) NULL,
			POSNO VARCHAR2(10) NULL,
			SEX NUMBER(1) NULL,
			PRFXCD NUMBER(1) NULL,
			PNAME VARCHAR2(10) NULL,
			FNAME VARCHAR2(100) NULL,
			LNAME VARCHAR2(100) NULL,		
			CONSTRAINT PK_MAP_PERSONAL  PRIMARY KEY (PERID)) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}

		$cmd = " DELETE FROM MAP_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PERID = trim($fields[0]);
				$PERTYPE = trim($fields[1]);
				$IDCRD = trim($fields[2]);
				$POSNO = trim($fields[3]);
				$SEX = trim($fields[4]);
				$PRFXCD = trim($fields[5]);
				$PNAME = trim($fields[6]);
				$FNAME = trim($fields[7]);
				$LNAME = trim($fields[8]);
				if (!$PRFXCD) $PRFXCD = "NULL";

				$cmd = " select PERID from MAP_PERSONAL where PERID='$PERID' ";
				$count_data = $db->send_cmd($cmd);
				if (!$count_data) {
					$cmd = " insert into MAP_PERSONAL (PERID, PERTYPE, IDCRD, POSNO, SEX, PRFXCD, PNAME, FNAME, LNAME)
									 values ('$PERID', $PERTYPE, '$IDCRD', '$POSNO', '$SEX', '$PRFXCD', '$PNAME', '$FNAME', '$LNAME') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT PERID FROM MAP_PERSONAL WHERE PERID = '$PERID' "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
				}

//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="TRAINING" && $uploadOk){
// ประวัติการฝึกอบรม (เจริญกรุง) 
		$cmd = " DELETE FROM PER_TRAINING WHERE UPDATE_USER = $UPDATE_USER_TRAIN ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " select max(TRN_ID) as max_id from PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$TRN_ID = $data[max_id] + 1;
		
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$OT_NAME = trim($fields[0]);
				$PER_NAME = trim($fields[1]);
				$PER_SURNAME = trim($fields[2]);
				$POS_NO = trim($fields[3]);
				$PL_NAME = trim($fields[4]);
				$ORG_NAME1 = trim($fields[5]);
				$ORG_NAME2 = trim($fields[6]);
				$PM_NAME = trim($fields[7]);
				$PER_SPECIALIST = trim($fields[8]);
				$LEVEL_NO = trim($fields[9]);
				$LEVEL_NO = str_pad(trim($LEVEL_NO), 2, "0", STR_PAD_LEFT);
				$PT_NAME = trim($fields[10]);
				$TRN_YEAR = trim($fields[11]);
				$TRN_STARTDATE = $TRN_ENDDATE = ($TRN_YEAR-543)."-00-00";
				$TRN_COURSE_NAME = trim($fields[12]);
				$TRN_COURSE_NAME = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($TRN_COURSE_NAME)));
				$TRN_PLACE = trim($fields[13]);
				$TRN_NO = trim($fields[14]);
				$TRN_TYPE = trim($fields[15]);
				$TRN_REMARK = trim($fields[16]);
				$TRN_DAY = trim($fields[17]);
				$TRN_ORG = trim($fields[18]);
				$TRN_DOCNO = trim($fields[19]);
				$OTHER = trim($fields[20]);
				$ORG_NAME = trim($fields[21]);
				$POS_NO_NAME = trim($fields[22]);
				if ($TRN_TYPE=="ฝึกอบรม") $TRN_TYPE = 1;
				elseif ($TRN_TYPE=="ดูงาน") $TRN_TYPE = 2;
				elseif ($TRN_TYPE=="สัมนา") $TRN_TYPE = 3;
				elseif ($TRN_TYPE=="ประชุม") $TRN_TYPE = 4;
				elseif ($TRN_TYPE=="อื่นๆ") $TRN_TYPE = 5;
				else $TRN_TYPE = 1;
				$CT_CODE = "140";
				$TRN_PASS = 1;

				$cmd = " select PER_ID, PER_CARDNO from PER_PERSONAL where PER_NAME = '$PER_NAME' and  PER_SURNAME = '$PER_SURNAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$PER_CARDNO = trim($data[PER_CARDNO]);

					$TR_CODE = "";
					$cmd = " select TR_CODE from PER_TRAIN where TR_NAME='$TRN_COURSE_NAME' ";
					$count_data = $db->send_cmd($cmd);
					if ($count_data) {
						$data = $db_dpis->get_array();
						$TR_CODE = trim($data[TR_CODE]);
						$TRN_COURSE_NAME = "";
					}
					$cmd = " insert into PER_TRAINING	(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
									TRN_ORG, TRN_PLACE, CT_CODE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
									TRN_DAY, TRN_REMARK, TRN_PASS, TRN_DOCNO, TRN_DOCDATE, TRN_COURSE_NAME)
									values ($TRN_ID, $PER_ID, '$TRN_TYPE', '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
									'$TRN_ORG', '$TRN_PLACE', '$CT_CODE', '$PER_CARDNO', $UPDATE_USER_TRAIN, '$UPDATE_DATE', 
									$TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_DOCNO', '$TRN_DOCDATE', '$TRN_COURSE_NAME') ";
					$db_dpis->send_cmd($cmd);

					$cmd1 = " SELECT TRN_ID FROM PER_TRAINING WHERE TRN_ID = $TRN_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
					$TRN_ID++;
				} else {
					echo "ไม่พบข้อมูลชื่อ-นามสกุล $PER_NAME $PER_SURNAME<br>";
				}

//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="SALARYHIS" && $uploadOk){
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

		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);

				$PER_CARDNO = trim($fields[0]);
				$FULLNAME = trim($fields[1]);
				$LEVEL_NO = trim($fields[2]);
				$EX_CODE = trim($fields[3]);
				if ($EX_CODE=="เงินเดือน") $EX_CODE = "024";
				$MOV_CODE = trim($fields[4]);
				if ($MOV_CODE=="เลื่อนเงินเดือน") $MOV_CODE = "21305";
				elseif ($MOV_CODE=="เงินเดือนเต็มขั้นได้เงินค่าตอบแทนพิเศษ") $MOV_CODE = "21415";
				$SAH_DOCNO = trim($fields[6]);
				$SAH_DOCDATE = "";
				if ($SAH_DOCNO=="กจ.938/2554" || $SAH_DOCNO=="กจ.939/2554" || $SAH_DOCNO=="กจ.940/2554" || $SAH_DOCNO=="กจ.941/2554") $SAH_DOCDATE = "2011-05-20";
				elseif ($SAH_DOCNO=="กบ.645/2554") $SAH_DOCDATE = "2011-04-22";
				elseif ($SAH_DOCNO=="ตง.1002/2554" || $SAH_DOCNO=="ตง.1003/2554" || $SAH_DOCNO=="ตง.1004/2554" || $SAH_DOCNO=="ตง.1103/2554" || $SAH_DOCNO=="ตง.1104/2554")
					$SAH_DOCDATE = "2011-06-10";
				elseif ($SAH_DOCNO=="ตร.689/2554" || $SAH_DOCNO=="ตร.690/2554" || $SAH_DOCNO=="ตร.691/2554" || $SAH_DOCNO=="ตร.692/2554" || $SAH_DOCNO=="ตร.693/2554" || 
					$SAH_DOCNO=="ตร.694/2554") $SAH_DOCDATE = "2011-05-20";
				elseif ($SAH_DOCNO=="ตร.761/2554") $SAH_DOCDATE = "2011-05-26";
				elseif ($SAH_DOCNO=="นน.781/2554" || $SAH_DOCNO=="นน.782/2554") $SAH_DOCDATE = "2011-03-31";
				elseif ($SAH_DOCNO=="นภ.669/2554" || $SAH_DOCNO=="นภ.670/2554" || $SAH_DOCNO=="นภ.671/2554") $SAH_DOCDATE = "2011-04-29";
				elseif ($SAH_DOCNO=="นย.703/2554" || $SAH_DOCNO=="นย.704/2554") $SAH_DOCDATE = "2011-05-20";
				elseif ($SAH_DOCNO=="นย.779/2554" || $SAH_DOCNO=="นย.780/2554" || $SAH_DOCNO=="นย.781/2554") $SAH_DOCDATE = "2011-05-31";
				elseif ($SAH_DOCNO=="นศ.1580/2554" || $SAH_DOCNO=="นศ.1581/2554") $SAH_DOCDATE = "2011-06-06";
				elseif ($SAH_DOCNO=="ปค.440/2554" || $SAH_DOCNO=="ปค.441/2554") $SAH_DOCDATE = "2011-04-29";
				elseif ($SAH_DOCNO=="ปจ.561/2554") $SAH_DOCDATE = "2011-05-25";
				elseif ($SAH_DOCNO=="พบ.917/2554" || $SAH_DOCNO=="พบ.918/2554") $SAH_DOCDATE = "2011-05-30";
				elseif ($SAH_DOCNO=="มค.1013/2554" || $SAH_DOCNO=="มค.1014/2554" || $SAH_DOCNO=="มค.1015/2554") $SAH_DOCDATE = "2011-06-15";
				elseif ($SAH_DOCNO=="มส.1565/2553" || $SAH_DOCNO=="มส.783/2554" || $SAH_DOCNO=="มส.784/2554" || $SAH_DOCNO=="มส.785/2554" || $SAH_DOCNO=="มส.786/2554" || 
					$SAH_DOCNO=="มส.787/2554" || $SAH_DOCNO=="มส.788/2554" || $SAH_DOCNO=="มส.789/2554" || $SAH_DOCNO=="มส.791/2554" || $SAH_DOCNO=="มส.792/2554" || 
					$SAH_DOCNO=="มส.793/2554" || $SAH_DOCNO=="มส.794/2554") $SAH_DOCDATE = "2011-05-20";
				elseif ($SAH_DOCNO=="รน.519/2554" || $SAH_DOCNO=="รน.520/2554") $SAH_DOCDATE = "2011-06-03";
				elseif ($SAH_DOCNO=="สร.968/2554" || $SAH_DOCNO=="สร.969/2554") $SAH_DOCDATE = "2011-04-29";
				else {
					$SAH_DOCDATE = trim($fields[7]);
					echo "xxxxx $SAH_DOCNO<br>";
					$SAH_DOCDATE =  save_date($SAH_DOCDATE);
					if (!$SAH_DOCDATE) echo "SAH_DOCNO - $SAH_DOCNO<br>";
				} 
/*				$SAH_EFFECTIVEDATE = trim($fields[8]);
				$SAH_EFFECTIVEDATE =  save_date($SAH_EFFECTIVEDATE); */
				$SAH_CMD_SEQ = trim($fields[9]);
				$SAH_SEQ_NO = trim($fields[10]);
				$SAH_POS_NO = trim($fields[11]);
				$SAH_OLD_SALARY = trim($fields[12]);
				$SAH_OLD_SALARY = str_replace(",", "", $SAH_OLD_SALARY);
				$SAH_PERCENT_UP = trim($fields[13]);
				$SAH_SALARY_UP = trim($fields[14]);
				$SAH_SALARY_UP = str_replace(",", "", $SAH_SALARY_UP);
				$SAH_SALARY_EXTRA = trim($fields[15]);
				$SAH_SALARY_EXTRA = str_replace(",", "", $SAH_SALARY_EXTRA);
				$SAH_SALARY_MIDPOINT = trim($fields[16]);
				$SAH_SALARY_MIDPOINT = str_replace(",", "", $SAH_SALARY_MIDPOINT);
				$SAH_SALARY = trim($fields[17]);
				$SAH_SALARY = str_replace(",", "", $SAH_SALARY);
				$SAH_KF_YEAR = trim($fields[18]);
				$SAH_KF_YEAR = str_replace(",", "", $SAH_KF_YEAR);
				$SAH_KF_CYCLE = trim($fields[19]);
				$SAH_TOTAL_SCORE = trim($fields[20]);
				if ($SAH_TOTAL_SCORE=="2554") $SAH_TOTAL_SCORE = "NULL";
				$AM_NAME = trim($fields[21]);
				$SAH_POSITION = trim($fields[22]);
				$SAH_ORG = trim($fields[23]);
				$SAH_LAST_SALARY = trim($fields[24]);
				if ($SAH_LAST_SALARY=="ใช่") $SAH_LAST_SALARY = "Y";
				else $SAH_LAST_SALARY = "N";
				$SAH_REMARK = trim($fields[25]) . '  ' . $AM_NAME;
				
				if (!$EX_CODE) $EX_CODE = "024";
				if (!$MOV_CODE) $MOV_CODE = "21305";
				if (!$SAH_DOCNO) $SAH_DOCNO = "-";
				if (!$SAH_DOCDATE) $SAH_DOCDATE = "-";
				if (!$SAH_SALARY_UP && !$SAH_SALARY_EXTRA) $MOV_CODE = "21375";
				if (!$SAH_PERCENT_UP || $SAH_PERCENT_UP=="-") $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP || $SAH_SALARY_UP=="-") $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA || $SAH_SALARY_EXTRA=="-") $SAH_SALARY_EXTRA = "NULL";
				if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
				if (!$SAH_TOTAL_SCORE || $SAH_TOTAL_SCORE=="-") $SAH_TOTAL_SCORE = "NULL";
				if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = 1;
				if (!$SAH_SEQ_NO) $SAH_SEQ_NO = 1;

				$cmd = " select a.PER_ID, a.POS_ID from PER_PERSONAL a, PER_POSITION b 
								  where a.POS_ID = b.POS_ID and PER_CARDNO = '$PER_CARDNO' and a.PER_STATUS =  1 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$POS_ID = $data[POS_ID];

					// update and insert into PER_SALARYHIS 
					$cmd = " select SAH_ID  from PER_SALARYHIS where PER_ID=$PER_ID and  SAH_EFFECTIVEDATE < $SAH_EFFECTIVEDATE
									order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$tmp_SAH_ID = trim($data1[SAH_ID]);

					$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
									where SAH_ID=$tmp_SAH_ID";
					$db_dpis1->send_cmd($cmd);	
					//$db_dpis1->show_error();							
					//echo "<br>";

					$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
					$db_dpis1->send_cmd($cmd);

					$cmd1 = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
									SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
									LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
									SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_OLD_SALARY)
									values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', 
									'$SAH_DOCDATE', '$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', 
									$SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', 
									'$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_POS_NO', 
									$SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, 
									'$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ, $SAH_OLD_SALARY) ";
					$db_dpis->send_cmd($cmd1);
					if ($PER_CARDNO=="1100400087194") {
					$db_dpis->show_error();
					echo "$cmd1<br>";
					}

					$cmd = " update PER_PERSONAL set PER_SALARY = $SAH_SALARY where PER_ID = $PER_ID ";
					$db_dpis->send_cmd($cmd);

					$cmd = " update PER_POSITION set POS_SALARY = $SAH_SALARY where POS_ID = $POS_ID ";
					$db_dpis->send_cmd($cmd);

					$cmd = " select SAH_ID  from PER_SALARYHIS where SAH_ID=$SAH_ID ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$tmp_SAH_ID = trim($data1[SAH_ID]);
					if (!$tmp_SAH_ID) echo "insert ไม่ได้ SAH_ID - $cmd1<br>";

					$SAH_ID++;
				} else echo "PER_CARDNO - $PER_CARDNO $FULLNAME<br>";   
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="DECORATEHIS" && $uploadOk){
		$cmd = " select max(DEH_ID) as max_id from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$DEH_ID = $data[max_id] + 1;
		
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);

				$PER_CARDNO = trim($fields[0]);
				$DEH_DATE = trim($fields[1]);
				$DC_NAME = trim($fields[2]);
				$DEH_GAZETTE = trim($fields[3]);
				$DEH_RECEIVE_DATE = trim($fields[4]);
				$DEH_REMARK = trim($fields[5]);
				$DEH_DATE =  save_date($DEH_DATE);
				
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' or  DC_SHORTNAME = '$DC_NAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$DC_CODE = $data[DC_CODE];
					} 
					else echo "DC_NAME - $DC_NAME<br>";   

					$cmd = " select DC_CODE from PER_DECORATEHIS where PER_ID = $PER_ID and  DC_CODE = '$DC_CODE' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) {			
						$cmd1 = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, DEH_GAZETTE, 
										DEH_RECEIVE_DATE, DEH_REMARK, UPDATE_USER, UPDATE_DATE)
										values ($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', '$DEH_GAZETTE', 
										'$DEH_RECEIVE_DATE', '$DEH_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd1);
						if ($PER_CARDNO=="1100400087194") {
						$db_dpis->show_error();
						echo "$cmd1<br>";
						}

						$cmd = " select DEH_ID  from PER_DECORATEHIS where DEH_ID=$DEH_ID ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$tmp_DEH_ID = trim($data1[DEH_ID]);
						if (!$tmp_DEH_ID) echo "insert ไม่ได้ DEH_ID - $cmd1<br>";

						$DEH_ID++;
					}
				} else echo "PER_CARDNO - $PER_CARDNO<br>";   
//			} else {
//				$inx = 1;
//				$fields = $row;
//			} 
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="KPI_FORM" && $uploadOk){
		$cmd = " select max(DEH_ID) as max_id from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$DEH_ID = $data[max_id] + 1;
		
		$excel_msg = "";
		require_once('ExcelRead.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);

				$PER_CARDNO = trim($fields[0]);
				$DEH_DATE = trim($fields[1]);
				$DC_NAME = trim($fields[2]);
				$DEH_GAZETTE = trim($fields[3]);
				$DEH_RECEIVE_DATE = trim($fields[4]);
				$DEH_REMARK = trim($fields[5]);
				$DEH_DATE =  save_date($DEH_DATE);
				
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' or  DC_SHORTNAME = '$DC_NAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$DC_CODE = $data[DC_CODE];
					} 
					else echo "DC_NAME - $DC_NAME<br>";   

					$cmd = " select DC_CODE from PER_DECORATEHIS where PER_ID = $PER_ID and  DC_CODE = '$DC_CODE' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) {			
						$cmd1 = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, DEH_GAZETTE, 
										DEH_RECEIVE_DATE, DEH_REMARK, UPDATE_USER, UPDATE_DATE)
										values ($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', '$DEH_GAZETTE', 
										'$DEH_RECEIVE_DATE', '$DEH_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd1);
						if ($PER_CARDNO=="1100400087194") {
						$db_dpis->show_error();
						echo "$cmd1<br>";
						}

						$cmd = " select DEH_ID  from PER_DECORATEHIS where DEH_ID=$DEH_ID ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$tmp_DEH_ID = trim($data1[DEH_ID]);
						if (!$tmp_DEH_ID) echo "insert ไม่ได้ DEH_ID - $cmd1<br>";

						$DEH_ID++;
					}
				} else echo "PER_CARDNO - $PER_CARDNO<br>";   
//			} else {
//				$inx = 1;
//				$fields = $row;
//			} 
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if
?>