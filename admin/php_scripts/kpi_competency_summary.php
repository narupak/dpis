<?	
	include("php_scripts/session_start.php");
	include("php_scripts/load_per_control.php");
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmd = " select KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$chkKFStartDate = $data[KF_START_DATE];
	$chkKFEndDate = $data[KF_END_DATE];
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_1 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_1, 6, 4);
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = show_date_format($data[KF_START_DATE], 1);
		$KF_END_DATE_2 = show_date_format($data[KF_END_DATE], 1);
		$KF_YEAR = substr($KF_END_DATE_2, 6, 4);
	}

	$PER_ID = $data[PER_ID];
	$THIS_KF_ID = $KF_ID;
	$THIS_PER_ID = $PER_ID;
	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from PER_PERSONAL where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$THIS_LEVEL=$LEVEL_NO;
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
		
	$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$LEVEL_NAME = trim($data[LEVEL_NAME]);
	$POSITION_LEVEL = $data[POSITION_LEVEL];
	if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

	if($PER_TYPE==1){
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PT_NAME = trim($data[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
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

// 	อ่านข้อมูลผู้ใต้บังคับบัญชา
	if ($S_KF_ID) {
		$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$S_KF_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CF_PER_ID = $data[PER_ID];
		$THIS_KF_ID = $S_KF_ID;
		$THIS_PER_ID = $CF_PER_ID;
		
		$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, LEVEL_NO, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from	PER_PERSONAL
					    where   PER_ID=$CF_PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_PN_CODE = trim($data[PN_CODE]);
		$CF_PER_NAME = trim($data[PER_NAME]);
		$CF_PER_SURNAME = trim($data[PER_SURNAME]);
		$CF_LEVEL_NO = trim($data[LEVEL_NO]);
		$THIS_LEVEL=$CF_LEVEL_NO;
		$CF_PER_TYPE = trim($data[PER_TYPE]);
		$CF_POS_ID = trim($data[POS_ID]);
		$CF_POEM_ID = trim($data[POEM_ID]);
		$CF_POEMS_ID = trim($data[POEMS_ID]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$CF_PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_PN_NAME = trim($data[PN_NAME]);
		
		$CF_PER_NAME = $CF_PN_NAME . $CF_PER_NAME . " " . $CF_PER_SURNAME;
		
		$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$CF_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CF_LEVEL_NAME = trim($data[LEVEL_NAME]);
		$CF_POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$CF_POSITION_LEVEL) $CF_POSITION_LEVEL = $CF_LEVEL_NAME;

		if($CF_PER_TYPE==1){ // ข้าราชการ
			$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
							 from 		PER_POSITION a, PER_LINE b, PER_ORG c
							 where	a.POS_ID=$CF_POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CF_POS_NO = $data[POS_NO];
			$CF_PL_NAME = trim($data[PL_NAME]);
			$CF_ORG_NAME = trim($data[ORG_NAME]);
			$CF_PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$CF_PT_CODE' ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$CF_PT_NAME = trim($data[PT_NAME]);
			$CF_PL_NAME = trim($CF_PL_NAME)?($CF_PL_NAME . $CF_POSITION_LEVEL . (($CF_PT_NAME != "ทั่วไป" && $CF_LEVEL_NO >= 6)?"$CF_PT_NAME":"")):" ".$CF_LEVEL_NAME;
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
	} // end if ($S_KF_ID)
	
	$arr_content = (array) null;
	$arr_percent = (array) null;
	$arr_n = (array) null;

	$cmd = " SELECT * FROM PER_COMPETENCY_FORM WHERE CF_PER_ID = $THIS_PER_ID AND CF_STATUS=1 ";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count=0;
	while($data = $db_dpis->get_array()){
		$U_KF_ID = $data[KF_ID];
		$cmd1 = " SELECT * FROM PER_KPI_FORM WHERE KF_ID=$U_KF_ID "; // KF_ID ของรายการ คนที่ประเมินให้ เพื่อเช็คว่าเป็นช่วงเดียวกัน
		$db_dpis2->send_cmd($cmd1);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$othStartDate=$data2[KF_START_DATE];
		$othEndDate=$data2[KF_END_DATE];
		if ($othStartDate == $chkKFStartDate && $othEndDate == $chkKFEndDate) { // if startDate and endDate ตรงกัน
			$CF_TYPE = $data[CF_TYPE]; // ประเภทการประเมิน 1.ประเมินตนเอง 2 ประเมินผู้บังคับบัญชา 3 ประเมินเพื่อน 4 ประเมินผู้ใต้บังคับบัญชา
			// รายงานนี้แสดงยอดสรุปการประเมินตามผู้ถูกประเมิน ความหมายจึงเป็นตรงกันข้ามดังนี้ 1 ตนเองประเมินตนเอง 2 ผู้ใต้บังคับบัญชาประเมิน 3 เพื่อนประเมิน 4 ผู้บังคับบัญชาประเมิน
			// จากที่เห็นจึงต้องเปลี่ยน $CF_TYPE เป็นตรงกันข้าม ซึ่งมีที่ต้องสลับ 2 ตัวคือค่า 2 เป็น 4 และ 4 เป็น 2 รายงานจะจะลงถูกความหมาย
			if ($CF_TYPE==2) { $CF_TYPE=4; } else if ($CF_TYPE==4) { $CF_TYPE=2; }
			// จัดเตรียมค่า cf_type array เพื่อการอ่าน Percent น้ำหนัก
			if (!in_array($CF_TYPE, $arr_cftype)) {
				$arr_cftype[] = $CF_TYPE;
			}

			$CF_SCORE = $data[CF_SCORE]; // จาก PER_COMPETENCY_FORM
			$ARR_POINT_SET = explode(",",$CF_SCORE);
			for($data_count=0; $data_count < count($ARR_POINT_SET); $data_count++) {
				$POINT_SET = explode(":",$ARR_POINT_SET[$data_count]);
				$SUB_CP_CODE=$POINT_SET[0];
				$POINT = $POINT_SET[1];
				$cmd2 = " select PC_TARGET_LEVEL from PER_KPI_COMPETENCE where KF_ID=$THIS_KF_ID AND CP_CODE='$SUB_CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
					
				$PC_TARGET_LEVEL = $data3[PC_TARGET_LEVEL];
				$cmd2 = " select CP_NAME, CP_MODEL from PER_COMPETENCE where CP_CODE='$SUB_CP_CODE' ";
				$db_dpis3->send_cmd($cmd2);
//				$db_dpis3->show_error(); 
				$data3 = $db_dpis3->get_array();
				$CP_NAME = $data3[CP_NAME];
				$CP_MODEL = $data3[CP_MODEL];
				$ST_CP_MODEL="";
				if($CP_MODEL==1) $ST_CP_MODEL = "สมรรถนะหลัก";
				elseif($CP_MODEL==2) $ST_CP_MODEL = "สมรรถนะผู้บริหาร";
				elseif($CP_MODEL==3) $ST_CP_MODEL = "สมรรถนะประจำสายงาน";

				$search_idx = -1;
				for($kkk=0; $kkk <= count($arr_content); $kkk++) {
					if ($arr_content[$kkk][code]==$SUB_CP_CODE) {
						$search_idx = $kkk;
						break;
					}
				} // end for $kkk loop
				if ($search_idx > -1) {
					$arr_content[$search_idx][$CF_TYPE] = $arr_content[$search_idx][$CF_TYPE] + $POINT;
					$arr_n[$search_idx][$CF_TYPE]++;
//					$arr_content[$search_idx][0] = $PC_TARGET_LEVEL;
				} else {
					$arr_content[$data_count][code] = $SUB_CP_CODE;
					$arr_content[$data_count][name] = $CP_NAME;
					$arr_content[$data_count][type] = $ST_CP_MODEL;
					$arr_content[$data_count][cftype] = $CF_TYPE;
					$arr_content[$data_count][$CF_TYPE] = $POINT;
					$arr_n[$data_count][$CF_TYPE] = 1;
					$arr_content[$data_count][0] = $PC_TARGET_LEVEL;
					$arr_content[$data_count][5] = 0;
					$arr_content[$data_count][gap] = 0;
				} // end if ($search_idx > -1)
//				echo "$KF_ID:$PER_ID:$PER_NAME==>$CP_NAME:$ST_CP_MODEL:$KC_EVALUATE:$PC_TARGET_LEVEL\n";
			} // end for $datacount loop
		} // end if startDate and endDate ตรงกัน
	} // end while

	// เรียงชุดของ cftype เพื่อไปต่อทำเป็น key ในการเรียก per_pos_type
	array_multisort($arr_cftype, SORT_ASC);
//	echo implode("",$arr_cftype)+"|";
	$postype=substr($THIS_LEVEL,0,1).implode("",$arr_cftype);
//	echo "LEVEL_NO=$THIS_LEVEL<br>";
	$cmd = " SELECT * FROM PER_POS_TYPE WHERE POS_TYPE='$postype' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$arr_percent[1] = $SEFT_RATIO = $data[SEFT_RATIO];
	$arr_percent[2] = $CHIEF_RATIO = $data[CHIEF_RATIO];
	$arr_percent[3] = $FRIEND_RATIO = $data[FRIEND_RATIO];
	$arr_percent[4] = $SUB_RATIO = $data[SUB_RATIO];
	//	echo "postype=$postype - RATIO[$postype]=$SEFT_RATIO,$CHIEF_RATIO,$FRIEND_RATIO,$SUB_RATIO<br>";

// กรณีที่มีการกดปุ่มคำนวนใหม่
	if ($command == "RECOMPUTE") {
		$UPDATE_DATE = date("Y-m-d H:i:s");
		for($data_count=0; $data_count<count($arr_content); $data_count++) {
			$KC_EVALUATE = 0;
			for($sumcolcnt=1; $sumcolcnt < 5; $sumcolcnt++) { 
	 			$point = $arr_content[$data_count][$sumcolcnt] / $arr_n[$data_count][$sumcolcnt];
	 			$KC_EVALUATE = $KC_EVALUATE + ($point * $arr_percent[$sumcolcnt] / 100);
			}
			$KC_EVALUATE = round($KC_EVALUATE,2);
			$TARGET_LEVEL = $arr_content[$data_count][0];

			$cmd = " UPDATE PER_KPI_COMPETENCE 
						   			SET PC_TARGET_LEVEL = $TARGET_LEVEL, KC_EVALUATE = $KC_EVALUATE,
									 	    UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
							 WHERE KF_ID=$THIS_KF_ID and trim(CP_CODE)='".$arr_content[$data_count][code]."'  ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		} // end loop for $data_count
	} // end if $command="RECOMPUTE"
?>