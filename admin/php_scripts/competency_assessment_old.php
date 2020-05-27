<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if ($CF_TYPE==1) $CF_TYPE_NAME = "ประเมินตนเอง";
	elseif ($CF_TYPE==2) $CF_TYPE_NAME = "ประเมินผู้บังคับบัญชา";
	elseif ($CF_TYPE==3) $CF_TYPE_NAME = "ประเมินเพื่อนร่วมงาน";
	elseif ($CF_TYPE==4) $CF_TYPE_NAME = "ประเมินผู้ใต้บังคับบัญชา";

	$cmd = " select	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where	KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$chkKFStartDate=$data[KF_START_DATE];
	$chkKFEndDate=$data[KF_END_DATE];
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

// อ่านข้อมูลผู้ประเมิน
	$PER_ID = $data[PER_ID];
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
					 from	PER_PERSONAL
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

	if($PER_TYPE==1){ // ข้าราชการ
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
	}elseif($PER_TYPE==2){ // ลูกจ้างประจำ
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){ // พนักงานราชการ
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 	 PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
					   where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

// อ่านข้อมูลผู้ถูกประเมิน
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
					 from	PER_PERSONAL
				    where   PER_ID=$CF_PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$CF_PN_CODE = trim($data[PN_CODE]);
	$CF_PER_NAME = trim($data[PER_NAME]);
	$CF_PER_SURNAME = trim($data[PER_SURNAME]);
	$CF_LEVEL_NO = trim($data[LEVEL_NO]);
	$CF_PER_TYPE = trim($data[PER_TYPE]);
	$CF_POS_ID = trim($data[POS_ID]);
	$CF_POEM_ID = trim($data[POEM_ID]);
	$CF_POEMS_ID = trim($data[POEMS_ID]);
		
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$CF_PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$CF_PN_NAME = trim($data[PN_NAME]);
		
	$CF_PER_NAME = $CF_PN_NAME . $CF_PER_NAME . " " . $CF_PER_SURNAME;
		
	$cmd = " select LEVEL_NAME from PER_LEVEL where trim(LEVEL_NO)='$CF_LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$CF_LEVEL_NAME = trim($data[LEVEL_NAME]);

	if($CF_PER_TYPE==1){ // ข้าราชการ
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE, d.PT_NAME 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c, PER_TYPE d
						 where	a.POS_ID=$CF_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID and a.PT_CODE=d.PT_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_POS_NO = $data[POS_NO];
		$CF_PL_NAME = trim($data[PL_NAME]);
		$CF_ORG_NAME = trim($data[ORG_NAME]);
		$CF_PT_CODE = trim($data[PT_CODE]);
		$CF_PT_NAME = trim($data[PT_NAME]);
		$CF_PL_NAME = trim($CF_PL_NAME)?($CF_PL_NAME ." ". level_no_format($CF_LEVEL_NAME) . (($CF_PT_CODE != "11" && $CF_LEVEL_NO >= 6)?"$CF_PT_NAME":"")):" ".level_no_format($CF_LEVEL_NAME);
	}elseif($CF_PER_TYPE==2){ // ลูกจ้างประจำ
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
					   where	a.POEM_ID=$CF_POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_PL_NAME = trim($data[PN_NAME]);
		$CF_ORG_NAME = trim($data[ORG_NAME]);
	}elseif($CF_PER_TYPE==3){ // พนักงานราชการ
		$cmd = " select 	 b.EP_NAME, c.ORG_NAME 
						 from 	 PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	 a.POEMS_ID=$CF_POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_PL_NAME = trim($data[EP_NAME]);
		$CF_ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	if($command == "SAVE"){

//		echo "00-command=$command,PAGE_ID=$PAGE_ID,RUN_PAGE=$RUN_PAGE,CONTINUE=$CONTINUE,CONTINUE_PAGE=$CONTINUE_PAGE<br>";
		if(!$PAGE_ID || $PAGE_ID==NaN) $PAGE_ID = 0;

		// ถ้าเป็นกรณี เข้าใหม่ จากการค้างการตอบที่ยังไม่เสร็จ
		// หาคำตอบที่ตอบไปแล้ว และ จำนวนคำถามที่เหลือ
		$cmd = " select CF_ID from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_STATUS=4 and CF_SCORE IS NULL ";
		$unfinish_evaluate = $db_dpis->send_cmd($cmd);
//	 	echo "01-"; $db_dpis->show_error(); echo "Page_ID=$PAGE_ID<br>";
		unset($ARR_PASSED_QUESTION);
		unset($ARR_PASSED_ANSWER);
		unset($ARR_VIEW_QUESTION);
		unset($ARR_VIEW_ANSWER);
		unset($ARR_VIEW_ANSWER_DESCRIPTION);
		if( $unfinish_evaluate && !$PAGE_ID ){ // ถ้ายังตอบไม่เสร็จ และรอบแรก
//		if( $unfinish_evaluate && !$NEXT_LOOP ){ // ถ้ายังตอบไม่เสร็จ และรอบแรก
			$data = $db_dpis->get_array();
			$CF_ID = $data[CF_ID];
		
			$cmd = " select QS_ID, CA_ANSWER, CA_DESCRIPTION from PER_COMPETENCY_ANSWER where CF_ID=$CF_ID ORDER BY QS_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "02-"; $db_dpis->show_error();
			while($data = $db_dpis->get_array()){ 
				$ARR_PASSED_QUESTION[] = $data[QS_ID];
				$ARR_PASSED_ANSWER[] = $data[CA_ANSWER];
				$ARR_VIEW_QUESTION[] = $data[QS_ID];
				$ARR_VIEW_ANSWER[$data[QS_ID]] = $data[CA_ANSWER];
				$ARR_VIEW_ANSWER_DESCRIPTION[$data[QS_ID]] = $data[CA_DESCRIPTION];
			} // loop while
			$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
			session_register("SESS_PASSED_QUESTION");
			$SESS_PASSED_ANSWER = implode(",", $ARR_PASSED_ANSWER);
			session_register("SESS_PASSED_ANSWER");
//			echo "03-SESS_PASSED_QUESTION(unfinish)=$SESS_PASSED_QUESTION-$SESS_PASSED_ANSWER<br>";
		
//			$CONTINUE = 1;
//			$CONTINUE_PAGE = count($ARR_PASSED_QUESTION) + 1;
//			$RUN_PAGE=$PAGE_ID=$CONTINUE_PAGE;
//		} else if ($unfinish_evaluate) { // รอบอื่น ๆ
//			$ARR_PASSED_QUESTION=explode(",",$SESS_PASSED_QUESTION);		// อ่านข้อมูลจาก session คำถามและคำตอบ
//			$ARR_PASSED_ANSWER=explode(",",$SESS_PASSED_ANSWER);
//			echo "031-SESS_PASSED_QUESTION=$SESS_PASSED_QUESTION-$SESS_PASSED_ANSWER<br>";
//			$CONTINUE = 0;
//			$RUN_PAGE=$PAGE_ID=$CONTINUE_PAGE;
		} // end if unfinish

//		if($PAGE_ID >= $CONTINUE_PAGE) $CONTINUE = 0;

		// หาข้อมูล คำถาม
		$cmd = " select QS_ID, a.CP_CODE, CL_NO, QS_NAME, b.PC_TARGET_LEVEL 
						  from PER_QUESTION_STOCK a, PER_POSITION_COMPETENCE b
						  where	a.CP_CODE = b.CP_CODE and POS_ID=$CF_POS_ID
						  order by QS_ID, CP_CODE, CL_NO ";
		$db_dpis->send_cmd($cmd);
//		echo "04-"; $db_dpis->show_error();
		$keep_cp_code="";
		$totQUESTION=0;
		$restQUESTION=0;
		while($data = $db_dpis->get_array()){
			$TMP_CP_CODE = trim($data[CP_CODE]);
			$cmd = " select * from PER_COMPETENCE where CP_CODE='$TMP_CP_CODE' ";
			$db_dpis2->send_cmd($cmd);
//			echo "05-";$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$CP_ASSESSMENT = $data2[CP_ASSESSMENT];
//			echo "06-in_array($data[QS_ID]<==>".implode(",",$ARR_PASSED_QUESTION).")";
			if (in_array($data[QS_ID],$ARR_PASSED_QUESTION)) { // ถ้าเคยผ่านคำถามนี้ไปแล้ว
				$haveAnswer=true;
//				echo "pass-qid=$data[QS_ID] ";
			} else { // ถ้ายังไม่เคยผ่านคำถามนี้ ต้องตรวจว่าเป็นคำถามกระโดดข้ามหรือเปล่า ถ้าใช้ก็ถือว่าตอบแล้ว
//				echo "not pass-qid=$data[QS_ID] ";
				$haveAnswer=false;
				$end_grp_qs=((floor(($data[QS_ID]-1)/5)+1)*5); 	// 	หาข้อสุดท้ายในกลุ่ม 5 ข้อ [5 10 15 ...]
				$start_grp_qs=$end_grp_qs-4; 								// 	หาข้อเริ่มในกลุ่ม 5 ข้อ [1 6 11 ...]
				$c = array_search($start_grp_qs,$ARR_PASSED_QUESTION); 	// หา index เริ่มต้นของกลุ่มคำถาม
				if ($c > -1) {  // ถ้ามี index ของคำถามเริ่มต้นนี้อยู่ คือหาคำถามเริ่มต้นกลุ่มเจอ
					// loop หาคำถามกรณีที่มีการกระโดดข้าม (ANSWER=1 หรือ 2)
//					echo "stgrpqs=$start_grp_qs-$c ";
					$cc=-1;
					for($kk=$c; $kk < $c+5; $kk++) {
						if ($ARR_PASSED_QUESTION[$kk] <= $end_grp_qs) {  // ถ้ายังอยู่ในกลุ่ม
							if ($ARR_PASSED_ANSWER[$kk]==2 || $ARR_PASSED_ANSWER[$kk]==1) {
								if ($data[QS_ID] > $ARR_PASSED_QUESTION[$kk]) {  // ถ้าคำถามนี้ มากกว่าคำถามที่ตอบ 1 หรือ 2
									$haveAnswer=true; // ถือว่าตอบผ่านแล้ว
//									echo "skip ";
								}
							} // end if คำตอบเป็น 2 หรือ 1
						} else { // ถ้าหลุดนอกกลุ่ม
							break;
						} // ถ้ายังอยู่ในกลุ่ม
					} // end loop for
					// ถ้าไม่มีคำตอบ 1,2 และ QS_ID เริ่มต้นกลุ่มเคยตอบไปแล้ว CP_CODE กลุ่มนี้เอามาตอบเป็นกลุ่มแรก
					if (!$haveAnswer && in_array($start_grp_qs,$ARR_PASSED_QUESTION))  $keep_cp_code=trim($data[CP_CODE]);
					if (!$haveAnswer) $restQUESTION++;
//					echo "haveAnswer=$haveAnswer ";
				} // if ($c) ถ้าหา index ของคำถามนี้ได้
			} // end if ยังไม่เคยผ่านคำถามนี้
//			echo "<br>";
			if ($CP_ASSESSMENT <> "N" && !$haveAnswer) {
				$ARR_EVALUATE_QUESTION[] = $data[QS_ID];
				$ARR_QUESTION_NAME[$data[QS_ID]] = trim($data[CP_CODE]);
				$ARR_QUESTION_TOPIC[$data[QS_ID]] = $data[CL_NO];
				$ARR_QUESTION_DESC[$data[QS_ID]] = $data[QS_NAME];
				$ARR_QUESTION_LEVEL[$data[QS_ID]] = $data[PC_TARGET_LEVEL];
				$GET_CP_CODE = trim($data[CP_CODE]);
				$GET_CL_NO = trim($data[CL_NO]);
				$GET_QS_ID = trim($data[QS_ID]);
		
				if(!in_array($GET_CP_CODE,$ARR_CP_CODE)) {
//					$keep="=keep";
					if ($GET_CP_CODE != $keep_cp_code) { // กรณีที่ในกลุ่มนี้มีการตอบไปแล้วแต่ยังไม่จบ จะไปเพิ่มท้ายสุด
						$ARR_CP_CODE[] = $GET_CP_CODE;
//						$keep="!=keep";
					}
//					echo "CP_CODE=$GET_CP_CODE $keep<br>";
				}
//				echo "07-QS_ID=$data[QS_ID],CP_CODE=$GET_CP_CODE,keep_cp_code=$keep_cp_code ";

				$ARR_TMP_CP_CODE[] = $GET_CP_CODE;
				$ARR_QUESTION[$GET_CP_CODE][$GET_CL_NO][ID] = $GET_QS_ID;
				$ARR_QUESTION[$GET_CP_CODE][$GET_CL_NO][NAME] = $data[QS_NAME];
			} // end if
			if ($CP_ASSESSMENT <> "N") {
				$totQUESTION++;
			}
		} // end while
		$listShowCnt=count($ARR_EVALUATE_QUESTION);
//		echo "08-TotalQ=$totQUESTION, restQ=$restQUESTION<br>";
		shuffle($ARR_CP_CODE);	// สลับกลุ่มคำถาม
		if ($keep_cp_code > "") $ARR_CP_CODE[]=$keep_cp_code; // เพิ่มกลุ่มที่ตอบไปแล้วแต่ยังไม่จบต่อท้าย
		if(empty($RAND_CP_CODE)) {
			$CP_CODE_STR = implode(',',$ARR_CP_CODE);
		}
		$unfinish_cut = 0;
//		echo "RAND_CP_CODE=$RAND_CP_CODE,CP_CODE_STR=$CP_CODE_STR<br>";
		if ($unfinish_evaluate && !$PAGE_ID) {
			$PAGE_ID=$RUN_PAGE=$totQUESTION - $listShowCnt + 1;
			
      		$xxx_array = explode(',',$CP_CODE_STR);
       		$RAND_CP_CODE = array_pop($xxx_array);
        	$CP_CODE_STR = implode(',',$xxx_array);
        	$CUR_CO_NO = $PAGE_ID%5;
			if ($CUR_CO_NO==0) $CUR_CO_NO = 5;
			$unfinish_cut = 1;
		}
//		echo "PAGE_ID=$PAGE_ID,RAND_CP_CODE=$RAND_CP_CODE,CP_CODE_STR=$CP_CODE_STR<br>";
		$UPDATE_DATE = date("Y-m-d H:i:s");
	
		$NEXT_PAGE_ID = $PAGE_ID;
//		$PAGE_ID = $PAGE_ID - 1;
//		echo "09-next_page=$NEXT_PAGE_ID,page_id=$PAGE_ID<br>";
		if(!$unfinish_evaluate && !$PAGE_ID){
//		if(!$NEXT_SAVE){
			$cmd = " SELECT max(CF_ID) as MAX_ID FROM PER_COMPETENCY_FORM ";
			$db_dpis->send_cmd($cmd);
//			echo "10-"; $db_dpis->show_error();
			$data = $db_dpis->get_array();
			$MAX_ID = $data[MAX_ID] + 1;
//			echo " not page_id+MAX_ID=$MAX_ID<br>";

			$cmd = " insert into PER_COMPETENCY_FORM (CF_ID, KF_ID, CF_TYPE, CF_PER_ID, CF_STATUS, UPDATE_USER, UPDATE_DATE)
						   values ($MAX_ID, $KF_ID, $CF_TYPE, $CF_PER_ID, 4, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			$cmd = " select CF_ID from PER_COMPETENCY_FORM where KF_ID=$KF_ID and CF_TYPE = $CF_TYPE and 
							CF_PER_ID = $CF_PER_ID and UPDATE_USER=$SESS_USERID and UPDATE_DATE='$UPDATE_DATE' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$CF_ID = $data[CF_ID];

			$PAGE_ID++;
			$RUN_PAGE=1;
//			$NEXT_SAVE=1;
//			echo "11-page_id=$PAGE_ID,run_page=$RUN_PAGE<br>";
		} else {
			//for ( $i=0; $i<115; $i++ ) $ARR_ANSWER_SCORE[$i] = 1;
			//$ANSWER_SCORE = $ARR_ANSWER_SCORE[$ANSWER];
			foreach($ANSWER as $ANSWER_ID => $ANSWER_SCORE) 
			$temp_array = explode('_',$ANSWER_SCORE);
			$ANSW_ID = $temp_array[0];
//			echo "ANSW_ID=$ANSW_ID<br>";
			$ANSWER_SCORE = $temp_array[1];
			$cmd = " INSERT into PER_COMPETENCY_ANSWER (CF_ID, QS_ID, CA_ANSWER, CA_DESCRIPTION, UPDATE_USER, UPDATE_DATE)
					       VALUES ($CF_ID, $QS_ID, $ANSWER_ID, '$DESCRIPTION', $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			// skip question
			$skip="";
			if($ANSWER && !$ANSWER_SCORE) {
				$CUR_PAGE_ID = $NEXT_PAGE_ID - 1;
				$NEXT_PAGE_ID = (ceil($CUR_PAGE_ID/5)*5)+1;
//				echo "CUR_PAGE=$CUR_PAGE_ID:$NEXT_PAGE_ID<== SKIP NEXT<br>";
//				$skip="SKIP";
			} 
			$PAGE_ID = $NEXT_PAGE_ID;
			if($ANSWER) $RUN_PAGE++;
			unset($ANSWER, $DESCRIPTION);
			unset($ANSWER_ID, $ANSWER_SCORE);
//			echo "12-page_id=$PAGE_ID,run_page=$RUN_PAGE,ans=$ANSW_ID,$skip<br>";
		} // end if(!$unfinish_evaluate && !$PAGE_ID)

//		echo "NEXT_PAGE_ID=$NEXT_PAGE_ID";
	} // end if SAVE

//	echo "13-VIEW=$VIEW,CONTINUE=$CONTINUE,CONTINUE_PAGE=$CONTINUE_PAGE<br>";

	if(!$VIEW && !$CONTINUE){
		if(!$PAGE_ID){
//		if(!$NEXT_LOOP){
			$SESS_PASSED_QUESTION = "";
			session_register("SESS_PASSED_QUESTION");
			$SESS_PASSED_ANSWER = "";
			session_register("SESS_PASSED_ANSWER");
//			echo "14-Q/A=$SESS_PASSED_QUESTION/$SESS_PASSED_ANSWER<br>";
		}elseif($PAGE_ID <= $totQUESTION){
			if(trim($SESS_PASSED_QUESTION)){ 
				unset($ARR_PASSED_QUESTION);
				$ARR_PASSED_QUESTION = explode(",", $SESS_PASSED_QUESTION);
				unset($ARR_PASSED_ANSWER);
				$ARR_PASSED_ANSWER = explode(",", $SESS_PASSED_ANSWER);
			} // end if
	/*
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
	*/
			$ARR_PASSED_QUESTION[] = $QS_ID;
			$SESS_PASSED_QUESTION = implode(",", $ARR_PASSED_QUESTION);
			session_register("SESS_PASSED_QUESTION");
			$ARR_PASSED_ANSWER[] = $ANSW_ID;
			$SESS_PASSED_ANSWER = implode(",", $ARR_PASSED_ANSWER);
			session_register("SESS_PASSED_ANSWER");
			
//			echo "15-Q/A=$SESS_PASSED_QUESTION/$SESS_PASSED_ANSWER<br>";
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
//			echo "  SESS_PASSED_QUESTION=$SESS_PASSED_QUESTION";
		}elseif($PAGE_ID > $totQUESTION){
			unset($QS_ID);

			// ========================== Prepare Data ===============================		
			$cmd = " select count(CP_CODE) as POINT, CP_CODE  
							  from PER_COMPETENCY_ANSWER a, PER_QUESTION_STOCK b
							  where CF_ID=$CF_ID and a.QS_ID = b.QS_ID
							  group by CP_CODE
							  order by CP_CODE ";
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
				$cmd = " select count(CP_CODE) as POINT
								  from PER_COMPETENCY_ANSWER a, PER_QUESTION_STOCK b
								  where CF_ID=$CF_ID and a.QS_ID = b.QS_ID and CP_CODE=$CP_CODE and CA_ANSWER > 2
								  group by CP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POINT = $data2[POINT];
				$ARR_SELECTED_ANSWER[] = "$CP_CODE:$POINT";
			}
			$SAVE_SCORE = implode(',',$ARR_SELECTED_ANSWER);
			$SAVE_STATUS = 1;
			
			$cmd = " update PER_COMPETENCY_FORM set
								CF_SCORE='$SAVE_SCORE',
								CF_STATUS='$SAVE_STATUS'
							 where CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			$cmd = " SELECT * FROM PER_COMPETENCY_FORM WHERE CF_PER_ID = $CF_PER_ID AND CF_STATUS=1 ORDER BY CF_TYPE";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			unset($arr_CP_CODE1);
			unset($arr_POINT);
			unset($arr_N);
			unset($arr_KF_ID);
			unset($arr_PERC);
			unset($arr_cttype);
			$U_KF_ID=0;
			while($data = $db_dpis->get_array()) {
				if ($U_KF_ID==0) {
					$cmd1 = " SELECT * FROM PER_KPI_FORM WHERE PER_ID=$CF_PER_ID AND KF_START_DATE='$chkKFStartDate' AND KF_END_DATE='$chkKFEndDate' ";
					$db_dpis2->send_cmd($cmd1);
	//				$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$U_KF_ID = $data2[KF_ID];
				}
				$U_CF_TYPE = $data[CF_TYPE]; // ประเภทการประเมิน 1.ประเมินตนเอง 2 ประเมินผู้บังคับบัญชา 3 ประเมินเพื่อน 4 ประเมินผู้ใต้บังคับบัญชา
				// รายงานนี้แสดงยอดสรุปการประเมินตามผู้ถูกประเมิน ความหมายจึงเป็นตรงกันข้ามดังนี้ 1 ตนเองประเมินตนเอง 2 ผู้ใต้บังคับบัญชาประเมิน 3 เพื่อนประเมิน 4 ผู้บังคับบัญชาประเมิน
				// จากที่เห็นจึงต้องเปลี่ยน $U_CF_TYPE เป็นตรงกันข้าม ซึ่งมีที่ต้องสลับ 2 ตัวคือค่า 2 เป็น 4 และ 4 เป็น 2 รายงานจะลงถูกความหมาย
				if ($U_CF_TYPE==2) { $U_CF_TYPE=4; } else if ($U_CF_TYPE==4) { $U_CF_TYPE=2; }
				// จัดเตรียมค่า cf_type array เพื่อการอ่าน Percent น้ำหนัก
				if (!in_array($CF_TYPE, $arr_cftype)) {
					$arr_cftype[] = $CF_TYPE;
				}
				
				$cmd1 = " SELECT * FROM PER_KPI_FORM WHERE KF_ID=$data[KF_ID] ";
				$db_dpis2->send_cmd($cmd1);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$othStartDate=$data2[KF_START_DATE];;
				$othEndDate=$data2[KF_END_DATE];;
				if ($othStartDate == $chkKFStartDate && $othEndDate == $chkKFEndDate) { // if startDate and endDate ตรงกัน
					$U_CF_SCORE = explode(",",$data[CF_SCORE]); // ค่า CF_SCORE คือ 101:3,102:4....
					for($kk = 0; $kk < count($U_CF_SCORE); $kk++) {
						$sub_score=explode(":",$U_CF_SCORE[$kk]);
//						echo "cp_code=$sub_score[0],point=$sub_score[1]<br>";
						if (strlen($sub_score[0]) > 0) {
							if (in_array($sub_score[0],$arr_CP_CODE1) && in_array($U_KF_ID,$arr_KF_ID)) {
							// ถ้าค่า KF_ID มี index ตรงกับ CP_CODE แสดงว่าเป็นข้อมูลชุดเดียวกัน
								$c=array_search($sub_score[0],$arr_CP_CODE1);
								$c1=array_search($U_KF_ID,$arr_KF_ID);
								if ($c == $c1) {
									$arr_POINT[$c] = $arr_POINT[$c] + $sub_score[1];
									$arr_N[$c]++;
								} else {
									$arr_CP_CODE1[] = $sub_score[0];
									$arr_POINT[] = $sub_score[1];
									$arr_N[] = 1;
									$arr_KF_ID[] = $U_KF_ID;
								}
							} else {
								$arr_CP_CODE1[] = $sub_score[0];
								$arr_POINT[] = $sub_score[1];
								$arr_N[] = 1;
								$arr_KF_ID[] = $U_KF_ID;
							} // end if
						} // end if (strlen($sub_score[0]) > 0)
					} // for loop
				} // end if startDate and endDate ตรงกัน
			} // while 

			// เรียงชุดของ cftype เพื่อไปต่อทำเป็น key ในการเรียก per_pos_type
			array_multisort($arr_cftype, SORT_ASC);
			//	echo implode("",$arr_cftype)+"|";
			$postype=substr($CF_LEVEL_NO,0,1).implode("",$arr_cftype);
//			echo "LEVEL_NO=$CF_LEVEL_NO<br>";
			$cmd = " SELECT * FROM PER_POS_TYPE
							WHERE POS_TYPE='$postype' ";
			$db_dpis->send_cmd($cmd);
//			echo "16-"; $db_dpis->show_error();
			$data = $db_dpis->get_array();
			$arr_PERC[1] = $data[SEFT_RATIO];
			$arr_PERC[2] = $data[CHIEF_RATIO];
			$arr_PERC[3] = $data[FRIEND_RATIO];
			$arr_PERC[4] = $data[SUB_RATIO];

			for($k1 = 0; $k1 < count($arr_POINT); $k1++) {
//				echo "18-($k1-->$arr_KF_ID[$k1]:$arr_CP_CODE1[$k1]:$arr_POINT[$k1]:$arr_N[$k1]:$arr_PERC[$k1])";
				$AVGMARK = $arr_POINT[$k1] / $arr_N[$k1];

				$cmd = " UPDATE PER_KPI_COMPETENCE SET
							 	 kc_evaluate  = $AVGMARK, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								 WHERE KF_ID=$arr_KF_ID[$k1] and trim(CP_CODE)='$arr_CP_CODE1[$k1]'";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
//			echo "<br>";
			//print_r($ARR_SELECTED_ANSWER);
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
			$SESS_PASSED_ANSWER = "";
			session_register("SESS_PASSED_ANSWER");
		} // end if PAGE_ID

	}elseif($VIEW || $CONTINUE){
		if(!$PAGE_ID){
//		if(!$NEXT_LOOP){
//			echo "19-PAGE_ID=$PAGE_ID<br>";
//		}elseif($PAGE_ID <= count($ARR_EVALUATE_QUESTION)){
		}elseif($PAGE_ID <= $totQUESTION){
			$cmd = " select		QS_ID, CA_ANSWER, CA_DESCRIPTION
							 from		PER_COMPETENCY_ANSWER
							 where		CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "20-"; $db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$ARR_VIEW_QUESTION[] = $data[QS_ID];
				$ARR_VIEW_ANSWER[$data[QS_ID]] = $data[CA_ANSWER];
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
//		}elseif($PAGE_ID > count($ARR_EVALUATE_QUESTION)){
		}elseif($PAGE_ID > $totQUESTION){
			$cmd = " select CF_ID from PER_COMPETENCY_FORM where CF_ID=$CF_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "21-"; $db_dpis->show_error();
			$ISPASSED = $db_dpis->get_array();
//			$ISPASSED = $data[ISPASSED];
		} // end if PAGE_ID

	} // end if VIEW
?>