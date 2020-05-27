<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/load_per_control.php");	
	include("../php_scripts/connect_file.php");
	
	$loan_date_time=date("Ymdhmisa");
	//echo $loan_date_time;

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = ",";
	if ($BKK_FLAG==1)
		$path_toshow = "C:\\portfolio\\personal_data\\";
	else
		$path_toshow = "C:\\dpis\\personal_data\\";
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
//------------------------------------ประเภท-----------------------------------------------------------------------------------------------	
		$arr_search_condition[] = "(b.PER_TYPE = $search_per_type)";
//------------------------------------ประเภท-----------------------------------------------------------------------------------------------	
	if ($command=="CONVERT" ) { 
		$table = array("PER_PERSONAL", "PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", "PER_EDUCATE", "PER_TRAINING", "PER_ABILITY", 
		"PER_SPECIAL_SKILL", "PER_HEIR", "PER_ABSENTHIS", "PER_PUNISHMENT", "PER_SERVICEHIS", "PER_REWARDHIS", "PER_MARRHIS", 
		"PER_NAMEHIS", "PER_DECORATEHIS", "PER_TIMEHIS", "PER_EXTRA_INCOMEHIS");

		// ===== วนลูปตาม array table =====
		for ( $i=0; $i<count($table); $i++ ) { 
			//echo "<b>$i. $table[$i] ::</b><br>";
	
			// ===== select ชื่อ fields จาก $table ===== 
			$cmd = " select * from $table[$i] ";
			$db_dpis->send_cmd($cmd);
			$field_list = $db_dpis->list_fields($table[$i]);
			//echo "<pre>"; print_r($field_list); echo "</pre>";

			// ===== นำชื่อ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($j=1; $j<=count($field_list); $j++) :
					$arr_fields[] = $field_list[$j]["name"];
				endfor;
			}else{
				for($j=0; $j<count($field_list); $j++) :
					$arr_fields[] = $field_list[$j]["name"];
				endfor;
			} // end if
			//echo (implode(", ", $arr_fields));
			//echo "<pre>"; print_r($arr_fields); echo "</pre>";

			// ===== เขียนชื่อ fields จาก db write ลง textfile =====
//			$db_textfile = new connect_file("$table[$i]", "w", "", "$path_tosave_tmp");
	//		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
			// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
			$fields_select = implode(", ", $arr_fields);
			$cmd = " select $fields_select from $table[$i] WHERE PER_ID in ($SELECTED_PER_ID) ";
			$db_dpis->send_cmd($cmd);
//			echo "<br>[".count($arr_fields)."] : $cmd<br><br>";
//			$db_dpis->show_error();
//			$db_textfile = new connect_file("$table[$i]", "a", "", "$path_tosave_tmp");
			unset($data, $arr_data);
			while($data = $db_dpis->get_array()){
				$rc = count($data);
//				$arr_data[] = implode("$DIVIDE_TEXTFILE", $data);
//				echo "<u>$table[$i]</u>[$rc] :  ". implode("$DIVIDE_TEXTFILE", $data) . "<br>===================<br>";
			}
//			$db_textfile->write_text_data(implode("\n", $arr_data));
		} 	// endif for ($i=0; $i<=count($table); $i++)

		$cmd = " select * from per_position where POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
