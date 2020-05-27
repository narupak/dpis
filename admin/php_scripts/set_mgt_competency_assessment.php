<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$CA_CYCLE= (isset($CA_CYCLE))?  $CA_CYCLE: 1;
	if($CA_CYCLE==1){	//ครั้งที่ 1
		$table = "PER_MGT_COMPETENCY_ASSESSMENT";	
	}else if($CA_CYCLE==2){	//ครั้งที่ 2
		$table = "PER_VER_COMPETENCY_ASSESSMENT";
	}

	if($command == "SAVE"){
		foreach($ARR_CONSISTENCY as $CA_ID => $CA_CONSISTENCY){
			$CA_CONSISTENCY = $ARR_CONSISTENCY[$CA_ID];
			$CA_SCORE_1 = $ARR_SCORE_1[$CA_ID];
			$CA_SCORE_2 = $ARR_SCORE_2[$CA_ID];
			$CA_SCORE_3 = $ARR_SCORE_3[$CA_ID];
			$CA_SCORE_4 = $ARR_SCORE_4[$CA_ID];
			$CA_SCORE_5 = $ARR_SCORE_5[$CA_ID];
			$CA_SCORE_6 = $ARR_SCORE_6[$CA_ID];
			$CA_SCORE_7 = $ARR_SCORE_7[$CA_ID];
			$CA_SCORE_8 = $ARR_SCORE_8[$CA_ID];
			$CA_SCORE_9 = $ARR_SCORE_9[$CA_ID];
			$CA_SCORE_10 = $ARR_SCORE_10[$CA_ID];
			$CA_SCORE_11 = $ARR_SCORE_11[$CA_ID];
			$CA_SCORE_12 = $ARR_SCORE_12[$CA_ID];
			$CA_MEAN = ($CA_SCORE_1 + $CA_SCORE_2 + $CA_SCORE_3 + $CA_SCORE_4 + $CA_SCORE_5 + $CA_SCORE_6 + $CA_SCORE_7 + 
										$CA_SCORE_8 + $CA_SCORE_9 + $CA_SCORE_10 + $CA_SCORE_11 + $CA_SCORE_12) / 12;

			$cmd = " update $table SET
							CA_CONSISTENCY = $CA_CONSISTENCY, 
							CA_SCORE_1 = $CA_SCORE_1, 
							CA_SCORE_2 = $CA_SCORE_2,
							CA_SCORE_3 = $CA_SCORE_3, 
							CA_SCORE_4 = $CA_SCORE_4, 
							CA_SCORE_5 = $CA_SCORE_5, 
							CA_SCORE_6 = $CA_SCORE_6, 
							CA_SCORE_7 = $CA_SCORE_7, 
							CA_SCORE_8 = $CA_SCORE_8, 
							CA_SCORE_9 = $CA_SCORE_9, 
							CA_SCORE_10 = $CA_SCORE_10, 
							CA_SCORE_11 = $CA_SCORE_11, 
							CA_SCORE_12 = $CA_SCORE_12, 
							CA_MEAN = $CA_MEAN
							 where CA_ID = $CA_ID ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//echo "$cmd <hr>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$CA_ID, $CA_CODE, $FULLNAME, $CA_MEAN]");
		} // loop foreach
	} // end if
?>