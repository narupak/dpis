<?
	include("php_scripts/session_start.php");
	include("../php_scripts/connect_file.php");

	$UPDATE_DATE = date("Y-m-d H:i:s");
	// ===== กำหนดค่าเริ่มต้น และค่าแยกประเภทตัวแปรระหว่าง ข้อความ กับ ตัวเลข
	$DIVIDE_TEXTFILE = "$";

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
	// ======================================================================

	//	echo $command."- $RealFile --".is_file($RealFile);
	if ($command=="CONVERT" && is_file($RealFile)) { 		//	trim($RealFile)
		$cmd = " select max(SLIP_ID) as MAX_ID from PER_SLIP ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MAX_ID = $data[MAX_ID] + 1;

		if ($DPISDB=="odbc") 
			$cmd = " select top 1 * from PER_SLIP ";
		elseif ($DPISDB=="oci8")
			$cmd = " select * from PER_SLIP where rownum = 1 ";
		elseif($DPISDB=="mysql")
			$cmd = " select * from PER_SLIP limit 1 ";
		$db_dpis->send_cmd($cmd);
		$field_list = $db_dpis->list_fields(PER_SLIP);
		// ===== start นำชื่อ fields และประเภทของ fields เก็บลง array =====
		unset($arr_fields);
		if ($DPISDB=="odbc" || $DPISDB=="oci8" || $DPISDB=="mssql") {
			for($i=1; $i<=count($field_list); $i++) : 
				$arr_fields[] = $tmp_name = $field_list[$i]["name"];
				$arr_fields_type[$tmp_name] = $field_list[$i]["type"];
			endfor;
		} // end if
		//print("<pre>");	print_r($arr_fields_type);		print("</pre>");
		
		$flag_del = 1;
		$db_textfile = new connect_file("PER_SLIP", "r", $DIVIDE_TEXTFILE,$RealFile);		
		$field_name=implode(",",$arr_fields);
		while ($data = $db_textfile -> get_text_data($arr_fields_type, $TYPE_TEXT_STR, $TYPE_TEXT_INT)) {
			if ($flag_del == 1) {
				$arr_temp = explode(",", $data);
				$SLIP_YEAR = $arr_temp[0];
				$SLIP_MONTH = $arr_temp[1];
				$cmd = "	DELETE FROM PER_SLIP WHERE SLIP_YEAR = $SLIP_YEAR AND SLIP_MONTH = $SLIP_MONTH ";	
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
				$flag_del = 0;
			}
			str_replace("'00000000'",0,$data);
			$field_value = $MAX_ID . ", 0, " . $data . ", " . $SESS_USERID . ", '$UPDATE_DATE'";
//			$cmd = "	INSERT INTO PER_SLIP ( $field_name ) VALUES ( $field_value ) ";	
			$cmd = "	INSERT INTO PER_SLIP ( SLIP_ID, PER_ID, SLIP_YEAR, SLIP_MONTH, PER_CARDNO, PN_NAME, PER_NAME, PER_SURNAME, DEPARTMENT_NAME,	
					ORG_NAME, BANK_CODE, BANK_NAME, BRANCH_CODE, BRANCH_NAME, PER_BANK_ACCOUNT, INCOME_01, INCOME_02, INCOME_03, INCOME_04, INCOME_05,
					INCOME_06, INCOME_07, INCOME_08, INCOME_09, INCOME_10, INCOME_11, INCOME_12, INCOME_13, INCOME_14, INCOME_15, INCOME_16, INCOME_17, INCOME_18,
					INCOME_19, INCOME_20, INCOME_NAME_01, EXTRA_INCOME_01, INCOME_NAME_02, EXTRA_INCOME_02, INCOME_NAME_03, EXTRA_INCOME_03,
					INCOME_NAME_04, EXTRA_INCOME_04, OTHER_INCOME, TOTAL_INCOME, DEDUCT_01, DEDUCT_02, DEDUCT_03, DEDUCT_04, DEDUCT_05, DEDUCT_06,
					DEDUCT_07, DEDUCT_08, DEDUCT_09, DEDUCT_10, DEDUCT_11, DEDUCT_12, DEDUCT_13, DEDUCT_14, DEDUCT_15, DEDUCT_16, DEDUCT_NAME_01,	
					EXTRA_DEDUCT_01, DEDUCT_NAME_02, EXTRA_DEDUCT_02, DEDUCT_NAME_03, EXTRA_DEDUCT_03, DEDUCT_NAME_04, EXTRA_DEDUCT_04,
					DEDUCT_NAME_05, EXTRA_DEDUCT_05, DEDUCT_NAME_06, EXTRA_DEDUCT_06, DEDUCT_NAME_07, EXTRA_DEDUCT_07, DEDUCT_NAME_08,	
					EXTRA_DEDUCT_08, OTHER_DEDUCT, TOTAL_DEDUCT, NET_INCOME, APPROVE_DATE, UPDATE_USER, UPDATE_DATE ) VALUES ( $field_value ) ";	
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "---- $cmd<br>";
			$MAX_ID++;
			unset($field_value);

		}  // end while 

		if($DPISDB=="odbc" || $DPISDB=="mysql") 
			$cmd = " UPDATE PER_SLIP, PER_PERSONAL SET PER_SLIP.PER_ID = 
							  PER_PERSONAL.PER_ID WHERE PER_PERSONAL.PER_CARDNO = PER_SLIP.PER_CARDNO AND PER_STATUS = 1 ";
		elseif($DPISDB=="oci8") 
			$cmd = " UPDATE PER_SLIP A SET A.PER_ID = 
							  (SELECT B.PER_ID FROM PER_PERSONAL B WHERE A.PER_CARDNO = B.PER_CARDNO AND PER_STATUS = 1 AND ROWNUM = 1) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		unset($data, $arr_fields, $field_name, $field_value);
	} // endif command==CONVERT
?>