//		$db_textfile = new connect_file("PER_DEBT", "w", "", "$path_tosave_tmp");
	//	$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
	
	$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
	
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where  a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " 								select		b.PER_CARDNO, a.PD_AMOUNT1, a.PD_AMOUNT2, a.PD_AMOUNT3, b.PER_NAME, b.PER_SURNAME, b.PER_TYPE
          																			
								from			PER_DEBTM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and 
												b.POEMS_ID=e.POEMS_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+) and b.POT_ID=g.POT_ID(+)
                                                 and (a.PD_AMOUNT1 > 0 OR a.PD_AMOUNT2 > 0 OR a.PD_AMOUNT3 > 0 )
												 $search_condition";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";


		$db_textfile = new connect_file("loan".$loan_date_time, "a", "", "C:\\appserv\\dpis35_8084\\textfile\\");
		unset($data, $arr_data);

		while ( $data = $db_dpis->get_array() ) {
			$data1[0]= $data[PER_CARDNO];
			$data1[1]= "$data[PER_NAME] $data[PER_SURNAME]";
			$data1[2]= "40603";
			$data1[3]= $data[PD_AMOUNT1];
			$data1[4]= "41101";
			$data1[5]= $data[PD_AMOUNT2];
			$data1[6]= "40313";
			$data1[7]= $data[PD_AMOUNT3];
			$data1[8]= "0,0,0,0,0,0,0,0,0,0,0,0,0,0";
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data1));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));



		unset($arr_fields, $data, $arr_data);
		$SELECTED_PER_ID = "";
	} // endif command==CONVERT && PER_ID
	
	
	
	
	
	
	
	
	
		if ($command=="CONVERT1" ) { 
				if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($j=1; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		}else{
			for($j=0; $j<=count($field_list); $j++) :
				$arr_fields[] = $field_list[$j]["name"];
			endfor;
		} // end if
		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
			$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where  a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " 								select		b.PER_CARDNO, a.PD_AMOUNT1, a.PD_AMOUNT2, a.PD_AMOUNT3, b.PER_NAME, b.PER_SURNAME
          																			
								from			PER_DEBTM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and 
												b.POEMS_ID=e.POEMS_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+) and b.POT_ID=g.POT_ID(+)
                                                 and a.PD_AMOUNT1 > 0  
												 												 $search_condition";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";
		$db_textfile = new connect_file("loan".$loan_date_time, "a", "", "C:\\appserv\\dpis35_8084\\textfile\\");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$data1[0]= $data[PER_CARDNO];
			$data1[1]= "$data[PER_NAME] $data[PER_SURNAME]";
			$data1[2]= "40603";
			$data1[3]= $data[PD_AMOUNT1];
			$data1[4]= "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data1));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));
		unset($arr_fields, $data, $arr_data);
		$SELECTED_PER_ID = "";
		}
	
			if ($command=="CONVERT2" ) { 
				$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where  a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " 								select		b.PER_CARDNO, a.PD_AMOUNT2, b.PER_NAME, b.PER_SURNAME
          																			
								from			PER_DEBTM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and 
												b.POEMS_ID=e.POEMS_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+) and b.POT_ID=g.POT_ID(+)
                                                 and a.PD_AMOUNT2 > 0 
												 												 $search_condition";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";
		$db_textfile = new connect_file("loan".$loan_date_time, "a", "", "C:\\appserv\\dpis35_8084\\textfile\\");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$data1[0]= $data[PER_CARDNO];
			$data1[1]= "$data[PER_NAME] $data[PER_SURNAME]";
			$data1[2]= "41101";
			$data1[3]= $data[PD_AMOUNT2];
			$data1[4]= "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data1));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));
		unset($arr_fields, $data, $arr_data);
		}
		
				if ($command=="CONVERT3" ) { 
					$search_condition = "";
	if(count($arr_search_condition)) $search_condition = " and " . implode(" and ", $arr_search_condition);
		if ($DPISDB == "odbc") {
			$cmd = " select a.POS_ID, a.ORG_ID, a.POS_NO, a.OT_CODE, a.ORG_ID_1, a.ORG_ID_2, a.PM_CODE, a.PL_CODE, a.CL_NAME, a.POS_SALARY, 
							  a.POS_MGTSALARY, a.SKILL_CODE, a.PT_CODE, a.PC_CODE, a.POS_CONDITION, a.POS_DOC_NO, a.POS_REMARK, a.POS_DATE, a.POS_GET_DATE, 
							  a.POS_CHANGE_DATE, a.POS_STATUS, a.UPDATE_USER, a.UPDATE_DATE, a.DEPARTMENT_ID 
							  from PER_POSITION a
							  where  a.POS_ID in (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) and POS_STATUS=1 
							 order by iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
		} elseif ($DPISDB == "oci8") {
			$cmd = " 								select		b.PER_CARDNO, a.PD_AMOUNT3, b.PER_NAME, b.PER_SURNAME
          																			
								from			PER_DEBTM a, PER_PERSONAL b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, 
												PER_LEVEL f, PER_POS_TEMP g
								where		a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID(+) and b.POEM_ID=d.POEM_ID(+) and 
												b.POEMS_ID=e.POEMS_ID(+) and b.LEVEL_NO=f.LEVEL_NO(+) and b.POT_ID=g.POT_ID(+)
                                                 and a.PD_AMOUNT3 > 0  
												 												 $search_condition";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$count :: $cmd <br>==========<br>";
		$db_textfile = new connect_file("loan".$loan_date_time, "a", "", "C:\\appserv\\dpis35_8084\\textfile\\");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$data1[0]= $data[PER_CARDNO];
			$data1[1]= "$data[PER_NAME] $data[PER_SURNAME]";
			$data1[2]= "40313";
			$data1[3]= $data[PD_AMOUNT3];
			$data1[4]= "0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0";
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data1));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));
		unset($arr_fields, $data, $arr_data);
		}

	
?>