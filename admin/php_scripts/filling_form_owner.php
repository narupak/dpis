<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select 	a.ORG_ID, b.ORG_NAME
					 from 	PER_CONTROL a, PER_ORG b
					 where	a.ORG_ID=b.ORG_ID
				   ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_ID = $data[ORG_ID];
	$DEPARTMENT_NAME = $data[ORG_NAME];
	
	if(!$PER_GENDER) $PER_GENDER = 1;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="UPDATE" && $PER_ID){
		$PER_BIRTHDATE =  save_date($PER_BIRTHDATE);
		$PER_STARTDATE =  save_date($PER_STARTDATE);

		$cmd = " update PER_PERSONAL  set
								PN_CODE = trim('$PN_CODE'), 
								PER_NAME = trim('$PER_NAME'), 
								PER_SURNAME = trim('$PER_SURNAME'), 
								PER_GENDER = $PER_GENDER, 
								MR_CODE = trim('$MR_CODE'), 
								PER_CARDNO = trim('$PER_CARDNO'), 
								RE_CODE = trim('$RE_CODE'), 
								PER_BIRTHDATE = '$PER_BIRTHDATE', 
								PER_STARTDATE = '$PER_STARTDATE', 
								PER_ADD2 = trim('$PER_ADD2'), 
								PER_MEMBER = $PER_MEMBER, 
							UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ/ลูกจ้างประจำ [ $PER_ID : $PER_NAME $PER_SURNAME ]");
	} // end if
	
	if($PER_ID){
		$cmd = " select		PER_ID, PER_TYPE, PN_CODE, PER_NAME, PER_SURNAME, 
										PER_GENDER, MR_CODE, PER_CARDNO, RE_CODE, PER_BIRTHDATE, 
										PER_STARTDATE, PER_ADD2, PER_MEMBER 
						 from		PER_PERSONAL 
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_TYPE = trim($data[PER_TYPE]);

		$PN_CODE = trim($data[PN_CODE]);
		$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);		
		$PER_GENDER = trim($data[PER_GENDER]);
		$MR_CODE = trim($data[MR_CODE]);		
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$RE_CODE = trim($data[RE_CODE]);

		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);
		$PER_STARTDATE = show_date_format($data[PER_STARTDATE], 1);

		$cmd = " select ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_POSTCODE 
						from PER_ADDRESS where PER_ID=$PER_ID and ADR_TYPE=1 ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();

		$AP_CODE_ADR = trim($data_dpis1[AP_CODE]);
		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$AP_NAME_ADR = trim($data_dpis2[AP_NAME]);
				
		$PV_CODE_ADR = trim($data_dpis1[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE_ADR' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PV_NAME_ADR = trim($data_dpis2[PV_NAME]);
				
		$PER_ADD2="";
		if($data_dpis1[ADR_VILLAGE]) $PER_ADD2 .= "หมู่บ้าน".$data_dpis1[ADR_VILLAGE]." ";
		if($data_dpis1[ADR_BUILDING]) $PER_ADD2 .= "อาคาร".$data_dpis1[ADR_BUILDING]." ";
		if($data_dpis1[ADR_NO]) $PER_ADD2 .= "เลขที่ ".$data_dpis1[ADR_NO]." ";
		if($data_dpis1[ADR_MOO]) $PER_ADD2 .= "หมู่ที่ ".$data_dpis1[ADR_MOO]." ";
		if($data_dpis1[ADR_SOI]) $PER_ADD2 .= "ซอย".$data_dpis1[ADR_SOI]." ";
		if($data_dpis1[ADR_ROAD]) $PER_ADD2 .= "ถนน".$data_dpis1[ADR_ROAD]." ";
		if($data_dpis1[ADR_DISTRICT]) $PER_ADD2 .= "ตำบล/แขวง ".$data_dpis1[ADR_DISTRICT]." ";
		if($AP_NAME_ADR) $PER_ADD2 .= "อำเภอ/เขต ".$AP_NAME_ADR." ";
		if($PV_NAME_ADR) $PER_ADD2 .= "จังหวัด ".$PV_NAME_ADR." ";
		if($data_dpis1[ADR_POSTCODE]) $PER_ADD2 .= "รหัสไปรษณีย์ ".$data_dpis1[ADR_POSTCODE]." ";
		if (!$PER_ADD2) $PER_ADD2 = trim($data[PER_ADD2]);

		$PER_MEMBER = trim($data[PER_MEMBER]);
		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
		
		$cmd = " select CHILD_ID from PER_CHILD where PER_ID=$PER_ID ";
		$COUNT_CHILD = $db_dpis->send_cmd($cmd);
		if(!$COUNT_CHILD){
			$cmd = " select HEIR_ID, HEIR_NAME, HEIR_BIRTHDAY from PER_HEIR where PER_ID=$PER_ID and HR_CODE='01' order by HEIR_BIRTHDAY ";
			$COUNT_CHILD = $db_dpis->send_cmd($cmd);
			$child_count = 0;
			while($data = $db_dpis->get_array()){
				$child_count++;
				list($HEIR_NAME, $HEIR_SURNAME) = split(" ", $data[HEIR_NAME], 2);
				$HEIR_BIRTHDAY = substr($data[HEIR_BIRTHDAY], 0, 10);
				
				$cmd = " select max(CHILD_ID) as max_id from PER_CHILD ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$CHILD_ID = $data2[max_id] + 1;
				$CHILD_SEQ = str_pad($child_count, 2, "0", STR_PAD_LEFT);				
				
				$cmd = " insert into PER_CHILD 
									(CHILD_ID, PER_ID, CHILD_SEQ, PER_NAME, PER_SURNAME, PER_BIRTHDATE, UPDATE_USER, UPDATE_DATE)
								 values
								 	($CHILD_ID, $PER_ID, '$CHILD_SEQ', '$HEIR_NAME', '$HEIR_SURNAME', '$HEIR_BIRTHDAY', $SESS_USERID, '$UPDATE_DATE')
							   ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end while
		} // end if
	} 	// 	if($PER_ID){
	
?>