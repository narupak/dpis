<?	
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_TN_control.php");
	include("php_scripts/load_per_control.php");

	if($MAIN_VIEW==1) $VIEW = 1;
	if(!$TN_GENDER) $TN_GENDER = 1;
	
//	echo "$CTRL_TYPE :: $SESS_USERGROUP_LEVEL :: $MINISTRY_ID :: $MINISTRY_NAME :: $DEPARTMENT_ID :: $DEPARTMENT_NAME :: $SESS_MINISTRY_NAME :: $SESS_DEPARTMENT_NAME<br>";

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" || $command=="UPDATE"){
		$TRAINNER_NAME = (trim($TRAINNER_NAME))? "'".trim($TRAINNER_NAME)."'" : 'NULL';
		$TN_EDU_HIS1 = (trim($TN_EDU_HIS1))? "'".trim($TN_EDU_HIS1)."'" : 'NULL';
		$TN_EDU_HIS2 = (trim($TN_EDU_HIS2))? "'".trim($TN_EDU_HIS2)."'" : 'NULL';
		$TN_EDU_HIS3 = (trim($TN_EDU_HIS3))? "'".trim($TN_EDU_HIS3)."'" : 'NULL';
		$TN_POSITION = (trim($TN_POSITION))? "'".trim($TN_POSITION)."'" : 'NULL';
		$TN_WORK_PLACE = (trim($TN_WORK_PLACE))? "'".trim($TN_WORK_PLACE)."'" : 'NULL';
		$TN_WORK_TEL = (trim($TN_WORK_TEL))? "'".trim($TN_WORK_TEL)."'" : 'NULL';
		$TN_WORK_EXPERIENCE = (trim($TN_WORK_EXPERIENCE))? "'".trim($TN_WORK_EXPERIENCE)."'" : 'NULL';
		$TN_TRAIN_EXPERIENCE = (trim($TN_TRAIN_EXPERIENCE))? "'".trim($TN_TRAIN_EXPERIENCE)."'" : 'NULL';
		$TN_ADDRESS = (trim($TN_ADDRESS))? "'".trim($TN_ADDRESS)."'" : 'NULL';
		$TN_ADDRESS_TEL = (trim($TN_ADDRESS_TEL))? "'".trim($TN_ADDRESS_TEL)."'" : 'NULL';
		$TN_TECHNOLOGY_HIS = (trim($TN_TECHNOLOGY_HIS))? "'".trim($TN_TECHNOLOGY_HIS)."'" : 'NULL';
		$TN_TRAIN_SKILL1 = (trim($TN_TRAIN_SKILL1))? "'".trim($TN_TRAIN_SKILL1)."'" : 'NULL';
		$TN_TRAIN_SKILL2 = (trim($TN_TRAIN_SKILL2))? "'".trim($TN_TRAIN_SKILL2)."'" : 'NULL';
		$TN_TRAIN_SKILL3 = (trim($TN_TRAIN_SKILL3))? "'".trim($TN_TRAIN_SKILL3)."'" : 'NULL';
		$TN_DEPT_TRAIN = (trim($TN_DEPT_TRAIN))? "'".trim($TN_DEPT_TRAIN)."'" : 'NULL';
		$TN_SPEC_ABILITY = (trim($TN_SPEC_ABILITY))? "'".trim($TN_SPEC_ABILITY)."'" : 'NULL';
		$TN_HOBBY = (trim($TN_HOBBY))? "'".trim($TN_HOBBY)."'" : 'NULL';

		$TN_BIRTHDATE =  save_date($TN_BIRTHDATE);
	} // end if
	
	if($command=="ADD"){
		
		$cmd = " select max(TRAINNER_ID) as max_id from PER_TRAINNER ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);		
		$TRAINNER_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_TRAINNER 
							(	TRAINNER_ID,TRAINNER_NAME, TN_GENDER, TN_INOUT_ORG,TN_BIRTHDATE, TN_EDU_HIS1,TN_EDU_HIS2,TN_EDU_HIS3,
							 TN_POSITION, TN_WORK_PLACE, TN_WORK_TEL, TN_WORK_EXPERIENCE, TN_TRAIN_EXPERIENCE,
							 TN_ADDRESS, TN_ADDRESS_TEL, TN_TECHNOLOGY_HIS, TN_TRAIN_SKILL1, TN_TRAIN_SKILL2, TN_TRAIN_SKILL3,
							 TN_DEPT_TRAIN,TN_SPEC_ABILITY, TN_HOBBY, UPDATE_USER, UPDATE_DATE )
						 values
							(	$TRAINNER_ID, $TRAINNER_NAME, $TN_GENDER, $TN_INOUT_ORG, '$TN_BIRTHDATE', $TN_EDU_HIS1, $TN_EDU_HIS2, $TN_EDU_HIS3,
								 $TN_POSITION, $TN_WORK_PLACE, $TN_WORK_TEL, $TN_WORK_EXPERIENCE, $TN_TRAIN_EXPERIENCE,
								 $TN_ADDRESS, $TN_ADDRESS_TEL, $TN_TECHNOLOGY_HIS, $TN_TRAIN_SKILL1, $TN_TRAIN_SKILL2, $TN_TRAIN_SKILL3,
								 $TN_DEPT_TRAIN, $TN_SPEC_ABILITY, $TN_HOBBY, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูล$PERSON_TITLE [ $TRAINNER_ID : $TRAINNER_NAME ]");
		$UPD = 1;
		$TRAINNER_ID = "";

/*		
		$cmd = " select * from PER_TRAINNER  where TRAINNER_ID = $TRAINNER_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		echo "parent.refresh_opener('2<::><::>')";
		echo "</script>";
*/	
	}
	
	if($command=="UPDATE" && $TRAINNER_ID){
			$cmd = " update PER_TRAINNER  set
									TN_INOUT_ORG = $TN_INOUT_ORG, 
									TRAINNER_NAME = $TRAINNER_NAME, 
									TN_GENDER = $TN_GENDER, 
									TN_BIRTHDATE = '$TN_BIRTHDATE', 
									TN_EDU_HIS1 = $TN_EDU_HIS1,  
									TN_EDU_HIS2 = $TN_EDU_HIS2,  
									TN_EDU_HIS3 = $TN_EDU_HIS3,  
									TN_POSITION = $TN_POSITION,  
									TN_WORK_PLACE = $TN_WORK_PLACE,  
									TN_WORK_TEL = $TN_WORK_TEL,  
									TN_WORK_EXPERIENCE = $TN_WORK_EXPERIENCE,  
									TN_TRAIN_EXPERIENCE = $TN_TRAIN_EXPERIENCE,  
									TN_ADDRESS = $TN_ADDRESS,  
									TN_ADDRESS_TEL = $TN_ADDRESS_TEL,  
									TN_TECHNOLOGY_HIS = $TN_TECHNOLOGY_HIS,  
									TN_TRAIN_SKILL1 = $TN_TRAIN_SKILL1,  
									TN_TRAIN_SKILL2 = $TN_TRAIN_SKILL2,  
									TN_TRAIN_SKILL3 = $TN_TRAIN_SKILL3,  
									TN_DEPT_TRAIN = $TN_DEPT_TRAIN,  
									TN_SPEC_ABILITY = $TN_SPEC_ABILITY, 
									TN_HOBBY = $TN_HOBBY, 
									UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
							 where TRAINNER_ID=$TRAINNER_ID	  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "$cmd<br>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูล$PERSON_TITLE [ $TRAINNER_ID : $TRAINNER_NAME $TN_SURNAME ]");
			$UPD = 1;
	} // end if
	
	if($command=="DELETE" && $TRAINNER_ID){
		$cmd = " delete from PER_TRAINNER where TRAINNER_ID=$TRAINNER_ID ";	
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error;
				
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูล$PERSON_TITLE [ $TRAINNER_ID : $TRAINNER_NAME]");		
		$TRAINNER_ID = "";
	} // end if

	if($TRAINNER_ID && ($UPD || $VIEW)){
		$cmd = "	select	*	from		PER_TRAINNER 	where	TRAINNER_ID=$TRAINNER_ID	";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$TN_INOUT_ORG = trim($data[TN_INOUT_ORG]);

		$TRAINNER_NAME = trim($data[TRAINNER_NAME]);

		$TN_GENDER = trim($data[TN_GENDER]);
		$TN_INOUT_ORG = trim($data[TN_INOUT_ORG]);
		$TN_EDU_HIS1 = trim($data[TN_EDU_HIS1]);
		$TN_EDU_HIS2 = trim($data[TN_EDU_HIS2]);		
		$TN_EDU_HIS3 = trim($data[TN_EDU_HIS3]);
		$TN_POSITION = trim($data[TN_POSITION]);
		$TN_WORK_PLACE = trim($data[TN_WORK_PLACE]);	
		$TN_WORK_TEL = trim($data[TN_WORK_TEL]);
		$TN_WORK_EXPERIENCE = trim($data[TN_WORK_EXPERIENCE]);
		$TN_TRAIN_EXPERIENCE = trim($data[TN_TRAIN_EXPERIENCE]);

		$TN_BIRTHDATE = show_date_format($data[TN_BIRTHDATE], 1);
		$TN_ADDRESS = trim($data[TN_ADDRESS]);
		$TN_ADDRESS_TEL = trim($data[TN_ADDRESS_TEL]);
		$TN_TECHONOLOGY_HIS = trim($data[TN_TECHONOLOGY_HIS]);
		$TN_TRAIN_SKILL1 = trim($data[TN_TRAIN_SKILL1]);
		$TN_TRAIN_SKILL2 = trim($data[TN_TRAIN_SKILL2]);
		$TN_TRAIN_SKILL3 = trim($data[TN_TRAIN_SKILL3]);
		$TN_DEPT_TRAIN = trim($data[TN_DEPT_TRAIN]);
		$TN_SPEC_ABILITY = trim($data[TN_SPEC_ABILITY]);
		$TN_HOBBY = trim($data[TN_HOBBY]);
		$TN_TECHNOLOGY_HIS = trim($data[TN_TECHNOLOGY_HIS]);
	} 	// 	if($TRAINNER_ID)
	
	if(!$TRAINNER_ID){
		$TRAINNER_NAME = "";
		$TN_GENDER=1;
		$TN_INOUT_ORG=0;
		$TN_BIRTHDATE="";
		$TN_EDU_HIS1="";
		$TN_EDU_HIS2="";
		$TN_EDU_HIS3="";
		$TN_POSITION="";
		$TN_WORK_PLACE="";
		$TN_WORK_TEL="";
		$TN_WORK_EXPERIENCE="";
		$TN_TRAIN_EXPERIENCE="";
		$TN_ADDRESS="";
		$TN_ADDRESS_TEL="";
		$TN_TECHNOLOGY_HIS="";
		$TN_TRAIN_SKILL1="";
		$TN_TRAIN_SKILL2="";
		$TN_TRAIN_SKILL3="";
		$TN_DEPT_TRAIN="";
		$TN_SPEC_ABILITY="";
		$TN_HOBBY="";
	}
?>