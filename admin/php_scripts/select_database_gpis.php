<?	
	error_reporting(1);

	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
	} // end switch case

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_USER = 1;
	$UPDATE_DATE = date("Y-m-d H:i:s");

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

		$table = array(	"per_personal" );
		$where_per_id = " (SELECT PER_ID FROM PER_PERSONAL WHERE DEPARTMENT_ID = $search_department_id) ";
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in $where_per_id ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();
		} // end for

		$cmd = " DELETE FROM PER_POSITION WHERE DEPARTMENT_ID = $search_department_id ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " delete from PER_ORG WHERE DEPARTMENT_ID = $search_department_id AND ORG_CODE IS NULL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

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
				$PT_NAME = trim($fields[5]);
				$LEVEL_NO = trim($fields[6]);
				$PM_NAME = trim($fields[7]);
				$SKILL_NAME = trim($fields[8]);
				$OT_NAME = trim($fields[9]);
				$PV_NAME = trim($fields[10]);
				$POS_STATUS = trim($fields[11]);
				$PN_NAME = trim($fields[12]);
				$PER_NAME = trim($fields[13]);
				$PER_SURNAME = trim($fields[14]);
				$PER_CARDNO = trim($fields[15]);
				$PER_GENDER = trim($fields[16]);
				$PER_BIRTHDATE = save_date(trim($fields[17]));
				$PER_STARTDATE = save_date(trim($fields[18]));
				$PER_SALARY = trim($fields[19]);
				$PER_MGTSALARY = trim($fields[20]);
				$EL_NAME = trim($fields[21]);
				$EN_NAME = trim($fields[22]);
				$EM_NAME = trim($fields[23]);
				$INS_NAME = trim($fields[24]);
				$CT_NAME_EDU = trim($fields[25]); 
				$ST_NAME = trim($fields[26]);
				$MOV_CODE = trim($fields[27]);
				$EFFECTIVEDATE = trim($fields[28]);
				$CL_NAME = trim($fields[29]);
				$FLOWDATE = trim($fields[30]);
				echo "$PER_NAME $PER_SURNAME";
	/*			
				$cmd = " select ORG_ID from PER_ORG where ORG_NAME = '$DEPARTMENT_NAME' and OL_CODE = '02' ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$DEPARTMENT_ID = trim($data[ORG_ID]);
				} else echo "$MINISTRY_NAME $DEPARTMENT_NAME<br>";

				$cmd = " select OT_CODE from PER_ORG_TYPE where OT_NAME = '$OT_NAME' or instr(OT_OTHERNAME, '||$OT_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$OT_CODE = trim($data[OT_CODE]);
				} else echo "$OT_NAME<br>";

				$cmd = " select PV_CODE from PER_PROVINCE where PV_NAME = '$PV_NAME' or instr(PV_OTHERNAME, '||$PV_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PV_CODE = trim($data[PV_CODE]);
				} else echo "$PV_NAME<br>";
				if (!$OT_CODE) $OT_CODE = "01";

				$cmd = " select ORG_ID from PER_ORG 
								  where ORG_NAME = '$ORG_NAME' and OL_CODE = '03' and DEPARTMENT_ID = $DEPARTMENT_ID ";
				$count_data = $db_dpis->send_cmd($cmd);

				if (!$count_data) {			
					$cmd = " INSERT INTO PER_ORG (ORG_ID, ORG_NAME, ORG_SHORT, OL_CODE, OT_CODE, 
								PV_CODE, CT_CODE, ORG_ID_REF, ORG_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
								VALUES ($MAX_ORG_ID, '$ORG_NAME', '$ORG_NAME', '03', '$OT_CODE', 
								'$PV_CODE', '140', $DEPARTMENT_ID, 1, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
					$ORG_ID = $MAX_ORG_ID;
					$MAX_ORG_ID++;
				} else {
					$data = $db_dpis->get_array();
					$ORG_ID = $data[ORG_ID];
				}

				$cmd = " select PL_CODE from PER_LINE where PL_NAME = '$PL_NAME' or instr(PL_OTHERNAME, '||$PL_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PL_CODE = trim($data[PL_CODE]);
				} else echo "$PL_NAME<br>";

				$cmd = " select PT_CODE from PER_TYPE where PT_NAME = '$PT_NAME' or instr(PT_OTHERNAME, '||$PT_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PT_CODE = trim($data[PT_CODE]);
				} else echo "$PT_NAME<br>";

				$cmd = " select PM_CODE from PER_MGT where PM_NAME = '$PM_NAME' or instr(PM_OTHERNAME, '||$PM_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PM_CODE = trim($data[PM_CODE]);
				} else echo "$PM_NAME<br>";

				$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = '$SKILL_NAME' or instr(SKILL_OTHERNAME, '||$SKILL_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$SKILL_CODE = trim($data[SKILL_CODE]);
				} else echo "$SKILL_NAME<br>";

				$cmd = " select PN_CODE from PER_PRENAME where PN_NAME = '$PN_NAME' or instr(PN_OTHERNAME, '||$PN_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$PN_CODE = trim($data[PN_CODE]);
				} else echo "$PN_NAME<br>";

				$cmd = " select EL_CODE from PER_EDUCLEVEL where EL_NAME = '$EL_NAME' or instr(EL_OTHERNAME, '||$EL_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EL_CODE = trim($data[EL_CODE]);
				} else echo "$EL_NAME<br>";

				$cmd = " select EN_CODE from PER_EDUCNAME where EN_NAME = '$EN_NAME' or instr(EN_OTHERNAME, '||$EN_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EN_CODE = trim($data[EN_CODE]);
				} else echo "$EN_NAME<br>";

				$cmd = " select EM_CODE from PER_EDUCMAJOR where EM_NAME = '$EM_NAME' or instr(EM_OTHERNAME, '||$EM_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$EM_CODE = trim($data[EM_CODE]);
				} else echo "$EM_NAME<br>";

				$INS_CODE = $EDU_INSTITUTE = "";
				$cmd = " select INS_CODE from PER_INSTITUTE where INS_NAME = '$INS_NAME' or instr(INS_OTHERNAME, '||$INS_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$INS_CODE = trim($data[INS_CODE]);
				} else $EDU_INSTITUTE = $INS_NAME;

				$cmd = " select CT_CODE from PER_COUNTRY where CT_NAME = '$CT_NAME_EDU' or instr(CT_OTHERNAME, '||$CT_NAME_EDU||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$CT_CODE_EDU = trim($data[CT_CODE]);
				} else echo "$CT_NAME_EDU<br>";

				$cmd = " select ST_CODE from PER_SCHOLARTYPE where ST_NAME = '$ST_NAME' or instr(ST_OTHERNAME, '||$ST_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$ST_CODE = trim($data[ST_CODE]);
				} else echo "$ST_NAME<br>";

				$cmd = " select CL_NAME from PER_CO_LEVEL where CL_NAME = '$CL_NAME' or instr(CL_OTHERNAME, '||$CL_NAME||') > 0 ";
				$count_data = $db_dpis->send_cmd($cmd);
				if ($count_data) {			
					$data = $db_dpis->get_array();
					$CL_NAME = trim($data[CL_NAME]);
				} else echo "$CL_NAME<br>";
				
				if ($PER_GENDER=="ªÒÂ") $PER_GENDER = 1;
				elseif ($PER_GENDER=="Ë­Ô§") $PER_GENDER = 2;
				if ($PER_SALARY=="''") $PER_SALARY = 0;
				if ($PER_MGTSALARY=="''") $PER_MGTSALARY = 0;
				$POS_STATUS = 1;
				$POS_ID++;
				$cmd = " INSERT INTO PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, PM_CODE, PL_CODE, CL_NAME, POS_SALARY, 
								POS_MGTSALARY, SKILL_CODE, PT_CODE, POS_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, LEVEL_NO)
								VALUES ($POS_ID, $MAX_ORG_ID, '$POS_NO', '$OT_CODE', '$PM_CODE', '$PL_CODE', '$CL_NAME', $PER_SALARY, 
								$PER_MGTSALARY, '$SKILL_CODE', '$PT_CODE', $POS_STATUS, $UPDATE_USER, '$UPDATE_DATE', $DEPARTMENT_ID, '$LEVEL_NO') ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				//echo "$cmd<br>"; */
/*
				if ($PER_NAME != "''") {
					$MOV_CODE = "101";
					if ($PN_CODE=="003") $PER_GENDER = 1;
					else $PER_GENDER = 2;
					$PER_ID++;
					$cmd = " INSERT INTO PER_PERSONAL (PER_ID, PER_TYPE, OT_CODE, PN_CODE, PER_NAME, PER_SURNAME, 
									POS_ID, LEVEL_NO, PER_ORGMGT, PER_SALARY, PER_MGTSALARY, PER_SPSALARY, PER_GENDER, 
									MR_CODE, PER_STARTDATE, MOV_CODE, PER_STATUS, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID)
									VALUES ($PER_ID, 1, '01', '$PN_CODE', $PER_NAME, $PER_SURNAME,	$POS_ID, $LEVEL_NO, 0, 
									$PER_SALARY, 0, 0, $PER_GENDER, '9', '$UPDATE_DATE', '$MOV_CODE', 1, $UPDATE_USER, '$UPDATE_DATE', 
									$DEPARTMENT_ID) ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					//echo "$cmd<br>";
				} */
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