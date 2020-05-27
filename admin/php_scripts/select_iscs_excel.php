<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
	if ($command=="SENIOR_EXCUSIVE") {
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

	if($command=="SENIOR_EXCUSIVE" && $uploadOk)){	
		if ($search_se_no)
			$cmd = " delete from PER_SENIOR_EXCUSIVE where SE_NO = '$search_se_no' ";
		else
			$cmd = " delete from PER_SENIOR_EXCUSIVE ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select max(SE_ID) as max_id from PER_SENIOR_EXCUSIVE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SE_ID = $data[max_id] + 1;
		
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

				$SE_CODE = trim($fields[1]);
				if (strlen(trim($SE_CODE)) > 4 && trim($SE_CODE) < "76000")
					$SE_NO = substr($SE_CODE,1,2);
				else
					$SE_NO = substr($SE_CODE,0,2);
				if ($SE_NO=="76") {
					$SE_STARTDATE = "2012-05-25";
					$SE_ENDDATE = "2012-09-14";
				} elseif ($SE_NO=="75") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="74") {
					$SE_STARTDATE = "2011-04-21";
					$SE_ENDDATE = "2011-08-19";
				} elseif ($SE_NO=="73") {
					$SE_STARTDATE = "2011-04-21";
					$SE_ENDDATE = "2011-08-19";
				} elseif ($SE_NO=="72") {
					$SE_STARTDATE = "2010-12-29";
					$SE_ENDDATE = "2011-04-22";
				} elseif ($SE_NO=="71") {
					$SE_STARTDATE = "2010-12-29";
					$SE_ENDDATE = "2011-04-22";
				} elseif ($SE_NO=="70") {
					$SE_STARTDATE = "2010-04-19";
					$SE_ENDDATE = "2010-08-20";
				} elseif ($SE_NO=="69") {
					$SE_STARTDATE = "2010-04-19";
					$SE_ENDDATE = "2010-08-20";
				} elseif ($SE_NO=="68") {
					$SE_STARTDATE = "2009-12-19";
					$SE_ENDDATE = "2010-04-22";
				} elseif ($SE_NO=="67") {
					$SE_STARTDATE = "2009-12-19";
					$SE_ENDDATE = "2010-04-22";
				} elseif ($SE_NO=="66") {
					$SE_STARTDATE = "2009-04-24";
					$SE_ENDDATE = "2009-08-28";
				} elseif ($SE_NO=="65") {
					$SE_STARTDATE = "2009-04-24";
					$SE_ENDDATE = "2009-08-28";
				} elseif ($SE_NO=="64") {
					$SE_STARTDATE = "2008-12-12";
					$SE_ENDDATE = "2009-04-10";
				} elseif ($SE_NO=="63") {
					$SE_STARTDATE = "2008-12-12";
					$SE_ENDDATE = "2009-04-10";
				} elseif ($SE_NO=="62") {
					$SE_STARTDATE = "2008-05-02";
					$SE_ENDDATE = "2008-09-18";
				} elseif ($SE_NO=="61") {
					$SE_STARTDATE = "2008-05-02";
					$SE_ENDDATE = "2008-09-18";
				} elseif ($SE_NO=="60") {
					$SE_STARTDATE = "2008-05-02";
					$SE_ENDDATE = "2008-09-18";
				} elseif ($SE_NO=="59") {
					$SE_STARTDATE = "2008-01-04";
					$SE_ENDDATE = "2008-05-15";
				} elseif ($SE_NO=="58") {
					$SE_STARTDATE = "2008-01-04";
					$SE_ENDDATE = "2008-05-15";
				} elseif ($SE_NO=="57") {
					$SE_STARTDATE = "2008-01-04";
					$SE_ENDDATE = "2008-05-15";
				} elseif ($SE_NO=="56") {
					$SE_STARTDATE = "2007-06-01";
					$SE_ENDDATE = "2007-09-18";
				} elseif ($SE_NO=="55") {
					$SE_STARTDATE = "2007-06-01";
					$SE_ENDDATE = "2007-09-18";
				} elseif ($SE_NO=="54") {
					$SE_STARTDATE = "2007-03-19";
					$SE_ENDDATE = "2007-07-19";
				} elseif ($SE_NO=="53") {
					$SE_STARTDATE = "2007-03-19";
					$SE_ENDDATE = "2007-07-19";
				} elseif ($SE_NO=="52") {
					$SE_STARTDATE = "2006-11-20";
					$SE_ENDDATE = "2007-03-09";
				} elseif ($SE_NO=="51") {
					$SE_STARTDATE = "2006-11-20";
					$SE_ENDDATE = "2007-03-09";
				} elseif ($SE_NO=="50") {
					$SE_STARTDATE = "2006-03-20";
					$SE_ENDDATE = "2006-08-18";
				} elseif ($SE_NO=="49") {
					$SE_STARTDATE = "2006-03-20";
					$SE_ENDDATE = "2006-08-18";
				} elseif ($SE_NO=="48") {
					$SE_STARTDATE = "2006-03-20";
					$SE_ENDDATE = "2006-08-18";
				} elseif ($SE_NO=="47") {
					$SE_STARTDATE = "2006-03-20";
					$SE_ENDDATE = "2006-08-18";
				} elseif ($SE_NO=="46") {
					$SE_STARTDATE = "2005-08-04";
					$SE_ENDDATE = "2006-02-10";
				} elseif ($SE_NO=="45") {
					$SE_STARTDATE = "2005-08-04";
					$SE_ENDDATE = "2006-02-10";
				} elseif ($SE_NO=="44") {
					$SE_STARTDATE = "2005-03-10";
					$SE_ENDDATE = "2005-08-09";
				} elseif ($SE_NO=="43") {
					$SE_STARTDATE = "2005-03-10";
					$SE_ENDDATE = "2005-08-09";
				} elseif ($SE_NO=="42") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="41") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="40") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="39") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="38") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="37") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="36") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="35") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="34") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="33") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="32") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="31") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="30") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="29") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="28") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="27") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="26") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="25") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="24") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="23") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="22") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="21") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="20") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="19") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="18") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="17") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="16") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="15") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="14") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="13") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="12") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="11") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="10") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="09") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="08") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="07") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="06") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="05") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="04") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="03") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="02") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				} elseif ($SE_NO=="01") {
//					$SE_STARTDATE = "2012-05-25";
//					$SE_ENDDATE = "2012-05-25";
				}
				$PN_NAME = trim($fields[4]);
				$SE_NAME = trim($fields[5]);
				$SE_SURNAME = trim($fields[6]);
				$SE_SURNAME = str_replace("R", "", $SE_SURNAME);
				$SE_SURNAME = str_replace("D", "", $SE_SURNAME);
				$SE_CARDNO = trim($fields[8]);
				if ($SE_CARDNO=="42") $SE_CARDNO = "";
				$SE_MINISTRY_NAME = trim($fields[9]);
				if ($SE_MINISTRY_NAME=="42") $SE_MINISTRY_NAME = "";
				$SE_DEPARTMENT_NAME = trim($fields[10]);
				if ($SE_DEPARTMENT_NAME=="42") $SE_DEPARTMENT_NAME = "";
				$SE_ORG_NAME = trim($fields[11]);
				if ($SE_ORG_NAME=="42") $SE_ORG_NAME = "";
				$SE_LINE = trim($fields[12]);
				if ($SE_LINE=="42") $SE_LINE = "";
				$LEVEL_NO = trim($fields[13]);
				if ($LEVEL_NO=="42") $LEVEL_NO = "";
				$SE_MGT = trim($fields[14]);
				if ($SE_MGT=="42") $SE_MGT = "";
				$SE_TRAIN_POSITION = trim($fields[18]);
				$SE_TRAIN_DEPARTMENT = trim($fields[19]);
				$SE_TRAIN_MINISTRY = trim($fields[20]);
				$SE_PASS = trim($fields[21]);
				$SE_YEAR = trim($fields[22]);
