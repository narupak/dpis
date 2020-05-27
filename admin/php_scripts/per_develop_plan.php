<?	
	include("php_scripts/session_start.php");
	include("php_scripts/load_per_control.php");
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

// Check KF_ID ว่าเป็นรอบการประเมินไหน	และ หารหัส พนักงานด้วย
	$cmd = " select 	KF_CYCLE, KF_START_DATE, KF_END_DATE, PER_ID from PER_KPI_FORM where KF_ID=$KF_ID ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$KF_CYCLE = trim($data[KF_CYCLE]);
	$chkKFStartDate = $data[KF_START_DATE];
	$chkKFEndDate = $data[KF_END_DATE];
	if($KF_CYCLE==1){	//ตรวจสอบรอบการประเมิน
		$KF_START_DATE_1 = show_date_format($KF_START_DATE, 1);
		$KF_END_DATE_1 = show_date_format($KF_END_DATE, 1);
		$KF_YEAR = substr($KF_END_DATE_1, 6, 4);
	}else if($KF_CYCLE==2){
		$KF_START_DATE_2 = show_date_format($KF_START_DATE, 1);
		$KF_END_DATE_2 = show_date_format($KF_END_DATE, 1);
		$KF_YEAR = substr($KF_END_DATE_2, 6, 4);
	}
	$PER_ID = $data[PER_ID];
//  end Check KF_ID ว่าเป็นรอบการประเมินไหน	และ หารหัส พนักงานด้วย

