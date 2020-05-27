<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select ID, NAME, TOPIC, DESCRIPTION from JEM_QUESTION_INFO order by ID ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_EVALUATE_QUESTION[] = $data[ID];
		$ARR_QUESTION_NAME[$data[ID]] = trim($data[NAME]);
		$ARR_QUESTION_TOPIC[$data[ID]] = $data[TOPIC];
		$ARR_QUESTION_DESC[$data[ID]] = $data[DESCRIPTION];
	} // end while
	
	$cmd = " select a.POS_NO, a.POS_STATUS, b.PL_NAME, c.LEVEL_NAME 
				    from PER_POSITION a, PER_LINE b, PER_LEVEL c
				  where a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.LEVEL_NO=c.LEVEL_NO ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$POS_NO = $data[POS_NO];
	$PL_NAME = $data[PL_NAME];
	$LEVEL_NAME = $data[LEVEL_NAME];
	$POS_STATUS = $data[POS_STATUS];
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$PAGE_ID) $PAGE_ID = 0;
	
	$cmd = " select JOB_EVA_ID from JOB_EVALUATION where POS_ID=$POS_ID and STATUS='4' and SCORE IS NULL ";
	$unfinish_evaluate = $db_dpis->send_cmd($cmd);
	if( $unfinish_evaluate && $PAGE_ID==0 ){
		$data = $db_dpis->get_array();
		$JOB_EVA_ID = $data[JOB_EVA_ID];
		
		unset($ARR_PASSED_QUESTION);
		$cmd = " select QUESTION_ID from JOB_EVALUATION_ANSWER_HISTORY where JOB_EVA_ID=$JOB_EVA_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){ 
			$ARR_PASSED_QUESTION[] = $data[QUESTION_ID];
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
			$cmd = " insert into JOB_EVALUATION (POS_ID, ISPASSED, TESTER_ID, TEST_TIME, STATUS)
							 values ($POS_ID, 'N', $SESS_USERID, '$UPDATE_DATE', '4') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			$cmd = " select JOB_EVA_ID from JOB_EVALUATION where POS_ID=$POS_ID and TESTER_ID=$SESS_USERID and TEST_TIME='$UPDATE_DATE' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$JOB_EVA_ID = $data[JOB_EVA_ID];

			$cmd = " insert into JOB_ANALYSIS
								(JOB_EVA_ID, PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
								ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
								ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8, CREATE_DATE)
							 values
							 	($JOB_EVA_ID, '$PIORITY', '$WORKLOAD', '$KNOWLEDGE', '$SKILL', '$EXP', '$COMPETENCY',
								'$ACCOUNTABILITY1', '$ACCOUNTABILITY2', '$ACCOUNTABILITY3', '$ACCOUNTABILITY4',
								'$ACCOUNTABILITY5', '$ACCOUNTABILITY6', '$ACCOUNTABILITY7', '$ACCOUNTABILITY8', '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}else{
			if( in_array($QUESTION_ID, array(8,11,12,13,14,16)) ){
				// การดึงคำตอบขึ้นมาโชว์ขึ้นอยู่กับคำตอบของข้อ 2 (AP-K-H)
				$cmd = " select 	ANSWER
								 from 	JOB_EVALUATION_ANSWER_HISTORY
								 where	JOB_EVA_ID=$JOB_EVA_ID and QUESTION_ID=2 ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ANSWER_NO2 = trim($data[ANSWER]);

				$cmd = " select		TYPE_OF_CHOICE
								 from		JEM_ANSWER_INFO
								 where	QUESTION_NO=2 and SCORE='$ANSWER_NO2' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TYPE_OF_CHOICE = $data[TYPE_OF_CHOICE];

				$filter_type_of_choice = "and TYPE_OF_CHOICE".$TYPE_OF_CHOICE."='Y'";
			} // end if

			$cmd = " select 	ID, ANSWER_INFO, SCORE 
							 from 		JEM_ANSWER_INFO 
						  where 	QUESTION_NO=$QUESTION_ID 
											$filter_type_of_choice
							 order by ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()) $ARR_ANSWER_SCORE[$data[ID]] = $data[SCORE];
			$ANSWER_SCORE = $ARR_ANSWER_SCORE[$ANSWER];

			$cmd = " insert into JOB_EVALUATION_ANSWER_HISTORY (JOB_EVA_ID, QUESTION_ID, ANSWER, ANSWER_ID, DESCRIPTION)
							 values ($JOB_EVA_ID, '$QUESTION_ID', '$ANSWER_SCORE', $ANSWER, '$DESCRIPTION') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			unset($ANSWER, $DESCRIPTION);
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
				$QUESTION_ID = $ARR_EVALUATE_QUESTION[($PAGE_ID - 1)];
			}else{
				// Random Question No. 3 - 19
				$rand_question = mt_rand(3, 19);
				while( in_array($rand_question, $ARR_PASSED_QUESTION) ){ 
					$rand_question = mt_rand(3, 19);
				} // loop while
				$QUESTION_ID = $rand_question;
			} // end if		
	
			$ARR_PASSED_QUESTION[] = $QUESTION_ID;
			$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
			session_register("SESS_PASSED_QUESTION");
			
	//		echo "$SESS_PASSED_QUESTION<br>";
			if( in_array($QUESTION_ID, array(8,11,12,13,14,16)) ){
				// การดึงคำตอบขึ้นมาโชว์ขึ้นอยู่กับคำตอบของข้อ 2 (AP-K-H)
				$cmd = " select 	ANSWER
								 from 		JOB_EVALUATION_ANSWER_HISTORY
							  where	JOB_EVA_ID=$JOB_EVA_ID and QUESTION_ID=2 ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ANSWER_NO2 = trim($data[ANSWER]);
				
				$cmd = " select		TYPE_OF_CHOICE
								 from		JEM_ANSWER_INFO
							  where	QUESTION_NO=2 and SCORE='$ANSWER_NO2' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TYPE_OF_CHOICE = $data[TYPE_OF_CHOICE];
				
				$filter_type_of_choice = "and TYPE_OF_CHOICE".$TYPE_OF_CHOICE."='Y'";
			} // end if
			
			$cmd = " select 	ID, ANSWER_INFO, SCORE 
							 from 		JEM_ANSWER_INFO 
						  where 	QUESTION_NO=$QUESTION_ID 
											$filter_type_of_choice
							 order by ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_EVALUATE_ANSWER[] = $data[ID];
				$ARR_ANSWER_INFO[$data[ID]] = $data[ANSWER_INFO];
				$ARR_ANSWER_SCORE[$data[ID]] = $data[SCORE];
			} // end while
			
			$DESCRIPTION = "";
		}elseif($PAGE_ID > count($ARR_EVALUATE_QUESTION)){
			unset($QUESTION_ID);

			// ========================== Prepare Data ===============================		
			$cmd = " select LEVEL_NO from PER_POSITION where POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_LEVEL_NO = trim($data[LEVEL_NO]);
			if($POS_LEVEL_NO == "NOT SPECIFY") $POS_LEVEL_NO = "-";
			
			$cmd = " select MAP_ID, MAP_NAME, MAP_LEVEL, GRADE from JEM_MAPPING order by MAP_NAME, MAP_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_MAP_LEVEL[trim($data[MAP_NAME])][trim($data[MAP_LEVEL])] = trim($data[GRADE]);
				//$ARR_MAP_LEVEL[AP_K_H][1] = A;
				//$ARR_MAP_LEVEL[AP_K_H][2] = A+;
				//$ARR_MAP_LEVEL[AP_K_H][3] = B;
			} // end while
	//		print "ARR_MAP_LEVEL :: <br><pre>"; print_r($ARR_MAP_LEVEL); print "</pre><br>";
	
			$cmd = " select CONSISTENCY_CHECK, ANSWER from JEM_CONSISTENCY_CHECK order by ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_CONSISTENCY_DATA[trim($data[CONSISTENCY_CHECK])] = trim($data[ANSWER]);
				//$ARR_CONSISTENCY_DATA[A] = 1;
				//$ARR_CONSISTENCY_DATA[A+] = 2;
				//$ARR_CONSISTENCY_DATA[B] = 4;
			} // end while
	//		print "ARR_CONSISTENCY_DATA :: <br><pre>"; print_r($ARR_CONSISTENCY_DATA); print "</pre><br>";
	
			$cmd = " select RESULT, SCORE from JEM_PROFILE_CHECK order by ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_PROFILE_DATA[trim($data[RESULT])] = trim($data[SCORE]);
				//$ARR_PROFILE_DATA[P5] = 1;
				//$ARR_PROFILE_DATA[P4] = 2;
				//$ARR_PROFILE_DATA[P3] = 3;
			} // end while
	//		print "ARR_PROFILE_DATA :: <br><pre>"; print_r($ARR_PROFILE_DATA); print "</pre><br>";
	
			$cmd = " select 	QUESTION_ID, ANSWER
							 from 		JOB_EVALUATION_ANSWER_HISTORY
						   where	  JOB_EVA_ID=$JOB_EVA_ID
							 order by QUESTION_ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_SELECTED_ANSWER[$data[QUESTION_ID]] = trim($data[ANSWER]);
				}
			//print "ARR_SELECTED_ANSWER :: <br><pre>"; print_r($ARR_SELECTED_ANSWER); print "</pre><br>";

			foreach($ARR_SELECTED_ANSWER as $QUESTION_ID => $ANSWER_SCORE){
				unset($ARR_MAP_CODE);
				if($QUESTION_ID == 6){
					// ต้องได้ 2 ค่า คือ MB1, MB2
					$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID]."1";
					$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID]."2";
				}else{
					$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID];
				} // end if
				
				foreach($ARR_MAP_CODE as $MAP_CODE){
					$ARR_RAW_GRADE[$MAP_CODE] = $ARR_MAP_LEVEL[$MAP_CODE][$ANSWER_SCORE];
				} // inner loop foreach
			} // loop foreach	
	//		print "<pre>"; print_r($ARR_RAW_GRADE); print "</pre><br>";
	print_r($ARR_SELECTED_ANSWER);
			// ========================== Prepare Data ===============================		
	
			// ========================== Start Process ===============================		
			$ISPASSED = "Y";
			
			// *********** find KH **************			
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[2]];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[3]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["TechnicalKnowHow"] = $min_index;			
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["TechnicalKnowHow"] = $ISPASSED;
	//		print "Technical Know How :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
	
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[($ARR_QUESTION_NAME[6]."1")];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[7]];
			$ARR_INDEX[2] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[8]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[2]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[2]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["ManagementBreath"] = $min_index;
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["ManagementBreath"] = $ISPASSED;
	//		print "Management Breath :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
	
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[9]];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[10]];
			$ARR_INDEX[2] = $ARR_RAW_GRADE[($ARR_QUESTION_NAME[6]."2")];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[2]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[2]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["InterpersonalSkills"] = $min_index;
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["InterpersonalSkills"] = $ISPASSED;
	//		print "Interpersonal Skills :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
			
			$ARR_CONSISTENCY_RESULT["KnowHow"] = $ISPASSED;

			$KH = $ARR_MIN_VALUE["TechnicalKnowHow"];
			$KH .= $ARR_MIN_VALUE["ManagementBreath"];
			$KH .= $ARR_MIN_VALUE["InterpersonalSkills"];
	
			$cmd = " select SCORE from JEM_KH where RESULT='$KH' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$KH_SCORE = trim($data[SCORE]) + 0;
	//		echo "KH = $KH | KH_SCORE = $KH_SCORE<br>";
			// *********** find KH **************
			
			// *********** find PS and PS_KH **************
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[11]];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[12]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["FreedomToThink"] = $min_index;
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["FreedomToThink"] = $ISPASSED;
	//		print "Freedom To Think :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
	
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[13]];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[14]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["ThinkingChallenge"] = $min_index;
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["ThinkingChallenge"] = $ISPASSED;
	//		print "Thinking Challenge :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
	
			$ARR_CONSISTENCY_RESULT["ProblemSolving"] = $ISPASSED;

			$PS = $ARR_MIN_VALUE["FreedomToThink"];
			$PS .= $ARR_MIN_VALUE["ThinkingChallenge"];
	
			$cmd = " select SCORE from JEM_PS where RESULT='$PS' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PS_SCORE = str_replace("%", "", trim($data[SCORE]));
			
			$PS_KH = $KH_SCORE ."(". number_format(($PS_SCORE / 100), 2) .")";
			$cmd = " select SCORE from JEM_PS_KH where PS_KH='$PS_KH' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PS_KH_SCORE = trim($data[SCORE]) + 0;
			
			$cmd = " select STEP_COUNT from JEM_STEP where STEP='$PS_KH_SCORE' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PS_KH_STEPCOUNT = trim($data[STEP_COUNT] + 0);

	//		echo "PS = $PS | PS_SCORE = $PS_SCORE | PS_KH_SCORE = $PS_KH_SCORE | PS_KH_STEPCOUNT = $PS_KH_STEPCOUNT<br>";
			// *********** find PS and PS_KH **************
	
			// *********** find ACC **************
			unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
			$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[15]];
			$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[16]];
			$ARR_INDEX[2] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[17]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[0]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[0]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[1]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[1]];
			$ARR_CONSISTENCY_CHECK[$ARR_INDEX[2]] = $ARR_CONSISTENCY_DATA[$ARR_INDEX[2]];
			asort($ARR_CONSISTENCY_CHECK);
			reset($ARR_CONSISTENCY_CHECK);
			$min_index = key($ARR_CONSISTENCY_CHECK);
			$min_value = current($ARR_CONSISTENCY_CHECK);
			$max_value = end($ARR_CONSISTENCY_CHECK);
			$ARR_MIN_VALUE["FreedomToAct"] = $min_index;
			if(($max_value - $min_value) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["FreedomToAct"] = $ISPASSED;
	//		print "Freedom To Act :: <br><pre>"; print_r($ARR_CONSISTENCY_CHECK); print "</pre><br>";		
	
			$ARR_CONSISTENCY_RESULT["Accountability"] = $ISPASSED;
	
			$ACC = $ARR_MIN_VALUE["FreedomToAct"];
			$ACC .= $ARR_RAW_GRADE[$ARR_QUESTION_NAME[18]];
			$ACC .= $ARR_RAW_GRADE[$ARR_QUESTION_NAME[19]];
	
			$cmd = " select SCORE from JEM_ACC where RESULT='$ACC' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ACC_SCORE = trim($data[SCORE]) + 0;
	
			$cmd = " select STEP_COUNT from JEM_STEP where STEP='$ACC_SCORE' ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ACC_STEPCOUNT = trim($data[STEP_COUNT] + 0);
	//		echo "ACC = $ACC | ACC_SCORE = $ACC_SCORE | ACC_STEPCOUNT = $ACC_STEPCOUNT<br>";
			// *********** find ACC **************
			
			$PROFILE_CALCULATE = "";
			if($ACC_STEPCOUNT > $PS_KH_STEPCOUNT) 			$PROFILE_CALCULATE = "A";			
			elseif($ACC_STEPCOUNT < $PS_KH_STEPCOUNT)		$PROFILE_CALCULATE = "P";
			elseif($ACC_STEPCOUNT == $PS_KH_STEPCOUNT)	$PROFILE_CALCULATE = "L";
			$PROFILE_CALCULATE .= abs($ACC_STEPCOUNT - $PS_KH_STEPCOUNT);
			$PROFILE_CALCULATE_SCORE = $ARR_PROFILE_DATA[$PROFILE_CALCULATE];
			
			$PROFILE_ANSWER = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[20]];
			if($PROFILE_ANSWER=="L") $PROFILE_ANSWER = "L0";
			$PROFILE_ANSWER_SCORE = $ARR_PROFILE_DATA[$PROFILE_ANSWER];
	
			if(abs($PROFILE_CALCULATE_SCORE - $PROFILE_ANSWER_SCORE) > 3) $ISPASSED = "N";
			$ARR_CONSISTENCY_RESULT["ProfileCheck"] = $ISPASSED;
			
			$TOTAL_POINTS = $KH_SCORE + $PS_KH_SCORE + $ACC_SCORE;
			
	//		echo "PROFILE_CALCULATED = $PROFILE_CALCULATED = $PROFILE_CALCULATED_SCORE | PROFILE_ANSWER = $PROFILE_ANSWER = $PROFILE_ANSWER_SCORE<br>";
	//		echo "TOTAL_POINTS = $TOTAL_POINTS | ANSWERED_GRADE = $ANSWERED_GRADE<br>";
	
			$ANSWERED_GRADE = $ARR_SELECTED_ANSWER[1];
			$cmd = " select NAME from JEM_GRADE where MINIMUM <= $TOTAL_POINTS and MAXIMUM >= $TOTAL_POINTS ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			while($data = $db_dpis->get_array()) $ARR_CALCULATED_GRADE[] = trim($data[NAME]);
	//		print "ARR_CALCULATED_GRADE :: <br><pre>"; print_r($ARR_CALCULATED_GRADE); print "</pre><br>";		
			
			if( !in_array($ANSWERED_GRADE, $ARR_CALCULATED_GRADE) ) $ISPASSED = "N";
			// ========================== End Process  ===============================
			
			// ========================== Save Value  ================================
			$SAVE_SCORE = "";
			$SAVE_SCORE .= $ARR_MIN_VALUE["TechnicalKnowHow"].",";
			$SAVE_SCORE .= $ARR_MIN_VALUE["ManagementBreath"].",";
			$SAVE_SCORE .= $ARR_MIN_VALUE["InterpersonalSkills"].",";
			$SAVE_SCORE .= $KH_SCORE.",";
			$SAVE_SCORE .= $ARR_MIN_VALUE["FreedomToThink"].",";
			$SAVE_SCORE .= $ARR_MIN_VALUE["ThinkingChallenge"].",";
			$SAVE_SCORE .= $PS_SCORE."%,";
			$SAVE_SCORE .= $PS_KH_SCORE.",";
			$SAVE_SCORE .= $ARR_MIN_VALUE["FreedomToAct"].",";
			$SAVE_SCORE .= $ARR_RAW_GRADE[$ARR_QUESTION_NAME[18]].",";
			$SAVE_SCORE .= $ARR_RAW_GRADE[$ARR_QUESTION_NAME[19]].",";
			$SAVE_SCORE .= $ACC_SCORE.",";
			$SAVE_SCORE .= $TOTAL_POINTS.",";
			$SAVE_SCORE .= $PROFILE_CALCULATE.",";
			$SAVE_SCORE .= $ANSWERED_GRADE.",";
	//		$SAVE_SCORE .= implode("|", $ARR_CALCULATED_GRADE);
			$SAVE_SCORE .= $POS_LEVEL_NO;
			
			$SAVE_CONSISTENCY = "";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["TechnicalKnowHow"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["ManagementBreath"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["InterpersonalSkills"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["KnowHow"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["FreedomToThink"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["ThinkingChallenge"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["ProblemSolving"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["FreedomToAct"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["Accountability"].",";
			$SAVE_CONSISTENCY .= $ARR_CONSISTENCY_RESULT["ProfileCheck"];

			$SAVE_STATUS = 1;
			
			$cmd = " update JOB_EVALUATION set
								ISPASSED='$ISPASSED',
								SCORE='$SAVE_SCORE',
								CONSISTENCY='$SAVE_CONSISTENCY',
								STATUS='$SAVE_STATUS'
							 where JOB_EVA_ID=$JOB_EVA_ID ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();		
			// ========================== Save Value  ================================
			?>
<script language="javascript">
//alert("sdjhjd");
</script>
			<?			
			// ========================== NEW POSITION =============================
			if($POS_STATUS >=50 && $ISPASSED == "N"){
				$cmd = " delete from PER_POSITION where POS_ID=$POS_ID ";
				$db_dpis->send_cmd($cmd);
			} 
			// ========================== NEW POSITION =============================
	
			$SESS_PASSED_QUESTION = "";
			session_register("SESS_PASSED_QUESTION");
		} // end if PAGE_ID

	}elseif($VIEW || $CONTINUE){

		if(!$PAGE_ID){
			$cmd = " select		PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
											ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
											ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8
							 from		JOB_ANALYSIS
							 where	JOB_EVA_ID=$JOB_EVA_ID ";
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
			$cmd = " select		QUESTION_ID, ANSWER_ID, ANSWER, DESCRIPTION
							 from		JOB_EVALUATION_ANSWER_HISTORY
							 where		JOB_EVA_ID=$JOB_EVA_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			while($data = $db_dpis->get_array()){			
				$ARR_VIEW_QUESTION[] = $data[QUESTION_ID];
				$ARR_VIEW_ANSWER[$data[QUESTION_ID]] = $data[ANSWER_ID];
				$ARR_VIEW_ANSWER_DESCRIPTION[$data[QUESTION_ID]] = $data[DESCRIPTION];
			} // loop while

			$QUESTION_ID = $ARR_VIEW_QUESTION[($PAGE_ID - 1)];
			$ANSWER = $ARR_VIEW_ANSWER[$QUESTION_ID];
			$DESCRIPTION = $ARR_VIEW_ANSWER_DESCRIPTION[$QUESTION_ID];
			
//			echo $PAGE_ID ." : ". $CONTINUE_PAGE ." : ". $QUESTION_ID ." : ". $ANSWER ." : ". $DESCRIPTION;

			if( in_array($QUESTION_ID, array(8,11,12,13,14,16)) ){
				// การดึงคำตอบขึ้นมาโชว์ขึ้นอยู่กับคำตอบของข้อ 2 (AP-K-H)
				$cmd = " select 	ANSWER
								 from 		JOB_EVALUATION_ANSWER_HISTORY
								 where	JOB_EVA_ID=$JOB_EVA_ID and QUESTION_ID=2 ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ANSWER_NO2 = trim($data[ANSWER]);
				
				$cmd = " select		TYPE_OF_CHOICE
								 from		JEM_ANSWER_INFO
								 where	QUESTION_NO=2 and SCORE='$ANSWER_NO2' ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$TYPE_OF_CHOICE = $data[TYPE_OF_CHOICE];
				
				$filter_type_of_choice = "and TYPE_OF_CHOICE".$TYPE_OF_CHOICE."='Y'";
			} // end if

			$cmd = " select 	ID, ANSWER_INFO, SCORE 
							 from 		JEM_ANSWER_INFO 
							 where 	QUESTION_NO=$QUESTION_ID 
											$filter_type_of_choice
							 order by ID ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$ARR_EVALUATE_ANSWER[] = $data[ID];
				$ARR_ANSWER_INFO[$data[ID]] = $data[ANSWER_INFO];
				$ARR_ANSWER_SCORE[$data[ID]] = $data[SCORE];
			} // end while
		}elseif($PAGE_ID > count($ARR_EVALUATE_QUESTION)){
			$cmd = " select ISPASSED from JOB_EVALUATION where JOB_EVA_ID=$JOB_EVA_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ISPASSED = $data[ISPASSED];
		} // end if PAGE_ID

	} // end if VIEW
?>