//				$SE_YEAR = trim($fields[24]);
//				$SE_YEAR = trim($fields[25]);
				$SE_MOBILE = trim($fields[26]);
				$SE_TEL = trim($fields[27]);
				$SE_FAX = trim($fields[28]);
				if ($LEVEL_NO=="ประเภทบริหาร ระดับสูง") $LEVEL_NO = "M2";
				elseif ($LEVEL_NO=="ประเภทบริหาร ระดับต้น") $LEVEL_NO = "M1";
				elseif ($LEVEL_NO=="ประเภทอำนวยการ ระดับสูง") $LEVEL_NO = "D2";
				elseif ($LEVEL_NO=="ประเภทอำนวยการ ระดับต้น") $LEVEL_NO = "D1";
				elseif ($LEVEL_NO=="ประเภทวิชาการ ระดับทรงคุณวุฒิ") $LEVEL_NO = "K5";
				elseif ($LEVEL_NO=="ประเภทวิชาการ ระดับเชี่ยวชาญ") $LEVEL_NO = "K4";
				elseif ($LEVEL_NO=="ประเภทวิชาการ ระดับชำนาญการพิเศษ") $LEVEL_NO = "K3";
				elseif ($LEVEL_NO=="ประเภทวิชาการ ระดับชำนาญการ") $LEVEL_NO = "K2";
				elseif ($LEVEL_NO=="ประเภทวิชาการ ระดับปฏิบัติการ") $LEVEL_NO = "K1";
				elseif ($LEVEL_NO=="ประเภททั่วไป ระดับอาวุโส") $LEVEL_NO = "O3";
				elseif ($LEVEL_NO=="ประเภททั่วไป ระดับชำนาญงาน") $LEVEL_NO = "O2";
				elseif ($LEVEL_NO=="ประเภททั่วไป ระดับชำนาญการ") $LEVEL_NO = "O1";
				
				if ($SE_PASS=="สำนักงานปลัดกระทรวงสาธารณสุข") $SE_PASS = "";
				$PN_CODE = "";
				if ($PN_NAME) {
					$cmd = " select PN_CODE from PER_PRENAME 
									  where PN_NAME = '$PN_NAME' or PN_SHORTNAME = '$PN_NAME' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$PN_CODE = trim($data[PN_CODE]);
					} else {
						echo "$PN_NAME<br>";
					}
				}

				$cmd = " select PER_ID from PER_PERSONAL 
								  where (PER_NAME = '$SE_NAME' and PER_SURNAME = '$SE_SURNAME') or PER_CARDNO = '$SE_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
					$SE_TYPE = 1;
				} else {
					$PER_ID = "NULL";   
					$SE_TYPE = 2;
				}

				if (strpos($SE_CODE,"รุ่น") === false) {
					$cmd1 = " insert into PER_SENIOR_EXCUSIVE (SE_ID, SE_TYPE, PER_ID, SE_CODE, SE_NO, PN_CODE, SE_NAME, 
									SE_SURNAME, SE_CARDNO, SE_MINISTRY_NAME, SE_DEPARTMENT_NAME, SE_ORG_NAME, 
									SE_ORG_NAME1, SE_ORG_NAME2, SE_LINE, LEVEL_NO, SE_MGT, SE_TRAIN_POSITION, 
									SE_TRAIN_MINISTRY, SE_TRAIN_DEPARTMENT, SE_PASS, SE_YEAR, SE_BIRTHDATE, SE_STARTDATE, 
									SE_ENDDATE, SE_TEL, SE_FAX, SE_MOBILE, SE_EMAIL, UPDATE_USER, UPDATE_DATE)
									values ($SE_ID, $SE_TYPE, $PER_ID, '$SE_CODE', '$SE_NO', '$PN_CODE', '$SE_NAME', '$SE_SURNAME', 
									'$SE_CARDNO', '$SE_MINISTRY_NAME', '$SE_DEPARTMENT_NAME', '$SE_ORG_NAME', 
									'$SE_ORG_NAME1', '$SE_ORG_NAME2', '$SE_LINE', '$LEVEL_NO', '$SE_MGT', '$SE_TRAIN_POSITION', 
									'$SE_TRAIN_MINISTRY', '$SE_TRAIN_DEPARTMENT', '$SE_PASS', '$SE_YEAR', '$SE_BIRTHDATE', '$SE_STARTDATE', 
									'$SE_ENDDATE', '$SE_TEL', '$SE_FAX', '$SE_MOBILE', '$SE_EMAIL', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis->send_cmd($cmd1);
					if ($PER_CARDNO=="1100400087194") {
					$db_dpis->show_error();
					echo "$cmd1<br>";
					}

					$cmd = " select SE_ID  from PER_SENIOR_EXCUSIVE where SE_ID=$SE_ID ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$tmp_SE_ID = trim($data1[SE_ID]);
					if (!$tmp_SE_ID) echo "insert ไม่ได้ SE_ID - $cmd1<br>";
					else $SE_ID++;
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