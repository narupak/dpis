<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	//echo("command - $command");
	function list_answer($question_no,$LEVEL_NO){
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$cmd = " SELECT * FROM JEM_ANSWER_INFO WHERE question_no='".$question_no."' ORDER BY id ";
		$db_dpis->send_cmd($cmd);
		unset($ARR_AWNSER_RANGE);
		$all_lv_name = array('O1','O2','O3','O4','K1','K2','K3','K4','K5','M1','M2','SES1','SES2');
		//$selected = "<select name='PASSED_QUESTION_$question_no' id='PASSED_QUESTION_$question_no'  style='width:200px' disabled='disabled'>";
		//$selected = "<select name='PASSED_QUESTION_$question_no' id='PASSED_QUESTION_$question_no'  style='width:200px'>";
		$selected = "<font color='#ffffff'>$LEVEL_NO</font><input id='LEVEL_NO_SHOW' name='LEVEL_NO_SHOW' type='text'";
		$x=1;
		while($data = $db_dpis->get_array()){
			$ARR_AWNSER_RANGE []=$data[score];
			//$selected = $selected."<option value='$data[score]'";
			if($all_lv_name[array_search($LEVEL_NO, $all_lv_name)+1]==$data[score]){
				$selected = $selected." value='$data[answer_info]' style='width:200px;' readonly />";
				$selected = $selected."<input name='PASSED_QUESTION_$question_no' id='PASSED_QUESTION_$question_no' type='hidden' value='$x' />";
				}//if($all_lv_name[array_search($LEVEL_NO, $all_lv_name)+1]==$data[score]){
			$x++;
			} //while($data = $db_dpis->get_array()){
		//print_r($ARR_AWNSER_RANGE);
		$AWNSER_RANGE = "<input id='AWNSER_RANGE_$question_no' name='AWNSER_RANGE_$question_no' type='hidden' value='".implode(",", $ARR_AWNSER_RANGE)."' />";
		//$AWNSER_RANGE = $AWNSER_RANGE."<input name='' type='button' onclick='alert(document.form1.AWNSER_RANGE_$question_no.value)' />";
		//$AWNSER_RANGE = $AWNSER_RANGE."<input name='' type='button' onclick='alert(".count($ARR_AWNSER_RANGE).")' />";
		if($question_no==1){
			$selected = $selected."</select>".$AWNSER_RANGE;
			}else{
			$selected = $AWNSER_RANGE;
			}
		return $selected;
		//return "PASSED_QUESTION_$question_no";
		}//function list_answer($question_no){
	if($command==""){$command="open";}
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select ID, NAME, TOPIC, DESCRIPTION from JEM_QUESTION_INFO order by sequence ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_EVALUATE_QUESTION[] = $data[ID];
		$ARR_QUESTION_NAME[$data[ID]] = trim($data[NAME]);
		$ARR_QUESTION_TOPIC[$data[ID]] = $data[TOPIC];
		$ARR_QUESTION_DESC[$data[ID]] = $data[DESCRIPTION];
		} //while($data = $db_dpis->get_array()){

	$cmd = " select a.POS_NO, a.POS_STATUS, b.PL_NAME, c.LEVEL_NAME from PER_POSITION a, PER_LINE b, PER_LEVEL c
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
	//echo("unfinish_evaluate - $unfinish_evaluate");
	if( $unfinish_evaluate && $PAGE_ID==0 ){
		$data = $db_dpis->get_array();
		$JOB_EVA_ID = $data[JOB_EVA_ID];

		unset($ARR_PASSED_QUESTION);
		$cmd = " select QUESTION_ID from JOB_EVALUATION_ANSWER_HISTORY where JOB_EVA_ID=$JOB_EVA_ID ";
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){ 
			$ARR_PASSED_QUESTION[] = $data[QUESTION_ID];
			} //while($data = $db_dpis->get_array()){ 
		$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
		session_register("SESS_PASSED_QUESTION");
		
		$CONTINUE = 1;
		$CONTINUE_PAGE = count($ARR_PASSED_QUESTION) + 1;
		if($CONTINUE_PAGE > 20){
			$CONTINUE_PAGE = 20;
			}
		} //if( $unfinish_evaluate && $PAGE_ID==0 ){

	if($PAGE_ID >= $CONTINUE_PAGE) $CONTINUE = 0;

	if($command == "SAVE"){
		$NEXT_PAGE_ID = $PAGE_ID;
		$PAGE_ID = $PAGE_ID - 1;

		if(!$PAGE_ID){
			$cmd = " insert into JOB_EVALUATION
								(POS_ID, ISPASSED, TESTER_ID, TEST_TIME, STATUS)
							 values
							 	($POS_ID, 'N', $SESS_USERID, '$UPDATE_DATE', '4')
						  ";
			$db_dpis->send_cmd($cmd);
			$db_dpis->show_error();

			$cmd = " select JOB_EVA_ID from JOB_EVALUATION where POS_ID=$POS_ID and TESTER_ID=$SESS_USERID and TEST_TIME='$UPDATE_DATE' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$JOB_EVA_ID = $data[JOB_EVA_ID];

			/*$cmd = " insert into JOB_ANALYSIS
								(JOB_EVA_ID, PIORITY, WORKLOAD, KNOWLEDGE, SKILL, EXP, COMPETENCY,
								ACCOUNTABILITY1, ACCOUNTABILITY2, ACCOUNTABILITY3, ACCOUNTABILITY4,
								ACCOUNTABILITY5, ACCOUNTABILITY6, ACCOUNTABILITY7, ACCOUNTABILITY8, CREATE_DATE)
							 values
							 	($JOB_EVA_ID, '$PIORITY', '$WORKLOAD', '$KNOWLEDGE', '$SKILL', '$EXP', '$COMPETENCY',
								'$ACCOUNTABILITY1', '$ACCOUNTABILITY2', '$ACCOUNTABILITY3', '$ACCOUNTABILITY4',
								'$ACCOUNTABILITY5', '$ACCOUNTABILITY6', '$ACCOUNTABILITY7', '$ACCOUNTABILITY8', '$UPDATE_DATE')
						  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			*/
			$command="continute";
		}
	} // if($command == "SAVE"){
