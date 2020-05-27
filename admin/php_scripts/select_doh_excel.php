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
			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$POS_NO = trim($fields[1]);
				if ($POS_NO) {
					$PM_NAME = trim($fields[2]);
					$PL_NAME = trim($fields[3]);
					$POSITION_TYPE = trim($fields[4]);
					$CL_NAME = trim($fields[5]);
					if ($POSITION_TYPE=="บริหาร" || $POSITION_TYPE=="อำนวยการ") $CL_NAME = $POSITION_TYPE.$CL_NAME;
					
					$cmd = " select POS_ID, PM_CODE, PL_CODE, CL_NAME from PER_POSITION where POS_NO = '$POS_NO' ";
					$count_data = $db_dpis->send_cmd($cmd);
					if ($count_data) {			
						$data = $db_dpis->get_array();
						$POS_ID = $data[POS_ID];
						$DPIS_PM_CODE = trim($data[PM_CODE]);
						$DPIS_PL_CODE = trim($data[PL_CODE]);
						$DPIS_CL_NAME = trim($data[CL_NAME]);

						$DPIS_PM_NAME = "";
						if ($DPIS_PM_CODE) {
							$cmd = " select PM_NAME from PER_MGT where PM_CODE = '$DPIS_PM_CODE' order by PM_SEQ_NO ";
							$db_dpis->send_cmd($cmd);
							$data = $db_dpis->get_array();
							$DPIS_PM_NAME = trim($data[PM_NAME]);
							if ($PM_NAME!=$DPIS_PM_NAME) echo "ตำแหน่งในการบริหารงานไม่ตรงกัน $PM_NAME!=$DPIS_PM_NAME<br>";
						}

						$cmd = " select PL_NAME from PER_LINE where PL_CODE = '$DPIS_PL_CODE' order by PL_SEQ_NO ";
						$db_dpis->send_cmd($cmd);
						$data = $db_dpis->get_array();
						$DPIS_PL_NAME = trim($data[PL_NAME]);
						if ($PL_NAME!=$DPIS_PL_NAME) echo "ตำแหน่งในสายงานไม่ตรงกัน $PL_NAME!=$DPIS_PL_NAME<br>";
						if ($CL_NAME!=$DPIS_CL_NAME) echo "ช่วงระดับตำแหน่งไม่ตรงกัน $CL_NAME!=$DPIS_CL_NAME<br>";

						if ($PM_NAME && $DPIS_PM_NAME && $PM_NAME!=$DPIS_PM_NAME) {
							$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' order by PM_SEQ_NO ";
							$db_dpis->send_cmd($cmd);
							$data = $db_dpis->get_array();
							$PM_CODE = trim($data[PM_CODE]);

							$cmd = " update PER_POSITION set PM_CODE = '$PM_CODE' where POS_ID=$POS_ID ";
							//$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							echo "$cmd<br>";
						}

						if ($PL_NAME!=$DPIS_PL_NAME) {
							$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' order by PL_SEQ_NO ";
							$db_dpis->send_cmd($cmd);
							$data = $db_dpis->get_array();
							$PL_CODE = trim($data[PL_CODE]);

							$cmd = " update PER_POSITION set PL_CODE = '$PL_CODE' where POS_ID=$POS_ID ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "$cmd<br>";
						}

						if ($CL_NAME!=$DPIS_CL_NAME) {
							$cmd = " update PER_POSITION set CL_NAME = '$CL_NAME' where POS_ID=$POS_ID ";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							//echo "$cmd<br>";
						}
					} else echo "ไม่พบข้อมูล $POS_NO $PM_NAME $PL_NAME $CL_NAME<br>";
				}
			} else {
				$inx = 1;
				$fields = $row;
			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

?>