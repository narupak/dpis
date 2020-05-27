<?
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_USER = 99999;

	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "|#|";
	$path_toshow = "C:\\dpis\\personal_data\\";
	$path_tosave = (trim($path_tosave))? $path_tosave : $path_toshow;
	$path_tosave_tmp = str_replace("\\", "\\\\", $path_tosave);
	$path_toshow = $path_tosave;
	if(is_dir($path_toshow) == false) {
		mkdir($path_toshow, 0777);
	}

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
		$cmd = " ALTER TABLE PER_TRAINING DISABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT DISABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL DISABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS DISABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE DISABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL DISABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS DISABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE DISABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS DISABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P DISABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E DISABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C DISABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB DISABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ DISABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER DISABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE DISABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS DISABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR DISABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS DISABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS DISABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC DISABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS DISABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS DISABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE DISABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS DISABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT DISABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY DISABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL DISABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL DISABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR DISABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS DISABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS DISABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI DISABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$table = array( "per_absenthis", "per_marrhis", "per_timehis", "per_ability", "per_rewardhis", "per_extrahis", "per_extra_incomehis", "per_namehis", 
			"per_punishment", "per_decoratehis", "per_training", "per_educate", "per_salaryhis", "per_positionhis", "per_personalpic", "per_heir", "per_servicehis", 
			"per_comdtl", "per_bonuspromote", "per_bonusquotadtl2", "per_bonusquotadtl1", "per_bonusquota", "per_special_skill", "per_coursedtl", "per_decordtl", "per_org_job", 
			"per_promote_p", "per_letter", "per_move_req", "per_scholar", "per_absent", "per_invest1dtl", "per_promote_c", "per_address", "per_kpi_form", 
			"per_salpromote", "per_family", "per_personal", "per_salquotadtl1", "per_salquotadtl2", "per_salquota", "per_command", "per_decor", 
			"per_order_dtl", "per_req3_dtl" );
		for ( $i=0; $i<count($table); $i++ ) { 
			$cmd = " delete from $table[$i] where per_id in (select per_id from PER_PERSONAL where DEPARTMENT_ID = $SESS_DEPARTMENT_ID) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end for

		$cmd = " select ORG_CODE from PER_ORG where ORG_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$ORG_CODE = $data2[ORG_CODE];

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_PERSONAL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " delete from PER_POS_MOVE where POS_ID in (select POS_ID from PER_POSITION where DEPARTMENT_ID = $SESS_DEPARTMENT_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_POSITION' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " delete from PER_POSITION where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_POSITION where DEPARTMENT_ID not in (select ORG_ID from PER_ORG) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

//		$cmd = " delete from PER_POS_EMP where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
//		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

//		$cmd = " delete from PER_POS_EMPSER where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
//		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " delete from PER_POSITIONHIS where ORG_ID_3 in (select ORG_ID from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update PER_ORG set DEPARTMENT_ID = NULL where ORG_ID = 1 or OL_CODE in ('01', '02') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_ORG' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " delete from PER_ORG where DEPARTMENT_ID not in (select ORG_ID from PER_ORG) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ORG where ORG_ID_REF in (SELECT ORG_ID from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ORG where DEPARTMENT_ID = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ORG where ORG_ID_REF in (SELECT ORG_ID from PER_ORG where ORG_ID_REF = $SESS_DEPARTMENT_ID) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " delete from PER_ORG where ORG_ID_REF  = $SESS_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_MGT' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " DELETE FROM PER_MAP_CODE WHERE MAP_CODE = 'PER_SKILL' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " update PER_AMPHUR set AP_CODE = '1401' where AP_CODE = '1400' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " update PER_MARRIED set MR_NAME = 'หย่า/ร้าง' where MR_CODE = '3' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MARRIED (MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE)
						VALUES ('4', 'หม้าย', 1, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " INSERT INTO PER_MARRIED (MR_CODE, MR_NAME, MR_ACTIVE, UPDATE_USER, UPDATE_DATE)
						VALUES ('9', 'ไม่ระบุ', 1, $UPDATE_USER, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		$cmd = " select * from PER_AMPHUR where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_AMPHUR);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_AMPHUR", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_AMPHUR ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_ORG_TYPE where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_ORG_TYPE);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_ORG_TYPE", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_ORG_TYPE ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_ORG_PROVINCE where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_ORG_PROVINCE);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_ORG_PROVINCE", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_ORG_PROVINCE ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_ORG where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_ORG);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} else {
			for($i=0; $i<=count($field_list); $i++) : 
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
			$OL_CODE = trim($data[OL_CODE]);	
			$ORG_ID_REF = $data[ORG_ID_REF];	
			unset($data[ORG_ID], $field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				if ($key!='OP_CODE' && $key!='OS_CODE') {
					if (($key=='ORG_CODE' || $key=='ORG_NAME' || $key=='ORG_SHORT' || $key=='OL_CODE' || $key=='OT_CODE' || $key=='OP_CODE' || $key=='OS_CODE' || 
						$key=='ORG_ADDR1' || $key=='ORG_ADDR2' || $key=='ORG_ADDR3' || $key=='AP_CODE' || $key=='PV_CODE' || $key=='CT_CODE'  || $key=='ORG_DATE' || 
						$key=='ORG_JOB' || $key=='UPDATE_DATE' || $key=='ORG_WEBSITE') && $fieldvalue!='NULL') 	$fieldvalue = $fieldvalue;
					if ($key=='DEPARTMENT_ID') $fieldvalue = $SESS_DEPARTMENT_ID;
					if ($key=='ORG_ID_REF') { 
						if ($OL_CODE=="'03'") 
							$fieldvalue = $SESS_DEPARTMENT_ID;
						else {
							$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$ORG_ID_REF' ";
							$db_dpis2->send_cmd($cmd);
							$data2 = $db_dpis2->get_array();
							$fieldvalue = $data2[NEW_CODE];
						}
					}
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
					if ( in_array($key, array_keys($arrfield_except_per_org)) )		
						$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_org[$key] : $arrfield_except_per_org[$key];
					else
						$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
	//				echo "$field_value<br>";
				}	
			}	
			$cmd = "	INSERT INTO PER_ORG ( ORG_ID, $field_name ) VALUES ( $MAX_ID, $field_value )";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br><u>PER_ORG:</u> <b>$MAX_ID.</b> $cmd<br>===================<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_ORG', '$ORG_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$MAX_ID++;
		}  // end while 

		$cmd = " select * from PER_CO_LEVEL where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_CO_LEVEL);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_CO_LEVEL", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_CO_LEVEL ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_CONDITION where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_CONDITION);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_CONDITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_CONDITION ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_TYPE where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_TYPE);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_TYPE", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_TYPE ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_MGT where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_MGT);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_MGT", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			$PM_CODE = $data[PM_CODE];	
			$PM_NAME = $data[PM_NAME];	

			$cmd = " select PM_CODE from PER_MGT where PM_NAME = $PM_NAME or PM_CODE = $PM_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MAP_PM_CODE = trim($data2[PM_CODE]);

			if ($MAP_PM_CODE) {
	 			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_MGT', $PM_CODE, '$MAP_PM_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			}

			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_MGT ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_SKILL where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_SKILL);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_SKILL", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			$SKILL_CODE = $data[SKILL_CODE];	
			$SKILL_NAME = $data[SKILL_NAME];	

			$cmd = " select SKILL_CODE from PER_SKILL where SKILL_NAME = $SKILL_NAME or SKILL_CODE = $SKILL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$MAP_SKILL_CODE = trim($data2[SKILL_CODE]);

			if ($MAP_SKILL_CODE) {
	 			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
								VALUES ('PER_SKILL', $SKILL_CODE, '$MAP_SKILL_CODE', $UPDATE_USER, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			}

			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_SKILL ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_POSITION where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_POSITION);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$cmd = " select max(POS_ID) as MAX_ID from PER_POSITION ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MAX_ID = $data2[MAX_ID] + 1;
		$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

		$db_textfile = new connect_file("PER_POSITION", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			// ลบค่าข้อมูล field POS_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			$POS_ID = $data[POS_ID];	
			$TEMP_PM_CODE = $data[PM_CODE];	
			$TEMP_SKILL_CODE = $data[SKILL_CODE];	
			$TEMP_ORG_ID = $data[ORG_ID];	
			$TEMP_ORG_ID_1 = $data[ORG_ID_1];	
			$TEMP_ORG_ID_2 = $data[ORG_ID_2];	
			unset($data[POS_ID], $field_name, $field_value);
			unset($data[DEPARTMENT_ID], $field_name, $field_value);

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_MGT' AND OLD_CODE = $TEMP_PM_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_PM_CODE = $data2[NEW_CODE];
			if (!$TMP_PM_CODE) $TMP_PM_CODE = $TEMP_PM_CODE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_SKILL' AND OLD_CODE = $TEMP_SKILL_CODE ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_SKILL_CODE = $data2[NEW_CODE];
			if (!$TMP_SKILL_CODE) $TMP_SKILL_CODE = $TEMP_SKILL_CODE;

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$TEMP_ORG_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$TEMP_ORG_ID_1' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_ID_1 = $data2[NEW_CODE];
			if (!$TMP_ORG_ID_1) $TMP_ORG_ID_1 = 'NULL';

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_ORG' AND OLD_CODE = '$TEMP_ORG_ID_2' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TMP_ORG_ID_2 = $data2[NEW_CODE];
			if (!$TMP_ORG_ID_2) $TMP_ORG_ID_2 = 'NULL';
//echo "$TEMP_ORG_ID-$TMP_ORG_ID-$TEMP_ORG_ID_1-$TMP_ORG_ID_1-$TEMP_ORG_ID_2-$TMP_ORG_ID_2<br>";		
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				if ($key=='ORG_FLAG_KPI' || $key=='ORG_ID_KPI' || $key=='ORG_FLAG_SALARY' || $key=='ORG_ID_SALARY')
					$err=$err;
				else {
					if ($key=='PM_CODE' && $fieldvalue!="NULL") $fieldvalue = " '$TMP_PM_CODE' ";
					if ($key=='SKILL_CODE' && $fieldvalue!="NULL") $fieldvalue = $TMP_SKILL_CODE;
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
			}	
			$cmd = "	INSERT INTO PER_POSITION ( POS_ID, DEPARTMENT_ID, $field_name ) 
								VALUES ( $MAX_ID, $DEPARTMENT_ID, $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "<br><u>PER_POSITION:</u><b>$POS_ID.</b> $cmd<br>===================<br>";

			$cmd = " INSERT INTO PER_MAP_CODE (MAP_CODE, OLD_CODE, NEW_CODE, UPDATE_USER, UPDATE_DATE)
							VALUES ('PER_POSITION', '$POS_ID', '$MAX_ID', $UPDATE_USER, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$MAX_ID++;
		}  // end while 

		$cmd = " select * from PER_PRENAME where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_PRENAME);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_PRENAME", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_PRENAME ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$cmd = " select * from PER_MOVMENT where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_MOVMENT);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if

		$db_textfile = new connect_file("PER_MOVMENT", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			unset($field_name, $field_value);
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {	
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
//			echo "$field_value<br>";
			}	
			$cmd = "	INSERT INTO PER_MOVMENT ( $field_name ) VALUES ( $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
		}  // end while 

		$table = array(	"PER_POSITIONHIS", "PER_SALARYHIS", "PER_EXTRAHIS", 
				"PER_EDUCATE", "PER_TRAINING", "PER_ABILITY", 
				"PER_SPECIAL_SKILL", "PER_HEIR", "PER_ABSENTHIS", 
				"PER_PUNISHMENT", "PER_SERVICEHIS", "PER_REWARDHIS", 
				"PER_MARRHIS", "PER_NAMEHIS", "PER_DECORATEHIS", 
				"PER_TIMEHIS", "PER_EXTRA_INCOMEHIS");

		// =====================================================
		// ===== นำข้อมูลเข้า PER_PERSONAL ก่อน table อื่น =====
		// ===== select ชื่อ fields จาก $table ===== 
		$cmd = " select * from PER_PERSONAL where rownum = 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields($table);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		}else{
			for($i=0; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if
		//echo "<pre>".print_r($field_list)."</pre>";
//		echo "<br>=>".count($arr_fields)."+".count($arr_fields_type)."<br>";
		// =====================================================
		// ===== insert into PER_PERSONAL =====
		$loop=1;
		$cmd = " select max(PER_ID) as MAX_ID from PER_PERSONAL ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		//$PER_ID= $data2[MAX_ID] + 1;
		$LAST_PER_ID= $data2[MAX_ID];
		$DEPARTMENT_ID = $SESS_DEPARTMENT_ID;

		$db_textfile = new connect_file("PER_PERSONAL", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
		while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			$TEMP_PER_NAME = trim($data[PER_NAME]);	
			$TEMP_POS_ID = $data[POS_ID];	
			$TEMP_PAY_ID = $data[PAY_ID];	
			$ctext = count($data);
			$oldperid=$data[PER_ID];
			$PER_ID[$oldperid]=($LAST_PER_ID+$loop);
			//echo "<br>old: $oldperid - new:  $PER_ID[$oldperid]<br> ";

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$TEMP_POS_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TEMP_POS_ID = $data2[NEW_CODE];

			$cmd = " select NEW_CODE from PER_MAP_CODE where MAP_CODE = 'PER_POSITION' AND OLD_CODE = '$TEMP_PAY_ID' ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$TEMP_PAY_ID = $data2[NEW_CODE];

			// ลบค่าข้อมูล field PER_ID ที่ได้จาก textfile ออก เพื่อใส่ค่า max ตัวใหม่
			//ถ้า unset เท่ากับลบ field หายไป 3 fields และใน $field_name, $field_value ค่าเหล่านี้จะหายไปด้วย
			unset($data[PER_ID], $field_name, $field_value);
			unset($data[DEPARTMENT_ID], $field_name, $field_value);
			///unset($data[PER_OFFNO], $field_name, $field_value);   //field นี้จะหายและไม่ครบในการ insert ซึ่งมันเป็น filed อยู่กลาง
			$PER_NAME = str_replace("'", "", $data[PER_NAME]) ." ". str_replace("'", "", $data[PER_SURNAME]);
			
			// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields
			foreach ($data as $key => $fieldvalue) {
				//echo "<br>$key => $fieldvalue<br>";
				$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
				if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
					$fieldvalue = date("Y-m-d H:i:s");
					$fieldvalue = "'$fieldvalue'";
				}
				if (!$TEMP_POS_ID) $TEMP_POS_ID = "NULL";
				if (!$TEMP_PAY_ID) $TEMP_PAY_ID = "NULL";
				if ($key=='POS_ID') 	$fieldvalue = $TEMP_POS_ID;
				if ($key=='PAY_ID') 	$fieldvalue = $TEMP_PAY_ID;
				if ($key=='ORG_ID') 	$fieldvalue = "NULL";
				$field_name .= (trim($field_name) != "")? ", " . $key : $key;
				// ทดสอบว่าเป็น field ที่ไม่ต้องการ insert ค่าลงใน table ใช่หรือไม่
				if (in_array($key, array_keys($arrfield_except_per_personal))){
					$field_value .= (trim($field_value) != "")? ", " . $arrfield_except_per_personal[$key] : $arrfield_except_per_personal[$key];
				}else{
					$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
				}
			}	
			//$cmd = "	INSERT INTO PER_PERSONAL ( PER_ID, DEPARTMENT_ID, $field_name ) VALUES ( $PER_ID[$loop], $DEPARTMENT_ID, $field_value )";	
			$cmd = "	INSERT INTO PER_PERSONAL ( PER_ID, DEPARTMENT_ID, $field_name ) VALUES ($PER_ID[$oldperid], $DEPARTMENT_ID, $field_value )";	
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			$atestfield_name=explode(",",$field_name);
			$atestfield_value=explode(",",$field_value);
			//+2 fields คือ PER_ID/DEPARTMENT_ID ที่ถูก unset ไป และเอาค่าใหม่ใส่เข้ามา เพราะมันอยู่หลัง function นี้
			$cloop = count($data)+2; $nfield_name=count($atestfield_name)+2;	$nfield_value=count($atestfield_value)+2;
//			echo "<br><u>PER_PERSONAL:</u>[".$ctext."-".$cloop."-".$nfield_name."+".$nfield_value."] <b>$j.</b> $cmd<br>===================<br>";
		//$PER_ID++;	//***********ถ้าบวก PER_ID เพิ่มไป 1 เวลา insert query ถัดไป PER_ID ถูกเพิ่มด้วย และจะไม่ตรงกัน 
			$loop++;
		}  // end while 
		// ===== end นำข้อมูลเข้า PER_PERSONAL ก่อน =====
		// =====================================================

		// =========================================================================
		// ===== วนลูปตาม array table ที่เหลือโดยอิง PER_ID กับ PER_PERSONAL ที่ insert ด้านบ้าน =====
		//print_r($PER_ID);
		for ( $i=0; $i<count($table); $i++ ) { 
			//echo "<b>:: $table[$i] :: </b><br>";
			// ===== select ชื่อ fields จาก $table ===== 
			$cmd = " select * from $table[$i] where rownum = 1 ";
			$db_dpis->send_cmd($cmd);
			$field_list = $db_dpis->list_fields($table);
			// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
			unset($arr_fields);
			if ($DPISDB=="odbc" || $DPISDB=="oci8") {
				for($j=1; $j<=count($field_list); $j++) : 
					$arr_fields[] = $tmp_name = $field_list[$j]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$j]["type"];
				endfor;
			}else{
				for($j=0; $j<=count($field_list); $j++) : 
					$arr_fields[] = $tmp_name = $field_list[$j]["name"];
					$arr_fields_type[$tmp_name] = $field_list[$j]["type"];
				endfor;
			} // end if

			// ===== นำข้อมูล fields จาก textfile write ลง db
			$db_textfile = new connect_file("$table[$i]", "r", $DIVIDE_TEXTFILE, "$path_tosave_tmp");
			while ($data = $db_textfile -> get_array_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
				$oldperid = $data[PER_ID];
//				echo "<br>old: $oldperid- new:  $PER_ID[$oldperid]<br> ";
				switch($table[$i]){
					case "PER_POSITIONHIS" :
						$cmd = " select max(POH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$data2 = $db_dpis2->get_array();
						$POH_ID = $data2[MAX_ID] + 1;

						$key_POH_ID = array_search('POH_ID', $arr_fields); 
						unset($arr_fields[$key_POH_ID], $data[PER_ID], $data[POH_ID], $data[ORG_ID_1], $data[ORG_ID_2], $data[ORG_ID_3]);
						$insert_field = "PER_ID, POH_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ";
						$insert_value = "$PER_ID[$oldperid], $POH_ID, $DEPARTMENT_ID, $DEPARTMENT_ID, $DEPARTMENT_ID, ";	
						break;
					case "PER_SALARYHIS" :
						$cmd = " select max(SAH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SAH_ID = $data2[MAX_ID] + 1;
				
						$key_SAH_ID = array_search('SAH_ID', $arr_fields); 
						unset($arr_fields[$key_SAH_ID], $data[PER_ID], $data[SAH_ID]);
						$insert_field = "PER_ID, SAH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SAH_ID, ";	
						break;
					case "PER_EXTRAHIS" :
						$cmd = " select max(EXH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();
						$EXH_ID = $data2[MAX_ID] + 1;
				
						$key_EXH_ID = array_search('EXH_ID', $arr_fields); 
						unset($arr_fields[$key_EXH_ID], $data[PER_ID], $data[EXH_ID]);
						$insert_field = "PER_ID, EXH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EXH_ID, ";		
						break;
					case "PER_EDUCATE" :
						$cmd = " select max(EDU_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$EDU_ID = $data2[MAX_ID] + 1;
	
						$key_EDU_ID = array_search('EDU_ID', $arr_fields); 
						unset($arr_fields[$key_EDU_ID], $data[PER_ID], $data[EDU_ID]);		
						$insert_field = "PER_ID, EDU_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EDU_ID, ";	
						break;
					case "PER_TRAINING" :
						$cmd = " select max(TRN_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$db_dpis2->get_array();
						$TRN_ID = $data2[MAX_ID] + 1;
				
						$key_TRN_ID = array_search('TRN_ID', $arr_fields); 
						unset($arr_fields[$key_TRN_ID], $data[PER_ID], $data[TRN_ID]);		
						$insert_field = "PER_ID, TRN_ID, ";
						$insert_value = "$PER_ID[$oldperid], $TRN_ID, ";	
						break;
					case "PER_ABILITY" :
						$cmd = " select max(ABI_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$ABI_ID = $data2[MAX_ID] + 1;
				
						$key_ABI_ID = array_search('ABI_ID', $arr_fields); 
						unset($arr_fields[$key_ABI_ID], $data[PER_ID], $data[ABI_ID]);	
						$insert_field = "PER_ID, ABI_ID, ";
						$insert_value = "$PER_ID[$oldperid], $ABI_ID, ";	
						break;
					case "PER_SPECIAL_SKILL" :
						$cmd = " select max(SPS_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SPS_ID = $data2[MAX_ID] + 1;

						$key_SPS_ID = array_search('SPS_ID', $arr_fields); 
						unset($arr_fields[$key_SPS_ID], $data[PER_ID], $data[SPS_ID]);
						$insert_field = "PER_ID, SPS_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SPS_ID, ";	
						break;
					case "PER_HEIR" :
						$cmd = " select max(HEIR_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$HEIR_ID = $data2[MAX_ID] + 1;
				
						$key_HEIR_ID = array_search('HEIR_ID', $arr_fields); 
						unset($arr_fields[$key_HEIR_ID], $data[PER_ID], $data[HEIR_ID]);
						$insert_field = "PER_ID, HEIR_ID, ";
						$insert_value = "$PER_ID[$oldperid], $HEIR_ID, ";	
						break;
					case "PER_ABSENTHIS" :
						$cmd = " select max(ABS_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$ABS_ID = $data2[MAX_ID] + 1;
				
						$key_ABS_ID = array_search('ABS_ID', $arr_fields); 
						unset($arr_fields[$key_ABS_ID], $data[PER_ID], $data[ABS_ID]);
						$insert_field = "PER_ID, ABS_ID, ";
						$insert_value = "$PER_ID[$oldperid], $ABS_ID, ";	
						break;
					case "PER_PUNISMENT" :
						$cmd = " select max(PUN_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$PUN_ID = $data2[MAX_ID] + 1;
				
						$key_PUN_ID = array_search('PUN_ID', $arr_fields); 
						unset($arr_fields[$key_PUN_ID], $data[PER_ID], $data[PUN_ID]);	
						$insert_field = "PER_ID, PUN_ID, ";
						$insert_value = "$PER_ID[$oldperid], $PUN_ID, ";
						break;
					case "PER_SERVICEHIS" :
						$cmd = " select max(SRH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$SRH_ID = $data2[MAX_ID] + 1;
				
						$key_SRH_ID = array_search('SRH_ID', $arr_fields); 
						unset($arr_fields[$key_SRH_ID], $data[PER_ID], $data[SRH_ID]);
						$insert_field = "PER_ID, SRH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $SRH_ID, ";
						break;
					case "PER_REWARDHIS" :
						$cmd = " select max(REH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$REH_ID = $data2[MAX_ID] + 1;
				
						$key_REH_ID = array_search('REH_ID', $arr_fields); 
						unset($arr_fields[$key_REH_ID], $data[PER_ID], $data[REH_ID]);
						$insert_field = "PER_ID, REH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $REH_ID, ";				
						break;
					case "PER_MARRHIS" :
						$cmd = " select max(MAH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$MAH_ID = $data2[MAX_ID] + 1;
				
						$key_MAH_ID = array_search('MAH_ID', $arr_fields); 
						unset($arr_fields[$key_MAH_ID], $data[PER_ID], $data[MAH_ID]);	
						$insert_field = "PER_ID, MAH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $MAH_ID, ";
						break;
					case "PER_NAMEHIS" :
						$cmd = " select max(NH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$NH_ID = $data2[MAX_ID] + 1;
				
						$key_NH_ID = array_search('NH_ID', $arr_fields); 
						unset($arr_fields[$key_NH_ID], $data[PER_ID], $data[NH_ID]);
						$insert_field = "PER_ID, NH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $NH_ID, ";
						break;
					case "PER_DECORATEHIS" :
						$cmd = " select max(DEH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$DEH_ID = $data2[MAX_ID] + 1;
				
						$key_DEH_ID = array_search('DEH_ID', $arr_fields); 
						unset($arr_fields[$key_DEH_ID], $data[PER_ID], $data[DEH_ID]);
						$insert_field = "PER_ID, DEH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $DEH_ID, ";
						break;
					case "PER_TIMEHIS" :
						$cmd = " select max(TIMEH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$TIMEH_ID = $data2[MAX_ID] + 1;
				
						$key_TIMEH_ID = array_search('TIMEH_ID', $arr_fields); 
						unset($arr_fields[$key_TIMEH_ID], $data[PER_ID], $data[TIMEH_ID]);
						$insert_field = "PER_ID, TIMEH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $TIMEH_ID, ";				
						break;
					case "PER_EXTRA_INCOMEHIS" :
						$cmd = " select max(EXINH_ID) as MAX_ID from $table[$i] ";
						$db_dpis2->send_cmd($cmd);
						//$db_dpis2->show_error();				
						$data2 = $db_dpis2->get_array();
						$EXINH_ID = $data2[MAX_ID] + 1;
				
						$key_EXINH_ID = array_search('EXINH_ID', $arr_fields); 
						unset($arr_fields[$key_EXINH_ID], $data[PER_ID], $data[EXINH_ID]);
						$insert_field = "PER_ID, EXINH_ID, ";
						$insert_value = "$PER_ID[$oldperid], $EXINH_ID, ";
						break;				
				} // end switch case
		
				// ===== วนลูปให้ชื่อ fields ตรงกับค่าของ fields ตาม texfile 
				unset ($field_name, $field_value);
				foreach ($data as $key => $fieldvalue) {	
					$fieldvalue = str_replace("<br>", "\n", $fieldvalue);
					if ($key=='UPDATE_DATE' && $fieldvalue=='NULL') {
						$fieldvalue = date("Y-m-d H:i:s");
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='MAH_MARRY_DATE' || $key=='MV_DATE' || $key=='POH_EFFECTIVEDATE' || $key=='POH_DOCDATE' || $key=='SAH_EFFECTIVEDATE' || 			
						$key=='SAH_DOCDATE') && $fieldvalue=='NULL') {
						$fieldvalue = "1957-01-01";
						$fieldvalue = "'$fieldvalue'";
					}
					if (($key=='POH_REMARK' || $key=='POH_POS_NO' || $key=='POH_DOCNO' || $key=='SAH_DOCNO') && $fieldvalue=='NULL') {
						$fieldvalue = "-";
						$fieldvalue = "'$fieldvalue'";
					}
					$field_name .= (trim($field_name) != "")? ", " . $key : $key;
					$field_value .= (trim($field_value) != "")? ", " . $fieldvalue : $fieldvalue;
				}		
				// ===== insert ข้อมูลลง database =====
				$cmd = "	INSERT INTO $table[$i] ( $insert_field $field_name ) VALUES ( $insert_value $field_value )";
				$db_dpis->send_cmd($cmd);		
//			$db_dpis->show_error();
//			echo "<br><u>$table[$i]:</u><b>$j.</b> $cmd<br>===================<br>";	
			}  // end while 
		} 	// endif for ($i=0; $i<=count($table); $i++)

		unset($data, $arr_fields, $field_name, $field_value);

		$cmd = " ALTER TABLE PER_TRAINING ENABLE CONSTRAINTS FK1_PER_TRAINING ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PUNISHMENT ENABLE CONSTRAINTS FK1_PER_PUNISHMENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORDTL ENABLE CONSTRAINTS FK3_PER_DECORDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_DECORATEHIS ENABLE CONSTRAINTS FK1_PER_DECORATEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSE ENABLE CONSTRAINTS FK4_PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_COURSEDTL ENABLE CONSTRAINTS FK2_PER_COURSEDTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK1_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SERVICEHIS ENABLE CONSTRAINTS FK5_PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALPROMOTE ENABLE CONSTRAINTS FK2_PER_SALPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SALARYHIS ENABLE CONSTRAINTS FK1_PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_P ENABLE CONSTRAINTS FK2_PER_PROMOTE_P ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_E ENABLE CONSTRAINTS FK2_PER_PROMOTE_E ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PROMOTE_C ENABLE CONSTRAINTS FK1_PER_PROMOTE_C ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ORG_JOB ENABLE CONSTRAINTS FK1_PER_ORG_JOB ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MOVE_REQ ENABLE CONSTRAINTS FK1_PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK1_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_LETTER ENABLE CONSTRAINTS FK2_PER_LETTER ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_BONUSPROMOTE ENABLE CONSTRAINTS FK2_PER_BONUSPROMOTE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_TIMEHIS ENABLE CONSTRAINTS FK1_PER_TIMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_SCHOLAR ENABLE CONSTRAINTS FK1_PER_SCHOLAR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_REWARDHIS ENABLE CONSTRAINTS FK1_PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_POSITIONHIS ENABLE CONSTRAINTS FK1_PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_PERSONALPIC ENABLE CONSTRAINTS FK1_PER_PERSONALPIC ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_NAMEHIS ENABLE CONSTRAINTS FK1_PER_NAMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_MARRHIS ENABLE CONSTRAINTS FK1_PER_MARRHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EDUCATE ENABLE CONSTRAINTS FK1_PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENTHIS ENABLE CONSTRAINTS FK1_PER_ABSENTHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABSENT ENABLE CONSTRAINTS FK1_PER_ABSENT ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_ABILITY ENABLE CONSTRAINTS FK1_PER_ABILITY ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST2DTL ENABLE CONSTRAINTS FK2_PER_INVEST2DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_INVEST1DTL ENABLE CONSTRAINTS FK2_PER_INVEST1DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_HEIR ENABLE CONSTRAINTS FK1_PER_HEIR ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRA_INCOMEHIS ENABLE CONSTRAINTS FK1_PER_EXTRA_INCOMEHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_EXTRAHIS ENABLE CONSTRAINTS FK1_PER_EXTRAHIS ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

		$cmd = " ALTER TABLE PER_KPI ENABLE CONSTRAINTS FK1_PER_KPI ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error() ;

	} // endif command==CONVERT
?>