// Read Head Data
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

	if($PER_TYPE==1){ // ข้าราชการ
		$cmd = " select 	a.POS_NO, b.PL_NAME, c.ORG_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b, PER_ORG c
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];
		$PL_NAME = trim($data[PL_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME = trim($data2[PT_NAME]);
		$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
	}elseif($PER_TYPE==2){  // ลูกจ้างประจำ
		$cmd = " select 	b.PN_NAME, c.ORG_NAME 
						 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	}elseif($PER_TYPE==3){ // พนักงานราชการ
		$cmd = " select 	b.EP_NAME, c.ORG_NAME 
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
		$ORG_NAME = trim($data[ORG_NAME]);
	} // end if

	$cmd = " select CP_NAME, CP_MODEL from PER_COMPETENCE where CP_CODE='$CP_CODE' ";
	$db_dpis->send_cmd($cmd);
//	$db_dpis3->show_error(); 
	$data = $db_dpis->get_array();
	$CP_NAME = $data[CP_NAME];
	$CP_MODEL = $data[CP_MODEL];
// end Read Head Data

	if (strlen($command) > 0) { // loop save(Update, Insert) and delete button
//		echo "PLAN_ID=$PLAN_ID,GUIDE_ID=$GUIDE_ID,command=$command<br>";
		// SAVE for ADD or UPDATE
		$UPDATE_DATE = date("Y-m-d H:i:s");
		if ($command=="SAVE") {
			if ($PLAN_ID > 0) {
//				echo "Upd -> GUIDE_ID=$GUIDE_ID<BR>";
				$cmd = " update PER_DEVELOPE_PLAN set
								PD_GUIDE_ID=$GUIDE_ID, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
								where PD_PLAN_ID = $PLAN_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			} else { // $PLAN_ID ไม่มีค่า
//				echo "Ins -> GUIDE_ID=$GUIDE_ID<BR>";
				$UPDATE_DATE = date("Y-m-d H:i:s");
				$cmd = " SELECT MAX(PD_PLAN_ID) as MAXID FROM PER_DEVELOPE_PLAN ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$NEXTID=$data[MAXID]+1;
				$cmd = " INSERT INTO PER_DEVELOPE_PLAN  
								(PD_PLAN_ID, PD_PLAN_KF_ID, PD_GUIDE_ID, PLAN_FREE_TEXT, PD_PLAN_START, PD_PLAN_END, UPDATE_USER, UPDATE_DATE)
								VALUES ($NEXTID, $KF_ID, $GUIDE_ID, '', '', '', $SESS_USERID, '$UPDATE_DATE')";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$PLAN_ID = $NEXTID;
			} // end if ($PLA_ID > 0)
		} else if ($command=="DELETE") {
			$cmd = "  delete from PER_DEVELOPE_PLAN 
							where PD_PLAN_ID = $PLAN_ID ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$PLAN_ID="";
			$GUIDE_ID="";
			$DESCRIPTION1="";
			$DESCRIPTION2="";
			$ENDDATE="";
		} // end if ($command=="SAVE")
	} else { // $command ไม่มีค่า // เป็น ส่วน ของการแสดงค่าบน form
		// Read Form Data
		if (!$PLAN_ID) {
			$cmd = " 	SELECT * FROM PER_DEVELOPE_PLAN a, PER_DEVELOPE_GUIDE b 
							WHERE a.pd_guide_id = b.pd_guide_id and PD_PLAN_KF_ID = $KF_ID AND PD_GUIDE_COMPETENCE = $CP_CODE AND PD_GUIDE_LEVEL = $GONEXT_LEVEL ";
			$db_dpis->send_cmd($cmd);
//			echo "1."; $db_dpis->show_error();
			if ($data = $db_dpis->get_array()) { // มีข้อมูล PER_DEVLOPE_PLAN ป้อนเข้าไปแล้ว
				$PLAN_ID = $data[PD_PLAN_ID];
				$GUIDE_ID = $data[PD_GUIDE_ID];
				$DESCRIPTION1 = $data[PD_GUIDE_DESCRIPTION1];
				$DESCRIPTION2 = $data[PD_GUIDE_DESCRIPTION2];
			} else if ($GUIDE_ID > 0) { // ไม่มีข้อมูล PER_DEVLOPE_PLAN อยู่ และมีการเลือกข้อมูล แนวทางมาแล้ว
				$cmd = " 	SELECT * FROM PER_DEVELOPE_GUIDE
								WHERE PD_GUIDE_COMPETENCE = $CP_CODE AND PD_GUIDE_LEVEL = $GONEXT_LEVEL AND PD_GUIDE_ID = $GUIDE_ID ";
				$db_dpis->send_cmd($cmd);
//				echo "2."; $db_dpis->show_error();
				if ($data = $db_dpis->get_array()) { 
					$DESCRIPTION1 = $data[PD_GUIDE_DESCRIPTION1];
					$DESCRIPTION2 = $data[PD_GUIDE_DESCRIPTION2];
				}
			} // end if ($data = $db_dpis->get_array()
		} else { // ถ้า $PLA_ID มีค่าคือมีข้อมูลอยู่ ต้องเปรียบเทียบ guide_id ที่อ่านได้ กับที่เลือก ถ้าตรงกันก็แสดงข้อมูลเก่า
			$cmd = " 	SELECT * FROM PER_DEVELOPE_PLAN a, PER_DEVELOPE_GUIDE b 
							WHERE a.pd_guide_id = b.pd_guide_id and PD_PLAN_ID = $PLAN_ID ";
			$db_dpis->send_cmd($cmd);
//			echo "3."; $db_dpis->show_error();
			if ($data = $db_dpis->get_array()) { // มีข้อมูล PER_DEVLOPE_PLAN ป้อนเข้าไปแล้ว
				if ($GUIDE_ID == $data[PD_GUIDE_ID]) {
					$DESCRIPTION1 = $data[PD_GUIDE_DESCRIPTION1];
					$DESCRIPTION2 = $data[PD_GUIDE_DESCRIPTION2];
				}  else { // ถ้า $GUIDE_ID ที่เลือกไม่ตรงกับข้อมูลเก่า ก็แสดงตาม GUIDE_ID ที่เลือก เพื่อที่จะแก้ไข
					$cmd = " 	SELECT * FROM PER_DEVELOPE_GUIDE
									WHERE PD_GUIDE_COMPETENCE = $CP_CODE AND PD_GUIDE_LEVEL = $GONEXT_LEVEL AND PD_GUIDE_ID = $GUIDE_ID ";
					$db_dpis->send_cmd($cmd);
//					echo "4."; $db_dpis->show_error();
					if ($data = $db_dpis->get_array()) { 
						$DESCRIPTION1 = $data[PD_GUIDE_DESCRIPTION1];
						$DESCRIPTION2 = $data[PD_GUIDE_DESCRIPTION2];
					}
				} // end if  ($GUIDE_ID == $data[PD_GUIDE_ID])
			}
		} // end if (!$PLAN_ID)
		// end ReadForm Data	
	} // end if (strlen($command) > 0)

// Read Plan Guide List สำหรับ CP_CODE และ GUIDE_LEVEL ที่ต้องการ
	$arr_content = (array) null;

	$data_count = 0;
	$cmd = " SELECT * FROM PER_DEVELOPE_GUIDE WHERE PD_GUIDE_COMPETENCE = $CP_CODE AND PD_GUIDE_LEVEL = $GONEXT_LEVEL ORDER BY PD_GUIDE_ID";
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
	$data_count=0;
	while($data = $db_dpis->get_array()){
		$S_GUIDE_ID = $data[PD_GUIDE_ID];
		$cmd1 = " SELECT * FROM PER_DEVELOPE_PLAN WHERE PD_PLAN_KF_ID = $KF_ID AND PD_GUIDE_ID=$S_GUIDE_ID ";
		$db_dpis2->send_cmd($cmd1);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		if ($data2) {
			$fANSWER = 1;
		} else {
			$fANSWER = 0;
		}
		$arr_content[$data_count][guide_id] = $S_GUIDE_ID;
		$arr_content[$data_count][description1] = $data[PD_GUIDE_DESCRIPTION1];
		$arr_content[$data_count][description2] = $data[PD_GUIDE_DESCRIPTION2];
		$arr_content[$data_count][fanswer] = $fANSWER;
	//	echo "$GUIDE_ID,$data[PD_GUIDE_DESCRIPTION1],$data[PD_GUIDE_DESCRIPTION2],$fANSWER,$planStartDate,$planEndDate<br>";
		$data_count++;
	} // end while
// end Read Plan Guide List สำหรับ CP_CODE และ GUIDE_LEVEL ที่ต้องการ
?>