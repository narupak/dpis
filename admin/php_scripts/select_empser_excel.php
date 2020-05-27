<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 99999;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//$RealFile = str_replace("'","",addslashes("C:'fakepath'rpt_geis_xls.xls"));
	//echo "$command -> $RealFile";
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

		$cmd = " delete from PER_PERSONAL where PER_TYPE = 3 and UPDATE_USER = 99999 ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " delete from PER_POS_EMPSER where UPDATE_USER = 99999 ";
		$db_dpis->send_cmd($cmd);
		
		$cmd = " select max(ORG_ID) as ORG_ID from PER_ORG ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ORG_ID = $data[ORG_ID] + 1;

		$cmd = " select max(POEMS_ID) as POEMS_ID from PER_POS_EMPSER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POEMS_ID = $data[POEMS_ID] + 1;

		$cmd = " select max(PER_ID) as PER_ID from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID] + 1;

		$cmd = " select max(POH_ID) as POH_ID from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POH_ID = $data[POH_ID] + 1;

		$cmd = " select max(SAH_ID) as SAH_ID from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID] + 1;

		$cmd = " select max(EDU_ID) as EDU_ID from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EDU_ID = $data[EDU_ID] + 1;

		$cmd = " select max(DEH_ID) as DEH_ID from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEH_ID = $data[DEH_ID] + 1;

		$cmd = " select max(KF_ID) as KF_ID from PER_KPI_FORM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$KF_ID = $data[KF_ID] + 1;

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$MINISTRY_NAME = trim($fields[0]);
				$DEPARTMENT_NAME = trim($fields[1]);
				$ORG_NAME = trim($fields[2]);
				$col = 0;
