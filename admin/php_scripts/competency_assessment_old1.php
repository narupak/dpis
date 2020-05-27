<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if ($CF_TYPE==1) $CF_TYPE_NAME = "ประเมินตนเอง";
	elseif ($CF_TYPE==2) $CF_TYPE_NAME = "ประเมินผู้บังคับบัญชา";
	elseif ($CF_TYPE==3) $CF_TYPE_NAME = "ประเมินเพื่อนร่วมงาน";
	elseif ($CF_TYPE==4) $CF_TYPE_NAME = "ประเมินผู้ใต้บังคับบัญชา";

	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where	KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = substr($data[KF_START_DATE], 0, 10);
		if($KF_START_DATE_1){
			$arr_temp = explode("-", $KF_START_DATE_1);
			$KF_START_DATE_1 = $arr_temp[2] ."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		} // end if
		$KF_END_DATE_1 = substr($data[KF_END_DATE], 0, 10);
		$KF_YEAR = substr($KF_END_DATE_1, 0, 4) + 543;
		if($KF_END_DATE_1){
			$arr_temp = explode("-", $KF_END_DATE_1);
			$KF_END_DATE_1 = $arr_temp[2] ."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		}
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = substr($data[KF_START_DATE], 0, 10);
		if($KF_START_DATE_2){
			$arr_temp = explode("-", $KF_START_DATE_2);
			$KF_START_DATE_2 = $arr_temp[2] ."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		} // end if
		$KF_END_DATE_2 = substr($data[KF_END_DATE], 0, 10);
		$KF_YEAR = substr($KF_END_DATE_2, 0, 4) + 543;
		if($KF_END_DATE_2){
			$arr_temp = explode("-", $KF_END_DATE_2);
			$KF_END_DATE_2 = $arr_temp[2] ."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		}
	}

	$PER_ID = $data[PER_ID];
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$PER_SALARY = trim($data[PER_SALARY]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
		
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
		
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);

	if($PER_TYPE==1){
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = trim($data[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NAME) . (($PT_CODE != "11" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".level_no_format($LEVEL_NAME);
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$cmd = " select QS_ID, a.CP_CODE, CL_NO, QS_NAME, b.PC_TARGET_LEVEL 
					  from PER_QUESTION_STOCK a, PER_POSITION_COMPETENCE b
					  where	a.CP_CODE = b.CP_CODE and POS_ID=$POS_ID
					  order by CP_CODE,CL_NO ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_EVALUATE_QUESTION[] = $data[QS_ID];
		$ARR_QUESTION_NAME[$data[QS_ID]] = trim($data[CP_CODE]);
		$ARR_QUESTION_TOPIC[$data[QS_ID]] = $data[CL_NO];
		$ARR_QUESTION_DESC[$data[QS_ID]] = $data[QS_NAME];
		$ARR_QUESTION_LEVEL[$data[QS_ID]] = $data[PC_TARGET_LEVEL];
		$GET_CP_CODE = trim($data[CP_CODE]);
		$GET_CL_NO = trim($data[CL_NO]);
		$GET_QS_ID = trim($data[QS_ID]);
		$ARR_TMP_CP_CODE[] = $GET_CP_CODE;
		$ARR_QUESTION[$GET_CP_CODE][$GET_CL_NO][ID] = $GET_QS_ID;
		$ARR_QUESTION[$GET_CP_CODE][$GET_CL_NO][NAME] = $data[QS_NAME];		
	} // end while
	$ARR_CP_CODE = array_unique($ARR_TMP_CP_CODE);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$PAGE_ID) $PAGE_ID = 0;
	
	$cmd = " select CF_ID from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_STATUS=4 and CF_SCORE IS NULL ";
	$unfinish_evaluate = $db_dpis->send_cmd($cmd);
	if( $unfinish_evaluate && $PAGE_ID==0 ){
		$data = $db_dpis->get_array();
		$CF_ID = $data[CF_ID];
		
		unset($ARR_PASSED_QUESTION);
		$cmd = " select QS_ID from PER_COMPETENCY_ANSWER where CF_ID=$CF_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){ 
			$ARR_PASSED_QUESTION[] = $data[QS_ID];
		} // loop while
		$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
		session_register("SESS_PASSED_QUESTION");
		
		$CONTINUE = 1;
		$CONTINUE_PAGE = count($ARR_PASSED_QUESTION) + 1;
		if($CONTINUE_PAGE > 20) $CONTINUE_PAGE = 20;
	} // end if

	if($PAGE_ID >= $CONTINUE_PAGE) $CONTINUE = 0;

	if($command == "SAVE"){
		$NEXT_PAGE_ID = $PAGE_ID;
		$PAGE_ID = $PAGE_ID - 1;

		if(!$PAGE_ID){
			$cmd = " SELECT max(CF_ID) as MAX_ID FROM PER_COMPETENCY_FORM ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;

			$cmd = " insert into PER_COMPETENCY_FORM (CF_ID, KF_ID, CF_TYPE, CF_STATUS, UPDATE_USER, UPDATE_DATE)
							 values ($MAX_ID, $KF_ID, 1, 4, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
			$cmd = " select CF_ID from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_TYPE = 1 and 
							UPDATE_USER=$SESS_USERID and UPDATE_DATE='$UPDATE_DATE' ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$CF_ID = $data[CF_ID];

		}else{
			//for ( $i=0; $i<115; $i++ ) $ARR_ANSWER_SCORE[$i] = 1;
			//$ANSWER_SCORE = $ARR_ANSWER_SCORE[$ANSWER];
			foreach($ANSWER as $ANSWER_ID => $ANSWER_SCORE) 
				$cmd = " insert into PER_COMPETENCY_ANSWER (CF_ID, QS_ID, CA_ANSWER, CA_DESCRIPTION, UPDATE_USER, 
							  UPDATE_DATE)
							  values	($CF_ID, $QS_ID, $ANSWER_ID, '$DESCRIPTION', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			// skip question
			if(!$ANSWER_SCORE) {
				$CUR_PAGE_ID = $NEXT_PAGE_ID - 1;
				$NEXT_PAGE_ID = (ceil($CUR_PAGE_ID/5)*5)+1;
				//echo $NEXT_PAGE_ID . " <== SKIP NEXT <br>";
			} 
			unset($ANSWER, $DESCRIPTION);
			unset($ANSWER_ID, $ANSWER_SCORE);
		} // end if

		$PAGE_ID = $NEXT_PAGE_ID;
	} // end if

	if(!$VIEW && !$CONTINUE){

		if(!$PAGE_ID){
			$SESS_PASSED_QUESTION = "";
			session_register("SESS_PASSED_QUESTION");
		}elseif($PAGE_ID <= count($ARR_EVALUATE_QUESTION)){
			if(trim($SESS_PASSED_QUESTION)){ 
				unset($ARR_PASSED_QUESTION);
				$ARR_PASSED_QUESTION = explode(",", $SESS_PASSED_QUESTION);
			} // end if
	
			if( in_array($PAGE_ID,array(1, 2, 20)) ){
				$QS_ID = $ARR_EVALUATE_QUESTION[($PAGE_ID - 1)];
			}else{
				// Random Question No. 3 - 19
				$rand_question = mt_rand(3, 19);
				while( in_array($rand_question, $ARR_PASSED_QUESTION) ){ 
					$rand_question = mt_rand(3, 19);
				} // loop while
				$QS_ID = $rand_question;
			} // end if		
	
			$ARR_PASSED_QUESTION[] = $QS_ID;
			$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
			session_register("SESS_PASSED_QUESTION");
			
	//		echo "$SESS_PASSED_QUESTION<br>";
			$DESCRIPTION = "";
			$ARR_EVALUATE_ANSWER[] = 1;
			$ARR_ANSWER_INFO[1] = "ไม่แสดงสมรรถนะนี้";
			$ARR_ANSWER_SCORE[1] = 0;
			$ARR_EVALUATE_ANSWER[] = 2;
			$ARR_ANSWER_INFO[2] = "แสดงสมรรถนะนี้ไม่บ่อย (25-50% ของการทำงาน)";
			$ARR_ANSWER_SCORE[2] = 0;
			$ARR_EVALUATE_ANSWER[] = 3;
			$ARR_ANSWER_INFO[3] = "แสดงสมรรถนะนี้ในเกือบทุกสถานการณ์ (51-75% ของการทำงาน)";
			$ARR_ANSWER_SCORE[3] = 1;
			$ARR_EVALUATE_ANSWER[] = 4;
			$ARR_ANSWER_INFO[4] = "แสดงสมรรถนะนี้อย่างสม่ำเสมอ";
			$ARR_ANSWER_SCORE[4] = 1; 
		}elseif($PAGE_ID > count($ARR_EVALUATE_QUESTION)){
			unset($QS_ID);

			// ========================== Prepare Data ===============================		
			$cmd = " select 	QS_ID, CA_ANSWER
							 from 		PER_COMPETENCY_ANSWER
							 where	CF_ID=$CF_ID
							 order by QS_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_SELECTED_ANSWER[$data[QS_ID]] = trim($data[ANSWER]);
				}
			//print "ARR_SELECTED_ANSWER :: <br><pre>"; print_r($ARR_SELECTED_ANSWER); print "</pre><br>";

			foreach($ARR_SELECTED_ANSWER as $QS_ID => $ANSWER_SCORE){
				unset($ARR_MAP_CODE);
				$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QS_ID];
				
				foreach($ARR_MAP_CODE as $MAP_CODE){
					$ARR_RAW_GRADE[$MAP_CODE] = $ARR_MAP_LEVEL[$MAP_CODE][$ANSWER_SCORE];
				} // inner loop foreach
			} // loop foreach	
	//		print "<pre>"; print_r($ARR_RAW_GRADE); print "</pre><br>";
	print_r($ARR_SELECTED_ANSWER);
			// ========================== Prepare Data ===============================		
	
			// ========================== Start Process ===============================		
			$ISPASSED = "Y";
			
			$TOTAL_POINTS = $KH_SCORE + $PS_KH_SCORE + $ACC_SCORE;
			
			$ANSWERED_GRADE = $ARR_SELECTED_ANSWER[1];
			// ========================== End Process  ===============================
			
			// ========================== Save Value  ================================
			$SAVE_SCORE = "";
			
			$SAVE_CONSISTENCY = "";

			$SAVE_STATUS = 1;
			
			$cmd = " update PER_COMPETENCY_FORM set
								ISPASSED='$ISPASSED',
								SCORE='$SAVE_SCORE',
								CONSISTENCY='$SAVE_CONSISTENCY',
								STATUS='$SAVE_STATUS'
							 where CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();		
/*   Update ผลการประเมินสมรรถนะ 
			$cmd = " select CP_CODE from PER_KPI_COMPETENCE where KF_ID=$KF_ID and trim(CP_CODE)='$CP_CODE' and KC_ID<>$KC_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				$cmd = " UPDATE PER_KPI_COMPETENCE SET
									CP_CODE='$CP_CODE',
									PC_TARGET_LEVEL=$PC_TARGET_LEVEL,
									UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								WHERE KF_ID=$KF_ID and KC_ID=$KC_ID";
				$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
		
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขคุณลักษณะ/สมรรถนะที่คาดหวัง [$KC_ID : $CP_CODE : $CP_NAME]");
*/
			// ========================== Save Value  ================================

			$SESS_PASSED_QUESTION = "";
			session_register("SESS_PASSED_QUESTION");
		} // end if PAGE_ID

	}elseif($VIEW || $CONTINUE){

		if(!$PAGE_ID){
			$cmd = " select		PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
											ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
											ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8
							 from		JOB_ANALYSIS
							 where	CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PIORITY = trim($data[PIORITY]);
			$WORKLOAD = trim($data[WORKLOAD]);
			$KNOWLEDGE = trim($data[KNOWLEDGE]);
			$SKILL = trim($data[SKILL]);
			$EXP = trim($data[EXP]);
			$COMPETENCY = trim($data[COMPETENCY]);
			$ACCOUNTABILITY1 = trim($data[ACCOUNTABILITY1]);
			$ACCOUNTABILITY2 = trim($data[ACCOUNTABILITY2]);
			$ACCOUNTABILITY3 = trim($data[ACCOUNTABILITY3]);
			$ACCOUNTABILITY4 = trim($data[ACCOUNTABILITY4]);
			$ACCOUNTABILITY5 = trim($data[ACCOUNTABILITY5]);
			$ACCOUNTABILITY6 = trim($data[ACCOUNTABILITY6]);
			$ACCOUNTABILITY7 = trim($data[ACCOUNTABILITY7]);
			$ACCOUNTABILITY8 = trim($data[ACCOUNTABILITY8]);
		}elseif($PAGE_ID <= count($ARR_EVALUATE_QUESTION)){
			$cmd = " select		QS_ID, ANSWER_ID, CA_ANSWER, CA_DESCRIPTION
							 from		PER_COMPETENCY_ANSWER
							 where		CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			while($data = $db_dpis->get_array()){			
				$ARR_VIEW_QUESTION[] = $data[QS_ID];
				$ARR_VIEW_ANSWER[$data[QS_ID]] = $data[ANSWER_ID];
				$ARR_VIEW_ANSWER_DESCRIPTION[$data[QS_ID]] = $data[CA_DESCRIPTION];
			} // loop while

			$QS_ID = $ARR_VIEW_QUESTION[($PAGE_ID - 1)];
			$ANSWER = $ARR_VIEW_ANSWER[$QS_ID];
			$DESCRIPTION = $ARR_VIEW_ANSWER_DESCRIPTION[$QS_ID];
			
//			echo $PAGE_ID ." : ". $CONTINUE_PAGE ." : ". $QS_ID ." : ". $ANSWER ." : ". $DESCRIPTION;

			$ARR_EVALUATE_ANSWER[] = 1;
			$ARR_ANSWER_INFO[1] = "ไม่แสดงสมรรถนะนี้";
			$ARR_ANSWER_SCORE[1] = 0;
			$ARR_EVALUATE_ANSWER[] = 2;
			$ARR_ANSWER_INFO[2] = "แสดงสมรรถนะนี้ไม่บ่อย (25-50% ของการทำงาน)";
			$ARR_ANSWER_SCORE[2] = 0;
			$ARR_EVALUATE_ANSWER[] = 3;
			$ARR_ANSWER_INFO[3] = "แสดงสมรรถนะนี้ในเกือบทุกสถานการณ์ (51-75% ของการทำงาน)";
			$ARR_ANSWER_SCORE[3] = 1;
			$ARR_EVALUATE_ANSWER[] = 4;
			$ARR_ANSWER_INFO[4] = "แสดงสมรรถนะนี้อย่างสม่ำเสมอ";
			$ARR_ANSWER_SCORE[4] = 1; 
		}elseif($PAGE_ID > count($ARR_EVALUATE_QUESTION)){
			$cmd = " select ISPASSED from PER_COMPETENCY_FORM where CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ISPASSED = $data[ISPASSED];
		} // end if PAGE_ID

	} // end if VIEW
?>
