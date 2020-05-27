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
				$SEQ_NO = trim($fields[0]);
				$POS_NO = trim($fields[1]);
				$OT_NAME = trim($fields[2]);
				$PV_NAME = trim($fields[3]);
				$PPT_NAME = trim($fields[4]);
				$POS_RETIRE = trim($fields[5]);
				$POS_RETIRE_REMARK = trim($fields[6]);
				$POS_DOC_NO = trim($fields[7]);
				$POS_CHANGE_DATE = trim($fields[8]);
				$PER_CARDNO = trim($fields[9]);
				$FULLNAME = trim($fields[10]);
				$PM_NAME = trim($fields[11]);
				$PL_NAME = trim($fields[12]);
				$POSITION_TYPE = trim($fields[13]);
				$LEVEL_SHORTNAME = trim($fields[14]);
				$CO_LEVEL = trim($fields[15]);
				$ORG_NAME = trim($fields[16]);
				$ORG_NAME_1 = trim($fields[17]);
				$ORG_NAME_2 = trim($fields[18]);
				$PR_NAME = trim($fields[19]);
				$POS_RESERVE = trim($fields[20]);
				$POS_RESERVE2 = trim($fields[21]);
				$POS_RESERVE_DESC = trim($fields[22]);
				$POS_RESERVE_DOCNO = trim($fields[23]);
				if ($OT_NAME == "ก") $OT_CODE = "01";
				elseif ($OT_NAME == "ก/ภ") $OT_CODE = "02";
				$PV_CODE = "";
				if ($PV_NAME == "กทม.") $PV_CODE = "1000";
				elseif ($PV_NAME == "อยุธยา") $PV_CODE = "1400";
				else {
					$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$PV_CODE = trim($data[PV_CODE]);
					if (!$PV_CODE) echo "จังหวัด $PV_NAME";
				}
				if ($PPT_NAME == "หลัก") $PPT_CODE = "1";
				elseif ($PPT_NAME == "รอง") $PPT_CODE = "2";
				elseif ($PPT_NAME == "สนับสนุน") $PPT_CODE = "3";
				if ($POS_CHANGE_DATE == "01ต.ค.53") $POS_CHANGE_DATE = "2010-10-01";
				elseif ($POS_CHANGE_DATE == "10พ.ย.53") $POS_CHANGE_DATE = "2010-11-10";
				elseif ($POS_CHANGE_DATE == "12เม.ย.53") $POS_CHANGE_DATE = "2010-04-12";
				elseif ($POS_CHANGE_DATE == "13พ.ย.53") $POS_CHANGE_DATE = "2010-11-13";
				elseif ($POS_CHANGE_DATE == "14มิ.ย.53") $POS_CHANGE_DATE = "2010-06-14";
				elseif ($POS_CHANGE_DATE == "14มี.ค.54") $POS_CHANGE_DATE = "2011-03-14";
				elseif ($POS_CHANGE_DATE == "17ก.พ.54") $POS_CHANGE_DATE = "2011-02-17";
				elseif ($POS_CHANGE_DATE == "17ม.ค.54") $POS_CHANGE_DATE = "2011-01-17";
				elseif ($POS_CHANGE_DATE == "19มี.ค.53") $POS_CHANGE_DATE = "2010-03-19";
				elseif ($POS_CHANGE_DATE == "1ธ.ค.53") $POS_CHANGE_DATE = "2010-12-01";
				elseif ($POS_CHANGE_DATE == "26มี.ค.53") $POS_CHANGE_DATE = "2010-03-26";
				elseif ($POS_CHANGE_DATE == "2มิ.ย.53") $POS_CHANGE_DATE = "2010-06-02";
				elseif ($POS_CHANGE_DATE == "2เม.ย..53") $POS_CHANGE_DATE = "2010-04-02";
				elseif ($POS_CHANGE_DATE == "3 มี.ค.54") $POS_CHANGE_DATE = "2011-03-03";
				elseif ($POS_CHANGE_DATE == "30ต.ค.52") $POS_CHANGE_DATE = "2009-10-30";
				elseif ($POS_CHANGE_DATE == "31พ.ค.53") $POS_CHANGE_DATE = "2010-05-31";
				elseif ($POS_CHANGE_DATE == "5เม.ย.53") $POS_CHANGE_DATE = "2010-04-05";
				elseif ($POS_CHANGE_DATE == "7ก.พ.54") $POS_CHANGE_DATE = "2011-02-07";
				elseif ($POS_CHANGE_DATE == "7พ.ค.53") $POS_CHANGE_DATE = "2010-05-07";
				elseif ($POS_CHANGE_DATE == "ข330/21เม.ย.53") $POS_CHANGE_DATE = "2010-04-21";
				elseif ($POS_CHANGE_DATE == "ใบเงินคือคนใหม่") $POS_CHANGE_DATE = "";
				elseif ($POS_CHANGE_DATE == "ลว.15 ก.พ.53") $POS_CHANGE_DATE = "2010-02-15";
				elseif ($POS_CHANGE_DATE == "ลว.26 ก.พ.53") $POS_CHANGE_DATE = "2010-02-26";
				elseif ($POS_CHANGE_DATE == "ลว.26 เม.ย.53") $POS_CHANGE_DATE = "2010-04-26";
				elseif ($POS_CHANGE_DATE == "ลว.4 มี.ค.53") $POS_CHANGE_DATE = "2010-03-04";
				
				if ($PER_CARDNO) {
					$cmd = " select PER_ID, PER_NAME, PER_SURNAME, PN_NAME from PER_PERSONAL a, PER_PRENAME b 
										where a.PN_CODE=b.PN_CODE(+) and PER_CARDNO = '$PER_CARDNO' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if (!$count_data) {			
						$cmd = " select PER_ID, PER_NAME, PER_SURNAME, PN_NAME from PER_PERSONAL a, PER_PRENAME b
											where a.PN_CODE=b.PN_CODE and (PN_NAME||PER_NAME||' '||PER_SURNAME = '$FULLNAME' or PN_NAME||PER_NAME||'  '||PER_SURNAME = '$FULLNAME' or 
											PN_SHORTNAME||PER_NAME||' '||PER_SURNAME = '$FULLNAME' or PN_SHORTNAME||PER_NAME||'  '||PER_SURNAME = '$FULLNAME') ";
						$count_data = $db_dpis->send_cmd($cmd);
					}
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$PER_ID = $data[PER_ID];
						$PER_NAME = trim($data[PER_NAME]);
						$PER_SURNAME = trim($data[PER_SURNAME]);
						$PN_NAME = trim($data[PN_NAME]);

						if ($FULLNAME!="$PN_NAME$PER_NAME $PER_SURNAME") echo "ชื่อ-นามสกุลไม่ตรงกัน $FULLNAME!=$PN_NAME$PER_NAME $PER_SURNAME<br>";
					} else echo "ไม่พบข้อมูล $cmd $PER_CARDNO $FULLNAME<br>"; 
				}

				$cmd = " select POS_ID, PL_CODE, PM_CODE, ORG_ID, POS_REMARK, POS_DOC_NO 
								  from PER_POSITION where POS_NO = '$POS_NO' and POS_STATUS=1 ";
				$db_dpis->send_cmd($cmd);
				if ($POS_NO=="10")
					echo "$cmd<br>";
				$data = $db_dpis->get_array();
				$POS_ID = $data[POS_ID];
				$OLD_PL_CODE = trim($data[PL_CODE]);
				$OLD_PM_CODE = trim($data[PM_CODE]);
				$ORG_ID = $data[ORG_ID];
				$OLD_POS_REMARK = trim($data[POS_REMARK]);
				$OLD_POS_DOC_NO = trim($data[POS_DOC_NO]);

				$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$OLD_PL_CODE' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$RID_PL_NAME = trim($data[PL_NAME]);
				if ($PL_NAME!=$RID_PL_NAME) echo "ตำแหน่งในสายงานไม่ตรงกัน $POS_NO $PL_NAME!=$RID_PL_NAME<br>";

				$RID_PM_NAME = "";
				if ($PM_NAME && $PM_NAME != $PL_NAME) {
					$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$OLD_PM_CODE' ";
					$db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$RID_PM_NAME = trim($data[PM_NAME]);
					if ($PM_NAME!=$RID_PM_NAME) echo "ตำแหน่งทางบริหารไม่ตรงกัน $POS_NO $PM_NAME!=$RID_PM_NAME<br>";
				}

				if ($POS_ID) {
					$cmd = " BEGIN
									update PER_POSITION set 
									OT_CODE = '$OT_CODE', 
									PPT_CODE = '$PPT_CODE', 
									POS_RETIRE = '$POS_RETIRE', 
									POS_RETIRE_REMARK = '$POS_RETIRE_REMARK', 
									POS_RESERVE = '$POS_RESERVE', 
									POS_RESERVE_DESC = '$POS_RESERVE_DESC', 
									POS_RESERVE_DOCNO = '$POS_RESERVE_DOCNO', 
									POS_RESERVE2 = '$POS_RESERVE_DOCNO' 
									where POS_ID=$POS_ID;
									 END; ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
/*
					if (!$OLD_POS_REMARK) {
						$cmd = " update PER_POSITION set POS_REMARK = '$POS_REMARK' where POS_ID=$POS_ID ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					} */
					if (!$OLD_POS_DOC_NO) {
						$cmd = " update PER_POSITION set POS_DOC_NO = '$POS_DOC_NO'	where POS_ID=$POS_ID ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}
					if ($PV_CODE && $ORG_ID) {
						$cmd = " update PER_ORG set PV_CODE = '$PV_CODE' where ORG_ID=$ORG_ID ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						//echo "$cmd<br>";
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