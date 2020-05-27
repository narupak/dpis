<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
	$path_toshow = "C:\\dpis35\\jethro_data\\";
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;

	$TYPE_TEXT_STR = array("VARCHAR", "TEXT");
	$TYPE_TEXT_INT = array("INTEGER", "SMALLINT", "DECIMAL");

	if ($command=="CONVERT") { 
		// =====================================================
		// ===== select ชื่อ fields จาก $table ===== 
		$cmd = " delete from PER_MAP_POSITION where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db->send_cmd($cmd);
//		$db->show_error();

		$cmd = " delete from PER_FORMULA where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db->send_cmd($cmd);
//		$db->show_error();

		$cmd = " select * from PER_MAP_POSITION ";
		$db->send_cmd($cmd);
		$field_list = $db->list_fields(PER_MAP_POSITION);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		for($i=0; $i<=count($field_list); $i++) : 
			$arr_fields[] = $tmp_name = $field_list[$i]["name"];
			$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
		endfor;

		$cmd = " select max(POS_ID) as MAX_ID from PER_MAP_POSITION ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		$db_textfile = new connect_file("PER_MAP_POSITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			// ลบค่าข้อมูล field POS_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			$POS_ID = $data[POS_ID];	

			unset($data[POS_ID], $field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				if (($key=='POS_NO' || $key=='PT_CODE_N' || $key=='OT_CODE' || $key=='PM_CODE' || $key=='PL_CODE' || $key=='CL_NAME' || $key=='LEVEL_NO' || 
					$key=='SKILL_CODE' || $key=='PT_CODE' || $key=='POS_DATE' || $key=='POS_GET_DATE' || $key=='POS_CHANGE_DATE' || $key=='PERSON_LEVEL_NO' || 					$key=='UPDATE_DATE' || $key=='INSERT_TYPE') && $fieldvalue!='NULL') 	$fieldvalue = "'$fieldvalue'";
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
			}	
			$cmd = "	INSERT INTO PER_MAP_POSITION ( POS_ID, $field_name ) VALUES ( $MAX_ID, $field_value )";	
			$db->send_cmd($cmd);
			$db->show_error();
			echo "<br><b>$MAX_ID.</b> $cmd<br>===================<br>";
			$MAX_ID++;
		}  // end while 

		$cmd = " select * from PER_FORMULA ";
		$db->send_cmd($cmd);
		$field_list = $db->list_fields(PER_FORMULA);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		for($i=0; $i<=count($field_list); $i++) : 
			$arr_fields[] = $tmp_name = $field_list[$i]["name"];
			$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
		endfor;

		$cmd = " select max(PER_ID) as MAX_ID from PER_FORMULA ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$PER_ID = $data[MAX_ID] + 1;
		$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

		$db_textfile = new connect_file("PER_FORMULA", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			// ลบค่าข้อมูล field PER_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			unset($data[PER_ID], $field_name, $field_value);
			unset($data[DEPARTMENT_ID], $field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				if (($key=='LEVEL_NO' || $key=='PL_CODE' || $key=='PT_CODE' || $key=='PT_CODE_N' || $key=='POS_NO' || $key=='PN_CODE' || $key=='PER_NAME' || 
					$key=='PER_SURNAME' || $key=='PER_ENG_NAME' || $key=='PER_ENG_SURNAME' || $key=='OT_CODE' || $key=='PER_CARDNO' || $key=='PER_OFFNO' || 
					$key=='PER_BIRTHDATE' || $key=='PER_STARTDATE' || $key=='PER_OCCUPYDATE' || $key=='PER_RETIREDATE' || $key=='POS_DATE' || 
					$key=='POS_GET_DATE' || $key=='POS_CHANGE_DATE' || $key=='PM_CODE' || $key=='CL_NAME' || $key=='SKILL_CODE' || $key=='EN_CODE' || $key=='EM_CODE' || 
					$key=='PV_CODE' || $key=='CT_CODE' || $key=='ABILITY' || $key=='UPDATE_DATE' || $key=='INSERT_TYPE') && $fieldvalue!='NULL') 	$fieldvalue = "'$fieldvalue'";
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
			}	
			$cmd = "	INSERT INTO PER_FORMULA ( PER_ID, $field_name ) 
								VALUES ( $PER_ID, $field_value )";	
			$db->send_cmd($cmd);
			$db->show_error();
			echo "<br><b>$PER_ID.</b> $cmd<br>===================<br>";
			$PER_ID++;
		}  // end while 

		unset($data, $arr_fields, $field_name, $field_value);
	} // endif command==CONVERT
?>