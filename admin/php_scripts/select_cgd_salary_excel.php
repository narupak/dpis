<?	
//	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

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

	$SAH_DOCDATE =  save_date($SAH_DOCDATE);
	$SAH_ENDDATE =  save_date($SAH_ENDDATE);

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

		foreach($excel_data as $row) {
//			if($inx) {
				$values = implode("','",$row);
				$fields = explode(',',$values);
				$fields = str_replace("'", "", $fields);
				$PER_CARDNO = trim($fields[0]);
				$NAME = trim($fields[1]);
				$SAH_POS_NO = ($fields[2]) + 0;
				$SAH_POSITION = trim($fields[3]);
				$POSITION_LEVEL = trim($fields[4]);
				$PER_SALARY = trim($fields[5]);
				$PER_SALARY = str_replace(",", "", $PER_SALARY);
				$LAYER_SALARY_MAX = trim($fields[6]);
				$LAYER_SALARY_MAX = str_replace(",", "", $LAYER_SALARY_MAX);
				$SAH_SALARY_MIDPOINT = trim($fields[7]);
				$SAH_SALARY_MIDPOINT = str_replace(",", "", $SAH_SALARY_MIDPOINT);
				$SAH_TOTAL_SCORE = trim($fields[8]);
				$SAH_PERCENT_UP = trim($fields[9]);
//				$SAH_SALARY_UP = trim($fields[10]);
				$SAH_SALARY_UP = trim($fields[11]);
				$SAH_SALARY_UP = str_replace(",", "", $SAH_SALARY_UP);
				$SAH_SALARY_EXTRA = trim($fields[12]);
				$SAH_SALARY_EXTRA = str_replace(",", "", $SAH_SALARY_EXTRA);
				$SAH_SALARY_TOTAL = trim($fields[13]);
				$SAH_SALARY_TOTAL = str_replace(",", "", $SAH_SALARY_TOTAL);
				$SAH_SALARY = trim($fields[14]);
				$SAH_SALARY = str_replace(",", "", $SAH_SALARY);
				$AM_NAME = trim($fields[15]);
				$SAH_REMARK = trim($fields[16]);

				$SM_CODE = "";
				$cmd = " select MOV_CODE from PER_MOVMENT where MOV_NAME = '$AM_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$MOV_CODE = $data[MOV_CODE];
				} else {
					if ($AM_NAME=="ดีเด่น") $MOV_CODE = "21345";
					elseif ($AM_NAME=="ดีมาก") $MOV_CODE = "21335";
					elseif ($AM_NAME=="ดี") $MOV_CODE = "21325";
					elseif ($AM_NAME=="พอใช้") $MOV_CODE = "21315";
					elseif ($AM_NAME=="ขาดราชการ") $MOV_CODE = "22374";
					elseif ($AM_NAME=="ควรปรับปรุง" || $AM_NAME=="ปรับปรุง" || $AM_NAME=="ต้องปรับปรุง") $MOV_CODE = "21375";
					elseif ($AM_NAME=="ลาไปเข้ารับการอบรม") $MOV_CODE = "22384";
					elseif ($AM_NAME=="ลาไปศึกษาและรับเงินเดือน") $MOV_CODE = "22383";
					elseif ($AM_NAME=="ลาศึกษา") $MOV_CODE = "22383";
					elseif ($AM_NAME=="อบรมต่างประเทศ") $MOV_CODE = "22384";
					elseif ($AM_NAME=="สั่งพักราชการ") $MOV_CODE = "22377";
					elseif ($AM_NAME=="ประเภทเลื่อนขั้นเงินเดือน") $MOV_CODE = "213";
					elseif ($AM_NAME=="เลื่อนขั้นเงินเดือน 0.5 ขั้น" || $AM_NAME=="0.5 ขั้น" || $SAH_REMARK=="0.5 ขั้น") {
						$MOV_CODE = "21310";
						$SM_CODE = "1";
					} elseif ($AM_NAME=="เลื่อนขั้นเงินเดือน 1 ขั้น" || $AM_NAME=="1 ขั้น" || $SAH_REMARK=="1 ขั้น") {
						$MOV_CODE = "21320";
						$SM_CODE = "2";
					} elseif ($AM_NAME=="เลื่อนขั้นเงินเดือน 1.5 ขั้น" || $AM_NAME=="1.5 ขั้น" || $SAH_REMARK=="1.5 ขั้น") {
						$MOV_CODE = "21330";
						$SM_CODE = "3";
					} elseif ($AM_NAME=="เลื่อนขั้นเงินเดือน 2 ขั้น" || $AM_NAME=="2 ขั้น" || $SAH_REMARK=="2 ขั้น") {
						$MOV_CODE = "21340";
						$SM_CODE = "4";
					} elseif ($AM_NAME=="เงินค่าตอบแทนพิเศษร้อยละ 2" || $AM_NAME=="เงินตอบแทนพิเศษ 2%" || $SAH_REMARK=="เงินตอบแทนพิเศษ 2%") {
						$MOV_CODE = "21420";
						$SM_CODE = "5";
					} elseif ($AM_NAME=="เงินค่าตอบแทนพิเศษร้อยละ 2" || $AM_NAME=="เงินตอบแทนพิเศษ 4%" || $SAH_REMARK=="เงินตอบแทนพิเศษ 4%") {
						$MOV_CODE = "21430";
						$SM_CODE = "17";
					} else  $MOV_CODE = "21375";
				}

				$POSITION_LEVEL = str_replace("บ 1", "บ1", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("บ 2", "บ2", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ส 1", "ส1", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ส 2", "ส2", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ส 3", "ส3", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ส 4", "ส4", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ช 1", "ช1", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ช 2", "ช2", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ช 3", "ช3", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ช 4", "ช4", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ท 1", "ท1", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ท 2", "ท2", $POSITION_LEVEL);
				$POSITION_LEVEL = str_replace("ท 3", "ท3", $POSITION_LEVEL);
				$cmd = " select LEVEL_NO from PER_LEVEL 
								  where LEVEL_NAME = '$POSITION_LEVEL' or  LEVEL_SHORTNAME = '$POSITION_LEVEL' or  POSITION_LEVEL = '$POSITION_LEVEL' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$LEVEL_NO = $data[LEVEL_NO];
				} else {
					if ($POSITION_LEVEL=="ปฏิบัติงาน") $LEVEL_NO = "O1";
					elseif ($POSITION_LEVEL=="ชำนาญงาน") $LEVEL_NO = "O2";
					elseif ($POSITION_LEVEL=="อาวุโส") $LEVEL_NO = "O3";
					elseif ($POSITION_LEVEL=="ทักษะพิเศษ") $LEVEL_NO = "O4";
					elseif ($POSITION_LEVEL=="ปฏิบัติการ") $LEVEL_NO = "K1";
					elseif ($POSITION_LEVEL=="ชำนาญการ") $LEVEL_NO = "K2";
					elseif ($POSITION_LEVEL=="ชำนาญการพิเศษ") $LEVEL_NO = "K3";
					elseif ($POSITION_LEVEL=="เชี่ยวชาญ") $LEVEL_NO = "K4";
					elseif ($POSITION_LEVEL=="ทรงคุณวุฒิ") $LEVEL_NO = "K5";
					elseif ($POSITION_LEVEL=="อำนวยการต้น") $LEVEL_NO = "D1";
					elseif ($POSITION_LEVEL=="อำนวยการสูง") $LEVEL_NO = "D2";
					elseif ($POSITION_LEVEL=="บริหารต้น") $LEVEL_NO = "M1";
					elseif ($POSITION_LEVEL=="บริหารสูง") $LEVEL_NO = "M2";
					elseif ($POSITION_LEVEL=="ต้น") 
						if (substr($SAH_POSITION,0,9)=="นักบริหาร") $LEVEL_NO = "M1";
						else $LEVEL_NO = "D1";
					elseif ($POSITION_LEVEL=="สูง") 
						if (substr($SAH_POSITION,0,9)=="นักบริหาร") $LEVEL_NO = "M2";
						else $LEVEL_NO = "D2";
				}

				if (!$MOV_CODE) $MOV_CODE = "213";
				if (!$EX_CODE) $EX_CODE = "024";
				if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
				if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
				if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
				if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
				$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		
				if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
				if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
				if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";

				// mai edit 29 09 54
				if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
				if (!$PER_SALARY_CURRENT_UPDATE) $PER_SALARY_CURRENT_UPDATE = "N";

				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					// update and insert into PER_SALARYHIS 
					$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$PER_ID 
									order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc, SAH_DOCNO desc ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$tmp_SAH_ID = trim($data1[SAH_ID]);
					$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
					$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
									where SAH_ID=$tmp_SAH_ID";
					
					$db_dpis1->send_cmd($cmd);	
					//$db_dpis1->show_error();		

					if ( $SAH_LAST_SALARY == "Y" ) { // ตรวจสอบว่าถ้ารายการที่นำเข้าเป็นรายการล่าสุดให้แก้ไขรายการที่แล้วเป็น SAH_LAST_SALARY ='N'
						$cmd = " update PER_SALARYHIS set SAH_LAST_SALARY='N' 		
										where PER_ID=$PER_ID";
						$db_dpis1->send_cmd($cmd);	
						//$db_dpis1->show_error();	
					}
					//echo "<br>";
					echo "</br>ข้อมูล $NAME";
					echo "</br>ให้เป็นประวัติเงินเดือนล่าสุด : ".$SAH_LAST_SALARY;
					echo "</br>วันที่สิ้นสุด : ".$SAH_ENDDATE;
					echo "</br>ให้ปรับปรุงเงินเดือนปัจจุบัน : ".$PER_SALARY_CURRENT_UPDATE;
					echo "</br>XLS เงินเดือนก่อนเลื่อน : ".$PER_SALARY;
					echo "</br>XLS เงินเดือนหลังเลื่อน : ".$SAH_SALARY;
					$cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
									SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
									SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
									LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
									SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_OLD_SALARY)
									values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', 
									'$SAH_DOCDATE', '$SAH_ENDDATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', 
									$SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', 
									'$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_POS_NO', 
									$SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, 
									'$SAH_LAST_SALARY', '$SM_CODE', $SAH_CMD_SEQ, $PER_SALARY) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
					if ($PER_SALARY_CURRENT_UPDATE=='Y' ){
						$cmd = " update PER_PERSONAL set PER_SALARY = $SAH_SALARY , MOV_CODE = '$MOV_CODE' ,
										PER_DOCNO = '$SAH_DOCNO' , PER_DOCDATE = '$SAH_DOCDATE' where PER_ID = $PER_ID ";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
					}

					$SAH_ID++;
				} else echo "$PER_CARDNO $NAME<br>";
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}
		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
		$SAH_DOCDATE = show_date_format($SAH_DOCDATE, 1);
		$SAH_ENDDATE = show_date_format($SAH_ENDDATE, 1);
	} // end if
?>