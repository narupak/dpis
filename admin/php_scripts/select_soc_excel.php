<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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
		require_once('excelread.php');

	 	$config = array('excel_filename'=>$RealFile,
 						'excel_sheet'=>0,'excel_numeric'=>FALSE,'excel_duplicate'=>FALSE,'excel_sort'=>FALSE,'excel_debug'=>FALSE);

	 	$excel_data = excel_read($config);
 
		echo "<pre>";

		$cmd = " select max(DEH_ID) as DEH_ID from PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEH_ID = $data[DEH_ID] + 1;

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[0]);
				$FNAME = trim($fields[1]);
				$LNAME = trim($fields[2]);
				$INS_CODE = trim($fields[3]);
				$INS_DATE = trim($fields[4]);
				$POS_NAME = trim($fields[5]);
				$DEP_NAME = trim($fields[6]);
				$DEH_DATE =  save_date($INS_DATE);
				
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];
				} else echo "$PER_CARDNO $FNAME $LNAME $INS_CODE $POS_NAME $DEP_NAME $DEH_DATE<br>";

				$cmd = " select DC_CODE from PER_DECORATION where DC_SHORTNAME = '$INS_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$DC_CODE = trim($data[DC_CODE]);
				} else echo "$INS_CODE<br>";

				$cmd = " select DEH_ID from PER_DECORATEHIS where PER_ID = $PER_ID and DC_CODE = '$DC_CODE' ";
				$count_data = $db_dpis->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, DEH_REMARK) 
							values	($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', '$POS_NAME $DEP_NAME') ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
					$DEH_ID++;
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