if($VIEW || $CONTINUE){//if(!$VIEW && !$CONTINUE){
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
		}
	} //}elseif($VIEW || $CONTINUE){
if($command=="finish"){
	$error_range_all=0;
	//echo("เสร็จ");
	//print_r($ARR_QUESTION_NAME);
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
//	print "ARR_PROFILE_DATA :: <br><pre>"; print_r($ARR_PROFILE_DATA); print "</pre><br>";
	for($i=0;$i<count($QUESTION_ID);$i++){
		$AWNSER_RANGE_CHK = explode(",", trim(${"AWNSER_RANGE_".($QUESTION_ID[$i])}));
//		$ARR_SELECTED_ANSWER[$QUESTION_ID[$i]] = trim(${"PASSED_QUESTION_".($QUESTION_ID[$i])}) ;
		$ARR_SELECTED_ANSWER[$QUESTION_ID[$i]] = $AWNSER_RANGE_CHK[trim(${"PASSED_QUESTION_".($QUESTION_ID[$i])})-1] ;
		//if (!in_array(trim(${"PASSED_QUESTION_".($QUESTION_ID[$i])}), $AWNSER_RANGE_CHK)) {
		if (trim(${"PASSED_QUESTION_".($QUESTION_ID[$i])}>count($AWNSER_RANGE_CHK))) {
			$error_range_all=$error_range_all+1;
			${"ERROR_".($QUESTION_ID[$i])}="*";
			}
		//echo($QUESTION_ID[$i]." - ".${"PASSED_QUESTION_".($i+1)}."<BR>");
		}//for($i=0;$i<count($QUESTION_ID);$i++){
	// ========================== Check LEVEL_NO ===============================			
	$cmd="SELECT POS_ID,POS_NO FROM PER_POSITION WHERE DEPARTMENT_ID = '$DEPARTMENT_ID' ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$ARR_DB_POS_NO[] = $data[POS_NO];
		$ARR_DB_POS_ID[] = $data[POS_ID];
		} //while($data = $db_dpis->get_array()){
	$cmd="SELECT POS_NO FROM PER_POSITION WHERE PL_CODE = '$PL_CODE' AND LEVEL_NO = '$LEVEL_NO' AND DEPARTMENT_ID = '$DEPARTMENT_ID' ";
	$db_dpis->send_cmd($cmd);	//$db_dpis->show_error();
	while($data = $db_dpis->get_array()){
		$ARR_DB_POS_NO_2[] = $data[POS_NO];
		} //while($data = $db_dpis->get_array()){
	$ARR_POS_NO_LIST = explode(",", $POS_NO_LIST);
	$ARR_COMPARE_DEP = array_diff($ARR_POS_NO_LIST, $ARR_DB_POS_NO);//ตรวจว่ามีเลขที่ตำแหน่งในกรมหรือไม่
	$ARR_COMPARE_LV = array_diff($ARR_POS_NO_LIST, $ARR_DB_POS_NO_2);//ตรวจว่ามีเลขที่ตำแหน่งในกรมหรือไม่
	$array_intersect = array_intersect($ARR_DB_POS_NO, $ARR_POS_NO_LIST);//เก็บคีย์ที่ใช้ในการค้นหา POS_ID

	if(count($ARR_COMPARE_DEP)>0){
		$ERROR_POS_ID="* มีเลขที่ตำแหน่งที่ไม่ได้อยู่ในกรม "."(".implode(",", $ARR_COMPARE_DEP).")<BR>";
		}	
	if(count($ARR_COMPARE_LV)>0){
		$ERROR_POS_ID.="* มีเลขที่ตำแหน่งที่ไม่ได้อยู่ในระดับตำแหน่ง $LEVEL_NO "."(".implode(",", $ARR_COMPARE_LV).")";
		}
		// ========================== Check LEVEL_NO ===============================		
	if($error_range_all==0&&count($ARR_COMPARE_DEP)==0&&count($ARR_COMPARE_LV)==0){
		unset($POS_ID_LIST);
		while (current($array_intersect)) {
			$POS_ID_LIST[] = $ARR_DB_POS_ID[key($array_intersect)];
			next($array_intersect);
			}
		foreach($ARR_SELECTED_ANSWER as $QUESTION_ID => $ANSWER_SCORE){
			unset($ARR_MAP_CODE);
			if($QUESTION_ID == 6){
				// ต้องได้ 2 ค่า คือ MB1, MB2
				$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID]."1";
				$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID]."2";
				}else{//if($QUESTION_ID == 6){
				$ARR_MAP_CODE[] = $ARR_QUESTION_NAME[$QUESTION_ID];
				} //}else{//if($QUESTION_ID == 6){
				foreach($ARR_MAP_CODE as $MAP_CODE){
					$ARR_RAW_GRADE[$MAP_CODE] = $ARR_MAP_LEVEL[$MAP_CODE][$ANSWER_SCORE];
					} //foreach($ARR_MAP_CODE as $MAP_CODE){
				} //foreach($ARR_SELECTED_ANSWER as $QUESTION_ID => $ANSWER_SCORE){
		//	print_r($ARR_RAW_GRADE);
		// ========================== Prepare Data ===============================		
		// ========================== Start Process ===============================		
				$ISPASSED = "Y";
				
				// *********** find KH **************			
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);
				$ARR_INDEX[0] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[2]];
				$ARR_INDEX[1] = $ARR_RAW_GRADE[$ARR_QUESTION_NAME[3]];
				//print_r($ARR_RAW_GRADE);
				//echo("<BR>".$ARR_QUESTION_NAME[2]);
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
	
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);$ISPASSED = "Y";
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
		
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);$ISPASSED = "Y";
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
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);$ISPASSED = "Y";
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
		
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);$ISPASSED = "Y";
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

				//echo "PS = $PS | PS_SCORE = $PS_SCORE | PS_KH_SCORE = $PS_KH_SCORE | PS_KH_STEPCOUNT = $PS_KH_STEPCOUNT<br>";
				// *********** find PS and PS_KH **************

				// *********** find ACC **************
				unset($ARR_INDEX, $ARR_CONSISTENCY_CHECK);$ISPASSED = "Y";
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
//				print "ARR_CALCULATED_GRADE :: <br><pre>"; print_r($ARR_CALCULATED_GRADE); print "</pre><br>";		
//				print "ANSWERED_GRADE :: <br><pre>"; print_r($ANSWERED_GRADE); print "</pre><br>";		
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
				$SAVE_SCORE .= $LEVEL_NO;
				
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
				//$POS_ID_LIST =  implode(",", $POS_ID_LIST);
				for($i=0;$i<count($POS_ID_LIST);$i++){
					$cmd = " 	insert into JOB_EVALUATION  
									(POS_ID, SCORE, ISPASSED, TESTER_ID, TEST_TIME, STATUS, CONSISTENCY)
									values
									('".$POS_ID_LIST[$i]."','$SAVE_SCORE','$ISPASSED','$SESS_USERID', '$UPDATE_DATE','$SAVE_STATUS','$SAVE_CONSISTENCY')
								  ";
					$db_dpis->send_cmd($cmd);
					//$db_dpis->show_error();
					}//for($i=0;$i<count($POS_ID_LIS);$i++){
				//$cmd = " select JOB_EVA_ID from JOB_EVALUATION where POS_ID = '".$POS_ID_LIST[0]."' and TESTER_ID = '$SESS_USERID' and TEST_TIME = '$UPDATE_DATE' ";
				unset($JOB_EVA_ID);
				$cmd = " select JOB_EVA_ID from JOB_EVALUATION where TESTER_ID = '$SESS_USERID' and TEST_TIME = '$UPDATE_DATE' ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				while ($data = $db_dpis->get_array()) {
					$JOB_EVA_ID[] = $data[JOB_EVA_ID];
					}
				$JOB_EVA_ID = implode(",", $JOB_EVA_ID);
				//echo($JOB_EVA_ID);
?>
<script type="text/javascript">
	//alert('<?=$JOB_EVA_ID?>');
	window.location="job_approve_2.html?MENU_ID_LV0=<?=$MENU_ID_LV0?>&MENU_ID_LV1=<?=$MENU_ID_LV1?>&MENU_ID_LV2=<?=$MENU_ID_LV2?>&MENU_ID_LV3=<?=$MENU_ID_LV3?>&JOB_EVA_ID=<?=$JOB_EVA_ID?>";
	//setActiveStyleSheet(document.getElementById("defaultTheme"), "winter");
</script>
<?
		// ========================== Save Value  ================================
		}//if($error_range_all==0&&count($ARR_COMPARE_DEP)==0&&count($ARR_COMPARE_LV)==0){
	}//if($command=="finish"){
?>