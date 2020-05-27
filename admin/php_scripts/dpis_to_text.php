<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/load_per_control.php");	
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";

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

	if ($command=="CONVERT") { 
		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " ALTER TABLE  PER_PRENAME DROP CONSTRAINT INXU2_PER_PRENAME ";
		elseif($DPISDB=="oci8") 
			$cmd = " DROP INDEX INXU2_PER_PRENAME ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$path_tosave = "C:\\dpis\\";
		$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_AMPHUR where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_AMPHUR ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_AMPHUR);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_AMPHUR", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_AMPHUR where AP_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_AMPHUR", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_ORG_TYPE where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_ORG_TYPE ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_ORG_TYPE);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_ORG_TYPE", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_ORG_TYPE where OT_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_ORG_TYPE", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from per_org where rownum = 1 ";
		else
			$cmd = " select top 1 * from per_org ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(per_org);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_ORG", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_ORG where DEPARTMENT_ID = $DEPARTMENT_ID and ORG_ACTIVE = 1 order by OL_CODE, ORG_CODE ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_ORG", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_CO_LEVEL where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_CO_LEVEL ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_CO_LEVEL);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_CO_LEVEL", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_CO_LEVEL where CL_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_CO_LEVEL", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_CONDITION where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_CONDITION ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_CONDITION);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_CONDITION", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_CONDITION where PC_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_CONDITION", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_TYPE where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_TYPE ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_TYPE);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_TYPE", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_TYPE where PT_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_TYPE", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_MGT where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_MGT ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_MGT);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_MGT", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_MGT where PM_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_MGT", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_SKILL where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_SKILL ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_SKILL);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_SKILL", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_SKILL where SKILL_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_SKILL", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_PRENAME where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_PRENAME ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_PRENAME);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_PRENAME", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_PRENAME where PN_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_PRENAME", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		if ($DPISDB=="oci8") 
			$cmd = " select * from PER_MOVMENT where rownum = 1 ";
		else
			$cmd = " select top 1 * from PER_MOVMENT ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_MOVMENT);

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

		// ===== เขียนชื่อ fields จาก db write ลง textfile =====
		$db_textfile = new connect_file("PER_MOVMENT", "w", "", "$path_tosave_tmp");
		$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");

		// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
		$fields_select = implode(", ", $arr_fields);
		$cmd = " select $fields_select from PER_MOVMENT where MOV_ACTIVE = 1 ";
		$count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$db_textfile = new connect_file("PER_MOVMENT", "a", "", "$path_tosave_tmp");
		unset($data, $arr_data);
		while ( $data = $db_dpis->get_array() ) {
			$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
		}
		$db_textfile->write_text_data(implode("\n", $arr_data));

		unset($arr_fields, $data, $arr_data);

		$table = array("PER_PERSONAL", "PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", "PER_EDUCATE", 
		"PER_TRAINING", "PER_ABILITY", "PER_SPECIAL_SKILL", "PER_HEIR", "PER_ABSENTHIS", "PER_PUNISHMENT", 
		"PER_SERVICEHIS", "PER_REWARDHIS", "PER_MARRHIS", "PER_NAMEHIS", "PER_DECORATEHIS", 
		"PER_TIMEHIS", "PER_EXTRA_INCOMEHIS");
		// ===== วนลูปตาม array table =====
		$field_list = (array) null;
		for ( $i=0; $i<count($table); $i++ ) { 
			// ===== select ชื่อ fields จาก $table ===== 
			if ($DPISDB=="odbc" || $DPISDB=="mysql") {
				$cmd = " select top 1 * from $table[$i] ";
			} else { // oci8
				$cmd = " select * from $table[$i] where rownum = 1 ";
			}
			$db_dpis->send_cmd($cmd);
			$field_list[$table[$i]] = $db_dpis->list_fields($table[$i]);
		} // end for loop

		$cmd = " select PV_CODE, PV_NAME from PER_PROVINCE WHERE PV_CODE > '1000' ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		while ( $data2 = $db_dpis2->get_array() ) {
			$PV_CODE = $data2[PV_CODE];
			$PV_NAME = $data2[PV_NAME];

			unset($arr_per_id);
			$cmd = " select PER_ID from PER_PERSONAL a, PER_POSITION b, PER_ORG c 
							  where (a.POS_ID = b.POS_ID or a.PAY_ID = b.POS_ID) and b.ORG_ID = c.ORG_ID and a.DEPARTMENT_ID = $DEPARTMENT_ID and c.PV_CODE = '$PV_CODE' ";
			$pv_exist = $db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			if ($pv_exist) {
				while($data = $db_dpis->get_array()){
					$arr_per_id[] = $data[PER_ID];
				}
				$SELECTED_PER_ID = implode(",", $arr_per_id);

				$path_tosave = "C:\\dpis\\$PV_NAME\\";
				$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
				if(is_dir("$path_tosave") == false) {
					mkdir("$path_tosave", 0777);
				}

				// ===== วนลูปตาม array table =====
				for ( $i=0; $i<count($table); $i++ ) { 
					//echo "<b>$i. $table[$i] ::</b><br>";
	
					unset($arr_fields);
					if ($DPISDB=="odbc" || $DPISDB=="oci8") {
						for($j=1; $j<=count($field_list[$table[$i]]); $j++) :
							$arr_fields[] = $field_list[$table[$i]][$j]["name"];
						endfor;
					}else{
						for($j=0; $j<count($field_list[$table[$i]]); $j++) :
							$arr_fields[] = $field_list[$table[$i]][$j]["name"];
						endfor;
					} // end if
					//echo (implode(", ", $arr_fields));
					//echo "<pre>"; print_r($arr_fields); echo "</pre>";

					// ===== เขียนชื่อ fields จาก db write ลง textfile =====
					$db_textfile = new connect_file("$table[$i]", "w", "", "$path_tosave_tmp");
					$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
					// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
					$fields_select = implode(", ", $arr_fields);
					$cmd = " select $fields_select from $table[$i] WHERE PER_ID in ($SELECTED_PER_ID) ";
					$db_dpis->send_cmd($cmd);
					//echo "<br>[".count($arr_fields)."] : $cmd<br><br>";
//					$db_dpis->show_error();
					$db_textfile = new connect_file("$table[$i]", "a", "", "$path_tosave_tmp");
					unset($data, $arr_data);
					while($data = $db_dpis->get_array()){
						$rc = count($data);
						$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
						//echo "<u>$table[$i]</u>[$rc] :  ". implode("$DIVIDE_TEXTFILE", $data) . "<br>===================<br>";
					}
					$db_textfile->write_text_data(implode("\n", $arr_data)); 
				} 	// endif for ($i=0; $i<=count($table); $i++)

				if ($DPISDB=="oci8") 
					$cmd = " select * from PER_POSITION where rownum = 1 ";
				else
					$cmd = " select top 1 * from PER_POSITION ";
				$db_dpis->send_cmd($cmd);
				$field_list1 = $db_dpis->list_fields(PER_POSITION);

				unset($arr_fields);
				if ($DPISDB=="odbc" || $DPISDB=="oci8") {
					for($j=1; $j<=count($field_list1); $j++) :
						$arr_fields[] = $field_list1[$j]["name"];
					endfor;
				}else{
					for($j=0; $j<=count($field_list1); $j++) :
						$arr_fields[] = $field_list1[$j]["name"];
					endfor;
				} // end if

				// ===== เขียนชื่อ fields จาก db write ลง textfile =====
				$db_textfile = new connect_file("PER_POSITION", "w", "", "$path_tosave_tmp");
				$db_textfile->write_text_data(implode("$DIVIDE_TEXTFILE", $arr_fields) . "\n");
	
				// ===== นำข้อมูลแต่ละ fileds จาก db write ลง textfile =====
				$fields_select = implode(", ", $arr_fields);
				$cmd = " select $fields_select from PER_POSITION 
								  where DEPARTMENT_ID = $DEPARTMENT_ID and POS_ID in 
								  (SELECT POS_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) or 
								  POS_ID in (SELECT PAY_ID FROM PER_PERSONAL WHERE PER_ID in ($SELECTED_PER_ID)) or 
								  (ORG_ID in (SELECT ORG_ID FROM PER_ORG WHERE DEPARTMENT_ID = $DEPARTMENT_ID and PV_CODE = '$PV_CODE')) ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				//echo "$count :: $cmd <br>==========<br>";

				$db_textfile = new connect_file("PER_POSITION", "a", "", "$path_tosave_tmp");
				unset($data, $arr_data);
				while ( $data = $db_dpis->get_array() ) {
					$arr_data[] = implode("$DIVIDE_TEXTFILE", str_replace("\n", "<br>", $data));
				}
				$db_textfile->write_text_data(implode("\n", $arr_data));

				unset($arr_fields, $data, $arr_data);

			} // while PV_CODE 
		} // end if ($pv_exist) 
	} // endif command==CONVERT 
?>