//				$ORG_NAME_1 = trim($fields[3]);
//				$ORG_NAME_2 = trim($fields[4]);
				$POEMS_NO = trim($fields[$col+3]);
				$POEMS_NO = str_replace("E", "", $POEMS_NO);
				$EP_NAME = trim($fields[$col+4]);
				$LEVEL_NAME = trim($fields[$col+5]);
				$OT_NAME = trim($fields[$col+6]);
				$PV_NAME = trim($fields[$col+7]);
				$PPS_NAME = trim($fields[$col+8]);
				$PN_NAME = trim($fields[$col+9]);
				$PER_NAME = trim($fields[$col+10]);
				$PER_SURNAME = trim($fields[$col+11]);
				$PER_CARDNO = trim($fields[$col+12]);
				$PER_GENDER_NAME = trim($fields[$col+13]);
				$BIRTHDATE = trim($fields[$col+14]);
				$BIRTHDATE = str_replace("  มกราคม  ", "/01/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  กุมภาพันธ์  ", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  มีนาคม  ", "/03/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  เมษายน  ", "/04/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  พฤษภาคม  ", "/05/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  มิถุนายน  ", "/06/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  กรกฎาคม  ", "/07/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  สิงหาคม  ", "/08/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  กันยายน  ", "/09/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  ตุลาคม  ", "/10/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  พฤศจิกายน  ", "/11/", $BIRTHDATE);
				$BIRTHDATE = str_replace("  ธันวาคม  ", "/12/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มกราคม ", "/01/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" กุมภาพันธ์ ", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" กุมภาพันธื ", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" กุมภาพัน์ ", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace("กุมภาพันธ์", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มีนาคม ", "/03/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" เมษายน ", "/04/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" พฤษภาคม ", "/05/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มิถุนายน ", "/06/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มิถุยานย ", "/06/", $BIRTHDATE);  
				$BIRTHDATE = str_replace(" กรกฎาคม ", "/07/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" กรกฏาคม ", "/07/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" สิงหาคม ", "/08/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" กันยายน ", "/09/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ตุลาคม ", "/10/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" พฤศจิกายน ", "/11/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ธันวาคม ", "/12/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ม.ค. ", "/01/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ก.พ. ", "/02/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มี.ค. ", "/03/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" เม.ย. ", "/04/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" พ.ค. ", "/01/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" มิ.ย. ", "/06/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ก.ค. ", "/07/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ส.ค. ", "/08/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ก.ย. ", "/09/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ต.ค. ", "/10/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" พ.ย. ", "/11/", $BIRTHDATE);
				$BIRTHDATE = str_replace(" ธ.ค. ", "/12/", $BIRTHDATE);
				if ($BIRTHDATE) {
					$arr_temp = explode("/", $BIRTHDATE);
					$DAY = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$MONTH = str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT);
					$YEAR = $arr_temp[2];
					if ($YEAR > 2300) $YEAR = $YEAR - 543;
					$PER_BIRTHDATE = $YEAR."-".$MONTH."-".$DAY;
				}
				$STARTDATE = trim($fields[$col+15]);
				$STARTDATE = str_replace("  มกราคม  ", "/01/", $STARTDATE);
				$STARTDATE = str_replace("  กุมภาพันธ์  ", "/02/", $STARTDATE);
				$STARTDATE = str_replace("  มีนาคม  ", "/03/", $STARTDATE);
				$STARTDATE = str_replace("  เมษายน  ", "/04/", $STARTDATE);
				$STARTDATE = str_replace("  พฤษภาคม  ", "/05/", $STARTDATE);
				$STARTDATE = str_replace("  มิถุนายน  ", "/06/", $STARTDATE);
				$STARTDATE = str_replace("  กรกฎาคม  ", "/07/", $STARTDATE);
				$STARTDATE = str_replace("  สิงหาคม  ", "/08/", $STARTDATE);
				$STARTDATE = str_replace("  กันยายน  ", "/09/", $STARTDATE);
				$STARTDATE = str_replace("  ตุลาคม  ", "/10/", $STARTDATE);
				$STARTDATE = str_replace("  พฤศจิกายน  ", "/11/", $STARTDATE);
				$STARTDATE = str_replace("  ธันวาคม  ", "/12/", $STARTDATE);
				$STARTDATE = str_replace(" มกราคม ", "/01/", $STARTDATE);
				$STARTDATE = str_replace(" กุมภาพันธ์ ", "/02/", $STARTDATE);
				$STARTDATE = str_replace(" มีนาคม ", "/03/", $STARTDATE);
				$STARTDATE = str_replace(" เมษายน ", "/04/", $STARTDATE);
				$STARTDATE = str_replace(" พฤษภาคม ", "/05/", $STARTDATE);
				$STARTDATE = str_replace(" มิถุนายน ", "/06/", $STARTDATE);
				$STARTDATE = str_replace(" กรกฎาคม ", "/07/", $STARTDATE);
				$STARTDATE = str_replace(" กรกฏาคม ", "/07/", $STARTDATE);
				$STARTDATE = str_replace(" สิงหาคม ", "/08/", $STARTDATE);
				$STARTDATE = str_replace(" กันยายน ", "/09/", $STARTDATE);
				$STARTDATE = str_replace(" ตุลาคม ", "/10/", $STARTDATE);
				$STARTDATE = str_replace(" พฤศจิกายน ", "/11/", $STARTDATE);
				$STARTDATE = str_replace(" ธันวาคม ", "/12/", $STARTDATE);
				$STARTDATE = str_replace(" ม.ค. ", "/01/", $STARTDATE);
				$STARTDATE = str_replace(" ก.พ. ", "/02/", $STARTDATE);
				$STARTDATE = str_replace(" มี.ค. ", "/03/", $STARTDATE);
				$STARTDATE = str_replace(" เม.ย. ", "/04/", $STARTDATE);
				$STARTDATE = str_replace(" พ.ค. ", "/01/", $STARTDATE);
				$STARTDATE = str_replace(" มิ.ย. ", "/06/", $STARTDATE);
				$STARTDATE = str_replace(" ก.ค. ", "/07/", $STARTDATE);
				$STARTDATE = str_replace(" ส.ค. ", "/08/", $STARTDATE);
				$STARTDATE = str_replace(" ก.ย. ", "/09/", $STARTDATE);
				$STARTDATE = str_replace(" ต.ค. ", "/10/", $STARTDATE);
				$STARTDATE = str_replace(" พ.ย. ", "/11/", $STARTDATE);
				$STARTDATE = str_replace(" ธ.ค. ", "/12/", $STARTDATE);
				$STARTDATE = str_replace(" ม.ค.", "/01/", $STARTDATE);
				$STARTDATE = str_replace(" ก.พ.", "/02/", $STARTDATE);
				$STARTDATE = str_replace(" มี.ค.", "/03/", $STARTDATE);
				$STARTDATE = str_replace(" เม.ย.", "/04/", $STARTDATE);
				$STARTDATE = str_replace(" พ.ค.", "/01/", $STARTDATE);
				$STARTDATE = str_replace(" มิ.ย.", "/06/", $STARTDATE);
				$STARTDATE = str_replace(" ก.ค.", "/07/", $STARTDATE);
				$STARTDATE = str_replace(" ส.ค.", "/08/", $STARTDATE);
				$STARTDATE = str_replace(" ก.ย.", "/09/", $STARTDATE);
				$STARTDATE = str_replace(" ต.ค.", "/10/", $STARTDATE);
				$STARTDATE = str_replace(" พ.ย.", "/11/", $STARTDATE);
				$STARTDATE = str_replace(" ธ.ค.", "/12/", $STARTDATE);
				if ($STARTDATE) {
					$arr_temp = explode("/", $STARTDATE);
					$DAY = str_pad(trim($arr_temp[0]), 2, "0", STR_PAD_LEFT);
					$MONTH = str_pad(trim($arr_temp[1]), 2, "0", STR_PAD_LEFT);
					$YEAR = $arr_temp[2];
					if ($YEAR > 2300) $YEAR = $YEAR - 543;
					$PER_STARTDATE = $YEAR."-".$MONTH."-".$DAY;
				}
				$EL_NAME = trim($fields[$col+16]);
				$POEMS_SKILL = trim($fields[$col+17]);
				$EN_NAME = trim($fields[$col+18]);
				$INS_NAME = trim($fields[$col+19]);
				$CT_NAME_EDU = trim($fields[$col+20]);
				$MOV_NAME = trim($fields[$col+21]);
				$PEF_NAME = trim($fields[$col+22]);
				$POEMS_SOUTH = trim($fields[$col+23]);
				$PER_SALARY = trim($fields[$col+24]);
				$PER_MGTSALARY = trim($fields[$col+25]);
				$PER_COST_OF_LIVING = trim($fields[$col+26]);
				$PER_CONTACT_COUNT = trim($fields[$col+27]);
				$PPT_NAME = trim($fields[$col+28]);
				$POH_EFFECTIVEDATE = trim($fields[$col+29]);
				$POH_ENDDATE = trim($fields[$col+30]);
				$PUN_TYPE = trim($fields[$col+31]);
				$PEN_NAME = trim($fields[$col+32]);
				$DC_NAME = trim($fields[$col+33]);
				$SAH_PERCENT_UP = trim($fields[$col+34]);
				$SUM_KPI = trim($fields[$col+35]);
				$SUM_COMPETENCE = trim($fields[$col+36]);
				$RESIGN_NAME = trim($fields[$col+37]);
				$PER_DISABILITY = trim($fields[$col+38]);
				if ($POEMS_SOUTH=="ใช่" || $POEMS_SOUTH=="1") $POEMS_SOUTH = 1;
				else $POEMS_SOUTH = 0;
				if ($PER_DISABILITY=="ปกติ" || $PER_DISABILITY=="1" || $PER_DISABILITY=="ว่าง") $PER_DISABILITY = 1;
				elseif ($PER_DISABILITY=="พิการ") $PER_DISABILITY = 2;
				
				if ($OT_NAME=="2") $OT_NAME = "02";
				$cmd = " select OT_CODE from PER_ORG_TYPE where OT_CODE='$OT_NAME' or OT_NAME='$OT_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$OT_CODE = trim($data[OT_CODE]);
				} else echo "$OT_NAME<br>";

				$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME='$PV_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PV_CODE = trim($data[PV_CODE]);
				} else echo "$PV_NAME<br>";

				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT_NAME' and OL_CODE = '02' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$DEPARTMENT_ID = $data[ORG_ID];
				} else echo "$cmd $MINISTRY_NAME $DEPARTMENT_NAME<br>";

				$cmd = " select ORG_ID from PER_ORG 
								  where trim(ORG_NAME) = '$ORG_NAME' and OL_CODE = '03' and DEPARTMENT_ID = $DEPARTMENT_ID ";
				$count_data = $db_dpis->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, PV_CODE, 
								CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ORG_ID, '$ORG_ID', '$ORG_NAME', '$ORG_NAME', '03', '$OT_CODE', '$PV_CODE', 
								'140', $DEPARTMENT_ID, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd1 = " SELECT ORG_ID FROM PER_ORG WHERE ORG_ID = $ORG_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}

					$ORG_ID = $MAX_ORG_ID;
					$MAX_ORG_ID++;
				} else {
					$data = $db_dpis->get_array();
					$ORG_ID = $data[ORG_ID];
				}

				if ($ORG_NAME_1) {
					$cmd = " select ORG_ID from PER_ORG 
									  where ORG_NAME = '$ORG_NAME_1' and OL_CODE = '04' and DEPARTMENT_ID = $DEPARTMENT_ID ";
					$count_data = $db_dpis->send_cmd($cmd);

					if (!$count_data) {			
						echo "$cmd<br>";
						$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
									CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
									VALUES ($MAX_ORG_ID, '$ORG_ID', '$ORG_NAME', '$ORG_NAME', '04', '$OT_CODE', 
									'140', $ORG_ID, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$ORG_ID_1 = $MAX_ORG_ID;
						$MAX_ORG_ID++;
					} else {
						$data = $db_dpis->get_array();
						$ORG_ID_1 = $data[ORG_ID];
					}
				}

				if ($ORG_NAME_2) {
					$cmd = " select ORG_ID from PER_ORG 
									  where ORG_NAME = '$ORG_NAME_2' and OL_CODE = '05' and DEPARTMENT_ID = $DEPARTMENT_ID ";
					$count_data = $db_dpis->send_cmd($cmd);

					if (!$count_data) {			
						echo "$cmd<br>";
						$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_CODE, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
									CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
									VALUES ($MAX_ORG_ID, '$ORG_ID', '$ORG_NAME', '$ORG_NAME', '05', '$OT_CODE', 
									'140', $ORG_ID_1, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$ORG_ID_2 = $MAX_ORG_ID;
						$MAX_ORG_ID++;
					} else {
						$data = $db_dpis->get_array();
						$ORG_ID_2 = $data[ORG_ID];
					}
				}

				$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NO='$LEVEL_NAME' or LEVEL_NAME='$LEVEL_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$LEVEL_NO = trim($data[LEVEL_NO]);
				} else echo "$LEVEL_NAME<br>";

				$cmd = " select EP_CODE from PER_EMPSER_POS_NAME where trim(EP_NAME) = '$EP_NAME' order by EP_CODE desc ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EP_CODE = trim($data[EP_CODE]);
				} else {
					$cmd = " select max(to_number(EP_CODE)) as EP_CODE from PER_EMPSER_POS_NAME ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$EP_CODE = $data[EP_CODE] + 1;
					$EP_CODE = str_pad(trim($EP_CODE), 3, "0", STR_PAD_LEFT);

					$cmd = " INSERT INTO PER_EMPSER_POS_NAME (EP_CODE, EP_NAME, LEVEL_NO, EP_DECOR, EP_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$EP_CODE', '$EP_NAME', '$LEVEL_NO', 0, 1, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					echo "$cmd<br>";
				}

				if (!$MOV_NAME) $MOV_NAME = "ไม่ระบุ";
				$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME='$MOV_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$MOV_CODE = trim($data[MOV_CODE]);
				} else {
					$cmd = " select max(MOV_CODE) as MOV_CODE from PER_MOVMENT WHERE MOV_CODE like '1%' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$MOV_CODE = $data[MOV_CODE] + 1;

					$cmd = " INSERT INTO PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_SUB_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$MOV_CODE', '$MOV_NAME', 1, NULL, 1, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd1 = " SELECT MOV_CODE FROM PER_MOVMENT WHERE MOV_CODE = '$MOV_CODE' "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
				}

				$cmd = " select PPS_CODE from PER_POS_STATUS where PPS_CODE='$PPS_NAME' or PPS_NAME='$PPS_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PPS_CODE = trim($data[PPS_CODE]);
				} else echo "$PPS_NAME<br>";

				$cmd = " select PPT_CODE from PER_PRACTICE where PPT_CODE='$PPT_NAME' or PPT_NAME='$PPT_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PPT_CODE = trim($data[PPT_CODE]);
				} else echo "$PPT_NAME<br>";

				$cmd = " select PEF_CODE from PER_POS_EMPSER_FRAME where PEF_CODE='$PEF_NAME' or PEF_NAME='$PEF_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PEF_CODE = trim($data[PEF_CODE]);
				} else echo "$PEF_NAME<br>";

				if (!$PER_SALARY || $PER_SALARY=="''") $PER_SALARY = 0;
				if (!$ORG_ID_1) $ORG_ID_1 = "NULL";
				if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

				$cmd = " select POEMS_NO from PER_POS_EMPSER where POEMS_NO='$POEMS_NO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if (!$count_data) {			
					$cmd = " INSERT INTO PER_POS_EMPSER (POEMS_ID, ORG_ID, POEMS_NO, ORG_ID_1, ORG_ID_2, EP_CODE, 
									POEM_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, PPT_CODE, PEF_CODE, PPS_CODE,
									POEMS_SKILL, POEMS_SOUTH)
									VALUES ($POEMS_ID, $ORG_ID, '$POEMS_NO', $ORG_ID_1, $ORG_ID_2, '$EP_CODE', 1, $UPDATE_USER, 
									'$UPDATE_DATE', $DEPARTMENT_ID, '$PPT_CODE', '$PEF_CODE', '$PPS_CODE', '$POEMS_SKILL', '$POEMS_SOUTH') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd1 = " SELECT POEMS_ID FROM PER_POS_EMPSER WHERE POEMS_ID = $POEMS_ID "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}

					$POEMS_ID++;
				}

				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PN_CODE = trim($data[PN_CODE]);
				} else {
					if ($PN_NAME=="ว่าที่เรือตรี") {
						$PN_SHORTNAME = "ว่าที่ ร.ต.";
						$PN_CODE = "370";
					}

					$cmd = " INSERT INTO PER_PRENAME (PN_CODE, PN_SHORTNAME, PN_NAME, RANK_FLAG, PN_ACTIVE, UPDATE_USER, UPDATE_DATE)
								VALUES ('$PN_CODE', '$PN_SHORTNAME', '$PN_NAME', 0, 1, $UPDATE_USER, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();

					$cmd1 = " SELECT PN_CODE FROM PER_PRENAME WHERE PN_CODE = '$PN_CODE' "; 
					$count_data = $db_dpis1->send_cmd($cmd1);
					if (!$count_data) {
						echo "$cmd<br>==================<br>";
						$db_dpis->show_error();
						echo "<br>end ". ++$i  ."=======================<br>";
					}
				}

				if ($PER_NAME != "" && $PER_NAME != "- ว่าง -" && $PER_NAME != "ว่าง") {
					if ($PER_GENDER_NAME=="ชาย") $PER_GENDER = 1;
					elseif ($PER_GENDER_NAME=="หญิง") $PER_GENDER = 2;
					$MR_CODE = "1";
					if ($PN_NAME=="นาง") $MR_CODE = "2";
					if (!$PER_DISABILITY || $PER_DISABILITY > 2) $PER_DISABILITY = 1;
					if (!$MOV_CODE) $MOV_CODE = "101";
					$OT_CODE = "08";
					if ($LEVEL_NO=="S1" || $LEVEL_NO=="S2" || $LEVEL_NO=="S3") $OT_CODE = "09";
					if (!$PER_CONTACT_COUNT) $PER_CONTACT_COUNT = "NULL";

					$cmd = " select PER_ID from PER_PERSONAL where PER_NAME='$PER_NAME' and PER_SURNAME='$PER_SURNAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) {			
						$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
										POEMS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, 
										MR_CODE, PER_CARDNO, PER_BIRTHDATE, PER_STARTDATE, MOV_CODE, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID,
										PER_CONTACT_COUNT, PER_DISABILITY)
										VALUES ($PER_ID, 3, '$OT_CODE', '$PN_CODE', '$PER_NAME', '$PER_SURNAME',	$POEMS_ID, '$LEVEL_NO', 0, 
										$PER_SALARY, 0, 0, $PER_GENDER, '$MR_CODE', '$PER_CARDNO', '$PER_BIRTHDATE', '$PER_STARTDATE', '$MOV_CODE', 1, $UPDATE_USER, '$UPDATE_DATE', 
										$DEPARTMENT_ID, $PER_CONTACT_COUNT, $PER_DISABILITY) ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();

						$cmd1 = " SELECT PER_ID FROM PER_PERSONAL WHERE PER_ID = $PER_ID "; 
						$count_data = $db_dpis1->send_cmd($cmd1);
						if (!$count_data) {
							echo "$cmd<br>==================<br>";
							$db_dpis->show_error();
							echo "<br>end ". ++$i  ."=======================<br>";
						}

						$PER_ID++;
					}
				}

				$EL_CODE = $EN_CODE = $EM_CODE = $INS_CODE = "";
				$cmd = " select EL_CODE from PER_EDUCLEVEL where EL_NAME = '$EL_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EL_CODE = trim($data[EL_CODE]);
				} else {
					echo "<br>$EL_NAME ". ++$i  ."=======================<br>";
				}

				$arr_temp = explode("(", $EN_NAME);
				$EN_NAME = $arr_temp[0];
				$EN_SHORTNAME = $arr_temp[1];
				$cmd = " select EN_CODE from PER_EDUCNAME 
								where EN_NAME = '$EN_NAME' or EN_SHORTNAME = '$EN_NAME' or EN_SHORTNAME = '$EN_SHORTNAME' or EN_NAME = 'ป.$EN_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EN_CODE = trim($data[EN_CODE]);
				} else {
					echo "<br>$EN_NAME ". ++$i  ."=======================<br>";
				}

				$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$INS_CODE = trim($data[INS_CODE]);
				} else {
					echo "<br>$INS_NAME ". ++$i  ."=======================<br>";
				}

				if ($EL_CODE) {
					if (!$EN_CODE) $EN_CODE = "NULL";
					else  $EN_CODE = "'$EN_CODE'";
					if ($INS_CODE) {
						$INS_CODE = "'$INS_CODE'";
						$INS_NAME = "NULL";
					} else { 
						$INS_CODE = "NULL";
						$INS_NAME = "'$INS_NAME'";
					}

					$cmd = " select EDU_ID from PER_EDUCATE where PER_ID = $PER_ID and EDU_TYPE='||2||' and EL_CODE='$EL_CODE' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) {			
						$cmd = " INSERT INTO PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EN_CODE, INS_CODE, EDU_TYPE, PER_CARDNO, 
										EL_CODE, EDU_INSTITUTE, UPDATE_USER, UPDATE_DATE)
										VALUES ($EDU_ID, $PER_ID, 1, $EN_CODE, $INS_CODE, '||2||', '$PER_CARDNO', '$EL_CODE', $INS_NAME, $UPDATE_USER, '$UPDATE_DATE') ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();

						$cmd1 = " SELECT EDU_ID FROM PER_EDUCATE WHERE EDU_ID = $EDU_ID "; 
						$count_data = $db_dpis1->send_cmd($cmd1);
						if (!$count_data) {
							echo "$cmd<br>==================<br>";
							$db_dpis->show_error();
							echo "<br>end ". ++$i  ."=======================<br>";
						}

						$EDU_ID++;
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
?>