<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!isset($search_kf_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_kf_year = date("Y") + 543;
		else $search_kf_year = (date("Y") + 543) + 1;
	} // end if
	
	if($search_kf_cycle == 1){
		$KF_START_DATE = ($search_kf_year-544) . "-10-01";
		$KF_END_DATE = ($search_kf_year-543) . "-03-31";
	}elseif($search_kf_cycle == 2){
		$KF_START_DATE = ($search_kf_year-543) . "-04-01";
		$KF_END_DATE = ($search_kf_year-543) . "-09-30";
	} // end if
/*	
	$path_toshow = "C:\\dpis35\\";
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;
	if(is_dir("$path_toshow") == false) {
		mkdir("$path_toshow", 0777);
	}
   $RealFile = $path_tosave_tmp;
   $RealFile = "C:\dpis35\dnp1¡ÃÁÍØ·ÂÒ¹.xls";
 */
//   echo $RealFile;
//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
	if ($command=="UPLOAD") {
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

	if($command=="CONVERT" || ($command=="UPLOAD" && $uploadOk)){	
		$excel_msg = "";
		require_once('excelread.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		$cmd = " delete from per_kpi_form ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from per_personal ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from per_position ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " select max(ORG_ID) as ORG_ID from PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ORG_ID = $data[ORG_ID] + 1;

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$MINISTRY_NAME = trim($fields[0]);
				$DEPARTMENT_NAME = trim($fields[1]);
				$ORG_NAME = trim($fields[2]);
				$POS_NO = trim($fields[3]);
				$PL_NAME = trim($fields[4]);
				$LEVEL_NO = trim($fields[5]);
				$PN_NAME = trim($fields[6]);
				$PER_NAME = trim($fields[7]);
				$PER_SURNAME = trim($fields[8]);
				$PER_SALARY = trim($fields[9]);
				$TOTAL_SCORE = trim($fields[10]);
				$TOTAL_SCORE = str_replace("'", "", trim($TOTAL_SCORE));
				
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT_NAME' and OL_CODE = '02' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$DEPARTMENT_ID = trim($data[ORG_ID]);
				} else echo "$MINISTRY_NAME $DEPARTMENT_NAME<br>";

				$cmd = " select ORG_ID from PER_ORG 
								  where ORG_NAME = '$ORG_NAME' and OL_CODE = '03' and DEPARTMENT_ID = $DEPARTMENT_ID ";
				$count_data = $db_dpis->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
								CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ORG_ID, '$ORG_ID', $ORG_NAME, $ORG_NAME, '03', '01', 
								'140', $DEPARTMENT_ID, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
					$ORG_ID = $MAX_ORG_ID;
					$MAX_ORG_ID++;
				} else {
					$data = $db_dpis->get_array();
					$ORG_ID = $data[ORG_ID];
				}

				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_CODE desc ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PL_CODE = trim($data[PL_CODE]);
				} else echo "$PL_NAME<br>";

				$CL_NAME = "ªÓ¹Ò­¡ÒÃ";
				$PT_CODE = "11";
				if ($PER_SALARY=="''") $PER_SALARY = 0;
				$POS_ID++;
				$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PL_CODE, CL_NAME, POS_SALARY, 
								POS_MGTSALARY, PT_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($POS_ID, $ORG_ID, $POS_NO, '01', '$PL_CODE', '$CL_NAME', $PER_SALARY, 0, '$PT_CODE', 1, 
								$UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				//echo "$cmd<br>";

				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PN_CODE = trim($data[PN_CODE]);
				} else echo "$PN_NAME<br>";

				if ($PER_NAME != "''") {
					$MOV_CODE = "101";
					if ($PN_CODE=="003") $PER_GENDER = 1;
					else $PER_GENDER = 2;
					$PER_ID++;
					$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
									POS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, 
									MR_CODE, PER_STARTDATE, MOV_CODE, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
									VALUES ($PER_ID, 1, '01', '$PN_CODE', $PER_NAME, $PER_SURNAME,	$POS_ID, '$LEVEL_NO', 0, 
									$PER_SALARY, 0, 0, $PER_GENDER, '9', '$UPDATE_DATE', '$MOV_CODE', 1, $UPDATE_USER, '$UPDATE_DATE', 
									$DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";

					$ORG_ID_1 = "NULL"; // ÃÍá¡é
					$cmd = " insert into PER_KPI_FORM (KF_ID, PER_ID, KF_CYCLE, KF_START_DATE, KF_END_DATE, DEPARTMENT_ID, UPDATE_USER, 
									UPDATE_DATE, ORG_ID, TOTAL_SCORE, SALARY_FLAG, ORG_ID_SALARY, KPI_FLAG, ORG_ID_KPI, ORG_ID_1_SALARY, LEVEL_NO) 
									values ($PER_ID, $PER_ID, $search_kf_cycle, '$KF_START_DATE', '$KF_END_DATE', 	
									$DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $ORG_ID, $TOTAL_SCORE, 'Y', $ORG_ID, 'Y', $ORG_ID, $ORG_ID_1, '$LEVEL_NO') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
				}
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}
		echo "</pre>";
		//ÃÕà¿Ãªà¾¨
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if
?>