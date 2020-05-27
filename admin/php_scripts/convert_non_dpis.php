<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
	$path_toshow = "C:\\compensation\\";
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;

	if ($DPISDB == "odbc") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	} elseif ($DPISDB == "oci8") {
		$TYPE_TEXT_STR = array("VARCHAR", "VARCHAR2", "CHAR");
		$TYPE_TEXT_INT = array("NUMBER");
	} elseif ($DPISDB == "mysql") {
		$TYPE_TEXT_STR = array("VARCHAR", "MEMO", "LONGCHAR", "TEXT");
		$TYPE_TEXT_INT = array("INTEGER", "INTEGER2", "SMALLINT", "SINGLE", "DOUBLE", "REAL", "NUMBER");
	}
	// =======================================================

	if ($command=="CONVERT") { 
//		$arrfield_except_per_org = array( 
//			"ORG_ID" => "NULL", 
//			"ORG_WEBSITE" => "NULL");

		// =====================================================
		// ===== select ชื่อ fields จาก $table ===== 
		if ($STP || $HIPPS) {
			$cmd = " delete from PER_POSITION where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " delete from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID and ORG_ACTIVE = 1 ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " update PER_ORG set ORG_WEBSITE = NULL ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$cmd = " select * from PER_ORG ";
			$db_dpis->send_cmd($cmd);
			$field_list = $db_dpis->list_fields(PER_ORG);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql") {
				for($i=1; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			} // end if

			$cmd = " select max(ORG_ID) as MAX_ID from PER_ORG ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MAX_ID = $data2[MAX_ID] + 1;

			$db_textfile = new connect_file("PER_ORG", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				// ลบค่าข้อมูล field ORG_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
				$ORG_ID = $data[ORG_ID];	
				$OL_CODE = $data[OL_CODE];	
				$ORG_ID_REF = $data[ORG_ID_REF];	
				unset($data[ORG_ID], $field_name, $field_value);
				// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
				foreach ($data as $key => $fieldvalue) {	
					if (($key=='ORG_CODE' || $key=='ORG_NAME' || $key=='ORG_SHORT' || $key=='OL_CODE' || $key=='OT_CODE' || $key=='OP_CODE' || $key=='OS_CODE' || 
					$key=='ORG_ADDR1' || $key=='ORG_ADDR2' || $key=='ORG_ADDR3' || $key=='AP_CODE' || $key=='PV_CODE' || $key=='CT_CODE'  || $key=='ORG_DATE' || 
					$key=='ORG_JOB' || $key=='UPDATE_DATE' || $key=='ORG_WEBSITE') && $fieldvalue!='NULL') 	$fieldvalue = $fieldvalue;
					if ($key=='ORG_WEBSITE') 	$fieldvalue = "'$ORG_ID'";
					if ($OL_CODE=='03' && $key=='ORG_ID_REF') $fieldvalue = $SESS_DEPARTMENT_ID;
					if ($OL_CODE > "03" && $key=='ORG_ID_REF') {
						$cmd = " select ORG_ID from PER_ORG where ORG_WEBSITE = $ORG_ID_REF ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$fieldvalue = $data2[ORG_ID];
					}
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
					if ( in_array($key, array_keys($arrfield_except_per_org)) )		
						$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_org[$key] : $arrfield_except_per_org[$key];
					else
						$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
					echo "$field_value<br>";
				}	
				$cmd = "	INSERT INTO PER_ORG ( ORG_ID, $field_name ) VALUES ( $MAX_ID, $field_value )";	
				$db_dpis->send_cmd($cmd);
				$db_dpis->show_error();
				echo "<br><b>$MAX_ID.</b> $cmd<br>===================<br>";
				$MAX_ID++;
			}  // end while 

			$cmd = " select * from PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			$field_list = $db_dpis->list_fields(PER_POSITION);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql") {
				for($i=1; $i<=count($field_list); $i++) : 
					$arr_fields[] = $tmp_name = $field_list[$i]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
				endfor;
			} // end if

			$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$POS_ID = $data2[MAX_ID] + 1;
			$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

			$db_textfile = new connect_file("PER_POSITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				// ลบค่าข้อมูล field POS_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
				$CL_NAME = "'$data[CL_NAME]'";	
				$LV_NAME = $data[LV_NAME];	
				$POS_NO_INT = $data[POS_NO] + 0;	
				$TEMP_ORG_ID = $data[ORG_ID];	
				$TEMP_ORG_ID_1 = $data[ORG_ID_1];	
				$TEMP_ORG_ID_2 = $data[ORG_ID_2];	
				unset($data[POS_ID], $field_name, $field_value);
				unset($data[DEPARTMENT_ID], $field_name, $field_value);
				unset($data[POS_NO_INT], $field_name, $field_value);

				$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$TMP_ORG_ID = $data2[ORG_ID];
				if ($TEMP_ORG_ID_1!='NULL') {
					$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID_1 ";
					$db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$TMP_ORG_ID_1 = $data2[ORG_ID];
				} else $TMP_ORG_ID_1 = 'NULL';
				if ($TEMP_ORG_ID_2!='NULL') {
					$cmd = " select ORG_ID from  PER_ORG where ORG_WEBSITE = $TEMP_ORG_ID_2 ";
					$db_dpis2->send_cmd($cmd);
//					$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$TMP_ORG_ID_2 = $data2[ORG_ID];
				} else $TMP_ORG_ID_2 = 'NULL';
//echo "$TEMP_ORG_ID-$TMP_ORG_ID-$TEMP_ORG_ID_1-$TMP_ORG_ID_1-$TEMP_ORG_ID_2-$TMP_ORG_ID_2<br>";		
				// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
				foreach ($data as $key => $fieldvalue) {	
					if ($key=='LV_NAME') 	
						if ($LV_NAME=='M1') $fieldvalue = "SES1";
						elseif ($LV_NAME=='M2') $fieldvalue = "SES2";
						elseif ($LV_NAME=='D1') $fieldvalue = "M1";
						elseif ($LV_NAME=='D2') $fieldvalue = "M2";
					if (($key=='CL_NAME' || $key=='POS_NO' || $key=='OT_CODE' || $key=='LV_NAME' || $key=='SKILL_CODE' || $key=='PT_CODE' || $key=='PC_CODE' || $key=='PL_CODE' || 
					$key=='PM_CODE' || $key=='POS_DATE' || $key=='POS_GET_DATE' || $key=='POS_CHANGE_DATE' || $key=='UPDATE_DATE'  || $key=='POS_CONDITION' || 
					$key=='POS_DOC_NO' || $key=='POS_REMARK' || $key=='LEVEL_NO' || $key=='POS_PER_NAME') && $fieldvalue!='NULL') 	$fieldvalue = "'$fieldvalue'";
					if ($key=='ORG_ID') 	$fieldvalue = $TMP_ORG_ID;
					if ($key=='ORG_ID_1') 	$fieldvalue = $TMP_ORG_ID_1;
					if ($key=='ORG_ID_2') 	$fieldvalue = $TMP_ORG_ID_2;
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
					if ( in_array($key, array_keys($arrfield_except_per_position)) )		
						$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_position[$key] : $arrfield_except_per_position[$key];
					else
						$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
				}	
				$cmd = "	INSERT INTO PER_POSITION ( POS_ID, DEPARTMENT_ID, POS_NO_INT, $field_name ) 
									VALUES ( $POS_ID, $DEPARTMENT_ID, $POS_NO_INT, $field_value )";	
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
//				echo "<br><b>$POS_ID.</b> $cmd<br>===================<br>";
				$POS_ID++;
			}  // end while 
		}  // end if 

		$arrfield_except_per_personal = array( 
			"ORG_ID" => "NULL", 
			"POS_ID" => "NULL", 
			"POEM_ID" => "NULL", 
			"POEMS_ID" => "NULL" );

		// =====================================================
		// ===== นำข้อมูลเข้า PER_PERSONAL ก่อน table อื่น =====
		// ===== select ชื่อ fields จาก $table ===== 
		$cmd = " select * from PER_PERSONAL ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields($table);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if
		// =====================================================
		// ===== insert into PER_PERSONAL =====
		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PER_ID = $data2[MAX_ID] + 1;
		$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

		$db_textfile = new connect_file("PER_PERSONAL", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			// ลบค่าข้อมูล field PER_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			unset($data[PER_ID], $field_name, $field_value);
			unset($data[DEPARTMENT_ID], $field_name, $field_value);
			unset($data[PER_OFFNO], $field_name, $field_value);
			$PER_NAME = str_replace("'", "", $data[PER_NAME]) ." ". str_replace("'", "", $data[PER_SURNAME]);	
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
				if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
					$fieldvalue = date("Y-m-d H:i:s");
					$fieldvalue = "'$fieldvalue'";
				}
				if (($key=='MAH_MARRY_DATE' || $key=='MV_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || $key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
					$fieldvalue = "1957-01-01";
					$fieldvalue = "'$fieldvalue'";
				}
				if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
					$fieldvalue = "-";
					$fieldvalue = "'$fieldvalue'";
				}
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
				if ( in_array($key, array_keys($arrfield_except_per_personal)) )		
					$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_personal[$key] : $arrfield_except_per_personal[$key];
				else
					$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
			}	
			$cmd = "	INSERT INTO PER_PERSONAL ( PER_ID, DEPARTMENT_ID, $field_name ) VALUES ( $PER_ID, $DEPARTMENT_ID, $field_value )";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br><b>$j.</b> $cmd<br>===================<br>";
		}  // end while 
		// ===== end นำข้อมูลเข้า PER_PERSONAL ก่อน =====
		// =====================================================

		// =========================================================================
		// ===== วนลูปตาม array table ที่เหลือโดยอิง PER_ID กับ PER_PERSONAL ที่ insert ด้านบ้าน =====
		unset($data, $arr_fields, $field_name, $field_value);
	} // endif command==CONVERT
?>