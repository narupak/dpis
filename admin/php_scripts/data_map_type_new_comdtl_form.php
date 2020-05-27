<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ ตัวล่าสุด
		$CMD_SEQ = $CMD_SEQ_LAST + 1;	
	}	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	
	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE =  save_date($CMD_DATE);
		
		$POS_ID = $POEM_ID = $POEMS_ID = "";
		$PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = "";
		$PL_CODE = trim($PL_PN_CODE);
		$PL_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
		$POS_ID = trim($POS_POEM_ID);
		
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		$CMD_SALARY = str_replace(",", "", $CMD_SALARY) + 0;	
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);
		
		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
		$PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
		$POS_ID = trim($POS_ID)? $POS_ID : "NULL";
		$POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
		$POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
		$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
		$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
		$EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
		$PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
		$PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
		$EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";		
	} // end if
	
	if($command=="ADD"){
		$cmd = " insert into PER_COMDTL
					(	COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
						CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
						CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
						POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
						PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
						CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
						PER_CARDNO, UPDATE_USER, UPDATE_DATE, CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
				 values
					(	$COM_ID, $CMD_SEQ, $PER_ID, '$EN_CODE', '$CMD_DATE', '$CMD_POSITION', 
						'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
						$CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
						$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
						$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
						'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2',  0, 
						$PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', '$CMD_LEVEL1', '$CMD_POSPOEM_NO_NAME', '$CMD_POSPOEM_NO') ";
		$db_dpis->send_cmd($cmd);
		//__$db_dpis->show_error();
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งจัดคนลงตาม พรบ.ใหม่ [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
		$cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								CMD_EDUCATE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION', 
								CMD_ORG1 = '$CMD_ORG1', 
								CMD_ORG2 = '$CMD_ORG2', 
								CMD_ORG3 = '$CMD_ORG3', 
								CMD_ORG4 = '$CMD_ORG4', 
								CMD_ORG5 = '$CMD_ORG5', 
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
								PN_CODE = $PN_CODE, 
								EP_CODE = $EP_CODE, 
								CMD_AC_NO = '$CMD_AC_NO', 
								CMD_ACCOUNT = '$CMD_ACCOUNT', 
								POS_ID = $POS_ID, 
								POEM_ID = $POEM_ID, 
								POEMS_ID = $POEMS_ID, 
								LEVEL_NO = $LEVEL_NO, 
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = 0, 
								PL_CODE_ASSIGN = $PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN = $PN_CODE_ASSIGN, 
								EP_CODE_ASSIGN = $EP_CODE_ASSIGN, 
								CMD_NOTE1 = '$CMD_NOTE1', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_DATE2 = '$CMD_DATE2', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								CMD_POS_NO_NAME = '$CMD_POSPOEM_NO_NAME', 
								CMD_POS_NO = '$CMD_POSPOEM_NO', 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_LEVEL_POS='$CMD_LEVEL1'
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งจัดคนลงตาม พรบ.ใหม่ [ $COM_ID : $PER_ID ]");
		
		$UPD_PER="";		$VIEW_PER="";
		// ให้ย้อนกลับไป หน้าที่ 2 
		$show_topic = 2;	
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งจัดคนลงตาม พรบ.ใหม่ [ $COM_ID : $PER_ID ]");		
	} // end if

	// ค้นหาเพื่อแสดงผลข้อมูล	
	if( ($PAGE_AUTH["add"] || $UPD || $VIEW) && trim($COM_ID) && trim($CMD_SEQ) ){
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		//if($PAGE_AUTH["add"] && trim($CMD_SEQ_LAST)){	//กรณีเพิ่มใหม่เอาข้อมูลก่อนหน้ามาแสดง เฉพาะวันที่แต่งตั้ง และประเภทความเคลื่อนไหว ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			//$CMD_SEQ = $CMD_SEQ_LAST;
		//}
		$cmd = "	select	CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
							CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
							PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO 
					from	PER_COMDTL
					where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $POS_POEM_ID = trim($data[POS_ID]);
		$PER_ID = trim($data[PER_ID]);
		$PL_CODE = trim($data[PL_CODE]); 		
		$EN_CODE = trim($data[CMD_EDUCATE]);
		$MOV_CODE = trim($data[MOV_CODE]); 
		$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 
		$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]);
		$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]);
		$CMD_POSITION = trim($data[CMD_POSITION]);
		$CMD_ORG3 = trim($data[CMD_ORG3]); 
		$CMD_ORG4 = trim($data[CMD_ORG4]); 
		$CMD_ORG5 = trim($data[CMD_ORG5]); 
		$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
		$CMD_SALARY = number_format(trim($data[CMD_SALARY]), 2, '.', ','); 
		$LEVEL_NO = trim($data[LEVEL_NO]); 
		$LEVEL_NO_show = level_no_format($LEVEL_NO);	
		$CMD_LEVEL = level_no_format($data[CMD_LEVEL]); 
		$CMD_LEVEL_full = substr("00".trim($data[CMD_LEVEL]), 1, 2);  
		$CMD_LEVEL3 = level_no_format($data[CMD_LEVEL_POS]);
		$CMD_LEVEL_full1 = substr("00".trim($data[CMD_LEVEL_POS]), 1, 2);  
		$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
		$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
		$CMD_DATE = show_date_format($data[CMD_DATE], 1);

		if ($CMD_LEVEL) {
			$cmd = " select LEVEL_NO from PER_LEVEL where LEVEL_NO < '$CMD_LEVEL_full' order by LEVEL_NO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$LEVEL_NO_prev = trim($data1[LEVEL_NO]);
			$LEVEL_NO_prev_show = level_no_format($LEVEL_NO_prev);			
		}
		
		if (trim($LEVEL_NO_prev)) { 
			$selected_1 = ($CMD_LEVEL != $LEVEL_NO_show)? "selected" : "";
			$selected_2 = ($CMD_LEVEL == $LEVEL_NO_show)? "selected" : "";			
			$LEVEL_NO_list = "	<option value='$LEVEL_NO_prev' $selected_1>$LEVEL_NO_prev_show</option><option value='$CMD_LEVEL_full' $selected_2>$CMD_LEVEL</option> ";
		} elseif (!trim($LEVEL_NO_prev)) { 
			$LEVEL_NO_list = "	<option value='$CMD_LEVEL_full' selected>$CMD_LEVEL</option> ";
		}
		$LEVEL_NO_list = "";
		
		// สร้าง select option ของ LEVEL_NO จาก table per_map_type (MySQL)
		$cmd = " select distinct PT_CODE_N from per_map_type ";
		$db->send_cmd($cmd);
		while ($data_my = $db->get_array()) {
			$num++;
			$level_no_new = $data_my[PT_CODE_N];
			$selected = ($LEVEL_NO==$level_no_new)? "selected" : "";
			$LEVEL_NO_list.= " <option value='$level_no_new' $selected>$level_no_new</option>";
		}

		if ($PER_ID > 900000000) {
			$PER_CARDNO = '';
			$PER_NAME = 'ตำแหน่งว่าง';
			$PER_BIRTHDATE = '';
			$PER_TYPE = 1;
		} else {
			$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
						  from 	PER_PERSONAL a, PER_PRENAME b 
						  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);
			$data_dpis2 = $db_dpis1->get_array();
			$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
			$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
			$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
			$PER_TYPE = trim($data_dpis2[PER_TYPE]);
		}
			
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_CODE' ";			
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$POS_POEM_NAME = trim($data1[PL_NAME]);
		
		$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";			
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
		$PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);

		$cmd = " 	select 	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, a.DEPARTMENT_ID 
						from 		PER_POSITION a, PER_LINE b 
						where 	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE";
		$db_dpis1->send_cmd($cmd);	
		$data1 = $db_dpis1->get_array();
		$POS_POEM_NO_NAME = trim($data1[POS_NO_NAME]);
		$POS_POEM_NO = trim($data1[POS_NO]);

		$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
		$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
		$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($DTLFORM_ORG_ID)			$tmp_ORG_ID[] =  $DTLFORM_ORG_ID;
		if ($DTLFORM_ORG_ID_1)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_1;
		if ($DTLFORM_ORG_ID_2)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_2;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
		while ( $data1 = $db_dpis->get_array() ) {
			$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG3 = ($DTLFORM_ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($DTLFORM_ORG_ID_1 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($DTLFORM_ORG_ID_2 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
		}	// while
			
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";	
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MOV_NAME = trim($data1[MOV_NAME]);	
	
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MINISTRY_ID = $data1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];
	} 	// 	if($COM_ID){	
?>