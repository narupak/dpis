<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command == "ADD" && trim($CP_CODE) && trim($QS_NAME)){
		$cmd = " 	SELECT  CP_CODE, CL_NO, QS_NAME FROM PER_QUESTION_STOCK 
							WHERE CP_CODE='$CP_CODE' AND CL_NO=$CL_NO AND QS_NAME='$QS_NAME' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
	//	$db_dpis->show_error(); echo "<hr>";
		if($count_duplicate <= 0){
			//หา QS_ID
			$cmd = " select max(QS_ID) as max_id from PER_QUESTION_STOCK  ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$QS_ID = $data[max_id] + 1;

			$QS_SCORE1 = trim($QS_SCORE1)? $QS_SCORE1 : "NULL";
			$QS_SCORE2 = trim($QS_SCORE2)? $QS_SCORE2 : "NULL";
			$QS_SCORE3 = trim($QS_SCORE3)? $QS_SCORE3 : "NULL";
			$QS_SCORE4 = trim($QS_SCORE4)? $QS_SCORE4 : "NULL";

//			$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE, CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
//							  QS_SCORE3, QS_SCORE4, QS_SCORE5, QS_SCORE6, UPDATE_USER, UPDATE_DATE) 
//							  VALUES ($QS_ID, '$CP_CODE', $CL_NO, '$QS_NAME', $QS_SCORE1, $QS_SCORE2, $QS_SCORE3, $QS_SCORE4, 
//							  $QS_SCORE5, $QS_SCORE6, $SESS_USERID, '$UPDATE_DATE') ";
			$cmd = " INSERT INTO PER_QUESTION_STOCK (QS_ID, CP_CODE, CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, 
							  QS_SCORE3, QS_SCORE4, UPDATE_USER, UPDATE_DATE) 
							  VALUES ($QS_ID, '$CP_CODE', $CL_NO, '$QS_NAME', $QS_SCORE1, $QS_SCORE2, $QS_SCORE3, $QS_SCORE4, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//echo "=> $cmd";
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$CP_CODE : $QS_NAME]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [$data[CP_CODE] $data[CL_NO] $data[QS_NAME]]";
		} // endif
	}

	if($command == "UPDATE" && $QS_ID){
		$cmd = " 	UPDATE PER_QUESTION_STOCK SET 
								CP_CODE='$CP_CODE', 
								CL_NO=$CL_NO, 
								QS_NAME='$QS_NAME', 
								QS_SCORE1=$QS_SCORE1, 
								QS_SCORE2=$QS_SCORE2, 
								QS_SCORE3=$QS_SCORE3, 
								QS_SCORE4=$QS_SCORE4, 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
						WHERE QS_ID=$QS_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//								QS_SCORE5=$QS_SCORE5, 
//								QS_SCORE6=$QS_SCORE6, 

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$QS_ID : $CP_CODE : $CL_NO : $QS_NAME]");
	}

	if($command == "DELETE" && $QS_ID){
		$cmd = " delete from PER_QUESTION_STOCK where QS_ID=$QS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$QS_ID : $CP_CODE : $CL_NO : $QS_NAME]");
	}
	
	if($UPD){
		$cmd = "	SELECT 	a.QS_ID, a.CP_CODE, a.CL_NO, QS_NAME, QS_SCORE1, QS_SCORE2, QS_SCORE3, QS_SCORE4, 
							QS_SCORE5, QS_SCORE6, CP_NAME 
							FROM 		PER_QUESTION_STOCK a, PER_COMPETENCE b  
							WHERE 	a.QS_ID=$QS_ID AND a.CP_CODE=b.CP_CODE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$QS_ID = $data[QS_ID];
		$CP_CODE = $data[CP_CODE];
		$CL_NO = $data[CL_NO];
		$QS_NAME = $data[QS_NAME];
		$QS_SCORE1 = $data[QS_SCORE1];
		$QS_SCORE2 = $data[QS_SCORE2];
		$QS_SCORE3 = $data[QS_SCORE3];
		$QS_SCORE4 = $data[QS_SCORE4];
		$QS_SCORE5 = $data[QS_SCORE5];
		$QS_SCORE6 = $data[QS_SCORE6];
		$CP_NAME = $data[CP_NAME];
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$QS_ID = "";
		$CP_CODE = "";
		$CL_NO = "";
		$QS_NAME = "";
		$QS_SCORE1 = "";
		$QS_SCORE2 = "";
		$QS_SCORE3 = "";
		$QS_SCORE4 = "";
		$QS_SCORE5 = "";
		$QS_SCORE6 = "";
		$CP_NAME = "";
	} // end if 
?>