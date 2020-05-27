<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select a.POS_NO_NAME, a.POS_NO, b.ORG_ID_REF from PER_POSITION a, PER_ORG b where a.ORG_ID=b.ORG_ID and a.POS_ID=$POS_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$POS_NO_NAME = $data[POS_NO_NAME];
	$POS_NO = $data[POS_NO];
	$DEPARTMENT_ID = $data[ORG_ID_REF];
	
	$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];

	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($command=="ADD" || $command=="UPDATE"){
		$POS_DOC_NO = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_DOC_NO)));
		$POS_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_REMARK)));
		$POS_CONDITION = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_CONDITION)));
	
		if(!trim($ORG_ID)) $ORG_ID = "NULL";
		if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
		if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
		if(!trim($ORG_ID_3)) $ORG_ID_3 = "NULL";
		if(!trim($ORG_ID_4)) $ORG_ID_4 = "NULL";
		if(!trim($ORG_ID_5)) $ORG_ID_5 = "NULL";
		$PAY_NO = (trim($PAY_NO))? "'".trim($PAY_NO)."'" : "NULL";
		$PT_CODE = (trim($PT_CODE))? "".$PT_CODE."" : "NULL";
		$PC_CODE = (trim($PC_CODE))? "".$PC_CODE."" : "NULL";
		$PM_CODE = (trim($PM_CODE))? "".$PM_CODE."" : "NULL";
		$PC_CODE = (trim($PC_CODE))? "".$PC_CODE."" : "NULL";
		
		$POS_SALARY += 0;
		$POS_MGTSALARY += 0;

		$POS_DATE =  save_date($POS_DATE);
		
		if(!trim($POS_STATUS)) $POS_STATUS = "NULL";
	} // end if
	
	if($command=="ADD" && trim($POS_DATE)){		
		$cmd = " select POS_ID from PER_POS_MOVE where POS_ID=$POS_ID and POS_DATE='$POS_DATE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){		
			//#######################################################
			//==เครื่องหมายสำคัญ%%%%%%%%%%ถ้าเปลี่ยนแล้วมันอาจจะเพิ่มไม่ได้***************
			if(trim($PM_CODE) && $PM_CODE!="NULL"){	$PM_CODE = "'$PM_CODE'";		}
			$cmd = " insert into PER_POS_MOVE (POS_ID, ORG_ID, OT_CODE, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, 
							PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION,
							POS_DOC_NO, POS_REMARK, POS_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, PAY_NO, POS_ORGMGT, LEVEL_NO )
							 values ($POS_ID, $ORG_ID, '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $PM_CODE, '$PL_CODE', 
							'$CL_NAME', $POS_SALARY, $POS_MGTSALARY, '$SKILL_CODE', $PT_CODE, $PC_CODE, '$POS_CONDITION',
							'$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', $POS_STATUS, 
							$SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID, $PAY_NO, '$POS_ORGMGT', '$LEVEL_NO' ) ";
			//#######################################################				
			$db_dpis->send_cmd($cmd);
			//echo($cmd);
			//$db_dpis->show_error();		//ไม่มีฟิล์ดนี้ POS_NO	, '$POS_NO'
			//die;
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลประวัติตำแหน่ง [ $POS_ID : $POS_DATE ]");
		}else{			
			$err_text = "ประวัติตำแหน่งในวันที่ระบุซ้ำ [".$data[POS_DATE]."]";
			
			$POS_DATE = show_date_format($POS_DATE, 1);
			
			if($ORG_ID == "NULL") $ORG_ID = "";
			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";
		} // end if
	} // end if

	if($command=="UPDATE" && trim($POS_DATE)){
		//#######################################################
		//==เครื่องหมายสำคัญ%%%%%%%%%%ถ้าเปลี่ยนแล้วมันอาจจะเพิ่มไม่ได้***************
		if(trim($PM_CODE) && $PM_CODE!="NULL"){	$PM_CODE = "'$PM_CODE'";		}
		if(trim($PT_CODE) && $PT_CODE!="NULL"){	$PT_CODE = "'$PT_CODE'";		}
		if(trim($PC_CODE) && $PC_CODE!="NULL"){	$PC_CODE = "'$PC_CODE'";		}
		$cmd = " update PER_POS_MOVE  set
							ORG_ID = $ORG_ID, 
							PAY_NO = $PAY_NO,
							OT_CODE = '$OT_CODE', 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ORG_ID_3 = $ORG_ID_3, 
							ORG_ID_4 = $ORG_ID_4, 
							ORG_ID_5 = $ORG_ID_5, 
							PM_CODE =$PM_CODE, 
							PL_CODE = '$PL_CODE', 
							CL_NAME = '$CL_NAME', 
							POS_SALARY = $POS_SALARY, 
							POS_MGTSALARY = $POS_MGTSALARY, 
							SKILL_CODE = '$SKILL_CODE',
							PT_CODE = $PT_CODE, 
							PC_CODE = $PC_CODE, 
							POS_CONDITION = '$POS_CONDITION',
							POS_DOC_NO = '$POS_DOC_NO', 
							POS_REMARK = '$POS_REMARK',
							POS_STATUS = $POS_STATUS, 
							POS_ORGMGT = '$POS_ORGMGT', 
							LEVEL_NO = '$LEVEL_NO', 
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE',
							DEPARTMENT_ID = $DEPARTMENT_ID
						 where POS_ID=$POS_ID and POS_DATE='$POS_DATE' "; 
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "$cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลประวัติตำแหน่ง [ $POS_ID : $POS_DATE ]");
		//ค้างหน้าข้อมูลเดิม
		//$UPD=1;								//ไม่มีฟิล์ดนี้ POS_NO = '$POS_NO',
	} // end if
	
	if($command=="DELETE" && trim($POS_DATE)){
//		$POS_DATE =  save_date($POS_DATE);
		
		 $cmd = " delete from PER_POS_MOVE where POS_ID=$POS_ID and POS_DATE='$POS_DATE' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลประวัติตำแหน่ง [ $POS_ID : $POS_DATE ]");
	} // end if

	if($UPD || $VIEW){
		if(trim($POS_DATE)){
			$cmd = "	select	POS_NO_NAME, POS_NO from PER_POSITION
								where	POS_ID=$POS_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			$POS_NO = trim($data[POS_NO]);
	
			$cmd = "	select	OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE,
												POS_DATE, POS_SALARY, POS_MGTSALARY, PC_CODE, POS_CONDITION,	POS_STATUS, 
												DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_DOC_NO, POS_REMARK, 
												UPDATE_USER, UPDATE_DATE, PAY_NO, POS_ORGMGT, LEVEL_NO
								from		PER_POS_MOVE
								where	POS_ID=$POS_ID and POS_DATE='$POS_DATE' ";
			$db_dpis->send_cmd($cmd);
		//	echo "$cmd<br>";
		//	$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$PAY_NO = trim($data[PAY_NO]);
			
			$OT_CODE = trim($data[OT_CODE]);
			$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$OT_NAME = trim($data_dpis2[OT_NAME]);
	
			$PL_CODE = trim($data[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PL_NAME = trim($data_dpis2[PL_NAME]);
	
			$PM_CODE = trim($data[PM_CODE]);
			$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PM_NAME = trim($data_dpis2[PM_NAME]);
			
			$CL_NAME = trim($data[CL_NAME]);
			$CL_CODE = $CL_NAME;
	
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PT_NAME = trim($data_dpis2[PT_NAME]);
	
			$SKILL_CODE = trim($data[SKILL_CODE]);
			$cmd = " select SKILL_NAME, a.SG_CODE, SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SG_CODE=b.SG_CODE and trim(SKILL_CODE)='$SKILL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$SKILL_NAME = trim($data_dpis2[SKILL_NAME]);
			$SG_CODE = trim($data_dpis2[SG_CODE]);
			$SG_NAME = trim($data_dpis2[SG_NAME]);
			
			$POS_DATE = show_date_format($data[POS_DATE], 1);
	
			$POS_SALARY = trim($data[POS_SALARY]);
			$POS_MGTSALARY = trim($data[POS_MGTSALARY]);
	
			$PC_CODE = trim($data[PC_CODE]);
			$cmd = " select PC_NAME from PER_CONDITION where trim(PC_CODE)='$PC_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$PC_NAME = trim($data_dpis2[PC_NAME]);
	
			$POS_CONDITION = trim($data[POS_CONDITION]);
			$POS_STATUS = trim($data[POS_STATUS]);
	
			$ORG_ID = trim($data[ORG_ID]);
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data_dpis2[ORG_NAME]);
			
			$ORG_ID_1 = trim($data[ORG_ID_1]);
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='04' and ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);
	
			$ORG_ID_2 = trim($data[ORG_ID_2]); 
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='05' and ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);
			
			$POS_DOC_NO = trim($data[POS_DOC_NO]);
			$POS_REMARK = trim($data[POS_REMARK]);
			$POS_ORGMGT = trim($data[POS_ORGMGT]);
			$LEVEL_NO = trim($data[LEVEL_NO]);
	
			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		} // end if
	} // end if
	
	if( (!$UPD && !$VIEW && !$DEL && !$err_text) ){
		$PAY_NO = "";
		$OT_CODE = "";
		$OT_NAME = "";
		$PL_CODE = "";
		$PL_NAME = "";
		$PM_CODE = "";
		$PM_NAME = "";
		$CL_CODE = "";
		$CL_NAME = "";
		$PT_CODE = "";
		$PT_NAME = "";
		$SG_CODE = "";
		$SG_NAME = "";
		$POS_DATE = "";
		$SKILL_CODE = "";
		$SKILL_NAME = "";
		$POS_SALARY = "";
		$POS_MGTSALARY = "";
		$PC_CODE = "";
		$PC_NAME = "";
		$POS_CONDITION = "";
		$POS_STATUS = "";
		$ORG_ID = "";
		$ORG_NAME = "";
		$ORG_ID_1 = "";
		$ORG_NAME_1 = "";
		$ORG_ID_2 = "";
		$ORG_NAME_2 = "";
		$ORG_ID_3 = "";
		$ORG_NAME_3 = "";
		$ORG_ID_4 = "";
		$ORG_NAME_4 = "";
		$ORG_ID_5 = "";
		$ORG_NAME_5 = "";
		$POS_DOC_NO = "";
		$POS_REMARK = "";
		$POS_ORGMGT = "";
		$LEVEL_NO = "";
	} // end if
?>