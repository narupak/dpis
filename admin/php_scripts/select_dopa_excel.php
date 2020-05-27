<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

//	$RealFile = stripslashes($RealFile);
//	echo "command=".$command."- RealFile=$RealFile --[".is_file($RealFile)."]<br>";
	if ($command=="CONVERT1" || $command=="CONVERT2" || $command=="POSITIONHIS" || $command=="SALARYHIS" || $command=="DECORATEHIS") {
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

	if(($command=="CONVERT1" || $command=="CONVERT2") && $uploadOk){
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
				$PER_OFFNO = trim($fields[1]);
				$LEVEL_NO = trim($fields[2]);
				$TR_NAME = trim($fields[3]);
				if ($command=="CONVERT1") $PER_POS_ORGMGT = "ผ่านหลักสูตร นอ.".trim($fields[4]);
				elseif ($command=="CONVERT2") $PER_POS_ORGMGT = "หัวหน้าฝ่าย / เทียบเท่า";
				
				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					$cmd = " update PER_PERSONAL set PER_POS_ORGMGT = '$PER_POS_ORGMGT' where PER_ID=$PER_ID ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
				} else echo "$PER_CARDNO $PER_OFFNO<br>";
//			} else {
//				$inx = 1;
//				$fields = $row;
//			}
		}

		echo "</pre>";
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=select_database_excel.html?excel_msg=".$excel_msg."\">";	
	} // end if

	if($command=="POSITIONHIS" && $uploadOk){
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
				$POH_PL_NAME = trim($fields[0]);
				$POH_ORG = trim($fields[1]);
				$POH_UNDER_ORG2 = trim($fields[2]);
				$POH_UNDER_ORG1 = trim($fields[3]);
				$POH_ORG3 = trim($fields[4]);
				$POH_ORG2 = trim($fields[5]);
				$POH_ORG1 = trim($fields[6]);
/*				
				if ($POH_ORG1=="กระทรวงมหาดไทย") $ORG_ID_1 = 2899;
				elseif ($POH_ORG1) {
					$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$POH_ORG1' and OL_CODE = '01' ";
					$count_data = $db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ORG_ID_1 = $data[ORG_ID];
				}
				if (!$ORG_ID_1) $ORG_ID_1 = "NULL";

				if ($POH_ORG2=="กรมการปกครอง") $ORG_ID_2 = 2987;
				elseif ($POH_ORG2) {
					$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$POH_ORG2' and OL_CODE = '02' ";
					$count_data = $db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ORG_ID_2 = $data[ORG_ID];
				}
				if (!$ORG_ID_2) $ORG_ID_2 = "NULL";

				if ($POH_ORG3) {
					$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$POH_ORG3' and OL_CODE = '03' and ORG_ID_REF = $ORG_ID_2 ";
					$count_data = $db_dpis->send_cmd($cmd);
					$data = $db_dpis->get_array();
					$ORG_ID_3 = $data[ORG_ID];
				}
				if (!$ORG_ID_3) $ORG_ID_3 = "NULL";
*/
				$cmd = " select POH_ID from PER_POSITIONHIS where POH_ORG = '$POH_ORG' ";
				$count_data = $db_dpis->send_cmd($cmd);
				while ($data = $db_dpis->get_array()) {
					$POH_ID = $data[POH_ID];

//					$cmd = " update PER_POSITIONHIS set POH_ORG1 = '$POH_ORG1', POH_ORG2 = '$POH_ORG2', POH_ORG3 = '$POH_ORG3', 
//										POH_UNDER_ORG1 = '$POH_UNDER_ORG1', POH_UNDER_ORG2 = '$POH_UNDER_ORG2', ORG_ID_1 = $ORG_ID_1, 
//										ORG_ID_2 = $ORG_ID_2, ORG_ID_3 = $ORG_ID_3 where POH_ID=$POH_ID ";
					$cmd = " update PER_POSITIONHIS set POH_ORG1 = '$POH_ORG1', POH_ORG2 = '$POH_ORG2', POH_ORG3 = '$POH_ORG3', 
										POH_UNDER_ORG1 = '$POH_UNDER_ORG1', POH_UNDER_ORG2 = '$POH_UNDER_ORG2' where POH_ID=$POH_ID ";
					$db_dpis1->send_cmd($cmd);
//					if ($POH_ID==248803) {
//					$db_dpis1->show_error();
//					echo "$cmd<br>";
//					}
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

					if ($SAH_LAST_SALARY=="Y") {
						$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
						$db_dpis1->send_cmd($cmd);
					}

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
		
		if(!isset($search_budget_year)){
			if(date("Y-m-d") <= date("Y")."-10-01") $search_budget_year = date("Y") + 543;
			else $search_budget_year = (date("Y") + 543) + 1;
		} // end if
		$DEH_DATE = ($search_budget_year-543) . "-12-05";

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
//				$DEH_DATE = trim($fields[1]);
				$DC_NAME = trim($fields[2]);
				$DEH_GAZETTE = trim($fields[3]);
				$DEH_RECEIVE_DATE = trim($fields[4]);
				$DEH_RECEIVE_DATE = "2010-06-06";
				$DEH_RECEIVE_FLAG = trim($fields[5]);
				if ($DEH_RECEIVE_FLAG=="ยังไม่ได้รับเครื่องราชฯ") $DEH_RECEIVE_FLAG = 0;
				else $DEH_RECEIVE_FLAG = "NULL";
				$DEH_REMARK = trim($fields[6]);
				
				$cmd = " select DC_CODE from PER_DECORATION where DC_NAME = '$DC_NAME' or  DC_SHORTNAME = '$DC_NAME' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$DC_CODE = $data[DC_CODE];
				} 
				else echo "DC_NAME - $DC_NAME<br>";   

				$cmd = " select PER_ID from PER_PERSONAL where PER_CARDNO = '$PER_CARDNO' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PER_ID = $data[PER_ID];

					$cmd1 = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, DEH_GAZETTE, 
									DEH_RECEIVE_FLAG, DEH_RECEIVE_DATE, DEH_REMARK, UPDATE_USER, UPDATE_DATE)
									values ($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', '$DEH_GAZETTE', 
									$DEH_RECEIVE_FLAG, '$DEH_RECEIVE_DATE', '$DEH_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
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