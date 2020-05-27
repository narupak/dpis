<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
	$path_toshow = "C:\\dpis\\jethro_data\\";
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;
	if(is_dir("$path_toshow") == false) {
		mkdir("$path_toshow", 0777);
	}

	if ($DPISDB == "odbc") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	} elseif ($DPISDB == "oci8") {
		$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
		$TYPE_TEXT_INT = array("NUMBER");
	}elseif($DPISDB=="mysql"){
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	}
	// =======================================================

	if ($command=="CONVERT" && $COM_ID) { 
		$cmd = " select * from per_position ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(per_position);

		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($j=1; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		}else{
			for($j=0; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		} // end if
		$arr_fields[] = "LV_NAME|#|LEVEL_NO|#|POS_PER_NAME|#|";

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("per_position", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, c.PL_CODE_ASSIGN, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID, c.LEVEL_NO as LV_NAME, b.LEVEL_NO, 
							  d.PN_NAME&trim(b.PER_NAME)&' '&trim(b.PER_SURNAME) 
							  from ((PER_POSITION a
								         left join PER_PERSONAL b on (a.POS_ID = b.POS_ID)
										) left join PER_COMDTL c on (a.POS_ID = c.POS_ID)
										) left join PER_PRENAME d on (b.PN_CODE = d.PN_CODE)  
							  where  c.COM_ID = $COM_ID and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, c.PL_CODE_ASSIGN, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID, c.LEVEL_NO as LV_NAME, b.LEVEL_NO, 
							  d.PN_NAME||trim(b.PER_NAME)||' '||trim(b.PER_SURNAME) 
							  from PER_POSITION a, PER_PERSONAL b, PER_COMDTL c, PER_PRENAME d 
							  where a.POS_ID = b.POS_ID(+) and a.POS_ID = c.POS_ID(+) and c.COM_ID = $COM_ID and b.PN_CODE = d.PN_CODE(+) and POS_STATUS=1 
							 order by to_number(replace(a.POS_NO,'-','')) ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";

		$db_textfile = new connect_file("per_position", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		$cmd = " select * from per_org ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(per_org);

		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($j=1; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		}else{
			for($j=0; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		} // end if

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("per_org", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_ORG where DEPARTMENT_ID = $DEPARTMENT_ID and ORG_ACTIVE = 1 order by OL_CODE, ORG_CODE ";
		$count = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$db_textfile = new connect_file("per_org", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));
/*
		$cmd = " select * from per_formula ";
		$db->send_cmd($cmd);
		$field_list = $db->list_fields(per_formula);

		unset($arr_fields);
		for($j=0; $j<=count($field_list); $j++) :
			$arr_fields[] = $field_list[$j]["name"];
		endfor;

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("per_formula", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		if ($DPISDB == "odbc") {
			$cmd = " select b.PER_ID, b.LEVEL_NO, a.PL_CODE, a.PT_CODE, c.LEVEL_NO as PT_CODE_N, a.POS_ID, a.POS_NO, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, 
							  b.PER_ENG_NAME, b.PER_ENG_SURNAME, a.OT_CODE, b.PER_CARDNO, b.PER_OFFNO, b.PER_BIRTHDATE, b.PER_STARTDATE, b.PER_OCCUPYDATE, 
							  b.PER_RETIREDATE, b.PER_GENDER, b.PER_TYPE, b.PER_STATUS, a.POS_SALARY, a.POS_MGTSALARY, a.POS_DATE, 
							  a.POS_GET_DATE, a.POS_CHANGE_DATE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.CL_NAME, a.SKILL_CODE, d.EN_CODE, d.EM_CODE, 
							  e.PV_CODE, e.CT_CODE, f.ABI_DESC, a.UPDATE_USER, a.UPDATE_DATE, 'A'
							  from ((((PER_POSITION a
										left join PER_PERSONAL b on (a.POS_ID = b.POS_ID)
										) left join PER_COMDTL c on (a.POS_ID = c.POS_ID) 
										) left join PER_EDUCATE d on (b.PER_ID = d.PER_ID)
										) left join PER_ORG e on (a.ORG_ID = e.ORG_ID) 
										) left join PER_ABILITY f on (b.PER_ID = f.PER_ID)
							  where c.COM_ID = $COM_ID and d.EDU_TYPE like '%2%' and a.POS_STATUS=1
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " select b.PER_ID, b.LEVEL_NO, a.PL_CODE, a.PT_CODE, c.LEVEL_NO as PT_CODE_N, a.POS_ID, a.POS_NO, b.PN_CODE, b.PER_NAME, b.PER_SURNAME, 
							  b.PER_ENG_NAME, b.PER_ENG_SURNAME, a.OT_CODE, b.PER_CARDNO, b.PER_OFFNO, b.PER_BIRTHDATE, b.PER_STARTDATE, b.PER_OCCUPYDATE, 
							  b.PER_RETIREDATE, b.PER_GENDER, b.PER_TYPE, b.PER_STATUS, a.POS_SALARY, a.POS_MGTSALARY, a.POS_DATE, 
							  a.POS_GET_DATE, a.POS_CHANGE_DATE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.CL_NAME, a.SKILL_CODE, d.EN_CODE, d.EM_CODE, 
							  e.PV_CODE, e.CT_CODE, f.ABI_DESC, a.UPDATE_USER, a.UPDATE_DATE, 'A'
							  from PER_POSITION a, PER_PERSONAL b, PER_COMDTL c, PER_EDUCATE d, PER_ORG e, PER_ABILITY f 
							  where a.POS_ID = b.POS_ID and a.POS_ID = c.POS_ID(+) and c.COM_ID = $COM_ID and b.PER_ID = d.PER_ID(+) and d.EDU_TYPE like '%2%' and 
							  a.ORG_ID = e.ORG_ID(+) and b.PER_ID = f.PER_ID(+) and a.POS_STATUS=1
							 order by to_number(replace(a.POS_NO,'-','')) ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";

		$db_textfile = new connect_file("per_formula", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		$cmd = " select * from per_map_position ";
		$db->send_cmd($cmd);
		$field_list = $db->list_fields(per_map_position);

		unset($arr_fields);
		for($j=0; $j<=count($field_list); $j++) :
			$arr_fields[] = $field_list[$j]["name"];
		endfor;

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("per_map_position", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select a.POS_ID, a.POS_NO, c.LEVEL_NO as PT_CODE_N, a.OT_CODE, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, 
									   d.LEVEL_NO_MIN, a.POS_SALARY, a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.POS_DATE, a.POS_GET_DATE, a.POS_CHANGE_DATE, 
									   a.POS_STATUS, b.PER_ID, b.LEVEL_NO, a.UPDATE_USER, a.UPDATE_DATE, 'A'
						  from PER_POSITION a, PER_PERSONAL b, PER_COMDTL c, PER_CO_LEVEL d 
						  where a.POS_ID = b.POS_ID(+) and a.POS_ID = c.POS_ID(+) and c.COM_ID = $COM_ID and a.CL_NAME = d.CL_NAME(+) and POS_STATUS=1
						 order by to_number(replace(a.POS_NO,'-','')) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";

		$db_textfile = new connect_file("per_map_position", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));
*/
//		unset($arr_fields, $data, $arr_data);
	} // endif command==CONVERT && COM_ID

	if(!$current_page) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$tmp_COM_DATE =  save_date($COM_DATE);
	//$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
						VALUES 	($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', 1, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
		// ===== เริ่มต้น insert ข้อมูลจาก PER_POSITION เพื่อจัดคนลง พรบ. ใหม่ เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		$cmd = " select a.POS_ID, a.POS_NO_NAME, a.POS_NO, a.PL_CODE, b.PL_NAME, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, a.PT_CODE, b.PL_TYPE, 
				 a.CL_NAME, a.POS_SALARY, b.PL_CODE_NEW, b.PL_CODE_DIRECT 
				 from PER_POSITION a, PER_LINE b 
				 where a.POS_STATUS = 1 and a.PL_CODE=b.PL_CODE and a.DEPARTMENT_ID = $DEPARTMENT_ID 
				order by a.POS_ID ";				  
		$count_temp = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$POS_ID = $data[POS_ID];
			$cmd = " select PER_ID, PER_SALARY, LEVEL_NO as PER_LEVEL, PER_CARDNO
					 from PER_PERSONAL where POS_ID=$POS_ID and PER_STATUS = 1 ";				  
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_NO_NAME = trim($data[POS_NO_NAME]);
			$POS_NO = trim($data[POS_NO]);
			$PL_CODE = trim($data[PL_CODE]);
			$PL_NAME = trim($data[PL_NAME]);
			$PT_CODE = trim($data[PT_CODE]);					
			$PL_TYPE = $data[PL_TYPE];
			$CL_NAME = trim($data[CL_NAME]);
			$POS_SALARY = $data[POS_SALARY];
			$PL_CODE_NEW = trim($data[PL_CODE_NEW]);
			$PL_CODE_DIRECT = trim($data[PL_CODE_DIRECT]);
			$CMD_POSITION = $PL_NAME;
			$ORG_ID_1 = (trim($data[ORG_ID]))? trim($data[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;

			$cmd_seq++;
			$TMP_PER_ID = trim($data2[PER_ID]);
			$CMD_OLD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SALARY = trim($data2[PER_SALARY]);
			$CMD_SPSALARY = 0;
			$TMP_SALP_LEVEL = trim($data2[PER_LEVEL]);
			$PER_CARDNO = trim($data2[PER_CARDNO]);
			// 215=ประเภทย้าย
			if($BKK_FLAG==1) $MOV_CODE = "40";
			else $MOV_CODE = "103";
			//$tmp_date = explode("-", substr(trim($data[SALP_DATE]), 0, 10));
			$CMD_DATE = trim($tmp_COM_DATE);
				
			$CMD_LEVEL = $data2[PER_LEVEL];
			$POS_ID = (trim($POS_ID))? $POS_ID : 'NULL';
			$TMP_PER_ID = (trim($TMP_PER_ID))? $TMP_PER_ID : $POS_ID+900000000;
			$POEM_ID = 'NULL';
			$POEMS_ID = 'NULL';		
			$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
			$PL_CODE_NEW = trim($PL_CODE_NEW)? "'".$PL_CODE_NEW."'" : "NULL";
			$PL_CODE_DIRECT = trim($PL_CODE_DIRECT)? "'".$PL_CODE_DIRECT."'" : "NULL";
			$PN_CODE = $EP_CODE = "NULL";
			$PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";	
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}
				
			$cmd = " select EN_CODE from PER_EDUCATE where PER_ID=$TMP_PER_ID and EDU_TYPE like '%1%' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_CODE = (trim($data2[EN_CODE]))? "'".$data2[EN_CODE]."'" : "NULL";
				
			if ($TMP_PER_ID > 900000000){
				$cmd = " select LEVEL_NO_MIN, LEVEL_NO_MAX from PER_CO_LEVEL where CL_NAME='$CL_NAME' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				if ($SELECT_LEVEL_NO == 11)
					$CMD_LEVEL = trim($data2[LEVEL_NO_MAX]);
				else
					$CMD_LEVEL = trim($data2[LEVEL_NO_MIN]);
				$CMD_OLD_SALARY = $POS_SALARY;
				$CMD_SALARY = $POS_SALARY;
			}	
				
			$cmd = " select NEW_LEVEL_NO from PER_MAP_POS 
						   where LEVEL_NO='$CMD_LEVEL' and PL_TYPE=$PL_TYPE ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$LEVEL_NO = $data1[NEW_LEVEL_NO];

			if ($PL_TYPE==2 || $PL_TYPE==3 || $PL_TYPE==5) 
				if ($CMD_LEVEL=="08" && $PT_CODE=="31") {
					$LEVEL_NO = "D1";
					$PL_CODE_NEW = ($PL_CODE_DIRECT!="NULL")? $PL_CODE_DIRECT : "'510308'";
				} elseif ($CMD_LEVEL=="09" && $PT_CODE=="32") {
					$LEVEL_NO = "D2";
					$PL_CODE_NEW = ($PL_CODE_DIRECT!="NULL")? $PL_CODE_DIRECT : "'510308'";
				}
			$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";

			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
							CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
							PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
							PER_CARDNO, CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE )
							values ($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
							'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
							$CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
							$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
							$PL_CODE_NEW, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
							'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2', 0, 
							'$PER_CARDNO', '$POS_NO_NAME', '$POS_NO', $SESS_USERID, '$UPDATE_DATE' ) ";			  
			$db_dpis1->send_cmd($cmd);
//				echo "$cmd<br>==================<br>";
//				$db_dpis1->show_error();
//				echo "<br>end ". ++$i  ."=======================<br>";
		}	// end while
		
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $ADD_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้จัดคนลงตาม พรบ. ใหม่ เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )


	if( $command == "UPDATE" && trim($COM_ID) ) {

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$tmp_COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	

// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		include ("php_scripts/data_map_type_new_comdtl_confirm_check.php");	
		if (!trim($error_move_personal)) {
			include ("php_scripts/data_map_type_new_comdtl_confirm.php");
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where COM_ID=$COM_ID  ";
			$db_dpis->send_cmd($cmd);		

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		} elseif (trim($error_move_personal)) {
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=0, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where COM_ID=$COM_ID  ";
			$db_dpis->send_cmd($cmd);		
		}
	}		// 	if( $command == "COMMAND" && trim($COM_ID) ) 	

// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
		
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID)){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO."]");
		$COM_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, 
						a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID 
				from		PER_COMMAND a, PER_COMTYPE b
				where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
	}


	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
	
		$SELECT_LEVEL_NO = 11;		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
		$search_per_name = "";
		$search_per_surname = "";	

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
	} // end if		
?>